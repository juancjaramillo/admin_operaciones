<?
include("../application.php");
die;

$i=0;
$idCentro=4;


// paso uno
$qid = $db->sql_query("SELECT * FROM mtto.equipos WHERE id_centro='".$idCentro."' ORDER BY id");
while($eq = $db->sql_fetchrow($qid))
{
	$datos=array($eq["id_grupo"]);
	$grupos = obtenerIdsGrupos($eq["id_grupo"],$datos);

	//averiguar rutinas de los grupos
	$cons = "
			SELECT * FROM (
			SELECT r.*,f.dias*24 as frec_horas_uno 
			FROM mtto.rutinas r
			LEFT JOIN mtto.tipos t ON t.id=r.id_tipo_mantenimiento
			LEFT JOIN mtto.frecuencias f ON f.id=r.id_frecuencia
			WHERE t.cron AND r.id_centro='".$idCentro."' AND r.id_grupo IN (".implode(",",$datos).")
			UNION
			SELECT r2.*,f2.dias*24 as frec_horas_uno 
			FROM mtto.rutinas r2
			LEFT JOIN mtto.tipos t2 ON t2.id=r2.id_tipo_mantenimiento
			LEFT JOIN mtto.frecuencias f2 ON f2.id=r2.id_frecuencia
			WHERE t2.cron AND r2.id_centro='".$idCentro."' AND r2.id_equipo = '".$eq["id"]."'
			) AS foo
			ORDER BY rutina" ;
	$qidRG = $db->sql_query($cons);
	while($rutGru = $db->sql_fetchrow($qidRG))
	{
		$fechaPlaneada = "2011-".rand(3,12)."-".rand(1,30)." ".rand(6,20).":".rand(1,59).":".rand(1,59);
		crearOrdenTrabajoFalsa($rutGru["id"],$eq["id"],$fechaPlaneada);
		$i++;
	}
}



echo "total i ".$i;
$i=0;



/*
$qid = $db->sql_query("SELECT * FROM mtto.equipos");
while($query = $db->sql_fetchrow($qid))
{
	if($query["id_vehiculo"] != '')
	{
		$vehi = $db->sql_row("SELECT kilometraje, horometro from vehiculos WHERE id=".$query["id_vehiculo"]);
		preguntar($vehi);
		if($vehi["kilometraje"] != '')
			$db->sql_query("UPDATE mtto.equipos SET kilometraje='".$vehi["kilometraje"]."' WHERE id=".$query["id"]);
		if($vehi["horometro"] != '')
			$db->sql_query("UPDATE mtto.equipos SET horometro ='".$vehi["horometro"]."' WHERE id=".$query["id"]);

	}
	$i++;
}
*/






$i=$j=1;
$cons = "SELECT ot.*, r.frec_horas, r.frec_km, f.dias,  to_char(ot.fecha_planeada,'YYYY-MM-DD') as fp, to_char(ot.fecha_planeada,'HH24:MI:SS') as hp, r.tiempo_ejecucion
	FROM mtto.ordenes_trabajo ot 
	LEFT JOIN mtto.rutinas r ON r.id=ot.id_rutina
	LEFT JOIN mtto.frecuencias f ON f.id=r.id_frecuencia
	WHERE ot.id_estado_orden_trabajo IN (SELECT id FROM mtto.estados_ordenes_trabajo WHERE NOT CERRADO )
	ORDER BY ot.id";
$qid = $db->sql_query($cons);
while($ot = $db->sql_fetchrow($qid))
{

	$num = $db->sql_row("SELECT count(id) as num FROM historico_recorrido WHERE id_equipo='".$ot["id_equipo"]."'");

	$promKmyHoras = $db->sql_row("SELECT sum(km) as kms, sum(horas) as horo 
		FROM historico_recorrido 
		WHERE id in (SELECT id FROM historico_recorrido WHERE id_equipo='".$ot["id_equipo"]."' ORDER BY fecha DESC)");

	if($ot["frec_km"] != "")
	{
		if($promKmyHoras["kms"]==0 || $promKmyHoras["kms"]=="") $promKmyHoras["kms"]=1;
		$dias = (($ot["frec_km"]*$num["num"])/$promKmyHoras["kms"])-5;
		list($anio,$mes,$dia)=split("-",$ot["fp"]);
		$sig = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) - $dias * 24 * 60 * 60);
		$hora = rand(6,20).":".rand(1,59).":".rand(1,59);
		$fechaFin =	SumarMinutosFechaStr($sig." ".$hora, $ot["tiempo_ejecucion"]);
		$db->sql_query("UPDATE mtto.ordenes_trabajo 
				SET 
					id_estado_orden_trabajo=2, 
					fecha_planeada='".$sig." ".$hora."', 
					fecha_ejecucion_inicio='".$sig." ".$hora."',
					fecha_ejecucion_fin='".$fechaFin."'
				WHERE id=".$ot["id"]);
		$j++;
	}
	
	if($ot["dias"] != "")
	{
		$rn = rand (0, $ot["dias"]);
		list($anio,$mes,$dia)=split("-",$ot["fp"]);
		$sig = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) - $rn * 24 * 60 * 60);
		$hora = rand(6,20).":".rand(1,59).":".rand(1,59);
		$fechaFin =	SumarMinutosFechaStr($sig." ".$hora, $ot["tiempo_ejecucion"]);
		$db->sql_query("UPDATE mtto.ordenes_trabajo 
				SET 
					id_estado_orden_trabajo=2, 
					fecha_planeada='".$sig." ".$hora."', 
					fecha_ejecucion_inicio='".$sig." ".$hora."',
					fecha_ejecucion_fin='".$fechaFin."'
				WHERE id=".$ot["id"]);

	}

	if($ot["frec_horas"] != "")
	{
		$rn = $ot["frec_horas"]/24;
		list($anio,$mes,$dia)=split("-",$ot["fp"]);
		$sig = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) - $rn * 24 * 60 * 60);
		$hora = rand(6,20).":".rand(1,59).":".rand(1,59);
		$fechaFin =	SumarMinutosFechaStr($sig." ".$hora, $ot["tiempo_ejecucion"]);
		$db->sql_query("UPDATE mtto.ordenes_trabajo 
				SET 
					id_estado_orden_trabajo=2, 
					fecha_planeada='".$sig." ".$hora."', 
					fecha_ejecucion_inicio='".$sig." ".$hora."',
					fecha_ejecucion_fin='".$fechaFin."'
				WHERE id=".$ot["id"]);
	}

	$i++;

}


$i=1;
$cons = "SELECT ot.*, to_char(ot.fecha_planeada,'YYYY-MM-DD') as fp, to_char(ot.fecha_planeada,'HH24:MI:SS') as hp
	FROM mtto.ordenes_trabajo ot 
	WHERE fecha_planeada > '2011-09-16 01:00:00' 
	ORDER BY ot.id";
$qid = $db->sql_query($cons);
while($ot = $db->sql_fetchrow($qid))
{
	$db->sql_query("UPDATE mtto.ordenes_trabajo
			SET
				id_estado_orden_trabajo=7,
				fecha_ejecucion_inicio=null,
				fecha_ejecucion_fin=null
			WHERE id=".$ot["id"]);

	$i++;
}


echo "FIN i ".$i;



function crearOrdenTrabajoFalsa($idRutina,$idEquipo,$fechaPlaneada)
{
	global $db,$CFG,$ME;

	$cargos = array(
		1 => array(14=>1, 12=>3, 15=>39, 16=>50),
		4 => array(14=>36, 12=>34, 15=>40, 16=>51)
		);

	$idCentro = $db->sql_row("SELECT id_centro FROM mtto.equipos WHERE id='".$idEquipo."'");
	$idCreador = $db->sql_row("SELECT id FROM personas WHERE nombre='Aida' AND apellido='Automático' AND id_centro=(SELECT id_centro FROM mtto.equipos WHERE id='".$idEquipo."')");

	$db->sql_query("INSERT INTO mtto.ordenes_trabajo (id_rutina, id_equipo, fecha_planeada, id_creador, id_estado_orden_trabajo) VALUES ('".$idRutina."','".$idEquipo."','".$fechaPlaneada."','".$idCreador["id"]."','7')");
	$id = $db->sql_nextid();
	$db->sql_query("INSERT INTO mtto.ordenes_trabajo_elementos (id_orden_trabajo, id_elemento, cantidad) SELECT '".$id."', id_elemento, cantidad FROM mtto.rutinas_elementos WHERE id_rutina='".$idRutina."'");

	insertarFechaProgramadaOT($id,$idCreador["id"],$fechaPlaneada);

	$qidRA = $db->sql_query("SELECT rac.* 
			FROM mtto.rutinas_actividades_cargos rac
			LEFT JOIN mtto.rutinas_actividades r ON r.id=rac.id_actividad
			WHERE r.id_rutina=".$idRutina);
	while($ra = $db->sql_fetchrow($qidRA))
	{
		$idPersona = $cargos[$idCentro["id_centro"]][$ra["id_cargo"]];
		$db->sql_query("INSERT INTO mtto.ordenes_trabajo_personas (id_orden_trabajo, id_rutina_actividad_cargo, id_persona, tiempo_ejecucion) VALUES ('".$id."','".$ra["id"]."','".$idPersona."','".$ra["tiempo"]."')");
	}
	

}



function SumarMinutosFechaStr($FechaStr, $MinASumar)
{
	$FechaStr = str_replace("-", " ", $FechaStr);
	$FechaStr = str_replace(":", " ", $FechaStr);

	$FechaOrigen = explode(" ", $FechaStr);

	$Dia = $FechaOrigen[2];
	$Mes = $FechaOrigen[1];
	$Anyo = $FechaOrigen[0];

	$Horas = $FechaOrigen[3];
	$Minutos = $FechaOrigen[4];
	$Segundos = $FechaOrigen[5];

	

	// Sumo los minutos
	$Minutos = ((int)$Minutos) + ((int)$MinASumar); 

	// Asigno la fecha modificada a una nueva variable
	$FechaNueva = date("Y-m-d H:i:s",mktime($Horas,$Minutos,$Segundos,$Mes,$Dia,$Anyo));

	return $FechaNueva;
}



?>
