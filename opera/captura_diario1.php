<?PHP
echo "<pre>";
print_r($_POST);
print_r($_GET);
echo "</pre>"; 

include_once("../application.php");

if(!isset($_SESSION[$CFG->sesion]["user"])){
  $errorMsg="No existe la sesión.";
  error_log($errorMsg);
  die($errorMsg);
}
global $db,$CFG,$ME;

$hora=time();
// Aqui debe traer la fecha de trabajo
$fecha= $_GET["fecha"];
$id_turno=$_POST["id_turno"];
$vehiculo=$_POST["vehiculo"];
$id_movimiento=$_POST["id_movimiento"];
$fecmov= nvl($_POST["fecmov"]);

if ($fecmov==''){
  $fecmov = date ( "Y-m-d" , $hora);
}
if(!isset($condicion)) $condicion="";
if ($id_movimiento<>""){
	$condicion=" AND mov.id=".$id_movimiento;
}

//Opción guardar desplazamiento cuando cambia el km o el horometro
if (nvl($_POST["accion"])=="Guardar"){
	$gid_desplazamiento=$_POST["id_desplazamiento"];
	$gid_desplazant=$_POST["id_desplazant"];
	$id_movimiento=$_POST["id_movimiento"];
	$gviaje=$_POST[$gid_desplazamiento."_viaje"];
	$ghorainicio = $_POST[$gid_desplazamiento."_fecha_desplaza"]." ".$_POST[$gid_desplazamiento."_horainicio"];
	$gkm=$_POST[$gid_desplazamiento."_km"];	
	$ghorometro=$_POST[$gid_desplazamiento."_horometro"];

	/*log*/
	$ant = $db->sql_row("SELECT t.tipo, d.* FROM rec.desplazamientos d LEFT JOIN rec.tipos_desplazamientos t ON t.id=d.id_tipo_desplazamiento WHERE d.id=".$gid_desplazamiento);
	/**/

	$guardar1= "UPDATE rec.desplazamientos SET hora_inicio='".$ghorainicio."',numero_viaje='".$gviaje."', km='".$gkm."', 
	horometro='".$ghorometro."'	WHERE id='".$gid_desplazamiento."';";
	$qid = $db->sql_query($guardar1);
	
	$guardar2= "UPDATE rec.desplazamientos SET hora_fin='".$ghorainicio."' WHERE id='".$gid_desplazant."';";
	$qid = $db->sql_query($guardar2);
	
	$guardar3="UPDATE rec.desplazamientos SET hora_fin = hora_fin+'1 days' where id='$gid_desplazant' and hora_fin<hora_inicio";
	$qid = $db->sql_query($guardar3);
	
	$guardar4="UPDATE rec.desplazamientos SET hora_inicio=hora_inicio + '1 days' WHERE id='$gid_desplazamiento' and hora_inicio < (select hora_fin from rec.desplazamientos where id='$gid_desplazant')";
	$qid = $db->sql_query($guardar4);


	/*log*/
	$new = $db->sql_row("SELECT t.tipo, d.* FROM rec.desplazamientos d LEFT JOIN rec.tipos_desplazamientos t ON t.id=d.id_tipo_desplazamiento WHERE d.id=".$gid_desplazamiento);
	$accion = "Actualizó desplazamiento\nTipo: dato anterior: ".$ant["tipo"]." | dato nuevo: ".$new["tipo"]."\nInicio: dato anterior: ".$ant["hora_inicio"]." | dato nuevo: ".$new["hora_inicio"]."\nFinal: dato anterior: ".$ant["hora_fin"]." | dato nuevo: ".$new["hora_fin"]."\nViaje: dato anterior: ".$ant["numero_viaje"]." | dato anterior: ".$new["numero_viaje"]."\nKm: dato anterior: ".$ant["km"]." | dato nuevo: ".$new["km"]."\nHorometro: dato anterior: ".$ant["horometro"]." | dato nuevo: ".$new["horometro"];
	ingresarLogMovimiento("rec", $id_movimiento, $accion);
	/*fin log*/
	actualizarKmDesdeMovODes("",$gid_desplazamiento);
	actualizarHoroDesdeMovODes("",$gid_desplazamiento);
}	
//Elimina un tripulaciones 
if ($_POST["accion"]=="Eliminartrip"){
	$gid_trip=$_POST["id_trip"];

	/*log*/
	$per = $db->sql_row("SELECT p.nombre||' '||p.apellido as nombre, c.nombre as nombre_cargo, mp.* 
			FROM rec.movimientos_personas mp 
			LEFT JOIN personas p ON p.id=mp.id_persona 
			LEFT JOIN cargos c ON c.id = mp.cargo 
			WHERE mp.id=".$gid_trip);
	/**/
	$guardar1= "delete from rec.movimientos_personas WHERE id='".$gid_trip."';";
	$qid = $db->sql_query($guardar1);

	/*log*/
	$accion = "Borró Operario al movimiento\nPersona: ".$per["nombre"]."\nCargo: ".$per["nombre_cargo"]."\nInicio: ".$per["hora_inicio"]."\nFinal: ".$per["hora_fin"];
	ingresarLogMovimiento("rec", $per["id_movimiento"], $accion);
	/*fin log*/
}
//Guarda Cambios en horas de la tripulaciones
if ($_POST["accion"]=="Guardartrip"){
	$gid_trip=$_POST["id_trip"];
	$horainitrip = $_POST[$gid_trip.'_horainicio'];
	$horafintrip = $_POST[$gid_trip.'_hora_fin'];

	/*log*/
	$ant = $db->sql_row("SELECT p.nombre||' '||p.apellido as nombre, c.nombre as nombre_cargo, mp.* 
		FROM rec.movimientos_personas mp 
		LEFT JOIN personas p ON p.id=mp.id_persona 
		LEFT JOIN cargos c ON c.id = mp.cargo 
		WHERE mp.id=".$gid_trip);
	/**/

	if ($horafintrip==' '){
		$guardar1= "update rec.movimientos_personas set hora_inicio = '$horainitrip', hora_fin = '$horafintrip' WHERE id='".$gid_trip."'";}
	else{
		$guardar1= "update rec.movimientos_personas set hora_inicio = '$horainitrip' WHERE id='".$gid_trip."'";}
	$qid = $db->sql_query($guardar1);
	
	/*log*/
	$new = $db->sql_row("SELECT p.nombre||' '||p.apellido as nombre, c.nombre as nombre_cargo, mp.* 
		FROM rec.movimientos_personas mp 
		LEFT JOIN personas p ON p.id=mp.id_persona 
		LEFT JOIN cargos c ON c.id = mp.cargo 
		WHERE mp.id=".$gid_trip);
	$accion = "Actualizó Operario al movimiento\nPersona: dato anterior: ".$ant["nombre"]." | dato nuevo: ".$new["nombre"]."\nCargo: dato anterior: ".$ant["nombre_cargo"]." | dato nuevo: ".$new["nombre_cargo"]."\nInicio: dato anterior: ".$ant["hora_inicio"]." | dato nuevo: ".$new["hora_inicio"]."\nFinal: dato anterior: ".$ant["hora_fin"]." | dato nuevo: ".$new["hora_fin"];
	ingresarLogMovimiento("rec", $ant["id_movimiento"], $accion);
	/*fin log*/

}

//Elimina un desplazamiento 
if ($_POST["accion"]=="Eliminardespl"){
	$gid_desplazamiento=$_POST["id_desplazamiento"];
	$gid_desplazant=$_POST["id_desplazant"];

	/*log*/
	$ant = $db->sql_row("SELECT t.tipo, d.* FROM rec.desplazamientos d LEFT JOIN rec.tipos_desplazamientos t ON t.id=d.id_tipo_desplazamiento WHERE d.id=".$gid_desplazamiento);
	$accion = "Eliminó desplazamiento\nTipo: ".$ant["tipo"]."\nInicio: ".$ant["hora_inicio"]."\nFinal: ".$ant["hora_fin"]."\nViaje: ".$ant["numero_viaje"]."\nKm: ".$ant["km"]."\nHorometro:".$ant["horometro"];
	ingresarLogMovimiento("rec", $ant["id_movimiento"], $accion);
	/*fin log*/
	
	$guardar1= "delete from rec.desplazamientos WHERE id='".$gid_desplazamiento."';";
	$qid = $db->sql_query($guardar1);
	
	$guardar2= "UPDATE rec.desplazamientos SET hora_fin=null WHERE id='".$gid_desplazant."';";
	$qid = $db->sql_query($guardar2);
}

if ($_POST["accion"]=="Eliminardescargue"){
	$gidpesoborrar=$_POST["idpesoborrar"];

	/*log*/
	$idMovimiento = $db->sql_row("select id, id_peso from rec.movimientos_pesos WHERE id_peso=".$gidpesoborrar);
	$ant = $db->sql_row("SELECT v2.codigo||' ('||v2.placa||') /' || p.fecha_entrada ||'/'|| l.nombre ||'/'|| c.centro ||'/'|| COALESCE(tiquete_entrada,'') as dato_peso, mp.porcentaje, mp.viaje, mp.id_movimiento, mp.id_peso
			FROM rec.movimientos_pesos mp
			LEFT JOIN rec.movimientos m ON m.id = mp.id_movimiento
			LEFT JOIN rec.pesos p ON p.id=mp.id_peso
			LEFT JOIN vehiculos v2 ON v2.id = p.id_vehiculo
			LEFT JOIN lugares_descargue l ON l.id=p.id_lugar_descargue
			LEFT JOIN centros c ON c.id=l.id_centro
		WHERE mp.id= ".$idMovimiento["id"]);
	$accion="Borró Peso\nPeso: ".$ant["dato_peso"]."\nPorcentaje: ".$ant["porcentaje"]."\nViaje: ".$ant["viaje"];
	ingresarLogMovimiento("rec", $ant["id_movimiento"], $accion);
	/*fin log*/

	$guardar1= "delete from rec.pesos WHERE id='".$gidpesoborrar."';";
	$qid = $db->sql_query($guardar1);
	$guardar1= "delete from rec.movimientos_pesos WHERE id_peso='".$gidpesoborrar."';";
	$qid = $db->sql_query($guardar1);
	actualizarPesoAsignado(0,true,$idMovimiento["id_peso"]);
}

if (nvl($_POST["accion"])=="Eliminarapoyo"){
	$gidapoyoborrar=$_POST["idapoyoborrar"];

	/*log*/
	$ant = $db->sql_row("SELECT codigo||'/'||placa as codigo, a.* FROM rec.apoyos a LEFT JOIN vehiculos v ON v.id=a.id_vehiculo WHERE a.id=".$gidapoyoborrar);
	$qidBA = $db->sql_query("SELECT * FROM rec.apoyos_movimientos WHERE id_apoyo=".$gidapoyoborrar);
	while($queryBA = $db->sql_fetchrow($qidBA))
	{
		$accion = "Borró Apoyo\nFecha:".$ant["inicio"]."\nVehículo: ".$ant["codigo"]."\nPeso Total: ".$ant["peso"]."\nKm Inicial: ".$ant["km_inicial"]."\nKm Final: ".$ant["km_final"]."\nFecha Final: ".$ant["final"];
		ingresarLogMovimiento("rec", $queryBA["id_movimiento"], $accion);	
	}
	/*fin log*/

	$guardar1= "delete from rec.apoyos_movimientos WHERE id_apoyo='".$gidapoyoborrar."';";
	$qid = $db->sql_query($guardar1);
	$guardar1= "delete from rec.apoyos WHERE id='".$gidapoyoborrar."';";
	$qid = $db->sql_query($guardar1);

}
//agregar una tripulacion
if ($_POST["accion"]=="tripulacion"){
	$nuevapersona=$_POST["nuevapersona"];
	$triphorainicio = $fecmov." ".$_POST["triphorainicio"];
	$id_movimiento=$_POST["id_movimientodes"];
	
	$consulant = "select id_cargo from personas where id=$nuevapersona";
	$qidant = $db->sql_query($consulant);
	while($desant = $db->sql_fetchrow($qidant))
	{
		$id_cargo = $desant["id_cargo"];
	}
	
	$guardar2= "insert into rec.movimientos_personas (id_movimiento,cargo,id_persona,hora_inicio) 
	 values ($id_movimiento,$id_cargo,$nuevapersona,'$triphorainicio')";
	$qid = $db->sql_query($guardar2);
	$id = $db->sql_nextid();

	/*log*/
	$per = $db->sql_row("SELECT p.nombre||' '||p.apellido as nombre, c.nombre as nombre_cargo, mp.* 
		FROM rec.movimientos_personas mp 
		LEFT JOIN personas p ON p.id=mp.id_persona 
		LEFT JOIN cargos c ON c.id = mp.cargo 
		WHERE mp.id=".$id);
	$accion = "Ingresó Operario al movimiento\nPersona: ".$per["nombre"]."\nCargo: ".$per["nombre_cargo"]."\nInicio: ".$per["hora_inicio"]."\nFinal: ".$per["hora_fin"];
	ingresarLogMovimiento("rec", $per["id_movimiento"], $accion);
	/*fin log*/
}

//Opción guardar nuevo desplazamiento
if ($_POST["accion"]=="desplazamientos"){
	
      /// Consulto el ultimo kilometraje digitado.
			$consultakms = "SELECT (d.hora_inicio),(cast(km as float)) as km, (cast(d.horometro as float)) as horo,min(m.km_final) as ks_final
					FROM rec.desplazamientos d
					left join rec.movimientos m on m.id=d.id_movimiento
					left join vehiculos v on v.id=m.id_vehiculo
					where v.codigo like '".$vehiculo."%' and m.inicio::date <= '".$_GET["fecha"]."' and m.id=".$_POST[id_mov_cerrar]."
				 	group by d.hora_inicio,d.km,d.horometro order by 1 desc  limit 2";
			#echo $consultakms;
			$qidkms=@$db->sql_query($consultakms);
			if(isset($qidkms)) {
					$consultakms = "SELECT cast(kilometraje as integer) as km, cast(horometro as integer) as horo FROM vehiculos
								where codigo like '".$vehiculo."%' and id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')";
						$qidkms=@$db->sql_query($consultakms);
			}
			while($rowkms = $db->sql_fetchrow($qidkms))
			{
				$kmsanterior = $rowkms["km"];
				$horanterior = $rowkms["horo"];
				$ks_final = $rowkms["ks_final"];
				if ($ks_final>$kmsanterior) $kmsanterior=$ks_final;			
			}
		$kmsmayor = $kmsanterior +130;

		if ($_POST[nuevokm]<$kmsanterior ||$_POST[nuevokm]>$kmsmayor){
    ?>	<script type='text/javascript'>
						alert('NO SE GUARDO, El Odometro debe ser mayor a <?PHP echo $kmsanterior." y menor a ". $kmsmayor ?>');
				</script>
		<?PHP
		} 
    else{
				$ghorainicio = $fecmov." ".$_POST["nuevahorainicio"];
	
				$consulant = "select id, id_movimiento from rec.desplazamientos where hora_inicio is not null and hora_fin is null 
				and id_movimiento=$_POST[id_movimientodes]";
				$qidant = $db->sql_query($consulant);
				while($desant = $db->sql_fetchrow($qidant))
				{
					$desplaant = $desant["id"];
				}
	
					$guardar1= "INSERT INTO rec.desplazamientos(id_movimiento, id_tipo_desplazamiento, hora_inicio, numero_viaje, km, horometro) 
						values (".$_POST["id_movimientodes"].",".$_POST["nuevodesplaza"].",'".$ghorainicio."',1,'".$_POST["nuevokm"]."','".$_POST["nuevahorometro"]."')";
					$qid = $db->sql_query($guardar1);
					$idDesplazamiento = $db->sql_nextid();	
				  if ($desplaant<>"") {
						$guardar2= "UPDATE rec.desplazamientos SET hora_fin='".$ghorainicio."' WHERE id='".$desplaant."';";
						$qid = $db->sql_query($guardar2);
						$guardar3="UPDATE rec.desplazamientos SET hora_fin = hora_fin+'1 days' where id='$desplaant' and hora_fin<hora_inicio";
						$qid = $db->sql_query($guardar3);
				}
				$consulnuevo = "select id, id_movimiento from rec.desplazamientos where hora_inicio is not null and hora_fin is null 
				and id_movimiento=$_POST[id_movimientodes]";
				$qidnuevo = $db->sql_query($consulnuevo);
				while($desnuevo = $db->sql_fetchrow($qidnuevo))
				{
					$desplanuevo = $desnuevo["id"];
				}
				if ($desplaant<>"" and $desplanuevo<>"") {
					$guardar4="UPDATE rec.desplazamientos SET hora_inicio=hora_inicio + '1 days' WHERE id='$desplanuevo' and 
									hora_inicio < (select hora_fin from rec.desplazamientos where id='$desplaant')";
					$qid = $db->sql_query($guardar4);	
				}
				/*log*/
				$ant = $db->sql_row("SELECT t.tipo, d.* FROM rec.desplazamientos d LEFT JOIN rec.tipos_desplazamientos t ON t.id=d.id_tipo_desplazamiento WHERE d.id=".$idDesplazamiento);
				$accion = "Insertó desplazamiento\nTipo: ".$ant["tipo"]."\nInicio: ".$ant["hora_inicio"]."\nFinal: ".$ant["hora_fin"]."\nViaje: ".$ant["numero_viaje"]."\nKm: ".$ant["km"]."\nHorometro: ".$ant["horometro"];
				ingresarLogMovimiento("rec", $ant["id_movimiento"], $accion);
				/*fin log*/
	}
}

// Opción para agregar descargue
if ($_POST["accion"]=="descargues"){
	$qidvehi = $db->sql_query("select vehiculos.id, vehiculos.codigo, vehiculos.id_centro, centros.id_empresa from vehiculos
		left join centros on vehiculos.id_centro=centros.id
		WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')
		and vehiculos.codigo like '$_POST[vehiculo]%'
		order by codigo");
	while($des = $db->sql_fetchrow($qidvehi))
	{
		$idvehi = $des["id"];
		$idcentro =$des["id_centro"];
	}
	$ghoradescargue = $fecmov." ".$_POST["deshorainicio"];
	if ($idcentro=="1" || $idcentro=="2"){
		$guardar1= "INSERT INTO rec.pesos(peso_total,id_lugar_descargue,tiquete_entrada, fecha_entrada,id_vehiculo, reparte)
			values ('".$_POST["pesoinicial"]."','".$_POST["id_sitiodesc"]."','".$_POST["tiqentrada"]."','".$ghoradescargue."','".$idvehi."',False)";
	}
	else {
		$guardar1= "INSERT INTO rec.pesos(peso_inicial,id_lugar_descargue,tiquete_entrada, fecha_entrada,id_vehiculo, reparte)
		values ('".$_POST["pesoinicial"]."','".$_POST["id_sitiodesc"]."','".$_POST["tiqentrada"]."','".$ghoradescargue."','".$idvehi."',False)";
	}
	$qid = $db->sql_query($guardar1);
	$idBuscarPeso = $db->sql_nextid();

	if ($idcentro=="1" || $idcentro=="2"){
		$qidpeso = $db->sql_query("select id from rec.pesos where peso_total='".$_POST["pesoinicial"]."' and id_lugar_descargue='".$_POST["id_sitiodesc"]."' and
		tiquete_entrada='".$_POST["tiqentrada"]."' and fecha_entrada='".$ghoradescargue."' and id_vehiculo='".$idvehi."'");
	}
	else
	{
		$qidpeso = $db->sql_query("select id from rec.pesos where peso_inicial='".$_POST["pesoinicial"]."' and id_lugar_descargue='".$_POST["id_sitiodesc"]."' and
		tiquete_entrada='".$_POST["tiqentrada"]."' and fecha_entrada='".$ghoradescargue."' and id_vehiculo='".$idvehi."'");
	}
	while($despes = $db->sql_fetchrow($qidpeso))
	{
		$idpeso = $despes["id"];
	}
	$guardar2= "INSERT INTO rec.movimientos_pesos(id_peso, id_movimiento, porcentaje, viaje)
	values ('".$idpeso."','".$_POST["id_movimientodesc"]."','100','1')";
	$qid = $db->sql_query($guardar2);
	$idNuevoMovPeso =  $db->sql_nextid();
	actualizarPesoAsignado($idNuevoMovPeso);

	/*log*/
	$new = $db->sql_row("SELECT v2.codigo||' ('||v2.placa||') /' || p.fecha_entrada ||'/'|| l.nombre ||'/'|| c.centro ||'/'|| COALESCE(tiquete_entrada,'') as dato_peso
			FROM rec.pesos p 
			LEFT JOIN vehiculos v2 ON v2.id = p.id_vehiculo
			LEFT JOIN lugares_descargue l ON l.id=p.id_lugar_descargue
			LEFT JOIN centros c ON c.id=l.id_centro
		WHERE p.id=".$idBuscarPeso);
	$accion="Ingresó Peso\nPeso: ".$new["dato_peso"]."\nPorcentaje: 100\nViaje: 1";
	ingresarLogMovimiento("rec", $_POST["id_movimientodesc"], $accion);
	/*fin log*/

}
	
//Opción para hacer cambios en el descargue.
if ($_POST["accion"]=="Guardardesc"){
	$id_descargue=$_POST["id_descargue"];
	$gpesini=$_POST[$id_descargue."_pesini"];
	$gpesfin=$_POST[$id_descargue."_pesfin"];
	$gpestot=$_POST[$id_descargue."_pestot"];
	$gtiqent=$_POST[$id_descargue."_tiqent"];
	$gtiqsal=$_POST[$id_descargue."_tiqsal"];
	$gfecent=$_POST[$id_descargue."_fecent"];
	$gfecsal=$_POST[$id_descargue."_fecsal"];
	if ($gfecsal=='') $gfecsal= $gfecent;

	if ($gpesini <> "" and $gpesfin<>"" ){
		$gpestot = $gpesini-$gpesfin;
	}

	if ($gpestot <> "" and $gpesini<>"" ){
		$gpesfin = $gpesini-$gpestot;
	}
	
	$qidvehi = $db->sql_query("select vehiculos.id, vehiculos.codigo, vehiculos.id_centro, centros.id_empresa from vehiculos
		left join centros on vehiculos.id_centro=centros.id
		WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')
		and vehiculos.codigo like '$_POST[vehiculo]%'
		order by codigo");
	
	while($des = $db->sql_fetchrow($qidvehi))
	{
		$idvehi = $des["id"];
	}
	
	$cerrado = $db->sql_row("SELECT cerrado FROM rec.pesos WHERE id=".$id_descargue);
	if($cerrado["cerrado"] == "f")
	{
		$guardar1= "UPDATE rec.pesos SET peso_inicial='$gpesini', peso_final='$gpesfin', peso_total='$gpestot',  
			tiquete_entrada='$gtiqent', tiquete_salida='$gtiqsal', fecha_entrada='$gfecent', fecha_salida='$gfecsal', 
			id_vehiculo='$idvehi' WHERE id='$id_descargue'";	
		$qid = $db->sql_query($guardar1);
		$guardar1= "update rec.pesos SET fecha_salida=fecha_salida + '1 days'
				where id='$id_descargue' and fecha_salida < fecha_entrada";	
		$qid = $db->sql_query($guardar1);
	}
}

if ($_POST["accion"]=="repartir"){

	$sqlpeso  = $db->sql_query("select rec.movimientos_pesos.*,rec.pesos.peso_total from rec.movimientos_pesos 
				left join rec.pesos on rec.pesos.id=rec.movimientos_pesos.id_peso 
				where id_movimiento=$_POST[id_movimientorep] and id_peso=$_POST[pesoseleccionadorepar]");
	while($des = $db->sql_fetchrow($sqlpeso))
	{
		$idmovpeso = $des["id"];
		$idpesotot = $des["peso_total"];
	}
	$porcentaje = ($_POST["pesorepartir"]/$idpesotot)*100;
	/*log*/
	$ant = $db->sql_row("SELECT v2.codigo||' ('||v2.placa||') /' || p.fecha_entrada ||'/'|| l.nombre ||'/'|| c.centro ||'/'|| COALESCE(tiquete_entrada,'') as dato_peso, mp.porcentaje, mp.viaje, mp.id_movimiento
			FROM rec.movimientos_pesos mp
			LEFT JOIN rec.movimientos m ON m.id = mp.id_movimiento
			LEFT JOIN rec.pesos p ON p.id=mp.id_peso
			LEFT JOIN vehiculos v2 ON v2.id = p.id_vehiculo
			LEFT JOIN lugares_descargue l ON l.id=p.id_lugar_descargue
			LEFT JOIN centros c ON c.id=l.id_centro
		WHERE mp.id=".$idmovpeso);
	/*---*/

	$guardar1= "UPDATE rec.movimientos_pesos SET porcentaje=porcentaje-$porcentaje WHERE id='$idmovpeso'";	
	$qid = $db->sql_query($guardar1);
	actualizarPesoAsignado($idmovpeso);

	/*log*/
	$new = $db->sql_row("SELECT v2.codigo||' ('||v2.placa||') /' || p.fecha_entrada ||'/'|| l.nombre ||'/'|| c.centro ||'/'|| COALESCE(tiquete_entrada,'') as dato_peso, mp.porcentaje, mp.viaje, mp.id_movimiento
			FROM rec.movimientos_pesos mp
			LEFT JOIN rec.movimientos m ON m.id = mp.id_movimiento
			LEFT JOIN rec.pesos p ON p.id=mp.id_peso
			LEFT JOIN vehiculos v2 ON v2.id = p.id_vehiculo
			LEFT JOIN lugares_descargue l ON l.id=p.id_lugar_descargue
			LEFT JOIN centros c ON c.id=l.id_centro
		WHERE mp.id=".$idmovpeso);
	$accion="Actualizó Peso\nPeso: dato anterior: ".$ant["dato_peso"]." | nuevo dato: ".$new["dato_peso"]."\nPorcentaje: dato anterior: ".$ant["porcentaje"]." | nuevo dato: ".$new["porcentaje"]."\nViaje: dato anterior: ".$ant["viaje"]." | nuevo dato: ".$new["viaje"];
	ingresarLogMovimiento("rec", $ant["id_movimiento"], $accion);
	/*fin log*/
	
	$guardar3= "UPDATE rec.pesos SET reparte=true, asignado=true WHERE id='$_POST[pesoseleccionadorepar]'";	
	$qid = $db->sql_query($guardar3);
	
	$guardar2= "INSERT INTO rec.movimientos_pesos(id_peso, id_movimiento, porcentaje, viaje)
	values ('".$_POST["pesoseleccionadorepar"]."','".$_POST["movimientoselecrepartir"]."','$porcentaje','1')";
	$qid = $db->sql_query($guardar2);
	

}

if ($_POST["accion"]=="Guardarapoyo"){
	$id_apoyo=$_POST["idapoyo"];
	$gapoyokfi=$_POST[$id_apoyo."_apoyokfi"];
	$gapoyokin=$_POST[$id_apoyo."_apoyokin"];
	$gapoyopes=$_POST[$id_apoyo."_apoyopes"];
	$gapoyofin=$_POST[$id_apoyo."_apoyofin"];
  if ( $gapoyopes==''){
	  $guardar1= "UPDATE rec.apoyos SET km_inicial='$gapoyokin', km_final='$gapoyokfi', final='$gapoyofin'
		 WHERE id=$id_apoyo";
	}
	
	else {
		$guardar1= "UPDATE rec.apoyos SET peso='$gapoyopes',km_inicial='$gapoyokin', km_final='$gapoyokfi', final='$gapoyofin'
		WHERE id=$id_apoyo";
	}

	$qid = $db->sql_query($guardar1);
 	$guardar1= "UPDATE rec.apoyos SET final=final + '1 days'
				WHERE id=$id_apoyo and final < inicio";
	$qid = $db->sql_query($guardar1);

}

if ($_POST["accion"]=="apoyos"){
	$qidvehi = $db->sql_query("select vehiculos.id, vehiculos.codigo, vehiculos.id_centro, centros.id_empresa from vehiculos
		left join centros on vehiculos.id_centro=centros.id
		WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')
		and vehiculos.codigo like '$_POST[vehiculo]%'
		order by codigo");
	while($des = $db->sql_fetchrow($qidvehi))
	{
		$idvehi = $des["id"];
	}
	
	if ($_POST["horaturno"] > $_POST["aphrini"]) 
	{
		$fecmov= date("Y-m-d", strtotime("$fecmov + 1 days"));
	}
	$gapoyoini= $fecmov." ".$_POST["aphrini"];
	
	$guardar1= "INSERT INTO rec.apoyos(id_vehiculo, inicio, km_inicial)
    VALUES ('$idvehi','$gapoyoini','$_POST[apkmini]')";
	$qid = $db->sql_query($guardar1);
	
	$qidapoyo = $db->sql_query("select id from rec.apoyos where id_vehiculo='".$idvehi."' and inicio='".$gapoyoini."' and
	km_inicial='".$_POST["apkmini"]."'");
	while($desapoyo = $db->sql_fetchrow($qidapoyo))
	{
		$idapoyonew = $desapoyo["id"];
	}
	
	$guardar2= "INSERT INTO rec.apoyos_movimientos(id_apoyo, id_movimiento)
	values ('".$idapoyonew."','".$_POST["apruta"]."')";
$qid = $db->sql_query($guardar2);

	/*log*/
	$veh = $db->sql_row("SELECT codigo||'/'||placa as codigo FROM vehiculos WHERE id=".$idvehi);
	$accion = "Insertó Apoyo\nFecha:".$gapoyoini."\nVehículo: ".$veh["codigo"]."\nKm Inicial: ".$_POST["apkmini"];
	ingresarLogMovimiento("rec", $idapoyonew, $accion);	
	/*fin log*/

}
//CERRAMOS EL VIAJE
if ($_POST["accion"]=="cerrarviaje"){
      /// Consulto el ultimo kilometraje digitado.
			$consultakms = "SELECT (d.hora_inicio),(cast(km as float)) as km, (cast(d.horometro as float)) as horo FROM rec.desplazamientos d
					left join rec.movimientos m on m.id=d.id_movimiento
					left join vehiculos v on v.id=m.id_vehiculo
					where v.codigo like '".$vehiculo."%' and m.inicio::date <= '".$_GET["fecha"]."' and m.id=".$_POST[id_mov_cerrar]."
				 	group by d.hora_inicio,d.km,d.horometro order by 1 desc  limit 2";
			$qidkms=@$db->sql_query($consultakms);
			while($rowkms = $db->sql_fetchrow($qidkms))
			{
					$kmsanterior = $rowkms["km"];
					$horanterior = $rowkms["horo"];
			}	
			$kmsmayor = $kmsanterior +130;

		if ($_POST[nuevokmfinal]<$kmsanterior ||$_POST[nuevokmfinal]>$kmsmayor){
    ?>		<script type='text/javascript'>
						alert('NO SE GUARDO, El Odometro debe ser mayor a <?PHP echo $kmsanterior." y menor a ". $kmsmayor ?>');
				</script>
		<?PHP
		} 
    else{
				$qidvehi = $db->sql_query("delete from rec.desplazamientos WHERE id_movimiento='$_POST[id_mov_cerrar]' and hora_inicio is null");
				$ghoracerrar= $fecmov." ".$_POST["nuevahorafinal"];
				$consulnuevo = "select id, id_movimiento from rec.desplazamientos where hora_inicio is not null and hora_fin is null
		 		and id_movimiento='$_POST[id_mov_cerrar]'";
						
				$qidnuevo = $db->sql_query($consulnuevo);
				while($desnuevo = $db->sql_fetchrow($qidnuevo))
				{
					$desplanuevo = $desnuevo["id"];
				}
								
				$guardar1= "update rec.desplazamientos set hora_fin='$ghoracerrar' WHERE id_movimiento='$_POST[id_mov_cerrar]' and hora_fin is null ";
				$qid = $db->sql_query($guardar1);	 

				$guardar3="UPDATE rec.desplazamientos SET hora_fin = hora_fin+'1 days' WHERE id='$desplanuevo' and hora_fin < hora_inicio ";
				$qid = $db->sql_query($guardar3);	
													
				$userId = $_SESSION[$CFG->sesion]["user"]["id"];
				$guardar2= "update rec.movimientos set final='$ghoracerrar',km_final='$_POST[nuevokmfinal]',horometro_final='$_POST[nuevohorofinal]',  
								id_persona_cerro =".$userId."	WHERE id='$_POST[id_mov_cerrar]'";
															
				$qid = $db->sql_query($guardar2);
																
				//Solo si es la misma fecha
				$mfecha = date ( "Y-m-d" , $hora);
				if ($fecmov==$mfecha){
					$guardar2= "update vehiculos set kilometraje='$_POST[nuevokmfinal]',horometro='$_POST[nuevohorofinal]'
					where codigo like '".$vehiculo."%' AND id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')";									
					$qid = $db->sql_query($guardar2);
				}
				$guardar4="UPDATE rec.movimientos SET final = final+'1 days'  WHERE id='$_POST[id_mov_cerrar]' and final < inicio ";
				$qid = $db->sql_query($guardar4);
																		
				$guardar2= "update rec.movimientos_personas set hora_fin = '$ghoracerrar' WHERE id_movimiento='$_POST[id_mov_cerrar]' and hora_fin is null";
				$qid = $db->sql_query($guardar2);
																				
				$guardar5="UPDATE rec.movimientos_personas SET hora_fin = hora_fin+'1 days'  WHERE id='$_POST[id_mov_cerrar]' and hora_fin < hora_inicio";
				$qid = $db->sql_query($guardar5);

				/*log*/
				$accion = "Cerró movimiento\nFinal: ".$ghoracerrar;
				ingresarLogMovimiento("rec", $_POST["id_mov_cerrar"], $accion);	
				/*fin log*/
		}
}

//CERRAMOS TRIPULACION
if ($_POST["accion"]=="cerrartrip"){
	$ghoracerrar= $fecmov." ".$_POST["nuevahorafinal"];

	/*log*/
	$ant = $db->sql_row("SELECT p.nombre||' '||p.apellido as nombre, c.nombre as nombre_cargo, mp.* 
		FROM rec.movimientos_personas mp 
		LEFT JOIN personas p ON p.id=mp.id_persona 
		LEFT JOIN cargos c ON c.id = mp.cargo 
		WHERE mp.id=".$_POST["id_trip"]);
	/**/

	$guardar1= "update rec.movimientos_personas set hora_fin='$ghoracerrar' WHERE id='$_POST[id_trip]' and hora_fin is null ";
	$qid = $db->sql_query($guardar1);

	/*log*/
	$new = $db->sql_row("SELECT p.nombre||' '||p.apellido as nombre, c.nombre as nombre_cargo, mp.* 
		FROM rec.movimientos_personas mp 
		LEFT JOIN personas p ON p.id=mp.id_persona 
		LEFT JOIN cargos c ON c.id = mp.cargo 
		WHERE mp.id=".$_POST["id_trip"]);
	$accion = "Actualizó Operario al movimiento\nPersona: dato anterior: ".$ant["nombre"]." | dato nuevo: ".$new["nombre"]."\nCargo: dato anterior: ".$ant["nombre_cargo"]." | dato nuevo: ".$new["nombre_cargo"]."\nInicio: dato anterior: ".$ant["hora_inicio"]." | dato nuevo: ".$new["hora_inicio"]."\nFinal: dato anterior: ".$ant["hora_fin"]." | dato nuevo: ".$new["hora_fin"];
	ingresarLogMovimiento("rec", $ant["id_movimiento"], $accion);	
	/*fin log*/
}

$hora=time();
$mhora = date ( "H:i:s" , $hora);

?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <link rel="stylesheet" href="Promo.css" type="text/css" media="screen"/>
	<script type="text/javascript">
		var apn=navigator.appName;
		var apv=parseInt(navigator.appVersion);
	</script>
	<script type="text/javascript">
		function permite(elEvento, permitidos) {
		  // Variables que definen los caracteres permitidos
		  var numeros = "0123456789";
		  var horas = ":";
		  var peso = ".";
		  var fecha = "-";
		  var tiq = "GPS0123456789";
			var numeros_horas = numeros + horas;
		  var numeros_peso = numeros + peso;
		  var numeros_fecha = numeros + fecha + horas;
		  var teclas_especiales = [8, 9, 11, 13, 32, 37, 39, 46];
		  // 8 = BackSpace, 46 = Supr, 37 = flecha izquierda, 39 = flecha derecha, 9 = tab, 11 = tab, 13 = enter, 32 = Espacio	 
		  // Seleccionar los caracteres a partir del parámetro de la función
		  switch(permitidos) {
			case 'num':
			  permitidos = numeros;
			  break;
			case 'tiq':
			  permitidos = tiq;
			  break;
			case 'num_hor':
			  permitidos = numeros_horas;
			  break;
			case 'num_pes':
			  permitidos = numeros_peso;
			  break;
			case 'num_fec':
			  permitidos = numeros_fecha;
			  break;
		  }		
		// Obtener la tecla pulsada 
		  var evento = elEvento || window.event;
		  var codigoCaracter = evento.charCode || evento.keyCode;
		  var caracter = String.fromCharCode(codigoCaracter);
		  if (codigoCaracter == 13){
			(document.activeElement.onchange(true));
		  }
		  // Comprobar si la tecla pulsada es alguna de las teclas especiales
		  // (teclas de borrado y flechas horizontales)
		  var tecla_especial = false;
		  for(var i in teclas_especiales) {
			if(codigoCaracter == teclas_especiales[i]) {
			  tecla_especial = true;
			  break;
			}
		  }
		 
		  // Comprobar si la tecla pulsada se encuentra en los caracteres permitidos
		  // o si es una tecla especial
		  return permitidos.indexOf(caracter) != -1 || tecla_especial;
		}
		 	
		function mostrar(param) 
		{
		  document.captura_diario.accion.value=param
		  document.captura_diario.submit()
		}
		
		function movimiento(param) 
		{
		  document.captura_diario.accion.value='movimiento'
		  document.captura_diario.id_movimiento.value=param
		  document.captura_diario.submit()
		}
		
		function Recargartrip(idtrip) 
		{ 
		  document.captura_diario.accion.value='Guardartrip';
		  document.captura_diario.id_trip.value = idtrip;
		  document.captura_diario.submit()
		}
		function Recargardesplaza(idmov,idactual,idanterior,kmsactual,horactual) 
		{    			
		  document.captura_diario.accion.value='Guardar';
		  document.captura_diario.id_movimiento.value = idmov;
		  document.captura_diario.id_desplazamiento.value = idactual;
		  document.captura_diario.id_desplazant.value = idanterior;
		  document.captura_diario.submit()	   			   
		}
		
		function Recargardesplazakm(idmov,idactual,idanterior,kmsactual,horactual,valordigitado,objeto,vlranterior) 
		{   
		 	var valormax = vlranterior;
			valormax += parseInt(100);
			if (valordigitado<vlranterior ||valordigitado>valormax){
				alert("El Odometro debe ser mayor a "+vlranterior +"  y menor a "+valormax);
				setTimeout (function () {objeto.focus ()}, 50);
				objeto.value=kmsactual-1;
				objeto.select(); 
			}
			else{
				  document.captura_diario.accion.value='Guardar';
				  document.captura_diario.id_movimiento.value = idmov;
				  document.captura_diario.id_desplazamiento.value = idactual;
				  document.captura_diario.id_desplazant.value = idanterior;
				  document.captura_diario.submit()	   			   
			}
		}
		function Recargardesplazahr(idmov,idactual,idanterior,kmsactual,horactual,valordigitado,objeto,vlranterior) 
		{    
			var valormax = vlranterior;
  		valormax += parseInt(20);
			if (valordigitado<vlranterior ||valordigitado>valormax){
				alert("El Horometro debe ser mayor a "+vlranterior +"  y menor a "+valormax);
				setTimeout (function () {objeto.focus ()}, 50);
				objeto.value=horactual-1;
				objeto.select(); 
			}
			else{
				  document.captura_diario.accion.value='Guardar';
				  document.captura_diario.id_movimiento.value = idmov;
				  document.captura_diario.id_desplazamiento.value = idactual;
				  document.captura_diario.id_desplazant.value = idanterior;
				  document.captura_diario.submit()	   			   
			}
		}
		
		function Recargardescargue(iddes,peso,objeto) 
		{ 
			if (peso>50){
				alert("El Vehículo no puede transportar mas de 40 Tons");
				setTimeout (function () {objeto.focus ()}, 50);
				objeto.value=peso-1;
				objeto.select(); 
			}
			else{
			  document.captura_diario.accion.value='Guardardesc';
			  document.captura_diario.id_descargue.value = iddes;
			  document.captura_diario.submit();
			}
		}
		
		function Recargarapoyo(idapoyo) 
		{ 
		  document.captura_diario.accion.value='Guardarapoyo';
		  document.captura_diario.idapoyo.value = idapoyo;
		  document.captura_diario.submit()
		}
		
		function eliminatrip(idtrip) 
		{ 
		  document.captura_diario.accion.value='Eliminartrip';
		  document.captura_diario.id_trip.value = idtrip;
		  document.captura_diario.submit()
		}
		
		function eliminadespla(idactual, idanterior) 
		{ 
		  document.captura_diario.accion.value='Eliminardespl';
		  document.captura_diario.id_desplazamiento.value = idactual;
		  document.captura_diario.id_desplazant.value = idanterior;
		  document.captura_diario.submit()
		}
		
		function eliminadescargue(idpeso) 
		{ 
		  document.captura_diario.accion.value='Eliminardescargue';
		  document.captura_diario.idpesoborrar.value = idpeso;
		  document.captura_diario.submit()
		}
		
		function eliminaapoyo(idapoyo) 
		{ 
		  document.captura_diario.accion.value='Eliminarapoyo';
		  document.captura_diario.idapoyoborrar.value = idapoyo;
		  document.captura_diario.submit()
		}
		
		function cerrartrip(idtrip) 
		{ 
		  document.captura_diario.accion.value='cerrartrip';
		  document.captura_diario.id_trip.value = idtrip;
		  document.captura_diario.submit()
		}
		
		function cerrarviaje(idapoyo) 
		{ 
		  document.captura_diario.accion.value='cerrarviaje';
		  document.captura_diario.idmovimiento.value = idapoyo;
		  document.captura_diario.submit()
		}
		
		function mostrarVentana(param)
		{
			var ventana = document.getElementById(param); // Accedemos al contenedor
			ventana.style.marginTop = "200px"; // Definimos su posición vertical. La ponemos fija para simplificar el código
			ventana.style.marginLeft = ((document.body.clientWidth-550) / 2) +  "px"; // Definimos su posición horizontal
			ventana.style.display = 'block'; // Y lo hacemos visible
		}

		function ocultarVentana(param)
		{
		    var ventana = document.getElementById(param); // Accedemos al contenedor
		    ventana.style.display = 'none'; // Y lo hacemos invisible
			if (param=='descargues'){
				document.captura_diario.accion.value = 'descargues';
				document.captura_diario.id_movimiento.value = document.captura_diario.id_mov_cerrar.value;
			}
			if (param=='desplazamientos'){
				document.captura_diario.accion.value = 'desplazamientos';
			}
			if (param=='repartir'){
				document.captura_diario.accion.value = 'repartir';
			}
			if (param=='apoyo'){
				document.captura_diario.accion.value = 'apoyos';
			}
			if (param=='cerrarviaje'){
				document.captura_diario.accion.value = 'cerrarviaje';
			}
			if (param=='tripulacion'){
				document.captura_diario.accion.value = 'tripulacion';
			}
			document.captura_diario.submit();
		}
		
		function cerrarVentana(param)
		{
		    var ventana = document.getElementById(param); // Accedemos al contenedor
		    ventana.style.display = 'none'; // Y lo hacemos invisible
		}
	</script>
</head>
<body>

	<form method="POST" name="captura_diario">
	<input type="hidden" name=accion>
	<input type="hidden" name=id_movimiento>
	<input type="hidden" name=id_desplazamiento>
	<input type="hidden" name=id_descargue>
	<input type="hidden" name=id_desplazant>
	<input type="hidden" name=idpesoborrar>
	<input type="hidden" name=idapoyoborrar>
	<input type="hidden" name=idapoyo>
	<input type="hidden" name=id_trip>
	
	 <table width="850" cellspacing="0" cellpadding="0" align="center" border="0" style="font-size:12px">
	<tr>
	    <td style="width: 13px; height: 24px;" height="24" width="13"></td>
	    <td height="40" align="center"><span class="azul_16"><strong>MOVIMIENTOS DEL DÍA  
			<?=strtoupper(strftime("%A %d de %B de %Y",strtotime($fecha)))?></strong></span>
		</td>
	    <td style="width: 15px; height: 24px;" height="24" width="15"></td>
	</tr>
	</table>
	
	<table width="250" cellspacing="0" cellpadding="0" align="center" border="0" style="font-size:12px">
	<tr>
		<td style="width: 13px; height: 24px;" height="24" width="13"></td>
		<td align="center"><span class="azul_16">Turno&nbsp;</strong></span></td>
		<td align="center">
		<select onchange=mostrar("Turno") name="id_turno" class="CampoTexto">
				<option value="">Seleccionar..</option>
			   <?PHP
			    $qidDes = $db->sql_query("SELECT t.turno,(t.hora_inicio - INTERVAL '150 MINUTE') as hora_inicio 
					FROM turnos t 
					LEFT JOIN centros c ON c.id_empresa=t.id_empresa
					where c.id IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')
					group by t.turno,t.hora_inicio");
				while($des = $db->sql_fetchrow($qidDes))
				{
					if($id_turno==$des["turno"]){	
					$horaturno = $des["hora_inicio"]
					?>
						<option value="<?PHP echo $des["turno"]?>" selected><?PHP echo $des["turno"]?>
					<?PHP
					}else{ 
					?>
						<option value="<?PHP echo $des["turno"]?>"><?PHP echo $des["turno"]?>
					</option>
					<?php
					}
				}
			   ?>
			</select>
			<input type="hidden" name=horaturno value=<?PHP echo $horaturno?>>
		</td>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td align="Center"><strong><span class="azul_16">Vehículo&nbsp;</strong></span></td>
		<td align="center">
			<input style="font-family: Arial; font-size: 20pt; background-color: #BDBDBD" type="text" tabindex="1" name="vehiculo" maxlength=7 size=5 value="<?php echo $vehiculo?>" onchange=mostrar("Vehiculo")></td>
		<td style="width: 15px; height: 24px;" height="24" width="15"></td>
	</tr>
	</table>
	&nbsp
	<?PHP
if ($vehiculo<>"" and $id_turno<>"")
{
	if ($vehiculo<>"")
	{
		$consulta1 = "SELECT count(*) as registros
			FROM rec.movimientos mov
			LEFT JOIN vehiculos v ON v.id=mov.id_vehiculo
			LEFT JOIN micros m ON m.id=mov.id_micro
			LEFT JOIN servicios s ON s.id=m.id_servicio
			LEFT JOIN personas p ON p.id=m.id_coordinador
			LEFT JOIN tipos_residuos t ON t.id=m.id_tipo_residuo
			LEFT JOIN cuartelillos c ON c.id=m.id_cuartelillo
			WHERE mov.inicio::date = '".$fecha."' AND v.codigo like '".$vehiculo.$condicion."%' 
			AND mov.id_turno in (select id from turnos where turno like '%".$id_turno."%')
			AND m.id_ase IN (SELECT id FROM ases WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]'))";
		
		$qid1 = $db->sql_query($consulta1);
		while($row1 = $db->sql_fetchrow($qid1))
		{
			$registros = $row1["registros"];
		}
		if ($registros>1)
		{	
			?>
			&nbsp
			<table width="850" cellspacing="0" cellpadding="0" align="center" border="0" style="font-size:12px">
			<tr>
				<td colspan=9 align="center" style="font-family: Arial; font-size: 12pt; background-color: #BDBDBD" >Movimientos del Vehículo</td>
		    </tr>
		    <tr>
				<td class="WorkWithTitle" align="center">Fecha</td>
				<td class="WorkWithTitle" align="center">No. Orden</td>
				<td class="WorkWithTitle" align="center">Ruta</td>
				<td class="WorkWithTitle" align="center">Hora Salida Base</td>				
				<td class="WorkWithTitle" align="center">Hora Regreso Base</td>
				<td class="WorkWithTitle" align="center">KM Regreso Base</td>
				<td class="WorkWithTitle" align="center">HM Regreso Base</td>
				<td class="WorkWithTitle" align="center">Servicio</td>
				<td class="WorkWithTitle" align="center">Opciones</td>
		    </tr>
		   <?
			$consulta = "SELECT mov.*,mov.final as hfin, mov.inicio::date as fecmov, m.codigo, v.codigo as vehiculo, p.nombre||' '||p.apellido as coordinador,
			t.nombre as tipo_residuo, s.servicio
				FROM rec.movimientos mov
				LEFT JOIN vehiculos v ON v.id=mov.id_vehiculo
				LEFT JOIN micros m ON m.id=mov.id_micro
				LEFT JOIN servicios s ON s.id=m.id_servicio
				LEFT JOIN personas p ON p.id=m.id_coordinador
				LEFT JOIN tipos_residuos t ON t.id=m.id_tipo_residuo
				LEFT JOIN cuartelillos c ON c.id=m.id_cuartelillo
				WHERE mov.inicio::date = '".$fecha."' AND v.codigo like '".$vehiculo.$condicion."%' 
				AND mov.id_turno in (select id from turnos where turno like '%".$id_turno."%')
				AND m.id_ase IN (SELECT id FROM ases WHERE id_centro IN (SELECT id_centro 
				FROM personas_centros WHERE id_persona='$user[id]'))";
			$qid = $db->sql_query($consulta);
			$i=0;
			while($row = $db->sql_fetchrow($qid))
			{
				$i++;
				if($i%2==0)
					$class='FreeStyleGridEvenB';
				else
					$class='FreeStyleGridEven';	
			?>
			     <tr class="<?PHP echo $class?>" style="font-size:10px">
				     <td align="center"><?echo $row["fecmov"];?></td>
					 <td align="center"><?echo $row["numero_orden"];?></td>
					 <td align="center"><?echo $row["codigo"];?></td>
					 <td align="center"><?echo $row["inicio"];?></td>
					 <td align="center"><?echo $row["hfin"];?></td>
					 <td align="center"><?echo $row["km_final"];?></td>
					 <td align="center"><?echo $row["horometro_final"];?></td>
					 <td align="center"><?echo $row["servicio"];?></td>  
					 <td align="center">
					 <img src="images/update.png" width="20" height="20" style="cursor: pointer; border: 0px " title="Seleccionar" onclick=movimiento("<?echo $row["id"];?>") /></td> 
			     </tr>
			    <?
			}
		}
		else
		{
			//Consulto los datos del movimiento
			$consulta = "SELECT mov.*,mov.final as hfin, mov.inicio::date as fecmov, m.codigo, v.codigo as vehiculo, p.nombre||' '||p.apellido as coordinador,
			t.nombre as tipo_residuo, s.servicio
				FROM rec.movimientos mov
				LEFT JOIN vehiculos v ON v.id=mov.id_vehiculo
				LEFT JOIN micros m ON m.id=mov.id_micro
				LEFT JOIN servicios s ON s.id=m.id_servicio
				LEFT JOIN personas p ON p.id=m.id_coordinador
				LEFT JOIN tipos_residuos t ON t.id=m.id_tipo_residuo
				LEFT JOIN cuartelillos c ON c.id=m.id_cuartelillo
				WHERE mov.inicio::date = '".$fecha."' AND v.codigo like '".$vehiculo."%'
				AND mov.id_turno in (select id from turnos where turno like '%".$id_turno."%')
				AND m.id_ase IN (SELECT id FROM ases WHERE id_centro IN (SELECT id_centro 
				FROM personas_centros WHERE id_persona='$user[id]'))" .$condicion;
			$qid = $db->sql_query($consulta);
			?>
				&nbsp
				<table width="850" cellspacing="0" cellpadding="0" align="center" border="0" style="font-size:12px">
				<tr>
					<td colspan=9 align="center" style="font-family: Arial; font-size: 14pt; background-color: #BDBDBD" >Movimiento</td>
				</tr>
				<tr>
					<td class="WorkWithTitle" align="center">Fecha</td>
					<td class="WorkWithTitle" align="center">No. Orden</td>
					<td class="WorkWithTitle" align="center">Ruta</td>
					<td class="WorkWithTitle" align="center">Hora Salida Base</td>				
					<td class="WorkWithTitle" align="center">Hora Regreso Base</td>
					<td class="WorkWithTitle" align="center">KM Regreso Base</td>
					<td class="WorkWithTitle" align="center">HM Regreso Base</td>
					<td class="WorkWithTitle" align="center">Servicio</td>
					<td class="WorkWithTitle" align="center">Cerrar Movimiento</td>
				</tr>
			
			<?PHP
			$i=1;
			while($row = $db->sql_fetchrow($qid))
			{
			  $idMovimiento= $row["id"];
				$horainicio_mov = $row["inicio"];
				$horafin_mov = $row["hfin"];
				$i++;
				if($i%2==0)
					$class='FreeStyleGridEvenB';
				else
					$class='FreeStyleGridEven';
		?>
				<input type="hidden" name=fecmov value=<?PHP echo $row["fecmov"]?>>
				<input type="hidden" name=horainicio_movimiento value=<?PHP echo $row["hfin"]?>>
				<tr class="<?PHP echo $class?>" style="font-size:10px">
					 <td align="center"><?echo $row["fecmov"];?></td>
					 <td align="center"><?echo $row["numero_orden"];?></td>
					 <td align="center"><?echo $row["codigo"];?></td>
					 <td align="center"><?echo $row["inicio"];?></td>
					 <td align="center"><?echo $row["hfin"];?></td>
					 <td align="center"><?echo $row["km_final"];?></td>
					 <td align="center"><?echo $row["horometro_final"];?></td>
					 <td align="center"><?echo $row["servicio"];?></td>
					 <td align="center">
						<img src="images/icon-activate.gif" width="20" height="20" style="cursor: pointer; border: 0px " title="Cerrar Movimiento" onclick="javascript:mostrarVentana('cerrarviaje')"/>
					 </td>
				</tr>	 
				<?php
			}
			echo "</table>";
			//Consulto los datos de la tripulación
			$consultatrip = "SELECT m.id, p.nombre||' '||p.apellido as persona, c.nombre as cargo, to_char(hora_inicio,'YYYY-MM-DD HH24:MI:SS') as hora_inicio, 
			to_char(hora_fin,'YYYY-MM-DD HH24:MI:SS') as hora_fin
				FROM rec.movimientos_personas m
		 		LEFT JOIN personas p ON p.id=m.id_persona
				LEFT JOIN cargos c ON c.id=p.id_cargo
			 	WHERE m.id_movimiento=".$idMovimiento."
				ORDER BY hora_inicio";
			?>
				&nbsp
				<table width="850" cellspacing="0" cellpadding="0" align="center" border="0" style="font-size:12px">
				<tr>
					<td colspan=5 align	="center" style="font-family: Arial; font-size: 14pt; background-color: #BDBDBD" >Tripulación</td>
				</tr>
				<tr>
					<td width="120" class="WorkWithTitle" align="center">Cargo</td>	 
					<td width="250"  class="WorkWithTitle" align="center">Nombres</td>
					<td width="200" class="WorkWithTitle" align="center">Hora Inicio</td>
					<td width="200" class="WorkWithTitle" align="center">Hora Fin</td>
					<td width="120"  class="WorkWithTitle" align="center">Opciones</td>
				</tr>
			
			<?PHP
			$i=0;
			$qidtrip = $db->sql_query($consultatrip);
			while($rowtrip = $db->sql_fetchrow($qidtrip))
			{
				$i++;
				if($i%2==0)
					$class='FreeStyleGridEvenB';
				else
					$class='FreeStyleGridEven';
			?>
				<tr class="<?PHP echo $class?>" style="font-size:10px">
					<td align="left"><?echo $rowtrip["cargo"];?></td>
					<td align="left"><?echo $rowtrip["persona"];?></td>
					<td align="center"><input type=text name=<?PHP echo $rowtrip["id"]?>.horainicio size=19 maxlength=19 value='<?echo $rowtrip["hora_inicio"];?>' 	onchange=Recargartrip(<?echo $rowtrip["id"];?>)></td>
					<td align="center"><input type=text name=<?PHP echo $rowtrip["id"]?>.hora_fin size=19 maxlength=19 value='<?echo $rowtrip["hora_fin"];?>' 	onchange=Recargartrip(<?echo $rowtrip["id"];?>)></td>
					<td align="center">
						<img src="images/button_del.png" width="15" height="15" style="cursor: pointer; border: 0px " title="Eliminar" onclick=eliminatrip(<?echo $rowtrip["id"];?>)> &nbsp;
						<img src="images/icon-activate.gif" width="20" height="20" style="cursor: pointer; border: 0px " title="Cerrar Tripulación" onclick=cerrartrip(<?echo $rowtrip["id"];?>)></td>
				</tr>	 
				<?php
			}
			echo "</table>";
			?>
		
			<div id="tripulacion" style="position: fixed; width: 450px; height: 250px; top: 0; left: 0; font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal; border: #333333 3px solid; background-color: #FAFAFA; color: #000000; display:none;">
				<div style="font-weight: bold; text-align: left; color: #FFFFFF; padding: 5px; background-color:#006394">Agregar Tripulación</div>
				<center>		
				<table class="Table1" width="410" cellspacing="0" cellpadding="0" align="center" border="0" style="">
				<tr>
					<td colspan="4">&nbsp; </td>
						<input type="hidden" name=id_movimientodes value='<?echo $idMovimiento?>'>
				</tr>
				<tr>
					<td width="7" height="17"></td>
					<td width="101" class="CampoTitulo">(*)Empleado:</td>
					<td align="left">
						<select name="nuevapersona" class="CampoTexto">
							<option value="">Seleccionar..</option>
						   <?PHP
						   $qidtripula = $db->sql_query("Select p.id,p.cedula,p.nombre,p.apellido,p.id_cargo,car.nombre as cargo 
									from personas p
									left join personas_centros c on p.id=c.id_persona 
									left join cargos car on p.id_cargo=car.id
									where cedula is not null and id_cargo in (21,22) and p.id_estado<>3
									and c.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')
									group by 1,2,3,4,5,6
									order by 4,5,6");
							while($tripula = $db->sql_fetchrow($qidtripula))
							{
								?>
								<option value="<?PHP echo $tripula["id"]?>"><?PHP echo $tripula["apellido"]." ".$tripula["nombre"]."-".$tripula["cargo"]."-".$tripula["cedula"]?></option>
								<?php
							}
						   ?>
						</select>
						
					</td>
					<td width="8" style="height: 18px;" height="13"></td>
				<tr>
					<td height="13"></td>
					<td width="101" class="CampoTitulo">Hora Inicio:</td>
					<td align="left"><input type=text name=triphorainicio size=6 maxlength=8 value='<?echo $mhora;?>'></td>
					<td width="8" style="height: 18px;" height="13"></td>
				</tr>
				</table>
				</center>
				<div style="padding: 10px; background-color: #F0F0F0; text-align: center; margin-top: 44px;">
					<input id="btnAceptar1" onclick="ocultarVentana('tripulacion');" name="btnAceptar1" size="20" type="button" value="Aceptar"/> 
					<input id="btnCancelar1" onclick="cerrarVentana('tripulacion');" name="btncancelar1" size="20" type="button" value="Cancelar"/> 
				</div>
			</div>
			<center>
			<a style="font-family: Arial; font-size: 14pt" href="javascript:mostrarVentana('tripulacion');">Agregar Tripulación</a>
			</center>
			<p>
			</p>
			<?php
			$consultadespap = "SELECT d.id, t.tipo,hora_inicio as inicio, to_char(hora_inicio,'YYYY-MM-DD') as fecmov, 
						to_char(hora_inicio,'HH24:MI:SS') as hora_inicio,to_char(hora_inicio,'YYYY-MM-DD') as fecha_desplaza,
						hora_fin,numero_viaje, km, horometro 
						FROM rec.desplazamientos d LEFT JOIN rec.tipos_desplazamientos t ON d.id_tipo_desplazamiento=t.id 
						WHERE id_movimiento='".$idMovimiento."' 
						ORDER BY inicio,id limit 1";
			$qiddespap=$db->sql_query($consultadespap);
			while($rowap = $db->sql_fetchrow($qiddespap))
			{
				$horainicio_mov = $rowap["inicio"];
			}
			
			//Consulto los datos de los desplazamientos
			$consultadesp = "SELECT d.id, t.tipo,hora_inicio as inicio, to_char(hora_inicio,'YYYY-MM-DD') as fecmov, 
			to_char(hora_inicio,'HH24:MI:SS') as hora_inicio,to_char(hora_inicio,'YYYY-MM-DD') as fecha_desplaza,
			hora_fin,numero_viaje, km, horometro 
			FROM rec.desplazamientos d LEFT JOIN rec.tipos_desplazamientos t ON d.id_tipo_desplazamiento=t.id 
			WHERE id_movimiento='".$idMovimiento."' 
			ORDER BY inicio,id";
			?>
				&nbsp
				<table width="850" cellspacing="0" cellpadding="0" align="center" border="0" style="font-size:12px">
				<tr>
					<td colspan=8 align="center" style="font-family: Arial; font-size: 14pt; background-color: #BDBDBD">Desplazamientos Movimiento No. <?PHP echo $idMovimiento;?></td>
				</tr>
				<tr>
					<td class="WorkWithTitle" align="center">Desplazamiento</td>
					<td class="WorkWithTitle" align="center">Viaje</td>
					<td class="WorkWithTitle" align="center">Fecha</td>	 
					<td class="WorkWithTitle" align="center">Inicio</td>	 
					<td class="WorkWithTitle" align="center">Fin</td>
					<td class="WorkWithTitle" align="center">Kilometraje</td>
					<td class="WorkWithTitle" align="center">Horómetro</td>
					<td class="WorkWithTitle" align="center">Opciones</td>
				</tr>
			
			<?PHP	
			$qiddesp=$db->sql_query($consultadesp);
			$i=0;
			$despant=0;
			$habilita="";
      /// Consulto el ultimo kilometraje digitado.
			$consultakms = "SELECT (d.hora_inicio),(cast(km as float)) as km, (cast(d.horometro as float)) as horo,min(m.km_final) as ks_final
					FROM rec.desplazamientos d
					left join rec.movimientos m on m.id=d.id_movimiento
					left join vehiculos v on v.id=m.id_vehiculo
					where v.codigo like '".$vehiculo."%' and m.inicio <= '".$_GET["fecha"]." ".$horaturno."'
					and v.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."')
				 	group by d.hora_inicio,d.km,d.horometro order by 1 desc  limit 2";
			$qidkms=@$db->sql_query($consultakms);
			while($rowkms = $db->sql_fetchrow($qidkms))
			{
				$kmsanterior = $rowkms["km"];
				$horanterior = $rowkms["horo"];
				$ks_final = $rowkms["ks_final"];
				if ($ks_final>$kmsanterior) $kmsanterior=$ks_final;
			}		
			
			//Recorrdo la consulta de los recorrido.
			while($rowtdesp = $db->sql_fetchrow($qiddesp))
			{
				if($rowtdesp["hora_fin"]<>"") {
					/// Consulto el ultimo kilometraje anterio digitado.
						$consultakms = "SELECT (d.hora_inicio),(cast(km as float)) as km, (cast(d.horometro as float)) as horo 
						FROM rec.desplazamientos d
							left join rec.movimientos m on m.id=d.id_movimiento
							left join vehiculos v on v.id=m.id_vehiculo
							where v.codigo like '".$vehiculo."%' and d.hora_inicio < '".$rowtdesp['hora_fin']."'
							and v.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."')
							group by d.hora_inicio,d.km,d.horometro order by 1 desc  limit 2";
						$qidkms=@$db->sql_query($consultakms);
						while($rowkms = $db->sql_fetchrow($qidkms))
						{
							$kmsanterior = $rowkms["km"];
							$horanterior = $rowkms["horo"];
						}		
   			}
				if($rowtdesp["km"]=="") {
						// traigo los ultimos km digitados o los ultimos del vehículo
						$mfecha = date ( "Y-m-d" , $hora);
						$mfecactual = date ( "Y-m-d H:i:s" , $hora);
						if ($fecha==$mfecha){
								$consultakms = "SELECT (d.hora_inicio),(cast(km as float)) as km, (cast(d.horometro as float)) as horo,min(m.km_final) as ks_final
									FROM rec.desplazamientos d
									left join rec.movimientos m on m.id=d.id_movimiento
									left join vehiculos v on v.id=m.id_vehiculo
									where v.codigo like '".$vehiculo."%' and m.inicio <= '".$mfecactual."'
									and v.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."')
									group by d.hora_inicio,d.km,d.horometro order by 1 desc  limit 2";
						}
						else
						{
							$consultakms = "SELECT cast(max(km) as integer) as km, cast(max(horometro) as integer) as horo FROM rec.desplazamientos
							where id_movimiento=".$idMovimiento;
						}
						$qidkms=$db->sql_query($consultakms);
						while($rowkms = $db->sql_fetchrow($qidkms))
						{
								$kmsactual = $rowkms["km"];
								$horactual = $rowkms["horo"];
								$ks_final = $rowkms["ks_final"];
								if ($ks_final>$kmsactual) $kmsactual=$ks_final;
						}
						if ($kmsactual ==""){
								$kmsactual=$kmsanterior;
								$horactual=$horanterior;
						}
				} 
				else 
				{
					$kmsactual=$kmsanterior;
					$horactual=$horanterior;
				}
			
				$i++;
				if($i%2==0)
					$class='FreeStyleGridEvenB';
				else
					$class='FreeStyleGridEven';
				//coloco la hora del sistema en el campo para que lo puedan cambiar
				
			?>
				<tr class="<?PHP echo $class?>" style="font-size:10px">			
					<td align="center">
					<?echo $rowtdesp["tipo"];?></td> 
					<td align="center"><input type=text name=<?PHP echo $rowtdesp["id"]?>.viaje size=1 <?php echo $habilita?> 				maxlength=8 	value='<?echo $rowtdesp["numero_viaje"];?>' 																onkeypress="return permite(event, 'num')" 		onChange=Recargardesplaza(<?echo $idMovimiento.','.$rowtdesp["id"].','.$despant.','.$kmsactual.','.$horactual?>)></td>
					<td align="center"><input type=text name=<?PHP echo $rowtdesp["id"]?>.fecha_desplaza size=10 <?php echo $habilita?> 	maxlength=10 	value='<?if($rowtdesp["fecha_desplaza"]=="") {echo $fecha;} else {echo $rowtdesp["fecha_desplaza"];}?>' 	onkeypress="return permite(event, 'num_fec')"	onChange=Recargardesplaza(<?echo $idMovimiento.','.$rowtdesp["id"].','.$despant.','.$kmsactual.','.$horactual?>)></td>
					<td align="center"><input type=text name=<?PHP echo $rowtdesp["id"]?>.horainicio size=6 <?php echo $habilita?> 			maxlength=8 	value='<?if($rowtdesp["hora_inicio"]=="") {echo $mhora;} else {echo $rowtdesp["hora_inicio"];}?>' 			onkeypress="return permite(event, 'num_hor')" 	onChange=Recargardesplaza(<?echo $idMovimiento.','.$rowtdesp["id"].','.$despant.','.$kmsactual.','.$horactual?>)></td>
					<td align="center"><input type=text name=<?PHP echo $rowtdesp["id"]?>.horafin size=16 disabled 							maxlength=16 	value='<?echo $rowtdesp["hora_fin"];?>' 																	onkeypress="return permite(event, 'num_hor')" 	onChange=Recargardesplaza(<?echo $idMovimiento.','.$rowtdesp["id"].','.$despant.','.$kmsactual.','.$horactual?>)></td>
					<td align="center"><input type=text name=<?PHP echo $rowtdesp["id"]?>.km size=6 <?php echo $habilita?> 					maxlength=6 	value='<?if($rowtdesp["km"]=="") {echo $kmsactual;} else {echo $rowtdesp["km"];}?>' 					    onkeypress="return permite(event, 'num')" 		onChange=Recargardesplazakm(<?echo $idMovimiento.','.$rowtdesp["id"].','.$despant.','.$kmsactual.','.$horactual?>,this.value,this,<?echo $kmsanterior?>)></td>
					<td align="center"><input type=text name=<?PHP echo $rowtdesp["id"]?>.horometro size=6 <?php echo $habilita?> 			maxlength=6 	value='<?if($rowtdesp["horometro"]=="") {echo $horactual;} else {echo $rowtdesp["horometro"];}?>' 		    onkeypress="return permite(event, 'num')" 		onChange=Recargardesplazahr(<?echo $idMovimiento.','.$rowtdesp["id"].','.$despant.','.$kmsactual.','.$horactual?>,this.value,this,<?echo$horanterior?>)></td>					
					<td align="center">
						<img src="images/button_del.png" width="15" height="15" style="cursor: pointer; border: 0px " title="Eliminar" onclick=eliminadespla(<?echo $rowtdesp["id"].','.$despant;?>) />
					</td>
				</tr>	 
				<?php
				$despant = $rowtdesp["id"];
				$kmsanterior = $rowtdesp["km"];
				$horanterior = $rowtdesp["horometro"];
				if($rowtdesp["hora_inicio"]=="") $habilita="disabled";
			}
			echo "</table>";
			
			?>
			<div id="desplazamientos" style="position: fixed; width: 450px; height: 250px; top: 0; left: 0; font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal; border: #333333 3px solid; background-color: #FAFAFA; color: #000000; display:none;">
				<div style="font-weight: bold; text-align: left; color: #FFFFFF; padding: 5px; background-color:#006394">Agregar Desplazamiento</div>
				<center>		
				<table class="Table1" width="410" cellspacing="0" cellpadding="0" align="center" border="0" style="">
				<tr>
					<td colspan="4">&nbsp; </td>
						<input type="hidden" name=id_movimientodes value='<?echo $idMovimiento?>'>
				</tr>
				<tr>
					<td width="7" height="17"></td>
					<td width="101" class="CampoTitulo">(*)Desplazamiento:</td>
					<td align="left">
						<select name="nuevodesplaza" class="CampoTexto">
							<option value="">Seleccionar..</option>
						   <?PHP
						   $qidDesplaza = $db->sql_query("SELECT id, tipo FROM rec.tipos_desplazamientos ORDER BY tipo");
							while($desplaza = $db->sql_fetchrow($qidDesplaza))
							{
								?>
								<option value="<?PHP echo $desplaza["id"]?>"><?PHP echo $desplaza["tipo"]?></option>
								<?php
							}
						   ?>
						</select>
						
					</td>
					<td width="8" style="height: 18px;" height="13"></td>
				<tr>
					<td height="13"></td>
					<td width="101" class="CampoTitulo">Hora Inicio:</td>
					<td align="left"><input type=text name=nuevahorainicio size=6 maxlength=8 value='<?echo $mhora;?>' onkeypress="return permite(event, 'num_hor')"></td>
					<td width="8" style="height: 18px;" height="13"></td>
				</tr>
				<tr>
					<td height="13"></td>
					<td width="101" class="CampoTitulo">Kilometraje:</td>
					<?
					if(!isset($kmsactual)){
						// traigo los ultimos km digitados o los ultimos del vehículo
						$mfecha = date ( "Y-m-d" , $hora);
						if ($fecmov==$mfecha){
							$consultakms = "SELECT cast(kilometraje as integer) as km, cast(horometro as integer) as horo FROM vehiculos
								where codigo like '".$vehiculo."%' and id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')";
						}
						else
						{
							$consultakms = "SELECT cast(max(km) as integer) as km, cast(max(horometro) as integer) as horo FROM rec.desplazamientos
							where id_movimiento=".$idMovimiento;
						}	
						$qidkms=$db->sql_query($consultakms);
						while($rowkms = $db->sql_fetchrow($qidkms))
						{
							$kmsactual = $rowkms["km"];
							$horactual = $rowkms["horo"];
						}
					}
					?>
					<td align="left"><input type=text name=nuevokm size=6 maxlength=8 value='<?echo $kmsactual;?>' onkeypress="return permite(event, 'num')"	></td>
					<td width="8" style="height: 18px;" height="13"></td>
				</tr>
				<tr>
					<td height="13"></td>
					<td width="101" class="CampoTitulo">Horometro:</td>
					<td align="left"><input type=text name=nuevahorometro size=6 maxlength=8 value='<?echo $horactual;?>' onkeypress="return permite(event, 'num_hor')"></td>
					<td width="8" style="height: 18px;" height="13"></td>
				</tr>
				</table>
				</center>
				<div style="padding: 10px; background-color: #F0F0F0; text-align: center; margin-top: 44px;">
					<input id="btnAceptar1" onclick="ocultarVentana('desplazamientos');" name="btnAceptar1" size="20" type="button" value="Aceptar"/> 
					<input id="btnCancelar1" onclick="cerrarVentana('desplazamientos');" name="btncancelar1" size="20" type="button" value="Cancelar"/> 
				</div>
			</div>
			<center>
			<a style="font-family: Arial; font-size: 14pt" href="javascript:mostrarVentana('desplazamientos');">Agregar Desplazamiento</a>
			</center>
			<p>
			</p>
			<?PHP
		
			//Consulto los descargues del vehículo
			$consultadesc = "SELECT p.*,l.nombre,pm.porcentaje
			FROM rec.pesos p
			LEFT JOIN rec.movimientos_pesos pm ON pm.id_peso = p.id
			LEFT JOIN vehiculos v2 ON v2.id = p.id_vehiculo
			LEFT JOIN lugares_descargue l ON l.id=p.id_lugar_descargue
			LEFT JOIN centros c ON c.id=l.id_centro
			WHERE pm.id_movimiento='".$idMovimiento."' 
			GROUP BY p.id,p.peso_inicial,p.peso_final,p.peso_total,p.id_lugar_descargue,tiquete_entrada,tiquete_salida,
			fecha_entrada,fecha_salida,p.id_vehiculo,p.reparte,p.asignado,
			l.nombre,pm.porcentaje, p.cerrado, p.id_persona_cerro, p.fecha_cerro
			ORDER BY id";
			?>
				
				&nbsp
				<table width="850" cellspacing="0" cellpadding="0" align="center" border="0" style="font-size:12px">
				<tr>
					<td colspan=11 align="center" style="font-family: Arial; font-size: 14pt; background-color: #BDBDBD">Descargues del Vehículo</td>
				</tr>
				<tr>
					<td class="WorkWithTitle" width="100" align="center">Lugar</td>
					<td class="WorkWithTitle" align="center">Peso Inicial</td>
					<td class="WorkWithTitle" align="center">Peso Final</td>	 
					<td class="WorkWithTitle" align="center">Peso total</td>
					<td class="WorkWithTitle" align="center">Tiquete Entrada</td>
					<td class="WorkWithTitle" align="center">Tiquete Salida</td>
					<td class="WorkWithTitle" align="center">Fecha Entrada</td>
					<td class="WorkWithTitle" align="center">Fecha Salida</td>
					<td class="WorkWithTitle" align="center">Reparte</td>
					<td class="WorkWithTitle" align="center">Peso Ruta</td>
					<td class="WorkWithTitle" align="center">Opciones</td>
				</tr>
			
			<?PHP	
			// traigo los descargues que tenga el vehículo
			$mfecha = date ( "Y-m-d" , $hora);
			$qiddesc=$db->sql_query($consultadesc);
			$i=0;
			$habilita="";
			
			while($rowtdesc = $db->sql_fetchrow($qiddesc))
			{
				$i++;
				if($i%2==0)
					$class='FreeStyleGridEvenB';
				else
					$class='FreeStyleGridEven';
				//coloco la hora del sistema en el campo para que lo puedan cambiar
				
			?>
				<tr class="<?PHP echo $class?>" style="font-size:10px">			
					<td align="center"><input type=text name=<?PHP echo $rowtdesc["id"]?>.lugard size=30 disabled value='<?echo $rowtdesc["nombre"];?>'></td>
					<td align="center"><input type=text name=<?PHP echo $rowtdesc["id"]?>.pesini size=4 maxlength=6 value='<?echo $rowtdesc["peso_inicial"];?>' onkeypress="return permite(event, 'num_pes')" onchange=Recargardescargue(<?echo $rowtdesc["id"]?>,this.value,this)></td>
					<td align="center"><input type=text name=<?PHP echo $rowtdesc["id"]?>.pesfin size=4 maxlength=6 value='<?echo $rowtdesc["peso_final"];?>' 	onkeypress="return permite(event, 'num_pes')" onchange=Recargardescargue(<?echo $rowtdesc["id"]?>,this.value,this)></td>
					<td align="center"><input type=text name=<?PHP echo $rowtdesc["id"]?>.pestot size=4 maxlength=6 value='<?echo $rowtdesc["peso_total"];?>' 	onkeypress="return permite(event, 'num_pes')" onchange=Recargardescargue(<?echo $rowtdesc["id"]?>,this.value,this)></td>
					<td align="center"><input type=text name=<?PHP echo $rowtdesc["id"]?>.tiqent size=4 maxlength=8 value='<?echo $rowtdesc["tiquete_entrada"];?>' onkeypress="return permite(event, 'tiq')" onchange=Recargardescargue(<?echo $rowtdesc["id"]?>)></td>
					<td align="center"><input type=text name=<?PHP echo $rowtdesc["id"]?>.tiqsal size=4 maxlength=8 value='<?echo $rowtdesc["tiquete_salida"];?>' onkeypress="return permite(event, 'num')" onchange=Recargardescargue(<?echo $rowtdesc["id"]?>)></td>
					<td align="center"><input type=text name=<?PHP echo $rowtdesc["id"]?>.fecent size=16 maxlength=19 value='<?echo $rowtdesc["fecha_entrada"];?>' onkeypress="return permite(event, 'num_fec')" onchange=Recargardescargue(<?echo $rowtdesc["id"]?>)></td>
					<td align="center"><input type=text name=<?PHP echo $rowtdesc["id"]?>.fecsal size=16 maxlength=19 value='<?echo $rowtdesc["fecha_salida"];?>' onkeypress="return permite(event, 'num_fec')" onchange=Recargardescargue(<?echo $rowtdesc["id"]?>)></td>
					<td align="center">
						<select name=reparte class="CampoTexto" disabled>
							<option value='false' <?if ($rowtdesc["reparte"]=='f'){echo 'Selected';}?>>No</option>
							<option value='True'  <?if ($rowtdesc["reparte"]=='t'){echo 'Selected';}?>>Si</option>
						</select>						
					<td align="center"><?echo $rowtdesc["peso_total"]*$rowtdesc["porcentaje"]/100;?></td>
					<td align="center">
						<img src="images/button_del.png" width="15" height="15" style="cursor: pointer; border: 0px " title="Eliminar" onclick=eliminadescargue(<?echo $rowtdesc["id"];?>) />
					</td>
				</tr>	 
				<?php
			}
			echo "</table>";
			
			?>
			<div id="descargues" style="position: fixed; width: 450px; height: 250px; top: 0; left: 0; font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal; border: #333333 3px solid; background-color: #FAFAFA; color: #000000; display:none;">
				<div style="font-weight: bold; text-align: left; color: #FFFFFF; padding: 5px; background-color:#006394">Agregar Descargues</div>
				<center>		
				<table class="Table1" width="410" cellspacing="0" cellpadding="0" align="center" border="0" style="">
				<tr>
					<td colspan="4">&nbsp; </td>
						<input type="hidden" name=id_movimientodesc value='<?echo $idMovimiento?>'>
				</tr>
				<tr>
					<td width="7" height="17"></td>
					<td width="101" class="CampoTitulo">(*)Lugar Descargue:</td>
					<td align="left">
						<select name=id_sitiodesc class="CampoTexto">
							<option value="">Seleccionar..</option>
						   <?PHP
						   $qidsitio = $db->sql_query("select * from lugares_descargue where id_centro IN 
						   (SELECT id_centro FROM vehiculos WHERE codigo like '".$vehiculo."%'
								and id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]'))");
							while($dessitio = $db->sql_fetchrow($qidsitio))
							{
								?>
								<option value="<?PHP echo $dessitio["id"]?>"><?PHP echo $dessitio["nombre"]?></option>
								<?php
							}
						   ?>
						</select>
					</td>
					<td width="8" style="height: 18px;" height="13"></td>
				</tr>
				<tr>
					<td height="13"></td>
					<td width="101" class="CampoTitulo">Tiquete Entrada:</td>
					<td align="left"><input type=text name=tiqentrada size=6 maxlength=8 onkeypress="return permite(event, 'tiq')"></td>
					<td width="8" style="height: 18px;" height="13"></td>
				</tr>
				<tr>
					<td height="13"></td>
					<td width="101" class="CampoTitulo">Hora Inicio:</td>
					<td align="left"><input type=text name=deshorainicio size=6 maxlength=8 value='<?echo $mhora;?>' onkeypress="return permite(event, 'num_hor')"></td>
					<td width="8" style="height: 18px;" height="13"></td>
				</tr>
				<tr>
					<td height="13"></td>
					<td width="101" class="CampoTitulo">Peso Inicial:</td>
					<td align="left"><input type=text name=pesoinicial size=6 maxlength=6 onkeypress="return permite(event, 'num_pes')"></td>
					<td width="8" style="height: 18px;" height="13"></td>
				</tr>
				</table>
				</center>
				<div style="padding: 10px; background-color: #F0F0F0; text-align: center; margin-top: 44px;">
					<input id="btnAceptar2" onclick="ocultarVentana('descargues');" name="btnAceptar2" size="20" type="button" value="Aceptar"/> 
					<input id="btnCancelar2" onclick="cerrarVentana('descargues');" name="btncancelar2" size="20" type="button" value="Cancelar"/> 
				</div>
			</div>
			<center>
			<?
			$cerrado = $db->sql_row("SELECT peso_cerrado FROM rec.movimientos WHERE id=".$idMovimiento);
			if($cerrado["peso_cerrado"] == "f"){?>
			<a style="font-family: Arial; font-size: 14pt" href="javascript:mostrarVentana('descargues');">Agregar Descargue</a>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a style="font-family: Arial; font-size: 14pt" href="javascript:mostrarVentana('repartir');">Repartir Descargue</a>
			<?}?>
			</center>
		
			<div id="repartir" style="position: fixed; width: 450px; height: 250px; top: 0; left: 0; font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal; border: #333333 3px solid; background-color: #FAFAFA; color: #000000; display:none;">
				<div style="font-weight: bold; text-align: left; color: #FFFFFF; padding: 5px; background-color:#006394">Repartir Descargue</div>
				<center>		
				<table class="Table1" width="410" cellspacing="0" cellpadding="0" align="center" border="0" style="">
				<tr>
					<td colspan="4">&nbsp; </td>
						<input type="hidden" name=id_movimientorep value='<?echo $idMovimiento?>'>
				</tr>			
				<tr>
					<td width="7" height="17"></td>
					<td width="101" class="CampoTitulo">(*)Peso a Repartir</td>
					<td align="left">
						<select name=pesoseleccionadorepar class="CampoTexto">
						   <?PHP
						   $qidsitio = $db->sql_query("select rec.pesos.id as idpeso, rec.movimientos_pesos.id as idmovpeso, peso_total
								from rec.movimientos_pesos 
								left join rec.pesos on rec.movimientos_pesos.id_peso=rec.pesos.id
								where id_movimiento=$idMovimiento");					   
							while($dessitio = $db->sql_fetchrow($qidsitio))
							{
								?>
								<option value="<?PHP echo $dessitio["idpeso"]?>"><?PHP echo $dessitio["peso_total"]?></option>
								<?php
							}
						   ?>
						</select>
					</td>
					<td width="8" style="height: 18px;" height="13"></td>
				</tr>
				
				<tr>
					<td width="7" height="17"></td>
					<td width="101" class="CampoTitulo">(*)Movimiento</td>
					<td align="left">
					<?
					$fecmov1= date("Y-m-d", strtotime("$fecha - 15 days"));
					$fecmov2= date("Y-m-d", strtotime("$fecha + 1 days"));
					?>
					<select name=movimientoselecrepartir class="CampoTexto">
							<option value="">Seleccionar..</option>
						   <?PHP
							   $qidsitio = $db->sql_query("SELECT mov.id,i.codigo||' / '||v.codigo||' / '||mov.inicio as movimiento
									FROM rec.movimientos mov
									LEFT JOIN vehiculos v ON v.id = mov.id_vehiculo
									LEFT JOIN micros i ON i.id=mov.id_micro
									where id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]') 
									and mov.inicio<='$fecmov2' and mov.inicio>'$fecmov1' 
									order by mov.inicio DESC,i.codigo,v.codigo
								");
							
					
							while($dessitio = $db->sql_fetchrow($qidsitio))
							{
								?>
								<option value="<?PHP echo $dessitio["id"]?>"><?PHP echo $dessitio["movimiento"]?></option>
								<?php
							}
						   ?>
						</select>
					</td>
					<td width="8" style="height: 18px;" height="13"></td>
				</tr>
				<tr>
					<td height="13"></td>
					<td width="101" class="CampoTitulo">Peso:</td>
					<td align="left"><input type=text name='pesorepartir' size=6 maxlength=6 onkeypress="return permite(event, 'num_pes')"></td>
					<td width="8" style="height: 18px;" height="13"></td>
				</tr>
				</table>
				</center>
				<div style="padding: 10px; background-color: #F0F0F0; text-align: center; margin-top: 44px;">
					<input id="btnAceptar2" onclick="ocultarVentana('repartir');" name="btnAceptar2" size="20" type="button" value="Aceptar"/> 
					<input id="btnCancelar2" onclick="cerrarVentana('repartir');" name="btncancelar2" size="20" type="button" value="Cancelar"/> 
				</div>
			</div>
			<p>
			</p>
			
			<?PHP
			//Consulto los Apoyos del vehículo
			if ($horafin_mov=='') $horafin_mov=''; else $horafin_mov = "AND (a.final<='".$horafin_mov."' or a.final is null)";
		  $consultaapoyo = "select apoyo.*, m.codigo as ruta from 
					(SELECT a.*, a.inicio as hora_inicio, a.final as hora_fin, v.codigo,
  				p.id_movimiento, mov.id_micro, mov.id_turno
	 				FROM rec.apoyos a 
					LEFT JOIN vehiculos v ON v.id=a.id_vehiculo 
					LEFT JOIN rec.apoyos_movimientos p ON p.id_apoyo=a.id
					LEFT JOIN rec.movimientos mov ON mov.id=p.id_movimiento
					where v.codigo like '$vehiculo%' and mov.inicio::date='$fecha' and id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')
					AND mov.id_turno in (select id from turnos where turno like '%$id_turno%')
					AND (a.inicio>='$horainicio_mov' $horafin_mov)
					) as apoyo
			LEFT JOIN micros m ON m.id=apoyo.id_micro";		
#		echo $consultaapoyo;
			?>
				&nbsp
				<table width="850" cellspacing="0" cellpadding="0" align="center" border="0" style="font-size:12px">
				<tr>
					<td colspan=7 align="center" style="font-family: Arial; font-size: 14pt; background-color: #BDBDBD">Apoyos del Vehículo</td>
				</tr>
				<tr>
					<td class="WorkWithTitle" align="center">Ruta Apoyada</td>
					<td class="WorkWithTitle" width="100" align="center">Inicio</td>
					<td class="WorkWithTitle" align="center">Km Inicial</td>	 
					<td class="WorkWithTitle" align="center">Km Final</td>	 
					<td class="WorkWithTitle" align="center">Peso Apoyo</td>
					<td class="WorkWithTitle" width="100" align="center">Final</td>
					<td class="WorkWithTitle" align="center">Opciones</td>
				</tr>
			<?PHP	
			// traigo los descargues que tenga el vehículo
			$mfecha = date ( "Y-m-d" , $hora);
			$qidapoyo=$db->sql_query($consultaapoyo);
			$i=0;
						
			while($rowapoyo = $db->sql_fetchrow($qidapoyo))
			{
			
				$i++;
				if($i%2==0)
					$class='FreeStyleGridEvenB';
				else
					$class='FreeStyleGridEven';
			?>
				<tr class="<?PHP echo $class;?>" style="font-size:10px">			
					<td align="center"><input type=text disabled name=<?PHP echo $rowapoyo["id"];?>.apoyorut size=6  maxlength=8 value='<?echo $rowapoyo["ruta"];?>'></td>
					<td align="center"><input type=text disabled name=<?PHP echo $rowapoyo["id"];?>.apoyoini size=18  maxlength=19 value='<?echo $rowapoyo["hora_inicio"];?>'></td>
					<td align="center"><input type=text name=<?PHP echo $rowapoyo["id"];?>.apoyokin size=6  maxlength=8 value='<?echo $rowapoyo["km_inicial"];?>' onkeypress="return permite(event, 'num')" onchange=Recargarapoyo(<?echo $rowapoyo["id"]?>)></td>
					<td align="center"><input type=text name=<?PHP echo $rowapoyo["id"];?>.apoyokfi size=6  maxlength=8 value='<?echo $rowapoyo["km_final"];?>' onkeypress="return permite(event, 'num')" ></td>
					<td align="center"><input type=text name=<?PHP echo $rowapoyo["id"];?>.apoyopes size=6  maxlength=8 value='<?echo $rowapoyo["peso"];?>' onkeypress="return permite(event, 'num_pes')" ></td>
					<td align="center"><input type=text name=<?PHP echo $rowapoyo["id"];?>.apoyofin size=18  maxlength=19 value='<?echo $rowapoyo["hora_fin"];?>' onchange=Recargarapoyo(<?echo $rowapoyo["id"]?>)></td>
					<td align="center">
						<img src="images/button_del.png" width="15" height="15" style="cursor: pointer; border: 0px " title="Eliminar" onclick=eliminaapoyo(<?echo $rowapoyo["id"];?>) />
					</td>
				</tr>	 
				<?php
			}
			echo "</table>";
			
			?>
			<div id="apoyo" style="position: fixed; width: 450px; height: 250px; top: 0; left: 0; font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal; border: #333333 3px solid; background-color: #FAFAFA; color: #000000; display:none;">
				<div style="font-weight: bold; text-align: left; color: #FFFFFF; padding: 5px; background-color:#006394">Agregar Apoyos</div>
				<center>		
				<table class="Table1" width="410" cellspacing="0" cellpadding="0" align="center" border="0" style="">
				<tr>
					<td colspan="4">&nbsp; </td>
						<input type="hidden" name=id_movimientodesc value='<?echo $idMovimiento?>'>
				</tr>
				<tr>
					<td width="7" height="17"></td>
					<td width="101" class="CampoTitulo">(*)Movimiento Apoyado:</td>
					<td align="left">
						<select name=apruta class="CampoTexto">
							<option value="">Seleccionar..</option>
						   <?PHP
						   $qidsitio = $db->sql_query("SELECT mov.id,v.codigo as vehiculo,m.codigo as ruta 
							FROM rec.movimientos mov
							LEFT JOIN vehiculos v ON v.id=mov.id_vehiculo
							LEFT JOIN micros m ON m.id=mov.id_micro
							LEFT JOIN servicios s ON s.id=m.id_servicio
							LEFT JOIN personas p ON p.id=m.id_coordinador
							LEFT JOIN tipos_residuos t ON t.id=m.id_tipo_residuo
							LEFT JOIN cuartelillos c ON c.id=m.id_cuartelillo
							WHERE mov.inicio::date = '".$fecha."' 
							AND m.id_ase IN (SELECT id FROM ases 
								WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]'))
							order by 2,3");
							while($dessitio = $db->sql_fetchrow($qidsitio))
							{
								?>
								<option value="<?PHP echo $dessitio["id"]?>"><?PHP echo $dessitio["ruta"]."-".$dessitio["vehiculo"]?></option>
								<?php
							}
						   ?>
						</select>
					</td>
					<td width="8" style="height: 18px;" height="13"></td>
				</tr>
				<tr>
					<td height="13"></td>
					<td width="101" class="CampoTitulo">Km Inicio:</td>
					<td align="left"><input type=text name=apkmini size=6 maxlength=8 onkeypress="return permite(event, 'num')"></td>
					<td width="8" style="height: 18px;" height="13"></td>
				</tr>
				<tr>
					<td height="13"></td>
					<td width="101" class="CampoTitulo">Hora Inicio:</td>
					<td align="left"><input type=text name=aphrini size=6 maxlength=8 value='<?echo $mhora;?>' onkeypress="return permite(event, 'num_hor')"></td>
					<td width="8" style="height: 18px;" height="13"></td>
				</tr>
				</table>
				</center>
				<div style="padding: 10px; background-color: #F0F0F0; text-align: center; margin-top: 44px;">
					<input id="btnAceptar2" onclick="ocultarVentana('apoyo');" name="btnAceptar2" size="20" type="button" value="Aceptar"/> 
					<input id="btnCancelar2" onclick="cerrarVentana('apoyo');" name="btncancelar2" size="20" type="button" value="Cancelar"/> 
				</div>
			</div>
			<center>
			<a style="font-family: Arial; font-size: 14pt" href="javascript:mostrarVentana('apoyo');">Agregar Apoyo</a>
			</center>
			
			
			<div id="cerrarviaje" style="position: fixed; width: 450px; height: 250px; top: 0; left: 0; font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal; border: #333333 3px solid; background-color: #FAFAFA; color: #000000; display:none;">
				<div style="font-weight: bold; text-align: left; color: #FFFFFF; padding: 5px; background-color:#006394">Cerrar Movimiento</div>
				<center>		
				<table class="Table1" width="410" cellspacing="0" cellpadding="0" align="center" border="0" style="">
				<tr>
					<td colspan="4">&nbsp; </td>
						<input type="hidden" name=id_mov_cerrar value='<?echo $idMovimiento?>'>
				</tr>		
				<?
			if(!isset($kmsactual)){
			// traigo los ultimos km digitados o los ultimos del vehículo
				$mfecha = date ( "Y-m-d" , $hora);
				if ($fecmov==$mfecha){
					$consultakms = "SELECT cast(kilometraje as integer) as km, cast(horometro as integer) as horo FROM vehiculos
					where codigo like '".$vehiculo."%' and id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')";
				}
				else
				{
					$consultakms = "SELECT cast(max(km) as integer) as km, cast(max(horometro) as integer) as horo FROM rec.desplazamientos
						where id_movimiento=".$idMovimiento;
				}	
				$qidkms=$db->sql_query($consultakms);
				while($rowkms = $db->sql_fetchrow($qidkms))
				{
						$kmsactual = $rowkms["km"];
						$horactual = $rowkms["horo"];
				}
			}
		?>				
				<tr>
					<td height="13"></td>
					<td width="101" class="CampoTitulo">Hora Regreso:</td>
					<td align="left"><input type=text name=nuevahorafinal size=6 maxlength=8 value='<?echo $mhora;?>' onkeypress="return permite(event, 'num_hor')"></td>
					<td width="8" style="height: 18px;" height="13"></td>
				</tr>
				<tr>
					<td height="13"></td>
					<td width="101" class="CampoTitulo">Kilometraje:</td>
					<td align="left"><input type=text name=nuevokmfinal size=6 maxlength=6 value='<?echo $kmsactual;?>' onkeypress="return permite(event, 'num')"></td>
					<td width="8" style="height: 18px;" height="13"></td>
				</tr>
				<tr>
					<td height="13"></td>
					<td width="101" class="CampoTitulo">Horometro:</td>
					<td align="left"><input type=text name=nuevohorofinal size=6 maxlength=6 value='<?echo $horactual;?>' ></td>
					<td width="8" style="height: 18px;" height="13"></td>
				</tr>
				</table>
				</center>
				<div style="padding: 10px; background-color: #F0F0F0; text-align: center; margin-top: 44px;">
					<input id="btnAceptar2" onclick="ocultarVentana('cerrarviaje');" name="btnAceptar2" size="20" type="button" value="Aceptar"/> 
					<input id="btnCancelar2" onclick="cerrarVentana('cerrarviaje');" name="btncancelar2" size="20" type="button" value="Cancelar"/> 
				</div>
			</div>
		<?PHP	

		
		//Fin de mostar información
		}
	}
}
?>
</form>
</div>
</body>
</html>
<?php
//echo "<pre>";
//print_r($_POST);
//print_r($_GET);
// echo "</pre>"; 
?>
