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

$tipos = $titulosDos = array("t"=>array(), "h"=>array());
$qidDes = $db->sql_query("SELECT * FROM rec.tipos_desplazamientos ORDER BY orden");
while($queryTP = $db->sql_fetchrow($qidDes))
{
	if($queryTP["informe_tiempos_operacion_tiempos"] == "t")
	{
		$tipos["t"][] = $queryTP["id"];
		$titulosDos["t"][] = $queryTP["tipo"];
	}
	if($queryTP["informe_tiempos_operacion_horas_inicio"] == "t")
	{
		$tipos["h"][] = array("id"=>$queryTP["id"],"hora"=>"i");
		$titulosDos["h"][] = "Inicio ".$queryTP["tipo"];
	}
	if($queryTP["informe_tiempos_operacion_horas_final"] == "t")
	{
		$tipos["h"][] = array("id"=>$queryTP["id"],"hora"=>"f");
		$titulosDos["h"][] = "Fin ".$queryTP["tipo"];
	}
}

$titulosUno = array("FECHA", "RUTA", "VEHÍCULO", "VIAJE", "TIQUETE ENTRADA", "TONS", "TIEMPOS", "HORAS", "MIN/TON");
if($html)
{
	echo '
		<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
			<tr>';
	foreach($titulosUno as $tt)
	{
		if($tt == "TIEMPOS")
		{
			$cols = count($tipos["t"])+2;	
			echo '<th height="40" colspan="'.$cols.'">'.$tt.'</th>';
		}
		elseif($tt == "HORAS")
		{
			$num = count($tipos["h"]);
			echo '<th height="40" colspan="'.$num.'">'.$tt.'</th>';
		}
		else
			echo '<th height="40" rowspan=2>'.$tt.'</th>';
	}
	echo "</tr><tr>";
	foreach($titulosDos["t"] as $tt)
		echo '<th height="40">'.$tt.'</th>';
	echo '<th height="40">TOTAL RUTA</th>';
	echo '<th height="40">TOTAL OPERACION</th>';
	foreach($titulosDos["h"] as $tt)
		echo '<th height="40">'.$tt.'</th>';
	echo "</tr>";
}else
{
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, array("FECHA", "RUTA", "VEHÍCULO","VIAJE","TIQUETE ENTRADA", "TONS"),0,1);
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, array("TIEMPOS"),count($tipos["t"])+1);
	$columna = $columna+count($tipos["t"])+1;
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, array("HORAS"),count($tipos["h"])-1);
	$columna = $columna+count($tipos["h"])-1;
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, array("MIN/TON"),0,1);
	$fila++;$columna=5;
	titulos_uno_xls($workbook, $worksheet, $fila, $columna,$titulosDos["t"]);
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, array("TOTAL RUTA"));
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, array("TOTAL OPERACIÓN"));
	titulos_uno_xls($workbook, $worksheet, $fila, $columna,$titulosDos["h"]);
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

$qid = $db->sql_query("SELECT m.id, to_char(m.inicio,'YYYY-MM-DD') as inicio, i.codigo, v.codigo||'/'||v.placa as vehiculo
		FROM rec.movimientos m
		LEFT JOIN micros i ON i.id=m.id_micro
		LEFT JOIN ases a ON a.id=i.id_ase
		LEFT JOIN servicios s ON s.id=i.id_servicio
		LEFT JOIN vehiculos v ON v.id=m.id_vehiculo
		WHERE esquema != 'bar' AND inicio::date>='".$inicio."' AND inicio::date <= '".$final."'  $cond
		ORDER BY inicio, i.codigo");
while($mov = $db->sql_fetchrow($qid))
{
	$qidViaj = $db->sql_query("SELECT distinct(numero_viaje) AS num FROM rec.desplazamientos WHERE id_movimiento='".$mov["id"]."' ORDER BY numero_viaje");
	while($viaje = $db->sql_fetchrow($qidViaj))
	{
		$linea = array($mov["inicio"], $mov["codigo"], $mov["vehiculo"], $viaje["num"]);
		$tiquete = averiguarTiqueteXpeso($mov["id"],$viaje["num"],true,$id_turno);
		$linea[] = $tiquete;
		$peso = averiguarPesoXMov($mov["id"],$viaje["num"],true,$id_turno);
		$linea[] = number_format($peso,3);
		$totalTiempos = "00:00:00";

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
				$totalTiempos = SumaHoras($totalTiempos,$mn);
				$linea[] = $mn;
				$totalOperacion = SumaHoras($totalOperacion,$mn);
			}else
			{
				if($html)
					$linea[] = "&nbsp;";
				else
					$linea[] = "";
			}
		}
		
		$dxGraf[$mov["codigo"]."/".$mov["vehiculo"]."/Viaje ".$viaje["num"]] ["data"][] = $totalTiempos;
		$dxGraf[$mov["codigo"]."/ Viaje ".$viaje["num"]] ["labels"][] = ucfirst(strftime("%b.%d.%Y",strtotime($mov["inicio"])));

		$movruta = conversor_segundos(tiempoMovimientoRuta($mov["id"], false, $viaje["num"]));;
		$linea[] = $movruta;
		$totalRuta = SumaHoras($totalRuta,$movruta);

		$linea[] = $totalTiempos;

		foreach($tipos["h"] as $tt)
		{
			if(isset($des[$tt["id"]]))
			{
				if($tt["hora"] == "i")
					$linea[] = $des[$tt["id"]]["hora_inicio"];
				else
					$linea[] = $des[$tt["id"]]["hora_fin"];
			}else
			{
				if($html)
					$linea[] = "&nbsp;";
				else
					$linea[] = "";
			}
		}

		$max = $db->sql_row("SELECT max(hora_fin) as fin, min(hora_inicio) as ini 
				FROM rec.desplazamientos 
				WHERE id_movimiento='".$mov["id"]."' AND numero_viaje='".$viaje["num"]."' AND hora_inicio IS NOT NULL AND hora_fin IS NOT NULL ");
		$totalMin = restarFechasConHHmmss($max["fin"], $max["ini"]);
		@$linea[] = number_format($totalMin/$peso, 2, ",", ".");
		if($html)
			 imprimirLinea($linea);	
		else
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea);
	}
}


if($html)
{
	$cols = count($tipos["t"])+4;
	echo '<tr><th height="40" colspan="'.$cols.'">TOTALES</th><th>'.$totalRuta.'</th><th>'.$totalOperacion.'</th></tr>';
}else
{
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, array("TOTALES"),count($tipos["t"])+3);
	$columna = $columna+count($tipos["t"])+3;
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, array($totalRuta, $totalOperacion));
}




//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro."&id_turno=".$id_turno."&id_ase=".$id_ase;
	echo "</table><br /><br /><table  width='98%'>";

	$i=1;
	foreach($dxGraf  as $key => $data)
	{
		if($i==1) echo "<tr>";
		echo "<td width='50%'>";
		//$graf = array("data" => $data, "labels" => array_values($dxGraf["labels"]));
		graficaBarras($data, "TIEMPO DE OPERACION RUTA ".$key, "Tiempo (hora)", "Día", "Hora");
		echo "</td>";
		if($i==2)
		{
			echo "</tr>";
			$i=0;
		}
		$i++;
	}
	
	echo "
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
