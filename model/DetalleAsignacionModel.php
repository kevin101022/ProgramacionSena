<?php
require_once dirname(__DIR__) . '/Conexion.php';
class DetalleAsignacionModel
{
    private $asignacion_asig_id;
    private $detasig_hora_ini;
    private $detasig_hora_fin;
    private $detasig_id;
    private $db;

    public function __construct($asignacion_asig_id = null, $detasig_hora_ini = null, $detasig_hora_fin = null, $detasig_id = null)
    {
        $this->asignacion_asig_id = $asignacion_asig_id;
        $this->detasig_hora_ini = $detasig_hora_ini;
        $this->detasig_hora_fin = $detasig_hora_fin;
        $this->detasig_id = $detasig_id;
        $this->db = Conexion::getConnect();
    }

    public function create()
    {
        $query = "INSERT INTO DETALLExASIGNACION (ASIGNACION_asig_id, detasig_hora_ini, detasig_hora_fin) 
                  VALUES (:asig_id, :hora_ini, :hora_fin)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':asig_id', $this->asignacion_asig_id);
        $stmt->bindParam(':hora_ini', $this->detasig_hora_ini);
        $stmt->bindParam(':hora_fin', $this->detasig_hora_fin);
        $stmt->execute();
        return $this->db->lastInsertId();
    }

    public function readAllByAsignacion($asig_id)
    {
        $sql = "SELECT detasig_id, ASIGNACION_asig_id as asignacion_asig_id, detasig_hora_ini, detasig_hora_fin 
                FROM DETALLExASIGNACION WHERE ASIGNACION_asig_id = :asig_id ORDER BY detasig_hora_ini ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':asig_id' => $asig_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ... otros métodos actualizados con el nombre de tabla DETALLExASIGNACION
    public function update()
    {
        $query = "UPDATE DETALLExASIGNACION SET ASIGNACION_asig_id = :asig_id, detasig_hora_ini = :hora_ini, detasig_hora_fin = :hora_fin WHERE detasig_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':asig_id', $this->asignacion_asig_id);
        $stmt->bindParam(':hora_ini', $this->detasig_hora_ini);
        $stmt->bindParam(':hora_fin', $this->detasig_hora_fin);
        $stmt->bindParam(':id', $this->detasig_id);
        return $stmt->execute();
    }

    public function delete()
    {
        $query = "DELETE FROM DETALLExASIGNACION WHERE detasig_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $this->detasig_id);
        return $stmt->execute();
    }

    public function checkGlobalConflicts($asig_id, $hora_ini, $hora_fin, $detasig_id = null)
    {
        // 1. Get current assignment info
        $sqlAsig = "SELECT asig_fecha_ini, asig_fecha_fin, INSTRUCTOR_inst_id, AMBIENTE_amb_id, FICHA_fich_id 
                    FROM ASIGNACION WHERE ASIG_ID = :asig_id";
        $stmtAsig = $this->db->prepare($sqlAsig);
        $stmtAsig->execute([':asig_id' => $asig_id]);
        $current = $stmtAsig->fetch(PDO::FETCH_ASSOC);

        if (!$current) return [];

        // 2. Search for ANY detail that overlaps in:
        // - Date Range (Assignment level)
        // - Time Range (Detail level)
        // - Resource (Instructor OR Environment OR Ficha)
        $sql = "SELECT d.*, a.INSTRUCTOR_inst_id, a.AMBIENTE_amb_id, a.FICHA_fich_id, 
                       i.inst_nombres, i.inst_apellidos, am.amb_nombre, f.fich_id as ficha_num
                FROM DETALLExASIGNACION d
                INNER JOIN ASIGNACION a ON d.ASIGNACION_asig_id = a.ASIG_ID
                INNER JOIN INSTRUCTOR i ON a.INSTRUCTOR_inst_id = i.inst_id
                INNER JOIN AMBIENTE am ON a.AMBIENTE_amb_id = am.amb_id
                INNER JOIN FICHA f ON a.FICHA_fich_id = f.fich_id
                WHERE (a.asig_fecha_ini <= :fecha_fin AND a.asig_fecha_fin >= :fecha_ini)
                AND (d.detasig_hora_ini < :hora_fin AND d.detasig_hora_fin > :hora_ini)
                AND (a.INSTRUCTOR_inst_id = :inst_id OR a.AMBIENTE_amb_id = :amb_id OR a.FICHA_fich_id = :fich_id)";

        if ($detasig_id) {
            $sql .= " AND d.detasig_id != :det_id";
        }

        $stmt = $this->db->prepare($sql);
        $params = [
            ':fecha_ini' => $current['asig_fecha_ini'],
            ':fecha_fin' => $current['asig_fecha_fin'],
            ':hora_ini'  => $hora_ini,
            ':hora_fin'  => $hora_fin,
            ':inst_id'   => $current['INSTRUCTOR_inst_id'],
            ':amb_id'    => $current['AMBIENTE_amb_id'],
            ':fich_id'   => $current['FICHA_fich_id']
        ];
        if ($detasig_id) $params[':det_id'] = $detasig_id;

        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($row) use ($current) {
            $row['conflict_type'] = [];
            if ($row['INSTRUCTOR_inst_id'] == $current['INSTRUCTOR_inst_id']) $row['conflict_type'][] = 'instructor';
            if ($row['AMBIENTE_amb_id'] == $current['AMBIENTE_amb_id']) $row['conflict_type'][] = 'ambiente';
            if ($row['FICHA_fich_id'] == $current['FICHA_fich_id']) $row['conflict_type'][] = 'ficha';
            return $row;
        }, $results);
    }
}
