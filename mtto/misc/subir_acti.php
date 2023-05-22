<?
include("../../application.php");
die;

$mecan = 12;

$idRutina = 143;

$fp=fopen("actividades.csv","r");
$i=1;
while(($data=fgetcsv($fp,1000,";"))!=FALSE)
{
	preguntar($data);
	$db->sql_query("INSERT INTO mtto.rutinas_actividades (id_rutina, orden, descripcion, tiempo) VALUES ('".$idRutina."','".$i."','".ucfirst($data[0])."','".trim($data[1])."')");
	$i++;
	$idRA = $db->sql_nextid();
	$db->sql_query("INSERT INTO mtto.rutinas_actividades_cargos (id_actividad, id_cargo, tiempo) VALUES ('".$idRA."','".$mecan."','".trim($data[1])."')");
}



echo "fin ".$i;




















?>
