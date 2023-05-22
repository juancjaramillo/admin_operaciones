<?
include("../application.php");
require_once($CFG->libdir."/ChartDirector/lib/phpchartdir.php");

$user=$_SESSION[$CFG->sesion]["user"];

$titulo1 = $db->sql_row("SELECT upper(nombre||' : '||informe) as inf FROM informes i LEFT JOIN categorias_informes c ON c.id=i.id_categoria_informe WHERE i.id=".str_replace(".php","",simple_me($ME)));

$semana = array("1"=>"Lunes", "2"=>"Martes","3"=>"Miércoles","4"=>"Jueves","5"=>"Viernes","6"=>"Sábado","7"=>"Domingo");
$dia = nvl($_POST["dia"],nvl($_GET["dia"], strftime("%u",strtotime(date("Y-m-d")))));

$fechasSemana = obtenerSemanaCompleta(date("Y-m-d"));

$prc = $db->sql_row("SELECT id FROM centros WHERE id IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]') ORDER BY centro LIMIT 1");
$vista = nvl($_POST["vista"], nvl($_GET["vista"], $prc["id"]));

$id_ase = "";
if(isset($_POST["id_ase"]))
	$id_ase = $_POST["id_ase"];
elseif(isset($_GET["id_ase"]))
	$id_ase = $_GET["id_ase"];

include($CFG->dirroot."/templates/header_popup.php");
include($CFG->dirroot."/info/templates/dias_form.php");
tablita_titulos($titulo1["inf"],"Día : ". $semana[$dia],"tablagrafica");
echo '<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
<h1 id="tablagrafica"></h1>';

$cond = " AND a.id_centro = '".$vista."'";
if($id_ase != "")
	$cond = " AND a.id='".$id_ase."'";

$vehiculos = $codigos = array();

//planeado
$rini = $rfin = $fechasSemana[$dia]. " 08:00:00";
$consulta = "SELECT i.id_vehiculo, v.codigo, v.placa, hora_inicio, hora_fin, i.codigo as micro, id_servicio, servicio
	FROM micros_frecuencia mf
	LEFT JOIN micros i ON i.id=mf.id_micro
	LEFT JOIN ases a ON a.id = i.id_ase
	LEFT JOIN vehiculos v ON v.id=i.id_vehiculo
	LEFT JOIN servicios s ON s.id=i.id_servicio
	WHERE dia='".$dia."' AND fecha_hasta IS NULL ".$cond." AND i.id_vehiculo IS NOT NULL
	ORDER BY v.codigo, hora_inicio";
$qid = $db->sql_query($consulta);
while($mov = $db->sql_fetchrow($qid))
{
	$ini = $fechasSemana[$dia]." ".$mov["hora_inicio"];
	$fin = $fechasSemana[$dia]." ".$mov["hora_fin"];
	if($mov["hora_inicio"] > $mov["hora_fin"])
	{
		list($anio,$mes,$diaSig)=split("-",$fechasSemana[$dia]);
		$fin = date("Y-m-d",mktime(0,0,0, $mes,$diaSig,$anio) + 1 * 24 * 60 * 60)." ".$mov["hora_fin"];
	}	

	$key = $ini."-".$fin;
	$vehiculos[$mov["id_vehiculo"]][$key] = array("ini"=>$ini, "fin"=>$fin, "tipo"=>"plan");
	$codigos[$mov["id_vehiculo"]] = array("placa"=>$mov["placa"], "codigo"=>$mov["codigo"]);
}

/*
//ejecutado
$qid = $db->sql_query("SELECT m.*,  i.codigo as micro, id_servicio, v.codigo, v.placa
	FROM rec.movimientos m
	LEFT JOIN vehiculos v ON v.id=m.id_vehiculo
	LEFT JOIN micros i ON i.id=m.id_micro
	WHERE m.inicio::date='".$fechasSemana[$dia]."' AND m.final IS NOT NULL AND v.id_centro = '".$vista."'");
while($mov = $db->sql_fetchrow($qid))
{
	$ejecutado[$mov["id_vehiculo"]][] = array("micro"=>$mov["micro"], "ini"=>$mov["inicio"], "fin"=>$mov["final"], "servicio"=>101);
	$vehiculos[$mov["id_vehiculo"]] = array("placa"=>$mov["placa"], "codigo"=>$mov["codigo"]);
	$servicios[101] = array("nombre"=>"Ejecutado", "color"=>"gray(0.9)"); 
	if($mov["inicio"] < $rini)
		$rini = $mov["inicio"];
	if($mov["final"] > $rfin)
		$rfin = $mov["final"];
}
*/


//mtto
foreach($vehiculos as $idVeh => $dxVeh)
{
	$qid = $db->sql_query("SELECT o.fecha_planeada, r.tiempo_ejecucion, e.id_vehiculo
		FROM mtto.ordenes_trabajo o
		LEFT JOIN mtto.equipos e ON e.id=o.id_equipo
		LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
		WHERE e.id_vehiculo='".$idVeh."' AND o.fecha_planeada::date = '".$fechasSemana[$dia]."'");
	while($oo = $db->sql_fetchrow($qid))
	{
		list($f,$h) = split(" ",$oo["fecha_planeada"]);
		list($anioo,$meso,$diao)=split("-",$f);
		list($horao,$minutoo,$sego)=split(":",$h);
		$time = mktime($horao,$minutoo+$oo["tiempo_ejecucion"],$sego,$meso,$diao,$anioo);
		$fe = date('Y-m-d H:i:s', $time);
		
		$vehiculos[$idVeh][] = array("ini"=>$oo["fecha_planeada"], "fin"=>$fe, "tipo"=>"mtto"); 
	}
}




$i=$z=0;
$labels = $taskNo = $startDatePlan = $endDatePlan = $startDateMtto = $endDateMtto = $colorsPlan = $colorsMtto = array();
foreach($vehiculos as $idVehiculo => $dx)
{
	$labels[] = $codigos[$idVehiculo]["codigo"];
	
	for($j=0; $j<count($dx); $j++)
	{
		$taskNo[] = $i;
	}

	
	foreach($dx as $rangos)
	{
		$fechaIni = dejarUnDigitoFecha($rangos["ini"]);
		$fechaFin = dejarUnDigitoFecha($rangos["fin"]);

		
		if($rangos["tipo"] == "plan")
		{
			$startDatePlan[] = chartTime($fechaIni["anio"], $fechaIni["mes"], $fechaIni["dia"], $fechaIni["hora"], $fechaIni["minuto"], 0);
			$endDatePlan[] = chartTime($fechaFin["anio"], $fechaFin["mes"], $fechaFin["dia"], $fechaFin["hora"], $fechaFin["minuto"], 0);
			$startDateMtto[] = "";
			$endDateMtto[] = "";
		}else{
			$startDateMtto[] = chartTime($fechaIni["anio"], $fechaIni["mes"], $fechaIni["dia"], $fechaIni["hora"], $fechaIni["minuto"], 0);
			$endDateMtto[] = chartTime($fechaFin["anio"], $fechaFin["mes"], $fechaFin["dia"], $fechaFin["hora"], $fechaFin["minuto"], 0);
		
			$startDatePlan[] = "";
			$endDatePlan[] = "";
		}

		$colorsPlan[] = 0x660033;
		$colorsMtto[] = 0xFF9900;

		if($z==0)
		{
			$fechaUno = $rangos["ini"];
			$fechaDos = $rangos["fin"];
		}else
		{
			if($rangos["ini"] < $fechaUno) $fechaUno = $rangos["ini"];
			if($rangos["fin"] > $fechaDos) $fechaDos = $rangos["fin"];
		}
	}
	
	$i++;$z++;
}


# Create a XYChart object of size 620 x 325 pixels. Set background color to light red # (0xffcccc), with 1 pixel 3D border effect. 
$c = new XYChart(620, 325); 
$c->setBackground($c->linearGradientColor(0, 0, 0, 100, 0x99ccff, 0xffffff), 0x888888 );
$c->setRoundedFrame(); 
$c->setDropShadow();

$plotAreaObj = $c->setPlotArea(50, 20, 550, 265, 0xffffff, 0xeeeeee, LineColor, 0xc0c0c0, 0xc0c0c0); 

# swap the x and y axes to create a horziontal box-whisker chart 
$c->swapXY(); 

$fechaUno = dejarUnDigitoFecha($fechaUno);
$fechaDos = dejarUnDigitoFecha($fechaDos);
$c->yAxis->setDateScale(chartTime($fechaUno["anio"], $fechaUno["mes"], $fechaUno["dia"], $fechaUno["hora"], $fechaUno["minuto"], 0), chartTime($fechaDos["anio"], $fechaDos["mes"], $fechaDos["dia"], $fechaDos["hora"], $fechaDos["minuto"], 0), 3600); 

$c->yAxis->setMultiFormat(StartOfDayFilter(), "<*font=arialbd.ttf*>{value|dd}", AllPassFilter(), "{value|hh}");

# Set the y-axis to shown on the top (right + swapXY = top) 
$c->setYAxisOnRight(); 

# Set the labels on the x axis 
$c->xAxis->setLabels($labels); 
$c->xAxis->setLabelStyle("Arial", 7, TextColor);

# Reverse the x-axis scale so that it points downwards. 
$c->xAxis->setReverse(); 

# Add a multi-color box-whisker layer to represent the gantt bars 
$layerMtto = $c->addBoxWhiskerLayer2($startDateMtto, $endDateMtto, null, null, null, $colorsMtto); 
$layerMtto->setXData($taskNo); 
$layerMtto->setBorderColor(SameAsMainColor); 
$per = ((200 * 4 / 5 / count($labels)) * 50 )/100;
$layerMtto->setDataWidth((int)($per)); 

# Add a multi-color box-whisker layer to represent the gantt bars 
$layer = $c->addBoxWhiskerLayer2($startDatePlan, $endDatePlan, null, null, null, $colorsPlan); 
$layer->setXData($taskNo); 
$layer->setBorderColor(SameAsMainColor); 
# Divide the plot area height ( = 200 in this chart) by the number of tasks to get # the height of each slot. Use 80% of that as the bar height. 
$layer->setDataWidth((int)(200 * 4 / 5 / count($labels))); 

# Add a legend box at 
$legendBox = $c->addLegend2(20, 290, 0, "arial.ttf", 8); 
$legendBox->setWidth(600); 

# The keys for the scatter layers (milestone symbols) will automatically be added to # the legend box. We just need to add keys to show the meanings of the bar colors. 
$legendBox->addKey("Planeado", 0x660033);
$legendBox->addKey("Mantenimiento", 0xFF9900);






$tiempo = md5( rand(254696, microtime(true)));
$ruta_grafica = $CFG->tmpdir."/charDirector_".$tiempo.".png";
$file = fopen($ruta_grafica, "w");
if($file)
{
	fwrite($file, $c->makeChart2(PNG));
	fclose($file);
}

?>
<table width='98%' align='center'>
	<tr>
		<td>
			<h1 id="tablagrafica"></h1>
			<img src="<?=$CFG->wwwroot?>/tmp/charDirector_<?=$tiempo?>.png" border="0" > 
		</td>
	</tr>
</table>