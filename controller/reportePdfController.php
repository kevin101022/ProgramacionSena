<?php
/**
 * ReportePdfController - Generación de reportes PDF para calendarios
 * Formato Visual Moderno Mensual
 */

require_once dirname(__DIR__) . '/Conexion.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

class ReportePdfController
{
    private $db;

    public function __construct()
    {
        $this->db = Conexion::getConnect();
    }

    private function getRoleContext() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $rol     = $_SESSION["rol"] ?? null;
        $cent_id = $_SESSION["centro_id"] ?? null;
        $user_id = $_SESSION["id"] ?? null;

        $coord_id   = null;
        if ($rol === "coordinador" && $user_id) {
            $stmt = $this->db->prepare("SELECT coord_id FROM COORDINACION WHERE coordinador_actual = :uid AND estado = 1 LIMIT 1");
            $stmt->execute([":uid" => $user_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $coord_id = $row["coord_id"] ?? null;
        }
        
        return ["rol" => $rol, "cent_id" => $cent_id, "coord_id" => $coord_id];
    }

    private function getTituloTiempo($mes, $anio) {
        if ($mes !== "all" && $anio !== "all") {
            return "{$anio}-" . str_pad($mes, 2, "0", STR_PAD_LEFT);
        } elseif ($mes === "all" && $anio !== "all") {
            return "Año {$anio}";
        } else {
            return "Historial Completo";
        }
    }

    public function calendarioFicha()
    {
        $context = $this->getRoleContext();
        
        $fichId = $_GET['fich_id'] ?? null;
        $mes = $_GET['mes'] ?? date('m');
        $anio = $_GET['anio'] ?? date('Y');

        if (!$fichId) {
            http_response_code(400);
            die('ID de ficha requerido');
        }

        $ficha = $this->getFichaData($fichId);
        if (!$ficha) {
            http_response_code(404);
            die('Ficha no encontrada');
        }

        $asignaciones = $this->getAsignacionesFicha($fichId, $mes, $anio, $context["cent_id"], $context["coord_id"]);

        $this->generarHTMLModerno(
            "Horario Ficha — " . $this->getTituloTiempo($mes, $anio),
            "Ficha: {$ficha['fich_id']} — " . ($ficha['prog_denominacion'] ?? $ficha['titpro_nombre'] ?? 'Sin programa'),
            $asignaciones,
            'ficha',
            "Calendario_Ficha_{$fichId}_{$anio}_{$mes}.pdf",
            $mes, $anio,
            $context['coord_id']
        );
    }

    public function calendarioInstructor()
    {
        $context = $this->getRoleContext();
        
        $instId = $_GET['inst_id'] ?? null;
        $mes = $_GET['mes'] ?? date('m');
        $anio = $_GET['anio'] ?? date('Y');

        if (!$instId) {
            http_response_code(400);
            die('ID de instructor requerido');
        }

        $instructor = $this->getInstructorData($instId);
        if (!$instructor) {
            http_response_code(404);
            die('Instructor no encontrado');
        }

        $asignaciones = $this->getAsignacionesInstructor($instId, $mes, $anio, $context["cent_id"], null);

        $this->generarHTMLModerno(
            "Horario Instructor — " . $this->getTituloTiempo($mes, $anio),
            "{$instructor['inst_nombres']} {$instructor['inst_apellidos']}",
            $asignaciones,
            'instructor',
            "Calendario_Instructor_{$instId}_{$anio}_{$mes}.pdf",
            $mes, $anio,
            $context['coord_id']
        );
    }

    public function calendarioAmbiente()
    {
        $context = $this->getRoleContext();
        
        $ambId = $_GET['amb_id'] ?? null;
        $mes = $_GET['mes'] ?? date('m');
        $anio = $_GET['anio'] ?? date('Y');

        if (!$ambId) {
            http_response_code(400);
            die('ID de ambiente requerido');
        }

        $ambiente = $this->getAmbienteData($ambId);
        if (!$ambiente) {
            http_response_code(404);
            die('Ambiente no encontrado');
        }

        $asignaciones = $this->getAsignacionesAmbiente($ambId, $mes, $anio, $context["cent_id"], null);

        $this->generarHTMLModerno(
            "Horario Ambiente — " . $this->getTituloTiempo($mes, $anio),
            "Ambiente: {$ambiente['amb_id']} - {$ambiente['amb_nombre']}",
            $asignaciones,
            'ambiente',
            "Calendario_Ambiente_{$ambId}_{$anio}_{$mes}.pdf",
            $mes, $anio,
            $context['coord_id']
        );
    }

    public function calendarioTotal()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $context = $this->getRoleContext();

        $mes = $_GET['mes'] ?? date('m');
        $anio = $_GET['anio'] ?? date('Y');

        $coord_id = $context['coord_id'];
        $cent_id = $context['cent_id'];
        
        $coord_desc = 'Todas las coordinaciones';
        if ($context['rol'] === 'coordinador' && $coord_id) {
            $stmt = $this->db->prepare("SELECT coord_descripcion FROM COORDINACION WHERE coord_id = :cid");
            $stmt->execute([':cid' => $coord_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $coord_desc = $row['coord_descripcion'] ?? 'Mi coordinación';
            $subtitulo = "Coordinación: {$coord_desc}";
        } else {
            $stmt = $this->db->prepare("SELECT cent_nombre FROM CENTRO_FORMACION WHERE cent_id = :cid");
            $stmt->execute([':cid' => $cent_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $cent_nombre = $row['cent_nombre'] ?? '';
            $subtitulo = "Todas las coordinaciones del centro" . ($cent_nombre ? " ({$cent_nombre})" : "");
        }

        $asignaciones = $this->getAsignacionesTotal($cent_id, $coord_id, $mes, $anio);

        $this->generarHTMLModerno(
            "Horario Total — " . $this->getTituloTiempo($mes, $anio),
            $subtitulo,
            $asignaciones,
            'total',
            "Calendario_Total_{$anio}_{$mes}.pdf",
            $mes, $anio,
            $context['coord_id']
        );
    }

    private function getFichaData($fichId) {
        $sql = "SELECT f.fich_id, p.prog_denominacion, tp.titpro_nombre
                FROM FICHA f
                LEFT JOIN PROGRAMA p ON f.PROGRAMA_prog_id = p.prog_codigo
                LEFT JOIN TITULO_PROGRAMA tp ON p.TIT_PROGRAMA_titpro_id = tp.titpro_id
                WHERE f.fich_id = :fich_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':fich_id' => $fichId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getInstructorData($instId) {
        $sql = "SELECT numero_documento, inst_nombres, inst_apellidos FROM INSTRUCTOR WHERE numero_documento = :inst_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':inst_id' => $instId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getAmbienteData($ambId) {
        $sql = "SELECT a.amb_id, a.amb_nombre, s.sede_nombre
                FROM AMBIENTE a
                LEFT JOIN SEDE s ON a.SEDE_sede_id = s.sede_id
                WHERE a.amb_id = :amb_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':amb_id' => $ambId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getAsignacionesFicha($fichId, $mes, $anio, $cent_id, $coord_id) {
        $sql = "SELECT a.asig_id, d.detasig_fecha, d.detasig_hora_ini, d.detasig_hora_fin,
                       f.fich_id, p.prog_denominacion,
                       c.comp_nombre_corto, c.comp_nombre_unidad_competencia,
                       i.inst_nombres, i.inst_apellidos, i.numero_documento,
                       amb.amb_id, amb.amb_nombre, co.coord_descripcion, co.coord_id as asignacion_coord_id
                FROM ASIGNACION a
                LEFT JOIN FICHA f ON a.FICHA_fich_id = f.fich_id
                LEFT JOIN PROGRAMA p ON f.PROGRAMA_prog_id = p.prog_codigo
                INNER JOIN COORDINACION co ON f.COORDINACION_coord_id = co.coord_id
                INNER JOIN DETALLExASIGNACION d ON a.asig_id = d.ASIGNACION_asig_id
                INNER JOIN COMPETENCIA c ON a.COMPETENCIA_comp_id = c.comp_id
                INNER JOIN INSTRUCTOR i ON a.INSTRUCTOR_inst_id = i.numero_documento
                LEFT JOIN AMBIENTE amb ON a.AMBIENTE_amb_id = amb.amb_id
                WHERE a.FICHA_fich_id = :fich_id";

        $params = [':fich_id' => $fichId];

        if ($mes !== 'all') {
            $sql .= " AND MONTH(d.detasig_fecha) = :mes";
            $params[':mes'] = $mes;
        }
        if ($anio !== 'all') {
            $sql .= " AND YEAR(d.detasig_fecha) = :anio";
            $params[':anio'] = $anio;
        }
        if ($coord_id) {
            $sql .= " AND co.coord_id = :coord_id";
            $params[':coord_id'] = $coord_id;
        } elseif ($cent_id) {
            $sql .= " AND co.CENTRO_FORMACION_cent_id = :cent_id";
            $params[':cent_id'] = $cent_id;
        }

        $sql .= " ORDER BY d.detasig_fecha, d.detasig_hora_ini";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $this->agruparPorAsignacion($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    private function getAsignacionesInstructor($instId, $mes, $anio, $cent_id, $coord_id) {
        $sql = "SELECT a.asig_id, d.detasig_fecha, d.detasig_hora_ini, d.detasig_hora_fin,
                       f.fich_id, p.prog_denominacion,
                       c.comp_nombre_corto, c.comp_nombre_unidad_competencia,
                       i.inst_nombres, i.inst_apellidos, i.numero_documento,
                       amb.amb_id, amb.amb_nombre, co.coord_descripcion, co.coord_id as asignacion_coord_id
                FROM ASIGNACION a
                LEFT JOIN FICHA f ON a.FICHA_fich_id = f.fich_id
                LEFT JOIN PROGRAMA p ON f.PROGRAMA_prog_id = p.prog_codigo
                INNER JOIN COORDINACION co ON f.COORDINACION_coord_id = co.coord_id
                INNER JOIN DETALLExASIGNACION d ON a.asig_id = d.ASIGNACION_asig_id
                INNER JOIN COMPETENCIA c ON a.COMPETENCIA_comp_id = c.comp_id
                INNER JOIN INSTRUCTOR i ON a.INSTRUCTOR_inst_id = i.numero_documento
                LEFT JOIN AMBIENTE amb ON a.AMBIENTE_amb_id = amb.amb_id
                WHERE a.INSTRUCTOR_inst_id = :inst_id";

        $params = [':inst_id' => $instId];

        if ($mes !== 'all') {
            $sql .= " AND MONTH(d.detasig_fecha) = :mes";
            $params[':mes'] = $mes;
        }
        if ($anio !== 'all') {
            $sql .= " AND YEAR(d.detasig_fecha) = :anio";
            $params[':anio'] = $anio;
        }
        if ($coord_id) {
            $sql .= " AND co.coord_id = :coord_id";
            $params[':coord_id'] = $coord_id;
        } elseif ($cent_id) {
            $sql .= " AND co.CENTRO_FORMACION_cent_id = :cent_id";
            $params[':cent_id'] = $cent_id;
        }

        $sql .= " ORDER BY d.detasig_fecha, d.detasig_hora_ini";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $this->agruparPorAsignacion($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    private function getAsignacionesAmbiente($ambId, $mes, $anio, $cent_id, $coord_id) {
        $sql = "SELECT a.asig_id, d.detasig_fecha, d.detasig_hora_ini, d.detasig_hora_fin,
                       f.fich_id, p.prog_denominacion,
                       c.comp_nombre_corto, c.comp_nombre_unidad_competencia,
                       i.inst_nombres, i.inst_apellidos, i.numero_documento,
                       amb.amb_id, amb.amb_nombre, co.coord_descripcion, co.coord_id as asignacion_coord_id
                FROM ASIGNACION a
                LEFT JOIN FICHA f ON a.FICHA_fich_id = f.fich_id
                LEFT JOIN PROGRAMA p ON f.PROGRAMA_prog_id = p.prog_codigo
                INNER JOIN COORDINACION co ON f.COORDINACION_coord_id = co.coord_id
                INNER JOIN DETALLExASIGNACION d ON a.asig_id = d.ASIGNACION_asig_id
                INNER JOIN COMPETENCIA c ON a.COMPETENCIA_comp_id = c.comp_id
                INNER JOIN INSTRUCTOR i ON a.INSTRUCTOR_inst_id = i.numero_documento
                LEFT JOIN AMBIENTE amb ON a.AMBIENTE_amb_id = amb.amb_id
                WHERE a.AMBIENTE_amb_id = :amb_id";

        $params = [':amb_id' => $ambId];

        if ($mes !== 'all') {
            $sql .= " AND MONTH(d.detasig_fecha) = :mes";
            $params[':mes'] = $mes;
        }
        if ($anio !== 'all') {
            $sql .= " AND YEAR(d.detasig_fecha) = :anio";
            $params[':anio'] = $anio;
        }
        if ($coord_id) {
            $sql .= " AND co.coord_id = :coord_id";
            $params[':coord_id'] = $coord_id;
        } elseif ($cent_id) {
            $sql .= " AND co.CENTRO_FORMACION_cent_id = :cent_id";
            $params[':cent_id'] = $cent_id;
        }

        $sql .= " ORDER BY d.detasig_fecha, d.detasig_hora_ini";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $this->agruparPorAsignacion($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    private function getAsignacionesTotal($cent_id, $coord_id, $mes, $anio) {
        $sql = "SELECT a.asig_id, d.detasig_fecha, d.detasig_hora_ini, d.detasig_hora_fin,
                       co.coord_descripcion,
                       f.fich_id, p.prog_denominacion,
                       c.comp_nombre_corto, c.comp_nombre_unidad_competencia,
                       i.inst_nombres, i.inst_apellidos, i.numero_documento,
                       amb.amb_id, amb.amb_nombre
                FROM ASIGNACION a
                INNER JOIN FICHA f ON a.FICHA_fich_id = f.fich_id
                LEFT JOIN PROGRAMA p ON f.PROGRAMA_prog_id = p.prog_codigo
                INNER JOIN COORDINACION co ON f.COORDINACION_coord_id = co.coord_id
                INNER JOIN DETALLExASIGNACION d ON a.asig_id = d.ASIGNACION_asig_id
                INNER JOIN COMPETENCIA c ON a.COMPETENCIA_comp_id = c.comp_id
                INNER JOIN INSTRUCTOR i ON a.INSTRUCTOR_inst_id = i.numero_documento
                LEFT JOIN AMBIENTE amb ON a.AMBIENTE_amb_id = amb.amb_id
                WHERE 1=1";

        $params = [];
        if ($mes !== 'all') {
            $sql .= " AND MONTH(d.detasig_fecha) = :mes";
            $params[':mes'] = $mes;
        }
        if ($anio !== 'all') {
            $sql .= " AND YEAR(d.detasig_fecha) = :anio";
            $params[':anio'] = $anio;
        }
        if ($coord_id) {
            $sql .= " AND co.coord_id = :coord_id";
            $params[':coord_id'] = $coord_id;
        } elseif ($cent_id) {
            $sql .= " AND co.CENTRO_FORMACION_cent_id = :cent_id";
            $params[':cent_id'] = $cent_id;
        }
        $sql .= " ORDER BY co.coord_descripcion, d.detasig_fecha, d.detasig_hora_ini";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $this->agruparPorAsignacion($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    private function agruparPorAsignacion($detalles) {
        $grupos = [];
        foreach ($detalles as $d) {
            $key = $d['asig_id'];
            if (!isset($grupos[$key])) {
                $grupos[$key] = [
                    'key' => $key,
                    'ficha' => $d['fich_id'] ?? null,
                    'programa' => $d['prog_denominacion'] ?? '',
                    'competencia' => !empty($d['comp_nombre_corto']) ? $d['comp_nombre_corto'] : ($d['comp_nombre_unidad_competencia'] ?? ''),
                    'instructor_req' => trim(($d['inst_nombres'] ?? '') . ' ' . ($d['inst_apellidos'] ?? '')),
                    'instructor_id' => $d['numero_documento'] ?? '',
                    'ambiente' => $d['amb_id'] ?? 'N/A',
                    'coord_id' => $d['asignacion_coord_id'] ?? null,
                    'coord_descripcion' => $d['coord_descripcion'] ?? '',
                    'hora_ini' => $d['detasig_hora_ini'],
                    'hora_fin' => $d['detasig_hora_fin'],
                    'fechas' => []
                ];
            }
            if (!in_array($d['detasig_fecha'], $grupos[$key]['fechas'])) {
                $grupos[$key]['fechas'][] = $d['detasig_fecha'];
            }
        }
        return array_values($grupos);
    }

    private function formatearHora($time) {
        $datetime = DateTime::createFromFormat('H:i:s', $time);
        if (!$datetime) $datetime = DateTime::createFromFormat('H:i', $time);
        return $datetime ? $datetime->format('h:i A') : $time;
    }

    private function generarHTMLModerno($tituloAbsoluto, $subtituloAbsoluto, $asignaciones, $tipo, $filename, $mes, $anio, $currentUserCoordId) {
        // Formateo y agrupamiento logico
        $agrupadoPorSubGrupo = [];
        $totalHorasGeneral = 0;
        
        $etiquetaTiempo = ($mes !== "all" && $anio !== "all") ? "del mes" : (($mes === "all" && $anio !== "all") ? "del año" : "del historial");
        $etiquetaCorto = ($mes !== "all" && $anio !== "all") ? "mes" : (($mes === "all" && $anio !== "all") ? "año" : "historial");
        
        foreach ($asignaciones as $asig) {
            $h_ini = strtotime($asig['hora_ini']);
            $h_fin = strtotime($asig['hora_fin']);
            $horasDiarias = ($h_fin - $h_ini) / 3600;
            $dias = count($asig['fechas']);
            $totalHorasAsig = $horasDiarias * $dias;
            $totalHorasGeneral += $totalHorasAsig;
            $asig['total_horas'] = $totalHorasAsig;
            $asig['horas_diarias'] = $horasDiarias;

            $arrayDias = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
            $nombresMes = ['', 'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
            if (empty($asig['fechas'])) continue; 
            $minDate = min($asig['fechas']);
            $maxDate = max($asig['fechas']);
            
            $weekdays = [];
            foreach($asig['fechas'] as $f) {
                $wd = date('N', strtotime($f)) - 1;
                $weekdays[$wd] = $arrayDias[$wd];
            }
            ksort($weekdays);
            $dayString = implode(' · ', $weekdays);
            
            if ($minDate == $maxDate) {
                $dayString .= " · " . date('j', strtotime($minDate)) . " de " . $nombresMes[(int)date('n', strtotime($minDate))];
            } else {
                $dayString .= " · " . date('j', strtotime($minDate)) . "-" . date('j', strtotime($maxDate)) . " de " . $nombresMes[(int)date('n', strtotime($maxDate))];
            }
            $asig['string_dias'] = $dayString;

            // Clave de Agrupacion
            if ($tipo == 'instructor' || $tipo == 'ambiente' || $tipo == 'total') {
                $claveAgrupamiento = $asig['ficha'] ?? 'Sin Ficha';
                $tituloAgrupamiento = "FICHA {$claveAgrupamiento}" . (!empty($asig['programa']) ? " — {$asig['programa']}" : "");
                
                // Añadir aviso si es de otra coordinación
                if ($currentUserCoordId && $asig['coord_id'] != $currentUserCoordId) {
                    $tituloAgrupamiento .= " (De: {$asig['coord_descripcion']})";
                }
            } else { // Ficha
                $claveAgrupamiento = $asig['competencia'] ?? 'Sin Competencia';
                $tituloAgrupamiento = "COMPETENCIA: " . $asig['competencia'];
            }
            
            if (!isset($agrupadoPorSubGrupo[$claveAgrupamiento])) {
                $agrupadoPorSubGrupo[$claveAgrupamiento] = [
                    'titulo' => $tituloAgrupamiento,
                    'horas_diarias_promedio' => 0,
                    'total_mes' => 0,
                    'items' => []
                ];
            }
            if ($horasDiarias > $agrupadoPorSubGrupo[$claveAgrupamiento]['horas_diarias_promedio']) {
                $agrupadoPorSubGrupo[$claveAgrupamiento]['horas_diarias_promedio'] = $horasDiarias;
            }
            $agrupadoPorSubGrupo[$claveAgrupamiento]['total_mes'] += $totalHorasAsig;
            $agrupadoPorSubGrupo[$claveAgrupamiento]['items'][] = $asig;
        }

        $countGrupos = count($agrupadoPorSubGrupo);
        $descriptorInfo = ($tipo == 'instructor') ? "{$countGrupos} fichas" :
                         (($tipo == 'ficha') ? "{$countGrupos} instructores" : "{$countGrupos} registros");

        $totalHorasGeneral = number_format($totalHorasGeneral, 1, ',', '.');
        
        $html = '<!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>' . htmlspecialchars($filename) . '</title>
            <style>
                body {
                    font-family: "Helvetica", "Arial", sans-serif;
                    margin: 0;
                    padding: 0;
                }
                .pdf-container {
                    width: 100%;
                    min-height: 210mm;
                    margin: 0 auto;
                    background: white;
                    padding: 15mm;
                    box-sizing: border-box;
                }
                .main-header {
                    background-color: #003265;
                    color: white;
                    text-align: center;
                    padding: 12px;
                    font-size: 20px;
                    font-weight: bold;
                    margin-bottom: 20px;
                }
                .sub-header {
                    margin-bottom: 25px;
                }
                .sub-title {
                    color: #003265;
                    font-size: 18px;
                    font-weight: bold;
                    margin-bottom: 5px;
                }
                .sub-info {
                    font-size: 12px;
                    color: #4b5563;
                }
                .group-card {
                    margin-bottom: 20px;
                    border-left: 1px solid #9ca3af;
                    border-right: 1px solid #9ca3af;
                    border-bottom: 1px solid #9ca3af;
                }
                .group-header {
                    background-color: #effaf3;
                    border-top: 1px solid #9ca3af;
                    border-bottom: 1px solid #9ca3af;
                    color: #0067b1;
                    padding: 0;
                }
                .group-header table {
                    width: 100%;
                    border-collapse: collapse;
                }
                .group-header td {
                    border: none;
                    font-size: 11px;
                    font-weight: bold;
                    padding: 8px 12px;
                }
                .group-header-title {
                    text-transform: uppercase;
                    text-align: left;
                }
                .group-header-stats {
                    color: #1f2937;
                    text-align: right;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                th {
                    background-color: #003265;
                    color: white;
                    font-size: 11px;
                    padding: 8px;
                    text-align: center;
                    border: 1px solid #9ca3af;
                }
                td {
                    font-size: 10px;
                    padding: 8px;
                    border: 1px solid #9ca3af;
                    color: #1f2937;
                }
                .col-dias { width: 25%; }
                .col-inicio { width: 10%; text-align: center; color: #39A900; font-weight: bold; }
                .col-fin { width: 10%; text-align: center; color: #9ca3af; font-weight: bold; }
                .col-ambiente { width: 10%; text-align: center; font-weight: bold; }
                .col-competencia { width: 25%; }
                .col-programa { width: 20%; text-transform: uppercase; font-size: 9px; }

                .footer-total {
                    text-align: right;
                    font-size: 14px;
                    font-weight: bold;
                    color: #39A900;
                    margin-top: 25px;
                    padding-right: 10px;
                }
                }
            </style>
        </head>
        <body>
            <div class="pdf-container" id="pdfContent">
                <div class="main-header">
                    ' . htmlspecialchars($tituloAbsoluto) . '
                </div>
                
                <div class="sub-header">
                    <div class="sub-title">' . htmlspecialchars($subtituloAbsoluto) . '</div>
                    <div class="sub-info">' . $descriptorInfo . ' · Total ' . $etiquetaTiempo . ': ' . $totalHorasGeneral . 'h</div>
                </div>';

        foreach ($agrupadoPorSubGrupo as $grupo) {
            $hd = number_format($grupo['horas_diarias_promedio'], 1, ',', '.');
            $tm = number_format($grupo['total_mes'], 1, ',', '.');
            
            $html .= '<div class="group-card">
                        <div class="group-header">
                            <table style="width: 100%; border: none;">
                                <tr>
                                    <td class="group-header-title" style="text-align: left; border: none; width: 60%;">' . htmlspecialchars($grupo['titulo']) . '</td>
                                    <td class="group-header-stats" style="text-align: right; border: none; width: 20%; font-weight: normal; font-size: 10px;">
                                        h/día ' . ($tipo == 'ficha' ? 'comp' : 'ficha') . ': <b>' . $hd . 'h</b>
                                    </td>
                                    <td class="group-header-stats" style="text-align: right; border: none; width: 20%; font-weight: normal; font-size: 10px;">
                                        Total ' . $etiquetaCorto . ': <b>' . $tm . 'h</b>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th class="col-dias">Días programados</th>
                                    <th class="col-inicio">Inicio</th>
                                    <th class="col-fin">Fin</th>';
            if ($tipo == 'instructor') {
                $html .= '<th class="col-ambiente">Ambiente</th><th class="col-competencia" style="width: 45%;">Competencia</th>';
            } elseif ($tipo == 'ficha') {
                $html .= '<th class="col-ambiente">Ambiente</th><th class="col-programa" style="width: 45%;">Instructor</th>';
            } elseif ($tipo == 'ambiente') {
                $html .= '<th class="col-competencia" style="width: 35%;">Competencia</th><th class="col-programa" style="width: 20%;">Instructor</th>';
            } else {
                $html .= '<th class="col-ambiente">Ambiente</th><th class="col-competencia">Competencia</th><th class="col-programa" style="width: 20%;">Instructor</th>';
            }
            $html .= '              </tr>
                            </thead>
                            <tbody>';
            
            foreach ($grupo['items'] as $item) {
                $html .= '<tr>
                            <td class="col-dias">' . htmlspecialchars($item['string_dias']) . '</td>
                            <td class="col-inicio">' . $this->formatearHora($item['hora_ini']) . '</td>
                            <td class="col-fin">' . $this->formatearHora($item['hora_fin']) . '</td>';
                
                if ($tipo == 'instructor') {
                    $html .= '<td class="col-ambiente">' . htmlspecialchars($item['ambiente']) . '</td>
                              <td class="col-competencia" style="width: 45%;">' . htmlspecialchars($item['competencia']) . '</td>';
                } elseif ($tipo == 'ficha') {
                    $html .= '<td class="col-ambiente">' . htmlspecialchars($item['ambiente']) . '</td>
                              <td class="col-programa" style="text-transform: none; width: 45%;">' . htmlspecialchars($item['instructor_req']) . '</td>';
                } elseif ($tipo == 'ambiente') {
                    $html .= '<td class="col-competencia" style="width: 35%;">' . htmlspecialchars($item['competencia']) . '</td>
                              <td class="col-programa" style="text-transform: none; width: 20%;">' . htmlspecialchars($item['instructor_req']) . '</td>';
                } else {
                    $html .= '<td class="col-ambiente">' . htmlspecialchars($item['ambiente']) . '</td>
                              <td class="col-competencia">' . htmlspecialchars($item['competencia']) . '</td>
                              <td class="col-programa" style="text-transform: none; width: 20%;">' . htmlspecialchars($item['instructor_req']) . '</td>';
                }
                $html .= '</tr>';
            }
            $html .= '          </tbody>
                        </table>
                      </div>';
        }

        if (empty($agrupadoPorSubGrupo)) {
            $html .= '<div style="text-align: center; padding: 50px; color: #9ca3af; font-style: italic;">No hay horas asignadas.</div>';
        }
        
        $html .= '      <div class="footer-total">
                            Total ' . htmlspecialchars($tituloAbsoluto) . ': ' . $totalHorasGeneral . 'h
                        </div>
                    </div>
        </body>
        </html>';

        try {
            $mpdf = new \Mpdf\Mpdf([
                'mode'          => 'utf-8',
                'format'        => 'A4',
                'orientation'   => 'L',
                'margin_top'    => 12,
                'margin_bottom' => 12,
                'margin_left'   => 10,
                'margin_right'  => 10,
                'tempDir'       => sys_get_temp_dir(),
            ]);
            $mpdf->SetTitle(str_replace(['_', '.pdf'], [' ', ''], $filename));
            $mpdf->WriteHTML($html);
            $mpdf->Output($filename, 'I');
            exit;
        } catch (\Mpdf\MpdfException $e) {
            http_response_code(500);
            echo "Error al generar PDF: " . $e->getMessage();
            exit;
        }
    }
}
