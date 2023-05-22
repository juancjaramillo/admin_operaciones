<?
include_once("../application.php");

if(!isset($_SESSION[$CFG->sesion]["user"])){
  $errorMsg="No existe la sesión.";
  error_log($errorMsg);
  die($errorMsg);
}

include($CFG->dirroot."/templates/header_2panel.php");?>
<div id="right1">
  <?include($CFG->dirroot."/info/templates/menu_informes.php");?>
</div>
<div id="center1">

<?
$tipo=nvl($_POST["tipo"],"");

switch(nvl($tipo)){

	case "produccion_diaria_servicio";
		produccion_diaria_servicio($_POST);
	break;

	case "produccionxmicro":
		produccionxmicro($_POST);
	break;

	case "produccionxase":
		produccionxase($_POST);
	break;

	case "descarguediario":
		descarguediario($_POST);
	break;

	case "tiemposxruta":
		tiemposxruta($_POST);
	break;

	case "tiemposxmovimiento":
		tiemposxmovimiento($_POST);
	break;

	case "sobrepesos":
		sobrepesos($_POST);
	break;

	case "consumocombustible":
		consumocombustible($_POST);
	break;

	case "barridoxcoordinador":
		barridoxcoordinador($_POST);
	break;

	case "consumosdiarios":
		consumosdiarios($_POST);
	break;

	case "indicador_factorcarga":
		indicador_factorcarga($_POST);
	break;

	case "indicador_factorutilizacion":
		indicador_factorutilizacion($_POST);
	break;

}


function produccion_diaria_servicio($frm)
{
	global $db, $CFG, $ME;

	echo '
		<table width="98%">
			<tr>
				<td class="azul_16" align="center" height="30" valign="bottom">PRODUCCIÓN DIARIA POR SERVICIO</td>
			</tr>
			<tr>
				<td class="azul_16" align="center" height="40" valign="center">'.$frm["inicio"]." / ".$frm["fin"].'</td>
			</tr>
		</table>
		<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla">
					<tr>
						<th height="40">FECHA</th>';

	$servicios = array();
	$qid = $db->sql_query("SELECT * FROM servicios ORDER BY servicio");
	while($ser = $db->sql_fetchrow($qid))
	{
		echo '<th height="40">'.strtoupper($ser["servicio"]).'</th>';
		$servicios[$ser["id"]] = $ser;
	}
	echo '<th height="40">TOTAL</th></tr>';

	$diasBTW = restarFechas($frm["fin"],$frm["inicio"]);
	for($i=0 ; $i<=$diasBTW; $i++)
	{
		list($anio,$mes,$dia)=split("-",$frm["inicio"]);
		$dia = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + $i * 24 * 60 * 60);
		$datos = array("dia"=>array($dia),"viajes"=>array("No. Viajes"),"tonv"=>array("Toneladas/Viaje"),"kms"=>array("Kms. Recorridos"),"comb"=>array("Combustible"),"kmsgal"=>array("kms/galón"));
		$totalProd = $totalViajes = $totalTonViaj = $totalKms = 0;
		foreach($servicios as $idSer => $dxServicio)
		{
			//producción
			if($dxServicio["esquema"] == "rec")
			{
				$qid = $db->sql_row("SELECT sum(m.peso_final-m.peso_inicial) as prod FROM rec.movimientos m LEFT JOIN micros i ON i.id=m.id_micro WHERE i.id_servicio='".$idSer."' AND m.inicio::date='".$dia."'");
				if($qid["prod"] != "")
					$totalProd+=$qid["prod"];
				$datos["dia"][] = $qid["prod"];
			
				//numero viajes
				$viajes = 0;
				$cons = "SELECT max(numero_viaje) as num FROM rec.desplazamientos d LEFT JOIN rec.movimientos m ON m.id = d.id_movimiento LEFT JOIN micros i ON i.id=m.id_micro WHERE i.id_servicio='".$idSer."' AND m.inicio::date='".$dia."' GROUP BY id_movimiento";
				$qidV = $db->sql_query($cons);
				while($queryV = $db->sql_fetchrow($qidV))
				{
					$viajes+=$queryV["num"];
				}
				$datos["viajes"][] = $viajes;
				$totalViajes+=$viajes;

				//toneladas por viaje
				if($viajes==0)
					$datos["tonv"][] = "";
				else
				{
					$datos["tonv"][] = number_format($qid["prod"]/$viajes, 2, ",", ".");	
					$totalTonViaj+=$qid["prod"]/$viajes;
				}

				//desplazamientos
				$kms = 0;
				$qidDes = $db->sql_query("SELECT max(d.km) as maxkm, min(d.km) as minkm FROM rec.desplazamientos d LEFT JOIN rec.movimientos m ON m.id = d.id_movimiento LEFT JOIN micros i ON i.id=m.id_micro WHERE i.id_servicio='".$idSer."' AND m.inicio::date='".$dia."' GROUP BY id_movimiento");
				while($des = $db->sql_fetchrow($qidDes))
				{
					$kms+=$des["maxkm"]-$des["minkm"];
				}
				$datos["kms"][] = number_format($kms, 2, ",", ".");
				$totalKms+=$kms;





			}
			else
			{
				$datos["dia"][] = "";
				$datos["viajes"][] = "";
				$datos["tonv"][] = "";
				$datos["kms"][] = "";
			}





		}
		$datos["dia"][] = $totalProd;
		$datos["viajes"][] = $totalViajes;
		$datos["tonv"][] = number_format($totalTonViaj, 2, ",", ".");
		$datos["kms"][] = number_format($totalKms, 2, ",", ".");


		imprimirLinea($datos["dia"],"#b2d2e1");		
		imprimirLinea($datos["viajes"]);		
		imprimirLinea($datos["tonv"]);		
		imprimirLinea($datos["kms"]);		
		imprimirLinea($datos["comb"]);		
		imprimirLinea($datos["kmsgal"]);		

	
	
	}



	echo '</table>';
}


function produccionxmicro($frm)
{
	global $db, $CFG, $ME;

	echo '
		<table width="98%">
			<tr>
				<td class="azul_16" align="center" height="30" valign="bottom">PRODUCCIÓN POR RUTA</td>
			</tr>
			<tr>
				<td class="azul_16" align="center" height="40" valign="center">'.$frm["inicio"]." / ".$frm["fin"].'</td>
			</tr>
		</table>
		<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla">
			<tr>
				<th height="40">SERVICIO</th>
				<th>MICRO</th>
				<th>TONELADAS</th>
				<th>VIAJES</th>
				<th>TONS/VIAJE</th>
			<tr>';

	$stylos = array(1=>"colspan=2");
	$qid = $db->sql_query("SELECT * FROM servicios ORDER BY servicio");
	while($ser = $db->sql_fetchrow($qid))
	{
		if($ser["esquema"] == "rec")
		{
			$micros = $codigos = array();
			$qidMov = $db->sql_query("SELECT m.*, i.codigo FROM rec.movimientos m LEFT JOIN micros i ON i.id=m.id_micro WHERE i.id_servicio='".$ser["id"]."' AND inicio::date >= '".$frm["inicio"]."' AND final::date<='".$frm["fin"]."'");
			while($mov = $db->sql_fetchrow($qidMov))
			{
				$codigos[$mov["id"]] = $mov["codigo"];

				//toneladas
				if(isset($micros["id"]["ton"]))
					$micros[$mov["id"]]["ton"]+= $mov["peso_final"]-$mov["peso_inicial"];
				else
					$micros[$mov["id"]]["ton"] = $mov["peso_final"]-$mov["peso_inicial"];

				//viajes
				$qidV = $db->sql_row("SELECT max(numero_viaje) as num FROM rec.desplazamientos WHERE id_movimiento='".$mov["id"]."'");
				if(isset($micros["id"]["viaj"]))
					$micros[$mov["id"]]["viaj"]+= nvl($qidV["num"],0);
				else
					$micros[$mov["id"]]["viaj"] = nvl($qidV["num"],0); 
			}

			$ton=$viajes=$tonviaje=0;
			foreach($micros as $key => $dx)
			{
				$div = 0;
				if($dx["viaj"]!= 0)
					$div = $dx["ton"]/$dx["viaj"];
				$linea = array($ser["servicio"], $codigos[$key], number_format($dx["ton"], 2, ",", "."), $dx["viaj"], number_format($div, 2, ",", "."));
				imprimirLinea($linea);
				$ton+=$dx["ton"];
				$viajes+=$dx["viaj"];
				$tonviaje+=$div;
			}
			$linea = array("Total ".$ser["servicio"], number_format($ton, 2, ",", "."),$viajes,number_format($tonviaje, 2, ",", "."));
			imprimirLinea($linea, "#b2d2e1", $stylos);
		}
	
	
	
	
	
	
	
	
	}
	echo "</table>";
}


function produccionxase($frm)
{
	global $db, $CFG, $ME;

	echo '
		<table width="98%">
			<tr>
				<td class="azul_16" align="center" height="30" valign="bottom">PRODUCCIÓN POR ASE</td>
			</tr>
			<tr>
				<td class="azul_16" align="center" height="40" valign="center">'.$frm["inicio"]." / ".$frm["fin"].'</td>
			</tr>
		</table>
		<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla">
			<tr>
				<th height="40">ASE</th>
				<th>TONELADAS</th>
				<th>VIAJES</th>
				<th>TONS/VIAJE</th>
			<tr>';

	
	$ase = array();
	$qid = $db->sql_query("SELECT id, ase FROM ases ORDER BY ase");
	while($ser = $db->sql_fetchrow($qid))
	{
		$ase[$ser["id"]] = array("ase"=>$ser["ase"],"ton"=>0, "viaj"=>0, "tonviaj"=>0);
			
		//recolección
		$qidMov = $db->sql_query("SELECT m.*, i.codigo FROM rec.movimientos m LEFT JOIN micros i ON i.id=m.id_micro WHERE i.id_ase='".$ser["id"]."' AND inicio::date >= '".$frm["inicio"]."' AND final::date<='".$frm["fin"]."'");
		while($mov = $db->sql_fetchrow($qidMov))
		{
			//toneladas
			$ase[$ser["id"]]["ton"]+= $mov["peso_final"]-$mov["peso_inicial"];
			
			//viajes
			$qidV = $db->sql_row("SELECT max(numero_viaje) as num FROM rec.desplazamientos WHERE id_movimiento='".$mov["id"]."'");
			$ase[$ser["id"]]["viaj"]+= nvl($qidV["num"],0);
		}






		//ton /viaje por ase
		$div = 0;
		if($ase[$ser["id"]]["viaj"]!= 0)
			$div = number_format($ase[$ser["id"]]["ton"]/$ase[$ser["id"]]["viaj"], 2, ",", ".");
		$ase[$ser["id"]]["tonviaj"] = $div;
		$ase[$ser["id"]]["ton"]=number_format($ase[$ser["id"]]["ton"], 2, ",", ".");
		imprimirLinea($ase[$ser["id"]]);
	}
	echo "</table>";
}


function descarguediario($frm)
{
	global $db, $CFG, $ME;

	echo '
		<table width="98%">
			<tr>
				<td class="azul_16" align="center" height="30" valign="bottom">DESCARGUE DIARIO</td>
			</tr>
			<tr>
				<td class="azul_16" align="center" height="40" valign="center">'.$frm["inicio"]." / ".$frm["fin"].'</td>
			</tr>
		</table>
		<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla">
			<tr>
				<th height="40">FECHA DESCARGUE</th>
				<th>SITIO DISPOSICIÓN</th>
				<th>VIAJES</th>
				<th>TONELADAS DISPUESTAS</th>
			<tr>';

	$disp = $lugares = array();
	$qidMov = $db->sql_query("SELECT m.id, peso_inicial, peso_final, to_char(inicio,'YYYY-MM-DD') as fecha, id_lugar_descargue as id_lugar, l.nombre
			FROM rec.movimientos m
			LEFT JOIN lugares_descargue l ON l.id=id_lugar_descargue
			WHERE inicio::date >= '".$frm["inicio"]."' AND final::date<='".$frm["fin"]."' 
			ORDER BY inicio");
	while($mov = $db->sql_fetchrow($qidMov))
	{
		$lugares[$mov["id_lugar"]] = $mov["nombre"];
		if(!isset($disp[$mov["fecha"]][$mov["id_lugar"]]))
			$disp[$mov["fecha"]][$mov["id_lugar"]] = array("viaje"=>0, "ton"=>0);

		$viaje = $db->sql_row("SELECT max(numero_viaje) as num FROM rec.desplazamientos WHERE id_movimiento='".$mov["id"]."'");
		$disp[$mov["fecha"]][$mov["id_lugar"]]["viaje"]+= nvl($viaje["num"],0);
		$disp[$mov["fecha"]][$mov["id_lugar"]]["ton"]+= $mov["peso_final"]-$mov["peso_inicial"];
	}

	
	
	
	
	
	
	
	$stilos=array(2=>"align='left'");	
	foreach($disp as $fecha => $movs)
	{
		foreach($movs as $idSitio => $dx)
		{
			$linea = array($fecha, $lugares[$idSitio], $dx["viaje"], number_format($dx["ton"], 2, ",", "."));
			imprimirLinea($linea,"",$stilos);
		}
	}
	echo "</table>";
}

function tiemposxruta($frm)
{
	global $db, $CFG, $ME;

	echo '
		<table width="98%">
			<tr>
				<td class="azul_16" align="center" height="30" valign="bottom">PROMEDIO DE TIEMPOS POR RUTA</td>
			</tr>
			<tr>
				<td class="azul_16" align="center" height="40" valign="center">'.$frm["inicio"]." / ".$frm["fin"].'</td>
			</tr>
		</table>
		<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla">
			<tr>
				<th height="40">RUTA</th>
				<th>TIEMPO TOTAL</th>';

	$tipos = array();
	$qid = $db->sql_query("SELECT * FROM rec.tipos_desplazamientos ORDER BY orden");
	while($query = $db->sql_fetchrow($qid))
	{
		$tipos[$query["id"]] = $query["tipo"];
		echo "<th>".strtoupper($query["tipo"])."</th>";
	}

	echo "<th>TONELADAS</th>
				<th>VIAJES</th>
				<th>TONS/VIAJE</th>
		</tr>";

	$micros = $codigos = array();
	$qid = $db->sql_query("SELECT m.id, m.codigo, s.esquema
			FROM micros m
			LEFT JOIN servicios s ON s.id=m.id_servicio
			WHERE esquema != 'bar' 
			ORDER BY m.codigo");
	while($mc = $db->sql_fetchrow($qid))
	{
		$codigos[$mc["id"]] = $mc["codigo"];
		if($mc["esquema"] == "rec")
		{
			$qidMov = $db->sql_query("SELECT id, peso_final-peso_inicial as peso, id_micro
					FROM rec.movimientos
					WHERE id_micro='".$mc["id"]."' AND inicio>='".$frm["inicio"]."' AND final <= '".$frm["fin"]."'");
			while($mov = $db->sql_fetchrow($qidMov))
			{
				$micros[$mc["id"]]["peso"]=$mov["peso"];
				$ttotal = $db->sql_row("SELECT max(hora_fin) - min(hora_inicio) as hora, max(numero_viaje) as numviaje FROM rec.desplazamientos WHERE id_movimiento=".$mov["id"]);
				if(!isset($micros[$mc["id"]]["total"]))
					$micros[$mc["id"]]["total"]="00:00:00";
				$micros[$mc["id"]]["total"]=SumaHoras(nvl($ttotal["hora"]), $micros[$mc["id"]]["total"]);
				if(!isset($micros[$mc["id"]]["numViajes"]))
					$micros[$mc["id"]]["numviaje"]=0;
				$micros[$mc["id"]]["numviaje"]+=nvl($ttotal["numviaje"]);

				foreach($tipos as $idTipo => $val)
				{
					$qidDes = $db->sql_query("SELECT hora_fin-hora_inicio as hora, id_tipo_desplazamiento FROM rec.desplazamientos WHERE id_movimiento=".$mov["id"]." AND id_tipo_desplazamiento=".$idTipo);
					while($des = $db->sql_fetchrow($qidDes))
					{
						if(!isset($micros[$mc["id"]][$idTipo]))
							$micros[$mc["id"]][$idTipo]="00:00:00";
						$micros[$mc["id"]][$idTipo]=SumaHoras($micros[$mc["id"]][$idTipo],$des["hora"]);
					}
				}
			}
		}



//		preguntar($mc);
	}

	foreach($micros as $idMicro => $dx)
	{
		$linea = array($codigos[$idMicro], $dx["total"]);
		foreach($tipos as $idTipo => $val)
		{
			$linea[] = nvl($dx[$idTipo],"");
		}
		$linea[] = number_format($dx["peso"], 2, ",", ".");
		$linea[] = $dx["numviaje"];
		$linea[] = number_format($dx["peso"]/$dx["numviaje"], 2, ",", ".");
		imprimirLinea($linea);	
	}

	echo "</table>";
}


function tiemposxmovimiento($frm)
{
	global $db, $CFG, $ME;

	$tipos = array();
	$qid = $db->sql_query("SELECT * FROM rec.tipos_desplazamientos ORDER BY orden");
	while($query = $db->sql_fetchrow($qid))
	{
		$tipos[$query["id"]] = $query["tipo"];
	}
	$cols = count($tipos)+1;
	echo '
		<table width="98%">
			<tr>
				<td class="azul_16" align="center" height="30" valign="bottom">PROMEDIO DE TIEMPOS POR MOVIMIENTO</td>
			</tr>
			<tr>
				<td class="azul_16" align="center" height="40" valign="center">'.$frm["inicio"]." / ".$frm["fin"].'</td>
			</tr>
		</table>
		<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla">
			<tr>
				<th rowspan=2>FECHA</th>
				<th rowspan=2>RUTA</th>
				<th rowspan=2>VIAJE</th>
				<th rowspan=2>TONS</th>
				<th colspan='.$cols.'>TIEMPOS</th>
				<th colspan=6>HORAS</th>
				<th rowspan=2>MIN/TON</th>
			</tr>
			<tr>';
	foreach($tipos as $dx)
	{
		echo "<th>".strtoupper($dx)."</th>";
	}
	echo "<th>TOTAL</th>
				<th>SALIDA<br>BASE</th>
				<th>INICIO<br>MICRO</th>
				<th>FIN<br>MICRO</th>
				<th>ENTRADA<br>RELLENO</th>
				<th>SALIDA<br>RELLENO</th>
				<th>REGRESO<br>BASE</th>
		</tr>";

	$tabla = array();
	$qid = $db->sql_query("SELECT m.id, m.peso_final-peso_inicial as peso, to_char(m.inicio,'YYYY:MM:DD') as inicio, i.codigo
					FROM rec.movimientos m
					LEFT JOIN micros i ON i.id=m.id_micro
					LEFT JOIN servicios s ON s.id=i.id_servicio
					WHERE esquema != 'bar' AND inicio>='".$frm["inicio"]."' AND final <= '".$frm["fin"]."'
					ORDER BY inicio");
	while($mov = $db->sql_fetchrow($qid))
	{
		$viajes = array();
		$qidV = $db->sql_query("SELECT distinct(numero_viaje) as numviaje  FROM rec.desplazamientos WHERE id_movimiento=".$mov["id"]);
		while($queryViajes = $db->sql_fetchrow($qidV))
		{
			$viajes[] = $queryViajes["numviaje"];
		}
		foreach($viajes as $vj)
		{
			if($vj == 1)
				$linea = array($mov["inicio"], $mov["codigo"],$vj, $mov["peso"]);
			else
				$linea = array($mov["inicio"], $mov["codigo"],$vj,"");

			$totalTiempos = "00:00:00";
			foreach($tipos as $idTipo => $dx)
			{
				$des = $db->sql_row("SELECT sum(hora_fin-hora_inicio) as hora FROM rec.desplazamientos WHERE numero_viaje='".$vj."' AND id_movimiento=".$mov["id"]." AND id_tipo_desplazamiento='".$idTipo."'");
				$linea[] = nvl(formatearHora($des["hora"],""));
				if($des["hora"] != "")
					$totalTiempos = SumaHoras($totalTiempos,$des["hora"]);
			}
			$linea[] = formatearHora($totalTiempos);
			$salida =  $db->sql_row("SELECT to_char(hora_fin,'HH24:MI') as fin, to_char(hora_inicio,'HH24:MI') as inicio FROM rec.desplazamientos WHERE numero_viaje='".$vj."' AND id_movimiento=".$mov["id"]." AND id_tipo_desplazamiento='1'");
			$linea[] = $salida["inicio"];
			$micro =  $db->sql_row("SELECT to_char(hora_fin,'HH24:MI') as fin, to_char(hora_inicio,'HH24:MI') as inicio FROM rec.desplazamientos WHERE numero_viaje='".$vj."' AND id_movimiento=".$mov["id"]." AND id_tipo_desplazamiento='3'");
			$linea[] = $micro["inicio"];
			$linea[] = $micro["fin"];
			$relleno =  $db->sql_row("SELECT to_char(hora_fin,'HH24:MI') as fin, to_char(hora_inicio,'HH24:MI') as inicio FROM rec.desplazamientos WHERE numero_viaje='".$vj."' AND id_movimiento=".$mov["id"]." AND id_tipo_desplazamiento='6'");
			$linea[] = nvl($relleno["inicio"]);
			$linea[] = nvl($relleno["fin"]);
			$regreso =  $db->sql_row("SELECT to_char(hora_fin,'HH24:MI') as fin, to_char(hora_inicio,'HH24:MI') as inicio FROM rec.desplazamientos WHERE numero_viaje='".$vj."' AND id_movimiento=".$mov["id"]." AND id_tipo_desplazamiento='5'");
			$linea[] = $regreso["fin"];


	
		
		
			imprimirLinea($linea);
		}	
	}
	echo "</table>";
}

function sobrepesos($frm)
{
	global $db, $CFG, $ME;

	$stilos=array(2=>"align='left'", 3=>"align='left'", 4=>"align='left'", 5=>"align='left'");	

	echo '
		<table width="98%">
			<tr>
				<td class="azul_16" align="center" height="30" valign="bottom">SOBREPESOS</td>
			</tr>
			<tr>
				<td class="azul_16" align="center" height="40" valign="center">'.$frm["inicio"]." / ".$frm["fin"].'</td>
			</tr>
		</table>
		<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla">
			<tr>
				<th height="40">VEHÍCULO</th>
				<th>DESCRIPCIÓN</th>
				<th>MOV</th>	
				<th>RUTA</th>	
				<th>VIAJES</th>	
				<th>PENTRE</th>	
				<th>PESALRE</th>	
				<th>PNETO</th>	
				<th>CAPACIDAD</th>
				<th>SPESO</th>	
			</tr>';
	
	$qid = $db->sql_query("SELECT v.codigo as vehi, t.tipo, to_char(m.inicio,'YYYY-MM-DD') as fechamov, i.codigo as ruta, m.peso_inicial, m.peso_final, m.peso_final-peso_inicial as neto, t.capacidad, case when (m.peso_final-peso_inicial) > t.capacidad then (m.peso_final-peso_inicial)-t.capacidad else 0 end as sobrepeso, (SELECT max(numero_viaje) FROM rec.desplazamientos WHERE id_movimiento=m.id) as viajes
					FROM vehiculos v 
					LEFT JOIN rec.movimientos m ON v.id=m.id_vehiculo
					LEFT JOIN micros i ON i.id=m.id_micro
					LEFT JOIN servicios s ON s.id=i.id_servicio
					LEFT JOIN tipos_vehiculos t ON t.id=v.id_tipo_vehiculo
					WHERE esquema != 'bar' AND inicio>='".$frm["inicio"]."' AND final <= '".$frm["fin"]."'
					ORDER BY inicio");
	while($mov = $db->sql_fetchrow($qid))
	{
		$linea = array($mov["vehi"], $mov["tipo"], $mov["fechamov"], $mov["ruta"], $mov["viajes"], number_format($mov["peso_inicial"], 2, ",", "."), number_format($mov["peso_final"], 2, ",", "."), number_format($mov["neto"], 2, ",", "."), number_format($mov["capacidad"], 2, ",", "."), number_format($mov["sobrepeso"], 2, ",", "."));
		imprimirLinea($linea,'', $stilos);
	}

	echo "</table>";
}

function consumocombustible($frm)
{
	global $db, $CFG, $ME;

	$stilos=array(2=>"align='left'", 3=>"align='left'", 4=>"align='left'");	

	echo '
		<table width="98%">
			<tr>
				<td class="azul_16" align="center" height="30" valign="bottom">SOBREPESOS</td>
			</tr>
			<tr>
				<td class="azul_16" align="center" height="40" valign="center">'.$frm["inicio"]." / ".$frm["fin"].'</td>
			</tr>
		</table>
		<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla">
			<tr>
				<th height="40">VEHÍCULO</th>
				<th>MOV</th>	
				<th>RUTA</th>	
				<th>VIAJES</th>	
				<th>KM SALIDA</th>	
				<th>KM REGRESO</th>	
				<th>KM RECORRIDO</th>	
				<th>PESO ENTRADA</th>
				<th>PESO SALIDA</th>	
				<th>PESO NETO</th>
				<th>#TIQUETE RELLENO</th>
				<th>COMBUSTIBLE</th>
			</tr>';
	$qid = $db->sql_query("SELECT v.codigo as vehi, to_char(m.inicio,'YYYY-MM-DD') as fechamov, i.codigo as ruta, (SELECT max(numero_viaje) FROM rec.desplazamientos WHERE id_movimiento=m.id) as viajes, (SELECT min(km) FROM rec.desplazamientos WHERE id_movimiento=m.id) AS kmsalida, km_final as kmregreso, m.peso_inicial as pesoentrada, m.peso_final as pesosalida, m.peso_final-peso_inicial as pesoneto
					FROM vehiculos v 
					LEFT JOIN rec.movimientos m ON v.id=m.id_vehiculo
					LEFT JOIN micros i ON i.id=m.id_micro
					LEFT JOIN servicios s ON s.id=i.id_servicio
					WHERE esquema != 'bar' AND inicio>='".$frm["inicio"]."' AND final <= '".$frm["fin"]."'
					ORDER BY inicio");
	while($mov = $db->sql_fetchrow($qid))
	{
		$recor = $mov["kmregreso"] - $mov["kmsalida"];
		$linea = array($mov["vehi"], $mov["fechamov"], $mov["ruta"], $mov["viajes"], $mov["kmsalida"], $mov["kmregreso"], $recor, $mov["pesoentrada"], $mov["pesosalida"], $mov["pesoneto"],"", "");
		imprimirLinea($linea,'', $stilos);
	}

	echo "</table>";
}




function barridoxcoordinador($frm)
{
	global $db, $CFG;

	$idsCoor = array(0);
	if(isset($frm["id_coordinador"]))
		$idsCoor = array_merge($idsCoor, $frm["id_coordinador"]);

	$stilos=array(1=>"align='center'", 2=>"align='center'", 3=>"align='center'");	
	$stilos2=array(1=>"align='center' colspan=3");	
	
	$coord = $micros = array();
	$qid = $db->sql_query("SELECT to_char(m.inicio,'YYYY-MM-DD') as inicio, i.km, i.id_coordinador, p.nombre ||' '||p.apellido as coord, i.id as id_micro
			FROM bar.movimientos m
			LEFT JOIN micros i ON i.id=m.id_micro
			LEFT JOIN personas p ON p.id=i.id_coordinador
			WHERE inicio>='".$frm["inicio"]."' AND final <= '".$frm["fin"]."' AND i.id_coordinador IN (".implode(",",$idsCoor).")
			ORDER BY inicio");	
		while($mov = $db->sql_fetchrow($qid))
		{
			$sem = strftime("%V",strtotime($mov["inicio"]));
			$coord[$mov["id_coordinador"]]=$mov["coord"];
			if(!isset($micros[$sem][$mov["inicio"]][$mov["id_coordinador"]]))
				$micros[$sem][$mov["inicio"]][$mov["id_coordinador"]] = 0;

			$micros[$sem][$mov["inicio"]][$mov["id_coordinador"]]+= $mov["km"];
		}
	
	echo '
		<table width="98%">
			<tr>
				<td class="azul_16" align="center" height="30" valign="bottom">BARRIDO: LONGITUDES POR COORDINADOR</td>
			</tr>
			<tr>
				<td class="azul_16" align="center" height="40" valign="center">'.$frm["inicio"]." / ".$frm["fin"].'</td>
			</tr>
		</table>
		<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla">
			<tr>
				<th height="40">SEMANA</th>
				<th>FECHA</th>	
				<th>DÍA</th>';
	foreach($coord as $dx)
	{
		echo '<th>Metros '.strtoupper($dx).'</th>';
	}
	echo '<th>TOTALES</th>
			</tr>';

	foreach($micros as $sem => $dxSemana)
	{
		$totSem = array();
		foreach($dxSemana as $fecha => $dx)
		{
			$totalFecha = 0;
			$dia = ucfirst(strftime("%A",strtotime($fecha)));
			$linea = array($sem, $fecha, $dia);		
			foreach($coord as $idCoord => $dxCoord)
			{
				if(isset($dx[$idCoord]))	
				{
					$linea[] = $dx[$idCoord];
					$totalFecha+=$dx[$idCoord];
					if(!isset($totalSem[$idCoord]))
						$totalSem[$idCoord]=0;
					$totalSem[$idCoord]+=$dx[$idCoord];

				}
				else
					$linea[] = "";
			}
			$linea[] = $totalFecha;
			imprimirLinea($linea, "", $stilos);
		}
		$total = 0;
		$linea = array("Total Semana ".$sem);
		foreach($coord as $idCoord => $dxCoord)
		{
			if(isset($totalSem[$idCoord]))
			{
				$linea[] = $totalSem[$idCoord];	
				$total+=$totalSem[$idCoord];
			}
			else
				$linea[] = "";
		}
		$linea[] = $total;
		imprimirLinea($linea, "#b2d2e1", $stilos2);
	}
	echo "</table>";
}

/*
function consumosdiarios($frm)
{
	global $db, $CFG, $ME;

	$stilos=array(2=>"align='center'", 3=>"align='left'", 4=>"align='center'");	
	$qid = $db->sql_query("SELECT * FROM bar.tipos_bolsas");

	echo '
		<table width="98%">
			<tr>
				<td class="azul_16" align="center" height="30" valign="bottom">CONSUMOS DIARIOS</td>
			</tr>
			<tr>
				<td class="azul_16" align="center" height="40" valign="center">'.$frm["inicio"]." / ".$frm["fin"].'</td>
			</tr>
		</table>
		<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla">
			<tr>
				<th height="40" rowspan=2>FECHA</th>
				<th rowspan=2>IDENTIFICACIÓN</th>	
				<th rowspan=2>NOMBRES</th>
				<th rowspan=2>RUTA</th>
				<th rowspan=2>METROS</th>
				<th rowspan=2>HORA INICIO</th>
				<th colspan='.$db->sql_numrows($qid).'>BOLSAS</th>
			</tr>
			<tr>';

	while($query = $db->sql_fetchrow($qid))
	{
		echo "<th>".strtoupper($query["tipo"])."</th>";
		$bol[$query["id"]]= $query["tipo"];
	}
	echo "</tr>";

	$qid = $db->sql_query("SELECT mov.id, to_char(mov.inicio,'YYYY-MM-DD') as fecha, to_char(mov.inicio,'HH24:MI:SS') as hora, i.codigo as ruta, i.km
		FROM bar.movimientos mov
		LEFT JOIN micros i ON i.id=mov.id_micro
		WHERE inicio>='".$frm["inicio"]."' AND final <= '".$frm["fin"]."'
		ORDER BY mov.inicio");
	while($query = $db->sql_fetchrow($qid))
	{
		$personas = $ident = $bolsas = array();
		$qidPer = $db->sql_query("SELECT cedula, p.nombre||' '||p.apellido as nombre 
				FROM bar.movimientos_personas mp 
				LEFT JOIN personas p ON p.id=mp.id_persona 
				WHERE mp.id_movimiento=".$query["id"]);
		while($per = $db->sql_fetchrow($qidPer))
		{
			$personas[] = $per["nombre"];
			$ident[] = $per["cedula"];
		}
		$qidBolsas = $db->sql_query("SELECT numero_fin-numero_inicio as num, id_tipo_bolsa FROM bar.movimientos_bolsas WHERE  id_movimiento=".$query["id"]);
		while($queryBol=$db->sql_fetchrow($qidBolsas))
		{
			if(!isset($bolsas[$queryBol["id_tipo_bolsa"]])) $bolsas[$queryBol["id_tipo_bolsa"]] = 0;
			$bolsas[$queryBol["id_tipo_bolsa"]]+=$queryBol["num"];
		}

		$linea = array($query["fecha"], implode(", ",$ident), implode(", ",$personas), $query["ruta"], $query["km"]*1000, $query["hora"]);	
		foreach($bol as $idBolsa => $dx)
		{
			if(isset($bolsas[$idBolsa])) $linea[] = $bolsas[$idBolsa];
			else $linea[] = "";
		}
				
		imprimirLinea($linea, "", $stilos);
	}

	echo "</table>";
}
*/

function indicador_factorcarga($frm)
{
	global $db, $CFG, $ME;

	$semanas = dividirEnSemanas($frm["inicio"], $frm["fin"]);
	$stilos=array(5=>"align='center'", 6=>"align='center'");	

	echo '
		<table width="98%">
			<tr>
				<td class="azul_16" align="center" height="30" valign="bottom">INDICADOR FACTOR DE CARGA</td>
			</tr>
			<tr>
				<td class="azul_16" align="center" height="40" valign="center">'.$frm["inicio"]." / ".$frm["fin"].'</td>
			</tr>
		</table>
		<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla">
			<tr>
				<th height="40">PERÍODO</th>
				<th>TONOP</th>	
				<th>TONCAP</th>	
				<th>SOBREPESO</th>	
				<th>INDICADOR FACTOR<BR>CARGA</th>	
				<th>INDICADOR<BR>SOBREPESO</th>	
			</tr>';

	foreach($semanas as $fechas)
	{
		$linea = array($fechas["Monday"]." / ".$fechas["Sunday"]);
		$qid = $db->sql_query("SELECT sum(m.peso_final)-sum(m.peso_inicial) as tonop, sum(m.peso_inicial) as inicial, sum(m.peso_final) as final, sum(t.capacidad) as capacidad 
				FROM rec.movimientos m
				LEFT JOIN vehiculos v ON v.id=m.id_vehiculo
				LEFT JOIN tipos_vehiculos t ON t.id=v.id_tipo_vehiculo
				WHERE inicio>='".$fechas["Monday"]."' AND final <= '".$fechas["Sunday"]."'");
		while($mov = $db->sql_fetchrow($qid))
		{
			$linea[] = $mov["tonop"];
			$linea[] = $mov["capacidad"];
			$linea[] = "";
			if($mov["capacidad"] != "")
				$linea[]= number_format(($mov["tonop"]*100)/$mov["capacidad"], 2, ",", ".") ."%";
			else
				$linea[] = "";
			$linea[] = "";
			imprimirLinea($linea,'', $stilos);
		}
	}


	echo "</table>";
}


function indicador_factorutilizacion($frm)
{
	global $db, $CFG, $ME;

	$stilos=array(5=>"align='center'", 6=>"align='center'");	
	$semanas = dividirEnSemanas($frm["inicio"], $frm["fin"]);

	echo '
		<table width="98%">
			<tr>
				<td class="azul_16" align="center" height="30" valign="bottom">INDICADOR FACTOR DE UTILIZACIÓN</td>
			</tr>
			<tr>
				<td class="azul_16" align="center" height="40" valign="center">'.$frm["inicio"]." / ".$frm["fin"].'</td>
			</tr>
		</table>
		<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla">
			<tr>
				<th height="40">PERÍODO</th>
				<th>TIEMPO OPERACIÓN</th>	
				<th>TIEMPO TEÓRICO</th>	
				<th>KMS OPERACION</th>	
				<th>KMS TERÓRICO</th>	
				<th>INDICADOR FUT</th>	
				<th>INDICADOR FUK</th>	
			</tr>';
	foreach($semanas as $fechas)
	{
		$sec = "0";
		
		$cons = "SELECT extract('epoch' from sum(d.hora_fin-d.hora_inicio)) as sec ,  sum(d.hora_fin-d.hora_inicio) as ho 
			FROM rec.desplazamientos d
			LEFT JOIN rec.movimientos m ON m.id=d.id_movimiento
			WHERE inicio>='".$fechas["Monday"]."' AND final <= '".$fechas["Sunday"]."'";
		
		/*
		$cons = "SELECT extract(days from d.hora_fin-d.hora_inicio) as sec
			FROM rec.desplazamientos d
			LEFT JOIN rec.movimientos m ON m.id=d.id_movimiento
			WHERE inicio>='".$fechas["Monday"]."' AND final <= '".$fechas["Sunday"]."'";
		*/
		echo $cons;
		$qid = $db->sql_query($cons);
		while($mov = $db->sql_fetchrow($qid))
		{
			$sec+=$mov["sec"];
			preguntar($mov);
		
		}

		preguntar("sec ".$sec);
		conversor_segundos($sec);

	}

	echo "</table>";
}



function imprimirLinea($datos,$bgColor="#ffffff", $stilos=array())
{
	echo "<tr>\n";
	$i=1;
	foreach($datos as $val)
	{
		$extra = "";
		$align="align='right'";
		if(isset($stilos[$i])) {
			$extra = $stilos[$i];
			if(preg_match("/align/",$extra,$match))
				$align="";
		}

		if($i==1)
			if(!isset($stilos[1]))
				$align="align='left'";


		echo "<td ".$align." bgcolor='".$bgColor."' ".$extra.">".$val."</td>\n";
		$i++;
	}
	
	echo "</tr>\n";
}


?>

</div>
<?include($CFG->dirroot."/templates/footer_2panel.php");?>
