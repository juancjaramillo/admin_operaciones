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

$titulosUno = array("FECHA", "VEHICULO", "ASE", "RUTA", "TONS", "TONS/Viaje", "HORA SALIDA BASE", "HORA REGRESO BASE", "TOTAL TIEMPO", "CEDULA", "NOMBRES", "CARGO", "HORA INICIO", "HORA FINAL", "TIEMPO TRABAJADO");
if($html)
{
	echo '
		<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
			<tr>';
	foreach($titulosUno as $tt)
	{
		echo '<th height="35">'.$tt.'</th>';
	}
	echo "</tr>";
}else
{
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, $titulosUno);
	$fila++;$columna=0;
}

$datos = array();
$qid = $db->sql_query("SELECT m.id,inicio::date as fecmov,v.codigo||' / '||v.placa as vehiculo,a.ase,i.codigo as ruta,
						sum(rec.pesos.peso_total) as peso_total,despla.hora_inicio,m.final,(m.final-despla.hora_inicio) as horas_vehi,
						p.cedula,p.apellido||' '||p.nombre as nombres,c.nombre as cargo,
						per.hora_inicio as inicio_trip,per.hora_fin as fin_trip,(per.hora_fin-per.hora_inicio) as horas_trip
						FROM rec.movimientos m 
						LEFT JOIN (SELECT id_movimiento,min(hora_inicio) as hora_inicio, max(hora_fin) as hora_fin
								FROM rec.desplazamientos 
								where hora_inicio is not null
								group by id_movimiento) despla ON m.id=despla.id_movimiento
						LEFT JOIN rec.movimientos_personas per on m.id=per.id_movimiento
						LEFT JOIN rec.movimientos_pesos pes on m.id=pes.id_movimiento
						LEFT JOIN rec.pesos on pes.id_peso=rec.pesos.id
						LEFT JOIN micros i ON i.id=m.id_micro 
						LEFT JOIN vehiculos v ON v.id=m.id_vehiculo
						LEFT JOIN ases a ON a.id = i.id_ase
						LEFT JOIN personas p on per.id_persona=p.id
						LEFT JOIN cargos c on p.id_cargo=c.id	
						WHERE inicio::date >= '".$inicio."' AND inicio::date<='".$final."'
						AND a.id IN (SELECT id FROM ases WHERE id_centro = ".$centro.")
						and despla.hora_inicio is not null
						group by m.id,m.inicio,vehiculo,ase,ruta,despla.hora_inicio,final,horas_vehi,cedula,p.apellido,p.nombre,c.nombre,inicio_trip,fin_trip,horas_trip
						order by fecmov,vehiculo,despla.hora_inicio,c.nombre desc");

while($query = $db->sql_fetchrow($qid))
{
	$qid2 = $db->sql_query("select peso_total from rec.movimientos_pesos m
	left join rec.pesos p on m.id_peso=p.id where id_movimiento=$query[id] 
	order by fecha_entrada");
	while($query2 = $db->sql_fetchrow($qid2))
	{
		$datos2 .= 'Viaje N.'.$i.':'.number_format($query2["peso_total"],2).' ';
		$i++;
	}
	$i=1;
	$datos[$query["id"]]["id"][] = $query;
	$datos[$query["id"]]["fecmov"]=$query["fecmov"];
	$datos[$query["id"]]["vehiculo"]=$query["vehiculo"];
	$datos[$query["id"]]["ase"]=$query["ase"];
	$datos[$query["id"]]["ruta"]=$query["ruta"];
	$datos[$query["id"]]["peso_total"]=$query["peso_total"];
	$datos[$query["id"]]["peso_parcial"]=$datos2;
	$datos[$query["id"]]["hora_inicio"]=$query["hora_inicio"];
	$datos[$query["id"]]["final"]=$query["final"];
	$datos[$query["id"]]["horas_vehi"]=$query["horas_vehi"];
	$datos[$query["id"]]["cedula"]=$query["cedula"];
	$datos[$query["id"]]["nombre"]=$query["nombres"];
	$datos[$query["id"]]["cargo"]=$query["cargo"];
	$datos[$query["id"]]["inicio_trip"]=$query["inicio_trip"];
	$datos[$query["id"]]["fin_trip"]=$query["fin_trip"];
	$datos[$query["id"]]["horas_trip"]=$query["horas_trip"];
	$datos2='';
}

foreach($datos as $id => $dx)
{
	$rowspan=count($dx["id"]);
	$i = 1;
	foreach($dx["id"] as $id)
	{
		if($i==1)
		{
			$linea = array($dx["fecmov"],$dx["vehiculo"],$dx["ase"],$dx["ruta"],$dx["peso_total"],$dx["peso_parcial"],$dx["hora_inicio"],$dx["final"],$dx["horas_vehi"],$id["cedula"],$id["nombres"],$id["cargo"],$id["inicio_trip"],$id["fin_trip"],$id["horas_trip"]);
			
			if($html)
				imprimirLinea($linea, "", array(1=>"align='center' rowspan='".$rowspan."'", 2=>" align='center' rowspan='".$rowspan."'", 3=>"align='center' rowspan='".$rowspan."'", 4=>"align='center' rowspan='".$rowspan."'", 5=>"rowspan='".$rowspan."'", 6=>"rowspan='".$rowspan."'", 7=>"align='center' rowspan='".$rowspan."'", 8=>"align='center' rowspan='".$rowspan."'", 9=>"align='center' rowspan='".$rowspan."'", 10=>"align='right'", 11=>"align='left'", 12=>"align='left'", 13=>"align='center'",14=>"align='center'",15=>"align='center'"));	
			else
				imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_center", 10=>"txt_der", 11=>"txt_izq", 12=>"txt_izq", 13=>"txt_center",14=>"txt_center",15=>"txt_center"), array(), array(1=>$rowspan-1, 2=>$rowspan-1, 3=>$rowspan-1, 4=>$rowspan-1, 5=>$rowspan-1, 6=>$rowspan-1, 7=>$rowspan-1, 8=>$rowspan-1, 9=>$rowspan-1  ));
		}
		else
		{
			$linea = array($id["cedula"],$id["nombres"],$id["cargo"],$id["inicio_trip"],$id["fin_trip"],$id["horas_trip"]);
			if($html)
					imprimirLinea($linea,"",array(1=>"align='right'", 2=>"align='left'", 3=>"align='left'", 4=>"align='center'",5=>"align='center'",6=>"align='center'"));	
			else
			{
				$linea = array_merge(array("","","","","","","","",""),$linea);
				imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array( 8=>"txt_izq"));
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
