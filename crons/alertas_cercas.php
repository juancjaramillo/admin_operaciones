<?
error_reporting(E_ALL);
ini_set("display_errors", 1);
$idcentro=15;
include(dirname(__FILE__) . "/../application.php");
ini_set("error_log",dirname(__FILE__) . "/log_alertas.log");

#"Se quitó la opcion en Base 
#SELECT v.id,codigo,v.id_centro,placa,v.hrposition,v.tiempo,estado, v.the_geom,min(g.tiempo),
#'En Base' as ubicacion, '5 MINUTES' as duracion
#FROM gps_vehi g LEFT JOIN vehiculos v ON g.id_vehi=v.id
# 	LEFT JOIN estados_vehiculos ev on ev.id=v.id_estado 
#WHERE g.tiempo>= now()- interval '5 MINUTES' 
#AND ST_Contains(ST_GeomFromText('POLYGON((-74.14310 4.65727, -74.14172 4.65806, -74.14015 4.65628, -74.14102 4.65542,-74.14310 4.65727))',4326),the_geom)
#group by v.id,codigo, placa,v.hrposition,v.tiempo,estado,v.the_geom,duracion
#UNION
$qVehi=$db->sql_query("
SELECT v.id,codigo,v.id_centro,placa,v.hrposition,v.tiempo,estado, v.the_geom,min(g.tiempo),
'En Relleno' as ubicacion, '5 MINUTES' as duracion
FROM gps_vehi g LEFT JOIN vehiculos v ON g.id_vehi=v.id
 	LEFT JOIN estados_vehiculos ev on ev.id=v.id_estado 
WHERE g.tiempo>= now()- interval '5 MINUTES' 
AND ST_Contains(ST_GeomFromText('POLYGON((-74.15281 4.49393,-74.13753 4.48777,-74.12465 4.51892,-74.13083 4.52551,-74.15281 4.49393))',4326),the_geom)
group by v.id,codigo, placa,v.hrposition,v.tiempo,estado,v.the_geom,duracion
UNION
SELECT v.id,codigo,v.id_centro,placa,v.hrposition,v.tiempo,estado, v.the_geom,min(g.tiempo),
'RG - Cl 43 Sur Cr 85A' as ubicacion, '3 MINUTES' as duracion
FROM gps_vehi g LEFT JOIN vehiculos v ON g.id_vehi=v.id
 	LEFT JOIN estados_vehiculos ev on ev.id=v.id_estado 
WHERE g.tiempo>= now()- interval '3 MINUTES' 
AND ST_Contains(ST_GeomFromText('POLYGON((-74.17008 4.62789,-74.16981 4.62782,-74.17000 4.62768,-74.17009 4.62779,-74.17008 4.62789))',4326),the_geom)
group by v.id,codigo, placa,v.hrposition,v.tiempo,estado,v.the_geom,duracion
UNION
SELECT v.id,codigo,v.id_centro,placa,v.hrposition,v.tiempo,estado, v.the_geom,min(g.tiempo),
'RG - Dg 62 Sur # 19D' as ubicacion, '3 MINUTES' as duracion
FROM gps_vehi g LEFT JOIN vehiculos v ON g.id_vehi=v.id
 	LEFT JOIN estados_vehiculos ev on ev.id=v.id_estado 
WHERE g.tiempo>= now()- interval '3 MINUTES' 
AND ST_Contains(ST_GeomFromText('POLYGON((-74.14118 4.56269,-74.14085 4.56253,-74.14115 4.56231,-74.14142 4.56253,-74.14118 4.56269))',4326),the_geom)
group by v.id,codigo, placa,v.hrposition,v.tiempo,estado,v.the_geom,duracion
UNION
SELECT v.id,codigo,v.id_centro,placa,v.hrposition,v.tiempo,estado, v.the_geom,min(g.tiempo),
'RG - Cra 16B # 59B Sur' as ubicacion, '3 MINUTES' as duracion
FROM gps_vehi g LEFT JOIN vehiculos v ON g.id_vehi=v.id
 	LEFT JOIN estados_vehiculos ev on ev.id=v.id_estado 
WHERE g.tiempo>= now()- interval '3 MINUTES' 
AND ST_Contains(ST_GeomFromText('POLYGON((-74.13248 4.56051,-74.13224 4.56029,-74.13242 4.56016,-74.13273 4.56036,-74.13248 4.56051))',4326),the_geom)
group by v.id,codigo, placa,v.hrposition,v.tiempo,estado,v.the_geom,duracion
UNION
SELECT v.id,codigo,v.id_centro,placa,v.hrposition,v.tiempo,estado, v.the_geom,min(g.tiempo),
'RG - CL 73B Sur #17F' as ubicacion, '3 MINUTES' as duracion
FROM gps_vehi g LEFT JOIN vehiculos v ON g.id_vehi=v.id
 	LEFT JOIN estados_vehiculos ev on ev.id=v.id_estado 
WHERE g.tiempo>= now()- interval '3 MINUTES' 
AND ST_Contains(ST_GeomFromText('POLYGON((-74.13903 4.54406,-74.13904 4.54364,-74.14021 4.54375,-74.14015 4.54410,-74.13903 4.54406))',4326),the_geom)
group by v.id,codigo, placa,v.hrposition,v.tiempo,estado,v.the_geom,duracion
UNION
SELECT v.id,codigo,v.id_centro,placa,v.hrposition,v.tiempo,estado, v.the_geom,min(g.tiempo),
'RG - CL 71 Sur #10 - 18' as ubicacion, '3 MINUTES' as duracion
FROM gps_vehi g LEFT JOIN vehiculos v ON g.id_vehi=v.id
 	LEFT JOIN estados_vehiculos ev on ev.id=v.id_estado 
WHERE g.tiempo>= now()- interval '3 MINUTES' 
AND ST_Contains(ST_GeomFromText('POLYGON((-74.13161 4.53834,-74.13133 4.53812,-74.13145 4.53797,-74.13177 4.53818,-74.13161 4.53834))',4326),the_geom)
group by v.id,codigo, placa,v.hrposition,v.tiempo,estado,v.the_geom,duracion
UNION
SELECT v.id,codigo,v.id_centro,placa,v.hrposition,v.tiempo,estado, v.the_geom,min(g.tiempo),
'RG - CL 71 Sur #3H' as ubicacion, '3 MINUTES' as duracion
FROM gps_vehi g LEFT JOIN vehiculos v ON g.id_vehi=v.id
 	LEFT JOIN estados_vehiculos ev on ev.id=v.id_estado 
WHERE g.tiempo>= now()- interval '3 MINUTES' 
AND ST_Contains(ST_GeomFromText('POLYGON((-74.12574 4.52384,-74.12571 4.52373,-74.12578 4.52371,-74.12589 4.52386,-74.12574 4.52384))',4326),the_geom)
group by v.id,codigo, placa,v.hrposition,v.tiempo,estado,v.the_geom,duracion ");


while($vehi=$db->sql_fetchrow($qVehi)){
	if($alerta=$db->sql_row(" SELECT * FROM alertas WHERE hora::date='" . date("Y-m-d") . "' AND id_centro='15'	AND id_tipo='6' 
				AND id_vehiculo=".$vehi["id"]." AND ack_hora IS NULL"))
	{
		#$qUpdate=$db->sql_query("UPDATE alertas SET ack_id_motivo='2', ack_id_persona='6095', ack_hora=now() WHERE id='$alerta[id]'");
		error_log("\tDesactivando alerta.\n");
	}
	else
	{
		$strMail1= "El Vehiculo ".$vehi["codigo"]."/".$vehi["placa"]." se ubica ".$vehi["hrposition"]." ".$vehi["ubicacion"].".\n";
		$strMail2[$idcentro].= $strMail1;
		$qInsert=$db->sql_query("INSERT INTO alertas (hora,id_centro,id_tipo,id_vehiculo,observaciones)
			VALUES (now(),15,6,$vehi[id],'$strMail1')");
	}
		
//	
}


$qVehi=$db->sql_query("SELECT (tiempo-min) as demora,* FROM 
(SELECT v.id,codigo, placa,v.hrposition,v.tiempo,estado, v.the_geom,min(g.tiempo), count(*) as reg,
'3 horas' as duracion
FROM gps_vehi g LEFT JOIN vehiculos v ON g.id_vehi=v.id
 	LEFT JOIN estados_vehiculos ev on ev.id=v.id_estado 
WHERE g.tiempo>= now()- interval '4 hours' 
AND ST_Contains(gps_geom,the_geom) 
group by v.id,codigo, placa,v.hrposition,v.tiempo,estado,v.the_geom,duracion
order by 2) as positio
WHERE ST_Contains(ST_GeomFromText('POLYGON((-74.14310 4.65727, -74.14172 4.65806, -74.14015 4.65628, -74.14102 4.65542,-74.14310 4.65727))',4326),the_geom)=false
AND (tiempo-min)>interval '3 hours'");

while($vehi=$db->sql_fetchrow($qVehi)){
	if($alerta=$db->sql_row(" SELECT * FROM alertas WHERE hora::date='" . date("Y-m-d") . "' AND id_centro='$id_centro'	AND id_tipo='7' 
				AND id_vehiculo=".$vehi["id"]." AND ack_hora IS NULL"))
	{
		error_log("\tYa Tiene Alerta.\n");
	}
	else
	{
		$strMail1= "El Vehiculo ".$vehi["codigo"]."/".$vehi["placa"]." se ubica en ".$vehi["hrposition"]." durante ".$vehi["demora"]."horas\n";
		$strMail2[$idcentro].= $strMail1;
		$qInsert=$db->sql_query("INSERT INTO alertas (hora,id_centro,id_tipo,id_vehiculo,observaciones)
			VALUES (now(),15,7,$vehi[id],'$strMail1')");
	}
//	
}

$qVehi=$db->sql_query("SELECT v.id,codigo,v.id_centro,placa,v.hrposition,v.tiempo,estado, v.the_geom,min(g.tiempo),
'Fuera de Bogotá' as ubicacion, '5 MINUTES' as duracion
FROM gps_vehi g LEFT JOIN vehiculos v ON g.id_vehi=v.id
 	LEFT JOIN estados_vehiculos ev on ev.id=v.id_estado 
WHERE g.tiempo>= now()- interval '5 MINUTES' 
AND ST_Contains(ST_GeomFromText('POLYGON((-74.05537 4.81216,-74.99872 4.76666,-74.08479 4.45974,-74.12942 4.29954,-74.21697 4.28687,-74.21148 4.39300,-74.15963 4.51074,-74.18367 4.59596,-74.22212 4.62710,-74.17030 4.69562,-74.09597 4.79852,-74.05537 4.81216))',4326),the_geom)=true
group by v.id,codigo, placa,v.hrposition,v.tiempo,estado,v.the_geom,duracion ");

while($vehi=$db->sql_fetchrow($qVehi)){
	if($alerta=$db->sql_row(" SELECT * FROM alertas WHERE hora::date='" . date("Y-m-d") . "' AND id_centro='15'	AND id_tipo='6' 
				AND id_vehiculo=".$vehi["id"]." AND ack_hora IS NULL"))
	{
		error_log("\tYa Tiene Alerta.\n");
	}
	else
	{
		$strMail1= "El Vehiculo ".$vehi["codigo"]."/".$vehi["placa"]." se ubica ".$vehi["hrposition"]." ".$vehi["ubicacion"].".\n";
		$strMail2[$idcentro].= $strMail1;
		$qInsert=$db->sql_query("INSERT INTO alertas (hora,id_centro,id_tipo,id_vehiculo,observaciones)
			VALUES (now(),15,6,$vehi[id],'$strMail1')");
	}
		
//	
}

#Print_r($strMail);
if (isset($strMail)){
	while ($idcentro = key($strMail)) {
		$qCorreos=$db->sql_query("
			SELECT DISTINCT trim(per.email) AS email
			FROM personas_centros pc LEFT JOIN personas per ON pc.id_persona=per.id
			WHERE per.id_cargo IN(8,91,23,54,69,89,109,108,112) AND per.id_estado<>3 AND pc.id_centro='$idcentro'");

			if($db->sql_numrows($qCorreos)>0){
				$strMail[$idcentro].="\t

Att.:

AIDA \n";
				#Print_r($strMail["$idcentro"]);
				$cabeceras = 'From: AIDA <aida@promoambientaldistrito.com>' . "\r\n";
				while($correo=$db->sql_fetchrow($qCorreos)){
					error_log("Enviando correo a " . $correo["email"]);
					mail($correo["email"],"Alerta Automática de AIDA",$strMail2[$idcentro],$cabeceras);
				}		
			}
	next($strMail); 
	}
}
?>
