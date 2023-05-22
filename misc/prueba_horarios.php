<?
include("../application.php");

$arreglo = array("id_vehiculo"=>10023, "tiempo"=>"2012-12-04 17:48:48");

//$qid=$db->sql_row("SELECT v.* FROM vehiculos v WHERE v.idgps = '" . $arreglo["id_vehiculo"] . "'");

$insertar = horarios_laborables($arreglo);

//preguntar($qid);

preguntar("insertar: ".$insertar);


function horarios_laborables($arreglo)
{
	global $db, $CFG;

	$dia = strftime("%u",strtotime($arreglo["tiempo"]));
	$hora = strftime("%H:%M:%S",strtotime($arreglo["tiempo"]));	

	$horarios = array();
	$qidH = $db->sql_query("SELECT h.* FROM vehiculos_horarios h LEFT JOIN vehiculos v ON v.id=h.id_vehiculo WHERE v.idgps='".$arreglo["id_vehiculo"]."'");
	if($db->sql_numrows($qidH) == 0)
		return "inserta";
	else
	{
		while($h = $db->sql_fetchrow($qidH))		
		{
			if($h["dia"] == $dia && $h["hora_inicio"]<=$hora && $h["hora_final"] >= $hora)
				return "inserta";
		}
		
		return "NO inserta";
	}
}


?>
