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

$titulos = array("CENTRO", "MES", "PROGRAMADOS", "EJECUTADOS", "META", "VALOR");
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

$i=0;
$dxGraf = array("data"=>array(),  "labels"=>array());
$qidC = $db->sql_query("SELECT id,centro FROM centros WHERE id IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') ORDER BY centro");
while($centro = $db->sql_fetchrow($qidC))
{
	$SqlStr = $db->sql_query("select * from (SELECT EXTRACT(year FROM o.fecha_planeada) as ano, 
			EXTRACT(Month FROM o.fecha_planeada) as orden,
			to_char(o.fecha_planeada, 'Month')||' '|| EXTRACT(YEAR FROM o.fecha_planeada) as mes, 
			count(o.id) as realizados 
			FROM mtto.ordenes_trabajo o 
			LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
		 	LEFT JOIN mtto.rutinas_centros rc ON rc.id_rutina=r.id	
			WHERE r.id_tipo_mantenimiento=1 AND (o.fecha_planeada::date>='".$inicio."' AND o.fecha_planeada::date<='".$final."')
			AND EXTRACT(Month FROM o.fecha_ejecucion_inicio)<=(EXTRACT(Month FROM o.fecha_planeada))
			AND o.id_estado_orden_trabajo IN (SELECT id FROM mtto.estados_ordenes_trabajo WHERE indicadores) AND rc.id_centro='".$centro["id"]."' and r.activa=true
			group by ano,orden,mes) a
			LEFT JOIN
			(SELECT EXTRACT(year FROM o.fecha_planeada) as ano1, 
			EXTRACT(Month FROM o.fecha_planeada) as orden1,
			to_char(o.fecha_planeada, 'Month')||' '|| EXTRACT(YEAR FROM o.fecha_planeada) as mes1, 
			count(o.id) as programados
			FROM mtto.ordenes_trabajo o
			LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
			LEFT JOIN mtto.rutinas_centros rc ON rc.id_rutina=r.id
			WHERE r.id_tipo_mantenimiento=1 AND o.fecha_planeada::date>='".$inicio."' AND o.fecha_planeada::date<='".$final."' AND 
			o.id_estado_orden_trabajo IN (SELECT id FROM mtto.estados_ordenes_trabajo WHERE indicadores) AND rc.id_centro='".$centro["id"]."' and r.activa=true
			group by ano1,orden1,mes1) b
			ON a.mes=b.mes1
			ORDER BY ano,orden");
	
	
	$total="";
	while($datos = $db->sql_fetchrow($SqlStr))
	{	
		@$total = number_format($datos["realizados"]/$datos["programados"] * 100, 2, ",", ".")."%";
		$linea = array($centro["centro"],$datos["mes"],$datos["programados"],$datos["realizados"],">= 95%",$total);
		if($html)
			imprimirLinea($linea,"",array(1=>"align='left'", 2=>"align='left'", 3=>"align='center'", 4=>"align='center'", 5=>"align='center'"));
		else
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq", 2=>"txt_izq", 3=>"txt_center", 4=>"txt_center", 5=>"txt_center"));

		$dxGraf["data"][] = $total;
		$dxGraf["labels"][] = $centro["mes"];
	
	}
	$i++;
}

for($j=0; $j<$i; $j++)
{
		$dxGraf["metas"]["< Meta"][] = 95;
		$dxGraf["metas"][""][] = "";
		$dxGraf["metas"]["Meta"][] = 100;
}


if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final;
	echo "</table></td>
			<td valign='top'>";
	graficaIndicadores($dxGraf, "CUMPLIMIENTO MTTO PREVENTIVOS: ".$inicio."/".$final);
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
