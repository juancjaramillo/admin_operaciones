<?
// error_reporting(E_ALL);
// ini_set("display_errors", 1);
// if($_SERVER["REMOTE_ADDR"] != "186.30.42.202")
// die("un momento por favor estamos ajustando los indicadores de acuerdo a reunione de gerencia del 23 de mayo");

// echo "<pre>";
// print_r($_POST);
// print_r($_GET);
// echo "</pre>"; 

include("../application.php");
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

$titulo1 = $db->sql_row("SELECT upper(nombre||' : '||informe) as inf FROM informes i LEFT JOIN categorias_informes c ON c.id=i.id_categoria_informe WHERE i.id=".str_replace(".php","",simple_me($ME)));

if($html)
{
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/info/templates/fechas_form_46.php");
	tablita_titulos($titulo1["inf"],$inicio." / ".$final);
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

$fechas = sacarMeses($inicio, $final);

$titulos = array();
$titulosRecoleccion = array("RECOLECCIÓN");
$titulosBarrido = array("BARRIDO");
$titulosGenerales = array("GENERALES");
$titulosComerciales = array("COMERCIALES");
$titulosIndFinan = array("INDICADORES FINANCIEROS");
$titMargenes = array("<b>MARGENES</b>");
$titRentabilidad = array("<b>RENTABILIDAD</b>");
$titLiquidez = array("<b>LIQUIDEZ</b>");
$titEndeudamiento = array("<b>ENDEUDAMIENTO</b>");
$titCobertura = array("<b>COBERTURA DE INTERESES Y SERV. DE DEUDA</b>");


$estilosTitulosRecoleccion = array(1=>" height='30'  class='azul_osc_14'");
$estilos = array(1=>"align='left'  class='azul_osc_12'");
$i=2;

$qidCentro = $db->sql_query("SELECT id_centro, centro FROM personas_centros LEFT JOIN centros ON centros.id=personas_centros.id_centro WHERE id_persona='".$user["id"]."' ORDER BY id_centro");
while($queryCen = $db->sql_fetchrow($qidCentro)){
	$centros[$queryCen["id_centro"]] = $queryCen["centro"];
	$titulos[] = $queryCen["centro"];
	if ($tipo_info==1){
		foreach($fechas as $mes)
		{
			$titulosRecoleccion[] = $titulosBarrido[] = $titulosGenerales[] = $titulosComerciales[] = $titulosIndFinan[] = ucfirst(strftime("%b.%Y",strtotime($mes."-01")));
			$titMargenes[] = $titRentabilidad[] = $titLiquidez[] = $titEndeudamiento[] = $titCobertura[] = "";
			$estilosTitulosRecoleccion[$i] = " class='azul_osc_14'";
			$estilos[$i] = "class='azul_osc_12'";
			$i++;
		}
	}
	else {
		$titulosRecoleccion[]=$titulosBarrido[]=$titulosGenerales[]=$titulosComerciales[] = $titulosIndFinan[] = $queryCen["centro"];
		$titMargenes[] = $titRentabilidad[] = $titLiquidez[] = $titEndeudamiento[] = $titCobertura[] = "";
		$estilosTitulosRecoleccion[] = " class='azul_osc_14'";
		$estilos[] = "class='azul_osc_12'";
	}
}

$titulosRecoleccion[]=$titulosBarrido[]=$titulosGenerales[]=$titulosComerciales[] = $titulosIndFinan[] = "PROMEDIOS CONSOLIDADOS";
$titMargenes[] = $titRentabilidad[] = $titLiquidez[] = $titEndeudamiento[] = $titCobertura[] = "";
$estilosTitulosRecoleccion[] = " class='azul_osc_14'";
$estilos[] = "class='azul_osc_12'";

if ($tipo_info==1){
	$colspan = (count($fechas) * count($centros))+2;
	if($html)
	{
		$titulos[] = "Consolidados Promedios";
		$anchotabla = (120*(count($titulosRecoleccion)-2))+430;
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
		imprimirLinea($titulosRecoleccion, "#b2d2e1", $estilosTitulosRecoleccion);
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

		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosRecoleccion, array(1=>"azul_izq"));	
		$fila++;$columna=0;
	}	
}
else {
	$colspan = count($centros)+2;
	if($html)
	{
		$titulos[] = "Consolidado Total";
		$anchotabla = (120*(count($titulosRecoleccion)-2))+430;
		if ($anchotabla<800) $anchotabla=800;
		echo '<table width="'.$anchotabla.'" border=1 bordercolor="#7fa840" align="center">';
	
		echo '<tr><td  height="30" colspan="'.$colspan.'" align="left" bgcolor="#b2d2e1" class="azul_osc_14">INDICADORES OPERACIONALES</td></tr>';
		imprimirLinea($titulosRecoleccion, "#b2d2e1", $estilosTitulosRecoleccion);
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

		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosRecoleccion, array(1=>"azul_izq"));	
		$fila++;$columna=0;
	}
}

$tiposVehiculos = $tiposVehiculosXCentro = $capacidad = $recorrido = $combustible = $tonTipo = $viajes = $ton = $per = $kmsR = array();

$lineaTon = array("Toneladas recogidas");
$lineaTonprom = array("Toneladas recogidas");
$lineaCosTon = array("Costo/Ton Recogida y transportada");
$lineaCosprom = array("Costo/Ton Recogida y transportada");
$lineaPers = array("Toneladas recogidas por  tripulacion");
$lineaLl = array("Costo de llantas por km recorrido ($/Km)");
$tonTOTAL = $cosTonTOTAL = $persTOTAL = $costoLlanTOTAL = $lineaInoperaTOTAL = 0;

//Toneladas recogidas
consultatons($fila, $columna, $html, implode(",",array_keys($centros)), "rec", $inicio, $final, $estilos, "asc", $toneladas);
$lineaTon = array("Toneladas");
$cosTonTOTAL = 0;
foreach($centros as $idCentro => $da)
{
	$promCos = $numMes1 = $numMes = $totalCentro = 	$totalCentroCos = 0;
	
	foreach($fechas as $mes)
	{
		$lineaTon[] = number_format(nvl($toneladas[$idCentro][$mes],0),2);
		$totalCentro+=nvl($toneladas[$idCentro][$mes],0);
		//Consultamos el costo del mes		
		$qidCTRP = $db->sql_row("SELECT sum(c.valor) as val FROM costos c LEFT JOIN servicios s ON s.id=c.id_servicio 
		WHERE id_centro='".$idCentro."' AND esquema='rec' AND id_variable_informe = 1 AND fecha = '".$mes."'");
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
	$lineaTonprom[] = number_format($promCen,2);
	$promCos = $totalCentroCos/$numMes1;
	$lineaCosprom[] = number_format($promCos,0);
	
	$tonTOTAL += $promCen;
	if($promCos!=0) {
		$cosTonTOTAL += $promCos;
		$numCentro++;
		//echo "Promedio Costo Total->".$cosTonTOTAL;
	}
}

$lineaTon[] = number_format($tonTOTAL/count($centros),2);
$lineaCosTon[] = number_format($cosTonTOTAL/$numCentro,0);
$lineaLl[] = number_format($costoLlanTOTAL/count($centros),2);

$lineaTonprom[] = number_format($tonTOTAL,2);
$lineaCosprom[] = number_format($cosTonTOTAL/$numCentro,2);
$lineaLl[] = number_format($costoLlanTOTAL/count($centros),2);

if($html)
{
	if ($tipo_info==1){
		imprimirLinea($lineaTon,"",$estilos);
		imprimirLinea($lineaCosTon,"",$estilos);
	}
	else{
		imprimirLinea($lineaTonprom,"",$estilos);
		imprimirLinea($lineaCosprom,"",$estilos);
	}
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaTon, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaCosTon, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaPers, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaLl, array(1=>"txt_izq"));
}

//Calculamos la inoperatividad 
inoperatividad($fila, $columna, $html, implode(",",array_keys($centros)), "rec", $inicio, $final, $estilos, "asc",$diasinop,$vxdinop);
$lineaInopera = array("Inoperatividad Flota (Días Inoperativoos)");
$lineaInoperaProm = array("Inoperatividad Flota (Días Inoperativos)");
$InoTOTAL = 0;
foreach($centros as $idCentro => $da)
{
	$promInoCen = $numMes =  $totalInoCentro = 0;
	foreach($fechas as $mes)
			{
					$lineaInopera[] = number_format(nvl($diasinop[$idCentro][$mes],0),0);
					$totalInoCentro+=nvl($diasinop[$idCentro][$mes],0);
					$numMes++;	
			}
	$promInoCen = $totalInoCentro/$numMes;
	$lineaInoperaProm[] = number_format($promInoCen,0);
	$InoTOTAL += $promInoCen;
	$numCentro++;
}
$lineaInopera[] = number_format($InoTOTAL/count($centros),0);
$lineaInoperaProm[] = number_format($InoTOTAL/count($centros),0);

//Calcula cuantas toneladas recoje un vehiculo en el mes separando los turnos para sacar le promedio que sería igual al numero de tripulaciones
// y tambien hace el calculo de cuantos viajes por turno se realizan para saber el promedio de viajes por turno por día.
consuviajtontrip(&$fila, &$columna, $html, implode(",",array_keys($centros)), "rec", $inicio, $final, $estilos, $orden, $tontrip, $viajtur);
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

}

$lineaTontrip[] = number_format($PromTOTAL/count($centros),2);
$lineaviajestur[] = number_format($PromTOTAL1/count($centros),2);
$lineaTontripprom[] = number_format($PromTOTAL/count($centros),2);
$lineaviajesturprom[] = number_format($PromTOTAL1/count($centros),2);

if($html)
{
	if ($tipo_info==1){
		imprimirLinea($lineaTontrip,"",$estilos);
		imprimirLinea($lineaviajestur,"",$estilos);
	}
	else {
		imprimirLinea($lineaTontripprom,"",$estilos);
		imprimirLinea($lineaviajesturprom,"",$estilos);
	}
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaTontrip, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaviajestur, array(1=>"txt_izq"));
}


//Eficiencia Consumo de combustible Kilometros por Galon
consuKmgl($fila, $columna, $html, implode(",",array_keys($centros)), "rec", $inicio, $final, $estilos, "asc", $kmsgl, $tpvhs);
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
	}
	
	$linea[] = number_format(($total/$numCen),2);
	$lineaprom[] = number_format(($total/$numCen),2);

	if($html){
		if ($tipo_info==1){
			imprimirLinea($linea,"",$estilos);}
		else {
			imprimirLinea($lineaprom,"",$estilos);}
	}
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
}	
unset($tpvhs);

//Eficiencia vehiculos
$lineaflot = array('Eficiencia % Promedio Flota');
consuEficiencia($fila, $columna, $html,implode(",",array_keys($centros)), "rec", $inicio, $final, $estilos, "asc", $eficiencia, $eficiencia1, $tpvhs,$tpvh);	
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
				$lineaprom[] = number_format($totalCentro/$numMes,1)." / ".number_format($totalCentro1/$numMes,1)."%";
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
	}
	$linea[] = number_format(($total/$numCen),1);
	if (in_array($tpv, $tpvh)) 
		$lineaprom[] = number_format(($total/$numCen),1)." / ".number_format(($total1/$numCen),1)."%";
	else
		$lineaprom[] = number_format(($total/$numCen),1);
		
	$linea1[] = number_format(($total1/$numCen),1)."%";
	$linea1prom[] = number_format(($total1/$numCen),1)."%";
	
	if($html){
		if ($tipo_info==1){
			imprimirLinea($linea,"",$estilos);
			// if (in_array($tpv, $tpvh)) {
				// imprimirlinea($linea1,"",$estilos);
			// }	
		}
		else {
			imprimirLinea($lineaprom,"",$estilos);
			// if (in_array($tpv, $tpvh)) {
				// imprimirLinea($linea1prom,"",$estilos);
			// }
		}
	
	}
	else{
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea1, array(1=>"txt_izq"));}
}	
unset($tpvhs);

//barrido
if($html)
	imprimirLinea($titulosBarrido, "#b2d2e1", $estilosTitulosRecoleccion);
else
{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosBarrido, array(1=>"azul_izq"));
	$fila++;$columna=0;
}


$linea = array("Km barridos");
$linea2 = array("Costo / Km Barrido");
$linea3 = array("Km barridos por operario");
$linea4= array("Bolsas por Operario de Barrido");
$lineaprom = array("Km barridos");
$linea2prom = array("Costo / Km Barrido");
$linea3prom = array("Km barridos por operario");
$linea4prom = array("Bolsas por Operario de Barrido");

$tiposVehiculos = $tiposVehiculosXCentro = $capacidad = $recorrido = $combustible = $tonTipo = $viajes = $ton = $per = array();
$kmTot = $cosKmTOTAL = $kmOpe = $bolOpe = 0;
$idVars = array(2,3,4,5,6);
foreach($centros as $idCentro => $da)
{
	$vars = array();
	$kmTotCen = $cosKmTOTALCen = $kmOpeCen = $bolOpeCen = $counmes = 0;
	foreach($fechas as $mes)
	{
		$qid3 = $db->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c LEFT JOIN servicios s ON s.id=c.id_servicio WHERE id_centro='".$idCentro."' AND esquema='bar' AND id_variable_informe IN (".implode(",",$idVars).") AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
		while($query = $db->sql_fetchrow($qid3))
		{
			$vars[$query["id_variable_informe"]][$mes] = $query["val"];
		}
	
		//preguntar($vars);
		if(isset($vars[2][$mes]))
		{
			$linea[] = number_format($vars[2][$mes],2);
			$kmTotCen+= $vars[2][$mes];
			$counmes = $counmes+1;
		}else
			$linea[] = "";

		if(isset($vars[2][$mes]) && isset($vars[3][$mes]))
		{
			$val = nvl($vars[3][$mes],0)/nvl($vars[2][$mes],0);
			$linea2[]=number_format($val,2);
			$cosKmTOTALCen+=$val;
		}
		else
			$linea2[] = "";

		if(isset($vars[4][$mes]))
		{
			$val = nvl($vars[2][$mes],0)/nvl($vars[6][$mes],1);
			$linea3[] = number_format($val,2);
			$kmOpeCen+= $val;
		}else
			$linea3[] = "";

		if(isset($vars[5][$mes]))
		{
			$linea4[] = number_format($vars[5][$mes],2);
			$bolOpeCen+= $vars[5][$mes];
		}else
			$linea4[] = "";
	}
	
	$kmTotCen = $kmTotCen/$counmes;
	$lineaprom[] = number_format($kmTotCen,2);
	$kmTot+=$kmTotCen;
	
	$cosKmTOTALCen = $cosKmTOTALCen/count($fechas);
	$linea2prom[] = number_format($cosKmTOTALCen,0);
	$cosKmTOTAL+=$cosKmTOTALCen;

	$kmOpeCen = $kmOpeCen/count($fechas);
	$linea3prom[] = number_format($kmOpeCen,2);
	$kmOpe+=$kmOpeCen;

	$bolOpeCen = $bolOpeCen/count($fechas);
	$linea4prom[] = number_format($bolOpeCen,2);
	$bolOpe+=$bolOpeCen;
}

$linea[] = number_format($kmTot/count($centros),2);
$linea2[] = number_format($cosKmTOTAL/count($centros),0);
$linea3[] = number_format($kmOpe/count($centros),2);
$linea4[] = number_format($bolOpe/count($centros),2);
$lineaprom[] = number_format($kmTot,2);
$linea2prom[] = number_format($cosKmTOTAL/count($centros),0);
$linea3prom[] = number_format($kmOpe/count($centros),2);
$linea4prom[] = number_format($bolOpe/count($centros),2);

if($html)
{
	if ($tipo_info==1){
		imprimirLinea($linea,"", $estilos);
		imprimirLinea($linea2,"", $estilos);
		imprimirLinea($linea3,"", $estilos);
		imprimirLinea($linea4,"", $estilos);
	}
	else{
		imprimirLinea($lineaprom,"", $estilos);
		imprimirLinea($linea2prom,"", $estilos);
		imprimirLinea($linea3prom,"", $estilos);
		imprimirLinea($linea4prom,"", $estilos);
	}
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea2, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea3, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea4, array(1=>"txt_izq"));
}

//Calcula cuantas toneladas recoje un vehiculo en el mes separando los turnos para sacar le promedio que sería igual al numero de tripulaciones
// y tambien hace el calculo de cuantos viajes por turno se realizan para saber el promedio de viajes por turno por día.
unset($lineaTontrip);
unset($lineaviajestur);
unset($lineaTontripprom);
unset($lineaviajesturprom);
$PromTOTAL=$PromTOTAL1=0;

consuviajtontrip(&$fila, &$columna, $html, implode(",",array_keys($centros)), "bar", $inicio, $final, $estilos, $orden, $tontrip, $viajtur);
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

}

$lineaTontrip[] = number_format($PromTOTAL/count($centros),2);
$lineaviajestur[] = number_format($PromTOTAL1/count($centros),2);
$lineaTontripprom[] = number_format($PromTOTAL/count($centros),2);
$lineaviajesturprom[] = number_format($PromTOTAL1/count($centros),2);

if($html)
{
	if ($tipo_info==1){
		imprimirLinea($lineaTontrip,"",$estilos);
		imprimirLinea($lineaviajestur,"",$estilos);
	}
	else{
		imprimirLinea($lineaTontripprom,"",$estilos);
		imprimirLinea($lineaviajesturprom,"",$estilos);
	}
}
else
{
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaTontrip, array(1=>"txt_izq"));
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaviajestur, array(1=>"txt_izq"));
}

//Eficiencia Consumo de combustible Kilometros por Galon
consuKmgl($fila, $columna, $html, implode(",",array_keys($centros)), "bar", $inicio, $final, $estilos, "desc", $kmsgl, $tpvhs);
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
	}
	$linea[] = number_format(($total/$numCen),2);
	$lineaprom[] = number_format(($total/$numCen),2);

	if($html)
		if ($tipo_info==1){
			imprimirLinea($linea,"",$estilos);
		}
		else{
			imprimirLinea($lineaprom,"",$estilos);
		}
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
}	
unset($tpvhs);

//Eficiencia vehiculos
unset($eficiencia);
unset ($eficiencia1);
consuEficiencia($fila, $columna, $html,implode(",",array_keys($centros)), "bar", $inicio, $final, $estilos, "desc", $eficiencia, $eficiencia1, $tpvhs,$tpvh);	
foreach($tpvhs as $tpv => $tp)
{
	$linea = array($tp);
	$lineaprom = array($tp);
	$efi= substr_replace("Ton/viaje", "Eficiencia %", $tp);	
	$linea1 = array($efi);
	$linea1prom = array($efi);
	$numCen = $numCen = $total = $total1 = 0;
	
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
				$lineaprom[] = number_format($totalCentro/$numMes,1)." / ".number_format($totalCentro1/$numMes,1)."%";
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
	}
	$linea[] = number_format(($total/$numCen),2);
	if (in_array($tpv, $tpvh)) 
		$lineaprom[] = number_format(($total/$numCen),1)." / ".number_format(($total1/$numCen),1)."%";
	else
		$lineaprom[] = number_format(($total/$numCen),1);
		
	$linea1[] = number_format(($total1/$numCen),2)."%";
	$linea1prom[] = number_format(($total1/$numCen),2)."%";

	if($html){
		if ($tipo_info==1){
			imprimirLinea($linea,"",$estilos);
			// if (in_array($tpv, $tpvh)) imprimirLinea($linea1,"",$estilos);
		}
		else{
			imprimirLinea($lineaprom,"",$estilos);
			// if (in_array($tpv, $tpvh)) imprimirLinea($linea1prom,"",$estilos);
		}
	}
	else{
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea1, array(1=>"txt_izq"));
	}
}	
unset($tpvhs);

//JFMC Para que solo los vean los Directores y Gerentes
if ($nivel==1 || $nivel==13 || $nivel==7 || $nivel==8 || $nivel==11 || $nivel==12 || $nivel==15){
	//generales
	if($html)
		imprimirLinea($titulosGenerales, "#b2d2e1", $estilosTitulosRecoleccion);
	else
	{
		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosGenerales, array(1=>"azul_izq"));
		$fila++;$columna=0;
	}

	$linea0 = array("Costos Llantas / Ventas Totales");
	$linea = array("Costos Mtto / Ventas Totales");
	$linea2 = array("Gastos Adtivos / Ventas Totales");
	$linea3 = array("N° trabajadores / 1.000 usuarios");
	$linea4 = array("N° total de empleados");
	$linea5 = array("N° total de Usuarios");
	$linea6 = array("Mano Obra / Ventas Totales");
	$lineaFact = array("Facturación Total");
	$lineaRecau = array("Indice de Recaudo Total");
	$lineaRC = array("Indice de Recaudo Corriente");
	$lineaUVD = array("Usuarios Vinculados Vs Desvinculados");
	$linea0prom = array("Costos Llantas / Ventas Totales");
	$lineaprom = array("Costos Mtto / Ventas Totales");
	$linea2prom = array("Gastos Adtivos / Ventas Totales");
	$linea3prom = array("N° trabajadores / 1.000 usuarios");
	$linea4prom = array("N° total de empleados");
	$linea5prom = array("N° total de usuarios");
	$linea6prom = array("Mano Obra / Ventas Totales");
	$lineaFactprom = array("Facturación Total");
	$lineaRecauprom = array("Indice de Recaudo Total");
	$lineaRCprom = array("Indice de Recaudo Corriente");
	$lineaUVDprom = array("Usuarios Vinculados Vs Desvinculados");
	
	$otrasLineas = $nombreOtras = $otrasLineasDos = $nombreOtrasDos = array();
	$otrasLineasprom = array();

	//ind comerciales
	$qidOV = $db->sql_query("SELECT * FROM variables_informes WHERE id=13 or id=14 or id=15 or id=17 or (id >=20 AND id<=26) or id=37 or id= 38 ORDER BY orden");
	while($queryOV = $db->sql_fetchrow($qidOV))
	{
		$nombreOtras[$queryOV["id"]] = $queryOV["variable"];
	}

	//ind financieros
	$qidOV = $db->sql_query("SELECT * FROM variables_informes WHERE id >=27 AND id<=36 ORDER BY id");
	while($queryOV = $db->sql_fetchrow($qidOV))
	{
		$nombreOtrasDos[$queryOV["id"]] = $queryOV["variable"];
	}

	$cosMtto = $gastos = $numTa = $totEmp = $totFact = $totRecau = $totRC = $totUVD = $cosMO = $totindllanta =0;
	foreach($centros as $idCentro => $da)
	{
		$cosMttoCen = $gastosCen = $numTaCen = $totEmpCen = $totFactCen = $totRecauCen = $totRCCen = $totUVDCen = $cosMOCen = $totindllantacen= 0;
		$vars =  array();
		$totfec = 0;
		foreach($fechas as $mes)
		{
			$qid3 = $db->sql_query("SELECT sum(c.valor) as val, id_variable_informe FROM costos c 
			LEFT JOIN servicios s ON s.id=c.id_servicio WHERE id_centro='".$idCentro."'  
			AND id_variable_informe >= 9 AND fecha = '".$mes."' 	GROUP BY id_variable_informe");
			while($query = $db->sql_fetchrow($qid3))
			{
				$vars[$query["id_variable_informe"]][$mes] = $query["val"];
			}
			
			if(isset($vars[9][$mes]))
			{
				#$val=nvl($vars[9][$mes],0)/(nvl($vars[13][$mes],0) + nvl($vars[14][$mes],0) + nvl($vars[15][$mes],0))*100;
				$val=nvl($vars[9][$mes],0)/(nvl($vars[13][$mes],0) + nvl($vars[14][$mes],0))*100;
				$linea6[] = number_format($val,2).'%';
				$cosMOCen+=$val;
			}
			else
			{
				$linea6[] = '';
			}
			
			if(isset($vars[10][$mes])  && isset($vars[13][$mes]) && isset($vars[14][$mes]) && isset($vars[15][$mes]))
			{
				#$val=nvl($vars[10][$mes],0)/nvl($vars[11][$mes],0)*100;
				$val=nvl($vars[10][$mes],0)/( nvl($vars[13][$mes],0)+ nvl($vars[14][$mes],0) )*100;
				$linea[] = number_format($val,2).'%';
				$cosMttoCen+=$val;
			}else
				$linea[] = "";

			if(isset($vars[12][$mes]) && isset($vars[13][$mes]) && isset($vars[14][$mes]) && isset($vars[15][$mes]))
			{
				$val=nvl($vars[12][$mes],0)/(nvl($vars[13][$mes],0)+nvl($vars[14][$mes],0)+nvl($vars[15][$mes],0))*100;
				$linea2[] = number_format($val,2).'%';
				$gastosCen+=$val;
			}
			else
				$linea2[] = "";

			if(isset($vars[16][$mes]) && isset($vars[17][$mes]))
			{
				$val=(nvl($vars[16][$mes],0)/nvl($vars[17][$mes],0))*1000;
				$linea3[] = number_format($val,2);
				$numTaCen+=$val;
			}
			else
				$linea3[] = "";

			if(isset($vars[16][$mes]))
			{
				$val = $vars[16][$mes];
				$linea4[] = number_format($val,0);
				$totEmpCen+= $val;
			}else
				$linea4[] = "";
		
			if(isset($vars[39][$mes]))
			{
				$val=nvl($vars[39][$mes],0)/( nvl($vars[13][$mes],0)+ nvl($vars[14][$mes],0) )*100;
				$linea0[] = number_format($val,2)."%";
				$totindllantacen+= $val;
			}else
				$linea0[] = "";
		
			if(isset($vars[17][$mes]))
			{
				$val = $vars[17][$mes];
				$linea5[] = number_format($val,0);
				$totUsuCen+= $val;
			}else
				$linea5[] = "";

			//ind comerciales
			foreach($nombreOtras as $id => $name)
			{
				if(isset($vars[$id][$mes]))
					$otrasLineas[$id][$idCentro][$mes] = $vars[$id][$mes];
				else
					$otrasLineas[$id][$idCentro][$mes] = "";
			}


			//ind financieros
			foreach($nombreOtrasDos as $id => $name)
			{
				if(isset($vars[$id][$mes])) 
						$otrasLineasDos[$id][$idCentro][$mes] = $vars[$id][$mes];
				else
						$otrasLineasDos[$id][$idCentro][$mes] = "";
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

			@$valUVD = nvl($vars[26][$mes],0)+nvl($vars[38][$mes],0);
			$lineaUVD[] = number_format($valUVD,0);
			$totUVDCen+=$valUVD;
			
		}
	
		if ($cosMOCen!=0)
			$linea6prom[] = number_format($cosMOCen/count($lineaRecaucount),2)."%";
		else 
			$linea6prom[] = '';
		$cosMO+=$cosMOCen/count($lineaRecaucount);
		
		$cosMttoCen = $cosMttoCen/count($fechas);
		if ($cosMttoCen!=0)
			$lineaprom[] = number_format($cosMttoCen,2)."%";
		$cosMtto+=$cosMttoCen;
		
		$gastosCen = $gastosCen/count($fechas);
		$linea2prom[] = number_format($gastosCen,2)."%";
		$gastos+=$gastosCen;

		$numTaCen = $numTaCen/count($fechas);
		$linea3prom[] = number_format($numTaCen,2);
		$numTa+=$numTaCen;

		$totEmpCen = $totEmpCen/count($fechas);
		$linea4prom[] = number_format($totEmpCen,0);
		$totEmp+=$totEmpCen;

		$totindllantacen = $totindllantacen/count($fechas);
		$linea0prom[] = number_format($totindllantacen,2).'%';
		$totindllanta+=$totindllantacen;

		$totUsuCen = $totUsuCen/count($fechas);
		$linea5prom[] = number_format($totUsuCen,0);
		$totUsu+=$totUsuCen;

		$totFactCen = $totFactCen/count($lineaRecaucount);
		$lineaFactprom1[] = $totFactCen;
		$lineaFactprom[] = number_format($totFactCen,0);
		$totFact+=$totFactCen;

		$totRecauCen = $totRecauCen/count($lineaRecaucount);
		$lineaRecauprom1[]  = $totRecauCen;
		$lineaRecauprom[]  = number_format($totRecauCen,2).'%';
		$totRecau+=$totRecauCen;

		$totRCCen = $totRCCen/count($lineaRecaucount);
		$lineaRCprom[] = number_format($totRCCen,2)."%";
		$totRC+=$totRCCen;

		$totUVDCen = $totUVDCen/count($fechas);
		$lineaUVDprom[] = number_format($totUVDCen,0);
		$totUVD+=$totUVDCen;
		unset($lineaRecaucount);
	}

	$linea0[] = number_format($totindllanta/count($centros),2).'%';
	$linea[] = number_format($cosMtto/count($centros),2).'%';
	$linea2[] = number_format($gastos/count($centros),2).'%';
	$linea3[] = number_format($numTa/count($centros),2);
	$linea4[] = number_format($totEmp/count($centros),0);
	$linea5[] = number_format($totUsu/count($centros),0);
	$linea6[] = number_format($cosMO/count($centros),2);
	$lineaFact[] = number_format($totFact/count($centros),0);
	$lineaRecau[]  = number_format($totRecau/count($centros),0)."%";
	$lineaRC[] = number_format($totRC/count($centros),0)."%";
	$lineaUVD[] = number_format($totUVD/count($centros),0);
	
	$linea0prom[] = number_format($totindllanta/count($centros),2).'%';
	$lineaprom[] = number_format($cosMtto/count($centros),2).'%';
	$linea2prom[] = number_format($gastos/count($centros),2).'%';
	$linea3prom[] = number_format($numTa/count($centros),2);
	$linea4prom[] = number_format($totEmp,0);
	$linea5prom[] = number_format($totUsu,0);
	if ($cosMO!=0)
		$linea6prom[] = number_format($cosMO/count($centros),2).'%';
	else
		$linea6prom[] ='0%';
		
	$lineaFactprom[] = number_format(array_sum($lineaFactprom1),0);
	$lineaRecauprom[]  = number_format(array_sum($lineaRecauprom1)/count($lineaRecauprom1),2).'%';
	$lineaRCprom[] = number_format($totRC/count($centros),2)."%";
	$lineaUVDprom[] = number_format($totUVD/count($centros),0);

	if($html)
	{
		if ($tipo_info==1){
			imprimirLinea($linea,"", $estilos);
			imprimirLinea($linea2,"", $estilos);
			imprimirLinea($linea6,"", $estilos);
			imprimirLinea($linea3,"", $estilos);
			imprimirLinea($linea4,"", $estilos);
			imprimirLinea($linea0,"", $estilos);
			imprimirLinea($lineaInopera,"",$estilos);
			imprimirLinea($lineaLl,"",$estilos);
		}
		else{
			imprimirLinea($lineaprom,"", $estilos);
			imprimirLinea($linea2prom,"", $estilos);
			imprimirLinea($linea6prom,"", $estilos);
			imprimirLinea($linea3prom,"", $estilos);
			imprimirLinea($linea4prom,"", $estilos);
			imprimirLinea($linea0prom,"", $estilos);
			imprimirLinea($lineaInoperaProm,"",$estilos);
			imprimirLinea($lineaLl,"",$estilos);
		}
	}
	else
	{
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea2, array(1=>"txt_izq"));
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea6, array(1=>"txt_izq"));
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea3, array(1=>"txt_izq"));
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea4, array(1=>"txt_izq"));
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea0, array(1=>"txt_izq"));

	}


	if($html)
		imprimirLinea($titulosComerciales, "#b2d2e1", $estilosTitulosRecoleccion);
	else
	{
		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $titulosComerciales, array(1=>"azul_izq"));
		$fila++;$columna=0;
	}
	
	//facturacion
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
	
	imprimirOtras($tipo_info,$fila, $columna, $html, $centros, $fechas, $nombreOtras, $otrasLineas, $estilos, array(37));
	if($html){
		if ($tipo_info==1){
			imprimirLinea($lineaRecau,"", $estilos); 
		}
		else{
			imprimirLinea($lineaRecauprom,"", $estilos);
		}
	}
	else {
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaRecau, array(1=>"txt_izq"));
	}
	imprimirOtras($tipo_info,$fila, $columna, $html, $centros, $fechas, $nombreOtras, $otrasLineas, $estilos, array(19));
	if($html){
		if ($tipo_info==1){
			imprimirLinea($lineaRC,"", $estilos); 
		}
		else{
			imprimirLinea($lineaRCprom,"", $estilos);
		}
	}
	else {
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaRC, array(1=>"txt_izq"));
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


	if($html) imprimirLinea($titMargenes,"", $estilos); else imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $titMargenes, array(1=>"txt_izq")); 
	imprimirOtras($tipo_info,$fila, $columna, $html, $centros, $fechas, $nombreOtrasDos, $otrasLineasDos, $estilos, array(27,28),"%");
	if($html) imprimirLinea($titRentabilidad,"", $estilos); else imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $titRentabilidad, array(1=>"txt_izq")); 
	imprimirOtras($tipo_info,$fila, $columna, $html, $centros, $fechas, $nombreOtrasDos, $otrasLineasDos, $estilos, array(29,30),"%");
	if($html) imprimirLinea($titLiquidez,"", $estilos); else imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $titLiquidez, array(1=>"txt_izq")); 
	imprimirOtras($tipo_info,$fila, $columna, $html, $centros, $fechas, $nombreOtrasDos, $otrasLineasDos, $estilos, array(31,32));
	if($html) imprimirLinea($titEndeudamiento,"", $estilos); else imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $titEndeudamiento, array(1=>"txt_izq")); 
	imprimirOtras($tipo_info,$fila, $columna, $html, $centros, $fechas, $nombreOtrasDos, $otrasLineasDos, $estilos, array(33,34),"%");
	if($html) imprimirLinea($titCobertura,"", $estilos); else imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $titCobertura, array(1=>"txt_izq")); 
	imprimirOtras($tipo_info,$fila, $columna, $html, $centros, $fechas, $nombreOtrasDos, $otrasLineasDos, $estilos, array(35,36));
}


/*funciones*/
function consultatons(&$fila, &$columna, $html, $idCentro, $servicio, $inicio, $final, $estilos, $orden, &$toneladas)
{
	global $db, $CFG, $ME;
	
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
				WHERE tvh.id in (1,2,4,8,10,12,14,15,19,21,27) and s.inf_indope='$servicio' and inicio::date >= '$inicio' AND inicio::date<='$final' and c.id in ($idCentro)
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
				$qidpesos = $db->sql_query($strQuery);
	
	while($pesos= $db->sql_fetchrow($qidpesos))
	{
		$toneladas[$pesos["id"]][$pesos["fecha"]]+=$pesos["toneladas"];
		$i++;
	}
}

function consuKmgl(&$fila, &$columna, $html, $idCentro, $servicio, $inicio, $final, $estilos, $orden, &$kmsgl, &$tpvhs)
{
	global $db, $CFG, $ME;
	global $workbook;
	global $worksheet;
	if ($servicio=="rec") $tpvh = "2,8,14,15";
	if ($servicio=="bar") $tpvh = "1,19,4,29,21"; #se quito 11 roll/off 04-09-14
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
				WHERE tvh.id in ($tpvh) AND inicio::date >= '$inicio' AND inicio::date<='$final' and c.id in ($idCentro)
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
		$qidkmsgls = $db->sql_query($sqlkmsgls);
		$tpvh= '';
		
 		while($kmgls = $db->sql_fetchrow($qidkmsgls))
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

function consuEficiencia(&$fila, &$columna, $html, $idCentro, $servicio, $inicio, $final, $estilos, $orden, &$eficiencia, &$eficiencia1, &$tpvhs, &$tpvh)
{
	global $db, $CFG, $ME;
	global $workbook;
	global $worksheet;	
	if ($servicio=="rec") $tpvh = "2,8,14,15";
	if ($servicio=="bar") $tpvh = "1,19,4,29,21";
	$tpvh2=$tpvh;
	$tpvh = explode(",", $tpvh);
	
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
					avg(pesos.capacidad)as capacidad,avg(eficiencia) as eficiencia, avg(pesos.pes_viaje) as pesviaje,sum(pesos.viajedes) as viajes 
					FROM  rec.movimientos mov LEFT JOIN
						(select p.id,p.peso_inicial,p.peso_final,p.peso_total, v.id_tipo_vehiculo,tv.tipo,tv.orden_info,tv.capacidad,min(id_movimiento) as id_movimiento,
						(p.peso_total/tv.capacidad)*100 as eficiencia, avg(p.peso_total) as pes_viaje, 1 as viajedes
						from rec.pesos p
						LEFT JOIN vehiculos v ON p.id_vehiculo=v.id
						LEFT JOIN rec.movimientos_pesos mp ON p.id=mp.id_peso
						LEFT JOIN tipos_vehiculos tv ON v.id_tipo_vehiculo=tv.id
						WHERE tv.id in (1,2,4,8,10,11,12,14,15,19,21,27)
						GROUP BY p.id,p.peso_inicial,p.peso_final,p.peso_total,v.id_tipo_vehiculo,v.id_centro,tv.tipo,tv.orden_info,tv.capacidad,tv.capacidad) as pesos
						ON mov.id=pesos.id_movimiento
					LEFT JOIN vehiculos vh ON mov.id_vehiculo=vh.id
					LEFT JOIN micros i ON i.id=mov.id_micro
					LEFT JOIN servicios s ON s.id = i.id_servicio
					LEFT JOIN centros c ON vh.id_centro=c.id
					WHERE pesos.id_tipo_vehiculo in ($tpvh2) and inicio::date >= '$inicio' AND inicio::date<='$final' and c.id in ($idCentro)
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
					c.id,c.centro,pesos.id_tipo_vehiculo,pesos.tipo,pesos.orden_info
				order by pesos.orden_info $orden,2,1 ";
		$qidefic = $db->sql_query($sqlefic);
 		while($efic = $db->sql_fetchrow($qidefic))
		{		
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

function consuviajtontrip(&$fila, &$columna, $html, $idCentro, $servicio, $inicio, $final, $estilos, $orden, &$tontrip, &$viajtur)
{
	global $db, $CFG, $ME;
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
				WHERE tv.id in (1,2,4,8,10,11,12,14,15,19,21,27)
				GROUP BY p.id,p.peso_inicial,p.peso_final,p.peso_total,v.id_tipo_vehiculo,v.id_centro,tv.tipo,tv.capacidad,tv.capacidad) as pesos
				ON mov.id=pesos.id_movimiento
			LEFT JOIN vehiculos vh ON mov.id_vehiculo=vh.id
			LEFT JOIN micros i ON i.id=mov.id_micro
			LEFT JOIN servicios s ON s.id = i.id_servicio
			LEFT JOIN centros c ON vh.id_centro=c.id
			LEFT JOIN tipos_vehiculos tvh ON vh.id_tipo_vehiculo=tvh.id
			LEFT JOIN turnos tur ON tur.id=mov.id_turno
			WHERE tvh.id in (1,2,4,8,10,11,12,14,15,19,21,27) and s.inf_indope='$servicio' and inicio::date >= '$inicio' AND inicio::date<='$final' and c.id in ($idCentro)
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
		$qidefic = $db->sql_query($sqlefic);
 		while($efic = $db->sql_fetchrow($qidefic))
		{		
			$tontrip[$efic["id"]][$efic["fecha"]]+=number_format($efic["tontripula"],2);
			$viajtur[$efic["id"]][$efic["fecha"]]+=number_format($efic["promviajes"],2);
			$i++;
		} 
}

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

			if ((key($otrasLineas))==13 && ($lineaprom[0]!='Costo Facturación por usuario' && $lineaprom[0]!='Aforo a Grandes Generadores')) 		
					 if ($key==13 or $key==14 or $key==15 or $key==37 or $key==21 or $key==22 or $key==23 or $key==24 or $key==25)  $lineaprom[] = number_format($total,0).$signoAd;
					 else $lineaprom[] = number_format($total,2).$signoAd;
			else
					if ($key==13 or $key==14 or $key==15 or $key==37 or $key==21 or $key==22 or $key==23 or $key==24 or $key==25) $lineaprom[] = number_format($total/count($centros),0).$signoAd;
					else $lineaprom[] = number_format($total/count($centros),2).$signoAd;
			//$lineaprom[] = number_format($total/count($centros),2).$signoAd;

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

function inoperatividad(&$fila, &$columna, $html, $idCentro, $servicio, $inicio, $final, $estilos, $orden, &$diasinop, &$vxdinop)
{
	global $db, $CFG, $ME;
	global $workbook;
	global $worksheet;	

	$sqlefic = "select id_centro,date_part('year',TB.dia)||'-'||
				CASE WHEN date_part('month',TB.dia)=1 THEN '01'
					WHEN date_part('month',TB.dia)=2 THEN '02'
					WHEN date_part('month',TB.dia)=3 THEN '03'
					WHEN date_part('month',TB.dia)=4 THEN '04'
					WHEN date_part('month',TB.dia)=5 THEN '05'
					WHEN date_part('month',TB.dia)=6 THEN '06'
					WHEN date_part('month',TB.dia)=7 THEN '07'
					WHEN date_part('month',TB.dia)=8 THEN '08'
					WHEN date_part('month',TB.dia)=9 THEN '09'
					WHEN date_part('month',TB.dia)=10 THEN '10'
					WHEN date_part('month',TB.dia)=11 THEN '11'
					WHEN date_part('month',TB.dia)=12 THEN '12'	
				END as fecha, sum(dispon-operaron) as inopera, count(TB.dia) as diasop, sum(dispon-operaron)/count(TB.dia) as vxd
				 from
				(select dia,id,centro,count(id_vehiculo)as operaron from 
					(select dia,id_vehiculo,codigo,fecha,id,centro from
							(SELECT date(m.inicio)as dia,m.id_vehiculo,vh.codigo,date_part('year',m.inicio)||'-'||
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
							END as fecha,c.id,c.centro,vh.id_tipo_vehiculo as tpvehi,tvh.tipo
							FROM rec.movimientos m
							LEFT JOIN vehiculos vh ON m.id_vehiculo=vh.id
							LEFT JOIN centros c ON vh.id_centro=c.id
							LEFT JOIN tipos_vehiculos tvh ON vh.id_tipo_vehiculo=tvh.id
							WHERE tvh.id in (1,2,4,8,11,14,15,19,21,29) AND inicio::date >= '$inicio' AND inicio::date<='$final' and c.id in ($idCentro))operacion
					group by dia,id_vehiculo,codigo,fecha,id,centro) as operativos
					
				where EXTRACT(DOW FROM dia)<>0
				group by dia,id,centro	
				order by 1) as TB
			left join
				(select id_centro,dia,count(codigo)as dispon from
					(select  vh.id,vh.id_centro,vh.codigo,dias.*
					from (select date(inicio)as dia from  rec.movimientos where EXTRACT(DOW FROM inicio)<>0 AND inicio::date >= '$inicio' AND inicio::date<='$final'
						group by 1 order by 1) as dias
					, vehiculos vh
					LEFT JOIN tipos_vehiculos tvh ON vh.id_tipo_vehiculo=tvh.id	 
					where id_centro in ($idCentro) and tvh.id in (1,2,4,8,11,14,15,19,21,29) and vh.id_estado<>4
					order by 3,4) as total
				group by id_centro,dia) as TT
			on TB.id=TT.id_centro and TB.dia=TT.dia
			group by id_centro,date_part('year',TB.dia)||'-'||
				CASE WHEN date_part('month',TB.dia)=1 THEN '01'
					WHEN date_part('month',TB.dia)=2 THEN '02'
					WHEN date_part('month',TB.dia)=3 THEN '03'
					WHEN date_part('month',TB.dia)=4 THEN '04'
					WHEN date_part('month',TB.dia)=5 THEN '05'
					WHEN date_part('month',TB.dia)=6 THEN '06'
					WHEN date_part('month',TB.dia)=7 THEN '07'
					WHEN date_part('month',TB.dia)=8 THEN '08'
					WHEN date_part('month',TB.dia)=9 THEN '09'
					WHEN date_part('month',TB.dia)=10 THEN '10'
					WHEN date_part('month',TB.dia)=11 THEN '11'
					WHEN date_part('month',TB.dia)=12 THEN '12'	
				END 
			order by 1,2";
		$qidefic = $db->sql_query($sqlefic);
 		while($efic = $db->sql_fetchrow($qidefic))
		{		
			$diasinop[$efic["id_centro"]][$efic["fecha"]]+=number_format($efic["inopera"],0);
			$vxdinop[$efic["id_centro"]][$efic["fecha"]]+=number_format($efic["vxd"],2);
			$i++;
		}
}


//final
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
