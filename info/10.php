<?
// barrido : consumos diarios
include("../application.php");
$html = true;
if(isset($_GET["format"])) 
{
	$html=false;
	$inicio = $_GET["inicio"];
	$final = $_GET["final"];
}

$titulo1 = $db->sql_row("SELECT upper(nombre||' : '||informe) as inf FROM informes i LEFT JOIN categorias_informes c ON c.id=i.id_categoria_informe WHERE i.id=".str_replace(".php","",simple_me($ME)));
$stilos=array(2=>"align='center'", 3=>"align='left'", 4=>"align='center'");
$qid = $db->sql_query("SELECT * FROM bar.tipos_bolsas");

if($html)
{
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/info/templates/fechas_form.php");
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
	titulo_grande_xls($workbook, $worksheet, 0, 6+$db->sql_numrows($qid), $titulo1["inf"]."\n".$inicio." / ".$final);
}

if($html)
{
	tablita_titulos($titulo1["inf"],$inicio." / ".$final);
	echo '
	<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
		<tr>
			<th height="40" rowspan=2>FECHA</th>
			<th rowspan=2>IDENTIFICACIÓN</th> 
			<th rowspan=2>NOMBRES</th>
			<th rowspan=2>RUTA</th>
			<th rowspan=2>METROS</th>
			<th rowspan=2>HORA INICIO</th>
			<th colspan='.$db->sql_numrows($qid).'>BOLSAS</th>
		</tr>
		<tr>';
}else
{
	$titulos = array("FECHA", "IDENTIFICACIÓN", "NOMBRES", "RUTA", "METROS", "HORA INICIO");
	$fila=2; $columna=0;
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, $titulos,0,1);
	$titulos = array("BOLSAS");
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, $titulos, $db->sql_numrows($qid)-1);
	$fila++;$columna=6;
}


while($query = $db->sql_fetchrow($qid))
{
	if($html)
		echo "<th>".strtoupper($query["tipo"])."</th>";
	else
	{
		titulos_uno_xls($workbook, $worksheet, $fila, $columna, array($query["tipo"]));
	}
	$bol[$query["id"]]= $query["tipo"];
}

if($html)
	echo "</tr>";
else
	$fila++;$columna=0;

$dxGrafBolsa = array();
$qid = $db->sql_query("SELECT mov.id, to_char(mov.inicio,'YYYY-MM-DD') as fecha, to_char(mov.inicio,'HH24:MI:SS') as hora, i.codigo as ruta, i.km
		FROM bar.movimientos mov
		LEFT JOIN micros i ON i.id=mov.id_micro
		WHERE inicio::date>='".$inicio."' AND inicio::date <= '".$final."'
		ORDER BY mov.inicio");
while($query = $db->sql_fetchrow($qid))
{
	$personas = $ident = $bolsas = array();
	$qidPer = $db->sql_query("SELECT cedula, p.nombre||' '||p.apellido as nombre,p.id
			FROM bar.movimientos_personas mp 
			LEFT JOIN personas p ON p.id=mp.id_persona 
			WHERE mp.id_movimiento=".$query["id"]);
	while($per = $db->sql_fetchrow($qidPer))
	{
		$personas[$per["id"]] = $per["nombre"];
		$ident[$per["id"]] = $per["cedula"];
	}

	$qidBolsas = $db->sql_query("SELECT numero_inicio-numero_fin as num, id_tipo_bolsa FROM bar.movimientos_bolsas WHERE  id_movimiento=".$query["id"]);
	while($queryBol=$db->sql_fetchrow($qidBolsas))
	{
		if(!isset($bolsas[$queryBol["id_tipo_bolsa"]])) $bolsas[$queryBol["id_tipo_bolsa"]] = 0;
		$bolsas[$queryBol["id_tipo_bolsa"]]+=$queryBol["num"];
	}

	$linea = array($query["fecha"], implode(", ",$ident), implode(", ",$personas), $query["ruta"], number_format($query["km"]*1000,1,",","."), $query["hora"]);
	foreach($bol as $idBolsa => $dx)
	{
		if(isset($bolsas[$idBolsa])) 
		{
			$linea[] = $bolsas[$idBolsa];
			$dxGrafBolsa[$idBolsa]["labels"][] = $query["fecha"]."\n".devolverIniciales(implode(", ",$personas));
			$dxGrafBolsa[$idBolsa]["data"][] = $bolsas[$idBolsa];
		}
		else $linea[] = "";
	}

	if($html)
		imprimirLinea($linea, "", $stilos);
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq", 2=>"txt_center", 3=>"txt_izq", 4=>"txt_center"));
}

if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final;
	echo "</table><br /><br /><table width='98%' align='center'>";
	echo "<tr>";
	$i=0;
	foreach($bol as $idBolsa => $dx)
	{
		if(isset($dxGrafBolsa[$idBolsa]))
		{
			echo "<br /><br /><td align='center'>";
			 graficaBarras($dxGrafBolsa[$idBolsa],  "CONSUMO BOLSA ".strtoupper($dx), "Num. Bolsas", "uno","dos");
			echo "</td>";
			$i++;
		}

		if($i==2)
			echo "</tr><tr>";
	}

	echo "</tr></table>
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
