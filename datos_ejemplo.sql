-- ============================================================
-- DATOS DE EJEMPLO - Sistema de Programas de Formación SENA
-- Norte de Santander - CIES y CEDRUM
-- Compatible con PostgreSQL
-- Limpia todas las tablas antes de insertar
-- ============================================================

BEGIN;

-- ============================================================
-- LIMPIEZA (orden inverso a dependencias FK)
-- ============================================================
TRUNCATE TABLE public.detallexasignacion    RESTART IDENTITY CASCADE;
TRUNCATE TABLE public.auditoria_asignacion  RESTART IDENTITY CASCADE;
TRUNCATE TABLE public.asignacion            RESTART IDENTITY CASCADE;
TRUNCATE TABLE public.instru_competencia    RESTART IDENTITY CASCADE;
TRUNCATE TABLE public.ficha                                CASCADE;
TRUNCATE TABLE public.competxprograma                      CASCADE;
TRUNCATE TABLE public.coordinacion          RESTART IDENTITY CASCADE;
TRUNCATE TABLE public.instructor                           CASCADE;
TRUNCATE TABLE public.usuario_coordinador                  CASCADE;
TRUNCATE TABLE public.competencia                          CASCADE;
TRUNCATE TABLE public.programa                             CASCADE;
TRUNCATE TABLE public.titulo_programa                      CASCADE;
TRUNCATE TABLE public.ambiente                             CASCADE;
TRUNCATE TABLE public.sede                                 CASCADE;
TRUNCATE TABLE public.centro_formacion                     CASCADE;

-- ============================================================
-- 1. CENTROS DE FORMACIÓN
-- cent_id | cent_nombre | cent_correo | cent_password
-- ============================================================
INSERT INTO public.centro_formacion (cent_id, cent_nombre, cent_correo, cent_password) VALUES
(1, 'CIES',   'cies@sena.edu.co',   crypt('password',   gen_salt('bf', 6))),
(2, 'CEDRUM', 'cedrum@sena.edu.co', crypt('password', gen_salt('bf', 6)));

-- ============================================================
-- 2. TÍTULOS DE PROGRAMA
-- titpro_id | titpro_nombre | centro_formacion_cent_id
-- (NULL = transversal a todos los centros)
-- ============================================================
INSERT INTO public.titulo_programa (titpro_id, titpro_nombre, centro_formacion_cent_id) VALUES
(1, 'Técnico',                    NULL),
(2, 'Tecnólogo',                  NULL),
(3, 'Especialización Tecnológica', NULL),
(4, 'Curso Corto',                NULL),
(5, 'Operario y/o Auxiliar',      NULL);

-- ============================================================
-- 3. PROGRAMAS
-- prog_codigo | prog_denominacion | tit_programa_titpro_id | prog_tipo | centro_formacion_cent_id
-- 2 por título de programa
-- ============================================================
INSERT INTO public.programa (prog_codigo, prog_denominacion, tit_programa_titpro_id, prog_tipo, centro_formacion_cent_id) VALUES
-- Técnico (titpro_id=1)
(110101, 'Técnico en Sistemas',                                    1, 'Técnico',                    1),
(110102, 'Técnico en Contabilización de Operaciones Comerciales',  1, 'Técnico',                    2),
-- Tecnólogo (titpro_id=2)
(220201, 'Tecnología en Análisis y Desarrollo de Software',        2, 'Tecnólogo',                  1),
(220202, 'Tecnología en Gestión Empresarial',                      2, 'Tecnólogo',                  2),
-- Especialización Tecnológica (titpro_id=3)
(330301, 'Especialización en Seguridad Informática',               3, 'Especialización Tecnológica', 1),
(330302, 'Especialización en Gestión del Talento Humano',          3, 'Especialización Tecnológica', 2),
-- Curso Corto (titpro_id=4)
(440401, 'Curso de Excel Avanzado',                                4, 'Curso Corto',                1),
(440402, 'Curso de Atención al Cliente',                           4, 'Curso Corto',                2),
-- Operario y/o Auxiliar (titpro_id=5)
(550501, 'Auxiliar en Servicios Administrativos',                  5, 'Operario y/o Auxiliar',      1),
(550502, 'Operario en Mantenimiento de Equipos',                   5, 'Operario y/o Auxiliar',      2);

-- ============================================================
-- 4. COMPETENCIAS TRANSVERSALES
-- comp_id | comp_nombre_corto(30) | comp_horas | comp_nombre_unidad_competencia(150) | centro_formacion_cent_id
-- ============================================================
INSERT INTO public.competencia (comp_id, comp_nombre_corto, comp_horas, comp_nombre_unidad_competencia, centro_formacion_cent_id) VALUES
(1,  'Inglés',               48, 'Interactuar en idioma extranjero según el Marco Común Europeo de Referencia para Idiomas',  NULL),
(2,  'Ética',                48, 'Ejercer derechos fundamentales del trabajo y comportamientos éticos',                        NULL),
(3,  'Comunicación',         48, 'Comunicar de forma asertiva en contextos laborales y sociales',                              NULL),
(4,  'Cultura Física',       48, 'Promover hábitos de vida saludable mediante la actividad física',                            NULL),
(5,  'Derechos Fundamentales', 48, 'Aplicar los derechos fundamentales en el contexto laboral y ciudadano',                   NULL),
(6,  'Medio Ambiente',       48, 'Promover la gestión ambiental sostenible en el entorno productivo',                          NULL),
(7,  'SST',                  48, 'Aplicar los principios de Seguridad y Salud en el Trabajo',                                  NULL),
(8,  'Emprendimiento',       48, 'Desarrollar iniciativas de emprendimiento e innovación empresarial',                         NULL),
(9,  'Investigación',        48, 'Aplicar metodología de investigación en contextos formativos',                               NULL),
(10, 'Matemáticas',          48, 'Razonar cuantitativamente frente a situaciones matemáticas en contextos laborales',          NULL),
(11, 'Tecnologías',          48, 'Utilizar herramientas tecnológicas en el desempeño laboral',                                 NULL),
(12, 'Liderazgo',            48, 'Ejercer liderazgo y trabajo en equipo en contextos organizacionales',                        NULL);

-- ============================================================
-- 5. COMPETENCIA_PROGRAMA
-- programa_prog_id | competencia_comp_id
-- Todas las transversales vinculadas a todos los programas
-- ============================================================
INSERT INTO public.competxprograma (programa_prog_id, competencia_comp_id)
SELECT p.prog_codigo, c.comp_id
FROM public.programa p
CROSS JOIN public.competencia c;

-- ============================================================
-- 6. SEDES (Norte de Santander - reales)
-- sede_id | sede_nombre | centro_formacion_cent_id
-- ============================================================
INSERT INTO public.sede (sede_id, sede_nombre, centro_formacion_cent_id) VALUES
-- CIES (Cúcuta)
(1, 'Sede Principal Cúcuta - CIES',                                    1),
(2, 'Sede Los Patios - CIES',                                          1),
(3, 'Sede Villa del Rosario - CIES',                                   1),
(4, 'Sede Comuneros - Moda, Confección y Multimedia - CIES',           1),
-- CEDRUM (Ocaña)
(5, 'Sede Principal Ocaña - CEDRUM',                                   2),
(6, 'Sede Convención - CEDRUM',                                        2),
(7, 'Sede Teorama - CEDRUM',                                           2);

-- ============================================================
-- 7. AMBIENTES (4-8 por sede)
-- amb_id(5) | amb_nombre(45) | tipo_ambiente | sede_sede_id
-- ============================================================
INSERT INTO public.ambiente (amb_id, amb_nombre, tipo_ambiente, sede_sede_id) VALUES
-- Sede 1: Principal Cúcuta CIES (6 ambientes)
('A101', 'Aula 101 Sistemas',        'Especializado',  1),
('A102', 'Aula 102 Redes',           'Especializado',  1),
('A103', 'Aula 103 Convencional',    'Convencional',   1),
('A104', 'Lab Electrónica',          'Especializado',  1),
('A105', 'Aula 105 Multimedios',     'Especializado',  1),
('A106', 'Aula 106 Convencional',    'Convencional',   1),
-- Sede 2: Los Patios CIES (5 ambientes)
('B101', 'Aula 101 Informática',     'Especializado',  2),
('B102', 'Aula 102 Convencional',    'Convencional',   2),
('B103', 'Aula 103 Convencional',    'Convencional',   2),
('B104', 'Lab Bilingüismo',          'Especializado',  2),
('B105', 'Aula 105 Convencional',    'Convencional',   2),
-- Sede 3: Villa del Rosario CIES (4 ambientes)
('C101', 'Aula 101 Convencional',    'Convencional',   3),
('C102', 'Aula 102 Sistemas',        'Especializado',  3),
('C103', 'Aula 103 Convencional',    'Convencional',   3),
('C104', 'Lab Contabilidad',         'Especializado',  3),
-- Sede 4: Comuneros CIES (5 ambientes)
('D101', 'Aula 101 Diseño',          'Especializado',  4),
('D102', 'Aula 102 Convencional',    'Convencional',   4),
('D103', 'Aula 103 Confección',      'Especializado',  4),
('D104', 'Aula 104 Convencional',    'Convencional',   4),
('D105', 'Lab Multimedia',           'Especializado',  4),
-- Sede 5: Principal Ocaña CEDRUM (7 ambientes)
('E101', 'Aula 101 Sistemas',        'Especializado',  5),
('E102', 'Aula 102 Convencional',    'Convencional',   5),
('E103', 'Aula 103 Convencional',    'Convencional',   5),
('E104', 'Lab Agroindustria',        'Especializado',  5),
('E105', 'Aula 105 Multimedios',     'Especializado',  5),
('E106', 'Aula 106 Convencional',    'Convencional',   5),
('E107', 'Lab Química',              'Especializado',  5),
-- Sede 6: Convención CEDRUM (4 ambientes)
('F101', 'Aula 101 Convencional',    'Convencional',   6),
('F102', 'Aula 102 Convencional',    'Convencional',   6),
('F103', 'Lab Cómputo',              'Especializado',  6),
('F104', 'Aula 104 Convencional',    'Convencional',   6),
-- Sede 7: Teorama CEDRUM (4 ambientes)
('G101', 'Aula 101 Convencional',    'Convencional',   7),
('G102', 'Aula 102 Convencional',    'Convencional',   7),
('G103', 'Lab Cómputo',              'Especializado',  7),
('G104', 'Aula 104 Convencional',    'Convencional',   7);

-- ============================================================
-- 8. USUARIOS COORDINADORES
-- numero_documento | coord_nombre_coordinador | coord_correo(60) | coord_password(150) | estado | centro_formacion_id
-- ============================================================
INSERT INTO public.usuario_coordinador (numero_documento, coord_nombre_coordinador, coord_correo, coord_password, estado, centro_formacion_id) VALUES
-- CIES
(10000001, 'Carlos Andrés Pérez Rueda',   'cperez@sena.edu.co',   crypt('password', gen_salt('bf', 6)), 1, 1),
(10000002, 'María Fernanda López Torres', 'mlopez@sena.edu.co',   crypt('password', gen_salt('bf', 6)), 1, 1),
(10000003, 'Jorge Enrique Díaz Cárdenas', 'jdiaz@sena.edu.co',    crypt('password', gen_salt('bf', 6)), 1, 1),
-- CEDRUM
(10000004, 'Ana Milena Rojas Quintero',   'arojas@sena.edu.co',   crypt('password', gen_salt('bf', 6)), 1, 2),
(10000005, 'Luis Fernando Vargas Mora',   'lvargas@sena.edu.co',  crypt('password', gen_salt('bf', 6)), 1, 2),
(10000006, 'Sandra Patricia Niño Blanco', 'snino@sena.edu.co',    crypt('password', gen_salt('bf', 6)), 1, 2);

-- ============================================================
-- 9. COORDINACIONES (3 por centro)
-- coord_descripcion(45) | centro_formacion_cent_id | coord_id | estado | coordinador_actual -> usuario_coordinador.numero_documento
-- ============================================================
INSERT INTO public.coordinacion (coord_id, coord_descripcion, centro_formacion_cent_id, estado, coordinador_actual) VALUES
(1, 'Coordinación TIC',               1, 1, 10000001),
(2, 'Coordinación Industria Comercio', 1, 1, 10000002),
(3, 'Coordinación Gestión Empresarial',1, 1, 10000003),
(4, 'Coordinación Agroindustria',      2, 1, 10000004),
(5, 'Coordinación Salud y Bienestar',  2, 1, 10000005),
(6, 'Coordinación Desarrollo Rural',   2, 1, 10000006);

SELECT setval('public.coordinacion_coord_id_seq', 6);

-- ============================================================
-- 10. INSTRUCTORES
-- inst_nombres(45) | inst_apellidos(45) | inst_correo(45) | inst_telefono | centro_formacion_cent_id | inst_password(150) | numero_documento (PK) | estado
-- ============================================================
INSERT INTO public.instructor (numero_documento, inst_nombres, inst_apellidos, inst_correo, inst_telefono, centro_formacion_cent_id, inst_password, estado) VALUES
-- CIES (8 instructores)
(20000001, 'Andrés',   'Martínez Gómez',   'amartinez@sena.edu.co',  3001234501, 1, crypt('password', gen_salt('bf', 6)), 1),
(20000002, 'Beatriz',  'Sánchez Mora',     'bsanchez@sena.edu.co',   3001234502, 1, crypt('password', gen_salt('bf', 6)), 1),
(20000003, 'Camilo',   'Torres Ríos',      'ctorres@sena.edu.co',    3001234503, 1, crypt('password', gen_salt('bf', 6)), 1),
(20000004, 'Diana',    'Ramírez Castro',   'dramirez@sena.edu.co',   3001234504, 1, crypt('password', gen_salt('bf', 6)), 1),
(20000005, 'Eduardo',  'Vargas Peña',      'evargas@sena.edu.co',    3001234505, 1, crypt('password', gen_salt('bf', 6)), 1),
(20000006, 'Fernanda', 'Ortiz Leal',       'fortiz@sena.edu.co',     3001234506, 1, crypt('password', gen_salt('bf', 6)), 1),
(20000007, 'Gustavo',  'Herrera Pinto',    'gherrera@sena.edu.co',   3001234507, 1, crypt('password', gen_salt('bf', 6)), 1),
(20000008, 'Helena',   'Cárdenas Suárez',  'hcardenas@sena.edu.co',  3001234508, 1, crypt('password', gen_salt('bf', 6)), 1),
-- CEDRUM (7 instructores)
(20000009, 'Iván',     'Morales Duarte',   'imorales@sena.edu.co',   3001234509, 2, crypt('password', gen_salt('bf', 6)), 1),
(20000010, 'Juliana',  'Prada Acosta',     'jprada@sena.edu.co',     3001234510, 2, crypt('password', gen_salt('bf', 6)), 1),
(20000011, 'Kevin',    'Blanco Serrano',   'kblanco@sena.edu.co',    3001234511, 2, crypt('password', gen_salt('bf', 6)), 1),
(20000012, 'Laura',    'Quintero Vega',    'lquintero@sena.edu.co',  3001234512, 2, crypt('password', gen_salt('bf', 6)), 1),
(20000013, 'Miguel',   'Fuentes Arenas',   'mfuentes@sena.edu.co',   3001234513, 2, crypt('password', gen_salt('bf', 6)), 1),
(20000014, 'Natalia',  'Guerrero Páez',    'nguerrero@sena.edu.co',  3001234514, 2, crypt('password', gen_salt('bf', 6)), 1),
(20000015, 'Oscar',    'Delgado Rincón',   'odelgado@sena.edu.co',   3001234515, 2, crypt('password', gen_salt('bf', 6)), 1);

-- ============================================================
-- 11. FICHAS
-- fich_id | programa_prog_id | instructor_inst_id_lider -> instructor.numero_documento
-- fich_jornada(20) | coordinacion_coord_id | fich_fecha_ini_lectiva | fich_fecha_fin_lectiva
-- 3-5 fichas por coordinación, jornadas variadas
-- ============================================================
INSERT INTO public.ficha (fich_id, programa_prog_id, instructor_inst_id_lider, fich_jornada, coordinacion_coord_id, fich_fecha_ini_lectiva, fich_fecha_fin_lectiva) VALUES
-- Coord 1: TIC - CIES (5 fichas)
(4001001, 220201, 20000001, 'Mañana',  1, '2025-03-01', '2026-12-15'),
(4001002, 110101, 20000002, 'Tarde',   1, '2025-03-01', '2026-06-30'),
(4001003, 220201, 20000003, 'Noche',   1, '2025-06-01', '2026-12-15'),
(4001004, 330301, 20000001, 'Mixta',   1, '2025-09-01', '2026-09-30'),
(4001005, 110101, 20000004, 'Mañana',  1, '2026-01-15', '2027-06-30'),
-- Coord 2: Industria y Comercio - CIES (4 fichas)
(4002001, 110102, 20000005, 'Tarde',   2, '2025-03-01', '2026-06-30'),
(4002002, 220202, 20000006, 'Mañana',  2, '2025-06-01', '2026-12-15'),
(4002003, 110102, 20000007, 'Noche',   2, '2025-09-01', '2026-09-30'),
(4002004, 550501, 20000005, 'Mixta',   2, '2026-01-15', '2026-12-15'),
-- Coord 3: Gestión Empresarial - CIES (3 fichas)
(4003001, 220202, 20000008, 'Mañana',  3, '2025-06-01', '2026-12-15'),
(4003002, 330302, 20000007, 'Tarde',   3, '2025-09-01', '2026-09-30'),
(4003003, 550501, 20000006, 'Noche',   3, '2026-01-15', '2027-06-30'),
-- Coord 4: Agroindustria - CEDRUM (5 fichas)
(4004001, 550502, 20000009, 'Mañana',  4, '2025-03-01', '2026-06-30'),
(4004002, 110102, 20000010, 'Tarde',   4, '2025-06-01', '2026-12-15'),
(4004003, 550502, 20000011, 'Noche',   4, '2025-09-01', '2026-09-30'),
(4004004, 440402, 20000009, 'Mixta',   4, '2026-01-15', '2026-12-15'),
(4004005, 110102, 20000012, 'Mañana',  4, '2026-03-01', '2027-06-30'),
-- Coord 5: Salud y Bienestar - CEDRUM (4 fichas)
(4005001, 440402, 20000013, 'Tarde',   5, '2025-03-01', '2026-06-30'),
(4005002, 330302, 20000014, 'Mañana',  5, '2025-06-01', '2026-12-15'),
(4005003, 440402, 20000015, 'Noche',   5, '2025-09-01', '2026-09-30'),
(4005004, 550502, 20000013, 'Mixta',   5, '2026-01-15', '2027-06-30'),
-- Coord 6: Desarrollo Rural - CEDRUM (3 fichas)
(4006001, 550502, 20000009, 'Mañana',  6, '2025-06-01', '2026-12-15'),
(4006002, 110102, 20000010, 'Tarde',   6, '2025-09-01', '2026-09-30'),
(4006003, 550502, 20000011, 'Noche',   6, '2026-01-15', '2027-06-30');

-- ============================================================
-- 12. INSTRUCTOR_COMPETENCIA (instru_competencia)
-- inscomp_id (seq) | instructor_inst_id | competxprograma_programa_prog_id
-- competxprograma_competencia_comp_id | inscomp_vigencia
-- El par (programa_prog_id, competencia_comp_id) DEBE existir en competxprograma
-- ============================================================
INSERT INTO public.instru_competencia (instructor_inst_id, competxprograma_programa_prog_id, competxprograma_competencia_comp_id, inscomp_vigencia) VALUES
-- CIES - Instructor 20000001: Inglés, Comunicación, Tecnologías (prog CIES)
(20000001, 220201, 1,  '2027-12-31'),
(20000001, 220201, 3,  '2027-12-31'),
(20000001, 220201, 11, '2027-12-31'),
(20000001, 110101, 1,  '2027-12-31'),
(20000001, 110101, 3,  '2027-12-31'),
-- CIES - Instructor 20000002: Ética, Derechos Fundamentales, Liderazgo
(20000002, 220201, 2,  '2027-12-31'),
(20000002, 220201, 5,  '2027-12-31'),
(20000002, 220201, 12, '2027-12-31'),
(20000002, 110101, 2,  '2027-12-31'),
(20000002, 110101, 5,  '2027-12-31'),
-- CIES - Instructor 20000003: Matemáticas, Investigación, Emprendimiento
(20000003, 220201, 10, '2027-12-31'),
(20000003, 220201, 9,  '2027-12-31'),
(20000003, 220201, 8,  '2027-12-31'),
(20000003, 110101, 10, '2027-12-31'),
(20000003, 330301, 10, '2027-12-31'),
-- CIES - Instructor 20000004: SST, Medio Ambiente, Cultura Física
(20000004, 220201, 7,  '2027-12-31'),
(20000004, 220201, 6,  '2027-12-31'),
(20000004, 220201, 4,  '2027-12-31'),
(20000004, 110101, 7,  '2027-12-31'),
(20000004, 110101, 6,  '2027-12-31'),
-- CIES - Instructor 20000005: Inglés, Comunicación (prog comerciales)
(20000005, 110102, 1,  '2027-12-31'),
(20000005, 110102, 3,  '2027-12-31'),
(20000005, 220202, 1,  '2027-12-31'),
(20000005, 220202, 3,  '2027-12-31'),
(20000005, 550501, 1,  '2027-12-31'),
-- CIES - Instructor 20000006: Ética, Liderazgo, Emprendimiento
(20000006, 110102, 2,  '2027-12-31'),
(20000006, 110102, 12, '2027-12-31'),
(20000006, 110102, 8,  '2027-12-31'),
(20000006, 220202, 2,  '2027-12-31'),
(20000006, 550501, 2,  '2027-12-31'),
-- CIES - Instructor 20000007: Matemáticas, SST, Tecnologías
(20000007, 110102, 10, '2027-12-31'),
(20000007, 110102, 7,  '2027-12-31'),
(20000007, 110102, 11, '2027-12-31'),
(20000007, 220202, 10, '2027-12-31'),
(20000007, 330302, 10, '2027-12-31'),
-- CIES - Instructor 20000008: Medio Ambiente, Derechos Fundamentales, Cultura Física
(20000008, 110102, 6,  '2027-12-31'),
(20000008, 110102, 5,  '2027-12-31'),
(20000008, 110102, 4,  '2027-12-31'),
(20000008, 220202, 6,  '2027-12-31'),
(20000008, 330302, 6,  '2027-12-31'),
-- CEDRUM - Instructor 20000009: Inglés, Comunicación, Tecnologías
(20000009, 550502, 1,  '2027-12-31'),
(20000009, 550502, 3,  '2027-12-31'),
(20000009, 550502, 11, '2027-12-31'),
(20000009, 110102, 1,  '2027-12-31'),
(20000009, 440402, 1,  '2027-12-31'),
-- CEDRUM - Instructor 20000010: Ética, Liderazgo, Emprendimiento
(20000010, 550502, 2,  '2027-12-31'),
(20000010, 550502, 12, '2027-12-31'),
(20000010, 550502, 8,  '2027-12-31'),
(20000010, 110102, 2,  '2027-12-31'),
(20000010, 440402, 2,  '2027-12-31'),
-- CEDRUM - Instructor 20000011: Matemáticas, Investigación, SST
(20000011, 550502, 10, '2027-12-31'),
(20000011, 550502, 9,  '2027-12-31'),
(20000011, 550502, 7,  '2027-12-31'),
(20000011, 110102, 10, '2027-12-31'),
(20000011, 440402, 10, '2027-12-31'),
-- CEDRUM - Instructor 20000012: Medio Ambiente, Cultura Física, Derechos Fundamentales
(20000012, 550502, 6,  '2027-12-31'),
(20000012, 550502, 4,  '2027-12-31'),
(20000012, 550502, 5,  '2027-12-31'),
(20000012, 110102, 6,  '2027-12-31'),
(20000012, 440402, 6,  '2027-12-31'),
-- CEDRUM - Instructor 20000013: Inglés, Comunicación, Liderazgo
(20000013, 440402, 1,  '2027-12-31'),
(20000013, 440402, 3,  '2027-12-31'),
(20000013, 440402, 12, '2027-12-31'),
(20000013, 330302, 1,  '2027-12-31'),
(20000013, 550502, 12, '2027-12-31'),
-- CEDRUM - Instructor 20000014: Ética, Emprendimiento, Investigación
(20000014, 440402, 2,  '2027-12-31'),
(20000014, 440402, 8,  '2027-12-31'),
(20000014, 440402, 9,  '2027-12-31'),
(20000014, 330302, 2,  '2027-12-31'),
(20000014, 550502, 9,  '2027-12-31'),
-- CEDRUM - Instructor 20000015: Matemáticas, SST, Tecnologías
(20000015, 440402, 10, '2027-12-31'),
(20000015, 440402, 7,  '2027-12-31'),
(20000015, 440402, 11, '2027-12-31'),
(20000015, 330302, 10, '2027-12-31'),
(20000015, 550502, 11, '2027-12-31');

SELECT setval('public.instru_competencia_inscomp_id_seq',
    (SELECT MAX(inscomp_id) FROM public.instru_competencia));

-- ============================================================
-- 13. ASIGNACIONES
-- asig_id | instructor_inst_id -> instructor.numero_documento
-- asig_fecha_ini | asig_fecha_fin | ficha_fich_id | ambiente_amb_id | competencia_comp_id
-- Reglas:
--   - instructor debe tener instru_competencia vigente para (programa de la ficha, competencia)
--   - fechas dentro del rango lectivo de la ficha
--   - ambiente debe pertenecer a sede del mismo centro que la coordinación de la ficha
--   - 1-2 asignaciones por coordinación
-- ============================================================
INSERT INTO public.asignacion (asig_id, instructor_inst_id, asig_fecha_ini, asig_fecha_fin, ficha_fich_id, ambiente_amb_id, competencia_comp_id) VALUES
-- Coord 1 TIC-CIES: ficha 4001001 (prog 220201, Mañana), ficha 4001002 (prog 110101, Tarde)
(1001, 20000001, '2026-03-16', '2026-04-24', 4001001, 'A101', 1),   -- Inglés
(1002, 20000003, '2026-03-16', '2026-04-24', 4001002, 'A103', 10),  -- Matemáticas
-- Coord 2 Industria-CIES: ficha 4002001 (prog 110102, Tarde), ficha 4002002 (prog 220202, Mañana)
(1003, 20000005, '2026-03-16', '2026-04-24', 4002001, 'B101', 1),   -- Inglés
(1004, 20000007, '2026-03-16', '2026-04-24', 4002002, 'B102', 10),  -- Matemáticas
-- Coord 3 Gestión-CIES: ficha 4003001 (prog 220202, Mañana), ficha 4003002 (prog 330302, Tarde)
(1005, 20000006, '2026-03-16', '2026-04-24', 4003001, 'C101', 2),   -- Ética
(1006, 20000008, '2026-03-16', '2026-04-24', 4003002, 'C103', 6),   -- Medio Ambiente
-- Coord 4 Agroindustria-CEDRUM: ficha 4004001 (prog 550502, Mañana), ficha 4004002 (prog 110102, Tarde)
(1007, 20000009, '2026-03-16', '2026-04-24', 4004001, 'E101', 1),   -- Inglés
(1008, 20000011, '2026-03-16', '2026-04-24', 4004002, 'E102', 10),  -- Matemáticas
-- Coord 5 Salud-CEDRUM: ficha 4005001 (prog 440402, Tarde), ficha 4005002 (prog 330302, Mañana)
(1009, 20000013, '2026-03-16', '2026-04-24', 4005001, 'F101', 1),   -- Inglés
(1010, 20000014, '2026-03-16', '2026-04-24', 4005002, 'F102', 2),   -- Ética
-- Coord 6 Desarrollo Rural-CEDRUM: ficha 4006001 (prog 550502, Mañana), ficha 4006002 (prog 110102, Tarde)
(1011, 20000009, '2026-03-16', '2026-04-24', 4006001, 'G101', 3),   -- Comunicación
(1012, 20000011, '2026-03-16', '2026-04-24', 4006002, 'G102', 7);   -- SST

SELECT setval('public.asignacion_asig_id_seq', 1012);

-- ============================================================
-- 14. DETALLES DE ASIGNACIÓN
-- detasig_id (seq) | asignacion_asig_id | detasig_hora_ini | detasig_hora_fin | detasig_fecha
-- Horarios coherentes con jornada de la ficha:
--   Mañana: 08:00-12:00 | Tarde: 14:00-18:00 | Noche: 18:00-22:00 | Mixta: 07:00-17:00
-- Fechas dentro del rango asig_fecha_ini / asig_fecha_fin
-- ============================================================

-- Asig 1001 (Mañana) semanas 16-20 Mar y 23-27 Mar
INSERT INTO public.detallexasignacion (asignacion_asig_id, detasig_hora_ini, detasig_hora_fin, detasig_fecha) VALUES
(1001,'08:00','12:00','2026-03-16'),(1001,'08:00','12:00','2026-03-17'),
(1001,'08:00','12:00','2026-03-18'),(1001,'08:00','12:00','2026-03-19'),
(1001,'08:00','12:00','2026-03-20'),(1001,'08:00','12:00','2026-03-23'),
(1001,'08:00','12:00','2026-03-24'),(1001,'08:00','12:00','2026-03-25'),
(1001,'08:00','12:00','2026-03-26'),(1001,'08:00','12:00','2026-03-27');

-- Asig 1002 (Tarde)
INSERT INTO public.detallexasignacion (asignacion_asig_id, detasig_hora_ini, detasig_hora_fin, detasig_fecha) VALUES
(1002,'14:00','18:00','2026-03-16'),(1002,'14:00','18:00','2026-03-17'),
(1002,'14:00','18:00','2026-03-18'),(1002,'14:00','18:00','2026-03-19'),
(1002,'14:00','18:00','2026-03-20'),(1002,'14:00','18:00','2026-03-23'),
(1002,'14:00','18:00','2026-03-24'),(1002,'14:00','18:00','2026-03-25');

-- Asig 1003 (Tarde)
INSERT INTO public.detallexasignacion (asignacion_asig_id, detasig_hora_ini, detasig_hora_fin, detasig_fecha) VALUES
(1003,'14:00','18:00','2026-03-16'),(1003,'14:00','18:00','2026-03-17'),
(1003,'14:00','18:00','2026-03-18'),(1003,'14:00','18:00','2026-03-19'),
(1003,'14:00','18:00','2026-03-20'),(1003,'14:00','18:00','2026-03-23'),
(1003,'14:00','18:00','2026-03-24'),(1003,'14:00','18:00','2026-03-25'),
(1003,'14:00','18:00','2026-03-26');

-- Asig 1004 (Mañana)
INSERT INTO public.detallexasignacion (asignacion_asig_id, detasig_hora_ini, detasig_hora_fin, detasig_fecha) VALUES
(1004,'08:00','12:00','2026-03-16'),(1004,'08:00','12:00','2026-03-17'),
(1004,'08:00','12:00','2026-03-18'),(1004,'08:00','12:00','2026-03-19'),
(1004,'08:00','12:00','2026-03-20'),(1004,'08:00','12:00','2026-03-23'),
(1004,'08:00','12:00','2026-03-24'),(1004,'08:00','12:00','2026-03-25');

-- Asig 1005 (Mañana)
INSERT INTO public.detallexasignacion (asignacion_asig_id, detasig_hora_ini, detasig_hora_fin, detasig_fecha) VALUES
(1005,'08:00','12:00','2026-03-16'),(1005,'08:00','12:00','2026-03-17'),
(1005,'08:00','12:00','2026-03-18'),(1005,'08:00','12:00','2026-03-19'),
(1005,'08:00','12:00','2026-03-20'),(1005,'08:00','12:00','2026-03-23'),
(1005,'08:00','12:00','2026-03-24'),(1005,'08:00','12:00','2026-03-25'),
(1005,'08:00','12:00','2026-03-26'),(1005,'08:00','12:00','2026-03-27');

-- Asig 1006 (Tarde)
INSERT INTO public.detallexasignacion (asignacion_asig_id, detasig_hora_ini, detasig_hora_fin, detasig_fecha) VALUES
(1006,'14:00','18:00','2026-03-16'),(1006,'14:00','18:00','2026-03-17'),
(1006,'14:00','18:00','2026-03-18'),(1006,'14:00','18:00','2026-03-19'),
(1006,'14:00','18:00','2026-03-20'),(1006,'14:00','18:00','2026-03-23'),
(1006,'14:00','18:00','2026-03-24');

-- Asig 1007 (Mañana) CEDRUM
INSERT INTO public.detallexasignacion (asignacion_asig_id, detasig_hora_ini, detasig_hora_fin, detasig_fecha) VALUES
(1007,'08:00','12:00','2026-03-16'),(1007,'08:00','12:00','2026-03-17'),
(1007,'08:00','12:00','2026-03-18'),(1007,'08:00','12:00','2026-03-19'),
(1007,'08:00','12:00','2026-03-20'),(1007,'08:00','12:00','2026-03-23'),
(1007,'08:00','12:00','2026-03-24'),(1007,'08:00','12:00','2026-03-25'),
(1007,'08:00','12:00','2026-03-26');

-- Asig 1008 (Tarde)
INSERT INTO public.detallexasignacion (asignacion_asig_id, detasig_hora_ini, detasig_hora_fin, detasig_fecha) VALUES
(1008,'14:00','18:00','2026-03-16'),(1008,'14:00','18:00','2026-03-17'),
(1008,'14:00','18:00','2026-03-18'),(1008,'14:00','18:00','2026-03-19'),
(1008,'14:00','18:00','2026-03-20'),(1008,'14:00','18:00','2026-03-23'),
(1008,'14:00','18:00','2026-03-24'),(1008,'14:00','18:00','2026-03-25');

-- Asig 1009 (Tarde)
INSERT INTO public.detallexasignacion (asignacion_asig_id, detasig_hora_ini, detasig_hora_fin, detasig_fecha) VALUES
(1009,'14:00','18:00','2026-03-16'),(1009,'14:00','18:00','2026-03-17'),
(1009,'14:00','18:00','2026-03-18'),(1009,'14:00','18:00','2026-03-19'),
(1009,'14:00','18:00','2026-03-20'),(1009,'14:00','18:00','2026-03-23'),
(1009,'14:00','18:00','2026-03-24'),(1009,'14:00','18:00','2026-03-25'),
(1009,'14:00','18:00','2026-03-26'),(1009,'14:00','18:00','2026-03-27');

-- Asig 1010 (Mañana)
INSERT INTO public.detallexasignacion (asignacion_asig_id, detasig_hora_ini, detasig_hora_fin, detasig_fecha) VALUES
(1010,'08:00','12:00','2026-03-16'),(1010,'08:00','12:00','2026-03-17'),
(1010,'08:00','12:00','2026-03-18'),(1010,'08:00','12:00','2026-03-19'),
(1010,'08:00','12:00','2026-03-20'),(1010,'08:00','12:00','2026-03-23'),
(1010,'08:00','12:00','2026-03-24');

-- Asig 1011 (Mañana)
INSERT INTO public.detallexasignacion (asignacion_asig_id, detasig_hora_ini, detasig_hora_fin, detasig_fecha) VALUES
(1011,'08:00','12:00','2026-03-16'),(1011,'08:00','12:00','2026-03-17'),
(1011,'08:00','12:00','2026-03-18'),(1011,'08:00','12:00','2026-03-19'),
(1011,'08:00','12:00','2026-03-20'),(1011,'08:00','12:00','2026-03-23'),
(1011,'08:00','12:00','2026-03-24'),(1011,'08:00','12:00','2026-03-25');

-- Asig 1012 (Tarde)
INSERT INTO public.detallexasignacion (asignacion_asig_id, detasig_hora_ini, detasig_hora_fin, detasig_fecha) VALUES
(1012,'14:00','18:00','2026-03-16'),(1012,'14:00','18:00','2026-03-17'),
(1012,'14:00','18:00','2026-03-18'),(1012,'14:00','18:00','2026-03-19'),
(1012,'14:00','18:00','2026-03-20'),(1012,'14:00','18:00','2026-03-23'),
(1012,'14:00','18:00','2026-03-24'),(1012,'14:00','18:00','2026-03-25'),
(1012,'14:00','18:00','2026-03-26');

-- Ajuste secuencia detallexasignacion
SELECT setval('public.detallexasignacion_detasig_id_seq',
    (SELECT MAX(detasig_id) FROM public.detallexasignacion));

COMMIT;
-- FIN DEL SCRIPT
