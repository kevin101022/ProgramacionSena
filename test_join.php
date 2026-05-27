<?php
/**
 * Verifica que el nuevo JOIN en InstruCompetenciaModel::readAll() 
 * funciona correctamente con PK compuesta en competencia.
 */
require_once __DIR__ . '/Conexion.php';
$db = Conexion::getConnect();

echo "=== TEST: readAll con PK compuesta en competencia ===\n\n";

// Simular exactamente la consulta de InstruCompetenciaModel::readAll() corregida
$sql = "SELECT ic.inscomp_id, ic.INSTRUCTOR_inst_id as instructor_inst_id, 
               ic.programa_prog_id, 
               ic.competencia_comp_id, 
               i.inst_nombres, i.inst_apellidos, c.comp_nombre_corto, p.prog_denominacion 
        FROM instru_competencia ic
        INNER JOIN instructor i ON ic.INSTRUCTOR_inst_id = i.numero_documento
        INNER JOIN competencia c ON ic.competencia_comp_id = c.comp_id
               AND (ic.programa_prog_id = c.programa_prog_id OR ic.programa_prog_id IS NULL OR ic.programa_prog_id = '')
        LEFT JOIN programa p ON ic.programa_prog_id = p.prog_codigo
        WHERE 1=1
        ORDER BY ic.inscomp_id DESC
        LIMIT 20";

$stmt = $db->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Total filas devueltas: " . count($rows) . "\n\n";

// Agrupar por instructor para ver duplicados
$byInstructor = [];
foreach ($rows as $r) {
    $key = $r['instructor_inst_id'];
    $byInstructor[$key][] = $r;
}

foreach ($byInstructor as $instId => $records) {
    echo "Instructor ID={$instId} ({$records[0]['inst_nombres']} {$records[0]['inst_apellidos']}): " . count($records) . " habilitaciones\n";
    foreach ($records as $rec) {
        $prog = $rec['programa_prog_id'] ?? 'NULL';
        echo "  comp_id={$rec['competencia_comp_id']} | {$rec['comp_nombre_corto']} | prog_id={$prog} | prog_nombre={$rec['prog_denominacion']}\n";
    }
}

echo "\n=== TEST: Filtro JS simulado (ficha programa=228106, comp=113) ===\n";
// Simular exactamente lo que hace el JS:
// h.competencia_comp_id == compId && (h.programa_prog_id == progId || null || '' || undefined)
$compId = 113;
$progId = 228106;

$filtered = array_filter($rows, function($h) use ($compId, $progId) {
    return $h['competencia_comp_id'] == $compId &&
        ($h['programa_prog_id'] == $progId || $h['programa_prog_id'] === null || $h['programa_prog_id'] === '');
});

if (empty($filtered)) {
    echo "❌ NINGÚN instructor encontrado para comp_id={$compId}, prog_id={$progId}\n";
    echo "   Revisa si hay datos en instru_competencia para esa combinación.\n";
} else {
    echo "✅ Instructores encontrados:\n";
    foreach ($filtered as $f) {
        echo "  {$f['inst_nombres']} {$f['inst_apellidos']} (inst_id={$f['instructor_inst_id']}, prog_guardado={$f['programa_prog_id']})\n";
    }
}

echo "\n=== TEST: Sin filtro de programa (comportamiento anterior BUGGY) ===\n";
$filteredOld = array_filter($rows, function($h) use ($compId) {
    return $h['competencia_comp_id'] == $compId;
});
echo "Sin filtro de programa: " . count($filteredOld) . " instructores\n";
echo "Con filtro de programa: " . count($filtered) . " instructores\n";
