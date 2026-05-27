<?php
/**
 * DIAGNÓSTICO COMPLETO DEL FLUJO DE ASIGNACIÓN → CARGA DE INSTRUCTORES
 * Accede a: http://localhost/ProgramacionSena/diagnostico_asignacion.php
 */
require_once __DIR__ . '/Conexion.php';
$db = Conexion::getConnect();

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
<title>Diagnóstico Asignación</title>
<style>
body { font-family: monospace; padding: 20px; background: #1a1a2e; color: #eee; }
h2 { color: #39a900; border-bottom: 1px solid #39a900; padding-bottom: 8px; }
h3 { color: #ffcc00; margin-top: 20px; }
table { border-collapse: collapse; width: 100%; margin-bottom: 20px; font-size: 13px; }
th { background: #39a900; color: white; padding: 6px 10px; text-align: left; }
td { border: 1px solid #333; padding: 5px 10px; }
tr:nth-child(even) { background: #222; }
.ok { color: #39a900; font-weight: bold; }
.error { color: #ff4444; font-weight: bold; }
.warn { color: #ffcc00; font-weight: bold; }
pre { background: #111; padding: 10px; border-left: 4px solid #39a900; overflow-x: auto; }
</style>
</head>
<body>

<h2>🔍 DIAGNÓSTICO: Flujo Asignación → Carga Instructores</h2>

<?php
// ============================================================
// PASO 1: ¿Qué fichas existen y qué programa tienen?
// ============================================================
echo "<h3>📋 PASO 1: Fichas disponibles (con su programa)</h3>";
$stmt = $db->query("SELECT f.fich_id, f.PROGRAMA_prog_id as programa_prog_id, p.prog_denominacion 
                     FROM ficha f 
                     LEFT JOIN programa p ON f.PROGRAMA_prog_id = p.prog_codigo 
                     ORDER BY f.fich_id DESC LIMIT 10");
$fichas = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($fichas)) {
    echo "<p class='error'>❌ No hay fichas en la BD</p>";
} else {
    echo "<table><tr><th>fich_id</th><th>programa_prog_id</th><th>prog_denominacion</th></tr>";
    foreach ($fichas as $f) {
        echo "<tr><td>{$f['fich_id']}</td><td>{$f['programa_prog_id']}</td><td>{$f['prog_denominacion']}</td></tr>";
    }
    echo "</table>";
}

// ============================================================
// PASO 2: ¿Qué competencias existen (con su programa)?
// ============================================================
echo "<h3>📚 PASO 2: Competencias (con programa)</h3>";
$stmt = $db->query("SELECT c.comp_id, c.comp_nombre_corto, c.programa_prog_id, p.prog_denominacion 
                     FROM competencia c 
                     LEFT JOIN programa p ON c.programa_prog_id = p.prog_codigo 
                     ORDER BY c.comp_id DESC LIMIT 20");
$comps = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($comps)) {
    echo "<p class='error'>❌ No hay competencias en la BD</p>";
} else {
    echo "<table><tr><th>comp_id</th><th>comp_nombre_corto</th><th>programa_prog_id (en competencia)</th><th>prog_denominacion</th></tr>";
    foreach ($comps as $c) {
        $progVal = $c['programa_prog_id'] ?? '<span class="warn">NULL</span>';
        echo "<tr><td>{$c['comp_id']}</td><td>{$c['comp_nombre_corto']}</td><td>$progVal</td><td>{$c['prog_denominacion']}</td></tr>";
    }
    echo "</table>";
}

// ============================================================
// PASO 3: ¿Qué tiene instru_competencia?
// ============================================================
echo "<h3>🔗 PASO 3: Tabla instru_competencia (lo que se guarda cuando creas un instructor)</h3>";
$stmt = $db->query("SELECT ic.inscomp_id, ic.INSTRUCTOR_inst_id, ic.programa_prog_id, ic.competencia_comp_id,
                           i.inst_nombres, i.inst_apellidos,
                           c.comp_nombre_corto,
                           p.prog_denominacion
                    FROM instru_competencia ic
                    INNER JOIN instructor i ON ic.INSTRUCTOR_inst_id = i.numero_documento
                    INNER JOIN competencia c ON ic.competencia_comp_id = c.comp_id
                    LEFT JOIN programa p ON ic.programa_prog_id = p.prog_codigo
                    ORDER BY ic.inscomp_id DESC LIMIT 30");
$habs = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($habs)) {
    echo "<p class='error'>❌ instru_competencia está vacía — los instructores no tienen competencias asignadas</p>";
} else {
    echo "<table>
            <tr><th>inscomp_id</th><th>instructor</th><th>programa_prog_id<br>(en instru_competencia)</th><th>competencia_comp_id</th><th>comp_nombre</th><th>prog_denominacion</th></tr>";
    foreach ($habs as $h) {
        $progColor = ($h['programa_prog_id'] === null || $h['programa_prog_id'] === '') 
            ? 'warn' : 'ok';
        $progVal = ($h['programa_prog_id'] === null || $h['programa_prog_id'] === '') 
            ? 'NULL/vacío ⚠️' : $h['programa_prog_id'];
        echo "<tr>
                <td>{$h['inscomp_id']}</td>
                <td>{$h['inst_nombres']} {$h['inst_apellidos']}<br><small>({$h['INSTRUCTOR_inst_id']})</small></td>
                <td class='{$progColor}'>{$progVal}</td>
                <td>{$h['competencia_comp_id']}</td>
                <td>{$h['comp_nombre_corto']}</td>
                <td>{$h['prog_denominacion']}</td>
              </tr>";
    }
    echo "</table>";
}

// ============================================================
// PASO 4: Simulación del filtro del frontend
// Para cada ficha, simular qué instructores cargaría
// ============================================================
echo "<h3>⚙️ PASO 4: Simulación del filtro JS (por ficha + competencia)</h3>";
echo "<p>Para cada combinación ficha+competencia, ¿qué instructores devuelve el filtro?</p>";

// Toma la primera ficha disponible
if (!empty($fichas) && !empty($habs)) {
    $fichaEjemplo = $fichas[0];
    $progIdFicha = $fichaEjemplo['programa_prog_id'];
    
    echo "<p><strong>Ficha ejemplo:</strong> #{$fichaEjemplo['fich_id']} — Programa: <span class='ok'>{$progIdFicha}</span> ({$fichaEjemplo['prog_denominacion']})</p>";
    
    // Obtener competencias de ese programa
    $stmtComp = $db->prepare("SELECT comp_id, comp_nombre_corto, programa_prog_id FROM competencia WHERE programa_prog_id = :pid");
    $stmtComp->execute([':pid' => $progIdFicha]);
    $compsPrograma = $stmtComp->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($compsPrograma)) {
        echo "<p class='error'>❌ No hay competencias para el programa {$progIdFicha} — No aparecerán en el select de asignación</p>";
    } else {
        echo "<p class='ok'>✅ Competencias del programa {$progIdFicha}: " . count($compsPrograma) . "</p>";
        foreach ($compsPrograma as $cp) {
            echo "<h4 style='color:#ccc'>Competencia: {$cp['comp_nombre_corto']} (comp_id={$cp['comp_id']})</h4>";
            
            // Simular filtro JS: h.competencia_comp_id == compId && (h.programa_prog_id == progId || NULL || '')
            $stmtHab = $db->prepare("SELECT ic.*, i.inst_nombres, i.inst_apellidos
                                      FROM instru_competencia ic
                                      INNER JOIN instructor i ON ic.INSTRUCTOR_inst_id = i.numero_documento
                                      WHERE ic.competencia_comp_id = :cid
                                      AND (ic.programa_prog_id = :pid OR ic.programa_prog_id IS NULL OR ic.programa_prog_id = '')");
            $stmtHab->execute([':cid' => $cp['comp_id'], ':pid' => $progIdFicha]);
            $instructoresHab = $stmtHab->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($instructoresHab)) {
                echo "<p class='error'>❌ NINGÚN instructor habilitado para esta competencia en este programa</p>";
                
                // Mostrar si hay instructores con OTRO programa
                $stmtOtros = $db->prepare("SELECT ic.*, i.inst_nombres, i.inst_apellidos, ic.programa_prog_id as prog_guardado
                                            FROM instru_competencia ic
                                            INNER JOIN instructor i ON ic.INSTRUCTOR_inst_id = i.numero_documento
                                            WHERE ic.competencia_comp_id = :cid");
                $stmtOtros->execute([':cid' => $cp['comp_id']]);
                $otros = $stmtOtros->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($otros)) {
                    echo "<p class='warn'>⚠️ Hay " . count($otros) . " instructor(es) con esta competencia pero guardados con OTRO programa_prog_id:</p>";
                    echo "<table><tr><th>Instructor</th><th>programa_prog_id guardado</th><th>programa_prog_id de la ficha</th><th>¿Coincide?</th></tr>";
                    foreach ($otros as $o) {
                        $coincide = ($o['prog_guardado'] == $progIdFicha) ? '<span class="ok">SÍ</span>' : '<span class="error">NO - guardado como: '.$o['prog_guardado'].'</span>';
                        echo "<tr><td>{$o['inst_nombres']} {$o['inst_apellidos']}</td><td class='warn'>{$o['prog_guardado']}</td><td>{$progIdFicha}</td><td>{$coincide}</td></tr>";
                    }
                    echo "</table>";
                }
            } else {
                echo "<table><tr><th>Instructor</th><th>programa_prog_id en instru_competencia</th></tr>";
                foreach ($instructoresHab as $ih) {
                    echo "<tr><td class='ok'>{$ih['inst_nombres']} {$ih['inst_apellidos']}</td><td>{$ih['programa_prog_id']}</td></tr>";
                }
                echo "</table>";
            }
        }
    }
} else {
    echo "<p class='warn'>No hay suficientes datos para simular</p>";
}

// ============================================================
// PASO 5: La clave compuesta que genera el JS de instructor
// ============================================================
echo "<h3>🔑 PASO 5: Verificación de claves compuestas</h3>";
echo "<p>La clave compuesta es: <code>programa_prog_id|comp_id</code></p>";
echo "<p>El JS de asignación filtra usando: <code>h.programa_prog_id == progId</code></p>";
echo "<p>La API <code>instru_competencia?action=index</code> devuelve estos campos:</p>";

$stmt = $db->query("SELECT ic.programa_prog_id, ic.competencia_comp_id FROM instru_competencia LIMIT 5");
$sample = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!empty($sample)) {
    echo "<pre>" . json_encode($sample, JSON_PRETTY_PRINT) . "</pre>";
    $first = $sample[0];
    if ($first['programa_prog_id'] === null || $first['programa_prog_id'] === '') {
        echo "<p class='error'>❌ PROBLEMA: programa_prog_id es NULL/vacío. El filtro del frontend NO encontrará coincidencias con el progId de la ficha.</p>";
        echo "<p class='warn'>⚠️ Esto significa que cuando guardaste el instructor, el programa no se guardó en instru_competencia.</p>";
    } else {
        echo "<p class='ok'>✅ programa_prog_id tiene valor: " . $first['programa_prog_id'] . "</p>";
    }
}
?>

<h3>📋 RESUMEN DE CAMPOS EN instru_competencia</h3>
<?php
$stmt = $db->query("DESCRIBE instru_competencia");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<table><tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
foreach ($cols as $c) {
    echo "<tr><td>{$c['Field']}</td><td>{$c['Type']}</td><td>{$c['Null']}</td><td>{$c['Key']}</td><td>{$c['Default']}</td></tr>";
}
echo "</table>";
?>

</body>
</html>
