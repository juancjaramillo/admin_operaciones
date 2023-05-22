<?
/*-----------------------------------------------------------------------------------------
'Descripción       :  Visualiza los indicadores para el reporte para la gerencia de Tecnologia    
'Autor      		: Juan Carlos Jaramillo . Aseo Regional - Promoambiental Distrito.
'Fecha de Creación  : Agosto 16/2019
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
$titulo1["inf"]="INDICADORES GERENCIALES : INDICADORES DE TECNOLOGÍA";
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
/*=============================================================================*/
	$centrospromo[$queryCen["id_centro"]] = $queryCen["centro"];
	
	$titulos[] = $queryCen["centro"];	
	if ($tipo_info==1){
		foreach($fechas as $mes)
		{			
			$titulosFinanciera[] = $titulosVolumenes[] = $titulosEficiencia[]  = ucfirst(strftime("%b.%Y",strtotime($mes."-01")));
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
#JFMC Para que solo los vean los Directores y Gerentes

if ($nivel==1 || $nivel==13 || $nivel==7 || $nivel==8 || $nivel==11 || $nivel==12 || $nivel==15){
	
	
	
/********************************************/
//      HASTA AQUI ENCABEZADOS Y TITULOS    //
/********************************************/	
	
	
	$lineaProyEjecutados = array("Valor Proyectos Ejecutados / Ventas");	
	$lineaCantCasos = array("Cantidad Casos");
	$lineaCalSoft = array("Calidad del Software");
	$lineaDisponibilidad = array("Disponibilidad");	
	$lineaAtencion = array("Atención");
	
	
	$lineaProyEjecutadosprom = array("Valor Proyectos Ejecutados / Ventas");
	$lineaCantCasosprom = array("Cantidad Casos");
	$lineaCalSoftprom = array("Calidad del Software");
	$lineaDisponibilidadprom = array("Disponibilidad");	
	$lineaAtencionprom = array("Atención");
	
		
	$totProyEjecutados = $totCantCasos =  $totCalSoft = $TotDisponibilidadCen = $TotAtencion = 0;
	foreach($centros as $idCentro => $da)
	{
		$valFact = $valReNom = $totProyEjecutadosCen = $totlineaCantCasosCen = $counmeslineaCantCasos = $totlineaCalSoftCen = $counmeslineaCalSoftMes = $totDisponibilidadCen = 0;
		$valaten = $valdisp = $valsoft = $$valcas = $counmeslineaDisponibilidad =  $totAtencionCen = $counmeslineaAtencion = 0;
		$vars =  array();	
		
		foreach($fechas as $mes){
			
			if($idCentro!=15 and $idCentro!=14){
			$qid3 = $dbnacionalproduccion->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c 
			WHERE id_centro='".$idCentro."'  
			AND id_variable_informe >= 92 AND id_variable_informe <= 96  AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
			
			while($query = $dbnacionalproduccion->sql_fetchrow($qid3)){
					$vars[$query["id_variable_informe"]][$mes] = $query["val"];	
			}
			}else{
			$qid3 = $dbpromo->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c 
			WHERE id_centro='".$idCentro."'  
			AND id_variable_informe >= 92 AND id_variable_informe <= 96 	 AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
			
			
			while($query = $dbpromo->sql_fetchrow($qid3)){
					$vars[$query["id_variable_informe"]][$mes] = $query["val"];
				}
			}	
			 
				
			/*  Valor Proyectos Ejecutados / Ventas */
			/*======================================*/
			
			if((isset($vars[92][$mes])) || (isset($vars[13][$mes])) || (isset($vars[14][$mes]))){
				$valFact = nvl($vars[13][$mes],2) + nvl($vars[14][$mes],2);				
				$valReNom = (nvl($vars[92][$mes],2)/$valFact)*100;
				$lineaProyEjecutados[] = number_format($valReNom,2)."%";				
				$totProyEjecutadosCen+= $valReNom;
			}else
				$lineaProyEjecutados[] = "";


			/*           Cantidad Casos                **/
			/*==========================================*/	
			if(isset($vars[93][$mes]))
			{
				$valcas = nvl($vars[93][$mes],0);
				$lineaCantCasos[] = number_format($valcas,0);
				$totlineaCantCasosCen+= $valcas;
			}else
				$lineaCantCasos[] = "";
			
			
			
			/*         Calidad del Software            **/
			/*==========================================*/
			if(isset($vars[94][$mes]))
			{
				$valsoft =  nvl($vars[94][$mes],2);
				$lineaCalSoft[] = number_format($valsoft,2);
				$totlineaCalSoftCen+= $valsoft;
				$counmeslineaCalSoftMes = $counmeslineaCalSoftMes+1;
			}else
				$lineaCalSoft[] = "";
						
			
			/*              Disponibilidad             **/
			/*==========================================*/
			if(isset($vars[95][$mes]))
			{
				$valdisp =  nvl($vars[95][$mes],2);
				$lineaDisponibilidad[] = number_format($valdisp,2);
				$totDisponibilidadCen+= $valdisp;
				$counmeslineaDisponibilidad = $counmeslineaDisponibilidad+1;
			}else
				$lineaDisponibilidad[] = "";
		

			/*                  Atención               **/
			/*==========================================*/
				if(isset($vars[96][$mes]))
			{
				$valaten =  nvl($vars[96][$mes],2);
				$lineaAtencion[] = number_format($valaten,2);
				$totAtencionCen+= $valaten;
				$counmeslineaAtencion = $counmeslineaAtencion+1;
			}else
				$lineaAtencion[] = "";
		
		}
			
	
		
		$totProyEjecutadosCen = $totProyEjecutadosCen/count($fechas);
		$lineaProyEjecutadosprom[] = number_format($totProyEjecutadosCen,2).'%';
		$totProyEjecutados+=$totProyEjecutadosCen;		
		
				
		$totlineaCantCasosCen = $totlineaCantCasosCen;
		$lineaCantCasosprom[] = number_format($totlineaCantCasosCen,0);
		$totCantCasos+=$totlineaCantCasosCen;		
				
		$totlineaCalSoftCen = $totlineaCalSoftCen/count($fechas);
		$lineaCalSoftprom[] = number_format($totlineaCalSoftCen,2);
		$totCalSoft+=$totlineaCalSoftCen;				
		
	 
		$totDisponibilidadCen=$totDisponibilidadCen/$counmeslineaDisponibilidad;
		$lineaDisponibilidadprom[] = number_format($totDisponibilidadCen,2);			
		$TotDisponibilidadCen+=$totDisponibilidadCen;
		 
		 
		$totAtencionCen=$totAtencionCen/$counmeslineaAtencion;
		$lineaAtencionprom[] = number_format($totAtencionCen,2);			
		$TotAtencion+=$totAtencionCen;
		 
				 
		/*** VALOR ULTIMO MES ***/
		$lineaProyEjecutadosprom[] = number_format(((nvl($vars[92][$mes],0)/(nvl($vars[13][$mes],0)+ nvl($vars[14][$mes],2)))*100),2)."%";
		
		$lineaCantCasosprom[] = number_format($vars[93][$mes],2);		
		$lineaCalSoftprom[] = number_format($vars[94][$mes],2);
		$lineaDisponibilidadprom[]  = number_format($vars[95][$mes],2);
		$lineaAtencionprom[]  = number_format($vars[96][$mes],2);				
		/*** HASTA AQUI VALOR ULTIMO MES ***/
		 
		 
		 
	}
	
		$lineaProyEjecutados[]  = number_format($totProyEjecutados,2).'%';
		$lineaCantCasos[]  = number_format($totCantCasos,2); 
		$lineaCalSoft[] = number_format($totCalSoft/count($centros),2);		
		$lineaDisponibilidad[] = number_format($TotDisponibilidadCen,2);	
		$lineaAtencion[] = number_format($TotAtencion,2);



		$lineaProyEjecutadosprom[]  = number_format($totProyEjecutados,2).'%';
		$lineaCantCasosprom[]  = number_format($totCantCasos,2); 
		$lineaCalSoftprom[] = number_format($totCalSoft/count($centros),2);		
		$lineaDisponibilidadprom[] = number_format($TotDisponibilidadCen,2);	
		$lineaAtencionprom[] = number_format($TotAtencion,2);


#########################################	
#MOSTRAMOS LA INFORMACIÓN DE FINANCIEROS#
#########################################

if($html)
		imprimirLinea($titulosGenerales, "#b2d2e1", $estilosTitulosRecoleccion);
	else
	{
		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosGenerales, array(1=>"azul_izq"));
		$fila++;$columna=0;
	}


if($html){
	if ($tipo_info==1){
	
	imprimirLinea($lineaProyEjecutados,"",$estilos);
	
	}
	else{		
		imprimirLinea($lineaProyEjecutadosprom,"",$estilos);
	}
}else{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaProyEjecutados, array(1=>"txt_izq"));	
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
		imprimirLinea($lineaCantCasos,"",$estilos);	
	}
	else{
		imprimirLinea($lineaCantCasosprom,"",$estilos);	
	}
}
else{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaCantCasos, array(1=>"txt_izq"));
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
		imprimirLinea($lineaCalSoft,"",$estilos);
		imprimirLinea($lineaDisponibilidad,"",$estilos);
		imprimirLinea($lineaAtencion,"",$estilos);
	

	}else {
		imprimirLinea($lineaCalSoftprom,"",$estilos);
		imprimirLinea($lineaDisponibilidadprom,"",$estilos);
		imprimirLinea($lineaAtencionprom,"",$estilos);		
	
	}
}else{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaCalSoft, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaDisponibilidad, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaAtencion, array(1=>"txt_izq"));
	
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
