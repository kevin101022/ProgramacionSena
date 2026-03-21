<?php
require_once dirname(__DIR__) . '/Conexion.php';

class InstruCompetenciaModel
{
    private $inscomp_id;
    private $instructor_inst_id;
    private $programa_prog_id;
    private $competencia_comp_id;
    private $inscomp_vigencia;
    private $db;

    public function __construct($inscomp_id = null, $instructor_inst_id = null, $programa_prog_id = null, $competencia_comp_id = null, $inscomp_vigencia = null)
    {
        $this->inscomp_id = $inscomp_id;
        $this->instructor_inst_id = $instructor_inst_id;
        $this->programa_prog_id = $programa_prog_id;
        $this->competencia_comp_id = $competencia_comp_id;
        $this->inscomp_vigencia = $inscomp_vigencia ?: date('Y-12-31'); // Por defecto fin de año
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
    public function getProgramaProgId()
    {
        return $this->programa_prog_id;
    }
    public function getCompetenciaCompId()
    {
        return $this->competencia_comp_id;
    }


    // CRUD
    public function create()
    {
        try {
            $query = "INSERT INTO INSTRU_COMPETENCIA (INSTRUCTOR_inst_id, programa_prog_id, competencia_comp_id, inscomp_vigencia) 
                      VALUES (:inst_id, :prog_id, :comp_id, :vigencia)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':inst_id', $this->instructor_inst_id);
            $stmt->bindParam(':prog_id', $this->programa_prog_id);
            $stmt->bindParam(':comp_id', $this->competencia_comp_id);
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
                       ic.programa_prog_id, 
                       ic.competencia_comp_id, 
                       i.inst_nombres, i.inst_apellidos, i.inst_correo, i.inst_telefono,
                       i.profesion, i.especializacion,
                       p.prog_denominacion, c.comp_nombre_corto, cf.cent_nombre
                FROM INSTRU_COMPETENCIA ic
                INNER JOIN INSTRUCTOR i ON ic.INSTRUCTOR_inst_id = i.numero_documento
                INNER JOIN COMPETENCIA c ON ic.competencia_comp_id = c.comp_id
                INNER JOIN PROGRAMA p ON c.programa_prog_id = p.prog_codigo
                LEFT JOIN CENTRO_FORMACION cf ON i.CENTRO_FORMACION_cent_id = cf.cent_id
                WHERE ic.inscomp_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $this->inscomp_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readAll($centro_id = null)
    {
        $sql = "SELECT ic.inscomp_id, ic.INSTRUCTOR_inst_id as instructor_inst_id, 
                       ic.programa_prog_id, 
                       ic.competencia_comp_id, 
                       i.inst_nombres, i.inst_apellidos, c.comp_nombre_corto, p.prog_denominacion 
                FROM INSTRU_COMPETENCIA ic
                INNER JOIN INSTRUCTOR i ON ic.INSTRUCTOR_inst_id = i.numero_documento
                INNER JOIN COMPETENCIA c ON ic.competencia_comp_id = c.comp_id
                INNER JOIN PROGRAMA p ON c.programa_prog_id = p.prog_codigo
                WHERE 1=1";

        $params = [];
        if ($centro_id) {
            $sql .= " AND i.CENTRO_FORMACION_cent_id = :centro_id";
            $params[':centro_id'] = $centro_id;
        }

        $sql .= " ORDER BY ic.inscomp_id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        try {
            $query = "UPDATE INSTRU_COMPETENCIA 
                      SET INSTRUCTOR_inst_id = :inst_id, 
                          programa_prog_id = :prog_id, 
                          competencia_comp_id = :comp_id,
                          inscomp_vigencia = :vigencia
                      WHERE inscomp_id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':inst_id', $this->instructor_inst_id);
            $stmt->bindParam(':prog_id', $this->programa_prog_id);
            $stmt->bindParam(':comp_id', $this->competencia_comp_id);
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
                AND programa_prog_id = :prog_id 
                AND competencia_comp_id = :comp_id";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':inst_id' => $inst_id,
            ':prog_id' => $prog_id,
            ':comp_id' => $comp_id
        ]);
        return $stmt->fetchColumn() > 0;
    }
    public function deleteByInstructor($inst_id)
    {
        $query = "DELETE FROM INSTRU_COMPETENCIA WHERE INSTRUCTOR_inst_id = :inst_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':inst_id', $inst_id);
        return $stmt->execute();
    }
}
