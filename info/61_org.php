<?
// error_reporting(E_ALL);
// ini_set("display_errors", 1);

// echo "<pre>";
// print_r($_POST);
// print_r($_GET);
// echo "</pre>"; 

include("../application.php");
$html = true;
$user=$_SESSION[$CFG->sesion]["user"];
$nivel=$_SESSION[$CFG->sesion]["user"]["nivel_acceso"];
$tipo_info = $_POST["order"];
if($tipo_info=="") {
	$tipo_info=1;
}

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
	include($CFG->dirroot."/info/templates/fechas_form_46.php");
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
$titulosFinanciera = array("FINANCIERA");
$titulosCliente = array("CLIENTE");
$titulosProceso = array("PROCESO");
$titulosInnovacion = array("INNOVACIÓN Y PERSONAL");

$estilostitulosFinanciera = array(1=>" height='30'  class='azul_osc_14'");
$estilos = array(1=>"align='left'  class='azul_osc_12'");
$i=2;

$qidCentro = $db->sql_query("SELECT id_centro, centro FROM personas_centros LEFT JOIN centros ON centros.id=personas_centros.id_centro WHERE id_persona='".$user["id"]."' ORDER BY id_centro");
while($queryCen = $db->sql_fetchrow($qidCentro)){
	$centros[$queryCen["id_centro"]] = $queryCen["centro"];
	$titulos[] = $queryCen["centro"];
	if ($tipo_info==1){
		foreach($fechas as $mes)
		{
			$titulosFinanciera[] = $titulosCliente[] = $titulosProceso[] = $titulosInnovacion[] = ucfirst(strftime("%b.%Y",strtotime($mes."-01")));
			$estilostitulosFinanciera[$i] = " class='azul_osc_14'";
			$estilos[$i] = "class='azul_osc_12'";
			$i++;
		}
	}
	else {
		$titulosFinanciera[]=$titulosCliente[]=$titulosProceso[]=$titulosInnovacion[] = $titulosIndFinan[] = $queryCen["centro"];
		$estilostitulosFinanciera[] = " class='azul_osc_14'";
		$estilos[] = "class='azul_osc_12'";
	}
}

$titulosFinanciera[]=$titulosCliente[]=$titulosProceso[]=$titulosInnovacion[] = $titulosIndFinan[] = "PROMEDIOS CONSOLIDADOS";
$estilostitulosFinanciera[] = " class='azul_osc_14'";
$estilos[] = "class='azul_osc_12'";

if ($tipo_info==1){
	$colspan = (count($fechas) * count($centros))+2;
	if($html)
	{
		$titulos[] = "Consolidados Promedios";
		$anchotabla = (120*(count($titulosFinanciera)-2))+430;
		if ($anchotabla<850) $anchotabla=850;
		echo '<table width="'.$anchotabla.'" border=1 bordercolor="#7fa840" align="center">
				<tr>';
		echo '	<th height="40" width="300" class="azul_osc_16">MENSUAL</th>';
		foreach($titulos as $tt){
			if ($tt=="Consolidados Promedios"){
				echo '<th height="40" width="120" class="azul_osc_16">'.$tt.'</th>';
			}
			else {
				echo '<th height="40" class="azul_osc_16" colspan='.count($fechas).'>'.$tt.'</th>';
			}
		}
		echo '</tr>';
		echo '<tr><td  height="30" colspan="'.$colspan.'" align="left" bgcolor="#b2d2e1" class="azul_osc_14">INDICADORES OPERACION</td></tr>';
		imprimirLinea($titulosFinanciera, "#b2d2e1", $estilostitulosFinanciera);
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
		
		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array("INDICADORES OPERACION"),array(1=>"azul_izq"), $colspan-1);
		$fila++;$columna=0;

		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosFinanciera, array(1=>"azul_izq"));	
		$fila++;$columna=0;
	}	
}
else {
	$colspan = count($centros)+2;
	if($html)
	{
		$titulos[] = "Consolidado Total";
		$anchotabla = (120*(count($titulosFinanciera)-2))+430;
		if ($anchotabla<800) $anchotabla=800;
		echo '<table width="'.$anchotabla.'" border=1 bordercolor="#7fa840" align="center">';
	
		echo '<tr><td  height="30" colspan="'.$colspan.'" align="left" bgcolor="#b2d2e1" class="azul_osc_14">INDICADORES OPERACIONALES</td></tr>';
		imprimirLinea($titulosFinanciera, "#b2d2e1", $estilostitulosFinanciera);
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
		
		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array("INDICADORES OPERACION"),array(1=>"azul_izq"), $colspan-1);
		$fila++;$columna=0;

		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosFinanciera, array(1=>"azul_izq"));	
		$fila++;$columna=0;
	}
}


##CALCULAMOS LOS INDICADORES
$tiposVehiculos = $tiposVehiculosXCentro = $capacidad = $recorrido = $combustible = $tonTipo = $viajes = $ton = $per = $kmsR = array();
$lineaTon = array("Toneladas recogidas");
$lineaTonprom = array("Toneladas recogidas");
$lineaCosTon = array("Costo / Toneladas Recolección");
$lineaCosTonMtto = array("Costos Mtto / Toneladas Recolección");
$lineaCosprom = array("Costo/Toneladas Recolección");
$lineaPers = array("Toneladas recogidas por  tripulacion");
$lineaLl = array("Costo de llantas por km recorrido ($/Km)");
$tonTOTAL = $cosTonTOTAL = $persTOTAL = $costoLlanTOTAL = $lineaInoperaTOTAL = 0;

##############################
#     Toneladas recogidas    #
##############################
consultatons($fila, $columna, $html, implode(",",array_keys($centros)), "rec", $inicio, $final, $estilos, "asc", $toneladas);
$lineaTon = array("Toneladas");
$cosTonTOTAL = 0;

##############################
# Costo Toneladas recogidas  #
##############################
foreach($centros as $idCentro => $da)
{
	$promCos = $numMes1 = $numMes = $totalCentro = 	$totalCentroCos = 0;
	foreach($fechas as $mes)
	{
		$lineaTon[] = number_format(nvl($toneladas[$idCentro][$mes],0),2);
		$totalCentro+=nvl($toneladas[$idCentro][$mes],0);
		#Consultamos el costo del mes		
		$qidCTRP = $db->sql_row("SELECT sum(c.valor) as val FROM costos c LEFT JOIN servicios s ON s.id=c.id_servicio 
		WHERE id_centro='".$idCentro."' AND esquema='rec' AND id_variable_informe = 1 AND fecha = '".$mes."'");
		@$costo=nvl($qidCTRP["val"],0)/nvl($toneladas[$idCentro][$mes],0);
		$lineaCosTon[] = number_format($costo,0);
		
		if ($costo!=0) {
			$totalCentroCos+=nvl($costo,0);
			$numMes1++;
		}
		if(isset($toneladas[$idCentro][$mes]))
			$numMes++;	
	}
	
	$promCen = $totalCentro/$numMes;
	$lineaTonprom[] = number_format($promCen,2);
	$promCos = $totalCentroCos/$numMes1;
	$lineaCosprom[] = number_format($promCos,0);
	
	$tonTOTAL += $promCen;
	if($promCos!=0) {
		$cosTonTOTAL += $promCos;
		$numCentro++;
	}
}
$lineaTon[] = number_format($tonTOTAL,2);
$lineaCosTon[] = number_format($cosTonTOTAL/$numCentro,0);
$lineaLl[] = number_format($costoLlanTOTAL/count($centros),2);
$lineaTonprom[] = number_format($tonTOTAL,2);
$lineaCosprom[] = number_format($cosTonTOTAL/$numCentro,2);
$lineaLl[] = number_format($costoLlanTOTAL/count($centros),2);

###################################
# Costo mtto Toneladas recogidas  #
###################################
foreach($centros as $idCentro => $da)
{
	$promCos = $numMes1 = $numMes = $totalCentro = 	$totalCentroCos = 0;
	foreach($fechas as $mes)
	{
		$totalCentro+=nvl($toneladas[$idCentro][$mes],0);
		#Consultamos el costo del mes		
		$qidCTRP = $db->sql_row("SELECT sum(c.valor) as val FROM costos c LEFT JOIN servicios s ON s.id=c.id_servicio 
		WHERE id_centro='".$idCentro."' AND esquema='rec' AND id_variable_informe = 10 AND fecha = '".$mes."'");
		@$costo=nvl($qidCTRP["val"],0)/nvl($toneladas[$idCentro][$mes],0);
		$lineaCosTonMtto[] = number_format($costo,0);
		
		if ($costo!=0) {
			$totalCentroCos+=nvl($costo,0);
			$numMes1++;
		}
		if(isset($toneladas[$idCentro][$mes]))
			$numMes++;	
	}
	
	$promCen = $totalCentro/$numMes;
	$promCos = $totalCentroCos/$numMes1;
	$lineaCosTonMtto[] = number_format($promCos,0);
	
	$tonTOTAL += $promCen;
	if($promCos!=0) {
		$cosTonTOTAL += $promCos;
		$numCentro++;
	}
}

##############################
#Calculamos la inoperatividad#
##############################
inoperatividad($fila, $columna, $html, implode(",",array_keys($centros)), "rec", $inicio, $final, $estilos, "asc",$diasinop,$vxdinop);
$lineaInopera = array("Inoperatividad Flota (Días Inoperativoos)");
$lineaInoperaProm = array("Inoperatividad Flota (Días Inoperativos)");
$InoTOTAL = 0;
foreach($centros as $idCentro => $da)
{
	$promInoCen = $numMes =  $totalInoCentro = 0;
	foreach($fechas as $mes)
			{
					$lineaInopera[] = number_format(nvl($diasinop[$idCentro][$mes],0),0);
					$totalInoCentro+=nvl($diasinop[$idCentro][$mes],0);
					$numMes++;	
			}
	$promInoCen = $totalInoCentro/$numMes;
	$lineaInoperaProm[] = number_format($promInoCen,0);
	$InoTOTAL += $promInoCen;
	$numCentro++;
}
$lineaInopera[] = number_format($InoTOTAL/count($centros),0);
$lineaInoperaProm[] = number_format($InoTOTAL/count($centros),0);

#Calcula cuantas toneladas recoje un vehiculo en el mes separando los turnos para sacar le promedio que sería igual al numero de tripulaciones
# y tambien hace el calculo de cuantos viajes por turno se realizan para saber el promedio de viajes por turno por día.
consuviajtontrip($fila, $columna, $html, implode(",",array_keys($centros)), "rec", $inicio, $final, $estilos, $orden, $tontrip, $viajtur);
$lineaTontrip = array("Toneladas Recogidas por Tripulación");
$lineaviajestur = array("Promedio Viajes realizados por Turno");
$lineaTontripprom = array("Toneladas Recogidas por Tripulación");
$lineaviajesturprom = array("Promedio Viajes realizados por Turno");
foreach($centros as $idCentro => $da)
{
	$promCen = $promCen1 = $numMes = $totalCentro =  $totalCentro1 = 0;
	foreach($fechas as $mes)
	{
		$lineaTontrip[] = number_format(nvl($tontrip[$idCentro][$mes],0),2);
		$lineaviajestur[] = number_format(nvl($viajtur[$idCentro][$mes],0),2);
		$totalCentro+=nvl($tontrip[$idCentro][$mes],0);
		$totalCentro1+=nvl($viajtur[$idCentro][$mes],0);
		$numMes++;	
	}
	$promCen = $totalCentro/$numMes;
	$lineaTontripprom[] = number_format($totalCentro/$numMes,2);
	$promCen1 = $totalCentro1/$numMes;
	$lineaviajesturprom[] = number_format($totalCentro1/$numMes,2);
	$PromTOTAL += $promCen;
	$PromTOTAL1 += $promCen1;

}
$lineaTontrip[] = number_format($PromTOTAL/count($centros),2);
$lineaviajestur[] = number_format($PromTOTAL1/count($centros),2);
$lineaTontripprom[] = number_format($PromTOTAL/count($centros),2);
$lineaviajesturprom[] = number_format($PromTOTAL1/count($centros),2);


$lineakmsbar = array("Km barridos");
$linea2costobar = array("Costo / Km Barrido");
$linea3kmbarope = array("Km barridos por operario");
$linea4bolsasope= array("Bolsas por Operario de Barrido");
$lineapromkmsbar = array("Km barridos");
$linea2promcostobar = array("Costo / Km Barrido");
$linea3promkmbarope = array("Km barridos por operario");
$linea4promkmsbar = array("Bolsas por Operario de Barrido");

$tiposVehiculos = $tiposVehiculosXCentro = $capacidad = $recorrido = $combustible = $tonTipo = $viajes = $ton = $per = array();
$kmTot = $cosKmTOTAL = $kmOpe = $bolOpe = 0;
$idVars = array(2,3,4,5,6);
foreach($centros as $idCentro => $da)
{
	$vars = array();
	$kmTotCen = $cosKmTOTALCen = $kmOpeCen = $bolOpeCen = $counmes = 0;
	foreach($fechas as $mes)
	{
		$qid3 = $db->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c LEFT JOIN servicios s ON s.id=c.id_servicio WHERE id_centro='".$idCentro."' AND esquema='bar' AND id_variable_informe IN (".implode(",",$idVars).") AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
		while($query = $db->sql_fetchrow($qid3))
		{
			$vars[$query["id_variable_informe"]][$mes] = $query["val"];
		}
		if(isset($vars[2][$mes]))
		{
			$lineakmsbar[] = number_format($vars[2][$mes],2);
			$kmTotCen+= $vars[2][$mes];
			$counmes = $counmes+1;
		}else
			$lineakmsbar[] = "";

		if(isset($vars[2][$mes]) && isset($vars[3][$mes]))
		{
			$val = nvl($vars[3][$mes],0)/nvl($vars[2][$mes],0);
			$linea2costobar[]=number_format($val,2);
			$cosKmTOTALCen+=$val;
		}
		else
			$linea2costobar[] = "";

		if(isset($vars[4][$mes]))
		{
			$val = nvl($vars[2][$mes],0)/nvl($vars[6][$mes],1);
			$linea3kmbarope[] = number_format($val,2);
			$kmOpeCen+= $val;
		}else
			$linea3kmbarope[] = "";

		if(isset($vars[5][$mes]))
		{
			$linea4bolsasope[] = number_format($vars[5][$mes],2);
			$bolOpeCen+= $vars[5][$mes];
		}else
			$linea4bolsasope[] = "";
	}
	$kmTotCen = $kmTotCen/$counmes;
	$lineapromkmsbar[] = number_format($kmTotCen,2);
	$kmTot+=$kmTotCen;
	
	$cosKmTOTALCen = $cosKmTOTALCen/count($fechas);
	$linea2promcostobar[] = number_format($cosKmTOTALCen,0);
	$cosKmTOTAL+=$cosKmTOTALCen;

	$kmOpeCen = $kmOpeCen/count($fechas);
	$linea3promkmbarope[] = number_format($kmOpeCen,2);
	$kmOpe+=$kmOpeCen;

	$bolOpeCen = $bolOpeCen/count($fechas);
	$linea4promkmsbar[] = number_format($bolOpeCen,2);
	$bolOpe+=$bolOpeCen;
}

$lineakmsbar[] = number_format($kmTot/count($centros),2);
$linea2costobar[] = number_format($cosKmTOTAL/count($centros),0);
$linea3kmbarope[] = number_format($kmOpe/count($centros),2);
$linea4bolsasope[] = number_format($bolOpe/count($centros),2);
$lineapromkmsbar[] = number_format($kmTot,2);
$linea2promcostobar[] = number_format($cosKmTOTAL/count($centros),0);
$linea3promkmbarope[] = number_format($kmOpe/count($centros),2);
$linea4promkmsbar[] = number_format($bolOpe/count($centros),2);

###############################################################################
#CONSULTAMOS LA INFORMACIÓN ENTREGADA POR FINANCIERA EN EL FORMATO ESTABLECIDO#
###############################################################################
$lineaCosllavent = array("Costos Llantas / Ventas Totales");
$lineamttoventas = array("Costos Mtto / Ventas Totales");
$lineaPresupuesto = array("Ejecución Presupuesto");
$lineaDisponibilidad = array("Disponibilidad Flota");
$lineaConfiabilidad = array("Confiabilidad Flota"	);
$lineaKilometrosafalla = array("Kilometros a falla");
$lineaGestiondeSolicitudes = array("Gestión de Solicitudes");
$linea7201 = array("Calidad de cumplimiento frecuencia de recolección");
$linea7202 = array("Calidad de cumplimiento horario de recolección"	);
$linea7203 = array("Calidad Técnica en la Recolección");
$lineaGestionPQRS = array("Gestión de PQRS");
$lineaRotacion = array("Rotación de Personal");
$lineaAusentismo = array("Ausentismo de Personal");

$lineaCosllaventprom = array("Costos Llantas / Ventas Totales");
$lineamttoventasprom = array("Costos Mtto / Ventas Totales");
$lineaPresupuestoprom = array("Ejecución Presupuesto");
$lineaDisponibilidadprom = array("Disponibilidad Flota");
$lineaConfiabilidadprom = array("Confiabilidad Flota"	);
$lineaKilometrosafallaprom = array("Kilometros a falla");
$lineaGestiondeSolicitudesprom = array("Gestión de Solicitudes");
$linea7201prom = array("Calidad de cumplimiento frecuencia de recolección");
$linea7202prom = array("Calidad de cumplimiento horario de recolección"	);
$linea7203prom = array("Calidad Técnica en la Recolección");
$lineaGestionPQRSprom = array("Gestión de PQRS");
$lineaRotacionprom = array("Rotación de Personal");
$lineaAusentismoprom = array("Ausentismo de Personal");


#ind financieros
$qidOV = $db->sql_query("SELECT * FROM variables_informes WHERE id >=27 AND id<=36 ORDER BY id");
while($queryOV = $db->sql_fetchrow($qidOV))
{
	$nombreOtrasDos[$queryOV["id"]] = $queryOV["variable"];
}
$cosMtto = $gastos = $numTa = $totEmp = $totFact = $totRecau = $totRC = $totUVD = $cosMO = $totindllanta =0;
foreach($centros as $idCentro => $da)
{
	$cosMttoCen = $gastosCen = $numTaCen = $totUsuCen = $totEmpCen = $totFactCen = $totRecauCen = $totRCCen = $totUVDCen = $cosMOCen = $totindllantacen= 0;
	$vars =  array();
	$totfec = 0;
	foreach($fechas as $mes)
	{
		$qid3 = $db->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c 
		LEFT JOIN servicios s ON s.id=c.id_servicio WHERE id_centro='".$idCentro."'  
		AND id_variable_informe >= 9 AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
		while($query = $db->sql_fetchrow($qid3))
		{
			$vars[$query["id_variable_informe"]][$mes] = $query["val"];
		}			

		if(isset($vars[50][$mes]))
		{
			$val = $vars[50][$mes];
			$linea7201[] = number_format($val,0);
			$linea7202[] = number_format($val,0);
			$linea7203[] = number_format($val,0);
			$lineaGestionPQRS[] = number_format($val,0);
			$lineaDisponibilidad[] = number_format($val,0);
			$lineaConfiabilidad[] = number_format($val,0);
			$lineaKilometrosafalla[] = number_format($val,0);
			$lineaGestiondeSolicitudes[] = number_format($val,0);
			$lineaPresupuesto[] = number_format($val,0);
			$lineaRotacion[] = number_format($val,0);
			$lineaAusentismo[] = number_format($val,0);
			$totEmpCen+= $val;
		}else{
			$linea7201[] = "";
			$linea7202[] = "";
			$linea7203[] = "";;
			$lineaGestionPQRS[] = "";
			$lineaDisponibilidad[] = "";
			$lineaConfiabilidad[] = "";
			$lineaKilometrosafalla[] = "";
			$lineaGestiondeSolicitudes[] = "";
			$lineaPresupuesto[] = "";
			$lineaRotacion[] = "";
			$lineaAusentismo[] = "";
		}
	}
	
}

#########################################	
#MOSTRAMOS LA INFORMACIÓN DE FINANCIEROS#
if($html)
{
	if ($tipo_info==1){
		imprimirLinea($lineaPresupuesto,"",$estilos);
		imprimirLinea($lineaCosTon,"",$estilos);
		imprimirLinea($linea2costobar,"",$estilos);
	}
	else{
		imprimirLinea($lineaPresupuestoprom,"",$estilos);
		imprimirLinea($lineaCosprom,"",$estilos);
		imprimirLinea($linea2promcostobar,"",$estilos);
	}
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaCosTon, array(1=>"txt_izq"));
}
#########################################

#########################################
#      IMPRIMO TITULOS DE CLIENTE       #
#########################################
if($html)
	imprimirLinea($titulosCliente, "#b2d2e1", $estilostitulosFinanciera);
else
{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosProceso, array(1=>"azul_izq"));
	$fila++;$columna=0;
}
#MOSTRAMOS LA INFORMACIÓN DE CLIENTE
if($html)
{
	if ($tipo_info==1){
		imprimirLinea($linea7201,"",$estilos);
		imprimirLinea($linea7202,"",$estilos);
		imprimirLinea($linea7203,"",$estilos);
		imprimirLinea($lineaGestionPQRS,"",$estilos);
	}
	else{
		imprimirLinea($linea7201prom,"",$estilos);
		imprimirLinea($linea7202prom,"",$estilos);
		imprimirLinea($linea7203prom,"",$estilos);
		imprimirLinea($lineaGestionPQRSprom,"",$estilos);
	}
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea7201, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea7202, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea7203, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaGestionPQRS, array(1=>"txt_izq"));
}
#########################################

#########################################
#        IMPRIMO TITULOS DE PROCESO     #
#########################################
if($html)
	imprimirLinea($titulosProceso, "#b2d2e1", $estilostitulosFinanciera);
else
{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosProceso, array(1=>"azul_izq"));
	$fila++;$columna=0;
}
#MOSTRAMOS LA INFORMACIÓN DE PROCESO
if($html)
{
	if ($tipo_info==1){
		imprimirLinea($lineaTon,"",$estilos);
	}
	else{
		imprimirLinea($lineaTonprom,"",$estilos);
	}
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaCosTon, array(1=>"txt_izq"));
}
#Eficiencia vehiculos
$lineaflot = array('Eficiencia % Promedio Flota');
consuEficiencia($fila, $columna, $html,implode(",",array_keys($centros)), "rec", $inicio, $final, $estilos, "asc", $eficiencia, $eficiencia1, $tpvhs,$tpvh);	
foreach($tpvhs as $tpv => $tp)
{
	$linea = array($tp);
	$lineaprom = array($tp);
	$efi= substr_replace("Ton/viaje", "Eficiencia %", $tp);	
	$linea1 = array($efi);
	$linea1prom = array($efi);
	$numCen = $total = $total1 = 0;
	foreach($centros as $idCentro => $da)
	{
		$promCen = $promCen1 = $totalCentro = $totalCentro1 = $numMes = 0;
		foreach($fechas as $mes)
		{
			if ($eficiencia[$tpv][$idCentro][$mes]!=0){
				$linea[] = number_format(nvl($eficiencia[$tpv][$idCentro][$mes],0),2)." / ". number_format(nvl($eficiencia1[$tpv][$idCentro][$mes],0),2)."%";
				$linea1[] = number_format(nvl($eficiencia1[$tpv][$idCentro][$mes],0),2)."%";}
			else{
				$linea[] = '';
				$linea1[] = '';}
			$totalCentro+=nvl($eficiencia[$tpv][$idCentro][$mes],0);
			$totalCentro1+=nvl($eficiencia1[$tpv][$idCentro][$mes],0);
			if(isset($eficiencia[$tpv][$idCentro][$mes]))
				$numMes++;
		}
		$promCen = $totalCentro/$numMes;
		if (in_array($tpv, $tpvh)) 
			if ($totalCentro!=0)
				$lineaprom[] = number_format($totalCentro/$numMes,1)." / ".number_format($totalCentro1/$numMes,1)."%";
			else
				$lineaprom[] ='';
		else 
			if ($totalCentro!=0)
				$lineaprom[] = number_format($totalCentro/$numMes,1);
			else
				$lineaprom[] ='';
		$promCen1 = $totalCentro1/$numMes;
		$linea1prom[] = number_format($totalCentro1/$numMes,2);
		
		if($totalCentro>0)
			$numCen++;
			$total += $promCen;
			if (in_array($tpv, $tpvh)) $total1 += $promCen1;
	}
	$linea[] = number_format(($total/$numCen),1);
	if (in_array($tpv, $tpvh)) 
		$lineaprom[] = number_format(($total/$numCen),1)." / ".number_format(($total1/$numCen),1)."%";
	else
		$lineaprom[] = number_format(($total/$numCen),1);
		
	$linea1[] = number_format(($total1/$numCen),1)."%";
	$linea1prom[] = number_format(($total1/$numCen),1)."%";
	
	if($html){
		if ($tipo_info==1){
			imprimirLinea($linea,"",$estilos);
		}
		else {
			imprimirLinea($lineaprom,"",$estilos);
		}
	}
	else{
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea1, array(1=>"txt_izq"));}
}	
unset($tpvhs);
if($html)
{
	if ($tipo_info==1){
		imprimirLinea($lineaTontrip,"",$estilos);
		imprimirLinea($lineaviajestur,"",$estilos);
		imprimirLinea($lineakmsbar,"",$estilos);
		imprimirLinea($linea2costobar,"",$estilos);
		imprimirLinea($linea3kmbarope,"",$estilos);
		imprimirLinea($linea4bolsasope,"",$estilos);
		
	}
	else {
		imprimirLinea($lineaTontripprom,"",$estilos);
		imprimirLinea($lineaviajesturprom,"",$estilos);
		imprimirLinea($lineapromkmsbar,"",$estilos);
		imprimirLinea($linea2promcostobar,"",$estilos);
		imprimirLinea($linea3promkmbarope,"",$estilos);
		imprimirLinea($linea4promkmsbar,"",$estilos);
	}
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaTontrip, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaviajestur, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineakmsbar, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea2costobar, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea3kmbarope, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea4bolsasope, array(1=>"txt_izq"));
}
#IMPRIMO TITULOS DE INNOVACIÓN
if($html)
	imprimirLinea($titulosInnovacion, "#b2d2e1", $estilostitulosFinanciera);
else
{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosProceso, array(1=>"azul_izq"));
	$fila++;$columna=0;
}
#MOSTRAMOS LA INFORMACIÓN DE INNOVACIÓN
if($html)
{
	if ($tipo_info==1){
		imprimirLinea($lineaRotacion,"",$estilos);
		imprimirLinea($lineaAusentismo,"",$estilos);
	}
	else{
		imprimirLinea($lineaRotacionprom,"",$estilos);
		imprimirLinea($lineaAusentismoprom,"",$estilos);
	}
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaRotacion, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaAusentismo, array(1=>"txt_izq"));
}

###########################################
##IMPRIMIMOS TITULOS PARA INDICADORES MTTO#
###########################################

if ($tipo_info==1){
	$colspan = (count($fechas) * count($centros))+2;
	if($html)
	{
		$anchotabla = (120*(count($titulosFinanciera)-2))+430;
		if ($anchotabla<850) $anchotabla=850;
		echo '<table width="'.$anchotabla.'" border=1 bordercolor="#7fa840" align="center">
				<tr>';
		echo '	<th height="40" width="300" class="azul_osc_16">MENSUAL</th>';
		foreach($titulos as $tt){
			if ($tt=="Consolidados Promedios"){
				echo '<th height="40" width="120" class="azul_osc_16">'.$tt.'</th>';
			}
			else {
				echo '<th height="40" class="azul_osc_16" colspan='.count($fechas).'>'.$tt.'</th>';
			}
		}
		echo '</tr>';
		echo '<tr><td  height="30" colspan="'.$colspan.'" align="left" bgcolor="#b2d2e1" class="azul_osc_14">INDICADORES MANTENIMIENTO</td></tr>';
		imprimirLinea($titulosFinanciera, "#b2d2e1", $estilostitulosFinanciera);
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
		
		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array("INDICADORES MANTENIMIENTO"),array(1=>"azul_izq"), $colspan-1);
		$fila++;$columna=0;

		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosFinanciera, array(1=>"azul_izq"));	
		$fila++;$columna=0;
	}	
}
else {
	$colspan = count($centros)+2;
	if($html)
	{
		$anchotabla = (120*(count($titulosFinanciera)-2))+430;
		if ($anchotabla<800) $anchotabla=800;
		echo '<table width="'.$anchotabla.'" border=1 bordercolor="#7fa840" align="center">';
	
		echo '<tr><td  height="30" colspan="'.$colspan.'" align="left" bgcolor="#b2d2e1" class="azul_osc_14">INDICADORES OPERACIONALES</td></tr>';
		imprimirLinea($titulosFinanciera, "#b2d2e1", $estilostitulosFinanciera);
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
		
		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array("INDICADORES MANTENIMIENTO"),array(1=>"azul_izq"), $colspan-1);
		$fila++;$columna=0;

		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosFinanciera, array(1=>"azul_izq"));	
		$fila++;$columna=0;
	}
}
	
#MOSTRAMOS LA INFORMACIÓN DE FINANCIEROS
if($html)
{
	if ($tipo_info==1){
		imprimirLinea($lineaPresupuesto,"",$estilos);
		imprimirLinea($lineamttoventas,"", $estilos);
		imprimirLinea($lineaCosTonMtto,"", $estilos);
	}
	else{
		imprimirLinea($lineaPresupuestoprom,"",$estilos);
		imprimirLinea($lineamttoventasprom,"", $estilos);
		imprimirLinea($lineaCosTonMttoprom,"", $estilos);
	}
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaPresupuesto, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineamttoventas, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaCosTonMtto, array(1=>"txt_izq"));
}

#IMPRIMO TITULOS DE CLIENTE
if($html)
	imprimirLinea($titulosCliente, "#b2d2e1", $estilostitulosFinanciera);
else
{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosProceso, array(1=>"azul_izq"));
	$fila++;$columna=0;
}
#MOSTRAMOS LA INFORMACIÓN DE CLIENTE
if($html)
{
	if ($tipo_info==1){
		imprimirLinea($lineaDisponibilidad,"",$estilos);
		imprimirLinea($lineaConfiabilidad,"",$estilos);
		imprimirLinea($lineaKilometrosafalla,"",$estilos);
		imprimirLinea($lineaGestiondeSolicitudes,"",$estilos);
	}
	else{
		imprimirLinea($lineaDisponibilidadprom,"",$estilos);
		imprimirLinea($lineaConfiabilidadprom,"",$estilos);
		imprimirLinea($lineaKilometrosafallaprom,"",$estilos);
		imprimirLinea($lineaGestiondeSolicitudesprom,"",$estilos);
	}
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaDisponibilidad, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaConfiabilidad, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaKilometrosafalla, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaGestiondeSolicitudes, array(1=>"txt_izq"));
}

#IMPRIMO TITULOS DE PROCESO
if($html)
	imprimirLinea($titulosProceso, "#b2d2e1", $estilostitulosFinanciera);
else
{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosProceso, array(1=>"azul_izq"));
	$fila++;$columna=0;
}
#MOSTRAMOS LA INFORMACIÓN DE PROCESO
if($html)
{
	if ($tipo_info==1){
		
	}
	else{
		
	}
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaCosTon, array(1=>"txt_izq"));
}
#Eficiencia Consumo de combustible Kilometros por Galon
consuKmgl($fila, $columna, $html, implode(",",array_keys($centros)), "rec", $inicio, $final, $estilos, "asc", $kmsgl, $tpvhs);
foreach($tpvhs as $tpv => $tp)
{
	$linea = array($tp);
	$lineaprom = array($tp);
	$numCen = $total = 0;
	foreach($centros as $idCentro => $da)
	{
		$promCen = $totalCentro = $numMes = 0;
		foreach($fechas as $mes)
		{
			if ($kmsgl[$tpv][$idCentro][$mes]!=0)
				$linea[] = number_format(nvl($kmsgl[$tpv][$idCentro][$mes],0),2);
			else 
				$linea[] = '';
			$totalCentro+=nvl($kmsgl[$tpv][$idCentro][$mes],0);
			if(isset($kmsgl[$tpv][$idCentro][$mes]))
				$numMes++;
		}
		$promCen = $totalCentro/$numMes;
		if ($promCen!=0)
			$lineaprom[] = number_format($totalCentro/$numMes,2);
		else 
			$lineaprom[]='';
		if($totalCentro>0)
			$numCen++;
			$total += $promCen;
	}
	
	$linea[] = number_format(($total/$numCen),2);
	$lineaprom[] = number_format(($total/$numCen),2);

	if($html){
		if ($tipo_info==1){
			imprimirLinea($linea,"",$estilos);}
		else {
			imprimirLinea($lineaprom,"",$estilos);}
	}
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
}	
unset($tpvhs);

if($html)
{
	if ($tipo_info==1){
		
	}
	else {
		
	}
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaTontrip, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaviajestur, array(1=>"txt_izq"));
}

#IMPRIMO TITULOS DE INNOVACIÓN
if($html)
	imprimirLinea($titulosInnovacion, "#b2d2e1", $estilostitulosFinanciera);
else
{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosProceso, array(1=>"azul_izq"));
	$fila++;$columna=0;
}
#MOSTRAMOS LA INFORMACIÓN DE INNOVACIÓN
if($html)
{
	if ($tipo_info==1){
		imprimirLinea($lineaRotacion,"",$estilos);
		imprimirLinea($lineaAusentismo,"",$estilos);
	}
	else{
		imprimirLinea($lineaRotacionprom,"",$estilos);
		imprimirLinea($lineaAusentismoprom,"",$estilos);
	}
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaRotacion, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaAusentismo, array(1=>"txt_izq"));
}




########################################
##seccion doonde estan las /*funciones*/
########################################
function consultatons(&$fila, &$columna, $html, $idCentro, $servicio, $inicio, $final, $estilos, $orden, &$toneladas)
{
	global $db, $CFG, $ME;
	$strQuery="select date_part('year',m.inicio)||'-'||
				CASE WHEN date_part('month',m.inicio)=1 THEN '01'
					WHEN date_part('month',m.inicio)=2 THEN '02'
					WHEN date_part('month',m.inicio)=3 THEN '03'
					WHEN date_part('month',m.inicio)=4 THEN '04'
					WHEN date_part('month',m.inicio)=5 THEN '05'
					WHEN date_part('month',m.inicio)=6 THEN '06'
					WHEN date_part('month',m.inicio)=7 THEN '07'
					WHEN date_part('month',m.inicio)=8 THEN '08'
					WHEN date_part('month',m.inicio)=9 THEN '09'
					WHEN date_part('month',m.inicio)=10 THEN '10'
					WHEN date_part('month',m.inicio)=11 THEN '11'
					WHEN date_part('month',m.inicio)=12 THEN '12'	
				END as fecha,
				c.id,c.centro,(sum(peso_total*porcentaje)/100) as toneladas
				from rec.pesos p
				LEFT JOIN rec.movimientos_pesos mp ON p.id=mp.id_peso
				LEFT JOIN rec.movimientos m on mp.id_movimiento=m.id
				LEFT JOIN vehiculos vh ON m.id_vehiculo=vh.id
				LEFT JOIN micros i ON i.id=m.id_micro
				LEFT JOIN servicios s ON s.id = i.id_servicio
				LEFT JOIN centros c ON vh.id_centro=c.id
				LEFT JOIN tipos_vehiculos tvh ON vh.id_tipo_vehiculo=tvh.id
				WHERE tvh.id in (1,2,4,8,11,14,15,19,21,27,28,29) and inicio::date >= '$inicio' AND inicio::date<='$final' and c.id in ($idCentro)
				group by date_part('year',m.inicio)||'-'||
				CASE WHEN date_part('month',m.inicio)=1 THEN '01'
					WHEN date_part('month',m.inicio)=2 THEN '02'
					WHEN date_part('month',m.inicio)=3 THEN '03'
					WHEN date_part('month',m.inicio)=4 THEN '04'
					WHEN date_part('month',m.inicio)=5 THEN '05'
					WHEN date_part('month',m.inicio)=6 THEN '06'
					WHEN date_part('month',m.inicio)=7 THEN '07'
					WHEN date_part('month',m.inicio)=8 THEN '08'
					WHEN date_part('month',m.inicio)=9 THEN '09'
					WHEN date_part('month',m.inicio)=10 THEN '10'
					WHEN date_part('month',m.inicio)=11 THEN '11'
					WHEN date_part('month',m.inicio)=12 THEN '12'	
				END,c.id,c.centro
				order by 2,3";
				$qidpesos = $db->sql_query($strQuery);
	
	while($pesos= $db->sql_fetchrow($qidpesos))
	{
		$toneladas[$pesos["id"]][$pesos["fecha"]]+=$pesos["toneladas"];
		$i++;
	}
}

function consuKmgl(&$fila, &$columna, $html, $idCentro, $servicio, $inicio, $final, $estilos, $orden, &$kmsgl, &$tpvhs)
{
	global $db, $CFG, $ME;
	global $workbook;
	global $worksheet;
	if ($servicio=="rec") $tpvh = "2,8,14,15,1,19,4,29,21";
	$sqlkmsgls = "SELECT date_part('year',m.inicio)||'-'||
				CASE WHEN date_part('month',m.inicio)=1 THEN '01'
					WHEN date_part('month',m.inicio)=2 THEN '02'
					WHEN date_part('month',m.inicio)=3 THEN '03'
					WHEN date_part('month',m.inicio)=4 THEN '04'
					WHEN date_part('month',m.inicio)=5 THEN '05'
					WHEN date_part('month',m.inicio)=6 THEN '06'
					WHEN date_part('month',m.inicio)=7 THEN '07'
					WHEN date_part('month',m.inicio)=8 THEN '08'
					WHEN date_part('month',m.inicio)=9 THEN '09'
					WHEN date_part('month',m.inicio)=10 THEN '10'
					WHEN date_part('month',m.inicio)=11 THEN '11'
					WHEN date_part('month',m.inicio)=12 THEN '12'	
				END as fecha,c.id,c.centro,vh.id_tipo_vehiculo as tpvehi,tvh.tipo, SUM(m.km_final-d.kms) as kmsrec, sum(m.combustible),SUM(m.km_final-d.kms)/sum(m.combustible) AS KMGL 
				FROM rec.movimientos m
				LEFT JOIN (select des.id_movimiento,des.km as kms from (select id_movimiento,min(hora_inicio)as hora_inicio from rec.desplazamientos group by id_movimiento)desp
				LEFT JOIN rec.desplazamientos des on  (des.id_movimiento=desp.id_movimiento and des.hora_inicio=desp.hora_inicio)
				group  by des.id_movimiento,des.km) as d ON m.id=d.id_movimiento
				LEFT JOIN vehiculos vh ON m.id_vehiculo=vh.id
				LEFT JOIN micros i ON i.id=m.id_micro
				LEFT JOIN servicios s ON s.id = i.id_servicio
				LEFT JOIN centros c ON vh.id_centro=c.id
				LEFT JOIN tipos_vehiculos tvh ON vh.id_tipo_vehiculo=tvh.id
				WHERE tvh.id in ($tpvh) AND inicio::date >= '$inicio' AND inicio::date<='$final' and c.id in ($idCentro)
				GROUP BY date_part('year',m.inicio)||'-'||
				CASE WHEN date_part('month',m.inicio)=1 THEN '01'
					WHEN date_part('month',m.inicio)=2 THEN '02'
					WHEN date_part('month',m.inicio)=3 THEN '03'
					WHEN date_part('month',m.inicio)=4 THEN '04'
					WHEN date_part('month',m.inicio)=5 THEN '05'
					WHEN date_part('month',m.inicio)=6 THEN '06'
					WHEN date_part('month',m.inicio)=7 THEN '07'
					WHEN date_part('month',m.inicio)=8 THEN '08'
					WHEN date_part('month',m.inicio)=9 THEN '09'
					WHEN date_part('month',m.inicio)=10 THEN '10'
					WHEN date_part('month',m.inicio)=11 THEN '11'
					WHEN date_part('month',m.inicio)=12 THEN '12'	
				END,c.id,c.centro,vh.id_tipo_vehiculo,tvh.tipo,tvh.orden_info
				order by tvh.orden_info,2,1 $orden";
		$qidkmsgls = $db->sql_query($sqlkmsgls);
		$tpvh= '';
		
 		while($kmgls = $db->sql_fetchrow($qidkmsgls))
		{
			if ($tpvh==$kmgls["tpvehi"]){
				$kmsgl[$kmgls["tpvehi"]][$kmgls["id"]][$kmgls["fecha"]]+=number_format($kmgls["kmgl"],2);
			}
			else {
				$kmsgl[$kmgls["tpvehi"]][$kmgls["id"]][$kmgls["fecha"]]+=number_format($kmgls["kmgl"],2);
				$tpvhs[$kmgls["tpvehi"]] = "Km/gl ".$kmgls["tipo"];
			}
			$i++;
		} 
}

function consuEficiencia(&$fila, &$columna, $html, $idCentro, $servicio, $inicio, $final, $estilos, $orden, &$eficiencia, &$eficiencia1, &$tpvhs, &$tpvh)
{
	global $db, $CFG, $ME;
	global $workbook;
	global $worksheet;	
	if ($servicio=="rec") $tpvh = "2,8,14,15,1,19,4,29,21";
	$tpvh2=$tpvh;
	$tpvh = explode(",", $tpvh);
	
	$sqlefic = "SELECT date_part('year',mov.inicio)||'-'||
					CASE WHEN date_part('month',mov.inicio)=1 THEN '01'
						WHEN date_part('month',mov.inicio)=2 THEN '02'
						WHEN date_part('month',mov.inicio)=3 THEN '03'
						WHEN date_part('month',mov.inicio)=4 THEN '04'
						WHEN date_part('month',mov.inicio)=5 THEN '05'
						WHEN date_part('month',mov.inicio)=6 THEN '06'
						WHEN date_part('month',mov.inicio)=7 THEN '07'
						WHEN date_part('month',mov.inicio)=8 THEN '08'
						WHEN date_part('month',mov.inicio)=9 THEN '09'
						WHEN date_part('month',mov.inicio)=10 THEN '10'
						WHEN date_part('month',mov.inicio)=11 THEN '11'
						WHEN date_part('month',mov.inicio)=12 THEN '12'	
					END as fecha,
					c.id,c.centro,pesos.id_tipo_vehiculo as tpvehi,pesos.tipo,
					avg(pesos.capacidad)as capacidad,avg(eficiencia) as eficiencia, avg(pesos.pes_viaje) as pesviaje,sum(pesos.viajedes) as viajes 
					FROM  rec.movimientos mov LEFT JOIN
						(select p.id,p.peso_inicial,p.peso_final,p.peso_total, v.id_tipo_vehiculo,tv.tipo,tv.orden_info,tv.capacidad,min(id_movimiento) as id_movimiento,
						(p.peso_total/tv.capacidad)*100 as eficiencia, avg(p.peso_total) as pes_viaje, 1 as viajedes
						from rec.pesos p
						LEFT JOIN vehiculos v ON p.id_vehiculo=v.id
						LEFT JOIN rec.movimientos_pesos mp ON p.id=mp.id_peso
						LEFT JOIN tipos_vehiculos tv ON v.id_tipo_vehiculo=tv.id
						WHERE tv.id in (1,2,4,8,11,14,15,19,21,29) 
						GROUP BY p.id,p.peso_inicial,p.peso_final,p.peso_total,v.id_tipo_vehiculo,v.id_centro,tv.tipo,tv.orden_info,tv.capacidad,tv.capacidad) as pesos
						ON mov.id=pesos.id_movimiento
					LEFT JOIN vehiculos vh ON mov.id_vehiculo=vh.id
					LEFT JOIN micros i ON i.id=mov.id_micro
					LEFT JOIN servicios s ON s.id = i.id_servicio
					LEFT JOIN centros c ON vh.id_centro=c.id
					WHERE pesos.id_tipo_vehiculo in ($tpvh2) and inicio::date >= '$inicio' AND inicio::date<='$final' and c.id in ($idCentro)
					GROUP BY date_part('year',mov.inicio)||'-'||
					CASE WHEN date_part('month',mov.inicio)=1 THEN '01'
						WHEN date_part('month',mov.inicio)=2 THEN '02'
						WHEN date_part('month',mov.inicio)=3 THEN '03'
						WHEN date_part('month',mov.inicio)=4 THEN '04'
						WHEN date_part('month',mov.inicio)=5 THEN '05'
						WHEN date_part('month',mov.inicio)=6 THEN '06'
						WHEN date_part('month',mov.inicio)=7 THEN '07'
						WHEN date_part('month',mov.inicio)=8 THEN '08'
						WHEN date_part('month',mov.inicio)=9 THEN '09'
						WHEN date_part('month',mov.inicio)=10 THEN '10'
						WHEN date_part('month',mov.inicio)=11 THEN '11'
						WHEN date_part('month',mov.inicio)=12 THEN '12'	
					END,
					c.id,c.centro,pesos.id_tipo_vehiculo,pesos.tipo,pesos.orden_info
				order by pesos.orden_info $orden,2,1 ";
		$qidefic = $db->sql_query($sqlefic);
 		while($efic = $db->sql_fetchrow($qidefic))
		{		
			if ($tpvh==$kmgls["tpvehi"]){
				$eficiencia[$efic["tpvehi"]][$efic["id"]][$efic["fecha"]]+=number_format($efic["pesviaje"],2);
				$eficiencia1[$efic["tpvehi"]][$efic["id"]][$efic["fecha"]]+=number_format($efic["eficiencia"],2);
			}
			else {
				$eficiencia[$efic["tpvehi"]][$efic["id"]][$efic["fecha"]]+=number_format($efic["pesviaje"],2);
				$eficiencia1[$efic["tpvehi"]][$efic["id"]][$efic["fecha"]]+=number_format($efic["eficiencia"],2);
				$tpvhs[$efic["tpvehi"]] = "Ton/viaje ".$efic["tipo"];
			}
			$i++;
		} 
}

function consuviajtontrip(&$fila, &$columna, $html, $idCentro, $servicio, $inicio, $final, $estilos, $orden, &$tontrip, &$viajtur)
{
	global $db, $CFG, $ME;
	global $workbook;
	global $worksheet;	

	$tpvh = explode(",", $tpvh);	
	$sqlefic = "select fecha,centro,id,sum(tontripula),avg(promviajes) as promviajes, max(trips),(sum(tontripula)/max(trips)) as tontripula from
(select fecha,fecmov,id,centro,sum(tontrip) as tontripula, avg(viadia) as promviajes, count(*) as trips from
	(select id,centro,fecha,fecmov,codigo,turno,sum(tondia) as tontrip, avg(viajdia) as viadia, 1 as trip from
		(SELECT mov.id as idmov, mov.id_micro,date_part('year',mov.inicio)||'-'||
				CASE WHEN date_part('month',mov.inicio)=1 THEN '01'
					WHEN date_part('month',mov.inicio)=2 THEN '02'
					WHEN date_part('month',mov.inicio)=3 THEN '03'
					WHEN date_part('month',mov.inicio)=4 THEN '04'
					WHEN date_part('month',mov.inicio)=5 THEN '05'
					WHEN date_part('month',mov.inicio)=6 THEN '06'
					WHEN date_part('month',mov.inicio)=7 THEN '07'
					WHEN date_part('month',mov.inicio)=8 THEN '08'
					WHEN date_part('month',mov.inicio)=9 THEN '09'
					WHEN date_part('month',mov.inicio)=10 THEN '10'
					WHEN date_part('month',mov.inicio)=11 THEN '11'
					WHEN date_part('month',mov.inicio)=12 THEN '12'	
				END as fecha,c.id,c.centro,
				inicio::date as fecmov, mov.id_vehiculo,mov.id_turno,tur.turno,vh.codigo,sum(pesos.peso_total) as tondia,sum(pesos.viajedes) as viajdia
			FROM  rec.movimientos mov LEFT JOIN
				(select p.id,p.peso_inicial,p.peso_final,p.peso_total, v.id_tipo_vehiculo,tv.tipo,tv.capacidad,min(id_movimiento) as id_movimiento, 1 as viajedes
				from rec.pesos p
				LEFT JOIN vehiculos v ON p.id_vehiculo=v.id
				LEFT JOIN rec.movimientos_pesos mp ON p.id=mp.id_peso
				LEFT JOIN tipos_vehiculos tv ON v.id_tipo_vehiculo=tv.id
				WHERE tv.id in (1,2,4,8,11,14,15,19,21,29) 
				GROUP BY p.id,p.peso_inicial,p.peso_final,p.peso_total,v.id_tipo_vehiculo,v.id_centro,tv.tipo,tv.capacidad,tv.capacidad) as pesos
				ON mov.id=pesos.id_movimiento
			LEFT JOIN vehiculos vh ON mov.id_vehiculo=vh.id
			LEFT JOIN micros i ON i.id=mov.id_micro
			LEFT JOIN servicios s ON s.id = i.id_servicio
			LEFT JOIN centros c ON vh.id_centro=c.id
			LEFT JOIN tipos_vehiculos tvh ON vh.id_tipo_vehiculo=tvh.id
			LEFT JOIN turnos tur ON tur.id=mov.id_turno
			WHERE tvh.id in (1,2,4,8,11,14,15,19,21,29) and inicio::date >= '$inicio' AND inicio::date<='$final' and c.id in ($idCentro)
			group by  mov.id, mov.id_micro,date_part('year',mov.inicio)||'-'||
				CASE WHEN date_part('month',mov.inicio)=1 THEN '01'
					WHEN date_part('month',mov.inicio)=2 THEN '02'
					WHEN date_part('month',mov.inicio)=3 THEN '03'
					WHEN date_part('month',mov.inicio)=4 THEN '04'
					WHEN date_part('month',mov.inicio)=5 THEN '05'
					WHEN date_part('month',mov.inicio)=6 THEN '06'
					WHEN date_part('month',mov.inicio)=7 THEN '07'
					WHEN date_part('month',mov.inicio)=8 THEN '08'
					WHEN date_part('month',mov.inicio)=9 THEN '09'
					WHEN date_part('month',mov.inicio)=10 THEN '10'
					WHEN date_part('month',mov.inicio)=11 THEN '11'
					WHEN date_part('month',mov.inicio)=12 THEN '12'	
				END,c.id,c.centro,inicio::date, mov.id_vehiculo,mov.id_turno,tur.turno,vh.codigo
			order by 10,6,9) res
	group by id,centro,fecha,fecmov,codigo,turno	
	order by 2,1) final
group by fecha,fecmov,id,centro) as adf
group by fecha,id,centro
order by 2,1";
		$qidefic = $db->sql_query($sqlefic);
 		while($efic = $db->sql_fetchrow($qidefic))
		{		
			$tontrip[$efic["id"]][$efic["fecha"]]+=number_format($efic["tontripula"],2);
			$viajtur[$efic["id"]][$efic["fecha"]]+=number_format($efic["promviajes"],2);
			$i++;
		} 
}

function inoperatividad(&$fila, &$columna, $html, $idCentro, $servicio, $inicio, $final, $estilos, $orden, &$diasinop, &$vxdinop)
{
	global $db, $CFG, $ME;
	global $workbook;
	global $worksheet;	

	$sqlefic = "select id_centro,date_part('year',TB.dia)||'-'||
				CASE WHEN date_part('month',TB.dia)=1 THEN '01'
					WHEN date_part('month',TB.dia)=2 THEN '02'
					WHEN date_part('month',TB.dia)=3 THEN '03'
					WHEN date_part('month',TB.dia)=4 THEN '04'
					WHEN date_part('month',TB.dia)=5 THEN '05'
					WHEN date_part('month',TB.dia)=6 THEN '06'
					WHEN date_part('month',TB.dia)=7 THEN '07'
					WHEN date_part('month',TB.dia)=8 THEN '08'
					WHEN date_part('month',TB.dia)=9 THEN '09'
					WHEN date_part('month',TB.dia)=10 THEN '10'
					WHEN date_part('month',TB.dia)=11 THEN '11'
					WHEN date_part('month',TB.dia)=12 THEN '12'	
				END as fecha, sum(dispon-operaron) as inopera, count(TB.dia) as diasop, sum(dispon-operaron)/count(TB.dia) as vxd
				 from
				(select dia,id,centro,count(id_vehiculo)as operaron from 
					(select dia,id_vehiculo,codigo,fecha,id,centro from
							(SELECT date(m.inicio)as dia,m.id_vehiculo,vh.codigo,date_part('year',m.inicio)||'-'||
							CASE WHEN date_part('month',m.inicio)=1 THEN '01'
								WHEN date_part('month',m.inicio)=2 THEN '02'
								WHEN date_part('month',m.inicio)=3 THEN '03'
								WHEN date_part('month',m.inicio)=4 THEN '04'
								WHEN date_part('month',m.inicio)=5 THEN '05'
								WHEN date_part('month',m.inicio)=6 THEN '06'
								WHEN date_part('month',m.inicio)=7 THEN '07'
								WHEN date_part('month',m.inicio)=8 THEN '08'
								WHEN date_part('month',m.inicio)=9 THEN '09'
								WHEN date_part('month',m.inicio)=10 THEN '10'
								WHEN date_part('month',m.inicio)=11 THEN '11'
								WHEN date_part('month',m.inicio)=12 THEN '12'	
							END as fecha,c.id,c.centro,vh.id_tipo_vehiculo as tpvehi,tvh.tipo
							FROM rec.movimientos m
							LEFT JOIN vehiculos vh ON m.id_vehiculo=vh.id
							LEFT JOIN centros c ON vh.id_centro=c.id
							LEFT JOIN tipos_vehiculos tvh ON vh.id_tipo_vehiculo=tvh.id
							WHERE tvh.id in (1,2,4,8,11,14,15,19,21,29) AND EXTRACT(DOW FROM inicio)<>0 AND inicio::date >= '$inicio' AND inicio::date<='$final' and c.id in ($idCentro))operacion
					group by dia,id_vehiculo,codigo,fecha,id,centro) as operativos
					
				where EXTRACT(DOW FROM dia)<>0
				group by dia,id,centro	
				order by 1) as TB
			left join
				(select id_centro,dia,count(codigo)as dispon from
					(select  vh.id,vh.id_centro,vh.codigo,dias.*
					from (select date(inicio)as dia from  rec.movimientos where EXTRACT(DOW FROM inicio)<>0 AND inicio::date >= '$inicio' AND inicio::date<='$final'
						group by 1 order by 1) as dias
					, vehiculos vh
					LEFT JOIN tipos_vehiculos tvh ON vh.id_tipo_vehiculo=tvh.id	 
					where id_centro in ($idCentro) and tvh.id in (1,2,4,8,11,14,15,19,21,29) and vh.id_estado<>4
					order by 3,4) as total
				group by id_centro,dia) as TT
			on TB.id=TT.id_centro and TB.dia=TT.dia
			group by id_centro,date_part('year',TB.dia)||'-'||
				CASE WHEN date_part('month',TB.dia)=1 THEN '01'
					WHEN date_part('month',TB.dia)=2 THEN '02'
					WHEN date_part('month',TB.dia)=3 THEN '03'
					WHEN date_part('month',TB.dia)=4 THEN '04'
					WHEN date_part('month',TB.dia)=5 THEN '05'
					WHEN date_part('month',TB.dia)=6 THEN '06'
					WHEN date_part('month',TB.dia)=7 THEN '07'
					WHEN date_part('month',TB.dia)=8 THEN '08'
					WHEN date_part('month',TB.dia)=9 THEN '09'
					WHEN date_part('month',TB.dia)=10 THEN '10'
					WHEN date_part('month',TB.dia)=11 THEN '11'
					WHEN date_part('month',TB.dia)=12 THEN '12'	
				END 
			order by 1,2";
		$qidefic = $db->sql_query($sqlefic);
 		while($efic = $db->sql_fetchrow($qidefic))
		{		
			$diasinop[$efic["id_centro"]][$efic["fecha"]]+=number_format($efic["inopera"],0);
			$vxdinop[$efic["id_centro"]][$efic["fecha"]]+=number_format($efic["vxd"],2);
			$i++;
		}
}



#final
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
