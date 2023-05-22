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

if($html)
{
	echo '
		<table width="98%">
			<tr>';
}

$cond = "";
if($id_turno != "")
	$cond = " AND m.id_turno = '".$id_turno."'";

if($id_ase != "") 
	$cond.= " AND a.id='".$id_ase."'";
else
	$cond .= " AND a.id IN (SELECT id FROM ases WHERE id_centro = '".$centro."')";

$tipos = array();
$qidVe = $db->sql_query("SELECT distinct(id_tipo_vehiculo), tv.tipo 
		FROM vehiculos v
		LEFT JOIN tipos_vehiculos tv ON tv.id=v.id_tipo_vehiculo	
		LEFT JOIN rec.movimientos m ON m.id_vehiculo=v.id
		LEFT JOIN micros i ON i.id=m.id_micro
		LEFT JOIN ases a ON a.id = i.id_ase
		WHERE m.inicio::date >='".$inicio."' AND m.inicio::date<='".$final."' $cond
		ORDER BY tipo");
while($veh = $db->sql_fetchrow($qidVe))
{
	$tipos[$veh["id_tipo_vehiculo"]] = $veh["tipo"];
}

$datos = $dxTipos = array();
foreach($tipos as $idTipo => $nombreTipo)
{
	$kms = $comb = 0;
	$qidMov = $db->sql_query("SELECT m.id, m.combustible, v.id_tipo_vehiculo, v.codigo||'/'||v.placa as vehiculo, v.codigo as solo_codigo, m.id_vehiculo
			FROM rec.movimientos m 
			LEFT JOIN micros i ON i.id=m.id_micro 
			LEFT JOIN vehiculos v ON v.id=m.id_vehiculo
			LEFT JOIN ases a ON a.id = i.id_ase
			WHERE v.id_tipo_vehiculo='".$idTipo."' AND inicio::date >= '".$inicio."' AND inicio::date<='".$final."' $cond");
	while($mov = $db->sql_fetchrow($qidMov))
	{
		$recXMov = kmsRecorridoPorMov($mov["id"]);
		$kms+=$recXMov;
		$comb+=$mov["combustible"];

		if(!isset($datos[$idTipo][$mov["id_vehiculo"]])) $datos[$idTipo][$mov["id_vehiculo"]] = array("codigo"=>$mov["vehiculo"], "solo_codigo"=>$mov["solo_codigo"], "kms"=>0, "comb"=>0);

		$datos[$idTipo][$mov["id_vehiculo"]]["kms"]+=$recXMov;
		$datos[$idTipo][$mov["id_vehiculo"]]["comb"]+=$mov["combustible"];
	}

	@$ind = $kms/$comb;
	$dxTipos[$idTipo] = array("kms"=>$kms, "comb"=>$comb, "ind"=>$ind);
}


//cuadro uno
$titulos = array("TIPO VEHÍCULO", "KMS RECORRIDOS", "COMBUSTIBLE (GL)", "KMS/COMBUSTIBLE");
if($html)
{
	echo '<td valign="top" align="left" width="48%">
					<table width="100%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
						<tr>';
	foreach($titulos as $tt)
		echo '<th height="40">'.$tt.'</th>';
	echo "</tr>";
}
foreach($tipos as $idTipo => $nombreTipo)
{
	$linea = array($nombreTipo, number_format($dxTipos[$idTipo]["kms"], 0, ",", "."), number_format($dxTipos[$idTipo]["comb"], 2, ",", "."), number_format($dxTipos[$idTipo]["ind"], 2, ",", "."));

	if($html)
		imprimirLinea($linea,"",array(1=>"align='left'"));
}

if($html)
{
	echo '</table></td>
		<td width="4%"></td>';
}

//cuadro dos
$titulos = array("VEHÍCULO", "KMS RECORRIDOS", "COMBUSTIBLE (GL)", "KMS/COMBUSTIBLE");
if($html)
{
	echo '<td valign="top" align="right" width="48">
			<table width="100%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
				<tr>';
	foreach($titulos as $tt)
		echo '<th height="40">'.$tt.'</th>';
	echo "</tr>";
}else
{
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, $titulos);
	$fila++;$columna=0;
}
foreach($tipos as $idTipo => $nombreTipo)
{
	$linea = array($nombreTipo, number_format($dxTipos[$idTipo]["kms"], 0, ",", "."), number_format($dxTipos[$idTipo]["comb"], 2, ",", "."), number_format($dxTipos[$idTipo]["ind"], 2, ",", "."));

	if($html)
		imprimirLinea($linea, "#b2d2e1", array(1=>"align='left'"));
	else
	{	
		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $linea,  array(1=>"azul_izq"));
		$fila++;$columna=0;		
	}

	foreach($datos[$idTipo] as $dx)
	{
		@$ind = $dx["kms"]/$dx["comb"];
		$linea = array($dx["codigo"], number_format($dx["kms"], 0, ",", "."), number_format($dx["comb"], 2, ",", "."), number_format($ind, 2, ",", "."));
		if($html)
			imprimirLinea($linea,"",array(1=>"align='left'"));
		else
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
	}
}

if($html)
{
	echo "</table>
			</td>
		</tr>
	</table>";
}

if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro."&id_turno=".$id_turno."&id_ase=".$id_ase;
	echo "<table width='98%' align='center'>";

	$i=$j=0;
	foreach($tipos as $idTipo => $nombreTipo)
	{
		$dxGraf = array();
		foreach($datos[$idTipo] as $dx)
		{
			@$ind = $dx["kms"]/$dx["comb"];
			$dxGraf["data"][] =  $ind;
			$dxGraf["labels"][] = $dx["solo_codigo"];
		}

		if($i==0) echo "</tr>";

		echo "<td>";
		graficaBarras_21_23($dxGraf,$nombreTipo, "KMS/COMBUSTIBLE", $inicio, $final, $centro, $id_turno);
		echo "</td>";

		$i++;$j++;
		if($i==2 || $j==count($tipos))
		{
			echo "</tr>";
			$i=0;
		}
	}

	echo "</table>";
	echo "<table width='98%' align='center'>
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