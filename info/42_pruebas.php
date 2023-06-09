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

if(isset($_GET["format"])) 
{
	$html=false;
	$inicio = $_GET["inicio"];
	$final = $_GET["final"];
}

//$titulo1 = $db->sql_row("SELECT upper(nombre||' : '||informe) as inf FROM informes i LEFT JOIN categorias_informes c ON c.id=i.id_categoria_informe WHERE i.id=".str_replace(".php","",simple_me($ME)));
$titulo1 = "";

if($html)
{
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/info/templates/fechas_form.php");
	tablita_titulos($titulo1["inf"],$inicio." / ".$final);
	$estilosText = array(1=>"align='left'", 2=>"align='left'");
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
	$estilosText = array(1=>"txt_izq", 2=>"txt_izq");
}

$turnos = array();
$qidTurno = $db->sql_query("SELECT t.* 
	FROM turnos t 
	LEFT JOIN empresas e ON e.id=t.id_empresa 
	LEFT JOIN centros c ON c.id_empresa=e.id 
	WHERE c.id=".$centro." 
	ORDER BY t.id");
while($queryTurnos = $db->sql_fetchrow($qidTurno))
{
	$turnos[$queryTurnos["id"]] = $queryTurnos["turno"];
}

preguntar($turnos);

$lineaPrimera = array("FECHA", "D�A");
foreach($turnos as $tn)
{
	$lineaPrimera = array_merge($lineaPrimera, array("TURNO ".strtoupper($tn)."<br />NUMERO RUTAS A CUBRIR", "TURNO ".strtoupper($tn)."<br />NUMERO RUTAS CUBIERTAS", "TURNO ".strtoupper($tn)."<br />NUMERO RUTAS EN EL HORARIO"));
}
$lineaPrimera = array_merge($lineaPrimera, array("TOTAL A CUBRIR", "TOTAL CUBIERTAS", "TOTAL EN HORARIO", "%CUMPLIMIENTO", "%CUMPLIMIENTO HORARIOS"));

if($html)
{
	echo '<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
			<tr>';
		foreach($lineaPrimera as $ln)
			echo '<th height="40" valign="top">'.$ln.'</th>';
	echo "</tr>";
}else
{
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, $lineaPrimera);
	$fila++;$columna=0;
}

$diasBTW = restarFechas($final,$inicio);
$dxGraf = array("data"=>array(),  "labels"=>array());

$dx = $total = array();
//$serv = array("rec","bar");
$serv = array("rec");
for($i=0 ; $i<=$diasBTW; $i++)
{
	list($anio,$mes,$dia)=split("-",$inicio);
	$fecha = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + $i * 24 * 60 * 60);
	$linea = array($fecha, ucfirst(strftime("%a",strtotime($fecha))));
	$ultimoDia = strftime("%Y-",strtotime($fecha)).strftime("%m-",strtotime($fecha)).ultimoDia(strftime("%m",strtotime($fecha)), strftime("%Y",strtotime($fecha)));
	

	foreach($serv as $squema)
	{
		$dia = strftime("%u",strtotime($fecha)); 
		foreach($turnos as $idTurno => $tn)
		{
			//numero rutas a cubrir
			$qidNRA = $db->sql_row("SELECT count(f.id) as num
				FROM micros_frecuencia f 
				LEFT JOIN micros m ON m.id=f.id_micro
				LEFT JOIN ases a ON a.id=m.id_ase
				LEFT JOIN servicios s ON s.id=m.id_servicio
				WHERE f.dia = '".$dia."' AND f.id_turno = '".$idTurno."' AND a.id_centro='".$centro."' AND s.esquema='".$squema."'");
			

		echo "SELECT m.codigo, f.*
				FROM micros_frecuencia f 
				LEFT JOIN micros m ON m.id=f.id_micro
				LEFT JOIN ases a ON a.id=m.id_ase
				LEFT JOIN servicios s ON s.id=m.id_servicio
				WHERE f.dia = '".$dia."' AND f.id_turno = '".$idTurno."' AND a.id_centro='".$centro."' AND s.esquema='".$squema."' 
				ORDER BY m.codigo<br>";



			if(!isset($dx[$fecha][$idTurno]["racubrir"])) $dx[$fecha][$idTurno]["racubrir"]=0;
			$dx[$fecha][$idTurno]["racubrir"]+=nvl($qidNRA["num"]);
			if(!isset($dx[$fecha][$idTurno]["rcubiertas"])) $dx[$fecha][$idTurno]["rcubiertas"]=0;
			if(!isset($dx[$fecha][$idTurno]["rhorario"])) $dx[$fecha][$idTurno]["rhorario"]=0;
	
			//rutas cubiertas y rutas en horario
			$qid = $db->sql_query("SELECT m.id as id_movimiento, m.inicio, m.final, m.id_micro, '".$squema."' as esquma
				FROM $squema.movimientos m
				LEFT JOIN micros r ON r.id = m.id_micro
				LEFT JOIN ases a ON a.id = r.id_ase
				WHERE m.final IS NOT NULL AND m.inicio::date = '".$fecha."' AND m.id_turno = '".$idTurno."' AND a.id_centro = '".$centro."'");
/*
			echo "SELECT m.id as id_movimiento, m.inicio, m.final, m.id_micro, r.codigo
				FROM $squema.movimientos m
				LEFT JOIN micros r ON r.id = m.id_micro
				LEFT JOIN ases a ON a.id = r.id_ase
				WHERE m.final IS NOT NULL AND m.inicio::date = '".$fecha."' AND m.id_turno = '".$idTurno."' AND a.id_centro = '".$centro."'
				ORDER BY r.codigo, m.inicio<br>";
*/
			while($mov = $db->sql_fetchrow($qid))
			{
				
				$dx[$fecha][$idTurno]["rcubiertas"]+=1;

				$frec = $db->sql_row("SELECT * FROM micros_frecuencia WHERE id_micro='".$mov["id_micro"]."' AND id_turno = '".$idTurno."' AND dia='".$dia."'");
				if(isset($frec["id"]))
				{
					$fi = $fecha." ".$frec["hora_inicio"];
					if($frec["hora_inicio"] < $frec["hora_fin"])
						$ff = $fecha." ".$frec["hora_fin"];
					else
					{
						list($anio2,$mes2,$dia2)=split("-",$fecha);
						$ff = date("Y-m-d",mktime(0,0,0, $mes2,$dia2,$anio2) + 1 * 24 * 60 * 60)." ".$frec["hora_fin"];;
					}

					if($mov["final"] <= $ff)
						$dx[$fecha][$idTurno]["rhorario"]+=1;	
				}
			}
		}
	}
	
	//preguntar($dx);

	$racubir = $rcubiertas = $rhorario = 0;
	foreach($turnos as $idTurno => $tn)
	{
		if(!isset($total[strftime("%Y-%m",strtotime($fecha))][$idTurno]["racubrir"])) $total[strftime("%Y-%m",strtotime($fecha))][$idTurno]["racubrir"] = 0;
		if(!isset($total[strftime("%Y-%m",strtotime($fecha))][$idTurno]["rcubiertas"])) $total[strftime("%Y-%m",strtotime($fecha))][$idTurno]["rcubiertas"] = 0;
		if(!isset($total[strftime("%Y-%m",strtotime($fecha))][$idTurno]["rhorario"])) $total[strftime("%Y-%m",strtotime($fecha))][$idTurno]["rhorario"] = 0;

		if(isset($dx[$fecha][$idTurno]))
		{
			$racubir +=nvl($dx[$fecha][$idTurno]["racubrir"]);
			$rcubiertas +=nvl($dx[$fecha][$idTurno]["rcubiertas"]);
			$rhorario +=nvl($dx[$fecha][$idTurno]["rhorario"]);
			$linea = array_merge($linea, array(nvl($dx[$fecha][$idTurno]["racubrir"]), nvl($dx[$fecha][$idTurno]["rcubiertas"]), nvl($dx[$fecha][$idTurno]["rhorario"])));
		}

		$total[strftime("%Y-%m",strtotime($fecha))][$idTurno]["racubrir"] += nvl($dx[$fecha][$idTurno]["racubrir"]);
		$total[strftime("%Y-%m",strtotime($fecha))][$idTurno]["rcubiertas"] += nvl($dx[$fecha][$idTurno]["rcubiertas"]);
		$total[strftime("%Y-%m",strtotime($fecha))][$idTurno]["rhorario"] += nvl($dx[$fecha][$idTurno]["rhorario"]);
	}

	@$pcr = ($rcubiertas/$racubir)*100;
	@$pho = ($rhorario/$rcubiertas)*100;
	$linea = array_merge($linea, array($racubir, $rcubiertas, $rhorario, number_format($pcr, 0, ",", ".")."%", number_format($pho, 0, ",", ".")."%"));

	if($html)
		imprimirLinea($linea, "", $estilosText);
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, $estilosText);


	if($fecha == $ultimoDia || $i==$diasBTW)
	{
		$liFi = array("TOTAL ".strtoupper(strftime("%B/%Y",strtotime($fecha))), "");
		$racubir = $rcubiertas = $rhorario = 0;
		foreach($turnos as $idTurno => $tn)
		{
			$liFi = array_merge($liFi, array(nvl($tota[strftime("%Y-%m",strtotime($fecha))][$idTurno]["racubrir"]), nvl($tota[strftime("%Y-%m",strtotime($fecha))][$idTurno]["rcubiertas"]), nvl($total[strftime("%Y-%m",strtotime($fecha))][$idTurno]["rhorario"])));
			$racubir+=nvl($total[strftime("%Y-%m",strtotime($fecha))][$idTurno]["racubrir"]);
			$rcubiertas+= nvl($total[strftime("%Y-%m",strtotime($fecha))][$idTurno]["rcubiertas"]);
			$rhorario+=nvl($total[strftime("%Y-%m",strtotime($fecha))][$idTurno]["rhorario"]);
		}

		@$pcr = ($rcubiertas/$racubir)*100;
		@$pho = ($rhorario/$rcubiertas)*100;
		$liFi = array_merge($liFi, array($racubir, $rcubiertas, $rhorario, number_format($pcr, 0, ",", ".")."%", number_format($pho, 0, ",", ".")."%"));

		if($html)
			imprimirLinea($liFi, "#b2d2e1", $estilosText);
		else
			imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $liFi, $estilosTit);

		$dxGraf["data"][] = $pho;
		$dxGraf["labels"][] = strftime("%Y-%m",strtotime($fecha));
		$dxGraf["metas"]["Cr�tico"][] = 88;
		$dxGraf["metas"]["Intermedio"][] = 90;
		$dxGraf["metas"]["Satisfactorio"][] = 100;
	}
}

//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro;
	echo "</table><br /><br />";
	graficaIndicadores($dxGraf, "CUMPLIMIENTO HORARIOS RECOLECCI�N: ".$inicio."/".$final);
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