<?
include("application.php");
$frm=$_GET;
if(!isset($_SESSION[$CFG->sesion]["user"])){
  error_log("No existe la sesi�n.");
  die();
}

$user=$_SESSION[$CFG->sesion]["user"];
$condicion="true AND micro IS NOT NULL";
if(isset($frm["id"])) $condicion.=" AND id_micro='$frm[id]'";
if(isset($frm["id_micro"]) && $frm["id_micro"]!="") $condicion="id_micro='$frm[id_micro]'";
elseif(isset($frm["id_empresa"]) && isset($frm["dias"]) && isset($frm["id_turno"])){
	$condicion.=" AND id_micro IN (
			SELECT mf.id_micro
			FROM micros_frecuencia mf LEFT JOIN turnos t ON mf.id_turno=t.id
			WHERE t.id_empresa='$frm[id_empresa]' AND mf.dia IN($frm[dias]) AND mf.id_turno='$frm[id_turno]'
		)
	";
}
if(!isset($frm["field"])) $frm["field"]="the_geom";

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

$bounds=$db->sql_row("
  SELECT ST_XMin(ST_Boundary($frm[field])) as minx, ST_YMin(ST_Boundary($frm[field])) as miny, ST_XMax(ST_Boundary($frm[field])) as maxx, ST_YMax(ST_Boundary($frm[field])) as maxy
  FROM micros_arcos_barrido
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
$strSQL="SELECT gid as id, id_micro, gid AS id_arco, macro AS nombre, ST_AsGML($frm[field],7,0) as \"gmlgeom\" FROM micros_arcos_barrido WHERE $condicion ORDER BY id_micro";
$qid=$db->sql_query($strSQL);
$i=0;
$totalClases=$db->sql_field("SELECT COUNT(DISTINCT id_micro) FROM micros_arcos_barrido WHERE $condicion");
error_log("totalClases:" . $totalClases);
if($totalClases>0){
	$delta=(1/$totalClases);
	$macro=0;
	$distinctColor=-1;
	while($result=$db->sql_fetchrow($qid)){
		if($result["nombre"]!=$macro){
      $color = substr(str_shuffle('ABCDEF0123456789'), 0, 6);
		}

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
			echo "<ms:strokecolor>#$color</ms:strokecolor>\n";
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
		$macro=$result["nombre"];
	}
}
?>
</wfs:FeatureCollection>

