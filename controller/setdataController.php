<?php
require_once dirname(__DIR__) . '/model/SetdataModel.php';

class SetdataController
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'coordinador') {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            return;
        }
        // La vista se carga directamente en views/setdata/index.php
        $this->sendResponse(['status' => 'ok']);
    }

    public function upload()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'coordinador') {
            http_response_code(403);
            $this->sendResponse(['error' => 'Acceso denegado']);
            return;
        }

        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            $this->sendResponse(['error' => 'No se recibió ningún archivo válido'], 400);
            return;
        }

        $file = $_FILES['csv_file'];
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['csv', 'txt'])) {
            $this->sendResponse(['error' => 'Solo se aceptan archivos CSV'], 400);
            return;
        }

        try {
            $model = new SetdataModel();
            $result = $model->parseCSV($file['tmp_name']);
            $this->sendResponse($result);
        } catch (Throwable $e) {
            $this->sendResponse(['error' => 'Error al procesar el archivo: ' . $e->getMessage()], 500);
        }
    }

    private function sendResponse($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
