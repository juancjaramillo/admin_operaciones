<?
// opera : Producción por Ases
include("../application.php");
if($_SERVER["REMOTE_ADDR"] != "186.30.42.202" and $_SERVER["REMOTE_ADDR"] !="190.27.212.169" && $_SERVER["REMOTE_ADDR"] !="201.244.162.111")
	die("Estamos realizando ajustes de acuerdo a las solicitudes del día 15 de mayo y haciendo una revisión de las eficiancias para ajustarla y que no tomen los vehiculos que van cargados a la base para completar el viaje en el siguiente turno");

$html = true;
$user=$_SESSION[$CFG->sesion]["user"];
$nivel=$_SESSION[$CFG->sesion]["user"]["nivel_acceso"];
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

$titulos = $turnos = array();
$titulosRecoleccion = array("RECOLECCIÓN");
$titulosBarrido = array("BARRIDO");
$titulosGenerales = array("GENERALES");
$titulosComerciales = array("COMERCIALES");
$titulosIndFinan = array("INDICADORES FINANCIEROS");
$titMargenes = array("<b>MARGENES</b>");
$titRentabilidad = array("<b>RENTABILIDAD</b>");
$titLiquidez = array("<b>LIQUIDEZ</b>");
$titEndeudamiento = array("<b>ENDEUDAMIENTO</b>");
$titCobertura = array("<b>COBERTURA DE INTERESES Y SERV. DE DEUDA</b>");


$estilosTitulosRecoleccion = array(1=>" height='30'  class='azul_osc_14'");
$estilos = array(1=>"align='left'  class='azul_osc_12'");
$i=2;
$qidCentro = $db->sql_query("SELECT id_centro, centro FROM personas_centros LEFT JOIN centros ON centros.id=personas_centros.id_centro WHERE id_persona='".$user["id"]."' ORDER BY id_centro");
while($queryCen = $db->sql_fetchrow($qidCentro)){
	$centros[$queryCen["id_centro"]] = $queryCen["centro"];
	$titulos[] = $queryCen["centro"];

	foreach($fechas as $mes)
	{
		$titulosRecoleccion[] = $titulosBarrido[] = $titulosGenerales[] = $titulosComerciales[] = $titulosIndFinan[] = ucfirst(strftime("%b.%Y",strtotime($mes."-01")));
		$titMargenes[] = $titRentabilidad[] = $titLiquidez[] = $titEndeudamiento[] = $titCobertura[] = "";
		$estilosTitulosRecoleccion[$i] = " class='azul_osc_14'";
		$estilos[$i] = "class='azul_osc_12'";
		$i++;
	}
	
	//turnos
	$qidT = $db->sql_query("SELECT t.* FROM turnos t LEFT JOIN centros c ON c.id_empresa=t.id_empresa WHERE c.id=".$queryCen["id_centro"]." ORDER BY hora_inicio");
	while($queryTur = $db->sql_fetchrow($qidT))
	{
		$turnos[$queryCen["id_centro"]][$queryTur["id"]] = array("hora_inicio"=>$queryTur["hora_inicio"], "turno"=>$queryTur["turno"]);
	}
}

$titulosRecoleccion[]=$titulosBarrido[]=$titulosGenerales[]=$titulosComerciales[] = $titulosIndFinan[] = "Promedio";
$titMargenes[] = $titRentabilidad[] = $titLiquidez[] = $titEndeudamiento[] = $titCobertura[] = "";
$estilosTitulosRecoleccion[$i] = " class='azul_osc_14'";
$estilos[$i] = "class='azul_osc_12'";

$colspan = (count($fechas) * count($centros))+2;
if($html)
{
	$titulos[] = "Consolidados Promedios";
	$anchotabla = (85*(count($titulosRecoleccion)-2))+430;
	if ($anchotabla<800) $anchotabla=800;
	echo '
				<table width="'.$anchotabla.'" border=1 bordercolor="#7fa840" align="center">
							<tr>';
	echo '<th height="40" width="280" class="azul_osc_16">MENSUAL</th>';
	foreach($titulos as $tt){
		if ($tt=="Consolidados Promedios"){
			echo '<th height="40" width="130" class="azul_osc_16">'.$tt.'</th>';
		}
		else {
			echo '<th height="40" class="azul_osc_16" colspan='.count($fechas).'>'.$tt.'</th>';
		}
	}
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


$tiposVehiculos = $tiposVehiculosXCentro = $capacidad = $recorrido = $combustible = $tonTipo = $viajes = $ton = $per = $kmsR = $viajesXTurno = array();
$lineaTon = array("Toneladas recogidas");
$lineaCosTon = array("Costo/Ton Recogida y transportada");
$lineaPers = array("Toneladas recogidas por  tripulacion");
$lineaLl = array("Costo de llantas por km recorrido ($/Km)");
$tonTOTAL = $cosTonTOTAL = $persTOTAL = $costoLlanTOTAL = 0;
foreach($centros as $idCentro => $da)
{
	 $costoLlan = 0;
	
	movimientosCostos($inicio, $final, $idCentro, "rec", $tiposVehiculos, $tiposVehiculosXCentro, $capacidad, $ton, $per, $kmsR, $costoLlan, $recorrido, $combustible, $tonTipo, $viajes, $viajesXTurno, $turnos[$idCentro]);

	$tonCen = 0;
	foreach($fechas as $ff)
	{
		$val = nvl($ton[$idCentro][$ff],0);
		$lineaTon[] = number_format($val,2);
		$tonCen += $val;
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

		@$personas = nvl($ton[$idCentro][$mes],0)/nvl($per[$idCentro][$mes],0);
		$lineaPers[] = number_format($personas,2);
		$tonPers+=$personas;

		//costo de llanta por km recorrido ¿?
		$lineaLl[] = 0;
	}

	$cosTon = $cosTon/count($fechas);
	$cosTonTOTAL+=$cosTon;

	$tonPers = $tonPers/count($fechas);
	$persTOTAL+=$tonPers;
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


imprimirKmgl($fila, $columna, $html, $centros, $tiposVehiculos, $tiposVehiculosXCentro, $fechas, $recorrido, $combustible, $estilos);
imprimirEficiencia($fila, $columna, $html, $centros, $tiposVehiculos, $tiposVehiculosXCentro, $fechas, $tonTipo, $viajes, $capacidad, $estilos);
imprimirTurnos($fila, $columna, $html, $centros, $fechas, $turnos, $viajesXTurno, $estilos);







//barrido
if($html)
	imprimirLinea($titulosBarrido, "#b2d2e1", $estilosTitulosRecoleccion);
else
{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosBarrido, array(1=>"azul_izq"));
	$fila++;$columna=0;
}


$linea = array("Km barridos");
$linea2 = array("Costo / Km Barrido");
$linea3 = array("Km barridos por operario");
$linea4= array("Bolsas por Operario de Barrido");

$tiposVehiculos = $tiposVehiculosXCentro = $capacidad = $recorrido = $combustible = $tonTipo = $viajes = $ton = $per = $viajesXTurno = array();
$kmTot = $cosKmTOTAL = $kmOpe = $bolOpe = 0;
$idVars = array(2,3,4,5,6);
foreach($centros as $idCentro => $da)
{
	$vars = array();
	$kmTotCen = $cosKmTOTALCen = $kmOpeCen = $bolOpeCen = 0;
	foreach($fechas as $mes)
	{
		$qid3 = $db->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c LEFT JOIN servicios s ON s.id=c.id_servicio WHERE id_centro='".$idCentro."' AND esquema='bar' AND id_variable_informe IN (".implode(",",$idVars).") AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
		while($query = $db->sql_fetchrow($qid3))
		{
			$vars[$query["id_variable_informe"]][$mes] = $query["val"];
		}
	
		//preguntar($vars);
		if(isset($vars[2][$mes]))
		{
			$linea[] = number_format($vars[2][$mes],2);
			$kmTotCen+= $vars[2][$mes];
		}else
			$linea[] = "";


		if(isset($vars[2][$mes]) && isset($vars[3][$mes]))
		{
			$val = nvl($vars[3][$mes],0)/nvl($vars[2][$mes],0);
			$linea2[]=number_format($val,2);
			$cosKmTOTALCen+=$val;
		}
		else
			$linea2[] = "";

		if(isset($vars[4][$mes]))
		{
			$val = nvl($vars[4][$mes],0)/nvl($vars[6][$mes],1);
			$linea3[] = number_format($val,2);
			$kmOpeCen+= $val;
		}else
			$linea3[] = "";

		if(isset($vars[5][$mes]))
		{
			$linea4[] = number_format($vars[5][$mes],2);
			$bolOpeCen+= $vars[5][$mes];
		}else
			$linea4[] = "";
	}

	
	$kmTotCen = $kmTotCen/count($fechas);
	$kmTot+=$kmTotCen;
	
	$cosKmTOTALCen = $cosKmTOTALCen/count($fechas);
	$cosKmTOTAL+=$cosKmTOTALCen;

	$kmOpeCen = $kmOpeCen/count($fechas);
	$kmOpe+=$kmOpeCen;

	$bolOpeCen = $bolOpeCen/count($fechas);
	$bolOpe+=$bolOpeCen;

	movimientosCostos($inicio, $final, $idCentro, "bar", $tiposVehiculos, $tiposVehiculosXCentro, $capacidad, $ton, $per, $kmsR, $costoLlan, $recorrido, $combustible, $tonTipo, $viajes, $viajesXTurno, $turnos[$idCentro]);
}

$linea[] = number_format($kmTot/count($centros),2);
$linea2[] = number_format($cosKmTOTAL/count($centros),2);
$linea3[] = number_format($kmOpe/count($centros),2);
$linea4[] = number_format($bolOpe/count($centros),2);


if($html)
{
	imprimirLinea($linea,"", $estilos);
	imprimirLinea($linea2,"", $estilos);
	imprimirLinea($linea3,"", $estilos);
	imprimirLinea($linea4,"", $estilos);
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea2, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea3, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea4, array(1=>"txt_izq"));
}

imprimirKmgl($fila, $columna, $html, $centros, $tiposVehiculos, $tiposVehiculosXCentro, $fechas, $recorrido, $combustible, $estilos);
imprimirEficiencia($fila, $columna, $html, $centros, $tiposVehiculos, $tiposVehiculosXCentro, $fechas, $tonTipo, $viajes, $capacidad, $estilos);

//JFMC Para que solo los vean los Directores y Gerentes
if ($nivel=="1" || $nivel==7 || $nivel==8 || $nivel==11 || $nivel==12 || $nivel==15){
//generales
if($html)
	imprimirLinea($titulosGenerales, "#b2d2e1", $estilosTitulosRecoleccion);
else
{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosGenerales, array(1=>"azul_izq"));
	$fila++;$columna=0;
}


$linea = array("Costos Mtto / Costos totales");
$linea2 = array("Gastos Adtivos / Ventas Totales");
$linea3 = array("N° trabajadores / 1.000 usuarios");
$linea4 = array("N° total de empleados");
$lineaFact = array("Facturación Total");
$lineaRecau = array("Indice de Recaudo Total");
$lineaRC = array("Indice de Recaudo Corriente");
$lineaUVD = array("Usuarios Vinculados Vs Desvinculados");
$otrasLineas = $nombreOtras = $otrasLineasDos = $nombreOtrasDos = array();

//ind comerciales
$qidOV = $db->sql_query("SELECT * FROM variables_informes WHERE id=13 or id=14 or id=15 or (id >=19 AND id<=26) or id=37 or id= 38 ORDER BY id");
while($queryOV = $db->sql_fetchrow($qidOV))
{
	$nombreOtras[$queryOV["id"]] = $queryOV["variable"];
}

//ind financieros
$qidOV = $db->sql_query("SELECT * FROM variables_informes WHERE id >=27 AND id<=36 ORDER BY id");
while($queryOV = $db->sql_fetchrow($qidOV))
{
	$nombreOtrasDos[$queryOV["id"]] = $queryOV["variable"];
}

$cosMtto = $gastos = $numTa = $totEmp = $totFact = $totRecau = $totRC = $totUVD = 0;
foreach($centros as $idCentro => $da)
{
	$cosMttoCen = $gastosCen = $numTaCen = $totEmpCen = $totFactCen = $totRecauCen = $totRCCen = $totUVDCen = 0;
	$vars =  array();
	foreach($fechas as $mes)
	{
		$qid3 = $db->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c LEFT JOIN servicios s ON s.id=c.id_servicio WHERE id_centro='".$idCentro."'  AND id_variable_informe >= 10 AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
		while($query = $db->sql_fetchrow($qid3))
		{
			$vars[$query["id_variable_informe"]][$mes] = $query["val"];
		}

		if(isset($vars[10][$mes]) && isset($vars[11][$mes]))
		{
			$val=nvl($vars[10][$mes],0)/nvl($vars[11][$mes],0)*100;
			$linea[] = number_format($val,2).'%';
			$cosMttoCen+=$val;
		}else
			$linea[] = "";

		if(isset($vars[12][$mes]) && isset($vars[13][$mes]) && isset($vars[14][$mes]) && isset($vars[15][$mes]))
		{
			$val=nvl($vars[12][$mes],0)/(nvl($vars[13][$mes],0)+nvl($vars[14][$mes],0)+nvl($vars[15][$mes],0))*100;
			$linea2[] = number_format($val,2).'%';
			$gastosCen+=$val;
		}
		else
			$linea2[] = "";

		if(isset($vars[16][$mes]) && isset($vars[17][$mes]))
		{
			$val=(nvl($vars[16][$mes],0)/nvl($vars[17][$mes],0))*1000;
			$linea3[] = number_format($val,2);
			$numTaCen+=$val;
		}
		else
			$linea3[] = "";

		if(isset($vars[16][$mes]))
		{
			$val = $vars[16][$mes];
			$linea4[] = number_format($val,2);
			$totEmpCen+= $val;
		}else
			$linea4[] = "";
	
		//ind comerciales
		foreach($nombreOtras as $id => $name)
		{
			if(isset($vars[$id][$mes]))
				$otrasLineas[$id][$idCentro][$mes] = $vars[$id][$mes];
			else
				$otrasLineas[$id][$idCentro][$mes] = "";
		}


		//ind financieros
		foreach($nombreOtrasDos as $id => $name)
		{
			if(isset($vars[$id][$mes])) 
					$otrasLineasDos[$id][$idCentro][$mes] = $vars[$id][$mes];
			else
					$otrasLineasDos[$id][$idCentro][$mes] = "";
		}

		$valFact = nvl($vars[13][$mes],0) + nvl($vars[14][$mes],0) + nvl($vars[15][$mes],0);
		$lineaFact[] = number_format($valFact,2);
		$totFactCen+=$valFact;

		$valRecau = nvl($vars[37][$mes],0)/$valFact;
		$lineaRecau[] = number_format($valRecau,2)."%";
		$totRecauCen += $valRecau;

		$valRC =  nvl($vars[19][$mes],0)/$valFact;
		$lineaRC[]  = number_format($valRC,2)."%";
		$totRCCen+=$valRC;

		@$valUVD = nvl($vars[26][$mes],0)/nvl($vars[38][$mes],0);
		$lineaUVD[] = number_format($valUVD,2);
		$totUVDCen+=$valUVD;
	}

	$cosMttoCen = $cosMttoCen/count($fechas);
	$cosMtto+=$cosMttoCen;
	
	$gastosCen = $gastosCen/count($fechas);
	$gastos+=$gastosCen;

	$numTaCen = $numTaCen/count($fechas);
	$numTa+=$numTaCen;

	$totEmpCen = $totEmpCen/count($fechas);
	$totEmp+=$totEmpCen;

	$totFactCen = $totFactCen;
	$totFact+=$totFactCen;

	$totRecauCen = $totRecauCen/count($fechas);
	$totRecau+=$totRecauCen;

	$totRCCen = $totRCCen/count($fechas);
	$totRC+=$totRCCen;

	$totUVDCen = $totUVDCen/count($fechas);
	$totUVD+=$totUVDCen;
}

$linea[] = number_format($cosMtto/count($centros),2).'%';
$linea2[] = number_format($gastos/count($centros),2).'%';
$linea3[] = number_format($numTa/count($centros),2);
$linea4[] = number_format($totEmp/count($centros),2);
$lineaFact[] = number_format($totFact/count($centros),2);
$lineaRecau[]  = number_format($totRecau/count($centros),2)."%";
$lineaRC[] = number_format($totRC/count($centros),2)."%";
$lineaUVD[] = number_format($totUVD/count($centros),2);

if($html)
{
	imprimirLinea($linea,"", $estilos);
	imprimirLinea($linea2,"", $estilos);
	imprimirLinea($linea3,"", $estilos);
	imprimirLinea($linea4,"", $estilos);
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea2, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea3, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea4, array(1=>"txt_izq"));
}


if($html)
	imprimirLinea($titulosComerciales, "#b2d2e1", $estilosTitulosRecoleccion);
else
{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosComerciales, array(1=>"azul_izq"));
	$fila++;$columna=0;
}
//facturacion
imprimirOtras($fila, $columna, $html, $centros, $fechas, $nombreOtras, $otrasLineas, $estilos, array(13,14,15));
if($html) imprimirLinea($lineaFact,"", $estilos); else imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaFact, array(1=>"txt_izq"));
imprimirOtras($fila, $columna, $html, $centros, $fechas, $nombreOtras, $otrasLineas, $estilos, array(37));
if($html) imprimirLinea($lineaRecau,"", $estilos); else imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaRecau, array(1=>"txt_izq"));
imprimirOtras($fila, $columna, $html, $centros, $fechas, $nombreOtras, $otrasLineas, $estilos, array(19));
if($html) imprimirLinea($lineaRC,"", $estilos); else imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaRC, array(1=>"txt_izq"));
imprimirOtras($fila, $columna, $html, $centros, $fechas, $nombreOtras, $otrasLineas, $estilos, array(20,21,22,23,24,25));
if($html) imprimirLinea($lineaUVD,"", $estilos); else imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaUVD, array(1=>"txt_izq")); 

if($html)
	imprimirLinea($titulosIndFinan, "#b2d2e1", $estilosTitulosRecoleccion);
else
{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosIndFinan, array(1=>"azul_izq"));
	$fila++;$columna=0;
}


if($html) imprimirLinea($titMargenes,"", $estilos); else imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $titMargenes, array(1=>"txt_izq")); 
imprimirOtras($fila, $columna, $html, $centros, $fechas, $nombreOtrasDos, $otrasLineasDos, $estilos, array(27,28),"%");
if($html) imprimirLinea($titRentabilidad,"", $estilos); else imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $titRentabilidad, array(1=>"txt_izq")); 
imprimirOtras($fila, $columna, $html, $centros, $fechas, $nombreOtrasDos, $otrasLineasDos, $estilos, array(29,30),"%");
if($html) imprimirLinea($titLiquidez,"", $estilos); else imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $titLiquidez, array(1=>"txt_izq")); 
imprimirOtras($fila, $columna, $html, $centros, $fechas, $nombreOtrasDos, $otrasLineasDos, $estilos, array(31,32));
if($html) imprimirLinea($titEndeudamiento,"", $estilos); else imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $titEndeudamiento, array(1=>"txt_izq")); 
imprimirOtras($fila, $columna, $html, $centros, $fechas, $nombreOtrasDos, $otrasLineasDos, $estilos, array(33,34),"%");
if($html) imprimirLinea($titCobertura,"", $estilos); else imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $titCobertura, array(1=>"txt_izq")); 
imprimirOtras($fila, $columna, $html, $centros, $fechas, $nombreOtrasDos, $otrasLineasDos, $estilos, array(35,36));

} //Fin de Gerentes y directores.

/*funciones*/

function movimientosCostos($inicio, $final, $centro, $tipo, &$tiposVehiculos, &$tiposVehiculosXCentro, &$capacidad, &$ton, &$per, &$kmsR, &$costoLlan, &$recorrido, &$combustible, &$tonTipo, &$viajes, &$viajesXTurno, $turnos)
{
	global $db, $CFG, $ME;
	
	$conTipo = "2,8,14,15";
	$orden_info = "";
	if($tipo == "bar")
	{
		$conTipo = "1,11,19,4,29,21";
		$orden_info = " DESC";
	}

	$strQuery="SELECT m.id, m.id_vehiculo, m.combustible, i.codigo, t.tipo, v.id_tipo_vehiculo, t.capacidad, v.kilometraje as veh_km, m.inicio
				FROM rec.movimientos m 
				LEFT JOIN vehiculos v ON v.id=m.id_vehiculo
				LEFT JOIN tipos_vehiculos t ON t.id = v.id_tipo_vehiculo
				LEFT JOIN micros i ON i.id=m.id_micro
				LEFT JOIN servicios s ON s.id = i.id_servicio
				LEFT JOIN ases a ON a.id = i.id_ase
				WHERE a.id_centro='".$centro."' AND s.inf_indope='".$tipo."' and t.id in (".$conTipo.") AND inicio::date >= '".$inicio."' AND inicio::date<='".$final."' 
				ORDER BY t.orden_info ".$orden_info;
	$qidMov = $db->sql_query($strQuery);
	while($mov = $db->sql_fetchrow($qidMov))
	{
		$mes = strftime("%Y-%m",strtotime($mov["inicio"]));

		$tiposVehiculos[$mov["id_tipo_vehiculo"]] = $mov["tipo"];
		$capacidad[$mov["id_tipo_vehiculo"]] = $mov["capacidad"];
		$tiposVehiculosXCentro[$centro][$mov["id_tipo_vehiculo"]] = $mov["tipo"];

		//pesos
		$tonMov = averiguarPesoXMov($mov["id"],"",true);
		if(!isset($ton[$centro][$mes])) $ton[$centro][$mes]=0;
		$ton[$centro][$mes]+=$tonMov;

		if(!isset($tonTipo[$centro][$mov["id_tipo_vehiculo"]][$mes])) $tonTipo[$centro][$mov["id_tipo_vehiculo"]][$mes]=0;
		$tonTipo[$centro][$mov["id_tipo_vehiculo"]][$mes]+=$tonMov;

		//se cuentan tripulaciones,  se supone que por tripulación va un conductor que es el cargo 21
		$qidPer = $db->sql_row("SELECT count(id) as num FROM rec.movimientos_personas WHERE id_movimiento=".$mov["id"]." AND cargo=21");
		if(!isset($per[$centro][$mes])) $per[$centro][$mes]=0;
			//$per[$centro][$mes]+=$qidPer["num"];
			$per[$centro][$mes]+=1;

		//costo de llantas por km recorrido
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
		foreach($turnos as $idTurno => $dx)
		{
			$vxm = averiguarViajeXMov($mov["id"], $idTurno);

			if(!isset($viajes[$centro][$mov["id_tipo_vehiculo"]][$mes])) $viajes[$centro][$mov["id_tipo_vehiculo"]][$mes]=0;
			$viajes[$centro][$mov["id_tipo_vehiculo"]][$mes] += $vxm;
			
			if(!isset($viajesXTurno[$centro][$dx["turno"]][$mes])) $viajesXTurno[$centro][$dx["turno"]][$mes] = 0;
			$viajesXTurno[$centro][$dx["turno"]][$mes] += $vxm;
		}
	}
}

function imprimirKmgl(&$fila, &$columna, $html, $centros, $tiposVehiculos, $tiposVehiculosXCentro, $fechas, $recorrido, $combustible, $estilos)
{
	global $workbook;
	global $worksheet;

	//preguntar($recorrido);

	foreach($tiposVehiculos as $id => $tipo)
	{
		$linea = array("Km/gl ".$tipo);
		$total = $numCentros = 0;
		foreach($centros as $idCentro => $daCen)
		{
			$totalCen = 0;
			foreach($fechas as $mes)
			{
				if(isset($tiposVehiculosXCentro[$idCentro][$id]))
				{
					@$val = nvl($recorrido[$idCentro][$id][$mes],0)/nvl($combustible[$idCentro][$id][$mes],0);
					$linea[] =  number_format($val,2);
					$totalCen+=$val;
				}
				else
					$linea[] = "";
			}
		
			$total += $totalCen/count($fechas);
			if(isset($tiposVehiculosXCentro[$idCentro][$id]))
				$numCentros+=1;
		}

		$linea[] = number_format(($total/$numCentros),2);

		if($html)
			imprimirLinea($linea,"",$estilos);
		else
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
	}
}


function imprimirEficiencia(&$fila, &$columna, $html, $centros, $tiposVehiculos, $tiposVehiculosXCentro, $fechas, $tonTipo, $viajes, $capacidad, $estilos)
{
	global $workbook;
	global $worksheet;

	//preguntar($tiposVehiculos);
	//preguntar($tonTipo);
	//preguntar($viajes);

	foreach($tiposVehiculos as $id => $tipo)
	{
		$linea = array("Ton/Viaje ".$tipo);
		$linea2 = array("Eficiencia %");
		$total = $totalEfi = $numCentros = 0;

		foreach($centros as $idCentro => $daCen)
		{
			$totalCen = $totalCenEfi = 0;
			foreach($fechas as $mes)
			{
				if(isset($tiposVehiculosXCentro[$idCentro][$id]))
				{
					@$val= nvl($tonTipo[$idCentro][$id][$mes],0)/nvl($viajes[$idCentro][$id][$mes],0);
					$linea[] =  number_format($val,2);
					$totalCen+=$val;

					$linea2[] = number_format(($val/$capacidad[$id]) * 100,2)."%";
					$totalCenEfi+=($val/$capacidad[$id]) * 100;
					
					if(!isset($eficiencia[$idCentro][$mes])) $eficiencia[$idCentro][$mes] = array("val"=>0, "tipos"=>0);
					$eficiencia[$idCentro][$mes]["val"] += ($val/$capacidad[$id])*100;
					$eficiencia[$idCentro][$mes]["tipos"] +=1;
				}else
				{
					$linea[] = "";
					$linea2[] = "";
				}
			}

			$total += $totalCen/count($fechas);
			$totalEfi += $totalCenEfi/count($fechas);
			if(isset($tiposVehiculosXCentro[$idCentro][$id]))
				$numCentros+=1;
		}

		$linea[] = number_format($total/$numCentros , 2);
		$linea2[] = number_format($totalEfi/$numCentros , 2)."%";

		if($html)
		{
			imprimirLinea($linea,"",$estilos);
			imprimirLinea($linea2,"",$estilos);
		}
		else
		{
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea2, array(1=>"txt_izq"));
		}
	}

	//preguntar($eficiencia);
	$linea = array("Eficiencia  % promedio flota");
	$granTotEfi = 0;
	foreach($centros as $idCentro => $dxCen)
	{
		$granTotEfiCen = 0;
		foreach($fechas as $mes)
		{
			$val = nvl($eficiencia[$idCentro][$mes]["val"],0)/nvl($eficiencia[$idCentro][$mes]["tipos"],0);
			$linea[] = number_format($val,2)."%";
			$granTotEfiCen += $val; 
		}

		$granTotEfi += $granTotEfiCen/count($fechas);
	}

	$linea[] = number_format(($granTotEfi/count($centros)), 2)."%";

	if($html)
		imprimirLinea($linea,"",$estilos);
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
}


function imprimirOtras(&$fila, &$columna, $html, $centros, $fechas, $nombreOtras, $otrasLineas, $estilos, $imprimirSolo = array(), $signoAd = "")
{
	global $workbook;
	global $worksheet;

	foreach($nombreOtras as $key => $nombre)
	{
		$sigue = false;
		if(count($imprimirSolo) == 0)
			$sigue = true;
		elseif(in_array($key, $imprimirSolo))
			$sigue = true;

		if($sigue)
		{
			$linea = array($nombre);
			$total = 0;

			foreach($centros as $idCentro => $da)
			{
				$totalCen = 0;
				foreach($fechas as $mes)
				{
					$val = nvl($otrasLineas[$key][$idCentro][$mes]);
					if($val != "")
					{
						$linea[] = number_format($val,2).$signoAd;
						$totalCen+=$val;
					}
					else
						$linea[] = "";
				}

				$totalCen = $totalCen/count($fechas);
				$total+=$totalCen;
			}

			$linea[] = number_format($total/count($centros),2).$signoAd;
			if($html)
				imprimirLinea($linea,"",$estilos);
			else
				imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
		}
	}
}

function imprimirTurnos($fila, $columna, $html, $centros, $fechas, $turnos, $viajesXTurno, $estilos)
{
	$keys = array("Mañana"=>"Mañana", "Tarde"=>"Tarde", "Noche"=>"Noche");

	//$totMan = $tot


	preguntar($viajesXTurno);

	foreach($keys as $tr)
	{
		$linea = array($tr);
		foreach($centros as $idCentros => $da)
		{
			foreach($fechas as $mes)
			{
				preguntar("mes ".$mes);
				$mes = explode("-",$mes);
				$dias = 	ultimoDia($mes[1], $mes[0]);
				preguntar("dias ".$dias);
	
			}
			


		}


		preguntar($linea);
	}





	//preguntar($turnos);
	//preguntar($keys);





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
