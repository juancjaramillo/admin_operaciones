<?php
include("../application.php");
$frm=$_REQUEST;
switch(nvl($frm["mode"])){
	case "save":
		save_route($frm);
		break;
	default:
		get_route($frm);
		break;
}

//	FUNCIONES:

function save_route($frm){
GLOBAL $CFG,$db;

	error_log(print_r($frm,true));
	$qid=$db->sql_query("
		UPDATE micros
		SET geometry=ST_GeomFromText('MULTILINESTRING(($frm[points]))',4326)
		WHERE id='$frm[id_micro]'
	");
}

function get_route($frm){
GLOBAL $CFG,$db,$dbr;
	error_log(print_r($frm,true));

	$dbr=new sql_db_postgres($CFG->dbhost_routing,$CFG->dbuser_routing,$CFG->dbpass_routing,$CFG->dbname_routing);
	$vehiculo=$db->sql_row("SELECT * FROM vehiculos WHERE id='$frm[id_vehiculo]'");
	$strSQL="
		SELECT x(gps_geom), y(gps_geom)
		FROM gps_vehi
		WHERE tiempo BETWEEN '$frm[desde]' AND '$frm[hasta]' AND id_vehi='$vehiculo[idgps]' AND gps_geom IS NOT NULL
		ORDER BY id
	";
	error_log($strSQL);
	$qPuntos=$db->sql_query($strSQL);
	// Return route as GeoJSON
	$geojson = array(
		'type'      => 'FeatureCollection',
		'features'  => array()
	); 

	$i=0;
	while($punto=$db->sql_fetchrow($qPuntos)){
		error_log(print_r($punto,true));
		if($i>0){
			$startPoint = array($punto_anterior["x"], $punto_anterior["y"]);
			$endPoint = array($punto["x"], $punto["y"]);

			// Find the nearest edge
			$startEdge = findNearestEdge($startPoint);
			$endEdge   = findNearestEdge($endPoint);

			$sql = "
				SELECT gid, ST_AsGeoJSON(the_geom) AS geojson, length(the_geom) AS length 
				FROM ways
				WHERE gid IN
				(
					SELECT edge_id
					FROM shortest_path(
						'SELECT gid as id, source,target,length AS cost, reverse_cost FROM ways',
						".$startEdge['source'].",
						".$endEdge['target'].",
						true,true
					)
				)
			";
			$qEdges = $dbr->sql_query($sql); 
			// Add edges to GeoJSON array
			while($edge=$dbr->sql_fetchrow($qEdges)) {  
				$feature = array(
						'type' => 'Feature',
						'geometry' => json_decode($edge['geojson'], true),
						'crs' => array(
							'type' => 'EPSG',
							'properties' => array('code' => '4326')
							),
						'properties' => array(
							'id' => $edge['gid'],
							'length' => $edge['length']
							)
						);

				// Add feature array to feature collection array
				if($feature!=$geojson['features'][sizeof($geojson['features'])-1]) array_push($geojson['features'], $feature);
			}
		}
		$punto_anterior=$punto;
		$i++;
	}

	// Return routing result
	header('Content-type: application/json',true);
	echo json_encode($geojson);

}














// FUNCTION findNearestEdge
function findNearestEdge($lonlat) {

	GLOBAL $dbr;

	// Connect to database

	$sql = "SELECT gid, source, target, the_geom, 
		distance(the_geom, GeometryFromText(
					'POINT(".$lonlat[0]." ".$lonlat[1].")', 4326)) AS dist 
		FROM ways  
		WHERE the_geom && setsrid(
				'BOX3D(".($lonlat[0]-0.1)." 
					".($lonlat[1]-0.1).", 
					".($lonlat[0]+0.1)." 
					".($lonlat[1]+0.1).")'::box3d, 4326) 
		ORDER BY dist LIMIT 1";

	$edge=$dbr->sql_row($sql);
/*
	$edge['gid']      = pg_fetch_result($query, 0, 0);  
	$edge['source']   = pg_fetch_result($query, 0, 1);  
	$edge['target']   = pg_fetch_result($query, 0, 2);  
	$edge['the_geom'] = pg_fetch_result($query, 0, 3);  

	// Close database connection
	pg_close($con);
*/
	return $edge;
}

?>
