<?
// error_reporting(E_ALL);
// ini_set("display_errors", 1);
# if($_SERVER["REMOTE_ADDR"] != "186.30.42.202")
# die("un momento por favor estamos ajustando los indicadores de acuerdo a reunione de gerencia del 23 de mayo");

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
$titulo1["inf"]="INDICADORES GERENCIALES : INDICADORES RECOLECCION";
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
			$titulosFinanciera[] = $titulosVolumenes[] = $titulosEficiencia[] = ucfirst(strftime("%b.%Y",strtotime($mes."-01")));
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
			$titulosFinanciera[] = $titulosVolumenes[] = $titulosEficiencia[] = ucfirst(strftime("%b.%Y",strtotime($mes."-01")));
			$estilostitulosFinanciera[$i] = " class='azul_osc_14'";
			$estilos[$i] = "class='azul_osc_12'";
			$i++;
		}
	}
	else {	
		$titulosFinanciera[] = $titulosVolumenes[] = $titulosEficiencia[] = $titulosIndFinan[] = $queryCen["centro"]." Consolidado";
		$titulosFinanciera[] = $titulosVolumenes[] = $titulosEficiencia[] = $titulosIndFinan[] = "Ultimo Mes";
		$estilostitulosFinanciera[] = " class='azul_osc_14'";
		$estilos[] = "class='azul_osc_12'";
		$estilostitulosFinanciera[] = " class='azul_osc_14'";
		$estilos[] = "class='azul_osc_12'"; 
	}
}
$centrosnacionales= $centrosnacional + $centrospromo;
$titulosFinanciera[]=$titulosVolumenes[]=$titulosEficiencia[] = $titulosIndFinan[] = "PROMEDIOS CONSOLIDADOS";
$estilostitulosFinanciera[] = " class='azul_osc_14' width='150'";
$estilos[] = "class='azul_osc_12'";

if ($tipo_info==1){
	$colspan = (count($fechas) * count($centros))+2;
	if($html)
	{
		$titulos[] = "Consolidados Promedios";
		$anchotabla = (120*(count($titulosFinanciera)-2))+430;
		if ($anchotabla<950) $anchotabla=950;
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
	}else{
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
$lineaPers = array("Toneladas recogidas por  tripulacion");
$lineaLl = array("Costo de llantas por km recorrido ($/Km)");
$TonBascula= array("Toneladas Báscula Recolección");
$GAPTon= array("GAP Toneladas Operación");
$TonBasculaprom= array("Toneladas Báscula Recolección");
$GAPTonprom= array("GAP Toneladas Opperación");
$tonTOTAL = $cosTonTOTAL = $persTOTAL = $costoLlanTOTAL  = 0;

##############################
#     Toneladas recogidas    #
##############################

consultatons($fila, $columna, $html, implode(",",array_keys($centrosnacionales)),"rec", $inicio, $final, $estilos, "asc", $toneladas);

$lineaTon = array("Toneladas");
$cosTonTOTAL = 0;

##############################
# Costo Toneladas recogidas  #
##############################
foreach($centros as $idCentro => $da)
{
	$vars = array();
	$promCos = $numMes1 = $numMes = $totalCentro = 	$totalCentroCos = 0;
	foreach($fechas as $mes)
	{		
		$lineaTon[] = number_format(nvl($toneladas[$idCentro][$mes],0),2);	
		
		$totalCentro+=nvl($toneladas[$idCentro][$mes],0);
		#Consultamos el costo del mes	
		
		if($idCentro!=15 and $idCentro!=14){			
			$qidCTRP = $dbnacionalproduccion->sql_row("SELECT sum(c.valor) as val FROM costos c 
			WHERE id_centro='".$idCentro."'  AND id_variable_informe = 1  AND fecha = '".$mes."'");			
		}else{		
			$qidCTRP = $dbpromo->sql_row("SELECT sum(c.valor) as val FROM costos c 
			WHERE id_centro='".$idCentro."'  AND id_variable_informe = 1  AND fecha = '".$mes."'");				
		}
		
		if($idCentro!=15 and $idCentro!=14){	
			$qidfact = $dbnacionalproduccion->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c 
			 WHERE id_centro='".$idCentro."' AND id_variable_informe = 197 AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
			while($query = $dbnacionalproduccion->sql_fetchrow($qidfact)){
			$vars[$query["id_variable_informe"]][$mes] = $query["val"];
			}	
		}else{			
			$qidfact = $dbpromo->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c 
			 WHERE id_centro='".$idCentro."' AND id_variable_informe = 197 AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
			while($query = $dbpromo->sql_fetchrow($qidfact)){
			$vars[$query["id_variable_informe"]][$mes] = $query["val"];
			}	
		}
	
		@$costo=nvl($qidCTRP["val"],2)/nvl($toneladas[$idCentro][$mes],2);
		$lineaCosTon[] = number_format($costo,2);
		
		if ($costo!=0) {
			$totalCentroCos+=nvl($costo,2);
			$numMes1++;
		}
		if(isset($toneladas[$idCentro][$mes]))
			$numMes++;		
		
		/*** TONELADAS BASCULA ***/
		if(isset($vars[197][$mes])){
			$TonBascula[] = number_format($vars[197][$mes],2);
			$kmTonBasculaCen+= $vars[197][$mes];
			$counmesTonBascula = $counmesTonBascula+1;
		}else
			$TonBascula[] = "";
		
		
		/*** GAB TONELADAS ***/
		if(isset($vars[197][$mes])){
			$valGAPTon = nvl($vars[197][$mes],2)- nvl($toneladas[$idCentro][$mes],2);
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
	
	
	$promCen = $totalCentro/$numMes;
	$lineaTonprom[] = number_format($promCen,2);
	$promCos = $totalCentroCos/$numMes1;
	$lineaCosprom[] = number_format($promCos,2);
	
	$tonTOTAL += $promCen;
	if($promCos!=0) {
		$cosTonTOTAL += $promCos;
		$numCentro++;
	}
	
	
	/*** VALOR ULTIMO MES ***/
	$TonBasculaprom[]  = number_format($vars[197][$mes],2);
	$GAPTonprom[]= number_format((nvl($vars[197][$mes],0)- nvl($toneladas[$idCentro][$mes],0)),2);	
	$lineaTonprom[] = number_format((nvl($toneladas[$idCentro][$mes],0)),2);	
	$lineaCosprom[] = number_format((nvl($qidCTRP["val"],0)/nvl($toneladas[$idCentro][$mes],2)),0);
	
	/*** HASTA AQUI VALOR ULTIMO MES ***/
}


$TonBascula[] = number_format($kmTotTonBascula/count($centros),2);
$TonBasculaprom[] = number_format($kmTotTonBascula/count($centros),2);

$GAPTon[] = number_format($kmTotGAPTon/count($centros),2);
$GAPTonprom[] = number_format($kmTot/count($centros),2);

$lineaTon[] = number_format($tonTOTAL,2);
$lineaCosTon[] = number_format($cosTonTOTAL/$numCentro,0);
$lineaLl[] = number_format($costoLlanTOTAL/count($centros),2);
$lineaTonprom[] = number_format($tonTOTAL,2);
$lineaCosprom[] = number_format($cosTonTOTAL/$numCentro,2);

#Calcula cuantas toneladas recoje un vehiculo en el mes separando los turnos para sacar le promedio que sería igual al numero de tripulaciones
# y tambien hace el calculo de cuantos viajes por turno se realizan para saber el promedio de viajes por turno por día.

consuviajtontrip($fila, $columna, $html, implode(",",array_keys($centrosnacionales)), "rec", $inicio, $final, $estilos, $orden, $tontrip, $viajtur);
$lineaTontrip = array("Toneladas Recogidas por Tripulación");
$lineaviajestur = array("Promedio Viajes realizados por Turno");
$factorcargasobrepeso = array("Factor de Carga y Sobrepeso");

$lineaTontripprom = array("Toneladas Recogidas por Tripulación");
$lineaviajesturprom = array("Promedio Viajes realizados por Turno");
$factorcargasobrepesoprom = array("Factor de Carga y Sobrepeso");

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
	
	
	/*** VALOR ULTIMO MES ***/
	$lineaTontripprom[] = number_format($totalCentro/$numMes,2);
	$lineaviajesturprom[] = number_format((nvl($viajtur[$idCentro][$mes],0)),2);
	
	/*** HASTA AQUI VALOR ULTIMO MES ***/
	
}
$lineaTontrip[] = number_format($PromTOTAL/count($centros),2);
$lineaviajestur[] = number_format($PromTOTAL1/count($centros),2);
$lineaTontripprom[] = number_format($PromTOTAL/count($centros),2);
$lineaviajesturprom[] = number_format($PromTOTAL1/count($centros),2);


###############################################################################
#CONSULTAMOS LA INFORMACIÓN ENTREGADA POR FINANCIERA EN EL FORMATO ESTABLECIDO#
###############################################################################
$lineaCosllavent = array("Costos Llantas / Ventas Totales");
$lineamttoventas = array("Costos Mtto / Ventas Totales");
//$lineaPresupuesto = array("Ejecución Presupuesto");
//$lineaPresupuestorecoleccion = array("Presupuesto Recolección");

$lineaDisponibilidad = array("Disponibilidad Flota");
$lineaConfiabilidad = array("Confiabilidad Flota"	);
$lineaKilometrosafalla = array("Kilometros a falla");
$lineaGestiondeSolicitudes = array("Gestión de Solicitudes");
//$linea7201 = array("Calidad de cumplimiento frecuencia de recolección");
$linea7202 = array("Calidad de cumplimiento horario de recolección"	);


$lineacontenUsuario = array("Usuarios / Cantidad de contenedores");
$lineacontenVentas = array("Cantidad de contenedores / Ventas");


//$linea7203 = array("Calidad Técnica en la Recolección");
//$lineaGestionPQRS = array("Gestión de PQRS");
//$lineaRotacion = array("Rotación de Personal");
//$lineaAusentismo = array("Ausentismo de Personal");
//$lineaHoraCapaOperario = array("Horas Capacitación Operario");

$lineaCosllaventprom = array("Costos Llantas / Ventas Totales");
$lineamttoventasprom = array("Costos Mtto / Ventas Totales");
//$lineaPresupuestoprom = array("Ejecución Presupuesto");
//$lineaPresupuestorecoleccionprom = array("Presupuesto Recolección");

$lineaDisponibilidadprom = array("Disponibilidad Flota");
$lineaConfiabilidadprom = array("Confiabilidad Flota"	);
$lineaKilometrosafallaprom = array("Kilometros a falla");
$lineaGestiondeSolicitudesprom = array("Gestión de Solicitudes");
//$linea7201prom = array("Calidad de cumplimiento frecuencia de recolección");
$linea7202prom = array("Calidad de cumplimiento horario de recolección"	);

$lineacontenUsuarioprom = array("Usuarios / Cantidad de contenedores");
$lineacontenVentasprom = array("Cantidad de contenedores / Ventas");

//$linea7203prom = array("Calidad Técnica en la Recolección");
//$lineaGestionPQRSprom = array("Gestión de PQRS");
//$lineaRotacionprom = array("Rotación de Personal");
//$lineaAusentismoprom = array("Ausentismo de Personal");
//$lineaHoraCapaOperarioprom = array("Horas Capacitación Operario");

#ind financieros
$qidOV = $dbnacionalproduccion->sql_query("SELECT * FROM variables_informes WHERE id >=27 AND id<=36 ORDER BY id");
while($queryOV = $dbnacionalproduccion->sql_fetchrow($qidOV))
{
	$nombreOtrasDos[$queryOV["id"]] = $queryOV["variable"];
}
$kmTot203Cen =  $kmTot204Cen /*= $kmTot7203Cen*/ = 0;
foreach($centros as $idCentro => $da)
{
	 $counmeslinea203 = $linea203Cen = $counmeslinea204 = $linea204Cen = 0;
	$vars =  array();
	$totfec = 0;
	foreach($fechas as $mes)
	{
		if($idCentro!=15 and $idCentro!=14){	
			$qidcom = $dbnacionalproduccion->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c 
			 WHERE id_centro='".$idCentro."'  
			AND id_variable_informe >= 203 AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
			while($query = $dbnacionalproduccion->sql_fetchrow($qidcom)){
				$vars[$query["id_variable_informe"]][$mes] = $query["val"];
			}	
		}else{			
			$qidcom = $dbpromo->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c 
			 WHERE id_centro='".$idCentro."'  
			AND id_variable_informe >= 203 AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
			while($query = $dbpromo->sql_fetchrow($qidcom)){
				$vars[$query["id_variable_informe"]][$mes] = $query["val"];
			}	
		}
			if((isset($vars[203][$mes])) && (isset($vars[204][$mes])))
		{
			$factorcargasobrepeso[] = number_format($vars[203][$mes],2)."% / ".number_format($vars[204][$mes],2)."%";
			$linea203Cen+= $vars[203][$mes];
			$counmeslinea203 = $counmeslinea203+1;
			$linea204Cen+= $vars[204][$mes];
			$counmeslinea204 = $counmeslinea204+1;
			
		}else
			$factorcargasobrepeso[] = "";		
	}
	//$factorcargasobrepeso[] = "";
		$linea203Cen=$linea203Cen/$counmeslinea203;
		$linea204Cen=$linea204Cen/$counmeslinea204;
		 $factorcargasobrepesoprom[] = number_format($linea203Cen,2)."% / ". number_format($linea204Cen,2)."%";			
		 $kmTot203Cen+=$linea203Cen;
		 $kmTot204Cen+=$linea204Cen;
	
	/*** VALOR ULTIMO MES ***/
	
	 $factorcargasobrepesoprom[] = number_format($vars[203][$mes],2)."% / ". number_format($vars[204][$mes],2)."%";			
	/* $factorcargasobrepesoprom[] = number_format($linea203Cen,2)."% / ". number_format($linea204Cen,2)."%";	*/		
	/*** HASTA AQUI VALOR ULTIMO MES ***/
	
	
}
$factorcargasobrepeso[]		= number_format($kmTot203Cen/count($centros),2)."% /".number_format($kmTot204Cen/count($centros),2)."%"; 
$factorcargasobrepesoprom[] = number_format($kmTot203Cen/count($centros),2)."% /".number_format($kmTot204Cen/count($centros),2)."%";  


$cosMtto = $gastos = $numTa = $totEmp = $totFact = $totRecau = $totRC = $totUVD = $cosMO = $totindllanta=/*$kmTot7201Cen =*/ $kmTot7202Cen /*= $kmTot7203Cen*/ = 0;
foreach($centros as $idCentro => $da)
{
	$cosMttoCen = $numMes1 = $numMes = $gastosCen = /*$counmeslinea7201 = $linea7201Cen =*/ $counmeslinea7202 = $linea7202Cen /*= $counmeslinea7203 = $linea7203Cen*/ = $numTaCen = $totUsuCen = $totEmpCen = $totFactCen = $totRecauCen = $totRCCen = $totUVDCen = $cosMOCen = $totindllantacen= 0;
	$vars =  array();
	$totfec = 0;
	foreach($fechas as $mes)
	{
		if ($idCentro!=15){
			$qid3 = $dbnacionalproduccion->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c 
			LEFT JOIN servicios s ON s.id=c.id_servicio WHERE id_centro='".$idCentro."'  
			AND id_variable_informe >= 9 AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
			while($query = $dbnacionalproduccion->sql_fetchrow($qid3)){
				$vars[$query["id_variable_informe"]][$mes] = $query["val"];
			}	
		}else{			
			$qid3 = $dbpromo->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c 
			LEFT JOIN servicios s ON s.id=c.id_servicio WHERE id_centro='".$idCentro."'  
			AND id_variable_informe >= 9 AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
			while($query = $dbpromo->sql_fetchrow($qid3)){
				$vars[$query["id_variable_informe"]][$mes] = $query["val"];
			}	
		}

			if(isset($vars[79][$mes]))
		{
			$linea7202[] = number_format($vars[79][$mes],2)."%";
			$linea7202Cen+= $vars[79][$mes];
			$counmeslinea7202 = $counmeslinea7202+1;
			
		}else
			$linea7202[] = "";
		
		
		
		
			
	
		
		/*** CANTIDAD CONTENEDORES USUARIO ***/
		if(isset($vars[78][$mes]))
			{
				//$val=nvl($vars[78][$mes],0)/( nvl($vars[17][$mes],0))*100;
				$val=nvl($vars[17][$mes],0)/( nvl($vars[78][$mes],0));
				$lineacontenUsuario[] = number_format($val,2);
				$totcontenUsuariocen+= $val;
			}else
				$lineacontenUsuario[] = "";
			
			
		
		/*** CANTIDAD CONTENEDORES VENTAS ***/
		if(isset($vars[78][$mes]))
			{
				
				$val=(nvl($vars[78][$mes],0))/( nvl($vars[13][$mes],0)+ nvl($vars[14][$mes],0) )*100;				
				$lineacontenVentas[] = number_format($val,2)."%";
				$totcontenVentascen+= $val;
			}else
				$lineacontenVentas[] = "";
		
		
				
		if(isset($vars[500][$mes]))
		{
			$val = $vars[500][$mes];		
			$lineaDisponibilidad[] = number_format($val,0);
			$lineaConfiabilidad[] = number_format($val,0);
			$lineaKilometrosafalla[] = number_format($val,0);
			$lineaGestiondeSolicitudes[] = number_format($val,0);			
			$totEmpCen+= $val;
		}else{
	
			$lineaDisponibilidad[] = "";
			$lineaConfiabilidad[] = "";
			$lineaKilometrosafalla[] = "";
		
		}
	}
	
	
	
	
	
		
	 $linea7202Cen=$linea7202Cen/$counmeslinea7202;
	 $linea7202prom[] = number_format($linea7202Cen,2)."%";			
	 $kmTot7202Cen+=$linea7202Cen;
	
	
	$totcontenUsuariocen = $totcontenUsuariocen/count($fechas);
	$lineacontenUsuarioprom[] = number_format($totcontenUsuariocen,2);
	$totcontenUsuario+=$totcontenUsuariocen;
	
	
	$totcontenVentascen = $totcontenVentascen/count($fechas);
	$lineacontenVentasprom[] = number_format($totcontenVentascen,2).'%';
	$totcontenVentas+=$totcontenVentascen;
	
	
	
		
	/*** VALOR ULTIMO MES ***/
	 $linea7202prom[] = number_format($vars[79][$mes],2)."%";	
	 $lineacontenUsuarioprom[] = number_format((nvl($vars[17][$mes],0)/( nvl($vars[78][$mes],0))),2);
	  $lineacontenVentasprom[] = number_format((nvl($vars[78][$mes],0)/( nvl($vars[13][$mes],0)+ nvl($vars[14][$mes],0) )*100),2)."%";
	

	/*** HASTA AQUI VALOR ULTIMO MES ***/
	
}
//$linea7201prom[] = number_format($kmTot7201Cen,2); 

#########################################	
#MOSTRAMOS LA INFORMACIÓN DE FINANCIEROS#
if($html)
{
	if ($tipo_info==1){
		imprimirLinea($lineaCosTon,"",$estilos);
	}
	else{
		imprimirLinea($lineaCosprom,"",$estilos);
	
	}
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaCosTon, array(1=>"txt_izq"));
	
}

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
$linea7201[] = number_format($kmTot7201Cen/count($centros),2)."%";
$linea7202[] = number_format($kmTot7202Cen/count($centros),2)."%";  
$linea7203[] = number_format($kmTot7203Cen/count($centros),2)."%"; 
$linea7201prom[] = number_format($kmTot7201Cen/count($centros),2)."%"; 
$linea7202prom[] = number_format($kmTot7202Cen/count($centros),2)."%";  
$linea7203prom[] = number_format($kmTot7203Cen/count($centros),2)."%";    
                     
$lineacontenUsuario[] = number_format($totcontenUsuario/count($centros),2)."%"; 
$lineacontenVentas[] = number_format($totcontenVentas/count($centros),2)."%";

$lineacontenUsuarioprom[] = number_format($totcontenUsuario/count($centros),2)."%"; 
$lineacontenVentasprom[] = number_format($totcontenVentas/count($centros),2)."%";







if($html)
{
	if ($tipo_info==1){
		imprimirLinea($lineaTon,"",$estilos);
		imprimirLinea($TonBascula,"",$estilos);
		imprimirLinea($GAPTon,"",$estilos);
	//	imprimirLinea($linea7201,"",$estilos);
		imprimirLinea($linea7202,"",$estilos);
		imprimirLinea($lineacontenUsuario,"",$estilos);
	//	imprimirLinea($lineacontenVentas,"",$estilos);
	//	imprimirLinea($linea7203,"",$estilos);
	//	imprimirLinea($lineaGestionPQRS,"",$estilos);
	}
	else{
		imprimirLinea($lineaTonprom,"",$estilos);
		imprimirLinea($TonBasculaprom,"",$estilos);
		imprimirLinea($GAPTonprom,"",$estilos);
	//	imprimirLinea($linea7201prom,"",$estilos);
		imprimirLinea($linea7202prom,"",$estilos);
		imprimirLinea($lineacontenUsuarioprom,"",$estilos);
	//	imprimirLinea($lineacontenVentasprom,"",$estilos);

	//	imprimirLinea($linea7203prom,"",$estilos);
	//	imprimirLinea($lineaGestionPQRSprom,"",$estilos);
	}
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaTon, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $TonBascula, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $GAPTon, array(1=>"txt_izq"));
//	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea7201, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea7202, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineacontenUsuario, array(1=>"txt_izq"));
//	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineacontenVentas, array(1=>"txt_izq"));
//	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea7203, array(1=>"txt_izq"));
//	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaGestionPQRS, array(1=>"txt_izq"));
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
#MOSTRAMOS LA INFORMACIÓN DE EFICIENCIA
/* if($html)
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
} */
#Eficiencia vehiculos
$lineaflot = array('Eficiencia % Promedio Flota');


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
			
			$lineaprom[] = number_format(nvl($eficiencia[$tpv][$idCentro][$mes],0),2)." / ".number_format(nvl($eficiencia1[$tpv][$idCentro][$mes],0),2)."%";
			
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
		imprimirLinea($lineaTontrip,"",$estilos);
		imprimirLinea($lineaviajestur,"",$estilos);
	//	imprimirLinea($costokmrecolecservespeciales,"",$estilos);
	//	imprimirLinea($costotonrecolecservespeciales,"",$estilos);
		imprimirLinea($factorcargasobrepeso,"",$estilos);
	//	imprimirLinea($indiceoperariokmbarridoiob,"",$estilos);

	}
	else {
		imprimirLinea($lineaTontripprom,"",$estilos);
		imprimirLinea($lineaviajesturprom,"",$estilos);
	//	imprimirLinea($costokmrecolecservespecialesprom,"",$estilos);
	//	imprimirLinea($costotonrecolecservespecialesprom,"",$estilos);
	//	imprimirLinea($factorcargasobrepesoprom,"",$estilos);
	//	imprimirLinea($indiceoperariokmbarridoiobprom,"",$estilos);
	
	}
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaTontrip, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaviajestur, array(1=>"txt_izq"));
//	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $costokmrecolecservespeciales, array(1=>"txt_izq"));
//	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $costotonrecolecservespeciales, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $factorcargasobrepeso, array(1=>"txt_izq"));
//	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $indiceoperariokmbarridoiob, array(1=>"txt_izq"));

}
#########################################

#########################################
#      IMPRIMO TITULOS DE PERSONAL      #
#########################################
/*if($html)
	imprimirLinea($titulosPersonal, "#b2d2e1", $estilostitulosFinanciera);
else
{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosPersonal, array(1=>"azul_izq"));
	$fila++;$columna=0;
}*/
#MOSTRAMOS LA INFORMACIÓN DE PERSONAL
/* if($html)
{
	if ($tipo_info==1){
	//	imprimirLinea($lineaRotacion,"",$estilos);
	//	imprimirLinea($lineaAusentismo,"",$estilos);
	//	imprimirLinea($lineaHoraCapaOperario,"",$estilos);
		
	}
	else{
	//	imprimirLinea($lineaRotacionprom,"",$estilos);
	//	imprimirLinea($lineaAusentismoprom,"",$estilos);
	//	imprimirLinea($lineaHoraCapaOperarioprom,"",$estilos);
	}
}
else
{
//	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaRotacion, array(1=>"txt_izq"));
//	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaAusentismo, array(1=>"txt_izq"));
//	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaHoraCapaOperario, array(1=>"txt_izq"));
} */

########################################
##seccion doonde estan las /*funciones*/
########################################
function consultatons(&$fila, &$columna, $html, $idCentros, $servicio, $inicio, $final, $estilos, $orden, &$toneladas)
{
	/* echo $idCentro."<br>";
	echo $idCentroPromo."<br>";
	 */
	//if ($servicio  == 'rec')$idservicio="1,10";	
	if ($servicio  == 'rec')$idservicio="1,10";	
	if ($servicio  == 'bar')$idservicio="2,4,5";		
	if ($servicio  == 'clus')	$idservicio="3,9,17,18,20";	
	if ($servicio  == 'todo')	$idservicio="1,2,3,4,5,9,10,17,18,20";

	global $db, $dbpromo, $dbnacionalproduccion, $CFG, $ME;
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
				while($pesos= $dbnacionalproduccion->sql_fetchrow($qidpesos))
				{
					$toneladas[$pesos["id"]][$pesos["fecha"]]+=$pesos["toneladas"];
					$i++;
				}
			}
				
			if($idCentros!=''){				
				$qidpesos2 = $dbpromo->sql_query($strQuery);
				while($pesos= $dbpromo->sql_fetchrow($qidpesos2))
				{
					$toneladas[$pesos["id"]][$pesos["fecha"]]+=$pesos["toneladas"];
					$i++;
				}
			}		
}

function consuEficiencia(&$fila, &$columna, $html, $idCentros, $servicio, $inicio, $final, $estilos, $orden, &$eficiencia, &$eficiencia1, &$tpvhs, &$tpvh)
{
	global $db, $dbpromo, $dbnacionalproduccion, $CFG, $ME;
	global $workbook;
	global $worksheet;	
	if ($servicio=="rec") $tpvh = "1,2,4,8,11,14,15,19,21,29";
	$tpvh2=$tpvh;
	$tpvh = explode(",", $tpvh);
	
$idservicio="1,10";	
	
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
					avg(pesos.capacidad)as capacidad, avg(eficiencia) as eficiencia,avg(pesos.pes_viaje) as pesviaje,sum(pesos.viajedes) as viajes 
					FROM  rec.movimientos mov LEFT JOIN
						(select p.id,p.peso_inicial,p.peso_final,p.peso_total, v.id_tipo_vehiculo,tv.tipo,tv.orden_info,tv.capacidad,min(id_movimiento) as id_movimiento,
						(p.peso_total/tv.capacidad)*100 as eficiencia, avg(p.peso_total) as pes_viaje, 1 as viajedes
						from rec.pesos p
						LEFT JOIN vehiculos v ON p.id_vehiculo=v.id
						LEFT JOIN rec.movimientos_pesos mp ON p.id=mp.id_peso
						LEFT JOIN tipos_vehiculos tv ON v.id_tipo_vehiculo=tv.id
						WHERE tv.capacidad>0
						GROUP BY p.id,p.peso_inicial,p.peso_final,p.peso_total,v.id_tipo_vehiculo,v.id_centro,tv.tipo,tv.orden_info,tv.capacidad,tv.capacidad) as pesos
						ON mov.id=pesos.id_movimiento
					LEFT JOIN vehiculos vh ON mov.id_vehiculo=vh.id
					LEFT JOIN micros i ON i.id=mov.id_micro
					LEFT JOIN servicios s ON s.id = i.id_servicio
					LEFT JOIN centros c ON vh.id_centro=c.id					
					WHERE inicio::date >= '$inicio' AND inicio::date<='$final' and c.id in ($idCentros) and i.id_servicio in($idservicio)									
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
		
 	
		if($idCentros!=''){	
			$qidefic = $dbnacionalproduccion->sql_query($sqlefic);
			while($efic = $dbnacionalproduccion->sql_fetchrow($qidefic)){		
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
			while($efic = $dbpromo->sql_fetchrow($qidefic2)){		
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

function consuviajtontrip(&$fila, &$columna, $html, $idCentros, $servicio, $inicio, $final, $estilos, $orden, &$tontrip, &$viajtur)
{
	global $db, $dbpromo,$dbnacionalproduccion, $CFG, $ME;	
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
			WHERE tvh.id in (1,2,4,8,11,14,15,19,21,29) and inicio::date >= '$inicio' AND inicio::date<='$final' and c.id in ($idCentros)
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
		
		if($idCentros!=''){					
			$qidefic = $dbnacionalproduccion->sql_query($sqlefic);			
			while($efic = $dbnacionalproduccion->sql_fetchrow($qidefic)){		
				$tontrip[$efic["id"]][$efic["fecha"]]+=number_format($efic["tontripula"],2);
				$viajtur[$efic["id"]][$efic["fecha"]]+=number_format($efic["promviajes"],2);
				$i++;
			} 
		}
		
		if($idCentros!=''){	
			$qidefic2 = $dbpromo->sql_query($sqlefic);		
			while($efic = $dbpromo->sql_fetchrow($qidefic2)){		
				$tontrip[$efic["id"]][$efic["fecha"]]+=number_format($efic["tontripula"],2);
				$viajtur[$efic["id"]][$efic["fecha"]]+=number_format($efic["promviajes"],2);
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