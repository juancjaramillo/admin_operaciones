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

$titulos = array("TIPO VEHÍCULO", "TONELADAS RECOGIDAS", "VIAJES", "TONELADAS/VIAJES");
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

$tipos = array();

$cond = "";
if($id_turno != "")
	$cond = " AND m.id_turno=".$id_turno;

if($id_ase != "") 
	$cond.= " AND a.id='".$id_ase."'";
else
	$cond.=" AND a.id_centro = '".$centro."'";

$qidVe = $db->sql_query("SELECT distinct(id_tipo_vehiculo), tv.tipo 
		FROM vehiculos v
	 	LEFT JOIN tipos_vehiculos tv ON tv.id=v.id_tipo_vehiculo	
		LEFT JOIN rec.movimientos m ON m.id_vehiculo=v.id
		LEFT JOIN micros i ON i.id = m.id_micro
		LEFT JOIN ases a ON a.id=i.id_ase
		WHERE m.inicio::date >='".$inicio."' AND m.inicio::date<='".$final."' $cond
		ORDER BY tipo");
while($veh = $db->sql_fetchrow($qidVe))
{
	$tipos[$veh["id_tipo_vehiculo"]] = $veh["tipo"];
}

$dxGraf = array("data"=>array(),  "labels"=>array());
foreach($tipos as $idTipo => $nombreTipo)
{
	$tons = $viajes = 0;
	$qidMov = $db->sql_query("SELECT m.id
			FROM rec.movimientos m 
			LEFT JOIN micros i ON i.id=m.id_micro 
			LEFT JOIN vehiculos v ON v.id=m.id_vehiculo
			LEFT JOIN ases a ON a.id=i.id_ase
			WHERE v.id_tipo_vehiculo='".$idTipo."' AND inicio::date >= '".$inicio."' AND inicio::date<='".$final."' $cond");
	while($mov = $db->sql_fetchrow($qidMov))
	{
		$tons+=averiguarPesoXMov($mov["id"], "", true, $id_turno);
		$viajes+=averiguarViajeXMov($mov["id"], $id_turno);
	}

	@$ind = $tons/$viajes;
	$linea = array($nombreTipo, number_format($tons, 2, ",", "."), $viajes, number_format($ind, 2, ",", "."));
	$dxGraf["data"][] = $ind;
	$dxGraf["labels"][] = $nombreTipo;

	if($html)
		imprimirLinea($linea,"",array(1=>"align='left'"));
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
}


if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro."&id_turno=".$id_turno."&id_ase=".$id_ase;
	echo "</table></td>
			<td valign='top'>";
	graficaBarras($dxGraf, "TONELADAS RECOGIDAS POR VIAJE: ".$inicio."/".$final, "Tons/Viaje", "Tipo", "Valor", 40);
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
