<?
// echo "<pre>";
// print_r($_POST);
// print_r($_GET);
// echo "</pre>"; 

function dateDiff($start, $end) {
	$start_ts = strtotime($start);
	$end_ts = strtotime($end);
	$diff = $end_ts - $start_ts;
	return round($diff / 86400);
}

require_once("../application.php");


$fecactual = date ( "Y-m-d H:m:s" , time());
$id_rutina=$_POST["id_rutina"];

$consulta = "select *, 
	CASE  WHEN tipo_frec='km' then cast((ot_km+frecuencia) as text)
		WHEN tipo_frec='hr' then cast((ot_hr+frecuencia) as text)
		ELSE cast(ultima_ejecucion + cast(frecuencia ||' days' as interval)as text)
	END proximo
	from
	(select u.*, e.nombre, e.kilometraje as km_actual, e.horometro as hr_actual,
	r.rutina||' Frec. '|| CASE WHEN r.frec_km<>0 or r.frec_km=0 THEN cast(r.frec_km as char(6))||' kms' WHEN r.frec_horas<>0 
	THEN cast(r.frec_horas as char(6))||' hrs' ELSE (select frecuencia from mtto.frecuencias where id in(r.id_frecuencia)) END as rutina,
	CASE WHEN r.frec_km<>0 or r.frec_km=0 THEN r.frec_km  WHEN r.frec_horas<>0 
	THEN r.frec_horas ELSE (select dias from mtto.frecuencias where id in(r.id_frecuencia)) END as frecuencia,
	CASE WHEN r.frec_km<>0 or r.frec_km=0 THEN 'km'  WHEN r.frec_horas<>0 
	THEN 'hr' ELSE 'dias' END as tipo_frec,
	ot.km as ot_km, ot.horometro as ot_hr
	from
	(select o.id_equipo,o.id_rutina,max(o.fecha_ejecucion_inicio) as ultima_ejecucion
	from mtto.ordenes_trabajo o
	where o.id_rutina=$id_rutina and o.fecha_ejecucion_inicio is not null
	group by o.id_equipo,o.id_rutina
	order by o.id_equipo) u
	left join mtto.equipos e on u.id_equipo=e.id 
	left join mtto.rutinas r on u.id_rutina=r.id
	left join mtto.ordenes_trabajo ot on 
	(u.id_equipo=ot.id_equipo and u.id_rutina=ot.id_rutina and u.ultima_ejecucion=ot.fecha_ejecucion_inicio)) eje
	order by nombre";
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="http://64.131.77.126/pa/css/cfc.css" />
<link rel="stylesheet" type="text/css" href="http://64.131.77.126/pa/css/paginator.css" />
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.9.0/build/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.9.0/build/datatable/assets/skins/sam/datatable.css" />
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.6.0/build/autocomplete/assets/skins/sam/autocomplete.css" />
</head>
<body>

<table width="100%" align = "center">
	<tr>
		<td align="center" height="40" valign="bottom"><span class="azul_12"><strong>Ultimo Programa de mantenimiento Preventivo Realizado</strong></span></td>
	</tr>
</table>

<?php
$qids = $db->sql_query($consulta);
if($db->sql_numrows($qids)){
	echo '<table align="center" width="90%" border=1 bordercolor="#7fa840" id="tabla_actividades">
			<tr>
				<td align="center">RUTINA</td>
				<td align="center">EQUIPO</td>
				<td align="center">KMS ACTUAL</td>
				<td align="center">HORAS ACTUAL</td>
				<td align="center">FECHA ULTIMA EJECUCIÓN</td>
				<td align="center">EJECTUADO</td>
				<td align="center">PROXIMA EJECUCIÓN</td>
				<td align="center">DIFERENCIA</td>
				<td align="center">PROXIMA OT</td>
				<td align="center">FECHA PROXIMA OT</td>
			</tr>';
	while($dat = $db->sql_fetchrow($qids)){
		if ($dat["tipo_frec"]=='km'){
			$calcular=number_format($dat["proximo"]-$dat["km_actual"],0);
			$ejecutado=number_format($dat["ot_km"],0);
			$proximo=number_format($dat["proximo"],0);
		}
		if ($dat["tipo_frec"]=='hr'){
			$calcular=number_format($dat["proximo"]-$dat["hr_actual"],0);
			$ejecutado=number_format($dat["ot_hr"],0);
			$proximo=number_format($dat["proximo"],0);
		}
		if ($dat["tipo_frec"]=='dias'){
			$proximo=$dat["proximo"];
			$calcular= dateDiff($fecactual,$proximo);
			$ejecutado=$dat["ultima_ejecucion"]; 
			
		}
	
		echo "<tr>
				<td>".$dat["rutina"]."</td>
				<td>".$dat["nombre"]."</td>
				<td align='right'>".number_format($dat["km_actual"],0)."</td>
				<td align='right'>".number_format($dat["hr_actual"],0)."</td>
				<td align='right'>".$dat["ultima_ejecucion"]."</td>
				<td align='right'>".$ejecutado."</td>
				<td align='right'>".$proximo."</td>
				<td align='right'>".$calcular."</td>";
				$consulta1 = "select ot.id,ot.fecha_planeada from 
											(select o.id_equipo,o.id_rutina,min(o.fecha_planeada) as fecha_planeada
								 				from mtto.ordenes_trabajo o
												where o.id_rutina=$id_rutina and o.fecha_ejecucion_inicio is null and id_equipo=".$dat["id_equipo"]."
												group by 1,2) n
											left join mtto.ordenes_trabajo ot on 
											(n.id_equipo=ot.id_equipo and n.id_rutina=ot.id_rutina and n.fecha_planeada=ot.fecha_planeada)";
				$qids1 = $db->sql_query($consulta1);
				while($dat1 = $db->sql_fetchrow($qids1)){
						echo "<td align='right'>".$dat1["id"]."</td>
									<td align='right'>".$dat1["fecha_planeada"]."</td>";
				}
				echo"</tr>";
	}
	echo "</table>";
}
?>
</html>
