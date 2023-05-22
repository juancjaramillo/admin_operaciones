<?
header("Content-type: image/png");
$dirroot     = dirname(__FILE__);
if(!isset($_GET["color"])) $color="0,255,0";
else $color=$_GET["color"];
if(!isset($_GET["icono"]) || $_GET["icono"]=="") $icono="flecha.png";
else $icono=$_GET["icono"];
$color=explode(",",$color);
$imagen=imagecreatefrompng($dirroot . "/" . $icono);

$red=imagecolorclosest($imagen,255,0,0);
//imagecolordeallocate($imagen, $red);
//$nuevo_color=imagecolorallocate($imagen,$color[0],$color[1],$color[2]);
imagecolorset($imagen,$red,$color[0],$color[1],$color[2]);

imagealphablending($imagen,true);
imagesavealpha($imagen,true);

imagepng($imagen);
imagedestroy($imagen);
?>
