<?php
require_once dirname(__DIR__) . '/Conexion.php';

class InstruCompetenciaModel
{
    private $inscomp_id;
    private $instructor_inst_id;
    private $competxprograma_programa_prog_id;
    private $competxprograma_competencia_comp_id;
    private $inscomp_vigencia;
    private $db;

    public function __construct($inscomp_id = null, $instructor_inst_id = null, $competxprograma_programa_prog_id = null, $competxprograma_competencia_comp_id = null, $inscomp_vigencia = null)
    {
        $this->inscomp_id = $inscomp_id;
        $this->instructor_inst_id = $instructor_inst_id;
        $this->competxprograma_programa_prog_id = $competxprograma_programa_prog_id;
        $this->competxprograma_competencia_comp_id = $competxprograma_competencia_comp_id;
        $this->inscomp_vigencia = $inscomp_vigencia;
        $this->db = Conexion::getConnect();
    }

    // Getters
    public function getInscompId()
    {
        return $this->inscomp_id;
    }
    public function getInstructorInstId()
    {
        return $this->instructor_inst_id;
    }
    public function getCompetxprogramaProgramaProgId()
    {
        return $this->competxprograma_programa_prog_id;
    }
    public function getCompetxprogramaCompetenciaCompId()
    {
        return $this->competxprograma_competencia_comp_id;
    }
    public function getInscompVigencia()
    {
        return $this->inscomp_vigencia;
    }

    // Setters
    public function setInscompId($inscomp_id)
    {
        $this->inscomp_id = $inscomp_id;
    }
    public function setInstructorInstId($instructor_inst_id)
    {
        $this->instructor_inst_id = $instructor_inst_id;
    }
    public function setCompetxprogramaProgramaProgId($prog_id)
    {
        $this->competxprograma_programa_prog_id = $prog_id;
    }
    public function setCompetxprogramaCompetenciaCompId($comp_id)
    {
        $this->competxprograma_competencia_comp_id = $comp_id;
    }
    public function setInscompVigencia($vigencia)
    {
        $this->inscomp_vigencia = $vigencia;
    }

    // CRUD
    public function create()
    {
        try {
            $query = "INSERT INTO INSTRU_COMPETENCIA (INSTRUCTOR_inst_id, COMPETxPROGRAMA_PROGRAMA_prog_id, COMPETxPROGRAMA_COMPETENCIA_comp_id, inscomp_vigencia) 
                      VALUES (:inst_id, :prog_id, :comp_id, :vigencia)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':inst_id', $this->instructor_inst_id);
            $stmt->bindParam(':prog_id', $this->competxprograma_programa_prog_id);
            $stmt->bindParam(':comp_id', $this->competxprograma_competencia_comp_id);
            $stmt->bindParam(':vigencia', $this->inscomp_vigencia);
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error en InstruCompetenciaModel::create: " . $e->getMessage());
            throw $e;
        }
    }

    public function read()
    {
        $sql = "SELECT ic.inscomp_id, ic.INSTRUCTOR_inst_id as instructor_inst_id, 
                       ic.COMPETxPROGRAMA_PROGRAMA_prog_id as competxprograma_programa_prog_id, 
                       ic.COMPETxPROGRAMA_COMPETENCIA_comp_id as competxprograma_competencia_comp_id, 
                       ic.inscomp_vigencia, 
                       i.inst_nombres, i.inst_apellidos, p.prog_denominacion, c.comp_nombre_corto 
                FROM INSTRU_COMPETENCIA ic
                INNER JOIN INSTRUCTOR i ON ic.INSTRUCTOR_inst_id = i.inst_id
                INNER JOIN PROGRAMA p ON ic.COMPETxPROGRAMA_PROGRAMA_prog_id = p.prog_codigo
                INNER JOIN COMPETENCIA c ON ic.COMPETxPROGRAMA_COMPETENCIA_comp_id = c.comp_id
                WHERE ic.inscomp_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $this->inscomp_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readAll()
    {
        $sql = "SELECT ic.inscomp_id, ic.INSTRUCTOR_inst_id as instructor_inst_id, 
                       ic.COMPETxPROGRAMA_PROGRAMA_prog_id as competxprograma_programa_prog_id, 
                       ic.COMPETxPROGRAMA_COMPETENCIA_comp_id as competxprograma_competencia_comp_id, 
                       ic.inscomp_vigencia, 
                       i.inst_nombres, i.inst_apellidos, p.prog_denominacion, c.comp_nombre_corto 
                FROM INSTRU_COMPETENCIA ic
                INNER JOIN INSTRUCTOR i ON ic.INSTRUCTOR_inst_id = i.inst_id
                INNER JOIN PROGRAMA p ON ic.COMPETxPROGRAMA_PROGRAMA_prog_id = p.prog_codigo
                INNER JOIN COMPETENCIA c ON ic.COMPETxPROGRAMA_COMPETENCIA_comp_id = c.comp_id
                ORDER BY ic.inscomp_id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        try {
            $query = "UPDATE INSTRU_COMPETENCIA 
                      SET INSTRUCTOR_inst_id = :inst_id, 
                          COMPETxPROGRAMA_PROGRAMA_prog_id = :prog_id, 
                          COMPETxPROGRAMA_COMPETENCIA_comp_id = :comp_id, 
                          inscomp_vigencia = :vigencia 
                      WHERE inscomp_id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':inst_id', $this->instructor_inst_id);
            $stmt->bindParam(':prog_id', $this->competxprograma_programa_prog_id);
            $stmt->bindParam(':comp_id', $this->competxprograma_competencia_comp_id);
            $stmt->bindParam(':vigencia', $this->inscomp_vigencia);
            $stmt->bindParam(':id', $this->inscomp_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en InstruCompetenciaModel::update: " . $e->getMessage());
            throw $e;
        }
    }

    public function delete()
    {
        $query = "DELETE FROM INSTRU_COMPETENCIA WHERE inscomp_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $this->inscomp_id);
        return $stmt->execute();
    }

    public static function isQualified($inst_id, $prog_id, $comp_id)
    {
        $db = Conexion::getConnect();
        $sql = "SELECT COUNT(*) FROM INSTRU_COMPETENCIA 
                WHERE INSTRUCTOR_inst_id = :inst_id 
                AND COMPETxPROGRAMA_PROGRAMA_prog_id = :prog_id 
                AND COMPETxPROGRAMA_COMPETENCIA_comp_id = :comp_id";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':inst_id' => $inst_id,
            ':prog_id' => $prog_id,
            ':comp_id' => $comp_id
        ]);
        return $stmt->fetchColumn() > 0;
    }
}
