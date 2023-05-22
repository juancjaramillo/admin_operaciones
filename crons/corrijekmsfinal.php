<?
error_reporting(E_ALL);
ini_set("display_errors", 1);
$finicio = '2013-07-01';
#$ffinal = date ( "Y-m-d" , time()-86400);	 
#$finicio = '2013-01-01';
$ffinal =  '2013-07-30';	 
// operaciones : báscula
 include(dirname(__FILE__) . "/../application.php");
///Consulto todos los movimientos
$consulta = "drop table rec.tmp_car";
$qid = $db->sql_query($consulta);
$consulta = "create table rec.tmp_car as (select salida.id_movimiento,salida.hora_inicio::date as hsbas,salida.hora_inicio,des.id as id_des,des.km as kms_sal, 
				des.horometro as horo_sal,m.id as mov_id,m.id_vehiculo,vh.codigo,m.inicio,m.final,m.km_final,m.horometro_final
			from 
				(SELECT id_movimiento,min(hora_inicio)as hora_inicio
				FROM rec.desplazamientos 
				WHERE hora_inicio::date >= '$finicio' AND hora_inicio::date<='$ffinal'
				group by id_movimiento)salida
			LEFT JOIN rec.desplazamientos des on  (des.id_movimiento=salida.id_movimiento and des.hora_inicio=salida.hora_inicio)
			LEFT JOIN rec.movimientos m ON (salida.id_movimiento=m.id)
			LEFT JOIN vehiculos vh ON m.id_vehiculo=vh.id
			where vh.id_centro in (3,4)
			order by id_vehiculo,des.hora_inicio)";
#			echo $consulta;
#			$qid = $db->sql_query($consulta);

$consulta = " SELECT vh.id_centro,m.id,id_vehiculo,inicio,final,km_final,min(des.hora_inicio) as minimo
			FROM rec.movimientos m
			LEFT JOIN rec.desplazamientos des on  (des.id_movimiento=m.id)
			LEFT JOIN vehiculos vh ON m.id_vehiculo=vh.id
			WHERE inicio::date >= '".$finicio."' AND inicio::date<='".$ffinal."' AND vh.id_centro in (3,4) and km_final is not null and km_final!=0
			and des.hora_inicio is not null
			group by vh.id_centro,m.id,id_vehiculo,inicio,final,km_final
			ORDER BY id_vehiculo,final";
echo $consulta;

#$qid = $db->sql_query($consulta);
while($query = $db->sql_fetchrow($qid))
{
	$id_mov_actualizar = $query['id'];
	echo "mov->".$id_mov_actualizar."\n"; 
	///consulta el movimiento siguiente validando que no ande mas de 200 kms y que el diferencia de horas no sea mayor de 6 horas
	
	if ($query["id_centro"] == 3) {
			$consMovSig = "select * from rec.tmp_car
			where id_vehiculo =".$query["id_vehiculo"]." and id_movimiento!= ".$query["id"]." and kms_sal<=(".$query["km_final"]."+200) and kms_sal!=0 
			and hora_inicio>'".$query["minimo"]."' and horometro_final is not null and horo_sal is not null
			order by inicio limit 1";
	}
	else{		
#			$consMovSig = "select * from rec.tmp_car
#				where id_vehiculo =".$query["id_vehiculo"]." and kms_sal<=(".$query["km_final"]."+200) and id_movimiento!= ".$query["id"]."
#				and hora_inicio>'".$query["final"]."' and (hora_inicio-'".$query["final"]."') < '14:00:00'
#			order by hora_inicio limit 1";
				
			$consMovSig="select min(kms_sal) as kms_sal, min(horo_sal) as horo_sal from rec.tmp_car where id_vehiculo =".$query["id_vehiculo"]."
				and kms_sal<=(".$query["km_final"]."+200)  and id_movimiento!= ".$query["id"]." and kms_sal>".$query["km_final"]."
				and hora_inicio>'".$query["final"]."'";
			#echo  $consMovSig;
	}
#	$qidMov = $db->sql_query($consMovSig);
	while($mov = $db->sql_fetchrow($qidMov))
	{
		// Consigo el kms del siguiente movimiento y actualizo el movimiento a actualizar.
		$kms_final = $mov["kms_sal"];
		$hr_final =  $mov["horo_sal"];
		if ($kms_final!='')	{

			$ant = $db->sql_row("SELECT m.*, v.codigo||'/'||v.placa as codigo, i.codigo as micro FROM rec.movimientos m LEFT JOIN vehiculos v ON v.id=m.id_vehiculo LEFT JOIN micros i ON i.id = m.id_micro WHERE m.id=".$id_mov_actualizar);

			$actualizar = "update rec.movimientos set km_final= $kms_final, horometro_final=$hr_final where id=$id_mov_actualizar";
			//echo  $actualizar;
#			$qidActualiza = $db->sql_query($actualizar);
		
			$accion="Actualizó movimiento desde el cron corrijekmsfinal: \nRuta: ".$ant["micro"]."\nVehículo: ".$ant["codigo"]."\nKm: dato anterior: ".$ant["km_final"]." | nuevo dato: ".$kms_final."\nHorometro: dato anterior: ".$ant["horometro_final"]." | nuevo dato: ".$hr_final;
			ingresarLogMovimiento("rec", $id_mov_actualizar, $accion);
		}
	}
}
?>
