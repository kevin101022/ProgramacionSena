# 📘 Documentación Arquitectónica: Modelo de Coordinadores

Este documento explica a detalle la refactorización profunda aplicada al módulo de Coordinación Académica dentro del sistema **SENA Académico (MVC)**. 

El modelo anterior presentaba riesgos de seguridad (auto-registros), acoplamiento de datos (la persona y la dependencia eran la misma entidad en la base de datos) y pérdida de historial cuando un coordinador abandonaba su cargo. Todo esto fue rediseñado bajo principios de normalización estricta.

---

## 1. El Problema: Acoplamiento de Datos (Antes)
Anteriormente, la tabla `coordinacion` guardaba revueltos los datos del departamento administrativo (Ej: "Coordinación Académica V") junto con los datos personales del coordinador a cargo (Ej: "Juan Pérez, Correo, Clave").

**Consecuencias Atacadas:**
- 🚫 Si "Juan Pérez" se iba de la institución, al intentar borrar sus datos visuales, se borraba *toda el Área de Coordinación*.
- 🚫 Cientos de **Fichas** que dependían de esa Coordinación quedaban huérfanas o perdían su integridad de datos.
- 🚫 Se requería que el propio humano entrara al sistema en la ventana de login a "Registrarse a sí mismo" (Autoregistro), haciéndose dueño absoluto de bases de datos que en realidad pertenecen al Centro de Formación.

---

## 2. La Solución: Desacoplamiento (Ahora)
Decidimos operar como en la vida real: **Las oficinas existen, y las personas las ocupan.** 
Para ello, dividimos el modelo en dos tablas de base de datos independientes, relacionadas entre sí:

### A) Entidad Administrativa: `coordinacion` (La Oficina)
Esta tabla representa puramente a la dependencia formal del SENA.
* **Llave Primaria:** `coord_id` (Autoincremental en backend/base de datos).
* **Guarda:** El nombre del área (Ej: "Coordinación de Teleinformática") y su centro de formación contenedor (`centro_formacion_id`).
* **Relación Clave:** Tiene un campo foráneo opcional llamado `coordinador_actual`. Si está vacío (`NULL`), significa que la ofcina está "Vacante".

### B) Entidad Humana: `usuario_coordinador` (El Funcionario)
Esta tabla engloba estrictamente los datos del trabajador.
* **Llave Primaria Natural:** `numero_documento` (DNI/Cédula).
* **Guarda:** El nombre real del trabajador, su correo, su hash de contraseña y su *estado*.
* **Relación Clave:** No le importa de qué oficina es dueño. Su única misión es identificarse biométricamente y guardar su historial de acceso, perfiles e interacciones orgánicas.

---

## 3. Fin del Autorregistro: El Administrador (Centro) toma el Control
Para maximizar la seguridad perimetral del software, **el portal abierto de registro de coordinadores en la ventana de Login se cerró totalmente.** 

Ahora, el flujo de vida del coordinador opera bajo un entorno controlado de panel cerrado:
1. El Administrador o Subdirector (Rol: `Centro de Formación`) ingresa al sistema.
2. Abre la pestaña **Coordinadores (Persona)**.
3. El Centro registra la cédula, el nombre y el correo del trabajador, otorgándole acceso institucional real al sistema.
4. Luego, el Centro va a **Área de Coordinaciones** y lo asigna a dirigir una dependencia específica.

---

## 4. Nuevo Flujo Funcional y Privacidad en la Interfaz (UI/UX)

La separación arquitectónica introdujo herramientas avanzadas para la gestión de novedades del talento humano en los Centros:

### 🧩 Asignación Dinámica
Cuando un administrador va a editar o crear una *Coordinación*, el formulario mostrará un menú desplegable (Select) donde podrá elegir **únicamente a los Coordinadores que estén Habilitados y que no estén dirigiendo otra dependencia**. Un trabajador no puede ser clonado y enviado a gobernar dos sedes a la vez de forma estricta.

### 🔗 "Desvincular" vs "Eliminar"
Si el Funcionario actual es removido del cargo o trasladado, el Administrador usa una función revolucionaria del sistema: **Desvincular**.
Al desvincular un coordinador del área:
* El área retiene todo su organigrama o jerarquía histórica de *Fichas* y *Asignaciones* a la luz de los Reportes (¡Los Reportes no se borran!).
* La Coordinación pasa a un estado en vivo de "Vacante", lista para recibir a un nuevo Funcionario.
* Las credenciales del viejo funcionario siguen vigentes si así se desea, permitiéndole ser transferido a **otra** dependencia u oficina en la misma ciudad sin tener que crear una cuenta desde cero ni migrar las tablas de la base de datos subyacente.

### 🔌 Habilitación / Deshabilitación de Personas (Soft Disable)
¿Qué pasa si el Funcionario cometió desfalco o renunció al SENA?
Al hacer clic en el botón rojo de **Deshabilitar** (Suspendido inhabilitado):
* Su estado de base de datos (`estado`) pasa de **1 a 0**.
* El sistema bloquea irrevocablemente todo intento de acceso (Login) asociado a ese documento, repeliéndolo la próxima vez que teclee la contraseña.
* Sus registros en el pasado como instructor o coordinador **jamás** desaparecen visualmente de la historia por reglas de **Auditoría de Actividad e Imparcialidad**. Lo que se hizo, escrito allí quedará para reportabilidad general. El registro de base de datos está desactivado mas nunca se hace un comando destructivo (`DELETE`) en todo su rastro digital.

---

## 5. Resumen de Ventajas Transversales
✅ El número de Cédula/Documento es el validador final de identidad.  
✅ Se eliminaron fugas de permisos o personas fingiendo ser coordinaciones (ataques fantasma).  
✅ Escalabilidad: Mañana se puede crear una tabla "Gestores", "Apoyo" y vincularlos a la oficina sin modificar otra celda de la Base de Datos general.  
✅ Auditoría real y no destructiva adaptada a ambientes del Estado (Colombia) donde se prefiere dejar rastro histórico.
