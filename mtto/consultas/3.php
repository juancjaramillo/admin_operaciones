<?
$strQuery="
SELECT
veh.codigo as \"No. Interno\",
	veh.placa as \"PLACA VEHÍCULO\",
	ll.numero as \"No. al Calor\",
	(select CASE WHEN id_tipo_movimiento='6' THEN null WHEN id_tipo_movimiento='3' THEN null ELSE posicion END as posicion from llta.movimientos WHERE id_llanta=ll.id ORDER BY fecha DESC LIMIT 1) as \"Posición\",
	m.marca as \"Marca\",
	dim.dimension as \"Dimensión\",
	ll.vida as \"VIDA\",
	ll.disenio as \"Diseño\",
	(SELECT fecha FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento='5' AND id_vehiculo=veh.id ORDER BY fecha ASC LIMIT 1) AS \"Fecha Montaje\",
	(SELECT fecha FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento IN('2','6') AND id_vehiculo=veh.id ORDER BY fecha DESC LIMIT 1) AS \"Fecha Inspección\",
	(SELECT km FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento='5' AND id_vehiculo=veh.id ORDER BY fecha ASC LIMIT 1) AS \"Km. Montaje\",
	(SELECT km FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento IN('2','6') AND id_vehiculo=veh.id ORDER BY fecha DESC LIMIT 1) AS \"Km. Inspecc.\",
	(SELECT sum(ult.km-ini.km) AS otrosvehi FROM 
	 		(select a.id_llanta,a.fecha,a.id_vehiculo,a.km FROM llta.movimientos a 
			 		INNER JOIN (SELECT mv.id_llanta,mv.id_vehiculo,max(mv.fecha)as fecha FROM llta.movimientos mv
										LEFT JOIN llta.llantas ll ON mv.id_llanta=ll.id 	
													    WHERE ll.id_centro IN (" . implode(",",$user["id_centro"]) . ") 
														 	and mv.id_llanta=ll.id AND mv.id_tipo_movimiento IN('2','6') group by mv.id_llanta,mv.id_vehiculo) x 
								ON a.id_llanta=x.id_llanta and a.id_vehiculo=x.id_vehiculo and a.fecha=x.fecha
										WHERE a.id_llanta=ll.id AND a.id_tipo_movimiento IN('2','6') ) ult
				INNER JOIN
						(select a.id_llanta,a.fecha,a.id_vehiculo,a.km FROM llta.movimientos a 
						 		INNER JOIN (SELECT id_llanta,id_vehiculo,min(fecha)as fecha FROM llta.movimientos WHERE  id_llanta=ll.id AND id_tipo_movimiento IN('5') group by id_llanta,id_vehiculo) y 
											ON a.id_llanta=y.id_llanta and a.id_vehiculo=y.id_vehiculo and a.fecha=y.fecha
													LEFT JOIN llta.llantas ll ON y.id_llanta=ll.id 	
															WHERE ll.id_centro IN (" . implode(",",$user["id_centro"]) . ") and a.id_llanta=ll.id AND a.id_tipo_movimiento IN('5') ) ini
							ON ini.id_vehiculo=ult.id_vehiculo 
								GROUP BY ult.id_llanta) AS \"Km. recorridos\",
	(SELECT prof_uno FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento='5' AND id_vehiculo=veh.id ORDER BY fecha ASC LIMIT 1) AS \"Prof. Inicial (mm)\",
	(SELECT prof_uno FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento IN('2','6') AND id_vehiculo=veh.id ORDER BY fecha DESC LIMIT 1) AS \"Prof. Revisión (mm)\",
	'=INDIRECT(ADDRESS(ROW();COLUMN()-2))-INDIRECT(ADDRESS(ROW();COLUMN()-1))' AS \"mm gastados\",
	'=INDIRECT(ADDRESS(ROW();COLUMN()-2))-3' AS \"mm útiles restantes\",
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
					ORDER BY ll.id_vehiculo
					";


					$strQuery="
					SELECT
					veh.codigo as \"No. Interno\",
					veh.placa as \"PLACA VEHÍCULO\",
					ll.numero as \"No. al Calor\",
					(select CASE WHEN id_tipo_movimiento='6' THEN null WHEN id_tipo_movimiento='3' THEN null ELSE posicion END as posicion from llta.movimientos WHERE id_llanta=ll.id ORDER BY fecha DESC LIMIT 1) as \"Posición\",
					m.marca as \"Marca\",
					dim.dimension as \"Dimensión\",
					ll.vida as \"VIDA\",
					ll.disenio as \"Diseño\",
					(SELECT fecha FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento='5' AND id_vehiculo=veh.id ORDER BY fecha ASC LIMIT 1) AS \"Fecha Montaje\",
					(SELECT fecha FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento IN('2','6') AND id_vehiculo=veh.id ORDER BY fecha DESC LIMIT 1) AS \"Fecha Inspección\",
					(SELECT km FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento='5' AND id_vehiculo=veh.id ORDER BY fecha ASC LIMIT 1) AS \"Km. Montaje\",
					(SELECT km FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento IN('2','6') AND id_vehiculo=veh.id ORDER BY fecha DESC LIMIT 1) AS \"Km. Inspecc.\",
					(SELECT sum(ult.km-ini.km) AS otrosvehi FROM 
					 		(select a.id_llanta,a.fecha,a.id_vehiculo,a.km FROM llta.movimientos a 
							 		INNER JOIN (SELECT mv.id_llanta,mv.id_vehiculo,max(mv.fecha)as fecha FROM llta.movimientos mv
														LEFT JOIN llta.llantas ll ON mv.id_llanta=ll.id 	
																	    WHERE ll.id_centro IN (" . implode(",",$user["id_centro"]) . ") and mv.id_llanta=ll.id AND mv.id_tipo_movimiento IN('2','6') group by mv.id_llanta,mv.id_vehiculo) x 
												ON a.id_llanta=x.id_llanta and a.id_vehiculo=x.id_vehiculo and a.fecha=x.fecha
														WHERE a.id_llanta=ll.id AND a.id_tipo_movimiento IN('2','6') ) ult
								INNER JOIN
										(select a.id_llanta,a.fecha,a.id_vehiculo,a.km FROM llta.movimientos a 
										 		INNER JOIN (SELECT id_llanta,id_vehiculo,min(fecha)as fecha FROM llta.movimientos WHERE  id_llanta=ll.id AND id_tipo_movimiento IN('5') group by id_llanta,id_vehiculo) y 
															ON a.id_llanta=y.id_llanta and a.id_vehiculo=y.id_vehiculo and a.fecha=y.fecha
																	LEFT JOIN llta.llantas ll ON y.id_llanta=ll.id 	
																			WHERE ll.id_centro IN (" . implode(",",$user["id_centro"]) . ")  and a.id_llanta=ll.id AND a.id_tipo_movimiento IN('5') ) ini
											ON ini.id_vehiculo=ult.id_vehiculo 
												GROUP BY ult.id_llanta) AS \"Km. recorridos\",
					(SELECT prof_uno FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento='5' AND id_vehiculo=veh.id ORDER BY fecha ASC LIMIT 1) AS \"Prof. Inicial (mm)\",
					(SELECT prof_uno FROM llta.movimientos WHERE id_llanta=ll.id AND id_tipo_movimiento IN('2','6') AND id_vehiculo=veh.id ORDER BY fecha DESC LIMIT 1) AS \"Prof. Revisión (mm)\",
					'=INDIRECT(ADDRESS(ROW();COLUMN()-2))-INDIRECT(ADDRESS(ROW();COLUMN()-1))' AS \"mm gastados\",
					'=INDIRECT(ADDRESS(ROW();COLUMN()-2))-3' AS \"mm útiles restantes\",
					'=INDIRECT(ADDRESS(ROW();COLUMN()-5))/INDIRECT(ADDRESS(ROW();COLUMN()-2))' AS \"Km.recorr./mm gastados\",
					'=(INDIRECT(ADDRESS(ROW();COLUMN()-2))*INDIRECT(ADDRESS(ROW();COLUMN()-1)))+INDIRECT(ADDRESS(ROW();COLUMN()-6))' AS \"Proyecc. De rendimiento (Km)\", ll.costo +
					COALESCE((
								 SELECT sum(costo)
								  FROM llta.movimientos
									 WHERE id_llanta=ll.id AND id_tipo_movimiento='1'
								),0) AS \"COSTO DE LA LLANTA ($)\",
					'=INDIRECT(ADDRESS(ROW();COLUMN()-1))/INDIRECT(ADDRESS(ROW();COLUMN()-8))' AS \"$/Km\"
					FROM llta.llantas ll
					LEFT JOIN vehiculos veh ON veh.id = ll.id_vehiculo 
					LEFT JOIN llta.dimensiones dim ON ll.id_dimension=dim.id
					LEFT JOIN llta.marcas m ON dim.id_marca=m.id
					WHERE ll.id_centro IN (" . implode(",",$user["id_centro"]) . ")
					ORDER BY ll.numero
					";


//echo $strQuery;die;
?>
