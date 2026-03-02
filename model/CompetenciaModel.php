<?php
require_once dirname(__DIR__) . '/Conexion.php';

class CompetenciaModel
{

    private $comp_id;
    private $comp_nombre_corto;
    private $comp_horas;
    private $comp_nombre_unidad_competencia;
    private $centro_formacion_cent_id;
    private $db;

    public function __construct($comp_id = null, $comp_nombre_corto = null, $comp_horas = null, $comp_nombre_unidad_competencia = null, $centro_formacion_cent_id = null)
    {
        $this->setCompId($comp_id);
        $this->setCompNombreCorto($comp_nombre_corto);
        $this->setCompHoras($comp_horas);
        $this->setCompNombreUnidadCompetencia($comp_nombre_unidad_competencia);
        $this->setCentroFormacionId($centro_formacion_cent_id);
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
    public function getCentroFormacionId()
    {
        return $this->centro_formacion_cent_id;
    }
    public function setCentroFormacionId($id)
    {
        $this->centro_formacion_cent_id = $id;
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
        if (!$this->comp_id) {
            $this->comp_id = $this->getNextId();
        }
        $query = "INSERT INTO COMPETENCIA (comp_id, comp_nombre_corto, comp_horas, comp_nombre_unidad_competencia, centro_formacion_cent_id) 
                  VALUES (:id, :corto, :horas, :unidad, :centro_formacion_cent_id)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $this->comp_id);
        $stmt->bindParam(':corto', $this->comp_nombre_corto);
        $stmt->bindParam(':horas', $this->comp_horas);
        $stmt->bindParam(':unidad', $this->comp_nombre_unidad_competencia);
        $stmt->bindParam(':centro_formacion_cent_id', $this->centro_formacion_cent_id);
        if ($stmt->execute()) {
            return $this->comp_id;
        }
        return false;
    }

    public function read()
    {
        $sql = "SELECT * FROM COMPETENCIA WHERE comp_id = :comp_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':comp_id' => $this->comp_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readAll($cent_id = null)
    {
        $sql = "SELECT * FROM COMPETENCIA";
        $params = [];
        if ($cent_id) {
            $sql .= " WHERE centro_formacion_cent_id = :cent_id OR centro_formacion_cent_id IS NULL";
            $params[':cent_id'] = $cent_id;
        }
        $sql .= " ORDER BY comp_nombre_corto ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $query = "UPDATE COMPETENCIA 
                  SET comp_nombre_corto = :comp_nombre_corto, 
                      comp_horas = :comp_horas, 
                      comp_nombre_unidad_competencia = :comp_nombre_unidad_competencia,
                      centro_formacion_cent_id = :centro_formacion_cent_id
                  WHERE comp_id = :comp_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':comp_nombre_corto', $this->comp_nombre_corto);
        $stmt->bindParam(':comp_horas', $this->comp_horas);
        $stmt->bindParam(':comp_nombre_unidad_competencia', $this->comp_nombre_unidad_competencia);
        $stmt->bindParam(':centro_formacion_cent_id', $this->centro_formacion_cent_id);
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

    /**
     * Obtiene los instructores habilitados para esta competencia
     */
    public function getInstructoresByCompetencia()
    {
        $sql = "SELECT i.numero_documento as inst_id, i.inst_nombres, i.inst_apellidos, 
                       i.inst_correo, p.prog_codigo, p.prog_denominacion
                FROM INSTRU_COMPETENCIA ic
                INNER JOIN INSTRUCTOR i ON ic.INSTRUCTOR_inst_id = i.numero_documento
                INNER JOIN PROGRAMA p ON ic.COMPETxPROGRAMA_PROGRAMA_prog_id = p.prog_codigo
                WHERE ic.COMPETxPROGRAMA_COMPETENCIA_comp_id = :comp_id
                ORDER BY i.inst_apellidos, i.inst_nombres";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':comp_id' => $this->comp_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
