<?
include_once("../application.php");
verificarPagina(simple_me($ME));
$frm = $_GET;

$user=$_SESSION[$CFG->sesion]["user"];
$cond = array("o.fecha_planeada >= '".$frm["inicio"]." 00:00:00' AND o.fecha_planeada <= '".$frm["final"]." 23:59:59'");

if(isset($frm["mode"]) && $frm["mode"] == "bajar_resultados")
{
	$cond = array("r.activa");
	
	if(isset($frm["id_tipo_mantenimiento"]))
		$cond[] = "r.id_tipo_mantenimiento='".$frm["id_tipo_mantenimiento"]."'";
	if(isset($frm["id_sistema"]))
		$cond[] = "r.id_sistema='".$frm["id_sistema"]."'";
	if(isset($frm["id_rutina"]))
		$cond[] = "o.id_rutina='".$frm["id_rutina"]."'";
	if(isset($frm["id_equipo"]))
		$cond[] = "o.id_equipo='".$frm["id_equipo"]."'";
	if(isset($frm["id_motivo"]))
		$cond[] = "o.id_motivo='".$frm["id_motivo"]."'";
	if(isset($frm["id"]))
		$cond[] = "o.id='".$frm["id"]."'";

	//fecha planeada
	if(isset($frm["inicio_fecha_planeada"]) && isset($frm["fin_fecha_planeada"]))
		$cond[] = "to_char(o.fecha_planeada,'YYYY-MM-DD') >= '".$frm["inicio_fecha_planeada"]."' AND to_char(o.fecha_planeada,'YYYY-MM-DD') <= '".$frm["fin_fecha_planeada"]."'";
	if(isset($frm["inicio_hora_planeada"]) && isset($frm["inicio_minuto_planeada"]) && isset($frm["fin_hora_planeada"]) && isset($frm["fin_minuto_planeada"]))
		$cond[] = "to_char(o.fecha_planeada,'HH24:MI:SS') >= '".$frm["inicio_hora_planeada"].":".$frm["inicio_minuto_planeada"].":00' AND to_char(o.fecha_planeada,'HH24:MI:SS') <= '".$frm["fin_hora_planeada"].":".$frm["fin_minuto_planeada"].":59'";
	if(isset($frm["inicio_fecha_ejecucion"]) && isset($frm["fin_fecha_ejecucion"]))
		$cond[] = "to_char(o.fecha_ejecucion_inicio,'YYYY-MM-DD') >= '".$frm["inicio_fecha_ejecucion"]."' AND to_char(o.fecha_ejecucion_fin,'YYYY-MM-DD') <= '".$frm["fin_fecha_ejecucion"]."'";
	if(isset($frm["inicio_hora_ejecucion"]) && isset($frm["inicio_minuto_ejecucion"]) && isset($frm["fin_hora_ejecucion"]) && isset($frm["fin_minuto_ejecucion"]))
		$cond[] = "to_char(o.fecha_ejecucion_inicio,'HH24:MI:SS') >= '".$frm["inicio_hora_ejecucion"].":".$frm["inicio_minuto_ejecucion"].":00' AND to_char(o.fecha_ejecucion_fin,'HH24:MI:SS') <= '".$frm["fin_hora_ejecucion"].":".$frm["fin_minuto_ejecucion"].":59'";
	if(isset($frm["id_responsable"]))
		$cond[] = "o.id_responsable='".$frm["id_responsable"]."'";
	if(isset($frm["id_creador"]))
		$cond[] = "o.id_creador='".$frm["id_creador"]."'";
	if(isset($frm["id_planeador"]))
		$cond[] = "o.id_planeador='".$frm["id_planeador"]."'";
	if(isset($frm["id_estado_orden_trabajo"]))
		$cond[] = "o.id_estado_orden_trabajo='".$frm["id_estado_orden_trabajo"]."'";
	if(isset($frm["inicio_tiempo_ejecucion"]) && isset($frm["fin_tiempo_ejecucion"]))
		$cond[] = "o.tiempo_ejecucion >= '".$frm["inicio_tiempo_ejecucion"]."' AND o.tiempo_ejecucion <= '".$frm["fin_tiempo_ejecucion"]."'";
	if(isset($frm["inicio_km"])&& isset($frm["fin_km"]))
		$cond[] = "o.km >= '".$frm["inicio_km"]."' AND o.km <= '".$frm["fin_km"]."'";
	if(isset($frm["inicio_horometro"]) && isset($frm["fin_horometro"]))
		$cond[] = "o.horometro >= '".$frm["inicio_horometro"]."' AND o.horometro <= '".$frm["fin_horometro"]."'";
}

$abierta = $cerrada = array();
$consulta = "SELECT o.id,c.centro, r.rutina, e.nombre as equipo, to_char(o.fecha_planeada,'YYYY/MM/DD') as fecha_planeada, to_char(o.fecha_planeada,'HH24:MI:SS') as hora_planeada, to_char(o.fecha_ejecucion_inicio,'YYYY/MM/DD HH24:MI:SS') as fecha_ejecucion_inicio,  to_char(o.fecha_ejecucion_fin,'YYYY/MM/DD HH24:MI:SS') as fecha_ejecucion_fin, s.estado, s.cerrado, p.prioridad, resp.nombre||' '||resp.apellido as responsable, cre.nombre||' '||cre.apellido as creador, 	pla.nombre||' '||pla.apellido as planeador, o.observaciones
		 FROM mtto.ordenes_trabajo o
		 LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
		 LEFT JOIN mtto.prioridades p ON p.id=r.id_prioridad
		 LEFT JOIN mtto.equipos e ON e.id=o.id_equipo
		 LEFT JOIN centros c on e.id_centro=c.id
		 LEFT JOIN mtto.estados_ordenes_trabajo s ON s.id=o.id_estado_orden_trabajo
		LEFT JOIN personas resp ON resp.id = o.id_responsable
		LEFT JOIN personas cre ON cre.id = o.id_creador
		LEFT JOIN personas pla ON pla.id = o.id_planeador
		 WHERE activa AND r.id IN (SELECT id_rutina FROM mtto.rutinas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."')) 
		 	AND (".implode(" AND ",$cond).")
		 ORDER BY o.fecha_planeada,o.fecha_ejecucion_inicio";
//echo $consulta;
$qid = $db->sql_query($consulta);
while($ot = $db->sql_fetchrow($qid))
{
	if($ot["cerrado"] == "t") $cerrada[] = $ot;
	else $abierta[] =     $ot;
}

require_once $CFG->common_libdir."/writeexcel/class.writeexcel_workbook.inc.php";
require_once $CFG->common_libdir."/writeexcel/class.writeexcel_worksheet.inc.php";

$fname=$CFG->tmpdir."/ordenes_trabajo.xls";
if(file_exists($fname))
	unlink($fname);

$workbook = new writeexcel_workbook($fname);
$workbook->set_tempdir($CFG->tmpdir);

if(count($abierta) > 0)
{
	$worksheet = &$workbook->addworksheet("abiertas");
	$worksheet->set_zoom(80);
	$titulos = array("CENTRO","OT","FECHA PLANEADA", "HORA PLANEADA", "EQUIPO", "RUTINA", "ESTADO", "PRIORIDAD", "RESPONSABLE", "CREADOR", "PLANEADOR", "OBSERVACIONES");
	$fila = $columna= 0;
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, $titulos);
	$fila++; $columna=0;
	foreach($abierta as $dx)
	{
		$linea = array($dx["centro"],$dx["id"], $dx["fecha_planeada"], $dx["hora_planeada"], $dx["equipo"], $dx["rutina"], $dx["estado"], $dx["prioridad"], $dx["responsable"], $dx["creador"], $dx["planeador"], $dx["observaciones"]);
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_center", 2=>"txt_center", 3=>"txt_izq", 4=>"txt_izq", 5=>"txt_center", 6=>"txt_center", 7=>"txt_izq", 8=>"txt_izq", 9=>"txt_izq",  10=>"txt_izq", 11=>"txt_izq",12=>"txt_izq"));
	}
}

if(count($cerrada) > 0)
{
	$worksheet = &$workbook->addworksheet("cerradas");
	$worksheet->set_zoom(80);
	$titulos = array("CENTRO","OT","FECHA PLANEADA", "HORA PLANEADA", "EQUIPO", "RUTINA", "FECHA EJECUCIÓN INICIO", "FECHA EJECUCIÓN FINAL", "ESTADO", "PRIORIDAD", "RESPONSABLE", "CREADOR", "PLANEADOR", "OBSERVACIONES");
	$fila = $columna= 0;
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, $titulos);
	$fila++; $columna=0;
	foreach($cerrada as $dx)
	{
		$linea = array($dx["centro"],$dx["id"],$dx["fecha_planeada"], $dx["hora_planeada"], $dx["equipo"], $dx["rutina"], $dx["fecha_ejecucion_inicio"], $dx["fecha_ejecucion_fin"], $dx["estado"], $dx["prioridad"], $dx["responsable"], $dx["creador"], $dx["planeador"], $dx["observaciones"]);
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_center", 2=>"txt_center", 3=>"txt_izq", 4=>"txt_izq", 5=>"txt_center", 6=>"txt_center", 7=>"txt_center", 8=>"txt_center", 9=>"txt_izq", 10=>"txt_izq", 11=>"txt_izq",  12=>"txt_izq", 13=>"txt_izq",14=>"txt_izq"));
	}
}

if(count($abierta) == 0 && count($cerrada) == 0)
{
	$worksheet = &$workbook->addworksheet("ot");
}

$workbook->close();
$nombreArchivo="ordenes_trabajo.xls";
header("Content-Type: application/x-msexcel; name=\"".$nombreArchivo."\"");
header("Content-Disposition: inline; filename=\"".$nombreArchivo."\"");
$fh=fopen($fname, "rb");
fpassthru($fh);

?>
