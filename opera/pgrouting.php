<?php
include("../application.php");
$frm=$_REQUEST;
switch(nvl($frm["mode"])){

	case "agregar_arco";
		agregar_arco($frm);
	break;

	case "save":
		save_route($frm);
		break;

	case "cargar_puntos_control":
		cargar_puntos_control($frm);
	break;

	case "saveParadero":
		newParadero($frm);
	break;

	case "borrar_punto_control":
		borrar_punto_control($frm);
	break;

	default:
		get_route($frm);
		break;
}

//	FUNCIONES:

function save_route($frm){
GLOBAL $CFG,$db;

//	preguntarLog($frm);
	
	$features=json_decode($frm["features"]);
	$qDelete=$db->sql_query("DELETE FROM micros_arcos WHERE id_micro='$frm[id_micro]'");
	foreach($features AS $feature){
		$qInsert=$db->sql_query("
			INSERT INTO micros_arcos (id_micro,id_arco,nombre,the_geom)
			VALUES(
				'$frm[id_micro]',
				'" . $feature->id . "',
				'" . trim($feature->nombre) . "',
				ST_Multi(ST_GeomFromText('" . trim($feature->geometry) . "',4326))
			)
		");
	}
/*
//	error_log(print_r($features,true));

	$qid=$db->sql_query("
		UPDATE micros
		SET geometry=ST_GeomFromText('MULTILINESTRING(" . implode(",",$features) . ")',4326)
		WHERE id='$frm[id_micro]'
	");
*/
}

function get_route($frm){
GLOBAL $CFG,$db,$dbr;
//	error_log(print_r($frm,true));

	$frm["desde"]=date("Y-m-d H:i:s",strtotime("+ 5 hours",strtotime($frm["desde"])));
	$frm["hasta"]=date("Y-m-d H:i:s",strtotime("+ 5 hours",strtotime($frm["hasta"])));

	$geojson = array(
		'type'      => 'FeatureCollection',
		'features'  => array()
	);
	error_log(print_r($frm,true));
	if(isset($frm["id_vehiculo"])){
		$dbr=new sql_db_postgres($CFG->dbhost_routing,$CFG->dbuser_routing,$CFG->dbpass_routing,$CFG->dbname_routing);
		$vehiculo=$db->sql_row("SELECT * FROM vehiculos WHERE idgps='$frm[id_vehiculo]'");
		$strSQL="
			SELECT x(gps_geom), y(gps_geom)
			FROM gps_vehi
			WHERE tiempo BETWEEN '$frm[desde]' AND '$frm[hasta]' AND id_vehi='$vehiculo[idgps]' AND gps_geom IS NOT NULL
			ORDER BY tiempo
		";
		//error_log($strSQL);
		$qPuntos=$db->sql_query($strSQL);
		// Return route as GeoJSON

		$i=0;
		$numArco=0;
		while($punto=$db->sql_fetchrow($qPuntos)){
	//		error_log(print_r($punto,true));
			if($i>0){
				$startPoint = array($punto_anterior["x"], $punto_anterior["y"]);
				$endPoint = array($punto["x"], $punto["y"]);

				// Find the nearest edge
				$startEdge = findNearestEdge($startPoint);
				$endEdge   = findNearestEdge($endPoint);
				if($startEdge["gid"] == $endEdge["gid"]){//Es el mismo vértice
					$feature = array(
						'type' => 'Feature',
						'geometry' => json_decode($startEdge['geojson'], true),
						'crs' => array(
							'type' => 'EPSG',
							'properties' => array('code' => '4326')
						),
						'properties' => array(
							'id' => $startEdge['gid'],
							'name' => $startEdge['name']
						)
					);

					// Add feature array to feature collection array
					if(sizeof($geojson['features'])==0 || $feature["properties"]["id"]!=$geojson['features'][sizeof($geojson['features'])-1]["properties"]["id"]){
						$numArco++;
						$feature["properties"]["num"]=$numArco;
						array_push($geojson['features'], $feature);
					}

				}
				elseif(
					$startEdge["source"]==$endEdge["source"] ||
					$startEdge["source"]==$endEdge["target"] ||
					$startEdge["target"]==$endEdge["source"] ||
					$startEdge["target"]==$endEdge["target"]
				){//Está conectado con el anterior
					$feature = array(
						'type' => 'Feature',
						'geometry' => json_decode($endEdge['geojson'], true),
						'crs' => array(
							'type' => 'EPSG',
							'properties' => array('code' => '4326')
						),
						'properties' => array(
							'id' => $startEdge['gid'],
							'name' => $startEdge['name']
						)
					);

					// Add feature array to feature collection array
					if(sizeof($geojson['features'])==0 || $feature["properties"]["id"]!=$geojson['features'][sizeof($geojson['features'])-1]["properties"]["id"]){
						$numArco++;
						$feature["properties"]["num"]=$numArco;
						array_push($geojson['features'], $feature);
					}

				}
				else{
	//				error_log(print_r($startEdge,true));
	//				error_log(print_r($endEdge,true));

					$sql = "
						SELECT w.gid,ST_AsGeoJSON(w.the_geom) AS geojson, w.name
						FROM shortest_path(
							'SELECT gid as id, source,target,length AS cost, reverse_cost FROM ways',
							".$startEdge['source'].",
							".$endEdge['target'].",
							true,true
						) as sp LEFT JOIN ways w ON sp.edge_id=w.gid
						WHERE w.gid IS NOT NULL
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
									'name' => $edge['name']
									)
								);

						// Add feature array to feature collection array
						if(sizeof($geojson['features'])==0 || $feature["properties"]["id"]!=$geojson['features'][sizeof($geojson['features'])-1]["properties"]["id"]){
							$numArco++;
							$feature["properties"]["num"]=$numArco;
							array_push($geojson['features'], $feature);
						}
					}
				}
			}
			$punto_anterior=$punto;
			$i++;
		}
	}

	// Return routing result
	header('Content-type: application/json',true);
	echo json_encode($geojson);

}


// FUNCTION findNearestEdge
function findNearestEdge($lonlat) {

	GLOBAL $dbr;

	// Connect to database
	$sql = "SELECT gid, source, target, ST_AsGeoJSON(the_geom) as geojson, name,
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



function agregar_arco($frm)
{
	GLOBAL $CFG,$db,$dbr;

	$dbr=new sql_db_postgres($CFG->dbhost_routing,$CFG->dbuser_routing,$CFG->dbpass_routing,$CFG->dbname_routing);
	$sql = "SELECT gid, source, target, ST_AsGeoJSON(ST_Transform(the_geom,900913)) as geojson, name,
		distance(the_geom, GeometryFromText(
					'POINT(".$frm["x"]." ".$frm["y"].")', 4326)) AS dist 
		FROM ways  
		WHERE the_geom && setsrid(
				'BOX3D(".($frm["x"]-0.1)." 
					".($frm["y"]-0.1).", 
					".($frm["x"]+0.1)." 
					".($frm["y"]+0.1).")'::box3d, 4326) 
		ORDER BY dist LIMIT 1";
	$edge=$dbr->sql_row($sql);
	
	$geojson = '
	{
		"type":"Feature",
		"features":
		[{
			"type":"Feature",
			"geometries" : ['.$edge["geojson"].'],
			"properties":
			{
				"id":"'.$edge['gid'].'",
				"name":"'.$edge['name'].'",
				"num":"'.$frm["orden"].'"
			},
			"crs":
			{
				"type":"EPSG",
				"properties":{"code":"900913"}
			}
		}],
		"properties":
		{
			"id":"'.$edge['gid'].'",
			"name":"'.$edge['name'].'",
			"num":"'.$frm["orden"].'"
		},
		"geometry":
		{
			"type":"GeometryCollection",
			"geometries" : ['.$edge["geojson"].']
		},
		"crs":
		{
			"type":"EPSG",
			"properties":{"code":"900913"}
		}
	}';

	echo $geojson;
}


function preguntarLog($dato)
{
	global $CFG;

	if(is_array($dato))
	{
		foreach($dato as $key => $val)
		{
			file_put_contents($CFG->dirroot.'/mtto/ver.log',$key." => ".$val."\n",FILE_APPEND);
		}
	}
	else
		file_put_contents($CFG->dirroot.'/mtto/ver.log',$dato."\n",FILE_APPEND);


	file_put_contents($CFG->dirroot.'/mtto/ver.log',"=====\n",FILE_APPEND);
}


function cargar_puntos_control($frm)
{
	GLOBAL $db,$CFG, $ME;

	$geojson = array(
		'type'      => 'FeatureCollection',
		'features'  => array()
	);
			
	$strSQL="SELECT ST_AsGeoJSON(ST_Transform(the_geom,900913)) as geojson, direccion, hora, id
			FROM micros_puntos_control
			WHERE id_micro = '".$frm["id_micro"]."'
			ORDER BY hora";
		//preguntarLog($strSQL);
		$qPuntos=$db->sql_query($strSQL);
		// Return route as GeoJSON

		while($punto=$db->sql_fetchrow($qPuntos)){
	//		error_log(print_r($punto,true));
					
					$feature = array(
						"geometry" =>  json_decode($punto['geojson'], true),
						"type" => "Feature",
						"properties" => array(
								'id_punto' => $punto['id'],
								'direccion' => $punto['direccion'],
								'hora' => $punto['hora']
								),
						"id"=> $punto['id']
							);
					array_push($geojson['features'], $feature);
		}

	// Return routing result
	header('Content-type: application/json',true);
	echo json_encode($geojson);
}


function newParadero($frm){
GLOBAL $db,$CFG, $ME;

  $paradero["id_micro"]=$frm["id_micro"];
  $paradero["direccion"]=$frm["nombre"];
  $paradero["hora"]=$frm["hora"];
  $paradero["the_geom"]="ST_setsrid(ST_geomfromtext('POINT($frm[x] $frm[y])'),4326)";

	//preguntarLog($frm);
	//preguntarLog($paradero);

	$consulta = "INSERT INTO micros_puntos_control (id_micro,direccion,hora,the_geom)
    VALUES('".$frm["id_micro"]."','".$frm["nombre"]."','".$frm["hora"]."',ST_setsrid(ST_geomfromtext('POINT(".$frm["x"]." ".$frm["y"].")'),4326))";
	//preguntarLog($consulta);

  $db->sql_query($consulta);
	cargar_puntos_control($frm);
}

function borrar_punto_control($frm)
{
	GLOBAL $db,$CFG, $ME;
	
	$qid = $db->sql_query("DELETE FROM micros_puntos_control WHERE id=".$frm["id"]);
}













?>
