--
-- PostgreSQL database dump
--


-- Dumped from database version 18.1
-- Dumped by pg_dump version 18.1

-- Started on 2026-03-20 12:07:32

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

ALTER TABLE IF EXISTS ONLY public.rap_fase DROP CONSTRAINT IF EXISTS rap_fase_rap_rap_id_fkey;
ALTER TABLE IF EXISTS ONLY public.rap_fase DROP CONSTRAINT IF EXISTS rap_fase_fase_fase_id_fkey;
ALTER TABLE IF EXISTS ONLY public.proyecto_formativo DROP CONSTRAINT IF EXISTS proyecto_formativo_programa_prog_codigo_fkey;
ALTER TABLE IF EXISTS ONLY public.proyecto_formativo DROP CONSTRAINT IF EXISTS proyecto_formativo_centro_formacion_cent_id_fkey;
ALTER TABLE IF EXISTS ONLY public.usuario_coordinador DROP CONSTRAINT IF EXISTS fk_user_centro;
ALTER TABLE IF EXISTS ONLY public.titulo_programa DROP CONSTRAINT IF EXISTS fk_titulo_programa_centro;
ALTER TABLE IF EXISTS ONLY public.sede DROP CONSTRAINT IF EXISTS fk_sede_centro;
ALTER TABLE IF EXISTS ONLY public.resultado_aprendizaje DROP CONSTRAINT IF EXISTS fk_rap_competxprog;
ALTER TABLE IF EXISTS ONLY public.programa DROP CONSTRAINT IF EXISTS fk_programa_tipo_programa;
ALTER TABLE IF EXISTS ONLY public.programa DROP CONSTRAINT IF EXISTS fk_programa_centro;
ALTER TABLE IF EXISTS ONLY public.instructor DROP CONSTRAINT IF EXISTS fk_instructor_centro_formacion1;
ALTER TABLE IF EXISTS ONLY public.instru_competencia DROP CONSTRAINT IF EXISTS fk_instru_competencia_instructor1;
ALTER TABLE IF EXISTS ONLY public.instru_competencia DROP CONSTRAINT IF EXISTS fk_instru_comp_competxprog;
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
ALTER TABLE IF EXISTS ONLY public.ficha_proyecto DROP CONSTRAINT IF EXISTS ficha_proyecto_pf_pf_id_fkey;
ALTER TABLE IF EXISTS ONLY public.ficha_proyecto DROP CONSTRAINT IF EXISTS ficha_proyecto_fich_fich_id_fkey;
ALTER TABLE IF EXISTS ONLY public.fase_proyecto DROP CONSTRAINT IF EXISTS fase_proyecto_pf_pf_id_fkey;
ALTER TABLE IF EXISTS ONLY public.competencia_horas_programa DROP CONSTRAINT IF EXISTS competencia_horas_programa_prog_codigo_fkey;
ALTER TABLE IF EXISTS ONLY public.competencia_horas_programa DROP CONSTRAINT IF EXISTS competencia_horas_programa_comp_id_fkey;
DROP TRIGGER IF EXISTS trg_asignacion_audit ON public.asignacion;
ALTER TABLE IF EXISTS ONLY public.usuario_coordinador DROP CONSTRAINT IF EXISTS usuario_coordinador_pkey;
ALTER TABLE IF EXISTS ONLY public.titulo_programa DROP CONSTRAINT IF EXISTS titulo_programa_pkey;
ALTER TABLE IF EXISTS ONLY public.sede DROP CONSTRAINT IF EXISTS sede_pkey;
ALTER TABLE IF EXISTS ONLY public.resultado_aprendizaje DROP CONSTRAINT IF EXISTS resultado_aprendizaje_pkey;
ALTER TABLE IF EXISTS ONLY public.rap_fase DROP CONSTRAINT IF EXISTS rap_fase_pkey;
ALTER TABLE IF EXISTS ONLY public.proyecto_formativo DROP CONSTRAINT IF EXISTS proyecto_formativo_pkey;
ALTER TABLE IF EXISTS ONLY public.programa DROP CONSTRAINT IF EXISTS programa_pkey;
ALTER TABLE IF EXISTS ONLY public.instructor DROP CONSTRAINT IF EXISTS instructor_pkey;
ALTER TABLE IF EXISTS ONLY public.instru_competencia DROP CONSTRAINT IF EXISTS instru_competencia_pkey;
ALTER TABLE IF EXISTS ONLY public.ficha_proyecto DROP CONSTRAINT IF EXISTS ficha_proyecto_pkey;
ALTER TABLE IF EXISTS ONLY public.ficha DROP CONSTRAINT IF EXISTS ficha_pkey;
ALTER TABLE IF EXISTS ONLY public.fase_proyecto DROP CONSTRAINT IF EXISTS fase_proyecto_pkey;
ALTER TABLE IF EXISTS ONLY public.detallexasignacion DROP CONSTRAINT IF EXISTS detallexasignacion_pkey;
ALTER TABLE IF EXISTS ONLY public.coordinacion DROP CONSTRAINT IF EXISTS coordinacion_pkey;
ALTER TABLE IF EXISTS ONLY public.competxprograma DROP CONSTRAINT IF EXISTS competxprograma_pkey;
ALTER TABLE IF EXISTS ONLY public.competencia DROP CONSTRAINT IF EXISTS competencia_pkey;
ALTER TABLE IF EXISTS ONLY public.competencia_horas_programa DROP CONSTRAINT IF EXISTS competencia_horas_programa_pkey;
ALTER TABLE IF EXISTS ONLY public.centro_formacion DROP CONSTRAINT IF EXISTS centro_formacion_pkey;
ALTER TABLE IF EXISTS ONLY public.auditoria_asignacion DROP CONSTRAINT IF EXISTS auditoria_asignacion_pkey;
ALTER TABLE IF EXISTS ONLY public.asignacion DROP CONSTRAINT IF EXISTS asignacion_pkey;
ALTER TABLE IF EXISTS ONLY public.ambiente DROP CONSTRAINT IF EXISTS ambiente_pkey;
DROP TABLE IF EXISTS public.usuario_coordinador;
DROP TABLE IF EXISTS public.titulo_programa;
DROP TABLE IF EXISTS public.sede;
DROP TABLE IF EXISTS public.resultado_aprendizaje;
DROP SEQUENCE IF EXISTS public.resultado_aprendizaje_rap_id_seq;
DROP TABLE IF EXISTS public.rap_fase;
DROP TABLE IF EXISTS public.proyecto_formativo;
DROP SEQUENCE IF EXISTS public.proyecto_formativo_pf_id_seq;
DROP TABLE IF EXISTS public.programa;
DROP TABLE IF EXISTS public.instructor;
DROP TABLE IF EXISTS public.instru_competencia;
DROP SEQUENCE IF EXISTS public.instru_competencia_inscomp_id_seq;
DROP TABLE IF EXISTS public.ficha_proyecto;
DROP TABLE IF EXISTS public.ficha;
DROP TABLE IF EXISTS public.fase_proyecto;
DROP SEQUENCE IF EXISTS public.fase_proyecto_fase_id_seq;
DROP TABLE IF EXISTS public.detallexasignacion;
DROP SEQUENCE IF EXISTS public.detallexasignacion_detasig_id_seq;
DROP TABLE IF EXISTS public.coordinacion;
DROP SEQUENCE IF EXISTS public.coordinacion_coord_id_seq;
DROP TABLE IF EXISTS public.competxprograma;
DROP TABLE IF EXISTS public.competencia_horas_programa;
DROP TABLE IF EXISTS public.competencia;
DROP TABLE IF EXISTS public.centro_formacion;
DROP TABLE IF EXISTS public.auditoria_asignacion;
DROP SEQUENCE IF EXISTS public.auditoria_asignacion_id_auditoria_seq;
DROP TABLE IF EXISTS public.asignacion;
DROP SEQUENCE IF EXISTS public.asignacion_asig_id_seq;
DROP TABLE IF EXISTS public.ambiente;
DROP FUNCTION IF EXISTS public.func_auditoria_asignacion();
--
-- TOC entry 248 (class 1255 OID 30984)
-- Name: func_auditoria_asignacion(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.func_auditoria_asignacion() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    -- Si es una INSERCIÓN (Crear una nueva asignación)
    IF (TG_OP = 'INSERT') THEN
        INSERT INTO public.auditoria_asignacion (
            instructor_inst_id, asig_fecha_ini, asig_fecha_fin, ficha_fich_id, 
            ambiente_amb_id, competencia_comp_id, asig_id, 
            tipo_accion, 
            documento_usuario_accion, correo_usuario, nombre_usuario_accion
        ) VALUES (
            NEW.instructor_inst_id, NEW.asig_fecha_ini, NEW.asig_fecha_fin, NEW.ficha_fich_id,
            NEW.ambiente_amb_id, NEW.competencia_comp_id, NEW.asig_id,
            'INSERT', 
            0, 'sistema@admin.com', 'Sistema'
        );
        RETURN NEW;
        
    -- Si es una ACTUALIZACIÓN (Modificar una asignación existente)
    ELSIF (TG_OP = 'UPDATE') THEN
        INSERT INTO public.auditoria_asignacion (
            instructor_inst_id, asig_fecha_ini, asig_fecha_fin, ficha_fich_id, 
            ambiente_amb_id, competencia_comp_id, asig_id, 
            tipo_accion, 
            documento_usuario_accion, correo_usuario, nombre_usuario_accion
        ) VALUES (
            NEW.instructor_inst_id, NEW.asig_fecha_ini, NEW.asig_fecha_fin, NEW.ficha_fich_id,
            NEW.ambiente_amb_id, NEW.competencia_comp_id, NEW.asig_id,
            'UPDATE', 
            0, 'sistema@admin.com', 'Sistema'
        );
        RETURN NEW;
        
    -- Si es una ELIMINACIÓN (Borrar una asignación)
    ELSIF (TG_OP = 'DELETE') THEN
        INSERT INTO public.auditoria_asignacion (
            instructor_inst_id, asig_fecha_ini, asig_fecha_fin, ficha_fich_id, 
            ambiente_amb_id, competencia_comp_id, asig_id, 
            tipo_accion, 
            documento_usuario_accion, correo_usuario, nombre_usuario_accion
        ) VALUES (
            OLD.instructor_inst_id, OLD.asig_fecha_ini, OLD.asig_fecha_fin, OLD.ficha_fich_id,
            OLD.ambiente_amb_id, OLD.competencia_comp_id, OLD.asig_id,
            'DELETE', 
            0, 'sistema@admin.com', 'Sistema'
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
-- TOC entry 234 (class 1259 OID 30686)
-- Name: ambiente; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.ambiente (
    amb_id character varying NOT NULL,
    amb_nombre character varying,
    tipo_ambiente character varying DEFAULT 'Convencional'::character varying NOT NULL,
    sede_sede_id integer NOT NULL
);


ALTER TABLE public.ambiente OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 30566)
-- Name: asignacion_asig_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.asignacion_asig_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.asignacion_asig_id_seq OWNER TO postgres;

--
-- TOC entry 241 (class 1259 OID 30836)
-- Name: asignacion; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.asignacion (
    asig_id integer DEFAULT nextval('public.asignacion_asig_id_seq'::regclass) NOT NULL,
    instructor_inst_id bigint NOT NULL,
    asig_fecha_ini date NOT NULL,
    asig_fecha_fin date NOT NULL,
    ficha_fich_id integer NOT NULL,
    ambiente_amb_id character varying NOT NULL,
    competencia_comp_id integer NOT NULL
);


ALTER TABLE public.asignacion OWNER TO postgres;

--
-- TOC entry 220 (class 1259 OID 30567)
-- Name: auditoria_asignacion_id_auditoria_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.auditoria_asignacion_id_auditoria_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.auditoria_asignacion_id_auditoria_seq OWNER TO postgres;

--
-- TOC entry 228 (class 1259 OID 30583)
-- Name: auditoria_asignacion; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.auditoria_asignacion (
    id_auditoria integer DEFAULT nextval('public.auditoria_asignacion_id_auditoria_seq'::regclass) NOT NULL,
    instructor_inst_id bigint NOT NULL,
    asig_fecha_ini date NOT NULL,
    asig_fecha_fin date NOT NULL,
    ficha_fich_id integer NOT NULL,
    ambiente_amb_id character varying NOT NULL,
    competencia_comp_id integer NOT NULL,
    asig_id integer NOT NULL,
    fecha_hora timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    documento_usuario_accion bigint NOT NULL,
    correo_usuario character varying NOT NULL,
    tipo_accion character varying NOT NULL,
    nombre_usuario_accion character varying
);


ALTER TABLE public.auditoria_asignacion OWNER TO postgres;

--
-- TOC entry 227 (class 1259 OID 30574)
-- Name: centro_formacion; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.centro_formacion (
    cent_id integer NOT NULL,
    cent_nombre character varying NOT NULL,
    cent_correo character varying,
    cent_password character varying
);


ALTER TABLE public.centro_formacion OWNER TO postgres;

--
-- TOC entry 233 (class 1259 OID 30670)
-- Name: competencia; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.competencia (
    comp_id integer NOT NULL,
    comp_nombre_corto character varying NOT NULL,
    comp_horas integer NOT NULL,
    comp_nombre_unidad_competencia character varying NOT NULL,
    centro_formacion_cent_id integer
);


ALTER TABLE public.competencia OWNER TO postgres;

--
-- TOC entry 239 (class 1259 OID 30798)
-- Name: competencia_horas_programa; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.competencia_horas_programa (
    prog_codigo integer NOT NULL,
    comp_id integer NOT NULL,
    horas_requeridas integer DEFAULT 0 NOT NULL,
    aplica boolean DEFAULT true NOT NULL
);


ALTER TABLE public.competencia_horas_programa OWNER TO postgres;

--
-- TOC entry 240 (class 1259 OID 30819)
-- Name: competxprograma; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.competxprograma (
    programa_prog_id integer NOT NULL,
    competencia_comp_id integer NOT NULL
);


ALTER TABLE public.competxprograma OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 30568)
-- Name: coordinacion_coord_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.coordinacion_coord_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.coordinacion_coord_id_seq OWNER TO postgres;

--
-- TOC entry 235 (class 1259 OID 30702)
-- Name: coordinacion; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.coordinacion (
    coord_id integer DEFAULT nextval('public.coordinacion_coord_id_seq'::regclass) NOT NULL,
    coord_descripcion character varying NOT NULL,
    centro_formacion_cent_id integer NOT NULL,
    estado smallint DEFAULT 1 NOT NULL,
    coordinador_actual bigint
);


ALTER TABLE public.coordinacion OWNER TO postgres;

--
-- TOC entry 222 (class 1259 OID 30569)
-- Name: detallexasignacion_detasig_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.detallexasignacion_detasig_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.detallexasignacion_detasig_id_seq OWNER TO postgres;

--
-- TOC entry 246 (class 1259 OID 30948)
-- Name: detallexasignacion; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.detallexasignacion (
    detasig_id integer DEFAULT nextval('public.detallexasignacion_detasig_id_seq'::regclass) NOT NULL,
    asignacion_asig_id integer NOT NULL,
    detasig_hora_ini time without time zone NOT NULL,
    detasig_hora_fin time without time zone NOT NULL,
    detasig_fecha date DEFAULT CURRENT_DATE NOT NULL,
    observaciones character varying
);


ALTER TABLE public.detallexasignacion OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 30570)
-- Name: fase_proyecto_fase_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.fase_proyecto_fase_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.fase_proyecto_fase_id_seq OWNER TO postgres;

--
-- TOC entry 242 (class 1259 OID 30871)
-- Name: fase_proyecto; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.fase_proyecto (
    fase_id integer DEFAULT nextval('public.fase_proyecto_fase_id_seq'::regclass) NOT NULL,
    fase_nombre character varying NOT NULL,
    fase_orden smallint NOT NULL,
    fase_fecha_ini date NOT NULL,
    fase_fecha_fin date NOT NULL,
    pf_pf_id integer NOT NULL
);


ALTER TABLE public.fase_proyecto OWNER TO postgres;

--
-- TOC entry 237 (class 1259 OID 30746)
-- Name: ficha; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.ficha (
    fich_id integer NOT NULL,
    programa_prog_id integer NOT NULL,
    instructor_inst_id_lider bigint NOT NULL,
    fich_jornada character varying NOT NULL,
    coordinacion_coord_id integer NOT NULL,
    fich_fecha_ini_lectiva date NOT NULL,
    fich_fecha_fin_lectiva date NOT NULL
);


ALTER TABLE public.ficha OWNER TO postgres;

--
-- TOC entry 243 (class 1259 OID 30890)
-- Name: ficha_proyecto; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.ficha_proyecto (
    fich_fich_id integer NOT NULL,
    pf_pf_id integer NOT NULL
);


ALTER TABLE public.ficha_proyecto OWNER TO postgres;

--
-- TOC entry 224 (class 1259 OID 30571)
-- Name: instru_competencia_inscomp_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.instru_competencia_inscomp_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.instru_competencia_inscomp_id_seq OWNER TO postgres;

--
-- TOC entry 244 (class 1259 OID 30907)
-- Name: instru_competencia; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.instru_competencia (
    inscomp_id integer DEFAULT nextval('public.instru_competencia_inscomp_id_seq'::regclass) NOT NULL,
    instructor_inst_id bigint NOT NULL,
    competxprograma_programa_prog_id integer NOT NULL,
    competxprograma_competencia_comp_id integer NOT NULL,
    inscomp_vigencia date NOT NULL
);


ALTER TABLE public.instru_competencia OWNER TO postgres;

--
-- TOC entry 232 (class 1259 OID 30649)
-- Name: instructor; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.instructor (
    numero_documento bigint NOT NULL,
    inst_nombres character varying NOT NULL,
    inst_apellidos character varying NOT NULL,
    inst_correo character varying NOT NULL,
    inst_telefono bigint NOT NULL,
    centro_formacion_cent_id integer NOT NULL,
    inst_password character varying NOT NULL,
    estado smallint DEFAULT 1 NOT NULL
);


ALTER TABLE public.instructor OWNER TO postgres;

--
-- TOC entry 236 (class 1259 OID 30725)
-- Name: programa; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.programa (
    prog_codigo integer NOT NULL,
    prog_denominacion character varying NOT NULL,
    tit_programa_titpro_id integer NOT NULL,
    prog_tipo character varying NOT NULL,
    centro_formacion_cent_id integer
);


ALTER TABLE public.programa OWNER TO postgres;

--
-- TOC entry 225 (class 1259 OID 30572)
-- Name: proyecto_formativo_pf_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.proyecto_formativo_pf_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.proyecto_formativo_pf_id_seq OWNER TO postgres;

--
-- TOC entry 238 (class 1259 OID 30775)
-- Name: proyecto_formativo; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.proyecto_formativo (
    pf_id integer DEFAULT nextval('public.proyecto_formativo_pf_id_seq'::regclass) NOT NULL,
    pf_codigo character varying NOT NULL,
    pf_nombre character varying NOT NULL,
    pf_descripcion text,
    programa_prog_codigo integer NOT NULL,
    centro_formacion_cent_id integer NOT NULL
);


ALTER TABLE public.proyecto_formativo OWNER TO postgres;

--
-- TOC entry 247 (class 1259 OID 30967)
-- Name: rap_fase; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.rap_fase (
    rap_rap_id integer NOT NULL,
    fase_fase_id integer NOT NULL
);


ALTER TABLE public.rap_fase OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 30573)
-- Name: resultado_aprendizaje_rap_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.resultado_aprendizaje_rap_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.resultado_aprendizaje_rap_id_seq OWNER TO postgres;

--
-- TOC entry 245 (class 1259 OID 30928)
-- Name: resultado_aprendizaje; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.resultado_aprendizaje (
    rap_id integer DEFAULT nextval('public.resultado_aprendizaje_rap_id_seq'::regclass) NOT NULL,
    rap_codigo character varying NOT NULL,
    rap_descripcion text NOT NULL,
    rap_horas integer DEFAULT 0 NOT NULL,
    competxprog_prog_id integer NOT NULL,
    competxprog_comp_id integer NOT NULL
);


ALTER TABLE public.resultado_aprendizaje OWNER TO postgres;

--
-- TOC entry 229 (class 1259 OID 30603)
-- Name: sede; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sede (
    sede_id integer NOT NULL,
    sede_nombre character varying NOT NULL,
    centro_formacion_cent_id integer
);


ALTER TABLE public.sede OWNER TO postgres;

--
-- TOC entry 230 (class 1259 OID 30617)
-- Name: titulo_programa; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.titulo_programa (
    titpro_id integer NOT NULL,
    titpro_nombre character varying NOT NULL,
    centro_formacion_cent_id integer
);


ALTER TABLE public.titulo_programa OWNER TO postgres;

--
-- TOC entry 231 (class 1259 OID 30631)
-- Name: usuario_coordinador; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.usuario_coordinador (
    numero_documento bigint NOT NULL,
    coord_nombre_coordinador character varying NOT NULL,
    coord_correo character varying NOT NULL,
    coord_password character varying NOT NULL,
    estado smallint DEFAULT 1 NOT NULL,
    centro_formacion_id integer
);


ALTER TABLE public.usuario_coordinador OWNER TO postgres;

--
-- TOC entry 5198 (class 0 OID 30686)
-- Dependencies: 234
-- Data for Name: ambiente; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.ambiente (amb_id, amb_nombre, tipo_ambiente, sede_sede_id) FROM stdin;
AMB01	Sistemas	Convencional	1
AMB02	Gestión	Convencional	2
AMB03	Minería	Especializado	3
AMB04	Software	Especializado	4
AMB05	Diseño	Convencional	5
AMB06	Electrónica	Especializado	6
AMB07	Logística	Convencional	7
AMB08	Polivalente	Polivalente	8
\.


--
-- TOC entry 5205 (class 0 OID 30836)
-- Dependencies: 241
-- Data for Name: asignacion; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.asignacion (asig_id, instructor_inst_id, asig_fecha_ini, asig_fecha_fin, ficha_fich_id, ambiente_amb_id, competencia_comp_id) FROM stdin;
\.


--
-- TOC entry 5192 (class 0 OID 30583)
-- Dependencies: 228
-- Data for Name: auditoria_asignacion; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.auditoria_asignacion (id_auditoria, instructor_inst_id, asig_fecha_ini, asig_fecha_fin, ficha_fich_id, ambiente_amb_id, competencia_comp_id, asig_id, fecha_hora, documento_usuario_accion, correo_usuario, tipo_accion, nombre_usuario_accion) FROM stdin;
\.


--
-- TOC entry 5191 (class 0 OID 30574)
-- Dependencies: 227
-- Data for Name: centro_formacion; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.centro_formacion (cent_id, cent_nombre, cent_correo, cent_password) FROM stdin;
1	CEDRUM	cedrum@sena.edu.co	$2y$12$H1JUPMDnDs/SdGQDkFBJUu1rIdvzOCTBQ3ZQDrastmo1ltYkVvu2y
2	CIES	cies@sena.edu.co	$2y$12$H1JUPMDnDs/SdGQDkFBJUu1rIdvzOCTBQ3ZQDrastmo1ltYkVvu2y
\.


--
-- TOC entry 5197 (class 0 OID 30670)
-- Dependencies: 233
-- Data for Name: competencia; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.competencia (comp_id, comp_nombre_corto, comp_horas, comp_nombre_unidad_competencia, centro_formacion_cent_id) FROM stdin;
1	COMP-ADSO	200	Construir software según requerimientos	1
2	COMP-GESTION	150	Administrar recursos de la organización	1
3	COMP-MOT	180	Corregir fallas en motores rotativos	1
4	COMP-VJ	220	Programar interactividad del videojuego	1
5	COMP-A3D	200	Modelar y animar objetos tridimensionales	2
6	COMP-IA	240	Entrenar modelos de aprendizaje automático	2
7	COMP-MODA	160	Elaborar patrones de prendas de vestir	2
8	COMP-ELEC	190	Implementar circuitos electrónicos	2
\.


--
-- TOC entry 5203 (class 0 OID 30798)
-- Dependencies: 239
-- Data for Name: competencia_horas_programa; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.competencia_horas_programa (prog_codigo, comp_id, horas_requeridas, aplica) FROM stdin;
\.


--
-- TOC entry 5204 (class 0 OID 30819)
-- Dependencies: 240
-- Data for Name: competxprograma; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.competxprograma (programa_prog_id, competencia_comp_id) FROM stdin;
1	1
2	2
3	3
4	4
5	5
6	6
7	7
8	8
\.


--
-- TOC entry 5199 (class 0 OID 30702)
-- Dependencies: 235
-- Data for Name: coordinacion; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.coordinacion (coord_id, coord_descripcion, centro_formacion_cent_id, estado, coordinador_actual) FROM stdin;
1	Coordinación CEDRUM 1	1	1	11
5	Coordinación CIES 1	2	1	21
2	Coordinación CEDRUM 2	1	1	12
6	Coordinación CIES 2	2	1	22
3	Coordinación CEDRUM 3	1	1	13
7	Coordinación CIES 3	2	1	23
4	Coordinación CEDRUM 4	1	1	14
8	Coordinación CIES 4	2	1	24
\.


--
-- TOC entry 5210 (class 0 OID 30948)
-- Dependencies: 246
-- Data for Name: detallexasignacion; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.detallexasignacion (detasig_id, asignacion_asig_id, detasig_hora_ini, detasig_hora_fin, detasig_fecha, observaciones) FROM stdin;
\.


--
-- TOC entry 5206 (class 0 OID 30871)
-- Dependencies: 242
-- Data for Name: fase_proyecto; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.fase_proyecto (fase_id, fase_nombre, fase_orden, fase_fecha_ini, fase_fecha_fin, pf_pf_id) FROM stdin;
1	Análisis	1	2026-03-20	2026-03-24	1
2	Planeación	2	2026-03-25	2026-03-26	1
3	Ejecución	3	2026-04-07	2026-04-24	1
4	Evaluación	4	2026-05-06	2026-05-29	1
\.


--
-- TOC entry 5201 (class 0 OID 30746)
-- Dependencies: 237
-- Data for Name: ficha; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.ficha (fich_id, programa_prog_id, instructor_inst_id_lider, fich_jornada, coordinacion_coord_id, fich_fecha_ini_lectiva, fich_fecha_fin_lectiva) FROM stdin;
1001	1	101	Diurna	1	2026-01-01	2026-12-31
2001	5	201	Diurna	5	2026-01-01	2026-12-31
1002	2	102	Diurna	2	2026-01-01	2026-12-31
2002	6	202	Diurna	6	2026-01-01	2026-12-31
1003	3	103	Diurna	3	2026-01-01	2026-12-31
2003	7	203	Diurna	7	2026-01-01	2026-12-31
1004	4	104	Diurna	4	2026-01-01	2026-12-31
2004	8	204	Diurna	8	2026-01-01	2026-12-31
\.


--
-- TOC entry 5207 (class 0 OID 30890)
-- Dependencies: 243
-- Data for Name: ficha_proyecto; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.ficha_proyecto (fich_fich_id, pf_pf_id) FROM stdin;
\.


--
-- TOC entry 5208 (class 0 OID 30907)
-- Dependencies: 244
-- Data for Name: instru_competencia; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.instru_competencia (inscomp_id, instructor_inst_id, competxprograma_programa_prog_id, competxprograma_competencia_comp_id, inscomp_vigencia) FROM stdin;
1	101	1	1	2026-12-31
2	102	2	2	2026-12-31
3	103	3	3	2026-12-31
4	104	4	4	2026-12-31
5	105	1	1	2026-12-31
6	106	2	2	2026-12-31
7	107	3	3	2026-12-31
8	108	4	4	2026-12-31
9	201	5	5	2026-12-31
10	202	6	6	2026-12-31
11	203	7	7	2026-12-31
12	204	8	8	2026-12-31
13	205	5	5	2026-12-31
14	206	6	6	2026-12-31
15	207	7	7	2026-12-31
16	208	8	8	2026-12-31
\.


--
-- TOC entry 5196 (class 0 OID 30649)
-- Dependencies: 232
-- Data for Name: instructor; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.instructor (numero_documento, inst_nombres, inst_apellidos, inst_correo, inst_telefono, centro_formacion_cent_id, inst_password, estado) FROM stdin;
101	Instr CEDRUM 1	Apellido 1	inst_cedrum1@sena.edu.co	3000000001	1	$2y$12$lEf4dphK4g2z.jowGNJR5eF1WEnmtTd5cNyLK8uce0Hbl6w5nIbo.	1
201	Instr CIES 1	Apellido 1	inst_cies1@sena.edu.co	3100000001	2	$2y$12$lEf4dphK4g2z.jowGNJR5eF1WEnmtTd5cNyLK8uce0Hbl6w5nIbo.	1
102	Instr CEDRUM 2	Apellido 2	inst_cedrum2@sena.edu.co	3000000002	1	$2y$12$A5ysOelYgfGHNN2r94IaU.599o4KKzUSkGPjbuYaldF8ftpOvcyrW	1
202	Instr CIES 2	Apellido 2	inst_cies2@sena.edu.co	3100000002	2	$2y$12$A5ysOelYgfGHNN2r94IaU.599o4KKzUSkGPjbuYaldF8ftpOvcyrW	1
103	Instr CEDRUM 3	Apellido 3	inst_cedrum3@sena.edu.co	3000000003	1	$2y$12$gx4R/tdQ3HIJMgpGm1r/.e2SW1S48rzIN4CjpA3e8yltVzUEDmd/.	1
203	Instr CIES 3	Apellido 3	inst_cies3@sena.edu.co	3100000003	2	$2y$12$gx4R/tdQ3HIJMgpGm1r/.e2SW1S48rzIN4CjpA3e8yltVzUEDmd/.	1
104	Instr CEDRUM 4	Apellido 4	inst_cedrum4@sena.edu.co	3000000004	1	$2y$12$osGJWS1Rn02vbIImNPZAGezpJMXhPYddmVTzP.Aid/emy76k3r0oK	1
204	Instr CIES 4	Apellido 4	inst_cies4@sena.edu.co	3100000004	2	$2y$12$osGJWS1Rn02vbIImNPZAGezpJMXhPYddmVTzP.Aid/emy76k3r0oK	1
105	Instr CEDRUM 5	Apellido 5	inst_cedrum5@sena.edu.co	3000000005	1	$2y$12$ESnubAA.ZZmz9XeM/qa2UeWh8ucUgwMlRk1OoLf7BI37K/im39NM.	1
205	Instr CIES 5	Apellido 5	inst_cies5@sena.edu.co	3100000005	2	$2y$12$ESnubAA.ZZmz9XeM/qa2UeWh8ucUgwMlRk1OoLf7BI37K/im39NM.	1
106	Instr CEDRUM 6	Apellido 6	inst_cedrum6@sena.edu.co	3000000006	1	$2y$12$aOgM.tf/BLGNyKaMQN6wJOmQFUo1vluWsgKinCg8kwcC8mcacNBae	1
206	Instr CIES 6	Apellido 6	inst_cies6@sena.edu.co	3100000006	2	$2y$12$aOgM.tf/BLGNyKaMQN6wJOmQFUo1vluWsgKinCg8kwcC8mcacNBae	1
107	Instr CEDRUM 7	Apellido 7	inst_cedrum7@sena.edu.co	3000000007	1	$2y$12$RLWgW5mJ3CRVGHPDTIT/5ukE2w1rq4XM20xPDhaOsnArLiBSstn6.	1
207	Instr CIES 7	Apellido 7	inst_cies7@sena.edu.co	3100000007	2	$2y$12$RLWgW5mJ3CRVGHPDTIT/5ukE2w1rq4XM20xPDhaOsnArLiBSstn6.	1
108	Instr CEDRUM 8	Apellido 8	inst_cedrum8@sena.edu.co	3000000008	1	$2y$12$hssO4XBvu21zblsRCAKaee0dQNhdJxLdrXWkSBfxq0qDryEXt08xK	1
208	Instr CIES 8	Apellido 8	inst_cies8@sena.edu.co	3100000008	2	$2y$12$hssO4XBvu21zblsRCAKaee0dQNhdJxLdrXWkSBfxq0qDryEXt08xK	1
\.


--
-- TOC entry 5200 (class 0 OID 30725)
-- Dependencies: 236
-- Data for Name: programa; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.programa (prog_codigo, prog_denominacion, tit_programa_titpro_id, prog_tipo, centro_formacion_cent_id) FROM stdin;
1	ADSO - Analisis y Desarrollo de Software	1	Presencial	1
2	Gestión Administrativa	1	Presencial	1
3	Mantenimiento de Motores	1	Presencial	1
4	Desarrollo de Videojuegos	1	Virtual	1
5	Animación 3D	2	Presencial	2
6	Inteligencia Artificial	2	Presencial	2
7	Diseño de Modas	2	Presencial	2
8	Mantenimiento Electrónico	2	Presencial	2
\.


--
-- TOC entry 5202 (class 0 OID 30775)
-- Dependencies: 238
-- Data for Name: proyecto_formativo; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.proyecto_formativo (pf_id, pf_codigo, pf_nombre, pf_descripcion, programa_prog_codigo, centro_formacion_cent_id) FROM stdin;
1	6456565	hola	adios	6	2
\.


--
-- TOC entry 5211 (class 0 OID 30967)
-- Dependencies: 247
-- Data for Name: rap_fase; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.rap_fase (rap_rap_id, fase_fase_id) FROM stdin;
1	1
\.


--
-- TOC entry 5209 (class 0 OID 30928)
-- Dependencies: 245
-- Data for Name: resultado_aprendizaje; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.resultado_aprendizaje (rap_id, rap_codigo, rap_descripcion, rap_horas, competxprog_prog_id, competxprog_comp_id) FROM stdin;
1	657576	holaaa	451	6	6
\.


--
-- TOC entry 5193 (class 0 OID 30603)
-- Dependencies: 229
-- Data for Name: sede; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sede (sede_id, sede_nombre, centro_formacion_cent_id) FROM stdin;
1	Sede Principal CEDRUM	1
2	Sede Norte CEDRUM	1
3	Sede Sur CEDRUM	1
4	Sede Oriente CEDRUM	1
5	Sede Principal CIES	2
6	Sede Industrial CIES	2
7	Sede Comercio CIES	2
8	Sede Tecnol CIES	2
\.


--
-- TOC entry 5194 (class 0 OID 30617)
-- Dependencies: 230
-- Data for Name: titulo_programa; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.titulo_programa (titpro_id, titpro_nombre, centro_formacion_cent_id) FROM stdin;
1	Tecnólogo	1
2	Tecnólogo	2
\.


--
-- TOC entry 5195 (class 0 OID 30631)
-- Dependencies: 231
-- Data for Name: usuario_coordinador; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.usuario_coordinador (numero_documento, coord_nombre_coordinador, coord_correo, coord_password, estado, centro_formacion_id) FROM stdin;
11	Coordinador CEDRUM 1	coord_cedrum1@sena.edu.co	$2y$12$9q1LZgMMlgHTohyrsl4TQuR1tqrpQMwoaymYbJpsBlUcnTNr1E9T.	1	1
21	Coordinador CIES 1	coord_cies1@sena.edu.co	$2y$12$9q1LZgMMlgHTohyrsl4TQuR1tqrpQMwoaymYbJpsBlUcnTNr1E9T.	1	2
12	Coordinador CEDRUM 2	coord_cedrum2@sena.edu.co	$2y$12$Qton42GeBRxEizsw/laEBueFohb5srIGsvUphaxLr.4ZEtChsHX1a	1	1
22	Coordinador CIES 2	coord_cies2@sena.edu.co	$2y$12$Qton42GeBRxEizsw/laEBueFohb5srIGsvUphaxLr.4ZEtChsHX1a	1	2
13	Coordinador CEDRUM 3	coord_cedrum3@sena.edu.co	$2y$12$Pa/btTHxf15vQMEknPZwiefuBF5F1xyOo/1P.iDMyy7sy1SQkj4/.	1	1
23	Coordinador CIES 3	coord_cies3@sena.edu.co	$2y$12$Pa/btTHxf15vQMEknPZwiefuBF5F1xyOo/1P.iDMyy7sy1SQkj4/.	1	2
14	Coordinador CEDRUM 4	coord_cedrum4@sena.edu.co	$2y$12$rBanip5Io00fWbeBl3WBSeHgVHl/7T.9i6ikuG6h.QZOXYDUgoo1m	1	1
24	Coordinador CIES 4	coord_cies4@sena.edu.co	$2y$12$rBanip5Io00fWbeBl3WBSeHgVHl/7T.9i6ikuG6h.QZOXYDUgoo1m	1	2
\.


--
-- TOC entry 5217 (class 0 OID 0)
-- Dependencies: 219
-- Name: asignacion_asig_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.asignacion_asig_id_seq', 1, false);


--
-- TOC entry 5218 (class 0 OID 0)
-- Dependencies: 220
-- Name: auditoria_asignacion_id_auditoria_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.auditoria_asignacion_id_auditoria_seq', 1, false);


--
-- TOC entry 5219 (class 0 OID 0)
-- Dependencies: 221
-- Name: coordinacion_coord_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.coordinacion_coord_id_seq', 1, false);


--
-- TOC entry 5220 (class 0 OID 0)
-- Dependencies: 222
-- Name: detallexasignacion_detasig_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.detallexasignacion_detasig_id_seq', 1, false);


--
-- TOC entry 5221 (class 0 OID 0)
-- Dependencies: 223
-- Name: fase_proyecto_fase_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.fase_proyecto_fase_id_seq', 4, true);


--
-- TOC entry 5222 (class 0 OID 0)
-- Dependencies: 224
-- Name: instru_competencia_inscomp_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.instru_competencia_inscomp_id_seq', 16, true);


--
-- TOC entry 5223 (class 0 OID 0)
-- Dependencies: 225
-- Name: proyecto_formativo_pf_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.proyecto_formativo_pf_id_seq', 1, true);


--
-- TOC entry 5224 (class 0 OID 0)
-- Dependencies: 226
-- Name: resultado_aprendizaje_rap_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.resultado_aprendizaje_rap_id_seq', 1, true);


--
-- TOC entry 4976 (class 2606 OID 30696)
-- Name: ambiente ambiente_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ambiente
    ADD CONSTRAINT ambiente_pkey PRIMARY KEY (amb_id);


--
-- TOC entry 4990 (class 2606 OID 30850)
-- Name: asignacion asignacion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asignacion
    ADD CONSTRAINT asignacion_pkey PRIMARY KEY (asig_id);


--
-- TOC entry 4964 (class 2606 OID 30602)
-- Name: auditoria_asignacion auditoria_asignacion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.auditoria_asignacion
    ADD CONSTRAINT auditoria_asignacion_pkey PRIMARY KEY (id_auditoria);


--
-- TOC entry 4962 (class 2606 OID 30582)
-- Name: centro_formacion centro_formacion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.centro_formacion
    ADD CONSTRAINT centro_formacion_pkey PRIMARY KEY (cent_id);


--
-- TOC entry 4986 (class 2606 OID 30808)
-- Name: competencia_horas_programa competencia_horas_programa_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.competencia_horas_programa
    ADD CONSTRAINT competencia_horas_programa_pkey PRIMARY KEY (prog_codigo, comp_id);


--
-- TOC entry 4974 (class 2606 OID 30680)
-- Name: competencia competencia_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.competencia
    ADD CONSTRAINT competencia_pkey PRIMARY KEY (comp_id);


--
-- TOC entry 4988 (class 2606 OID 30825)
-- Name: competxprograma competxprograma_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.competxprograma
    ADD CONSTRAINT competxprograma_pkey PRIMARY KEY (programa_prog_id, competencia_comp_id);


--
-- TOC entry 4978 (class 2606 OID 30714)
-- Name: coordinacion coordinacion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.coordinacion
    ADD CONSTRAINT coordinacion_pkey PRIMARY KEY (coord_id);


--
-- TOC entry 5000 (class 2606 OID 30961)
-- Name: detallexasignacion detallexasignacion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detallexasignacion
    ADD CONSTRAINT detallexasignacion_pkey PRIMARY KEY (detasig_id);


--
-- TOC entry 4992 (class 2606 OID 30884)
-- Name: fase_proyecto fase_proyecto_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fase_proyecto
    ADD CONSTRAINT fase_proyecto_pkey PRIMARY KEY (fase_id);


--
-- TOC entry 4982 (class 2606 OID 30759)
-- Name: ficha ficha_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ficha
    ADD CONSTRAINT ficha_pkey PRIMARY KEY (fich_id);


--
-- TOC entry 4994 (class 2606 OID 30896)
-- Name: ficha_proyecto ficha_proyecto_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ficha_proyecto
    ADD CONSTRAINT ficha_proyecto_pkey PRIMARY KEY (fich_fich_id, pf_pf_id);


--
-- TOC entry 4996 (class 2606 OID 30917)
-- Name: instru_competencia instru_competencia_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.instru_competencia
    ADD CONSTRAINT instru_competencia_pkey PRIMARY KEY (inscomp_id);


--
-- TOC entry 4972 (class 2606 OID 30664)
-- Name: instructor instructor_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.instructor
    ADD CONSTRAINT instructor_pkey PRIMARY KEY (numero_documento);


--
-- TOC entry 4980 (class 2606 OID 30735)
-- Name: programa programa_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.programa
    ADD CONSTRAINT programa_pkey PRIMARY KEY (prog_codigo);


--
-- TOC entry 4984 (class 2606 OID 30787)
-- Name: proyecto_formativo proyecto_formativo_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.proyecto_formativo
    ADD CONSTRAINT proyecto_formativo_pkey PRIMARY KEY (pf_id);


--
-- TOC entry 5002 (class 2606 OID 30973)
-- Name: rap_fase rap_fase_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rap_fase
    ADD CONSTRAINT rap_fase_pkey PRIMARY KEY (rap_rap_id, fase_fase_id);


--
-- TOC entry 4998 (class 2606 OID 30942)
-- Name: resultado_aprendizaje resultado_aprendizaje_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.resultado_aprendizaje
    ADD CONSTRAINT resultado_aprendizaje_pkey PRIMARY KEY (rap_id);


--
-- TOC entry 4966 (class 2606 OID 30611)
-- Name: sede sede_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sede
    ADD CONSTRAINT sede_pkey PRIMARY KEY (sede_id);


--
-- TOC entry 4968 (class 2606 OID 30625)
-- Name: titulo_programa titulo_programa_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.titulo_programa
    ADD CONSTRAINT titulo_programa_pkey PRIMARY KEY (titpro_id);


--
-- TOC entry 4970 (class 2606 OID 30643)
-- Name: usuario_coordinador usuario_coordinador_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuario_coordinador
    ADD CONSTRAINT usuario_coordinador_pkey PRIMARY KEY (numero_documento);


--
-- TOC entry 5035 (class 2620 OID 30985)
-- Name: asignacion trg_asignacion_audit; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_asignacion_audit AFTER INSERT OR DELETE OR UPDATE ON public.asignacion FOR EACH ROW EXECUTE FUNCTION public.func_auditoria_asignacion();


--
-- TOC entry 5018 (class 2606 OID 30814)
-- Name: competencia_horas_programa competencia_horas_programa_comp_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.competencia_horas_programa
    ADD CONSTRAINT competencia_horas_programa_comp_id_fkey FOREIGN KEY (comp_id) REFERENCES public.competencia(comp_id);


--
-- TOC entry 5019 (class 2606 OID 30809)
-- Name: competencia_horas_programa competencia_horas_programa_prog_codigo_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.competencia_horas_programa
    ADD CONSTRAINT competencia_horas_programa_prog_codigo_fkey FOREIGN KEY (prog_codigo) REFERENCES public.programa(prog_codigo);


--
-- TOC entry 5026 (class 2606 OID 30885)
-- Name: fase_proyecto fase_proyecto_pf_pf_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fase_proyecto
    ADD CONSTRAINT fase_proyecto_pf_pf_id_fkey FOREIGN KEY (pf_pf_id) REFERENCES public.proyecto_formativo(pf_id);


--
-- TOC entry 5027 (class 2606 OID 30897)
-- Name: ficha_proyecto ficha_proyecto_fich_fich_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ficha_proyecto
    ADD CONSTRAINT ficha_proyecto_fich_fich_id_fkey FOREIGN KEY (fich_fich_id) REFERENCES public.ficha(fich_id);


--
-- TOC entry 5028 (class 2606 OID 30902)
-- Name: ficha_proyecto ficha_proyecto_pf_pf_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ficha_proyecto
    ADD CONSTRAINT ficha_proyecto_pf_pf_id_fkey FOREIGN KEY (pf_pf_id) REFERENCES public.proyecto_formativo(pf_id);


--
-- TOC entry 5008 (class 2606 OID 30697)
-- Name: ambiente fk_ambiente_sede1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ambiente
    ADD CONSTRAINT fk_ambiente_sede1 FOREIGN KEY (sede_sede_id) REFERENCES public.sede(sede_id);


--
-- TOC entry 5022 (class 2606 OID 30866)
-- Name: asignacion fk_asignacion_ambiente1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asignacion
    ADD CONSTRAINT fk_asignacion_ambiente1 FOREIGN KEY (ambiente_amb_id) REFERENCES public.ambiente(amb_id);


--
-- TOC entry 5023 (class 2606 OID 30861)
-- Name: asignacion fk_asignacion_competencia1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asignacion
    ADD CONSTRAINT fk_asignacion_competencia1 FOREIGN KEY (competencia_comp_id) REFERENCES public.competencia(comp_id);


--
-- TOC entry 5024 (class 2606 OID 30856)
-- Name: asignacion fk_asignacion_ficha1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asignacion
    ADD CONSTRAINT fk_asignacion_ficha1 FOREIGN KEY (ficha_fich_id) REFERENCES public.ficha(fich_id);


--
-- TOC entry 5025 (class 2606 OID 30851)
-- Name: asignacion fk_asignacion_instructor1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asignacion
    ADD CONSTRAINT fk_asignacion_instructor1 FOREIGN KEY (instructor_inst_id) REFERENCES public.instructor(numero_documento);


--
-- TOC entry 5007 (class 2606 OID 30681)
-- Name: competencia fk_competencia_centro; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.competencia
    ADD CONSTRAINT fk_competencia_centro FOREIGN KEY (centro_formacion_cent_id) REFERENCES public.centro_formacion(cent_id);


--
-- TOC entry 5020 (class 2606 OID 30826)
-- Name: competxprograma fk_competxprograma_competencia1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.competxprograma
    ADD CONSTRAINT fk_competxprograma_competencia1 FOREIGN KEY (competencia_comp_id) REFERENCES public.competencia(comp_id);


--
-- TOC entry 5021 (class 2606 OID 30831)
-- Name: competxprograma fk_competxprograma_programa1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.competxprograma
    ADD CONSTRAINT fk_competxprograma_programa1 FOREIGN KEY (programa_prog_id) REFERENCES public.programa(prog_codigo);


--
-- TOC entry 5009 (class 2606 OID 30715)
-- Name: coordinacion fk_coordinacion_centro_formacion1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.coordinacion
    ADD CONSTRAINT fk_coordinacion_centro_formacion1 FOREIGN KEY (centro_formacion_cent_id) REFERENCES public.centro_formacion(cent_id);


--
-- TOC entry 5010 (class 2606 OID 30720)
-- Name: coordinacion fk_coordinador_actual; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.coordinacion
    ADD CONSTRAINT fk_coordinador_actual FOREIGN KEY (coordinador_actual) REFERENCES public.usuario_coordinador(numero_documento);


--
-- TOC entry 5032 (class 2606 OID 30962)
-- Name: detallexasignacion fk_detallexasignacion_asignacion1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detallexasignacion
    ADD CONSTRAINT fk_detallexasignacion_asignacion1 FOREIGN KEY (asignacion_asig_id) REFERENCES public.asignacion(asig_id);


--
-- TOC entry 5013 (class 2606 OID 30770)
-- Name: ficha fk_ficha_coordinacion; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ficha
    ADD CONSTRAINT fk_ficha_coordinacion FOREIGN KEY (coordinacion_coord_id) REFERENCES public.coordinacion(coord_id);


--
-- TOC entry 5014 (class 2606 OID 30765)
-- Name: ficha fk_ficha_instructor1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ficha
    ADD CONSTRAINT fk_ficha_instructor1 FOREIGN KEY (instructor_inst_id_lider) REFERENCES public.instructor(numero_documento);


--
-- TOC entry 5015 (class 2606 OID 30760)
-- Name: ficha fk_ficha_programa1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ficha
    ADD CONSTRAINT fk_ficha_programa1 FOREIGN KEY (programa_prog_id) REFERENCES public.programa(prog_codigo);


--
-- TOC entry 5029 (class 2606 OID 30923)
-- Name: instru_competencia fk_instru_comp_competxprog; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.instru_competencia
    ADD CONSTRAINT fk_instru_comp_competxprog FOREIGN KEY (competxprograma_programa_prog_id, competxprograma_competencia_comp_id) REFERENCES public.competxprograma(programa_prog_id, competencia_comp_id);


--
-- TOC entry 5030 (class 2606 OID 30918)
-- Name: instru_competencia fk_instru_competencia_instructor1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.instru_competencia
    ADD CONSTRAINT fk_instru_competencia_instructor1 FOREIGN KEY (instructor_inst_id) REFERENCES public.instructor(numero_documento);


--
-- TOC entry 5006 (class 2606 OID 30665)
-- Name: instructor fk_instructor_centro_formacion1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.instructor
    ADD CONSTRAINT fk_instructor_centro_formacion1 FOREIGN KEY (centro_formacion_cent_id) REFERENCES public.centro_formacion(cent_id);


--
-- TOC entry 5011 (class 2606 OID 30736)
-- Name: programa fk_programa_centro; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.programa
    ADD CONSTRAINT fk_programa_centro FOREIGN KEY (centro_formacion_cent_id) REFERENCES public.centro_formacion(cent_id);


--
-- TOC entry 5012 (class 2606 OID 30741)
-- Name: programa fk_programa_tipo_programa; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.programa
    ADD CONSTRAINT fk_programa_tipo_programa FOREIGN KEY (tit_programa_titpro_id) REFERENCES public.titulo_programa(titpro_id);


--
-- TOC entry 5031 (class 2606 OID 30943)
-- Name: resultado_aprendizaje fk_rap_competxprog; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.resultado_aprendizaje
    ADD CONSTRAINT fk_rap_competxprog FOREIGN KEY (competxprog_prog_id, competxprog_comp_id) REFERENCES public.competxprograma(programa_prog_id, competencia_comp_id);


--
-- TOC entry 5003 (class 2606 OID 30612)
-- Name: sede fk_sede_centro; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sede
    ADD CONSTRAINT fk_sede_centro FOREIGN KEY (centro_formacion_cent_id) REFERENCES public.centro_formacion(cent_id);


--
-- TOC entry 5004 (class 2606 OID 30626)
-- Name: titulo_programa fk_titulo_programa_centro; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.titulo_programa
    ADD CONSTRAINT fk_titulo_programa_centro FOREIGN KEY (centro_formacion_cent_id) REFERENCES public.centro_formacion(cent_id);


--
-- TOC entry 5005 (class 2606 OID 30644)
-- Name: usuario_coordinador fk_user_centro; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuario_coordinador
    ADD CONSTRAINT fk_user_centro FOREIGN KEY (centro_formacion_id) REFERENCES public.centro_formacion(cent_id);


--
-- TOC entry 5016 (class 2606 OID 30793)
-- Name: proyecto_formativo proyecto_formativo_centro_formacion_cent_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.proyecto_formativo
    ADD CONSTRAINT proyecto_formativo_centro_formacion_cent_id_fkey FOREIGN KEY (centro_formacion_cent_id) REFERENCES public.centro_formacion(cent_id);


--
-- TOC entry 5017 (class 2606 OID 30788)
-- Name: proyecto_formativo proyecto_formativo_programa_prog_codigo_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.proyecto_formativo
    ADD CONSTRAINT proyecto_formativo_programa_prog_codigo_fkey FOREIGN KEY (programa_prog_codigo) REFERENCES public.programa(prog_codigo);


--
-- TOC entry 5033 (class 2606 OID 30979)
-- Name: rap_fase rap_fase_fase_fase_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rap_fase
    ADD CONSTRAINT rap_fase_fase_fase_id_fkey FOREIGN KEY (fase_fase_id) REFERENCES public.fase_proyecto(fase_id);


--
-- TOC entry 5034 (class 2606 OID 30974)
-- Name: rap_fase rap_fase_rap_rap_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rap_fase
    ADD CONSTRAINT rap_fase_rap_rap_id_fkey FOREIGN KEY (rap_rap_id) REFERENCES public.resultado_aprendizaje(rap_id);


-- Completed on 2026-03-20 12:07:32

--
-- PostgreSQL database dump complete
--

