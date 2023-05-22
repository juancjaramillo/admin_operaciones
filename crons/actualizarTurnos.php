<?
include(dirname(__FILE__) . "/../application.php");

$cambiados = "";
$qid = $db->sql_query("SELECT id,id_micro, inicio FROM rec.movimientos where id_turno is null");
while($mov = $db->sql_fetchrow($qid))
{
	$cambiados .= "Movimiento id: ".$mov["id"]."\n";
	$ultTurno=$idTurno="";

	$hora = strftime("%H:%M:%S",strtotime($mov["inicio"]));
	$qidTur = $db->sql_query("SELECT t.id, t.hora_inicio
		FROM turnos t 
		LEFT JOIN centros c ON c.id_empresa=t.id_empresa
		LEFT JOIN ases a ON a.id_centro = c.id
		LEFT JOIN micros m ON m.id_ase = a.id
		WHERE m.id=".$mov["id_micro"]."
		ORDER BY hora_inicio");
	while($queryTur = $db->sql_fetchrow($qidTur))
	{
//		preguntar($queryTur);
		if($hora >= $queryTur["hora_inicio"])
			$idTurno= $queryTur["id"];
		$ultTurno = $queryTur["id"];
	}
	if($idTurno == "")
		$idTurno = $ultTurno;

//	preguntar($idTurno);


//	echo "/***********************************//";

	$db->sql_query("UPDATE rec.movimientos SET id_turno = '".$idTurno."' WHERE id=".$mov["id"]);
}

if($cambiados != "")
{
	$mensaje = "Buen día\n\n".$cambiados."\n\nCordialmente,\nAIDA";
	mail("luisa@apli-k.com","Actualizó turnos",$mensaje);	
}

//actualizar asignado pesos
$idsPesosFalse = $idsPesosTrue = array();
$qid = $db->sql_query("SELECT id, asignado FROM rec.pesos ORDER BY id");
while($query = $db->sql_fetchrow($qid))
{
	$qidMP = $db->sql_row("SELECT count(id) as num FROM rec.movimientos_pesos WHERE id_peso=".$query["id"]);
	if($qidMP["num"] == 0)
	{
		if($query["asignado"] == "t")
		{
			$idsPesosFalse[] = $query["id"];
			$db->sql_query("UPDATE rec.pesos SET asignado= false WHERE id=".$query["id"]);
		}
	}elseif($query["asignado"] == "f")
	{
		$idsPesosTrue[] = $query["id"];
		$db->sql_query("UPDATE rec.pesos SET asignado= true WHERE id=".$query["id"]);
	}
}


if(count($idsPesosFalse) > 0 || count($idsPesosTrue) > 0)
{
	$mensaje = "Buen día\n\nSe cambiaron a false los siguiente pesos: \n".implode(",\n",$idsPesosFalse) ."\n\nSe cambiaron a true los siguiente pesos:\n".implode(",\n",$idsPesosTrue) ."\n\nCordialmente,\nAIDA";
	mail("luisa@apli-k.com","Actualizó asignado pesos",$mensaje);
}


?>
