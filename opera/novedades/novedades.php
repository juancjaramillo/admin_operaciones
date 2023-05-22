<?
include_once("../application.php");
error_reporting(E_ALL);
if(!isset($_SESSION[$CFG->sesion]["user"])){
  $errorMsg="No existe la sesión.";
  error_log($errorMsg);
  die($errorMsg);
}

$mode=nvl($_GET["mode"],nvl($_POST["mode"],""));

switch(nvl($mode)){

	case "agregar":
		agregar($_GET);
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

	case "observaciones";
		observaciones($_POST);
	break;

	case "cerrar":
		cerrar($_GET["id"]);
	break;
	
	case "buscar":
		buscar($_GET["clase"]);
	break;

	case "resultados":
		resultados($_POST);
	break;

	case "listar_resultados":
		listar_resultados($_GET);
	break;

	case "bajar_excel":
		bajar_excel($_GET);
	break;

	default:
		listado(nvl($_GET));
	break;

}


function listado($frm)
{
	global $CFG, $db,$ME;

	$user=$_SESSION[$CFG->sesion]["user"];
	$cond = " AND hora_fin IS NULL";
	$titulo = "NOVEDADES ABIERTAS";
	$clase = nvl($frm["clase"],"");
	$editarObser = true;
	if(nvl($frm["abierta"])=="f")
	{
		$cond = " AND hora_fin IS NOT NULL";
		$titulo = "NOVEDADES CERRADAS";
		$editarObser = false;
	}
	$camposAd = "";

	if($clase == "mtto")
	{
		$cond .= " AND n.esquema = 'mtto'";
		$camposAd = ", array_to_string(array(
			SELECT mtto.rutinas.rutina||'/'||mtto.ordenes_trabajo.fecha_planeada
			FROM mtto.ordenes_trabajo
			LEFT JOIN mtto.rutinas ON mtto.ordenes_trabajo.id_rutina=mtto.rutinas.id
			WHERE mtto.ordenes_trabajo.id_novedad = n.id
         ),', ') as ots";
	}
	else
		$cond .= " AND n.esquema != 'mtto'";

	$datos = array();

	$cons = "SELECT n.id, e.nombre, to_char(n.hora_inicio,'YYYY/MM/DD') as fini, to_char(n.hora_inicio,'HH24:MI:SS') as hini, n.observaciones, case when hora_fin IS NULL then '<a href=''javascript:edicion('||n.id||')''><img alt=''Editar'' src=''".$CFG->wwwroot."/admin/iconos/transparente/iconoeditar.gif'' border=''0''></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=''javascript:cerrar('||n.id||')''><img alt=''Cerrar'' src=''".$CFG->wwwroot."/admin/iconos/transparente/check_green.png'' border=''0''></a>' else '<a href=''javascript:edicion('||n.id||')''><img alt=''Editar'' src=''".$CFG->wwwroot."/admin/iconos/transparente/iconoeditar.gif'' border=''0''></a>' end as editar ".$camposAd."
			FROM novedades n
			LEFT JOIN mtto.equipos e ON e.id=n.id_equipo
			WHERE n.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]') ".$cond."
			ORDER BY n.hora_inicio";

	$qid = $db->sql_query($cons);
	while($query = $db->sql_fetchrow($qid))
	{
		$observaciones = str_replace('"',"",$query["observaciones"]);
		$observaciones = str_replace("'","",$observaciones);
		$observaciones = str_replace("\r\n","<br>",$observaciones);
		
		if(!in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["opciones_novedades"]))
			$query["editar"]="";

		if($clase == "mtto")
			$datos[] = '{id:"'.$query["id"].'", equipo:"'.$query["nombre"].'", fini:"'.$query["fini"].'", hini:"'.$query["hini"].'",  observaciones:"'.$observaciones.'", editar:"'.$query["editar"].'", ordenes:"'.$query["ots"].'" }';
		else
			$datos[] = '{id:"'.$query["id"].'", equipo:"'.$query["nombre"].'", fini:"'.$query["fini"].'", hini:"'.$query["hini"].'",  observaciones:"'.$observaciones.'", editar:"'.$query["editar"].'"}';
	}

	include($CFG->dirroot."/novedades/templates/listado_novedades.php");
}

function agregar($nov)
{
	global $CFG, $db,$ME;

	$condicion="";
	$user=$_SESSION[$CFG->sesion]["user"];
	$condCentro = "IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')";
	if(isset($nov["id_centro"]))
		$condCentro = "='".$nov["id_centro"]."'";

	$db->crear_select("SELECT mtto.equipos.id, mtto.equipos.nombre FROM mtto.equipos LEFT JOIN vehiculos ON mtto.equipos.id_vehiculo=vehiculos.id WHERE mtto.equipos.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]') and vehiculos.id_estado!=4 ORDER BY nombre",$equipos);
	$db->crear_select("SELECT id, (nombre || ' ' || apellido) FROM personas WHERE id='$user[id]' ORDER BY nombre,apellido",$personas_i,$user["id"]);
	$db->crear_select("SELECT id, (codigo || ' / ' || placa) FROM vehiculos WHERE id_centro ".$condCentro." and id_estado!=4 ORDER BY codigo, placa",$vehiculos);
	$db->crear_select("
		SELECT id, (nombre || ' ' || apellido)
		FROM personas
		WHERE id IN (SELECT id_persona FROM personas_centros WHERE id_centro ".$condCentro.") ORDER BY nombre,apellido ",$personas_r);
	$db->crear_select("SELECT id, centro FROM centros WHERE id IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]') ORDER BY centro",$centros,nvl($nov["id_centro"]));

	$newMode="insertar";
	
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/novedades/templates/novedades_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function insertar($frm)
{
	global $CFG, $db,$ME;

	if($frm["clase"] == "mtto")
	{
		$idCentro = $db->sql_row("SELECT id_centro FROM mtto.equipos WHERE id=".$frm["id_equipo"]);
		$frm["id_centro"] = $idCentro["id_centro"];
	}	

	include($CFG->modulesdir . "/novedades.php");
	$entidad->loadValues($frm);
	$id=$entidad->insert();
/*
Hay que replantear esto, porque la idea era que permitiera crear la novedad
y ahí mismo pudiera crear la OT asociada, pero cuando se termina de crear,
recarga la página y vuelve a lanzar el popup.

	if(isset($frm["mode2"]) && ($frm["mode2"]=="generarOT")){
		echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.location.href='" . $ME . "?mode=editar&id=" . $id . "&mode2=generarOT';\n</script>";
	}
	else
*/
		echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}


function editar($id)
{
	global $CFG, $db,$ME;

	$condicion="";
	$user=$_SESSION[$CFG->sesion]["user"];
	if($user["nivel_acceso"]!=1)
		$condicion=" AND id_centro IN (" . implode(",",$user["id_centro"]) . ")";

	$nov = $db->sql_row("SELECT * FROM novedades WHERE id=".$id);
	$nov["clase"]="";
	if($nov["esquema"]=="mtto")
		$nov["clase"]="mtto";

	$db->crear_select("SELECT id, nombre FROM mtto.equipos WHERE true ".$condicion." ORDER BY nombre",$equipos,$nov["id_equipo"]);
	$db->crear_select("SELECT id, (nombre || ' ' || apellido) FROM personas WHERE id='$nov[id_ingresa]' ORDER BY nombre,apellido",$personas_i,$nov["id_ingresa"]);
	$db->crear_select("
		SELECT id, (nombre || ' ' || apellido) FROM personas WHERE id IN ( SELECT id_persona FROM personas_centros WHERE id_centro = '".$nov["id_centro"]."') ORDER BY nombre,apellido",$personas_r,$nov["id_reporta"]);

	if($nov["esquema"]=="mtto")
		$db->build_recursive_tree_path("tipos_novedades",$tipos,$nov["id_tipo_novedad"],"id","id_superior","tipos_novedades.nombre","-1","","clase=3");
	else
		$db->build_recursive_tree_path("tipos_novedades",$tipos,$nov["id_tipo_novedad"],"id","id_superior","tipos_novedades.nombre","-1","","clase!=3");

	$db->crear_select("SELECT id, (codigo || ' / ' || placa) FROM vehiculos WHERE id_centro = '".$nov["id_centro"]."' and id_estado!=4  ORDER BY codigo, placa",$vehiculos,$nov["id_vehiculo_apoyo"]);
	$db->crear_select("SELECT id, centro FROM centros WHERE id IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]') ORDER BY centro",$centros,$nov["id_centro"]);

	$newMode="actualizar";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/novedades/templates/novedades_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function actualizar($frm)
{
	global $CFG, $db,$ME;

	if($frm["clase"] == "mtto")
	{
		$idCentro = $db->sql_row("SELECT id_centro FROM mtto.equipos WHERE id=".$frm["id_equipo"]);
		$frm["id_centro"] = $idCentro["id_centro"];
	}	

	include($CFG->modulesdir . "/novedades.php");
	$entidad->loadValues($frm);
	$entidad->set("mode","update");
	$entidad->update();

	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function eliminar($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir . "/novedades.php");
	$entidad->set("mode","eliminar");
	$entidad->set("id",$frm["id"]);
	$entidad->delete();
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function observaciones($frm)
{
	global $db,$CFG,$ME;

	$db->sql_query("UPDATE novedades SET observaciones='".$frm["newValue"]."' WHERE id=".$frm["id"]);
	//$resp = '{"replyCode":201, "replyText":"Data follows","data:[{"motivo":"'.$frm["newValue"].'"}]}';
	return "ok";
}

function cerrar($id)
{
	global $db,$CFG,$ME;

	$db->sql_query("UPDATE novedades SET hora_fin='".date("Y-m-d H:i:s")."' WHERE id=".$id);
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}



function buscar($clase)
{
	global $CFG, $db,$ME;

	$user=$_SESSION[$CFG->sesion]["user"];

	$db->crear_select("SELECT id, nombre FROM mtto.equipos WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]') ORDER BY nombre",$equipos);
	$db->crear_select("SELECT id, (nombre || ' ' || apellido) FROM personas WHERE id IN (SELECT id_persona FROM personas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')) ORDER BY nombre,apellido",$personas_i);
	$db->crear_select("SELECT id, (nombre || ' ' || apellido) FROM personas WHERE id IN ( SELECT id_persona FROM personas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')) ORDER BY nombre,apellido
	",$personas_r);
	$db->crear_select("SELECT id, (codigo || ' / ' || placa) FROM vehiculos WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]') and id_estado!=4  ORDER BY codigo, placa",$vehiculos);
	$db->crear_select("SELECT id, centro FROM centros WHERE id IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]') ORDER BY centro",$centros);
	
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/novedades/templates/novedades_buscar_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function resultados($frm){
	GLOBAL $CFG, $ME, $entidad;

	$queryArray=array();
	foreach($frm AS $key=>$val){
		if($key!="mode" && $val!="" && $val!="%")
			array_push($queryArray,$key . "=" . $val);
	}
	$queryString=$ME . "?mode=listar_resultados&";
	$queryString.=implode("&",$queryArray);

	echo "<script>\n";
	echo "var url='" . $queryString . "';\n";
	echo "window.opener.location.href=url;\nwindow.close();\n</script>\n";
	echo "</script>\n";
}


function listar_resultados($frm)
{
	global $CFG, $db,$ME;

	$user=$_SESSION[$CFG->sesion]["user"];
	$cond = array("n.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')");

	$clase = nvl($frm["clase"],"");
	
	if(isset($frm["id_equipo"]))
		$cond[] = "n.id_equipo=".$frm["id_equipo"];
	if(isset($frm["hora_inicio_inicio"]) && isset($frm["hora_inicio_fin"]))
		$cond[] = "hora_inicio >='".$frm["hora_inicio_inicio"]."' AND hora_inicio <='".$frm["hora_inicio_fin"]."'";
	if(isset($frm["hora_fin_inicio"]) && isset($frm["hora_fin_fin"]))
		$cond[] = "hora_fin >='".$frm["hora_fin_inicio"]."' AND hora_fin <='".$frm["hora_fin_fin"]."'";
	if(isset($frm["id_ingresa"]))
		$cond[] = "n.id_ingresa='".$frm["id_ingresa"]."'";
	if(isset($frm["id_reporta"]))
		$cond[] = "n.id_reporta='".$frm["id_reporta"]."'";
	if(isset($frm["id_vehiculo_apoyo"]))
		$cond[] = "n.id_vehiculo_apoyo='".$frm["id_vehiculo_apoyo"]."'";
	if(isset($frm["id_centro"]))
		$cond[] = "n.id_centro='".$frm["id_centro"]."'";
	if(isset($frm["estado"]) && $frm["estado"]=="abierta")
		$cond[] = "n.hora_fin IS NULL";
	if(isset($frm["estado"]) && $frm["estado"]=="cerrada")
		$cond[] = "n.hora_fin IS NOT NULL";

	$camposAd = "";
	if($clase == "mtto")
	{
		$cond[] = "n.esquema = 'mtto'";
		$camposAd = ", array_to_string(array(
		SELECT mtto.rutinas.rutina||'/'||mtto.ordenes_trabajo.fecha_planeada
		FROM mtto.ordenes_trabajo
		LEFT JOIN mtto.rutinas ON mtto.ordenes_trabajo.id_rutina=mtto.rutinas.id
		WHERE mtto.ordenes_trabajo.id_novedad = n.id
		),', ') as ots";
	}
	else
	{
		if(isset($frm["esquema"]))
			$cond[] = "n.esquema = '".$frm["esquema"]."'";
		else
			$cond[] = "n.esquema != 'mtto'";
	}

	$datos = array();
	$cons = "SELECT n.id, e.nombre, to_char(n.hora_inicio,'YYYY/MM/DD') as fini, to_char(n.hora_inicio,'HH24:MI:SS') as hini, n.observaciones, case when hora_fin IS NULL then '<a href=\'javascript:edicion('||n.id||')\'><img alt=\'Editar\' src=\'".$CFG->wwwroot."/admin/iconos/transparente/iconoeditar.gif\' border=\'0\'></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\'javascript:cerrar('||n.id||')\'><img alt=\'Cerrar\' src=\'".$CFG->wwwroot."/admin/iconos/transparente/check_green.png\' border=\'0\'></a>' else '<a href=\'javascript:edicion('||n.id||')\'><img alt=\'Editar\' src=\'".$CFG->wwwroot."/admin/iconos/transparente/iconoeditar.gif\' border=\'0\'></a>' end as editar ".$camposAd."
		FROM novedades n
		LEFT JOIN mtto.equipos e ON e.id=n.id_equipo
		WHERE true AND ".implode(" and ",$cond)."
		ORDER BY n.hora_inicio";
	$qid = $db->sql_query($cons);
	while($query = $db->sql_fetchrow($qid))
	{
		$observaciones = str_replace('"',"",$query["observaciones"]);
		$observaciones = str_replace("'","",$observaciones);
		$observaciones = str_replace("\r\n","<br>",$observaciones);
		
		if(!in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["opciones_novedades"]))
			$query["editar"]="";

		if($clase == "mtto")
			$datos[] = '{id:"'.$query["id"].'", equipo:"'.$query["nombre"].'", fini:"'.$query["fini"].'", hini:"'.$query["hini"].'",  observaciones:"'.$observaciones.'", editar:"'.$query["editar"].'", ordenes:"'.$query["ots"].'" }';
		else
			$datos[] = '{id:"'.$query["id"].'", equipo:"'.$query["nombre"].'", fini:"'.$query["fini"].'", hini:"'.$query["hini"].'",  observaciones:"'.$observaciones.'", editar:"'.$query["editar"].'"}';
	}

	$ant = $sig = "";

	//mandar los datos para bajar a excel
	$queryArray=array();
	foreach($frm AS $key=>$val){
		if($key!="mode" && $val!="" && $val!="%")
			array_push($queryArray,$key . "=" . $val);
	}
	$excel=$ME . "?mode=bajar_excel&".implode("&",$queryArray)."&clase=".$clase;
			
	$titulo = "RESULTADOS ENCONTRADOS : ".$db->sql_numrows($qid);
	include($CFG->dirroot."/novedades/templates/listado_novedades.php");
}

function bajar_excel($frm)
{
	global $CFG, $db,$ME;
		
	$user=$_SESSION[$CFG->sesion]["user"];
	$cond = array("n.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')");

	$clase = $frm["clase"];
	
	if(isset($frm["id_equipo"]))
		$cond[] = "n.id_equipo=".$frm["id_equipo"];
	if(isset($frm["hora_inicio_inicio"]) && isset($frm["hora_inicio_fin"]))
		$cond[] = "hora_inicio >='".$frm["hora_inicio_inicio"]."' AND hora_inicio <='".$frm["hora_inicio_fin"]."'";
	if(isset($frm["hora_fin_inicio"]) && isset($frm["hora_fin_fin"]))
		$cond[] = "hora_fin >='".$frm["hora_fin_inicio"]."' AND hora_fin <='".$frm["hora_fin_fin"]."'";
	if(isset($frm["id_ingresa"]))
		$cond[] = "n.id_ingresa='".$frm["id_ingresa"]."'";
	if(isset($frm["id_reporta"]))
		$cond[] = "n.id_reporta='".$frm["id_reporta"]."'";
	if(isset($frm["id_vehiculo_apoyo"]))
		$cond[] = "n.id_vehiculo_apoyo='".$frm["id_vehiculo_apoyo"]."'";
	if(isset($frm["id_centro"]))
		$cond[] = "n.id_centro='".$frm["id_centro"]."'";
	if(isset($frm["estado"]) && $frm["estado"]=="abierta")
		$cond[] = "n.hora_fin IS NULL";
	if(isset($frm["estado"]) && $frm["estado"]=="cerrada")
		$cond[] = "n.hora_fin IS NOT NULL";

	$camposAd = "";
	if($clase == "mtto")
	{
		$cond[] = "n.esquema = 'mtto'";
		$camposAd = ", e.nombre as equipo, array_to_string(array(
		SELECT mtto.rutinas.rutina||'/'||mtto.ordenes_trabajo.fecha_planeada
		FROM mtto.ordenes_trabajo
		LEFT JOIN mtto.rutinas ON mtto.ordenes_trabajo.id_rutina=mtto.rutinas.id
		WHERE mtto.ordenes_trabajo.id_novedad = n.id
		),', ') as ots";
		$titulos = array("centro"=>"Centro", "id_tipo_novedad"=>"Tipo Novedad", "hora_inicio" => "Fecha Inicio", "hora_fin" => "Fecha Fin", "equipo"=>"Equipo", "observaciones" => "Observaciones", "id_reporta"=>"Reporta", "id_ingresa"=>"Ingresó Nov");
	}
	else
	{
		$cond[] = "n.esquema != 'mtto'";
		$camposAd = ", case when n.esquema='rec' then r.codigo||'/'||m.inicio else rb.codigo||'/'||b.inicio end as movimiento, v.codigo||'/'||v.placa as apoyo";
		$titulos = array("centro"=>"Centro", "id_tipo_novedad"=>"Tipo Novedad", "movimiento"=>"Movimiento", "hora_inicio" => "Fecha Inicio", "hora_fin" => "Fecha Fin", "observaciones" => "Observaciones",  "id_reporta"=>"Reporta", "id_ingresa"=>"Ingresó Nov", "apoyo"=>"Apoyo");
	}
	
	require_once $CFG->common_libdir."/writeexcel/class.writeexcel_workbook.inc.php";
	require_once $CFG->common_libdir."/writeexcel/class.writeexcel_worksheet.inc.php";

	$fname=$CFG->tmpdir."/novedades.xls";
	if(file_exists($fname))
		unlink($fname);

	$workbook = new writeexcel_workbook($fname);
	$workbook->set_tempdir($CFG->tmpdir);
	$worksheet = &$workbook->addworksheet("novedades");
	$worksheet->set_zoom(80);

	$style1 =& $workbook->addformat(array("align"=>"center","size"=>"11","border"=>"1","bold"=>"1"));
	$style2=& $workbook->addformat(array("size"=>"9","border"=>"1"));

	$columna=$fila=0;
	foreach($titulos as $texto)
	{
		$worksheet->write($fila,$columna,$texto,$style1);
		$columna++;
	}
	$fila++;

	$cons = "SELECT n.id, getPath(t.id,'tipos_novedades') as id_tipo_novedad, e.nombre, n.hora_inicio, n.observaciones, n.hora_fin, centro, rep.nombre||' '||rep.apellido as id_reporta, ing.nombre||' '||ing.apellido as id_ingresa".$camposAd."
		FROM novedades n
		LEFT JOIN mtto.equipos e ON e.id=n.id_equipo
		LEFT JOIN rec.movimientos m ON m.id=n.id_movimiento
		LEFT JOIN bar.movimientos b ON b.id=n.id_movimiento
		LEFT JOIN micros r ON r.id=m.id_micro
		LEFT JOIN micros rb ON rb.id=b.id_micro
		LEFT JOIN tipos_novedades t ON t.id = n.id_tipo_novedad
		LEFT JOIN vehiculos v ON v.id=n.id_vehiculo_apoyo
		LEFT JOIN centros c ON c.id=n.id_centro
		LEFT JOIN personas rep ON rep.id=n.id_reporta
		LEFT JOIN personas ing ON ing.id=n.id_ingresa
		WHERE true AND ".implode(" and ",$cond)."
		ORDER BY n.hora_inicio";
	$qid = $db->sql_query($cons);
	while($query = $db->sql_fetchrow($qid))
	{
		$columna = 0;
		foreach($titulos as $key => $texto)
		{
			$worksheet->write($fila,$columna,$query[$key],$style2);
			$columna++;
		}
		$fila++;
	}

	//FIN 
	$workbook->close();
	$nombreArchivo = "novedades.xls";
	header("Content-Type: application/x-msexcel; name=\"".$nombreArchivo."\"");
	header("Content-Disposition: inline; filename=\"".$nombreArchivo."\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	unlink($fname);
}


?>
