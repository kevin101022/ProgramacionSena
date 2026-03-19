<?php
require_once dirname(__DIR__) . '/Conexion.php';
class DetalleAsignacionModel
{
    private $asignacion_asig_id;
    private $detasig_fecha;
    private $detasig_hora_ini;
    private $detasig_hora_fin;
    private $detasig_id;
    private $observaciones;
    private $db;

    public function __construct($asignacion_asig_id = null, $detasig_fecha = null, $detasig_hora_ini = null, $detasig_hora_fin = null, $detasig_id = null, $observaciones = null)
    {
        $this->asignacion_asig_id = $asignacion_asig_id;
        $this->detasig_fecha = $detasig_fecha;
        $this->detasig_hora_ini = $detasig_hora_ini;
        $this->detasig_hora_fin = $detasig_hora_fin;
        $this->detasig_id = $detasig_id;
        $this->observaciones = $observaciones;
        $this->db = Conexion::getConnect();
    }

    public function create()
    {
        $query = "INSERT INTO DETALLExASIGNACION (ASIGNACION_asig_id, detasig_fecha, detasig_hora_ini, detasig_hora_fin, observaciones) 
                  VALUES (:asig_id, :fecha, :hora_ini, :hora_fin, :observaciones)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':asig_id', $this->asignacion_asig_id);
        $stmt->bindParam(':fecha', $this->detasig_fecha);
        $stmt->bindParam(':hora_ini', $this->detasig_hora_ini);
        $stmt->bindParam(':hora_fin', $this->detasig_hora_fin);
        $stmt->bindParam(':observaciones', $this->observaciones);
        $stmt->execute();
        return $this->db->lastInsertId();
    }

    public function readAllByAsignacion($asig_id)
    {
        $sql = "SELECT detasig_id, ASIGNACION_asig_id as asignacion_asig_id, detasig_fecha, detasig_hora_ini, detasig_hora_fin, observaciones 
                FROM DETALLExASIGNACION WHERE ASIGNACION_asig_id = :asig_id ORDER BY detasig_fecha ASC, detasig_hora_ini ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':asig_id' => $asig_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ... otros métodos actualizados con el nombre de tabla DETALLExASIGNACION
    public function update()
    {
        $query = "UPDATE DETALLExASIGNACION SET ASIGNACION_asig_id = :asig_id, detasig_fecha = :fecha, detasig_hora_ini = :hora_ini, detasig_hora_fin = :hora_fin, observaciones = :observaciones WHERE detasig_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':asig_id', $this->asignacion_asig_id);
        $stmt->bindParam(':fecha', $this->detasig_fecha);
        $stmt->bindParam(':hora_ini', $this->detasig_hora_ini);
        $stmt->bindParam(':hora_fin', $this->detasig_hora_fin);
        $stmt->bindParam(':observaciones', $this->observaciones);
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

    public function checkGlobalConflicts($asig_id, $fecha, $hora_ini, $hora_fin, $detasig_id = null)
    {
        // 1. Get current assignment info
        $sqlAsig = "SELECT INSTRUCTOR_inst_id as instructor_inst_id, AMBIENTE_amb_id as ambiente_amb_id, FICHA_fich_id as ficha_fich_id 
                    FROM ASIGNACION WHERE ASIG_ID = :asig_id";
        $stmtAsig = $this->db->prepare($sqlAsig);
        $stmtAsig->execute([':asig_id' => $asig_id]);
        $current = $stmtAsig->fetch(PDO::FETCH_ASSOC);

        if (!$current) return [];

        $inst_id = $current['instructor_inst_id'];
        $amb_id  = $current['ambiente_amb_id'];
        $fich_id = $current['ficha_fich_id'];

        // 2. Search for ANY detail in OTHER assignments that overlaps
        $sql = "SELECT d.detasig_id, d.detasig_fecha, d.detasig_hora_ini, d.detasig_hora_fin,
                       a.INSTRUCTOR_inst_id as instructor_inst_id, a.AMBIENTE_amb_id as ambiente_amb_id, 
                       a.FICHA_fich_id as ficha_fich_id, a.ASIG_ID as asig_id,
                       i.inst_nombres, i.inst_apellidos, am.amb_nombre, f.fich_id as ficha_num
                FROM DETALLExASIGNACION d
                INNER JOIN ASIGNACION a ON d.ASIGNACION_asig_id = a.ASIG_ID
                INNER JOIN INSTRUCTOR i ON a.INSTRUCTOR_inst_id = i.numero_documento
                INNER JOIN AMBIENTE am ON a.AMBIENTE_amb_id = am.amb_id
                INNER JOIN FICHA f ON a.FICHA_fich_id = f.fich_id
                WHERE d.ASIGNACION_asig_id != :current_asig_id
                AND d.detasig_fecha = :fecha
                AND (d.detasig_hora_ini < :hora_fin AND d.detasig_hora_fin > :hora_ini)
                AND (a.INSTRUCTOR_inst_id = :inst_id OR a.AMBIENTE_amb_id = :amb_id OR a.FICHA_fich_id = :fich_id)";

        if ($detasig_id) {
            $sql .= " AND d.detasig_id != :det_id";
        }

        $stmt = $this->db->prepare($sql);
        $params = [
            ':current_asig_id' => $asig_id,
            ':fecha'     => $fecha,
            ':hora_ini'  => $hora_ini,
            ':hora_fin'  => $hora_fin,
            ':inst_id'   => $inst_id,
            ':amb_id'    => $amb_id,
            ':fich_id'   => $fich_id
        ];
        if ($detasig_id) $params[':det_id'] = $detasig_id;

        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($row) use ($inst_id, $amb_id, $fich_id) {
            $row['conflict_type'] = [];
            if ($row['instructor_inst_id'] == $inst_id) $row['conflict_type'][] = 'instructor';
            if ($row['ambiente_amb_id'] == $amb_id) $row['conflict_type'][] = 'ambiente';
            if ($row['ficha_fich_id'] == $fich_id) $row['conflict_type'][] = 'ficha';
            return $row;
        }, $results);
    }
}
