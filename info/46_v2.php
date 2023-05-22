<?
// opera : Producción por Ases
include("../application.php");
if($_SERVER["REMOTE_ADDR"] != "186.30.42.202")
	die("un momento por favor");

$html = true;
$user=$_SESSION[$CFG->sesion]["user"];

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

$fechas = sacarMeses($inicio, $final);

$titulos = array();
$titulosRecoleccion = array("RECOLECCIÓN");
$estilosTitulosRecoleccion = array(1=>" height='30'  class='azul_osc_14'");
$estilos = array(1=>"align='left'  class='azul_osc_12'");
$i=2;
$qidCentro = $db->sql_query("SELECT id_centro, centro FROM personas_centros LEFT JOIN centros ON centros.id=personas_centros.id_centro WHERE id_persona='".$user["id"]."' and id_centro=3 ORDER BY id_centro");
while($queryCen = $db->sql_fetchrow($qidCentro)){
	$centros[$queryCen["id_centro"]] = $queryCen["centro"];
	$titulos[] = $queryCen["centro"]." Promedio";

	foreach($fechas as $mes)
	{
		$titulosRecoleccion[] = ucfirst(strftime("%b.%Y",strtotime($mes."-01")));
		$estilosTitulosRecoleccion[$i] = " class='azul_osc_14'";
		$estilos[$i] = "class='azul_osc_12'";
		$i++;
	}
}

$titulosRecoleccion[]="Promedio";
$estilosTitulosRecoleccion[$i] = " class='azul_osc_14'";
$estilos[$i] = "class='azul_osc_12'";

$colspan = (count($fechas) * count($centros))+2;
if($html)
{
	$titulos[] = "Consolidados Promedios";
	echo '
		<table width="98%" border=1 bordercolor="#7fa840" align="center">
			<tr>';
	echo '<th height="40" class="azul_osc_16">MENSUAL</th>';
	foreach($titulos as $tt)
		echo '<th height="40" class="azul_osc_16" colspan='.count($fechas).'>'.$tt.'</th>';
	echo '</tr>';
	echo '<tr><td  height="30" colspan="'.$colspan.'" align="left" bgcolor="#b2d2e1" class="azul_osc_14">INDICADORES OPERACIONALES</td></tr>';
	imprimirLinea($titulosRecoleccion, "#b2d2e1", $estilosTitulosRecoleccion);
}else
{
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, array("MENSUAL"));
	foreach($titulos as $tt)
	{
		titulos_uno_xls($workbook, $worksheet, $fila, $columna, array($tt), count($fechas)-1);
		$columna+=count($fechas)-1;
	}
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, array("Consolidados Promedios"));
	$fila++;$columna=0;
	
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array("INDICADORES OPERACIONALES"),array(1=>"azul_izq"), $colspan-1);
	$fila++;$columna=0;

	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosRecoleccion, array(1=>"azul_izq"));	
	$fila++;$columna=0;
}	




$tiposVehiculos = $tiposVehiculosXCentro = $capacidad = $recorrido = $combustible = $tonTipo = $viajes = $ton = $per = $dias = $kmsR = array();
$lineaTon = array("Toneladas recogidas");
$lineaCosTon = array("Costo/Ton Recogida y transportada");
$lineaPers = array("Toneladas recogidas por  tripulacion");
$lineaLl = array("Costo de llantas por km recorrido ($/Km)");
$tonTOTAL = $cosTonTOTAL = $persTOTAL = $costoLlanTOTAL = 0;
foreach($centros as $idCentro => $da)
{
	 $costoLlan = 0;
	
	movimientosCostos($inicio, $final, $idCentro, "rec", $tiposVehiculos, $tiposVehiculosXCentro, $capacidad, $ton, $per, $kmsR, $costoLlan, $recorrido, $combustible, $tonTipo, $viajes, $dias);
	//preguntar($dias);
	//preguntar($ton);
	$diasTotales = array_sum($dias[$idCentro]);
	$tonCen = 0;
	foreach($fechas as $ff)
	{
		$lineaTon[] = number_format(nvl($ton[$idCentro][$ff],0)/$dias[$idCentro][$ff],2);
		$tonCen += nvl($ton[$idCentro][$ff],0)/$dias[$idCentro][$ff];
	}

	$tonTOTAL+=$tonCen/count($fechas);

	//costoTon recogida y tranportada
	$cosTon = $tonPers = 0;
	foreach($fechas as $mes)
	{
		$qidCTRP = $db->sql_row("SELECT sum(c.valor) as val FROM costos c LEFT JOIN servicios s ON s.id=c.id_servicio WHERE id_centro='".$idCentro."' AND esquema='rec' AND id_variable_informe = 1 AND fecha = '".$mes."'");
		$costo=nvl($qidCTRP["val"],0)/nvl($ton[$idCentro][$mes],0);
		$lineaCosTon[] = number_format($costo,2);
		$cosTon+=$costo;

		$personas = nvl($ton[$idCentro][$mes],0)/nvl($per[$idCentro][$mes],0);
		$lineaPers[] = number_format($personas,2);
		$tonPers+=$personas;

		//costo de llanta por km recorrido ¿?
		$lineaLl[] = 0;
	}

	$cosTon = $cosTon/count($fechas);
	$cosTonTOTAL+=$cosTon;

	$tonPers = $tonPers/count($fechas);
	$persTOTAL+=$tonPers;

	preguntar($kmsR);
	preguntar($costoLlan);


	/*
	--$tonCen = array_sum($ton[$idCentro])/count($ton[$idCentro]);
	--$lineaTon[] = number_format($tonCen,2);
	--$tonTOTAL+=$tonCen;

	//costoTon recogida y tranportada
	$cosTon = $tonPers = 0;
	foreach($fechas as $mes)
	{
		$qidCTRP = $db->sql_row("SELECT sum(c.valor) as val FROM costos c LEFT JOIN servicios s ON s.id=c.id_servicio WHERE id_centro='".$idCentro."' AND esquema='rec' AND id_variable_informe = 1 AND fecha = '".$mes."'");
		$cosTon+= nvl($qidCTRP["val"],0)/nvl($ton[$idCentro][$mes],0);
		$tonPers+=nvl($ton[$idCentro][$mes],0)/nvl($per[$idCentro][$mes],0);
	}
	$cosTon = $cosTon/count($fechas);
	$lineaCosTon[] = number_format($cosTon,2);
	$cosTonTOTAL+=$cosTon;

	$tonPers = $tonPers/count($fechas);
	@$lineaPers[] = number_format($tonPers,2);
	$persTOTAL+=$tonPers;


	$val = ($costoLlan/count($fechas))/($kmsR/count($fechas));
	@$lineaLl[] = number_format($val,2);
	$costoLlanTOTAL+=$val;
*/
}


$lineaTon[] = number_format($tonTOTAL/count($centros),2);
$lineaCosTon[] = number_format($cosTonTOTAL/count($centros),2);
$lineaPers[] = number_format($persTOTAL/count($centros),2);
$lineaLl[] = number_format($costoLlanTOTAL/count($centros),2);

if($html)
{
	imprimirLinea($lineaTon,"",$estilos);
	imprimirLinea($lineaCosTon,"",$estilos);
	imprimirLinea($lineaPers,"",$estilos);
	imprimirLinea($lineaLl,"",$estilos);
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaTon, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaCosTon, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaPers, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaLl, array(1=>"txt_izq"));
}

/*
imprimirKmgl($fila, $columna, $html, $centros, $tiposVehiculos, $tiposVehiculosXCentro, $fechas, $recorrido, $combustible);
imprimirEficiencia($fila, $columna, $html, $centros, $tiposVehiculos, $tiposVehiculosXCentro, $fechas, $tonTipo, $viajes, $capacidad);




//barrido
if($html)
	imprimirLinea(array("BARRIDO"), "#b2d2e1", array(1=>"colspan=".$colspan." height='30' class='azul_osc_14'"));
else
{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array("BARRIDO"), array(1=>"azul_izq"),$colspan-2);
	$fila++;$columna=0;
}

$linea = array("Km barridos");
$linea2 = array("Costo / Km Barrido");
$linea3 = array("Km barridos por operario");
$linea4= array("Bolsas por Operario de Barrido");

$tiposVehiculos = $tiposVehiculosXCentro = $capacidad = $recorrido = $combustible = $tonTipo = $viajes = $ton = $per = array();
$kmTot = $cosKmTOTAL = $kmOpe = $bolOpe = 0;
$idVars = array(2,3,4,5);
foreach($centros as $idCentro => $da)
{
	$vars = array();
	foreach($fechas as $mes)
	{
		$qid3 = $db->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c LEFT JOIN servicios s ON s.id=c.id_servicio WHERE id_centro='".$idCentro."' AND esquema='bar' AND id_variable_informe IN (".implode(",",$idVars).") AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
		while($query = $db->sql_fetchrow($qid3))
		{
			$vars[$query["id_variable_informe"]][$mes] = $query["val"];
		}
	}

	if(isset($vars[2]))
	{
		$linea[] = number_format(array_sum($vars[2])/count($vars[2]),2);
		$kmTot+= array_sum($vars[2])/count($vars[2]);
	}else
		$linea[] = "";

	if(isset($vars[2]) && isset($vars[3]))
	{
		$val = 0;
		foreach($fechas as $mes)
			$val+=nvl($vars[3][$mes],0)/nvl($vars[2][$mes],0);
		$linea2[] = number_format($val/count($fechas),2);
		$cosKmTOTAL+=$val/count($fechas);
	}
	else
		$linea2[] = "";

	if(isset($vars[4]))
	{
		$linea3[] = number_format(array_sum($vars[4])/count($vars[4]),2);
		$kmOpe+= array_sum($vars[4])/count($vars[4]);
	}else
		$linea3[] = "";

	if(isset($vars[5]))
	{
		$linea4[] = number_format(array_sum($vars[5])/count($vars[5]),2);
		$bolOpe+= array_sum($vars[5])/count($vars[5]);
	}else
		$linea4[] = "";

	movimientosCostos($inicio, $final, $idCentro, "bar", $tiposVehiculos, $tiposVehiculosXCentro, $capacidad, $ton, $per, $kmsR, $costoLlan, $recorrido, $combustible, $tonTipo, $viajes);
}

$linea[] = number_format($kmTot/count($centros),2);
$linea2[] = number_format($cosKmTOTAL/count($centros),2);
$linea3[] = number_format($kmOpe/count($centros),2);
$linea4[] = number_format($bolOpe/count($centros),2);


if($html)
{
	imprimirLinea($linea,"",array(1=>"align='left' class='azul_osc_12'", 2=>"class='azul_osc_12'", 3=>"class='azul_osc_12'", 4=>"class='azul_osc_12'", 5=>"class='azul_osc_12'", 6=>"class='azul_osc_12'"));
	imprimirLinea($linea2,"",array(1=>"align='left' class='azul_osc_12'", 2=>"class='azul_osc_12'", 3=>"class='azul_osc_12'", 4=>"class='azul_osc_12'", 5=>"class='azul_osc_12'", 6=>"class='azul_osc_12'"));
	imprimirLinea($linea3,"",array(1=>"align='left' class='azul_osc_12'", 2=>"class='azul_osc_12'", 3=>"class='azul_osc_12'", 4=>"class='azul_osc_12'", 5=>"class='azul_osc_12'", 6=>"class='azul_osc_12'"));
	imprimirLinea($linea4,"",array(1=>"align='left' class='azul_osc_12'", 2=>"class='azul_osc_12'", 3=>"class='azul_osc_12'", 4=>"class='azul_osc_12'", 5=>"class='azul_osc_12'", 6=>"class='azul_osc_12'"));
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea2, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea3, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea4, array(1=>"txt_izq"));
}

imprimirKmgl($fila, $columna, $html, $centros, $tiposVehiculos, $tiposVehiculosXCentro, $fechas, $recorrido, $combustible);
imprimirEficiencia($fila, $columna, $html, $centros, $tiposVehiculos, $tiposVehiculosXCentro, $fechas, $tonTipo, $viajes, $capacidad);


//generales
if($html)
	imprimirLinea(array("GENERALES"), "#b2d2e1", array(1=>"colspan=".$colspan." height='30' class='azul_osc_14'"));
else
{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array("GENERALES"), array(1=>"azul_izq"),$colspan-2);
	$fila++;$columna=0;
}

$linea = array("Costos Mtto / Costos totales");
$linea2 = array("Gastos Adtivos / Ventas Totales");
$linea3 = array("N° trabajadores / 1.000 usuarios");
$linea4 = array("N° total de empleados");
$otrasLineas = $nombreOtras = $otrasLineasDos = $nombreOtrasDos = array();

$cosMtto = $gastos = $numTa = $totEmp = 0;
foreach($centros as $idCentro => $da)
{
	$vars =  array();
	foreach($fechas as $mes)
	{
		$qid3 = $db->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c LEFT JOIN servicios s ON s.id=c.id_servicio WHERE id_centro='".$idCentro."'  AND id_variable_informe >= 10 AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
		while($query = $db->sql_fetchrow($qid3))
		{
			$vars[$query["id_variable_informe"]][$mes] = $query["val"];
		}
	}

	if(isset($vars[10]) && isset($vars[11]))
	{
		$val = 0;
		foreach($fechas as $mes)
			$val+=nvl($vars[10][$mes],0)/nvl($vars[11][$mes],0);
		$linea[] = number_format($val/count($fechas),2);
		$cosMtto+=$val/count($fechas);
	}
	else
		$linea[] = "";

	if(isset($vars[12]) && isset($vars[13]) && isset($vars[14]) && isset($vars[15]))
	{
		$val = 0;
		foreach($fechas as $mes)
			$val+=nvl($vars[12][$mes],0)/(nvl($vars[13][$mes],0)+nvl($vars[14][$mes],0)+nvl($vars[15][$mes],0));
		$linea2[] = number_format($val/count($fechas),2);
		$gastos+=$val/count($fechas);
	}
	else
		$linea2[] = "";

	if(isset($vars[16]) && isset($vars[17]))
	{
		$val = 0;
		foreach($fechas as $mes)
			$val+=(nvl($vars[16][$mes],0)/nvl($vars[17][$mes],0))*1000;
		$linea3[] = number_format($val/count($fechas),2);
		$numTa+=$val/count($fechas);
	}
	else
		$linea3[] = "";
	
	if(isset($vars[17]))
	{
		$linea4[] = number_format(array_sum($vars[17])/count($vars[17]),2);
		$totEmp+= array_sum($vars[17])/count($vars[17]);
	}else
		$linea4[] = "";

	$qidOV = $db->sql_query("SELECT * FROM variables_informes WHERE id=13 or id=14 or id=15 or (id >=19 AND id<=26) ORDER BY id");
	while($queryOV = $db->sql_fetchrow($qidOV))
	{
		$nombreOtras[$queryOV["id"]] = $queryOV["variable"];
		$total = 0;
		
		if(isset($vars[$queryOV["id"]]))
		{
			$otrasLineas[$queryOV["id"]][] = array_sum($vars[$queryOV["id"]])/count($vars[$queryOV["id"]]);
			$total+= array_sum($vars[$queryOV["id"]])/count($vars[$queryOV["id"]]);
		}else
			$otrasLineas[$queryOV["id"]][] = "";
	}

	//ind financieros
	$qidOV = $db->sql_query("SELECT * FROM variables_informes WHERE id >=27 AND id<=36 ORDER BY id");
	while($queryOV = $db->sql_fetchrow($qidOV))
	{
		$nombreOtrasDos[$queryOV["id"]] = $queryOV["variable"];
		if(isset($vars[$queryOV["id"]]))
		{
			$otrasLineasDos[$queryOV["id"]][] = array_sum($vars[$queryOV["id"]])/count($vars[$queryOV["id"]]);
		}else
			$otrasLineasDos[$queryOV["id"]][] = "";
	}
}

$linea[] = number_format($cosMtto/count($centros),2);
$linea2[] = number_format($gastos/count($centros),2);
$linea3[] = number_format($numTa/count($centros),2);
$linea4[] = number_format($totEmp/count($centros),2);

if($html)
{
	imprimirLinea($linea,"",array(1=>"align='left' class='azul_osc_12'", 2=>"class='azul_osc_12'", 3=>"class='azul_osc_12'", 4=>"class='azul_osc_12'", 5=>"class='azul_osc_12'", 6=>"class='azul_osc_12'"));
	imprimirLinea($linea2,"",array(1=>"align='left' class='azul_osc_12'", 2=>"class='azul_osc_12'", 3=>"class='azul_osc_12'", 4=>"class='azul_osc_12'", 5=>"class='azul_osc_12'", 6=>"class='azul_osc_12'"));
	imprimirLinea($linea3,"",array(1=>"align='left' class='azul_osc_12'", 2=>"class='azul_osc_12'", 3=>"class='azul_osc_12'", 4=>"class='azul_osc_12'", 5=>"class='azul_osc_12'", 6=>"class='azul_osc_12'"));
	imprimirLinea($linea4,"",array(1=>"align='left' class='azul_osc_12'", 2=>"class='azul_osc_12'", 3=>"class='azul_osc_12'", 4=>"class='azul_osc_12'", 5=>"class='azul_osc_12'", 6=>"class='azul_osc_12'"));
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea2, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea3, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea4, array(1=>"txt_izq"));
}

if($html)
	imprimirLinea(array("COMERCIALES"), "#b2d2e1", array(1=>"colspan=".$colspan." height='30' class='azul_osc_14'"));
else
{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array("COMERCIALES"), array(1=>"azul_izq"),$colspan-2);
	$fila++;$columna=0;
}
imprimirOtras($fila, $columna, $html, $centros, $nombreOtras, $otrasLineas);

if($html)
	imprimirLinea(array("INDICADORES FINANCIEROS"), "#b2d2e1", array(1=>"colspan=".$colspan." height='30' class='azul_osc_14'"));
else
{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array("INDICADORES FINANCIEROS"), array(1=>"azul_izq"),$colspan-2);
	$fila++;$columna=0;
}
imprimirOtras($fila, $columna, $html, $centros, $nombreOtrasDos, $otrasLineasDos);
*/



/*funciones*/


function movimientosCostos($inicio, $final, $centro, $tipo, &$tiposVehiculos, &$tiposVehiculosXCentro, &$capacidad, &$ton, &$per, &$kmsR, &$costoLlan, &$recorrido, &$combustible, &$tonTipo, &$viajes, &$dias)
{
	global $db, $CFG, $ME;
	
	$strQuery="SELECT m.id, m.id_vehiculo, m.combustible, i.codigo, t.tipo, v.id_tipo_vehiculo, t.capacidad, v.kilometraje as veh_km, m.inicio
				FROM rec.movimientos m 
				LEFT JOIN vehiculos v ON v.id=m.id_vehiculo
				LEFT JOIN tipos_vehiculos t ON t.id = v.id_tipo_vehiculo
				LEFT JOIN micros i ON i.id=m.id_micro
				LEFT JOIN servicios s ON s.id = i.id_servicio
				LEFT JOIN ases a ON a.id = i.id_ase
				WHERE a.id_centro='".$centro."' AND s.inf_indope='".$tipo."' and t.id in (1,2,4,6,8,10,11,12,14,15,19,21,27) AND inicio::date >= '".$inicio."' AND inicio::date<='".$final."' order by 5";
	$qidMov = $db->sql_query($strQuery);
	while($mov = $db->sql_fetchrow($qidMov))
	{
		$mes = strftime("%Y-%m",strtotime($mov["inicio"]));

		if(!isset($dias[$centro][$mes])) $dias[$centro][$mes] = 1;
		else $dias[$centro][$mes]++;



		$tiposVehiculos[$mov["id_tipo_vehiculo"]] = $mov["tipo"];
		$capacidad[$mov["id_tipo_vehiculo"]] = $mov["capacidad"];
		$tiposVehiculosXCentro[$centro][$mov["id_tipo_vehiculo"]] = $mov["tipo"];

		//pesos
		$tonMov = averiguarPesoXMov($mov["id"],"",true);
		if(!isset($ton[$centro][$mes])) $ton[$centro][$mes]=0;
		$ton[$centro][$mes]+=$tonMov;

		if(!isset($tonTipo[$centro][$mov["id_tipo_vehiculo"]][$mes])) $tonTipo[$centro][$mov["id_tipo_vehiculo"]][$mes]=0;
		$tonTipo[$centro][$mov["id_tipo_vehiculo"]][$mes]+=$tonMov;

		$qidPer = $db->sql_row("SELECT count(id) as num FROM rec.movimientos_personas WHERE id_movimiento=".$mov["id"]);
		if(!isset($per[$centro][$mes])) $per[$centro][$mes]=0;
			$per[$centro][$mes]+=$qidPer["num"];

		//costo de llantas por km recorrido
		//echo "SELECT id, costo FROM llta.llantas WHERE id_vehiculo='".$mov["id_vehiculo"]."'<br>";
		$qidLlta = $db->sql_query("SELECT id, costo FROM llta.llantas WHERE id_vehiculo='".$mov["id_vehiculo"]."'");
		while($llta = $db->sql_fetchrow($qidLlta))
		{
			// no se'cómo sacr el costo de la llanta por km recorrido por mes
			$costoLlan+=$llta["costo"];
			$veh = $db->sql_row("SELECT id,km, posicion FROM llta.movimientos WHERE id_llanta='".$llta["id"]."' AND id_tipo_movimiento=5");
			if(!isset($kmsR[$centro][$mes])) $kmsR[$centro][$mes]=0;
			$kmsR[$centro][$mes]+=$mov["veh_km"]-$veh["km"];
		}
		/*ojo*/

		//combustible
		if(!isset($combustible[$centro][$mov["id_tipo_vehiculo"]][$mes])) $combustible[$centro][$mov["id_tipo_vehiculo"]][$mes]=0;
		if($mov["combustible"] != "")
			$combustible[$centro][$mov["id_tipo_vehiculo"]][$mes]+=$mov["combustible"];

		//recorrido
		$rec = kmsRecorridoPorMov($mov["id"]);
		$kmini = $db->sql_row("SELECT km FROM rec.desplazamientos WHERE id_movimiento=".$mov["id"]." ORDER BY hora_inicio LIMIT 1");
		if(nvl($kmini["km"],0)==0)
			$rec="0";
		if(!isset($recorrido[$centro][$mov["id_tipo_vehiculo"]][$mes])) $recorrido[$centro][$mov["id_tipo_vehiculo"]][$mes]=0;
		$recorrido[$centro][$mov["id_tipo_vehiculo"]][$mes]+=$rec;

		//viajes
		if(!isset($viajes[$centro][$mov["id_tipo_vehiculo"]][$mes])) $viajes[$centro][$mov["id_tipo_vehiculo"]][$mes]=0;
		$viajes[$centro][$mov["id_tipo_vehiculo"]][$mes] +=averiguarViajeXMov($mov["id"]);
	}
}

function imprimirKmgl(&$fila, &$columna, $html, $centros, $tiposVehiculos, $tiposVehiculosXCentro, $fechas, $recorrido, $combustible)
{
	global $workbook;
	global $worksheet;

	foreach($tiposVehiculos as $id => $tipo)
	{
		$linea = array("Km/gl ".$tipo);
		$total = $numCentros = 0;
		foreach($centros as $idCentro => $daCen)
		{
			$val = 0;
			foreach($fechas as $mes)
				$val+= nvl($recorrido[$idCentro][$id][$mes],0)/nvl($combustible[$idCentro][$id][$mes],0);

			$val = $val/count($fechas);
			if(isset($tiposVehiculosXCentro[$idCentro][$id]))
			{
				$linea[] =  number_format($val,2);
				$total+=$val;
				$numCentros+=1;
			}else
				$linea[] = "";
		}

		$linea[] = number_format(($total/$numCentros),2);

		if($html)
			imprimirLinea($linea,"",array(1=>"align='left' class='azul_osc_12'", 2=>"class='azul_osc_12'", 3=>"class='azul_osc_12'", 4=>"class='azul_osc_12'", 5=>"class='azul_osc_12'", 6=>"class='azul_osc_12'"));
		else
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
	}
}


function imprimirEficiencia(&$fila, &$columna, $html, $centros, $tiposVehiculos, $tiposVehiculosXCentro, $fechas, $tonTipo, $viajes, $capacidad)
{
	global $workbook;
	global $worksheet;

	$eficiencia = array();
	foreach($tiposVehiculos as $id => $tipo)
	{
		@$linea = array("Ton/Viaje ".$tipo);
		$linea2 = array("Eficiencia %");
		$total = $totalEfi = $numCentros = 0;
		foreach($centros as $idCentro => $daCentros)
		{
			$val = 0;
			foreach($fechas as $mes)
				@$val+= nvl($tonTipo[$idCentro][$id][$mes],0)/nvl($viajes[$idCentro][$id][$mes],0);
			$val = $val/count($fechas);
			if(isset($tiposVehiculosXCentro[$idCentro][$id]))
			{
				$linea[] =  number_format($val,2);
				$linea2[] = number_format(($val/$capacidad[$id]) * 100,2)."%";
				if(!isset($eficiencia[$idCentro])) $eficiencia[$idCentro] = array("val"=>0, "tipos"=>0);
				$eficiencia[$idCentro]["val"] += ($val/$capacidad[$id])*100;
				$eficiencia[$idCentro]["tipos"] +=1;

				$total+=$val;
				$totalEfi+=($val/$capacidad[$id]) * 100;
				$numCentros+=1;
			}else
			{
				$linea[] = "";
				$linea2[] = "";
			}
		}

		$linea[] = number_format($total/$numCentros , 2);
		$linea2[] = number_format($totalEfi/$numCentros , 2)."%";

		if($html)
		{
			imprimirLinea($linea,"",array(1=>"align='left' class='azul_osc_12'",2=>"class='azul_osc_12'", 3=>"class='azul_osc_12'", 4=>"class='azul_osc_12'", 5=>"class='azul_osc_12'", 6=>"class='azul_osc_12'"));
			imprimirLinea($linea2,"",array(1=>"align='left' class='azul_osc_12'", 2=>"class='azul_osc_12'", 3=>"class='azul_osc_12'", 4=>"class='azul_osc_12'", 5=>"class='azul_osc_12'", 6=>"class='azul_osc_12'"));
		}
		else
		{
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea2, array(1=>"txt_izq"));
		}
	}

	$linea = array("Eficiencia  % promedio flota");
	$granTotEfi = 0;
	foreach($centros as $idCentro => $dxCen)
	{
		$val = nvl($eficiencia[$idCentro]["val"],0)/nvl($eficiencia[$idCentro]["tipos"],0);
		$linea[] = number_format($val,2)."%";
		$granTotEfi += $val; 
	}
	$linea[] = number_format(($granTotEfi/count($centros)), 2)."%";

	if($html)
		imprimirLinea($linea,"",array(1=>"align='left' class='azul_osc_12'", 2=>"class='azul_osc_12'", 3=>"class='azul_osc_12'", 4=>"class='azul_osc_12'", 5=>"class='azul_osc_12'", 6=>"class='azul_osc_12'"));
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
}


function imprimirOtras(&$fila, &$columna, $html, $centros, $nombreOtras, $otrasLineas)
{
	global $workbook;
	global $worksheet;

	foreach($nombreOtras as $key => $nombre)
	{
		$linea = array($nombre);
		$total = 0;
		foreach($otrasLineas[$key] as $val)
		{
			if($val != "")
			{
				$linea[] = number_format($val,2);
				$total+=$val;
			}else
				$linea[] = "";
		}
		$linea[] = number_format($total/count($centros),2);
		
		if($html)
			imprimirLinea($linea,"",array(1=>"align='left' class='azul_osc_12'", 2=>"class='azul_osc_12'", 3=>"class='azul_osc_12'", 4=>"class='azul_osc_12'", 5=>"class='azul_osc_12'", 6=>"class='azul_osc_12'"));
		else
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
	}


}

//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final;
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
