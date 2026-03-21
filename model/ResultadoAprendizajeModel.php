<?php
class ResultadoAprendizajeModel {
    private $db;

    public function __construct() {
        $this->db = Conexion::getConnect();
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getAll($centro_id, $prog_id = null) {
        $sql = "
            SELECT r.*, c.comp_nombre_corto, c.comp_nombre_unidad_competencia, p.prog_denominacion 
            FROM resultado_aprendizaje r 
            LEFT JOIN competencia c ON r.competencia_comp_id = c.comp_id 
            LEFT JOIN programa p ON r.programa_prog_id = p.prog_codigo
            WHERE 1=1
        ";
        $params = [];
        
        if ($centro_id) {
            $sql .= " AND (p.centro_formacion_cent_id = :centro_id OR p.centro_formacion_cent_id IS NULL)";
            $params['centro_id'] = $centro_id;
        }

        if ($prog_id) {
            $sql .= " AND r.programa_prog_id = :prog_id";
            $params['prog_id'] = $prog_id;
        }

        $sql .= " ORDER BY r.rap_codigo ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($rap_id) {
        $stmt = $this->db->prepare("
            SELECT r.*, c.comp_nombre_corto, c.comp_nombre_unidad_competencia, p.prog_denominacion,
                   f.fase_nombre, f.fase_orden, pf.pf_nombre, pf.pf_codigo
            FROM resultado_aprendizaje r 
            JOIN competencia c ON r.competencia_comp_id = c.comp_id 
            JOIN programa p ON r.programa_prog_id = p.prog_codigo
            LEFT JOIN rap_fase rf ON r.rap_id = rf.rap_rap_id
            LEFT JOIN fase_proyecto f ON rf.fase_fase_id = f.fase_id
            LEFT JOIN proyecto_formativo pf ON f.pf_pf_id = pf.pf_id
            WHERE r.rap_id = :rap_id
        ");
        $stmt->execute(['rap_id' => $rap_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByCompetenciaPrograma($prog_id, $comp_id) {
        $stmt = $this->db->prepare("SELECT * FROM resultado_aprendizaje WHERE programa_prog_id = :prog_id AND competencia_comp_id = :comp_id");
        $stmt->execute(['prog_id' => $prog_id, 'comp_id' => $comp_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByFase($fase_id) {
        $stmt = $this->db->prepare("
            SELECT r.*, c.comp_nombre_corto, c.comp_nombre_unidad_competencia 
            FROM resultado_aprendizaje r
            JOIN rap_fase rf ON r.rap_id = rf.rap_rap_id
            JOIN competencia c ON r.competencia_comp_id = c.comp_id
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
            JOIN competencia c ON r.competencia_comp_id = c.comp_id
            WHERE rf.fase_fase_id = :fase_id AND r.programa_prog_id = :prog_id
        ");
        $stmt->execute(['fase_id' => $fase_id, 'prog_id' => $prog_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByCompetencia($comp_id) {
        $stmt = $this->db->prepare("SELECT * FROM resultado_aprendizaje WHERE competencia_comp_id = :comp_id ORDER BY rap_codigo ASC");
        $stmt->execute(['comp_id' => $comp_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAvailableByCompetencia($comp_id, $pf_id) {
        $sql = "
            SELECT r.* 
            FROM resultado_aprendizaje r
            WHERE r.competencia_comp_id = :comp_id
            AND r.rap_id NOT IN (
                SELECT ra.rap_id 
                FROM rap_actividad ra
                JOIN actividad_proyecto a ON ra.act_id = a.act_id
                JOIN fase_proyecto f ON a.fase_id = f.fase_id
                WHERE f.pf_pf_id = :pf_id
            )
            ORDER BY r.rap_codigo ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['comp_id' => $comp_id, 'pf_id' => $pf_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO resultado_aprendizaje (rap_codigo, rap_descripcion, rap_horas, programa_prog_id, competencia_comp_id) VALUES (:rap_codigo, :rap_descripcion, :rap_horas, :programa_prog_id, :competencia_comp_id) RETURNING rap_id");
        $stmt->execute([
            'rap_codigo' => $data['rap_codigo'],
            'rap_descripcion' => $data['rap_descripcion'],
            'rap_horas' => $data['rap_horas'] ?? 0,
            'programa_prog_id' => $data['programa_prog_id'],
            'competencia_comp_id' => $data['competencia_comp_id']
        ]);
        return $stmt->fetchColumn();
    }

    public function update($rap_id, $data) {
        $stmt = $this->db->prepare("UPDATE resultado_aprendizaje SET rap_codigo = :rap_codigo, rap_descripcion = :rap_descripcion, rap_horas = :rap_horas, programa_prog_id = :programa_prog_id, competencia_comp_id = :competencia_comp_id WHERE rap_id = :rap_id");
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

    public function getHierarchyByProyecto($pf_id) {
        $stmt = $this->db->prepare("
            SELECT f.fase_id, f.fase_nombre, f.fase_orden, f.fase_fecha_ini, f.fase_fecha_fin,
                   a.act_id, a.act_nombre,
                   r.rap_id, r.rap_codigo, r.rap_descripcion, r.rap_horas,
                   c.comp_id, c.comp_nombre_corto
            FROM fase_proyecto f
            LEFT JOIN actividad_proyecto a ON f.fase_id = a.fase_id
            LEFT JOIN rap_actividad ra ON a.act_id = ra.act_id
            LEFT JOIN resultado_aprendizaje r ON ra.rap_id = r.rap_id
            LEFT JOIN competencia c ON r.competencia_comp_id = c.comp_id
            WHERE f.pf_pf_id = :pf_id
            ORDER BY f.fase_orden ASC, a.act_id ASC, c.comp_id ASC
        ");
        $stmt->execute(['pf_id' => $pf_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $hierarchy = [];
        foreach ($rows as $row) {
            $f_id = $row['fase_id'];
            if (!isset($hierarchy[$f_id])) {
                $hierarchy[$f_id] = [
                    'fase_id' => $f_id,
                    'fase_nombre' => $row['fase_nombre'],
                    'fase_orden' => $row['fase_orden'],
                    'fase_fecha_ini' => $row['fase_fecha_ini'],
                    'fase_fecha_fin' => $row['fase_fecha_fin'],
                    'actividades' => []
                ];
            }
            
            if ($row['act_id']) {
                $a_id = $row['act_id'];
                if (!isset($hierarchy[$f_id]['actividades'][$a_id])) {
                    $hierarchy[$f_id]['actividades'][$a_id] = [
                        'act_id' => $a_id,
                        'act_nombre' => $row['act_nombre'],
                        'competencias' => [] // Grouped by competency
                    ];
                }
                
                if ($row['rap_id']) {
                    $c_id = $row['comp_id'];
                    if (!isset($hierarchy[$f_id]['actividades'][$a_id]['competencias'][$c_id])) {
                        $hierarchy[$f_id]['actividades'][$a_id]['competencias'][$c_id] = [
                            'comp_id' => $c_id,
                            'comp_nombre_corto' => $row['comp_nombre_corto'],
                            'raps' => []
                        ];
                    }
                    
                    $hierarchy[$f_id]['actividades'][$a_id]['competencias'][$c_id]['raps'][] = [
                        'rap_id' => $row['rap_id'],
                        'rap_codigo' => $row['rap_codigo'],
                        'rap_descripcion' => $row['rap_descripcion'],
                        'rap_horas' => $row['rap_horas']
                    ];
                }
            }
        }
        
        // Final normalization to indexed arrays
        foreach ($hierarchy as &$fase) {
            foreach ($fase['actividades'] as &$act) {
                $act['competencias'] = array_values($act['competencias']);
            }
            $fase['actividades'] = array_values($fase['actividades']);
        }
        return array_values($hierarchy);
    }
}
