<?php
require_once dirname(__DIR__) . '/Conexion.php';
class AsignacionModel
{
    private $asig_id;
    private $instructor_inst_id;
    private $asig_fecha_ini;
    private $asig_fecha_fin;
    private $ficha_fich_id;
    private $ambiente_amb_id;
    private $competencia_comp_id;
    private $db;

    public function __construct($asig_id = null, $instructor_inst_id = null, $asig_fecha_ini = null, $asig_fecha_fin = null, $ficha_fich_id = null, $ambiente_amb_id = null, $competencia_comp_id = null)
    {
        $this->asig_id = $asig_id;
        $this->instructor_inst_id = $instructor_inst_id;
        $this->asig_fecha_ini = $asig_fecha_ini;
        $this->asig_fecha_fin = $asig_fecha_fin;
        $this->ficha_fich_id = $ficha_fich_id;
        $this->ambiente_amb_id = $ambiente_amb_id;
        $this->competencia_comp_id = $competencia_comp_id;
        $this->db = Conexion::getConnect();
    }

    public function create()
    {
        try {
            $query = "INSERT INTO ASIGNACION (INSTRUCTOR_inst_id, asig_fecha_ini, asig_fecha_fin, FICHA_fich_id, AMBIENTE_amb_id, COMPETENCIA_comp_id) 
                      VALUES (:inst_id, :fecha_ini, :fecha_fin, :ficha_id, :amb_id, :comp_id)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':inst_id', $this->instructor_inst_id);
            $stmt->bindParam(':fecha_ini', $this->asig_fecha_ini);
            $stmt->bindParam(':fecha_fin', $this->asig_fecha_fin);
            $stmt->bindParam(':ficha_id', $this->ficha_fich_id);
            $stmt->bindParam(':amb_id', $this->ambiente_amb_id);
            $stmt->bindParam(':comp_id', $this->competencia_comp_id);
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error en AsignacionModel::create: " . $e->getMessage());
            throw $e;
        }
    }

    public function readAll($cent_id = null)
    {
        $sql = "SELECT a.ASIG_ID as asig_id, a.INSTRUCTOR_inst_id as instructor_inst_id, 
                       a.asig_fecha_ini, a.asig_fecha_fin, 
                       a.FICHA_fich_id as ficha_fich_id, 
                       a.AMBIENTE_amb_id as ambiente_amb_id, 
                       a.COMPETENCIA_comp_id as competencia_comp_id,
                       i.inst_nombres, i.inst_apellidos, f.fich_id, am.amb_nombre, c.comp_nombre_corto, s.sede_nombre
                FROM ASIGNACION a
                INNER JOIN INSTRUCTOR i ON a.INSTRUCTOR_inst_id = i.numero_documento
                INNER JOIN FICHA f ON a.FICHA_fich_id = f.fich_id
                INNER JOIN AMBIENTE am ON a.AMBIENTE_amb_id = am.amb_id
                INNER JOIN COMPETENCIA c ON a.COMPETENCIA_comp_id = c.comp_id
                INNER JOIN SEDE s ON am.SEDE_sede_id = s.sede_id";

        if ($cent_id) {
            $sql .= " WHERE i.CENTRO_FORMACION_cent_id = :cent_id";
        }
        $sql .= " ORDER BY a.ASIG_ID DESC";

        $stmt = $this->db->prepare($sql);
        if ($cent_id) {
            $stmt->execute([':cent_id' => $cent_id]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function read($cent_id = null)
    {
        $sql = "SELECT a.ASIG_ID as asig_id, a.INSTRUCTOR_inst_id as instructor_inst_id, 
                       a.asig_fecha_ini, a.asig_fecha_fin, 
                       a.FICHA_fich_id as ficha_fich_id, 
                       a.AMBIENTE_amb_id as ambiente_amb_id, 
                       a.COMPETENCIA_comp_id as competencia_comp_id,
                       i.inst_nombres, i.inst_apellidos, f.fich_id, am.amb_nombre, c.comp_nombre_corto, s.sede_nombre
                FROM ASIGNACION a
                INNER JOIN INSTRUCTOR i ON a.INSTRUCTOR_inst_id = i.numero_documento
                INNER JOIN FICHA f ON a.FICHA_fich_id = f.fich_id
                INNER JOIN AMBIENTE am ON a.AMBIENTE_amb_id = am.amb_id
                INNER JOIN COMPETENCIA c ON a.COMPETENCIA_comp_id = c.comp_id
                INNER JOIN SEDE s ON am.SEDE_sede_id = s.sede_id
                WHERE a.ASIG_ID = :asig_id";

        $params = [':asig_id' => $this->asig_id];
        if ($cent_id) {
            $sql .= " AND i.CENTRO_FORMACION_cent_id = :cent_id";
            $params[':cent_id'] = $cent_id;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ... otros métodos ajustados (update, delete) siguiendo el mismo patrón de nombres
    public function update()
    {
        $query = "UPDATE ASIGNACION 
                  SET INSTRUCTOR_inst_id = :inst_id, 
                      asig_fecha_ini = :fecha_ini, asig_fecha_fin = :fecha_fin, 
                      FICHA_fich_id = :ficha_id, AMBIENTE_amb_id = :amb_id, COMPETENCIA_comp_id = :comp_id 
                  WHERE ASIG_ID = :asig_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':inst_id', $this->instructor_inst_id);
        $stmt->bindParam(':fecha_ini', $this->asig_fecha_ini);
        $stmt->bindParam(':fecha_fin', $this->asig_fecha_fin);
        $stmt->bindParam(':ficha_id', $this->ficha_fich_id);
        $stmt->bindParam(':amb_id', $this->ambiente_amb_id);
        $stmt->bindParam(':comp_id', $this->competencia_comp_id);
        $stmt->bindParam(':asig_id', $this->asig_id);
        return $stmt->execute();
    }

    public function delete()
    {
        $query = "DELETE FROM ASIGNACION WHERE ASIG_ID = :asig_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':asig_id', $this->asig_id);
        return $stmt->execute();
    }

    public function checkConflicts($inst_id, $amb_id, $fich_id, $fecha_ini, $fecha_fin, $asig_id = null)
    {
        // Detect overlaps for: Same Instructor OR Same Environment OR Same Ficha
        $sql = "SELECT a.*, i.inst_nombres, i.inst_apellidos, am.amb_nombre, f.fich_id as ficha_num
                FROM ASIGNACION a
                INNER JOIN INSTRUCTOR i ON a.INSTRUCTOR_inst_id = i.numero_documento
                INNER JOIN AMBIENTE am ON a.AMBIENTE_amb_id = am.amb_id
                INNER JOIN FICHA f ON a.FICHA_fich_id = f.fich_id
                WHERE (a.INSTRUCTOR_inst_id = :inst_id OR a.AMBIENTE_amb_id = :amb_id OR a.FICHA_fich_id = :fich_id)
                AND (a.asig_fecha_ini <= :fecha_fin AND a.asig_fecha_fin >= :fecha_ini)";

        if ($asig_id) {
            $sql .= " AND a.ASIG_ID != :asig_id";
        }

        $stmt = $this->db->prepare($sql);
        $params = [
            ':inst_id' => $inst_id,
            ':amb_id' => $amb_id,
            ':fich_id' => $fich_id,
            ':fecha_ini' => $fecha_ini,
            ':fecha_fin' => $fecha_fin
        ];
        if ($asig_id) $params[':asig_id'] = $asig_id;

        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($row) use ($inst_id, $amb_id, $fich_id) {
            $row['conflict_type'] = [];
            if ($row['INSTRUCTOR_inst_id'] == $inst_id) $row['conflict_type'][] = 'instructor';
            if ($row['AMBIENTE_amb_id'] == $amb_id) $row['conflict_type'][] = 'ambiente';
            if ($row['FICHA_fich_id'] == $fich_id) $row['conflict_type'][] = 'ficha';
            return $row;
        }, $results);
    }
}
