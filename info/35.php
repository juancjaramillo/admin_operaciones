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
$user=$_SESSION[$CFG->sesion]["user"];

$titulos = array("TIPO", "NÚMERO", "VALOR");
if($html)
{
	echo '<table width="100%">
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


$qid = $db->sql_query("SELECT * FROM mtto.tipos ORDER BY tipo");
while($query = $db->sql_fetchrow($qid))
{
	$nombre = $query["tipo"];
	$queryOT = $db->sql_query("SELECT r.id_tipo_mantenimiento, r.id as id_rutina, o.id as id_orden, c.id as id_centro
			FROM mtto.ordenes_trabajo o
			LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
			LEFT JOIN mtto.equipos e ON o.id_equipo=e.id
			LEFT JOIN centros c ON c.id=e.id_centro
			WHERE c.id ='".$centro."' AND  r.id_tipo_mantenimiento=".$query["id"]." AND id_estado_orden_trabajo IN (SELECT id FROM mtto.estados_ordenes_trabajo WHERE cerrado) AND o.fecha_ejecucion_inicio::date>='".$inicio."' AND o.fecha_ejecucion_inicio::date<='".$final."'");
	while($qidOT = $db->sql_fetchrow($queryOT))
	{
		if(!isset($datos[$qidOT["id_tipo_mantenimiento"]]["numero"]))
			$datos[$qidOT["id_tipo_mantenimiento"]]["numero"]=0;
		if(!isset($datos[$qidOT["id_tipo_mantenimiento"]]["valor"]))
			$datos[$qidOT["id_tipo_mantenimiento"]]["valor"] = 0;

		$datos[$qidOT["id_tipo_mantenimiento"]]["numero"]+=1;
		$datos[$qidOT["id_tipo_mantenimiento"]]["valor"]+=costoXOrdenTrabajo($qidOT["id_orden"]);
	}
}

$total = array("TOTAL", "numero"=>0, "valor"=>0);
$dxGraf = array("data"=>array(),  "labels"=>array());
$qidTM = $db->sql_query("SELECT * FROM mtto.tipos ORDER BY id");
while($queryTM = $db->sql_fetchrow($qidTM))
{
	$linea = array("Mantenimiento ".$queryTM["tipo"]);
	if(isset($datos[$queryTM["id"]]))
	{
		$linea = array_merge($linea, array($datos[$queryTM["id"]]["numero"],number_format($datos[$queryTM["id"]]["valor"], 2, ",", ".")));
		$total["numero"]+=$datos[$queryTM["id"]]["numero"];
		$total["valor"]+=$datos[$queryTM["id"]]["valor"];
		$dxGraf["data"][] = $datos[$queryTM["id"]]["valor"];
	}
	else
	{
		$linea = array_merge($linea, array("",""));
		$dxGraf["data"][] = "";
	}
	$dxGraf["labels"][] = $queryTM["tipo"];

	if($html)
		imprimirLinea($linea,"",array(1=>"align='left'"));
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
}


$total["valor"] = number_format($total["valor"], 2, ",", ".");
if($html)
	imprimirLinea($total,"#b2d2e1",array(1=>"align='left' strong"));
else
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $total, array(1=>"azul_izq"));


if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro;
	echo "</table></td>
			<td valign='top'>";
	graficaBarras($dxGraf, "COSTO POR TIPO DE MTTO: ".$inicio."/".$final, "Valor", "Tipo", "Valor");
	echo "</td>
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
