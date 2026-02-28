--
-- PostgreSQL database dump
--

\restrict UsCgKKWbOEcryS9xHhlf3NIh6t8Wpl2k2UzWz5bXAufuGmeUfy1V99fBeWsceEb

-- Dumped from database version 18.1
-- Dumped by pg_dump version 18.1

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
-- Name: pgcrypto; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS pgcrypto WITH SCHEMA public;


--
-- Name: EXTENSION pgcrypto; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION pgcrypto IS 'cryptographic functions';


--
-- Name: func_auditoria_asignacion(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.func_auditoria_asignacion() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_documento BIGINT;
    v_correo VARCHAR;
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

    IF v_documento IS NULL THEN v_documento := 0; END IF;
    IF v_correo IS NULL OR v_correo = '' THEN v_correo := 'Sistema'; END IF;

    IF TG_OP = 'INSERT' THEN
        INSERT INTO auditoria_asignacion (
            instructor_inst_id, asig_fecha_ini, asig_fecha_fin, ficha_fich_id, ambiente_amb_id, competencia_comp_id, asig_id,
            documento_usuario_accion, correo_usuario, tipo_accion
        ) VALUES (
            NEW.instructor_inst_id, NEW.asig_fecha_ini, NEW.asig_fecha_fin, NEW.ficha_fich_id, NEW.ambiente_amb_id, NEW.competencia_comp_id, NEW.asig_id,
            v_documento, v_correo, 'INSERT'
        );
        RETURN NEW;
    ELSIF TG_OP = 'UPDATE' THEN
        INSERT INTO auditoria_asignacion (
            instructor_inst_id, asig_fecha_ini, asig_fecha_fin, ficha_fich_id, ambiente_amb_id, competencia_comp_id, asig_id,
            documento_usuario_accion, correo_usuario, tipo_accion
        ) VALUES (
            NEW.instructor_inst_id, NEW.asig_fecha_ini, NEW.asig_fecha_fin, NEW.ficha_fich_id, NEW.ambiente_amb_id, NEW.competencia_comp_id, NEW.asig_id,
            v_documento, v_correo, 'UPDATE'
        );
        RETURN NEW;
    ELSIF TG_OP = 'DELETE' THEN
        INSERT INTO auditoria_asignacion (
            instructor_inst_id, asig_fecha_ini, asig_fecha_fin, ficha_fich_id, ambiente_amb_id, competencia_comp_id, asig_id,
            documento_usuario_accion, correo_usuario, tipo_accion
        ) VALUES (
            OLD.instructor_inst_id, OLD.asig_fecha_ini, OLD.asig_fecha_fin, OLD.ficha_fich_id, OLD.ambiente_amb_id, OLD.competencia_comp_id, OLD.asig_id,
            v_documento, v_correo, 'DELETE'
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
-- Name: asignacion_asig_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.asignacion_asig_id_seq OWNED BY public.asignacion.asig_id;


--
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
    tipo_accion character varying(10) NOT NULL
);


ALTER TABLE public.auditoria_asignacion OWNER TO postgres;

--
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
-- Name: auditoria_asignacion_id_auditoria_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.auditoria_asignacion_id_auditoria_seq OWNED BY public.auditoria_asignacion.id_auditoria;


--
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
-- Name: competencia; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.competencia (
    comp_id integer NOT NULL,
    comp_nombre_corto character varying(30) NOT NULL,
    comp_horas integer NOT NULL,
    comp_nombre_unidad_competencia character varying(150) NOT NULL
);


ALTER TABLE public.competencia OWNER TO postgres;

--
-- Name: competxprograma; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.competxprograma (
    programa_prog_id integer NOT NULL,
    competencia_comp_id integer NOT NULL
);


ALTER TABLE public.competxprograma OWNER TO postgres;

--
-- Name: coordinacion; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.coordinacion (
    coord_descripcion character varying(45) NOT NULL,
    centro_formacion_cent_id integer NOT NULL,
    coord_nombre_coordinador character varying(45) NOT NULL,
    coord_correo character varying(45) NOT NULL,
    coord_password character varying(150) NOT NULL,
    numero_documento bigint NOT NULL,
    estado smallint DEFAULT 1 NOT NULL
);


ALTER TABLE public.coordinacion OWNER TO postgres;

--
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
-- Name: detallexasignacion_detasig_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.detallexasignacion_detasig_id_seq OWNED BY public.detallexasignacion.detasig_id;


--
-- Name: ficha; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.ficha (
    fich_id integer NOT NULL,
    programa_prog_id integer NOT NULL,
    instructor_inst_id_lider bigint NOT NULL,
    fich_jornada character varying(20) NOT NULL,
    coordinacion_coord_id bigint NOT NULL,
    fich_fecha_ini_lectiva date NOT NULL,
    fich_fecha_fin_lectiva date NOT NULL
);


ALTER TABLE public.ficha OWNER TO postgres;

--
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
-- Name: instru_competencia_inscomp_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.instru_competencia_inscomp_id_seq OWNED BY public.instru_competencia.inscomp_id;


--
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
-- Name: programa; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.programa (
    prog_codigo integer NOT NULL,
    prog_denominacion character varying(100) NOT NULL,
    tit_programa_titpro_id integer NOT NULL,
    prog_tipo character varying(30) NOT NULL
);


ALTER TABLE public.programa OWNER TO postgres;

--
-- Name: sede; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sede (
    sede_id integer NOT NULL,
    sede_nombre character varying(45) NOT NULL,
    centro_formacion_cent_id integer
);


ALTER TABLE public.sede OWNER TO postgres;

--
-- Name: titulo_programa; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.titulo_programa (
    titpro_id integer NOT NULL,
    titpro_nombre character varying(45) NOT NULL
);


ALTER TABLE public.titulo_programa OWNER TO postgres;

--
-- Name: asignacion asig_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asignacion ALTER COLUMN asig_id SET DEFAULT nextval('public.asignacion_asig_id_seq'::regclass);


--
-- Name: auditoria_asignacion id_auditoria; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.auditoria_asignacion ALTER COLUMN id_auditoria SET DEFAULT nextval('public.auditoria_asignacion_id_auditoria_seq'::regclass);


--
-- Name: detallexasignacion detasig_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detallexasignacion ALTER COLUMN detasig_id SET DEFAULT nextval('public.detallexasignacion_detasig_id_seq'::regclass);


--
-- Name: instru_competencia inscomp_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.instru_competencia ALTER COLUMN inscomp_id SET DEFAULT nextval('public.instru_competencia_inscomp_id_seq'::regclass);


--
-- Name: ambiente ambiente_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ambiente
    ADD CONSTRAINT ambiente_pkey PRIMARY KEY (amb_id);


--
-- Name: asignacion asignacion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asignacion
    ADD CONSTRAINT asignacion_pkey PRIMARY KEY (asig_id);


--
-- Name: auditoria_asignacion auditoria_asignacion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.auditoria_asignacion
    ADD CONSTRAINT auditoria_asignacion_pkey PRIMARY KEY (id_auditoria);


--
-- Name: centro_formacion centro_formacion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.centro_formacion
    ADD CONSTRAINT centro_formacion_pkey PRIMARY KEY (cent_id);


--
-- Name: competencia competencia_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.competencia
    ADD CONSTRAINT competencia_pkey PRIMARY KEY (comp_id);


--
-- Name: competxprograma competxprograma_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.competxprograma
    ADD CONSTRAINT competxprograma_pkey PRIMARY KEY (programa_prog_id, competencia_comp_id);


--
-- Name: coordinacion coordinacion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.coordinacion
    ADD CONSTRAINT coordinacion_pkey PRIMARY KEY (numero_documento);


--
-- Name: detallexasignacion detallexasignacion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detallexasignacion
    ADD CONSTRAINT detallexasignacion_pkey PRIMARY KEY (detasig_id);


--
-- Name: ficha ficha_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ficha
    ADD CONSTRAINT ficha_pkey PRIMARY KEY (fich_id);


--
-- Name: instru_competencia instru_competencia_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.instru_competencia
    ADD CONSTRAINT instru_competencia_pkey PRIMARY KEY (inscomp_id);


--
-- Name: instructor instructor_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.instructor
    ADD CONSTRAINT instructor_pkey PRIMARY KEY (numero_documento);


--
-- Name: programa programa_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.programa
    ADD CONSTRAINT programa_pkey PRIMARY KEY (prog_codigo);


--
-- Name: sede sede_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sede
    ADD CONSTRAINT sede_pkey PRIMARY KEY (sede_id);


--
-- Name: titulo_programa titulo_programa_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.titulo_programa
    ADD CONSTRAINT titulo_programa_pkey PRIMARY KEY (titpro_id);


--
-- Name: asignacion trg_asignacion_audit; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_asignacion_audit AFTER INSERT OR DELETE OR UPDATE ON public.asignacion FOR EACH ROW EXECUTE FUNCTION public.func_auditoria_asignacion();


--
-- Name: ambiente fk_ambiente_sede1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ambiente
    ADD CONSTRAINT fk_ambiente_sede1 FOREIGN KEY (sede_sede_id) REFERENCES public.sede(sede_id);


--
-- Name: asignacion fk_asignacion_ambiente1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asignacion
    ADD CONSTRAINT fk_asignacion_ambiente1 FOREIGN KEY (ambiente_amb_id) REFERENCES public.ambiente(amb_id);


--
-- Name: asignacion fk_asignacion_competencia1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asignacion
    ADD CONSTRAINT fk_asignacion_competencia1 FOREIGN KEY (competencia_comp_id) REFERENCES public.competencia(comp_id);


--
-- Name: asignacion fk_asignacion_ficha1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asignacion
    ADD CONSTRAINT fk_asignacion_ficha1 FOREIGN KEY (ficha_fich_id) REFERENCES public.ficha(fich_id);


--
-- Name: asignacion fk_asignacion_instructor1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asignacion
    ADD CONSTRAINT fk_asignacion_instructor1 FOREIGN KEY (instructor_inst_id) REFERENCES public.instructor(numero_documento);


--
-- Name: competxprograma fk_competxprograma_competencia1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.competxprograma
    ADD CONSTRAINT fk_competxprograma_competencia1 FOREIGN KEY (competencia_comp_id) REFERENCES public.competencia(comp_id);


--
-- Name: competxprograma fk_competxprograma_programa1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.competxprograma
    ADD CONSTRAINT fk_competxprograma_programa1 FOREIGN KEY (programa_prog_id) REFERENCES public.programa(prog_codigo);


--
-- Name: coordinacion fk_coordinacion_centro_formacion1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.coordinacion
    ADD CONSTRAINT fk_coordinacion_centro_formacion1 FOREIGN KEY (centro_formacion_cent_id) REFERENCES public.centro_formacion(cent_id);


--
-- Name: detallexasignacion fk_detallexasignacion_asignacion1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detallexasignacion
    ADD CONSTRAINT fk_detallexasignacion_asignacion1 FOREIGN KEY (asignacion_asig_id) REFERENCES public.asignacion(asig_id);


--
-- Name: ficha fk_ficha_coordinacion1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ficha
    ADD CONSTRAINT fk_ficha_coordinacion1 FOREIGN KEY (coordinacion_coord_id) REFERENCES public.coordinacion(numero_documento);


--
-- Name: ficha fk_ficha_instructor1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ficha
    ADD CONSTRAINT fk_ficha_instructor1 FOREIGN KEY (instructor_inst_id_lider) REFERENCES public.instructor(numero_documento);


--
-- Name: ficha fk_ficha_programa1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ficha
    ADD CONSTRAINT fk_ficha_programa1 FOREIGN KEY (programa_prog_id) REFERENCES public.programa(prog_codigo);


--
-- Name: instru_competencia fk_instru_competencia_competxprograma1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.instru_competencia
    ADD CONSTRAINT fk_instru_competencia_competxprograma1 FOREIGN KEY (competxprograma_programa_prog_id, competxprograma_competencia_comp_id) REFERENCES public.competxprograma(programa_prog_id, competencia_comp_id);


--
-- Name: instru_competencia fk_instru_competencia_instructor1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.instru_competencia
    ADD CONSTRAINT fk_instru_competencia_instructor1 FOREIGN KEY (instructor_inst_id) REFERENCES public.instructor(numero_documento);


--
-- Name: instructor fk_instructor_centro_formacion1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.instructor
    ADD CONSTRAINT fk_instructor_centro_formacion1 FOREIGN KEY (centro_formacion_cent_id) REFERENCES public.centro_formacion(cent_id);


--
-- Name: programa fk_programa_tipo_programa; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.programa
    ADD CONSTRAINT fk_programa_tipo_programa FOREIGN KEY (tit_programa_titpro_id) REFERENCES public.titulo_programa(titpro_id);


--
-- PostgreSQL database dump complete
--

\unrestrict UsCgKKWbOEcryS9xHhlf3NIh6t8Wpl2k2UzWz5bXAufuGmeUfy1V99fBeWsceEb

