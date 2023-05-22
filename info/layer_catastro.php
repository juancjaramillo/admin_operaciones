<?
include("application.php");
$frm=$_GET;
if(!isset($_SESSION[$CFG->sesion]["user"])){
  error_log("No existe la sesión.");
  die();
}

$user=$_SESSION[$CFG->sesion]["user"];

$condicion=" the_geom IS NOT NULL";

if(!isset($frm["field"])) $frm["field"]="the_geom";

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

$bounds=$db->sql_row("SELECT -74.173976 as minx, -74.024752 as maxx,
		4.495615 as miny,4.758483 as maxy FROM catastro cl");
echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
?>
<wfs:FeatureCollection xmlns:ms="https://mapserver.gis.umn.edu/mapserver" xmlns:wfs="https://www.opengis.net/wfs" xmlns:gml="https://www.opengis.net/gml" xmlns:ogc="https://www.opengis.net/ogc" xmlns:xsi="https://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="https://www.opengis.net/wfs http://schemas.opengis.net/wfs/1.0.0/WFS-basic.xsd http://mapserver.gis.umn.edu/mapserver https://aneto.oco/cgi-bin/worldwfs?SERVICE=WFS&amp;VERSION=1.0.0&amp;REQUEST=DescribeFeatureType&amp;TYPENAME=point&amp;OUTPUTFORMAT=XMLSCHEMA">
      <gml:boundedBy>
        <gml:Box srsName="EPSG:4326">
          <gml:coordinates><?=$bounds["minx"]?>,<?=$bounds["miny"]?> <?=$bounds["maxx"]?>,<?=$bounds["maxy"]?></gml:coordinates>
        </gml:Box>
      </gml:boundedBy>
<?
$strSQL ="SELECT gid,suscriptor,alcaldia,direccion,ST_AsGML($frm[field],7,0) as \"gmlgeom\" FROM catastro WHERE $condicion limit 50000";

$macro=0;
$distinctColor=-1;
$qid=$db->sql_query($strSQL);
while($result=$db->sql_fetchrow($qid)){
	if(preg_match("/^(<[^>]*>)(.*)(<[^>]*>)$/",$result["gmlgeom"],$matches)){
		$startTag=$matches[1];
		$featureMember=$matches[2];
		$endTag=$matches[3];
		echo "<gml:featureMember>\n";
		echo $startTag . "\n";
		echo "<gml:gid>" . $result["gid"] . "</gml:gid>\n";
		echo "<ms:suscriptor>" . $result["suscriptor"] . "</ms:suscriptor>\n";
		echo "<ms:alcaldia>" . $result["alcaldia"] . "</ms:alcaldia>\n";
		echo "<ms:direccion>" . $result["direccion"] . "</ms:direccion>\n";
		echo "<ms:strokecolor>#ff0000</ms:strokecolor>\n";
		echo $featureMember . "\n";
		echo $endTag . "\n";
		echo "</gml:featureMember>\n";
	}
}

?>
</wfs:FeatureCollection>

