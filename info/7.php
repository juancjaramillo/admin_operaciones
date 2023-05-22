<?
// operacion : sobrepesos
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

$id_vehiculo = "";
if(isset($_POST["id_vehiculo"]))
	$id_vehiculo = $_POST["id_vehiculo"];
elseif(isset($_GET["id_vehiculo"]))
	$id_vehiculo = $_GET["id_vehiculo"];

$id_micro = "";
if(isset($_POST["id_micro"]))
	$id_micro = $_POST["id_micro"];
elseif(isset($_GET["id_micro"]))
	$id_micro = $_GET["id_micro"];



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

$titulos = array("VEHÍCULO", "TIPO", "FECHA", "RUTA", "TURNO", "PESO ENTRADA", "PESO SALIDA", "PESO NETO", "CAPACIDAD", "SOBREPESO");
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

$cond = "";
if($id_vehiculo != "")
	$cond.= " AND v.id='".$id_vehiculo."'";
if($id_micro != "")
	$cond.= " AND p.id IN (SELECT id_peso FROM rec.movimientos_pesos mp LEFT JOIN rec.movimientos m ON m.id=mp.id_movimiento WHERE m.id_micro='".$id_micro."' )";

$dxGraf = array();
$consulta = "
		SELECT p.*, t.tipo, t.capacidad, v.codigo||' / '||v.placa as vehi, 
			array_to_string(array(
				SELECT i.codigo
				FROM rec.movimientos_pesos mp
				LEFT JOIN rec.movimientos mov ON mov.id=mp.id_movimiento
				LEFT JOIN micros i ON i.id=mov.id_micro
				WHERE mp.id_peso = p.id
			),', ') as ruta,
			array_to_string(array(
				SELECT i.turno
				FROM rec.movimientos_pesos mp
				LEFT JOIN rec.movimientos mov ON mov.id=mp.id_movimiento
				LEFT JOIN turnos i ON i.id=mov.id_turno
				WHERE mp.id_peso = p.id
			),', ') as turnos
		FROM rec.pesos p
		LEFT JOIN vehiculos v ON v.id = p.id_vehiculo
		LEFT JOIN tipos_vehiculos t ON t.id=v.id_tipo_vehiculo
		WHERE fecha_entrada::date>='".$inicio."' AND fecha_entrada::date<= '".$final."' AND v.id_centro = '".$centro."' ".$cond."
		ORDER BY v.codigo, v.placa, fecha_entrada";
#echo $consulta;
$qid = $db->sql_query($consulta);
while($p = $db->sql_fetchrow($qid))
{
	if($p["peso_inicial"] != "" && $p["peso_final"]) $neto = abs($p["peso_final"]-$p["peso_inicial"]);
	else $neto = $p["peso_total"];

	$sobre = 0;
	if($neto > $p["capacidad"]) $sobre = $neto - $p["capacidad"];
	else $falta = ($p["capacidad"] - $neto)*-1;

	if($sobre>0||$falta<0)
	{
		if ($sobre==0) $sobre=$falta;
		$linea = array($p["vehi"], $p["tipo"], $p["fecha_entrada"], $p["ruta"], $p["turnos"],number_format($p["peso_inicial"], 2, ",", "."),number_format($p["peso_final"], 2, ",", "."), number_format($neto, 2, ",", "."), number_format($p["capacidad"], 2, ",", "."),number_format($sobre, 2, ",", "."));
		if($html)
		{
			if($sobre<-1) $styles = array(1=>"align='left'", 2=>"align='left'", 3=>"align='left'", 4=>"align='left'",5=>"align='left'", 10=>"align='right' bgcolor='#F781F3'");
			if($sobre>-1 && $sobre<0) $styles = array(1=>"align='left'", 2=>"align='left'", 3=>"align='left'", 4=>"align='left'", 5=>"align='left'", 10=>"align='right' bgcolor='#81F7F3'");
			if($sobre>0 && $sobre < 0.5) $styles = array(1=>"align='left'", 2=>"align='left'", 3=>"align='left'", 4=>"align='left'", 5=>"align='left'", 10=>"align='right' bgcolor='#26d026'");
			if($sobre >= 0.5 && $sobre <=1.5) $styles = array(1=>"align='left'", 2=>"align='left'", 3=>"align='left'", 4=>"align='left'", 5=>"align='left'", 10=>"align='right' bgcolor='#f3d633'");
			if($sobre > 1.5) $styles = array(1=>"align='left'", 2=>"align='left'", 3=>"align='left'", 4=>"align='left'", 5=>"align='left'", 10=>"align='right' bgcolor='#c13644'");
			imprimirLinea($linea,"",$styles);
		}
		else
		{
			if($sobre<-1) $styles = array(1=>"txt_izq", 2=>"txt_izq", 3=>"txt_izq", 4=>"txt_izq",5=>"align='left'", 10=>"txt_norm_rojo");
			if($sobre>-1 && $sobre<0) $styles = array(1=>"txt_izq", 2=>"txt_izq", 3=>"txt_izq", 4=>"txt_izq",5=>"align='left'", 10=>"txt_norm_amarillo");
			if($sobre < 0.5) $styles = array(1=>"txt_izq", 2=>"txt_izq", 3=>"txt_izq", 4=>"txt_izq", 5=>"align='left'",10=>"txt_norm_verde");
			if($sobre >= 0.5 && $sobre <=1.5) $styles = array(1=>"txt_izq", 2=>"txt_izq", 3=>"txt_izq", 4=>"txt_izq",5=>"align='left'", 10=>"txt_norm_amarillo");
			if($sobre > 1.5) $styles = array(1=>"txt_izq", 2=>"txt_izq", 3=>"txt_izq", 4=>"txt_izq",5=>"align='left'", 10=>"txt_norm_rojo");
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, $styles);
		}

		$dxGraf[$p["vehi"]]["data"][] =  $sobre;
		$dxGraf[$p["vehi"]]["dataDash"][] =  $p["capacidad"];
		$dxGraf[$p["vehi"]]["labels"][] =  str_replace(" ","\n",ucfirst(strftime("%b.%d.%Y %H:%M",strtotime($p["fecha_entrada"]))));
	}
}


//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro;
	echo "</table><br /><br /><table  width='98%'>";
	
	$i=1;
	foreach($dxGraf as $key => $data)
	{
		if($i==1) echo "<tr>";
		echo "<td width='50%'>";
		graficaMultiLine($data, "SOBREPESOS ".$key,  "Toneladas", "Sobrepeso", "Capacidad");
		echo "</td>";
		if($i==2)
		{
			echo "</tr>";
			$i=0;
		}
		$i++;
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
