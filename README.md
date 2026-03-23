# 🎓 Guía de Configuración: SENA Académico (MVC)

Esta guía explica paso a paso cómo poner en marcha el proyecto, configurar la base de datos MySQL y entender la arquitectura.

---

## 1. Configuración y Conexión (MySQL)

### 🛡️ Archivo `.env`
Configura el archivo `.env` en la raíz del proyecto (**sin comentarios inline**):
```env
DB_DRIVER=mysql
DB_PORT=3306
DB_HOST=localhost
DB_NAME=nombre_base_datos
DB_USER=root
DB_PASS=
```

> ⚠️ **No agregues comentarios** en la misma línea que los valores (ej: `DB_USER=root # esto falla`).

### 📎 Requisitos: XAMPP
1. Abre `C:\xampp\php\php.ini`
2. Busca y habilita (quitar el `;`):
   ```
   -----------------------
   extension=pdo_mysql
   -----------------------
   ```
3. Reinicia Apache desde el panel de XAMPP.

### 🟢 Requisitos: Laragon
1. Click derecho → PHP → Extensiones → marcar `pdo_mysql`.
2. Reiniciar.

### ⚙️ Clase de Conexión (`Conexion.php`)
El sistema usa patrón **Singleton** con MySQL:
```php
-----------------------------------------
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
            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
            self::$instance = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        }
        return self::$instance;
    }

    public static function setAuditVars($documento, $correo, $nombre = 'Sistema') {
        $db = self::getConnect();
        $stmt = $db->prepare("SET @myapp_documento_usuario = :doc, @myapp_correo_usuario = :correo, @myapp_nombre_usuario = :nombre");
        $stmt->execute([':doc' => (string)$documento, ':correo' => (string)$correo, ':nombre' => (string)$nombre]);
    }
}
-----------------------------------------
```

---

## 2. 🔍 Datos de Prueba

Para poblar la base de datos MySQL con datos de ejemplo:
```bash
php insertar_datos_mysql.php
```

Esto crea: 2 centros, 5 sedes, 27 ambientes, 10 programas, 6 coordinadores, 15 instructores, 19 competencias, 10 proyectos formativos, 8 fichas, 6 asignaciones con horarios.

🔑 **Contraseña para TODOS los usuarios:** `password` (encriptada con bcrypt)

---

## 3. 📋 Reglas de Negocio

### 🛡️ Habilitación de Instructores
- Solo puedes asignar a un instructor si está previamente **habilitado** para la Competencia específica (tabla `INSTRU_COMPETENCIA`).
- Al registrar un instructor desde el **Centro de Formación**, se marcan con checkboxes las competencias que puede dictar.

### ⏰ Control de Horarios
| # | Bloqueo | Descripción |
|---|---------|-------------|
| 1 | Cruce de Instructor | No puede estar en dos lugares a la vez |
| 2 | Cruce de Ambiente | El aula no puede ser doble-reservada |
| 3 | Cruce de Ficha | La ficha no puede recibir dos clases simultáneas |
| 4 | Coherencia Cronológica | Hora fin > hora inicio |
| 5 | Jornada Institucional | Solo 06:00 AM – 10:00 PM |
| 6 | Fechas Vigentes | No se puede programar en fechas pasadas |

### ⚖️ Límites Académicos
- **160 horas/mes** máximo por instructor
- **Horas de competencia** no pueden exceder lo que estipula el programa
- **Umbral 80%:** Si la distribución cubre < 80%, requiere aprobación del coordinador
- **Certificación vigente:** Se bloquea si el instructor tiene certificación expirada

---

## 4. 🏢 Control de Acceso (RBAC)

### Roles
| Rol | Puede hacer |
|-----|-------------|
| **Centro de Formación** | Gestión total: Sedes, Ambientes, Programas, Instructores, Competencias, Coordinaciones |
| **Coordinador** | Operatividad: Fichas, Asignaciones, Horarios, Auditoría propia |
| **Instructor** | Solo lectura: Mis Asignaciones, Mis Competencias, Mis Fichas |

### Aislamiento Multi-Tenant
Cada Centro de Formación solo ve sus propios datos: instructores, fichas, coordinaciones, asignaciones.

---

## 5. 🔒 Auditoría

- **Tabla Espejo:** `auditoria_asignacion` registra INSERT, UPDATE y DELETE automáticamente.
- **Triggers MySQL:** Se activan tras cualquier alteración de asignaciones.
- **Trazabilidad:** Variables de sesión MySQL (`@myapp_documento_usuario`, `@myapp_correo_usuario`) rastrean qué usuario realizó cada cambio.
- **Centro:** Ve auditoría completa de su centro.
- **Coordinador:** Ve solo sus propias acciones.

---

## 6. 📊 Módulos Adicionales

### SetData (CSV)
- Acepta archivos CSV para generar dashboards automáticos con totales y distribuciones.
- Generación de PDF con identidad SENA.
- Exclusivo para Coordinadores.

### Reportes de Calendario
- **Calendario de Ficha:** Asignaciones de una ficha específica.
- **Calendario de Instructor:** Agenda completa de un instructor.
- **Calendario de Ambiente:** Ocupación de un aula.
- Generación de PDF horizontal con agrupamiento inteligente de fechas.

### Proyectos Formativos
- Gestión de proyectos por programa con fases, actividades y RAPs.
- Acordeón interactivo para ver RAPs por fase.

---

## 7. Arquitectura Técnica

- **Front Controller:** `routing.php` — punto de entrada único usando Reflexión PHP.
- **Rutas Absolutas:** `dirname(__DIR__)` para portabilidad.
- **Llaves Naturales:** Número de documento como PK en instructores y coordinadores.
- **Borrado Lógico:** Campo `estado` (1=activo, 0=inactivo) en vez de DELETE físico.
