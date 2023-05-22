<?
// barrido : longitudes x coordinador
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

$id_turno = "";
if(isset($_POST["id_turno"]))
	$id_turno = $_POST["id_turno"];
elseif(isset($_GET["id_turno"]))
	$id_turno = $_GET["id_turno"];

$id_ase = "";
if(isset($_POST["id_ase"]))
	$id_ase = $_POST["id_ase"];
elseif(isset($_GET["id_ase"]))
	$id_ase = $_GET["id_ase"];

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

$titulosUno = array("FECHA", "VEHÍCULO", "RUTA", "VIAJE", "TONS", "GAL", "INICIO VIAJE", "FIN VIAJE", "TRIPULACIÓN", "H/INICIO", "H/FIN");
if($html)
{
	echo '
		<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
			<tr>';
	foreach($titulosUno as $tt)
	{
		echo '<th height="40">'.$tt.'</th>';
	}
	echo "</tr>";
}else
{
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, $titulosUno);
	$fila++;$columna=0;
}


$dxGraf = array();
$cond = "";
if($id_turno != "")
	$cond .= " AND id_turno=".$id_turno;
if($id_ase != "")
	$cond .= " AND a.id = '".$id_ase."'";
else
	$cond .= " AND a.id_centro ='".$centro."'";

 $totalRuta = $totalOperacion = "00:00:00";

$qid = $db->sql_query("SELECT m.id, to_char(m.inicio,'YYYY-MM-DD') as inicio, i.codigo, v.codigo||'/'||v.placa as vehiculo, m.combustible
		FROM rec.movimientos m
		LEFT JOIN micros i ON i.id=m.id_micro
		LEFT JOIN ases a ON a.id=i.id_ase
		LEFT JOIN servicios s ON s.id=i.id_servicio
		LEFT JOIN vehiculos v ON v.id=m.id_vehiculo
		WHERE esquema != 'bar' AND inicio::date>='".$inicio."' AND inicio::date <= '".$final."'  $cond
		ORDER BY inicio, i.codigo");
while($mov = $db->sql_fetchrow($qid))
{
	$linea = array($mov["inicio"], $mov["vehiculo"], $mov["codigo"]);
	$viajes = $db->sql_row("SELECT max(numero_viaje) AS num FROM rec.desplazamientos WHERE id_movimiento=".$mov["id"]);
	$linea[] = $viajes["num"];
	$peso = averiguarPesoXMov($mov["id"],"",true,$id_turno);
	$linea[] = number_format($peso, 2, ",", ".");
	$linea[] = $mov["combustible"];
	$horas = $db->sql_row("SELECT min(hora_inicio) AS inicio, max(hora_fin) as fin FROM rec.desplazamientos WHERE id_movimiento=".$mov["id"]);
	$linea = array_merge($linea, array($horas["inicio"], $horas["fin"]));
	
	$qidPer = $db->sql_query("SELECT u.nombre||' '||u.apellido as persona, mp.hora_inicio, mp.hora_fin
		FROM rec.movimientos_personas mp
		LEFT JOIN personas u ON u.id = mp.id_persona
		WHERE mp.id_movimiento=".$mov["id"]);
	if($db->sql_numrows($qidPer) == 0)
	{
		$linea = array_merge($linea, array("","",""));
		if($html)
			 imprimirLinea($linea, "", array(1=>"align='center'" , 2=>"align='center'", 3=>"align='center'", 4=>"align='center'", 5=>"align='center'", 6=>"align='center'", 7=>"align='center'", 8=>"align='center'"));	
		else
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_center", 2=>"txt_center", 3=>"txt_center", 4=>"txt_center", 5=>"txt_center", 6=>"txt_center", 7=>"txt_center", 8=>"txt_center"));
	}
	else
	{
		$i=1;
		$rowspan = $db->sql_numrows($qidPer);
		while($trip = $db->sql_fetchrow($qidPer))
		{
			if($i==1)
			{
				$linea = array_merge($linea, array($trip["persona"], $trip["hora_inicio"], $trip["hora_fin"]));
				if($html)
					imprimirLinea($linea, "", array(1=>"align='center' rowspan='".$rowspan."'", 2=>"align='center' rowspan='".$rowspan."'", 3=>"align='center' rowspan='".$rowspan."'", 4=>"align='center' rowspan='".$rowspan."'", 5=>"align='center' rowspan='".$rowspan."'", 6=>"align='center' rowspan='".$rowspan."'", 7=>"align='center' rowspan='".$rowspan."'", 8=>"align='center' rowspan='".$rowspan."'", 9=>"align='left'"));	
				else
				{
					imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_center", 2=>"txt_center", 3=>"txt_center", 4=>"txt_center", 5=>"txt_center", 6=>"txt_center", 7=>"txt_center", 8=>"txt_center", 9=>"txt_izq"), array(), array(1=>$rowspan-1, 2=>$rowspan-1, 3=>$rowspan-1, 4=>$rowspan-1, 5=>$rowspan-1, 6=>$rowspan-1, 7=>$rowspan-1, 8=>$rowspan-1));
				}
			}
			else
			{
				$linea = array($trip["persona"], $trip["hora_inicio"], $trip["hora_fin"]);
				if($html)
					imprimirLinea($linea);	
				else
				{
					$linea = array_merge(array("","","","","","","",""),$linea);
					imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array( 9=>"txt_izq"));
				}
			}
			$i++;
		}
	}
}

//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro."&id_turno=".$id_turno."&id_ase=".$id_ase;
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
