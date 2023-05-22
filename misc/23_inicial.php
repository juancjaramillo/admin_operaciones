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

$vista = "global";
if(isset($_POST["vista"]))
	$vista = $_POST["vista"];
elseif(isset($_GET["vista"]))
	$vista = $_GET["vista"];

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

$titulos = array("TIPO VEHÍCULO", "TONELADAS", "VIAJES", "PROMEDIO TONELADAS POR VIAJE", "CAPACIDAD", "PROMEDIO/CAPACIDAD");
if($html)
{
	echo '
		<table width="100%">
			<tr>
				<td valign="top" align="center" width="50%">
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
if($id_turno != "")
	$cond = " AND m.id_turno=".$id_turno;

if($id_ase != "") 
	$cond.= " AND a.id='".$id_ase."'";
else
	$cond.=" AND a.id_centro = '".$centro."'";

if($dia != "")
	$cond.=" AND to_char(m.inicio, 'D') = '".$dia."'";

$tipos = array();
$qidVe = $db->sql_query("SELECT distinct(id_tipo_vehiculo), tv.tipo, tv.capacidad 
		FROM vehiculos v
	 	LEFT JOIN tipos_vehiculos tv ON tv.id=v.id_tipo_vehiculo	
		LEFT JOIN rec.movimientos m ON m.id_vehiculo=v.id
		LEFT JOIN micros i ON i.id = m.id_micro
		LEFT JOIN ases a ON a.id=i.id_ase
		WHERE m.inicio::date >='".$inicio."' AND m.inicio::date<='".$final."' $cond
		ORDER BY tipo");
while($veh = $db->sql_fetchrow($qidVe))
{
	$tipos[$veh["id_tipo_vehiculo"]] = $veh;
}

$dxGraf = array("data"=>array(),  "labels"=>array());
foreach($tipos as $idTipo => $dx)
{
	$tons = $viajes = 0;
	$vehiculos = array();
	$qidMov = $db->sql_query("SELECT m.id, m.inicio, v.codigo||'/'||v.placa as codigo
			FROM rec.movimientos m 
			LEFT JOIN micros i ON i.id=m.id_micro 
			LEFT JOIN vehiculos v ON v.id=m.id_vehiculo
			LEFT JOIN ases a ON a.id=i.id_ase
			WHERE v.id_tipo_vehiculo='".$idTipo."' AND inicio::date >= '".$inicio."' AND inicio::date<='".$final."' ".$cond."
			ORDER BY v.codigo, v.placa");
	while($mov = $db->sql_fetchrow($qidMov))
	{
		$tonsXMov = averiguarPesoXMov($mov["id"], "", true, $id_turno);
		$viajesxmov=averiguarViajeXMov($mov["id"], $id_turno);

		$tons+=$tonsXMov;
		$viajes+=$viajesxmov;
			
		if(!isset($vehiculos[$mov["codigo"]])) $vehiculos[$mov["codigo"]] = array("tons"=>0, "viajes"=>0);
		$vehiculos[$mov["codigo"]]["tons"] += $tonsXMov;
		$vehiculos[$mov["codigo"]]["viajes"] += $viajesxmov;
	}
//	error_log(print_r($vehiculos,true));

	@$tonxViaje = $tons/$viajes;
	@$ind = ($tonxViaje/$dx["capacidad"]) * 100;
		
	$linea = array($dx["tipo"], number_format($tons, 2, ",", "."), $viajes, number_format($tonxViaje, 2, ",", "."), $dx["capacidad"], number_format($ind, 2, ",", ".")."%");

	if($vista == "global")
	{
		if($html)
			imprimirLinea($linea,"",array(1=>"align='left'"));
		else
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq"));
	}else
	{
			foreach($vehiculos as $key => $dxVehi)
			{
				@$tonxViajeVehiculo = $dxVehi["tons"]/$dxVehi["viajes"];
				@$indVehiculo = ($tonxViajeVehiculo/$dx["capacidad"])*100;

				$lineaVehiculo = array($key, number_format($dxVehi["tons"], 2, ",", "."), $dxVehi["viajes"], number_format($tonxViajeVehiculo, 2, ",", "."), $dx["capacidad"], number_format($indVehiculo, 2, ",", ".")."%");
				if($html)
					imprimirLinea($lineaVehiculo, "", array(1=>"align='left'"));
				else
					imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $lineaVehiculo, array(1=>"txt_izq"));
			}
			if($html)
				imprimirLinea($linea,"#b2d2e1", array(1=>"align='left'"));
			else
			{
				imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $linea,  array(1=>"azul_izq"));
				$fila++;$columna=0;
			}
	}

	$dxGraf["data"][] = $ind;
	$dxGraf["labels"][] = $dx["tipo"];
}

if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro."&id_turno=".$id_turno."&id_ase=".$id_ase."&dia=".$dia."&vista=".$vista;
	echo "</table></td>
			<td valign='top'>";
	graficaGradientBar($dxGraf, "FACTOR DE CARGA: ".$inicio."/".$final, "PROM TONELADAS/CAPACIDAD");
	echo "
			</td>
		</tr>
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
