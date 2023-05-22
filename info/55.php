<?
 // error_reporting(E_ALL);
 // ini_set("display_errors", 1);
// operacion : velocidades
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

$id_vehiculo = "";
if(isset($_POST["id_vehiculo"]))
	$id_vehiculo = $_POST["id_vehiculo"];
elseif(isset($_GET["id_vehiculo"]))
	$id_vehiculo = $_GET["id_vehiculo"];

if($id_vehiculo != "")
	$cond.= " AND v.id='".$id_vehiculo."'";
	
$velocidad = "";
if(isset($_POST["velocidad"]))
	$velocidad = $_POST["velocidad"];
elseif(isset($_GET["velocidad"]))
	$velocidad = $_GET["velocidad"];

$inicio = "";
if(isset($_POST["inicio"]))
	$inicio = $_POST["inicio"];
elseif(isset($_GET["inicio"]))
	$inicio = $_GET["inicio"];
	
$final = "";
if(isset($_POST["final"]))
	$final = $_POST["final"];
elseif(isset($_GET["final"]))
	$final = $_GET["final"];
	
// $inicio=date("Y-m-d H:i:s",strtotime("+ 5 hours",strtotime($inicio)));
// $final=date("Y-m-d H:i:s",strtotime("+ 5 hours",strtotime($final)));

$titulo1 = $db->sql_row("SELECT upper(nombre||' : '||informe) as inf FROM informes i LEFT JOIN categorias_informes c ON c.id=i.id_categoria_informe WHERE i.id=".str_replace(".php","",simple_me($ME)));

if($html)
{
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/info/templates/fechas_form_55.php");
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
echo "PASO POR ACA";
$titulos = array("VEHÍCULO", "FECHA", "VELOCIDAD",  "UBICACION");
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

$cond = "";
if($id_vehiculo != "")
	$cond.= " AND v.id='".$id_vehiculo."'";
$consulta = "
		SELECT v.codigo||'/'||v.placa as vehiculo, (gps.tiempo) as tiempo, gps.rumbo, gps.velocidad, ev.nombre as evento, gps.hrposition
		FROM gps_vehi gps 
		LEFT JOIN vehiculos v ON gps.id_vehi=v.id 
		LEFT JOIN eventos ev ON gps.evento=ev.codigo
		WHERE gps.tiempo>='".$inicio."' AND gps.tiempo<='".$final."' ". $cond ." AND gps.velocidad>=$velocidad 
		AND gps.velocidad<=110 and v.id_centro ='".$centro."'
		ORDER BY gps.id";
#echo $consulta;
$qid = $db->sql_query($consulta);
while($p = $db->sql_fetchrow($qid))
{
		$linea = array($p["vehiculo"], $p["tiempo"], $p["velocidad"], $p["hrposition"]);
		if($html)
		{
			imprimirLinea($linea,"",$styles);
		}
		else
		{
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, $styles);
		}

}

//final

?>
