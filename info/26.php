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

$titulos = array("ASE", "CENTRO", "COSTOS", "TONELADAS", "COSTO POR TONELADA DISPUESTA");
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

dl("oci8.so");
include($CFG->common_libdir."/db/oci.php");
$dbOracle=new sql_db_oci($CFG->db_oracle_dbhost[$centro], $CFG->db_oracle_dbuser[$centro], $CFG->db_oracle_dbpass[$centro], $CFG->db_oracle_dbname[$centro]);

$cond = "";
if($id_turno != "")
	$cond = " AND m.id_turno=".$id_turno;

$ase = array();
$dxGraf = array("data"=>array(),  "labels"=>array());
$granPeso = $granCosto = 0;
$qid = $db->sql_query("SELECT a.id, ase, c.centro 
		FROM ases a 
		LEFT JOIN centros c ON c.id=a.id_centro 
		WHERE a.id_centro = '".$centro."'
		ORDER BY centro,ase");
while($ser = $db->sql_fetchrow($qid))
{
	$ase[$ser["id"]] = array("ase"=>$ser["ase"],"centro"=>$ser["centro"], "costo"=>0, "ton"=>0, "costoton"=>0);
	$vehiculos = array();

	//recolección
	$qidMov = $db->sql_query("SELECT m.*, i.codigo , v.placa as vehiculo
			FROM rec.movimientos m 
			LEFT JOIN micros i ON i.id=m.id_micro 
			LEFT JOIN vehiculos v ON v.id=m.id_vehiculo
			WHERE i.id_ase='".$ser["id"]."' AND inicio::date >= '".$inicio."' AND inicio::date<='".$final."' $cond");
	while($mov = $db->sql_fetchrow($qidMov))
	{
		//pesos
		$ase[$ser["id"]]["ton"]+=averiguarPesoXMov($mov["id"]);

		//costos
		$vehiculos[$mov["vehiculo"]] = $mov["vehiculo"];
	}

	$ase[$ser["id"]]["costo"] = costoRecoleccion($vehiculos, $inicio, $final);
	@$ase[$ser["id"]]["costoton"] = $ase[$ser["id"]]["costo"] /$ase[$ser["id"]]["ton"];

	

	$div = 0;
	$granPeso+=$ase[$ser["id"]]["ton"];
	$granCosto+=$ase[$ser["id"]]["costo"];

	$dxGraf["data"][] = $ase[$ser["id"]]["costoton"];
	$dxGraf["labels"][] = $ser["ase"];	

	$ase[$ser["id"]]["ton"]=number_format($ase[$ser["id"]]["ton"], 2, ",", ".");
	$ase[$ser["id"]]["costoton"]=number_format($ase[$ser["id"]]["costoton"], 2, ",", ".");
	$ase[$ser["id"]]["costo"]=number_format($ase[$ser["id"]]["costo"], 2, ",", ".");

	if($html)
		imprimirLinea($ase[$ser["id"]],"",array(2=>"align='left'"));
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $ase[$ser["id"]], array(1=>"txt_izq", 2=>"txt_izq"));
}

$stylos = array(1=>"colspan=2 align='left' strong", 2=>"strong", 3=>"strong", 4=>"strong", 5=>"strong");
@$granTotal = $granCosto/$granPeso;
$linea = array("GRAN TOTAL", number_format($granCosto, 2, ",", "."), number_format($granPeso, 2, ",", "."),number_format($granTotal, 2, ",", "."));
if($html)
	imprimirLinea($linea, "#b2d2e1", $stylos);
else
{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array("GRAN TOTAL"), array(1=>"azul_izq"),1);
	$columna++;
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array($granCosto, number_format($granPeso, 2, ",", "."),number_format($granTotal, 2, ",", ".")));
}


//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro."&id_turno=".$id_turno;
	echo "</table></td>
			<td valign='top'>";
	graficaBarras($dxGraf, "COSTO POR TONELADA: ".$inicio."/".$final, "COSTO TON", "Ase", "Costo");
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
