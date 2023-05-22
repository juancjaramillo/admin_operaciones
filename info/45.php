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


$id_vehiculo = "";
if(isset($_POST["id_vehiculo"]))
	$id_vehiculo = $_POST["id_vehiculo"];
elseif(isset($_GET["id_vehiculo"]))
	$id_vehiculo = $_GET["id_vehiculo"];

$vista = "general";
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
	$estilosTit = array(1=>"azul_izq");
	$estilosText = array(1=>"txt_izq");
}




$cond = "";
$cond.= " AND c.id_empresa = '".$centro."'";

//$cond.= " AND a.id_centro = '".$centro."'"; John Cambio a c.id_empresa y en la consulta quite el ase  LEFT JOIN ases a ON a.id_centro = c.id

if($id_vehiculo != "")
	$cond.= " AND v.id='".$id_vehiculo."'";

$datos = $aceites = $vehiculos = $fechas = $dxGraf = $labels = array();
$consulta  = "SELECT v.codigo,v.placa,  el.elemento, o.fecha_ejecucion_inicio, el.id as id_elemento, v.id as id_vehiculo, ote.cantidad
	FROM mtto.ordenes_trabajo_elementos ote 
	LEFT JOIN mtto.elementos el  ON el.id=ote.id_elemento
	LEFT JOIN mtto.ordenes_trabajo o ON o.id = ote.id_orden_trabajo
	LEFT JOIN mtto.estados_ordenes_trabajo e ON e.id = o.id_estado_orden_trabajo
	LEFT JOIN mtto.equipos eq ON eq.id = o.id_equipo
	LEFT JOIN vehiculos v ON v.id=eq.id_vehiculo
	LEFT JOIN centros c ON c.id = v.id_centro
	WHERE e.cerrado AND o.fecha_ejecucion_inicio::date >='".$inicio."' AND o.fecha_ejecucion_inicio::date <='".$final."' AND el.tipoe=13 " .$cond."
	ORDER BY v.codigo, v.placa";

//echo $consulta;
$qid = $db->sql_query($consulta);
while($ele = $db->sql_fetchrow($qid))
{
	$aceites[$ele["id_elemento"]] = $ele["elemento"];
	$vehiculos[$ele["id_vehiculo"]] = array("codigo"=>$ele["codigo"], "placa"=>$ele["placa"]);
	$fechas[strftime("%Y-%m",strtotime($ele["fecha_ejecucion_inicio"]))] = strftime("%Y-%m",strtotime($ele["fecha_ejecucion_inicio"]));
	$fechasDos[$ele["id_elemento"]][strftime("%Y-%m",strtotime($ele["fecha_ejecucion_inicio"]))][strftime("%Y-%m-%d",strtotime($ele["fecha_ejecucion_inicio"]))]= strftime("%Y-%m-%d",strtotime($ele["fecha_ejecucion_inicio"]));

	if(!isset($datos[$ele["id_elemento"]][$ele["id_vehiculo"]][strftime("%Y-%m",strtotime($ele["fecha_ejecucion_inicio"]))]))
		$datos[$ele["id_elemento"]][$ele["id_vehiculo"]][strftime("%Y-%m",strtotime($ele["fecha_ejecucion_inicio"]))]=0;
	$datos[$ele["id_elemento"]][$ele["id_vehiculo"]][strftime("%Y-%m",strtotime($ele["fecha_ejecucion_inicio"]))]+=$ele["cantidad"];

	//vista dias
	if(!isset($datosDos[$ele["id_elemento"]][$ele["id_vehiculo"]][strftime("%Y-%m",strtotime($ele["fecha_ejecucion_inicio"]))]["dias"][strftime("%Y-%m-%d",strtotime($ele["fecha_ejecucion_inicio"]))]))
		$datosDos[$ele["id_elemento"]][$ele["id_vehiculo"]][strftime("%Y-%m",strtotime($ele["fecha_ejecucion_inicio"]))]["dias"][strftime("%Y-%m-%d",strtotime($ele["fecha_ejecucion_inicio"]))]    =0;
	$datosDos[$ele["id_elemento"]][$ele["id_vehiculo"]][strftime("%Y-%m",strtotime($ele["fecha_ejecucion_inicio"]))]["dias"][strftime("%Y-%m-%d",strtotime($ele["fecha_ejecucion_inicio"]))]+=$ele["cantidad"];
}


asort($fechas);
asort($fechasDos);
foreach($fechasDos as $key => $valUno)
{
	asort($valUno);
	$fechasDos[$key] = $valUno;
	foreach($valUno as $keyDos => $valDos)
	{
		asort($valDos);
		$fechasDos[$key][$keyDos] = $valDos;
	}
}


$lineaUno = $lineaDos = $lineaTres = "";
if($html)
{
	echo '
		<table width="70%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">';
		if($vista == "general") 
			echo '<tr><th rowspan=2>VEHÍCULO</th>';
		else
			echo '<tr><th rowspan=3>VEHÍCULO/DÍAS</th>';
				
			foreach($aceites as $key => $da)
			{
				$totalDias = 0;
				foreach($fechasDos[$key] as $mes => $dias)
				{
					if($vista == "general")
						$lineaDos .= "<th>".strtoupper(strftime("%B/%Y",strtotime($mes."-01")))."</th>";
					else
					{
						$totalDias += count($dias);
						$lineaDos .= "<th colspan='".count($dias)."'>".strtoupper(strftime("%B/%Y",strtotime($mes."-01")))."</th>";
						foreach($dias as $dx)
						{
							$lineaTres.= "<th>".strtoupper(strftime("%d",strtotime($dx)))."</th>";
						}
					}
				}

				if($vista == "general")
					$lineaUno .=  '<th height="40" colspan='.count($fechasDos[$key]).'>'.$da.'</th>';	
				else
					$lineaUno .=  '<th height="40" colspan='.$totalDias.'>'.$da.'</th>';	
			}
			echo $lineaUno."</tr><tr>".$lineaDos."</tr><tr>".$lineaTres."</tr>";
}else
{
	$lineaUno = $lineaDos = $lineaTres = array();;
	if($vista == "general")
		titulos_uno_xls($workbook, $worksheet, $fila, $columna, array("VEHÍCULO"), 0,1);
	else
	{
		titulos_uno_xls($workbook, $worksheet, $fila, $columna, array("VEHÍCULO/DÍAS"), 0,2);
		$fila++;
	}

	foreach($aceites as $key => $da)
	{
		$columnaInicial = $columna;
		$totalDias = 0;
		if($vista == "general")
		{
			titulos_uno_xls($workbook, $worksheet, $fila, $columna, array($da), count($fechasDos[$key])-1);
			$columna+=count($fechasDos[$key])-1;
		}

		foreach($fechasDos[$key] as $mes => $dias)
		{
			if($vista == "general")
				$lineaDos[] = strtoupper(strftime("%B/%Y",strtotime($mes."-01")));
			else
			{
				$colAnt = $columna;
				$totalDias += count($dias);
				titulos_uno_xls($workbook, $worksheet, $fila, $columna, array(strtoupper(strftime("%B/%Y",strtotime($mes."-01")))),count($dias)-1);
				$fila++;
				$columna--;
				foreach($dias as $dx)
					titulos_uno_xls($workbook, $worksheet, $fila, $columna, array(strtoupper(strftime("%d",strtotime($dx)))));
				$columna = $colAnt+1;
				$columna+=count($dias) -1  ;
				$fila--;
			}
		}
		
		if($vista=="dias")
		{
			$columna = $columnaInicial;
			$fila = 2;
			titulos_uno_xls($workbook, $worksheet, $fila, $columna, array($da),$totalDias-1);
			$columna=$columnaInicial+$totalDias;
			$fila++;
		}
	}

	$fila++;$columna=1;
	//preguntar($lineaDos);

	if($vista == "general")
		titulos_uno_xls($workbook, $worksheet, $fila, $columna, $lineaDos);




	$fila++;$columna=0;
}

//die;
//datos
foreach($vehiculos as $idVeh => $codigo)
{
	$linea = array($codigo["codigo"]."/".$codigo["placa"]);
	foreach($aceites as $idAceite => $nombreAceite)
	{
		foreach($fechasDos[$idAceite] as $date => $dx)
		{
			if($vista == "general")
			{
				if(isset($datos[$idAceite][$idVeh][$date]))
				{
					$linea[] = $datos[$idAceite][$idVeh][$date];
					$dxGraf[$idAceite][ucfirst(strftime("%B/%Y",strtotime($date."-01")))][] = $datos[$idAceite][$idVeh][$date];
				}
				else
				{
					$linea[] = "";
					$dxGraf[$idAceite][ucfirst(strftime("%B/%Y",strtotime($date."-01")))][] = 0;
				}
			}else
			{
				foreach($dx as $dia)
				{
					if(isset($datosDos[$idAceite][$idVeh][$date]["dias"][$dia]))
						$linea[] = $datosDos[$idAceite][$idVeh][$date]["dias"][$dia];
					else
						$linea[] = "";
				}

				//gráfica
				if(isset($datos[$idAceite][$idVeh][$date]))
					$dxGraf[$idAceite][ucfirst(strftime("%B/%Y",strtotime($date."-01")))][] = $datos[$idAceite][$idVeh][$date];
				else
					$dxGraf[$idAceite][ucfirst(strftime("%B/%Y",strtotime($date."-01")))][] = 0;
			}
		}
	}
	
	$labels[] = $codigo["codigo"];
	
	if($html)
		imprimirLinea($linea);
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea);
	
}


//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro."&id_vehiculo=".$id_vehiculo."&vista=".$vista;
	echo "</table><br /><br /><table width='98%' align='center'>";
	$i=1;

	foreach($aceites as $idAceite => $nombreAceite)
	{
		if($i==1) echo "<tr>";
		echo "<td width='50%'>";
		$dxTemp = array("data"=>$dxGraf[$idAceite], "labels"=>$labels);
		graficaMultiBar($dxTemp, $nombreAceite, "Consumo");		
		echo "</td>";
		if($i==2)
		{
			echo "</tr>";
			$i=0;
		}
		$i++;
	}

	echo "
	</table><table width='98%' align='center'>
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