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

	$user=$_SESSION[$CFG->sesion]["user"];
	
	$datos = $cols = array();
	$titulo="";
	if($frm["tipo"] == "cumplimiento_preventivos")
	{
		$titulo = "CUMPLIMIENTO DE MANTENIMIENTOS PREVENTIVOS";
		$cols = array("Centro","Meta","Valor");
		cumplimiento_preventivos($frm,$datos);
	}elseif($frm["tipo"] == "costos_mantenimientoxvehiculo")
	{
		$titulo = "COSTOS MANTENIMIENTO POR VEHÍCULO";
		$cols = array("Equipo/Centro","Meta","Valor");
		costos_mantenimientoxvehiculo($frm,$datos);
	}elseif($frm["tipo"] == "costos_mantenimientoxcentro")
	{
		$titulo = "COSTOS MANTENIMIENTO POR CENTRO";
		$cols = array("Centro","Meta","Valor");
		costos_mantenimientoxcentro($frm,$datos);
	}

	?>
	<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
		<tr>
			<td height="40" colspan=3 align="center"><span class="azul_16"><strong><?=$titulo?></strong></span></td>
		</tr>
		<tr>
			<td align="center">
				<table width="98%" border=1 bordercolor="#7fa840" id="tabla_actividades">
					<tr>
						<?foreach($cols as $dx){
						echo "<td align=\"center\"><strong>".strtoupper($dx)."</strong></td>";
						}?>
					</tr>
					<?foreach($datos as $linea)
					{
						echo "<tr>";
						foreach($linea as $dx){
							echo "<td>".$dx."</td>";
						}
						echo "</tr>";
					}
					?>
				</table>
			</td>
		</tr>
	</table>
<?
}


function imprimirForm(){

	global $db, $CFG, $ME;

	$days_of_month = date('t',strtotime(date("Y-m-01", strtotime("last month"))));
	$fechaUno=date("Y-m-01", strtotime("last month"));
	$fechaDos=date("Y-m-".$days_of_month,strtotime("last month"));

?>
<form name="entryform" action="<?=$ME?>" method="POST" onSubmit="return revisar()" class="form">
<input type="hidden" name="mode" value="reporte">

<table width="100%">
	<tr>
		<td height="40" colspan=3 class="azul_16"><strong>INDICADORES MANTENIMIENTO</strong></td>
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
								<td align='left'><input type='text' size='20' class="casillatext_fecha" name='fecha_fin' value='<?=$fechaDos?>' readonly>&nbsp;<a title="Calendario" href="javascript:abrir('fecha_fin','entryform');"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></a></td>
							</tr>
							<tr>
								<td align='right'>Indicador</td>
								<td align='left'>
									<select  name='tipo'>
										<option value='%'>Seleccione...</option>
										<option value='cumplimiento_preventivos'>Cumplimiento de Mantenimientos Preventivos</option>
										<option value='costos_mantenimientoxvehiculo'>Costos Mantenimiento por Vehículo</option>
										<option value='costos_mantenimientoxcentro'>Costos Mantenimiento por Centro</option>
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
}

function cumplimiento_preventivos($frm,&$datos)
{
	global $db, $CFG;

	$condicion="true";
	$user=$_SESSION[$CFG->sesion]["user"];
	if($user["nivel_acceso"]!=1)
		$condicion="id IN (" . implode(",",$user["id_centro"]) . ")";

	$qidC = $db->sql_query("SELECT id,centro FROM centros WHERE ".$condicion." ORDER BY centro");
	while($centro = $db->sql_fetchrow($qidC))
	{
		$realizados = $db->sql_row("SELECT count(o.id) as num 
				FROM mtto.ordenes_trabajo o 
				LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina 
				WHERE o.fecha_ejecucion_inicio>='".$frm["fecha_inicio"]." 00:00:00' AND o.fecha_ejecucion_fin<='".$frm["fecha_fin"]." 23:59:59' AND r.id_centro='".$centro["id"]."' AND o.id_estado_orden_trabajo IN (SELECT id FROM mtto.estados_ordenes_trabajo WHERE cerrado AND indicadores)");
		$programados = $db->sql_row("SELECT count(o.id) as num
				FROM mtto.ordenes_trabajo o
				LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
				WHERE o.fecha_planeada>='".$frm["fecha_inicio"]." 00:00:00' AND o.fecha_planeada<='".$frm["fecha_fin"]." 23:59:59' AND r.id_centro='".$centro["id"]."'");
		@$total = $realizados["num"]/$programados["num"] * 100;
		$datos[] = array($centro["centro"],">= 95%",$total."%");
	}
}


function costos_mantenimientoxvehiculo($frm,&$datos)
{
	global $db, $CFG;

	$condicion="true";
	$user=$_SESSION[$CFG->sesion]["user"];
	if($user["nivel_acceso"]!=1)
		$condicion="c.id IN (" . implode(",",$user["id_centro"]) . ")";

	$equ = array();
	$qid = $db->sql_query("SELECT e.id, e.nombre, c.centro, c.id as id_centro FROM mtto.equipos e LEFT JOIN centros c ON c.id=e.id_centro WHERE ".$condicion." ORDER BY c.centro, e.nombre");
	while($query = $db->sql_fetchrow($qid))
	{
		$nombre = $query["nombre"]." / ".$query["centro"];
		if(!isset($equ[$query["id"]]))
			$equ[$query["id"]] = array("nombre"=>$nombre,"realizados"=>0,"presupuestados"=>0);

		//lo que se utilizó en la ordenes de trabajo
		$queryOT = $db->sql_query("SELECT r.id_tipo_mantenimiento, r.id as id_rutina, o.id as id_orden
				FROM mtto.ordenes_trabajo o
				LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
				WHERE o.id_equipo=".$query["id"]." AND id_estado_orden_trabajo IN (SELECT id FROM mtto.estados_ordenes_trabajo WHERE cerrado AND indicadores) 
				AND o.fecha_ejecucion_inicio>='".$frm["fecha_inicio"]." 00:00:00' AND o.fecha_ejecucion_fin<='".$frm["fecha_fin"]." 23:59:59'");
		while($qidOT = $db->sql_fetchrow($queryOT))
		{
			//personas
			$qidPer=$db->sql_query("SELECT oc.tiempo as tiempo_ejecucion, c.valor
					FROM mtto.ordenes_trabajo_actividades_cargos oc
					LEFT JOIN mtto.ordenes_trabajo_actividades a ON a.id=oc.id_orden_trabajo_actividad
					LEFT JOIN cargos c ON c.id=oc.id_cargo
					WHERE a.id_orden_trabajo='".$qidOT["id_orden"]."'");
			while($quePer = $db->sql_fetchrow($qidPer))
			{
				$total = ($quePer["tiempo_ejecucion"]*$quePer["valor"])/60;
				$equ[$query["id"]]["realizados"]+=$total;
			}

			//elementos
			$qidEleExis = $db->sql_query("
					SELECT x.cantidad, ex.precio
					FROM mtto.ordenes_trabajo_elementos x
					LEFT JOIN mtto.elementos e ON e.id=x.id_elemento 
					LEFT JOIN mtto.elementos_existencias ex ON ex.id_elemento=e.id AND ex.id_centro='".$query["id_centro"]."'
					WHERE x.id_orden_trabajo='".$qidOT["id_orden"]."'");
			while($ele =  $db->sql_fetchrow($qidEleExis)) 
			{
				$total = $ele["precio"]*$ele["cantidad"];
				$equ[$query["id"]]["realizados"]+=$total;
			} 
		}
	
		//lo que había presupuestado en la ordenes de trabajo
		$queryOT = $db->sql_query("SELECT r.id_tipo_mantenimiento, r.id as id_rutina, o.id as id_orden
				FROM mtto.ordenes_trabajo o
				LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
				WHERE o.id_equipo=".$query["id"]." AND id_estado_orden_trabajo IN (SELECT id FROM mtto.estados_ordenes_trabajo WHERE cerrado AND indicadores) 
				AND o.fecha_ejecucion_inicio>='".$frm["fecha_inicio"]." 00:00:00' AND o.fecha_ejecucion_fin<='".$frm["fecha_fin"]." 23:59:59'");
		while($qidOT = $db->sql_fetchrow($queryOT))
		{
			//personas
			$qidPer=$db->sql_query("SELECT r.tiempo, c.valor
					FROM mtto.rutinas_actividades_cargos r
					LEFT JOIN mtto.rutinas_actividades a ON a.id=r.id_actividad
					LEFT JOIN cargos c ON c.id=r.id_cargo
					WHERE a.id_rutina='".$qidOT["id_rutina"]."'");
			while($quePer = $db->sql_fetchrow($qidPer))
			{
				$total = ($quePer["tiempo"]*$quePer["valor"])/60;
				$equ[$query["id"]]["presupuestados"]+=$total;
			}

			//elementos
			$qidEleExis = $db->sql_query("
					SELECT x.cantidad, ex.precio
					FROM mtto.rutinas_elementos x
					LEFT JOIN mtto.elementos e ON e.id=x.id_elemento 
					LEFT JOIN mtto.elementos_existencias ex ON ex.id_elemento=e.id AND ex.id_centro='".$query["id_centro"]."'
					WHERE x.id_rutina='".$qidOT["id_rutina"]."'");
			while($ele =  $db->sql_fetchrow($qidEleExis)) 
			{
				$total = $ele["precio"]*$ele["cantidad"];
				$equ[$query["id"]]["presupuestados"]+=$total;
			} 
		}	
	}

	foreach($equ as $dx)
	{
		@$total = $dx["realizados"]/$dx["presupuestados"] * 100;
		$datos[] = array($dx["nombre"],"<= 100%",$total."%");
	}
}

function costos_mantenimientoxcentro($frm,&$datos)
{
	global $db, $CFG;

	$condicion="true";
	$user=$_SESSION[$CFG->sesion]["user"];
	if($user["nivel_acceso"]!=1)
		$condicion="c.id IN (" . implode(",",$user["id_centro"]) . ")";

	$centro = array();
	$qid = $db->sql_query("SELECT e.id, e.nombre, c.centro, c.id as id_centro FROM mtto.equipos e LEFT JOIN centros c ON c.id=e.id_centro WHERE ".$condicion." ORDER BY c.centro, e.nombre");
	while($query = $db->sql_fetchrow($qid))
	{
		$nombre = $query["centro"];
		if(!isset($centro[$query["id_centro"]]))
			$centro[$query["id_centro"]] = array("nombre"=>$nombre,"realizados"=>0,"presupuestados"=>0);

		//lo que se utilizó en la ordenes de trabajo
		$queryOT = $db->sql_query("SELECT r.id_tipo_mantenimiento, r.id as id_rutina, o.id as id_orden
				FROM mtto.ordenes_trabajo o
				LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
				WHERE o.id_equipo=".$query["id"]." AND id_estado_orden_trabajo IN (SELECT id FROM mtto.estados_ordenes_trabajo WHERE cerrado AND indicadores) 
				AND o.fecha_ejecucion_inicio>='".$frm["fecha_inicio"]." 00:00:00' AND o.fecha_ejecucion_fin<='".$frm["fecha_fin"]." 23:59:59'");
		while($qidOT = $db->sql_fetchrow($queryOT))
		{
			//personas
			$qidPer=$db->sql_query("SELECT oc.tiempo as tiempo_ejecucion, c.valor
					FROM mtto.ordenes_trabajo_actividades_cargos oc
					LEFT JOIN mtto.ordenes_trabajo_actividades a ON a.id=oc.id_orden_trabajo_actividad
					LEFT JOIN cargos c ON c.id=oc.id_cargo
					WHERE a.id_orden_trabajo='".$qidOT["id_orden"]."'");
			while($quePer = $db->sql_fetchrow($qidPer))
			{
				$total = ($quePer["tiempo_ejecucion"]*$quePer["valor"])/60;
				$centro[$query["id_centro"]]["realizados"]+=$total;
			}

			//elementos
			$qidEleExis = $db->sql_query("
					SELECT x.cantidad, ex.precio
					FROM mtto.ordenes_trabajo_elementos x
					LEFT JOIN mtto.elementos e ON e.id=x.id_elemento 
					LEFT JOIN mtto.elementos_existencias ex ON ex.id_elemento=e.id AND ex.id_centro='".$query["id_centro"]."'
					WHERE x.id_orden_trabajo='".$qidOT["id_orden"]."'");
			while($ele =  $db->sql_fetchrow($qidEleExis)) 
			{
				$total = $ele["precio"]*$ele["cantidad"];
				$centro[$query["id_centro"]]["realizados"]+=$total;
			} 
		}
	
		//lo que había presupuestado en la ordenes de trabajo
		$queryOT = $db->sql_query("SELECT r.id_tipo_mantenimiento, r.id as id_rutina, o.id as id_orden
				FROM mtto.ordenes_trabajo o
				LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
				WHERE o.id_equipo=".$query["id"]." AND id_estado_orden_trabajo IN (SELECT id FROM mtto.estados_ordenes_trabajo WHERE cerrado AND indicadores) 
				AND o.fecha_ejecucion_inicio>='".$frm["fecha_inicio"]." 00:00:00' AND o.fecha_ejecucion_fin<='".$frm["fecha_fin"]." 23:59:59'");
		while($qidOT = $db->sql_fetchrow($queryOT))
		{
			//personas
			$qidPer=$db->sql_query("SELECT r.tiempo, c.valor
					FROM mtto.rutinas_actividades_cargos r
					LEFT JOIN mtto.rutinas_actividades a ON a.id=r.id_actividad
					LEFT JOIN cargos c ON c.id=r.id_cargo
					WHERE a.id_rutina='".$qidOT["id_rutina"]."'");
			while($quePer = $db->sql_fetchrow($qidPer))
			{
				$total = ($quePer["tiempo"]*$quePer["valor"])/60;
				$centro[$query["id_centro"]]["presupuestados"]+=$total;
			}

			//elementos
			$qidEleExis = $db->sql_query("
					SELECT x.cantidad, ex.precio
					FROM mtto.rutinas_elementos x
					LEFT JOIN mtto.elementos e ON e.id=x.id_elemento 
					LEFT JOIN mtto.elementos_existencias ex ON ex.id_elemento=e.id AND ex.id_centro='".$query["id_centro"]."'
					WHERE x.id_rutina='".$qidOT["id_rutina"]."'");
			while($ele =  $db->sql_fetchrow($qidEleExis)) 
			{
				$total = $ele["precio"]*$ele["cantidad"];
				$centro[$query["id_centro"]]["presupuestados"]+=$total;
			} 
		}	
	}

	foreach($centro as $dx)
	{
		@$total = $dx["realizados"]/$dx["presupuestados"] * 100;
		$datos[] = array($dx["nombre"],"<= 100%",$total."%");
	}
}






?>
