# 🎓 Guía de Configuración: SENA Académico (MVC)

Esta guía explica paso a paso cómo poner en marcha el proyecto, configurar la base de datos y entender la arquitectura de rutas y reglas de negocio.

## 1. Configuración y Conexión de Base de Datos

El pilar fundamental del sistema es su conexión segura y flexible. Utilizamos un sistema basado en variables de entorno para proteger tus credenciales.

### 🛡️ Seguridad con `.env`
No escribimos contraseñas dentro del código. Todo se configura en el archivo `.env` en la raíz del proyecto:
```env
DB_DRIVER=pgsql      # O 'mysql'
DB_PORT=5432         # 3306 para MySQL
DB_HOST=localhost
DB_NAME=programacionesSena
DB_USER=postgres
DB_PASS=tu_contraseña
```
Requisitos del Servidor

### En XAMPP
1. Abre **php.ini** (desde el panel de XAMPP).
2. Habilita las extensiones quitando el `;` inicial:
    para postgresql
   - `extension=pdo_pgsql`
   - `extension=pgsql`
    para mysql
   - `extension=pdo_mysql`
   - `extension=mysql`
3. Reinicia Apache.

### 🟢 En Laragon
1. Click derecho -> PHP -> Extensiones.
2. Asegúrate de marcar 
    para postgresql
   - `extension=pdo_pgsql`
   - `extension=pgsql`
    para mysql
   - `extension=pdo_mysql`
   - `extension=mysql`
3. Reinicia Apache.

### ⚙️ Clase de Conexión (`Conexion.php`)
El sistema usa el patrón **Singleton** para la base de datos. Esto significa que solo se abre una conexión por cada petición.

#### 🐬 Opción A: Configuración para MySQL
Si usas MySQL (XAMPP/Laragon por defecto), copia esto en `Conexion.php`:
```php
<?php
class Conexion {
    private static $instance = NULL;
    public static function getConnect() {
        if (!isset(self::$instance)) {
            require_once __DIR__ . '/EnvLoader.php';
            EnvLoader::load(__DIR__ . '/.env');
            $host = getenv('DB_HOST') ?: 'localhost';
            $db   = getenv('DB_NAME') ?: 'transversal';
            $user = getenv('DB_USER') ?: 'root';
            $pass = getenv('DB_PASS') ?: '';
            $port = getenv('DB_PORT') ?: '3306';
            $dsn = "mysql:host=$host;port=$port;dbname=$db";
            self::$instance = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        }
        return self::$instance;
    }
}
```

#### 🐘 Opción B: Configuración para PostgreSQL
Si usas PostgreSQL (Recomendado para este proyecto), copia esto en `Conexion.php`:
```php
<?php
class Conexion {
    private static $instance = NULL;
    private static $driver = 'pgsql'; // Necesario para el Seeder
    public static function getConnect() {
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
    public static function getDriver() { return self::$driver; }
}
```




## 2. 📋 Reglas de Negocio y Restricciones (El "Cerebro")

Este sistema no es solo una base de datos; tiene un motor de validaciones que garantiza que la programación académica sea coherente.

### 🛡️ Habilitación de Instructores
- Solo puedes asignar a un instructor si está previamente **habilitado** para la Competencia y el Programa específicos (tabla `INSTRU_COMPETENCIA`).

### ⏰ Control de Horarios (Nivel Franjas Horarias)
Al registrar una hora (ej: 7:00 AM a 10:00 AM), el sistema realiza un **Escaneo Global** y aplica estos bloqueos:

#### 🚫 Bloqueos Estrictos
1. **Cruce de Instructor:** El instructor no puede estar en dos lugares a la vez.
2. **Cruce de Ambiente:** El aula no puede ser ocupada por dos fichas al mismo tiempo.
3. **Cruce de Ficha:** La ficha no puede recibir dos clases simultáneamente.
4. **Coherencia Cronológica:** La hora de fin debe ser mayor a la de inicio.
5. **Jornada Institucional:** Solo se permite programar entre las **06:00 AM y 10:00 PM**.
6. **Fechas Vigentes:** No se puede iniciar una programación en una fecha que ya pasó.

#### ✅ Precisión Quirúrgica
- **Empalmes:** Se permite que una clase termine a las 9:00 y la siguiente empiece a las 9:00.
- **Alcance Global:** La validación revisa todas las asignaciones existentes, no solo la actual.

### 🏢 Control de Acceso y Estructura Organizacional
El sistema está modelado para respetar la estructura física de los centros SENA bajo un modelo de accesos estrictos:
1. **Coordinaciones por Defecto:** Al inicializar la base de datos, el sistema se despliega con **4 coordinaciones fijas** pre-ancladas a sus respectivos *Centros de Formación* (Industria y Comercio, Industria, Comercio, y Moda/Turismo/Tecnología).
2. **Auto-registro Inteligente (Coordinadores):** Un coordinador no digita su centro de formación. Selecciona una de las coordinaciones vacantes de la base de datos, heredando **automáticamente** el perfil de su `Centro de Formación` asociado. Esto evita inconsistencias de tipado y relaciona directamente al usuario con su contexto.
3. **Visibilidad Restringida (Aislamiento de Información):** Al ingresar al sistema, a los Coordinadores **solo se les listan los Instructores que pertenezcan a su mismo Centro de Formación**, ocultando por diseño toda la red de instructores de otras sedes del país.
4. **Vistas Limitadas para Instructores (RBAC):** El sistema cuenta con Control de Acceso Basado en Roles. Si un Instructor inicia sesión, es redirigido a un entorno de **solo lectura** ("Mi Espacio"). Solo pueden visualizar las Asignaciones y Competencias que les corresponden. Cualquier intento de un Instructor por acceder a vistas de Coordinador o alterar datos a través del enrutador (`routing.php`) es bloqueado automáticamente devolviendo un error HTTP 403 (Acceso Denegado).

---

## 3. Estructura y Conceptos Técnicos

###  Gestión de Rutas Maestras
Usamos rutas absolutas calculadas con `dirname(__DIR__)`. Esto garantiza que el sistema funcione en cualquier servidor sin importar el nombre de la carpeta donde lo instales.

###  Front Controller (`routing.php`)
Es el punto de entrada único. Todas las peticiones pasan por aquí y son distribuidas a sus respectivos controladores usando **Reflexión de PHP**, lo que hace que el sistema sea modular y fácil de escalar.

---

## 4.  Requisitos del Servidor

### � En XAMPP
1. Abre **php.ini** (desde el panel de XAMPP).
2. Habilita las extensiones quitando el `;` inicial:
   - `extension=pdo_pgsql`
   - `extension=pgsql`
3. Reinicia Apache.

### 🟢 En Laragon
1. Click derecho -> PHP -> Extensiones.
2. Asegúrate de marcar `pdo_pgsql` y `pgsql`.

---

## 5. 🔍 Verificación y Datos de Prueba

### Diagnóstico rápido
Accede a `http://localhost/MVC/ProgramacionSena/debug_db.php` para verificar:
- ✅ Extensiones PHP activas.
- ✅ Conexión exitosa a la DB.
- ✅ Existencia de tablas requeridas.

### Sembrado de Datos (Seeding)
Si necesitas llenar el sistema con datos de prueba, usa la terminal en la raíz:
```bash
# Limpiar todo e insertar datos nuevos
c:\xampp\php\php.exe scripts/seed.php --clean

# Solo ver la ayuda
c:\xampp\php\php.exe scripts/seed.php --help
```