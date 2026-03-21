<?php
require_once dirname(__DIR__) . '/Conexion.php';

class CompetenciaModel
{

    private $comp_id;
    private $comp_nombre_corto;
    private $comp_horas;
    private $comp_nombre_unidad_competencia;
    private $centro_formacion_cent_id;
    private $programa_prog_id;
    private $requisitos_academicos;
    private $experiencia_laboral;
    private $db;

    public function __construct($comp_id = null, $comp_nombre_corto = null, $comp_horas = null, $comp_nombre_unidad_competencia = null, $centro_formacion_cent_id = null, $programa_prog_id = null, $requisitos_academicos = null, $experiencia_laboral = null)
    {
        $this->setCompId($comp_id);
        $this->setCompNombreCorto($comp_nombre_corto);
        $this->setCompHoras($comp_horas);
        $this->setCompNombreUnidadCompetencia($comp_nombre_unidad_competencia);
        $this->setCentroFormacionId($centro_formacion_cent_id);
        $this->setProgramaProgId($programa_prog_id);
        $this->setRequisitosAcademicos($requisitos_academicos);
        $this->setExperienciaLaboral($experiencia_laboral);
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
    public function getProgramaProgId()
    {
        return $this->programa_prog_id;
    }
    public function getRequisitosAcademicos()
    {
        return $this->requisitos_academicos;
    }
    public function getExperienciaLaboral()
    {
        return $this->experiencia_laboral;
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
    public function setProgramaProgId($id)
    {
        $this->programa_prog_id = $id;
    }
    public function setRequisitosAcademicos($texto)
    {
        $this->requisitos_academicos = $texto;
    }
    public function setExperienciaLaboral($texto)
    {
        $this->experiencia_laboral = $texto;
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
        $query = "INSERT INTO COMPETENCIA (comp_id, comp_nombre_corto, comp_horas, comp_nombre_unidad_competencia, centro_formacion_cent_id, programa_prog_id, requisitos_academicos, experiencia_laboral) 
                  VALUES (:id, :corto, :horas, :unidad, :centro_formacion_cent_id, :prog_id, :requisitos, :experiencia)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $this->comp_id);
        $stmt->bindParam(':corto', $this->comp_nombre_corto);
        $stmt->bindParam(':horas', $this->comp_horas);
        $stmt->bindParam(':unidad', $this->comp_nombre_unidad_competencia);
        $stmt->bindParam(':centro_formacion_cent_id', $this->centro_formacion_cent_id);
        $stmt->bindParam(':prog_id', $this->programa_prog_id);
        $stmt->bindParam(':requisitos', $this->requisitos_academicos);
        $stmt->bindParam(':experiencia', $this->experiencia_laboral);
        if ($stmt->execute()) {
            return $this->comp_id;
        }
        return false;
    }

    public function read()
    {
        $sql = "SELECT c.*, p.prog_denominacion 
                FROM COMPETENCIA c
                LEFT JOIN PROGRAMA p ON c.programa_prog_id = p.prog_codigo
                WHERE c.comp_id = :comp_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':comp_id' => $this->comp_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readById($id)
    {
        $sql = "SELECT c.*, p.prog_denominacion 
                FROM COMPETENCIA c
                LEFT JOIN PROGRAMA p ON c.programa_prog_id = p.prog_codigo
                WHERE c.comp_id = :comp_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':comp_id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function readAll($cent_id = null)
    {
        $sql = "SELECT c.*, p.prog_denominacion 
                FROM COMPETENCIA c
                LEFT JOIN PROGRAMA p ON c.programa_prog_id = p.prog_codigo";
        $params = [];
        if ($cent_id) {
            $sql .= " WHERE c.centro_formacion_cent_id = :cent_id OR c.centro_formacion_cent_id IS NULL";
            $params[':cent_id'] = $cent_id;
        }
        $sql .= " ORDER BY c.comp_nombre_corto ASC";
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
                      centro_formacion_cent_id = :centro_formacion_cent_id,
                      programa_prog_id = :prog_id,
                      requisitos_academicos = :requisitos,
                      experiencia_laboral = :experiencia
                  WHERE comp_id = :comp_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':comp_nombre_corto', $this->comp_nombre_corto);
        $stmt->bindParam(':comp_horas', $this->comp_horas);
        $stmt->bindParam(':comp_nombre_unidad_competencia', $this->comp_nombre_unidad_competencia);
        $stmt->bindParam(':centro_formacion_cent_id', $this->centro_formacion_cent_id);
        $stmt->bindParam(':prog_id', $this->programa_prog_id);
        $stmt->bindParam(':requisitos', $this->requisitos_academicos);
        $stmt->bindParam(':experiencia', $this->experiencia_laboral);
        $stmt->bindParam(':comp_id', $this->comp_id);
        return $stmt->execute();
    }

    public function delete()
    {
        try {
            $this->db->beginTransaction();

            // 1. Eliminar asociaciones con programas (Refactorizado)
            // 1. Ya no se necesita eliminar asociaciones en tabla de unión
            // Las claves foráneas en la base de datos manejarán la integridad

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

    // Association with Programs - Refactored to one-to-many
    public function getPrograma()
    {
        $sql = "SELECT p.* FROM PROGRAMA p 
                INNER JOIN COMPETENCIA c ON p.prog_codigo = c.programa_prog_id 
                WHERE c.comp_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $this->comp_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene los instructores habilitados para esta competencia
     */
    public function getInstructoresByCompetencia($cent_id = null)
    {
        $sql = "SELECT i.numero_documento as inst_id, i.inst_nombres, i.inst_apellidos, 
                       i.inst_correo, p.prog_codigo, p.prog_denominacion
                FROM INSTRU_COMPETENCIA ic
                INNER JOIN INSTRUCTOR i ON ic.INSTRUCTOR_inst_id = i.numero_documento
                INNER JOIN PROGRAMA p ON ic.programa_prog_id = p.prog_codigo
                WHERE ic.competencia_comp_id = :comp_id";

        $params = [':comp_id' => $this->comp_id];
        if ($cent_id) {
            $sql .= " AND i.CENTRO_FORMACION_cent_id = :cent_id";
            $params[':cent_id'] = $cent_id;
        }

        $sql .= " ORDER BY i.inst_apellidos, i.inst_nombres";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene las competencias por programa
     */
    public function getByPrograma($prog_id, $cent_id = null)
    {
        $sql = "SELECT c.*, p.prog_denominacion 
                FROM COMPETENCIA c
                LEFT JOIN PROGRAMA p ON c.programa_prog_id = p.prog_codigo
                WHERE c.programa_prog_id = :prog_id";
        $params = [':prog_id' => $prog_id];
        if ($cent_id) {
            $sql .= " AND (c.centro_formacion_cent_id = :cent_id OR c.centro_formacion_cent_id IS NULL)";
            $params[':cent_id'] = $cent_id;
        }
        $sql .= " ORDER BY c.comp_nombre_corto ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todos los programas que tienen competencias asociadas
     */
    public function getProgramas($cent_id = null)
    {
        $sql = "SELECT DISTINCT p.prog_codigo, p.prog_denominacion 
                FROM PROGRAMA p
                INNER JOIN COMPETENCIA c ON p.prog_codigo = c.programa_prog_id";
        
        $params = [];
        if ($cent_id) {
            $sql .= " WHERE (c.centro_formacion_cent_id = :cent_id OR c.centro_formacion_cent_id IS NULL)";
            $params[':cent_id'] = $cent_id;
        }
        
        $sql .= " ORDER BY p.prog_denominacion ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
