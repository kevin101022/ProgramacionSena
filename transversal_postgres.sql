--
-- Estructura de tabla para la tabla centro_formacion
--
CREATE TABLE centro_formacion (
  cent_id integer NOT NULL,
  cent_nombre varchar(100) NOT NULL,
  PRIMARY KEY (cent_id)
);

--
-- Estructura de tabla para la tabla sede
--
CREATE TABLE sede (
  sede_id integer NOT NULL,
  sede_nombre varchar(45) NOT NULL,
  foto varchar(150) DEFAULT NULL,
  PRIMARY KEY (sede_id)
);

--
-- Estructura de tabla para la tabla ambiente
--
CREATE TABLE ambiente (
  amb_id varchar(5) NOT NULL,
  amb_nombre varchar(45) DEFAULT NULL,
  tipo_ambiente varchar(50) NOT NULL DEFAULT 'Convencional',
  SEDE_sede_id integer NOT NULL,
  PRIMARY KEY (amb_id),
  CONSTRAINT fk_AMBIENTE_SEDE1 FOREIGN KEY (SEDE_sede_id) REFERENCES sede (sede_id)
);

--
-- Estructura de tabla para la tabla titulo_programa
--
CREATE TABLE titulo_programa (
  titpro_id integer NOT NULL,
  titpro_nombre varchar(45) NOT NULL,
  PRIMARY KEY (titpro_id)
);

--
-- Estructura de tabla para la tabla programa
--
CREATE TABLE programa (
  prog_codigo integer NOT NULL,
  prog_denominacion varchar(100) NOT NULL,
  TIT_PROGRAMA_titpro_id integer NOT NULL,
  prog_tipo varchar(30) NOT NULL,
  PRIMARY KEY (prog_codigo),
  CONSTRAINT fk_PROGRAMA_TIPO_PROGRAMA FOREIGN KEY (TIT_PROGRAMA_titpro_id) REFERENCES titulo_programa (titpro_id)
);

--
-- Estructura de tabla para la tabla competencia
--
CREATE TABLE competencia (
  comp_id integer NOT NULL,
  comp_nombre_corto varchar(30) NOT NULL,
  comp_horas integer NOT NULL,
  comp_nombre_unidad_competencia varchar(150) NOT NULL,
  PRIMARY KEY (comp_id)
);

--
-- Estructura de tabla para la tabla competxprograma
--
CREATE TABLE competxprograma (
  PROGRAMA_prog_id integer NOT NULL,
  COMPETENCIA_comp_id integer NOT NULL,
  PRIMARY KEY (PROGRAMA_prog_id, COMPETENCIA_comp_id),
  CONSTRAINT fk_COMPETxPROGRAMA_COMPETENCIA1 FOREIGN KEY (COMPETENCIA_comp_id) REFERENCES competencia (comp_id),
  CONSTRAINT fk_COMPETxPROGRAMA_PROGRAMA1 FOREIGN KEY (PROGRAMA_prog_id) REFERENCES programa (prog_codigo)
);

--
-- Estructura de tabla para la tabla instructor
--
CREATE TABLE instructor (
  inst_id integer NOT NULL,
  inst_nombres varchar(45) NOT NULL,
  inst_apellidos varchar(45) NOT NULL,
  inst_correo varchar(45) NOT NULL,
  inst_telefono bigint NOT NULL,
  CENTRO_FORMACION_cent_id integer NOT NULL,
  inst_password varchar(150) NOT NULL, -- Aumentado para soportar hashes de password
  PRIMARY KEY (inst_id),
  CONSTRAINT fk_INSTRUCTOR_CENTRO_FORMACION1 FOREIGN KEY (CENTRO_FORMACION_cent_id) REFERENCES centro_formacion (cent_id)
);

--
-- Estructura de tabla para la tabla coordinacion
--
CREATE TABLE coordinacion (
  coord_id integer NOT NULL,
  coord_descripcion varchar(45) NOT NULL,
  CENTRO_FORMACION_cent_id integer NOT NULL,
  coord_nombre_coordinador varchar(45) NOT NULL,
  coord_correo varchar(45) NOT NULL,
  coord_password varchar(150) NOT NULL, -- Aumentado
  PRIMARY KEY (coord_id),
  CONSTRAINT fk_COORDINACION_CENTRO_FORMACION1 FOREIGN KEY (CENTRO_FORMACION_cent_id) REFERENCES centro_formacion (cent_id)
);

--
-- Estructura de tabla para la tabla ficha
--
CREATE TABLE ficha (
  fich_id integer NOT NULL,
  PROGRAMA_prog_id integer NOT NULL,
  INSTRUCTOR_inst_id_lider integer NOT NULL,
  fich_jornada varchar(20) NOT NULL,
  COORDINACION_coord_id integer NOT NULL,
  fich_fecha_ini_lectiva date NOT NULL,
  fich_fecha_fin_lectiva date NOT NULL,
  PRIMARY KEY (fich_id),
  CONSTRAINT fk_FICHA_COORDINACION1 FOREIGN KEY (COORDINACION_coord_id) REFERENCES coordinacion (coord_id),
  CONSTRAINT fk_FICHA_INSTRUCTOR1 FOREIGN KEY (INSTRUCTOR_inst_id_lider) REFERENCES instructor (inst_id),
  CONSTRAINT fk_FICHA_PROGRAMA1 FOREIGN KEY (PROGRAMA_prog_id) REFERENCES programa (prog_codigo)
);

--
-- Estructura de tabla para la tabla asignacion
--
CREATE TABLE asignacion (
  ASIG_ID SERIAL PRIMARY KEY,
  INSTRUCTOR_inst_id integer NOT NULL,
  asig_fecha_ini date NOT NULL,
  asig_fecha_fin date NOT NULL,
  FICHA_fich_id integer NOT NULL,
  AMBIENTE_amb_id varchar(5) NOT NULL,
  COMPETENCIA_comp_id integer NOT NULL,
  CONSTRAINT fk_ASIGNACION_AMBIENTE1 FOREIGN KEY (AMBIENTE_amb_id) REFERENCES ambiente (amb_id),
  CONSTRAINT fk_ASIGNACION_COMPETENCIA1 FOREIGN KEY (COMPETENCIA_comp_id) REFERENCES competencia (comp_id),
  CONSTRAINT fk_ASIGNACION_FICHA1 FOREIGN KEY (FICHA_fich_id) REFERENCES ficha (fich_id),
  CONSTRAINT fk_ASIGNACION_INSTRUCTOR1 FOREIGN KEY (INSTRUCTOR_inst_id) REFERENCES instructor (inst_id)
);

--
-- Estructura de tabla para la tabla detallexasignacion
--
CREATE TABLE detallexasignacion (
  detasig_id SERIAL PRIMARY KEY,
  ASIGNACION_ASIG_ID integer NOT NULL,
  detasig_hora_ini time NOT NULL,
  detasig_hora_fin time NOT NULL,
  CONSTRAINT fk_DETALLExASIGNACION_ASIGNACION1 FOREIGN KEY (ASIGNACION_ASIG_ID) REFERENCES asignacion (ASIG_ID)
);

--
-- Estructura de tabla para la tabla instru_competencia
--
CREATE TABLE instru_competencia (
  inscomp_id SERIAL PRIMARY KEY,
  INSTRUCTOR_inst_id integer NOT NULL,
  COMPETxPROGRAMA_PROGRAMA_prog_id integer NOT NULL,
  COMPETxPROGRAMA_COMPETENCIA_comp_id integer NOT NULL,
  inscomp_vigencia date NOT NULL,
  CONSTRAINT fk_INSTRU_COMPETENCIA_COMPETxPROGRAMA1 FOREIGN KEY (COMPETxPROGRAMA_PROGRAMA_prog_id, COMPETxPROGRAMA_COMPETENCIA_comp_id) REFERENCES competxprograma (PROGRAMA_prog_id, COMPETENCIA_comp_id),
  CONSTRAINT fk_INSTRU_COMPETENCIA_INSTRUCTOR1 FOREIGN KEY (INSTRUCTOR_inst_id) REFERENCES instructor (inst_id)
);
