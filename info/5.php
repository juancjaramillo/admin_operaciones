<?
// opera : Promedio Tiempos por Ruta

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
$user=$_SESSION[$CFG->sesion]["user"];


$tipos = array();
$titulos = array("RUTA","TIEMPO TOTAL");
$qid = $db->sql_query("SELECT * FROM rec.tipos_desplazamientos ORDER BY orden");
while($query = $db->sql_fetchrow($qid))
{
	$tipos[$query["id"]] = $query["tipo"];
	$titulos[] = strtoupper($query["tipo"]);
}
$titulos = array_merge($titulos, array("TONELADAS","VIAJES","TONS/VIAJE"));

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

$cond="true";
if($id_turno != "")
	$cond .= " AND id_turno=".$id_turno;
if($id_ase != "")
	$cond .= " AND a.id = '".$id_ase."'";
else
	$cond .= " AND a.id_centro ='".$centro."'";
$cond.= " AND inicio::date >= '".$inicio."' AND inicio::date<='".$final."'";

$micros = $codigos = array();
$qid = $db->sql_query("SELECT mov.id as id_mov, m.codigo, m.id as id_micro
		FROM rec.movimientos mov
		LEFT JOIN micros m ON m.id=mov.id_micro
		LEFT JOIN ases a ON a.id=m.id_ase
		WHERE  $cond
		ORDER BY m.codigo");
while($mc = $db->sql_fetchrow($qid))
{
	$codigos[$mc["id_micro"]] = $mc["codigo"];
	if(!isset($micros[$mc["id_micro"]]))
		$micros[$mc["id_micro"]] = array("ton"=>0, "viaje"=>0, "desp"=>array());

	foreach($tipos as $idTipo => $val)
	{
		$qidDes = $db->sql_query("SELECT hora_fin-hora_inicio as hora FROM rec.desplazamientos WHERE id_movimiento=".$mc["id_mov"]." AND id_tipo_desplazamiento=".$idTipo." AND hora_fin IS NOT NULL AND hora_inicio IS NOT NULL");
		while($des = $db->sql_fetchrow($qidDes))
		{
			if(!isset($micros[$mc["id_micro"]]["desp"][$idTipo]))
				$micros[$mc["id_micro"]]["desp"][$idTipo]=array("time"=>"00:00:00","num"=>0);
			$micros[$mc["id_micro"]]["desp"][$idTipo]["time"]=SumaHoras($micros[$mc["id_micro"]]["desp"][$idTipo]["time"],$des["hora"]);
			$micros[$mc["id_micro"]]["desp"][$idTipo]["num"]++;
		}
	}

	//pesos
	$micros[$mc["id_micro"]]["ton"]+=averiguarPesoXMov($mc["id_mov"], "", true, $id_turno);

	//viajes
	$micros[$mc["id_micro"]]["viaje"]+=averiguarViajeXMov($mc["id_mov"],  $id_turno);
}

//preguntar($micros);
$dxGraf = array("data"=>array(),  "labels"=>array());
foreach($micros as $idMicro => $dx)
{
	$tt = "00:00:00";
	$promedios = array();
	foreach($dx["desp"] as $idTipo => $tm)
	{
		$promedios[$idTipo] = dividirTiempo($tm["time"],$tm["num"]);
		$tt = SumaHoras($tt,$promedios[$idTipo]);
	}

	$linea = array($codigos[$idMicro], $tt);
	$dxGraf["labels"][] = $codigos[$idMicro];
	$dxGraf["data"][] =  $tt;

	foreach($tipos as $idTipo => $val)
	{
		if(isset($promedios[$idTipo]))
			$linea[] = $promedios[$idTipo];
		else
		{
			if($html)
				$linea[] = "&nbsp;";
			else
				$linea[] = "";
		}
	}

	$linea[] = number_format($dx["ton"], 2, ",", ".");
	$linea[] = $dx["viaje"];
	@$div = $dx["ton"]/$dx["viaje"];
	$linea[] = number_format($div, 2, ",", ".");
	
	if($html)
		imprimirLinea($linea,"",array(1=>"align='left'"));
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));

}


//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro."&id_turno=".$id_turno."&id_ase=".$id_ase;
	echo "</table><br><br>";
	//preguntar($dxGraf);
	graficaBarras($dxGraf, "PROMEDIO DE TIEMPOS POR RUTA: ".$inicio." / ".$final, "Tiempo (horas)","Ruta","Horas");
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
