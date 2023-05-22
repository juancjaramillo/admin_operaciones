<?
include("application.php");
if(!isset($_SESSION[$CFG->sesion]["user"])){
	error_log("No existe la sesión.");
	die();
}

$user=$_SESSION[$CFG->sesion]["user"];

/**/
if(!is_array($user["id_centro"]))
{
	echo "<script>
	  window.location.href='".$CFG->wwwroot."/admin/login.php';
		</script>";
	die;
}

/*
if($user["nivel_acceso"]!=1 && $user["nivel_acceso"]!=4){//No es admin ni visor AVL global
	$condicion="v.id_centro IN (" . implode(",",$user["id_centro"]) . ") AND v.publico='t'";
	if($user["nivel_acceso"]==5) $condicion.=" AND v.id_tipo_vehiculo NOT IN (3,13)";
}
else $condicion="true";
*/


if($user["nivel_acceso"]==1 )
	$condicion="v.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') AND v.id_estado<>4 ";
else
	$condicion="v.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') AND v.publico='t' AND v.id_estado<>4";

if($user["nivel_acceso"]==5) $condicion.=" AND v.id_tipo_vehiculo NOT IN (3,13) AND v.id_estado<>4";

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
//error_log($condicion . "::" . $user["nivel_acceso"] . "::" . $user["login"]);
$bounds=$db->sql_row("SELECT min(ST_X(the_geom)) as minx, min(ST_Y(the_geom)) as miny, max(ST_X(the_geom)) as maxx, max(ST_Y(the_geom)) as maxy 
		FROM vehiculos v
		WHERE ".$condicion);
//		WHERE vehiculos.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') AND vehiculos.publico='t'");
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
	SELECT v.id,v.idgps,v.codigo,v.placa,tv,tipo, ST_X(v.the_geom) as x, ST_Y(v.the_geom) as y, v.hrposition,v.velocidad,v.tiempo,
		CASE WHEN v.tiempo > (CURRENT_DATE - INTERVAL '5 minute') THEN './images/taxi_green.png'
		ELSE './images/taxi_gray.png'
		END AS graphic,
		CASE WHEN v.id_estado_motor = 14 THEN 'Prendido' ELSE 'Apagado' END AS estado_motor,
		km_virtual, horometro_virtual,
    (
      SELECT rec.movimientos.id  FROM rec.movimientos
      WHERE rec.movimientos.id_vehiculo = v.id  AND
      rec.movimientos.inicio::date= '".date("Y-m-d")."'
      AND rec.movimientos.final is null
      ORDER BY rec.movimientos.inicio ASC LIMIT 1
    ) idmov, 
		(
			SELECT rec.tipos_desplazamientos.tipo
			FROM rec.desplazamientos LEFT JOIN rec.tipos_desplazamientos ON rec.tipos_desplazamientos.id=rec.desplazamientos.id_tipo_desplazamiento 
			LEFT  JOIN rec.movimientos ON rec.movimientos.id= rec.desplazamientos.id_movimiento
			WHERE rec.movimientos.id_vehiculo = v.id AND rec.desplazamientos.hora_inicio::date = '".date("Y-m-d")."'
			ORDER BY rec.desplazamientos.hora_inicio DESC LIMIT 1
		) as ultimodesplazamiento,
		(
		  SELECT rec.desplazamientos.hora_inicio
		  FROM rec.desplazamientos LEFT JOIN rec.tipos_desplazamientos ON
		  rec.tipos_desplazamientos.id=rec.desplazamientos.id_tipo_desplazamiento 
		  LEFT JOIN rec.movimientos ON rec.movimientos.id=
		  rec.desplazamientos.id_movimiento
		  WHERE rec.movimientos.id_vehiculo = v.id AND rec.tipos_desplazamientos.id in ('1','9')
		  AND rec.desplazamientos.id_movimiento=
			(SELECT rec.movimientos.id  FROM rec.movimientos
			WHERE rec.movimientos.id_vehiculo = v.id  AND
			rec.movimientos.inicio::date= '".date("Y-m-d")."'
			AND rec.movimientos.final is null
			ORDER BY rec.movimientos.inicio ASC LIMIT 1)
		  ORDER BY rec.desplazamientos.hora_inicio DESC LIMIT 1
		) as hsbas,
		(
		  SELECT rec.desplazamientos.hora_inicio
		  FROM rec.desplazamientos LEFT JOIN rec.tipos_desplazamientos ON
		  rec.tipos_desplazamientos.id=rec.desplazamientos.id_tipo_desplazamiento 
		  LEFT  JOIN rec.movimientos ON rec.movimientos.id=
		  rec.desplazamientos.id_movimiento
		  WHERE rec.movimientos.id_vehiculo = v.id AND
		  rec.tipos_desplazamientos.id='3'
		  AND rec.desplazamientos.id_movimiento=
			(SELECT rec.movimientos.id  FROM rec.movimientos
			WHERE rec.movimientos.id_vehiculo = v.id  AND
			rec.movimientos.inicio::date= '".date("Y-m-d")."'
			AND rec.movimientos.final is null
			ORDER BY rec.movimientos.inicio ASC LIMIT 1)
		  ORDER BY rec.desplazamientos.hora_inicio DESC LIMIT 1
		) as himic,
		(
		  SELECT rec.desplazamientos.hora_fin
		  FROM rec.desplazamientos LEFT JOIN rec.tipos_desplazamientos ON
		  rec.tipos_desplazamientos.id=rec.desplazamientos.id_tipo_desplazamiento 
		  LEFT  JOIN rec.movimientos ON rec.movimientos.id=
		  rec.desplazamientos.id_movimiento
		  WHERE rec.movimientos.id_vehiculo = v.id AND
		  rec.tipos_desplazamientos.id='3'
		  AND rec.desplazamientos.id_movimiento=
			(SELECT rec.movimientos.id  FROM rec.movimientos
			WHERE rec.movimientos.id_vehiculo = v.id  AND
			rec.movimientos.inicio::date= '".date("Y-m-d")."'
			AND rec.movimientos.final is null
			ORDER BY rec.movimientos.inicio ASC LIMIT 1)                                  
		  ORDER BY rec.desplazamientos.hora_inicio DESC LIMIT 1
		) as hfmic
	FROM vehiculos v
  LEFT join tipos_vehiculos tv ON v.id_tipo_vehiculo = tv.id
	WHERE v.tiene_gps AND $condicion
	ORDER BY v.codigo
";
$qVehiculos=$db->sql_query($strq);
while($vehiculo=$db->sql_fetchrow($qVehiculos)){
	$idRuta="";
	$codRuta="";
  
	$distRuta="-";
	if($ruta=$db->sql_row("
		SELECT r.id, r.codigo, mov.id_vehiculo,tur.turno
		FROM rec.movimientos mov LEFT JOIN micros r ON mov.id_micro=r.id
		LEFT JOIN turnos tur ON mov.id_turno=tur.id
		WHERE mov.id_vehiculo = '$vehiculo[id]' AND mov.inicio > (now() - interval '12 hours')
		ORDER BY mov.inicio DESC, mov.id DESC
		LIMIT 1
	")){
		$codRuta=$ruta["codigo"];
		$idRuta=$ruta["id"];

		if($vehiculo["x"]!="" && ($distancia=$db->sql_field("
				SELECT min(distance(GeometryFromText('POINT(".$vehiculo["x"]." ".$vehiculo["y"].")',4326),the_geom))
				FROM micros_arcos
				WHERE id_micro='$ruta[id]'
		"))){
			$distRuta=round($distancia/$CFG->metrosXgrado);
//			error_log($ruta["codigo"] . "::" . $distancia . "::" . $CFG->metrosXgrado . "::" . $distRuta);
		}

//		if($ruta["distancia"]!="") $distRuta=round($ruta["distancia"]*$CFG->metrosXgrado);
	}
?>
    <gml:featureMember>
      <ms:point fid="1">
<?/*
        <gml:boundedBy>
        	<gml:Box srsName="EPSG:4326">
        		<gml:coordinates><?=$vehiculo["x"]?>,<?=$vehiculo["y"]?> <?=$vehiculo["x"]?>,<?=$vehiculo["y"]?></gml:coordinates>
        	</gml:Box>
        </gml:boundedBy>
*/?>
        <ms:msGeometry>
        <gml:Point srsName="EPSG:4326">
          <gml:coordinates><?=$vehiculo["x"]?>,<?=$vehiculo["y"]?></gml:coordinates>
        </gml:Point>
        </ms:msGeometry>
        <ms:ogc_fid><?=$vehiculo["idgps"]?></ms:ogc_fid>
        <ms:name><?=$vehiculo["codigo"]?></ms:name>
        <ms:placa><?=$vehiculo["placa"]?></ms:placa>
        <ms:tipo><?=$vehiculo["tipo"]?></ms:tipo>
        <ms:x><?=$vehiculo["x"]?></ms:x>
        <ms:y><?=$vehiculo["y"]?></ms:y>
        <ms:ruta><?=$codRuta?></ms:ruta>
        <ms:id_ruta><?=$idRuta?></ms:id_ruta>
        <ms:turno><?=$ruta["turno"]?></ms:turno>
        <ms:hsbas><?=$vehiculo["hsbas"]?></ms:hsbas>
        <ms:himic><?=$vehiculo["himic"]?></ms:himic>
        <ms:hfmic><?=$vehiculo["hfmic"]?></ms:hfmic>
        <ms:distancia><?=$distRuta?></ms:distancia>
        <ms:hrposition><?=utf8_encode($vehiculo["hrposition"])?></ms:hrposition>
        <ms:velocidad><?=$vehiculo["velocidad"]?></ms:velocidad>
        <ms:ultimodesplazamiento><?=$vehiculo["ultimodesplazamiento"]?></ms:ultimodesplazamiento>
        <ms:id_vehiculo><?=$vehiculo["id"]?></ms:id_vehiculo>
        <ms:estado_motor><?=$vehiculo["estado_motor"]?></ms:estado_motor>
        <ms:k_v><?=round($vehiculo["km_virtual"])?></ms:k_v>
        <ms:h_v><?=round($vehiculo["horometro_virtual"],1)?></ms:h_v>
        <ms:tiempo><?=$vehiculo["tiempo"]?></ms:tiempo>a
<?
			$vehiculo["alerta"]="0";
			if($user["nivel_acceso"]!=5){
					$consultaAlerta = "SELECT al.*, t.nombre as tipo, m.codigo as ruta, c.centro, v.codigo as vehiculo
					FROM alertas al LEFT JOIN tipos_alertas t ON al.id_tipo=t.id
						LEFT JOIN micros m ON al.id_micro=m.id
						LEFT JOIN centros c ON al.id_centro=c.id
						LEFT JOIN vehiculos v ON al.id_vehiculo=v.id
					WHERE hora >= '" . date("Y-m-d H:i:s",strtotime("-8 hour")) . "'
						AND v.codigo='$vehiculo[codigo]' AND al.ack_hora IS NULL";
//				file_put_contents($CFG->dirroot.'/logtmp.log',$consultaAlerta . "\n=====\n",FILE_APPEND);

						
				if($alerta=$db->sql_row($consultaAlerta)){
					$vehiculo["graphic"]="./images/taxi_alerta.gif";
					$vehiculo["alerta"]="1";
				}
			}
?>
        <ms:graphic><?=$vehiculo["graphic"]?></ms:graphic>
        <ms:alerta><?=$vehiculo["alerta"]?></ms:alerta>
      </ms:point>
    </gml:featureMember>
<?
}
?>
</wfs:FeatureCollection>
