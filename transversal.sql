-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-02-2026 a las 23:56:08
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `transversal`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ambiente`
--

CREATE TABLE `ambiente` (
  `amb_id` varchar(5) NOT NULL,
  `amb_nombre` varchar(45) DEFAULT NULL,
  `tipo_ambiente` varchar(50) NOT NULL,
  `SEDE_sede_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignacion`
--

CREATE TABLE `asignacion` (
  `INSTRUCTOR_inst_id` int(11) NOT NULL,
  `asig_fecha_ini` date NOT NULL,
  `asig_fecha_fin` date NOT NULL,
  `FICHA_fich_id` int(11) NOT NULL,
  `AMBIENTE_amb_id` varchar(5) NOT NULL,
  `COMPETENCIA_comp_id` int(11) NOT NULL,
  `ASIG_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `centro_formacion`
--

CREATE TABLE `centro_formacion` (
  `cent_id` int(11) NOT NULL,
  `cent_nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `competencia`
--

CREATE TABLE `competencia` (
  `comp_id` int(11) NOT NULL,
  `comp_nombre_corto` varchar(30) NOT NULL,
  `comp_horas` int(11) NOT NULL,
  `comp_nombre_unidad_competencia` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `competxprograma`
--

CREATE TABLE `competxprograma` (
  `PROGRAMA_prog_id` int(11) NOT NULL,
  `COMPETENCIA_comp_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `coordinacion`
--

CREATE TABLE `coordinacion` (
  `coord_id` int(11) NOT NULL,
  `coord_descripcion` varchar(45) NOT NULL,
  `CENTRO_FORMACION_cent_id` int(11) NOT NULL,
  `coord_nombre_coordinador` varchar(45) NOT NULL,
  `coord_correo` varchar(45) NOT NULL,
  `coord_password` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallexasignacion`
--

CREATE TABLE `detallexasignacion` (
  `ASIGNACION_ASIG_ID` int(11) NOT NULL,
  `detasig_hora_ini` time NOT NULL,
  `detasig_hora_fin` time NOT NULL,
  `detasig_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ficha`
--

CREATE TABLE `ficha` (
  `fich_id` int(11) NOT NULL,
  `PROGRAMA_prog_id` int(11) NOT NULL,
  `INSTRUCTOR_inst_id_lider` int(11) NOT NULL,
  `fich_jornada` varchar(20) NOT NULL,
  `COORDINACION_coord_id` int(11) NOT NULL,
  `fich_fecha_ini_lectiva` date NOT NULL,
  `fich_fecha_fin_lectiva` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instructor`
--

CREATE TABLE `instructor` (
  `inst_id` int(11) NOT NULL,
  `inst_nombres` varchar(45) NOT NULL,
  `inst_apellidos` varchar(45) NOT NULL,
  `inst_correo` varchar(45) NOT NULL,
  `inst_telefono` bigint(20) NOT NULL,
  `CENTRO_FORMACION_cent_id` int(11) NOT NULL,
  `inst_password` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instru_competencia`
--

CREATE TABLE `instru_competencia` (
  `INSTRUCTOR_inst_id` int(11) NOT NULL,
  `COMPETxPROGRAMA_PROGRAMA_prog_id` int(11) NOT NULL,
  `COMPETxPROGRAMA_COMPETENCIA_comp_id` int(11) NOT NULL,
  `inscomp_vigencia` date NOT NULL,
  `inscomp_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `programa`
--

CREATE TABLE `programa` (
  `prog_codigo` int(11) NOT NULL,
  `prog_denominacion` varchar(100) NOT NULL,
  `TIT_PROGRAMA_titpro_id` int(11) NOT NULL,
  `prog_tipo` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sede`
--

CREATE TABLE `sede` (
  `sede_id` int(11) NOT NULL,
  `sede_nombre` varchar(45) NOT NULL,
  `foto` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `titulo_programa`
--

CREATE TABLE `titulo_programa` (
  `titpro_id` int(11) NOT NULL,
  `titpro_nombre` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `ambiente`
--
ALTER TABLE `ambiente`
  ADD PRIMARY KEY (`amb_id`),
  ADD KEY `fk_AMBIENTE_SEDE1` (`SEDE_sede_id`);

--
-- Indices de la tabla `asignacion`
--
ALTER TABLE `asignacion`
  ADD PRIMARY KEY (`ASIG_ID`),
  ADD KEY `fk_ASIGNACION_INSTRUCTOR1` (`INSTRUCTOR_inst_id`),
  ADD KEY `fk_ASIGNACION_FICHA1` (`FICHA_fich_id`),
  ADD KEY `fk_ASIGNACION_AMBIENTE1` (`AMBIENTE_amb_id`),
  ADD KEY `fk_ASIGNACION_COMPETENCIA1` (`COMPETENCIA_comp_id`);

--
-- Indices de la tabla `centro_formacion`
--
ALTER TABLE `centro_formacion`
  ADD PRIMARY KEY (`cent_id`);

--
-- Indices de la tabla `competencia`
--
ALTER TABLE `competencia`
  ADD PRIMARY KEY (`comp_id`);

--
-- Indices de la tabla `competxprograma`
--
ALTER TABLE `competxprograma`
  ADD PRIMARY KEY (`PROGRAMA_prog_id`,`COMPETENCIA_comp_id`),
  ADD KEY `fk_COMPETxPROGRAMA_COMPETENCIA1` (`COMPETENCIA_comp_id`);

--
-- Indices de la tabla `coordinacion`
--
ALTER TABLE `coordinacion`
  ADD PRIMARY KEY (`coord_id`),
  ADD KEY `fk_COORDINACION_CENTRO_FORMACION1` (`CENTRO_FORMACION_cent_id`);

--
-- Indices de la tabla `detallexasignacion`
--
ALTER TABLE `detallexasignacion`
  ADD PRIMARY KEY (`detasig_id`),
  ADD KEY `fk_DETALLExASIGNACION_ASIGNACION1` (`ASIGNACION_ASIG_ID`);

--
-- Indices de la tabla `ficha`
--
ALTER TABLE `ficha`
  ADD PRIMARY KEY (`fich_id`),
  ADD KEY `fk_FICHA_PROGRAMA1` (`PROGRAMA_prog_id`),
  ADD KEY `fk_FICHA_INSTRUCTOR1` (`INSTRUCTOR_inst_id_lider`),
  ADD KEY `fk_FICHA_COORDINACION1` (`COORDINACION_coord_id`);

--
-- Indices de la tabla `instructor`
--
ALTER TABLE `instructor`
  ADD PRIMARY KEY (`inst_id`),
  ADD KEY `fk_INSTRUCTOR_CENTRO_FORMACION1` (`CENTRO_FORMACION_cent_id`);

--
-- Indices de la tabla `instru_competencia`
--
ALTER TABLE `instru_competencia`
  ADD PRIMARY KEY (`inscomp_id`),
  ADD KEY `fk_INSTRU_COMPETENCIA_INSTRUCTOR1` (`INSTRUCTOR_inst_id`),
  ADD KEY `fk_INSTRU_COMPETENCIA_COMPETxPROGRAMA1` (`COMPETxPROGRAMA_PROGRAMA_prog_id`,`COMPETxPROGRAMA_COMPETENCIA_comp_id`);

--
-- Indices de la tabla `programa`
--
ALTER TABLE `programa`
  ADD PRIMARY KEY (`prog_codigo`),
  ADD KEY `fk_PROGRAMA_TIPO_PROGRAMA` (`TIT_PROGRAMA_titpro_id`);

--
-- Indices de la tabla `sede`
--
ALTER TABLE `sede`
  ADD PRIMARY KEY (`sede_id`);

--
-- Indices de la tabla `titulo_programa`
--
ALTER TABLE `titulo_programa`
  ADD PRIMARY KEY (`titpro_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asignacion`
--
ALTER TABLE `asignacion`
  MODIFY `ASIG_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `detallexasignacion`
--
ALTER TABLE `detallexasignacion`
  MODIFY `detasig_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `instru_competencia`
--
ALTER TABLE `instru_competencia`
  MODIFY `inscomp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ambiente`
--
ALTER TABLE `ambiente`
  ADD CONSTRAINT `fk_AMBIENTE_SEDE1` FOREIGN KEY (`SEDE_sede_id`) REFERENCES `sede` (`sede_id`);

--
-- Filtros para la tabla `asignacion`
--
ALTER TABLE `asignacion`
  ADD CONSTRAINT `fk_ASIGNACION_AMBIENTE1` FOREIGN KEY (`AMBIENTE_amb_id`) REFERENCES `ambiente` (`amb_id`),
  ADD CONSTRAINT `fk_ASIGNACION_COMPETENCIA1` FOREIGN KEY (`COMPETENCIA_comp_id`) REFERENCES `competencia` (`comp_id`),
  ADD CONSTRAINT `fk_ASIGNACION_FICHA1` FOREIGN KEY (`FICHA_fich_id`) REFERENCES `ficha` (`fich_id`),
  ADD CONSTRAINT `fk_ASIGNACION_INSTRUCTOR1` FOREIGN KEY (`INSTRUCTOR_inst_id`) REFERENCES `instructor` (`inst_id`);

--
-- Filtros para la tabla `competxprograma`
--
ALTER TABLE `competxprograma`
  ADD CONSTRAINT `fk_COMPETxPROGRAMA_COMPETENCIA1` FOREIGN KEY (`COMPETENCIA_comp_id`) REFERENCES `competencia` (`comp_id`),
  ADD CONSTRAINT `fk_COMPETxPROGRAMA_PROGRAMA1` FOREIGN KEY (`PROGRAMA_prog_id`) REFERENCES `programa` (`prog_codigo`);

--
-- Filtros para la tabla `coordinacion`
--
ALTER TABLE `coordinacion`
  ADD CONSTRAINT `fk_COORDINACION_CENTRO_FORMACION1` FOREIGN KEY (`CENTRO_FORMACION_cent_id`) REFERENCES `centro_formacion` (`cent_id`);

--
-- Filtros para la tabla `detallexasignacion`
--
ALTER TABLE `detallexasignacion`
  ADD CONSTRAINT `fk_DETALLExASIGNACION_ASIGNACION1` FOREIGN KEY (`ASIGNACION_ASIG_ID`) REFERENCES `asignacion` (`ASIG_ID`);

--
-- Filtros para la tabla `ficha`
--
ALTER TABLE `ficha`
  ADD CONSTRAINT `fk_FICHA_COORDINACION1` FOREIGN KEY (`COORDINACION_coord_id`) REFERENCES `coordinacion` (`coord_id`),
  ADD CONSTRAINT `fk_FICHA_INSTRUCTOR1` FOREIGN KEY (`INSTRUCTOR_inst_id_lider`) REFERENCES `instructor` (`inst_id`),
  ADD CONSTRAINT `fk_FICHA_PROGRAMA1` FOREIGN KEY (`PROGRAMA_prog_id`) REFERENCES `programa` (`prog_codigo`);

--
-- Filtros para la tabla `instructor`
--
ALTER TABLE `instructor`
  ADD CONSTRAINT `fk_INSTRUCTOR_CENTRO_FORMACION1` FOREIGN KEY (`CENTRO_FORMACION_cent_id`) REFERENCES `centro_formacion` (`cent_id`);

--
-- Filtros para la tabla `instru_competencia`
--
ALTER TABLE `instru_competencia`
  ADD CONSTRAINT `fk_INSTRU_COMPETENCIA_COMPETxPROGRAMA1` FOREIGN KEY (`COMPETxPROGRAMA_PROGRAMA_prog_id`,`COMPETxPROGRAMA_COMPETENCIA_comp_id`) REFERENCES `competxprograma` (`PROGRAMA_prog_id`, `COMPETENCIA_comp_id`),
  ADD CONSTRAINT `fk_INSTRU_COMPETENCIA_INSTRUCTOR1` FOREIGN KEY (`INSTRUCTOR_inst_id`) REFERENCES `instructor` (`inst_id`);

--
-- Filtros para la tabla `programa`
--
ALTER TABLE `programa`
  ADD CONSTRAINT `fk_PROGRAMA_TIPO_PROGRAMA` FOREIGN KEY (`TIT_PROGRAMA_titpro_id`) REFERENCES `titulo_programa` (`titpro_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
