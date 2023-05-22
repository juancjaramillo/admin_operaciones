<?
include(dirname(__FILE__) . "/../application.php");
ini_set("error_log",dirname(__FILE__) . "/log_alertas.log");

$fecha= date("Y-m-d", strtotime("-2 day"));
$fec1=date("Y-m-d", strtotime("-8 day"));
//actualizamos el km_final en la tablarec.movimientos con el del máximo desplazamiento por cada movimiento 
//cuando este con concuerde ya que hacen correcciones y no cambian este último

/*se crea ésta tabla temporal antes de la actualizacion de qultimokms para seguir el rastro del campo km_final de los movimientos, a ver si lo cambio o ....*/
$fechaTabla = date("Y_m_d")."__".date("H_i");
#$create = "create table rec.tmp_km_finales_movimientos_".$fechaTabla." as select d.id_movimiento, rec.movimientos.inicio, rec.movimientos.km_final, kms1, kms2 from (select id_movimiento,min(km) as kms1,max(km) as kms2 from rec.desplazamientos GROUP BY id_movimiento) as d left join rec.movimientos on rec.movimientos.id=d.id_movimiento where km_final<>kms2";

#$db->sql_query($create);
/* -- */

#$qultimokms=$db->sql_query("Update  rec.movimientos  set km_final= d.kms2 
#FROM (select id_movimiento,min(km) as kms1,max(km) as kms2 from rec.desplazamientos GROUP BY id_movimiento) as d 
#where rec.movimientos.id=d.id_movimiento
#and km_final<>kms2");

$strMail="";

$qCorreos=$db->sql_query("SELECT DISTINCT trim(per.email) AS email, pc.id_centro, centros.centro
	FROM personas_centros pc 
	LEFT JOIN personas per ON pc.id_persona=per.id
	LEFT JOIN centros  on pc.id_centro=centros.id
	WHERE per.id_cargo IN(6,7) AND per.id_estado<>3
	group by  trim(per.email), pc.id_centro,centros.centro
	order by pc.id_centro");

while($correos=$db->sql_fetchrow($qCorreos)){
	if($alerta=$db->sql_row("SELECT m.id, m.inicio::date as fecmov, vh.codigo as vehi, i.codigo as ruta,d.kms1, m.km_final, m.km_final-d.kms1 as kms_recorrido 
		FROM rec.movimientos m
		LEFT JOIN (select id_movimiento,min(km) as kms1,max(km) as kms2 from rec.desplazamientos GROUP BY id_movimiento) as d ON m.id=d.id_movimiento
		LEFT JOIN vehiculos vh ON m.id_vehiculo=vh.id
		LEFT JOIN micros i ON i.id=m.id_micro
		LEFT JOIN servicios s ON s.id = i.id_servicio
		LEFT JOIN centros c ON vh.id_centro=c.id
		LEFT JOIN tipos_vehiculos tvh ON vh.id_tipo_vehiculo=tvh.id
		LEFT JOIN turnos tur ON m.id_turno=tur.id
		where inicio::date >= '$fec1' AND inicio::date <='$fecha' AND kms2-kms1>250 and c.id = $correos[id_centro]
		order by 2,3"))
	{
		$qErrores=$db->sql_query("SELECT m.id, m.inicio::date as fecmov, vh.codigo as vehi, i.codigo as ruta,d.kms1, m.km_final, m.km_final-d.kms1 as 
					kms_recorrido FROM rec.movimientos m
					LEFT JOIN (select id_movimiento,min(km) as kms1,max(km) as kms2 from rec.desplazamientos GROUP BY id_movimiento) as d ON m.id=d.id_movimiento
  				LEFT JOIN vehiculos vh ON m.id_vehiculo=vh.id
					LEFT JOIN micros i ON i.id=m.id_micro
	  			LEFT JOIN servicios s ON s.id = i.id_servicio
					LEFT JOIN centros c ON vh.id_centro=c.id
					LEFT JOIN tipos_vehiculos tvh ON vh.id_tipo_vehiculo=tvh.id
		  		LEFT JOIN turnos tur ON m.id_turno=tur.id
					where inicio::date >= '$fec1' AND inicio::date <='$fecha' AND kms2-kms1>250 and c.id = $correos[id_centro]
					order by 2,3");
		while($error=$db->sql_fetchrow($qErrores)){
			$strMail .= "El vehiculo $error[vehi] para el día $error[fecmov] tiene kms de inicio $error[kms1] y de regreso $error[km_final] recorriendo $error[kms_recorrido] kms. \n";
			}
	}
	if($alerta=$db->sql_row("select fecha_entrada::date, v.codigo as vehi, peso_total
		from rec.pesos p
		left join vehiculos v on p.id_vehiculo=v.id
		left join rec.movimientos_pesos mp on p.id=mp.id_peso
		left join tipos_vehiculos tv on v.id_tipo_vehiculo=tv.id
		LEFT JOIN centros c ON v.id_centro=c.id
		where (p.peso_total>40 or p.peso_total<0) and 
		fecha_entrada::date >= '$fec1' AND fecha_entrada::date <='$fecha' AND c.id = $correos[id_centro]
		order by 2,3"))
		{
		$qErrores=$db->sql_query("select fecha_entrada::date, v.codigo as vehi, peso_total
					from rec.pesos p
					left join vehiculos v on p.id_vehiculo=v.id
					left join rec.movimientos_pesos mp on p.id=mp.id_peso
					left join tipos_vehiculos tv on v.id_tipo_vehiculo=tv.id
					LEFT JOIN centros c ON v.id_centro=c.id
					where (p.peso_total>40 or p.peso_total<0) and 
					fecha_entrada::date >= '$fec1' AND fecha_entrada::date <='$fecha' AND c.id = $correos[id_centro]
					order by 2,3");
		while($error=$db->sql_fetchrow($qErrores)){
				$strMail .= "El vehiculo $error[vehi] para el día $error[fecha_entrada] tiene un peso de $error[peso_total] toneladas.\n";
		}
	}
	if($alerta=$db->sql_row("SELECT m.id,inicio::date as fecmov,v.codigo||' / '||v.placa as vehiculo,a.ase,i.codigo as ruta,
						sum(rec.pesos.peso_total) as peso_total,despla.hora_inicio,m.final,(m.final-despla.hora_inicio) as horas_vehi,
						p.cedula,p.apellido||' '||p.nombre as nombres,c.nombre as cargo,
						per.hora_inicio as inicio_trip,per.hora_fin as fin_trip,(per.hora_fin-per.hora_inicio) as horas_trip
						FROM rec.movimientos m 
						LEFT JOIN (SELECT id_movimiento,min(hora_inicio) as hora_inicio, max(hora_fin) as hora_fin
								FROM rec.desplazamientos 
								where hora_inicio is not null
								group by id_movimiento) despla ON m.id=despla.id_movimiento
						LEFT JOIN rec.movimientos_personas per on m.id=per.id_movimiento
						LEFT JOIN rec.movimientos_pesos pes on m.id=pes.id_movimiento
						LEFT JOIN rec.pesos on pes.id_peso=rec.pesos.id
						LEFT JOIN micros i ON i.id=m.id_micro 
						LEFT JOIN vehiculos v ON v.id=m.id_vehiculo
						LEFT JOIN ases a ON a.id = i.id_ase
						LEFT JOIN personas p on per.id_persona=p.id
						LEFT JOIN cargos c on p.id_cargo=c.id	
						WHERE inicio::date >= '$fec1' AND inicio::date<='$fecha'
						AND a.id IN (SELECT id FROM ases WHERE id_centro = $correos[id_centro])
						and despla.hora_inicio is not null and  ((m.final-despla.hora_inicio)> '16 hours' or (per.hora_fin-per.hora_inicio)> '16 hours' or (m.final-despla.hora_inicio)<'0 hours')
						group by m.id,m.inicio,vehiculo,ase,ruta,despla.hora_inicio,final,horas_vehi,cedula,p.apellido,p.nombre,c.nombre,inicio_trip,fin_trip,horas_trip
						order by fecmov,vehiculo,despla.hora_inicio,c.nombre"))
	{
		$strMail .= "El vehiculo $alerta[vehiculo] para el día $alerta[fecmov] tiene un tiempo de opereracion $alerta[horas_vehi] ($alerta[final] - $alerta[hora_inicio]).\n";
		if ($alerta["cedula"]<>''){
			$strMail .= "Y en su tripulación el $alerta[cargo]- $alerta[nombres] tiene $alerta[horas_trip] horas laboradas ( $alerta[fin_trip] - $alerta[inicio_trip]).\n";
		}
	}
	
	$cabeceras = 'From: AIDA <aida@promoambientaldistrito.com>' . '\r\n';
	error_log("Enviando correo a " . $correos["email"]);
	mail($correos["email"],"Alerta Automatica de AIDA - Errores de digitacion",$strMail,$cabeceras);
	$strMail='';
}

?>
