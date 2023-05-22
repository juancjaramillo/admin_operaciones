<?
include("../application.php");
$frm=$_GET;
if(!isset($_SESSION[$CFG->sesion]["user"])){
  error_log("No existe la sesión.");
  die();
}

$user=$_SESSION[$CFG->sesion]["user"];
if(!isset($frm["id"])) die();
if(!isset($frm["field"])) $frm["field"]="the_geom";

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

$strq = "
  SELECT ST_XMin(ST_Boundary($frm[field])) as minx, ST_YMin(ST_Boundary($frm[field])) as miny, ST_XMax(ST_Boundary($frm[field])) as maxx, ST_YMax(ST_Boundary($frm[field])) as maxy
  FROM micros_arcos
  WHERE id_micro='$frm[id]'
";
$bounds=$db->sql_row($strq);
echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
?>
<wfs:FeatureCollection xmlns:ms="http://mapserver.gis.umn.edu/mapserver" xmlns:wfs="http://www.opengis.net/wfs" xmlns:gml="http://www.opengis.net/gml" xmlns:ogc="http://www.opengis.net/ogc" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opengis.net/wfs http://schemas.opengis.net/wfs/1.0.0/WFS-basic.xsd                         http://mapserver.gis.umn.edu/mapserver http://aneto.oco/cgi-bin/worldwfs?SERVICE=WFS&amp;VERSION=1.0.0&amp;REQUEST=DescribeFeatureType&amp;TYPENAME=point&amp;OUTPUTFORMAT=XMLSCHEMA">
      <gml:boundedBy>
        <gml:Box srsName="EPSG:4326">
          <gml:coordinates><?=$bounds["minx"]?>,<?=$bounds["miny"]?> <?=$bounds["maxx"]?>,<?=$bounds["maxy"]?></gml:coordinates>
        </gml:Box>
      </gml:boundedBy>
<?
$strSQL="SELECT id, id_arco, nombre, ST_AsGML($frm[field],7,0) as \"gmlgeom\" FROM micros_arcos WHERE id_micro='$frm[id]'";
$qid=$db->sql_query($strSQL);
$i=0;
while($result=$db->sql_fetchrow($qid)){
	if(preg_match("/^(<[^>]*>)(.*)(<[^>]*>)$/",$result["gmlgeom"],$matches)){
		$startTag=$matches[1];
		$featureMember=$matches[2];
		$endTag=$matches[3];
//		print_r($matches);
		echo "<gml:featureMember>\n";
		echo $startTag . "\n";
		echo "<gml:id>" . $result["id_arco"] . "</gml:id>\n";
		echo "<ms:num>" . $i . "</ms:num>\n";
		echo "<ms:name>" . $result["nombre"] . "</ms:name>\n";
		echo $featureMember . "\n";
		echo $endTag . "\n";
		echo "</gml:featureMember>\n";
	}
	/*
  ?>
  <gml:featureMember>
		<gml:id><?=$result["id"]?></gml:id>
		<ms:num><?=$i?></ms:num>
  <?=$result["gmlgeom"];?>
  </gml:featureMember>
<?
	*/
	$i++;
}
?>
</wfs:FeatureCollection>

