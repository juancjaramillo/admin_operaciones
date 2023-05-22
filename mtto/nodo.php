<?
include("../application.php");
echo __FILE__ . "<br>\n";
$frm=$_GET;
$grupo=$db->sql_row("SELECT * FROM mtto.grupos WHERE id='$frm[id_grupo]'");
preguntar($grupo);
$qEquipos=$db->sql_query("SELECT * FROM mtto.equipos WHERE id_grupo='$frm[id_grupo]'");
echo "<table>\n";
while($equipo=$db->sql_fetchrow($qEquipos)){
	echo "<tr style=\"cursor:pointer\" onClick=\"abrirVentanaJavaScript('hojavidaequipos','900','600','/pa/mtto/equipos.php?mode=hoja_vida&amp;id_equipo=124')\"><td>";
	echo "<img src=\"" . $CFG->admin_dir . "/iconos/transparente/gear.png\"></td><td width=\"5\"></td><td>" . $equipo["nombre"];
 	echo "</td></tr>\n";
}
echo "</table>\n";
$qRutinas=$db->sql_query("SELECT * FROM mtto.rutinas WHERE id_grupo IN(SELECT getParents('$frm[id_grupo]','mtto.grupos'))");
echo "<table>\n";
while($rutina=$db->sql_fetchrow($qRutinas)){
	echo "<tr><td><img src=\"" . $CFG->admin_dir . "/iconos/transparente/file2.gif\"></td><td width=\"5\"></td><td>" . utf8_encode($rutina["rutina"]) . "</td></tr>\n";
}
echo "</table>\n";

?>
