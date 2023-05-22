<?
$strQuery="
SELECT
veh.placa as \"PLACA VEH�CULO\",
veh.codigo as \"No. Interno\",
ll.numero as \"No. al Calor\",
(select CASE WHEN id_tipo_movimiento='6' THEN null WHEN id_tipo_movimiento='3' THEN null ELSE posicion END as posicion from llta.movimientos WHERE id_llanta=ll.id ORDER BY fecha DESC LIMIT 1) as \"Posici�n\",
m.marca as \"Marca\",
dim.dimension as \"Dimensi�n\",
ll.vida as \"VIDA\",
ll.disenio as \"Dise�o\",
(SELECT fecha FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento='5' AND id_vehiculo=veh.id ORDER BY fecha ASC LIMIT 1) AS \"Fecha Montaje\",
(SELECT fecha FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento IN('2','6') AND id_vehiculo=veh.id ORDER BY fecha DESC LIMIT 1) AS \"Fecha Inspecci�n\",
(SELECT km FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento='5' AND id_vehiculo=veh.id ORDER BY fecha ASC LIMIT 1) AS \"Km. Montaje\",
(SELECT km FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento IN('2','6') AND id_vehiculo=veh.id ORDER BY fecha DESC LIMIT 1) AS \"Km. Inspecc.\",
(SELECT km FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento IN('2','6') AND id_vehiculo=veh.id ORDER BY fecha DESC LIMIT 1) - (SELECT km FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento='5' AND id_vehiculo=veh.id ORDER BY fecha ASC LIMIT 1) AS \"Km. recorridos\",
(SELECT prof_uno FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento='5' AND id_vehiculo=veh.id ORDER BY fecha ASC LIMIT 1) AS \"Prof. Inicial (mm)\",
(SELECT prof_uno FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento IN('2','6') AND id_vehiculo=veh.id ORDER BY fecha DESC LIMIT 1) AS \"Prof. Revisi�n (mm)\",
'=INDIRECT(ADDRESS(ROW();COLUMN()-2))-INDIRECT(ADDRESS(ROW();COLUMN()-1))' AS \"mm gastados\",
'=INDIRECT(ADDRESS(ROW();COLUMN()-2))-3' AS \"mm �tiles restantes\",
'=INDIRECT(ADDRESS(ROW();COLUMN()-5))/INDIRECT(ADDRESS(ROW();COLUMN()-2))' AS \"Km.recorr./mm gastados\",
'=(INDIRECT(ADDRESS(ROW();COLUMN()-2))*INDIRECT(ADDRESS(ROW();COLUMN()-1)))+INDIRECT(ADDRESS(ROW();COLUMN()-6))' AS \"Proyecc. De rendimiento (Km)\",
COALESCE((
 SELECT costo
 FROM llta.movimientos
 WHERE id_llanta=ll.id AND id_tipo_movimiento='1'
  AND fecha<(SELECT fecha FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento='5' AND id_vehiculo=veh.id ORDER BY fecha ASC LIMIT 1)
 ORDER BY fecha DESC LIMIT 1
),ll.costo) AS \"COSTO DE LA LLANTA ($)\",
'=INDIRECT(ADDRESS(ROW();COLUMN()-1))/INDIRECT(ADDRESS(ROW();COLUMN()-8))' AS \"$/Km\"
FROM (SELECT DISTINCT id_llanta,id_vehiculo FROM llta.movimientos WHERE id_vehiculo IS NOT NULL) as dl
	LEFT JOIN llta.llantas ll ON dl.id_llanta=ll.id 
	LEFT JOIN vehiculos veh ON dl.id_vehiculo=veh.id
	LEFT JOIN llta.dimensiones dim ON ll.id_dimension=dim.id
	LEFT JOIN llta.marcas m ON dim.id_marca=m.id
WHERE ll.id_centro IN (" . implode(",",$user["id_centro"]) . ")
and ll.id in (select id from (select id, max(fecha) from (select llta.llantas.id,llta.movimientos.fecha from llta.llantas
					left join llta.movimientos on llta.llantas.id=llta.movimientos.id_llanta
						where id_tipo_movimiento = 5 
							order by 1 desc) x
				group by id
					order by 1)y)
ORDER BY ll.id_vehiculo
";


$strQuery="
SELECT
veh.codigo as \"No. Interno\",
veh.placa as \"PLACA VEH�CULO\",
ll.numero as \"No. al Calor\",
(select CASE WHEN id_tipo_movimiento='6' THEN null WHEN id_tipo_movimiento='3' THEN null ELSE posicion END as posicion from llta.movimientos WHERE id_llanta=ll.id ORDER BY fecha DESC LIMIT 1) as \"Posici�n\",
m.marca as \"Marca\",
dim.dimension as \"Dimensi�n\",
ll.vida as \"VIDA\",
ll.disenio as \"Dise�o\",
(SELECT fecha FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento='5' AND id_vehiculo=veh.id ORDER BY fecha ASC LIMIT 1) AS \"Fecha Montaje\",
(SELECT fecha FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento IN('2','6') AND id_vehiculo=veh.id ORDER BY fecha DESC LIMIT 1) AS \"Fecha Inspecci�n\",
(SELECT km FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento='5' AND id_vehiculo=veh.id ORDER BY fecha ASC LIMIT 1) AS \"Km. Montaje\",
(SELECT km FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento IN('2','6') AND id_vehiculo=veh.id ORDER BY fecha DESC LIMIT 1) AS \"Km. Inspecc.\",
(SELECT km FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento IN('2','6') AND id_vehiculo=veh.id ORDER BY fecha DESC LIMIT 1) - (SELECT km FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento='5' AND id_vehiculo=veh.id ORDER BY fecha ASC LIMIT 1) AS \"Km. recorridos\",
(SELECT prof_uno FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento='5' AND id_vehiculo=veh.id ORDER BY fecha ASC LIMIT 1) AS \"Prof. Inicial (mm)\",
(SELECT prof_uno FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento IN('2','6') AND id_vehiculo=veh.id ORDER BY fecha DESC LIMIT 1) AS \"Prof. Revisi�n (mm)\",
'=INDIRECT(ADDRESS(ROW();COLUMN()-2))-INDIRECT(ADDRESS(ROW();COLUMN()-1))' AS \"mm gastados\",
'=INDIRECT(ADDRESS(ROW();COLUMN()-2))-3' AS \"mm �tiles restantes\",
'=INDIRECT(ADDRESS(ROW();COLUMN()-5))/INDIRECT(ADDRESS(ROW();COLUMN()-2))' AS \"Km.recorr./mm gastados\",
'=(INDIRECT(ADDRESS(ROW();COLUMN()-2))*INDIRECT(ADDRESS(ROW();COLUMN()-1)))+INDIRECT(ADDRESS(ROW();COLUMN()-6))' AS \"Proyecc. De rendimiento (Km)\", ll.costo +
COALESCE((
 SELECT sum(costo)
 FROM llta.movimientos
 WHERE id_llanta=ll.id AND id_tipo_movimiento='1'
 ),0) AS \"COSTO DE LA LLANTA ($)\",
'=INDIRECT(ADDRESS(ROW();COLUMN()-1))/INDIRECT(ADDRESS(ROW();COLUMN()-8))' AS \"$/Km\"
FROM vehiculos veh
LEFT JOIN llta.llantas ll ON veh.id = ll.id_vehiculo 
LEFT JOIN llta.dimensiones dim ON ll.id_dimension=dim.id
LEFT JOIN llta.marcas m ON dim.id_marca=m.id
WHERE ll.id_centro IN (" . implode(",",$user["id_centro"]) . ")
and ll.id in (select id from (select id, max(fecha) from (select llta.llantas.id,llta.movimientos.fecha from llta.llantas
					left join llta.movimientos on llta.llantas.id=llta.movimientos.id_llanta
						where id_tipo_movimiento = 5 
							order by 1 desc) x
				group by id
					order by 1)y)
ORDER BY veh.codigo, veh.placa
";


//echo $strQuery;die;
?>
