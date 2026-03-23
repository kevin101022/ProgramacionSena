-- ============================================================
-- SCRIPT DE ESTRUCTURA DE BASE DE DATOS - ProgramacionSena (MySQL)
-- Solo creación de tablas y triggers.
-- ============================================================

-- Desactivar temporalmente la comprobación de llaves foráneas para poder hacer los DROPs
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- 1. LIMPIEZA TOTAL
-- ============================================================
DROP TABLE IF EXISTS rap_actividad;
DROP TABLE IF EXISTS rap_fase;
DROP TABLE IF EXISTS actividad_proyecto;
DROP TABLE IF EXISTS fase_proyecto;
DROP TABLE IF EXISTS proyecto_formativo;
DROP TABLE IF EXISTS detallexasignacion;
DROP TABLE IF EXISTS asignacion;
DROP TABLE IF EXISTS auditoria_asignacion;
DROP TABLE IF EXISTS instru_competencia;
DROP TABLE IF EXISTS ficha;
DROP TABLE IF EXISTS instructor;
DROP TABLE IF EXISTS coordinacion;
DROP TABLE IF EXISTS usuario_coordinador;
DROP TABLE IF EXISTS resultado_aprendizaje;
DROP TABLE IF EXISTS competencia_horas_programa;
DROP TABLE IF EXISTS competencia;
DROP TABLE IF EXISTS programa;
DROP TABLE IF EXISTS titulo_programa;
DROP TABLE IF EXISTS ambiente;
DROP TABLE IF EXISTS sede;
DROP TABLE IF EXISTS centro_formacion;

-- Reactivar comprobación de llaves foráneas
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- 2. TABLAS BASE
-- ============================================================

CREATE TABLE centro_formacion (
    cent_id INT PRIMARY KEY,
    cent_nombre VARCHAR(255) NOT NULL,
    cent_correo VARCHAR(255),
    cent_password VARCHAR(255)
);

CREATE TABLE sede (
    sede_id INT PRIMARY KEY,
    sede_nombre VARCHAR(255) NOT NULL,
    centro_formacion_cent_id INT,
    FOREIGN KEY (centro_formacion_cent_id) REFERENCES centro_formacion(cent_id) ON DELETE CASCADE
);

CREATE TABLE ambiente (
    amb_id VARCHAR(255) PRIMARY KEY,
    amb_nombre VARCHAR(255),
    tipo_ambiente VARCHAR(255) DEFAULT 'Convencional' NOT NULL,
    sede_sede_id INT,
    FOREIGN KEY (sede_sede_id) REFERENCES sede(sede_id) ON DELETE CASCADE
);

CREATE TABLE titulo_programa (
    titpro_id INT PRIMARY KEY,
    titpro_nombre VARCHAR(255) NOT NULL,
    centro_formacion_cent_id INT,
    FOREIGN KEY (centro_formacion_cent_id) REFERENCES centro_formacion(cent_id) ON DELETE CASCADE
);

CREATE TABLE programa (
    prog_codigo INT PRIMARY KEY,
    prog_denominacion VARCHAR(255) NOT NULL,
    tit_programa_titpro_id INT,
    prog_tipo VARCHAR(255) NOT NULL,
    centro_formacion_cent_id INT,
    FOREIGN KEY (tit_programa_titpro_id) REFERENCES titulo_programa(titpro_id),
    FOREIGN KEY (centro_formacion_cent_id) REFERENCES centro_formacion(cent_id)
);

CREATE TABLE competencia (
    comp_id INT PRIMARY KEY,
    comp_nombre_corto VARCHAR(255) NOT NULL,
    comp_horas INT NOT NULL,
    comp_nombre_unidad_competencia VARCHAR(255) NOT NULL,
    centro_formacion_cent_id INT,
    programa_prog_id INT,
    requisitos_academicos TEXT,
    experiencia_laboral TEXT,
    FOREIGN KEY (centro_formacion_cent_id) REFERENCES centro_formacion(cent_id),
    FOREIGN KEY (programa_prog_id) REFERENCES programa(prog_codigo) ON DELETE CASCADE
);

CREATE TABLE resultado_aprendizaje (
    rap_id INT AUTO_INCREMENT PRIMARY KEY,
    rap_codigo VARCHAR(255) NOT NULL,
    rap_descripcion TEXT NOT NULL,
    rap_horas INT DEFAULT 0 NOT NULL,
    programa_prog_id INT,
    competencia_comp_id INT,
    FOREIGN KEY (programa_prog_id) REFERENCES programa(prog_codigo) ON DELETE CASCADE,
    FOREIGN KEY (competencia_comp_id) REFERENCES competencia(comp_id) ON DELETE CASCADE
);

CREATE TABLE usuario_coordinador (
    numero_documento BIGINT PRIMARY KEY,
    coord_nombre_coordinador VARCHAR(255) NOT NULL,
    coord_correo VARCHAR(255) NOT NULL,
    coord_password VARCHAR(255) NOT NULL,
    estado SMALLINT DEFAULT 1 NOT NULL,
    centro_formacion_id INT,
    FOREIGN KEY (centro_formacion_id) REFERENCES centro_formacion(cent_id)
);

CREATE TABLE coordinacion (
    coord_id INT AUTO_INCREMENT PRIMARY KEY,
    coord_descripcion VARCHAR(255) NOT NULL,
    centro_formacion_cent_id INT,
    estado SMALLINT DEFAULT 1 NOT NULL,
    coordinador_actual BIGINT,
    FOREIGN KEY (centro_formacion_cent_id) REFERENCES centro_formacion(cent_id),
    FOREIGN KEY (coordinador_actual) REFERENCES usuario_coordinador(numero_documento)
);

CREATE TABLE instructor (
    numero_documento BIGINT PRIMARY KEY,
    inst_nombres VARCHAR(255) NOT NULL,
    inst_apellidos VARCHAR(255) NOT NULL,
    inst_correo VARCHAR(255) NOT NULL,
    inst_telefono BIGINT NOT NULL,
    centro_formacion_cent_id INT,
    inst_password VARCHAR(255) NOT NULL,
    profesion VARCHAR(150),
    especializacion VARCHAR(150),
    estado SMALLINT DEFAULT 1 NOT NULL,
    FOREIGN KEY (centro_formacion_cent_id) REFERENCES centro_formacion(cent_id)
);

CREATE TABLE ficha (
    fich_id INT PRIMARY KEY,
    programa_prog_id INT,
    instructor_inst_id_lider BIGINT,
    fich_jornada VARCHAR(255) NOT NULL,
    coordinacion_coord_id INT,
    fich_fecha_ini_lectiva DATE NOT NULL,
    fich_fecha_fin_lectiva DATE NOT NULL,
    FOREIGN KEY (programa_prog_id) REFERENCES programa(prog_codigo),
    FOREIGN KEY (instructor_inst_id_lider) REFERENCES instructor(numero_documento),
    FOREIGN KEY (coordinacion_coord_id) REFERENCES coordinacion(coord_id)
);

CREATE TABLE instru_competencia (
    inscomp_id INT AUTO_INCREMENT PRIMARY KEY,
    instructor_inst_id BIGINT,
    programa_prog_id INT,
    competencia_comp_id INT,
    inscomp_vigencia DATE NOT NULL,
    FOREIGN KEY (instructor_inst_id) REFERENCES instructor(numero_documento) ON DELETE CASCADE,
    FOREIGN KEY (programa_prog_id) REFERENCES programa(prog_codigo) ON DELETE CASCADE,
    FOREIGN KEY (competencia_comp_id) REFERENCES competencia(comp_id) ON DELETE CASCADE
);

-- ============================================================
-- 3. GESTIÓN DE RIESGO Y PROYECTO FORMATIVO
-- ============================================================

CREATE TABLE proyecto_formativo (
    pf_id INT AUTO_INCREMENT PRIMARY KEY,
    pf_codigo VARCHAR(255) NOT NULL,
    pf_nombre VARCHAR(255) NOT NULL,
    pf_descripcion TEXT,
    programa_prog_codigo INT,
    centro_formacion_cent_id INT,
    FOREIGN KEY (programa_prog_codigo) REFERENCES programa(prog_codigo) ON DELETE CASCADE,
    FOREIGN KEY (centro_formacion_cent_id) REFERENCES centro_formacion(cent_id)
);

CREATE TABLE fase_proyecto (
    fase_id INT AUTO_INCREMENT PRIMARY KEY,
    fase_nombre VARCHAR(255) NOT NULL,
    fase_orden SMALLINT NOT NULL,
    fase_fecha_ini DATE NOT NULL,
    fase_fecha_fin DATE NOT NULL,
    pf_pf_id INT,
    FOREIGN KEY (pf_pf_id) REFERENCES proyecto_formativo(pf_id) ON DELETE CASCADE
);

CREATE TABLE actividad_proyecto (
    act_id INT AUTO_INCREMENT PRIMARY KEY,
    act_nombre VARCHAR(255) NOT NULL,
    fase_id INT,
    FOREIGN KEY (fase_id) REFERENCES fase_proyecto(fase_id) ON DELETE CASCADE
);

CREATE TABLE rap_fase (
    rap_rap_id INT,
    fase_fase_id INT,
    PRIMARY KEY (rap_rap_id, fase_fase_id),
    FOREIGN KEY (rap_rap_id) REFERENCES resultado_aprendizaje(rap_id) ON DELETE CASCADE,
    FOREIGN KEY (fase_fase_id) REFERENCES fase_proyecto(fase_id) ON DELETE CASCADE
);

CREATE TABLE rap_actividad (
    rap_id INT,
    act_id INT,
    PRIMARY KEY (rap_id, act_id),
    FOREIGN KEY (rap_id) REFERENCES resultado_aprendizaje(rap_id) ON DELETE CASCADE,
    FOREIGN KEY (act_id) REFERENCES actividad_proyecto(act_id) ON DELETE CASCADE
);

-- ============================================================
-- 4. ASIGNACIÓN Y AUDITORÍA
-- ============================================================

CREATE TABLE asignacion (
    asig_id INT AUTO_INCREMENT PRIMARY KEY,
    instructor_inst_id BIGINT,
    asig_fecha_ini DATE NOT NULL,
    asig_fecha_fin DATE NOT NULL,
    ficha_fich_id INT,
    ambiente_amb_id VARCHAR(255),
    competencia_comp_id INT,
    FOREIGN KEY (instructor_inst_id) REFERENCES instructor(numero_documento),
    FOREIGN KEY (ficha_fich_id) REFERENCES ficha(fich_id),
    FOREIGN KEY (ambiente_amb_id) REFERENCES ambiente(amb_id),
    FOREIGN KEY (competencia_comp_id) REFERENCES competencia(comp_id)
);

CREATE TABLE detallexasignacion (
    detasig_id INT AUTO_INCREMENT PRIMARY KEY,
    asignacion_asig_id INT,
    detasig_hora_ini TIME NOT NULL,
    detasig_hora_fin TIME NOT NULL,
    detasig_fecha DATE DEFAULT (CURRENT_DATE) NOT NULL,
    observaciones VARCHAR(255),
    FOREIGN KEY (asignacion_asig_id) REFERENCES asignacion(asig_id) ON DELETE CASCADE
);

CREATE TABLE auditoria_asignacion (
    id_auditoria INT AUTO_INCREMENT PRIMARY KEY,
    instructor_inst_id BIGINT NOT NULL,
    asig_fecha_ini DATE NOT NULL,
    asig_fecha_fin DATE NOT NULL,
    ficha_fich_id INT NOT NULL,
    ambiente_amb_id VARCHAR(255) NOT NULL,
    competencia_comp_id INT NOT NULL,
    asig_id INT NOT NULL,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    documento_usuario_accion BIGINT NOT NULL,
    correo_usuario VARCHAR(255) NOT NULL,
    tipo_accion VARCHAR(255) NOT NULL,
    nombre_usuario_accion VARCHAR(255)
);

CREATE TABLE competencia_horas_programa (
    prog_codigo INT,
    comp_id INT,
    horas_requeridas INT DEFAULT 0 NOT NULL,
    aplica BOOLEAN DEFAULT TRUE NOT NULL,
    PRIMARY KEY (prog_codigo, comp_id),
    FOREIGN KEY (prog_codigo) REFERENCES programa(prog_codigo) ON DELETE CASCADE,
    FOREIGN KEY (comp_id) REFERENCES competencia(comp_id) ON DELETE CASCADE
);

-- ============================================================
-- 5. TRIGGERS
-- ============================================================

DELIMITER //

-- Trigger para INSERT
CREATE TRIGGER trg_asignacion_audit_insert
AFTER INSERT ON asignacion
FOR EACH ROW
BEGIN
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
END //

-- Trigger para UPDATE
CREATE TRIGGER trg_asignacion_audit_update
AFTER UPDATE ON asignacion
FOR EACH ROW
BEGIN
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
END //

-- Trigger para DELETE
CREATE TRIGGER trg_asignacion_audit_delete
AFTER DELETE ON asignacion
FOR EACH ROW
BEGIN
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
END //

DELIMITER ;