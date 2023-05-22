<?
include("../../application.php");

$peajes = array(5, 6, 7, 1, 12, 15);
//$peajes = array(8, 9, 10, 11, 13, 14);

$qid = $db->sql_query("SELECT mp.* 
	FROM rec.movimientos_peajes mp
	LEFT JOIN rec.movimientos m ON m.id=mp.id_movimiento
	LEFT JOIN micros r ON r.id = m.id_micro
	LEFT JOIN ases a ON a.id = r.id_ase
	WHERE a.id_centro=1
	ORDER BY mp.id");
while($pe = $db->sql_fetchrow($qid))
{
//	if(!in_array($pe["id_peaje"], $peajes))
	{
		preguntar($pe);

	}

}



?>
