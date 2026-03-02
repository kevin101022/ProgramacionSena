<?php
require_once dirname(__DIR__) . '/model/UsuarioCoordinadorModel.php';

class usuarioCoordinadorController
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $cent_id = $_SESSION['centro_id'] ?? null;

        $model = new UsuarioCoordinadorModel();
        // Aplicar filtro por estado desde GET si existe
        $estadoFiltro = isset($_GET['estado']) ? $_GET['estado'] : null;

        $usuarios = $model->getAll($cent_id);

        // Filtrar en PHP si se requiere un estado específico (alternativamente podría ser en SQL)
        if ($estadoFiltro !== null && $estadoFiltro !== '') {
            $usuarios = array_filter($usuarios, function ($u) use ($estadoFiltro) {
                return (string)$u['estado'] === (string)$estadoFiltro;
            });
            $usuarios = array_values($usuarios);
        }

        $this->sendResponse($usuarios);
    }

    public function show()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->sendResponse(['error' => 'ID requerido'], 400);
            return;
        }

        $model = new UsuarioCoordinadorModel();
        $coordinador = $model->read($id);

        if ($coordinador) {
            $this->sendResponse($coordinador);
        } else {
            $this->sendResponse(['error' => 'Coordinador no encontrado'], 404);
        }
    }

    public function store()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $cent_id = $_SESSION['centro_id'] ?? null;

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['numero_documento']) || !isset($data['coord_nombre_coordinador'])) {
            $this->sendResponse(['error' => 'Documento y nombre son requeridos'], 400);
            return;
        }

        $model = new UsuarioCoordinadorModel(
            $data['numero_documento'],
            $data['coord_nombre_coordinador'],
            $data['coord_correo'] ?? 'N/A',
            isset($data['coord_password']) ? password_hash($data['coord_password'], PASSWORD_BCRYPT) : password_hash('123456', PASSWORD_BCRYPT),
            1, // Activo por defecto
            $data['centro_formacion_id'] ?? $cent_id
        );

        if ($model->create()) {
            $this->sendResponse(['message' => 'Coordinador registrado correctamente']);
        } else {
            $this->sendResponse(['error' => 'Error al registrar al coordinador (Puede que la cédula ya exista)'], 500);
        }
    }

    public function update()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['numero_documento'])) {
            $this->sendResponse(['error' => 'Cédula requerida para actualizar'], 400);
            return;
        }

        $pass_hash = !empty($data['coord_password']) ? password_hash($data['coord_password'], PASSWORD_BCRYPT) : '';

        $model = new UsuarioCoordinadorModel(
            $data['numero_documento'],
            $data['coord_nombre_coordinador'] ?? null,
            $data['coord_correo'] ?? null,
            $pass_hash,
            $data['estado'] ?? null
        );

        if ($model->update()) {
            $this->sendResponse(['message' => 'Datos actualizados exitosamente']);
        } else {
            $this->sendResponse(['error' => 'Error al actualizar datos'], 500);
        }
    }

    public function toggle()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['numero_documento']) || !isset($data['estado'])) {
            $this->sendResponse(['error' => 'Datos incompletos'], 400);
            return;
        }

        $model = new UsuarioCoordinadorModel($data['numero_documento']);

        if ($model->toggleEstado($data['estado'])) {
            $this->sendResponse(['message' => 'Estado modificado correctamente']);
        } else {
            $this->sendResponse(['error' => 'Error al modificar estado'], 500);
        }
    }

    private function sendResponse($data, $status = 200)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
}
