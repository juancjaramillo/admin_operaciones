<?
include_once("../application.php");

if(!isset($_SESSION[$CFG->sesion]["user"])){
  $errorMsg="No existe la sesión.";
  error_log($errorMsg);
  die($errorMsg);
}
$user=$_SESSION[$CFG->sesion]["user"];

$mode=nvl($_GET["mode"],nvl($_POST["mode"],""));

switch(nvl($mode)){

	case "listar_items":
		listar_items($_GET["id_grupo"]);
	break;

	case "agregar_item":
		agregar_item($_GET["id_grupo"]);
	break;

	case "insertar_item":
		insertar_item($_POST);
	break;

	case "editar_item":
		editar_item($_GET["id"]);
	break;

	case "actualizar_item":
		actualizar_item($_POST);
	break;

	case "eliminar_item":
		eliminar_item($_GET["id"]);
	break;

	case "agregar":
		agregar();
	break;

	case "insertar":
		insertar($_POST);
	break;

	case "editar":
		editar($_GET["id"]);
	break;

	case "actualizar":
		actualizar($_POST);
	break;

	case "eliminar":
		eliminar($_POST);
	break;

	case "listar_inspecciones_semanales";
		listar_inspecciones_semanales(nvl($_GET));
	break;

	case "listar_inspecciones_mensuales";
		listar_inspecciones_mensuales(nvl($_GET));
	break;

	default:
		listar_inspecciones_diario(nvl($_GET));
	break;

}


function listar_items($idGrupo)
{
	global $CFG, $db,$ME;

	$condicion = "true";
	$user=$_SESSION[$CFG->sesion]["user"];
	if($user["nivel_acceso"]!=1)
		$condicion="(id_centro IS NULL OR id_centro IN (" . implode(",",$user["id_centro"]) . "))";

	$idsGrupos[] = $idGrupo;
	obtenerIdsGrupos($idGrupo,$idsGrupos);

	$consulta = "SELECT i.*, getPath(g.id,'mtto.grupos') as grupo, i.texto
		FROM mtto.items i
		LEFT JOIN mtto.grupos g ON g.id=i.id_grupo
		WHERE i.id_grupo IN (".implode(",",$idsGrupos).")
		ORDER BY i.id_grupo, i.orden";
	$qid = $db->sql_query($consulta);

	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/listar_items_xgrupo.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function agregar_item($idGrupo)
{
	global $CFG, $db,$ME;

	$condicion="true";
	$user=$_SESSION[$CFG->sesion]["user"];
	if($user["nivel_acceso"]!=1)
		$condicion="(id_centro IS NULL OR id_centro IN (" .implode(",",$user["id_centro"]) . "))";

	$db->build_recursive_tree_path("mtto.grupos",$grupos,$idGrupo,"id","id_superior","nombre","-1","",$condicion);
		
	$newMode="insertar_item";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/items_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function insertar_item($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir . "/mtto.items.php");
	$entidad->loadValues($frm);
	$id=$entidad->insert();

	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function editar_item($idItem)
{
	global $CFG, $db,$ME;

	$condicion="true";
	$user=$_SESSION[$CFG->sesion]["user"];
	if($user["nivel_acceso"]!=1)
		$condicion="(id_centro IS NULL OR id_centro IN (" . implode(",",$user["id_centro"]) . "))";

	$item = $db->sql_row("SELECT * FROM mtto.items WHERE id=".$idItem);

	$db->build_recursive_tree_path("mtto.grupos",$grupos,$item["id_grupo"],"id","id_superior","nombre","-1","",$condicion);
	$newMode="actualizar_item";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/items_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function actualizar_item($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir . "/mtto.items.php");
	$entidad->loadValues($frm);
	$entidad->set("mode","update");
	$entidad->update();

	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function eliminar_item($idItem)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir . "/mtto.items.php");
	$entidad->load($idItem);
	$entidad->set("mode","eliminar");
	$entidad->set("id",$idItem);
	if($entidad->hasRelatedEntities()){
		include($CFG->dirroot."/templates/header_popup.php");
		echo "<br>No se puede borrar, porque tiene inspecciones relacionadas.<br><br>\n";
		echo "<input type=\"button\" onClick=\"window.close();\" value=\"Cerrar\" class=\"boton_verde\">";
		include($CFG->dirroot."/templates/footer_popup.php");
		die();
	}
	$entidad->delete();
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function listar_inspecciones_diario($frm)
{
	global $CFG, $db,$ME;

	$fecha = nvl($frm["fecha"],date("Y-m-d"));
	$condicion = "true";
	$user=$_SESSION[$CFG->sesion]["user"];
	if($user["nivel_acceso"]!=1)
		$condicion="v.id_centro IN (" . implode(",",$user["id_centro"]) . ")";

	$datos = array();
	$cons = "SELECT i.id, to_char(i.fecha,'YYYY/MM/DD') as fecha_i, to_char(i.fecha,'HH24:MI:SS') as hora, v.codigo||'/'||v.placa as vehiculo, p.nombre||' '||p.apellido as reporto, '<a href=\'javascript:edicion('||i.id||')\'><img alt=\'Editar\' src=\'".$CFG->wwwroot."/admin/iconos/transparente/iconoeditar.gif\' border=\'0\'></a>' as editar
		FROM mtto.inspecciones i
		LEFT JOIN vehiculos v ON v.id=i.id_vehiculo
		LEFT JOIN personas p ON p.id=i.id_reporto
		WHERE ".$condicion." AND i.fecha::date = '".$fecha."'
		ORDER BY i.fecha";
	$qid = $db->sql_query($cons);
	while($query = $db->sql_fetchrow($qid))
	{
		$datos[] = '{id:"'.$query["id"].'", fecha:"'.$query["fecha_i"].'", hora:"'.$query["hora"].'", vehiculo:"'.$query["vehiculo"].'",  reporto:"'.$query["reporto"].'", editar:"'.$query["editar"].'"}';
	}
	list($anio,$mes,$dia)=split("-",$fecha);
	$ant = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) - 1 * 24 * 60 * 60)."&mode=listar_inspecciones_diario";
	$sig = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + 1 * 24 * 60 * 60)."&mode=listar_inspecciones_diario";

	$tipoListado="diario";
	$titulo = "INSPECCIONES DEL DÍA ".strtoupper(strftime("%A %d de %B / %Y",strtotime($fecha)));
	include("templates/listado_inspecciones.php");
}

function listar_inspecciones_semanales($frm)
{
	global $CFG, $db,$ME;

	$fecha = nvl($frm["fecha"],date("Y-m-d"));
	$semana = obtenerSemana($fecha);
	$condicion = "true";
	$user=$_SESSION[$CFG->sesion]["user"];
	if($user["nivel_acceso"]!=1)
		$condicion="v.id_centro IN (" . implode(",",$user["id_centro"]) . ")";

	$datos = $horas = array();
	$cons = "SELECT i.id, to_char(i.fecha,'HH24:MI:SS') as hora_plan, v.codigo||'/'||v.placa as vehiculo, to_char(i.fecha,'HH24') as horas, to_char(i.fecha,'DD') as dia
		FROM mtto.inspecciones i
		LEFT JOIN vehiculos v ON v.id=i.id_vehiculo
		LEFT JOIN personas p ON p.id=i.id_reporto
		WHERE ".$condicion." AND i.fecha >= '".$semana["Monday"]." 00:00:00' AND i.fecha <= '".$semana["Sunday"]." 23:59:59'
		ORDER BY i.fecha";
	$qid = $db->sql_query($cons);
	while($query = $db->sql_fetchrow($qid))
	{
		$datos[$query["horas"]][$query["dia"]][] = array("line"=>"<a href=\"javascript:edicion(".$query["id"].")\">".$query["hora_plan"]." : ".$query["vehiculo"]."</a>");
		$horas[$query["horas"]] = $query["horas"];

	}

	asort($horas);
	list($anio,$mes,$dia)=split("-",$semana["Monday"]);
	$ant = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) - 1 * 24 * 60 * 60)."&mode=listar_inspecciones_semanales";
	list($anio,$mes,$dia)=split("-",$semana["Sunday"]);
	$sig = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + 1 * 24 * 60 * 60)."&mode=listar_inspecciones_semanales";

	$tipoListado="semanal";
	$titulo = "INSPECCIONES SEMANALES: ".strtoupper(strftime("%d de %B/%Y",strtotime($semana["Monday"])))." A ".strtoupper(strftime("%d de %B/%Y",strtotime($semana["Sunday"])));
	include("templates/listado_inspecciones.php");
}

function listar_inspecciones_mensuales($frm)
{
	global $CFG, $db,$ME;

	$fecha = nvl($frm["fecha"],date("Y-m-d"));
	list($anio,$mes,$dia)=split("-",$fecha);
	$primerDia = $anio."-".$mes."-01";
	$ultimoDia = $anio."-".$mes."-".ultimoDia($mes,$anio);

	$condicion = "true";
	$user=$_SESSION[$CFG->sesion]["user"];
	if($user["nivel_acceso"]!=1)
		$condicion="v.id_centro IN (" . implode(",",$user["id_centro"]) . ")";

	$datos = $horas = array();
	$cons = "SELECT i.id, to_char(i.fecha,'HH24:MI:SS') as hora_plan, v.codigo||'/'||v.placa as vehiculo, i.fecha as fecha_completa
		FROM mtto.inspecciones i
		LEFT JOIN vehiculos v ON v.id=i.id_vehiculo
		LEFT JOIN personas p ON p.id=i.id_reporto
		WHERE ".$condicion." AND i.fecha >= '".$primerDia." 00:00:00' AND i.fecha <= '".$ultimoDia." 23:59:59' 
		ORDER BY i.fecha";
	$qid = $db->sql_query($cons);
	while($query = $db->sql_fetchrow($qid))
	{
		$dia = trim(strftime("%e",strtotime($query["fecha_completa"])));
		$datos[$dia][] =array("line"=>"<a href=\"javascript:edicion(".$query["id"].")\">".$query["hora_plan"]." : ".$query["vehiculo"]."</a>");
	}

	list($anio,$mes,$dia)=split("-",$primerDia);
	$ant = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) - 1 * 24 * 60 * 60)."&mode=listar_inspecciones_mensuales";
	list($anio,$mes,$dia)=split("-",$ultimoDia);
	$sig = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + 1 * 24 * 60 * 60)."&mode=listar_inspecciones_mensuales";
	//Variable para llevar la cuenta del dia actual
	$dia_actual = 1;
	//calculo el numero del dia de la semana del primer dia
	$numero_dia = calcula_numero_dia_semana(1,$mes,$anio);
	$tipoListado="mensual";
	$titulo = "INSPECCIONES MENSUALES: ".strtoupper(strftime("%B/%Y",strtotime($primerDia)));
	include("templates/listado_inspecciones.php");
}

function agregar()
{
	global $CFG, $db,$ME;

	$condicion="";
	$user=$_SESSION[$CFG->sesion]["user"];
	if($user["nivel_acceso"]!=1)
		$condicion=" AND id_centro IN (" . implode(",",$user["id_centro"]) . ")";

	$db->crear_select("SELECT id, codigo||'/'||placa FROM vehiculos WHERE true ".$condicion." ORDER BY codigo,placa",$vehiculos,"","");

	$newMode="insertar";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/inspecciones_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function insertar($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir . "/mtto.inspecciones.php");
	$entidad->loadValues($frm);
	$id=$entidad->insert();

	$goto = $CFG->wwwroot."/mtto/inspecciones.php?mode=editar&id=".$id;
	echo "<script>window.location.href='".$goto."';</script>";
}

function editar($id)
{
	global $CFG, $db,$ME;

	$condicion="";
	$user=$_SESSION[$CFG->sesion]["user"];
	if($user["nivel_acceso"]!=1)
		$condicion=" AND id_centro IN (" . implode(",",$user["id_centro"]) . ")";

	$insp = $db->sql_row("SELECT * FROM mtto.inspecciones WHERE id=".$id);
	$db->crear_select("SELECT id, codigo||'/'||placa FROM vehiculos WHERE true ".$condicion." ORDER BY codigo,placa",$vehiculos,$insp["id_vehiculo"],"");
	$centro = $db->sql_row("SELECT id_centro FROM vehiculos WHERE id=".$insp["id_vehiculo"]);
	$db->crear_select("SELECT id, nombre||' '||apellido as nombre FROM  personas WHERE id IN (SELECT id_persona FROM personas_centros WHERE id_centro='".$centro["id_centro"]."') ORDER BY nombre,apellido",$reporto,$insp["id_reporto"],"");

	//items
	$items = array();
	$qid = $db->sql_query("SELECT i.id, s.texto, i.hecha, i.observaciones,s.orden
			FROM mtto.inspecciones_items i
			LEFT JOIN mtto.items s ON s.id=i.id_item
			WHERE i.id_inspeccion=".$insp["id"]."
			ORDER BY s.orden");


	$newMode="actualizar";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/inspecciones_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function actualizar($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir . "/mtto.inspecciones.php");
	$entidad->loadValues($frm);
	$entidad->set("mode","update");
	$entidad->update();

	//elementos
	$items = array();
	foreach($frm as $key => $value)
	{ 
		if(preg_match("/insp_observa_/",$key,$match))
		{ 
			$id = str_replace("insp_observa_","",$key);
			$items[$id]["observaciones"]=$value;
		}

		if(preg_match("/insp_check_/",$key,$match))
		{ 
			$id = str_replace("insp_check_","",$key);
			$items[$id]["insp_check"]=$value;
		}
	}

	foreach($items as $key => $dx)
	{
		$check = "false";
		if(isset($dx["insp_check"]))
			$check = "true";

		$db->sql_query("UPDATE mtto.inspecciones_items SET hecha=".$check.", observaciones='".$dx["observaciones"]."' WHERE id=".$key);
	}

	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function eliminar($frm)
{
	global $CFG, $db,$ME;

	$db->sql_query("DELETE FROM mtto.inspecciones WHERE id=".$frm["id"]);
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}



?>

