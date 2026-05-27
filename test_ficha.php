<?php
session_start();
$_SESSION['usuario_id'] = 1;
$_SESSION['rol'] = 'admin';

require_once __DIR__ . '/controller/fichaController.php';
$controller = new FichaController();

ob_start();
$controller->index();
$output = ob_get_clean();

$data = json_decode($output, true);
echo "Count: " . count($data) . "\n";
echo json_encode(array_slice($data, 0, 5), JSON_PRETTY_PRINT);
