<?
include("application.php");
if(!isset($_SESSION[$CFG->sesion]["user"])){
	error_log("No existe la sesión.");
	die();
}

$user=$_SESSION[$CFG->sesion]["user"];

/**/
if(!is_array($user["id_centro"]))
{
	echo "<script>
	  window.location.href='".$CFG->wwwroot."/admin/login.php';
		</script>";
	die;
}
$condicion="al.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') ";
$qAlertas=$db->sql_query("
	SELECT al.*, t.nombre as tipo, m.codigo as ruta, c.centro, v.codigo as vehiculo
	FROM alertas al LEFT JOIN tipos_alertas t ON al.id_tipo=t.id
		LEFT JOIN micros m ON al.id_micro=m.id
		LEFT JOIN centros c ON al.id_centro=c.id
		LEFT JOIN vehiculos v ON al.id_vehiculo=v.id
	WHERE hora >= '" . date("Y-m-d H:i:s",strtotime("-8 hour")) . "'
		AND $condicion
	ORDER BY al.hora
");
echo "<table border=\"1\">\n";
echo "<tr><th>Centro</th><th>Hora</th><th>Alerta</th><th>Ruta</th><th>Móvil</th></tr>\n";
while($alerta=$db->sql_fetchrow($qAlertas)){
	echo "<tr>";
	echo "<td>" . $alerta["centro"] . "</td>";
	echo "<td>" . date("H:i:s",strtotime($alerta["hora"])) . "</td>";
	echo "<td>" . $alerta["tipo"] . "</td>";
	echo "<td>" . $alerta["ruta"] . "</td>";
	echo "<td>" . $alerta["vehiculo"] . "</td>";
	echo "</tr>\n";
}
echo "</table>\n";

?>
