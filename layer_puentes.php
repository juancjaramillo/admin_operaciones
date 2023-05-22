<?
include("application.php");
$frm=$_GET;
if(!isset($_SESSION[$CFG->sesion]["user"])){
  error_log("No existe la sesión.");
  die();
}

$user=$_SESSION[$CFG->sesion]["user"];

if(!isset($frm["field"])) $frm["field"]="geom";

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
?>
<wfs:FeatureCollection xmlns:ms="http://mapserver.gis.umn.edu/mapserver" xmlns:wfs="http://www.opengis.net/wfs" xmlns:gml="http://www.opengis.net/gml" xmlns:ogc="http://www.opengis.net/ogc" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opengis.net/wfs http://schemas.opengis.net/wfs/1.0.0/WFS-basic.xsd                         http://mapserver.gis.umn.edu/mapserver http://aneto.oco/cgi-bin/worldwfs?SERVICE=WFS&amp;VERSION=1.0.0&amp;REQUEST=DescribeFeatureType&amp;TYPENAME=point&amp;OUTPUTFORMAT=XMLSCHEMA">
      <gml:boundedBy>
        <gml:Box srsName="EPSG:4326">
          <gml:coordinates><?=$bounds["minx"]?>,<?=$bounds["miny"]?> <?=$bounds["maxx"]?>,<?=$bounds["maxy"]?></gml:coordinates>
        </gml:Box>
      </gml:boundedBy>
<?
$strSQL="SELECT gid as id, puetipo, puecodigo, pueubicaci as ubicacion,
ST_AsGML($frm[field],7,0) as \"gmlgeom\" FROM ideca.puen";

$qid=$db->sql_query($strSQL);
$i=0;
$totalClases=$db->sql_field("SELECT COUNT(DISTINCT puetipo) FROM ideca.puen");
error_log("totalClases:" . $totalClases);
if($totalClases>0){
	$delta=(1/$totalClases);
	$puetipo=0;
	$distinctColor=-1;
	while($result=$db->sql_fetchrow($qid)){
		if($result["puetipo"]!=$puetipo){
			$distinctColor++;
			$hue=$distinctColor*$delta;
			$arrayColorRGB=hsv2rgb($hue,1,1);
			$hexColor=rgb2hex($arrayColorRGB[0],$arrayColorRGB[1],$arrayColorRGB[2]);
			error_log($result["puetipo"] . "=>" . $hue . "=>" . $arrayColorRGB[0] . "," . $arrayColorRGB[1] . "," . $arrayColorRGB[2] . "=>" . $hexColor);
		}

		if(preg_match("/^(<[^>]*>)(.*)(<[^>]*>)$/",$result["gmlgeom"],$matches)){
			$startTag=$matches[1];
			$featureMember=$matches[2];
			$endTag=$matches[3];
			echo "<gml:featureMember>\n";
			echo $startTag . "\n";
			echo "<gml:id>" . $result["id"] . "</gml:id>\n";
			echo "<ms:num>" . $i . "</ms:num>\n";
			echo "<ms:name>" . $result["ubicacion"] . "</ms:name>\n";
			echo "<ms:strokecolor>#$hexColor</ms:strokecolor>\n";
			echo $featureMember . "\n";
			echo $endTag . "\n";
			echo "</gml:featureMember>\n";
		}
		$i++;
		$puetipo=$result["puetipo"];
	}
}
?>
</wfs:FeatureCollection>

