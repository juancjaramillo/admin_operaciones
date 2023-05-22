<?
include("../application.php");
require($CFG->common_libdir . "/funciones_gps.php");
$trama="8                 30038 D1,DE   46    0 12 \$GPRMC,131208.00,A,1026.182357,N,07530.917425,W,21.9,22.2,200213,5.2,E,A*21";
$trama="8                10053     203     0    -8  14 GPRMC,090506.00,A,0417.92159,N,07447.82378,W,0.000,0.0,200213,,,D*43";

echo $trama . "\n";
if(preg_match("/^([^ ]+) +([^ ]+) .*([$]?)(GPRMC.*)$/",$trama,$matches)){
  $trama=$matches[1] . " " . $matches[2] . " $" . $matches[4];
}
echo $trama . "\n";

if($arreglo_registro=interpretar_trama($trama)){
	print_r($arreglo_registro);
}
else echo "Error";
echo "\n";

?>
