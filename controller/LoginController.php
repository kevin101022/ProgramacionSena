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



        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF verify
            if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                header("Location: routing.php?controller=login&action=showLogin&error=" . urlencode("Error de validación de seguridad (CSRF)."));
                exit;
            }

            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                header("Location: routing.php?controller=login&action=showLogin&error=" . urlencode("Por favor, completa todos los campos."));
                exit;
            }

            $model = new LoginModel();
            $user = null;
            $rol = null;

            // Detección automática de rol (Centro -> Coordinador -> Instructor)
            $user = $model->findCentroFormacionByEmail($email);
            if ($user) {
                $rol = 'centro';
            } else {
                $user = $model->findCoordinatorByEmail($email);
                if ($user) {
                    $rol = 'coordinador';
                } else {
                    $user = $model->findInstructorByEmail($email);
                    if ($user) {
                        $rol = 'instructor';
                    }
                }
            }

            if (!$user) {
                header("Location: routing.php?controller=login&action=showLogin&error=" . urlencode("Usuario no encontrado o rol no válido."));
                exit;
            }

            // Verify Password using Bcrypt verification or fallback to plain text for old records
            if (password_verify($password, $user['password']) || $password === $user['password']) {
                // Success: Regenerate ID securely
                session_regenerate_id(true);

                $_SESSION['id'] = $user['id'];
                $_SESSION['nombre'] = $user['nombre'];
                $_SESSION['correo'] = $email;
                $_SESSION['rol'] = $rol;

                if ($rol === 'centro') {
                    // El Centro de Formación es su propio centro_id
                    $_SESSION['centro_id'] = $user['id'];
                    header("Location: views/dashboard/index.php");
                } else if ($rol === 'coordinador') {
                    $_SESSION['centro_id'] = $user['centro_id'];
                    // NO se guarda coord_id en sesión: siempre se consulta la DB en tiempo real
                    header("Location: views/dashboard/index.php");
                } else {
                    // instructor
                    $_SESSION['centro_id'] = $user['centro_id'];
                    header("Location: views/asignacion/instructor_index.php");
                }
                exit;
            } else {
                // Fail
                header("Location: routing.php?controller=login&action=showLogin&error=" . urlencode("Credenciales incorrectas o rol equivocado."));
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

    // Métodos removidos correspondientes al autoregistro de coordinadores
}
