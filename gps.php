<?
include("application.php");
$frm=$_GET;
if(!isset($frm["id_vehi"])){
	error_log("No viene la variable id_vehi");
	die();
}
if(!isset($frm["desde"]) && !isset($frm["hasta"])) $condicionFecha="tiempo>=(now() - INTERVAL '1 HOURS')";
elseif(isset($frm["desde"]) && $frm["desde"]=="turno"){
	$vehi=$db->sql_row("
		SELECT v.*,c.id_empresa
		FROM vehiculos v LEFT JOIN centros c ON v.id_centro=c.id
		WHERE v.idgps='$frm[id_vehi]'
	");
	$hora=date("H:i:s");
	if($turno=$db->sql_row("
		SELECT *,('$hora'-hora_inicio) as diff
		FROM turnos
		WHERE id_empresa='$vehi[id_empresa]' AND ('$hora'-hora_inicio)>'00:00:00'
		ORDER BY ('$hora'-hora_inicio)
	")){
		$fecha_desde=date("Y-m-d H:i:s",strtotime("+ 0 hours",strtotime(date("Y-m-d $turno[hora_inicio]"))));
	}
	else{//Es la madrugada => buscar el último turno
		$turno=$db->sql_row("SELECT * FROM turnos WHERE id_empresa='$vehi[id_empresa]' ORDER BY hora_inicio DESC LIMIT 1");
		//Día anterior => -24 horas + 5 horas GMT = -19
		$fecha_desde=date("Y-m-d H:i:s",strtotime("-24 hours",strtotime(date("Y-m-d $turno[hora_inicio]"))));
	}
	$condicionFecha="tiempo>='$fecha_desde'";
}
else{
	$fecha_desde=date("Y-m-d H:i:s",strtotime("+ 0 hours",strtotime($frm["desde"])));
	$fecha_hasta=date("Y-m-d H:i:s",strtotime("+ 0 hours",strtotime($frm["hasta"])));
	$condicionFecha="tiempo BETWEEN '$fecha_desde' AND '$fecha_hasta'";
}
$id_vehi=$frm["id_vehi"];
if(!is_numeric($id_vehi)){  // ajuste por cambiar idgps a la placa y no siempre viene en id_vehi la placa
  if($id_vehi=="") $id_vehi="0";
  else {
    $qid=$db->sql_query("SELECT * FROM vehiculos WHERE idgps = '".$id_vehi."' ");
    $equipo=$db->sql_fetchrow($qid);
    $id_vehi = $equipo["id"];
  }
}
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

$bounds=$db->sql_row("SELECT min(ST_X(the_geom)) as minx, min(ST_Y(the_geom)) as miny, max(ST_X(the_geom)) as maxx, max(ST_Y(the_geom)) as maxy FROM vehiculos;");
echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
?>
<wfs:FeatureCollection xmlns:ms="http://mapserver.gis.umn.edu/mapserver" xmlns:wfs="http://www.opengis.net/wfs" xmlns:gml="http://www.opengis.net/gml" xmlns:ogc="http://www.opengis.net/ogc" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opengis.net/wfs http://schemas.opengis.net/wfs/1.0.0/WFS-basic.xsd                         http://mapserver.gis.umn.edu/mapserver http://aneto.oco/cgi-bin/worldwfs?SERVICE=WFS&amp;VERSION=1.0.0&amp;REQUEST=DescribeFeatureType&amp;TYPENAME=point&amp;OUTPUTFORMAT=XMLSCHEMA">

      <gml:boundedBy>
      	<gml:Box srsName="EPSG:4326">
      		<gml:coordinates><?=$bounds["minx"]?>,<?=$bounds["miny"]?> <?=$bounds["maxx"]?>,<?=$bounds["maxy"]?></gml:coordinates>
      	</gml:Box>
      </gml:boundedBy>
<?
$strq = "
	SELECT id,tiempo,rumbo,velocidad,ST_X(gps_geom) as x, ST_Y(gps_geom) as y,
		CASE WHEN velocidad='0' THEN 'stop.png'
			WHEN velocidad <='10' THEN 'icono.php?color=0,153,255'
			WHEN velocidad <='20' THEN 'icono.php?color=0,153,51'
			WHEN velocidad <='30' THEN 'icono.php?color=0,235,0'
			WHEN velocidad <='40' THEN 'icono.php?color=204,255,0'
			WHEN velocidad <='50' THEN 'icono.php?color=235,255,0'
			WHEN velocidad <='60' THEN 'icono.php?color=255,255,51'
			WHEN velocidad <='80' THEN 'icono.php?color=255,153,0'
			ELSE 'icono.php?color=255,0,0'
		END as icono
	FROM gps_vehi
	where $condicionFecha AND id_vehi='$id_vehi' AND gps_geom IS NOT NULL
	ORDER BY tiempo
";
$qVehiculos=$db->sql_query($strq);
while($geom=$db->sql_fetchrow($qVehiculos)){
?>
    <gml:featureMember>
      <ms:point fid="1">
        <ms:msGeometry>
        <gml:Point srsName="EPSG:4326">
          <gml:coordinates><?=$geom["x"]?>,<?=$geom["y"]?></gml:coordinates>
        </gml:Point>
        </ms:msGeometry>
        <ms:ogc_fid>1</ms:ogc_fid>
        <ms:rumbo><?=$geom["rumbo"]?></ms:rumbo>
        <ms:velocidad><?=$geom["velocidad"]?></ms:velocidad>
        <ms:hora><?=date("H:i:s",strtotime($geom["tiempo"]))?></ms:hora>
        <ms:graphic>images/<?=$geom["icono"]?></ms:graphic>
      </ms:point>
    </gml:featureMember>
<?
}
?>
</wfs:FeatureCollection>
