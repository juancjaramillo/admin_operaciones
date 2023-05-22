<?
// opera : PRODUCCIÓN POR RUTA
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

$condicion = "";
if($id_turno != "")
	$condicion = " AND m.id_turno = ".$id_turno;
if($id_ase != "")
	$condicion.= " AND i.id_ase = '".$id_ase."'";
else
	$condicion.= " AND i.id_ase IN (SELECT a.id FROM ases a WHERE a.id_centro = ".$centro.")";

if($html)
	echo '<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla"  align="center">
		<tr>
			<th height="40">SERVICIO</th>
			<th>MICRO</th>
			<th>TONELADAS</th>
			<th>VIAJES</th>
			<th>TONS/VIAJE</th>
		<tr>';
else
{
	$titulos = array("SERVICIO", "RUTA", "TONELADAS", "VIAJES", "TONS/VIAJE");
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, $titulos);
	$fila++;$columna=0;
}

$dxGraf = array("data"=>array(),  "labels"=>array());
$granPeso = $granViaje = 0;
$stylos = array(1=>"colspan=2 align='left'");
$qid = $db->sql_query("SELECT * FROM servicios WHERE esquema!='bar' ORDER BY servicio");
while($ser = $db->sql_fetchrow($qid))
{
	if($ser["esquema"] == "rec")
	{
		$micros = $codigos = array();
		$qidMov = $db->sql_query("SELECT m.*, i.codigo 
				FROM rec.movimientos m 
				LEFT JOIN micros i ON i.id=m.id_micro
				WHERE i.id_servicio='".$ser["id"]."' AND inicio::date >= '".$inicio."' AND inicio::date<='".$final."' $condicion
				ORDER BY i.codigo");
		while($mov = $db->sql_fetchrow($qidMov))
		{
			$codigos[$mov["id_micro"]] = $mov["codigo"];
			if(!isset($micros[$mov["id_micro"]]["peso"]))
				$micros[$mov["id_micro"]]["peso"] = 0;
			if(!isset($micros[$mov["id_micro"]]["viaj"]))
				$micros[$mov["id_micro"]]["viaj"] = 0;

			//pesos
			$micros[$mov["id_micro"]]["peso"]+=averiguarPesoXMov($mov["id"],"",true,$id_turno);

			//viajes
			$micros[$mov["id_micro"]]["viaj"]+= averiguarViajeXMov($mov["id"],$id_turno);
		}

		$ton=$viajes=0;
		foreach($micros as $key => $dx)
		{
			$div = 0;
			if($dx["viaj"]!= 0)
				$div = $dx["peso"]/$dx["viaj"];
			$linea = array($ser["servicio"], $codigos[$key], number_format($dx["peso"], 2, ",", "."), $dx["viaj"], number_format($div, 2, ",", "."));
			if($html)
				imprimirLinea($linea,"",array(2=>"align='left'"));
			else
				imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq",2=>"txt_izq"));
			$ton+=$dx["peso"];
			$viajes+=$dx["viaj"];
		}
		@$tonviaje = $ton/$viajes;
		$lineaHTML = array("Total ".$ser["servicio"], number_format($ton, 2, ",", "."),$viajes,number_format($tonviaje, 2, ",", "."));
		$granPeso+=$ton;
		$granViaje+=$viajes;

		$lineaXLS = array(number_format($ton, 2, ",", "."),$viajes,number_format($tonviaje, 2, ",", "."));
		if($html)
		{
			imprimirLinea($lineaHTML, "#b2d2e1", $stylos);
			$dxGraf["labels"][] = $ser["servicio"];
			if($tonviaje == "")
				$dxGraf["data"][] = 0;
			else
				$dxGraf["data"][] = $tonviaje;
		}
		else
		{
			imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array("Total ".$ser["servicio"]), array(1=>"azul_izq"),1);
			$columna++;
			imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $lineaXLS);
			$fila++;$columna=0;
		}
	}
}



$stylos = array(1=>"colspan=2 align='left' strong", 2=>"strong", 3=>"strong", 4=>"strong");
@$granTotal = $granPeso/$granViaje;
$linea = array("GRAN TOTAL",number_format($granPeso, 2, ",", "."),$granViaje,number_format($granTotal, 2, ",", "."));
if($html)
	imprimirLinea($linea, "#b2d2e1", $stylos);
else
{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array("GRAN TOTAL"), array(1=>"azul_izq"),1);
	$columna++;
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array(number_format($granPeso, 2, ",", "."),$granViaje,number_format($granTotal, 2, ",", ".")));
}

//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro."&id_turno=".$id_turno."&id_ase=".$id_ase;
	echo "</table><br /><br />";
	graficaBarras($dxGraf, "PRODUCCIÓN POR RUTAS: ".$inicio."/".$final, "Tons/Viaje", "Servicio", "Valor", 23);
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
