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
-- Name: gps_vehi; Type: TABLE; Schema: public; Owner: promoambiental; Tablespace: 
--

CREATE TABLE gps_vehi (
    id integer NOT NULL,
    id_vehi bigint,
    tiempo timestamp without time zone,
    rumbo smallint,
    velocidad smallint,
    gps_geom geometry,
    satelites smallint,
    evento smallint,
    hrposition character varying(128),
    CONSTRAINT "$1" CHECK ((srid(gps_geom) = 4326)),
    CONSTRAINT "$2" CHECK (((geometrytype(gps_geom) = 'POINT'::text) OR (gps_geom IS NULL)))
);


ALTER TABLE public.gps_vehi OWNER TO promoambiental;

--
-- Name: gps_vehi_gid_seq; Type: SEQUENCE; Schema: public; Owner: promoambiental
--

CREATE SEQUENCE gps_vehi_gid_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.gps_vehi_gid_seq OWNER TO promoambiental;

--
-- Name: gps_vehi_gid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: promoambiental
--

ALTER SEQUENCE gps_vehi_gid_seq OWNED BY gps_vehi.id;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: promoambiental
--

ALTER TABLE gps_vehi ALTER COLUMN id SET DEFAULT nextval('gps_vehi_gid_seq'::regclass);


--
-- Name: gps_vehi_pkey; Type: CONSTRAINT; Schema: public; Owner: promoambiental; Tablespace: 
--

ALTER TABLE ONLY gps_vehi
    ADD CONSTRAINT gps_vehi_pkey PRIMARY KEY (id);


--
-- Name: gps_vehi_evento; Type: INDEX; Schema: public; Owner: promoambiental; Tablespace: 
--

CREATE INDEX gps_vehi_evento ON gps_vehi USING btree (evento);


--
-- Name: gps_vehi_id_vehi; Type: INDEX; Schema: public; Owner: promoambiental; Tablespace: 
--

CREATE INDEX gps_vehi_id_vehi ON gps_vehi USING btree (id_vehi);


--
-- Name: gps_vehi_idx; Type: INDEX; Schema: public; Owner: promoambiental; Tablespace: 
--

CREATE INDEX gps_vehi_idx ON gps_vehi USING gist (gps_geom);


--
-- Name: gps_vehi_tiempo; Type: INDEX; Schema: public; Owner: promoambiental; Tablespace: 
--

CREATE INDEX gps_vehi_tiempo ON gps_vehi USING btree (tiempo);


--
-- PostgreSQL database dump complete
--

