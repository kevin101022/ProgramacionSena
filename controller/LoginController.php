<?php

require_once 'model/LoginModel.php';

class LoginController
{

    private function initSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                ini_set('session.cookie_secure', 1);
            }
            session_start();
        }
    }

    public function showLogin()
    {
        $this->initSession();
        require_once 'views/login/index.php';
    }

    public function login()
    {
        $this->initSession();

        // Limit attempts (Rate Limiting)
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['last_attempt_time'] = time();
        }

        if ($_SESSION['login_attempts'] >= 5) {
            if (time() - $_SESSION['last_attempt_time'] < 300) { // 5 minutes block
                header("Location: routing.php?controller=login&action=showLogin&error=" . urlencode("Demasiados intentos. Intenta de nuevo en 5 minutos."));
                exit;
            } else {
                $_SESSION['login_attempts'] = 0; // Reset after 5 min
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF verify
            if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                header("Location: routing.php?controller=login&action=showLogin&error=" . urlencode("Error de validación de seguridad (CSRF)."));
                exit;
            }

            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $_SESSION['login_attempts']++;
                $_SESSION['last_attempt_time'] = time();
                header("Location: routing.php?controller=login&action=showLogin&error=" . urlencode("Por favor, completa todos los campos."));
                exit;
            }

            $model = new LoginModel();

            // Searh Coordinador first
            $user = $model->findCoordinatorByEmail($email);
            $rol = 'coordinador';

            // If not found, search Instructor
            if (!$user) {
                $user = $model->findInstructorByEmail($email);
                $rol = 'instructor';
            }

            // Verify Password using Bcrypt verification
            if ($user && password_verify($password, $user['password'])) {
                // Success: Regenerate ID securely
                session_regenerate_id(true);
                $_SESSION['login_attempts'] = 0;

                $_SESSION['id'] = $user['id'];
                $_SESSION['nombre'] = $user['nombre'];
                $_SESSION['correo'] = $email;
                $_SESSION['centro_id'] = $user['centro_id'];
                $_SESSION['rol'] = $rol;

                // Coordinadores go directly to manage fichas or their dashboard, Instructores go to asignacion
                if ($rol === 'coordinador') {
                    header("Location: views/dashboard/index.php"); // Coordinator needs to manage Instructors for their Center
                } else {
                    header("Location: views/asignacion/index.php");
                }
                exit;
            } else {
                // Fail
                $_SESSION['login_attempts']++;
                $_SESSION['last_attempt_time'] = time();
                header("Location: routing.php?controller=login&action=showLogin&error=" . urlencode("Credenciales incorrectas."));
                exit;
            }
        }
    }

    public function logout()
    {
        $this->initSession();
        // Clear all variables and destroy session
        session_unset();
        session_destroy();
        // Also clear session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        header("Location: routing.php?controller=login&action=showLogin");
        exit;
    }

    public function registroCoordinador()
    {
        $this->initSession();
        $model = new LoginModel();
        $coordinaciones = $model->findCoordinaciones();
        require_once 'views/auth/coordinador_registro.php';
    }

    public function guardarCoordinador()
    {
        $this->initSession();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF verify
            if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                header("Location: ?controller=login&action=registroCoordinador&error=" . urlencode("Error de validación de seguridad (CSRF)."));
                exit;
            }

            $coord_id = filter_input(INPUT_POST, 'coordinacion_id', FILTER_VALIDATE_INT);
            $nombre = trim($_POST['nombre'] ?? '');
            $correo = filter_input(INPUT_POST, 'correo', FILTER_VALIDATE_EMAIL);
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            if (!$coord_id || empty($nombre) || !$correo || empty($password)) {
                header("Location: ?controller=login&action=registroCoordinador&error=" . urlencode("Todos los campos son obligatorios y el correo debe ser válido."));
                exit;
            }

            if ($password !== $password_confirm) {
                header("Location: ?controller=login&action=registroCoordinador&error=" . urlencode("Las contraseñas no coinciden."));
                exit;
            }

            // Secure hash (Bcrypt automatically with PASSWORD_DEFAULT)
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $model = new LoginModel();
            $result = $model->registrarCoordinador($coord_id, $nombre, $correo, $password_hash);

            if ($result) {
                header("Location: routing.php?controller=login&action=showLogin&success=" . urlencode("Registro exitoso. Ahora puedes iniciar sesión."));
                exit;
            } else {
                header("Location: ?controller=login&action=registroCoordinador&error=" . urlencode("Error al registrarse. Intenta más tarde."));
                exit;
            }
        }
    }
}
