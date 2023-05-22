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

if(isset($_GET["format"])) 
{
	$html=false;
	$inicio = $_GET["inicio"];
	$final = $_GET["final"];
}


$horariosTurnos = array();
$qidTur = $db->sql_query("SELECT * FROM turnos ORDER BY hora_inicio");
while($queryTur = $db->sql_fetchrow($qidTur))
{
	$horariosTurnos[$queryTur["id_empresa"]][$queryTur["id"]] = array("hora_inicio"=>$queryTur["hora_inicio"], "turno"=>$queryTur["turno"]);
}

$titulo1 = $db->sql_row("SELECT upper(nombre||' : '||informe) as inf FROM informes i LEFT JOIN categorias_informes c ON c.id=i.id_categoria_informe WHERE i.id=".str_replace(".php","",simple_me($ME)));

if($html)
{
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/info/templates/fechas_form.php");

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


$cond = "";

if($id_ase != "") 
	$cond.= " AND a.id='".$id_ase."'";
else
	$cond.=" AND a.id_centro = '".$centro."'";

if($dia != "")
	$cond.=" AND to_char(p.fecha_entrada, 'D') = '".$dia."'";

$tipos = array();
$qidVe = $db->sql_query("SELECT distinct(id_tipo_vehiculo), tv.tipo, tv.capacidad , to_char(p.fecha_entrada,'HH24:MI:SS') as hora_entrada , c.id_empresa
		FROM vehiculos v
	 	LEFT JOIN tipos_vehiculos tv ON tv.id=v.id_tipo_vehiculo	
		LEFT JOIN rec.pesos p ON p.id_vehiculo=v.id
		LEFT JOIN ases a ON a.id_centro=v.id_centro
		LEFT JOIN centros c ON c.id=v.id_centro
		WHERE p.fecha_entrada::date >='".$inicio."' AND p.fecha_entrada::date<='".$final."' $cond
		ORDER BY tipo");
while($veh = $db->sql_fetchrow($qidVe))
{
	if($id_turno != "")
	{
		if(estaEnTurno($id_turno, $veh["hora_entrada"], $horariosTurnos[$veh["id_empresa"]] ))
			$tipos[$veh["id_tipo_vehiculo"]] = $veh;
	}
	else
		$tipos[$veh["id_tipo_vehiculo"]] = $veh;
}

$vehiculos = $dxTipos = array();
foreach($tipos as $idTipo => $dx)
{
	$tons = $viajes = 0;
	$qid = $db->sql_query("SELECT p.*, v.codigo||'/'||v.placa as codigo, v.id as id_vehiculo, v.codigo as solo_codigo, c.id_empresa, to_char(p.fecha_entrada,'HH24:MI:SS') as hora_entrada 
		FROM rec.pesos p 
		LEFT JOIN vehiculos v ON v.id=p.id_vehiculo 
		LEFT JOIN ases a ON a.id_centro=v.id_centro
		LEFT JOIN centros c ON c.id=v.id_centro
		WHERE v.id_tipo_vehiculo= '".$idTipo."' AND p.fecha_entrada::date>='".$inicio."' and p.fecha_entrada<='".$final."' $cond
		ORDER BY v.codigo, v.placa");
	while($query = $db->sql_fetchrow($qid))
	{
		$entra = true;
		if($id_turno != "")
			if(!estaEnTurno($id_turno, $query["hora_entrada"], $horariosTurnos[$query["id_empresa"]] ))
				$entra = false;
		
		if($entra)
		{
			if(!isset($vehiculos[$idTipo][$query["id_vehiculo"]])) $vehiculos[$idTipo][$query["id_vehiculo"]] = array("tons"=>0, "viajes"=>0, "turnos"=>array());
					
			$qidPer = $db->sql_query("SELECT mp.porcentaje, m.id_vehiculo 
				FROM rec.movimientos_pesos mp 
				left join rec.movimientos m on m.id=mp.id_movimiento 
				WHERE mp.id_peso=".$query["id"]);
			while($per = $db->sql_fetchrow($qidPer))
			{
				$pesoNeto = 0;
				if($query["peso_inicial"] != "" && $query["peso_final"] != "") $pesoNeto = abs($query["peso_inicial"]-$query["peso_final"]);
				elseif($query["peso_total"] != "") $pesoNeto = $query["peso_total"];

				$val = ($pesoNeto*$per["porcentaje"])/100;;
				$vehiculos[$idTipo][$query["id_vehiculo"]]["tons"] += $val;
				$tons+=$val;
			}

			$viajes+=1;
			$vehiculos[$idTipo][$query["id_vehiculo"]]["viajes"] += 1;
			$vehiculos[$idTipo][$query["id_vehiculo"]]["capacidad"] = $dx["capacidad"];
			$vehiculos[$idTipo][$query["id_vehiculo"]]["codigo"] = $query["codigo"];
			$vehiculos[$idTipo][$query["id_vehiculo"]]["solo_codigo"] = $query["solo_codigo"];
			$vehiculos[$idTipo][$query["id_vehiculo"]]["id_empresa"] = $query["id_empresa"];
			viajesXTurno($vehiculos, $idTipo, $query["id_vehiculo"], $query["hora_entrada"], $horariosTurnos[$query["id_empresa"]]);
		}
	}

	@$tonxViaje = $tons/$viajes;
	@$ind = ($tonxViaje/$dx["capacidad"]) * 100;
	$dxTipos[$idTipo] = array("tons"=>$tons, "viajes"=>$viajes, "tonxviaje"=> $tonxViaje, "ind"=>$ind, "turnos"=>"");
}

//cuadrar la linea de viajesxturno
$convenciones = array();
foreach($tipos as $idTipo => $valsTipo)
{
	$turnos = array();
	foreach($vehiculos[$idTipo] as $idVehiculo => $dx)
	{
		$txTurno = array();
		foreach($horariosTurnos[$dx["id_empresa"]] as $key => $dxTurnos )
		{
			if(isset($dx["turnos"][$key]))
			{
				$txTurno[] = substr($dxTurnos["turno"],0,1) .":".$dx["turnos"][$key];
				if(!isset($turnos[$key])) $turnos[$key]= array("cv"=>"", "val"=>0);
				$turnos[$key]["val"]+=$dx["turnos"][$key];
				$turnos[$key]["cv"]=substr($dxTurnos["turno"],0,1);
			}
			$convenciones[$key] = substr($dxTurnos["turno"],0,1).": ".$dxTurnos["turno"];
		}
		$vehiculos[$idTipo][$idVehiculo]["turnos"] = implode(" / ",$txTurno);
	}
	$txTurno = array();
	foreach($turnos as $key => $dx)
	{
		$txTurno[] = $dx["cv"].":".$dx["val"];
	}
	$dxTipos[$idTipo]["turnos"] =  implode(" / ",$txTurno);
}


//cuadro uno
$titulos = array("TIPO VEHÍCULO", " TONELADAS TRANSPORTADAS", " VIAJES REALIZADOS","VIAJES REALIZADOS X TURNO ", "PROMEDIO TONELADAS POR VIAJE", " CAPACIDAD DE CARGA (tons)", "EFICIENCIA DE CARGA");
if($html)
{
	tablita_titulos($titulo1["inf"],$inicio." / ".$final);
	echo '<table width="98%" align="center">
		<tr>
			<td height="40" valign="center" class="azul_11">'.implode("&nbsp;&nbsp; / &nbsp;&nbsp;",$convenciones).'</td>
		</tr>
		</table>';

	echo '<table width="98%"><tr>
		<td valign="top" align="left" width="48%">
						<table width="100%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
							<tr>';
	foreach($titulos as $tt)
		echo '<th height="40">'.$tt.'</th>';
	echo "</tr>";
}
foreach($tipos as $idTipo => $valsTipo)
{
	$linea = array($valsTipo["tipo"], number_format($dxTipos[$idTipo]["tons"], 2, ",", "."), $dxTipos[$idTipo]["viajes"], $dxTipos[$idTipo]["turnos"] , number_format($dxTipos[$idTipo]["tonxviaje"], 2, ",", "."), $valsTipo["capacidad"], number_format($dxTipos[$idTipo]["ind"], 2, ",", ".")."%");
	if($html)
		imprimirLinea($linea,"",array(1=>"align='left'", 4=>"align='left'"));
}

//<td>'..'</td>

if($html)
{
	echo '</table>
	</td>
	<td width="4%"></td>';
}

//cuadro dos
if($html)
{
	echo '<td valign="top" align="right" width="48">
			<table width="100%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
				<tr>';
	foreach($titulos as $tt)
		echo '<th height="40">'.$tt.'</th>';
	echo "</tr>";
}else
{
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, $titulos);
	$fila++;$columna=0;
}

foreach($tipos as $idTipo => $valsTipo)
{
	$linea = array($valsTipo["tipo"], number_format($dxTipos[$idTipo]["tons"], 2, ",", "."), $dxTipos[$idTipo]["viajes"], $dxTipos[$idTipo]["turnos"], number_format($dxTipos[$idTipo]["tonxviaje"], 2, ",", "."), $valsTipo["capacidad"], number_format($dxTipos[$idTipo]["ind"], 2, ",", ".")."%");

	if($html)
		imprimirLinea($linea, "#b2d2e1", array(1=>"align='left'", 4=>"align='left'"));
	else
	{	
		imprimirLineaAzul_xls($workbook, $worksheet, $fila, $columna, $linea,  array(1=>"azul_izq", 4=>"azul_izq"));
		$fila++;$columna=0;		
	}
		
	foreach($vehiculos[$idTipo] as $dx)
	{
		@$tonxViajeVehiculo = $dx["tons"]/$dx["viajes"];
		@$indVehiculo = ($tonxViajeVehiculo/$dx["capacidad"])*100;
		$linea = array($dx["codigo"], number_format($dx["tons"], 2, ",", "."), $dx["viajes"], $dx["turnos"], number_format($tonxViajeVehiculo, 2, ",", "."), $dx["capacidad"], number_format($indVehiculo, 2, ",", ".")."%");
		
		if($html)
			imprimirLinea($linea,"",array(1=>"align='left'", 4=>"align='left'"));
		else
			imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_izq", 4=>"azul_izq"));
	}
}

if($html)
	echo "</table></td></tr></table>";

if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro."&id_turno=".$id_turno."&id_ase=".$id_ase."&dia=".$dia;
		echo "<table width='98%' align='center'>";
	$i=$j=0;
	foreach($tipos as $idTipo => $valsTipo)
	{
		$dxGraf = array();
		foreach($vehiculos[$idTipo] as $dx)
		{
			@$tonxViajeVehiculo = $dx["tons"]/$dx["viajes"];
			@$ind = ($tonxViajeVehiculo/$dx["capacidad"])*100;
			$dxGraf["data"][] =  $ind;
			$dxGraf["labels"][] = $dx["solo_codigo"];
		}

		if($i==0) echo "</tr>";

		echo "<td>";
		graficaBarras_21_23($dxGraf,$valsTipo["tipo"], "PROMEDIO/CAPACIDAD (tons)", $inicio, $final, $centro, $id_turno);
		echo "</td>";

		$i++;$j++;
		if($i==2 || $j==count($tipos))
		{
			echo "</tr>";
			$i=0;
		}
	}
	echo "</table>";
	echo "<table width='98%' align='center'>
		<tr>
			<td height='50' valign='bottom' align='right'><input type='button' class='boton_verde' value='Bajar en xls' onclick=\"window.location.href='".$ME.$link."'\"/></td>
		</tr>
	</table>";
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


function viajesXTurno(&$vehiculos, $idTipo, $id_vehiculo, $hora, $turnos)
{
	$i=1;$j=count($turnos);
	$idTurno="";
	foreach($turnos as $key => $dx)
	{
		if($hora >= $dx["hora_inicio"])
			$idTurno = $key;
		
		if($i==$j)
			$ultTurno = $key;

		$i++;
	}

	if($idTurno == "")
		$idTurno = $ultTurno;

	if(!isset($vehiculos[$idTipo][$id_vehiculo]["turnos"][$idTurno])) $vehiculos[$idTipo][$id_vehiculo]["turnos"][$idTurno] = 0;
	$vehiculos[$idTipo][$id_vehiculo]["turnos"][$idTurno]+=1;
}

function estaEnTurno($id_turno_escogido, $hora, $turnos)
{
	$idTurno="";
	$i=1;$j=count($turnos);
	foreach($turnos as $key => $dx)
	{
		if($hora >= $dx["hora_inicio"])
			$idTurno = $key;
		
		if($i==$j)
			$ultTurno = $key;

		$i++;
	}

	if($idTurno == "")
		$idTurno = $ultTurno;

	if($idTurno == $id_turno_escogido)
		return true;

	return false;
}

?>
