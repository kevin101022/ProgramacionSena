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
     * Helper: Obtener el ID de coordinación del coordinador actual
     */
    private function getCoordId($userId)
    {
        if (!$userId) return null;
        $stmt = $this->db->prepare(
            "SELECT coord_id FROM coordinacion WHERE coordinador_actual = :uid AND estado = 1 LIMIT 1"
        );
        $stmt->execute([':uid' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['coord_id'] ?? null;
    }

    /**
     * Reporte 1: Instructores por Centro de Formación
     */
    public function instructoresPorCentro()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $cent_id = $_SESSION['centro_id'] ?? null;

        $sql = "SELECT cf.cent_id, cf.cent_nombre, 
                       i.numero_documento as inst_id, i.inst_nombres, i.inst_apellidos, i.inst_correo, i.inst_telefono
                FROM instructor i
                INNER JOIN centro_formacion cf ON i.centro_formacion_cent_id = cf.cent_id";

        if ($cent_id) {
            $sql .= " WHERE cf.cent_id = :cent_id";
        }
        $sql .= " ORDER BY cf.cent_nombre, i.inst_apellidos";

        $stmt = $this->db->prepare($sql);
        if ($cent_id) {
            $stmt->execute([':cent_id' => $cent_id]);
        } else {
            $stmt->execute();
        }
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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $rol = $_SESSION['rol'] ?? null;
        $cent_id = $_SESSION['centro_id'] ?? null;
        $user_id = $_SESSION['id'] ?? null;

        $coord_id = null;
        if ($rol === 'coordinador') {
            $coord_id = $this->getCoordId($user_id);
        }

        $sql = "SELECT p.prog_codigo, p.prog_denominacion, p.prog_tipo,
                       f.fich_id, f.fich_jornada, f.fich_fecha_ini_lectiva, f.fich_fecha_fin_lectiva,
                       i.inst_nombres, i.inst_apellidos,
                       co.coord_descripcion
                FROM ficha f
                INNER JOIN programa p ON f.programa_prog_id = p.prog_codigo
                LEFT JOIN instructor i ON f.instructor_inst_id_lider = i.numero_documento
                LEFT JOIN coordinacion co ON f.coordinacion_coord_id = co.coord_id";

        $params = [];
        if ($coord_id) {
            $sql .= " WHERE co.coord_id = :coord_id";
            $params[':coord_id'] = $coord_id;
        } elseif ($rol === 'centro' && $cent_id) {
            $sql .= " WHERE co.centro_formacion_cent_id = :cent_id";
            $params[':cent_id'] = $cent_id;
        }

        $sql .= " ORDER BY p.prog_denominacion, f.fich_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $rol = $_SESSION['rol'] ?? null;
        $cent_id = $_SESSION['centro_id'] ?? null;
        $user_id = $_SESSION['id'] ?? null;

        $coord_id = null;
        if ($rol === 'coordinador') {
            $coord_id = $this->getCoordId($user_id);
        }

        $sql = "SELECT i.numero_documento as inst_id, i.inst_nombres, i.inst_apellidos,
                       a.asig_id, a.asig_fecha_ini, a.asig_fecha_fin,
                       f.fich_id, p.prog_denominacion,
                       c.comp_nombre_corto,
                       amb.amb_id, amb.amb_nombre
                FROM asignacion a
                INNER JOIN instructor i ON a.instructor_inst_id = i.numero_documento
                INNER JOIN ficha f ON a.ficha_fich_id = f.fich_id
                INNER JOIN programa p ON f.programa_prog_id = p.prog_codigo
                INNER JOIN competencia c ON a.competencia_comp_id = c.comp_id
                LEFT JOIN ambiente amb ON a.ambiente_amb_id = amb.amb_id
                LEFT JOIN coordinacion co ON f.coordinacion_coord_id = co.coord_id";

        $params = [];
        if ($coord_id) {
            $sql .= " WHERE co.coord_id = :coord_id";
            $params[':coord_id'] = $coord_id;
        } elseif ($rol === 'centro' && $cent_id) {
            $sql .= " WHERE co.centro_formacion_cent_id = :cent_id";
            $params[':cent_id'] = $cent_id;
        }

        $sql .= " ORDER BY i.inst_apellidos, a.asig_fecha_ini";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
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
                FROM competencia c
                INNER JOIN programa p ON c.programa_prog_id = p.prog_codigo
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

    /**
     * Reporte 5: Calendario Total
     * - rol=centro: todas las asignaciones del centro
     * - rol=coordinador: todas las asignaciones de su coordinación
     */
    public function calendarioTotal()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $rol     = $_SESSION['rol'] ?? null;
        $cent_id = $_SESSION['centro_id'] ?? null;
        $user_id = $_SESSION['id'] ?? null;

        // Para coordinador: resolver coord_id igual que asignacionController
        $coord_id = null;
        if ($rol === 'coordinador' && $user_id) {
            $stmt = $this->db->prepare(
                "SELECT coord_id FROM coordinacion WHERE coordinador_actual = :uid AND estado = 1 LIMIT 1"
            );
            $stmt->execute([':uid' => $user_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $coord_id = $row['coord_id'] ?? null;
        }

        $sql = "SELECT 
                    a.asig_id,
                    da.detasig_id,
                    da.detasig_fecha,
                    da.detasig_hora_ini,
                    da.detasig_hora_fin,
                    f.fich_id,
                    c.comp_nombre_corto,
                    a.instructor_inst_id,
                    i.inst_nombres,
                    i.inst_apellidos,
                    a.ambiente_amb_id,
                    amb.amb_nombre,
                    co.coord_id,
                    co.coord_descripcion
                FROM asignacion a
                INNER JOIN detallexasignacion da ON da.asignacion_asig_id = a.asig_id
                LEFT JOIN ficha f ON a.ficha_fich_id = f.fich_id
                LEFT JOIN competencia c ON a.competencia_comp_id = c.comp_id
                LEFT JOIN instructor i ON a.instructor_inst_id = i.numero_documento
                LEFT JOIN ambiente amb ON a.ambiente_amb_id = amb.amb_id
                LEFT JOIN coordinacion co ON f.coordinacion_coord_id = co.coord_id";

        $params = [];

        if ($coord_id) {
            // Coordinador: filtrar por su coord_id resuelto
            $sql .= " WHERE co.coord_id = :coord_id";
            $params[':coord_id'] = $coord_id;
        } elseif ($rol === 'centro' && $cent_id) {
            // Centro: filtrar por centro_formacion
            $sql .= " WHERE co.centro_formacion_cent_id = :cent_id";
            $params[':cent_id'] = $cent_id;
        } elseif ($rol === 'instructor') {
            // Instructor: puede ver el calendario de una ficha específica (usado en Mi Ficha Liderada)
            $fich_id_param = $_GET['fich_id'] ?? null;
            if ($fich_id_param) {
                $sql .= " WHERE f.fich_id = :fich_id";
                $params[':fich_id'] = $fich_id_param;
            } else {
                // Si no hay ficha, por seguridad solo ver sus propias asignaciones
                $sql .= " WHERE a.instructor_inst_id = :user_id";
                $params[':user_id'] = $user_id;
            }
        }

        $sql .= " ORDER BY da.detasig_fecha, da.detasig_hora_ini";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->sendResponse($rows);
    }

    private function sendResponse($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
