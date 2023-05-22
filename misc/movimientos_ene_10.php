<?
include_once("../application.php");

if(!isset($_SESSION[$CFG->sesion]["user"])){
  $errorMsg="No existe la sesión.";
  error_log($errorMsg);
  die($errorMsg);
}

$mode=nvl($_GET["mode"],nvl($_POST["mode"],""));

switch(nvl($mode)){

	case "agregar_movimientos":
		agregar_movimientos($_GET["esquema"],$_GET["fecha"]);
	break;

	case "agregar_movimiento_unico":
		agregar_movimiento_unico($_GET["id_micro"],$_GET["fecha"]);
	break;

	case "insertar_movimiento_unico":
		insertar_movimiento_unico($_POST);
	break;

	case "editar_movimiento_unico":
		editar_movimiento_unico($_GET["id_movimiento"], $_GET["esquema"]);
	break;

	case "actualizar_movimiento_unico":
		actualizar_movimiento_unico($_POST);
	break;

	case "insertar_movimientos":
		insertar_movimientos($_POST);
	break;

	case "eliminar_movimiento":
		eliminar_movimiento($_POST);
	break;

	case "editar_pesos":
		editar_pesos($_GET["id_movimiento"]);
	break;

	case "actualizar_pesos":
		actualizar_pesos($_POST);
	break;

	case "agregar_peso":
		agregar_peso($_GET);
	break;

	case "eliminar_peso":
		eliminar_peso($_GET);
	break;

	case "editar_combustible":
		editar_combustible($_GET["id_movimiento"]);
	break;

	case "actualizar_combustible":
		actualizar_combustible($_POST);
	break;

  case "listado_micros":
    listado_micros($_GET["esquema"], nvl($_GET["fecha"],date("Y-m-d")));
  break;

	case "desplazamientos_rec":
		desplazmientos("rec",nvl($_GET["fecha"],""),nvl($_GET["id_movimiento"],""));
	break;

	case "desplazamientos_bar":
		desplazmientos("bar",nvl($_GET["fecha"],""),nvl($_GET["id_movimiento"],""));
	break;

	case "insertarDesplazamientoRec":
		insertarDesplazamientoRec($_POST);
	break;

	case "listado_desplazamientos":
		listado_desplazamientos($_GET["id_movimiento"]);
	break;

	case "listadoDesplazamientosDesdeAVL":
		listadoDesplazamientosDesdeAVL($_GET["id_vehiculo"]);
	break;

	case "activar_desplazamiento":
		activar_desplazamiento($_GET["id_desplazamiento"]);
	break;

	case "listado_operarios":
		listado_operarios($_GET["esquema"], $_GET["id_movimiento"]);
	break;

	case "actualizar_datos_desplazamientos_rec":
		actualizar_datos_desplazamientos_rec($_POST);
	break;

	case "actualizar_datos_desplazamientos_rec_get":
		actualizar_datos_desplazamientos_rec($_GET);
	break;

	case "cerrarDesplazamientoOtraHora":
		cerrarDesplazamientoOtraHora($_POST);
	break;

	case "eliminar_desplazamiento_rec":
		eliminar_desplazamiento_rec($_GET["id"]);
	break;

	case "insertarOperariosVarios":
		insertarOperariosVarios($_POST);
	break;

	case "actualizar_datos_operarios":
		actualizar_datos_operarios($_POST);
	break;

	case "actualizar_datos_operarios_get":
		actualizar_datos_operarios($_GET);
	break;

	case "eliminar_operario":
		eliminar_operario($_GET["id"],$_GET["esquema"]);
	break;

	case "cerrar_movimiento_rec_desde_busq":
		cerrar_movimiento_rec_desde_busq($_POST);
	break;

	case "cerrarMovimiento":
		cerrarMovimiento($_GET["esquema"],$_GET["id_movimiento"]);
	break;

	case "cerrarMovimientoConFecha_form":
		cerrarMovimientoConFecha_form($_GET["esquema"],$_GET["id_movimiento"]);
	break;

	case "cerrarMovimientoConOtraFecha":
		cerrarMovimientoConOtraFecha($_POST);
	break;

	case "listado_bolsas_barrido":
		listado_bolsas_barrido($_GET["id_movimiento"]);
	break;

	case "insertarBolsas":
		insertarBolsas($_POST);
	break;

	case "actualizar_datos_bolsas":
		actualizar_datos_bolsas($_POST);
	break;

	case "agregar_desplazamiento_desde_busq":
		agregar_desplazamiento_desde_busq($_POST);
	break;

	case "listHoraBolsasFinales":
		listHoraBolsasFinales($_GET["fecha"]);
	break;

	case "cerrarMovimientoBarDesdeListadoFinal":
		cerrarMovimientoBarDesdeListadoFinal($_POST["id_movimiento"], $_POST["fecha"]);
	break;

	case "actualizar_datos_bolsasDesdeListadoFinal":
		actualizar_datos_bolsasDesdeListadoFinal($_POST);
	break;

	case "listar_apoyos":
		listar_apoyos($_GET);
	break;

	case "agregar_apoyo":
		agregar_apoyo($_GET);
	break;

	case "insertar_apoyo":
		insertar_apoyo($_POST);
	break;

	case "eliminar_apoyo":
		eliminar_apoyo($_GET);
	break;

	default:
		listado_movimientos($_GET["esquema"], nvl($_GET["fecha"],date("Y-m-d")), nvl($_GET["estado"],"abierta"));
	break;


}

function listado_movimientos($schema, $fecha, $estado)
{
	global $CFG, $db,$ME;

	$campoAdicional = $opcionAdicional = "";
	$altDesplazamiento = "Recursos";
	if($schema == "rec")
	{
		$campoAdicional = ", (SELECT rec.tipos_desplazamientos.tipo FROM rec.desplazamientos LEFT JOIN rec.tipos_desplazamientos ON rec.tipos_desplazamientos.id=rec.desplazamientos.id_tipo_desplazamiento WHERE rec.desplazamientos.id_movimiento=mov.id AND rec.desplazamientos.hora_inicio IS NOT NULL ORDER BY rec.desplazamientos.hora_inicio DESC LIMIT 1) as ultimodesp, combustible";	
		$opcionAdicional = "&nbsp;<a href=\'javascript:pesos('||mov.id||')\'><img alt=\'Pesos\' title=\'Pesos\' src=\'".$CFG->wwwroot."/admin/iconos/transparente/balance.gif\' border=\'0\'></a>&nbsp;<a href=\'javascript:combustible('||mov.id||')\'><img alt=\'Combustible\' title=\'Combustible\' src=\'".$CFG->wwwroot."/admin/iconos/transparente/combustible.png\' border=\'0\'></a>&nbsp;"; 
		$altDesplazamiento = "Desplazamiento";
	}

	$condicion = " AND final IS NULL";
	if($estado == "cerrada")
		$condicion = " AND final IS NOT NULL";
	
	$user=$_SESSION[$CFG->sesion]["user"];
	$consulta = "SELECT mov.*, m.codigo, v.codigo as vehiculo, p.nombre||' '||p.apellido as coordinador, t.nombre as tipo_residuo, s.servicio, c.nombre as cuartelillo, case when mov.final IS NULL THEN '<a href=\'javascript:editar_movimiento_unico('||mov.id||')\'><img alt=\'Editar\' title=\'Editar\' src=\'".$CFG->wwwroot."/admin/iconos/transparente/iconoeditar.gif\' border=\'0\'></a>".$opcionAdicional."&nbsp;<a href=\'javascript:cerrar_movimiento('||mov.id||')\'><img alt=\'Cerrar movimiento con fecha actual\' title=\'Cerrar movimiento con fecha actual\' src=\'".$CFG->wwwroot."/admin/iconos/transparente/check_green.png\' border=\'0\'></a>&nbsp;<a href=\'javascript:cerrar_movimiento_con_fecha('||mov.id||')\'><img alt=\'Cerrar Movimiento con otra fecha\' title=\'Cerrar Movimiento con otra fecha\' src=\'".$CFG->wwwroot."/admin/iconos/transparente/icon-activate.gif\' border=\'0\'></a>' else '<a href=\'javascript:editar_movimiento_unico('||mov.id||')\'><img alt=\'Editar\' title=\'Ver\'src=\'".$CFG->wwwroot."/admin/iconos/transparente/iconoeditar.gif\' border=\'0\'></a>' end || '&nbsp;<a href=\'javascript:desplazamiento('||mov.id||')\'><img alt=\'".$altDesplazamiento."\' title=\'".$altDesplazamiento."\' src=\'".$CFG->wwwroot."/admin/iconos/transparente/icon-route.png\' border=\'0\'></a>' as opciones ".$campoAdicional."
		FROM ".$schema.".movimientos mov
		LEFT JOIN vehiculos v ON v.id=mov.id_vehiculo
		LEFT JOIN micros m ON m.id=mov.id_micro
		LEFT JOIN servicios s ON s.id=m.id_servicio
		LEFT JOIN personas p ON p.id=m.id_coordinador
		LEFT JOIN tipos_residuos t ON t.id=m.id_tipo_residuo
		LEFT JOIN cuartelillos c ON c.id=m.id_cuartelillo
		WHERE mov.inicio::date = '".$fecha."' AND m.id_ase IN (SELECT id FROM ases WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]'))".$condicion;
	$data = array();
	$qid = $db->sql_query($consulta);
	while($query = $db->sql_fetchrow($qid))
	{
		if($schema == "rec")
			$data[] = '{id: "'.$query["id"].'", micro: "'.$query["codigo"].'", servicio: "'.$query["servicio"].'", vehiculo: "'.$query["vehiculo"].'", coordinador: "'.$query["coordinador"].'", inicio: "'.$query["inicio"].'", final: "'.$query["final"].'", ultimodesp: "'.$query["ultimodesp"].'", combustible : "'.$query["combustible"].'", opciones : "'.$query["opciones"].'"}';
		else
			$data[] = '{id: "'.$query["id"].'", micro: "'.$query["codigo"].'", cuartelillo: "'.$query["cuartelillo"].'", coordinador: "'.$query["coordinador"].'", tipo_residuo: "'.$query["tipo_residuo"].'", inicio: "'.$query["inicio"].'", final: "'.$query["final"].'", opciones : "'.$query["opciones"].'"}';
	}

	if($schema == "rec")
		$fields = '{key:"id"}, "micro", "servicio", "vehiculo", "coordinador", "inicio", "final", "ultimodesp", "combustible", "opciones"';
	else
		$fields = '{key:"id"}, "micro", "cuartelillo", "coordinador", "tipo_residuo", "inicio", "final", "opciones"';

	if($schema == "rec")
	{
		$myColumnDefs = '{key:"id"}, {key:"micro", label:"Ruta", sortable: true},  {key:"servicio", label:"Servicio", sortable: true}, {key:"vehiculo", label:"Vehículo", sortable: true}, {key:"coordinador", label:"Coordinador", sortable: true},  {key:"inicio", label:"Inicio", sortable: true}, {key:"final", label:"Final", sortable: true}, {key:"ultimodesp", label:"Ult. Desplazamiento"}, {key:"combustible", label:"Combustible", sortable: true}, {key:"opciones", label:"Opciones"}';
		$cellClickEvent = "
		myDataTable.subscribe('cellClickEvent',function(ev) {
				var target = YAHOO.util.Event.getTarget(ev);
				var column = myDataTable.getColumn(target);
				if(column.key != 'opciones')
				{
					var record = this.getRecord(target);
					var cols = this.getColumnSet().keys;
					var primaryKey = '';
					for (var i = 0; i < cols.length; i++) {
						if(cols[i].key==\"id\")
						{
							primaryKey = escape(record.getData(cols[i].key));
						}
					}
					url = '".$CFG->wwwroot."/opera/movimientos.php?mode=listado_desplazamientos&id_movimiento='+primaryKey;
					abrirVentanaJavaScript('listunicadesp','800','300',url);
				}
		});";	
	}
	else
		$myColumnDefs = '{key:"id"}, {key:"micro", label:"Ruta", sortable: true}, {key:"cuartelillo", label:"Cuartelillo", sortable: true}, {key:"coordinador", label:"Coordinador", sortable: true}, {key:"tipo_residuo", label:"Tipo Residuo", sortable: true}, {key:"inicio", label:"Inicio", sortable: true}, {key:"final", label:"Final", sortable: true}, {key:"opciones", label:"Opciones"}';

	$datos = array("data"=>implode(", ",$data),  "myColumnDefs"=>$myColumnDefs, "fields"=>$fields);

	list($anio,$mes,$dia)=split("-",$fecha);
	$ant = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) - 1 * 24 * 60 * 60);
	$sig = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + 1 * 24 * 60 * 60);
	$paginacionAnt = '<a href="'.$CFG->wwwroot.'/opera/movimientos_'.$schema.'.php?mode=listado_movimientos&esquema='.$schema.'&fecha='.$ant.'"><img src="'.$CFG->wwwroot.'/admin/iconos/transparente/flechaizquierda.gif" border=0></a>';
	$paginacionSig = '<a href="'.$CFG->wwwroot.'/opera/movimientos_'.$schema.'.php?mode=listado_movimientos&esquema='.$schema.'&fecha='.$sig.'"><img src="'.$CFG->wwwroot.'/admin/iconos/transparente/flechaderecha.gif" border=0></a>';

	if($schema == "rec")
		$botones = '<input type="button" class="boton_verde" value="Apoyos" class="boton_verde" value="Apoyos" onClick="abrirVentanaJavaScript(\'apoyos\',\'400\',\'400\',\''.$CFG->wwwroot.'/opera/movimientos.php?mode=listar_apoyos&fecha='.$fecha.'\')">&nbsp;&nbsp;&nbsp;<input type="button" class="boton_verde" value="Desplazamientos del Día" onClick="abrirVentanaJavaScript(\'listdesplazamientos\',\'900\',\'500\',\''.$CFG->wwwroot.'/opera/movimientos_'.$schema.'.php?mode=desplazamientos_'.$schema.'&fecha='.$fecha.'\');">&nbsp;&nbsp;&nbsp;';

	if($schema == "bar")
		$botones = '<input type="button" class="boton_verde" value="Actualizar Hora y Bolsas Finales" onClick="abrirVentanaJavaScript(\'listHoraBolsasFinales\',\'900\',\'500\',\''.$CFG->wwwroot.'/opera/movimientos_'.$schema.'.php?mode=listHoraBolsasFinales&fecha='.$fecha.'\');">&nbsp;&nbsp;&nbsp;<input type="button" class="boton_verde" value="Recursos" onClick="abrirVentanaJavaScript(\'listdesplazamientos\',\'900\',\'500\',\''.$CFG->wwwroot.'/opera/movimientos_'.$schema.'.php?mode=desplazamientos_'.$schema.'&fecha='.$fecha.'\');">&nbsp;&nbsp;&nbsp;';

	
	//si le faltan movimientos al día se muestra el botón
	$qidMov = $db->sql_row("SELECT count(m.id) as num
			FROM micros m
			LEFT JOIN servicios s ON s.id = m.id_servicio
			LEFT JOIN ases a ON a.id=m.id_ase
			WHERE s.esquema='".$schema."' AND m.id NOT IN (SELECT id_micro FROM ".$schema.".movimientos WHERE inicio::date='".$fecha."') AND m.fecha_hasta IS NULL AND m.id_ase IN (SELECT id FROM ases WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')) AND m.id IN (SELECT id_micro FROM micros_frecuencia WHERE dia='".strftime("%u",strtotime($fecha))."')");
	if($qidMov["num"]!=0)
		$botones .='<input type="button" class="boton_verde" value="Insertar los Movimientos del Día" onClick="abrirVentanaJavaScript(\'movimientos\',\'900\',\'500\',\''.$CFG->wwwroot.'/opera/movimientos_'.$schema.'.php?mode=agregar_movimientos&fecha='.$fecha.'&esquema='.$schema.'\');">&nbsp;&nbsp;&nbsp;';

	$botones.= '<input type="button" class="boton_verde" value="Ir a Fecha" onClick="abrirCalendarioConModo(\'opera/movimientos_'.$schema.'\',\'listado_movimientos\',\''.$schema.'\')">&nbsp;';

	$titulo = "MOVIMIENTOS DEL DÍA ".strtoupper(strftime("%A %d de %B de %Y",strtotime($fecha)));

	if($user["nivel_acceso"]!=1)
	{
		//si es control solo puede ver dos dias atrás
		$dias = restarFechas(date("Y-m-d"),$fecha);
		if($dias > 2)
			$paginacionAnt = "&nbsp;";
	}

	include($CFG->dirroot."/opera/templates/listado.php");
}


function listado_micros($schema, $fecha)
{
  global $CFG, $db,$ME;

	$user=$_SESSION[$CFG->sesion]["user"];

	$consulta = "SELECT m.id, m.codigo, v.codigo as vehiculo, r.nombre as tipo_residuo, s.servicio, s.esquema, c.nombre as cuartelillo, p.nombre||' '||p.apellido as coordinador
			FROM micros m
			LEFT JOIN vehiculos v  ON v.id=m.id_vehiculo
			LEFT JOIN tipos_residuos r ON r.id=m.id_tipo_residuo
			LEFT JOIN servicios s ON s.id = m.id_servicio
			LEFT JOIN cuartelillos c ON c.id = m.id_cuartelillo
			LEFT JOIN personas p ON p.id = m.id_coordinador
			WHERE s.esquema='".$schema."' AND m.fecha_hasta IS NULL AND m.id_ase IN (SELECT id FROM ases WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')) AND m.id IN (SELECT id_micro FROM micros_frecuencia WHERE dia='".strftime("%u",strtotime($fecha))."')
			ORDER BY m.codigo";
	$data = array();
	$qid = $db->sql_query($consulta);
	while($query = $db->sql_fetchrow($qid))
	{
		$linea = '{id: "'.$query["id"].'", codigo: "'.$query["codigo"].'", vehiculo: "'.$query["vehiculo"].'", tipo_residuo: "'.$query["tipo_residuo"].'", servicio: "'.$query["servicio"].'", cuartelillo: "'.$query["cuartelillo"].'", coordinador: "'.$query["coordinador"].'"';

		$qidMov = $db->sql_query("SELECT id FROM ".$query["esquema"].".movimientos WHERE inicio::date = '".$fecha."' AND id_micro='".$query["id"]."'");
		if($db->sql_numrows($qidMov) == 0)
			$opciones = ", opciones : \"<a href=\'javascript:agregar_movimiento_unico(".$query["id"].")\'><img alt=\'Agregar\' src=\'".$CFG->wwwroot."/admin/iconos/transparente/icon-add.gif\' border=\'0\'></a>\"";
		else
		{
			$mov = $db->sql_fetchrow($qidMov);
			$opciones = ", opciones : \"<a href=\'javascript:editar_movimiento_unico(".$mov["id"].")\'><img alt=\'Editar\' src=\'".$CFG->wwwroot."/admin/iconos/transparente/iconoeditar.gif\' border=\'0\'></a>\"";
		}
		$linea .= $opciones;
		
		$linea .= '}';
		$data[] = $linea;
	}



	if($schema == "rec")
	{
		$fields = '{key:"id"}, "codigo", "vehiculo", "tipo_residuo", "servicio","opciones"';
		$myColumnDefs = '{key:"id"}, {key:"codigo", label:"Código", sortable: true}, {key:"vehiculo", label:"Vehículo", sortable: true}, {key:"tipo_residuo", label:"Tipo de Residuo", sortable: true}, {key:"servicio", label:"Servicio", sortable: true}, {key:"opciones", label:"Movimiento"}';
	}elseif($schema == "bar")
	{
		$fields = '{key:"id"}, "codigo", "tipo_residuo", "servicio", "cuartelillo", "coordinador","opciones"';
		$myColumnDefs = '{key:"id"}, {key:"codigo", label:"Código", sortable: true}, {key:"tipo_residuo", label:"Tipo de Residuo", sortable: true}, {key:"servicio", label:"Servicio", sortable: true}, {key:"cuartelillo", label:"Cuartelillo", sortable: true}, {key:"coordinador", label:"Coordinador", sortable: true}, {key:"opciones", label:"Movimiento"}';
	}



	$datos = array("data"=>implode(", ",$data),  "myColumnDefs"=>$myColumnDefs, "fields"=>$fields);
	list($anio,$mes,$dia)=split("-",$fecha);
	$ant = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) - 1 * 24 * 60 * 60);
	$sig = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + 1 * 24 * 60 * 60);
	$paginacionAnt = '<a href="'.$CFG->wwwroot.'/opera/movimientos_'.$schema.'.php?mode=listado_micros&esquema='.$schema.'&fecha='.$ant.'"><img src="'.$CFG->wwwroot.'/admin/iconos/transparente/flechaizquierda.gif" border=0></a>';
	$paginacionSig = '<a href="'.$CFG->wwwroot.'/opera/movimientos_'.$schema.'.php?mode=listado_micros&esquema='.$schema.'&fecha='.$sig.'"><img src="'.$CFG->wwwroot.'/admin/iconos/transparente/flechaderecha.gif" border=0></a>';
	$botones = '<input type="button" class="boton_verde" value="Insertar los Movimientos del Día" onClick="abrirVentanaJavaScript(\'movimientos\',\'900\',\'500\',\''.$CFG->wwwroot.'/opera/movimientos_'.$schema.'.php?mode=agregar_movimientos&fecha='.$fecha.'&esquema='.$schema.'\');">&nbsp;&nbsp;&nbsp;<input type="button" class="boton_verde" value="Ir a Fecha" onClick="abrirCalendarioConModo(\'opera/movimientos_'.$schema.'\',\'listado_micros\',\''.$schema.'\')">&nbsp;';
		
	$titulo = "RUTAS DEL DÍA ".strtoupper(strftime("%A %d de %B de %Y",strtotime($fecha)));
	include($CFG->dirroot."/opera/templates/listado.php");
}


function agregar_movimiento_unico($idMicro,$fecha)
{
	global $db, $CFG, $ME;

	$micro = $db->sql_row("SELECT m.id, s.esquema, m.id_vehiculo, a.id_centro, s.id as id_servicio FROM micros m LEFT JOIN servicios s ON s.id = m.id_servicio LEFT JOIN ases a ON a.id=m.id_ase WHERE m.id=".$idMicro);
	$mov = array("id_micro"=>$idMicro, "inicio"=>$fecha, "esquema"=>$micro["esquema"]);

	$db->crear_select("SELECT v.id, v.codigo || '/' || v.placa || CASE WHEN (select count(o.id) FROM mtto.ordenes_trabajo o WHERE o.id_equipo=e.id AND o.fecha_planeada::date = '".$fecha."') != 0 then '(Mantenimiento Programado)' else '' end as nombre
			FROM vehiculos v
			LEFT JOIN mtto.equipos e ON v.id=e.id_vehiculo 
			LEFT JOIN tipos_vehiculos_servicios tp ON tp.id_tipo_vehiculo=v.id_tipo_vehiculo
			WHERE v.id_centro = '".$micro["id_centro"]."' AND tp.id_servicio='".$micro["id_servicio"]."'
			ORDER BY v.codigo,v.placa",$vehiculos,$micro["id_vehiculo"]);

	$newMode="insertar_movimiento_unico";
	$titulo = "INSERTAR";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/opera/templates/movimiento_unico_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function insertar_movimiento_unico($frm)
{
	global $db,$CFG,$ME;

	include($CFG->modulesdir."/".$frm["esquema"].".movimientos.php");
	$frm["inicio"] = $frm["inicio"]." ".$frm["hora"];
	$entidad->loadValues($frm);
	$id=$entidad->insert();
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}


function editar_movimiento_unico($idMov, $esquema)
{
	global $db, $CFG, $ME;

	$mov = $db->sql_row("SELECT mov.*, to_char(mov.inicio,'YYYY-MM-DD') as inicio, to_char(mov.inicio,'HH24:MI:SS') as hora, a.id_centro, '".$esquema."' as esquema, s.id as id_servicio FROM ".$esquema.".movimientos mov LEFT JOIN micros m ON m.id=mov.id_micro LEFT JOIN ases a ON a.id=m.id_ase LEFT JOIN servicios s ON s.id = m.id_servicio WHERE mov.id=".$idMov);
	$db->crear_select("SELECT v.id, v.codigo || '/' || v.placa || CASE WHEN (select count(o.id) FROM mtto.ordenes_trabajo o WHERE o.id_equipo=e.id AND o.fecha_planeada::date = '".$mov["inicio"]."') != 0 then '(Mantenimiento Programado)' else '' end as nombre
			FROM vehiculos v
			LEFT JOIN mtto.equipos e ON v.id=e.id_vehiculo
			LEFT JOIN tipos_vehiculos_servicios tp ON tp.id_tipo_vehiculo=v.id_tipo_vehiculo
			WHERE v.id_centro = '".$mov["id_centro"]."' AND tp.id_servicio='".$mov["id_servicio"]."' ORDER BY v.codigo,v.placa",$vehiculos,$mov["id_vehiculo"]);

	$newMode="actualizar_movimiento_unico";
	$titulo = "EDITAR";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/opera/templates/movimiento_unico_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}


function actualizar_movimiento_unico($frm)
{
	global $db, $CFG, $ME;

	include($CFG->modulesdir . "/".$frm["esquema"].".movimientos.php");
	$frm["inicio"] = $frm["inicio"]." ".$frm["hora"];
	$entidad->loadValues($frm);
	$entidad->set("mode","update");
	$entidad->update();
	if($frm["final"]!= "")
	{
		$db->sql_query("UPDATE ".$frm["esquema"].".movimientos SET final='".$frm["final"]."' WHERE id=".$frm["id"]);
		$db->sql_query("UPDATE ".$frm["esquema"].".movimientos_personas SET hora_fin='".$frm["final"]."' WHERE id_movimiento='".$frm["id"]."' AND hora_fin IS NULL");
		if($frm["esquema"] == "rec")
			$db->sql_query("UPDATE rec.desplazamientos SET hora_fin='".$frm["final"]."' WHERE id_movimiento='".$frm["id"]."' AND hora_fin IS NULL");

		$movNew = $db->sql_row("SELECT id_vehiculo rec.movimientos WHERE id=".$frm["id"]);
		if($mov["id_vehiculo"] != "")
		{
			$kmHoro = $db->sql_row("SELECT kilometraje as km, horometro as horo FROM vehiculos WHERE id=".$movNew["id_vehiculo"]);
			if($kmHoro["km"] != "")
				$db->sql_query("UPDATE ".$frm["esquema"].".movimientos SET km_final='".$kmHoro["km"]."' WHERE id=".$frm["id"]);
			if($kmHoro["horo"] != "")
				$db->sql_query("UPDATE ".$frm["esquema"].".movimientos SET horometro_final='".$kmHoro["horo"]."' WHERE id=".$frm["id"]);
		}
	}	
	
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}


function agregar_movimientos($esquema,$fecha)
{
	global $db, $CFG, $ME;

	$user=$_SESSION[$CFG->sesion]["user"];

	$consulta = "SELECT m.id, m.codigo, m.id_vehiculo, r.nombre as tipo_residuo, s.servicio, s.esquema, a.id_centro, s.id as id_servicio, m.id_lugar_descargue
			FROM micros m
			LEFT JOIN tipos_residuos r ON r.id=m.id_tipo_residuo
			LEFT JOIN servicios s ON s.id = m.id_servicio
			LEFT JOIN ases a ON a.id=m.id_ase
			WHERE s.esquema='".$esquema."' AND m.id NOT IN (SELECT id_micro FROM ".$esquema.".movimientos WHERE inicio::date='".$fecha."') AND m.fecha_hasta IS NULL AND m.id_ase IN (SELECT id FROM ases WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')) AND m.id IN (SELECT id_micro FROM micros_frecuencia WHERE dia='".strftime("%u",strtotime($fecha))."') 
			ORDER BY m.codigo";
	$qid = $db->sql_query($consulta);

	$newMode="insertar_movimientos";
	$titulo = "INSERTAR";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/opera/templates/movimientos_varios_".$esquema."_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}


function insertar_movimientos($frm)
{
	global $db, $CFG, $ME;

	$dx=array();
	foreach($frm as $key => $value)
	{
		if(preg_match("/micro_/",$key,$match))
		{
			$i = str_replace("micro_","",$key);
			$dx[$i]["id_micro"]=$value;
		}

		if(preg_match("/inicio_/",$key,$match))
		{
			$i = str_replace("inicio_","",$key);
			$dx[$i]["inicio"]=$value;
		}

		if(preg_match("/hora_/",$key,$match))
		{
			$i = str_replace("hora_","",$key);
			if($value != "")
				$dx[$i]["hora"]=$value;
		}

		if(preg_match("/vehiculo_/",$key,$match))
		{
			$i = str_replace("vehiculo_","",$key);
			$dx[$i]["id_vehiculo"]=$value;
		}

		if(preg_match("/id_lugar_descargue_/",$key,$match))
		{
			$i = str_replace("id_lugar_descargue_","",$key);
			$dx[$i]["id_lugar_descargue"]=$value;
		}
	
		if(preg_match("/peso_inicial_/",$key,$match))
		{
			$i = str_replace("peso_inicial_","",$key);
			$dx[$i]["peso_inicial"]=$value;
		}

		//solo se utiliza en barrido
		if(preg_match("/persona_/",$key,$match))
		{
			$i = str_replace("persona_","",$key);
			$dx[$i]["id_persona"]=$value;
		}
		if(preg_match("/bolsa_/",$key,$match))
		{
			if($value != "")
			{
				$vals = explode("_",$key);
				$dx[$vals[2]]["bolsa"][$vals[1]] = $value;
			}
		}
	}

	include($CFG->modulesdir."/".$frm["esquema"].".movimientos.php");
	foreach($dx as $valores)
	{
		$valores["inicio"] = $valores["inicio"]." ".$valores["hora"];
		$entidad->loadValues($valores);
		$idMovimiento=$entidad->insert();

		if($frm["esquema"]=="rec")
		{
			if($valores["id_lugar_descargue"] != "%" || $valores["peso_inicial"] != "")
			{
				$idLugDes = $peso = "null";
				if($valores["id_lugar_descargue"] != "%")
					$idLugDes = "'".$valores["id_lugar_descargue"]."'";
				if($valores["peso_inicial"] != "")
					$peso = "'".$valores["peso_inicial"]."'";

				$db->sql_query("INSERT INTO rec.movimientos_pesos (id_movimiento, peso_inicial, id_lugar_descargue) VALUES ('".$idMovimiento."', ".$peso.", ".$idLugDes.")");
			}
		}

		if($frm["esquema"] == "bar")
		{
			$db->sql_query("INSERT INTO bar.movimientos_personas (id_movimiento,id_persona,hora_inicio) VALUES ('".$idMovimiento."', '".$valores["id_persona"]."', '".$valores["inicio"]."')");
			if(isset($valores["bolsa"]))
			{
				foreach($valores["bolsa"] as $idTipo => $num)
				{
					$db->sql_query("INSERT INTO bar.movimientos_bolsas (id_movimiento, id_tipo_bolsa, numero_inicio) VALUES ('".$idMovimiento."', '".$idTipo."', '".$num."')");
				}
			}
		}
	}

	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}


function eliminar_movimiento($frm)
{
	global $db, $CFG, $ME;

	$qid=$db->sql_query("DELETE FROM ".$frm["esquema"].".movimientos WHERE id='$frm[id]'");
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function editar_pesos($idMov)
{
	global $db, $CFG, $ME;

	$data = $tipos = array();
	$movimientoAbierto=true;
	$idCentro = 0;
	$mov = $db->sql_row("SELECT c.codigo as ruta, v.codigo, mov.inicio, mov.final , a.id_centro
			FROM rec.movimientos mov
			LEFT JOIN vehiculos v ON v.id=mov.id_vehiculo
			LEFT JOIN micros c ON c.id=mov.id_micro
			LEFT JOIN ases a ON a.id=c.id_ase
			WHERE mov.id=".$idMov);

	$titulo = '<span class="azul_12">RUTA '.$mov["ruta"].'</span> / Vehículo: '.$mov["codigo"].' / Fecha Inicial: '.$mov["inicio"].' / Fecha Final: '.$mov["final"];
	$idCentro = $mov["id_centro"];
	if($mov["final"] != "")
			$movimientoAbierto=false;

	$qid = $db->sql_query("SELECT p.*, ld.nombre as lugar, case when mov.final is null then '<a href=\'javascript:eliminar('||p.id||')\'><img alt=\'Eliminar\' title=\'Eliminar\' src=\'".$CFG->wwwroot."/admin/iconos/transparente/trash-x.png\' border=\'0\'></a>' else '' end as opciones
			FROM rec.movimientos_pesos p
			LEFT JOIN lugares_descargue ld ON ld.id=p.id_lugar_descargue
			LEFT JOIN rec.movimientos mov ON mov.id=p.id_movimiento
			WHERE p.id_movimiento=".$idMov);
	while($query = $db->sql_fetchrow($qid))
	{
		$data[] = '{id: "'.$query["id"].'", peso_inicial: "'.$query["peso_inicial"].'", peso_final: "'.$query["peso_final"].'", peso_total: "'.$query["peso_final"].'", id_lugar_descargue: "'.$query["lugar"].'", opciones : "'.$query["opciones"].'", tiquete_entrada: "'.$query["tiquete_entrada"].'", tiquete_salida: "'.$query["tiquete_salida"].'"}';
	}

	$qidT = $db->sql_query("SELECT * FROM lugares_descargue WHERE id_centro='".$idCentro."' ORDER BY nombre");
	while($query = $db->sql_fetchrow($qidT))
	{
	  $tipos[] = '"'.str_replace('"',"",$query["nombre"]).'"';
	}

	include($CFG->dirroot."/templates/header_popup_tabview.php");
	include($CFG->dirroot."/opera/templates/listado_pesos_movimiento.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function actualizar_pesos($frm)
{
	global $db, $CFG, $ME;

	//si está cerrado ell  movimiento no se actualiza nadita
	$des = $db->sql_row("SELECT final FROM rec.movimientos d LEFT JOIN rec.movimientos_pesos m ON m.id_movimiento = d.id WHERE m.id=".$frm["id"]);
	if($des["final"] == "")
	{
		if($frm["campo"]=="id_lugar_descargue")
		{
			$tipo = $db->sql_row("SELECT id FROM lugares_descargue WHERE nombre = '".$frm["newValue"]."'");
			$frm["newValue"] = $tipo["id"];
		}
	
		$db->sql_query("UPDATE rec.movimientos_pesos SET ".$frm["campo"]."='".$frm["newValue"]."' WHERE id=".$frm["id"]);
	}
	
	return "ok";
}

function agregar_peso($frm)
{
	global $db, $CFG, $ME;

	$idLugarDes = $db->sql_row("SELECT m.id_lugar_descargue, m.id FROM micros m LEFT JOIN rec.movimientos mov ON m.id=mov.id_micro WHERE mov.id=".$frm["id_movimiento"]);
	if($idLugarDes["id_lugar_descargue"] != "")
		$frm["id_lugar_descargue"] = $idLugarDes["id_lugar_descargue"];

	include($CFG->modulesdir . "/rec.movimientos_pesos.php");
	$entidad->loadValues($frm);
	$entidad->insert();

	echo "<script>window.location.href='".$CFG->wwwroot."/opera/movimientos.php?mode=editar_pesos&id_movimiento=".$frm["id_movimiento"]."';</script>";
}

function eliminar_peso($frm)
{
	global $db, $CFG, $ME;

	$db->sql_query("DELETE FROM rec.movimientos_pesos WHERE id=".$frm["id"]);
	echo "<script>window.location.href='".$CFG->wwwroot."/opera/movimientos.php?mode=editar_pesos&id_movimiento=".$frm["id_movimiento"]."';</script>";
}

function editar_combustible($idMov)
{
	global $db, $CFG, $ME;

	$mov = $db->sql_row("SELECT * FROM rec.movimientos WHERE id=".$idMov);
	$newMode = "actualizar_combustible";
	$titulo = "ACTUALIZAR";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/opera/templates/combustible_form.php");
}

function actualizar_combustible($frm)
{
	global $db, $CFG, $ME;

	$comb = "null";
	if($frm["combustible"] != "")
		$comb = "'".$frm["combustible"]."'";

	$db->sql_query("UPDATE rec.movimientos SET combustible=".$comb." WHERE id=".$frm["id"]);
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.close();\n</script>";
}


function desplazmientos($squema,$fecha,$idMovimiento)
{
	global $db, $CFG, $ME;

	$user=$_SESSION[$CFG->sesion]["user"];

	if($fecha != "")
		$cond = "v.inicio::date = '".$fecha."' AND a.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')";
	elseif($idMovimiento != "")
		$cond = "v.id=".$idMovimiento;

	$consulta = "SELECT v.*, m.codigo, a.id_centro 
			FROM ".$squema.".movimientos v 
			LEFT JOIN micros m ON m.id=v.id_micro 
			LEFT JOIN ases a ON a.id=m.id_ase 
			WHERE $cond
			ORDER BY codigo, v.inicio";

	$qid = $db->sql_query($consulta);
	include($CFG->dirroot."/templates/header_popup.php");
	if($squema == "rec")
		include($CFG->dirroot."/opera/templates/desplazamientos_dia.php");
	else
		include($CFG->dirroot."/opera/templates/desplazamientos_barrido_dia.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function insertarDesplazamientoRec($frm)
{
	global $db, $CFG, $ME;

	include($CFG->modulesdir."/rec.desplazamientos.php");
	$entidad->loadValues($frm);
	$id=$entidad->insert();
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function activar_desplazamiento($idDespla)
{
	global $db, $CFG, $ME;

	$qid = $db->sql_row("SELECT v.kilometraje, v.horometro,m.id as id_movimiento
			FROM rec.desplazamientos d
			LEFT JOIN rec.movimientos m ON d.id_movimiento=m.id
			LEFT JOIN vehiculos v ON m.id_vehiculo=v.id
			WHERE d.id=".$idDespla);
	$km = $horo= "null";
	if($qid["kilometraje"] != "")
		$km = "'".$qid["kilometraje"]."'";
	if($qid["horometro"] != "")
		$horo = "'".$qid["horometro"]."'";

	$db->sql_query("UPDATE rec.desplazamientos SET hora_inicio='".date("Y-m-d H:i:s")."', km=".$km.", horometro=".$horo." WHERE id=".$idDespla);
	echo "<script>window.location.href='".$CFG->wwwroot."/opera/movimientos.php?mode=listado_desplazamientos&id_movimiento=".$qid["id_movimiento"]."';</script>";
}

function listado_desplazamientos($idMovimento)
{
	global $db, $CFG, $ME;

	$movimientoCerrado = false;
	$cerrado = $db->sql_row("SELECT final FROM rec.movimientos WHERE id=".$idMovimento);
	if($cerrado["final"] != "")
		$movimientoCerrado = true;

	$data = $tipos = array();
	$qidDes = $db->sql_query("SELECT d.id, t.tipo, to_char(hora_inicio,'YYYY-MM-DD HH24:MI:SS') as hora_inicio, to_char(hora_fin,'YYYY-MM-DD HH24:MI:SS') as hora_fin, numero_viaje, km, horometro, case when hora_inicio IS NULL THEN '<a href=\'javascript:activar('||d.id||')\'><img alt=\'Activar\' title=\'Activar\' src=\'".$CFG->wwwroot."/admin/iconos/transparente/estrella2.png\' border=\'0\'></a>&nbsp;&nbsp;' else '' END || case when hora_fin IS NULL and hora_inicio IS NOT NULL then '<a href=\'javascript:cerrar('||d.id||')\'><img alt=\'Cerrar Con Hora Actual\' title=\'Cerrar Con Hora Actual\' src=\'".$CFG->wwwroot."/admin/iconos/transparente/check_green.png\' border=\'0\'></a>&nbsp;&nbsp;<a href=\'javascript:cerrarOtraHora('||d.id||')\'><img alt=\'Cerrar Con Otra Hora\' title=\'Cerrar Con Otra Hora\' src=\'".$CFG->wwwroot."/admin/iconos/transparente/icon-activate.gif\' border=\'0\'></a>&nbsp;&nbsp;<a href=\'javascript:eliminar('||d.id||')\'><img alt=\'Eliminar\' title=\'Eliminar\' src=\'".$CFG->wwwroot."/admin/iconos/transparente/trash-x.png\' border=\'0\'></a>' else '<a href=\'javascript:eliminar('||d.id||')\'><img alt=\'Eliminar\' title=\'Eliminar\' src=\'".$CFG->wwwroot."/admin/iconos/transparente/trash-x.png\' border=\'0\'></a>' end as opciones
			FROM rec.desplazamientos d 
			LEFT JOIN rec.tipos_desplazamientos t ON d.id_tipo_desplazamiento=t.id
			WHERE id_movimiento='".$idMovimento."' 
			ORDER BY hora_inicio,id");
	while($des = $db->sql_fetchrow($qidDes))
	{
		if($movimientoCerrado)
			$des["opciones"] = "";
		
		$data[] = '{id: "'.$des["id"].'", tipo: "'.$des["tipo"].'", hora_inicio: "'.$des["hora_inicio"].'", hora_fin: "'.$des["hora_fin"].'", numero_viaje: "'.$des["numero_viaje"].'", km: "'.$des["km"].'", horometro: "'.$des["horometro"].'", opciones: "'.$des["opciones"].'"}';
	}

	$qidT = $db->sql_query("SELECT * FROM rec.tipos_desplazamientos ORDER BY tipo");
	while($query = $db->sql_fetchrow($qidT))
	{
		$tipos[] = '"'.$query["tipo"].'"';
	}

	include($CFG->dirroot."/templates/header_popup_tabview.php"); 
	include($CFG->dirroot."/opera/templates/listado_desplazamientos.php"); 
	include($CFG->dirroot."/templates/footer_popup.php");
}

function listadoDesplazamientosDesdeAVL($idVehiculo)
{
	global $db, $CFG, $ME;

	$mov = $db->sql_row("SELECT m.id FROM rec.movimientos m WHERE m.id_vehiculo = '".$idVehiculo."' AND inicio::date = '".date("Y-m-d")."'");
	if(isset($mov["id"]))
		echo "<script>window.location.href='".$CFG->wwwroot."/opera/movimientos.php?mode=listado_desplazamientos&id_movimiento=".$mov["id"]."';</script>";
	else
	{
		echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.outerHeight=200;\nwindow.outerWidth=300;\n</script>\n";
		echo "No existe operación para el vehículo en el día de hoy.<br><br>\n";
		echo "<input type=\"button\" onClick=\"window.close();\" value=\"Cerrar\">";
		die();
	}
}


function actualizar_datos_desplazamientos_rec($frm)
{
	global $db,$CFG,$ME;

	//si está cerrado el desplazamiento no se actualiza nadita
	$des = $db->sql_row("SELECT hora_fin FROM rec.desplazamientos WHERE id=".$frm["id"]);
	if($des["hora_fin"] == "")
	{
		if($frm["campo"]=="id_tipo_desplazamiento")
		{
			$tipo = $db->sql_row("SELECT id FROM rec.tipos_desplazamientos WHERE tipo = '".$frm["newValue"]."'");
			$frm["newValue"] = $tipo["id"];
		}
		if($frm["campo"]=="hora_fin")
			$frm["newValue"] = date("Y-m-d H:i:s");

		$db->sql_query("UPDATE rec.desplazamientos SET ".$frm["campo"]."='".$frm["newValue"]."' WHERE id=".$frm["id"]);
	}
	
	if(isset($frm["reload"]))
		echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";

	return "ok";
}

function eliminar_desplazamiento_rec($idDesp)
{
	global $db,$CFG,$ME;

	$db->sql_query("DELETE FROM rec.desplazamientos WHERE id=".$idDesp);
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function cerrarDesplazamientoOtraHora($frm)
{
	global $db,$CFG,$ME;

	$db->sql_query("UPDATE rec.desplazamientos SET hora_fin='".$frm["hora_fin"]."' WHERE id=".$frm["id_desplazamiento"]);
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}



function listado_operarios($esquema, $idMovimiento)
{
	global $db,$CFG,$ME;

	$movimientoCerrado = false;
	$cerrado = $db->sql_row("SELECT final FROM ".$esquema.".movimientos WHERE id=".$idMovimiento);
	if($cerrado["final"] != "")
		$movimientoCerrado = true;

	$data = array();
	$cons= "SELECT m.id, p.nombre||' '||p.apellido as persona, c.nombre as cargo, to_char(hora_inicio,'YYYY-MM-DD HH24:MI:SS') as hora_inicio, to_char(hora_fin,'YYYY-MM-DD HH24:MI:SS') as hora_fin, case when hora_fin IS NULL then '<a href=\'javascript:cerrar('||m.id||')\'><img alt=\'Cerrar\' src=\'".$CFG->wwwroot."/admin/iconos/transparente/check_green.png\' border=\'0\'></a>&nbsp;&nbsp;<a href=\'javascript:eliminar('||m.id||')\'><img alt=\'Eliminar\' src=\'".$CFG->wwwroot."/admin/iconos/transparente/trash-x.png\' border=\'0\'></a>' else '<a href=\'javascript:eliminar('||m.id||')\'><img alt=\'Eliminar\' src=\'".$CFG->wwwroot."/admin/iconos/transparente/trash-x.png\' border=\'0\'></a>' end as opciones
		FROM ".$esquema.".movimientos_personas m
 		LEFT JOIN personas p ON p.id=m.id_persona
		LEFT JOIN cargos c ON c.id=p.id_cargo
	 	WHERE m.id_movimiento=".$idMovimiento."
		ORDER BY hora_inicio";
	$qid = $db->sql_query($cons);
	while($mov = $db->sql_fetchrow($qid))
	{
		if($movimientoCerrado)
			$mov["opciones"]="";
	
		if($esquema == "rec")	
			$data[] = '{id: "'.$mov["id"].'", persona: "'.$mov["persona"].'", cargo: "'.$mov["cargo"].'", hora_inicio: "'.$mov["hora_inicio"].'", hora_fin: "'.$mov["hora_fin"].'", opciones: "'.$mov["opciones"].'"}';
		else
			$data[] = '{id: "'.$mov["id"].'", persona: "'.$mov["persona"].'", hora_inicio: "'.$mov["hora_inicio"].'", hora_fin: "'.$mov["hora_fin"].'", opciones: "'.$mov["opciones"].'"}';
	}

	include($CFG->dirroot."/templates/header_popup_tabview.php"); 
	include($CFG->dirroot."/opera/templates/listado_operarios.php"); 
	include($CFG->dirroot."/templates/footer_popup.php");
}

function insertarOperariosVarios($frm)
{
	global $db,$CFG,$ME;


	if($frm["esquema"] == "rec")
	{
		$personas = array("cond" => array("cargo"=>21,"id_movimiento"=>$frm["id_movimiento"]), "ayu1" => array("cargo"=>22,"id_movimiento"=>$frm["id_movimiento"]), "ayu2" => array("cargo"=>22,"id_movimiento"=>$frm["id_movimiento"]));
		foreach($frm as $key => $value)
		{
			if(preg_match("/conductor_/",$key,$match))
			{
				$at = str_replace("conductor_","",$key);
				$personas["cond"][$at]=$value;
			}

			if(preg_match("/ayudante1_/",$key,$match))
			{
				$at = str_replace("ayudante1_","",$key);
				$personas["ayu1"][$at]=$value;
			}

			if(preg_match("/ayudante2_/",$key,$match))
			{
				$at = str_replace("ayudante2_","",$key);
				$personas["ayu2"][$at]=$value;
			}
		}
	}elseif($frm["esquema"] == "bar")
		$personas = array(0 => $frm);	

	include($CFG->modulesdir."/".$frm["esquema"].".movimientos_personas.php");
	foreach($personas as $dx)
	{
		if($dx["id_persona"] != "%")
		{
			$entidad->loadValues($dx);
			$id=$entidad->insert();
		}
	}

	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function actualizar_datos_operarios($frm)
{
	global $db,$CFG,$ME;

	$db->sql_query("UPDATE ".$frm["esquema"].".movimientos_personas SET ".$frm["campo"]."='".$frm["newValue"]."' WHERE id=".$frm["id"]);
	
	if(isset($frm["reload"]))
		echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";

	return "ok";
}

function eliminar_operario($id,$esquema)
{
	global $db,$CFG,$ME;

	$db->sql_query("DELETE FROM ".$esquema.".movimientos_personas WHERE id=".$id);
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function cerrar_movimiento_rec_desde_busq($frm)
{
	global $db,$CFG,$ME;

	$mov = $db->sql_row("SELECT count(id) as num FROM rec.movimientos WHERE inicio::date = '".$frm["fecha"]."' AND inicio < now() AND final IS NULL AND id_vehiculo=".$frm["id_vehiculo"]);
	if($mov["num"] == 1)
	{
		$cerraMov = $db->sql_row("SELECT id FROM rec.movimientos WHERE inicio::date = '".$frm["fecha"]."' AND inicio < now() AND final IS NULL AND id_vehiculo=".$frm["id_vehiculo"]);
		cerrarMovimiento("rec",$cerraMov["id"]);
	}elseif($mov["num"] == 0)
		echo "<script>window.location.href='".$CFG->wwwroot."/opera/templates/cerrar_movimiento_rec_form.php?fecha=".$frm["fecha"]."&id_vehiculo=".$frm["id_vehiculo"]."&error';</script>";
	else
	{
		$mov = $db->sql_query("SELECT mov.*, m.codigo, v.codigo as vehiculo 
				FROM rec.movimientos mov 
				LEFT JOIN vehiculos v ON v.id=mov.id_vehiculo
				LEFT JOIN micros m ON m.id=mov.id_micro
				WHERE mov.inicio::date = '".$frm["fecha"]."' AND mov.inicio < now() AND mov.final IS NULL AND mov.id_vehiculo=".$frm["id_vehiculo"]);
		
		$texto = "
			<table width=\"100%\">
			<tr><td align=\"center\" height=\"40\" class=\"azul_12\">Se encontraron varios resultados:</td></tr>
			</table>
			<table width=\"100%\" class=\"tabla_sencilla\">
			<tr><td class=\"tabla_sencilla_td\"><span class=\"azul_12\">RUTA</span></td><td class=\"tabla_sencilla_td\"><span class=\"azul_12\">VEHÍCULO</span></td><td class=\"tabla_sencilla_td\"><span class=\"azul_12\">INICIO</span></td><td class=\"tabla_sencilla_td\"><span class=\"azul_12\">OPCIONES</span></td></tr>";
		while($dx = $db->sql_fetchrow($mov))
		{
			$texto.="<tr><td class=\"tabla_sencilla_td\">".$dx["codigo"]."</td><td class=\"tabla_sencilla_td\">".$dx["vehiculo"]."</td><td class=\"tabla_sencilla_td\">".$dx["inicio"]."</td><td align=\"center\" class=\"tabla_sencilla_td\"><a href=\"".$CFG->wwwroot."/opera/movimientos.php?mode=cerrarMovimiento&esquema=rec&id_movimiento=".$dx["id"]."\"><img alt='Cerrar' src='".$CFG->wwwroot."/admin/iconos/transparente/check_green.png' border='0'></a></td></tr>";
		}

		$texto.="</table>
			<table width=\"100%\">
			<tr><td align=\"center\" height=\"40\" valign=\"bottom\"><input type=\"button\" value=\"Cerrar\" class=\"boton_verde\" onclick=\"window.close()\"/></td></tr>
			</table>";
		include($CFG->dirroot."/templates/header_popup.php");
		echo $texto;
		include($CFG->dirroot."/templates/footer_popup.php");
		include($CFG->templatedir . "/resize_window.php");
	}
}

function cerrarMovimiento($squema,$idMov)
{
	global $db,$CFG,$ME;

	$mov = $db->sql_row("SELECT inicio, id_vehiculo FROM ".$squema.".movimientos WHERE id=".$idMov);

	if($mov["inicio"] < date("Y-m-d H:i:s"))
	{
		$db->sql_query("UPDATE ".$squema.".movimientos SET final='".date("Y-m-d H:i:s")."' WHERE id=".$idMov);
		$db->sql_query("UPDATE ".$squema.".movimientos_personas SET hora_fin='".date("Y-m-d H:i:s")."' WHERE id_movimiento='".$idMov."' AND hora_fin IS NULL");
		if($squema == "rec")
			$db->sql_query("UPDATE rec.desplazamientos SET hora_fin='".date("Y-m-d H:i:s")."' WHERE id_movimiento='".$idMov."' AND hora_fin IS NULL");

		if($mov["id_vehiculo"] != "")
		{
			$kmHoro = $db->sql_row("SELECT kilometraje as km, horometro as horo FROM vehiculos WHERE id=".$mov["id_vehiculo"]);
			if($kmHoro["km"] != "")
				$db->sql_query("UPDATE ".$squema.".movimientos SET km_final='".$kmHoro["km"]."' WHERE id=".$idMov);
			if($kmHoro["horo"] != "")
				$db->sql_query("UPDATE ".$squema.".movimientos SET horometro_final='".$kmHoro["horo"]."' WHERE id=".$idMov);
		}

		echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
	}else
	{
		echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.outerHeight=200;\nwindow.outerWidth=300;\n</script>\n";
		echo "No se puede cerrar porque su fecha de inicio es mayor que ahora.<br><br>\n";
		echo "<input type=\"button\" onClick=\"window.close();\" value=\"Cerrar\">";
		die();
	}
}


function cerrarMovimientoConFecha_form($squema,$idMov)
{
	global $db,$CFG,$ME;

	$mov = $db->sql_row("SELECT id, inicio, id_vehiculo FROM ".$squema.".movimientos WHERE id=".$idMov);
	$kmHoro = $db->sql_row("SELECT kilometraje, horometro FROM vehiculos WHERE id=".$mov["id_vehiculo"]);

	$newMode="cerrarMovimientoConOtraFecha";
	$titulo = "EDITAR";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/opera/templates/cerrar_movimiento_otra_fecha_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function cerrarMovimientoConOtraFecha($frm)
{
	global $db,$CFG,$ME;

	$squema = $frm["esquema"];
	$fecha = $frm["final"];

	$db->sql_query("UPDATE ".$squema.".movimientos SET final='".$fecha."' WHERE id=".$frm["id"]);
	$db->sql_query("UPDATE ".$squema.".movimientos_personas SET hora_fin='".$fecha."' WHERE id_movimiento='".$frm["id"]."' AND hora_fin IS NULL");
	if($squema == "rec")
		$db->sql_query("UPDATE rec.desplazamientos SET hora_fin='".$fecha."' WHERE id_movimiento='".$frm["id"]."' AND hora_fin IS NULL");

	if(isset($frm["kilometraje"]) && isset($frm["horometro"]))
	{
		$db->sql_query("UPDATE ".$squema.".movimientos SET km_final='".$frm["kilometraje"]."' WHERE id=".$frm["id"]);
		$db->sql_query("UPDATE ".$squema.".movimientos SET horometro_final='".$frm["horometro"]."' WHERE id=".$frm["id"]);
	}

	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}



function listado_bolsas_barrido($idMovimiento)
{
	global $db,$CFG,$ME;

	$movimientoCerrado = false;
	$cerrado = $db->sql_row("SELECT final FROM bar.movimientos WHERE id=".$idMovimiento);
	if($cerrado["final"] != "")
		$movimientoCerrado = true;

	$data = array();
	$qid = $db->sql_query("SELECT m.*,t.tipo FROM bar.movimientos_bolsas m LEFT JOIN bar.tipos_bolsas t ON t.id=m.id_tipo_bolsa WHERE id_movimiento=".$idMovimiento);
	while($query = $db->sql_fetchrow($qid))
	{
		$data[] = '{id: "'.$query["id"].'", tipo: "'.$query["tipo"].'", numero_inicio: "'.$query["numero_inicio"].'", numero_fin: "'.$query["numero_fin"].'"}';
	}

	include($CFG->dirroot."/templates/header_popup_tabview.php"); 
	include($CFG->dirroot."/opera/templates/listado_bolsas_barrido.php"); 
	include($CFG->dirroot."/templates/footer_popup.php");
}

function insertarBolsas($frm)
{
	global $db,$CFG,$ME;

	include($CFG->modulesdir."/bar.movimientos_bolsas.php");
	$entidad->loadValues($frm);
	$id=$entidad->insert();
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function actualizar_datos_bolsas($frm)
{
	global $db,$CFG,$ME;

	//si está cerrado el desplazamiento no se actualiza nadita
	$des = $db->sql_row("SELECT final FROM bar.movimientos WHERE id=".$frm["id"]);
	if($des["final"] == "")
		$db->sql_query("UPDATE bar.movimientos_bolsas SET ".$frm["campo"]."='".$frm["newValue"]."' WHERE id=".$frm["id"]);
	
	return "ok";
}

function agregar_desplazamiento_desde_busq($frm)
{
	global $db,$CFG,$ME;

	$existe = $db->sql_row("SELECT count(id) as num FROM rec.movimientos WHERE inicio::date = '".$frm["fecha"]."' AND final IS NULL AND id_vehiculo=".$frm["id_vehiculo"]);
	if($existe["num"] == 1)
	{
		$mov = $db->sql_row("SELECT id FROM rec.movimientos WHERE inicio::date = '".$frm["fecha"]."' AND final IS NULL AND id_vehiculo=".$frm["id_vehiculo"]);
		echo "<script>window.location.href='".$CFG->wwwroot."/opera/templates/desplazamientos_form.php?id_movimiento=".$mov["id"]."';</script>";
	}elseif($existe["num"] == 0)
		echo "<script>window.location.href='".$CFG->wwwroot."/opera/templates/agregar_desplazamiento_busq_form.php?fecha=".$frm["fecha"]."&id_vehiculo=".$frm["id_vehiculo"]."&error';</script>";
	else
	{
		$mov = $db->sql_query("SELECT mov.*, m.codigo, v.codigo as vehiculo 
				FROM rec.movimientos mov 
				LEFT JOIN vehiculos v ON v.id=mov.id_vehiculo
				LEFT JOIN micros m ON m.id=mov.id_micro
				WHERE mov.inicio::date = '".$frm["fecha"]."' AND mov.final IS NULL AND mov.id_vehiculo=".$frm["id_vehiculo"]);
		$texto = "
			<table width=\"100%\">
			<tr><td align=\"center\" height=\"40\" class=\"azul_12\">Se encontraron varios resultados:</td></tr>
			</table>
			<table width=\"100%\" class=\"tabla_sencilla\">
			<tr><td class=\"tabla_sencilla_td\"><span class=\"azul_12\">RUTA</span></td><td class=\"tabla_sencilla_td\"><span class=\"azul_12\">VEHÍCULO</span></td><td class=\"tabla_sencilla_td\"><span class=\"azul_12\">INICIO</span></td><td class=\"tabla_sencilla_td\"><span class=\"azul_12\">OPCIONES</span></td></tr>";
		while($dx = $db->sql_fetchrow($mov))
		{
			$texto.="<tr><td class=\"tabla_sencilla_td\">".$dx["codigo"]."</td><td class=\"tabla_sencilla_td\">".$dx["vehiculo"]."</td><td class=\"tabla_sencilla_td\">".$dx["inicio"]."</td><td align=\"center\" class=\"tabla_sencilla_td\"><a href=\"".$CFG->wwwroot."/opera/templates/desplazamientos_form.php?id_movimiento=".$dx["id"]."\"><img alt='Cerrar' src='".$CFG->wwwroot."/admin/iconos/transparente/icon-add.gif' border='0'></a></td></tr>";
		}

		$texto.="</table>
			<table width=\"100%\">
			<tr><td align=\"center\" height=\"40\" valign=\"bottom\"><input type=\"button\" value=\"Cerrar\" class=\"boton_verde\" onclick=\"window.close()\"/></td></tr>
			</table>";
		include($CFG->dirroot."/templates/header_popup.php");
		echo $texto;
		include($CFG->dirroot."/templates/footer_popup.php");
		include($CFG->templatedir . "/resize_window.php");
	}
}

function listHoraBolsasFinales($fecha)
{
	global $db, $CFG, $ME;

	$user=$_SESSION[$CFG->sesion]["user"];
	$tp = $datos = array();
	$fields = '{key:"id"},  "codigo", "inicio","final", "opciones"';
	$consulta = "SELECT v.*, m.codigo, a.id_centro 
		FROM bar.movimientos v 
		LEFT JOIN micros m ON m.id=v.id_micro 
		LEFT JOIN ases a ON a.id=m.id_ase 
		WHERE v.inicio::date = '".$fecha."' AND final is null AND a.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')
		ORDER BY codigo, v.inicio";
	$qid = $db->sql_query($consulta);
	while($query = $db->sql_fetchrow($qid))
	{
		//recoger los tipos de bolsas
		$qidTP = $db->sql_query("SELECT t.* FROM bar.movimientos_bolsas m LEFT JOIN bar.tipos_bolsas t ON t.id=m.id_tipo_bolsa WHERE m.id_movimiento=".$query["id"]." ORDER BY tipo");
		while($queryTP = $db->sql_fetchrow($qidTP))
		{
			$tp[$queryTP["id"]]=$queryTP["tipo"];
		}
	}
	foreach($tp as $key => $dx)
	{
		$name = preg_replace("/[^0-9a-z_.]/i","_",$dx);
		$fields.= ', "id_movimiento_bolsa_'.$name.'", "inicio_'.$name.'","final_'.$dx.'"';
	}

	$consulta = "SELECT v.*, m.codigo, a.id_centro, '&nbsp;<a href=\'javascript:cerrar_movimiento('||v.id||')\'><img alt=\'Cerrar\' title=\'Cerrar\' src=\'".$CFG->wwwroot."/admin/iconos/transparente/check_green.png\' border=\'0\'></a>' as opciones
		FROM bar.movimientos v 
		LEFT JOIN micros m ON m.id=v.id_micro 
		LEFT JOIN ases a ON a.id=m.id_ase 
		WHERE v.inicio::date = '".$fecha."' AND final is null AND a.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')
		ORDER BY codigo, v.inicio";
	$qid = $db->sql_query($consulta);
	while($query = $db->sql_fetchrow($qid))
	{
		$linea = '{id: "'.$query["id"].'", codigo: "'.$query["codigo"].'", inicio : "'.$query["inicio"].'",  final : "'.$query["final"].'"';
		foreach($tp as $key => $dx)
		{
			$name = preg_replace("/[^0-9a-z_.]/i","_",$dx); 
			$bolsa = $db->sql_row("SELECT * FROM bar.movimientos_bolsas WHERE id_movimiento=".$query["id"]." AND id_tipo_bolsa=".$key);
			if(isset($bolsa["id"]))
				$linea.= ', id_movimiento_bolsa_'.$name.' : "'.$bolsa["id"].'", inicio_'.$name.' : "'.$bolsa["numero_inicio"].'", final_'.$name.' : "'.$bolsa["numero_fin"].'"';
			else
				$linea.= ', id_movimiento_bolsa : "0", inicio_'.$name.' : "NA", final_'.$name.' : "NA"';
		}

		
		$linea.= ', opciones: "'.$query["opciones"].'"}';
		$datos[] = $linea;
	}

	include($CFG->dirroot."/templates/header_popup_tabview.php"); 
	include($CFG->dirroot."/opera/templates/listado_HoraBolsasFinales.php"); 
	include($CFG->dirroot."/templates/footer_popup.php");
}

function cerrarMovimientoBarDesdeListadoFinal($idMov, $fecha)
{
	global $db,$CFG,$ME;

	$mov = $db->sql_row("SELECT inicio FROM bar.movimientos WHERE id=".$idMov);
	if($mov["inicio"] < $fecha)
	{
		$db->sql_query("UPDATE bar.movimientos SET final='".$fecha."' WHERE id=".$idMov);
		$db->sql_query("UPDATE bar.movimientos_personas SET hora_fin='".$fecha."' WHERE id_movimiento='".$idMov."' AND hora_fin IS NULL");
	}
}

function actualizar_datos_bolsasDesdeListadoFinal($frm)
{
	global $db,$CFG,$ME;

	$db->sql_query("UPDATE bar.movimientos_bolsas SET ".$frm["campo"]."='".$frm["newValue"]."' WHERE id='".$frm["id_movimiento_bolsa"]."'");
}

function listar_apoyos($frm)
{
	global $db,$CFG,$ME;

	$cons = "SELECT a.*, v.codigo, 
			array_to_string(array(
					SELECT m.codigo
					FROM rec.apoyos_movimientos p
					LEFT JOIN rec.movimientos mov ON mov.id=p.id_movimiento
					LEFT JOIN micros m ON m.id=mov.id_micro
					WHERE p.id_apoyo = a.id
					),', ') as ruta
			FROM rec.apoyos a
			LEFT JOIN vehiculos v ON v.id=a.id_vehiculo
			WHERE a.inicio::date='".$frm["fecha"]."'";
	$qid = $db->sql_query($cons);

	include($CFG->dirroot."/templates/header_popup.php"); 
	include($CFG->dirroot."/opera/templates/listado_apoyos.php"); 
	include($CFG->dirroot."/templates/footer_popup.php");
}

function agregar_apoyo($frm)
{
	global $db,$CFG,$ME;

	$apoyo["inicio"] = $frm["fecha"];
	$user=$_SESSION[$CFG->sesion]["user"];
	$qidMov = $db->sql_query("SELECT m.id, i.codigo FROM rec.movimientos m LEFT JOIN micros i ON i.id=m.id_micro LEFT JOIN ases a ON a.id=i.id_ase
			WHERE inicio::date = '".$frm["fecha"]."' AND final IS NULL AND a.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')");
	$db->crear_select("SELECT v.id, v.codigo || '/' || v.placa || CASE WHEN (select count(o.id) FROM mtto.ordenes_trabajo o WHERE o.id_equipo=e.id AND o.fecha_planeada::date = '".$frm["fecha"]."') != 0 then ' (Mantenimiento Programado)' else '' end as nombre
			FROM vehiculos v
			LEFT JOIN mtto.equipos e ON v.id=e.id_vehiculo 
			WHERE v.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')
			ORDER BY v.codigo,v.placa",$vehiculos);

	$newMode = "insertar_apoyo";
	$titulo ="INSERTAR";
	include($CFG->dirroot."/templates/header_popup.php"); 
	include($CFG->dirroot."/opera/templates/apoyo_form.php"); 
	include($CFG->dirroot."/templates/footer_popup.php");
}

function insertar_apoyo($frm)
{
	global $db,$CFG,$ME;

	include($CFG->modulesdir."/rec.apoyos.php");
	list($anio,$mes,$dia)=split("-",$frm["inicio"]);
	$frm["inicio"] = $frm["inicio"]." ".$frm["hora"];
	$entidad->loadValues($frm);
	$id=$entidad->insert();

	$primeraHora = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) - 1 * 24 * 60 * 60);

	$qidMovDia = $db->sql_query("SELECT id FROM rec.movimientos WHERE id_vehiculo=".$frm["id_vehiculo"]." AND inicio >='".$primeraHora." 00:00:00' AND inicio <= '".$frm["inicio"]."'");
	while($movDia = $db->sql_fetchrow($qidMovDia))
	{
		if(!in_array($movDia["id"], $frm["id_movimiento"]))
			$frm["id_movimiento"][] = $movDia["id"];
	}

	$qidTotal = $db->sql_row("SELECT sum(compactadas) as total FROM rec.movimientos mov LEFT JOIN micros i ON i.id=mov.id_micro WHERE mov.id IN (".implode(",",$frm["id_movimiento"]).")");
	$total = $qidTotal["total"];

	foreach($frm["id_movimiento"] as $idmov)
	{
		$mov = $db->sql_row("SELECT compactadas,id_lugar_descargue FROM rec.movimientos mov LEFT JOIN micros i ON i.id=mov.id_micro WHERE mov.id=".$idmov);	
		$perc = ($mov["compactadas"] * $frm["peso"]) / $total; 
		$db->sql_query("INSERT INTO rec.apoyos_movimientos (id_apoyo, id_movimiento) VALUES ($id, $idmov)");

		$lugDes = "null";
		if($mov["id_lugar_descargue"] != "")
			$lugDes = $mov["id_lugar_descargue"];
		$db->sql_query("INSERT INTO rec.movimientos_pesos (id_movimiento, id_lugar_descargue, peso_total, id_apoyo) VALUES ($idmov, $lugDes, '".$perc."', $id)");
	}

	echo "<script>window.location.href='".$CFG->wwwroot."/opera/movimientos.php?mode=listar_apoyos&fecha=".$anio."-".$mes."-".$dia."';</script>";
}

function eliminar_apoyo($frm)
{
	global $db,$CFG,$ME;

	$db->sql_query("DELETE FROM rec.apoyos WHERE id=".$frm["id"]);
	echo "<script>window.location.href='".$CFG->wwwroot."/opera/movimientos.php?mode=listar_apoyos&fecha=".$frm["fecha"]."';</script>";
}


?>
