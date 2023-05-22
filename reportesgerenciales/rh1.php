<?
/*-----------------------------------------------------------------------------------------
'Descripción       :  Visualiza los indicadores para el reporte para la gerencia Recursos Humanos    
'Autor      		: Juan Carlos Jaramillo . Aseo Regional - Promoambiental Distrito.
'Fecha de Creación  : Agosto 08/2019
'-------------------------------------------------------------------------------------------
'	Propósito :	Armar el reporte de los indicadores para la Gerencia Recursos Humanos. Visualizar 
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
$titulo1["inf"]="INDICADORES GERENCIALES : INDICADORES GESTIÓN HUMANA";
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
	
	$lineaCostoManoObra = array("Costo Total Mano de Obra");
	$lineaCostoNominaVentas = array("Costo Nómina sobre Ventas Totales");
	$lineaIndicadorRMPerCosto = array("Cantidad de los RM | Costo de los RM");				
		
	$lineaCostoManoObraprom = array("Costo Total Mano de Obra");
	$lineaCostoNominaVentasprom = array("Costo Nómina sobre Ventas Totales");	
	$lineaIndicadorRMPerCostoprom = array("Cantidad de los RM | Costo de los RM");		
			
	/**INDICADORES VOLUMENES **/		
		
	$lineaAccimes = array("N° Accidentes por Mes");	
	$lineaPersMes = array("N° Personas por Mes");
	$lineaDP = array("Días Ausentismo | Costo Ausentismo");
	$lineaIndicadorDiasPedidos = array("Dias Ausentismo");
		
	$lineaAccimesprom = array("N° Accidentes por Mes");
	$lineaPersMesprom = array("N° Personas por Mes");
	$lineaDPprom = array("Días Ausentismo | Costo Ausentismo");
	$lineaIndicadorDiasPedidosprom = array("Dias Ausentismo");


	/**INDICADORES EFICIENCIA **/
	
	$lineaIndiceAccid = array("Indice de Accidentes");
	$lineaIR = array("Indice de Rotación | Costo Rotación");		
	//$lineaRetiros = array("Rotación por Mes");	
	
	$lineaIndiceAccidprom = array("Indice de Accidentes");	
	$lineaIRprom = array("Indice de Rotación | Costo Rotación");	
	//$lineaRetirosprom = array("Rotación por Mes");
	


	#ind rrhh
	$totIndicadorDiasPedidos = $totAccMes = $cosMtto = $gastos = $numTa = $totEmp = $totFact = $totRecau = $totRC = $totUVD = $cosMO = $totindllanta =0;
	foreach($centros as $idCentro => $da)
	{
		$totUsuCen = $totalcosto = $cosMttoCen = $gastosCen = $numTaCen =  $totDP1Cen = $totDP2Cen = $totIndicadorDiasPedidoscen = $totIndiceAccidcen = $totlineaIR1Cen = $totlineaIR2Cen = 0;
		$valFact  =   $valReNom =  $totCostoNominaVentasCen   =  $val1  =   $val2   =  $totIndicadorRMPerCosto1Cen   =   $totIndicadorRMPerCosto2Cen  =   $valacidente   = $totAccimesCen  =  $valpersonas  =  $totNumPerMesCen = 0;
		$valdias   =  $valcosto  =  $totDP1Cen  =  $totDP2Cen  =  $totIndicadorDiasPedidoscen  =  $valindiacc  =   $totIndiceAccidcen  =  $valindirot  =  $valcostorotacion  = $totlineaIR1Cen  =  $totlineaIR2Cen = 0;
		$vars =  array();
	
		foreach($fechas as $mes)
		{
			if($idCentro!=15 and $idCentro!=14){
			$qid3 = $dbnacionalproduccion->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c 
			LEFT JOIN servicios s ON s.id=c.id_servicio WHERE id_centro='".$idCentro."'  
			AND id_variable_informe >= 6 AND id_variable_informe <= 75  AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
			
			while($query = $dbnacionalproduccion->sql_fetchrow($qid3)){
					$vars[$query["id_variable_informe"]][$mes] = $query["val"];	
			}
			}else{
			$qid3 = $dbpromo->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c 
			LEFT JOIN servicios s ON s.id=c.id_servicio WHERE id_centro='".$idCentro."'  
			AND id_variable_informe >= 6 AND id_variable_informe <= 75 	 AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
			
			
			while($query = $dbpromo->sql_fetchrow($qid3)){
					$vars[$query["id_variable_informe"]][$mes] = $query["val"];
				}
			}	

			
			if(isset($vars[9][$mes])){
				$val = $vars[9][$mes];
				$lineaCostoManoObra[] = number_format($val,0);
				$totUsuCen+= $val;
				$totalcosto=$totalcosto+1;
			}else
				$lineaCostoManoObra[] = "";
			
				
				/*Costo nomina sobre ventas totales**/
			/*============================*/
			
			if(isset($vars[9][$mes])  || (isset($vars[13][$mes])) || (isset($vars[14][$mes]))){
				$valFact = nvl($vars[13][$mes],2) + nvl($vars[14][$mes],2);				
				$valReNom = (nvl($vars[9][$mes],2)/$valFact)*100;
				$lineaCostoNominaVentas[] = number_format($valReNom,2)."%";				
				$totCostoNominaVentasCen+= $valReNom;
			}else
				$lineaCostoNominaVentas[] = "";



			/*  Cantidad de los RM | Costo de los RM   **/
			/*==========================================*/
			
			if(isset($vars[54][$mes]) || isset($vars[55][$mes])){
				$val1 = nvl($vars[54][$mes],2);
				$val2 = nvl($vars[55][$mes],2);
				$lineaIndicadorRMPerCosto[] = number_format($val1,2)." | ".number_format($val2,2);
				$totIndicadorRMPerCosto1Cen+= $val1;
				$totIndicadorRMPerCosto2Cen+= $val2;
			}else
				$lineaIndicadorRMPerCosto[] = "";




			/*  No. Accidente por Mes   **/
			/*==========================*/
			
			if(isset($vars[56][$mes])){
				$valacidente = nvl($vars[56][$mes],0);
				$lineaAccimes[] = number_format($valacidente,0);
				$totAccimesCen+= $valacidente;
			}else
				$lineaAcciMes[] = "";	



			/* No. Personas por Mes**/
			/*================================*/
			
			if(isset($vars[16][$mes])){
				$valpersonas = nvl($vars[16][$mes]);
				$lineaPersMes[] = number_format($valpersonas,0);
				$totNumPerMesCen+= $valpersonas;
			}else
				$lineaPersMes[] = "";


			/*  Dias Ausentismo | Costo Ausentismo   **/
			/*=========================================*/
			
			if(isset($vars[58][$mes]) || isset($vars[59][$mes])){
				$valdias = nvl($vars[58][$mes]);
				$valcosto = nvl($vars[59][$mes]);
				$lineaDP[] = number_format($valdias,2)." | ".number_format($valcosto,2);
			    $totDP1Cen+= $valdias;
				$totDP2Cen+= $valcosto;
			}else
				$lineaDP[] = "";
			
			
			
					/* Dias Ausentismo**/
			/*================================*/
			
			if(isset($vars[58][$mes])){
				$valperdidos = nvl($vars[58][$mes]);
				$lineaIndicadorDiasPedidos[] = number_format($valperdidos,0);
				$totIndicadorDiasPedidoscen+= $valperdidos;
			}else
				$lineaIndicadorDiasPedidos[] = "";
			
			
			/* Indice de Accidentes**/
			/*================================*/		
			
			if(isset($vars[56][$mes]) && isset($vars[16][$mes]))
			{
				$valindiacc=(nvl($vars[56][$mes])/nvl($vars[16][$mes]))*100;
				$lineaIndiceAccid[] = number_format($valindiacc,2).'%';
				$totIndiceAccidcen+=$valindiacc;
			}else
				$lineaIndiceAccid[] = "";
			
			
				/* Indice de Rotación | Costo Rotación**/
			/*================================*/
			
			if(isset($vars[75][$mes]) || isset($vars[16][$mes]) || isset($vars[71][$mes])){				
				$valindirot=(nvl($vars[75][$mes])/nvl($vars[16][$mes]))*100;
				$valcostorotacion = nvl($vars[71][$mes]);
				$lineaIR[] = number_format($valindirot,2)."% | " .number_format($valcostorotacion,2);				
				$totlineaIR1Cen+= $valindirot;
				$totlineaIR2Cen+= $valcostorotacion;
			
			
			}else
				$lineaIR[] = "";						
		}
		
		
		$totUsuCen = $totUsuCen/$totalcosto;
		$lineaCostoManoObraprom[] = number_format($totUsuCen,2);
		$totCostoManoObra+=$totUsuCen;		
		
		$totCostoNominaVentasCen = $totCostoNominaVentasCen/count($fechas);
		$lineaCostoNominaVentasprom[] = number_format($totCostoNominaVentasCen,2).'%';
		$totCostoNominaVentas+=$totCostoNominaVentasCen;		
		
		$totIndicadorRMPerCosto1Cen = $totIndicadorRMPerCosto1Cen/count($fechas);
		$totIndicadorRMPerCosto2Cen = $totIndicadorRMPerCosto2Cen/count($fechas);
		$lineaIndicadorRMPerCostoprom[] = number_format($totIndicadorRMPerCosto1Cen,2)." | ".number_format($totIndicadorRMPerCosto2Cen,2);
		$totIndicadorRMPerCosto1+=$totIndicadorRMPerCosto1Cen;
		$totIndicadorRMPerCosto2+=$totIndicadorRMPerCosto2Cen;		
		
		$totAccimesCen = $totAccimesCen/count($fechas);
		$lineaAcciMesprom[] = number_format($totAccimesCen,2);
		$totAccMes+=$totAccimesCen;		
				
		$totNumPerMesCen = $totNumPerMesCen/count($fechas);
		$lineaPersMesprom[] = number_format($totNumPerMesCen,2);
		$totPersMes+=$totNumPerMesCen;				
		
		$totDP1Cen = $totDP1Cen/count($fechas);
		$totDP2Cen = $totDP2Cen/count($fechas);
		$lineaDPprom[] = number_format($totDP1Cen,2)." | ".number_format($totDP2Cen,2);
		$totlineaDP1+=$totDP1Cen;
		$totlineaDP2+=$totDP2Cen;		
		
		$totIndicadorDiasPedidoscen = $totIndicadorDiasPedidoscen/count($fechas);
		$lineaIndicadorDiasPedidosprom[] = number_format($totIndicadorDiasPedidoscen,2);
		$totIndicadorDiasPedidos+=$totIndicadorDiasPedidoscen;			
	
		$totIndiceAccidcen = $totIndiceAccidcen/count($fechas);
		$lineaIndiceAccidprom[] = number_format($totIndiceAccidcen,2).'%';
		$totIndiceAccid+=$totIndiceAccidcen;		
		
		$totlineaIR1Cen = $totlineaIR1Cen/count($fechas);
		$totlineaIR2Cen = $totlineaIR2Cen/count($fechas);
		$lineaIRprom[] =  number_format($totlineaIR1Cen,2)."% | " .number_format($totlineaIR2Cen,2);
		$totlineaIR1+=$totlineaIR1Cen;
		$totlineaIR2+=$totlineaIR2Cen;			
		
		
			
		
		/*** VALOR ULTIMO MES ***/
		$lineaCostoManoObraprom[] = number_format($vars[9][$mes],2);		
		$lineaCostoNominaVentasprom[] = number_format(((nvl($vars[9][$mes],0)/(nvl($vars[13][$mes],0)+ nvl($vars[14][$mes],0)))*100),2)."%";
		$lineaIndicadorRMPerCostoprom[] = number_format(nvl($vars[54][$mes]),0)." | ".number_format(nvl($vars[55][$mes]),0);
		$lineaAccimesprom[]  = number_format($vars[56][$mes],2);
		$lineaPersMesprom[]  = number_format($vars[16][$mes],2);
		$lineaDPprom[] = number_format( nvl($vars[58][$mes]),2)." | ".number_format(nvl($vars[59][$mes]),2);
		$lineaIndicadorDiasPedidosprom[]  = number_format($vars[58][$mes],2);		
		$lineaIndiceAccidprom[]  = number_format(((nvl($vars[56][$mes],2)/nvl($vars[16][$mes],2))*100),2)."%";		
		$lineaIRprom[] = number_format(((nvl($vars[75][$mes])/nvl($vars[16][$mes]))*100),2)."% | " .number_format(nvl($vars[71][$mes],2),2);			
		
		/*** HASTA AQUI VALOR ULTIMO MES ***/
		
	}

	$lineaCostoManoObra[] = number_format($totCostoManoObra,0);
	$lineaCostoNominaVentas[] = number_format($totCostoNominaVentas,2).'%';
	$lineaIndicadorRMPerCosto[] = number_format($totIndicadorRMPerCosto1,2)." | ".number_format($totIndicadorRMPerCosto2,2);
	$lineaAccimes[] = number_format($totAccMes,0);	
	$lineaPersMes[] = number_format($totPersMes,2);	
	$lineaDP[] = number_format($totlineaDP1,2)." | ".number_format($totlineaDP2,2);	
	$lineaIndicadorDiasPedidos[] = number_format($$totIndicadorDiasPedidos,0);	
	$lineaIndiceAccid[] = number_format($totIndiceAccid,0);
	$lineaIR[] = number_format($totlineaIR1,2)."% | " .number_format($totlineaIR2,2);
	
	
	
	
	
	
	$lineaCostoManoObraprom[] = number_format($totCostoManoObra,0);
	$lineaCostoNominaVentasprom[] = number_format($totCostoNominaVentas,2).'%';
	$lineaIndicadorRMPerCostoprom[] = number_format($totIndicadorRMPerCosto1,2)." | ".number_format($totIndicadorRMPerCosto2,2);
	$lineaAccimesprom[] = number_format($totAccMes,0);	
	$lineaPersMesprom[] = number_format($totPersMes,2);	
	$lineaDPprom[] = number_format($totlineaDP1,2)." | ".number_format($totlineaDP2,2);	
	$lineaIndicadorDiasPedidosprom[] = number_format($totIndicadorDiasPedidos,0);	
	$lineaIndiceAccidprom[] = number_format($totIndiceAccid,0);
	$lineaIRprom[] = number_format($totlineaIR1,2)."% | " .number_format($totlineaIR2,2);     
	
	
	
	
	
#########################################	
#MOSTRAMOS LA INFORMACIÓN DE FINANCIEROS#
#########################################

#generales
	if($html)
		imprimirLinea($titulosFinanciera, "#b2d2e1", $estilostitulosFinanciera);
	else
	{
		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosFinanciera, array(1=>"azul_izq"));
		$fila++;$columna=0;
	}
	
	
if($html){
		if ($tipo_info==1){
			imprimirLinea($lineaCostoManoObra,"", $estilos);
		    imprimirLinea($lineaCostoNominaVentas,"", $estilos);
			imprimirLinea($lineaIndicadorRMPerCosto,"", $estilos); 
			
		   
		}
		else{
			imprimirLinea($lineaCostoManoObraprom,"", $estilos);
			imprimirLinea($lineaCostoNominaVentasprom,"", $estilos);			
			imprimirLinea($lineaIndicadorRMPerCostoprom,"", $estilos); 
					
		}
	}
	else {
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaCostoManoObra, array(1=>"txt_izq"));
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaCostoNominaVentas, array(1=>"txt_izq"));
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaIndicadorRMPerCosto, array(1=>"txt_izq"));	
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
		   imprimirLinea($lineaAccimes,"", $estilos);
		   imprimirLinea($lineaPersMes,"", $estilos);
		   imprimirLinea($lineaDP,"", $estilos);		 
		   imprimirLinea($lineaIndicadorDiasPedidos,"", $estilos);
		
		  
		}
		else{
		   imprimirLinea($lineaAccimesprom,"", $estilos);
		   imprimirLinea($lineaPersMesprom,"", $estilos);
		   imprimirLinea($lineaDPprom,"", $estilos);		 
		   imprimirLinea($lineaIndicadorDiasPedidosprom,"", $estilos);
	
		}
	}
	else {
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaAccimes, array(1=>"txt_izq"));
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaPersMes, array(1=>"txt_izq"));
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaDP, array(1=>"txt_izq"));		
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaIndicadorDiasPedidos, array(1=>"txt_izq"));
		
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
	
		imprimirLinea($lineaIndiceAccid,"", $estilos); 
		imprimirLinea($lineaIR,"", $estilos); 
	
		
		
	}else {
		
		imprimirLinea($lineaIndiceAccidprom,"", $estilos); 
		imprimirLinea($lineaIRprom,"", $estilos); 
	
		
	}
}else{
	
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaIndiceAccid, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaIR, array(1=>"txt_izq"));
	

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
