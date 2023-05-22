<?
include("../application.php");
$frm=$_GET;
dl("php_mapscript.so");
$map = ms_newMapObj("../pa.map");
Print_R($map);

preg_match("/^([^,]*),([^,]*),([^,]*),([^,]*)$/",$frm["BBOX"],$extents);
$frm["minx"]=$extents[1];
$frm["miny"]=$extents[2];
$frm["maxx"]=$extents[3];
$frm["maxy"]=$extents[4];

$map->setextent($frm["minx"], $frm["miny"], $frm["maxx"], $frm["maxy"]);
$map->setsize($frm["WIDTH"],$frm["HEIGHT"]);
$mapa = $map->draw();
$mapa->saveImage("/tmp/borrar.png");
header('Content-Type: image/png');
readfile("/tmp/borrar.png");
//preguntar($map);
?>
