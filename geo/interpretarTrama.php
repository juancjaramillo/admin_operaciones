<?
include("../application.php");
require($CFG->common_libdir . "/funciones_gps.php");
if(!isset($_GET["tr"])) die("Error");

$tr=$_GET["tr"];
echo date("Y-m-d H:i:s || ");

$trama=$tr;
/*
if(preg_match("/^([^ ]+) +([^ ]+) .*(\\\$GPRMC.*)$/",$trama,$matches)){
	$trama=$matches[1] . " " . $matches[2] . " " . $matches[3];
}
*/
if(preg_match("/^([^ ]+) +([^ ]+) .*([$]?)(GPRMC.*)$/",$trama,$matches)){
  $trama=$matches[1] . " " . $matches[2] . " $" . $matches[4];
}
echo $trama . " ";
if($arreglo_registro=interpretar_trama($trama)){
	if(!insertar_registro_gps($arreglo_registro)){
		error_log(print_r($arreglo_registro,TRUE));
		echo "Error";
	}
	else{
		//Verificar alertas
//		error_log(print_r($arreglo_registro,TRUE));
		if($arreglo_registro["velocidad"]>80 and $arreglo_registro["velocidad"]<115){
			$vehi=$db->sql_row("SELECT * FROM vehiculos WHERE idgps='$arreglo_registro[id_vehiculo]'");
			if($alerta=$db->sql_row("
				SELECT *
				FROM alertas
				WHERE hora>'" . date("Y-m-d H:i:s",strtotime("1 hour ago")) . "'
				AND id_vehiculo='$vehi[id]'
				AND id_tipo='4' /*Exceso de velocidad*/
				AND ack_hora IS NULL
			")){
				//Ya existe la alerta
			}
			else{
				error_log("Exceso de velocidad ($arreglo_registro[velocidad]) vehículo $arreglo_registro[id_vehiculo].");
				$qInsert=$db->sql_query("
					INSERT INTO alertas (hora,id_centro,id_tipo,id_micro,id_vehiculo,observaciones)
					VALUES (now(),'$vehi[id_centro]','4' /*Exceso de velocidad*/,NULL,'$vehi[id]','$arreglo_registro[velocidad] k/h')
				");
			}

		}
		echo "OK";
	}
}
else echo "Error";

?>
