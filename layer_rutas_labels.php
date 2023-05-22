<?
include("application.php");
$frm=$_GET;
if(!isset($_SESSION[$CFG->sesion]["user"])){
	error_log("No existe la sesión.");
	die();
}

$user=$_SESSION[$CFG->sesion]["user"];

$condicion="true";
if(isset($frm["id"])) $condicion.=" AND id_micro='$frm[id]'";
if(isset($frm["id_micro"]) && $frm["id_micro"]!="") $condicion="id_micro='$frm[id_micro]'";
elseif(isset($frm["id_empresa"]) && isset($frm["dias"]) && isset($frm["id_turno"])){
	$condicion.=" AND ma.id_micro IN (
			SELECT mf.id_micro
			FROM micros_frecuencia mf LEFT JOIN turnos t ON mf.id_turno=t.id
			WHERE t.id_empresa='$frm[id_empresa]' AND mf.dia IN($frm[dias]) AND mf.id_turno='$frm[id_turno]'
		)
	";
}
if(!isset($frm["field"])) $frm["field"]="ma.the_geom";


header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

$bounds=$db->sql_row("
  SELECT ST_XMin(ST_Boundary($frm[field])) as minx, ST_YMin(ST_Boundary($frm[field])) as miny, ST_XMax(ST_Boundary($frm[field])) as maxx, ST_YMax(ST_Boundary($frm[field])) as maxy
  FROM micros_arcos ma
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
$strSQL="
SELECT foo.id_micro, m.codigo, ST_X(foo.the_geom) as x, ST_Y(foo.the_geom) as y
FROM (
	SELECT ma.id_micro,ST_CENTROID(ST_COLLECT(ma.the_geom)) as the_geom
	FROM micros_arcos ma WHERE $condicion
	GROUP BY ma.id_micro
) AS foo LEFT JOIN micros m ON foo.id_micro=m.id
WHERE m.id IS NOT NULL
ORDER BY m.id
";
error_log($strSQL);
$qid=$db->sql_query($strSQL);
$i=0;
$totalClases=$db->sql_numrows($qid);
error_log("totalClases:" . $totalClases);
$delta=(1/$totalClases);
$id_micro=0;
$distinctColor=-1;
while($micro=$db->sql_fetchrow($qid)){
	$distinctColor++;
	$hue=$distinctColor*$delta;
	$arrayColorRGB=hsv2rgb($hue,1,1);
	$hexColor=rgb2hex($arrayColorRGB[0],$arrayColorRGB[1],$arrayColorRGB[2]);
	error_log($micro["id_micro"] . "=>" . $hue . "=>" . $arrayColorRGB[0] . "," . $arrayColorRGB[1] . "," . $arrayColorRGB[2] . "=>" . $hexColor);
?>
    <gml:featureMember>
      <ms:point fid="1">
        <ms:msGeometry>
        <gml:Point srsName="EPSG:4326">
          <gml:coordinates><?=$micro["x"]?>,<?=$micro["y"]?></gml:coordinates>
        </gml:Point>
        </ms:msGeometry>
        <ms:ogc_fid><?=$micro["id_micro"]?></ms:ogc_fid>
        <ms:name><?=$micro["codigo"]?></ms:name>
				<ms:strokeColor>#<?=$hexColor?></ms:strokeColor>
      </ms:point>
    </gml:featureMember>
<?
}
?>
</wfs:FeatureCollection>
