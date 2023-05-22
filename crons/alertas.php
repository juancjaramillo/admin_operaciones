<?
include(dirname(__FILE__) . "/../application.php");
ini_set("error_log",dirname(__FILE__) . "/log_alertas.log");
$qRutas=$db->sql_query("
	SELECT mf.*, m.codigo, ases.id_centro,centros.centro
	FROM micros_frecuencia mf LEFT JOIN micros m ON mf.id_micro=m.id
		LEFT JOIN ases ON m.id_ase=ases.id
		LEFT JOIN centros ON ases.id_centro=centros.id
	WHERE mf.dia='" . date("N") . "' AND '" . date("H:i:s") . "' BETWEEN mf.hora_inicio AND CASE WHEN (mf.hora_inicio>mf.hora_fin) THEN '23:59:59' ELSE mf.hora_fin END
		AND m.id_servicio='1' AND (m.fecha_hasta IS NULL OR m.fecha_hasta>'" . date("Y-m-d") . "')
	ORDER BY ases.id_centro,m.codigo
");

while($ruta=$db->sql_fetchrow($qRutas)){
	error_log($ruta["id_centro"] . "::" . $ruta["codigo"] . "::");
	//Revisar si ya se hizo el despacho o no.
	//	echo $ruta["codigo"] . "\n";
	if($mov=$db->sql_row("
		SELECT mov.*, v.codigo, ST_X(v.the_geom) as x, ST_Y(v.the_geom) as y, v.velocidad
		FROM rec.movimientos mov LEFT JOIN vehiculos v ON mov.id_vehiculo=v.id
		WHERE mov.inicio>='" . date("Y-m-d 00:00:00") . "' AND mov.id_micro='$ruta[id_micro]'
	")){
		//echo $mov["codigo"] . "::" . $mov["x"] . ", " . $mov["y"] . "\n";
		error_log("Ya inició ruta.\n");
		//Mirar si tenía una alerta, para darle un ACK automático
		if($alerta=$db->sql_row("
			SELECT *
			FROM alertas
			WHERE hora::date='" . date("Y-m-d") . "'
				AND id_centro='$ruta[id_centro]'
				AND id_tipo='1' /*Ruta sin iniciar*/
				AND id_micro='$ruta[id_micro]'
				AND ack_hora IS NULL
			")){
			$qUpdate=$db->sql_query("UPDATE alertas SET ack_id_motivo='1', ack_id_persona='39', ack_hora=now() WHERE id='$alerta[id]'");
			error_log("\tDesactivando alerta.\n");
			}
		//Verificar si el vehículo está lejos de la ruta... (John) Y si en desplazamientos esta en ruta y esta en ruta
		if($desp=$db->sql_row("
			SELECT desp.*
			FROM rec.desplazamientos desp
			WHERE desp.id_movimiento = '$mov[id]'
				AND	desp.id_tipo_desplazamiento in (3) 
				AND desp.hora_inicio>='" . date("Y-m-d 00:00:00") . "' 
				AND desp.hora_fin is null 
		")){
				if($mov["x"]!="" && $mov["y"]!=""){
					$strQuery="
					SELECT min(distance(GeometryFromText('POINT(".$mov["x"]." ".$mov["y"].")',4326),the_geom))
					FROM micros_arcos
					WHERE id_micro='$ruta[id_micro]'
					";
					//echo $mov["codigo"] . "\n";
					//echo "====\n" . $strQuery . "====\n";
					$dist=$db->sql_field($strQuery);
					$distRuta=round($dist/$CFG->metrosXgrado);
				}
				else $distRuta=0;
					//echo "distRuta=" .$distRuta . "\n";
		}
		else $distRuta=0;

		if($distRuta>2000 || ($mov["velocidad"] > 50 and $mov["velocidad"]<=110)){  //Está fuera de ruta o va a más de 85 k/h
			if ($mov["velocidad"] > 50 and $mov["velocidad"]<=110) {
				$strMail[$ruta[id_centro]].= "\t $ruta[centro] - El vehículo $mov[codigo] Excedio el Límite de Velocidad (60 km/h), va a $mov[velocidad] k/h. \n";
				$qInsert=$db->sql_query("
						INSERT INTO alertas (hora,id_centro,id_tipo,id_micro,id_vehiculo,observaciones)
						VALUES (now(),'$ruta[id_centro]','4' /*Exceso de Velocidad*/,'$ruta[id_micro]','$mov[id_vehiculo]','Excedio el Límite de Velocidad (60 km/h), registro $mov[velocidad] k/h.')
					");
				}
			if($alerta=$db->sql_row("
				SELECT *
				FROM alertas
				WHERE hora::date='" . date("Y-m-d") . "'
					AND id_centro='$ruta[id_centro]'
					AND id_tipo='3' /*Fuera de ruta*/
					AND id_micro='$ruta[id_micro]'
					AND id_vehiculo='$mov[id_vehiculo]'
					AND ack_hora IS NULL
			")){
				error_log("\tYa hay una alerta.\n");
			}

			else{
				/* Aquí habría que revisar, porque puede ser que le den ACK y luego se vuelve a insertar...*/
				if ($distRuta<10000) {
					$strMail[$ruta['id_centro']].= "\t $ruta[centro] - El vehículo $mov[codigo] se encuentra a $distRuta metros de la ruta $ruta[codigo] pudo ser que no Reportó Fin de Ruta. \n";
					error_log("\tNo hay una alerta.\n");
					$qInsert=$db->sql_query("
						INSERT INTO alertas (hora,id_centro,id_tipo,id_micro,id_vehiculo)
						VALUES (now(),'$ruta[id_centro]','3' /*Fuera de ruta*/,'$ruta[id_micro]','$mov[id_vehiculo]')
					");
				}
			}
		}
		else{
			//Está dentro de la ruta
			if($alerta=$db->sql_row("
				SELECT *
				FROM alertas
				WHERE hora::date='" . date("Y-m-d") . "'
					AND id_centro='$ruta[id_centro]'
					AND id_tipo='3' /*Fuera de ruta*/
					AND id_micro='$ruta[id_micro]'
					AND id_vehiculo='$mov[id_vehiculo]'
					AND ack_hora IS NULL
			")){
				error_log("\tYa hay una alerta.\n");
				$qUpdate=$db->sql_query("UPDATE alertas SET ack_id_persona='0', ack_hora=now() WHERE id='$alerta[id]'");
			}
		}
	}
	else{
		error_log("No ha iniciado ruta\n");
		//Mirar si ya tiene una alerta por este motivo
		if($alerta=$db->sql_row("
			SELECT *
			FROM alertas
			WHERE hora::date='" . date("Y-m-d") . "'
				AND id_centro='$ruta[id_centro]'
				AND id_tipo='1' /*Ruta sin iniciar*/
				AND id_micro='$ruta[id_micro]'
		")){
			error_log("\tYa hay una alerta.\n");
		}
		else{
			error_log("\tNo hay una alerta.\n");
			$strMail[$ruta["id_centro"]].= "\t$ruta[centro] -  ".date("Y-m-d H:i:s", time())." La Ruta $ruta[codigo] que debia salir a las $ruta[hora_inicio] No se ha iniciado o no reportó inicio a Radio-Control.\n";
			$qInsert=$db->sql_query("
				INSERT INTO alertas (hora,id_centro,id_tipo,id_micro)
				VALUES (now(),'$ruta[id_centro]','1' /*Ruta sin iniciar*/,'$ruta[id_micro]')
			");
		}
	}
//	print_r($ruta);
}

$qRutas=$db->sql_query("
	SELECT mf.hora_fin + INTERVAL '180 MINUTE' as maxhfin, mf.*, m.codigo, ases.id_centro,centros.centro
	FROM micros_frecuencia mf LEFT JOIN micros m ON mf.id_micro=m.id
		LEFT JOIN ases ON m.id_ase=ases.id
		LEFT JOIN centros ON ases.id_centro=centros.id
	WHERE mf.dia='" . date("N") . "' AND '" . date("H:i:s") . "'- INTERVAL '480 MINUTE' BETWEEN mf.hora_inicio AND CASE WHEN (mf.hora_inicio>mf.hora_fin) THEN '23:59:59' ELSE mf.hora_fin END
		AND m.id_servicio='1' AND (m.fecha_hasta IS NULL OR m.fecha_hasta>'" . date("Y-m-d") . "')
	ORDER BY ases.id_centro,m.codigo
");
while($ruta=$db->sql_fetchrow($qRutas)){
	error_log($ruta["id_centro"] . "::" . $ruta["codigo"] . "::");
	// print_r($ruta);
	//Revisar si ya se hizo fin Ruta
	//	echo $ruta["codigo"] . "\n";
	if($mov=$db->sql_row("
		SELECT mov.*, v.codigo, ST_X(v.the_geom) as x, ST_Y(v.the_geom) as y, v.velocidad
			FROM rec.movimientos mov LEFT JOIN vehiculos v ON mov.id_vehiculo=v.id
			LEFT JOIN rec.desplazamientos des ON mov.id=des.id_movimiento
			WHERE des.id_tipo_desplazamiento='3' AND
			(des.hora_fin::time>='$ruta[maxhfin]' or 
				(des.hora_fin is null and '" . date("Y-m-d 00:00:00") . "' >='$ruta[maxhfin]')
			)")
	){
		error_log("No ha terminado Ruta o no ha Reportado Fin de Ruta.\n");
		//Mirar si tenía una alerta, para darle un ACK automático
		if($alerta=$db->sql_row("
			SELECT *
			FROM alertas
			WHERE hora::date='" . date("Y-m-d") . "'
				AND id_centro='$ruta[id_centro]'
				AND id_tipo='2' /*Retraso en Ruta*/
				AND id_micro='$ruta[id_micro]'
				AND ack_hora IS NULL
			")){
				$qUpdate=$db->sql_query("UPDATE alertas SET ack_id_motivo='2', ack_id_persona='6095', ack_hora=now() WHERE id='$alerta[id]'");
				error_log("\tDesactivando alerta.\n");
			}
		$qInsert=$db->sql_query("
				INSERT INTO alertas (hora,id_centro,id_tipo,id_micro)
				VALUES (now(),'$ruta[id_centro]','2' /*Retraso en Ruta*/,'$ruta[id_micro]')
			");
	}
}


#Print_r($strMail);
if (isset($strMail)){
	while ($idcentro = key($strMail)) {
		$qCorreos=$db->sql_query("
			SELECT DISTINCT trim(per.email) AS email
			FROM personas_centros pc LEFT JOIN personas per ON pc.id_persona=per.id
			WHERE per.id_cargo IN(4,8,91,23,69,89,109,108) AND per.id_estado<>3 AND pc.id_centro='$idcentro'");

			if($db->sql_numrows($qCorreos)>0){
				$strMail[$idcentro].="\t

Att.:

AIDA \n";
				#Print_r($strMail["$idcentro"]);
				$cabeceras = 'From: AIDA <aida@promoambientaldistrito.com>' . "\r\n";
				while($correo=$db->sql_fetchrow($qCorreos)){
					error_log("Enviando correo a " . $correo["email"]);
					mail($correo["email"],"Alerta Automática de AIDA",$strMail[$idcentro],$cabeceras);
				}		
			}
	next($strMail); 
	}
}
?>
