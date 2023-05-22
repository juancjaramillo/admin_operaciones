<?
include("../application.php");
if(!isset($_GET["lineas"])) $lineas=200;
else $lineas=$_GET["lineas"];
$command="tail -n $lineas /var/log/comDaemon | grep -a \"^[0-9]\"";
echo "<pre>\n";
exec($command,$output);
print_r(implode("\n",$output));
echo "\n</pre>\n";

?>
