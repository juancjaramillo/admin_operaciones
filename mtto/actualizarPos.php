<?
include("../application.php");

$qid = $db->sql_query("SELECT * FROM llta.llantas WHERE id_centro=4");
while($query = $db->sql_fetchrow($qid))
{
	$mov = $db->sql_row("SELECT posicion, id_tipo_movimiento FROM llta.movimientos WHERE id_llanta=".$query["id"]." ORDER BY fecha DESC limit 1");
	if(nvl($mov["posicion"]) != "")
	{
		if($mov["id_tipo_movimiento"] == 5)
		{
		preguntar($query);
		preguntar($mov);
		}
	}

}


?>
