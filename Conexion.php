<?php
class Conexion
{
    private static $instance = NULL;
    private static $driver = 'pgsql';
    public static function getConnect()
    {
        if (!isset(self::$instance)) {
            require_once __DIR__ . '/EnvLoader.php';
            EnvLoader::load(__DIR__ . '/.env');
            $host = getenv('DB_HOST') ?: 'localhost';
            $db   = getenv('DB_NAME') ?: 'programacionesSena';
            $user = getenv('DB_USER') ?: 'postgres';
            $pass = getenv('DB_PASS') ?: '';
            $port = getenv('DB_PORT') ?: '5432';
            $dsn = "pgsql:host=$host;port=$port;dbname=$db";
            self::$instance = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        }
        return self::$instance;
    }
    public static function getDriver()
    {
        return self::$driver;
    }

    /**
     * Inyecta variables de sesión en la conexión de la BD para que los triggers
     * puedan capturar quién está realizando la acción.
     */
    public static function setAuditVars($documento, $correo, $nombre = 'Sistema')
    {
        $db = self::getConnect();
        $driver = self::getDriver();

        try {
            if ($driver === 'pgsql') {
                // Para PostgreSQL
                $stmt = $db->prepare("SELECT set_config('myapp.documento_usuario', :doc, false), 
                                             set_config('myapp.correo_usuario', :correo, false),
                                             set_config('myapp.nombre_usuario', :nombre, false)");
                $stmt->execute([
                    ':doc' => (string)$documento,
                    ':correo' => (string)$correo,
                    ':nombre' => (string)$nombre
                ]);
            } else {
                // Para MySQL (si se llega a usar)
                $stmt = $db->prepare("SET @myapp_documento_usuario = :doc, @myapp_correo_usuario = :correo, @myapp_nombre_usuario = :nombre");
                $stmt->execute([':doc' => $documento, ':correo' => $correo, ':nombre' => $nombre]);
            }
        } catch (Exception $e) {
            error_log("Error al configurar variables de auditoría: " . $e->getMessage());
        }
    }
}
