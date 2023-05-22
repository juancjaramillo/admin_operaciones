<?
die;








include("../../application.php");

$tipos = array("Preventivo"=>1, "Predictivo"=>3, "Correctivo"=>2);
$prior = array("Baja"=>3, "Media"=>2, "Alta"=>1);
$frec = array("anual"=>4, "quincenal"=>7, "semestral"=>5, "diario"=>3, "mensual"=>1);

$i=0;
$j=1;
$fp=fopen("girardot2.csv","r");
while(($data=fgetcsv($fp,1000,";"))!=FALSE)
{
	preguntar($data);
	if($i > 0)
	{
		if($data[0] != "")
		{
			$j = 1;
			$f1 = $f2 = $f3 = "null";
			if($data[4] != "")
				$f1 = "'".$frec[strtolower(trim($data[4]))]."'";
			if($data[5] != "")
				$f2 = "'".str_replace(".","",trim($data[5]))."'";
			if($data[6] != "")
				$f3 = "'".str_replace(".","",trim($data[6]))."'";

			$te = 0;
			if($data[8] != "")
				$te = "'".trim($data[8])."'";

			$pr = 1;
			if($data[10] != "")
				$pr = "'".$prior[trim($data[10])]."'";

			$tp = 1;
			if($data[9] != "")
				$tp = "'".$tipos[trim($data[9])]."'";


			$cons = "INSERT INTO mtto.rutinas (rutina, id_sistema, id_grupo, lugar, id_frecuencia, frec_horas, frec_km, fec_cumplir, tiempo_ejecucion, id_tipo_mantenimiento, id_prioridad) VALUES ('".trim($data[0])."', '".trim($data[1])."', '".trim($data[2])."','".trim($data[3])."',".$f1.",".$f2.",".$f3.",1, ".$te." ,".$tp.", ".$pr.")";
			$db->sql_query($cons);
			$idRutina = $db->sql_nextid();
			$db->sql_query("INSERT INTO mtto.rutinas_centros (id_rutina, id_centro) VALUES ('".$idRutina."','3')");
		}

		//actividades
		$tiempo = $tiempoCar = 1;
		if(trim($data[14]) != "")
			$tiempo = "'".str_replace(".","",trim($data[14]))."'";

		if(trim($data[15]) != "")
			$tiempoCar = "'".str_replace(".","",trim($data[15]))."'";

		$db->sql_query("INSERT INTO mtto.rutinas_actividades (id_rutina, orden, descripcion, tiempo) VALUES ('".$idRutina."', '".$j."','".trim($data[13])."',".$tiempo.")");
		$idActividad = $db->sql_nextid();
		if($data[15] != "")
		{
			$db->sql_query("INSERT INTO mtto.rutinas_actividades_cargos (id_actividad, id_cargo, tiempo) VALUES ('".$idActividad."', '".trim($data[15])."',".$tiempoCar.")");
		}







	}
	$i++;
/*
	preguntar($data);
	$db->sql_query("INSERT INTO mtto.rutinas_actividades (id_rutina, orden, descripcion, tiempo) VALUES ('".$idRutina."','".$i."','".ucfirst($data[0])."','".trim($data[1])."')");
	$i++;
	$idRA = $db->sql_nextid();
	$db->sql_query("INSERT INTO mtto.rutinas_actividades_cargos (id_actividad, id_cargo, tiempo) VALUES ('".$idRA."','".$mecan."','".trim($data[1])."')");
	*/
}



echo "fin ";




















?>
