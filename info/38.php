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


$first = $inicio;
$meses = array();
while($first <= $final)
{
	list($anio,$mes,$dia)=split("-",$first);	
	$numMes = strftime("%Y-%m",strtotime($first));
	$in = $anio."-".$mes."-01";
	$fi = $anio."-".$mes."-".ultimoDia($mes,$anio);
	if($in < $inicio)
	  $in = $inicio;
	if($fi > $final)
	  $fi = $final;
	$meses[$numMes] = array("ini"=>$in, "fin"=>$fi);

	//siguiente
	$first = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + 1 * 24 * 60 * 60);
}

$titulos = array("COSTOS VARIABLES");
$datos = $centros = $tipos = array();
foreach($meses as $keyMes => $dxFecha)
{
  $qidEleExis = $db->sql_query("
			SELECT x.cantidad, ex.precio, t.id as id_tipo, t.nombre as tipo, ec.id_centro, centro, o.fecha_ejecucion_inicio, o.fecha_ejecucion_fin
			FROM mtto.ordenes_trabajo_elementos x
			LEFT JOIN mtto.ordenes_trabajo o ON o.id=x.id_orden_trabajo
			LEFT JOIN mtto.elementos e ON e.id=x.id_elemento 
			LEFT JOIN mtto.elementos_existencias ex ON ex.id_elemento=e.id
			LEFT JOIN mtto.ele_tipos t ON t.id=e.tipoe
			LEFT JOIN mtto.elementos_centros ec ON ec.id_elemento=e.id
			LEFT JOIN centros c ON c.id=ec.id_centro
			WHERE o.fecha_ejecucion_inicio::date>='".$dxFecha["ini"]."' AND o.fecha_ejecucion_inicio::date<='".$dxFecha["fin"]."' AND c.id = '".$centro."'");
  while($ele =  $db->sql_fetchrow($qidEleExis))
  {
	$total = $ele["precio"]*$ele["cantidad"];
	$tipos[$ele["id_tipo"]] = $ele["tipo"];
	$centros[$ele["id_centro"]] = $ele["centro"];

	if(!isset($datos[$keyMes][$ele["id_tipo"]][$ele["id_centro"]])) $datos[$keyMes][$ele["id_tipo"]][$ele["id_centro"]]=0;
	$datos[$keyMes][$ele["id_tipo"]][$ele["id_centro"]]+=$total;
  } 
}

foreach($meses as $keyMes => $dxFecha)
{
  foreach($centros as $nombre)
  {
	$titulos[] = $keyMes."/".$nombre;
  }
}
$titulos = array_merge($titulos, array("PROMEDIO", "TOTAL"));
if($html)
{
	echo '
		<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
		<tr>';
	foreach($titulos as $tt)
		echo '<th height="40">'.$tt.'</th>';
	echo "</tr>";
}else
{
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, $titulos);
	$fila++;$columna=0;
}

$dxGraf = array();
foreach($tipos as $idTipo => $nombre)
{
  $sum = $num = 0;
  $linea = array($nombre);
	$dxGraf[$nombre] = array("data"=>array(),  "labels"=>array());
  foreach($meses as $keyMes => $dxFecha)
  {
	foreach($centros as $idCentro => $nombreCentro)
	{
	  if(isset($datos[$keyMes][$idTipo][$idCentro]))
	  {
		$linea[] = number_format($datos[$keyMes][$idTipo][$idCentro], 2, ",", ".");
		$sum+=$datos[$keyMes][$idTipo][$idCentro];
		$num++;
		$dxGraf[$nombre] ["data"][] = $datos[$keyMes][$idTipo][$idCentro];
	  }
	  else
		{
			$linea[] = "";
			$dxGraf[$nombre] ["data"][] = "";
		}
	}
	$dxGraf[$nombre] ["labels"][] = $keyMes;
  }

  $linea[] = number_format($sum/$num, 2, ",", ".");
  $linea[] = number_format($sum, 2, ",", ".");
  
  if($html)
	imprimirLinea($linea);
  else
	imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea);
}

if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro;
	echo "</table><br /><br /><table  width='98%'>	";
	$i=1;
	foreach($dxGraf as $key => $data)
	{
		if($i==1) echo "<tr>";
		echo "<td width='50%'>";
		graficaBarras($data, $key, "Valor", "Mes", "Valor");
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
