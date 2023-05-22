<?
include_once("../application.php");

if(!isset($_SESSION[$CFG->sesion]["user"])){
	$errorMsg="No existe la sesión.";
	error_log($errorMsg);
	die($errorMsg);
}

verificarPagina("actualizacioneskmyhr");

$mode=nvl($_GET["mode"],nvl($_POST["mode"],""));

switch(nvl($mode)){

	case "kilometraje";
	  kilometraje($_POST);
	break;

	case "horometro";
	  horometro($_POST);
	break;

	default:
		listar();
	break;
}

function listar()
{
	global $db,$CFG,$ME;

	$condicion = "true";
	$user=$_SESSION[$CFG->sesion]["user"];
	if($user["nivel_acceso"]!=1)
		$condicion="e.id_centro IN (" . implode(",",$user["id_centro"]) . ")";

	$datos = array();
#$cons = "SELECT e.id, e.nombre, getPath(g.id,'mtto.grupos') as grupo, e.kilometraje, e.horometro
	$cons = "SELECT e.id, e.nombre, g.nombre as grupo, e.kilometraje, e.horometro
		 FROM mtto.equipos e
		 LEFT JOIN mtto.grupos g ON g.id=e.id_grupo
		 WHERE ".$condicion."
		 ORDER BY e.nombre" ;
	$qid = $db->sql_query($cons);
	while($query = $db->sql_fetchrow($qid))
	{
		$datos[] = '{id:"'.$query["id"].'", equipo:"'.$query["nombre"].'", grupo:"'.$query["grupo"].'", kilometraje:"'.number_format($query["kilometraje"], 0, ",", ".").'",  horometro:"'.number_format($query["horometro"],0, ",", ".").'"}';
	}

	include($CFG->dirroot."/templates/header_popup_tabview.php");
	include("templates/actualizacioneskmyhr_form.php");
}

function kilometraje($frm)
{
	global $db,$CFG,$ME;

	actualizarKmyHoro("kilometraje",preg_replace("/[,.]/","",$frm["newValue"]),$frm["id"]);
}

function horometro($frm)
{
	global $db,$CFG,$ME;

	actualizarKmyHoro("horometro",preg_replace("/[,.]/","",$frm["newValue"]),$frm["id"]);
}

include($CFG->dirroot."/templates/footer_2panel.php");
?>
