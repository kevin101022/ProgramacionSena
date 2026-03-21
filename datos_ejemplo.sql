-- SCRIPT DE POBLACIÓN MASIVA DE DATOS - VERSIÓN PROFESIONAL (POSTGRESQL)
-- Incluye flujo completo para 10 Programas, 10 Proyectos, 15 Instructores y +20 Fichas.

-- ============================================================
-- 1. LIMPIEZA Y REINICIO DE IDENTIDADES
-- ============================================================
TRUNCATE TABLE 
    public.rap_actividad,
    public.rap_fase,
    public.actividad_proyecto,
    public.fase_proyecto,
    public.proyecto_formativo,
    public.detallexasignacion,
    public.asignacion,
    public.auditoria_asignacion,
    public.instru_competencia,
    public.ficha,
    public.instructor,
    public.coordinacion,
    public.usuario_coordinador,
    public.resultado_aprendizaje,
    public.competencia_horas_programa,
    public.competencia,
    public.programa,
    public.titulo_programa,
    public.ambiente,
    public.sede,
    public.centro_formacion
RESTART IDENTITY CASCADE;

-- ============================================================
-- 2. CENTROS DE FORMACIÓN (Bcrypt: password)
-- ============================================================
INSERT INTO public.centro_formacion (cent_id, cent_nombre, cent_correo, cent_password) VALUES 
(1, 'CIES - Centro de Industria, Empresa y Servicios',   'cies@sena.edu.co',   'password'),
(2, 'CEDRUM - Centro de Desarrollo Agroempresarial',     'cedrum@sena.edu.co', 'password');

-- ============================================================
-- 3. SEDES (Norte de Santander)
-- ============================================================
INSERT INTO public.sede (sede_id, sede_nombre, centro_formacion_cent_id) VALUES 
(1, 'Cúcuta - Sede Principal', 1),
(2, 'Ocaña', 1),
(3, 'Pamplona', 2),
(4, 'Villa del Rosario', 2),
(5, 'Los Patios', 1);

-- ============================================================
-- 4. AMBIENTES (27 Ambientes)
-- ============================================================
INSERT INTO public.ambiente (amb_id, amb_nombre, tipo_ambiente, sede_sede_id) VALUES 
-- Cúcuta (Sede 1)
('C101', 'Laboratorio de Sistemas 1', 'Especializado', 1),
('C102', 'Laboratorio de Sistemas 2', 'Especializado', 1),
('C103', 'Laboratorio de Redes', 'Especializado', 1),
('C104', 'Aula Teórica 101', 'Convencional', 1),
('C105', 'Aula Teórica 102', 'Convencional', 1),
('C106', 'Taller de Mecánica', 'Especializado', 1),
('C107', 'Aula Multimedial', 'TIC', 1),
('C108', 'Sala de Conferencias', 'Administrativo', 1),
-- Ocaña (Sede 2)
('O201', 'Laboratorio de Cómputo 1', 'Convencional', 2),
('O202', 'Laboratorio de Cómputo 2', 'Convencional', 2),
('O203', 'Aula Teórica 201', 'Convencional', 2),
('O204', 'Aula Teórica 202', 'Convencional', 2),
('O205', 'Taller Polivalente', 'Especializado', 2),
('O206', 'Sala de Bienestar', 'Bienestar', 2),
-- Pamplona (Sede 3)
('P301', 'Laboratorio de Sistemas', 'TIC', 3),
('P302', 'Aula Teórica 301', 'Convencional', 3),
('P303', 'Aula Teórica 302', 'Convencional', 3),
('P304', 'Taller de Mantenimiento', 'Especializado', 3),
('P305', 'Sala de Reuniones', 'Administrativo', 3),
-- Villa del Rosario (Sede 4)
('V401', 'Laboratorio de Cómputo', 'TIC', 4),
('V402', 'Aula Teórica 401', 'Convencional', 4),
('V403', 'Aula Teórica 402', 'Convencional', 4),
('V404', 'Taller Polivalente', 'Especializado', 4),
-- Los Patios (Sede 5)
('L501', 'Laboratorio de Sistemas', 'TIC', 5),
('L502', 'Aula Teórica 501', 'Convencional', 5),
('L503', 'Aula Teórica 502', 'Convencional', 5),
('L504', 'Taller de Mecánica', 'Especializado', 5);

-- ============================================================
-- 5. TÍTULOS DE PROGRAMA
-- ============================================================
INSERT INTO public.titulo_programa (titpro_id, titpro_nombre, centro_formacion_cent_id) VALUES 
(1, 'Técnico', 1), (2, 'Tecnólogo', 1), (3, 'Especialización Tecnológica', 1), 
(4, 'Técnico', 2), (5, 'Tecnólogo', 2);

-- ============================================================
-- 6. PROGRAMAS (10 Programas)
-- ============================================================
INSERT INTO public.programa (prog_codigo, prog_denominacion, tit_programa_titpro_id, prog_tipo, centro_formacion_cent_id) VALUES 
-- CIES (1)
(228106, 'Análisis y Desarrollo de Software', 2, 'Presencial', 1),
(228118, 'Gestión de Redes de Datos', 2, 'Presencial', 1),
(934108, 'Sistemas', 1, 'Presencial', 1),
(300501, 'Gestión de Proyectos de Software', 3, 'Virtual', 1),
(500101, 'Auxiliar Contable y Financiero', 1, 'Presencial', 1),
-- CEDRUM (2)
(934130, 'Mecánica Automotriz', 4, 'Presencial', 2),
(300502, 'Seguridad Informática', 5, 'Virtual', 2),
(400101, 'Inglés para el Trabajo', 4, 'A distancia', 2),
(400102, 'Emprendimiento e Innovación', 4, 'A distancia', 2),
(500102, 'Operario de Mantenimiento Locativo', 4, 'Presencial', 2);

-- ============================================================
-- 7. USUARIOS COORDINADORES
-- ============================================================
INSERT INTO public.usuario_coordinador (numero_documento, coord_nombre_coordinador, coord_correo, coord_password, estado, centro_formacion_id) VALUES 
(1090001, 'Laura Milena Díaz Rojas', 'coord.tic@cies.sena.edu.co',       'password', 1, 1),
(1090002, 'Hernán Darío Prada Cáceres', 'coord.industria@cies.sena.edu.co', 'password', 1, 1),
(1090003, 'Yolanda Suárez Contreras',   'coord.gestion@cies.sena.edu.co',   'password', 1, 1),
(1090004, 'Fabio Enrique Mora Quintero', 'coord.agro@cedrum.sena.edu.co',    'password', 1, 2),
(1090005, 'Claudia Inés Vargas',        'coord.ambiental@cedrum.sena.edu.co','password', 1, 2),
(1090006, 'Jorge Armando Rueda Téllez', 'coord.rural@cedrum.sena.edu.co',   'password', 1, 2);

-- ============================================================
-- 8. COORDINACIONES
-- ============================================================
INSERT INTO public.coordinacion (coord_id, coord_descripcion, centro_formacion_cent_id, estado, coordinador_actual) VALUES 
(1, 'Coordinación TIC y Servicios', 1, 1, 1090001),
(2, 'Coordinación Industria y Manufactura', 1, 1, 1090002),
(3, 'Coordinación Gestión Empresarial', 1, 1, 1090003),
(4, 'Coordinación Agropecuaria', 2, 1, 1090004),
(5, 'Coordinación Ambiental', 2, 1, 1090005),
(6, 'Coordinación Desarrollo Rural', 2, 1, 1090006);

-- ============================================================
-- 9. INSTRUCTORES (15 Instructores)
-- ============================================================
INSERT INTO public.instructor (numero_documento, inst_nombres, inst_apellidos, inst_correo, inst_telefono, centro_formacion_cent_id, inst_password, profesion, especializacion) VALUES 
-- CIES (1)
(101, 'Carlos Andrés',  'Peña Villamizar',    'carlos.pena@cies.sena.edu.co',      3175001001, 1, 'password', 'Ingeniero de Sistemas', 'Desarrollo Web'),
(102, 'Diana Marcela',  'Cáceres Ortiz',      'diana.caceres@cies.sena.edu.co',    3175001002, 1, 'password', 'Ingeniera de Sistemas', 'Redes y Seguridad'),
(103, 'Jhon Fredy',     'Rangel Sepúlveda',   'jhon.rangel@cies.sena.edu.co',      3175001003, 1, 'password', 'Ingeniero de Sistemas', 'Gestión de Proyectos'),
(104, 'Adriana',        'Suárez Maldonado',   'adriana.suarez@cies.sena.edu.co',   3175001004, 1, 'password', 'Ingeniera Industrial', 'Procesos'),
(105, 'Mauricio',       'Delgado Contreras',  'mauricio.delgado@cies.sena.edu.co', 3175001005, 1, 'password', 'Ingeniero Mecánico', 'Automotriz'),
(106, 'Paola Fernanda', 'Niño Guerrero',      'paola.nino@cies.sena.edu.co',       3175001006, 1, 'password', 'Contadora Pública', 'Finanzas'),
(107, 'Rodrigo',        'Hernández Jaimes',   'rodrigo.hernandez@cies.sena.edu.co',3175001007, 1, 'password', 'Ingeniero Eléctrico', 'Electrónica'),
(108, 'Sonia Milena',   'Rueda Castellanos',  'sonia.rueda@cies.sena.edu.co',      3175001008, 1, 'password', 'Licenciada en Inglés', 'Bilingüismo'),
-- CEDRUM (2)
(201, 'Fabián',         'Mora Angarita',      'fabian.mora@cedrum.sena.edu.co',    3175002001, 2, 'password', 'Ingeniero Agrónomo', 'Producción Limpia'),
(202, 'Luz Dary',       'Quintero Pabón',     'luz.quintero@cedrum.sena.edu.co',   3175002002, 2, 'password', 'Ingeniera Ambiental', 'Tratamiento de Aguas'),
(203, 'Néstor Iván',    'Cárdenas Flórez',    'nestor.cardenas@cedrum.sena.edu.co',3175002003, 2, 'password', 'Ingeniero Civil', 'Estructuras'),
(204, 'Yenny Paola',    'Serrano Duarte',     'yenny.serrano@cedrum.sena.edu.co',  3175002004, 2, 'password', 'Administradora de Empresas', 'Emprendimiento'),
(205, 'Álvaro',         'Chaparro Medina',    'alvaro.chaparro@cedrum.sena.edu.co',3175002005, 2, 'password', 'Veterinario', 'Zootecnia'),
(206, 'Marcela',        'Torrado Becerra',    'marcela.torrado@cedrum.sena.edu.co',3175002006, 2, 'password', 'Ingeniera Química', 'Procesos Rurales'),
(207, 'Gustavo',        'Patiño Valderrama',  'gustavo.patino@cedrum.sena.edu.co', 3175002007, 2, 'password', 'Arquitecto', 'Diseño Sostenible');

-- ============================================================
-- 10. COMPETENCIAS (Transversales + Técnicas x10 Programas)
-- ============================================================
-- 10.1 Transversales (IDs 1-12)
INSERT INTO public.competencia (comp_id, comp_nombre_corto, comp_horas, comp_nombre_unidad_competencia) VALUES 
(1, 'INGLES', 48,  'Comprender textos en inglés escritos y auditivos'),
(2, 'ETICA', 48,  'Promover la interacción idónea consigo mismo'),
(3, 'COMUNICACION', 48,  'Utilizar el lenguaje oral y escrito eficazmente'),
(4, 'CULTURA_FISICA', 48,  'Desarrollar habilidades psicomotrices'),
(5, 'DER_FUNDAMENTALES', 48,  'Ejercer los derechos fundamentales del trabajo'),
(6, 'MEDIO_AMBIENTE', 48,  'Promover la preservación ambiental'),
(7, 'SST', 48,  'Aplicar normas de seguridad y salud en el trabajo'),
(8, 'EMPRENDIMIENTO', 48,  'Resolver problemas reales del sector productivo'),
(9, 'INVESTIGACION', 48,  'Desarrollar procesos de investigación aplicada'),
(10, 'MATEMATICAS', 48,  'Aplicar conceptos matemáticos en problemas'),
(11, 'TECNOLOGIAS', 48,  'Aplicar tecnologías de la información'),
(12, 'LIDERAZGO', 48,  'Ejercer liderazgo colaborativo');

-- 10.2 Técnicas (IDs 100+)
INSERT INTO public.competencia (comp_id, comp_nombre_corto, comp_horas, comp_nombre_unidad_competencia, programa_prog_id) VALUES 
-- ADSO (228106)
(101, 'REQUERIMIENTOS', 200, 'Identificar requerimientos de software', 228106),
(102, 'DISEÑO', 150, 'Diseñar la solución de software', 228106),
(103, 'DESARROLLO', 400, 'Codificar componentes de software', 228106),
-- Redes (228118)
(104, 'ADMIN_REDES', 300, 'Configurar dispositivos de red', 228118),
-- Sistemas (934108)
(105, 'MANT_PREVENTIVO', 120, 'Realizar mantenimiento a equipos', 934108),
-- Mecánica (934130)
(106, 'MOTORES', 280, 'Corregir fallas en motores', 934130),
-- Contabilidad (500101)
(107, 'FINANZAS', 200, 'Registrar hechos económicos', 500101);

-- ============================================================
-- 11. HABILITACIONES (Instrucción de Competencias)
-- ============================================================
INSERT INTO public.instru_competencia (instructor_inst_id, programa_prog_id, competencia_comp_id, inscomp_vigencia) VALUES 
-- Carlos Peña -> ADSO (Técnicas) + Tecnologías
(101, 228106, 101, '2028-12-31'), (101, 228106, 103, '2028-12-31'), (101, 228106, 11, '2028-12-31'),
-- Diana Cáceres -> Redes + Sistemas
(102, 228118, 104, '2028-12-31'), (102, 934108, 105, '2028-12-31'),
-- Fabián Mora (CEDRUM) -> Medio Ambiente
(201, 228106, 6, '2028-12-31'),
-- Sonia Rueda -> Inglés todas las fichas
(108, 228106, 1, '2028-12-31'), (108, 228118, 1, '2028-12-31'), (108, 934130, 1, '2028-12-31');

-- ============================================================
-- 12. PROYECTOS FORMATIVOS (1 por programa)
-- ============================================================
INSERT INTO public.proyecto_formativo (pf_id, pf_codigo, pf_nombre, pf_descripcion, programa_prog_codigo, centro_formacion_cent_id) VALUES 
(1, 'P-ADSO-01', 'Sistema de Gestión Escolar', 'Desarrollo de un software para control de notas.', 228106, 1),
(2, 'P-REDES-01', 'Diseño de Red de Campus', 'Implementación de topología jerárquica.', 228118, 1),
(3, 'P-AUTO-01', 'Taller de Diagnóstico Electrónico', 'Mantenimiento preventivo de flota.', 934130, 2),
(4, 'P-CONT-01', 'Automatización de Nómina', 'Sistema de liquidación de sueldos.', 500101, 1),
(5, 'P-SIST-01', 'Optimización de Datacenter', 'Soporte y mantenimiento de infraestructura.', 934108, 1),
(6, 'P-PROY-01', 'Gestión Ágil de Software', 'Metodologías para equipos de desarrollo.', 300501, 1),
(7, 'P-SEG-01', 'Auditoría de Seguridad', 'Detección de vulnerabilidades en red.', 300502, 2),
(8, 'P-ING-01', 'Inglés Comunicativo B1', 'Comunicación asertiva en inglés.', 400101, 2),
(9, 'P-EMP-01', 'Pre-Incubadora de Negocios', 'Plan de negocios para emprendimientos.', 400102, 2),
(10, 'P-MANT-01', 'Mantenimiento Locativo Integral', 'Gestión de espacios físicos.', 500102, 2);

-- ============================================================
-- 13. FASES DE PROYECTOS (Muestra para cada proyecto)
-- ============================================================
INSERT INTO public.fase_proyecto (fase_id, fase_nombre, fase_orden, fase_fecha_ini, fase_fecha_fin, pf_pf_id) VALUES 
-- Proyecto 1 (ADSO)
(1, 'Análisis', 1, '2025-01-01', '2025-03-31', 1), (2, 'Planeación', 2, '2025-04-01', '2025-06-30', 1),
(3, 'Ejecución', 3, '2025-07-01', '2026-06-30', 1), (4, 'Evaluación', 4, '2026-07-01', '2026-12-31', 1),
-- Proyecto 5 (Sistemas)
(5, 'Diagnóstico', 1, '2025-01-15', '2025-02-15', 5), (6, 'Intervención', 2, '2025-02-16', '2025-06-15', 5),
-- Proyecto 10 (Mant. Locativo)
(7, 'Inspección', 1, '2025-01-01', '2025-01-31', 10), (8, 'Reparación', 3, '2025-02-01', '2025-10-31', 10);

-- ============================================================
-- 14. ACTIVIDADES DE PROYECTO
-- ============================================================
INSERT INTO public.actividad_proyecto (act_id, act_nombre, fase_id) VALUES 
(1, 'Levantamiento de Requisitos', 1), (2, 'Modelado de Base de Datos', 2),
(3, 'Codificación de Módulos', 3), (4, 'Despliegue en Servidor', 4),
(5, 'Instalación de Servidores', 6), (6, 'Pruebas de Estrés', 6),
(7, 'Inspección de Maquinaria', 7), (8, 'Reparación de Motores', 8);

-- ============================================================
-- 15. RESULTADOS DE APRENDIZAJE (RAPs)
-- ============================================================
INSERT INTO public.resultado_aprendizaje (rap_id, rap_codigo, rap_descripcion, rap_horas, programa_prog_id, competencia_comp_id) VALUES 
-- ADSO Técnicos
(1, 'RAP-101', 'Elaborar instrumentos de recolección de datos', 40, 228106, 101),
(2, 'RAP-102', 'Modelar el sistema usando UML', 60, 228106, 102),
(3, 'RAP-103', 'Implementar el frontend de la solución', 120, 228106, 103),
-- ADSO Transversales
(4, 'RAP-COM-01', 'Expresar ideas con claridad de forma escrita', 12, 228106, 3),
(5, 'RAP-ING-01', 'Comprender vocabulario técnico en manuales', 24, 228106, 1),
-- Redes (228118)
(6, 'RAP-NET-01', 'Instalar medios de transmisión física', 40, 228118, 104),
(7, 'RAP-NET-02', 'Configurar servicios de red básicos', 60, 228118, 104),
-- Sistemas (934108)
(8, 'RAP-SIS-01', 'Ensamblar componentes de hardware', 30, 934108, 105),
-- Mecánica (934130)
(9, 'RAP-AUT-01', 'Desmontar sistemas de frenos', 50, 934130, 106),
-- Contabilidad (500101)
(10, 'RAP-FIN-01', 'Elaborar estados financieros básicos', 80, 500101, 107),
(11, 'RAP-SEG-01', 'Configurar firewalls perimetrales', 45, 300502, 104);

-- ============================================================
-- 16. RAP -> FASE / RAP -> ACTIVIDAD
-- ============================================================
-- Fases
INSERT INTO public.rap_fase (rap_rap_id, fase_fase_id) VALUES 
(1, 1), (2, 2), (3, 3), (4, 1), (5, 2), 
(6, 5), (7, 6), (9, 7), (10, 8);
-- Actividades
INSERT INTO public.rap_actividad (rap_id, act_id) VALUES (1, 1), (2, 2), (3, 3), (6, 5), (10, 8);

-- ============================================================
-- 17. FICHAS (Varias por Coordinación)
-- ============================================================
INSERT INTO public.ficha (fich_id, programa_prog_id, instructor_inst_id_lider, fich_jornada, coordinacion_coord_id, fich_fecha_ini_lectiva, fich_fecha_fin_lectiva) VALUES 
-- TIC (CIES)
(2750001, 228106, 101, 'Mañana', 1, '2025-01-01', '2026-12-31'),
(2750002, 228118, 102, 'Tarde', 1, '2025-01-01', '2026-12-31'),
(2750003, 300501, 103, 'Mixta', 1, '2025-06-01', '2026-05-31'),
-- Industria (CIES)
(2750004, 934108, 104, 'Mañana', 2, '2025-01-01', '2025-12-31'),
(2750005, 500102, 105, 'Noche', 2, '2025-01-01', '2025-12-31'),
-- Gestión (CIES)
(2750006, 500101, 106, 'Tarde', 3, '2025-09-01', '2026-08-31'),
-- Agro (CEDRUM)
(2670553, 934130, 201, 'Mixta', 4, '2025-01-01', '2025-12-31'),
(2670554, 300502, 202, 'Mañana', 4, '2025-01-01', '2026-12-31');

-- ============================================================
-- 18. ASIGNACIONES (2 por Coordinador)
-- ============================================================
INSERT INTO public.asignacion (asig_id, instructor_inst_id, asig_fecha_ini, asig_fecha_fin, ficha_fich_id, ambiente_amb_id, competencia_comp_id) VALUES 
-- Coordinador 1 (Laura - TIC)
(1, 101, '2026-03-02', '2026-03-27', 2750001, 'C101', 101),
(2, 103, '2026-03-02', '2026-03-27', 2750001, 'C102', 11),
-- Coordinador 2 (Hernán - Industria)
(3, 104, '2026-04-01', '2026-04-30', 2750004, 'C104', 105),
(4, 105, '2026-04-01', '2026-04-30', 2750005, 'C106', 7),
-- Coordinador 4 (Fabio - Agro)
(5, 201, '2025-04-01', '2025-04-30', 2670553, 'P301', 6),
(6, 202, '2025-04-01', '2025-04-30', 2670554, 'P304', 104);

-- ============================================================
-- 19. DETALLES DE ASIGNACIÓN (Horarios)
-- ============================================================
INSERT INTO public.detallexasignacion (detasig_id, asignacion_asig_id, detasig_fecha, detasig_hora_ini, detasig_hora_fin, observaciones) VALUES 
(1, 1, '2026-03-02', '08:00:00', '12:00:00', 'Clase presencial Requerimientos'),
(2, 1, '2026-03-04', '08:00:00', '12:00:00', 'Clase presencial Requerimientos'),
(3, 2, '2026-03-03', '14:00:00', '18:00:00', 'Taller de TIC'),
(4, 5, '2025-04-07', '07:00:00', '11:00:00', 'Prácticas de campo'),
(5, 6, '2025-04-08', '08:00:00', '12:00:00', 'Seguridad en Redes');

-- ============================================================
-- 20. SINCRONIZACIÓN DE SECUENCIAS
-- ============================================================
SELECT pg_catalog.setval('public.resultado_aprendizaje_rap_id_seq', 11, true);
SELECT pg_catalog.setval('public.coordinacion_coord_id_seq', 6, true);
SELECT pg_catalog.setval('public.instru_competencia_inscomp_id_seq', 10, true);
SELECT pg_catalog.setval('public.proyecto_formativo_pf_id_seq', 10, true);
SELECT pg_catalog.setval('public.fase_proyecto_fase_id_seq', 8, true);
SELECT pg_catalog.setval('public.actividad_proyecto_act_id_seq', 8, true);
SELECT pg_catalog.setval('public.asignacion_asig_id_seq', 6, true);
SELECT pg_catalog.setval('public.detallexasignacion_detasig_id_seq', 5, true);
SELECT pg_catalog.setval('public.auditoria_asignacion_id_auditoria_seq', 1, false);
