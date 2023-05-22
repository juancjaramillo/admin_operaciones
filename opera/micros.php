<?
 // error_reporting(E_ALL);
 // ini_set("display_errors", 1);
include_once("../application.php");

if(!isset($_SESSION[$CFG->sesion]["user"])){
  $errorMsg="No existe la sesión.";
  error_log($errorMsg);
  die($errorMsg);
}

verificarPagina(simple_me($ME));

$mode=nvl($_GET["mode"],nvl($_POST["mode"],""));

switch(nvl($mode)){

	case "eliminar":
		eliminar($_POST);
	break;

	case "editar":
		editar($_GET);
	break;

	case "actualizar":
		actualizar($_POST);
	break;

	case "agregar":
		agregar();
	break;

	case "insertar":
		insertar($_POST);
	break;

	case "editar_codigo":
		editar_codigo($_POST);
	break;

	case "editar_frecuencia":
		editar_frecuencia($_GET["id"]);
	break;

	case "actualizar_frecuencia":
		actualizar_frecuencia($_POST);
	break;

	case "eliminar_frecuencia":
		eliminar_frecuencia($_GET["id"]);
	break;

	case "agregar_frecuencia":
		agregar_frecuencia($_GET);
	break;

	case "insertar_frecuencia":
		insertar_frecuencia($_POST);
	break;

	case "agregar_operario":
		agregar_operario($_GET["id_frecuencia"]);
	break;

	case "insertar_operario":
		insertar_operario($_POST);
	break;

	case "eliminar_operario":
		eliminar_operario($_GET["id"]);
	break;

	case "agregar_desplazamiento":
		agregar_desplazamiento($_GET["id_frecuencia"]);
	break;

	case "insertar_desplazamiento":
		insertar_desplazamiento($_POST);
	break;

	case "eliminar_desplazamiento":
		eliminar_desplazamiento($_GET["id"]);
	break;

	case "agregar_bolsa":
		agregar_bolsa($_GET["id_frecuencia"]);
	break;

	case "insertar_bolsa":
		insertar_bolsa($_POST);
	break;

 	case "eliminar_bolsa":
		eliminar_bolsa($_GET["id"]);
	break;

	case "duplicar":
		duplicar($_GET);
	break;

	case "duplicar_frecuencia":
		duplicar_frecuencia($_GET);
	break;

	case "resultados":
		resultados($_POST);
	break;

 default:
    listado(nvl($_GET));
  break;

}

function listado($frm=array())
{
  global $CFG, $db,$ME;

	$cond = "";
	if(isset($frm["compactadas_inicial"]) && isset($frm["compactadas_final"]))
		$cond = " AND compactadas >= '".$frm["compactadas_inicial"]."' AND compactadas <= '".$frm["compactadas_final"]."'";
	if(isset($frm["km_inicial"]) && isset($frm["km_final"]))
		$cond = " AND km >= '".$frm["km_inicial"]."' AND km <= '".$frm["km_final"]."'";

	include($CFG->modulesdir."/micros.php");
	$entidad->loadValues($frm);
	$entidad->find($cond);

	$datos = $entidad->getRowYahoo();
	$scriptC = $entidad->JSComplementary;

	$titulo = "RUTAS";
	$schema = "";
	include($CFG->dirroot."/opera/templates/listado.php");
}


function editar_codigo($frm)
{ 
	global $db,$CFG,$ME;

	if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["opciones_micros"]))
		$db->sql_query("UPDATE micros SET codigo='".$frm["newValue"]."' WHERE id=".$frm["id"]);
	return "ok";
}


function editar($frm)
{
	global $CFG, $db,$ME;

	$user=$_SESSION[$CFG->sesion]["user"];
	$micro = $db->sql_row("SELECT m.*, a.id_centro FROM micros m LEFT JOIN ases a ON a.id=m.id_ase WHERE m.id=".$frm["id"]);
	$db->crear_select("SELECT id, ase FROM ases WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') ORDER BY ase",$ases,$micro["id_ase"]);
	$db->crear_select("SELECT id, nombre FROM tipos_residuos ORDER BY nombre",$tipos_residuos,$micro["id_tipo_residuo"]);
	$db->crear_select("SELECT id, servicio FROM servicios ORDER BY servicio",$servicios,$micro["id_servicio"]);
	$db->crear_select("SELECT id, nombre  FROM cuartelillos WHERE id_centro = '".$micro["id_centro"]."' ORDER BY nombre",$cuartelillos,$micro["id_cuartelillo"]);
	$db->crear_select("SELECT v.id, v.codigo || '/' || v.placa 
			FROM vehiculos v
		 	LEFT JOIN tipos_vehiculos_servicios tp ON tp.id_tipo_vehiculo=v.id_tipo_vehiculo	
			WHERE v.id_centro = '".$micro["id_centro"]."' AND v.id_estado <> 4  AND tp.id_servicio='".$micro["id_servicio"]."'
			ORDER BY v.codigo,v.placa",$vehiculos,$micro["id_vehiculo"]);
	$cargos = array(8);
	obtenerIdCargos(8,$cargos);
	$db->crear_select("SELECT id, nombre||' '||apellido as nombre FROM personas WHERE id IN (SELECT id_persona FROM personas_centros WHERE id_centro='".$micro["id_centro"]."') AND id_cargo IN (".implode(",",$cargos).") ORDER BY nombre,apellido",$coordinador,$micro["id_coordinador"]);
	$db->crear_select("SELECT id, nombre FROM lugares_descargue WHERE id_centro='".$micro["id_centro"]."'  ORDER BY nombre",$descargue,$micro["id_lugar_descargue"]);

	$newMode="actualizar";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/opera/templates/micros_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function actualizar($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir."/micros.php");
	$entidad->loadValues($frm);
	$entidad->set("mode","update");
	$entidad->update();
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function agregar()
{
	global $CFG, $db,$ME;

	$user=$_SESSION[$CFG->sesion]["user"];
	$db->crear_select("SELECT id, ase FROM ases WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') ORDER BY ase",$ases);
	$db->crear_select("SELECT id, nombre FROM tipos_residuos ORDER BY nombre",$tipos_residuos);
	$db->crear_select("SELECT id, servicio FROM servicios ORDER BY servicio",$servicios);
	$db->crear_select("SELECT id, nombre FROM lugares_descargue WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') ORDER BY nombre",$descargue);
	$cuartelillos = $vehiculos = $coordinador = "<option value='%'>Seleccione</option>";

	$newMode="insertar";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/opera/templates/micros_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function insertar($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir."/micros.php");
	$entidad->loadValues($frm);
	$id=$entidad->insert();
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}


function eliminar($frm){
	GLOBAL $CFG, $ME, $db;

	include($CFG->modulesdir."/micros.php");
	$entidad->load($frm["id"]);
	$entidad->set("mode","eliminar");
	if($entidad->hasRelatedEntities()){
		echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.outerHeight=200;\nwindow.outerWidth=300;\n</script>\n";
		echo "No se puede borrar, porque tiene elementos relacionados.<br><br>\n";
		echo "<input type=\"button\" onClick=\"window.close();\" value=\"Cerrar\">";
		die();
	}
	$entidad->set("id",$frm['id']);
	$entidad->delete();
	
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function editar_frecuencia($idFrecuencia)
{
	GLOBAL $CFG, $ME, $db;

	$frecuencia = $db->sql_row("SELECT f.*, c.id_empresa, esquema 
			FROM micros_frecuencia f
			LEFT JOIN micros m ON f.id_micro=m.id
			LEFT JOIN ases a ON a.id=m.id_ase
			LEFT JOIN centros c ON c.id=a.id_centro
			LEFT JOIN servicios s ON s.id=m.id_servicio
			WHERE f.id=".$idFrecuencia);
	$db->crear_select("SELECT id, turno FROM turnos WHERE id_empresa='".$frecuencia["id_empresa"]."' ORDER BY turno",$turnos,$frecuencia["id_turno"]);

	$newMode="actualizar_frecuencia";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/opera/templates/frecuencias_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function actualizar_frecuencia($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir."/micros_frecuencia.php");
	$entidad->loadValues($frm);
	$entidad->set("mode","update");
	$entidad->update();
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function eliminar_frecuencia($id)
{
	global $CFG, $db,$ME;

	$db->sql_query("DELETE FROM micros_frecuencia WHERE id=".$id);
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function insertar_frecuencia($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir."/micros_frecuencia.php");
	$entidad->loadValues($frm);
	$id=$entidad->insert();
	echo "<script>window.location.href='".$CFG->wwwroot."/opera/micros.php?mode=editar_frecuencia&id=".$id."';</script>";
}

function agregar_operario($id_frecuencia)
{
	global $CFG, $db,$ME;

	$esquema = $db->sql_row("SELECT esquema, id_centro, dia
			FROM micros_frecuencia mf
			LEFT JOIN micros m ON m.id=mf.id_micro
			LEFT JOIN servicios s ON s.id=m.id_servicio
			LEFT JOIN ases a ON a.id=m.id_ase
			WHERE mf.id=".$id_frecuencia);
	if($esquema["esquema"] == "bar")
		$db->crear_select("SELECT id, nombre FROM cargos WHERE id IN (23) ORDER BY nombre", $cargos);
	else
		$db->crear_select("SELECT id, nombre FROM cargos WHERE id IN (21,22,41,48,8) ORDER BY nombre", $cargos);

	$newMode="insertar_operario";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/opera/templates/operarioxfrecuencias_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function insertar_operario($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir."/frecuencias_operarios.php");
	$entidad->loadValues($frm);
	$id=$entidad->insert();
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function eliminar_operario($id)
{
	global $CFG, $db,$ME;

	$db->sql_query("DELETE FROM frecuencias_operarios WHERE id=".$id);
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function agregar_frecuencia($frecuencia)
{
	GLOBAL $CFG, $ME, $db;

	$micro = $db->sql_row("SELECT c.id_empresa 
			FROM micros m 
			LEFT JOIN ases a ON a.id=m.id_ase
			LEFT JOIN centros c ON c.id=a.id_centro
			WHERE m.id=".$frecuencia["id_micro"]);
	$db->crear_select("SELECT id, turno FROM turnos WHERE id_empresa='".$micro["id_empresa"]."' ORDER BY turno",$turnos);

	$newMode="insertar_frecuencia";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/opera/templates/frecuencias_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function agregar_desplazamiento($id_frecuencia)
{
	global $CFG, $db,$ME;

	$db->crear_select("SELECT id, tipo FROM rec.tipos_desplazamientos ORDER BY tipo", $tipos);
	$newMode="insertar_desplazamiento";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/opera/templates/desplazamientoxfrecuencias_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function insertar_desplazamiento($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir."/frecuencias_desplazamientos.php");
	$entidad->loadValues($frm);
	$id=$entidad->insert();
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function eliminar_desplazamiento($id)
{
	global $CFG, $db,$ME;

	$db->sql_query("DELETE FROM frecuencias_desplazamientos WHERE id=".$id);
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function agregar_bolsa($id_frecuencia)
{
	global $CFG, $db,$ME;

	$db->crear_select("SELECT id, tipo FROM bar.tipos_bolsas ORDER BY tipo", $tipos);
	$newMode="insertar_bolsa";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/opera/templates/bolsaxfrecuencias_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function insertar_bolsa($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir."/frecuencias_bolsas.php");
	$entidad->loadValues($frm);
	$id=$entidad->insert();
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function eliminar_bolsa($id)
{
	global $CFG, $db,$ME;

	$db->sql_query("DELETE FROM frecuencias_bolsas WHERE id=".$id);
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function duplicar($frm)
{
	global $CFG, $db,$ME;

	$db->sql_query("INSERT INTO micros (codigo, km, id_cuartelillo, id_vehiculo, id_coordinador, id_tipo_residuo, id_ase,  id_servicio, fecha_desde, fecha_hasta, id_lugar_descargue, compactadas) SELECT 'Duplicado de '||codigo, km, id_cuartelillo, id_vehiculo, id_coordinador, id_tipo_residuo, id_ase,  id_servicio, fecha_desde, fecha_hasta, id_lugar_descargue, compactadas FROM micros WHERE id=".$frm["id"]);
	$idMicro = $db->sql_nextid();


	$qid = $db->sql_query("SELECT * FROM micros_frecuencia WHERE id_micro = ".$frm["id"]);
	while($query = $db->sql_fetchrow($qid))
	{
		$prod = "null";
		if($query["produccion"] != "")
			$prod = $query["produccion"];

		$db->sql_query("INSERT INTO micros_frecuencia (id_micro, dia, id_turno, produccion, viajes, hora_inicio, hora_fin) VALUES ('".$idMicro."', '".$query["dia"]."', '".$query["id_turno"]."', ".$prod.", '".$query["viajes"]."', '".$query["hora_inicio"]."', '".$query["hora_fin"]."')");
		$idFrecuencia = $db->sql_nextid();
		
		//operarios
		$db->sql_query("INSERT INTO frecuencias_operarios (id_frecuencia, id_cargo, id_persona) SELECT $idFrecuencia, id_cargo, id_persona FROM frecuencias_operarios WHERE id_frecuencia='".$query["id"]."'");

		//desplazamientos
		$db->sql_query("INSERT INTO frecuencias_desplazamientos (id_frecuencia, id_tipo_desplazamiento, orden) SELECT $idFrecuencia, id_tipo_desplazamiento, orden FROM frecuencias_desplazamientos WHERE id_frecuencia='".$query["id"]."'");

		//bolsas
		$db->sql_query("INSERT INTO frecuencias_bolsas (id_frecuencia, id_tipo_bolsa, numero_inicio) SELECT $idFrecuencia, id_tipo_bolsa, numero_inicio FROM frecuencias_bolsas WHERE id_frecuencia='".$query["id"]."'");
	}

	//segmentos
	$db->sql_query("INSERT INTO micros_segmentos (id_micro, hora_inicio, hora_fin, id_tipo_segmento, geometry_inicio, geometry_fin) SELECT $idMicro, hora_inicio, hora_fin, id_tipo_segmento, geometry_inicio, geometry_fin FROM micros_segmentos WHERE id_micro = ".$frm["id"]);

	//tipos_vehiculos
	$db->sql_query("INSERT INTO micros_tipos_vehiculos (id_micro, id_tipo_vehiculo) SELECT $idMicro, id_tipo_vehiculo FROM micros_tipos_vehiculos WHERE id_micro = ".$frm["id"]);

	//peajes
	$db->sql_query("INSERT INTO peajes_micros (id_micro, id_peaje) SELECT $idMicro, id_peaje FROM peajes_micros WHERE id_micro = ".$frm["id"]);

	echo "<script>window.location.href='".$CFG->wwwroot."/opera/micros.php';</script>";
}


function duplicar_frecuencia($frm)
{
	global $CFG, $db,$ME;

	$qid = $db->sql_query("SELECT * FROM micros_frecuencia WHERE id = ".$frm["id_frecuencia"]);
	while($query = $db->sql_fetchrow($qid))
	{
		$prod = "null";
		if($query["produccion"] != "")
			$prod = $query["produccion"];

		$db->sql_query("INSERT INTO micros_frecuencia (id_micro, dia, id_turno, produccion, viajes, hora_inicio, hora_fin) VALUES ('".$frm["id_micro"]."', '".$query["dia"]."', '".$query["id_turno"]."', ".$prod.", '".$query["viajes"]."', '".$query["hora_inicio"]."', '".$query["hora_fin"]."')");
		$idFrecuencia = $db->sql_nextid();
		
		//operarios
		$db->sql_query("INSERT INTO frecuencias_operarios (id_frecuencia, id_cargo, id_persona) SELECT $idFrecuencia, id_cargo, id_persona FROM frecuencias_operarios WHERE id_frecuencia='".$query["id"]."'");

		//desplazamientos
		$db->sql_query("INSERT INTO frecuencias_desplazamientos (id_frecuencia, id_tipo_desplazamiento, orden) SELECT $idFrecuencia, id_tipo_desplazamiento, orden FROM frecuencias_desplazamientos WHERE id_frecuencia='".$query["id"]."'");

		//bolsas
		$db->sql_query("INSERT INTO frecuencias_bolsas (id_frecuencia, id_tipo_bolsa, numero_inicio) SELECT $idFrecuencia, id_tipo_bolsa, numero_inicio FROM frecuencias_bolsas WHERE id_frecuencia='".$query["id"]."'");
	}

	echo "<script>window.location.href='".$CFG->wwwroot."/opera/templates/listado_frecuencias.php?id_micro=".$frm["id_micro"]."';</script>";
}

function resultados($frm)
{
	global $db,$CFG,$ME;

	$queryArray=array();
	foreach($frm AS $key=>$val){
		if($key!="mode" && $val!="" && $val!="%")
			array_push($queryArray,$key . "=" . $val);
	}
	$queryString=$ME . "?mode=listado&";
	$queryString.=implode("&",$queryArray);

	echo "<script>\n";
	echo "var url='" . $queryString . "';\n";
	echo "window.opener.location.href=url;\nwindow.close();\n</script>\n";
	echo "</script>\n";
}

?>
