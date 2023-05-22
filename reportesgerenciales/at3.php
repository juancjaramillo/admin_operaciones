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

//$titulo1 = $db->sql_row("SELECT upper(nombre||' : '||informe) as inf FROM informes i LEFT JOIN categorias_informes c ON c.id=i.id_categoria_informe WHERE i.id=".str_replace(".php","",simple_me($ME)));
$titulo1["inf"]="INDICADORES GERENCIALES : INDICADORES CLUS";
if($html)
{
	include($CFG->dirroot."/templates/header_popup.php");
	//include($CFG->dirroot."/info/templates/fechas_form_46.php");
	//tablita_titulos($titulo1["inf"],$inicio." / ".$final);
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
			$titulosFinanciera[] = $titulosVolumenes[] = $titulosEficiencia[] = $titulosPersonal[] = ucfirst(strftime("%b.%Y",strtotime($mes."-01")));
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
			$titulosFinanciera[] = $titulosVolumenes[] = $titulosEficiencia[] = $titulosPersonal[] = ucfirst(strftime("%b.%Y",strtotime($mes."-01")));
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
	/* 	echo '<tr><td  height="30" colspan="'.$colspan.'" align="left" bgcolor="#b2d2e1" class="azul_osc_14">INDICADORES OPERACIÓN</td></tr>';
		echo '<tr><td  height="30" colspan="'.$colspan.'" align="left" bgcolor="#b2d2e1" class="azul_osc_14">INDICADORES CLUS</td></tr>'; */
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
$lineaCosTonMtto = array("Costos Mtto / Toneladas Recolección");
$lineaCosprom = array("Costo/Toneladas Recolección");
$lineaPers = array("Toneladas recogidas por  tripulacion");
$lineaLl = array("Costo de llantas por km recorrido ($/Km)");
$lineaLlprom = array("Costo de llantas por km recorrido ($/Km)");

$lineaTonEscDom  = array("Toneladas Escombros Domiciliarios");
$lineaTonEscClan = array("Toneladas Escombros Clandestinos");
$lineaPuntosCriticos = array("Puntos Criticos");
$lineaCorteCesped 	 = array("Metros cuadrados Corte Cesped");
$lineaArbolPoda 	 = array("Número de Arboles Podados");
$lineaMetroLava	 	 = array("Metros Cuadrados Lavado");
$lineaMetroCuadradoHom	  = array("Metros Cuadrados X Hombre");
$lineaMetroCuadradoMaq	  = array("Metros Cuadrados X Máquina");
$lineaNumArbolHom	      = array("Número Arboles X Hombre");
$lineaKmLimpiaPlayas	  = array("Kilometros de Limpieza de Playas");
$TonBascula				  = array("Toneladas Báscula Recolección"); 
$GAPTon					  = array("GAP Toneladas Operación");

$lineaTonEscDomprom  = array("Toneladas Escombros Domiciliarios");
$lineaTonEscClanprom = array("Toneladas Escombros Clandestinos");
$lineaPuntosCriticosprom = array("Puntos Criticos");
$lineaCorteCespedprom 	 = array("Metros cuadrados Corte Cesped");
$lineaArbolPodaprom 	 = array("Número de Arboles Podados");
$lineaMetroLavaprom	 	 = array("Metros Cuadrados Lavado");
$lineaMetroCuadradoHomprom	  = array("Metros Cuadrados X Hombre");
$lineaMetroCuadradoMaqprom	  = array("Metros Cuadrados X Máquina");
$lineaNumArbolHomprom	      = array("Número Arboles X Hombre");
$lineaKmLimpiaPlayasprom	  = array("Kilometros de Limpieza de Playas");
$TonBasculaprom				  = array("Toneladas Báscula Recolección");
$GAPTonprom					  = array("GAP Toneladas Operación");

$tonTOTAL = $cosTonTOTAL = $persTOTAL = $costoLlanTOTAL = $lineaInoperaTOTAL = 0;

##############################
#     Toneladas recogidas    #
##############################

//print_r(array_keys($centros))."<br>";
//print_r(array_keys($centros-1));

/* print_r($centros)."<br><br><br>";*/
/* unset($centros['15']);
print_r($centros)."<br><br><br>";  */
//print_r($centros2);

consultatons($fila, $columna, $html, implode(",",array_keys($centrosnacionales)), "clus", $inicio, $final, $estilos, "asc", $toneladas);
$lineaTon  = array("Toneladas");
$cosTonTOTAL = $kmTotCorteCespedCen = $kmTotArbolPodaCen = $kmTotMetroLavaCen = $kmTotTonBascula = $kmTotGAPTon   = $kmTotPuntosCriticosCen = $kmTotKmLimpiaPlayasCen = 0;
	
##############################
# Costo Toneladas recogidas  #
##############################
foreach($centros as $idCentro => $da)
{
	$promCos = $numMes1 = $numMes = $totalCentro = $counmeslineaPuntosCriticos = $counmesTonGAPTon = $counmesTonBascula = $cosGAPTonCen = $lineaPuntosCriticosCen = 	$lineaKmLimpiaPlayasCen = $lineaTonBasculaCen = $totalCentroCos = $lineaCorteCespedCen = $counmeslineaCorteCesped = $lineaArbolPodaCen = $counmeslineaArbolPoda = $counmeslineaKmLimpiaPlayas = $lineaMetroLavaCen = $counmeslineaMetroLava = 0;
	$vars =  array();
	foreach($fechas as $mes)
	{
		$lineaTon[] = number_format(nvl($toneladas[$idCentro][$mes],0),2);
		$totalCentro+=nvl($toneladas[$idCentro][$mes],0);
		
	/* 	$lineaTonEscDom[]  = "";
		$lineaTonEscClan[] = "";		
		$lineaMetroCuadradoHom[] = "";
		$lineaMetroCuadradoMaq[] = "";
		$lineaNumArbolHom[] = */ "";
		
		
		#Consultamos el costo del mes
	if($idCentro!=15 and $idCentro!=14){				
		$qidCTRP = $dbnacionalproduccion->sql_row("SELECT sum(c.valor) as val FROM costos c LEFT JOIN servicios s ON s.id=c.id_servicio 
		WHERE id_centro='".$idCentro."'  AND id_variable_informe = 40 AND fecha = '".$mes."'");
		}else{	
		$qidCTRP = $dbpromo->sql_row("SELECT sum(c.valor) as val FROM costos c LEFT JOIN servicios s ON s.id=c.id_servicio 
		WHERE id_centro='".$idCentro."'  AND id_variable_informe = 40 AND fecha = '".$mes."'");
		}		
			
	if($idCentro!=15 and $idCentro!=14){			
		
			$qid3 = $dbnacionalproduccion->sql_query("SELECT sum(c.valor) as indicador, id_variable_informe FROM costos c 
			WHERE id_centro='".$idCentro."' AND id_variable_informe >= 68 AND id_variable_informe <= 199 AND fecha = '".$mes."' GROUP BY id_variable_informe");
			
			while($query = $dbnacionalproduccion->sql_fetchrow($qid3)){
			$vars[$query["id_variable_informe"]][$mes] = $query["indicador"];
			}
		
		}else{	
		$qid3 = $dbpromo->sql_query("SELECT sum(c.valor) as indicador, id_variable_informe FROM costos c 
			 WHERE id_centro='".$idCentro."' AND id_variable_informe >= 68 AND id_variable_informe <= 199 AND fecha = '".$mes."' GROUP BY id_variable_informe");
			
			while($query = $dbpromo->sql_fetchrow($qid3)){
			$vars[$query["id_variable_informe"]][$mes] = $query["indicador"];
			}
		}
		
		@$costo=nvl($qidCTRP["val"],0)/nvl($toneladas[$idCentro][$mes],0);		
		
		$lineaCosTon[] = number_format($costo,0);
		
		if ($costo!=0) {
			$totalCentroCos+=nvl($costo,0);
			$numMes1++;
		}
		if(isset($toneladas[$idCentro][$mes]))
			$numMes++;



		
		if(isset($vars[68][$mes])){
				$lineaCorteCesped[] = number_format($vars[68][$mes],2);
				$lineaCorteCespedCen+= $vars[68][$mes];
				$counmeslineaCorteCesped = $counmeslineaCorteCesped+1;				
			}else
				$lineaCorteCesped[] = "";
				
				
		if(isset($vars[69][$mes])){
				$lineaArbolPoda[] = number_format($vars[69][$mes],2);
				$lineaArbolPodaCen+= $vars[69][$mes];
				$counmeslineaArbolPoda = $counmeslineaArbolPoda+1;				
			}else
				$lineaArbolPoda[] = "";
		
		
		if(isset($vars[70][$mes])){
				$lineaMetroLava[] = number_format($vars[70][$mes],2);
				$lineaMetroLavaCen+= $vars[70][$mes];
				$counmeslineaMetroLava = $counmeslineaMetroLava+1;				
			}else
				$lineaMetroLava[] = "";
			
			
			if(isset($vars[77][$mes])){
				$lineaKmLimpiaPlayas[] = number_format($vars[77][$mes],2);
				$lineaKmLimpiaPlayasCen+= $vars[77][$mes];
				$counmeslineaKmLimpiaPlayas = $counmeslineaKmLimpiaPlayas+1;				
			}else
				$lineaKmLimpiaPlayas[] = "";
			
				/*** TONELADAS BASCULA ***/
		if(isset($vars[199][$mes])){
			$TonBascula[] = number_format($vars[199][$mes],2);
			$lineaTonBasculaCen+= $vars[199][$mes];
			$counmesTonBascula = $counmesTonBascula+1;
		}else
			$TonBascula[] = "";
		
		
		
		/*** GAB TONELADAS ***/
		if(isset($vars[199][$mes])){
			$valGAPTon = nvl($vars[199][$mes],2)- nvl($toneladas[$idCentro][$mes],2);
			$GAPTon[]=number_format($valGAPTon,2);
			$cosGAPTonCen+=$valGAPTon;
			$counmesTonGAPTon = $counmesTonGAPTon+1;
		}
		else
			$GAPTon[] = "";
			
			
				if(isset($vars[84][$mes])){
				$lineaPuntosCriticos[] = number_format($vars[84][$mes],2);
				$lineaPuntosCriticosCen+= $vars[84][$mes];
				$counmeslineaPuntosCriticos = $counmeslineaPuntosCriticos+1;				
			}else
				$lineaPuntosCriticos[] = "";
			
			
			
			
			
			
			
			
		
	}
	
	
	
		$lineaTonBasculaCen = $lineaTonBasculaCen/$counmesTonBascula;
		$TonBasculaprom[] = number_format($lineaTonBasculaCen,2);
		$kmTotTonBascula+=$lineaTonBasculaCen;
		
		$cosGAPTonCen = $cosGAPTonCen/$counmesTonGAPTon;
		$GAPTonprom[] = number_format($cosGAPTonCen,2);
		$kmTotGAPTon+=$cosGAPTonCen;
		
	
	 $lineaCorteCespedCen=$lineaCorteCespedCen/$counmeslineaCorteCesped;
	 $lineaCorteCespedprom[] = number_format($lineaCorteCespedCen,2);			
	 $kmTotCorteCespedCen+=$lineaCorteCespedCen;	 
	 
	 $lineaArbolPodaCen=$lineaArbolPodaCen/$counmeslineaArbolPoda;
	 $lineaArbolPodaprom[] = number_format($lineaArbolPodaCen,2);			
	 $kmTotArbolPodaCen+=$lineaArbolPodaCen;
	 
	 $lineaMetroLavaCen=$lineaMetroLavaCen/$counmeslineaMetroLava;
	 $lineaMetroLavaprom[] = number_format($lineaMetroLavaCen,2);			
	 $kmTotMetroLavaCen+=$lineaMetroLavaCen;
	 
	
	  $lineaPuntosCriticosCen=$lineaPuntosCriticosCen/$counmeslineaPuntosCriticos;
	 $lineaPuntosCriticosprom[] = number_format($lineaPuntosCriticosCen,2);			
	 $kmTotPuntosCriticosCen+=$lineaPuntosCriticosCen;
	 
	 
	 $lineaKmLimpiaPlayasCen=$lineaKmLimpiaPlayasCen/$counmeslineaKmLimpiaPlayas;
	 $lineaKmLimpiaPlayasprom[] = number_format($lineaKmLimpiaPlayasCen,2);			
	 $kmTotKmLimpiaPlayasCen+=$lineaKmLimpiaPlayasCen;
	
	
		
	$promCen = $totalCentro/$numMes;
	$lineaTonprom[] = number_format($promCen,2);
	
	$promCos = $totalCentroCos/$numMes1;
	$lineaCosprom[] = number_format($promCos,0);
	
	$tonTOTAL += $promCen;
	if($promCos!=0) {
		$cosTonTOTAL += $promCos;
		$numCentro++;
		
		
		
	}
	
	/* $lineaTonEscDomprom[]  = "";
	$lineaTonEscClanprom[] = "";
	//$lineaPuntosCriticosprom[] = "";
	$lineaMetroCuadradoHomprom[] = "";
	$lineaMetroCuadradoMaqprom[] = "";
	$lineaNumArbolHomprom[] = ""; */
	

	/*** VALOR ULTIMO MES ***/
	
	$TonBasculaprom[]  = number_format($vars[199][$mes],2);
	$GAPTonprom[]= number_format((nvl($vars[199][$mes],0)- nvl($toneladas[$idCentro][$mes],0)),2);	
	
	$lineaTonprom[] = number_format((nvl($toneladas[$idCentro][$mes],0)),2);	
	$lineaCosprom[] = number_format((nvl($qidCTRP["val"],0)/nvl($toneladas[$idCentro][$mes],2)),0);
	
	$lineaMetroLavaprom[]   = number_format(($vars[70][$mes]),2);			
	$lineaArbolPodaprom[]   = number_format(($vars[69][$mes]),2);	
	$lineaCorteCespedprom[] = number_format(($vars[68][$mes]),2);
	$lineaKmLimpiaPlayasprom[] = number_format(($vars[77][$mes]),2);	
	
	$lineaPuntosCriticosprom[] = number_format($vars[84][$mes],2);
	
/* 	$lineaTonEscDomprom[]  = "";
	$lineaTonEscClanprom[] = "";
	$lineaMetroCuadradoHomprom[] = "";
	$lineaMetroCuadradoMaqprom[] = "";
	$lineaNumArbolHomprom[] = ""; */

	/*** HASTA AQUI VALOR ULTIMO MES ***/

	
}




/*** MODIFICA VALORES Y REALIZAR LOS CALCULOS REALES ***/
/* $lineaTonEscDom[] = "";
$lineaTonEscClan[] ="";

$lineaMetroCuadradoHom[] = "";
$lineaMetroCuadradoMaq[] = "";
$lineaNumArbolHom[] = ""; */
/*** HASTA AQUI MODIFICA VALORES Y REALIZAR LOS CALCULOS REALES ***/

$lineaTon[] = number_format($tonTOTAL,2);
$TonBascula[] = number_format($kmTotTonBascula/count($centros),2);
$GAPTon[] = number_format($kmTotGAPTon/count($centros),2);
$lineaCosTon[]  = number_format($cosTonTOTAL/$numCentro,0);
//$lineaLl[]      = number_format($costoLlanTOTAL/count($centros),2);
$lineaPuntosCriticos[] = number_format($kmTotPuntosCriticosCen,2);
$lineaCorteCesped[]      = number_format($kmTotCorteCespedCen,2);
$lineaArbolPoda[]        = number_format($kmTotArbolPodaCen,2);
$lineaMetroLava[]        = number_format($kmTotMetroLavaCen,2);
$lineaKmLimpiaPlayas[]   = number_format($kmTotKmLimpiaPlayasCen,2);


$lineaTonprom[] = number_format($tonTOTAL,2);
$GAPTonprom[] = number_format($kmTotGAPTon/count($centros),2);
$lineaCosprom[] = number_format($cosTonTOTAL/$numCentro,2);
$lineaCorteCespedprom[] 	 = number_format($kmTotCorteCespedCen,2);
$lineaArbolPodaprom[]  		 = number_format($kmTotArbolPodaCen,2);
$lineaMetroLavaprom[]  		 = number_format($kmTotMetroLavaCen,2);
$lineaKmLimpiaPlayasprom[]   = number_format($kmTotKmLimpiaPlayasCen,2);
//$lineaLlprom[]      = number_format($costoLlanTOTAL/count($centros),2);
$lineaPuntosCriticosprom[]  = number_format($kmTotPuntosCriticosCen,2);
$TonBasculaprom[] = number_format($kmTotTonBascula/count($centros),2);

/* $lineaTonEscDomprom[]  = "";
$lineaTonEscClanprom[] = "";
$lineaMetroCuadradoHomprom[] = "";
$lineaMetroCuadradoMaqprom[] = "";
$lineaNumArbolHomprom[] = ""; */

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
		
		
		if($idCentro!=15 and $idCentro!=14){	
		$qidCTRP = $dbnacionalproduccion->sql_row("SELECT sum(c.valor) as val FROM costos c LEFT JOIN servicios s ON s.id=c.id_servicio 
		WHERE id_centro='".$idCentro."' AND esquema='rec' AND id_variable_informe = 10 AND fecha = '".$mes."'");
		}else{	
			$qidCTRP = $dbpromo->sql_row("SELECT sum(c.valor) as val FROM costos c LEFT JOIN servicios s ON s.id=c.id_servicio 
		WHERE id_centro='".$idCentro."' AND esquema='rec' AND id_variable_informe = 10 AND fecha = '".$mes."'");
		}
		
		
		
		
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


#Calcula cuantas toneladas recoje un vehiculo en el mes separando los turnos para sacar le promedio que sería igual al numero de tripulaciones
# y tambien hace el calculo de cuantos viajes por turno se realizan para saber el promedio de viajes por turno por día.


consuviajtontrip($fila, $columna, $html, implode(",",array_keys($centrosnacionales)),"rec", $inicio, $final, $estilos, $orden, $tontrip, $viajtur);
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
	
	
	/*** VALOR ULTIMO MES ***/
	
	$lineaTontripprom[]   = number_format(($tontrip[$idCentro][$mes]),2);
	$lineaviajesturprom[] = number_format(($viajtur[$idCentro][$mes]),2);
	
	/*** HASTA AQUI VALOR ULTIMO MES ***/
	

}
$lineaTontrip[] = number_format($PromTOTAL/count($centros),2);
$lineaviajestur[] = number_format($PromTOTAL1/count($centros),2);
$lineaTontripprom[] = number_format($PromTOTAL/count($centros),2);
$lineaviajesturprom[] = number_format($PromTOTAL1/count($centros),2);


/* $lineakmsbar = array("Km barridos"); */
/* $linea2costobar = array("Costo / Km Barrido"); */
/* $linea3kmbarope = array("Km barridos por operario"); */
/* $linea4bolsasope= array("Bolsas por Operario de Barrido"); */
/* $lineapromkmsbar = array("Km barridos"); */
/* $linea2promcostobar = array("Costo / Km Barrido"); */
/* $linea3promkmbarope = array("Km barridos por operario"); */
/* $linea4promkmsbar = array("Bolsas por Operario de Barrido"); */

/* $tiposVehiculos = $tiposVehiculosXCentro = $capacidad = $recorrido = $combustible = $tonTipo = $viajes = $ton = $per = array();
$kmTot = $cosKmTOTAL = $kmOpe = $bolOpe = 0;
$idVars = array(2,3,4,5,6); */
/* foreach($centros as $idCentro => $da)
{
	$vars = array();
	$kmTotCen = $cosKmTOTALCen = $kmOpeCen = $bolOpeCen = $counmes = 0;
	foreach($fechas as $mes)
	{ */
		
		/* if($idCentro!=15){
			$qid3 = $dbnacionalproduccion->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c LEFT JOIN servicios s ON s.id=c.id_servicio WHERE id_centro='".$idCentro."' AND esquema='bar' AND id_variable_informe IN (".implode(",",$idVars).") AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
			while($query = $dbnacionalproduccion->sql_fetchrow($qid3))
			{
				$vars[$query["id_variable_informe"]][$mes] = $query["val"];
			}
		}else{
			$qid3 = $dbpromo->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c LEFT JOIN servicios s ON s.id=c.id_servicio WHERE id_centro='".$idCentro."' AND esquema='bar' AND id_variable_informe IN (".implode(",",$idVars).") AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
			while($query = $dbpromo->sql_fetchrow($qid3))
			{
				$vars[$query["id_variable_informe"]][$mes] = $query["val"];
			}			
		} */
		/*if(isset($vars[2][$mes]))
		{
			$lineakmsbar[] = number_format($vars[2][$mes],2);
			$kmTotCen+= $vars[2][$mes];
			$counmes = $counmes+1;
		}else
			$lineakmsbar[] = "";*/

		/* if(isset($vars[2][$mes]) && isset($vars[3][$mes]))
		{
			$val = nvl($vars[3][$mes],0)/nvl($vars[2][$mes],0);
			$linea2costobar[]=number_format($val,2);
			$cosKmTOTALCen+=$val;
		}
		else
			$linea2costobar[] = ""; */

		/* if(isset($vars[4][$mes]))
		{
			$val = nvl($vars[2][$mes],0)/nvl($vars[6][$mes],1);
			$linea3kmbarope[] = number_format($val,2);
			$kmOpeCen+= $val;
		}else
			$linea3kmbarope[] = "";
 */
		/* if(isset($vars[5][$mes]))
		{
			$linea4bolsasope[] = number_format($vars[5][$mes],2);
			$bolOpeCen+= $vars[5][$mes];
		}else
			$linea4bolsasope[] = ""; */
	/*}*/
	/* $kmTotCen = $kmTotCen/$counmes;
	$lineapromkmsbar[] = number_format($kmTotCen,2);
	$kmTot+=$kmTotCen; */
	
	/* $cosKmTOTALCen = $cosKmTOTALCen/count($fechas);
	$linea2promcostobar[] = number_format($cosKmTOTALCen,0);
	$cosKmTOTAL+=$cosKmTOTALCen; */

	/* $kmOpeCen = $kmOpeCen/count($fechas);
	$linea3promkmbarope[] = number_format($kmOpeCen,2);
	$kmOpe+=$kmOpeCen; */

	/* $bolOpeCen = $bolOpeCen/count($fechas);
	$linea4promkmsbar[] = number_format($bolOpeCen,2);
	$bolOpe+=$bolOpeCen; */
/*}*/

/* $lineakmsbar[] = number_format($kmTot/count($centros),2); */
/* $linea2costobar[] = number_format($cosKmTOTAL/count($centros),0); */
/* $linea3kmbarope[] = number_format($kmOpe/count($centros),2); */
/* $linea4bolsasope[] = number_format($bolOpe/count($centros),2); */
/* $lineapromkmsbar[] = number_format($kmTot,2); */
/* $linea2promcostobar[] = number_format($cosKmTOTAL/count($centros),0); */
/* $linea3promkmbarope[] = number_format($kmOpe/count($centros),2); */
/* $linea4promkmsbar[] = number_format($bolOpe/count($centros),2); */

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
/* $linea7201 = array("Calidad de cumplimiento frecuencia de recolección"); */
/* $linea7202 = array("Calidad de cumplimiento horario de recolección"	); */
/* $linea7203 = array("Calidad Técnica en la Recolección"); */
//$lineaGestionPQRS = array("Gestión de PQRS");
//$lineaRotacion = array("Rotación de Personal");
//$lineaAusentismo = array("Ausentismo de Personal");

$lineaCosllaventprom = array("Costos Llantas / Ventas Totales");
$lineamttoventasprom = array("Costos Mtto / Ventas Totales");
//$lineaPresupuestoprom = array("Ejecución Presupuesto");
$lineaDisponibilidadprom = array("Disponibilidad Flota");
$lineaConfiabilidadprom = array("Confiabilidad Flota"	);
$lineaKilometrosafallaprom = array("Kilometros a falla");
$lineaGestiondeSolicitudesprom = array("Gestión de Solicitudes");
/* $linea7201prom = array("Calidad de cumplimiento frecuencia de recolección"); */
/* $linea7202prom = array("Calidad de cumplimiento horario de recolección"	); */
/* $linea7203prom = array("Calidad Técnica en la Recolección"); */
//$lineaGestionPQRSprom = array("Gestión de PQRS");
//$lineaRotacionprom = array("Rotación de Personal");
//$lineaAusentismoprom = array("Ausentismo de Personal");


#ind financieros
$qidOV = $dbnacionalproduccion->sql_query("SELECT * FROM variables_informes WHERE id <9 ORDER BY id");
while($queryOV = $dbnacionalproduccion->sql_fetchrow($qidOV))
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
		
		
		
		if($idCentro!=15 and $idCentro!=14){	
				$qid3 = $dbnacionalproduccion->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c 
				LEFT JOIN servicios s ON s.id=c.id_servicio WHERE id_centro='".$idCentro."'  
				AND id_variable_informe >= 9 AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
				
				while($query = $dbnacionalproduccion->sql_fetchrow($qid3))
				{
					$vars[$query["id_variable_informe"]][$mes] = $query["val"];
				}
		}else{
				$qid3 = $dbpromo->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c 
				LEFT JOIN servicios s ON s.id=c.id_servicio WHERE id_centro='".$idCentro."'  
				AND id_variable_informe >= 9 AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
				
				while($query = $dbpromo->sql_fetchrow($qid3))
				{
					$vars[$query["id_variable_informe"]][$mes] = $query["val"];
				}	
		}

	/* 	if(isset($vars[40][$mes]))
		{
			$lineaPresupuesto[] = number_format($val,0);
		}else{
			$lineaPresupuesto[] = "";
			} */
			//print_r($vars[46][$mes]);
			//print_r($vars[45][$mes]);
		$valpresupuesto = $vars[46][$mes]/$vars[45][$mes];
		//echo	$valpresupuesto."<br>" ;
		
		if(isset($vars[46][$mes]))
		{
			//$lineaPresupuesto[] = number_format($valpresupuesto,0);
			$lineaPresupuesto[] = number_format(($vars[46][$mes]/$vars[45][$mes]),2);
			}else{
				$lineaPresupuesto[] = "";	
		}	
			
		if(isset($vars[500][$mes]))
		{
			$val = $vars[500][$mes];
			/* $linea7201[] = number_format($val,0); */
			/* $linea7202[] = number_format($val,0); */
			/* $linea7203[] = number_format($val,0); */
		//	$lineaGestionPQRS[] = number_format($val,0);
			$lineaDisponibilidad[] = number_format($val,0);
			$lineaConfiabilidad[] = number_format($val,0);
			$lineaKilometrosafalla[] = number_format($val,0);
			$lineaGestiondeSolicitudes[] = number_format($val,0);
			/* $lineaPresupuesto[] = number_format($valpresupuesto,0); */
		//	$lineaRotacion[] = number_format($val,0);
		//	$lineaAusentismo[] = number_format($val,0);
			$totEmpCen+= $val;
		}else{
			/*$linea7201[] = ""; */
			/* $linea7202[] = ""; */
			/* $linea7203[] = ""; */
			//$lineaGestionPQRS[] = "";
			$lineaDisponibilidad[] = "";
			$lineaConfiabilidad[] = "";
			$lineaKilometrosafalla[] = "";
			$lineaGestiondeSolicitudes[] = "";
			/* $lineaPresupuesto[] = "";	 */		
		//	$lineaRotacion[] = "";
		//	$lineaAusentismo[] = "";
		}
	}
	
}

#########################################	
#MOSTRAMOS LA INFORMACIÓN DE FINANCIEROS#
if($html)
{
	if ($tipo_info==1){
	//	imprimirLinea($lineaPresupuesto,"",$estilos);
		imprimirLinea($lineaCosTon,"",$estilos);
		/* imprimirLinea($linea2costobar,"",$estilos); */
	}
	else{
	//	imprimirLinea($lineaPresupuestoprom,"",$estilos);
		imprimirLinea($lineaCosprom,"",$estilos);
		/* imprimirLinea($linea2promcostobar,"",$estilos); */
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
if($html)
{
	if ($tipo_info==1){
		imprimirLinea($lineaTon,"",$estilos);
		imprimirLinea($TonBascula,"",$estilos);
		imprimirLinea($GAPTon,"",$estilos);		
		//imprimirLinea($lineaTonEscDom,"",$estilos);
		//imprimirLinea($lineaTonEscClan,"",$estilos);
		imprimirLinea($lineaPuntosCriticos,"",$estilos);		
		imprimirLinea($lineaCorteCesped,"",$estilos);
		imprimirLinea($lineaArbolPoda,"",$estilos);
		imprimirLinea($lineaMetroLava,"",$estilos);		
	//	imprimirLinea($lineaMetroCuadradoHom,"",$estilos);	
	//	imprimirLinea($lineaMetroCuadradoMaq,"",$estilos);
	//	imprimirLinea($lineaNumArbolHom,"",$estilos);
		imprimirLinea($lineaKmLimpiaPlayas,"",$estilos);
		
		
	
		/* imprimirLinea($linea7201,"",$estilos); */
		/* imprimirLinea($linea7202,"",$estilos); */
		/* imprimirLinea($linea7203,"",$estilos); */
		//imprimirLinea($lineaGestionPQRS,"",$estilos);
	}
	else{
		imprimirLinea($lineaTonprom,"",$estilos);
		imprimirLinea($TonBasculaprom,"",$estilos);
		imprimirLinea($GAPTonprom,"",$estilos);		
	//	imprimirLinea($lineaTonEscDomprom,"",$estilos);
	//	imprimirLinea($lineaTonEscClanprom,"",$estilos);
		imprimirLinea($lineaPuntosCriticosprom,"",$estilos);		
		imprimirLinea($lineaCorteCespedprom,"",$estilos);
		imprimirLinea($lineaArbolPodaprom,"",$estilos);
		imprimirLinea($lineaMetroLavaprom,"",$estilos);		
	//	imprimirLinea($lineaMetroCuadradoHomprom,"",$estilos);
	//	imprimirLinea($lineaMetroCuadradoMaqprom,"",$estilos);
	//	imprimirLinea($lineaNumArbolHomprom,"",$estilos);
		imprimirLinea($lineaKmLimpiaPlayasprom,"",$estilos);
		/* imprimirLinea($linea7201prom,"",$estilos); */
		/* imprimirLinea($linea7202prom,"",$estilos); */
		//imprimirLinea($linea7203prom,"",$estilos);
	//	imprimirLinea($lineaGestionPQRSprom,"",$estilos);
	}
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaTon, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $TonBascula, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $GAPTon, array(1=>"txt_izq"));
	//imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaTonEscDom, array(1=>"txt_izq"));
	//imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaTonEscClan, array(1=>"txt_izq"));	
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaPuntosCriticos, array(1=>"txt_izq")); 
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaCorteCesped, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaArbolPoda, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaMetroLava, array(1=>"txt_izq")); 
/* 	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaMetroCuadradoHom, array(1=>"txt_izq")); 
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaMetroCuadradoMaq, array(1=>"txt_izq")); 
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaNumArbolHom, array(1=>"txt_izq")); */
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaKmLimpiaPlayas, array(1=>"txt_izq"));
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
				$lineaprom[] = number_format($totalCentro/$numMes,1)."  ".number_format($totalCentro1/$numMes,1)."%";
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
		$lineaprom[] = number_format(($total/$numCen),2)." / ".number_format(($total1/$numCen),2)."%";
	else
		$lineaprom[] = number_format(($total/$numCen),2);
		
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
		/* imprimirLinea($lineakmsbar,"",$estilos); */
	/* 	imprimirLinea($linea2costobar,"",$estilos); */
	/* 	imprimirLinea($linea3kmbarope,"",$estilos); */
	/* 	imprimirLinea($linea4bolsasope,"",$estilos); */
		
	}
	else {
		imprimirLinea($lineaTontripprom,"",$estilos);
		imprimirLinea($lineaviajesturprom,"",$estilos);
		/* imprimirLinea($lineapromkmsbar,"",$estilos) */;
		/*imprimirLinea($linea2promcostobar,"",$estilos);*/
		/*imprimirLinea($linea3promkmbarope,"",$estilos);*/
		/*imprimirLinea($linea4promkmsbar,"",$estilos); */
	}
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaTontrip, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaviajestur, array(1=>"txt_izq"));
/*	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineakmsbar, array(1=>"txt_izq"));*/
 /* imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea2costobar, array(1=>"txt_izq"));*/
/* 	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea3kmbarope, array(1=>"txt_izq")); */
	/* imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea4bolsasope, array(1=>"txt_izq")); */
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
if($html)
{
	if ($tipo_info==1){
	//	imprimirLinea($lineaRotacion,"",$estilos);
	//	imprimirLinea($lineaAusentismo,"",$estilos);
	}
	else{
		imprimirLinea($lineaRotacionprom,"",$estilos);
		imprimirLinea($lineaAusentismoprom,"",$estilos);
	}
}
else
{
//	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaRotacion, array(1=>"txt_izq"));
//	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaAusentismo, array(1=>"txt_izq"));
}


########################################
##seccion doonde estan las /*funciones*/
########################################
function consultatons(&$fila, &$columna, $html, $idCentros, $servicio, $inicio, $final, $estilos, $orden, &$toneladas)
{

	if ($servicio  == 'rec')$idservicio="1,10";	
	if ($servicio  == 'bar')$idservicio="2.4,5";		
	if ($servicio  == 'clus')	$idservicio="3,9,17,18,20";	
	if ($servicio  == 'todo')	$idservicio="1,2,3,4,5,9,10,17,18,20";
	
	global $db, $dbpromo, $dbnacionalproduccion,  $CFG, $ME;
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

function consuEficiencia(&$fila, &$columna, $html, $idCentros,$servicio, $inicio, $final, $estilos, $orden, &$eficiencia, &$eficiencia1, &$tpvhs, &$tpvh)
{
	//echo $idCentros."<br><br>";
	
	/* $idCentro2=15;
	$idCentro="1,2,3,4";
 */
	global $db, $dbpromo, $dbnacionalproduccion, $CFG, $ME;	
	global $workbook;
	global $worksheet;	
	$idservicios = "9,17,20";
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
	
	$tpvh2= "1,2,6,8,14,19,21,28";
	$tpvh3= "1,19,21";
	
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
						GROUP BY  p.id,p.peso_inicial,p.peso_final,p.peso_total,v.id_tipo_vehiculo,v.id_centro,tv.tipo,tv.orden_info,tv.capacidad,tv.capacidad) as pesos
						ON mov.id=pesos.id_movimiento
					LEFT JOIN vehiculos vh ON mov.id_vehiculo=vh.id
					LEFT JOIN micros i ON i.id=mov.id_micro
					LEFT JOIN servicios s ON s.id = i.id_servicio
					LEFT JOIN centros c ON vh.id_centro=c.id
					WHERE pesos.id_tipo_vehiculo in ($tpvh2) and inicio::date >= '$inicio' AND inicio::date<='$final' and c.id in ($idCentros) and i.id_servicio in($idservicios)
						 
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
				
			
		$qidefic = $dbnacionalproduccion->sql_query($sqlefic);		
 		while($efic = $dbnacionalproduccion->sql_fetchrow($qidefic))
		{		
	
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

function consuviajtontrip(&$fila, &$columna, $html, $idCentros, $servicio, $inicio, $final, $estilos, $orden, &$tontrip, &$viajtur)
{
	/* $idCentro2=15;
	$idCentro="1,2,3,4"; */
	global $db, $dbpromo, $dbnacionalproduccion, $CFG, $ME;
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
		
	
		$qidefic = $dbnacionalproduccion->sql_query($sqlefic);
		while($efic = $dbnacionalproduccion->sql_fetchrow($qidefic)){		
			$tontrip[$efic["id"]][$efic["fecha"]]+=number_format($efic["tontripula"],2);
			$viajtur[$efic["id"]][$efic["fecha"]]+=number_format($efic["promviajes"],2);
			$i++;
		} 
				
		$qidefic2 = $dbpromo->sql_query($sqlefic);
 		while($efic = $dbpromo->sql_fetchrow($qidefic2)){		
			$tontrip[$efic["id"]][$efic["fecha"]]+=number_format($efic["tontripula"],2);
			$viajtur[$efic["id"]][$efic["fecha"]]+=number_format($efic["promviajes"],2);
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
