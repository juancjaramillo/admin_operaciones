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

$lineaPrimera = array("FECHA MOVIMIENTO","TURNO", "VEHICULO", "PLACA", 'ZONA', "FECHA ENTRADA", "TIQUETE ENTRADA", "LLEGADA DESCARGUE", "HORA TICKET ENTRADA", "HORA TICKET SALIDA", "SALIDA DESCARGUE", "TIEMPO RELLENO","LUGAR");
if($html)
{
	echo '
		<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
			<tr>';
		foreach($lineaPrimera as $dx)
			echo '<th>'.$dx.'</th>';
	echo "</tr>";
}else
{
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, $lineaPrimera);
	$fila++;$columna=0;
}

$cond = "true";
if($id_turno != "") $cond .= " AND m.id_turno='".$id_turno."'";
$cond .=" AND p.fecha_entrada::date >= '".$inicio."' AND p.fecha_entrada <= '".$final."'";

if($inicio == $final)
	$cond .=" AND p.fecha_entrada::date = '".$inicio."'";

if($id_ase != "") 
	$cond.= " AND a.id='".$id_ase."'";
else
	$cond.=" AND a.id_centro = '".$centro."'";

$consulta = "SELECT p.*, m.id as id_movimiento, turno,v.codigo, v.placa, mp.viaje, 
	to_char(p.fecha_entrada, 'HH24:MI')  as fecha_entrada1,to_char(p.fecha_salida, 'HH24:MI')  as fecha_salida1,
	p.fecha_entrada::date as fecmov, m.inicio::date as inicio, l.nombre,l.id as ldes
	FROM rec.pesos p
	LEFT JOIN vehiculos v ON v.id = p.id_vehiculo
	LEFT JOIN rec.movimientos_pesos mp ON mp.id_peso = p.id
	LEFT JOIN rec.movimientos m ON m.id = mp.id_movimiento AND m.id_vehiculo = p.id_vehiculo
	LEFT JOIN micros i ON i.id = m.id_micro
	LEFT JOIN ases a ON a.id = i.id_ase
	LEFT JOIN turnos t ON t.id=m.id_turno
	LEFT JOIN lugares_descargue l on p.id_lugar_descargue=l.id
	WHERE ".$cond."
	ORDER BY id,tiquete_salida,mp.id";

$qid = $db->sql_query($consulta);
$tiquete_salida='0';
$entro = true;

while($query = $db->sql_fetchrow($qid))
{
	$dif = $nv = $llegada = $salida = $tentrada= "ND";
	if($tiquete_salida !=  $query["tiquete_salida"])
	{
		if($query["id_movimiento"] != "")
		{		
			$des = $db->sql_row("SELECT hora_inicio,to_char(hora_inicio, 'HH24:MI')  as hora_inicio1, numero_viaje, 
				to_char(hora_fin, 'HH24:MI')  as hora_fin1,hora_fin  FROM rec.desplazamientos WHERE id_movimiento='".$query["id_movimiento"]."' 
				AND id_tipo_desplazamiento=6 AND hora_inicio<='".$query["fecha_salida"]."' order by hora_inicio desc limit 1");
			
			//AND numero_viaje='".$query["viaje"]."' Se quit porque algunos movimientos quedan con el mismo viaje.

				$dif = conversor_segundos(restarFechasConHHmmss($des["hora_inicio"], $des["hora_fin"], true));
				$nv = $des["numero_viaje"];
				$llegada = $des["hora_inicio1"];
				$salida = $des["hora_fin1"];
				$turno = $query["turno"];
				$tentrada = $query["tiquete_salida"];
		}
	//$nv,
		if ($centro==1) $zona=1;
		if ($centro==2) $zona=3;
		$linea = array( $query["inicio"],$turno, $query["codigo"],$query["placa"],$zona,$query["fecmov"],$tentrada,$llegada, $query["fecha_entrada1"],$query["fecha_salida1"], $salida, $dif,$query["nombre"]);
		if($html)
		{
			if($dif > '00:30:00')
				imprimirLinea($linea, "", array(1=>"align='center'", 2=>"align='center'", 3=>"align='center'", 4=>"align='center'", 5=>"align='center'", 6=>"align='center' bgcolor='red'", 7=>"align='center'", 8=>"align='center'", 9=>"align='center'", 10=>"align='center'", 11=>"align='center'",13=>"align='center'"));
			else
				imprimirLinea($linea, "", array(1=>"align='center'", 2=>"align='center'", 3=>"align='center'", 4=>"align='center'", 5=>"align='center'", 6=>"align='center'", 7=>"align='center'", 8=>"align='center'", 9=>"align='center'", 10=>"align='center'", 11=>"align='center'",13=>"align='center'"));
		}
		else
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, $estilosText);
	}
	if ($query["ldes"]==12) {$tiquete_salida='99';}
	else {$tiquete_salida = $query["tiquete_salida"];}
}






//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro."&id_turno=".$id_turno."&id_ase=".$id_ase;
	echo "</table><br /><br />";
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