<?
// opera :  Descargue Diario 
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

$vista = nvl($_POST["vista"], nvl($_GET["vista"], "fecha"));

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

if($vista=="fecha")
  $titulos = array("FECHA DESCARGUE", "SITIO DISPOSICIÓN", "VIAJES", "TONELADAS DISPUESTAS");
else
  $titulos = array("SITIO DISPOSICIÓN", "VIAJES", "TONELADAS DISPUESTAS");

if($html)
{
	echo '
		<table width="98%" >
			<tr>
				<td valign="top" width="50%">
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

$cond = "";
if($id_ase != "")
	$cond = " AND a.id='".$id_ase."'";

$disp = $lugares = array();
$qidMov = $db->sql_query("SELECT p.peso_inicial, p.peso_final, p.peso_total, l.nombre as nombre, to_char(p.fecha_entrada,'YYYY-MM-DD') as fecha, p.id_lugar_descargue
		FROM rec.pesos p 
		LEFT JOIN lugares_descargue l ON l.id=p.id_lugar_descargue
		LEFT JOIN centros c ON c.id=l.id_centro
		LEFT JOIN ases a ON a.id_centro = c.id 
		WHERE p.fecha_entrada::date >= '".$inicio."' AND p.fecha_entrada::date<='".$final."' AND l.id_centro = '".$centro."' $cond
		group by p.peso_inicial, p.peso_final, p.peso_total, l.nombre, p.fecha_entrada, p.id_lugar_descargue
		ORDER BY p.fecha_entrada, l.nombre");
while($mov = $db->sql_fetchrow($qidMov))
{
	if($mov["peso_inicial"] != "" && $mov["peso_final"] != "") $ton=abs($mov["peso_inicial"]-$mov["peso_final"]);
	elseif($mov["peso_total"] != "") $ton=$mov["peso_total"];
	
	$lugares[$mov["id_lugar_descargue"]] = $mov["nombre"];

	if($vista=="fecha")
	{
	  if(!isset($disp[$mov["fecha"]][$mov["id_lugar_descargue"]]))
		  $disp[$mov["fecha"]][$mov["id_lugar_descargue"]] = array("viaje"=>0, "ton"=>0);
  
	  if($mov["peso_inicial"] != "" && $mov["peso_final"] != "") $disp[$mov["fecha"]][$mov["id_lugar_descargue"]]["ton"]+=$ton;
	  elseif($mov["peso_total"] != "") $disp[$mov["fecha"]][$mov["id_lugar_descargue"]]["ton"]+=$ton;
  
	  $disp[$mov["fecha"]][$mov["id_lugar_descargue"]]["viaje"]++;
	}else{
	  if(!isset($disp[$mov["id_lugar_descargue"]])) $disp[$mov["id_lugar_descargue"]] = array("sitio"=>$mov["nombre"], "viaje"=>0, "ton"=>0);
	  
	  $disp[$mov["id_lugar_descargue"]]["ton"]+=$ton;
	  $disp[$mov["id_lugar_descargue"]]["viaje"]+=1;
	}
}

$dxGraf = array("data"=>array(), "labels"=>array());
$dxGrafBorrador = array();
if($vista=="fecha")
{
  foreach($disp as $fecha => $movs)
  {
	  foreach($movs as $idSitio => $dx)
	  {
		  $linea = array($fecha, $lugares[$idSitio], $dx["viaje"], number_format($dx["ton"], 2, ",", "."));
		  if($html)
			  imprimirLinea($linea,"",array(2=>"align='left'"));
		  else
			  imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq", 2=>"txt_izq"));
		
			$fechaGraf = ucfirst(strftime("%b.%d.%Y",strtotime($fecha)));
			$dxGrafBorrador[$lugares[$idSitio]][$fechaGraf] = $dx["ton"];
			$dxGraf["labels"][] = $fechaGraf;
	 } 
  }
}else
{
  foreach($disp as $dx)
  {
		$linea = array($dx["sitio"], $dx["viaje"], number_format($dx["ton"], 2, ",", "."));
		if($html)
		imprimirLinea($linea,"",array(1=>"align='left'"));
		else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
		$dxGraf["data"][] = $dx["ton"];
		$dxGraf["labels"][] = $dx["sitio"];
   }
} 


//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&vista=".$vista."&id_centro=".$centro."&id_ase=".$id_ase;
	echo "	</table></td>
				<td valign='top'>";

	if($vista=="fecha")
	{
		foreach($dxGrafBorrador as $key => $dx)
		{
			foreach($dxGraf["labels"] as $fecha)
			{
				if(isset($dx[$fecha]))
					$dxGraf["data"][$key][] = $dx[$fecha];
				else
					$dxGraf["data"][$key][] = "";
			}
		}
		graficaMultiBar($dxGraf, "DESCARGUE DIARIO: ".$inicio."/".$final, "Toneladas Dispuestas");
	}else
		graficaBarras($dxGraf, "DESCARGUE DIARIO: ".$inicio."/".$final, "Toneladas Dispuestas", "Tons", "Valor", 20);

	echo "</td></table>";
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
