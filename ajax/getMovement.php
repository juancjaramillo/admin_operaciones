<?
include("../application.php");
if(sizeof($_POST)>sizeof($_GET)) $frm=$_POST;
else $frm=$_GET;
if(!isset($frm["codigo"]) || !isset($frm["fecha"])) die();
$user=$_SESSION[$CFG->sesion]["user"];
$cons = "
	SELECT mov.id, m.codigo, v.kilometraje, v.horometro
	FROM rec.movimientos mov LEFT JOIN vehiculos v ON v.id=mov.id_vehiculo LEFT JOIN micros m ON m.id=mov.id_micro
	WHERE mov.inicio::date = '".$frm["fecha"]."' AND mov.final IS NULL AND v.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')
    AND (v.codigo = '".$frm["codigo"]."' OR upper(v.placa) like '".strtoupper($frm["codigo"])."')
";
$qid=$db->sql_query($cons);
echo "{\nmovements:[";
$i=0;
while($result=$db->sql_fetchrow($qid)){
	if($i!=0) echo ",";
	echo "{id:" . $result["id"] . ",code:\"" . $result["codigo"] . "\",kilometraje:\"" . round($result["kilometraje"]) . "\",horometro:\"" . round($result["horometro"]) . "\"}";
	$i++;
}
echo "]}";

