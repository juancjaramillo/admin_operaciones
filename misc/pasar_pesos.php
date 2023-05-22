<?
include("../application.php");

$qid = $db->sql_query("SELECT * FROM rec.pesos ORDER BY id");
while($query = $db->sql_fetchrow($qid))
{
	$qp = $db->sql_row("SELECT count(id) as num FROM rec.movimientos_pesos WHERE id_peso=".$query["id"]);
	if($qp["num"] != 1)
	{
		preguntar($query);
		preguntar($qp);
	
	}
}

echo "***********<br>";

$qid = $db->sql_query("SELECT m.id_vehiculo , p.id_peso
		FROM rec.movimientos_pesos p
		LEFT JOIN rec.movimientos m ON m.id=p.id_movimiento
		ORDER BY p.id");
while($query = $db->sql_fetchrow($qid))
{
	preguntar($query);
//	$db->sql_query("UPDATE rec.pesos SET id_vehiculo=".$query["id_vehiculo"]. " WHERE id=".$query["id_peso"]);

}



/*
$qid = $db->sql_query("SELECT t.*, m.inicio FROM rec.temporal t LEFT JOIN rec.movimientos m ON m.id=t.id_movimiento ORDER BY t.id");
while($query = $db->sql_fetchrow($qid))
{
	preguntar($query);
	$peso_inicial = $peso_final = $peso_total = $tiquete_entrada = $tiquete_salida = $fecha_entrada = $fecha_salida = "NULL";
	if($query["peso_inicial"] != "") $peso_inicial = "'".$query["peso_inicial"]."'";
	if($query["peso_final"] != "") $peso_final = "'".$query["peso_final"]."'";
	if($query["peso_total"] != "") $peso_total = "'".$query["peso_total"]."'";
	if($query["tiquete_entrada"] != "") $tiquete_entrada = "'".$query["tiquete_entrada"]."'";
	if($query["tiquete_salida"] != "") $tiquete_salida = "'".$query["tiquete_salida"]."'";
	if($query["fecha_entrada"] != "") 
		$fecha_entrada = "'".$query["fecha_entrada"]."'";
	else
		$fecha_entrada = "'".$query["inicio"]."'";

	if($query["fecha_salida"] != "") $fecha_salida = "'".$query["fecha_salida"]."'";

//	$db->sql_query("INSERT INTO rec.pesos (peso_inicial, peso_final, peso_total, id_lugar_descargue, tiquete_entrada, tiquete_salida, fecha_entrada, fecha_salida) VALUES ($peso_inicial, $peso_final, $peso_total, '".$query["id_lugar_descargue"]."',  $tiquete_entrada, $tiquete_salida, $fecha_entrada,   $fecha_salida)");
//	$id =  $db->sql_nextid();

//	$db->sql_query("INSERT INTO rec.movimientos_pesos (id_peso, id_movimiento, porcentaje) VALUES ($id, '".$query["id_movimiento"]."', 100)");

}
*/


echo "fin";
?>
