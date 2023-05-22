<?php
 
include(dirname(__FILE__) . "/../application.php");

$qid = $db->sql_query("SELECT * FROM mtto.equipos");
while($query = $db->sql_fetchrow($qid))
{
	if($query["id_vehiculo"] != '')
	{
		$vehi = $db->sql_row("SELECT kilometraje, horometro from vehiculos WHERE id=".$query["id_vehiculo"]);
		if($vehi["kilometraje"] != '')
			$db->sql_query("UPDATE mtto.equipos SET kilometraje='".$vehi["kilometraje"]."' WHERE id=".$query["id"]);
		if($vehi["horometro"] != '')
			$db->sql_query("UPDATE mtto.equipos SET horometro ='".$vehi["horometro"]."' WHERE id=".$query["id"]);
	}
}

?>
