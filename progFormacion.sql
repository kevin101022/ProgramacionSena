-- SCRIPT DE ESTRUCTURA DE BASE DE DATOS - ProgramacionSena
-- Solo creación de tablas y triggers, con Drops en Cascara.

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;

-- ============================================================
-- 1. LIMPIEZA TOTAL (Drops en Cascada)
-- ============================================================
DROP TABLE IF EXISTS public.rap_actividad CASCADE;
DROP TABLE IF EXISTS public.rap_fase CASCADE;
DROP TABLE IF EXISTS public.actividad_proyecto CASCADE;
DROP TABLE IF EXISTS public.fase_proyecto CASCADE;
DROP TABLE IF EXISTS public.proyecto_formativo CASCADE;
DROP TABLE IF EXISTS public.detallexasignacion CASCADE;
DROP TABLE IF EXISTS public.asignacion CASCADE;
DROP TABLE IF EXISTS public.auditoria_asignacion CASCADE;
DROP TABLE IF EXISTS public.instru_competencia CASCADE;
DROP TABLE IF EXISTS public.ficha CASCADE;
DROP TABLE IF EXISTS public.instructor CASCADE;
DROP TABLE IF EXISTS public.coordinacion CASCADE;
DROP TABLE IF EXISTS public.usuario_coordinador CASCADE;
DROP TABLE IF EXISTS public.resultado_aprendizaje CASCADE;
DROP TABLE IF EXISTS public.competencia_horas_programa CASCADE;
DROP TABLE IF EXISTS public.competencia CASCADE;
DROP TABLE IF EXISTS public.programa CASCADE;
DROP TABLE IF EXISTS public.titulo_programa CASCADE;
DROP TABLE IF EXISTS public.ambiente CASCADE;
DROP TABLE IF EXISTS public.sede CASCADE;
DROP TABLE IF EXISTS public.centro_formacion CASCADE;

-- Drops de Secuencias
DROP SEQUENCE IF EXISTS public.resultado_aprendizaje_rap_id_seq CASCADE;
DROP SEQUENCE IF EXISTS public.coordinacion_coord_id_seq CASCADE;
DROP SEQUENCE IF EXISTS public.instru_competencia_inscomp_id_seq CASCADE;
DROP SEQUENCE IF EXISTS public.proyecto_formativo_pf_id_seq CASCADE;
DROP SEQUENCE IF EXISTS public.fase_proyecto_fase_id_seq CASCADE;
DROP SEQUENCE IF EXISTS public.actividad_proyecto_act_id_seq CASCADE;
DROP SEQUENCE IF EXISTS public.asignacion_asig_id_seq CASCADE;
DROP SEQUENCE IF EXISTS public.detallexasignacion_detasig_id_seq CASCADE;
DROP SEQUENCE IF EXISTS public.auditoria_asignacion_id_auditoria_seq CASCADE;

DROP FUNCTION IF EXISTS public.func_auditoria_asignacion() CASCADE;

-- ============================================================
-- 2. TABLAS BASE
-- ============================================================

CREATE TABLE public.centro_formacion (
    cent_id integer PRIMARY KEY,
    cent_nombre character varying NOT NULL,
    cent_correo character varying,
    cent_password character varying
);

CREATE TABLE public.sede (
    sede_id integer PRIMARY KEY,
    sede_nombre character varying NOT NULL,
    centro_formacion_cent_id integer REFERENCES public.centro_formacion(cent_id) ON DELETE CASCADE
);

CREATE TABLE public.ambiente (
    amb_id character varying PRIMARY KEY,
    amb_nombre character varying,
    tipo_ambiente character varying DEFAULT 'Convencional' NOT NULL,
    sede_sede_id integer REFERENCES public.sede(sede_id) ON DELETE CASCADE
);

CREATE TABLE public.titulo_programa (
    titpro_id integer PRIMARY KEY,
    titpro_nombre character varying NOT NULL,
    centro_formacion_cent_id integer REFERENCES public.centro_formacion(cent_id) ON DELETE CASCADE
);

CREATE TABLE public.programa (
    prog_codigo integer PRIMARY KEY,
    prog_denominacion character varying NOT NULL,
    tit_programa_titpro_id integer REFERENCES public.titulo_programa(titpro_id),
    prog_tipo character varying NOT NULL,
    centro_formacion_cent_id integer REFERENCES public.centro_formacion(cent_id)
);

CREATE TABLE public.competencia (
    comp_id integer PRIMARY KEY,
    comp_nombre_corto character varying NOT NULL,
    comp_horas integer NOT NULL,
    comp_nombre_unidad_competencia character varying NOT NULL,
    centro_formacion_cent_id integer REFERENCES public.centro_formacion(cent_id),
    programa_prog_id integer REFERENCES public.programa(prog_codigo) ON DELETE CASCADE,
    requisitos_academicos text,
    experiencia_laboral text
);

CREATE SEQUENCE public.resultado_aprendizaje_rap_id_seq;
CREATE TABLE public.resultado_aprendizaje (
    rap_id integer DEFAULT nextval('public.resultado_aprendizaje_rap_id_seq') PRIMARY KEY,
    rap_codigo character varying NOT NULL,
    rap_descripcion text NOT NULL,
    rap_horas integer DEFAULT 0 NOT NULL,
    programa_prog_id integer REFERENCES public.programa(prog_codigo) ON DELETE CASCADE,
    competencia_comp_id integer REFERENCES public.competencia(comp_id) ON DELETE CASCADE
);

CREATE TABLE public.usuario_coordinador (
    numero_documento bigint PRIMARY KEY,
    coord_nombre_coordinador character varying NOT NULL,
    coord_correo character varying NOT NULL,
    coord_password character varying NOT NULL,
    estado smallint DEFAULT 1 NOT NULL,
    centro_formacion_id integer REFERENCES public.centro_formacion(cent_id)
);

CREATE SEQUENCE public.coordinacion_coord_id_seq;
CREATE TABLE public.coordinacion (
    coord_id integer DEFAULT nextval('public.coordinacion_coord_id_seq') PRIMARY KEY,
    coord_descripcion character varying NOT NULL,
    centro_formacion_cent_id integer REFERENCES public.centro_formacion(cent_id),
    estado smallint DEFAULT 1 NOT NULL,
    coordinador_actual bigint REFERENCES public.usuario_coordinador(numero_documento)
);

CREATE TABLE public.instructor (
    numero_documento bigint PRIMARY KEY,
    inst_nombres character varying NOT NULL,
    inst_apellidos character varying NOT NULL,
    inst_correo character varying NOT NULL,
    inst_telefono bigint NOT NULL,
    centro_formacion_cent_id integer REFERENCES public.centro_formacion(cent_id),
    inst_password character varying NOT NULL,
    profesion character varying(150),
    especializacion character varying(150),
    estado smallint DEFAULT 1 NOT NULL
);

CREATE TABLE public.ficha (
    fich_id integer PRIMARY KEY,
    programa_prog_id integer REFERENCES public.programa(prog_codigo),
    instructor_inst_id_lider bigint REFERENCES public.instructor(numero_documento),
    fich_jornada character varying NOT NULL,
    coordinacion_coord_id integer REFERENCES public.coordinacion(coord_id),
    fich_fecha_ini_lectiva date NOT NULL,
    fich_fecha_fin_lectiva date NOT NULL
);

CREATE SEQUENCE public.instru_competencia_inscomp_id_seq;
CREATE TABLE public.instru_competencia (
    inscomp_id integer DEFAULT nextval('public.instru_competencia_inscomp_id_seq') PRIMARY KEY,
    instructor_inst_id bigint REFERENCES public.instructor(numero_documento) ON DELETE CASCADE,
    programa_prog_id integer REFERENCES public.programa(prog_codigo) ON DELETE CASCADE,
    competencia_comp_id integer REFERENCES public.competencia(comp_id) ON DELETE CASCADE,
    inscomp_vigencia date NOT NULL
);

-- ============================================================
-- 3. GESTIÓN DE RIESGO Y PROYECTO FORMATIVO
-- ============================================================

CREATE SEQUENCE public.proyecto_formativo_pf_id_seq;
CREATE TABLE public.proyecto_formativo (
    pf_id integer DEFAULT nextval('public.proyecto_formativo_pf_id_seq') PRIMARY KEY,
    pf_codigo character varying NOT NULL,
    pf_nombre character varying NOT NULL,
    pf_descripcion text,
    programa_prog_codigo integer REFERENCES public.programa(prog_codigo) ON DELETE CASCADE,
    centro_formacion_cent_id integer REFERENCES public.centro_formacion(cent_id)
);

CREATE SEQUENCE public.fase_proyecto_fase_id_seq;
CREATE TABLE public.fase_proyecto (
    fase_id integer DEFAULT nextval('public.fase_proyecto_fase_id_seq') PRIMARY KEY,
    fase_nombre character varying NOT NULL,
    fase_orden smallint NOT NULL,
    fase_fecha_ini date NOT NULL,
    fase_fecha_fin date NOT NULL,
    pf_pf_id integer REFERENCES public.proyecto_formativo(pf_id) ON DELETE CASCADE
);

CREATE SEQUENCE public.actividad_proyecto_act_id_seq;
CREATE TABLE public.actividad_proyecto (
    act_id integer DEFAULT nextval('public.actividad_proyecto_act_id_seq') PRIMARY KEY,
    act_nombre character varying NOT NULL,
    fase_id integer REFERENCES public.fase_proyecto(fase_id) ON DELETE CASCADE
);

CREATE TABLE public.rap_fase (
    rap_rap_id integer REFERENCES public.resultado_aprendizaje(rap_id) ON DELETE CASCADE,
    fase_fase_id integer REFERENCES public.fase_proyecto(fase_id) ON DELETE CASCADE,
    PRIMARY KEY (rap_rap_id, fase_fase_id)
);

CREATE TABLE public.rap_actividad (
    rap_id integer REFERENCES public.resultado_aprendizaje(rap_id) ON DELETE CASCADE,
    act_id integer REFERENCES public.actividad_proyecto(act_id) ON DELETE CASCADE,
    PRIMARY KEY (rap_id, act_id)
);

-- ============================================================
-- 4. ASIGNACIÓN Y AUDITORÍA
-- ============================================================

CREATE SEQUENCE public.asignacion_asig_id_seq;
CREATE TABLE public.asignacion (
    asig_id integer DEFAULT nextval('public.asignacion_asig_id_seq') PRIMARY KEY,
    instructor_inst_id bigint REFERENCES public.instructor(numero_documento),
    asig_fecha_ini date NOT NULL,
    asig_fecha_fin date NOT NULL,
    ficha_fich_id integer REFERENCES public.ficha(fich_id),
    ambiente_amb_id character varying REFERENCES public.ambiente(amb_id),
    competencia_comp_id integer REFERENCES public.competencia(comp_id)
);

CREATE SEQUENCE public.detallexasignacion_detasig_id_seq;
CREATE TABLE public.detallexasignacion (
    detasig_id integer DEFAULT nextval('public.detallexasignacion_detasig_id_seq') PRIMARY KEY,
    asignacion_asig_id integer REFERENCES public.asignacion(asig_id) ON DELETE CASCADE,
    detasig_hora_ini time NOT NULL,
    detasig_hora_fin time NOT NULL,
    detasig_fecha date DEFAULT CURRENT_DATE NOT NULL,
    observaciones character varying
);

CREATE SEQUENCE public.auditoria_asignacion_id_auditoria_seq;
CREATE TABLE public.auditoria_asignacion (
    id_auditoria integer DEFAULT nextval('public.auditoria_asignacion_id_auditoria_seq') PRIMARY KEY,
    instructor_inst_id bigint NOT NULL,
    asig_fecha_ini date NOT NULL,
    asig_fecha_fin date NOT NULL,
    ficha_fich_id integer NOT NULL,
    ambiente_amb_id character varying NOT NULL,
    competencia_comp_id integer NOT NULL,
    asig_id integer NOT NULL,
    fecha_hora timestamp DEFAULT CURRENT_TIMESTAMP,
    documento_usuario_accion bigint NOT NULL,
    correo_usuario character varying NOT NULL,
    tipo_accion character varying NOT NULL,
    nombre_usuario_accion character varying
);

CREATE TABLE public.competencia_horas_programa (
    prog_codigo integer REFERENCES public.programa(prog_codigo) ON DELETE CASCADE,
    comp_id integer REFERENCES public.competencia(comp_id) ON DELETE CASCADE,
    horas_requeridas integer DEFAULT 0 NOT NULL,
    aplica boolean DEFAULT true NOT NULL,
    PRIMARY KEY (prog_codigo, comp_id)
);

-- ============================================================
-- 5. TRIGGERS Y FUNCIONES
-- ============================================================

CREATE OR REPLACE FUNCTION public.func_auditoria_asignacion() RETURNS trigger AS $$
BEGIN
    IF (TG_OP = 'INSERT') THEN
        INSERT INTO public.auditoria_asignacion (instructor_inst_id, asig_fecha_ini, asig_fecha_fin, ficha_fich_id, ambiente_amb_id, competencia_comp_id, asig_id, tipo_accion, documento_usuario_accion, correo_usuario, nombre_usuario_accion)
        VALUES (NEW.instructor_inst_id, NEW.asig_fecha_ini, NEW.asig_fecha_fin, NEW.ficha_fich_id, NEW.ambiente_amb_id, NEW.competencia_comp_id, NEW.asig_id, 'INSERT', 0, 'sistema@admin.com', 'Sistema');
    ELSIF (TG_OP = 'UPDATE') THEN
        INSERT INTO public.auditoria_asignacion (instructor_inst_id, asig_fecha_ini, asig_fecha_fin, ficha_fich_id, ambiente_amb_id, competencia_comp_id, asig_id, tipo_accion, documento_usuario_accion, correo_usuario, nombre_usuario_accion)
        VALUES (NEW.instructor_inst_id, NEW.asig_fecha_ini, NEW.asig_fecha_fin, NEW.ficha_fich_id, NEW.ambiente_amb_id, NEW.competencia_comp_id, NEW.asig_id, 'UPDATE', 0, 'sistema@admin.com', 'Sistema');
    ELSIF (TG_OP = 'DELETE') THEN
        INSERT INTO public.auditoria_asignacion (instructor_inst_id, asig_fecha_ini, asig_fecha_fin, ficha_fich_id, ambiente_amb_id, competencia_comp_id, asig_id, tipo_accion, documento_usuario_accion, correo_usuario, nombre_usuario_accion)
        VALUES (OLD.instructor_inst_id, OLD.asig_fecha_ini, OLD.asig_fecha_fin, OLD.ficha_fich_id, OLD.ambiente_amb_id, OLD.competencia_comp_id, OLD.asig_id, 'DELETE', 0, 'sistema@admin.com', 'Sistema');
    END IF;
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_asignacion_audit AFTER INSERT OR DELETE OR UPDATE ON public.asignacion FOR EACH ROW EXECUTE PROCEDURE public.func_auditoria_asignacion();