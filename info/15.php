<?
// barrido : longitudes x coordinador
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

$titulos = array("PERÍODO", "OT´s PREVENTIVAS", "EJECUTADAS", "EJECUTADAS EN<br />OTRO PERÍODO", "PENDIENTES", "BACKLOG<br />(HORAS)", "INDICADOR<br />DE EJECUCIÓN", "INDICADOR<br />CIERRE OT");
if($html)
{
	echo '
		<table width="78%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
		<tr>';
	foreach($titulos as $tt)
		echo '<th height="40">'.$tt.'</th>';
	echo "</tr>";
}else
{
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, $titulos);
	$fila++;$columna=0;
}


$semana = array();
$first = $inicio;
while($first <= $final)
{
	$s = obtenerSemana($first);
	$key = strftime("%V",strtotime($first));
	if($s["Monday"] < $inicio)
		$s["Monday"] = $inicio;
	if($s["Sunday"] > $final)
		$s["Sunday"] = $final;

	$semana[$key] = $s;

	//siguiente
	list($anio,$mes,$dia)=split("-",$first);
	$first = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + 1 * 24 * 60 * 60);
} 

$datos = array();
foreach($semana as $fecha)
{
	$ini = $fecha["Monday"];
	$fin = $fecha["Sunday"];

	$consulta = "SELECT count(o.id) as num
				FROM mtto.ordenes_trabajo o
				LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
				LEFT JOIN (select id_orden_trabajo,min(fecha) as fecha from mtto.ordenes_trabajo_fechas_programadas group by id_orden_trabajo) p ON p.id_orden_trabajo=o.id
				WHERE r.id IN (SELECT id_rutina FROM mtto.rutinas_centros WHERE id_centro = '".$centro."') and id_tipo_mantenimiento=1";

//solicitadas
		$cond = " AND (case when p.fecha>(case when p.fecha>o.fecha_planeada then o.fecha_planeada else p.fecha end) then (case when p.fecha>o.fecha_planeada then o.fecha_planeada else p.fecha end) else p.fecha end)::date>='".$ini."' AND (case when p.fecha>o.fecha_planeada then o.fecha_planeada else p.fecha end)::date<='".$fin."'";

		$qidSol = $db->sql_row($consulta.$cond);

		$cond = " AND (case when p.fecha>o.fecha_planeada then o.fecha_planeada else p.fecha end)::date>='".$ini."' AND (case when p.fecha>o.fecha_planeada then o.fecha_planeada else p.fecha end)::date<='".$fin."' AND o.id_estado_orden_trabajo IN (SELECT id FROM mtto.estados_ordenes_trabajo WHERE cerrado) AND o.fecha_ejecucion_inicio::date>='".$ini."' AND o.fecha_ejecucion_inicio::date<='".$fin."'";
		$qidEje = $db->sql_row($consulta.$cond);

		$cond = " AND (case when p.fecha>o.fecha_planeada then o.fecha_planeada else p.fecha end)::date>='".$ini."' AND (case when p.fecha>o.fecha_planeada then o.fecha_planeada else p.fecha end)::date<='".$fin."' AND o.id_estado_orden_trabajo IN (SELECT id FROM mtto.estados_ordenes_trabajo WHERE cerrado) AND o.fecha_ejecucion_inicio::date NOT BETWEEN '".$ini."' AND '".$fin."'";
		$qidEjeOtroPer = $db->sql_row($consulta.$cond);

		$pendientes = $qidSol["num"] - $qidEje["num"] - $qidEjeOtroPer["num"];

		$consulta = str_replace("count(o.id)","sum(r.tiempo_ejecucion)",$consulta);
		$cond = " AND (case when p.fecha>o.fecha_planeada then o.fecha_planeada else p.fecha end)::date>='".$ini."' AND (case when p.fecha>o.fecha_planeada then o.fecha_planeada else p.fecha end)::date<='".$fin."' AND o.id_estado_orden_trabajo NOT IN (SELECT id FROM mtto.estados_ordenes_trabajo WHERE cerrado)";
  	$qidBak = $db->sql_row($consulta.$cond);
	
	$backlog = 0;
	if($qidBak["num"] != "" || $qidBak["num"] != "0") $backlog = ($qidBak["num"]/60);

	//indicadores
	$ie = ($qidEje["num"] *100)/$qidSol["num"];
	$ic = (($qidEje["num"]+$qidEjeOtroPer["num"]) * 100)/$qidSol["num"];

	$linea = array($fecha["Monday"]." / ".$fecha["Sunday"], $qidSol["num"], $qidEje["num"], $qidEjeOtroPer["num"], $pendientes, number_format($backlog, 0, ",", "."), number_format($ie, 2, ",", ".")."%", number_format($ic, 2, ",", ".")."%");
	if($html)
		imprimirLinea($linea,"",array(1=>"align='center'", 2=>"align='center'", 3=>"align='center'", 4=>"align='center'", 5=>"align='center'", 6=>"align='center'", 7=>"align='center'", 8=>"align='center'"));
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_center", 2=>"txt_center", 3=>"txt_center", 4=>"txt_center", 5=>"txt_center", 6=>"txt_center", 7=>"txt_center", 8=>"align='center'"));

	$dxGraf["data"][] = $ie;
	$dxGraf["dataDos"][] = $ic;
	$dxGraf["labels"][] = ucfirst(strftime("%b.%d.%Y",strtotime($fecha["Monday"])))."\n".ucfirst(strftime("%b.%d.%Y",strtotime($fecha["Sunday"])));
}

//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro;
	echo "</table><br><br>";
	graficaMultiLine($dxGraf, "ÓRDENES DE TRABAJO: ".$inicio."/".$final, "", "Ejecución", "", "Cierre OT");
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
