<?
include_once("../application.php");
include($CFG->dirroot."/templates/header_2panel.php");

if(!isset($_SESSION[$CFG->sesion]["user"])){
	$errorMsg="No existe la sesión.";
	error_log($errorMsg);
	die($errorMsg);
}
$user=$_SESSION[$CFG->sesion]["user"];

verificarPagina(simple_me($ME));

$mode=nvl($_GET["mode"],nvl($_POST["mode"],""));
if($mode=="resultados")
	$busq = $_POST;
?>
<div id="right1"><?include($CFG->dirroot."/mtto/templates/opciones.php")?></div>
<div id="center1">
<?
//file_put_contents('/var/www/html/pa/mtto/error.log',$mode ,FILE_APPEND);
switch(nvl($mode)){

	case "listado_semanal":
		listado_semanal(nvl($_GET));
	break;

	case "listado_mensual":
		listado_mensual(nvl($_GET), nvl($_GET["abiertas"],1));
	break;

	case "observaciones";
		observaciones($_POST);
	break;

	case "fecha_planeada";
		fecha_planeada($_POST,true);
	break;

	case "hora_planeada";
		fecha_planeada($_POST,false,true);
	break;

	case "resultados":
		resultados($_POST);
	break;

	case "listar_resultados":
		listar_resultados($_GET);
	break;

	case "listado_diario":
		listado_diario(nvl($_GET));
	break;

	default:
		listado_mensual(nvl($_GET), nvl($_GET["abiertas"],1));
	break;
}



function listado_diario($frm)
{
	global $db,$CFG,$ME;

	$fecha = nvl($frm["fecha"],date("Y-m-d"));
	$user=$_SESSION[$CFG->sesion]["user"];
	$condicion=" activa AND NOT s.cerrado AND r.id IN (SELECT id_rutina FROM mtto.rutinas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."'))";

	$datos = array();
  $cons = "SELECT o.id, r.rutina, e.nombre as equipo, to_char(o.fecha_planeada,'YYYY/MM/DD') as fecha_planeada,to_char(o.fecha_planeada,'HH24:MI:SS') as hora_planeada , s.estado,observaciones, '<a href='|| chr(39)||'javascript:edicion('||o.id||')'|| chr(39)||'><img alt='|| chr(39)||'Editar'|| chr(39)||' src='||chr(39)||'".$CFG->wwwroot."/admin/iconos/transparente/iconoeditar.gif'||chr(39)||' border=0></a>' as editar, '<img alt='|| chr(39)||'Prioridad'||chr(39)||' src='||chr(39)||'".$CFG->wwwroot."/files/mtto.prioridades/imagen/'||p.id||''||chr(39)||' border=0>' as prioridad, case when o.id_estado_orden_trabajo IN (SELECT id FROM mtto.estados_ordenes_trabajo WHERE cerrado) then '<img alt='|| chr(39)||'A tiempo'|| chr(39)||' src='||chr(39)||'".$CFG->wwwroot."/admin/iconos/transparente/check_green.png'|| chr(39)||' border=0>'   when o.fecha_planeada < now() then '<img alt='||chr(39)||'A tiempo'|| chr(39)||' src='||chr(39)||'".$CFG->wwwroot."/admin/iconos/transparente/uncheck.png'|| chr(39)||' border=0>' else '<img alt='|| chr(39)||'A tiempo'|| chr(39)||' src='||chr(39)||'".$CFG->wwwroot."/admin/iconos/transparente/check_green.png'||chr(39)||' border=0>' end as atiempo
		 FROM mtto.ordenes_trabajo o
		 LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
		 LEFT JOIN mtto.equipos e ON e.id=o.id_equipo
		 LEFT JOIN mtto.estados_ordenes_trabajo s ON s.id=o.id_estado_orden_trabajo
		 LEFT JOIN mtto.prioridades p ON p.id=r.id_prioridad
		 WHERE ".$condicion." AND o.fecha_planeada::date = '".$fecha."' 
		 ORDER BY o.fecha_planeada, o.fecha_ejecucion_inicio";
	$qid = $db->sql_query($cons);
	while($query = $db->sql_fetchrow($qid))
	{
		$datos[] = '{atiempo:"'.$query["atiempo"].'", hora_planeada:"'.$query["hora_planeada"].'", fecha_planeada:"'.$query["fecha_planeada"].'",id:"'.$query["id"].'", rutina:"'.$query["rutina"].'", equipo:"'.$query["equipo"].'", estado:"'.$query["estado"].'", observaciones:"'.str_replace("\n\t","",$query["observaciones"]).'", prioridad:"'.$query["prioridad"].'", editar:"'.$query["editar"].'"}';
	}
	list($anio,$mes,$dia)=split("-",$fecha);
	$ant = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) - 1 * 24 * 60 * 60)."&mode=listado_diario";
	$sig = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + 1 * 24 * 60 * 60)."&mode=listado_diario";

	$tipoListado="diario";
	$titulo = "PROGRAMACIÓN DEL DÍA ".strtoupper(strftime("%A %d de %B / %Y",strtotime($fecha)));
	include("templates/listado_calendario.php");
}

function observaciones($frm)
{
	global $db,$CFG,$ME;

	$db->sql_query("UPDATE mtto.ordenes_trabajo SET observaciones='".$frm["newValue"]."' WHERE id=".$frm["id"]);
	//$resp = '{"replyCode":201, "replyText":"Data follows","data:[{"motivo":"'.$frm["newValue"].'"}]}';
	return "ok";
}

function fecha_planeada($frm,$fecha=false,$hora=false)
{
	global $db,$CFG,$ME;
	
	$ot = $db->sql_row("SELECT to_char(fecha_planeada,'YYYY-MM-DD') as fecha_planeada, to_char(fecha_planeada,' HH24:MI:SS') as hora_planeada
		 FROM mtto.ordenes_trabajo
		 WHERE id=".$frm["id"]);
	if($fecha)
	{
		$nuevo=date("Y-m-d",strtotime(preg_replace("/ GMT.*$/","",$frm["newValue"])))." ".$ot["hora_planeada"];
		$db->sql_query("UPDATE mtto.ordenes_trabajo SET fecha_planeada='".$nuevo." ' WHERE id=".$frm["id"]);
		insertarFechaProgramadaOT($frm["id"],$_SESSION[$CFG->sesion]["user"]["id"],$nuevo,true);
	}else
	{
		$nuevo=$ot["fecha_planeada"]." ".$frm["newValue"];
		$db->sql_query("UPDATE mtto.ordenes_trabajo SET fecha_planeada='".$nuevo." ' WHERE id=".$frm["id"]);
		insertarFechaProgramadaOT($frm["id"],$_SESSION[$CFG->sesion]["user"]["id"],$nuevo);
	}
}


function listado_semanal($frm)
{
	global $db,$CFG,$ME;

	$fecha = nvl($frm["fecha"],date("Y-m-d"));
	$semana = obtenerSemana($fecha);
	$user=$_SESSION[$CFG->sesion]["user"];
	$condicion=" activa AND NOT s.cerrado AND r.id IN (SELECT id_rutina FROM mtto.rutinas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."'))";

	$datos = $horas = array();
	$consulta = "SELECT o.id, r.rutina, e.nombre as equipo,to_char(o.fecha_planeada,'HH24') as horas, to_char(o.fecha_planeada,'DD') as dia, to_char(o.fecha_planeada,'HH24:MI:SS') as hora_planeada, '<img alt='||chr(39)||'Prioridad'|| chr(39)||' src='||chr(39)||'".$CFG->wwwroot."/files/mtto.prioridades/imagen/'||p.id||''||chr(39)||'border=0>' as prioridad, o.fecha_planeada as fecha_planeada_completa, id_estado_orden_trabajo as id_estado
	 	 FROM mtto.ordenes_trabajo o
		 LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
		 LEFT JOIN mtto.prioridades p ON p.id=r.id_prioridad
		 LEFT JOIN mtto.estados_ordenes_trabajo s ON s.id=o.id_estado_orden_trabajo
		 LEFT JOIN mtto.equipos e ON e.id=o.id_equipo
		 WHERE ".$condicion." AND o.fecha_planeada >= '".$semana["Monday"]." 00:00:00' AND o.fecha_planeada <= '".$semana["Sunday"]." 23:59:59'
		 ORDER BY o.fecha_planeada,o.fecha_ejecucion_inicio";
	$qid = $db->sql_query($consulta);
	while($query = $db->sql_fetchrow($qid))
	{
		$datos[$query["horas"]][$query["dia"]][] = array("line"=>$query["prioridad"]." <a href=\"javascript:edicion(".$query["id"].")\">".$query["hora_planeada"]." : ".$query["equipo"]." (".$query["rutina"].")</a>","date"=>$query["fecha_planeada_completa"],"id_estado"=>$query["id_estado"], "idOT"=>$query["id"]);
		$horas[$query["horas"]] = $query["horas"];
	}
//	preguntar($datos);
//	preguntar($horas);
	asort($horas);
	list($anio,$mes,$dia)=split("-",$semana["Monday"]);
	$ant = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) - 1 * 24 * 60 * 60)."&mode=listado_semanal";
	list($anio,$mes,$dia)=split("-",$semana["Sunday"]);
	$sig = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + 1 * 24 * 60 * 60)."&mode=listado_semanal";

	$tipoListado="semanal";
	$titulo = "PROGRAMACIÓN SEMANAL: ".strtoupper(strftime("%d de %B/%Y",strtotime($semana["Monday"])))." A ".strtoupper(strftime("%d de %B/%Y",strtotime($semana["Sunday"])));
	include("templates/listado_calendario.php");
}


function listado_mensual($frm, $abiertas=1)
{
	global $db,$CFG,$ME;
	
	$fecha = nvl($frm["fecha"],date("Y-m-d"));
	list($anio,$mes,$dia)=split("-",$fecha);
	$primerDia = $anio."-".$mes."-01";
	$ultimoDia = $anio."-".$mes."-".ultimoDia($mes,$anio);
	$titulo = "PROGRAMACIÓN MENSUAL: ".strtoupper(strftime("%B/%Y",strtotime($primerDia)));

	$user=$_SESSION[$CFG->sesion]["user"];

	if($abiertas)
		$condicion=" activa AND NOT s.cerrado AND r.id IN (SELECT id_rutina FROM mtto.rutinas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."'))";
	else
	{
		$condicion=" activa AND s.cerrado AND r.id IN (SELECT id_rutina FROM mtto.rutinas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."'))";
		$sinBotones=true;
		$titulo = "EJECUTADAS ".strtoupper(strftime("%B/%Y",strtotime($primerDia)));
	}

	$datos = array();
	$consulta = "SELECT o.id, r.rutina, e.nombre as equipo, to_char(o.fecha_planeada,'YYYY/MM/DD') as fecha_planeada, o.fecha_planeada as fecha_planeada_completa, to_char(o.fecha_planeada,'HH24:MI:SS') as hora_planeada, to_char(o.fecha_ejecucion_inicio,'YYYY/MM/DD HH24:MI:SS') as fecha_ejecucion,s.estado, o.tiempo_ejecucion,'<img src='||chr(39)||'".$CFG->wwwroot."/files/mtto.estados_ordenes_trabajo/imagen/'||o.id_estado_orden_trabajo||''||chr(39)||' border=0>' as impresa,'<img src='||chr(39)||'".$CFG->wwwroot."/files/mtto.prioridades/imagen/'||p.id||''||chr(39)||' border=0>' as prioridad, id_estado_orden_trabajo as id_estado, e.id as id_equipo 
    FROM mtto.ordenes_trabajo o LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina 
     LEFT JOIN mtto.prioridades p ON p.id=r.id_prioridad
     LEFT JOIN mtto.equipos e ON e.id=o.id_equipo
     LEFT JOIN mtto.estados_ordenes_trabajo s ON s.id=o.id_estado_orden_trabajo
     WHERE ".$condicion." AND o.fecha_planeada >= '".$primerDia." 00:00:00' AND o.fecha_planeada <= '".$ultimoDia." 23:59:59'
     ORDER BY o.fecha_planeada,o.fecha_ejecucion_inicio";
  //echo $consulta;
	$qid = $db->sql_query($consulta);
	while($query = $db->sql_fetchrow($qid))
	{
		//novedades
		$novedades="";
		$qidNov = $db->sql_row("SELECT count(id) as num FROM novedades WHERE id_equipo=".$query["id_equipo"]." AND hora_fin IS NULL");
		if($qidNov["num"] != 0)
			$novedades = "<br /><a href=\"javascript:verNovedadesAbiertas('".$query["id_equipo"]."')\" style='font:bold 10px Verdana, Arial, Helvetica, sans-serif; font-style: italic;'>NA: ".$qidNov["num"]."</a>";
	
		$dia = trim(strftime("%e",strtotime($query["fecha_planeada_completa"])));
		$datos[$dia][] = array("line"=>"<table width='100%'><tr><td width='50%' align='left'>".$query["prioridad"]."&nbsp;&nbsp;".$query["impresa"]."</td><td style='vertical-align:text-top; text-align:right;'>".$novedades."</td></tr><tr><td colspan=2><a href=\"javascript:edicion(".$query["id"].")\">".$query["hora_planeada"]." : ".$query["equipo"]." (".$query["rutina"].")</a></td></tr></table>","date"=>$query["fecha_planeada_completa"],"id_estado"=>$query["id_estado"], "idOT"=>$query["id"]); 
	}
	list($anio,$mes,$dia)=split("-",$primerDia);
	$ant = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) - 1 * 24 * 60 * 60)."&mode=listado_mensual&abiertas=".$abiertas;
	list($anio,$mes,$dia)=split("-",$ultimoDia);
	$sig = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + 1 * 24 * 60 * 60)."&mode=listado_mensual&abiertas=".$abiertas;
	//Variable para llevar la cuenta del dia actual
	$dia_actual = 1;
	//calculo el numero del dia de la semana del primer dia
	$numero_dia = calcula_numero_dia_semana(1,$mes,$anio);
	$tipoListado="mensual";
	include("templates/listado_calendario.php");
}

function resultados($frm){
	GLOBAL $CFG, $ME, $entidad;

	$queryArray=array();
	foreach($frm AS $key=>$val){
		if($key!="mode" && $val!="" && $val!="%") 
			array_push($queryArray,$key . "=" . $val);
	} 
	$queryString=$ME . "?mode=listar_resultados&";
	$queryString.=implode("&",$queryArray);

	echo "<script>\n";
	echo "var url='" . $queryString . "';\n";
	echo "window.opener.location.href=url;\nwindow.close();\n</script>\n";
	echo "</script>\n";
}



function listar_resultados($frm)
{
	global $CFG, $db,$ME;

	////$cond = array("r.activa");
	//$cond = array();
	$user=$_SESSION[$CFG->sesion]["user"];
	$cond[]="r.id IN (SELECT id_rutina FROM mtto.rutinas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."')) ";
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

	$datos=array();

  $consulta = "SELECT o.id, r.rutina, e.nombre as equipo, to_char(o.fecha_planeada,'YYYY/MM/DD') as fecha_planeada, to_char(o.fecha_planeada,'HH24:MI:SS') as hora_planeada , s.estado,o.observaciones,'<a href='|| chr(39)||'javascript:edicion('||o.id||')'||chr(39)||'><img alt='|| chr(39)||'Editar'|| chr(39)||' src='||chr(39)||'".$CFG->wwwroot."/admin/iconos/transparente/iconoeditar.gif'||chr(39)||' border=0></a>' as editar, '<img alt='|| chr(39)||'Prioridad'||chr(39)||' src='||chr(39)||'".$CFG->wwwroot."/files/mtto.prioridades/imagen/'||p.id||''||chr(39)||' border=0>' as prioridad, case when o.id_estado_orden_trabajo IN (SELECT id FROM mtto.estados_ordenes_trabajo WHERE cerrado) then '<img alt='|| chr(39)||'A tiempo'|| chr(39)||' src='||chr(39)||'".$CFG->wwwroot."/admin/iconos/transparente/check_green.png'||chr(39)||' border=0>' when o.fecha_planeada < now() then '<img alt='|| chr(39)||'A tiempo'||chr(39)||' src='||chr(39)||'".$CFG->wwwroot."/admin/iconos/transparente/uncheck.png'|| chr(39)||' border=0>' else '<img alt='|| chr(39)||'A tiempo'|| chr(39)||' src='|| chr(39)||'".$CFG->wwwroot."/admin/iconos/transparente/check_green.png'||chr(39)||' border=0>>' end as atiempo
     FROM mtto.ordenes_trabajo o
     LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
     LEFT JOIN mtto.equipos e ON e.id=o.id_equipo
     LEFT JOIN mtto.estados_ordenes_trabajo s ON s.id=o.id_estado_orden_trabajo
     LEFT JOIN mtto.prioridades p ON p.id=r.id_prioridad
     WHERE ".implode(" AND ",$cond)." 
     ORDER BY o.fecha_planeada";
	//echo $consulta;
	$qid = $db->sql_query($consulta);
	while($query = $db->sql_fetchrow($qid))
	{
		$observaciones = str_replace('"',"",$query["observaciones"]);
		$observaciones = str_replace("'","",$observaciones);
		$observaciones = str_replace("\r\n","<br>",$observaciones);

		$datos[] = '{atiempo:"'.$query["atiempo"].'", hora_planeada:"'.$query["hora_planeada"].'", fecha_planeada:"'.$query["fecha_planeada"].'",id:"'.$query["id"].'", rutina:"'.$query["rutina"].'", equipo:"'.$query["equipo"].'", estado:"'.$query["estado"].'", observaciones:"'.$observaciones.'", prioridad:"'.$query["prioridad"].'", editar:"'.$query["editar"].'"}';
	}

	$queryArray=array();
	foreach($frm AS $key=>$val){
		if($key!="mode" && $val!="" && $val!="%") 
			array_push($queryArray,$key . "=" . $val);
	} 
	$link_bajar_OT_resultados=$CFG->wwwroot . "/mtto/bajar_excel_OT.php?mode=bajar_resultados&";
	$link_bajar_OT_resultados.=implode("&",$queryArray);
	

	$ant = $sig = ""; 
	$tipoListado="diarioResultados";
	$titulo = "RESULTADOS ENCONTRADOS : ".$db->sql_numrows($qid);
	include("templates/listado_calendario.php");
}


?></div>
<?include($CFG->dirroot."/templates/footer_2panel.php");?>
