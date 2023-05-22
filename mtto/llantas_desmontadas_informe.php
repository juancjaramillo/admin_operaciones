<?
include_once("../application.php");

if(!isset($_SESSION[$CFG->sesion]["user"])){
  $errorMsg="No existe la sesión.";
  error_log($errorMsg);
  die($errorMsg);
}
$user=$_SESSION[$CFG->sesion]["user"];
verificarPagina(simple_me($ME));

$mode=nvl($_GET["mode"],nvl($_POST["mode"],""));

switch(nvl($mode)){

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

	case "resultados":
	  resultados($_POST);
	break;

	case "listar_resultados":
		  listar_resultados($_GET);
	break;

	case "listar_movimientos":
		listar_movimientos(nvl($_GET["id"]));
	break;

	case "eliminar_movimiento":
		eliminar_movimiento($_GET["id"],nvl($_GET["popup"]));
	break;

	case "editar_movimiento":
		editar_movimiento($_GET["id"],nvl($_GET["popup"]));
	break;

	case "detalles_movimiento":
		detalles_movimiento($_GET["id"],nvl($_GET["popup"]));
	break;

	case "agregar_movimiento":
		agregar_movimiento($_GET["id_llanta"],nvl($_GET["facil"],false));
	break;

	case "insertar_movimiento":
		insertar_movimiento($_POST);
	break;

	case "actualizar_movimiento":
		actualizar_movimiento($_POST);
	break;

	case "ingreso_movimiento_facil":
		ingreso_movimiento_facil($_POST);
	break;

	case "llantas_desmontadas_informe":
		llantas_desmontadas_informe();
	break;

	default:
		listado_llantas(nvl($_GET["id_estado"]));
	break;
}

function listado_llantas($idEstado="")
{
	global $CFG, $db,$ME;

	$user=$_SESSION[$CFG->sesion]["user"];
	$condicion="l.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')";

	$titulo = "LLANTAS";

	if($idEstado != "")
	{
		$est = $db->sql_row("SELECT * FROM llta.estados WHERE id=".$idEstado);
		$titulo = "LLANTA ".strtoupper($est["nombre"]);
		$condicion.=" AND l.id_estado=".$idEstado;
	}

	$datos = array();
	$cons = "SELECT l.id, c.centro, l.numero,  l.disenio,tl.tipo,m.marca||' / '||r.dimension as dimension, p.razon as proveedor, (v.codigo || '/' || v.placa) as vehiculo,
		(select CASE WHEN id_tipo_movimiento='6' THEN null WHEN id_tipo_movimiento='3' THEN null ELSE posicion END as posicion from llta.movimientos WHERE id_llanta=l.id ORDER BY fecha DESC LIMIT 1) as posicion,
	 	'<a href=|| chr(39)||javascript:edicion('||l.id||')|| chr(39)||><img alt=|| chr(39)||Editar|| chr(39)|| src=|| chr(39)||".$CFG->wwwroot."/admin/iconos/transparente/iconoeditar.gif|| chr(39)|| border=|| chr(39)||0|| chr(39)||></a>&nbsp;&nbsp;&nbsp;<a href=|| chr(39)||javascript:movimientos('||l.id||')|| chr(39)||><img alt=|| chr(39)||Movimientos|| chr(39)|| src=|| chr(39)||".$CFG->wwwroot."/admin/iconos/transparente/icon-overview.gif|| chr(39)|| border=|| chr(39)||0|| chr(39)||></a>' as opciones
		FROM llta.llantas l
		LEFT JOIN llta.dimensiones r ON r.id=l.id_dimension
		LEFT JOIN llta.marcas m ON m.id=r.id_marca
		LEFT JOIN llta.tipos_llantas tl ON tl.id=l.id_tipo_llanta
		LEFT JOIN llta.proveedores p ON p.id=l.id_proveedor
	 	LEFT JOIN	vehiculos v ON l.id_vehiculo=v.id
		LEFT JOIN centros c ON c.id=l.id_centro
		WHERE ".$condicion."
		ORDER BY l.numero";

	$qid = $db->sql_query($cons);
	while($query = $db->sql_fetchrow($qid))
	{
		$datos[] = '{id:"'.$query["id"].'", centro:"'.$query["centro"].'", numero:"'.$query["numero"].'", disenio:"'.$query["disenio"].'", tipo:"'.$query["tipo"].'", dimension:"'.$query["dimension"].'",  proveedor:"'.$query["proveedor"].'", vehiculo:"'.$query["vehiculo"].'",  posicion:"'.$query["posicion"].'", opciones:"'.$query["opciones"].'"}';
	}

	include("templates/listado_llantas.php");
}

function llantas_desmontadas_informe()
{
	global $CFG, $db,$ME;

	$user=$_SESSION[$CFG->sesion]["user"];
	$condicion="l.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')";

	$strQuery="
	SELECT m.id as id_marca, ll.numero as calor,
	m.marca,
	ll.disenio,
	(SELECT fecha FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento='5' AND id_vehiculo=veh.id ORDER BY fecha ASC LIMIT 1) AS fecha_montaje,
	(SELECT fecha FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento IN('2','6') AND id_vehiculo=veh.id ORDER BY fecha DESC LIMIT 1) AS fecha_inspeccion,
	(SELECT km FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento='5' AND id_vehiculo=veh.id ORDER BY fecha ASC LIMIT 1) AS km_ontaje,
	(SELECT km FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento IN('2','6') AND id_vehiculo=veh.id ORDER BY fecha DESC LIMIT 1) AS km_inspecc,
	(SELECT km FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento IN('2','6') AND id_vehiculo=veh.id ORDER BY fecha DESC LIMIT 1) - (SELECT km FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento='5' AND id_vehiculo=veh.id ORDER BY fecha ASC LIMIT 1) AS km_recorridos,
	(SELECT prof_uno FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento='5' AND id_vehiculo=veh.id ORDER BY fecha ASC LIMIT 1) AS prof_inicial,
	(SELECT prof_uno FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento IN('2','6') AND id_vehiculo=veh.id ORDER BY fecha DESC LIMIT 1) AS prof_revision,
	COALESCE((
		SELECT costo
		FROM llta.movimientos
		WHERE id_llanta=ll.id AND id_tipo_movimiento='1'
			AND fecha<(SELECT fecha FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento='5' AND id_vehiculo=veh.id ORDER BY fecha ASC LIMIT 1)
		ORDER BY fecha DESC LIMIT 1
		),ll.costo) AS costo_llanta
	FROM (SELECT DISTINCT id_llanta,id_vehiculo FROM llta.movimientos WHERE id_vehiculo IS NOT NULL) as dl
		LEFT JOIN llta.llantas ll ON dl.id_llanta=ll.id 
		LEFT JOIN vehiculos veh ON dl.id_vehiculo=veh.id
		LEFT JOIN llta.dimensiones dim ON ll.id_dimension=dim.id
		LEFT JOIN llta.marcas m ON dim.id_marca=m.id
	WHERE ll.id_centro IN (" . implode(",",$user["id_centro"]) . ") AND ll.id_estado=2
	ORDER BY m.marca, ll.disenio";

	$qid = $db->sql_query($strQuery);
	$datosPrin = $datos = $marcas = array();
	while($query = $db->sql_fetchrow($qid))
	{
		$mmut = $query["prof_revision"]-3;
		@$kmrg = $query["km_recorridos"]/($query["prof_inicial"]-$query["prof_revision"]);
		@$pryrend = ($mmut*$kmrg)+$query["km_recorridos"];
		if(!isset($datosPrin[$query["id_marca"]][$query["disenio"]]["costo"])) $datosPrin[$query["id_marca"]][$query["disenio"]]["costo"]=0;
		if(!isset($datosPrin[$query["id_marca"]][$query["disenio"]]["pyr"])) $datosPrin[$query["id_marca"]][$query["disenio"]]["pyr"]=0;
		$datosPrin[$query["id_marca"]][$query["disenio"]]["costo"]+=$query["costo_llanta"];
		$datosPrin[$query["id_marca"]][$query["disenio"]]["pyr"]+=$pryrend;
		$marcas[$query["id_marca"]] = $query["marca"];
	}
	
	foreach($datosPrin as $idMarca => $grupo)
	{
		foreach($grupo as $key => $dx)
		{
			@$total = $dx["costo"] / $dx["pyr"];
			$datos[] = '{marca:"'.$marcas[$idMarca].'", disenio:"'.$key.'", total:"'.number_format($total, 2, ",", ".").'"}';
		}
	}

	$titulo = "LLANTAS";
	include("templates/listado_llantas_desmontadas.php");
}




function agregar()
{
	global $CFG, $db,$ME;

	$condicion="";
	$user=$_SESSION[$CFG->sesion]["user"];
	$condicion=" AND id IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')";

	$db->crear_select("SELECT id, centro FROM centros WHERE true ".$condicion." ORDER BY centro",$centros);
	$db->crear_select("SELECT id, marca FROM llta.marcas ORDER BY marca",$marcas,"","");
	$db->crear_select("SELECT id, tipo FROM llta.tipos_llantas ORDER BY tipo",$tipos,"","");
	$db->crear_select("SELECT id, nombre FROM llta.estados ORDER BY nombre",$estados,2,"");

	$newMode="insertar";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/llantas_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function insertar($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir . "/llta.llantas.php");
	$entidad->loadValues($frm);
	$id=$entidad->insert();

	//agregar_movimiento ingreso:
	$mov = array("id_llanta"=>$id, "fecha"=>$frm["fecha_compra"], "id_tipo_movimiento"=>4);
	include($CFG->modulesdir . "/llta.movimientos.php");
	$entidad->loadValues($mov);
	$entidad->insert();

	if($frm["id_vehiculo"]!="%" && $frm["id_vehiculo"]!=""){
		//agregar_movimiento montaje:
		$mov = array("id_llanta"=>$id, "fecha"=>$frm["fecha_compra"], "id_tipo_movimiento"=>5, "id_vehiculo"=>$frm["id_vehiculo"], "posicion"=>$frm["posicion"],"km"=>$frm["km"]);
		$entidad->loadValues($mov);
		$entidad->insert();
	}

	$goto = $CFG->wwwroot."/mtto/llantas.php?mode=editar&id=".$id;
	echo "<script>window.location.href='".$goto."';</script>";
}

function editar($idLlanta)
{
	global $CFG, $db,$ME;

	$condicion="";
	$user=$_SESSION[$CFG->sesion]["user"];
	$condicion=" AND id IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')";

	$llanta = $db->sql_row("SELECT l.*, (v.codigo || '/' || v.placa) as vehiculo, d.id_marca 
			FROM llta.llantas l 
			LEFT JOIN vehiculos v ON l.id_vehiculo=v.id 
			LEFT JOIN llta.dimensiones d ON d.id=l.id_dimension
			WHERE l.id=".$idLlanta);
	$db->crear_select("SELECT id, centro FROM centros WHERE true ".$condicion." ORDER BY centro",$centros,$llanta["id_centro"]);
	$db->crear_select("SELECT id, tipo FROM llta.tipos_llantas ORDER BY tipo",$tipos,$llanta["id_tipo_llanta"],"");
	$db->crear_select("SELECT id, dimension FROM llta.dimensiones WHERE id_marca='".$llanta["id_marca"]."' ORDER BY dimension",$dimensiones,$llanta["id_dimension"],"");
	$db->crear_select("SELECT p.id, p.razon FROM llta.proveedores_centros pc LEFT JOIN llta.proveedores p ON p.id=pc.id_proveedor WHERE pc.id_centro='".$llanta["id_centro"]."' ORDER BY razon",$proveedores,$llanta["id_proveedor"],"");
	$db->crear_select("SELECT id, marca FROM llta.marcas ORDER BY marca",$marcas,$llanta["id_marca"],"");
	$db->crear_select("SELECT id, nombre FROM llta.estados WHERE id='$llanta[id_estado]' ORDER BY nombre",$estados,$llanta["id_estado"],"");

	$newMode="actualizar";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/llantas_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function actualizar($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir . "/llta.llantas.php");
	$entidad->loadValues($frm);
	$entidad->set("mode","update");
	$entidad->update();

	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function eliminar($frm)
{
	global $CFG, $db,$ME;

	$db->sql_query("DELETE FROM llta.llantas WHERE id=".$frm["id"]);
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
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

	$cond = array("true");

	$user=$_SESSION[$CFG->sesion]["user"];
	$cond[]="  c.id IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')";


	if(isset($frm["id_centro"]))
		$cond[] = "c.id=".$frm["id_centro"];
	if(isset($frm["numero"]))
		$cond[] = "upper(l.numero) LIKE '%".strtoupper($frm["numero"])."%'";
	if(isset($frm["id_marca"]))
		$cond[] = "r.id_marca=".$frm["id_marca"];
	if(isset($frm["id_dimension"]))
		$cond[] = "l.id_dimension=".$frm["id_dimension"];
	if(isset($frm["disenio"]))
		$cond[] = "upper(l.disenio) LIKE '%".strtoupper($frm["disenio"])."%'";
	if(isset($frm["id_tipo_llanta"]))
		$cond[] = "l.id_tipo_llanta=".$frm["id_tipo_llanta"];
	if(isset($frm["id_proveedor"]))
		$cond[] = "l.id_proveedor=".$frm["id_proveedor"];
	if(isset($frm["fecha_compra_inicial"]) && isset($frm["fecha_compra_final"]))
		$cond[] = "l.fecha_compra>='".$frm["fecha_compra_inicial"]."' AND l.fecha_compra<='".$frm["fecha_compra_final"]."'";
	if(isset($frm["dot"]))
		$cond[] = "upper(l.dot) LIKE '%".strtoupper($frm["dot"])."%'";
	if(isset($frm["matricula"]))
		$cond[] = "upper(l.matricula) LIKE '%".strtoupper($frm["matricula"])."%'";
	if(isset($frm["id_vehiculo"]))
		$cond[] = "l.id_vehiculo=".$frm["id_vehiculo"];
	if(isset($frm["km_inicial"]) && isset($frm["km_final"]))
		$cond[] = "l.km>='".$frm["km_inicial"]."' AND l.km<='".$frm["km_final"]."'";
	if(isset($frm["vida"]))
		$cond[] = "l.vida='".$frm["vida"]."'";
	if(isset($frm["id_estado"]))
		$cond[] = "l.id_estado=".$frm["id_estado"];

	$datos = array();
	$cons = "SELECT l.id, c.centro, l.numero, l.disenio,tl.tipo,m.marca||' / '||r.dimension as dimension, p.razon as proveedor, (v.codigo || '/' || v.placa) as vehiculo,(select CASE WHEN id_tipo_movimiento='6' THEN null WHEN id_tipo_movimiento='3' THEN null ELSE posicion END as posicion from llta.movimientos WHERE id_llanta=l.id ORDER BY fecha DESC LIMIT 1) as posicion, '<a href=|| chr(39)||javascript:edicion('||l.id||')|| chr(39)||><img alt=|| chr(39)||Editar|| chr(39)|| src=|| chr(39)||".$CFG->wwwroot."/admin/iconos/transparente/iconoeditar.gif|| chr(39)|| border=|| chr(39)||0|| chr(39)||></a>&nbsp;&nbsp;&nbsp;<a href=|| chr(39)||javascript:movimientos('||l.id||')|| chr(39)||><img alt=|| chr(39)||Movimientos|| chr(39)|| src=|| chr(39)||".$CFG->wwwroot."/admin/iconos/transparente/icon-overview.gif|| chr(39)|| border=|| chr(39)||0|| chr(39)||></a>' as opciones
		FROM llta.llantas l
		LEFT JOIN llta.dimensiones r ON r.id=l.id_dimension
		LEFT JOIN llta.marcas m ON m.id=r.id_marca
		LEFT JOIN llta.tipos_llantas tl ON tl.id=l.id_tipo_llanta
		LEFT JOIN llta.proveedores p ON p.id=l.id_proveedor
	 	LEFT JOIN	vehiculos v ON l.id_vehiculo=v.id
		LEFT JOIN centros c ON c.id=l.id_centro
		WHERE ".implode(" AND ",$cond)."
		ORDER BY l.numero";
	$qid = $db->sql_query($cons);
	while($query = $db->sql_fetchrow($qid))
	{
		$datos[] = '{id:"'.$query["id"].'", centro:"'.$query["centro"].'", numero:"'.$query["numero"].'", disenio:"'.$query["disenio"].'", tipo:"'.$query["tipo"].'", dimension:"'.$query["dimension"].'",  proveedor:"'.$query["proveedor"].'", vehiculo:"'.$query["vehiculo"].'",posicion:"'.$query["posicion"].'",  opciones:"'.$query["opciones"].'"}';
	}
	
	$titulo = "RESULTADOS ENCONTRADOS ".$db->sql_numrows($qid);
	include("templates/listado_llantas.php");
}


function listar_movimientos($idLLanta)
{
	global $CFG, $db,$ME;

	$user=$_SESSION[$CFG->sesion]["user"];

	$datos = array();
	$cond = "m.id_llanta IN (SELECT id FROM llta.llantas WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]'))";
	$titulo = "MOVIMIENTOS";
	$popup = false;
	$ante = "pop_";
	if($idLLanta != "")
	{
		$cond.= " AND m.id_llanta=".$idLLanta;
		$llanta = $db->sql_row("SELECT numero FROM llta.llantas WHERE id=".$idLLanta);
		$titulo = "MOVIMIENTOS DE LA LLANTA ".$llanta["numero"];
		$popup = true;
		$ante = "";
	}

	$i=0;
	$cons = "SELECT m.id, m.fecha, tm.tipo, m.km, m.horas, (v.codigo || '/' || v.placa) as vehiculo, m.posicion, '<a href=|| chr(39)||javascript:".$ante."detalles_movimiento('||m.id||')|| chr(39)||><img alt=|| chr(39)||Detalles|| chr(39)|| src=|| chr(39)||".$CFG->wwwroot."/admin/iconos/transparente/file2.gif|| chr(39)|| border=|| chr(39)||0|| chr(39)|| title=|| chr(39)||Detalles|| chr(39)||></a>' as detalles, '&nbsp;&nbsp;&nbsp;<a href=|| chr(39)||javascript:".$ante."editar_movimiento('||m.id||')|| chr(39)||><img alt=|| chr(39)||Editar|| chr(39)|| src=|| chr(39)||".$CFG->wwwroot."/admin/iconos/transparente/iconoeditar.gif|| chr(39)|| border=|| chr(39)||0|| chr(39)|| title=|| chr(39)||Editar|| chr(39)||></a>' as editar, '&nbsp;&nbsp;&nbsp;<a href=|| chr(39)||javascript:".$ante."eliminar_movimiento('||m.id||')|| chr(39)||><img alt=|| chr(39)||Eliminar|| chr(39)|| src=|| chr(39)||".$CFG->wwwroot."/admin/iconos/transparente/icon-erase.gif|| chr(39)|| border=|| chr(39)||0|| chr(39)|| title=|| chr(39)||Eliminar|| chr(39)||></a>' as eliminar
		FROM llta.movimientos m
		LEFT JOIN llta.tipos_movimientos tm ON tm.id=m.id_tipo_movimiento
		LEFT JOIN vehiculos v ON m.id_vehiculo=v.id
		WHERE ".$cond."
		ORDER BY m.fecha DESC, m.id DESC";
	$qid = $db->sql_query($cons);
	while($query = $db->sql_fetchrow($qid))
	{
		if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["eliminarAgregarMovimientoLlanta"]))
		{
			if($i == 0)
				$opciones = $query["detalles"].$query["editar"].$query["eliminar"];
			else
				$opciones = $query["detalles"];
		}
		else
			$opciones = $query["detalles"];
			
		if($idLLanta == "")
			$opciones = $query["detalles"];

		$datos[] = '{id:"'.$query["id"].'", fecha:"'.$query["fecha"].'", tipo:"'.$query["tipo"].'", km:"'.$query["km"].'", horas:"'.$query["horas"].'", vehiculo:"'.$query["vehiculo"].'", posicion:"'.$query["posicion"].'",  opciones:"'.$opciones.'"}';
		$i++;
	}

	include("templates/listado_movimientos_llantas.php");
}

function editar_movimiento($idMov,$popup="")
{
	global $CFG, $db,$ME;

	$mov = $db->sql_row("SELECT m.*, l.numero, l.id_centro FROM llta.movimientos m LEFT JOIN llta.llantas l ON l.id=m.id_llanta WHERE m.id=".$idMov);
	$db->crear_select("SELECT id, tipo FROM llta.tipos_movimientos ORDER BY tipo",$tipos,$mov["id_tipo_movimiento"]);
	$db->crear_select("SELECT id, subtipo FROM llta.subtipos_movimientos WHERE id_tipo_movimiento='".$mov["id_tipo_movimiento"]."' ORDER BY subtipo",$subtipos,$mov["id_subtipo_movimiento"],"");
	$db->crear_select("SELECT id, (codigo || '/' || placa) as nombre FROM vehiculos WHERE id_centro='".$mov["id_centro"]."' ORDER BY codigo,placa",$vehiculos,$mov["id_vehiculo"],"");

	if($mov["id_vehiculo"]!="")
		$posiciones= opcionesPosicionesLlantas($mov["id_vehiculo"],$mov["posicion"]);

	$newMode="actualizar_movimiento";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/movimiento_llantas_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function detalles_movimiento($idMov,$popup="")
{
	global $CFG, $db,$ME;

	$mov = $db->sql_row("SELECT m.*, l.numero, l.id_centro FROM llta.movimientos m LEFT JOIN llta.llantas l ON l.id=m.id_llanta WHERE m.id=".$idMov);
	$db->crear_select("SELECT id, tipo FROM llta.tipos_movimientos ORDER BY tipo",$tipos,$mov["id_tipo_movimiento"]);
	$db->crear_select("SELECT id, subtipo FROM llta.subtipos_movimientos WHERE id_tipo_movimiento='".$mov["id_tipo_movimiento"]."' ORDER BY subtipo",$subtipos,$mov["id_subtipo_movimiento"],"");
	$db->crear_select("SELECT id, (codigo || '/' || placa) as nombre FROM vehiculos WHERE id_centro='".$mov["id_centro"]."' ORDER BY codigo,placa",$vehiculos,$mov["id_vehiculo"],"");

	if($mov["id_vehiculo"]!="")
		$posiciones= opcionesPosicionesLlantas($mov["id_vehiculo"],$mov["posicion"]);

	$newMode="detalles_movimiento";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/movimiento_llantas_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function eliminar_movimiento($idMov,$popup="")
{
	global $CFG, $db,$ME;

	$idLlanta = $db->sql_row("SELECT id_llanta FROM llta.movimientos WHERE id=".$idMov);
	$db->sql_query("DELETE FROM llta.movimientos WHERE id=".$idMov);
	echo "<script>window.alert('Debe chequear el estado de la llanta,  si quedó montada o desmontada.')</script>";
	
	if($popup=="")
		echo "<script>window.location.href='".$CFG->wwwroot."/mtto/llantas.php?mode=listar_movimientos&id=".$idLlanta["id_llanta"]."';</script>";
	else
		echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
} 


function agregar_movimiento($idLlanta,$facil)
{
	global $CFG, $db,$ME;

	$llanta = $db->sql_row("SELECT id_centro,numero,id_vehiculo,km,id_estado,posicion FROM llta.llantas WHERE id=".$idLlanta);
	$mov= array("id_llanta"=>$idLlanta, "numero"=>$llanta["numero"],"km"=>$llanta["km"],"id_vehiculo"=>$llanta["id_vehiculo"]);
	$db->crear_select("SELECT id, tipo FROM llta.tipos_movimientos WHERE id IN(SELECT id_tipo_movimiento FROM llta.estados_tiposmovimiento WHERE id_estado='$llanta[id_estado]') ORDER BY tipo",$tipos);
	$db->crear_select("SELECT id, (codigo || '/' || placa) as nombre FROM vehiculos WHERE id_centro='".$llanta["id_centro"]."' ORDER BY codigo,placa",$vehiculos,$llanta["id_vehiculo"],"");

	if($mov["id_vehiculo"]!="")
		$posiciones= opcionesPosicionesLlantas($mov["id_vehiculo"],$llanta["posicion"]);

	$newMode="insertar_movimiento";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/movimiento_llantas_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}


function actualizar_movimiento($frm)
{
	global $CFG, $db,$ME;
	
	include($CFG->modulesdir . "/llta.movimientos.php");
	$entidad->loadValues($frm);
	$entidad->set("mode","update");
	$entidad->update();

	$goto = $CFG->wwwroot."/mtto/llantas.php?mode=listar_movimientos&id=".$frm["id_llanta"];
	echo "<script>window.location.href='".$goto."';</script>";
}

function insertar_movimiento($frm)
{
	global $CFG, $db,$ME;
	
	include($CFG->modulesdir . "/llta.movimientos.php");
	$entidad->loadValues($frm);
	$id=$entidad->insert();
	$tipo_movimiento=$db->sql_row("SELECT * FROM llta.tipos_movimientos WHERE id='$frm[id_tipo_movimiento]'");
	if($tipo_movimiento["id_estado"]!=""){
		if($tipo_movimiento["id_estado"]==1){
			$id_vehiculo="'" . $frm["id_vehiculo"] . "'";
			$posicion="'" . $frm["posicion"] . "'";
		}
		elseif($tipo_movimiento["id_estado"]==2){
			$id_vehiculo="NULL";
			$posicion="NULL";
		}
		
		$actualizar = "";
		if(str_replace("'","",$id_vehiculo) != "%") $actualizar.= ", id_vehiculo=".$id_vehiculo;
		if(str_replace("'","",$posicion) != "%") $actualizar.= ", posicion=".$posicion;
		$cons = "UPDATE llta.llantas SET id_estado='".$tipo_movimiento["id_estado"]."' ".$actualizar." WHERE id='".$frm["id_llanta"]."'";
		$qUpdate=$db->sql_query($cons);
	}

	$goto = $CFG->wwwroot."/mtto/llantas.php?mode=listar_movimientos&id=".$frm["id_llanta"];
	echo "<script>window.location.href='".$goto."';</script>";
}

function ingreso_movimiento_facil($llanta)
{
	global $CFG, $db,$ME;

	$user=$_SESSION[$CFG->sesion]["user"];
	$cond=" AND l.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')";

	if($llanta["numero"] != "")
		$cond .= " AND upper(numero) LIKE '%".strtoupper($llanta["numero"])."%'";
	if($llanta["id_vehiculo"] != "%")
		$cond .= " AND id_vehiculo= '".$llanta["id_vehiculo"]."'";

	$consulta = "SELECT l.id, l.numero, l.disenio,tl.tipo,m.marca||' / '||r.dimension as dimension, (v.codigo || '/' || v.placa) as vehiculo,l.posicion
		FROM llta.llantas l
		LEFT JOIN llta.dimensiones r ON r.id=l.id_dimension
		LEFT JOIN llta.marcas m ON m.id=r.id_marca
		LEFT JOIN llta.tipos_llantas tl ON tl.id=l.id_tipo_llanta
	 	LEFT JOIN	vehiculos v ON l.id_vehiculo=v.id
		WHERE true ".$cond."
		ORDER BY l.numero";

	$qid = $db->sql_query($consulta);
	if($db->sql_numrows($qid) == 0)
	{
		$error = true;
		include($CFG->dirroot."/mtto/templates/ingreso_movimiento_facil_form.php");
	}elseif($db->sql_numrows($qid) == 1)
	{
		$llan = $db->sql_fetchrow($qid);
		$goto = $CFG->wwwroot."/mtto/llantas.php?mode=agregar_movimiento&id_llanta=".$llan["id"]."&facil=true";
		echo "<script>window.location.href='".$goto."';</script>";
	}else
	{
		include($CFG->dirroot."/templates/header_popup.php");
		?>
		<table width="100%">
			<tr>
				<td height="50" width="80%" valign="middle" class="azul_16" align="center">RESULTADOS</td>
			</tr>
		</table>
		<table width="100%">
			<tr>
				<td valign="top">
					<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
						<tr>
							<td>
								<table width="100%" border=1 bordercolor="#7fa840">
									<tr>
										<td width="20%" align="center" class="casillatext">NÚMERO</td>
										<td width="10%" align="center" class="casillatext">DISEÑO</td>
										<td width="10%" align="center" class="casillatext">TIPO</td>
										<td width="30%" align="center" class="casillatext">MARCA / DIMENSIÓN</td>
										<td width="10%" align="center" class="casillatext">VEHÍCULO</td>
										<td width="10%" align="center" class="casillatext">POSICIÓN</td>
										<td width="10%" align="center" class="casillatext">OPCIONES</td>
									</tr>
									<?
									while($llan = $db->sql_fetchrow($qid)){?>
									<tr>
										<td><?=$llan["numero"]?></td>
										<td><?=$llan["disenio"]?></td>
										<td><?=$llan["tipo"]?></td>
										<td><?=$llan["dimension"]?></td>
										<td><?=$llan["vehiculo"]?></td>
										<td><?=$llan["posicion"]?></td>
										<td align="center">
											<a href="<?=$CFG->wwwroot?>/mtto/llantas.php?mode=agregar_movimiento&id_llanta=<?=$llan["id"]?>&facil=true" class="link_verde" title="Ingresar Movimiento"><img alt='Movimientos' src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-add.gif' border='0'></a>&nbsp;
										</td>
									</tr>
									<?}?>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<?
		include($CFG->dirroot."/templates/footer_popup.php");
	}
}


?>
