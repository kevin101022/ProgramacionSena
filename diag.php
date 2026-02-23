<?php
require 'Conexion.php';
try {
    $db = Conexion::getConnect();
    $stmt = $db->query("DESCRIBE PROGRAMA");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "COLUMNS_START\n";
    foreach ($columns as $col) {
        echo $col['Field'] . " (" . $col['Type'] . ")\n";
    }
    echo "COLUMNS_END\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
