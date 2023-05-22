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

$tipos = $titulosDos = array("t"=>array(), "h"=>array());
$qidDes = $db->sql_query("SELECT * FROM rec.tipos_desplazamientos ORDER BY orden");
while($queryTP = $db->sql_fetchrow($qidDes))
{
	if($queryTP["informe_tiempos_operacion_tiempos"] == "t")
	{
		$tipos["t"][] = $queryTP["id"];
		$titulosDos["t"][] = $queryTP["tipo"];
	}
}

$titulosUno = array("VEHÍCULO", "TONS", "TIEMPOS", "MIN/TON");
if($html)
{
	echo '
		<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
			<tr>';
	foreach($titulosUno as $tt)
	{
		if($tt == "TIEMPOS")
		{
			$cols = count($tipos["t"])+1;	
			echo '<th height="40" colspan="'.$cols.'">'.$tt.'</th>';
		}
		else
			echo '<th height="40" rowspan=2>'.$tt.'</th>';
	}
	echo "</tr><tr>";
	foreach($titulosDos["t"] as $tt)
		echo '<th height="40">'.$tt.'</th>';
	echo '<th height="40">TOTAL</th>';
	foreach($titulosDos["h"] as $tt)
		echo '<th height="40">'.$tt.'</th>';
	echo "</tr>";
}else
{
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, array("VEHÍCULO", "TONS"),0,1);
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, array("TIEMPOS"),count($tipos["t"]));
	$columna = $columna+count($tipos["t"]);
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, array("MIN/TON"),0,1);
	$fila++;$columna=2;
	titulos_uno_xls($workbook, $worksheet, $fila, $columna,$titulosDos["t"]);
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, array("TOTAL"));
	$fila++;$columna=0;
}

$dxGraf = $datos = array();
$cond = "";
if($id_turno != "")
	$cond .= " AND id_turno=".$id_turno;
if($id_ase != "")
	$cond .= " AND a.id = '".$id_ase."'";
else
	$cond .= " AND a.id_centro ='".$centro."'";

$qid = $db->sql_query("SELECT m.id, to_char(m.inicio,'YYYY-MM-DD') as inicio, i.codigo, v.codigo||'/'||v.placa as vehiculo, v.id as id_vehiculo
		FROM rec.movimientos m
		LEFT JOIN micros i ON i.id=m.id_micro
		LEFT JOIN ases a ON a.id=i.id_ase
		LEFT JOIN servicios s ON s.id=i.id_servicio
		LEFT JOIN vehiculos v ON v.id = m.id_vehiculo
		WHERE esquema != 'bar' AND inicio::date>='".$inicio."' AND inicio::date <= '".$final."'  $cond
		ORDER BY inicio, i.codigo");
while($mov = $db->sql_fetchrow($qid))
{
	$datos[$mov["id_vehiculo"]]["codigo"] = $mov["vehiculo"];
	if(!isset($datos[$mov["id_vehiculo"]]["peso"])) $datos[$mov["id_vehiculo"]]["peso"]=0;
	if(!isset($datos[$mov["id_vehiculo"]]["ttiempos"])) $datos[$mov["id_vehiculo"]]["ttiempos"]="00:00:00";

	$qidViaj = $db->sql_query("SELECT distinct(numero_viaje) AS num FROM rec.desplazamientos WHERE id_movimiento='".$mov["id"]."' ORDER BY numero_viaje");
	while($viaje = $db->sql_fetchrow($qidViaj))
	{
		$peso = averiguarPesoXMov($mov["id"],$viaje["num"],true,$id_turno);
		$datos[$mov["id_vehiculo"]]["peso"]+=$peso;

		$des = array();
		$qidDes = $db->sql_query("SELECT hora_inicio as fecha_inicio, hora_fin as fecha_fin, to_char(hora_inicio,'HH24:MI:SS') as hora_inicio, to_char(hora_fin,'HH24:MI:SS') as hora_fin, id_tipo_desplazamiento
				FROM rec.desplazamientos 
				WHERE id_movimiento='".$mov["id"]."' AND numero_viaje='".$viaje["num"]."' AND hora_inicio IS NOT NULL AND hora_fin IS NOT NULL ");
		while($queryDes = $db->sql_fetchrow($qidDes))
		{
			$des[$queryDes["id_tipo_desplazamiento"]] = $queryDes;
		}

		foreach($tipos["t"] as $tt)
		{
			if(isset($des[$tt]))
			{
				$mn = conversor_segundos(restarFechasConHHmmss($des[$tt]["fecha_fin"], $des[$tt]["fecha_inicio"],true));
				if(!isset($datos[$mov["id_vehiculo"]]["td"][$tt])) $datos[$mov["id_vehiculo"]]["td"][$tt] ="00:00:00";

				$datos[$mov["id_vehiculo"]]["td"][$tt] = SumaHoras($datos[$mov["id_vehiculo"]]["td"][$tt],$mn);
				$datos[$mov["id_vehiculo"]]["ttiempos"] = SumaHoras($datos[$mov["id_vehiculo"]]["ttiempos"],$mn);
			}
		}
	}
}

foreach($datos as $idVehi => $dx)
{
	$linea = array($dx["codigo"], $dx["peso"]);
	foreach($tipos["t"] as $tt)
	{
		if(isset($dx["td"][$tt]))
			$linea[] = $dx["td"][$tt];
		else
			$linea[] = "";
	}

	$linea[] = $dx["ttiempos"];

	list($hora,$minuto,$seg)=split(":",$dx["ttiempos"]);
	$min = ((3600*$hora) + ($minuto*60) + $seg)/60;
	@$total =  number_format($min/$dx["peso"], 2, ",", ".");
	$linea[] = $total;

	if($html)
		imprimirLinea($linea);	
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea);

	$dxGraf["data"][] = $total;
	$dxGraf ["labels"][] = $dx["codigo"];
}



//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro."&id_turno=".$id_turno."&id_ase=".$id_ase;
	echo "</table><br /><br /><table  width='98%'><tr><td>";
	graficaBarras($dxGraf, "TIEMPOS DE OPERACIÓN", "Min/Ton", "Vehículo", "Min/Ton");
	echo "</td></tr>
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
