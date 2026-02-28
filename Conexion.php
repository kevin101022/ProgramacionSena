<?php

class Conexion
{
    private static $instance = NULL;
    private static $driver = 'pgsql';

    private function __construct() {}

    public static function getConnect()
    {
        if (!isset(self::$instance)) {
            require_once __DIR__ . '/EnvLoader.php';
            EnvLoader::load(__DIR__ . '/.env');

            $pdo_options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

            $host = getenv('DB_HOST') ?: 'localhost';
            $db   = getenv('DB_NAME') ?: 'programacionesSena';
            $user = getenv('DB_USER') ?: 'postgres';
            $pass = getenv('DB_PASS') ?: '';
            $port = getenv('DB_PORT') ?: '5432';

            // Verificar driver de PostgreSQL
            if (!in_array('pgsql', PDO::getAvailableDrivers())) {
                throw new Exception("El driver 'pdo_pgsql' no está habilitado.");
            }

            // Driver para PostgreSQL
            $dsn = "pgsql:host=$host;port=$port;dbname=$db";
            try {
                self::$instance = new PDO($dsn, $user, $pass, $pdo_options);

                // Configurar variables de sesión para auditoría si hay sesión activa
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                $doc_usuario = isset($_SESSION['id']) ? $_SESSION['id'] : 0;
                $correo_usuario = isset($_SESSION['correo']) ? $_SESSION['correo'] : 'Sistema';

                $stmt = self::$instance->prepare("SELECT set_config('myapp.documento_usuario', :doc, false), set_config('myapp.correo_usuario', :correo, false)");
                $stmt->execute([':doc' => (string)$doc_usuario, ':correo' => $correo_usuario]);
            } catch (PDOException $e) {
                throw new Exception("Error al conectar a PostgreSQL: " . $e->getMessage());
            }
        }
        return self::$instance;
    }

    public static function getDriver()
    {
        return self::$driver;
    }
}
