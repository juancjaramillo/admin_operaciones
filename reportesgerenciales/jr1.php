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
if(isset($_GET["format"])) 
{
	$html=false;
	$inicio = $_GET["inicio"];
	$final = $_GET["final"];
}
$titulo1["inf"]="INDICADORES GERENCIALES : INDICADORES DE JURÍDICA";
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
		if ($anchotabla<850) $anchotabla=950;
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
	
	$lineaCostoRecaudo = array("Costo Recaudo Cartera Jurídca");
	$lineaCarteraPreJur = array("Cartera (Prejuridica)");
	$lineindicador_cartera_ventas = array("% Cartera / Ventas");
	$lineaNumDemLaborales = array("Número Demandas Laborales");
	$lineaNumDemPenales = array("Número Demandas Penales");
	$lineaNumDemAdmin = array("Número Demandas Administrativas");
	$lineaEfecRecaudo = array("Efectividad en el Recaudo de Cartera");
	$lineaEfecConsResuel = array("Efectividad en la Atención de Consultas Resueltas");

	/* 	
	$lineaTotalRecuperacion = array("Total (Recuperable y No recuperable)");
	$lineaIndicadorRecuperacionCartera = array("Tasa Cartera Recuperada / Tasa Cartera Recuperable"); */
	
	
	$lineaCostoRecaudoprom = array("Costo Recaudo Cartera Jurídca");
	$lineaCarteraPreJurprom = array("Cartera (Prejuridica)");
	$lineindicador_carteraprom_ventas = array("% Cartera / Ventas");
	$lineaNumDemLaboralesprom = array("Número Demandas Laborales");
	$lineaNumDemPenalesprom = array("Número Demandas Penales");
	$lineaNumDemAdminprom = array("Número Demandas Administrativas");
	$lineaEfecRecaudoprom = array("Efectividad en el Recaudo de Cartera");
	$lineaEfecConsResuelprom = array("Efectividad en la Atención de Consultas Resueltas");
	
	
	$kmTotAccimesCen = $kmTotPersMesCen = $kmTotIndiceAccidCen = $kmTotIRCen = $kmTotDPCen = 0;
	foreach($centros as $idCentro => $da)
	{
		$cosMttoCen = $gastosCen = $numTaCen = $totUsuCen = $totEmpCen = $totFactCen = $totRecauCen = $totRCCen = $totUVDCen = $cosMOCen = $counmeslineaAccimes = $counmeslineaPersMes = $counmeslineaRetiros = $counmeslineaAccid =  $totlineaAccimesCen = $totlineaPersCen = $totDPCen = $totRetirosCen = $totlineaEfecConsResuelCen = 0;
		$vars =  array();	
		
		foreach($fechas as $mes)
		{
			 
		
			$qid3 = $dbcorporativo->sql_query("SELECT 
			sum(j.costo_recaudo_cartera_juridica) as costorecaudocarterajuridica, 
			sum(j.cartera_prejuridica) as carteraprejuridica, 
			sum(j.porcentaje_cartera_ventas) as porcentajecarteraventas,
			sum(j.numero_demandas_laborales) as numerodemandaslaborales, 
			sum(j.numero_demandas_penales) as numerodemandaspenales,
			sum(j.numero_demandas_administrativas) as numerodemandasadministrativas,
			sum(j.efectividad_recaudo_cartera) as efectividadrecaudocartera,	
			sum(j.efectividad_atencion_consultas_resueltas) as efectividadatencionconsultasresueltas
			FROM juridica_indicadores j 
			WHERE id_centro='".$idCentro."'  and
			fecha = '".$mes."' GROUP BY id_empresa");
					 
			while($query = $dbcorporativo->sql_fetchrow($qid3))
			{
				
				$vars[1][$mes]  = $query["costorecaudocarterajuridica"];
				$vars[1][$mes]  = $query["carteraprejuridica"];
				$vars[3][$mes] = $query["porcentajecarteraventas"];	
				
				$vars[3][$mes] = $query["numerodemandaslaborales"];
				$vars[6][$mes] = $query["numerodemandaspenales"];
				$vars[7][$mes] = $query["numerodemandasadministrativas"];	
				$vars[2][$mes] = $query["efectividadrecaudocartera"];
				$vars[4][$mes] = $query["efectividadatencionconsultasresueltas"];
				
				
							
				
						
				
	
			}
						
			if(isset($vars[1][$mes]))
			{
				$val = nvl($vars[1][$mes],0);
				$lineaCarteraPreJur[] = number_format($val,0);
				$totlineaAccimesCen+= $val;
				$counmeslineaAccimes = $counmeslineaAccimes+1;
			}else
				$lineaCarteraPreJur[] = "";
			
			
			
				if(isset($vars[1][$mes]) && isset($vars[9][$mes]))
			{
				$val = nvl($vars[1][$mes],0)/nvl($vars[9][$mes],0);
				$lineindicador_cartera_ventas[]=number_format($val,2);
				$totlineaindicador_cartera_ventasCen+=$val;
				$counmesindicador_cartera_ventas= $indicador_cartera_ventas+1;
			}
			else
				$lineindicador_cartera_ventas[] = "";
			
			
			
			if(isset($vars[2][$mes]))
			{
				$val = $vars[2][$mes];
				$lineaEfecRecaudo[] = number_format($val,0);
				$totlineaPersCen+= $val;
				$counmeslineaPersMes = $counmeslineaPersMes+1;
			}else
				$lineaEfecRecaudo[] = "";
			
			
			
	
		
			
			
			if(isset($vars[4][$mes]))
			{
				$val = $vars[4][$mes];
				$lineaEfecConsResuel[] = number_format($val,0);
				$totRetirosCen+= $val;
				$counmeslineaRetiros = $counmeslineaRetiros+1;
			}else
				$lineaEfecConsResuel[] = "";
		
	
			if(isset($vars[1][$mes]) && isset($vars[2][$mes]))
			{
				$val = nvl($vars[1][$mes],0)/nvl($vars[2][$mes],0);
				$lineaCostoRecaudo[]=number_format($val,2);
				$totlineaEfecConsResuelCen+=$val;
				$counmeslineaAccid = $counmeslineaAccid+1;
			}
			else
				$lineaCostoRecaudo[] = "";
			
		
			
			
			
				if(isset($vars[3][$mes]) && isset($vars[5][$mes]))
			{
				$val = nvl($vars[3][$mes],0)/nvl($vars[5][$mes],0);
				$lineaNumDemLaborales[]=number_format($val,2);
				$totlineaIndicadorRotacionCostosCen+=$val;
				$counmeslineaIndicadorRotacionCostos = $counmeslineaIndicadorRotacionCostos+1;
			}
			else
				$lineaNumDemLaborales[] = "";
			
			
			if(isset($vars[6][$mes]))
			{
				$val = $vars[6][$mes];
				$lineaNumDemPenales[] = number_format($val,0);
				$totCostoNominaVentasCen+= $val;
				$counmeslineaCostoNominaVentas = $counmeslineaCostoNominaVentas+1;
			}else
				$lineaNumDemPenales[] = "";
			
				if(isset($vars[7][$mes]))
			{
				$val = $vars[7][$mes];
				$lineaNumDemAdmin[] = number_format($val,0);
				$totCostoNominaVentasCen+= $val;
				$counmeslineaCostoNominaVentas = $counmeslineaCostoNominaVentas+1;
			}else
				$lineaNumDemAdmin[] = "";
		
		}
			
	  //   $totlineaAccimesCen=$totlineaAccimesCen;		
		 $lineaCarteraPreJurprom[] = number_format($totlineaAccimesCen,2);		
		 $kmTotAccimesCen+=$totlineaAccimesCen;
		 
		  $totlineaindicador_cartera_ventasCen=$totlineaindicador_cartera_ventasCen/$counmesindicador_cartera_ventas;
		 $lineindicador_cartera_ventasprom[] = number_format($totlineaindicador_cartera_ventasCen,2)."%";			
		 $kmTotindicador_cartera_ventasCen+=$totlineaindicador_cartera_ventasCen;
	
	 //  $totlineaPersCen=$totlineaPersCen;		
		 $lineaEfecRecaudoprom[] = number_format($totlineaPersCen,2);		
		 $kmTotPersMesCen+=$totlineaPersCen;

	 
		 
		 $totlineaEfecConsResuelCen=$totlineaEfecConsResuelCen/$counmeslineaAccid;
		 $lineaEfecConsResuelprom[] = number_format($totlineaEfecConsResuelCen,2)."%";			
		 $kmTotIndiceAccidCen+=$totlineaEfecConsResuelCen;
		 
		 
		
		 
		 /**AQUI**/
		 
		 $lineaCostoRecaudoprom[] = number_format($totCostoNominaVentasCen,2);		
		 $kmTotCostoNominaVentasCen+=$totCostoNominaVentasCen;
		 
	
		$lineaNumDemLaboralesprom[] = number_format($totIndicadorRotacionCostosCen,2);		
		$kmTotIndicadorRotacionCostosCen+=$totIndicadorRotacionCostosCen;
	
		$lineaNumDemPenalesprom[] = number_format($totIndicadorRotacionCostosCen,2);		
		$kmTotIndicadorRotacionCostosCen+=$totIndicadorRotacionCostosCen;
		 
		 $lineaNumDemAdminprom[] = number_format($totIndicadorRotacionCostosCen,2);		
		 $kmTotIndicadorRotacionCostosCen+=$totIndicadorRotacionCostosCen;
		 
		 
		 
		 
		/*** VALOR ULTIMO MES ***/ 	
		$lineaCarteraPreJurprom[] = "";
		$lineindicador_cartera_ventasprom[] = "";
		$lineaEfecRecaudoprom[] = "";
		$lineaEfecConsResuelprom[] = "";
		$lineaCostoRecaudoprom[] = "";
		$lineaNumDemLaboralesprom[] = "";
		$lineaNumDemPenalesprom[] = "";
		$lineaNumDemAdminprom[] = "";
	
		 /*** HASTA AQUI VALOR ULTIMO MES ***/
		 
		 
		 
		 
		 
		 
		 
	}
	
		$lineaCarteraPreJur[]  = number_format($kmTotAccimesCen,0);
		$lineindicador_cartera_ventas[]  = number_format($kmTotPersMesCen,0);
		$lineaEfecRecaudo[]  = number_format($kmTotPersMesCen,0); 
		$lineaEfecConsResuel[] = number_format($kmTotIndiceAccidCen/count($centros),2)."%"; 		
		$lineaCostoRecaudo[] = number_format($kmTotCostoNominaVentasCen,0);
		$lineaNumDemLaborales[] = number_format($kmTotIndicadorRotacionCostosCen,0);
		$lineaNumDemPenales[] = number_format($kmTotIndicadorRotacionCostosCen,0);
		$lineaNumDemAdmin[] = number_format($kmTotIndicadorRotacionCostosCen,0);
		
		$lineaCarteraPreJurprom[]  = number_format($kmTotAccimesCen,0);
		$lineindicador_cartera_ventasprom[]  = number_format($kmTotPersMesCen,0);
		$lineaEfecRecaudoprom[]  = number_format($kmTotPersMesCen,0); 
		$lineaEfecConsResuelprom[] = number_format($kmTotIndiceAccidCen/count($centros),2)."%"; 		
		$lineaCostoRecaudoprom[] = number_format($kmTotCostoNominaVentasCen,0);
		$lineaNumDemLaboralesprom[] = number_format($kmTotIndicadorRotacionCostosCen,0);
		$lineaNumDemPenalesprom[] = number_format($kmTotIndicadorRotacionCostosCen,0);
		$lineaNumDemAdminprom[] = number_format($kmTotIndicadorRotacionCostosCen,0);




#########################################	
#MOSTRAMOS LA INFORMACIÓN DE FINANCIEROS#
#########################################
if($html)
{
	if ($tipo_info==1){
	
	imprimirLinea($lineaCostoRecaudo,"",$estilos);
	
	}
	else{		
		imprimirLinea($lineaCostoRecaudoprom,"",$estilos);
	}
}else{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaCostoRecaudo, array(1=>"txt_izq"));	
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
		imprimirLinea($lineaCarteraPreJur,"",$estilos);
		imprimirLinea($lineindicador_cartera_ventas,"",$estilos);
		imprimirLinea($lineaNumDemLaborales,"",$estilos);
		imprimirLinea($lineaNumDemPenales,"",$estilos);
		imprimirLinea($lineaNumDemAdmin,"",$estilos);
	}
	else{
		imprimirLinea($lineaCarteraPreJurprom,"",$estilos);
		imprimirLinea($lineindicador_cartera_ventasprom,"",$estilos);
		imprimirLinea($lineaNumDemLaboralesprom,"",$estilos);
		imprimirLinea($lineaNumDemPenalesprom,"",$estilos);
		imprimirLinea($lineaNumDemAdminprom,"",$estilos);
	
	}
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaCarteraPreJur, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineindicador_cartera_ventas, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaNumDemLaborales, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaNumDemPenales, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaNumDemAdmin, array(1=>"txt_izq"));

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
		imprimirLinea($lineaEfecRecaudo,"",$estilos);
		imprimirLinea($lineaEfecConsResuel,"",$estilos);
	

	}else {
		imprimirLinea($lineaEfecRecaudoprom,"",$estilos);
		imprimirLinea($lineaEfecConsResuelprom,"",$estilos);	
	
	}
}else{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaEfecRecaudo, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaEfecConsResuel, array(1=>"txt_izq"));
	
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
