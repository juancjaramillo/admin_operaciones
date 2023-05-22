<?
include_once("../application.php");

if(!isset($_SESSION[$CFG->sesion]["user"])){
  $errorMsg="No existe la sesión.";
  error_log($errorMsg);
  die($errorMsg);
}

verificarPagina(simple_me($ME));

$mode=nvl($_GET["mode"],nvl($_POST["mode"],""));

switch(nvl($mode)){

	case "agregar_movimientos":
		agregar_movimientos($_GET["esquema"],$_GET["fecha"],nvl($_GET["id_turno"],""));
	break;

	case "agregar_movimiento_unico":
		agregar_movimiento_unico($_GET["id_micro"],$_GET["fecha"]);
	break;

	case "insertar_movimiento_unico":
		insertar_movimiento_unico($_POST);
	break;

	case "editar_vehiculo":
		editar_vehiculo($_GET["id_movimiento"], $_GET["esquema"]);
	break;

	case "actualizar_vehiculo":
		actualizar_vehiculo($_POST);
	break;

	case "insertar_movimientos":
		insertar_movimientos($_POST);
	break;

	case "editar_movimiento_cerrado":
		editar_movimiento_cerrado($_GET["id_movimiento"], $_GET["esquema"]);
	break;

	case "actualizar_movimiento_cerrado":
		actualizar_movimiento_cerrado($_POST);
	break;

	case "agregar_movimiento_descuadrado":
		agregar_movimiento_descuadrado($_POST);
	break;

	case "eliminar_movimiento":
		eliminar_movimiento($_GET);
	break;

	case "agregar_peso_movimiento":
		agregar_peso_movimiento($_GET);
	break;

	case "insertar_peso_movimiento":
		insertar_peso_movimiento($_POST);
	break;

	case "editar_peso_movimiento":
		editar_peso_movimiento($_GET["id"]);
	break;

	case "actualizar_peso_movimiento":
		actualizar_peso_movimiento($_POST);
	break;

	case "eliminar_peso_movimiento":
		eliminar_peso_movimiento($_GET);
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
		desplazmientos("rec",nvl($_GET["fecha"],""),nvl($_GET["id_movimiento"],""), nvl($_GET["codigo_vehiculo"],""), nvl($_GET["id_turno"],""));
	break;

	case "desplazamientos_bar":
		desplazmientos("bar",nvl($_GET["fecha"],""),nvl($_GET["id_movimiento"],""));
	break;

	case "insertarDesplazamientoRec":
		insertarDesplazamientoRec($_POST);
	break;

	case "insertarDesplazamientoRec2":
		insertarDesplazamientoRec2($_POST);
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

	case "actualizar_desplazamiento_cerrado":
		actualizar_desplazamiento_cerrado($_POST);
	break;

	case "cerrarDesplazamientoOtraHora":
		cerrarDesplazamientoOtraHora($_POST);
	break;

	case "eliminar_desplazamiento_rec":
		eliminar_desplazamiento_rec($_GET["id"]);
	break;

	case "editar_desplazamiento_cerrado":
		editar_desplazamiento_cerrado($_GET["id_desplazamiento"]);
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
		cerrarMovimiento($_GET["esquema"],$_GET["id_movimiento"],nvl($_GET["fecha"],date("Y-m-d H:i:s")), nvl($_GET["km"],""), nvl($_GET["horo"],""));
	break;

	case "cerrarMovimientoConFecha_form":
		cerrarMovimientoConFecha_form($_GET["esquema"],$_GET["id_movimiento"]);
	break;

	case "cerrarMovimientoConOtraFecha":
		cerrarMovimiento($_POST["esquema"],$_POST["id"],$_POST["final"],$_POST["kilometraje"],$_POST["horometro"]);
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

	case "editar_apoyo":
		editar_apoyo($_GET["id"]);
	break;

	case "actualizar_apoyo":
		actualizar_apoyo($_POST);
	break;

	case "eliminar_apoyo":
		eliminar_apoyo($_GET);
	break;

	case "reporte_dia":
		reporte_dia($_GET["fecha"], $_GET["esquema"]);
	break;

	case "actualizar_peaje":
		actualizar_peaje($_POST);
	break;

	case "agregar_peaje":
		agregar_peaje($_GET);
	break;

	case "insertar_peaje":
		insertar_peaje($_POST);
	break;

	case "eliminar_peaje":
		eliminar_peaje($_GET);
	break;

	case "agregar_cliente_movimiento":
		agregar_cliente_movimiento($_GET);
	break;

	case "insertar_cliente_movimiento":
		insertar_cliente_movimiento($_POST);
	break;

	case "eliminar_cliente_movimiento":
		eliminar_cliente_movimiento($_GET);
	break;

	case "cambiar_ruta_movimiento":
		cambiar_ruta_movimiento($_POST);
	break;

	case "log_movimientos":
		log_movimientos($_GET);
	break;

	case "graficaDetalleVehiculo":
		graficaDetalleVehiculo($_GET);
	break;

	case "cerrar_peso_movimiento":
		cerrar_peso_movimiento(nvl($_GET["id_movimiento"]), nvl($_GET["id_peso"]));
	break;

	default:
		listado_movimientos($_GET["esquema"], nvl($_GET["fecha"],date("Y-m-d")), nvl($_GET["estado"],"abierta"), nvl($_GET["id_turno"],""));
	break;


}

function listado_movimientos($schema, $fecha, $estado, $id_turno)
{
	global $CFG, $db,$ME;

	$campoAdicional = $opcionAdicional = $opcionAdicionalDos = $opcionLog = "";
	$altDesplazamiento = "Recursos";
	if($schema == "rec")
	{
//		$campoAdicional = ", (SELECT rec.tipos_desplazamientos.tipo||' / Viaje '||numero_viaje FROM rec.desplazamientos LEFT JOIN rec.tipos_desplazamientos ON rec.tipos_desplazamientos.id=rec.desplazamientos.id_tipo_desplazamiento WHERE rec.desplazamientos.id_movimiento=mov.id AND rec.desplazamientos.hora_inicio IS NOT NULL ORDER BY rec.desplazamientos.hora_inicio DESC LIMIT 1) as ultimodesp, combustible";	
		$opcionAdicional = "<a href='|| chr(39)||'javascript:pesos('||mov.id||')'|| chr(39)||'><img alt='|| chr(39)||'Pesos'|| chr(39)||' title='|| chr(39)||'Pesos'|| chr(39)||' src='|| chr(39)||'".$CFG->wwwroot."/admin/iconos/transparente/balance.gif'|| chr(39)||' border='|| chr(39)||'0'|| chr(39)||'></a>&nbsp;<a href='|| chr(39)||'javascript:combustible('||mov.id||')'|| chr(39)||'><img alt='|| chr(39)||'Combustible'|| chr(39)||' title='|| chr(39)||'Combustible'|| chr(39)||' src='|| chr(39)||'".$CFG->wwwroot."/admin/iconos/transparente/combustible.png'|| chr(39)||' border='|| chr(39)||'0'|| chr(39)||'></a>&nbsp;<a href='|| chr(39)||'javascript:peajes('||mov.id||')'|| chr(39)||'><img alt='|| chr(39)||'Peajes'|| chr(39)||' title='|| chr(39)||'Peajes'|| chr(39)||' src='|| chr(39)||'".$CFG->wwwroot."/admin/iconos/transparente/ico-peaje.jpeg'|| chr(39)||' border='|| chr(39)||'0'|| chr(39)||'></a>&nbsp;<a href='|| chr(39)||'javascript:clientes('||mov.id||')'|| chr(39)||'><img alt='|| chr(39)||'Clientes'|| chr(39)||' title='|| chr(39)||'Clientes'|| chr(39)||' src='|| chr(39)||'".$CFG->wwwroot."/admin/iconos/transparente/grupo.jpeg'|| chr(39)||' border='|| chr(39)||'0'|| chr(39)||'></a>&nbsp;";
		if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["cambiar_ruta_movimiento"]))
			$opcionAdicionalDos = "&nbsp;<a href='|| chr(39)||'javascript:cambiarRuta('||mov.id||')'|| chr(39)||'><img alt='|| chr(39)||'Cambiar Ruta'|| chr(39)||' title='|| chr(39)||'Cambiar Ruta'|| chr(39)||' src='|| chr(39)||'".$CFG->wwwroot."/admin/iconos/transparente/camino.jpeg'|| chr(39)||' border='|| chr(39)||'0'|| chr(39)||'></a>";
		$altDesplazamiento = "Desplazamiento";
	}

	if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_rec.desplazamientos_trailer"]))
		$opcionLog = "&nbsp<a href='|| chr(39)||'javascript:log_movimientos('||mov.id||')'|| chr(39)||'><img alt='|| chr(39)||'Log Movimiento'|| chr(39)||' title='|| chr(39)||'Log Movimiento'|| chr(39)||' src='|| chr(39)||'".$CFG->wwwroot."/admin/iconos/transparente/icon-overview.gif'|| chr(39)||' border='|| chr(39)||'0'|| chr(39)||'></a>";

	$condicion = " AND final IS NULL";
	if($estado == "cerrada")
		$condicion = " AND final IS NOT NULL";
	if($id_turno != "" && $id_turno != "%")
		$condicion.= " AND mov.id_turno='".$id_turno."'";
	
	$user=$_SESSION[$CFG->sesion]["user"];
	$consulta = "SELECT mov.*, m.codigo, v.codigo||'/'||v.placa as vehiculo, p.nombre||' '||p.apellido as coordinador, t.nombre as tipo_residuo, s.servicio, c.nombre as cuartelillo, mov.inicio as inicio_real, '&nbsp;&nbsp;<a href='|| chr(39)||'javascript:editar_movimiento_cerrado('||mov.id||')'|| chr(39)||'><img alt='|| chr(39)||'Editar Movimiento Cerrado'|| chr(39)||' title='|| chr(39)||'Editar Movimiento Cerrado'|| chr(39)||' src='|| chr(39)||'".$CFG->wwwroot."/admin/iconos/transparente/iconoeditar.gif'|| chr(39)||' border='|| chr(39)||'0'|| chr(39)||'></a>' as editar_cerrado,  '".$opcionAdicional."&nbsp;<a href='|| chr(39)||'javascript:desplazamiento('||mov.id||')'|| chr(39)||'><img alt='|| chr(39)||'".$altDesplazamiento."'|| chr(39)||' title='|| chr(39)||'".$altDesplazamiento."'|| chr(39)||' src='|| chr(39)||'".$CFG->wwwroot."/admin/iconos/transparente/icon-route.png'|| chr(39)||' border='|| chr(39)||'0'|| chr(39)||'></a>' || case when mov.final IS NULL THEN '&nbsp;<a href='|| chr(39)||'javascript:editar_vehiculo('||mov.id||')'|| chr(39)||'><img alt='|| chr(39)||'Editar Vehículo y No. Orden'|| chr(39)||' title='|| chr(39)||'Editar Vehículo y No. Orden'|| chr(39)||' src='|| chr(39)||'".$CFG->wwwroot."/admin/iconos/transparente/truck.gif'|| chr(39)||' border='|| chr(39)||'0'|| chr(39)||'></a>&nbsp;<a href='|| chr(39)||'javascript:cerrar_movimientoFechaActual('||mov.id||')'|| chr(39)||'><img alt='|| chr(39)||'Cerrar movimiento con fecha actual'|| chr(39)||' title='|| chr(39)||'Cerrar movimiento con fecha actual'|| chr(39)||' src='|| chr(39)||'".$CFG->wwwroot."/admin/iconos/transparente/check_green.png'|| chr(39)||' border='|| chr(39)||'0'|| chr(39)||'></a>&nbsp;<a href='|| chr(39)||'javascript:cerrar_movimiento_con_fecha('||mov.id||')'|| chr(39)||'><img alt='|| chr(39)||'Cerrar Movimiento con otra fecha'|| chr(39)||' title='|| chr(39)||'Cerrar Movimiento con otra fecha'|| chr(39)||' src='|| chr(39)||'".$CFG->wwwroot."/admin/iconos/transparente/icon-activate.gif'|| chr(39)||' border='|| chr(39)||'0'|| chr(39)||'></a>".$opcionAdicionalDos." ' else '' end || '&nbsp;<a href='|| chr(39)||'javascript:eliminar_movimiento('||mov.id||')'|| chr(39)||'><img alt='|| chr(39)||'Eliminar Movimiento'|| chr(39)||' title='|| chr(39)||'Eliminar Movimiento'|| chr(39)||' src='|| chr(39)||'".$CFG->wwwroot."/admin/iconos/transparente/trash-x.png'|| chr(39)||' border='|| chr(39)||'0'|| chr(39)||'></a>$opcionLog' as opciones ".$campoAdicional."
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
		{
			$peso = number_format(averiguarPesoXMov($query["id"]), 2, ",", ".");
			if($query["final"] != "" && in_array($user["nivel_acceso"],$CFG->permisos["abrirMovimientoCerrado"]))
			{
				$query["opciones"] .= $query["editar_cerrado"];
			}

			if(!in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["opciones_movimientos"]))
				$query["opciones"] = "";
		
			$data[] = '{id: "'.$query["id"].'", micro: "'.$query["codigo"].'", servicio: "'.$query["servicio"].'", numero_orden: "'.$query["numero_orden"].'", vehiculo: "'.$query["vehiculo"].'", inicio: "'.$query["inicio"].'", inicio_real:"'.$query["inicio_real"].'", final: "'.$query["final"].'", ultimodesp: "'.$query["ultimodesp"].'", combustible : "'.$query["combustible"].'", peso : "'.$peso.'", opciones : "'.$query["opciones"].'"}';
		}
		else
		{
			if(!in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["opciones_movimientos"]))
				$query["opciones"] = "";
			$data[] = '{id: "'.$query["id"].'", micro: "'.$query["codigo"].'", cuartelillo: "'.$query["cuartelillo"].'", coordinador: "'.$query["coordinador"].'", tipo_residuo: "'.$query["tipo_residuo"].'", inicio: "'.$query["inicio"].'", inicio_real:"'.$query["inicio_real"].'", final: "'.$query["final"].'", opciones : "'.$query["opciones"].'"}';
		}
	}

//	preguntar($data);
	if($schema == "rec")
		$fields = '{key:"id"}, "micro", "servicio", "numero_orden", "vehiculo", "inicio", "inicio_real", "final", "ultimodesp", "combustible", "peso", "opciones"';
	else
		$fields = '{key:"id"}, "micro", "cuartelillo", "coordinador", "tipo_residuo", "inicio", "inicio_real", "final", "opciones"';

	if($schema == "rec")
	{
		$myColumnDefs = '{key:"id"}, {key:"micro", label:"Ruta", sortable: true},  {key:"servicio", label:"Servicio", sortable: true}, {key:"numero_orden", label:"Orden", sortable: true}, {key:"vehiculo", label:"Vehículo", sortable: true}, {key:"inicio", label:"Inicio Prog.", sortable: true}, {key:"inicio_real", label:"Inicio Real", sortable: true}, {key:"final", label:"Final", sortable: true}, {key:"ultimodesp", label:"Ult. Desplazamiento"}, {key:"combustible", label:"Combustible", sortable: true}, {key:"peso", label:"Peso", sortable: true}, {key:"opciones", label:"Opciones"}';
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
					url = '".$CFG->wwwroot."/opera/movimientos_rec.php?mode=listado_desplazamientos&id_movimiento='+primaryKey;
					abrirVentanaJavaScript('listunicadesp','800','300',url);
				}
		});";	
	}
	else
		$myColumnDefs = '{key:"id"}, {key:"micro", label:"Ruta", sortable: true}, {key:"cuartelillo", label:"Cuartelillo", sortable: true}, {key:"coordinador", label:"Supervisor", sortable: true}, {key:"tipo_residuo", label:"Tipo Residuo", sortable: true}, {key:"inicio", label:"Inicio Prog.", sortable: true}, {key:"inicio_real", label:"Inicio Real", sortable: true}, {key:"final", label:"Final", sortable: true}, {key:"opciones", label:"Opciones"}';

	$datos = array("data"=>implode(", ",$data),  "myColumnDefs"=>$myColumnDefs, "fields"=>$fields);

	list($anio,$mes,$dia)=split("-",$fecha);
	$ant = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) - 1 * 24 * 60 * 60);
	$sig = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + 1 * 24 * 60 * 60);
	$paginacionAnt = '<a href="'.$CFG->wwwroot.'/opera/movimientos_'.$schema.'.php?mode=listado_movimientos&esquema='.$schema.'&fecha='.$ant.'"><img src="'.$CFG->wwwroot.'/admin/iconos/transparente/flechaizquierda.gif" border=0></a>';
	$paginacionSig = '<a href="'.$CFG->wwwroot.'/opera/movimientos_'.$schema.'.php?mode=listado_movimientos&esquema='.$schema.'&fecha='.$sig.'"><img src="'.$CFG->wwwroot.'/admin/iconos/transparente/flechaderecha.gif" border=0></a>';

	//filtrar por turnos
	$db->crear_select("SELECT t.id, centro||' / '||t.turno 
			FROM turnos t 
			LEFT JOIN centros c ON c.id_empresa = t.id_empresa
			WHERE c.id in (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')
			ORDER BY centro,t.id",$turnos,$id_turno, "Filtre por Turno...");

	$botones='
		<form name="entryform_filtro" action="'.$ME.'" method="GET"  class="form">
		<input type="hidden" name="esquema" value="'.$schema.'">
		<input type="hidden" name="fecha" value="'.$fecha.'">
		<input type="hidden" name="estado" value="'.$estado.'">
		<select  name="id_turno" id="id_turno" onchange="this.form.submit();">'.$turnos."</select>&nbsp;&nbsp;
		</form>";

	if($schema == "rec")
		$botones .= '<input type="button" class="boton_verde" value="Apoyos" class="boton_verde" value="Apoyos" onClick="abrirVentanaJavaScript(\'apoyos\',\'700\',\'400\',\''.$CFG->wwwroot.'/opera/movimientos_rec.php?mode=listar_apoyos&fecha='.$fecha.'\')">&nbsp;&nbsp;&nbsp;<input type="button" class="boton_verde" value="Desplazamientos del Día" onClick="abrirVentanaJavaScript(\'listdesplazamientos\',\'900\',\'500\',\''.$CFG->wwwroot.'/opera/movimientos_'.$schema.'.php?mode=desplazamientos_'.$schema.'&fecha='.$fecha.'\');">&nbsp;&nbsp;&nbsp;';

	if($schema == "bar")
		$botones .= '<input type="button" class="boton_verde" value="Actualizar Hora y Bolsas Finales" onClick="abrirVentanaJavaScript(\'listHoraBolsasFinales\',\'900\',\'500\',\''.$CFG->wwwroot.'/opera/movimientos_'.$schema.'.php?mode=listHoraBolsasFinales&fecha='.$fecha.'\');">&nbsp;&nbsp;&nbsp;<input type="button" class="boton_verde" value="Recursos" onClick="abrirVentanaJavaScript(\'listdesplazamientos\',\'900\',\'500\',\''.$CFG->wwwroot.'/opera/movimientos_'.$schema.'.php?mode=desplazamientos_'.$schema.'&fecha='.$fecha.'\');">&nbsp;&nbsp;&nbsp;';

	
	//si le faltan movimientos al día se muestra el botón
	$qidMov = $db->sql_row("SELECT count(m.id) as num
			FROM micros m
			LEFT JOIN servicios s ON s.id = m.id_servicio
			LEFT JOIN ases a ON a.id=m.id_ase
			WHERE s.esquema='".$schema."' AND m.id NOT IN (SELECT id_micro FROM ".$schema.".movimientos WHERE inicio::date='".$fecha."') AND m.fecha_hasta IS NULL AND m.id_ase IN (SELECT id FROM ases WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')) AND m.id IN (SELECT id_micro FROM micros_frecuencia WHERE dia='".strftime("%u",strtotime($fecha))."')");
	$tituloBoton = "Insertar los Movimientos del Día";
	if($schema == "bar")
		$tituloBoton = "Insertar los Recursos del Día";

	if($qidMov["num"]!=0)
		$botones .='<input type="button" class="boton_verde" value="'.$tituloBoton.'" onClick="abrirVentanaJavaScript(\'movimientos\',\'900\',\'500\',\''.$CFG->wwwroot.'/opera/movimientos_'.$schema.'.php?mode=agregar_movimientos&fecha='.$fecha.'&esquema='.$schema.'\');">&nbsp;&nbsp;&nbsp;';

	if(!in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["opciones_movimientos"]))
		$botones="";

	$botones.= '<input type="button" class="boton_verde" value="Ir a Fecha" onClick="abrirCalendarioConModo(\'opera/movimientos_'.$schema.'\',\'listado_movimientos\',\''.$schema.'\')">&nbsp;';

	$titulo = "MOVIMIENTOS DEL DÍA ".strtoupper(strftime("%A %d de %B de %Y",strtotime($fecha)));

	if($user["nivel_acceso"]!=1)
	{
		//si es control solo puede ver 30 dias atrás
		$dias = restarFechas(date("Y-m-d"),$fecha);
		if($dias > 30)
			$paginacionAnt = "&nbsp;";
	}

	include($CFG->dirroot."/opera/templates/listado.php");
}


function listado_micros($schema, $fecha)
{
  global $CFG, $db,$ME;

	$user=$_SESSION[$CFG->sesion]["user"];
	$opciones = "";

	$consulta = "SELECT m.id, m.codigo, v.codigo as vehiculo, r.nombre as tipo_residuo, servicio, s.esquema, c.nombre as cuartelillo, p.nombre||' '||p.apellido as coordinador
		FROM micros m
		LEFT JOIN servicios s ON s.id = m.id_servicio
		LEFT JOIN vehiculos v  ON v.id=m.id_vehiculo
		LEFT JOIN tipos_residuos r ON r.id=m.id_tipo_residuo
		LEFT JOIN cuartelillos c ON c.id = m.id_cuartelillo
		LEFT JOIN personas p ON p.id = m.id_coordinador
		WHERE s.esquema='".$schema."' AND m.fecha_hasta IS NULL AND m.id_ase IN (SELECT id FROM ases WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]'))  AND m.id IN (SELECT id_micro FROM micros_frecuencia WHERE dia='".strftime("%u",strtotime($fecha))."')
		ORDER BY m.codigo";

	$data = array();
	$qid = $db->sql_query($consulta);
	while($query = $db->sql_fetchrow($qid))
	{
		$linea = '{id: "'.$query["id"].'", codigo: "'.$query["codigo"].'", vehiculo: "'.$query["vehiculo"].'", tipo_residuo: "'.$query["tipo_residuo"].'", servicio: "'.$query["servicio"].'", cuartelillo: "'.$query["cuartelillo"].'", coordinador: "'.$query["coordinador"].'"';

		$opciones="";
		$qidMov = $db->sql_query("SELECT id FROM ".$query["esquema"].".movimientos WHERE inicio::date = '".$fecha."' AND id_micro='".$query["id"]."'");
		if($db->sql_numrows($qidMov) == 0)
			$opciones = ", opciones : \"<a href='|| chr(39)||'javascript:agregar_movimiento_unico(".$query["id"].")'|| chr(39)||'><img alt='|| chr(39)||'Agregar'|| chr(39)||' src='|| chr(39)||'".$CFG->wwwroot."/admin/iconos/transparente/icon-add.gif'|| chr(39)||' border='|| chr(39)||'0'|| chr(39)||'></a>\"";
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
		$myColumnDefs = '{key:"id"}, {key:"codigo", label:"Código", sortable: true}, {key:"tipo_residuo", label:"Tipo de Residuo", sortable: true}, {key:"servicio", label:"Servicio", sortable: true}, {key:"cuartelillo", label:"Cuartelillo", sortable: true}, {key:"coordinador", label:"Supervisor", sortable: true}, {key:"opciones", label:"Movimiento"}';
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

	//ver si ya se insertó 
	$con = "SELECT count(id) as num FROM $frm[esquema].movimientos WHERE id_micro='".$frm["id_micro"]."' AND inicio::date = '".strftime("%Y-%m-%d",strtotime($frm["inicio"]))."'";
	$qidMov = $db->sql_row($con);

	$ultTurno=$idTurno="";
	if($qidMov["num"] == 0)
	{
		//averiguar el turno de la hora en que se insertó el movimiento
		$hora = strftime("%H:%M:%S",strtotime($frm["inicio"]));
		$qidTur = $db->sql_query("SELECT t.* 
			FROM turnos t 
			LEFT JOIN centros c ON c.id_empresa=t.id_empresa
			LEFT JOIN ases a ON a.id_centro = c.id
			LEFT JOIN micros m ON m.id_ase = a.id
			WHERE m.id=".$frm["id_micro"]."
			ORDER BY hora_inicio");
		while($queryTur = $db->sql_fetchrow($qidTur))
		{
			if($hora >= $queryTur["hora_inicio"])
				$idTurno= $queryTur["id"];
			$ultTurno = $queryTur["id"];
		}
		if($idTurno == "")
			$idTurno = $ultTurno;
		$frm["id_turno"] = $idTurno;
		
		include($CFG->modulesdir."/".$frm["esquema"].".movimientos_".$frm["esquema"].".php");
		$entidad->loadValues($frm);
		$id=$entidad->insert();
		echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
	}
	else
	{
		include($CFG->dirroot."/templates/header_popup.php");
		echo "Ya hay un movimiento con esa fecha<br/><br />";
		echo "<input type=\"button\" onClick=\"window.close();\" value=\"Cerrar\">";
		include($CFG->dirroot."/templates/footer_popup.php");
	}
}


function editar_vehiculo($idMov, $esquema)
{
	global $db, $CFG, $ME;

	$mov = $db->sql_row("SELECT mov.*, to_char(mov.inicio,'YYYY-MM-DD') as inicio, to_char(mov.inicio,'HH24:MI:SS') as hora, a.id_centro, '".$esquema."' as esquema, s.id as id_servicio FROM ".$esquema.".movimientos mov LEFT JOIN micros m ON m.id=mov.id_micro LEFT JOIN ases a ON a.id=m.id_ase LEFT JOIN servicios s ON s.id = m.id_servicio WHERE mov.id=".$idMov);
	$db->crear_select("SELECT v.id, v.codigo || '/' || v.placa || CASE WHEN (select count(o.id) FROM mtto.ordenes_trabajo o WHERE o.id_equipo=e.id AND o.fecha_planeada::date = '".$mov["inicio"]."') != 0 then '(Mantenimiento Programado)' else '' end as nombre
			FROM vehiculos v
			LEFT JOIN mtto.equipos e ON v.id=e.id_vehiculo
			LEFT JOIN tipos_vehiculos_servicios tp ON tp.id_tipo_vehiculo=v.id_tipo_vehiculo
			WHERE v.id_centro = '".$mov["id_centro"]."' AND tp.id_servicio='".$mov["id_servicio"]."' ORDER BY v.codigo,v.placa",$vehiculos,$mov["id_vehiculo"]);

	$newMode="actualizar_vehiculo";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/opera/templates/vehiculo_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}


function actualizar_vehiculo($frm)
{
	global $db, $CFG, $ME;

	/*log*/
	$ant = $db->sql_row("SELECT v.codigo||'/'||v.placa as codigo, numero_orden FROM ".$frm["esquema"].".movimientos m LEFT JOIN vehiculos v ON v.id=m.id_vehiculo WHERE m.id=".$frm["id"]);
	$new = $db->sql_row("SELECT codigo||'/'||placa as codigo FROM vehiculos WHERE id = ".$frm["id_vehiculo"]);
	$accion = "Actualizó Vehículo/Orden\nVehiculo: anterior dato: ".$ant["codigo"]." | nuevo dato: ".$new["codigo"]."\nNum Orden: anterior dato: ".$ant["numero_orden"	]." | nuevo dato: ".$frm["numero_orden"];
	ingresarLogMovimiento("rec", $frm["id"], $accion);
	/*fin log*/

	$db->sql_query("UPDATE ".$frm["esquema"].".movimientos SET id_vehiculo='".$frm["id_vehiculo"]."', numero_orden='".$frm["numero_orden"]."' WHERE id=".$frm["id"]);
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}


function agregar_movimientos($esquema,$fecha,$idTurno="")
{
	global $db, $CFG, $ME;

	$user=$_SESSION[$CFG->sesion]["user"];

	$cond = "";
	if($idTurno != "" && $idTurno != "%")
		$cond = " AND id_turno='".$idTurno."'";
	$consulta = "SELECT m.id, m.codigo, m.id_vehiculo, r.nombre as tipo_residuo, s.servicio, s.esquema, a.id_centro, s.id as id_servicio
			FROM micros m
			LEFT JOIN tipos_residuos r ON r.id=m.id_tipo_residuo
			LEFT JOIN servicios s ON s.id = m.id_servicio
			LEFT JOIN ases a ON a.id=m.id_ase
			WHERE s.esquema='".$esquema."' AND m.id NOT IN (SELECT id_micro FROM ".$esquema.".movimientos WHERE inicio::date='".$fecha."') AND m.fecha_hasta IS NULL AND m.id_ase IN (SELECT id FROM ases WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')) AND m.id IN (SELECT id_micro FROM micros_frecuencia WHERE dia='".strftime("%u",strtotime($fecha))."' ".$cond.") 
			ORDER BY m.codigo";
	$qid = $db->sql_query($consulta);

	$db->crear_select("SELECT t.id, centro||' / '||t.turno 
			FROM turnos t 
			LEFT JOIN centros c ON c.id_empresa = t.id_empresa
			WHERE c.id in (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')
			ORDER BY centro,t.id",$turnos,$idTurno);

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

		if(preg_match("/turno_/",$key,$match))
		{
			$i = str_replace("turno_","",$key);
			$dx[$i]["id_turno"]=$value;
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

		if(preg_match("/numero_orden_/",$key,$match))
		{
			$i = str_replace("numero_orden_","",$key);
			$dx[$i]["numero_orden"]=$value;
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

function agregar_movimiento_descuadrado($frm)
{
	global $db, $CFG, $ME;

	$micro = $db->sql_row("SELECT id_micro FROM micros_frecuencia WHERE id=".$frm["id_micro_frecuencia"]);
	$frm["id_micro"] = $micro["id_micro"];
	$frm["inicio"] = $frm["inicio"]." ".$frm["hora"];
	include($CFG->modulesdir."/".$frm["esquema"].".movimientos.php");
	$entidad->loadValues($frm);
	$idMovimiento=$entidad->insert();

	if($frm["esquema"]=="rec")
	{
		//eliminar los que se agregaron por el módulo y agregar los que corresponden a la frecuencia.
		$db->sql_query("DELETE FROM rec.movimientos_personas WHERE id_movimiento=".$idMovimiento);
		$db->sql_query("DELETE FROM rec.desplazamientos WHERE id_movimiento=".$idMovimiento);

		$db->sql_query("INSERT INTO rec.movimientos_personas (id_movimiento, cargo, id_persona, hora_inicio) SELECT '".$idMovimiento."', id_cargo, id_persona, '".$frm["inicio"]."' FROM frecuencias_operarios WHERE id_frecuencia=".$frm["id_micro_frecuencia"]);

		//desplazamientos
		$db->sql_query("INSERT INTO rec.desplazamientos (id_movimiento, id_tipo_desplazamiento, numero_viaje) SELECT '".$idMovimiento."', id_tipo_desplazamiento, 1 FROM frecuencias_desplazamientos WHERE id_frecuencia=".$frm["id_micro_frecuencia"]." ORDER BY orden");
	}elseif($frm["esquema"]=="bar")
	{
		if($frm["id_persona"] != "%")			
			$db->sql_query("INSERT INTO bar.movimientos_personas (id_movimiento, id_persona, hora_inicio) VALUES ('".$idMovimiento."', '".$frm["id_persona"]."', '".$frm["inicio"]."')");
		$qid = $db->sql_query("SELECT id FROM bar.tipos_bolsas ORDER BY tipo");
		while($b = $db->sql_fetchrow($qid))
		{
			if(isset($frm["id_tipo_bolsa_".$b["id"]]))
			{
				if(preg_match('/(^-?\d+$)|(^-?\d+\.\d+$)/', $frm["id_tipo_bolsa_".$b["id"]], $matches))	
				{
					$db->sql_query("INSERT INTO bar.movimientos_bolsas (id_movimiento, id_tipo_bolsa, numero_inicio) VALUES ('".$idMovimiento."', '".$b["id"]."', '".$frm["id_tipo_bolsa_".$b["id"]]."')");
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

function editar_movimiento_cerrado($id_movimiento, $esquema)
{
	global $db, $CFG, $ME;

	if($esquema == "rec")
	{
		$user=$_SESSION[$CFG->sesion]["user"];
		if(!in_array($user["nivel_acceso"],$CFG->permisos["abrirMovimientoCerrado"])) die();

		$mov = $db->sql_row("SELECT mov.*, a.id_centro, s.id as id_servicio 
			FROM rec.movimientos mov 
			LEFT JOIN micros m ON m.id=mov.id_micro 
			LEFT JOIN ases a ON a.id=m.id_ase 
			LEFT JOIN servicios s ON s.id = m.id_servicio 
			WHERE mov.id=".$id_movimiento);
		$db->crear_select("SELECT v.id, v.codigo || '/' || v.placa || CASE WHEN (select count(o.id) FROM mtto.ordenes_trabajo o WHERE o.id_equipo=e.id AND o.fecha_planeada::date = '".strftime("%Y-%m-%d",strtotime($mov["inicio"]))."') != 0 then '(Mantenimiento Programado)' else '' end as nombre
			FROM vehiculos v
			LEFT JOIN mtto.equipos e ON v.id=e.id_vehiculo
			LEFT JOIN tipos_vehiculos_servicios tp ON tp.id_tipo_vehiculo=v.id_tipo_vehiculo
			WHERE v.id_centro = '".$mov["id_centro"]."' AND tp.id_servicio='".$mov["id_servicio"]."' ORDER BY v.codigo,v.placa",$vehiculos,$mov["id_vehiculo"]);

		//de la ruta
		$rutaAct = $db->sql_row("SELECT r.codigo, to_char(m.inicio,'YYYY-MM-DD') as fecha, id_ase, r.id as id_micro,a.id_centro
			FROM rec.movimientos m
			LEFT JOIN micros r ON r.id = m.id_micro
			LEFT JOIN ases a ON r.id_ase = a.id
			WHERE m.id=".$id_movimiento);

	$db->crear_select("SELECT r.id, r.codigo
			FROM micros r
			LEFT JOIN ases a ON a.id = r.id_ase
			WHERE a.id_centro = '".$rutaAct["id_centro"]."' AND id_servicio IN (SELECT id FROM servicios WHERE esquema='rec' ) AND (r.fecha_hasta IS NULL OR r.fecha_hasta>'" . date("Y-m-d") . "')
			ORDER BY r.codigo", $rutas, $rutaAct["id_micro"]);

		$newMode = "actualizar_movimiento_cerrado";
		$titulo = "ACTUALIZAR MOVIMIENTO CERRADO";
		include($CFG->dirroot."/templates/header_popup.php");
		include($CFG->dirroot."/opera/templates/movimiento_cerrado_form.php");
	}
}

function actualizar_movimiento_cerrado($frm)
{
	global $db, $CFG, $ME;
	
	$user=$_SESSION[$CFG->sesion]["user"];
	if(!in_array($user["nivel_acceso"],$CFG->permisos["abrirMovimientoCerrado"])) die();

	$numero_orden = "null";
	if($frm["numero_orden"] != "")  $numero_orden = "'".$frm["numero_orden"]."'";

	/*log*/
	$ant = $db->sql_row("SELECT m.*, v.codigo||'/'||v.placa as codigo, i.codigo as micro FROM rec.movimientos m LEFT JOIN vehiculos v ON v.id=m.id_vehiculo LEFT JOIN micros i ON i.id = m.id_micro WHERE m.id=".$frm["id"]);
	$newVeh = $db->sql_row("SELECT codigo||'/'||placa as codigo FROM vehiculos WHERE id=".$frm["id_vehiculo"]);
	$newRuta = $ant["micro"];
	if(isset($frm["id_micro"]))
	{
		$ruta = $db->sql_row("SELECT codigo FROM micros WHERE id=".$frm["id_micro"]);
		$newRuta = $ruta["codigo"];
	}	

	$accion="Actualizó movimiento cerrado\nRuta: dato anterior: ".$ant["micro"]." | nuevo dato: ".$newRuta."\nVehículo: dato anterior: ".$ant["codigo"]." | nuevo dato: ".$newVeh["codigo"]."\nInicio: dato anterior: ".$ant["inicio"]." | nuevo dato: ".$frm["inicio"]."\nFinal: dato anterior: ".$ant["final"]." | nuevo dato: ".$frm["final"]."\nKm: dato anterior: ".$ant["km_final"]." | nuevo dato: ".$frm["km_final"]."\nHorometro: dato anterior: ".$ant["horometro_final"]." | nuevo dato: ".$frm["horometro_final"]."\nNum Orden: dato anterior: ".$ant["numero_orden"]." | nuevo dato: ".$frm["numero_orden"];
	ingresarLogMovimiento("rec", $frm["id"], $accion);
	/*fin log*/

	$db->sql_query("UPDATE rec.movimientos SET 
		id_vehiculo = '".$frm["id_vehiculo"]."', 
		inicio = '".$frm["inicio"]."',
		final = '".$frm["final"]."',
		km_final = '".$frm["km_final"]."',
		horometro_final = '".$frm["horometro_final"]."',
		numero_orden = '".$frm["numero_orden"]."'
		WHERE id=".$frm["id"]);
	
	if(isset($frm["id_micro"]))
		$db->sql_query("UPDATE rec.movimientos SET id_micro = '".$frm["id_micro"]."' WHERE id=".$frm["id"]);

	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}



function editar_peso_movimiento($id)
{
	global $db, $CFG, $ME;

	$pm = $db->sql_row("SELECT * FROM rec.movimientos_pesos WHERE id=".$id);
	$vehiculo = $db->sql_row("SELECT id_vehiculo FROM rec.pesos WHERE id=".$pm["id_peso"]);
	$user=$_SESSION[$CFG->sesion]["user"];
	$consultaPeso = "SELECT p.id, v2.codigo||' ('||v2.placa||')' as vehiculo, case when p.peso_total is not null then p.peso_total when p.peso_inicial is not null and peso_final is not null then p.peso_inicial-peso_final else '0' end  as peso, p.fecha_entrada, l.nombre, c.centro, COALESCE(tiquete_entrada,'') as tiquete, (SELECT sum(porcentaje) FROM rec.movimientos_pesos WHERE id_peso=p.id) as porc 
				FROM rec.pesos p
				LEFT JOIN vehiculos v2 ON v2.id = p.id_vehiculo
				LEFT JOIN lugares_descargue l ON l.id=p.id_lugar_descargue
				LEFT JOIN centros c ON c.id=l.id_centro
				WHERE c.id IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')
				ORDER BY p.fecha_entrada DESC, v2.placa, v2.codigo";

	$db->crear_select("SELECT mov.id, mov.inicio||' / '||i.codigo||' / '||v.codigo||' ('||v.placa||')' as movimiento
			FROM rec.movimientos mov
			LEFT JOIN vehiculos v ON v.id = mov.id_vehiculo
			LEFT JOIN micros i ON i.id=mov.id_micro
			WHERE mov.id_vehiculo=".$vehiculo["id_vehiculo"]." ORDER BY mov.inicio DESC,i.codigo,v.placa,v.codigo", $movimientos,$pm["id_movimiento"]);

	$viajes = '';
	$qid = $db->sql_query("SELECT distinct(numero_viaje) as num FROM rec.desplazamientos WHERE id_movimiento=".$pm["id_movimiento"]);
	while($v = $db->sql_fetchrow($qid))
	{
		$sel = "";
		if(trim($pm["viaje"]) == trim($v["num"])) $sel=" selected";
	  $viajes.="<option value='".$v["num"]."' ".$sel.">".$v["num"]."</option>";
	}

	$newMode = "actualizar_peso_movimiento";
	$titulo = "ACTUALIZAR";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/opera/templates/pesos_movimiento_form.php");
}

function actualizar_peso_movimiento($frm)
{
	global $db, $CFG, $ME;

	preguntar($frm);

	/*log*/
	$ant = $db->sql_row("SELECT v2.codigo||' ('||v2.placa||') /' || p.fecha_entrada ||'/'|| l.nombre ||'/'|| c.centro ||'/'|| COALESCE(tiquete_entrada,'') as dato_peso, mp.porcentaje, mp.viaje, mp.id_movimiento
			FROM rec.movimientos_pesos mp
			LEFT JOIN rec.movimientos m ON m.id = mp.id_movimiento
			LEFT JOIN rec.pesos p ON p.id=mp.id_peso
			LEFT JOIN vehiculos v2 ON v2.id = p.id_vehiculo
			LEFT JOIN lugares_descargue l ON l.id=p.id_lugar_descargue
			LEFT JOIN centros c ON c.id=l.id_centro
		WHERE mp.id=".$frm["id"]);
	$new = $db->sql_row("SELECT v2.codigo||' ('||v2.placa||') /' || p.fecha_entrada ||'/'|| l.nombre ||'/'|| c.centro ||'/'|| COALESCE(tiquete_entrada,'') as dato_peso
			FROM rec.pesos p 
			LEFT JOIN vehiculos v2 ON v2.id = p.id_vehiculo
			LEFT JOIN lugares_descargue l ON l.id=p.id_lugar_descargue
			LEFT JOIN centros c ON c.id=l.id_centro
		WHERE p.id=".$frm["id_peso"]);
	if($ant["id_movimiento"] == $frm["id_movimiento"])
	{
		$accion="Actualizó Peso\nPeso: dato anterior: ".$ant["dato_peso"]." | nuevo dato: ".$new["dato_peso"]."\nPorcentaje: dato anterior: ".$ant["porcentaje"]." | nuevo dato: ".$frm["porcentaje"]."\nViaje: dato anterior: ".$ant["viaje"]." | nuevo dato: ".$frm["viaje"];
		ingresarLogMovimiento("rec", $frm["id_movimiento"], $accion);
	}else
	{
		$accion="Ingresó Peso\nPeso: ".$new["dato_peso"]."\nPorcentaje: ".$frm["porcentaje"]."\nViaje: ".$frm["viaje"];
		ingresarLogMovimiento("rec", $frm["id_movimiento"], $accion);
		$accion="Borró Peso\nPeso: ".$ant["dato_peso"]."\nPorcentaje: ".$ant["porcentaje"]."\nViaje: ".$ant["viaje"];
		ingresarLogMovimiento("rec", $ant["id_movimiento"], $accion);
	}
	/*fin log*/

	include($CFG->modulesdir."/rec.movimientos_pesos.php");
	$entidad->loadValues($frm);
	$entidad->set("mode","update");
	$entidad->update();
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function agregar_peso_movimiento($frm)
{
	global $db, $CFG, $ME;

	$user=$_SESSION[$CFG->sesion]["user"];
			
	if(isset($frm["id_peso"]))
	{
		$vehiculo = $db->sql_row("SELECT id_vehiculo FROM rec.pesos WHERE id=".$frm["id_peso"]);
		$consultaPeso = "SELECT p.id, v2.codigo||' ('||v2.placa||')' as vehiculo, case when p.peso_total is not null then p.peso_total when p.peso_inicial is not null and peso_final is not null then p.peso_inicial-peso_final else '0' end  as peso, p.fecha_entrada, l.nombre, c.centro, COALESCE(tiquete_entrada,'') as tiquete, (SELECT sum(porcentaje) FROM rec.movimientos_pesos WHERE id_peso=p.id) as porc 
				FROM rec.pesos p
				LEFT JOIN vehiculos v2 ON v2.id = p.id_vehiculo
				LEFT JOIN lugares_descargue l ON l.id=p.id_lugar_descargue
				LEFT JOIN centros c ON c.id=l.id_centro
				WHERE NOT p.cerrado AND c.id IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')
				ORDER BY p.fecha_entrada DESC, v2.placa, v2.codigo";
		$db->crear_select("SELECT mov.id, mov.inicio||' / '||i.codigo||' / '||v.codigo||' ('||v.placa||')' as movimiento
				FROM rec.movimientos mov
				LEFT JOIN vehiculos v ON v.id = mov.id_vehiculo
				LEFT JOIN micros i ON i.id=mov.id_micro
				WHERE NOT mov.peso_cerrado AND mov.id_vehiculo=".$vehiculo["id_vehiculo"]." ORDER BY mov.inicio DESC,i.codigo,v.placa,v.codigo", $movimientos);
	}elseif($frm["id_movimiento"])
	{
		$vehiculo = $db->sql_row("SELECT id_vehiculo FROM rec.movimientos WHERE id=".$frm["id_movimiento"]);
		$consultaPeso = "SELECT p.id, v2.codigo||' ('||v2.placa||')' as vehiculo, case when p.peso_total is not null then p.peso_total when p.peso_inicial is not null and peso_final is not null then p.peso_inicial-peso_final else '0' end  as peso, p.fecha_entrada, l.nombre, c.centro, COALESCE(tiquete_entrada,'') as tiquete, (SELECT sum(porcentaje) FROM rec.movimientos_pesos WHERE id_peso=p.id) as porc 
				FROM rec.pesos p
				LEFT JOIN vehiculos v2 ON v2.id = p.id_vehiculo
				LEFT JOIN lugares_descargue l ON l.id=p.id_lugar_descargue
				LEFT JOIN centros c ON c.id=l.id_centro
				WHERE NOT p.cerrado AND v2.id = ".$vehiculo["id_vehiculo"]." 
				ORDER BY p.fecha_entrada DESC, v2.placa, v2.codigo";
		$db->crear_select("SELECT mov.id, mov.inicio||' / '||i.codigo||' / '||v.codigo||'('||v.placa||')' as movimiento
				FROM rec.movimientos mov
				LEFT JOIN vehiculos v ON v.id = mov.id_vehiculo
				LEFT JOIN micros i ON i.id=mov.id_micro
				WHERE NOT mov.peso_cerrado AND mov.id_vehiculo=".$vehiculo["id_vehiculo"]." ORDER BY mov.inicio DESC,i.codigo,v.placa,v.codigo", $movimientos, $frm["id_movimiento"]);
		
		$viajes = '';
		$qid = $db->sql_query("SELECT distinct(numero_viaje) as num FROM rec.desplazamientos WHERE id_movimiento=".$frm["id_movimiento"]);
		while($v = $db->sql_fetchrow($qid))
		{
			$viajes.="<option value='".$v["num"]."'>".$v["num"]."</option>";
		}
	}

	$newMode = "insertar_peso_movimiento";
	$titulo = "INGRESAR";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/opera/templates/pesos_movimiento_form.php");
}

function insertar_peso_movimiento($frm)
{
	global $db, $CFG, $ME;

	/*log*/
	$new = $db->sql_row("SELECT v2.codigo||' ('||v2.placa||') /' || p.fecha_entrada ||'/'|| l.nombre ||'/'|| c.centro ||'/'|| COALESCE(tiquete_entrada,'') as dato_peso
			FROM rec.pesos p 
			LEFT JOIN vehiculos v2 ON v2.id = p.id_vehiculo
			LEFT JOIN lugares_descargue l ON l.id=p.id_lugar_descargue
			LEFT JOIN centros c ON c.id=l.id_centro
		WHERE p.id=".$frm["id_peso"]);
	$accion="Ingresó Peso\nPeso: ".$new["dato_peso"]."\nPorcentaje: ".$frm["porcentaje"]."\nViaje: ".$frm["viaje"];
	ingresarLogMovimiento("rec", $frm["id_movimiento"], $accion);
	/*fin log*/

	include($CFG->modulesdir."/rec.movimientos_pesos.php");
	$entidad->loadValues($frm);
	$id=$entidad->insert();
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function eliminar_peso_movimiento($frm)
{
	global $db, $CFG, $ME;

	/*log*/
	$ant = $db->sql_row("SELECT v2.codigo||' ('||v2.placa||') /' || p.fecha_entrada ||'/'|| l.nombre ||'/'|| c.centro ||'/'|| COALESCE(tiquete_entrada,'') as dato_peso, mp.porcentaje, mp.viaje, mp.id_movimiento
			FROM rec.movimientos_pesos mp
			LEFT JOIN rec.movimientos m ON m.id = mp.id_movimiento
			LEFT JOIN rec.pesos p ON p.id=mp.id_peso
			LEFT JOIN vehiculos v2 ON v2.id = p.id_vehiculo
			LEFT JOIN lugares_descargue l ON l.id=p.id_lugar_descargue
			LEFT JOIN centros c ON c.id=l.id_centro
		WHERE mp.id=".$frm["id"]);
	$accion="Borró Peso\nPeso: ".$ant["dato_peso"]."\nPorcentaje: ".$ant["porcentaje"]."\nViaje: ".$ant["viaje"];
	ingresarLogMovimiento("rec", $ant["id_movimiento"], $accion);
	/*fin log*/
	
	include($CFG->modulesdir."/rec.movimientos_pesos.php");
	$entidad->loadValues($frm);
	$entidad->delete();

	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function editar_combustible($idMov)
{
	global $db, $CFG, $ME;

	$mov = $db->sql_row("SELECT m.*, t.combustible as mx_comb
		FROM rec.movimientos m
		LEFT JOIN vehiculos v ON v.id = m.id_vehiculo
		LEFT JOIN tipos_vehiculos t ON t.id = v.id_tipo_vehiculo
		WHERE m.id=".$idMov);
	$newMode = "actualizar_combustible";
	$titulo = "ACTUALIZAR";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/opera/templates/combustible_form.php");
}

function actualizar_combustible($frm)
{
	global $db, $CFG, $ME;

	$comb = $km_tanqueo =  "null";
	if($frm["combustible"] != "")
		$comb = "'".$frm["combustible"]."'";
	if($frm["km_tanqueo"] != "")
		$km_tanqueo = "'".$frm["km_tanqueo"]."'";

	/*log*/
	$ant = $db->sql_row("SELECT * FROM rec.movimientos WHERE id=".$frm["id"]);
	$accion = "Actualizó Combustible\nCombustible: anterior dato: ".$ant["combustible"]." | nuevo dato: ".$frm["combustible"]."\nKm tanqueo: anterior dato: ".$ant["km_tanqueo"	]." | nuevo dato: ".$frm["km_tanqueo"];
	ingresarLogMovimiento("rec", $frm["id"], $accion);
	/*fin log*/

	$db->sql_query("UPDATE rec.movimientos SET combustible=".$comb.", km_tanqueo=".$km_tanqueo." WHERE id=".$frm["id"]);
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.close();\n</script>";
}


function desplazmientos($squema,$fecha,$idMovimiento, $codigo_vehiculo="",$id_turno="")
{
	global $db, $CFG, $ME;
	
	$cond = " TRUE ";
	$movxdia = true;
	$user=$_SESSION[$CFG->sesion]["user"];

	if($fecha != "")
		$cond .= " AND v.inicio::date = '".$fecha."' AND a.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')";
	elseif($idMovimiento != "")
	{
		$cond .= " AND v.id=".$idMovimiento;
		$qifFecha = $db->sql_row("SELECT inicio::date as fecha FROM ".$squema.".movimientos WHERE id=".$idMovimiento);
		$fecha = $qifFecha["fecha"];
		$movxdia = false;
	}

	if($id_turno != "" && $id_turno != "%")
		$cond .=" AND v.id_turno='".$id_turno."'";

	$consulta = "SELECT v.*, m.codigo, a.id_centro 
			FROM ".$squema.".movimientos v 
			LEFT JOIN micros m ON m.id=v.id_micro 
			LEFT JOIN ases a ON a.id=m.id_ase 
			WHERE $cond
			ORDER BY codigo, v.inicio";

	if($codigo_vehiculo != "")
	{
		$consulta = "SELECT v.*, m.codigo, a.id_centro 
			FROM ".$squema.".movimientos v 
			LEFT JOIN micros m ON m.id=v.id_micro 
			LEFT JOIN ases a ON a.id=m.id_ase
			LEFT JOIN vehiculos veh ON veh.id=v.id_vehiculo
			WHERE $cond AND (veh.codigo like '%".$codigo_vehiculo."%'  OR  upper(veh.placa) like '%".strtoupper($codigo_vehiculo)."%')
			ORDER BY m.codigo, v.inicio";
	}
	
	$qid = $db->sql_query($consulta);
	include($CFG->dirroot."/templates/header_popup.php");
	if($squema == "rec")
		include($CFG->dirroot."/opera/templates/desplazamientos_dia.php");
	else
		include($CFG->dirroot."/opera/templates/desplazamientos_barrido_dia.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function insertarDesplazamientoRec2($frm)
{
	global $db, $CFG, $ME;

	$frm["hora_inicio"]=$frm["fecha"] . " " . $frm["horas_inicio"] . ":" . $frm["minutos_inicio"] . ":00";
	if($frm["horas_fin"]!="") $frm["hora_fin"]=$frm["fecha"] . " " . $frm["horas_fin"] . ":" . $frm["minutos_fin"] . ":00";
	$frm["id"] = $db->sql_field("SELECT id FROM rec.desplazamientos WHERE id_movimiento='".$frm["id_movimiento"]."' AND hora_inicio IS NULL AND hora_fin IS NULL ORDER BY id LIMIT 1");
	insertarDesplazamientoRec($frm);
//	preguntar($frm);
}

function insertarDesplazamientoRec($frm)
{
	global $db, $CFG, $ME;

	$db->sql_query("UPDATE rec.desplazamientos SET hora_fin='".$frm["hora_inicio"]."' WHERE id_movimiento='".$frm["id_movimiento"]."' AND hora_fin IS NULL AND hora_inicio<'".$frm["hora_inicio"]."'");
	$fecha = strftime("%Y-%m-%d",strtotime($frm["hora_inicio"]));

	$tipo = $db->sql_row("SELECT tipo FROM rec.tipos_desplazamientos WHERE id=".$frm["id_tipo_desplazamiento"]);
	
	include($CFG->modulesdir."/rec.desplazamientos.php");
	$entidad->loadValues($frm);
	if($frm["id"] == "")
	{
		$id=$entidad->insert();

		/*log*/
		$accion = "Insertó desplazamiento\nTipo: ".$tipo["tipo"]."\nInicio: ".$frm["hora_inicio"]."\nFinal: ".$frm["hora_fin"]."\nViaje: ".$frm["numero_viaje"]."\nKm: ".$frm["km"]."\nHorometro: ".$frm["horometro"];
	}
	else
	{
		$entidad->set("mode","update");
		$entidad->update();
		$id = $frm["id"];

		/*log*/
		$ant = $db->sql_row("SELECT t.tipo, d.* FROM rec.desplazamientos d LEFT JOIN rec.tipos_desplazamientos t ON t.id=d.id_tipo_desplazamiento WHERE d.id=".$frm["id"]);
		$accion = "Actualizó desplazamiento\nTipo: dato anterior: ".$ant["tipo"]." | dato nuevo: ".$tipo["tipo"]."\nInicio: dato anterior: ".$ant["hora_inicio"]." | dato nuevo: ".$frm["hora_inicio"]."\nFinal: dato anterior: ".$ant["hora_fin"]." | dato nuevo: ".$frm["hora_fin"]."\nViaje: dato anterior: ".$ant["numero_viaje"]." | dato anterior: ".$frm["numero_viaje"]."\nKm: dato anterior: ".$ant["km"]." | dato nuevo: ".$frm["km"]."\nHorometro: dato anterior: ".$ant["horometro"]." | dato nuevo: ".$frm["horometro"];
	}

	 ingresarLogMovimiento("rec", $frm["id_movimiento"], $accion);
	/*fin log*/
	
	actualizarKmDesdeMovODes("", $id);
	actualizarHoroDesdeMovODes("",$id);

	$url=$CFG->wwwroot."/opera/templates/agregar_desplazamiento_busq_form.php?fecha=".$fecha;
	if($frm["mode"]=="insertarDesplazamientoRec2") $url=$CFG->wwwroot."/opera/templates/agregar_desplazamiento2.php?fecha=".$fecha;
	echo "<script>window.location.href='".$url."';</script>";
}

function activar_desplazamiento($idDespla)
{
	global $db, $CFG, $ME;

	$qid = $db->sql_row("SELECT v.kilometraje, v.horometro,m.id as id_movimiento, id_vehiculo, id_tipo_desplazamiento, inicio, t.tipo
			FROM rec.desplazamientos d
			LEFT JOIN rec.movimientos m ON d.id_movimiento=m.id
			LEFT JOIN vehiculos v ON m.id_vehiculo=v.id
			LEFT JOIN rec.tipos_desplazamientos t ON t.id=d.id_tipo_desplazamiento
			WHERE d.id=".$idDespla);
	$km = $horo= "null";

	//dia de Ayer
	$ayer = date("Y-m-d",mktime (0,0,0,date("m"),date("d")-1, date("Y")));
	if(strftime("%Y-%m-%d",strtotime($qid["inicio"])) == date("Y-m-d") || strftime("%Y-%m-%d",strtotime($qid["inicio"])) == $ayer )
	{
		if($qid["kilometraje"] != "")
			$km = "'".$qid["kilometraje"]."'";
		if($qid["horometro"] != "")
			$horo = "'".$qid["horometro"]."'";
	}

	$fechaDes = date("Y-m-d H:i:s");
	if(strftime("%Y-%m-%d",strtotime($qid["inicio"])) < $ayer)
		$fechaDes = $qid["inicio"];
	
	$db->sql_query("UPDATE rec.desplazamientos SET hora_fin = '".$fechaDes."' WHERE id_movimiento= '".$qid["id_movimiento"]."' AND hora_fin IS NULL AND hora_inicio < '".$fechaDes."' AND id !='".$idDespla."'");
	$db->sql_query("UPDATE rec.desplazamientos SET hora_inicio='".$fechaDes."', km=".$km.", horometro=".$horo." WHERE id=".$idDespla);
	
	/*log*/
	$accion = str_replace("'","","Activó desplazamiento\nTipo: ".$qid["tipo"]."\nInicio: ".$fechaDes."\nKm: ".$km."\nHorometro: ".$horo);
	 ingresarLogMovimiento("rec", $qid["id_movimiento"], $accion);
	/*finlog*/

	echo "<script>window.location.href='".$CFG->wwwroot."/opera/movimientos_rec.php?mode=listado_desplazamientos&id_movimiento=".$qid["id_movimiento"]."';</script>";
}

function listado_desplazamientos($idMovimento)
{
	global $db, $CFG, $ME;

	$user=$_SESSION[$CFG->sesion]["user"];
	$movimientoCerrado = false;
	$mov = $db->sql_row("SELECT m.final , v.codigo||'/'||v.placa as vehiculo, i.codigo as micro, m.inicio, m.id_vehiculo, m.id_micro
		FROM rec.movimientos m 
		LEFT JOIN vehiculos v ON v.id=m.id_vehiculo
		LEFT JOIN micros i ON i.id=m.id_micro
		WHERE m.id=".$idMovimento);
	
	if($mov["final"] != "")
		$movimientoCerrado = true;

	$ultDesp = $db->sql_row("SELECT id,orden_micro, km, horometro FROM rec.desplazamientos WHERE id_movimiento=".$idMovimento." AND hora_inicio IS NOT NULL ORDER BY hora_inicio DESC LIMIT 1");
	
	$data = $tipos = array();
	$i=0;
	$siguiente=false;
	$qidDes = $db->sql_query("SELECT d.id, t.tipo, d.hora_inicio as hora_inicio_completa, to_char(hora_inicio,'YYYY-MM-DD') as fecha_inicio, to_char(hora_inicio,'HH24:MI:SS') as hora_inicio,   to_char(hora_fin,'YYYY-MM-DD HH24:MI:SS') as hora_fin, numero_viaje, km, horometro, 
		'<a href=\'javascript:activar('||d.id||')\'><img alt=\'Activar\' title=\'Activar\' src=\'".$CFG->wwwroot."/admin/iconos/transparente/estrella2.png\' border=\'0\'></a>&nbsp;&nbsp;' as activacion, 
		case when hora_fin IS NULL and hora_inicio IS NOT NULL then '<a href=\'javascript:cerrar('||d.id||')\'><img alt=\'Cerrar Con Hora Actual\' title=\'Cerrar Con Hora Actual\' src=\'".$CFG->wwwroot."/admin/iconos/transparente/check_green.png\' border=\'0\'></a>&nbsp;&nbsp;<a href=\'javascript:cerrarOtraHora('||d.id||')\'><img alt=\'Cerrar Con Otra Hora\' title=\'Cerrar Con Otra Hora\' src=\'".$CFG->wwwroot."/admin/iconos/transparente/icon-activate.gif\' border=\'0\'></a>&nbsp;&nbsp;<a href=\'javascript:eliminar('||d.id||')\'><img alt=\'Eliminar\' title=\'Eliminar\' src=\'".$CFG->wwwroot."/admin/iconos/transparente/trash-x.png\' border=\'0\'></a>' else '<a href=\'javascript:eliminar('||d.id||')\'><img alt=\'Eliminar\' title=\'Eliminar\' src=\'".$CFG->wwwroot."/admin/iconos/transparente/trash-x.png\' border=\'0\'></a>' end as opciones,  d.orden_micro,  '&nbsp;&nbsp;<a href=\'javascript:editar_desplazamiento_cerrado('||d.id||')\'><img alt=\'Editar Desplazamiento Cerrado\' title=\'Editar Desplazamiento Cerrado\' src=\'".$CFG->wwwroot."/admin/iconos/transparente/iconoeditar.gif\' border=\'0\'></a>' as editar_cerrado
			FROM rec.desplazamientos d 
			LEFT JOIN rec.tipos_desplazamientos t ON d.id_tipo_desplazamiento=t.id
			WHERE id_movimiento='".$idMovimento."' 
			ORDER BY hora_inicio_completa,orden_micro");
	while($des = $db->sql_fetchrow($qidDes))
	{
		if($movimientoCerrado)
			$des["opciones"] = "";
		
		if(!isset($ultDes["orden_micro"]) && $i==0)
		{
			if($des["hora_inicio_completa"] == "" )
					$des["opciones"] = $des["activacion"].$des["opciones"];
		}
		
		if($siguiente)
		{
			if($des["hora_inicio_completa"] == "" )
			{
				$des["opciones"] = $des["activacion"].$des["opciones"];
				$siguiente=false;
			}
		}

		if(isset($ultDesp["id"]))
				if($des["id"] == $ultDesp["id"]) $siguiente=true;
		
		if($des["hora_fin"] != "" && in_array($user["nivel_acceso"],$CFG->permisos["abrirMovimientoCerrado"]))
		{
			$des["opciones"] .= $des["editar_cerrado"];
		}

		if(!in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["opciones_desplazamientos"]))
			$des["opciones"]="";

		$data[] = '{id: "'.$des["id"].'", tipo: "'.$des["tipo"].'", fecha_inicio: "'.$des["fecha_inicio"].'",  hora_inicio: "'.$des["hora_inicio"].'", hora_fin: "'.$des["hora_fin"].'", numero_viaje: "'.$des["numero_viaje"].'", km: "'.$des["km"].'", horometro: "'.$des["horometro"].'", orden_micro:"'.$des["orden_micro"].'",  opciones: "'.$des["opciones"].'"}';
		
		$i++;
	}

	//die;
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
		echo "<script>window.location.href='".$CFG->wwwroot."/opera/movimientos_rec.php?mode=listado_desplazamientos&id_movimiento=".$mov["id"]."';</script>";
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

	$ant = $db->sql_row("SELECT t.tipo, d.* FROM rec.desplazamientos d LEFT JOIN rec.tipos_desplazamientos t ON t.id=d.id_tipo_desplazamiento WHERE d.id=".$frm["id"]);

	//si está cerrado el desplazamiento no se actualiza nadita
	$des = $db->sql_row("SELECT to_char(hora_inicio,'YYYY-MM-DD') as fecha_inicio, to_char(hora_inicio,'HH24:MI:SS') as hora_inicio, hora_fin, id_movimiento FROM rec.desplazamientos WHERE id=".$frm["id"]);
	if($des["hora_fin"] == "")
	{
		if($frm["campo"]=="id_tipo_desplazamiento")
		{
			$tipo = $db->sql_row("SELECT id FROM rec.tipos_desplazamientos WHERE tipo = '".$frm["newValue"]."'");
			$frm["newValue"] = $tipo["id"];
		}
		if($frm["campo"]=="hora_fin")
			$frm["newValue"] = date("Y-m-d H:i:s");
		
		if($frm["campo"] == "hora_inicio")
			$frm["newValue"] = $des["fecha_inicio"]. " ". $frm["newValue"] ;

		if($frm["campo"] == "fecha_inicio")
		{
			$frm["campo"] = "hora_inicio";
			$frm["newValue"] = $frm["newValue"] . " ". $des["hora_inicio"];
		}
		
		$actualizar = true;
		
		if($frm["campo"] == "km" || $frm["campo"] == "horometro")
		{
			$frm["newValue"] = preg_replace("/[,.]/","",$frm["newValue"]);
			$consulta = "SELECT * FROM rec.desplazamientos WHERE id_movimiento='".$des["id_movimiento"]."' AND id!='".$frm["id"]."' AND  ".$frm["campo"]." IS NOT NULL ORDER BY ".$frm["campo"]." DESC LIMIT 1";
			$ant = $db->sql_row($consulta);
			if(nvl($ant[$frm["campo"]]) != "")
			{
				if($ant[$frm["campo"]] > $frm["newValue"])
				{
					$actualizar = false;
					echo "window.alert('El ".$frm["campo"]." no puede ser menor que el anterior desplazamiento. No se puede actualizar.');";
				}
			}
		}

		$des = $db->sql_row("SELECT to_char(hora_inicio,'YYYY-MM-DD') as fecha_inicio, hora_inicio as fecha_inicio_completa, id_movimiento FROM rec.desplazamientos WHERE id=".$frm["id"]);
		

		if($frm["campo"] == "hora_fin")
		{
			if($frm["newValue"] < $des["fecha_inicio_completa"])
			{
				echo "<script>window.alert('La hora fin no puede ser menor que la hora inicio')</script>";
				$actualizar=false;
			}
		}

		if($actualizar)
		{
			$db->sql_query("UPDATE rec.desplazamientos SET ".$frm["campo"]."='".$frm["newValue"]."' WHERE id=".$frm["id"]);

			/*log*/
			$new = $db->sql_row("SELECT t.tipo, d.* FROM rec.desplazamientos d LEFT JOIN rec.tipos_desplazamientos t ON t.id=d.id_tipo_desplazamiento WHERE d.id=".$frm["id"]);
			$accion = "Actualizó desplazamiento\nTipo: dato anterior: ".$ant["tipo"]." | dato nuevo: ".$new["tipo"]."\nInicio: dato anterior: ".$ant["hora_inicio"]." | dato nuevo: ".$new["hora_inicio"]."\nFinal: dato anterior: ".$ant["hora_fin"]." | dato nuevo: ".$new["hora_fin"]."\nViaje: dato anterior: ".$ant["numero_viaje"]." | dato anterior: ".$new["numero_viaje"]."\nKm: dato anterior: ".$ant["km"]." | dato nuevo: ".$new["km"]."\nHorometro: dato anterior: ".$ant["horometro"]." | dato nuevo: ".$new["horometro"];
			ingresarLogMovimiento("rec", $des["id_movimiento"], $accion);
			/*fin log*/
		}

		if($des["fecha_inicio"] == date("Y-m-d"))
		{
			if($frm["campo"] == "km")
				actualizarKmDesdeMovODes("", $frm["id"]);
			if($frm["campo"] == "horometro")
				actualizarHoroDesdeMovODes("", $frm["id"]);
		}
	}

	if(isset($frm["reload"]))
		echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";

	return "Ok";
}

function editar_desplazamiento_cerrado($idDesp)
{
	global $db,$CFG,$ME;

	$user=$_SESSION[$CFG->sesion]["user"];
	if(!in_array($user["nivel_acceso"],$CFG->permisos["abrirMovimientoCerrado"])) die();
	
	$des = $db->sql_row("SELECT * FROM rec.desplazamientos WHERE id=".$idDesp);
	$db->crear_select("SELECT * FROM rec.tipos_desplazamientos ORDER BY tipo",$tipos, $des["id_tipo_desplazamiento"]);

	$newMode = "actualizar_desplazamiento_cerrado";
	$titulo = "ACTUALIZAR DESPLAZAMIENTO CERRADO";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/opera/templates/desplazamiento_cerrado_form.php");
}

function actualizar_desplazamiento_cerrado($frm)
{
	global $db,$CFG,$ME;

	$user=$_SESSION[$CFG->sesion]["user"];
	if(!in_array($user["nivel_acceso"],$CFG->permisos["abrirMovimientoCerrado"])) die();

	$ant = $db->sql_row("SELECT t.tipo, d.* FROM rec.desplazamientos d LEFT JOIN rec.tipos_desplazamientos t ON t.id=d.id_tipo_desplazamiento WHERE d.id=".$frm["id"]);

	$db->sql_query("UPDATE rec.desplazamientos SET
			id_tipo_desplazamiento = '".$frm["id_tipo_desplazamiento"]."',
			hora_inicio = '".$frm["hora_inicio"]."',
			hora_fin = '".$frm["hora_fin"]."',
			numero_viaje = '".$frm["numero_viaje"]."',
			km = '".$frm["km"]."',
			horometro = '".$frm["horometro"]."'
		WHERE id = ".$frm["id"]);

	/*log*/
	$tipo = $db->sql_row("SELECT tipo FROM rec.tipos_desplazamientos WHERE id=".$frm["id_tipo_desplazamiento"]);
	$accion = "Actualizó desplazamiento Cerrado\nTipo: dato anterior: ".$ant["tipo"]." | dato nuevo: ".$tipo["tipo"]."\nInicio: dato anterior: ".$ant["hora_inicio"]." | dato nuevo: ".$frm["hora_inicio"]."\nFinal: dato anterior: ".$ant["hora_fin"]." | dato nuevo: ".$frm["hora_fin"]."\nViaje: dato anterior: ".$ant["numero_viaje"]." | dato anterior: ".$frm["numero_viaje"]."\nKm: dato anterior: ".$ant["km"]." | dato nuevo: ".$frm["km"]."\nHorometro: dato anterior: ".$ant["horometro"]." | dato nuevo: ".$frm["horometro"];
	ingresarLogMovimiento("rec", $ant["id_movimiento"], $accion);
	/*fin log*/

	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}


function eliminar_desplazamiento_rec($idDesp)
{
	global $db,$CFG,$ME;

	/*log*/
	$ant = $db->sql_row("SELECT t.tipo, d.* FROM rec.desplazamientos d LEFT JOIN rec.tipos_desplazamientos t ON t.id=d.id_tipo_desplazamiento WHERE d.id=".$idDesp);
	$accion = "Eliminó desplazamiento\nTipo: ".$ant["tipo"]."\nInicio: ".$ant["hora_inicio"]."\nFinal: ".$ant["hora_fin"]."\nViaje: ".$ant["numero_viaje"]."\nKm: ".$ant["km"]."\nHorometro:".$ant["horometro"];
	ingresarLogMovimiento("rec", $ant["id_movimiento"], $accion);
	/*fin log*/

	$db->sql_query("DELETE FROM rec.desplazamientos WHERE id=".$idDesp);
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function cerrarDesplazamientoOtraHora($frm)
{
	global $db,$CFG,$ME;

	/*log*/
	$ant = $db->sql_row("SELECT t.tipo, d.* FROM rec.desplazamientos d LEFT JOIN rec.tipos_desplazamientos t ON t.id=d.id_tipo_desplazamiento WHERE d.id=".$frm["id_desplazamiento"]);
	$accion = "Actualizó Final Desplazamiento\nTipo: ".$ant["tipo"]."\nFinal: dato anterior: ".$ant["hora_fin"]." | dato nuevo: ".$frm["hora_fin"];
	ingresarLogMovimiento("rec", $ant["id_movimiento"], $accion);
	/*fin log*/

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
		$personas = array(
			"cond" => array("cargo"=>21,"id_movimiento"=>$frm["id_movimiento"]), 
			"ayu1" => array("cargo"=>22,"id_movimiento"=>$frm["id_movimiento"]), 
			"ayu2" => array("cargo"=>22,"id_movimiento"=>$frm["id_movimiento"]),
			"ayu3" => array("cargo"=>22,"id_movimiento"=>$frm["id_movimiento"])
			);
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

			if(preg_match("/ayudante3_/",$key,$match))
			{
				$at = str_replace("ayudante3_","",$key);
				$personas["ayu3"][$at]=$value;
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
			
			/*log*/
			$per = $db->sql_row("SELECT p.nombre||' '||p.apellido as nombre, c.nombre as nombre_cargo, mp.* 
				FROM ".$frm["esquema"].".movimientos_personas mp 
				LEFT JOIN personas p ON p.id=mp.id_persona 
				LEFT JOIN cargos c ON c.id = mp.cargo 
				WHERE mp.id=".$id);
			$accion = "Ingresó Operario al movimiento\nPersona: ".$per["nombre"]."\nCargo: ".$per["nombre_cargo"]."\nInicio: ".$per["hora_inicio"]."\nFinal: ".$per["hora_fin"];
			ingresarLogMovimiento($frm["esquema"], $frm["id_movimiento"], $accion);
			/*fin log*/
		}
	}

	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function actualizar_datos_operarios($frm)
{
	global $db,$CFG,$ME;

	if($frm["esquema"] == "rec")
		$ant = $db->sql_row("SELECT p.nombre||' '||p.apellido as nombre, c.nombre as nombre_cargo, mp.* 
		FROM ".$frm["esquema"].".movimientos_personas mp 
		LEFT JOIN personas p ON p.id=mp.id_persona 
		LEFT JOIN cargos c ON c.id = mp.cargo 
		WHERE mp.id=".$frm["id"]);
	else
		$ant = $db->sql_row("SELECT p.nombre||' '||p.apellido as nombre, '' as nombre_cargo, mp.* 
		FROM ".$frm["esquema"].".movimientos_personas mp 
		LEFT JOIN personas p ON p.id=mp.id_persona 
		WHERE mp.id=".$frm["id"]);

	$db->sql_query("UPDATE ".$frm["esquema"].".movimientos_personas SET ".$frm["campo"]."='".$frm["newValue"]."' WHERE id=".$frm["id"]);

	/*log*/
	if($frm["esquema"] == "rec")
		$new = $db->sql_row("SELECT p.nombre||' '||p.apellido as nombre, c.nombre as nombre_cargo, mp.* 
		FROM ".$frm["esquema"].".movimientos_personas mp 
		LEFT JOIN personas p ON p.id=mp.id_persona 
		LEFT JOIN cargos c ON c.id = mp.cargo 
		WHERE mp.id=".$frm["id"]);
	else
		$new = $db->sql_row("SELECT p.nombre||' '||p.apellido as nombre, '' as nombre_cargo, mp.* 
		FROM ".$frm["esquema"].".movimientos_personas mp 
		LEFT JOIN personas p ON p.id=mp.id_persona 
		WHERE mp.id=".$frm["id"]);
	$accion = "Actualizó Operario al movimiento\nPersona: dato anterior: ".$ant["nombre"]." | dato nuevo: ".$new["nombre"]."\nCargo: dato anterior: ".$ant["nombre_cargo"]." | dato nuevo: ".$new["nombre_cargo"]."\nInicio: dato anterior: ".$ant["hora_inicio"]." | dato nuevo: ".$new["hora_inicio"]."\nFinal: dato anterior: ".$ant["hora_fin"]." | dato nuevo: ".$new["hora_fin"];
	ingresarLogMovimiento($frm["esquema"], $ant["id_movimiento"], $accion);
	/*fin log*/
	
	if(isset($frm["reload"]))
		echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";

	return "ok";
}

function eliminar_operario($id,$esquema)
{
	global $db,$CFG,$ME;

	/*log*/
	if($frm["esquema"] == "rec")
		$per = $db->sql_row("SELECT p.nombre||' '||p.apellido as nombre, c.nombre as nombre_cargo, mp.* 
			FROM ".$esquema.".movimientos_personas mp 
			LEFT JOIN personas p ON p.id=mp.id_persona 
			LEFT JOIN cargos c ON c.id = mp.cargo 
			WHERE mp.id=".$id);
	else
		$per = $db->sql_row("SELECT p.nombre||' '||p.apellido as nombre, '' as nombre_cargo, mp.* 
			FROM ".$esquema.".movimientos_personas mp 
			LEFT JOIN personas p ON p.id=mp.id_persona 
			WHERE mp.id=".$id);
	$accion = "Borró Operario al movimiento\nPersona: ".$per["nombre"]."\nCargo: ".$per["nombre_cargo"]."\nInicio: ".$per["hora_inicio"]."\nFinal: ".$per["hora_fin"];
	ingresarLogMovimiento($esquema, $per["id_movimiento"], $accion);
	/*fin log*/

	$db->sql_query("DELETE FROM ".$esquema.".movimientos_personas WHERE id=".$id);
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function cerrar_movimiento_rec_desde_busq($frm)
{
	global $db,$CFG,$ME;

	$mov = $db->sql_row("SELECT count(id) as num FROM rec.movimientos WHERE inicio::date = '".$frm["inicio"]."' AND inicio < '".$frm["final"]."' AND final IS NULL AND id_vehiculo=".$frm["id_vehiculo"]);

	if($mov["num"] == 1)
	{
		$cerraMov = $db->sql_row("SELECT id FROM rec.movimientos WHERE inicio::date = '".$frm["inicio"]."' AND inicio < '".$frm["final"]."' AND final IS NULL AND id_vehiculo=".$frm["id_vehiculo"]);
		cerrarMovimiento("rec",$cerraMov["id"],$frm["final"],$frm["kilometraje"],$frm["horometro"]);
	}elseif($mov["num"] == 0)
		echo "<script>window.location.href='".$CFG->wwwroot."/opera/templates/cerrar_movimiento_rec_form.php?fecha=".$frm["inicio"]."&id_vehiculo=".$frm["id_vehiculo"]."&error';</script>";
	else
	{
		$mov = $db->sql_query("SELECT mov.*, m.codigo, v.codigo as vehiculo 
				FROM rec.movimientos mov 
				LEFT JOIN vehiculos v ON v.id=mov.id_vehiculo
				LEFT JOIN micros m ON m.id=mov.id_micro
				WHERE mov.inicio::date = '".$frm["inicio"]."' AND mov.inicio < '".$frm["final"]."' AND mov.final IS NULL AND mov.id_vehiculo=".$frm["id_vehiculo"]);
		
		$texto = "
			<table width=\"100%\">
			<tr><td align=\"center\" height=\"40\" class=\"azul_12\">Se encontraron varios resultados:</td></tr>
			</table>
			<table width=\"100%\" class=\"tabla_sencilla\">
			<tr><td class=\"tabla_sencilla_td\"><span class=\"azul_12\">RUTA</span></td><td class=\"tabla_sencilla_td\"><span class=\"azul_12\">VEHÍCULO</span></td><td class=\"tabla_sencilla_td\"><span class=\"azul_12\">INICIO</span></td><td class=\"tabla_sencilla_td\"><span class=\"azul_12\">OPCIONES</span></td></tr>";
		while($dx = $db->sql_fetchrow($mov))
		{
			$texto.="<tr><td class=\"tabla_sencilla_td\">".$dx["codigo"]."</td><td class=\"tabla_sencilla_td\">".$dx["vehiculo"]."</td><td class=\"tabla_sencilla_td\">".$dx["inicio"]."</td><td align=\"center\" class=\"tabla_sencilla_td\"><a href=\"".$CFG->wwwroot."/opera/movimientos_rec.php?mode=cerrarMovimiento&esquema=rec&id_movimiento=".$dx["id"]."&fecha=".$frm["final"]."&km=".$frm["kilometraje"]."&horo=".$frm["horometro"]."\"><img alt='Cerrar' src='".$CFG->wwwroot."/admin/iconos/transparente/check_green.png' border='0'></a></td></tr>";
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

function cerrarMovimiento($squema,$idMov,$fecha="",$km="",$horo="")
{
	global $db,$CFG,$ME;
	
	$mov = $db->sql_row("SELECT m.inicio, m.final, m.id_vehiculo, i.codigo FROM ".$squema.".movimientos m LEFT JOIN micros i ON i.id=m.id_micro WHERE m.id=".$idMov);
	if($fecha=="")
		$fecha=date("Y-m-d H:i:s");

	if($mov["inicio"] < $fecha)
	{
		$db->sql_query("UPDATE ".$squema.".movimientos SET final='".$fecha."' WHERE id=".$idMov);
		$db->sql_query("UPDATE ".$squema.".movimientos_personas SET hora_fin='".$fecha."' WHERE id_movimiento='".$idMov."' AND hora_fin IS NULL");
		if($squema == "rec")
		{
			$db->sql_query("DELETE FROM rec.desplazamientos WHERE hora_fin IS NULL AND hora_inicio IS NULL AND id_movimiento=".$idMov);
			$db->sql_query("UPDATE rec.desplazamientos SET hora_fin='".$fecha."' WHERE id_movimiento='".$idMov."' AND hora_fin IS NULL");
		}
	
		if($mov["id_vehiculo"] != "")
		{
			$kmHoro = $db->sql_row("SELECT kilometraje as km, horometro as horo FROM vehiculos WHERE id=".$mov["id_vehiculo"]);
			if($kmHoro["km"] != "")
				$db->sql_query("UPDATE ".$squema.".movimientos SET km_final='".$kmHoro["km"]."' WHERE id=".$idMov);
			if($kmHoro["horo"] != "")
				$db->sql_query("UPDATE ".$squema.".movimientos SET horometro_final='".$kmHoro["horo"]."' WHERE id=".$idMov);
		}

		if($km!="")
		{
			$db->sql_query("UPDATE ".$squema.".movimientos SET km_final='".$km."' WHERE id=".$idMov);
			actualizarKmDesdeMovODes($idMov);
		}
		if($horo!="")
		{
			$db->sql_query("UPDATE ".$squema.".movimientos SET horometro_final='".$horo."' WHERE id=".$idMov);
			actualizarHoroDesdeMovODes($idMov);
		}

		/*log*/
		$accion = "Cerró movimiento\nRuta: ".$mov["codigo"]."\nFinal: ".$fecha;
		ingresarLogMovimiento($squema, $idMov, $accion);	
		actualizarUsuarioCerroMovimiento($squema, $idMov);
		/*fin log*/

		echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
	}else
	{
		echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.outerHeight=200;\nwindow.outerWidth=300;\n</script>\n";
		echo "No se puede cerrar porque su fecha de inicio es mayor que la fecha de cierre.<br><br>\n";
		echo "<input type=\"button\" onClick=\"window.close();\" value=\"Cerrar\">";
		die();
	}
}


function cerrarMovimientoConFecha_form($squema,$idMov)
{
	global $db,$CFG,$ME;

	$mov = $db->sql_row("SELECT id, inicio, id_vehiculo FROM ".$squema.".movimientos WHERE id=".$idMov);
	if($mov["id_vehiculo"] != "")
		$kmHoro = $db->sql_row("SELECT kilometraje, horometro FROM vehiculos WHERE id=".$mov["id_vehiculo"]);

	$newMode="cerrarMovimientoConOtraFecha";
	$titulo = "EDITAR";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/opera/templates/cerrar_movimiento_otra_fecha_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
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
	
	/*log*/
	$tipo = $db->sql_row("SELECT tipo FROM bar.tipos_bolsas WHERE id=".$frm["id_tipo_bolsa"]);
	$accion = "Insertó Bolsas al Movimiento\nTipo: ".$tipo["tipo"]."\nNum Inicio: ".$frm["numero_inicio"]."\nNum Fin: ".$frm["numero_fin"];
	ingresarLogMovimiento("bar", $frm["id_movimiento"], $accion);	
	/*fin log*/

	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function actualizar_datos_bolsas($frm)
{
	global $db,$CFG,$ME;

	$ant = $db->sql_row("SELECT tipo, m.* FROM bar.movimientos_bolsas m LEFT JOIN bar.tipos_bolsas b ON b.id = m. id_tipo_bolsa WHERE m.id=".$frm["id"]);

	//si está cerrado el desplazamiento no se actualiza nadita
	$des = $db->sql_row("SELECT final FROM bar.movimientos WHERE id=".$frm["id"]);
	if($des["final"] == "")
	{
		$db->sql_query("UPDATE bar.movimientos_bolsas SET ".$frm["campo"]."='".$frm["newValue"]."' WHERE id=".$frm["id"]);

		/*log*/
		$new = $db->sql_row("SELECT tipo, m.* FROM bar.movimientos_bolsas m LEFT JOIN bar.tipos_bolsas b ON b.id = m. id_tipo_bolsa WHERE m.id=".$frm["id"]);
		$accion = "Actualizó Bolsas del Movimiento\nTipo: nuevo dato: ".$ant["tipo"]." | dato anterior: ".$new["tipo"]."\nNum Inicio: nuevo dato: ".$ant["numero_inicio"]." | dato anterior: ".$new["numero_inicio"]."\nNum Fin: nuevo dato: ".$ant["numero_fin"]." | dato anterior:".$new["numero_fin"];
		ingresarLogMovimiento("bar", $ant["id_movimiento"], $accion);	
		/*fin log*/
	}
	
	return "ok";
}

function agregar_desplazamiento_desde_busq($frm)
{
	global $db,$CFG,$ME;
	
	$user=$_SESSION[$CFG->sesion]["user"];
	$cons = "SELECT id_vehiculo
		FROM rec.movimientos m
		LEFT JOIN vehiculos v ON v.id=m.id_vehiculo
		WHERE m.inicio::date = '".$frm["fecha"]."' AND m.final IS NULL AND v.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]') 
			AND (codigo like '%".$frm["codigo"]."%'  OR  upper(placa) like '%".strtoupper($frm["codigo"])."%')";
	$qidEx = $db->sql_query($cons);
	$existe = $db->sql_numrows($qidEx);
	$vehiculo = $db->sql_fetchrow($qidEx);
		
	if($existe == 1)
	{
		$mov = $db->sql_row("SELECT id FROM rec.movimientos WHERE inicio::date = '".$frm["fecha"]."' AND final IS NULL AND id_vehiculo=".$vehiculo["id_vehiculo"]);
		echo "<script>window.location.href='".$CFG->wwwroot."/opera/templates/desplazamientos_form.php?id_movimiento=".$mov["id"]."&fecha=".$frm["fecha"]."';</script>";
	}elseif($existe == 0)
		echo "<script>window.location.href='".$CFG->wwwroot."/opera/templates/agregar_desplazamiento_busq_form.php?fecha=".$frm["fecha"]."&codigo=".$frm["codigo"]."&error';</script>";
	else
	{
		$mov = $db->sql_query("SELECT mov.*, m.codigo, v.codigo as vehiculo 
				FROM rec.movimientos mov 
				LEFT JOIN vehiculos v ON v.id=mov.id_vehiculo
				LEFT JOIN micros m ON m.id=mov.id_micro
				WHERE mov.inicio::date = '".$frm["fecha"]."' AND mov.final IS NULL AND mov.id_vehiculo=".$vehiculo["id_vehiculo"]);
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

	$consulta = "SELECT v.*, m.codigo, a.id_centro, '&nbsp;<a href='|| chr(39)||'javascript:cerrar_movimiento('||v.id||')'|| chr(39)||'><img alt='|| chr(39)||'Cerrar'|| chr(39)||' title='|| chr(39)||'Cerrar'|| chr(39)||' src='|| chr(39)||'".$CFG->wwwroot."/admin/iconos/transparente/check_green.png'|| chr(39)||' border='|| chr(39)||'0'|| chr(39)||'></a>' as opciones
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
	
		/*log*/
		$accion = "Cerró movimiento\nFinal: ".$fecha;
		ingresarLogMovimiento("bar", $idMov, $accion);	
		actualizarUsuarioCerroMovimiento("bar", $idMov);
		/*fin log*/
	}
}

function actualizar_datos_bolsasDesdeListadoFinal($frm)
{
	global $db,$CFG,$ME;

	$ant = $db->sql_row("SELECT tipo, m.* FROM bar.movimientos_bolsas m LEFT JOIN bar.tipos_bolsas b ON b.id = m. id_tipo_bolsa WHERE m.id=".$frm["id_movimiento_bolsa"]);

	$db->sql_query("UPDATE bar.movimientos_bolsas SET ".$frm["campo"]."='".$frm["newValue"]."' WHERE id='".$frm["id_movimiento_bolsa"]."'");

	/*log*/
	$new = $db->sql_row("SELECT tipo, m.* FROM bar.movimientos_bolsas m LEFT JOIN bar.tipos_bolsas b ON b.id = m. id_tipo_bolsa WHERE m.id=".$frm["id_movimiento_bolsa"]);
	$accion = "Actualizó Bolsas del Movimiento\nTipo: nuevo dato: ".$ant["tipo"]." | dato anterior: ".$new["tipo"]."\nNum Inicio: nuevo dato: ".$ant["numero_inicio"]." | dato anterior: ".$new["numero_inicio"]."\nNum Fin: nuevo dato: ".$ant["numero_fin"]." | dato anterior:".$new["numero_fin"];
	ingresarLogMovimiento("bar", $ant["id_movimiento"], $accion);	
	/*fin log*/
}

function listar_apoyos($frm)
{
	global $db,$CFG,$ME;

	$user=$_SESSION[$CFG->sesion]["user"];
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
			WHERE a.inicio::date='".$frm["fecha"]."' AND v.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')";
	$qid = $db->sql_query($cons);

	include($CFG->dirroot."/templates/header_popup.php"); 
	include($CFG->dirroot."/opera/templates/listado_apoyos.php"); 
	include($CFG->dirroot."/templates/footer_popup.php");
}

function agregar_apoyo($frm)
{
	global $db,$CFG,$ME;

	list($anio,$mes,$dia)=split("-",$frm["fecha"]);
	$ant = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) - 1 * 24 * 60 * 60);
		
	$apoyo["inicio"] = $frm["fecha"];
	$user=$_SESSION[$CFG->sesion]["user"];
	$qidMov = $db->sql_query("SELECT m.id, i.codigo || ' / '||m.inicio as codigo
			FROM rec.movimientos m 
			LEFT JOIN micros i ON i.id=m.id_micro 
			LEFT JOIN ases a ON a.id=i.id_ase
			WHERE (inicio::date = '".$frm["fecha"]."' OR inicio::date = '".$ant."') AND a.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')
			ORDER BY i.codigo");
	$db->crear_select("SELECT v.id, v.codigo || '/' || v.placa || CASE WHEN (select count(o.id) FROM mtto.ordenes_trabajo o WHERE o.id_equipo=e.id AND o.fecha_planeada::date = '".$frm["fecha"]."') != 0 then ' (Mantenimiento Programado)' else '' end as nombre
			FROM vehiculos v
			LEFT JOIN mtto.equipos e ON v.id=e.id_vehiculo 
			WHERE v.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')
			ORDER BY v.codigo,v.placa",$vehiculos, nvl($frm["id_vehiculo"]));
	
	$movimientos = array();
	if(isset($frm["id_movimiento"])) $movimientos[] = $frm["id_movimiento"];

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
	$entidad->loadValues($frm);
	$id=$entidad->insert();

	$primeraHora = strftime("%Y-%m-%d",strtotime($frm["inicio"]));

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
		$db->sql_query("INSERT INTO rec.apoyos_movimientos (id_apoyo, id_movimiento) VALUES ($id, $idmov)");
		
		/*log*/
		$veh = $db->sql_row("SELECT codigo||'/'||placa as codigo FROM vehiculos WHERE id=".$frm["id_vehiculo"]);
		$accion = "Insertó Apoyo\nFecha:".$frm["inicio"]."\nVehículo: ".$veh["codigo"]."\nPeso Total: ".$frm["peso"]."\nKm Inicial: ".$frm["km_inicial"]."\nKm Final: ".$frm["km_final"]."\nFecha Final: ".$frm["final"];
		ingresarLogMovimiento("rec", $idmov, $accion);	
		/*fin log*/
	}

	if($frm["accion"]=="cerrar")
		echo "<script>window.location.href='".$CFG->wwwroot."/opera/movimientos_rec.php?mode=listar_apoyos&fecha=".$primeraHora."';</script>";
	elseif($frm["accion"]=="sincerrar")
		echo "<script>window.location.href='".$CFG->wwwroot."/opera/movimientos_rec.php?mode=editar_apoyo&id=".$id."';</script>";
	else
		echo "<script>window.location.href='".$CFG->wwwroot."/opera/movimientos_rec.php?mode=agregar_apoyo&fecha=".$primeraHora."';</script>";
}


function editar_apoyo($idApoyo)
{
	global $db,$CFG,$ME;

	$user=$_SESSION[$CFG->sesion]["user"];
	$apoyo = $db->sql_row("SELECT * FROM rec.apoyos WHERE id=".$idApoyo);

	list($anio,$mes,$dia)=split("-",strftime("%Y-%m-%d",strtotime($apoyo["inicio"])));
	$ant = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) - 1 * 24 * 60 * 60);

	$qidMov = $db->sql_query("SELECT m.id, i.codigo || ' / '||m.inicio as codigo
			FROM rec.movimientos m 
			LEFT JOIN micros i ON i.id=m.id_micro 
			LEFT JOIN ases a ON a.id=i.id_ase
			WHERE (inicio::date = '".strftime("%Y-%m-%d",strtotime($apoyo["inicio"]))."' OR inicio::date = '".$ant."' ) AND a.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')
			ORDER BY i.codigo");
	$db->crear_select("SELECT v.id, v.codigo || '/' || v.placa || CASE WHEN (select count(o.id) FROM mtto.ordenes_trabajo o WHERE o.id_equipo=e.id AND o.fecha_planeada::date = '".strftime("%Y-%m-%d",strtotime($apoyo["inicio"]))."') != 0 then ' (Mantenimiento Programado)' else '' end as nombre
			FROM vehiculos v
			LEFT JOIN mtto.equipos e ON v.id=e.id_vehiculo 
			WHERE v.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')
			ORDER BY v.codigo,v.placa",$vehiculos, $apoyo["id_vehiculo"]);

	$movimientos = array();
	$qidMovApo = $db->sql_query("SELECT * FROM  rec.apoyos_movimientos WHERE id_apoyo=".$idApoyo);
	while($queryMA = $db->sql_fetchrow($qidMovApo))
	{
		$movimientos[] = $queryMA["id_movimiento"];
	}

	$newMode = "actualizar_apoyo";
	$titulo ="ACTUALIZAR";
	include($CFG->dirroot."/templates/header_popup.php"); 
	include($CFG->dirroot."/opera/templates/apoyo_form.php"); 
	include($CFG->dirroot."/templates/footer_popup.php");
}

function actualizar_apoyo($frm)
{
	global $db,$CFG,$ME;

	$ant = $db->sql_row("SELECT codigo||'/'||placa as codigo, a.* FROM rec.apoyos a LEFT JOIN vehiculos v ON v.id=a.id_vehiculo WHERE a.id=".$frm["id"]);

	include($CFG->modulesdir."/rec.apoyos.php");
	$entidad->loadValues($frm);
	$entidad->set("mode","update");
	$entidad->update();

	$new = $db->sql_row("SELECT codigo||'/'||placa as codigo, a.* FROM rec.apoyos a LEFT JOIN vehiculos v ON v.id=a.id_vehiculo WHERE a.id=".$frm["id"]);

	$primeraHora = strftime("%Y-%m-%d",strtotime($frm["inicio"]));
	$qidMovDia = $db->sql_query("SELECT id FROM rec.movimientos WHERE id_vehiculo=".$frm["id_vehiculo"]." AND inicio >='".$primeraHora." 00:00:00' AND inicio <= '".$frm["inicio"]."'");
	while($movDia = $db->sql_fetchrow($qidMovDia))
	{
		if(!in_array($movDia["id"], $frm["id_movimiento"]))
			$frm["id_movimiento"][] = $movDia["id"];
	}

	/*log*/
	$qidBA = $db->sql_query("SELECT * FROM rec.apoyos_movimientos WHERE id_apoyo=".$frm["id"]);
	while($queryBA = $db->sql_fetchrow($qidBA))
	{
		$accion = "Borró Apoyo (automático)\nFecha:".$ant["inicio"]."\nVehículo: ".$ant["codigo"]."\nPeso Total: ".$ant["peso"]."\nKm Inicial: ".$ant["km_inicial"]."\nKm Final: ".$ant["km_final"]."\nFecha Final: ".$ant["final"];
		ingresarLogMovimiento("rec", $queryBA["id_movimiento"], $accion);	
	}
	/*fin log*/

	$db->sql_query("DELETE FROM rec.apoyos_movimientos WHERE id_apoyo=".$frm["id"]);

	foreach($frm["id_movimiento"] as $idmov)
	{
		$db->sql_query("INSERT INTO rec.apoyos_movimientos (id_apoyo, id_movimiento) VALUES ('".$frm["id"]."', $idmov)");
	
		/*log*/
		$veh = $db->sql_row("SELECT codigo||'/'||placa as codigo FROM vehiculos WHERE id=".$frm["id_vehiculo"]);
		$accion = "Actualizó/Insertó Apoyo Relacionado\nFecha: dato anterior: ".$ant["inicio"]." | dato nuevo: ".$new["inicio"]."\nVehículo: dato anterior: ".$ant["codigo"]." | dato nuevo: ".$new["codigo"]."\nPeso Total: dato anterior: ".$ant["peso"]." | dato nuevo: ".$new["peso"]."\nKm Inicial: dato anterior: ".$ant["km_inicial"]." | dato nuevo: ".$new["km_inicial"]."\nKm Final: dato anterior: ".$ant["km_final"]." | dato nuevo: ".$new["km_final"]."\nFecha Final: dato anterior: ".$ant["final"]." | dato nuevo: ".$new["final"];
		ingresarLogMovimiento("rec", $idmov, $accion);	
		/*fin log*/
	}

	if($frm["accion"]=="cerrar")
		echo "<script>window.location.href='".$CFG->wwwroot."/opera/movimientos_rec.php?mode=listar_apoyos&fecha=".$primeraHora."';</script>";
	elseif($frm["accion"]=="sincerrar")
		echo "<script>window.location.href='".$CFG->wwwroot."/opera/movimientos_rec.php?mode=editar_apoyo&id=".$frm["id"]."';</script>";
	else
		echo "<script>window.location.href='".$CFG->wwwroot."/opera/movimientos_rec.php?mode=agregar_apoyo&fecha=".$primeraHora."';</script>";
}


function eliminar_apoyo($frm)
{
	global $db,$CFG,$ME;

	/*log*/
	$ant = $db->sql_row("SELECT codigo||'/'||placa as codigo, a.* FROM rec.apoyos a LEFT JOIN vehiculos v ON v.id=a.id_vehiculo WHERE a.id=".$frm["id"]);
	$qidBA = $db->sql_query("SELECT * FROM rec.apoyos_movimientos WHERE id_apoyo=".$frm["id"]);
	while($queryBA = $db->sql_fetchrow($qidBA))
	{
		$accion = "Borró Apoyo\nFecha:".$ant["inicio"]."\nVehículo: ".$ant["codigo"]."\nPeso Total: ".$ant["peso"]."\nKm Inicial: ".$ant["km_inicial"]."\nKm Final: ".$ant["km_final"]."\nFecha Final: ".$ant["final"];
		ingresarLogMovimiento("rec", $queryBA["id_movimiento"], $accion);	
	}
	/*fin log*/

	$db->sql_query("DELETE FROM rec.apoyos WHERE id=".$frm["id"]);
	echo "<script>window.location.href='".$CFG->wwwroot."/opera/movimientos_rec.php?mode=listar_apoyos&fecha=".$frm["fecha"]."';</script>";
}

function reporte_dia($fecha, $schema)
{
	global $db,$CFG,$ME;

	include($CFG->dirroot."/templates/header_popup.php"); 
	
	$user=$_SESSION[$CFG->sesion]["user"];
	$consulta = "SELECT m.codigo as ruta, v.codigo||'/'||v.placa as vehiculo, mov.*
		FROM ".$schema.".movimientos mov
		LEFT JOIN vehiculos v ON v.id=mov.id_vehiculo
		LEFT JOIN micros m ON m.id=mov.id_micro
		WHERE mov.inicio::date = '".$fecha."' AND m.id_ase IN (SELECT id FROM ases WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]'))
		ORDER BY inicio,m.codigo";
	$qid = $db->sql_query($consulta);

	?>
	<form class="form">
	<table width="100%">
		<tr>
			<td height="40" colspan=3 align="center"><span class="azul_16"><strong>MOVIMIENTOS DEL DÍA <br> <?=strtoupper(strftime("%A %d de %B de %Y",strtotime($fecha)))?></strong></span></td>
		</tr>
		<tr>
			<td valign="top">
				<table width="100%" cellpadding="5" cellspacing="3">
					<tr>
						<td>
							<table width="100%" border=1 bordercolor="#7fa840" align="center" id="tabla_mov">
								<tr>
									<td align="center">RUTA</td>
									<td align="center">VEHÍCULO</td>
									<td align="center">INICIO</td>
									<td align="center">FINAL</td>
									<td align="center">KM INICIO</td>
									<td align="center">KM FINAL</td>
									<td align="center">COMBUSTIBLE</td>
									<td align="center">No. ORDEN</td>
									<td align="center">DESPLAZAMIENTOS</td>
									<td align="center">PERSONAS</td>
									<td align="center">PESOS</td>
									<td align="center">APOYOS</td>
								</tr>
								<?while($mov = $db->sql_fetchrow($qid)){?>
								<tr>
									<td><?=$mov["ruta"]?></td>
									<td><?=$mov["vehiculo"]?></td>
									<td><?=$mov["inicio"]?></td>
									<td><?=$mov["final"]?></td>
									<td><?=$mov["km_final"]?></td>
									<td><?=$mov["horometro_final"]?></td>
									<td><?=$mov["combustible"]?></td>
									<td><?=$mov["numero_orden"]?></td>
									<td valign="top">
									<?
									$qidDes = $db->sql_query("SELECT t.tipo, d.*
											FROM rec.desplazamientos d
											LEFT JOIN rec.tipos_desplazamientos t ON t.id=d.id_tipo_desplazamiento
											WHERE d.id_movimiento='".$mov["id"]."'
											ORDER BY hora_inicio, d.id");	
									while($des = $db->sql_fetchrow($qidDes))
									{
										echo "Tipo : ".$des["tipo"]."<br />";
										if($des["hora_inicio"] == "")							
											echo "<font color='red'>Inicio : ".$des["hora_inicio"]."</font><br />";
										else
											echo "Inicio : ".$des["hora_inicio"]."<br />";

										if($des["hora_fin"] == "")
											echo "<font color='red'>Fin : ".$des["hora_fin"]."</font><br />";
										else
											echo "Fin : ".$des["hora_fin"]."<br />";

										echo "Número Viaje : ".$des["numero_viaje"]."<br />";

										if($des["km"] == "")
											echo "<font color='red'>Km : ".$des["km"]."</font><br />";
										else
											echo "Km : ".$des["km"]."<br />";

										if($des["horometro"] == "")
											echo "<font color='red'>Horómetro : ".$des["horometro"]."</font><br />";
										else
											echo "Horómetro : ".$des["horometro"]."<br />";

										echo "------<br />";
									}
									?>
									</td>
									<td valign="top">
									<?
									$qidPer = $db->sql_query("SELECT c.nombre as cargo, p.nombre||' '||p.apellido as persona, hora_inicio, hora_fin
											FROM rec.movimientos_personas mov
											LEFT JOIN personas p ON p.id=mov.id_persona
											LEFT JOIN cargos c ON c.id=mov.cargo
											WHERE mov.id_movimiento=".$mov["id"]."
											ORDER BY c.nombre, p.nombre, p.apellido");
									while($per = $db->sql_fetchrow($qidPer))
									{
										echo $per["cargo"]." : ".$per["persona"]."<br />";
										echo "Inicio : ".$per["hora_inicio"]."<br />";
										echo "Fin : ".$per["hora_fin"]."<br />";
										echo "------<br />";	
									}
									?>
									</td>
									<td valign="top">
									<?
									$total = 0;
									$qidPeso = $db->sql_query("SELECT v.placa||' / '||v.codigo as vehiculo, l.nombre, p.*, mp.porcentaje, mp.viaje, mp.id_movimiento
											FROM rec.movimientos_pesos mp
											LEFT JOIN rec.pesos p ON p.id=mp.id_peso
											LEFT JOIN lugares_descargue l ON l.id=p.id_lugar_descargue
											LEFT JOIN vehiculos v ON v.id=p.id_vehiculo
											WHERE mp.id_movimiento=".$mov["id"]."
											ORDER BY p.fecha_entrada");
									while($peso = $db->sql_fetchrow($qidPeso))
									{
										echo "Datos Pesaje:<br >";
										echo "Vehículo : ".$peso["vehiculo"]."<br />";
										echo "Peso Inicial : ".$peso["peso_inicial"]."<br />";
										echo "Peso Final : ".$peso["peso_final"]."<br />";
										echo "Peso Total : ".$peso["peso_total"]."<br />";
										echo "Descargue : ".$peso["nombre"]."<br />";
										echo "Tiquete Entrada : ".$peso["tiquete_entrada"]."<br />";
										echo "Tiquete Salida : ".$peso["tiquete_salida"]."<br />";
										echo "Entrada : ".$peso["fecha_entrada"]."<br />";
										echo "Salida : ".$peso["fecha_salida"]."<br /><br />";

										echo "Porcentaje :".$peso["porcentaje"]."<br />";
										$pv = averiguarPesoXMov($peso["id_movimiento"], $peso["viaje"],false);
										$total+=$pv;
										echo "Subtotal = ".$pv."<br />";
										$apo = averiguarPesoApoyoxMov($peso["id_movimiento"]);
										$total+=$apo;	
										echo "Peso Apoyos = ".$apo."<br />";
										echo "------<br />";
									}
									echo "<br />TOTAL = ".$total;
									?>
									</td>
									<td valign="top">
									<?
									$qidApoyo = $db->sql_query("SELECT v.codigo||'/'||v.placa as vehiculo, p.*
											FROM rec.apoyos_movimientos am
											LEFT JOIN rec.apoyos p ON p.id=am.id_apoyo
											LEFT JOIN vehiculos v ON v.id=p.id_vehiculo
											WHERE am.id_movimiento=".$mov["id"]."
											ORDER BY inicio");
									while($apo = $db->sql_fetchrow($qidApoyo))
									{
										echo "Vehiculo : ".$apo["vehiculo"]."<br />";
										echo "Inicio : ".$apo["inicio"]."<br />";
										echo "Final : ".$apo["final"]."<br />";
										echo "Peso : ".$apo["peso"]."<br />";
										echo "Km Inicio : ".$apo["km_inicial"]."<br />";
										echo "Km Final : ".$apo["km_final"]."<br />";
										echo "------<br />";
									}
									?>
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
	</form>




<?
	include($CFG->dirroot."/templates/footer_popup.php");

}

function actualizar_peaje($frm)
{
	global $db, $CFG;

	/*log*/
	$ant = $db->sql_row("SELECT p.nombre, m.veces, m.id_movimiento FROM rec.movimientos_peajes m LEFT JOIN peajes p ON p.id = m.id_peaje WHERE m.id=".$frm["id"]);
	$accion = "Actualizó Peaje \nPeaje:".$ant["nombre"]."\nVeces: dato anterior : ".$ant["veces"]." | dato nuevo: ".$frm["newValue"];
	ingresarLogMovimiento("rec", $ant["id_movimiento"], $accion);	
	/*fin log*/

	$db->sql_query("UPDATE rec.movimientos_peajes SET veces='".$frm["newValue"]."' WHERE id=".$frm["id"]);
}

function agregar_peaje($frm)
{
	global $db, $CFG, $ME;

	$db->crear_select("SELECT id, nombre FROM peajes WHERE id_centro IN (SELECT a.id_centro FROM rec.movimientos m LEFT JOIN micros i ON i.id=m.id_micro LEFT JOIN ases a ON a.id=i.id_ase WHERE m.id='".$frm["id_movimiento"]."')", $peajes);

	$newMode="insertar_peaje";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/opera/templates/peajes_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function insertar_peaje($frm)
{
	global $db, $CFG, $ME;

	include($CFG->modulesdir."/rec.movimientos_peajes.php");
	$entidad->loadValues($frm);
	$id=$entidad->insert();

	/*log*/
	$ant = $db->sql_row("SELECT p.nombre, m.veces, m.id_movimiento FROM rec.movimientos_peajes m LEFT JOIN peajes p ON p.id = m.id_peaje WHERE m.id=".$id);
	$accion = "Insertó Peaje \nPeaje:".$ant["nombre"]."\nVeces: ".$ant["veces"];
	ingresarLogMovimiento("rec", $ant["id_movimiento"], $accion);	
	/*fin log*/

	echo "<script>window.location.href='".$CFG->wwwroot."/opera/templates/listado_peajes_movimiento.php?id_movimiento=".$frm["id_movimiento"]."';</script>";
}

function eliminar_peaje($frm)
{
	global $db, $CFG, $ME;

	/*log*/
	$ant = $db->sql_row("SELECT p.nombre, m.veces, m.id_movimiento FROM rec.movimientos_peajes m LEFT JOIN peajes p ON p.id = m.id_peaje WHERE m.id=".$frm["id"]);
	$accion = "Borró Peaje \nPeaje:".$ant["nombre"]."\nVeces: ".$ant["veces"];
	ingresarLogMovimiento("rec", $ant["id_movimiento"], $accion);	
	/*fin log*/

	$db->sql_query("DELETE FROM rec.movimientos_peajes WHERE id=".$frm["id"]);
	echo "<script>window.location.href='".$CFG->wwwroot."/opera/templates/listado_peajes_movimiento.php?id_movimiento=".$frm["id_movimiento"]."';</script>";
}

function agregar_cliente_movimiento($frm)
{
	global $db, $CFG, $ME;

	$mov = $db->sql_row("SELECT r.codigo, m.inicio, a.id_centro
		FROM rec.movimientos m
		LEFT JOIN micros r ON r.id=m.id_micro
		LEFT JOIN ases a ON a.id = r.id_ase
		WHERE m.id=".$frm["id_movimiento"]);
	
	$db->crear_select("SELECT id, nombre || ' (' || codigo || ' / ' || direccion || ')' as cliente
		FROM clientes
		WHERE gp AND id_centro='".$mov["id_centro"]."'", $clientes);
	$newMode="insertar_cliente_movimiento";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/opera/templates/clientes_mov_form.php");
	include($CFG->dirroot."/templates/footer_popup.php");
}

function insertar_cliente_movimiento($frm)
{
	global $db, $CFG, $ME;

	include($CFG->modulesdir."/rec.movimientos_clientes.php");
	$entidad->loadValues($frm);
	$id=$entidad->insert();
	
	/*log*/
	$ant = $db->sql_row("SELECT c.nombre, m.id_movimiento FROM rec.movimientos_clientes m LEFT JOIN clientes c ON c.id=m.id_cliente WHERE m.id=".$id);
	$accion = "Agregó Cliente \nCliente:".$ant["nombre"];
	ingresarLogMovimiento("rec", $ant["id_movimiento"], $accion);	
	/*fin log*/

	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function eliminar_cliente_movimiento($frm)
{
	global $db, $CFG, $ME;

	/*log*/
	$ant = $db->sql_row("SELECT c.nombre, m.id_movimiento FROM rec.movimientos_clientes m LEFT JOIN clientes c ON c.id=m.id_cliente WHERE m.id=".$frm["id"]);
	$accion = "Borró Cliente \nCliente:".$ant["nombre"];
	ingresarLogMovimiento("rec", $ant["id_movimiento"], $accion);	
	/*fin log*/

	$db->sql_query("DELETE FROM rec.movimientos_clientes WHERE id=".$frm["id"]);
	echo "<script>window.location.href='".$CFG->wwwroot."/opera/templates/listado_clientes_movimiento.php?id_movimiento=".$frm["id_movimiento"]."';</script>";
}

function cambiar_ruta_movimiento($frm)
{
	global $db, $CFG, $ME;

	$rutaAct = $db->sql_row("SELECT m.id_micro, i.codigo FROM ".$frm["esquema"].".movimientos m LEFT JOIN micros i ON i.id=m.id_micro WHERE m.id=".$frm["id_movimiento"]);
	$db->sql_query("INSERT INTO ".$frm["esquema"].".rutas_cambios_historicos (id_movimiento, id_ruta_original,  id_nueva_ruta,  id_persona, fecha) VALUES ('".$frm["id_movimiento"]."', '".$rutaAct["id_micro"]."', '".$frm["id_nueva_ruta"]."', '".$_SESSION[$CFG->sesion]["user"]["id"]."', now())");

	$db->sql_query("UPDATE ".$frm["esquema"].".movimientos SET id_micro='".$frm["id_nueva_ruta"]."' WHERE id=".$frm["id_movimiento"]);

	/*log*/
	$new = $db->sql_row("SELECT m.id_micro, i.codigo FROM ".$frm["esquema"].".movimientos m LEFT JOIN micros i ON i.id=m.id_micro WHERE m.id=".$frm["id_movimiento"]);
	$accion = "Cambió ruta movimiento\nRuta: dato anterior: ".$rutaAct["codigo"]." | dato nuevo: ".$new["codigo"];
	ingresarLogMovimiento("rec", $frm["id_movimiento"], $accion);	
	/*fin log*/

	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function log_movimientos($frm)
{
	global $db, $CFG;

	$mov = $db->sql_row("SELECT codigo, m.inicio, log
		FROM ".$frm["esquema"].".movimientos m
		LEFT JOIN micros i ON i.id = m.id_micro
		WHERE m.id=".$frm["id_movimiento"]);
	include($CFG->dirroot."/templates/header_popup.php");
	?>
<form  class="form">
<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong>LOG DEL MOVIMIENTO<br /><?=$mov["codigo"]?> / <?=$mov["inicio"]?></strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td align='left'><?=nl2br($mov["log"])?></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=3 align="center">
			<input type="button" class="boton_verde" value="Cerrar" onclick="window.close()"/>
		</td>
	</tr>
	</form>
</table>

<?

	
	include($CFG->dirroot."/templates/footer_popup.php");
}

function graficaDetalleVehiculo($frm)
{
	global $db, $CFG, $ME;

	$veh = $db->sql_row("SELECT id FROM vehiculos WHERE codigo='".$frm["codigo"]."' AND id_centro='".$frm["id_centro"]."'");
	$diasBTW = restarFechas($frm["final"],$frm["inicio"]);
	$peso = array();

	for($i=0 ; $i<=$diasBTW; $i++)
	{
		list($anio,$mes,$diaAc)=split("-",$frm["inicio"]);
		$diaAc = date("Y-m-d",mktime(0,0,0, $mes,$diaAc,$anio) + $i * 24 * 60 * 60);
		$qid = $db->sql_query("SELECT m.id
			FROM rec.movimientos m 
			LEFT JOIN vehiculos v ON v.id=m.id_vehiculo
			WHERE inicio::date = '".$diaAc."' AND m.id_vehiculo='".$veh["id"]."'");
		while($query = $db->sql_fetchrow($qid))
		{
			$fecha=ucfirst(strftime("%b.%d.%Y",strtotime($diaAc)));
			if(!isset($peso[$fecha])) $peso[$fecha] = 0;
			$peso[$fecha] += averiguarPesoXMov($query["id"], "", true, $frm["id_turno"]);
		}
	}

	$titulo = "PRODUCCIÓN ".$frm["codigo"];
	if($frm["id_turno"] != "")
	{
		$turno = $db->sql_row("SELECT turno FROM turnos WHERE id=".$frm["id_turno"]);
		$titulo.=" / Turno: ".$turno["turno"];
	}

	$dxGraf = array("data"=>array_values($peso),  "labels"=>array_keys($peso));
	graficaBarras($dxGraf, $titulo , "TOTAL (tons)", "Fecha", "Tons");
}


function cerrar_peso_movimiento($id_movimiento, $id_peso)
{
	global $db, $CFG, $ME;

	if($id_movimiento != "")
	{
		$qid = $db->sql_query("UPDATE rec.movimientos 
			SET
				peso_cerrado = true,
				id_persona_cerro_peso = '".$_SESSION[$CFG->sesion]["user"]["id"]."',
				fecha_cerro_peso = now()
			WHERE id = ". $id_movimiento);

		$link = $CFG->wwwroot."/opera/templates/listado_pesos_movimiento.php?id_movimiento=".$id_movimiento;
		echo "<script>window.location.href='".$link."';</script>";
	}else
	{
		$qid = $db->sql_query("UPDATE rec.pesos
			SET
				cerrado = true,
				id_persona_cerro = '".$_SESSION[$CFG->sesion]["user"]["id"]."',
				fecha_cerro = now()
			WHERE id = ". $id_peso);

		echo "<script>window.opener.location.reload();\nwindow.close();\n</script>";
	}
}


?>
