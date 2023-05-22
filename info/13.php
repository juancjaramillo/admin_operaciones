<?
// mtto : indicador factor de utilización
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

if(isset($_GET["format"])) 
{
	$html=false;
	$inicio = $_GET["inicio"];
	$final = $_GET["final"];
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


$titulos = array("PERÍODO", "TONOP", "TONCAP", "SOBREPESO", "INDICADOR FACTOR CARGA", "INDICADOR SOBREPESO");
if($html)
{
	echo '
		<table width="70%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
		<tr>';
	foreach($titulos as $tt)
		echo '<th height="40">'.$tt.'</th>';
	echo "</tr>";
}else
{
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, $titulos);
	$fila++;$columna=0;
}
$user=$_SESSION[$CFG->sesion]["user"];

$semana = array();
$first = $inicio;
while($first <= $final)
{
	$s = obtenerSemana($first);
	$key = strftime("%V",strtotime($first));
	if($s["Monday"] < $inicio)
		$s["Monday"] = $inicio;
	if($s["Sunday"] > $final)
		$s["Sunday"] = $final;

	$semana[$key] = $s;

	//siguiente
	list($anio,$mes,$dia)=split("-",$first);
	$first = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + 1 * 24 * 60 * 60);

}


foreach($semana as $fecha)
{
	$sobrepeso = $recogidas = $capacidad = 0;
	$consulta = "
		SELECT p.*, t.capacidad
		FROM rec.pesos p
		LEFT JOIN vehiculos v ON v.id = p.id_vehiculo
		LEFT JOIN tipos_vehiculos t ON t.id=v.id_tipo_vehiculo
		WHERE fecha_entrada::date>='".$fecha["Monday"]."' AND fecha_entrada::date<= '".$fecha["Sunday"]."' AND v.id_centro = '".$centro."'";
	$qid = $db->sql_query($consulta);
	while($p = $db->sql_fetchrow($qid))
	{
		if($p["peso_inicial"] != "" && $p["peso_final"]) $neto = abs($p["peso_final"]-$p["peso_inicial"]);
		else $neto = $p["peso_total"];

		$recogidas+=$neto;
		$capacidad+= $p["capacidad"];

		if($neto > $p["capacidad"]) $sobrepeso += $neto - $p["capacidad"];

	}

	@$is = ($sobrepeso/$recogidas)*100;
	@$ic = ($recogidas/$capacidad)*100;

	$linea = array($fecha["Monday"]." / ".$fecha["Sunday"], number_format($recogidas, 2, ",", "."), number_format($capacidad, 2, ",", "."), number_format($sobrepeso, 2, ",", "."),number_format($ic, 3, ",", ".")."%" ,number_format($is, 3, ",", ".")."%");

	if($html)
		imprimirLinea($linea,"",array(1=>"align='left'", 5=>"align='center'", 6=>"align='center'"));
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq", 5=>"txt_center", 6=>"txt_center"));
	
	$dxGraf["sobrepeso"][] = $is;
	$dxGraf["carga"][] = $ic;
	$dxGraf["labels"][] = ucfirst(strftime("%b.%d.%Y",strtotime($fecha["Monday"])))."\n".ucfirst(strftime("%b.%d.%Y",strtotime($fecha["Sunday"])));
}

//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro;
	echo "</table><br><br>";
	graficaFactorCargaySobrepeso($dxGraf, "FACTOR DE CARGA Y SOBREPESO: ".$inicio."/".$final);
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

$titulos = array("VEHICULO", "TONOP", "TONCAP", "SOBREPESO", "INDICADOR FACTOR CARGA", "INDICADOR SOBREPESO");
if($html)
{
	echo '
		<table width="60%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
		<tr>';
	foreach($titulos as $tt)
		echo '<th height="40">'.$tt.'</th>';
	echo "</tr>";
}else
{
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, $titulos);
	$fila++;$columna=0;
}


	$sobrepeso = $recogidas = $capacidad = 0;
	$consulta = "SELECT v.codigo||'/'||v.placa as vehiculo,  
		SUM(CASE WHEN p.peso_inicial IS NOT NULL AND p.peso_final IS NOT NULL THEN abs(p.peso_final-p.peso_inicial) ELSE p.peso_total END) as neto,
		SUM(CASE WHEN CASE WHEN p.peso_inicial IS NOT NULL AND p.peso_final IS NOT NULL THEN abs(p.peso_final-p.peso_inicial) ELSE p.peso_total END > t.capacidad 
		THEN  CASE WHEN p.peso_inicial IS NOT NULL AND p.peso_final IS NOT NULL THEN abs(p.peso_final-p.peso_inicial) ELSE p.peso_total END - t.capacidad 
		ELSE 0 END) AS sobrepeso, SUM(t.capacidad) AS capacidad
		FROM rec.pesos p
		LEFT JOIN vehiculos v ON v.id = p.id_vehiculo
		LEFT JOIN tipos_vehiculos t ON t.id=v.id_tipo_vehiculo
		WHERE fecha_entrada::date>='".$inicio."' AND fecha_entrada::date<= '".$final."' AND v.id_centro = '".$centro."'
		group by v.codigo||'/'||v.placa
		order by v.codigo||'/'||v.placa";
	$qid = $db->sql_query($consulta);
	while($p = $db->sql_fetchrow($qid))
	{
		@$is = ($p["sobrepeso"]/$p["neto"])*100;
		@$ic = ($p["neto"]/$p["capacidad"])*100;

		$linea1 = array($p["vehiculo"], number_format($p["neto"], 2, ",", "."), number_format($p["capacidad"], 2, ",", "."), number_format($p["sobrepeso"], 2, ",", "."),number_format($ic, 3, ",", ".")."%" ,number_format($is, 3, ",", ".")."%");

		if($html)
			imprimirLinea($linea1,"",array(1=>"align='left'", 5=>"align='center'", 6=>"align='center'"));
		else
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea1, array(1=>"txt_izq", 5=>"txt_center", 6=>"txt_center"));
	}

echo "
	<table width='98%' align='center'>
		<tr>
			<td height='50' valign='bottom' align='right'><input type='button' class='boton_verde' value='Bajar en xls' onclick=\"window.location.href='".$ME.$link."'\"/></td>
		</tr>
	</table>
	";
?>
