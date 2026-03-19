<?php
/**
 * ReportePdfController - Generación de reportes PDF para calendarios
 * Usa TCPDF para generar documentos profesionales
 */

require_once dirname(__DIR__) . '/Conexion.php';

class ReportePdfController
{
    private $db;

    public function __construct()
    {
        $this->db = Conexion::getConnect();
    }

    /**
     * Genera PDF de calendario de Ficha
     */
    public function calendarioFicha()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $fichId = $_GET['fich_id'] ?? null;
        if (!$fichId) {
            http_response_code(400);
            die('ID de ficha requerido');
        }

        // Obtener datos de la ficha
        $ficha = $this->getFichaData($fichId);
        if (!$ficha) {
            http_response_code(404);
            die('Ficha no encontrada');
        }

        // Obtener asignaciones agrupadas
        $asignaciones = $this->getAsignacionesFicha($fichId);

        // Generar PDF
        $this->generarPDF(
            'Reporte de Asignaciones - Ficha',
            "Ficha: {$ficha['fich_id']}",
            $ficha['prog_denominacion'] ?? $ficha['titpro_nombre'] ?? 'Sin programa',
            $asignaciones,
            'ficha',
            "Calendario_Ficha_{$fichId}.pdf"
        );
    }

    /**
     * Genera PDF de calendario de Instructor
     */
    public function calendarioInstructor()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $instId = $_GET['inst_id'] ?? null;
        if (!$instId) {
            http_response_code(400);
            die('ID de instructor requerido');
        }

        // Obtener datos del instructor
        $instructor = $this->getInstructorData($instId);
        if (!$instructor) {
            http_response_code(404);
            die('Instructor no encontrado');
        }

        // Obtener asignaciones agrupadas
        $asignaciones = $this->getAsignacionesInstructor($instId);

        // Generar PDF
        $this->generarPDF(
            'Reporte de Asignaciones - Instructor',
            "{$instructor['inst_nombres']} {$instructor['inst_apellidos']}",
            "Documento: {$instructor['numero_documento']}",
            $asignaciones,
            'instructor',
            "Calendario_Instructor_{$instId}.pdf"
        );
    }

    /**
     * Genera PDF de calendario de Ambiente
     */
    public function calendarioAmbiente()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $ambId = $_GET['amb_id'] ?? null;
        if (!$ambId) {
            http_response_code(400);
            die('ID de ambiente requerido');
        }

        // Obtener datos del ambiente
        $ambiente = $this->getAmbienteData($ambId);
        if (!$ambiente) {
            http_response_code(404);
            die('Ambiente no encontrado');
        }

        // Obtener asignaciones agrupadas
        $asignaciones = $this->getAsignacionesAmbiente($ambId);

        // Generar PDF
        $this->generarPDF(
            'Reporte de Asignaciones - Ambiente',
            "Ambiente: {$ambiente['amb_id']} - {$ambiente['amb_nombre']}",
            "Sede: {$ambiente['sede_nombre']}",
            $asignaciones,
            'ambiente',
            "Calendario_Ambiente_{$ambId}.pdf"
        );
    }

    /**
     * Genera PDF del Calendario Total (filtrado por rol)
     */
    public function calendarioTotal()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $rol     = $_SESSION['rol'] ?? null;
        $cent_id = $_SESSION['centro_id'] ?? null;
        $user_id = $_SESSION['id'] ?? null;

        // Resolver coord_id para coordinador
        $coord_id   = null;
        $coord_desc = 'Todas las coordinaciones';
        if ($rol === 'coordinador' && $user_id) {
            $stmt = $this->db->prepare("SELECT coord_id, coord_descripcion FROM COORDINACION WHERE coordinador_actual = :uid AND estado = 1 LIMIT 1");
            $stmt->execute([':uid' => $user_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $coord_id   = $row['coord_id'] ?? null;
            $coord_desc = $row['coord_descripcion'] ?? 'Mi coordinación';
        }

        // Subtítulo según rol
        $subtitulo = $rol === 'coordinador' ? "Coordinación: {$coord_desc}" : 'Todas las coordinaciones del centro';

        $asignaciones = $this->getAsignacionesTotal($cent_id, $coord_id);

        $this->generarPDF(
            'Reporte de Asignaciones - Calendario Total',
            $subtitulo,
            'Generado: ' . date('d/m/Y H:i'),
            $asignaciones,
            'total',
            'Calendario_Total_' . date('Ymd') . '.pdf'
        );
    }

    /**
     * Obtiene todas las asignaciones filtradas por centro o coordinación
     */
    private function getAsignacionesTotal($cent_id = null, $coord_id = null)
    {
        $sql = "SELECT a.asig_id, a.asig_fecha_ini, a.asig_fecha_fin,
                       co.coord_descripcion,
                       f.fich_id,
                       c.comp_nombre_corto, c.comp_nombre_unidad_competencia,
                       i.inst_nombres, i.inst_apellidos,
                       amb.amb_id, amb.amb_nombre,
                       d.detasig_fecha, d.detasig_hora_ini, d.detasig_hora_fin
                FROM ASIGNACION a
                INNER JOIN FICHA f ON a.FICHA_fich_id = f.fich_id
                INNER JOIN COORDINACION co ON f.COORDINACION_coord_id = co.coord_id
                INNER JOIN COMPETENCIA c ON a.COMPETENCIA_comp_id = c.comp_id
                INNER JOIN INSTRUCTOR i ON a.INSTRUCTOR_inst_id = i.numero_documento
                LEFT JOIN AMBIENTE amb ON a.AMBIENTE_amb_id = amb.amb_id
                LEFT JOIN DETALLExASIGNACION d ON a.asig_id = d.ASIGNACION_asig_id";

        $params = [];
        if ($coord_id) {
            $sql .= " WHERE co.coord_id = :coord_id";
            $params[':coord_id'] = $coord_id;
        } elseif ($cent_id) {
            $sql .= " WHERE co.CENTRO_FORMACION_cent_id = :cent_id";
            $params[':cent_id'] = $cent_id;
        }
        $sql .= " ORDER BY co.coord_descripcion, d.detasig_fecha, d.detasig_hora_ini";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $this->agruparAsignacionesTotal($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Agrupa asignaciones del calendario total por asig_id (una fila por asignación)
     */
    private function agruparAsignacionesTotal($detalles)
    {
        if (empty($detalles)) return [];

        $grupos = [];

        foreach ($detalles as $d) {
            $key = $d['asig_id'];

            if (!isset($grupos[$key])) {
                $grupos[$key] = [
                    'key'              => $key,
                    'fecha_inicio'     => $d['detasig_fecha'],
                    'fecha_fin'        => $d['detasig_fecha'],
                    'hora_ini'         => $d['detasig_hora_ini'],
                    'hora_fin'         => $d['detasig_hora_fin'],
                    'coordinacion'     => $d['coord_descripcion'],
                    'ficha'            => $d['fich_id'],
                    'competencia'      => $d['comp_nombre_corto'],
                    'competencia_full' => $d['comp_nombre_unidad_competencia'],
                    'instructor'       => trim($d['inst_nombres'] . ' ' . $d['inst_apellidos']),
                    'ambiente'         => $d['amb_id']
                        ? $d['amb_id'] . ' - ' . ($d['amb_nombre'] ?? '')
                        : null,
                ];
            } else {
                // Extender rango de fechas
                if ($d['detasig_fecha'] < $grupos[$key]['fecha_inicio']) {
                    $grupos[$key]['fecha_inicio'] = $d['detasig_fecha'];
                }
                if ($d['detasig_fecha'] > $grupos[$key]['fecha_fin']) {
                    $grupos[$key]['fecha_fin'] = $d['detasig_fecha'];
                }
            }
        }

        return array_values($grupos);
    }

    /**
     * Obtiene datos de una ficha
     */
    private function getFichaData($fichId)
    {
        $sql = "SELECT f.fich_id, f.fich_jornada, 
                       p.prog_denominacion, tp.titpro_nombre
                FROM FICHA f
                LEFT JOIN PROGRAMA p ON f.PROGRAMA_prog_id = p.prog_codigo
                LEFT JOIN TITULO_PROGRAMA tp ON p.TIT_PROGRAMA_titpro_id = tp.titpro_id
                WHERE f.fich_id = :fich_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':fich_id' => $fichId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene datos de un instructor
     */
    private function getInstructorData($instId)
    {
        $sql = "SELECT numero_documento, inst_nombres, inst_apellidos, inst_correo
                FROM INSTRUCTOR
                WHERE numero_documento = :inst_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':inst_id' => $instId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene datos de un ambiente
     */
    private function getAmbienteData($ambId)
    {
        $sql = "SELECT a.amb_id, a.amb_nombre, s.sede_nombre
                FROM AMBIENTE a
                LEFT JOIN SEDE s ON a.SEDE_sede_id = s.sede_id
                WHERE a.amb_id = :amb_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':amb_id' => $ambId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene asignaciones de una ficha agrupadas por rango
     */
    private function getAsignacionesFicha($fichId)
    {
        $sql = "SELECT a.asig_id, a.asig_fecha_ini, a.asig_fecha_fin,
                       c.comp_nombre_corto, c.comp_nombre_unidad_competencia,
                       i.inst_nombres, i.inst_apellidos,
                       amb.amb_id, amb.amb_nombre,
                       d.detasig_fecha, d.detasig_hora_ini, d.detasig_hora_fin
                FROM ASIGNACION a
                INNER JOIN COMPETENCIA c ON a.COMPETENCIA_comp_id = c.comp_id
                INNER JOIN INSTRUCTOR i ON a.INSTRUCTOR_inst_id = i.numero_documento
                LEFT JOIN AMBIENTE amb ON a.AMBIENTE_amb_id = amb.amb_id
                LEFT JOIN DETALLExASIGNACION d ON a.asig_id = d.ASIGNACION_asig_id
                WHERE a.FICHA_fich_id = :fich_id
                ORDER BY d.detasig_fecha, d.detasig_hora_ini";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':fich_id' => $fichId]);
        return $this->agruparAsignaciones($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Obtiene asignaciones de un instructor agrupadas por rango
     */
    private function getAsignacionesInstructor($instId)
    {
        $sql = "SELECT a.asig_id, a.asig_fecha_ini, a.asig_fecha_fin,
                       f.fich_id,
                       c.comp_nombre_corto, c.comp_nombre_unidad_competencia,
                       amb.amb_id, amb.amb_nombre,
                       d.detasig_fecha, d.detasig_hora_ini, d.detasig_hora_fin
                FROM ASIGNACION a
                INNER JOIN FICHA f ON a.FICHA_fich_id = f.fich_id
                INNER JOIN COMPETENCIA c ON a.COMPETENCIA_comp_id = c.comp_id
                LEFT JOIN AMBIENTE amb ON a.AMBIENTE_amb_id = amb.amb_id
                LEFT JOIN DETALLExASIGNACION d ON a.asig_id = d.ASIGNACION_asig_id
                WHERE a.INSTRUCTOR_inst_id = :inst_id
                ORDER BY d.detasig_fecha, d.detasig_hora_ini";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':inst_id' => $instId]);
        return $this->agruparAsignaciones($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Obtiene asignaciones de un ambiente agrupadas por rango
     */
    private function getAsignacionesAmbiente($ambId)
    {
        $sql = "SELECT a.asig_id, a.asig_fecha_ini, a.asig_fecha_fin,
                       f.fich_id,
                       c.comp_nombre_corto, c.comp_nombre_unidad_competencia,
                       i.inst_nombres, i.inst_apellidos,
                       d.detasig_fecha, d.detasig_hora_ini, d.detasig_hora_fin
                FROM ASIGNACION a
                INNER JOIN FICHA f ON a.FICHA_fich_id = f.fich_id
                INNER JOIN COMPETENCIA c ON a.COMPETENCIA_comp_id = c.comp_id
                INNER JOIN INSTRUCTOR i ON a.INSTRUCTOR_inst_id = i.numero_documento
                LEFT JOIN DETALLExASIGNACION d ON a.asig_id = d.ASIGNACION_asig_id
                WHERE a.AMBIENTE_amb_id = :amb_id
                ORDER BY d.detasig_fecha, d.detasig_hora_ini";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':amb_id' => $ambId]);
        return $this->agruparAsignaciones($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Agrupa asignaciones por asig_id — una fila por asignación con rango min/max de fechas
     */
    private function agruparAsignaciones($detalles)
    {
        if (empty($detalles)) return [];

        $grupos = [];

        foreach ($detalles as $d) {
            $key = $d['asig_id'];

            if (!isset($grupos[$key])) {
                $grupos[$key] = [
                    'key'              => $key,
                    'fecha_inicio'     => $d['detasig_fecha'],
                    'fecha_fin'        => $d['detasig_fecha'],
                    'hora_ini'         => $d['detasig_hora_ini'],
                    'hora_fin'         => $d['detasig_hora_fin'],
                    'ficha'            => $d['fich_id'] ?? null,
                    'competencia'      => $d['comp_nombre_corto'],
                    'competencia_full' => $d['comp_nombre_unidad_competencia'],
                    'instructor'       => isset($d['inst_nombres'])
                        ? trim($d['inst_nombres'] . ' ' . $d['inst_apellidos'])
                        : null,
                    'ambiente'         => isset($d['amb_id'])
                        ? $d['amb_id'] . ' - ' . ($d['amb_nombre'] ?? '')
                        : null,
                ];
            } else {
                if ($d['detasig_fecha'] < $grupos[$key]['fecha_inicio']) {
                    $grupos[$key]['fecha_inicio'] = $d['detasig_fecha'];
                }
                if ($d['detasig_fecha'] > $grupos[$key]['fecha_fin']) {
                    $grupos[$key]['fecha_fin'] = $d['detasig_fecha'];
                }
            }
        }

        return array_values($grupos);
    }

    /**
     * Genera el PDF usando HTML y CSS
     */
    private function generarPDF($titulo, $subtitulo, $info, $asignaciones, $tipo, $filename)
    {
        // Generar HTML del PDF
        $html = $this->generarHTMLPDF($titulo, $subtitulo, $info, $asignaciones, $tipo, $filename);

        // Intentar usar FPDF si está disponible
        $fpdfPath = dirname(__DIR__) . '/lib/fpdf/fpdf.php';
        
        if (file_exists($fpdfPath)) {
            $this->generarConFPDF($titulo, $subtitulo, $info, $asignaciones, $tipo, $filename);
        } else {
            // Fallback: Enviar HTML optimizado para impresión a PDF desde el navegador
            header('Content-Type: text/html; charset=UTF-8');
            echo $html;
        }
    }

    /**
     * Genera el HTML del reporte
     */
    private function generarHTMLPDF($titulo, $subtitulo, $info, $asignaciones, $tipo, $filename)
    {
        $totalAsignaciones = count($asignaciones);
        $fecha = date('d/m/Y H:i');

        // Determinar columnas según tipo
        $columnas = $this->getColumnasPorTipo($tipo);

        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title><?php echo htmlspecialchars($filename); ?></title>
            <style>
                @page { 
                    size: A4 landscape; 
                    margin: 15mm;
                }
                @media print {
                    body { margin: 0; }
                    .no-print { display: none; }
                }
                body {
                    font-family: 'Arial', sans-serif;
                    font-size: 9pt;
                    color: #1f2937;
                    margin: 0;
                    padding: 20px;
                    background: white;
                }
                .header {
                    background: linear-gradient(135deg, #39A900, #2d8a00);
                    color: white;
                    padding: 15px 20px;
                    margin-bottom: 15px;
                    border-radius: 8px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                .header-left h1 {
                    margin: 0 0 5px 0;
                    font-size: 18pt;
                    font-weight: bold;
                }
                .header-left p {
                    margin: 2px 0;
                    font-size: 10pt;
                    opacity: 0.95;
                }
                .header-right {
                    text-align: right;
                    font-size: 8pt;
                }
                .stats-bar {
                    background: #f3f4f6;
                    padding: 10px 15px;
                    margin-bottom: 15px;
                    border-radius: 6px;
                    border-left: 4px solid #39A900;
                }
                .stats-bar strong {
                    color: #39A900;
                    font-size: 11pt;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 10px;
                }
                thead {
                    background: #39A900;
                    color: white;
                }
                th {
                    padding: 8px 6px;
                    text-align: left;
                    font-size: 8pt;
                    font-weight: bold;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
                tbody tr:nth-child(even) {
                    background: #f9fafb;
                }
                tbody tr:nth-child(odd) {
                    background: white;
                }
                td {
                    padding: 7px 6px;
                    border-bottom: 1px solid #e5e7eb;
                    font-size: 8pt;
                    vertical-align: top;
                }
                .fecha-col {
                    font-weight: 600;
                    color: #374151;
                    white-space: nowrap;
                }
                .hora-col {
                    color: #6b7280;
                    white-space: nowrap;
                }
                .competencia-col {
                    font-weight: 500;
                    color: #1f2937;
                }
                .footer {
                    margin-top: 20px;
                    padding-top: 10px;
                    border-top: 2px solid #e5e7eb;
                    text-align: center;
                    font-size: 7pt;
                    color: #9ca3af;
                }
                .no-data {
                    text-align: center;
                    padding: 40px;
                    color: #9ca3af;
                    font-style: italic;
                }
                .print-button {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: #39A900;
                    color: white;
                    border: none;
                    padding: 12px 24px;
                    border-radius: 8px;
                    cursor: pointer;
                    font-size: 14px;
                    font-weight: bold;
                    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                    z-index: 1000;
                }
                .print-button:hover {
                    background: #2d8a00;
                }
            </style>
            <script>
                function printPDF() {
                    window.print();
                }
                // Auto-abrir diálogo de impresión después de cargar
                window.onload = function() {
                    setTimeout(function() {
                        window.print();
                    }, 500);
                };
            </script>
        </head>
        <body>
            <button class="print-button no-print" onclick="printPDF()">
                🖨️ Imprimir / Guardar como PDF
            </button>
            
            <div class="header">
                <div class="header-left">
                    <h1><?php echo htmlspecialchars($titulo); ?></h1>
                    <p><strong><?php echo htmlspecialchars($subtitulo); ?></strong></p>
                    <p><?php echo htmlspecialchars($info); ?></p>
                </div>
                <div class="header-right">
                    <p><strong>SENA</strong></p>
                    <p>Sistema de Programaciones</p>
                    <p><?php echo $fecha; ?></p>
                </div>
            </div>

            <div class="stats-bar">
                <strong><?php echo $totalAsignaciones; ?></strong> asignaciones programadas
            </div>

            <?php if (empty($asignaciones)): ?>
                <div class="no-data">
                    No hay asignaciones registradas para este elemento.
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <?php foreach ($columnas as $col): ?>
                                <th><?php echo $col; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($asignaciones as $asig): ?>
                            <tr>
                                <td class="fecha-col">
                                    <?php 
                                    if ($asig['fecha_inicio'] === $asig['fecha_fin']) {
                                        echo date('d/m/Y', strtotime($asig['fecha_inicio']));
                                    } else {
                                        echo date('d/m/Y', strtotime($asig['fecha_inicio'])) . 
                                             ' - ' . 
                                             date('d/m/Y', strtotime($asig['fecha_fin']));
                                    }
                                    ?>
                                </td>
                                <td class="hora-col"><?php echo substr($asig['hora_ini'], 0, 5); ?></td>
                                <td class="hora-col"><?php echo substr($asig['hora_fin'], 0, 5); ?></td>
                                <?php if ($tipo === 'total'): ?>
                                    <td><?php echo htmlspecialchars($asig['coordinacion'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($asig['ficha'] ?? 'N/A'); ?></td>
                                    <td class="competencia-col"><?php echo htmlspecialchars($asig['competencia']); ?></td>
                                    <td><?php echo htmlspecialchars($asig['instructor'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($asig['ambiente'] ?? 'N/A'); ?></td>
                                <?php else: ?>
                                    <?php if ($tipo !== 'ficha'): ?>
                                        <td><?php echo htmlspecialchars($asig['ficha'] ?? 'N/A'); ?></td>
                                    <?php endif; ?>
                                    <td class="competencia-col"><?php echo htmlspecialchars($asig['competencia']); ?></td>
                                    <?php if ($tipo !== 'ambiente'): ?>
                                        <td><?php echo htmlspecialchars($asig['ambiente'] ?? 'N/A'); ?></td>
                                    <?php endif; ?>
                                    <?php if ($tipo !== 'instructor'): ?>
                                        <td><?php echo htmlspecialchars($asig['instructor'] ?? 'N/A'); ?></td>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <div class="footer">
                <p>Documento generado automáticamente por el Sistema de Programaciones SENA</p>
                <p>Este reporte es válido únicamente como referencia informativa</p>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }

    /**
     * Obtiene las columnas según el tipo de reporte
     */
    private function getColumnasPorTipo($tipo)
    {
        $base = ['Fecha/Rango', 'Hora Inicio', 'Hora Fin'];
        
        switch ($tipo) {
            case 'ficha':
                return array_merge($base, ['Competencia', 'Ambiente', 'Instructor']);
            case 'instructor':
                return array_merge($base, ['Ficha', 'Competencia', 'Ambiente']);
            case 'ambiente':
                return array_merge($base, ['Ficha', 'Competencia', 'Instructor']);
            case 'total':
                return array_merge($base, ['Coordinación', 'Ficha', 'Competencia', 'Instructor', 'Ambiente']);
            default:
                return $base;
        }
    }

    /**
     * Genera PDF con FPDF (cuando esté disponible)
     */
    private function generarConFPDF($titulo, $subtitulo, $info, $asignaciones, $tipo, $filename)
    {
        require_once dirname(__DIR__) . '/lib/fpdf/fpdf.php';
        
        $pdf = new \FPDF('L', 'mm', 'A4'); // Landscape
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        
        // Header
        $pdf->SetFillColor(57, 169, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 15, utf8_decode($titulo), 0, 1, 'C', true);
        
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, utf8_decode($subtitulo), 0, 1, 'C', true);
        $pdf->Cell(0, 6, utf8_decode($info), 0, 1, 'C', true);
        $pdf->Ln(5);
        
        // Stats
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 8, count($asignaciones) . ' asignaciones programadas', 0, 1);
        $pdf->Ln(3);
        
        // Table header
        $columnas = $this->getColumnasPorTipo($tipo);
        $widths = $this->getColumnWidths($tipo);
        
        $pdf->SetFillColor(57, 169, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 8);
        
        foreach ($columnas as $i => $col) {
            $pdf->Cell($widths[$i], 8, utf8_decode($col), 1, 0, 'L', true);
        }
        $pdf->Ln();
        
        // Table body
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);
        $fill = false;
        
        foreach ($asignaciones as $asig) {
            $pdf->SetFillColor($fill ? 249 : 255, $fill ? 250 : 255, $fill ? 251 : 255);
            
            // Fecha
            if ($asig['fecha_inicio'] === $asig['fecha_fin']) {
                $fecha = date('d/m/Y', strtotime($asig['fecha_inicio']));
            } else {
                $fecha = date('d/m/Y', strtotime($asig['fecha_inicio'])) . ' - ' . date('d/m/Y', strtotime($asig['fecha_fin']));
            }
            $pdf->Cell($widths[0], 7, utf8_decode($fecha), 1, 0, 'L', true);
            
            // Horas
            $pdf->Cell($widths[1], 7, substr($asig['hora_ini'], 0, 5), 1, 0, 'L', true);
            $pdf->Cell($widths[2], 7, substr($asig['hora_fin'], 0, 5), 1, 0, 'L', true);
            
            // Columnas dinámicas según tipo
            $colIndex = 3;
            if ($tipo === 'total') {
                $pdf->Cell($widths[$colIndex], 7, utf8_decode(substr($asig['coordinacion'] ?? 'N/A', 0, 25)), 1, 0, 'L', true); $colIndex++;
                $pdf->Cell($widths[$colIndex], 7, utf8_decode($asig['ficha'] ?? 'N/A'), 1, 0, 'L', true); $colIndex++;
                $pdf->Cell($widths[$colIndex], 7, utf8_decode(substr($asig['competencia'], 0, 30)), 1, 0, 'L', true); $colIndex++;
                $pdf->Cell($widths[$colIndex], 7, utf8_decode(substr($asig['instructor'] ?? 'N/A', 0, 25)), 1, 0, 'L', true); $colIndex++;
                $pdf->Cell($widths[$colIndex], 7, utf8_decode(substr($asig['ambiente'] ?? 'N/A', 0, 15)), 1, 0, 'L', true);
            } else {
                if ($tipo !== 'ficha') {
                    $pdf->Cell($widths[$colIndex], 7, utf8_decode($asig['ficha'] ?? 'N/A'), 1, 0, 'L', true);
                    $colIndex++;
                }
                $pdf->Cell($widths[$colIndex], 7, utf8_decode(substr($asig['competencia'], 0, 30)), 1, 0, 'L', true);
                $colIndex++;
                if ($tipo !== 'ambiente') {
                    $pdf->Cell($widths[$colIndex], 7, utf8_decode(substr($asig['ambiente'] ?? 'N/A', 0, 20)), 1, 0, 'L', true);
                    $colIndex++;
                }
                if ($tipo !== 'instructor') {
                    $pdf->Cell($widths[$colIndex], 7, utf8_decode(substr($asig['instructor'] ?? 'N/A', 0, 25)), 1, 0, 'L', true);
                }
            }
            
            $pdf->Ln();
            $fill = !$fill;
        }
        
        // Footer
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'I', 7);
        $pdf->SetTextColor(150, 150, 150);
        $pdf->Cell(0, 5, utf8_decode('Documento generado automáticamente por el Sistema de Programaciones SENA'), 0, 1, 'C');
        
        // Output
        $pdf->Output('D', $filename);
    }
    
    /**
     * Obtiene los anchos de columna según el tipo
     */
    private function getColumnWidths($tipo)
    {
        switch ($tipo) {
            case 'ficha':
                return [45, 20, 20, 80, 45, 50]; // Fecha, HoraIni, HoraFin, Competencia, Ambiente, Instructor
            case 'instructor':
                return [45, 20, 20, 25, 80, 45]; // Fecha, HoraIni, HoraFin, Ficha, Competencia, Ambiente
            case 'ambiente':
                return [45, 20, 20, 25, 80, 50]; // Fecha, HoraIni, HoraFin, Ficha, Competencia, Instructor
            case 'total':
                return [38, 18, 18, 45, 18, 55, 45, 30]; // Fecha, HoraIni, HoraFin, Coord, Ficha, Comp, Instructor, Ambiente
            default:
                return [45, 20, 20, 80, 45, 50];
        }
    }
}
