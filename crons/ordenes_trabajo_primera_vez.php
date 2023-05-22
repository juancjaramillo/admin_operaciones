<?
include(dirname(__FILE__) . "/../application.php");

$mensaje = "";

$qid = $db->sql_query("SELECT o.id, o.id_equipo, to_char(o.fecha_planeada,'YYYY-MM-DD') as fecha_planeada, to_char(o.fecha_planeada,'HH24:MI:SS') as hora_planeada, r.rutina, e.nombre as equipo,e.kilometraje as km, e.horometro as horo
		FROM mtto.ordenes_trabajo o 
		LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina 
		LEFT JOIN mtto.equipos e ON e.id=o.id_equipo
		WHERE o.id_estado_orden_trabajo=10 AND o.fecha_ejecucion_inicio IS NULL AND r.activa
		ORDER BY o.id");
while($ot = $db->sql_fetchrow($qid))
{
	if(!$rpv = $db->sql_row("SELECT * FROM mtto.rutinas_primera_vez WHERE id_orden_trabajo=".$ot["id"])){//Quiere decir que no se ha programado por primera vez.
	//No hacer nada
		break(1);
	}
	$entra = true;
//	$manana = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + 1 * 24 * 60 * 60);
	$manana = date("Y-m-d",strtotime("tomorrow"));

	if($rpv["fecha"] != "")
	{
		if(!preg_match("/^[+]/",$rpv["fecha"],$match))
		{
			if($rpv["fecha"] <= $manana)
			{
				$fechaPlaneada = $manana;
				$entra = false;
			}
		}
	}
	if($rpv["km"] != "")
	{
		if(!preg_match("/^[+]/",$rpv["km"],$match))
		{
			if(round($rpv["km"]) <= round($ot["km"]))
			{
				$fechaPlaneada = $manana;
				$entra = false;
			}
		}
	}

	if($rpv["horometro"] != "")
	{
		if(!preg_match("/^[+]/",$rpv["horometro"],$match))
		{
			if($rpv["horometro"] <= $ot["horo"])
			{
				$fechaPlaneada = $manana;
				$entra = false;
			}
		}
	}

	if($entra)
	{
		$actual = array("km"=>$rpv["km_actual"], "horo"=>$rpv["horo_actual"], "fecha"=>$rpv["fecha_actual"]);
		$fechaPlaneada = calcular_fecha_planeada($ot["id_equipo"], $rpv["fecha"], $rpv["horometro"], $rpv["km"], $actual);
	}
	if($ot["fecha_planeada"] != $fechaPlaneada)
	{
		$strSQL="UPDATE mtto.ordenes_trabajo SET fecha_planeada='".$fechaPlaneada." ".$ot["hora_planeada"]."' WHERE id=".$ot["id"];
//		echo $strSQL . "\n";
		$db->sql_query($strSQL);
		$mensaje .= "La fecha planeada de la rutina ".$ot["rutina"]." del equipo ".$ot["equipo"]." se cambió de ".$ot["fecha_planeada"]." ".$ot["hora_planeada"]." a ".$fechaPlaneada." ".$ot["hora_planeada"]."\n";
	}
}
if($mensaje != "")
{
	$mensaje = "Buen dia \n\n".$mensaje."\n\nCordialmente,\nAIDA";
	$to = "sistemas@promoambientaldistrito.com";
	$subject = "Cambios de fecha en las rutinas de primera vez";
	mail($to,$subject,$mensaje);

	mail("sistemas@promoambientaldistrito.com",$subject,$mensaje);
}

?>
