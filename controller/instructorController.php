<?php
require_once dirname(__DIR__) . '/model/InstructorModel.php';
require_once dirname(__DIR__) . '/model/CentroFormacionModel.php';

class instructorController
{
    public function index()
    {
        $model = new InstructorModel();
        $instructores = $model->readAll();
        return $this->sendResponse($instructores);
    }

    public function getCentros()
    {
        $model = new CentroFormacionModel();
        $centros = $model->getAll();
        return $this->sendResponse($centros);
    }

    public function store()
    {
        $nombres = $_POST['inst_nombres'] ?? null;
        $apellidos = $_POST['inst_apellidos'] ?? null;
        $correo = $_POST['inst_correo'] ?? null;
        $telefono = $_POST['inst_telefono'] ?? null;
        $cent_id = $_POST['centro_formacion_cent_id'] ?? null;
        $password = $_POST['inst_password'] ?? 'Sena123*'; // Default password

        if (!$nombres || !$apellidos || !$correo || !$cent_id) {
            return $this->sendResponse(['error' => 'Datos obligatorios faltantes'], 400);
        }

        $model = new InstructorModel(
            null,
            $nombres,
            $apellidos,
            $correo,
            $telefono,
            $cent_id,
            $password
        );

        $id = $model->create();
        if ($id) {
            // Guardar habilitaciones (competencias seleccionadas)
            $competencias = $_POST['competencias'] ?? [];
            $programaId = $_POST['programa_id'] ?? null;

            if (!empty($competencias) && $programaId) {
                require_once dirname(__DIR__) . '/model/InstruCompetenciaModel.php';
                $vigencia = date('Y') + 1; // Vigencia por defecto: año siguiente

                foreach ($competencias as $compId) {
                    $instruCompModel = new InstruCompetenciaModel(
                        null,
                        $id, // Nuevo ID del instructor
                        $programaId,
                        $compId,
                        $vigencia
                    );
                    $instruCompModel->create();
                }
            }

            return $this->sendResponse(['message' => 'Instructor creado correctamente', 'id' => $id]);
        }
        return $this->sendResponse(['error' => 'Error al crear el instructor'], 500);
    }

    public function show()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            return $this->sendResponse(['error' => 'ID no proporcionado'], 400);
        }

        $model = new InstructorModel($id);
        $instructor = $model->read();

        if ($instructor) {
            return $this->sendResponse($instructor[0]);
        }
        return $this->sendResponse(['error' => 'Instructor no encontrado'], 404);
    }

    public function update()
    {
        $id = $_POST['inst_id'] ?? null;
        $nombres = $_POST['inst_nombres'] ?? null;
        $apellidos = $_POST['inst_apellidos'] ?? null;
        $correo = $_POST['inst_correo'] ?? null;
        $telefono = $_POST['inst_telefono'] ?? null;
        $cent_id = $_POST['centro_formacion_cent_id'] ?? null;
        $password = $_POST['inst_password'] ?? null;

        if (!$id || !$nombres || !$apellidos || !$correo || !$cent_id) {
            return $this->sendResponse(['error' => 'Datos incompletos'], 400);
        }

        $model = new InstructorModel(
            $id,
            $nombres,
            $apellidos,
            $correo,
            $telefono,
            $cent_id,
            $password
        );

        if ($model->update()) {
            return $this->sendResponse(['message' => 'Instructor actualizado correctamente']);
        }
        return $this->sendResponse(['error' => 'Error al actualizar el instructor'], 500);
    }

    public function destroy()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            return $this->sendResponse(['error' => 'ID no proporcionado'], 400);
        }

        $model = new InstructorModel($id);
        if ($model->delete()) {
            return $this->sendResponse(['message' => 'Instructor eliminado correctamente']);
        }
        return $this->sendResponse(['error' => 'Error al eliminar el instructor'], 500);
    }

    public function getAsignaciones($id = null)
    {
        if (!$id) {
            return $this->sendResponse(['error' => 'ID no proporcionado'], 400);
        }
        try {
            $model = new InstructorModel($id);
            $data = $model->getAsignacionesByInstructor();
            return $this->sendResponse($data);
        } catch (Throwable $e) {
            return $this->sendResponse(['error' => $e->getMessage()], 500);
        }
    }

    public function getCompetencias($id = null)
    {
        if (!$id) {
            return $this->sendResponse(['error' => 'ID no proporcionado'], 400);
        }
        try {
            $model = new InstructorModel($id);
            $data = $model->getCompetenciasByInstructor();
            return $this->sendResponse($data);
        } catch (Throwable $e) {
            return $this->sendResponse(['error' => $e->getMessage()], 500);
        }
    }

    private function sendResponse($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
