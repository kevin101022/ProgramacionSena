-- ============================================================
-- SCRIPT DE POBLACIÓN MASIVA - CORREGIDO (CIES=1 / CEDRUM=2)
-- ============================================================

-- 1. CENTROS DE FORMACIÓN (Bcrypt: sena123 = $2y$10$EUhrJ4farpC/u5ZaBpdjJ.FwogUXW.nheImV5DAKePUSpZS9IaHMq)
INSERT INTO public.centro_formacion (cent_id, cent_nombre, cent_correo, cent_password)
VALUES 
(1, 'CIES - Centro de la Industria, la Empresa y los Servicios', 'cies@sena.edu.co', '$2y$10$EUhrJ4farpC/u5ZaBpdjJ.FwogUXW.nheImV5DAKePUSpZS9IaHMq'),
(2, 'CEDRUM - Centro de Desarrollo Rural y Minero', 'cedrum@sena.edu.co', '$2y$10$EUhrJ4farpC/u5ZaBpdjJ.FwogUXW.nheImV5DAKePUSpZS9IaHMq')
ON CONFLICT (cent_id) DO UPDATE SET 
    cent_nombre = EXCLUDED.cent_nombre,
    cent_correo = EXCLUDED.cent_correo,
    cent_password = EXCLUDED.cent_password;

-- 2. SEDES (Globales - 4 para cada centro)
INSERT INTO public.sede (sede_id, sede_nombre, centro_formacion_cent_id)
VALUES 
-- CIES (1)
(4, 'Sede Principal Cúcuta (CIES)', 1),
(5, 'Sede Los Patios (CIES)', 1),
(6, 'Sede Atalaya (CIES)', 1),
(8, 'Sede El Zulia (CIES)', 1),
-- CEDRUM (2)
(1, 'Sede Villa del Rosario (CEDRUM)', 2),
(2, 'Sede Tibú (CEDRUM)', 2),
(3, 'Sede Pamplona (CEDRUM)', 2),
(7, 'Sede Chinácota (CEDRUM)', 2)
ON CONFLICT (sede_id) DO UPDATE SET sede_nombre = EXCLUDED.sede_nombre, centro_formacion_cent_id = EXCLUDED.centro_formacion_cent_id;

-- 3. AMBIENTES (Globales)
INSERT INTO public.ambiente (amb_id, amb_nombre, tipo_ambiente, sede_sede_id)
VALUES 
-- CIES SEDES (4,5,6,8)
('C301', 'Laboratorio de Software 301', 'Informática', 4),
('C302', 'Aula de Diseño 302', 'Creativo', 4),
('A101', 'Taller de Electrónica 101', 'Especializado', 6),
('Z501', 'Aula de Logística 501', 'Convencional', 8),
-- CEDRUM SEDES (1,2,3,7)
('V101', 'Aula de Sistemas 101', 'Informática', 1),
('V102', 'Aula de Gestión 102', 'Convencional', 1),
('T201', 'Taller de Minería 201', 'Especializado', 2),
('P301', 'Aula Polivalente 301', 'Convencional', 3)
ON CONFLICT (amb_id) DO UPDATE SET amb_nombre = EXCLUDED.amb_nombre, sede_sede_id = EXCLUDED.sede_sede_id;

-- 4. TÍTULOS DE PROGRAMA
INSERT INTO public.titulo_programa (titpro_id, titpro_nombre, centro_formacion_cent_id)
VALUES 
(4, 'Tecnólogo', 1), (5, 'Técnico', 1), (6, 'Operario', 1), (8, 'Profundización', 1),
(1, 'Tecnólogo', 2), (2, 'Técnico', 2), (3, 'Auxiliar', 2), (7, 'Operario', 2)
ON CONFLICT (titpro_id) DO UPDATE SET titpro_nombre = EXCLUDED.titpro_nombre, centro_formacion_cent_id = EXCLUDED.centro_formacion_cent_id;

-- 5. PROGRAMAS
INSERT INTO public.programa (prog_codigo, prog_denominacion, tit_programa_titpro_id, prog_tipo, centro_formacion_cent_id)
VALUES 
-- CIES (1)
(228118, 'Desarrollo de Videojuegos', 4, 'Titulada', 1),
(133101, 'Asistencia Administrativa', 5, 'Titulada', 1),
(225201, 'Animación 3D', 4, 'Titulada', 1),
(228120, 'Inteligencia Artificial', 4, 'Titulada', 1),
-- CEDRUM (2)
(228106, 'Análisis y Desarrollo de Software', 1, 'Titulada', 2),
(123101, 'Gestión Contable y Financiera', 2, 'Titulada', 2),
(839301, 'Mantenimiento de Motores Diesel', 2, 'Titulada', 2),
(228107, 'Programación de Software', 2, 'Titulada', 2)
ON CONFLICT (prog_codigo) DO UPDATE SET prog_denominacion = EXCLUDED.prog_denominacion, centro_formacion_cent_id = EXCLUDED.centro_formacion_cent_id;

-- 6. USUARIOS COORDINADORES
INSERT INTO public.usuario_coordinador (numero_documento, coord_nombre_coordinador, coord_correo, coord_password, estado, centro_formacion_id)
VALUES 
-- CIES (1)
(1090000001, 'Maria Coordinadora CIES', 'coord_cies1@sena.edu.co', '$2y$10$KLQX2ykMc91jvhYx.vxjjeVYPLcdA5ZazJjqxm65fezmx2AjR28tW', 1, 1),
(1090000002, 'Jose Coordinador CIES', 'coord_cies2@sena.edu.co', '$2y$10$cbHtvhxb4cbIuWBBb/A/PeP9fI.92qkrswPX2ZeE.dNH9RUZ3gb32', 1, 1),
(1090000003, 'Elena Coordinadora CIES', 'coord_cies3@sena.edu.co', '$2y$10$cbHtvhxb4cbIuWBBb/A/PeP9fI.92qkrswPX2ZeE.dNH9RUZ3gb32', 1, 1),
(1090000004, 'Carlos Coordinador CIES', 'coord_cies4@sena.edu.co', '$2y$10$cbHtvhxb4cbIuWBBb/A/PeP9fI.92qkrswPX2ZeE.dNH9RUZ3gb32', 1, 1),
-- CEDRUM (2)
(1090123456, 'Juan Coordinador CEDRUM', 'coord_cedrum1@sena.edu.co', '$2y$10$KLQX2ykMc91jvhYx.vxjjeVYPLcdA5ZazJjqxm65fezmx2AjR28tW', 1, 2),
(1090123457, 'Ana Coordinadora CEDRUM', 'coord_cedrum2@sena.edu.co', '$2y$10$cbHtvhxb4cbIuWBBb/A/PeP9fI.92qkrswPX2ZeE.dNH9RUZ3gb32', 1, 2),
(1090123458, 'Luis Coordinador CEDRUM', 'coord_cedrum3@sena.edu.co', '$2y$10$cbHtvhxb4cbIuWBBb/A/PeP9fI.92qkrswPX2ZeE.dNH9RUZ3gb32', 1, 2),
(1090123459, 'Rosa Coordinadora CEDRUM', 'coord_cedrum4@sena.edu.co', '$2y$10$cbHtvhxb4cbIuWBBb/A/PeP9fI.92qkrswPX2ZeE.dNH9RUZ3gb32', 1, 2)
ON CONFLICT (numero_documento) DO UPDATE SET coord_password = EXCLUDED.coord_password, centro_formacion_id = EXCLUDED.centro_formacion_id;

-- 7. COORDINACIONES
INSERT INTO public.coordinacion (coord_id, coord_descripcion, centro_formacion_cent_id, estado, coordinador_actual)
VALUES 
-- CIES (1)
(2, 'Coordinación TIC CIES', 1, 1, 1090000001),
(4, 'Coordinación Servicios', 1, 1, 1090000002),
(6, 'Coordinación Industria', 1, 1, 1090000003),
(8, 'Coordinación Empresa', 1, 1, 1090000004),
-- CEDRUM (2)
(1, 'Coordinación Académica - TIC', 2, 1, 1090123456),
(3, 'Coordinación Rural', 2, 1, 1090123457),
(5, 'Coordinación Minería', 2, 1, 1090123458),
(7, 'Coordinación Bilingüismo', 2, 1, 1090123459)
ON CONFLICT (coord_id) DO UPDATE SET coord_descripcion = EXCLUDED.coord_descripcion, centro_formacion_cent_id = EXCLUDED.centro_formacion_cent_id;

-- 8. INSTRUCTORES
INSERT INTO public.instructor (numero_documento, inst_nombres, inst_apellidos, inst_correo, inst_telefono, centro_formacion_cent_id, inst_password, estado)
VALUES 
-- CIES (1)
(1090999991, 'Ana', 'Gómez', 'inst_cies1@sena.edu.co', 3209876541, 1, '$2y$10$KLQX2ykMc91jvhYx.vxjjeVYPLcdA5ZazJjqxm65fezmx2AjR28tW', 1),
(1090999992, 'Ricardo', 'Torres', 'inst_cies2@sena.edu.co', 3209876542, 1, '$2y$10$cbHtvhxb4cbIuWBBb/A/PeP9fI.92qkrswPX2ZeE.dNH9RUZ3gb32', 1),
(1090999993, 'Fabio', 'Rojas', 'inst_cies3@sena.edu.co', 3209876543, 1, '$2y$10$cbHtvhxb4cbIuWBBb/A/PeP9fI.92qkrswPX2ZeE.dNH9RUZ3gb32', 1),
(1090999994, 'Olga', 'Ríos', 'inst_cies4@sena.edu.co', 3209876544, 1, '$2y$10$cbHtvhxb4cbIuWBBb/A/PeP9fI.92qkrswPX2ZeE.dNH9RUZ3gb32', 1),
(1090999995, 'Diego', 'Cano', 'inst_cies5@sena.edu.co', 3209876545, 1, '$2y$10$cbHtvhxb4cbIuWBBb/A/PeP9fI.92qkrswPX2ZeE.dNH9RUZ3gb32', 1),
(1090999996, 'Sara', 'Mejía', 'inst_cies6@sena.edu.co', 3209876546, 1, '$2y$10$cbHtvhxb4cbIuWBBb/A/PeP9fI.92qkrswPX2ZeE.dNH9RUZ3gb32', 1),
(1090999997, 'Enrique', 'Bello', 'inst_cies7@sena.edu.co', 3209876547, 1, '$2y$10$cbHtvhxb4cbIuWBBb/A/PeP9fI.92qkrswPX2ZeE.dNH9RUZ3gb32', 1),
(1090999998, 'Inés', 'Pardo', 'inst_cies8@sena.edu.co', 3209876548, 1, '$2y$10$cbHtvhxb4cbIuWBBb/A/PeP9fI.92qkrswPX2ZeE.dNH9RUZ3gb32', 1),
-- CEDRUM (2)
(1090654321, 'Pedro', 'Pérez', 'inst_cedrum1@sena.edu.co', 3101234561, 2, '$2y$10$KLQX2ykMc91jvhYx.vxjjeVYPLcdA5ZazJjqxm65fezmx2AjR28tW', 1),
(1090654322, 'Sofia', 'Díaz', 'inst_cedrum2@sena.edu.co', 3101234562, 2, '$2y$10$cbHtvhxb4cbIuWBBb/A/PeP9fI.92qkrswPX2ZeE.dNH9RUZ3gb32', 1),
(1090654323, 'Jorge', 'Ruiz', 'inst_cedrum3@sena.edu.co', 3101234563, 2, '$2y$10$cbHtvhxb4cbIuWBBb/A/PeP9fI.92qkrswPX2ZeE.dNH9RUZ3gb32', 1),
(1090654324, 'Marta', 'León', 'inst_cedrum4@sena.edu.co', 3101234564, 2, '$2y$10$cbHtvhxb4cbIuWBBb/A/PeP9fI.92qkrswPX2ZeE.dNH9RUZ3gb32', 1),
(1090654325, 'Victor', 'Sosa', 'inst_cedrum5@sena.edu.co', 3101234565, 2, '$2y$10$cbHtvhxb4cbIuWBBb/A/PeP9fI.92qkrswPX2ZeE.dNH9RUZ3gb32', 1),
(1090654326, 'Carmen', 'Vega', 'inst_cedrum6@sena.edu.co', 3101234566, 2, '$2y$10$cbHtvhxb4cbIuWBBb/A/PeP9fI.92qkrswPX2ZeE.dNH9RUZ3gb32', 1),
(1090654327, 'Hugo', 'Mora', 'inst_cedrum7@sena.edu.co', 3101234567, 2, '$2y$10$cbHtvhxb4cbIuWBBb/A/PeP9fI.92qkrswPX2ZeE.dNH9RUZ3gb32', 1),
(1090654328, 'Beatriz', 'Luna', 'inst_cedrum8@sena.edu.co', 3101234568, 2, '$2y$10$cbHtvhxb4cbIuWBBb/A/PeP9fI.92qkrswPX2ZeE.dNH9RUZ3gb32', 1)
ON CONFLICT (numero_documento) DO UPDATE SET inst_password = EXCLUDED.inst_password, centro_formacion_cent_id = EXCLUDED.centro_formacion_cent_id;

-- 9. COMPETENCIAS (Globales)
INSERT INTO public.competencia (comp_id, comp_nombre_corto, comp_horas, comp_nombre_unidad_competencia, centro_formacion_cent_id)
VALUES 
(220501100, 'Videojuegos 3D', 300, 'Crear entornos y personajes 3D', 1),
(220501001, 'Python Avanzado', 120, 'Programación con Python', 1),
(220501004, 'UX/UI Design', 100, 'Diseñar interfaces de usuario', 1),
(220501093, 'Desarrollo SW', 400, 'Desarrollar componentes según diseño', 2),
(220501094, 'Pruebas SW', 200, 'Validar software según requerimientos', 2),
(220501095, 'Bases de Datos', 300, 'Gestionar BD según parámetros', 2),
(220501002, 'Seguridad Info', 180, 'Proteger sistemas de información', 2),
(220501003, 'Redes Cisco', 240, 'Configurar dispositivos de red', 2)
ON CONFLICT (comp_id) DO UPDATE SET comp_nombre_corto = EXCLUDED.comp_nombre_corto, centro_formacion_cent_id = EXCLUDED.centro_formacion_cent_id;

-- 10. COMPETENCIAS POR PROGRAMA
INSERT INTO public.competxprograma (programa_prog_id, competencia_comp_id)
VALUES 
(228118, 220501100), (228118, 220501004), (228120, 220501001),
(228106, 220501093), (228106, 220501094), (228106, 220501095)
ON CONFLICT DO NOTHING;

-- 11. FICHAS
INSERT INTO public.ficha (fich_id, programa_prog_id, instructor_inst_id_lider, fich_jornada, coordinacion_coord_id, fich_fecha_ini_lectiva, fich_fecha_fin_lectiva)
VALUES 
-- CIES (1)
(2670001, 228118, 1090999991, 'Diurna', 2, '2025-02-01', '2027-01-31'),
(2670002, 133101, 1090999992, 'Mixta', 4, '2025-02-01', '2027-01-31'),
(2670003, 225201, 1090999993, 'Nocturna', 6, '2025-02-01', '2027-01-31'),
(2670004, 228120, 1090999994, 'Diurna', 8, '2025-02-01', '2027-01-31'),
-- CEDRUM (2)
(2670553, 228106, 1090654321, 'Diurna', 1, '2025-01-01', '2026-12-31'),
(2670554, 123101, 1090654322, 'Mixta', 3, '2025-01-01', '2026-12-31'),
(2670555, 839301, 1090654323, 'Nocturna', 5, '2025-01-01', '2026-12-31'),
(2670556, 228107, 1090654324, 'Diurna', 7, '2025-01-01', '2026-12-31')
ON CONFLICT (fich_id) DO UPDATE SET fich_jornada = EXCLUDED.fich_jornada, coordinacion_coord_id = EXCLUDED.coordinacion_coord_id;

-- 12. HABILITACIONES (INSTRU_COMPETENCIA)
INSERT INTO public.instru_competencia (INSTRUCTOR_inst_id, COMPETxPROGRAMA_PROGRAMA_prog_id, COMPETxPROGRAMA_COMPETENCIA_comp_id, inscomp_vigencia)
VALUES 
-- CIES (1) - Programa 228118
(1090999991, 228118, 220501100, '2026-12-31'), (1090999991, 228118, 220501004, '2026-12-31'),
(1090999992, 228118, 220501100, '2026-12-31'), (1090999993, 228118, 220501004, '2026-12-31'),
-- CIES (1) - Programa 228120
(1090999994, 228120, 220501001, '2026-12-31'), (1090999995, 228120, 220501001, '2026-12-31'),
(1090999996, 228118, 220501100, '2026-12-31'), (1090999997, 228118, 220501004, '2026-12-31'),
(1090999998, 228120, 220501001, '2026-12-31'),
-- CEDRUM (2) - Programa 228106
(1090654321, 228106, 220501093, '2026-12-31'), (1090654321, 228106, 220501094, '2026-12-31'), (1090654321, 228106, 220501095, '2026-12-31'),
(1090654322, 228106, 220501093, '2026-12-31'), (1090654323, 228106, 220501094, '2026-12-31'), (1090654324, 228106, 220501095, '2026-12-31'),
(1090654325, 228106, 220501093, '2026-12-31'), (1090654326, 228106, 220501094, '2026-12-31'), (1090654327, 228106, 220501095, '2026-12-31'),
(1090654328, 228106, 220501093, '2026-12-31')
ON CONFLICT DO NOTHING;
