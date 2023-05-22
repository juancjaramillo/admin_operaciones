<?
include("../application.php");
if(sizeof($_POST)>sizeof($_GET)) $frm=$_POST;
else $frm=$_GET;
$from="micros m";
$where="(m.fecha_hasta IS NULL OR m.fecha_hasta>now())";
if(isset($frm["id_empresa"]) && $frm["id_empresa"]!="0" && $frm["id_empresa"]!=""){
	$from="micros m LEFT JOIN ases ON m.id_ase=ases.id LEFT JOIN centros c ON ases.id_centro=c.id";
	$where.=" AND c.id_empresa='$frm[id_empresa]'";
}
if(isset($frm["dias"]) && $frm["dias"]!=""){
	$condicionFreq="mf.dia IN($frm[dias])";
	if(isset($frm["id_turno"]) && $frm["id_turno"]!="" && $frm["id_turno"]!="0") $condicionFreq.=" AND mf.id_turno='$frm[id_turno]'";
	$where.=" AND m.id IN (
		SELECT mf.id_micro
		FROM micros_frecuencia mf LEFT JOIN turnos t ON mf.id_turno=t.id
		WHERE t.id_empresa='$frm[id_empresa]' AND $condicionFreq
	)";
}
$qRoutes=$db->sql_query("SELECT m.id,m.codigo FROM $from WHERE $where ORDER BY m.codigo, m.id");
$arrayResults["rows"]=Array();
while($row=$db->sql_fetchrow($qRoutes)){
	$arrayResult["id"]=$row["id"];
	$arrayResult["codigo"]=$row["codigo"];
	array_push($arrayResults["rows"],$arrayResult);
}
echo json_encode($arrayResults);

?>
