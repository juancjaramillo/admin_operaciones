<?
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
include("../application.php");

$user=$_SESSION[$CFG->sesion]["user"];

if(isset($_POST["id_centro"]) && $_POST["id_centro"] != "")
	$centro = $_POST["id_centro"];
elseif(isset($_GET["id_centro"]) && $_GET["id_centro"] != "")
	$centro = $_GET["id_centro"];
else
{
	$qidCentro = $db->sql_row("SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."' ORDER BY id_centro");
	$centro = $qidCentro["id_centro"];
}

if(!isset($_POST["inicio"]))
{
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/info/templates/fechas_form.php");
}else
{
	$titulo1 = $db->sql_row("SELECT upper(nombre||' : '||informe) as inf FROM informes i LEFT JOIN categorias_informes c ON c.id=i.id_categoria_informe WHERE i.id=".str_replace(".php","",simple_me($ME)));
	$inicio = $_POST["inicio"];
	$final = $_POST["final"];
	$idAse = $_POST["id_ase"];

	$ase = $db->sql_row("SELECT a.ase, a.nuap, m.municipio, d.departamento, a.fecha_entrada
		FROM ases a
		LEFT JOIN centros c ON c.id=a.id_centro
		LEFT JOIN municipios m ON m.id=c.id_municipio
		LEFT JOIN departamentos d ON d.id=m.id_departamento
		WHERE a.id=".$idAse);

	require_once $CFG->common_libdir."/writeexcel/class.writeexcel_workbook.inc.php";
	require_once $CFG->common_libdir."/writeexcel/class.writeexcel_worksheet.inc.php";

	$fname=$CFG->tmpdir."/informe.xls";
	if(file_exists($fname))
		unlink($fname);

	$workbook = new writeexcel_workbook($fname);
	$workbook->set_tempdir($CFG->tmpdir);

	$styleTit =& $workbook->addformat(array("align"=>"center","bold"=>"1","size"=>"11","merge"=>"1"));
	$styleTitBorder =& $workbook->addformat(array("align"=>"center","bold"=>"1","size"=>"11","merge"=>"1", "border"=>"1"));
	$styleTitBorderSimple =& $workbook->addformat(array("align"=>"center", "valign"=>"vcenter", "size"=>"11", "merge"=>"1", "border"=>"1"));
	$styleTextLeft =& $workbook->addformat(array("align"=>"left","size"=>"11","border"=>"1"));
	$styleTextCenter =& $workbook->addformat(array("align"=>"center","size"=>"11","border"=>"1"));

	//Artículo 4.4.1.2 FORMULARIO. REGISTRO DE -ÁREAS DE PRESTACIÓN DEL SERVICIO
	$worksheet = &$workbook->addworksheet("Formulario 1");
	$worksheet->set_column(0,0,50);
	$worksheet->set_column(1,1,30);
	$worksheet->write(1,0,"REGISTRO DE ÁREAS DE PRESTACIÓN DEL SERVICIO",$styleTit);
	$worksheet->merge_cells(1,0,1,1);
	$worksheet->write(3,0,"CONCEPTO",$styleTitBorder);	$worksheet->write(3,1,"INFORMACIÓN",$styleTitBorder);
	$worksheet->write(4,0,"Nombre del Departamento",$styleTextLeft); $worksheet->write(4,1,$ase["departamento"],$styleTextCenter);  
	$worksheet->write(5,0,"Nombre del Municipio",$styleTextLeft); $worksheet->write(5,1,$ase["municipio"],$styleTextCenter);
	$worksheet->write(6,0,"Nombre del área de prestación del servicio",$styleTextLeft); $worksheet->write(6,1,$ase["ase"],$styleTextCenter);
	$worksheet->write(7,0,"Fecha de entrada en operación del área de prestación",$styleTextLeft); $worksheet->write(7,1,ucfirst(strftime("%B %d de %Y",strtotime($ase["fecha_entrada"]))),$styleTextCenter);
	$worksheet->write(8,0,"NUAP",$styleTextLeft); $worksheet->write(8,1,$ase["nuap"],$styleTextCenter);  




/*--------------
	//Artículo 4.4.1.3 FORMULARIO. CENTROIDE DEL ÁREA DE PRESTACIÓN DEL SERVICIO
	$worksheet = &$workbook->addworksheet("Formulario 2");
	$worksheet->set_column(0,0,70);
	$worksheet->set_column(1,1,20);
	$worksheet->write(1,0,"CENTROIDE DEL ÁREA DE PRESTACIÓN DEL SERVICIO",$styleTit);
	$worksheet->merge_cells(1,0,1,1);
	$worksheet->write(3,0,"CONCEPTO",$styleTitBorder);	$worksheet->write(3,1,"INFORMACIÓN",$styleTitBorder);
	$worksheet->write(4,0,"NUAP",$styleTextLeft); $worksheet->write(4,1,$ase["nuap"],$styleTextCenter);  
	$worksheet->write(5,0,"Nombre del departamento donde está ubicado el centroide",$styleTextLeft); $worksheet->write(5,1,$ase["departamento"],$styleTextCenter);
	$worksheet->write(6,0,"Nombre del municipio donde está ubicado el centroide",$styleTextLeft); $worksheet->write(6,1,$ase["municipio"],$styleTextCenter);
	$worksheet->write(7,0,"Nombre del centro poblado donde está ubicado el centroide",$styleTextLeft); $worksheet->write(7,1,$ase["municipio"],$styleTextCenter);
	$worksheet->write(8,0,"Punto inicial de medición de la distancia al sitio de disposición final",$styleTextLeft); $worksheet->write(8,1,"",$styleTextCenter);
	$worksheet->write(9,0,"Longitud",$styleTextLeft); $worksheet->write(9,1,"",$styleTextCenter);
	$worksheet->write(10,0,"Latitud",$styleTextLeft); $worksheet->write(10,1,"",$styleTextCenter);
	$worksheet->write(11,0,"Altitud",$styleTextLeft); $worksheet->write(11,1,"",$styleTextCenter);
	$worksheet->write(12,0,"Dirección del centroide",$styleTextLeft); $worksheet->write(12,1,"",$styleTextCenter);  
	
	//Artículo 4.4.1.4 FORMATO. VÉRTICES DEL ÁREAS DE PRESTACIÓN DEL SERVICIO
	$worksheet = &$workbook->addworksheet("Formulario 3");
	$worksheet->set_column(0,0,40);
	$worksheet->set_column(1,1,20);
	$worksheet->write(1,0,"VÉRTICES DEL ÁREAS DE PRESTACIÓN DEL SERVICIO",$styleTit);
	$worksheet->merge_cells(1,0,1,1);
	$worksheet->write(3,0,"CONCEPTO",$styleTitBorder);	$worksheet->write(3,1,"INFORMACIÓN",$styleTitBorder);
	$worksheet->write(4,0,"NUAP",$styleTextLeft); $worksheet->write(4,1,$ase["nuap"],$styleTextCenter);  
	$worksheet->write(5,0,"Código DANE",$styleTextLeft); $worksheet->write(5,1,"",$styleTextCenter);
	$worksheet->write(6,0,"Número de vértice",$styleTextLeft); $worksheet->write(6,1,"",$styleTextCenter);  
	$worksheet->write(7,0,"Longitud",$styleTextLeft); $worksheet->write(7,1,"",$styleTextCenter);  
	$worksheet->write(8,0,"Latitud",$styleTextLeft); $worksheet->write(8,1,"",$styleTextCenter);  
	$worksheet->write(9,0,"Altitud",$styleTextLeft); $worksheet->write(9,1,"",$styleTextCenter);  
	$worksheet->write(10,0,"Dirección del vértice",$styleTextLeft); $worksheet->write(10,1,"",$styleTextCenter);  
--------------------*/


	//Artículo 4.4.1.5 FORMULARIO. ACTUALIZACIÓN DE ESTADO - ÁREAS DE PRESTACIÓN DEL SERVICIO
	$worksheet = &$workbook->addworksheet("Formulario 4");
	$worksheet->set_column(0,0,50);
	$worksheet->set_column(1,1,30);
	$worksheet->write(1,0,"ACTUALIZACIÓN DE ESTADO - ÁREAS DE PRESTACIÓN DEL SERVICIO",$styleTit);
	$worksheet->merge_cells(1,0,1,1);
	$worksheet->write(3,0,"CONCEPTO",$styleTitBorder);	$worksheet->write(3,1,"INFORMACIÓN",$styleTitBorder);
	$worksheet->write(4,0,"Nombre del área de prestación del servicio",$styleTextLeft); $worksheet->write(4,1,$ase["ase"],$styleTextCenter);
	$worksheet->write(5,0,"Estado",$styleTextLeft); $worksheet->write(5,1,"En Operación",$styleTextCenter);
	$worksheet->write(6,0,"Fecha en que adquirió el estado",$styleTextLeft); $worksheet->write(6,1,ucfirst(strftime("%B %d de %Y",strtotime($ase["fecha_entrada"]))),$styleTextCenter);

	//Artículo 4.4.1.6 FORMATO. REGISTRO DE MICRORUTAS
	$worksheet = &$workbook->addworksheet("Formato 5");
	$worksheet->set_column(0,1,15);
	$worksheet->set_column(3,3,50);
	$worksheet->set_column(4,4,20);
	$worksheet->set_column(5,5,50);
	$worksheet->set_column(6,15,20);
	$worksheet->write(1,0,"REGISTRO DE MICRORUTAS",$styleTit);
	$worksheet->merge_cells(1,0,1,14);
	$titulos = array("Ruta","Microruta","Tipo de\nmicroruta", "NUAP", "Dirección del predio de inicio de la microruta", "Hora de inicio\nde la microruta", "Dirección del predio de finalización de la microruta", "Hora de finalización\nde la microruta", "Distancia en vía\npavimentada de la\nmicroruta (Km.)", "Distancia en vía\nno pavimentada de la\nmicroruta (Km.)", "Frecuencia\n(veces/semana)", "Días de la\nfrecuencia", "Recolección\nselectiva", "Tipo de residuos\nrecolectados", "Estación de\ntransferencia", "Fecha de entrada\nen operación de\nla microruta");
	$columna=0; $fila=3;
	foreach($titulos as $tt)
	{
		$worksheet->write($fila, $columna, $tt, $styleTitBorderSimple);
		$columna++;
	}
	$columna=0; $fila++;
	$qidM = $db->sql_query("SELECT r.codigo as ruta,r.id, r.codigo_sui as codigo, tms.codigo as tipo_micro, r.selectiva, trs.codigo as tresiduo, case when r.id_lugar_descargue IS NULL then 'NO' else 'SÍ' end termina_transferencia, r.fecha_desde
		FROM micros r
		LEFT JOIN servicios s ON s.id=r.id_servicio
		LEFT JOIN tipo_micros_sui tms ON tms.id=s.id_tipo_micro_sui
		LEFT JOIN tipos_residuos tr ON tr.id = r.id_tipo_residuo
		LEFT JOIN tipos_residuos_sui trs ON trs.id = tr.id_tipo_residuo_sui 
		WHERE r.id_ase='".$idAse."' AND s.esquema='rec' AND r.fecha_hasta IS NULL
		ORDER BY r.codigo_sui");
	while($ruta = $db->sql_fetchrow($qidM))
	{
		$dxFrec = $db->sql_row("SELECT min(hora_inicio)  as inicio, min(hora_fin)  as fin,  count(id) as numveces FROM micros_frecuencia WHERE id_micro=".$ruta["id"]);

		$linea = array($ruta["ruta"],$ruta["codigo"], $ruta["tipo_micro"], $ase["nuap"],"", $dxFrec["inicio"], "", $dxFrec["fin"], "", "",  $dxFrec["numveces"], diasFrecuencia($ruta["id"]), $ruta["selectiva"], $ruta["tresiduo"], $ruta["termina_transferencia"], ucfirst(strftime("%B %d de %Y",strtotime($ruta["fecha_desde"]))));
		
		foreach($linea as $dx)
		{
			$worksheet->write($fila, $columna, $dx, $styleTextCenter);
			$columna++;
		}
		$columna=0; $fila++;
	}

	//Artículo 4.4.1.7 FORMATO. TONELADAS PROVENIENTES DEL ÁREA DE PRESTACION DEL SERVICIO.
	$worksheet = &$workbook->addworksheet("Formato 6");
	$worksheet->set_column(0,0,13);
	$worksheet->set_column(1,1,18);
	$worksheet->set_column(3,7,13);
	$worksheet->set_column(8,11,24);
	$worksheet->set_column(12,12,12);
	$titulos = array("Tipo de sitio", "Numero del sitio al\nque le entregan\nlos residuos", "NUAP", "Placa\nVehiculo", "Fecha", "Hora de\nentrada del\nvehiculo", "Hora de\nsalida del\nvehiculo", "Microruta", "Toneladas recogidas en\nsuelo urbano asociadas\nal barrido y limpieza\nprovenientes del area\nde prestacion del\n servicio Ton", "Toneladas recogidas en\nsuelo rural asociadas al\nbarrido y limpieza\nprovenientes del area\nde prestacion del\nservicio Ton", "Toneladas recogidas\ndel servicio ordinario,\nen suelo urbano\nprovenientes del area\nde prestacion del\nservicio Ton", "Toneladas recogidas\ndel servicio ordinario\nen suelo rural\nprovenientes del area\nde prestación del\nservicio Ton", "Sistema de\nmedicion");
	$columna=0; $fila=0;
	foreach($titulos as $tt)
	{
		$worksheet->write($fila, $columna, $tt, $styleTitBorderSimple);
		$columna++;
	}
	$columna=0; $fila++;
	$qidPeso = $db->sql_query("SELECT distinct(p.id), l.tipo, p.fecha_entrada, l.codigo_sui,  v.placa, p.fecha_salida, medicion, peso_inicial, peso_final, peso_total
		FROM rec.pesos p
		LEFT JOIN rec.movimientos_pesos mp ON mp.id_peso=p.id
		LEFT JOIN rec.movimientos m ON m.id=mp.id_movimiento
		LEFT JOIN micros r ON r.id = m.id_micro
		LEFT JOIN lugares_descargue l ON l.id = p.id_lugar_descargue
		LEFT JOIN vehiculos v ON v.id = p.id_vehiculo 
		WHERE r.id_ase = '".$idAse."' AND fecha_entrada::date >= '".$inicio ."' AND fecha_entrada::date <= '".$final."'
		ORDER BY fecha_entrada, p.id");
	while($peso = $db->sql_fetchrow($qidPeso))
	{
		$totalPeso = 0;
		if($peso["peso_total"] != "") $totalPeso = $peso["peso_total"];
		elseif($peso["peso_inicial"] != "" && $peso["peso_final"] != "") $totalPeso = $peso["peso_inicial"]-$peso["peso_final"];

		$micros = microsXPeso($peso["id"]);
		$tons = calcularPorc($totalPeso, $micros["ids"]);
		$linea = array($peso["tipo"], $peso["codigo_sui"], $ase["nuap"], $peso["placa"], strftime("%Y-%m-%d",strtotime($peso["fecha_entrada"])), strftime("%H:%M",strtotime($peso["fecha_entrada"])), strftime("%H:%M",strtotime($peso["fecha_salida"])), $micros["codigos_sui"] , $tons["bar_urb"], $tons["bar_ru"], $tons["rec_urb"], $tons["rec_ru"], $peso["medicion"]);

		foreach($linea as $dx)
		{
			$worksheet->write($fila, $columna, $dx, $styleTextCenter);
			$columna++;
		}
		$columna=0; $fila++;
	}


	//Artículo 4.4.1.8 FORMULARIO. CONTINUIDAD EN RECOLECCIÓN DEL SERVICIO DE ASEO
	$worksheet = &$workbook->addworksheet("Formulario 7");
	$worksheet->set_column(0,0,80);
	$worksheet->set_column(1,12,20);
	$worksheet->write(1,0,"CONTINUIDAD EN RECOLECCIÓN DEL SERVICIO DE ASEO",$styleTit);
	$columna=0; $fila=3;
#$meses = sacarMeses_InicioyFin($inicio,$final);
	$meses = sacarMeses($inicio,$final);
	$dejo = sacarVecesDejoPS($idAse, $meses, "rec");
	
	$tit = array("CONCEPTO");
	$titDos = $titTres = $lineDejo = $suscriptores = array();
	foreach($meses as $key => $dx)
	{
		$tit[] = "INFORMACIÓN";
		$titDos[] = $ase["nuap"];
		$titTres[] = ucfirst(strftime("%B",strtotime(date("Y")."-".$key."-01")));
		if(isset($dejo[$key])) $lineDejo[] = $dejo[$key];  else $lineDejo[]=0;
		$suscriptores[] = 0;
	}
	foreach($tit as $dx)
	{
		$worksheet->write($fila, $columna, $dx, $styleTitBorder);
		$columna++;
	}
	$columna=0; $fila++;
	$worksheet->write($fila, $columna, "NUAP", $styleTextLeft);
	$columna++;
	foreach($titDos as $dx)
	{
		$worksheet->write($fila, $columna, $dx, $styleTextCenter);
		$columna++;
	}
	$columna=0; $fila++;
	$worksheet->write($fila, $columna, "Período de facturación", $styleTextLeft);
	$columna++;
	foreach($titTres as $dx)
	{
		$worksheet->write($fila, $columna, $dx, $styleTextCenter);
		$columna++;
	}
	$columna=0; $fila++;
	$worksheet->write($fila, $columna, "Número de veces que se dejó de prestar el servicio", $styleTextLeft);
	$columna++;
	foreach($lineDejo as $dx)
	{
		$worksheet->write($fila, $columna, $dx, $styleTextCenter);
		$columna++;
	}
	$columna=0; $fila++;
	$worksheet->write($fila, $columna, "Número de suscriptores afectados por la no prestación del servicio de recolección", $styleTextLeft);
	$columna++;
	foreach($suscriptores as $dx)
	{
		$worksheet->write($fila, $columna, $dx, $styleTextCenter);
		$columna++;
	}

	//Artículo 4.4.1.9 FORMATO. PEAJES
	$worksheet = &$workbook->addworksheet("Formato 8");
	$worksheet->set_column(1,9,20);
	$worksheet->write(1,0,"PEAJES",$styleTit);
	$worksheet->merge_cells(1,0,1,7);
	$titulos = array("NUAP", "Número del sitio que\nrecibe los residuos", "Nombre de peaje", "Ubicación Peaje", "Valor peaje para un\nvehículo de 2 ejes ($)", "Valor peaje para un\nvehículo de 5 ejes ($)", "Sentido de pago\ndel peaje", "Clasificación de\nestación de peaje");
	$columna=0; $fila=3;
	foreach($titulos as $tt)
	{
		$worksheet->write($fila, $columna, $tt, $styleTitBorderSimple);
		$columna++;
	}
	$columna=0; $fila++;
	$consulta = "SELECT p.id, l.codigo_sui as descargue, p.nombre as peaje, p.sentido, cp.codigo as categoria
		FROM rec.movimientos_peajes mp 
		LEFT JOIN rec.movimientos m ON m.id=mp.id_movimiento 
		LEFT JOIN peajes p ON p.id=mp.id_peaje 
		LEFT JOIN micros r on r.id=m.id_micro 
		LEFT JOIN lugares_descargue l ON l.id=p.id_lugar_descargue
		LEFT JOIN clasificacion_peajes cp ON cp.id = p.id_clasificacion_peaje
		WHERE r.id_ase = ".$idAse." and m.inicio::date >='".$inicio."' AND m.inicio::date <='".$final."'";
	$qid = $db->sql_query($consulta);
	$peajes = array();
	while($pe = $db->sql_fetchrow($qid))
	{
		$peajes[$pe["id"]] = $pe;
	}
	
	foreach($peajes as $key => $dxP)
	{
		$ejes2 = $db->sql_row("SELECT precio FROM peajes_vigencias pv LEFT JOIN tipos_vehiculos tv ON tv.id = pv.id_tipo_vehiculo WHERE pv.id_peaje = ".$dxP["id"] . " AND ejes= 2 AND pv.inicio_vigencia <= '".$inicio."' AND fin_vigencia >= '".$final."'");
		$ejes5 = $db->sql_row("SELECT precio FROM peajes_vigencias pv LEFT JOIN tipos_vehiculos tv ON tv.id = pv.id_tipo_vehiculo WHERE pv.id_peaje = ".$dxP["id"] . " AND ejes= 5 AND pv.inicio_vigencia <= '".$inicio."' AND fin_vigencia >= '".$final."'");
		
		$linea = array($ase["nuap"], $dxP["descargue"], $dxP["peaje"], "", nvl($ejes2["precio"],""), nvl($ejes5["precio"],""), $dxP["sentido"], $dxP["categoria"]);
		foreach($linea as $dx)
		{
			$worksheet->write($fila, $columna, $dx, $styleTextCenter);
			$columna++;
		}
		$columna=0; $fila++;
	}

	//Artículo 4.4.1.10 FORMATO. REGISTRO DE VEHÍCULOS PARA EL TRANSPORTE DE RESIDUOS SÓLIDOS
	$worksheet = &$workbook->addworksheet("Formato 9");
	$worksheet->set_column(0,0,10);
	$worksheet->set_column(1,9,20);
	$worksheet->write(1,0,"REGISTRO DE VEHÍCULOS DE RECOLECCIÓN Y TRANSPORTE",$styleTit);
	$worksheet->merge_cells(1,0,1,9);
	$worksheet->set_column(1,9,23);
	$titulos = array("PLACA", "MARCA", "CAPACIDAD (yd3)", "CAPACIDAD (Ton)", "NÚMERO DE EJES", "MODELO", "TIPO DE VEHÍCULO", "FECHA DE ENTRADA\nEN OPERACIÓN DEL\nVEHÍCULO", "TIPO DE USO\nDEL VEHICULO", "ACTIVIDAD\nDESARROLLADA\nPOR EL VEHICULO");
	$columna=0; $fila=3;
	foreach($titulos as $tt)
	{
		$worksheet->write($fila, $columna, $tt, $styleTitBorderSimple);
		$columna++;
	}
	$columna=0; $fila++;
	$consulta = "SELECT distinct(m.id_vehiculo), v.placa, mv.marca, t.capacidad, t.capacidad_yardas, t.ejes, v.ano, s.codigo as tipo, v.fecha_entrada_operacion, a.codigo
		FROM rec.movimientos m
		LEFT JOIN vehiculos v ON v.id = m.id_vehiculo
		LEFT JOIN referencias ref ON ref.id = v.id_referencia
		LEFT JOIN marcas_vehiculos mv ON mv.id = ref.id_marca_vehiculo
		LEFT JOIN tipos_vehiculos t ON t.id = v.id_tipo_vehiculo
		LEFT JOIN tipos_vehiculos_sui s ON s.id = v.id_tipo_vehiculo_sui
		LEFT JOIN actividad_vehiculo_sui a ON a.id = v.id_actividad_vehiculo_sui
		LEFT JOIN micros r ON r.id = m.id_micro
		WHERE r.id_ase = '".$idAse."' AND v.id_estado != 4
		ORDER BY v.placa";
	$qidV = $db->sql_query($consulta);
	while ($ve = $db->sql_fetchrow($qidV))
	{
		$linea =  array($ve["placa"], $ve["marca"], $ve["capacidad_yardas"], $ve["capacidad"], $ve["ejes"], $ve["ano"], $ve["tipo"], strftime("%d-%m-%Y",strtotime($ve["fecha_entrada_operacion"])), "1",  $ve["codigo"]);
		foreach($linea as $dx)
		{
			$worksheet->write($fila, $columna, $dx, $styleTextCenter);
			$columna++;
		}
		$columna=0; $fila++;
	}
	

	//Artículo 4.4.1.11 FORMULARIO. ACTUALIZACIÓN DE ESTADO - VEHÍCULOS
	$worksheet = &$workbook->addworksheet("Formato 10");
	$worksheet->set_column(0,0,10);
	$worksheet->set_column(1,3,20);
	$worksheet->write(1,0,"ACTUALIZACIÓN DE ESTADO - VEHÍCULOS",$styleTit);
	$worksheet->merge_cells(1,0,1,3);
	$worksheet->set_column(1,9,23);
	$titulos = array("PLACA", "FECHA ESTADO\nACTUAL", "ESTADO", "FECHA EN QUE\nADQUIRIÓ EL ESTADO");
	$columna=0; $fila=3;
	foreach($titulos as $tt)
	{
		$worksheet->write($fila, $columna, $tt, $styleTitBorderSimple);
		$columna++;
	}
	$columna=0; $fila++;
	$consulta = "SELECT distinct(m.id_vehiculo), v.placa, v.fecha_salida_operacion
		FROM rec.movimientos m
		LEFT JOIN vehiculos v ON v.id = m.id_vehiculo
		LEFT JOIN micros r ON r.id = m.id_micro
		WHERE r.id_ase = '".$idAse."' AND v.id_estado = 4
		ORDER BY v.placa";
	$qidV = $db->sql_query($consulta);
	while ($ve = $db->sql_fetchrow($qidV))
	{
		$linea =  array($ve["placa"], "", "Inactivo", $ve["fecha_entrada_operacion"]);
		foreach($linea as $dx)
		{
			$worksheet->write($fila, $columna, $dx, $styleTextCenter);
			$columna++;
		}
		$columna=0; $fila++;
	}


	//ésta hoja no esta en la resolución, pero si en los archiovs que envió Cristian
	$worksheet = &$workbook->addworksheet("Formato 11");
	$worksheet->set_column(0,6,15);
	
	$worksheet->write(1,0,"BARRIDO Y LIMPIEZA",$styleTit);
	$worksheet->merge_cells(1,0,1,6);
	$titulos = array("Microbarrido", "NUAP", "Tipo de\nbarrido", "Frecuencia\n(veces/semana)", "Vías barridas\n(km)", "Semana", "Mes");
	$columna=0; $fila=3;
	foreach($titulos as $tt)
	{
		$worksheet->write($fila, $columna, $tt, $styleTitBorderSimple);
		$columna++;
	}
	$columna=0; $fila++;
	$qidM = $db->sql_query("SELECT r.id, r.codigo_sui as codigo, tb.codigo as tipo_barrido, r.km
		FROM micros r
		LEFT JOIN servicios s ON s.id=r.id_servicio
		LEFT JOIN tipos_barridos_sui tb ON tb.id = s.id_tipo_barrido_sui
		WHERE r.id_ase='".$idAse."' AND s.esquema='bar' AND r.fecha_hasta IS NULL
		ORDER BY r.codigo_sui");
	while($ruta = $db->sql_fetchrow($qidM))
	{
		$qidFrec = $db->sql_row("SELECT count(dia) as num FROM micros_frecuencia WHERE id_micro = ".$ruta["id"]);
		$linea = array($ruta["codigo"], $ase["nuap"], $ruta["tipo_barrido"], diasFrecuencia($ruta["id"]), $ruta["km"], nvl($qidFrec["num"])*$ruta["km"], (nvl($qidFrec["num"])*$ruta["km"]) * 4.26);
		
		foreach($linea as $dx)
		{
			$worksheet->write($fila, $columna, $dx, $styleTextCenter);
			$columna++;
		}
		$columna=0; $fila++;
	}
	
	//Artículo 4.4.1.12 FORMULARIO. CONTINUIDAD EN BARRIDO Y LIMPIEZA
	$worksheet = &$workbook->addworksheet("Formulario 12");
	$worksheet->set_column(0,0,60);
	$worksheet->set_column(1,12,20);
	$worksheet->write(1,0,"CONTINUIDAD EN RECOLECCIÓN DEL SERVICIO DE ASEO",$styleTit);
	$columna=0; $fila=3;
#$meses = sacarMeses_InicioyFin($inicio,$final);
	$meses = sacarMeses($inicio,$final);
	$kmsBarridos = kmBarridos($idAse, $meses);
	$kmsNOBarridos = kmBarridos($idAse, $meses, true);
	
	$tit = array("CONCEPTO");
	$titDos = $titTres = $lineBarrio = $noBarrio = array();
	foreach($meses as $key => $dx)
	{
		$tit[] = "INFORMACIÓN";
		$titDos[] = $ase["nuap"];
		$titTres[] = ucfirst(strftime("%B",strtotime(date("Y")."-".$key."-01")));
		if(isset($kmsBarridos[$key])) $lineBarrio[] = $kmsBarridos[$key];  else $lineBarrio[]=0;
		if(isset($kmsNOBarridos[$key])) $noBarrio[] = $kmsNOBarridos[$key];  else $noBarrio[]=0;
	}
	foreach($tit as $dx)
	{
		$worksheet->write($fila, $columna, $dx, $styleTitBorder);
		$columna++;
	}
	$columna=0; $fila++;
	$worksheet->write($fila, $columna, "NUAP", $styleTextLeft);
	$columna++;
	foreach($titDos as $dx)
	{
		$worksheet->write($fila, $columna, $dx, $styleTextCenter);
		$columna++;
	}
	$columna=0; $fila++;
	$worksheet->write($fila, $columna, "Período de facturación", $styleTextLeft);
	$columna++;
	foreach($titTres as $dx)
	{
		$worksheet->write($fila, $columna, $dx, $styleTextCenter);
		$columna++;
	}
	$columna=0; $fila++;
	$worksheet->write($fila, $columna, "Kilómetros de cuneta barridos (km)", $styleTextLeft);
	$columna++;
	foreach($kmsBarridos as $dx)
	{
		$worksheet->write($fila, $columna, $dx, $styleTextCenter);
		$columna++;
	}
	$columna=0; $fila++;
	$worksheet->write($fila, $columna, "Kilómetros de cuneta que dejó de barrer (km)", $styleTextLeft);
	$columna++;
	foreach($kmsNOBarridos as $dx)
	{
		$worksheet->write($fila, $columna, $dx, $styleTextCenter);
		$columna++;
	}





//die;
	//fin
	$workbook->close();
	$nombreArchivo=preg_replace("/[^0-9a-z_.]/i","_",$titulo1["inf"])."_".$inicio."_".$final.".xls";
	header("Content-Type: application/x-msexcel; name=\"".$nombreArchivo."\"");
	header("Content-Disposition: inline; filename=\"".$nombreArchivo."\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
}





function diasFrecuencia($id)
{
	global $db, $CFG;

	$dias = array();
	$qidFrec = $db->sql_query("SELECT dia FROM micros_frecuencia WHERE id_micro = ".$id." ORDER BY dia");
	while($frec = $db->sql_fetchrow($qidFrec))
	{
		$dias[] = $frec["dia"];
	}
	return implode("-",$dias);
}


function microsXPeso($idPeso)
{
	global $db, $CFG;

	$micros = $idsMicros = array();
	$qidM = $db->sql_query("SELECT r.codigo_sui as codigo, r.id as id_micro, mp.porcentaje, porc_rural, porc_urbano, s.esquema
		FROM rec.movimientos_pesos mp
		LEFT JOIN rec.movimientos m ON m.id=mp.id_movimiento
		LEFT JOIN micros r ON r.id=m.id_micro
		LEFT JOIN servicios s ON s.id = r.id_servicio
		WHERE mp.id_peso = '".$idPeso."'");
	while($rut = $db->sql_fetchrow($qidM))
	{
		$micros[] = $rut["codigo"];
		$idsMicros[][$rut["id_micro"]] = array("rec" => $rut["porcentaje"], "rural"=>$rut["porc_rural"], "urbano"=>$rut["porc_urbano"], "tipo"=>$rut["esquema"]);
	}
	
	return array("codigos_sui" => implode(", ", $micros), "ids"=>$idsMicros);
}


function calcularPorc($totalPeso, $idsMicros)
{
	global $db;

	$bar_ru = $bar_urb = $rec_ru = $rec_urb = 0;

	foreach($idsMicros as $mc)
	{
		$dx = array_shift($mc);
		$peso = ($totalPeso * $dx["rec"])/100;
		if($dx["tipo"] == "bar")
		{
			$bar_ru+=($peso*$dx["rural"])/100; 
			$bar_urb+=($peso*$dx["urbano"])/100; 
		}else
		{
			$rec_ru+=($peso*$dx["rural"])/100;
			$rec_urb+=($peso*$dx["urbano"])/100;
		}
	}

	return array("bar_ru"=>$bar_ru, "bar_urb"=>$bar_urb, "rec_ru"=>$rec_ru, "rec_urb"=>$rec_urb);
}




function sacarVecesDejoPS($idAse, $meses, $squema)
{
	global $db, $CFG;

	$num =   array();
	foreach($meses as $key => $dx)
	{
		$diasBTW = restarFechas($dx["fi"], $dx["in"]);
		for($i=0 ; $i<=$diasBTW; $i++)
		{
			list($anio,$mes,$dia)=split("-",$dx["in"]);
			$dia = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + $i * 24 * 60 * 60);
			$consulta = "SELECT count(m.id) as numero
					FROM micros m
					LEFT JOIN servicios s ON s.id = m.id_servicio
					LEFT JOIN ases a ON a.id=m.id_ase
					WHERE s.esquema='".$squema."' AND m.fecha_hasta IS NULL AND m.id_ase = '".$idAse."' AND m.id IN (SELECT id_micro FROM micros_frecuencia WHERE dia='".strftime("%u",strtotime($dia))."') AND m.id NOT IN (SELECT id_micro FROM ".$squema.".movimientos WHERE inicio::date='".$dia."')";
			$qid = $db->sql_row($consulta);
			if(!isset($num[strftime("%m",strtotime($dia))])) $num[strftime("%m",strtotime($dia))] = 0;
			$num[strftime("%m",strtotime($dia))]+=$qid["numero"];
		}
	}

	return $num;
}

function kmBarridos($idAse, $meses, $notIn = false)
{
	global $db, $CFG;

	$kms =   array();
	if($notIn)
		$cond = " NOT IN ";
	else
		$cond = " IN ";

	foreach($meses as $key => $dx)
	{
		$diasBTW = restarFechas($dx["fi"], $dx["in"]);
		for($i=0 ; $i<=$diasBTW; $i++)
		{
			list($anio,$mes,$dia)=split("-",$dx["in"]);
			$dia = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + $i * 24 * 60 * 60);
			$consulta = "SELECT sum(m.km::real) as numero
					FROM micros m
					LEFT JOIN servicios s ON s.id = m.id_servicio
					LEFT JOIN ases a ON a.id=m.id_ase
					WHERE s.esquema='bar' AND m.fecha_hasta IS NULL AND m.id_ase = '".$idAse."' AND m.id IN (SELECT id_micro FROM micros_frecuencia WHERE dia='".strftime("%u",strtotime($dia))."') AND m.id ".$cond." (SELECT id_micro FROM bar.movimientos WHERE inicio::date='".$dia."')";
			$qid = $db->sql_row($consulta);
			if(!isset($kms[strftime("%m",strtotime($dia))])) $kms[strftime("%m",strtotime($dia))] = 0;
			$kms[strftime("%m",strtotime($dia))]+=$qid["numero"];
		}
	}

	return $kms;
}



?>
