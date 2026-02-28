<?php
require_once __DIR__ . '/Conexion.php';
try {
    $pdo = Conexion::getConnect();
    $hash = password_hash('12345', PASSWORD_DEFAULT);
    $pdo->exec("UPDATE centro_formacion SET cent_password = '$hash' WHERE cent_id IN (1, 2)");
    echo "Contraseñas actualizadas a hash bcrypt.";
} catch (Exception $e) {
}
