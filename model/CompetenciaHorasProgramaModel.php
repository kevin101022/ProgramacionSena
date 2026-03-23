<?php
class CompetenciaHorasProgramaModel {
    private $db;

    public function __construct() {
        $this->db = Conexion::getConnect();
    }

    public function getByPrograma($prog_codigo) {
        $stmt = $this->db->prepare("
            SELECT chp.*, c.comp_nombre_corto, c.comp_nombre_unidad_competencia 
            FROM competencia_horas_programa chp
            JOIN competencia c ON chp.comp_id = c.comp_id
            WHERE chp.prog_codigo = :prog_codigo
        ");
        $stmt->execute(['prog_codigo' => $prog_codigo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne($prog_codigo, $comp_id) {
        $stmt = $this->db->prepare("SELECT * FROM competencia_horas_programa WHERE prog_codigo = :prog_codigo AND comp_id = :comp_id");
        $stmt->execute(['prog_codigo' => $prog_codigo, 'comp_id' => $comp_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function upsert($prog_codigo, $comp_id, $horas_requeridas, $aplica) {
        $stmt = $this->db->prepare("
            INSERT INTO competencia_horas_programa (prog_codigo, comp_id, horas_requeridas, aplica) 
            VALUES (:prog_codigo, :comp_id, :horas_requeridas, :aplica) 
            ON DUPLICATE KEY UPDATE horas_requeridas = VALUES(horas_requeridas), aplica = VALUES(aplica)
        ");
        return $stmt->execute([
            'prog_codigo' => $prog_codigo,
            'comp_id' => $comp_id,
            'horas_requeridas' => $horas_requeridas,
            'aplica' => $aplica ? 1 : 0
        ]);
    }

    public function delete($prog_codigo, $comp_id) {
        $stmt = $this->db->prepare("DELETE FROM competencia_horas_programa WHERE prog_codigo = :prog_codigo AND comp_id = :comp_id");
        return $stmt->execute(['prog_codigo' => $prog_codigo, 'comp_id' => $comp_id]);
    }

    public function getHorasEjecutadas($prog_codigo, $comp_id, $fich_id) {
        return 0;
    }

    public function getEstadoCompetenciasFicha($fich_id) {
        // Necesitamos traer el prog_codigo de la ficha
        $stmtFicha = $this->db->prepare("SELECT programa_prog_id FROM ficha WHERE fich_id = :fich_id");
        $stmtFicha->execute(['fich_id' => $fich_id]);
        $prog_codigo = $stmtFicha->fetchColumn();
        
        if (!$prog_codigo) return [];

        // Traemos todas las competencias del programa y cruzamos con las horas requeridas y las ejecutadas
        $stmt = $this->db->prepare("
            SELECT 
                cxp.competencia_comp_id as comp_id,
                c.comp_nombre_corto, c.comp_nombre_unidad_competencia,
                COALESCE(chp.horas_requeridas, 0) as horas_requeridas,
                COALESCE(chp.aplica, 1) as aplica,
                0 as horas_ejecutadas
            FROM competencia c
            LEFT JOIN competencia_horas_programa chp ON c.programa_prog_id = chp.prog_codigo AND c.comp_id = chp.comp_id
            WHERE c.programa_prog_id = :prog_codigo
        ");
        
        $stmt->execute(['fich_id' => $fich_id, 'prog_codigo' => $prog_codigo]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calcular estado y porcentaje
        foreach ($rows as &$row) {
            if (!$row['aplica']) {
                $row['estado'] = 'No aplica';
                $row['porcentaje'] = 0;
            } else {
                $requeridas = (float)$row['horas_requeridas'];
                $ejecutadas = (float)$row['horas_ejecutadas'];
                $row['porcentaje'] = $requeridas > 0 ? round(($ejecutadas / $requeridas) * 100, 2) : 0;
                $row['estado'] = ($ejecutadas >= $requeridas && $requeridas > 0) ? 'OK' : 'Pendiente';
            }
        }
        
        return $rows;
    }
}
