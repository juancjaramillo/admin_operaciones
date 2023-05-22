<?
include("../application.php");
header("Content-type: text/xml");
echo "<?xml version=\"1.0\"  encoding=\"ISO-8859-1\" ?>\n";
if(!isset($_GET["telefono"])) die("No viene el teléfono.");
$telefono=$_GET["telefono"];
$qString="select *, x(the_geom), y(the_geom) FROM clientes WHERE telefono='$telefono'";

$qid=$db->sql_query($qString);
echo "<personas>\n";
if($persona=$db->sql_fetchrow($qid)){
  echo "\t<persona>\n";
  echo "\t\t<id>" . $persona["id"] . "</id>\n";
  echo "\t\t<nombre>" . trim($persona["nombre"]) . "</nombre>\n";
  echo "\t\t<direccion>" . $persona["direccion"] . "</direccion>\n";
  echo "\t\t<x>" . $persona["x"] . "</x>\n";
  echo "\t\t<y>" . $persona["y"] . "</y>\n";
  echo "\t</persona>\n";
}

echo "</personas>\n";

?>

