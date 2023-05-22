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

$titulos = array("CENTRO / MES");
$qidTM = $db->sql_query("SELECT * FROM mtto.tipos ORDER BY id");
while($queryTM = $db->sql_fetchrow($qidTM))
{
	$tpmttos[$queryTM["id"]] = $queryTM["tipo"];
	$titulos[] = "MANTENIMIENTO ".strtoupper($queryTM["tipo"]);
	$total[$queryTM["id"]] = array("numero"=>0, "valor"=>0);
}

$i=0;
if($html)
{
	echo '
		<table width="68%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
		<tr>';
	$segundaLinea = "";
	foreach($titulos as $tt)
	{
		if($i==0)
			echo '<th height="40" >'.$tt.'</th>';
		else
		{
			echo '<th height="40" >'.$tt.'</th>';
			//$segundaLinea.='<th height="40">Número</th><th height="40">Valor</th>';
		}
		$i++;
	}
	echo "</tr><tr>".$segundaLinea."</tr>";
}else
{
	foreach($titulos as $tt)
	{
		if($i==0)
			titulos_uno_xls($workbook, $worksheet, $fila, $columna, array($tt),0,1);	
		else
		{
			titulos_uno_xls($workbook, $worksheet, $fila, $columna, array($tt),1);	
			$columna++;
		}
		$i++;
	}
	$fila++;$columna=1;
	foreach($tpmttos as $tipo)
	{
		titulos_uno_xls($workbook, $worksheet, $fila, $columna, array("Número","Valor"));
	}
	$fila++;$columna=0;
}

$qid = $db->sql_query("SELECT c.* FROM centros c WHERE c.id IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') ORDER BY centro");
while($query = $db->sql_fetchrow($qid))
{
	$nombre = $query["centro"];
	$queryOT = $db->sql_query("SELECT r.id_tipo_mantenimiento, r.id as id_rutina, o.id as id_orden, c.id as id_centro,
			EXTRACT(YEAR FROM o.fecha_ejecucion_inicio)||' '||to_char(o.fecha_ejecucion_inicio, 'Month') as mes
			FROM mtto.ordenes_trabajo o
			LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
			LEFT JOIN mtto.equipos e ON o.id_equipo=e.id
			LEFT JOIN centros c ON c.id=e.id_centro
			WHERE e.id_centro=".$query["id"]." 
			AND id_estado_orden_trabajo IN (SELECT id FROM mtto.estados_ordenes_trabajo WHERE cerrado) 
			AND o.fecha_ejecucion_inicio::date>='".$inicio."' AND o.fecha_ejecucion_inicio::date<='".$final."'
			order by EXTRACT(YEAR FROM o.fecha_ejecucion_inicio),EXTRACT(MONTH FROM o.fecha_ejecucion_inicio)");
	
	while($qidOT = $db->sql_fetchrow($queryOT))
	{
		if(!isset($datos[$nombre][$qidOT["mes"]][$qidOT["id_tipo_mantenimiento"]]["numero"]))
			$datos[$nombre][$qidOT["mes"]][$qidOT["id_tipo_mantenimiento"]]["numero"]=0;
		if(!isset($datos[$nombre][$qidOT["mes"]][$qidOT["id_tipo_mantenimiento"]]["valor"]))
			$datos[$nombre][$qidOT["mes"]][$qidOT["id_tipo_mantenimiento"]]["valor"] = 0;
		$datos[$nombre][$qidOT["mes"]][$qidOT["id_tipo_mantenimiento"]]["numero"]+=1;
		$tmpmes1[]= $qidOT["mes"];
	}
}
$tmpmes = array_unique($tmpmes1);

$dxGraf = array();
foreach($datos as $nombre => $ot)
{
	$linea = array($nombre);
	$dxGraf[$nombre] = array("data"=>array(),  "labels"=>array());
	
	foreach($tmpmes as $i => $value)
	{
		$mes1=$tmpmes[$i];
		$linea = array($nombre.' / '.$mes1);
		
		foreach($tpmttos as $idTipo => $tipo)
		{
			if(isset($ot[$mes1][$idTipo]))
			{
				$linea = array_merge($linea, array($ot[$mes1][$idTipo]["numero"]));
				$total[$idTipo]["numero"]+=$ot[$mes1][$idTipo]["numero"];
				$dxGraf[$nombre] ["data"][] = $ot[$mes1][$idTipo]["numero"];
			}
			else
			{
				$linea = array_merge($linea, array(0));
				$dxGraf[$nombre] ["data"][] = "";
			}
		}
		if($html)
			imprimirLinea($linea,"",array(1=>"align='left'"));
		else
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
		
	}
}


$linea = array("TOTAL");
foreach($tpmttos as $idTipo => $tipo)
	$linea = array_merge($linea, array($total[$idTipo]["numero"]));
if($html)
	imprimirLinea($linea,"#b2d2e1",array(1=>"align='left' strong"));
else
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"azul_izq"));


if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final;
	echo "</table><br /><br /><table  width='98%'>	";
	$i=1;
	foreach($dxGraf as $key => $data)
	{
		if($i==1) echo "<tr>";
		echo "<td width='50%'>";
		graficaMultibar($data, $key,  "Cantidad","","","",0);
		echo "</td>";
		if($i==2)
		{
			echo "</tr>";
			$i=0;
		}
		$i++;
	}
	echo "</table>
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
