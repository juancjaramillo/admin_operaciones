<?
/*-----------------------------------------------------------------------------------------
'Descripción       :  Visualiza los indicadores para el reporte para la gerencia comercial
'                     
'Autor      		: Juan Carlos Jaramillo . Aseo Regional - Promoambiental Distrito.
'Fecha de Creación  : marzo 21/2019
'-------------------------------------------------------------------------------------------
'	Propósito :	Armar el reporte de los indicadores para la Gerencia Comercial. Visualizar 
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
$titulo1 = $dbcorporativo213->sql_row("SELECT nombre_nivel as inf FROM indicadores.nivel_indicador i WHERE i.id=".$id_nivel."and estado=1");

if($html){
	include($CFG->dirroot."/templates/header_popup.php");	
}
else{
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

$titulosmeses[] = 'INDICADORES DE '.$titulo1["inf"];
$estilosmeses[] = "align='Center' height='3em' class='azul_osc_12'";
$estilosdatos[] = "align='right' class='azul_osc_12'";

$titulosVolumenes = array("VOLUMENES");
$titulosEficiencia = array("EFICIENCIA");


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

/****CAPTURA EL VALOR DE CENTROS CON PERMISOS DE USUARIOS EN PROMO NACIONAL	****/	
while($queryCen = $db->sql_fetchrow($qidCentro)){
	$centros[$queryCen["id_centro"]] = $queryCen["centro"];
	$centrosnacional[$queryCen["id_centro"]] = $queryCen["centro"];
	$tituloscentro[] = $queryCen["centro"];
	$estiloscentro[] = "align='Center' class='azul_osc_12'";
	if ($tipo_info==1){
		foreach($fechas as $mes){
			$titulosmeses[] = ucfirst(strftime("%b.%Y",strtotime($mes."-01")));
			$estilosmeses[] = "align='Center' width=10em height='3em' class='azul_osc_12'";
			$estilosdatos[] = "align='right' class='azul_osc_12'";
			$i++;			
		}
	}else {
		$titulosmeses[] = $queryCen["centro"]." Consolidado";
		$estilosmeses[] = "align='Center' width=10em height='3em' class='azul_osc_12'";
		$titulosmeses[] = "Ultimo Mes";
		$estilosdatos[] = "align='right' class='azul_osc_12'";
	}
}
/****CAPTURA EL VALOR DE CENTROS CON PERMISOS DE USUARIOS EN PROMODISTRITO	****/
while($queryCen = $dbpromo->sql_fetchrow($qidCentro2)){
	$centros[$queryCen["id_centro"]] = $queryCen["centro"];	
	$centrospromo[$queryCen["id_centro"]] = $queryCen["centro"];
	$tituloscentro[] = $queryCen["centro"];
	$estiloscentro[] = "align='Center' class='azul_osc_12'";
	if ($tipo_info==1){
		foreach($fechas as $mes){
			$titulosmeses[]  = ucfirst(strftime("%b.%Y",strtotime($mes."-01")));
			$estilosmeses[] = "align='Center' width=10em height='3em' class='azul_osc_12'";
			$estilosdatos[] = "align='right' class='azul_osc_12'";
			$i++;			
		}
	}else {
		$titulosmeses[]= $queryCen["centro"]." ";
		$estilosmeses[] = "align='Center' width=10em height='3em' class='azul_osc_12'";
		$titulosmeses[]  = "Ultimo Mes";
		$estilosdatos[] = "align='right' class='azul_osc_12'";
	}
}
$titulosmeses[] = "Promedios <br> Totales";
$estilosmeses[] = "align='Center' width=10em height='3em' class='azul_osc_12'";
$estilosdatos[] = "align='right' class='azul_osc_12'";

if (count($titulosmeses)<9){$ancho='100%';}
else {$ancho='76em';}

?>
<script language= "JavaScript">
	var ancho , alto , cCeldas , celdas , pasoH , pasoV;
	function iniciar(){
		celdas0 = document.getElementById("encCol").getElementsByTagName("td").length;
		celdas1 = document.getElementById("contenido").getElementsByTagName("td").length;
		for (i=0; i<celdas0;i++){
			cCeldas = document.getElementById("encCol").getElementsByTagName("td").item(i).innerHTML;
			document.getElementById("encCol").getElementsByTagName("td").item(i).innerHTML = cCeldas+"<img class=\"rell\">";
		}

		for (j=0; j<celdas1;j++){
			cCeldas = document.getElementById("contenido").getElementsByTagName("td").item(j).innerHTML;
			document.getElementById("contenido").getElementsByTagName("td").item(j).innerHTML = cCeldas+"<img class=\"rell\">";
		}
	}
	function desplaza(){
		pasoH = document.getElementById("contenedor").scrollLeft;
		pasoV = document.getElementById("contenedor").scrollTop;
		document.getElementById("contEncCol").scrollLeft = pasoH;
		document.getElementById("contEncFil").scrollTop = pasoV;
	}
</script>
<style>
	table{border-collapse:collapse}
	table td{font:12px; border:solid 1px #3B7C93; height:4em}
	#contEncCol{overflow:hidden; overflow-y:scroll; text-align:Center; width:<?echo $ancho;?>; height:4em; }
	#contEncFil{overflow:hidden; overflow-x:scroll; text-align:Left; width:20em; height:30em}
	#contenedor{overflow:auto; width:<?echo $ancho;?>; height:30em}
	#contenido{}
	.tabla td{border:1px solid; width:10em}
	.rell{font:12px; width:10em; height:0; position:relative; top:-1em; z-index:-1; bor der:1px solid red}
</style>
<BODY onload=iniciar()>
<?

if ($tipo_info==1){
	$colspan = (count($fechas) * count($centros))+2;
	if($html){
		$tituloscentro[] = "";
		$estiloscentro[] = "align='Center' class='azul_osc_12'";
		echo '<table border=1 bordercolor=#3B7C93;>
			  <tr>';
			echo '<th width=300em; height=6em; class="azul_osc_16" >'.trim($titulosmeses[0]).'</th>';
			echo '<th>';
			echo '<div id="contEncCol">';
			echo '<table border=1 bordercolor=#3B7C93 id="encCol">';
			echo '<tr>';
			foreach($tituloscentro as $tt){
				if ($tt=="Promedios <br> Totales"){
					echo '<th>'.$tt.'</th>';
				}else {
					echo '<th class="azul_osc_13" align="Center" colspan='.count($fechas).'>'.$tt.'</th>';
				}
			}
			echo '</tr>';
	}else{
		titulos_uno_xls($workbook, $worksheet, $fila, $columna, array("MENSUAL"));
		foreach($titulos as $tt){
			titulos_uno_xls($workbook, $worksheet, $fila, $columna, array($tt), count($fechas)-1);
			$columna+=count($fechas)-1;
		}
		titulos_uno_xls($workbook, $worksheet, $fila, $columna, array("Promedios <br> Totales"));
		$fila++;$columna=0;
		
		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array("INDICADORES OPERACIONALES"),array(1=>"azul_izq"), $colspan-1);
		$fila++;$columna=0;

		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosmeses, array(1=>"azul_izq"));	
		$fila++;$columna=0;
	}	
}else {
	$colspan = (count($centros)*2)+2;
	if($html){
		$tituloscentro[] = "";
		$estiloscentro[] = "align='Center' class='azul_osc_12'";		
		echo '<table>';
	}else{
		titulos_uno_xls($workbook, $worksheet, $fila, $columna, array("MENSUAL"));
		foreach($titulos as $tt)
		{
			titulos_uno_xls($workbook, $worksheet, $fila, $columna, array($tt), count($fechas)-1);
			$columna+=count($fechas)-1;
		}
		titulos_uno_xls($workbook, $worksheet, $fila, $columna, array("Promedios <br> Totales"));
		$fila++;$columna=0;
		
		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array("INDICADORES OPERACIÓN"),array(1=>"azul_izq"), $colspan-1);
		$fila++;$columna=0;

		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosmeses, array(1=>"azul_izq"));	
		$fila++;$columna=0;	
	}
}

 if($html){
	$titulosmeses1= array_slice($titulosmeses, 1);
	imprimirLinea($titulosmeses1, "#b2d2e1", $estilosmeses);
	echo '</table>';
	echo '</div>';
	echo '</th>';
	echo '</tr>';
}else{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosmeses, array(1=>"azul_izq"));
	$fila++;$columna=0;
}  

/********************************************/
	   // HASTA AQUI ENCABEZADOS Y TITULOS //
/********************************************/	

$qidcolumnasid = $dbcorporativo213->sql_query("SELECT ini.id, ini.nombre_indicador 
FROM indicadores.inventario_indicadores ini 
LEFT JOIN indicadores.nivel_indicador ni ON ni.id=ini.id_nivel_indicador 
WHERE ini.id_nivel_indicador='".$id_nivel."' and ini.estado=1 ORDER BY ini.orden asc"); 

echo '<tr><td><div id="contEncFil"><table Id="encFil">';

while($querycolumna = $dbcorporativo213->sql_fetchrow($qidcolumnasid)){
	echo '<tr><td width=300em class="azul_osc_12">'.trim(ucfirst(strtolower($querycolumna[1]))).'</td></tr>';
}
echo '</table>
</div>
</td>
<td>
<div id="contenedor" onscroll="desplaza()">
 <table class="tabla" id= "contenido">';
 
$qidnivel = $dbcorporativo213->sql_query("SELECT ini.id, ini.nombre_indicador 
FROM indicadores.inventario_indicadores ini 
LEFT JOIN indicadores.nivel_indicador ni ON ni.id=ini.id_nivel_indicador 
WHERE ini.id_nivel_indicador='".$id_nivel."' and ini.estado=1 ORDER BY ini.orden asc"); 

while($querynivel = $dbcorporativo213->sql_fetchrow($qidnivel)){
	$titulosniveles=array("$querynivel[1]");
	$qidindicador = $dbcorporativo213->sql_query("SELECT ini.id, ini.nombre_indicador FROM indicadores.inventario_indicadores ini 
	WHERE ini.id='".$querynivel[0]."' and ini.id_nivel_indicador='".$id_nivel."' and ini.estado=1 ORDER BY ini.orden asc"); 
	while($queryindicador = $dbcorporativo213->sql_fetchrow($qidindicador)){	
		 $titulosindicadores[$queryindicador[0]]      =array("$queryindicador[1]");
		 $titulosindicadoresprom[$queryindicador[0]]  =array("$queryindicador[1]");
		
		$kmTotValorCen = 0;	
	
		foreach($centros as $idCentro => $da){
			$cosMttoCen = $gastosCen = $numTaCen = $totUsuCen = $totEmpCen = $totFactCen = $totRecauCen = $totRCCen = $totUVDCen = $cosMOCen = $counmeslineaNumSolCompra =  $counmeslineaCompTotalVentas = $counmesAhorroxCompania = $counmeslineaRetiros = $counmeslineaAccid = $counmesOporEntprom = $counmesCompetitiveBinding = $totlineaNumSolCompraCen = $totlineaCompTotalVentasCen = $totDPCen = $totRetirosCen = $totalvalor  = 0;
			$vars =  array();	
			foreach($fechas as $mes){			
				$qind = $dbcorporativo213->sql_query("SELECT c.id, c.valor as valor FROM indicadores.indicadores c WHERE id_centro='".$idCentro."' 
				and periodo = '".$mes."' and id_inventario_indicador= '".$queryindicador[0]."' and estado=1");	
				$query = $dbcorporativo213->sql_fetchrow($qind);
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
				imprimirLinea(array_slice($titulosindicadores[$queryindicador[0]],1),"",$estilosdatos);
			}else{
				imprimirLinea(array_slice($titulosindicadoresprom[$queryindicador[0]],1),"",$estilosdatos);
			}
			}
			else{
				imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $titulosindicadores[$queryindicador[0]], array(1=>"txt_izq"));
			}
	}
}

if($html){
	$link = "?format=xls&inicio=".$inicio."&final=".$final;
	echo "</table> </div> </td></tr></table>";
	echo "	<input type='button' class='boton_verde' value='Bajar en xls' onclick=\"window.location.href='".$ME.$link."'\"/>";
	echo "<br></br>";
}else{
	$workbook->close();
	$nombreArchivo=preg_replace("/[^0-9a-z_.]/i","_",$titulo1["inf"])."_".$inicio."_".$final.".xls";
	header("Content-Type: application/x-msexcel; name=\"".$nombreArchivo."\"");
	header("Content-Disposition: inline; filename=\"".$nombreArchivo."\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
}

?>