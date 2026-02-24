<?php
header('Content-Type: text/plain');
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "--- Diagnóstico de Conexión ---\n\n";

// 1. Verificar Drivers
echo "1. Drivers disponibles: " . implode(', ', PDO::getAvailableDrivers()) . "\n";
if (!in_array('pgsql', PDO::getAvailableDrivers())) {
    echo "❌ ERROR: Driver 'pgsql' NO encontrado.\n";
} else {
    echo "✅ Driver 'pgsql' detectado.\n";
}

// 2. Cargar .env
require_once __DIR__ . '/EnvLoader.php';
$envLoaded = EnvLoader::load(__DIR__ . '/.env');
echo "\n2. Carga de .env: " . ($envLoaded ? "✅ Éxito" : "❌ Fallo") . "\n";

// 3. Verificar variables de entorno
echo "\n3. Variables cargadas:\n";
$vars = ['DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASS'];
foreach ($vars as $v) {
    $val = getenv($v);
    echo "   $v: " . ($val !== false ? "'$val'" : "❌ NO DEFINIDA") . "\n";
}

// 4. Intento de Conexión Manual
echo "\n4. Intento de conexión manual...\n";
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '5432';
$db   = getenv('DB_NAME') ?: 'transversal';
$user = getenv('DB_USER') ?: 'postgres';
$pass = getenv('DB_PASS') ?: '';

$dsn = "pgsql:host=$host;port=$port;dbname=$db";
echo "   DSN: $dsn\n";
echo "   User: $user\n";

try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "   ✅ CONEXIÓN EXITOSA.\n";
} catch (PDOException $e) {
    echo "   ❌ ERROR DE CONEXIÓN: " . $e->getMessage() . "\n";
    echo "   Código de error: " . $e->getCode() . "\n";

    // Probar con el default de Conexion.php si es diferente
    if ($db !== 'programacionesSena') {
        echo "\n   Probando con el default de Conexion.php ('programacionesSena')...\n";
        $dsn2 = "pgsql:host=$host;port=$port;dbname=programacionesSena";
        try {
            $pdo2 = new PDO($dsn2, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            echo "   ✅ CONEXIÓN EXITOSA con 'programacionesSena'.\n";
            echo "   TIP: Cambia el nombre en tu .env a 'programacionesSena' o en PostgreSQL a 'transversal'.\n";
        } catch (PDOException $e2) {
            echo "   ❌ También falló: " . $e2->getMessage() . "\n";
        }
    }
}
