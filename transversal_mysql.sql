-- transversal_mysql.sql
-- Generado para compatibilidad con MariaDB / MySQL

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `centro_formacion`
--
CREATE TABLE `centro_formacion` (
  `cent_id` int(11) NOT NULL,
  `cent_nombre` varchar(100) NOT NULL,
  `cent_correo` varchar(45) DEFAULT NULL,
  `cent_password` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`cent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `titulo_programa`
--
CREATE TABLE `titulo_programa` (
  `titpro_id` int(11) NOT NULL,
  `titpro_nombre` varchar(45) NOT NULL,
  PRIMARY KEY (`titpro_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `competencia`
--
CREATE TABLE `competencia` (
  `comp_id` int(11) NOT NULL,
  `comp_nombre_corto` varchar(30) NOT NULL,
  `comp_horas` int(11) NOT NULL,
  `comp_nombre_unidad_competencia` varchar(150) NOT NULL,
  PRIMARY KEY (`comp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sede`
--
CREATE TABLE `sede` (
  `sede_id` int(11) NOT NULL,
  `sede_nombre` varchar(45) NOT NULL,
  `centro_formacion_cent_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`sede_id`),
  KEY `fk_sede_centro_formacion` (`centro_formacion_cent_id`),
  CONSTRAINT `fk_sede_centro_formacion` FOREIGN KEY (`centro_formacion_cent_id`) REFERENCES `centro_formacion` (`cent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ambiente`
--
CREATE TABLE `ambiente` (
  `amb_id` varchar(5) NOT NULL,
  `amb_nombre` varchar(45) DEFAULT NULL,
  `tipo_ambiente` varchar(50) NOT NULL DEFAULT 'Convencional',
  `sede_sede_id` int(11) NOT NULL,
  PRIMARY KEY (`amb_id`),
  KEY `fk_ambiente_sede1` (`sede_sede_id`),
  CONSTRAINT `fk_ambiente_sede1` FOREIGN KEY (`sede_sede_id`) REFERENCES `sede` (`sede_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `programa`
--
CREATE TABLE `programa` (
  `prog_codigo` int(11) NOT NULL,
  `prog_denominacion` varchar(100) NOT NULL,
  `tit_programa_titpro_id` int(11) NOT NULL,
  `prog_tipo` varchar(30) NOT NULL,
  PRIMARY KEY (`prog_codigo`),
  KEY `fk_programa_tipo_programa` (`tit_programa_titpro_id`),
  CONSTRAINT `fk_programa_tipo_programa` FOREIGN KEY (`tit_programa_titpro_id`) REFERENCES `titulo_programa` (`titpro_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `competxprograma`
--
CREATE TABLE `competxprograma` (
  `programa_prog_id` int(11) NOT NULL,
  `competencia_comp_id` int(11) NOT NULL,
  PRIMARY KEY (`programa_prog_id`, `competencia_comp_id`),
  KEY `fk_competxprograma_competencia1` (`competencia_comp_id`),
  CONSTRAINT `fk_competxprograma_competencia1` FOREIGN KEY (`competencia_comp_id`) REFERENCES `competencia` (`comp_id`),
  CONSTRAINT `fk_competxprograma_programa1` FOREIGN KEY (`programa_prog_id`) REFERENCES `programa` (`prog_codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instructor`
--
CREATE TABLE `instructor` (
  `numero_documento` bigint(20) NOT NULL,
  `inst_nombres` varchar(45) NOT NULL,
  `inst_apellidos` varchar(45) NOT NULL,
  `inst_correo` varchar(45) NOT NULL,
  `inst_telefono` bigint(20) NOT NULL,
  `centro_formacion_cent_id` int(11) NOT NULL,
  `inst_password` varchar(150) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`numero_documento`),
  KEY `fk_instructor_centro_formacion1` (`centro_formacion_cent_id`),
  CONSTRAINT `fk_instructor_centro_formacion1` FOREIGN KEY (`centro_formacion_cent_id`) REFERENCES `centro_formacion` (`cent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `coordinacion`
--
CREATE TABLE `coordinacion` (
  `numero_documento` bigint(20) NOT NULL,
  `coord_descripcion` varchar(45) NOT NULL,
  `centro_formacion_cent_id` int(11) NOT NULL,
  `coord_nombre_coordinador` varchar(45) NOT NULL,
  `coord_correo` varchar(45) NOT NULL,
  `coord_password` varchar(150) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`numero_documento`),
  KEY `fk_coordinacion_centro_formacion1` (`centro_formacion_cent_id`),
  CONSTRAINT `fk_coordinacion_centro_formacion1` FOREIGN KEY (`centro_formacion_cent_id`) REFERENCES `centro_formacion` (`cent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ficha`
--
CREATE TABLE `ficha` (
  `fich_id` int(11) NOT NULL,
  `programa_prog_id` int(11) NOT NULL,
  `instructor_inst_id_lider` bigint(20) NOT NULL,
  `fich_jornada` varchar(20) NOT NULL,
  `coordinacion_coord_id` bigint(20) NOT NULL,
  `fich_fecha_ini_lectiva` date NOT NULL,
  `fich_fecha_fin_lectiva` date NOT NULL,
  PRIMARY KEY (`fich_id`),
  KEY `fk_ficha_programa1` (`programa_prog_id`),
  KEY `fk_ficha_instructor1` (`instructor_inst_id_lider`),
  KEY `fk_ficha_coordinacion1` (`coordinacion_coord_id`),
  CONSTRAINT `fk_ficha_programa1` FOREIGN KEY (`programa_prog_id`) REFERENCES `programa` (`prog_codigo`),
  CONSTRAINT `fk_ficha_instructor1` FOREIGN KEY (`instructor_inst_id_lider`) REFERENCES `instructor` (`numero_documento`),
  CONSTRAINT `fk_ficha_coordinacion1` FOREIGN KEY (`coordinacion_coord_id`) REFERENCES `coordinacion` (`numero_documento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignacion`
--
CREATE TABLE `asignacion` (
  `asig_id` int(11) NOT NULL AUTO_INCREMENT,
  `instructor_inst_id` bigint(20) NOT NULL,
  `asig_fecha_ini` date NOT NULL,
  `asig_fecha_fin` date NOT NULL,
  `ficha_fich_id` int(11) NOT NULL,
  `ambiente_amb_id` varchar(5) NOT NULL,
  `competencia_comp_id` int(11) NOT NULL,
  PRIMARY KEY (`asig_id`),
  KEY `fk_asignacion_instructor1` (`instructor_inst_id`),
  KEY `fk_asignacion_ficha1` (`ficha_fich_id`),
  KEY `fk_asignacion_ambiente1` (`ambiente_amb_id`),
  KEY `fk_asignacion_competencia1` (`competencia_comp_id`),
  CONSTRAINT `fk_asignacion_instructor1` FOREIGN KEY (`instructor_inst_id`) REFERENCES `instructor` (`numero_documento`),
  CONSTRAINT `fk_asignacion_ficha1` FOREIGN KEY (`ficha_fich_id`) REFERENCES `ficha` (`fich_id`),
  CONSTRAINT `fk_asignacion_ambiente1` FOREIGN KEY (`ambiente_amb_id`) REFERENCES `ambiente` (`amb_id`),
  CONSTRAINT `fk_asignacion_competencia1` FOREIGN KEY (`competencia_comp_id`) REFERENCES `competencia` (`comp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallexasignacion`
--
CREATE TABLE `detallexasignacion` (
  `detasig_id` int(11) NOT NULL AUTO_INCREMENT,
  `asignacion_asig_id` int(11) NOT NULL,
  `detasig_hora_ini` time NOT NULL,
  `detasig_hora_fin` time NOT NULL,
  PRIMARY KEY (`detasig_id`),
  KEY `fk_detallexasignacion_asignacion1` (`asignacion_asig_id`),
  CONSTRAINT `fk_detallexasignacion_asignacion1` FOREIGN KEY (`asignacion_asig_id`) REFERENCES `asignacion` (`asig_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instru_competencia`
--
CREATE TABLE `instru_competencia` (
  `inscomp_id` int(11) NOT NULL AUTO_INCREMENT,
  `instructor_inst_id` bigint(20) NOT NULL,
  `competxprograma_programa_prog_id` int(11) NOT NULL,
  `competxprograma_competencia_comp_id` int(11) NOT NULL,
  `inscomp_vigencia` date NOT NULL,
  PRIMARY KEY (`inscomp_id`),
  KEY `fk_instru_competencia_instructor1` (`instructor_inst_id`),
  KEY `fk_instru_competencia_competxprograma1` (`competxprograma_programa_prog_id`, `competxprograma_competencia_comp_id`),
  CONSTRAINT `fk_instru_competencia_instructor1` FOREIGN KEY (`instructor_inst_id`) REFERENCES `instructor` (`numero_documento`),
  CONSTRAINT `fk_instru_competencia_competxprograma1` FOREIGN KEY (`competxprograma_programa_prog_id`, `competxprograma_competencia_comp_id`) REFERENCES `competxprograma` (`programa_prog_id`, `competencia_comp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_asignacion`
--
CREATE TABLE `auditoria_asignacion` (
  `id_auditoria` int(11) NOT NULL AUTO_INCREMENT,
  `instructor_inst_id` bigint(20) NOT NULL,
  `asig_fecha_ini` date NOT NULL,
  `asig_fecha_fin` date NOT NULL,
  `ficha_fich_id` int(11) NOT NULL,
  `ambiente_amb_id` varchar(5) NOT NULL,
  `competencia_comp_id` int(11) NOT NULL,
  `asig_id` int(11) NOT NULL,
  `fecha_hora` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `documento_usuario_accion` bigint(20) NOT NULL,
  `correo_usuario` varchar(45) NOT NULL,
  `tipo_accion` varchar(10) NOT NULL,
  PRIMARY KEY (`id_auditoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Disparadores (Triggers) para Auditoría en MySQL
--

DELIMITER //

CREATE TRIGGER `trg_asignacion_ins` AFTER INSERT ON `asignacion`
FOR EACH ROW
BEGIN
    INSERT INTO `auditoria_asignacion` (
        `instructor_inst_id`, `asig_fecha_ini`, `asig_fecha_fin`, `ficha_fich_id`, 
        `ambiente_amb_id`, `competencia_comp_id`, `asig_id`,
        `documento_usuario_accion`, `correo_usuario`, `tipo_accion`
    ) VALUES (
        NEW.`instructor_inst_id`, NEW.`asig_fecha_ini`, NEW.`asig_fecha_fin`, NEW.`ficha_fich_id`, 
        NEW.`ambiente_amb_id`, NEW.`competencia_comp_id`, NEW.`asig_id`,
        IFNULL(@myapp_documento_usuario, 0), IFNULL(@myapp_correo_usuario, 'Sistema'), 'INSERT'
    );
END//

CREATE TRIGGER `trg_asignacion_upd` AFTER UPDATE ON `asignacion`
FOR EACH ROW
BEGIN
    INSERT INTO `auditoria_asignacion` (
        `instructor_inst_id`, `asig_fecha_ini`, `asig_fecha_fin`, `ficha_fich_id`, 
        `ambiente_amb_id`, `competencia_comp_id`, `asig_id`,
        `documento_usuario_accion`, `correo_usuario`, `tipo_accion`
    ) VALUES (
        NEW.`instructor_inst_id`, NEW.`asig_fecha_ini`, NEW.`asig_fecha_fin`, NEW.`ficha_fich_id`, 
        NEW.`ambiente_amb_id`, NEW.`competencia_comp_id`, NEW.`asig_id`,
        IFNULL(@myapp_documento_usuario, 0), IFNULL(@myapp_correo_usuario, 'Sistema'), 'UPDATE'
    );
END//

CREATE TRIGGER `trg_asignacion_del` AFTER DELETE ON `asignacion`
FOR EACH ROW
BEGIN
    INSERT INTO `auditoria_asignacion` (
        `instructor_inst_id`, `asig_fecha_ini`, `asig_fecha_fin`, `ficha_fich_id`, 
        `ambiente_amb_id`, `competencia_comp_id`, `asig_id`,
        `documento_usuario_accion`, `correo_usuario`, `tipo_accion`
    ) VALUES (
        OLD.`instructor_inst_id`, OLD.`asig_fecha_ini`, OLD.`asig_fecha_fin`, OLD.`ficha_fich_id`, 
        OLD.`ambiente_amb_id`, OLD.`competencia_comp_id`, OLD.`asig_id`,
        IFNULL(@myapp_documento_usuario, 0), IFNULL(@myapp_correo_usuario, 'Sistema'), 'DELETE'
    );
END//

DELIMITER ;

COMMIT;
