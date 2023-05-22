<?
include("../application.php");
header("Content-type: text/xml");
echo "<?xml version=\"1.0\"  encoding=\"ISO-8859-1\" ?>\n";
if(!isset($_GET["direccion"])){error_log("No viene la dirección");die();};
if(!isset($_GET["id_cliente"])){error_log("No viene el id_cliente");die();};
if(!isset($_GET["id_error"])){error_log("No viene el id_error");die();};
if(!isset($_GET["id_estado"])){error_log("No viene el id_estado");die();};

$servicio["id_cliente"]=$_GET["id_cliente"];
$servicio["fecha_servicio"]=date("Y-m-d H:i:s");
$servicio["direccion"]=$_GET["direccion"];
$servicio["id_estado"]=$_GET["id_estado"];
$servicio["id_usuario"]="1";//El usuario automático
//Verificar si ya está ocupado...
if(isset($_GET["id_vehiculo"]) && $_GET["id_error"]==0){//Servicio exitoso
	$vehiculo=$db->sql_row("SELECT * FROM vehiculos WHERE id='" . $_GET["id_vehiculo"] . "'");
	if($vehiculo["acepta"]==0){
		$_GET["id_error"]=12;//Vehículo ocupado
		error_log("Se le iba a asignar doble carrera al vehículo " . $vehiculo["idgps"]);
	}
}

if($_GET["id_error"]!=0) $servicio["fecha_cancelacion"]=date("Y-m-d H:i:s");
$servicio["id_tipo_servicio"]="1";// 1 | Normal
$servicio["id_frecuencia"]="1";//Hay que ver luego cómo se diferencian
if($_GET["id_error"]!=0) $servicio["id_error"]=$_GET["id_error"];

$id_servicio=$db->sql_insert("gd_servicios",$servicio);
if(isset($_GET["x"])){
	$db->sql_query("UPDATE clientes SET the_geom=GeometryFromText('POINT($_GET[x] $_GET[y])',4326), fecha=now() WHERE id='$id_cliente'");
	$db->sql_query("UPDATE gd_servicios SET the_geom=GeometryFromText('POINT($_GET[x] $_GET[y])',4326) WHERE id='$id_servicio'");
}
if(isset($_GET["id_vehiculo"]) && $_GET["id_error"]==0){//Servicio exitoso
		$asignacion["id_servicio"]=$id_servicio;
		$asignacion["fecha_asignacion"]=$servicio["fecha_servicio"];
		$asignacion["id_vehiculo"]=$_GET["id_vehiculo"];
		$asignacion["id_usuario"]=$servicio["id_usuario"];
		$asignacion["id_estado"]=3;//3 | RESERVADO     | ASIGNACION
		$id_asignacion=$db->sql_insert("gd_asignaciones",$asignacion);
		error_log("Ocupando carro $asignacion[id_vehiculo]");
		$db->sql_query("UPDATE vehiculos SET acepta='0', estado='Ocupado' WHERE id='$asignacion[id_vehiculo]'");
		$db->sql_query("INSERT INTO gps_vehi (id_vehi,tiempo,evento) VALUES((SELECT idgps::int FROM vehiculos WHERE id='$asignacion[id_vehiculo]'),'" . gmdate("Y-m-d H:i:s") . "','98')");
}
echo "<servicios>\n";
  echo "\t<servicio>\n";
  echo "\t\t<id>$id_servicio</id>\n";
  echo "\t\t<error>" . $_GET["id_error"] . "</error>\n";
  echo "\t</servicio>\n";
echo "</servicios>\n";

?>

