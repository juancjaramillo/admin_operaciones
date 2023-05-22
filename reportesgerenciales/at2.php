<?
// error_reporting(E_ALL);
// ini_set("display_errors", 1);

// echo "<pre>";
// print_r($_POST);
// print_r($_GET);
// echo "</pre>"; 

include("../applicationreports.php");
$html = true;
$user=$_SESSION[$CFG->sesion]["user"];
$nivel=$_SESSION[$CFG->sesion]["user"]["nivel_acceso"];
$tipo_info = $_GET["tipo_info"];
if($tipo_info==""){
	$tipo_info=1;
}

if(isset($_GET["format"])) 
{
	$html=false;
	$inicio = $_GET["inicio"];
	$final  = $_GET["final"];
}
$titulo1["inf"]="INDICADORES GERENCIALES : INDICADORES BARRIDO";
if($html)
{
	include($CFG->dirroot."/templates/header_popup.php");	
	if ($tipo_info==1) tabla_titulos_reportes($titulo1["inf"]);
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
$inicio = $_GET["inicio"];
$final = $_GET["final"]; 
$fechas = sacarMeses($inicio, $final);
$titulos = array();
$titulosFinanciera = array("FINANCIERA");
$titulosPersonal = array("PERSONAL RRHH");
$titulosVolumenes = array("VOLUMENES");
$titulosEficiencia = array("EFICIENCIA");
$centrosnacional = array();
$centrospromo = array();
$estilostitulosFinanciera = array(1=>" height='30'  class='azul_osc_14'");
$estilos = array(1=>"align='left'  class='azul_osc_12'");
$i=2;

/****BUSCA EL NUMERO DOCUMENTO EN LA BASE DE DATOS LOCAL ***/
$qiddocumento = $db->sql_query("SELECT id, cedula FROM personas WHERE id='".$user["id"]."'");
$querydocumento = $db->sql_fetchrow($qiddocumento);
$documento= $querydocumento["cedula"];

/*** CON EL NUMERO DE DOCUMENTO BUSCA EL ID EN LA BASE DE DATOS DE CONSULTA DE REPORTES  ***/
$qidCentro = $dbnacionalproduccion->sql_query("SELECT id_centro, centro FROM personas_centros LEFT JOIN centros ON centros.id=personas_centros.id_centro 
LEFT JOIN personas ON personas.id=personas_centros.id_persona  WHERE cedula='".$documento."' and id_centro in (SELECT DISTINCT(id_centro) as id_centro from costos)
ORDER BY id_centro");

//Consulta a base de datos de promoambiental
$qidCentro2 = $dbpromo->sql_query("SELECT id_centro, centro FROM personas_centros LEFT JOIN centros ON centros.id=personas_centros.id_centro 
LEFT JOIN personas ON personas.id=personas_centros.id_persona  WHERE cedula='".$documento."' and id_centro in (SELECT DISTINCT(id_centro) as id_centro from costos)
ORDER BY id_centro");


while($queryCen = $db->sql_fetchrow($qidCentro)){
	$centros[$queryCen["id_centro"]] = $queryCen["centro"];
	
	/****CAPTURA EL VALOR DE CENTROS CON PERMISOS DE USUARIOS EN PROMO NACIONAL	****/
	$centrosnacional[$queryCen["id_centro"]] = $queryCen["centro"];
	
	$titulos[] = $queryCen["centro"];
	if ($tipo_info==1){
		foreach($fechas as $mes)
		{
			$titulosFinanciera[] = $titulosVolumenes[] = $titulosEficiencia[]= ucfirst(strftime("%b.%Y",strtotime($mes."-01")));
			$estilostitulosFinanciera[$i] = " class='azul_osc_14'";
			$estilos[$i] = "class='azul_osc_12'";
			$i++;
		}
	}
	else {
		$titulosFinanciera[] = $titulosVolumenes[] = $titulosEficiencia[] = $titulosIndFinan[] = $queryCen["centro"]. " Consolidado";
		$titulosFinanciera[] = $titulosVolumenes[] = $titulosEficiencia[] = $titulosIndFinan[] = "Ultimo Mes";
		$estilostitulosFinanciera[] = " class='azul_osc_14'";
		$estilos[] = "class='azul_osc_12'";
		$estilostitulosFinanciera[] = " class='azul_osc_14'";
		$estilos[] = "class='azul_osc_12'";
	}
}

while($queryCen = $dbpromo->sql_fetchrow($qidCentro2)){	
	$centros[$queryCen["id_centro"]] = $queryCen["centro"];
	
/****CAPTURA EL VALOR DE CENTROS CON PERMISOS DE USUARIOS EN PROMODISTRITO	****/
	$centrospromo[$queryCen["id_centro"]] = $queryCen["centro"];
	
	$titulos[] = $queryCen["centro"];	
	if ($tipo_info==1){
		foreach($fechas as $mes)
		{			
			$titulosFinanciera[] = $titulosVolumenes[] = $titulosEficiencia[] /*= $titulosPersonal[]*/ = ucfirst(strftime("%b.%Y",strtotime($mes."-01")));
			$estilostitulosFinanciera[$i] = " class='azul_osc_14'";
			$estilos[$i] = "class='azul_osc_12'";
			$i++;
		}
	}
	else {
		$titulosFinanciera[] = $titulosVolumenes[] = $titulosEficiencia[]  = $titulosIndFinan[] = $queryCen["centro"]. " Consolidado";
		$titulosFinanciera[] = $titulosVolumenes[] = $titulosEficiencia[]  = $titulosIndFinan[] = "Ultimo Mes";
		$estilostitulosFinanciera[] = " class='azul_osc_14'";
		$estilos[] = "class='azul_osc_12'";
		$estilostitulosFinanciera[] = " class='azul_osc_14'";
		$estilos[] = "class='azul_osc_12'";
	}
}
$centrosnacionales = $centrosnacional + $centrospromo;
$titulosFinanciera[]=$titulosVolumenes[]=$titulosEficiencia[]/*=$titulosPersonal[]*/ = $titulosIndFinan[] = "PROMEDIOS CONSOLIDADOS";
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
		
		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array("INDICADORES OPERACIÓN"),array(1=>"azul_izq"), $colspan-1);
		$fila++;$columna=0;

		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosFinanciera, array(1=>"azul_izq"));	
		$fila++;$columna=0;
	}	
}
else {
	$colspan = (count($centros)*2)+2;
	if($html)
	{
		$titulos[] = "Consolidado Total";
		$anchotabla = (120*(count($titulosFinanciera)-2))+430;
		if ($anchotabla<800) $anchotabla=800;
		echo '<table width="'.$anchotabla.'" border=1 bordercolor="#7fa840" align="center">';
		echo '<tr><td  height="30" colspan="'.$colspan.'" align="left" bgcolor="#b2d2e1" class="azul_osc_14">'.$titulo1["inf"].'</td></tr>';
		#echo '<tr><td  height="30" colspan="'.$colspan.'" align="left" bgcolor="#b2d2e1" class="azul_osc_14">INDICADORES OPERACIONALES</td></tr>';
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
		
		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array("INDICADORES OPERACIÓN"),array(1=>"azul_izq"), $colspan-1);
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
$lineaCosprom = array("Costo/Toneladas Recolección");


$tonTOTAL = $cosTonTOTAL = $persTOTAL = $costoLlanTOTAL  = 0;


#Calcula cuantas toneladas recoje un vehiculo en el mes separando los turnos para sacar le promedio que sería igual al numero de tripulaciones
# y tambien hace el calculo de cuantos viajes por turno se realizan para saber el promedio de viajes por turno por día.

$linea = array("Km barridos");
$linea2 = array("Costo / Km Barrido");
$linea3 = array("Km barridos por operario por día");
$linea4= array("Bolsas por Operario de Barrido");
$linea5= array("Bolsas por Km de Barrido");
$lineaTotBolCon= array("Total bolsas consumidas");



$lineaprom = array("Km barridos");
$linea2prom = array("Costo / Km Barrido");
$linea3prom = array("Km barridos por operario por día");
$linea4prom = array("Bolsas por Operario de Barrido");
$linea5prom= array("Bolsas por Km de Barrido");
$lineaTotBolConprom= array("Total bolsas consumidas");



$tiposVehiculos = $tiposVehiculosXCentro = $capacidad = $recorrido = $combustible = $tonTipo = $viajes = $ton = $per = array();
$kmTot = $cosKmTOTAL = $kmOpe = $bolOpe = 0;
$idVars = array(2,3,4,5,6);



foreach($centros as $idCentro => $da)
{
	$vars = array();
	$kmTotCen = $cosKmTOTALCen = $kmOpeCen = $bolOpeCen = $bolKmBarrCen = $counmes = 0;
	foreach($fechas as $mes)
	{
		
		

		
		
		if($idCentro!=15 and $idCentro!=14){	
			$qid3 = $dbnacionalproduccion->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c LEFT JOIN servicios s ON s.id=c.id_servicio WHERE id_centro='".$idCentro."' AND esquema='bar' AND id_variable_informe IN (".implode(",",$idVars).") AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
			while($query = $dbnacionalproduccion->sql_fetchrow($qid3)){
				$vars[$query["id_variable_informe"]][$mes] = $query["val"];
			}
		}else{
			$qid3 = $dbpromo->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c LEFT JOIN servicios s ON s.id=c.id_servicio WHERE id_centro='".$idCentro."' AND esquema='bar' AND id_variable_informe IN (".implode(",",$idVars).") AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
			while($query = $dbpromo->sql_fetchrow($qid3)){
				$vars[$query["id_variable_informe"]][$mes] = $query["val"];
			}			
		}
		
		
		#preguntar($vars);
		
		/*** KM BARRIDOS ***/
		if(isset($vars[2][$mes])){
			$linea[] = number_format($vars[2][$mes],2);
			$kmTotCen+= $vars[2][$mes];
			$counmes = $counmes+1;
		}else
			$linea[] = "";

		/*** COSTO KM BARRIDOS ***/
		if(isset($vars[2][$mes]) && isset($vars[3][$mes])){
			$valkmbarrido = nvl($vars[3][$mes],0)/nvl($vars[2][$mes],0);
			$linea2[]=number_format($valkmbarrido,2);
			$cosKmTOTALCen+=$valkmbarrido;
		}
		else
			$linea2[] = "";

		/*** KM BARRIDOS POR OPERARIO | SE DIVIDE POR 26 YA QUE NO TODOS LOS DOMINGOS SE LABORAN ***/
		if(isset($vars[6][$mes])){
			$val = (nvl($vars[2][$mes],2)/nvl($vars[6][$mes],1))/26;
			$linea3[] = number_format($val,2);
			$kmOpeCen+= $val;
		}else
			$linea3[] = "";

		/***  BOLSAS POR OPERARIO DE BARRIDO ***/
		if(isset($vars[5][$mes])){
			$linea4[] = number_format($vars[5][$mes],2);
			$bolOpeCen+= $vars[5][$mes];
		}else
			$linea4[] = "";
		
		
		/***  BOLSAS POR KM DE BARRIDO ***/
		if((isset($vars[5][$mes])) && (isset($vars[6][$mes]))){ 
			$val3 = nvl($vars[2][$mes],0)/((nvl($vars[5][$mes],1))*(nvl($vars[6][$mes],1)));			
			$linea5[] = number_format($val3,2);
			$bolKmBarrCen+= $val3;
		}else
			$linea5[] = "";		
	
	}
	
	$kmTotCen = $kmTotCen/$counmes;
	$lineaprom[] = number_format($kmTotCen,2);
	$kmTot+=$kmTotCen;
	
	$cosKmTOTALCen = $cosKmTOTALCen/count($fechas);
	$linea2prom[] = number_format($cosKmTOTALCen,0);
	$cosKmTOTAL+=$cosKmTOTALCen;

	$kmOpeCen = $kmOpeCen/count($fechas);
	$linea3prom[] = number_format($kmOpeCen,2);
	$kmOpe+=$kmOpeCen;

	$bolOpeCen = $bolOpeCen/count($fechas);
	$linea4prom[] = number_format($bolOpeCen,2);
	$bolOpe+=$bolOpeCen;
	
	$bolKmBarrCen = $bolKmBarrCen/count($fechas);
	$linea5prom[] = number_format($bolKmBarrCen,2);
	$bolKmBarr+=$bolKmBarrCen;
	
	
	/*** VALOR ULTIMO MES ***/
	$lineaprom[]  = number_format($vars[2][$mes],2);
	$linea2prom[] = number_format((nvl($vars[3][$mes],0)/nvl($vars[2][$mes],0)),2);
	$linea3prom[] = number_format(((nvl($vars[2][$mes],2)/nvl($vars[6][$mes],1))/26),2);
	$linea4prom[] = number_format($vars[5][$mes],2);
	$linea5prom[] = number_format((nvl($vars[2][$mes],0)/((nvl($vars[5][$mes],1))*(nvl($vars[6][$mes],1)))),2);
	//$lineaTotBolConprom[] = number_format((nvl($vars[74][$mes],0)),2);
	
	/*** HASTA AQUI VALOR ULTIMO MES ***/
}

$linea[] = number_format($kmTot/count($centros),2);
$linea2[] = number_format($cosKmTOTAL/count($centros),0);
$linea3[] = number_format($kmOpe/count($centros),2);
$linea4[] = number_format($bolOpe/count($centros),2);
$linea5[] = number_format($bolKmBarr/count($centros),2);
//$lineaTotBolCon[] = number_format($bolTotBolCon/count($centros),2); 

$lineaprom[] = number_format($kmTot,2);
$linea2prom[] = number_format($cosKmTOTAL/count($centros),0);
$linea3prom[] = number_format($kmOpe/count($centros),2);
$linea4prom[] = number_format($bolOpe/count($centros),2);
$linea5prom[] = number_format($bolKmBarr/count($centros),2);
//$lineaTotBolConprom[] = number_format($bolTotBolCon/count($centros),2);  

###############################################################################
#CONSULTAMOS LA INFORMACIÓN ENTREGADA POR FINANCIERA EN EL FORMATO ESTABLECIDO#
###############################################################################
$lineaCosllavent = array("Costos Llantas / Ventas Totales");
$lineamttoventas = array("Costos Mtto / Ventas Totales");
//$lineaPresupuesto = array("Ejecución Presupuesto");
$lineaDisponibilidad = array("Disponibilidad Flota");
$lineaConfiabilidad = array("Confiabilidad Flota"	);
$lineaKilometrosafalla = array("Kilometros a falla");
$lineaGestiondeSolicitudes = array("Gestión de Solicitudes");


$lineaCosllaventprom = array("Costos Llantas / Ventas Totales");
$lineamttoventasprom = array("Costos Mtto / Ventas Totales");
//$lineaPresupuestoprom = array("Ejecución Presupuesto");
$lineaDisponibilidadprom = array("Disponibilidad Flota");
$lineaConfiabilidadprom = array("Confiabilidad Flota");
$lineaKilometrosafallaprom = array("Kilometros a falla");
$lineaGestiondeSolicitudesprom = array("Gestión de Solicitudes");

//$cosMtto = $gastos = $numTa = $totEmp = $totFact = $totRecau = $totRC = $totUVD = $cosMO = $totindllanta =0;
foreach($centros as $idCentro => $da)
{
	//$cosMttoCen = $gastosCen = $numTaCen = $totUsuCen = $totEmpCen = $totFactCen = $totRecauCen = $totRCCen = $totUVDCen = $cosMOCen = $totindllantacen= 0;
	$variable =  array();
	$totfec = 0;
	foreach($fechas as $mes)
	{
		
		if($idCentro!=15 and $idCentro!=14){	
			$qid4 = $dbnacionalproduccion->sql_query("SELECT sum(c.valor) as valor, id_variable_informe FROM costos c 
			 WHERE id_centro='".$idCentro."'  
			AND id_variable_informe =74 AND fecha = '".$mes."'	GROUP BY id_variable_informe");
					
			while($query = $dbnacionalproduccion->sql_fetchrow($qid4))
			{
				$variable[$query["id_variable_informe"]][$mes] = $query["valor"];
			}
		}else{
			$qid4 = $dbpromo->sql_query("SELECT sum(c.valor) as valor, id_variable_informe FROM costos c 
			WHERE id_centro='".$idCentro."'  
			AND id_variable_informe = 74 AND fecha = '".$mes."' GROUP BY id_variable_informe");
					
			while($query = $dbpromo->sql_fetchrow($qid4))
			{
				$variable[$query["id_variable_informe"]][$mes] = $query["valor"];
			}
		}
		
		/***  TOTAL BOLSAS CONSUMIDAS ***/
		 if(isset($variable[74][$mes])){ 
			$valTotBolCon = nvl($variable[74][$mes],0);			
			$lineaTotBolCon[] = number_format($valTotBolCon,2);
			$bolTotBolConCen+= $valTotBolCon;
		}else
			$lineaTotBolCon[] = ""; 
	}
	
	$bolTotBolConCen = $bolTotBolConCen/count($fechas);
	$lineaTotBolConprom[] = number_format($bolTotBolConCen,2);
	$bolTotBolCon+=$bolTotBolConCen;
	/*** VALOR ULTIMO MES ***/	
	$lineaTotBolConprom[] = number_format((nvl($vars[74][$mes],0)),2);
	
	/*** HASTA AQUI VALOR ULTIMO MES ***/
		
}

$lineaTotBolCon[]     = number_format($bolTotBolCon/count($centros),2); 
$lineaTotBolConprom[] = number_format($bolTotBolCon/count($centros),2); 


#########################################	
#MOSTRAMOS LA INFORMACIÓN DE FINANCIEROS#
if($html)
{
	if ($tipo_info==1){
	//	imprimirLinea($lineaPresupuesto,"",$estilos);
	//	imprimirLinea($lineaCosTon,"",$estilos);
		imprimirLinea($linea2,"",$estilos);
	}
	else{
	//	imprimirLinea($lineaPresupuestoprom,"",$estilos);
	//	imprimirLinea($lineaCosprom,"",$estilos);
		imprimirLinea($linea2prom,"",$estilos);
	}
}
else
{
//	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaCosTon, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea2, array(1=>"txt_izq"));
}
#Toneladas recogidas Servidio barrido
unset($lineaTon);
unset($lineaTonprom);
unset($toneladas);

consultatons($fila, $columna, $html, implode(",",array_keys($centrosnacionales)), "bar", $inicio, $final, $estilos, "asc", $toneladas);

$lineaTon = array("Toneladas");
$lineaTonprom = array("Toneladas");
$TonBascula= array("Toneladas Báscula Barrido");
$GAPTon= array("GAP Toneladas Operación");
$TonBasculaprom= array("Toneladas Báscula Barrido");
$GAPTonprom= array("GAP Toneladas Operación");
$cosTonTOTAL = $tonTOTAL = $promCen = 0;

foreach($centros as $idCentro => $da)
{
	$promCos = $numMes1 = $numMes = $totalCentro = 	$totalCentroCos = 0;	
	foreach($fechas as $mes){		
		if($idCentro!=15 and $idCentro!=14){	
			$qidfact = $dbnacionalproduccion->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c 
			 WHERE id_centro='".$idCentro."' AND id_variable_informe = 198 AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
			while($query = $dbnacionalproduccion->sql_fetchrow($qidfact)){
			$vars[$query["id_variable_informe"]][$mes] = $query["val"];
			}	
		}else{			
			$qidfact = $dbpromo->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c 
			 WHERE id_centro='".$idCentro."' AND id_variable_informe = 198 AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
			while($query = $dbpromo->sql_fetchrow($qidfact)){
			$vars[$query["id_variable_informe"]][$mes] = $query["val"];
			}	
		}
		
		$lineaTon[] = number_format(nvl($toneladas[$idCentro][$mes],0),2);
		$totalCentro+=nvl($toneladas[$idCentro][$mes],0);
		$numMes++;
		
		
		
		
		
		
		/*** TONELADAS BASCULA ***/
		if(isset($vars[198][$mes])){
			$TonBascula[] = number_format($vars[198][$mes],2);
			$kmTonBasculaCen+= $vars[198][$mes];
			$counmesTonBascula = $counmesTonBascula+1;
		}else
			$TonBascula[] = "";
		
		
		/*** GAB TONELADAS ***/
		if(isset($vars[198][$mes])){
			$valGAPTon = nvl($vars[198][$mes],2)- nvl($toneladas[$idCentro][$mes],2);
			$GAPTon[]=number_format($valGAPTon,2);
			$cosGAPTonCen+=$valGAPTon;
			$counmesTonGAPTon = $counmesTonGAPTon+1;
		}
		else
			$GAPTon[] = "";
		
	}	
	
	
	$kmTonBasculaCen = $kmTonBasculaCen/$counmesTonBascula;
	$TonBasculaprom[] = number_format($kmTonBasculaCen,2);
	$kmTotTonBascula+=$kmTonBasculaCen;
	
	$cosGAPTonCen = $cosGAPTonCen/$counmesTonGAPTon;
	$GAPTonprom[] = number_format($cosGAPTonCen,2);
	$kmTotGAPTon+=$cosGAPTonCen;
		
	
	$promCen = $totalCentro/$numMes;;
	$lineaTonprom[] = number_format($promCen,2);	
	$tonTOTAL += $promCen;
	
	/*** VALOR ULTIMO MES ***/
	$TonBasculaprom[]  = number_format($vars[198][$mes],2);
	$GAPTonprom[]= number_format((nvl($vars[198][$mes],0)- nvl($toneladas[$idCentro][$mes],0)),2);	
	$lineaTonprom[] = number_format((nvl($toneladas[$idCentro][$mes],0)),2);	
	/*** HASTA AQUI VALOR ULTIMO MES ***/
	
}
$TonBascula[] = number_format($kmTotTonBascula/count($centros),2);
$TonBasculaprom[] = number_format($kmTotTonBascula/count($centros),2);

$GAPTon[] = number_format($kmTotGAPTon/count($centros),2);
$GAPTonprom[] = number_format($kmTot/count($centros),2);

$lineaTon[] = number_format($tonTOTAL,2);
$lineaCosTon[] = number_format($cosTonTOTAL/$numCentro,0);

$lineaTonprom[] = number_format($tonTOTAL,2);
$lineaCosprom[] = number_format($cosTonTOTAL/$numCentro,2);

#########################################

#########################################
#      IMPRIMO TITULOS DE VOLUMENES     #
#########################################
if($html)
	imprimirLinea($titulosVolumenes, "#b2d2e1", $estilostitulosFinanciera);
else
{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosVolumenes, array(1=>"azul_izq"));
	$fila++;$columna=0;
}
#MOSTRAMOS LA INFORMACIÓN DE VOLUMENES
if($html)
{
	if ($tipo_info==1){
		imprimirLinea($lineaTon,"",$estilos);
	    imprimirLinea($TonBascula,"",$estilos);
		imprimirLinea($GAPTon,"",$estilos);		
		imprimirLinea($linea,"", $estilos);	
		imprimirLinea($lineaTotBolCon,"", $estilos);	

		
	}
	else{
		imprimirLinea($lineaTonprom,"",$estilos);
		imprimirLinea($TonBasculaprom,"",$estilos);
		imprimirLinea($GAPTonprom,"",$estilos);
		imprimirLinea($lineaprom,"", $estilos);	
		imprimirLinea($lineaTotBolConprom,"", $estilos);	
	}
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaTon, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $TonBascula, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $GAPTon, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaTotBolCon, array(1=>"txt_izq"));

}

#########################################

#########################################
#        IMPRIMO TITULOS DE EFICIENCIA  #
#########################################
if($html)
	imprimirLinea($titulosEficiencia, "#b2d2e1", $estilostitulosFinanciera);
else
{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosEficiencia, array(1=>"azul_izq"));
	$fila++;$columna=0;
}

#Eficiencia vehiculos
//$lineaflot = array('Eficiencia % Promedio Flota');

consuEficiencia($fila, $columna, $html,implode(",",array_keys($centrosnacionales)), "rec", $inicio, $final, $estilos, "asc", $eficiencia, $eficiencia1, $tpvhs,$tpvh);	
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
			
			
	/*** VALOR ULTIMO MES ***/
	$lineaprom[] = number_format((nvl($eficiencia[$tpv][$idCentro][$mes],0)),2)." / ".number_format((nvl($eficiencia1[$tpv][$idCentro][$mes],0)),2)."%";
	
	/*** HASTA AQUI VALOR ULTIMO MES ***/
			
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
	//	imprimirLinea($linea2,"", $estilos);
		imprimirLinea($linea3,"", $estilos);
		imprimirLinea($linea4,"", $estilos);
		imprimirLinea($linea5,"", $estilos);
	}
	else{
	//	imprimirLinea($linea2prom,"", $estilos);
		imprimirLinea($linea3prom,"", $estilos);
		imprimirLinea($linea4prom,"", $estilos);
		imprimirLinea($linea5prom,"", $estilos);
	}
}
else
{
//	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea2, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea3, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea4, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea5, array(1=>"txt_izq"));
}


#########################################

#########################################
#      IMPRIMO TITULOS DE PERSONAL      #
#########################################
/* if($html)
	imprimirLinea($titulosPersonal, "#b2d2e1", $estilostitulosFinanciera);
else
{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosPersonal, array(1=>"azul_izq"));
	$fila++;$columna=0;
} */
#MOSTRAMOS LA INFORMACIÓN DE PERSONAL
/*if($html)
{
	if ($tipo_info==1){
	//	imprimirLinea($lineaRotacion,"",$estilos);
	//	imprimirLinea($lineaAusentismo,"",$estilos);
	}
	else{
	//	imprimirLinea($lineaRotacionprom,"",$estilos);
	//	imprimirLinea($lineaAusentismoprom,"",$estilos);
	}
}
else
{
//	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaRotacion, array(1=>"txt_izq"));
//	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaAusentismo, array(1=>"txt_izq"));
}*/

########################################
##seccion donde estan las /*funciones*/
########################################
function consultatons(&$fila, &$columna, $html, $idCentros, $servicio, $inicio, $final, $estilos, $orden, &$toneladas)
{
	if ($servicio  == 'rec')$idservicio="1,10";	
	if ($servicio  == 'bar')$idservicio="2.4,5";		
	if ($servicio  == 'clus')	$idservicio="3,9,17,18,20";	
	if ($servicio  == 'todo')	$idservicio="1,2,3,4,5,9,10,17,18,20";

	global $db, $dbpromo,$dbnacionalproduccion, $CFG, $ME;
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
				WHERE inicio::date >= '$inicio' AND inicio::date<='$final' and c.id in ($idCentros) and s.id in($idservicio)
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
				
	if($idCentros!=''){	
		$qidpesos = $dbnacionalproduccion->sql_query($strQuery);	
		while($pesos= $dbnacionalproduccion->sql_fetchrow($qidpesos)){
			$toneladas[$pesos["id"]][$pesos["fecha"]]+=$pesos["toneladas"];
			$i++;
		}
	}
	if($idCentros!=''){	
		$qidpesos2 = $dbpromo->sql_query($strQuery);
		while($pesos= $dbpromo->sql_fetchrow($qidpesos2)){
			$toneladas[$pesos["id"]][$pesos["fecha"]]+=$pesos["toneladas"];
			$i++;
		}
	}
}


function consuEficiencia(&$fila, &$columna, $html, $idCentros, $servicio, $inicio, $final, $estilos, $orden, &$eficiencia, &$eficiencia1, &$tpvhs, &$tpvh)
{
	global $db, $CFG, $dbpromo, $dbnacionalproduccion, $ME;
	global $workbook;
	global $worksheet;		
	$idservicios = "2,4,5";
	$arraytipovehiculosnal 		= array();
	$arraytipovehiculosdistrito = array();
	
	$sqlobtenidvehiculo= "SELECT id_tipo_vehiculo FROM tipos_vehiculos_servicios where id_servicio in($idservicios) group by id_tipo_vehiculo order by id_tipo_vehiculo";
	
	$qiobtenidvehiculonal = $dbnacionalproduccion->sql_query($sqlobtenidvehiculo);		
	while($rownal= $dbnacionalproduccion->sql_fetchrow($qiobtenidvehiculonal)){
		array_push($arraytipovehiculosnal, $rownal[0]);
	}
	$tpvh2= implode(',',$arraytipovehiculosnal);
	
	$qiobtenidvehiculdistrito = $dbpromo->sql_query($sqlobtenidvehiculo);		
	while($rowpromo= $dbpromo->sql_fetchrow($qiobtenidvehiculdistrito)){
		array_push($arraytipovehiculosdistrito, $rowpromo[0]);
	}
	$tpvh3= implode(',',$arraytipovehiculosdistrito);




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
					avg(pesos.capacidad)as capacidad, avg(eficiencia) as eficiencia, avg(pesos.pes_viaje) as pesviaje, sum(pesos.viajedes) as viajes
					FROM  rec.movimientos mov LEFT JOIN
						(select p.id,p.peso_inicial,p.peso_final,p.peso_total, v.id_tipo_vehiculo,tv.tipo,tv.orden_info,tv.capacidad,min(id_movimiento) as id_movimiento,
						(p.peso_total/tv.capacidad)*100 as eficiencia, avg(p.peso_total) as pes_viaje, 1 as viajedes
						from rec.pesos p
						LEFT JOIN vehiculos v ON p.id_vehiculo=v.id
						LEFT JOIN rec.movimientos_pesos mp ON p.id=mp.id_peso
						LEFT JOIN tipos_vehiculos tv ON v.id_tipo_vehiculo=tv.id
						WHERE tv.capacidad>0
						GROUP BY  p.id,p.peso_inicial,p.peso_final,p.peso_total,v.id_tipo_vehiculo,v.id_centro,tv.tipo,tv.orden_info,tv.capacidad,tv.capacidad) as pesos
						ON mov.id=pesos.id_movimiento
					LEFT JOIN vehiculos vh ON mov.id_vehiculo=vh.id
					LEFT JOIN micros i ON i.id=mov.id_micro
					LEFT JOIN servicios s ON s.id = i.id_servicio
					LEFT JOIN centros c ON vh.id_centro=c.id
					WHERE  inicio::date >= '$inicio' AND inicio::date<='$final' and c.id in ($idCentros) and i.id_servicio in($idservicios)						 
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
					pesos.id_tipo_vehiculo, i.id_servicio, c.id,c.centro,pesos.id_tipo_vehiculo,pesos.tipo,pesos.orden_info
				order by pesos.orden_info $orden,2,1 ";
		
		
		if($idCentros!=''){
			$qidefic = $dbnacionalproduccion->sql_query($sqlefic);
			while($efic = $dbnacionalproduccion->sql_fetchrow($qidefic)){		
				$tpvh=$tpvh2;
				$tpvh = explode(",", $tpvh); 
		
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
		if($idCentros!=''){
			$qidefic2 = $dbpromo->sql_query($sqlefic);
			while($efic = $dbpromo->sql_fetchrow($qidefic2))
			{		
				$tpvh=$tpvh3;
				$tpvh = explode(",", $tpvh); 
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