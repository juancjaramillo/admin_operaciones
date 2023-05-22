<?
/* login.php (c)2021 Juan Jaramillo  */

/******************************************************************************
 * MAIN
 *****************************************************************************/
 error_reporting(E_ALL);
 ini_set("display_errors", 1);
 echo "<pre>";
 print_r($_POST);
echo "</pre>"; 

include("../application.php");
$DOC_TITLE = "Login";

/* form has been submitted, check if it the user login information is correct */
if (isset($_POST["username"])) {
	$user = verify_login($_POST["username"], $_POST["email"]);

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
	$id=$user["id"];
	 $nombre=$user["nombre"];
	 $apellido =$user["apellido"];
	$email =$user["email"];
	
	$email_html = "
	<font size='3' face='Verdana'>Señor(a): $nombre $apellido 
	  <br><br>
	Reciba un cordial saludo. 
	<br><br> 
	Le informamos que se le ha asignado el caso No..

	
			
			puede ver el requerimiento en la siguiente direccion:<a href='http://xxxx.xxxx.xxxx.xxxx/aidagestiondocumental/index.php?module=Cases&action=DetailView&record=$id'> Link </a>  
	<br><br> 

	
	 <br><br> 
	Cordialmente,
	  <br><br>
	 
	Unidad de Gestión Documental – Promo</font>
	<br>

	<table><tr><td><img src='http://xxxxx.co/images/company_logo.png' width='550px'/></td></tr></table>

	<br><br>
	<font size='2' face='Verdana'>Este mensaje ha sido enviado por un sistema automático, por lo cual le agradecemos no responder ya que el buzón de correo no será revisado por ninguna persona.

	<br><br>Ante cualquier inquietud por favor comuníquese con nosotros a través del portal http://xxx.xxx.xxx.xxxx/aidagestiondocumental/ o al Contact Center línea Local (57+1) 484-1460  |  PBX: (57+1) 484-1410  |  Línea Nacional Gratuita 018000-519535
	de lunes a viernes de 8:00 a.m. a 6:00 p.m.
	<br>
	<br></font>
		
	"; 
	$subject="Recovery you PWD";
	$to=$email;
	$name="JCJC";
	$message=$email_html;
	$email="xxx@xxx.com";
	$headers = "From: $name <$email>\r\n";
  $headers .= "Content-type: text/html\r\n";
echo $to."  ".$subject."  ".$message."  ".$headers;

  mail($to, $subject, $message, $headers);
	
	
	
	
    }
             
if(!isset($_POST["goto"])) $frm["goto"]=nvl($_SESSION[$CFG->sesion]["goto"],"index.php?module=".$CFG->defaultModule);
else $frm["goto"]=$_POST["goto"];
if(isset($_SESSION[$CFG->sesion])) unset($_SESSION[$CFG->sesion]);
include("templates/login_form.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function verify_login($username, $email) {
	GLOBAL $db;
	$qid = $db->sql_query("SELECT id, email,nombre, apellido WHERE login = '" . $db->sql_escape($username) . "' AND email = '" . $email . "' and id_estado=1");
	
	echo "SELECT id, email,nombre, apellido WHERE login = '" . $db->sql_escape($username) . "' AND email = '" . $email . "' and id_estado=1";
	//die();
	if($user=$db->sql_fetchrow($qid)){
		return $user;
	}
	return(FALSE);
}

?>
