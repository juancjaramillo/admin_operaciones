<?
// barrido : longitudes x coordinador
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
	titulo_grande_xls($workbook, $worksheet, 0, 10, $titulo1["inf"]."\n".$inicio." / ".$final);
}
$user=$_SESSION[$CFG->sesion]["user"];

$idsCoor = array(0);
if(isset($_POST["id_coordinador"]))
	$idsCoor = array_merge($idsCoor, $_POST["id_coordinador"]);
if(isset($_GET["id_coordinador"]))
	$idsCoor = explode(",",$_GET["id_coordinador"]);

if(implode(",",$idsCoor) == "0")
{
	$cargos = array(8);
	obtenerIdCargos(8,$cargos);
	$qidCoord = $db->sql_query("SELECT id, nombre||' '||apellido as nombre 
		FROM personas WHERE id IN (SELECT id_persona FROM personas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."')) AND id_cargo IN (".implode(",",$cargos).") 
		ORDER BY nombre,apellido");
	while($coo = $db->sql_fetchrow($qidCoord)){
		$idsCoor[] = $coo["id"];
	}
}

$stilos=array(1=>"align='center'", 2=>"align='center'", 3=>"align='center'");
$stilos2=array(1=>"align='center' colspan=3");

$coord = $micros = array();
$qid = $db->sql_query("SELECT to_char(m.inicio,'YYYY-MM-DD') as inicio, i.km, i.id_coordinador, trim(p.nombre) ||' '||trim(p.apellido) as coord, i.id as id_micro
		FROM bar.movimientos m
		LEFT JOIN micros i ON i.id=m.id_micro
		LEFT JOIN personas p ON p.id=i.id_coordinador
		WHERE inicio::date>='".$inicio."' AND inicio::date <= '".$final."' AND i.id_coordinador IN (".implode(",",$idsCoor).")
		ORDER BY inicio");
while($mov = $db->sql_fetchrow($qid))
{
	$sem = strftime("%V",strtotime($mov["inicio"]));
	$coord[$mov["id_coordinador"]]=$mov["coord"];
	if(!isset($micros[$sem][$mov["inicio"]][$mov["id_coordinador"]]))
		$micros[$sem][$mov["inicio"]][$mov["id_coordinador"]] = 0;

	$micros[$sem][$mov["inicio"]][$mov["id_coordinador"]]+= $mov["km"];
}

$dxLabelGraf = "";
if($html)
{
	echo '
	<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
		<tr>
			<th height="40">SEMANA</th>
			<th>FECHA</th>  
			<th>DÍA</th>';

			foreach($coord as $dx)
			{
				echo '<th>Metros '.strtoupper($dx).'</th>';
				$dxLabelGraf.='"'.str_replace(" ",".",$dx).'"'."\t";
			}
			echo '<th>TOTALES</th>
		</tr>';
}else
{
	$titulos = array("SEMANA", "FECHA", "DÍA");
	foreach($coord as $dx)
	{
		$titulos[] = 'Metros '.strtoupper($dx);
	}
	$titulos[] = 'TOTALES';
	$fila=2; $columna=0;
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, $titulos);
	$fila++;$columna=0;
}

$totalSupervisor = $dxGraf = array();
foreach($micros as $sem => $dxSemana)
{
	$totSem = array();
	foreach($dxSemana as $fecha => $dx)
	{
		$totalFecha = 0;
		$dia = ucfirst(strftime("%A",strtotime($fecha)));
		$linea = array($sem, $fecha, $dia);
	
		foreach($coord as $idCoord => $dxCoord)
		{
			if(isset($dx[$idCoord]))
			{
				$linea[] = $dx[$idCoord];
				$totalFecha+=$dx[$idCoord];
				if(!isset($totalSem[$sem][$idCoord]))
					$totalSem[$sem][$idCoord]=0;
				$totalSem[$sem][$idCoord]+=$dx[$idCoord];
			}
			else
				$linea[] = "0";
		}
		$linea[] = $totalFecha;


		if($html)
			imprimirLinea($linea, "", $stilos);
		else
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_center", 2=>"txt_center", 3=>"txt_center"));
	}

	$dxGraf["labels"][] = "Semana ".$sem;
	if($html)
		$linea = array("Total Semana ".$sem);
	else
	{
		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array("Total Semana ".$sem), array(1=>"azul_center"), 2);
		$columna+=2;
		$linea = array();
	}

	//$lineaGraf = '"Semana '.$sem.'",N';
	$total = 0;
	foreach($coord as $idCoord => $dxCoord)
	{
		if(isset($totalSem[$sem][$idCoord]))
		{
			$linea[] = $totalSem[$sem][$idCoord];
			$total+=$totalSem[$sem][$idCoord];
			if(!isset($totalSuper[$idCoord])) $totalSuper[$idCoord]=0;
			$totalSuper[$idCoord] += $totalSem[$sem][$idCoord];
			$dxGraf["data"][$dxCoord][]=$totalSem[$sem][$idCoord];

		}
		else
		{
			$linea[] = "0";
			$dxGraf["data"][$dxCoord][]=0;
		}
	}


	$linea[] = $total;
	if($html)
		imprimirLinea($linea, "#b2d2e1", $stilos2);
	else
	{
		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $linea);
		$fila++;$columna=0;
	}
}

if($html)
	$linea = array("TOTAL RECORRIDO");
	else
{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, array("RECORRIDO"), array(1=>"azul_center"), 2);
	$columna+=2;
	$linea = array();
}
$total = 0;
foreach($coord as $idCoord => $dxCoord)
{
	if(isset($totalSuper[$idCoord]))
	{
		$total+=$totalSuper[$idCoord];
		$linea[] = $totalSuper[$idCoord];
	}
	else
		$linea[] = 0;
}
$linea[] = $total;
if($html)
	imprimirLinea($linea, "#e8b171", $stilos2);
else
{
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $linea);
	$fila++;$columna=0;
}


//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_coordinador=".implode(",",$idsCoor);
	echo "</table><br /><br />";
	graficaMultiBar($dxGraf, "LONGITUDES POR COORDINADOR: ".$inicio."/".$final, "Totales");
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
