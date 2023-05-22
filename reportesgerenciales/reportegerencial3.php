<?

/*-----------------------------------------------------------------------------------------
'Descripción       :  Visualiza los indicadores para el reporte para la gerencia comercial
'                     

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
$id_nivel = $_GET["id_nivel"];
if($tipo_info=="") {
	$tipo_info=1;
}
if(isset($_GET["format"])){
	$html=false;
	$inicio = $_GET["inicio"];
	$final = $_GET["final"];
}

/*'Armar el esquema de las tablas y titulos de columnas
'=======================================================*/	
$titulo1 = $dbcorporativo->sql_row("SELECT nombre_nivel as inf FROM indicadores.nivel_indicador i WHERE i.id=".$id_nivel);




if($html){
	include($CFG->dirroot."/templates/header_popup.php");	
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
	$fila=2; $columna=0;
}
$inicio = $_GET["inicio"];
$final = $_GET["final"]; 
$fechas = sacarMeses($inicio, $final);
$titulos = array();

$titulosFinanciera = array("FINANCIERA");
$titulosVolumenes = array("VOLUMENES");
$titulosEficiencia = array("EFICIENCIA");

$estilostitulos = array(1=>" height='30'  class='azul_osc_14'");
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
/*=============================================================================*/
	$centrosnacional[$queryCen["id_centro"]] = $queryCen["centro"];
	
	$titulos[] = $queryCen["centro"];
	if ($tipo_info==1){
		foreach($fechas as $mes){
			$titulosFinanciera[] = $titulosVolumenes[] = $titulosEficiencia[] = ucfirst(strftime("%b.%Y",strtotime($mes."-01")));
			$estilostitulos[$i] = " class='azul_osc_14'";
			$estilos[$i] = "class='azul_osc_12'";
			$i++;			
		}
	}else {
		$titulosFinanciera[] = $titulosVolumenes[] = $titulosEficiencia[] = $titulosIndCons[] = $queryCen["centro"]." Consolidado";
		$titulosFinanciera[] = $titulosVolumenes[] = $titulosEficiencia[] = $titulosIndCons[] = "Ultimo Mes";
		$estilostitulos[] = " class='azul_osc_14'";
		$estilos[] = "class='azul_osc_12'";
		$estilostitulos[] = " class='azul_osc_14'";
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
		foreach($fechas as $mes){
			$titulosFinanciera[] = $titulosVolumenes[] = $titulosEficiencia[] = ucfirst(strftime("%b.%Y",strtotime($mes."-01")));
			$estilostitulos[$i] = " class='azul_osc_14'";
			$estilos[$i] = "class='azul_osc_12'";
			$i++;			
		}
	}else {
		$titulosFinanciera[] = $titulosVolumenes[] = $titulosEficiencia[] = $titulosIndCons[] = $queryCen["centro"]." Consolidado";
		$titulosFinanciera[] = $titulosVolumenes[] = $titulosEficiencia[] = $titulosIndCons[] = "Ultimo Mes";
		$estilostitulos[] = " class='azul_osc_14'";
		$estilos[] = "class='azul_osc_12'";
		$estilostitulos[] = " class='azul_osc_14'";
		$estilos[] = "class='azul_osc_12'";
	}
}
$titulosFinanciera[]=$titulosVolumenes[]=$titulosEficiencia[] = $titulosIndCons[] = "PROMEDIOS CONSOLIDADOS";
$estilostitulos[] = " class='azul_osc_14' width='150'";
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
		echo '<tr><td  height="30" colspan="'.$colspan.'" align="left" bgcolor="#b2d2e1" class="azul_osc_14">INDICADORES DE '.$titulo1["inf"].'</td></tr>';
		
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

$qidnivel = $dbcorporativo->sql_query("SELECT ti.id, ti.nombre_tipo, ti.variable
FROM indicadores.tipo_indicador ti 
LEFT JOIN indicadores.inventario_indicadores ini ON ini.id_tipo_indicador=ti.id 
LEFT JOIN indicadores.nivel_indicador ni ON ni.id=ini.id_nivel_indicador 
WHERE ni.id='".$id_nivel."' GROUP BY ti.id
ORDER BY ti.id"); 

while($querynivel = $dbcorporativo->sql_fetchrow($qidnivel)){
$titulosniveles=array("$querynivel[1]");

if($html){
	imprimirLinea($titulosFinanciera, "#b2d2e1", $estilostitulos);
}else{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosFinanciera, array(1=>"azul_izq"));
	$fila++;$columna=0;
}

$qidindicador = $dbcorporativo->sql_query("SELECT ini.id, ini.nombre_indicador FROM indicadores.inventario_indicadores ini 
WHERE ini.id_tipo_indicador='".$querynivel[0]."' and ini.id_nivel_indicador='".$id_nivel."' ORDER BY ini.orden_visualizacion,nombre_indicador"); 

while($queryindicador = $dbcorporativo->sql_fetchrow($qidindicador)){	
		 $titulosindicadores[$queryindicador[0]]      =array("$queryindicador[1]");
		 $titulosindicadoresprom[$queryindicador[0]]  =array("$queryindicador[1]");
		
		$kmTotValorCen = 0;	
	
	foreach($centros as $idCentro => $da){
		$cosMttoCen = $gastosCen = $numTaCen = $totUsuCen = $totEmpCen = $totFactCen = $totRecauCen = $totRCCen = $totUVDCen = $cosMOCen = $counmeslineaNumSolCompra =  $counmeslineaCompTotalVentas = $counmesAhorroxCompania = $counmeslineaRetiros = $counmeslineaAccid = $counmesOporEntprom = $counmesCompetitiveBinding = $totlineaNumSolCompraCen = $totlineaCompTotalVentasCen = $totDPCen = $totRetirosCen = $totalvalor  = 0;
		$vars =  array();	

	   foreach($fechas as $mes){			
			$qind = $dbcorporativo->sql_query("SELECT c.id, c.valor as valor FROM indicadores.indicadores c WHERE id_centro='".$idCentro."' 
			and periodo = '".$mes."' and id_inventario_indicador= '".$queryindicador[0]."' and estado=1");	
			
			$query = $dbcorporativo->sql_fetchrow($qind);
			$vars[$queryindicador[0]][$query[0]][$mes]  = $query[1];
				
					if(isset($vars[$queryindicador[0]][$query[0]][$mes])){
					$valultimomes = ($vars[$queryindicador[0]][$query[0]][$mes]);
					$val = ($vars[$queryindicador[0]][$query[0]][$mes]);				
					$titulosindicadores[$queryindicador[0]][] = $val;
					$totalvalor+= $val;
					$counmesTotalValor = $counmesTotalValor+1;
				}else
					$titulosindicadores[$queryindicador[0]][] = "";
	  }	
	  
		 /*** VALOR CONSOLIDADO ****/
		$titulosindicadoresprom[$queryindicador[0]][]= number_format($totalvalor,2);		
		 $kmTotValorCen+=$totalvalor; 	 	
		 /*** HASTA AQUI CONSOLIDADO ***/


		/*** VALOR ULTIMO MES ****/	
		 $titulosindicadoresprom[$queryindicador[0]][]= $valultimomes;
		/*** HASTA AQUI ULTIMO MES ***/		 
	 }	
		$titulosindicadores[$queryindicador[0]][]     = number_format($kmTotValorCen,2);
		$titulosindicadoresprom[$queryindicador[0]][] = number_format($kmTotValorCen,2);	
			if($html){
				if ($tipo_info==1){	
					imprimirLinea($titulosindicadores[$queryindicador[0]],"",$estilos);
				}else{
					imprimirLinea($titulosindicadoresprom[$queryindicador[0]],"",$estilos);
				}
			}else{
				imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $titulosindicadores[$queryindicador[0]], array(1=>"txt_izq"));
			}
	}
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