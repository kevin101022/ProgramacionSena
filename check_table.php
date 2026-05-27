<?php
require_once __DIR__ . '/Conexion.php';
$db = Conexion::getConnect();
$stmt = $db->query('DESCRIBE instru_competencia');
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "=== ESTRUCTURA instru_competencia ===\n";
foreach ($cols as $c) {
    echo "  {$c['Field']} | {$c['Type']} | Null:{$c['Null']} | Key:{$c['Key']} | Default:{$c['Default']}\n";
}
echo "\n=== PRIMERAS 5 FILAS ===\n";
$stmt2 = $db->query('SELECT * FROM instru_competencia LIMIT 5');
$rows = $stmt2->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo json_encode($r) . "\n";
}
