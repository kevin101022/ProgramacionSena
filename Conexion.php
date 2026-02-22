<?php

class Conexion
{
    private static $instance = NULL;
    private static $driver = NULL;

    private function __construct() {}

    public static function getConnect()
    {
        if (!isset(self::$instance)) {
            require_once __DIR__ . '/EnvLoader.php';
            EnvLoader::load(__DIR__ . '/.env');

            $pdo_options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

            $driver = getenv('DB_CONNECTION') ?: 'mysql';
            $host   = getenv('DB_HOST') ?: 'localhost';
            $db     = getenv('DB_NAME') ?: 'transversal';
            $user   = getenv('DB_USER') ?: 'root';
            $pass   = getenv('DB_PASS') ?: '';
            $port   = getenv('DB_PORT') ?: ($driver === 'pgsql' ? '5432' : '3306');

            self::$driver = $driver;

            if ($driver === 'pgsql') {
                $dsn = "pgsql:host=$host;port=$port;dbname=$db";
            } else {
                $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8";
            }

            try {
                self::$instance = new PDO($dsn, $user, $pass, $pdo_options);
            } catch (PDOException $e) {
                throw new Exception("Error al conectar a la base de datos ($driver): " . $e->getMessage());
            }
        }
        return self::$instance;
    }

    public static function getDriver()
    {
        if (self::$driver === NULL) {
            self::getConnect();
        }
        return self::$driver;
    }
}