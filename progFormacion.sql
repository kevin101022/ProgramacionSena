--
-- PostgreSQL database dump
--



-- Dumped from database version 18.1
-- Dumped by pg_dump version 18.1

-- Started on 2026-03-13 11:56:10

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

ALTER TABLE IF EXISTS ONLY public.usuario_coordinador DROP CONSTRAINT IF EXISTS fk_user_centro;
ALTER TABLE IF EXISTS ONLY public.titulo_programa DROP CONSTRAINT IF EXISTS fk_titulo_programa_centro;
ALTER TABLE IF EXISTS ONLY public.programa DROP CONSTRAINT IF EXISTS fk_programa_tipo_programa;
ALTER TABLE IF EXISTS ONLY public.programa DROP CONSTRAINT IF EXISTS fk_programa_centro;
ALTER TABLE IF EXISTS ONLY public.instructor DROP CONSTRAINT IF EXISTS fk_instructor_centro_formacion1;
ALTER TABLE IF EXISTS ONLY public.instru_competencia DROP CONSTRAINT IF EXISTS fk_instru_competencia_instructor1;
ALTER TABLE IF EXISTS ONLY public.instru_competencia DROP CONSTRAINT IF EXISTS fk_instru_competencia_competxprograma1;
ALTER TABLE IF EXISTS ONLY public.ficha DROP CONSTRAINT IF EXISTS fk_ficha_programa1;
ALTER TABLE IF EXISTS ONLY public.ficha DROP CONSTRAINT IF EXISTS fk_ficha_instructor1;
ALTER TABLE IF EXISTS ONLY public.ficha DROP CONSTRAINT IF EXISTS fk_ficha_coordinacion;
ALTER TABLE IF EXISTS ONLY public.detallexasignacion DROP CONSTRAINT IF EXISTS fk_detallexasignacion_asignacion1;
ALTER TABLE IF EXISTS ONLY public.coordinacion DROP CONSTRAINT IF EXISTS fk_coordinador_actual;
ALTER TABLE IF EXISTS ONLY public.coordinacion DROP CONSTRAINT IF EXISTS fk_coordinacion_centro_formacion1;
ALTER TABLE IF EXISTS ONLY public.competxprograma DROP CONSTRAINT IF EXISTS fk_competxprograma_programa1;
ALTER TABLE IF EXISTS ONLY public.competxprograma DROP CONSTRAINT IF EXISTS fk_competxprograma_competencia1;
ALTER TABLE IF EXISTS ONLY public.competencia DROP CONSTRAINT IF EXISTS fk_competencia_centro;
ALTER TABLE IF EXISTS ONLY public.asignacion DROP CONSTRAINT IF EXISTS fk_asignacion_instructor1;
ALTER TABLE IF EXISTS ONLY public.asignacion DROP CONSTRAINT IF EXISTS fk_asignacion_ficha1;
ALTER TABLE IF EXISTS ONLY public.asignacion DROP CONSTRAINT IF EXISTS fk_asignacion_competencia1;
ALTER TABLE IF EXISTS ONLY public.asignacion DROP CONSTRAINT IF EXISTS fk_asignacion_ambiente1;
ALTER TABLE IF EXISTS ONLY public.ambiente DROP CONSTRAINT IF EXISTS fk_ambiente_sede1;
DROP TRIGGER IF EXISTS trg_asignacion_audit ON public.asignacion;
ALTER TABLE IF EXISTS ONLY public.usuario_coordinador DROP CONSTRAINT IF EXISTS usuario_coordinador_pkey;
ALTER TABLE IF EXISTS ONLY public.titulo_programa DROP CONSTRAINT IF EXISTS titulo_programa_pkey;
ALTER TABLE IF EXISTS ONLY public.sede DROP CONSTRAINT IF EXISTS sede_pkey;
ALTER TABLE IF EXISTS ONLY public.programa DROP CONSTRAINT IF EXISTS programa_pkey;
ALTER TABLE IF EXISTS ONLY public.instructor DROP CONSTRAINT IF EXISTS instructor_pkey;
ALTER TABLE IF EXISTS ONLY public.instru_competencia DROP CONSTRAINT IF EXISTS instru_competencia_pkey;
ALTER TABLE IF EXISTS ONLY public.ficha DROP CONSTRAINT IF EXISTS ficha_pkey;
ALTER TABLE IF EXISTS ONLY public.detallexasignacion DROP CONSTRAINT IF EXISTS detallexasignacion_pkey;
ALTER TABLE IF EXISTS ONLY public.coordinacion DROP CONSTRAINT IF EXISTS coordinacion_pkey;
ALTER TABLE IF EXISTS ONLY public.competxprograma DROP CONSTRAINT IF EXISTS competxprograma_pkey;
ALTER TABLE IF EXISTS ONLY public.competencia DROP CONSTRAINT IF EXISTS competencia_pkey;
ALTER TABLE IF EXISTS ONLY public.centro_formacion DROP CONSTRAINT IF EXISTS centro_formacion_pkey;
ALTER TABLE IF EXISTS ONLY public.auditoria_asignacion DROP CONSTRAINT IF EXISTS auditoria_asignacion_pkey;
ALTER TABLE IF EXISTS ONLY public.asignacion DROP CONSTRAINT IF EXISTS asignacion_pkey;
ALTER TABLE IF EXISTS ONLY public.ambiente DROP CONSTRAINT IF EXISTS ambiente_pkey;
ALTER TABLE IF EXISTS public.instru_competencia ALTER COLUMN inscomp_id DROP DEFAULT;
ALTER TABLE IF EXISTS public.detallexasignacion ALTER COLUMN detasig_id DROP DEFAULT;
ALTER TABLE IF EXISTS public.coordinacion ALTER COLUMN coord_id DROP DEFAULT;
ALTER TABLE IF EXISTS public.auditoria_asignacion ALTER COLUMN id_auditoria DROP DEFAULT;
ALTER TABLE IF EXISTS public.asignacion ALTER COLUMN asig_id DROP DEFAULT;
DROP TABLE IF EXISTS public.usuario_coordinador;
DROP TABLE IF EXISTS public.titulo_programa;
DROP TABLE IF EXISTS public.sede;
DROP TABLE IF EXISTS public.programa;
DROP TABLE IF EXISTS public.instructor;
DROP SEQUENCE IF EXISTS public.instru_competencia_inscomp_id_seq;
DROP TABLE IF EXISTS public.instru_competencia;
DROP TABLE IF EXISTS public.ficha;
DROP SEQUENCE IF EXISTS public.detallexasignacion_detasig_id_seq;
DROP TABLE IF EXISTS public.detallexasignacion;
DROP SEQUENCE IF EXISTS public.coordinacion_coord_id_seq;
DROP TABLE IF EXISTS public.coordinacion;
DROP TABLE IF EXISTS public.competxprograma;
DROP TABLE IF EXISTS public.competencia;
DROP TABLE IF EXISTS public.centro_formacion;
DROP SEQUENCE IF EXISTS public.auditoria_asignacion_id_auditoria_seq;
DROP TABLE IF EXISTS public.auditoria_asignacion;
DROP SEQUENCE IF EXISTS public.asignacion_asig_id_seq;
DROP TABLE IF EXISTS public.asignacion;
DROP TABLE IF EXISTS public.ambiente;
DROP FUNCTION IF EXISTS public.func_auditoria_asignacion();
DROP EXTENSION IF EXISTS pgcrypto;
--
-- TOC entry 2 (class 3079 OID 17904)
-- Name: pgcrypto; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS pgcrypto WITH SCHEMA public;


--
-- TOC entry 5191 (class 0 OID 0)
-- Dependencies: 2
-- Name: EXTENSION pgcrypto; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION pgcrypto IS 'cryptographic functions';


--
-- TOC entry 277 (class 1255 OID 17942)
-- Name: func_auditoria_asignacion(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.func_auditoria_asignacion() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_documento BIGINT;
    v_correo VARCHAR;
    v_nombre VARCHAR;
BEGIN
    BEGIN
        v_documento := current_setting('myapp.documento_usuario', true)::BIGINT;
    EXCEPTION WHEN OTHERS THEN
        v_documento := 0;
    END;

    BEGIN
        v_correo := current_setting('myapp.correo_usuario', true);
    EXCEPTION WHEN OTHERS THEN
        v_correo := 'Sistema';
    END;

    BEGIN
        v_nombre := current_setting('myapp.nombre_usuario', true);
    EXCEPTION WHEN OTHERS THEN
        v_nombre := 'Sistema';
    END;

    IF v_documento IS NULL THEN v_documento := 0; END IF;
    IF v_correo IS NULL OR v_correo = '' THEN v_correo := 'Sistema'; END IF;
    IF v_nombre IS NULL OR v_nombre = '' THEN v_nombre := 'Sistema'; END IF;

    IF TG_OP = 'INSERT' THEN
        INSERT INTO auditoria_asignacion (
            instructor_inst_id, asig_fecha_ini, asig_fecha_fin, ficha_fich_id, ambiente_amb_id, competencia_comp_id, asig_id,
            documento_usuario_accion, correo_usuario, nombre_usuario_accion, tipo_accion
        ) VALUES (
            NEW.instructor_inst_id, NEW.asig_fecha_ini, NEW.asig_fecha_fin, NEW.ficha_fich_id, NEW.ambiente_amb_id, NEW.competencia_comp_id, NEW.asig_id,
            v_documento, v_correo, v_nombre, 'INSERT'
        );
        RETURN NEW;
    ELSIF TG_OP = 'UPDATE' THEN
        INSERT INTO auditoria_asignacion (
            instructor_inst_id, asig_fecha_ini, asig_fecha_fin, ficha_fich_id, ambiente_amb_id, competencia_comp_id, asig_id,
            documento_usuario_accion, correo_usuario, nombre_usuario_accion, tipo_accion
        ) VALUES (
            NEW.instructor_inst_id, NEW.asig_fecha_ini, NEW.asig_fecha_fin, NEW.ficha_fich_id, NEW.ambiente_amb_id, NEW.competencia_comp_id, NEW.asig_id,
            v_documento, v_correo, v_nombre, 'UPDATE'
        );
        RETURN NEW;
    ELSIF TG_OP = 'DELETE' THEN
        INSERT INTO auditoria_asignacion (
            instructor_inst_id, asig_fecha_ini, asig_fecha_fin, ficha_fich_id, ambiente_amb_id, competencia_comp_id, asig_id,
            documento_usuario_accion, correo_usuario, nombre_usuario_accion, tipo_accion
        ) VALUES (
            OLD.instructor_inst_id, OLD.asig_fecha_ini, OLD.asig_fecha_fin, OLD.ficha_fich_id, OLD.ambiente_amb_id, OLD.competencia_comp_id, OLD.asig_id,
            v_documento, v_correo, v_nombre, 'DELETE'
        );
        RETURN OLD;
    END IF;
    RETURN NULL;
END;
$$;


ALTER FUNCTION public.func_auditoria_asignacion() OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 220 (class 1259 OID 17943)
-- Name: ambiente; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.ambiente (
    amb_id character varying(5) NOT NULL,
    amb_nombre character varying(45) DEFAULT NULL::character varying,
    tipo_ambiente character varying(50) DEFAULT 'Convencional'::character varying NOT NULL,
    sede_sede_id integer NOT NULL
);


ALTER TABLE public.ambiente OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 17951)
-- Name: asignacion; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.asignacion (
    asig_id integer NOT NULL,
    instructor_inst_id bigint NOT NULL,
    asig_fecha_ini date NOT NULL,
    asig_fecha_fin date NOT NULL,
    ficha_fich_id integer NOT NULL,
    ambiente_amb_id character varying(5) NOT NULL,
    competencia_comp_id integer NOT NULL
);


ALTER TABLE public.asignacion OWNER TO postgres;

--
-- TOC entry 222 (class 1259 OID 17961)
-- Name: asignacion_asig_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.asignacion_asig_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.asignacion_asig_id_seq OWNER TO postgres;

--
-- TOC entry 5192 (class 0 OID 0)
-- Dependencies: 222
-- Name: asignacion_asig_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.asignacion_asig_id_seq OWNED BY public.asignacion.asig_id;


--
-- TOC entry 223 (class 1259 OID 17962)
-- Name: auditoria_asignacion; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.auditoria_asignacion (
    id_auditoria integer NOT NULL,
    instructor_inst_id bigint NOT NULL,
    asig_fecha_ini date NOT NULL,
    asig_fecha_fin date NOT NULL,
    ficha_fich_id integer NOT NULL,
    ambiente_amb_id character varying(5) NOT NULL,
    competencia_comp_id integer NOT NULL,
    asig_id integer NOT NULL,
    fecha_hora timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    documento_usuario_accion bigint NOT NULL,
    correo_usuario character varying(45) NOT NULL,
    tipo_accion character varying(10) NOT NULL,
    nombre_usuario_accion character varying(100)
);


ALTER TABLE public.auditoria_asignacion OWNER TO postgres;

--
-- TOC entry 224 (class 1259 OID 17977)
-- Name: auditoria_asignacion_id_auditoria_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.auditoria_asignacion_id_auditoria_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.auditoria_asignacion_id_auditoria_seq OWNER TO postgres;

--
-- TOC entry 5193 (class 0 OID 0)
-- Dependencies: 224
-- Name: auditoria_asignacion_id_auditoria_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.auditoria_asignacion_id_auditoria_seq OWNED BY public.auditoria_asignacion.id_auditoria;


--
-- TOC entry 225 (class 1259 OID 17978)
-- Name: centro_formacion; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.centro_formacion (
    cent_id integer NOT NULL,
    cent_nombre character varying(100) NOT NULL,
    cent_correo character varying(45),
    cent_password character varying(150)
);


ALTER TABLE public.centro_formacion OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 17983)
-- Name: competencia; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.competencia (
    comp_id integer NOT NULL,
    comp_nombre_corto character varying(30) NOT NULL,
    comp_horas integer NOT NULL,
    comp_nombre_unidad_competencia character varying(150) NOT NULL,
    centro_formacion_cent_id integer
);


ALTER TABLE public.competencia OWNER TO postgres;

--
-- TOC entry 227 (class 1259 OID 17990)
-- Name: competxprograma; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.competxprograma (
    programa_prog_id integer NOT NULL,
    competencia_comp_id integer NOT NULL
);


ALTER TABLE public.competxprograma OWNER TO postgres;

--
-- TOC entry 228 (class 1259 OID 17995)
-- Name: coordinacion; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.coordinacion (
    coord_descripcion character varying(45) NOT NULL,
    centro_formacion_cent_id integer NOT NULL,
    coord_id integer NOT NULL,
    estado smallint DEFAULT 1 NOT NULL,
    coordinador_actual bigint
);


ALTER TABLE public.coordinacion OWNER TO postgres;

--
-- TOC entry 229 (class 1259 OID 18003)
-- Name: coordinacion_coord_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.coordinacion_coord_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.coordinacion_coord_id_seq OWNER TO postgres;

--
-- TOC entry 5194 (class 0 OID 0)
-- Dependencies: 229
-- Name: coordinacion_coord_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.coordinacion_coord_id_seq OWNED BY public.coordinacion.coord_id;


--
-- TOC entry 230 (class 1259 OID 18004)
-- Name: detallexasignacion; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.detallexasignacion (
    detasig_id integer NOT NULL,
    asignacion_asig_id integer NOT NULL,
    detasig_hora_ini time without time zone NOT NULL,
    detasig_hora_fin time without time zone NOT NULL,
    detasig_fecha date DEFAULT CURRENT_DATE NOT NULL
);


ALTER TABLE public.detallexasignacion OWNER TO postgres;

--
-- TOC entry 231 (class 1259 OID 18013)
-- Name: detallexasignacion_detasig_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.detallexasignacion_detasig_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.detallexasignacion_detasig_id_seq OWNER TO postgres;

--
-- TOC entry 5195 (class 0 OID 0)
-- Dependencies: 231
-- Name: detallexasignacion_detasig_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.detallexasignacion_detasig_id_seq OWNED BY public.detallexasignacion.detasig_id;


--
-- TOC entry 232 (class 1259 OID 18014)
-- Name: ficha; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.ficha (
    fich_id integer NOT NULL,
    programa_prog_id integer NOT NULL,
    instructor_inst_id_lider bigint NOT NULL,
    fich_jornada character varying(20) NOT NULL,
    coordinacion_coord_id integer NOT NULL,
    fich_fecha_ini_lectiva date NOT NULL,
    fich_fecha_fin_lectiva date NOT NULL
);


ALTER TABLE public.ficha OWNER TO postgres;

--
-- TOC entry 233 (class 1259 OID 18024)
-- Name: instru_competencia; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.instru_competencia (
    inscomp_id integer NOT NULL,
    instructor_inst_id bigint NOT NULL,
    competxprograma_programa_prog_id integer NOT NULL,
    competxprograma_competencia_comp_id integer NOT NULL,
    inscomp_vigencia date NOT NULL
);


ALTER TABLE public.instru_competencia OWNER TO postgres;

--
-- TOC entry 234 (class 1259 OID 18032)
-- Name: instru_competencia_inscomp_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.instru_competencia_inscomp_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.instru_competencia_inscomp_id_seq OWNER TO postgres;

--
-- TOC entry 5196 (class 0 OID 0)
-- Dependencies: 234
-- Name: instru_competencia_inscomp_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.instru_competencia_inscomp_id_seq OWNED BY public.instru_competencia.inscomp_id;


--
-- TOC entry 235 (class 1259 OID 18033)
-- Name: instructor; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.instructor (
    inst_nombres character varying(45) NOT NULL,
    inst_apellidos character varying(45) NOT NULL,
    inst_correo character varying(45) NOT NULL,
    inst_telefono bigint NOT NULL,
    centro_formacion_cent_id integer NOT NULL,
    inst_password character varying(150) NOT NULL,
    numero_documento bigint NOT NULL,
    estado smallint DEFAULT 1 NOT NULL
);


ALTER TABLE public.instructor OWNER TO postgres;

--
-- TOC entry 236 (class 1259 OID 18045)
-- Name: programa; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.programa (
    prog_codigo integer NOT NULL,
    prog_denominacion character varying(100) NOT NULL,
    tit_programa_titpro_id integer NOT NULL,
    prog_tipo character varying(30) NOT NULL,
    centro_formacion_cent_id integer
);


ALTER TABLE public.programa OWNER TO postgres;

--
-- TOC entry 237 (class 1259 OID 18052)
-- Name: sede; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sede (
    sede_id integer NOT NULL,
    sede_nombre character varying(100) NOT NULL,
    centro_formacion_cent_id integer
);


ALTER TABLE public.sede OWNER TO postgres;

--
-- TOC entry 238 (class 1259 OID 18057)
-- Name: titulo_programa; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.titulo_programa (
    titpro_id integer NOT NULL,
    titpro_nombre character varying(150) NOT NULL,
    centro_formacion_cent_id integer
);


ALTER TABLE public.titulo_programa OWNER TO postgres;

--
-- TOC entry 239 (class 1259 OID 18062)
-- Name: usuario_coordinador; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.usuario_coordinador (
    numero_documento bigint NOT NULL,
    coord_nombre_coordinador character varying(100) NOT NULL,
    coord_correo character varying(60) NOT NULL,
    coord_password character varying(150) NOT NULL,
    estado smallint DEFAULT 1 NOT NULL,
    centro_formacion_id integer
);


ALTER TABLE public.usuario_coordinador OWNER TO postgres;

--
-- TOC entry 4957 (class 2604 OID 18071)
-- Name: asignacion asig_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asignacion ALTER COLUMN asig_id SET DEFAULT nextval('public.asignacion_asig_id_seq'::regclass);


--
-- TOC entry 4958 (class 2604 OID 18072)
-- Name: auditoria_asignacion id_auditoria; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.auditoria_asignacion ALTER COLUMN id_auditoria SET DEFAULT nextval('public.auditoria_asignacion_id_auditoria_seq'::regclass);


--
-- TOC entry 4960 (class 2604 OID 18073)
-- Name: coordinacion coord_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.coordinacion ALTER COLUMN coord_id SET DEFAULT nextval('public.coordinacion_coord_id_seq'::regclass);


--
-- TOC entry 4962 (class 2604 OID 18074)
-- Name: detallexasignacion detasig_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detallexasignacion ALTER COLUMN detasig_id SET DEFAULT nextval('public.detallexasignacion_detasig_id_seq'::regclass);


--
-- TOC entry 4964 (class 2604 OID 18075)
-- Name: instru_competencia inscomp_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.instru_competencia ALTER COLUMN inscomp_id SET DEFAULT nextval('public.instru_competencia_inscomp_id_seq'::regclass);


--
-- TOC entry 5166 (class 0 OID 17943)
-- Dependencies: 220
-- Data for Name: ambiente; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.ambiente VALUES ('201', 'ADSO', 'Especializado', 7);
INSERT INTO public.ambiente VALUES ('AMB01', 'Ambiente Prueba', 'Convencional', 1);


--
-- TOC entry 5167 (class 0 OID 17951)
-- Dependencies: 221
-- Data for Name: asignacion; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.asignacion VALUES (20, 5678657856856, '2026-03-04', '2026-03-10', 3115418, '201', 1);
INSERT INTO public.asignacion VALUES (26, 76758463, '2026-03-16', '2026-04-10', 3225818, '201', 2);
INSERT INTO public.asignacion VALUES (28, 76758463, '2026-04-13', '2026-05-08', 3115418, '201', 2);
INSERT INTO public.asignacion VALUES (30, 76758463, '2026-05-11', '2026-06-05', 3218713, '201', 2);
INSERT INTO public.asignacion VALUES (31, 76758463, '2026-06-08', '2026-07-03', 3218713, '201', 1);
INSERT INTO public.asignacion VALUES (33, 76758463, '2026-07-20', '2026-08-10', 3225818, '201', 1);
INSERT INTO public.asignacion VALUES (34, 1093189418, '2026-04-27', '2026-05-08', 3115418, '201', 3);
INSERT INTO public.asignacion VALUES (35, 1093189418, '2026-04-27', '2026-05-08', 3115418, '201', 4);


--
-- TOC entry 5169 (class 0 OID 17962)
-- Dependencies: 223
-- Data for Name: auditoria_asignacion; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.auditoria_asignacion VALUES (1, 76758463, '2026-03-02', '2026-04-02', 3115418, '201', 2, 16, '2026-03-01 22:55:12.529625', 0, 'Sistema', 'INSERT', NULL);
INSERT INTO public.auditoria_asignacion VALUES (2, 76758463, '2026-03-02', '2026-04-02', 3115418, '201', 2, 16, '2026-03-03 22:30:41.557175', 0, 'Sistema', 'DELETE', 'Sistema');
INSERT INTO public.auditoria_asignacion VALUES (5, 76758463, '2026-03-04', '2026-03-13', 3115418, '201', 2, 19, '2026-03-03 23:54:53.040976', 234567, 'petrosky@gmail.com', 'INSERT', 'Gustavo Petro');
INSERT INTO public.auditoria_asignacion VALUES (6, 5678657856856, '2026-03-04', '2026-03-10', 3115418, '201', 1, 20, '2026-03-04 00:14:20.071888', 234567, 'petrosky@gmail.com', 'INSERT', 'Gustavo Petro');
INSERT INTO public.auditoria_asignacion VALUES (7, 76758463, '2026-03-04', '2026-03-13', 3115418, '201', 2, 19, '2026-03-04 00:24:13.848942', 234567, 'petrosky@gmail.com', 'DELETE', 'Gustavo Petro');
INSERT INTO public.auditoria_asignacion VALUES (11, 76758463, '2026-03-16', '2026-04-10', 3225818, '201', 1, 24, '2026-03-12 08:43:15.014667', 234567, 'petrosky@gmail.com', 'INSERT', 'Gustavo Petro');
INSERT INTO public.auditoria_asignacion VALUES (12, 76758463, '2026-03-16', '2026-04-10', 3225818, '201', 1, 24, '2026-03-12 08:43:48.511236', 234567, 'petrosky@gmail.com', 'DELETE', 'Gustavo Petro');
INSERT INTO public.auditoria_asignacion VALUES (13, 76758463, '2026-03-16', '2026-04-10', 3225818, '201', 2, 25, '2026-03-12 08:45:02.363978', 234567, 'petrosky@gmail.com', 'INSERT', 'Gustavo Petro');
INSERT INTO public.auditoria_asignacion VALUES (14, 76758463, '2026-03-16', '2026-04-10', 3225818, '201', 2, 25, '2026-03-12 08:45:20.024223', 234567, 'petrosky@gmail.com', 'DELETE', 'Gustavo Petro');
INSERT INTO public.auditoria_asignacion VALUES (15, 76758463, '2026-03-16', '2026-04-10', 3225818, '201', 2, 26, '2026-03-12 08:46:10.46371', 234567, 'petrosky@gmail.com', 'INSERT', 'Gustavo Petro');
INSERT INTO public.auditoria_asignacion VALUES (17, 76758463, '2026-04-13', '2026-05-08', 3115418, '201', 2, 28, '2026-03-12 08:48:42.498985', 234567, 'petrosky@gmail.com', 'INSERT', 'Gustavo Petro');
INSERT INTO public.auditoria_asignacion VALUES (19, 76758463, '2026-05-11', '2026-06-05', 3218713, '201', 2, 30, '2026-03-12 10:49:00.518262', 234567, 'petrosky@gmail.com', 'INSERT', 'Gustavo Petro');
INSERT INTO public.auditoria_asignacion VALUES (20, 76758463, '2026-06-08', '2026-07-03', 3218713, '201', 1, 31, '2026-03-12 10:52:12.959872', 234567, 'petrosky@gmail.com', 'INSERT', 'Gustavo Petro');
INSERT INTO public.auditoria_asignacion VALUES (22, 76758463, '2026-07-20', '2026-08-10', 3225818, '201', 1, 33, '2026-03-13 07:51:36.205707', 234567, 'petrosky@gmail.com', 'INSERT', 'Gustavo Petro');
INSERT INTO public.auditoria_asignacion VALUES (23, 1093189418, '2026-04-27', '2026-05-08', 3115418, '201', 3, 34, '2026-03-13 11:21:12.582621', 234567, 'petrosky@gmail.com', 'INSERT', 'Gustavo Petro');
INSERT INTO public.auditoria_asignacion VALUES (24, 1093189418, '2026-04-27', '2026-05-08', 3115418, '201', 4, 35, '2026-03-13 11:28:52.376295', 234567, 'petrosky@gmail.com', 'INSERT', 'Gustavo Petro');


--
-- TOC entry 5171 (class 0 OID 17978)
-- Dependencies: 225
-- Data for Name: centro_formacion; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.centro_formacion VALUES (1, 'CIES', 'ciessena@gmail.com', '$2a$06$WEPuDhY/sBsC/xJL29.a8uUtO5wAUkaeMY5laLeXDmtqwAUvs7JuW');
INSERT INTO public.centro_formacion VALUES (2, 'CEDRUM', 'cedrumsena@gmail.com', '$2a$06$8yNElBr8t38XH049YZ91WeMHmmXTBt6Wc6yYMnoutN44foyfFoOza');


--
-- TOC entry 5172 (class 0 OID 17983)
-- Dependencies: 226
-- Data for Name: competencia; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.competencia VALUES (1, 'Inglés', 48, 'Interactuar con otros en idioma extranjero según estipulaciones del Marco Común Europeo de Referencia para Idiomas', 1);
INSERT INTO public.competencia VALUES (2, 'Matemáticas', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales y sociales.', 1);
INSERT INTO public.competencia VALUES (3, 'Etica', 48, 'Ejercer derechos fundamentales del trabajo y comportamientos éticos.', 1);
INSERT INTO public.competencia VALUES (4, 'investigación', 48, 'metodologia para la investigación', 1);


--
-- TOC entry 5173 (class 0 OID 17990)
-- Dependencies: 227
-- Data for Name: competxprograma; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.competxprograma VALUES (223345, 1);
INSERT INTO public.competxprograma VALUES (223345, 2);
INSERT INTO public.competxprograma VALUES (54654, 1);
INSERT INTO public.competxprograma VALUES (54654, 2);
INSERT INTO public.competxprograma VALUES (223345, 3);
INSERT INTO public.competxprograma VALUES (54654, 3);
INSERT INTO public.competxprograma VALUES (2349012, 3);
INSERT INTO public.competxprograma VALUES (223345, 4);
INSERT INTO public.competxprograma VALUES (54654, 4);
INSERT INTO public.competxprograma VALUES (2349012, 4);


--
-- TOC entry 5174 (class 0 OID 17995)
-- Dependencies: 228
-- Data for Name: coordinacion; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.coordinacion VALUES ('Industria y Comercio', 1, 3, 1, 2345678643);
INSERT INTO public.coordinacion VALUES ('TIC', 1, 2, 1, 1234);


--
-- TOC entry 5176 (class 0 OID 18004)
-- Dependencies: 230
-- Data for Name: detallexasignacion; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.detallexasignacion VALUES (13, 20, '08:00:00', '12:00:00', '2026-03-05');
INSERT INTO public.detallexasignacion VALUES (15, 20, '08:00:00', '12:00:00', '2026-03-09');
INSERT INTO public.detallexasignacion VALUES (16, 20, '08:00:00', '12:00:00', '2026-03-10');
INSERT INTO public.detallexasignacion VALUES (14, 20, '06:00:00', '09:00:00', '2026-03-06');
INSERT INTO public.detallexasignacion VALUES (12, 20, '08:00:00', '12:00:00', '2026-03-04');
INSERT INTO public.detallexasignacion VALUES (66, 26, '08:00:00', '12:00:00', '2026-03-16');
INSERT INTO public.detallexasignacion VALUES (67, 26, '08:00:00', '12:00:00', '2026-03-17');
INSERT INTO public.detallexasignacion VALUES (68, 26, '08:00:00', '12:00:00', '2026-03-18');
INSERT INTO public.detallexasignacion VALUES (69, 26, '08:00:00', '12:00:00', '2026-03-19');
INSERT INTO public.detallexasignacion VALUES (70, 26, '08:00:00', '12:00:00', '2026-03-20');
INSERT INTO public.detallexasignacion VALUES (71, 26, '08:00:00', '12:00:00', '2026-03-23');
INSERT INTO public.detallexasignacion VALUES (72, 26, '08:00:00', '12:00:00', '2026-03-24');
INSERT INTO public.detallexasignacion VALUES (73, 26, '08:00:00', '12:00:00', '2026-03-25');
INSERT INTO public.detallexasignacion VALUES (74, 26, '08:00:00', '12:00:00', '2026-03-26');
INSERT INTO public.detallexasignacion VALUES (75, 26, '08:00:00', '12:00:00', '2026-03-27');
INSERT INTO public.detallexasignacion VALUES (76, 26, '08:00:00', '12:00:00', '2026-03-30');
INSERT INTO public.detallexasignacion VALUES (77, 26, '08:00:00', '12:00:00', '2026-03-31');
INSERT INTO public.detallexasignacion VALUES (78, 26, '08:00:00', '12:00:00', '2026-04-01');
INSERT INTO public.detallexasignacion VALUES (79, 26, '08:00:00', '12:00:00', '2026-04-02');
INSERT INTO public.detallexasignacion VALUES (80, 26, '08:00:00', '12:00:00', '2026-04-03');
INSERT INTO public.detallexasignacion VALUES (81, 26, '08:00:00', '12:00:00', '2026-04-06');
INSERT INTO public.detallexasignacion VALUES (82, 26, '08:00:00', '12:00:00', '2026-04-07');
INSERT INTO public.detallexasignacion VALUES (83, 26, '08:00:00', '12:00:00', '2026-04-08');
INSERT INTO public.detallexasignacion VALUES (84, 26, '08:00:00', '12:00:00', '2026-04-09');
INSERT INTO public.detallexasignacion VALUES (85, 26, '08:00:00', '12:00:00', '2026-04-10');
INSERT INTO public.detallexasignacion VALUES (86, 28, '08:00:00', '12:00:00', '2026-04-13');
INSERT INTO public.detallexasignacion VALUES (87, 28, '08:00:00', '12:00:00', '2026-04-14');
INSERT INTO public.detallexasignacion VALUES (88, 28, '08:00:00', '12:00:00', '2026-04-15');
INSERT INTO public.detallexasignacion VALUES (89, 28, '08:00:00', '12:00:00', '2026-04-16');
INSERT INTO public.detallexasignacion VALUES (90, 28, '08:00:00', '12:00:00', '2026-04-17');
INSERT INTO public.detallexasignacion VALUES (91, 28, '08:00:00', '12:00:00', '2026-04-20');
INSERT INTO public.detallexasignacion VALUES (92, 28, '08:00:00', '12:00:00', '2026-04-21');
INSERT INTO public.detallexasignacion VALUES (93, 28, '08:00:00', '12:00:00', '2026-04-22');
INSERT INTO public.detallexasignacion VALUES (94, 28, '08:00:00', '12:00:00', '2026-04-23');
INSERT INTO public.detallexasignacion VALUES (95, 28, '08:00:00', '12:00:00', '2026-04-24');
INSERT INTO public.detallexasignacion VALUES (96, 28, '08:00:00', '12:00:00', '2026-04-27');
INSERT INTO public.detallexasignacion VALUES (97, 28, '08:00:00', '12:00:00', '2026-04-28');
INSERT INTO public.detallexasignacion VALUES (98, 28, '08:00:00', '12:00:00', '2026-04-29');
INSERT INTO public.detallexasignacion VALUES (99, 28, '08:00:00', '12:00:00', '2026-04-30');
INSERT INTO public.detallexasignacion VALUES (100, 28, '08:00:00', '12:00:00', '2026-05-01');
INSERT INTO public.detallexasignacion VALUES (101, 28, '08:00:00', '12:00:00', '2026-05-04');
INSERT INTO public.detallexasignacion VALUES (102, 28, '08:00:00', '12:00:00', '2026-05-05');
INSERT INTO public.detallexasignacion VALUES (103, 28, '08:00:00', '12:00:00', '2026-05-06');
INSERT INTO public.detallexasignacion VALUES (104, 28, '08:00:00', '12:00:00', '2026-05-07');
INSERT INTO public.detallexasignacion VALUES (105, 28, '08:00:00', '12:00:00', '2026-05-08');
INSERT INTO public.detallexasignacion VALUES (106, 30, '08:00:00', '12:00:00', '2026-05-11');
INSERT INTO public.detallexasignacion VALUES (107, 30, '08:00:00', '12:00:00', '2026-05-12');
INSERT INTO public.detallexasignacion VALUES (108, 30, '08:00:00', '12:00:00', '2026-05-13');
INSERT INTO public.detallexasignacion VALUES (109, 30, '08:00:00', '12:00:00', '2026-05-14');
INSERT INTO public.detallexasignacion VALUES (110, 30, '08:00:00', '12:00:00', '2026-05-15');
INSERT INTO public.detallexasignacion VALUES (111, 30, '08:00:00', '12:00:00', '2026-05-18');
INSERT INTO public.detallexasignacion VALUES (112, 30, '08:00:00', '12:00:00', '2026-05-19');
INSERT INTO public.detallexasignacion VALUES (113, 30, '08:00:00', '12:00:00', '2026-05-20');
INSERT INTO public.detallexasignacion VALUES (114, 30, '08:00:00', '12:00:00', '2026-05-21');
INSERT INTO public.detallexasignacion VALUES (115, 30, '08:00:00', '12:00:00', '2026-05-22');
INSERT INTO public.detallexasignacion VALUES (116, 30, '08:00:00', '12:00:00', '2026-05-25');
INSERT INTO public.detallexasignacion VALUES (117, 30, '08:00:00', '12:00:00', '2026-05-26');
INSERT INTO public.detallexasignacion VALUES (118, 30, '08:00:00', '12:00:00', '2026-05-27');
INSERT INTO public.detallexasignacion VALUES (119, 30, '08:00:00', '12:00:00', '2026-05-28');
INSERT INTO public.detallexasignacion VALUES (120, 30, '08:00:00', '12:00:00', '2026-05-29');
INSERT INTO public.detallexasignacion VALUES (121, 30, '08:00:00', '12:00:00', '2026-06-01');
INSERT INTO public.detallexasignacion VALUES (122, 30, '08:00:00', '12:00:00', '2026-06-02');
INSERT INTO public.detallexasignacion VALUES (123, 30, '08:00:00', '12:00:00', '2026-06-03');
INSERT INTO public.detallexasignacion VALUES (124, 30, '08:00:00', '12:00:00', '2026-06-04');
INSERT INTO public.detallexasignacion VALUES (125, 30, '08:00:00', '12:00:00', '2026-06-05');
INSERT INTO public.detallexasignacion VALUES (126, 31, '08:00:00', '12:00:00', '2026-06-08');
INSERT INTO public.detallexasignacion VALUES (127, 31, '08:00:00', '12:00:00', '2026-06-09');
INSERT INTO public.detallexasignacion VALUES (128, 31, '08:00:00', '12:00:00', '2026-06-10');
INSERT INTO public.detallexasignacion VALUES (129, 31, '08:00:00', '12:00:00', '2026-06-11');
INSERT INTO public.detallexasignacion VALUES (130, 31, '08:00:00', '12:00:00', '2026-06-12');
INSERT INTO public.detallexasignacion VALUES (131, 31, '08:00:00', '12:00:00', '2026-06-15');
INSERT INTO public.detallexasignacion VALUES (132, 31, '08:00:00', '12:00:00', '2026-06-16');
INSERT INTO public.detallexasignacion VALUES (133, 31, '08:00:00', '12:00:00', '2026-06-17');
INSERT INTO public.detallexasignacion VALUES (134, 31, '08:00:00', '12:00:00', '2026-06-18');
INSERT INTO public.detallexasignacion VALUES (135, 31, '08:00:00', '12:00:00', '2026-06-19');
INSERT INTO public.detallexasignacion VALUES (136, 31, '08:00:00', '12:00:00', '2026-06-22');
INSERT INTO public.detallexasignacion VALUES (137, 31, '08:00:00', '12:00:00', '2026-06-23');
INSERT INTO public.detallexasignacion VALUES (138, 31, '08:00:00', '12:00:00', '2026-06-24');
INSERT INTO public.detallexasignacion VALUES (139, 31, '08:00:00', '12:00:00', '2026-06-25');
INSERT INTO public.detallexasignacion VALUES (140, 31, '08:00:00', '12:00:00', '2026-06-26');
INSERT INTO public.detallexasignacion VALUES (141, 31, '08:00:00', '12:00:00', '2026-06-29');
INSERT INTO public.detallexasignacion VALUES (142, 31, '08:00:00', '12:00:00', '2026-06-30');
INSERT INTO public.detallexasignacion VALUES (143, 31, '08:00:00', '12:00:00', '2026-07-01');
INSERT INTO public.detallexasignacion VALUES (144, 31, '08:00:00', '12:00:00', '2026-07-02');
INSERT INTO public.detallexasignacion VALUES (145, 31, '08:00:00', '12:00:00', '2026-07-03');
INSERT INTO public.detallexasignacion VALUES (146, 33, '18:00:00', '22:00:00', '2026-07-20');
INSERT INTO public.detallexasignacion VALUES (147, 33, '18:00:00', '22:00:00', '2026-07-22');
INSERT INTO public.detallexasignacion VALUES (148, 33, '18:00:00', '22:00:00', '2026-07-23');
INSERT INTO public.detallexasignacion VALUES (149, 33, '18:00:00', '22:00:00', '2026-07-24');
INSERT INTO public.detallexasignacion VALUES (150, 33, '18:00:00', '22:00:00', '2026-07-25');
INSERT INTO public.detallexasignacion VALUES (151, 33, '18:00:00', '22:00:00', '2026-07-26');
INSERT INTO public.detallexasignacion VALUES (152, 33, '18:00:00', '22:00:00', '2026-07-27');
INSERT INTO public.detallexasignacion VALUES (153, 33, '18:00:00', '22:00:00', '2026-07-28');
INSERT INTO public.detallexasignacion VALUES (154, 33, '18:00:00', '22:00:00', '2026-07-29');
INSERT INTO public.detallexasignacion VALUES (155, 33, '18:00:00', '22:00:00', '2026-08-01');
INSERT INTO public.detallexasignacion VALUES (156, 33, '18:00:00', '22:00:00', '2026-08-02');
INSERT INTO public.detallexasignacion VALUES (157, 33, '18:00:00', '22:00:00', '2026-08-03');
INSERT INTO public.detallexasignacion VALUES (158, 33, '18:00:00', '22:00:00', '2026-08-04');
INSERT INTO public.detallexasignacion VALUES (159, 33, '18:00:00', '22:00:00', '2026-08-05');
INSERT INTO public.detallexasignacion VALUES (160, 33, '16:00:00', '21:00:00', '2026-08-07');
INSERT INTO public.detallexasignacion VALUES (161, 33, '18:00:00', '22:00:00', '2026-08-08');
INSERT INTO public.detallexasignacion VALUES (162, 33, '18:00:00', '22:00:00', '2026-08-10');
INSERT INTO public.detallexasignacion VALUES (163, 34, '06:00:00', '08:00:00', '2026-04-27');
INSERT INTO public.detallexasignacion VALUES (164, 34, '06:00:00', '08:00:00', '2026-04-28');
INSERT INTO public.detallexasignacion VALUES (165, 34, '06:00:00', '08:00:00', '2026-04-29');
INSERT INTO public.detallexasignacion VALUES (166, 34, '06:00:00', '08:00:00', '2026-04-30');
INSERT INTO public.detallexasignacion VALUES (167, 34, '06:00:00', '08:00:00', '2026-05-01');
INSERT INTO public.detallexasignacion VALUES (168, 34, '06:00:00', '08:00:00', '2026-05-04');
INSERT INTO public.detallexasignacion VALUES (169, 34, '06:00:00', '08:00:00', '2026-05-05');
INSERT INTO public.detallexasignacion VALUES (170, 34, '06:00:00', '08:00:00', '2026-05-06');
INSERT INTO public.detallexasignacion VALUES (171, 34, '06:00:00', '08:00:00', '2026-05-07');
INSERT INTO public.detallexasignacion VALUES (172, 34, '06:00:00', '08:00:00', '2026-05-08');
INSERT INTO public.detallexasignacion VALUES (173, 35, '12:00:00', '14:00:00', '2026-04-27');
INSERT INTO public.detallexasignacion VALUES (174, 35, '12:00:00', '14:00:00', '2026-04-28');
INSERT INTO public.detallexasignacion VALUES (175, 35, '12:00:00', '14:00:00', '2026-04-29');
INSERT INTO public.detallexasignacion VALUES (176, 35, '12:00:00', '14:00:00', '2026-04-30');
INSERT INTO public.detallexasignacion VALUES (177, 35, '12:00:00', '14:00:00', '2026-05-01');
INSERT INTO public.detallexasignacion VALUES (178, 35, '12:00:00', '14:00:00', '2026-05-04');
INSERT INTO public.detallexasignacion VALUES (179, 35, '12:00:00', '14:00:00', '2026-05-05');
INSERT INTO public.detallexasignacion VALUES (180, 35, '12:00:00', '14:00:00', '2026-05-06');
INSERT INTO public.detallexasignacion VALUES (181, 35, '12:00:00', '14:00:00', '2026-05-07');
INSERT INTO public.detallexasignacion VALUES (182, 35, '16:00:00', '18:00:00', '2026-05-08');


--
-- TOC entry 5178 (class 0 OID 18014)
-- Dependencies: 232
-- Data for Name: ficha; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.ficha VALUES (3115418, 223345, 1092834765, 'Mañana', 2, '2026-03-01', '2027-12-13');
INSERT INTO public.ficha VALUES (3225818, 54654, 5678657856856, 'Tarde', 2, '2026-02-01', '2027-12-31');
INSERT INTO public.ficha VALUES (3218713, 223345, 1092834765, 'Mixta', 2, '2026-01-04', '2028-02-07');


--
-- TOC entry 5179 (class 0 OID 18024)
-- Dependencies: 233
-- Data for Name: instru_competencia; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.instru_competencia VALUES (1, 1092834765, 223345, 1, '2027-12-31');
INSERT INTO public.instru_competencia VALUES (18, 5678657856856, 223345, 1, '2026-12-31');
INSERT INTO public.instru_competencia VALUES (19, 5678657856856, 54654, 1, '2026-12-31');
INSERT INTO public.instru_competencia VALUES (29, 1093189418, 223345, 3, '2026-12-31');
INSERT INTO public.instru_competencia VALUES (30, 1093189418, 54654, 3, '2026-12-31');
INSERT INTO public.instru_competencia VALUES (31, 1093189418, 2349012, 3, '2026-12-31');
INSERT INTO public.instru_competencia VALUES (32, 1093189418, 223345, 1, '2026-12-31');
INSERT INTO public.instru_competencia VALUES (33, 1093189418, 54654, 1, '2026-12-31');
INSERT INTO public.instru_competencia VALUES (34, 1093189418, 223345, 4, '2026-12-31');
INSERT INTO public.instru_competencia VALUES (35, 1093189418, 54654, 4, '2026-12-31');
INSERT INTO public.instru_competencia VALUES (36, 1093189418, 2349012, 4, '2026-12-31');
INSERT INTO public.instru_competencia VALUES (37, 1093189418, 223345, 2, '2026-12-31');
INSERT INTO public.instru_competencia VALUES (38, 1093189418, 54654, 2, '2026-12-31');
INSERT INTO public.instru_competencia VALUES (39, 76758463, 223345, 1, '2026-12-31');
INSERT INTO public.instru_competencia VALUES (40, 76758463, 54654, 1, '2026-12-31');


--
-- TOC entry 5181 (class 0 OID 18033)
-- Dependencies: 235
-- Data for Name: instructor; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.instructor VALUES ('Cristian', 'Chaustre', 'chaustre@gmail.com', 1234556789, 1, 'Sena123*', 1092834765, 1);
INSERT INTO public.instructor VALUES ('Carlos', 'Pietro', 'pietro@gmail.com', 3239284393, 1, '$2y$10$GNZFjI5SpKSWcOQnLV1nuehKl6Dxq5LTdrikd3dsNnA37Erry8wAu', 5678657856856, 1);
INSERT INTO public.instructor VALUES ('Sergio', 'Rodriguez', 'serrod@gmail.com', 3225906525, 1, '$2y$10$.xD396X9f4RzD6/SiWPR8.wQNFXc5zACGv.cLhB0t.yV2aViKFQGO', 1093189418, 1);
INSERT INTO public.instructor VALUES ('Breyner', 'Pena', 'breygud@gmail.com', 978202439875, 1, '$2y$10$lPuCUhRRSD4x1c.IBT8DLuPfRo6hlCqO9J78v.c0i6OxCyW5XECmG', 76758463, 1);


--
-- TOC entry 5182 (class 0 OID 18045)
-- Dependencies: 236
-- Data for Name: programa; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.programa VALUES (223345, 'Análisis y Desarrollo de Software', 2, 'Tecnólogo', 1);
INSERT INTO public.programa VALUES (54654, 'Gestión contable', 3, 'Tecnólogo', 1);
INSERT INTO public.programa VALUES (2349012, 'Técnico Especialista en Cosmetología', 4, 'Técnico', 1);


--
-- TOC entry 5183 (class 0 OID 18052)
-- Dependencies: 237
-- Data for Name: sede; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.sede VALUES (1, 'Calzado - manufactura, marroquinería y calzado.', 1);
INSERT INTO public.sede VALUES (2, 'Comuneros - moda, confección, maderas y multimedia', 1);
INSERT INTO public.sede VALUES (4, 'Tecnoparque - proyectos de innovación', 1);
INSERT INTO public.sede VALUES (5, 'Villa del Rosario - Comercio, logística y servicios fronterizo', 1);
INSERT INTO public.sede VALUES (6, 'Patios - Atención en servicios y comercio para el área metropolitana', 1);
INSERT INTO public.sede VALUES (3, 'Industria - áreas técnicas, de mantenimiento, mecánicas e industriales', 1);
INSERT INTO public.sede VALUES (8, 'Pescadero - CIES', 1);
INSERT INTO public.sede VALUES (9, 'Sede Principal Cucuta', 1);
INSERT INTO public.sede VALUES (7, 'Biblioteca - servicio a todos los aprendices', 1);


--
-- TOC entry 5184 (class 0 OID 18057)
-- Dependencies: 238
-- Data for Name: titulo_programa; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.titulo_programa VALUES (2, 'Tecnólogo en Análisis y Desarrollo de Software', 1);
INSERT INTO public.titulo_programa VALUES (3, 'Tecnólogo en Gestión Contable', 1);
INSERT INTO public.titulo_programa VALUES (4, ' tecnico en cosmetologia', 1);


--
-- TOC entry 5185 (class 0 OID 18062)
-- Dependencies: 239
-- Data for Name: usuario_coordinador; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.usuario_coordinador VALUES (2345678643, 'Juan Diego Rubio', 'chuni@gmail.com', '$2y$10$VxwL4vZzXNwk5HV6qtVr7OtoQvTe25QxMI/0w7en8IPZ83kqOa74q', 1, 1);
INSERT INTO public.usuario_coordinador VALUES (234567, 'Gustavo Petro', 'petrosky@gmail.com', '$2y$10$6CeswVyXRk2OZqoCzhHWKusAZn.34mXwJF5xz9xKpnjr3YZBC3.dy', 1, 1);
INSERT INTO public.usuario_coordinador VALUES (1234, 'Palomita', 'paloma@gmail.com', '$2y$10$3MiVnegLrKBbit4mvvrULeOJzHvzkRtRbBqPQNL0E1wxmJs2Q2md6', 1, 1);


--
-- TOC entry 5197 (class 0 OID 0)
-- Dependencies: 222
-- Name: asignacion_asig_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.asignacion_asig_id_seq', 35, true);


--
-- TOC entry 5198 (class 0 OID 0)
-- Dependencies: 224
-- Name: auditoria_asignacion_id_auditoria_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.auditoria_asignacion_id_auditoria_seq', 24, true);


--
-- TOC entry 5199 (class 0 OID 0)
-- Dependencies: 229
-- Name: coordinacion_coord_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.coordinacion_coord_id_seq', 3, true);


--
-- TOC entry 5200 (class 0 OID 0)
-- Dependencies: 231
-- Name: detallexasignacion_detasig_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.detallexasignacion_detasig_id_seq', 182, true);


--
-- TOC entry 5201 (class 0 OID 0)
-- Dependencies: 234
-- Name: instru_competencia_inscomp_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.instru_competencia_inscomp_id_seq', 40, true);


--
-- TOC entry 4968 (class 2606 OID 18077)
-- Name: ambiente ambiente_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ambiente
    ADD CONSTRAINT ambiente_pkey PRIMARY KEY (amb_id);


--
-- TOC entry 4970 (class 2606 OID 18079)
-- Name: asignacion asignacion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asignacion
    ADD CONSTRAINT asignacion_pkey PRIMARY KEY (asig_id);


--
-- TOC entry 4972 (class 2606 OID 18081)
-- Name: auditoria_asignacion auditoria_asignacion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.auditoria_asignacion
    ADD CONSTRAINT auditoria_asignacion_pkey PRIMARY KEY (id_auditoria);


--
-- TOC entry 4974 (class 2606 OID 18083)
-- Name: centro_formacion centro_formacion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.centro_formacion
    ADD CONSTRAINT centro_formacion_pkey PRIMARY KEY (cent_id);


--
-- TOC entry 4976 (class 2606 OID 18085)
-- Name: competencia competencia_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.competencia
    ADD CONSTRAINT competencia_pkey PRIMARY KEY (comp_id);


--
-- TOC entry 4978 (class 2606 OID 18087)
-- Name: competxprograma competxprograma_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.competxprograma
    ADD CONSTRAINT competxprograma_pkey PRIMARY KEY (programa_prog_id, competencia_comp_id);


--
-- TOC entry 4980 (class 2606 OID 18089)
-- Name: coordinacion coordinacion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.coordinacion
    ADD CONSTRAINT coordinacion_pkey PRIMARY KEY (coord_id);


--
-- TOC entry 4982 (class 2606 OID 18091)
-- Name: detallexasignacion detallexasignacion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detallexasignacion
    ADD CONSTRAINT detallexasignacion_pkey PRIMARY KEY (detasig_id);


--
-- TOC entry 4984 (class 2606 OID 18093)
-- Name: ficha ficha_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ficha
    ADD CONSTRAINT ficha_pkey PRIMARY KEY (fich_id);


--
-- TOC entry 4986 (class 2606 OID 18095)
-- Name: instru_competencia instru_competencia_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.instru_competencia
    ADD CONSTRAINT instru_competencia_pkey PRIMARY KEY (inscomp_id);


--
-- TOC entry 4988 (class 2606 OID 18097)
-- Name: instructor instructor_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.instructor
    ADD CONSTRAINT instructor_pkey PRIMARY KEY (numero_documento);


--
-- TOC entry 4990 (class 2606 OID 18099)
-- Name: programa programa_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.programa
    ADD CONSTRAINT programa_pkey PRIMARY KEY (prog_codigo);


--
-- TOC entry 4992 (class 2606 OID 18101)
-- Name: sede sede_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sede
    ADD CONSTRAINT sede_pkey PRIMARY KEY (sede_id);


--
-- TOC entry 4994 (class 2606 OID 18103)
-- Name: titulo_programa titulo_programa_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.titulo_programa
    ADD CONSTRAINT titulo_programa_pkey PRIMARY KEY (titpro_id);


--
-- TOC entry 4996 (class 2606 OID 18105)
-- Name: usuario_coordinador usuario_coordinador_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuario_coordinador
    ADD CONSTRAINT usuario_coordinador_pkey PRIMARY KEY (numero_documento);


--
-- TOC entry 5018 (class 2620 OID 18106)
-- Name: asignacion trg_asignacion_audit; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_asignacion_audit AFTER INSERT OR DELETE OR UPDATE ON public.asignacion FOR EACH ROW EXECUTE FUNCTION public.func_auditoria_asignacion();


--
-- TOC entry 4997 (class 2606 OID 18107)
-- Name: ambiente fk_ambiente_sede1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ambiente
    ADD CONSTRAINT fk_ambiente_sede1 FOREIGN KEY (sede_sede_id) REFERENCES public.sede(sede_id);


--
-- TOC entry 4998 (class 2606 OID 18112)
-- Name: asignacion fk_asignacion_ambiente1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asignacion
    ADD CONSTRAINT fk_asignacion_ambiente1 FOREIGN KEY (ambiente_amb_id) REFERENCES public.ambiente(amb_id);


--
-- TOC entry 4999 (class 2606 OID 18117)
-- Name: asignacion fk_asignacion_competencia1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asignacion
    ADD CONSTRAINT fk_asignacion_competencia1 FOREIGN KEY (competencia_comp_id) REFERENCES public.competencia(comp_id);


--
-- TOC entry 5000 (class 2606 OID 18122)
-- Name: asignacion fk_asignacion_ficha1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asignacion
    ADD CONSTRAINT fk_asignacion_ficha1 FOREIGN KEY (ficha_fich_id) REFERENCES public.ficha(fich_id);


--
-- TOC entry 5001 (class 2606 OID 18127)
-- Name: asignacion fk_asignacion_instructor1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asignacion
    ADD CONSTRAINT fk_asignacion_instructor1 FOREIGN KEY (instructor_inst_id) REFERENCES public.instructor(numero_documento);


--
-- TOC entry 5002 (class 2606 OID 18132)
-- Name: competencia fk_competencia_centro; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.competencia
    ADD CONSTRAINT fk_competencia_centro FOREIGN KEY (centro_formacion_cent_id) REFERENCES public.centro_formacion(cent_id);


--
-- TOC entry 5003 (class 2606 OID 18137)
-- Name: competxprograma fk_competxprograma_competencia1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.competxprograma
    ADD CONSTRAINT fk_competxprograma_competencia1 FOREIGN KEY (competencia_comp_id) REFERENCES public.competencia(comp_id);


--
-- TOC entry 5004 (class 2606 OID 18142)
-- Name: competxprograma fk_competxprograma_programa1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.competxprograma
    ADD CONSTRAINT fk_competxprograma_programa1 FOREIGN KEY (programa_prog_id) REFERENCES public.programa(prog_codigo);


--
-- TOC entry 5005 (class 2606 OID 18147)
-- Name: coordinacion fk_coordinacion_centro_formacion1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.coordinacion
    ADD CONSTRAINT fk_coordinacion_centro_formacion1 FOREIGN KEY (centro_formacion_cent_id) REFERENCES public.centro_formacion(cent_id);


--
-- TOC entry 5006 (class 2606 OID 18152)
-- Name: coordinacion fk_coordinador_actual; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.coordinacion
    ADD CONSTRAINT fk_coordinador_actual FOREIGN KEY (coordinador_actual) REFERENCES public.usuario_coordinador(numero_documento) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 5007 (class 2606 OID 18157)
-- Name: detallexasignacion fk_detallexasignacion_asignacion1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detallexasignacion
    ADD CONSTRAINT fk_detallexasignacion_asignacion1 FOREIGN KEY (asignacion_asig_id) REFERENCES public.asignacion(asig_id);


--
-- TOC entry 5008 (class 2606 OID 18162)
-- Name: ficha fk_ficha_coordinacion; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ficha
    ADD CONSTRAINT fk_ficha_coordinacion FOREIGN KEY (coordinacion_coord_id) REFERENCES public.coordinacion(coord_id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 5009 (class 2606 OID 18167)
-- Name: ficha fk_ficha_instructor1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ficha
    ADD CONSTRAINT fk_ficha_instructor1 FOREIGN KEY (instructor_inst_id_lider) REFERENCES public.instructor(numero_documento);


--
-- TOC entry 5010 (class 2606 OID 18172)
-- Name: ficha fk_ficha_programa1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ficha
    ADD CONSTRAINT fk_ficha_programa1 FOREIGN KEY (programa_prog_id) REFERENCES public.programa(prog_codigo);


--
-- TOC entry 5011 (class 2606 OID 18177)
-- Name: instru_competencia fk_instru_competencia_competxprograma1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.instru_competencia
    ADD CONSTRAINT fk_instru_competencia_competxprograma1 FOREIGN KEY (competxprograma_programa_prog_id, competxprograma_competencia_comp_id) REFERENCES public.competxprograma(programa_prog_id, competencia_comp_id);


--
-- TOC entry 5012 (class 2606 OID 18182)
-- Name: instru_competencia fk_instru_competencia_instructor1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.instru_competencia
    ADD CONSTRAINT fk_instru_competencia_instructor1 FOREIGN KEY (instructor_inst_id) REFERENCES public.instructor(numero_documento);


--
-- TOC entry 5013 (class 2606 OID 18187)
-- Name: instructor fk_instructor_centro_formacion1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.instructor
    ADD CONSTRAINT fk_instructor_centro_formacion1 FOREIGN KEY (centro_formacion_cent_id) REFERENCES public.centro_formacion(cent_id);


--
-- TOC entry 5014 (class 2606 OID 18192)
-- Name: programa fk_programa_centro; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.programa
    ADD CONSTRAINT fk_programa_centro FOREIGN KEY (centro_formacion_cent_id) REFERENCES public.centro_formacion(cent_id);


--
-- TOC entry 5015 (class 2606 OID 18197)
-- Name: programa fk_programa_tipo_programa; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.programa
    ADD CONSTRAINT fk_programa_tipo_programa FOREIGN KEY (tit_programa_titpro_id) REFERENCES public.titulo_programa(titpro_id);


--
-- TOC entry 5016 (class 2606 OID 18202)
-- Name: titulo_programa fk_titulo_programa_centro; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.titulo_programa
    ADD CONSTRAINT fk_titulo_programa_centro FOREIGN KEY (centro_formacion_cent_id) REFERENCES public.centro_formacion(cent_id);


--
-- TOC entry 5017 (class 2606 OID 18207)
-- Name: usuario_coordinador fk_user_centro; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuario_coordinador
    ADD CONSTRAINT fk_user_centro FOREIGN KEY (centro_formacion_id) REFERENCES public.centro_formacion(cent_id) ON UPDATE CASCADE ON DELETE SET NULL;


-- Completed on 2026-03-13 11:56:10

--
-- PostgreSQL database dump complete
--



