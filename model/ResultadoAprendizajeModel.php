<?php
class ResultadoAprendizajeModel {
    private $db;

    public function __construct() {
        $this->db = Conexion::getConnect();
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getAll($centro_id) {
        // Enlaza con competencia y programa para retornar nombres descriptivos
        $stmt = $this->db->prepare("
            SELECT r.*, c.comp_nombre_corto, c.comp_nombre_unidad_competencia, p.prog_denominacion 
            FROM resultado_aprendizaje r 
            JOIN competencia c ON r.competxprog_comp_id = c.comp_id 
            JOIN programa p ON r.competxprog_prog_id = p.prog_codigo
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($rap_id) {
        $stmt = $this->db->prepare("
            SELECT r.*, c.comp_nombre_corto, c.comp_nombre_unidad_competencia, p.prog_denominacion,
                   f.fase_nombre, f.fase_orden, pf.pf_nombre, pf.pf_codigo
            FROM resultado_aprendizaje r 
            JOIN competencia c ON r.competxprog_comp_id = c.comp_id 
            JOIN programa p ON r.competxprog_prog_id = p.prog_codigo
            LEFT JOIN rap_fase rf ON r.rap_id = rf.rap_rap_id
            LEFT JOIN fase_proyecto f ON rf.fase_fase_id = f.fase_id
            LEFT JOIN proyecto_formativo pf ON f.pf_pf_id = pf.pf_id
            WHERE r.rap_id = :rap_id
        ");
        $stmt->execute(['rap_id' => $rap_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByCompetenciaPrograma($prog_id, $comp_id) {
        $stmt = $this->db->prepare("SELECT * FROM resultado_aprendizaje WHERE competxprog_prog_id = :prog_id AND competxprog_comp_id = :comp_id");
        $stmt->execute(['prog_id' => $prog_id, 'comp_id' => $comp_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByFase($fase_id) {
        $stmt = $this->db->prepare("
            SELECT r.*, c.comp_nombre_corto, c.comp_nombre_unidad_competencia 
            FROM resultado_aprendizaje r
            JOIN rap_fase rf ON r.rap_id = rf.rap_rap_id
            JOIN competencia c ON r.competxprog_comp_id = c.comp_id
            WHERE rf.fase_fase_id = :fase_id
        ");
        $stmt->execute(['fase_id' => $fase_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByFaseYPrograma($fase_id, $prog_id) {
        $stmt = $this->db->prepare("
            SELECT r.*, c.comp_nombre_corto, c.comp_nombre_unidad_competencia 
            FROM resultado_aprendizaje r
            JOIN rap_fase rf ON r.rap_id = rf.rap_rap_id
            JOIN competencia c ON r.competxprog_comp_id = c.comp_id
            WHERE rf.fase_fase_id = :fase_id AND r.competxprog_prog_id = :prog_id
        ");
        $stmt->execute(['fase_id' => $fase_id, 'prog_id' => $prog_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO resultado_aprendizaje (rap_codigo, rap_descripcion, rap_horas, competxprog_prog_id, competxprog_comp_id) VALUES (:rap_codigo, :rap_descripcion, :rap_horas, :competxprog_prog_id, :competxprog_comp_id) RETURNING rap_id");
        $stmt->execute([
            'rap_codigo' => $data['rap_codigo'],
            'rap_descripcion' => $data['rap_descripcion'],
            'rap_horas' => $data['rap_horas'] ?? 0,
            'competxprog_prog_id' => $data['competxprog_prog_id'],
            'competxprog_comp_id' => $data['competxprog_comp_id']
        ]);
        return $stmt->fetchColumn();
    }

    public function update($rap_id, $data) {
        $stmt = $this->db->prepare("UPDATE resultado_aprendizaje SET rap_codigo = :rap_codigo, rap_descripcion = :rap_descripcion, rap_horas = :rap_horas, competxprog_prog_id = :competxprog_prog_id, competxprog_comp_id = :competxprog_comp_id WHERE rap_id = :rap_id");
        $data['rap_id'] = $rap_id;
        return $stmt->execute($data);
    }

    public function delete($rap_id) {
        $stmt = $this->db->prepare("DELETE FROM resultado_aprendizaje WHERE rap_id = :rap_id");
        return $stmt->execute(['rap_id' => $rap_id]);
    }

    public function asignarAFase($rap_id, $fase_id) {
        $stmt = $this->db->prepare("INSERT INTO rap_fase (rap_rap_id, fase_fase_id) VALUES (:rap_id, :fase_id)");
        return $stmt->execute(['rap_id' => $rap_id, 'fase_id' => $fase_id]);
    }

    public function desasignarDeFase($rap_id, $fase_id) {
        $stmt = $this->db->prepare("DELETE FROM rap_fase WHERE rap_rap_id = :rap_id AND fase_fase_id = :fase_id");
        return $stmt->execute(['rap_id' => $rap_id, 'fase_id' => $fase_id]);
    }

    public function getRapsFasesDeProyecto($pf_id) {
        $stmt = $this->db->prepare("
            SELECT f.fase_id, f.fase_nombre, r.* 
            FROM fase_proyecto f
            LEFT JOIN rap_fase rf ON f.fase_id = rf.fase_fase_id
            LEFT JOIN resultado_aprendizaje r ON rf.rap_rap_id = r.rap_id
            WHERE f.pf_pf_id = :pf_id
            ORDER BY f.fase_orden ASC
        ");
        $stmt->execute(['pf_id' => $pf_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $grupos = [];
        foreach ($rows as $row) {
            $f_id = $row['fase_id'];
            if (!isset($grupos[$f_id])) {
                $grupos[$f_id] = [
                    'fase_id' => $f_id,
                    'fase_nombre' => $row['fase_nombre'],
                    'raps' => []
                ];
            }
            if ($row['rap_id']) {
                $grupos[$f_id]['raps'][] = $row;
            }
        }
        return array_values($grupos);
    }
}
