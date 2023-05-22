<?
include_once("../application.php");
include($CFG->dirroot."/templates/header_popup.php");

if(!isset($_SESSION[$CFG->sesion]["user"])){
  $errorMsg="No existe la sesión.";
  error_log($errorMsg);
  die($errorMsg);
}
$user=$_SESSION[$CFG->sesion]["user"];

switch(nvl($mode)){

	case "reporte";
		reporte($_POST);
	break;

	default:
		imprimirForm();
	break;
}

function reporte($frm)
{
	global $db, $CFG, $ME;

	$condicion="true";
	$user=$_SESSION[$CFG->sesion]["user"];
	if($user["nivel_acceso"]!=1)
		$condicion="c.id IN (" . implode(",",$user["id_centro"]) . ")";

	$tpmttos = array();
	$qidTM = $db->sql_query("SELECT * FROM mtto.tipos ORDER BY id");
	while($queryTM = $db->sql_fetchrow($qidTM))
	{
		$tpmttos[$queryTM["id"]] = "MANTENIMIENTO ".strtoupper($queryTM["tipo"]);
	}

	$datos = array();
	if($frm["tipo"] == "por_equipo")
	{
		$titulo = "POR EQUIPO";
		$tituloDos = "EQUIPO";
		$qid = $db->sql_query("SELECT e.id, e.nombre, c.centro, c.id as id_centro FROM mtto.equipos e LEFT JOIN centros c ON c.id=e.id_centro WHERE ".$condicion." ORDER BY c.centro, e.nombre");
		while($query = $db->sql_fetchrow($qid))
		{
			$nombre = $query["nombre"]." / ".$query["centro"];
			$queryOT = $db->sql_query("SELECT r.id_tipo_mantenimiento, r.id as id_rutina, o.id as id_orden
					FROM mtto.ordenes_trabajo o
					LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
					WHERE o.id_equipo=".$query["id"]." AND id_estado_orden_trabajo IN (SELECT id FROM mtto.estados_ordenes_trabajo WHERE cerrado) 
						AND o.fecha_ejecucion_inicio>='".$frm["fecha_inicio"]." 00:00:00' AND o.fecha_ejecucion_fin<='".$frm["fecha_fin"]." 23:59:59'");
			while($qidOT = $db->sql_fetchrow($queryOT))
			{
				if(!isset($datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["numero"]))
					$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["numero"]=0;
				if(!isset($datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["valor"]))
					$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["valor"] = 0;
				$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["numero"]+=1;

				//personas
				$qidPer=$db->sql_query("SELECT oc.tiempo as tiempo_ejecucion, c.valor
						FROM mtto.ordenes_trabajo_actividades_cargos oc
						LEFT JOIN mtto.ordenes_trabajo_actividades a ON a.id=oc.id_orden_trabajo_actividad
						LEFT JOIN cargos c ON c.id=oc.id_cargo
						WHERE a.id_orden_trabajo='".$qidOT["id_orden"]."'");
				while($quePer = $db->sql_fetchrow($qidPer))
				{
					$total = ($quePer["tiempo_ejecucion"]*$quePer["valor"])/60;
					$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["valor"]+=$total;
				}

				//elementos
/*
	 Cambié esto, porque ahora hay bodegas...
						LEFT JOIN mtto.elementos_existencias ex ON ex.id_elemento=e.id AND ex.id_centro='".$query["id_centro"]."'
*/
				$qidEleExis = $db->sql_query("
						SELECT x.cantidad, ex.precio
						FROM mtto.ordenes_trabajo_elementos x
						LEFT JOIN mtto.elementos e ON e.id=x.id_elemento 
						LEFT JOIN mtto.elementos_existencias ex ON ex.id_elemento=e.id
						WHERE x.id_orden_trabajo='".$qidOT["id_orden"]."'");
				while($ele =  $db->sql_fetchrow($qidEleExis)) 
				{
					$total = $ele["precio"]*$ele["cantidad"];
					$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["valor"]+=$total;
				}	
			}
		}

		include("templates/reportes_html.php");
	}elseif($frm["tipo"] == "por_sistema")
	{
		$titulo = "POR SISTEMA";
		$tituloDos = "SISTEMA";

		$qid = $db->sql_query("SELECT * FROM mtto.sistemas ORDER BY sistema");
		while($query = $db->sql_fetchrow($qid))
		{
			$nombre = $query["sistema"];
			$queryOT = $db->sql_query("SELECT r.id_tipo_mantenimiento, r.id as id_rutina, o.id as id_orden, c.id as id_centro
					FROM mtto.ordenes_trabajo o
					LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
					LEFT JOIN mtto.equipos e ON o.id_equipo=e.id
					LEFT JOIN centros c ON c.id=e.id_centro
					WHERE ".$condicion." AND r.id_sistema=".$query["id"]." AND id_estado_orden_trabajo IN (SELECT id FROM mtto.estados_ordenes_trabajo WHERE cerrado) AND o.fecha_ejecucion_inicio>='".$frm["fecha_inicio"]." 00:00:00' AND o.fecha_ejecucion_fin<='".$frm["fecha_fin"]." 23:59:59'");
			while($qidOT = $db->sql_fetchrow($queryOT))
			{
				if(!isset($datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["numero"]))
					$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["numero"]=0;
				if(!isset($datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["valor"]))
					$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["valor"] = 0;
				$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["numero"]+=1;

				//personas
				$qidPer=$db->sql_query("SELECT oc.tiempo as tiempo_ejecucion, c.valor
						FROM mtto.ordenes_trabajo_actividades_cargos oc
						LEFT JOIN mtto.ordenes_trabajo_actividades a ON a.id=oc.id_orden_trabajo_actividad
						LEFT JOIN cargos c ON c.id=oc.id_cargo
						WHERE a.id_orden_trabajo='".$qidOT["id_orden"]."'");
				while($quePer = $db->sql_fetchrow($qidPer))
				{
					$total = ($quePer["tiempo_ejecucion"]*$quePer["valor"])/60;
					$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["valor"]+=$total;
				}

				//elementos
				$qidEleExis = $db->sql_query("
						SELECT x.cantidad, ex.precio
						FROM mtto.ordenes_trabajo_elementos x
						LEFT JOIN mtto.elementos e ON e.id=x.id_elemento 
						LEFT JOIN mtto.elementos_existencias ex ON ex.id_elemento=e.id AND ex.id_centro='".$qidOT["id_centro"]."'
						WHERE x.id_orden_trabajo='".$qidOT["id_orden"]."'");
				while($ele =  $db->sql_fetchrow($qidEleExis)) 
				{
					$total = $ele["precio"]*$ele["cantidad"];
					$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["valor"]+=$total;
				}	
			}
		}
		include("templates/reportes_html.php");
	}elseif($frm["tipo"] == "por_centro")
	{
		$titulo = "POR CENTRO";
		$tituloDos = "CENTRO";

		$qid = $db->sql_query("SELECT c.* FROM centros c WHERE ".$condicion." ORDER BY centro");
		while($query = $db->sql_fetchrow($qid))
		{
			$nombre = $query["centro"];
			$queryOT = $db->sql_query("SELECT r.id_tipo_mantenimiento, r.id as id_rutina, o.id as id_orden, c.id as id_centro
					FROM mtto.ordenes_trabajo o
					LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
					LEFT JOIN mtto.equipos e ON o.id_equipo=e.id
					LEFT JOIN centros c ON c.id=e.id_centro
					WHERE e.id_centro=".$query["id"]." AND id_estado_orden_trabajo IN (SELECT id FROM mtto.estados_ordenes_trabajo WHERE cerrado) AND o.fecha_ejecucion_inicio>='".$frm["fecha_inicio"]." 00:00:00' AND o.fecha_ejecucion_fin<='".$frm["fecha_fin"]." 23:59:59'");
			while($qidOT = $db->sql_fetchrow($queryOT))
			{
				if(!isset($datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["numero"]))
					$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["numero"]=0;
				if(!isset($datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["valor"]))
					$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["valor"] = 0;
				$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["numero"]+=1;

				//personas
				$qidPer=$db->sql_query("SELECT oc.tiempo as tiempo_ejecucion, c.valor
						FROM mtto.ordenes_trabajo_actividades_cargos oc
						LEFT JOIN mtto.ordenes_trabajo_actividades a ON a.id=oc.id_orden_trabajo_actividad
						LEFT JOIN cargos c ON c.id=oc.id_cargo
						WHERE a.id_orden_trabajo='".$qidOT["id_orden"]."'");
				while($quePer = $db->sql_fetchrow($qidPer))
				{
					$total = ($quePer["tiempo_ejecucion"]*$quePer["valor"])/60;
					$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["valor"]+=$total;
				}

				//elementos
				$qidEleExis = $db->sql_query("
						SELECT x.cantidad, ex.precio
						FROM mtto.ordenes_trabajo_elementos x
						LEFT JOIN mtto.elementos e ON e.id=x.id_elemento 
						LEFT JOIN mtto.elementos_existencias ex ON ex.id_elemento=e.id AND ex.id_centro='".$qidOT["id_centro"]."'
						WHERE x.id_orden_trabajo='".$qidOT["id_orden"]."'");
				while($ele =  $db->sql_fetchrow($qidEleExis)) 
				{
					$total = $ele["precio"]*$ele["cantidad"];
					$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["valor"]+=$total;
				}	
			}
		}
		include("templates/reportes_html.php");
	}elseif($frm["tipo"] == "por_lugar")
	{
		$titulo = "POR LUGAR";
		$tituloDos = "LUGAR";

		$lugares = array("Interno","Externo");
		foreach($lugares as $sitio)
		{
			$nombre = $sitio;
			$queryOT = $db->sql_query("SELECT r.id_tipo_mantenimiento, r.id as id_rutina, o.id as id_orden, c.id as id_centro
					FROM mtto.ordenes_trabajo o
					LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
					LEFT JOIN mtto.equipos e ON o.id_equipo=e.id
					LEFT JOIN centros c ON c.id=e.id_centro
					WHERE r.lugar='".$sitio."' AND id_estado_orden_trabajo IN (SELECT id FROM mtto.estados_ordenes_trabajo WHERE cerrado) AND o.fecha_ejecucion_inicio>='".$frm["fecha_inicio"]." 00:00:00' AND o.fecha_ejecucion_fin<='".$frm["fecha_fin"]." 23:59:59'");
			while($qidOT = $db->sql_fetchrow($queryOT))
			{
				if(!isset($datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["numero"]))
					$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["numero"]=0;
				if(!isset($datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["valor"]))
					$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["valor"] = 0;
				$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["numero"]+=1;

				//personas
				$qidPer=$db->sql_query("SELECT oc.tiempo as tiempo_ejecucion, c.valor
						FROM mtto.ordenes_trabajo_actividades_cargos oc
						LEFT JOIN mtto.ordenes_trabajo_actividades a ON a.id=oc.id_orden_trabajo_actividad
						LEFT JOIN cargos c ON c.id=oc.id_cargo
						WHERE a.id_orden_trabajo='".$qidOT["id_orden"]."'");
				while($quePer = $db->sql_fetchrow($qidPer))
				{
					$total = ($quePer["tiempo_ejecucion"]*$quePer["valor"])/60;
					$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["valor"]+=$total;
				}

				/*
				//elementos
				$qidEleExis = $db->sql_query("
						SELECT x.cantidad, ex.precio
						FROM mtto.ordenes_trabajo_elementos x
						LEFT JOIN mtto.elementos e ON e.id=x.id_elemento 
						LEFT JOIN mtto.elementos_existencias ex ON ex.id_elemento=e.id AND ex.id_centro='".$qidOT["id_centro"]."'
						WHERE x.id_orden_trabajo='".$qidOT["id_orden"]."'");
				while($ele =  $db->sql_fetchrow($qidEleExis)) 
				{
					$total = $ele["precio"]*$ele["cantidad"];
					$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["valor"]+=$total;
				}	
				*/
			}
		}
		include("templates/reportes_html.php");
	}elseif($frm["tipo"] == "por_tipo")
	{
		$titulo = "POR TIPO";
		$tituloDos = "TIPO";

		$qid = $db->sql_query("SELECT * FROM mtto.tipos ORDER BY tipo");
		while($query = $db->sql_fetchrow($qid))
		{
			$nombre = $query["tipo"];
			$queryOT = $db->sql_query("SELECT r.id_tipo_mantenimiento, r.id as id_rutina, o.id as id_orden, c.id as id_centro
					FROM mtto.ordenes_trabajo o
					LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
					LEFT JOIN mtto.equipos e ON o.id_equipo=e.id
					LEFT JOIN centros c ON c.id=e.id_centro
					WHERE r.id_tipo_mantenimiento=".$query["id"]." AND id_estado_orden_trabajo IN (SELECT id FROM mtto.estados_ordenes_trabajo WHERE cerrado) AND o.fecha_ejecucion_inicio>='".$frm["fecha_inicio"]." 00:00:00' AND o.fecha_ejecucion_fin<='".$frm["fecha_fin"]." 23:59:59'");
			while($qidOT = $db->sql_fetchrow($queryOT))
			{
				if(!isset($datos[$qidOT["id_tipo_mantenimiento"]]["numero"]))
					$datos[$qidOT["id_tipo_mantenimiento"]]["numero"]=0;
				if(!isset($datos[$qidOT["id_tipo_mantenimiento"]]["valor"]))
					$datos[$qidOT["id_tipo_mantenimiento"]]["valor"] = 0;
				$datos[$qidOT["id_tipo_mantenimiento"]]["numero"]+=1;

				//personas
				$qidPer=$db->sql_query("SELECT oc.tiempo as tiempo_ejecucion, c.valor
						FROM mtto.ordenes_trabajo_actividades_cargos oc
						LEFT JOIN mtto.ordenes_trabajo_actividades a ON a.id=oc.id_orden_trabajo_actividad
						LEFT JOIN cargos c ON c.id=oc.id_cargo
						WHERE a.id_orden_trabajo='".$qidOT["id_orden"]."'");
				while($quePer = $db->sql_fetchrow($qidPer))
				{
					$total = ($quePer["tiempo_ejecucion"]*$quePer["valor"])/60;
					$datos[$qidOT["id_tipo_mantenimiento"]]["valor"]+=$total;
				}

				/*
				//elementos
				$qidEleExis = $db->sql_query("
						SELECT x.cantidad, ex.precio
						FROM mtto.ordenes_trabajo_elementos x
						LEFT JOIN mtto.elementos e ON e.id=x.id_elemento 
						LEFT JOIN mtto.elementos_existencias ex ON ex.id_elemento=e.id AND ex.id_centro='".$qidOT["id_centro"]."'
						WHERE x.id_orden_trabajo='".$qidOT["id_orden"]."'");
				while($ele =  $db->sql_fetchrow($qidEleExis)) 
				{
					$total = $ele["precio"]*$ele["cantidad"];
					$datos[$qidOT["id_tipo_mantenimiento"]]["valor"]+=$total;
				}	
				*/
			}
		}

		include("templates/reportes_html.php");
	}elseif($frm["tipo"] == "por_grupo")
	{
		$titulo = "POR GRUPO";
		$tituloDos = "GRUPO";

		$qid = $db->sql_query("SELECT c.id, getPath(c.id,'mtto.grupos') as grup FROM mtto.grupos c WHERE ".$condicion." OR c.id_centro IS NULL ORDER BY nombre");
		while($query = $db->sql_fetchrow($qid))
		{
			$nombre = $query["grup"];
			$queryOT = $db->sql_query("SELECT r.id_tipo_mantenimiento, r.id as id_rutina, o.id as id_orden, c.id as id_centro
					FROM mtto.ordenes_trabajo o
					LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
					LEFT JOIN mtto.equipos e ON o.id_equipo=e.id
					LEFT JOIN centros c ON c.id=e.id_centro
					WHERE r.id_grupo=".$query["id"]." AND id_estado_orden_trabajo IN (SELECT id FROM mtto.estados_ordenes_trabajo WHERE cerrado) AND o.fecha_ejecucion_inicio>='".$frm["fecha_inicio"]." 00:00:00' AND o.fecha_ejecucion_fin<='".$frm["fecha_fin"]." 23:59:59'");
			while($qidOT = $db->sql_fetchrow($queryOT))
			{
				if(!isset($datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["numero"]))
					$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["numero"]=0;
				if(!isset($datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["valor"]))
					$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["valor"] = 0;
				$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["numero"]+=1;

				//personas
				$qidPer=$db->sql_query("SELECT oc.tiempo as tiempo_ejecucion, c.valor
						FROM mtto.ordenes_trabajo_actividades_cargos oc
						LEFT JOIN mtto.ordenes_trabajo_actividades a ON a.id=oc.id_orden_trabajo_actividad
						LEFT JOIN cargos c ON c.id=oc.id_cargo
						WHERE a.id_orden_trabajo='".$qidOT["id_orden"]."'");
				while($quePer = $db->sql_fetchrow($qidPer))
				{
					$total = ($quePer["tiempo_ejecucion"]*$quePer["valor"])/60;
					$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["valor"]+=$total;
				}

				/*
				//elementos
				$qidEleExis = $db->sql_query("
						SELECT x.cantidad, ex.precio
						FROM mtto.ordenes_trabajo_elementos x
						LEFT JOIN mtto.elementos e ON e.id=x.id_elemento 
						LEFT JOIN mtto.elementos_existencias ex ON ex.id_elemento=e.id AND ex.id_centro='".$qidOT["id_centro"]."'
						WHERE x.id_orden_trabajo='".$qidOT["id_orden"]."'");
				while($ele =  $db->sql_fetchrow($qidEleExis)) 
				{
					$total = $ele["precio"]*$ele["cantidad"];
					$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["valor"]+=$total;
				}	
				*/
			}
		}
		include("templates/reportes_html.php");
	}





}


function imprimirForm(){

	global $db, $CFG, $ME;

	$fechaUno = date("Y-m-01");
?>
<form name="entryform" action="<?=$ME?>" method="POST" onSubmit="return revisar()" class="form">
<input type="hidden" name="mode" value="reporte">

<table width="100%">
	<tr>
		<td height="40" colspan=3 class="azul_16"><strong>REPORTES</strong></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840">
							<tr>
								<td align='right'>Fecha Inicio</td>
								<td align='left'><input type='text' size='20' class="casillatext_fecha" name='fecha_inicio' value='<?=$fechaUno?>' readonly>&nbsp;<a title="Calendario" href="javascript:abrir('fecha_inicio','entryform');"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></a></td>
							</tr>
							<tr>
								<td align='right'>Fecha Fin</td>
								<td align='left'><input type='text' size='20' class="casillatext_fecha" name='fecha_fin' value='<?=date("Y-m-d")?>' readonly>&nbsp;<a title="Calendario" href="javascript:abrir('fecha_fin','entryform');"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></a></td>
							</tr>
							<tr>
								<td align='right'>Tipo</td>
								<td align='left'>
									<select  name='tipo'>
										<option value='%'>Seleccione...</option>
										<option value='por_equipo'>Por Equipo</option>
										<option value='por_sistema'>Por Sistema</option>
										<option value='por_centro'>Por Centro</option>
										<option value='por_lugar'>Por Lugar</option>
										<option value='por_tipo'>Por Tipo mantenimiento</option>
										<option value='por_grupo'>Por Grupo</option>
									</select>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center">
			<input type="submit" class="boton_verde" value="Aceptar"/>
			<input type="button" class="boton_verde" value="Cancelar" onclick="window.close()"/>
		</td>
	</tr>
</table>
</form>

<script type="text/javascript">

function revisar()
{
	if(document.entryform.fecha_inicio.value.replace(/ /g, '') ==''){
		window.alert('Por favor seleccione: Fecha Inicio');
		document.entryform.fecha_inicio.focus();
		return(false);
	}
	else{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2}/;
		if(!regexpression.test(document.entryform.fecha_inicio.value)){
			window.alert('[Fecha Inicio] no contiene un dato válido.');
			document.entryform.fecha_inicio.focus();
			return(false);
		}
	}

	if(document.entryform.fecha_fin.value.replace(/ /g, '') ==''){
		window.alert('Por favor seleccione: Fecha Fin');
		document.entryform.fecha_fin.focus();
		return(false);
	}
	else{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2}/;
		if(!regexpression.test(document.entryform.fecha_fin.value)){
			window.alert('[Fecha Fin] no contiene un dato válido.');
			document.entryform.fecha_fin.focus();
			return(false);
		}
	}

	if(document.entryform.fecha_inicio.value > document.entryform.fecha_fin.value){
		window.alert('La fecha de inicio no puede ser mayor que la fecha fin');
		document.entryform.fecha_inicio.focus();
		return(false);
	}

	if(document.entryform.tipo.options[document.entryform.tipo.selectedIndex].value=='%'){
		window.alert('Por favor seleccione el tipo de informe');
		document.entryform.tipo.focus();
		return(false);
	}

	return true;
}

</script>

<?include($CFG->dirroot."/templates/footer_popup.php");
}?>


