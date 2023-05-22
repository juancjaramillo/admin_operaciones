<?
// operaciones : Programación Diaria
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

$id_persona = "";
if(isset($_POST["id_persona"]))
	$id_persona = $_POST["id_persona"];
elseif(isset($_GET["id_persona"]))
	$id_persona = $_GET["id_persona"];

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
	$estilosTit = array(1=>"azul_izq");
	$estilosText = array(1=>"txt_izq");
}

$lineaPrimera = array("PERSONA", "CÉDULA", "VEHÍCULO", "HORA INICIO", "HORA FIN", "TIEMPO TOTAL (mn)");

if($html)
{
	echo '
		<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
			<tr>';
		foreach($lineaPrimera as $dx)
			echo '<th height="40">'.$dx.'</th>';
	echo "</tr>";
}else
{
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, $lineaPrimera);
	$fila++;$columna=0;
}

$cond = " AND mp.id_persona IN (SELECT id_persona FROM personas_centros WHERE id_centro = '".$centro."'";
if($id_persona != "")
	$cond.= " AND mp.id_persona='".$id_persona."'";

$qid = $db->sql_query("
		SELECT *
		FROM(
			SELECT p.id, p.nombre||' '||p.apellido as nombre, p.cedula, mp.hora_inicio as inicio, mp.hora_fin as fin, v.codigo||'/'||v.placa as vehiculo
			FROM rec.movimientos_personas mp
			LEFT JOIN rec.movimientos m ON m.id=mp.id_movimiento
			LEFT JOIN vehiculos v ON v.id = m.id_vehiculo
			LEFT JOIN personas p ON p.id=mp.id_persona
			WHERE mp.hora_inicio::date>='".$inicio."' AND mp.hora_inicio::date<='".$final."' ".$cond.")
			UNION
			SELECT p.id, p.nombre||' '||p.apellido as nombre, p.cedula, mp.hora_inicio as inicio, mp.hora_fin as fin, v.codigo||'/'||v.placa as vehiculo
			FROM bar.movimientos_personas mp 
			LEFT JOIN bar.movimientos m ON m.id=mp.id_movimiento
			LEFT JOIN vehiculos v ON v.id = m.id_vehiculo
			LEFT JOIN personas p ON p.id=mp.id_persona
			WHERE mp.hora_inicio::date>='".$inicio."' AND mp.hora_inicio::date<='".$final."' ".$cond.")
		) AS foo
		ORDER BY inicio, fin, nombre");
while($per = $db->sql_fetchrow($qid))
{
	$tt = conversor_segundos(restarFechasConHHmmss($per["fin"], $per["inicio"], true));
	$linea = array($per["nombre"], $per["cedula"], $per["vehiculo"], $per["inicio"], $per["fin"], $tt);
	if($html)
		imprimirLinea($linea, "",  array(2=>"align='center'", 3=>"align='center'", 4=>"align='center'", 5=>"align='center'", 6=>"align='center'") );
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq", 2=>"txt_center", 3=>"txt_center", 4=>"txt_center", 5=>"txt_center", 6=>"txt_center"));
}

//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro."&id_persona=".$id_persona;
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