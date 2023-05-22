<?
include("../application.php");

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

$html = true;
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

$titulosUno = array("VEHÍCULO", "TOTAL KMs RECORRIDOS", "TOTAL HORAS MOTOR", "COMBUSTIBLE", "RENDIMIENTO (km/gal)", "RENDIMIENTO (gal/hora)", "ASE", "TOTAL KMs RECORRIDOS", "TOTAL KMs RUTA", "TOTAL KMs TRANSPORTE");
if($html)
{
	echo '
		<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
			<tr>';
	foreach($titulosUno as $tt)
	{
		echo '<th height="40">'.$tt.'</th>';
	}
	echo "</tr>";
}else
{
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, $titulosUno);
	$fila++;$columna=0;
}

$datos = array();
$qid = $db->sql_query("select * from 
						(SELECT  v.codigo||'/'||v.placa as vehiculo,max(m.horometro_final)as horo_final,min(minikms.horo_inicial) as horo_inicial,
						 	sum(m.combustible) as combustible
								FROM rec.movimientos m 
									LEFT JOIN (SELECT id_movimiento,min(km) as km_inicial, min(horometro) as horo_inicial
													FROM rec.desplazamientos 
																where hora_inicio is not null
																			group by id_movimiento) minikms ON m.id=minikms.id_movimiento
						LEFT JOIN micros i ON i.id=m.id_micro 
							LEFT JOIN vehiculos v ON v.id=m.id_vehiculo
								LEFT JOIN ases a ON a.id = i.id_ase
									WHERE inicio::date >= '".$inicio."' AND inicio::date<='".$final."'
										AND a.id IN (SELECT id FROM ases WHERE id_centro = ".$centro.")
											group by vehiculo
												order by vehiculo) as mes
			LEFT JOIN
				(SELECT v.codigo||'/'||v.placa as vehiculo,sum(m.km_final-minikms.km_inicial)as recorrido_total,
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
					AND a.id IN (SELECT id FROM ases WHERE id_centro = ".$centro.")
						group by vehiculo
							order by vehiculo)recorrido
							ON (mes.vehiculo=recorrido.vehiculo)
	LEFT JOIN 
		(SELECT v.codigo||'/'||v.placa as vehiculo,a.ase,sum(m.km_final-minikms.km_inicial)as recorrido_total,
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
			AND a.id IN (SELECT id FROM ases WHERE id_centro = ".$centro.")
				group by vehiculo, ase
					order by vehiculo, ase) as ases
					ON (mes.vehiculo=ases.vehiculo)");
while($query = $db->sql_fetchrow($qid))
{
	$datos[$query["vehiculo"]]["ases"][] = $query;
	$datos[$query["vehiculo"]]["cb"]=$query["combustible"];
	$datos[$query["vehiculo"]]["horo"]=$query["horo_final"] - $query["horo_inicial"];

	if(!isset($datos[$query["vehiculo"]]["km"])) $datos[$query["vehiculo"]]["km"]=0;
	$datos[$query["vehiculo"]]["km"]+=$query["recorrido_total"];
}

foreach($datos as $codigo => $dx)
{
	$rowspan=count($dx["ases"]);
	$i = 1;
	foreach($dx["ases"] as $ases)
	{
		if($i==1)
		{
			$linea = array($codigo, number_format($dx["km"], 2, ",", "."), number_format($dx["horo"], 0, ",", "."), number_format($dx["cb"], 2, ",", "."), number_format($dx["km"]/$dx["cb"], 2, ",", "."), number_format($dx["cb"]/$dx["horo"],2, ",", "."), $ases["ase"], number_format($ases["recorrido_total"], 2, ",", "."), number_format($ases["kms_ruta"], 2, ",", "."), number_format($ases["kms_transporte"], 2, ",", "."));
			if($html)
				imprimirLinea($linea, "", array(1=>"align='center' rowspan='".$rowspan."'", 2=>"rowspan='".$rowspan."'", 3=>"rowspan='".$rowspan."'", 4=>"rowspan='".$rowspan."'", 5=>"rowspan='".$rowspan."'", 6=>" rowspan='".$rowspan."'", 7=>"align='left'"));	
			else
				imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_center", 7=>"txt_izq"), array(), array(1=>$rowspan-1, 2=>$rowspan-1, 3=>$rowspan-1, 4=>$rowspan-1, 5=>$rowspan-1, 6=>$rowspan-1));
		}
		else
		{
			$linea = array($ases["ase"],number_format($ases["recorrido_total"], 2, ",", "."), number_format($ases["kms_ruta"], 2, ",", "."), number_format($ases["kms_transporte"], 2, ",", "." ));
			if($html)
					imprimirLinea($linea);	
			else
			{
				$linea = array_merge(array("","","","","",""),$linea);
				imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array( 7=>"txt_izq"));
			}
		}
		$i++;
	}
}

//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro;
	echo "</table>";
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
