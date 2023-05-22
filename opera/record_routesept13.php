<?php
include("../application.php");
$frm=$_GET;
switch(nvl($frm["mode"])){
	case "traer_gps":
		traer_gps($frm);
		break;
	default:
		$frm["newMode"]="traer_gps";
		print_form($frm);
		break;
}


//	FUNCIONES:
function traer_gps($frm){
GLOBAL $db,$CFG, $ME;

	$vehiculo=$db->sql_row("SELECT * FROM vehiculos WHERE id='$frm[id_vehiculo]'");
	$micro = $db->sql_row("
		SELECT m.*, a.id_centro, c.centro
		FROM micros m LEFT JOIN ases a ON a.id=m.id_ase LEFT JOIN centros c ON a.id_centro=c.id
		WHERE m.id='$frm[id_micro]'
	");
	$qVehiculos=$db->sql_query("
		SELECT v.id, v.codigo || '/' || v.placa as label
		FROM vehiculos v
			LEFT JOIN tipos_vehiculos_servicios tp ON tp.id_tipo_vehiculo=v.id_tipo_vehiculo	
		WHERE v.id_centro = '".$micro["id_centro"]."' AND tp.id_servicio='".$micro["id_servicio"]."'
		ORDER BY v.codigo,v.placa
	");
	if(isset($frm["hora_desde"])) $frm["fecha_desde"]=$frm["fecha_desde"] . " " . $frm["hora_desde"];
	if(isset($frm["hora_hasta"])) $frm["fecha_hasta"]=$frm["fecha_hasta"] . " " . $frm["hora_hasta"];

	$fecha_desde=date("Y-m-d H:i:s",strtotime("+ 5 hours",strtotime($frm["fecha_desde"])));
	$fecha_hasta=date("Y-m-d H:i:s",strtotime("+ 5 hours",strtotime($frm["fecha_hasta"])));

	$frm["newMode"]=$frm["mode"];

	include("templates/mapa_ruta_gps.php");

}

function print_form($frm){
GLOBAL $db,$CFG, $ME;

	if(!isset($frm["id_micro"])) die("No viene la microrruta.");
	$user=$_SESSION[$CFG->sesion]["user"];
	$micro = $db->sql_row("SELECT m.*, a.id_centro FROM micros m LEFT JOIN ases a ON a.id=m.id_ase WHERE m.id='$frm[id_micro]'");
	$db->crear_select("
		SELECT v.id, v.codigo || '/' || v.placa 
		FROM vehiculos v
			LEFT JOIN tipos_vehiculos_servicios tp ON tp.id_tipo_vehiculo=v.id_tipo_vehiculo	
		WHERE v.id_centro = '".$micro["id_centro"]."' AND tp.id_servicio='".$micro["id_servicio"]."'
		ORDER BY v.codigo,v.placa
	",$vehiculosOptions,nvl($micro["id_vehiculo"]));
	include($CFG->dirroot."/templates/header_popup.php");
	include("templates/record_route_form.php");
}
?>
