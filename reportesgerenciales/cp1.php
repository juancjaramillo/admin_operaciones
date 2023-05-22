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
if($tipo_info=="") {
	$tipo_info=1;
}
//$tipo_info=1;
if(isset($_GET["format"])){
	$html=false;
	$inicio = $_GET["inicio"];
	$final = $_GET["final"];
}
$titulo1["inf"]="INDICADORES GERENCIALES : INDICADORES DE COMPRAS";
if($html){
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
		foreach($fechas as $mes){
			$titulosFinanciera[] = $titulosVolumenes[] = $titulosEficiencia[] = ucfirst(strftime("%b.%Y",strtotime($mes."-01")));
			$estilostitulosFinanciera[$i] = " class='azul_osc_14'";
			$estilos[$i] = "class='azul_osc_12'";
			$i++;			
		}
	}else {
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
		foreach($fechas as $mes){
			$titulosFinanciera[] = $titulosVolumenes[] = $titulosEficiencia[] = ucfirst(strftime("%b.%Y",strtotime($mes."-01")));
			$estilostitulosFinanciera[$i] = " class='azul_osc_14'";
			$estilos[$i] = "class='azul_osc_12'";
			$i++;			
		}
	}else {
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
	if($html){
		$titulos[] = "Consolidados Promedios";
		$anchotabla = (120*(count($titulosFinanciera)-2))+430;
		if ($anchotabla<850) $anchotabla=950;
		echo '<table width="'.$anchotabla.'" border=1 bordercolor="#7fa840" align="center">
				<tr>';
		echo '	<th height="40" width="300" class="azul_osc_16">MENSUAL</th>';
		foreach($titulos as $tt){
			if ($tt=="Consolidados Promedios"){
				echo '<th height="40" width="120" class="azul_osc_16">'.$tt.'</th>';
			}else {
				echo '<th height="40" class="azul_osc_16" colspan='.count($fechas).'>'.$tt.'</th>';
			}
		}
		echo '</tr>';
		imprimirLinea($titulosFinanciera, "#b2d2e1", $estilostitulosFinanciera);
	}else{
		titulos_uno_xls($workbook, $worksheet, $fila, $columna, array("MENSUAL"));
		foreach($titulos as $tt){
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
}else {
	$colspan = (count($centros)*2)+2;
	if($html){
		$titulos[] = "Consolidado Total";		
		$anchotabla = (180*(count($titulosFinanciera)-2))+230;		
		if ($anchotabla<800) $anchotabla=800;
		echo '<table width="'.$anchotabla.'" border=1 bordercolor="#7fa840" align="center">';
		echo '<tr><td  height="30" colspan="'.$colspan.'" align="left" bgcolor="#b2d2e1" class="azul_osc_14">'.$titulo1["inf"].'</td></tr>';
		#echo '<tr><td  height="30" colspan="'.$colspan.'" align="left" bgcolor="#b2d2e1" class="azul_osc_14">INDICADORES OPERACIONALES</td></tr>';
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
		
		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array("INDICADORES OPERACIÓN"),array(1=>"azul_izq"), $colspan-1);
		$fila++;$columna=0;

		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosFinanciera, array(1=>"azul_izq"));	
		$fila++;$columna=0;	
	}
}
/********************************************/
	   // HASTA AQUI ENCABEZADOS Y TITULOS //
/********************************************/	
	
	$AhorroxCompania = array("Valor Ahorro");
	$lineaCompTotalVentas = array("Total Ventas / Gastos");
	$lineaTotalPresupuesto = array("Desviación Presupuestal y Real"); //NUEVO
	$lineaROIadquisicion = array("ROI de Adquisiciones"); //NUEVO
	
	$lineaNumSolCompra = array("Número Solicitudes de Compra");	
	$CompetitiveBinding = array("Competitive Binding(P,X,C)");
	$CompetitiveP = array("P");	
	$CompetitiveC = array("C");		
	$CompetitiveX = array("X");		
	$lineaOporEnt = array("Oportunidad en la Entrega");
    $lineaAhorroTotalCoste = array("Ahorro Total de Costes");   //NUEVO 	
	$lineaCicloAdq = array("Tiempo del Ciclo de Adquisición");   //NUEVO 	
	$lineaPorcProv = array("Porcentaje del proveedor (80%)");   //NUEVO 
	$lineaMangCost = array("Managed Cost");   //NUEVO 	
	$lineaCumpago = array("Cumplimiento Formas de Pago");      //NUEVO 		
	
	$lineaAnuCPX = array("Anulados CPX");    			  //NUEVO	
	$lineaPorPart = array("Porcentaje de Participación por Area");    			  //NUEVO
	$lineaPorPartMan = array("Porcentaje de Participación Mantenimiento");    			  //NUEVO
	$lineaPorPartTec = array("Porcentaje de Participación Tecnología");    			  //NUEVO
	$lineaValorAhorroNal = array("Valor Ahorro Nacional");    			  //NUEVO
	$lineaProyectoNal = array("Proyectos Nacionales");    			  //NUEVO





	$AhorroxCompaniaprom = array("Valor Ahorro");
	$lineaCompTotalVentasprom = array("Total Ventas / Gastos");
	$lineaTotalPresupuestoprom = array("Desviación Presupuestal y Real"); //NUEVO
	$lineaROIadquisicionprom = array("ROI de Adquisiciones"); //NUEVO
	
	$lineaNumSolCompraprom = array("Número Solicitudes de Compra");	
	$CompetitiveBindingprom = array("Competitive Binding(P,X,C)");
	$CompetitivePprom = array("P");	
	$CompetitiveCprom = array("C");		
	$CompetitiveXprom = array("X");			
	$lineaOporEntprom = array("Oportunidad en la Entrega");
	$lineaAhorroTotalCosteprom = array("Ahorro Total de Costes");    //NUEVO	
	$lineaCicloAdqprom = array("Tiempo del Ciclo de Adquisición");   //NUEVO 
	$lineaPorcProvprom = array("Porcentaje del proveedor (80%)");   //NUEVO 
	$lineaMangCostprom = array("Managed Cost");      //NUEVO 		
	$lineaCumpagoprom = array("Cumplimiento Formas de Pago");      //NUEVO 		
	
	$lineaAnuCPXprom = array("Anulados CPX");
	$lineaPorPartprom = array("Porcentaje de Participación por Area");    			  //NUEVO	
	$lineaPorPartManprom = array("Porcentaje de Participación Mantenimiento");    			  //NUEVO
	$lineaPorPartTecprom = array("Porcentaje de Participación Tecnología");    			  //NUEVO
	$lineaValorAhorroNalprom = array("Valor Ahorro Nacional");    			  //NUEVO
	$lineaProyectoNalprom = array("Proyectos Nacionales");    			  //NUEVO
	
	
		
	$kmTotAccimesCen = $kmCompTotalVentasCen = $kmTotIndiceAccidCen = $kmTotIRCen = $kmTotDPCen = 0;
	foreach($centros as $idCentro => $da){
		$cosMttoCen = $gastosCen = $numTaCen = $totUsuCen = $totEmpCen = $totFactCen = $totRecauCen = $totRCCen = $totUVDCen = $cosMOCen = $counmeslineaNumSolCompra =  $counmeslineaCompTotalVentas = $counmeslineaRetiros = $counmeslineaAccid = $counmesOporEntprom = $counmesCompetitiveBinding = $totlineaNumSolCompraCen = $totlineaCompTotalVentasCen = $totDPCen = $totRetirosCen = $totAhorroxCompaniaCen =  $totCompetitiveBindingCen = $totOporEntCen = 0;
		$vars =  array();	
		
	foreach($fechas as $mes){			
			$qid = $dbcorporativo->sql_query("SELECT sum(c.valor_ahorro) as valorahorro, 
			sum(c.compras_sobre_total_ventas) as comprastotalventas, 
			sum(c.numero_solicitudes_compra) as numerosolicitudcompra,
			sum(c.competitive_bindingpxc) as bindingpxc,
			sum(c.valorp) as valorp,			
			sum(c.valorc) as valorc,					
			sum(c.valorx) as valorx,
			sum(c.oportunidad_entrega) as oportunidadentrega
			FROM compras_indicadores c 
			WHERE id_centro='".$idCentro."'  and
			fecha = '".$mes."' GROUP BY id_empresa");			 
			 
			while($query = $dbcorporativo->sql_fetchrow($qid)){
				$vars[1][$mes]  = $query["valorahorro"];
				$vars[2][$mes]  = $query["comprastotalventas"];
				$vars[3][$mes] = $query["numerosolicitudcompra"];
				$vars[4][$mes] = $query["bindingpxc"];
				$vars[5][$mes] = $query["valorp"];	
				$vars[6][$mes] = $query["valorc"];						
				$vars[7][$mes] = $query["valorx"];
				$vars[8][$mes] = $query["oportunidadentrega"];	
			}			
			
			if(isset($vars[1][$mes])){
				$val = nvl($vars[1][$mes],2);
				$AhorroxCompania[] = number_format($val,2);
				$totAhorroxCompaniaCen+= $val;
				$counmesAhorroxCompania = $counmesAhorroxCompania+1;
			}else
				$AhorroxCompania[] = "";			
						
			if(isset($vars[2][$mes])){
				$val = $vars[2][$mes];
				$lineaCompTotalVentas[] = number_format($val,2);
				$totlineaCompTotalVentasCen+= $val;
				$counmeslineaCompTotalVentas = $counmeslineaCompTotalVentas+1;
			}else
				$lineaCompTotalVentas[] = "";
			
			
			if(isset($vars[2][$mes])){
				$val = $vars[2][$mes];
				$lineaTotalPresupuesto[] = number_format($val,2);
				$totlineaTotalPresupuestoCen+= $val;
				$counmeslineaTotalPresupuesto = $counmeslineaTotalPresupuesto+1;
			}else
				$lineaTotalPresupuesto[] = "";
			
			
			
			
			if(isset($vars[3][$mes])){
				$val = $vars[3][$mes];
				$lineaNumSolCompra[] = number_format($val,2);
				$totlineaNumSolCompraCen+= $val;
				$counmeslineaNumSolCompra = $counmeslineaNumSolCompra+1;
			}else
				$lineaNumSolCompra[] = "";			
			
			if(isset($vars[4][$mes])){
				$val = $vars[4][$mes];
				$CompetitiveBinding[] = number_format($val,2);
				$totCompetitiveBindingCen+= $val;
				$counmesCompetitiveBinding = $counmesCompetitiveBinding+1;
			}else
				$CompetitiveBinding[] = "";		
		
			if(isset($vars[5][$mes])){
				$val = $vars[5][$mes];			
				$CompetitiveP[]=number_format($val,2);
				$totCompetitivePCen+=$val;
				$counmesCompetitiveP+=$counmesCompetitiveP;
			}else
				$CompetitiveP[] = "";
		
		
			if(isset($vars[6][$mes])){
				$val = $vars[6][$mes];
				$CompetitiveC[]=number_format($val,2);
				$totCompetitiveCCen+=$val;
				$counmesCompetitiveC+=$counmesCompetitiveC;
			}else
				$CompetitiveC[] = "";
			
						
			if(isset($vars[7][$mes])){
				$val = $vars[7][$mes];
				$CompetitiveX[]=(number_format($val,2));
				$totCompetitiveXCen+=$val;
				$counmesCompetitiveX+=$counmesCompetitiveX;		
					
			}else
				$CompetitiveX[] = "";			
			
			if(isset($vars[8][$mes])){
				$val = $vars[8][$mes];
				$lineaOporEnt[]=(number_format($val,2));
				$totlineaOporEntCen+=$val;
				$counmeslineaOporEnt+=$counmeslineaOporEnt;						
			}else
				$lineaOporEnt[] = "";
			
			
				if(isset($vars[8][$mes])){
				$val = $vars[8][$mes];
				$lineaAhorroTotalCoste[]=(number_format($val,2));
				$totllineaAhorroTotalCosteCen+=$val;
				$counmeslineaAhorroTotalCoste+=$counmeslineaAhorroTotalCoste;						
			}else
				$lineaAhorroTotalCoste[] = "";
			
			if(isset($vars[8][$mes])){
				$val = $vars[8][$mes];
				$lineaCicloAdq[]=(number_format($val,2));
				$totllineaCicloAdqCen+=$val;
				$counmeslineaCicloAdq+=$counmeslineaCicloAdq;						
			}else
				$lineaCicloAdq[] = "";
			
			
			if(isset($vars[8][$mes])){
				$val = $vars[8][$mes];
				$lineaPorcProv[]=(number_format($val,2));
				$totllineaPorcProvCen+=$val;
				$counmeslineaPorcProv+=$counmeslineaPorcProv;						
			}else
				$lineaPorcProv[] = "";


			if(isset($vars[8][$mes])){
				$val = $vars[8][$mes];
				$lineaROIadquisicion[] = number_format($val,8);
				$totlineaROIadquisicionCen+= $val;				
				$counmeslineaROIadquisicion = $counmeslineaROIadquisicion;
			}else
				$lineaROIadquisicion[] = "";
			
			
				if(isset($vars[8][$mes])){
				$val = $vars[8][$mes];
				$lineaMangCost[] = number_format($val,8);
				$totlineaMangCostCen+= $val;				
				$counmeslineaMangCost = $counmeslineaMangCost;
			}else
				$lineaMangCost[] = "";
			
			
			if(isset($vars[8][$mes])){
				$val = $vars[8][$mes];
				$lineaCumpago[] = number_format($val,8);
				$totlineaCumpagoCen+= $val;				
				$counmeslineaCumpago = $counmeslineaCumpago;
			}else
				$lineaCumpago[] = "";
			
			
			
			
			
			/* AQUI ----*/
			if(isset($vars[8][$mes])){
				$val = $vars[8][$mes];
				$lineaAnuCPX[] = number_format($val,8);
				$totlineaAnuCPXCen+= $val;				
				$counmeslineaAnuCPX = $counmeslineaAnuCPX;
			}else
				$lineaAnuCPX[] = "";
			
			
			if(isset($vars[8][$mes])){
				$val = $vars[8][$mes];
				$lineaPorPart[] = number_format($val,8);
				$totlineaPorPartCen+= $val;				
				$counmeslineaPorPart = $counmeslineaPorPart;
			}else
				$lineaPorPart[] = "";
			
			
			if(isset($vars[8][$mes])){
				$val = $vars[8][$mes];
				$lineaPorPartMan[] = number_format($val,8);
				$totlineaPorPartManCen+= $val;				
				$counmeslineaPorPartMan = $counmeslineaPorPartMan;
			}else
				$lineaPorPartMan[] = "";
			
			
			if(isset($vars[8][$mes])){
				$val = $vars[8][$mes];
				$lineaPorPartTec[] = number_format($val,8);
				$totlineaPorPartTecCen+= $val;				
				$counmeslineaPorPartTec = $counmeslineaPorPartTec;
			}else
				$lineaPorPartTec[] = "";
			
			
			if(isset($vars[8][$mes])){
				$val = $vars[8][$mes];
				$lineaValorAhorroNal[] = number_format($val,8);
				$totlineaValorAhorroNalCen+= $val;				
				$counmeslineaValorAhorroNal = $counmeslineaValorAhorroNal;
			}else
				$lineaValorAhorroNal[] = "";
			
			if(isset($vars[8][$mes])){
				$val = $vars[8][$mes];
				$lineaProyectoNal[] = number_format($val,8);
				$totlineaProyectoNalCen+= $val;				
				$counmeslineaProyectoNal = $counmeslineaProyectoNal;
			}else
				$lineaProyectoNal[] = "";	
			
	}		

			
		
		
		 /*** VALOR CONSOLIDADO ****/
		 $AhorroxCompaniaprom[] = number_format($totAhorroxCompaniaCen,2);		
		 $kmTotAccimesCen+=$totAhorroxCompaniaCen;
		 
		 $lineaCompTotalVentasprom[] = number_format($totlineaCompTotalVentasCen,2);		
		 $kmCompTotalVentasCen+=$totlineaCompTotalVentasCen;
		 
		 $lineaTotalPresupuestoprom[] = number_format($totlineaTotalPresupuestoCen,2);		
		 $kmCompTotalPresupuestoCen+=$totlineaTotalPresupuestoCen;
		 
		 $lineaNumSolCompraprom[]= number_format($totlineaNumSolCompraCen,2);		
		 $kmNumSolCompraCen+=$totlineaNumSolCompraCen;
		 
		 $CompetitiveBindingprom[]= number_format($totCompetitiveBindingCen,2);		
		 $kmNumCompetitiveBindingCen+=$totCompetitiveBindingCen;		 
		 
		 $CompetitivePprom[]= number_format($totCompetitivePCen,2);		
		 $kmNumCompetitivePCen+=$totCompetitivePCen;
		 
		 $CompetitiveCprom[]= number_format($totCompetitiveCCen,2);		
		 $kmNumCompetitiveCCen+=$totCompetitiveCCen;
		 
		 $CompetitiveXprom[]= number_format($totCompetitiveXCen,2);		
		 $kmNumCompetitiveXCen+=$totCompetitiveXCen;
		 
		 $lineaOporEntprom[]= number_format($totlineaOporEntCen,2);		
		 $kmNumlineaOporEntCen+=$totlineaOporEntCen;	
		 
		 $lineaAhorroTotalCosteprom[]= number_format($totllineaAhorroTotalCosteCen,2);		
		 $kmNumlineaAhorroTotalCosteCen+=$totllineaAhorroTotalCosteCen;	
		 
		 $lineaCicloAdqprom[]= number_format($totllineaCicloAdqCen,2);		
		 $kmNumlineaCicloAdqCen+=$totllineaCicloAdqCen;	
		 
		 $lineaPorcProvprom[]= number_format($totllineaPorcProvCen,2);		
		 $kmNumlineaPorcProvCen+=$totllineaPorcProvCen;	
		 
		 $lineaROIadquisicionprom[]= number_format($totlineaROIadquisicionCen,2);		
		 $kmNumlineaROIadquisicionCen+=$totlineaROIadquisicionCen;	
		 
		 
		 $lineaMangCostprom[]= number_format($totlineaMangCostCen,2);		
		 $kmNumlineaMangCostCen+=$totlineaMangCostCen;	
		 		 
		 $lineaCumpagoprom[]= number_format($totlineaCumpagoCen,2);		
		 $kmNumlineaCumpagoCen+=$totlineaCumpagoCen;

		 $lineaAnuCPXprom[]= number_format($totlineaAnuCPXCen,2);		
		 $kmNumlineaAnuCPXCen+=$totlineaAnuCPXCen;	
		 
		 $lineaPorPartprom[]= number_format($totlineaPorPartCen,2);		
		 $kmNumlineaPorPartCen+=$totlineaPorPartCen;	
		 
		 $lineaPorPartManprom[]= number_format($totlineaPorPartManCen,2);		
		 $kmNumlineaPorPartManCen+=$totlineaPorPartManCen;	
		 
		 $lineaPorPartTecprom[]= number_format($totlineaPorPartTecCen,2);		
		 $kmNumlineaPorPartTecCen+=$totlineaPorPartTecCen;	
		 
		 $lineaValorAhorroNalprom[]= number_format($totlineaValorAhorroNalCen,2);		
		 $kmNumlineaValorAhorroNalCen+=$totlineaValorAhorroNalCen;	

		 $lineaProyectoNalprom[]= number_format($totlineaProyectoNalCen,2);		
		 $kmNumlineaProyectoNalCen+=$totlineaProyectoNalCen;	
			 
		 
	 	
		 /*** HASTA AQUI CONSOLIDADO ***/		

		/*** VALOR ULTIMO MES ****/		 
		 $AhorroxCompaniaprom[] = number_format(nvl($vars[1][$mes],2));			 
		 $lineaCompTotalVentasprom[] = number_format($vars[2][$mes],2);
		 $lineaTotalPresupuestoprom[] = number_format($vars[2][$mes],2);
		 $lineaNumSolCompraprom[] = number_format($vars[3][$mes],2);
		 $CompetitiveBindingprom[] = number_format($vars[4][$mes],2);
		 $CompetitivePprom[] = number_format($vars[5][$mes],2) ;			 
		 $CompetitiveCprom[] = number_format($vars[6][$mes],2);		
		 $CompetitiveXprom[] = number_format($vars[7][$mes],2);	
		 $lineaOporEntprom[] = number_format($vars[8][$mes],2);	
         $lineaAhorroTotalCosteprom[] = number_format($vars[8][$mes],2);
		 $lineaCicloAdqprom[] = number_format($vars[8][$mes],2);
		 $lineaPorcProvprom[] = number_format($vars[8][$mes],2);
		 $lineaROIadquisicionprom[] = number_format($vars[8][$mes],2);
		 $lineaMangCostprom[] = number_format($vars[8][$mes],2);
		 $lineaCumpagoprom[] = number_format($vars[8][$mes],2);		 
		

		$lineaAnuCPXprom[] = number_format($vars[8][$mes],2);
		 $lineaPorPartprom[] = number_format($vars[8][$mes],2);
		 $lineaPorPartManprom[] = number_format($vars[8][$mes],2);
		 $lineaPorPartTecprom[] = number_format($vars[8][$mes],2);
		 $lineaValorAhorroNalprom[] = number_format($vars[8][$mes],2);
		 $lineaProyectoNalprom[] = number_format($vars[8][$mes],2);
		 
		
		
		/*** HASTA AQUI ULTIMO MES ***/
		 
	}	
	$AhorroxCompania[]  = number_format($kmTotAccimesCen,2);
	$lineaCompTotalVentas[]  = number_format($kmCompTotalVentasCen,2);
	$lineaTotalPresupuesto[]  = number_format($kmCompTotalPresupuestoCen,2);	
	$lineaNumSolCompra[]  = number_format($kmNumSolCompraCen,2);	
	$CompetitiveBinding[]  = number_format($kmNumCompetitiveBindingCen,2);	
	$CompetitiveP[] = number_format($kmNumCompetitivePCen,2);	
	$CompetitiveC[] = number_format($kmNumCompetitiveCCen,2);	
	$CompetitiveX[] = number_format($kmNumCompetitiveXCen,2);	
	$lineaOporEnt[] = number_format($kmNumlineaOporEntCen,2);
	$lineaAhorroTotalCoste[] = number_format($kmNumlineaAhorroTotalCosteCen,2);
	$lineaCicloAdq[] = number_format($kmNumlineaCicloAdqCen,2);
	$lineaPorcProv[] = number_format($kmNumlineaPorcProvCen,2);
	$lineaROIadquisicion[] = number_format($kmNumlineaROIadquisicionCen,2);
	$lineaMangCost[] = number_format($kmNumlineaMangCostCen,2);
	$lineaCumpago[] = number_format($kmNumlineaCumpagoCen,2);	
	$lineaAnuCPX[] =  number_format($kmNumlineaAnuCPXCen,2);
	$lineaPorPart[] =  number_format($kmNumlineaPorPartCen,2);
	$lineaPorPartMan[] =  number_format($kmNumlineaPorPartManCen,2);
	$lineaPorPartTec[] =  number_format($kmNumlineaPorPartTecCen,2);
	$lineaValorAhorroNal[] =  number_format($kmNumlineaValorAhorroNalCen,2);
	$lineaProyectoNal[] =  number_format($kmNumlineaProyectoNalCen,2);
	

	$AhorroxCompaniaprom[]  = number_format($kmTotAccimesCen,2);
	$lineaCompTotalVentasprom[]  = number_format($kmCompTotalVentasCen,2);
	$lineaTotalPresupuestoprom[]  = number_format($kmCompTotalPresupuestoCen,2);	
	$lineaNumSolCompraprom[]  = number_format($kmNumSolCompraCen,2);	
	$CompetitiveBindingprom[]  = number_format($kmNumCompetitiveBindingCen,2);	
	$CompetitivePprom[] = number_format($kmNumCompetitivePCen,2);	
	$CompetitiveCprom[] = number_format($kmNumCompetitiveCCen,2);	
	$CompetitiveXprom[] = number_format($kmNumCompetitiveXCen,2);	
	$lineaOporEntprom[] = number_format($kmNumlineaOporEntCen,2);	
	$lineaAhorroTotalCosteprom[] = number_format($kmNumlineaAhorroTotalCosteCen,2);
	$lineaCicloAdqprom[] = number_format($kmNumlineaCicloAdqCen,2);
	$lineaPorcProvprom[] = number_format($kmNumlineaPorcProvCen,2);
	$lineaROIadquisicionprom[] = number_format($kmNumlineaROIadquisicionCen,2);
	$lineaMangCostprom[] = number_format($kmNumlineaMangCostCen,2);
	$lineaCumpagoprom[] = number_format($kmNumlineaCumpagoCen,2);	
	$lineaAnuCPXprom[] =  number_format($kmNumlineaAnuCPXCen,2);
	$lineaPorPartprom[] =  number_format($kmNumlineaPorPartCen,2);
	$lineaPorPartManprom[] =  number_format($kmNumlineaPorPartManCen,2);
	$lineaPorPartTecprom[] =  number_format($kmNumlineaPorPartTecCen,2);
	$lineaValorAhorroNalprom[] =  number_format($kmNumlineaValorAhorroNalCen,2);
	$lineaProyectoNalprom[] =  number_format($kmNumlineaProyectoNalCen,2);
	
if($html){
	if ($tipo_info==1){	
		imprimirLinea($AhorroxCompania,"",$estilos);
		imprimirLinea($lineaCompTotalVentas,"",$estilos);	
		imprimirLinea($lineaTotalPresupuesto,"",$estilos);	
		imprimirLinea($lineaROIadquisicion,"",$estilos);	
		imprimirLinea($lineaMangCost,"",$estilos);	
		imprimirLinea($lineaCumpago,"",$estilos);	
		imprimirLinea($lineaValorAhorroNal,"",$estilos);
		
		
	
	}else{
		imprimirLinea($AhorroxCompaniaprom,"",$estilos);
		imprimirLinea($lineaCompTotalVentasprom,"",$estilos);
		imprimirLinea($lineaTotalPresupuestoprom,"",$estilos);
		imprimirLinea($lineaROIadquisicionprom,"",$estilos);
		imprimirLinea($lineaMangCostprom,"",$estilos);	
		imprimirLinea($lineaCumpagoprom,"",$estilos);			
		imprimirLinea($lineaValorAhorroNalprom,"",$estilos);
	
	}
}else{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $AhorroxCompania, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaCompTotalVentas, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaTotalPresupuesto, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaROIadquisicion, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaMangCost, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaCumpago, array(1=>"txt_izq"));	
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaValorAhorroNal, array(1=>"txt_izq"));


}
			
#########################################

#########################################
#      IMPRIMO TITULOS DE VOLUMENES     #
#########################################
if($html)
	imprimirLinea($titulosVolumenes, "#b2d2e1", $estilostitulosFinanciera);
else{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosVolumenes, array(1=>"azul_izq"));
	$fila++;$columna=0;
}
	
if($html){
	if ($tipo_info==1){		
		imprimirLinea($lineaNumSolCompra,"",$estilos);	
		imprimirLinea($lineaProyectoNal,"",$estilos);
	}
	else{		
		imprimirLinea($lineaNumSolCompraprom,"",$estilos);	
		imprimirLinea($lineaProyectoNalprom,"",$estilos);		
	}
}else{	
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaNumSolCompra, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaProyectoNal, array(1=>"txt_izq"));
	
}

#########################################
#########################################
#        IMPRIMO TITULOS DE EFICIENCIA  #
#########################################
if($html)
	imprimirLinea($titulosEficiencia, "#b2d2e1", $estilostitulosFinanciera);
else{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosEficiencia, array(1=>"azul_izq"));
	$fila++;$columna=0;
}
if($html){
	if ($tipo_info==1){	
		imprimirLinea($CompetitiveBinding,"",$estilos);
		imprimirLinea($lineaAnuCPX,"",$estilos);
		imprimirLinea($CompetitiveP,"",$estilos);
		imprimirLinea($CompetitiveC,"",$estilos);
		imprimirLinea($CompetitiveX,"",$estilos);
		imprimirLinea($lineaOporEnt,"",$estilos);
		imprimirLinea($lineaAhorroTotalCoste,"",$estilos);
		imprimirLinea($lineaCicloAdq,"",$estilos);
		imprimirLinea($lineaPorcProv,"",$estilos);
		imprimirLinea($lineaPorPart,"",$estilos);
		imprimirLinea($lineaPorPartMan,"",$estilos);
		imprimirLinea($lineaPorPartTec,"",$estilos);
		
		
	}else{		
		imprimirLinea($CompetitiveBindingprom,"",$estilos);
		imprimirLinea($lineaAnuCPXprom,"",$estilos);
		imprimirLinea($CompetitivePprom,"",$estilos);
		imprimirLinea($CompetitiveCprom,"",$estilos);
		imprimirLinea($CompetitiveXprom,"",$estilos);
		imprimirLinea($lineaOporEntprom,"",$estilos);
		imprimirLinea($lineaAhorroTotalCosteprom,"",$estilos);
		imprimirLinea($lineaCicloAdqprom,"",$estilos);
		imprimirLinea($lineaPorcProvprom,"",$estilos);
		imprimirLinea($lineaPorPartprom,"",$estilos);
		imprimirLinea($lineaPorPartManprom,"",$estilos);
		imprimirLinea($lineaPorPartTecprom,"",$estilos);
	}
}else{	
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $CompetitiveBinding, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaAnuCPX, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $CompetitiveP, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $CompetitiveC, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $CompetitiveX, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaOporEnt, array(1=>"txt_izq"));	
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaAhorroTotalCoste, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaCicloAdq, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaPorcProv, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaPorPart, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaPorPartMan, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaPorPartTec, array(1=>"txt_izq"));
}
#final
if($html){
	$link = "?format=xls&inicio=".$inicio."&final=".$final;
	echo "</table><br /><br />";
	echo "
	<table width='98%' align='center'>
		<tr>
			<td height='50' valign='bottom' align='right'><input type='button' class='boton_verde' value='Bajar en xls' onclick=\"window.location.href='".$ME.$link."'\"/></td>
		</tr>
	</table>
	";
}else{
	$workbook->close();
	$nombreArchivo=preg_replace("/[^0-9a-z_.]/i","_",$titulo1["inf"])."_".$inicio."_".$final.".xls";
	header("Content-Type: application/x-msexcel; name=\"".$nombreArchivo."\"");
	header("Content-Disposition: inline; filename=\"".$nombreArchivo."\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
}
?>