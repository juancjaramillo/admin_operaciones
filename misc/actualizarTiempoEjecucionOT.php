<?
include("../application.php");

$qid = $db->sql_query("SELECT id, fecha_ejecucion_inicio, fecha_ejecucion_fin, tiempo_ejecucion 
	FROM mtto.ordenes_trabajo
	WHERE fecha_ejecucion_inicio IS NOT NULL AND fecha_ejecucion_fin IS NOT NULL
	ORDER BY fecha_ejecucion_inicio");
while($ot = $db->sql_fetchrow($qid))
{
	preguntar($ot);
	$gasto = restarFechasConHHmmss($ot["fecha_ejecucion_fin"],$ot["fecha_ejecucion_inicio"]);
	preguntar($gasto);
//	$db->sql_query("UPDATE mtto.ordenes_trabajo SET tiempo_ejecucion='".$gasto."' WHERE id=".$ot["id"]);
}

?>
