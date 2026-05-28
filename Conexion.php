<?php
class Conexion
{
    private static $instance = NULL;

    public static function getConnect()
    {
        if (!isset(self::$instance)) {
            // Asegurar la carga del cargador de entorno
            if (!class_exists('EnvLoader')) {
                require_once __DIR__ . '/EnvLoader.php';
            }
            EnvLoader::load(__DIR__ . '/.env');

            // Configuración optimizada para Localhost (XAMPP)
            $host = getenv('DB_HOST') ?: 'localhost'; 
            $db   = getenv('DB_NAME') ?: 'programaciones'; // Base de datos local
            $user = getenv('DB_USER') ?: 'root';          // Usuario por defecto en XAMPP
            $pass = getenv('DB_PASS') ?: '';              // Sin contraseña por defecto en XAMPP
            $port = getenv('DB_PORT') ?: '3306';          // Puerto nativo de MySQL

            // DSN corregido para usar el driver de MySQL
            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

            // Opciones de configuración para un PDO seguro y eficiente
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false
            ];

            self::$instance = new PDO($dsn, $user, $pass, $options);
        }
        return self::$instance;
    }

    /**
     * Inyecta variables de sesión en la conexión de MySQL para que los triggers
     * puedan capturar quién está realizando la acción.
     */
    public static function setAuditVars($documento, $correo, $nombre = 'Sistema')
    {
        $db = self::getConnect();
        try {
            // Sintaxis nativa de variables de sesión para MySQL (@myapp_...)
            $stmt = $db->prepare("SET @myapp_documento_usuario = :doc, @myapp_correo_usuario = :correo, @myapp_nombre_usuario = :nombre");
            $stmt->execute([
                ':doc'    => (string) $documento,
                ':correo' => (string) $correo,
                ':nombre' => (string) $nombre
            ]);
        } catch (Exception $e) {
            error_log("Error al configurar variables de auditoría: " . $e->getMessage());
        }
    }
}