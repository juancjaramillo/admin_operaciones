<?
include("../application.php");
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

$titulosUno = array("FECHA", "VEHICULO", "ASE", "RUTA", "HORA SALIDA BASE");
if($html)
{
	echo '
		<table width="58%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
			<tr>';
	foreach($titulosUno as $tt)
	{
		echo '<th height="35">'.$tt.'</th>';
	}
	echo "</tr>";
}else
{
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, $titulosUno);
	$fila++;$columna=0;
}

$datos = array();
$qid = $db->sql_query("SELECT m.id,inicio::date as fecmov,v.codigo||' / '||v.placa as vehiculo,a.ase,i.codigo as ruta,
						despla.hora_inicio,m.final
						FROM rec.movimientos m 
						LEFT JOIN (SELECT id_movimiento,min(hora_inicio) as hora_inicio, max(hora_fin) as hora_fin
								FROM rec.desplazamientos 
								where hora_inicio is not null
								group by id_movimiento) despla ON m.id=despla.id_movimiento
						LEFT JOIN micros i ON i.id=m.id_micro 
						LEFT JOIN vehiculos v ON v.id=m.id_vehiculo
						LEFT JOIN ases a ON a.id = i.id_ase
						WHERE inicio::date >= '".$inicio."' AND inicio::date<='".$final."' AND hora_inicio::time >= '03:00:00'
						AND a.id IN (SELECT id FROM ases WHERE id_centro = ".$centro.")
						and despla.hora_inicio is not null
						group by m.id,m.inicio,vehiculo,ase,ruta,despla.hora_inicio,m.final
						order by fecmov,despla.hora_inicio	");

while($query = $db->sql_fetchrow($qid))
{
	$datos[$query["id"]]["id"][] = $query;
	$datos[$query["id"]]["fecmov"]=$query["fecmov"];
	$datos[$query["id"]]["vehiculo"]=$query["vehiculo"];
	$datos[$query["id"]]["ase"]=$query["ase"];
	$datos[$query["id"]]["ruta"]=$query["ruta"];
	$datos[$query["id"]]["hora_inicio"]=$query["hora_inicio"];
	$datos2='';
}

foreach($datos as $id => $dx)
{
	$rowspan=count($dx["id"]);
	$i = 1;
	foreach($dx["id"] as $id)
	{
		if($i==1)
		{
			$linea = array($dx["fecmov"],$dx["vehiculo"],$dx["ase"],$dx["ruta"],$dx["hora_inicio"]);
			
			if($html)
				imprimirLinea($linea, "", array(1=>"align='center' rowspan='".$rowspan."'", 2=>" align='center' rowspan='".$rowspan."'", 3=>"align='center' rowspan='".$rowspan."'", 4=>"align='center' rowspan='".$rowspan."'", 5=>"rowspan='".$rowspan."'"));	
			else
				imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_center"), array(), array(1=>$rowspan-1, 2=>$rowspan-1, 3=>$rowspan-1, 4=>$rowspan-1, 5=>$rowspan-1));
		}
		
		$i++;
	}
}

//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro;
	echo "</table>";
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

