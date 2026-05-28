-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 27-05-2026 a las 15:47:27
-- Versión del servidor: 11.8.6-MariaDB-log
-- Versión de PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `u254004779_progSena`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividad_proyecto`
--

CREATE TABLE `actividad_proyecto` (
  `act_id` int(11) NOT NULL,
  `act_nombre` varchar(255) NOT NULL,
  `fase_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ambiente`
--

CREATE TABLE `ambiente` (
  `amb_id` varchar(50) NOT NULL,
  `amb_nombre` varchar(255) DEFAULT NULL,
  `tipo_ambiente` varchar(100) NOT NULL DEFAULT 'Convencional',
  `sede_sede_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ambiente`
--

INSERT INTO `ambiente` (`amb_id`, `amb_nombre`, `tipo_ambiente`, `sede_sede_id`) VALUES
('1', 'CANCHA INTERNA INDUSTRIA', 'Convencional', 1),
('CUC-AP1', 'Salón Comunal Alto Pamplonita', 'Convencional', 4),
('EF-101', 'ELECTRÓNICA MODERNA', 'Especializado', 3),
('EF-102', 'ELECTRÓNICA INDUSTRIAL', 'Especializado', 3),
('EF-203', 'ELECTRÓNICA', 'Especializado', 3),
('EF-204', 'SIG', 'Especializado', 3),
('EF-205', 'TELECOMUNICACIONES', 'Especializado', 3),
('GAB-1', 'Salón Comunal La Gabarra', 'Convencional', 4),
('GAB-2', 'Salón Comunal Pueblo Nuevo La Gabarra', 'Convencional', 4),
('IN-101A', 'AUTOMOTRIZ A', 'Especializado', 1),
('IN-101B', 'AUTOMOTRIZ B', 'Especializado', 1),
('IN-102A', 'MECÁNICA DIESEL', 'Especializado', 1),
('IN-102B', 'MECÁNICA DE MOTOS', 'Especializado', 1),
('IN-104', 'AMATROL', 'Especializado', 1),
('IN-105', 'REFRIGERACIÓN', 'Especializado', 1),
('IN-106A', 'FESTO A', 'Especializado', 1),
('IN-106B', 'FESTO B', 'Especializado', 1),
('IN-107', 'NEUMÁTICA BOSCH', 'Especializado', 1),
('IN-108', 'REDES ELÉCTRICAS', 'Especializado', 1),
('IN-109A', 'INSTALACIONES ELÉCTRICAS A', 'Especializado', 1),
('IN-109B', 'INSTALACIONES ELÉCTRICAS B', 'Especializado', 1),
('IN-110', 'TELECOMUNICACIONES', 'Especializado', 1),
('IN-111', 'CNC A', 'Especializado', 1),
('IN-112', 'CNC B', 'Especializado', 1),
('IN-114', 'SOLDADURA', 'Especializado', 1),
('IN-115', 'TIC', 'Convencional', 1),
('IN-118', 'CONSTRUCCIÓN', 'Especializado', 1),
('IN-119', 'TELECOMUNICACIONES', 'Especializado', 1),
('IN-121', 'SIG', 'Especializado', 1),
('IN-123', 'LABORATORIO DE MATERIALES', 'Especializado', 1),
('IN-201', 'SIG', 'Especializado', 1),
('IN-202', 'CONSTRUCCIÓN', 'Especializado', 1),
('IN-203', 'TOPOGRAFÍA A', 'Especializado', 1),
('IN-204', 'TOPOGRAFÍA B', 'Especializado', 1),
('IN-205', 'CONVENCIONAL', 'Convencional', 1),
('IN-206', 'PINTURA', 'Especializado', 1),
('IN-208', 'GAS', 'Especializado', 1),
('IN-212', 'GERFOR', 'Especializado', 1),
('IN-213', 'GERFOR', 'Especializado', 1),
('IN-MZ1', 'MEZANINE 1', 'Convencional', 1),
('IN-MZ2', 'MEZANINE 2', 'Convencional', 1),
('LAB-1', 'Punto Vive Digital Labateca', 'Convencional', 4),
('LP-101', 'INSTALACIONES ELÉCTRICAS', 'Especializado', 5),
('LP-102', 'MAQUINARIA PESADA', 'Especializado', 5),
('PTS-1', 'Punto Vive Digital Puerto Santander', 'Convencional', 4),
('SAR-1', 'Punto Vive Digital Sardinata', 'Convencional', 4),
('TAR-1', 'Salón Comunal El Tarra', 'Convencional', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignacion`
--

CREATE TABLE `asignacion` (
  `asig_id` int(11) NOT NULL,
  `instructor_inst_id` bigint(20) DEFAULT NULL,
  `asig_fecha_ini` date NOT NULL,
  `asig_fecha_fin` date NOT NULL,
  `ficha_fich_id` int(11) DEFAULT NULL,
  `ambiente_amb_id` varchar(50) DEFAULT NULL,
  `competencia_comp_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `asignacion`
--

INSERT INTO `asignacion` (`asig_id`, `instructor_inst_id`, `asig_fecha_ini`, `asig_fecha_fin`, `ficha_fich_id`, `ambiente_amb_id`, `competencia_comp_id`) VALUES
(3, 1093782895, '2026-06-01', '2026-06-05', 3314175, 'IN-101A', 38558),
(4, 1093782895, '2026-06-08', '2026-06-13', 3314188, 'IN-206', 38558),
(5, 88160803, '2026-06-01', '2026-06-06', 3314188, '1', 37800);

--
-- Disparadores `asignacion`
--
DELIMITER $$
CREATE TRIGGER `trg_asignacion_audit_delete` AFTER DELETE ON `asignacion` FOR EACH ROW BEGIN
    INSERT INTO auditoria_asignacion (
        instructor_inst_id, asig_fecha_ini, asig_fecha_fin, ficha_fich_id, 
        ambiente_amb_id, competencia_comp_id, asig_id, tipo_accion, 
        documento_usuario_accion, correo_usuario, nombre_usuario_accion
    )
    VALUES (
        OLD.instructor_inst_id, OLD.asig_fecha_ini, OLD.asig_fecha_fin, OLD.ficha_fich_id, 
        OLD.ambiente_amb_id, OLD.competencia_comp_id, OLD.asig_id, 'DELETE', 
        0, 'sistema@admin.com', 'Sistema'
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_asignacion_audit_insert` AFTER INSERT ON `asignacion` FOR EACH ROW BEGIN
    INSERT INTO auditoria_asignacion (
        instructor_inst_id, asig_fecha_ini, asig_fecha_fin, ficha_fich_id, 
        ambiente_amb_id, competencia_comp_id, asig_id, tipo_accion, 
        documento_usuario_accion, correo_usuario, nombre_usuario_accion
    )
    VALUES (
        NEW.instructor_inst_id, NEW.asig_fecha_ini, NEW.asig_fecha_fin, NEW.ficha_fich_id, 
        NEW.ambiente_amb_id, NEW.competencia_comp_id, NEW.asig_id, 'INSERT', 
        0, 'sistema@admin.com', 'Sistema'
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_asignacion_audit_update` AFTER UPDATE ON `asignacion` FOR EACH ROW BEGIN
    INSERT INTO auditoria_asignacion (
        instructor_inst_id, asig_fecha_ini, asig_fecha_fin, ficha_fich_id, 
        ambiente_amb_id, competencia_comp_id, asig_id, tipo_accion, 
        documento_usuario_accion, correo_usuario, nombre_usuario_accion
    )
    VALUES (
        NEW.instructor_inst_id, NEW.asig_fecha_ini, NEW.asig_fecha_fin, NEW.ficha_fich_id, 
        NEW.ambiente_amb_id, NEW.competencia_comp_id, NEW.asig_id, 'UPDATE', 
        0, 'sistema@admin.com', 'Sistema'
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_asignacion`
--

CREATE TABLE `auditoria_asignacion` (
  `id_auditoria` int(11) NOT NULL,
  `instructor_inst_id` bigint(20) NOT NULL,
  `asig_fecha_ini` date NOT NULL,
  `asig_fecha_fin` date NOT NULL,
  `ficha_fich_id` int(11) NOT NULL,
  `ambiente_amb_id` varchar(50) NOT NULL,
  `competencia_comp_id` int(11) NOT NULL,
  `asig_id` int(11) NOT NULL,
  `fecha_hora` timestamp NULL DEFAULT current_timestamp(),
  `documento_usuario_accion` bigint(20) NOT NULL,
  `correo_usuario` varchar(255) NOT NULL,
  `tipo_accion` varchar(50) NOT NULL,
  `nombre_usuario_accion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `auditoria_asignacion`
--

INSERT INTO `auditoria_asignacion` (`id_auditoria`, `instructor_inst_id`, `asig_fecha_ini`, `asig_fecha_fin`, `ficha_fich_id`, `ambiente_amb_id`, `competencia_comp_id`, `asig_id`, `fecha_hora`, `documento_usuario_accion`, `correo_usuario`, `tipo_accion`, `nombre_usuario_accion`) VALUES
(1, 1093782895, '2026-06-01', '2026-06-05', 3314175, 'IN-101A', 38558, 1, '2026-05-26 23:16:31', 0, 'sistema@admin.com', 'INSERT', 'Sistema'),
(2, 1093782895, '2026-06-01', '2026-06-05', 3314175, 'IN-101A', 38558, 1, '2026-05-27 00:48:00', 0, 'sistema@admin.com', 'DELETE', 'Sistema'),
(3, 1093782895, '2026-05-27', '2026-05-30', 3314175, 'IN-101A', 38558, 2, '2026-05-27 00:48:47', 0, 'sistema@admin.com', 'INSERT', 'Sistema'),
(4, 535348, '2026-05-27', '2026-05-30', 3314175, 'IN-101B', 37714, 3, '2026-05-27 01:12:18', 0, 'sistema@admin.com', 'INSERT', 'Sistema'),
(5, 1090397641, '2026-07-01', '2026-07-11', 3314175, 'IN-118', 37801, 4, '2026-05-27 01:50:16', 0, 'sistema@admin.com', 'INSERT', 'Sistema'),
(6, 1093782895, '2026-05-27', '2026-05-30', 3314175, 'IN-101A', 38558, 2, '2026-05-27 01:51:53', 0, 'sistema@admin.com', 'DELETE', 'Sistema'),
(7, 535348, '2026-05-27', '2026-05-30', 3314175, 'IN-101B', 37714, 3, '2026-05-27 01:51:55', 0, 'sistema@admin.com', 'DELETE', 'Sistema'),
(8, 1090397641, '2026-07-01', '2026-07-11', 3314175, 'IN-118', 37801, 4, '2026-05-27 01:51:57', 0, 'sistema@admin.com', 'DELETE', 'Sistema'),
(9, 1093782895, '2026-05-27', '2026-05-30', 3314175, 'IN-101A', 38558, 5, '2026-05-27 02:11:50', 0, 'sistema@admin.com', 'INSERT', 'Sistema'),
(10, 1090397641, '2026-06-01', '2026-06-06', 3314175, 'IN-101B', 37801, 6, '2026-05-27 03:16:23', 0, 'sistema@admin.com', 'INSERT', 'Sistema'),
(11, 37395403, '2026-06-08', '2026-06-26', 3314175, 'IN-104', 37714, 7, '2026-05-27 03:16:57', 0, 'sistema@admin.com', 'INSERT', 'Sistema'),
(12, 1093782895, '2026-05-27', '2026-05-30', 3314175, 'IN-101A', 38558, 5, '2026-05-27 03:34:08', 0, 'sistema@admin.com', 'DELETE', 'Sistema'),
(13, 1090397641, '2026-06-01', '2026-06-06', 3314175, 'IN-101B', 37801, 6, '2026-05-27 03:34:10', 0, 'sistema@admin.com', 'DELETE', 'Sistema'),
(14, 37395403, '2026-06-08', '2026-06-26', 3314175, 'IN-104', 37714, 7, '2026-05-27 03:34:11', 0, 'sistema@admin.com', 'DELETE', 'Sistema'),
(15, 1090397641, '2026-05-27', '2026-05-30', 3314175, 'IN-104', 37801, 1, '2026-05-27 03:35:30', 0, 'sistema@admin.com', 'INSERT', 'Sistema'),
(16, 1090397641, '2026-05-27', '2026-05-30', 3314175, 'IN-104', 37801, 1, '2026-05-27 03:35:37', 0, 'sistema@admin.com', 'DELETE', 'Sistema'),
(17, 1093782895, '2026-05-27', '2026-06-02', 3314175, 'IN-104', 38558, 1, '2026-05-27 12:13:39', 0, 'sistema@admin.com', 'INSERT', 'Sistema'),
(18, 1093782895, '2026-05-27', '2026-06-02', 3314175, 'IN-104', 38558, 1, '2026-05-27 12:13:51', 0, 'sistema@admin.com', 'DELETE', 'Sistema'),
(19, 1093782895, '2026-05-27', '2026-06-02', 3314175, 'IN-101A', 38558, 2, '2026-05-27 12:17:48', 0, 'sistema@admin.com', 'INSERT', 'Sistema'),
(20, 1093782895, '2026-05-27', '2026-06-02', 3314175, 'IN-101A', 38558, 2, '2026-05-27 14:05:45', 0, 'sistema@admin.com', 'DELETE', 'Sistema'),
(21, 1093782895, '2026-06-01', '2026-06-05', 3314175, 'IN-101A', 38558, 3, '2026-05-27 14:10:17', 0, 'sistema@admin.com', 'INSERT', 'Sistema'),
(22, 1093782895, '2026-06-08', '2026-06-13', 3314188, 'IN-206', 38558, 4, '2026-05-27 15:13:12', 0, 'sistema@admin.com', 'INSERT', 'Sistema'),
(23, 88160803, '2026-06-01', '2026-06-06', 3314188, '1', 37800, 5, '2026-05-27 15:38:59', 0, 'sistema@admin.com', 'INSERT', 'Sistema');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `centro_formacion`
--

CREATE TABLE `centro_formacion` (
  `cent_id` int(11) NOT NULL,
  `cent_nombre` varchar(255) NOT NULL,
  `cent_correo` varchar(255) DEFAULT NULL,
  `cent_password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `centro_formacion`
--

INSERT INTO `centro_formacion` (`cent_id`, `cent_nombre`, `cent_correo`, `cent_password`) VALUES
(9119, 'CEDRUM', 'centrocedrum@gmail.com', '$2y$10$Ih7QmY0c4p69X.KyRbCY1.2YtB2.lutI0g2k4TN4eYZrW/i/SJeoe'),
(9537, 'CIES', 'centrocies@gmail.com', '$2y$10$tZKxrV1uozFwqnJo1xB.v.6HYoXn6ClKbX0EFiJWueS.cXOW1OoMa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `competencia`
--

CREATE TABLE `competencia` (
  `comp_id` int(11) NOT NULL,
  `comp_nombre_corto` varchar(255) NOT NULL,
  `comp_horas` int(11) NOT NULL,
  `comp_nombre_unidad_competencia` varchar(255) NOT NULL,
  `centro_formacion_cent_id` int(11) DEFAULT NULL,
  `programa_prog_id` int(11) NOT NULL,
  `requisitos_academicos` text DEFAULT NULL,
  `experiencia_laboral` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `competencia`
--

INSERT INTO `competencia` (`comp_id`, `comp_nombre_corto`, `comp_horas`, `comp_nombre_unidad_competencia`, `centro_formacion_cent_id`, `programa_prog_id`, `requisitos_academicos`, `experiencia_laboral`) VALUES
(1, 'VARIAS', 5, 'PROMOVER LA INTERACCIÓN IDÓNEA CONSIGO MISMO, CON LOS DEMÁS Y CON LA NATURALEZA EN LOS CONTEXTOS LABORAL Y SOCIAL', NULL, 832333, NULL, NULL),
(1, 'VARIAS', 5, 'PROMOVER LA INTERACCIÓN IDÓNEA CONSIGO MISMO, CON LOS DEMÁS Y CON LA NATURALEZA EN LOS CONTEXTOS LABORAL Y SOCIAL', NULL, 832422, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 223206, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 223213, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 224201, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 224312, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 224315, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 224501, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 225224, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 225311, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 225314, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 226701, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 664212, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 821100, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 821202, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 821203, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 821307, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 821620, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 832102, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 832202, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 880, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 832303, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 880, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 832333, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 832402, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 880, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 832422, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 833100, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 833301, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 834258, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 836135, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 836136, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 836137, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 912, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 836138, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 432, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 836140, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 836600, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 837501, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 838100, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 838109, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 838200, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 838318, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 839317, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 845102, NULL, NULL),
(2, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', 864, 'RESULTADOS DE APRENDIZAJE ETAPA PRACTICA', NULL, 861100, NULL, NULL),
(3226, 'BILINGUISMO', 180, 'COMPRENDER TEXTOS EN INGLÉS EN FORMA ESCRITA Y AUDITIVA', NULL, 832333, NULL, NULL),
(3226, 'BILINGUISMO', 180, 'COMPRENDER TEXTOS EN INGLÉS EN FORMA ESCRITA Y AUDITIVA', NULL, 832422, NULL, NULL),
(3417, 'COMPETENCIA TECNICA', 48, 'Coordinar la atención a usuarios según proceso administrativo y estrategia de servicio', NULL, 821620, NULL, NULL),
(4020, 'COMPETENCIA TECNICA', 144, 'ANALIZAR CIRCUITOS ELÉCTRICOS DE ACUERDO CON EL MÉTODO REQUERIDO', NULL, 832422, NULL, NULL),
(4028, 'COMPETENCIA TECNICA', 288, 'ADMINISTRAR LA EJECUCIÓN DE LA CONSTRUCCIÓN E INSTALACIÓN DE REDES AÉREAS CUMPLIENDO PROCEDIMIENTOS ESTABLECIDOS', NULL, 821202, NULL, NULL),
(4032, 'COMPETENCIA TECNICA', 144, 'REPLANTEAR EL PROYECTO DE CONSTRUCCIÓN DE REDES AÉREAS DE DISTRIBUCIÓN DE ACUERDO CON LA INSPECCIÓN FÍSICA DEL LUGAR.', NULL, 821202, NULL, NULL),
(4419, 'COMPETENCIA TECNICA', 240, 'APLICAR ACABADOS ESPECIALES DE ACUERDO CON NORMAS, PLANOS Y ESPECIFICACIONES', NULL, 836600, NULL, NULL),
(4443, 'COMPETENCIA TECNICA', 96, 'CONSTRUIR FILTROS DE ACUERDO A NORMAS, PLANOS Y ESPECIFICACIONES', NULL, 861100, NULL, NULL),
(34122, 'COMPETENCIA TECNICA', 432, 'MONTAR EQUIPOS Y COMPONENTES PARA LA CONSTRUCCIÓN DE REDES DE DISTRIBUCIÓN CUMPLIENDO NORMAS Y PROCEDIMIENTOS.', NULL, 821202, NULL, NULL),
(34951, 'COMPETENCIA TECNICA', 220, 'DIRIGIR LA INSTALACIÓN Y PUESTA EN MARCHA DE LOS SISTEMAS DE GENERACIÓN DE ENERGÍA SOLAR FOTOVOLTAICA DE ACUERDO CON LAS ESPECIFICACIONES TÉCNICAS Y REQUERIMIENTOS ESTABLECIDOS.', NULL, 832333, NULL, NULL),
(34975, 'COMPETENCIA TECNICA', 220, 'DETERMINAR LAS ESPECIFICACIONES TÉCNICAS DE LOS SISTEMAS DE GENERACIÓN DE ENERGÍA SOLAR FOTOVOLTAICA DE ACUERDO CON EL ESTUDIO DE VIABILIDAD.', NULL, 832333, NULL, NULL),
(35011, 'COMPETENCIA TECNICA', 240, 'INTERPRETAR SISTEMAS POLIFÁSICOS DE ACUERDO CON APLICACIONES INDUSTRIALES', NULL, 821202, NULL, NULL),
(35011, 'COMPETENCIA TECNICA', 160, 'INTERPRETAR SISTEMAS POLIFÁSICOS DE ACUERDO CON APLICACIONES INDUSTRIALES', NULL, 832333, NULL, NULL),
(35494, 'COMPETENCIA TECNICA', 144, 'VERIFICAR PRUEBAS Y ENSAYOS EN LAS REDES DE DISTRIBUCIÓN DE ENERGÍA ELÉCTRICA', NULL, 821202, NULL, NULL),
(35511, 'COMPETENCIA TECNICA', 288, 'CONTROLAR LA EJECUCIÓN DE ACTIVIDADES DE MANTENIMIENTO DE REDES DE DISTRIBUCIÓN DE ENERGÍA ELÉCTRICA', NULL, 821202, NULL, NULL),
(35522, 'COMPETENCIA TECNICA', 192, 'GARANTIZAR EL CUMPLIMIENTO DE LAS NORMAS AMBIENTALES Y DE SEGURIDAD PREVIO Y DURANTE LA REALIZACIÓN DE LAS ÓRDENES DE TRABAJO', NULL, 821202, NULL, NULL),
(35531, 'COMPETENCIA TECNICA', 288, 'INSPECCIONAR LOS PARÁMETROS DE LAS INSTALACIONES Y/O EQUIPOS ELÉCTRICOS EN BAJA TENSIÓN.', NULL, 821203, NULL, NULL),
(35604, 'COMPETENCIA TECNICA', 60, 'CONTROLAR LOS RIESGOS DE TRABAJO EN ALTURAS DE ACUERDO A LA TAREA A REALIZAR, ACTIVIDAD ECONÓMICA Y NORMATIVA VIGENTE.', NULL, 832333, NULL, NULL),
(35604, 'COMPETENCIA TECNICA', 48, 'CONTROLAR LOS RIESGOS DE TRABAJO EN ALTURAS DE ACUERDO A LA TAREA A REALIZAR, ACTIVIDAD ECONÓMICA Y NORMATIVA VIGENTE.', NULL, 832422, NULL, NULL),
(35759, 'COMPETENCIA TECNICA', 240, 'DIGITALIZAR DIBUJO DE ACUERDO CON TÉCNICAS DE MODELACIÓN VIRTUAL Y MANUALES TÉCNICOS', NULL, 225224, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 223206, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 223213, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 224201, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 224312, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 224315, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 224501, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 225224, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 225311, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 225314, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 226701, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 664212, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 821100, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 821202, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 821203, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 821307, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 821620, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 832102, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 832202, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 832303, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 832402, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 833100, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 833301, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 834258, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 836135, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 836136, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 836137, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 836138, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 836140, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 836600, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 837501, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 838100, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 838109, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 838200, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 838318, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 839317, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 845102, NULL, NULL),
(36180, 'ETICA', 48, 'Enrique Low Murtra-Interactuar en el contexto productivo y social de acuerdo con principios  éticos para la construcción de una cultura de paz.', NULL, 861100, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 223206, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 223213, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 224201, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 224312, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 224315, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 224501, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 225224, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 225311, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 225314, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 226701, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 664212, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 821100, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 821202, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 821203, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 821307, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 821620, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 832102, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 832202, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 832303, NULL, NULL),
(36182, 'INDUCCION', 60, 'Resultado de Aprendizaje de la Inducción.', NULL, 832402, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 833100, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 833301, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 834258, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 836135, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 836136, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 836137, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 836138, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 836140, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 836600, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 837501, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 838100, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 838109, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 838200, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 838318, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 839317, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 845102, NULL, NULL),
(36182, 'INDUCCION', 48, 'Resultado de Aprendizaje de la Inducción.', NULL, 861100, NULL, NULL),
(36255, 'COMPETENCIA TECNICA', 144, 'REPARAR LA RED DE DISTRIBUCIÓN DE FIBRA ÓPTICA DE ACUERDO CON NORMAS TÉCNICAS Y PROCEDIMIENTOS DE LA EMPRESA.', NULL, 832422, NULL, NULL),
(36265, 'COMPETENCIA TECNICA', 192, 'TENDER LA RED DE FIBRA ÓPTICA DE ACUERDO CON NORMAS TÉCNICAS Y PROCEDIMIENTOS DE LA EMPRESA.', NULL, 832422, NULL, NULL),
(36292, 'COMPETENCIA TECNICA', 172, 'EMPALMAR CABLES DE FIBRA ÓPTICA, DE ACUERDO CON NORMAS TÉCNICAS Y PROCEDIMIENTOS DE LA EMPRESA.', NULL, 832422, NULL, NULL),
(36835, 'COMPETENCIA TECNICA', 150, 'ANALIZAR CIRCUITOS ELÉCTRICOS DE ACUERDO CON EL MÉTODO REQUERIDO', NULL, 832333, NULL, NULL),
(36851, 'COMPETENCIA TECNICA', 330, 'INSTALAR REDES INTERNAS DE ACUERDO CON EL DISEÑO ELÉCTRICO', NULL, 832333, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 223206, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 223213, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 224201, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 224312, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 224501, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 225224, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 225311, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 226701, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 664212, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 821100, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 821202, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 821203, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 821307, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 821620, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 832102, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 832202, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 832402, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 833100, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 833301, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 834258, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 836135, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 836136, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 836137, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 836138, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 836600, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 837501, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 838100, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 838109, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 838200, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 838318, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 839317, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 845102, NULL, NULL),
(37371, 'TIC', 48, 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información', NULL, 861100, NULL, NULL),
(37714, 'BILINGUISMO', 384, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 223206, NULL, NULL),
(37714, 'BILINGUISMO', 384, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 223213, NULL, NULL),
(37714, 'BILINGUISMO', 384, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 224201, NULL, NULL),
(37714, 'BILINGUISMO', 384, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 224312, NULL, NULL),
(37714, 'BILINGUISMO', 192, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 224315, NULL, NULL),
(37714, 'BILINGUISMO', 180, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 224501, NULL, NULL),
(37714, 'BILINGUISMO', 384, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 225311, NULL, NULL),
(37714, 'BILINGUISMO', 384, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 226701, NULL, NULL),
(37714, 'BILINGUISMO', 192, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 664212, NULL, NULL),
(37714, 'BILINGUISMO', 384, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 821100, NULL, NULL),
(37714, 'BILINGUISMO', 384, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 821202, NULL, NULL),
(37714, 'BILINGUISMO', 384, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 821203, NULL, NULL),
(37714, 'BILINGUISMO', 384, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 821307, NULL, NULL),
(37714, 'BILINGUISMO', 384, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 821620, NULL, NULL),
(37714, 'BILINGUISMO', 192, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 832102, NULL, NULL),
(37714, 'BILINGUISMO', 192, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 832202, NULL, NULL),
(37714, 'BILINGUISMO', 180, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 832402, NULL, NULL),
(37714, 'BILINGUISMO', 192, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 833100, NULL, NULL),
(37714, 'BILINGUISMO', 192, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 833301, NULL, NULL),
(37714, 'BILINGUISMO', 192, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 834258, NULL, NULL),
(37714, 'BILINGUISMO', 192, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 836135, NULL, NULL),
(37714, 'BILINGUISMO', 192, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 836136, NULL, NULL),
(37714, 'BILINGUISMO', 192, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 836600, NULL, NULL),
(37714, 'BILINGUISMO', 96, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 837501, NULL, NULL),
(37714, 'BILINGUISMO', 192, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 838100, NULL, NULL),
(37714, 'BILINGUISMO', 192, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 838109, NULL, NULL),
(37714, 'BILINGUISMO', 192, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 838200, NULL, NULL),
(37714, 'BILINGUISMO', 192, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 838318, NULL, NULL),
(37714, 'BILINGUISMO', 192, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 839317, NULL, NULL),
(37714, 'BILINGUISMO', 192, 'INTERACTUAR EN LENGUA INGLESA DE FORMA ORAL Y ESCRITA DENTRO DE CONTEXTOS SOCIALES Y LABORALES SEGÚN LOS CRITERIOS ESTABLECIDOS POR EL MARCO COMÚN EUROPEO DE REFERENCIA PARA LAS LENGUAS.', NULL, 861100, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 223206, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 223213, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 224201, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 224312, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 224315, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 224501, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 225224, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 225311, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 225314, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 226701, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 664212, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 821100, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 821202, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 821203, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 821307, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 821620, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 832102, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 832202, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 832303, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 832402, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 833100, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 833301, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 834258, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 836135, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 836136, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 836137, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 836138, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 836140, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 836600, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 837501, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 838100, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 838109, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 838200, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 838318, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 839317, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 845102, NULL, NULL),
(37799, 'AMBIENTAL Y SST', 48, 'APLICAR PRÁCTICAS  DE PROTECCIÓN AMBIENTAL, SEGURIDAD Y SALUD EN EL TRABAJO DE ACUERDO CON LAS POLÍTICAS ORGANIZACIONALES  Y LA NORMATIVIDAD VIGENTE.', NULL, 861100, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 223206, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 223213, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 224201, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 224312, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 224315, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 224501, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 225224, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 225311, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 225314, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 226701, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 664212, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 821100, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 821202, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 821203, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 821307, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 821620, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 832102, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 832202, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 832303, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 832402, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 833100, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 833301, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 834258, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 836135, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 836136, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 836137, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 836138, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 836140, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 836600, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 837501, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 838100, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 838109, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 838200, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 838318, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 839317, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 845102, NULL, NULL),
(37800, 'CULTURA FISICA', 48, 'GENERAR HÁBITOS SALUDABLES DE VIDA MEDIANTE LA APLICACIÓN DE PROGRAMAS DE ACTIVIDAD FÍSICA EN LOS CONTEXTOS PRODUCTIVOS Y SOCIALES.', NULL, 861100, NULL, NULL),
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 223206, NULL, NULL),
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 223213, NULL, NULL),
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 224201, NULL, NULL),
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 224312, NULL, NULL),
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 224315, NULL, NULL),
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 224501, NULL, NULL),
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 225311, NULL, NULL),
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 225314, NULL, NULL),
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 226701, NULL, NULL),
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 821100, NULL, NULL),
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 821202, NULL, NULL),
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 821203, NULL, NULL),
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 821307, NULL, NULL),
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 821620, NULL, NULL),
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 832102, NULL, NULL),
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 832402, NULL, NULL),
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 833100, NULL, NULL),
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 833301, NULL, NULL),
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 834258, NULL, NULL);
INSERT INTO `competencia` (`comp_id`, `comp_nombre_corto`, `comp_horas`, `comp_nombre_unidad_competencia`, `centro_formacion_cent_id`, `programa_prog_id`, `requisitos_academicos`, `experiencia_laboral`) VALUES
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 836135, NULL, NULL),
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 836136, NULL, NULL),
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 836600, NULL, NULL),
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 838100, NULL, NULL),
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 838109, NULL, NULL),
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 838200, NULL, NULL),
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 838318, NULL, NULL),
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 839317, NULL, NULL),
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 845102, NULL, NULL),
(37801, 'CIENCIAS NATURALES', 48, 'APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES DE ACUERDO CON SITUACIONES DEL CONTEXTO PRODUCTIVO Y SOCIAL.', NULL, 861100, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 223206, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 223213, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 224201, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 224312, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 224315, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 224501, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 225224, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 225311, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 225314, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 226701, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 664212, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 821100, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 821202, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 821203, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 821307, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 821620, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 832102, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 832402, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 833100, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 833301, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 834258, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 836135, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 836136, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 836137, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 836600, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 837501, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 838100, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 838109, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 838200, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 838318, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 839317, NULL, NULL),
(37802, 'COMUNICACIONES', 48, 'DESARROLLAR PROCESOS DE COMUNICACIÓN EFICACES Y EFECTIVOS, TENIENDO EN CUENTA SITUACIONES  DE ORDEN SOCIAL, PERSONAL Y PRODUCTIVO.', NULL, 861100, NULL, NULL),
(37820, 'COMPETENCIA TECNICA', 144, 'Ensamblar tarjetas electrónicas según especificaciones y normativa técnica', NULL, 224201, NULL, NULL),
(37860, 'COMPETENCIA TECNICA', 144, 'Dibujar planos mecánicos de acuerdo con normas técnicas', NULL, 821100, NULL, NULL),
(37871, 'COMPETENCIA TECNICA', 48, 'Desactivar propulsión eléctrica vehicular de acuerdo con parámetros y normativa técnica.', NULL, 838200, NULL, NULL),
(37871, 'COMPETENCIA TECNICA', 48, 'Desactivar propulsión eléctrica vehicular de acuerdo con parámetros y normativa técnica.', NULL, 838318, NULL, NULL),
(37889, 'COMPETENCIA TECNICA', 48, 'Calcular costos de operación de acuerdo con métodos', NULL, 225311, NULL, NULL),
(37889, 'COMPETENCIA TECNICA', 864, 'Calcular costos de operación de acuerdo con métodos', NULL, 821307, NULL, NULL),
(37920, 'COMPETENCIA TECNICA', 336, 'Medir los riesgos de acuerdo con metodología y proceso de negocio', NULL, 226701, NULL, NULL),
(37943, 'COMPETENCIA TECNICA', 108, 'Replantear los diseños de acuerdo con planos y especificaciones técnicas de construcción.', NULL, 836135, NULL, NULL),
(37943, 'COMPETENCIA TECNICA', 96, 'Replantear los diseños de acuerdo con planos y especificaciones técnicas de construcción.', NULL, 836136, NULL, NULL),
(37972, 'COMPETENCIA TECNICA', 432, 'Construir firmware según especificaciones y normativa técnica', NULL, 224201, NULL, NULL),
(38021, 'COMPETENCIA TECNICA', 768, 'DISEÑAR CIRCUITOS ELECTRÓNICOS SEGÚN ESPECIFICACIONES TÉCNICAS', NULL, 224201, NULL, NULL),
(38022, 'COMPETENCIA TECNICA', 192, 'Reparar máquinas eléctricas de baja tensión según procedimientos técnicos', NULL, 223213, NULL, NULL),
(38022, 'COMPETENCIA TECNICA', 144, 'Reparar máquinas eléctricas de baja tensión según procedimientos técnicos', NULL, 832102, NULL, NULL),
(38049, 'COMPETENCIA TECNICA', 144, 'Diseñar tarjetas de circuito impreso según especificaciones y normativa técnica', NULL, 224201, NULL, NULL),
(38067, 'COMPETENCIA TECNICA', 336, 'Reparar sistemas de control eléctrico según requerimientos técnicos', NULL, 223213, NULL, NULL),
(38067, 'COMPETENCIA TECNICA', 48, 'Reparar sistemas de control eléctrico según requerimientos técnicos', NULL, 837501, NULL, NULL),
(38081, 'COMPETENCIA TECNICA', 240, 'Determinar las bases del marco estratégico según criterios técnicos', NULL, 226701, NULL, NULL),
(38119, 'COMPETENCIA TECNICA', 672, 'Entrenar deportistas según estándar técnico-táctico', NULL, 664212, NULL, NULL),
(38120, 'COMPETENCIA TECNICA', 96, 'Instalar equipos industriales según procedimientos técnicos', NULL, 223206, NULL, NULL),
(38120, 'COMPETENCIA TECNICA', 96, 'Instalar equipos industriales según procedimientos técnicos', NULL, 223213, NULL, NULL),
(38130, 'COMPETENCIA TECNICA', 144, 'Preparar materiales según especificaciones de construcción', NULL, 836136, NULL, NULL),
(38130, 'COMPETENCIA TECNICA', 144, 'Preparar materiales según especificaciones de construcción', NULL, 836600, NULL, NULL),
(38199, 'INVESTIGACION', 48, 'Orientar investigación formativa según referentes técnicos', NULL, 223206, NULL, NULL),
(38199, 'INVESTIGACION', 48, 'Orientar investigación formativa según referentes técnicos', NULL, 223213, NULL, NULL),
(38199, 'INVESTIGACION', 48, 'Orientar investigación formativa según referentes técnicos', NULL, 224201, NULL, NULL),
(38199, 'INVESTIGACION', 48, 'Orientar investigación formativa según referentes técnicos', NULL, 224312, NULL, NULL),
(38199, 'INVESTIGACION', 48, 'Orientar investigación formativa según referentes técnicos', NULL, 225311, NULL, NULL),
(38199, 'INVESTIGACION', 48, 'Orientar investigación formativa según referentes técnicos', NULL, 226701, NULL, NULL),
(38199, 'INVESTIGACION', 48, 'Orientar investigación formativa según referentes técnicos', NULL, 821100, NULL, NULL),
(38199, 'INVESTIGACION', 48, 'Orientar investigación formativa según referentes técnicos', NULL, 821202, NULL, NULL),
(38199, 'INVESTIGACION', 48, 'Orientar investigación formativa según referentes técnicos', NULL, 821203, NULL, NULL),
(38199, 'INVESTIGACION', 48, 'Orientar investigación formativa según referentes técnicos', NULL, 821307, NULL, NULL),
(38199, 'INVESTIGACION', 48, 'Orientar investigación formativa según referentes técnicos', NULL, 821620, NULL, NULL),
(38200, 'COMPETENCIA TECNICA', 144, 'Colocar mezclas asfálticas de acuerdo con especificaciones técnicas', NULL, 861100, NULL, NULL),
(38202, 'COMPETENCIA TECNICA', 912, 'Operar equipo de excavación según manuales técnicos.', NULL, 845102, NULL, NULL),
(38208, 'COMPETENCIA TECNICA', 96, 'Conservar el sistema de automatización según el instructivo técnico de mantenimiento preventivo', NULL, 224315, NULL, NULL),
(38251, 'COMPETENCIA TECNICA', 48, 'Configurar el sistema de gestión de la energía de acuerdo con la normativa y estándares técnicos', NULL, 837501, NULL, NULL),
(38329, 'COMPETENCIA TECNICA', 96, 'Controlar los inventarios según indicadores y métodos', NULL, 821620, NULL, NULL),
(38364, 'COMPETENCIA TECNICA', 288, 'Georeferenciar proyectos de ingeniería de acuerdo con especificaciones técnicas de topografía.', NULL, 225311, NULL, NULL),
(38372, 'COMPETENCIA TECNICA', 240, 'Levantar superficies altimétricamente según especificaciones técnicas de topografía.', NULL, 225311, NULL, NULL),
(38379, 'COMPETENCIA TECNICA', 144, 'Levantar terrenos según técnicas de fotogrametría', NULL, 225311, NULL, NULL),
(38380, 'COMPETENCIA TECNICA', 240, 'Localizar obras hidráulicas de acuerdo con planos y especificaciones técnicas', NULL, 225311, NULL, NULL),
(38390, 'COMPETENCIA TECNICA', 336, 'Levantar terrenos según especificaciones técnicas de topografía planimétrica', NULL, 225311, NULL, NULL),
(38391, 'COMPETENCIA TECNICA', 336, 'Trazar proyectos viales de acuerdo con planos y especificaciones técnicas.', NULL, 225311, NULL, NULL),
(38442, 'COMPETENCIA TECNICA', 96, 'Armar andamios según especificaciones técnicas y normativa de trabajo en alturas', NULL, 836600, NULL, NULL),
(38445, 'COMPETENCIA TECNICA', 288, 'Localizar obras de urbanismo de acuerdo con planos y especificaciones técnicas', NULL, 225311, NULL, NULL),
(38446, 'COMPETENCIA TECNICA', 192, 'MANTENER EQUIPOS ELECTRO ELECTRÓNICOS SEGÚN MANUALES TÉCNICOS Y NORMATIVA', NULL, 839317, NULL, NULL),
(38459, 'COMPETENCIA TECNICA', 240, 'LEVANTAR OBRAS ESPECIALES SEGÚN ESPECIFICACIONES TÉCNICAS DE TOPOGRAFÍA.', NULL, 225311, NULL, NULL),
(38466, 'COMPETENCIA TECNICA', 336, 'IMPLEMENTAR REQUISITOS NORMATIVOS DE ACUERDO CON PARÁMETROS TÉCNICOS', NULL, 226701, NULL, NULL),
(38466, 'COMPETENCIA TECNICA', 48, 'IMPLEMENTAR REQUISITOS NORMATIVOS DE ACUERDO CON PARÁMETROS TÉCNICOS', NULL, 832102, NULL, NULL),
(38466, 'COMPETENCIA TECNICA', 48, 'IMPLEMENTAR REQUISITOS NORMATIVOS DE ACUERDO CON PARÁMETROS TÉCNICOS', NULL, 832202, NULL, NULL),
(38466, 'COMPETENCIA TECNICA', 72, 'IMPLEMENTAR REQUISITOS NORMATIVOS DE ACUERDO CON PARÁMETROS TÉCNICOS', NULL, 832300, NULL, NULL),
(38467, 'COMPETENCIA TECNICA', 480, 'Monitorear sistemas de gestión de acuerdo con normativa y requerimientos técnicos', NULL, 226701, NULL, NULL),
(38496, 'COMPETENCIA TECNICA', 274, 'REPARAR EQUIPOS A GAS SEGÚN PROCEDIMIENTOS TÉCNICOS Y MANUALES DEL FABRICANTE', NULL, 833301, NULL, NULL),
(38509, 'COMPETENCIA TECNICA', 240, 'Estructurar montaje de automatización según especificaciones y normativa técnica', NULL, 224312, NULL, NULL),
(38510, 'COMPETENCIA TECNICA', 96, 'Caracterizar equipos de automatización según requerimientos técnicos', NULL, 224312, NULL, NULL),
(38510, 'COMPETENCIA TECNICA', 240, 'Caracterizar equipos de automatización según requerimientos técnicos', NULL, 224315, NULL, NULL),
(38526, 'COMPETENCIA TECNICA', 192, 'Reparar tarjetas electrónicas de acuerdo con técnicas especializadas de diagnóstico', NULL, 224201, NULL, NULL),
(38526, 'COMPETENCIA TECNICA', 144, 'Reparar tarjetas electrónicas de acuerdo con técnicas especializadas de diagnóstico', NULL, 839317, NULL, NULL),
(38527, 'COMPETENCIA TECNICA', 48, 'TRABAJAR EN ALTURAS DE ACUERDO CON NORMATIVA DE SEGURIDAD Y SALUD EN EL TRABAJO', NULL, 224501, NULL, NULL),
(38527, 'COMPETENCIA TECNICA', 48, 'TRABAJAR EN ALTURAS DE ACUERDO CON NORMATIVA DE SEGURIDAD Y SALUD EN EL TRABAJO', NULL, 821202, NULL, NULL),
(38527, 'COMPETENCIA TECNICA', 48, 'TRABAJAR EN ALTURAS DE ACUERDO CON NORMATIVA DE SEGURIDAD Y SALUD EN EL TRABAJO', NULL, 821203, NULL, NULL),
(38527, 'COMPETENCIA TECNICA', 48, 'TRABAJAR EN ALTURAS DE ACUERDO CON NORMATIVA DE SEGURIDAD Y SALUD EN EL TRABAJO', NULL, 832102, NULL, NULL),
(38527, 'COMPETENCIA TECNICA', 48, 'TRABAJAR EN ALTURAS DE ACUERDO CON NORMATIVA DE SEGURIDAD Y SALUD EN EL TRABAJO', NULL, 832202, NULL, NULL),
(38527, 'COMPETENCIA TECNICA', 48, 'TRABAJAR EN ALTURAS DE ACUERDO CON NORMATIVA DE SEGURIDAD Y SALUD EN EL TRABAJO', NULL, 832303, NULL, NULL),
(38527, 'COMPETENCIA TECNICA', 48, 'TRABAJAR EN ALTURAS DE ACUERDO CON NORMATIVA DE SEGURIDAD Y SALUD EN EL TRABAJO', NULL, 832402, NULL, NULL),
(38527, 'COMPETENCIA TECNICA', 48, 'TRABAJAR EN ALTURAS DE ACUERDO CON NORMATIVA DE SEGURIDAD Y SALUD EN EL TRABAJO', NULL, 837501, NULL, NULL),
(38530, 'COMPETENCIA TECNICA', 336, 'DOCUMENTAR PROCESOS DE ACUERDO CON NORMATIVA Y PROCEDIMIENTOS TÉCNICOS', NULL, 226701, NULL, NULL),
(38534, 'COMPETENCIA TECNICA', 336, 'DISEÑAR RED DE TELECOMUNICACIONES SEGÚN ESPECIFICACIONES Y NORMATIVA TÉCNICA', NULL, 821203, NULL, NULL),
(38535, 'COMPETENCIA TECNICA', 288, 'COORDINAR MONTAJE DE PLATAFORMA DE SERVICIO DE ACUERDO CON NORMATIVA DE TELECOMUNICACIONES', NULL, 821203, NULL, NULL),
(38537, 'COMPETENCIA TECNICA', 240, 'CONTROLAR CONSTRUCCIÓN DE RED SEGÚN ESPECIFICACIONES Y NORMATIVA DE TELECOMUNICACIONES.', NULL, 821203, NULL, NULL),
(38538, 'COMPETENCIA TECNICA', 96, 'PLANEAR MANTENIMIENTO DE INFRAESTRUCTURA DE SERVICIO SEGÚN MANUALES TÉCNICOS Y NORMATIVA DE TELECOMUNICACIONES', NULL, 821203, NULL, NULL),
(38539, 'COMPETENCIA TECNICA', 384, 'CONFIGURAR RED SEGÚN REQUERIMIENTOS DEL CLIENTE Y NORMATIVA DE TELECOMUNICACIONES', NULL, 224501, NULL, NULL),
(38539, 'COMPETENCIA TECNICA', 384, 'CONFIGURAR RED SEGÚN REQUERIMIENTOS DEL CLIENTE Y NORMATIVA DE TELECOMUNICACIONES', NULL, 821203, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 223206, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 223213, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 224201, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 224312, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 224315, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 224501, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 225224, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 225311, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 225314, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 226701, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 664212, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 821100, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 821202, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 821203, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 821307, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 821620, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 832102, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 832202, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 832300, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 832303, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 832333, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 832402, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 832422, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 833100, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 833301, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 834258, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 836135, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 836136, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 836137, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 836138, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 836140, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 836600, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 837501, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 838100, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 838109, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 838200, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 838318, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 839317, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 845102, NULL, NULL),
(38558, 'DERECHOS FUNDAMENTALES', 48, 'Ejercer derechos fundamentales del trabajo en el marco de la constitución política y los convenios internacionales.', NULL, 861100, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 223206, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 223213, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 224201, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 224312, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 224315, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 224501, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 225224, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 225311, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 225314, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 226701, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 821100, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 821202, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 821203, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 821307, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 821620, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 832102, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 832202, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 832402, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 833100, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 833301, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 834258, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 836135, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 836136, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 836138, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 836600, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 837501, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 838100, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 838109, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 838200, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 838318, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 839317, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 845102, NULL, NULL),
(38560, 'MATEMATICA', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.', NULL, 861100, NULL, NULL),
(38561, 'EMPRENDIMIENTO', 48, 'Gestionar procesos propios de la cultura emprendedora y empresarial de acuerdo con el perfil personal y los requerimientos de los contextos productivo y social.', NULL, 223206, NULL, NULL),
(38561, 'EMPRENDIMIENTO', 48, 'Gestionar procesos propios de la cultura emprendedora y empresarial de acuerdo con el perfil personal y los requerimientos de los contextos productivo y social.', NULL, 223213, NULL, NULL),
(38561, 'EMPRENDIMIENTO', 48, 'Gestionar procesos propios de la cultura emprendedora y empresarial de acuerdo con el perfil personal y los requerimientos de los contextos productivo y social.', NULL, 224201, NULL, NULL),
(38561, 'EMPRENDIMIENTO', 48, 'Gestionar procesos propios de la cultura emprendedora y empresarial de acuerdo con el perfil personal y los requerimientos de los contextos productivo y social.', NULL, 224312, NULL, NULL),
(38561, 'EMPRENDIMIENTO', 48, 'Gestionar procesos propios de la cultura emprendedora y empresarial de acuerdo con el perfil personal y los requerimientos de los contextos productivo y social.', NULL, 224501, NULL, NULL),
(38561, 'EMPRENDIMIENTO', 48, 'Gestionar procesos propios de la cultura emprendedora y empresarial de acuerdo con el perfil personal y los requerimientos de los contextos productivo y social.', NULL, 225311, NULL, NULL),
(38561, 'EMPRENDIMIENTO', 48, 'Gestionar procesos propios de la cultura emprendedora y empresarial de acuerdo con el perfil personal y los requerimientos de los contextos productivo y social.', NULL, 226701, NULL, NULL),
(38561, 'EMPRENDIMIENTO', 48, 'Gestionar procesos propios de la cultura emprendedora y empresarial de acuerdo con el perfil personal y los requerimientos de los contextos productivo y social.', NULL, 821100, NULL, NULL),
(38561, 'EMPRENDIMIENTO', 48, 'Gestionar procesos propios de la cultura emprendedora y empresarial de acuerdo con el perfil personal y los requerimientos de los contextos productivo y social.', NULL, 821202, NULL, NULL),
(38561, 'EMPRENDIMIENTO', 48, 'Gestionar procesos propios de la cultura emprendedora y empresarial de acuerdo con el perfil personal y los requerimientos de los contextos productivo y social.', NULL, 821203, NULL, NULL),
(38561, 'EMPRENDIMIENTO', 48, 'Gestionar procesos propios de la cultura emprendedora y empresarial de acuerdo con el perfil personal y los requerimientos de los contextos productivo y social.', NULL, 821307, NULL, NULL),
(38561, 'EMPRENDIMIENTO', 48, 'Gestionar procesos propios de la cultura emprendedora y empresarial de acuerdo con el perfil personal y los requerimientos de los contextos productivo y social.', NULL, 821620, NULL, NULL),
(38561, 'EMPRENDIMIENTO', 48, 'Gestionar procesos propios de la cultura emprendedora y empresarial de acuerdo con el perfil personal y los requerimientos de los contextos productivo y social.', NULL, 832102, NULL, NULL),
(38561, 'EMPRENDIMIENTO', 48, 'Gestionar procesos propios de la cultura emprendedora y empresarial de acuerdo con el perfil personal y los requerimientos de los contextos productivo y social.', NULL, 832402, NULL, NULL),
(38561, 'EMPRENDIMIENTO', 48, 'Gestionar procesos propios de la cultura emprendedora y empresarial de acuerdo con el perfil personal y los requerimientos de los contextos productivo y social.', NULL, 833100, NULL, NULL),
(38561, 'EMPRENDIMIENTO', 48, 'Gestionar procesos propios de la cultura emprendedora y empresarial de acuerdo con el perfil personal y los requerimientos de los contextos productivo y social.', NULL, 833301, NULL, NULL),
(38561, 'EMPRENDIMIENTO', 48, 'Gestionar procesos propios de la cultura emprendedora y empresarial de acuerdo con el perfil personal y los requerimientos de los contextos productivo y social.', NULL, 834258, NULL, NULL),
(38561, 'EMPRENDIMIENTO', 48, 'Gestionar procesos propios de la cultura emprendedora y empresarial de acuerdo con el perfil personal y los requerimientos de los contextos productivo y social.', NULL, 836135, NULL, NULL),
(38561, 'EMPRENDIMIENTO', 48, 'Gestionar procesos propios de la cultura emprendedora y empresarial de acuerdo con el perfil personal y los requerimientos de los contextos productivo y social.', NULL, 836136, NULL, NULL),
(38561, 'EMPRENDIMIENTO', 48, 'Gestionar procesos propios de la cultura emprendedora y empresarial de acuerdo con el perfil personal y los requerimientos de los contextos productivo y social.', NULL, 836600, NULL, NULL),
(38561, 'EMPRENDIMIENTO', 48, 'Gestionar procesos propios de la cultura emprendedora y empresarial de acuerdo con el perfil personal y los requerimientos de los contextos productivo y social.', NULL, 838100, NULL, NULL),
(38561, 'EMPRENDIMIENTO', 48, 'Gestionar procesos propios de la cultura emprendedora y empresarial de acuerdo con el perfil personal y los requerimientos de los contextos productivo y social.', NULL, 838109, NULL, NULL),
(38561, 'EMPRENDIMIENTO', 48, 'Gestionar procesos propios de la cultura emprendedora y empresarial de acuerdo con el perfil personal y los requerimientos de los contextos productivo y social.', NULL, 838200, NULL, NULL),
(38561, 'EMPRENDIMIENTO', 48, 'Gestionar procesos propios de la cultura emprendedora y empresarial de acuerdo con el perfil personal y los requerimientos de los contextos productivo y social.', NULL, 838318, NULL, NULL),
(38562, 'COMPETENCIA TECNICA', 912, 'PROGRAMAR EQUIPO DE CONTROL DE ACUERDO CON DISEÑO DE AUTOMATIZACIÓN', NULL, 224312, NULL, NULL),
(38579, 'COMPETENCIA TECNICA', 240, 'PROGRAMAR LA PRODUCCIÓN SEGÚN MÉTODOS Y PARÁMETROS TÉCNICOS', NULL, 821100, NULL, NULL),
(38587, 'COMPETENCIA TECNICA', 864, 'Intervenir equipos de acuerdo con técnicas de mantenimiento preventivo', NULL, 223206, NULL, NULL),
(38587, 'COMPETENCIA TECNICA', 480, 'Intervenir equipos de acuerdo con técnicas de mantenimiento preventivo', NULL, 223213, NULL, NULL),
(38617, 'COMPETENCIA TECNICA', 144, 'Acondicionar vehículos de acuerdo con procedimientos de mantenimiento preventivo y normativas', NULL, 838100, NULL, NULL),
(38617, 'COMPETENCIA TECNICA', 96, 'Acondicionar vehículos de acuerdo con procedimientos de mantenimiento preventivo y normativas', NULL, 838109, NULL, NULL),
(38617, 'COMPETENCIA TECNICA', 144, 'Acondicionar vehículos de acuerdo con procedimientos de mantenimiento preventivo y normativas', NULL, 838200, NULL, NULL),
(38619, 'COMPETENCIA TECNICA', 288, 'REPARAR MOTORES DE ACUERDO CON PROCEDIMIENTOS TÉCNICOS Y PARÁMETROS DEL CICLO OTTO', NULL, 821620, NULL, NULL),
(38619, 'COMPETENCIA TECNICA', 96, 'REPARAR MOTORES DE ACUERDO CON PROCEDIMIENTOS TÉCNICOS Y PARÁMETROS DEL CICLO OTTO', NULL, 838109, NULL, NULL),
(38620, 'COMPETENCIA TECNICA', 144, 'REPARAR SISTEMA DE COMBUSTIBLE DE ACUERDO CON PROCEDIMIENTOS Y PARÁMETROS DEL CICLO OTTO', NULL, 821620, NULL, NULL),
(38620, 'COMPETENCIA TECNICA', 48, 'REPARAR SISTEMA DE COMBUSTIBLE DE ACUERDO CON PROCEDIMIENTOS Y PARÁMETROS DEL CICLO OTTO', NULL, 838109, NULL, NULL),
(38620, 'COMPETENCIA TECNICA', 96, 'REPARAR SISTEMA DE COMBUSTIBLE DE ACUERDO CON PROCEDIMIENTOS Y PARÁMETROS DEL CICLO OTTO', NULL, 838318, NULL, NULL),
(38621, 'COMPETENCIA TECNICA', 336, 'REPARAR SISTEMA ELÉCTRICO ELECTRÓNICO DE ACUERDO CON PROCEDIMIENTOS Y PARÁMETROS DE AUTOMOTORES', NULL, 821620, NULL, NULL),
(38621, 'COMPETENCIA TECNICA', 144, 'REPARAR SISTEMA ELÉCTRICO ELECTRÓNICO DE ACUERDO CON PROCEDIMIENTOS Y PARÁMETROS DE AUTOMOTORES', NULL, 838109, NULL, NULL),
(38621, 'COMPETENCIA TECNICA', 288, 'REPARAR SISTEMA ELÉCTRICO ELECTRÓNICO DE ACUERDO CON PROCEDIMIENTOS Y PARÁMETROS DE AUTOMOTORES', NULL, 838200, NULL, NULL),
(38622, 'COMPETENCIA TECNICA', 288, 'REPARAR SISTEMA TRANSMISOR DE POTENCIA DE ACUERDO CON PROCEDIMIENTOS Y PARÁMETROS TÉCNICOS DE AUTOMOTORES', NULL, 821620, NULL, NULL),
(38622, 'COMPETENCIA TECNICA', 96, 'REPARAR SISTEMA TRANSMISOR DE POTENCIA DE ACUERDO CON PROCEDIMIENTOS Y PARÁMETROS TÉCNICOS DE AUTOMOTORES', NULL, 838109, NULL, NULL),
(38623, 'COMPETENCIA TECNICA', 240, 'REPARAR SISTEMA DE SEGURIDAD ACTIVA DE ACUERDO CON PROCEDIMIENTOS Y PARÁMETROS TÉCNICOS DE AUTOMOTORES', NULL, 821620, NULL, NULL),
(38623, 'COMPETENCIA TECNICA', 96, 'REPARAR SISTEMA DE SEGURIDAD ACTIVA DE ACUERDO CON PROCEDIMIENTOS Y PARÁMETROS TÉCNICOS DE AUTOMOTORES', NULL, 838109, NULL, NULL),
(38648, 'COMPETENCIA TECNICA', 240, 'SOLDAR LÁMINAS METÁLICAS SEGÚN TÉCNICA DE ARCO MANUAL ELÉCTRODO REVESTIDO Y NORMATIVA.', NULL, 834258, NULL, NULL),
(38650, 'COMPETENCIA TECNICA', 240, 'SOLDAR LÁMINAS METÁLICAS SEGÚN TÉCNICA DE ALAMBRE SÓLIDO Y NORMATIVA.', NULL, 834258, NULL, NULL),
(38653, 'COMPETENCIA TECNICA', 192, 'REPARAR SISTEMA DE SEGURIDAD PASIVA Y CONFORT DE ACUERDO CON PROCEDIMIENTOS Y NORMATIVA VEHICULAR', NULL, 821620, NULL, NULL),
(38653, 'COMPETENCIA TECNICA', 96, 'REPARAR SISTEMA DE SEGURIDAD PASIVA Y CONFORT DE ACUERDO CON PROCEDIMIENTOS Y NORMATIVA VEHICULAR', NULL, 838109, NULL, NULL),
(38653, 'COMPETENCIA TECNICA', 192, 'REPARAR SISTEMA DE SEGURIDAD PASIVA Y CONFORT DE ACUERDO CON PROCEDIMIENTOS Y NORMATIVA VEHICULAR', NULL, 838200, NULL, NULL),
(38659, 'COMPETENCIA TECNICA', 48, 'MEDIR CONSTRUCCIONES SEGÚN TÉCNICAS Y PROCEDIMIENTOS TÉCNICOS', NULL, 225224, NULL, NULL),
(38659, 'COMPETENCIA TECNICA', 288, 'MEDIR CONSTRUCCIONES SEGÚN TÉCNICAS Y PROCEDIMIENTOS TÉCNICOS', NULL, 225314, NULL, NULL),
(38724, 'COMPETENCIA TECNICA', 48, 'Programar proyectos según especificaciones técnicas y métodos de planeación.', NULL, 225311, NULL, NULL),
(38726, 'COMPETENCIA TECNICA', 144, 'INSTALAR APARATOS SANITARIOS DE ACUERDO CON PLANOS Y NORMATIVA TÉCNICA.', NULL, 833100, NULL, NULL),
(38730, 'COMPETENCIA TECNICA', 144, 'PROBAR INSTALACIONES TÉCNICAS DE ACUERDO CON PLANOS Y ESPECIFICACIONES DE CONSTRUCCIÓN.', NULL, 836137, NULL, NULL),
(38748, 'COMPETENCIA TECNICA', 252, 'INSTALAR EQUIPOS DE RED INALÁMBRICA SEGÚN MANUALES Y NORMATIVA TELECOMUNICACIONES', NULL, 224501, NULL, NULL),
(38885, 'COMPETENCIA TECNICA', 288, 'Elaborar estructura de datos espaciales de acuerdo con normativa técnica y especificaciones del sistema de información geografica', NULL, 225314, NULL, NULL),
(38900, 'COMPETENCIA TECNICA', 432, 'TENDER REDES DE ENERGÍA DE ACUERDO CON NORMATIVA Y PROCEDIMIENTO TÉCNICO', NULL, 821202, NULL, NULL),
(38900, 'COMPETENCIA TECNICA', 192, 'TENDER REDES DE ENERGÍA DE ACUERDO CON NORMATIVA Y PROCEDIMIENTO TÉCNICO', NULL, 832303, NULL, NULL),
(38901, 'COMPETENCIA TECNICA', 96, 'FIJAR POSTERÍA DE ACUERDO CON PROCEDIMIENTO TÉCNICO', NULL, 832303, NULL, NULL),
(38918, 'COMPETENCIA TECNICA', 240, 'Diagnosticar circuitos electrónicos según manuales técnicos y normativa técnica', NULL, 821203, NULL, NULL),
(38918, 'COMPETENCIA TECNICA', 48, 'Diagnosticar circuitos electrónicos según manuales técnicos y normativa técnica', NULL, 837501, NULL, NULL),
(38918, 'COMPETENCIA TECNICA', 192, 'Diagnosticar circuitos electrónicos según manuales técnicos y normativa técnica', NULL, 839317, NULL, NULL),
(38974, 'COMPETENCIA TECNICA', 960, 'Reparar automatismos de acuerdo con metodología y procedimiento técnico', NULL, 224312, NULL, NULL),
(38974, 'COMPETENCIA TECNICA', 528, 'Reparar automatismos de acuerdo con metodología y procedimiento técnico', NULL, 224315, NULL, NULL),
(38976, 'COMPETENCIA TECNICA', 528, 'Integrar sistema de automatización de acuerdo con procedimientos y requerimientos técnicos', NULL, 224201, NULL, NULL),
(38991, 'COMPETENCIA TECNICA', 384, 'Validar planos de acuerdo con normativa y requerimientos técnicos', NULL, 225224, NULL, NULL),
(38993, 'COMPETENCIA TECNICA', 96, 'Fundir concreto de acuerdo con requerimientos técnicos de construcción', NULL, 836138, NULL, NULL),
(38993, 'COMPETENCIA TECNICA', 288, 'Fundir concreto de acuerdo con requerimientos técnicos de construcción', NULL, 861100, NULL, NULL),
(39003, 'COMPETENCIA TECNICA', 336, 'INSTALAR EQUIPOS DE RED INTERNA DE ACUERDO CON PROCEDIMIENTOS TÉCNICOS Y NORMATIVA DE TELECOMUNICACIONES', NULL, 832402, NULL, NULL),
(39004, 'COMPETENCIA TECNICA', 288, 'COMPROBAR FUNCIONAMIENTO DE COMUNICACIONES ELECTRÓNICAS SEGÚN NORMATIVA DE TELECOMUNICACIONES', NULL, 821203, NULL, NULL),
(39026, 'COMPETENCIA TECNICA', 672, 'CONTROLAR INSTALACIÓN DE REDES DE GAS DE ACUERDO CON PROCEDIMIENTOS TÉCNICOS Y NORMATIVA.', NULL, 821307, NULL, NULL),
(39028, 'COMPETENCIA TECNICA', 182, 'INSTALAR ARTEFACTOS A GAS SEGÚN MANUALES TÉCNICOS Y NORMATIVA', NULL, 833301, NULL, NULL),
(39064, 'COMPETENCIA TECNICA', 144, 'Diagnosticar motores de acuerdo con procedimientos técnicos y parámetros del ciclo Diesel', NULL, 838100, NULL, NULL),
(39065, 'COMPETENCIA TECNICA', 240, 'REPARAR MOTORES DE ACUERDO CON PROCEDIMIENTOS TÉCNICOS Y PARÁMETROS DEL CICLO DIESEL', NULL, 838100, NULL, NULL),
(39066, 'COMPETENCIA TECNICA', 192, 'REPARAR SISTEMA DE COMBUSTIBLE DE ACUERDO CON PROCEDIMIENTOS Y PARÁMETROS DEL CICLO DIESEL', NULL, 821620, NULL, NULL),
(39066, 'COMPETENCIA TECNICA', 144, 'REPARAR SISTEMA DE COMBUSTIBLE DE ACUERDO CON PROCEDIMIENTOS Y PARÁMETROS DEL CICLO DIESEL', NULL, 838100, NULL, NULL),
(39115, 'COMPETENCIA TECNICA', 1152, 'MECANIZAR PIEZA INDUSTRIAL DE ACUERDO CON SISTEMA DE CONTROL NUMÉRICO', NULL, 821100, NULL, NULL),
(39118, 'COMPETENCIA TECNICA', 432, 'MECANIZAR PIEZA INDUSTRIAL DE ACUERDO CON TÉCNICAS MANUALES Y SEMIAUTOMÁTICAS', NULL, 821100, NULL, NULL),
(39125, 'COMPETENCIA TECNICA', 96, 'ALISTAR MÁQUINA HERRAMIENTA DE CONTROL NUMÉRICO DE ACUERDO CON ESPECIFICACIONES TÉCNICAS', NULL, 821100, NULL, NULL),
(39149, 'COMPETENCIA TECNICA', 240, 'Armar refuerzos estructurales de acuerdo con planos y reglamento técnico de construcción', NULL, 836138, NULL, NULL),
(39154, 'COMPETENCIA TECNICA', 144, 'PULIR PIEZAS INDUSTRIALES DE ACUERDO CON TÉCNICAS MANUALES Y MECÁNICAS', NULL, 821100, NULL, NULL),
(39160, 'COMPETENCIA TECNICA', 96, 'REPARAR ESTRUCTURAS DE MADERA DE ACUERDO CON PLANOS ARQUITECTÓNICOS Y ESPECIFICACIONES TÉCNICAS.', NULL, 836136, NULL, NULL),
(39179, 'COMPETENCIA TECNICA', 96, 'INSTALAR ACOMETIDAS ELÉCTRICAS DE ACUERDO CON REGLAMENTO TÉCNICO', NULL, 832202, NULL, NULL),
(39192, 'COMPETENCIA TECNICA', 48, 'MONTAR SISTEMAS DE PUESTA A TIERRA DE ACUERDO CON NORMATIVA', NULL, 832202, NULL, NULL),
(39192, 'COMPETENCIA TECNICA', 48, 'MONTAR SISTEMAS DE PUESTA A TIERRA DE ACUERDO CON NORMATIVA', NULL, 832303, NULL, NULL),
(39197, 'COMPETENCIA TECNICA', 144, 'Mantener equipos de generación de acuerdo con procedimientos técnicos', NULL, 832300, NULL, NULL),
(39202, 'COMPETENCIA TECNICA', 528, 'INSTALAR REDES HIDROSANITARIAS DE ACUERDO CON PROCEDIMIENTOS TÉCNICOS Y NORMATIVA', NULL, 833100, NULL, NULL),
(39202, 'COMPETENCIA TECNICA', 96, 'INSTALAR REDES HIDROSANITARIAS DE ACUERDO CON PROCEDIMIENTOS TÉCNICOS Y NORMATIVA', NULL, 836138, NULL, NULL),
(39218, 'COMPETENCIA TECNICA', 96, 'Preparar concretos y morteros según normativa y especificaciones técnicas de construcción', NULL, 836138, NULL, NULL),
(39234, 'COMPETENCIA TECNICA', 240, 'EVALUAR INSTALACIONES DE GAS SEGÚN PROCEDIMIENTOS TÉCNICOS Y NORMATIVA', NULL, 821307, NULL, NULL),
(39244, 'COMPETENCIA TECNICA', 432, 'EVALUAR FUNCIONAMIENTO DE EQUIPOS A GAS DE ACUERDO CON PROCEDIMIENTOS TÉCNICOS Y NORMATIVA', NULL, 821307, NULL, NULL),
(39244, 'COMPETENCIA TECNICA', 216, 'EVALUAR FUNCIONAMIENTO DE EQUIPOS A GAS DE ACUERDO CON PROCEDIMIENTOS TÉCNICOS Y NORMATIVA', NULL, 833301, NULL, NULL),
(39250, 'COMPETENCIA TECNICA', 144, 'PROGRAMAR MANTENIMIENTO SEGÚN PROCEDIMIENTOS TÉCNICOS Y MANUAL DE FUNCIONES', NULL, 821620, NULL, NULL),
(39251, 'COMPETENCIA TECNICA', 240, 'COORDINAR MANTENIMIENTO DE ACUERDO CON PROCEDIMIENTOS TÉCNICOS', NULL, 821620, NULL, NULL),
(39295, 'COMPETENCIA TECNICA', 192, 'Manejar soldadura de eléctrodo tungsteno de acuerdo con procedimiento técnico y normativa', NULL, 834258, NULL, NULL),
(39303, 'COMPETENCIA TECNICA', 564, 'Instalar sistemas constructivos en seco de acuerdo con planos y especificaciones técnicas', NULL, 836135, NULL, NULL),
(39303, 'COMPETENCIA TECNICA', 192, 'Instalar sistemas constructivos en seco de acuerdo con planos y especificaciones técnicas', NULL, 836137, NULL, NULL),
(39319, 'COMPETENCIA TECNICA', 96, 'Montar componentes eléctricos de acuerdo con procedimiento técnico', NULL, 832102, NULL, NULL),
(39319, 'COMPETENCIA TECNICA', 240, 'Montar componentes eléctricos de acuerdo con procedimiento técnico', NULL, 832202, NULL, NULL),
(39319, 'COMPETENCIA TECNICA', 96, 'Montar componentes eléctricos de acuerdo con procedimiento técnico', NULL, 832303, NULL, NULL),
(39319, 'COMPETENCIA TECNICA', 288, 'Montar componentes eléctricos de acuerdo con procedimiento técnico', NULL, 832402, NULL, NULL),
(39341, 'COMPETENCIA TECNICA', 1248, 'Reparar equipos según procedimiento y manuales técnicos', NULL, 223206, NULL, NULL),
(39341, 'COMPETENCIA TECNICA', 960, 'Reparar equipos según procedimiento y manuales técnicos', NULL, 223213, NULL, NULL),
(39393, 'COMPETENCIA TECNICA', 96, 'Acondicionar motocicletas de acuerdo con procedimientos técnicos y normativas', NULL, 838318, NULL, NULL),
(39394, 'COMPETENCIA TECNICA', 144, 'Diagnosticar motocicletas de acuerdo con procedimientos y parámetros técnicos', NULL, 838318, NULL, NULL),
(39395, 'COMPETENCIA TECNICA', 288, 'Reparar motocicletas de acuerdo con procedimientos y parámetros técnicos', NULL, 838318, NULL, NULL),
(39430, 'COMPETENCIA TECNICA', 144, 'Mantener sistemas eléctricos de distribución desenergizadas de acuerdo con normativa', NULL, 832303, NULL, NULL),
(39431, 'COMPETENCIA TECNICA', 144, 'Montar instalaciones eléctricas internas de acuerdo con normativa', NULL, 223213, NULL, NULL),
(39431, 'COMPETENCIA TECNICA', 192, 'Montar instalaciones eléctricas internas de acuerdo con normativa', NULL, 832102, NULL, NULL),
(39431, 'COMPETENCIA TECNICA', 288, 'Montar instalaciones eléctricas internas de acuerdo con normativa', NULL, 832202, NULL, NULL),
(39508, 'COMPETENCIA TECNICA', 144, 'Elaborar el automatismo del sistema mecatrónico de acuerdo con especificaciones técnicas', NULL, 832102, NULL, NULL),
(39536, 'COMPETENCIA TECNICA', 336, 'Armar estructuras de guadua según planos y especificaciones técnicas							 							', NULL, 836136, NULL, NULL),
(39592, 'COMPETENCIA TECNICA', 336, 'Caracterizar bienes inmuebles de acuerdo con normativa valuatoria', NULL, 225314, NULL, NULL),
(39635, 'COMPETENCIA TECNICA', 144, 'Instalar pavimentos articulados de acuerdo con procedimiento y normativa técnica', NULL, 861100, NULL, NULL);
INSERT INTO `competencia` (`comp_id`, `comp_nombre_corto`, `comp_horas`, `comp_nombre_unidad_competencia`, `centro_formacion_cent_id`, `programa_prog_id`, `requisitos_academicos`, `experiencia_laboral`) VALUES
(39638, 'COMPETENCIA TECNICA', 192, 'Pintar superficie de acuerdo con procedimiento y ficha técnica', NULL, 836137, NULL, NULL),
(39638, 'COMPETENCIA TECNICA', 192, 'Pintar superficie de acuerdo con procedimiento y ficha técnica', NULL, 836600, NULL, NULL),
(39643, 'COMPETENCIA TECNICA', 144, 'Instalar cubiertas de acuerdo con tipo de diseño y normativa técnica', NULL, 836137, NULL, NULL),
(39643, 'COMPETENCIA TECNICA', 144, 'Instalar cubiertas de acuerdo con tipo de diseño y normativa técnica', NULL, 836138, NULL, NULL),
(39740, 'COMPETENCIA TECNICA', 144, 'Intervenir el sistema de refrigeración según manuales de buenas prácticas en refrigeración y tipo de refrigerante', NULL, 837501, NULL, NULL),
(39743, 'COMPETENCIA TECNICA', 168, 'Montar sistemas de energía renovable de acuerdo con procedimiento técnico y normativa', NULL, 832300, NULL, NULL),
(39744, 'COMPETENCIA TECNICA', 96, 'Desensamblar residuos de aparatos eléctricos y electrónicos de acuerdo con normativa y procedimientos técnicos', NULL, 839317, NULL, NULL),
(39746, 'COMPETENCIA TECNICA', 192, 'Instalar sistemas de climatización y refrigeración de acuerdo con especificaciones técnicas y manuales de fabricantes', NULL, 837501, NULL, NULL),
(39747, 'COMPETENCIA TECNICA', 192, 'Mantener sistemas de climatización y refrigeración según procedimientos y normativa técnica', NULL, 837501, NULL, NULL),
(39799, 'COMPETENCIA TECNICA', 48, 'Soldar tubería metálica de acuerdo con procedimiento técnico', NULL, 837501, NULL, NULL),
(39811, 'EMPRENDIMIENTO', 48, 'Fomentar cultura emprendedora según habilidades y competencias personales', NULL, 224315, NULL, NULL),
(39811, 'EMPRENDIMIENTO', 48, 'Fomentar cultura emprendedora según habilidades y competencias personales', NULL, 225224, NULL, NULL),
(39811, 'EMPRENDIMIENTO', 48, 'Fomentar cultura emprendedora según habilidades y competencias personales', NULL, 225314, NULL, NULL),
(39811, 'EMPRENDIMIENTO', 48, 'Fomentar cultura emprendedora según habilidades y competencias personales', NULL, 664212, NULL, NULL),
(39811, 'EMPRENDIMIENTO', 48, 'Fomentar cultura emprendedora según habilidades y competencias personales', NULL, 832202, NULL, NULL),
(39811, 'EMPRENDIMIENTO', 48, 'Fomentar cultura emprendedora según habilidades y competencias personales', NULL, 832303, NULL, NULL),
(39811, 'EMPRENDIMIENTO', 48, 'Fomentar cultura emprendedora según habilidades y competencias personales', NULL, 836137, NULL, NULL),
(39811, 'EMPRENDIMIENTO', 48, 'Fomentar cultura emprendedora según habilidades y competencias personales', NULL, 836138, NULL, NULL),
(39811, 'EMPRENDIMIENTO', 48, 'Fomentar cultura emprendedora según habilidades y competencias personales', NULL, 836140, NULL, NULL),
(39811, 'EMPRENDIMIENTO', 48, 'Fomentar cultura emprendedora según habilidades y competencias personales', NULL, 837501, NULL, NULL),
(39811, 'EMPRENDIMIENTO', 48, 'Fomentar cultura emprendedora según habilidades y competencias personales', NULL, 839317, NULL, NULL),
(39811, 'EMPRENDIMIENTO', 48, 'Fomentar cultura emprendedora según habilidades y competencias personales', NULL, 845102, NULL, NULL),
(39811, 'EMPRENDIMIENTO', 48, 'Fomentar cultura emprendedora según habilidades y competencias personales', NULL, 861100, NULL, NULL),
(39877, 'COMPETENCIA TECNICA', 96, 'Asistir personas de acuerdo con guías de atención y protocolos de primer respondiente', NULL, 664212, NULL, NULL),
(39896, 'COMPETENCIA TECNICA', 144, 'Ensamblar tarjetas electrónicas según normativa y documentación técnica', NULL, 839317, NULL, NULL),
(40051, 'COMPETENCIA TECNICA', 144, 'Armar andamios según especificaciones técnicas y normativa de trabajo en alturas', NULL, 836140, NULL, NULL),
(40141, 'COMPETENCIA TECNICA', 48, 'Atender clientes de acuerdo con procedimiento de servicio y normativa', NULL, 837501, NULL, NULL),
(40307, 'COMPETENCIA TECNICA', 144, 'Enchapar superficies de acuerdo con especificaciones técnicas de construcción y lineamientos de acabados', NULL, 836137, NULL, NULL),
(40308, 'COMPETENCIA TECNICA', 144, 'Levantar muros no estructurales de acuerdo con planos y normativa de construcción', NULL, 836137, NULL, NULL),
(40308, 'COMPETENCIA TECNICA', 240, 'Levantar muros no estructurales de acuerdo con planos y normativa de construcción', NULL, 836140, NULL, NULL),
(40309, 'COMPETENCIA TECNICA', 240, 'Levantar muros en mampostería estructural de acuerdo con planos y normativa técnica de construcción', NULL, 836138, NULL, NULL),
(40385, 'COMPETENCIA TECNICA', 240, 'Representar proyectos de construcción según especificaciones de diseño y normativa técnica', NULL, 225224, NULL, NULL),
(40443, 'COMPETENCIA TECNICA', 480, 'Implementar sistemas de gestión según normativa y requerimientos técnicos', NULL, 226701, NULL, NULL),
(40539, 'COMPETENCIA TECNICA', 192, 'Preparar materiales de construcción de acuerdo con procedimiento y especificaciones técnicas', NULL, 836140, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `competencia_horas_programa`
--

CREATE TABLE `competencia_horas_programa` (
  `prog_codigo` int(11) NOT NULL,
  `comp_id` int(11) NOT NULL,
  `horas_requeridas` int(11) NOT NULL DEFAULT 0,
  `aplica` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `coordinacion`
--

CREATE TABLE `coordinacion` (
  `coord_id` int(11) NOT NULL,
  `coord_descripcion` varchar(255) NOT NULL,
  `centro_formacion_cent_id` int(11) DEFAULT NULL,
  `estado` smallint(6) NOT NULL DEFAULT 1,
  `coordinador_actual` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `coordinacion`
--

INSERT INTO `coordinacion` (`coord_id`, `coord_descripcion`, `centro_formacion_cent_id`, `estado`, `coordinador_actual`) VALUES
(2, 'Industria', 9537, 1, 13276499),
(3, 'Moda, Tecnología y Turismo', 9537, 1, 12345),
(4, 'Comercio', 9537, 1, 67890),
(5, 'Programas Especiales', 9537, 1, 234),
(6, 'Ocaña', 9537, 1, 567);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallexasignacion`
--

CREATE TABLE `detallexasignacion` (
  `detasig_id` int(11) NOT NULL,
  `asignacion_asig_id` int(11) DEFAULT NULL,
  `detasig_hora_ini` time NOT NULL,
  `detasig_hora_fin` time NOT NULL,
  `detasig_fecha` date NOT NULL DEFAULT curdate(),
  `observaciones` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `detallexasignacion`
--

INSERT INTO `detallexasignacion` (`detasig_id`, `asignacion_asig_id`, `detasig_hora_ini`, `detasig_hora_fin`, `detasig_fecha`, `observaciones`) VALUES
(65, 3, '12:00:00', '18:00:00', '2026-06-01', NULL),
(66, 3, '12:00:00', '18:00:00', '2026-06-02', NULL),
(67, 3, '12:00:00', '18:00:00', '2026-06-03', NULL),
(68, 3, '12:00:00', '18:00:00', '2026-06-04', NULL),
(69, 3, '12:00:00', '18:00:00', '2026-06-05', NULL),
(70, 4, '08:00:00', '12:00:00', '2026-06-08', NULL),
(71, 4, '08:00:00', '12:00:00', '2026-06-09', NULL),
(72, 4, '08:00:00', '12:00:00', '2026-06-10', NULL),
(73, 4, '08:00:00', '12:00:00', '2026-06-11', NULL),
(74, 4, '08:00:00', '12:00:00', '2026-06-12', NULL),
(75, 4, '08:00:00', '12:00:00', '2026-06-13', NULL),
(76, 5, '06:00:00', '12:00:00', '2026-06-01', NULL),
(77, 5, '06:00:00', '12:00:00', '2026-06-02', NULL),
(78, 5, '06:00:00', '12:00:00', '2026-06-03', NULL),
(79, 5, '06:00:00', '12:00:00', '2026-06-04', NULL),
(80, 5, '06:00:00', '12:00:00', '2026-06-05', NULL),
(81, 5, '06:00:00', '12:00:00', '2026-06-06', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fase_proyecto`
--

CREATE TABLE `fase_proyecto` (
  `fase_id` int(11) NOT NULL,
  `fase_nombre` varchar(255) NOT NULL,
  `fase_orden` smallint(6) NOT NULL,
  `fase_fecha_ini` date NOT NULL,
  `fase_fecha_fin` date NOT NULL,
  `pf_pf_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ficha`
--

CREATE TABLE `ficha` (
  `fich_id` int(11) NOT NULL,
  `programa_prog_id` int(11) DEFAULT NULL,
  `instructor_inst_id_lider` bigint(20) DEFAULT NULL,
  `fich_jornada` varchar(100) NOT NULL,
  `coordinacion_coord_id` int(11) DEFAULT NULL,
  `fich_fecha_ini_lectiva` date NOT NULL,
  `fich_fecha_fin_lectiva` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ficha`
--

INSERT INTO `ficha` (`fich_id`, `programa_prog_id`, `instructor_inst_id_lider`, `fich_jornada`, `coordinacion_coord_id`, `fich_fecha_ini_lectiva`, `fich_fecha_fin_lectiva`) VALUES
(3064681, 821620, 88235820, 'Noche', 2, '2024-10-15', '2026-07-14'),
(3064740, 226701, 1090397615, 'Noche', 2, '2024-10-15', '2026-07-14'),
(3064772, 225311, 88310200, 'Mañana', 2, '2024-10-15', '2026-07-14'),
(3064775, 223206, 60376269, 'Mañana', 2, '2024-10-15', '2026-07-14'),
(3064778, 821202, 5483059, 'Mañana', 2, '2024-10-15', '2026-07-14'),
(3173594, 224312, 88251027, 'Tarde', 2, '2025-04-29', '2027-01-28'),
(3173898, 226701, 1090411533, 'Mañana', 2, '2025-04-29', '2027-01-28'),
(3173916, 224201, 88213759, 'Mañana', 2, '2025-04-29', '2027-01-28'),
(3173923, 821100, 1067710285, 'Noche', 2, '2025-04-29', '2027-01-28'),
(3228917, 225311, 60390516, 'Mañana', 2, '2025-07-25', '2027-04-23'),
(3228932, 821203, 88248787, 'Tarde', 2, '2025-07-25', '2027-04-23'),
(3228956, 226701, 1093737984, 'Tarde', 2, '2025-07-25', '2027-04-23'),
(3287501, 226701, 13275556, 'Noche', 2, '2025-07-25', '2027-04-23'),
(3314175, 838100, 88235820, 'Tarde', 2, '2026-02-01', '2026-12-01'),
(3314188, 836600, 5488352, 'Mañana', 2, '2025-10-09', '2026-07-09'),
(3410938, 833100, 88273231, 'Mañana', 2, '2026-03-05', '2026-12-04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instructor`
--

CREATE TABLE `instructor` (
  `numero_documento` bigint(20) NOT NULL,
  `inst_nombres` varchar(255) NOT NULL,
  `inst_apellidos` varchar(255) NOT NULL,
  `inst_correo` varchar(255) NOT NULL,
  `inst_telefono` bigint(20) NOT NULL,
  `centro_formacion_cent_id` int(11) DEFAULT NULL,
  `inst_password` varchar(255) NOT NULL,
  `profesion` varchar(150) DEFAULT NULL,
  `especializacion` varchar(150) DEFAULT NULL,
  `estado` smallint(6) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `instructor`
--

INSERT INTO `instructor` (`numero_documento`, `inst_nombres`, `inst_apellidos`, `inst_correo`, `inst_telefono`, `centro_formacion_cent_id`, `inst_password`, `profesion`, `especializacion`, `estado`) VALUES
(535348, 'ARMANDO JOSE', 'ARELLANO HEVIA', 'ajarellano@sena.edu.co', 3115502766, 9537, '$2y$10$eNPeE93CHPq49bFn5WU/TeXaI9uC6Ng7Nagp6DBdEssa7rrifpFq2', '', 'BILINGUISMO', 1),
(5435455, 'LUIS DARIO', 'CONTRERAS BAUTISTA', 'lcontrerasb@sena.edu.co', 3112139536, 9537, '$2b$10$jzufxiCd4c93BtTwroVI..c8USDvMFKD7J6M6absNNNNoWaz.w8VS', NULL, 'CONSTRUCCIÓN', 1),
(5483059, 'JOSE ELIECER', 'DIAZ GARCIA', 'je.diazg@sena.edu.co', 3006228667, 9537, '$2b$10$LLw5UPeuDnxBGiiKY3Ndwua9rj5ApUdpCq4qZHQEcEN9UAI4b0t0a', NULL, 'REDES ELECTRICAS', 1),
(5488352, 'Olger Eduardo', 'Torrado Obregon', 'otorrado@sena.edu.co', 3218821563, 9537, '$2b$10$oGZqwCOgXw7tPkVLGcQGAOCro7woSqe5WSfAYrOWW1oGJOBQQ5B2C', NULL, 'Arquitectura y decoración', 1),
(5501837, 'ORFEL', 'ORTIZ BACCA', 'oortizb@sena.edu.co', 3102388641, 9537, '$2b$10$OwM6dwHr3xmhnTcqaWi6xuTxcV2gGZR.ro2qgFzAWyxp49sGxFdIe', NULL, 'MOTOS', 1),
(5645917, 'Feissan Alonso', 'Gerena Mateus', 'FAGERENA@sena.edu.co', 3108703618, 9537, '$2b$10$1ufy5WSqhwCL5MqShaY85OAOma8LivYpbILbmj7mHcjZs38obWWbi', NULL, 'Redes cableadas para telecomunicaciones', 1),
(7602358, 'OSWALDO', 'HURTADO FIGUEROA', 'ohurtado@sena.edu.co', 3143026758, 9537, '$2b$10$EgdRlwEFkVp3r/v.ys2JVeLYZUQlyE/XCLSYTtjlAsfSNecmZkMGi', NULL, 'CONSTRUCCIÓN', 1),
(13198431, 'JAVIER MAURICIO', 'ESPINEL NAVAS', 'jespineln@sena.edu.co', 3125794176, 9537, '$2b$10$xESZHqhovLrtabdwFc29jeryRfk1YKkN93s5ngPxOSdAskTB5IqqO', NULL, 'ELECTRICIDAD INDUSTRIAL', 1),
(13232422, 'BENJAMIN', 'OTERO HERNANDEZ', 'boteroh@sena.edu.co', 3188440767, 9537, '$2b$10$t9ksIuhU/Rdc3c6scsZQUep3aq.0J0PCX79VntqwtwFSx3kjhsM8.', NULL, 'AUTOMATIZACIÓN', 1),
(13233053, 'ALFONSO LOPEZ', 'LOPEZ BECERRA', 'allopezb@sena.edu.co', 3052361206, 9537, '$2b$10$6vLTC0H.EFbeyTAfCrqjI.fCdzOdX6bv.P3XGqyHpka9tXOPhMHBC', NULL, 'ELECTRICIDAD INDUSTRIAL', 1),
(13270395, 'Jhon Edward', 'Lizarazo Parada', 'jlizarazop@sena.edu.co', 3202425856, 9537, '$2b$10$BIdMk6gfVzL33bTwPi17qOc6qVydlIQoQ1ksqRmlsGEEGa2hT1GwK', NULL, 'Mecatronica', 1),
(13270719, 'Andrés Mauricio', 'Puentes Velásquez', 'apuentesv@sena.edu.co', 3015782641, 9537, '$2y$10$13EHNAyZjQBgXUraLRMEW..jboEN3Z4TQPhexpnqRuySAyeAvYpPq', 'Ingeniero de Sistemas', 'Maestría en Ing de Sistemas y Computación', 1),
(13275556, 'JOSE FERNANDO', 'MORA MIRANDA', 'jfmoram@sena.edu.co', 3164931419, 9537, '$2b$10$fWEyoksiRdl3hkTycHVpUO56/ue6BZLX2zpHMtFSQLwQWE2Fml8KO', NULL, 'COORDINACION SISTEMAS INTEGRADOS GESTION', 1),
(13276499, 'Javier Fernando', 'Arenales Bernal', 'jarenalesb@sena.edu.co', 3007725519, 9537, '$2b$10$ViiD6eqLaru9mCbNtfQNBO/boVBW4JIobwcQQT8WOovmkZ.cx7nki', NULL, 'Mecatronica', 1),
(13276615, 'Mauricio Santiago', 'Rodriguez', 'msantiagor@sena.edu.co', 3183093618, 9537, '$2b$10$PFITGBEjJmJ1iPp/Qpo/FeAaw9qaHTzmilqTRkxBDtTu8yJm1Zqle', NULL, 'Uso final de la energia electrica', 1),
(13436369, 'JUAN DE DIOS', 'CARRILLO HIGUERA', 'jdcarrillo@sena.edu.co', 3241067900, 9537, '$2b$10$Zq8GXg9UTpkxyauTXlp5LeRUFiW6KBL86JXywLJ55TlgJGnN5lVAu', NULL, 'SOLDADURA', 1),
(13476837, 'Ricardo José', 'Ariza Raad', 'rarizar@sena.edu.co', 3173818983, 9537, '$2b$10$RTViI0JbyA.JkcjZHbRvie1pbwEBb3GLlTFxe1SB1rASTr0tOw6Sm', NULL, 'Construcción', 1),
(13484766, 'LUIS ENRIQUE', 'REYES LOZANO', 'lreyes@sena.edu.co', 3504828898, 9537, '$2b$10$QfsjjY4AQWwg2p7jFN6NH.xoER4Sf5W8Hwg/.vfb1LMRUiDbhTJGy', NULL, 'MOTOS', 1),
(13487149, 'Jorge Agustin', 'Baron Soto', 'jbarons@sena.edu.co', 3002541830, 9537, '$2b$10$wvSdppfPKIEZ1TSoT5BCWOEdFY7K5ne.Q/s3zfCDtXq7f6hr6zYOu', NULL, 'Arquitectura y decoración', 1),
(13502469, 'Pablo Antonio', 'Mojica Mendoza', 'pamojica@sena.edu.co', 3142898603, 9537, '$2b$10$ymshnt.6B5nSnBNkimIVSO3qdbzxNzrX/SFZBTyBk/q4VJGoS87fO', NULL, 'Motocicletas', 1),
(13509804, 'GERARDO', 'FLOREZ GOMEZ', 'gflorez@sena.edu.co', 3105592014, 9537, '$2y$10$ZZgaTiqYxphb1ombgoyJ.uVIk/nAvMd5OYTR55osDXICTHcnkwfwK', '', 'DERECHOS FUNDAMENTALES', 1),
(27591762, 'LUISA MAIRA', 'MENDOZA VALDERRAMA', 'lmendoza@sena.edu.co', 3014679016, 9537, '$2b$10$rELFQhYJolE6zHSvtnPK3.9AEe713je27yLbjEbDuMjGI8kgBGsi2', NULL, 'TSA', 1),
(27737690, 'MINFA SUSANA', 'LEAL VILLAMIZAR', 'msleal@sena.edu.co', 3202436520, 9537, '$2b$10$ZTUFTXRyJkkkJHCUwoJmJ.xPrcWitMCxvdVasUwDc.FxVxo6R/vrC', NULL, 'CONFECCIONES', 1),
(37291447, 'ADRIANA CAROLINA', 'ARCINIEGAS TORRES', 'aarciniegas@sena.edu.co', 3124035086, 9537, '$2b$10$DpmhSLzfGUz2QtGDJJ0QAube5EdLyllz.6smrbXRIK0mHXz9Oe5oO', NULL, 'SEGUIMIENTO ETAPA PRODUCTIVA', 1),
(37395403, 'MONICA JOHANNA', 'VELASCO TARAZONA', 'mjvelasco@sena.edu.co', 3017751067, 9537, '$2y$10$TJdfJ45I1NoPWcWkDsDwW.2Zfkro72bPENSy05QPcm.yUw2G3sSvK', '', 'BILINGUISMO', 1),
(37397194, 'Dadny Rocio', 'Dominguez Palencia', 'ddominguez@sena.edu.co', 3204958440, 9537, '$2b$10$LP51CabDeeOXXj3NJpcbg.KHQA39VKU.mn.Bi1PnKO5KE6uUDOp3q', NULL, 'Construcción', 1),
(51967026, 'JACQUELINE', 'MONTOYA PUERTO', 'jmontoyap@sena.edu.co', 3134157454, 9537, '$2y$10$34q0um1KrqUyLmGfMg6uiufjGeIUCA8G1ML/rez3QKf3AHTpU/Qg2', '', 'ETICA', 1),
(60301504, 'Patricia Sanchez', 'Perez', 'psanchezp@sena.edu.co', 3112386456, 9537, '$2y$10$nPbbANG1nSUSblRtRrzeeediZigU6Tig0xbCuLBv3KXdpZPtySNHq', '', 'Interacción consigo mismo, con los demas, con la naturaleza y con la transcendencia', 1),
(60354988, 'MARITZA CRUZ', 'RESTREPO CHAUSTRE', 'mrestrepoc@sena.edu.co', 3183867452, 9537, '$2b$10$ogc.5uVnNW98L9MT1zPI3OhSL/YwUZAhZ4dDzaoX9dB6veEDEUjsq', NULL, 'SEGUIMIENTO ETAPA PRODUCTIVA', 1),
(60365097, 'STELLA YAMILE', 'COVILLA ORTIZ', 'scovilla@sena.edu.co', 3203375919, 9537, '$2y$10$JZMIAwGi1nd1l4g2yODVxuhuNB74zfYSegTRPkiEksGCQZvMmxVXi', '', 'MEDIO AMBIENTE', 1),
(60366085, 'Claudia Milena', 'Parra Niño', 'cmparra@sena.edu.co', 3142929869, 9537, '$2b$10$75WLtLsthCkIopklFXyYI.lrK3UE4Vkm1Je.rnM83.GczWIkT0jwG', NULL, 'Seguridad y Salud en el trabajo', 1),
(60376269, 'FRANCY YOHANA', 'BEJARANO ROJAS', 'fbejarano@sena.edu.co', 3014170125, 9537, '$2b$10$t5tNknMhhOHlzEie1SkMgOQjmIDPVTrhyg5NElvV9RUjpvDRHqgTe', NULL, 'MANTENIMIENTO MECANICO', 1),
(60390516, 'Sandra Yaneth', 'Maldonado Gomez', 'smaldonado@sena.edu.co', 3125170252, 9537, '$2b$10$k/mOZN2FrNzuX/evgfBUTOK8AhM5fZnY4MNL9/qgBuMFOXE4BjUtu', NULL, 'Topografia', 1),
(60421324, 'YOLANDA VIVIANA', 'CASTELLANOS ROMERO', 'yvcastellanos@sena.edu.co', 3114827382, 9537, '$2b$10$tIgXrlhyaqCwvhRHOhnuVOX.SZ13HTKAWXvQo86l6IcGDwvMblv6m', NULL, 'TSA', 1),
(60444546, 'LUZ KARIME', 'LOPEZ GALEANO', 'lulopezg@sena.edu.co', 3126334155, 9537, '$2y$10$aDOjm0Hc8KQtU8QUDXGbTu.zQUqkHwXEMsPUY7IQNbVcXShVPulUy', '', 'COMUNICACIÓN', 1),
(80227809, 'JAIRO ANDRES', 'TRIANA LESMES', 'jatrianal@sena.edu.co', 3188708510, 9537, '$2y$10$qWwA/rAx/v3Icub7ZjNv9OCiKrRHY6XzC8gBEVMj0S20XGhF0xLiq', '', 'EMPRENDIMIENTO', 1),
(88158545, 'Nelson Guerrero', 'Santafe', 'nguerreros@sena.edu.co', 3163764468, 9537, '$2b$10$Xaa7QNbGx/vKjmvoPj07FOkUi5zJGAET628eaiM5vx5d6b9inOYO2', NULL, 'Mecatronica Automotriz', 1),
(88160803, 'LEONEL IVAN', 'VILLAMIZAR RAMIREZ', 'lvillamizar@sena.edu.co', 3186347103, 9537, '$2y$10$LiO31lURGBfeGnB1Xn0FDu0S.KgeSI8N790yYhpPh0s284Gb.02UW', '', 'CULTURA FISICA', 1),
(88211022, 'NELSON ENRIQUE', 'ACEVEDO ALVAREZ', 'nacevedoa@sena.edu.co', 3144210386, 9537, '$2y$10$Motar/93FdfsavncJJUm.uQIsAYujqNnxr56R5D0JQiMc7wxQ21R6', '', 'CULTURA FISICA', 1),
(88213759, 'JUAN MANUEL', 'ARENAS GALVIS', 'jarenas@sena.edu.co', 3188688743, 9537, '$2b$10$C/ldmShkcCR4mygTFpzAO.pSPYwJGSNLv5Bc81c5ahkWDbitrU0ju', NULL, 'ELECTRONICA', 1),
(88217480, 'Yorgos Yofrey', 'Ramirez Perez', 'yramirezp@sena.edu.co', 3125860645, 9537, '$2b$10$WXLH2h9RTZRoJPCec3e0A.vI88gkCe6N61eaXuoc1ghk98aKP/Aum', NULL, 'Distribución de la energia electrica', 1),
(88217769, 'Freddy Oswaldo', 'Ovalles Pabon', 'fovallesp@sena.edu.co', 3157265199, 9537, '$2y$10$AMvGnhcJr/RkQEWicv63v.3chGk7O10epefk8utmtvcvE0iYAeVL.', '', 'Adiminstracción de proyectos', 1),
(88220104, 'Rudy Hernando', 'Cortes Giron', 'rcortesg@sena.edu.co', 3214682798, 9537, '$2b$10$y.LF52HHzGs/0VPJbxJ7ou53bDukJNKvNzzj7EssE4IG8JkIG24SG', NULL, 'Mecatronica Automotriz', 1),
(88230069, 'JOHN JAIRO', 'MARTINEZ SANCHEZ', 'jjmartinezs@sena.edu.co', 3006329194, 9537, '$2b$10$zEqkmYy/4hCOmHg4VQ5O6eRnyspqsSAkVv0hcKaH8yzTQbiyty/z2', NULL, 'CONSTRUCCIÓN', 1),
(88231232, 'Edgar Fernando', 'Araque Orozco', 'earaqueo@sena.edu.co', 3118612326, 9537, '$2b$10$XhkLGAto79ibIfk4N162t.l.rkJ08orN8QL.Vy6la2fFJu/qbq5d6', NULL, 'Electronica', 1),
(88235820, 'JHON ALEXANDER', 'FERRER MEZA', 'jaferrerm@sena.edu.co', 3002558856, 9537, '$2b$10$KdBe4ZxiC.oXmozrL/DvzuDv20wuuXcrEXZKNKned9q5EMaB.dGLy', NULL, 'AUTOMOTRÍZ', 1),
(88240235, 'Manuel Guillermo', 'Cardozo Montes', 'mcardozom@sena.edu.co', 3209153721, 9537, '$2b$10$/fQpxfde6Cm5pmXnzHzcxOfz2I/opj763fC5ywpbjodrPVNzgWkFW', NULL, 'Mecatronica Automotriz', 1),
(88240965, 'ALEXANDER', 'SANCHEZ SANCHEZ', 'alsanchezs@sena.edu.co', 3124387228, 9537, '$2b$10$7HjfvwNGGIVvpE30wtqdR.joRwWRgXW6/9iHqfWARac5EkEOP9/Gm', NULL, 'VIAS', 1),
(88242181, 'Hector Enrique', 'Judex Balaguera', 'hjudex@sena.edu.co', 3164921170, 9537, '$2b$10$Noy2hmSUKT9OWvoxDBCA0uHi7X5dCtGYnRNLtX4r1aoPGP8gc90Nm', NULL, 'Mecanica industrial', 1),
(88243695, 'VICTOR MANUEL', 'BUENDIA FLOREZ', 'vbuendia@sena.edu.co', 3212487572, 9537, '$2b$10$jWVvHtUWBUee.Oz92ndNyu73n1E4zeJBBduNuxjmJ9LfwWAOuUnG.', NULL, 'PROMOTORIA CAMPESINA', 1),
(88243932, 'BERNARDO ANTONIO', 'RODRIGUEZ PINILLA', 'brodriguezp@sena.edu.co', 3104200626, 9537, '$2b$10$65tSKj9BnK/RzvUXHSY0Hu0odjfi0zl9UzNq5pno0NR8ZB/9O3vZK', NULL, 'REFRIGERACION', 1),
(88244316, 'MARLON', 'PINO TARAZONA', 'mpino@sena.edu.co', 3102000353, 9537, '$2y$10$LdRPeBxx9FJc6lFuLTYm3euCEw62EL1NtErdNCt3tHH7wNDHK9.6C', '', 'MATEMATICAS', 1),
(88244437, 'MIGUEL GOTARDO', 'RAMIREZ PEREZ', 'mgramirezp@sena.edu.co', 3505278527, 9537, '$2b$10$DDPh6yo7eOdaBgDkec46c.5WTIUiRjYdqsoFcAiZR95WwQWmxbW32', NULL, 'CONSTRUCCIÓN', 1),
(88248787, 'Mario Yesid', 'Veloza', 'myeveloza@sena.edu.co', 3014443104, 9537, '$2b$10$wRvpLmEM9U9h7hDUZnkVvuMu431qYSeX3MMPP5VNglOedQIMvMc8O', NULL, 'Redes cableadas para telecomunicaciones', 1),
(88251027, 'RAFAEL ANDRES', 'ACOSTA ROZO', 'raacosta@sena.edu.co', 3053251902, 9537, '$2b$10$6IZwcktCmsDZDcgCOqrliOM0ZFbqWqMNYxz.nEn3YwB1TzJdoqe3W', NULL, 'MECATRONICA', 1),
(88256591, 'WILLIAM JOSE', 'CAÑIZARES RUIZ', 'wjcanizares@sena.edu.co', 3115507964, 9537, '$2b$10$MyV8FOt404BnZnW39K63/Onq2nUL/dMhuVplQmF8CXNjip8bB0M9C', NULL, 'REFRIGERACION', 1),
(88259237, 'Nelson Julian', 'Cardenas Ortiz', 'ncardenaso@sena.edu.co', 3183418103, 9537, '$2b$10$G6MT0HdmO1kRv9KvX3wOp.hTtY4bt3pbYn0IaP1j/QzOoslycklaG', NULL, 'Soldadura', 1),
(88273231, 'Adolfo Leonardo', 'Rangel Cote', 'arangelc@sena.edu.co', 3002654143, 9537, '$2b$10$Gym4kj5CgDGZWORP5rQhGOq2gIRGHNe5MUzZH52QLXadvaPUqRGbe', NULL, 'Construcción', 1),
(88285095, 'WILLINTON', 'ARENIZ PACHECO', 'warenizp@sena.edu.co', 3165616617, 9537, '$2b$10$JszJl5Ov7wswKuYNQmTOK.lkzZ6EGbQzVkXcRzwbXLHXYzUOc6K/C', NULL, 'CONSTRUCCIÓN', 1),
(88310200, 'FERNANDO', 'JAIMES TARAZONA', 'fjaimes@sena.edu.co', 3112657221, 9537, '$2b$10$5bb2Hx.v4f.nT.LPea3B5ufUVLkanuYVZtpqsg55cTptg7KT1OJi2', NULL, 'TOPOGRAFÍA', 1),
(91217454, 'HERNANDO', 'GOMEZ PALENCIA', 'hgomezp@sena.edu.co', 3102327429, 9537, '$2b$10$YVM//62Z47n2n//8mgo.oOvXlp2uM47X3RNhTL1egwgT5udqc3MJm', NULL, 'ELECTRONICA', 1),
(91513199, 'Oscar José', 'Caceres Rincon', 'ocaceresr@sena.edu.co', 3102547343, 9537, '$2b$10$WK5OD/wBVUNNR/qf5IYESuVsqHlgT.ZOM8mhC7Kfs6EgwgtXmGs2a', NULL, 'Sistema integrado de Gestión', 1),
(92543537, 'YAIR FERNANDO', 'PALENCIA TRILLOS', 'yfpalencia@sena.edu.co', 3204118475, 9537, '$2b$10$NwlHDuQhPPpZ1jAMaUMdnuKHwGP7bq4s.Pem0UYS7KYhxkgks8z1e', NULL, 'TSA', 1),
(96168565, 'Leonardo', 'Mora Duarte', 'leomora@sena.edu.co', 3118134418, 9537, '$2b$10$VwgZXgkMA9l9hW1hxgAPq.glB8SK8KGpQ9hqn3SHYW.Qaz6yWO4K2', NULL, 'Soldadura', 1),
(1010232966, 'CAMILA ANDREA', 'CORZO FLOREZ', 'ccorzo@sena.edu.co', 3107736758, 9537, '$2y$10$JUJ7Ug2Ov6cOWMN2HurVT.XuCL4I.jfVfMWuhDW.z9RkmKgGyTN.C', '', 'BILINGUISMO', 1),
(1057574793, 'Fabián Alfonso', 'Plazas Martínez', 'faplazas@sena.edu.co', 3118749997, 9537, '$2b$10$3Qsq1iW945FMNH8SqfuIyO5XEzjWw6SLY3MS3S8HLAVHie1iiv5.e', NULL, 'mantenimiento mecánico industrial', 1),
(1065896797, 'AVIMELEC', 'CHINCHILLA', 'chinchilla@sena.edu.co', 3506553616, 9537, '$2b$10$wRdbV7H8YeWscrqCPruowOCyz0Muie5s92zStMgdJbaCVd5.VUhCO', NULL, 'CONSTRUCCIÓN', 1),
(1067710285, 'Dairo', 'Quintero Quintero', 'dquintero@sena.edu.co', 3147699976, 9537, '$2b$10$9GYYX62bwZPENUhyszOdpeOvSFpHc7Ofqh9iB/AQxE0QCI9C80feW', NULL, 'CNC', 1),
(1090373012, 'WILLIAM HUMBERTO', 'CHAVES RANGEL', 'whchaves@sena.edu.co', 3013164795, 9537, '$2b$10$jqhuthAqTG0G8nUbU44nMuKZYbj/tpgMO37UhZb3F86AyphWdba3G', NULL, 'CONSTRUCCIÓN', 1),
(1090382161, 'Saider Santiago', 'Perez', 'ssperez@sena.edu.co', 3204207186, 9537, '$2b$10$ya5gtXosddknQ6vbeTxc/ulA0NPQRHFRYJWtneL.TfORmUKBEqqha', NULL, 'Uso final de la energia electrica', 1),
(1090397615, 'JESUS RICARDO', 'CASTRO CABALLERO', 'jcastrocc@sena.edu.co', 3133444328, 9537, '$2b$10$rXPWPr62gkpiaAW3uRABgOEAwXazVNJZWmnKlo4OZe.vB/xqOV1fa', NULL, 'SST', 1),
(1090397641, 'MARIA ISABEL', 'MOLINA RIVERA', 'mimolinar@sena.edu.co', 3165301798, 9537, '$2y$10$Das6rgxm8V.QmUsdnUzkTuAAzjnO9k3.p1PNie8MJri.g/4P1Xqvu', '', 'CIENCIAS NATURALES', 1),
(1090401173, 'LUIS FERNEL', 'OSORIO PEREZ', 'losoriop@sena.edu.co', 3124520977, 9537, '$2y$10$6xB6NmZE.3/zWxKfFogU6.CyCT5yP1D5yu/JGC0e13wy7oQOOR956', '', 'DERECHOS FUNDAMENTALES', 1),
(1090407786, 'ALDO ANDRES', 'SUAREZ PEÑARANDA', 'asuarezp@sena.edu.co', 3165167990, 9537, '$2b$10$kOe40Qfux8xY2UHG2c.O7u6lWjnEF14yrNgqwfkUmgYO4tjLko3NS', NULL, 'INSTALACIONES ELECTRICAS', 1),
(1090411533, 'YOLANDA ANDREA', 'TURRIAGO GUTIERREZ', 'yaturriago@sena.edu.co', 3202059050, 9537, '$2b$10$fMzR93lT16v82SvZvLC1ieFXDvAkeHdQDgl81rOg5Lztv.tbmkEIG', NULL, 'COORDINACION SISTEMAS INTEGRADOS GESTION', 1),
(1090415739, 'MARIA DEL PILAR', 'MARTINEZ ROJAS', 'mdmartinezr@sena.edu.co', 3108486718, 9537, '$2b$10$4t27J.Nbc3b1DIIZXi5lO.1pfYRx/seIBDY0JDvmpwGxP1b3jHnbm', NULL, 'SEGUIMIENTO ETAPA PRODUCTIVA', 1),
(1090421209, 'SERGIO ENRIQUE', 'LAGUADO SIERRA', 'slaguados@sena.edu.co', 3134611992, 9537, '$2b$10$ArYb0Sj9kHGNFZpOuyJSc.5O93C2kbuKzhZD315q8WM1KYLyMdQgm', NULL, 'CONSTRUCCIÓN', 1),
(1090426564, 'YOLFAN MIGUEL', 'ABRIL CASADIEGOS', 'abrilc@sena.edu.co', 3163474608, 9537, '$2b$10$OQXg.okQ/5NaGMXZZuZ1u.ZxdQ6x8dvwKlwBlEuJlLnngMqNQj4qO', NULL, 'AUTOMOTRÍZ', 1),
(1090437106, 'ABIMELEC', 'PEÑARANDA BECERRA', 'apenaranda@sena.edu.co', 3143166468, 9537, '$2b$10$VKIwyLRCAwfs6awvrqS3S.EpoUEd8vuDwqQbjzXBXAsUQgHU4j5lu', NULL, 'MOTOS', 1),
(1090444770, 'DARLLY PAOLA', 'POLENTINO MUÑOZ', 'dpolentinom@sena.edu.co', 3163354473, 9537, '$2b$10$tcodbx.4PatQQDjoYZURPerN6ab0P8MNtsBJW5AH.Qwd3LAwsa.52', NULL, 'TSA', 1),
(1090457147, 'MARIA FERNANDA', 'GERENA DUARTE', 'mgerenad@sena.edu.co', 3107597886, 9537, '$2b$10$nrkT4qPYIctscm6YScDIsuvH6vWZLNG.ej2OjlHNq9EZej05ODWma', NULL, 'TELECOMUNICACIONES', 1),
(1090462158, 'JEISON EDUARDO', 'DIAZ RODRIGUEZ', 'jediazr@sena.edu.co', 3219540658, 9537, '$2y$10$8xWBBKaMbkEHHdlsdjZgt.V8WFNsu/UjR1u3LqmasASVHnu9NJFKG', '', 'TIC', 1),
(1090490528, 'EDGAR CASIANO', 'CAMARGO FUENTES', 'ecamargo@sena.edu.co', 3138796516, 9537, '$2b$10$w7ofK5i5mDRTz4.wCv4zdOOVQHqdYXTrinVWpgzv8QtGv4qgYpWEK', NULL, 'TELECOMUNICACIONES', 1),
(1090504966, 'HERNAN DARIO', 'DIAZ RESTREPO', 'hddiazr@sena.edu.co', 3202471935, 9537, '$2b$10$aVkLXVTIoc9ozCWKIu1JIeOYfCOzYHjHXL7AEUzi/3TEduDPuRXVm', NULL, 'AUTOMOTRÍZ', 1),
(1093734471, 'JUAN SEBASTIAN', 'GUERRERO ECHEVERRIA', 'jsguerrero@sena.edu.co', 3124137897, 9537, '$2y$10$/6J/ylahw7mq.ZZkMR112e38tugUJPZuyzVv/ZE6ndAkT6spXnqDS', '', 'COMUNICACIÓN', 1),
(1093737984, 'ANA MARIA', 'BLANCO RANGEL', 'amblanco@sena.edu.co', 3103366782, 9537, '$2b$10$gtJ05INVxl25OdIgGUnNCeNtWt7IPlml4GvrRo2IpYnEfMHdTk/x.', NULL, 'COORDINACION SISTEMAS INTEGRADOS GESTION', 1),
(1093738086, 'SANDRA LORENA', 'OROZCO CAMARGO', 'sorozcoc@sena.edu.co', 3142052103, 9537, '$2y$10$O6DSmGBCGrZYS6KTzERz6uQj37fcl/Cm6xyi16f5r9iBEULNyTY3G', '', 'SST', 1),
(1093741809, 'Pedro Antonio', 'Castro Mendoza', 'Pacastro@sena.edu.co', 3114798646, 9537, '$2b$10$6ddguB08vAR2edZT98sIoe348Dvt.nU6rO8UoApQ9joh8EE6Fa4hG', NULL, 'Operación de maquinaria pesada para excavación y movimiento de tierra', 1),
(1093747039, 'WILMER HARBEY', 'MORANTES MARTINEZ', 'wmorantes@sena.edu.co', 3134192158, 9537, '$2b$10$NTmguUiTO2ib/CpixI4tce0zjCIpAf9qEbPm/SWz7SKxGzzZ4ILq6', NULL, 'AUTOMOTRÍZ', 1),
(1093750038, 'JORGE GEOVANNI', 'SEPULVEDA PARADA', 'jgsepulveda@sena.edu.co', 3143864273, 9537, '$2b$10$.xhgRwbL5SJVhDBjksx3a.FrWzTca5f3IB4u3MTKHpTvpZ2eQck6S', NULL, 'REFRIGERACION', 1),
(1093750112, 'ANDRES MANUEL', 'RANGEL PRIETO', 'amrangelp@sena.edu.co', 3173806369, 9537, '$2b$10$YnlnFtnS9ZBOAtT9jv8xCuLy519i7w/H3HT2cE643jGoLKUtopDxC', NULL, 'MAQUINARIA PESADA', 1),
(1093750113, 'CARLOS ALFREDO', 'RESTREPO BOHORQUEZ', 'carestrepobo@sena.edu.co', 3168735491, 9537, '$2y$10$OBzeyegIYM5Qm7QVu7fh5OWFPttIp5Bvec9jxe7qI53KFwWFQ6UFS', '', 'EMPRENDIMIENTO', 1),
(1093781637, 'DANILSON ALEIDER', 'PARADA CONTRERAS', 'daparada@sena.edu.co', 3227726644, 9537, '$2b$10$V0QMGRJjLScsIWviapWr0e7huxTGt1IDmE3x87qaIgSIVR7AkCMuK', NULL, 'ELECTRICIDAD INDUSTRIAL', 1),
(1093782895, 'STEFFI YULEXI', 'CARREÑO CARRILLO', 'scarreno@sena.edu.co', 3183918938, 9537, '$2y$10$F2bY1HVAaHBhPn3J3NwxGOhnlI8Vhzg2edNgJFySXbfHEt2iocwAO', '', 'DERECHOS FUNDAMENTALES', 1),
(1094274331, 'ANA MARIA', 'HERNANDEZ CARVAJAL', 'anmhernandezc@sena.edu.co', 3123100063, 9537, '$2b$10$oMDvbMQOnD5LrCWBPV.veuprX0aGVbFaQIgu6.qJ5OWjrSmB1Gfo6', NULL, 'CONSTRUCCIÓN', 1),
(1101598417, 'LEIDY CAROLINA', 'LOPEZ ROJAS', 'lclopez@sena.edu.co', 3157120752, 9537, '$2y$10$XNl6PtxG3Y/eS7ewiA0sfuZvjSFhkG9rFDx5FTYmB.s6lDoX5tTlC', '', '', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instru_competencia`
--

CREATE TABLE `instru_competencia` (
  `inscomp_id` int(11) NOT NULL,
  `instructor_inst_id` bigint(20) DEFAULT NULL,
  `programa_prog_id` int(11) DEFAULT NULL,
  `competencia_comp_id` int(11) DEFAULT NULL,
  `inscomp_vigencia` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `instru_competencia`
--

INSERT INTO `instru_competencia` (`inscomp_id`, `instructor_inst_id`, `programa_prog_id`, `competencia_comp_id`, `inscomp_vigencia`) VALUES
(100, 535348, 833301, 37714, '2026-12-31'),
(101, 535348, 225311, 37714, '2026-12-31'),
(102, 535348, 836135, 37714, '2026-12-31'),
(103, 535348, 836136, 37714, '2026-12-31'),
(104, 535348, 224312, 37714, '2026-12-31'),
(105, 535348, 838200, 37714, '2026-12-31'),
(106, 535348, 224315, 37714, '2026-12-31'),
(107, 535348, 821307, 37714, '2026-12-31'),
(108, 535348, 833100, 37714, '2026-12-31'),
(109, 535348, 832102, 37714, '2026-12-31'),
(110, 535348, 821100, 37714, '2026-12-31'),
(111, 535348, 821620, 37714, '2026-12-31'),
(112, 535348, 837501, 37714, '2026-12-31'),
(113, 535348, 226701, 37714, '2026-12-31'),
(114, 535348, 832402, 37714, '2026-12-31'),
(115, 535348, 664212, 37714, '2026-12-31'),
(116, 535348, 839317, 37714, '2026-12-31'),
(117, 535348, 861100, 37714, '2026-12-31'),
(118, 535348, 838318, 37714, '2026-12-31'),
(119, 535348, 224201, 37714, '2026-12-31'),
(120, 535348, 832202, 37714, '2026-12-31'),
(121, 535348, 821202, 37714, '2026-12-31'),
(122, 535348, 834258, 37714, '2026-12-31'),
(123, 535348, 821203, 37714, '2026-12-31'),
(124, 535348, 838100, 37714, '2026-12-31'),
(125, 535348, 838109, 37714, '2026-12-31'),
(126, 535348, 223206, 37714, '2026-12-31'),
(127, 535348, 223213, 37714, '2026-12-31'),
(128, 535348, 224501, 37714, '2026-12-31'),
(129, 535348, 836600, 37714, '2026-12-31'),
(130, 535348, 832333, 3226, '2026-12-31'),
(131, 535348, 832422, 3226, '2026-12-31'),
(132, 1010232966, NULL, 37714, '2026-12-31'),
(133, 1010232966, 833301, 37714, '2026-12-31'),
(134, 1010232966, 225311, 37714, '2026-12-31'),
(135, 1010232966, 836135, 37714, '2026-12-31'),
(136, 1010232966, 836136, 37714, '2026-12-31'),
(137, 1010232966, 224312, 37714, '2026-12-31'),
(138, 1010232966, 838200, 37714, '2026-12-31'),
(139, 1010232966, 224315, 37714, '2026-12-31'),
(140, 1010232966, 821307, 37714, '2026-12-31'),
(141, 1010232966, 833100, 37714, '2026-12-31'),
(142, 1010232966, 832102, 37714, '2026-12-31'),
(143, 1010232966, 821100, 37714, '2026-12-31'),
(144, 1010232966, 821620, 37714, '2026-12-31'),
(145, 1010232966, 837501, 37714, '2026-12-31'),
(146, 1010232966, 226701, 37714, '2026-12-31'),
(147, 1010232966, 832402, 37714, '2026-12-31'),
(148, 1010232966, 664212, 37714, '2026-12-31'),
(149, 1010232966, 839317, 37714, '2026-12-31'),
(150, 1010232966, 861100, 37714, '2026-12-31'),
(151, 1010232966, 838318, 37714, '2026-12-31'),
(152, 1010232966, 224201, 37714, '2026-12-31'),
(153, 1010232966, 832202, 37714, '2026-12-31'),
(154, 1010232966, 821202, 37714, '2026-12-31'),
(155, 1010232966, 834258, 37714, '2026-12-31'),
(156, 1010232966, 821203, 37714, '2026-12-31'),
(157, 1010232966, 838100, 37714, '2026-12-31'),
(158, 1010232966, 838109, 37714, '2026-12-31'),
(159, 1010232966, 223206, 37714, '2026-12-31'),
(160, 1010232966, 223213, 37714, '2026-12-31'),
(161, 1010232966, 224501, 37714, '2026-12-31'),
(162, 1010232966, 836600, 37714, '2026-12-31'),
(163, 1010232966, 832333, 3226, '2026-12-31'),
(164, 1010232966, 832422, 3226, '2026-12-31'),
(165, 37395403, NULL, 37714, '2026-12-31'),
(166, 37395403, 833301, 37714, '2026-12-31'),
(167, 37395403, 225311, 37714, '2026-12-31'),
(168, 37395403, 836135, 37714, '2026-12-31'),
(169, 37395403, 836136, 37714, '2026-12-31'),
(170, 37395403, 224312, 37714, '2026-12-31'),
(171, 37395403, 838200, 37714, '2026-12-31'),
(172, 37395403, 224315, 37714, '2026-12-31'),
(173, 37395403, 821307, 37714, '2026-12-31'),
(174, 37395403, 833100, 37714, '2026-12-31'),
(175, 37395403, 832102, 37714, '2026-12-31'),
(176, 37395403, 821100, 37714, '2026-12-31'),
(177, 37395403, 821620, 37714, '2026-12-31'),
(178, 37395403, 837501, 37714, '2026-12-31'),
(179, 37395403, 226701, 37714, '2026-12-31'),
(180, 37395403, 832402, 37714, '2026-12-31'),
(181, 37395403, 664212, 37714, '2026-12-31'),
(182, 37395403, 839317, 37714, '2026-12-31'),
(183, 37395403, 861100, 37714, '2026-12-31'),
(184, 37395403, 838318, 37714, '2026-12-31'),
(185, 37395403, 224201, 37714, '2026-12-31'),
(186, 37395403, 832202, 37714, '2026-12-31'),
(187, 37395403, 821202, 37714, '2026-12-31'),
(188, 37395403, 834258, 37714, '2026-12-31'),
(189, 37395403, 821203, 37714, '2026-12-31'),
(190, 37395403, 838100, 37714, '2026-12-31'),
(191, 37395403, 838109, 37714, '2026-12-31'),
(192, 37395403, 223206, 37714, '2026-12-31'),
(193, 37395403, 223213, 37714, '2026-12-31'),
(194, 37395403, 224501, 37714, '2026-12-31'),
(195, 37395403, 836600, 37714, '2026-12-31'),
(196, 37395403, 832333, 3226, '2026-12-31'),
(197, 37395403, 832422, 3226, '2026-12-31'),
(231, 1101598417, NULL, 37714, '2026-12-31'),
(232, 1101598417, 836135, 37714, '2026-12-31'),
(233, 1101598417, 224312, 37714, '2026-12-31'),
(234, 1101598417, 224315, 37714, '2026-12-31'),
(235, 1101598417, 833100, 37714, '2026-12-31'),
(236, 1101598417, 821100, 37714, '2026-12-31'),
(237, 1101598417, 837501, 37714, '2026-12-31'),
(238, 1101598417, 832402, 37714, '2026-12-31'),
(239, 1101598417, 861100, 37714, '2026-12-31'),
(240, 1101598417, 224201, 37714, '2026-12-31'),
(241, 1101598417, 821202, 37714, '2026-12-31'),
(242, 1101598417, 821203, 37714, '2026-12-31'),
(243, 1101598417, 838109, 37714, '2026-12-31'),
(244, 1101598417, 223213, 37714, '2026-12-31'),
(245, 1101598417, 836600, 37714, '2026-12-31'),
(246, 1101598417, 225311, 37714, '2026-12-31'),
(247, 1101598417, 836136, 37714, '2026-12-31'),
(248, 1101598417, 838200, 37714, '2026-12-31'),
(249, 1101598417, 821307, 37714, '2026-12-31'),
(250, 1101598417, 832102, 37714, '2026-12-31'),
(251, 1101598417, 821620, 37714, '2026-12-31'),
(252, 1101598417, 226701, 37714, '2026-12-31'),
(253, 1101598417, 664212, 37714, '2026-12-31'),
(254, 1101598417, 838318, 37714, '2026-12-31'),
(255, 1101598417, 832202, 37714, '2026-12-31'),
(256, 1101598417, 834258, 37714, '2026-12-31'),
(257, 1101598417, 838100, 37714, '2026-12-31'),
(258, 1101598417, 223206, 37714, '2026-12-31'),
(259, 1101598417, 224501, 37714, '2026-12-31'),
(260, 1101598417, 833301, 37714, '2026-12-31'),
(261, 1101598417, 839317, 37714, '2026-12-31'),
(262, 1101598417, 832333, 3226, '2026-12-31'),
(263, 1101598417, 832422, 3226, '2026-12-31'),
(298, 1093734471, NULL, 37802, '2026-12-31'),
(299, 1093734471, 225311, 37802, '2026-12-31'),
(300, 1093734471, 836135, 37802, '2026-12-31'),
(301, 1093734471, 836137, 37802, '2026-12-31'),
(302, 1093734471, 838200, 37802, '2026-12-31'),
(303, 1093734471, 821307, 37802, '2026-12-31'),
(304, 1093734471, 832102, 37802, '2026-12-31'),
(305, 1093734471, 821620, 37802, '2026-12-31'),
(306, 1093734471, 226701, 37802, '2026-12-31'),
(307, 1093734471, 664212, 37802, '2026-12-31'),
(308, 1093734471, 861100, 37802, '2026-12-31'),
(309, 1093734471, 225224, 37802, '2026-12-31'),
(310, 1093734471, 821202, 37802, '2026-12-31'),
(311, 1093734471, 821203, 37802, '2026-12-31'),
(312, 1093734471, 838109, 37802, '2026-12-31'),
(313, 1093734471, 223213, 37802, '2026-12-31'),
(314, 1093734471, 836600, 37802, '2026-12-31'),
(315, 1093734471, 833301, 37802, '2026-12-31'),
(316, 1093734471, 225314, 37802, '2026-12-31'),
(317, 1093734471, 836136, 37802, '2026-12-31'),
(318, 1093734471, 224312, 37802, '2026-12-31'),
(319, 1093734471, 224315, 37802, '2026-12-31'),
(320, 1093734471, 833100, 37802, '2026-12-31'),
(321, 1093734471, 821100, 37802, '2026-12-31'),
(322, 1093734471, 837501, 37802, '2026-12-31'),
(323, 1093734471, 832402, 37802, '2026-12-31'),
(324, 1093734471, 839317, 37802, '2026-12-31'),
(325, 1093734471, 838318, 37802, '2026-12-31'),
(326, 1093734471, 224201, 37802, '2026-12-31'),
(327, 1093734471, 834258, 37802, '2026-12-31'),
(328, 1093734471, 838100, 37802, '2026-12-31'),
(329, 1093734471, 223206, 37802, '2026-12-31'),
(330, 1093734471, 224501, 37802, '2026-12-31'),
(331, 60444546, NULL, 37802, '2026-12-31'),
(332, 60444546, 226701, 37802, '2026-12-31'),
(333, 60444546, 832402, 37802, '2026-12-31'),
(334, 60444546, 664212, 37802, '2026-12-31'),
(335, 60444546, 839317, 37802, '2026-12-31'),
(336, 60444546, 832102, 37802, '2026-12-31'),
(337, 60444546, 821100, 37802, '2026-12-31'),
(338, 60444546, 821620, 37802, '2026-12-31'),
(339, 60444546, 837501, 37802, '2026-12-31'),
(340, 60444546, 836137, 37802, '2026-12-31'),
(341, 60444546, 224312, 37802, '2026-12-31'),
(342, 60444546, 838200, 37802, '2026-12-31'),
(343, 60444546, 224315, 37802, '2026-12-31'),
(344, 60444546, 821307, 37802, '2026-12-31'),
(345, 60444546, 833100, 37802, '2026-12-31'),
(346, 60444546, 861100, 37802, '2026-12-31'),
(347, 60444546, 838318, 37802, '2026-12-31'),
(348, 60444546, 225224, 37802, '2026-12-31'),
(349, 60444546, 224201, 37802, '2026-12-31'),
(350, 60444546, 821202, 37802, '2026-12-31'),
(351, 60444546, 834258, 37802, '2026-12-31'),
(352, 60444546, 821203, 37802, '2026-12-31'),
(353, 60444546, 838100, 37802, '2026-12-31'),
(354, 60444546, 838109, 37802, '2026-12-31'),
(355, 60444546, 223206, 37802, '2026-12-31'),
(356, 60444546, 223213, 37802, '2026-12-31'),
(357, 60444546, 224501, 37802, '2026-12-31'),
(358, 60444546, 833301, 37802, '2026-12-31'),
(359, 60444546, 225311, 37802, '2026-12-31'),
(360, 60444546, 225314, 37802, '2026-12-31'),
(361, 60444546, 836135, 37802, '2026-12-31'),
(362, 60444546, 836136, 37802, '2026-12-31'),
(363, 60444546, 836600, 37802, '2026-12-31'),
(364, 1090462158, NULL, 37371, '2026-12-31'),
(365, 1090462158, 833301, 37371, '2026-12-31'),
(366, 1090462158, 225311, 37371, '2026-12-31'),
(367, 1090462158, 836135, 37371, '2026-12-31'),
(368, 1090462158, 836136, 37371, '2026-12-31'),
(369, 1090462158, 836137, 37371, '2026-12-31'),
(370, 1090462158, 836138, 37371, '2026-12-31'),
(371, 1090462158, 845102, 37371, '2026-12-31'),
(372, 1090462158, 224312, 37371, '2026-12-31'),
(373, 1090462158, 838200, 37371, '2026-12-31'),
(374, 1090462158, 821307, 37371, '2026-12-31'),
(375, 1090462158, 833100, 37371, '2026-12-31'),
(376, 1090462158, 832102, 37371, '2026-12-31'),
(377, 1090462158, 821100, 37371, '2026-12-31'),
(378, 1090462158, 821620, 37371, '2026-12-31'),
(379, 1090462158, 837501, 37371, '2026-12-31'),
(380, 1090462158, 226701, 37371, '2026-12-31'),
(381, 1090462158, 832402, 37371, '2026-12-31'),
(382, 1090462158, 664212, 37371, '2026-12-31'),
(383, 1090462158, 839317, 37371, '2026-12-31'),
(384, 1090462158, 861100, 37371, '2026-12-31'),
(385, 1090462158, 838318, 37371, '2026-12-31'),
(386, 1090462158, 225224, 37371, '2026-12-31'),
(387, 1090462158, 224201, 37371, '2026-12-31'),
(388, 1090462158, 832202, 37371, '2026-12-31'),
(389, 1090462158, 821202, 37371, '2026-12-31'),
(390, 1090462158, 834258, 37371, '2026-12-31'),
(391, 1090462158, 821203, 37371, '2026-12-31'),
(392, 1090462158, 838100, 37371, '2026-12-31'),
(393, 1090462158, 838109, 37371, '2026-12-31'),
(394, 1090462158, 223206, 37371, '2026-12-31'),
(395, 1090462158, 223213, 37371, '2026-12-31'),
(396, 1090462158, 224501, 37371, '2026-12-31'),
(397, 1090462158, 836600, 37371, '2026-12-31'),
(398, 88244316, 833301, 38560, '2026-12-31'),
(399, 88244316, 225311, 38560, '2026-12-31'),
(400, 88244316, 225314, 38560, '2026-12-31'),
(401, 88244316, 836135, 38560, '2026-12-31'),
(402, 88244316, 836136, 38560, '2026-12-31'),
(403, 88244316, 836138, 38560, '2026-12-31'),
(404, 88244316, 845102, 38560, '2026-12-31'),
(405, 88244316, 224312, 38560, '2026-12-31'),
(406, 88244316, 838200, 38560, '2026-12-31'),
(407, 88244316, 224315, 38560, '2026-12-31'),
(408, 88244316, 821307, 38560, '2026-12-31'),
(409, 88244316, 833100, 38560, '2026-12-31'),
(410, 88244316, 832102, 38560, '2026-12-31'),
(411, 88244316, 821100, 38560, '2026-12-31'),
(412, 88244316, 821620, 38560, '2026-12-31'),
(413, 88244316, 837501, 38560, '2026-12-31'),
(414, 88244316, 226701, 38560, '2026-12-31'),
(415, 88244316, 832402, 38560, '2026-12-31'),
(416, 88244316, 839317, 38560, '2026-12-31'),
(417, 88244316, 861100, 38560, '2026-12-31'),
(418, 88244316, 838318, 38560, '2026-12-31'),
(419, 88244316, 225224, 38560, '2026-12-31'),
(420, 88244316, 224201, 38560, '2026-12-31'),
(421, 88244316, 832202, 38560, '2026-12-31'),
(422, 88244316, 821202, 38560, '2026-12-31'),
(423, 88244316, 834258, 38560, '2026-12-31'),
(424, 88244316, 821203, 38560, '2026-12-31'),
(425, 88244316, 838100, 38560, '2026-12-31'),
(426, 88244316, 838109, 38560, '2026-12-31'),
(427, 88244316, 223206, 38560, '2026-12-31'),
(428, 88244316, 223213, 38560, '2026-12-31'),
(429, 88244316, 224501, 38560, '2026-12-31'),
(430, 88244316, 836600, 38560, '2026-12-31'),
(431, 1090397641, 223206, 37801, '2026-12-31'),
(432, 1090397641, 225314, 37801, '2026-12-31'),
(433, 1090397641, 836136, 37801, '2026-12-31'),
(434, 1090397641, 224312, 37801, '2026-12-31'),
(435, 1090397641, 224315, 37801, '2026-12-31'),
(436, 1090397641, 833100, 37801, '2026-12-31'),
(437, 1090397641, 821620, 37801, '2026-12-31'),
(438, 1090397641, 832402, 37801, '2026-12-31'),
(439, 1090397641, 861100, 37801, '2026-12-31'),
(440, 1090397641, 224201, 37801, '2026-12-31'),
(441, 1090397641, 821203, 37801, '2026-12-31'),
(442, 1090397641, 223213, 37801, '2026-12-31'),
(443, 1090397641, 836600, 37801, '2026-12-31'),
(444, 1090397641, 225311, 37801, '2026-12-31'),
(445, 1090397641, 836135, 37801, '2026-12-31'),
(446, 1090397641, 845102, 37801, '2026-12-31'),
(447, 1090397641, 838200, 37801, '2026-12-31'),
(448, 1090397641, 821307, 37801, '2026-12-31'),
(449, 1090397641, 821100, 37801, '2026-12-31'),
(450, 1090397641, 226701, 37801, '2026-12-31'),
(451, 1090397641, 839317, 37801, '2026-12-31'),
(452, 1090397641, 838318, 37801, '2026-12-31'),
(453, 1090397641, 821202, 37801, '2026-12-31'),
(454, 1090397641, 838109, 37801, '2026-12-31'),
(455, 1090397641, 224501, 37801, '2026-12-31'),
(456, 1090397641, 832102, 37801, '2026-12-31'),
(457, 1090397641, 833301, 37801, '2026-12-31'),
(458, 1090397641, 838100, 37801, '2026-12-31'),
(459, 1090397641, 834258, 37801, '2026-12-31'),
(460, 1090397641, 223206, 37799, '2026-12-31'),
(461, 1090397641, 833301, 37799, '2026-12-31'),
(462, 1090397641, 225311, 37799, '2026-12-31'),
(463, 1090397641, 225314, 37799, '2026-12-31'),
(464, 1090397641, 836135, 37799, '2026-12-31'),
(465, 1090397641, 836136, 37799, '2026-12-31'),
(466, 1090397641, 836137, 37799, '2026-12-31'),
(467, 1090397641, 836138, 37799, '2026-12-31'),
(468, 1090397641, 836140, 37799, '2026-12-31'),
(469, 1090397641, 845102, 37799, '2026-12-31'),
(470, 1090397641, 832303, 37799, '2026-12-31'),
(471, 1090397641, 224312, 37799, '2026-12-31'),
(472, 1090397641, 838200, 37799, '2026-12-31'),
(473, 1090397641, 224315, 37799, '2026-12-31'),
(474, 1090397641, 821307, 37799, '2026-12-31'),
(475, 1090397641, 833100, 37799, '2026-12-31'),
(476, 1090397641, 832102, 37799, '2026-12-31'),
(477, 1090397641, 821100, 37799, '2026-12-31'),
(478, 1090397641, 821620, 37799, '2026-12-31'),
(479, 1090397641, 837501, 37799, '2026-12-31'),
(480, 1090397641, 226701, 37799, '2026-12-31'),
(481, 1090397641, 832402, 37799, '2026-12-31'),
(482, 1090397641, 664212, 37799, '2026-12-31'),
(483, 1090397641, 839317, 37799, '2026-12-31'),
(484, 1090397641, 861100, 37799, '2026-12-31'),
(485, 1090397641, 838318, 37799, '2026-12-31'),
(486, 1090397641, 225224, 37799, '2026-12-31'),
(487, 1090397641, 224201, 37799, '2026-12-31'),
(488, 1090397641, 832202, 37799, '2026-12-31'),
(489, 1090397641, 821202, 37799, '2026-12-31'),
(490, 1090397641, 834258, 37799, '2026-12-31'),
(491, 1090397641, 821203, 37799, '2026-12-31'),
(492, 1090397641, 838100, 37799, '2026-12-31'),
(493, 1090397641, 838109, 37799, '2026-12-31'),
(494, 1090397641, 223213, 37799, '2026-12-31'),
(495, 1090397641, 224501, 37799, '2026-12-31'),
(496, 1090397641, 836600, 37799, '2026-12-31'),
(528, 60365097, 833301, 37801, '2026-12-31'),
(529, 60365097, 836135, 37801, '2026-12-31'),
(530, 60365097, 845102, 37801, '2026-12-31'),
(531, 60365097, 838200, 37801, '2026-12-31'),
(532, 60365097, 821307, 37801, '2026-12-31'),
(533, 60365097, 832102, 37801, '2026-12-31'),
(534, 60365097, 226701, 37801, '2026-12-31'),
(535, 60365097, 839317, 37801, '2026-12-31'),
(536, 60365097, 838318, 37801, '2026-12-31'),
(537, 60365097, 821202, 37801, '2026-12-31'),
(538, 60365097, 838100, 37801, '2026-12-31'),
(539, 60365097, 223206, 37801, '2026-12-31'),
(540, 60365097, 224501, 37801, '2026-12-31'),
(541, 60365097, 225314, 37801, '2026-12-31'),
(542, 60365097, 836136, 37801, '2026-12-31'),
(543, 60365097, 224312, 37801, '2026-12-31'),
(544, 60365097, 224315, 37801, '2026-12-31'),
(545, 60365097, 833100, 37801, '2026-12-31'),
(546, 60365097, 821620, 37801, '2026-12-31'),
(547, 60365097, 832402, 37801, '2026-12-31'),
(548, 60365097, 861100, 37801, '2026-12-31'),
(549, 60365097, 224201, 37801, '2026-12-31'),
(550, 60365097, 834258, 37801, '2026-12-31'),
(551, 60365097, 838109, 37801, '2026-12-31'),
(552, 60365097, 223213, 37801, '2026-12-31'),
(553, 60365097, 836600, 37801, '2026-12-31'),
(554, 60365097, 821100, 37801, '2026-12-31'),
(555, 60365097, 225311, 37801, '2026-12-31'),
(556, 60365097, NULL, 37801, '2026-12-31'),
(557, 60365097, 821203, 37801, '2026-12-31'),
(558, 60365097, NULL, 37799, '2026-12-31'),
(559, 60365097, 833301, 37799, '2026-12-31'),
(560, 60365097, 225311, 37799, '2026-12-31'),
(561, 60365097, 225314, 37799, '2026-12-31'),
(562, 60365097, 836135, 37799, '2026-12-31'),
(563, 60365097, 836136, 37799, '2026-12-31'),
(564, 60365097, 836137, 37799, '2026-12-31'),
(565, 60365097, 836138, 37799, '2026-12-31'),
(566, 60365097, 836140, 37799, '2026-12-31'),
(567, 60365097, 845102, 37799, '2026-12-31'),
(568, 60365097, 832303, 37799, '2026-12-31'),
(569, 60365097, 224312, 37799, '2026-12-31'),
(570, 60365097, 838200, 37799, '2026-12-31'),
(571, 60365097, 224315, 37799, '2026-12-31'),
(572, 60365097, 821307, 37799, '2026-12-31'),
(573, 60365097, 833100, 37799, '2026-12-31'),
(574, 60365097, 832102, 37799, '2026-12-31'),
(575, 60365097, 821100, 37799, '2026-12-31'),
(576, 60365097, 821620, 37799, '2026-12-31'),
(577, 60365097, 837501, 37799, '2026-12-31'),
(578, 60365097, 226701, 37799, '2026-12-31'),
(579, 60365097, 832402, 37799, '2026-12-31'),
(580, 60365097, 664212, 37799, '2026-12-31'),
(581, 60365097, 839317, 37799, '2026-12-31'),
(582, 60365097, 861100, 37799, '2026-12-31'),
(583, 60365097, 838318, 37799, '2026-12-31'),
(584, 60365097, 225224, 37799, '2026-12-31'),
(585, 60365097, 224201, 37799, '2026-12-31'),
(586, 60365097, 832202, 37799, '2026-12-31'),
(587, 60365097, 821202, 37799, '2026-12-31'),
(588, 60365097, 834258, 37799, '2026-12-31'),
(589, 60365097, 821203, 37799, '2026-12-31'),
(590, 60365097, 838100, 37799, '2026-12-31'),
(591, 60365097, 838109, 37799, '2026-12-31'),
(592, 60365097, 223206, 37799, '2026-12-31'),
(593, 60365097, 223213, 37799, '2026-12-31'),
(594, 60365097, 224501, 37799, '2026-12-31'),
(595, 60365097, 836600, 37799, '2026-12-31'),
(642, 1093738086, NULL, 37799, '2026-12-31'),
(643, 1093738086, 225314, 37799, '2026-12-31'),
(644, 1093738086, 836136, 37799, '2026-12-31'),
(645, 1093738086, 836137, 37799, '2026-12-31'),
(646, 1093738086, 836140, 37799, '2026-12-31'),
(647, 1093738086, 832303, 37799, '2026-12-31'),
(648, 1093738086, 838200, 37799, '2026-12-31'),
(649, 1093738086, 224315, 37799, '2026-12-31'),
(650, 1093738086, 833100, 37799, '2026-12-31'),
(651, 1093738086, 821100, 37799, '2026-12-31'),
(652, 1093738086, 837501, 37799, '2026-12-31'),
(653, 1093738086, 226701, 37799, '2026-12-31'),
(654, 1093738086, 664212, 37799, '2026-12-31'),
(655, 1093738086, 861100, 37799, '2026-12-31'),
(656, 1093738086, 225224, 37799, '2026-12-31'),
(657, 1093738086, 224201, 37799, '2026-12-31'),
(658, 1093738086, 821202, 37799, '2026-12-31'),
(659, 1093738086, 821203, 37799, '2026-12-31'),
(660, 1093738086, 838100, 37799, '2026-12-31'),
(661, 1093738086, 223206, 37799, '2026-12-31'),
(662, 1093738086, 224501, 37799, '2026-12-31'),
(663, 1093738086, 833301, 37799, '2026-12-31'),
(664, 1093738086, 225311, 37799, '2026-12-31'),
(665, 1093738086, 836135, 37799, '2026-12-31'),
(666, 1093738086, 836138, 37799, '2026-12-31'),
(667, 1093738086, 845102, 37799, '2026-12-31'),
(668, 1093738086, 224312, 37799, '2026-12-31'),
(669, 1093738086, 821307, 37799, '2026-12-31'),
(670, 1093738086, 832102, 37799, '2026-12-31'),
(671, 1093738086, 821620, 37799, '2026-12-31'),
(672, 1093738086, 832402, 37799, '2026-12-31'),
(673, 1093738086, 839317, 37799, '2026-12-31'),
(674, 1093738086, 838318, 37799, '2026-12-31'),
(675, 1093738086, 832202, 37799, '2026-12-31'),
(676, 1093738086, 834258, 37799, '2026-12-31'),
(677, 1093738086, 838109, 37799, '2026-12-31'),
(678, 1093738086, 223213, 37799, '2026-12-31'),
(679, 1093738086, 836600, 37799, '2026-12-31'),
(694, 1093750113, 836137, 39811, '2026-12-31'),
(695, 1093750113, 224315, 39811, '2026-12-31'),
(696, 1093750113, 225224, 39811, '2026-12-31'),
(697, 1093750113, 836140, 39811, '2026-12-31'),
(698, 1093750113, 664212, 39811, '2026-12-31'),
(699, 1093750113, NULL, 39811, '2026-12-31'),
(700, 1093750113, 845102, 39811, '2026-12-31'),
(701, 1093750113, 839317, 39811, '2026-12-31'),
(702, 1093750113, 836138, 39811, '2026-12-31'),
(703, 1093750113, 837501, 39811, '2026-12-31'),
(704, 1093750113, 832202, 39811, '2026-12-31'),
(705, 1093750113, 225314, 39811, '2026-12-31'),
(706, 1093750113, 832303, 39811, '2026-12-31'),
(707, 1093750113, 861100, 39811, '2026-12-31'),
(708, 80227809, NULL, 39811, '2026-12-31'),
(709, 80227809, 225314, 39811, '2026-12-31'),
(710, 80227809, 836137, 39811, '2026-12-31'),
(711, 80227809, 836138, 39811, '2026-12-31'),
(712, 80227809, 836140, 39811, '2026-12-31'),
(713, 80227809, 845102, 39811, '2026-12-31'),
(714, 80227809, 832303, 39811, '2026-12-31'),
(715, 80227809, 224315, 39811, '2026-12-31'),
(716, 80227809, 837501, 39811, '2026-12-31'),
(717, 80227809, 664212, 39811, '2026-12-31'),
(718, 80227809, 839317, 39811, '2026-12-31'),
(719, 80227809, 861100, 39811, '2026-12-31'),
(720, 80227809, 225224, 39811, '2026-12-31'),
(721, 80227809, 832202, 39811, '2026-12-31'),
(722, 51967026, NULL, 36180, '2026-12-31'),
(723, 51967026, 833301, 36180, '2026-12-31'),
(724, 51967026, 225311, 36180, '2026-12-31'),
(725, 51967026, 225314, 36180, '2026-12-31'),
(726, 51967026, 836135, 36180, '2026-12-31'),
(727, 51967026, 836136, 36180, '2026-12-31'),
(728, 51967026, 836137, 36180, '2026-12-31'),
(729, 51967026, 836138, 36180, '2026-12-31'),
(730, 51967026, 836140, 36180, '2026-12-31'),
(731, 51967026, 845102, 36180, '2026-12-31'),
(732, 51967026, 832303, 36180, '2026-12-31'),
(733, 51967026, 224312, 36180, '2026-12-31'),
(734, 51967026, 838200, 36180, '2026-12-31'),
(735, 51967026, 224315, 36180, '2026-12-31'),
(736, 51967026, 821307, 36180, '2026-12-31'),
(737, 51967026, 833100, 36180, '2026-12-31'),
(738, 51967026, 832102, 36180, '2026-12-31'),
(739, 51967026, 821100, 36180, '2026-12-31'),
(740, 51967026, 821620, 36180, '2026-12-31'),
(741, 51967026, 837501, 36180, '2026-12-31'),
(742, 51967026, 226701, 36180, '2026-12-31'),
(743, 51967026, 832402, 36180, '2026-12-31'),
(744, 51967026, 664212, 36180, '2026-12-31'),
(745, 51967026, 839317, 36180, '2026-12-31'),
(746, 51967026, 861100, 36180, '2026-12-31'),
(747, 51967026, 838318, 36180, '2026-12-31'),
(748, 51967026, 225224, 36180, '2026-12-31'),
(749, 51967026, 224201, 36180, '2026-12-31'),
(750, 51967026, 832202, 36180, '2026-12-31'),
(751, 51967026, 821202, 36180, '2026-12-31'),
(752, 51967026, 834258, 36180, '2026-12-31'),
(753, 51967026, 821203, 36180, '2026-12-31'),
(754, 51967026, 838100, 36180, '2026-12-31'),
(755, 51967026, 838109, 36180, '2026-12-31'),
(756, 51967026, 223206, 36180, '2026-12-31'),
(757, 51967026, 223213, 36180, '2026-12-31'),
(758, 51967026, 224501, 36180, '2026-12-31'),
(759, 51967026, 836600, 36180, '2026-12-31'),
(760, 60301504, NULL, 36180, '2026-12-31'),
(761, 60301504, 833301, 36180, '2026-12-31'),
(762, 60301504, 225311, 36180, '2026-12-31'),
(763, 60301504, 225314, 36180, '2026-12-31'),
(764, 60301504, 836135, 36180, '2026-12-31'),
(765, 60301504, 836136, 36180, '2026-12-31'),
(766, 60301504, 836137, 36180, '2026-12-31'),
(767, 60301504, 836138, 36180, '2026-12-31'),
(768, 60301504, 836140, 36180, '2026-12-31'),
(769, 60301504, 845102, 36180, '2026-12-31'),
(770, 60301504, 832303, 36180, '2026-12-31'),
(771, 60301504, 224312, 36180, '2026-12-31'),
(772, 60301504, 838200, 36180, '2026-12-31'),
(773, 60301504, 224315, 36180, '2026-12-31'),
(774, 60301504, 821307, 36180, '2026-12-31'),
(775, 60301504, 833100, 36180, '2026-12-31'),
(776, 60301504, 832102, 36180, '2026-12-31'),
(777, 60301504, 821100, 36180, '2026-12-31'),
(778, 60301504, 821620, 36180, '2026-12-31'),
(779, 60301504, 837501, 36180, '2026-12-31'),
(780, 60301504, 226701, 36180, '2026-12-31'),
(781, 60301504, 832402, 36180, '2026-12-31'),
(782, 60301504, 664212, 36180, '2026-12-31'),
(783, 60301504, 839317, 36180, '2026-12-31'),
(784, 60301504, 861100, 36180, '2026-12-31'),
(785, 60301504, 838318, 36180, '2026-12-31'),
(786, 60301504, 225224, 36180, '2026-12-31'),
(787, 60301504, 224201, 36180, '2026-12-31'),
(788, 60301504, 832202, 36180, '2026-12-31'),
(789, 60301504, 821202, 36180, '2026-12-31'),
(790, 60301504, 834258, 36180, '2026-12-31'),
(791, 60301504, 821203, 36180, '2026-12-31'),
(792, 60301504, 838100, 36180, '2026-12-31'),
(793, 60301504, 838109, 36180, '2026-12-31'),
(794, 60301504, 223206, 36180, '2026-12-31'),
(795, 60301504, 223213, 36180, '2026-12-31'),
(796, 60301504, 224501, 36180, '2026-12-31'),
(797, 60301504, 836600, 36180, '2026-12-31'),
(798, 13509804, NULL, 38558, '2026-12-31'),
(799, 13509804, 833301, 38558, '2026-12-31'),
(800, 13509804, 225311, 38558, '2026-12-31'),
(801, 13509804, 225314, 38558, '2026-12-31'),
(802, 13509804, 836135, 38558, '2026-12-31'),
(803, 13509804, 836136, 38558, '2026-12-31'),
(804, 13509804, 836137, 38558, '2026-12-31'),
(805, 13509804, 836138, 38558, '2026-12-31'),
(806, 13509804, 832300, 38558, '2026-12-31'),
(807, 13509804, 836140, 38558, '2026-12-31'),
(808, 13509804, 845102, 38558, '2026-12-31'),
(809, 13509804, 832303, 38558, '2026-12-31'),
(810, 13509804, 224312, 38558, '2026-12-31'),
(811, 13509804, 838200, 38558, '2026-12-31'),
(812, 13509804, 224315, 38558, '2026-12-31'),
(813, 13509804, 821307, 38558, '2026-12-31'),
(814, 13509804, 833100, 38558, '2026-12-31'),
(815, 13509804, 832333, 38558, '2026-12-31'),
(816, 13509804, 832102, 38558, '2026-12-31'),
(817, 13509804, 821100, 38558, '2026-12-31'),
(818, 13509804, 821620, 38558, '2026-12-31'),
(819, 13509804, 837501, 38558, '2026-12-31'),
(820, 13509804, 226701, 38558, '2026-12-31'),
(821, 13509804, 832402, 38558, '2026-12-31'),
(822, 13509804, 664212, 38558, '2026-12-31'),
(823, 13509804, 839317, 38558, '2026-12-31'),
(824, 13509804, 832422, 38558, '2026-12-31'),
(825, 13509804, 861100, 38558, '2026-12-31'),
(826, 13509804, 838318, 38558, '2026-12-31'),
(827, 13509804, 225224, 38558, '2026-12-31'),
(828, 13509804, 224201, 38558, '2026-12-31'),
(829, 13509804, 832202, 38558, '2026-12-31'),
(830, 13509804, 821202, 38558, '2026-12-31'),
(831, 13509804, 834258, 38558, '2026-12-31'),
(832, 13509804, 821203, 38558, '2026-12-31'),
(833, 13509804, 838100, 38558, '2026-12-31'),
(834, 13509804, 838109, 38558, '2026-12-31'),
(835, 13509804, 223206, 38558, '2026-12-31'),
(836, 13509804, 223213, 38558, '2026-12-31'),
(837, 13509804, 224501, 38558, '2026-12-31'),
(838, 13509804, 836600, 38558, '2026-12-31'),
(839, 1090401173, NULL, 38558, '2026-12-31'),
(840, 1090401173, 833301, 38558, '2026-12-31'),
(841, 1090401173, 225311, 38558, '2026-12-31'),
(842, 1090401173, 225314, 38558, '2026-12-31'),
(843, 1090401173, 836135, 38558, '2026-12-31'),
(844, 1090401173, 836136, 38558, '2026-12-31'),
(845, 1090401173, 836137, 38558, '2026-12-31'),
(846, 1090401173, 836138, 38558, '2026-12-31'),
(847, 1090401173, 832300, 38558, '2026-12-31'),
(848, 1090401173, 836140, 38558, '2026-12-31'),
(849, 1090401173, 845102, 38558, '2026-12-31'),
(850, 1090401173, 832303, 38558, '2026-12-31'),
(851, 1090401173, 224312, 38558, '2026-12-31'),
(852, 1090401173, 838200, 38558, '2026-12-31'),
(853, 1090401173, 224315, 38558, '2026-12-31'),
(854, 1090401173, 821307, 38558, '2026-12-31'),
(855, 1090401173, 833100, 38558, '2026-12-31'),
(856, 1090401173, 832333, 38558, '2026-12-31'),
(857, 1090401173, 832102, 38558, '2026-12-31'),
(858, 1090401173, 821100, 38558, '2026-12-31'),
(859, 1090401173, 821620, 38558, '2026-12-31'),
(860, 1090401173, 837501, 38558, '2026-12-31'),
(861, 1090401173, 226701, 38558, '2026-12-31'),
(862, 1090401173, 832402, 38558, '2026-12-31'),
(863, 1090401173, 664212, 38558, '2026-12-31'),
(864, 1090401173, 839317, 38558, '2026-12-31'),
(865, 1090401173, 832422, 38558, '2026-12-31'),
(866, 1090401173, 861100, 38558, '2026-12-31'),
(867, 1090401173, 838318, 38558, '2026-12-31'),
(868, 1090401173, 225224, 38558, '2026-12-31'),
(869, 1090401173, 224201, 38558, '2026-12-31'),
(870, 1090401173, 832202, 38558, '2026-12-31'),
(871, 1090401173, 821202, 38558, '2026-12-31'),
(872, 1090401173, 834258, 38558, '2026-12-31'),
(873, 1090401173, 821203, 38558, '2026-12-31'),
(874, 1090401173, 838100, 38558, '2026-12-31'),
(875, 1090401173, 838109, 38558, '2026-12-31'),
(876, 1090401173, 223206, 38558, '2026-12-31'),
(877, 1090401173, 223213, 38558, '2026-12-31'),
(878, 1090401173, 224501, 38558, '2026-12-31'),
(879, 1090401173, 836600, 38558, '2026-12-31'),
(880, 1093782895, 838100, 38558, '2026-12-31'),
(881, 1093782895, 833301, 38558, '2026-12-31'),
(882, 1093782895, 225311, 38558, '2026-12-31'),
(883, 1093782895, 225314, 38558, '2026-12-31'),
(884, 1093782895, 836135, 38558, '2026-12-31'),
(885, 1093782895, 836136, 38558, '2026-12-31'),
(886, 1093782895, 836137, 38558, '2026-12-31'),
(887, 1093782895, 836138, 38558, '2026-12-31'),
(888, 1093782895, 832300, 38558, '2026-12-31'),
(889, 1093782895, 836140, 38558, '2026-12-31'),
(890, 1093782895, 845102, 38558, '2026-12-31'),
(891, 1093782895, 832303, 38558, '2026-12-31'),
(892, 1093782895, 224312, 38558, '2026-12-31'),
(893, 1093782895, 838200, 38558, '2026-12-31'),
(894, 1093782895, 224315, 38558, '2026-12-31'),
(895, 1093782895, 821307, 38558, '2026-12-31'),
(896, 1093782895, 833100, 38558, '2026-12-31'),
(897, 1093782895, 832333, 38558, '2026-12-31'),
(898, 1093782895, 832102, 38558, '2026-12-31'),
(899, 1093782895, 821100, 38558, '2026-12-31'),
(900, 1093782895, 821620, 38558, '2026-12-31'),
(901, 1093782895, 837501, 38558, '2026-12-31'),
(902, 1093782895, 226701, 38558, '2026-12-31'),
(903, 1093782895, 832402, 38558, '2026-12-31'),
(904, 1093782895, 664212, 38558, '2026-12-31'),
(905, 1093782895, 839317, 38558, '2026-12-31'),
(906, 1093782895, 832422, 38558, '2026-12-31'),
(907, 1093782895, 861100, 38558, '2026-12-31'),
(908, 1093782895, 838318, 38558, '2026-12-31'),
(909, 1093782895, 225224, 38558, '2026-12-31'),
(910, 1093782895, 224201, 38558, '2026-12-31'),
(911, 1093782895, 832202, 38558, '2026-12-31'),
(912, 1093782895, 821202, 38558, '2026-12-31'),
(913, 1093782895, 834258, 38558, '2026-12-31'),
(914, 1093782895, 821203, 38558, '2026-12-31'),
(915, 1093782895, 838109, 38558, '2026-12-31'),
(916, 1093782895, 223206, 38558, '2026-12-31'),
(917, 1093782895, 223213, 38558, '2026-12-31'),
(918, 1093782895, 224501, 38558, '2026-12-31'),
(919, 1093782895, 836600, 38558, '2026-12-31'),
(920, 88211022, NULL, 37800, '2026-12-31'),
(921, 88211022, 833301, 37800, '2026-12-31'),
(922, 88211022, 225311, 37800, '2026-12-31'),
(923, 88211022, 225314, 37800, '2026-12-31'),
(924, 88211022, 836135, 37800, '2026-12-31'),
(925, 88211022, 836136, 37800, '2026-12-31'),
(926, 88211022, 836137, 37800, '2026-12-31'),
(927, 88211022, 836138, 37800, '2026-12-31'),
(928, 88211022, 836140, 37800, '2026-12-31'),
(929, 88211022, 845102, 37800, '2026-12-31'),
(930, 88211022, 832303, 37800, '2026-12-31'),
(931, 88211022, 224312, 37800, '2026-12-31'),
(932, 88211022, 838200, 37800, '2026-12-31'),
(933, 88211022, 224315, 37800, '2026-12-31'),
(934, 88211022, 821307, 37800, '2026-12-31'),
(935, 88211022, 833100, 37800, '2026-12-31'),
(936, 88211022, 832102, 37800, '2026-12-31'),
(937, 88211022, 821100, 37800, '2026-12-31'),
(938, 88211022, 821620, 37800, '2026-12-31'),
(939, 88211022, 837501, 37800, '2026-12-31'),
(940, 88211022, 226701, 37800, '2026-12-31'),
(941, 88211022, 832402, 37800, '2026-12-31'),
(942, 88211022, 664212, 37800, '2026-12-31'),
(943, 88211022, 839317, 37800, '2026-12-31'),
(944, 88211022, 861100, 37800, '2026-12-31'),
(945, 88211022, 838318, 37800, '2026-12-31'),
(946, 88211022, 225224, 37800, '2026-12-31'),
(947, 88211022, 224201, 37800, '2026-12-31'),
(948, 88211022, 832202, 37800, '2026-12-31'),
(949, 88211022, 821202, 37800, '2026-12-31'),
(950, 88211022, 834258, 37800, '2026-12-31'),
(951, 88211022, 821203, 37800, '2026-12-31'),
(952, 88211022, 838100, 37800, '2026-12-31'),
(953, 88211022, 838109, 37800, '2026-12-31'),
(954, 88211022, 223206, 37800, '2026-12-31'),
(955, 88211022, 223213, 37800, '2026-12-31'),
(956, 88211022, 224501, 37800, '2026-12-31'),
(957, 88211022, 836600, 37800, '2026-12-31'),
(958, 88160803, NULL, 37800, '2026-12-31'),
(959, 88160803, 833301, 37800, '2026-12-31'),
(960, 88160803, 225311, 37800, '2026-12-31'),
(961, 88160803, 225314, 37800, '2026-12-31'),
(962, 88160803, 836135, 37800, '2026-12-31'),
(963, 88160803, 836136, 37800, '2026-12-31'),
(964, 88160803, 836137, 37800, '2026-12-31'),
(965, 88160803, 836138, 37800, '2026-12-31'),
(966, 88160803, 836140, 37800, '2026-12-31'),
(967, 88160803, 845102, 37800, '2026-12-31'),
(968, 88160803, 832303, 37800, '2026-12-31'),
(969, 88160803, 224312, 37800, '2026-12-31'),
(970, 88160803, 838200, 37800, '2026-12-31'),
(971, 88160803, 224315, 37800, '2026-12-31'),
(972, 88160803, 821307, 37800, '2026-12-31'),
(973, 88160803, 833100, 37800, '2026-12-31'),
(974, 88160803, 832102, 37800, '2026-12-31'),
(975, 88160803, 821100, 37800, '2026-12-31'),
(976, 88160803, 821620, 37800, '2026-12-31'),
(977, 88160803, 837501, 37800, '2026-12-31'),
(978, 88160803, 226701, 37800, '2026-12-31'),
(979, 88160803, 832402, 37800, '2026-12-31'),
(980, 88160803, 664212, 37800, '2026-12-31'),
(981, 88160803, 839317, 37800, '2026-12-31'),
(982, 88160803, 861100, 37800, '2026-12-31'),
(983, 88160803, 838318, 37800, '2026-12-31'),
(984, 88160803, 225224, 37800, '2026-12-31'),
(985, 88160803, 224201, 37800, '2026-12-31'),
(986, 88160803, 832202, 37800, '2026-12-31'),
(987, 88160803, 821202, 37800, '2026-12-31'),
(988, 88160803, 834258, 37800, '2026-12-31'),
(989, 88160803, 821203, 37800, '2026-12-31'),
(990, 88160803, 838100, 37800, '2026-12-31'),
(991, 88160803, 838109, 37800, '2026-12-31'),
(992, 88160803, 223206, 37800, '2026-12-31'),
(993, 88160803, 223213, 37800, '2026-12-31'),
(994, 88160803, 224501, 37800, '2026-12-31'),
(995, 88160803, 836600, 37800, '2026-12-31'),
(996, 88217769, NULL, 38199, '2026-12-31'),
(997, 88217769, 225311, 38199, '2026-12-31'),
(998, 88217769, 224312, 38199, '2026-12-31'),
(999, 88217769, 821307, 38199, '2026-12-31'),
(1000, 88217769, 821100, 38199, '2026-12-31'),
(1001, 88217769, 821620, 38199, '2026-12-31'),
(1002, 88217769, 226701, 38199, '2026-12-31'),
(1003, 88217769, 224201, 38199, '2026-12-31'),
(1004, 88217769, 821202, 38199, '2026-12-31'),
(1005, 88217769, 821203, 38199, '2026-12-31'),
(1006, 88217769, 223206, 38199, '2026-12-31'),
(1007, 88217769, 223213, 38199, '2026-12-31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `programa`
--

CREATE TABLE `programa` (
  `prog_codigo` int(11) NOT NULL,
  `prog_denominacion` varchar(255) NOT NULL,
  `prog_version` int(3) DEFAULT NULL,
  `tit_programa_titpro_id` int(11) DEFAULT NULL,
  `prog_tipo` varchar(100) NOT NULL,
  `centro_formacion_cent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `programa`
--

INSERT INTO `programa` (`prog_codigo`, `prog_denominacion`, `prog_version`, `tit_programa_titpro_id`, `prog_tipo`, `centro_formacion_cent_id`) VALUES
(223206, 'MANTENIMIENTO MECÁNICO INDUSTRIAL', 1, 2, 'Presencial', 9537),
(223213, 'MANTENIMIENTO ELECTROMECÁNICO INDUSTRIAL', 1, 2, 'Presencial', 9537),
(224201, 'DESARROLLO DE SISTEMAS ELECTRÓNICOS INDUSTRIALES', 1, 2, 'Presencial', 9537),
(224312, 'AUTOMATIZACION DE SISTEMAS MECATRONICOS', 1, 2, 'Presencial', 9537),
(224315, 'MANTENIMIENTO DE AUTOMATISMOS INDUSTRIALES', 1, 1, 'Presencial', 9537),
(224501, 'IMPLEMENTACION Y MANTENIMIENTO DE SISTEMAS DE INTERNET DE LAS COSAS', 1, 1, 'Presencial', 9537),
(225224, 'DIBUJO DIGITAL DE ARQUITECTURA E INGENIERIA', 1, 1, 'Presencial', 9537),
(225311, 'LEVANTAMIENTOS TOPOGRAFICOS Y GEORREFERENCIACION', 1, 2, 'Presencial', 9537),
(225314, 'CATASTRO MULTIPROPOSITO', 1, 1, 'Presencial', 9537),
(226701, 'COORDINACIÓN EN SISTEMAS INTEGRADOS DE GESTIÓN', 1, 2, 'Presencial', 9537),
(664212, 'EJECUCION DE PROGRAMAS DEPORTIVOS', 1, 1, 'Presencial', 9537),
(723200, 'PROMOTORIA CAMPESINA EN AGROECOLOGIA', 1, 1, 'Presencial', 9537),
(821100, 'PRODUCCION DE COMPONENTES MECANICOS CON MAQUINAS DE CONTROL NUMERICO COMPUTARIZADO', 1, 2, 'Presencial', 9537),
(821202, 'SUPERVISIÓN DE REDES DE DISTRIBUCIÓN DE ENERGÍA ELÉCTRICA', 2, 2, 'Presencial', 9537),
(821203, 'IMPLEMENTACION DE REDES Y SERVICIOS DE TELECOMUNICACIONES', 1, 2, 'Presencial', 9537),
(821307, 'GESTIÓN PARA SUMINISTRO DE GAS COMBUSTIBLE', 1, 2, 'Presencial', 9537),
(821620, 'GESTIÓN DEL MANTENIMIENTO DE AUTOMOTORES', 1, 2, 'Presencial', 9537),
(832102, 'ELECTRICISTA INDUSTRIAL', 1, 1, 'Presencial', 9537),
(832202, 'INSTALACION DE SISTEMAS ELECTRICOS RESIDENCIALES Y COMERCIALES', 2, 1, 'Presencial', 9537),
(832300, 'MONTAJE Y MANTENIMIENTO DE SISTEMAS SOLARES FOTOVOLTAICOS', 1, 6, 'Presencial', 9537),
(832303, 'INSTALACION Y MANTENIMIENTO DE REDES AEREAS DE DISTRIBUCION DE ENERGIA ELECTRICA EN MEDIA Y BAJA TENSION', 1, 1, 'Presencial', 9537),
(832333, 'MANTENIMIENTO E INSTALACION DE SISTEMAS SOLARES FOTOVOLTAICOS', 1, 1, 'Presencial', 9537),
(832402, 'INSTALACION Y MANTENIMIENTO DE REDES INTERNAS DE TELECOMUNICACIONES', 1, 1, 'Presencial', 9537),
(832422, 'INSTALACION Y REPARACION DE RED DE FIBRA OPTICA', 1, 1, 'Presencial', 9537),
(833100, 'INSTALACIONES HIDRAULICAS Y SANITARIAS EN EDIFICACIONES RESIDENCIALES Y COMERCIALES', 1, 1, 'Presencial', 9537),
(833301, 'INSTALACION, MANTENIMIENTO Y CONVERSION DE GASODOMESTICOS.', 1, 1, 'Presencial', 9537),
(834258, 'SOLDADURA DE PRODUCTOS METALICOS EN PLATINA', 1, 1, 'Presencial', 9537),
(836135, 'CONSTRUCCIONES LIVIANAS INDUSTRIALIZADAS EN SECO', 1, 1, 'Presencial', 9537),
(836136, 'CONSTRUCCION, MANTENIMIENTO Y REPARACION DE ESTRUCTURAS EN GUADUA', 1, 1, 'Presencial', 9537),
(836137, 'MANTENIMIENTO Y REPARACION DE EDIFICACIONES', 2, 1, 'Presencial', 9537),
(836138, 'CONSTRUCCIÓN DE EDIFICACIONES', 1, 1, 'Presencial', 9537),
(836140, 'MAMPOSTERÍA', 1, 4, 'Presencial', 9537),
(836600, 'PINTURA ARQUITECTONICA Y ACABADOS ESPECIALES', 1, 1, 'Presencial', 9537),
(837501, 'MANTENIMIENTO DE EQUIPOS DE AIRE ACONDICIONADO Y REFRIGERACION', 2, 1, 'Presencial', 9537),
(838100, 'MANTENIMIENTO DE LOS MOTORES DIESEL', 1, 1, 'Presencial', 9537),
(838109, 'MANTENIMIENTO DE VEHICULOS LIVIANOS', 1, 1, 'Presencial', 9537),
(838200, 'MANTENIMIENTO ELECTRICO Y CONTROL ELECTRONICO DE AUTOMOTORES', 1, 1, 'Presencial', 9537),
(838318, 'MANTENIMIENTO DE MOTOCICLETAS Y MOTOCARROS', 1, 1, 'Presencial', 9537),
(839317, 'MANTENIMIENTO Y ENSAMBLE DE EQUIPOS ELECTRONICOS', 1, 1, 'Presencial', 9537),
(845102, 'OPERACION DE MAQUINARIA PESADA PARA EXCAVACION', 2, 1, 'Presencial', 9537),
(861100, 'CONSTRUCCION DE VIAS', 2, 1, 'Presencial', 9537),
(937116, 'PRUEBA PRUEBA PRUEBA', 1, 1, 'Virtual', 9537);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyecto_formativo`
--

CREATE TABLE `proyecto_formativo` (
  `pf_id` int(11) NOT NULL,
  `pf_codigo` varchar(50) NOT NULL,
  `pf_nombre` varchar(255) NOT NULL,
  `pf_descripcion` text DEFAULT NULL,
  `programa_prog_codigo` int(11) DEFAULT NULL,
  `centro_formacion_cent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rap_actividad`
--

CREATE TABLE `rap_actividad` (
  `rap_id` int(11) NOT NULL,
  `act_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rap_fase`
--

CREATE TABLE `rap_fase` (
  `rap_rap_id` int(11) NOT NULL,
  `fase_fase_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resultado_aprendizaje`
--

CREATE TABLE `resultado_aprendizaje` (
  `rap_id` int(11) NOT NULL,
  `rap_codigo` varchar(50) NOT NULL,
  `rap_descripcion` text NOT NULL,
  `rap_horas` int(11) NOT NULL DEFAULT 0,
  `programa_prog_id` int(11) DEFAULT NULL,
  `competencia_comp_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sede`
--

CREATE TABLE `sede` (
  `sede_id` int(11) NOT NULL,
  `sede_nombre` varchar(255) NOT NULL,
  `centro_formacion_cent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sede`
--

INSERT INTO `sede` (`sede_id`, `sede_nombre`, `centro_formacion_cent_id`) VALUES
(1, 'Industria', 9537),
(2, 'Ed Biblioteca', 9537),
(3, 'Ed Formación Pescadero', 9537),
(4, 'Externo', 9537),
(5, 'Los Patios', 9537),
(6, 'Pamplona Agroindustrial', 9537),
(7, 'Pamplona Cajas Reales', 9537),
(8, 'Ocaña Tamaco', 9537),
(9, 'Ocaña Tecnoparque', 9537);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `titulo_programa`
--

CREATE TABLE `titulo_programa` (
  `titpro_id` int(11) NOT NULL,
  `titpro_nombre` varchar(255) NOT NULL,
  `centro_formacion_cent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `titulo_programa`
--

INSERT INTO `titulo_programa` (`titpro_id`, `titpro_nombre`, `centro_formacion_cent_id`) VALUES
(1, 'Técnico', 9537),
(2, 'Tecnólogo', NULL),
(3, 'Especialización Tecnológica', NULL),
(4, 'Operario', NULL),
(5, 'Auxiliar', NULL),
(6, 'Profundización Técnica', 9537);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_coordinador`
--

CREATE TABLE `usuario_coordinador` (
  `numero_documento` bigint(20) NOT NULL,
  `coord_nombre_coordinador` varchar(255) NOT NULL,
  `coord_correo` varchar(255) NOT NULL,
  `coord_password` varchar(255) NOT NULL,
  `estado` smallint(6) NOT NULL DEFAULT 1,
  `centro_formacion_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario_coordinador`
--

INSERT INTO `usuario_coordinador` (`numero_documento`, `coord_nombre_coordinador`, `coord_correo`, `coord_password`, `estado`, `centro_formacion_id`) VALUES
(234, 'Alba Gisela Araque Orozco', 'agorozco@sena.edu.co', '$2y$10$xka1oLQex1a1vqQ5aWWqtOgskS23jojANASFzNqk5bjbaN6ZG1nzO', 1, 9537),
(567, 'Sergio Andrés Guevara Garay', 'saguevara@sena.edu.co', '$2y$10$.qSCAn6YXEhJaJzmvIKKue4wSoLeYHFOkbN1VtlnSwh0SA9QQvP.u', 1, 9537),
(12345, 'Leydi Fernanda Rojas Ortega', 'lrojaso@sena.edu.co', '$2y$10$5DIJl/IEQaWOd389fCz/x.xqJwMgn/CzMj1zFRgk0VRcZWeuLO0ey', 1, 9537),
(67890, 'Esper Perez Rivera', 'esper.perez@sena.edu.co', '$2y$10$ziCo1DySX5hDuYO6Tx0P3OXxKNtZvmzY7EfHrESa899ym8oIQztd2', 1, 9537),
(13276499, 'Javier Fernando Arenales Bernal', 'jarenalesb@sena.edu.co', '$2y$10$Dsi2pHbQa/zkaUGeDHJGUOHNnEYd8sIE4Z.xCrz8NlPH9CrwmyDg.', 1, 9537);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividad_proyecto`
--
ALTER TABLE `actividad_proyecto`
  ADD PRIMARY KEY (`act_id`),
  ADD KEY `fase_id` (`fase_id`);

--
-- Indices de la tabla `ambiente`
--
ALTER TABLE `ambiente`
  ADD PRIMARY KEY (`amb_id`),
  ADD KEY `sede_sede_id` (`sede_sede_id`);

--
-- Indices de la tabla `asignacion`
--
ALTER TABLE `asignacion`
  ADD PRIMARY KEY (`asig_id`),
  ADD KEY `instructor_inst_id` (`instructor_inst_id`),
  ADD KEY `ficha_fich_id` (`ficha_fich_id`),
  ADD KEY `ambiente_amb_id` (`ambiente_amb_id`),
  ADD KEY `competencia_comp_id` (`competencia_comp_id`);

--
-- Indices de la tabla `auditoria_asignacion`
--
ALTER TABLE `auditoria_asignacion`
  ADD PRIMARY KEY (`id_auditoria`);

--
-- Indices de la tabla `centro_formacion`
--
ALTER TABLE `centro_formacion`
  ADD PRIMARY KEY (`cent_id`);

--
-- Indices de la tabla `competencia`
--
ALTER TABLE `competencia`
  ADD PRIMARY KEY (`comp_id`,`programa_prog_id`) USING BTREE,
  ADD KEY `centro_formacion_cent_id` (`centro_formacion_cent_id`),
  ADD KEY `programa_prog_id` (`programa_prog_id`);

--
-- Indices de la tabla `competencia_horas_programa`
--
ALTER TABLE `competencia_horas_programa`
  ADD PRIMARY KEY (`prog_codigo`,`comp_id`),
  ADD KEY `comp_id` (`comp_id`);

--
-- Indices de la tabla `coordinacion`
--
ALTER TABLE `coordinacion`
  ADD PRIMARY KEY (`coord_id`),
  ADD KEY `centro_formacion_cent_id` (`centro_formacion_cent_id`),
  ADD KEY `coordinador_actual` (`coordinador_actual`);

--
-- Indices de la tabla `detallexasignacion`
--
ALTER TABLE `detallexasignacion`
  ADD PRIMARY KEY (`detasig_id`),
  ADD KEY `asignacion_asig_id` (`asignacion_asig_id`);

--
-- Indices de la tabla `fase_proyecto`
--
ALTER TABLE `fase_proyecto`
  ADD PRIMARY KEY (`fase_id`),
  ADD KEY `pf_pf_id` (`pf_pf_id`);

--
-- Indices de la tabla `ficha`
--
ALTER TABLE `ficha`
  ADD PRIMARY KEY (`fich_id`),
  ADD KEY `programa_prog_id` (`programa_prog_id`),
  ADD KEY `instructor_inst_id_lider` (`instructor_inst_id_lider`),
  ADD KEY `coordinacion_coord_id` (`coordinacion_coord_id`);

--
-- Indices de la tabla `instructor`
--
ALTER TABLE `instructor`
  ADD PRIMARY KEY (`numero_documento`),
  ADD KEY `centro_formacion_cent_id` (`centro_formacion_cent_id`);

--
-- Indices de la tabla `instru_competencia`
--
ALTER TABLE `instru_competencia`
  ADD PRIMARY KEY (`inscomp_id`),
  ADD KEY `instructor_inst_id` (`instructor_inst_id`),
  ADD KEY `programa_prog_id` (`programa_prog_id`),
  ADD KEY `competencia_comp_id` (`competencia_comp_id`);

--
-- Indices de la tabla `programa`
--
ALTER TABLE `programa`
  ADD PRIMARY KEY (`prog_codigo`),
  ADD KEY `tit_programa_titpro_id` (`tit_programa_titpro_id`),
  ADD KEY `centro_formacion_cent_id` (`centro_formacion_cent_id`);

--
-- Indices de la tabla `proyecto_formativo`
--
ALTER TABLE `proyecto_formativo`
  ADD PRIMARY KEY (`pf_id`),
  ADD KEY `programa_prog_codigo` (`programa_prog_codigo`),
  ADD KEY `centro_formacion_cent_id` (`centro_formacion_cent_id`);

--
-- Indices de la tabla `rap_actividad`
--
ALTER TABLE `rap_actividad`
  ADD PRIMARY KEY (`rap_id`,`act_id`),
  ADD KEY `act_id` (`act_id`);

--
-- Indices de la tabla `rap_fase`
--
ALTER TABLE `rap_fase`
  ADD PRIMARY KEY (`rap_rap_id`,`fase_fase_id`),
  ADD KEY `fase_fase_id` (`fase_fase_id`);

--
-- Indices de la tabla `resultado_aprendizaje`
--
ALTER TABLE `resultado_aprendizaje`
  ADD PRIMARY KEY (`rap_id`),
  ADD KEY `programa_prog_id` (`programa_prog_id`),
  ADD KEY `competencia_comp_id` (`competencia_comp_id`);

--
-- Indices de la tabla `sede`
--
ALTER TABLE `sede`
  ADD PRIMARY KEY (`sede_id`),
  ADD KEY `centro_formacion_cent_id` (`centro_formacion_cent_id`);

--
-- Indices de la tabla `titulo_programa`
--
ALTER TABLE `titulo_programa`
  ADD PRIMARY KEY (`titpro_id`),
  ADD KEY `centro_formacion_cent_id` (`centro_formacion_cent_id`);

--
-- Indices de la tabla `usuario_coordinador`
--
ALTER TABLE `usuario_coordinador`
  ADD PRIMARY KEY (`numero_documento`),
  ADD KEY `centro_formacion_id` (`centro_formacion_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividad_proyecto`
--
ALTER TABLE `actividad_proyecto`
  MODIFY `act_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `asignacion`
--
ALTER TABLE `asignacion`
  MODIFY `asig_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `auditoria_asignacion`
--
ALTER TABLE `auditoria_asignacion`
  MODIFY `id_auditoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `coordinacion`
--
ALTER TABLE `coordinacion`
  MODIFY `coord_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `detallexasignacion`
--
ALTER TABLE `detallexasignacion`
  MODIFY `detasig_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT de la tabla `fase_proyecto`
--
ALTER TABLE `fase_proyecto`
  MODIFY `fase_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `instru_competencia`
--
ALTER TABLE `instru_competencia`
  MODIFY `inscomp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1008;

--
-- AUTO_INCREMENT de la tabla `proyecto_formativo`
--
ALTER TABLE `proyecto_formativo`
  MODIFY `pf_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `resultado_aprendizaje`
--
ALTER TABLE `resultado_aprendizaje`
  MODIFY `rap_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividad_proyecto`
--
ALTER TABLE `actividad_proyecto`
  ADD CONSTRAINT `actividad_proyecto_ibfk_1` FOREIGN KEY (`fase_id`) REFERENCES `fase_proyecto` (`fase_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ambiente`
--
ALTER TABLE `ambiente`
  ADD CONSTRAINT `ambiente_ibfk_1` FOREIGN KEY (`sede_sede_id`) REFERENCES `sede` (`sede_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `asignacion`
--
ALTER TABLE `asignacion`
  ADD CONSTRAINT `asignacion_ibfk_1` FOREIGN KEY (`instructor_inst_id`) REFERENCES `instructor` (`numero_documento`),
  ADD CONSTRAINT `asignacion_ibfk_2` FOREIGN KEY (`ficha_fich_id`) REFERENCES `ficha` (`fich_id`),
  ADD CONSTRAINT `asignacion_ibfk_3` FOREIGN KEY (`ambiente_amb_id`) REFERENCES `ambiente` (`amb_id`),
  ADD CONSTRAINT `asignacion_ibfk_4` FOREIGN KEY (`competencia_comp_id`) REFERENCES `competencia` (`comp_id`);

--
-- Filtros para la tabla `competencia`
--
ALTER TABLE `competencia`
  ADD CONSTRAINT `competencia_ibfk_1` FOREIGN KEY (`centro_formacion_cent_id`) REFERENCES `centro_formacion` (`cent_id`),
  ADD CONSTRAINT `competencia_ibfk_2` FOREIGN KEY (`programa_prog_id`) REFERENCES `programa` (`prog_codigo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `competencia_horas_programa`
--
ALTER TABLE `competencia_horas_programa`
  ADD CONSTRAINT `competencia_horas_programa_ibfk_1` FOREIGN KEY (`prog_codigo`) REFERENCES `programa` (`prog_codigo`) ON DELETE CASCADE,
  ADD CONSTRAINT `competencia_horas_programa_ibfk_2` FOREIGN KEY (`comp_id`) REFERENCES `competencia` (`comp_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `coordinacion`
--
ALTER TABLE `coordinacion`
  ADD CONSTRAINT `coordinacion_ibfk_1` FOREIGN KEY (`centro_formacion_cent_id`) REFERENCES `centro_formacion` (`cent_id`),
  ADD CONSTRAINT `coordinacion_ibfk_2` FOREIGN KEY (`coordinador_actual`) REFERENCES `usuario_coordinador` (`numero_documento`);

--
-- Filtros para la tabla `detallexasignacion`
--
ALTER TABLE `detallexasignacion`
  ADD CONSTRAINT `detallexasignacion_ibfk_1` FOREIGN KEY (`asignacion_asig_id`) REFERENCES `asignacion` (`asig_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `fase_proyecto`
--
ALTER TABLE `fase_proyecto`
  ADD CONSTRAINT `fase_proyecto_ibfk_1` FOREIGN KEY (`pf_pf_id`) REFERENCES `proyecto_formativo` (`pf_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ficha`
--
ALTER TABLE `ficha`
  ADD CONSTRAINT `ficha_ibfk_1` FOREIGN KEY (`programa_prog_id`) REFERENCES `programa` (`prog_codigo`),
  ADD CONSTRAINT `ficha_ibfk_2` FOREIGN KEY (`instructor_inst_id_lider`) REFERENCES `instructor` (`numero_documento`),
  ADD CONSTRAINT `ficha_ibfk_3` FOREIGN KEY (`coordinacion_coord_id`) REFERENCES `coordinacion` (`coord_id`);

--
-- Filtros para la tabla `instructor`
--
ALTER TABLE `instructor`
  ADD CONSTRAINT `instructor_ibfk_1` FOREIGN KEY (`centro_formacion_cent_id`) REFERENCES `centro_formacion` (`cent_id`);

--
-- Filtros para la tabla `instru_competencia`
--
ALTER TABLE `instru_competencia`
  ADD CONSTRAINT `instru_competencia_ibfk_1` FOREIGN KEY (`instructor_inst_id`) REFERENCES `instructor` (`numero_documento`) ON DELETE CASCADE,
  ADD CONSTRAINT `instru_competencia_ibfk_2` FOREIGN KEY (`programa_prog_id`) REFERENCES `programa` (`prog_codigo`) ON DELETE CASCADE,
  ADD CONSTRAINT `instru_competencia_ibfk_3` FOREIGN KEY (`competencia_comp_id`) REFERENCES `competencia` (`comp_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `programa`
--
ALTER TABLE `programa`
  ADD CONSTRAINT `programa_ibfk_1` FOREIGN KEY (`tit_programa_titpro_id`) REFERENCES `titulo_programa` (`titpro_id`),
  ADD CONSTRAINT `programa_ibfk_2` FOREIGN KEY (`centro_formacion_cent_id`) REFERENCES `centro_formacion` (`cent_id`);

--
-- Filtros para la tabla `proyecto_formativo`
--
ALTER TABLE `proyecto_formativo`
  ADD CONSTRAINT `proyecto_formativo_ibfk_1` FOREIGN KEY (`programa_prog_codigo`) REFERENCES `programa` (`prog_codigo`) ON DELETE CASCADE,
  ADD CONSTRAINT `proyecto_formativo_ibfk_2` FOREIGN KEY (`centro_formacion_cent_id`) REFERENCES `centro_formacion` (`cent_id`);

--
-- Filtros para la tabla `rap_actividad`
--
ALTER TABLE `rap_actividad`
  ADD CONSTRAINT `rap_actividad_ibfk_1` FOREIGN KEY (`rap_id`) REFERENCES `resultado_aprendizaje` (`rap_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rap_actividad_ibfk_2` FOREIGN KEY (`act_id`) REFERENCES `actividad_proyecto` (`act_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `rap_fase`
--
ALTER TABLE `rap_fase`
  ADD CONSTRAINT `rap_fase_ibfk_1` FOREIGN KEY (`rap_rap_id`) REFERENCES `resultado_aprendizaje` (`rap_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rap_fase_ibfk_2` FOREIGN KEY (`fase_fase_id`) REFERENCES `fase_proyecto` (`fase_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `resultado_aprendizaje`
--
ALTER TABLE `resultado_aprendizaje`
  ADD CONSTRAINT `resultado_aprendizaje_ibfk_1` FOREIGN KEY (`programa_prog_id`) REFERENCES `programa` (`prog_codigo`) ON DELETE CASCADE,
  ADD CONSTRAINT `resultado_aprendizaje_ibfk_2` FOREIGN KEY (`competencia_comp_id`) REFERENCES `competencia` (`comp_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sede`
--
ALTER TABLE `sede`
  ADD CONSTRAINT `sede_ibfk_1` FOREIGN KEY (`centro_formacion_cent_id`) REFERENCES `centro_formacion` (`cent_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `titulo_programa`
--
ALTER TABLE `titulo_programa`
  ADD CONSTRAINT `titulo_programa_ibfk_1` FOREIGN KEY (`centro_formacion_cent_id`) REFERENCES `centro_formacion` (`cent_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuario_coordinador`
--
ALTER TABLE `usuario_coordinador`
  ADD CONSTRAINT `usuario_coordinador_ibfk_1` FOREIGN KEY (`centro_formacion_id`) REFERENCES `centro_formacion` (`cent_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
