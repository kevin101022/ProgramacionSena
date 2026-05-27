<?php
$ch = curl_init('http://localhost/ProgramacionSena/routing.php?controller=instru_competencia&action=index');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
$data = json_decode($res, true);

if (!$data) {
    echo "ERROR PARSING JSON. Raw output:\n";
    echo substr($res, 0, 500);
} else {
    echo "SUCCESS. Total items: " . count($data) . "\n";
    echo "First 5 items:\n";
    echo json_encode(array_slice($data, 0, 5), JSON_PRETTY_PRINT);
}
