<?
include("../application.php");
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
echo '<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">';

$cond = " AND a.id_centro = '".$vista."'";
if($id_ase != "")
	$cond = " AND a.id='".$id_ase."'";

$veh = $servicios = array();
$fechasSemana[$dia]. " 08:00:00";
$consulta = "SELECT i.id_vehiculo, v.codigo||'/'||v.placa as codigo, hora_inicio, hora_fin, i.codigo as micro, id_servicio, servicio
	FROM micros_frecuencia mf
	LEFT JOIN micros i ON i.id=mf.id_micro
	LEFT JOIN ases a ON a.id = i.id_ase
	LEFT JOIN vehiculos v ON v.id=i.id_vehiculo
	LEFT JOIN servicios s ON s.id=i.id_servicio
	WHERE dia='".$dia."' AND fecha_hasta IS NULL ".$cond."
	ORDER BY v.codigo, hora_inicio";

$qid = $db->sql_query($consulta);
$defaultPalette = array(
  0x339966, 0x006666, 0xcc9999, 0xff3333, 0x33ff33, 0x6666ff, 0xffff00, 0xff66ff, 0x99ffff, 0xffcc33, 0x999900, 0xcc3300, 0x669999, 0x993333, 0x006600, 0x990099);

while($mov = $db->sql_fetchrow($qid))
{
	$ini = $fechasSemana[$dia]." ".$mov["hora_inicio"];
	$fin = $fechasSemana[$dia]." ".$mov["hora_fin"];
	if($mov["hora_inicio"] > $mov["hora_fin"])
	{
		list($anio,$mes,$diaSig)=split("-",$fechasSemana[$dia]);
		$fin = date("Y-m-d",mktime(0,0,0, $mes,$diaSig,$anio) + 1 * 24 * 60 * 60)." ".$mov["hora_fin"];
	}	

	$veh[$mov["id_vehiculo"]]["codigo"] = $mov["codigo"];
	$key = $ini."-".$fin;
	
	if(isset($veh[$mov["id_vehiculo"]]["horas"][$key]))
	{
		$ser = $mov["id_servicio"];
		//if($veh[$mov["id_vehiculo"]]["horas"][$key]["servicio"] != $mov["id_servicio"])
		//	$ser = $mov["id_servicio"]."/".$veh[$mov["id_vehiculo"]]["horas"][$key]["servicio"];
		$veh[$mov["id_vehiculo"]]["horas"][$key] = array("micro"=>$veh[$mov["id_vehiculo"]]["horas"][$key]["micro"]."/".$mov["micro"], "ini"=>$ini, "fin"=>$fin, "servicio"=> $ser);
	}
	else
		$veh[$mov["id_vehiculo"]]["horas"][$key] = array("micro"=>$mov["micro"],"ini"=>$ini, "fin"=>$fin, "servicio"=>$mov["id_servicio"]);
	
	$servicios[$mov["id_servicio"]] = array("nombre"=>$mov["servicio"], "color"=>$defaultPalette[$mov["id_servicio"]]); 
}

//preguntar($servicios);

require_once($CFG->libdir."/ChartDirector/lib/phpchartdir.php");

$labels = $taskNo = $startDate = $endDate = $colors = array();
$i=$z=0;
foreach($veh as $idVehiculo => $dx)
{
	$labels[] = $dx["codigo"];
	for($j=0; $j<count($dx["horas"]); $j++)
	{
		$taskNo[] = $i;
	}
	foreach($dx["horas"] as $rangos)
	{
		$fechaIni = dejarUnDigitoFecha($rangos["ini"]);
		$startDate[] = chartTime($fechaIni["anio"], $fechaIni["mes"], $fechaIni["dia"], $fechaIni["hora"], $fechaIni["minuto"], 0);
		
		$fechaFin = dejarUnDigitoFecha($rangos["fin"]);
		$endDate[] = chartTime($fechaFin["anio"], $fechaFin["mes"], $fechaFin["dia"], $fechaFin["hora"], $fechaFin["minuto"], 0);
		$colors[] = $servicios[$rangos["servicio"]]["color"];

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

$plotAreaObj = $c->setPlotArea(90, 20, 520, 250, 0xffffff, 0xeeeeee, LineColor, 0xc0c0c0, 0xc0c0c0); 

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
$layer = $c->addBoxWhiskerLayer2($startDate, $endDate, null, null, null, $colors); 
$layer->setXData($taskNo); 
$layer->setBorderColor(SameAsMainColor); 

# Divide the plot area height ( = 200 in this chart) by the number of tasks to get # the height of each slot. Use 80% of that as the bar height. 
$layer->setDataWidth((int)(200 * 4 / 5 / count($labels))); 

# Add a legend box at 
$legendBox = $c->addLegend2(15, 280, 0, "arial.ttf", 8); 
$legendBox->setWidth(600); 

# The keys for the scatter layers (milestone symbols) will automatically be added to # the legend box. We just need to add keys to show the meanings of the bar colors. 
foreach($servicios as $dx)
	$legendBox->addKey($dx["nombre"], $dx["color"]);

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