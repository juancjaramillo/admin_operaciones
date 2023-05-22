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

if(isset($_GET["format"])) 
	$html=false;

$titulo1 = $db->sql_row("SELECT upper(nombre||' : '||informe) as inf FROM informes i LEFT JOIN categorias_informes c ON c.id=i.id_categoria_informe WHERE i.id=".str_replace(".php","",simple_me($ME)));

if($html)
{
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/info/templates/fechas_form.php");
	tablita_titulos($titulo1["inf"],"");
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
	titulo_grande_xls($workbook, $worksheet, 0, 11, $titulo1["inf"]);
	$fila=2; $columna=0;
}
$user=$_SESSION[$CFG->sesion]["user"];

$titulos = array("CENTRO", "VEHÍCULO", "COSTO LLANTAS", "KM RECORRIDOS", "COSTO/KM");
if($html)
{
	echo '
		<table width="98%">
		<tr>
			<td valign="top" align="center">
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



$dxGraf = array("data"=>array(),  "labels"=>array());
$qid = $db->sql_query("SELECT v.id, v.codigo||' / '||v.placa as vehiculo, centro, kilometraje as km, v.codigo
		FROM vehiculos v
		LEFT JOIN centros c ON c.id=v.id_centro
		WHERE NOT v.alquilado AND c.id = '".$centro."'
		ORDER BY centro, v.codigo, v.placa");
while($veh = $db->sql_fetchrow($qid))
{
	$kmsR = $costo = 0;
	$qidLlta = $db->sql_query("SELECT id, costo FROM llta.llantas WHERE id_vehiculo='".$veh["id"]."'");
	while($llta = $db->sql_fetchrow($qidLlta))
	{
		$costo+=$llta["costo"];
		$mov = $db->sql_row("SELECT id,km, posicion FROM llta.movimientos WHERE id_llanta='".$llta["id"]."' AND id_tipo_movimiento=5");
		$kmsR += $veh["km"]-$mov["km"];
	}

	@$ind = $costo/$kmsR;
	$linea = array($veh["centro"], $veh["vehiculo"], number_format($costo, 0, ",", "."), number_format($kmsR, 0, ",", "."), number_format($ind, 2, ",", "."));
	if($html)
		imprimirLinea($linea,"",array(1=>"align='left'", 2=>"align='left'"));
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq", 2=>"txt_izq"));

	$dxGraf["data"][] = $ind;
	$dxGraf["labels"][] = $veh["codigo"];
}

if($html)
{
	$link = "?format=xls&id_centro=".$centro;
	echo "</table>
			</td>
			<td valign='top'>";
	graficaBarras($dxGraf, "COSTO DE LLANTAS POR KM RECORRIDO", "COSTO/KM", "Vehículo", "Costo/km");
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
	$nombreArchivo=preg_replace("/[^0-9a-z_.]/i","_",$titulo1["inf"]).".xls";
	header("Content-Type: application/x-msexcel; name=\"".$nombreArchivo."\"");
	header("Content-Disposition: inline; filename=\"".$nombreArchivo."\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
}






?>
