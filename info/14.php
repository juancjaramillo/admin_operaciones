<?
// barrido : longitudes x coordinador
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

$titulos = array("PERÍODO", "TIEMPO OPERACIÓN", "TIEMPO TEÓRICO", "KMS OPERACIÓN", "KMS TEÓRICO", "INDICADOR FUT", "INDICADOR FUK");
if($html)
{
	echo '
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

$datos = $dxGraf = array();
foreach($semana as $fecha)
{
	$uno = $fecha["Monday"];
	$OT=$TT=$kmO=$kmT=0;

	while($uno <= $fecha["Sunday"])
	{
		//el tiempo de operación 
		$OT+=tiempoOperacionxDia($uno, $centro, $id_turno, $id_ase);

		//el tiempo teórico
		$TT+=tiempoTeoricoOperacionxDia($uno, $centro, $id_turno, $id_ase);

		//el km recorrido
		$kmO+=kmsOperacionxDia($uno, $centro, $id_turno, $id_ase);

		//el km teorico
		$kmT+=kmsTeoricoOperacionxDia($uno, $centro, $id_turno, $id_ase);
		
		//siguiente
		list($anio,$mes,$dia)=split("-",$uno);
		$uno = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + 1 * 24 * 60 * 60);
	}

	$fut = ($OT/$TT)*100;
	$fuk = ($kmO/$kmT)*100;

	$linea = array($fecha["Monday"]." / ".$fecha["Sunday"], number_format($OT, 0,",", "."), number_format($TT, 0, ",", "."), number_format($kmO, 0, ",", "."), number_format($kmT, 0, ",", "."), number_format($fut, 2, ",", ".")."%", number_format($fuk,2,",",".")."%");
	if($html)
		imprimirLinea($linea,"",array(1=>"align='center'", 2=>"align='center'", 3=>"align='center'", 4=>"align='center'", 5=>"align='center'", 6=>"align='center'", 7=>"align='center'",));
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_center", 2=>"txt_center", 3=>"txt_center", 4=>"txt_center", 5=>"txt_center", 6=>"txt_center", 7=>"txt_center"));

	$dxGraf["data"][] = $fut;
	$dxGraf["dataDos"][] = $fuk;
	$dxGraf["dataDash"][] = 100;
	$dxGraf["labels"][] = ucfirst(strftime("%b.%d.%Y",strtotime($fecha["Monday"])))."\n".ucfirst(strftime("%b.%d.%Y",strtotime($fecha["Sunday"])));
}



//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro."&id_turno=".$id_turno."&id_ase=".$id_ase;
	echo "</table><br><br>";
	graficaMultiLine($dxGraf, "FACTOR DE UTILIZACIÓN: ".$inicio."/".$final, "", "FUT", "", "FUK");
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
