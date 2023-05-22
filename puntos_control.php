<?
include("application.php");

$frm=$_GET;
/*
if(!isset($_SESSION[$CFG->sesion]["user"])){
  error_log("No existe la sesión.");
  die();
}
*/

$user=$_SESSION[$CFG->sesion]["user"];
$condicion = "a.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."')";
if(isset($frm["id_micro"])) $condicion.=" AND p.id_micro='$frm[id_micro]'";

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

$bounds=$db->sql_row("SELECT min(ST_X(p.the_geom)) as minx, min(ST_Y(p.the_geom)) as miny, max(ST_X(p.the_geom)) as maxx, max(ST_Y(p.the_geom)) as maxy 
	FROM micros_puntos_control p
	LEFT JOIN micros m ON m.id = p.id_micro
	LEFT JOIN ases a ON a.id = m.id_ase
	WHERE ".$condicion);

echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
?>
<wfs:FeatureCollection xmlns:ms="http://mapserver.gis.umn.edu/mapserver" xmlns:wfs="http://www.opengis.net/wfs" xmlns:gml="http://www.opengis.net/gml" xmlns:ogc="http://www.opengis.net/ogc" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opengis.net/wfs http://schemas.opengis.net/wfs/1.0.0/WFS-basic.xsd                         http://mapserver.gis.umn.edu/mapserver http://aneto.oco/cgi-bin/worldwfs?SERVICE=WFS&amp;VERSION=1.0.0&amp;REQUEST=DescribeFeatureType&amp;TYPENAME=point&amp;OUTPUTFORMAT=XMLSCHEMA">

<?/*
      <gml:boundedBy>
        <gml:Box srsName="EPSG:4326">
          <gml:coordinates><?=$bounds["minx"]?>,<?=$bounds["miny"]?> <?=$bounds["maxx"]?>,<?=$bounds["maxy"]?></gml:coordinates>
        </gml:Box>
      </gml:boundedBy>
<?
*/

$strSQL="SELECT p.id, p.direccion, m.codigo as ruta, '".$CFG->wwwroot."/images/alert.png' as graphic, ST_X(p.the_geom) as x, ST_Y(p.the_geom) as y, p.hora
	FROM micros_puntos_control p
	LEFT JOIN micros m ON m.id = p.id_micro
	LEFT JOIN ases a ON a.id = m.id_ase
	WHERE ".$condicion."
	ORDER BY p.hora";
file_put_contents($CFG->dirroot.'/mtto/ver.log',$strSQL."\n",FILE_APPEND);


//echo $strSQL;
$qVehiculos=$db->sql_query($strSQL);
while($paradero=$db->sql_fetchrow($qVehiculos)){
?>
    <gml:featureMember>
      <ms:point fid="1">
        <ms:msGeometry>
        <gml:Point srsName="EPSG:4326">
          <gml:coordinates><?=$paradero["x"]?>,<?=$paradero["y"]?></gml:coordinates>
        </gml:Point>
        </ms:msGeometry>
        <ms:ogc_fid><?=$paradero["id"]?></ms:ogc_fid>
			<ms:id><?=$paradero["id_punto"]?></ms:id>
          <ms:direccion><?=$paradero["direccion"]?></ms:direccion>
			<ms:hora><?=$paradero["hora"]?></ms:hora>
        <ms:ruta><?=$paradero["ruta"]?></ms:ruta>
        <ms:graphic><?=$paradero["graphic"]?></ms:graphic>
      </ms:point>
    </gml:featureMember>
<?
}
?>
</wfs:FeatureCollection>