<?
include("../application.php");
$html = true;
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

$titulos = array("CENTRO", "META", "VALOR");
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

$centro = array();
$qid = $db->sql_query("SELECT e.id, e.nombre, c.centro, c.id as id_centro 
		FROM mtto.equipos e 
		LEFT JOIN centros c ON c.id=e.id_centro 
		WHERE c.id IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') 
		ORDER BY c.centro, e.nombre");

while($query = $db->sql_fetchrow($qid))
{
	if(!isset($centro[$query["id_centro"]]))
		$centro[$query["id_centro"]] = array("centro"=>$query["centro"],"realizados"=>0,"presupuestados"=>0);

	//lo que se utilizó en la ordenes de trabajo
	$queryOT = $db->sql_query("SELECT r.id_tipo_mantenimiento, r.id as id_rutina, o.id as id_orden
			FROM mtto.ordenes_trabajo o
			LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
			WHERE o.id_equipo=".$query["id"]." AND id_estado_orden_trabajo IN (SELECT id FROM mtto.estados_ordenes_trabajo WHERE cerrado AND indicadores) 
			AND o.fecha_ejecucion_inicio::date>='".$inicio."' AND o.fecha_ejecucion_inicio::date<='".$final."'");
	while($qidOT = $db->sql_fetchrow($queryOT))
	{
		$centro[$query["id_centro"]]["realizados"]+=costoXOrdenTrabajo($qidOT["id_orden"], $query["id_centro"]);
	}

	//lo que había presupuestado en la ordenes de trabajo
	$queryOT = $db->sql_query("SELECT r.id_tipo_mantenimiento, r.id as id_rutina, o.id as id_orden
			FROM mtto.ordenes_trabajo o
			LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
			WHERE o.id_equipo=".$query["id"]." AND id_estado_orden_trabajo IN (SELECT id FROM mtto.estados_ordenes_trabajo WHERE cerrado AND indicadores) 
			AND o.fecha_ejecucion_inicio::date>='".$inicio."' AND o.fecha_ejecucion_inicio::date<='".$final."'");
	while($qidOT = $db->sql_fetchrow($queryOT))
	{
		$centro[$query["id_centro"]]["presupuestados"]+=costoXRutina($qidOT["id_rutina"],$query["id_centro"]);
	}
}

$dxGraf = array("data"=>array(),  "labels"=>array());
$i=0;
foreach($centro as $dx)
{
	@$total = number_format($dx["realizados"]/$dx["presupuestados"] * 100, 0, ",", ".")."%";
	$linea = array($dx["centro"],"<= 100%",$total);
	if($html)
		imprimirLinea($linea,"",array(1=>"align='left'", 2=>"align='center'", 3=>"align='center'"));
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq", 2=>"txt_center", 3=>"txt_center"));

	$dxGraf["data"][] = $total;
	$dxGraf["labels"][] = $dx["centro"];
	$i++;
}

for($j=0; $j<$i; $j++)
{
		$dxGraf["metas"][""][] = "";
		$dxGraf["metas"]["Meta"][] = 100;
}


if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final;
	echo "</table></td>
			<td valign='top'>";
	graficaIndicadores($dxGraf, "COSTOS MANTENIMIENTO: ".$inicio."/".$final,0xecc1c1 );
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
