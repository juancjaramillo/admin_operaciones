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
if($tipo_info=="") {
	$tipo_info=1;
}

if(isset($_GET["format"])) 
{
	$html=false;
	$inicio = $_GET["inicio"];
	$final = $_GET["final"];
}
$titulo1["inf"]="INDICADORES GERENCIALES : INDICADORES MANTENIMIENTO";
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

$centrosnacionales = $centrosnacional + $centrospromo;
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
$linea = array("Costos Mtto / Ventas Totales");
$lineaCosTon = array("Costos Mtto / Toneladas Recolección");
$lineaprom = array("Costos Mtto / Ventas Totales");
$lineaCosprom  = array("Costos Mtto / Toneladas Recolección");
$tonTOTAL = $cosTonTOTAL = $persTOTAL = $costoLlanTOTAL  = 0;

##############################
#     Toneladas recogidas    #
##############################

consultatons($fila, $columna, $html, implode(",",array_keys($centrosnacionales)), "todo", $inicio, $final, $estilos, "asc", $toneladas);
$lineaTon = array("Toneladas");
$cosTonTOTAL = 0;

####################################
#  Costo mtto Toneladas recogidas  #
####################################
foreach($centros as $idCentro => $da)
{
	$promCos = $numMes1 = $numMes = $totalCentro = 	$totalCentroCos = 0;
	foreach($fechas as $mes)
	{
		$lineaTon[] = number_format(nvl($toneladas[$idCentro][$mes],0),2);
		$totalCentro+=nvl($toneladas[$idCentro][$mes],0);
		#Consultamos el costo del mes
		
		if($idCentro!=15 and $idCentro!=14){			
			$qidCTRP = $dbnacionalproduccion->sql_row("SELECT sum(c.valor) as val FROM costos c LEFT JOIN servicios s ON s.id=c.id_servicio 
			WHERE id_centro='".$idCentro."' AND id_variable_informe = 10 AND fecha = '".$mes."'");
		}else{			
			$qidCTRP = $dbpromo->sql_row("SELECT sum(c.valor) as val FROM costos c LEFT JOIN servicios s ON s.id=c.id_servicio 
			WHERE id_centro='".$idCentro."' AND id_variable_informe = 10 AND fecha = '".$mes."'");	
		}
			
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
	$promCos = $totalCentroCos/$numMes1;
	$lineaCosprom[] = number_format($promCos,0);
	
	$tonTOTAL += $promCen;
	if($promCos!=0) {
		$cosTonTOTAL += $promCos;
		$numCentro++;
	}	
	/*** VALOR ULTIMO MES ***/	
	$lineaCosprom[] = number_format((nvl($qidCTRP["val"],0)/nvl($toneladas[$idCentro][$mes],2)),0);	
	/*** HASTA AQUI VALOR ULTIMO MES ***/
	
}
$lineaCosTon[] = number_format($cosTonTOTAL/$numCentro,0);
$lineaCosprom[] = number_format($cosTonTOTAL/$numCentro,2);


###############################################################################
#CONSULTAMOS LA INFORMACIÓN ENTREGADA POR FINANCIERA EN EL FORMATO ESTABLECIDO#
###############################################################################
$lineaCostoLlantas = array("Costos Llantas / Ventas Totales");
$lineaDisponibilidad = array("Disponibilidad Flota");
$lineaConfiabilidad = array("Confiabilidad Flota"	);
$lineaKilometrosafalla = array("Kilometros a falla");
$lineaGestiondeSolicitudes = array("Eficiencia Gestión de Solicitudes");
$linea7201 = array("Calidad de cumplimiento frecuencia de recolección");
$linea7202 = array("Calidad de cumplimiento horario de recolección"	);
$linea7203 = array("Calidad Técnica en la Recolección");
$lineaGestionPQRS = array("Gestión de PQRS");


$lineaCostoLlantasprom = array("Costos Llantas / Ventas Totales");
$lineaDisponibilidadprom = array("Disponibilidad Flota");
$lineaConfiabilidadprom = array("Confiabilidad Flota"	);
$lineaKilometrosafallaprom = array("Kilometros a falla");
$lineaGestiondeSolicitudesprom = array("Eficiencia Gestión de Solicitudes");
$linea7201prom = array("Calidad de cumplimiento frecuencia de recolección");
$linea7202prom = array("Calidad de cumplimiento horario de recolección"	);
$linea7203prom = array("Calidad Técnica en la Recolección");
$lineaGestionPQRSprom = array("Gestión de PQRS");


#ind financieros
$qidOV = $dbnacionalproduccion->sql_query("SELECT * FROM variables_informes WHERE id >=27 AND id<=36 ORDER BY id");
while($queryOV = $dbnacionalproduccion->sql_fetchrow($qidOV))
{
	$nombreOtrasDos[$queryOV["id"]] = $queryOV["variable"];
}
$cosMtto = $gastos = $numTa = $totEmp = $totFact = $totRecau = $totRC = $totUVD = $cosMO = $totindllanta = $kmTotlineaDisponibilidadCen=0;
$kmTotlineaConfiabilidadCen=0;
foreach($centros as $idCentro => $da)
{
	$cosMttoCen = $gastosCen = $numTaCen = $totUsuCen = $totEmpCen = $totFactCen = $totRecauCen = $totRCCen = $totUVDCen = $cosMOCen = $totindllantacen = $counmeslineaDisponibilidad = $lineaDisponibilidadCen=0;
	$lineaConfiabilidadCen = $counmeslineaConfiabilidad=0;
	$vars =  array();
	$totfec = 0;
	foreach($fechas as $mes)
	{
		
		if($idCentro!=15 and $idCentro!=14){		
			$qid3 = $dbnacionalproduccion->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c 
			LEFT JOIN servicios s ON s.id=c.id_servicio WHERE id_centro='".$idCentro."'  
			AND id_variable_informe >= 9 AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
			
			while($query = $dbnacionalproduccion->sql_fetchrow($qid3)){
				$vars[$query["id_variable_informe"]][$mes] = $query["val"];
			}
		}else{
			$qid3 = $dbpromo->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c 
			LEFT JOIN servicios s ON s.id=c.id_servicio WHERE id_centro='".$idCentro."'  
			AND id_variable_informe >= 9 AND fecha = '".$mes."'  GROUP BY id_variable_informe");
			
			while($query = $dbpromo->sql_fetchrow($qid3)){
				$vars[$query["id_variable_informe"]][$mes] = $query["val"];
			}	
		}
		
		if(isset($vars[10][$mes])  && isset($vars[13][$mes]) && isset($vars[14][$mes]))
			{
				$val=nvl($vars[10][$mes],0)/( nvl($vars[13][$mes],0)+ nvl($vars[14][$mes],0) )*100;
				$linea[] = number_format($val,2).'%';
				$cosMttoCen+=$val;
			}else
				$linea[] = "";
		
		if(isset($vars[39][$mes]))
			{
				$val=nvl($vars[39][$mes],0)/( nvl($vars[13][$mes],0)+ nvl($vars[14][$mes],0) )*100;
				$lineaCostoLlantas[] = number_format($val,2)."%";
				$totindllantacen+= $val;
			}else
				$lineaCostoLlantas[] = "";
			
		

		if(isset($vars[82][$mes]))
		{
			$lineaDisponibilidad[] = number_format($vars[82][$mes],2)."%";
			$lineaDisponibilidadCen+= $vars[82][$mes];
			$counmeslineaDisponibilidad = $counmeslineaDisponibilidad+1;
			
		}else
			$lineaDisponibilidad[] = "";
		
		
		
			if(isset($vars[83][$mes]))
		{
			$lineaConfiabilidad[] = number_format($vars[83][$mes],2)."%";
			$lineaConfiabilidadCen+= $vars[83][$mes];
			$counmeslineaConfiabilidad = $counmeslineaConfiabilidad+1;
			
		}else
			$lineaConfiabilidad[] = "";

	
		
	//	$lineaKilometrosafalla[] = "";
	//	$lineaGestiondeSolicitudes[] = "";
	
		
		if(isset($vars[500][$mes]))
		{
			$val = $vars[500][$mes];
			$linea7201[] = number_format($val,0);
			$linea7202[] = number_format($val,0);
			$linea7203[] = number_format($val,0);
			$lineaGestionPQRS[] = number_format($val,0);
			
			$totEmpCen+= $val;
		}else{
			$linea7201[] = "";
			$linea7202[] = "";
			$linea7203[] = "";;
			$lineaGestionPQRS[] = "";			
		
		}
	}
	
	$cosMttoCen = $cosMttoCen/count($fechas);
	if ($cosMttoCen!=0)
		$lineaprom[] = number_format($cosMttoCen,2)."%";
	$cosMtto+=$cosMttoCen;
	
	$totindllantacen = $totindllantacen/count($fechas);
	$lineaCostoLlantasprom[] = number_format($totindllantacen,2).'%';
	$totindllanta+=$totindllantacen;
		
	 $lineaDisponibilidadCen=$lineaDisponibilidadCen/$counmeslineaDisponibilidad;
	 $lineaDisponibilidadprom[] = number_format($lineaDisponibilidadCen,2)."%";			
	 $kmTotlineaDisponibilidadCen+=$lineaDisponibilidadCen;
		
	 $lineaConfiabilidadCen=$lineaConfiabilidadCen/$counmeslineaConfiabilidad;
	 $lineaConfiabilidadprom[] = number_format($lineaConfiabilidadCen,2)."%";			
	 $kmTotlineaConfiabilidadCen+=$lineaConfiabilidadCen;
	
		/*** VALOR ULTIMO MES ***/
		$lineaprom[]= number_format((nvl($vars[10][$mes],0)/( nvl($vars[13][$mes],0)+ nvl($vars[14][$mes],0) )*100),2).'%';
		$lineaDisponibilidadprom[] = number_format($vars[82][$mes],2)."%";
		$lineaConfiabilidadprom[]  = number_format($vars[83][$mes],2)."%";		
		$lineaCostoLlantasprom[]   = number_format((nvl($vars[39][$mes],0)/( nvl($vars[13][$mes],0)+ nvl($vars[14][$mes],0) )*100),2)."%";			
	    /*** HASTA AQUI VALOR ULTIMO MES ***/
	}
	
	$lineaDisponibilidad[] = number_format($kmTotlineaDisponibilidadCen/count($centros),2)."%"; 
	$lineaConfiabilidad[] = number_format($kmTotlineaConfiabilidadCen/count($centros),2)."%"; 
	
	$lineaDisponibilidadprom[] = number_format($kmTotlineaDisponibilidadCen/count($centros),2)."%"; 
	$lineaConfiabilidadprom[] = number_format($kmTotlineaConfiabilidadCen/count($centros),2)."%"; 
	
	$lineaCostoLlantas[] = number_format($totindllanta/count($centros),2).'%';	
	$lineaCostoLlantasprom[] = number_format($totindllanta/count($centros),2).'%';
	
	$linea[] = number_format($cosMtto/count($centros),2).'%';
	$lineaprom[] = number_format($cosMtto/count($centros),2).'%';
	
#MOSTRAMOS LA INFORMACIÓN DE FINANCIEROS
if($html)
{
	if ($tipo_info==1){	
		imprimirLinea($linea,"", $estilos);
		imprimirLinea($lineaCosTon,"", $estilos);
	}
	else{	
		imprimirLinea($lineaprom,"", $estilos);
		imprimirLinea($lineaCosprom,"", $estilos);
	}
}
else{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaCosTon, array(1=>"txt_izq"));
}

#IMPRIMO TITULOS DE VOLUMENES
if($html)
	imprimirLinea($titulosVolumenes, "#b2d2e1", $estilostitulosFinanciera);
else
{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosEficiencia, array(1=>"azul_izq"));
	$fila++;$columna=0;
}
#MOSTRAMOS LA INFORMACIÓN DE VOLUMENES
if($html)
{
	if ($tipo_info==1){
		imprimirLinea($lineaDisponibilidad,"",$estilos);
		imprimirLinea($lineaConfiabilidad,"",$estilos);	
	}
	else{
		imprimirLinea($lineaDisponibilidadprom,"",$estilos);
		imprimirLinea($lineaConfiabilidadprom,"",$estilos);	
	}
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaDisponibilidad, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaConfiabilidad, array(1=>"txt_izq"));
}

#IMPRIMO TITULOS DE EFICIENCIA
if($html)
	imprimirLinea($titulosEficiencia, "#b2d2e1", $estilostitulosFinanciera);
else
{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosEficiencia, array(1=>"azul_izq"));
	$fila++;$columna=0;
}

#Eficiencia Consumo de combustible Kilometros por Galon

consuKmgl($fila, $columna, $html, implode(",",array_keys($centrosnacionales)),  "rec", $inicio, $final, $estilos, "asc", $kmsgl, $tpvhs);
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

		/*** VALOR ULTIMO MES ***/	
	$lineaprom[] = number_format(nvl($kmsgl[$tpv][$idCentro][$mes],0),2);	
	/*** HASTA AQUI VALOR ULTIMO MES ***/
	}
	$linea[] = number_format(($total/$numCen),2);
	$lineaprom[] = number_format(($total/$numCen),2);

	if($html){
		if ($tipo_info==1){
			imprimirLinea($linea,"",$estilos);
			
			}else {
			imprimirLinea($lineaprom,"",$estilos);
			}
	}
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
}	
unset($tpvhs);

if($html){
		if ($tipo_info==1){
			imprimirLinea($lineaCostoLlantas,"",$estilos);
			}else {
			imprimirLinea($lineaCostoLlantasprom,"",$estilos);
			}
	}
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaCostoLlantas, array(1=>"txt_izq"));
		

########################################
##seccion doonde estan las /*funciones*/
########################################
function consultatons(&$fila, &$columna, $html, $idCentros, $servicio, $inicio, $final, $estilos, $orden, &$toneladas)
{
	/* $idCentro2=15;
	$idCentro="1,2,3,4"; */
	if ($servicio  == 'rec')$idservicio="1,10";	
	if ($servicio  == 'bar')$idservicio="5";		
	if ($servicio  == 'clus')	$idservicio="3,9,17,18";	
	if ($servicio  == 'todo')	$idservicio="1,3,5,9,10,17,18";

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
			

	$qidpesos = $dbnacionalproduccion->sql_query($strQuery);
	while($pesos= $dbnacionalproduccion->sql_fetchrow($qidpesos))
	{
		$toneladas[$pesos["id"]][$pesos["fecha"]]+=$pesos["toneladas"];
		$i++;
	}
	
	$qidpesos2 = $dbpromo->sql_query($strQuery);
	while($pesos= $dbpromo->sql_fetchrow($qidpesos2))
	{
		$toneladas[$pesos["id"]][$pesos["fecha"]]+=$pesos["toneladas"];
		$i++;
	}
}

function consuKmgl(&$fila, &$columna, $html, $idCentros, $servicio, $inicio, $final, $estilos, $orden, &$kmsgl, &$tpvhs)
{
	if ($servicio  == 'rec')$idservicio="1,10";	
	if ($servicio  == 'bar')$idservicio="5";		
	if ($servicio  == 'clus')	$idservicio="3,9,17,18";	
	if ($servicio  == 'todo')	$idservicio="1,3,5,9,10,17,18";
	
	
	global $db, $dbpromo,$dbnacionalproduccion, $CFG, $ME;
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
				WHERE tvh.id in ($tpvh) AND inicio::date >= '$inicio' AND inicio::date<='$final' and c.id in ($idCentros)
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
			
		$tpvh= '';
		
		$qidkmsgls = $dbnacionalproduccion->sql_query($sqlkmsgls);		
 		while($kmgls = $dbnacionalproduccion->sql_fetchrow($qidkmsgls))
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
		
		$qidkmsgls2 = $dbpromo->sql_query($sqlkmsgls);
		while($kmgls = $dbpromo->sql_fetchrow($qidkmsgls2))
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
