-- DROPS DE SEGURIDAD
DROP TRIGGER IF EXISTS trg_rap_audit ON public.resultado_aprendizaje;
DROP FUNCTION IF EXISTS public.func_auditoria_rap() CASCADE;
DROP TABLE IF EXISTS public.auditoria_resultado_aprendizaje CASCADE;
DROP SEQUENCE IF EXISTS public.auditoria_rap_seq CASCADE;

DROP TRIGGER IF EXISTS trg_pf_audit ON public.proyecto_formativo;
DROP FUNCTION IF EXISTS public.func_auditoria_pf() CASCADE;
DROP TABLE IF EXISTS public.auditoria_proyecto_formativo CASCADE;
DROP SEQUENCE IF EXISTS public.auditoria_pf_seq CASCADE;

-- ==========================================
-- AUDITORIA RESULTADOS DE APRENDIZAJE
-- ==========================================
CREATE SEQUENCE public.auditoria_rap_seq;
CREATE TABLE public.auditoria_resultado_aprendizaje (
    id_auditoria integer DEFAULT nextval('public.auditoria_rap_seq') PRIMARY KEY,
    rap_id integer,
    rap_codigo character varying,
    rap_horas integer,
    programa_prog_id integer,
    competencia_comp_id integer,
    
    fecha_hora timestamp DEFAULT CURRENT_TIMESTAMP,
    documento_usuario_accion bigint,
    correo_usuario character varying,
    tipo_accion character varying,
    nombre_usuario_accion character varying
);

CREATE OR REPLACE FUNCTION public.func_auditoria_rap() RETURNS trigger AS $$
DECLARE
    v_doc bigint;
    v_correo varchar;
    v_nombre varchar;
BEGIN
    v_doc := NULLIF(current_setting('myapp.documento_usuario', true), '')::bigint;
    IF v_doc IS NULL THEN v_doc := 0; END IF;
    
    v_correo := current_setting('myapp.correo_usuario', true);
    IF v_correo IS NULL THEN v_correo := 'sistema@admin.com'; END IF;
    
    v_nombre := current_setting('myapp.nombre_usuario', true);
    IF v_nombre IS NULL THEN v_nombre := 'Sistema'; END IF;

    IF (TG_OP = 'INSERT') THEN
        INSERT INTO public.auditoria_resultado_aprendizaje (rap_id, rap_codigo, rap_horas, programa_prog_id, competencia_comp_id, tipo_accion, documento_usuario_accion, correo_usuario, nombre_usuario_accion)
        VALUES (NEW.rap_id, NEW.rap_codigo, NEW.rap_horas, NEW.programa_prog_id, NEW.competencia_comp_id, 'INSERT', v_doc, v_correo, v_nombre);
    ELSIF (TG_OP = 'UPDATE') THEN
        INSERT INTO public.auditoria_resultado_aprendizaje (rap_id, rap_codigo, rap_horas, programa_prog_id, competencia_comp_id, tipo_accion, documento_usuario_accion, correo_usuario, nombre_usuario_accion)
        VALUES (NEW.rap_id, NEW.rap_codigo, NEW.rap_horas, NEW.programa_prog_id, NEW.competencia_comp_id, 'UPDATE', v_doc, v_correo, v_nombre);
    ELSIF (TG_OP = 'DELETE') THEN
        INSERT INTO public.auditoria_resultado_aprendizaje (rap_id, rap_codigo, rap_horas, programa_prog_id, competencia_comp_id, tipo_accion, documento_usuario_accion, correo_usuario, nombre_usuario_accion)
        VALUES (OLD.rap_id, OLD.rap_codigo, OLD.rap_horas, OLD.programa_prog_id, OLD.competencia_comp_id, 'DELETE', v_doc, v_correo, v_nombre);
    END IF;
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_rap_audit AFTER INSERT OR DELETE OR UPDATE ON public.resultado_aprendizaje FOR EACH ROW EXECUTE PROCEDURE public.func_auditoria_rap();

-- ==========================================
-- AUDITORIA PROYECTOS FORMATIVOS
-- ==========================================
CREATE SEQUENCE public.auditoria_pf_seq;
CREATE TABLE public.auditoria_proyecto_formativo (
    id_auditoria integer DEFAULT nextval('public.auditoria_pf_seq') PRIMARY KEY,
    pf_id integer,
    pf_codigo character varying,
    pf_nombre character varying,
    programa_prog_codigo integer,
    
    fecha_hora timestamp DEFAULT CURRENT_TIMESTAMP,
    documento_usuario_accion bigint,
    correo_usuario character varying,
    tipo_accion character varying,
    nombre_usuario_accion character varying
);

CREATE OR REPLACE FUNCTION public.func_auditoria_pf() RETURNS trigger AS $$
DECLARE
    v_doc bigint;
    v_correo varchar;
    v_nombre varchar;
BEGIN
    v_doc := NULLIF(current_setting('myapp.documento_usuario', true), '')::bigint;
    IF v_doc IS NULL THEN v_doc := 0; END IF;
    
    v_correo := current_setting('myapp.correo_usuario', true);
    IF v_correo IS NULL THEN v_correo := 'sistema@admin.com'; END IF;
    
    v_nombre := current_setting('myapp.nombre_usuario', true);
    IF v_nombre IS NULL THEN v_nombre := 'Sistema'; END IF;

    IF (TG_OP = 'INSERT') THEN
        INSERT INTO public.auditoria_proyecto_formativo (pf_id, pf_codigo, pf_nombre, programa_prog_codigo, tipo_accion, documento_usuario_accion, correo_usuario, nombre_usuario_accion)
        VALUES (NEW.pf_id, NEW.pf_codigo, NEW.pf_nombre, NEW.programa_prog_codigo, 'INSERT', v_doc, v_correo, v_nombre);
    ELSIF (TG_OP = 'UPDATE') THEN
        INSERT INTO public.auditoria_proyecto_formativo (pf_id, pf_codigo, pf_nombre, programa_prog_codigo, tipo_accion, documento_usuario_accion, correo_usuario, nombre_usuario_accion)
        VALUES (NEW.pf_id, NEW.pf_codigo, NEW.pf_nombre, NEW.programa_prog_codigo, 'UPDATE', v_doc, v_correo, v_nombre);
    ELSIF (TG_OP = 'DELETE') THEN
        INSERT INTO public.auditoria_proyecto_formativo (pf_id, pf_codigo, pf_nombre, programa_prog_codigo, tipo_accion, documento_usuario_accion, correo_usuario, nombre_usuario_accion)
        VALUES (OLD.pf_id, OLD.pf_codigo, OLD.pf_nombre, OLD.programa_prog_codigo, 'DELETE', v_doc, v_correo, v_nombre);
    END IF;
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_pf_audit AFTER INSERT OR DELETE OR UPDATE ON public.proyecto_formativo FOR EACH ROW EXECUTE PROCEDURE public.func_auditoria_pf();
