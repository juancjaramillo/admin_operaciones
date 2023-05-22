<?
// error_reporting(E_ALL);
// ini_set("display_errors", 1);

include("application.php");
$frm=$_GET;
if(!isset($_SESSION[$CFG->sesion]["user"])){
  error_log("No existe la sesión.");
  die();
}

$user=$_SESSION[$CFG->sesion]["user"];
$condicion="true AND the_geom IS NOT NULL";
if(!isset($frm["field"])) $frm["field"]="the_geom";

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

$bounds=$db->sql_row("
  SELECT ST_XMin(ST_Boundary($frm[field])) as minx, ST_YMin(ST_Boundary($frm[field])) as miny, ST_XMax(ST_Boundary($frm[field])) as maxx, ST_YMax(ST_Boundary($frm[field])) as maxy
  FROM puntos_criticos 
  WHERE $condicion
");
echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
?>
<wfs:FeatureCollection xmlns:ms="http://mapserver.gis.umn.edu/mapserver" xmlns:wfs="http://www.opengis.net/wfs" xmlns:gml="http://www.opengis.net/gml" xmlns:ogc="http://www.opengis.net/ogc" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opengis.net/wfs http://schemas.opengis.net/wfs/1.0.0/WFS-basic.xsd                         http://mapserver.gis.umn.edu/mapserver http://aneto.oco/cgi-bin/worldwfs?SERVICE=WFS&amp;VERSION=1.0.0&amp;REQUEST=DescribeFeatureType&amp;TYPENAME=point&amp;OUTPUTFORMAT=XMLSCHEMA">
      <gml:boundedBy>
        <gml:Box srsName="EPSG:4326">
          <gml:coordinates><?=$bounds["minx"]?>,<?=$bounds["miny"]?> <?=$bounds["maxx"]?>,<?=$bounds["maxy"]?></gml:coordinates>
        </gml:Box>
      </gml:boundedBy>
<?
$strSQL="SELECT id, aprovechab, direccion, codigointe, ST_AsGML(geom,7,0) as \"gmlgeom\" FROM ideca.contenedor ORDER BY id";

$qid=$db->sql_query($strSQL);
$i=0;
$totalClases=$db->sql_field("SELECT COUNT(DISTINCT aprovechab) FROM ideca.contenedor");
error_log("totalClases:" . $totalClases);
if($totalClases>0){
	$delta=(1/$totalClases);
	$macro='';
	$distinctColor=-1;
	while($result=$db->sql_fetchrow($qid)){
		if($result["aprovechab"]!=$macro){
			$color = substr(str_shuffle('ABCDEF0123456789'), 0, 6);
		}

		if(preg_match("/^(<[^>]*>)(.*)(<[^>]*>)$/",$result["gmlgeom"],$matches)){
			$startTag=$matches[1];
			$featureMember=$matches[2];
			$endTag=$matches[3];
			echo "<gml:featureMember>\n";
			echo $startTag . "\n";
			echo "<gml:id>" . $result["id"] . "</gml:id>\n";
			echo "<ms:codigointe>" . $result["codigointe"] . "</ms:codigointe>\n";
			echo "<ms:direccion>" . $result["direccion"] . "</ms:direccion>\n";
			echo "<ms:strokecolor>#$color</ms:strokecolor>\n";
			//echo "<ms:strokecolor>#ff0000</ms:strokecolor>\n";
			echo $featureMember . "\n";
			echo $endTag . "\n";
			echo "</gml:featureMember>\n";
		}

		$i++;
		$macro=$result["aprovechab"];
	}
}
?>
</wfs:FeatureCollection>

