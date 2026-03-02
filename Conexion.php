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
}
