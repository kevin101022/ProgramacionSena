<?php
require_once dirname(__DIR__) . '/Conexion.php';

class ReporteController
{
    private $db;

    public function __construct()
    {
        $this->db = Conexion::getConnect();
    }

    /**
     * Reporte 1: Instructores por Centro de Formación
     */
    public function instructoresPorCentro()
    {
        $sql = "SELECT cf.cent_id, cf.cent_nombre, 
                       i.inst_id, i.inst_nombres, i.inst_apellidos, i.inst_correo, i.inst_telefono
                FROM INSTRUCTOR i
                INNER JOIN CENTRO_FORMACION cf ON i.CENTRO_FORMACION_cent_id = cf.cent_id
                ORDER BY cf.cent_nombre, i.inst_apellidos";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Agrupar por centro
        $grouped = [];
        foreach ($rows as $row) {
            $centId = $row['cent_id'];
            if (!isset($grouped[$centId])) {
                $grouped[$centId] = [
                    'cent_id' => $row['cent_id'],
                    'cent_nombre' => $row['cent_nombre'],
                    'instructores' => []
                ];
            }
            $grouped[$centId]['instructores'][] = [
                'inst_id' => $row['inst_id'],
                'inst_nombres' => $row['inst_nombres'],
                'inst_apellidos' => $row['inst_apellidos'],
                'inst_correo' => $row['inst_correo'],
                'inst_telefono' => $row['inst_telefono']
            ];
        }

        $this->sendResponse(array_values($grouped));
    }

    /**
     * Reporte 2: Fichas activas por Programa
     */
    public function fichasActivasPorPrograma()
    {
        $sql = "SELECT p.prog_codigo, p.prog_denominacion, p.prog_tipo,
                       f.fich_id, f.fich_jornada, f.fich_fecha_ini_lectiva, f.fich_fecha_fin_lectiva,
                       i.inst_nombres, i.inst_apellidos,
                       co.coord_descripcion
                FROM FICHA f
                INNER JOIN PROGRAMA p ON f.PROGRAMA_prog_id = p.prog_codigo
                LEFT JOIN INSTRUCTOR i ON f.INSTRUCTOR_inst_id_lider = i.inst_id
                LEFT JOIN COORDINACION co ON f.COORDINACION_coord_id = co.coord_id
                ORDER BY p.prog_denominacion, f.fich_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $grouped = [];
        foreach ($rows as $row) {
            $progId = $row['prog_codigo'];
            if (!isset($grouped[$progId])) {
                $grouped[$progId] = [
                    'prog_codigo' => $row['prog_codigo'],
                    'prog_denominacion' => $row['prog_denominacion'],
                    'prog_tipo' => $row['prog_tipo'],
                    'fichas' => []
                ];
            }
            $grouped[$progId]['fichas'][] = [
                'fich_id' => $row['fich_id'],
                'fich_jornada' => $row['fich_jornada'],
                'fich_fecha_ini_lectiva' => $row['fich_fecha_ini_lectiva'],
                'fich_fecha_fin_lectiva' => $row['fich_fecha_fin_lectiva'],
                'inst_lider' => trim($row['inst_nombres'] . ' ' . $row['inst_apellidos']),
                'coordinacion' => $row['coord_descripcion']
            ];
        }

        $this->sendResponse(array_values($grouped));
    }

    /**
     * Reporte 3: Asignaciones por Instructor
     */
    public function asignacionesPorInstructor()
    {
        $sql = "SELECT i.inst_id, i.inst_nombres, i.inst_apellidos,
                       a.asig_id, a.asig_fecha_ini, a.asig_fecha_fin,
                       f.fich_id, p.prog_denominacion,
                       c.comp_nombre_corto,
                       amb.amb_id, amb.amb_nombre
                FROM ASIGNACION a
                INNER JOIN INSTRUCTOR i ON a.INSTRUCTOR_inst_id = i.inst_id
                INNER JOIN FICHA f ON a.FICHA_fich_id = f.fich_id
                INNER JOIN PROGRAMA p ON f.PROGRAMA_prog_id = p.prog_codigo
                INNER JOIN COMPETENCIA c ON a.COMPETENCIA_comp_id = c.comp_id
                LEFT JOIN AMBIENTE amb ON a.AMBIENTE_amb_id = amb.amb_id
                ORDER BY i.inst_apellidos, a.asig_fecha_ini";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $grouped = [];
        foreach ($rows as $row) {
            $instId = $row['inst_id'];
            if (!isset($grouped[$instId])) {
                $grouped[$instId] = [
                    'inst_id' => $row['inst_id'],
                    'inst_nombres' => $row['inst_nombres'],
                    'inst_apellidos' => $row['inst_apellidos'],
                    'asignaciones' => []
                ];
            }
            $grouped[$instId]['asignaciones'][] = [
                'asig_id' => $row['asig_id'],
                'fecha_ini' => $row['asig_fecha_ini'],
                'fecha_fin' => $row['asig_fecha_fin'],
                'fich_id' => $row['fich_id'],
                'programa' => $row['prog_denominacion'],
                'competencia' => $row['comp_nombre_corto'],
                'ambiente' => $row['amb_nombre'] ? "{$row['amb_id']} - {$row['amb_nombre']}" : $row['amb_id']
            ];
        }

        $this->sendResponse(array_values($grouped));
    }

    /**
     * Reporte 4: Competencias por Programa
     */
    public function competenciasPorPrograma()
    {
        $sql = "SELECT p.prog_codigo, p.prog_denominacion, p.prog_tipo,
                       c.comp_id, c.comp_nombre_corto, c.comp_horas, c.comp_nombre_unidad_competencia
                FROM COMPETxPROGRAMA cp
                INNER JOIN PROGRAMA p ON cp.PROGRAMA_prog_id = p.prog_codigo
                INNER JOIN COMPETENCIA c ON cp.COMPETENCIA_comp_id = c.comp_id
                ORDER BY p.prog_denominacion, c.comp_nombre_corto";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $grouped = [];
        foreach ($rows as $row) {
            $progId = $row['prog_codigo'];
            if (!isset($grouped[$progId])) {
                $grouped[$progId] = [
                    'prog_codigo' => $row['prog_codigo'],
                    'prog_denominacion' => $row['prog_denominacion'],
                    'prog_tipo' => $row['prog_tipo'],
                    'competencias' => []
                ];
            }
            $grouped[$progId]['competencias'][] = [
                'comp_id' => $row['comp_id'],
                'comp_nombre_corto' => $row['comp_nombre_corto'],
                'comp_horas' => $row['comp_horas'],
                'comp_unidad' => $row['comp_nombre_unidad_competencia']
            ];
        }

        $this->sendResponse(array_values($grouped));
    }

    private function sendResponse($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
