<?php

/**
 * ============================================
 * SENA — Generador de Datos de Prueba (Seeder)
 * ============================================
 * 
 * Uso desde la terminal:
 *   php scripts/seed.php              → Inserta todos los datos
 *   php scripts/seed.php --clean      → Limpia TODO y luego inserta
 *   php scripts/seed.php --clean-only → Solo limpia, no inserta
 *   php scripts/seed.php --help       → Muestra ayuda
 * 
 * Ejecutar desde la raíz del proyecto:
 *   cd c:\xampp\htdocs\MVC\ProgramacionSena
 *   php scripts/seed.php
 */

// ─── Bootstrap ────────────────────────────────────────────
require_once __DIR__ . '/../Conexion.php';

// Colores para la terminal
function green($text)
{
    return "\033[32m$text\033[0m";
}
function red($text)
{
    return "\033[31m$text\033[0m";
}
function yellow($text)
{
    return "\033[33m$text\033[0m";
}
function cyan($text)
{
    return "\033[36m$text\033[0m";
}
function bold($text)
{
    return "\033[1m$text\033[0m";
}

function banner()
{
    echo "\n";
    echo cyan("  ╔══════════════════════════════════════════╗\n");
    echo cyan("  ║") . bold("   🌱 SENA Seeder — Datos de Prueba       ") . cyan("║\n");
    echo cyan("  ╚══════════════════════════════════════════╝\n");
    echo "\n";
}

function showHelp()
{
    banner();
    echo "  " . bold("Uso:") . " php scripts/seed.php [opciones]\n\n";
    echo "  " . bold("Opciones:\n");
    echo "    " . green("(sin opciones)") . "   Inserta datos de prueba\n";
    echo "    " . yellow("--clean") . "          Limpia TODO y luego inserta\n";
    echo "    " . red("--clean-only") . "     Solo limpia las tablas\n";
    echo "    --help           Muestra esta ayuda\n\n";
}

// ─── Parsear argumentos ──────────────────────────────────
$args = array_slice($argv ?? [], 1);
$doClean = in_array('--clean', $args) || in_array('--clean-only', $args);
$cleanOnly = in_array('--clean-only', $args);

if (in_array('--help', $args)) {
    showHelp();
    exit(0);
}

banner();

try {
    $pdo = Conexion::getConnect();
    $driver = Conexion::getDriver();
    echo "  ✅ Conectado a la base de datos (" . green($driver) . ")\n\n";
} catch (Exception $e) {
    echo "  " . red("❌ Error de conexión: " . $e->getMessage()) . "\n";
    exit(1);
}

// ─── Función helper para insertar ────────────────────────
function insertRow($pdo, $table, $data)
{
    $columns = implode(', ', array_keys($data));
    $placeholders = implode(', ', array_map(fn($k) => ":$k", array_keys($data)));
    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
        return true;
    } catch (PDOException $e) {
        // Si ya existe (duplicate key), skip silenciosamente
        if (
            strpos($e->getMessage(), 'Duplicate') !== false ||
            strpos($e->getMessage(), 'duplicate key') !== false ||
            strpos($e->getMessage(), 'already exists') !== false ||
            $e->getCode() == '23505' || $e->getCode() == '23000'
        ) {
            return false; // ya existe
        }
        throw $e;
    }
}

function progress($label, $count, $inserted)
{
    $skipped = $count - $inserted;
    $status = $inserted > 0 ? green("✓ $inserted insertados") : yellow("→ ya existían");
    if ($skipped > 0 && $inserted > 0) {
        $status .= ", " . yellow("$skipped omitidos");
    }
    echo "  📦 " . str_pad($label, 25) . " $status\n";
}

// ─── LIMPIEZA ────────────────────────────────────────────
if ($doClean) {
    echo "  " . yellow("🧹 Limpiando tablas...") . "\n";

    // Orden inverso de dependencias para DELETE
    $cleanOrder = [
        'detallexasignacion',
        'instru_competencia',
        'asignacion',
        'ficha',
        'coordinacion',
        'ambiente',
        'sede',
        'instructor',
        'centro_formacion',
        'competxprograma',
        'programa',
        'titulo_programa',
        'competencia',
    ];

    foreach ($cleanOrder as $table) {
        try {
            $pdo->exec("DELETE FROM $table");
            echo "    🗑  " . $table . "\n";
        } catch (PDOException $e) {
            // Tabla puede no existir, ignorar
        }
    }
    echo "  " . green("✅ Tablas limpiadas") . "\n\n";

    if ($cleanOnly) {
        echo "  " . bold("Listo.") . " Modo solo-limpieza completado.\n\n";
        exit(0);
    }
}

// ─── DATOS DE PRUEBA ─────────────────────────────────────
echo "  " . cyan("📊 Insertando datos de prueba...") . "\n\n";

// 1. TITULO_PROGRAMA
$titulos = [
    ['titpro_id' => 1, 'titpro_nombre' => 'Tecnólogo'],
    ['titpro_id' => 2, 'titpro_nombre' => 'Técnico'],
    ['titpro_id' => 3, 'titpro_nombre' => 'Auxiliar'],
    ['titpro_id' => 4, 'titpro_nombre' => 'Especialización Tecnológica'],
    ['titpro_id' => 5, 'titpro_nombre' => 'Operario'],
];
$ins = 0;
foreach ($titulos as $row) {
    if (insertRow($pdo, 'titulo_programa', $row)) $ins++;
}
progress('TITULO_PROGRAMA', count($titulos), $ins);

// 2. PROGRAMA
$programas = [
    ['prog_codigo' => 228106, 'prog_denominacion' => 'Análisis y Desarrollo de Software',         'TIT_PROGRAMA_titpro_id' => 1, 'prog_tipo' => 'Presencial'],
    ['prog_codigo' => 228185, 'prog_denominacion' => 'Gestión de Redes de Datos',                 'TIT_PROGRAMA_titpro_id' => 1, 'prog_tipo' => 'Presencial'],
    ['prog_codigo' => 217303, 'prog_denominacion' => 'Contabilidad y Finanzas',                   'TIT_PROGRAMA_titpro_id' => 1, 'prog_tipo' => 'Presencial'],
    ['prog_codigo' => 134101, 'prog_denominacion' => 'Asistencia Administrativa',                 'TIT_PROGRAMA_titpro_id' => 2, 'prog_tipo' => 'Presencial'],
    ['prog_codigo' => 122317, 'prog_denominacion' => 'Programación de Software',                  'TIT_PROGRAMA_titpro_id' => 2, 'prog_tipo' => 'Virtual'],
    ['prog_codigo' => 223219, 'prog_denominacion' => 'Producción Multimedia',                     'TIT_PROGRAMA_titpro_id' => 1, 'prog_tipo' => 'Presencial'],
    ['prog_codigo' => 921310, 'prog_denominacion' => 'Diseño Web con WordPress',                  'TIT_PROGRAMA_titpro_id' => 3, 'prog_tipo' => 'Virtual'],
    ['prog_codigo' => 524232, 'prog_denominacion' => 'Gestión de Seguridad y Salud en el Trabajo', 'TIT_PROGRAMA_titpro_id' => 4, 'prog_tipo' => 'Presencial'],
];
$ins = 0;
foreach ($programas as $row) {
    if (insertRow($pdo, 'programa', $row)) $ins++;
}
progress('PROGRAMA', count($programas), $ins);

// 3. COMPETENCIA
$competencias = [
    ['comp_id' => 1, 'comp_nombre_corto' => 'Ética Profesional',       'comp_horas' => 48,  'comp_nombre_unidad_competencia' => 'Promover la interacción idónea consigo mismo, con los demás y con la naturaleza en los contextos laboral y social'],
    ['comp_id' => 2, 'comp_nombre_corto' => 'Comunicación Efectiva',   'comp_horas' => 120, 'comp_nombre_unidad_competencia' => 'Comprender textos en inglés en forma escrita y auditiva'],
    ['comp_id' => 3, 'comp_nombre_corto' => 'Matemáticas Aplicadas',   'comp_horas' => 80,  'comp_nombre_unidad_competencia' => 'Aplicar herramientas ofimáticas, redes sociales y colaborativas de acuerdo con el proyecto a desarrollar'],
    ['comp_id' => 4, 'comp_nombre_corto' => 'Cultura Física',          'comp_horas' => 60,  'comp_nombre_unidad_competencia' => 'Desarrollar permanentemente las habilidades psicomotrices y de pensamiento en la ejecución de los procesos de aprendizaje'],
    ['comp_id' => 5, 'comp_nombre_corto' => 'Emprendimiento',          'comp_horas' => 100, 'comp_nombre_unidad_competencia' => 'Generar hábitos saludables en su estilo de vida para garantizar la prevención de riesgos ocupacionales'],
    ['comp_id' => 6, 'comp_nombre_corto' => 'Razonamiento Cuantitativo', 'comp_horas' => 72, 'comp_nombre_unidad_competencia' => 'Asumir actitudes críticas, argumentativas y propositivas en función de la resolución de problemas de carácter productivo y social'],
    ['comp_id' => 7, 'comp_nombre_corto' => 'Investigación',           'comp_horas' => 48,  'comp_nombre_unidad_competencia' => 'Gestionar la información de acuerdo con los procedimientos establecidos y con las tecnologías de la información'],
    ['comp_id' => 8, 'comp_nombre_corto' => 'Inglés Técnico',          'comp_horas' => 180, 'comp_nombre_unidad_competencia' => 'Reconocer el rol de los participantes en el proceso formativo, el papel de los ambientes de aprendizaje'],
];
$ins = 0;
foreach ($competencias as $row) {
    if (insertRow($pdo, 'competencia', $row)) $ins++;
}
progress('COMPETENCIA', count($competencias), $ins);

// 4. COMPETxPROGRAMA
$compxprog = [
    ['PROGRAMA_prog_id' => 228106, 'COMPETENCIA_comp_id' => 1],
    ['PROGRAMA_prog_id' => 228106, 'COMPETENCIA_comp_id' => 2],
    ['PROGRAMA_prog_id' => 228106, 'COMPETENCIA_comp_id' => 3],
    ['PROGRAMA_prog_id' => 228185, 'COMPETENCIA_comp_id' => 1],
    ['PROGRAMA_prog_id' => 228185, 'COMPETENCIA_comp_id' => 4],
    ['PROGRAMA_prog_id' => 217303, 'COMPETENCIA_comp_id' => 2],
    ['PROGRAMA_prog_id' => 217303, 'COMPETENCIA_comp_id' => 5],
    ['PROGRAMA_prog_id' => 134101, 'COMPETENCIA_comp_id' => 3],
    ['PROGRAMA_prog_id' => 122317, 'COMPETENCIA_comp_id' => 6],
    ['PROGRAMA_prog_id' => 223219, 'COMPETENCIA_comp_id' => 7],
    ['PROGRAMA_prog_id' => 921310, 'COMPETENCIA_comp_id' => 8],
    ['PROGRAMA_prog_id' => 524232, 'COMPETENCIA_comp_id' => 5],
];
$ins = 0;
foreach ($compxprog as $row) {
    if (insertRow($pdo, 'competxprograma', $row)) $ins++;
}
progress('COMPETxPROGRAMA', count($compxprog), $ins);

// 5. CENTRO_FORMACION
$centros = [
    ['cent_id' => 1, 'cent_nombre' => 'Centro de Servicios y Gestión Empresarial'],
    ['cent_id' => 2, 'cent_nombre' => 'Centro de Tecnología de la Manufactura Avanzada'],
    ['cent_id' => 3, 'cent_nombre' => 'Centro para el Desarrollo del Hábitat y la Construcción'],
    ['cent_id' => 4, 'cent_nombre' => 'Centro de Comercio'],
    ['cent_id' => 5, 'cent_nombre' => 'Centro Agroindustrial y de Fortalecimiento Empresarial'],
];
$ins = 0;
foreach ($centros as $row) {
    if (insertRow($pdo, 'centro_formacion', $row)) $ins++;
}
progress('CENTRO_FORMACION', count($centros), $ins);

// 6. SEDE
$sedes = [
    ['sede_id' => 1, 'sede_nombre' => 'Sede Principal Medellín'],
    ['sede_id' => 2, 'sede_nombre' => 'Centro de Tecnologías Bogotá'],
    ['sede_id' => 3, 'sede_nombre' => 'Sede Agroindustrial Cali'],
    ['sede_id' => 4, 'sede_nombre' => 'Sede Norte Barranquilla'],
    ['sede_id' => 5, 'sede_nombre' => 'Centro de Innovación Bucaramanga'],
    ['sede_id' => 6, 'sede_nombre' => 'Sede Industrial Cartagena'],
    ['sede_id' => 7, 'sede_nombre' => 'Complejo Tecnológico Pereira'],
    ['sede_id' => 8, 'sede_nombre' => 'Centro Agropecuario Villavicencio'],
    ['sede_id' => 9, 'sede_nombre' => 'Sede Turística Santa Marta'],
    ['sede_id' => 10, 'sede_nombre' => 'Centro Minero Sogamoso'],
    ['sede_id' => 11, 'sede_nombre' => 'Sede Artesanal Pasto'],
    ['sede_id' => 12, 'sede_nombre' => 'Centro Náutico Pesquero Tumaco'],
];
$ins = 0;
foreach ($sedes as $row) {
    if (insertRow($pdo, 'sede', $row)) $ins++;
}
progress('SEDE', count($sedes), $ins);

// 7. AMBIENTE
$ambientes = [
    ['amb_id' => 'A101', 'amb_nombre' => 'Laboratorio de Software',      'SEDE_sede_id' => 1],
    ['amb_id' => 'A102', 'amb_nombre' => 'Sala de Redes',                 'SEDE_sede_id' => 1],
    ['amb_id' => 'A103', 'amb_nombre' => 'Aula Magna',                    'SEDE_sede_id' => 1],
    ['amb_id' => 'B201', 'amb_nombre' => 'Laboratorio de Innovación',     'SEDE_sede_id' => 2],
    ['amb_id' => 'B202', 'amb_nombre' => 'Sala de Conferencias',          'SEDE_sede_id' => 2],
    ['amb_id' => 'B203', 'amb_nombre' => 'Taller de Electrónica',         'SEDE_sede_id' => 2],
    ['amb_id' => 'C301', 'amb_nombre' => 'Laboratorio Agroindustrial',    'SEDE_sede_id' => 3],
    ['amb_id' => 'C302', 'amb_nombre' => 'Sala de Procesos',              'SEDE_sede_id' => 3],
    ['amb_id' => 'D401', 'amb_nombre' => 'Aula Virtual Norte',            'SEDE_sede_id' => 4],
    ['amb_id' => 'D402', 'amb_nombre' => 'Laboratorio de Comercio',       'SEDE_sede_id' => 4],
    ['amb_id' => 'E501', 'amb_nombre' => 'Hub de Innovación',             'SEDE_sede_id' => 5],
    ['amb_id' => 'E502', 'amb_nombre' => 'Sala Maker',                    'SEDE_sede_id' => 5],
    ['amb_id' => 'F601', 'amb_nombre' => 'Taller Industrial',             'SEDE_sede_id' => 6],
    ['amb_id' => 'G701', 'amb_nombre' => 'Laboratorio Multimedia',        'SEDE_sede_id' => 7],
    ['amb_id' => 'H801', 'amb_nombre' => 'Campo de Prácticas Agro',       'SEDE_sede_id' => 8],
];
$ins = 0;
foreach ($ambientes as $row) {
    if (insertRow($pdo, 'ambiente', $row)) $ins++;
}
progress('AMBIENTE', count($ambientes), $ins);

// 8. INSTRUCTOR
$instructores = [
    ['inst_id' => 1001, 'inst_nombres' => 'Carlos Alberto',  'inst_apellidos' => 'Rodríguez Marín',     'inst_correo' => 'carlos.rodriguez@sena.edu.co',  'inst_telefono' => 3001234567, 'CENTRO_FORMACION_cent_id' => 1, 'inst_password' => password_hash('Sena2025', PASSWORD_DEFAULT)],
    ['inst_id' => 1002, 'inst_nombres' => 'María José',      'inst_apellidos' => 'González López',      'inst_correo' => 'maria.gonzalez@sena.edu.co',    'inst_telefono' => 3102345678, 'CENTRO_FORMACION_cent_id' => 1, 'inst_password' => password_hash('Sena2025', PASSWORD_DEFAULT)],
    ['inst_id' => 1003, 'inst_nombres' => 'Andrés Felipe',   'inst_apellidos' => 'Pérez Duarte',        'inst_correo' => 'andres.perez@sena.edu.co',      'inst_telefono' => 3203456789, 'CENTRO_FORMACION_cent_id' => 2, 'inst_password' => password_hash('Sena2025', PASSWORD_DEFAULT)],
    ['inst_id' => 1004, 'inst_nombres' => 'Diana Carolina',  'inst_apellidos' => 'Martínez Restrepo',   'inst_correo' => 'diana.martinez@sena.edu.co',    'inst_telefono' => 3004567890, 'CENTRO_FORMACION_cent_id' => 2, 'inst_password' => password_hash('Sena2025', PASSWORD_DEFAULT)],
    ['inst_id' => 1005, 'inst_nombres' => 'Jorge Enrique',   'inst_apellidos' => 'Hernández Villa',     'inst_correo' => 'jorge.hernandez@sena.edu.co',   'inst_telefono' => 3105678901, 'CENTRO_FORMACION_cent_id' => 3, 'inst_password' => password_hash('Sena2025', PASSWORD_DEFAULT)],
    ['inst_id' => 1006, 'inst_nombres' => 'Laura Patricia',  'inst_apellidos' => 'Ospina Cardona',      'inst_correo' => 'laura.ospina@sena.edu.co',      'inst_telefono' => 3206789012, 'CENTRO_FORMACION_cent_id' => 3, 'inst_password' => password_hash('Sena2025', PASSWORD_DEFAULT)],
    ['inst_id' => 1007, 'inst_nombres' => 'Diego Alejandro', 'inst_apellidos' => 'Ramírez Agudelo',     'inst_correo' => 'diego.ramirez@sena.edu.co',     'inst_telefono' => 3007890123, 'CENTRO_FORMACION_cent_id' => 4, 'inst_password' => password_hash('Sena2025', PASSWORD_DEFAULT)],
    ['inst_id' => 1008, 'inst_nombres' => 'Natalia Andrea',  'inst_apellidos' => 'Castro Bermúdez',     'inst_correo' => 'natalia.castro@sena.edu.co',     'inst_telefono' => 3108901234, 'CENTRO_FORMACION_cent_id' => 4, 'inst_password' => password_hash('Sena2025', PASSWORD_DEFAULT)],
    ['inst_id' => 1009, 'inst_nombres' => 'Ricardo Antonio', 'inst_apellidos' => 'Vargas Quintero',     'inst_correo' => 'ricardo.vargas@sena.edu.co',    'inst_telefono' => 3209012345, 'CENTRO_FORMACION_cent_id' => 5, 'inst_password' => password_hash('Sena2025', PASSWORD_DEFAULT)],
    ['inst_id' => 1010, 'inst_nombres' => 'Valentina',       'inst_apellidos' => 'Giraldo Arango',      'inst_correo' => 'valentina.giraldo@sena.edu.co',  'inst_telefono' => 3000123456, 'CENTRO_FORMACION_cent_id' => 5, 'inst_password' => password_hash('Sena2025', PASSWORD_DEFAULT)],
    ['inst_id' => 1011, 'inst_nombres' => 'Sebastián',       'inst_apellidos' => 'Mejía Córdoba',       'inst_correo' => 'sebastian.mejia@sena.edu.co',   'inst_telefono' => 3101234567, 'CENTRO_FORMACION_cent_id' => 1, 'inst_password' => password_hash('Sena2025', PASSWORD_DEFAULT)],
    ['inst_id' => 1012, 'inst_nombres' => 'Camila Alejandra', 'inst_apellidos' => 'Torres Salazar',      'inst_correo' => 'camila.torres@sena.edu.co',     'inst_telefono' => 3202345678, 'CENTRO_FORMACION_cent_id' => 2, 'inst_password' => password_hash('Sena2025', PASSWORD_DEFAULT)],
    ['inst_id' => 1013, 'inst_nombres' => 'Juan David',      'inst_apellidos' => 'López Echeverri',     'inst_correo' => 'juan.lopez@sena.edu.co',        'inst_telefono' => 3003456789, 'CENTRO_FORMACION_cent_id' => 3, 'inst_password' => password_hash('Sena2025', PASSWORD_DEFAULT)],
    ['inst_id' => 1014, 'inst_nombres' => 'Daniela María',   'inst_apellidos' => 'Ríos Montoya',       'inst_correo' => 'daniela.rios@sena.edu.co',      'inst_telefono' => 3104567890, 'CENTRO_FORMACION_cent_id' => 4, 'inst_password' => password_hash('Sena2025', PASSWORD_DEFAULT)],
    ['inst_id' => 1015, 'inst_nombres' => 'Santiago',        'inst_apellidos' => 'Muñoz Castaño',      'inst_correo' => 'santiago.munoz@sena.edu.co',    'inst_telefono' => 3205678901, 'CENTRO_FORMACION_cent_id' => 5, 'inst_password' => password_hash('Sena2025', PASSWORD_DEFAULT)],
];
$ins = 0;
foreach ($instructores as $row) {
    if (insertRow($pdo, 'instructor', $row)) $ins++;
}
progress('INSTRUCTOR', count($instructores), $ins);

// 9. COORDINACION
$coordinaciones = [
    ['coord_id' => 1, 'coord_descripcion' => 'Coordinación de Tecnologías de la Información',  'CENTRO_FORMACION_cent_id' => 1, 'coord_nombre_coordinador' => 'Pedro Luis Gómez',       'coord_correo' => 'pedro.gomez@sena.edu.co',    'coord_password' => password_hash('Coord2025', PASSWORD_DEFAULT)],
    ['coord_id' => 2, 'coord_descripcion' => 'Coordinación de Gestión Administrativa',         'CENTRO_FORMACION_cent_id' => 2, 'coord_nombre_coordinador' => 'Sandra Milena Arias',     'coord_correo' => 'sandra.arias@sena.edu.co',   'coord_password' => password_hash('Coord2025', PASSWORD_DEFAULT)],
    ['coord_id' => 3, 'coord_descripcion' => 'Coordinación Agroindustrial',                    'CENTRO_FORMACION_cent_id' => 3, 'coord_nombre_coordinador' => 'Julio César Ramírez',     'coord_correo' => 'julio.ramirez@sena.edu.co',  'coord_password' => password_hash('Coord2025', PASSWORD_DEFAULT)],
    ['coord_id' => 4, 'coord_descripcion' => 'Coordinación de Comercio y Servicios',           'CENTRO_FORMACION_cent_id' => 4, 'coord_nombre_coordinador' => 'Ana María Velásquez',     'coord_correo' => 'ana.velasquez@sena.edu.co',  'coord_password' => password_hash('Coord2025', PASSWORD_DEFAULT)],
    ['coord_id' => 5, 'coord_descripcion' => 'Coordinación de Innovación y Emprendimiento',    'CENTRO_FORMACION_cent_id' => 5, 'coord_nombre_coordinador' => 'Fernando José Betancur',  'coord_correo' => 'fernando.betancur@sena.edu.co', 'coord_password' => password_hash('Coord2025', PASSWORD_DEFAULT)],
];
$ins = 0;
foreach ($coordinaciones as $row) {
    if (insertRow($pdo, 'coordinacion', $row)) $ins++;
}
progress('COORDINACION', count($coordinaciones), $ins);

// 10. FICHA
$fichas = [
    ['fich_id' => 2547890, 'PROGRAMA_prog_id' => 228106, 'INSTRUCTOR_inst_id_lider' => 1001, 'fich_jornada' => 'Mañana',  'COORDINACION_coord_id' => 1, 'fich_fecha_ini_lectiva' => '2025-02-01', 'fich_fecha_fin_lectiva' => '2026-08-01'],
    ['fich_id' => 2547891, 'PROGRAMA_prog_id' => 228106, 'INSTRUCTOR_inst_id_lider' => 1002, 'fich_jornada' => 'Tarde',   'COORDINACION_coord_id' => 1, 'fich_fecha_ini_lectiva' => '2025-03-15', 'fich_fecha_fin_lectiva' => '2026-09-15'],
    ['fich_id' => 2659012, 'PROGRAMA_prog_id' => 228185, 'INSTRUCTOR_inst_id_lider' => 1003, 'fich_jornada' => 'Mañana',  'COORDINACION_coord_id' => 2, 'fich_fecha_ini_lectiva' => '2025-01-20', 'fich_fecha_fin_lectiva' => '2026-07-20'],
    ['fich_id' => 2659013, 'PROGRAMA_prog_id' => 217303, 'INSTRUCTOR_inst_id_lider' => 1004, 'fich_jornada' => 'Noche',   'COORDINACION_coord_id' => 2, 'fich_fecha_ini_lectiva' => '2025-04-01', 'fich_fecha_fin_lectiva' => '2026-10-01'],
    ['fich_id' => 2760134, 'PROGRAMA_prog_id' => 134101, 'INSTRUCTOR_inst_id_lider' => 1005, 'fich_jornada' => 'Mañana',  'COORDINACION_coord_id' => 3, 'fich_fecha_ini_lectiva' => '2025-02-10', 'fich_fecha_fin_lectiva' => '2025-12-10'],
    ['fich_id' => 2760135, 'PROGRAMA_prog_id' => 122317, 'INSTRUCTOR_inst_id_lider' => 1006, 'fich_jornada' => 'Tarde',   'COORDINACION_coord_id' => 3, 'fich_fecha_ini_lectiva' => '2025-05-01', 'fich_fecha_fin_lectiva' => '2026-03-01'],
    ['fich_id' => 2871256, 'PROGRAMA_prog_id' => 223219, 'INSTRUCTOR_inst_id_lider' => 1007, 'fich_jornada' => 'Mañana',  'COORDINACION_coord_id' => 4, 'fich_fecha_ini_lectiva' => '2025-01-15', 'fich_fecha_fin_lectiva' => '2026-07-15'],
    ['fich_id' => 2871257, 'PROGRAMA_prog_id' => 228106, 'INSTRUCTOR_inst_id_lider' => 1008, 'fich_jornada' => 'Noche',   'COORDINACION_coord_id' => 4, 'fich_fecha_ini_lectiva' => '2025-06-01', 'fich_fecha_fin_lectiva' => '2027-01-01'],
    ['fich_id' => 2982378, 'PROGRAMA_prog_id' => 524232, 'INSTRUCTOR_inst_id_lider' => 1009, 'fich_jornada' => 'Mañana',  'COORDINACION_coord_id' => 5, 'fich_fecha_ini_lectiva' => '2025-03-01', 'fich_fecha_fin_lectiva' => '2026-06-01'],
    ['fich_id' => 2982379, 'PROGRAMA_prog_id' => 921310, 'INSTRUCTOR_inst_id_lider' => 1010, 'fich_jornada' => 'Virtual', 'COORDINACION_coord_id' => 5, 'fich_fecha_ini_lectiva' => '2025-04-15', 'fich_fecha_fin_lectiva' => '2025-10-15'],
    ['fich_id' => 3001001, 'PROGRAMA_prog_id' => 228185, 'INSTRUCTOR_inst_id_lider' => 1011, 'fich_jornada' => 'Tarde',   'COORDINACION_coord_id' => 1, 'fich_fecha_ini_lectiva' => '2025-07-01', 'fich_fecha_fin_lectiva' => '2027-01-01'],
    ['fich_id' => 3001002, 'PROGRAMA_prog_id' => 217303, 'INSTRUCTOR_inst_id_lider' => 1012, 'fich_jornada' => 'Mañana',  'COORDINACION_coord_id' => 2, 'fich_fecha_ini_lectiva' => '2025-08-01', 'fich_fecha_fin_lectiva' => '2027-02-01'],
];
$ins = 0;
foreach ($fichas as $row) {
    if (insertRow($pdo, 'ficha', $row)) $ins++;
}
progress('FICHA', count($fichas), $ins);

// 11. ASIGNACION
$asignaciones = [
    ['INSTRUCTOR_inst_id' => 1001, 'asig_fecha_ini' => '2025-02-01 07:00:00', 'asig_fecha_fin' => '2025-06-30 12:00:00', 'FICHA_fich_id' => 2547890, 'AMBIENTE_amb_id' => 'A101', 'COMPETENCIA_comp_id' => 1],
    ['INSTRUCTOR_inst_id' => 1002, 'asig_fecha_ini' => '2025-03-15 13:00:00', 'asig_fecha_fin' => '2025-07-15 18:00:00', 'FICHA_fich_id' => 2547891, 'AMBIENTE_amb_id' => 'A102', 'COMPETENCIA_comp_id' => 2],
    ['INSTRUCTOR_inst_id' => 1003, 'asig_fecha_ini' => '2025-01-20 07:00:00', 'asig_fecha_fin' => '2025-06-20 12:00:00', 'FICHA_fich_id' => 2659012, 'AMBIENTE_amb_id' => 'B201', 'COMPETENCIA_comp_id' => 1],
    ['INSTRUCTOR_inst_id' => 1004, 'asig_fecha_ini' => '2025-04-01 18:00:00', 'asig_fecha_fin' => '2025-09-01 22:00:00', 'FICHA_fich_id' => 2659013, 'AMBIENTE_amb_id' => 'B202', 'COMPETENCIA_comp_id' => 2],
    ['INSTRUCTOR_inst_id' => 1005, 'asig_fecha_ini' => '2025-02-10 07:00:00', 'asig_fecha_fin' => '2025-07-10 12:00:00', 'FICHA_fich_id' => 2760134, 'AMBIENTE_amb_id' => 'C301', 'COMPETENCIA_comp_id' => 3],
    ['INSTRUCTOR_inst_id' => 1006, 'asig_fecha_ini' => '2025-05-01 13:00:00', 'asig_fecha_fin' => '2025-10-01 18:00:00', 'FICHA_fich_id' => 2760135, 'AMBIENTE_amb_id' => 'C302', 'COMPETENCIA_comp_id' => 6],
    ['INSTRUCTOR_inst_id' => 1007, 'asig_fecha_ini' => '2025-01-15 07:00:00', 'asig_fecha_fin' => '2025-06-15 12:00:00', 'FICHA_fich_id' => 2871256, 'AMBIENTE_amb_id' => 'D401', 'COMPETENCIA_comp_id' => 7],
    ['INSTRUCTOR_inst_id' => 1008, 'asig_fecha_ini' => '2025-06-01 18:00:00', 'asig_fecha_fin' => '2025-12-01 22:00:00', 'FICHA_fich_id' => 2871257, 'AMBIENTE_amb_id' => 'D402', 'COMPETENCIA_comp_id' => 1],
    ['INSTRUCTOR_inst_id' => 1009, 'asig_fecha_ini' => '2025-03-01 07:00:00', 'asig_fecha_fin' => '2025-08-01 12:00:00', 'FICHA_fich_id' => 2982378, 'AMBIENTE_amb_id' => 'E501', 'COMPETENCIA_comp_id' => 5],
    ['INSTRUCTOR_inst_id' => 1010, 'asig_fecha_ini' => '2025-04-15 08:00:00', 'asig_fecha_fin' => '2025-08-15 17:00:00', 'FICHA_fich_id' => 2982379, 'AMBIENTE_amb_id' => 'E502', 'COMPETENCIA_comp_id' => 8],
    ['INSTRUCTOR_inst_id' => 1011, 'asig_fecha_ini' => '2025-07-01 13:00:00', 'asig_fecha_fin' => '2025-12-01 18:00:00', 'FICHA_fich_id' => 3001001, 'AMBIENTE_amb_id' => 'A103', 'COMPETENCIA_comp_id' => 4],
    ['INSTRUCTOR_inst_id' => 1012, 'asig_fecha_ini' => '2025-08-01 07:00:00', 'asig_fecha_fin' => '2026-01-01 12:00:00', 'FICHA_fich_id' => 3001002, 'AMBIENTE_amb_id' => 'B203', 'COMPETENCIA_comp_id' => 5],
    ['INSTRUCTOR_inst_id' => 1013, 'asig_fecha_ini' => '2025-02-01 07:00:00', 'asig_fecha_fin' => '2025-08-01 12:00:00', 'FICHA_fich_id' => 2547890, 'AMBIENTE_amb_id' => 'A101', 'COMPETENCIA_comp_id' => 3],
    ['INSTRUCTOR_inst_id' => 1014, 'asig_fecha_ini' => '2025-03-15 13:00:00', 'asig_fecha_fin' => '2025-09-15 18:00:00', 'FICHA_fich_id' => 2547891, 'AMBIENTE_amb_id' => 'A102', 'COMPETENCIA_comp_id' => 4],
    ['INSTRUCTOR_inst_id' => 1015, 'asig_fecha_ini' => '2025-01-20 07:00:00', 'asig_fecha_fin' => '2025-07-20 12:00:00', 'FICHA_fich_id' => 2659012, 'AMBIENTE_amb_id' => 'B201', 'COMPETENCIA_comp_id' => 4],
];
$ins = 0;
foreach ($asignaciones as $row) {
    if (insertRow($pdo, 'asignacion', $row)) $ins++;
}
progress('ASIGNACION', count($asignaciones), $ins);

// ─── Resumen ─────────────────────────────────────────────
echo "\n";
echo "  ╔══════════════════════════════════════════╗\n";
echo "  ║  " . green("✅  ¡Datos de prueba generados!") . "           ║\n";
echo "  ╚══════════════════════════════════════════╝\n";
echo "\n";
echo "  " . bold("Resumen:") . "\n";
echo "    • 5 Títulos de Programa\n";
echo "    • 8 Programas de Formación\n";
echo "    • 8 Competencias Transversales\n";
echo "    • 12 Relaciones Competencia↔Programa\n";
echo "    • 5 Centros de Formación\n";
echo "    • 12 Sedes\n";
echo "    • 15 Ambientes\n";
echo "    • 15 Instructores (contraseña: Sena2025)\n";
echo "    • 5 Coordinaciones (contraseña: Coord2025)\n";
echo "    • 12 Fichas\n";
echo "    • 15 Asignaciones\n";
echo "\n";
echo "  " . cyan("🌐 Abre: http://localhost/MVC/ProgramacionSena/views/dashboard/index.php") . "\n\n";
