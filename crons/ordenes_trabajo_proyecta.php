<?
include(dirname(__FILE__) . "/../application.php");

$fecha1= date("Y-m-d", strtotime("-2 day"));
$fecha2= date("Y-m-d", strtotime("-3 day"));

$qid = $db->sql_query(" delete from historico_recorrido WHERE km=0 and horas=0 and fecha>='$fecha1'");
$qid = $db->sql_query("INSERT INTO historico_recorrido(id_equipo, fecha, km, horas)
		select mtto.equipos.id,'$fecha1' as fecha,(km1-km2) as km, CASE WHEN(hor1-hor2)>24 THEN 18 ELSE (hor1-hor2) END as horas from 
		(select id_vehiculo, inicio::date, max(km_final) as km1, max(horometro_final) as hor1 from rec.movimientos 
		 where id_vehiculo in (select id_vehiculo from mtto.equipos where id not in (select id_equipo from historico_recorrido where fecha='$fecha1'))
		 	 and inicio::date ='$fecha1' and final is not null
			 group by id_vehiculo, inicio::date) dia1
		left join 
		(select id_vehiculo, inicio::date, min(km) as km2,min(horometro) as hor2 
		 from rec.movimientos left join rec.desplazamientos on rec.movimientos.id= rec.desplazamientos.id_movimiento
		 where rec.movimientos.inicio::date ='$fecha1' and final is not null
		 group by id_vehiculo, inicio::date) dia2
		on dia1.id_vehiculo=dia2.id_vehiculo
		left join mtto.equipos on dia1.id_vehiculo=mtto.equipos.id_vehiculo
		where (km1-km2>0 and km1-km2<300) AND (hor1-hor2>0)");

$qid = $db->sql_query(" delete from mtto.ordenes_trabajo_proyecta");
$qid = $db->sql_query(" insert into mtto.ordenes_trabajo_proyecta
	select * FROM mtto.ordenes_trabajo
	WHERE id_rutina in (select id from mtto.rutinas where id_tipo_mantenimiento=1 and id>=4475 order by 1) 
	and fecha_ejecucion_fin is null");
		
$vars = array();

$qid = $db->sql_query("SELECT * FROM mtto.variables_mtto");
while($query = $db->sql_fetchrow($qid))
{
	$vars[$query["id_centro"]] = array("promedio"=>$query["promedio"], "programacion"=>$query["programacion"]);
}

$qid = $db->sql_query("SELECT e.* FROM mtto.equipos  e
				LEFT JOIN vehiculos v ON e.id_vehiculo=v.id 
				where (v.id_estado!=4  or v.id_estado is null)
				order by nombre");	

while($eq = $db->sql_fetchrow($qid))
{
	echo "Equipo--->".$eq["nombre"]." id--->".$eq["id"]."</p>";
	$datos=array($eq["id_grupo"]);
	$grupos = obtenerIdsGrupos($eq["id_grupo"],$datos);
	
	//averiguar rutinas de los grupos
	$consRutGru = "SELECT r.*,f.dias*24 as frec_horas_uno 
		FROM mtto.rutinas r
		LEFT JOIN mtto.tipos t ON t.id=r.id_tipo_mantenimiento
		LEFT JOIN mtto.frecuencias f ON f.id=r.id_frecuencia
		WHERE r.id>=4475 and r.activa AND t.cron AND r.id_grupo IN (".implode(",",$datos).")
		UNION
		SELECT r2.*,f2.dias*24 as frec_horas_uno 
		FROM mtto.rutinas r2
		LEFT JOIN mtto.tipos t2 ON t2.id=r2.id_tipo_mantenimiento
		LEFT JOIN mtto.frecuencias f2 ON f2.id=r2.id_frecuencia
		WHERE  r2.activa AND t2.cron AND r2.id_equipo = '".$eq["id"]."'";
	#echo $consRutGru ."</p>";	
	
	$qidRG = $db->sql_query($consRutGru);
	while($rutGru = $db->sql_fetchrow($qidRG))
	{
		$salir = False;
		echo "Rutina--->".$rutGru["rutina"]." id--->".$rutGru["id"]."</p>";
		//Buscamos la ultima ejecución.
		$cons = "SELECT o.km, o.horometro, e.cerrado,o.id,to_char(o.fecha_ejecucion_fin,'YYYY-MM-DD') as fecha_ejecucion, to_char(o.fecha_planeada,'YYYY-MM-DD') as fecha_planeada
			FROM mtto.ordenes_trabajo o 
			LEFT JOIN mtto.estados_ordenes_trabajo e ON e.id=o.id_estado_orden_trabajo
			WHERE o.id_rutina='".$rutGru["id"]."' AND o.id_equipo='".$eq["id"]."' AND e.cerrado
			ORDER BY fecha_planeada DESC limit 1";
		#echo $cons ."</p>";
		
		$qidOT = $db->sql_query($cons);
		if($db->sql_numrows($qidOT)>0)
		{
			$rutAnt = $db->sql_fetchrow($qidOT);
			$ultimavez = $rutAnt["fecha_ejecucion"];
			$toma = $rutAnt["fecha_ejecucion"];
			$vehi = array("km"=>$rutAnt["km"], "horo"=>$rutAnt["horometro"]);
			$dias = restarFechas(date("Y-m-d"),$toma);	
			$prog = nvl($vars[$eq["id_centro"]]["programacion"],1);
			$j=1;
			
			$prom = nvl($vars[$eq["id_centro"]]["promedio"],1);
			$promKmyHoras = $db->sql_row("SELECT sum(km)/".$prom." as kms, sum(horas)/".$prom." as horo, sum(km) as sumkm, sum(horas) as sumhoras 
					FROM historico_recorrido 
					WHERE id in (SELECT id FROM historico_recorrido WHERE id_equipo='".$eq["id"]."' ORDER BY fecha DESC LIMIT ".$prom.")
					and horas < 24 and km < 800");
			
			$diasAProgramar = $prog - $dias;
			$km = $vehi["km"];
			$horo = $vehi["horo"];
			$recoKm=$recoHoro=0;
			
			for($i=1;$i<=($diasAProgramar);$i++)
			{
				$crearOrden=false;
				$recoKm = $eq["kilometraje"]-$vehi["km"]+($promKmyHoras["kms"]*$i);
				$recoHoro = $eq["horometro"]-$vehi["horo"]+($promKmyHoras["horo"]*$i);

				if($rutGru["frec_horas"]!= "" && $recoHoro >= $rutGru["frec_horas"])
					$crearOrden = true;
					
				elseif($rutGru["frec_horas_uno"]!="" && $rutGru["frec_horas"]=="" && $rutGru["frec_km"]=="")
				{
					list($anio,$mes,$dia)=split("-",$toma);
					$fecha = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + ($dias+$i) * 24 * 60 * 60);
					$hanPasado = ((restarFechas($fecha,$toma))*24);
					if($hanPasado >= ($rutGru["frec_horas_uno"]*$j))
					{
						$crearOrden = true;
						$j++;
					}
				}
				
				elseif($rutGru["frec_horas_uno"]!= "" && $recoHoro >= $rutGru["frec_horas_uno"])
					$crearOrden = true;
					
				
				elseif($rutGru["frec_km"] != "" && $recoKm >= $rutGru["frec_km"]){
					$crearOrden = true;
					$restarKM = $rutGru["frec_km"];	
				}
										
				if($crearOrden && $salir==False)
				{
					list($anio,$mes,$dia)=split("-",$toma);
					$fecha = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + ($dias+$i) * 24 * 60 * 60);
					$fecha = averiguarFecha($eq["id_centro"],$fecha);
					$salir=True;					
					$otPSH = $db->sql_row("SELECT count(o.id) as num FROM mtto.ordenes_trabajo_proyecta o 
							LEFT JOIN mtto.estados_ordenes_trabajo e ON e.id=o.id_estado_orden_trabajo
							WHERE o.id_rutina='".$rutGru["id"]."' AND o.id_equipo='".$eq["id"]."' AND not e.cerrado");
										
					//No hay OT Abierta
					if($otPSH["num"]==0){
						echo "Creo OT porque no Existe </P>";					
						$db->sql_query("INSERT INTO mtto.ordenes_trabajo_proyecta
							(id_rutina, id_equipo,fecha_planeada,id_creador,id_estado_orden_trabajo) 
							VALUES (".$rutGru["id"].",".$eq["id"].",'".$fecha."',39,7)");
					}
					
					//Hay una OT pendiente y se actualiza la fecha de programación
					if($otPSH["num"]==1){
						$ConsActOT = "Update mtto.ordenes_trabajo_proyecta o set fecha_planeada = '".$fecha."' 
							FROM mtto.ordenes_trabajo_proyecta e 
							WHERE o.id_rutina='".$rutGru["id"]."' AND o.id_equipo='".$eq["id"]."'";		
						$qidActOT = $db->sql_query($ConsActOT);
						echo "Se Actualizo OT</p>";
					}	
				}
			}
		}
		#Consultamos la diferencia entre la ultima ejecución y la siguiente programada
		echo "Ultima Vez--->>>".$ultimavez."</p>";	
		echo "Fecha Planeada ---->>>".$fecha."</p>";
		$dias = restarFechas($ultimavez,$fecha);
		if($salir==True){
			for($k=1;$k<=15;$k++)
			{
				list($anio,$mes,$dia)=split("-",$fecha);
				if ($anio>2013){
					$fecha1 = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + ($dias*$k) * 24 * 60 * 60);
					echo "Ejecución ".$k."-->>".$fecha1."</p>";		
					 $db->sql_query("INSERT INTO mtto.ordenes_trabajo_proyecta
						(id_rutina, id_equipo,fecha_planeada,id_creador,id_estado_orden_trabajo) 
						VALUES (".$rutGru["id"].",".$eq["id"].",'".$fecha1."',39,7)");
				}
			}
		}
	}	
}

$db->sql_query(" update mtto.ordenes_trabajo_proyecta  set periodo= (cast(date_part('year',fecha_planeada) as text)||'-'||
CASE WHEN date_part('month',fecha_planeada)<10 	THEN  '0'||cast(date_part('month',fecha_planeada) as text)
	ELSE cast(date_part('month',fecha_planeada) as text) END)");
?>
