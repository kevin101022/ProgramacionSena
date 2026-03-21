<?php
require_once dirname(__DIR__) . '/model/InstructorModel.php';
require_once dirname(__DIR__) . '/model/CentroFormacionModel.php';

class instructorController
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $model = new InstructorModel();
        $rol = $_SESSION['rol'] ?? null;

        // Si es un instructor logueado, solo devuelve su propia información
        if ($rol === 'instructor' && !empty($_SESSION['id'])) {
            $instructores = $model->readById($_SESSION['id']);
            // Devolver como array para mantener consistencia
            return $this->sendResponse($instructores ? [$instructores] : []);
        }

        if (isset($_SESSION['centro_id']) && !empty($_SESSION['centro_id'])) {
            $instructores = $model->readByCentro($_SESSION['centro_id']);
        } else {
            $instructores = $model->readAll();
        }

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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $numero_documento = $_POST['numero_documento'] ?? null;
        $nombres = $_POST['inst_nombres'] ?? null;
        $apellidos = $_POST['inst_apellidos'] ?? null;
        $correo = $_POST['inst_correo'] ?? null;
        $telefono = $_POST['inst_telefono'] ?? null;
        $cent_id = $_SESSION['centro_id'] ?? null;
        $rawPassword = $_POST['inst_password'] ?? 'Sena123*';
        $password = password_hash($rawPassword, PASSWORD_BCRYPT);
        $profesion = $_POST['profesion'] ?? null;
        $especializacion = $_POST['especializacion'] ?? null;

        if (!$numero_documento || !$nombres || !$apellidos || !$correo || !$cent_id) {
            return $this->sendResponse(['error' => 'Datos obligatorios faltantes'], 400);
        }

        $model = new InstructorModel(
            $numero_documento,
            $nombres,
            $apellidos,
            $correo,
            $telefono,
            $cent_id,
            $password,
            $profesion,
            $especializacion
        );

        try {
            $id = $model->create();
        } catch (PDOException $e) {
            return $this->sendResponse(['error' => 'El número de documento ya está registrado o hay error en BD'], 500);
        }
        if ($id) {
            // Guardar habilitaciones (competencias seleccionadas)
            $competencias = $_POST['competencias'] ?? [];

            if (!empty($competencias)) {
                require_once dirname(__DIR__) . '/model/InstruCompetenciaModel.php';
                require_once dirname(__DIR__) . '/model/CompetenciaModel.php';
                $compModel = new CompetenciaModel();

                foreach ($competencias as $compData) {
                    try {
                        if (strpos($compData, '|') !== false) {
                            list($progId, $compId) = explode('|', $compData);
                            $instruCompModel = new InstruCompetenciaModel(null, $id, $progId, $compId);
                            $instruCompModel->create();
                        } else {
                            $compId = $compData;
                            $compRow = $compModel->readById($compId);
                            if ($compRow && !empty($compRow['programa_prog_id'])) {
                                try {
                                    $instruCompModel = new InstruCompetenciaModel(null, $id, $compRow['programa_prog_id'], $compId);
                                    $instruCompModel->create();
                                } catch (PDOException $innerEx) {
                                    error_log("Habilitación duplicada o error: " . $innerEx->getMessage());
                                }
                            } else {
                                error_log("Competencia $compId no tiene programa asociado, no se puede habilitar aún.");
                            }
                        }
                    } catch (PDOException $ex) {
                        error_log("Error habilitando competencia: " . $ex->getMessage());
                    }
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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        try {
            $id = $_POST['numero_documento'] ?? $_POST['inst_id'] ?? null;
            $nombres = $_POST['inst_nombres'] ?? null;
            $apellidos = $_POST['inst_apellidos'] ?? null;
            $correo = $_POST['inst_correo'] ?? null;
            $telefono = $_POST['inst_telefono'] ?? null;
            $cent_id = $_SESSION['centro_id'] ?? null;
            $rawPassword = $_POST['inst_password'] ?? null;
            // Solo hashear si el usuario envió una contraseña nueva
            $password = !empty($rawPassword) ? password_hash($rawPassword, PASSWORD_BCRYPT) : null;
            $profesion = $_POST['profesion'] ?? null;
            $especializacion = $_POST['especializacion'] ?? null;

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
                $password,
                $profesion,
                $especializacion
            );

            if ($model->update()) {
                // Actualizar habilitaciones (competencias seleccionadas)
                $competencias = $_POST['competencias'] ?? [];

                require_once dirname(__DIR__) . '/model/InstruCompetenciaModel.php';
                require_once dirname(__DIR__) . '/model/CompetenciaModel.php';
                $instruCompModel = new InstruCompetenciaModel();
                $compModel = new CompetenciaModel();

                // Limpiar habilitaciones previas antes de insertar las nuevas
                $instruCompModel->deleteByInstructor($id);

                if (!empty($competencias)) {
                    foreach ($competencias as $compData) {
                        try {
                            if (strpos($compData, '|') !== false) {
                                list($progId, $compId) = explode('|', $compData);
                                $newModel = new InstruCompetenciaModel(null, $id, $progId, $compId);
                                $newModel->create();
                            } else {
                                $compId = $compData;
                                $compRow = $compModel->readById($compId);
                                if ($compRow && !empty($compRow['programa_prog_id'])) {
                                    try {
                                        $newModel = new InstruCompetenciaModel(null, $id, $compRow['programa_prog_id'], $compId);
                                        $newModel->create();
                                    } catch (PDOException $innerEx) {
                                        error_log("Habilitación duplicada o error: " . $innerEx->getMessage());
                                    }
                                } else {
                                    error_log("Competencia $compId no tiene programa asociado, no se puede habilitar.");
                                }
                            }
                        } catch (PDOException $ex) {
                            error_log("Error habilitando competencia: " . $ex->getMessage());
                        }
                    }
                }

                return $this->sendResponse(['message' => 'Instructor actualizado correctamente']);
            }
            return $this->sendResponse(['error' => 'Error al actualizar el instructor'], 500);
        } catch (Throwable $e) {
            error_log("Error en InstructorController::update: " . $e->getMessage());
            return $this->sendResponse(['error' => 'Error interno: ' . $e->getMessage()], 500);
        }
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

    public function getFichasLider($id = null)
    {
        if (!$id) {
            return $this->sendResponse(['error' => 'ID no proporcionado'], 400);
        }
        try {
            $model = new InstructorModel($id);
            $data = $model->getFichasLider();
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
