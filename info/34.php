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

$titulos = array("LUGAR");
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
		<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
		<tr>';
	$segundaLinea = "";
	foreach($titulos as $tt)
	{
		if($i==0)
			echo '<th height="40" rowspan=2>'.$tt.'</th>';
		else
		{
			echo '<th height="40" colspan=2>'.$tt.'</th>';
			$segundaLinea.='<th height="40">Número</th><th height="40">Valor</th>';
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

$lugares = array("Interno","Externo");
foreach($lugares as $sitio)
{
	$nombre = $sitio;
	$queryOT = $db->sql_query("SELECT r.id_tipo_mantenimiento, r.id as id_rutina, o.id as id_orden, c.id as id_centro
			FROM mtto.ordenes_trabajo o
			LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
			LEFT JOIN mtto.equipos e ON o.id_equipo=e.id
			LEFT JOIN centros c ON c.id=e.id_centro
			WHERE c.id ='".$centro."' AND r.lugar='".$sitio."' AND id_estado_orden_trabajo IN (SELECT id FROM mtto.estados_ordenes_trabajo WHERE cerrado) AND o.fecha_ejecucion_inicio::date>='".$inicio."' AND o.fecha_ejecucion_inicio::date<='".$final."'");
	while($qidOT = $db->sql_fetchrow($queryOT))
	{
		if(!isset($datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["numero"]))
			$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["numero"]=0;
		if(!isset($datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["valor"]))
			$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["valor"] = 0;

		$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["numero"]+=1;
		$datos[$nombre][$qidOT["id_tipo_mantenimiento"]]["valor"]+=costoXOrdenTrabajo($qidOT["id_orden"]);
	}
}

$dxGraf = array();
foreach($datos as $nombre => $ot)
{
	$linea = array($nombre);
	$dxGraf[$nombre] = array("data"=>array(),  "labels"=>array());
	foreach($tpmttos as $idTipo => $tipo)
	{
		if(isset($ot[$idTipo]))
		{
			$linea = array_merge($linea, array($ot[$idTipo]["numero"],number_format($ot[$idTipo]["valor"], 2, ",", ".")));
			$total[$idTipo]["numero"]+=$ot[$idTipo]["numero"];
			$total[$idTipo]["valor"]+=$ot[$idTipo]["valor"];
			$dxGraf[$nombre] ["data"][] = $ot[$idTipo]["valor"];
		}
		else
		{
			$linea = array_merge($linea, array("",""));
			$dxGraf[$nombre] ["data"][] = "";
		}
		$dxGraf[$nombre] ["labels"][] = trim(str_replace("MANTENIMIENTO","",$titulos[$idTipo]));
	}

	if($html)
		imprimirLinea($linea,"",array(1=>"align='left'"));
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
}


$linea = array("TOTAL");
foreach($tpmttos as $idTipo => $tipo)
	$linea = array_merge($linea, array($total[$idTipo]["numero"],number_format($total[$idTipo]["valor"], 2, ",", ".")));
if($html)
	imprimirLinea($linea,"#b2d2e1",array(1=>"align='left' strong"));
else
	imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"azul_izq"));


if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro;
	echo "</table><br /><br /><table  width='98%'>	";
	$i=1;
	foreach($dxGraf as $key => $data)
	{
		if($i==1) echo "<tr>";
		echo "<td width='50%'>";
		graficaMultiLine($data, $key,  "Valor","","","",0);
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
