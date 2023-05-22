<?
/*-----------------------------------------------------------------------------------------
'Descripción       :  Visualiza los indicadores para el reporte para la Gerencia Recursos Humanos
'                     
'Autor      		: Juan Carlos Jaramillo . Aseo Regional - Promoambiental Distrito.
'Fecha de Creación  : Agosto 08/2019
'-------------------------------------------------------------------------------------------
'	Propósito :	Armar el reporte de los indicadores para la Gerencia Recursos Humanos. Visualizar 
				la información de manera consolidada y Detallada.	
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
$titulo1["inf"]="INDICADORES GERENCIALES : INDICADORES DE RECURSOS HUMANOS";
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

/********************************************/
	   // HASTA AQUI ENCABEZADOS Y TITULOS //
/********************************************/	
	
	$lineaAccimes = array("N° Accidentes por Mes");
	$lineaPersMes = array("N° Personas por Mes");
	$lineaIndiceAccid = array("Indice de Accidentes");
	$lineaIR = array("Indice de Rotación | Costo Rotación");	
	$lineaDP = array("Días Ausentismo | Costo Ausentismo");
	$lineaRetiros = array("Rotación por Mes");	
	$lineaCostoNominaVentas = array("Costo Nómina sobre Ventas Totales");
	$lineaIndicadorRMPerCosto = array("Cantidad de los RM | Costo de los RM");	
	$lineaCostoManoObra = array("Costo Total Mano de Obra");	
	$lineaIndicadorDiasPedidos = array("Dias Ausentismo");
	
	
	$lineaAccimesprom = array("N° Accidentes por Mes");
	$lineaPersMesprom = array("N° Personas por Mes");
	$lineaIndiceAccidprom = array("Indice de Accidentes");	
	$lineaIRprom = array("Indice de Rotación | Costo Rotación");
	$lineaDPprom = array("Días Ausentismo | Costo Ausentismo");
	$lineaRetirosprom = array("Rotación por Mes");
	$lineaCostoNominaVentasprom = array("Costo Nómina sobre Ventas Totales");
	$lineaIndicadorRMPerCostoprom = array("Cantidad de los RM | Costo de los RM");	
	$lineaCostoManoObraprom = array("Costo Total Mano de Obra");	
	$lineaIndicadorDiasPedidosprom = array("Dias Ausentismo");
	
	
	$kmTotAccimesCen = $kmTotPersMesCen = $kmTotIndiceAccidCen = $kmTotIRCen = $kmTotDPCen = $kmTotDPCen2 = 0;
	foreach($centros as $idCentro => $da)
	{
		$cosMttoCen = $gastosCen = $numTaCen = $totUsuCen = $totEmpCen = $totFactCen = $totRecauCen = $totRCCen = $totUVDCen = $cosMOCen = $counmeslineaAccimes = $counmeslineaPersMes = $counmeslineaDP = $counmeslineaRetiros = $counmeslineaAccid = $totlineaAccimesCen = $totlineaPersCen = $totDPCen = $totDPCen2 = $totlineaDiaPerdidoCen = $totRetirosCen = $totlineaIndiceAccidCen = $totlineaIRCen = 0;
		$vars =  array();	
		
		foreach($fechas as $mes)
		{
			 
		
			$qid3 = $dbcorporativo->sql_query("SELECT sum(c.numero_accidentes) as numaccidentes, sum(c.numero_personas) as numpersonas, sum(c.numero_dias_perdidos) as numdiasperdidos,
			sum(c.numero_personas_retiradas) as numpersonasretiradas, 
			sum(c.costo_rotacion) as costorotacion,
			sum(c.costo_ausentismo) as costoausentismo,
			sum(c.costo_mano_obra) as costomanodeobra	
			FROM rrhh_indicadores c 
			 WHERE id_centro='".$idCentro."'  and
			 fecha = '".$mes."' GROUP BY id_empresa");
			 
			 
			while($query = $dbcorporativo->sql_fetchrow($qid3))
			{
				$vars[1][$mes]  = $query["numaccidentes"];
				$vars[2][$mes]  = $query["numpersonas"];
				$vars[3][$mes] = $query["numdiasperdidos"];
				$vars[4][$mes] = $query["numpersonasretiradas"];
				$vars[5][$mes] = $query["costorotacion"];
				$vars[6][$mes] = $query["costoausentismo"];
				$vars[7][$mes] = $query["costomanodeobra"];
	
			}
			
			if(isset($vars[1][$mes]))
			{
				$val = nvl($vars[1][$mes],0);
				$lineaAccimes[] = number_format($val,0);
				$totlineaAccimesCen+= $val;
				$counmeslineaAccimes = $counmeslineaAccimes+1;
			}else
				$lineaAccimes[] = "";
			
			if(isset($vars[2][$mes]))
			{
				$val = $vars[2][$mes];
				$lineaPersMes[] = number_format($val,0);
				$totlineaPersCen+= $val;
				$counmeslineaPersMes = $counmeslineaPersMes+1;
			}else
				$lineaPersMes[] = "";
			
			
			if(isset($vars[4][$mes]))
			{
				$val = $vars[4][$mes];
				$lineaRetiros[] = number_format($val,0);
				$totRetirosCen+= $val;
				$counmeslineaRetiros = $counmeslineaRetiros+1;
			}else
				$lineaRetiros[] = "";
		
		
			if(isset($vars[1][$mes]) && isset($vars[2][$mes]))
			{
				$val = (nvl($vars[1][$mes],0)/nvl($vars[2][$mes],0))*100;
				$lineaIndiceAccid[]=number_format($val,2)."%";
				$totlineaIndiceAccidCen+=$val;
				$counmeslineaAccid = $counmeslineaAccid+1;
			}
			else
				$lineaIndiceAccid[] = "";
			
			if(isset($vars[4][$mes]) && isset($vars[2][$mes]))
			{
				$valcostorotacion = $vars[5][$mes];
				$val = (nvl($vars[4][$mes],0)/nvl($vars[2][$mes],0))*100;
				$lineaIR[]=(number_format($val,2)) ."% | " .number_format($valcostorotacion,2);
				$totlineaIRCen+=$val;
				$totlineaIRcostorotacionCen+=$valcostorotacion;

			}
			else
				$lineaIR[] = "";
			
			if((isset($vars[3][$mes])) || (isset($vars[6][$mes])))
			{
				$val = $vars[3][$mes];
				$val2 = $vars[6][$mes];
				$lineaDP[] = number_format($val,0) ." | ". number_format($val2,2);
				$totDPCen+= $val;
				$totDPCen2+= $val2;
					
			}else
				$lineaDP[] = "";
			
			
			if(isset($vars[3][$mes]))
			{
		/*** SE DEIVIDE EN 26 POR QUE NO TODOS LOS DOMINGOS SE LABORAN ***/
				$val = (nvl($vars[3][$mes],2)/26);
				$lineaIndicadorDiasPedidos[]=(number_format($val,2));
				$totlineaDiaPerdidoCen+=$val;				
			}
			else
				$lineaIndicadorDiasPedidos[] = "";
							
			if(isset($vars[7][$mes]))
			{
				$val = $vars[7][$mes];
				$lineaCostoManoObra[] = number_format($val,0);
				$totIndicadorCostoManoObraCen+= $val;
				$counmeslineaIndicadorCostoManoObra = $counmeslineaIndicadorCostoManoObra+1;
			}else
				$lineaCostoManoObra[] = "";
		
		if(isset($vars[8][$mes]))
			{
				$val = $vars[8][$mes];
				$lineaCostoNominaVentas[] = number_format($val,0);
				$totCostoNominaVentasCen+= $val;
				$counmeslineaCostoNominaVentas = $counmeslineaCostoNominaVentas+1;
			}else
				$lineaCostoNominaVentas[] = "";
		
			if(isset($vars[9][$mes]))
				{
					$val = $vars[9][$mes];
					$lineaIndicadorRMPerCosto[] = number_format($val,0);
					$totIndicadorRMPerCostoCen+= $val;
					$counmeslineaIndicadorRMPerCosto = $counmeslineaIndicadorRMPerCosto+1;
				}else
					$lineaIndicadorRMPerCosto[] = "";
		
			}
			
			/* $lineaCostoManoObra[]="";
			 */
		 /*** VALOR CONSOLIDADO ****/
		 $lineaAccimesprom[] = number_format($totlineaAccimesCen,2);		
		 $kmTotAccimesCen+=$totlineaAccimesCen;
		 
		 $lineaPersMesprom[] = number_format($totlineaPersCen,2);		
		 $kmTotPersMesCen+=$totlineaPersCen;
	 
		 $totlineaIndiceAccidCen=$totlineaIndiceAccidCen/$counmeslineaAccid;
		 $lineaIndiceAccidprom[] = number_format($totlineaIndiceAccidCen,2)."%";			
		 $kmTotIndiceAccidCen+=$totlineaIndiceAccidCen;
				 
		
		 $lineaIRprom[] = number_format($totlineaIRCen,2) ."% | ". number_format($totlineaIRcostorotacionCen,2);			
		 $kmTotIRCen+=$totlineaIRCen;
		 $kmTotIRCen2+=$totlineaIRCen2;
				
		 $lineaDPprom[] = number_format($totDPCen,2) ." | ". number_format($totDPCen2,2);		
		 $kmTotDPCen+=$totDPCen;
		 $kmTotDPCen2+=$totDPCen2;
		 
		 $lineaIndicadorDiasPedidosprom[] = number_format($totlineaDiaPerdidoCen,2) ;		
		 $kmTotDiasPedidosCen+=$totDiasPedidosCen;	
		 $kmTotCostoAusentismoCen+=$totCostoAusentismoCen;
		 
		 $lineaCostoNominaVentasprom[] = number_format($totCostoNominaVentasCen,2);		
		 $kmTotCostoNominaVentasCen+=$totCostoNominaVentasCen;
		 
			 
		 $lineaIndicadorRMPerCostoprom[] = number_format($totIndicadorRMPerCostoCen,2);		
		 $kmTotIndicadorRMPerCostoCen+=$totIndicadorRMPerCostoCen;
	
		 $lineaCostoManoObraprom[] = number_format($totIndicadorCostoManoObraCen,2);		
		 $kmTotIndicadorCostoManoObraCen+=$totIndicadorCostoManoObraCen;
		 /*** HASTA AQUI CONSOLIDADO ***/
		

		/*** VALOR ULTIMO MES ****/		 
		 $lineaAccimesprom[] = number_format(nvl($vars[1][$mes],0));			 
		 $lineaPersMesprom[] = number_format($vars[2][$mes],0);			
		 $lineaIndiceAccidprom[] = number_format(((nvl($vars[1][$mes],0)/nvl($vars[2][$mes],0))*100),2)."%";			
		 $lineaIRprom[] = number_format(((nvl($vars[4][$mes],0)/nvl($vars[2][$mes],0))*100),2)."% | ". number_format($vars[5][$mes],2);
		 $lineaDPprom[] = number_format($vars[3][$mes],2) ." | ". number_format($vars[6][$mes],2);		
		 $lineaIndicadorDiasPedidosprom[] = number_format((nvl($vars[3][$mes],2)/26),2) ;		
		 $lineaCostoManoObraprom[] = number_format(($vars[7][$mes]),2);		
		
		/**FALTA CALUCLAR ESTO **/
		$lineaCostoNominaVentasprom[] = number_format($totCostoNominaVentasCen,2);
		$lineaIndicadorRMPerCostoprom[] = number_format($totIndicadorRMPerCostoCen,2);		
		/**HASTA AQUI **/
		 
		/*** HASTA AQUI ULTIMO MES ***/
		 
	}
	

	
	$lineaAccimes[]  = number_format($kmTotAccimesCen,0);
	$lineaPersMes[]  = number_format($kmTotPersMesCen,0); 
	$lineaIndiceAccid[] = number_format($kmTotIndiceAccidCen/count($centros),2)."%"; 	
	$lineaIR[] = number_format($kmTotIRCen,0)."% | ". number_format($kmTotIRCen2,0);	
	$lineaDP[] = number_format($kmTotDPCen,0)." | ". number_format($kmTotDPCen2,0);	
	$lineaIndicadorDiasPedidos[] = number_format($kmTotDiasPedidosCen,0);
	$lineaCostoNominaVentas[] = number_format($kmTotCostoNominaVentasCen,0);
	$lineaIndicadorRMPerCosto[] = number_format($kmTotIndicadorRMPerCostoCen,0);
	$lineaCostoManoObra[]= number_format($kmTotIndicadorCostoManoObraCen,0);
		
	$lineaAccimesprom[]  = number_format($kmTotAccimesCen,0);
	$lineaPersMesprom[]  = number_format($kmTotPersMesCen,0); 
	$lineaIndiceAccidprom[] = number_format($kmTotIndiceAccidCen/count($centros),2)."%";  	
	$lineaIRprom[] = number_format($kmTotIRCen/count($centros),2)."% | ".number_format($kmTotIRCen2/count($centros),2);
	$lineaDPprom[] =  number_format($kmTotDPCen,0)." | ". number_format($kmTotDPCen2,0);
    $lineaIndicadorDiasPedidosprom[] = number_format($kmTotDiasPedidosCen,0);	
	$lineaCostoNominaVentasprom[] = number_format($kmTotCostoNominaVentasCen,0);
	$lineaIndicadorRMPerCostoprom[] = number_format($kmTotIndicadorRMPerCostoCen,0);
	$lineaCostoManoObraprom[]= number_format($kmTotIndicadorCostoManoObraCen,0);
	
if($html)
{
	if ($tipo_info==1){
	
	
	
	
		imprimirLinea($lineaCostoManoObra,"",$estilos);		
		imprimirLinea($lineaCostoNominaVentas,"",$estilos);
		imprimirLinea($lineaIndicadorRMPerCosto,"",$estilos);		
		
	
	}
	else{
		
	
	
		imprimirLinea($lineaCostoManoObraprom,"",$estilos);
		imprimirLinea($lineaCostoNominaVentasprom,"",$estilos);
		imprimirLinea($lineaIndicadorRMPerCostoprom,"",$estilos);
		
	}
}
else
{
	
	
	

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



	
if($html)
{
	if ($tipo_info==1){
	
		imprimirLinea($lineaAccimes,"",$estilos);
		imprimirLinea($lineaPersMes,"",$estilos);
		imprimirLinea($lineaDP,"",$estilos);
		imprimirLinea($lineaIndicadorDiasPedidos,"",$estilos);
	
	}
	else{
		
		imprimirLinea($lineaAccimesprom,"",$estilos);
		imprimirLinea($lineaPersMesprom,"",$estilos);
		imprimirLinea($lineaDPprom,"",$estilos);
		imprimirLinea($lineaIndicadorDiasPedidosprom,"",$estilos);
				
		
	}
}
else
{
	
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
	
		imprimirLinea($lineaIndiceAccid,"",$estilos);
		imprimirLinea($lineaIR,"",$estilos);
	
	
	}
	else{
		
		imprimirLinea($lineaIndiceAccidprom,"",$estilos);
		imprimirLinea($lineaIRprom,"",$estilos);
	
				
		
	}
}
else
{
	
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaIndiceAccid, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaIR, array(1=>"txt_izq"));
	
	
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
