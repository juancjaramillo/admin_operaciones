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
if(isset($_POST["id_vehiculo"]) && $_POST["id_vehiculo"] != "")
	$id_vehiculo = $_POST["id_vehiculo"];
elseif(isset($_GET["id_vehiculo"]) && $_GET["id_vehiculo"] != "")
	$id_vehiculo = $_GET["id_vehiculo"];

$id_micro = "";
if(isset($_POST["id_micro"]) && $_POST["id_vehiculo"] != "")
	$id_micro = $_POST["id_micro"];
elseif(isset($_GET["id_micro"]) && $_GET["id_vehiculo"] != "")
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

$lineaPrimera = array("FECHA", "CÓDIGO/PLACA", "RUTA", "Kms Fin", "Kms Inicio", "Kms Total", "Kms Ruta", "kms Transporte");

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


if($id_vehiculo != "")
	$cond.= " AND v.id='".$id_vehiculo."'";
if($id_micro != "")
	$cond.= " AND p.id IN (SELECT id_peso FROM rec.movimientos_pesos mp LEFT JOIN rec.movimientos m ON m.id=mp.id_movimiento WHERE m.id_micro='".$id_micro."' )";

$consulta = " SELECT inicio::date as fecmov,v.codigo||'/'||v.placa as vehiculo,i.codigo as ruta,m.km_final,minikms.km_inicial,sum(m.km_final-minikms.km_inicial)as recorrido_total,
				sum(kmsruta.kms_ruta) as kms_ruta, 
				CASE WHEN sum(kmsruta.kms_ruta) is null THEN sum(m.km_final-minikms.km_inicial)
				ELSE (sum(m.km_final-minikms.km_inicial)-sum(kmsruta.kms_ruta)) 
				END as kms_transporte
				FROM rec.movimientos m 
				LEFT JOIN (SELECT id_movimiento,min(km) as km_inicial, min(horometro) as horo_inicial
				FROM rec.desplazamientos 
				where hora_inicio is not null
				group by id_movimiento) minikms ON m.id=minikms.id_movimiento
				LEFT JOIN (select id_movimiento,sum(kms_ruta)as kms_ruta from
				(SELECT id_movimiento,numero_viaje, (max(km)-min(km)) as kms_ruta
				FROM rec.desplazamientos 
				WHERE id_tipo_desplazamiento IN (3,4) and hora_inicio is not null
				and hora_inicio::date >= '".$inicio."' AND hora_inicio::date<='".$final."'
				group by id_movimiento,numero_viaje)x
				group by id_movimiento) kmsruta on m.id=kmsruta.id_movimiento
				LEFT JOIN micros i ON i.id=m.id_micro 
				LEFT JOIN vehiculos v ON v.id=m.id_vehiculo
				LEFT JOIN ases a ON a.id = i.id_ase
				WHERE inicio::date >= '".$inicio."' AND inicio::date<='".$final."'
				AND a.id IN (SELECT id FROM ases WHERE id_centro = ".$centro .")
				group by inicio::date,vehiculo,i.codigo,m.km_final,minikms.km_inicial 
				order by inicio::date,vehiculo";
//echo $consulta;
$qid = $db->sql_query($consulta);
while($query = $db->sql_fetchrow($qid))
{
//	$linea = array($query["feentrada"], implode(", ", $turno), $query["placa"], implode(", ", $comuna), implode(", ", $codigo), implode(", ", $numero_orden), $query["lugar_descargue"], $query["feentrada"], $query["hoentrada"], $query["fesalida"], $query["hosalida"], implode(", ", $tipor), implode(", ", $servicio), $query["peso_inicial"], $query["peso_final"], $neto, $query["tiquete_salida"]);

	$linea = array($query["fecmov"], $query["vehiculo"], $query["ruta"],$query["km_inicial"],$query["km_final"],$query["recorrido_total"], $query["kms_ruta"],$query["kms_transporte"] );
	if($html)
		imprimirLinea($linea,"", array(1=>"align='center'", 2=>"align='center'", 3=>"align='center'", 4=>"align='center'", 5=>"align='center'", 6=>"align='center'", 7=>"align='center'", 8=>"align='center'", 9=>"align='center'", 10=>"align='center'", 11=>"align='center'", 12=>"align='center'", 13=>"align='center'", 14=>"align='center'", 15=>"align='center'", 16=>"align='center'", 17=>"align='center'",18=>"align='center'"));
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_center",  2=>"txt_center", 3=>"txt_center", 4=>"txt_center", 5=>"txt_center", 6=>"txt_center", 7=>"txt_center", 8=>"txt_center", 9=>"txt_center", 10=>"txt_center", 11=>"txt_center", 12=>"txt_center", 13=>"txt_center", 14=>"txt_center", 15=>"txt_center", 16=>"txt_center", 17=>"txt_center", 18=>"txt_center"));
}


//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro."&id_vehiculo=".$id_vehiculo."&id_micro=".$id_micro;
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
