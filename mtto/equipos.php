<?
// echo "<pre>";
// print_r($_POST);
// print_r($_GET);
// echo "</pre>"; 
// error_reporting(E_ALL);
// ini_set("display_errors", 1);

include_once("../application.php");

if(!isset($_SESSION[$CFG->sesion]["user"])){
  $errorMsg="No existe la sesión.";
  error_log($errorMsg);
  die($errorMsg);
}
$user=$_SESSION[$CFG->sesion]["user"];

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

	case "hoja_vida":
		hoja_vida($_GET["id_equipo"], nvl($_GET["botonCerrar"],1), nvl($_GET["id_rutina"]), nvl($_GET["id_tipo"]), nvl($_GET["id_estado"]), nvl($_GET["id_sistema"]), nvl($_GET["nov_ab"]), nvl($_GET["nov_cer"]), nvl($_GET["calendario"], false));
	break;

	case "eliminarImagen":
		eliminarImagen($_GET);
	break;

	case "primera_vez":
		primera_vez($_GET);
	break;

	case "insertarPrimeraVez":
		insertarPrimeraVez($_POST);
	break;

	case "editar_primera_vez":
		editar_primera_vez($_GET);
	break;

	case "actualizarPrimeraVez":
		actualizarPrimeraVez($_POST);
	break;

	case "editar_archivo":
		editar_archivo($_GET["id"]);
	break;

	case "actualizar_archivo":
		actualizar_archivo($_POST);
	break;

	case "borrar_archivo":
		borrar_archivo($_GET);
	break;

	case "agregar_archivo":
		agregar_archivo($_GET["id_equipo"]);
	break;

	case "insertar_archivo":
		insertar_archivo($_POST);
	break;

	case "bajarOTXLS":
		bajarOTXLS($_GET);
	break;

	case "bajarNAXLS":
		bajarNOVXLS($_GET, true);
	break;

	case "bajarNCXLS":
		bajarNOVXLS($_GET, false);
	break;

}

function hoja_vida($idEquipo,$botonCerrar, $id_rutina="", $id_tipo="", $id_estado="", $id_sistema="", $nov_ab="", $nov_cer="", $vistaDesdeCalendario=false)
{
	global $CFG, $db,$ME;

	$equipo = $db->sql_row("SELECT getPath(g.id,'mtto.grupos') as grupo, e.nombre, e.codigo, e.serial, c.centro, e.kilometraje, e.horometro, e.mmdd_imagen_filename as imagen, e.id
			FROM mtto.equipos e
			LEFT JOIN mtto.grupos g ON g.id=e.id_grupo
			LEFT JOIN centros c ON c.id=e.id_centro
			WHERE e.id=".$idEquipo);
	
	$cond = "";
	if($id_rutina != '')
		$cond .= " AND r.id='".$id_rutina."'";
	if($id_tipo != '')
		$cond .= " AND t.id='".$id_tipo."'";
	if($id_estado != '')
		$cond .= " AND s.id='".$id_estado."'";
	if($id_sistema != '')
		$cond .= " AND st.id='".$id_sistema."'";
	

	//ordenes de trabajo
	$consulta = "SELECT o.id, r.id as id_rutina, r.rutina, to_char(o.fecha_planeada,'YYYY/MM/DD') as fecha_planeada, to_char(o.fecha_planeada,'HH24:MI:SS') as hora_planeada , 
	to_char(o.fecha_ejecucion_inicio,'YYYY/MM/DD HH24:MI:SS') as fecha_ejecucion_inicio, to_char(o.fecha_ejecucion_fin,'YYYY/MM/DD HH24:MI:SS') as fecha_ejecucion_fin, s.id as id_estado, s.estado, 
	'<img alt='|| chr(39)||'Editar'|| chr(39)||' src='|| chr(39)||'".$CFG->wwwroot."/admin/iconos/transparente/iconoeditar.gif' || chr(39)||' border=0>' as editar, 
	'<img alt='|| chr(39)||'Prioridad'|| chr(39)||' src='|| chr(39)||'".$CFG->wwwroot."/files/mtto.prioridades/imagen/p.id' || chr(39)|| 'border=0>' as prioridad, 
	case when o.fecha_ejecucion_inicio IS NULL AND o.fecha_planeada < now() then 
		'<img alt='|| chr(39)||'A tiempo'|| chr(39)||' src='|| chr(39)||'".$CFG->wwwroot."/admin/iconos/transparente/uncheck.png' || chr(39)||' border=0>'
	when o.fecha_ejecucion_inicio IS NOT NULL AND o.fecha_ejecucion_inicio > o.fecha_planeada then 
		'<img alt='|| chr(39)||'A tiempo'|| chr(39)||' src='|| chr(39)||'".$CFG->wwwroot."/admin/iconos/transparente/uncheck.png' || chr(39)||' border=0>' 
	else 
		'<img alt='|| chr(39)||'A tiempo'|| chr(39)||' src='|| chr(39)||'".$CFG->wwwroot."/admin/iconos/transparente/check_green.png' || chr(39)||' border=0>' 
	end as atiempo, 
	t.tipo, o.horometro, o.km, t.id as id_tipo, st.id as id_sistema, st.sistema
		FROM mtto.ordenes_trabajo o
		LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
		LEFT JOIN mtto.equipos e ON e.id=o.id_equipo
		LEFT JOIN mtto.estados_ordenes_trabajo s ON s.id=o.id_estado_orden_trabajo
		LEFT JOIN mtto.prioridades p ON p.id=r.id_prioridad
		LEFT JOIN mtto.tipos t ON t.id=r.id_tipo_mantenimiento
		LEFT JOIN mtto.sistemas st ON st.id = r.id_sistema
		WHERE o.id_equipo='".$equipo["id"]."'". $cond. "
		ORDER BY o.fecha_planeada";
	$qid = $db->sql_query($consulta);

	$selectRutinas = $selectTipos = $selectEstado = $selectSistema = array();
	$consultaDos = "SELECT o.id, r.id as id_rutina, r.rutina, s.id as id_estado, s.estado, t.tipo, t.id as id_tipo, st.id as id_sistema, st.sistema
		FROM mtto.ordenes_trabajo o
		LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
		LEFT JOIN mtto.equipos e ON e.id=o.id_equipo
		LEFT JOIN mtto.estados_ordenes_trabajo s ON s.id=o.id_estado_orden_trabajo
		LEFT JOIN mtto.tipos t ON t.id=r.id_tipo_mantenimiento
		LEFT JOIN mtto.sistemas st ON st.id = r.id_sistema
		WHERE o.id_equipo='".$equipo["id"]."'
		ORDER BY r.rutina";
	$qidRut = $db->sql_query($consultaDos);
	while($queryR = $db->sql_fetchrow($qidRut))
	{
		$selRutina = $selTipo = $selEstado = $selSistema = "";

		if($queryR["id_rutina"] == $id_rutina) $selRutina=" selected ";
		if($queryR["id_tipo"] == $id_tipo) $selTipo=" selected ";
		if($queryR["id_estado"] == $id_estado) $selEstado=" selected ";
		if($queryR["id_sistema"] == $id_sistema) $selSistema=" selected ";

		$selectRutinas[$queryR["id_rutina"]] = "<option value='".$queryR["id_rutina"]."' ".$selRutina.">".$queryR["rutina"]."</option>\n";
		$selectTipos[$queryR["id_tipo"]] = "<option value='".$queryR["id_tipo"]."' ".$selTipo.">".$queryR["tipo"]."</option>\n";
		$selectEstado[$queryR["id_estado"]] = "<option value='".$queryR["id_estado"]."' ".$selEstado.">".$queryR["estado"]."</option>\n";
		$selectSistema[$queryR["id_sistema"]] = "<option value='".$queryR["id_sistema"]."' ".$selSistema.">".$queryR["sistema"]."</option>\n";
	}
	$selectRutinas = "<select name='id_rutina'><option value=''>Todas...</option>". implode("", $selectRutinas)."</select>";
	$selectTipos = "<select name='id_tipo'><option value=''>Todos...</option>". implode("", $selectTipos)."</select>";
	$selectEstado = "<select name='id_estado'><option value=''>Todos...</option>". implode("", $selectEstado)."</select>";
	$selectSistema = "<select name='id_sistema'><option value=''>Todos...</option>". implode("", $selectSistema)."</select>";
	
	//archivos adjuntos
	#$qidAA = $db->sql_query("SELECT id, id_equipo, fecha, nombre, '<a href=\'".$CFG->wwwroot."/admin/file.php?table=mtto.equipos_archivos&field=archivo&id='||id||'\' class=\'link_verde\'>'||mmdd_archivo_filename||'</a>' as link FROM mtto.equipos_archivos WHERE id_equipo=".$equipo["id"]." ORDER BY fecha");

	//novedades abiertas
	$condNA = "";
	if(trim($nov_ab) != "")
		$condNA = " AND upper(n.observaciones) like '%".strtoupper($nov_ab)."%'";
		
	$qidNovAbiertas = $db->sql_query("SELECT n.*,  array_to_string(array(
			SELECT mtto.rutinas.rutina||'/'||mtto.ordenes_trabajo.fecha_planeada
			FROM mtto.ordenes_trabajo
			LEFT JOIN mtto.rutinas ON mtto.ordenes_trabajo.id_rutina=mtto.rutinas.id
			WHERE mtto.ordenes_trabajo.id_novedad = n.id
         ),', ') as ots
		FROM novedades n
		WHERE n.id_equipo =". $idEquipo." AND hora_fin IS NULL AND n.esquema = 'mtto' ".$condNA."
		ORDER BY hora_inicio");

	//novedades cerradas
	$condNC = "";
	if(trim($nov_cer) != "")
		$condNC = " AND upper(n.observaciones) like '%".strtoupper($nov_cer)."%'";
	$qidNovCerradas = $db->sql_query("SELECT n.*,  array_to_string(array(
			SELECT mtto.rutinas.rutina||'/'||mtto.ordenes_trabajo.fecha_planeada
			FROM mtto.ordenes_trabajo
			LEFT JOIN mtto.rutinas ON mtto.ordenes_trabajo.id_rutina=mtto.rutinas.id
			WHERE mtto.ordenes_trabajo.id_novedad = n.id
         ),', ') as ots
		FROM novedades n
		WHERE n.id_equipo =". $idEquipo." AND hora_fin IS NOT NULL AND n.esquema = 'mtto' ".$condNC."
		ORDER BY hora_inicio DESC");

	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/hoja_vida_equipo.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}



function agregar($frm=array())
{
	global $CFG, $db,$ME;

	$condicionCentro=$condicionCentroGr="true";
	$user=$_SESSION[$CFG->sesion]["user"];
	if($user["nivel_acceso"]!=1)
	{	
		$condicionCentro="id IN (" . implode(",",$user["id_centro"]) . ")";
		$condicionCentroGr="(id_centro IS NULL OR id_centro IN (" . implode(",",$user["id_centro"]) . "))";
	}

	$db->build_recursive_tree_path("mtto.grupos",$grupos,"","id","id_superior","nombre","-1","",$condicionCentroGr);
	if(isset($frm["id_grupo"]))
		$db->build_recursive_tree_path("mtto.grupos",$grupos,$frm["id_grupo"],"id","id_superior","nombre","-1","",$condicionCentroGr);
	$db->crear_select("SELECT id, codigo||'/'||placa FROM vehiculos WHERE ".$condicionCentroGr." ORDER BY codigo,placa",$vehiculos,"","");
	$db->build_recursive_tree_path("mtto.equipos",$select_equipos,"","id","id_superior","nombre","-1","",$condicionCentroGr);
	$db->crear_select("SELECT id, centro FROM centros WHERE ".$condicionCentro." ORDER BY centro",$centros);
	
	$newMode="insertar";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/equipos_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}


function insertar($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir . "/mtto.equipos.php");
	$entidad->loadValues($frm);
	$id=$entidad->insert();

	echo "<script>window.location.href='".$CFG->wwwroot."/mtto/equipos.php?mode=editar&id=".$id."';</script>";
}


function editar($idEquipo)
{
	global $CFG, $db,$ME;

	$equipo = $db->sql_row("SELECT * FROM mtto.equipos WHERE id=".$idEquipo);
	$user=$_SESSION[$CFG->sesion]["user"];

	$condicionCentro=$condicionCentroGr="true";
	$user=$_SESSION[$CFG->sesion]["user"];
	if($user["nivel_acceso"]!=1)
	{	
		$condicionCentro="id IN (" . implode(",",$user["id_centro"]) . ")";
		$condicionCentroGr="(id_centro IS NULL OR id_centro IN (" . implode(",",$user["id_centro"]) . "))";
	}

	$db->build_recursive_tree_path("mtto.grupos",$grupos,$equipo["id_grupo"],"id","id_superior","nombre","-1","",$condicionCentroGr);
	$db->crear_select("SELECT id, codigo||'/'||placa FROM vehiculos WHERE ".$condicionCentroGr." ORDER BY codigo,placa",$vehiculos,$equipo["id_vehiculo"],"");
	$db->build_recursive_tree_path("mtto.equipos",$select_equipos,$equipo["id_superior"],"id","id_superior","nombre","-1","",$condicionCentroGr);
	$db->crear_select("SELECT id, centro FROM centros WHERE ".$condicionCentro." ORDER BY centro",$centros,$equipo["id_centro"]);

	$newMode="actualizar";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/equipos_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function actualizar($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir . "/mtto.equipos.php");
	$entidad->loadValues($frm);
	$entidad->set("mode","update");
	$entidad->update();

	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function eliminarImagen($frm)
{
	global $CFG, $db,$ME;

	eliminarImagenBD("mtto.equipos","imagen",$frm["id"]);
	echo "<script>window.location.href='".$CFG->wwwroot."/mtto/equipos.php?mode=editar&id=".$frm["id"]."';</script>";
}

function primera_vez($frm)
{
	global $CFG, $db,$ME;

	$equipo = $db->sql_row("SELECT id, nombre, id_grupo, kilometraje as km, horometro as horo FROM mtto.equipos WHERE id =".$frm["id_equipo"]);

	$datos = array($equipo["id_grupo"]);
	obtenerIdsGrupos($equipo["id_grupo"],$datos);

	$rutinas = array();
	$qid = $db->sql_query("SELECT r.*, f.dias 
			FROM mtto.rutinas r 
			LEFT JOIN mtto.frecuencias f ON f.id=r.id_frecuencia 
			WHERE r.activa AND r.id_grupo IN (".implode(",",$datos).")
			ORDER BY r.rutina");
	while($query = $db->sql_fetchrow($qid))
	{	
		$num = $db->sql_row("SELECT count(*) as num FROM mtto.rutinas_primera_vez WHERE id_rutina='".$query["id"]."' AND id_equipo='".$equipo["id"]."'");
		$num2 = $db->sql_row("SELECT count(*) as num FROM mtto.ordenes_trabajo WHERE id_rutina='".$query["id"]."' AND id_equipo='".$equipo["id"]."'");
		if($num["num"] == 0 && $num2["num"])
			$rutinas[$query["id"]]=array("nombre"=>$query["rutina"], "dias"=>$query["dias"],"km"=>$query["frec_km"], "horo"=>$query["frec_horas"]);
	}

	$newMode = "insertarPrimeraVez";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/equipos_primera_vez.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}



function insertarPrimeraVez($frm)
{
	global $CFG, $db,$ME;

	$rutinas = array();
	foreach($frm as $key => $value)
	{
		if(preg_match("/fecha_/",$key,$match))
		{
			$id = str_replace("fecha_","",$key);
			if($value != "")
				$rutina[$id]["fecha"]=$value;
		}
		if(preg_match("/horo_/",$key,$match))
		{
			$id = str_replace("horo_","",$key);
			if($value != "")
				$rutina[$id]["horo"]=$value;
		}
		if(preg_match("/km_/",$key,$match))
		{
			$id = str_replace("km_","",$key);
			if($value != "")
				$rutina[$id]["km"]=$value;
		}
	}

	include($CFG->modulesdir . "/mtto.ordenes_trabajo.php");
	$actual = $db->sql_row("SELECT kilometraje as km, horometro as horo, '".date("Y-m-d")."' as fecha FROM mtto.equipos WHERE id=".$frm["id"]);
	foreach($rutina as $key => $valores)
	{
		$fechaPlaneada = calcular_fecha_planeada($frm["id"], nvl($valores["fecha"]), nvl($valores["horo"]), nvl($valores["km"]), $actual)." 08:00:00";

		$fecha = $km = $horometro = "null";
		if(isset($valores["fecha"]) && $valores["fecha"]!="") $fecha = "'".$valores["fecha"]."'";
		if(isset($valores["km"]) && $valores["km"]!="") $km = "'".$valores["km"]."'";
		if(isset($valores["horo"]) && $valores["horo"]!="") $horometro = "'".$valores["horo"]."'";

		$idOT = crearOrdenTrabajo($entidad,$key,$frm["id"],$fechaPlaneada,10);
		$consulta = "INSERT INTO mtto.rutinas_primera_vez (id_rutina,id_equipo,km,horometro,fecha,id_orden_trabajo,km_actual,horo_actual,fecha_actual) VALUES (".$key.",".$frm["id"].",".$km.", ".$horometro.", ".$fecha.", '".$idOT."', '".$actual["km"]."', '".$actual["horo"]."','".$actual["fecha"]."')";
		$db->sql_query($consulta);
	}

	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function editar_primera_vez($frm)
{
	global $CFG, $db,$ME;

	$equipo = $db->sql_row("SELECT id, nombre, id_grupo, kilometraje as km, horometro as horo FROM mtto.equipos WHERE id =".$frm["id_equipo"]);

	$rutinas = array();
	$qid = $db->sql_query("SELECT pv.*, r.rutina, f.dias, r.frec_km, r.frec_horas
			FROM mtto.rutinas_primera_vez pv
			LEFT JOIN mtto.ordenes_trabajo o ON o.id = pv.id_orden_trabajo
			LEFT JOIN mtto.rutinas r ON r.id=pv.id_rutina
			LEFT JOIN mtto.frecuencias f ON f.id=r.id_frecuencia
			WHERE pv.id_equipo='".$equipo["id"]."' AND r.activa AND o.id_estado_orden_trabajo=10 AND o.fecha_ejecucion_inicio IS NULL
			ORDER BY r.rutina");

	while($query = $db->sql_fetchrow($qid))
	{	
		$rutinas[$query["id"]]=array("dx"=>$query, "nombre"=>$query["rutina"], "dias"=>$query["dias"],"km"=>$query["frec_km"], "horo"=>$query["frec_horas"]);
	}

	$newMode = "actualizarPrimeraVez";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/equipos_editar_primera_vez.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function actualizarPrimeraVez($frm)
{
	global $CFG, $db,$ME;

	$rutinas = array();
	foreach($frm as $key => $value)
	{
		if(preg_match("/fecha_/",$key,$match))
		{
			$id = str_replace("fecha_","",$key);
			$rutina[$id]["fecha"]=trim($value);
		}
		if(preg_match("/horo_/",$key,$match))
		{
			$id = str_replace("horo_","",$key);
			$rutina[$id]["horometro"]=trim($value);
		}
		if(preg_match("/km_/",$key,$match))
		{
			$id = str_replace("km_","",$key);
			$rutina[$id]["km"]=trim($value);
		}
	}

	$kmyHoroAct = $db->sql_row("SELECT kilometraje as km, horometro as horo, '".date("Y-m-d")."' as fecha FROM mtto.equipos WHERE id=".$frm["id"]);
	foreach($rutina as $key => $valores)
	{
		$act = $db->sql_row("SELECT * FROM mtto.rutinas_primera_vez WHERE id=".$key);
		if(nvl($valores["km"]) != $act["km"] || nvl($valores["horometro"]) != $act["horometro"] || nvl($valores["fecha"]) != $act["fecha"])
		{
			if(nvl($valores["km"]) == "" && nvl($valores["horometro"]) == "" && nvl($valores["fecha"])  == "")
			  $db->sql_query("DELETE FROM mtto.ordenes_trabajo WHERE id=".$act["id_orden_trabajo"]);
			else
			{
				$fechaPlaneada = calcular_fecha_planeada($act["id_equipo"], nvl($valores["fecha"]), nvl($valores["horometro"]), nvl($valores["km"]), $kmyHoroAct)." 08:00:00";

				$fecha = $km = $horometro = "null";
				if(nvl($valores["fecha"])!="") $fecha = "'".$valores["fecha"]."'";
				if(nvl($valores["km"])!="") $km = "'".$valores["km"]."'";
				if(nvl($valores["horometro"])!="") $horometro = "'".$valores["horometro"]."'";

				$db->sql_query("UPDATE mtto.rutinas_primera_vez SET km = ".$km.", horometro=".$horometro.", fecha=".$fecha.", km_actual='".$kmyHoroAct["km"]."', horo_actual='".$kmyHoroAct["horo"]."', fecha_actual=now() WHERE id='".$key."'");
				$db->sql_query("UPDATE mtto.ordenes_trabajo SET fecha_planeada='".$fechaPlaneada."' WHERE id='".$act["id_orden_trabajo"]."'");
			}
		}
	}

	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}


function editar_archivo($idArchivo)
{
	global $CFG, $db,$ME;

	$archivo = $db->sql_row("SELECT * FROM mtto.equipos_archivos WHERE id=".$idArchivo);
	$newMode="actualizar_archivo";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/equipo_archivo_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function actualizar_archivo($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir . "/mtto.equipos_archivos.php");
	$entidad->loadValues($frm);
	$entidad->set("mode","update");
	$entidad->update();

	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function borrar_archivo($frm)
{
	global $CFG, $db,$ME;

	$db->sql_query("DELETE FROM mtto.equipos_archivos WHERE id=".$frm["id"]);
	echo "<script>window.location.href='".$CFG->wwwroot."/mtto/equipos.php?mode=hoja_vida&id_equipo=".$frm["id_equipo"]."';</script>";
}

function agregar_archivo($idEquipo)
{
	global $CFG, $db,$ME;

	$archivo = array("id_equipo"=>$idEquipo); 
	$newMode="insertar_archivo";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/equipo_archivo_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function insertar_archivo($frm)
{
	global $CFG, $db,$ME;
	
	include($CFG->modulesdir . "/mtto.equipos_archivos.php");
	$entidad->loadValues($frm);
	$id=$entidad->insert();
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}


function bajarOTXLS($frm)
{
	global $CFG, $db,$ME;

	$equipo = $db->sql_row("SELECT e.id, e.nombre, 	e.codigo
			FROM mtto.equipos e
			WHERE e.id=".$frm["id_equipo"]);
	
	$cond = "";
	if($frm["id_rutina"] != '')
		$cond .= " AND r.id='".$frm["id_rutina"]."'";
	if($frm["id_tipo"] != '')
		$cond .= " AND t.id='".$frm["id_tipo"]."'";
	if($frm["id_estado"] != '')
		$cond .= " AND s.id='".$frm["id_estado"]."'";
	if($frm["id_sistema"] != '')
		$cond .= " AND st.id='".$frm["id_sistema"]."'";

	$consulta = "SELECT o.id, r.id as id_rutina, r.rutina, to_char(o.fecha_planeada,'YYYY/MM/DD') as fecha_planeada, to_char(o.fecha_planeada,'HH24:MI:SS') as hora_planeada , to_char(o.fecha_ejecucion_inicio,'YYYY/MM/DD HH24:MI:SS') as fecha_ejecucion_inicio, to_char(o.fecha_ejecucion_fin,'YYYY/MM/DD HH24:MI:SS') as fecha_ejecucion_fin, s.id as id_estado, s.estado, 
	case when o.fecha_ejecucion_inicio IS NULL AND o.fecha_planeada < now() then 'NO' when o.fecha_ejecucion_inicio IS NOT NULL AND o.fecha_ejecucion_inicio > o.fecha_planeada then 'NO' else 'SÍ' end as atiempo, t.tipo, o.horometro, o.km
		FROM mtto.ordenes_trabajo o
		LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
		LEFT JOIN mtto.equipos e ON e.id=o.id_equipo
		LEFT JOIN mtto.estados_ordenes_trabajo s ON s.id=o.id_estado_orden_trabajo
		LEFT JOIN mtto.prioridades p ON p.id=r.id_prioridad
		LEFT JOIN mtto.tipos t ON t.id=r.id_tipo_mantenimiento
		LEFT JOIN mtto.sistemas st ON st.id = r.id_sistema
		WHERE o.id_equipo='".$equipo["id"]."'". $cond. "
		ORDER BY o.fecha_planeada";
	$qid = $db->sql_query($consulta);

	$titulos = array("¿A TIEMPO?", "HORA PLANEADA", "FECHA PLANEADA", "RUTINA", "TIPO", "EJECUCIÓN INICIO", "EJECUCIÓN FIN", "ESTADO", "KM", "HORO");
	$dx = array();
	while($query = $db->sql_fetchrow($qid))
	{
		$dx[] = array($query["atiempo"], $query["hora_planeada"], $query["fecha_planeada"], $query["rutina"], $query["tipo"], $query["fecha_ejecucion_inicio"], $query["fecha_ejecucion_fin"], $query["estado"], $query["km"], $query["horometro"] );
	}

	$stylos = array(1=>"txt_center", 2=>"txt_center", 3=>"txt_center", 4=>"txt_izq", 5=>"txt_center", 6=>"txt_izq", 7=>"txt_izq", 8=>"txt_center", 9=>"txt_izq",  10=>"txt_izq");
	imprimirXLS($titulos, $dx, "ordenes_".$equipo["nombre"], $stylos);
}

function bajarNOVXLS($frm, $abierta)
{
	global $CFG, $db,$ME;

	$equipo = $db->sql_row("SELECT e.id, e.nombre, 	e.codigo
		FROM mtto.equipos e
		WHERE e.id=".$frm["id_equipo"]);

	$cond = "";
	if(trim(nvl($frm["nov_ab"])) != "")
		$cond = " AND upper(n.observaciones) like '%".strtoupper($frm["nov_ab"])."%'";
	if(trim(nvl($frm["nov_cer"])) != "")
		$cond = " AND upper(n.observaciones) like '%".strtoupper($frm["nov_cer"])."%'";

	if($abierta)
		$cond.=" AND hora_fin IS NULL";
	else
		$cond.=" AND hora_fin IS NOT NULL";
		
	$qid = $db->sql_query("SELECT n.*,  array_to_string(array(
			SELECT mtto.rutinas.rutina||'/'||mtto.ordenes_trabajo.fecha_planeada
			FROM mtto.ordenes_trabajo
			LEFT JOIN mtto.rutinas ON mtto.ordenes_trabajo.id_rutina=mtto.rutinas.id
			WHERE mtto.ordenes_trabajo.id_novedad = n.id
         ),', ') as ots
		FROM novedades n
		WHERE n.id_equipo =". $frm["id_equipo"]."  AND n.esquema = 'mtto' ".$cond."
		ORDER BY hora_inicio DESC");

	$dx = array();
	$titulos = array("FECHA INICIO", "FECHA FIN", "OBSERVACIONES", "ÓRDENES");
	while($query = $db->sql_fetchrow($qid))
	{
		$dx[] = array($query["hora_inicio"], $query["hora_fin"], $query["observaciones"], $query["ots"]);
	}

	$stylos = array(1=>"txt_center", 2=>"txt_center", 3=>"txt_izq", 4=>"txt_izq");
	imprimirXLS($titulos, $dx, "novedades_".$equipo["nombre"], $stylos);
}


?>