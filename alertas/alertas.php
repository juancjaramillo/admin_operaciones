<?
include("../application.php");
if(!isset($_SESSION[$CFG->sesion]["user"])){
	error_log("No existe la sesión.");
	die();
}

$user=$_SESSION[$CFG->sesion]["user"];

/**/
if(!is_array($user["id_centro"]))
{
	echo "<script>
	  window.location.href='".$CFG->wwwroot."/admin/login.php';
		</script>";
	die;
}
if(sizeof($_POST)>sizeof($_GET)) $frm=$_POST;
else $frm=$_GET;

switch(nvl($frm["mode"])){
	case "give_ack":
		give_ack($frm);
		break;
	case "ack":
		ack($frm);
		break;
	break;
	default:
		print_feeds($frm);
		break;
}


/*	FUNCIONES	*/
function give_ack($frm){
	GLOBAL $CFG, $db, $user, $ME;

//	preguntar($frm);
	$qUpdate=$db->sql_query("
		UPDATE alertas SET
			ack_id_motivo='$frm[id_motivo]',
			ack_id_persona='$user[id]',
			ack_hora='" . date("Y-m-d H:i:s") . "'
		WHERE id='$frm[id_alerta]'
	");
	echo '
	<script>
		if(window.opener && window.opener.location && window.opener.location)
			window.opener.location.reload();
		if(window.opener && window.opener.focus) window.opener.focus();
		window.close();
	</script>
	';
}

function ack($frm){
	GLOBAL $CFG, $db, $user, $ME;

	$alerta=$db->sql_row("
		SELECT al.*, t.nombre as tipo, m.codigo as ruta, c.centro, v.codigo as vehiculo
		FROM alertas al LEFT JOIN tipos_alertas t ON al.id_tipo=t.id
			LEFT JOIN micros m ON al.id_micro=m.id
			LEFT JOIN centros c ON al.id_centro=c.id
			LEFT JOIN vehiculos v ON al.id_vehiculo=v.id
		WHERE al.id='$frm[id_alerta]'
	");
//	preguntar($alerta);
	$optionsMotivos=$db->sql_listbox("SELECT id,nombre FROM motivos_ack WHERE id_tipo='$alerta[id_tipo]' ORDER BY nombre","Seleccione el motivo...");
	include("templates/alerta_frm.php");
//	preguntar($frm);
}

function print_feeds($frm){
	GLOBAL $CFG, $db, $user, $ME;

	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	//Cierres automaticos de las alertas cada hora
	 $qAlertas=$db->sql_query("
		UPDATE alertas SET
			ack_id_motivo='1',
			ack_id_persona='0',
			ack_hora='" . date("Y-m-d H:i:s") . "'
		WHERE  id_tipo<6 AND hora < '" . date("Y-m-d H:i:s",strtotime("-1 hour")) ."' and ack_id_motivo is null
	");

	$qAlertas=$db->sql_query("
		UPDATE alertas SET
			ack_id_motivo='13',
			ack_id_persona='0',
			ack_hora='" . date("Y-m-d H:i:s") . "'
		WHERE  id_tipo>=6 AND hora < '" . date("Y-m-d H:i:s",strtotime("-30 Minutes")) ."' and ack_id_motivo is null
	");
	
	$condicion="al.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') ";
	$qAlertas=$db->sql_query("
		SELECT al.*, t.nombre as tipo, m.codigo as ruta, c.centro, v.codigo as vehiculo
		FROM alertas al LEFT JOIN tipos_alertas t ON al.id_tipo=t.id
			LEFT JOIN micros m ON al.id_micro=m.id
			LEFT JOIN centros c ON al.id_centro=c.id
			LEFT JOIN vehiculos v ON al.id_vehiculo=v.id
		WHERE hora >= '" . date("Y-m-d H:i:s",strtotime("-4 hour")) . "'
			AND $condicion AND al.ack_hora IS NULL
		ORDER BY al.hora
	");
	echo "<ul>\n";
	while($alerta=$db->sql_fetchrow($qAlertas)){
		echo "<li>";
		echo "<span style=\"cursor:pointer\" onClick=\"acknowledge(" . $alerta["id"] . ")\">\n";
		echo "<b>[AIDA]:&nbsp".$alerta["centro"]."&nbsp;</b>";	
		echo "" . date("H:i",strtotime($alerta["hora"])) . "&nbsp;";
		echo "<b>" . $alerta["vehiculo"] . "</b>&nbsp;";
		echo "" . $alerta["tipo"] . "&nbsp;";
		if($alerta["ruta"]!="") echo "(" . $alerta["ruta"] . ")";
		echo "<b>" . $alerta["observaciones"] . "</b>&nbsp;";
		if ($alerta["id_tipo"]==4 and ($alerta["id_centro"]==1 or $alerta["id_centro"]==2 or $alerta["id_centro"]==3))echo '<video controls="controls" autoplay="autoplay" loop> <source src="alerta1_libtheora.ogv" type="video/ogg" />	</video>';
		echo "</span>\n";
		echo "</li>\n";
	}
	echo "</ul>\n";
}
?>
