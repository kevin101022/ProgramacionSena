<?php
// Just a node.js script to simulate the exact JS logic with real JSON data
$jsCode = <<<JS
const fs = require('fs');

async function test() {
    // Read JSONs
    const fichaData = [
        {"fich_id":2750001,"programa_prog_id":228106,"instructor_inst_id_lider":101,"fich_jornada":"Mañana","coordinacion_coord_id":1,"fich_fecha_ini_lectiva":"2025-09-01","fich_fecha_fin_lectiva":"2027-02-28","prog_denominacion":"Análisis y Desarrollo de Software"}
    ];
    
    // Simulate what allHabilitaciones would be
    const allHabilitaciones = [
        {"inscomp_id":124,"instructor_inst_id":101,"programa_prog_id":228106,"competencia_comp_id":113,"inst_nombres":"Carlos Andrés","inst_apellidos":"Peña Villamizar","comp_nombre_corto":"Prueba","prog_denominacion":"Análisis y Desarrollo de Software"}
    ];

    const fichaId = 2750001; // Or "2750001"
    const compId = "113"; // Select values are strings

    const ficha = fichaData.find(f => f.fich_id == fichaId);
    console.log("Ficha found:", !!ficha);
    
    const progId = ficha.programa_prog_codigo || ficha.programa_prog_id;
    console.log("progId:", progId, typeof progId);

    const habilitados = allHabilitaciones.filter(h =>
        h.competencia_comp_id == compId &&
        (h.programa_prog_id == progId || h.programa_prog_id === null || h.programa_prog_id === '' || h.programa_prog_id === undefined)
    );

    console.log("Habilitados:", habilitados.length);
    console.log(habilitados);
}

test();
JS;

file_put_contents('test_js.js', $jsCode);
