<?php
require_once __DIR__ . '/Conexion.php';
require_once __DIR__ . '/model/InstruCompetenciaModel.php';

try {
    $model = new InstruCompetenciaModel();
    $datos = $model->readAll(1); // Simulamos centro 1
    echo "OK. Registros: " . count($datos) . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
