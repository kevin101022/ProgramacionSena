--
-- PostgreSQL database dump
--

\restrict WtI9cxfCp0HCk35tURew9XkSdGHcChf3NAQJAQdD9DsKpQyKaVfcnxOIa3UTJ5g

-- Dumped from database version 18.1
-- Dumped by pg_dump version 18.1

-- Started on 2026-03-02 22:26:53

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

--
-- TOC entry 2 (class 3079 OID 22313)
-- Name: pgcrypto; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS pgcrypto WITH SCHEMA public;


--
-- TOC entry 5190 (class 0 OID 0)
-- Dependencies: 2
-- Name: EXTENSION pgcrypto; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION pgcrypto IS 'cryptographic functions';


--
-- TOC entry 288 (class 1255 OID 22235)
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
-- TOC entry 222 (class 1259 OID 21959)
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
-- TOC entry 231 (class 1259 OID 22082)
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
-- TOC entry 230 (class 1259 OID 22081)
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
-- TOC entry 5191 (class 0 OID 0)
-- Dependencies: 230
-- Name: asignacion_asig_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.asignacion_asig_id_seq OWNED BY public.asignacion.asig_id;


--
-- TOC entry 237 (class 1259 OID 22217)
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
-- TOC entry 236 (class 1259 OID 22216)
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
-- TOC entry 5192 (class 0 OID 0)
-- Dependencies: 236
-- Name: auditoria_asignacion_id_auditoria_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.auditoria_asignacion_id_auditoria_seq OWNED BY public.auditoria_asignacion.id_auditoria;


--
-- TOC entry 220 (class 1259 OID 21944)
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
-- TOC entry 225 (class 1259 OID 21995)
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
-- TOC entry 226 (class 1259 OID 22004)
-- Name: competxprograma; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.competxprograma (
    programa_prog_id integer NOT NULL,
    competencia_comp_id integer NOT NULL
);


ALTER TABLE public.competxprograma OWNER TO postgres;

--
-- TOC entry 228 (class 1259 OID 22038)
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
-- TOC entry 239 (class 1259 OID 22400)
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
-- TOC entry 5193 (class 0 OID 0)
-- Dependencies: 239
-- Name: coordinacion_coord_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.coordinacion_coord_id_seq OWNED BY public.coordinacion.coord_id;


--
-- TOC entry 233 (class 1259 OID 22116)
-- Name: detallexasignacion; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.detallexasignacion (
    detasig_id integer NOT NULL,
    asignacion_asig_id integer NOT NULL,
    detasig_hora_ini time without time zone NOT NULL,
    detasig_hora_fin time without time zone NOT NULL
);


ALTER TABLE public.detallexasignacion OWNER TO postgres;

--
-- TOC entry 232 (class 1259 OID 22115)
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
-- TOC entry 5194 (class 0 OID 0)
-- Dependencies: 232
-- Name: detallexasignacion_detasig_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.detallexasignacion_detasig_id_seq OWNED BY public.detallexasignacion.detasig_id;


--
-- TOC entry 229 (class 1259 OID 22054)
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
-- TOC entry 235 (class 1259 OID 22132)
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
-- TOC entry 234 (class 1259 OID 22131)
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
-- TOC entry 5195 (class 0 OID 0)
-- Dependencies: 234
-- Name: instru_competencia_inscomp_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.instru_competencia_inscomp_id_seq OWNED BY public.instru_competencia.inscomp_id;


--
-- TOC entry 227 (class 1259 OID 22021)
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
-- TOC entry 224 (class 1259 OID 21981)
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
-- TOC entry 221 (class 1259 OID 21951)
-- Name: sede; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sede (
    sede_id integer NOT NULL,
    sede_nombre character varying(100) NOT NULL,
    centro_formacion_cent_id integer
);


ALTER TABLE public.sede OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 21974)
-- Name: titulo_programa; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.titulo_programa (
    titpro_id integer NOT NULL,
    titpro_nombre character varying(150) NOT NULL,
    centro_formacion_cent_id integer
);


ALTER TABLE public.titulo_programa OWNER TO postgres;

--
-- TOC entry 238 (class 1259 OID 22384)
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
-- TOC entry 4960 (class 2604 OID 22085)
-- Name: asignacion asig_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asignacion ALTER COLUMN asig_id SET DEFAULT nextval('public.asignacion_asig_id_seq'::regclass);


--
-- TOC entry 4963 (class 2604 OID 22220)
-- Name: auditoria_asignacion id_auditoria; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.auditoria_asignacion ALTER COLUMN id_auditoria SET DEFAULT nextval('public.auditoria_asignacion_id_auditoria_seq'::regclass);


--
-- TOC entry 4958 (class 2604 OID 22401)
-- Name: coordinacion coord_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.coordinacion ALTER COLUMN coord_id SET DEFAULT nextval('public.coordinacion_coord_id_seq'::regclass);


--
-- TOC entry 4961 (class 2604 OID 22119)
-- Name: detallexasignacion detasig_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detallexasignacion ALTER COLUMN detasig_id SET DEFAULT nextval('public.detallexasignacion_detasig_id_seq'::regclass);


--
-- TOC entry 4962 (class 2604 OID 22135)
-- Name: instru_competencia inscomp_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.instru_competencia ALTER COLUMN inscomp_id SET DEFAULT nextval('public.instru_competencia_inscomp_id_seq'::regclass);


--
-- TOC entry 5167 (class 0 OID 21959)
-- Dependencies: 222
-- Data for Name: ambiente; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.ambiente (amb_id, amb_nombre, tipo_ambiente, sede_sede_id) FROM stdin;
201	ADSO	Especializado	7
\.


--
-- TOC entry 5176 (class 0 OID 22082)
-- Dependencies: 231
-- Data for Name: asignacion; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.asignacion (asig_id, instructor_inst_id, asig_fecha_ini, asig_fecha_fin, ficha_fich_id, ambiente_amb_id, competencia_comp_id) FROM stdin;
16	76758463	2026-03-02	2026-04-02	3115418	201	2
\.


--
-- TOC entry 5182 (class 0 OID 22217)
-- Dependencies: 237
-- Data for Name: auditoria_asignacion; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.auditoria_asignacion (id_auditoria, instructor_inst_id, asig_fecha_ini, asig_fecha_fin, ficha_fich_id, ambiente_amb_id, competencia_comp_id, asig_id, fecha_hora, documento_usuario_accion, correo_usuario, tipo_accion, nombre_usuario_accion) FROM stdin;
1	76758463	2026-03-02	2026-04-02	3115418	201	2	16	2026-03-01 22:55:12.529625	0	Sistema	INSERT	\N
\.


--
-- TOC entry 5165 (class 0 OID 21944)
-- Dependencies: 220
-- Data for Name: centro_formacion; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.centro_formacion (cent_id, cent_nombre, cent_correo, cent_password) FROM stdin;
1	CIES	ciessena@gmail.com	$2a$06$WEPuDhY/sBsC/xJL29.a8uUtO5wAUkaeMY5laLeXDmtqwAUvs7JuW
2	CEDRUM	cedrumsena@gmail.com	$2a$06$8yNElBr8t38XH049YZ91WeMHmmXTBt6Wc6yYMnoutN44foyfFoOza
\.


--
-- TOC entry 5170 (class 0 OID 21995)
-- Dependencies: 225
-- Data for Name: competencia; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.competencia (comp_id, comp_nombre_corto, comp_horas, comp_nombre_unidad_competencia, centro_formacion_cent_id) FROM stdin;
1	Inglés	48	Interactuar con otros en idioma extranjero según estipulaciones del Marco Común Europeo de Referencia para Idiomas	1
2	Matemáticas	48	Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales y sociales.	1
\.


--
-- TOC entry 5171 (class 0 OID 22004)
-- Dependencies: 226
-- Data for Name: competxprograma; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.competxprograma (programa_prog_id, competencia_comp_id) FROM stdin;
223345	1
223345	2
54654	1
54654	2
\.


--
-- TOC entry 5173 (class 0 OID 22038)
-- Dependencies: 228
-- Data for Name: coordinacion; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.coordinacion (coord_descripcion, centro_formacion_cent_id, coord_id, estado, coordinador_actual) FROM stdin;
Industria y Comercio	1	3	1	2345678643
TIC	1	2	1	\N
\.


--
-- TOC entry 5178 (class 0 OID 22116)
-- Dependencies: 233
-- Data for Name: detallexasignacion; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.detallexasignacion (detasig_id, asignacion_asig_id, detasig_hora_ini, detasig_hora_fin) FROM stdin;
2	16	06:00:00	09:00:00
\.


--
-- TOC entry 5174 (class 0 OID 22054)
-- Dependencies: 229
-- Data for Name: ficha; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.ficha (fich_id, programa_prog_id, instructor_inst_id_lider, fich_jornada, coordinacion_coord_id, fich_fecha_ini_lectiva, fich_fecha_fin_lectiva) FROM stdin;
3115418	223345	1092834765	Mañana	2	2026-03-01	2027-12-13
\.


--
-- TOC entry 5180 (class 0 OID 22132)
-- Dependencies: 235
-- Data for Name: instru_competencia; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.instru_competencia (inscomp_id, instructor_inst_id, competxprograma_programa_prog_id, competxprograma_competencia_comp_id, inscomp_vigencia) FROM stdin;
1	1092834765	223345	1	2027-12-31
10	76758463	223345	1	2026-12-31
11	76758463	54654	1	2026-12-31
12	76758463	223345	2	2026-12-31
13	76758463	54654	2	2026-12-31
16	5678657856856	223345	1	2026-12-31
17	5678657856856	54654	1	2026-12-31
\.


--
-- TOC entry 5172 (class 0 OID 22021)
-- Dependencies: 227
-- Data for Name: instructor; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.instructor (inst_nombres, inst_apellidos, inst_correo, inst_telefono, centro_formacion_cent_id, inst_password, numero_documento, estado) FROM stdin;
Cristian	Chaustre	chaustre@gmail.com	1234556789	1	Sena123*	1092834765	1
Breyner	Pena	breygud@gmail.com	978202439875	1	$2y$10$DjPXpiACiItvlN1Tx4gBoex5BE3IR.h.nLx0qJqTsY.7SsgVyHVhe	76758463	1
Carlos	Pietro	pietro@gmail.com	3239284393	1	$2y$10$Di2c2fd1w.zzFBiuSzmi7.s/WPmvW7dft8y.ovhtdr/vkH5/LFBTK	5678657856856	1
\.


--
-- TOC entry 5169 (class 0 OID 21981)
-- Dependencies: 224
-- Data for Name: programa; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.programa (prog_codigo, prog_denominacion, tit_programa_titpro_id, prog_tipo, centro_formacion_cent_id) FROM stdin;
223345	Análisis y Desarrollo de Software	2	Tecnólogo	1
54654	Gestión contable	3	Tecnólogo	1
\.


--
-- TOC entry 5166 (class 0 OID 21951)
-- Dependencies: 221
-- Data for Name: sede; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sede (sede_id, sede_nombre, centro_formacion_cent_id) FROM stdin;
1	Calzado - manufactura, marroquinería y calzado.	1
2	Comuneros - moda, confección, maderas y multimedia	1
4	Tecnoparque - proyectos de innovación	1
5	Villa del Rosario - Comercio, logística y servicios fronterizo	1
6	Patios - Atención en servicios y comercio para el área metropolitana	1
7	Biblioteca - servicio a todos los aprendices	1
3	Industria - áreas técnicas, de mantenimiento, mecánicas e industriales	1
8	Pescadero - CIES	1
\.


--
-- TOC entry 5168 (class 0 OID 21974)
-- Dependencies: 223
-- Data for Name: titulo_programa; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.titulo_programa (titpro_id, titpro_nombre, centro_formacion_cent_id) FROM stdin;
2	Tecnólogo en Análisis y Desarrollo de Software	1
3	Tecnólogo en Gestión Contable	1
\.


--
-- TOC entry 5183 (class 0 OID 22384)
-- Dependencies: 238
-- Data for Name: usuario_coordinador; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.usuario_coordinador (numero_documento, coord_nombre_coordinador, coord_correo, coord_password, estado, centro_formacion_id) FROM stdin;
2345678643	Juan Diego Rubio	chuni@gmail.com	$2y$10$VxwL4vZzXNwk5HV6qtVr7OtoQvTe25QxMI/0w7en8IPZ83kqOa74q	1	1
234567	Gustavo Petro	petrosky@gmail.com	$2y$10$6CeswVyXRk2OZqoCzhHWKusAZn.34mXwJF5xz9xKpnjr3YZBC3.dy	1	1
\.


--
-- TOC entry 5196 (class 0 OID 0)
-- Dependencies: 230
-- Name: asignacion_asig_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.asignacion_asig_id_seq', 16, true);


--
-- TOC entry 5197 (class 0 OID 0)
-- Dependencies: 236
-- Name: auditoria_asignacion_id_auditoria_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.auditoria_asignacion_id_auditoria_seq', 1, true);


--
-- TOC entry 5198 (class 0 OID 0)
-- Dependencies: 239
-- Name: coordinacion_coord_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.coordinacion_coord_id_seq', 3, true);


--
-- TOC entry 5199 (class 0 OID 0)
-- Dependencies: 232
-- Name: detallexasignacion_detasig_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.detallexasignacion_detasig_id_seq', 2, true);


--
-- TOC entry 5200 (class 0 OID 0)
-- Dependencies: 234
-- Name: instru_competencia_inscomp_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.instru_competencia_inscomp_id_seq', 17, true);


--
-- TOC entry 4971 (class 2606 OID 21968)
-- Name: ambiente ambiente_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ambiente
    ADD CONSTRAINT ambiente_pkey PRIMARY KEY (amb_id);


--
-- TOC entry 4987 (class 2606 OID 22094)
-- Name: asignacion asignacion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asignacion
    ADD CONSTRAINT asignacion_pkey PRIMARY KEY (asig_id);


--
-- TOC entry 4993 (class 2606 OID 22234)
-- Name: auditoria_asignacion auditoria_asignacion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.auditoria_asignacion
    ADD CONSTRAINT auditoria_asignacion_pkey PRIMARY KEY (id_auditoria);


--
-- TOC entry 4967 (class 2606 OID 21950)
-- Name: centro_formacion centro_formacion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.centro_formacion
    ADD CONSTRAINT centro_formacion_pkey PRIMARY KEY (cent_id);


--
-- TOC entry 4977 (class 2606 OID 22003)
-- Name: competencia competencia_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.competencia
    ADD CONSTRAINT competencia_pkey PRIMARY KEY (comp_id);


--
-- TOC entry 4979 (class 2606 OID 22010)
-- Name: competxprograma competxprograma_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.competxprograma
    ADD CONSTRAINT competxprograma_pkey PRIMARY KEY (programa_prog_id, competencia_comp_id);


--
-- TOC entry 4983 (class 2606 OID 22404)
-- Name: coordinacion coordinacion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.coordinacion
    ADD CONSTRAINT coordinacion_pkey PRIMARY KEY (coord_id);


--
-- TOC entry 4989 (class 2606 OID 22125)
-- Name: detallexasignacion detallexasignacion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detallexasignacion
    ADD CONSTRAINT detallexasignacion_pkey PRIMARY KEY (detasig_id);


--
-- TOC entry 4985 (class 2606 OID 22065)
-- Name: ficha ficha_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ficha
    ADD CONSTRAINT ficha_pkey PRIMARY KEY (fich_id);


--
-- TOC entry 4991 (class 2606 OID 22142)
-- Name: instru_competencia instru_competencia_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.instru_competencia
    ADD CONSTRAINT instru_competencia_pkey PRIMARY KEY (inscomp_id);


--
-- TOC entry 4981 (class 2606 OID 22174)
-- Name: instructor instructor_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.instructor
    ADD CONSTRAINT instructor_pkey PRIMARY KEY (numero_documento);


--
-- TOC entry 4975 (class 2606 OID 21989)
-- Name: programa programa_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.programa
    ADD CONSTRAINT programa_pkey PRIMARY KEY (prog_codigo);


--
-- TOC entry 4969 (class 2606 OID 21958)
-- Name: sede sede_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sede
    ADD CONSTRAINT sede_pkey PRIMARY KEY (sede_id);


--
-- TOC entry 4973 (class 2606 OID 21980)
-- Name: titulo_programa titulo_programa_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.titulo_programa
    ADD CONSTRAINT titulo_programa_pkey PRIMARY KEY (titpro_id);


--
-- TOC entry 4995 (class 2606 OID 22394)
-- Name: usuario_coordinador usuario_coordinador_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuario_coordinador
    ADD CONSTRAINT usuario_coordinador_pkey PRIMARY KEY (numero_documento);


--
-- TOC entry 5017 (class 2620 OID 22236)
-- Name: asignacion trg_asignacion_audit; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_asignacion_audit AFTER INSERT OR DELETE OR UPDATE ON public.asignacion FOR EACH ROW EXECUTE FUNCTION public.func_auditoria_asignacion();


--
-- TOC entry 4996 (class 2606 OID 21969)
-- Name: ambiente fk_ambiente_sede1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ambiente
    ADD CONSTRAINT fk_ambiente_sede1 FOREIGN KEY (sede_sede_id) REFERENCES public.sede(sede_id);


--
-- TOC entry 5009 (class 2606 OID 22095)
-- Name: asignacion fk_asignacion_ambiente1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asignacion
    ADD CONSTRAINT fk_asignacion_ambiente1 FOREIGN KEY (ambiente_amb_id) REFERENCES public.ambiente(amb_id);


--
-- TOC entry 5010 (class 2606 OID 22100)
-- Name: asignacion fk_asignacion_competencia1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asignacion
    ADD CONSTRAINT fk_asignacion_competencia1 FOREIGN KEY (competencia_comp_id) REFERENCES public.competencia(comp_id);


--
-- TOC entry 5011 (class 2606 OID 22105)
-- Name: asignacion fk_asignacion_ficha1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asignacion
    ADD CONSTRAINT fk_asignacion_ficha1 FOREIGN KEY (ficha_fich_id) REFERENCES public.ficha(fich_id);


--
-- TOC entry 5012 (class 2606 OID 22176)
-- Name: asignacion fk_asignacion_instructor1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asignacion
    ADD CONSTRAINT fk_asignacion_instructor1 FOREIGN KEY (instructor_inst_id) REFERENCES public.instructor(numero_documento);


--
-- TOC entry 5000 (class 2606 OID 22366)
-- Name: competencia fk_competencia_centro; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.competencia
    ADD CONSTRAINT fk_competencia_centro FOREIGN KEY (centro_formacion_cent_id) REFERENCES public.centro_formacion(cent_id);


--
-- TOC entry 5001 (class 2606 OID 22011)
-- Name: competxprograma fk_competxprograma_competencia1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.competxprograma
    ADD CONSTRAINT fk_competxprograma_competencia1 FOREIGN KEY (competencia_comp_id) REFERENCES public.competencia(comp_id);


--
-- TOC entry 5002 (class 2606 OID 22016)
-- Name: competxprograma fk_competxprograma_programa1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.competxprograma
    ADD CONSTRAINT fk_competxprograma_programa1 FOREIGN KEY (programa_prog_id) REFERENCES public.programa(prog_codigo);


--
-- TOC entry 5004 (class 2606 OID 22049)
-- Name: coordinacion fk_coordinacion_centro_formacion1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.coordinacion
    ADD CONSTRAINT fk_coordinacion_centro_formacion1 FOREIGN KEY (centro_formacion_cent_id) REFERENCES public.centro_formacion(cent_id);


--
-- TOC entry 5005 (class 2606 OID 22411)
-- Name: coordinacion fk_coordinador_actual; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.coordinacion
    ADD CONSTRAINT fk_coordinador_actual FOREIGN KEY (coordinador_actual) REFERENCES public.usuario_coordinador(numero_documento) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 5013 (class 2606 OID 22211)
-- Name: detallexasignacion fk_detallexasignacion_asignacion1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detallexasignacion
    ADD CONSTRAINT fk_detallexasignacion_asignacion1 FOREIGN KEY (asignacion_asig_id) REFERENCES public.asignacion(asig_id);


--
-- TOC entry 5006 (class 2606 OID 22421)
-- Name: ficha fk_ficha_coordinacion; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ficha
    ADD CONSTRAINT fk_ficha_coordinacion FOREIGN KEY (coordinacion_coord_id) REFERENCES public.coordinacion(coord_id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 5007 (class 2606 OID 22187)
-- Name: ficha fk_ficha_instructor1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ficha
    ADD CONSTRAINT fk_ficha_instructor1 FOREIGN KEY (instructor_inst_id_lider) REFERENCES public.instructor(numero_documento);


--
-- TOC entry 5008 (class 2606 OID 22076)
-- Name: ficha fk_ficha_programa1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ficha
    ADD CONSTRAINT fk_ficha_programa1 FOREIGN KEY (programa_prog_id) REFERENCES public.programa(prog_codigo);


--
-- TOC entry 5014 (class 2606 OID 22143)
-- Name: instru_competencia fk_instru_competencia_competxprograma1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.instru_competencia
    ADD CONSTRAINT fk_instru_competencia_competxprograma1 FOREIGN KEY (competxprograma_programa_prog_id, competxprograma_competencia_comp_id) REFERENCES public.competxprograma(programa_prog_id, competencia_comp_id);


--
-- TOC entry 5015 (class 2606 OID 22202)
-- Name: instru_competencia fk_instru_competencia_instructor1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.instru_competencia
    ADD CONSTRAINT fk_instru_competencia_instructor1 FOREIGN KEY (instructor_inst_id) REFERENCES public.instructor(numero_documento);


--
-- TOC entry 5003 (class 2606 OID 22033)
-- Name: instructor fk_instructor_centro_formacion1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.instructor
    ADD CONSTRAINT fk_instructor_centro_formacion1 FOREIGN KEY (centro_formacion_cent_id) REFERENCES public.centro_formacion(cent_id);


--
-- TOC entry 4998 (class 2606 OID 22361)
-- Name: programa fk_programa_centro; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.programa
    ADD CONSTRAINT fk_programa_centro FOREIGN KEY (centro_formacion_cent_id) REFERENCES public.centro_formacion(cent_id);


--
-- TOC entry 4999 (class 2606 OID 21990)
-- Name: programa fk_programa_tipo_programa; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.programa
    ADD CONSTRAINT fk_programa_tipo_programa FOREIGN KEY (tit_programa_titpro_id) REFERENCES public.titulo_programa(titpro_id);


--
-- TOC entry 4997 (class 2606 OID 22371)
-- Name: titulo_programa fk_titulo_programa_centro; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.titulo_programa
    ADD CONSTRAINT fk_titulo_programa_centro FOREIGN KEY (centro_formacion_cent_id) REFERENCES public.centro_formacion(cent_id);


--
-- TOC entry 5016 (class 2606 OID 22395)
-- Name: usuario_coordinador fk_user_centro; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuario_coordinador
    ADD CONSTRAINT fk_user_centro FOREIGN KEY (centro_formacion_id) REFERENCES public.centro_formacion(cent_id) ON UPDATE CASCADE ON DELETE SET NULL;


-- Completed on 2026-03-02 22:26:53

--
-- PostgreSQL database dump complete
--

\unrestrict WtI9cxfCp0HCk35tURew9XkSdGHcChf3NAQJAQdD9DsKpQyKaVfcnxOIa3UTJ5g

