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

//$titulo1 = $db->sql_row("SELECT upper(nombre||' : '||informe) as inf FROM informes i LEFT JOIN categorias_informes c ON c.id=i.id_categoria_informe WHERE i.id=".str_replace(".php","",simple_me($ME)));
$titulo1["inf"]="INDICADORES GERENCIALES : INDICADORES FACTURACI�N";
if($html)
{
	include($CFG->dirroot."/templates/header_popup.php");
	//include($CFG->dirroot."/info/templates/fechas_form_46.php");
	tabla_titulos_reportes($titulo1["inf"]);
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
$titulosComerciales = array("COMERCIALES");

$estilosTitulosRecoleccion = array(1=>" height='30'  class='azul_osc_14'");
$estilos = array(1=>"align='left'  class='azul_osc_12'");
$i=2;



/****BUSCA EL NUMERO DOCUMENTO EN LA BASE DE DATOS LOCAL ***/
$qiddocumento = $db->sql_query("SELECT id, cedula FROM personas WHERE id='".$user["id"]."'");
$querydocumento = $db->sql_fetchrow($qiddocumento);
$documento= $querydocumento["cedula"];

/*** CON EL NUMERO DE DOCUMENTO BUSCA EL ID EN LA BASE DE DATOS DE CONSULTA DE REPORTES  ***/
$qidCentro = $dbnacionalproduccion->sql_query("SELECT id_centro, centro FROM personas_centros LEFT JOIN centros ON centros.id=personas_centros.id_centro 
LEFT JOIN personas ON personas.id=personas_centros.id_persona  WHERE cedula='".$documento."' ORDER BY id_centro ");

//Consulta a base de datos de promoambiental
$qidCentro2 = $dbpromo->sql_query("SELECT id_centro, centro FROM personas_centros LEFT JOIN centros ON centros.id=personas_centros.id_centro 
LEFT JOIN personas ON personas.id=personas_centros.id_persona  WHERE cedula='".$documento."' ORDER BY id_centro ");



while($queryCen = $db->sql_fetchrow($qidCentro)){
	$centros[$queryCen["id_centro"]] = $queryCen["centro"];
	$titulos[] = $queryCen["centro"];
	if ($tipo_info==1){
		foreach($fechas as $mes)
		{
			$titulosComerciales[] = ucfirst(strftime("%b.%Y",strtotime($mes."-01")));	
			$estilosTitulosRecoleccion[$i] = " class='azul_osc_14'";
			$estilos[$i] = "class='azul_osc_12'";
			$i++;
		}
	}
	else {
		$titulosComerciales[]  = $queryCen["centro"];	
		$estilos[] = "class='azul_osc_12'";
	}
}
while($queryCen = $dbpromo->sql_fetchrow($qidCentro2)){
	$centros[$queryCen["id_centro"]] = $queryCen["centro"];
	$titulos[] = $queryCen["centro"];
	if ($tipo_info==1){
		foreach($fechas as $mes)
		{
			$titulosComerciales[] = ucfirst(strftime("%b.%Y",strtotime($mes."-01")));	
			$estilosTitulosRecoleccion[$i] = " class='azul_osc_14'";
			$estilos[$i] = "class='azul_osc_12'";
			$i++;
		}
	}
	else {
		$titulosComerciales[]  = $queryCen["centro"];	
		$estilos[] = "class='azul_osc_12'";
	}
}



$titulosComerciales[] = "PROMEDIOS CONSOLIDADOS";
$estilosTitulosRecoleccion[] = " class='azul_osc_14'";
$estilos[] = "class='azul_osc_12'";


if ($tipo_info==1){
	$colspan = (count($fechas) * count($centros))+2;
	if($html)
	{
		$titulos[] = "Consolidados Promedios";
		$anchotabla = (120*(count($titulosComerciales)-2))+430;
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
		echo '<tr><td  height="30" colspan="'.$colspan.'" align="left" bgcolor="#b2d2e1" class="azul_osc_14">INDICADORES OPERACIONALES</td></tr>';
	
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
		
		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array("INDICADORES OPERACIONALES"),array(1=>"azul_izq"), $colspan-1);
		$fila++;$columna=0;

		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosComerciales, array(1=>"azul_izq"));	
		$fila++;$columna=0;
	}	
}
else {
	$colspan = count($centros)+2;
	if($html)
	{
		$titulos[] = "Consolidado Total";
		$anchotabla = (120*(count($titulosComerciales)-2))+430;
		if ($anchotabla<800) $anchotabla=800;
		echo '<table width="'.$anchotabla.'" border=1 bordercolor="#7fa840" align="center">';	
		echo '<tr><td  height="30" colspan="'.$colspan.'" align="left" bgcolor="#b2d2e1" class="azul_osc_14">INDICADORES OPERACIONALES</td></tr>';
		imprimirLinea($titulosComerciales, "#b2d2e1", $estilosTitulosRecoleccion);
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
		
		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array("INDICADORES OPERACIONALES"),array(1=>"azul_izq"), $colspan-1);
		$fila++;$columna=0;

	
	}
}
#JFMC Para que solo los vean los Directores y Gerentes

if ($nivel==1 || $nivel==13 || $nivel==7 || $nivel==8 || $nivel==11 || $nivel==12 || $nivel==15){
	#generales
	if($html)
		imprimirLinea($titulosGenerales, "#b2d2e1", $estilosTitulosRecoleccion);
	else
	{
		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosGenerales, array(1=>"azul_izq"));
		$fila++;$columna=0;
	}
	
	$linea5 = array("N� total de Usuarios");	
	$lineaFact = array("Facturaci�n Total");
	$lineaUVD = array("Usuarios Vinculados Vs Desvinculados");
	$linea5prom = array("N� total de usuarios");	
	$lineaFactprom = array("Facturaci�n Total");
	$lineaUVDprom = array("Usuarios Vinculados Vs Desvinculados");
	
	$otrasLineas = $nombreOtras = $otrasLineasDos  = array();
	$otrasLineasprom = array();

	#ind comerciales
	$qidOV = $dbnacionalproduccion->sql_query("SELECT * FROM variables_informes WHERE id=13 or id=14 or id=15 or id=17 or id=20 or  (id >=23 AND id<=26) or id= 38 ORDER BY orden");
	while($queryOV = $dbnacionalproduccion->sql_fetchrow($qidOV))
	{
		$nombreOtras[$queryOV["id"]] = $queryOV["variable"];			
	}
	
	$qidOV2 = $dbpromo->sql_query("SELECT * FROM variables_informes WHERE id=13 or id=14 or id=15 or id=17 or id=20 or  (id >=23 AND id<=26) or id= 38 ORDER BY orden");
	while($queryOV = $dbpromo->sql_fetchrow($qidOV2))
	{
		$nombreOtras[$queryOV["id"]] = $queryOV["variable"];			
	}
	
	$cosMtto = $gastos = $numTa = $totEmp = $totFact = $totRecau = $totRC = $totUVD = $cosMO = $totindllanta =0;
	foreach($centros as $idCentro => $da)
	{
		$cosMttoCen = $gastosCen = $numTaCen = $totUsuCen = $totEmpCen = $totFactCen = $totRecauCen = $totRCCen = $totUVDCen = $cosMOCen =  0;
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
			//var_dump($vars)."<br>";
			//($vars[$query["id_variable_informe"]][$mes]);			
			$qid3 = $dbpromo->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c 
			LEFT JOIN servicios s ON s.id=c.id_servicio WHERE id_centro='".$idCentro."'  
			AND id_variable_informe >= 9 AND fecha = '".$mes."' 	GROUP BY id_variable_informe");				
			while($query = $dbpromo->sql_fetchrow($qid3))
			{
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

			#ind comerciales
			foreach($nombreOtras as $id => $name)
			{
				if(isset($vars[$id][$mes]))
					$otrasLineas[$id][$idCentro][$mes] = $vars[$id][$mes];
				else
					$otrasLineas[$id][$idCentro][$mes] = "";
			}
			#$valFact = nvl($vars[13][$mes],0) + nvl($vars[14][$mes],0) + nvl($vars[15][$mes],0);
			$valFact = nvl($vars[13][$mes],0) + nvl($vars[14][$mes],0);
			if ($valFact!='') {
				$totfec = $totfec+1;
			}
			
			if ($valFact!=0)
				$lineaFact[] = number_format($valFact,0);
			else
				$lineaFact[] = '';
				
			$totFactCen+=$valFact;

			$valRecau = (nvl($vars[37][$mes],0)/$valFact)*100;
			if ($valRecau!=0){
				$lineaRecau[] = number_format($valRecau,2)."%";
				$lineaRecaucount[] = number_format($valRecau,2)."%";}
			else 
				$lineaRecau[] = '';
			$totRecauCen += $valRecau;

			$valRC =  (nvl($vars[19][$mes],0)/$valFact)*100;			
			 if ($valRC!=0)
				$lineaRC[] = number_format($valRC,2)."%";
			else
				$lineaRC[] = ''; 
			
			$totRCCen+=$valRC;

			@$valUVD = nvl($vars[26][$mes],0)-nvl($vars[38][$mes],0);
			$lineaUVD[] = number_format($valUVD,0);
			$totUVDCen+=$valUVD;
			
		}
			
		$totUsuCen = $totUsuCen/count($fechas);
		$linea5prom[] = number_format($totUsuCen,0);
		$totUsu+=$totUsuCen;

		$totFactCen = $totFactCen/count($lineaRecaucount);
		$lineaFactprom1[] = $totFactCen;
		$lineaFactprom[] = number_format($totFactCen,0);
		$totFact+=$totFactCen;

		$totRecauCen = $totRecauCen/count($lineaRecaucount);
		$lineaRecauprom1[]  = $totRecauCen;
		//$lineaRecauprom[]  = number_format($totRecauCen,2).'%';
		$totRecau+=$totRecauCen;

		$totRCCen = $totRCCen/count($lineaRecaucount);
		//$lineaRCprom[] = number_format($totRCCen,2)."%";
		$totRC+=$totRCCen;

		$totUVDCen = $totUVDCen/count($fechas);
		$lineaUVDprom[] = number_format($totUVDCen,0);
		$totUVD+=$totUVDCen;
		unset($lineaRecaucount);
	}

	$linea5[] = number_format($totUsu,0);	
	$lineaFact[] = number_format($totFact/count($centros),0);
	//var_dump($lineaFact);
	//print_r($lineaFact);
	//$lineaRecau[]  = number_format($totRecau/count($centros),0)."%";
	//$lineaRC[] = number_format($totRC/count($centros),0)."%";
	$lineaUVD[] = number_format($totUVD/count($centros),0);	
	$linea5prom[] = number_format($totUsu,0);		
	$lineaFactprom[] = number_format(array_sum($lineaFactprom1),0);
	//$lineaRecauprom[]  = number_format(array_sum($lineaRecauprom1)/count($lineaRecauprom1),2).'%';
	//$lineaRCprom[] = number_format($totRC/count($centros),2)."%";
	$lineaUVDprom[] = number_format($totUVD/count($centros),0);

	if($html)
	{
		if ($tipo_info==1){
		
			imprimirLinea($lineaInopera,"",$estilos);		
		}
		else{		
			imprimirLinea($lineaInoperaProm,"",$estilos);		
		}
	}
	

	if($html)
		imprimirLinea($titulosComerciales, "#b2d2e1", $estilosTitulosRecoleccion);
	else
	{
		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosComerciales, array(1=>"azul_izq"));
		$fila++;$columna=0;
	}
	
	#facturacion
	imprimirOtras($tipo_info,$fila, $columna, $html, $centros, $fechas, $nombreOtras, $otrasLineas, $estilos, array(13,14));
	
	if($html){
		if ($tipo_info==1){
			imprimirLinea($lineaFact,"", $estilos); 
		}
		else{
			imprimirLinea($lineaFactprom,"", $estilos);
		}
	}
	else {
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaFact, array(1=>"txt_izq"));
	}	
	
	imprimirOtras($tipo_info,$fila, $columna, $html, $centros, $fechas, $nombreOtras, $otrasLineas, $estilos, array(20,21,22,15,23,24,25));
	if($html){
		if ($tipo_info==1){
			imprimirLinea($lineaUVD,"", $estilos);
		  imprimirLinea($linea5,"", $estilos);	
		}
		else{
			imprimirLinea($lineaUVDprom,"", $estilos);
			imprimirLinea($linea5prom,"", $estilos);
		}
	}
	else {
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaUVD, array(1=>"txt_izq"));
	}
		
	if($html)
		imprimirLinea($titulosIndFinan, "#b2d2e1", $estilosTitulosRecoleccion);
	else
	{
		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosIndFinan, array(1=>"azul_izq"));
		$fila++;$columna=0;
	}
}

/*funciones*/
function imprimirOtras($tipo_info,&$fila, &$columna, $html, $centros, $fechas, $nombreOtras, $otrasLineas, $estilos, $imprimirSolo = array(), $signoAd = "")
{
	global $workbook;
	global $worksheet;
	$councentot = 0;
	
	foreach($nombreOtras as $key => $nombre)
	{
		$sigue = false;
		if(count($imprimirSolo) == 0)
			$sigue = true;
		elseif(in_array($key, $imprimirSolo))
			$sigue = true;

		if($sigue)
		{
			$linea = array($nombre);
			$lineaprom = array($nombre);
			$total = 0;

			foreach($centros as $idCentro => $da)
			{
				$totalCen = $councen = 0;
				foreach($fechas as $mes)
				{
					$val = nvl($otrasLineas[$key][$idCentro][$mes]);
					if($val != "")
					{
						if ($key==13 or $key==14 or $key==15 or $key==37 or $key==21 or $key==22 or $key==23 or $key==24 or $key==25)	$linea[] = number_format($val,0).$signoAd;
						else $linea[] = number_format($val,2).$signoAd;
						$totalCen+=$val;
						$councen = $councen + 1;
						$councentot = $councentot +1;
					}
					else
						$linea[] = "";
				}
				$totalCen = $totalCen/$councen;
				if ($key==13 or $key==14 or $key==15 or $key==37 or $key==21 or $key==22 or $key==23 or $key==24 or $key==25) $lineaprom[] = number_format($totalCen,0).$signoAd;
				else 		$lineaprom[] = number_format($totalCen,2);	
				$total+=$totalCen;
			}

			if ($key==13 or $key==14 or $key==15 or $key==37 or $key==21 or $key==22 or $key==23 or $key==24 or $key==25)  $linea[] = number_format($total/count($centros),0).$signoAd;
		  else $linea[] = number_format($total/count($centros),2).$signoAd;

			if ((key($otrasLineas))==13 && ($lineaprom[0]!='Costo Facturaci�n por usuario' && $lineaprom[0]!='Aforo a Grandes Generadores')) 		
					 if ($key==13 or $key==14 or $key==15 or $key==37 or $key==21 or $key==22 or $key==23 or $key==24 or $key==25)  $lineaprom[] = number_format($total,0).$signoAd;
					 else $lineaprom[] = number_format($total,2).$signoAd;
			else
					if ($key==13 or $key==14 or $key==15 or $key==37 or $key==21 or $key==22 or $key==23 or $key==24 or $key==25) $lineaprom[] = number_format($total/count($centros),0).$signoAd;
					else $lineaprom[] = number_format($total/count($centros),2).$signoAd;
			#$lineaprom[] = number_format($total/count($centros),2).$signoAd;

			if($html){
				if ($tipo_info==1){
					
					imprimirLinea($linea,"",$estilos);
				}
				else{
					imprimirLinea($lineaprom,"",$estilos);
				}
			}
			else
				imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
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
