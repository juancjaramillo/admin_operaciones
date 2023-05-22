<?
include("../application.php");
$html = true;

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
$user=$_SESSION[$CFG->sesion]["user"];

dl("oci8.so");
include($CFG->common_libdir."/db/oci.php");
$dbOracle=new sql_db_oci($CFG->db_oracle_dbhost[$centro], $CFG->db_oracle_dbuser[$centro], $CFG->db_oracle_dbpass[$centro], $CFG->db_oracle_dbname[$centro]);

$titulos = array("ASE", "CENTRO", "COSTOS", "KM BARRIDOS", "DÍAS", "COSTO POR KM");
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

$dx = $ases = array();
$consulta = "SELECT a.ase, centro, i.km, a.id_centro, a.id as id_ase, m.id, to_char(m.inicio,'YYYY-MM-DD') as fecha, m.inicio, m.final, m.id as id_movimiento
		FROM bar.movimientos m
		LEFT JOIN micros i ON i.id=m.id_micro
		LEFT JOIN ases a ON a.id=i.id_ase
		LEFT JOIN centros c ON c.id=a.id_centro
		WHERE inicio::date>='".$inicio."' AND inicio::date <= '".$final."' AND c.id = '".$centro."' ".$cond."
		ORDER BY inicio, ase, centro";
$qid = $db->sql_query($consulta);
while($mov = $db->sql_fetchrow($qid))
{
	$ases[$mov["id_ase"]][$mov["id_centro"]] = array("ase"=>$mov["ase"], "centro"=>$mov["centro"]);

	if(!isset($dx[$mov["id_centro"]])) $dx[$mov["id_centro"]] = array("km"=>0, "costo"=>0, "dias"=>array());
	
	//km
	$dx[$mov["id_centro"]]["km"]+=$mov["km"];

	//costos
	$dx[$mov["id_centro"]]["costo"]+= costoBarridoPersona($mov["id_movimiento"], $mov["inicio"], $mov["final"]);

	//dias
	$dx[$mov["id_centro"]]["dias"][$mov["fecha"]]=$mov["fecha"];
}

$dxGraf = array("data"=>array(),  "labels"=>array());
foreach($ases as $idAse => $dxAse)
{
	foreach($dxAse as $idCentro => $centroA)
	{
		if(isset($dx[$idCentro]))
		{
			$otrosCostos = otrosCostosBarrido($inicio, $final);
			@$ind = ($dx[$idCentro]["costo"] + $otrosCostos)/$dx[$idCentro]["km"];
			$linea = array($centroA["ase"], $centroA["centro"], number_format(($dx[$idCentro]["costo"]+$otrosCostos), 2, ",", "."), number_format($dx[$idCentro]["km"], 2, ",", "."), count($dx[$idCentro]["dias"]), number_format($ind, 2, ",", "."));

			$dxGraf["data"][] = $ind;
			$dxGraf["labels"][] = $centroA["ase"]; 

			if($html)
				imprimirLinea($linea,"",array(1=>"align='left'", 2=>"align='left'"));
			else
				imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq", 2=>"txt_izq", 3=>"txt_center"));
		}
	}
}


//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro."&id_turno=".$id_turno;
	echo "</table></td>
			<td valign='top'>";
	graficaBarras($dxGraf, "COSTO POR KM : ".$inicio."/".$final, "COSTO", "Ase", "Costo");
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
