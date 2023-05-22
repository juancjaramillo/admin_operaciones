<?
include(dirname(__FILE__) . "/../application.php");

$vars = array();
include($CFG->modulesdir . "/mtto.ordenes_trabajo.php");

$qid = $db->sql_query("SELECT * FROM mtto.variables_mtto");
while($query = $db->sql_fetchrow($qid))
{
	$vars[$query["id_centro"]] = array("promedio"=>$query["promedio"], "programacion"=>$query["programacion"]);
}

$qid = $db->sql_query("SELECT * FROM mtto.equipos ORDER BY id");
//$qid = $db->sql_query("SELECT * FROM mtto.equipos where id=153");
while($eq = $db->sql_fetchrow($qid))
{
	$datos=array($eq["id_grupo"]);
	$grupos = obtenerIdsGrupos($eq["id_grupo"],$datos);

	//averiguar rutinas de los grupos
	$consRutGru = "SELECT r.*,f.dias*24 as frec_horas_uno 
		FROM mtto.rutinas r
		LEFT JOIN mtto.tipos t ON t.id=r.id_tipo_mantenimiento
		LEFT JOIN mtto.frecuencias f ON f.id=r.id_frecuencia
		WHERE r.activa AND t.cron AND r.id_grupo IN (".implode(",",$datos).")
		UNION
		SELECT r2.*,f2.dias*24 as frec_horas_uno 
		FROM mtto.rutinas r2
		LEFT JOIN mtto.tipos t2 ON t2.id=r2.id_tipo_mantenimiento
		LEFT JOIN mtto.frecuencias f2 ON f2.id=r2.id_frecuencia
		WHERE r2.activa AND t2.cron AND r2.id_equipo = '".$eq["id"]."'";
	$qidRG = $db->sql_query($consRutGru);
	while($rutGru = $db->sql_fetchrow($qidRG))
	{
		//mantenimiento de vehiculos
		$cons = "SELECT o.km, o.horometro, e.cerrado,o.id,to_char(o.fecha_ejecucion_fin,'YYYY-MM-DD') as fecha_ejecucion, to_char(o.fecha_planeada,'YYYY-MM-DD') as fecha_planeada
			FROM mtto.ordenes_trabajo o 
			LEFT JOIN mtto.estados_ordenes_trabajo e ON e.id=o.id_estado_orden_trabajo
			WHERE o.id_rutina='".$rutGru["id"]."' AND o.id_equipo='".$eq["id"]."' AND e.cerrado
			ORDER BY fecha_planeada DESC";
		$qidOT = $db->sql_query($cons);
		if($db->sql_numrows($qidOT)>0)
		{
			$rutAnt = $db->sql_fetchrow($qidOT);
			$toma = $rutAnt["fecha_ejecucion"];
			$vehi = array("km"=>$rutAnt["km"], "horo"=>$rutAnt["horometro"]);

			$dias = restarFechas(date("Y-m-d"),$toma);	
			$prog = nvl($vars[$eq["id_centro"]]["programacion"],1);
			$j=1;
	
			if($dias < $prog)
			{
				$prom = nvl($vars[$eq["id_centro"]]["promedio"],1);
				$promKmyHoras = $db->sql_row("SELECT sum(km)/".$prom." as kms, sum(horas)/".$prom." as horo, sum(km) as sumkm, sum(horas) as sumhoras 
						FROM historico_recorrido 
						WHERE id in (SELECT id FROM historico_recorrido WHERE id_equipo='".$eq["id"]."' ORDER BY fecha DESC LIMIT ".$prom.")");
				$diasAProgramar = $prog - $dias;
				$km = $vehi["km"];
				$horo = $vehi["horo"];
				$sumRecoKm=$sumRecoHoro=0;

				for($i=1;$i<=$diasAProgramar;$i++)
				{
					$crearOrden=false;
					$recoKm = ($km + $sumRecoKm + $promKmyHoras["kms"] ) - $km;
					$recoHoro = ($horo + $sumRecoHoro + $promKmyHoras["horo"]) - $horo;

					if($rutGru["frec_horas"]!= "" && $recoHoro >= $rutGru["frec_horas"])
						$crearOrden = true;
					elseif($rutGru["frec_horas_uno"]!= "" && $rutGru["frec_horas"]== "" && $rutGru["frec_km"]== "")
					{
						list($anio,$mes,$dia)=split("-",$toma);
						$fecha = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + $i * 24 * 60 * 60);
						$hanPasado = ((restarFechas($fecha,$toma))*24);
						if($hanPasado >= ($rutGru["frec_horas_uno"]*$j))
						{
							$crearOrden = true;
							$j++;
						}
					}
					elseif($rutGru["frec_horas_uno"]!= "" && $recoHoro >= $rutGru["frec_horas_uno"])
						$crearOrden = true;
					elseif($rutGru["frec_km"] != "" && $recoKm >= $rutGru["frec_km"])
						$crearOrden = true;

					if($crearOrden)
					{
						list($anio,$mes,$dia)=split("-",$toma);
						$fecha = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + $i * 24 * 60 * 60);
						$fecha = averiguarFecha($eq["id_centro"],$fecha);

						/*
						//averiguar si hay OT que sean menor igual que la fecha programada, si hay ot que no se han hecho, no se programa
						$otPSH = $db->sql_row("SELECT count(o.id) as num FROM mtto.ordenes_trabajo o 
								LEFT JOIN mtto.estados_ordenes_trabajo e ON e.id=o.id_estado_orden_trabajo
								WHERE o.id_rutina='".$rutGru["id"]."' AND o.id_equipo='".$eq["id"]."' AND not e.cerrado AND o.fecha_planeada::date <= '".$fecha."'");
						*/

						//change: 19/oct/2012:  because the date doesn't matter, only if exist an open order
						$otPSH = $db->sql_row("SELECT count(o.id) as num FROM mtto.ordenes_trabajo o 
								LEFT JOIN mtto.estados_ordenes_trabajo e ON e.id=o.id_estado_orden_trabajo
								WHERE o.id_rutina='".$rutGru["id"]."' AND o.id_equipo='".$eq["id"]."' AND not e.cerrado");

						if($otPSH["num"]==0)
							crearOrdenTrabajo($entidad,$rutGru["id"],$eq["id"],$fecha." 08:00:00");
						
						$km = $km+$recoKm;
						$horo=$horo+$recoHoro;
						$sumRecoKm=$sumRecoHoro=0;

					}else
					{
						$sumRecoKm+=$promKmyHoras["kms"];
						$sumRecoHoro+=$promKmyHoras["horo"];
					}
				}
			}
		}
	}
}


?>
