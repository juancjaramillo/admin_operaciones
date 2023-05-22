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

$id_punto_control = "";
if(isset($_POST["id_punto_control"]) && $_POST["id_punto_control"] != "")
	$id_punto_control = $_POST["id_punto_control"];
elseif(isset($_GET["id_punto_control"]) && $_GET["id_punto_control"] != "")
	$id_punto_control = $_GET["id_punto_control"];

$id_vehiculo = "";
if(isset($_POST["id_vehiculo"]))
	$id_vehiculo = $_POST["id_vehiculo"];
elseif(isset($_GET["id_vehiculo"]))
	$id_vehiculo = $_GET["id_vehiculo"];

$radio = "40";
if(isset($_POST["radio"]))
	$radio = $_POST["radio"];
elseif(isset($_GET["radio"]))
	$radio = $_GET["radio"];

$order = nvl($_GET["order"], nvl($_POST["order"],"g.tiempo"));


$modo = nvl($_GET["format"], nvl($_POST["modo"]));

if($modo == "xls")
{
	$html=false;
	$inicio = $_GET["inicio"];
	$final = $_GET["final"];
}

$titulo1 = $db->sql_row("SELECT upper(nombre||' : '||informe) as inf FROM informes i LEFT JOIN categorias_informes c ON c.id=i.id_categoria_informe WHERE i.id=".str_replace(".php","",simple_me($ME)));

if($html)
{
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/info/templates/fechas_form_44.php");
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
	$estilosTit = array(1=>"azul_izq");
	$estilosText = array(1=>"txt_izq");
}

$lineaPrimera = array("FECHA", "PUNTO INTERÉS", "VEHÍCULO", "POSICIÓN",  "DISTANCIA (m)");
if($html)
{
	echo '
		<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
			<tr>';
		foreach($lineaPrimera as $dx)
			echo '<th height="40">'.$dx.'</th>';
	echo "</tr>";
}else
{
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, $lineaPrimera);
	$fila++;$columna=0;
}

//		imprimirLinea($datos["dia"],"#b2d2e1");
//		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $datos["viajes"], $estilosText);



$estilosText = array( 1=>"align='center'", 2=>"align='center'", 3=>"align='center'", 4=>"align='center'", 5=>"align='center'");

//0.0000089827 x el número de metros => grados
//if(isset($_GET["mode"]) || isset($_POST["mode"]))

//preguntar($modo);die;

if($modo == "xls" || $modo=="resultados")
{
	$grados = $CFG->metrosXgrado * $radio;
	$punto = $db->sql_row("SELECT punto, x(the_geom) as x, y(the_geom) as y FROM puntos_interes WHERE id=".$id_punto_control);
		
	$cond = "";
	if($id_vehiculo != "")
		$cond = " AND v.id = '".$id_vehiculo."'";
	else
		$cond = " AND v.id_centro='".$centro."'";

	$consulta = "SELECT g.tiempo,  v.codigo||'/'||v.placa as codigo, distance(GeometryFromText('POINT(".$punto["x"]." ".$punto["y"].")',4326),gps_geom) as distancia, g.hrposition
		FROM gps_vehi  g
		LEFT JOIN vehiculos v ON v.idgps::bigint = g.id_vehi
		WHERE g.tiempo>= '".$inicio."' ".$cond." AND g.tiempo<='".$final."' AND distance(GeometryFromText('POINT(".$punto["x"]." ".$punto["y"].")',4326),gps_geom)<".$grados."
		ORDER BY ".$order;
	$qid = $db->sql_query($consulta);
	while($query = $db->sql_fetchrow($qid))
	{
		$linea = array($query["tiempo"], $punto["punto"], $query["codigo"], $query["hrposition"], number_format($query["distancia"]/0.0000089827));


		if($html)
			imprimirLinea($linea, "", $estilosText);
		else
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea);
	}
}

//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro."&id_punto_control=".$id_punto_control."&id_vehiculo=".$id_vehiculo."&radio=".$radio."&order=".$order;
	echo "</table><br /><br />";
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