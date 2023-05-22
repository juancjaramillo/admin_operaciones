<?
include(dirname(__FILE__) . "/../application.php");
echo "[" . date("Y-m-d H:i:s") . "] Iniciando...\n";

$i=0;
do {
	$db=NULL;
	$db=new sql_db_postgres($CFG->dbhost,$CFG->dbuser,$CFG->dbpass,$CFG->dbname);
	$qid=NULL;
	$qid=$db->sql_query("SELECT * FROM gps_vehi WHERE tiempo < (now() - ('3 months')::INTERVAL) ORDER BY id LIMIT 10000");
	while($reg=$db->sql_fetchrow($qid)){
	//	print_r($reg);
		$tabla="gps" . date("Ym",strtotime($reg["tiempo"]));
		if(!tableExists($tabla,"historico")){
			echo "\nTABLA: " . $tabla . " NO EXISTE\n";
			createGPSTable($tabla);
		}
		$db->sql_insert("historico.$tabla",$reg,TRUE);
		$db->sql_query("DELETE FROM gps_vehi WHERE id='$reg[id]'");
		$i++;
	}
	echo "\n[" . date("Y-m-d H:i:s") . "] " .$i . " :: " . $reg["id"];
} while ($db->sql_numrows($qid) == 10000);
echo "\n[" . date("Y-m-d H:i:s") . "] Haciendo VACUUM...";
//$db->sql_query("VACUUM ANALYZE gps_vehi");

echo "\n[" . date("Y-m-d H:i:s") . "] Listo.\n";

/*	FUNCIONES	*/

function createGPSTable($tableName){
GLOBAL $db;

	$db->sql_query("
		CREATE TABLE historico." . $tableName . " (
			id integer NOT NULL,
			id_vehi bigint,
			tiempo timestamp without time zone,
			rumbo smallint,
			velocidad smallint,
			gps_geom geometry,
			satelites smallint,
			evento smallint,
			hrposition character varying(128),
			CONSTRAINT \"\$1\" CHECK ((srid(gps_geom) = 4326)),
			CONSTRAINT \"\$2\" CHECK (((geometrytype(gps_geom) = 'POINT'::text) OR (gps_geom IS NULL)))
		)
	");
}

function tableExists($tableName,$schema="public"){
GLOBAL $db;
	if($result=$db->sql_row("
			SELECT relname
			FROM pg_class LEFT JOIN pg_namespace ON pg_class.relnamespace=pg_namespace.oid
			WHERE relkind = 'r' AND relname ='" . $tableName . "' AND nspname='" . $schema . "'
	")) return(true);
	else return(false);
}
?>
