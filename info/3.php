<?
// opera : Producción por Ases
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
	echo '
		<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla"  align="center">
			<tr>
				<th height="40">ASE</th>
				<th>CENTRO</th>
				<th>TONELADAS</th>
				<th>VIAJES</th>
				<th>TONS/VIAJE</th>
				<th>COMBUSTIBLE</th>
				<th>KMS RECORRIDOS</th>
			<tr>';
else
{
	$titulos = array("ASE", "CENTRO", "TONELADAS", "VIAJES", "TONS/VIAJE", "COMBUSTIBLE", "KMS RECORRIDOS");
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, $titulos);
	$fila++;$columna=0;
}

$ase = array();
$granPeso = $granViaje = $granKms = $granGas = 0;
$dxGraf = array("data"=>array(),  "labels"=>array());
$cond = "";
if($id_turno != "")
	$cond = " AND m.id_turno='".$id_turno."'";

$qid = $db->sql_query("SELECT a.id, ase, c.centro 
		FROM ases a 
		LEFT JOIN centros c ON c.id=a.id_centro 
		WHERE a.id_centro = '".$centro."'
		ORDER BY centro,ase");
while($ser = $db->sql_fetchrow($qid))
{
	$ase[$ser["id"]] = array("ase"=>$ser["ase"],"centro"=>$ser["centro"], "ton"=>0, "viaj"=>0, "tonviaj"=>0, "gas"=>0, "kms"=>0);
	
	//recolección
	$qidMov = $db->sql_query("SELECT m.*, i.codigo 
			FROM rec.movimientos m 
			LEFT JOIN micros i ON i.id=m.id_micro 
			WHERE i.id_ase='".$ser["id"]."' AND inicio::date >= '".$inicio."' AND inicio::date<='".$final."'".$cond);
	while($mov = $db->sql_fetchrow($qidMov))
	{
		//pesos
		$ase[$ser["id"]]["ton"]+=averiguarPesoXMov($mov["id"],"",true,$id_turno);

		//viajes
		$ase[$ser["id"]]["viaj"]+=averiguarViajeXMov($mov["id"],$id_turno);
	
		$ase[$ser["id"]]["gas"]+=$mov["combustible"];
		$ase[$ser["id"]]["kms"]+=kmsRecorridoPorMov($mov["id"]);
	}

	//ton /viaje por ase
	$div = 0;
	$granPeso+=$ase[$ser["id"]]["ton"];
	$granViaje+=$ase[$ser["id"]]["viaj"];
	$granGas+=$ase[$ser["id"]]["gas"];
	$granKms+=$ase[$ser["id"]]["kms"];


	if($ase[$ser["id"]]["viaj"]!= 0)
		$div = $ase[$ser["id"]]["ton"]/$ase[$ser["id"]]["viaj"];
	$ase[$ser["id"]]["tonviaj"] = number_format($div, 2, ",", ".");;
	$ase[$ser["id"]]["ton"]=number_format($ase[$ser["id"]]["ton"], 2, ",", ".");
	
	if($html)
		imprimirLinea($ase[$ser["id"]],"",array(2=>"align='left'"));
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $ase[$ser["id"]], array(1=>"txt_izq", 2=>"txt_izq"));

	$dxGraf["data"][]=$div;
	$dxGraf["labels"][]=$ser["ase"];
}

$stylos = array(1=>"colspan=2 align='left' strong", 2=>"strong", 3=>"strong", 4=>"strong", 5=>"strong", 6=>"strong");
@$granTotal = $granPeso/$granViaje;
$linea = array("GRAN TOTAL",number_format($granPeso, 2, ",", "."),$granViaje,number_format($granTotal, 2, ",", "."), $granGas, number_format($granKms, 2, ",", "."));
if($html)
	imprimirLinea($linea, "#b2d2e1", $stylos);
else
{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array("GRAN TOTAL"), array(1=>"azul_izq"),1);
	$columna++;
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array(number_format($granPeso, 2, ",", "."),$granViaje,number_format($granTotal, 2, ",", "."),  $granGas, number_format($granKms, 2, ",", ".")));
}



//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro."&id_turno=".$id_turno;
	echo "</table><br /><br />";
	graficaBarras($dxGraf, "PRODUCCIÓN POR ASES: ".$inicio."/".$final, "Tons/Viaje", "Servicio", "Valor", 40);
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
