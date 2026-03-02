<?php

// Carga de variables de entorno para que Conexion.php funcione
require_once __DIR__ . '/EnvLoader.php';

// Prevenir salida de errores HTML que rompan el JSON
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Gestor de errores personalizado para capturar Warnings/Notices en el log
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    $error_msg = [
        'type' => 'PHP Error/Warning',
        'level' => $errno,
        'message' => $errstr,
        'file' => $errfile,
        'line' => $errline,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    file_put_contents(__DIR__ . '/debug_error.log', "[" . date('Y-m-d H:i:s') . "] " . json_encode($error_msg, JSON_PRETTY_PRINT) . "\n", FILE_APPEND);
    return false; // Seguir con el flujo normal
});

$controllers = array(
    'sede' => ['index', 'show', 'store', 'update', 'destroy', 'getProgramas', 'getFichas'],
    'coordinacion' => ['index', 'show', 'store', 'update', 'destroy', 'getProgramas', 'get_coordinadores_disponibles', 'desvincular'],
    'asignacion' => ['index', 'show', 'store', 'update', 'destroy'],
    'detalle_asignacion' => ['index', 'show', 'store', 'update', 'destroy'],
    'ambiente' => ['index', 'show', 'store', 'update', 'destroy', 'getProgramacion'],
    'programa' => ['index', 'show', 'store', 'update', 'destroy', 'getTitulos'],
    'ficha' => ['index', 'show', 'store', 'update', 'destroy'],
    'competencia' => ['index', 'show', 'store', 'update', 'destroy'],
    'competencia_programa' => ['index', 'sync', 'getByPrograma', 'getAllPairs'],
    'titulo_programa' => ['index', 'show', 'store', 'update', 'destroy'],
    'centro_formacion' => ['index', 'show', 'store', 'update', 'destroy', 'getInstructores', 'getCoordinaciones'],
    'instructor' => ['index', 'show', 'showByCentro', 'store', 'update', 'destroy', 'getCentros', 'getAsignaciones', 'getCompetencias'],
    'instru_competencia' => ['index', 'show', 'store', 'update', 'destroy'],
    'reporte' => ['instructoresPorCentro', 'fichasActivasPorPrograma', 'asignacionesPorInstructor', 'competenciasPorPrograma'],
    'login' => ['showLogin', 'login', 'logout', 'registroCoordinador', 'guardarCoordinador', 'getCoordinacionesByCentro'],
    'auditoria_asignacion' => ['index', 'show'],
    'usuario_coordinador' => ['index', 'show', 'store', 'update', 'toggle'],
    'setdata' => ['index', 'upload'],
    // Agrega más controladores y acciones aquí si lo necesitas
);

try {
    // Obtener controlador y acción buscando en GET y POST
    $controller = $_GET['controller'] ?? $_POST['controller'] ?? 'sede';
    $action = $_GET['action'] ?? $_POST['action'] ?? 'index';

    // Validación básica de existencia del controlador y acción
    if (!array_key_exists($controller, $controllers) || !in_array($action, $controllers[$controller])) {
        $controller = 'sede';
        $action = 'index';
    }

    if ($controller === 'usuario_coordinador') {
        $controllerFile = 'controller/usuarioCoordinadorController.php';
    } elseif ($controller === 'auditoria_asignacion') {
        $controllerFile = 'controller/auditoria_asignacionController.php';
    } else {
        $controllerFile = 'controller/' . $controller . 'Controller.php';
    }

    // Verificar que el archivo del controlador existe
    if (!file_exists($controllerFile)) {
        throw new Exception("Controlador no encontrado: $controller");
    }

    // --- ROLE-BASED ACCESS CONTROL (RBAC) ---
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Si no es el controlador de login, verificamos permisos
    if ($controller !== 'login') {
        if (!isset($_SESSION['rol'])) {
            http_response_code(401);
            throw new Exception("No autorizado. Por favor, inicie sesión.");
        }

        $rol = $_SESSION['rol'];
        $isAjax = true; // routing.php maneja backend calls principalmente

        $allowedControllersByRole = [
            'centro' => ['sede', 'ambiente', 'programa', 'titulo_programa', 'instructor', 'competencia', 'competencia_programa', 'coordinacion', 'usuario_coordinador', 'reporte', 'centro_formacion', 'auditoria_asignacion'],
            'coordinador' => ['competencia_programa', 'ficha', 'instru_competencia', 'asignacion', 'detalle_asignacion', 'reporte', 'auditoria_asignacion', 'coordinacion', 'setdata'],
            'instructor' => ['asignacion', 'instructor']
        ];

        // Reglas de lectura adicionales para roles cruzados (Usado por Dashboard y Dropdowns)
        if ($rol === 'coordinador' && in_array($controller, ['programa', 'instructor', 'ambiente', 'competencia', 'centro_formacion', 'sede']) && in_array($action, ['index', 'show'])) {
            // Permitido para cargar selects, stats en dashboard y consultas de solo lectura
        } else if ($rol === 'coordinador' && in_array($controller, ['programa', 'instructor', 'ambiente', 'centro_formacion'])) {
            // Permitido para cargar acciones misceláneas de selects
        } else if ($rol === 'coordinador' && $controller === 'competencia' && !in_array($action, ['index', 'show'])) {
            http_response_code(403);
            throw new Exception("Acceso denegado: Los coordinadores solo pueden consultar competencias, no modificarlas.");
        } else if ($rol === 'centro' && in_array($controller, ['ficha', 'asignacion']) && $action === 'index') {
            // Permitido a centro para cargar estadísticas del dashboard
        } else if (!in_array($controller, $allowedControllersByRole[$rol] ?? [])) {
            http_response_code(403);
            throw new Exception("Acceso denegado: El rol '$rol' no tiene permisos para el módulo '$controller'.");
        }
    }
    // --- END RBAC ---

    require_once($controllerFile);

    switch ($controller) {
        case 'sede':
            require_once('model/SedeModel.php');
            $controllerObj = new SedeController();
            break;
        case 'coordinacion':
            require_once('model/CoordinacionModel.php');
            $controllerObj = new CoordinacionController();
            break;
        case 'usuario_coordinador':
            require_once('model/UsuarioCoordinadorModel.php');
            require_once('controller/usuarioCoordinadorController.php');
            $controllerObj = new usuarioCoordinadorController();
            break;
        case 'asignacion':
            require_once('model/AsignacionModel.php');
            $controllerObj = new AsignacionController();
            break;
        case 'detalle_asignacion':
            require_once('model/DetalleAsignacionModel.php');
            $controllerObj = new DetalleAsignacionController();
            break;
        case 'ambiente':
            require_once('model/AmbienteModel.php');
            require_once('model/SedeModel.php');
            $controllerObj = new AmbienteController();
            break;
        case 'programa':
            require_once('model/ProgramaModel.php');
            require_once('model/TituloProgramaModel.php');
            $controllerObj = new ProgramaController();
            break;
        case 'ficha':
            require_once('model/FichaModel.php');
            $controllerObj = new FichaController();
            break;
        case 'titulo_programa':
            require_once('model/TituloProgramaModel.php');
            $controllerObj = new TituloProgramaController();
            break;
        case 'centro_formacion':
            require_once('model/CentroFormacionModel.php');
            $controllerObj = new CentroFormacionController();
            break;
        case 'competencia':
            require_once('model/CompetenciaModel.php');
            $controllerObj = new CompetenciaController();
            break;
        case 'competencia_programa':
            require_once('model/CompetenciaProgramaModel.php');
            $controllerObj = new CompetenciaProgramaController();
            break;
        case 'instructor':
            require_once('model/InstructorModel.php');
            require_once('model/CentroFormacionModel.php');
            $controllerObj = new InstructorController();
            break;
        case 'instru_competencia':
            require_once('model/InstruCompetenciaModel.php');
            $controllerObj = new InstruCompetenciaController();
            break;
        case 'reporte':
            $controllerObj = new ReporteController();
            break;
        case 'login':
            require_once('model/LoginModel.php');
            $controllerObj = new LoginController();
            break;
        case 'auditoria_asignacion':
            require_once('model/AuditoriaAsignacionModel.php');
            $controllerObj = new AuditoriaAsignacionController();
            break;
        case 'setdata':
            require_once('model/SetdataModel.php');
            $controllerObj = new SetdataController();
            break;
        default:
            throw new Exception("Controlador no soportado: $controller");
    }

    // Verificar que el método existe
    if (!method_exists($controllerObj, $action)) {
        throw new Exception("Acción no encontrada: $action en $controller");
    }

    $reflection = new ReflectionMethod($controllerObj, $action);
    $args = [];

    // Recolecta los parámetros esperados desde $_POST o $_GET en el orden correcto
    foreach ($reflection->getParameters() as $param) {
        $paramName = $param->getName();
        if (isset($_POST[$paramName])) {
            $args[] = $_POST[$paramName];
        } elseif (isset($_GET[$paramName])) {
            $args[] = $_GET[$paramName];
        } elseif ($param->isDefaultValueAvailable()) {
            $args[] = $param->getDefaultValue();
        } elseif (!$param->isOptional()) {
            http_response_code(400);
            throw new Exception("Falta el parámetro requerido: $paramName");
        }
    }

    // Llama al método con los argumentos necesarios
    $controllerObj->{$action}(...$args);
} catch (Throwable $e) {
    // Limpiar cualquier salida previa (warnings, etc.) para asegurar un JSON válido
    if (ob_get_length()) ob_clean();

    http_response_code(500);

    // Asumir JSON si es una petición que no sea de navegación directa (o si pide JSON)
    $isJsonRequest = (
        isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
        (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
        isset($_GET['controller']) || isset($_POST['controller']) // Casi cualquier petición a routing es AJAX en este sistema
    );

    $error_msg = [
        'error' => 'Error interno en el servidor',
        'details' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];

    // Siempre loggear
    file_put_contents(__DIR__ . '/debug_error.log', "[" . date('Y-m-d H:i:s') . "] " . json_encode($error_msg, JSON_PRETTY_PRINT) . "\n", FILE_APPEND);

    if ($isJsonRequest) {
        header('Content-Type: application/json');
        echo json_encode($error_msg);
    } else {
        echo "<h1>Error 500: " . $e->getMessage() . "</h1>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
        echo "<p>En el archivo: " . $e->getFile() . " en la línea " . $e->getLine() . "</p>";
    }
}
