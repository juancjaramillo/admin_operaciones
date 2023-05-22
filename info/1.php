<?
// operaciones : Programación Diaria
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

$dia = "";
if(isset($_POST["dia"]))
	$dia = $_POST["dia"];
elseif(isset($_GET["dia"]))
	$dia = $_GET["dia"];

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
	$estilosTit = array(1=>"azul_izq");
	$estilosText = array(1=>"txt_izq");
}

$lineaPrimera = array("FECHA");
$servicios = array();
$qid = $db->sql_query("SELECT * FROM servicios WHERE esquema != 'bar' ORDER BY servicio");
while($ser = $db->sql_fetchrow($qid))
{
	$lineaPrimera[] = strtoupper($ser["servicio"]);
	$servicios[$ser["id"]] = $ser;
}
$lineaPrimera[] = "TOTAL";

if($html)
{
	echo '
		<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
			<tr>';
		foreach($lineaPrimera as $dx)
			echo '<th height="40">'.$dx.'</th>';
	echo "</tr>";
}else
{
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, $lineaPrimera);
	$fila++;$columna=0;
}

$diasBTW = restarFechas($final,$inicio);
$dxGraf = array("data"=>array(),  "labels"=>array());
for($i=0 ; $i<=$diasBTW; $i++)
{
	list($anio,$mes,$diaAc)=split("-",$inicio);
	$diaAc = date("Y-m-d",mktime(0,0,0, $mes,$diaAc,$anio) + $i * 24 * 60 * 60);
	
	$entra = false;
	if($dia != "")
	{
		if(strftime("%w",strtotime($diaAc))+1 == $dia)
			$entra = true;
	}
	else
		$entra = true;

	if($entra)
	{
		$datos = array("dia"=>array($diaAc),"viajes"=>array("No. Viajes"),"tonv"=>array("Toneladas/Viaje"),"kms"=>array("Kms. Recorridos"),"comb"=>array("Combustible"),"kmsgal"=>array("kms/galón"));
		$totalProd = $totalViajes = $totalTonViaj = $totalKms = $totalComb = $totalKmXgal = 0;
		foreach($servicios as $idSer => $dxServicio)
		{
			//producción
			if($dxServicio["esquema"] == "rec")
			{
				$peso=averiguarPeso($diaAc, $idSer, $centro,$id_turno, $id_ase);
				$totalProd+=$peso;
				if($peso != "")
					$datos["dia"][] = number_format($peso, 2, ",", ".");
				else
					$datos["dia"][] = "";
				
				//numero viajes
				$viajes=averiguarNumeroViajes($diaAc, $idSer, $centro,$id_turno, $id_ase);
				$datos["viajes"][] = $viajes;
				$totalViajes+=$viajes;

				//toneladas por viaje
				if($viajes=="")
					$datos["tonv"][] = "";
				else
				{
					$datos["tonv"][] = number_format($peso/$viajes, 2, ",", ".");
					$totalTonViaj+=$peso/$viajes;
				}

				//desplazamientos
				$kms = averiguarDesplazamientos($diaAc, $idSer, $centro,$id_turno, $id_ase);
				if($kms == "")
					$datos["kms"][] = "";
				else
					$datos["kms"][] = number_format($kms, 2, ",", ".");
				$totalKms+=$kms;
			
				//combustible
				$comb = averiguarCombustible($diaAc, $idSer, $centro,$id_turno, $id_ase);
				if($comb == "")
					$datos["comb"][] = "";
				else
					$datos["comb"][] = number_format($comb, 3, ",", ".");	
				$totalComb+=$comb;

				//km x galón
				if($comb == "")
					$datos["kmsgal"][] = "";
				else
				{
					@$kmxgal = $kms/$comb;
					$datos["kmsgal"][] = number_format($kmxgal, 2, ",", ".");
					$totalKmXgal+=$kmxgal;
				}
			}
			else
			{
				$datos["dia"][] = "";
				$datos["viajes"][] = "";
				$datos["tonv"][] = "";
				$datos["kms"][] = "";
				$datos["comb"][] = "";
				$datos["kmsgal"][] = "";
			}
		}

		$dxGraf["data"][] = $totalProd;
		$dxGraf["labels"][] = ucfirst(strftime("%b.%d.%Y",strtotime($diaAc)));

		$datos["dia"][] = number_format($totalProd, 2, ",", ".");
		$datos["viajes"][] = $totalViajes;
		$datos["tonv"][] = number_format($totalTonViaj, 2, ",", ".");
		$datos["kms"][] = number_format($totalKms, 2, ",", ".");
		$datos["comb"][] = number_format($totalComb, 3, ",", ".");
		$datos["kmsgal"][] = number_format($totalKmXgal, 2, ",", ".");

		if($html)
		{
			imprimirLinea($datos["dia"],"#b2d2e1");
			imprimirLinea($datos["viajes"]);
			imprimirLinea($datos["tonv"]);
			imprimirLinea($datos["kms"]);
			imprimirLinea($datos["comb"]);
			imprimirLinea($datos["kmsgal"]);
		}else
		{
			imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $datos["dia"], $estilosTit);
			$fila++;$columna=0;
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $datos["viajes"], $estilosText);
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $datos["tonv"], $estilosText);
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $datos["kms"], $estilosText);
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $datos["comb"], $estilosText);
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $datos["kmsgal"], $estilosText);
		}
	}
}

//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro."&id_turno=".$id_turno."&id_ase=".$id_ase."&dia=".$dia;
	echo "</table><br /><br />";
	graficaBarras($dxGraf, "PRODUCCIÓN DIARIA : ".$inicio."/".$final, "TOTAL (tons)", "Día", "Toneladas");
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