<?
// opera : Producción por Ases
include("../application.php");
$html = true;
$user=$_SESSION[$CFG->sesion]["user"];

$titulos = array("MENSUAL");
$qidCentro = $db->sql_query("SELECT id_centro, centro FROM personas_centros LEFT JOIN centros ON centros.id=personas_centros.id_centro WHERE id_persona='".$user["id"]."' ORDER BY id_centro");
while($queryCen = $db->sql_fetchrow($qidCentro)){
	$centros[$queryCen["id_centro"]] = $queryCen["centro"];
	$titulos[] = $queryCen["centro"]." Promedio";
}
$titulos[] = "Consolidados Promedios";

if(isset($_GET["format"])) 
{
	$html=false;
	$inicio = $_GET["inicio"];
	$final = $_GET["final"];
}

$titulo1 = $db->sql_row("SELECT upper(nombre||' : '||informe) as inf FROM informes i LEFT JOIN categorias_informes c ON c.id=i.id_categoria_informe WHERE i.id=".str_replace(".php","",simple_me($ME)));

if($html)
{
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/info/templates/fechas_form.php");
	tablita_titulos($titulo1["inf"],$inicio." / ".$final);
}else{
	require_once $CFG->common_libdir."/writeexcel/class.writeexcel_workbook.inc.php";
	require_once $CFG->common_libdir."/writeexcel/class.writeexcel_worksheet.inc.php";

	$fname=$CFG->tmpdir."/informe.xls";
	if(file_exists($fname))
		unlink($fname);

	$workbook = new writeexcel_workbook($fname);
	$workbook->set_tempdir($CFG->tmpdir);
	$worksheet = &$workbook->addworksheet("reporte");
	$worksheet->set_zoom(80);
	titulo_grande_xls($workbook, $worksheet, 0, 11, $titulo1["inf"]."\n".$inicio." / ".$final);
	$fila=2; $columna=0;
}

$funo = $inicio;
$fechasTemp = array();

while($funo<= $final)
{
	$fechasTemp[strftime("%Y-%m",strtotime($funo))][strftime("%d",strtotime($funo))] = strftime("%d",strtotime($funo));
	list($anio,$mes,$dia)= split("-",$funo);
	$funo = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + 1 * 24 * 60 * 60);
}

foreach($fechasTemp as $mes => $dias)
{
	$di = array_shift($dias);
	$df = array_pop($dias);
	$fechas[] = array("ini"=>$mes."-".$di, "fin"=>$mes."-".$df);
}

$colspan = count($titulos)+1;
if($html)
{
	echo '
		<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla"  align="center">
			<tr>';
		foreach($titulos as $tt)
			echo '<th height="40">'.$tt.'</th>';
		echo '</tr>';

		echo '<tr><td  height="30" colspan="'.$colspan.'" align="left"><strong>INDICADORES OPERACIONALES</strong></td></tr>';
		imprimirLinea(array("RECOLECCIÓN"), "#b2d2e1", array(1=>"colspan=".$colspan." align='left' height='30' strong"));
}else
{
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, $titulos);
	$fila++;$columna=0;
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array("INDICADORES OPERACIONALES"),array(1=>"azul_izq"), count($titulos)-1);
	$fila++;$columna=0;
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array("RECOLECCIÓN"),array(1=>"azul_izq"), count($titulos)-1);
	$fila++;$columna=0;
}	

$tiposVehiculos = $capacidad = $recorrido = $combustible = $tonTipo = $viajes = array();
$lineaTon = array("Toneladas recogidas");
$lineaCosTon = array("Costo/Ton Recogida y transportada");
$lineaPers = array("Toneladas recogidas por  tripulacion");
$lineaLl = array("Costo de llantas por km recorrido ($/Km)");
foreach($fechas as $da)
{
	$ton = $per = $kmsR = $costoLlan = 0;
	
	
	//here
	movimientosCostos($da["ini"], $da["fin"], $centro, "rec", $tiposVehiculos, $capacidad, $ton, $per, $kmsR, $costoLlan, $recorrido, $combustible, $tonTipo, $viajes);

	$lineaTon[] = $ton;

	//costoTon recogida y tranportada
	$qidCTRP = $db->sql_row("SELECT sum(c.valor) as val FROM costos c LEFT JOIN servicios s ON s.id=c.id_servicio WHERE id_centro='".$centro."' AND esquema='rec' AND id_variable_informe = 1 AND fecha = '".strftime("%Y-%m",strtotime($da["ini"]))."'");
	$lineaCosTon[] = number_format($qidCTRP["val"]/$ton,2);
	@$lineaPers[] = number_format($ton/$per,2);
	@$lineaLl[] = $costoLlan/$kmsR;;



}

if($html)
{
	imprimirLinea($lineaTon,"",array(1=>"align='left'"));
	imprimirLinea($lineaCosTon,"",array(1=>"align='left'"));
	imprimirLinea($lineaPers,"",array(1=>"align='left'"));
	imprimirLinea($lineaLl,"",array(1=>"align='left'"));
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaTon, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaCosTon, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaPers, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaLl, array(1=>"txt_izq"));
}



die;
imprimirKmgl($fila, $columna, $html, $tiposVehiculos, $fechas, $recorrido, $combustible);
imprimirEficiencia($fila, $columna, $html, $tiposVehiculos, $fechas, $tonTipo, $viajes, $capacidad);

function imprimirEficiencia($fila, $columna, $html, $tiposVehiculos, $fechas, $tonTipo, $viajes, $capacidad)
{
	global $workbook;
	global $worksheet;

	$eficiencia = array();
	foreach($tiposVehiculos as $id => $tipo)
	{
		@$linea = array("Ton/Viaje ".$tipo);
		$linea2 = array("Eficiencia %");
		foreach($fechas as $da)
		{
			$linea[] =  number_format(nvl($tonTipo[$da["ini"]][$id],0)/nvl($viajes[$da["ini"]][$id],0),2);
			$linea2[] = number_format(((nvl($tonTipo[$da["ini"]][$id],0)/nvl($viajes[$da["ini"]][$id],0)) / $capacidad[$id]) * 100,2)."%";
			$eficiencia[$da["ini"]][$id] = (nvl($tonTipo[$da["ini"]][$id],0)/nvl($viajes[$da["ini"]][$id],0)) / $capacidad[$id];
		}
		
		if($html)
		{
			imprimirLinea($linea,"",array(1=>"align='left'"));
			imprimirLinea($linea2,"",array(1=>"align='left'"));
		}
		else
		{
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea2, array(1=>"txt_izq"));
		}
	}

	$linea = array("Eficiencia  % promedio flota");
	foreach($fechas as $da)
		$linea[]=number_format( ((array_sum(nvl($eficiencia[$da["ini"]],0)) / count(nvl($eficiencia[$da["ini"]],0)))*100),2)."%";
	if($html)
		imprimirLinea($linea,"",array(1=>"align='left'"));
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));

}


if($html)
	imprimirLinea(array("BARRIDO"), "#b2d2e1", array(1=>"colspan=".$colspan." align='left' height='30' strong"));
else
{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array("BARRIDO"), array(1=>"azul_izq"),$colspan-2);
	$fila++;$columna=0;
}



/*
//barrido
$linea = array("Km barridos");
$linea2 = array("Costo / Km Barrido");
$linea3 = array("Km barridos por operario");
$linea4= array("Bolsas por Operario de Barrido");

$tiposVehiculos = $capacidad = $recorrido = $combustible = $tonTipo = $viajes = array();
foreach($fechas as $da)
{
	$vars = array();
	$qid3 = $db->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c LEFT JOIN servicios s ON s.id=c.id_servicio WHERE id_centro='".$centro."' AND esquema='bar' AND id_variable_informe IN (2,3,4,5) AND fecha = '".strftime("%Y-%m",strtotime($da["ini"]))."' 	GROUP BY id_variable_informe");
	while($query = $db->sql_fetchrow($qid3))
	{
		$vars[$query["id_variable_informe"]] = $query["val"];
	}

	movimientosCostos($da["ini"], $da["fin"], $centro, "bar", $tiposVehiculos, $capacidad, $ton, $per, $kmsR, $costoLlan, $recorrido, $combustible, $tonTipo, $viajes);

	$linea[] = number_format(nvl($vars[2],0),2);
	@$linea2[] = number_format(nvl($vars[3],0)/nvl($vars[3],0),2);
	$linea3[] = number_format(nvl($vars[4],0),2);
	$linea4[] = number_format(nvl($vars[5],0),2);
}

if($html)
{
	imprimirLinea($linea,"",array(1=>"align='left'"));
	imprimirLinea($linea2,"",array(1=>"align='left'"));
	imprimirLinea($linea3,"",array(1=>"align='left'"));
	imprimirLinea($linea4,"",array(1=>"align='left'"));
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea2, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea3, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea4, array(1=>"txt_izq"));
}

imprimirKmgl($fila, $columna, $html, $tiposVehiculos, $fechas, $recorrido, $combustible);
imprimirEficiencia($fila, $columna, $html, $tiposVehiculos, $fechas, $tonTipo, $viajes, $capacidad);


//generales
if($html)
	imprimirLinea(array("GENERALES"), "#b2d2e1", array(1=>"colspan=".$colspan." align='left' height='30' strong"));
else
{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array("GENERALES"), array(1=>"azul_izq"),$colspan-2);
	$fila++;$columna=0;
}

$linea = array("Costos Mtto / Costos totales");
$linea2 = array("Gastos Adtivos / Ventas Totales");
$linea3 = array("N° trabajadores / 1.000 usuarios");
$linea4 = array("N° total de empleados");
$otrasLineas = array();

$qidOV = $db->sql_query("SELECT * FROM variables_informes WHERE id >=19 ORDER BY id");
while($queryOV = $db->sql_fetchrow($qidOV))
{
	$otrasLineas[$queryOV["id"]][] = $queryOV["variable"];
}

foreach($fechas as $da)
{
	$vars = array();
	$qid3 = $db->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c LEFT JOIN servicios s ON s.id=c.id_servicio WHERE id_centro='".$centro."' AND id_variable_informe >= 10 AND fecha = '".strftime("%Y-%m",strtotime($da["ini"]))."' GROUP BY id_variable_informe");
	while($query = $db->sql_fetchrow($qid3))
	{
		$vars[$query["id_variable_informe"]] = $query["val"];
		if(isset($otrasLineas[$query["id_variable_informe"]]))
			$otrasLineas[$query["id_variable_informe"]][$da["ini"]] = $query["val"];
	}

	@$linea[] = number_format(nvl($vars[10],0)/nvl($vars[11],0),2)."%";
	@$linea2[] = number_format(nvl($vars[12],0)/(nvl($vars[13],0)+nvl($vars[14],0)+nvl($vars[15],0)),2);
	@$linea3[] = number_format((nvl($vars[16],0)/nvl($vars[17],0))*1000,2);
	@$linea4[] = $vars[17];
}

if($html)
{
	imprimirLinea($linea,"",array(1=>"align='left'"));
	imprimirLinea($linea2,"",array(1=>"align='left'"));
	imprimirLinea($linea3,"",array(1=>"align='left'"));
	imprimirLinea($linea4,"",array(1=>"align='left'"));
	foreach($otrasLineas as $var)
	{
		$linea = array(array_shift($var));
		foreach($fechas as $da)
		{
			if(isset($var[$da["ini"]]))
				$linea[] = number_format($var[$da["ini"]],2);
			else
				$linea[] = "&nbsp;";
		}
		imprimirLinea($linea,"",array(1=>"align='left'"));
	}


}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea2, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea3, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea4, array(1=>"txt_izq"));
}

*/



function movimientosCostos($inicio, $final, $centro, $tipo, &$tiposVehiculos, &$capacidad, &$ton, &$per, &$kmsR, &$costoLlan, &$recorrido, &$combustible, &$tonTipo, &$viajes)
{
	global $db, $CFG, $ME;
	
	$strQuery="SELECT m.*, i.codigo, t.tipo, v.id_tipo_vehiculo, t.capacidad
				FROM rec.movimientos m 
				LEFT JOIN vehiculos v ON v.id=m.id_vehiculo
				LEFT JOIN tipos_vehiculos t ON t.id = v.id_tipo_vehiculo
				LEFT JOIN micros i ON i.id=m.id_micro
				LEFT JOIN servicios s ON s.id = i.id_servicio
				LEFT JOIN ases a ON a.id = i.id_ase
				WHERE a.id_centro='".$centro."' AND s.inf_indope='".$tipo."' AND inicio::date >= '".$inicio."' AND inicio::date<='".$final."'";
	$qidMov = $db->sql_query($strQuery);
	while($mov = $db->sql_fetchrow($qidMov))
	{
		$tiposVehiculos[$mov["id_tipo_vehiculo"]] = $mov["tipo"];
		$capacidad[$mov["id_tipo_vehiculo"]] = $mov["capacidad"];
		
		//pesos
		$tonMov = averiguarPesoXMov($mov["id"],"",true);
		$ton+= $tonMov;
		if(!isset($tonTipo[$inicio][$mov["id_tipo_vehiculo"]])) $tonTipo[$inicio][$mov["id_tipo_vehiculo"]]=0;
		$tonTipo[$inicio][$mov["id_tipo_vehiculo"]]+=$tonMov;

		$qidPer = $db->sql_row("SELECT count(id) as num FROM rec.movimientos_personas WHERE id_movimiento=".$mov["id"]);
		$per+=$qidPer["num"];

		//costo de llantas por km recorrido
		//echo "SELECT id, costo FROM llta.llantas WHERE id_vehiculo='".$mov["id_vehiculo"]."'<br>";
		$qidLlta = $db->sql_query("SELECT id, costo FROM llta.llantas WHERE id_vehiculo='".$mov["id_vehiculo"]."'");
		while($llta = $db->sql_fetchrow($qidLlta))
		{
			$costoLlan+=$llta["costo"];
			$mov = $db->sql_row("SELECT id,km, posicion FROM llta.movimientos WHERE id_llanta='".$llta["id"]."' AND id_tipo_movimiento=5");
			$kmsR += $veh["km"]-$mov["km"];
		}

		//combustible
		if(!isset($combustible[$inicio][$mov["id_tipo_vehiculo"]])) $combustible[$inicio][$mov["id_tipo_vehiculo"]]=0;
		if($mov["combustible"] != "")
			$combustible[$inicio][$mov["id_tipo_vehiculo"]]+=$mov["combustible"];

		//recorrido
		$rec = kmsRecorridoPorMov($mov["id"]);
		$kmini = $db->sql_row("SELECT km FROM rec.desplazamientos WHERE id_movimiento=".$mov["id"]." ORDER BY hora_inicio LIMIT 1");
		if(nvl($kmini["km"],0)==0)
			$rec="0";
		if(!isset($recorrido[$inicio][$mov["id_tipo_vehiculo"]])) $recorrido[$inicio][$mov["id_tipo_vehiculo"]]=0;
		$recorrido[$inicio][$mov["id_tipo_vehiculo"]]+=$rec;

		//viajes
		if(!isset($viajes[$inicio][$mov["id_tipo_vehiculo"]])) $viajes[$inicio][$mov["id_tipo_vehiculo"]]=0;
		$viajes[$inicio][$mov["id_tipo_vehiculo"]] +=averiguarViajeXMov($mov["id"]);
	}


}

function imprimirKmgl(&$fila, &$columna, $html, $tiposVehiculos, $fechas, $recorrido, $combustible)
{
	global $workbook;
	global $worksheet;

	foreach($tiposVehiculos as $id => $tipo)
	{
		$linea = array("Km/gl ".$tipo);
		foreach($fechas as $da)
			@$linea[] =  number_format(nvl($recorrido[$da["ini"]][$id],0)/nvl($combustible[$da["ini"]][$id],0),2);
		
		if($html)
			imprimirLinea($linea,"",array(1=>"align='left'"));
		else
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
	}
}


//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro;
	echo "</table><br /><br />";
	echo "
	<table width='98%' align='center'>
		<tr>
			<td height='50' valign='bottom' align='right'><input type='button' class='boton_verde' value='Bajar en xls' onclick=\"window.location.href='".$ME.$link."'\"/></td>
		</tr>
	</table>
	";
}
else
{
	$workbook->close();
	$nombreArchivo=preg_replace("/[^0-9a-z_.]/i","_",$titulo1["inf"])."_".$inicio."_".$final.".xls";
	header("Content-Type: application/x-msexcel; name=\"".$nombreArchivo."\"");
	header("Content-Disposition: inline; filename=\"".$nombreArchivo."\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
}

?>
