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
$user=$_SESSION[$CFG->sesion]["user"];


$titulos = array("ASE", "CENTRO","CUARTELILLO", "FECHA", "No. BOLSAS", "No. OPERARIOS", "BOLSAS POR OPERARIO");
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

$cond = "";
if($id_turno != "")
	$cond = " AND m.id_turno=".$id_turno;

if($id_ase != "") 
	$cond.= " AND a.id='".$id_ase."'";
else
	$cond.=" AND c.id = '".$centro."'";

$dx = $ases = array();
$consulta = "SELECT to_char(m.inicio,'YYYY-MM-DD') as inicio, a.ase, centro, a.id_centro, a.id as id_ase, m.id, i.id_cuartelillo, l.nombre
		FROM bar.movimientos m
		LEFT JOIN micros i ON i.id=m.id_micro
		LEFT JOIN ases a ON a.id=i.id_ase
		LEFT JOIN centros c ON c.id=a.id_centro
		LEFT JOIN cuartelillos l ON l.id=i.id_cuartelillo
		WHERE inicio::date>='".$inicio."' AND inicio::date <= '".$final."' $cond
		ORDER BY nombre, inicio, ase, centro";
$qid = $db->sql_query($consulta);
while($mov = $db->sql_fetchrow($qid))
{
	$ases[$mov["id_ase"]][$mov["id_centro"]][$mov["id_cuartelillo"]] = array("ase"=>$mov["ase"], "centro"=>$mov["centro"], "cuartel" => strtoupper($mov["nombre"]));

	if(!isset($dx[$mov["id_cuartelillo"]][$mov["inicio"]])) $dx[$mov["id_cuartelillo"]][$mov["inicio"]] = array("bolsas"=>0, "oper"=>0);
	
	//bolsas
	$dx[$mov["id_cuartelillo"]][$mov["inicio"]]["bolsas"]+=bolsasXMovimiento($mov["id"]);

	//numOperarios
	$qidOpe = $db->sql_row("SELECT count(id) as num FROM bar.movimientos_personas WHERE id_movimiento=".$mov["id"]);
	$dx[$mov["id_cuartelillo"]][$mov["inicio"]]["oper"]+=nvl($qidOpe["num"]);
}

$dxGraf = array("data"=>array(),  "labels"=>array());
foreach($ases as $idAse => $dxAse)
{
	foreach($dxAse as $idCentro => $cuartel)
	{
		foreach($cuartel as $idCuartel => $dxCuartel)
		{
			$totalBolsas = $totalOpe = $total = 0;
			if(isset($dx[$idCuartel]))
			{
				foreach($dx[$idCuartel] as $fecha => $dx)	
				{
					$ind = $dx["bolsas"]/ $dx["oper"];
					$linea = array($dxCuartel["ase"], $dxCuartel["centro"], $dxCuartel["cuartel"], $fecha, $dx["bolsas"], $dx["oper"], number_format($ind, 2, ",", "."));
					if($html)
						imprimirLinea($linea,"",array(1=>"align='left'", 2=>"align='left'", 3=>"align='left'", 4=>"align='center'"));
					else
						imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq", 2=>"txt_izq", 3=>"txt_izq", 4=>"txt_center"));

					$totalBolsas+=$dx["bolsas"];
					$totalOpe+=$dx["oper"];
				}
			}

			$total = $totalBolsas/$totalOpe;
			$dxGraf[] = '"'.trim(str_replace("CUARTELILLO","",str_replace("-","",$dxCuartel["cuartel"]))).'",'.$total;
			$dxGraf["data"][] = $total;
			$dxGraf["labels"][] = $dxCuartel["cuartel"];
			$linea = array($dxCuartel["ase"], $dxCuartel["centro"],"TOTAL ".$dxCuartel["cuartel"], "",$totalBolsas, $totalOpe, number_format($total, 2, ",", "."));	
			if($html)
			  imprimirLinea($linea,"#b2d2e1",array(1=>"align='left'", 2=>"align='left'", 3=>"align='center'"));
			else
			{
				imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"azul_izq", 2=>"azul_izq", 3=>"azul_center"));
				$fila++;$columna=0;
			}
		}
	}
}


//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro."&id_turno=".$id_turno."&id_ase=".$id_ase;
	echo "</table></td>
			<td valign='top'>";
	graficaBarras($dxGraf, "BOLSAS POR OPERARIO: ".$inicio."/".$final, "TOTAL BOLSAS", "Bolsas", "Valor", 40);
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
