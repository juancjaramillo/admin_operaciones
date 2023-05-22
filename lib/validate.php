<?
	function is_admin() {

		global $CFG;

		if(!isset($_SESSION[$CFG->sesion])) error_log("CFG->sesion");
		if(!isset($_SESSION[$CFG->sesion]["user"])) error_log("CFG->sesion->user");
		if(!isset($_SESSION[$CFG->sesion]["ip"])) error_log("CFG->sesion->ip");
		if(isset($_SESSION[$CFG->sesion]["ip"]) && ($_SESSION[$CFG->sesion]["ip"]!=$_SERVER["REMOTE_ADDR"])) error_log($_SERVER["REMOTE_ADDR"]);

		return isset($_SESSION[$CFG->sesion])
			&& isset($_SESSION[$CFG->sesion]["user"])
			&& isset($_SESSION[$CFG->sesion]["ip"]);
	}

	function is_logged() {

		global $CFG;

		return isset($_SESSION["UE" . $CFG->sesion])
			&& isset($_SESSION["UE" . $CFG->sesion]["user"])
			&& isset($_SESSION["UE" . $CFG->sesion]["ip"])
			&& $_SESSION["UE" . $CFG->sesion]["ip"] == $_SERVER["REMOTE_ADDR"]
			&& nvl($_SESSION["UE" . $CFG->sesion]["nivel"]) == "usuario_registrado";
	}

	function verify_login_UR($email, $password) {
		GLOBAL $db;

		$pass = md5($password);
	//	$qid = db_query("INSERT INTO users VALUES('$username','$pass')");
	//Verificar si es usuario de sucursal:
		$qid = $db->sql_query("SELECT * FROM eventos.invitados_empresariales WHERE email = '$email' AND password = '" . $pass . "'");
		if($user=$db->sql_fetchrow($qid))	return $user;
		return(FALSE);
	}

?>
