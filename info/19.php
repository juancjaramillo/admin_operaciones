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

$titulos = array("TRIPULACIÓN", "CENTRO", "TONELADAS RECOGIDAS");
if($html)
{
	echo '
		<table width="100%">
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

$cond = " AND a.id IN (SELECT id FROM ases WHERE id_centro = '".$centro."')";
if($id_ase != "") $cond= " AND a.id='".$id_ase."'";

$dxGraf = array("data"=>array(),  "labels"=>array());
$personas = array();
$qid = $db->sql_query("SELECT distinct(mp.id_persona) as id_persona , p.nombre||' '||p.apellido as persona, 
		array_to_string(array(
				SELECT centro
				FROM personas_centros pc
				LEFT JOIN centros c ON c.id=pc.id_centro
				WHERE pc.id_persona = p.id),', ') as centro
		FROM rec.movimientos_personas mp
		LEFT JOIN rec.movimientos m ON m.id=mp.id_movimiento
		LEFT JOIN micros i ON i.id=m.id_micro
		LEFT JOIN ases a ON a.id = i.id_ase
		LEFT JOIN personas p ON p.id=mp.id_persona
		WHERE m.inicio::date >='".$inicio."' AND m.inicio::date <= '".$final."' $cond
		ORDER BY persona, centro");
while($query = $db->sql_fetchrow($qid))
{
	$linea = array($query["persona"], $query["centro"]);
	$totalPeso = 0;
	$qidMov = $db->sql_query("SELECT distinct(m.id) as id FROM rec.movimientos m LEFT JOIN rec.movimientos_personas mp ON m.id=mp.id_movimiento WHERE mp.id_persona=".$query["id_persona"]);
	while($mov = $db->sql_fetchrow($qidMov))
	{
		$peso = averiguarPesoXMov($mov["id"],"",true,$id_turno);
		$numPers = $db->sql_row("SELECT count(id) as num FROM rec.movimientos_personas WHERE id_movimiento=".$mov["id"]);
		$totalPeso+=$peso/$numPers["num"];
	}

	$linea[] = number_format($totalPeso,2,",",".");
	$dxGraf["data"][] = $totalPeso;
	$dxGraf["labels"][] = $query["persona"];

	if($html)
		imprimirLinea($linea, "", array(1=>"align='left'", 2=>"align='left'"));
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq", 2=>"txt_izq"));
}


if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro."&id_turno=".$id_turno."&id_ase=".$id_ase;
	echo "</table></td>
			<td valign='top'>";
	graficaBarras($dxGraf, "TONELADAS RECOGIDAS POR TRIPULACIÓN", "TONELADAS", "Tons", "Valor", 45, 5);
	echo "
				</td>
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
