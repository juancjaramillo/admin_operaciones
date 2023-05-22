<?
include("application.php");
$frm=$_GET;
dl("php_mapscript.so");
$map = ms_newMapObj("pa.map");
preg_match("/^([^,]*),([^,]*),([^,]*),([^,]*)$/",$frm["BBOX"],$extents);
$frm["minx"]=$extents[1];
$frm["miny"]=$extents[2];
$frm["maxx"]=$extents[3];
$frm["maxy"]=$extents[4];

$map->setextent($frm["minx"], $frm["miny"], $frm["maxx"], $frm["maxy"]);
$map->setsize($frm["WIDTH"],$frm["HEIGHT"]);
$layerquery=$map->getLayerByName("clientes");
$layerquery->set("template","ttt");
$layerquery->set("tolerance",5);

$x=$frm["X"];
//$y=$frm["maxy"]-($frm["Y"]*$factor_y);
$y=$frm["Y"];

$oPoint = ms_newPointObj();
$oPoint->setxy($x, $y);

$err=@$layerquery->queryByPoint($oPoint,MS_MULTIPLE,-1);
if ($err!=MS_SUCCESS) {
	die();
}
$num_results = $layerquery->getNumResults();
if ($num_results!=0) {
	$arreglo_ids=array();
	for($i=0;$i<$num_results;$i++){
		$oRes = $layerquery->getResult($i);
		array_push($arreglo_ids,$oRes->shapeindex);
	}
	info_punto($arreglo_ids);
}

function info_punto($arreglo_ids){
	global $CFG, $frm, $db;

	if(isset($frm["cont"])){
		$cont=$frm["cont"];
		unset($frm["cont"]);
	}
	else $cont=0;
	$id=$arreglo_ids[$cont];

	include($CFG->modulesdir . "/clientes.php");
	$entidad->load($id);
	$entidad->set("newMode","consultar");
	$entidad->set("mode","consultar");
	$string_entidad=utf8_encode($entidad->getForm($frm));

	echo "<table border=\"1\">";
	echo utf8_encode("<tr><td colspan=\"2\" align=\"center\"><b>CLIENTE</b></th></tr>");
	echo $string_entidad;
	echo "<tr><td colspan=\"2\" align=\"center\"><table width=\"100%\"><tr>";
	echo "<td width=\"25%\" align=\"left\">";
	if($cont>0){
		echo "<span style=\"cursor:pointer\" onClick=\"refreshPopup('info.php" . hallar_querystring("cont",($cont-1),$frm) . "')\">&lt;</span>";
	}
	echo "</td>";
	echo "<td align=\"center\">Cliente # " . ($cont+1) . "/" . sizeof($arreglo_ids) . "</td>";
	echo "<td width=\"25%\" align=\"right\">";
	if(sizeof($arreglo_ids)>($cont+1)){
		echo "<span style=\"cursor:pointer\" onClick=\"refreshPopup('info.php" . hallar_querystring("cont",($cont+1),$frm) . "')\">&gt;</span>";
	}
	echo "</td>";
	echo "</tr></table></td></tr>";
	echo "</table>";
}
function retUrl($frm){

}
?>
