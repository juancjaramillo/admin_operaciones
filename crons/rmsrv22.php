<?
include(dirname(__FILE__) . "/../application.php");
ini_set("error_log",dirname(__FILE__) . "/log_alertas.log");
$qRutas=$db->sql_query("
DROP TABLE srv.srv22;
CREATE TABLE srv.srv22
(
    fechaenvio timestamp with time zone,
    iddespacho integer,
    numeroviaje character varying(3) COLLATE pg_catalog."default",
    idplataforma integer,
    tiporeporte character varying(4) COLLATE pg_catalog."default",
    placavehiculo character varying(8) COLLATE pg_catalog."default",
    ignicion character varying(4) COLLATE pg_catalog."default",
    himic timestamp without time zone,
    hfmic timestamp without time zone,
    estado text COLLATE pg_catalog."default",
    fechahora timestamp without time zone,
    latitud double precision,
    longitud double precision,
    satelites integer,
    sentido smallint,
    direccion character varying(128) COLLATE pg_catalog."default",
    velocidad smallint,
    kilometraje double precision,
    tipoalerta text COLLATE pg_catalog."default"
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE srv.srv22
    OWNER to promoambiental;

CREATE INDEX srv22_fechaenvio
    ON srv.srv22 USING btree
    (fechaenvio)
    TABLESPACE pg_default;

CREATE INDEX srv22_idplataforma
    ON srv.srv22 USING btree
    (idplataforma)
    TABLESPACE pg_default;
");


?>
