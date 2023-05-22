<?
include_once("../application.php");

if(!isset($_SESSION[$CFG->sesion]["user"])){
  $errorMsg="No existe la sesión.";
  error_log($errorMsg);
  die($errorMsg);
}
$user=$_SESSION[$CFG->sesion]["user"];

$mode=nvl($_GET["mode"],nvl($_POST["mode"],""));

switch(nvl($mode)){

	case "agregar":
	  agregar();
	break;

	case "insertar":
		insertar($_POST);
	break;

	case "editar":
		editar($_GET["id"]);
	break;

	case "actualizar":
		actualizar($_POST);
	break;

	case "eliminar":
		eliminar($_POST);
	break;
}


function agregar()
{
	global $CFG, $db,$ME;

	$condicionCentro=$condicionCentroGr="true";
	$user=$_SESSION[$CFG->sesion]["user"];
	if($user["nivel_acceso"]!=1)
	{	
		$condicionCentro="id IN (" . implode(",",$user["id_centro"]) . ")";
		$condicionCentroGr="(id_centro IS NULL OR id_centro  IN (" . implode(",",$user["id_centro"]) . "))";
	}

	$db->crear_select("SELECT id, centro FROM centros WHERE ".$condicionCentro." ORDER BY centro",$centros);
	$db->build_recursive_tree_path("mtto.grupos",$select_grupos,"","id","id_superior","nombre","-1","",$condicionCentroGr);
	
	$newMode="insertar";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/grupos_form.php");
}


function insertar($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir . "/mtto.grupos.php");
	$entidad->loadValues($frm);
	$id=$entidad->insert();

	echo "<script>window.location.href='".$CFG->wwwroot."/mtto/grupos.php?mode=editar&id=".$id."';</script>";
}


function editar($idGrupo)
{
	global $CFG, $db,$ME;

	$grupo = $db->sql_row("SELECT * FROM mtto.grupos WHERE id=".$idGrupo);
	$condicionCentro=$condicionCentroGr="true";
	$user=$_SESSION[$CFG->sesion]["user"];
	if($user["nivel_acceso"]!=1)
	{	
		$condicionCentro="id IN (" . implode(",",$user["id_centro"]) . ")";
		$condicionCentroGr="(id_centro IS NULL OR id_centro  IN (" . implode(",",$user["id_centro"]) . "))";
	}

	$db->crear_select("SELECT id, centro FROM centros WHERE ".$condicionCentro." ORDER BY centro",$centros,$grupo["id_centro"]);
	$db->build_recursive_tree_path("mtto.grupos",$select_grupos,$grupo["id_superior"],"id","id_superior","nombre","-1","",$condicionCentroGr);
	
	$newMode="actualizar";
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/grupos_form.php");
}

function actualizar($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir . "/mtto.grupos.php");
	$entidad->loadValues($frm);
	$entidad->set("mode","update");
	$entidad->update();

	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
}

function eliminar($frm)
{
	global $CFG, $db,$ME;

	include($CFG->modulesdir . "/mtto.grupos.php");
	$entidad->load($frm["id"]);
	$entidad->set("mode","$frm[mode]");
	if($entidad->hasRelatedEntities()){
		echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.outerHeight=200;\nwindow.outerWidth=300;\n</script>\n";
		echo "No se puede borrar, porque tiene elementos relacionados.<br><br>\n";
		echo "<input type=\"button\" onClick=\"window.close();\" value=\"Cerrar\">";
		die();
	}
	$entidad->set("id",$frm['id']);
	$entidad->delete();
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";


}


include($CFG->dirroot."/templates/footer_popup.php");
?>

