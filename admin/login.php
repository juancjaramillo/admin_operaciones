<?
/* login.php (c) 2021 Juan Jaramillo  */

/******************************************************************************
 * MAIN
 *****************************************************************************/

include("../application.php");
$DOC_TITLE = "Login";

/* form has been submitted, check if it the user login information is correct */
if (isset($_POST["username"])) {
	$user = verify_login($_POST["username"], $_POST["password"]);

	if ($user) {
		$_SESSION[$CFG->sesion]["user"] = $user;
		$_SESSION[$CFG->sesion]["ip"] = $_SERVER["REMOTE_ADDR"];
		$direcip = $_SERVER["REMOTE_ADDR"];
		$_SESSION[$CFG->sesion]["nivel"] = $user["nivel"];
		$_SESSION[$CFG->sesion]["path"] = NULL;
		$goto=$CFG->wwwroot . "/" . $CFG->mainPage;

		$qUpdate=$db->sql_query("UPDATE personas SET fecha='" . date("Y-m-d H:i:s") . "' WHERE id='" . $user["id"] . "'");
		$qUpdate=$db->sql_query("UPDATE personas SET direccion='".$direcip."' WHERE id='" . $user["id"] . "'");
		if(isset($_SESSION[$CFG->sesion]["goto"])) unset($_SESSION[$CFG->sesion]["goto"]);
		echo "<script>\nwindow.location.href='" . $goto . "';\n</script>\n";
	

		die;
	} else {
		$errormsg = "Login inv�lido, por favor intente de nuevo.";
		$frm["username"] = $_POST["username"];
	}
}


if(!isset($_POST["goto"])) $frm["goto"]=nvl($_SESSION[$CFG->sesion]["goto"],"index.php?module=".$CFG->defaultModule);
else $frm["goto"]=$_POST["goto"];
if(isset($_SESSION[$CFG->sesion])) unset($_SESSION[$CFG->sesion]);
include("templates/login_form.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function verify_login($username, $password) {
	GLOBAL $db;

	$pass = md5($password);
	$qid = $db->sql_query("SELECT *, case when nivel_acceso=1 then 'admin' when nivel_acceso=2 then 'control' when nivel_acceso=3 then 'visor_avl' when nivel_acceso=4 then 'visor_avl_global' end as nivel  FROM personas WHERE login = '" . $db->sql_escape($username) . "' AND password = '" . $pass . "'");
	if($user=$db->sql_fetchrow($qid)){
		$idCentros=array();
		$qidCentros = $db->sql_query("SELECT id_centro FROM personas_centros WHERE id_persona=".$user["id"]);
		while($queryCentros = $db->sql_fetchrow($qidCentros))
		{
			$idCentros[] = $queryCentros["id_centro"];
		}
		$user["id_centro"] = $idCentros;
		return $user;
	}

	

	return(FALSE);
}

?>
