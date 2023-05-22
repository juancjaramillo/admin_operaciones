--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'LATIN1';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: ases; Type: TABLE; Schema: public; Owner: promoambiental; Tablespace: 
--

CREATE TABLE ases (
    id integer NOT NULL,
    id_centro integer DEFAULT 0 NOT NULL,
    ase character varying(32) DEFAULT ''::character varying NOT NULL,
    nuap integer DEFAULT 0,
    fecha_entrada date DEFAULT now() NOT NULL,
    geometry geometry,
    the_geom geometry,
    CONSTRAINT enforce_dims_geometry CHECK ((st_ndims(geometry) = 2)),
    CONSTRAINT enforce_dims_the_geom CHECK ((st_ndims(the_geom) = 2)),
    CONSTRAINT enforce_geotype_geometry CHECK (((geometrytype(geometry) = 'POLYGON'::text) OR (geometry IS NULL))),
    CONSTRAINT enforce_geotype_the_geom CHECK (((geometrytype(the_geom) = 'POINT'::text) OR (the_geom IS NULL))),
    CONSTRAINT enforce_srid_geometry CHECK ((st_srid(geometry) = 4326)),
    CONSTRAINT enforce_srid_the_geom CHECK ((st_srid(the_geom) = 4326))
);


ALTER TABLE public.ases OWNER TO promoambiental;

--
-- Name: ases_id_seq; Type: SEQUENCE; Schema: public; Owner: promoambiental
--

CREATE SEQUENCE ases_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.ases_id_seq OWNER TO promoambiental;

--
-- Name: ases_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: promoambiental
--

ALTER SEQUENCE ases_id_seq OWNED BY ases.id;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: promoambiental
--

ALTER TABLE ONLY ases ALTER COLUMN id SET DEFAULT nextval('ases_id_seq'::regclass);


--
-- Name: ases_pkey; Type: CONSTRAINT; Schema: public; Owner: promoambiental; Tablespace: 
--

ALTER TABLE ONLY ases
    ADD CONSTRAINT ases_pkey PRIMARY KEY (id);


--
-- Name: ases_gidx; Type: INDEX; Schema: public; Owner: promoambiental; Tablespace: 
--

CREATE INDEX ases_gidx ON ases USING gist (geometry);


--
-- Name: ases_tg_gidx; Type: INDEX; Schema: public; Owner: promoambiental; Tablespace: 
--

CREATE INDEX ases_tg_gidx ON ases USING gist (the_geom);


--
-- Name: idx_ases_id_centro; Type: INDEX; Schema: public; Owner: promoambiental; Tablespace: 
--

CREATE INDEX idx_ases_id_centro ON ases USING btree (id_centro);


--
-- Name: ref_id_centro; Type: FK CONSTRAINT; Schema: public; Owner: promoambiental
--

ALTER TABLE ONLY ases
    ADD CONSTRAINT ref_id_centro FOREIGN KEY (id_centro) REFERENCES centros(id);


--
-- PostgreSQL database dump complete
--

