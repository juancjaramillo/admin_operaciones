<?
include("../../application.php");

$qid = $db->sql_query("SELECT id,id_grupo,id_vehiculo FROM mtto.equipos ORDER BY id");
while($query = $db->sql_fetchrow($qid))
{
	if($query["id_vehiculo"] != "")
	{
		preguntar($query);
		$db->sql_query("UPDATE vehiculos SET id_grupo='".$query["id_grupo"]."' WHERE id='".$query["id_vehiculo"]."'");	
	}

}

echo "fin";

?>
