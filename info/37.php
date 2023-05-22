<?
include("../application.php");
$html = true;

$user=$_SESSION[$CFG->sesion]["user"];

if(isset($_POST["id_centro"]) && $_POST["id_centro"] != "")
	$centro = $_POST["id_centro"];
elseif(isset($_GET["id_centro"]) && $_GET["id_centro"] != "")
	$centro = $_GET["id_centro"];
else
{
	$qidCentro = $db->sql_row("SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."' ORDER BY id_centro");
	$centro = $qidCentro["id_centro"];
}

$id_turno = "";
if(isset($_POST["id_turno"]))
	$id_turno = $_POST["id_turno"];
elseif(isset($_GET["id_turno"]))
	$id_turno = $_GET["id_turno"];

$id_ase = "";
if(isset($_POST["id_ase"]))
	$id_ase = $_POST["id_ase"];
elseif(isset($_GET["id_ase"]))
	$id_ase = $_GET["id_ase"];


if(isset($_GET["format"]) && $_GET["format"]=="xls") 
{
	$html=false;
	$inicio = $_GET["inicio"];
	$final = $_GET["final"];
}


$vista = nvl($_POST["vista"],nvl($_GET["vista"],"tipo"));

$titulo1 = $db->sql_row("SELECT upper(nombre||' : '||informe) as inf FROM informes i LEFT JOIN categorias_informes c ON c.id=i.id_categoria_informe WHERE i.id=".str_replace(".php","",simple_me($ME)));


if($html)
{
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/info/templates/fechas_form.php");
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






if($vista == "tipo")
	$titulos = array("TIPO VEHÍCULO");
else
	$titulos = array("VEHÍCULO");
$titulos = array_merge($titulos, array("TONELADAS", "No. VIAJES", "PROMEDIO TONELADA<br />(días pico: lun-mar)", "PROMEDIO TIEMPO RUTA<br />(días pico: lun-mar)","PROMEDIO TIEMPO  OP<br />(días pico: lun-mar)",  "PROMEDIO TIEMPO OP", "TON/VIAJE", "%OCUPACIÓN<br />DÍA PICO", "%OCUPACIÓN"));	
if($html)
{
	echo '
		<table width="98%">
			<tr>
				<td valign="top" align="center" width="50%">

		<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
		<tr>';
	foreach($titulos as $tt)
		echo '<th height="40">'.$tt.'</th>';
	echo "</tr>";
}else
{
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, $titulos);
	$fila++;$columna=0;
}





$cond = "";
if($id_turno != "")
	$cond = " AND m.id_turno = '".$id_turno."'";

if($id_ase != "") 
	$cond.= " AND a.id='".$id_ase."'";
else
	$cond .= " AND a.id IN (SELECT id FROM ases WHERE id_centro = '".$centro."')";

$tipos = $cap = array();
$qidVe = $db->sql_query("SELECT distinct(id_tipo_vehiculo), tv.tipo, tv.capacidad
		FROM vehiculos v
		LEFT JOIN tipos_vehiculos tv ON tv.id=v.id_tipo_vehiculo	
		LEFT JOIN rec.movimientos m ON m.id_vehiculo=v.id
		LEFT JOIN micros i ON i.id=m.id_micro
		LEFT JOIN ases a ON a.id = i.id_ase
		WHERE m.inicio::date >='".$inicio."' AND m.inicio::date<='".$final."' AND v.id_centro = '".$centro."' $cond
		ORDER BY tipo");
while($veh = $db->sql_fetchrow($qidVe))
{
	$tipos[$veh["id_tipo_vehiculo"]] = $veh["tipo"];
	$cap[$veh["id_tipo_vehiculo"]] = $veh["capacidad"];
}

//$cond.= " and m.id_vehiculo = 87 and (to_char(inicio, 'D') ='1' or to_char(inicio, 'D')='4' or to_char(inicio, 'D')='5' or to_char(inicio, 'D')='6' or to_char(inicio, 'D')='7')";
//$cond.= " and m.id_vehiculo = 87 and (to_char(inicio, 'D') ='2' or to_char(inicio, 'D')='3')";
//$cond.= " and m.id_vehiculo = 87";
$datosCSV = "";

$dxGraf = array("data"=>array(),  "labels"=>array());
$dxGraf2 = array("data"=>array(),  "labels"=>array());
foreach($tipos as $idTipo => $nombreTipo)
{
	$tons = $viajes = $tiempo = $total = 0;
	$datos = $vehPico = array();
	$cons = "SELECT m.id, m.combustible, v.codigo||'/'||v.placa||' / '||centro as vehiculo, m.id_vehiculo, to_char(inicio, 'D') as dia, extract(dow from inicio) as dowinicio,  inicio, final  
			FROM rec.movimientos m 
			LEFT JOIN micros i ON i.id=m.id_micro 
			LEFT JOIN ases a ON a.id=i.id_ase
			LEFT JOIN centros c ON c.id=a.id_centro
			LEFT JOIN vehiculos v ON v.id=m.id_vehiculo
			WHERE v.id_tipo_vehiculo='".$idTipo."' AND inicio::date >= '".$inicio."' AND inicio::date<='".$final."' $cond  
			ORDER BY inicio";
	//echo $cons."<br>";
	$qidMov = $db->sql_query($cons);
	while($mov = $db->sql_fetchrow($qidMov))
	{
	//	preguntar($mov);
		$peso = averiguarPesoXMov($mov["id"], "", true, $id_turno, $mov["id_vehiculo"]);
	//	preguntar("pesomov ".$peso);
		$numViajes = averiguarViajeXMov($mov["id"], $id_turno);
		$timeXmov = restarFechasConHHmmss($mov["final"],$mov["inicio"],true);
		if($timeXmov>100000){
			error_log("***" . $timeXmov . "***");
			error_log($mov["id"] . "||" . $mov["final"] . "||" . $mov["inicio"]);
		}

		$datosCSV.=$mov["id"]."\t".$mov["vehiculo"]."\t".$mov["dia"]."\t".$mov["inicio"]."\t".$mov["final"]."\t".$peso."\t".$numViajes."\t".$timeXmov."\n";

		
	
		if(!isset($datos[$mov["id_vehiculo"]])) $datos[$mov["id_vehiculo"]] = array("codigo"=>$mov["vehiculo"],"tons"=>0,"viajes"=>0,"picoTons"=>0,"picoViajes"=>0,"picoTiempo"=>0, "tiempo"=>0, "picoRutaTiempo" =>0);

		$datos[$mov["id_vehiculo"]]["tons"]+=$peso;
		$datos[$mov["id_vehiculo"]]["viajes"]+=$numViajes;
		$datos[$mov["id_vehiculo"]]["tiempo"]+=$timeXmov;
		if($mov["dia"] == 2 || $mov["dia"] == 3)
		{
			$datos[$mov["id_vehiculo"]]["picoTons"]+=$peso;
			$datos[$mov["id_vehiculo"]]["picoViajes"]+=$numViajes;
			$vehPico[$mov["id_vehiculo"]] = $mov["id_vehiculo"];
			$datos[$mov["id_vehiculo"]]["picoTiempo"]+=$timeXmov;
			$datos[$mov["id_vehiculo"]]["picoRutaTiempo"]+=tiempoMovimientoRuta($mov["id"]);
		}

		//preguntar("peso total ".$datos[$mov["id_vehiculo"]]["tons"]);

	}

	//preguntar($vista);
	//preguntar($datos);

	$color = "";
	$picoTons = $picoTiempo = $picoTiempoRuta = 0;
	foreach($datos as $dx)
	{
		//@$tonxviaje = $dx["tons"]/$dx["viajes"];
		@$tonxviaje = $dx["tons"];
		@$pico = $dx["picoTons"]/$dx["picoViajes"];
		@$promTiempo = conversor_segundos($dx["tiempo"]/$dx["viajes"]);
		$picoTons+=$pico;
		@$tiempo+=$dx["tiempo"]/$dx["viajes"];
		@$tiempoPico = conversor_segundos($dx["picoTiempo"]/$dx["picoViajes"]);
		@$picoTiempo +=$dx["picoTiempo"]/$dx["picoViajes"];
		@$rutaTime =  conversor_segundos($dx["picoRutaTiempo"]/$dx["picoViajes"]);
		@$picoTiempoRuta += $dx["picoRutaTiempo"]/$dx["picoViajes"];

		$percPico = ($pico/$cap[$idTipo])*100;
		$perc = ($tonxviaje/$cap[$idTipo])*100;

		$linea = array($dx["codigo"], number_format($dx["tons"], 2, ",", "."), $dx["viajes"], number_format($pico, 2, ",", "."), number_format(pasarHorasADecimales($rutaTime),2,",","."), number_format(pasarHorasADecimales($tiempoPico),2,",","."),  number_format(pasarHorasADecimales($promTiempo),2,",","."), number_format($tonxviaje, 2, ",", "."), number_format($percPico, 0, ",", ".")."%",  number_format($perc, 0, ",", ".")."%");

		$tons+=$dx["tons"];
		$viajes+=$dx["viajes"];
		$total+=$tonxviaje;

		if($vista=="vehiculo")
		{
			if($html)
				imprimirLinea($linea,"",array(1=>"align='left'"));
			else
				imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
				$color = "#b2d2e1";
		}
	}
	

	//$tons=$tons/count($datos);
	$tons=$tons;
	$total=$total/count($datos);
	@$viajes = $viajes/count($datos);
	@$pico = $picoTons/count($vehPico);
	$picoTiempo = conversor_segundos($picoTiempo/count($vehPico));
	$picoTiempoRuta  = conversor_segundos($picoTiempoRuta/count($vehPico));
	$promTiempo = conversor_segundos($tiempo/count($datos));
	$percPico = ($pico/$cap[$idTipo])*100;
	$perc = ($total/$cap[$idTipo])*100;
		
	$linea = array($nombreTipo, number_format($tons, 2, ",", "."), number_format($viajes, 2, ",", "."), number_format($pico, 2, ",", "."),  number_format(pasarHorasADecimales($picoTiempoRuta),2,",","."), number_format(pasarHorasADecimales($picoTiempo),2,",","."),  number_format(pasarHorasADecimales($promTiempo),2,",","."),  number_format($total, 2, ",", "."), number_format($percPico, 0, ",", ".")."%",  number_format($perc, 0, ",", ".")."%");

	if($html)
		imprimirLinea($linea,$color,array(1=>"align='left'"));
	else
	{	
		if($vista=="vehiculo")
		{
			imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"azul_izq"));
			$fila++;$columna=0;
		}
		else
		{
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
		}
	}


	
	$dxGraf["data"][] = $total;
	$dxGraf["labels"][] = $nombreTipo;

	$dxGraf2["data"][] = number_format($perc, 0, ",", ".");
	$dxGraf2["labels"][] = $nombreTipo;
	$dxGraf2["metas"]["Crítico"][] = 83;
	$dxGraf2["metas"]["Intermedio"][] = 88;
	$dxGraf2["metas"]["Satisfactorio"][] = 100;

}



/*
$csv_file = "verificacion_37_dos.csv";
if(!$handle = fopen($csv_file, "w")) {
     echo "Cannot open file";
     exit;
 }
 if (fwrite($handle, $datosCSV) === FALSE) {
     echo "Cannot write to file";
     exit;
 }
 fclose($handle);
*/



if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&vista=".$vista."&id_centro=".$centro."&id_turno=".$id_turno."&id_ase=".$id_ase;
	echo "</table></td>
			<td valign='top'>";
	graficaBarras($dxGraf, "PRODUCCIÓN POR TIPO Y VEHÍCULO: ".$inicio."/".$final, "Tons/Viaje", "Tipo", "Valor", 40);
	graficaIndicadores($dxGraf2, "PRODUCCIÓN POR TIPO Y VEHÍCULO: ".$inicio."/".$final);
	echo "	</td>
		</tr>
	</table>
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
