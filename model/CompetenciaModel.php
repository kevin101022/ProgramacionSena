<?php
require_once dirname(__DIR__) . '/Conexion.php';
require_once __DIR__ . '/SchemaResilienceTrait.php';

class CompetenciaModel
{
    use SchemaResilienceTrait;

    private $comp_id;
    private $comp_nombre_corto;
    private $comp_horas;
    private $comp_nombre_unidad_competencia;
    private $db;

    public function __construct($comp_id = null, $comp_nombre_corto = null, $comp_horas = null, $comp_nombre_unidad_competencia = null)
    {
        $this->setCompId($comp_id);
        $this->setCompNombreCorto($comp_nombre_corto);
        $this->setCompHoras($comp_horas);
        $this->setCompNombreUnidadCompetencia($comp_nombre_unidad_competencia);
        $this->db = Conexion::getConnect();
    }

    // Getters
    public function getCompId()
    {
        return $this->comp_id;
    }
    public function getCompNombreCorto()
    {
        return $this->comp_nombre_corto;
    }
    public function getCompHoras()
    {
        return $this->comp_horas;
    }
    public function getCompNombreUnidadCompetencia()
    {
        return $this->comp_nombre_unidad_competencia;
    }

    // Setters
    public function setCompId($comp_id)
    {
        $this->comp_id = $comp_id;
    }
    public function setCompNombreCorto($comp_nombre_corto)
    {
        $this->comp_nombre_corto = $comp_nombre_corto;
    }
    public function setCompHoras($comp_horas)
    {
        $this->comp_horas = $comp_horas;
    }
    public function setCompNombreUnidadCompetencia($comp_nombre_unidad_competencia)
    {
        $this->comp_nombre_unidad_competencia = $comp_nombre_unidad_competencia;
    }

    // CRUD
    public function getNextId()
    {
        $query = "SELECT COALESCE(MAX(comp_id), 0) + 1 FROM COMPETENCIA";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function create()
    {
        $retryLogic = function () {
            if (!$this->comp_id) {
                $this->comp_id = $this->getNextId();
            }
            $query = "INSERT INTO COMPETENCIA (comp_id, comp_nombre_corto, comp_horas, comp_nombre_unidad_competencia) 
                      VALUES (:id, :corto, :horas, :unidad)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $this->comp_id);
            $stmt->bindParam(':corto', $this->comp_nombre_corto);
            $stmt->bindParam(':horas', $this->comp_horas);
            $stmt->bindParam(':unidad', $this->comp_nombre_unidad_competencia);
            if ($stmt->execute()) {
                return $this->comp_id;
            }
            return false;
        };

        try {
            return $retryLogic();
        } catch (PDOException $e) {
            return $this->handleTruncation($e, 'competencia', [
                'comp_nombre_corto' => $this->comp_nombre_corto,
                'comp_nombre_unidad_competencia' => $this->comp_nombre_unidad_competencia
            ], $retryLogic);
        }
    }

    public function read()
    {
        $sql = "SELECT * FROM COMPETENCIA WHERE comp_id = :comp_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':comp_id' => $this->comp_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readAll()
    {
        $sql = "SELECT * FROM COMPETENCIA ORDER BY comp_id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $query = "UPDATE COMPETENCIA 
                  SET comp_nombre_corto = :comp_nombre_corto, 
                      comp_horas = :comp_horas, 
                      comp_nombre_unidad_competencia = :comp_nombre_unidad_competencia
                  WHERE comp_id = :comp_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':comp_nombre_corto', $this->comp_nombre_corto);
        $stmt->bindParam(':comp_horas', $this->comp_horas);
        $stmt->bindParam(':comp_nombre_unidad_competencia', $this->comp_nombre_unidad_competencia);
        $stmt->bindParam(':comp_id', $this->comp_id);
        return $stmt->execute();
    }

    public function delete()
    {
        try {
            $this->db->beginTransaction();

            // 1. Eliminar asociaciones con programas (Refactorizado)
            require_once __DIR__ . '/CompetenciaProgramaModel.php';
            $assocModel = new CompetenciaProgramaModel();
            $assocModel->deleteAllByCompetencia($this->comp_id);

            // 2. Eliminar la competencia
            $queryComp = "DELETE FROM COMPETENCIA WHERE comp_id = :comp_id";
            $stmtComp = $this->db->prepare($queryComp);
            $stmtComp->bindParam(':comp_id', $this->comp_id);
            $stmtComp->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw new Exception("No se puede eliminar la competencia: Puede que esté asignada a un instructor o ficha.");
        }
    }

    // Association with Programs (competxprograma) - Refactored to use CompetenciaProgramaModel
    public function getProgramasByCompetencia()
    {
        require_once __DIR__ . '/CompetenciaProgramaModel.php';
        $assocModel = new CompetenciaProgramaModel();
        return $assocModel->getProgramasByCompetencia($this->comp_id);
    }

    public function assignProgramas($programaIds)
    {
        require_once __DIR__ . '/CompetenciaProgramaModel.php';
        $assocModel = new CompetenciaProgramaModel();
        return $assocModel->syncProgramas($this->comp_id, $programaIds);
    }
}
