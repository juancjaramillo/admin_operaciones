<?
include("application.php");
if(!isset($_SESSION[$CFG->sesion]["user"])){
	error_log("No existe la sesión.");
	die();
}

$user=$_SESSION[$CFG->sesion]["user"];

if(!is_array($user["id_centro"]))
{
	echo "<script>
	  window.location.href='".$CFG->wwwroot."/admin/login.php';
		</script>";
	die;
}

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
//error_log($condicion . "::" . $user["nivel_acceso"] . "::" . $user["login"]);

$condicion = "";
$id_categoria = nvl($_GET["id_categoria"]);
if($id_categoria != "")
	$condicion = " AND id_categoria='".$_GET["id_categoria"]."'";

$mazimos = $db->sql_row("SELECT min(ST_X(the_geom)) as minx, min(ST_Y(the_geom)) as miny, max(ST_X(the_geom)) as maxx, max(ST_Y(the_geom)) as maxy 
	FROM puntos_interes
	WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."')".$condicion);
?>
<wfs:FeatureCollection xmlns:ms="http://mapserver.gis.umn.edu/mapserver" xmlns:wfs="http://www.opengis.net/wfs" xmlns:gml="http://www.opengis.net/gml" xmlns:ogc="http://www.opengis.net/ogc" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opengis.net/wfs http://schemas.opengis.net/wfs/1.0.0/WFS-basic.xsd                         http://mapserver.gis.umn.edu/mapserver http://aneto.oco/cgi-bin/worldwfs?SERVICE=WFS&amp;VERSION=1.0.0&amp;REQUEST=DescribeFeatureType&amp;TYPENAME=point&amp;OUTPUTFORMAT=XMLSCHEMA">

      <gml:boundedBy>
      	<gml:Box srsName="EPSG:4326">
      		<gml:coordinates><?=$mazimos["minx"]?>,<?=$mazimos["miny"]?> <?=$mazimos["maxx"]?>,<?=$mazimos["maxy"]?></gml:coordinates>
      	</gml:Box>
      </gml:boundedBy>
<?
$qidPC = $db->sql_query("SELECT id, punto, radio, ST_X(the_geom) as x, ST_Y(the_geom) as y, './files/categorias_puntos_interes/icono/'||id_categoria as graphic
	FROM puntos_interes
	WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."')".$condicion."
	ORDER BY id_categoria");
while($punto = $db->sql_fetchrow($qidPC))
{?>
	<gml:featureMember>
		<ms:point fid="1">
			<ms:msGeometry>
				<gml:Point srsName="EPSG:4326">
					<gml:coordinates><?=$punto["x"]?>,<?=$punto["y"]?></gml:coordinates>
				</gml:Point>
			</ms:msGeometry>
			<ms:name><?=$punto["punto"]?></ms:name>
			<ms:graphic><?=$punto["graphic"]?></ms:graphic>
		</ms:point>
	</gml:featureMember>
<?
}
?>
</wfs:FeatureCollection>
