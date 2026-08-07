--
-- PostgreSQL database dump
--

\restrict AwCg5bsIiatTGw7EtL3CJUIz0ouPUFleqjYarJkpbpZ4z9Fdq3hf5lp36hCVCns

-- Dumped from database version 16.14 (Ubuntu 16.14-0ubuntu0.24.04.1)
-- Dumped by pg_dump version 16.14 (Ubuntu 16.14-0ubuntu0.24.04.1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: actividades; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.actividades (
    id_actividad bigint NOT NULL,
    id_tenant bigint NOT NULL,
    id_usuario bigint NOT NULL,
    id_cliente bigint,
    id_lead bigint,
    id_oportunidad bigint,
    tipo character varying(255) DEFAULT 'tarea'::character varying NOT NULL,
    titulo character varying(150) NOT NULL,
    descripcion text,
    estado character varying(255) DEFAULT 'pendiente'::character varying NOT NULL,
    fecha_inicio timestamp(0) without time zone,
    fecha_fin timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT actividades_estado_check CHECK (((estado)::text = ANY ((ARRAY['pendiente'::character varying, 'completada'::character varying, 'cancelada'::character varying])::text[]))),
    CONSTRAINT actividades_tipo_check CHECK (((tipo)::text = ANY ((ARRAY['llamada'::character varying, 'reunion'::character varying, 'email'::character varying, 'tarea'::character varying, 'nota'::character varying])::text[])))
);


--
-- Name: actividades_id_actividad_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.actividades_id_actividad_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: actividades_id_actividad_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.actividades_id_actividad_seq OWNED BY public.actividades.id_actividad;


--
-- Name: automatizaciones; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.automatizaciones (
    id bigint NOT NULL,
    id_tenant bigint NOT NULL,
    nombre_automatizacion character varying(150) NOT NULL,
    regla character varying(255) NOT NULL,
    evento character varying(100) NOT NULL,
    accion character varying(150) NOT NULL,
    activa boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: automatizaciones_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.automatizaciones_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: automatizaciones_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.automatizaciones_id_seq OWNED BY public.automatizaciones.id;


--
-- Name: cache; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration bigint NOT NULL
);


--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration bigint NOT NULL
);


--
-- Name: campana_cliente; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.campana_cliente (
    id bigint NOT NULL,
    id_campana bigint NOT NULL,
    id_cliente bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: campana_cliente_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.campana_cliente_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: campana_cliente_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.campana_cliente_id_seq OWNED BY public.campana_cliente.id;


--
-- Name: campanas; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.campanas (
    id bigint NOT NULL,
    id_tenant bigint NOT NULL,
    nombre_compania character varying(150) NOT NULL,
    segmento character varying(150) NOT NULL,
    estado character varying(255) DEFAULT 'activa'::character varying NOT NULL,
    fecha_inicio date,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT campanas_estado_check CHECK (((estado)::text = ANY ((ARRAY['activa'::character varying, 'pausada'::character varying, 'finalizada'::character varying])::text[])))
);


--
-- Name: campanas_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.campanas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: campanas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.campanas_id_seq OWNED BY public.campanas.id;


--
-- Name: campanas_marketing; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.campanas_marketing (
    id bigint NOT NULL,
    id_tenant bigint NOT NULL,
    id_usuario bigint NOT NULL,
    nombre_compania character varying(150) NOT NULL,
    segmento character varying(150) NOT NULL,
    estado character varying(255) DEFAULT 'activa'::character varying NOT NULL,
    fecha_inicio date,
    lista_contactos json,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT campanas_marketing_estado_check CHECK (((estado)::text = ANY ((ARRAY['activa'::character varying, 'pausada'::character varying, 'finalizada'::character varying])::text[])))
);


--
-- Name: campanas_marketing_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.campanas_marketing_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: campanas_marketing_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.campanas_marketing_id_seq OWNED BY public.campanas_marketing.id;


--
-- Name: categorias; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.categorias (
    id_categoria bigint NOT NULL,
    id_tenant bigint NOT NULL,
    nombre character varying(100) NOT NULL,
    descripcion character varying(350),
    activo boolean DEFAULT true NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: categorias_id_categoria_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.categorias_id_categoria_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: categorias_id_categoria_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.categorias_id_categoria_seq OWNED BY public.categorias.id_categoria;


--
-- Name: clientes; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.clientes (
    id_cliente bigint NOT NULL,
    id_tenant bigint NOT NULL,
    nombre character varying(150) NOT NULL,
    apellido_p character varying(150),
    apellido_m character varying(150),
    email character varying(150),
    telefono character varying(20),
    empresa character varying(150),
    rfc character varying(20),
    direccion character varying(150),
    tipo character varying(255) DEFAULT 'persona'::character varying NOT NULL,
    activo boolean DEFAULT true NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    sector_empresarial character varying(100),
    CONSTRAINT clientes_tipo_check CHECK (((tipo)::text = ANY ((ARRAY['persona'::character varying, 'empresa'::character varying])::text[])))
);


--
-- Name: clientes_id_cliente_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.clientes_id_cliente_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: clientes_id_cliente_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.clientes_id_cliente_seq OWNED BY public.clientes.id_cliente;


--
-- Name: contactos; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.contactos (
    id_contacto bigint NOT NULL,
    id_tenant bigint NOT NULL,
    id_cliente bigint NOT NULL,
    nombre character varying(150) NOT NULL,
    apellido_p character varying(150),
    apellido_m character varying(150),
    email character varying(200),
    telefono character varying(20),
    cargo character varying(100),
    principal boolean DEFAULT false NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: contactos_id_contacto_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.contactos_id_contacto_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: contactos_id_contacto_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.contactos_id_contacto_seq OWNED BY public.contactos.id_contacto;


--
-- Name: erp_asiento_detalles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.erp_asiento_detalles (
    id bigint NOT NULL,
    id_asiento bigint NOT NULL,
    id_cuenta bigint NOT NULL,
    debe numeric(14,2) DEFAULT '0'::numeric NOT NULL,
    haber numeric(14,2) DEFAULT '0'::numeric NOT NULL,
    descripcion character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: erp_asiento_detalles_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.erp_asiento_detalles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: erp_asiento_detalles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.erp_asiento_detalles_id_seq OWNED BY public.erp_asiento_detalles.id;


--
-- Name: erp_asientos; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.erp_asientos (
    id bigint NOT NULL,
    id_tenant bigint NOT NULL,
    fecha date NOT NULL,
    concepto character varying(255) NOT NULL,
    origen character varying(255) NOT NULL,
    referencia_tipo character varying(50),
    referencia_id bigint,
    total_debe numeric(14,2) NOT NULL,
    total_haber numeric(14,2) NOT NULL,
    id_usuario bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT erp_asientos_origen_check CHECK (((origen)::text = ANY ((ARRAY['manual'::character varying, 'venta'::character varying, 'compra'::character varying, 'nomina'::character varying, 'migracion'::character varying, 'ajuste'::character varying])::text[])))
);


--
-- Name: erp_asientos_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.erp_asientos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: erp_asientos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.erp_asientos_id_seq OWNED BY public.erp_asientos.id;


--
-- Name: erp_comanda_items; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.erp_comanda_items (
    id bigint NOT NULL,
    id_comanda bigint NOT NULL,
    id_producto bigint,
    nombre character varying(150) NOT NULL,
    precio_unitario numeric(10,2) NOT NULL,
    cantidad integer NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: erp_comanda_items_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.erp_comanda_items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: erp_comanda_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.erp_comanda_items_id_seq OWNED BY public.erp_comanda_items.id;


--
-- Name: erp_comandas; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.erp_comandas (
    id bigint NOT NULL,
    id_tenant bigint NOT NULL,
    id_mesa bigint NOT NULL,
    estado character varying(255) DEFAULT 'abierta'::character varying NOT NULL,
    enviada_cocina boolean DEFAULT false NOT NULL,
    total numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: erp_comandas_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.erp_comandas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: erp_comandas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.erp_comandas_id_seq OWNED BY public.erp_comandas.id;


--
-- Name: erp_empleados; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.erp_empleados (
    id bigint NOT NULL,
    id_tenant bigint NOT NULL,
    nombre character varying(150) NOT NULL,
    departamento character varying(100) NOT NULL,
    puesto character varying(100) NOT NULL,
    estado character varying(255) DEFAULT 'activo'::character varying NOT NULL,
    salario numeric(10,2),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT erp_empleados_estado_check CHECK (((estado)::text = ANY ((ARRAY['activo'::character varying, 'inactivo'::character varying])::text[])))
);


--
-- Name: erp_empleados_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.erp_empleados_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: erp_empleados_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.erp_empleados_id_seq OWNED BY public.erp_empleados.id;


--
-- Name: erp_envios; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.erp_envios (
    id bigint NOT NULL,
    id_tenant bigint NOT NULL,
    destino character varying(150) NOT NULL,
    transportista character varying(100) NOT NULL,
    eta character varying(100) NOT NULL,
    estado character varying(255) DEFAULT 'en_transito'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    CONSTRAINT erp_envios_estado_check CHECK (((estado)::text = ANY ((ARRAY['en_transito'::character varying, 'entregado'::character varying])::text[])))
);


--
-- Name: erp_envios_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.erp_envios_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: erp_envios_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.erp_envios_id_seq OWNED BY public.erp_envios.id;


--
-- Name: erp_habitacion_consumos; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.erp_habitacion_consumos (
    id bigint NOT NULL,
    id_habitacion bigint NOT NULL,
    id_producto bigint,
    nombre character varying(150) NOT NULL,
    precio_unitario numeric(10,2) NOT NULL,
    cantidad integer NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: erp_habitacion_consumos_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.erp_habitacion_consumos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: erp_habitacion_consumos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.erp_habitacion_consumos_id_seq OWNED BY public.erp_habitacion_consumos.id;


--
-- Name: erp_habitaciones; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.erp_habitaciones (
    id bigint NOT NULL,
    id_tenant bigint NOT NULL,
    numero integer NOT NULL,
    tipo character varying(20) DEFAULT 'Sgl'::character varying NOT NULL,
    piso integer DEFAULT 1 NOT NULL,
    estado character varying(255) DEFAULT 'libre'::character varying NOT NULL,
    huesped character varying(255),
    check_in date,
    check_out date,
    noches integer,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: erp_habitaciones_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.erp_habitaciones_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: erp_habitaciones_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.erp_habitaciones_id_seq OWNED BY public.erp_habitaciones.id;


--
-- Name: erp_mesas; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.erp_mesas (
    id bigint NOT NULL,
    id_tenant bigint NOT NULL,
    numero integer NOT NULL,
    capacidad integer DEFAULT 2 NOT NULL,
    estado character varying(255) DEFAULT 'libre'::character varying NOT NULL,
    mesero character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: erp_mesas_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.erp_mesas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: erp_mesas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.erp_mesas_id_seq OWNED BY public.erp_mesas.id;


--
-- Name: erp_movimientos; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.erp_movimientos (
    id bigint NOT NULL,
    id_tenant bigint NOT NULL,
    concepto character varying(200) NOT NULL,
    tipo character varying(255) NOT NULL,
    monto numeric(12,2) NOT NULL,
    fecha date NOT NULL,
    categoria character varying(100) DEFAULT 'General'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    CONSTRAINT erp_movimientos_tipo_check CHECK (((tipo)::text = ANY ((ARRAY['ingreso'::character varying, 'egreso'::character varying])::text[])))
);


--
-- Name: erp_movimientos_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.erp_movimientos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: erp_movimientos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.erp_movimientos_id_seq OWNED BY public.erp_movimientos.id;


--
-- Name: erp_movimientos_stock; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.erp_movimientos_stock (
    id bigint NOT NULL,
    id_tenant bigint NOT NULL,
    id_producto bigint NOT NULL,
    tipo character varying(255) NOT NULL,
    cantidad integer NOT NULL,
    motivo character varying(30) NOT NULL,
    referencia character varying(100),
    stock_resultante integer NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT erp_movimientos_stock_tipo_check CHECK (((tipo)::text = ANY ((ARRAY['entrada'::character varying, 'salida'::character varying, 'ajuste'::character varying])::text[])))
);


--
-- Name: erp_movimientos_stock_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.erp_movimientos_stock_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: erp_movimientos_stock_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.erp_movimientos_stock_id_seq OWNED BY public.erp_movimientos_stock.id;


--
-- Name: erp_nomina_pago_detalles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.erp_nomina_pago_detalles (
    id bigint NOT NULL,
    id_nomina_pago bigint NOT NULL,
    id_empleado bigint NOT NULL,
    salario numeric(10,2) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: erp_nomina_pago_detalles_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.erp_nomina_pago_detalles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: erp_nomina_pago_detalles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.erp_nomina_pago_detalles_id_seq OWNED BY public.erp_nomina_pago_detalles.id;


--
-- Name: erp_nomina_pagos; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.erp_nomina_pagos (
    id bigint NOT NULL,
    id_tenant bigint NOT NULL,
    fecha date NOT NULL,
    total numeric(14,2) NOT NULL,
    id_asiento bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: erp_nomina_pagos_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.erp_nomina_pagos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: erp_nomina_pagos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.erp_nomina_pagos_id_seq OWNED BY public.erp_nomina_pagos.id;


--
-- Name: erp_orden_compra_items; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.erp_orden_compra_items (
    id bigint NOT NULL,
    id_orden_compra bigint NOT NULL,
    id_producto bigint NOT NULL,
    cantidad integer NOT NULL,
    precio_unitario numeric(10,2) NOT NULL,
    subtotal numeric(12,2) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: erp_orden_compra_items_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.erp_orden_compra_items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: erp_orden_compra_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.erp_orden_compra_items_id_seq OWNED BY public.erp_orden_compra_items.id;


--
-- Name: erp_ordenes_compra; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.erp_ordenes_compra (
    id bigint NOT NULL,
    id_tenant bigint NOT NULL,
    fecha date NOT NULL,
    estado character varying(255) DEFAULT 'pendiente'::character varying NOT NULL,
    total numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    id_proveedor bigint NOT NULL,
    CONSTRAINT erp_ordenes_compra_estado_check CHECK (((estado)::text = ANY ((ARRAY['pendiente'::character varying, 'recibida'::character varying, 'cancelada'::character varying])::text[])))
);


--
-- Name: erp_ordenes_compra_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.erp_ordenes_compra_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: erp_ordenes_compra_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.erp_ordenes_compra_id_seq OWNED BY public.erp_ordenes_compra.id;


--
-- Name: erp_ordenes_produccion; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.erp_ordenes_produccion (
    id bigint NOT NULL,
    id_tenant bigint NOT NULL,
    producto character varying(150) NOT NULL,
    cantidad integer DEFAULT 0 NOT NULL,
    progreso smallint DEFAULT '0'::smallint NOT NULL,
    estado character varying(255) DEFAULT 'en proceso'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT erp_ordenes_produccion_estado_check CHECK (((estado)::text = ANY ((ARRAY['en proceso'::character varying, 'completada'::character varying])::text[])))
);


--
-- Name: erp_ordenes_produccion_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.erp_ordenes_produccion_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: erp_ordenes_produccion_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.erp_ordenes_produccion_id_seq OWNED BY public.erp_ordenes_produccion.id;


--
-- Name: erp_pedido_items; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.erp_pedido_items (
    id bigint NOT NULL,
    id_pedido bigint NOT NULL,
    id_producto bigint NOT NULL,
    cantidad integer NOT NULL,
    precio_unitario numeric(10,2) NOT NULL,
    subtotal numeric(12,2) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    costo_unitario numeric(10,2)
);


--
-- Name: erp_pedido_items_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.erp_pedido_items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: erp_pedido_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.erp_pedido_items_id_seq OWNED BY public.erp_pedido_items.id;


--
-- Name: erp_pedido_pagos; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.erp_pedido_pagos (
    id bigint NOT NULL,
    id_pedido bigint NOT NULL,
    metodo_pago character varying(30) NOT NULL,
    monto numeric(12,2) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: erp_pedido_pagos_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.erp_pedido_pagos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: erp_pedido_pagos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.erp_pedido_pagos_id_seq OWNED BY public.erp_pedido_pagos.id;


--
-- Name: erp_pedidos_venta; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.erp_pedidos_venta (
    id bigint NOT NULL,
    id_tenant bigint NOT NULL,
    total numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    estado character varying(30) DEFAULT 'pendiente'::character varying NOT NULL,
    fecha date NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    id_cliente bigint NOT NULL
);


--
-- Name: erp_pedidos_venta_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.erp_pedidos_venta_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: erp_pedidos_venta_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.erp_pedidos_venta_id_seq OWNED BY public.erp_pedidos_venta.id;


--
-- Name: erp_plan_cuentas; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.erp_plan_cuentas (
    id bigint NOT NULL,
    id_tenant bigint NOT NULL,
    codigo character varying(20) NOT NULL,
    nombre character varying(150) NOT NULL,
    tipo character varying(255) NOT NULL,
    naturaleza character varying(255) NOT NULL,
    id_cuenta_padre bigint,
    es_movible boolean DEFAULT true NOT NULL,
    activo boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT erp_plan_cuentas_naturaleza_check CHECK (((naturaleza)::text = ANY ((ARRAY['deudora'::character varying, 'acreedora'::character varying])::text[]))),
    CONSTRAINT erp_plan_cuentas_tipo_check CHECK (((tipo)::text = ANY ((ARRAY['activo'::character varying, 'pasivo'::character varying, 'capital'::character varying, 'ingreso'::character varying, 'costo'::character varying, 'gasto'::character varying])::text[])))
);


--
-- Name: erp_plan_cuentas_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.erp_plan_cuentas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: erp_plan_cuentas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.erp_plan_cuentas_id_seq OWNED BY public.erp_plan_cuentas.id;


--
-- Name: erp_proyecto_horas; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.erp_proyecto_horas (
    id bigint NOT NULL,
    id_tenant bigint NOT NULL,
    id_proyecto bigint NOT NULL,
    colaborador character varying(150) NOT NULL,
    fecha date NOT NULL,
    horas numeric(5,2) NOT NULL,
    descripcion character varying(200),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: erp_proyecto_horas_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.erp_proyecto_horas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: erp_proyecto_horas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.erp_proyecto_horas_id_seq OWNED BY public.erp_proyecto_horas.id;


--
-- Name: erp_proyecto_tareas; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.erp_proyecto_tareas (
    id bigint NOT NULL,
    id_tenant bigint NOT NULL,
    id_proyecto bigint NOT NULL,
    titulo character varying(150) NOT NULL,
    descripcion text,
    estado character varying(255) DEFAULT 'pendiente'::character varying NOT NULL,
    asignado character varying(150),
    orden integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    fecha_inicio date,
    fecha_fin date,
    CONSTRAINT erp_proyecto_tareas_estado_check CHECK (((estado)::text = ANY ((ARRAY['pendiente'::character varying, 'en_progreso'::character varying, 'completada'::character varying])::text[])))
);


--
-- Name: erp_proyecto_tareas_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.erp_proyecto_tareas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: erp_proyecto_tareas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.erp_proyecto_tareas_id_seq OWNED BY public.erp_proyecto_tareas.id;


--
-- Name: erp_proyectos; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.erp_proyectos (
    id bigint NOT NULL,
    id_tenant bigint NOT NULL,
    nombre character varying(150) NOT NULL,
    cliente character varying(150) NOT NULL,
    responsable character varying(150) NOT NULL,
    estado character varying(255) DEFAULT 'activo'::character varying NOT NULL,
    progreso smallint DEFAULT '0'::smallint NOT NULL,
    horas integer DEFAULT 0 NOT NULL,
    presupuesto numeric(12,2),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT erp_proyectos_estado_check CHECK (((estado)::text = ANY ((ARRAY['activo'::character varying, 'pausado'::character varying, 'completado'::character varying])::text[])))
);


--
-- Name: erp_proyectos_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.erp_proyectos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: erp_proyectos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.erp_proyectos_id_seq OWNED BY public.erp_proyectos.id;


--
-- Name: erp_recetas; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.erp_recetas (
    id bigint NOT NULL,
    id_tenant bigint NOT NULL,
    id_cliente bigint NOT NULL,
    id_producto bigint NOT NULL,
    dosis character varying(150),
    cantidad integer NOT NULL,
    pendiente boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: erp_recetas_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.erp_recetas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: erp_recetas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.erp_recetas_id_seq OWNED BY public.erp_recetas.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: integraciones; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.integraciones (
    id bigint NOT NULL,
    id_tenant bigint NOT NULL,
    nombre character varying(100) NOT NULL,
    tipo character varying(255) NOT NULL,
    estado character varying(255) DEFAULT 'desconectada'::character varying NOT NULL,
    configuracion json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT integraciones_estado_check CHECK (((estado)::text = ANY ((ARRAY['conectada'::character varying, 'desconectada'::character varying, 'error'::character varying])::text[]))),
    CONSTRAINT integraciones_tipo_check CHECK (((tipo)::text = ANY ((ARRAY['whatsapp'::character varying, 'email'::character varying, 'calendario'::character varying, 'almacenamiento'::character varying, 'otro'::character varying])::text[])))
);


--
-- Name: integraciones_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.integraciones_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: integraciones_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.integraciones_id_seq OWNED BY public.integraciones.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


--
-- Name: jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: leads; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.leads (
    id_lead bigint NOT NULL,
    id_tenant bigint NOT NULL,
    id_cliente bigint,
    id_usuario bigint NOT NULL,
    titulo character varying(150) NOT NULL,
    descripcion text,
    estado character varying(30) DEFAULT 'nuevo'::character varying NOT NULL,
    fuente character varying(255) DEFAULT 'otro'::character varying NOT NULL,
    valor_estimado numeric(10,2),
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    nombre character varying(150),
    email character varying(200),
    telefono character varying(20),
    CONSTRAINT leads_fuente_check CHECK (((fuente)::text = ANY ((ARRAY['web'::character varying, 'referido'::character varying, 'llamada'::character varying, 'email'::character varying, 'otro'::character varying])::text[])))
);


--
-- Name: leads_id_lead_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.leads_id_lead_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: leads_id_lead_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.leads_id_lead_seq OWNED BY public.leads.id_lead;


--
-- Name: membresias; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.membresias (
    id_membresia bigint NOT NULL,
    id_usuario bigint NOT NULL,
    id_tenant bigint NOT NULL,
    estado character varying(255) DEFAULT 'activa'::character varying NOT NULL,
    es_owner boolean DEFAULT false NOT NULL,
    invitado_por bigint,
    unido_en timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT membresias_estado_check CHECK (((estado)::text = ANY ((ARRAY['invitada'::character varying, 'activa'::character varying, 'suspendida'::character varying])::text[])))
);


--
-- Name: membresias_id_membresia_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.membresias_id_membresia_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: membresias_id_membresia_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.membresias_id_membresia_seq OWNED BY public.membresias.id_membresia;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: modulos; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.modulos (
    id_modulo bigint NOT NULL,
    clave character varying(40) NOT NULL,
    nombre character varying(100) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: modulos_id_modulo_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.modulos_id_modulo_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: modulos_id_modulo_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.modulos_id_modulo_seq OWNED BY public.modulos.id_modulo;


--
-- Name: negocios; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.negocios (
    id_tiponegocio bigint NOT NULL,
    nombre_negocio character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    slug character varying(30)
);


--
-- Name: negocios_id_tiponegocio_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.negocios_id_tiponegocio_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: negocios_id_tiponegocio_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.negocios_id_tiponegocio_seq OWNED BY public.negocios.id_tiponegocio;


--
-- Name: notificaciones; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.notificaciones (
    id_notificacion bigint NOT NULL,
    id_tenant bigint NOT NULL,
    id_cliente bigint,
    titulo character varying(150) NOT NULL,
    mensaje text,
    tipo character varying(50) DEFAULT 'info'::character varying NOT NULL,
    leida boolean DEFAULT false NOT NULL,
    url character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    id_usuario bigint NOT NULL
);


--
-- Name: notificaciones_id_notificacion_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.notificaciones_id_notificacion_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: notificaciones_id_notificacion_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.notificaciones_id_notificacion_seq OWNED BY public.notificaciones.id_notificacion;


--
-- Name: oauth_access_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.oauth_access_tokens (
    id character(80) NOT NULL,
    user_id bigint,
    client_id uuid NOT NULL,
    name character varying(255),
    scopes text,
    revoked boolean NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone
);


--
-- Name: oauth_auth_codes; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.oauth_auth_codes (
    id character(80) NOT NULL,
    user_id bigint NOT NULL,
    client_id uuid NOT NULL,
    scopes text,
    revoked boolean NOT NULL,
    expires_at timestamp(0) without time zone
);


--
-- Name: oauth_clients; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.oauth_clients (
    id uuid NOT NULL,
    owner_type character varying(255),
    owner_id bigint,
    name character varying(255) NOT NULL,
    secret character varying(255),
    provider character varying(255),
    redirect_uris text NOT NULL,
    grant_types text NOT NULL,
    revoked boolean NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: oauth_device_codes; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.oauth_device_codes (
    id character(80) NOT NULL,
    user_id bigint,
    client_id uuid NOT NULL,
    user_code character(8) NOT NULL,
    scopes text NOT NULL,
    revoked boolean NOT NULL,
    user_approved_at timestamp(0) without time zone,
    last_polled_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone
);


--
-- Name: oauth_refresh_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.oauth_refresh_tokens (
    id character(80) NOT NULL,
    access_token_id character(80) NOT NULL,
    revoked boolean NOT NULL,
    expires_at timestamp(0) without time zone
);


--
-- Name: oportunidades; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.oportunidades (
    id_oportunidad bigint NOT NULL,
    id_tenant bigint NOT NULL,
    id_cliente bigint NOT NULL,
    id_pipeline bigint NOT NULL,
    id_usuario bigint NOT NULL,
    titulo character varying(150) NOT NULL,
    valor numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    probabilidad integer DEFAULT 0 NOT NULL,
    estado character varying(255) DEFAULT 'abierta'::character varying NOT NULL,
    fecha_cierre date,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    etapa character varying(255) DEFAULT 'prospeccion'::character varying NOT NULL,
    CONSTRAINT oportunidades_estado_check CHECK (((estado)::text = ANY ((ARRAY['abierta'::character varying, 'ganada'::character varying, 'perdida'::character varying])::text[]))),
    CONSTRAINT oportunidades_etapa_check CHECK (((etapa)::text = ANY ((ARRAY['prospeccion'::character varying, 'contacto'::character varying, 'propuesta'::character varying, 'negociacion'::character varying, 'cierre'::character varying])::text[])))
);


--
-- Name: oportunidades_id_oportunidad_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.oportunidades_id_oportunidad_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: oportunidades_id_oportunidad_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.oportunidades_id_oportunidad_seq OWNED BY public.oportunidades.id_oportunidad;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


--
-- Name: permisos; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.permisos (
    id_permiso bigint NOT NULL,
    clave character varying(120) NOT NULL,
    id_modulo bigint,
    descripcion character varying(200),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: permisos_id_permiso_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.permisos_id_permiso_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: permisos_id_permiso_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.permisos_id_permiso_seq OWNED BY public.permisos.id_permiso;


--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name text NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- Name: pipelines; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.pipelines (
    id_pipeline bigint NOT NULL,
    id_tenant bigint NOT NULL,
    nombre character varying(150) NOT NULL,
    activo boolean DEFAULT true NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: pipelines_id_pipeline_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.pipelines_id_pipeline_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: pipelines_id_pipeline_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.pipelines_id_pipeline_seq OWNED BY public.pipelines.id_pipeline;


--
-- Name: plans; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.plans (
    id_plan bigint NOT NULL,
    nombre_plan character varying(255) NOT NULL,
    precio numeric(10,2) NOT NULL,
    fecha_inicio date NOT NULL,
    fecha_fin date NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    max_usuarios integer,
    stripe_price_id character varying(255)
);


--
-- Name: plans_id_plan_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.plans_id_plan_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: plans_id_plan_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.plans_id_plan_seq OWNED BY public.plans.id_plan;


--
-- Name: productos; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.productos (
    id_productos bigint NOT NULL,
    id_tenant bigint NOT NULL,
    id_categorias bigint NOT NULL,
    nombre character varying(150) NOT NULL,
    descripcion character varying(350),
    precio numeric(10,2) NOT NULL,
    stock integer DEFAULT 0 NOT NULL,
    activo boolean DEFAULT true NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    sku character varying(100),
    stock_minimo integer DEFAULT 0 NOT NULL,
    precio_compra numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    imagen character varying(255)
);


--
-- Name: productos_id_productos_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.productos_id_productos_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: productos_id_productos_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.productos_id_productos_seq OWNED BY public.productos.id_productos;


--
-- Name: proveedores; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.proveedores (
    id_proveedor bigint NOT NULL,
    id_tenant bigint NOT NULL,
    nombre character varying(150) NOT NULL,
    contacto character varying(150),
    email character varying(200),
    telefono character varying(20),
    direccion character varying(250),
    rfc character varying(20),
    activo boolean DEFAULT true NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: proveedores_id_proveedor_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.proveedores_id_proveedor_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: proveedores_id_proveedor_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.proveedores_id_proveedor_seq OWNED BY public.proveedores.id_proveedor;


--
-- Name: rol_permiso; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rol_permiso (
    id_rol bigint NOT NULL,
    id_permiso bigint NOT NULL
);


--
-- Name: roles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.roles (
    id_rol bigint NOT NULL,
    id_tenant bigint,
    id_modulo bigint,
    clave character varying(80) NOT NULL,
    nombre character varying(100) NOT NULL,
    descripcion text,
    es_sistema boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: roles_id_rol_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.roles_id_rol_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: roles_id_rol_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.roles_id_rol_seq OWNED BY public.roles.id_rol;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


--
-- Name: stripe_webhook_events; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.stripe_webhook_events (
    id bigint NOT NULL,
    stripe_event_id character varying(255) NOT NULL,
    tipo character varying(255) NOT NULL,
    procesado_en timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: stripe_webhook_events_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.stripe_webhook_events_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: stripe_webhook_events_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.stripe_webhook_events_id_seq OWNED BY public.stripe_webhook_events.id;


--
-- Name: suscripciones; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.suscripciones (
    id_suscripcion bigint NOT NULL,
    id_tenant bigint NOT NULL,
    id_plan bigint NOT NULL,
    stripe_subscription_id character varying(255) NOT NULL,
    stripe_price_id character varying(255) NOT NULL,
    estado character varying(30) DEFAULT 'incompleta'::character varying NOT NULL,
    fecha_inicio timestamp(0) without time zone,
    fecha_fin_periodo_actual timestamp(0) without time zone,
    cancela_al_final_periodo boolean DEFAULT false NOT NULL,
    fecha_cancelacion timestamp(0) without time zone,
    ultimo_evento_stripe json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: suscripciones_id_suscripcion_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.suscripciones_id_suscripcion_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: suscripciones_id_suscripcion_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.suscripciones_id_suscripcion_seq OWNED BY public.suscripciones.id_suscripcion;


--
-- Name: tenants; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tenants (
    id_tenant bigint NOT NULL,
    nombre_tenant character varying(100) NOT NULL,
    subdominio character varying(255) NOT NULL,
    estado character varying(255) DEFAULT 'activo'::character varying NOT NULL,
    id_tiponegocio bigint,
    id_plan bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    moneda character varying(3),
    modulo_crm boolean DEFAULT true NOT NULL,
    modulo_pos boolean DEFAULT false NOT NULL,
    modulo_erp boolean DEFAULT false NOT NULL,
    datos_nicho json,
    onboarding_completado boolean DEFAULT false NOT NULL,
    sector character varying(100),
    idioma character varying(5),
    zona_horaria character varying(60),
    logo character varying(255),
    stripe_customer_id character varying(255)
);


--
-- Name: tenants_id_tenant_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.tenants_id_tenant_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: tenants_id_tenant_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.tenants_id_tenant_seq OWNED BY public.tenants.id_tenant;


--
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: usuario; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.usuario (
    id_usr bigint NOT NULL,
    email character varying(255) NOT NULL,
    password character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: usuario_id_usr_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.usuario_id_usr_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: usuario_id_usr_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.usuario_id_usr_seq OWNED BY public.usuario.id_usr;


--
-- Name: usuario_rol; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.usuario_rol (
    id_usuario_rol bigint NOT NULL,
    id_usuario bigint NOT NULL,
    id_tenant bigint NOT NULL,
    id_rol bigint NOT NULL,
    asignado_por bigint,
    asignado_en timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: usuario_rol_global; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.usuario_rol_global (
    id_usuario_rol_global bigint NOT NULL,
    id_usuario bigint NOT NULL,
    id_rol bigint NOT NULL,
    asignado_por bigint,
    asignado_en timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: usuario_rol_global_id_usuario_rol_global_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.usuario_rol_global_id_usuario_rol_global_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: usuario_rol_global_id_usuario_rol_global_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.usuario_rol_global_id_usuario_rol_global_seq OWNED BY public.usuario_rol_global.id_usuario_rol_global;


--
-- Name: usuario_rol_id_usuario_rol_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.usuario_rol_id_usuario_rol_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: usuario_rol_id_usuario_rol_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.usuario_rol_id_usuario_rol_seq OWNED BY public.usuario_rol.id_usuario_rol;


--
-- Name: usuarios; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.usuarios (
    id_usuario bigint NOT NULL,
    id_tenant bigint NOT NULL,
    nombre character varying(100) NOT NULL,
    email character varying(200),
    password character varying(200) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    es_admin boolean DEFAULT false NOT NULL,
    es_superadmin boolean DEFAULT false NOT NULL,
    foto_perfil character varying(255),
    estado character varying(255) DEFAULT 'activo'::character varying NOT NULL,
    pin character varying(255)
);


--
-- Name: usuarios_id_usuario_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.usuarios_id_usuario_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: usuarios_id_usuario_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.usuarios_id_usuario_seq OWNED BY public.usuarios.id_usuario;


--
-- Name: actividades id_actividad; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.actividades ALTER COLUMN id_actividad SET DEFAULT nextval('public.actividades_id_actividad_seq'::regclass);


--
-- Name: automatizaciones id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.automatizaciones ALTER COLUMN id SET DEFAULT nextval('public.automatizaciones_id_seq'::regclass);


--
-- Name: campana_cliente id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.campana_cliente ALTER COLUMN id SET DEFAULT nextval('public.campana_cliente_id_seq'::regclass);


--
-- Name: campanas id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.campanas ALTER COLUMN id SET DEFAULT nextval('public.campanas_id_seq'::regclass);


--
-- Name: campanas_marketing id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.campanas_marketing ALTER COLUMN id SET DEFAULT nextval('public.campanas_marketing_id_seq'::regclass);


--
-- Name: categorias id_categoria; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.categorias ALTER COLUMN id_categoria SET DEFAULT nextval('public.categorias_id_categoria_seq'::regclass);


--
-- Name: clientes id_cliente; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.clientes ALTER COLUMN id_cliente SET DEFAULT nextval('public.clientes_id_cliente_seq'::regclass);


--
-- Name: contactos id_contacto; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.contactos ALTER COLUMN id_contacto SET DEFAULT nextval('public.contactos_id_contacto_seq'::regclass);


--
-- Name: erp_asiento_detalles id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_asiento_detalles ALTER COLUMN id SET DEFAULT nextval('public.erp_asiento_detalles_id_seq'::regclass);


--
-- Name: erp_asientos id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_asientos ALTER COLUMN id SET DEFAULT nextval('public.erp_asientos_id_seq'::regclass);


--
-- Name: erp_comanda_items id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_comanda_items ALTER COLUMN id SET DEFAULT nextval('public.erp_comanda_items_id_seq'::regclass);


--
-- Name: erp_comandas id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_comandas ALTER COLUMN id SET DEFAULT nextval('public.erp_comandas_id_seq'::regclass);


--
-- Name: erp_empleados id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_empleados ALTER COLUMN id SET DEFAULT nextval('public.erp_empleados_id_seq'::regclass);


--
-- Name: erp_envios id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_envios ALTER COLUMN id SET DEFAULT nextval('public.erp_envios_id_seq'::regclass);


--
-- Name: erp_habitacion_consumos id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_habitacion_consumos ALTER COLUMN id SET DEFAULT nextval('public.erp_habitacion_consumos_id_seq'::regclass);


--
-- Name: erp_habitaciones id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_habitaciones ALTER COLUMN id SET DEFAULT nextval('public.erp_habitaciones_id_seq'::regclass);


--
-- Name: erp_mesas id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_mesas ALTER COLUMN id SET DEFAULT nextval('public.erp_mesas_id_seq'::regclass);


--
-- Name: erp_movimientos id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_movimientos ALTER COLUMN id SET DEFAULT nextval('public.erp_movimientos_id_seq'::regclass);


--
-- Name: erp_movimientos_stock id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_movimientos_stock ALTER COLUMN id SET DEFAULT nextval('public.erp_movimientos_stock_id_seq'::regclass);


--
-- Name: erp_nomina_pago_detalles id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_nomina_pago_detalles ALTER COLUMN id SET DEFAULT nextval('public.erp_nomina_pago_detalles_id_seq'::regclass);


--
-- Name: erp_nomina_pagos id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_nomina_pagos ALTER COLUMN id SET DEFAULT nextval('public.erp_nomina_pagos_id_seq'::regclass);


--
-- Name: erp_orden_compra_items id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_orden_compra_items ALTER COLUMN id SET DEFAULT nextval('public.erp_orden_compra_items_id_seq'::regclass);


--
-- Name: erp_ordenes_compra id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_ordenes_compra ALTER COLUMN id SET DEFAULT nextval('public.erp_ordenes_compra_id_seq'::regclass);


--
-- Name: erp_ordenes_produccion id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_ordenes_produccion ALTER COLUMN id SET DEFAULT nextval('public.erp_ordenes_produccion_id_seq'::regclass);


--
-- Name: erp_pedido_items id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_pedido_items ALTER COLUMN id SET DEFAULT nextval('public.erp_pedido_items_id_seq'::regclass);


--
-- Name: erp_pedido_pagos id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_pedido_pagos ALTER COLUMN id SET DEFAULT nextval('public.erp_pedido_pagos_id_seq'::regclass);


--
-- Name: erp_pedidos_venta id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_pedidos_venta ALTER COLUMN id SET DEFAULT nextval('public.erp_pedidos_venta_id_seq'::regclass);


--
-- Name: erp_plan_cuentas id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_plan_cuentas ALTER COLUMN id SET DEFAULT nextval('public.erp_plan_cuentas_id_seq'::regclass);


--
-- Name: erp_proyecto_horas id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_proyecto_horas ALTER COLUMN id SET DEFAULT nextval('public.erp_proyecto_horas_id_seq'::regclass);


--
-- Name: erp_proyecto_tareas id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_proyecto_tareas ALTER COLUMN id SET DEFAULT nextval('public.erp_proyecto_tareas_id_seq'::regclass);


--
-- Name: erp_proyectos id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_proyectos ALTER COLUMN id SET DEFAULT nextval('public.erp_proyectos_id_seq'::regclass);


--
-- Name: erp_recetas id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_recetas ALTER COLUMN id SET DEFAULT nextval('public.erp_recetas_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: integraciones id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.integraciones ALTER COLUMN id SET DEFAULT nextval('public.integraciones_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: leads id_lead; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.leads ALTER COLUMN id_lead SET DEFAULT nextval('public.leads_id_lead_seq'::regclass);


--
-- Name: membresias id_membresia; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.membresias ALTER COLUMN id_membresia SET DEFAULT nextval('public.membresias_id_membresia_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: modulos id_modulo; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.modulos ALTER COLUMN id_modulo SET DEFAULT nextval('public.modulos_id_modulo_seq'::regclass);


--
-- Name: negocios id_tiponegocio; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.negocios ALTER COLUMN id_tiponegocio SET DEFAULT nextval('public.negocios_id_tiponegocio_seq'::regclass);


--
-- Name: notificaciones id_notificacion; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.notificaciones ALTER COLUMN id_notificacion SET DEFAULT nextval('public.notificaciones_id_notificacion_seq'::regclass);


--
-- Name: oportunidades id_oportunidad; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.oportunidades ALTER COLUMN id_oportunidad SET DEFAULT nextval('public.oportunidades_id_oportunidad_seq'::regclass);


--
-- Name: permisos id_permiso; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permisos ALTER COLUMN id_permiso SET DEFAULT nextval('public.permisos_id_permiso_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- Name: pipelines id_pipeline; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pipelines ALTER COLUMN id_pipeline SET DEFAULT nextval('public.pipelines_id_pipeline_seq'::regclass);


--
-- Name: plans id_plan; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.plans ALTER COLUMN id_plan SET DEFAULT nextval('public.plans_id_plan_seq'::regclass);


--
-- Name: productos id_productos; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.productos ALTER COLUMN id_productos SET DEFAULT nextval('public.productos_id_productos_seq'::regclass);


--
-- Name: proveedores id_proveedor; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.proveedores ALTER COLUMN id_proveedor SET DEFAULT nextval('public.proveedores_id_proveedor_seq'::regclass);


--
-- Name: roles id_rol; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles ALTER COLUMN id_rol SET DEFAULT nextval('public.roles_id_rol_seq'::regclass);


--
-- Name: stripe_webhook_events id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stripe_webhook_events ALTER COLUMN id SET DEFAULT nextval('public.stripe_webhook_events_id_seq'::regclass);


--
-- Name: suscripciones id_suscripcion; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.suscripciones ALTER COLUMN id_suscripcion SET DEFAULT nextval('public.suscripciones_id_suscripcion_seq'::regclass);


--
-- Name: tenants id_tenant; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tenants ALTER COLUMN id_tenant SET DEFAULT nextval('public.tenants_id_tenant_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: usuario id_usr; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario ALTER COLUMN id_usr SET DEFAULT nextval('public.usuario_id_usr_seq'::regclass);


--
-- Name: usuario_rol id_usuario_rol; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario_rol ALTER COLUMN id_usuario_rol SET DEFAULT nextval('public.usuario_rol_id_usuario_rol_seq'::regclass);


--
-- Name: usuario_rol_global id_usuario_rol_global; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario_rol_global ALTER COLUMN id_usuario_rol_global SET DEFAULT nextval('public.usuario_rol_global_id_usuario_rol_global_seq'::regclass);


--
-- Name: usuarios id_usuario; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuarios ALTER COLUMN id_usuario SET DEFAULT nextval('public.usuarios_id_usuario_seq'::regclass);


--
-- Data for Name: actividades; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.actividades (id_actividad, id_tenant, id_usuario, id_cliente, id_lead, id_oportunidad, tipo, titulo, descripcion, estado, fecha_inicio, fecha_fin, deleted_at, created_at, updated_at) FROM stdin;
4	14	18	\N	\N	\N	llamada	Llamar a Juan	\N	pendiente	2026-07-05 00:00:00	\N	\N	2026-07-03 20:53:18	2026-07-03 20:53:18
5	15	19	5	2	2	nota	Lead convertido a oportunidad	\N	completada	\N	\N	\N	2026-07-03 21:31:37	2026-07-03 21:31:37
6	19	23	7	6	3	nota	Lead convertido a oportunidad	\N	completada	\N	\N	\N	2026-07-03 21:39:54	2026-07-03 21:39:54
7	21	25	8	\N	5	nota	Oportunidad ganada	\N	completada	\N	\N	\N	2026-07-03 22:17:44	2026-07-03 22:17:44
8	14	18	2	\N	8	nota	Oportunidad ganada	\N	completada	\N	\N	\N	2026-07-03 22:30:21	2026-07-03 22:30:21
9	14	18	2	\N	9	nota	Oportunidad ganada	\N	completada	\N	\N	\N	2026-07-03 22:32:16	2026-07-03 22:32:16
10	35	43	21	11	10	nota	Lead convertido a oportunidad	\N	completada	\N	\N	\N	2026-08-07 14:02:37	2026-08-07 14:02:37
11	35	43	21	\N	10	nota	Oportunidad ganada	\N	completada	\N	\N	\N	2026-08-07 14:04:51	2026-08-07 14:04:51
12	1	7	18	\N	11	nota	Oportunidad ganada	\N	completada	\N	\N	\N	2026-08-07 15:13:56	2026-08-07 15:13:56
13	1	7	18	\N	12	nota	Oportunidad perdida	\N	completada	\N	\N	\N	2026-08-07 15:14:02	2026-08-07 15:14:02
\.


--
-- Data for Name: automatizaciones; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.automatizaciones (id, id_tenant, nombre_automatizacion, regla, evento, accion, activa, created_at, updated_at) FROM stdin;
1	1	sasd	dsasd	sdasd	sda	t	2026-07-03 20:43:46	2026-07-24 20:30:03
\.


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: campana_cliente; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.campana_cliente (id, id_campana, id_cliente, created_at, updated_at) FROM stdin;
1	1	1	\N	\N
\.


--
-- Data for Name: campanas; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.campanas (id, id_tenant, nombre_compania, segmento, estado, fecha_inicio, created_at, updated_at) FROM stdin;
1	1	sdasddas	dfsswddas	activa	2026-07-15	2026-07-03 20:43:23	2026-07-03 20:43:23
\.


--
-- Data for Name: campanas_marketing; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.campanas_marketing (id, id_tenant, id_usuario, nombre_compania, segmento, estado, fecha_inicio, lista_contactos, deleted_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: categorias; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.categorias (id_categoria, id_tenant, nombre, descripcion, activo, deleted_at, created_at, updated_at) FROM stdin;
1	1	Alimentos	Comida y alimentos	t	\N	2026-07-03 18:23:58	2026-07-03 18:23:58
2	1	Bebidas	Bebidas frías y calientes	t	\N	2026-07-03 18:23:58	2026-07-03 18:23:58
3	1	Snacks	Botanas y snacks	t	\N	2026-07-03 18:23:58	2026-07-03 18:23:58
5	31	Principales	\N	t	\N	2026-07-21 16:46:46	2026-07-21 16:46:46
6	32	Room Service	\N	t	\N	2026-07-21 17:36:41	2026-07-21 17:36:41
7	33	Medicamentos	\N	t	\N	2026-07-21 17:36:42	2026-07-21 17:36:42
8	34	General	\N	t	\N	2026-07-21 18:47:45	2026-07-21 18:47:45
9	1	Categoria Test Tenant Fix	prueba	t	2026-08-06 21:46:04	2026-08-06 21:44:40	2026-08-06 21:46:04
10	1	Bebidas Editado	Categoria de prueba e2e	t	2026-08-06 21:46:04	2026-08-06 21:45:06	2026-08-06 21:46:04
11	35	Gamesa	galletas	t	\N	2026-08-06 21:47:43	2026-08-06 21:47:43
\.


--
-- Data for Name: clientes; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.clientes (id_cliente, id_tenant, nombre, apellido_p, apellido_m, email, telefono, empresa, rfc, direccion, tipo, activo, deleted_at, created_at, updated_at, sector_empresarial) FROM stdin;
1	1	gg	\N	\N	israel.2014flores@gmail.com	233343234	\N	\N	sdd	empresa	t	2026-07-03 22:56:10	2026-07-03 20:29:54	2026-07-03 22:56:10	Software
2	14	Juan Pérez	\N	\N	\N	\N	\N	\N	\N	persona	t	\N	2026-07-03 20:32:44	2026-07-03 20:32:44	\N
3	14	María López	\N	\N	\N	\N	\N	\N	\N	persona	t	\N	2026-07-03 20:32:44	2026-07-03 20:32:44	\N
4	14	Carlos Ruiz	\N	\N	\N	\N	\N	\N	\N	persona	t	\N	2026-07-03 20:32:44	2026-07-03 20:32:44	\N
5	15	Ana García	\N	\N	ana@ejemplo.com	5551234567	\N	\N	\N	persona	t	\N	2026-07-03 21:31:37	2026-07-03 21:31:37	\N
7	19	Ana García	\N	\N	ana@ejemplo.com	\N	\N	\N	\N	persona	t	\N	2026-07-03 21:39:54	2026-07-03 21:39:54	\N
8	21	Roberto Sánchez	\N	\N	\N	\N	\N	\N	\N	persona	t	\N	2026-07-03 22:17:34	2026-07-03 22:17:34	\N
9	22	Fernanda Ruiz	\N	\N	\N	\N	\N	\N	\N	empresa	t	\N	2026-07-03 22:24:52	2026-07-03 22:24:52	Tecnología
10	14	Cliente Desechable	\N	\N	\N	\N	\N	\N	\N	empresa	t	2026-07-03 22:52:39	2026-07-03 22:52:38	2026-07-03 22:52:39	Tecnología
11	1	sdds	\N	\N	israel.2014flores@gmail.com	12334342234	\N	\N	sdd	empresa	t	\N	2026-07-03 23:06:38	2026-07-03 23:06:38	Tecnología
14	31	Público General	\N	\N	\N	\N	\N	\N	\N	persona	t	\N	2026-07-21 16:54:09	2026-07-21 16:54:09	\N
15	32	Público General	\N	\N	\N	\N	\N	\N	\N	persona	t	\N	2026-07-21 17:37:03	2026-07-21 17:37:03	\N
16	33	María García	\N	\N	\N	555-1234	\N	\N	\N	persona	t	\N	2026-07-21 17:37:26	2026-07-21 17:37:26	\N
17	34	Distribuidora del Norte	\N	\N	\N	555-9999	\N	\N	\N	empresa	t	\N	2026-07-21 18:47:54	2026-07-21 18:47:54	\N
18	1	Público General	\N	\N	\N	\N	\N	\N	\N	persona	t	\N	2026-07-24 20:25:09	2026-07-24 20:25:09	\N
19	35	Público General	\N	\N	\N	\N	\N	\N	\N	persona	t	\N	2026-08-06 22:05:03	2026-08-06 22:05:03	\N
20	35	hgbb	\N	\N	israel.2014flores@icloud.com	54165216351	\N	\N	yufghgvhvjhfvf 234 bhjbjhb	empresa	t	\N	2026-08-07 14:01:18	2026-08-07 14:01:18	Tecnología
21	35	sfdsxg	\N	\N	orfrvc@jhnjn.com	656926526	\N	\N	\N	persona	t	\N	2026-08-07 14:02:37	2026-08-07 14:02:37	\N
\.


--
-- Data for Name: contactos; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.contactos (id_contacto, id_tenant, id_cliente, nombre, apellido_p, apellido_m, email, telefono, cargo, principal, deleted_at, created_at, updated_at) FROM stdin;
2	1	18	Juan	Pérez	\N	juan.perez@test.com	555-1234	Director de Compras	f	2026-08-07 15:20:11	2026-08-07 15:20:06	2026-08-07 15:20:11
\.


--
-- Data for Name: erp_asiento_detalles; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.erp_asiento_detalles (id, id_asiento, id_cuenta, debe, haber, descripcion, created_at, updated_at) FROM stdin;
1	1	17	22.00	0.00	gastos varios	2026-07-23 21:20:42	2026-07-23 21:20:42
2	1	6	0.00	22.00	pago a florista	2026-07-23 21:20:42	2026-07-23 21:20:42
3	2	6	10.00	0.00	test	2026-07-23 21:20:42	2026-07-23 21:20:42
4	2	14	0.00	10.00	General	2026-07-23 21:20:42	2026-07-23 21:20:42
5	3	6	1.00	0.00	test	2026-07-23 21:20:42	2026-07-23 21:20:42
6	3	14	0.00	1.00	test	2026-07-23 21:20:42	2026-07-23 21:20:42
7	4	414	44.00	0.00	Cobro pedido #17	2026-07-23 21:23:20	2026-07-23 21:23:20
8	4	421	0.00	44.00	Venta pedido #17	2026-07-23 21:23:20	2026-07-23 21:23:20
9	4	423	44.00	0.00	Costo de venta pedido #17	2026-07-23 21:23:20	2026-07-23 21:23:20
10	4	416	0.00	44.00	Salida de inventario pedido #17	2026-07-23 21:23:20	2026-07-23 21:23:20
11	5	416	150.00	0.00	Recepción orden #2	2026-07-23 21:23:58	2026-07-23 21:23:58
12	5	417	0.00	150.00	Orden #2 a crédito	2026-07-23 21:23:58	2026-07-23 21:23:58
13	6	424	500.00	0.00	Gasto de nómina	2026-07-23 21:24:13	2026-07-23 21:24:13
14	6	414	0.00	500.00	Pago de nómina	2026-07-23 21:24:13	2026-07-23 21:24:13
15	7	424	0.00	500.00	Reversión: Gasto de nómina	2026-07-23 21:24:30	2026-07-23 21:24:30
16	7	414	500.00	0.00	Reversión: Pago de nómina	2026-07-23 21:24:30	2026-07-23 21:24:30
17	8	424	500.00	0.00	Gasto de nómina	2026-07-23 21:32:31	2026-07-23 21:32:31
18	8	414	0.00	500.00	Pago de nómina	2026-07-23 21:32:31	2026-07-23 21:32:31
19	9	506	44.00	0.00	\N	2026-07-23 21:42:57	2026-07-23 21:42:57
20	9	499	0.00	44.00	\N	2026-07-23 21:42:57	2026-07-23 21:42:57
21	10	499	55.00	0.00	\N	2026-07-23 21:43:17	2026-07-23 21:43:17
22	10	500	0.00	55.00	\N	2026-07-23 21:43:17	2026-07-23 21:43:17
23	11	414	10.00	0.00	\N	2026-07-23 21:46:10	2026-07-23 21:46:10
24	11	415	0.00	10.00	\N	2026-07-23 21:46:10	2026-07-23 21:46:10
25	12	500	74.00	0.00	\N	2026-07-23 21:53:01	2026-07-23 21:53:01
26	12	499	0.00	74.00	\N	2026-07-23 21:53:01	2026-07-23 21:53:01
27	13	6	193.00	0.00	Cobro pedido #18	2026-07-24 20:25:09	2026-07-24 20:25:09
28	13	13	0.00	193.00	Venta pedido #18	2026-07-24 20:25:09	2026-07-24 20:25:09
29	14	6	10.00	0.00	Cobro pedido #19 (efectivo)	2026-08-06 20:40:01	2026-08-06 20:40:01
30	14	645	10.00	0.00	Cobro pedido #19 (tarjeta_debito)	2026-08-06 20:40:01	2026-08-06 20:40:01
31	14	13	0.00	20.00	Venta pedido #19	2026-08-06 20:40:01	2026-08-06 20:40:01
32	15	6	30.00	0.00	Cobro pedido #21 (efectivo)	2026-08-06 20:40:53	2026-08-06 20:40:53
33	15	645	30.00	0.00	Cobro pedido #21 (tarjeta_debito)	2026-08-06 20:40:53	2026-08-06 20:40:53
34	15	646	30.00	0.00	Cobro pedido #21 (tarjeta_credito)	2026-08-06 20:40:53	2026-08-06 20:40:53
35	15	13	0.00	90.00	Venta pedido #21	2026-08-06 20:40:53	2026-08-06 20:40:53
36	16	499	5.00	0.00	Cobro pedido #22 (efectivo)	2026-08-06 22:05:03	2026-08-06 22:05:03
37	16	506	0.00	5.00	Venta pedido #22	2026-08-06 22:05:03	2026-08-06 22:05:03
38	16	508	5.00	0.00	Costo de venta pedido #22	2026-08-06 22:05:03	2026-08-06 22:05:03
39	16	501	0.00	5.00	Salida de inventario pedido #22	2026-08-06 22:05:03	2026-08-06 22:05:03
40	17	499	5.00	0.00	Cobro pedido #23 (efectivo)	2026-08-06 22:05:59	2026-08-06 22:05:59
41	17	506	0.00	5.00	Venta pedido #23	2026-08-06 22:05:59	2026-08-06 22:05:59
42	17	508	5.00	0.00	Costo de venta pedido #23	2026-08-06 22:05:59	2026-08-06 22:05:59
43	17	501	0.00	5.00	Salida de inventario pedido #23	2026-08-06 22:05:59	2026-08-06 22:05:59
44	18	499	5.00	0.00	Cobro pedido #24 (efectivo)	2026-08-06 22:08:28	2026-08-06 22:08:28
45	18	506	0.00	5.00	Venta pedido #24	2026-08-06 22:08:28	2026-08-06 22:08:28
46	18	508	5.00	0.00	Costo de venta pedido #24	2026-08-06 22:08:28	2026-08-06 22:08:28
47	18	501	0.00	5.00	Salida de inventario pedido #24	2026-08-06 22:08:28	2026-08-06 22:08:28
48	19	499	15.00	0.00	Cobro pedido #25 (efectivo)	2026-08-06 22:10:42	2026-08-06 22:10:42
49	19	506	0.00	15.00	Venta pedido #25	2026-08-06 22:10:42	2026-08-06 22:10:42
50	19	508	15.00	0.00	Costo de venta pedido #25	2026-08-06 22:10:42	2026-08-06 22:10:42
51	19	501	0.00	15.00	Salida de inventario pedido #25	2026-08-06 22:10:42	2026-08-06 22:10:42
52	20	499	100.00	0.00	Cobro pedido #26 (efectivo)	2026-08-06 22:40:38	2026-08-06 22:40:38
53	20	506	0.00	100.00	Venta pedido #26	2026-08-06 22:40:38	2026-08-06 22:40:38
54	21	499	5.00	0.00	Cobro pedido #27 (efectivo)	2026-08-06 22:47:48	2026-08-06 22:47:48
55	21	506	0.00	5.00	Venta pedido #27	2026-08-06 22:47:48	2026-08-06 22:47:48
56	21	508	5.00	0.00	Costo de venta pedido #27	2026-08-06 22:47:48	2026-08-06 22:47:48
57	21	501	0.00	5.00	Salida de inventario pedido #27	2026-08-06 22:47:48	2026-08-06 22:47:48
58	22	499	5.00	0.00	Cobro pedido #28 (efectivo)	2026-08-06 22:58:10	2026-08-06 22:58:10
59	22	506	0.00	5.00	Venta pedido #28	2026-08-06 22:58:10	2026-08-06 22:58:10
60	22	508	5.00	0.00	Costo de venta pedido #28	2026-08-06 22:58:10	2026-08-06 22:58:10
61	22	501	0.00	5.00	Salida de inventario pedido #28	2026-08-06 22:58:10	2026-08-06 22:58:10
62	23	499	60.00	0.00	Cobro pedido #29 (efectivo)	2026-08-07 17:49:38	2026-08-07 17:49:38
63	23	506	0.00	60.00	Venta pedido #29	2026-08-07 17:49:38	2026-08-07 17:49:38
64	23	508	10.00	0.00	Costo de venta pedido #29	2026-08-07 17:49:38	2026-08-07 17:49:38
65	23	501	0.00	10.00	Salida de inventario pedido #29	2026-08-07 17:49:38	2026-08-07 17:49:38
\.


--
-- Data for Name: erp_asientos; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.erp_asientos (id, id_tenant, fecha, concepto, origen, referencia_tipo, referencia_id, total_debe, total_haber, id_usuario, created_at, updated_at) FROM stdin;
1	1	2026-07-08	pago a florista	migracion	App\\Models\\Erp\\Movimiento	3	22.00	22.00	\N	2026-07-23 21:20:42	2026-07-23 21:20:42
2	1	2026-07-08	test	migracion	App\\Models\\Erp\\Movimiento	4	10.00	10.00	\N	2026-07-23 21:20:42	2026-07-23 21:20:42
3	1	2026-07-16	test	migracion	App\\Models\\Erp\\Movimiento	5	1.00	1.00	\N	2026-07-23 21:20:42	2026-07-23 21:20:42
4	31	2026-07-23	Venta facturada — Pedido #17	venta	App\\Models\\Erp\\Pedido	17	88.00	88.00	\N	2026-07-23 21:23:20	2026-07-23 21:23:20
5	31	2026-07-23	Compra recibida — Orden #2	compra	App\\Models\\Erp\\OrdenCompra	2	150.00	150.00	\N	2026-07-23 21:23:58	2026-07-23 21:23:58
6	31	2026-07-23	Pago de nómina	nomina	\N	\N	500.00	500.00	\N	2026-07-23 21:24:13	2026-07-23 21:24:13
7	31	2026-07-23	Reversión de asiento #6 — Pago de nómina	ajuste	App\\Models\\Erp\\Asiento	6	500.00	500.00	39	2026-07-23 21:24:30	2026-07-23 21:24:30
8	31	2026-07-23	Pago de nómina	nomina	\N	\N	500.00	500.00	\N	2026-07-23 21:32:31	2026-07-23 21:32:31
9	35	2026-07-23	pago en linea	manual	\N	\N	44.00	44.00	43	2026-07-23 21:42:57	2026-07-23 21:42:57
10	35	2026-07-23	vsv	manual	\N	\N	55.00	55.00	43	2026-07-23 21:43:17	2026-07-23 21:43:17
11	31	2026-07-23	Prueba registro en vivo	manual	\N	\N	10.00	10.00	39	2026-07-23 21:46:10	2026-07-23 21:46:10
12	35	2026-07-23	asd	manual	\N	\N	74.00	74.00	43	2026-07-23 21:53:01	2026-07-23 21:53:01
13	1	2026-07-24	Venta facturada — Pedido #18	venta	App\\Models\\Erp\\Pedido	18	193.00	193.00	\N	2026-07-24 20:25:09	2026-07-24 20:25:09
14	1	2026-08-06	Venta facturada — Pedido #19	venta	App\\Models\\Erp\\Pedido	19	20.00	20.00	\N	2026-08-06 20:40:01	2026-08-06 20:40:01
15	1	2026-08-06	Venta facturada — Pedido #21	venta	App\\Models\\Erp\\Pedido	21	90.00	90.00	\N	2026-08-06 20:40:53	2026-08-06 20:40:53
16	35	2026-08-06	Venta facturada — Pedido #22	venta	App\\Models\\Erp\\Pedido	22	10.00	10.00	\N	2026-08-06 22:05:03	2026-08-06 22:05:03
17	35	2026-08-06	Venta facturada — Pedido #23	venta	App\\Models\\Erp\\Pedido	23	10.00	10.00	\N	2026-08-06 22:05:59	2026-08-06 22:05:59
18	35	2026-08-06	Venta facturada — Pedido #24	venta	App\\Models\\Erp\\Pedido	24	10.00	10.00	\N	2026-08-06 22:08:28	2026-08-06 22:08:28
19	35	2026-08-06	Venta facturada — Pedido #25	venta	App\\Models\\Erp\\Pedido	25	30.00	30.00	\N	2026-08-06 22:10:42	2026-08-06 22:10:42
20	35	2026-08-06	Venta facturada — Pedido #26	venta	App\\Models\\Erp\\Pedido	26	100.00	100.00	\N	2026-08-06 22:40:38	2026-08-06 22:40:38
21	35	2026-08-06	Venta facturada — Pedido #27	venta	App\\Models\\Erp\\Pedido	27	10.00	10.00	\N	2026-08-06 22:47:48	2026-08-06 22:47:48
22	35	2026-08-06	Venta facturada — Pedido #28	venta	App\\Models\\Erp\\Pedido	28	10.00	10.00	\N	2026-08-06 22:58:10	2026-08-06 22:58:10
23	35	2026-08-07	Venta facturada — Pedido #29	venta	App\\Models\\Erp\\Pedido	29	70.00	70.00	\N	2026-08-07 17:49:38	2026-08-07 17:49:38
\.


--
-- Data for Name: erp_comanda_items; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.erp_comanda_items (id, id_comanda, id_producto, nombre, precio_unitario, cantidad, created_at, updated_at) FROM stdin;
3	2	12	Refresco	35.00	1	2026-07-21 22:15:03	2026-07-21 22:15:03
4	4	18	dasd	22.00	1	2026-07-21 22:39:52	2026-07-21 22:39:52
6	6	11	Filete de Res	220.00	1	2026-07-22 14:48:09	2026-07-22 14:48:09
7	7	11	Filete de Res	220.00	1	2026-07-22 14:49:07	2026-07-22 14:49:07
8	8	11	Filete de Res	220.00	1	2026-07-22 18:12:45	2026-07-22 18:12:45
9	9	18	dasd	22.00	2	2026-07-23 21:23:15	2026-07-23 21:23:15
10	10	21	Producto Con Foto 2	25.00	2	2026-08-07 20:38:55	2026-08-07 20:38:57
\.


--
-- Data for Name: erp_comandas; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.erp_comandas (id, id_tenant, id_mesa, estado, enviada_cocina, total, created_at, updated_at) FROM stdin;
2	31	3	cerrada	t	35.00	2026-07-21 21:56:01	2026-07-21 22:15:37
4	31	4	cerrada	f	22.00	2026-07-21 22:39:51	2026-07-21 22:39:55
6	31	3	cerrada	f	220.00	2026-07-22 14:47:31	2026-07-22 14:48:15
7	31	3	cerrada	f	220.00	2026-07-22 14:49:05	2026-07-22 14:49:10
8	31	3	cerrada	f	220.00	2026-07-22 18:12:43	2026-07-22 18:12:49
9	31	3	cerrada	f	44.00	2026-07-23 21:23:15	2026-07-23 21:23:20
10	35	13	abierta	f	50.00	2026-08-07 20:38:55	2026-08-07 20:38:57
\.


--
-- Data for Name: erp_empleados; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.erp_empleados (id, id_tenant, nombre, departamento, puesto, estado, salario, created_at, updated_at) FROM stdin;
2	1	fs	DDdf	DD	activo	2334.00	2026-07-08 14:59:40	2026-07-08 14:59:40
3	1	rogelio martinez perez	sistemas	programador	activo	200000.00	2026-07-08 22:00:03	2026-07-08 22:00:03
4	1	josmar hernandez hernandez	sistemas	Operativo	activo	50000.00	2026-07-08 22:00:37	2026-07-08 22:00:37
5	31	Juan Perez	Cocina	Chef	activo	500.00	2026-07-23 21:24:12	2026-07-23 21:24:12
\.


--
-- Data for Name: erp_envios; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.erp_envios (id, id_tenant, destino, transportista, eta, estado, created_at, updated_at, deleted_at) FROM stdin;
1	1	Sucursal Norte	DHL	2 días	entregado	2026-07-08 16:35:08	2026-07-08 16:35:09	\N
2	1	test	test	1 dia	en_transito	2026-07-08 16:55:47	2026-07-08 16:55:47	\N
\.


--
-- Data for Name: erp_habitacion_consumos; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.erp_habitacion_consumos (id, id_habitacion, id_producto, nombre, precio_unitario, cantidad, created_at, updated_at) FROM stdin;
8	8	19	tets	25.00	13	2026-07-24 20:28:07	2026-07-24 20:28:10
9	8	7	Chocolatín	20.00	1	2026-08-06 20:37:42	2026-08-06 20:37:42
\.


--
-- Data for Name: erp_habitaciones; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.erp_habitaciones (id, id_tenant, numero, tipo, piso, estado, huesped, check_in, check_out, noches, created_at, updated_at) FROM stdin;
1	32	101	Dbl	1	libre	\N	\N	\N	\N	2026-07-21 17:36:50	2026-07-21 22:30:01
2	32	3	Sgl	1	ocupada	po	2026-07-21	2026-07-22	1	2026-07-21 22:22:29	2026-07-21 22:31:15
3	37	1	Sgl	1	libre	\N	\N	\N	\N	2026-07-23 22:25:13	2026-07-23 22:25:13
4	37	2	Sgl	1	libre	\N	\N	\N	\N	2026-07-23 22:25:13	2026-07-23 22:25:13
5	37	3	Sgl	1	libre	\N	\N	\N	\N	2026-07-23 22:25:13	2026-07-23 22:25:13
6	37	4	Sgl	1	libre	\N	\N	\N	\N	2026-07-23 22:25:13	2026-07-23 22:25:13
7	37	5	Sgl	1	libre	\N	\N	\N	\N	2026-07-23 22:25:13	2026-07-23 22:25:13
8	1	2585	Sgl	7	ocupada	sd	2026-07-24	2026-07-25	1	2026-07-24 20:24:18	2026-07-24 20:28:02
9	1	985	Sgl	9	libre	\N	\N	\N	\N	2026-08-06 20:38:42	2026-08-06 20:38:42
10	1	918	Sgl	9	libre	\N	\N	\N	\N	2026-08-06 20:39:50	2026-08-06 20:40:01
\.


--
-- Data for Name: erp_mesas; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.erp_mesas (id, id_tenant, numero, capacidad, estado, mesero, created_at, updated_at) FROM stdin;
2	31	9	2	libre	\N	2026-07-21 16:54:26	2026-07-21 22:40:44
4	31	7	4	libre	\N	2026-07-21 21:55:40	2026-07-21 22:39:55
3	31	5	4	libre	\N	2026-07-21 21:53:42	2026-07-23 21:23:20
5	39	1	2	libre	\N	2026-07-23 22:37:55	2026-07-23 22:37:55
6	39	2	2	libre	\N	2026-07-23 22:37:55	2026-07-23 22:37:55
7	39	3	2	libre	\N	2026-07-23 22:37:55	2026-07-23 22:37:55
8	39	4	2	libre	\N	2026-07-23 22:37:55	2026-07-23 22:37:55
9	39	5	2	libre	\N	2026-07-23 22:37:55	2026-07-23 22:37:55
10	39	6	2	libre	\N	2026-07-23 22:37:55	2026-07-23 22:37:55
11	39	7	2	libre	\N	2026-07-23 22:37:55	2026-07-23 22:37:55
12	39	8	2	libre	\N	2026-07-23 22:37:55	2026-07-23 22:37:55
13	35	8	8	libre	\N	2026-08-07 20:38:51	2026-08-07 20:38:51
\.


--
-- Data for Name: erp_movimientos; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.erp_movimientos (id, id_tenant, concepto, tipo, monto, fecha, categoria, created_at, updated_at, deleted_at) FROM stdin;
3	1	pago a florista	egreso	22.00	2026-07-08	gastos varios	2026-07-08 16:37:27	2026-07-08 16:37:27	\N
4	1	test	ingreso	10.00	2026-07-08	General	2026-07-08 16:55:46	2026-07-08 16:55:47	\N
5	1	test	ingreso	1.00	2026-07-16	test	2026-07-16 22:28:37	2026-07-16 22:28:44	2026-07-16 22:28:44
\.


--
-- Data for Name: erp_movimientos_stock; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.erp_movimientos_stock (id, id_tenant, id_producto, tipo, cantidad, motivo, referencia, stock_resultante, created_at, updated_at) FROM stdin;
1	1	8	entrada	20	compra	orden_compra:1	30	2026-07-08 15:29:08	2026-07-08 15:29:08
2	1	8	salida	5	venta	pedido:1	25	2026-07-08 15:29:24	2026-07-08 15:29:24
3	1	8	entrada	5	cancelacion_venta	pedido:1	30	2026-07-08 15:29:50	2026-07-08 15:29:50
4	1	8	ajuste	3	merma	\N	27	2026-07-08 15:29:51	2026-07-08 15:29:51
5	1	1	salida	2	venta	pedido:2	48	2026-07-08 16:01:34	2026-07-08 16:01:34
6	1	1	entrada	2	cancelacion_venta	pedido:2	50	2026-07-08 17:05:40	2026-07-08 17:05:40
7	1	7	ajuste	6	/7478	\N	41	2026-07-15 22:06:38	2026-07-15 22:06:38
8	1	7	ajuste	58125	kjij	\N	58166	2026-07-15 22:06:47	2026-07-15 22:06:47
10	31	11	salida	2	venta_mesa	mesa:1·pedido:4	48	2026-07-21 16:54:09	2026-07-21 16:54:09
11	31	12	salida	1	venta_mesa	mesa:1·pedido:4	99	2026-07-21 16:54:09	2026-07-21 16:54:09
12	32	13	salida	2	consumo_habitacion	habitacion:101·pedido:5	198	2026-07-21 17:37:03	2026-07-21 17:37:03
13	32	14	salida	1	consumo_habitacion	habitacion:101·pedido:5	49	2026-07-21 17:37:03	2026-07-21 17:37:03
14	33	15	salida	21	dispensacion	receta:1·pedido:6	479	2026-07-21 17:37:39	2026-07-21 17:37:39
15	33	16	salida	10	dispensacion	receta:2·pedido:6	990	2026-07-21 17:37:39	2026-07-21 17:37:39
16	34	17	salida	5	venta	pedido:7	495	2026-07-21 18:47:54	2026-07-21 18:47:54
17	31	12	salida	1	venta_mesa	mesa:5·pedido:8	98	2026-07-21 22:15:37	2026-07-21 22:15:37
18	32	14	salida	1	consumo_habitacion	habitacion:101·pedido:9	48	2026-07-21 22:29:16	2026-07-21 22:29:16
19	32	13	salida	1	consumo_habitacion	habitacion:101·pedido:9	197	2026-07-21 22:29:16	2026-07-21 22:29:16
20	32	13	salida	3	consumo_habitacion	habitacion:101·pedido:10	194	2026-07-21 22:30:01	2026-07-21 22:30:01
21	32	14	salida	1	consumo_habitacion	habitacion:101·pedido:10	47	2026-07-21 22:30:01	2026-07-21 22:30:01
22	33	16	salida	1	dispensacion	receta:3·pedido:11	989	2026-07-21 22:34:49	2026-07-21 22:34:49
23	31	18	salida	1	venta_mesa	mesa:7·pedido:12	20	2026-07-21 22:39:55	2026-07-21 22:39:55
24	33	16	salida	1	dispensacion	receta:4·pedido:13	988	2026-07-21 22:54:05	2026-07-21 22:54:05
25	31	11	salida	1	venta_mesa	mesa:5·pedido:14	47	2026-07-22 14:48:15	2026-07-22 14:48:15
26	31	11	salida	1	venta_mesa	mesa:5·pedido:15	46	2026-07-22 14:49:10	2026-07-22 14:49:10
27	31	11	salida	1	venta_mesa	mesa:5·pedido:16	45	2026-07-22 18:12:49	2026-07-22 18:12:49
28	31	18	salida	2	venta_mesa	mesa:5·pedido:17	18	2026-07-23 21:23:20	2026-07-23 21:23:20
29	31	18	entrada	10	compra	orden_compra:2	28	2026-07-23 21:23:58	2026-07-23 21:23:58
30	1	6	salida	1	consumo_habitacion	habitacion:2585·pedido:18	39	2026-07-24 20:25:09	2026-07-24 20:25:09
31	1	7	salida	1	consumo_habitacion	habitacion:2585·pedido:18	58165	2026-07-24 20:25:09	2026-07-24 20:25:09
32	1	4	salida	1	consumo_habitacion	habitacion:2585·pedido:18	79	2026-07-24 20:25:09	2026-07-24 20:25:09
33	1	1	salida	1	consumo_habitacion	habitacion:2585·pedido:18	49	2026-07-24 20:25:09	2026-07-24 20:25:09
34	1	2	salida	1	consumo_habitacion	habitacion:2585·pedido:18	29	2026-07-24 20:25:09	2026-07-24 20:25:09
35	1	5	salida	1	consumo_habitacion	habitacion:2585·pedido:18	59	2026-07-24 20:25:09	2026-07-24 20:25:09
36	1	7	salida	1	consumo_habitacion	habitacion:918·pedido:19	58164	2026-08-06 20:40:01	2026-08-06 20:40:01
38	1	7	salida	1	venta	pedido:21	58163	2026-08-06 20:40:53	2026-08-06 20:40:53
39	35	20	salida	1	venta	pedido:22	55	2026-08-06 22:05:03	2026-08-06 22:05:03
40	35	20	salida	1	venta	pedido:23	54	2026-08-06 22:05:59	2026-08-06 22:05:59
41	35	20	salida	1	venta	pedido:24	53	2026-08-06 22:08:28	2026-08-06 22:08:28
42	35	20	salida	3	venta	pedido:25	50	2026-08-06 22:10:42	2026-08-06 22:10:42
43	35	21	salida	4	venta	pedido:26	6	2026-08-06 22:40:38	2026-08-06 22:40:38
44	35	20	salida	1	venta	pedido:27	49	2026-08-06 22:47:48	2026-08-06 22:47:48
45	35	20	salida	1	venta	pedido:28	48	2026-08-06 22:58:10	2026-08-06 22:58:10
46	35	20	salida	2	venta	pedido:29	46	2026-08-07 17:49:38	2026-08-07 17:49:38
47	35	21	salida	2	venta	pedido:29	4	2026-08-07 17:49:38	2026-08-07 17:49:38
\.


--
-- Data for Name: erp_nomina_pago_detalles; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.erp_nomina_pago_detalles (id, id_nomina_pago, id_empleado, salario, created_at, updated_at) FROM stdin;
1	1	5	500.00	2026-07-23 21:24:13	2026-07-23 21:24:13
2	2	5	500.00	2026-07-23 21:32:31	2026-07-23 21:32:31
\.


--
-- Data for Name: erp_nomina_pagos; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.erp_nomina_pagos (id, id_tenant, fecha, total, id_asiento, created_at, updated_at) FROM stdin;
1	31	2026-07-23	500.00	6	2026-07-23 21:24:13	2026-07-23 21:24:13
2	31	2026-07-23	500.00	8	2026-07-23 21:32:31	2026-07-23 21:32:31
\.


--
-- Data for Name: erp_orden_compra_items; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.erp_orden_compra_items (id, id_orden_compra, id_producto, cantidad, precio_unitario, subtotal, created_at, updated_at) FROM stdin;
1	1	8	20	8.50	170.00	2026-07-08 15:28:49	2026-07-08 15:28:49
2	2	18	10	15.00	150.00	2026-07-23 21:23:58	2026-07-23 21:23:58
\.


--
-- Data for Name: erp_ordenes_compra; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.erp_ordenes_compra (id, id_tenant, fecha, estado, total, created_at, updated_at, id_proveedor) FROM stdin;
1	1	2026-07-08	recibida	170.00	2026-07-08 15:28:49	2026-07-08 15:29:08	1
2	31	2026-07-23	recibida	150.00	2026-07-23 21:23:58	2026-07-23 21:23:58	4
\.


--
-- Data for Name: erp_ordenes_produccion; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.erp_ordenes_produccion (id, id_tenant, producto, cantidad, progreso, estado, created_at, updated_at) FROM stdin;
1	1	dfsd	222	0	en proceso	2026-07-08 14:59:06	2026-07-08 14:59:06
\.


--
-- Data for Name: erp_pedido_items; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.erp_pedido_items (id, id_pedido, id_producto, cantidad, precio_unitario, subtotal, created_at, updated_at, costo_unitario) FROM stdin;
1	1	8	5	18.00	90.00	2026-07-08 15:29:24	2026-07-08 15:29:24	\N
2	2	1	2	55.00	110.00	2026-07-08 16:01:34	2026-07-08 16:01:34	\N
4	4	11	2	220.00	440.00	2026-07-21 16:54:09	2026-07-21 16:54:09	\N
5	4	12	1	35.00	35.00	2026-07-21 16:54:09	2026-07-21 16:54:09	\N
6	5	13	2	180.00	360.00	2026-07-21 17:37:03	2026-07-21 17:37:03	\N
7	5	14	1	280.00	280.00	2026-07-21 17:37:03	2026-07-21 17:37:03	\N
8	6	15	21	8.00	168.00	2026-07-21 17:37:39	2026-07-21 17:37:39	\N
9	6	16	10	3.00	30.00	2026-07-21 17:37:39	2026-07-21 17:37:39	\N
10	7	17	5	50.00	250.00	2026-07-21 18:47:54	2026-07-21 18:47:54	\N
11	8	12	1	35.00	35.00	2026-07-21 22:15:37	2026-07-21 22:15:37	\N
12	9	14	1	280.00	280.00	2026-07-21 22:29:16	2026-07-21 22:29:16	\N
13	9	13	1	180.00	180.00	2026-07-21 22:29:16	2026-07-21 22:29:16	\N
14	10	13	3	180.00	540.00	2026-07-21 22:30:01	2026-07-21 22:30:01	\N
15	10	14	1	280.00	280.00	2026-07-21 22:30:01	2026-07-21 22:30:01	\N
16	11	16	1	3.00	3.00	2026-07-21 22:34:49	2026-07-21 22:34:49	\N
17	12	18	1	22.00	22.00	2026-07-21 22:39:55	2026-07-21 22:39:55	\N
18	13	16	1	3.00	3.00	2026-07-21 22:54:05	2026-07-21 22:54:05	\N
19	14	11	1	220.00	220.00	2026-07-22 14:48:15	2026-07-22 14:48:15	\N
20	15	11	1	220.00	220.00	2026-07-22 14:49:10	2026-07-22 14:49:10	\N
21	16	11	1	220.00	220.00	2026-07-22 18:12:49	2026-07-22 18:12:49	\N
22	17	18	2	22.00	44.00	2026-07-23 21:23:20	2026-07-23 21:23:20	22.00
23	18	6	1	16.00	16.00	2026-07-24 20:25:09	2026-07-24 20:25:09	0.00
24	18	7	1	20.00	20.00	2026-07-24 20:25:09	2026-07-24 20:25:09	0.00
25	18	4	1	22.00	22.00	2026-07-24 20:25:09	2026-07-24 20:25:09	0.00
26	18	1	1	55.00	55.00	2026-07-24 20:25:09	2026-07-24 20:25:09	0.00
27	18	2	1	45.00	45.00	2026-07-24 20:25:09	2026-07-24 20:25:09	0.00
28	18	5	1	35.00	35.00	2026-07-24 20:25:09	2026-07-24 20:25:09	0.00
29	19	7	1	20.00	20.00	2026-08-06 20:40:01	2026-08-06 20:40:01	0.00
31	21	7	1	90.00	90.00	2026-08-06 20:40:53	2026-08-06 20:40:53	0.00
32	22	20	1	5.00	5.00	2026-08-06 22:05:03	2026-08-06 22:05:03	5.00
33	23	20	1	5.00	5.00	2026-08-06 22:05:59	2026-08-06 22:05:59	5.00
34	24	20	1	5.00	5.00	2026-08-06 22:08:28	2026-08-06 22:08:28	5.00
35	25	20	3	5.00	15.00	2026-08-06 22:10:42	2026-08-06 22:10:42	5.00
36	26	21	4	25.00	100.00	2026-08-06 22:40:38	2026-08-06 22:40:38	0.00
37	27	20	1	5.00	5.00	2026-08-06 22:47:48	2026-08-06 22:47:48	5.00
38	28	20	1	5.00	5.00	2026-08-06 22:58:10	2026-08-06 22:58:10	5.00
39	29	20	2	5.00	10.00	2026-08-07 17:49:38	2026-08-07 17:49:38	5.00
40	29	21	2	25.00	50.00	2026-08-07 17:49:38	2026-08-07 17:49:38	0.00
\.


--
-- Data for Name: erp_pedido_pagos; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.erp_pedido_pagos (id, id_pedido, metodo_pago, monto, created_at, updated_at) FROM stdin;
1	19	efectivo	10.00	2026-08-06 20:40:01	2026-08-06 20:40:01
2	19	tarjeta_debito	10.00	2026-08-06 20:40:01	2026-08-06 20:40:01
3	21	efectivo	30.00	2026-08-06 20:40:53	2026-08-06 20:40:53
4	21	tarjeta_debito	30.00	2026-08-06 20:40:53	2026-08-06 20:40:53
5	21	tarjeta_credito	30.00	2026-08-06 20:40:53	2026-08-06 20:40:53
6	22	efectivo	5.00	2026-08-06 22:05:03	2026-08-06 22:05:03
7	23	efectivo	5.00	2026-08-06 22:05:59	2026-08-06 22:05:59
8	24	efectivo	5.00	2026-08-06 22:08:28	2026-08-06 22:08:28
9	25	efectivo	15.00	2026-08-06 22:10:42	2026-08-06 22:10:42
10	26	efectivo	100.00	2026-08-06 22:40:38	2026-08-06 22:40:38
11	27	efectivo	5.00	2026-08-06 22:47:48	2026-08-06 22:47:48
12	28	efectivo	5.00	2026-08-06 22:58:10	2026-08-06 22:58:10
13	29	efectivo	60.00	2026-08-07 17:49:38	2026-08-07 17:49:38
\.


--
-- Data for Name: erp_pedidos_venta; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.erp_pedidos_venta (id, id_tenant, total, estado, fecha, created_at, updated_at, id_cliente) FROM stdin;
1	1	90.00	cancelada	2026-07-08	2026-07-08 15:29:24	2026-07-08 15:29:50	11
2	1	110.00	cancelada	2026-07-08	2026-07-08 16:01:34	2026-07-08 17:05:40	11
4	31	475.00	facturado	2026-07-21	2026-07-21 16:54:09	2026-07-21 16:54:09	14
5	32	640.00	facturado	2026-07-21	2026-07-21 17:37:03	2026-07-21 17:37:03	15
6	33	198.00	facturado	2026-07-21	2026-07-21 17:37:39	2026-07-21 17:37:39	16
7	34	250.00	pendiente	2026-07-21	2026-07-21 18:47:54	2026-07-21 18:47:54	17
8	31	35.00	facturado	2026-07-21	2026-07-21 22:15:37	2026-07-21 22:15:37	14
9	32	460.00	facturado	2026-07-21	2026-07-21 22:29:16	2026-07-21 22:29:16	15
10	32	820.00	facturado	2026-07-21	2026-07-21 22:30:01	2026-07-21 22:30:01	15
11	33	3.00	facturado	2026-07-21	2026-07-21 22:34:49	2026-07-21 22:34:49	16
12	31	22.00	facturado	2026-07-21	2026-07-21 22:39:55	2026-07-21 22:39:55	14
13	33	3.00	facturado	2026-07-21	2026-07-21 22:54:05	2026-07-21 22:54:05	16
14	31	220.00	facturado	2026-07-22	2026-07-22 14:48:15	2026-07-22 14:48:15	14
15	31	220.00	facturado	2026-07-22	2026-07-22 14:49:10	2026-07-22 14:49:10	14
16	31	220.00	facturado	2026-07-22	2026-07-22 18:12:49	2026-07-22 18:12:49	14
17	31	44.00	facturado	2026-07-23	2026-07-23 21:23:20	2026-07-23 21:23:20	14
18	1	193.00	facturado	2026-07-24	2026-07-24 20:25:09	2026-07-24 20:25:09	18
19	1	20.00	facturado	2026-08-06	2026-08-06 20:40:01	2026-08-06 20:40:01	18
21	1	90.00	facturado	2026-08-06	2026-08-06 20:40:53	2026-08-06 20:40:53	18
22	35	5.00	facturado	2026-08-06	2026-08-06 22:05:03	2026-08-06 22:05:03	19
23	35	5.00	facturado	2026-08-06	2026-08-06 22:05:59	2026-08-06 22:05:59	19
24	35	5.00	facturado	2026-08-06	2026-08-06 22:08:28	2026-08-06 22:08:28	19
25	35	15.00	facturado	2026-08-06	2026-08-06 22:10:42	2026-08-06 22:10:42	19
26	35	100.00	facturado	2026-08-06	2026-08-06 22:40:38	2026-08-06 22:40:38	19
27	35	5.00	facturado	2026-08-06	2026-08-06 22:47:48	2026-08-06 22:47:48	19
28	35	5.00	facturado	2026-08-06	2026-08-06 22:58:10	2026-08-06 22:58:10	19
29	35	60.00	facturado	2026-08-07	2026-08-07 17:49:38	2026-08-07 17:49:38	19
\.


--
-- Data for Name: erp_plan_cuentas; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.erp_plan_cuentas (id, id_tenant, codigo, nombre, tipo, naturaleza, id_cuenta_padre, es_movible, activo, created_at, updated_at) FROM stdin;
1	1	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:36	2026-07-23 21:20:36
2	1	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:36	2026-07-23 21:20:36
3	1	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:36	2026-07-23 21:20:36
4	1	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:36	2026-07-23 21:20:36
5	1	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:36	2026-07-23 21:20:36
6	1	1100	Caja y Bancos	activo	deudora	1	t	t	2026-07-23 21:20:36	2026-07-23 21:20:36
7	1	1200	Cuentas por Cobrar Clientes	activo	deudora	1	t	t	2026-07-23 21:20:36	2026-07-23 21:20:36
8	1	1300	Inventario	activo	deudora	1	t	t	2026-07-23 21:20:36	2026-07-23 21:20:36
9	1	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	2	t	t	2026-07-23 21:20:36	2026-07-23 21:20:36
10	1	2200	Nómina por Pagar	pasivo	acreedora	2	t	t	2026-07-23 21:20:36	2026-07-23 21:20:36
11	1	3100	Capital Social	capital	acreedora	3	t	t	2026-07-23 21:20:36	2026-07-23 21:20:36
12	1	3200	Resultados Acumulados	capital	acreedora	3	t	t	2026-07-23 21:20:36	2026-07-23 21:20:36
13	1	4100	Ingresos por Ventas	ingreso	acreedora	4	t	t	2026-07-23 21:20:36	2026-07-23 21:20:36
14	1	4900	Otros Ingresos	ingreso	acreedora	4	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
15	1	5100	Costo de Mercancía Vendida	costo	deudora	5	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
16	1	5200	Gastos de Nómina	gasto	deudora	5	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
17	1	5900	Gastos Generales	gasto	deudora	5	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
18	2	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
19	2	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
20	2	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
21	2	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
22	2	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
23	2	1100	Caja y Bancos	activo	deudora	18	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
24	2	1200	Cuentas por Cobrar Clientes	activo	deudora	18	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
25	2	1300	Inventario	activo	deudora	18	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
26	2	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	19	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
27	2	2200	Nómina por Pagar	pasivo	acreedora	19	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
28	2	3100	Capital Social	capital	acreedora	20	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
29	2	3200	Resultados Acumulados	capital	acreedora	20	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
30	2	4100	Ingresos por Ventas	ingreso	acreedora	21	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
31	2	4900	Otros Ingresos	ingreso	acreedora	21	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
32	2	5100	Costo de Mercancía Vendida	costo	deudora	22	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
33	2	5200	Gastos de Nómina	gasto	deudora	22	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
34	2	5900	Gastos Generales	gasto	deudora	22	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
35	3	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
36	3	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
37	3	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
38	3	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
39	3	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
40	3	1100	Caja y Bancos	activo	deudora	35	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
41	3	1200	Cuentas por Cobrar Clientes	activo	deudora	35	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
42	3	1300	Inventario	activo	deudora	35	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
43	3	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	36	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
44	3	2200	Nómina por Pagar	pasivo	acreedora	36	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
45	3	3100	Capital Social	capital	acreedora	37	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
46	3	3200	Resultados Acumulados	capital	acreedora	37	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
47	3	4100	Ingresos por Ventas	ingreso	acreedora	38	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
48	3	4900	Otros Ingresos	ingreso	acreedora	38	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
49	3	5100	Costo de Mercancía Vendida	costo	deudora	39	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
50	3	5200	Gastos de Nómina	gasto	deudora	39	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
51	3	5900	Gastos Generales	gasto	deudora	39	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
52	4	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
53	4	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
54	4	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
55	4	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
56	4	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
57	4	1100	Caja y Bancos	activo	deudora	52	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
58	4	1200	Cuentas por Cobrar Clientes	activo	deudora	52	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
59	4	1300	Inventario	activo	deudora	52	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
60	4	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	53	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
61	4	2200	Nómina por Pagar	pasivo	acreedora	53	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
62	4	3100	Capital Social	capital	acreedora	54	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
63	4	3200	Resultados Acumulados	capital	acreedora	54	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
64	4	4100	Ingresos por Ventas	ingreso	acreedora	55	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
65	4	4900	Otros Ingresos	ingreso	acreedora	55	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
66	4	5100	Costo de Mercancía Vendida	costo	deudora	56	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
67	4	5200	Gastos de Nómina	gasto	deudora	56	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
68	4	5900	Gastos Generales	gasto	deudora	56	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
69	5	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
70	5	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
71	5	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
72	5	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
73	5	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
74	5	1100	Caja y Bancos	activo	deudora	69	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
75	5	1200	Cuentas por Cobrar Clientes	activo	deudora	69	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
76	5	1300	Inventario	activo	deudora	69	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
77	5	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	70	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
78	5	2200	Nómina por Pagar	pasivo	acreedora	70	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
79	5	3100	Capital Social	capital	acreedora	71	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
80	5	3200	Resultados Acumulados	capital	acreedora	71	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
81	5	4100	Ingresos por Ventas	ingreso	acreedora	72	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
82	5	4900	Otros Ingresos	ingreso	acreedora	72	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
83	5	5100	Costo de Mercancía Vendida	costo	deudora	73	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
84	5	5200	Gastos de Nómina	gasto	deudora	73	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
85	5	5900	Gastos Generales	gasto	deudora	73	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
86	6	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
87	6	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
88	6	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
89	6	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
90	6	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
91	6	1100	Caja y Bancos	activo	deudora	86	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
92	6	1200	Cuentas por Cobrar Clientes	activo	deudora	86	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
93	6	1300	Inventario	activo	deudora	86	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
94	6	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	87	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
95	6	2200	Nómina por Pagar	pasivo	acreedora	87	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
96	6	3100	Capital Social	capital	acreedora	88	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
97	6	3200	Resultados Acumulados	capital	acreedora	88	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
98	6	4100	Ingresos por Ventas	ingreso	acreedora	89	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
99	6	4900	Otros Ingresos	ingreso	acreedora	89	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
100	6	5100	Costo de Mercancía Vendida	costo	deudora	90	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
101	6	5200	Gastos de Nómina	gasto	deudora	90	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
102	6	5900	Gastos Generales	gasto	deudora	90	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
103	7	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
104	7	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
105	7	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
106	7	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
107	7	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
108	7	1100	Caja y Bancos	activo	deudora	103	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
109	7	1200	Cuentas por Cobrar Clientes	activo	deudora	103	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
110	7	1300	Inventario	activo	deudora	103	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
111	7	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	104	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
112	7	2200	Nómina por Pagar	pasivo	acreedora	104	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
113	7	3100	Capital Social	capital	acreedora	105	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
114	7	3200	Resultados Acumulados	capital	acreedora	105	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
115	7	4100	Ingresos por Ventas	ingreso	acreedora	106	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
116	7	4900	Otros Ingresos	ingreso	acreedora	106	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
117	7	5100	Costo de Mercancía Vendida	costo	deudora	107	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
118	7	5200	Gastos de Nómina	gasto	deudora	107	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
119	7	5900	Gastos Generales	gasto	deudora	107	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
120	8	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
121	8	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
122	8	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
123	8	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
124	8	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
125	8	1100	Caja y Bancos	activo	deudora	120	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
126	8	1200	Cuentas por Cobrar Clientes	activo	deudora	120	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
127	8	1300	Inventario	activo	deudora	120	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
128	8	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	121	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
129	8	2200	Nómina por Pagar	pasivo	acreedora	121	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
130	8	3100	Capital Social	capital	acreedora	122	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
131	8	3200	Resultados Acumulados	capital	acreedora	122	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
132	8	4100	Ingresos por Ventas	ingreso	acreedora	123	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
133	8	4900	Otros Ingresos	ingreso	acreedora	123	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
134	8	5100	Costo de Mercancía Vendida	costo	deudora	124	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
135	8	5200	Gastos de Nómina	gasto	deudora	124	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
136	8	5900	Gastos Generales	gasto	deudora	124	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
137	9	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
138	9	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
139	9	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
140	9	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
141	9	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
142	9	1100	Caja y Bancos	activo	deudora	137	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
143	9	1200	Cuentas por Cobrar Clientes	activo	deudora	137	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
144	9	1300	Inventario	activo	deudora	137	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
145	9	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	138	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
146	9	2200	Nómina por Pagar	pasivo	acreedora	138	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
147	9	3100	Capital Social	capital	acreedora	139	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
148	9	3200	Resultados Acumulados	capital	acreedora	139	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
149	9	4100	Ingresos por Ventas	ingreso	acreedora	140	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
150	9	4900	Otros Ingresos	ingreso	acreedora	140	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
151	9	5100	Costo de Mercancía Vendida	costo	deudora	141	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
152	9	5200	Gastos de Nómina	gasto	deudora	141	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
153	9	5900	Gastos Generales	gasto	deudora	141	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
154	10	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
155	10	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
156	10	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
157	10	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
158	10	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
159	10	1100	Caja y Bancos	activo	deudora	154	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
160	10	1200	Cuentas por Cobrar Clientes	activo	deudora	154	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
161	10	1300	Inventario	activo	deudora	154	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
162	10	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	155	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
163	10	2200	Nómina por Pagar	pasivo	acreedora	155	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
164	10	3100	Capital Social	capital	acreedora	156	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
165	10	3200	Resultados Acumulados	capital	acreedora	156	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
166	10	4100	Ingresos por Ventas	ingreso	acreedora	157	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
167	10	4900	Otros Ingresos	ingreso	acreedora	157	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
168	10	5100	Costo de Mercancía Vendida	costo	deudora	158	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
169	10	5200	Gastos de Nómina	gasto	deudora	158	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
170	10	5900	Gastos Generales	gasto	deudora	158	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
171	11	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
172	11	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
173	11	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
174	11	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
175	11	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
176	11	1100	Caja y Bancos	activo	deudora	171	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
177	11	1200	Cuentas por Cobrar Clientes	activo	deudora	171	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
178	11	1300	Inventario	activo	deudora	171	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
179	11	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	172	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
180	11	2200	Nómina por Pagar	pasivo	acreedora	172	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
181	11	3100	Capital Social	capital	acreedora	173	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
182	11	3200	Resultados Acumulados	capital	acreedora	173	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
183	11	4100	Ingresos por Ventas	ingreso	acreedora	174	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
184	11	4900	Otros Ingresos	ingreso	acreedora	174	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
185	11	5100	Costo de Mercancía Vendida	costo	deudora	175	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
186	11	5200	Gastos de Nómina	gasto	deudora	175	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
187	11	5900	Gastos Generales	gasto	deudora	175	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
188	12	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
189	12	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
190	12	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
191	12	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
192	12	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
193	12	1100	Caja y Bancos	activo	deudora	188	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
194	12	1200	Cuentas por Cobrar Clientes	activo	deudora	188	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
195	12	1300	Inventario	activo	deudora	188	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
196	12	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	189	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
197	12	2200	Nómina por Pagar	pasivo	acreedora	189	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
198	12	3100	Capital Social	capital	acreedora	190	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
199	12	3200	Resultados Acumulados	capital	acreedora	190	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
200	12	4100	Ingresos por Ventas	ingreso	acreedora	191	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
201	12	4900	Otros Ingresos	ingreso	acreedora	191	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
202	12	5100	Costo de Mercancía Vendida	costo	deudora	192	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
203	12	5200	Gastos de Nómina	gasto	deudora	192	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
204	12	5900	Gastos Generales	gasto	deudora	192	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
205	13	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
206	13	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
207	13	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
208	13	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
209	13	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
210	13	1100	Caja y Bancos	activo	deudora	205	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
211	13	1200	Cuentas por Cobrar Clientes	activo	deudora	205	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
212	13	1300	Inventario	activo	deudora	205	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
213	13	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	206	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
214	13	2200	Nómina por Pagar	pasivo	acreedora	206	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
215	13	3100	Capital Social	capital	acreedora	207	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
216	13	3200	Resultados Acumulados	capital	acreedora	207	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
217	13	4100	Ingresos por Ventas	ingreso	acreedora	208	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
218	13	4900	Otros Ingresos	ingreso	acreedora	208	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
219	13	5100	Costo de Mercancía Vendida	costo	deudora	209	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
220	13	5200	Gastos de Nómina	gasto	deudora	209	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
221	13	5900	Gastos Generales	gasto	deudora	209	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
222	14	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
223	14	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
224	14	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
225	14	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
226	14	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
227	14	1100	Caja y Bancos	activo	deudora	222	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
228	14	1200	Cuentas por Cobrar Clientes	activo	deudora	222	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
229	14	1300	Inventario	activo	deudora	222	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
230	14	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	223	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
231	14	2200	Nómina por Pagar	pasivo	acreedora	223	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
232	14	3100	Capital Social	capital	acreedora	224	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
233	14	3200	Resultados Acumulados	capital	acreedora	224	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
234	14	4100	Ingresos por Ventas	ingreso	acreedora	225	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
235	14	4900	Otros Ingresos	ingreso	acreedora	225	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
236	14	5100	Costo de Mercancía Vendida	costo	deudora	226	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
237	14	5200	Gastos de Nómina	gasto	deudora	226	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
238	14	5900	Gastos Generales	gasto	deudora	226	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
239	15	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
240	15	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
241	15	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
242	15	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
243	15	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
244	15	1100	Caja y Bancos	activo	deudora	239	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
245	15	1200	Cuentas por Cobrar Clientes	activo	deudora	239	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
246	15	1300	Inventario	activo	deudora	239	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
247	15	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	240	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
248	15	2200	Nómina por Pagar	pasivo	acreedora	240	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
249	15	3100	Capital Social	capital	acreedora	241	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
250	15	3200	Resultados Acumulados	capital	acreedora	241	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
251	15	4100	Ingresos por Ventas	ingreso	acreedora	242	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
252	15	4900	Otros Ingresos	ingreso	acreedora	242	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
253	15	5100	Costo de Mercancía Vendida	costo	deudora	243	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
254	15	5200	Gastos de Nómina	gasto	deudora	243	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
255	15	5900	Gastos Generales	gasto	deudora	243	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
256	16	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
257	16	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
258	16	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
259	16	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
260	16	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
261	16	1100	Caja y Bancos	activo	deudora	256	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
262	16	1200	Cuentas por Cobrar Clientes	activo	deudora	256	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
263	16	1300	Inventario	activo	deudora	256	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
264	16	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	257	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
265	16	2200	Nómina por Pagar	pasivo	acreedora	257	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
266	16	3100	Capital Social	capital	acreedora	258	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
267	16	3200	Resultados Acumulados	capital	acreedora	258	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
268	16	4100	Ingresos por Ventas	ingreso	acreedora	259	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
269	16	4900	Otros Ingresos	ingreso	acreedora	259	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
270	16	5100	Costo de Mercancía Vendida	costo	deudora	260	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
271	16	5200	Gastos de Nómina	gasto	deudora	260	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
272	16	5900	Gastos Generales	gasto	deudora	260	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
273	17	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
274	17	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
275	17	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
276	17	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
277	17	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
278	17	1100	Caja y Bancos	activo	deudora	273	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
279	17	1200	Cuentas por Cobrar Clientes	activo	deudora	273	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
280	17	1300	Inventario	activo	deudora	273	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
281	17	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	274	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
282	17	2200	Nómina por Pagar	pasivo	acreedora	274	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
283	17	3100	Capital Social	capital	acreedora	275	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
284	17	3200	Resultados Acumulados	capital	acreedora	275	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
285	17	4100	Ingresos por Ventas	ingreso	acreedora	276	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
286	17	4900	Otros Ingresos	ingreso	acreedora	276	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
287	17	5100	Costo de Mercancía Vendida	costo	deudora	277	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
288	17	5200	Gastos de Nómina	gasto	deudora	277	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
289	17	5900	Gastos Generales	gasto	deudora	277	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
290	18	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
291	18	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
292	18	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
293	18	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
294	18	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
295	18	1100	Caja y Bancos	activo	deudora	290	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
296	18	1200	Cuentas por Cobrar Clientes	activo	deudora	290	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
297	18	1300	Inventario	activo	deudora	290	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
298	18	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	291	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
299	18	2200	Nómina por Pagar	pasivo	acreedora	291	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
300	18	3100	Capital Social	capital	acreedora	292	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
301	18	3200	Resultados Acumulados	capital	acreedora	292	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
302	18	4100	Ingresos por Ventas	ingreso	acreedora	293	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
303	18	4900	Otros Ingresos	ingreso	acreedora	293	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
304	18	5100	Costo de Mercancía Vendida	costo	deudora	294	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
305	18	5200	Gastos de Nómina	gasto	deudora	294	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
306	18	5900	Gastos Generales	gasto	deudora	294	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
307	19	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
308	19	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
309	19	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
310	19	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
311	19	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
312	19	1100	Caja y Bancos	activo	deudora	307	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
313	19	1200	Cuentas por Cobrar Clientes	activo	deudora	307	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
314	19	1300	Inventario	activo	deudora	307	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
315	19	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	308	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
316	19	2200	Nómina por Pagar	pasivo	acreedora	308	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
317	19	3100	Capital Social	capital	acreedora	309	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
318	19	3200	Resultados Acumulados	capital	acreedora	309	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
319	19	4100	Ingresos por Ventas	ingreso	acreedora	310	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
320	19	4900	Otros Ingresos	ingreso	acreedora	310	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
321	19	5100	Costo de Mercancía Vendida	costo	deudora	311	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
322	19	5200	Gastos de Nómina	gasto	deudora	311	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
323	19	5900	Gastos Generales	gasto	deudora	311	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
324	20	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
325	20	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
326	20	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
327	20	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
328	20	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
329	20	1100	Caja y Bancos	activo	deudora	324	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
330	20	1200	Cuentas por Cobrar Clientes	activo	deudora	324	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
331	20	1300	Inventario	activo	deudora	324	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
332	20	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	325	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
333	20	2200	Nómina por Pagar	pasivo	acreedora	325	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
334	20	3100	Capital Social	capital	acreedora	326	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
335	20	3200	Resultados Acumulados	capital	acreedora	326	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
336	20	4100	Ingresos por Ventas	ingreso	acreedora	327	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
337	20	4900	Otros Ingresos	ingreso	acreedora	327	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
338	20	5100	Costo de Mercancía Vendida	costo	deudora	328	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
339	20	5200	Gastos de Nómina	gasto	deudora	328	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
340	20	5900	Gastos Generales	gasto	deudora	328	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
341	21	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
342	21	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
343	21	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
344	21	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
345	21	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
346	21	1100	Caja y Bancos	activo	deudora	341	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
347	21	1200	Cuentas por Cobrar Clientes	activo	deudora	341	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
348	21	1300	Inventario	activo	deudora	341	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
349	21	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	342	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
350	21	2200	Nómina por Pagar	pasivo	acreedora	342	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
351	21	3100	Capital Social	capital	acreedora	343	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
352	21	3200	Resultados Acumulados	capital	acreedora	343	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
353	21	4100	Ingresos por Ventas	ingreso	acreedora	344	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
354	21	4900	Otros Ingresos	ingreso	acreedora	344	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
355	21	5100	Costo de Mercancía Vendida	costo	deudora	345	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
356	21	5200	Gastos de Nómina	gasto	deudora	345	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
357	21	5900	Gastos Generales	gasto	deudora	345	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
358	22	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
359	22	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
360	22	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
361	22	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
362	22	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
363	22	1100	Caja y Bancos	activo	deudora	358	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
364	22	1200	Cuentas por Cobrar Clientes	activo	deudora	358	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
365	22	1300	Inventario	activo	deudora	358	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
366	22	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	359	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
367	22	2200	Nómina por Pagar	pasivo	acreedora	359	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
368	22	3100	Capital Social	capital	acreedora	360	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
369	22	3200	Resultados Acumulados	capital	acreedora	360	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
370	22	4100	Ingresos por Ventas	ingreso	acreedora	361	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
371	22	4900	Otros Ingresos	ingreso	acreedora	361	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
372	22	5100	Costo de Mercancía Vendida	costo	deudora	362	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
373	22	5200	Gastos de Nómina	gasto	deudora	362	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
374	22	5900	Gastos Generales	gasto	deudora	362	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
375	23	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
376	23	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
377	23	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
378	23	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
379	23	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
380	23	1100	Caja y Bancos	activo	deudora	375	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
381	23	1200	Cuentas por Cobrar Clientes	activo	deudora	375	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
382	23	1300	Inventario	activo	deudora	375	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
383	23	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	376	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
384	23	2200	Nómina por Pagar	pasivo	acreedora	376	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
385	23	3100	Capital Social	capital	acreedora	377	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
386	23	3200	Resultados Acumulados	capital	acreedora	377	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
387	23	4100	Ingresos por Ventas	ingreso	acreedora	378	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
388	23	4900	Otros Ingresos	ingreso	acreedora	378	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
389	23	5100	Costo de Mercancía Vendida	costo	deudora	379	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
390	23	5200	Gastos de Nómina	gasto	deudora	379	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
391	23	5900	Gastos Generales	gasto	deudora	379	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
392	26	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
393	26	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
394	26	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
395	26	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
396	26	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
397	26	1100	Caja y Bancos	activo	deudora	392	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
398	26	1200	Cuentas por Cobrar Clientes	activo	deudora	392	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
399	26	1300	Inventario	activo	deudora	392	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
400	26	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	393	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
401	26	2200	Nómina por Pagar	pasivo	acreedora	393	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
402	26	3100	Capital Social	capital	acreedora	394	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
403	26	3200	Resultados Acumulados	capital	acreedora	394	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
404	26	4100	Ingresos por Ventas	ingreso	acreedora	395	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
405	26	4900	Otros Ingresos	ingreso	acreedora	395	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
406	26	5100	Costo de Mercancía Vendida	costo	deudora	396	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
407	26	5200	Gastos de Nómina	gasto	deudora	396	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
408	26	5900	Gastos Generales	gasto	deudora	396	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
409	31	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
410	31	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
411	31	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
412	31	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
413	31	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
414	31	1100	Caja y Bancos	activo	deudora	409	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
415	31	1200	Cuentas por Cobrar Clientes	activo	deudora	409	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
416	31	1300	Inventario	activo	deudora	409	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
417	31	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	410	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
418	31	2200	Nómina por Pagar	pasivo	acreedora	410	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
419	31	3100	Capital Social	capital	acreedora	411	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
420	31	3200	Resultados Acumulados	capital	acreedora	411	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
421	31	4100	Ingresos por Ventas	ingreso	acreedora	412	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
422	31	4900	Otros Ingresos	ingreso	acreedora	412	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
423	31	5100	Costo de Mercancía Vendida	costo	deudora	413	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
424	31	5200	Gastos de Nómina	gasto	deudora	413	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
425	31	5900	Gastos Generales	gasto	deudora	413	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
426	32	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
427	32	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
428	32	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
429	32	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
430	32	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:37	2026-07-23 21:20:37
431	32	1100	Caja y Bancos	activo	deudora	426	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
432	32	1200	Cuentas por Cobrar Clientes	activo	deudora	426	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
433	32	1300	Inventario	activo	deudora	426	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
434	32	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	427	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
435	32	2200	Nómina por Pagar	pasivo	acreedora	427	t	t	2026-07-23 21:20:37	2026-07-23 21:20:37
436	32	3100	Capital Social	capital	acreedora	428	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
437	32	3200	Resultados Acumulados	capital	acreedora	428	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
438	32	4100	Ingresos por Ventas	ingreso	acreedora	429	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
439	32	4900	Otros Ingresos	ingreso	acreedora	429	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
440	32	5100	Costo de Mercancía Vendida	costo	deudora	430	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
441	32	5200	Gastos de Nómina	gasto	deudora	430	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
442	32	5900	Gastos Generales	gasto	deudora	430	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
443	33	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:38	2026-07-23 21:20:38
444	33	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:38	2026-07-23 21:20:38
445	33	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:38	2026-07-23 21:20:38
446	33	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:38	2026-07-23 21:20:38
447	33	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:38	2026-07-23 21:20:38
448	33	1100	Caja y Bancos	activo	deudora	443	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
449	33	1200	Cuentas por Cobrar Clientes	activo	deudora	443	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
450	33	1300	Inventario	activo	deudora	443	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
451	33	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	444	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
452	33	2200	Nómina por Pagar	pasivo	acreedora	444	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
453	33	3100	Capital Social	capital	acreedora	445	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
454	33	3200	Resultados Acumulados	capital	acreedora	445	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
455	33	4100	Ingresos por Ventas	ingreso	acreedora	446	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
456	33	4900	Otros Ingresos	ingreso	acreedora	446	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
457	33	5100	Costo de Mercancía Vendida	costo	deudora	447	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
458	33	5200	Gastos de Nómina	gasto	deudora	447	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
459	33	5900	Gastos Generales	gasto	deudora	447	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
460	34	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:38	2026-07-23 21:20:38
461	34	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:38	2026-07-23 21:20:38
462	34	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:38	2026-07-23 21:20:38
463	34	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:38	2026-07-23 21:20:38
464	34	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:38	2026-07-23 21:20:38
465	34	1100	Caja y Bancos	activo	deudora	460	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
466	34	1200	Cuentas por Cobrar Clientes	activo	deudora	460	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
467	34	1300	Inventario	activo	deudora	460	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
468	34	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	461	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
469	34	2200	Nómina por Pagar	pasivo	acreedora	461	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
470	34	3100	Capital Social	capital	acreedora	462	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
471	34	3200	Resultados Acumulados	capital	acreedora	462	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
472	34	4100	Ingresos por Ventas	ingreso	acreedora	463	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
473	34	4900	Otros Ingresos	ingreso	acreedora	463	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
474	34	5100	Costo de Mercancía Vendida	costo	deudora	464	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
475	34	5200	Gastos de Nómina	gasto	deudora	464	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
476	34	5900	Gastos Generales	gasto	deudora	464	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
477	36	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:38	2026-07-23 21:20:38
478	36	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:38	2026-07-23 21:20:38
479	36	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:38	2026-07-23 21:20:38
480	36	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:38	2026-07-23 21:20:38
481	36	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:38	2026-07-23 21:20:38
482	36	1100	Caja y Bancos	activo	deudora	477	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
483	36	1200	Cuentas por Cobrar Clientes	activo	deudora	477	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
484	36	1300	Inventario	activo	deudora	477	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
485	36	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	478	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
486	36	2200	Nómina por Pagar	pasivo	acreedora	478	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
487	36	3100	Capital Social	capital	acreedora	479	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
488	36	3200	Resultados Acumulados	capital	acreedora	479	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
489	36	4100	Ingresos por Ventas	ingreso	acreedora	480	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
490	36	4900	Otros Ingresos	ingreso	acreedora	480	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
491	36	5100	Costo de Mercancía Vendida	costo	deudora	481	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
492	36	5200	Gastos de Nómina	gasto	deudora	481	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
493	36	5900	Gastos Generales	gasto	deudora	481	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
494	35	1000	Activo	activo	deudora	\N	f	t	2026-07-23 21:20:38	2026-07-23 21:20:38
495	35	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 21:20:38	2026-07-23 21:20:38
496	35	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 21:20:38	2026-07-23 21:20:38
497	35	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 21:20:38	2026-07-23 21:20:38
498	35	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 21:20:38	2026-07-23 21:20:38
499	35	1100	Caja y Bancos	activo	deudora	494	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
500	35	1200	Cuentas por Cobrar Clientes	activo	deudora	494	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
501	35	1300	Inventario	activo	deudora	494	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
502	35	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	495	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
503	35	2200	Nómina por Pagar	pasivo	acreedora	495	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
504	35	3100	Capital Social	capital	acreedora	496	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
505	35	3200	Resultados Acumulados	capital	acreedora	496	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
506	35	4100	Ingresos por Ventas	ingreso	acreedora	497	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
507	35	4900	Otros Ingresos	ingreso	acreedora	497	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
508	35	5100	Costo de Mercancía Vendida	costo	deudora	498	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
509	35	5200	Gastos de Nómina	gasto	deudora	498	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
510	35	5900	Gastos Generales	gasto	deudora	498	t	t	2026-07-23 21:20:38	2026-07-23 21:20:38
511	37	1000	Activo	activo	deudora	\N	f	t	2026-07-23 22:25:03	2026-07-23 22:25:03
512	37	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 22:25:03	2026-07-23 22:25:03
513	37	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 22:25:03	2026-07-23 22:25:03
514	37	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 22:25:03	2026-07-23 22:25:03
515	37	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 22:25:03	2026-07-23 22:25:03
516	37	1100	Caja y Bancos	activo	deudora	511	t	t	2026-07-23 22:25:03	2026-07-23 22:25:03
517	37	1200	Cuentas por Cobrar Clientes	activo	deudora	511	t	t	2026-07-23 22:25:03	2026-07-23 22:25:03
518	37	1300	Inventario	activo	deudora	511	t	t	2026-07-23 22:25:03	2026-07-23 22:25:03
519	37	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	512	t	t	2026-07-23 22:25:03	2026-07-23 22:25:03
520	37	2200	Nómina por Pagar	pasivo	acreedora	512	t	t	2026-07-23 22:25:03	2026-07-23 22:25:03
521	37	3100	Capital Social	capital	acreedora	513	t	t	2026-07-23 22:25:03	2026-07-23 22:25:03
522	37	3200	Resultados Acumulados	capital	acreedora	513	t	t	2026-07-23 22:25:03	2026-07-23 22:25:03
523	37	4100	Ingresos por Ventas	ingreso	acreedora	514	t	t	2026-07-23 22:25:03	2026-07-23 22:25:03
524	37	4900	Otros Ingresos	ingreso	acreedora	514	t	t	2026-07-23 22:25:03	2026-07-23 22:25:03
525	37	5100	Costo de Mercancía Vendida	costo	deudora	515	t	t	2026-07-23 22:25:03	2026-07-23 22:25:03
526	37	5200	Gastos de Nómina	gasto	deudora	515	t	t	2026-07-23 22:25:03	2026-07-23 22:25:03
527	37	5900	Gastos Generales	gasto	deudora	515	t	t	2026-07-23 22:25:03	2026-07-23 22:25:03
528	38	1000	Activo	activo	deudora	\N	f	t	2026-07-23 22:35:44	2026-07-23 22:35:44
529	38	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 22:35:44	2026-07-23 22:35:44
530	38	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 22:35:44	2026-07-23 22:35:44
531	38	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 22:35:44	2026-07-23 22:35:44
532	38	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 22:35:44	2026-07-23 22:35:44
533	38	1100	Caja y Bancos	activo	deudora	528	t	t	2026-07-23 22:35:44	2026-07-23 22:35:44
534	38	1200	Cuentas por Cobrar Clientes	activo	deudora	528	t	t	2026-07-23 22:35:44	2026-07-23 22:35:44
535	38	1300	Inventario	activo	deudora	528	t	t	2026-07-23 22:35:44	2026-07-23 22:35:44
536	38	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	529	t	t	2026-07-23 22:35:44	2026-07-23 22:35:44
537	38	2200	Nómina por Pagar	pasivo	acreedora	529	t	t	2026-07-23 22:35:44	2026-07-23 22:35:44
538	38	3100	Capital Social	capital	acreedora	530	t	t	2026-07-23 22:35:44	2026-07-23 22:35:44
539	38	3200	Resultados Acumulados	capital	acreedora	530	t	t	2026-07-23 22:35:44	2026-07-23 22:35:44
540	38	4100	Ingresos por Ventas	ingreso	acreedora	531	t	t	2026-07-23 22:35:44	2026-07-23 22:35:44
541	38	4900	Otros Ingresos	ingreso	acreedora	531	t	t	2026-07-23 22:35:44	2026-07-23 22:35:44
542	38	5100	Costo de Mercancía Vendida	costo	deudora	532	t	t	2026-07-23 22:35:44	2026-07-23 22:35:44
543	38	5200	Gastos de Nómina	gasto	deudora	532	t	t	2026-07-23 22:35:44	2026-07-23 22:35:44
544	38	5900	Gastos Generales	gasto	deudora	532	t	t	2026-07-23 22:35:44	2026-07-23 22:35:44
545	39	1000	Activo	activo	deudora	\N	f	t	2026-07-23 22:37:09	2026-07-23 22:37:09
546	39	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-23 22:37:09	2026-07-23 22:37:09
547	39	3000	Capital	capital	acreedora	\N	f	t	2026-07-23 22:37:09	2026-07-23 22:37:09
548	39	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-23 22:37:09	2026-07-23 22:37:09
549	39	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-23 22:37:09	2026-07-23 22:37:09
550	39	1100	Caja y Bancos	activo	deudora	545	t	t	2026-07-23 22:37:09	2026-07-23 22:37:09
551	39	1200	Cuentas por Cobrar Clientes	activo	deudora	545	t	t	2026-07-23 22:37:09	2026-07-23 22:37:09
552	39	1300	Inventario	activo	deudora	545	t	t	2026-07-23 22:37:09	2026-07-23 22:37:09
553	39	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	546	t	t	2026-07-23 22:37:09	2026-07-23 22:37:09
554	39	2200	Nómina por Pagar	pasivo	acreedora	546	t	t	2026-07-23 22:37:09	2026-07-23 22:37:09
555	39	3100	Capital Social	capital	acreedora	547	t	t	2026-07-23 22:37:09	2026-07-23 22:37:09
556	39	3200	Resultados Acumulados	capital	acreedora	547	t	t	2026-07-23 22:37:09	2026-07-23 22:37:09
557	39	4100	Ingresos por Ventas	ingreso	acreedora	548	t	t	2026-07-23 22:37:09	2026-07-23 22:37:09
558	39	4900	Otros Ingresos	ingreso	acreedora	548	t	t	2026-07-23 22:37:09	2026-07-23 22:37:09
559	39	5100	Costo de Mercancía Vendida	costo	deudora	549	t	t	2026-07-23 22:37:09	2026-07-23 22:37:09
560	39	5200	Gastos de Nómina	gasto	deudora	549	t	t	2026-07-23 22:37:09	2026-07-23 22:37:09
561	39	5900	Gastos Generales	gasto	deudora	549	t	t	2026-07-23 22:37:09	2026-07-23 22:37:09
562	40	1000	Activo	activo	deudora	\N	f	t	2026-07-31 18:46:38	2026-07-31 18:46:38
563	40	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-07-31 18:46:38	2026-07-31 18:46:38
564	40	3000	Capital	capital	acreedora	\N	f	t	2026-07-31 18:46:38	2026-07-31 18:46:38
565	40	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-07-31 18:46:38	2026-07-31 18:46:38
566	40	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-07-31 18:46:38	2026-07-31 18:46:38
567	40	1100	Caja y Bancos	activo	deudora	562	t	t	2026-07-31 18:46:38	2026-07-31 18:46:38
568	40	1200	Cuentas por Cobrar Clientes	activo	deudora	562	t	t	2026-07-31 18:46:38	2026-07-31 18:46:38
569	40	1300	Inventario	activo	deudora	562	t	t	2026-07-31 18:46:38	2026-07-31 18:46:38
570	40	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	563	t	t	2026-07-31 18:46:38	2026-07-31 18:46:38
571	40	2200	Nómina por Pagar	pasivo	acreedora	563	t	t	2026-07-31 18:46:38	2026-07-31 18:46:38
572	40	3100	Capital Social	capital	acreedora	564	t	t	2026-07-31 18:46:38	2026-07-31 18:46:38
573	40	3200	Resultados Acumulados	capital	acreedora	564	t	t	2026-07-31 18:46:38	2026-07-31 18:46:38
574	40	4100	Ingresos por Ventas	ingreso	acreedora	565	t	t	2026-07-31 18:46:38	2026-07-31 18:46:38
575	40	4900	Otros Ingresos	ingreso	acreedora	565	t	t	2026-07-31 18:46:38	2026-07-31 18:46:38
576	40	5100	Costo de Mercancía Vendida	costo	deudora	566	t	t	2026-07-31 18:46:38	2026-07-31 18:46:38
577	40	5200	Gastos de Nómina	gasto	deudora	566	t	t	2026-07-31 18:46:38	2026-07-31 18:46:38
578	40	5900	Gastos Generales	gasto	deudora	566	t	t	2026-07-31 18:46:38	2026-07-31 18:46:38
579	2	1150	Bancos	activo	deudora	18	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
580	2	1250	Tarjeta por Cobrar	activo	deudora	18	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
581	3	1150	Bancos	activo	deudora	35	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
582	3	1250	Tarjeta por Cobrar	activo	deudora	35	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
583	4	1150	Bancos	activo	deudora	52	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
584	4	1250	Tarjeta por Cobrar	activo	deudora	52	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
585	5	1150	Bancos	activo	deudora	69	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
586	5	1250	Tarjeta por Cobrar	activo	deudora	69	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
587	6	1150	Bancos	activo	deudora	86	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
588	6	1250	Tarjeta por Cobrar	activo	deudora	86	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
589	7	1150	Bancos	activo	deudora	103	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
590	7	1250	Tarjeta por Cobrar	activo	deudora	103	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
591	8	1150	Bancos	activo	deudora	120	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
592	8	1250	Tarjeta por Cobrar	activo	deudora	120	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
593	9	1150	Bancos	activo	deudora	137	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
594	9	1250	Tarjeta por Cobrar	activo	deudora	137	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
595	10	1150	Bancos	activo	deudora	154	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
596	10	1250	Tarjeta por Cobrar	activo	deudora	154	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
597	11	1150	Bancos	activo	deudora	171	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
598	11	1250	Tarjeta por Cobrar	activo	deudora	171	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
599	12	1150	Bancos	activo	deudora	188	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
600	12	1250	Tarjeta por Cobrar	activo	deudora	188	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
601	13	1150	Bancos	activo	deudora	205	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
602	13	1250	Tarjeta por Cobrar	activo	deudora	205	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
603	14	1150	Bancos	activo	deudora	222	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
604	14	1250	Tarjeta por Cobrar	activo	deudora	222	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
605	15	1150	Bancos	activo	deudora	239	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
606	15	1250	Tarjeta por Cobrar	activo	deudora	239	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
607	16	1150	Bancos	activo	deudora	256	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
608	16	1250	Tarjeta por Cobrar	activo	deudora	256	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
609	17	1150	Bancos	activo	deudora	273	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
610	17	1250	Tarjeta por Cobrar	activo	deudora	273	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
611	18	1150	Bancos	activo	deudora	290	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
612	18	1250	Tarjeta por Cobrar	activo	deudora	290	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
613	19	1150	Bancos	activo	deudora	307	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
614	19	1250	Tarjeta por Cobrar	activo	deudora	307	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
615	20	1150	Bancos	activo	deudora	324	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
616	20	1250	Tarjeta por Cobrar	activo	deudora	324	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
617	21	1150	Bancos	activo	deudora	341	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
618	21	1250	Tarjeta por Cobrar	activo	deudora	341	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
619	22	1150	Bancos	activo	deudora	358	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
620	22	1250	Tarjeta por Cobrar	activo	deudora	358	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
621	23	1150	Bancos	activo	deudora	375	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
622	23	1250	Tarjeta por Cobrar	activo	deudora	375	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
623	26	1150	Bancos	activo	deudora	392	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
624	26	1250	Tarjeta por Cobrar	activo	deudora	392	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
625	32	1150	Bancos	activo	deudora	426	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
626	32	1250	Tarjeta por Cobrar	activo	deudora	426	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
627	33	1150	Bancos	activo	deudora	443	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
628	33	1250	Tarjeta por Cobrar	activo	deudora	443	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
629	34	1150	Bancos	activo	deudora	460	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
630	34	1250	Tarjeta por Cobrar	activo	deudora	460	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
631	36	1150	Bancos	activo	deudora	477	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
632	36	1250	Tarjeta por Cobrar	activo	deudora	477	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
633	31	1150	Bancos	activo	deudora	409	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
634	31	1250	Tarjeta por Cobrar	activo	deudora	409	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
635	40	1150	Bancos	activo	deudora	562	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
636	40	1250	Tarjeta por Cobrar	activo	deudora	562	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
637	37	1150	Bancos	activo	deudora	511	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
638	37	1250	Tarjeta por Cobrar	activo	deudora	511	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
639	38	1150	Bancos	activo	deudora	528	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
640	38	1250	Tarjeta por Cobrar	activo	deudora	528	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
641	39	1150	Bancos	activo	deudora	545	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
642	39	1250	Tarjeta por Cobrar	activo	deudora	545	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
643	35	1150	Bancos	activo	deudora	494	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
644	35	1250	Tarjeta por Cobrar	activo	deudora	494	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
645	1	1150	Bancos	activo	deudora	1	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
646	1	1250	Tarjeta por Cobrar	activo	deudora	1	t	t	2026-08-06 20:26:18	2026-08-06 20:26:18
647	41	1000	Activo	activo	deudora	\N	f	t	2026-08-06 20:55:39	2026-08-06 20:55:39
648	41	2000	Pasivo	pasivo	acreedora	\N	f	t	2026-08-06 20:55:39	2026-08-06 20:55:39
649	41	3000	Capital	capital	acreedora	\N	f	t	2026-08-06 20:55:39	2026-08-06 20:55:39
650	41	4000	Ingresos	ingreso	acreedora	\N	f	t	2026-08-06 20:55:39	2026-08-06 20:55:39
651	41	5000	Costos y Gastos	gasto	deudora	\N	f	t	2026-08-06 20:55:39	2026-08-06 20:55:39
652	41	1100	Caja y Bancos	activo	deudora	647	t	t	2026-08-06 20:55:39	2026-08-06 20:55:39
653	41	1200	Cuentas por Cobrar Clientes	activo	deudora	647	t	t	2026-08-06 20:55:39	2026-08-06 20:55:39
654	41	1300	Inventario	activo	deudora	647	t	t	2026-08-06 20:55:39	2026-08-06 20:55:39
655	41	1150	Bancos	activo	deudora	647	t	t	2026-08-06 20:55:39	2026-08-06 20:55:39
656	41	1250	Tarjeta por Cobrar	activo	deudora	647	t	t	2026-08-06 20:55:39	2026-08-06 20:55:39
657	41	2100	Cuentas por Pagar Proveedores	pasivo	acreedora	648	t	t	2026-08-06 20:55:39	2026-08-06 20:55:39
658	41	2200	Nómina por Pagar	pasivo	acreedora	648	t	t	2026-08-06 20:55:39	2026-08-06 20:55:39
659	41	3100	Capital Social	capital	acreedora	649	t	t	2026-08-06 20:55:39	2026-08-06 20:55:39
660	41	3200	Resultados Acumulados	capital	acreedora	649	t	t	2026-08-06 20:55:39	2026-08-06 20:55:39
661	41	4100	Ingresos por Ventas	ingreso	acreedora	650	t	t	2026-08-06 20:55:39	2026-08-06 20:55:39
662	41	4900	Otros Ingresos	ingreso	acreedora	650	t	t	2026-08-06 20:55:39	2026-08-06 20:55:39
663	41	5100	Costo de Mercancía Vendida	costo	deudora	651	t	t	2026-08-06 20:55:39	2026-08-06 20:55:39
664	41	5200	Gastos de Nómina	gasto	deudora	651	t	t	2026-08-06 20:55:39	2026-08-06 20:55:39
665	41	5900	Gastos Generales	gasto	deudora	651	t	t	2026-08-06 20:55:39	2026-08-06 20:55:39
\.


--
-- Data for Name: erp_proyecto_horas; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.erp_proyecto_horas (id, id_tenant, id_proyecto, colaborador, fecha, horas, descripcion, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: erp_proyecto_tareas; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.erp_proyecto_tareas (id, id_tenant, id_proyecto, titulo, descripcion, estado, asignado, orden, created_at, updated_at, fecha_inicio, fecha_fin) FROM stdin;
4	1	2	yy	rt	completada	ertter	0	2026-07-16 23:09:55	2026-07-16 23:13:37	\N	\N
8	1	2	frd	wetr	completada	wqere	0	2026-07-21 21:23:03	2026-07-21 21:23:07	2026-07-22	2026-07-22
9	1	2	hrd	sdffs	completada	fdfds	0	2026-07-24 20:28:50	2026-07-24 20:28:53	2026-07-24	2026-07-24
\.


--
-- Data for Name: erp_proyectos; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.erp_proyectos (id, id_tenant, nombre, cliente, responsable, estado, progreso, horas, presupuesto, created_at, updated_at) FROM stdin;
2	1	dff	dd	sd	activo	0	0	22.00	2026-07-08 14:59:23	2026-07-08 14:59:23
\.


--
-- Data for Name: erp_recetas; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.erp_recetas (id, id_tenant, id_cliente, id_producto, dosis, cantidad, pendiente, created_at, updated_at) FROM stdin;
1	33	16	15	Cada 8h x 7 dias	21	f	2026-07-21 17:37:26	2026-07-21 17:37:39
2	33	16	16	Cada 6h si dolor	10	f	2026-07-21 17:37:27	2026-07-21 17:37:39
3	33	16	16	dosis cada 8 hrs	1	f	2026-07-21 22:34:46	2026-07-21 22:34:49
4	33	16	16	78484	1	f	2026-07-21 22:53:59	2026-07-21 22:54:05
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: integraciones; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.integraciones (id, id_tenant, nombre, tipo, estado, configuracion, created_at, updated_at) FROM stdin;
1	2	WhatsApp Business	whatsapp	desconectada	\N	2026-07-03 15:38:47	2026-07-03 15:38:47
2	2	Email Marketing	email	desconectada	\N	2026-07-03 15:38:47	2026-07-03 15:38:47
3	2	Google Calendar	calendario	desconectada	\N	2026-07-03 15:38:47	2026-07-03 15:38:47
4	2	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-03 15:38:47	2026-07-03 15:38:47
5	4	WhatsApp Business	whatsapp	desconectada	\N	2026-07-03 18:24:21	2026-07-03 18:24:21
6	4	Email Marketing	email	desconectada	\N	2026-07-03 18:24:21	2026-07-03 18:24:21
7	4	Google Calendar	calendario	desconectada	\N	2026-07-03 18:24:21	2026-07-03 18:24:21
8	4	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-03 18:24:21	2026-07-03 18:24:21
9	5	WhatsApp Business	whatsapp	desconectada	\N	2026-07-03 18:27:54	2026-07-03 18:27:54
10	5	Email Marketing	email	desconectada	\N	2026-07-03 18:27:54	2026-07-03 18:27:54
11	5	Google Calendar	calendario	desconectada	\N	2026-07-03 18:27:54	2026-07-03 18:27:54
12	5	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-03 18:27:54	2026-07-03 18:27:54
13	6	WhatsApp Business	whatsapp	desconectada	\N	2026-07-03 18:44:38	2026-07-03 18:44:38
14	6	Email Marketing	email	desconectada	\N	2026-07-03 18:44:38	2026-07-03 18:44:38
15	6	Google Calendar	calendario	desconectada	\N	2026-07-03 18:44:38	2026-07-03 18:44:38
16	6	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-03 18:44:38	2026-07-03 18:44:38
17	7	WhatsApp Business	whatsapp	desconectada	\N	2026-07-03 18:45:42	2026-07-03 18:45:42
18	7	Email Marketing	email	desconectada	\N	2026-07-03 18:45:42	2026-07-03 18:45:42
19	7	Google Calendar	calendario	desconectada	\N	2026-07-03 18:45:42	2026-07-03 18:45:42
20	7	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-03 18:45:42	2026-07-03 18:45:42
21	8	WhatsApp Business	whatsapp	desconectada	\N	2026-07-03 18:46:30	2026-07-03 18:46:30
22	8	Email Marketing	email	desconectada	\N	2026-07-03 18:46:30	2026-07-03 18:46:30
23	8	Google Calendar	calendario	desconectada	\N	2026-07-03 18:46:30	2026-07-03 18:46:30
24	8	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-03 18:46:30	2026-07-03 18:46:30
25	9	WhatsApp Business	whatsapp	desconectada	\N	2026-07-03 18:47:31	2026-07-03 18:47:31
26	9	Email Marketing	email	desconectada	\N	2026-07-03 18:47:31	2026-07-03 18:47:31
27	9	Google Calendar	calendario	desconectada	\N	2026-07-03 18:47:31	2026-07-03 18:47:31
28	9	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-03 18:47:31	2026-07-03 18:47:31
29	10	WhatsApp Business	whatsapp	desconectada	\N	2026-07-03 18:49:47	2026-07-03 18:49:47
30	10	Email Marketing	email	desconectada	\N	2026-07-03 18:49:47	2026-07-03 18:49:47
31	10	Google Calendar	calendario	desconectada	\N	2026-07-03 18:49:47	2026-07-03 18:49:47
32	10	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-03 18:49:47	2026-07-03 18:49:47
33	11	WhatsApp Business	whatsapp	desconectada	\N	2026-07-03 18:55:40	2026-07-03 18:55:40
34	11	Email Marketing	email	desconectada	\N	2026-07-03 18:55:40	2026-07-03 18:55:40
35	11	Google Calendar	calendario	desconectada	\N	2026-07-03 18:55:40	2026-07-03 18:55:40
36	11	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-03 18:55:40	2026-07-03 18:55:40
37	12	WhatsApp Business	whatsapp	desconectada	\N	2026-07-03 18:57:35	2026-07-03 18:57:35
38	12	Email Marketing	email	desconectada	\N	2026-07-03 18:57:35	2026-07-03 18:57:35
39	12	Google Calendar	calendario	desconectada	\N	2026-07-03 18:57:35	2026-07-03 18:57:35
40	12	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-03 18:57:35	2026-07-03 18:57:35
41	13	WhatsApp Business	whatsapp	desconectada	\N	2026-07-03 18:59:27	2026-07-03 18:59:27
42	13	Email Marketing	email	desconectada	\N	2026-07-03 18:59:27	2026-07-03 18:59:27
43	13	Google Calendar	calendario	desconectada	\N	2026-07-03 18:59:27	2026-07-03 18:59:27
44	13	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-03 18:59:27	2026-07-03 18:59:27
45	14	WhatsApp Business	whatsapp	desconectada	\N	2026-07-03 20:32:44	2026-07-03 20:32:44
46	14	Email Marketing	email	desconectada	\N	2026-07-03 20:32:44	2026-07-03 20:32:44
47	14	Google Calendar	calendario	desconectada	\N	2026-07-03 20:32:44	2026-07-03 20:32:44
48	14	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-03 20:32:44	2026-07-03 20:32:44
49	15	WhatsApp Business	whatsapp	desconectada	\N	2026-07-03 21:31:37	2026-07-03 21:31:37
50	15	Email Marketing	email	desconectada	\N	2026-07-03 21:31:37	2026-07-03 21:31:37
51	15	Google Calendar	calendario	desconectada	\N	2026-07-03 21:31:37	2026-07-03 21:31:37
52	15	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-03 21:31:37	2026-07-03 21:31:37
53	16	WhatsApp Business	whatsapp	desconectada	\N	2026-07-03 21:31:52	2026-07-03 21:31:52
54	16	Email Marketing	email	desconectada	\N	2026-07-03 21:31:52	2026-07-03 21:31:52
55	16	Google Calendar	calendario	desconectada	\N	2026-07-03 21:31:52	2026-07-03 21:31:52
56	16	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-03 21:31:52	2026-07-03 21:31:52
57	17	WhatsApp Business	whatsapp	desconectada	\N	2026-07-03 21:36:13	2026-07-03 21:36:13
58	17	Email Marketing	email	desconectada	\N	2026-07-03 21:36:13	2026-07-03 21:36:13
59	17	Google Calendar	calendario	desconectada	\N	2026-07-03 21:36:13	2026-07-03 21:36:13
60	17	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-03 21:36:13	2026-07-03 21:36:13
61	18	WhatsApp Business	whatsapp	desconectada	\N	2026-07-03 21:38:01	2026-07-03 21:38:01
62	18	Email Marketing	email	desconectada	\N	2026-07-03 21:38:01	2026-07-03 21:38:01
63	18	Google Calendar	calendario	desconectada	\N	2026-07-03 21:38:01	2026-07-03 21:38:01
64	18	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-03 21:38:01	2026-07-03 21:38:01
65	19	WhatsApp Business	whatsapp	desconectada	\N	2026-07-03 21:39:43	2026-07-03 21:39:43
66	19	Email Marketing	email	desconectada	\N	2026-07-03 21:39:43	2026-07-03 21:39:43
67	19	Google Calendar	calendario	desconectada	\N	2026-07-03 21:39:43	2026-07-03 21:39:43
68	19	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-03 21:39:43	2026-07-03 21:39:43
69	20	WhatsApp Business	whatsapp	desconectada	\N	2026-07-03 21:50:48	2026-07-03 21:50:48
70	20	Email Marketing	email	desconectada	\N	2026-07-03 21:50:48	2026-07-03 21:50:48
71	20	Google Calendar	calendario	desconectada	\N	2026-07-03 21:50:48	2026-07-03 21:50:48
72	20	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-03 21:50:48	2026-07-03 21:50:48
73	21	WhatsApp Business	whatsapp	desconectada	\N	2026-07-03 22:17:34	2026-07-03 22:17:34
74	21	Email Marketing	email	desconectada	\N	2026-07-03 22:17:34	2026-07-03 22:17:34
75	21	Google Calendar	calendario	desconectada	\N	2026-07-03 22:17:34	2026-07-03 22:17:34
76	21	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-03 22:17:34	2026-07-03 22:17:34
77	22	WhatsApp Business	whatsapp	desconectada	\N	2026-07-03 22:24:45	2026-07-03 22:24:45
78	22	Email Marketing	email	desconectada	\N	2026-07-03 22:24:45	2026-07-03 22:24:45
79	22	Google Calendar	calendario	desconectada	\N	2026-07-03 22:24:45	2026-07-03 22:24:45
80	22	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-03 22:24:45	2026-07-03 22:24:45
81	23	WhatsApp Business	whatsapp	desconectada	\N	2026-07-08 21:38:20	2026-07-08 21:38:20
82	23	Email Marketing	email	desconectada	\N	2026-07-08 21:38:20	2026-07-08 21:38:20
83	23	Google Calendar	calendario	desconectada	\N	2026-07-08 21:38:20	2026-07-08 21:38:20
84	23	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-08 21:38:20	2026-07-08 21:38:20
85	24	WhatsApp Business	whatsapp	desconectada	\N	2026-07-08 21:53:52	2026-07-08 21:53:52
86	24	Email Marketing	email	desconectada	\N	2026-07-08 21:53:52	2026-07-08 21:53:52
87	24	Google Calendar	calendario	desconectada	\N	2026-07-08 21:53:52	2026-07-08 21:53:52
88	24	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-08 21:53:52	2026-07-08 21:53:52
93	26	WhatsApp Business	whatsapp	desconectada	\N	2026-07-15 20:15:04	2026-07-15 20:15:04
94	26	Email Marketing	email	desconectada	\N	2026-07-15 20:15:04	2026-07-15 20:15:04
95	26	Google Calendar	calendario	desconectada	\N	2026-07-15 20:15:04	2026-07-15 20:15:04
96	26	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-15 20:15:04	2026-07-15 20:15:04
97	31	WhatsApp Business	whatsapp	desconectada	\N	2026-07-21 16:46:36	2026-07-21 16:46:36
98	31	Email Marketing	email	desconectada	\N	2026-07-21 16:46:36	2026-07-21 16:46:36
99	31	Google Calendar	calendario	desconectada	\N	2026-07-21 16:46:36	2026-07-21 16:46:36
100	31	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-21 16:46:36	2026-07-21 16:46:36
101	32	WhatsApp Business	whatsapp	desconectada	\N	2026-07-21 17:36:31	2026-07-21 17:36:31
102	32	Email Marketing	email	desconectada	\N	2026-07-21 17:36:31	2026-07-21 17:36:31
103	32	Google Calendar	calendario	desconectada	\N	2026-07-21 17:36:31	2026-07-21 17:36:31
104	32	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-21 17:36:31	2026-07-21 17:36:31
105	33	WhatsApp Business	whatsapp	desconectada	\N	2026-07-21 17:36:31	2026-07-21 17:36:31
106	33	Email Marketing	email	desconectada	\N	2026-07-21 17:36:31	2026-07-21 17:36:31
107	33	Google Calendar	calendario	desconectada	\N	2026-07-21 17:36:31	2026-07-21 17:36:31
108	33	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-21 17:36:31	2026-07-21 17:36:31
109	34	WhatsApp Business	whatsapp	desconectada	\N	2026-07-21 18:47:40	2026-07-21 18:47:40
110	34	Email Marketing	email	desconectada	\N	2026-07-21 18:47:40	2026-07-21 18:47:40
111	34	Google Calendar	calendario	desconectada	\N	2026-07-21 18:47:40	2026-07-21 18:47:40
112	34	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-21 18:47:40	2026-07-21 18:47:40
115	35	Google Calendar	calendario	desconectada	\N	2026-07-22 22:52:51	2026-07-22 22:52:51
116	35	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-22 22:52:51	2026-07-22 22:52:51
117	36	WhatsApp Business	whatsapp	desconectada	\N	2026-07-22 23:00:40	2026-07-22 23:00:40
118	36	Email Marketing	email	desconectada	\N	2026-07-22 23:00:40	2026-07-22 23:00:40
119	36	Google Calendar	calendario	desconectada	\N	2026-07-22 23:00:40	2026-07-22 23:00:40
120	36	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-22 23:00:40	2026-07-22 23:00:40
121	37	WhatsApp Business	whatsapp	desconectada	\N	2026-07-23 22:25:03	2026-07-23 22:25:03
122	37	Email Marketing	email	desconectada	\N	2026-07-23 22:25:03	2026-07-23 22:25:03
123	37	Google Calendar	calendario	desconectada	\N	2026-07-23 22:25:03	2026-07-23 22:25:03
124	37	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-23 22:25:03	2026-07-23 22:25:03
125	38	WhatsApp Business	whatsapp	desconectada	\N	2026-07-23 22:35:44	2026-07-23 22:35:44
126	38	Email Marketing	email	desconectada	\N	2026-07-23 22:35:44	2026-07-23 22:35:44
127	38	Google Calendar	calendario	desconectada	\N	2026-07-23 22:35:44	2026-07-23 22:35:44
128	38	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-23 22:35:44	2026-07-23 22:35:44
129	39	WhatsApp Business	whatsapp	desconectada	\N	2026-07-23 22:37:09	2026-07-23 22:37:09
130	39	Email Marketing	email	desconectada	\N	2026-07-23 22:37:09	2026-07-23 22:37:09
131	39	Google Calendar	calendario	desconectada	\N	2026-07-23 22:37:09	2026-07-23 22:37:09
132	39	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-23 22:37:09	2026-07-23 22:37:09
133	40	WhatsApp Business	whatsapp	desconectada	\N	2026-07-31 18:46:38	2026-07-31 18:46:38
134	40	Email Marketing	email	desconectada	\N	2026-07-31 18:46:38	2026-07-31 18:46:38
135	40	Google Calendar	calendario	desconectada	\N	2026-07-31 18:46:38	2026-07-31 18:46:38
136	40	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-07-31 18:46:38	2026-07-31 20:09:12
137	41	WhatsApp Business	whatsapp	desconectada	\N	2026-08-06 20:55:39	2026-08-06 20:55:39
138	41	Email Marketing	email	desconectada	\N	2026-08-06 20:55:39	2026-08-06 20:55:39
139	41	Google Calendar	calendario	desconectada	\N	2026-08-06 20:55:39	2026-08-06 20:55:39
140	41	Almacenamiento en la nube	almacenamiento	desconectada	\N	2026-08-06 20:55:39	2026-08-06 20:55:39
114	35	Email Marketing	email	desconectada	\N	2026-07-22 22:52:51	2026-08-07 21:00:20
113	35	WhatsApp Business	whatsapp	desconectada	\N	2026-07-22 22:52:51	2026-08-07 21:00:22
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: leads; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.leads (id_lead, id_tenant, id_cliente, id_usuario, titulo, descripcion, estado, fuente, valor_estimado, deleted_at, created_at, updated_at, nombre, email, telefono) FROM stdin;
2	15	5	19	Interés en sistema ERP	\N	convertido	web	25000.00	\N	2026-07-03 21:31:37	2026-07-03 21:31:37	Ana García	ana@ejemplo.com	5551234567
3	16	\N	20	Otro lead	\N	nuevo	otro	\N	\N	2026-07-03 21:31:53	2026-07-03 21:31:53	Carlos	\N	\N
4	17	\N	21	Interés en sistema ERP	\N	nuevo	web	\N	\N	2026-07-03 21:36:22	2026-07-03 21:36:22	Ana García	ana@ejemplo.com	\N
5	18	\N	22	Interés en sistema ERP	\N	nuevo	web	\N	\N	2026-07-03 21:38:10	2026-07-03 21:38:10	Ana García	ana@ejemplo.com	\N
6	19	7	23	Interés en sistema ERP	\N	convertido	web	\N	\N	2026-07-03 21:39:51	2026-07-03 21:39:54	Ana García	ana@ejemplo.com	\N
10	1	\N	2	Test permiso	\N	nuevo	otro	\N	2026-07-16 22:28:37	2026-07-16 22:28:28	2026-07-16 22:28:37	\N	\N	\N
11	35	21	43	ygbhybgh	uhjn	convertido	web	56662.25	\N	2026-08-07 14:02:22	2026-08-07 14:02:37	sfdsxg	orfrvc@jhnjn.com	656926526
\.


--
-- Data for Name: membresias; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.membresias (id_membresia, id_usuario, id_tenant, estado, es_owner, invitado_por, unido_en, created_at, updated_at) FROM stdin;
2	2	1	activa	f	\N	2026-07-01 22:13:15	2026-07-09 17:34:49	2026-07-09 17:34:49
4	6	2	activa	t	\N	2026-07-03 15:38:47	2026-07-09 17:34:49	2026-07-09 17:34:49
5	7	1	activa	t	\N	2026-07-03 18:23:58	2026-07-09 17:34:49	2026-07-09 17:34:49
6	8	4	activa	t	\N	2026-07-03 18:24:21	2026-07-09 17:34:49	2026-07-09 17:34:49
7	9	5	activa	t	\N	2026-07-03 18:27:54	2026-07-09 17:34:49	2026-07-09 17:34:49
8	10	6	activa	t	\N	2026-07-03 18:44:38	2026-07-09 17:34:49	2026-07-09 17:34:49
9	11	7	activa	t	\N	2026-07-03 18:45:42	2026-07-09 17:34:49	2026-07-09 17:34:49
10	12	8	activa	t	\N	2026-07-03 18:46:30	2026-07-09 17:34:49	2026-07-09 17:34:49
11	13	9	activa	t	\N	2026-07-03 18:47:31	2026-07-09 17:34:49	2026-07-09 17:34:49
12	14	10	activa	t	\N	2026-07-03 18:49:47	2026-07-09 17:34:49	2026-07-09 17:34:49
13	15	11	activa	t	\N	2026-07-03 18:55:40	2026-07-09 17:34:49	2026-07-09 17:34:49
14	16	12	activa	t	\N	2026-07-03 18:57:35	2026-07-09 17:34:49	2026-07-09 17:34:49
15	17	13	activa	t	\N	2026-07-03 18:59:27	2026-07-09 17:34:49	2026-07-09 17:34:49
16	18	14	activa	t	\N	2026-07-03 20:32:44	2026-07-09 17:34:49	2026-07-09 17:34:49
17	19	15	activa	t	\N	2026-07-03 21:31:37	2026-07-09 17:34:49	2026-07-09 17:34:49
18	20	16	activa	t	\N	2026-07-03 21:31:52	2026-07-09 17:34:49	2026-07-09 17:34:49
19	21	17	activa	t	\N	2026-07-03 21:36:13	2026-07-09 17:34:49	2026-07-09 17:34:49
20	22	18	activa	t	\N	2026-07-03 21:38:01	2026-07-09 17:34:49	2026-07-09 17:34:49
21	23	19	activa	t	\N	2026-07-03 21:39:43	2026-07-09 17:34:49	2026-07-09 17:34:49
22	24	20	activa	t	\N	2026-07-03 21:50:48	2026-07-09 17:34:49	2026-07-09 17:34:49
23	25	21	activa	t	\N	2026-07-03 22:17:34	2026-07-09 17:34:49	2026-07-09 17:34:49
24	26	22	activa	t	\N	2026-07-03 22:24:45	2026-07-09 17:34:49	2026-07-09 17:34:49
25	27	23	activa	t	\N	2026-07-08 21:38:20	2026-07-09 17:34:49	2026-07-09 17:34:49
26	28	24	activa	t	\N	2026-07-08 21:53:52	2026-07-09 17:34:49	2026-07-09 17:34:49
29	32	26	activa	t	\N	2026-07-15 20:15:04	2026-07-15 20:15:04	2026-07-15 20:15:04
35	39	31	activa	t	\N	2026-07-21 16:46:36	2026-07-21 16:46:36	2026-07-21 16:46:36
36	40	32	activa	t	\N	2026-07-21 17:36:31	2026-07-21 17:36:31	2026-07-21 17:36:31
37	41	33	activa	t	\N	2026-07-21 17:36:31	2026-07-21 17:36:31	2026-07-21 17:36:31
38	42	34	activa	t	\N	2026-07-21 18:47:40	2026-07-21 18:47:40	2026-07-21 18:47:40
39	43	35	activa	t	\N	2026-07-22 22:52:51	2026-07-22 22:52:51	2026-07-22 22:52:51
40	44	36	activa	t	\N	2026-07-22 23:00:40	2026-07-22 23:00:40	2026-07-22 23:00:40
41	45	37	activa	t	\N	2026-07-23 22:25:03	2026-07-23 22:25:03	2026-07-23 22:25:03
42	46	38	activa	t	\N	2026-07-23 22:35:44	2026-07-23 22:35:44	2026-07-23 22:35:44
43	47	39	activa	t	\N	2026-07-23 22:37:08	2026-07-23 22:37:08	2026-07-23 22:37:08
44	44	35	activa	f	43	2026-07-27 22:04:57	2026-07-27 22:04:57	2026-07-27 22:04:57
45	48	35	activa	f	43	2026-07-27 22:28:51	2026-07-27 22:28:51	2026-07-27 22:28:51
46	49	35	activa	f	43	2026-07-27 22:54:37	2026-07-27 22:54:37	2026-07-27 22:54:37
47	50	31	activa	f	39	2026-07-28 23:14:55	2026-07-28 23:14:55	2026-07-28 23:14:55
48	51	40	activa	t	\N	2026-07-31 18:46:38	2026-07-31 18:46:38	2026-07-31 18:46:38
49	52	41	activa	t	\N	2026-08-06 20:55:39	2026-08-06 20:55:39	2026-08-06 20:55:39
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2026_04_23_065222_create_personal_access_tokens_table	1
5	2026_04_29_020000_create_negocios_table	1
6	2026_04_29_023522_create_plans_table	1
7	2026_04_29_030000_create_tenants_table	1
8	2026_04_29_044927_create_usuarios_table	1
9	2026_04_29_044959_create_rols_table	1
10	2026_04_29_045010_create_permisos_table	1
11	2026_04_30_021253_create_permiso_rol_table	1
12	2026_04_30_051015_create_usuario_table	1
13	2026_05_10_223139_create_categorias_table	1
14	2026_05_10_223149_create_productos_table	1
15	2026_05_13_043324_create_clientes_table	1
16	2026_05_13_043344_create_contactos_table	1
17	2026_05_13_043352_create_leads_table	1
18	2026_05_13_043403_create_pipelines_table	1
19	2026_05_13_043424_create_oportunidades_table	1
20	2026_05_13_043435_create_actividades_table	1
21	2026_05_13_043449_create_notificaciones_table	1
22	2026_05_16_230007_add_direccion_to_clientes_table	2
23	2026_07_02_000000_create_campanas_marketing_table	3
24	2026_07_03_050433_add_etapa_to_oportunidades_table	4
25	2026_07_03_050434_create_automatizaciones_table	4
26	2026_07_03_050434_create_campanas_table	4
27	2026_07_03_050435_add_sector_empresarial_to_clientes_table	4
28	2026_07_03_050435_create_integraciones_table	4
29	2026_07_03_050758_add_deleted_at_to_tenants_table	4
30	2026_07_03_052041_modify_campanas_add_clientes_pivot	4
31	2026_07_03_052041_remove_lista_contactos_from_campanas_table	4
32	2026_07_03_171844_add_slug_to_negocios_table	5
33	2026_07_03_171845_add_onboarding_fields_to_tenants_table	5
34	2026_07_03_171845_make_id_tiponegocio_nullable_on_tenants_table	5
35	2026_07_03_212858_add_contacto_fields_to_leads_table	6
36	2026_07_03_212859_add_convertido_to_leads_estado_enum	6
37	2026_07_03_221502_fix_notificaciones_columns	7
38	2026_07_07_182825_create_erp_inventario_table	8
39	2026_07_07_182826_create_erp_movimientos_table	8
40	2026_07_07_182826_create_erp_ordenes_compra_table	8
41	2026_07_07_182826_create_erp_pedidos_venta_table	8
42	2026_07_07_182827_create_erp_empleados_table	8
43	2026_07_07_182827_create_erp_envios_table	8
44	2026_07_07_182827_create_erp_ordenes_produccion_table	8
45	2026_07_07_182828_create_erp_proyectos_table	8
46	2026_07_08_102301_add_erp_fields_to_productos_table	9
47	2026_07_08_102302_drop_erp_inventario_table	10
48	2026_07_08_102303_create_proveedores_table	11
49	2026_07_08_102304_update_erp_ordenes_compra_table	12
50	2026_07_08_102305_create_erp_orden_compra_items_table	13
51	2026_07_08_102306_update_erp_pedidos_venta_table	14
52	2026_07_08_102307_create_erp_pedido_items_table	15
53	2026_07_08_102308_create_erp_movimientos_stock_table	16
54	2026_07_08_115325_add_soft_deletes_to_erp_movimientos_and_envios	17
55	2026_07_09_151759_add_max_usuarios_to_plans_table	18
56	2026_07_09_151759_add_roles_to_usuarios_table	18
57	2026_07_09_171536_create_rbac_dinamico_tables	19
58	2026_07_09_173424_create_membresias_table	20
59	2026_07_09_181912_create_oauth_auth_codes_table	21
60	2026_07_09_181913_create_oauth_access_tokens_table	21
61	2026_07_09_181914_create_oauth_refresh_tokens_table	21
62	2026_07_09_181915_create_oauth_clients_table	21
63	2026_07_09_181916_create_oauth_device_codes_table	21
64	2026_07_13_212610_add_unique_index_to_usuarios_email	22
65	2026_07_15_203311_add_foto_perfil_to_usuarios_table	23
66	2026_07_16_165438_add_configuracion_general_to_tenants_table	24
67	2026_07_16_170000_seed_permisos_y_backfill_roles	25
68	2026_07_16_180047_create_erp_proyecto_tareas_table	26
69	2026_07_16_180048_create_erp_proyecto_horas_table	26
70	2026_07_20_215539_add_estado_to_usuarios_table	27
71	2026_07_20_225646_add_fechas_to_erp_proyecto_tareas_table	28
72	2026_07_21_113927_create_erp_mesas_table	29
73	2026_07_21_113928_create_erp_comandas_table	29
74	2026_07_21_113929_create_erp_comanda_items_table	29
75	2026_07_21_123049_create_erp_habitaciones_table	30
76	2026_07_21_123050_create_erp_habitacion_consumos_table	30
77	2026_07_21_123051_create_erp_recetas_table	30
78	2026_07_23_161230_create_erp_plan_cuentas_table	31
79	2026_07_23_161231_create_erp_asientos_table	31
80	2026_07_23_161232_create_erp_asiento_detalles_table	31
81	2026_07_23_161233_add_costo_unitario_to_erp_pedido_items_table	31
82	2026_07_23_161234_create_erp_nomina_pagos_table	31
83	2026_07_23_171242_add_logo_to_tenants_table	32
84	2026_07_24_120001_add_stripe_price_id_to_plans_table	33
85	2026_07_24_120002_add_stripe_customer_id_to_tenants_table	33
86	2026_07_24_120003_create_suscripciones_table	33
87	2026_07_24_120004_create_stripe_webhook_events_table	33
88	2026_07_27_223623_add_pin_to_usuarios_table	34
89	2026_07_27_225200_make_usuarios_email_nullable	35
90	2026_08_06_150000_create_erp_pedido_pagos_table	36
91	2026_08_06_150100_backfill_cuentas_pago_tenants_existentes	36
92	2026_08_06_180000_add_imagen_to_productos_table	37
\.


--
-- Data for Name: modulos; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.modulos (id_modulo, clave, nombre, created_at, updated_at) FROM stdin;
1	crm	CRM	2026-07-09 17:18:05	2026-07-09 17:18:05
2	erp	ERP	2026-07-09 17:18:05	2026-07-09 17:18:05
3	pos	POS	2026-07-09 17:18:05	2026-07-09 17:18:05
\.


--
-- Data for Name: negocios; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.negocios (id_tiponegocio, nombre_negocio, created_at, updated_at, slug) FROM stdin;
1	SaaS	\N	\N	\N
2	Hotel	2026-07-03 18:23:58	2026-07-03 18:23:58	hotel
3	Restaurante	2026-07-03 18:23:58	2026-07-03 18:23:58	restaurante
4	Almacén / Bodega	2026-07-03 18:23:58	2026-07-03 18:23:58	almacen
5	Farmacia	2026-07-03 18:23:58	2026-07-03 18:23:58	farmacia
6	Startup	2026-07-03 18:23:58	2026-07-03 18:23:58	startup
7	Tienda / Retail	2026-07-03 18:23:58	2026-07-03 18:23:58	tienda
\.


--
-- Data for Name: notificaciones; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.notificaciones (id_notificacion, id_tenant, id_cliente, titulo, mensaje, tipo, leida, url, created_at, updated_at, id_usuario) FROM stdin;
1	21	8	🎉 Oportunidad ganada	"Contrato anual" con Roberto Sánchez por $50,000.00 fue marcada como ganada.	success	t	/crm/oportunidades	2026-07-03 22:17:44	2026-07-03 22:18:17	25
2	14	2	🎉 Oportunidad ganada	"TestUnico1783117777" con Juan Pérez por $30,000.00 fue marcada como ganada.	success	f	/crm/oportunidades	2026-07-03 22:30:21	2026-07-03 22:30:21	18
3	14	2	🎉 Oportunidad ganada	"TestUnico1783117905" con Juan Pérez por $30,000.00 fue marcada como ganada.	success	f	/crm/oportunidades	2026-07-03 22:32:16	2026-07-03 22:32:16	18
15	1	\N	✅ Tarea completada	"hrd" del proyecto "dff" fue marcada como completada.	success	t	/erp	2026-07-24 20:28:53	2026-07-24 20:37:11	7
13	1	\N	✅ Tarea completada	"frd" del proyecto "dff" fue marcada como completada.	success	t	/erp	2026-07-21 21:23:07	2026-07-24 20:37:12	7
11	1	\N	✅ Tarea completada	"yy" del proyecto "dff" fue marcada como completada.	success	t	/erp	2026-07-16 23:13:37	2026-07-24 20:37:13	7
17	1	18	🎉 Oportunidad ganada	"Test PW - Ganada" con Público General por $1,000.00 fue marcada como ganada.	success	f	/crm/oportunidades	2026-08-07 15:13:57	2026-08-07 15:13:57	7
18	1	18	Oportunidad perdida	"Test PW - Perdida" con Público General por $500.00 fue marcada como perdida.	warning	f	/crm/oportunidades	2026-08-07 15:14:02	2026-08-07 15:14:02	7
16	35	21	🎉 Oportunidad ganada	"ygbhybgh" con sfdsxg por $56,662.25 fue marcada como ganada.	success	t	/crm/oportunidades	2026-08-07 14:04:51	2026-08-07 16:04:46	43
\.


--
-- Data for Name: oauth_access_tokens; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.oauth_access_tokens (id, user_id, client_id, name, scopes, revoked, created_at, updated_at, expires_at) FROM stdin;
1fcb5a83657ddb38d94615e06ab62bcaa80b08c121b42d99ba99d0c2bc1dae0ee240b395f9a900a7	7	019f482a-b3ff-716e-b295-2e44f7b1d47f	\N	[]	t	2026-07-09 18:36:26	2026-07-09 18:36:26	2026-07-09 18:51:26
26c67895260799b7d32708e6677fc0be4decad877f1741bf2f7d27e99ff8f19d4d39f1c7baee65f6	7	019f4822-0b56-7386-8fef-5304cb786205	\N	[]	f	2026-07-09 18:26:58	2026-07-09 18:26:58	2026-07-09 18:41:58
524ab5db7c840b5493be22a1f66cd7e5d7e8c56a71a5c33d31460495bcab2a8e4a6de163523cb1a1	7	019f4828-d186-70b2-8027-e2700856b6b8	\N	[]	f	2026-07-09 18:34:22	2026-07-09 18:34:22	2026-07-09 18:49:22
9f7aa5fbba1391f4ce001ef28fc4204ca6714591631b5728ad5e20609a6bbb973f09dc810d85567a	7	019f482a-b3ff-716e-b295-2e44f7b1d47f	\N	[]	f	2026-07-09 18:36:26	2026-07-09 18:36:26	2026-07-09 18:51:26
b2c385e4fb8b6bde0419aee3a5f850c1c5d28325f3ce8d50f6f73286f97577d7217e36a94a45ad5c	7	019f4824-b640-70f8-9a37-d35071e45ba2	\N	[]	f	2026-07-09 18:29:53	2026-07-09 18:29:53	2026-07-09 18:44:53
\.


--
-- Data for Name: oauth_auth_codes; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.oauth_auth_codes (id, user_id, client_id, scopes, revoked, expires_at) FROM stdin;
\.


--
-- Data for Name: oauth_clients; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.oauth_clients (id, owner_type, owner_id, name, secret, provider, redirect_uris, grant_types, revoked, created_at, updated_at) FROM stdin;
019f481b-508c-7006-81bd-5595132f1b4d	\N	\N	STRATo Hub SPA	$2y$12$hVdxBVflkCh/fEU.9YTzdOwM1TqC/m58VoffeEGCzLO2pSuhUp/0S	usuarios	[]	["password","refresh_token"]	f	2026-07-09 18:19:37	2026-07-09 18:19:37
019f4833-80db-73d7-8604-ea670d4d4be0	\N	\N	STRATo Hub SPA (PKCE)	\N	\N	["http:\\/\\/localhost:4200\\/auth\\/callback"]	["authorization_code","refresh_token"]	f	2026-07-09 18:46:02	2026-07-09 18:46:02
\.


--
-- Data for Name: oauth_device_codes; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.oauth_device_codes (id, user_id, client_id, user_code, scopes, revoked, user_approved_at, last_polled_at, expires_at) FROM stdin;
\.


--
-- Data for Name: oauth_refresh_tokens; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.oauth_refresh_tokens (id, access_token_id, revoked, expires_at) FROM stdin;
0e8b59dae6e4c8b011f68b033ba989d99d03aefccc12eb7faaeee5bff34decb5c56347a7b965c9b4	b2c385e4fb8b6bde0419aee3a5f850c1c5d28325f3ce8d50f6f73286f97577d7217e36a94a45ad5c	f	2026-08-08 18:29:53
1d5d45a750c147039fda1995b4bb7a861b31ca687ab33d2c91be65f6946d9a05dedf8cd6726ec115	9f7aa5fbba1391f4ce001ef28fc4204ca6714591631b5728ad5e20609a6bbb973f09dc810d85567a	f	2026-08-08 18:36:26
6e65231422c598f070df9807768b481cda933e783820b1aa6d6ede120ae2c7e945ba92044cd57b7f	26c67895260799b7d32708e6677fc0be4decad877f1741bf2f7d27e99ff8f19d4d39f1c7baee65f6	f	2026-08-08 18:26:58
9ba52389af88f380b842f6001d9e34f639b821c12840d75ef780811f0395cd9e11f2a490771cecc8	524ab5db7c840b5493be22a1f66cd7e5d7e8c56a71a5c33d31460495bcab2a8e4a6de163523cb1a1	f	2026-08-08 18:34:22
baf4373eaa3851dd26788def7c4a7bc73db9bcae63ef3c8a5f6250391b05d00842b467985dd469fe	1fcb5a83657ddb38d94615e06ab62bcaa80b08c121b42d99ba99d0c2bc1dae0ee240b395f9a900a7	t	2026-08-08 18:36:26
\.


--
-- Data for Name: oportunidades; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.oportunidades (id_oportunidad, id_tenant, id_cliente, id_pipeline, id_usuario, titulo, valor, probabilidad, estado, fecha_cierre, deleted_at, created_at, updated_at, etapa) FROM stdin;
1	14	2	23	18	Venta grande	15000.00	0	abierta	\N	2026-07-03 22:29:37	2026-07-03 20:56:05	2026-07-03 22:29:37	propuesta
2	15	5	25	19	Interés en sistema ERP	25000.00	0	abierta	\N	\N	2026-07-03 21:31:37	2026-07-03 21:31:37	prospeccion
3	19	7	33	23	Interés en sistema ERP	0.00	0	abierta	\N	\N	2026-07-03 21:39:54	2026-07-03 21:39:54	prospeccion
5	21	8	39	25	Contrato anual	50000.00	0	abierta	\N	\N	2026-07-03 22:17:34	2026-07-03 22:18:17	negociacion
6	14	2	23	18	Contrato de soporte	30000.00	0	abierta	\N	2026-07-03 22:29:37	2026-07-03 22:25:53	2026-07-03 22:29:37	negociacion
7	14	2	23	18	Contrato de soporte	30000.00	0	abierta	\N	2026-07-03 22:29:37	2026-07-03 22:26:04	2026-07-03 22:29:37	negociacion
8	14	2	23	18	TestUnico1783117777	30000.00	0	ganada	2026-07-03	2026-07-03 22:31:45	2026-07-03 22:29:37	2026-07-03 22:31:45	cierre
9	14	2	23	18	TestUnico1783117905	30000.00	0	ganada	2026-07-03	\N	2026-07-03 22:31:45	2026-07-03 22:32:16	cierre
10	35	21	59	43	ygbhybgh	56662.25	0	ganada	2026-08-07	\N	2026-08-07 14:02:37	2026-08-07 14:04:51	cierre
11	1	18	38	7	Test PW - Ganada	1000.00	0	ganada	2026-08-07	2026-08-07 15:15:01	2026-08-07 15:13:49	2026-08-07 15:15:01	cierre
12	1	18	38	7	Test PW - Perdida	500.00	0	perdida	2026-08-07	2026-08-07 15:15:02	2026-08-07 15:13:51	2026-08-07 15:15:02	cierre
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: permisos; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.permisos (id_permiso, clave, id_modulo, descripcion, created_at, updated_at) FROM stdin;
1	categorias.ver	\N	Ver categorias	2026-07-16 22:24:07	2026-07-16 22:24:07
2	categorias.crear	\N	Crear categorias	2026-07-16 22:24:07	2026-07-16 22:24:07
3	categorias.editar	\N	Editar categorias	2026-07-16 22:24:07	2026-07-16 22:24:07
4	categorias.eliminar	\N	Eliminar categorias	2026-07-16 22:24:07	2026-07-16 22:24:07
5	productos.ver	\N	Ver productos	2026-07-16 22:24:07	2026-07-16 22:24:07
6	productos.crear	\N	Crear productos	2026-07-16 22:24:07	2026-07-16 22:24:07
7	productos.editar	\N	Editar productos	2026-07-16 22:24:07	2026-07-16 22:24:07
8	productos.eliminar	\N	Eliminar productos	2026-07-16 22:24:07	2026-07-16 22:24:07
9	clientes.ver	\N	Ver clientes	2026-07-16 22:24:07	2026-07-16 22:24:07
10	clientes.crear	\N	Crear clientes	2026-07-16 22:24:07	2026-07-16 22:24:07
11	clientes.editar	\N	Editar clientes	2026-07-16 22:24:07	2026-07-16 22:24:07
12	clientes.eliminar	\N	Eliminar clientes	2026-07-16 22:24:07	2026-07-16 22:24:07
13	contactos.ver	\N	Ver contactos	2026-07-16 22:24:07	2026-07-16 22:24:07
14	contactos.crear	\N	Crear contactos	2026-07-16 22:24:07	2026-07-16 22:24:07
15	contactos.editar	\N	Editar contactos	2026-07-16 22:24:07	2026-07-16 22:24:07
16	contactos.eliminar	\N	Eliminar contactos	2026-07-16 22:24:07	2026-07-16 22:24:07
17	leads.ver	\N	Ver leads	2026-07-16 22:24:07	2026-07-16 22:24:07
18	leads.crear	\N	Crear leads	2026-07-16 22:24:07	2026-07-16 22:24:07
19	leads.editar	\N	Editar leads	2026-07-16 22:24:07	2026-07-16 22:24:07
20	leads.eliminar	\N	Eliminar leads	2026-07-16 22:24:07	2026-07-16 22:24:07
21	pipelines.ver	\N	Ver pipelines	2026-07-16 22:24:07	2026-07-16 22:24:07
22	pipelines.crear	\N	Crear pipelines	2026-07-16 22:24:07	2026-07-16 22:24:07
23	pipelines.editar	\N	Editar pipelines	2026-07-16 22:24:07	2026-07-16 22:24:07
24	pipelines.eliminar	\N	Eliminar pipelines	2026-07-16 22:24:07	2026-07-16 22:24:07
25	oportunidades.ver	\N	Ver oportunidades	2026-07-16 22:24:07	2026-07-16 22:24:07
26	oportunidades.crear	\N	Crear oportunidades	2026-07-16 22:24:07	2026-07-16 22:24:07
27	oportunidades.editar	\N	Editar oportunidades	2026-07-16 22:24:07	2026-07-16 22:24:07
28	oportunidades.eliminar	\N	Eliminar oportunidades	2026-07-16 22:24:07	2026-07-16 22:24:07
29	actividades.ver	\N	Ver actividades	2026-07-16 22:24:07	2026-07-16 22:24:07
30	actividades.crear	\N	Crear actividades	2026-07-16 22:24:07	2026-07-16 22:24:07
31	actividades.editar	\N	Editar actividades	2026-07-16 22:24:07	2026-07-16 22:24:07
32	actividades.eliminar	\N	Eliminar actividades	2026-07-16 22:24:07	2026-07-16 22:24:07
33	marketing.ver	\N	Ver marketing	2026-07-16 22:24:07	2026-07-16 22:24:07
34	marketing.crear	\N	Crear marketing	2026-07-16 22:24:07	2026-07-16 22:24:07
35	marketing.editar	\N	Editar marketing	2026-07-16 22:24:07	2026-07-16 22:24:07
36	marketing.eliminar	\N	Eliminar marketing	2026-07-16 22:24:07	2026-07-16 22:24:07
37	automatizaciones.ver	\N	Ver automatizaciones	2026-07-16 22:24:07	2026-07-16 22:24:07
38	automatizaciones.crear	\N	Crear automatizaciones	2026-07-16 22:24:07	2026-07-16 22:24:07
39	automatizaciones.editar	\N	Editar automatizaciones	2026-07-16 22:24:07	2026-07-16 22:24:07
40	automatizaciones.eliminar	\N	Eliminar automatizaciones	2026-07-16 22:24:07	2026-07-16 22:24:07
41	integraciones.ver	\N	Ver integraciones	2026-07-16 22:24:07	2026-07-16 22:24:07
42	integraciones.crear	\N	Crear integraciones	2026-07-16 22:24:07	2026-07-16 22:24:07
43	integraciones.editar	\N	Editar integraciones	2026-07-16 22:24:07	2026-07-16 22:24:07
44	integraciones.eliminar	\N	Eliminar integraciones	2026-07-16 22:24:07	2026-07-16 22:24:07
45	erp_inventario.ver	\N	Ver erp inventario	2026-07-16 22:24:07	2026-07-16 22:24:07
46	erp_inventario.crear	\N	Crear erp inventario	2026-07-16 22:24:07	2026-07-16 22:24:07
47	erp_inventario.editar	\N	Editar erp inventario	2026-07-16 22:24:07	2026-07-16 22:24:07
48	erp_inventario.eliminar	\N	Eliminar erp inventario	2026-07-16 22:24:07	2026-07-16 22:24:07
49	erp_proveedores.ver	\N	Ver erp proveedores	2026-07-16 22:24:07	2026-07-16 22:24:07
50	erp_proveedores.crear	\N	Crear erp proveedores	2026-07-16 22:24:07	2026-07-16 22:24:07
51	erp_proveedores.editar	\N	Editar erp proveedores	2026-07-16 22:24:07	2026-07-16 22:24:07
52	erp_proveedores.eliminar	\N	Eliminar erp proveedores	2026-07-16 22:24:07	2026-07-16 22:24:07
53	erp_compras.ver	\N	Ver erp compras	2026-07-16 22:24:07	2026-07-16 22:24:07
54	erp_compras.crear	\N	Crear erp compras	2026-07-16 22:24:07	2026-07-16 22:24:07
55	erp_compras.editar	\N	Editar erp compras	2026-07-16 22:24:07	2026-07-16 22:24:07
56	erp_compras.eliminar	\N	Eliminar erp compras	2026-07-16 22:24:07	2026-07-16 22:24:07
57	erp_finanzas.ver	\N	Ver erp finanzas	2026-07-16 22:24:07	2026-07-16 22:24:07
58	erp_finanzas.crear	\N	Crear erp finanzas	2026-07-16 22:24:07	2026-07-16 22:24:07
59	erp_finanzas.editar	\N	Editar erp finanzas	2026-07-16 22:24:07	2026-07-16 22:24:07
60	erp_finanzas.eliminar	\N	Eliminar erp finanzas	2026-07-16 22:24:07	2026-07-16 22:24:07
61	erp_ventas.ver	\N	Ver erp ventas	2026-07-16 22:24:07	2026-07-16 22:24:07
62	erp_ventas.crear	\N	Crear erp ventas	2026-07-16 22:24:07	2026-07-16 22:24:07
63	erp_ventas.editar	\N	Editar erp ventas	2026-07-16 22:24:07	2026-07-16 22:24:07
64	erp_ventas.eliminar	\N	Eliminar erp ventas	2026-07-16 22:24:07	2026-07-16 22:24:07
65	erp_rrhh.ver	\N	Ver erp rrhh	2026-07-16 22:24:07	2026-07-16 22:24:07
66	erp_rrhh.crear	\N	Crear erp rrhh	2026-07-16 22:24:07	2026-07-16 22:24:07
67	erp_rrhh.editar	\N	Editar erp rrhh	2026-07-16 22:24:07	2026-07-16 22:24:07
68	erp_rrhh.eliminar	\N	Eliminar erp rrhh	2026-07-16 22:24:07	2026-07-16 22:24:07
69	erp_fabricacion.ver	\N	Ver erp fabricacion	2026-07-16 22:24:07	2026-07-16 22:24:07
70	erp_fabricacion.crear	\N	Crear erp fabricacion	2026-07-16 22:24:07	2026-07-16 22:24:07
71	erp_fabricacion.editar	\N	Editar erp fabricacion	2026-07-16 22:24:07	2026-07-16 22:24:07
72	erp_fabricacion.eliminar	\N	Eliminar erp fabricacion	2026-07-16 22:24:07	2026-07-16 22:24:07
73	erp_scm.ver	\N	Ver erp scm	2026-07-16 22:24:07	2026-07-16 22:24:07
74	erp_scm.crear	\N	Crear erp scm	2026-07-16 22:24:07	2026-07-16 22:24:07
75	erp_scm.editar	\N	Editar erp scm	2026-07-16 22:24:07	2026-07-16 22:24:07
76	erp_scm.eliminar	\N	Eliminar erp scm	2026-07-16 22:24:07	2026-07-16 22:24:07
77	erp_proyectos.ver	\N	Ver erp proyectos	2026-07-16 22:24:07	2026-07-16 22:24:07
78	erp_proyectos.crear	\N	Crear erp proyectos	2026-07-16 22:24:07	2026-07-16 22:24:07
79	erp_proyectos.editar	\N	Editar erp proyectos	2026-07-16 22:24:07	2026-07-16 22:24:07
80	erp_proyectos.eliminar	\N	Eliminar erp proyectos	2026-07-16 22:24:07	2026-07-16 22:24:07
\.


--
-- Data for Name: personal_access_tokens; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.personal_access_tokens (id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at) FROM stdin;
1	App\\Models\\Usuarios	1	api_token	f0b7a9972ada58aae86b23f36a5f4cfc3a2d9dadc12aa628e436346068c3f456	["*"]	2026-06-30 22:55:12	\N	2026-06-30 22:54:43	2026-06-30 22:55:12
3	App\\Models\\Usuarios	3	api_token	3a8de095a1a3ec7a5dd70b73ac3d2a65ec10c8fc9e6f5bad0b248c3a4fea6275	["*"]	\N	\N	2026-07-01 22:36:37	2026-07-01 22:36:37
4	App\\Models\\Usuarios	3	api_token	e75703d4ef958451a9570444933958a5939bb92b457789b455f70f9419c06186	["*"]	\N	\N	2026-07-01 23:21:16	2026-07-01 23:21:16
5	App\\Models\\Usuarios	3	api_token	28e18b1efc350e8923f36b353266e956d75c3228276a8201a753ee290647fa71	["*"]	\N	\N	2026-07-01 23:22:12	2026-07-01 23:22:12
6	App\\Models\\Usuarios	3	api_token	b4a30ba0263c330270a765ee9e35b9c100e1230e0a91aa7e9516bab6d5462082	["*"]	2026-07-01 23:23:02	\N	2026-07-01 23:23:01	2026-07-01 23:23:02
7	App\\Models\\Usuarios	3	api_token	8caae6feda3aeccdf7b8836d407f4a714330bf96fee8b943b97806390e923a9f	["*"]	2026-07-01 23:24:40	\N	2026-07-01 23:24:38	2026-07-01 23:24:40
8	App\\Models\\Usuarios	4	api_token	11ec047db8b899bcd4035103cc5f9ed4abd12a5743f2ac2724b937e0e4477fd7	["*"]	2026-07-01 23:31:28	\N	2026-07-01 23:31:27	2026-07-01 23:31:28
9	App\\Models\\Usuarios	5	api_token	eb47817fe3c2cf079c7fa71ba53268ce6d832babb7ac505b0018e09330060121	["*"]	2026-07-03 18:27:00	\N	2026-07-02 22:17:33	2026-07-03 18:27:00
11	App\\Models\\Usuarios	6	api_token	8640f06cc29a7cd50f08d13729d8e8277538112ec27b33f323cbabb5bb54842d	["*"]	2026-07-03 15:38:48	\N	2026-07-03 15:38:47	2026-07-03 15:38:48
12	App\\Models\\Usuarios	8	api_token	0588e20c391d0fbc9492d31f1c4dac7aef15b312f1bc52233937dbfa57f52d58	["*"]	2026-07-03 18:24:22	\N	2026-07-03 18:24:21	2026-07-03 18:24:22
13	App\\Models\\Usuarios	9	api_token	db500b312fbae650e826d978ec7cd57025cd829fd11d289d9f41dc7ace5c7be9	["*"]	2026-07-03 18:27:54	\N	2026-07-03 18:27:54	2026-07-03 18:27:54
14	App\\Models\\Usuarios	6	api_token	c6e21ae8674fea0acea6fec5c955a8b60a9d90856a953b204d90435950f4a6f4	["*"]	\N	\N	2026-07-03 18:28:09	2026-07-03 18:28:09
16	App\\Models\\Usuarios	10	api_token	3a9d18e3f0d1c672c392ef0975974026477e379701cce222b5ebedb6662b71b2	["*"]	\N	\N	2026-07-03 18:44:38	2026-07-03 18:44:38
17	App\\Models\\Usuarios	11	api_token	232102fe7a8d215f61651ce0974eed9e82397dcdbd3f9ad40b8ba8d8678f1c82	["*"]	\N	\N	2026-07-03 18:45:42	2026-07-03 18:45:42
18	App\\Models\\Usuarios	12	api_token	40bd52265aa03e4a01717e600ae8e3f018a02899b1c713fbf586575d2dcfe410	["*"]	\N	\N	2026-07-03 18:46:30	2026-07-03 18:46:30
19	App\\Models\\Usuarios	13	api_token	445aa0d6d469e9eedece4c5ce72f2075c80b4e38f1c96e11a128d941200ea704	["*"]	2026-07-03 18:47:36	\N	2026-07-03 18:47:31	2026-07-03 18:47:36
20	App\\Models\\Usuarios	14	api_token	9718bc9ea69ec82f7e5d9c0e69cac95d72d61c6843eccbe1d866ef42471df897	["*"]	2026-07-03 18:49:56	\N	2026-07-03 18:49:47	2026-07-03 18:49:56
21	App\\Models\\Usuarios	15	api_token	c97a5d0b093e4a960e4058eadc0170e970c550c7e8f0994451e0295c2ff0a5b5	["*"]	2026-07-03 18:55:49	\N	2026-07-03 18:55:40	2026-07-03 18:55:49
22	App\\Models\\Usuarios	16	api_token	f09ea53b41a4bb700702a88f6ab0125eebdee778b0d7d8b32562e9758726acba	["*"]	2026-07-03 18:57:41	\N	2026-07-03 18:57:35	2026-07-03 18:57:41
23	App\\Models\\Usuarios	17	api_token	9ce04529a33a08212c1efd97173b5153579005ae4b19cd51c3c3b2dffea9f1f6	["*"]	2026-07-03 18:59:35	\N	2026-07-03 18:59:27	2026-07-03 18:59:35
24	App\\Models\\Usuarios	18	api_token	68850a98018e10f9d5cd7b8560c01d6f91942b0307f599ee189f7381319dab23	["*"]	2026-07-03 20:32:45	\N	2026-07-03 20:32:44	2026-07-03 20:32:45
25	App\\Models\\Usuarios	18	api_token	cb212bb409dd5846b9578708b0d1ff88e514f2062fb6318b888ad1374ccd8da6	["*"]	2026-07-03 20:33:19	\N	2026-07-03 20:33:16	2026-07-03 20:33:19
26	App\\Models\\Usuarios	18	api_token	23697d234549378d2d310eef32cc49261f850b1c1f4d5af337a6d50a821f5cdc	["*"]	2026-07-03 20:35:50	\N	2026-07-03 20:35:46	2026-07-03 20:35:50
27	App\\Models\\Usuarios	18	api_token	698bd528fbe7623c7fc0c546236df4574378119a6a7a27deaf59f46587ea9049	["*"]	2026-07-03 20:37:34	\N	2026-07-03 20:37:29	2026-07-03 20:37:34
28	App\\Models\\Usuarios	18	api_token	b647d58d8d18bbb0715fcdcf74771553d8132348699d456a492743d816bd63f4	["*"]	2026-07-03 20:38:49	\N	2026-07-03 20:38:45	2026-07-03 20:38:49
29	App\\Models\\Usuarios	18	api_token	e081bdc879ec8973447a767b1937e45d48bf0a29028a6bf1a190c1a998d4ff59	["*"]	2026-07-03 20:41:04	\N	2026-07-03 20:41:01	2026-07-03 20:41:04
30	App\\Models\\Usuarios	18	api_token	ad9f6048ef0de155e3f319d1786660b538b6ffe6110b898b6842a3927757f810	["*"]	2026-07-03 20:42:59	\N	2026-07-03 20:42:58	2026-07-03 20:42:59
31	App\\Models\\Usuarios	18	api_token	110e302f1cd45f007d160659620d91d6fdfeddd939e4b499ca8263ca23525b88	["*"]	2026-07-03 20:52:54	\N	2026-07-03 20:52:53	2026-07-03 20:52:54
32	App\\Models\\Usuarios	18	api_token	d78073b7fdcd210ab8a2cd8cdbf0e67bf26e6ca569b717d6a47fa05ef97dfdd0	["*"]	2026-07-03 20:53:18	\N	2026-07-03 20:53:18	2026-07-03 20:53:18
33	App\\Models\\Usuarios	18	api_token	218cdb539edc21bb41f950016b15edb51b7c3a72beef909df4bcf9f23435e4ca	["*"]	2026-07-03 20:53:50	\N	2026-07-03 20:53:41	2026-07-03 20:53:50
34	App\\Models\\Usuarios	18	api_token	8b72806b7a008e2b3b5b795362ec0745388a9fe63bfad095ca0247a357c68962	["*"]	2026-07-03 20:54:55	\N	2026-07-03 20:54:46	2026-07-03 20:54:55
35	App\\Models\\Usuarios	18	api_token	9dfa991b6a87bde83d107e13811846a7992d7536406fef9b7ed5efed8ade2279	["*"]	2026-07-03 20:55:18	\N	2026-07-03 20:55:18	2026-07-03 20:55:18
36	App\\Models\\Usuarios	18	api_token	e958b4478657e06b168842b10742ebbf0c9cdcde89101f71ba7e6702662d5a04	["*"]	2026-07-03 20:55:35	\N	2026-07-03 20:55:35	2026-07-03 20:55:35
37	App\\Models\\Usuarios	18	api_token	f8c7f48e663561a1b30cb75c3d8feddd1418c252e81e96f31a343a4403cde269	["*"]	2026-07-03 20:55:48	\N	2026-07-03 20:55:48	2026-07-03 20:55:48
38	App\\Models\\Usuarios	18	api_token	20c72b1e7b50b000a14d29d8906413300abf8aa51ba0ea11d52734ddbd4b675d	["*"]	2026-07-03 20:56:05	\N	2026-07-03 20:56:05	2026-07-03 20:56:05
39	App\\Models\\Usuarios	18	api_token	a477d034cc403442777f4266fb0efc684c078081565f2a57286f4d289b333945	["*"]	2026-07-03 20:56:36	\N	2026-07-03 20:56:30	2026-07-03 20:56:36
40	App\\Models\\Usuarios	19	api_token	bab129525de2bc7eb865045ce12bb12002d2dd2aab8c4199e770b5cb52a8fe59	["*"]	2026-07-03 21:31:37	\N	2026-07-03 21:31:37	2026-07-03 21:31:37
41	App\\Models\\Usuarios	19	api_token	c9227cbe15710f9298c87e45b0c0e0faeb896ce8b77457bf31d8cced27495df4	["*"]	2026-07-03 21:31:52	\N	2026-07-03 21:31:51	2026-07-03 21:31:52
42	App\\Models\\Usuarios	20	api_token	05178937198ce790da63742395df09e98812fb5571d1eafc854b6e6f34e30910	["*"]	2026-07-03 21:31:53	\N	2026-07-03 21:31:52	2026-07-03 21:31:53
43	App\\Models\\Usuarios	19	api_token	1a1cbb3a40c6fa5aadf04cf52162c61fb4f00ef5a8e21501eb256ae83be7d4a9	["*"]	2026-07-03 21:32:12	\N	2026-07-03 21:32:12	2026-07-03 21:32:12
44	App\\Models\\Usuarios	21	api_token	0f9ddd467390f0faf49a800d01949ae07f926c8b7fbe7cdd5b6f4fdf0664eb66	["*"]	2026-07-03 21:36:31	\N	2026-07-03 21:36:13	2026-07-03 21:36:31
45	App\\Models\\Usuarios	22	api_token	a24c6bbdf3c283d9493f8b02e80fc9637f4912d990f6315f892a5fe21fd4bbb3	["*"]	2026-07-03 21:38:19	\N	2026-07-03 21:38:01	2026-07-03 21:38:19
46	App\\Models\\Usuarios	23	api_token	4992142f32b342d5bda1d4830ef263f118d543f47bb0f39ba0a56cf11f116bd3	["*"]	2026-07-03 21:39:56	\N	2026-07-03 21:39:43	2026-07-03 21:39:56
47	App\\Models\\Usuarios	24	api_token	a11dc2f2479c3b581e9d8e86b43371bd9338eddc16cbdac8056351390fb151d9	["*"]	2026-07-03 21:51:01	\N	2026-07-03 21:50:48	2026-07-03 21:51:01
48	App\\Models\\Usuarios	25	api_token	ccf63cb1803593f9f7f48c38432527aa4b90018b62e520940ff2816d48f6b4c9	["*"]	2026-07-03 22:17:35	\N	2026-07-03 22:17:34	2026-07-03 22:17:35
49	App\\Models\\Usuarios	25	api_token	f88072f4cf98397d204f99d5dc194c1658033d0cf48972c18e3e8b09a6d6788f	["*"]	2026-07-03 22:17:44	\N	2026-07-03 22:17:44	2026-07-03 22:17:44
50	App\\Models\\Usuarios	25	api_token	f47d92ffd37884f15fa6d1cf2e3ed9c34770c00008dd481869dfce2a257460f1	["*"]	2026-07-03 22:17:56	\N	2026-07-03 22:17:53	2026-07-03 22:17:56
51	App\\Models\\Usuarios	25	api_token	7719849505462a215323970051fee2515a2d7e4d1a0659cb0ac01295df2a96d2	["*"]	2026-07-03 22:18:06	\N	2026-07-03 22:18:06	2026-07-03 22:18:06
52	App\\Models\\Usuarios	25	api_token	b43444d7f83837933328469014c12727dbb893d9f18b33d37610ba8336959be8	["*"]	2026-07-03 22:18:17	\N	2026-07-03 22:18:16	2026-07-03 22:18:17
53	App\\Models\\Usuarios	26	api_token	697c878a15bc033ba87f2c5ba2ba1d735b66ad576dccb1f0e2e14f1f9a5d9346	["*"]	2026-07-03 22:24:54	\N	2026-07-03 22:24:45	2026-07-03 22:24:54
54	App\\Models\\Usuarios	18	api_token	8de4ed1bc23ede90ba9323e2e8575ba0a580e9d5f145c2302af9d44c6c6b0679	["*"]	2026-07-03 22:25:28	\N	2026-07-03 22:25:25	2026-07-03 22:25:28
55	App\\Models\\Usuarios	18	api_token	07e2a8c1a330bc0e49ca582b831bc3ed8db5dcd9f1cce44109eda0422b7b7a67	["*"]	2026-07-03 22:25:53	\N	2026-07-03 22:25:53	2026-07-03 22:25:53
56	App\\Models\\Usuarios	18	api_token	12a6745744aefa9dd0bfbc559c47b152f09f9296824b84f6208b2554d201e0d4	["*"]	2026-07-03 22:26:04	\N	2026-07-03 22:26:04	2026-07-03 22:26:04
57	App\\Models\\Usuarios	18	api_token	954083bc94e661b5e8b64a6831d26bfe72d967b58d8f8b01a8ad43a709506d80	["*"]	2026-07-03 22:27:15	\N	2026-07-03 22:27:09	2026-07-03 22:27:15
58	App\\Models\\Usuarios	18	api_token	15c4aa4d3a276854341ed29c9cf09836c8b346001cbbc06f2b53de730f17f93c	["*"]	2026-07-03 22:28:34	\N	2026-07-03 22:28:34	2026-07-03 22:28:34
59	App\\Models\\Usuarios	18	api_token	5251be2f5e45779ea20bf1c94542032dd4ea0b78ca3e121ffff342bc28c7a9d1	["*"]	2026-07-03 22:28:52	\N	2026-07-03 22:28:52	2026-07-03 22:28:52
60	App\\Models\\Usuarios	18	api_token	1defb7ace70a6049e3be0d347a723974b33ad0982c7ecdb29f120a85e351bd61	["*"]	2026-07-03 22:29:20	\N	2026-07-03 22:29:14	2026-07-03 22:29:20
61	App\\Models\\Usuarios	18	api_token	7988e2a869c44421cb0973779e7b9134aad32f57d26259c5002a93d036b5428f	["*"]	2026-07-03 22:29:37	\N	2026-07-03 22:29:37	2026-07-03 22:29:37
62	App\\Models\\Usuarios	18	api_token	58d5380e8b16f1cdd0e29baefad42d18726ae08d7d0c8af355c63eaf53542850	["*"]	2026-07-03 22:30:21	\N	2026-07-03 22:30:08	2026-07-03 22:30:21
63	App\\Models\\Usuarios	18	api_token	ecca97a7cbac0094706ff52c3eee50900fb711fb3bbe593b331dfbede0a25d7e	["*"]	2026-07-03 22:31:45	\N	2026-07-03 22:31:45	2026-07-03 22:31:45
64	App\\Models\\Usuarios	18	api_token	e3585ae755ba1c64910e79836e8953693a9821ec4a9913b4bc868ff6a6c8ad3f	["*"]	2026-07-03 22:32:17	\N	2026-07-03 22:32:06	2026-07-03 22:32:17
65	App\\Models\\Usuarios	18	api_token	8ee4032a09b1bdfaaa76cc190710450298dcb82d45efeb1705ae8b9a139ac688	["*"]	2026-07-03 22:51:55	\N	2026-07-03 22:51:54	2026-07-03 22:51:55
66	App\\Models\\Usuarios	18	api_token	f8954e52b623bb161d312c48be2836de891d518ae0861d33669577f35f7aada9	["*"]	2026-07-03 22:52:39	\N	2026-07-03 22:52:33	2026-07-03 22:52:39
67	App\\Models\\Usuarios	18	api_token	73c451c852fa3c48ad14612247b57b40321d0c0d6a6d333d27e18471f13be8b7	["*"]	2026-07-03 23:01:14	\N	2026-07-03 23:01:04	2026-07-03 23:01:14
68	App\\Models\\Usuarios	18	api_token	018de2c38d59cd23aa1eeeec34bef8d5098b2bfbc473249ca1c8758493d82657	["*"]	2026-07-03 23:02:19	\N	2026-07-03 23:02:16	2026-07-03 23:02:19
69	App\\Models\\Usuarios	18	api_token	3ec1f04b6516963c1202c57d1bb8153759d2614a9d18a2cbe5615d7c50b7e8e9	["*"]	2026-07-03 23:03:12	\N	2026-07-03 23:03:09	2026-07-03 23:03:12
70	App\\Models\\Usuarios	18	api_token	2a107e9b2585275c97bc75772c8d58879c2248bebc95c9eae07a218f02ac97e2	["*"]	2026-07-03 23:04:14	\N	2026-07-03 23:04:12	2026-07-03 23:04:14
71	App\\Models\\Usuarios	18	api_token	6dc105210faaca8c0226d099f7abae3c23324a7134f89f42645cd28e1f79c58d	["*"]	2026-07-03 23:04:46	\N	2026-07-03 23:04:41	2026-07-03 23:04:46
73	App\\Models\\Usuarios	7	api_token	474d2027739b2e9bc005e68938f6c34380b4b856e08ed5fd19df890869f2a568	["*"]	2026-07-07 18:42:58	\N	2026-07-07 18:42:37	2026-07-07 18:42:58
74	App\\Models\\Usuarios	7	api_token	09d45836df41f55b86d3f8729c7879c3bb465ca3fffd0f6b4f0c253544f7425b	["*"]	2026-07-07 21:06:36	\N	2026-07-07 20:37:05	2026-07-07 21:06:36
75	App\\Models\\Usuarios	7	api_token	2c94736f23863f866a2c9ca437f972e4a871ba43e4837b48e408cae73c810ec0	["*"]	2026-07-07 22:52:14	\N	2026-07-07 22:52:13	2026-07-07 22:52:14
76	App\\Models\\Usuarios	7	api_token	bb58b2c911a1d3bf1e20fd9831cd7ce20cd4b06b356d67a6355a16e6f7028897	["*"]	2026-07-07 22:52:59	\N	2026-07-07 22:52:49	2026-07-07 22:52:59
77	App\\Models\\Usuarios	7	api_token	452d6d6dbda2fe89a3f9a6090f87f97aa954f77d9a3eb26cfdc8bb187a172a91	["*"]	2026-07-07 22:55:18	\N	2026-07-07 22:55:10	2026-07-07 22:55:18
78	App\\Models\\Usuarios	7	api_token	7723fd8dd4c2d6f52c7aa66cc68abb1138f1830f9aac39427342f68abb86f7d3	["*"]	2026-07-07 22:57:40	\N	2026-07-07 22:57:33	2026-07-07 22:57:40
79	App\\Models\\Usuarios	7	api_token	c900992b2ffc15cc6c84269c2974e58e3e198df1e042680f2af504f8b10d4366	["*"]	2026-07-07 23:00:29	\N	2026-07-07 23:00:23	2026-07-07 23:00:29
80	App\\Models\\Usuarios	7	api_token	24652d066f1b58c7fb62315cb5dd62c2ab7696a7679368b0fa15406f72e1a95a	["*"]	2026-07-07 23:04:11	\N	2026-07-07 23:04:04	2026-07-07 23:04:11
81	App\\Models\\Usuarios	7	api_token	35b6622d4cd65cf716e3402b93e2c5ab4bcd838a9122e59aed21470f249d656b	["*"]	2026-07-07 23:09:21	\N	2026-07-07 23:09:12	2026-07-07 23:09:21
82	App\\Models\\Usuarios	7	api_token	93b3a24d0fe6d36b146def5c3bd82f89288a8752d63b3272bcd434dbf1129d40	["*"]	2026-07-07 23:10:18	\N	2026-07-07 23:10:12	2026-07-07 23:10:18
83	App\\Models\\Usuarios	7	api_token	149e00cafd4bda46387388622a3fcf31529aa989ef3f25bb60388b5683988b1f	["*"]	2026-07-07 23:14:29	\N	2026-07-07 23:14:21	2026-07-07 23:14:29
84	App\\Models\\Usuarios	7	api_token	b4fa818c687da0bd794e4514b96cb34c46b1b4ff4c5423d7b5e5667d8b954b6a	["*"]	2026-07-07 23:14:44	\N	2026-07-07 23:14:43	2026-07-07 23:14:44
85	App\\Models\\Usuarios	1	test	61541352a87a7826deadbf8cfed3d7f30eb84476ea84f9a28f31e3a612e20225	["*"]	2026-07-08 15:29:51	\N	2026-07-08 15:27:36	2026-07-08 15:29:51
86	App\\Models\\Usuarios	1	api_token	97a330f627fdebd4045d81608299e4533544ccc6c7f2db565098850bfb9c6d12	["*"]	2026-07-08 16:55:47	\N	2026-07-08 16:00:06	2026-07-08 16:55:47
87	App\\Models\\Usuarios	1	api_token	2cded8f64f29b839fa1f0e41e4ab4d1a787a57f001d0d6ae1fe564a9ed8f57d0	["*"]	2026-07-08 16:00:48	\N	2026-07-08 16:00:36	2026-07-08 16:00:48
88	App\\Models\\Usuarios	1	api_token	f44c1295c56c27899d2c7ace9b2380283878664e7b1dd307d149ca1f770c97dd	["*"]	2026-07-08 16:01:34	\N	2026-07-08 16:01:25	2026-07-08 16:01:34
89	App\\Models\\Usuarios	1	api_token	c65cc1c3ebf1fa60de27557d420788cf584035d8800b1c3c582aa71741f9dda5	["*"]	2026-07-08 16:33:50	\N	2026-07-08 16:33:25	2026-07-08 16:33:50
90	App\\Models\\Usuarios	1	api_token	ee5e43f38c0b1474be0bf3e366ea931d1123a2986a35750c5e21724a3ba29e49	["*"]	2026-07-08 16:35:09	\N	2026-07-08 16:35:02	2026-07-08 16:35:09
91	App\\Models\\Usuarios	1	api_token	8ea5a68aaeba82101123d7000f5c79301af9b8d5ef2106c4e79f29bc2715de84	["*"]	2026-07-08 21:10:11	\N	2026-07-08 21:10:04	2026-07-08 21:10:11
92	App\\Models\\Usuarios	1	api_token	f2ff86a0a7d7988f4f253b51159a90dc7094819c4e39c31fd4663b035c354554	["*"]	2026-07-08 21:17:21	\N	2026-07-08 21:17:16	2026-07-08 21:17:21
93	App\\Models\\Usuarios	1	api_token	6afcbf11f31f02b8b25dde4eda2d601cb9660b64f0b00511bc53e9a900fd5a29	["*"]	2026-07-08 21:30:55	\N	2026-07-08 21:30:52	2026-07-08 21:30:55
100	App\\Models\\Usuarios	31	api_token	4d9df08222496f74ee24814df7254222ad2c3d1406b9a7d023aeb957a437f5e4	["*"]	\N	\N	2026-07-09 17:43:34	2026-07-09 17:43:34
102	App\\Models\\Usuarios	7	api_token	e09749c532029005935744a0d91869387a12723d2d42bc558d98ccb4530e9226	["*"]	\N	\N	2026-07-13 18:12:48	2026-07-13 18:12:48
103	App\\Models\\Usuarios	5	api_token	c6807519ca751ff06103fa23dc68631898d5e4992f32657a6e5b38da2c8b0b58	["*"]	\N	\N	2026-07-13 18:14:08	2026-07-13 18:14:08
104	App\\Models\\Usuarios	5	api_token	ae49797b0c36b58a70f4515dd759acf2b6237d7da6bedf06728d0ecbc20e923a	["*"]	\N	\N	2026-07-13 18:58:05	2026-07-13 18:58:05
105	App\\Models\\Usuarios	5	api_token	2d6ba6ba5c9655baa1833279ea588606c40eaf12fc9050db64daef9ddede363c	["*"]	\N	\N	2026-07-13 18:58:23	2026-07-13 18:58:23
106	App\\Models\\Usuarios	1	api_token	2cc4597d738133d3a3417957d7c9d90da4ed8246d64ceb4ef0109202cfb919eb	["*"]	\N	\N	2026-07-13 18:58:27	2026-07-13 18:58:27
107	App\\Models\\Usuarios	1	api_token	2cdab74ea66adfe4df0c58bc1a5270cd18ab30d1700e1eb551619606a7705394	["*"]	2026-07-13 21:20:05	\N	2026-07-13 21:20:04	2026-07-13 21:20:05
108	App\\Models\\Usuarios	5	api_token	55089a68eda417392a9919f2ed7b9ab093c1efe88e27870c4c5e96d99f1d632e	["*"]	2026-07-13 21:35:16	\N	2026-07-13 21:20:49	2026-07-13 21:35:16
109	App\\Models\\Usuarios	1	api_token	127cfabb99d76c9079f221b4e1e34663cc5ef14f75e7f9b7c220783cb50cd8ff	["*"]	\N	\N	2026-07-13 21:28:39	2026-07-13 21:28:39
110	App\\Models\\Usuarios	5	api_token	97ba0682d2006a62e9617452a57ceedb46ef060f70f8bca77ee89aa3f93c1fa3	["*"]	2026-07-14 22:22:38	\N	2026-07-14 18:38:00	2026-07-14 22:22:38
111	App\\Models\\Usuarios	5	api_token	b7df89ddcdf2d9ebc8b8fe404eec9c82d683526484c88601978e4bca2526e351	["*"]	2026-07-14 18:38:00	\N	2026-07-14 18:38:00	2026-07-14 18:38:00
112	App\\Models\\Usuarios	7	api_token	7c1bf964a73c4986548d31a367c9a7c3ade6f2ca84062cc4828f8fc46f90ff00	["*"]	2026-07-14 22:08:02	\N	2026-07-14 22:07:58	2026-07-14 22:08:02
113	App\\Models\\Usuarios	7	api_token	cdb25236a457fe1d43c75eede4e0de7b9f04d2ac0d86ceb5f4ad35cc97f1c345	["*"]	2026-07-15 18:37:06	\N	2026-07-14 22:23:16	2026-07-15 18:37:06
114	App\\Models\\Usuarios	7	api_token	d640394478ec0e54b29c80fe080c6bb76e1bb92f40f2beaf9d3f2b3ca8649a8d	["*"]	2026-07-14 22:37:15	\N	2026-07-14 22:37:15	2026-07-14 22:37:15
115	App\\Models\\Usuarios	7	api_token	bfa01b9e892be82de46a36e56929aa2c0923f176328ce9221e3719c847158b25	["*"]	2026-07-14 22:37:43	\N	2026-07-14 22:37:43	2026-07-14 22:37:43
116	App\\Models\\Usuarios	7	api_token	f46cfdb8d14c0b0d3ca11cb8d1fe8807497759aadeccb86c1b4527dc184a66fc	["*"]	2026-07-14 22:38:05	\N	2026-07-14 22:38:05	2026-07-14 22:38:05
117	App\\Models\\Usuarios	28	api_token	45442a2a10e9c464ae5537e33af20d18f3c62c1d7d20a178066c973c81894e85	["*"]	2026-07-14 22:38:05	\N	2026-07-14 22:38:05	2026-07-14 22:38:05
118	App\\Models\\Usuarios	28	api_token	728a1995042b200b27cf600cd7b43149e0d18a1777f37c8f177adac2c28229b4	["*"]	2026-07-14 22:38:18	\N	2026-07-14 22:38:18	2026-07-14 22:38:18
119	App\\Models\\Usuarios	7	api_token	7c272a6cb5595c04e27a2ce4324e545f53d61c896679edc617384e99b866fabe	["*"]	2026-07-14 22:38:28	\N	2026-07-14 22:38:28	2026-07-14 22:38:28
120	App\\Models\\Usuarios	7	api_token	ad0a3a67b6f166c178b0683369fbdd764a24647b1109bca460442fddda25e0c5	["*"]	2026-07-14 22:41:37	\N	2026-07-14 22:41:37	2026-07-14 22:41:37
121	App\\Models\\Usuarios	7	api_token	102aa39f12f855d0688ff906c86dd22455b28a99d93a91eea0ac59e3ce44daed	["*"]	2026-07-14 22:51:27	\N	2026-07-14 22:51:26	2026-07-14 22:51:27
122	App\\Models\\Usuarios	7	api_token	bf21f92d39ee9ece644d1fe4f1710d74267b96381266f66793a1aee272a63908	["*"]	2026-07-14 23:04:29	\N	2026-07-14 23:04:23	2026-07-14 23:04:29
123	App\\Models\\Usuarios	32	api_token	9ef3e3fa269f0674f9601e017cc087832ea3b9dbdaddab49129b34987e691d06	["*"]	\N	\N	2026-07-15 20:15:04	2026-07-15 20:15:04
124	App\\Models\\Usuarios	5	api_token	33571776096e707fe6b5c61312e165ff1d63fad041231e50d533838ae3419638	["*"]	2026-07-16 15:30:49	\N	2026-07-15 20:21:55	2026-07-16 15:30:49
125	App\\Models\\Usuarios	33	api_token	dc8973ae017f1201bf5a5c41eed12cf855832c91a63cc815e5227397ee8bb227	["*"]	2026-07-15 20:43:35	\N	2026-07-15 20:42:07	2026-07-15 20:43:35
126	App\\Models\\Usuarios	7	api_token	774092a6f0eff4443470c0cafa6aa5fce96278066e50a78ebe11dc14310d7200	["*"]	2026-07-16 21:53:52	\N	2026-07-16 15:38:32	2026-07-16 21:53:52
128	App\\Models\\Usuarios	7	api_token	9aacc84b0dea9f35ac2aac38c8270e0371fc30d04c818f1ab4966b302e0be4f6	["*"]	2026-07-21 22:17:42	\N	2026-07-16 21:54:20	2026-07-21 22:17:42
134	App\\Models\\Usuarios	35	api_token	a88599d3a01b9195fa788f31c845bf013d68327c645d74b640001ec92044b559	["*"]	2026-07-20 22:18:05	\N	2026-07-20 22:17:36	2026-07-20 22:18:05
135	App\\Models\\Usuarios	36	api_token	f5d62d8880ec849b102c1e61fd063cb1e24c02e608296c8375046eec8c6fdcf9	["*"]	2026-07-20 22:54:54	\N	2026-07-20 22:54:37	2026-07-20 22:54:54
136	App\\Models\\Usuarios	37	api_token	5920ae06aa4519ff34afbb622e2c012585b0ba62fd5ea6dca0f51940f81aade3	["*"]	2026-07-20 22:54:54	\N	2026-07-20 22:54:37	2026-07-20 22:54:54
137	App\\Models\\Usuarios	38	api_token	150505337f32c2254250502d83c894ce6347a86f5c2bb4441621470a2e60b0e4	["*"]	2026-07-20 23:07:47	\N	2026-07-20 23:07:28	2026-07-20 23:07:47
138	App\\Models\\Usuarios	39	api_token	df8ec058110531bcaae833f6b20a43bfc4548f25dba0440c418a280a313d014b	["*"]	\N	\N	2026-07-21 16:46:36	2026-07-21 16:46:36
139	App\\Models\\Usuarios	39	api_token	0befecbf7e2d575385f1a9a45ab164666a899e5cee2833d1e025c8f1f42e9994	["*"]	2026-07-21 16:53:33	\N	2026-07-21 16:53:33	2026-07-21 16:53:33
140	App\\Models\\Usuarios	39	api_token	bd300c9bd013fa7ec8dd7fbaff646f17a4e00662e0f1a4c73091c6c71579963a	["*"]	2026-07-21 16:53:45	\N	2026-07-21 16:53:44	2026-07-21 16:53:45
141	App\\Models\\Usuarios	39	api_token	64f3a6a4e4b80d11587240fe22ab6c446f485e71b844553e42c38bd747559c5d	["*"]	2026-07-21 16:53:53	\N	2026-07-21 16:53:53	2026-07-21 16:53:53
142	App\\Models\\Usuarios	39	api_token	cabe95c8d8ba81473bd0deb983a97d2b1e1a053e25ac8981752396ce9898e250	["*"]	2026-07-21 16:54:01	\N	2026-07-21 16:54:01	2026-07-21 16:54:01
143	App\\Models\\Usuarios	39	api_token	86966f017bb14bd5aa090cc41b94ea9d8c6222597dcf559092cedadac1f11edd	["*"]	2026-07-21 16:54:09	\N	2026-07-21 16:54:09	2026-07-21 16:54:09
144	App\\Models\\Usuarios	39	api_token	784a03b0d7aec598bfa4c2671ed8b18707939af6a36eafa0df6582b0c971a7a5	["*"]	2026-07-21 16:54:26	\N	2026-07-21 16:54:26	2026-07-21 16:54:26
145	App\\Models\\Usuarios	39	api_token	c46b2f8c708d9629e3f08d82fca883c05802dcfd740095bc3f3814474498d608	["*"]	2026-07-21 16:54:35	\N	2026-07-21 16:54:35	2026-07-21 16:54:35
146	App\\Models\\Usuarios	40	api_token	0d18f08addc980bb61b3dbaf184c39c15019c4558ed39f34fcd38c48631e80e4	["*"]	\N	\N	2026-07-21 17:36:31	2026-07-21 17:36:31
147	App\\Models\\Usuarios	41	api_token	c3e4fb861da57017c87fdd190b372165a0b5f53b5e07df4d4c5427eb0677ed67	["*"]	\N	\N	2026-07-21 17:36:31	2026-07-21 17:36:31
148	App\\Models\\Usuarios	40	api_token	b4641d003a8d5a3668fe9a47289f37a1150d809956028c5f2165ca21a63840cc	["*"]	2026-07-21 17:36:50	\N	2026-07-21 17:36:50	2026-07-21 17:36:50
149	App\\Models\\Usuarios	40	api_token	502f7ca726820b0d4ffe569c80d3383b57e54d4dad064e94cca5593cc263b5d3	["*"]	2026-07-21 17:37:03	\N	2026-07-21 17:37:03	2026-07-21 17:37:03
150	App\\Models\\Usuarios	40	api_token	ae24699a8473973fbf7015dbca7ec0d70c1d8e1d9628423223a02eec595fcb7b	["*"]	2026-07-21 17:37:15	\N	2026-07-21 17:37:15	2026-07-21 17:37:15
151	App\\Models\\Usuarios	41	api_token	bbd841f9d0db84dfb667cc9c244bf7078ccd847d61ac3ae59735a1d805156a0b	["*"]	2026-07-21 17:37:27	\N	2026-07-21 17:37:26	2026-07-21 17:37:27
152	App\\Models\\Usuarios	41	api_token	05caf015ba9450e82eb06554ab8cbdda8deef7756534903ae2830800a3495b21	["*"]	2026-07-21 17:37:39	\N	2026-07-21 17:37:39	2026-07-21 17:37:39
153	App\\Models\\Usuarios	42	api_token	84a591a4ebc194af17c758a3c2ec332f224572739b630bc756dad8ace871f20e	["*"]	\N	\N	2026-07-21 18:47:40	2026-07-21 18:47:40
154	App\\Models\\Usuarios	42	api_token	8f91d34a48ba7a7d70b6e0a9bb60fee85f551609844603cfd966bfd2eff99975	["*"]	2026-07-21 18:47:54	\N	2026-07-21 18:47:54	2026-07-21 18:47:54
155	App\\Models\\Usuarios	39	api_token	7b7ddfd3bf887b79c0c046e25f70f858ba87e377e92c4285b0c1bde5516320ef	["*"]	2026-07-21 21:51:20	\N	2026-07-21 21:51:19	2026-07-21 21:51:20
156	App\\Models\\Usuarios	39	api_token	461e8136a7f6698cd2b588edb6df1e28b5bf341e6667f9b0f950f029ec7621fd	["*"]	2026-07-21 21:53:00	\N	2026-07-21 21:52:57	2026-07-21 21:53:00
157	App\\Models\\Usuarios	39	api_token	1d198c1d9b1ea65f038186c2aaaecaf2e7d286e5dc5b495ea0072e0a54f102c3	["*"]	2026-07-21 21:53:57	\N	2026-07-21 21:53:34	2026-07-21 21:53:57
158	App\\Models\\Usuarios	39	api_token	96edf3d51e347991a7acf472085d75fd2d9defbfbd03178f207462a8b2c76a41	["*"]	2026-07-21 21:56:01	\N	2026-07-21 21:55:31	2026-07-21 21:56:01
159	App\\Models\\Usuarios	39	api_token	97061f675eeffc338b680e33475fabfc75d7be95bf5992484258f7a08ed6aeee	["*"]	2026-07-21 21:56:35	\N	2026-07-21 21:56:32	2026-07-21 21:56:35
160	App\\Models\\Usuarios	39	api_token	f226bc2095ee4e72909d6a58429deed4597470dd215c3cbcbd2781787c89c83a	["*"]	2026-07-21 21:57:53	\N	2026-07-21 21:57:50	2026-07-21 21:57:53
161	App\\Models\\Usuarios	39	api_token	d74966c401dfe69e88b034c9df1760c1a29f2eae2f648edf01ffc306b4ad848a	["*"]	2026-07-21 21:59:00	\N	2026-07-21 21:58:54	2026-07-21 21:59:00
162	App\\Models\\Usuarios	39	api_token	2266a50ac06e119ac758a600055b52e4b5afb8e61c841c0f385f6e06a3249f2d	["*"]	2026-07-21 21:59:44	\N	2026-07-21 21:59:42	2026-07-21 21:59:44
163	App\\Models\\Usuarios	39	api_token	bf030739a4192250df1fb737f626ed194211eeda5b9aad7d938634ead48ac1b4	["*"]	2026-07-21 22:01:03	\N	2026-07-21 22:00:40	2026-07-21 22:01:03
164	App\\Models\\Usuarios	39	api_token	c190839af154a283a429887fa1952bade6641e9155d1d71fca7d3d659d790200	["*"]	2026-07-21 22:02:03	\N	2026-07-21 22:02:00	2026-07-21 22:02:03
165	App\\Models\\Usuarios	39	api_token	bb57d9c83e8ef42a7adc02ef0d10d541d95500cddc13e4509241d18d48477d9b	["*"]	2026-07-21 22:03:25	\N	2026-07-21 22:03:21	2026-07-21 22:03:25
166	App\\Models\\Usuarios	39	api_token	c7da0570cb346e2f28c3a436d2777f8289e1e73ed5287d05c43d7e60ca474d84	["*"]	2026-07-21 22:04:37	\N	2026-07-21 22:04:34	2026-07-21 22:04:37
167	App\\Models\\Usuarios	39	api_token	807dd60bf931bc8e1afed2da24b611c091cda2e96590e5aefb23fa430da71caf	["*"]	2026-07-21 22:07:39	\N	2026-07-21 22:07:36	2026-07-21 22:07:39
168	App\\Models\\Usuarios	39	api_token	b946771b23ffa9d99a189b4bdde63c9a893c10c9ef0db1faab32daabb888c586	["*"]	2026-07-21 22:09:29	\N	2026-07-21 22:09:26	2026-07-21 22:09:29
169	App\\Models\\Usuarios	39	api_token	408bdd659392461dc0780faf98da6f255fde958791caa88ea3ed674db4bdc2c0	["*"]	2026-07-21 22:14:33	\N	2026-07-21 22:14:30	2026-07-21 22:14:33
170	App\\Models\\Usuarios	39	api_token	f27cf0d19c0d152968e89e2780bdee4a6b9102cf9cb9174d51c0ab49cbc649a5	["*"]	2026-07-21 22:15:02	\N	2026-07-21 22:14:56	2026-07-21 22:15:02
171	App\\Models\\Usuarios	39	api_token	df296e909dadaa4a516a412c3627b261c4324dfd7e9172a87af94de578ceac6f	["*"]	2026-07-21 22:15:37	\N	2026-07-21 22:15:28	2026-07-21 22:15:37
172	App\\Models\\Usuarios	40	api_token	a30ff63f275f132c15a5bee9d3b89e56d362d7a43736dc6d53794b9afb80c0c5	["*"]	2026-07-21 22:16:11	\N	2026-07-21 22:16:09	2026-07-21 22:16:11
173	App\\Models\\Usuarios	41	api_token	a7415ef351b0f20bad3408d65d2883585276648e66da61481541e669c83b5533	["*"]	2026-07-21 22:16:20	\N	2026-07-21 22:16:17	2026-07-21 22:16:20
174	App\\Models\\Usuarios	39	api_token	1cd45a956aed0d5c67c9af5c5344e4f1e2ddfbaff6b4fe5a8efaaa0e380414cd	["*"]	2026-07-21 22:21:34	\N	2026-07-21 22:17:55	2026-07-21 22:21:34
175	App\\Models\\Usuarios	40	api_token	fbfc9eac264b526051400445ded7e89dc52cdf54f226ac715e50b5abe9509bc5	["*"]	2026-07-21 22:34:04	\N	2026-07-21 22:21:55	2026-07-21 22:34:04
176	App\\Models\\Usuarios	40	api_token	ddfd4c1debeef5d99b0041c431893303a1215aa91dd676a3a3bd5375d80f1c76	["*"]	2026-07-21 22:24:43	\N	2026-07-21 22:24:35	2026-07-21 22:24:43
178	App\\Models\\Usuarios	39	api_token	4d2e669be34019b61f61c982f77ab536455e85e932bcfc52ca39fd8856afc092	["*"]	2026-07-21 22:39:55	\N	2026-07-21 22:39:46	2026-07-21 22:39:55
179	App\\Models\\Usuarios	39	api_token	b44999f2cfdae0a8e6e90c552feee3e50a8ca2cd7f6b2f5487a25a11aff48fbe	["*"]	2026-07-21 22:40:34	\N	2026-07-21 22:40:27	2026-07-21 22:40:34
180	App\\Models\\Usuarios	39	api_token	b477b81d2146810ecbeb4004452c52f9f468dd1cc7c407efcbfa8982bd6f67e9	["*"]	2026-07-22 14:44:49	\N	2026-07-22 14:44:48	2026-07-22 14:44:49
181	App\\Models\\Usuarios	39	api_token	e9aee5e779535e76daf72bd4a716c215cced2e007d3242171eebb7d4bd34e448	["*"]	2026-07-22 14:45:57	\N	2026-07-22 14:45:32	2026-07-22 14:45:57
182	App\\Models\\Usuarios	39	api_token	31f1a2d6ac08f351512cc549450b4618124cbb3cf93e80da48075851e97cdf72	["*"]	2026-07-22 14:46:54	\N	2026-07-22 14:46:52	2026-07-22 14:46:54
183	App\\Models\\Usuarios	39	api_token	bb5b357e41767044e2fe76270c4b5183356138da90b415a537efda88f6159559	["*"]	2026-07-22 14:47:31	\N	2026-07-22 14:47:25	2026-07-22 14:47:31
184	App\\Models\\Usuarios	39	api_token	2bbd2fa88e9b9063a990fca51e519071a9e8e26f8b03edaf0f2f9e1845188294	["*"]	2026-07-22 14:48:15	\N	2026-07-22 14:48:02	2026-07-22 14:48:15
185	App\\Models\\Usuarios	39	api_token	d5dec5f55ec51a3dfe3bce005741522eb4e9b9c1b99b41167c098e2075af070e	["*"]	2026-07-22 14:49:10	\N	2026-07-22 14:48:59	2026-07-22 14:49:10
186	App\\Models\\Usuarios	39	api_token	599272003e50328a69f840489c12176eb8a186f5e1a82bc861f7f8bb221d7d0c	["*"]	\N	\N	2026-07-22 18:12:14	2026-07-22 18:12:14
195	App\\Models\\Usuarios	9	api_token	63136d4540f18f447d3749ddd88954c28e27895052eb09ee5644426766ecc9f7	["*"]	2026-07-23 20:53:37	\N	2026-07-23 20:53:35	2026-07-23 20:53:37
254	App\\Models\\Usuarios	39	api_token	95ee9830df8e0e1f6607928be2f01e530b7ef3a9f6e93123b18d28d09ef215b5	["*"]	2026-07-28 23:15:12	\N	2026-07-28 23:13:20	2026-07-28 23:15:12
202	App\\Models\\Usuarios	45	api_token	9b3e7270bcb447981e5d0f21da4275e82bef1aa342302892dda017c5066e1169	["*"]	\N	\N	2026-07-23 22:25:03	2026-07-23 22:25:03
201	App\\Models\\Usuarios	39	api_token	0aaed2280ebd93be463244ff2dcd7a80cb113fc7be0c2b38c4e710ee3fa98558	["*"]	2026-07-23 21:57:01	\N	2026-07-23 21:56:58	2026-07-23 21:57:01
187	App\\Models\\Usuarios	39	api_token	a500ff3eeabe861dc5016cef374f6fa2221ad832ffb3415357bb078d9d0a7af7	["*"]	2026-07-22 18:12:49	\N	2026-07-22 18:12:37	2026-07-22 18:12:49
197	App\\Models\\Usuarios	39	api_token	dccc5762b5d93f5cc184c974345b60dff8ef569a47b1de969e00049dc77b53d6	["*"]	2026-07-23 21:30:29	\N	2026-07-23 21:30:22	2026-07-23 21:30:29
177	App\\Models\\Usuarios	41	api_token	0cec5872f334ff960bfe655bd65f4ee9cb1d1be48cfd25083ae0ac41560c7e73	["*"]	2026-07-22 22:32:59	\N	2026-07-21 22:34:19	2026-07-22 22:32:59
188	App\\Models\\Usuarios	43	api_token	942ab01c436e9cd57fd3ec2ef4e4a66d24d6fa189997c1b5dbd75f65ef71f41f	["*"]	2026-07-22 22:52:52	\N	2026-07-22 22:52:51	2026-07-22 22:52:52
189	App\\Models\\Usuarios	44	api_token	579b1de8fb52d688f1a7790e03b775c543bfec915455a8cef686fa388267d76f	["*"]	2026-07-22 23:00:40	\N	2026-07-22 23:00:40	2026-07-22 23:00:40
217	App\\Models\\Usuarios	43	debug	2171251f7f36181cc3e3a2e6468aac0460ea9a87934ccd8a5687389b130b9b31	["*"]	2026-07-27 22:27:46	\N	2026-07-27 22:27:45	2026-07-27 22:27:46
253	App\\Models\\Usuarios	7	api_token	9d2b0a644b541ad5d125beb3f1793a8d3751d88fe387a7e83b870bfe2e9a88be	["*"]	2026-07-28 23:09:11	\N	2026-07-28 23:08:10	2026-07-28 23:09:11
219	App\\Models\\Usuarios	49	api_token	640b1aa2dc9163cba0e54b93adf40b8c449719e3c0fe25f38cdbc9428018ad4a	["*"]	2026-07-27 23:01:06	\N	2026-07-27 23:01:03	2026-07-27 23:01:06
191	App\\Models\\Usuarios	39	api_token	e003e8cba774b55b1d6ce0c436e15bf8c3a83997218871fb86a53cb1a6fcf510	["*"]	2026-07-23 18:51:02	\N	2026-07-23 18:50:58	2026-07-23 18:51:02
203	App\\Models\\Usuarios	45	api_token	c76d9743d227ed1a215de338c578bc7419b67f011d0ea7809dc762c4f9890194	["*"]	2026-07-23 22:25:53	\N	2026-07-23 22:25:12	2026-07-23 22:25:53
198	App\\Models\\Usuarios	39	api_token	5c8bdbcb244da82805b6537d0270eb47191617f7fb5c4736258e46d06d09a906	["*"]	2026-07-23 21:31:06	\N	2026-07-23 21:31:04	2026-07-23 21:31:06
206	App\\Models\\Usuarios	46	api_token	0e230580efdd69bd1e7fa4eea98adedfb760a897633bb274f9170ffb9757f671	["*"]	\N	\N	2026-07-23 22:35:44	2026-07-23 22:35:44
192	App\\Models\\Usuarios	40	api_token	2653127aebbcb76ea39b0aa1ad194e998009a8f702f0064b04c65890c40ffb20	["*"]	2026-07-23 18:51:12	\N	2026-07-23 18:51:07	2026-07-23 18:51:12
214	App\\Models\\Usuarios	43	api_token	acd76432b212801f9d823ae5df3da15e226fa128fd8a1d633e5644d53cbeff93	["*"]	2026-07-27 22:05:20	\N	2026-07-27 19:23:16	2026-07-27 22:05:20
196	App\\Models\\Usuarios	39	api_token	3caa78f02bb28831b573955dc9a31ec130c0f905939d70e724bf40252e01b412	["*"]	2026-07-23 22:15:16	\N	2026-07-23 21:22:37	2026-07-23 22:15:16
193	App\\Models\\Usuarios	41	api_token	72a030884915ecea6ddddd1f1689cc3ef8886f790b17653d820bc1e79ad95dc7	["*"]	2026-07-23 18:51:21	\N	2026-07-23 18:51:16	2026-07-23 18:51:21
190	App\\Models\\Usuarios	43	api_token	2e9b99f641a24ab19123add62e04a6f7da9c9c8f4ad7febd5ecda05788343011	["*"]	2026-07-24 17:10:35	\N	2026-07-23 15:39:17	2026-07-24 17:10:35
204	App\\Models\\Usuarios	45	api_token	94265db9319bd290a8f966b39cb717a880dc8fdba74090f8b788fb235c246261	["*"]	2026-07-23 22:26:46	\N	2026-07-23 22:26:35	2026-07-23 22:26:46
194	App\\Models\\Usuarios	40	api_token	dad6c29b3b9518975c15d06106b384d5f23bc62c3723098e08c1500c459a6ce9	["*"]	2026-07-23 18:58:27	\N	2026-07-23 18:58:26	2026-07-23 18:58:27
199	App\\Models\\Usuarios	39	api_token	501d3fc4a27063df13325e35e6d664bc3adccb81d3db2f458e4081d631305f05	["*"]	2026-07-23 21:32:31	\N	2026-07-23 21:32:22	2026-07-23 21:32:31
207	App\\Models\\Usuarios	47	api_token	9caaa62358241868934eba671a7ea17cc776dba8358c152aafd613ac4cbf6303	["*"]	2026-07-23 22:37:55	\N	2026-07-23 22:37:09	2026-07-23 22:37:55
208	App\\Models\\Usuarios	47	api_token	0ccabd06309140e011b3189002f89b546fe49af5303a1bf635019b0c845c2b58	["*"]	2026-07-23 22:38:44	\N	2026-07-23 22:38:44	2026-07-23 22:38:44
200	App\\Models\\Usuarios	39	api_token	c95315283d989659426b55db7221c93656cfad191eb4c9d09c31aa849c6e15c5	["*"]	2026-07-23 21:46:10	\N	2026-07-23 21:45:29	2026-07-23 21:46:10
205	App\\Models\\Usuarios	45	api_token	1485ef81bb42194ef16398e574575c57f79ff41ee9d91234f9acfa0f937c4a40	["*"]	2026-07-23 22:27:57	\N	2026-07-23 22:27:32	2026-07-23 22:27:57
209	App\\Models\\Usuarios	47	api_token	650fbe3ccc24876f81715cb66157c78bd67dda86ec43b2c56ff01a5442becd1d	["*"]	2026-07-23 22:39:15	\N	2026-07-23 22:39:15	2026-07-23 22:39:15
213	App\\Models\\Usuarios	7	api_token	1575a622d6e8c999d200f7809a0ab6d08509f2a8ae48c3be8d4f09070e39127d	["*"]	2026-07-27 19:22:47	\N	2026-07-24 20:17:43	2026-07-27 19:22:47
210	App\\Models\\Usuarios	43	api_token	8b5cd4659073bc357f1e31b539d45ddd47840dd28c1abfd61cbed2f75cf1863b	["*"]	2026-07-24 17:12:18	\N	2026-07-24 17:11:13	2026-07-24 17:12:18
211	App\\Models\\Usuarios	43	api_token	e9ed303e37e0c72f0e704d7904ed51a64a7ccf39b58f8f1475c99eb32b4da463	["*"]	2026-07-24 17:16:07	\N	2026-07-24 17:16:06	2026-07-24 17:16:07
212	App\\Models\\Usuarios	43	api_token	8212710fde946b0cadf07a57a72faab80e95da67c37ed64544c7ecda72df76dd	["*"]	2026-07-24 17:21:14	\N	2026-07-24 17:21:14	2026-07-24 17:21:14
215	App\\Models\\Usuarios	43	debug	2e1b13874ec5268029971e8208b9f7b560968f7d37f1d3888afac3ea3cd84e0d	["*"]	2026-07-27 21:44:33	\N	2026-07-27 21:44:32	2026-07-27 21:44:33
252	App\\Models\\Usuarios	7	api_token	cb9ead6582991919718b843ab06dd98828e51680316c549d239f4ba262ef8ac5	["*"]	\N	\N	2026-07-28 23:08:10	2026-07-28 23:08:10
216	App\\Models\\Usuarios	43	api_token	ea22a32f48118f44388216241e59839bb91a54136b14636ca1757042774de274	["*"]	2026-07-27 22:55:08	\N	2026-07-27 22:06:10	2026-07-27 22:55:08
218	App\\Models\\Usuarios	49	api_token	8d87db58b2e6b1d90473b07b250b58d59ef38ba61731d7fcf24166ff95938736	["*"]	\N	\N	2026-07-27 22:56:42	2026-07-27 22:56:42
255	App\\Models\\Usuarios	50	api_token	b4c81b394022d69523aa4636769489b08c7653233723061df21758e1213d097f	["*"]	2026-07-29 15:46:33	\N	2026-07-28 23:15:20	2026-07-29 15:46:33
256	App\\Models\\Usuarios	7	api_token	375256cbf23fc35af965b15b81aab7c473c7d22990dc22811674f85145ae3c73	["*"]	2026-07-31 17:02:39	\N	2026-07-31 17:00:32	2026-07-31 17:02:39
257	App\\Models\\Usuarios	7	api_token	ad3dee67bc814b8459f61de54da81d8561ec97ed26e8d4e9033c6998bbf49726	["*"]	2026-07-31 17:15:04	\N	2026-07-31 17:14:54	2026-07-31 17:15:04
258	App\\Models\\Usuarios	7	api_token	ab59af3f41c5a962926e110f4a4a48323350a01ca9a8c4e5d5284dcbd68f5224	["*"]	2026-07-31 17:16:00	\N	2026-07-31 17:15:58	2026-07-31 17:16:00
290	App\\Models\\Usuarios	7	api_token	e09fa4b647d9238a60c4bb858122878fd5c2475b7013791431199c93025536c1	["*"]	2026-08-06 14:27:03	\N	2026-08-06 21:27:02	2026-08-06 21:27:02
259	App\\Models\\Usuarios	7	api_token	f3379d82e8d13221d9b5210a49a5eb8e8d577a604a63a59ac31d15daeb8cec11	["*"]	2026-07-31 17:16:32	\N	2026-07-31 17:16:30	2026-07-31 17:16:32
260	App\\Models\\Usuarios	7	api_token	f98db3534455c2ba9b5b9f23a25510fb71665f068c9ace0642d15679a6ca6836	["*"]	2026-07-31 18:09:45	\N	2026-07-31 18:09:45	2026-07-31 18:09:45
261	App\\Models\\Usuarios	7	api_token	0e45079513621472347c0e5b652cd9617afe8ada5007070b654bf67d2d0c4317	["*"]	2026-07-31 18:32:20	\N	2026-07-31 18:32:19	2026-07-31 18:32:20
265	App\\Models\\Usuarios	7	api_token	981db2ebaa56b3eee20acbad2454df033bdfc6442b67051e07b369f51aa76ad1	["*"]	2026-08-06 20:37:46	\N	2026-08-06 20:37:37	2026-08-06 20:37:46
99	App\\Models\\Usuarios	7	api_token	06210af78968071eee8e7ec7c41a8a69755f39b99f664df1b4af496ed298f8fd	["*"]	2026-07-31 18:45:18	\N	2026-07-09 16:56:35	2026-07-31 18:45:18
274	App\\Models\\Usuarios	2	test-inactividad	ad29f51dc303b3aec044dd354b41170c0161a6fdf12298bbefa2cca470b5ea70	["*"]	2026-08-06 19:16:16	\N	2026-08-06 21:16:04	2026-08-06 21:16:16
285	App\\Models\\Usuarios	7	debug-test4	6035e811638a17d3cf065887bae8228d3010fd1b69e8abaef61d4245ce6d4b13	["*"]	2026-08-06 21:23:43	\N	2026-08-06 21:23:43	2026-08-06 21:23:43
281	App\\Models\\Usuarios	7	api_token	27d477e47dba9c72d30089bb385c771441c253e7b19aa64b659b3ced247351bc	["*"]	2026-08-06 21:21:23	\N	2026-08-06 21:21:22	2026-08-06 21:21:23
275	App\\Models\\Usuarios	7	api_token	085b3dd886dba883d7898040461662cbc2fd2799ba51f037474949f76149b9cd	["*"]	2026-08-06 21:17:07	\N	2026-08-06 21:16:51	2026-08-06 21:17:07
266	App\\Models\\Usuarios	7	api_token	17abfdbf5b9668f8a09c3cf144e8023b3d7a7ffb6033f21e5b2664d9a875de87	["*"]	2026-08-06 20:38:59	\N	2026-08-06 20:38:36	2026-08-06 20:38:59
288	App\\Models\\Usuarios	7	debug-test7	8a28db16afee6a1f2ed4f9064d2829fcd7c018d7f38b69364969526af796fb3c	["*"]	2026-08-06 21:25:19	\N	2026-08-06 21:25:19	2026-08-06 21:25:19
282	App\\Models\\Usuarios	7	debug-test	b89aee4afa85eab6bc2a9d1c4f17a848f808511b438c460618eed7da34f4ae7f	["*"]	2026-08-06 21:22:03	\N	2026-08-06 21:22:03	2026-08-06 21:22:03
276	App\\Models\\Usuarios	7	api_token	1c081c221108c2c1f58f9405ece1e9333c86a2a0f69802b706f9306620a97ecf	["*"]	2026-08-06 21:18:25	\N	2026-08-06 21:18:24	2026-08-06 21:18:25
277	App\\Models\\Usuarios	7	api_token	fb64c5c4480e34573df6b561f85db3782c780b5856efc62384363d1c9f28d906	["*"]	2026-08-06 19:18:50	\N	2026-08-06 21:18:49	2026-08-06 21:18:50
267	App\\Models\\Usuarios	7	api_token	298c1b6169ad541fc4728cae54ada7a34c1289da22295c9396f623beaf605210	["*"]	2026-08-06 20:40:01	\N	2026-08-06 20:39:43	2026-08-06 20:40:01
262	App\\Models\\Usuarios	51	api_token	f10fe54fbb43b2a9b30e03e71d24a0c2c2375049517e294457251120c4e128aa	["*"]	2026-08-06 18:58:38	\N	2026-07-31 18:46:38	2026-08-06 18:58:38
263	App\\Models\\Usuarios	51	api_token	bf90fb9331a2a470d36e6c849faddadbd5f4c5bb98e34dd0f4f2f1a9fb9462cf	["*"]	2026-08-06 18:58:52	\N	2026-08-06 18:58:50	2026-08-06 18:58:52
268	App\\Models\\Usuarios	7	api_token	7882fb7ef4528b1b214b53aef502e9d097f243de1e5fd7308ee6d67ae686ba5f	["*"]	2026-08-06 20:40:29	\N	2026-08-06 20:40:28	2026-08-06 20:40:29
269	App\\Models\\Usuarios	7	api_token	f7833b6e22c936a00059bf59ecb2a07b70b8c2c35247091f345e20280822860a	["*"]	2026-08-06 20:40:42	\N	2026-08-06 20:40:41	2026-08-06 20:40:42
264	App\\Models\\Usuarios	7	api_token	64abbbcbad1ec118b1b6382ad8b573caa3d52fdd94c5137e84d971f40fd29c1a	["*"]	2026-08-06 20:35:26	\N	2026-08-06 20:35:23	2026-08-06 20:35:26
270	App\\Models\\Usuarios	7	api_token	30ce82460f81ed0c2fcd48a8eb124f863f83f43766dcaed4378b05e3a8bda2fd	["*"]	2026-08-06 20:40:53	\N	2026-08-06 20:40:53	2026-08-06 20:40:53
272	App\\Models\\Usuarios	52	api_token	794b552283d48134ecfc3a3ea9a752d62d14630b65675f2009a4477ddfdb0546	["*"]	2026-08-06 20:56:03	\N	2026-08-06 20:55:39	2026-08-06 20:56:03
278	App\\Models\\Usuarios	7	api_token	687d7e56b9b42f94754cf7c6be0950dd72287ca1d0339e4f703213f316467a5f	["*"]	2026-08-06 21:19:21	\N	2026-08-06 21:19:21	2026-08-06 21:19:21
279	App\\Models\\Usuarios	7	api_token	78696e66e1ddfbf30ab0e65e14fb60dbc60294492faabc8b67e0b23946c39e3e	["*"]	\N	\N	2026-08-06 21:19:34	2026-08-06 21:19:34
273	App\\Models\\Usuarios	44	api_token	50a8fe357e10688da9140490cd7ad3539ca51ae8a2b5a3a226fcbe04f6b1f0cd	["*"]	2026-08-06 20:57:08	\N	2026-08-06 20:56:52	2026-08-06 20:57:08
280	App\\Models\\Usuarios	7	api_token	a4d0a9b68a820db8727a0bda44f218d6b4adc96a05d871ec55a86058cbbc7174	["*"]	2026-08-06 21:19:58	\N	2026-08-06 21:19:56	2026-08-06 21:19:58
283	App\\Models\\Usuarios	7	debug-test2	46dd06707f1bd6c571a34e7674ad4d741bf634476d6d395b9e19f05743d608e2	["*"]	2026-08-06 19:22:33	\N	2026-08-06 21:22:33	2026-08-06 21:22:33
286	App\\Models\\Usuarios	7	debug-test5	6deebd25c36543a6afd5465d8744109a73180eac27ac0bd3b240e23af1282513	["*"]	2026-08-06 21:24:12	\N	2026-08-06 21:24:12	2026-08-06 21:24:12
284	App\\Models\\Usuarios	7	debug-test3	330b17b2cefbcfa308c636433fe446ef14515b10321602c5c95c8f781c007517	["*"]	2026-08-06 21:23:27	\N	2026-08-06 21:23:27	2026-08-06 21:23:27
287	App\\Models\\Usuarios	7	debug-test6	14346f74f4a62f5528f5d3d30c95097fc292bb3857165d14f82cc22947ce1203	["*"]	2026-08-06 19:24:35	\N	2026-08-06 21:24:35	2026-08-06 21:24:35
289	App\\Models\\Usuarios	7	debug-test8	c6ae9f27b0266e5d2fb8c331ad072c740572078fbdda638b93a5a745ed6731f3	["*"]	2026-08-06 21:26:02	\N	2026-08-06 21:26:02	2026-08-06 21:26:02
293	App\\Models\\Usuarios	7	api_token	f73e8788d478bb364b53b4f0c2036111d44a995a685e853be4214d9602947185	["*"]	2026-08-06 21:30:52	\N	2026-08-06 21:30:52	2026-08-06 21:30:52
292	App\\Models\\Usuarios	7	api_token	a08b84b1ae78e1f2002816b57dc74a21275cceeaa7c709ba9226f2af885368c3	["*"]	2026-08-06 21:30:51	\N	2026-08-06 21:30:51	2026-08-06 21:30:51
294	App\\Models\\Usuarios	7	api_token	891077c6fe1d5368ea1dfc61e560af055860876d343bc99f18644957011bfbd8	["*"]	2026-08-06 21:30:53	\N	2026-08-06 21:30:53	2026-08-06 21:30:53
301	App\\Models\\Usuarios	7	api_token	9cf08a76b5d46c80c941217831aafd3896fef4fb81736926df487c855f6a9511	["*"]	2026-08-06 21:32:37	\N	2026-08-06 21:32:37	2026-08-06 21:32:37
295	App\\Models\\Usuarios	7	api_token	e99af58f9f42c932b39f4138359204932886404411b197fe6c91d787fc28a988	["*"]	2026-08-06 21:30:55	\N	2026-08-06 21:30:53	2026-08-06 21:30:55
297	App\\Models\\Usuarios	7	api_token	8f1a113ff82d87c27af28de78d5862c32393f4b0c3c5388210b06d74c0eb6e96	["*"]	2026-08-06 21:32:00	\N	2026-08-06 21:32:00	2026-08-06 21:32:00
300	App\\Models\\Usuarios	7	api_token	a14aa452904a47f5c7a9cb16bbadac37be29a342fde3e0c7d5942068ae529b20	["*"]	2026-08-06 21:32:03	\N	2026-08-06 21:32:02	2026-08-06 21:32:03
318	App\\Models\\Usuarios	43	debug-foto-producto2	99b904ecd29cec9b8c5d16b4beee0aba8d722d5eadb92bcd443c230c5fd76c92	["*"]	2026-08-06 22:30:30	\N	2026-08-06 22:29:55	2026-08-06 22:30:30
302	App\\Models\\Usuarios	7	api_token	a96076690859bc9e6ee7fb9741f221c398a38979ba83003ecea3862521e63096	["*"]	2026-08-06 21:32:54	\N	2026-08-06 21:32:54	2026-08-06 21:32:54
314	App\\Models\\Usuarios	43	debug-card-size	92df76da0b35704348d77e56bd5c3627ccfae2e1b2c964ccfabfe0496fda0bad	["*"]	2026-08-06 22:13:32	\N	2026-08-06 22:13:17	2026-08-06 22:13:32
313	App\\Models\\Usuarios	43	debug-fix-verify3	932f4e73d8c33ee12acb2fa08a213809b5ad84717b505330364ac07ca131c9f0	["*"]	2026-08-06 22:08:27	\N	2026-08-06 22:07:50	2026-08-06 22:08:27
304	App\\Models\\Usuarios	7	api_token	74168f0a07ac3d6061548fa2a0cf4f7d4430eb4933125dd494321eea9d30127c	["*"]	2026-08-06 21:33:46	\N	2026-08-06 21:33:42	2026-08-06 21:33:46
307	App\\Models\\Usuarios	7	api_token	eb78a3d606386d8700e708e0cc4b1753a4daa428b9937d52bde599dd47fde7bf	["*"]	2026-08-06 21:45:36	\N	2026-08-06 21:45:31	2026-08-06 21:45:36
308	App\\Models\\Usuarios	7	api_token	3183eb58f585408547891e2ce5c2f90af32ae0f3bf8de7cc40375406469d2f41	["*"]	2026-08-06 21:45:51	\N	2026-08-06 21:45:51	2026-08-06 21:45:51
324	App\\Models\\Usuarios	43	debug-eliminar-foto	1a464aa7aaff4e2c76d82c3be0cb3b9d246276afbec78f7ee62cf24af96b973c	["*"]	2026-08-06 22:35:28	\N	2026-08-06 22:35:28	2026-08-06 22:35:28
309	App\\Models\\Usuarios	7	api_token	4cdebe9852dfd161450c3d580b40e4910efa5e8b9a7816d221f78e973d726f6e	["*"]	2026-08-06 21:46:04	\N	2026-08-06 21:46:04	2026-08-06 21:46:04
310	App\\Models\\Usuarios	43	debug-pago-modal	7eefdc75bbb7fb8e419b2102b28f5b54089a77017d1227df235631bca390ef2d	["*"]	2026-08-06 21:51:19	\N	2026-08-06 21:49:22	2026-08-06 21:51:19
326	App\\Models\\Usuarios	43	debug-modal-center2	f18f5aa3a14ca088f7f124f1ec3da0a38fa54f3e4d2d09c4d2638ae7f2f4df5a	["*"]	2026-08-06 22:43:55	\N	2026-08-06 22:43:31	2026-08-06 22:43:55
311	App\\Models\\Usuarios	43	debug-fix-verify	dddb5d7d90d048e02121773e274118f57eb6a57188dfa24b4e3edcac287cdb42	["*"]	2026-08-06 22:05:03	\N	2026-08-06 22:03:38	2026-08-06 22:05:03
329	App\\Models\\Usuarios	43	api_token	51e2144f74b92fc6bdae7e6c1bc7edd304910d25e87ac4a5cb9410cb9d630d2b	["*"]	2026-08-07 16:08:09	\N	2026-08-07 13:59:01	2026-08-07 16:08:09
315	App\\Models\\Usuarios	43	debug-card-size2	ac6a64256f1dfd11d5ae075c38027e028d9ec9f348b81283a2dfbc719ef3dcab	["*"]	2026-08-06 22:13:59	\N	2026-08-06 22:13:43	2026-08-06 22:13:59
321	App\\Models\\Usuarios	43	debug-foto-final	23a83c9ab199747110a532e5238050bdc5b88b1bd96de7aa6fcf196c55083e29	["*"]	2026-08-06 22:33:46	\N	2026-08-06 22:33:23	2026-08-06 22:33:46
305	App\\Models\\Usuarios	7	api_token	180529a256cb4105879b0b3c211e5e62a08c56d33649dff40426c816de5e31ea	["*"]	2026-08-06 21:44:40	\N	2026-08-06 21:44:40	2026-08-06 21:44:40
312	App\\Models\\Usuarios	43	debug-fix-verify2	23733ce4f6ebb881c0170dc8c59c198003560c3e84d3e0582ade520c3e4fd514	["*"]	2026-08-06 22:05:59	\N	2026-08-06 22:05:17	2026-08-06 22:05:59
316	App\\Models\\Usuarios	43	debug-card-size3	548ad1ce802b4dc73daca1613c6d0466c6738171718733fc354768d5629b5237	["*"]	2026-08-06 22:14:34	\N	2026-08-06 22:14:16	2026-08-06 22:14:34
317	App\\Models\\Usuarios	43	debug-foto-producto	4a50d58c0c86df4471384fb185db7df9ab247cd2f1c754abcfb86a7a81e35c15	["*"]	2026-08-06 22:28:52	\N	2026-08-06 22:28:04	2026-08-06 22:28:52
319	App\\Models\\Usuarios	43	debug-foto-cat	d48ba27797ff2a68f8ff94ffd163d98e04006c8c961a9e9c93db45c376746de7	["*"]	2026-08-06 22:31:39	\N	2026-08-06 22:30:59	2026-08-06 22:31:39
306	App\\Models\\Usuarios	7	api_token	a2116dd96f177ee9af7d7b684a89be2d2add809e81e4a83130889a2dd6b08483	["*"]	2026-08-06 21:45:06	\N	2026-08-06 21:44:57	2026-08-06 21:45:06
322	App\\Models\\Usuarios	43	debug-verify-url	c0136be57f4090ab35f392b9cd2985e61425fcc3e6fdf54c4add0ab11de833ae	["*"]	2026-08-06 22:34:10	\N	2026-08-06 22:34:09	2026-08-06 22:34:10
320	App\\Models\\Usuarios	43	debug-foto-err	13cd5dda1e1d9ff3a64d1f6a21de020af1d7a95896be2f3e78d8df23d35d59a9	["*"]	2026-08-06 22:32:15	\N	2026-08-06 22:31:53	2026-08-06 22:32:15
327	App\\Models\\Usuarios	43	debug-modal-center3	f37c8acee3c335a306056058650db68a1c8cb835526216115676d72c44b072a2	["*"]	2026-08-06 22:44:47	\N	2026-08-06 22:44:26	2026-08-06 22:44:47
332	App\\Models\\Usuarios	7	api_token	63d0ca3f828c0a13b45a7962529cd0dec7d255d959f4d2592b952414eaa04361	["*"]	2026-08-07 15:15:02	\N	2026-08-07 15:14:56	2026-08-07 15:15:02
325	App\\Models\\Usuarios	43	debug-modal-center	4bbef9d7f1b6dc9c3fd4c8788f3c73a0e13690db81529a85c431460c1c2f8a8c	["*"]	2026-08-06 22:43:15	\N	2026-08-06 22:42:52	2026-08-06 22:43:15
323	App\\Models\\Usuarios	43	debug-cambiar-foto	02cd6b7298eb2db1edb0b360666046770c22d89bfa5b0a751d161355a080552e	["*"]	2026-08-06 22:35:07	\N	2026-08-06 22:34:38	2026-08-06 22:35:07
331	App\\Models\\Usuarios	7	api_token	0a0759787de58c5730d0598375f6461833b6903edbb20e39e54812fffdca0d86	["*"]	2026-08-07 15:14:08	\N	2026-08-07 15:13:44	2026-08-07 15:14:08
328	App\\Models\\Usuarios	43	debug-modal-fix	9d7f4624fc6d6d798c4f28d976015afcaa032f3989bac1cd97ae0e3dc42bae5f	["*"]	2026-08-06 22:47:48	\N	2026-08-06 22:47:22	2026-08-06 22:47:48
336	App\\Models\\Usuarios	7	api_token	d13a1ec2ce7b1e1ce795ea0f951e94ab0d7caaeb910d0cf462039982a361db38	["*"]	2026-08-07 16:47:14	\N	2026-08-07 16:47:12	2026-08-07 16:47:14
330	App\\Models\\Usuarios	7	api_token	6204011151d159f4a05f1310216abd7220eaaa63d26ca6204a2335335c626440	["*"]	2026-08-07 15:11:49	\N	2026-08-07 15:11:45	2026-08-07 15:11:49
339	App\\Models\\Usuarios	7	api_token	d1d252a526fbb5cd50d82af3df9d6a368879d630f0a27d58e277038a351f868c	["*"]	2026-08-07 16:48:58	\N	2026-08-07 16:48:56	2026-08-07 16:48:58
338	App\\Models\\Usuarios	7	api_token	9e8eda572d04c307eff55c8e0182044c63d7e44e05e09891ef89018015128321	["*"]	2026-08-07 16:48:32	\N	2026-08-07 16:48:30	2026-08-07 16:48:32
333	App\\Models\\Usuarios	7	api_token	0c24a2f128842963d2b2d64f935bc911335f6a60754ae7de30635c59d12b6f06	["*"]	2026-08-07 15:20:11	\N	2026-08-07 15:19:56	2026-08-07 15:20:11
334	App\\Models\\Usuarios	7	api_token	a6c6d1044cf29669e9f037559e39386df304a780cb155125e9186ff9935239ef	["*"]	2026-08-07 16:00:42	\N	2026-08-07 16:00:41	2026-08-07 16:00:42
337	App\\Models\\Usuarios	7	api_token	d20de29b4b4243e281ebac041b428a16f1dffb84b7a082ec2d387eaf5b1d5d0d	["*"]	2026-08-07 16:47:40	\N	2026-08-07 16:47:38	2026-08-07 16:47:40
340	App\\Models\\Usuarios	43	api_token	a8e45a29358e759e3c7ab3b6c7734e974cf421fcff97ce30cb0f1f763cada585	["*"]	2026-08-07 21:54:04	\N	2026-08-07 20:31:42	2026-08-07 21:54:04
341	App\\Models\\Usuarios	43	api_token	0438ad2bf582089296ef4183f33f526294e9cb95f17675f667a6a63ae7b59ec1	["*"]	2026-08-07 22:52:41	\N	2026-08-07 21:54:16	2026-08-07 22:52:41
\.


--
-- Data for Name: pipelines; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.pipelines (id_pipeline, id_tenant, nombre, activo, deleted_at, created_at, updated_at) FROM stdin;
1	2	Ventas	t	\N	2026-07-03 15:38:47	2026-07-03 15:38:47
2	2	Servicios	t	\N	2026-07-03 15:38:47	2026-07-03 15:38:47
3	4	Ventas	t	\N	2026-07-03 18:24:21	2026-07-03 18:24:21
4	4	Servicios	t	\N	2026-07-03 18:24:21	2026-07-03 18:24:21
5	5	Ventas	t	\N	2026-07-03 18:27:54	2026-07-03 18:27:54
6	5	Servicios	t	\N	2026-07-03 18:27:54	2026-07-03 18:27:54
7	6	Ventas	t	\N	2026-07-03 18:44:38	2026-07-03 18:44:38
8	6	Servicios	t	\N	2026-07-03 18:44:38	2026-07-03 18:44:38
9	7	Ventas	t	\N	2026-07-03 18:45:42	2026-07-03 18:45:42
10	7	Servicios	t	\N	2026-07-03 18:45:42	2026-07-03 18:45:42
11	8	Ventas	t	\N	2026-07-03 18:46:30	2026-07-03 18:46:30
12	8	Servicios	t	\N	2026-07-03 18:46:30	2026-07-03 18:46:30
13	9	Ventas	t	\N	2026-07-03 18:47:31	2026-07-03 18:47:31
14	9	Servicios	t	\N	2026-07-03 18:47:31	2026-07-03 18:47:31
15	10	Ventas	t	\N	2026-07-03 18:49:47	2026-07-03 18:49:47
16	10	Servicios	t	\N	2026-07-03 18:49:47	2026-07-03 18:49:47
17	11	Ventas	t	\N	2026-07-03 18:55:40	2026-07-03 18:55:40
18	11	Servicios	t	\N	2026-07-03 18:55:40	2026-07-03 18:55:40
19	12	Ventas	t	\N	2026-07-03 18:57:35	2026-07-03 18:57:35
20	12	Servicios	t	\N	2026-07-03 18:57:35	2026-07-03 18:57:35
21	13	Ventas	t	\N	2026-07-03 18:59:27	2026-07-03 18:59:27
22	13	Servicios	t	\N	2026-07-03 18:59:27	2026-07-03 18:59:27
23	14	Ventas	t	\N	2026-07-03 20:32:44	2026-07-03 20:32:44
24	14	Servicios	t	\N	2026-07-03 20:32:44	2026-07-03 20:32:44
25	15	Ventas	t	\N	2026-07-03 21:31:37	2026-07-03 21:31:37
26	15	Servicios	t	\N	2026-07-03 21:31:37	2026-07-03 21:31:37
27	16	Ventas	t	\N	2026-07-03 21:31:52	2026-07-03 21:31:52
28	16	Servicios	t	\N	2026-07-03 21:31:52	2026-07-03 21:31:52
29	17	Ventas	t	\N	2026-07-03 21:36:13	2026-07-03 21:36:13
30	17	Servicios	t	\N	2026-07-03 21:36:13	2026-07-03 21:36:13
31	18	Ventas	t	\N	2026-07-03 21:38:01	2026-07-03 21:38:01
32	18	Servicios	t	\N	2026-07-03 21:38:01	2026-07-03 21:38:01
33	19	Ventas	t	\N	2026-07-03 21:39:43	2026-07-03 21:39:43
34	19	Servicios	t	\N	2026-07-03 21:39:43	2026-07-03 21:39:43
35	20	Ventas	t	\N	2026-07-03 21:50:48	2026-07-03 21:50:48
36	20	Servicios	t	\N	2026-07-03 21:50:48	2026-07-03 21:50:48
37	20	Renovaciones	f	\N	2026-07-03 21:50:57	2026-07-03 21:51:01
38	1	sde	t	\N	2026-07-03 21:56:15	2026-07-03 21:56:15
39	21	Ventas	t	\N	2026-07-03 22:17:34	2026-07-03 22:17:34
40	21	Servicios	t	\N	2026-07-03 22:17:34	2026-07-03 22:17:34
41	22	Ventas	t	\N	2026-07-03 22:24:45	2026-07-03 22:24:45
42	22	Servicios	t	\N	2026-07-03 22:24:45	2026-07-03 22:24:45
43	23	Ventas	t	\N	2026-07-08 21:38:20	2026-07-08 21:38:20
44	23	Servicios	t	\N	2026-07-08 21:38:20	2026-07-08 21:38:20
45	24	Ventas	t	\N	2026-07-08 21:53:52	2026-07-08 21:53:52
46	24	Servicios	t	\N	2026-07-08 21:53:52	2026-07-08 21:53:52
49	26	Ventas	t	\N	2026-07-15 20:15:04	2026-07-15 20:15:04
50	26	Servicios	t	\N	2026-07-15 20:15:04	2026-07-15 20:15:04
51	31	Ventas	t	\N	2026-07-21 16:46:36	2026-07-21 16:46:36
52	31	Servicios	t	\N	2026-07-21 16:46:36	2026-07-21 16:46:36
53	32	Ventas	t	\N	2026-07-21 17:36:31	2026-07-21 17:36:31
54	32	Servicios	t	\N	2026-07-21 17:36:31	2026-07-21 17:36:31
55	33	Ventas	t	\N	2026-07-21 17:36:31	2026-07-21 17:36:31
56	33	Servicios	t	\N	2026-07-21 17:36:31	2026-07-21 17:36:31
57	34	Ventas	t	\N	2026-07-21 18:47:40	2026-07-21 18:47:40
58	34	Servicios	t	\N	2026-07-21 18:47:40	2026-07-21 18:47:40
60	35	Servicios	t	\N	2026-07-22 22:52:51	2026-07-22 22:52:51
61	36	Ventas	t	\N	2026-07-22 23:00:40	2026-07-22 23:00:40
62	36	Servicios	t	\N	2026-07-22 23:00:40	2026-07-22 23:00:40
63	37	Ventas	t	\N	2026-07-23 22:25:03	2026-07-23 22:25:03
64	37	Servicios	t	\N	2026-07-23 22:25:03	2026-07-23 22:25:03
65	38	Ventas	t	\N	2026-07-23 22:35:44	2026-07-23 22:35:44
66	38	Servicios	t	\N	2026-07-23 22:35:44	2026-07-23 22:35:44
67	39	Ventas	t	\N	2026-07-23 22:37:08	2026-07-23 22:37:08
68	39	Servicios	t	\N	2026-07-23 22:37:09	2026-07-23 22:37:09
69	40	Ventas	t	\N	2026-07-31 18:46:38	2026-07-31 18:46:38
70	40	Servicios	t	\N	2026-07-31 18:46:38	2026-07-31 18:46:38
71	41	Ventas	t	\N	2026-08-06 20:55:39	2026-08-06 20:55:39
72	41	Servicios	t	\N	2026-08-06 20:55:39	2026-08-06 20:55:39
59	35	Ventas	t	\N	2026-07-22 22:52:51	2026-08-07 14:02:56
\.


--
-- Data for Name: plans; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.plans (id_plan, nombre_plan, precio, fecha_inicio, fecha_fin, created_at, updated_at, max_usuarios, stripe_price_id) FROM stdin;
3	Pro	599.00	2026-01-01	2026-12-31	2026-07-03 18:23:58	2026-07-03 18:23:58	15	\N
4	Enterprise	999.00	2026-01-01	2026-12-31	2026-07-03 18:23:58	2026-07-03 18:23:58	\N	\N
1	Básico	299.00	2026-01-01	2027-01-01	\N	2026-07-27 21:12:09	5	price_1Txu2J3IquxaKVUJjdoERGHs
\.


--
-- Data for Name: productos; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.productos (id_productos, id_tenant, id_categorias, nombre, descripcion, precio, stock, activo, deleted_at, created_at, updated_at, sku, stock_minimo, precio_compra, imagen) FROM stdin;
3	1	2	Agua 600ml	\N	18.00	100	t	\N	2026-07-03 18:23:58	2026-07-03 18:23:58	\N	0	0.00	\N
8	1	2	Refresco Cola 600ml	\N	18.00	27	t	2026-07-08 21:17:21	2026-07-08 15:28:17	2026-07-08 21:17:21	REF-600	5	8.00	\N
9	1	1	Agua Mineral 1L	\N	25.00	50	t	2026-07-08 16:37:49	2026-07-08 16:00:44	2026-07-08 16:37:49	\N	0	0.00	\N
12	31	5	Refresco	\N	35.00	98	t	\N	2026-07-21 16:46:46	2026-07-21 22:15:37	\N	10	15.00	\N
13	32	6	Desayuno Continental	\N	180.00	194	t	\N	2026-07-21 17:36:41	2026-07-21 22:30:01	\N	10	90.00	\N
14	32	6	Vino Tinto	\N	280.00	47	t	\N	2026-07-21 17:36:41	2026-07-21 22:30:01	\N	5	140.00	\N
15	33	7	Amoxicilina 500mg	\N	8.00	479	t	\N	2026-07-21 17:36:42	2026-07-21 17:37:39	\N	50	4.00	\N
16	33	7	Paracetamol 500mg	\N	3.00	988	t	\N	2026-07-21 17:36:42	2026-07-21 22:54:05	\N	100	1.00	\N
17	34	8	Caja de Tornillos	\N	50.00	495	t	\N	2026-07-21 18:47:45	2026-07-21 18:47:54	\N	20	25.00	\N
11	31	5	Filete de Res	\N	220.00	45	t	\N	2026-07-21 16:46:46	2026-07-22 18:12:49	\N	5	120.00	\N
18	31	5	dasd	\N	22.00	28	t	\N	2026-07-21 22:18:35	2026-07-23 21:23:58	sdad	21	15.00	\N
6	1	3	Papas Sabritas	\N	16.00	39	t	\N	2026-07-03 18:23:58	2026-07-24 20:25:09	\N	0	0.00	\N
4	1	2	Refresco 355ml	\N	22.00	79	t	\N	2026-07-03 18:23:58	2026-07-24 20:25:09	\N	0	0.00	\N
1	1	1	Torta de jamón	\N	55.00	49	t	\N	2026-07-03 18:23:58	2026-07-24 20:25:09	\N	0	0.00	\N
2	1	1	Quesadilla	\N	45.00	29	t	\N	2026-07-03 18:23:58	2026-07-24 20:25:09	\N	0	0.00	\N
5	1	2	Café americano	\N	35.00	59	t	\N	2026-07-03 18:23:58	2026-07-24 20:25:09	\N	0	0.00	\N
19	1	2	tets	\N	25.00	25	t	2026-07-24 20:28:30	2026-07-24 20:27:50	2026-07-24 20:28:30	2	25	25.00	\N
7	1	3	Chocolatín	\N	20.00	58163	t	\N	2026-07-03 18:23:58	2026-08-06 20:40:53	\N	0	0.00	\N
20	35	11	test	\N	5.00	46	t	\N	2026-08-06 21:48:02	2026-08-07 17:49:38	\N	56	5.00	\N
21	35	11	Producto Con Foto 2	\N	25.00	4	t	\N	2026-08-06 22:30:27	2026-08-07 17:49:38	\N	0	0.00	productos/XVdopCvmsiutfy31U5YaJtCUWPrGAzgTgOkHBUb3.png
\.


--
-- Data for Name: proveedores; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.proveedores (id_proveedor, id_tenant, nombre, contacto, email, telefono, direccion, rfc, activo, deleted_at, created_at, updated_at) FROM stdin;
1	1	Distribuidora ACME	\N	acme@test.com	\N	\N	\N	t	\N	2026-07-08 15:28:16	2026-07-08 15:28:16
2	1	Distribuidora Test	\N	\N	\N	\N	\N	t	\N	2026-07-08 16:00:48	2026-07-08 16:00:48
3	1	ProvTemp	\N	\N	\N	\N	\N	t	\N	2026-07-08 16:55:46	2026-07-08 16:55:46
4	31	Proveedor Test	\N	\N	\N	\N	\N	t	\N	2026-07-23 21:23:58	2026-07-23 21:23:58
5	35	Bimbo	Bimbo	israel.2014flores@gmail.com	5255252525	\N	\N	t	\N	2026-08-07 16:06:54	2026-08-07 16:06:54
\.


--
-- Data for Name: rol_permiso; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.rol_permiso (id_rol, id_permiso) FROM stdin;
27	1
27	2
27	3
27	4
27	5
27	6
27	7
27	8
27	9
27	10
27	11
27	12
27	13
27	14
27	15
27	16
27	17
27	18
27	19
27	20
27	21
27	22
27	23
27	24
27	25
27	26
27	27
27	28
27	29
27	30
27	31
27	32
27	33
27	34
27	35
27	36
27	37
27	38
27	39
27	40
27	41
27	42
27	43
27	44
27	45
27	46
27	47
27	48
27	49
27	50
27	51
27	52
27	53
27	54
27	55
27	56
27	57
27	58
27	59
27	60
27	61
27	62
27	63
27	64
27	65
27	66
27	67
27	68
27	69
27	70
27	71
27	72
27	73
27	74
27	75
27	76
27	77
27	78
27	79
27	80
28	1
28	2
28	3
28	4
28	5
28	6
28	7
28	8
28	9
28	10
28	11
28	12
28	13
28	14
28	15
28	16
28	17
28	18
28	19
28	20
28	21
28	22
28	23
28	24
28	25
28	26
28	27
28	28
28	29
28	30
28	31
28	32
28	33
28	34
28	35
28	36
28	37
28	38
28	39
28	40
28	41
28	42
28	43
28	44
28	45
28	46
28	47
28	48
28	49
28	50
28	51
28	52
28	53
28	54
28	55
28	56
28	57
28	58
28	59
28	60
28	61
28	62
28	63
28	64
28	65
28	66
28	67
28	68
28	69
28	70
28	71
28	72
28	73
28	74
28	75
28	76
28	77
28	78
28	79
28	80
29	1
29	2
29	3
29	4
29	5
29	6
29	7
29	8
29	9
29	10
29	11
29	12
29	13
29	14
29	15
29	16
29	17
29	18
29	19
29	20
29	21
29	22
29	23
29	24
29	25
29	26
29	27
29	28
29	29
29	30
29	31
29	32
29	33
29	34
29	35
29	36
29	37
29	38
29	39
29	40
29	41
29	42
29	43
29	44
29	45
29	46
29	47
29	48
29	49
29	50
29	51
29	52
29	53
29	54
29	55
29	56
29	57
29	58
29	59
29	60
29	61
29	62
29	63
29	64
29	65
29	66
29	67
29	68
29	69
29	70
29	71
29	72
29	73
29	74
29	75
29	76
29	77
29	78
29	79
29	80
30	1
30	2
30	3
30	4
30	5
30	6
30	7
30	8
30	9
30	10
30	11
30	12
30	13
30	14
30	15
30	16
30	17
30	18
30	19
30	20
30	21
30	22
30	23
30	24
30	25
30	26
30	27
30	28
30	29
30	30
30	31
30	32
30	33
30	34
30	35
30	36
30	37
30	38
30	39
30	40
30	41
30	42
30	43
30	44
30	45
30	46
30	47
30	48
30	49
30	50
30	51
30	52
30	53
30	54
30	55
30	56
30	57
30	58
30	59
30	60
30	61
30	62
30	63
30	64
30	65
30	66
30	67
30	68
30	69
30	70
30	71
30	72
30	73
30	74
30	75
30	76
30	77
30	78
30	79
30	80
31	1
31	2
31	3
31	4
31	5
31	6
31	7
31	8
31	9
31	10
31	11
31	12
31	13
31	14
31	15
31	16
31	17
31	18
31	19
31	20
31	21
31	22
31	23
31	24
31	25
31	26
31	27
31	28
31	29
31	30
31	31
31	32
31	33
31	34
31	35
31	36
31	37
31	38
31	39
31	40
31	41
31	42
31	43
31	44
31	45
31	46
31	47
31	48
31	49
31	50
31	51
31	52
31	53
31	54
31	55
31	56
31	57
31	58
31	59
31	60
31	61
31	62
31	63
31	64
31	65
31	66
31	67
31	68
31	69
31	70
31	71
31	72
31	73
31	74
31	75
31	76
31	77
31	78
31	79
31	80
32	1
32	2
32	3
32	4
32	5
32	6
32	7
32	8
32	9
32	10
32	11
32	12
32	13
32	14
32	15
32	16
32	17
32	18
32	19
32	20
32	21
32	22
32	23
32	24
32	25
32	26
32	27
32	28
32	29
32	30
32	31
32	32
32	33
32	34
32	35
32	36
32	37
32	38
32	39
32	40
32	41
32	42
32	43
32	44
32	45
32	46
32	47
32	48
32	49
32	50
32	51
32	52
32	53
32	54
32	55
32	56
32	57
32	58
32	59
32	60
32	61
32	62
32	63
32	64
32	65
32	66
32	67
32	68
32	69
32	70
32	71
32	72
32	73
32	74
32	75
32	76
32	77
32	78
32	79
32	80
33	1
33	2
33	3
33	4
33	5
33	6
33	7
33	8
33	9
33	10
33	11
33	12
33	13
33	14
33	15
33	16
33	17
33	18
33	19
33	20
33	21
33	22
33	23
33	24
33	25
33	26
33	27
33	28
33	29
33	30
33	31
33	32
33	33
33	34
33	35
33	36
33	37
33	38
33	39
33	40
33	41
33	42
33	43
33	44
33	45
33	46
33	47
33	48
33	49
33	50
33	51
33	52
33	53
33	54
33	55
33	56
33	57
33	58
33	59
33	60
33	61
33	62
33	63
33	64
33	65
33	66
33	67
33	68
33	69
33	70
33	71
33	72
33	73
33	74
33	75
33	76
33	77
33	78
33	79
33	80
34	1
34	2
34	3
34	4
34	5
34	6
34	7
34	8
34	9
34	10
34	11
34	12
34	13
34	14
34	15
34	16
34	17
34	18
34	19
34	20
34	21
34	22
34	23
34	24
34	25
34	26
34	27
34	28
34	29
34	30
34	31
34	32
34	33
34	34
34	35
34	36
34	37
34	38
34	39
34	40
34	41
34	42
34	43
34	44
34	45
34	46
34	47
34	48
34	49
34	50
34	51
34	52
34	53
34	54
34	55
34	56
34	57
34	58
34	59
34	60
34	61
34	62
34	63
34	64
34	65
34	66
34	67
34	68
34	69
34	70
34	71
34	72
34	73
34	74
34	75
34	76
34	77
34	78
34	79
34	80
35	1
35	2
35	3
35	4
35	5
35	6
35	7
35	8
35	9
35	10
35	11
35	12
35	13
35	14
35	15
35	16
35	17
35	18
35	19
35	20
35	21
35	22
35	23
35	24
35	25
35	26
35	27
35	28
35	29
35	30
35	31
35	32
35	33
35	34
35	35
35	36
35	37
35	38
35	39
35	40
35	41
35	42
35	43
35	44
35	45
35	46
35	47
35	48
35	49
35	50
35	51
35	52
35	53
35	54
35	55
35	56
35	57
35	58
35	59
35	60
35	61
35	62
35	63
35	64
35	65
35	66
35	67
35	68
35	69
35	70
35	71
35	72
35	73
35	74
35	75
35	76
35	77
35	78
35	79
35	80
36	1
36	2
36	3
36	4
36	5
36	6
36	7
36	8
36	9
36	10
36	11
36	12
36	13
36	14
36	15
36	16
36	17
36	18
36	19
36	20
36	21
36	22
36	23
36	24
36	25
36	26
36	27
36	28
36	29
36	30
36	31
36	32
36	33
36	34
36	35
36	36
36	37
36	38
36	39
36	40
36	41
36	42
36	43
36	44
36	45
36	46
36	47
36	48
36	49
36	50
36	51
36	52
36	53
36	54
36	55
36	56
36	57
36	58
36	59
36	60
36	61
36	62
36	63
36	64
36	65
36	66
36	67
36	68
36	69
36	70
36	71
36	72
36	73
36	74
36	75
36	76
36	77
36	78
36	79
36	80
37	1
37	2
37	3
37	4
37	5
37	6
37	7
37	8
37	9
37	10
37	11
37	12
37	13
37	14
37	15
37	16
37	17
37	18
37	19
37	20
37	21
37	22
37	23
37	24
37	25
37	26
37	27
37	28
37	29
37	30
37	31
37	32
37	33
37	34
37	35
37	36
37	37
37	38
37	39
37	40
37	41
37	42
37	43
37	44
37	45
37	46
37	47
37	48
37	49
37	50
37	51
37	52
37	53
37	54
37	55
37	56
37	57
37	58
37	59
37	60
37	61
37	62
37	63
37	64
37	65
37	66
37	67
37	68
37	69
37	70
37	71
37	72
37	73
37	74
37	75
37	76
37	77
37	78
37	79
37	80
38	1
38	2
38	3
38	4
38	5
38	6
38	7
38	8
38	9
38	10
38	11
38	12
38	13
38	14
38	15
38	16
38	17
38	18
38	19
38	20
38	21
38	22
38	23
38	24
38	25
38	26
38	27
38	28
38	29
38	30
38	31
38	32
38	33
38	34
38	35
38	36
38	37
38	38
38	39
38	40
38	41
38	42
38	43
38	44
38	45
38	46
38	47
38	48
38	49
38	50
38	51
38	52
38	53
38	54
38	55
38	56
38	57
38	58
38	59
38	60
38	61
38	62
38	63
38	64
38	65
38	66
38	67
38	68
38	69
38	70
38	71
38	72
38	73
38	74
38	75
38	76
38	77
38	78
38	79
38	80
39	1
39	2
39	3
39	4
39	5
39	6
39	7
39	8
39	9
39	10
39	11
39	12
39	13
39	14
39	15
39	16
39	17
39	18
39	19
39	20
39	21
39	22
39	23
39	24
39	25
39	26
39	27
39	28
39	29
39	30
39	31
39	32
39	33
39	34
39	35
39	36
39	37
39	38
39	39
39	40
39	41
39	42
39	43
39	44
39	45
39	46
39	47
39	48
39	49
39	50
39	51
39	52
39	53
39	54
39	55
39	56
39	57
39	58
39	59
39	60
39	61
39	62
39	63
39	64
39	65
39	66
39	67
39	68
39	69
39	70
39	71
39	72
39	73
39	74
39	75
39	76
39	77
39	78
39	79
39	80
40	1
40	2
40	3
40	4
40	5
40	6
40	7
40	8
40	9
40	10
40	11
40	12
40	13
40	14
40	15
40	16
40	17
40	18
40	19
40	20
40	21
40	22
40	23
40	24
40	25
40	26
40	27
40	28
40	29
40	30
40	31
40	32
40	33
40	34
40	35
40	36
40	37
40	38
40	39
40	40
40	41
40	42
40	43
40	44
40	45
40	46
40	47
40	48
40	49
40	50
40	51
40	52
40	53
40	54
40	55
40	56
40	57
40	58
40	59
40	60
40	61
40	62
40	63
40	64
40	65
40	66
40	67
40	68
40	69
40	70
40	71
40	72
40	73
40	74
40	75
40	76
40	77
40	78
40	79
40	80
41	1
41	2
41	3
41	4
41	5
41	6
41	7
41	8
41	9
41	10
41	11
41	12
41	13
41	14
41	15
41	16
41	17
41	18
41	19
41	20
41	21
41	22
41	23
41	24
41	25
41	26
41	27
41	28
41	29
41	30
41	31
41	32
41	33
41	34
41	35
41	36
41	37
41	38
41	39
41	40
41	41
41	42
41	43
41	44
41	45
41	46
41	47
41	48
41	49
41	50
41	51
41	52
41	53
41	54
41	55
41	56
41	57
41	58
41	59
41	60
41	61
41	62
41	63
41	64
41	65
41	66
41	67
41	68
41	69
41	70
41	71
41	72
41	73
41	74
41	75
41	76
41	77
41	78
41	79
41	80
42	1
42	2
42	3
42	4
42	5
42	6
42	7
42	8
42	9
42	10
42	11
42	12
42	13
42	14
42	15
42	16
42	17
42	18
42	19
42	20
42	21
42	22
42	23
42	24
42	25
42	26
42	27
42	28
42	29
42	30
42	31
42	32
42	33
42	34
42	35
42	36
42	37
42	38
42	39
42	40
42	41
42	42
42	43
42	44
42	45
42	46
42	47
42	48
42	49
42	50
42	51
42	52
42	53
42	54
42	55
42	56
42	57
42	58
42	59
42	60
42	61
42	62
42	63
42	64
42	65
42	66
42	67
42	68
42	69
42	70
42	71
42	72
42	73
42	74
42	75
42	76
42	77
42	78
42	79
42	80
43	1
43	2
43	3
43	4
43	5
43	6
43	7
43	8
43	9
43	10
43	11
43	12
43	13
43	14
43	15
43	16
43	17
43	18
43	19
43	20
43	21
43	22
43	23
43	24
43	25
43	26
43	27
43	28
43	29
43	30
43	31
43	32
43	33
43	34
43	35
43	36
43	37
43	38
43	39
43	40
43	41
43	42
43	43
43	44
43	45
43	46
43	47
43	48
43	49
43	50
43	51
43	52
43	53
43	54
43	55
43	56
43	57
43	58
43	59
43	60
43	61
43	62
43	63
43	64
43	65
43	66
43	67
43	68
43	69
43	70
43	71
43	72
43	73
43	74
43	75
43	76
43	77
43	78
43	79
43	80
44	1
44	2
44	3
44	4
44	5
44	6
44	7
44	8
44	9
44	10
44	11
44	12
44	13
44	14
44	15
44	16
44	17
44	18
44	19
44	20
44	21
44	22
44	23
44	24
44	25
44	26
44	27
44	28
44	29
44	30
44	31
44	32
44	33
44	34
44	35
44	36
44	37
44	38
44	39
44	40
44	41
44	42
44	43
44	44
44	45
44	46
44	47
44	48
44	49
44	50
44	51
44	52
44	53
44	54
44	55
44	56
44	57
44	58
44	59
44	60
44	61
44	62
44	63
44	64
44	65
44	66
44	67
44	68
44	69
44	70
44	71
44	72
44	73
44	74
44	75
44	76
44	77
44	78
44	79
44	80
45	1
45	2
45	3
45	4
45	5
45	6
45	7
45	8
45	9
45	10
45	11
45	12
45	13
45	14
45	15
45	16
45	17
45	18
45	19
45	20
45	21
45	22
45	23
45	24
45	25
45	26
45	27
45	28
45	29
45	30
45	31
45	32
45	33
45	34
45	35
45	36
45	37
45	38
45	39
45	40
45	41
45	42
45	43
45	44
45	45
45	46
45	47
45	48
45	49
45	50
45	51
45	52
45	53
45	54
45	55
45	56
45	57
45	58
45	59
45	60
45	61
45	62
45	63
45	64
45	65
45	66
45	67
45	68
45	69
45	70
45	71
45	72
45	73
45	74
45	75
45	76
45	77
45	78
45	79
45	80
46	1
46	2
46	3
46	4
46	5
46	6
46	7
46	8
46	9
46	10
46	11
46	12
46	13
46	14
46	15
46	16
46	17
46	18
46	19
46	20
46	21
46	22
46	23
46	24
46	25
46	26
46	27
46	28
46	29
46	30
46	31
46	32
46	33
46	34
46	35
46	36
46	37
46	38
46	39
46	40
46	41
46	42
46	43
46	44
46	45
46	46
46	47
46	48
46	49
46	50
46	51
46	52
46	53
46	54
46	55
46	56
46	57
46	58
46	59
46	60
46	61
46	62
46	63
46	64
46	65
46	66
46	67
46	68
46	69
46	70
46	71
46	72
46	73
46	74
46	75
46	76
46	77
46	78
46	79
46	80
47	1
47	2
47	3
47	4
47	5
47	6
47	7
47	8
47	9
47	10
47	11
47	12
47	13
47	14
47	15
47	16
47	17
47	18
47	19
47	20
47	21
47	22
47	23
47	24
47	25
47	26
47	27
47	28
47	29
47	30
47	31
47	32
47	33
47	34
47	35
47	36
47	37
47	38
47	39
47	40
47	41
47	42
47	43
47	44
47	45
47	46
47	47
47	48
47	49
47	50
47	51
47	52
47	53
47	54
47	55
47	56
47	57
47	58
47	59
47	60
47	61
47	62
47	63
47	64
47	65
47	66
47	67
47	68
47	69
47	70
47	71
47	72
47	73
47	74
47	75
47	76
47	77
47	78
47	79
47	80
48	1
48	2
48	3
48	4
48	5
48	6
48	7
48	8
48	9
48	10
48	11
48	12
48	13
48	14
48	15
48	16
48	17
48	18
48	19
48	20
48	21
48	22
48	23
48	24
48	25
48	26
48	27
48	28
48	29
48	30
48	31
48	32
48	33
48	34
48	35
48	36
48	37
48	38
48	39
48	40
48	41
48	42
48	43
48	44
48	45
48	46
48	47
48	48
48	49
48	50
48	51
48	52
48	53
48	54
48	55
48	56
48	57
48	58
48	59
48	60
48	61
48	62
48	63
48	64
48	65
48	66
48	67
48	68
48	69
48	70
48	71
48	72
48	73
48	74
48	75
48	76
48	77
48	78
48	79
48	80
49	1
49	2
49	3
49	4
49	5
49	6
49	7
49	8
49	9
49	10
49	11
49	12
49	13
49	14
49	15
49	16
49	17
49	18
49	19
49	20
49	21
49	22
49	23
49	24
49	25
49	26
49	27
49	28
49	29
49	30
49	31
49	32
49	33
49	34
49	35
49	36
49	37
49	38
49	39
49	40
49	41
49	42
49	43
49	44
49	45
49	46
49	47
49	48
49	49
49	50
49	51
49	52
49	53
49	54
49	55
49	56
49	57
49	58
49	59
49	60
49	61
49	62
49	63
49	64
49	65
49	66
49	67
49	68
49	69
49	70
49	71
49	72
49	73
49	74
49	75
49	76
49	77
49	78
49	79
49	80
50	1
50	2
50	3
50	4
50	5
50	6
50	7
50	8
50	9
50	10
50	11
50	12
50	13
50	14
50	15
50	16
50	17
50	18
50	19
50	20
50	21
50	22
50	23
50	24
50	25
50	26
50	27
50	28
50	29
50	30
50	31
50	32
50	33
50	34
50	35
50	36
50	37
50	38
50	39
50	40
50	41
50	42
50	43
50	44
50	45
50	46
50	47
50	48
50	49
50	50
50	51
50	52
50	53
50	54
50	55
50	56
50	57
50	58
50	59
50	60
50	61
50	62
50	63
50	64
50	65
50	66
50	67
50	68
50	69
50	70
50	71
50	72
50	73
50	74
50	75
50	76
50	77
50	78
50	79
50	80
51	1
51	2
51	3
51	4
51	5
51	6
51	7
51	8
51	9
51	10
51	11
51	12
51	13
51	14
51	15
51	16
51	17
51	18
51	19
51	20
51	21
51	22
51	23
51	24
51	25
51	26
51	27
51	28
51	29
51	30
51	31
51	32
51	33
51	34
51	35
51	36
51	37
51	38
51	39
51	40
51	41
51	42
51	43
51	44
51	45
51	46
51	47
51	48
51	49
51	50
51	51
51	52
51	53
51	54
51	55
51	56
51	57
51	58
51	59
51	60
51	61
51	62
51	63
51	64
51	65
51	66
51	67
51	68
51	69
51	70
51	71
51	72
51	73
51	74
51	75
51	76
51	77
51	78
51	79
51	80
65	1
65	2
65	3
65	4
65	13
65	14
65	15
65	16
65	21
65	22
65	23
65	24
65	29
65	30
65	31
65	32
65	41
65	42
65	43
65	44
65	45
65	46
65	47
65	48
65	69
65	70
65	71
65	72
65	77
65	78
65	79
65	80
66	1
66	2
66	3
66	4
66	5
66	6
66	7
66	8
66	9
66	10
66	11
66	12
66	13
66	14
66	15
66	16
66	17
66	18
66	19
66	20
66	21
66	22
66	23
66	24
66	25
66	26
66	27
66	28
66	29
66	30
66	31
66	32
66	33
66	34
66	35
66	36
66	37
66	38
66	39
66	40
66	41
66	42
66	43
66	44
66	45
66	46
66	47
66	48
66	49
66	50
66	51
66	52
66	53
66	54
66	55
66	56
66	57
66	58
66	59
66	60
66	61
66	62
66	63
66	64
66	65
66	66
66	67
66	68
66	69
66	70
66	71
66	72
66	73
66	74
66	75
66	76
66	77
66	78
66	79
66	80
67	1
67	2
67	3
67	4
67	5
67	6
67	7
67	8
67	9
67	10
67	11
67	12
67	13
67	14
67	15
67	16
67	17
67	18
67	19
67	20
67	21
67	22
67	23
67	24
67	25
67	26
67	27
67	28
67	29
67	30
67	31
67	32
67	33
67	34
67	35
67	36
67	37
67	38
67	39
67	40
67	41
67	42
67	43
67	44
67	45
67	46
67	47
67	48
67	49
67	50
67	51
67	52
67	53
67	54
67	55
67	56
67	57
67	58
67	59
67	60
67	61
67	62
67	63
67	64
67	65
67	66
67	67
67	68
67	69
67	70
67	71
67	72
67	73
67	74
67	75
67	76
67	77
67	78
67	79
67	80
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.roles (id_rol, id_tenant, id_modulo, clave, nombre, descripcion, es_sistema, created_at, updated_at) FROM stdin;
1	\N	\N	platform.superadmin	Super Administrador	\N	t	2026-07-09 17:18:05	2026-07-09 17:18:05
2	1	\N	tenant.admin	Administrador	\N	t	2026-07-09 17:18:05	2026-07-09 17:18:05
3	2	\N	tenant.admin	Administrador	\N	t	2026-07-09 17:18:05	2026-07-09 17:18:05
4	4	\N	tenant.admin	Administrador	\N	t	2026-07-09 17:18:05	2026-07-09 17:18:05
5	5	\N	tenant.admin	Administrador	\N	t	2026-07-09 17:18:05	2026-07-09 17:18:05
6	6	\N	tenant.admin	Administrador	\N	t	2026-07-09 17:18:05	2026-07-09 17:18:05
7	7	\N	tenant.admin	Administrador	\N	t	2026-07-09 17:18:05	2026-07-09 17:18:05
8	8	\N	tenant.admin	Administrador	\N	t	2026-07-09 17:18:05	2026-07-09 17:18:05
9	9	\N	tenant.admin	Administrador	\N	t	2026-07-09 17:18:05	2026-07-09 17:18:05
10	10	\N	tenant.admin	Administrador	\N	t	2026-07-09 17:18:05	2026-07-09 17:18:05
11	11	\N	tenant.admin	Administrador	\N	t	2026-07-09 17:18:05	2026-07-09 17:18:05
12	12	\N	tenant.admin	Administrador	\N	t	2026-07-09 17:18:05	2026-07-09 17:18:05
13	13	\N	tenant.admin	Administrador	\N	t	2026-07-09 17:18:05	2026-07-09 17:18:05
14	14	\N	tenant.admin	Administrador	\N	t	2026-07-09 17:18:05	2026-07-09 17:18:05
15	15	\N	tenant.admin	Administrador	\N	t	2026-07-09 17:18:05	2026-07-09 17:18:05
16	16	\N	tenant.admin	Administrador	\N	t	2026-07-09 17:18:05	2026-07-09 17:18:05
17	17	\N	tenant.admin	Administrador	\N	t	2026-07-09 17:18:05	2026-07-09 17:18:05
18	18	\N	tenant.admin	Administrador	\N	t	2026-07-09 17:18:05	2026-07-09 17:18:05
19	19	\N	tenant.admin	Administrador	\N	t	2026-07-09 17:18:05	2026-07-09 17:18:05
20	20	\N	tenant.admin	Administrador	\N	t	2026-07-09 17:18:05	2026-07-09 17:18:05
21	21	\N	tenant.admin	Administrador	\N	t	2026-07-09 17:18:05	2026-07-09 17:18:05
22	22	\N	tenant.admin	Administrador	\N	t	2026-07-09 17:18:05	2026-07-09 17:18:05
23	23	\N	tenant.admin	Administrador	\N	t	2026-07-09 17:18:05	2026-07-09 17:18:05
24	24	\N	tenant.admin	Administrador	\N	t	2026-07-09 17:18:05	2026-07-09 17:18:05
26	26	\N	tenant.admin	Administrador	\N	t	2026-07-15 20:15:04	2026-07-15 20:15:04
27	1	\N	tenant.miembro	Miembro	Rol por defecto con todos los permisos, asignado automáticamente para no romper el acceso de usuarios existentes.	t	2026-07-16 22:24:07	2026-07-16 22:24:07
28	2	\N	tenant.miembro	Miembro	Rol por defecto con todos los permisos, asignado automáticamente para no romper el acceso de usuarios existentes.	t	2026-07-16 22:24:08	2026-07-16 22:24:08
29	3	\N	tenant.miembro	Miembro	Rol por defecto con todos los permisos, asignado automáticamente para no romper el acceso de usuarios existentes.	t	2026-07-16 22:24:08	2026-07-16 22:24:08
30	4	\N	tenant.miembro	Miembro	Rol por defecto con todos los permisos, asignado automáticamente para no romper el acceso de usuarios existentes.	t	2026-07-16 22:24:08	2026-07-16 22:24:08
31	5	\N	tenant.miembro	Miembro	Rol por defecto con todos los permisos, asignado automáticamente para no romper el acceso de usuarios existentes.	t	2026-07-16 22:24:09	2026-07-16 22:24:09
32	6	\N	tenant.miembro	Miembro	Rol por defecto con todos los permisos, asignado automáticamente para no romper el acceso de usuarios existentes.	t	2026-07-16 22:24:11	2026-07-16 22:24:11
33	7	\N	tenant.miembro	Miembro	Rol por defecto con todos los permisos, asignado automáticamente para no romper el acceso de usuarios existentes.	t	2026-07-16 22:24:11	2026-07-16 22:24:11
34	8	\N	tenant.miembro	Miembro	Rol por defecto con todos los permisos, asignado automáticamente para no romper el acceso de usuarios existentes.	t	2026-07-16 22:24:11	2026-07-16 22:24:11
35	9	\N	tenant.miembro	Miembro	Rol por defecto con todos los permisos, asignado automáticamente para no romper el acceso de usuarios existentes.	t	2026-07-16 22:24:12	2026-07-16 22:24:12
36	10	\N	tenant.miembro	Miembro	Rol por defecto con todos los permisos, asignado automáticamente para no romper el acceso de usuarios existentes.	t	2026-07-16 22:24:12	2026-07-16 22:24:12
37	11	\N	tenant.miembro	Miembro	Rol por defecto con todos los permisos, asignado automáticamente para no romper el acceso de usuarios existentes.	t	2026-07-16 22:24:12	2026-07-16 22:24:12
38	12	\N	tenant.miembro	Miembro	Rol por defecto con todos los permisos, asignado automáticamente para no romper el acceso de usuarios existentes.	t	2026-07-16 22:24:13	2026-07-16 22:24:13
39	13	\N	tenant.miembro	Miembro	Rol por defecto con todos los permisos, asignado automáticamente para no romper el acceso de usuarios existentes.	t	2026-07-16 22:24:13	2026-07-16 22:24:13
40	14	\N	tenant.miembro	Miembro	Rol por defecto con todos los permisos, asignado automáticamente para no romper el acceso de usuarios existentes.	t	2026-07-16 22:24:14	2026-07-16 22:24:14
41	15	\N	tenant.miembro	Miembro	Rol por defecto con todos los permisos, asignado automáticamente para no romper el acceso de usuarios existentes.	t	2026-07-16 22:24:14	2026-07-16 22:24:14
42	16	\N	tenant.miembro	Miembro	Rol por defecto con todos los permisos, asignado automáticamente para no romper el acceso de usuarios existentes.	t	2026-07-16 22:24:14	2026-07-16 22:24:14
43	17	\N	tenant.miembro	Miembro	Rol por defecto con todos los permisos, asignado automáticamente para no romper el acceso de usuarios existentes.	t	2026-07-16 22:24:15	2026-07-16 22:24:15
44	18	\N	tenant.miembro	Miembro	Rol por defecto con todos los permisos, asignado automáticamente para no romper el acceso de usuarios existentes.	t	2026-07-16 22:24:15	2026-07-16 22:24:15
45	19	\N	tenant.miembro	Miembro	Rol por defecto con todos los permisos, asignado automáticamente para no romper el acceso de usuarios existentes.	t	2026-07-16 22:24:15	2026-07-16 22:24:15
46	20	\N	tenant.miembro	Miembro	Rol por defecto con todos los permisos, asignado automáticamente para no romper el acceso de usuarios existentes.	t	2026-07-16 22:24:16	2026-07-16 22:24:16
47	21	\N	tenant.miembro	Miembro	Rol por defecto con todos los permisos, asignado automáticamente para no romper el acceso de usuarios existentes.	t	2026-07-16 22:24:16	2026-07-16 22:24:16
48	22	\N	tenant.miembro	Miembro	Rol por defecto con todos los permisos, asignado automáticamente para no romper el acceso de usuarios existentes.	t	2026-07-16 22:24:16	2026-07-16 22:24:16
49	23	\N	tenant.miembro	Miembro	Rol por defecto con todos los permisos, asignado automáticamente para no romper el acceso de usuarios existentes.	t	2026-07-16 22:24:17	2026-07-16 22:24:17
50	24	\N	tenant.miembro	Miembro	Rol por defecto con todos los permisos, asignado automáticamente para no romper el acceso de usuarios existentes.	t	2026-07-16 22:24:17	2026-07-16 22:24:17
51	26	\N	tenant.miembro	Miembro	Rol por defecto con todos los permisos, asignado automáticamente para no romper el acceso de usuarios existentes.	t	2026-07-16 22:24:17	2026-07-16 22:24:17
56	31	\N	tenant.admin	Administrador	\N	t	2026-07-21 16:46:36	2026-07-21 16:46:36
57	32	\N	tenant.admin	Administrador	\N	t	2026-07-21 17:36:31	2026-07-21 17:36:31
58	33	\N	tenant.admin	Administrador	\N	t	2026-07-21 17:36:31	2026-07-21 17:36:31
59	34	\N	tenant.admin	Administrador	\N	t	2026-07-21 18:47:40	2026-07-21 18:47:40
60	35	\N	tenant.admin	Administrador	\N	t	2026-07-22 22:52:51	2026-07-22 22:52:51
61	36	\N	tenant.admin	Administrador	\N	t	2026-07-22 23:00:40	2026-07-22 23:00:40
62	37	\N	tenant.admin	Administrador	\N	t	2026-07-23 22:25:03	2026-07-23 22:25:03
63	38	\N	tenant.admin	Administrador	\N	t	2026-07-23 22:35:44	2026-07-23 22:35:44
64	39	\N	tenant.admin	Administrador	\N	t	2026-07-23 22:37:08	2026-07-23 22:37:08
65	35	\N	custom.vendedor.klJFau	Vendedor	Es para vender	f	2026-07-27 22:03:55	2026-07-27 22:03:55
66	35	\N	tenant.miembro	Miembro	Rol por defecto con todos los permisos.	t	2026-07-27 22:54:37	2026-07-27 22:54:37
67	31	\N	tenant.miembro	Miembro	Rol por defecto con todos los permisos.	t	2026-07-28 23:14:55	2026-07-28 23:14:55
68	40	\N	tenant.admin	Administrador	\N	t	2026-07-31 18:46:38	2026-07-31 18:46:38
69	41	\N	tenant.admin	Administrador	\N	t	2026-08-06 20:55:39	2026-08-06 20:55:39
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
\.


--
-- Data for Name: stripe_webhook_events; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.stripe_webhook_events (id, stripe_event_id, tipo, procesado_en, created_at, updated_at) FROM stdin;
1	evt_1TxuXQ3Ia6HFhXln9dzcZUfM	product.created	2026-07-27 19:50:10	2026-07-27 19:50:10	2026-07-27 19:50:10
2	evt_1TxuXQ3Ia6HFhXlnTt8dhYVu	price.created	2026-07-27 19:50:11	2026-07-27 19:50:11	2026-07-27 19:50:11
3	evt_3TxuXS3Ia6HFhXln1TzNcvg5	charge.succeeded	2026-07-27 19:50:13	2026-07-27 19:50:13	2026-07-27 19:50:13
4	evt_3TxuXS3Ia6HFhXln1CTAoi8Z	payment_intent.succeeded	2026-07-27 19:50:13	2026-07-27 19:50:13	2026-07-27 19:50:13
5	evt_1TxuXT3Ia6HFhXlndprNN04k	checkout.session.completed	2026-07-27 19:50:13	2026-07-27 19:50:13	2026-07-27 19:50:13
6	evt_3TxuXS3Ia6HFhXln1D6lSF6R	payment_intent.created	2026-07-27 19:50:13	2026-07-27 19:50:13	2026-07-27 19:50:13
7	evt_3TxuXS3Ia6HFhXln1iZE4JfS	charge.updated	2026-07-27 19:50:15	2026-07-27 19:50:15	2026-07-27 19:50:15
\.


--
-- Data for Name: suscripciones; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.suscripciones (id_suscripcion, id_tenant, id_plan, stripe_subscription_id, stripe_price_id, estado, fecha_inicio, fecha_fin_periodo_actual, cancela_al_final_periodo, fecha_cancelacion, ultimo_evento_stripe, created_at, updated_at) FROM stdin;
1	35	1	sub_1TxuZC3IquxaKVUJ9ZLIr8qI	price_1Txu2J3IquxaKVUJjdoERGHs	activa	2026-07-27 19:52:00	2026-08-27 19:52:00	f	\N	{"id":"sub_1TxuZC3IquxaKVUJ9ZLIr8qI","object":"subscription","application":null,"application_fee_percent":null,"automatic_tax":{"disabled_reason":null,"enabled":false,"liability":null},"billing_cycle_anchor":1785181920,"billing_cycle_anchor_config":null,"billing_mode":{"flexible":{"proration_discounts":"included"},"type":"flexible","updated_at":1785181901},"billing_schedules":[],"billing_thresholds":null,"cancel_at":null,"cancel_at_period_end":false,"canceled_at":null,"cancellation_details":{"comment":null,"feedback":null,"reason":null},"collection_method":"charge_automatically","created":1785181920,"currency":"mxn","customer":"cus_UxpzgLPWJMY1MK","customer_account":null,"days_until_due":null,"default_payment_method":"pm_1TxuZ93IquxaKVUJOF3ZZUwd","default_source":null,"default_tax_rates":[],"description":null,"discounts":[],"ended_at":null,"invoice_settings":{"account_tax_ids":null,"custom_fields":null,"description":null,"footer":null,"issuer":{"type":"self"}},"items":{"object":"list","data":[{"id":"si_UxqFR2UxmQSmyg","object":"subscription_item","billing_thresholds":null,"created":1785181921,"current_period_end":1787860320,"current_period_start":1785181920,"discounts":[],"metadata":[],"plan":{"id":"price_1Txu2J3IquxaKVUJjdoERGHs","object":"plan","active":true,"amount":29900,"amount_decimal":"29900","billing_scheme":"per_unit","created":1785179883,"currency":"mxn","interval":"month","interval_count":1,"livemode":false,"metadata":[],"meter":null,"nickname":null,"product":"prod_Uxph2ESmfs1vIS","tiers_mode":null,"transform_usage":null,"trial_period_days":null,"usage_type":"licensed"},"price":{"id":"price_1Txu2J3IquxaKVUJjdoERGHs","object":"price","active":true,"billing_scheme":"per_unit","created":1785179883,"currency":"mxn","custom_unit_amount":null,"livemode":false,"lookup_key":null,"metadata":[],"nickname":null,"product":"prod_Uxph2ESmfs1vIS","recurring":{"interval":"month","interval_count":1,"meter":null,"trial_period_days":null,"usage_type":"licensed"},"tax_behavior":"unspecified","tiers_mode":null,"transform_quantity":null,"type":"recurring","unit_amount":29900,"unit_amount_decimal":"29900"},"quantity":1,"subscription":"sub_1TxuZC3IquxaKVUJ9ZLIr8qI","tax_rates":[]}],"has_more":false,"total_count":1,"url":"\\/v1\\/subscription_items?subscription=sub_1TxuZC3IquxaKVUJ9ZLIr8qI"},"latest_invoice":"in_1TxuZA3IquxaKVUJXu0FOmHz","livemode":false,"managed_payments":{"enabled":false},"metadata":{"id_plan":"1","id_tenant":"35"},"next_pending_invoice_item_invoice":null,"on_behalf_of":null,"pause_collection":null,"payment_settings":{"payment_method_options":{"acss_debit":null,"bancontact":null,"card":{"network":null,"request_three_d_secure":"automatic"},"customer_balance":null,"konbini":null,"payto":null,"pix":null,"sepa_debit":null,"upi":null,"us_bank_account":null},"payment_method_types":null,"save_default_payment_method":"off"},"pending_invoice_item_interval":null,"pending_setup_intent":null,"pending_update":null,"plan":{"id":"price_1Txu2J3IquxaKVUJjdoERGHs","object":"plan","active":true,"amount":29900,"amount_decimal":"29900","billing_scheme":"per_unit","created":1785179883,"currency":"mxn","interval":"month","interval_count":1,"livemode":false,"metadata":[],"meter":null,"nickname":null,"product":"prod_Uxph2ESmfs1vIS","tiers_mode":null,"transform_usage":null,"trial_period_days":null,"usage_type":"licensed"},"quantity":1,"schedule":null,"start_date":1785181920,"status":"active","test_clock":null,"transfer_data":null,"trial_end":null,"trial_settings":{"end_behavior":{"missing_payment_method":"create_invoice"}},"trial_start":null}	2026-07-27 20:21:44	2026-07-27 20:22:20
\.


--
-- Data for Name: tenants; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.tenants (id_tenant, nombre_tenant, subdominio, estado, id_tiponegocio, id_plan, created_at, updated_at, deleted_at, moneda, modulo_crm, modulo_pos, modulo_erp, datos_nicho, onboarding_completado, sector, idioma, zona_horaria, logo, stripe_customer_id) FROM stdin;
2	Smoke Test Company	smoke-test-A4dm2M	activo	1	1	2026-07-03 15:38:47	2026-07-03 15:38:47	\N	\N	t	f	f	\N	f	\N	\N	\N	\N	\N
3	Demo Company	demo	activo	\N	1	2026-07-03 18:23:58	2026-07-03 18:23:58	\N	\N	t	f	f	\N	f	\N	\N	\N	\N	\N
4	Farmacia del Centro	nicho-test-7KStzs	activo	5	1	2026-07-03 18:24:21	2026-07-03 18:24:21	\N	MXN	t	t	t	{"farmTipo":"Farmacia independiente","farmAtencion":["mostrador","delivery"],"farmEspecialidades":["genericos","controlados"]}	t	\N	\N	\N	\N	\N
5	Startup XYZ	device-test-luaBMA	activo	6	1	2026-07-03 18:27:54	2026-07-03 18:27:54	\N	USD	t	f	f	{"startupEtapa":"MVP \\/ Seed","startupModelo":"SaaS \\/ Software","startupMetricas":["mrr","churn"]}	t	\N	\N	\N	\N	\N
6	Repro Test Company	repro-test-b6zAeD	activo	\N	1	2026-07-03 18:44:37	2026-07-03 18:44:37	\N	\N	t	f	f	\N	f	\N	\N	\N	\N	\N
7	Repro Test Company	repro-test-7XbZRz	activo	\N	1	2026-07-03 18:45:42	2026-07-03 18:45:42	\N	\N	t	f	f	\N	f	\N	\N	\N	\N	\N
8	Repro Test Company	repro-test-Kdbyvh	activo	\N	1	2026-07-03 18:46:30	2026-07-03 18:46:30	\N	\N	t	f	f	\N	f	\N	\N	\N	\N	\N
9	Repro Startup Co	repro-test-fhZirf	activo	6	1	2026-07-03 18:47:30	2026-07-03 18:47:36	\N	MXN	t	f	f	{"hotelTipo":null,"hotelHabitaciones":null,"hotelAmenidades":[],"restTipo":null,"restMesas":null,"restCanales":[],"almacenTipo":null,"almacenSkus":null,"almacenOps":[],"farmTipo":null,"farmAtencion":[],"farmEspecialidades":[],"startupEtapa":null,"startupModelo":null,"startupMetricas":[],"tiendaTipo":null,"tiendaCanales":[]}	t	\N	\N	\N	\N	\N
10	Repro Startup Co	repro-test-qNnoC9	activo	6	1	2026-07-03 18:49:47	2026-07-03 18:49:56	\N	MXN	t	f	f	{"hotelTipo":null,"hotelHabitaciones":null,"hotelAmenidades":[],"restTipo":null,"restMesas":null,"restCanales":[],"almacenTipo":null,"almacenSkus":null,"almacenOps":[],"farmTipo":null,"farmAtencion":[],"farmEspecialidades":[],"startupEtapa":null,"startupModelo":null,"startupMetricas":[],"tiendaTipo":null,"tiendaCanales":[]}	t	\N	\N	\N	\N	\N
11	Repro Startup Co	repro-test-wNZzUo	activo	6	1	2026-07-03 18:55:39	2026-07-03 18:55:49	\N	MXN	t	f	f	{"hotelTipo":null,"hotelHabitaciones":null,"hotelAmenidades":[],"restTipo":null,"restMesas":null,"restCanales":[],"almacenTipo":null,"almacenSkus":null,"almacenOps":[],"farmTipo":null,"farmAtencion":[],"farmEspecialidades":[],"startupEtapa":null,"startupModelo":null,"startupMetricas":[],"tiendaTipo":null,"tiendaCanales":[]}	t	\N	\N	\N	\N	\N
12	Repro Startup Co	repro-test-Bfbw4z	activo	6	1	2026-07-03 18:57:34	2026-07-03 18:57:41	\N	MXN	t	f	f	{"hotelTipo":null,"hotelHabitaciones":null,"hotelAmenidades":[],"restTipo":null,"restMesas":null,"restCanales":[],"almacenTipo":null,"almacenSkus":null,"almacenOps":[],"farmTipo":null,"farmAtencion":[],"farmEspecialidades":[],"startupEtapa":null,"startupModelo":null,"startupMetricas":[],"tiendaTipo":null,"tiendaCanales":[]}	t	\N	\N	\N	\N	\N
13	Repro Startup Co	repro-test-nO0xTY	activo	6	1	2026-07-03 18:59:27	2026-07-03 18:59:32	\N	MXN	t	f	f	{"hotelTipo":null,"hotelHabitaciones":null,"hotelAmenidades":[],"restTipo":null,"restMesas":null,"restCanales":[],"almacenTipo":null,"almacenSkus":null,"almacenOps":[],"farmTipo":null,"farmAtencion":[],"farmEspecialidades":[],"startupEtapa":null,"startupModelo":null,"startupMetricas":[],"tiendaTipo":null,"tiendaCanales":[]}	t	\N	\N	\N	\N	\N
14	Cli Test Co	cli-test-DLggGf	activo	7	1	2026-07-03 20:32:44	2026-07-03 20:32:44	\N	MXN	t	t	f	{"tiendaTipo":"Ropa & Moda","tiendaCanales":["fisica"]}	t	\N	\N	\N	\N	\N
15	Lead Test Company	lead-test-8QzZxn	activo	\N	1	2026-07-03 21:31:37	2026-07-03 21:31:37	\N	\N	t	f	f	\N	f	\N	\N	\N	\N	\N
16	Lead Test 2 Company	lead-test-2-eCRONA	activo	\N	1	2026-07-03 21:31:52	2026-07-03 21:31:52	\N	\N	t	f	f	\N	f	\N	\N	\N	\N	\N
17	Puppet Test Co	puppet-test-kKEjYA	activo	6	1	2026-07-03 21:36:13	2026-07-03 21:36:16	\N	MXN	t	f	f	{"hotelTipo":null,"hotelHabitaciones":null,"hotelAmenidades":[],"restTipo":null,"restMesas":null,"restCanales":[],"almacenTipo":null,"almacenSkus":null,"almacenOps":[],"farmTipo":null,"farmAtencion":[],"farmEspecialidades":[],"startupEtapa":null,"startupModelo":null,"startupMetricas":[],"tiendaTipo":null,"tiendaCanales":[]}	t	\N	\N	\N	\N	\N
18	Puppet Test Co	puppet-test-d3hlip	activo	6	1	2026-07-03 21:38:00	2026-07-03 21:38:03	\N	MXN	t	f	f	{"hotelTipo":null,"hotelHabitaciones":null,"hotelAmenidades":[],"restTipo":null,"restMesas":null,"restCanales":[],"almacenTipo":null,"almacenSkus":null,"almacenOps":[],"farmTipo":null,"farmAtencion":[],"farmEspecialidades":[],"startupEtapa":null,"startupModelo":null,"startupMetricas":[],"tiendaTipo":null,"tiendaCanales":[]}	t	\N	\N	\N	\N	\N
19	Puppet Test Co	puppet-test-i2SUod	activo	6	1	2026-07-03 21:39:43	2026-07-03 21:39:45	\N	MXN	t	f	f	{"hotelTipo":null,"hotelHabitaciones":null,"hotelAmenidades":[],"restTipo":null,"restMesas":null,"restCanales":[],"almacenTipo":null,"almacenSkus":null,"almacenOps":[],"farmTipo":null,"farmAtencion":[],"farmEspecialidades":[],"startupEtapa":null,"startupModelo":null,"startupMetricas":[],"tiendaTipo":null,"tiendaCanales":[]}	t	\N	\N	\N	\N	\N
20	Pipe Test Co	pipe-test-1eMS0p	activo	6	1	2026-07-03 21:50:47	2026-07-03 21:50:50	\N	MXN	t	f	f	{"hotelTipo":null,"hotelHabitaciones":null,"hotelAmenidades":[],"restTipo":null,"restMesas":null,"restCanales":[],"almacenTipo":null,"almacenSkus":null,"almacenOps":[],"farmTipo":null,"farmAtencion":[],"farmEspecialidades":[],"startupEtapa":null,"startupModelo":null,"startupMetricas":[],"tiendaTipo":null,"tiendaCanales":[]}	t	\N	\N	\N	\N	\N
21	Cierre Test Company	cierre-test-MzbAxs	activo	\N	1	2026-07-03 22:17:34	2026-07-03 22:17:34	\N	\N	t	f	f	\N	f	\N	\N	\N	\N	\N
22	Cierre Test Co	cierre-test-pGoioi	activo	6	1	2026-07-03 22:24:44	2026-07-03 22:24:47	\N	MXN	t	f	f	{"hotelTipo":null,"hotelHabitaciones":null,"hotelAmenidades":[],"restTipo":null,"restMesas":null,"restCanales":[],"almacenTipo":null,"almacenSkus":null,"almacenOps":[],"farmTipo":null,"farmAtencion":[],"farmEspecialidades":[],"startupEtapa":null,"startupModelo":null,"startupMetricas":[],"tiendaTipo":null,"tiendaCanales":[]}	t	\N	\N	\N	\N	\N
23	hggh	dfgdf-d-qMpsBa	activo	7	1	2026-07-08 21:38:20	2026-07-08 21:39:28	\N	MXN	t	t	t	{"hotelTipo":null,"hotelHabitaciones":null,"hotelAmenidades":[],"restTipo":null,"restMesas":null,"restCanales":[],"almacenTipo":null,"almacenSkus":null,"almacenOps":[],"farmTipo":null,"farmAtencion":[],"farmEspecialidades":[],"startupEtapa":null,"startupModelo":null,"startupMetricas":[],"tiendaTipo":"Abarrotes \\/ Minisuper","tiendaCanales":["ecommerce","fisica","whatsapp","delivery","mayoreo"]}	t	\N	\N	\N	\N	\N
24	tsdef	manolo-kdfjj-DRVPuM	activo	7	1	2026-07-08 21:53:51	2026-07-14 22:38:28	2026-07-14 22:38:28	MXN	t	t	t	{"hotelTipo":null,"hotelHabitaciones":null,"hotelAmenidades":[],"restTipo":null,"restMesas":null,"restCanales":[],"almacenTipo":null,"almacenSkus":null,"almacenOps":[],"farmTipo":null,"farmAtencion":[],"farmEspecialidades":[],"startupEtapa":null,"startupModelo":null,"startupMetricas":[],"tiendaTipo":"Boutique \\/ Accesorios","tiendaCanales":["fisica"]}	t	\N	\N	\N	\N	\N
26	fsd fsd Company	fsd-fsd-3Ul5WC	activo	\N	1	2026-07-15 20:15:03	2026-07-20 22:20:29	\N	\N	t	f	f	\N	f	\N	\N	\N	\N	\N
32	Hotel Prueba Company	hotel-prueba-xL0w8T	activo	2	1	2026-07-21 17:36:30	2026-07-21 21:52:30	\N	\N	t	t	f	\N	t	\N	\N	\N	\N	\N
33	Farmacia Prueba Company	farmacia-prueba-EhUIWS	activo	5	1	2026-07-21 17:36:31	2026-07-21 21:52:30	\N	\N	t	t	f	\N	t	\N	\N	\N	\N	\N
34	Almacen Prueba Company	almacen-prueba-Bfgr6c	activo	4	1	2026-07-21 18:47:40	2026-07-21 18:47:45	\N	\N	t	f	f	\N	t	\N	\N	\N	\N	\N
36	israel flores quintos Company	israel-flores-quintos-CPZ4BM	activo	\N	1	2026-07-22 23:00:40	2026-07-22 23:00:40	\N	\N	t	f	f	\N	f	\N	\N	\N	\N	\N
31	Restaurante Prueba Company	restaurante-prueba-oTU19e	activo	3	1	2026-07-21 16:46:36	2026-07-28 23:15:05	\N	MXN	t	t	t	\N	t	\N	es	America/Mexico_City	\N	\N
40	dff	iflores-zsJWuY	activo	7	1	2026-07-31 18:46:37	2026-07-31 18:46:57	\N	MXN	t	t	t	{"hotelHabitaciones":null,"restMesas":null}	t	\N	\N	\N	\N	\N
41	Google Test Company	google-test-eL5GGx	activo	\N	1	2026-08-06 20:55:39	2026-08-06 20:55:39	\N	\N	t	f	f	\N	f	\N	\N	\N	\N	\N
37	Hotel Renombrado SA	hotel-onboarding-test-GNCvPh	activo	2	1	2026-07-23 22:25:03	2026-07-23 22:26:46	\N	MXN	t	t	f	{"hotelHabitaciones":5}	t	\N	\N	\N	logos/uHvyDQ9TPdXUTcSljrYBcJXocqbeNqySc4zUaBhr.png	\N
38	Demo Restaurante Company	demo-restaurante-WoBpIG	activo	\N	1	2026-07-23 22:35:44	2026-07-23 22:35:44	\N	\N	t	f	f	\N	f	\N	\N	\N	\N	\N
1	hola	strato	activo	2	1	\N	2026-07-28 23:08:45	\N	MXN	t	t	t	{"hotelTipo":"Boutique","hotelHabitaciones":"1-10 habitaciones","hotelAmenidades":["bar","piscina","eventos","estacionamiento","spa","restaurante"],"restTipo":null,"restMesas":null,"restCanales":[],"almacenTipo":null,"almacenSkus":null,"almacenOps":[],"farmTipo":null,"farmAtencion":[],"farmEspecialidades":[],"startupEtapa":null,"startupModelo":null,"startupMetricas":[],"tiendaTipo":null,"tiendaCanales":[]}	t	Retail	es	America/Mexico_City	\N	\N
39	Demo Restaurante Onboarding	demo-restaurante-RuXcx1	activo	3	1	2026-07-23 22:37:08	2026-07-23 22:37:55	\N	MXN	f	t	t	{"hotelHabitaciones":null,"restMesas":8}	t	\N	\N	\N	logos/988Sa4LBFpk2DpAJRiQQWS6u7ePWVvCuHqk1NyDE.png	\N
35	sd	israel-flores-quintos-ntfX35	activo	2	1	2026-07-22 22:52:51	2026-08-07 21:07:17	\N	MXN	t	t	t	{"hotelTipo":null,"hotelHabitaciones":"11-30 habitaciones","hotelAmenidades":["restaurante","spa","estacionamiento","eventos","piscina","bar"],"restTipo":null,"restMesas":null,"restCanales":[],"almacenTipo":null,"almacenSkus":null,"almacenOps":[],"farmTipo":null,"farmAtencion":[],"farmEspecialidades":[],"startupEtapa":null,"startupModelo":null,"startupMetricas":[],"tiendaTipo":null,"tiendaCanales":[]}	t	\N	es	America/Mexico_City	\N	cus_UxpzgLPWJMY1MK
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.users (id, name, email, email_verified_at, password, remember_token, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: usuario; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.usuario (id_usr, email, password, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: usuario_rol; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.usuario_rol (id_usuario_rol, id_usuario, id_tenant, id_rol, asignado_por, asignado_en, created_at, updated_at) FROM stdin;
2	7	1	2	\N	2026-07-09 17:18:05	2026-07-09 17:18:05	2026-07-09 17:18:05
3	6	2	3	\N	2026-07-09 17:18:05	2026-07-09 17:18:05	2026-07-09 17:18:05
4	8	4	4	\N	2026-07-09 17:18:05	2026-07-09 17:18:05	2026-07-09 17:18:05
5	9	5	5	\N	2026-07-09 17:18:05	2026-07-09 17:18:05	2026-07-09 17:18:05
6	10	6	6	\N	2026-07-09 17:18:05	2026-07-09 17:18:05	2026-07-09 17:18:05
7	11	7	7	\N	2026-07-09 17:18:05	2026-07-09 17:18:05	2026-07-09 17:18:05
8	12	8	8	\N	2026-07-09 17:18:05	2026-07-09 17:18:05	2026-07-09 17:18:05
9	13	9	9	\N	2026-07-09 17:18:05	2026-07-09 17:18:05	2026-07-09 17:18:05
10	14	10	10	\N	2026-07-09 17:18:05	2026-07-09 17:18:05	2026-07-09 17:18:05
11	15	11	11	\N	2026-07-09 17:18:05	2026-07-09 17:18:05	2026-07-09 17:18:05
12	16	12	12	\N	2026-07-09 17:18:05	2026-07-09 17:18:05	2026-07-09 17:18:05
13	17	13	13	\N	2026-07-09 17:18:05	2026-07-09 17:18:05	2026-07-09 17:18:05
14	18	14	14	\N	2026-07-09 17:18:05	2026-07-09 17:18:05	2026-07-09 17:18:05
15	19	15	15	\N	2026-07-09 17:18:05	2026-07-09 17:18:05	2026-07-09 17:18:05
16	20	16	16	\N	2026-07-09 17:18:05	2026-07-09 17:18:05	2026-07-09 17:18:05
17	21	17	17	\N	2026-07-09 17:18:05	2026-07-09 17:18:05	2026-07-09 17:18:05
18	22	18	18	\N	2026-07-09 17:18:05	2026-07-09 17:18:05	2026-07-09 17:18:05
19	23	19	19	\N	2026-07-09 17:18:05	2026-07-09 17:18:05	2026-07-09 17:18:05
20	24	20	20	\N	2026-07-09 17:18:05	2026-07-09 17:18:05	2026-07-09 17:18:05
21	25	21	21	\N	2026-07-09 17:18:05	2026-07-09 17:18:05	2026-07-09 17:18:05
22	26	22	22	\N	2026-07-09 17:18:05	2026-07-09 17:18:05	2026-07-09 17:18:05
23	27	23	23	\N	2026-07-09 17:18:05	2026-07-09 17:18:05	2026-07-09 17:18:05
24	28	24	24	\N	2026-07-09 17:18:05	2026-07-09 17:18:05	2026-07-09 17:18:05
27	32	26	26	\N	2026-07-15 20:15:04	2026-07-15 20:15:04	2026-07-15 20:15:04
29	2	1	27	\N	2026-07-16 22:24:08	2026-07-16 22:24:08	2026-07-16 22:24:08
30	6	2	28	\N	2026-07-16 22:29:53	2026-07-16 22:29:53	2026-07-16 22:29:53
37	39	31	56	\N	2026-07-21 16:46:36	2026-07-21 16:46:36	2026-07-21 16:46:36
38	40	32	57	\N	2026-07-21 17:36:31	2026-07-21 17:36:31	2026-07-21 17:36:31
39	41	33	58	\N	2026-07-21 17:36:31	2026-07-21 17:36:31	2026-07-21 17:36:31
40	42	34	59	\N	2026-07-21 18:47:40	2026-07-21 18:47:40	2026-07-21 18:47:40
41	43	35	60	\N	2026-07-22 22:52:51	2026-07-22 22:52:51	2026-07-22 22:52:51
42	44	36	61	\N	2026-07-22 23:00:40	2026-07-22 23:00:40	2026-07-22 23:00:40
43	45	37	62	\N	2026-07-23 22:25:03	2026-07-23 22:25:03	2026-07-23 22:25:03
44	46	38	63	\N	2026-07-23 22:35:44	2026-07-23 22:35:44	2026-07-23 22:35:44
45	47	39	64	\N	2026-07-23 22:37:08	2026-07-23 22:37:08	2026-07-23 22:37:08
46	44	35	65	43	2026-07-27 22:04:57	2026-07-27 22:04:57	2026-07-27 22:04:57
47	48	35	65	43	2026-07-27 22:28:51	2026-07-27 22:28:51	2026-07-27 22:28:51
48	49	35	66	43	2026-07-27 22:54:38	2026-07-27 22:54:38	2026-07-27 22:54:38
49	50	31	67	39	2026-07-28 23:14:56	2026-07-28 23:14:56	2026-07-28 23:14:56
50	51	40	68	\N	2026-07-31 18:46:38	2026-07-31 18:46:38	2026-07-31 18:46:38
51	52	41	69	\N	2026-08-06 20:55:39	2026-08-06 20:55:39	2026-08-06 20:55:39
\.


--
-- Data for Name: usuario_rol_global; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.usuario_rol_global (id_usuario_rol_global, id_usuario, id_rol, asignado_por, asignado_en, created_at, updated_at) FROM stdin;
1	7	1	\N	2026-07-09 17:18:05	2026-07-09 17:18:05	2026-07-09 17:18:05
\.


--
-- Data for Name: usuarios; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.usuarios (id_usuario, id_tenant, nombre, email, password, created_at, updated_at, es_admin, es_superadmin, foto_perfil, estado, pin) FROM stdin;
2	1	d sd	a@s	$2y$12$aEKdsUJ/.Rqmr3ImLowpfO5Ul6KOCrD8YEgLpj.KYei9db22ijpQu	2026-07-01 22:13:15	2026-07-09 17:40:07	f	f	\N	activo	\N
6	2	Smoke Test	smoketest_1783093127@test.com	$2y$12$Gqy/jli4418WFuWxbzqer.mVEY6PKWKRzDi/Mb1V9alp9IALqk4rK	2026-07-03 15:38:47	2026-07-03 15:38:47	t	f	\N	activo	\N
7	1	Admin Demo	admin@demo.com	$2y$12$W9DTx53XeLZmEeOZTYsD7ukTYzhw6Q3lDuBhwByvYA3c3o9Fr5P82	2026-07-03 18:23:58	2026-07-03 18:23:58	t	t	\N	activo	\N
8	4	Nicho Test	nichotest_1783103060@test.com	$2y$12$S0gvo4uL5uyiFPyiFERH8.g4.c6Q6dtAZv.snhYE95EY9zBzpvLmK	2026-07-03 18:24:21	2026-07-03 18:24:21	t	f	\N	activo	\N
10	6	Repro Test	repro_1783104276651@test.com	$2y$12$aBVk6Q3jIILXxhkxKSf/9.ky6V79jCdkZjZblqsUl6gWBut7VyPHC	2026-07-03 18:44:38	2026-07-03 18:44:38	t	f	\N	activo	\N
11	7	Repro Test	repro_1783104341053@test.com	$2y$12$AQZEuFzNP726iRkl6bO8O.Cz38tGA4aKnY03/waDGvD3iZSgKtxva	2026-07-03 18:45:42	2026-07-03 18:45:42	t	f	\N	activo	\N
12	8	Repro Test	repro_1783104389259@test.com	$2y$12$jMqKuZ85qn2mDt4kk2Hk3.Mr6mUMQRgOIJDIfOcceQcc/dlYV0dnu	2026-07-03 18:46:30	2026-07-03 18:46:30	t	f	\N	activo	\N
13	9	Repro Test	repro_1783104450012@test.com	$2y$12$pw/Hl2xlPVjBXq7baSCTU.w3iX0ExgQetk3.8.9TJQ1m9MfzMwjGq	2026-07-03 18:47:31	2026-07-03 18:47:31	t	f	\N	activo	\N
14	10	Repro Test	repro_1783104586920@test.com	$2y$12$zqL5HfWuroLdlQYuYlHnH.GSntzOEswujvvcrMaMWgAqOowOyAs0.	2026-07-03 18:49:47	2026-07-03 18:49:47	t	f	\N	activo	\N
15	11	Repro Test	repro_1783104937388@test.com	$2y$12$Q8d3X8yJLwX7ctoBaVqTPev.qp.EC6pzjDupo4dQSHZxOSeRvSG3q	2026-07-03 18:55:40	2026-07-03 18:55:40	t	f	\N	activo	\N
16	12	Repro Test	repro_1783105053265@test.com	$2y$12$9Xx5p9i0pOO081egriYPe.dyh3dVrfJWjabF/gb3oaFxYT8AjEhbq	2026-07-03 18:57:35	2026-07-03 18:57:35	t	f	\N	activo	\N
17	13	Repro Test	repro_1783105165962@test.com	$2y$12$eLSSAwKogrvrztQpIGaNceDPOLKrOKofc4R.rapaszT69dZ8K9if6	2026-07-03 18:59:27	2026-07-03 18:59:27	t	f	\N	activo	\N
18	14	Cli Test	clitest_1783110763@test.com	$2y$12$lm7FzWwVQNAfpToJqg29Luy5oJG4mE6HxIhnscfoJUry8x1iYWaxG	2026-07-03 20:32:44	2026-07-03 20:32:44	t	f	\N	activo	\N
19	15	Lead Test	leadtest_1783114297@test.com	$2y$12$v/5KYwS1dQY9f8IK5U9prOMcZkzXPR1ageTQwNOqZMWmSBAI.EUea	2026-07-03 21:31:37	2026-07-03 21:31:37	t	f	\N	activo	\N
20	16	Lead Test 2	leadtest2_1783114312@test.com	$2y$12$l6kOakSZn8w..v1QDIClZew/y09fDWTjM4GI7fE5zMdyhfYyqqCPW	2026-07-03 21:31:52	2026-07-03 21:31:52	t	f	\N	activo	\N
21	17	Puppet Test	puppet_lead_1783114565385@test.com	$2y$12$mDd9z68onkdob4Q9vMSZs.ev0fQGu3OciGTuelKnQZ9PK0RUEQohu	2026-07-03 21:36:13	2026-07-03 21:36:13	t	f	\N	activo	\N
22	18	Puppet Test	puppet_lead_1783114672860@test.com	$2y$12$LAZfRj4NvzsXfkA..Y3yTuTSmLc0NnQeoZl0oPpXycq2BbZIpMKNq	2026-07-03 21:38:01	2026-07-03 21:38:01	t	f	\N	activo	\N
23	19	Puppet Test	puppet_lead_1783114778090@test.com	$2y$12$gq.rnPz4mrhcT9gf8.jwwuCU8HIt4C/ibqbis3ScFx/8h3fSfgzqu	2026-07-03 21:39:43	2026-07-03 21:39:43	t	f	\N	activo	\N
24	20	Pipe Test	puppet_pipe_1783115437096@test.com	$2y$12$eGe6yzhUyjYOlemwXGsirOb1RYvAAMJzmmbZ9rxHho23KzC9ctaj.	2026-07-03 21:50:48	2026-07-03 21:50:48	t	f	\N	activo	\N
25	21	Cierre Test	cierre_test_1783117053@test.com	$2y$12$1pj5UzKzyFsGZxpQffYclONkTAOok2N7gtHIjYuqXJOLCoSB2bUPa	2026-07-03 22:17:34	2026-07-03 22:17:34	t	f	\N	activo	\N
26	22	Cierre Test	puppet_cierre_1783117476344@test.com	$2y$12$lg2oS4k6doyMcgrht53tk.uaEgyOjCiwH8OgPmsrSDOIuww57mE6C	2026-07-03 22:24:45	2026-07-03 22:24:45	t	f	\N	activo	\N
27	23	dfgdf d	sdfsdf@hdh.com	$2y$12$VgDpB4jExmVjxaQL4vEV2eSio7PKvv/MqnrHB0ToTSQ/aDnxKVl22	2026-07-08 21:38:20	2026-07-08 21:38:20	t	f	\N	activo	\N
28	24	manolo kdfjj	isuiabgdh@hdh.com	$2y$12$Y6dmqWJvjxd8dXqW/dYPKOWzycmFhh7PF6wnCJfTdbHGJ3/AnaVEu	2026-07-08 21:53:52	2026-07-08 21:53:52	t	f	\N	activo	\N
32	26	fsd fsd	fsd@fsd.com	$2y$12$YyzG0uTFphNSGykbiQmx0e9PhZAqxHAccOs9oztBlE0cJ8iLVLagu	2026-07-15 20:15:04	2026-07-15 20:15:04	f	f	\N	activo	\N
42	34	Almacen Prueba	almacen-prueba@test.com	$2y$12$R4TtyFEaBZWVq1cAOLpKwOBXlQWUBkC/32Xsu73YGp9uCCnu9NAnO	2026-07-21 18:47:40	2026-07-21 18:47:40	f	f	\N	activo	\N
43	35	israel flores Quintos	israel.2014flores@gmail.com	$2y$12$VSJ/XhmfDYkXs7s6alg9y.59AGRmeI/TQmwh1nAl4QR921ILQerpS	2026-07-22 22:52:51	2026-07-22 22:52:51	f	f	\N	activo	\N
41	33	Farmacia Prueba	farmacia-prueba@test.com	$2y$12$2Z8qymrBiNatUdvBYO.dyeRG/YjP4w4YtFqFnnGLZlpOOJ/lOqony	2026-07-21 17:36:31	2026-07-28 23:10:48	f	f	\N	activo	\N
50	31	david	\N	$2y$12$9JY0AaWStjdk1pCYwY./hOxukH6JIcNM1cjkRkQmQGQoAByh4I.pi	2026-07-28 23:14:55	2026-07-28 23:14:55	f	f	\N	activo	$2y$12$aPC3wR/1Dw8o3XwSxVZTTuFeQxWfkbBlefsM561hcx9pcgUuw9tyK
52	41	Google Test	google-test-1786049739@example.com	$2y$12$UDSjKZ6Vb.1vze3CBkk3NOu5l7pvsuIYplNHepWMRCELBmEVeM3Ji	2026-08-06 20:55:39	2026-08-06 20:55:39	f	f	\N	activo	\N
9	5	Device Test	devicetest_1783103274@test.com	$2y$12$ZG7C5U16y8Y5U3nuhSARmOJUo2lalaaSp9I2MvGS8yzsSSsEiUDGK	2026-07-03 18:27:54	2026-07-23 20:53:11	t	f	\N	activo	\N
45	37	Hotel Onboarding Test	hotel-onb-test@test.com	$2y$12$8YCBX5xSjmL7T3R8xWMPc.aoBZDuNY1t9/sLW2xja6FJagqHvQfSS	2026-07-23 22:25:03	2026-07-23 22:26:14	f	f	\N	activo	\N
46	38	Demo Restaurante	demo-restaurante-1784846138843@test.com	$2y$12$lErAYsQnno9RJXM.B3QbAORyQl1mwR8TV6QuPnKK/u.qp/34pIWPq	2026-07-23 22:35:44	2026-07-23 22:35:44	f	f	\N	activo	\N
47	39	Demo Restaurante	demo-restaurante-1784846224599@test.com	$2y$12$m7mz7G8YcKDWQwbRoANetOIDJAjpDn/rxlvKLLHezNgf4YsJ6J.o6	2026-07-23 22:37:08	2026-07-23 22:37:08	f	f	\N	activo	\N
49	35	josmar	\N	$2y$12$TfdPiUu5jLRPHOD951uGAOgGofwPdVaaBjQFegZBlhZBEoxG9yFNa	2026-07-27 22:54:37	2026-07-27 22:56:41	f	f	\N	activo	$2y$12$p7klrVwTUwA0na44KCdk9uup5Dn1wxqepimmsurR.zdLWWbdfdC6m
44	36	israel 2	israelkintos201620@gmail.com	$2y$12$iZT9.YfthTSluxu7sT6e0O/igQ7xzWNbyJbs8lqdyQeOKK5KO9o5a	2026-07-22 23:00:40	2026-07-27 22:29:41	f	f	\N	activo	\N
48	35	israel 23	iflores@ferry.com	$2y$12$NaKJYIP9cRNsTiDoWo2pJOBTsJdHNGywX2lYU39e4Y1INoOCjH7IG	2026-07-27 22:28:51	2026-07-27 22:29:54	f	f	\N	activo	\N
39	31	Restaurante Prueba	restaurante-prueba@test.com	$2y$12$8.mXIyxBRtsWtyyQ/56Ahu6MQ3kOlSyct3aot9UheiNmLZ1OumIRW	2026-07-21 16:46:36	2026-07-28 23:10:47	f	f	\N	activo	\N
40	32	Hotel Prueba	hotel-prueba@test.com	$2y$12$b8cxbWBe8unP0oEEdl6ABOvbfyrcjIaku0PhhloENKfyFiDnHvdee	2026-07-21 17:36:31	2026-07-28 23:10:47	f	f	\N	activo	\N
51	40	iflores 	iflores@transcaribe.mx	$2y$12$0VJP5rkIIh69VzNYWYNaaeUpLJdUIH54YuLw56Be0uSQbX3UwI2kq	2026-07-31 18:46:38	2026-07-31 18:46:38	f	f	\N	activo	\N
\.


--
-- Name: actividades_id_actividad_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.actividades_id_actividad_seq', 13, true);


--
-- Name: automatizaciones_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.automatizaciones_id_seq', 1, true);


--
-- Name: campana_cliente_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.campana_cliente_id_seq', 1, true);


--
-- Name: campanas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.campanas_id_seq', 1, true);


--
-- Name: campanas_marketing_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.campanas_marketing_id_seq', 2, true);


--
-- Name: categorias_id_categoria_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.categorias_id_categoria_seq', 11, true);


--
-- Name: clientes_id_cliente_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.clientes_id_cliente_seq', 21, true);


--
-- Name: contactos_id_contacto_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.contactos_id_contacto_seq', 2, true);


--
-- Name: erp_asiento_detalles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.erp_asiento_detalles_id_seq', 65, true);


--
-- Name: erp_asientos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.erp_asientos_id_seq', 23, true);


--
-- Name: erp_comanda_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.erp_comanda_items_id_seq', 42, true);


--
-- Name: erp_comandas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.erp_comandas_id_seq', 42, true);


--
-- Name: erp_empleados_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.erp_empleados_id_seq', 5, true);


--
-- Name: erp_envios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.erp_envios_id_seq', 2, true);


--
-- Name: erp_habitacion_consumos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.erp_habitacion_consumos_id_seq', 10, true);


--
-- Name: erp_habitaciones_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.erp_habitaciones_id_seq', 10, true);


--
-- Name: erp_mesas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.erp_mesas_id_seq', 45, true);


--
-- Name: erp_movimientos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.erp_movimientos_id_seq', 5, true);


--
-- Name: erp_movimientos_stock_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.erp_movimientos_stock_id_seq', 47, true);


--
-- Name: erp_nomina_pago_detalles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.erp_nomina_pago_detalles_id_seq', 2, true);


--
-- Name: erp_nomina_pagos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.erp_nomina_pagos_id_seq', 2, true);


--
-- Name: erp_orden_compra_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.erp_orden_compra_items_id_seq', 2, true);


--
-- Name: erp_ordenes_compra_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.erp_ordenes_compra_id_seq', 2, true);


--
-- Name: erp_ordenes_produccion_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.erp_ordenes_produccion_id_seq', 1, true);


--
-- Name: erp_pedido_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.erp_pedido_items_id_seq', 40, true);


--
-- Name: erp_pedido_pagos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.erp_pedido_pagos_id_seq', 13, true);


--
-- Name: erp_pedidos_venta_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.erp_pedidos_venta_id_seq', 29, true);


--
-- Name: erp_plan_cuentas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.erp_plan_cuentas_id_seq', 665, true);


--
-- Name: erp_proyecto_horas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.erp_proyecto_horas_id_seq', 1, true);


--
-- Name: erp_proyecto_tareas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.erp_proyecto_tareas_id_seq', 9, true);


--
-- Name: erp_proyectos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.erp_proyectos_id_seq', 2, true);


--
-- Name: erp_recetas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.erp_recetas_id_seq', 4, true);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, true);


--
-- Name: integraciones_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.integraciones_id_seq', 140, true);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, true);


--
-- Name: leads_id_lead_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.leads_id_lead_seq', 11, true);


--
-- Name: membresias_id_membresia_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.membresias_id_membresia_seq', 49, true);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.migrations_id_seq', 92, true);


--
-- Name: modulos_id_modulo_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.modulos_id_modulo_seq', 3, true);


--
-- Name: negocios_id_tiponegocio_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.negocios_id_tiponegocio_seq', 7, true);


--
-- Name: notificaciones_id_notificacion_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.notificaciones_id_notificacion_seq', 18, true);


--
-- Name: oportunidades_id_oportunidad_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.oportunidades_id_oportunidad_seq', 12, true);


--
-- Name: permisos_id_permiso_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.permisos_id_permiso_seq', 80, true);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.personal_access_tokens_id_seq', 341, true);


--
-- Name: pipelines_id_pipeline_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.pipelines_id_pipeline_seq', 72, true);


--
-- Name: plans_id_plan_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.plans_id_plan_seq', 4, true);


--
-- Name: productos_id_productos_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.productos_id_productos_seq', 21, true);


--
-- Name: proveedores_id_proveedor_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.proveedores_id_proveedor_seq', 5, true);


--
-- Name: roles_id_rol_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.roles_id_rol_seq', 69, true);


--
-- Name: stripe_webhook_events_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.stripe_webhook_events_id_seq', 7, true);


--
-- Name: suscripciones_id_suscripcion_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.suscripciones_id_suscripcion_seq', 1, true);


--
-- Name: tenants_id_tenant_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.tenants_id_tenant_seq', 41, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.users_id_seq', 1, true);


--
-- Name: usuario_id_usr_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.usuario_id_usr_seq', 1, true);


--
-- Name: usuario_rol_global_id_usuario_rol_global_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.usuario_rol_global_id_usuario_rol_global_seq', 1, true);


--
-- Name: usuario_rol_id_usuario_rol_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.usuario_rol_id_usuario_rol_seq', 51, true);


--
-- Name: usuarios_id_usuario_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.usuarios_id_usuario_seq', 52, true);


--
-- Name: actividades actividades_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.actividades
    ADD CONSTRAINT actividades_pkey PRIMARY KEY (id_actividad);


--
-- Name: automatizaciones automatizaciones_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.automatizaciones
    ADD CONSTRAINT automatizaciones_pkey PRIMARY KEY (id);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: campana_cliente campana_cliente_id_campana_id_cliente_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.campana_cliente
    ADD CONSTRAINT campana_cliente_id_campana_id_cliente_unique UNIQUE (id_campana, id_cliente);


--
-- Name: campana_cliente campana_cliente_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.campana_cliente
    ADD CONSTRAINT campana_cliente_pkey PRIMARY KEY (id);


--
-- Name: campanas_marketing campanas_marketing_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.campanas_marketing
    ADD CONSTRAINT campanas_marketing_pkey PRIMARY KEY (id);


--
-- Name: campanas campanas_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.campanas
    ADD CONSTRAINT campanas_pkey PRIMARY KEY (id);


--
-- Name: categorias categorias_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.categorias
    ADD CONSTRAINT categorias_pkey PRIMARY KEY (id_categoria);


--
-- Name: clientes clientes_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.clientes
    ADD CONSTRAINT clientes_pkey PRIMARY KEY (id_cliente);


--
-- Name: contactos contactos_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.contactos
    ADD CONSTRAINT contactos_pkey PRIMARY KEY (id_contacto);


--
-- Name: erp_asiento_detalles erp_asiento_detalles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_asiento_detalles
    ADD CONSTRAINT erp_asiento_detalles_pkey PRIMARY KEY (id);


--
-- Name: erp_asientos erp_asientos_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_asientos
    ADD CONSTRAINT erp_asientos_pkey PRIMARY KEY (id);


--
-- Name: erp_comanda_items erp_comanda_items_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_comanda_items
    ADD CONSTRAINT erp_comanda_items_pkey PRIMARY KEY (id);


--
-- Name: erp_comandas erp_comandas_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_comandas
    ADD CONSTRAINT erp_comandas_pkey PRIMARY KEY (id);


--
-- Name: erp_empleados erp_empleados_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_empleados
    ADD CONSTRAINT erp_empleados_pkey PRIMARY KEY (id);


--
-- Name: erp_envios erp_envios_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_envios
    ADD CONSTRAINT erp_envios_pkey PRIMARY KEY (id);


--
-- Name: erp_habitacion_consumos erp_habitacion_consumos_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_habitacion_consumos
    ADD CONSTRAINT erp_habitacion_consumos_pkey PRIMARY KEY (id);


--
-- Name: erp_habitaciones erp_habitaciones_id_tenant_numero_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_habitaciones
    ADD CONSTRAINT erp_habitaciones_id_tenant_numero_unique UNIQUE (id_tenant, numero);


--
-- Name: erp_habitaciones erp_habitaciones_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_habitaciones
    ADD CONSTRAINT erp_habitaciones_pkey PRIMARY KEY (id);


--
-- Name: erp_mesas erp_mesas_id_tenant_numero_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_mesas
    ADD CONSTRAINT erp_mesas_id_tenant_numero_unique UNIQUE (id_tenant, numero);


--
-- Name: erp_mesas erp_mesas_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_mesas
    ADD CONSTRAINT erp_mesas_pkey PRIMARY KEY (id);


--
-- Name: erp_movimientos erp_movimientos_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_movimientos
    ADD CONSTRAINT erp_movimientos_pkey PRIMARY KEY (id);


--
-- Name: erp_movimientos_stock erp_movimientos_stock_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_movimientos_stock
    ADD CONSTRAINT erp_movimientos_stock_pkey PRIMARY KEY (id);


--
-- Name: erp_nomina_pago_detalles erp_nomina_pago_detalles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_nomina_pago_detalles
    ADD CONSTRAINT erp_nomina_pago_detalles_pkey PRIMARY KEY (id);


--
-- Name: erp_nomina_pagos erp_nomina_pagos_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_nomina_pagos
    ADD CONSTRAINT erp_nomina_pagos_pkey PRIMARY KEY (id);


--
-- Name: erp_orden_compra_items erp_orden_compra_items_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_orden_compra_items
    ADD CONSTRAINT erp_orden_compra_items_pkey PRIMARY KEY (id);


--
-- Name: erp_ordenes_compra erp_ordenes_compra_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_ordenes_compra
    ADD CONSTRAINT erp_ordenes_compra_pkey PRIMARY KEY (id);


--
-- Name: erp_ordenes_produccion erp_ordenes_produccion_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_ordenes_produccion
    ADD CONSTRAINT erp_ordenes_produccion_pkey PRIMARY KEY (id);


--
-- Name: erp_pedido_items erp_pedido_items_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_pedido_items
    ADD CONSTRAINT erp_pedido_items_pkey PRIMARY KEY (id);


--
-- Name: erp_pedido_pagos erp_pedido_pagos_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_pedido_pagos
    ADD CONSTRAINT erp_pedido_pagos_pkey PRIMARY KEY (id);


--
-- Name: erp_pedidos_venta erp_pedidos_venta_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_pedidos_venta
    ADD CONSTRAINT erp_pedidos_venta_pkey PRIMARY KEY (id);


--
-- Name: erp_plan_cuentas erp_plan_cuentas_id_tenant_codigo_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_plan_cuentas
    ADD CONSTRAINT erp_plan_cuentas_id_tenant_codigo_unique UNIQUE (id_tenant, codigo);


--
-- Name: erp_plan_cuentas erp_plan_cuentas_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_plan_cuentas
    ADD CONSTRAINT erp_plan_cuentas_pkey PRIMARY KEY (id);


--
-- Name: erp_proyecto_horas erp_proyecto_horas_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_proyecto_horas
    ADD CONSTRAINT erp_proyecto_horas_pkey PRIMARY KEY (id);


--
-- Name: erp_proyecto_tareas erp_proyecto_tareas_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_proyecto_tareas
    ADD CONSTRAINT erp_proyecto_tareas_pkey PRIMARY KEY (id);


--
-- Name: erp_proyectos erp_proyectos_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_proyectos
    ADD CONSTRAINT erp_proyectos_pkey PRIMARY KEY (id);


--
-- Name: erp_recetas erp_recetas_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_recetas
    ADD CONSTRAINT erp_recetas_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: integraciones integraciones_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.integraciones
    ADD CONSTRAINT integraciones_pkey PRIMARY KEY (id);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: leads leads_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.leads
    ADD CONSTRAINT leads_pkey PRIMARY KEY (id_lead);


--
-- Name: membresias membresias_id_usuario_id_tenant_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.membresias
    ADD CONSTRAINT membresias_id_usuario_id_tenant_unique UNIQUE (id_usuario, id_tenant);


--
-- Name: membresias membresias_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.membresias
    ADD CONSTRAINT membresias_pkey PRIMARY KEY (id_membresia);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: modulos modulos_clave_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.modulos
    ADD CONSTRAINT modulos_clave_unique UNIQUE (clave);


--
-- Name: modulos modulos_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.modulos
    ADD CONSTRAINT modulos_pkey PRIMARY KEY (id_modulo);


--
-- Name: negocios negocios_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.negocios
    ADD CONSTRAINT negocios_pkey PRIMARY KEY (id_tiponegocio);


--
-- Name: negocios negocios_slug_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.negocios
    ADD CONSTRAINT negocios_slug_unique UNIQUE (slug);


--
-- Name: notificaciones notificaciones_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.notificaciones
    ADD CONSTRAINT notificaciones_pkey PRIMARY KEY (id_notificacion);


--
-- Name: oauth_access_tokens oauth_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.oauth_access_tokens
    ADD CONSTRAINT oauth_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: oauth_auth_codes oauth_auth_codes_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.oauth_auth_codes
    ADD CONSTRAINT oauth_auth_codes_pkey PRIMARY KEY (id);


--
-- Name: oauth_clients oauth_clients_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.oauth_clients
    ADD CONSTRAINT oauth_clients_pkey PRIMARY KEY (id);


--
-- Name: oauth_device_codes oauth_device_codes_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.oauth_device_codes
    ADD CONSTRAINT oauth_device_codes_pkey PRIMARY KEY (id);


--
-- Name: oauth_device_codes oauth_device_codes_user_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.oauth_device_codes
    ADD CONSTRAINT oauth_device_codes_user_code_unique UNIQUE (user_code);


--
-- Name: oauth_refresh_tokens oauth_refresh_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.oauth_refresh_tokens
    ADD CONSTRAINT oauth_refresh_tokens_pkey PRIMARY KEY (id);


--
-- Name: oportunidades oportunidades_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.oportunidades
    ADD CONSTRAINT oportunidades_pkey PRIMARY KEY (id_oportunidad);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: permisos permisos_clave_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permisos
    ADD CONSTRAINT permisos_clave_unique UNIQUE (clave);


--
-- Name: permisos permisos_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permisos
    ADD CONSTRAINT permisos_pkey PRIMARY KEY (id_permiso);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: pipelines pipelines_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pipelines
    ADD CONSTRAINT pipelines_pkey PRIMARY KEY (id_pipeline);


--
-- Name: plans plans_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.plans
    ADD CONSTRAINT plans_pkey PRIMARY KEY (id_plan);


--
-- Name: plans plans_stripe_price_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.plans
    ADD CONSTRAINT plans_stripe_price_id_unique UNIQUE (stripe_price_id);


--
-- Name: productos productos_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.productos
    ADD CONSTRAINT productos_pkey PRIMARY KEY (id_productos);


--
-- Name: proveedores proveedores_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.proveedores
    ADD CONSTRAINT proveedores_pkey PRIMARY KEY (id_proveedor);


--
-- Name: rol_permiso rol_permiso_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rol_permiso
    ADD CONSTRAINT rol_permiso_pkey PRIMARY KEY (id_rol, id_permiso);


--
-- Name: roles roles_id_tenant_clave_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_id_tenant_clave_unique UNIQUE (id_tenant, clave);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id_rol);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: stripe_webhook_events stripe_webhook_events_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stripe_webhook_events
    ADD CONSTRAINT stripe_webhook_events_pkey PRIMARY KEY (id);


--
-- Name: stripe_webhook_events stripe_webhook_events_stripe_event_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stripe_webhook_events
    ADD CONSTRAINT stripe_webhook_events_stripe_event_id_unique UNIQUE (stripe_event_id);


--
-- Name: suscripciones suscripciones_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.suscripciones
    ADD CONSTRAINT suscripciones_pkey PRIMARY KEY (id_suscripcion);


--
-- Name: suscripciones suscripciones_stripe_subscription_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.suscripciones
    ADD CONSTRAINT suscripciones_stripe_subscription_id_unique UNIQUE (stripe_subscription_id);


--
-- Name: tenants tenants_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tenants
    ADD CONSTRAINT tenants_pkey PRIMARY KEY (id_tenant);


--
-- Name: tenants tenants_stripe_customer_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tenants
    ADD CONSTRAINT tenants_stripe_customer_id_unique UNIQUE (stripe_customer_id);


--
-- Name: tenants tenants_subdominio_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tenants
    ADD CONSTRAINT tenants_subdominio_unique UNIQUE (subdominio);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: usuario usuario_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario
    ADD CONSTRAINT usuario_pkey PRIMARY KEY (id_usr);


--
-- Name: usuario_rol_global usuario_rol_global_id_usuario_id_rol_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario_rol_global
    ADD CONSTRAINT usuario_rol_global_id_usuario_id_rol_unique UNIQUE (id_usuario, id_rol);


--
-- Name: usuario_rol_global usuario_rol_global_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario_rol_global
    ADD CONSTRAINT usuario_rol_global_pkey PRIMARY KEY (id_usuario_rol_global);


--
-- Name: usuario_rol usuario_rol_id_usuario_id_tenant_id_rol_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario_rol
    ADD CONSTRAINT usuario_rol_id_usuario_id_tenant_id_rol_unique UNIQUE (id_usuario, id_tenant, id_rol);


--
-- Name: usuario_rol usuario_rol_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario_rol
    ADD CONSTRAINT usuario_rol_pkey PRIMARY KEY (id_usuario_rol);


--
-- Name: usuarios usuarios_email_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_email_unique UNIQUE (email);


--
-- Name: usuarios usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_pkey PRIMARY KEY (id_usuario);


--
-- Name: cache_expiration_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cache_expiration_index ON public.cache USING btree (expiration);


--
-- Name: cache_locks_expiration_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cache_locks_expiration_index ON public.cache_locks USING btree (expiration);


--
-- Name: erp_asientos_id_tenant_fecha_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX erp_asientos_id_tenant_fecha_index ON public.erp_asientos USING btree (id_tenant, fecha);


--
-- Name: erp_asientos_referencia_tipo_referencia_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX erp_asientos_referencia_tipo_referencia_id_index ON public.erp_asientos USING btree (referencia_tipo, referencia_id);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: oauth_access_tokens_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX oauth_access_tokens_user_id_index ON public.oauth_access_tokens USING btree (user_id);


--
-- Name: oauth_auth_codes_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX oauth_auth_codes_user_id_index ON public.oauth_auth_codes USING btree (user_id);


--
-- Name: oauth_clients_owner_type_owner_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX oauth_clients_owner_type_owner_id_index ON public.oauth_clients USING btree (owner_type, owner_id);


--
-- Name: oauth_device_codes_client_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX oauth_device_codes_client_id_index ON public.oauth_device_codes USING btree (client_id);


--
-- Name: oauth_device_codes_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX oauth_device_codes_user_id_index ON public.oauth_device_codes USING btree (user_id);


--
-- Name: oauth_refresh_tokens_access_token_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX oauth_refresh_tokens_access_token_id_index ON public.oauth_refresh_tokens USING btree (access_token_id);


--
-- Name: personal_access_tokens_expires_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX personal_access_tokens_expires_at_index ON public.personal_access_tokens USING btree (expires_at);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: suscripciones_id_tenant_estado_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX suscripciones_id_tenant_estado_index ON public.suscripciones USING btree (id_tenant, estado);


--
-- Name: actividades actividades_id_cliente_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.actividades
    ADD CONSTRAINT actividades_id_cliente_foreign FOREIGN KEY (id_cliente) REFERENCES public.clientes(id_cliente) ON DELETE SET NULL;


--
-- Name: actividades actividades_id_lead_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.actividades
    ADD CONSTRAINT actividades_id_lead_foreign FOREIGN KEY (id_lead) REFERENCES public.leads(id_lead) ON DELETE SET NULL;


--
-- Name: actividades actividades_id_oportunidad_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.actividades
    ADD CONSTRAINT actividades_id_oportunidad_foreign FOREIGN KEY (id_oportunidad) REFERENCES public.oportunidades(id_oportunidad) ON DELETE SET NULL;


--
-- Name: actividades actividades_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.actividades
    ADD CONSTRAINT actividades_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: actividades actividades_id_usuario_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.actividades
    ADD CONSTRAINT actividades_id_usuario_foreign FOREIGN KEY (id_usuario) REFERENCES public.usuarios(id_usuario) ON DELETE CASCADE;


--
-- Name: automatizaciones automatizaciones_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.automatizaciones
    ADD CONSTRAINT automatizaciones_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: campana_cliente campana_cliente_id_campana_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.campana_cliente
    ADD CONSTRAINT campana_cliente_id_campana_foreign FOREIGN KEY (id_campana) REFERENCES public.campanas(id) ON DELETE CASCADE;


--
-- Name: campana_cliente campana_cliente_id_cliente_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.campana_cliente
    ADD CONSTRAINT campana_cliente_id_cliente_foreign FOREIGN KEY (id_cliente) REFERENCES public.clientes(id_cliente) ON DELETE CASCADE;


--
-- Name: campanas campanas_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.campanas
    ADD CONSTRAINT campanas_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: campanas_marketing campanas_marketing_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.campanas_marketing
    ADD CONSTRAINT campanas_marketing_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: campanas_marketing campanas_marketing_id_usuario_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.campanas_marketing
    ADD CONSTRAINT campanas_marketing_id_usuario_foreign FOREIGN KEY (id_usuario) REFERENCES public.usuarios(id_usuario) ON DELETE CASCADE;


--
-- Name: categorias categorias_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.categorias
    ADD CONSTRAINT categorias_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: clientes clientes_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.clientes
    ADD CONSTRAINT clientes_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: contactos contactos_id_cliente_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.contactos
    ADD CONSTRAINT contactos_id_cliente_foreign FOREIGN KEY (id_cliente) REFERENCES public.clientes(id_cliente) ON DELETE CASCADE;


--
-- Name: contactos contactos_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.contactos
    ADD CONSTRAINT contactos_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: erp_asiento_detalles erp_asiento_detalles_id_asiento_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_asiento_detalles
    ADD CONSTRAINT erp_asiento_detalles_id_asiento_foreign FOREIGN KEY (id_asiento) REFERENCES public.erp_asientos(id) ON DELETE CASCADE;


--
-- Name: erp_asiento_detalles erp_asiento_detalles_id_cuenta_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_asiento_detalles
    ADD CONSTRAINT erp_asiento_detalles_id_cuenta_foreign FOREIGN KEY (id_cuenta) REFERENCES public.erp_plan_cuentas(id);


--
-- Name: erp_asientos erp_asientos_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_asientos
    ADD CONSTRAINT erp_asientos_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: erp_asientos erp_asientos_id_usuario_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_asientos
    ADD CONSTRAINT erp_asientos_id_usuario_foreign FOREIGN KEY (id_usuario) REFERENCES public.usuarios(id_usuario) ON DELETE SET NULL;


--
-- Name: erp_comanda_items erp_comanda_items_id_comanda_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_comanda_items
    ADD CONSTRAINT erp_comanda_items_id_comanda_foreign FOREIGN KEY (id_comanda) REFERENCES public.erp_comandas(id) ON DELETE CASCADE;


--
-- Name: erp_comanda_items erp_comanda_items_id_producto_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_comanda_items
    ADD CONSTRAINT erp_comanda_items_id_producto_foreign FOREIGN KEY (id_producto) REFERENCES public.productos(id_productos) ON DELETE SET NULL;


--
-- Name: erp_comandas erp_comandas_id_mesa_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_comandas
    ADD CONSTRAINT erp_comandas_id_mesa_foreign FOREIGN KEY (id_mesa) REFERENCES public.erp_mesas(id) ON DELETE CASCADE;


--
-- Name: erp_comandas erp_comandas_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_comandas
    ADD CONSTRAINT erp_comandas_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: erp_empleados erp_empleados_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_empleados
    ADD CONSTRAINT erp_empleados_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: erp_envios erp_envios_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_envios
    ADD CONSTRAINT erp_envios_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: erp_habitacion_consumos erp_habitacion_consumos_id_habitacion_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_habitacion_consumos
    ADD CONSTRAINT erp_habitacion_consumos_id_habitacion_foreign FOREIGN KEY (id_habitacion) REFERENCES public.erp_habitaciones(id) ON DELETE CASCADE;


--
-- Name: erp_habitacion_consumos erp_habitacion_consumos_id_producto_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_habitacion_consumos
    ADD CONSTRAINT erp_habitacion_consumos_id_producto_foreign FOREIGN KEY (id_producto) REFERENCES public.productos(id_productos) ON DELETE SET NULL;


--
-- Name: erp_habitaciones erp_habitaciones_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_habitaciones
    ADD CONSTRAINT erp_habitaciones_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: erp_mesas erp_mesas_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_mesas
    ADD CONSTRAINT erp_mesas_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: erp_movimientos erp_movimientos_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_movimientos
    ADD CONSTRAINT erp_movimientos_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: erp_movimientos_stock erp_movimientos_stock_id_producto_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_movimientos_stock
    ADD CONSTRAINT erp_movimientos_stock_id_producto_foreign FOREIGN KEY (id_producto) REFERENCES public.productos(id_productos);


--
-- Name: erp_movimientos_stock erp_movimientos_stock_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_movimientos_stock
    ADD CONSTRAINT erp_movimientos_stock_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: erp_nomina_pago_detalles erp_nomina_pago_detalles_id_empleado_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_nomina_pago_detalles
    ADD CONSTRAINT erp_nomina_pago_detalles_id_empleado_foreign FOREIGN KEY (id_empleado) REFERENCES public.erp_empleados(id);


--
-- Name: erp_nomina_pago_detalles erp_nomina_pago_detalles_id_nomina_pago_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_nomina_pago_detalles
    ADD CONSTRAINT erp_nomina_pago_detalles_id_nomina_pago_foreign FOREIGN KEY (id_nomina_pago) REFERENCES public.erp_nomina_pagos(id) ON DELETE CASCADE;


--
-- Name: erp_nomina_pagos erp_nomina_pagos_id_asiento_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_nomina_pagos
    ADD CONSTRAINT erp_nomina_pagos_id_asiento_foreign FOREIGN KEY (id_asiento) REFERENCES public.erp_asientos(id);


--
-- Name: erp_nomina_pagos erp_nomina_pagos_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_nomina_pagos
    ADD CONSTRAINT erp_nomina_pagos_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: erp_orden_compra_items erp_orden_compra_items_id_orden_compra_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_orden_compra_items
    ADD CONSTRAINT erp_orden_compra_items_id_orden_compra_foreign FOREIGN KEY (id_orden_compra) REFERENCES public.erp_ordenes_compra(id) ON DELETE CASCADE;


--
-- Name: erp_orden_compra_items erp_orden_compra_items_id_producto_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_orden_compra_items
    ADD CONSTRAINT erp_orden_compra_items_id_producto_foreign FOREIGN KEY (id_producto) REFERENCES public.productos(id_productos);


--
-- Name: erp_ordenes_compra erp_ordenes_compra_id_proveedor_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_ordenes_compra
    ADD CONSTRAINT erp_ordenes_compra_id_proveedor_foreign FOREIGN KEY (id_proveedor) REFERENCES public.proveedores(id_proveedor);


--
-- Name: erp_ordenes_compra erp_ordenes_compra_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_ordenes_compra
    ADD CONSTRAINT erp_ordenes_compra_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: erp_ordenes_produccion erp_ordenes_produccion_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_ordenes_produccion
    ADD CONSTRAINT erp_ordenes_produccion_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: erp_pedido_items erp_pedido_items_id_pedido_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_pedido_items
    ADD CONSTRAINT erp_pedido_items_id_pedido_foreign FOREIGN KEY (id_pedido) REFERENCES public.erp_pedidos_venta(id) ON DELETE CASCADE;


--
-- Name: erp_pedido_items erp_pedido_items_id_producto_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_pedido_items
    ADD CONSTRAINT erp_pedido_items_id_producto_foreign FOREIGN KEY (id_producto) REFERENCES public.productos(id_productos);


--
-- Name: erp_pedido_pagos erp_pedido_pagos_id_pedido_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_pedido_pagos
    ADD CONSTRAINT erp_pedido_pagos_id_pedido_foreign FOREIGN KEY (id_pedido) REFERENCES public.erp_pedidos_venta(id) ON DELETE CASCADE;


--
-- Name: erp_pedidos_venta erp_pedidos_venta_id_cliente_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_pedidos_venta
    ADD CONSTRAINT erp_pedidos_venta_id_cliente_foreign FOREIGN KEY (id_cliente) REFERENCES public.clientes(id_cliente);


--
-- Name: erp_pedidos_venta erp_pedidos_venta_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_pedidos_venta
    ADD CONSTRAINT erp_pedidos_venta_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: erp_plan_cuentas erp_plan_cuentas_id_cuenta_padre_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_plan_cuentas
    ADD CONSTRAINT erp_plan_cuentas_id_cuenta_padre_foreign FOREIGN KEY (id_cuenta_padre) REFERENCES public.erp_plan_cuentas(id) ON DELETE SET NULL;


--
-- Name: erp_plan_cuentas erp_plan_cuentas_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_plan_cuentas
    ADD CONSTRAINT erp_plan_cuentas_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: erp_proyecto_horas erp_proyecto_horas_id_proyecto_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_proyecto_horas
    ADD CONSTRAINT erp_proyecto_horas_id_proyecto_foreign FOREIGN KEY (id_proyecto) REFERENCES public.erp_proyectos(id) ON DELETE CASCADE;


--
-- Name: erp_proyecto_horas erp_proyecto_horas_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_proyecto_horas
    ADD CONSTRAINT erp_proyecto_horas_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: erp_proyecto_tareas erp_proyecto_tareas_id_proyecto_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_proyecto_tareas
    ADD CONSTRAINT erp_proyecto_tareas_id_proyecto_foreign FOREIGN KEY (id_proyecto) REFERENCES public.erp_proyectos(id) ON DELETE CASCADE;


--
-- Name: erp_proyecto_tareas erp_proyecto_tareas_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_proyecto_tareas
    ADD CONSTRAINT erp_proyecto_tareas_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: erp_proyectos erp_proyectos_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_proyectos
    ADD CONSTRAINT erp_proyectos_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: erp_recetas erp_recetas_id_cliente_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_recetas
    ADD CONSTRAINT erp_recetas_id_cliente_foreign FOREIGN KEY (id_cliente) REFERENCES public.clientes(id_cliente) ON DELETE CASCADE;


--
-- Name: erp_recetas erp_recetas_id_producto_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_recetas
    ADD CONSTRAINT erp_recetas_id_producto_foreign FOREIGN KEY (id_producto) REFERENCES public.productos(id_productos);


--
-- Name: erp_recetas erp_recetas_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.erp_recetas
    ADD CONSTRAINT erp_recetas_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: integraciones integraciones_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.integraciones
    ADD CONSTRAINT integraciones_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: leads leads_id_cliente_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.leads
    ADD CONSTRAINT leads_id_cliente_foreign FOREIGN KEY (id_cliente) REFERENCES public.clientes(id_cliente) ON DELETE SET NULL;


--
-- Name: leads leads_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.leads
    ADD CONSTRAINT leads_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: leads leads_id_usuario_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.leads
    ADD CONSTRAINT leads_id_usuario_foreign FOREIGN KEY (id_usuario) REFERENCES public.usuarios(id_usuario) ON DELETE CASCADE;


--
-- Name: membresias membresias_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.membresias
    ADD CONSTRAINT membresias_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: membresias membresias_id_usuario_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.membresias
    ADD CONSTRAINT membresias_id_usuario_foreign FOREIGN KEY (id_usuario) REFERENCES public.usuarios(id_usuario) ON DELETE CASCADE;


--
-- Name: membresias membresias_invitado_por_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.membresias
    ADD CONSTRAINT membresias_invitado_por_foreign FOREIGN KEY (invitado_por) REFERENCES public.usuarios(id_usuario) ON DELETE SET NULL;


--
-- Name: notificaciones notificaciones_id_cliente_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.notificaciones
    ADD CONSTRAINT notificaciones_id_cliente_foreign FOREIGN KEY (id_cliente) REFERENCES public.clientes(id_cliente) ON DELETE SET NULL;


--
-- Name: notificaciones notificaciones_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.notificaciones
    ADD CONSTRAINT notificaciones_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: notificaciones notificaciones_id_usuario_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.notificaciones
    ADD CONSTRAINT notificaciones_id_usuario_foreign FOREIGN KEY (id_usuario) REFERENCES public.usuarios(id_usuario) ON DELETE CASCADE;


--
-- Name: oportunidades oportunidades_id_cliente_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.oportunidades
    ADD CONSTRAINT oportunidades_id_cliente_foreign FOREIGN KEY (id_cliente) REFERENCES public.clientes(id_cliente) ON DELETE CASCADE;


--
-- Name: oportunidades oportunidades_id_pipeline_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.oportunidades
    ADD CONSTRAINT oportunidades_id_pipeline_foreign FOREIGN KEY (id_pipeline) REFERENCES public.pipelines(id_pipeline) ON DELETE CASCADE;


--
-- Name: oportunidades oportunidades_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.oportunidades
    ADD CONSTRAINT oportunidades_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: oportunidades oportunidades_id_usuario_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.oportunidades
    ADD CONSTRAINT oportunidades_id_usuario_foreign FOREIGN KEY (id_usuario) REFERENCES public.usuarios(id_usuario) ON DELETE CASCADE;


--
-- Name: permisos permisos_id_modulo_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permisos
    ADD CONSTRAINT permisos_id_modulo_foreign FOREIGN KEY (id_modulo) REFERENCES public.modulos(id_modulo) ON DELETE CASCADE;


--
-- Name: pipelines pipelines_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pipelines
    ADD CONSTRAINT pipelines_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: productos productos_id_categorias_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.productos
    ADD CONSTRAINT productos_id_categorias_foreign FOREIGN KEY (id_categorias) REFERENCES public.categorias(id_categoria);


--
-- Name: productos productos_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.productos
    ADD CONSTRAINT productos_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: proveedores proveedores_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.proveedores
    ADD CONSTRAINT proveedores_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: rol_permiso rol_permiso_id_permiso_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rol_permiso
    ADD CONSTRAINT rol_permiso_id_permiso_foreign FOREIGN KEY (id_permiso) REFERENCES public.permisos(id_permiso) ON DELETE CASCADE;


--
-- Name: rol_permiso rol_permiso_id_rol_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rol_permiso
    ADD CONSTRAINT rol_permiso_id_rol_foreign FOREIGN KEY (id_rol) REFERENCES public.roles(id_rol) ON DELETE CASCADE;


--
-- Name: roles roles_id_modulo_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_id_modulo_foreign FOREIGN KEY (id_modulo) REFERENCES public.modulos(id_modulo) ON DELETE CASCADE;


--
-- Name: roles roles_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: suscripciones suscripciones_id_plan_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.suscripciones
    ADD CONSTRAINT suscripciones_id_plan_foreign FOREIGN KEY (id_plan) REFERENCES public.plans(id_plan);


--
-- Name: suscripciones suscripciones_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.suscripciones
    ADD CONSTRAINT suscripciones_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: tenants tenants_id_plan_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tenants
    ADD CONSTRAINT tenants_id_plan_foreign FOREIGN KEY (id_plan) REFERENCES public.plans(id_plan) ON DELETE CASCADE;


--
-- Name: tenants tenants_id_tiponegocio_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tenants
    ADD CONSTRAINT tenants_id_tiponegocio_foreign FOREIGN KEY (id_tiponegocio) REFERENCES public.negocios(id_tiponegocio) ON DELETE SET NULL;


--
-- Name: usuario_rol usuario_rol_asignado_por_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario_rol
    ADD CONSTRAINT usuario_rol_asignado_por_foreign FOREIGN KEY (asignado_por) REFERENCES public.usuarios(id_usuario) ON DELETE SET NULL;


--
-- Name: usuario_rol_global usuario_rol_global_asignado_por_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario_rol_global
    ADD CONSTRAINT usuario_rol_global_asignado_por_foreign FOREIGN KEY (asignado_por) REFERENCES public.usuarios(id_usuario) ON DELETE SET NULL;


--
-- Name: usuario_rol_global usuario_rol_global_id_rol_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario_rol_global
    ADD CONSTRAINT usuario_rol_global_id_rol_foreign FOREIGN KEY (id_rol) REFERENCES public.roles(id_rol) ON DELETE CASCADE;


--
-- Name: usuario_rol_global usuario_rol_global_id_usuario_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario_rol_global
    ADD CONSTRAINT usuario_rol_global_id_usuario_foreign FOREIGN KEY (id_usuario) REFERENCES public.usuarios(id_usuario) ON DELETE CASCADE;


--
-- Name: usuario_rol usuario_rol_id_rol_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario_rol
    ADD CONSTRAINT usuario_rol_id_rol_foreign FOREIGN KEY (id_rol) REFERENCES public.roles(id_rol) ON DELETE CASCADE;


--
-- Name: usuario_rol usuario_rol_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario_rol
    ADD CONSTRAINT usuario_rol_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- Name: usuario_rol usuario_rol_id_usuario_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario_rol
    ADD CONSTRAINT usuario_rol_id_usuario_foreign FOREIGN KEY (id_usuario) REFERENCES public.usuarios(id_usuario) ON DELETE CASCADE;


--
-- Name: usuarios usuarios_id_tenant_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_id_tenant_foreign FOREIGN KEY (id_tenant) REFERENCES public.tenants(id_tenant) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict AwCg5bsIiatTGw7EtL3CJUIz0ouPUFleqjYarJkpbpZ4z9Fdq3hf5lp36hCVCns

