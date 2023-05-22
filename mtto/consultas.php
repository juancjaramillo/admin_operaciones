<?
include("../application.php");
if(sizeof($_POST)>sizeof($_GET)) $frm=$_POST;
else $frm=$_GET;
if(!isset($frm["id"])) die("No viene el # del informe.");
if(!isset($frm["format"])) die("No viene el formato.");
if(!file_exists("consultas/" . $frm["id"] . ".php")) die("No existe el informe # " . $frm["id"]);
if(!file_exists("formatos/" . $frm["format"] . ".php")) die("No existe el formato " . $frm["format"]);
$user=$_SESSION[$CFG->sesion]["user"];
include("consultas/" . $frm["id"] . ".php");
$qid=$db->sql_query($strQuery);
include("formatos/" . $frm["format"] . ".php");


?>
