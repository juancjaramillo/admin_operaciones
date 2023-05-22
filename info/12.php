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

$id_ase = "";
if(isset($_POST["id_ase"]))
	$id_ase = $_POST["id_ase"];
elseif(isset($_GET["id_ase"]))
	$id_ase = $_GET["id_ase"];

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

$titulos = array("PERÍODO", "META MÍNIMA", "META INTERMEDIA<br />(Entre 97% y 99%)", "META MÁXIMA<br />(Entre 99% y/o<br />Mayor a 99.8%)", "TCT (min)", "TO (min)", "INDICADOR");
if($html)
{
	echo '
		<table width="88%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
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
	// $key = strftime("%V",strtotime($first));
	$key= date('W',strtotime($first));
	if($s["Monday"] < $inicio)
		$s["Monday"] = $inicio;
	if($s["Sunday"] > $final)
		$s["Sunday"] = $final;

	$semana[$key] = $s;
	
	//siguiente
	list($anio,$mes,$dia)=split("-",$first);
	$first = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + 1 * 24 * 60 * 60);
}

$cond = " AND a.id IN (SELECT id FROM ases WHERE id_centro = '".$centro."')";
if($id_ase != "") $cond= " AND a.id='".$id_ase."'";

$datos = array();
$i=0;

foreach($semana as $fecha)
{
	$uno = $fecha["Monday"];
	$OT=$tct=0;

	while($uno <= $fecha["Sunday"])
	{
		//el tiempo de operación 
		$OT+=tiempoOperacionxDia($uno, $centro);
		$datos = array();

		/*TCT = tiempo carro taller
			TCT son las interrupciones una vez iniciada la ruta
			El tiempo de Carrotaller se calcula con las ordenes de trabajo de mantenimiento realizadas mientras el vehículo estaba en operación.
		 */

		$qid = $db->sql_query("SELECT m.id_vehiculo, m.inicio, m.final
				FROM rec.movimientos m
				LEFT JOIN micros i ON i.id=m.id_micro
				LEFT JOIN ases a ON a.id = i.id_ase
				WHERE m.inicio::date='".$uno."' AND m.final IS NOT NULL ".$cond);
		while($veh = $db->sql_fetchrow($qid))
		{
			$cons = "SELECT fecha_ejecucion_inicio, fecha_ejecucion_fin, e.id_vehiculo, to_char(fecha_ejecucion_inicio,'YYYY-MM-DD') as fecha_inicio_sola, o.id
					FROM mtto.ordenes_trabajo o
					LEFT JOIN mtto.equipos e ON e.id=o.id_equipo
					WHERE id_vehiculo = '".$veh["id_vehiculo"]."' AND id_estado_orden_trabajo IN (SELECT id FROM mtto.estados_ordenes_trabajo WHERE cerrado) AND fecha_ejecucion_inicio >= '".$veh["inicio"]."' AND fecha_ejecucion_fin <= '".$veh["final"]."' 
					ORDER BY fecha_ejecucion_inicio";
			$qidOt = $db->sql_query($cons);
			while($ot = $db->sql_fetchrow($qidOt))
			{
				$inter = getIntersection($ot["fecha_ejecucion_inicio"], $ot["fecha_ejecucion_fin"], $veh["inicio"], $veh["final"]);
				if(is_array($inter))
				{
					if($inter['start'] != $inter['end'])
					{
						$keyTCT = $inter['start'] + $inter['end'];
						$datos[$ot["fecha_inicio_sola"]][$veh["id_vehiculo"]][$keyTCT] = array(date('Y-m-d H:i:s', $inter['start']),date('Y-m-d H:i:s', $inter['end']));
					}
				}
			}
		}
		
		if(count($datos)>0)
			$tct += sacarTiemposDisponibilidadFlota($datos);

		@$ind = (1 -($tct)/$OT)*100;

		//siguiente
		list($anio,$mes,$dia)=split("-",$uno);
		$uno = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + 1 * 24 * 60 * 60);
	}

	$linea = array($fecha["Monday"]." / ".$fecha["Sunday"], "97%", "97% - 99.6%", "99.6%",$tct,$OT, number_format($ind, 2, ",", ".")."%");
	
	$dxGraf["data"][] = $ind;
	$dxGraf["labels"][] = ucfirst(strftime("%b.%d.%Y",strtotime($fecha["Monday"])))."\n".ucfirst(strftime("%b.%d.%Y",strtotime($fecha["Sunday"])));

	if($html)
		imprimirLinea($linea,"",array(1=>"align='center'", 2=>"align='center'", 3=>"align='center'", 4=>"align='center'", 5=>"align='center'", 6=>"align='center'", 7=>"align='center'",));
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_center", 2=>"txt_center", 3=>"txt_center", 4=>"txt_center", 5=>"txt_center", 6=>"txt_center", 7=>"txt_center"));

	$i++;
}

for($j=0; $j<$i; $j++)
{
		$dxGraf["metas"]["Meta Mínima"][] = 97;
		$dxGraf["metas"]["Meta Intermedia"][] = 99.6;
		$dxGraf["metas"]["Meta  Máxima"][] = 100;
}

//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro."&id_ase=".$id_ase;
	echo "</table><br><br>";
	graficaIndicadores($dxGraf, "CONFIABILIDAD DE FLOTA: ".$inicio."/".$final);
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
	
$TOxVxD=tiempoOperacionxvehi($inicio,$final,$centro);
$datos = array();
$qid = $db->sql_query("SELECT m.id_vehiculo, m.inicio, m.final
	FROM rec.movimientos m
	LEFT JOIN micros i ON i.id=m.id_micro
	LEFT JOIN ases a ON a.id = i.id_ase
	WHERE m.inicio::date>='$inicio' AND m.inicio::date<='$final' AND m.final IS NOT NULL ".$cond." order by m.id_vehiculo ");
$vehi = 0;	
while($veh = $db->sql_fetchrow($qid))
{
	$cons = "SELECT fecha_ejecucion_inicio, fecha_ejecucion_fin, e.id_vehiculo, to_char(fecha_ejecucion_inicio,'YYYY-MM-DD') as fecha_inicio_sola, o.id
			FROM mtto.ordenes_trabajo o
			LEFT JOIN mtto.equipos e ON e.id=o.id_equipo
			WHERE id_vehiculo = '".$veh["id_vehiculo"]."' AND id_estado_orden_trabajo IN (SELECT id FROM mtto.estados_ordenes_trabajo WHERE cerrado) 
			AND fecha_ejecucion_inicio >= '".$veh["inicio"]."' AND fecha_ejecucion_fin <= '".$veh["final"]."' 
			ORDER BY fecha_ejecucion_inicio";
	//echo $cons;
	$qidOt = $db->sql_query($cons);
	while($ot = $db->sql_fetchrow($qidOt))
	{
		$inter = getIntersection($ot["fecha_ejecucion_inicio"], $ot["fecha_ejecucion_fin"], $veh["inicio"], $veh["final"]);
		if(is_array($inter))
		{
			if($inter['start'] != $inter['end'])
			{
				$keyTCT = $inter['start'] + $inter['end'];
				$datos[$ot["fecha_inicio_sola"]][$veh["id_vehiculo"]][$keyTCT] = array(date('Y-m-d H:i:s', $inter['start']),date('Y-m-d H:i:s', $inter['end']));
			}
		}
	}
	if ($vehi != $veh["id_vehiculo"]){
		$tct = sacarTiemposDisponibilidadFlota($datos);
		$TCTxVh[$vehi] = $tct;
		unset($datos);
	}
	$vehi = $veh["id_vehiculo"];
}


tablita_titulos("CONFIABILIDAD POR VEHICULO ",$inicio." / ".$final);
$titulos = array("VEHÍCULO", "META MÍNIMA", "META INTERMEDIA<br />(Entre 97% y 99%)", "META MÁXIMA<br />(Entre 99% y/o<br />Mayor a 99.8%)", "TCT (min)", "TO (min)", "INDICADOR");
if($html)
{
	echo '
		<table width="70%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
		<tr>';
	foreach($titulos as $tt)
		echo '<th height="40">'.$tt.'</th>';
	echo "</tr>";
}else
{
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, $titulos);
	$fila++;$columna=0;
}

unset($dxGraf);

foreach ($TOxVxD as $clave => $valor) {
	if ($clave!=0){
		$consu = "select   codigo, codigo||'/'||placa as vehi from vehiculos where id =".$clave;
		$qvehi = $db->sql_query($consu);
		while($vh = $db->sql_fetchrow($qvehi))
		{
			$vehiculo = $vh["vehi"];
			$codigo = $vh["codigo"];
		}
		@$ind = (1 -($TCTxVh[$clave])/$TOxVxD[$clave])*100;
		$lineaxvehi = array($vehiculo, "97%", "97% - 99.6%", "99.6%",$TCTxVh[$clave],$TOxVxD[$clave], number_format($ind, 2, ",", ".")."%");
		$dxGraf["data"][] = $ind;
		$dxGraf["labels"][] = $codigo;
		$dxGraf["metas"]["Meta Mínima"][] = 97;
		$dxGraf["metas"]["Meta Intermedia"][] = 99.6;
		$dxGraf["metas"]["Meta  Máxima"][] = 100;
		if($html)
			imprimirLinea($lineaxvehi,"",array(1=>"align='center'", 2=>"align='center'", 3=>"align='center'", 4=>"align='center'", 5=>"align='center'", 6=>"align='center'", 7=>"align='center'",));
		else
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaxvehi, array(1=>"txt_center", 2=>"txt_center", 3=>"txt_center", 4=>"txt_center", 5=>"txt_center", 6=>"txt_center", 7=>"txt_center"));
	}
}	


//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro."&id_ase=".$id_ase;
	echo "</table><br><br>";
	graficaIndicadores($dxGraf, "CONFIABILIDAD DE FLOTA: ".$inicio."/".$final);
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
