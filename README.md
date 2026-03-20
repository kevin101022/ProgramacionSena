# 🎓 Guía de Configuración: SENA Académico (MVC)

Esta guía explica paso a paso cómo poner en marcha el proyecto, configurar la base de datos y entender la arquitectura de rutas y reglas de negocio.

## 1. Configuración y Conexión de Base de Datos

El pilar fundamental del sistema es su conexión segura y flexible. Utilizamos un sistema basado en variables de entorno para proteger tus credenciales.

### 🛡️ Seguridad con `.env`
No escribimos contraseñas dentro del código. Todo se configura en el archivo `.env` en la raíz del proyecto:
```env
DB_DRIVER=pgsql      # O 'mysql'
DB_PORT=5432         # 3306 para MySQL
DB_HOST=localhost    # Cambia esto si tu base de datos está en otro host
DB_NAME=nombre_de_tu_base_de_datos   # Cambia esto por el nombre de tu base de datos
DB_USER=postgres     # Cambia esto si tu usuario es diferente
DB_PASS=tu_contraseña  # Cambia esto por tu contraseña
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
- Solo puedes asignar a un instructor si está previamente **habilitado** para la Competencia específica (tabla `INSTRU_COMPETENCIA`).

#### 📌 Registro Simplificado de Competencias
Al registrar o editar un instructor desde el **Centro de Formación**, se muestra una lista plana de todas las competencias disponibles. El usuario simplemente marca con checkboxes cuáles competencias puede dictar ese instructor (puede ser **una o varias**). Ya no es necesario seleccionar los programas manualmente; el sistema se encarga internamente de vincular las competencias con los programas que correspondan.

#### 👀 Instructores por Competencia
Desde la vista de detalle de una **Competencia** (al hacer clic en ella desde la consulta), se puede ver la lista de todos los **instructores habilitados** para dictarla. Cada instructor muestra su nombre, correo y los programas en los que está vinculado con esa competencia.

#### 🔐 Permisos sobre Instructores y Habilitaciones
- **Centro de Formación:** Responsable exclusivo de registrar instructores y habilitarlos para sus respectivas competencias. Tiene control total: crear, editar y eliminar.
- **Coordinador:** Solo tiene **vistas de consulta**. Puede ver la ficha del instructor (sus competencias habilitadas) y la lista general de habilitaciones ("Instructor x Competencia"), pero **no puede registrar, editar ni eliminar** ninguna relación o instructor.

### ⏰ Control de Horarios (Nivel Franjas Horarias)
Al registrar una hora (ej: 7:00 AM a 10:00 AM), el sistema realiza un **Escaneo Global** y aplica estos bloqueos:

#### 🚫 Bloqueos Estrictos
1. **Cruce de Instructor:** El instructor no puede estar en dos lugares a la vez.
2. **Cruce de Ambiente:** El aula no puede ser ocupada por dos fichas al mismo tiempo.
3. **Cruce de Ficha:** La ficha no puede recibir dos clases simultáneamente.
4. **Coherencia Cronológica:** La hora de fin debe ser mayor a la de inicio.
5. **Jornada Institucional:** Solo se permite programar entre las **06:00 AM y 10:00 PM**.
6. **Fechas Vigentes:** No se puede iniciar una programación en una fecha que ya pasó.

#### 📍 Ubicación en el Código
| # | Restricción | Archivo | Método / Líneas |
|---|---|---|---|
| 1 | Cruce de Instructor | `model/DetalleAsignacionModel.php` | `checkGlobalConflicts()` — valida solapamiento de horas + fechas por instructor |
| 2 | Cruce de Ambiente | `model/DetalleAsignacionModel.php` | `checkGlobalConflicts()` — misma consulta, filtra por `AMBIENTE_amb_id` |
| 3 | Cruce de Ficha | `model/DetalleAsignacionModel.php` | `checkGlobalConflicts()` — misma consulta, filtra por `FICHA_fich_id` |
| 4 | Coherencia Cronológica | `controller/detalle_asignacionController.php` | `store()` y `update()` — `hora_ini >= hora_fin` retorna error 400 |
| 5 | Jornada Institucional | `controller/detalle_asignacionController.php` | `store()` y `update()` — verifica `< 06:00` o `> 22:00` |
| 6 | Fechas Vigentes | `controller/asignacionController.php` | `store()` y `update()` — `fecha_ini < date('Y-m-d')` retorna error 400 |
| 🎁 | Habilitación Instructor-Competencia | `controller/asignacionController.php` | `store()` y `update()` — `InstruCompetenciaModel::isQualified()` |

#### ⚖️ Límites y Regulaciones Académicas
Para asegurar el bienestar del instructor y la correcta ejecución del plan de estudios:
1. **Límite Mensual de Horas:** Un instructor no puede ser asignado por más de **160 horas en un mismo mes**. El sistema abortará la operación si se supera este tope garantizando el límite laboral.
2. **Horas Totales de Competencia:** No se pueden asignar más horas de las que estipula el programa original. Por ejemplo, si una competencia es de 48h, el sistema no permitirá programar 50h, evitando sobre-ejecución.
3. **Control del 80% (Umbral Mínimo):** Si se intenta registrar una distribución donde las horas cubren menos del 80% del total de la competencia, el sistema levanta una alerta requiriendo **Aceptación Directa del Coordinador** para proceder.
4. **Cálculo Automático de Fechas:** Las diferencias temporales entre Fecha de Inicio / Fin están fuertemente validadas con el volumen real de clases programadas en días de semana.
5. **Vencimiento de Certificación Técnica:** Se cruza la "Fecha de Vigencia" certificada del Instructor vs la "Fecha de Asignación"; bloqueando la programación si el instructor está expirado para dictar el módulo.

> **Nota:** Los cruces (1-3) se validan en **dos niveles**: a nivel de **Asignación** (`AsignacionModel::checkConflicts` — solapamiento de fechas) y a nivel de **Detalle/Franja Horaria** (`DetalleAsignacionModel::checkGlobalConflicts` — solapamiento de horas dentro de fechas solapadas). El bloqueo real que impide guardar ocurre en el nivel de Detalle.

#### ✅ Precisión Quirúrgica
- **Empalmes:** Se permite que una clase termine a las 9:00 y la siguiente empiece a las 9:00.
- **Alcance Global:** La validación revisa todas las asignaciones existentes, no solo la actual.

### 🏢 Control de Acceso y Estructura Organizacional
El sistema está modelado para respetar la estructura física de los centros SENA bajo un modelo de accesos estrictos:
1. **Desacoplamiento Funcionario-Dependencia (Coordinador vs Coordinación):** 
   A nivel de arquitectura de base de datos se manejan dos entidades independientes:
   - **`usuario_coordinador`:** Representa a la persona física (Nombre, Correo, Credenciales de acceso, Estado).
   - **`coordinacion`:** Representa a la dependencia o departamento administrativo.
   Esta separación permite que una dependencia cambie de líder sin perder su historial, y que un coordinador pueda ser "Desvinculado" (vacante) para posteriormente ser asignado a otra coordinación, o ser dado de baja (deshabilitado).
2. **Creación Controlada (Cero Auto-registros):** Se eliminó por completo el "Auto-registro" de coordinadores por razones de seguridad. **Unicamente el Centro de Formación** tiene el poder de:
   - Crear el perfíl del coordinador (Persona).
   - Crear el área de coordinación (Dependencia).
   - Asignar o Desvincular un coordinador a una dependencia.
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

---

## 6. 🔒 Auditoría y Refactorización de Base de Datos

El sistema cuenta con una arquitectura de base de datos optimizada y segura para la trazabilidad de la información:

### 🔑 Llaves Primarias Naturales
Para garantizar la inmutabilidad y coherencia de los datos, las tablas principales (`instructor` y `coordinacion`) utilizan el **Número de Documento** (BIGINT) como Llave Primaria (PK) en lugar de IDs autoincrementables. Todas las llaves foráneas en el sistema apuntan directamente a estos documentos.     

### 🗑️ Borrado Lógico (Soft Delete)
Para proteger el historial académico y las programaciones pasadas, está **estrictamente prohibido el borrado físico** de instructores y coordinadores. El sistema utiliza un esquema de *Borrado Lógico* mediante un campo `estado` (1 = Activo, 0 = Inactivo). Al "eliminar" un registro desde la interfaz, internamente solo se inactiva, preservando intacta la trazabilidad y métricas del sistema.

### 🕵️‍♂️ Sistema de Auditoría a Nivel de Motor
Se implementa un esquema de auditoría robusto y transparente a nivel de motor de base de datos (PostgreSQL), garantizando captura total:


- **Tabla Espejo (`auditoria_asignacion`):** Los cambios se respaldan automáticamente guardando tanto el estado anterior como la acción realizada.
- **Triggers (Disparadores):** Funciones nativas de PostgreSQL configuradas para activarse tras cualquier alteración (`INSERT`, `UPDATE`, `DELETE`).


- **Trazabilidad Pura:** Mediante inyección de variables de sesión (`myapp.documento_usuario` y `myapp.correo_usuario`) en la instancia de conexión PDO, el motor de BD rastrea con precisión inquebrantable qué usuario del sistema originó la modificación.

### 📊 Acceso desde la Interfaz por Rol
El sistema expone estos registros de auditoría de manera controlada:
1. **Centro de Formación:** Tiene acceso a la auditoría **completa de su centro**. Puede supervisar todas las acciones realizadas por cualquier coordinador vinculado a su sede.
2. **Coordinador:** Tiene acceso a su propio historial de **"Mis Acciones"**. Esto le permite verificar qué cambios ha realizado personalmente en las programaciones para mantener su propio control.

Esta vista de Auditoría se encuentra disponible directamente desde el menú lateral (Sidebar) en la sección de Gestión correspondiente.

---

## 7. 🛡️ Aislamiento de Datos por Centro y Vistas por Rol

El sistema implementa una estricta política de multitenencia (multi-tenant) lógica, donde la información se aísla dependiendo del **Centro de Formación** (`centro_id`) al que pertenece el usuario autenticado:

### 🏢 Aislamiento Global (SaaS Multi-Tenant)
Absolutamente todas las entidades principales están diseñadas bajo un esquema estricto Multi-Tenant lógico. Esto incluye: 
**Sedes, Instructores, Ambientes, Fichas, Coordinaciones, Asignaciones, Reportes, Auditoría**, y desde la última actualización arquitectónica, también los **Programas y Competencias**. 

Los datos se filtran en el backend usando el `centro_id` de la sesión del usuario autenticado. Esto significa que si un Centro registra un Programa (ej. "Análisis de Software"), otro centro en el país no podrá ver ni usar ese mismo registro; cada centro gestiona estrictamente sus propios catálogos como si fuera una base de datos exclusiva para ellos, impidiendo colisiones y garantizando la autonomía total.

### 🧭 Navegación Estricta por Rol (Sidebar)
La interfaz de navegación lateral (`sidebar.php`) presenta opciones puramente limitadas según el cargo validado tras el `Login`.
- **Centro de Formación:** Dispone de la gestión estructural de: **Sedes, Ambientes, Programas, Instructores, Competencias y Coordinaciones** (incluyendo la gestión de Títulos y el registro de Personas Coordinadoras).
- **Coordinador Académico:** Se encarga de la operatividad académica: **Competencia x Programa, Ficha, Instructor x Competencia y Asignación**. (Adicional: Auditoría de sus cambios).
- **Instructor:** Entorno confinado de solo lectura sobre sus recursos.

### 🧭 Navegación Robusta
Se utiliza un esquema de navegación basado en rutas relativas directas (`../modulo/index.php`) y carga de datos asíncrona (AJAX) para garantizar que los enlaces del menú lateral sean funcionales desde cualquier profundidad de la aplicación, evitando errores de enrutamiento 404.

### 🧑‍🏫 Vistas Especializadas del Instructor
El instructor cuenta con un apartado web dedicado llamado **"Mi Espacio"**:
- **Mis Asignaciones:** Visualización de sus clases programadas y espacios designados.
- **Mis Competencias:** Listado de los programas y normativas para los cuales el coordinador local lo ha habilitado para enseñar.
- **Mis Fichas (Líder):** Seguimiento detallado de las fichas específicas de formación donde este instructor ha sido designado como "líder" del programa tecnológico.

## 8. 📊 Sincronización y Dashboard SetData

El sistema incluye un motor de visualización de datos externos diseñado para transformar archivos crudos en tableros de control inteligentes.

### 📁 Procesamiento de Archivos "SetData"
- **Origen Flexible:** Acepta cualquier archivo CSV (exportado de FET, Excel o plataformas externas).
- **Detección Automática:** El motor identifica el delimitador (`,`, `;`, `\t`), los encabezados y la naturaleza de los datos (numéricos vs. texto).
- **Zero Persistence:** Por diseño, estos datos externos no se guardan en la base de datos SQL del proyecto. Se procesan "en vivo" para garantizar que el coordinador siempre visualice la versión más reciente del archivo cargado.

### 📈 Análisis Visual Automático
El dashboard genera dinámicamente:
1. **Tarjetas de Totales:** Suma automática de columnas numéricas (ej. Total Horas, Sesiones).
2. **Distribución de Frecuencia:** Gráficos de barras automáticos para cualquier categoría con menos de 30 valores únicos (ej. Horas por Instructor, por Ficha o por Competencia).
3. **Tabla de Exploración:** Vista previa filtrable con búsqueda instantánea para inspeccionar los datos fila por fila.

### 📄 Exportación de Reportes Premium
A diferencia de una simple exportación a texto, el sistema permite generar un **Reporte Visual en PDF**:
- **Fidelidad Total:** El PDF captura exactamente los gráficos y tarjetas que se ven en pantalla a través de renderizado por canvas.
- **Identidad Institucional:** Incluye encabezados del SENA y marcas de tiempo, convirtiendo los datos crudos en documentos profesionales listos para ser presentados.
- **Acceso:** Exclusivo para el rol **Coordinador Académico** desde el menú "Sincronizar Datos (CSV)".

---

## 9. 📅 Reportes de Calendarios con Generación de PDF

El sistema incluye un módulo completo de reportes visuales para consultar y exportar las programaciones académicas en formato calendario.

### 📊 Tipos de Reportes Disponibles
El módulo de reportes ofrece tres vistas especializadas de calendario:

1. **📅 Calendario de Ficha**
   - Visualiza todas las asignaciones programadas para una ficha específica
   - Muestra: Horarios, Competencias, Instructores y Ambientes asignados
   - Permite filtrar por número de ficha o programa

2. **👨‍🏫 Calendario de Instructor**
   - Consulta la agenda completa de un instructor
   - Muestra: Fichas asignadas, Competencias a dictar y Ambientes designados
   - Permite búsqueda por nombre o documento del instructor

3. **🏢 Calendario de Ambiente**
   - Visualiza la ocupación de un ambiente/aula específica
   - Muestra: Fichas programadas, Instructores asignados y Competencias
   - Permite búsqueda por ID o nombre del ambiente

### 🎨 Características de Visualización
- **Interfaz FullCalendar:** Calendario interactivo con vistas de mes, semana y lista
- **Código de Colores:** Cada asignación tiene un color distintivo para fácil identificación
- **Modal de Detalles:** Al hacer clic en un evento, se muestra un modal con información completa:
  - Fecha y horario exacto
  - Ficha, Competencia, Instructor y Ambiente
  - Diseño coherente con la identidad visual SENA

### 📄 Generación de PDF Profesional
Cada calendario incluye un botón de **"Descargar PDF"** que genera un reporte imprimible con las siguientes características:

#### Estructura del Documento
- **Encabezado Verde SENA:** Incluye título del reporte, información del elemento seleccionado y fecha de generación
- **Barra de Estadísticas:** Muestra el total de asignaciones programadas
- **Tabla de Agenda:** Formato de tabla profesional (no captura de pantalla del calendario)
- **Footer Informativo:** Texto institucional sobre el documento

#### Características Técnicas
- **Orientación Horizontal (Landscape):** Optimizada para visualizar todas las columnas sin cortes
- **Zebra Striping:** Filas alternadas en gris claro para mejor legibilidad
- **Agrupamiento Inteligente:** Las asignaciones consecutivas con el mismo horario se agrupan en un solo bloque (ej: "01/03/2026 - 05/03/2026")
- **Columnas Dinámicas:** Se ajustan según el tipo de reporte:
  - **Ficha:** Fecha/Rango, Hora Inicio, Hora Fin, Competencia, Ambiente, Instructor
  - **Instructor:** Fecha/Rango, Hora Inicio, Hora Fin, Ficha, Competencia, Ambiente
  - **Ambiente:** Fecha/Rango, Hora Inicio, Hora Fin, Ficha, Competencia, Instructor

#### Modos de Generación
El sistema soporta dos modos de generación de PDF:

1. **Modo Navegador (Predeterminado):**
   - Genera HTML optimizado con CSS `@page`
   - Se abre en nueva ventana con diálogo de impresión automático
   - El usuario guarda como PDF usando la función del navegador
   - ✅ No requiere instalación adicional
   - ✅ Compatible con todos los navegadores modernos

2. **Modo FPDF (Opcional):**
   - Si se instala la librería FPDF en `lib/fpdf/`, el sistema la detecta automáticamente
   - Genera PDF nativo con descarga directa
   - Mayor control sobre el formato y diseño
   - Instalación: Descargar de http://www.fpdf.org y extraer en `lib/fpdf/`

### 🔐 Control de Acceso
- **Coordinador Académico:** Acceso completo a todos los reportes de su centro
- **Centro de Formación:** Acceso completo a todos los reportes
- **Instructor:** Sin acceso (solo consulta sus propias asignaciones en "Mi Espacio")

### 📍 Ubicación en el Sistema
Los reportes están disponibles desde el menú lateral en la sección **"Reportes"**:
- Calendario de Ficha
- Calendario de Instructor
- Calendario de Ambiente

Cada reporte incluye:
- Buscador inteligente con autocompletado
- Visualización de calendario interactivo
- Estadísticas en tiempo real
- Botón de descarga de PDF

---🌍 1. Nivel Global (Todo el mundo en el sistema ve lo mismo)
Estas son bases de datos "catálogo" que nutren al sistema general sin importar de qué Centro de Formación se trate. Si un Centro crea o edita uno de estos, se reflejará para todos los demás Centros del país:

Sedes y Ambientes de Formación: Si un Centro registra la Sede ("Sede Principal") y un Ambiente ("Aula 201"), ese ambiente quedará disponible globalmente.
Competencias y Títulos de Programas (TITULOS_PROGRAMAS): El catálogo de competencias técnicas (ej. Inglés, Promover la Interacción) y los programas de formación general (ej. Tecnólogo en ADSO) son únicos del SENA nacional, por lo que todos los Centros ven el mismo listado a la hora de armar una ficha.
🔒 2. Nivel Centro de Formación (Datos Aislados)
Aquí es donde funciona la magia de tu sistema. Lo que crea un Centro solo lo puede ver y gestionar ese Centro de Formación y sus respectivos coordinadores:

Instructores: Los instructores que registra un Centro le pertenecen exclusivamente a ese Centro. Si otro Centro (ej. CASD) inicia sesión, jamás verá la lista de instructores, teléfonos o correos de tu Centro de Formación (CIES, por ejemplo).
Coordinaciones Académicas: Las coordinaciones que creas, así como a qué personas les diste el rol de "Coordinador", están amarradas a tu Centro.
Fichas de Formación (Las cohortes): Los números de Fichas (ej. Ficha 3115418) y sus fechas de inicio/fin son de total dominio y exclusividad del Centro que las crea.
Asignaciones (Clases y Horarios): Toda la programación y cruce de horarios que realizan los coordinadores entre instructores, fichas y ambientes es 100% invisible para cualquier otro Centro de Formación. Todo ocurre en su propio universo.
👤 3. Nivel Instructor (El punto más privado)
El Instructor es el último eslabón y tiene la vista más restringida y "limpia" de todo el sistema:

Su Calendario e Información: Un Instructor solo puede ver sus propias clases. No puede ver en qué está trabajando otro compañero instructor, ni qué otros instructores existen en el Centro, ni explorar fichas ajenas.
Restricción de Edición: Como hemos repasado hoy, el Instructor está configurado en un modo de "Solo Lectura". Puede ver toda su programación detallada, los ambientes y las fechas, pero no puede alterar horarios, ni crear, editar o eliminar asignaciones.
