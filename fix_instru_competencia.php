<?php
/**
 * CORRECCIÓN AUTOMÁTICA: Rellena programa_prog_id en instru_competencia
 * para los registros que tienen NULL, usando el programa_prog_id de la competencia.
 *
 * Accede a: http://localhost/ProgramacionSena/fix_instru_competencia.php
 * O ejecuta: php -f fix_instru_competencia.php
 */
require_once __DIR__ . '/Conexion.php';
$db = Conexion::getConnect();

echo "=== CORRECCIÓN DE programa_prog_id en instru_competencia ===\n\n";

// 1. Contar registros con programa_prog_id NULL
$stmt = $db->query("SELECT COUNT(*) FROM instru_competencia WHERE programa_prog_id IS NULL OR programa_prog_id = ''");
$nullCount = $stmt->fetchColumn();
echo "Registros con programa_prog_id NULL/vacío: {$nullCount}\n\n";

if ($nullCount == 0) {
    echo "✅ No hay registros que corregir.\n";
    exit(0);
}

// 2. Mostrar qué se va a corregir
$stmt = $db->query("
    SELECT ic.inscomp_id, ic.INSTRUCTOR_inst_id, ic.competencia_comp_id, 
           c.comp_nombre_corto, c.programa_prog_id as prog_de_competencia,
           p.prog_denominacion
    FROM instru_competencia ic
    INNER JOIN competencia c ON ic.competencia_comp_id = c.comp_id
    LEFT JOIN programa p ON c.programa_prog_id = p.prog_codigo
    WHERE (ic.programa_prog_id IS NULL OR ic.programa_prog_id = '')
    ORDER BY ic.inscomp_id
");
$toFix = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Registros que se corregirán:\n";
$canFix = 0;
$cantFix = 0;
foreach ($toFix as $r) {
    if ($r['prog_de_competencia'] !== null && $r['prog_de_competencia'] !== '') {
        echo "  [CORREGIR] inscomp_id={$r['inscomp_id']} | inst={$r['INSTRUCTOR_inst_id']} | comp={$r['comp_nombre_corto']} → prog={$r['prog_de_competencia']} ({$r['prog_denominacion']})\n";
        $canFix++;
    } else {
        echo "  [TRANSVERSAL] inscomp_id={$r['inscomp_id']} | inst={$r['INSTRUCTOR_inst_id']} | comp={$r['comp_nombre_corto']} → Competencia sin programa (transversal, se deja NULL)\n";
        $cantFix++;
    }
}
echo "\nSe pueden corregir: {$canFix} | Transversales (se dejan NULL): {$cantFix}\n\n";

// 3. Aplicar la corrección
if ($canFix > 0) {
    $updateStmt = $db->prepare("
        UPDATE instru_competencia ic
        INNER JOIN competencia c ON ic.competencia_comp_id = c.comp_id
        SET ic.programa_prog_id = c.programa_prog_id
        WHERE (ic.programa_prog_id IS NULL OR ic.programa_prog_id = '')
        AND c.programa_prog_id IS NOT NULL
        AND c.programa_prog_id != ''
    ");
    
    $updateStmt->execute();
    $affected = $updateStmt->rowCount();
    echo "✅ Corrección aplicada. Filas actualizadas: {$affected}\n\n";
    
    // 4. Verificar resultado
    $stmt2 = $db->query("SELECT COUNT(*) FROM instru_competencia WHERE programa_prog_id IS NULL OR programa_prog_id = ''");
    $remainingNull = $stmt2->fetchColumn();
    echo "Registros con programa_prog_id NULL restantes: {$remainingNull} (estos son transversales, OK)\n";
    
    // 5. Mostrar muestra del resultado
    echo "\nMuestra de los primeros 10 registros después de la corrección:\n";
    $stmt3 = $db->query("
        SELECT ic.inscomp_id, ic.INSTRUCTOR_inst_id, ic.programa_prog_id, ic.competencia_comp_id,
               c.comp_nombre_corto, p.prog_denominacion
        FROM instru_competencia ic
        INNER JOIN competencia c ON ic.competencia_comp_id = c.comp_id
        LEFT JOIN programa p ON ic.programa_prog_id = p.prog_codigo
        ORDER BY ic.inscomp_id DESC LIMIT 10
    ");
    $result = $stmt3->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $r) {
        $progVal = $r['programa_prog_id'] ?? 'NULL (transversal)';
        echo "  inscomp_id={$r['inscomp_id']} | {$r['comp_nombre_corto']} | prog_id={$progVal} | prog_nombre={$r['prog_denominacion']}\n";
    }
} else {
    echo "⚠️ No hay registros con programa conocido que corregir.\n";
}

echo "\n=== FIN ===\n";
