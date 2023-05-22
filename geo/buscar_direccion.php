<?
/* buscar_ase.php3 (c) 2021 Juan Jaramillo�n (vguzman@bigfoot.com) */

require_once("../application.php");
//require_once($CFG->libdir . "/direccion.php");
require_once($CFG->common_libdir . "/georref/direccion.php");

/******************************************************************************
 * FUNCIONES
 *****************************************************************************/


function calcular_direccion($string, $base, &$error, $ciudad="11001", $barrio="",&$descrError=""){

	global $CFG, $db, $direccion;
	$dbgeo=new sql_db_postgres($CFG->dbhost_geo_postgres,$CFG->dbuser_geo_postgres,$CFG->dbpass_geo_postgres,$CFG->dbname_geo_postgres);
//	error_log($CFG->dbhost_geo_postgres . "," . $CFG->dbuser_geo_postgres . "," . $CFG->dbpass_geo_postgres . "," . $CFG->dbname_geo_postgres);

	if($ciudad!=""){
//		require_once($CFG->libdir . "/direccion_poligono.php");
		$direccion = new direccion($dbgeo,$string);
		$qconf=$dbgeo->sql_query("SELECT * FROM sig_gr_conf_cities WHERE city=$ciudad");
		if($city=$dbgeo->sql_fetchrow($qconf)){
			$direccion->set("defaultEast",$city["defaulteast"]);
			$direccion->set("defaultSouth",$city["defaultsouth"]);
			$direccion->set("numExtrapolations",$city["extrapolations"]);
			$direccion->set("clSouth",$city["clsouth"]);
		}
		$direccion->set("municipio",$ciudad);
		$direccion->set("barrio",$barrio);
		if($direccion->translate()){
			if($direccion->locate()){
			/*
		  	$zonificacion=$dbgeo->sql_query("SELECT ID FROM ZONIFICACIONES WHERE MUNICIPIO=$ciudad AND  ZONIFICACION='$base'");
				$zonificacion=$dbgeo->sql_fetchrow($zonificacion);
				$idZonificacion=$zonificacion["ID"];
				$direccion->set("poligonos",$idZonificacion);
				preguntar($direccion);i*/
				//if($idPoligono=$direccion->returnPolygon()) return ($idPoligono);
/*			preguntar($direccion);
			*/
				return($direccion);
			}
			else{
				$descrError=$direccion->errors[$direccion->errorCode];
				return($direccion->errorCode);
			}
		}
		$error=$direccion->errorCode;
		$descrError=$direccion->errors[$direccion->errorCode];
		return($error);
	}
}

?>
