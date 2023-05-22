<?
// operaciones : báscula
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
	$estilosText = array(1=>"txt_center");
}

if ($centro==1 ||$centro==2){
	$campo="Cliente";
}
else{
$campo="TICKET ENTRADA";
}

$lineaPrimera = array("FECHA OPERACION", "TURNO", "CÓDIGO/PLACA", "COMUNA", "RUTA", "PER.CERRO", "ORDEN DE SERVICIO", "LUGAR DESCARGUE", "FECHA DE ENTRADA", "HORA ENTRADA", "FECHA DE SALIDA", "HORA SALIDA", "TIPO DE RESIDUO",  "TIPO DE SERVICIO",  "PESO DE ENTRADA", "TARA", "PESO NETO", "TICKET SALIDA");
$lineaPrimera[]=$campo;

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

$cond =" p.fecha_entrada::date >= '".$inicio."' AND p.fecha_entrada <= '".$final."'";
if($inicio == $final)
	$cond =" p.fecha_entrada::date = '".$inicio."'";

if($id_vehiculo != "")
	$cond.= " AND v.id='".$id_vehiculo."'";
if($id_micro != "")
	$cond.= " AND p.id IN (SELECT id_peso FROM rec.movimientos_pesos mp LEFT JOIN rec.movimientos m ON m.id=mp.id_movimiento WHERE m.id_micro='".$id_micro."' )";

$consulta = " SELECT p.id, to_char(p.fecha_entrada,'YYYY-MM-DD') as feentrada,  to_char(p.fecha_entrada, 'HH24:MI')  as hoentrada, 
to_char(p.fecha_salida,'YYYY-MM-DD') as fesalida,  to_char(p.fecha_salida, 'HH24:MI')  as hosalida,  peso_inicial, peso_final, peso_total,tiquete_entrada, 
tiquete_salida, v.codigo||'/'||v.placa as placa, v.id as id_vehiculo, ld.nombre as lugar_descargue
	FROM rec.pesos p
	LEFT JOIN vehiculos v ON v.id = p.id_vehiculo
	LEFT JOIN lugares_descargue ld ON ld.id = p.id_lugar_descargue
	WHERE v.id_centro = ".$centro ." AND ".$cond."
	ORDER BY p.fecha_entrada";
// echo $consulta;
$qid = $db->sql_query($consulta);
while($query = $db->sql_fetchrow($qid))
{
	$fechas = $turno = $perCerro = $comuna = $codigo = $numero_orden = $tipor = $servicio = array();
	$consMov = "SELECT to_char(m.inicio,'YYYY-MM-DD') as inicio, t.turno, r.comuna,  r.codigo, m.numero_orden, tr.nombre as tipo, s.servicio, 
	p.nombre||' '||p.apellido as pcerro, m.inicio as orden, mp.porcentaje
		FROM rec.movimientos_pesos mp
		LEFT JOIN rec.movimientos m ON m.id = mp.id_movimiento
		LEFT JOIN turnos t ON t.id = m.id_turno
		LEFT JOIN micros r ON r.id = m.id_micro
		LEFT JOIN tipos_residuos tr ON tr.id = r.id_tipo_residuo
		LEFT JOIN servicios s ON s.id = r.id_servicio
		LEFT JOIN personas p ON p.id = m.id_persona_cerro
		WHERE (m.numero_orden is not null and m.numero_orden<>'' and m.numero_orden<>'0')
	 	and mp.id_peso=".$query["id"]." AND m.id_vehiculo='".$query["id_vehiculo"]."' 
		order by inicio,orden desc,porcentaje desc ";
	// echo $consMov;
	$qidMov = $db->sql_query($consMov);
	$orden = '2001-01-01-00:00:00';
	$porcentaje = 0 ;
	while($mov = $db->sql_fetchrow($qidMov))
	{
//		preguntar($mov);
		if ($orden <= $mov["orden"]){
			if ($porcentaje <= $mov["porcentaje"]){
#if (count($turno)<=1){
					unset($codigo);
					unset($numero_orden);
					unset($fechas);
					unset($comuna);
					unset($turno);
					$turno[] = $mov["turno"];
					$comuna[] = $mov["comuna"];
					if($mov["pcerro"] != "")
						$perCerro[] = $mov["pcerro"];
					$codigo[] = $mov["codigo"];
					$numero_orden[] = $mov["numero_orden"];
					$tipor[$mov["tipo"]] = $mov["tipo"];
					$servicio[$mov["servicio"]] = $mov["servicio"];
					$fechas[] = $mov["inicio"];
					$orden = $mov["orden"];
					$porcentaje = $mov["porcentaje"];
#				}
			}
		}
	}
	
	$neto = $query["peso_total"];
	if($query["peso_inicial"] != '' && $query["peso_final"] != '') $neto = $query["peso_inicial"] - $query["peso_final"];
//	$linea = array($query["feentrada"], implode(", ", $turno), $query["placa"], implode(", ", $comuna), implode(", ", $codigo), implode(", ", $numero_orden), $query["lugar_descargue"], $query["feentrada"], $query["hoentrada"], $query["fesalida"], $query["hosalida"], implode(", ", $tipor), implode(", ", $servicio), $query["peso_inicial"], $query["peso_final"], $neto, $query["tiquete_salida"]);
	$linea = array(implode(", ", $fechas), implode(", ", $turno), $query["placa"], implode(", ", $comuna), implode(", ", $codigo),  implode("; ", $perCerro) , implode(", ", $numero_orden), $query["lugar_descargue"], $query["feentrada"], $query["hoentrada"], $query["fesalida"], $query["hosalida"], implode(", ", $tipor), implode(", ", $servicio), $query["peso_inicial"], $query["peso_final"], $neto, $query["tiquete_salida"],$query["tiquete_entrada"]);
	if($html)
		imprimirLinea($linea,"", array(1=>"align='center'", 2=>"align='center'", 3=>"align='center'", 4=>"align='center'", 5=>"align='center'", 6=>"align='center'", 7=>"align='center'", 8=>"align='center'", 9=>"align='center'", 10=>"align='center'", 11=>"align='center'", 12=>"align='center'", 13=>"align='center'", 14=>"align='center'", 15=>"align='center'", 16=>"align='center'", 17=>"align='center'",18=>"align='center'"));
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_center",  2=>"txt_center", 3=>"txt_center", 4=>"txt_center", 5=>"txt_center", 6=>"txt_center", 7=>"txt_center", 8=>"txt_center", 9=>"txt_center", 10=>"txt_center", 11=>"txt_center", 12=>"txt_center", 13=>"txt_center", 14=>"txt_center", 15=>"txt_center", 16=>"txt_center", 17=>"txt_center", 18=>"txt_center"));
}


//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro;
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
