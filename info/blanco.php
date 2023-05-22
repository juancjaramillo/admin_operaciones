<?
include("../application.php");
include($CFG->dirroot."/templates/header_popup.php");

$conPie = array(2,3,22,23);
$conReq = array(1,5,9,16,17,18,21,24,25,26,27,37);

$todas = array_merge($conPie, $conReq);

echo '<table width="98%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">';
$qidInf = $db->sql_query("SELECT pi.id_informe, informe, nombre
	FROM personas_informes pi
	LEFT JOIN informes i ON i.id=pi.id_informe
	LEFT JOIN categorias_informes c ON c.id=i.id_categoria_informe
	WHERE pi.id_persona = '".$_SESSION[$CFG->sesion]["user"]["id"]."' AND primera
	ORDER BY nombre, informe");
while($inf = $db->sql_fetchrow($qidInf))
{
	$alto = 500;
	$parte = "#tabladatos";
	if(in_array($inf["id_informe"], $todas))
		$parte = "#tablagrafica";

	if(in_array($inf["id_informe"], $conPie))
		$alto = 450;
	if(in_array($inf["id_informe"], $conReq))
		$alto = 510;

	echo "<tr>
						<td>
							<iframe name='inf_".$inf["id_informe"]."' width='100%' height='".$alto."' frameborder='0' src='".$CFG->wwwroot."/info/".$inf["id_informe"].".php".$parte."'></iframe>
							<br />
							<br />
							&nbsp;&nbsp;<a class='boton_verde_active' href='".$CFG->wwwroot."/info/".$inf["id_informe"].".php' title='Ir a Informe'>Ir a Informe ".$inf["informe"]." / ".$inf["nombre"]."</a>
							<br />
							<br />
						</td>
				</tr>";
}
echo "</table>";
?>
