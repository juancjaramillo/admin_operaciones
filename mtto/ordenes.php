<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

include_once("../application.php");

if(!isset($_SESSION[$CFG->sesion]["user"])){
	$errorMsg="No existe la sesión.";
	error_log($errorMsg);
	die($errorMsg);
}
$user=$_SESSION[$CFG->sesion]["user"];

$mode=nvl($_GET["mode"],nvl($_POST["mode"],""));

$modosConHeader = array("editar","actualizar","agregar","insertar","imprimirFaltantes","agregar_relacionada_correctiva","agregar_facil", "historicoFechasProgramacion");
if(in_array(nvl($mode),$modosConHeader))
	include($CFG->dirroot."/templates/header_popup.php");

switch(nvl($mode)){

	case "eliminar":
		eliminar($_GET);
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

	case "agregar_facil":
		agregar_facil($_GET);
	break;

	case "insertar":
		insertar($_POST);
	break;

	case "insertar_facil":
		insertar_facil($_POST);
	break;

	case "imprimir_unica":
		imprimir_unica($_POST);
	break;

	case "reimprimir":
		reimprimir($_POST);
	break;

	case "imprimir_unica_prueba":
		imprimir_unica_prueba($_GET);
	break;

	case "imprimirDiario":
		imprimirDiario($_GET);
	break;

	case "imprimirFaltantes":
		imprimirFaltantes($_GET);
	break;

	case "agregar_relacionada_correctiva":
		agregar_relacionada_correctiva($_GET);
	break;

	case "insertar_relacionada_correctiva":
		insertar_relacionada_correctiva($_POST);
	break;

	case "historicoFechasProgramacion":
		historicoFechasProgramacion($_GET["id_orden_trabajo"]);
	break;

}


function eliminar($frm)
{
	global $CFG, $db,$ME;
	
	$qid=$db->sql_query("INSERT INTO mtto.ordenes_trabajo_delete
				(SELECT id, id_rutina, id_equipo, id_motivo, fecha_planeada, fecha_ejecucion_inicio, 
		 			id_responsable, id_creador, id_planeador, id_estado_orden_trabajo, 
					tiempo_ejecucion, km, horometro, observaciones, fecha_ejecucion_fin, 
					id_ingreso_ejecutada, id_novedad, herramientas, iev,'".$_SESSION[$CFG->sesion]["user"]["id"]."' as id_borro
					FROM mtto.ordenes_trabajo WHERE id='$frm[id]')");

	$qid=$db->sql_query("DELETE FROM mtto.ordenes_trabajo WHERE id='$frm[id]'");
	echo "<script>\nif(window.opener) window.opener.location.reload();\nif(window.opener.focus) window.opener.focus();\nwindow.close();\n</script>\n";
}

function editar($frm)
{
	global $CFG, $db,$ME;

	$orden = $db->sql_row("SELECT o.*, r.rutina,e.nombre as equipo, e.id_centro
			FROM mtto.ordenes_trabajo o 
			LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
			LEFT JOIN mtto.equipos e ON e.id=o.id_equipo
			WHERE o.id=".$frm["id"]);
	$centro = array();
	$idCen = $db->sql_query("SELECT id_centro FROM mtto.rutinas_centros WHERE id_rutina='".$orden["id_rutina"]."'");
	while($queryCen = $db->sql_fetchrow($idCen))
	{
		$centro[] = $queryCen["id_centro"];
	}

	$db->build_recursive_tree_path("mtto.motivos",$options,$orden["id_motivo"],"id","id_superior","mtto.motivos.nombre");
	$db->crear_select("SELECT id, nombre||' '||apellido as nombre FROM personas WHERE id IN (SELECT id_persona FROM personas_centros WHERE id_centro IN (".implode(",",$centro).") ) AND id IN (SELECT id_persona FROM personas_tareas WHERE id_tarea = 1) AND id_estado=1 ORDER BY nombre,apellido",$responsable,$orden["id_responsable"]);
	$creador=$db->sql_row("SELECT id, nombre||' '||apellido as nombre FROM personas WHERE id=".$orden["id_creador"]);
	$db->crear_select("SELECT id, nombre||' '||apellido as nombre FROM personas WHERE id IN (SELECT id_persona FROM personas_centros WHERE id_centro IN (".implode(",",$centro).")) AND id IN (SELECT id_persona FROM personas_tareas WHERE id_tarea = 2) and id_estado=1 ORDER BY nombre,apellido",$planeador,$orden["id_planeador"]);
	if($orden["id_ingreso_ejecutada"] == "")
		$db->crear_select("SELECT id, nombre||' '||apellido as nombre FROM personas WHERE id=".$_SESSION[$CFG->sesion]["user"]["id"],$ingreso_ejecutada);
	else
		$ingreso_ejecutada = $db->sql_row("SELECT id, nombre||' '||apellido as nombre FROM personas WHERE id_estado=1 and id=".$orden["id_ingreso_ejecutada"]);
	
	$db->crear_select("SELECT id, estado FROM mtto.estados_ordenes_trabajo ORDER BY estado",$estado,$orden["id_estado_orden_trabajo"]);
	$newMode="actualizar";

	$actividades = array();
	$qidAct = $db->sql_query("SELECT *
			FROM mtto.ordenes_trabajo_actividades
			WHERE id_orden_trabajo=".$orden["id"]."
			ORDER BY orden");
	while($ac = $db->sql_fetchrow($qidAct))
	{
		$actividades[$ac["id"]]= array("prin"=>$ac,"car"=>array());
		$qidCargos = $db->sql_query("SELECT c.*, car.nombre as cargo  
				FROM mtto.ordenes_trabajo_actividades_cargos c 
				LEFT JOIN cargos car ON car.id=c.id_cargo
				WHERE c.id_orden_trabajo_actividad=".$ac["id"]);
		while($car = $db->sql_fetchrow($qidCargos))
		{
			$actividades[$ac["id"]]["car"][$car["id"]] = $car;
		}
	}

	$qidEstados = $db->sql_query("SELECT * FROM mtto.estados_ordenes_trabajo WHERE cerrado");
	$qidEstadosAbiertos = $db->sql_query("SELECT * FROM mtto.estados_ordenes_trabajo WHERE not cerrado");

	//elementos
#'<a href=\"javascript:delete_celda(\'existe_'||x.id||'\')\" class=\"link_verde\" title=\"Borrar\">B</a>' as opciones,
	$qidEleExis = $db->sql_query("
			SELECT x.id, e.codigo||' ('||e.elemento||')/'||u.unidad as cod, x.cantidad,
			
			e.id as id_elemento
			FROM mtto.ordenes_trabajo_elementos x
			LEFT JOIN mtto.elementos e ON e.id=x.id_elemento 
			LEFT JOIN mtto.unidades u ON u.id=e.id_unidad 
			WHERE x.id_orden_trabajo='".$orden["id"]."'
			ORDER BY cod");

	$qidOEl = $db->sql_query("SELECT e.codigo||' ('||e.elemento||')/'||u.unidad as cod, e.id
			FROM mtto.elementos e 
			LEFT JOIN mtto.unidades u ON u.id=e.id_unidad 
			WHERE e.id NOT IN (SELECT r2.id_elemento FROM mtto.ordenes_trabajo_elementos r2 WHERE r2.id_orden_trabajo='".$orden["id"]."')
			ORDER BY e.codigo,e.elemento");

	//ordenes relacionadas
	$qidOtR = $db->sql_query("SELECT r.rutina, e.nombre as equipo, o.fecha_planeada as fecha
		FROM mtto.ordenes_trabajo_origen i
		LEFT JOIN mtto.ordenes_trabajo o ON o.id=i.id_orden_trabajo
		LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
		LEFT JOIN mtto.equipos e ON e.id=o.id_equipo
		WHERE i.id_orden_trabajo_origen=".$orden["id"]);

	//mediciones
	$qCampos=$db->sql_query("SELECT * FROM mtto.rutinas_mediciones WHERE id_rutina='".$orden["id_rutina"]."' ORDER BY orden");

	//talleres
	$qidTE = $db->sql_query("SELECT t.*, p.razon
			FROM mtto.ordenes_trabajo_talleres t 
			LEFT JOIN llta.proveedores p ON p.id=t.id_proveedor 
			WHERE t.id_orden_trabajo=".$orden["id"]);
	$qidProveedores = $db->sql_query("SELECT id, razon FROM llta.proveedores WHERE id IN (SELECT id_proveedor FROM llta.proveedores_centros WHERE id_centro IN (SELECT id_centro FROM mtto.equipos WHERE id='".$orden["id_equipo"]."'))");

	include("templates/ordenes_form.php");
}

function actualizar($frm, $hacerAlgoFin=true)
{
	global $CFG, $db,$ME;

	$ordenAnt = $db->sql_row("SELECT to_char(fecha_planeada,'YYYY-MM-DD') as fecha_planeada, to_char(fecha_planeada,' HH24:MI:SS') as hora_planeada, id_novedad 
		FROM mtto.ordenes_trabajo 
		WHERE id=".$frm["id"]);
	$planNew = strftime("%Y-%m-%d",strtotime($frm["fecha_planeada"]));

	//la inserción del id_novedad sólo se hace en la novedad,  nunca más se vuelve a actualizar
	$frm["id_novedad"] = $ordenAnt["id_novedad"];
	
	include($CFG->modulesdir . "/mtto.ordenes_trabajo.php");
	$entidad->loadValues($frm);
	$entidad->set("mode","update");
	$entidad->update();
	if($planNew!=$ordenAnt["fecha_planeada"])
		insertarFechaProgramadaOT($frm["id"],$_SESSION[$CFG->sesion]["user"]["id"],$frm["fecha_planeada"],true);
	
	//se cerró
	$estado= $db->sql_row("SELECT * FROM mtto.estados_ordenes_trabajo WHERE id=".$frm["id_estado_orden_trabajo"]);
	if($estado["cerrado"])
	{
		$db->sql_query("UPDATE mtto.ordenes_trabajo SET id_estado_orden_trabajo=".$frm["id_estado_orden_trabajo"]." WHERE id=".$frm["id"]);
		//lo anterior es por si se reprogramó,  entones quedaría en estado reprogramada, pero necesitamos es cerrar la orden
	}

	$db->sql_query("DELETE FROM mtto.ordenes_trabajo_elementos WHERE id_orden_trabajo=".$frm["id"]);
	$db->sql_query("DELETE FROM mtto.ordenes_trabajo_actividades WHERE id_orden_trabajo=".$frm["id"]);
	$db->sql_query("DELETE FROM mtto.ordenes_trabajo_talleres WHERE id_orden_trabajo=".$frm["id"]);


	$ele=$actividades = $talleres = array();
	foreach($frm as $key => $value)
	{
		if(preg_match("/id_elemento_/",$key,$match))
		{
			$i = str_replace("id_elemento_","",$key);
			$ele[$i]["id_elemento"]=$value;
		}
		if(preg_match("/cantidad_/",$key,$match))
		{
			$i = str_replace("cantidad_","",$key);
			$ele[$i]["cantidad"]=$value;
		}

		//actividades	
		if(preg_match("/^ordencargo_/",$key,$match))
		{
			list($nombre,$idActividad)=split("_",$key);
			$actividades[$idActividad]["prin"]["orden"]=$value;
		}
		if(preg_match("/^descripcioncargo_/",$key,$match))
		{
			list($nombre,$idActividad)=split("_",$key);
			$actividades[$idActividad]["prin"]["descripcion"]=$value;
		}
		if(preg_match("/^tiempocargo_/",$key,$match))
		{
			list($nombre,$idActividad)=split("_",$key);
			$actividades[$idActividad]["prin"]["tiempo"]=$value;
		}
		if(preg_match("/^cargos_/",$key,$match))
		{
			list($bas,$idActividad,$clave,$idPos)=split("_",$key);
			if($clave=="idcargo") $clave="id_cargo";
			if($clave=="idpersona") $clave="id_persona";
			$actividades[$idActividad]["cargos"][$idPos][$clave]=$value;
		}

		//talleres
		if(preg_match("/^talleridproveedor_/",$key,$match))
		{
			$i = str_replace("talleridproveedor_","",$key);
			$talleres[$i]["id_proveedor"]=$value;
		}
		if(preg_match("/^tallercosto_/",$key,$match))
		{
			$i = str_replace("tallercosto_","",$key);
			$talleres[$i]["costo"]=$value;
		}
		if(preg_match("/^tallertiempo_/",$key,$match))
		{
			$i = str_replace("tallertiempo_","",$key);
			$talleres[$i]["tiempo"]=$value;
		}
	}


	if(count($ele)>0)
	{
		include_once($CFG->modulesdir . "/mtto.ordenes_trabajo_elementos.php");
		foreach($ele as $otp)
		{
			if($otp["id_elemento"] != "" && $otp["id_elemento"] !="NULL")
			{
				$otp["id_orden_trabajo"]=$frm["id"];
				$entidad->loadValues($otp);
				$entidad->insert();
			}
		}
	}

	if(count($talleres)>0)
	{
		include_once($CFG->modulesdir . "/mtto.ordenes_trabajo_talleres.php");
		foreach($talleres as $vt)
		{
			if($vt["id_proveedor"] != "%")
			{
				$vt["id_orden_trabajo"]=$frm["id"];
				$entidad->loadValues($vt);
				$entidad->insert();
			}
		}
	}

	if(count($actividades) > 0)
	{
		foreach($actividades as $dx)
		{
			if($dx["prin"]["descripcion"] != "")
			{
				$orden = $dx["prin"]["orden"];
				if($orden == "") $orden = 0;
				$tiempo = $dx["prin"]["tiempo"];
				if($tiempo == "") $tiempo  = 0;
				$db->sql_query("INSERT INTO mtto.ordenes_trabajo_actividades (id_orden_trabajo,orden, descripcion, tiempo) VALUES ('".$frm["id"]."', '".$orden."', '".$dx["prin"]["descripcion"]."', '".$tiempo."')");
				$idOTA = $db->sql_nextid();

				if(isset($dx["cargos"]))
				{
					foreach($dx["cargos"] as $dxCar)
					{
						if($dxCar["id_cargo"] != "%")
						{
							$id_persona="null";
							if($dxCar["id_persona"] != '%') $id_persona = "'".$dxCar["id_persona"]."'";
							$tiempoCargo = 0;
							if($dxCar["tiempo"] != "") $tiempoCargo = $dxCar["tiempo"];

							$db->sql_query("INSERT INTO mtto.ordenes_trabajo_actividades_cargos (id_orden_trabajo_actividad, id_cargo, tiempo, id_persona) VALUES ('".$idOTA."', '".$dxCar["id_cargo"]."', '".$tiempoCargo."',".$id_persona.")");
						}
					}
				}
			}
		}	
	}
	
	//mediciones
	$qCampos=$db->sql_query("SELECT * FROM mtto.rutinas_mediciones WHERE id_rutina='".$frm["id_rutina"]."' ORDER BY orden");
	while($campo=$db->sql_fetchrow($qCampos)){
		if(isset($frm["CA_" . $campo["id"]])){
			$resultado=$frm["CA_" . $campo["id"]];
			$qUpdate=$db->sql_query("UPDATE mtto.ordenes_rutinas_mediciones SET resultado='$resultado' WHERE id_orden_trabajo='".$frm["id"]."' AND id_medicion='".$campo["id"]."'");
		}
	}

	if($hacerAlgoFin)
	{
		//el hacerAlgoFin viene de imprimir,   la OT se actualiza pero no se hace un reload, ni se va a editar.

		if($frm["accion"]=="cerrar")
			echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
		else
			echo "<script>window.location.href='".$CFG->wwwroot."/mtto/ordenes.php?mode=editar&id=".$frm["id"]."';</script>";
	}
}

function agregar($frm=array())
{
	global $CFG, $db,$ME;

	$user=$_SESSION[$CFG->sesion]["user"];

	if(isset($frm["mode"]) && $frm["mode"]=="insertar_relacionada_correctiva")
	{
		$grupoEqui = $db->sql_row("SELECT id_grupo FROM mtto.equipos WHERE id=".$frm["id_equipo"]);
		$datos=array(0);
		obtenerIdsGrupos($grupoEqui["id_grupo"],$datos);

		$db->crear_select("SELECT r.id, case when g.nombre != '' then r.rutina ||' ('||getPath(g.id,'mtto.grupos')||')' when e.nombre != '' then r.rutina ||' ('||e.nombre||')' else r.rutina end as nrut
				FROM mtto.rutinas r 
				LEFT JOIN mtto.grupos g ON g.id=r.id_grupo
				LEFT JOIN mtto.equipos e ON e.id=r.id_equipo
				WHERE r.id_tipo_mantenimiento=2 AND r.id_grupo IN (".implode(",",$datos).")
				ORDER BY r.rutina",$rutinas);
		$db->crear_select("SELECT e.id, e.nombre||' ('||getPath(g.id,'mtto.grupos')||')' as nom_equ
				FROM mtto.equipos e
				LEFT JOIN mtto.grupos g ON g.id=e.id_grupo
				ORDER BY e.nombre",$equipos,$frm["id_equipo"]);
		$orden["fecha_planeada"] = date("Y-m-d H:i:s");
	}
	else
	{
		$db->crear_select("SELECT r.id, case when g.nombre != '' then r.rutina ||' ('||getPath(g.id,'mtto.grupos')||')' when e.nombre != '' then r.rutina ||' ('||e.nombre||')' else r.rutina end as nrut
				FROM mtto.rutinas r 
				LEFT JOIN mtto.grupos g ON g.id=r.id_grupo
				LEFT JOIN mtto.equipos e ON e.id=r.id_equipo
				ORDER BY r.rutina",$rutinas);
	if($user["nivel_acceso"]!=1)
		$db->crear_select("SELECT r.id, case when g.nombre != '' then r.rutina ||' ('||getPath(g.id,'mtto.grupos')||')' when e.nombre != '' then r.rutina ||' ('||e.nombre||')' else r.rutina end as nrut
				FROM mtto.rutinas r 
				LEFT JOIN mtto.grupos g ON g.id=r.id_grupo
				LEFT JOIN mtto.equipos e ON e.id=r.id_equipo
				WHERE r.id IN (SELECT id_rutina FROM mtto.rutinas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."'))
				ORDER BY r.rutina",$rutinas);
	}
	$db->build_recursive_tree_path("mtto.motivos",$options,"","id","id_superior","mtto.motivos.nombre");
	$creador=$db->sql_row("SELECT id, nombre||' '||apellido as nombre FROM personas WHERE id=".$_SESSION[$CFG->sesion]["user"]["id"]);
	$db->crear_select("SELECT id, estado FROM mtto.estados_ordenes_trabajo ORDER BY estado",$estado);
	$db->crear_select("SELECT id, sistema FROM mtto.sistemas ORDER BY sistema",$sistemas,"","");
	$newMode="insertar";
	if(isset($frm["mode"]))
		$newMode=$frm["mode"];

	$qidEstados = $db->sql_query("SELECT * FROM mtto.estados_ordenes_trabajo WHERE cerrado");
	$qidEstadosAbiertos = $db->sql_query("SELECT * FROM mtto.estados_ordenes_trabajo WHERE not cerrado");

	include("templates/ordenes_form.php");
}


function agregar_facil($orden)
{
	global $CFG, $db,$ME;

	$user=$_SESSION[$CFG->sesion]["user"];

	$db->crear_select("SELECT id, nombre FROM mtto.equipos WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') ORDER BY nombre",$equipos,nvl($orden["id_equipo"]));
	$db->crear_select("SELECT id, sistema FROM mtto.sistemas ORDER BY sistema",$sistemas);
	$db->crear_select("SELECT id, tipo FROM mtto.tipos WHERE id != 1 ORDER BY tipo",$tipos);
	if(isset($orden["id_equipo"]))
		$db->crear_select("SELECT id, rutina FROM mtto.rutinas WHERE id_grupo IN (SELECT getParents((SELECT id_grupo FROM mtto.equipos WHERE id='$orden[id_equipo]'),'mtto.grupos')) ORDER BY rutina",$rutinas);
	else $rutinas="<option value=\"%\">Seleccione...</option>";
	$newMode="insertar_facil";

	include("templates/ordenes_facil_form.php");
}


function agregar_relacionada_correctiva($frm)
{
	$frm["mode"]="insertar_relacionada_correctiva";
	agregar($frm);
}


function insertar($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir . "/mtto.ordenes_trabajo.php");
	$entidad->loadValues($frm);
	$id=$entidad->insert();
	insertarFechaProgramadaOT($id,$_SESSION[$CFG->sesion]["user"]["id"],$frm["fecha_planeada"]);

	$goto = $CFG->wwwroot."/mtto/ordenes.php?mode=editar&id=".$id;
	echo "<script>window.location.href='".$goto."';</script>";
}

function insertar_facil($frm)
{
	global $CFG, $db,$ME;

	$user=$_SESSION[$CFG->sesion]["user"];
	$frm["id_creador"]=$user["id"];
	$frm["id_estado_orden_trabajo"]=7;

	include($CFG->modulesdir . "/mtto.ordenes_trabajo.php");
	$entidad->loadValues($frm);
	$id=$entidad->insert();
	insertarFechaProgramadaOT($id,$user["id"],$frm["fecha_planeada"]);

	$goto = $CFG->wwwroot."/mtto/ordenes.php?mode=editar&id=".$id;
	echo "<script>window.location.href='".$goto."';</script>";
}

function insertar_relacionada_correctiva($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir . "/mtto.ordenes_trabajo.php");
	$entidad->loadValues($frm);
	$id=$entidad->insert();
	insertarFechaProgramadaOT($id,$_SESSION[$CFG->sesion]["user"]["id"],$frm["fecha_planeada"]);

	$oot = array("id_orden_trabajo"=>$id, "id_orden_trabajo_origen"=>$frm["id_orden_trabajo_origen"], "id_persona_reporto"=>$_SESSION[$CFG->sesion]["user"]["id"], "id_motivo"=>$frm["id_motivo"]);

	include($CFG->modulesdir . "/mtto.ordenes_trabajo_origen.php");
	$entidad->loadValues($oot);
	$entidad->insert();

	$goto = $CFG->wwwroot."/mtto/ordenes.php?mode=editar&id=".$id;
	echo "<script>window.location.href='".$goto."';</script>";
}


function imprimirFaltantes($frm)
{
	global $db,$CFG,$ME;

	$condicion = "true";
	$user=$_SESSION[$CFG->sesion]["user"];
	if($user["nivel_acceso"]!=1)
		$condicion=" r.id IN (SELECT id_rutina FROM mtto.rutinas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."'))";

	$cons = "SELECT o.id, e.nombre as equipo, r.rutina, o.id_responsable, o.id_planeador, r.id as id_rutina
		FROM mtto.ordenes_trabajo o
		LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
		LEFT JOIN mtto.equipos e ON e.id=o.id_equipo
		LEFT JOIN mtto.estados_ordenes_trabajo est ON est.id=o.id_estado_orden_trabajo
		WHERE ".$condicion." AND o.fecha_planeada::date = '".$frm["fecha"]."' AND NOT est.cerrado";
	$fallas = array();
	$qid = $db->sql_query($cons);
	while($query = $db->sql_fetchrow($qid))
	{
		if($query["id_responsable"] == "")
			$fallas[$query["equipo"]][$query["rutina"]][] = " &middot; No tiene Responsable";
		if($query["id_planeador"] == "")
			$fallas[$query["equipo"]][$query["rutina"]][] = " &middot; No tiene Planeador";
	
		$qidAct = $db->sql_query("SELECT id,orden||'. '||descripcion as actividad
				FROM mtto.ordenes_trabajo_actividades
				WHERE mtto.ordenes_trabajo_actividades.id_orden_trabajo='".$query["id"]."'
				ORDER BY orden");
		while($act = $db->sql_fetchrow($qidAct))
		{
			$qcar = $db->sql_query("SELECT *
					FROM mtto.ordenes_trabajo_actividades_cargos
					WHERE id_orden_trabajo_actividad= '".$act["id"]."' AND (id_cargo is null or id_persona is null)");
			while($car = $db->sql_fetchrow($qcar))
			{
				if($car["id_cargo"] == "")
					$fallas[$query["equipo"]][$query["rutina"]][$act["id"]] = " &middot; Falta el cargo de la actividad  : '".$act["actividad"]."'";
				if($car["id_persona"] == "")
					$fallas[$query["equipo"]][$query["rutina"]][$act["id"]] = " &middot; Falta asignar la persona de la actividad  : '".$act["actividad"]."'";
			}
		}
	}

	$texto = "
	<table width=\"100%\">
		<tr><td align=\"center\" height=\"40\" class=\"azul_12\">Para imprimir las órdenes del día,  se debe primero resolver:</td></tr>
	</table>
	<table width=\"100%\" class=\"tabla_sencilla\">
		<tr><td class=\"tabla_sencilla_td\"><span class=\"azul_12\">EQUIPO</span></td><td class=\"tabla_sencilla_td\"><span class=\"azul_12\">FALTANTES</span></td></tr>";
	foreach($fallas as $key => $rutina)
	{
		$texto.="<tr><td  class=\"tabla_sencilla_td\">".$key."</td><td class=\"tabla_sencilla_td\">";
		foreach($rutina as $nm => $dx)
		{
			$texto.="RUTINA : ".$nm.":<br />";
			$texto.=implode("<br />",$dx);
		}
		$texto.="</td></tr>";	
	}
	$texto.="</table>
		<table width=\"100%\">
			<tr><td align=\"center\" height=\"40\" valign=\"bottom\"><input type=\"button\" value=\"Cerrar\" class=\"boton_verde\" onclick=\"window.close()\"/></td></tr>
		</table>";
	echo $texto;
}

function imprimir_unica($frm)
{
	global $db,$CFG,$ME;
	
	$frm["id_estado_orden_trabajo"]=5;
	actualizar($frm, false);
	imprimir(array($frm["id"]));
}

function reimprimir($frm)
{
	global $db,$CFG,$ME;
		
	imprimir(array($frm["id"]));
}


function imprimir_unica_prueba($frm)
{
	global $db,$CFG,$ME;
	
	$frm["id_estado_orden_trabajo"]=5;
	imprimir(array($frm["id"]));
}

function imprimirDiario($frm)
{
	global $db,$CFG,$ME;

	$condicion = "true";
	$user=$_SESSION[$CFG->sesion]["user"];
	if($user["nivel_acceso"]!=1)
		$condicion=" r.id IN (SELECT id_rutina FROM mtto.rutinas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."'))";

	$varios = array();
	$cons = "SELECT o.id
		FROM mtto.ordenes_trabajo o
		LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
		LEFT JOIN mtto.equipos e ON e.id=o.id_equipo
		WHERE ".$condicion." AND o.fecha_planeada::date = '".$frm["fecha"]."'";
	$qid = $db->sql_query($cons);
	while($query = $db->sql_fetchrow($qid))
	{
		$varios[] = $query["id"];
	}
	imprimir($varios);
}


function imprimir($ids)
{
	global $db,$CFG,$ME;

	if(!defined("FPDF_FONTPATH")) define("FPDF_FONTPATH",$CFG->common_libdir . "/fpdf/font/");
	include_once($CFG->common_libdir . "/fpdf/fpdf.php");
	include_once($CFG->libdir . "/funciones_pdf.php");

	$pdf=new PDF('P','mm','Letter');
	$pdf->SetMargins(5, 5, 5);
	$pdf->SetAutoPageBreak(1,5);
	$pdf->SetDisplayMode('fullpage','single');
	$pdf->Open();
	$pdf->AliasNbPages();
	

	$orden = $estadosEjecutados = array();
	$qidEs = $db->sql_query("SELECT id FROM mtto.estados_ordenes_trabajo WHERE cerrado");
	while($queryEst = $db->sql_fetchrow($qidEs))
	{
		$estadosEjecutados[] = $queryEst["id"];
	}

	$centro = "";
	$formatoAparte = false;
	$cons = "SELECT o.*, r.rutina,r.id as id_rutina, e.nombre as equipo, m.nombre as motivo, pr.nombre||' '||pr.apellido as responsable, pc.nombre||' '||pc.apellido as creador, pp.nombre||' '||pp.apellido as planeador, emp.empresa,tp.tipo as tipo_vehiculo, mv.marca||'/'||ref.referencia as marca, v.modelo, e.id_centro, cen.centro as zona, tm.tipo as tipo_mtto, n.observaciones as ob_novedad, cen.mmdd_logo_filename as logo, v.placa, v.codigo as codigo_vehiculo
			FROM mtto.ordenes_trabajo o 
			LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
			LEFT JOIN mtto.equipos e ON e.id=o.id_equipo
			LEFT JOIN mtto.motivos m ON m.id=o.id_motivo
			LEFT JOIN personas pr ON pr.id=o.id_responsable
			LEFT JOIN personas pc ON pc.id=o.id_creador
			LEFT JOIN personas pp ON pp.id=o.id_planeador
			LEFT JOIN centros cen ON cen.id=e.id_centro
			LEFT JOIN empresas emp ON emp.id=cen.id_empresa
			LEFT JOIN vehiculos v ON e.id_vehiculo=v.id
			LEFT JOIN tipos_vehiculos tp ON tp.id=v.id_tipo_vehiculo
			LEFT JOIN referencias ref ON ref.id=v.id_referencia 
			LEFT JOIN marcas_vehiculos mv ON mv.id=ref.id_marca_vehiculo
			LEFT JOIN mtto.tipos tm ON tm.id=r.id_tipo_mantenimiento
			LEFT JOIN novedades n ON n.id = o.id_novedad
			WHERE o.id IN (".implode(",",$ids).")
			ORDER BY o.fecha_planeada";
	$qidO = $db->sql_query($cons);
	while($queryOrd = $db->sql_fetchrow($qidO))
	{
		$centro = $queryOrd["id_centro"];

		if(!in_array($queryOrd["id_estado_orden_trabajo"],$estadosEjecutados))
			$db->sql_query("UPDATE mtto.ordenes_trabajo SET id_estado_orden_trabajo=5 WHERE id=".$queryOrd["id"]);

		$orden[$queryOrd["id"]]["gen"]=array(
				"centro"=>$queryOrd["empresa"],
				"zona"=>$queryOrd["zona"],
				"id_orden"=>$queryOrd["id"],
				"equipo"=>$queryOrd["equipo"],
				"tipo_vehiculo"=>$queryOrd["tipo_vehiculo"],
				"marca"=>$queryOrd["marca"],
				"modelo"=>$queryOrd["modelo"],
				"observaciones"=>$queryOrd["observaciones"],
				"rutina"=>$queryOrd["rutina"],
				"fecha_planeada"=>$queryOrd["fecha_planeada"],
				"herramientas"=>$queryOrd["herramientas"],
				"tipo_mtto"=>$queryOrd["tipo_mtto"],
				"ob_novedad"=>$queryOrd["ob_novedad"],
				"logo"=>$queryOrd["logo"], 
				"id_centro"=>$queryOrd["id_centro"],
				"placa"=>$queryOrd["placa"],
				"codigo_vehiculo"=>$queryOrd["codigo_vehiculo"],
				"logo"=>$queryOrd["logo"]
				);

		//mano de obra
		$qidPer=$db->sql_query("SELECT p.nombre||' '||p.apellido as persona, oc.tiempo, c.nombre as cargo, c.valor, a.orden||'. '||a.descripcion as actividad
				FROM mtto.ordenes_trabajo_actividades a
				LEFT JOIN mtto.ordenes_trabajo_actividades_cargos oc  ON a.id=oc.id_orden_trabajo_actividad
				LEFT JOIN personas p ON p.id=oc.id_persona
				LEFT JOIN cargos c ON c.id=oc.id_cargo
				WHERE a.id_orden_trabajo='".$queryOrd["id"]."'
				ORDER BY a.orden");
		while($quePer = $db->sql_fetchrow($qidPer))
		{
			$dato = array("act"=>$quePer["actividad"],"carp"=>$quePer["cargo"]." / ".$quePer["persona"], "cargo"=>$quePer["cargo"], "persona" =>$quePer["persona"],  "valor"=>$quePer["valor"],"tiempo"=>$quePer["tiempo"]);
			$orden[$queryOrd["id"]]["ma"][]= $dato;
		}
	
		//elementos
		$cons = "SELECT x.id, e.codigo||' ('||e.elemento||')/'||u.unidad as cod, x.cantidad, e.tipoe, e.id as id_elemento
				FROM mtto.ordenes_trabajo_elementos x
				LEFT JOIN mtto.elementos e ON e.id=x.id_elemento 
				LEFT JOIN mtto.unidades u ON u.id=e.id_unidad 
				WHERE x.id_orden_trabajo='".$queryOrd["id"]."'
				ORDER BY cod";
		$qidEleExis = $db->sql_query($cons);
		while($ele =  $db->sql_fetchrow($qidEleExis))	
		{
			$exist = $db->sql_row("SELECT e.existencia, e.precio 
					FROM mtto.elementos_existencias e 
					LEFT JOIN mtto.bodegas b ON b.id=e.id_bodega 
					WHERE e.id_elemento = '".$ele["id_elemento"]."' AND b.id_centro=".$queryOrd["id_centro"]);
			$existencia = nvl($exist["existencia"],0);
			$orden[$queryOrd["id"]]["elementos"][] = array("cant"=>$ele["cantidad"],"exis"=>$existencia,"cod"=>$ele["cod"],"precio"=>nvl($exist["precio"],0));
		}
	
		//talleres
		$cons = "SELECT p.razon, t.costo, t.tiempo , p.contacto, p.direccion, p.telefono, p.celular
			FROM mtto.ordenes_trabajo_talleres t
			LEFT JOIN llta.proveedores p ON p.id=t.id_proveedor
			WHERE t.id_orden_trabajo=".$queryOrd["id"];
		$qidOEx = $db->sql_query($cons);
		while($taller = $db->sql_fetchrow($qidOEx))
		{
			$orden[$queryOrd["id"]]["taller"][] = array("proveedor"=>$taller["razon"], "costo"=>$taller["costo"], "tiempo"=>$taller["tiempo"], "contacto"=>$taller["contacto"], "direccion"=>$taller["direccion"],  "telefono" => $taller["telefono"], "celular"=>$taller["telefono"]);
			$formatoAparte = true;
		}
	}

	$pdf->SetFillColor(220,218,193);

	if($centro == 1 || $centro == 2 || $centro == 6)
	{
		foreach($orden as $od)
		{
			$pdf->AddPage();
			$pdf->SetFont('Arial','B',12);
			if($od["gen"]["logo"] != "")
			{
				$archivo = $CFG->dirroot."/tmp/".$od["gen"]["logo"];
				if(file_exists($archivo)) unlink ($archivo);
				copy($CFG->dirroot."/files/centros/logo/".$centro, $archivo);
				$arrayInfo=getimagesize($archivo);
				$ancho=$arrayInfo[0]/$pdf->k;
				$alto=$arrayInfo[1]/$pdf->k;
				if($alto > 30)
				{
					$ancho = resizeToHeight(30, $ancho, $alto);
					$alto = 30;
				}
				$x = ($pdf->lMargin+20) -($ancho/2);
				$pdf->Image($archivo, $x, $pdf->getY()+3, $alto);
			}
			$pdf->cell(40,25,"",1,0);
			$pdf->SetFont('Arial','B',16);
			$pdf->MultiCell($pdf->areaUtil-40-65,25,"ORDEN DE TRABAJO",1,"C");
			$codigos = array(
				array("Fecha Emisión: 2012-05-12"),
				array("Fecha Actualización:"),
				array("Versión: 1"),
				array("Código: MT-F-12"),
				array("Página: 1 de 1"));
			$pdf->SetFont('Arial','',10);
			$pdf->rowHeight=5;
			$pdf->setWidths(array(65));
			$yFin = 5;
			foreach($codigos as $line)
			{
				$pdf->setXY(145.9,$yFin);
				$pdf->row($line);
				$yFin = $pdf->getY();
			}

			$pdf->cell(120,14,"",1,1);
			$pdf->setXY($pdf->lMargin,$yFin);
			$pdf->MultiCell(125,4,"\nDEPARTAMENTO DE MANTENIMIENTO\n(TALLER INTERNO Y/O EXTERNO)",0,"C");
			$pdf->setXY(125,$yFin);
			$pdf->SetFont('Arial','BI',11);
			$pdf->cell($pdf->areaUtil-125+$pdf->lMargin,7,"ORDEN DE TRABAJO No. ".$od["gen"]["id_orden"],1,1,"C");
			$pdf->SetFont('Arial','B',11);
			
			$pdf->setXY(125,$pdf->getY());
			$valMitad = ($pdf->areaUtil-125+$pdf->lMargin)/2;
			$pdf->cell($valMitad,7,"MÓVIL",1,0,"C",true);
			$pdf->cell($valMitad,7,$od["gen"]["equipo"],1,1,"C",true);
			$yIni=$pdf->getY();
			$pdf->SetFont('Arial','',10);
			$pdf->MultiCell(40,5,"ZONA: ".$od["gen"]["zona"],1,"L");
			$pdf->setXY(40+$pdf->lMargin,$yIni);
			$pdf->MultiCell(80,5,"TIPO DE MANTENIMIENTO: ".$od["gen"]["tipo_mtto"],1,"L");
			$pdf->setXY(40+80+$pdf->lMargin,$yIni);
			$pdf->MultiCell($valMitad*2,5,"INVENTARIO EQUIPO",1,"C");
			$yFin = $pdf->getY();
			$pdf->MultiCell(120,5,$od["gen"]["rutina"],1,"C",true);
			$pdf->MultiCell(120,5,"FECHA Y HORA DE IMPRESIÓN: ".ucfirst(strftime("%B %d de %Y %H:%M",strtotime(date("Y-m-d H:i:s")))),1,"L");
			$pdf->MultiCell(120,5,"FECHA Y HORA PLANEADA: ".ucfirst(strftime("%B %d de %Y %H:%M",strtotime($od["gen"]["fecha_planeada"]))),1,"L");
			$yPS = $pdf->getY();
			$pdf->SetFont('Arial','',9);
			$pdf->MultiCell($pdf->areaUtil, 5,"PROBLEMAS/SÍNTOMAS REPORTADOS POR EL CONDUCTOR:\n".$od["gen"]["ob_novedad"],0,"L");
			$pdf->setWidths(array($valMitad,$valMitad));
			$pdf->setAligns(array('L','L'));
			$pdf->setBorders(array(1,1));
			$invEq = array(
					array("Marca",$od["gen"]["marca"]),
					array("Modelo",$od["gen"]["modelo"]),
					array("Tipo",$od["gen"]["tipo_vehiculo"]),
					array("Horómetro",""),
					array("Kilometraje",""));
			$pdf->rowHeight=3.5;
			foreach($invEq as $dx)
			{
				$pdf->setXY(125,$yFin);
				$pdf->row($dx);
				$yFin = $pdf->getY();
			}
			$pdf->setXY($pdf->lMargin,$yPS);
			$pdf->cell($pdf->areaUtil,20,"","LRB",1);

			//ACTIVIDADES
			$pdf->setXY($pdf->lMargin,$pdf->getY()+2);
			$pdf->setWidths(array(65,30,25,86));
			$pdf->SetFont('Arial','',10);
			$pdf->setAligns(array('C','C','C','C'));
			$pdf->setFills(array(1,1,1,1));
			$pdf->rowHeight=4;
			$pdf->row(array("ACTIVIDAD", "NOMBRE", "CARGO","OBSERVACIONES"));
			$pdf->setFills(array(0,0,0,0));
			$pdf->setAligns(array('L','L','L','L'));
			$pdf->SetFont('Arial','',9);
			$pdf->rowHeight=3.5;
			if(isset($od["ma"])){
				foreach($od["ma"] as $dx)
				{
					$pdf->row(array($dx["act"],$dx["persona"], $dx["cargo"], ""));
				}
			}else
			{
				for($i=0;$i<=4;$i++)
				{
					$pdf->row(array("","","",""));
				}
			}

			//ELEMENTOS
			$pdf->setXY($pdf->lMargin,$pdf->getY()+2);
			$pdf->setWidths(array(10, $pdf->areaUtil-10));
			$pdf->SetFont('Arial','',10);
			$pdf->setAligns(array('C','C'));
			$pdf->setFills(array(1,1));
			$pdf->rowHeight=4;
			$pdf->row(array("#", "REPUESTOS / INSUMOS"));
			$pdf->setFills(array(0,0));
			$pdf->setAligns(array('C','L'));
			$pdf->SetFont('Arial','',9);
			$pdf->rowHeight=3.5;
			if(isset($od["elementos"])){
				foreach($od["elementos"] as $dx)
				{
					$pdf->row(array($dx["cant"],$dx["cod"]));
				}
			}else
			{
				for($i=0;$i<=4;$i++)
				{
					$pdf->row(array("",""));
				}
			}
			
			//TRABAJOS A REALIZAR
			$pdf->SetFont('Arial','',10);
			$pdf->setXY($pdf->lMargin,$pdf->getY()+2);
			$pdf->cell($pdf->areaUtil,5,"TRABAJOS A REALIZAR",1,1,"C",true);
			$pdf->SetFont('Arial','',8);
			if($od["gen"]["observaciones"] != "")
				$pdf->MultiCell($pdf->areaUtil, 3.5, $od["gen"]["observaciones"], 1, "L");
			else
				$pdf->cell($pdf->areaUtil,20,"",1,1);
			$pdf->SetFont('Arial','',10);

			//OTROS TALLERES
			if(isset($od["taller"]))	
			{
				foreach($od["taller"] as $dx)
				{
					$pdf->setXY($pdf->lMargin,$pdf->getY()+2);
					$pdf->setWidths(array(40, 166));
					$pdf->SetFont('Arial','',10);
					$pdf->setAligns(array('L','L'));
					$pdf->rowHeight=4;
					$pdf->cell($pdf->areaUtil,5,"TRABAJOS DE OTROS TALLERES",1,1,"C",true);
					$pdf->SetFont('Arial','',9);
					$pdf->row(array("NOMBRE TALLER", $dx["proveedor"]));
					$pdf->row(array("DIRECCIÓN TALLER", $dx["direccion"]));
					$pdf->row(array("NOMBRE CONTACTO", $dx["contacto"]));
					$pdf->row(array("TELEFONO CONTACTO", $dx["telefono"]));
					$pdf->row(array("CELULAR CONTACTO", $dx["celular"]));
				}
			}

			//OBSERVACIONES
			$pdf->setXY($pdf->lMargin,$pdf->getY()+2);
			$pdf->setWidths(array(20, $pdf->areaUtil-20));
			$pdf->SetFont('Arial','',10);
			$pdf->setAligns(array('C','C'));
			$pdf->setFills(array(1,1));
			$pdf->rowHeight=4;
			$pdf->row(array("FECHA", "OBSERVACIONES"));
			$pdf->setFills(array(0,0));
			$pdf->setAligns(array('C','L'));
			$pdf->SetFont('Arial','',9);
			$pdf->rowHeight=3.5;
			for($i=0;$i<=8;$i++)
			{
				$pdf->row(array("",""));
			}

			//FINAL
			$pdf->SetFont('Arial','',9);
			$pdf->setWidths(array($pdf->areaUtil/2, $pdf->areaUtil/2));
			$pdf->setAligns(array('L', 'L'));
			$pdf->row(array("HORA INICIO:", "HORA FINAL:"));			
			if($od["gen"]["herramientas"] != "")
			{
				$pdf->setY($pdf->getY()+1);
				$pdf->MultiCell($pdf->areaUtil,3.5,"HERRAMIENTAS : ".$od["gen"]["herramientas"]);
			}

			$pdf->setY($pdf->getY()+5);
			$y = $pdf->getY();
			$pdf->MultiCell($pdf->areaUtil,3.5,"FIRMA MANTENIMIENTO ______________________________________________________");
			$pdf->setXY($pdf->rMargin-60,$y);
			$pdf->MultiCell($pdf->areaUtil,3.5,"ORDEN CERRADA SI  ____");
					
			if($formatoAparte)
			{
				foreach($od["taller"] as $dx)
				{
				  for($i=1; $i<=2; $i++)
				  {
					 //preguntar($dx);
					 $pdf->AddPage("L");
					if($od["gen"]["logo"] != "")
					{
						$archivo = $CFG->dirroot."/tmp/".$od["gen"]["logo"];
						if(file_exists($archivo)) unlink ($archivo);
						copy($CFG->dirroot."/files/centros/logo/".$centro, $archivo);
						$arrayInfo=getimagesize($archivo);
						$ancho=$arrayInfo[0]/$pdf->k;
						$alto=$arrayInfo[1]/$pdf->k;
						if($alto > 30)
						{
							$ancho = resizeToHeight(30, $ancho, $alto);
							$alto = 30;
						}
						$x = ($pdf->lMargin+20) -($ancho/2);
						$pdf->Image($archivo, $x, $pdf->getY()+3, $alto);
					}

					 $pdf->cell(40,25,"",1,0);
					 $pdf->SetFont('Arial','B',16);
					 $pdf->MultiCell(165,25,"REMISIÓN A TALLER EXTERNO",1,"C");
					 $codigos = array(
					 array("Fecha Emisión: ".date("Y-m-d")),
					 array("Fecha Actualización:"),
					 array("Versión: 1"),
					 array("Código: MT-F-10"),
					 array("Página: 1 de 1"));
					 $pdf->SetFont('Arial','',10);
					 $pdf->rowHeight=5;
					 $pdf->setWidths(array(65));
					 $yFin = 5;
					 foreach($codigos as $line)
					 {
						$pdf->setXY(210,$yFin);
						$pdf->row($line);
						$yFin = $pdf->getY();
					 }
					 $pdf->setXY($pdf->lMargin,$pdf->getY()+2);
					 $pdf->SetFont('Arial','',12);
					 $pdf->cell(48,6,"FECHA:",1,0,"L", true);
					 $pdf->cell(47,6, $od["gen"]["fecha_planeada"],1,0,"C");
					 $pdf->cell(38,6,"PLACA:",1,0,"L", true);
					 $pdf->cell(47,6, $od["gen"]["placa"],1,0,"C");
					 $pdf->cell(43,6,"No. INTERNO:",1,0,"L", true);
					 $pdf->cell(47,6, $od["gen"]["codigo_vehiculo"],1,1,"C");
					 $pdf->cell(48,6,"NOMBRE DEL TALLER:",1,0,"L", true);
					 $pdf->cell(222,6,$dx["proveedor"],1,1,"L");
					 $pdf->cell(48,6,"DIRECCIÓN:",1,0,"L", true);
					 $pdf->cell(222,6,$dx["direccion"],1,1,"L");
					 $pdf->cell(48,6,"TELÉFONO:",1,0,"L", true);
					 $pdf->cell(90,6,$dx["telefono"],1,0,"L");
					 $pdf->cell(48,6,"CELULAR:",1,0,"L", true);
					 $pdf->cell(84,6,$dx["celular"],1,1,"L");
					 $pdf->cell(48,6,"CONTACTO:",1,0,"L", true);
					 $pdf->cell(222,6,$dx["contacto"],1,1,"L");
		
					 $pdf->setXY($pdf->lMargin,$pdf->getY()+2);
					 $pdf->cell(270,6,"DETALLE DE LA REPARACIÓN:",1,1,"C", true);
					 $pdf->cell(270,37,$od["gen"]["ob_novedad"],1,1);
					 $pdf->cell(270,6,"REPUESTOS UTILIZADOS:",1,1,"C", true);
					 $pdf->cell(270,37,"",1,1);
					 $pdf->cell(270,6,"OBSERVACIONES:",1,1,"L", true);
					 $pdf->cell(270,25,"",1,1);
					 $pdf->SetFont('Arial','',10);
					 $pdf->cell(270,6,"NOTA: FAVOR ANEXAR ÉSTE FORMATO, DEBIDAMENTE DILIGENCIADO, A LA FACTURA O CUENTA DE COBRO",0,1,"L");

					 $pdf->setXY($pdf->lMargin,$pdf->getY()+5);
					 $pdf->SetFont('Arial','',12);
					 $pdf->cell(35,6,"RECIBIDO POR:",1,0,"L", true);
					 $pdf->cell(235,6, "",1,1);
					 $pdf->cell(35,6,"REMITENTE:",1,0,"L", true);
					 $pdf->cell(80,6, "",1,0);
					 $pdf->cell(55,6,"FIRMA DEL REMITENTE:",1,0,"L", true);
					 $pdf->cell(100,6, "",1,0);
				  }
				}
			}
			
			
		}
	}
	elseif($centro == 3 || $centro == 15)
	{
		foreach($orden as $od)
		{
			
			$pdf->AddPage();
			
			$pdf->SetFont('Arial','B',9);
			if ($centro == 3)$imagen = $CFG->dirroot."/images/logos/ser.jpg";
			if ($centro == 15)$imagen = $CFG->dirroot."/images/logos/distrito.jpg";
			$pdf->Image($imagen, 80, 5, 70);
			$pdf->setXY(90,8);
			$pdf->Cell(200,45,'ORDEN DE TRABAJO');
			
			$yFin = 35;
			$pdf->cell(120,12,"",1,1);
			$pdf->setXY($pdf->lMargin,$yFin);
			$pdf->MultiCell(120,5,"GESTION DE MANTENIMIENTO\n(TALLER INTERNO Y/O EXTERNO)",1,"C");
			$pdf->setXY(125,$yFin);
			$pdf->SetFont('Arial','BI',9);
			$pdf->MultiCell(86,5,"\nORDEN DE TRABAJO No. ".$od["gen"]["id_orden"],1,"L");
			$pdf->SetFont('Arial','B',9);
			
			$yIni=$pdf->getY();
			$yFin = 45;
			$pdf->SetFont('Arial','B',9);
			$pdf->MultiCell(120,5,"CENTRO DE OPERACIÓN: ".$od["gen"]["zona"],1,"L");
			$pdf->setXY(125,$yFin);
			$valMitad = ($pdf->areaUtil-125+$pdf->lMargin)/2;
			$pdf->cell($valMitad,5,"TÚCAN Y/O PLACA",1,0,"L",true);
			$pdf->cell($valMitad,5,$od["gen"]["equipo"],1,1,"L",true);
			
			$yIni=$pdf->getY();
			
			$pdf->SetFont('Arial','',9);
			$pdf->MultiCell(120,5,"TIPO DE MANTENIMIENTO: ".$od["gen"]["tipo_mtto"],1,"L");
			$pdf->setXY(40+80+$pdf->lMargin,$yIni);
			$pdf->MultiCell($valMitad*2,5,"INVENTARIO EQUIPO",1,"C");
			$yFin = $pdf->getY();
			$pdf->SetFont('Arial','B',9);
			$pdf->MultiCell(120,5,$od["gen"]["rutina"],1,"l",true);
			$pdf->SetFont('Arial','',9);
			$pdf->MultiCell(120,5,"FECHA Y HORA DE IMPRESIÓN: ".ucfirst(strftime("%B %d de %Y %H:%M",strtotime(date("Y-m-d H:i:s")))),1,"L");
			$pdf->MultiCell(120,5,"FECHA Y HORA PLANEADA: ".ucfirst(strftime("%B %d de %Y %H:%M",strtotime($od["gen"]["fecha_planeada"]))),1,"L");
			$pdf->SetFont('Arial','B',8.5);
			$pdf->MultiCell(120,5,"LUGAR DE MANTENIMIENTO: INTERNO [__] EXTERNO [__] CARRO TALLER [__]",1,"L");
			$pdf->MultiCell(120,5,"MANTENIMIENTO: ",1,"L");
			$yPS = $pdf->getY();
			
			$pdf->SetFont('Arial','B',9);
			$pdf->MultiCell($pdf->areaUtil, 5,"PROBLEMAS Y SÍNTOMAS REPORTADOS POR EL CONDUCTOR:\n".$od["gen"]["ob_novedad"],1,"L");
			
			$pdf->MultiCell($pdf->areaUtil, 5,"CAUSA Y/O DIAGNOSTICO:\n",0,"L");
			
			$pdf->setWidths(array($valMitad,$valMitad));
			$pdf->setAligns(array('L','L'));
			$pdf->setBorders(array(1,1));
			$invEq = array(
					array("Marca",$od["gen"]["marca"]),
					array("Modelo",$od["gen"]["modelo"]),
					array("Tipo",$od["gen"]["tipo_vehiculo"]),
					array("Horómetro",""),
					array("Kilometraje",""));
			$pdf->rowHeight=5;
			foreach($invEq as $dx)
			{
				$pdf->setXY(125,$yFin);
				$pdf->row($dx);
				$yFin = $pdf->getY();
			}
			$pdf->setXY($pdf->lMargin,$yPS);
			$pdf->cell($pdf->areaUtil,20,"","LRB",1);
			
			//ACTIVIDADES
			$pdf->setXY($pdf->lMargin,$pdf->getY()+2);
			$pdf->setWidths(array(65,30,25,86));
			$pdf->SetFont('Arial','',10);
			$pdf->setAligns(array('C','C','C','C'));
			$pdf->setFills(array(1,1,1,1));
			$pdf->rowHeight=4;
			$pdf->row(array("ACTIVIDAD", "NOMBRE", "CARGO","OBSERVACIONES"));
			$pdf->setFills(array(0,0));
			$pdf->setAligns(array('L','L','L','L'));
			$pdf->SetFont('Arial','',9);
			$pdf->rowHeight=4.5;
			if(isset($od["ma"])){
				foreach($od["ma"] as $dx)
				{
					$pdf->row(array($dx["act"],$dx["persona"], $dx["cargo"], ""));
				}
			}else
			{
				for($i=0;$i<=4;$i++)
				{
					$pdf->row(array("","","",""));
				}
			}

			//ELEMENTOS
			$pdf->setXY($pdf->lMargin,$pdf->getY()+2);
			$pdf->setWidths(array(10, $pdf->areaUtil-10));
			$pdf->SetFont('Arial','',10);
			$pdf->setAligns(array('C','C'));
			$pdf->setFills(array(1,1));
			$pdf->rowHeight=4;
			$pdf->row(array("#", "REPUESTOS / INSUMOS"));
			$pdf->setFills(array(0,0));
			$pdf->setAligns(array('C','L'));
			$pdf->SetFont('Arial','',9);
			$pdf->rowHeight=4.5;
			if(isset($od["elementos"])){
				foreach($od["elementos"] as $dx)
				{
					$pdf->row(array($dx["cant"],$dx["cod"]));
				}
			}else
			{
				for($i=0;$i<=4;$i++)
				{
					$pdf->row(array("",""));
				}
			}
			//TRABAJOS Otros Talleres
			$pdf->SetFont('Arial','',10);
			$pdf->setXY($pdf->lMargin,$pdf->getY()+2);
			$pdf->cell($pdf->areaUtil,5,"TRABAJOS DE OTROS TALLERES",1,1,"C",true);
			//OTROS TALLERES
			if(isset($od["taller"]))	
			{
				foreach($od["taller"] as $dx)
				{
					$pdf->MultiCell($pdf->areaUtil,5,"NOMBRE DEL TALLER: ".$dx["proveedor"] ."-".$dx["direccion"]."-".$dx["contacto"]."-".$dx["telefono"],1,"L");
				}
			}
			
			
			
			//TRABAJOS A REALIZAR
			$pdf->SetFont('Arial','',10);
			$pdf->setXY($pdf->lMargin,$pdf->getY()+2);
			$pdf->cell($pdf->areaUtil,5,"TRABAJOS A REALIZAR",1,1,"C",true);
			$pdf->SetFont('Arial','',8);
			if($od["gen"]["observaciones"] != "")
				$pdf->MultiCell($pdf->areaUtil, 3.5, $od["gen"]["observaciones"], 1, "L");
			else
				$pdf->cell($pdf->areaUtil,10,"",1,1);
			$pdf->SetFont('Arial','',10);


			//OBSERVACIONES
			$pdf->setXY($pdf->lMargin,$pdf->getY()+2);
			$pdf->setWidths(array(20, $pdf->areaUtil-20));
			$pdf->SetFont('Arial','',10);
			$pdf->setAligns(array('C','C'));
			$pdf->setFills(array(1,1));
			$pdf->rowHeight=5;
			$pdf->row(array("FECHA", "OBSERVACIONES"));
			$pdf->setFills(array(0,0));
			$pdf->setAligns(array('C','L'));
			$pdf->SetFont('Arial','',9);
			$pdf->rowHeight=5;
			for($i=0;$i<=5;$i++)
			{
				$pdf->row(array("",""));
			}

			//FINAL
			$pdf->SetFont('Arial','',9);
			$pdf->setWidths(array($pdf->areaUtil/4, $pdf->areaUtil/4,$pdf->areaUtil/4));
			$pdf->setAligns(array('L', 'L'));
			$pdf->row(array("HORA INICIO:", "HORA FINAL:","ORDEN CERRADA:"));			
			
			$pdf->setY($pdf->getY()+5);
			$y = $pdf->getY();
			$pdf->Cell($pdf->areaUtil,3.5,"______________________________________________________",0,1,'R');
			$pdf->Cell($pdf->areaUtil,4.5,"FIRMA DE MANTENIMIENTO                        ",0,0,'R');
			
			$pdf->SetY(-20);
			$pdf->SetFont('Arial','I',8);
			$pdf->Cell(0,10,'GMA-RE-02 /VERSION 2 / 14/04/2016',0,0,'L');
			$pdf->Cell(0,10,$pdf->PageNo().' de {nb}',0,0,'C');
						
		}
	}
	else{
		
		foreach($orden as $od)
		{
			echo $centro;
			$pdf->AddPage();
			$pdf->SetFont('Arial','B',12);
			$archivo = $CFG->dirroot.'/files/centros/logo/4.jpg';
			$arrayInfo=getimagesize($archivo);
			$ancho=$arrayInfo[0]/$pdf->k;
			$alto=$arrayInfo[1]/$pdf->k;
			if($alto > 27)
			{
				$ancho = resizeToHeight(27, $ancho, $alto);
				$alto = 27;
			}
			$x = ($pdf->lMargin+20) -($ancho/2);
			$pdf->Image($archivo, $x, $pdf->getY()+3, $alto);

			$pdf->cell(40,25,"",1,0);
			$pdf->SetFont('Arial','B',16);
			$pdf->MultiCell($pdf->areaUtil-40-65,25,"ORDEN DE TRABAJO",1,"C");
			$codigos = array(
				array(""),
				array("Fecha Emisión: 2014-05-14"),
				array("Versión: 3"),
				array("Página: 1 de 1"),
				array(""));
			$pdf->SetFont('Arial','',10);
			$pdf->rowHeight=5;
			$pdf->setWidths(array(65));
			$yFin = 5;
			foreach($codigos as $line)
			{
				$pdf->setXY(145.9,$yFin);
				$pdf->row($line);
				$yFin = $pdf->getY();
			}

			$pdf->cell(120,14,"",1,1);
			$pdf->setXY($pdf->lMargin,$yFin);
			$pdf->MultiCell(125,4,"\nDEPARTAMENTO DE MANTENIMIENTO\n(TALLER INTERNO Y/O EXTERNO)",0,"C");
			$pdf->setXY(125,$yFin);
			$pdf->SetFont('Arial','BI',11);
			$pdf->cell($pdf->areaUtil-125+$pdf->lMargin,7,"ORDEN DE TRABAJO No. ".$od["gen"]["id_orden"],1,1,"C");
			$pdf->SetFont('Arial','B',11);
			
			$pdf->setXY(125,$pdf->getY());
			$valMitad = ($pdf->areaUtil-125+$pdf->lMargin)/2;
			$pdf->cell($valMitad,7,"MÓVIL",1,0,"C",true);
			$pdf->cell($valMitad,7,$od["gen"]["equipo"],1,1,"C",true);
			$yIni=$pdf->getY();
			$pdf->SetFont('Arial','',10);
			$pdf->MultiCell(120,5,"TIPO DE MANTENIMIENTO: ".$od["gen"]["tipo_mtto"],1,"L");
			$pdf->setXY(40+80+$pdf->lMargin,$yIni);
			$pdf->MultiCell($valMitad*2,5,"INVENTARIO EQUIPO",1,"C");
			$yFin = $pdf->getY();
			$pdf->MultiCell(120,5,$od["gen"]["rutina"],1,"C",true);
			$pdf->MultiCell(120,5,"FECHA Y HORA PLANEADA: ".ucfirst(strftime("%B %d de %Y %H:%M",strtotime($od["gen"]["fecha_planeada"]))),1,"L");
			$pdf->MultiCell(120,5,"FECHA Y HORA INICIO EJECUCIÓN: ",1,"L");
			$pdf->MultiCell(120,5,"FECHA Y HORA FIN EJECUCION: ",1,"L");
			$yPS = $pdf->getY();
			$pdf->SetFont('Arial','',9);
			$pdf->MultiCell($pdf->areaUtil, 5,"PROBLEMAS/SÍNTOMAS REPORTADOS POR EL CONDUCTOR:\n".$od["gen"]["observaciones"],0,"L");
			$pdf->setWidths(array($valMitad,$valMitad));
			$pdf->setAligns(array('L','L'));
			$pdf->setBorders(array(1,1));
			$invEq = array(
					array("Marca",$od["gen"]["marca"]),
					array("Modelo",$od["gen"]["modelo"]),
					array("Tipo",$od["gen"]["tipo_vehiculo"]),
					array("Kilometraje",""),
					array("Horometro",""));
			$pdf->rowHeight=3.5;
			foreach($invEq as $dx)
			{
				$pdf->setXY(125,$yFin);
				$pdf->row($dx);
				$yFin = $pdf->getY();
			}
			$pdf->setXY($pdf->lMargin,$yPS);
			$pdf->cell($pdf->areaUtil,15,"","LRB",1);

			//Causa -Actividad
			$pdf->SetFont('Arial','',10);
			$pdf->setXY($pdf->lMargin,$pdf->getY()+2);
			$pdf->cell($pdf->areaUtil,5,"CAUSA ACTIVIDAD",1,1,"C",true);
			$pdf->SetFont('Arial','',8);
			$pdf->cell($pdf->areaUtil,15,"",1,1);
			$pdf->SetFont('Arial','',10);
			
			//ACTIVIDADES
			$pdf->setXY($pdf->lMargin,$pdf->getY()+2);
			#$pdf->setWidths(array(65,45,35,60));
			$pdf->setWidths(array(105,100));
			$pdf->SetFont('Arial','',10);
			#$pdf->setAligns(array('C','C','C','C'));
			$pdf->setAligns(array('C','C'));
			#$pdf->setFills(array(1,1,1,1));
			$pdf->setFills(array(1,1));
			$pdf->rowHeight=4;
			#$pdf->row(array("ACTIVIDAD", "NOMBRE", "CARGO","OBSERVACION"));
			$pdf->row(array("ACTIVIDAD", "OBSERVACION"));
			#$pdf->setFills(array(0,0,0,0));
			$pdf->setFills(array(0,0));
			#$pdf->setAligns(array('L','L','L','L'));
			$pdf->setAligns(array('L','L'));
			$pdf->SetFont('Arial','',9);
			$pdf->rowHeight=3.5;
			if(isset($od["ma"])){
				foreach($od["ma"] as $dx)
				{
#$pdf->row(array($dx["act"],$dx["persona"],$dx["cargo"],""));
					$pdf->row(array($dx["act"],""));
				}
			}else
			{
				for($i=0;$i<=3;$i++)
				{
					$pdf->row(array("","",""));
				}
			}

			//ELEMENTOS
			$pdf->setXY($pdf->lMargin,$pdf->getY()+2);
			$pdf->setWidths(array(10, $pdf->areaUtil-10));
			$pdf->SetFont('Arial','',10);
			$pdf->setAligns(array('C','C'));
			$pdf->setFills(array(1,1));
			$pdf->rowHeight=4;
			$pdf->row(array("#", "REPUESTOS / INSUMOS"));
			$pdf->setFills(array(0,0));
			$pdf->setAligns(array('C','L'));
			$pdf->SetFont('Arial','',9);
			$pdf->rowHeight=3.5;
			if(isset($od["elementos"])){
				foreach($od["elementos"] as $dx)
				{
					$pdf->row(array($dx["cant"],$dx["cod"]));
				}
			}else
			{
				for($i=0;$i<=4;$i++)
				{
					$pdf->row(array("",""));
				}
			}
			
			//TRABAJOS A REALIZAR
			// $pdf->SetFont('Arial','',10);
			// $pdf->setXY($pdf->lMargin,$pdf->getY()+2);
			// $pdf->cell($pdf->areaUtil,5,"TRABAJOS A REALIZAR",1,1,"C",true);
			// $pdf->SetFont('Arial','',8);
			// if($od["gen"]["observaciones"] != "")
				// $pdf->MultiCell($pdf->areaUtil, 3.5, $od["gen"]["observaciones"], 1, "L");
			// else
				// $pdf->cell($pdf->areaUtil,20,"",1,1);
			// $pdf->SetFont('Arial','',10);

			//OTROS TALLERES
			if(isset($od["taller"]))	
			{
				foreach($od["taller"] as $dx)
				{
					$pdf->setXY($pdf->lMargin,$pdf->getY()+2);
					$pdf->setWidths(array(40, 166));
					$pdf->SetFont('Arial','',10);
					$pdf->setAligns(array('L','L'));
					$pdf->rowHeight=4;
					$pdf->cell($pdf->areaUtil,5,"TRABAJOS DE OTROS TALLERES",1,1,"C",true);
					$pdf->SetFont('Arial','',9);
					$pdf->row(array("NOMBRE TALLER", $dx["proveedor"]));
					$pdf->row(array("DIRECCIÓN TALLER", $dx["direccion"]));
					$pdf->row(array("NOMBRE CONTACTO", $dx["contacto"]));
					$pdf->row(array("TELEFONO CONTACTO", $dx["telefono"]));
					$pdf->row(array("CELULAR CONTACTO", $dx["celular"]));
				}
			}

			//OBSERVACIONES
			$pdf->setXY($pdf->lMargin,$pdf->getY()+2);
			$pdf->setWidths(array(20, $pdf->areaUtil-20));
			$pdf->SetFont('Arial','',10);
			$pdf->setAligns(array('C','C'));
			$pdf->setFills(array(1,1));
			$pdf->rowHeight=4;
			$pdf->row(array("FECHA", "OBSERVACIONES"));
			$pdf->setFills(array(0,0));
			$pdf->setAligns(array('C','L'));
			$pdf->SetFont('Arial','',9);
			$pdf->rowHeight=4;
			for($i=0;$i<=6;$i++)
			{
				$pdf->row(array("",""));
			}

			//FINAL
			$pdf->SetFont('Arial','',9);
			$pdf->setWidths(array($pdf->areaUtil/2, $pdf->areaUtil/2));
			$pdf->setAligns(array('L', 'L'));
			#$pdf->row(array("HORA INICIO:", "HORA FINAL:"));			
			// if($od["gen"]["herramientas"] != "")
			// {
				// $pdf->setY($pdf->getY()+1);
				// $pdf->MultiCell($pdf->areaUtil,3.5,"HERRAMIENTAS : ".$od["gen"]["herramientas"]);
			// }

			$pdf->setY($pdf->getY()+5);
			$y = $pdf->getY();
			$pdf->MultiCell($pdf->areaUtil,3.5,"FIRMA TECNICO MANTENIMIENTO      ________________________            FIRMA TECNICO SUPERVISOR _________________________");
			$pdf->Ln(5);
			$pdf->MultiCell($pdf->areaUtil,3.5,"FIRMA COORDINADOR MANTENIMIENTO _________________________    ORDEN CERRADA SI  ____ ");
			$pdf->Ln(2);
			$pdf->MultiCell($pdf->areaUtil,3.5,"Nota: Promoambiental Caribe S.A. ESP establece como politica el uso obligatorio de los EPP para la ejecución de las actividades.");
			
					
			if($formatoAparte)
			{
				foreach($od["taller"] as $dx)
				{
				  for($i=1; $i<=2; $i++)
				  {
					 //preguntar($dx);
					 $pdf->AddPage("L");
					if($od["gen"]["logo"] != "")
					{
						$archivo = $CFG->dirroot."/tmp/".$od["gen"]["logo"];
						if(file_exists($archivo)) unlink ($archivo);
						copy($CFG->dirroot."/files/centros/logo/".$centro, $archivo);
						$arrayInfo=getimagesize($archivo);
						$ancho=$arrayInfo[0]/$pdf->k;
						$alto=$arrayInfo[1]/$pdf->k;
						if($alto > 30)
						{
							$ancho = resizeToHeight(30, $ancho, $alto);
							$alto = 30;
						}
						$x = ($pdf->lMargin+20) -($ancho/2);
						$pdf->Image($archivo, $x, $pdf->getY()+3, $alto);
					}

					 $pdf->cell(40,25,"",1,0);
					 $pdf->SetFont('Arial','B',16);
					 $pdf->MultiCell(165,25,"REMISIÓN A TALLER EXTERNO",1,"C");
					 $codigos = array(
					 array("Fecha Emisión: ".date("Y-m-d")),
					 array("Fecha Actualización:"),
					 array("Versión: 1"),
					 array("Código: MT-F-10"),
					 array("Página: 1 de 1"));
					 $pdf->SetFont('Arial','',10);
					 $pdf->rowHeight=5;
					 $pdf->setWidths(array(65));
					 $yFin = 5;
					 foreach($codigos as $line)
					 {
						$pdf->setXY(210,$yFin);
						$pdf->row($line);
						$yFin = $pdf->getY();
					 }
					 $pdf->setXY($pdf->lMargin,$pdf->getY()+2);
					 $pdf->SetFont('Arial','',12);
					 $pdf->cell(48,6,"FECHA:",1,0,"L", true);
					 $pdf->cell(47,6, $od["gen"]["fecha_planeada"],1,0,"C");
					 $pdf->cell(38,6,"PLACA:",1,0,"L", true);
					 $pdf->cell(47,6, $od["gen"]["placa"],1,0,"C");
					 $pdf->cell(43,6,"No. INTERNO:",1,0,"L", true);
					 $pdf->cell(47,6, $od["gen"]["codigo_vehiculo"],1,1,"C");
					 $pdf->cell(48,6,"NOMBRE DEL TALLER:",1,0,"L", true);
					 $pdf->cell(222,6,$dx["proveedor"],1,1,"L");
					 $pdf->cell(48,6,"DIRECCIÓN:",1,0,"L", true);
					 $pdf->cell(222,6,$dx["direccion"],1,1,"L");
					 $pdf->cell(48,6,"TELÉFONO:",1,0,"L", true);
					 $pdf->cell(90,6,$dx["telefono"],1,0,"L");
					 $pdf->cell(48,6,"CELULAR:",1,0,"L", true);
					 $pdf->cell(84,6,$dx["celular"],1,1,"L");
					 $pdf->cell(48,6,"CONTACTO:",1,0,"L", true);
					 $pdf->cell(222,6,$dx["contacto"],1,1,"L");
		
					 $pdf->setXY($pdf->lMargin,$pdf->getY()+2);
					 $pdf->cell(270,6,"DETALLE DE LA REPARACIÓN:",1,1,"C", true);
					 $pdf->cell(270,37,$od["gen"]["ob_novedad"],1,1);
					 $pdf->cell(270,6,"REPUESTOS UTILIZADOS:",1,1,"C", true);
					 $pdf->cell(270,37,"",1,1);
					 $pdf->cell(270,6,"OBSERVACIONES:",1,1,"L", true);
					 $pdf->cell(270,25,"",1,1);
					 $pdf->SetFont('Arial','',10);
					 $pdf->cell(270,6,"NOTA: FAVOR ANEXAR ÉSTE FORMATO, DEBIDAMENTE DILIGENCIADO, A LA FACTURA O CUENTA DE COBRO",0,1,"L");

					 $pdf->setXY($pdf->lMargin,$pdf->getY()+5);
					 $pdf->SetFont('Arial','',12);
					 $pdf->cell(35,6,"RECIBIDO POR:",1,0,"L", true);
					 $pdf->cell(235,6, "",1,1);
					 $pdf->cell(35,6,"REMITENTE:",1,0,"L", true);
					 $pdf->cell(80,6, "",1,0);
					 $pdf->cell(55,6,"FIRMA DEL REMITENTE:",1,0,"L", true);
					 $pdf->cell(100,6, "",1,0);
				  }
				}
			}
			
			
		}
	}
	$pdf->Output();
}

function historicoFechasProgramacion($idOT)
{
	global $db, $CFG;

	$qid = $db->sql_query("SELECT h.*, p.nombre||' '||p.apellido as persona
		FROM mtto.ordenes_trabajo_fechas_programadas h
		LEFT JOIN personas p ON p.id=h.id_persona
		WHERE h.id_orden_trabajo='".$idOT."'
		ORDER BY fecha");
?>
	<form class="form">
		<table width="100%">
			<tr>
				<td height="40" colspan=3 align="center"><span class="azul_16"><strong>HISTÓRICO FECHAS PROGRAMADAS</strong></span></td>
			</tr>
			<tr>
				<td valign="top">
					<table width="100%" cellpadding="5" cellspacing="3">
						<tr>
							<td>
								<table width="100%" border=1 bordercolor="#7fa840" align="center" id="tabla_mov">
									<tr>
										<td align="center">FECHA</td>
										<td align="center">PERSONA</td>
									</tr>
<?
									while($ot = $db->sql_fetchrow($qid))
									{
										echo "<tr><td>".$ot["fecha"]."</td><td>".$ot["persona"]."</td></tr>";
									}
?>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</form>
<?
}




include($CFG->dirroot."/templates/footer_popup.php");
?>
