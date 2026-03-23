<?php
class ActividadProyectoModel {
    private $db;

    public function __construct() {
        $this->db = Conexion::getConnect();
    }

    public function getAllByFase($fase_id) {
        $stmt = $this->db->prepare("SELECT * FROM actividad_proyecto WHERE fase_id = :fase_id ORDER BY act_id ASC");
        $stmt->execute(['fase_id' => $fase_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO actividad_proyecto (act_nombre, fase_id) VALUES (:act_nombre, :fase_id)");
        $stmt->execute([
            'act_nombre' => $data['act_nombre'],
            'fase_id' => $data['fase_id']
        ]);
        return $this->db->lastInsertId();
    }

    public function update($act_id, $data) {
        $stmt = $this->db->prepare("UPDATE actividad_proyecto SET act_nombre = :act_nombre WHERE act_id = :act_id");
        return $stmt->execute([
            'act_nombre' => $data['act_nombre'],
            'act_id' => $act_id
        ]);
    }

    public function delete($act_id) {
        $stmt = $this->db->prepare("DELETE FROM actividad_proyecto WHERE act_id = :act_id");
        return $stmt->execute(['act_id' => $act_id]);
    }

    public function asignarRap($rap_id, $act_id) {
        // Validation: Verify if the RAP is already assigned to ANY activity within the same project
        $checkSql = "
            SELECT a.fase_id, f.pf_pf_id 
            FROM actividad_proyecto a
            JOIN fase_proyecto f ON a.fase_id = f.fase_id
            WHERE a.act_id = :act_id
        ";
        $stmtCheck = $this->db->prepare($checkSql);
        $stmtCheck->execute(['act_id' => $act_id]);
        $projectInfo = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        
        if ($projectInfo) {
            $pf_id = $projectInfo['pf_pf_id'];
            
            $dupSql = "
                SELECT ra.act_id 
                FROM rap_actividad ra
                JOIN actividad_proyecto a ON ra.act_id = a.act_id
                JOIN fase_proyecto f ON a.fase_id = f.fase_id
                WHERE ra.rap_id = :rap_id AND f.pf_pf_id = :pf_id
            ";
            $stmtDup = $this->db->prepare($dupSql);
            $stmtDup->execute(['rap_id' => $rap_id, 'pf_id' => $pf_id]);
            if ($stmtDup->fetch()) {
                throw new Exception("El Resultado de Aprendizaje (RAP) ya está asignado a otra actividad en este proyecto.");
            }
        }

        $stmt = $this->db->prepare("INSERT IGNORE INTO rap_actividad (rap_id, act_id) VALUES (:rap_id, :act_id)");
        return $stmt->execute(['rap_id' => $rap_id, 'act_id' => $act_id]);
    }

    public function desasignarRap($rap_id, $act_id) {
        $stmt = $this->db->prepare("DELETE FROM rap_actividad WHERE rap_id = :rap_id AND act_id = :act_id");
        return $stmt->execute(['rap_id' => $rap_id, 'act_id' => $act_id]);
    }

    public function getRapsByActividad($act_id) {
        $stmt = $this->db->prepare("
            SELECT r.*, c.comp_nombre_corto 
            FROM resultado_aprendizaje r
            JOIN rap_actividad ra ON r.rap_id = ra.rap_id
            JOIN competencia c ON r.competencia_comp_id = c.comp_id
            WHERE ra.act_id = :act_id
        ");
        $stmt->execute(['act_id' => $act_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
