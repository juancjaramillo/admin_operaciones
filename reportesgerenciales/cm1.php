<?
/*-----------------------------------------------------------------------------------------
'Descripción       :  Visualiza los indicadores para el reporte para la gerencia comercial    
'Autor      		: Juan Carlos Jaramillo . Aseo Regional - Promoambiental Distrito.
'Fecha de Creación  : marzo 21/2019
'-------------------------------------------------------------------------------------------
'	Propósito :	Armar el reporte de los indicadores para la Gerencia Comercial. Visualizar 
				la información de manera consolidada y Detallada.
'				
'	............................................................................
'	Entradas :  Rango Fecha de consulta y tipo vista (Detallado o Consolidado)
'	............................................................................
'	Proceso  :	Armar el diseño del reporte y luego consultar en la tabla costos las variables que se 
				se necesitan por cada fecha por cada empresa 				
'............................................................................
'Consideraciones :

*/

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
if($tipo_info=="") {
	$tipo_info=1;
}
//$tipo_info=1;
if(isset($_GET["format"])) 
{
	$html=false;
	$inicio = $_GET["inicio"];
	$final = $_GET["final"];
}

/*'Armar el esquema de las tablas y titulos de columnas
'=======================================================*/	
$titulo1["inf"]="INDICADORES GERENCIALES : INDICADORES COMERCIALES";
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
$titulos = array();
//$titulosComerciales = array("INDICADORES");

$estilostitulosFinanciera = array(1=>" height='30'  class='azul_osc_14'");
$estilos = array(1=>"align='left'  class='azul_osc_12'");
$i=2;



/****BUSCA EL NUMERO DOCUMENTO EN LA BASE DE DATOS LOCAL ***/
$qiddocumento = $db->sql_query("SELECT id, cedula FROM personas WHERE id='".$user["id"]."'");
$querydocumento = $db->sql_fetchrow($qiddocumento);
$documento= $querydocumento["cedula"];

/*** CON EL NUMERO DE DOCUMENTO BUSCA EL ID EN LA BASE DE DATOS DE CONSULTA DE REPORTES  ***/
$qidCentro = $dbnacionalproduccion->sql_query("SELECT id_centro, centro FROM personas_centros LEFT JOIN centros ON centros.id=personas_centros.id_centro 
LEFT JOIN personas ON personas.id=personas_centros.id_persona WHERE cedula='".$documento."' and id_centro in (SELECT DISTINCT(id_centro) as id_centro from costos)
ORDER BY id_centro");

//Consulta a base de datos de promoambiental
$qidCentro2 = $dbpromo->sql_query("SELECT id_centro, centro FROM personas_centros LEFT JOIN centros ON centros.id=personas_centros.id_centro 
LEFT JOIN personas ON personas.id=personas_centros.id_persona WHERE cedula='".$documento."' and id_centro in (SELECT DISTINCT(id_centro) as id_centro from costos)
ORDER BY id_centro");


while($queryCen = $db->sql_fetchrow($qidCentro)){
	$centros[$queryCen["id_centro"]] = $queryCen["centro"];
	
/****CAPTURA EL VALOR DE CENTROS CON PERMISOS DE USUARIOS EN PROMO NACIONAL	****/	
/*=============================================================================*/
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
		$titulosFinanciera[] = $titulosVolumenes[] = $titulosEficiencia[] = $titulosIndFinan[] = $queryCen["centro"]." Consolidado";
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
/*=============================================================================*/
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
$titulosFinanciera[]=$titulosVolumenes[]=$titulosEficiencia[] = $titulosIndFinan[] = "PROMEDIOS CONSOLIDADOS";
$estilostitulosFinanciera[] = " class='azul_osc_14' width='150'";
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
		$anchotabla = (180*(count($titulosFinanciera)-2))+230;
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
#JFMC Para que solo los vean los Directores y Gerentes

if ($nivel==1 || $nivel==13 || $nivel==7 || $nivel==8 || $nivel==11 || $nivel==12 || $nivel==15){
	
	
	
/********************************************/
//      HASTA AQUI ENCABEZADOS Y TITULOS    //
/********************************************/	
	
	
	/**INDICADORES FINANCIEROS **/
	
	$lineaFactConj   = array("Facturación Conjunta");
	$lineaFactDirec  = array("Facturación Directa");
	$lineaFact       = array("Facturación Total");
	$lineaRecTot     = array("Recaudo Total");
	$lineaCart90dias = array("Cartera de mas de 90 días");
	$lineaCostFacUsu = array("Costo Facturación por usuario");
	$lineaFactGrGen  = array("Facturación Grandes Generadores");
	
	
	$lineaFactConjprom   = array("Facturación Conjunta");
	$lineaFactDirecprom  = array("Facturación Directa");
	$lineaFactprom       = array("Facturación Total");
	$lineaRecTotprom     = array("Recaudo Total");
	$lineaCart90diasprom = array("Cartera de mas de 90 días");
	$lineaCostFacUsuprom = array("Costo Facturación por usuario");
	$lineaFactGrGenprom  = array("Facturación Grandes Generadores");
	
	/**INDICADORES VOLUMENES **/
	
	$lineaAfoGrGen    = array("Aforo a Grandes Generadores");
	$linea5   		  = array("N° total de Usuarios");
	$lineaUVD  		  = array("Usuarios Vinculados Vs Desvinculados");
	$lineaPetServ     = array("Peticiones de Servicio");
	$lineaReclFact    = array("Reclamos por facturación");
	$lineaQueRecBar   = array("Quejas Recolección y Barrido");
	//$lineaPetServ     = array("Peticiones de Servicio");
	
	$lineaAfoGrGenprom    = array("Aforo a Grandes Generadores");
	$linea5prom   		  = array("N° total de Usuarios");
	$lineaUVDprom  		  = array("Usuarios Vinculados Vs Desvinculados");
	$lineaPetServprom     = array("Peticiones de Servicio");
	$lineaReclFactprom    = array("Reclamos por facturación");
	$lineaQueRecBarprom   = array("Quejas Recolección y Barrido");
	//$lineaPetServprom     = array("Peticiones de Servicio");

	/**INDICADORES EFICIENCIA **/
	
	$lineaRecau         	   = array("Indice de Recaudo Total");
	$lineaRC           		   = array("Indice de Recaudo Corriente");
	$lineaValorMCGrGen         = array("valor metro cubicos/ grandes generadores");
	$lineaTarEst4     		   = array("Tarifas estrato 4");
	$lineaCartCorFactAnual     = array("Cartera no corriente / facturacion anual");
	$lineaCartCorFactDiaria    = array("Cartera no corriente / facturacion diaria");

	$lineaRecauprom            = array("Indice de Recaudo Total");
	$lineaRCprom          	   = array("Indice de Recaudo Corriente");
	$lineaValorMCGrGenprom         = array("valor metro cubicos/ grandes generadores");
	$lineaTarEst4prom     		   = array("Tarifas estrato 4");
	$lineaCartCorFactAnualprom     = array("Cartera no corriente / facturacion anual");
	$lineaCartCorFactDiariaprom    = array("Cartera no corriente / facturacion diaria");
	
	
	
	/* 	
	$linea5 = array("N° total de Usuarios");
	$lineaRecau = array("Indice de Recaudo Total");
	$lineaRC = array("Indice de Recaudo Corriente");
	$lineaUVD = array("Usuarios Vinculados Vs Desvinculados");
	
	$linea5prom = array("N° total de usuarios");	
	$lineaRecauprom = array("Indice de Recaudo Total");
	$lineaRCprom = array("Indice de Recaudo Corriente");
	$lineaUVDprom = array("Usuarios Vinculados Vs Desvinculados"); */
	
	$otrasLineas = $nombreOtras = $otrasLineasDos  = array();
	$otrasLineasprom = array();

	#ind comerciales
	$qidOV = $dbnacionalproduccion->sql_query("SELECT * FROM variables_informes WHERE id=13 or id=14 or id=15 or id=17 or id=19 or id=20 or id=21 or id=22 or  (id >=23 AND id<=26) or id=37 or id= 38 ORDER BY orden");
	while($queryOV = $dbnacionalproduccion->sql_fetchrow($qidOV))
	{
		$nombreOtras[$queryOV["id"]] = $queryOV["variable"];
	}
	
	$qidOV2 = $dbpromo->sql_query("SELECT * FROM variables_informes WHERE id=13 or id=14 or id=15 or id=17 or id=19 or id=20 or id=21 or id=22 or  (id >=23 AND id<=26) or id=37 or id= 38 ORDER BY orden");
	while($queryOV = $dbpromo->sql_fetchrow($qidOV2))
	{
		$nombreOtras[$queryOV["id"]] = $queryOV["variable"];
	} 

	$cosMtto = $gastos = $numTa = $totEmp = $totFact = $totRecau = $totRC = $totUVD = $cosMO = $totindllanta =0;
	foreach($centros as $idCentro => $da)
	{
		$cosMttoCen = $gastosCen = $numTaCen = $totPetServCen = $totUsuCen = $totEmpCen = $totFactCen = $totRecauCen = $totRCCen = $totUVDCen = $cosMOCen =  0;
		$vars =  array();
		$totfec = 0;
		foreach($fechas as $mes)
		{
			if($idCentro!=15 and $idCentro!=14){
			$qid3 = $dbnacionalproduccion->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c 
			LEFT JOIN servicios s ON s.id=c.id_servicio WHERE id_centro='".$idCentro."'  
			AND id_variable_informe >= 13 AND id_variable_informe <= 76 AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
			
			while($query = $dbnacionalproduccion->sql_fetchrow($qid3)){
					$vars[$query["id_variable_informe"]][$mes] = $query["val"];	
			}
			}else{
			$qid3 = $dbpromo->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c 
			LEFT JOIN servicios s ON s.id=c.id_servicio WHERE id_centro='".$idCentro."'  
			AND id_variable_informe >= 13 AND id_variable_informe <= 76 AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
			
			
			while($query = $dbpromo->sql_fetchrow($qid3)){
					$vars[$query["id_variable_informe"]][$mes] = $query["val"];
				}
			}			
			if(isset($vars[17][$mes]))
			{
				$val = $vars[17][$mes];
				$linea5[] = number_format($val,0);
				$totUsuCen+= $val;
			}else
				$linea5[] = "";
			
				
			/*  Facturación Conjunta   **/
			/*==========================*/
			
			if(isset($vars[13][$mes])){
				$val = $vars[13][$mes];
				$lineaFactConj[] = number_format($val,0);
				$totFactConjCen+= $val;
			}else
				$lineaFactConj[] = "";

			/*  Facturación Directa    **/
			/*==========================*/
			
			if(isset($vars[14][$mes])){
				$val = $vars[14][$mes];
				$lineaFactDirec[] = number_format($val,0);
				$totFactDirecCen+= $val;
			}else
				$lineaFactDirec[] = "";	
			
			
			
			/*  FACTURACIÓN TOTAL  DESDE AQUI    **/
			/*===================================*/
			
			$valFact = nvl($vars[13][$mes],0) + nvl($vars[14][$mes],0);
			if ($valFact!=''){
				$totfec = $totfec+1;
			}
			
			$totFactCen+=$valFact;
			
			if ($valFact!=0)
				$lineaFact[] = number_format($valFact,0);
			else
				$lineaFact[] = '';
				
			

			/**    Indice de recaudo   **/
			/*==========================*/
			$valRecau = (nvl($vars[37][$mes],0)/$valFact)*100;
			if ($valRecau!=0){
				$lineaRecau[] = number_format($valRecau,2)."%";
				$lineaRecaucount[] = number_format($valRecau,2)."%";}
			else 
				$lineaRecau[] = '';
			$totRecauCen += $valRecau;
			
			/** Indice de Recaudo Corriente**/
			/*==============================*/
			
			$valRC =  (nvl($vars[19][$mes],0)/$valFact)*100;			
			 if ($valRC!=0)
				$lineaRC[] = number_format($valRC,2)."%";
			else
				$lineaRC[] = ''; 
			
			$totRCCen+=$valRC;

		
			
			/* HASTA AQUI */
			/*-----------------------------------*/
			
			
			/*    Recaudo Total        **/
			/*==========================*/
			
			if(isset($vars[37][$mes])){
				$val = $vars[37][$mes];
				$lineaRecTot[] = number_format($val,0);
				$totRecTotCen+= $val;
			}else
				$lineaRecTot[] = "";
			
			/*Facturción Grandes Generadoress**/
			/*================================*/
			
			if(isset($vars[15][$mes])){
				$val = $vars[15][$mes];
				$lineaFactGrGen[] = number_format($val,0);
				$totFactGrGenCen+= $val;
			}else
				$lineaFactGrGen[] = "";
			
			
			/*Costo facturado por usuario**/
			/*============================*/
			
			if(isset($vars[20][$mes])){
				$val = $vars[20][$mes];
				$lineaCostFacUsu[] = number_format($val,0);
				$totCostFacUsuCen+= $val;
			}else
				$lineaCostFacUsu[] = "";
			
			/*Cartera de más de 90 días**/
			/*==========================*/
			
			if(isset($vars[21][$mes])){
				$val = $vars[21][$mes];
				$lineaCart90dias[] = number_format($val,0);
				$totCart90diasCen+= $val;
			}else
				$lineaCart90dias[] = "";
			
			
			
			/*Aforo Grandes Generadores**/
			/*==========================*/
			
			if(isset($vars[22][$mes])){
				$val = $vars[22][$mes];
				$lineaAfoGrGen[] = number_format($val,0);
				$totAfoGrGenCen+= $val;
			}else
				$lineaAfoGrGen[] = "";
			
			
			
			/*valor metro cubicos/ grandes generadores**/
			/*=========================================*/
			
			// $vars[37][$mes];/$vars[22][$mes])
			 if (nvl($vars[22][$mes],2)>0){
			$valMC =  (nvl($vars[15][$mes],2)/nvl($vars[22][$mes],2));			
			 if ($valMC!=0){
				$lineaValorMCGrGen[] = number_format($valMC,2);
				$totValorMCGrGenCen+=$valMC;
			 }else{
				$lineaValorMCGrGen[] = ''; 
			 }}
			
			
			
			/*Reclamo por facturacion**/
			/*========================*/
			
			if(isset($vars[23][$mes])){
				$val = $vars[23][$mes];
				$lineaReclFact[] = number_format($val,0);
				$totReclFactCen+= $val;
			}else
				$lineaReclFact[] = "";

			
			/*Quejas Recolección y Barrido**/
			/*=============================*/
			if(isset($vars[24][$mes])){
				$val = $vars[24][$mes];
				$lineaQueRecBar[] = number_format($val,0);
				$totQueRecBarCen+= $val;
			}else
				$lineaQueRecBar[] = "";
			
			
			/*Peticiones de Servicio**/
			/*=======================*/
			
			if(isset($vars[25][$mes])){
				$val = $vars[25][$mes];
				$lineaPetServ[] = number_format($val,0);
				$totPetServCen+= $val;
			}else
				$lineaPetServ[] = "";
			
			
		
			/*Tarifas estrato 4**/
			/*=======================*/
			
			if(isset($vars[76][$mes])){
				$val = $vars[76][$mes];
				$lineaTarEst4[] = number_format($val,0);
				$totTarEst4Cen+= $val;
			}else
				$lineaTarEst4[] = "";
			
			
			/*Cartera no corriente / facturacion anual**/
			/*=======================*/
			
			if(isset($valFact)){
				$valFactAnual = (nvl($vars[21][$mes],2)/$valFact);
				$lineaCartCorFactAnual[] = number_format($valFactAnual,2);
				$lineaCartCorFactAnualen+= $valFactAnual;
			}else
				$lineaCartCorFactAnual[] = "";
			
			
			
			
			/*Cartera no corriente / facturacion diaria**/
			/*=======================*/
			
			if(isset($valFactAnual)){
				$val = $valFactAnual/30;
				$lineaCartCorFactDiaria[] = number_format($val,2);
				$lineaCartCorFactAnualen+= $val;
			}else
				$lineaCartCorFactDiaria[] = "";
			
				
	
			#ind comerciales
			foreach($nombreOtras as $id => $name)
			{
				if(isset($vars[$id][$mes]))
					$otrasLineas[$id][$idCentro][$mes] = $vars[$id][$mes];
				else
					$otrasLineas[$id][$idCentro][$mes] = "";
			}
			
			 if(isset($vars[38][$mes])){			
			$valUVD = nvl($vars[26][$mes],0)-nvl($vars[38][$mes],0);
			$lineaUVD[] = number_format($valUVD,0);
			$totUVDCen+=$valUVD;
			}else
			$lineaUVD[] = ""; 			
		}
		
			
		$totUsuCen = $totUsuCen/count($fechas);
		$linea5prom[] = number_format($totUsuCen,0);
		$totUsu+=$totUsuCen;
		
		
		$totPetServCen = $totPetServCen/count($fechas);
		$lineaPetServprom[] = number_format($totPetServCen,0);
		$totUsuPet+=$totPetServCen;
		
		$totTarEst4Cen = $totTarEst4Cen/count($fechas);
		$lineaTarEst4prom[] = number_format($totTarEst4Cen,0);
		$totUsuTarEst4+=$totTarEst4Cen;
		
		
		$totReclFactCen = $totReclFactCen/count($fechas);
		$lineaReclFactprom[] = number_format($totReclFactCen,0);
		$totUsuReclFact+=$totReclFactCen;
		
		$totFactCen = $totFactCen/count($lineaRecaucount);
		$lineaFactprom1[] = $totFactCen;
		$lineaFactprom[] = number_format($totFactCen,0);
		$totFact+=$totFactCen;
		
		$totQueRecBarCen = $totQueRecBarCen/count($fechas);
		$lineaQueRecBarprom[] = number_format($totQueRecBarCen,0);
		$totUsuQueRecBar+=$totQueRecBarCen;
		
		
		$totFactGrGenCen = $totFactGrGenCen/count($fechas);
		$lineaFactGrGenprom[] = number_format($totFactGrGenCen,0);
		$totUsuFactGrGenCen+=$totFactGrGenCen;
		
		$totCostFacUsuCen = $totCostFacUsuCen/count($fechas);
		$lineaCostFacUsuprom[] = number_format($totCostFacUsuCen,0);
		$totUsuCostFacUsuCen+=$totCostFacUsuCen;
		
		$totCart90diasCen = $totCart90diasCen/count($fechas);
		$lineaCart90diasprom[] = number_format($totCart90diasCen,0);
		$totUsuCart90diasCen+=$totCart90diasCen;
		
		$totValorMCGrGenCen = $totValorMCGrGenCen/count($fechas);
		$lineaValorMCGrGenprom[] = number_format($totValorMCGrGenCen,0);
		$totUsuValorMCGrGenCen+=$totValorMCGrGenCen;
		
		$totAfoGrGenCen = $totAfoGrGenCen/count($fechas);
		$lineaAfoGrGenprom[] = number_format($totAfoGrGenCen,0);
		$totUsuAfoGrGenCen+=$totAfoGrGenCen;
		
		$totFactDirecCen = $totFactDirecCen/count($fechas);
		$lineaFactDirecprom[] = number_format($totFactDirecCen,0);
		$totUsuFactDirec+=$totFactDirecCen;
		
		$totRecTotCen = $totRecTotCen/count($fechas);
		$lineaRecTotprom[] = number_format($totRecTotCen,0);
		$totUsuRecTotCen+=$totRecTotCen;
		
		
		
		$totFactConjCen = $totFactConjCen/count($fechas);
		$lineaFactConjprom[] = number_format($totFactConjCen,0);
		$totUsuFactConjCen+=$totFactConjCen;

		
		$totFactCen = $totFactCen/count($lineaRecaucount);
		$lineaFactprom1[] = $totFactCen;
		$totFact+=$totFactCen;

		$totRecauCen = $totRecauCen/count($lineaRecaucount);
		$lineaRecauprom1[]  = $totRecauCen;
		$lineaRecauprom[]  = number_format($totRecauCen,2).'%';
		$totRecau+=$totRecauCen;

		$totRCCen = $totRCCen/count($lineaRecaucount);
		$lineaRCprom[] = number_format($totRCCen,2)."%";
		$totRC+=$totRCCen;

		$totUVDCen = $totUVDCen/count($fechas);
		$lineaUVDprom[] = number_format($totUVDCen,0);
		$totUVD+=$totUVDCen;
		unset($lineaRecaucount);
		
		/**CALCULAR BIEN**/
		$lineaCartCorFactAnualprom[] = "";
		$lineaCartCorFactDiariaprom[] = "";
			
		/*** VALOR ULTIMO MES ***/
		
		$linea5prom[] = number_format($vars[17][$mes],0);		
		$lineaPetServprom[] = number_format($vars[25][$mes],0);		
		$lineaTarEst4prom[] = number_format($vars[76][$mes],0);
		$lineaReclFactprom[] = number_format($vars[23][$mes],0);		
		$lineaFactprom[] = number_format(nvl($vars[13][$mes],0) + nvl($vars[14][$mes],0),0);
		
		$lineaQueRecBarprom[] = number_format($vars[24][$mes],0);
		$lineaFactGrGenprom[] = number_format($vars[15][$mes],0);
		$lineaCostFacUsuprom[] = number_format($vars[20][$mes],0);
		$lineaCart90diasprom[] = number_format($vars[21][$mes],0);
		$lineaAfoGrGenprom[] = number_format($vars[22][$mes],0);
		$lineaFactDirecprom[] = number_format($vars[14][$mes],0);
		$lineaRecTotprom[] = number_format($vars[37][$mes],0);
		$lineaFactConjprom[] = number_format($vars[13][$mes],0);
		$lineaValorMCGrGenprom[] = number_format(((nvl($vars[15][$mes],2)/nvl($vars[22][$mes],2))),0);
		$lineaRecauprom[]  = number_format(((nvl($vars[37][$mes],0)/$valFact)*100),2).'%';
		$lineaRCprom[] = number_format(((nvl($vars[19][$mes],0)/$valFact)*100),2)."%";
		$lineaUVDprom[] = number_format((nvl($vars[26][$mes],0)-nvl($vars[38][$mes],0)),0);
				
				
			/**CALCULAR BIEN**/
		$lineaCartCorFactAnualprom[] = "";
		$lineaCartCorFactDiariaprom[] = "";
	
		/*** HASTA AQUI VALOR ULTIMO MES ***/
		
	}

	$linea5[] = number_format($totUsu,0);
	$lineaRecau[]  = number_format($totRecau/count($centros),0)."%";
	$lineaRC[] = number_format($totRC/count($centros),0)."%";
	$lineaUVD[] = number_format($totUVD/count($centros),0);	
	$lineaPetServ[]  = number_format($totUsuPet,0);
	$lineaReclFact[] = number_format($totUsuReclFact,0);
	$lineaQueRecBar[] = number_format($totUsuQueRecBar,0);
	$lineaAfoGrGen[] = number_format($totUsuAfoGrGenCen,0); 	
	$lineaFactConj[] = number_format($totUsuFactConjCen,0);	
	$lineaFactDirec[] = number_format($totUsuFactDirec,0); 
	$lineaFact[] = number_format($totFact/count($centros),0);
	
	$lineaRecTot[] = number_format($totUsuRecTotCen,0);	
	$lineaCart90dias[] = number_format($totUsuCart90diasCen,0);
	$lineaCostFacUsu[] = number_format($totUsuCostFacUsuCen,0);
	$lineaFactGrGen[] = number_format($totUsuFactGrGenCen,0);			
	$lineaValorMCGrGen[] = number_format($totUsuValorMCGrGenCen,0);
	$lineaTarEst4[] = number_format($totUsuTarEst4,0);
	
	
	$linea5prom[] = number_format($totUsu,0);
	$lineaRecauprom[]  = number_format(array_sum($lineaRecauprom1)/count($lineaRecauprom1),2).'%';
	$lineaRCprom[] = number_format($totRC/count($centros),2)."%";
	$lineaUVDprom[] = number_format($totUVD/count($centros),0);
	$lineaPetServprom[]   = number_format($totUsuPet,0);
	$lineaReclFactprom[]  = number_format($totUsuReclFact,0);
	$lineaQueRecBarprom[] = number_format($totUsuQueRecBar,0); 
	$lineaAfoGrGenprom[]  = number_format($totUsuAfoGrGenCen,0); 
	$lineaFactConjprom[]  = number_format($totUsuFactConjCen,0); 	
	$lineaFactDirecprom[]  = number_format($totUsuFactDirec,0); 
	$lineaFactprom[] = number_format($totFact/count($centros),0);
	
	$lineaRecTotprom[] = number_format($totUsuRecTotCen,0);	
	$lineaCart90diasprom[] = number_format($totUsuCart90diasCen,0);	
	$lineaCostFacUsuprom[] = number_format($totUsuCostFacUsuCen,0);	
	$lineaFactGrGenprom[] = number_format($totUsuFactGrGenCen,0);			
	$lineaValorMCGrGenprom[] = number_format($totUsuValorMCGrGenCen,0);	
	$lineaTarEst4prom [] = number_format($totUsuTarEst4,0);
	
	
	
					
			/**CALCULAR BIEN**/
		$lineaCartCorFactAnual[] = "";
		$lineaCartCorFactDiaria[] = "";
		$lineaCartCorFactAnualprom[] = "";
		$lineaCartCorFactDiariaprom[] = "";
	
	
	
	
	
	
	
#########################################	
#MOSTRAMOS LA INFORMACIÓN DE FINANCIEROS#
#########################################

#generales
	if($html)
		imprimirLinea($titulosGenerales, "#b2d2e1", $estilosTitulosRecoleccion);
	else
	{
		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosGenerales, array(1=>"azul_izq"));
		$fila++;$columna=0;
	}
	
	
if($html){
		if ($tipo_info==1){
			imprimirLinea($lineaFactConj,"", $estilos);
		    imprimirLinea($lineaFactDirec,"", $estilos);
			imprimirLinea($lineaFact,"", $estilos); 
			imprimirLinea($lineaRecTot,"", $estilos); 
			imprimirLinea($lineaCart90dias,"", $estilos); 
			imprimirLinea($lineaCostFacUsu,"", $estilos); 
			imprimirLinea($lineaFactGrGen,"", $estilos); 
		   
		}
		else{
			imprimirLinea($lineaFactConjprom,"", $estilos);
			imprimirLinea($lineaFactDirecprom,"", $estilos);
			imprimirLinea($lineaFactprom,"", $estilos); 
			imprimirLinea($lineaRecTotprom,"", $estilos);
			imprimirLinea($lineaCart90diasprom,"", $estilos); 
			imprimirLinea($lineaCostFacUsuprom,"", $estilos);
			imprimirLinea($lineaFactGrGenprom,"", $estilos);  			
		}
	}
	else {
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaFactConj, array(1=>"txt_izq"));
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaFactDirec, array(1=>"txt_izq"));
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaFact, array(1=>"txt_izq"));
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaRecTot, array(1=>"txt_izq"));
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaCart90dias, array(1=>"txt_izq"));
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaCostFacUsu, array(1=>"txt_izq"));
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaFactGrGen, array(1=>"txt_izq"));
	
	
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
	if($html){
		if ($tipo_info==1){
		   imprimirLinea($lineaAfoGrGen,"", $estilos);
		   imprimirLinea($linea5,"", $estilos);
		   imprimirLinea($lineaUVD,"", $estilos);		 
		   imprimirLinea($lineaPetServ,"", $estilos);
		   imprimirLinea($lineaReclFact,"", $estilos);
		   imprimirLinea($lineaQueRecBar,"", $estilos);
		   
		   
		   
		   
		  
		}
		else{
			imprimirLinea($lineaAfoGrGenprom,"", $estilos);
			imprimirLinea($linea5prom,"", $estilos);
			imprimirLinea($lineaUVDprom,"", $estilos);			
			imprimirLinea($lineaPetServprom,"", $estilos);
			imprimirLinea($lineaReclFactprom,"", $estilos);
			imprimirLinea($lineaQueRecBarprom,"", $estilos);
		}
	}
	else {
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaAfoGrGen, array(1=>"txt_izq"));
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea5, array(1=>"txt_izq"));
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaUVD, array(1=>"txt_izq"));		
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaPetServ, array(1=>"txt_izq"));
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaReclFact, array(1=>"txt_izq"));
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaQueRecBar, array(1=>"txt_izq"));
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

if($html)
{
	if ($tipo_info==1){
	
		imprimirLinea($lineaRecau,"", $estilos); 
		imprimirLinea($lineaRC,"", $estilos); 
		imprimirLinea($lineaValorMCGrGen,"", $estilos); 
		imprimirLinea($lineaTarEst4,"", $estilos); 
		
		imprimirLinea($lineaCartCorFactAnual,"", $estilos); 
		imprimirLinea($lineaCartCorFactDiaria,"", $estilos); 

		
		
	}else {
		
		imprimirLinea($lineaRecauprom,"", $estilos); 
		imprimirLinea($lineaRCprom,"", $estilos); 
		imprimirLinea($lineaValorMCGrGenprom,"", $estilos); 
		imprimirLinea($lineaTarEst4prom,"", $estilos); 
		
		imprimirLinea($lineaCartCorFactAnualprom,"", $estilos); 
		imprimirLinea($lineaCartCorFactDiariaprom,"", $estilos); 
	}
}else{
	
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaRecau, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaRC, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaValorMCGrGen, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaTarEst4, array(1=>"txt_izq"));
	
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaCartCorFactAnual, array(1=>"txt_izq"));	
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaCartCorFactDiaria, array(1=>"txt_izq"));
	

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
