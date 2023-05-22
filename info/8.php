<?
// operación : combustibles
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

$vista = nvl($_POST["vista"], nvl($_GET["vista"], "detalle"));

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

if($vista == "detalle")
{
	$titulos = array("VEHÍCULO", "FECHA MOVIMIENTO", "DÍA", "RUTA", "VIAJES", "KM SALIDA", "KM REGRESO", "KM RECORRIDO", "PESO NETO", "#TIQUETE RELLENO", "COMBUSTIBLE", "HOROMETRO FINAL");
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
}

$cond = "";
if($id_turno != "")
	$cond .= " AND m.id_turno =".$id_turno;
if($id_ase != "")
	$cond .= " AND a.id = '".$id_ase."'";
else
	$cond.= " AND a.id_centro = '".$centro."'";

$dxGraf = array();
$dxMensual = $meses= array();
$qidMov = $db->sql_query("SELECT m.*, i.codigo, v.codigo||' / '||v.placa as vehi, v.id as id_vehiculo
		FROM rec.movimientos m 
		LEFT JOIN micros i ON i.id=m.id_micro 
		LEFT JOIN vehiculos v ON v.id=m.id_vehiculo
		LEFT JOIN ases a ON a.id = i.id_ase
		WHERE inicio::date >= '".$inicio."' AND inicio::date<='".$final."' $cond
		ORDER BY v.codigo, v.placa, m.inicio");
while($mov = $db->sql_fetchrow($qidMov))
{
	$viajes = averiguarViajeXMov($mov["id"]);
	$recorrido = kmsRecorridoPorMov($mov["id"]);
	$kmini = $db->sql_row("SELECT km FROM rec.desplazamientos WHERE id_movimiento=".$mov["id"]." ORDER BY hora_inicio LIMIT 1");
	if(nvl($kmini["km"],0)==0){
		$kmfin="0";
		$recorrido="0";
	}
	else $kmfin=$mov["km_final"];
	$peso = averiguarPesoXMov($mov["id"]);
	if(nvl($peso,0)==0){
			$peso="0";
	}
	$tiquetes = array();
	$qidPT = $db->sql_query("SELECT tiquete_entrada FROM rec.movimientos_pesos mp LEFT JOIN rec.pesos p ON p.id=mp.id_peso WHERE id_movimiento=".$mov["id"]);
	while($tq = $db->sql_fetchrow($qidPT))
	{
		if($tq["tiquete_entrada"] != "")
			$tiquetes[] = $tq["tiquete_entrada"];
	}

	$meses[strftime("%Y-%m",strtotime($mov["inicio"]))] = strftime("%Y-%m",strtotime($mov["inicio"]));
	$dxMensual[$mov["id_vehiculo"]]["codigo"]  = $mov["vehi"];
	if(!isset($dxMensual[$mov["id_vehiculo"]]["dates"][strftime("%Y-%m",strtotime($mov["inicio"]))])) $dxMensual[$mov["id_vehiculo"]]["dates"][strftime("%Y-%m",strtotime($mov["inicio"]))] = array("kms"=>0, "combustible"=>0);
	$dxMensual[$mov["id_vehiculo"]]["dates"][strftime("%Y-%m",strtotime($mov["inicio"]))]["kms"]+= $recorrido;
	$dxMensual[$mov["id_vehiculo"]]["dates"][strftime("%Y-%m",strtotime($mov["inicio"]))]["combustible"]+= $mov["combustible"];
	$dxMensual[$mov["id_vehiculo"]]["dates"][strftime("%Y-%m",strtotime($mov["inicio"]))]["peso"]+= $peso;

	if($vista == "detalle")
	{
		$linea = array($mov["vehi"], $mov["inicio"], ucfirst(strftime("%a",strtotime($mov["inicio"]))),$mov["codigo"], $viajes, nvl($kmini["km"],0), $kmfin, $recorrido, number_format($peso,3), implode(", ",$tiquetes), $mov["combustible"], $mov["horometro_final"]);
			
		if($html)
			imprimirLinea($linea,"",array(1=>"align='left'", 2=>"align='left'", 3=>"align='left'", 4=>"align='left'"));
		else
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq", 2=>"txt_izq", 3=>"txt_izq", 4=>"txt_izq"));

		$fecha = ucfirst(strftime("%b.%d.%Y",strtotime($mov["inicio"])));
		if(!isset($dxGraf[$mov["vehi"]]["data"][$fecha])) $dxGraf[$mov["vehi"]]["data"][$fecha]=0;
		$dxGraf[$mov["vehi"]]["data"][$fecha] +=  $mov["combustible"];
		$dxGraf[$mov["vehi"]]["labels"][$fecha] =  $fecha;
	}
}

if($vista == "mensual")
{
	$titulos = $titulosDos = array();
	foreach($meses as $mx)
	{
		$titulos[] = strtoupper(strftime("%B",strtotime($mx."-01")));
		$titulosDos = array_merge($titulosDos, array("TONELADAS","KM RECORRIDO",  "COMBUSTIBLE", "KM/COMBUSTIBLE"));
	}
	
	if($html)
	{
		echo '
			<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
			<tr><th rowspan=2>VEHÍCULO</th>';
		foreach($titulos as $tt)
			echo '<th height="40" colspan=4>'.$tt.'</th>';
		echo "</tr></tr>";
		foreach($titulosDos as $tt)
			echo '<th height="40">'.$tt.'</th>';
		echo "</tr>";
	}else
	{
		titulos_uno_xls($workbook, $worksheet, $fila, $columna, array("VEHÍCULO"), 0,1);
		foreach($titulos as $tt)
		{
			titulos_uno_xls($workbook, $worksheet, $fila, $columna, array($tt),2);
			$columna+=2;
		}
		$fila++;$columna=1;
		titulos_uno_xls($workbook, $worksheet, $fila, $columna, $titulosDos);
		$fila++;$columna=0;
	}

	
	foreach($dxMensual as $dx)
	{
		$linea = array($dx["codigo"]);
		foreach($meses as $mx)
		{
			//if(!isset($dxGraf[$dx["codigo"]]["data"][$mx])) $dxGraf[$dx["codigo"]]["data"][$mx]=0;
			if(isset($dx["dates"][$mx]))
			{
				$linea[] = number_format($dx["dates"][$mx]["kms"],1);
  			$linea[] = $dx["dates"][$mx]["peso"];
				$linea[] = $dx["dates"][$mx]["combustible"];
				@$linea[] = number_format($dx["dates"][$mx]["kms"] / $dx["dates"][$mx]["combustible"],1);
				
				$dxGraf[$dx["codigo"]]["comb"]["data"][$mx] =  $dx["dates"][$mx]["combustible"];
				$dxGraf[$dx["codigo"]]["comb"]["labels"][$mx] =  $mx;
				$dxGraf[$dx["codigo"]]["kmxcomb"]["data"][$mx] =  $dx["dates"][$mx]["kms"] / $dx["dates"][$mx]["combustible"];
				$dxGraf[$dx["codigo"]]["kmxcomb"]["labels"][$mx] =  $mx;

			}
			else
			{
				if($html)
					$linea = array_merge($linea, array("&nbsp;","&nbsp;","&nbsp;","&nbsp;"));
				else
					$linea = array_merge($linea, array("","","",""));

				$dxGraf[$dx["codigo"]]["comb"]["data"][$mx] =  0;
				$dxGraf[$dx["codigo"]]["comb"]["labels"][$mx] =  $mx;
				$dxGraf[$dx["codigo"]]["kmxcomb"]["data"][$mx] =  0;
				$dxGraf[$dx["codigo"]]["kmxcomb"]["labels"][$mx] =  $mx;
			}
		}

		if($html)
			imprimirLinea($linea);
		else
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
	}
}




//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro."&id_turno=".$id_turno."&id_ase=".$id_ase."&vista=".$vista;
	echo "</table><br /><br /><table  width='98%'>";
	
	$i=1;
	if($vista == "detalle")
	{
		foreach($dxGraf as $key => $data)
		{
			if($i==1) echo "<tr>";
			echo "<td width='50%'>";
			$temp = array("data"=>array_values($data["data"]), "labels"=>array_values($data["labels"]));
			graficaMultiLine($temp, "CONSUMOS COMBUSTIBLES ".$key,  "Combustible");
			echo "</td>";
			if($i==2)
			{
				echo "</tr>";
				$i=0;
			}
			$i++;
		}
	}else{
		foreach($dxGraf as $key => $data)
		{
			echo "<tr><td width='50%'>";
			$temp = array("data"=>array_values($data["comb"]["data"]), "labels"=>array_values($data["comb"]["labels"]));
			graficaMultiLine($temp, "CONSUMOS COMBUSTIBLES ".$key,  "Combustible");
			echo "</td><td width='50%'>";
			$temp = array("data"=>array_values($data["kmxcomb"]["data"]), "labels"=>array_values($data["kmxcomb"]["labels"]));
			graficaMultiLine($temp, "KM/COMBUSTIBLE ".$key,  "km/Combustible");
			echo "</td></tr>";
		}
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
