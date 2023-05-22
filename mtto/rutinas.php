<?
include_once("../application.php");

if(!isset($_SESSION[$CFG->sesion]["user"])){
  $errorMsg="No existe la sesión.";
  error_log($errorMsg);
  die($errorMsg);
}
$user=$_SESSION[$CFG->sesion]["user"];

verificarPagina(simple_me($ME));

//preguntar($_GET);
//preguntar($_POST);

$mode=nvl($_GET["mode"],nvl($_POST["mode"],""));

switch(nvl($mode)){

	case "agregar":
	  agregar(nvl($_GET));
	break;

	case "insertar":
		insertar($_POST);
	break;

	case "editar":
		editar($_GET["id"],nvl($_GET["devolver"],0));
	break;

	case "actualizar":
		actualizar($_POST);
	break;

	case "eliminar":
		eliminar($_GET["id"]);
	break;

	case "listar_actividades":
		listar_actividades($_GET["id_rutina"]);
	break;

	case "agregar_actividad":
	  agregar_actividad($_GET);
	break;

	case "insertar_actividad":
	  insertar_actividad($_POST);
	break;

	case "editar_actividad":
	  editar_actividad($_GET);
	break;

	case "actualizar_actividad":
		actualizar_actividad($_POST);
	break;

	case "eliminar_actividad":
		eliminar_actividad($_GET["id"]);
	break;

	case "listar_cargos":
		listar_cargos($_GET["id_actividad"]);
	break;

	case "agregar_cargo":
		agregar_cargo($_GET);
	break;

	case "insertar_cargo":
		insertar_cargo($_POST);
	break;

	case "editar_cargo":
		editar_cargo($_GET);
	break;

	case "actualizar_cargo":
		actualizar_cargo($_POST);
	break;

	case "eliminar_cargo":
		eliminar_cargo($_GET["id"]);
	break;

	case "listar_mediciones":
		listar_mediciones($_GET["id_rutina"]);
	break;

	case "agregar_medicion":
	  agregar_medicion($_GET);
	break;

	case "insertar_medicion":
	  insertar_medicion($_POST);
	break;

	case "editar_medicion":
	  editar_medicion($_GET);
	break;

	case "actualizar_medicion":
		actualizar_medicion($_POST);
	break;

	case "eliminar_medicion":
		eliminar_medicion($_GET["id"]);
	break;

	case "agregar_unidad":
	  agregar_unidad();
	break;

	case "insertar_unidad":
	  insertar_unidad($_POST);
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

	case "doblar_rutina":
		doblar_rutina($_GET);
	break;

	case "listar_talleres":
		listar_talleres($_GET["id_rutina"]);
	break;

	case "agregar_taller":
	  agregar_taller($_GET);
	break;

	case "insertar_taller":
	  insertar_taller($_POST);
	break;

	case "eliminar_taller":
		eliminar_taller($_GET["id"]);
	break;

}


function agregar($rutina=array())
{
	global $CFG, $db,$ME;

	$condicionCentroGr=$condicionCentro="true";
	$user=$_SESSION[$CFG->sesion]["user"];
	if($user["nivel_acceso"]!=1)
	{	
		$condicionCentroGr="(id_centro IS NULL OR id_centro IN (" . implode(",",$user["id_centro"]) . "))";
		$condicionCentro="id IN (".implode(",",$user["id_centro"]).")";
	}

	if(isset($rutina["id_grupo"]))
	{
		$datos = array($rutina["id_grupo"]);
		obtenerIdsGrupos($rutina["id_grupo"],$datos);
		$cond = $condicionCentroGr." AND id in (".implode(",",$datos).")";
//		$db->build_recursive_tree_path_sesgado("mtto.grupos",$grupos,$rutina["id_grupo"],"id","nombre","",$cond);
		$db->crear_select("
			SELECT id, getpath(id,'mtto.grupos')
			FROM mtto.grupos
			WHERE id IN (SELECT * FROM getParents($rutina[id_grupo],'mtto.grupos'))
			ORDER BY length(getpath(id,'mtto.grupos'))
			",$grupos,$rutina["id_grupo"]);
		$sinEquipo = true;
	}else
		$db->build_recursive_tree_path("mtto.grupos",$grupos,"","id","id_superior","nombre","-1","",$condicionCentroGr);

	$db->crear_select("SELECT id, centro FROM centros WHERE ".$condicionCentro." ORDER BY centro",$centros);
	$db->crear_select("SELECT id, sistema FROM mtto.sistemas ORDER BY sistema",$sistemas,nvl($rutina["id_sistema"]));
	$db->build_recursive_tree_path("mtto.equipos",$equipos,"","id","id_superior","nombre","-1","",$condicionCentroGr);
	$db->crear_select("SELECT id, frecuencia FROM mtto.frecuencias ORDER BY frecuencia",$frecuencias);

	if(isset($rutina["id_tipo_mantenimiento"]))
	{
		$cond = " AND id=".$rutina["id_tipo_mantenimiento"];
		$db->crear_select("SELECT id, tipo FROM mtto.tipos WHERE true ".$cond." ORDER BY tipo",$tipos,nvl($rutina["id_tipo_mantenimiento"]));
		if($rutina["id_tipo_mantenimiento"]==2)
			$sinFrecuencias = true;
	}else
		$db->crear_select("SELECT id, tipo FROM mtto.tipos ORDER BY tipo",$tipos);
	
	$db->crear_select("SELECT id, prioridad FROM mtto.prioridades ORDER BY prioridad",$prioridades);
	$newMode="insertar";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/rutinas_form.php");
}

function insertar($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir . "/mtto.rutinas.php");
	$entidad->loadValues($frm);
	$id=$entidad->insert();

	$goto = $CFG->wwwroot."/mtto/rutinas.php?mode=editar&devolver=".$frm["devolver"]."&id=".$id;
	echo "<script>window.location.href='".$goto."';</script>";
}

function editar($idRutina,$devolver)
{
	global $CFG, $db,$ME;

	$rutina = $db->sql_row("SELECT * FROM mtto.rutinas WHERE id=".$idRutina);
	$rutina["devolver"]=$devolver;
	$condicionCentroGr="true";
	$user=$_SESSION[$CFG->sesion]["user"];
	if($user["nivel_acceso"]!=1)
	{	
		$condicionCentroGr="(id_centro IS NULL OR id_centro IN (" . implode(",",$user["id_centro"]) . "))";
	}

	$rutinasCentros=array();
	$qidRC = $db->sql_query("SELECT id_centro FROM mtto.rutinas_centros WHERE id_rutina=".$rutina["id"]);
	while($queryRC = $db->sql_fetchrow($qidRC))
	{
		$rutinasCentros[] = $queryRC["id_centro"];
	}
	$db->crear_select("SELECT id, sistema FROM mtto.sistemas ORDER BY sistema",$sistemas,$rutina["id_sistema"]);
	$db->build_recursive_tree_path("mtto.grupos",$grupos,$rutina["id_grupo"],"id","id_superior","nombre","-1","",$condicionCentroGr);
	$db->build_recursive_tree_path("mtto.equipos",$equipos,$rutina["id_equipo"],"id","id_superior","nombre","-1","",$condicionCentroGr);
	$db->crear_select("SELECT id, frecuencia FROM mtto.frecuencias ORDER BY frecuencia",$frecuencias,$rutina["id_frecuencia"]);
	$db->crear_select("SELECT id, tipo FROM mtto.tipos ORDER BY tipo",$tipos,$rutina["id_tipo_mantenimiento"]);
	$db->crear_select("SELECT id, prioridad FROM mtto.prioridades ORDER BY prioridad",$prioridades,$rutina["id_prioridad"]);
//	$qidEle = $db->sql_query("SELECT e.id, e.codigo||' ('||e.elemento||'/'||u.unidad||')' as cod FROM mtto.elementos e LEFT JOIN mtto.unidades u ON u.id=e.id_unidad ORDER BY e.codigo,e.elemento");
	$qidEleExist = $db->sql_query("SELECT x.cantidad, x.id, e.id as id_elemento 
		FROM mtto.rutinas_elementos x
		LEFT JOIN mtto.elementos e ON e.id=x.id_elemento 
		LEFT JOIN mtto.unidades u ON u.id=e.id_unidad 
		WHERE x.id_rutina='".$rutina["id"]."'
		ORDER BY x.id");

	$newMode="actualizar";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/rutinas_form.php");
}


function actualizar($frm)
{
	global $CFG, $db,$ME;

	$ant = $db->sql_row("SELECT id_frecuencia, frec_horas, frec_km FROM mtto.rutinas WHERE id=".$frm["id"]);

	include($CFG->modulesdir . "/mtto.rutinas.php");
	$entidad->loadValues($frm);
	$entidad->set("mode","update");
	$entidad->update();

	//elementos
	$elementos = $elementosEx = array();
	foreach($frm as $key => $value)
	{
		if(preg_match("/id_elemento_/",$key,$match))
		{
			$ord = str_replace("id_elemento_","",$key);
			$elementos[$ord]["id_elemento"]=$value;
		}
		if(preg_match("/cantidad_/",$key,$match))
		{
			$ord = str_replace("cantidad_","",$key);
			$elementos[$ord]["cantidad"]=$value;
		}
		if(preg_match("/existe_id_ele_/",$key,$match))
		{
			$ord = str_replace("existe_id_ele_","",$key);
			$elementosEx[$ord]["id_elemento"]=$value;
		}
		if(preg_match("/ex_cant_/",$key,$match))
		{
			$ord = str_replace("ex_cant_","",$key);
			$elementosEx[$ord]["cantidad"]=$value;
		}
	}


	$resultado = array_merge_recursive($elementosEx,$elementos);
	$db->sql_query("DELETE FROM mtto.rutinas_elementos WHERE id_rutina=".$frm["id"]);

	foreach($resultado as $dx)
	{
		$dx["id_rutina"]=$frm["id"];
		if(trim($dx["cantidad"]) == "") $dx["cantidad"]=1;
		if(trim($dx["id_elemento"]) != "%" && trim($dx["id_elemento"]) != "")
		{
			include($CFG->modulesdir . "/mtto.rutinas_elementos.php");
			$entidad->loadValues($dx);
			$id=$entidad->insert();
		}
	}

	//si se cambian las frecuencias se borran las ordenes de trabajo no cerradas
	$actual = $db->sql_row("SELECT id_frecuencia, frec_horas, frec_km FROM mtto.rutinas WHERE id=".$frm["id"]);
	if($ant["id_frecuencia"] != $actual["id_frecuencia"] || $ant["frec_horas"] != $actual["frec_horas"] || $ant["frec_km"] != $actual["frec_km"])
	{
		$db->sql_query("DELETE FROM mtto.ordenes_trabajo WHERE id IN (SELECT id FROM mtto.ordenes_trabajo WHERE id_rutina='".$frm["id"]."' AND id_estado_orden_trabajo IN (SELECT id FROM mtto.estados_ordenes_trabajo WHERE NOT cerrado))");	
	}

	if($frm["accion"]=="cerrar"){
		if($frm["devolver"]==1)
			echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.cargarValoresRutina();\nwindow.close();\n</script>";	
		else
			echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
	}else
	{
		if($frm["devolver"]==1)
			echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.cargarValoresRutina();</script>";	
		echo "<script>window.location.href='".$CFG->wwwroot."/mtto/rutinas.php?mode=editar&devolver=".$frm["devolver"]."&id=".$frm["id"]."';</script>";
	}
}

function eliminar($idRutina)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir . "/mtto.rutinas.php");
	$entidad->set("mode","eliminar");
	$entidad->set("id",$idRutina);
	$entidad->delete();
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}


function listar_actividades($idRutina)
{
	global $CFG, $db,$ME;
	
	$qidR = $db->sql_query("SELECT * FROM mtto.rutinas_actividades WHERE id_rutina=".$idRutina." ORDER BY orden");

	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/rutinas_actividades_listado.php");
}

function agregar_actividad($actividad)
{
	global $CFG, $db,$ME;

	$newMode="insertar_actividad";		
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/rutinas_actividades_form.php");
}

function insertar_actividad($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir . "/mtto.rutinas_actividades.php");
	$entidad->loadValues($frm);
	$id=$entidad->insert();

	$valorTiempo = cargarValorTiempoRutina($frm["id_rutina"]);

	$goto = $CFG->wwwroot."/mtto/rutinas.php?mode=editar_actividad&id=".$id;
	echo "<script>window.opener.cargarValorTiempoRutinaUno('".$valorTiempo."');window.location.href='".$goto."';</script>";
}

function editar_actividad($frm)
{
	global $CFG, $db,$ME;

	$actividad = $db->sql_row("SELECT * FROM mtto.rutinas_actividades WHERE id=".$frm["id"]);
	$newMode="actualizar_actividad";		
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/rutinas_actividades_form.php");
}

function actualizar_actividad($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir . "/mtto.rutinas_actividades.php");
	$entidad->loadValues($frm);
	$entidad->set("mode","update");
	$entidad->update();
	$valorTiempo = cargarValorTiempoRutina($frm["id_rutina"]);

	echo "<script language=\"JavaScript\" type=\"text/javascript\">window.opener.cargarValorTiempoRutinaUno('".$valorTiempo."');\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function eliminar_actividad($idActividad)
{
	global $CFG, $db,$ME;

	$idRutina = $db->sql_row("SELECT id_rutina FROM mtto.rutinas_actividades WHERE id=".$idActividad);
	include($CFG->modulesdir . "/mtto.rutinas_actividades.php");
	$entidad->set("mode","eliminar");
	$entidad->set("id",$idActividad);
	$entidad->delete();
	$valorTiempo = cargarValorTiempoRutina($idRutina["id_rutina"]);
	echo "<script language=\"JavaScript\" type=\"text/javascript\">window.opener.cargarValorTiempoRutinaUno('".$valorTiempo."');\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function cargarValorTiempoRutina($idRutina)
{
	global $CFG, $db,$ME;

	$tiempo = $db->sql_row("
			SELECT sum(total) as tot
			FROM (
				SELECT sum(tiempo) as total FROM mtto.rutinas_actividades WHERE id_rutina=".$idRutina."
				UNION
				SELECT sum(tiempo)*60 as total FROM mtto.rutinas_talleres  WHERE id_rutina=".$idRutina."
			) AS foo
				");
	return $tiempo["tot"];
}



function listar_cargos($idActividad)
{
	global $CFG, $db,$ME;
	
	$qidR = $db->sql_query("SELECT rac.*, c.nombre AS cargo FROM mtto.rutinas_actividades_cargos rac LEFT JOIN cargos c ON c.id=rac.id_cargo WHERE id_actividad=".$idActividad);

	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/rutinas_actividades_cargos_listado.php");
}

function agregar_cargo($cargo)
{
	global $CFG, $db,$ME;

	$newMode="insertar_cargo";
	$db->crear_select("SELECT id, nombre FROM cargos where id_superior=32 ORDER BY nombre",$selectCargos);
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/rutinas_actividades_cargos_form.php");
}

function insertar_cargo($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir . "/mtto.rutinas_actividades_cargos.php");
	$entidad->loadValues($frm);
	$id=$entidad->insert();
	$valorTiempo = cargarValorTiempoActividad($frm["id_actividad"]);
	echo "<script language=\"JavaScript\" type=\"text/javascript\">window.opener.cargarValorTiempoActividadUno('".$valorTiempo."');\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function editar_cargo($frm)
{
	global $CFG, $db,$ME;

	$cargo = $db->sql_row("SELECT * FROM mtto.rutinas_actividades_cargos WHERE id=".$frm["id"]);
	$db->crear_select("SELECT id, nombre FROM cargos ORDER BY nombre",$selectCargos,$cargo["id_cargo"]);

	$newMode="actualizar_cargo";		
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/rutinas_actividades_cargos_form.php");
}

function actualizar_cargo($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir . "/mtto.rutinas_actividades_cargos.php");
	$entidad->loadValues($frm);
	$entidad->set("mode","update");
	$entidad->update();
	$valorTiempo = cargarValorTiempoActividad($frm["id_actividad"]);

	echo "<script language=\"JavaScript\" type=\"text/javascript\">window.opener.cargarValorTiempoActividadUno('".$valorTiempo."');\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function eliminar_cargo($idCargo)
{
	global $CFG, $db,$ME;

	$idActividad = $db->sql_row("SELECT id_actividad FROM mtto.rutinas_actividades_cargos WHERE id=".$idCargo);
	$valorTiempo = cargarValorTiempoActividad($idActividad["id_actividad"]);
	include($CFG->modulesdir . "/mtto.rutinas_actividades_cargos.php");
	$entidad->set("mode","eliminar");
	$entidad->set("id",$idCargo);
	$entidad->delete();
	$valorTiempo = cargarValorTiempoActividad($idActividad["id_actividad"]);
	echo "<script language=\"JavaScript\" type=\"text/javascript\">window.opener.cargarValorTiempoActividadUno('".$valorTiempo."');\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function cargarValorTiempoActividad($idActividad)
{
	global $CFG, $db,$ME;

	$tiempo = $db->sql_row("SELECT sum(tiempo) as total FROM mtto.rutinas_actividades_cargos WHERE id_actividad=".$idActividad);
	return $tiempo["total"];
}


function listar_mediciones($idRutina)
{
	global $CFG, $db,$ME;
	
	$qidR = $db->sql_query("SELECT r.*, u.unidad FROM mtto.rutinas_mediciones r LEFT JOIN mtto.unidades u ON u.id=r.id_unidad WHERE r.id_rutina=".$idRutina." ORDER BY r.orden");

	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/rutinas_mediciones_listado.php");
}

function agregar_medicion($medicion)
{
	global $CFG, $db,$ME;

	$newMode="insertar_medicion";		
	$db->crear_select("SELECT id, unidad FROM mtto.unidades ORDER BY unidad",$unidades);
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/rutinas_mediciones_form.php");
}

function insertar_medicion($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir . "/mtto.rutinas_mediciones.php");
	$entidad->loadValues($frm);
	$id=$entidad->insert();

	echo "<script>\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function editar_medicion($frm)
{
	global $CFG, $db,$ME;

	$newMode="actualizar_medicion";
	$medicion = $db->sql_row("SELECT * FROM mtto.rutinas_mediciones WHERE id=".$frm["id"]);
	$db->crear_select("SELECT id, unidad FROM mtto.unidades ORDER BY unidad",$unidades,$medicion["id_unidad"]);
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/rutinas_mediciones_form.php");
}

function actualizar_medicion($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir . "/mtto.rutinas_mediciones.php");
	$entidad->loadValues($frm);
	$entidad->set("mode","update");
	$entidad->update();
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function eliminar_medicion($idMedicion)
{
	global $CFG, $db,$ME;

	$num = $db->sql_row("SELECT count(*) as num FROM mtto.ordenes_rutinas_mediciones WHERE id_medicion=".$idMedicion);
	if($num["num"]!=0)
	{
		include($CFG->dirroot."/templates/header_popup.php");
		?>
		<table width="100%">
			<tr><td height="10">&nbsp;</td></tr>
			<tr>
				<td valign="top">
					<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
						<tr>
							<td>
								<table width="100%" border=1 bordercolor="#7fa840" id="tabla_actividades">
									<tr>
										<td align='center'><span class="azul_12">LA MEDICIÓN ESTÁ RELACIONADA CON UNA ORDEN DE TRABAJO.<br /><br />¡NO SE PUEDE BORRAR!</span></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td height="50" valign="bottom"><input type="button" class="boton_verde" value="Cerrar" onclick="window.close()"/></td>
			</tr>
		</table>
		<?
	}else
	{
		include($CFG->modulesdir . "/mtto.rutinas_mediciones.php");
		$entidad->set("mode","eliminar");
		$entidad->set("id",$idMedicion);
		$entidad->delete();
		echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
	}
}

function agregar_unidad()
{
	global $CFG, $db,$ME;

	$newMode="insertar_unidad";		
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/rutinas_unidades_form.php");
}

function insertar_unidad($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir . "/mtto.unidades.php");
	$entidad->loadValues($frm);
	$id=$entidad->insert();

	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.cargarValoresUnidades();\nwindow.close();\n</script>";
}

function primera_vez($frm)
{
	global $CFG, $db,$ME;

	$rutina = $db->sql_row("SELECT r.*, f.dias FROM mtto.rutinas r LEFT JOIN mtto.frecuencias f ON f.id=r.id_frecuencia WHERE r.id=".$frm["id_rutina"]);	
	$equipos = array();
	if($rutina["activa"]=="t"){
		if($rutina["id_grupo"] != "")
		{
			$idsGrupos=array($rutina["id_grupo"]);
			obtenerIdsGruposAbajo($rutina["id_grupo"],$idsGrupos);
			$strSQL="SELECT e.*
					FROM mtto.equipos e  LEFT JOIN vehiculos v on e.id_vehiculo=v.id 
					WHERE  v.id_estado!=4 AND e.id_centro IS NULL OR e.id_centro IN (SELECT id_centro FROM mtto.rutinas_centros WHERE id_rutina='".$rutina["id"]."') AND e.id_grupo IN (".implode(",",$idsGrupos).")
					ORDER BY e.nombre";
			
			error_log($strSQL);
			$qidEq = $db->sql_query($strSQL);
			while($query = $db->sql_fetchrow($qidEq))
			{
//				$num = $db->sql_row("SELECT count(*) as num FROM mtto.ordenes_trabajo WHERE id_equipo='".$query["id"]."' AND id_rutina='".$rutina["id"]."'");
				$num = $db->sql_row("SELECT count(*) as num FROM mtto.rutinas_primera_vez WHERE id_equipo='".$query["id"]."' AND id_rutina='".$rutina["id"]."'");
				if($num["num"]==0)
					$equipos[$query["id"]]=array("nombre"=>$query["nombre"], "km"=>number_format($query["kilometraje"],2,".",""), "horo"=>number_format($query["horometro"],2,".",""));
			}
		}elseif($rutina["id_equipo"])
		{
			$qidRP = $db->sql_row("SELECT count(*) as num FROM mtto.ordenes_trabajo WHERE id_rutina='".$rutina["id"]."' AND id_equipo='".$rutina["id_equipo"]."'");
			if($qidRP["num"] == 0)
			{
				$qidEq = $db->sql_row("SELECT * FROM mtto.equipos WHERE id_equipo='".$rutina["id_equipo"]."' AND (e.id_centro IS NULL OR e.id_centro IN (SELECT id_centro FROM mtto.rutinas_centros WHERE id_rutina='".$rutina["id"]."') )");
				$equipos[$qidEq["id"]]=array("nombre"=>$qidEq["nombre"], "km"=>number_format($qidEq["kilometraje"],2,".",""), "horo"=>number_format($qidEq["horometro"],2,".",""));
			}
		}   
	}
	
	$newMode = "insertarPrimeraVez";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/rutinas_primera_vez.php");
}


function insertarPrimeraVez($frm)
{
	global $CFG, $db,$ME;
	$eq = array();
	foreach($frm as $key => $value)
	{
		if(preg_match("/fecha_/",$key,$match))
		{
			$id = str_replace("fecha_","",$key);
			if($value != "")
				$eq[$id]["fecha"]=$value;
		}
		if(preg_match("/horometro_/",$key,$match))
		{
			$id = str_replace("horometro_","",$key);
			if($value != "")
				$eq[$id]["horometro"]=$value;
		}
		if(preg_match("/km_/",$key,$match))
		{
			$id = str_replace("km_","",$key);
			if($value != "")
				$eq[$id]["km"]=$value;
		}
	}
	
	include($CFG->modulesdir . "/mtto.ordenes_trabajo.php");
	foreach($eq as $key => $valores)
	{
		$actual = $db->sql_row("SELECT kilometraje as km, horometro as horo, '".date("Y-m-d")."' as fecha FROM mtto.equipos WHERE id=".$key);

		$fechaPlaneada = calcular_fecha_planeada($key, nvl($valores["fecha"]), nvl($valores["horometro"]), nvl($valores["km"]), $actual)." 08:00:00";
		$fecha = $km = $horometro = "null";
		if(isset($valores["fecha"]) && $valores["fecha"]!="") $fecha = "'".$valores["fecha"]."'";
		if(isset($valores["km"]) && $valores["km"]!="") $km = "'".$valores["km"]."'";
		if(isset($valores["horometro"]) && $valores["horometro"]!="") $horometro = "'".$valores["horometro"]."'";
    $idOT = crearOrdenTrabajo($entidad,$frm["id"],$key,$fechaPlaneada,10);
		$consulta = "INSERT INTO mtto.rutinas_primera_vez (id_rutina,id_equipo,km,horometro,fecha,id_orden_trabajo,km_actual,horo_actual,fecha_actual) VALUES (".$frm["id"].",".$key.",".$km.", ".$horometro.", ".$fecha.", '".$idOT."', '".$actual["km"]."', '".$actual["horo"]."','".$actual["fecha"]."')";
		
    $db->sql_query($consulta);
	}
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}


function editar_primera_vez($frm)
{
	global $CFG, $db,$ME;

	$rutina = $db->sql_row("SELECT r.*, f.dias FROM mtto.rutinas r LEFT JOIN mtto.frecuencias f ON f.id=r.id_frecuencia WHERE r.id=".$frm["id_rutina"]);	
	$equipos = array();
	if($rutina["activa"]=="t"){
		if($rutina["id_grupo"] != "")
		{
			$idsGrupos=array($rutina["id_grupo"]);
			obtenerIdsGruposAbajo($rutina["id_grupo"],$idsGrupos);
			//Vamos a probar quitando esta condición:
			//					AND o.id_estado_orden_trabajo=10
			$strSQL="
				SELECT pv.*, e.nombre, e.kilometraje as km_equipo, e.horometro as horo_equipo
				FROM mtto.rutinas_primera_vez pv
					LEFT JOIN mtto.ordenes_trabajo o ON o.id=pv.id_orden_trabajo
					LEFT JOIN mtto.equipos e ON e.id=pv.id_equipo
					LEFT JOIN vehiculos v on e.id_vehiculo=v.id
				WHERE e.id_grupo IN (".implode(",",$idsGrupos).") and v.id_estado!=4 
					AND pv.id_rutina='".$rutina["id"]."'
			";
			
			$qidNPV = $db->sql_query($strSQL);
			while($query = $db->sql_fetchrow($qidNPV))
			{
				$equipos[$query["id"]]=array("dx"=>$query, "nombre"=>$query["nombre"], "km"=>number_format($query["km_equipo"],2,".",""), "horo"=>number_format($query["horo_equipo"],2,".",""));
			}
		}else
		{
//					AND o.id_estado_orden_trabajo=10
			$qidNPV = $db->sql_query("
				SELECT pv.*, e.nombre, e.kilometraje as km_equipo, e.horometro as horo_equipo
				FROM mtto.rutinas_primera_vez pv
					LEFT JOIN mtto.ordenes_trabajo o ON o.id=pv.id_orden_trabajo
					LEFT JOIN mtto.equipos e ON e.id=pv.id_equipo
				WHERE pv.id_equipo='".$rutina["id_equipo"]."' 
					AND pv.id_rutina='".$rutina["id"]."' 
			");
			if($db->sql_numrows($qidNPV) != 0)		
			{
				$queryEq = $db->sql_fetchrow($qidNPV);
				$equipos[$query["id"]]=array("dx"=>$queryEq, "nombre"=>$query["nombre"], "km"=>number_format($query["km_equipo"],2,".",""), "horo"=>number_format($query["horo_equipo"],2,".",""));
			}
		}
	}

	$newMode = "actualizarPrimeraVez";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/editar_rutinas_primera_vez.php");
}

function actualizarPrimeraVez($frm)
{
	global $CFG, $db,$ME;

	$eq = array();
	foreach($frm as $key => $value)
	{
		if(preg_match("/fecha_/",$key,$match))
		{
			$id = str_replace("fecha_","",$key);
			$eq[$id]["fecha"]=trim($value);
		}
		if(preg_match("/horometro_/",$key,$match))
		{
			$id = str_replace("horometro_","",$key);
			$eq[$id]["horometro"]=trim($value);
		}
		if(preg_match("/km_/",$key,$match))
		{
			$id = str_replace("km_","",$key);
			$eq[$id]["km"]=trim($value);
		}
		if(preg_match("/cambiar_/",$key,$match))
		{
			$id = str_replace("cambiar_","",$key);
			$eq[$id]["cambiar"]=1;
		}
	}

	foreach($eq as $key => $valores)
	{
		if(isset($valores["cambiar"]))
		{
			$act = $db->sql_row("SELECT * FROM mtto.rutinas_primera_vez WHERE id=".$key);
			$OT = $db->sql_row("SELECT * FROM mtto.ordenes_trabajo WHERE id='$act[id_orden_trabajo]'");
			$kmyHoroAct = $db->sql_row("SELECT kilometraje as km, horometro as horo, '".date("Y-m-d")."' as fecha FROM mtto.equipos WHERE id=".$act["id_equipo"]);

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
					if(in_array($OT["id_estado_orden_trabajo"],array(2,3,4))){
						//Buscar la última abierta, para cambiarle la fecha:
						if($ultima=$db->sql_row("SELECT * FROM mtto.ordenes_trabajo WHERE id_rutina='$OT[id_rutina]' AND id_equipo='$OT[id_equipo]' AND id_estado_orden_trabajo NOT IN(2,3,4)")){
							$db->sql_query("UPDATE mtto.ordenes_trabajo SET fecha_planeada='".$fechaPlaneada."' WHERE id='".$ultima["id"]."'");
						}
						else{
							$NOT["id_rutina"]=$OT["id_rutina"];
							$NOT["id_equipo"]=$OT["id_equipo"];
							$NOT["id_motivo"]=8;//Preventivo
							$NOT["fecha_planeada"]=$fechaPlaneada;
							$NOT["id_creador"]=$db->sql_field("SELECT id FROM personas WHERE nombre='Aida' AND apellido='Automático'");
							$NOT["id_planeador"]=$_SESSION[$CFG->sesion]["user"]["id"];
							$NOT["id_estado_orden_trabajo"]=10;//Pre-programada
							$db->sql_insert("mtto.ordenes_trabajo",$NOT);
						}
					}
					else{
						$db->sql_query("UPDATE mtto.ordenes_trabajo SET fecha_planeada='".$fechaPlaneada."' WHERE id='".$act["id_orden_trabajo"]."'");
					}
				}	
			}
			
		}
	}

	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}


function doblar_rutina($frm)
{
	global $CFG, $db,$ME;

	duplicar_rutina($frm);
	$url = $CFG->wwwroot."/mtto/templates/listado_rutinas.php?id_grupo=".$frm["id_grupo"] . "&tipo=1";
	echo "<script>
		    window.location.href='".$url."';
	</script>";
}

function listar_talleres($idRutina)
{
	global $CFG, $db,$ME;
	
	$qidR = $db->sql_query("SELECT r.*, p.razon FROM mtto.rutinas_talleres r LEFT JOIN llta.proveedores p ON p.id=r.id_proveedor WHERE r.id_rutina=".$idRutina);

	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/rutinas_talleres_listado.php");
}

function agregar_taller($taller)
{
	global $CFG, $db,$ME;

	$cond = array("true");
	$qidC = $db->sql_query("SELECT id_centro FROM mtto.rutinas_centros WHERE id_rutina=".$taller["id_rutina"]);
	while($queryC = $db->sql_fetchrow($qidC))
	{
		$cond[] = "id IN (SELECT id_proveedor FROM llta.proveedores_centros WHERE id_centro=".$queryC["id_centro"].")";
	}

	$newMode="insertar_taller";
	$db->crear_select("SELECT distinct(id), razon FROM llta.proveedores WHERE ".implode(" AND ",$cond), $proveedores); 
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/rutinas_talleres_form.php");
}

function insertar_taller($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir . "/mtto.rutinas_talleres.php");
	$entidad->loadValues($frm);
	$id=$entidad->insert();

	$valorTiempo = cargarValorTiempoRutina($frm["id_rutina"]);

	echo "<script language=\"JavaScript\" type=\"text/javascript\">window.opener.cargarValorTiempoRutinaUno('".$valorTiempo."');\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function eliminar_taller($id)
{
	global $CFG, $db,$ME;

	$idRutina = $db->sql_row("SELECT id_rutina FROM mtto.rutinas_talleres WHERE id=".$id);

	include($CFG->modulesdir . "/mtto.rutinas_talleres.php");
	$entidad->set("mode","eliminar");
	$entidad->set("id",$id);
	$entidad->delete();

	$valorTiempo = cargarValorTiempoRutina($idRutina["id_rutina"]);
	echo "<script language=\"JavaScript\" type=\"text/javascript\">window.opener.cargarValorTiempoRutinaUno('".$valorTiempo."');\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

include($CFG->dirroot."/templates/footer_popup.php");
?>

