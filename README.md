# 🎓 Guía de Configuración: SENA Académico (MVC)

Esta guía explica paso a paso cómo poner en marcha el proyecto, configurar la base de datos y entender la arquitectura de rutas y seguridad.

---

## 1. 📂 Estructura y Conceptos Clave

### 🛡️ Seguridad con `.env` y `EnvLoader.php`
- **¿Para qué sirve?**: En lugar de escribir tu contraseña dentro de los archivos de PHP (lo cual es inseguro), la guardamos en el archivo `.env`.
- **EnvLoader.php**: Es el encargado de leer ese archivo y "prestarle" los datos a la clase `Conexion.php`. Si mañana cambias de contraseña, **solo editas el .env**.

### 🛰️ Gestión de Rutas Maestras
- El proyecto usa la función `dirname(__DIR__)` y cadenas de `dirname`.
- **¿Por qué?**: Esto hace que las rutas sean **absolutas e inteligentes**. No importa si usas Laragon o XAMPP, el sistema siempre sabrá dónde están las carpetas `model`, `view` y `controller` sin perderse.

### � Front Controller (routing.php)
- **¿Qué es?**: Es el punto de entrada único de la aplicación.
- **¿Cómo funciona?**: En lugar de llamar a cada archivo por separado, todas las peticiones van a `routing.php`. Él se encarga de llamar al controlador y la acción correcta de forma segura usando **Reflexión de PHP**.

---

## 2. 🔌 Configuración del Servidor (Paso a Paso)

Elige tu servidor local:

### 🟢 Opción A: Laragon (Recomendado)
1. **Activar Extensiones**:
   - Click derecho en el botón de Laragon -> **PHP** -> **Extensiones**.
   - Asegúrate de que `pdo_pgsql` y `pgsql` tengan el check (para PostgreSQL).
   - O `pdo_mysql` y `mysqli` (para MySQL).
2. **Carpeta**: Coloca el proyecto en `C:\laragon\www\MVC`.

### 🟠 Opción B: XAMPP
1. **Activar Extensiones**:
   - Abre el **XAMPP Control Panel**.
   - En la fila de Apache, haz clic en **Config** -> **PHP (php.ini)**.
   - Busca (Ctrl + B) la línea `;extension=pdo_pgsql` y quítale el punto y coma `;` inicial. Haz lo mismo con `;extension=pgsql`.
   - **Guarda el archivo** y dale a **Stop** y luego **Start** en Apache.
2. **Carpeta**: Coloca el proyecto en `C:\xampp\htdocs\MVC`.

---

## 3. 🗄️ Configuración de la Base de Datos (.env)

Crea y abre en tu editor de código el archivo `.env` en la raíz y configura según tu motor:

### 🐘 Usando PostgreSQL
```env
DB_DRIVER=pgsql
DB_PORT=5432
DB_HOST=localhost
DB_NAME=transversal
DB_USER=postgres
DB_PASS=tu_contraseña_de_postgres
```

### 🐬 Usando MySQL
```env
DB_DRIVER=mysql
DB_PORT=3306
DB_HOST=localhost
DB_NAME=transversal
DB_USER=root
DB_PASS=          # En XAMPP suele estar vacío
```

---

## 5. 🔄 Configuración por Motor de Base de Datos

El proyecto es compatible con ambos motores. Aquí tienes el código de `Conexion.php` y la configuración de `.env` separada para cada uno.

---

### 🐬 Opción A: Configuración para MySQL

**1. Archivo `.env`:**
```env
DB_DRIVER=mysql
DB_PORT=3306
DB_HOST=localhost
DB_NAME=transversal
DB_USER=root
DB_PASS=          # Vacío en XAMPP/Laragon por defecto
```

**2.copia y pega este Código en `mvc_programa/Conexion.php`:**
```php
<?php

class Conexion
{
    private static $instance = NULL;

    private function __construct() {}

    public static function getConnect()
    {
        if (!isset(self::$instance)) {
            require_once __DIR__ . '/EnvLoader.php';
            EnvLoader::load(__DIR__ . '/.env');

            $pdo_options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

            $host = getenv('DB_HOST') ?: 'localhost';
            $db   = getenv('DB_NAME') ?: 'transversal';
            $user = getenv('DB_USER') ?: 'root';
            $pass = getenv('DB_PASS') ?: '';
            $port = getenv('DB_PORT') ?: '3306';

            // Driver para MySQL
            $dsn = "mysql:host=$host;port=$port;dbname=$db";
            
            try {
                self::$instance = new PDO($dsn, $user, $pass, $pdo_options);
            } catch (PDOException $e) {
                throw new Exception("Error al conectar a MySQL: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
```

---

### 🐘 Opción B: Configuración para PostgreSQL

**1. Archivo `.env`:**
```env
DB_DRIVER=pgsql
DB_PORT=5432
DB_HOST=localhost
DB_NAME=transversal
DB_USER=postgres
DB_PASS=tu_contraseña
```

**2.copia y pega este Código en `mvc_programa/Conexion.php`:**
```php
<?php

class Conexion
{
    private static $instance = NULL;

    private function __construct() {}

    public static function getConnect()
    {
        if (!isset(self::$instance)) {
            require_once __DIR__ . '/EnvLoader.php';
            EnvLoader::load(__DIR__ . '/.env');

            $pdo_options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

            $host = getenv('DB_HOST') ?: 'localhost';
            $db   = getenv('DB_NAME') ?: 'transversal';
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
            } catch (PDOException $e) {
                throw new Exception("Error al conectar a PostgreSQL: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
```



### Paso 4: Importar la base de datos
Recuerda importar tu archivo `.sql` en PHPMyAdmin o la herramienta que utilices para MySQL.

---

## 6. 🔍 Verificación (¿Cómo saber si todo está bien?)

1. Abre tu navegador y ve a: `http://localhost/MVC/mvc_programa/debug_db.php`.
2. El sistema te mostrará una lista verde:
   - ✅ Extensiones PHP cargadas.
   - ✅ Conexión establecida.
   - ✅ Tablas encontradas con su estructura.

---

# Insertar datos de prueba
c:\xampp\php\php.exe scripts/seed.php

# Limpiar TODO y volver a insertar
c:\xampp\php\php.exe scripts/seed.php --clean

# Solo limpiar (sin insertar)
c:\xampp\php\php.exe scripts/seed.php --clean-only

# Ver ayuda
c:\xampp\php\php.exe scripts/seed.php --help