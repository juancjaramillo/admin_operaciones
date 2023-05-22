<?
	include("../application.php");
	include($CFG->dirroot."/templates/header_popup.php");
	if(sizeof($_POST)>sizeof($_GET)) $frm=$_POST;
	else $frm=$_GET;
	
	$module=nvl($frm["module"],$CFG->defaultModule);
	$CFG->module=$module;

	$nivel_acceso=$_SESSION[$CFG->sesion]["user"]["nivel_acceso"];
	if(!in_array($nivel_acceso,$CFG->permisos["modulo_".$module]))
		die("No tiene permisos.");

	if(file_exists($CFG->modulesdir . "/" . $module . ".php"))
		include($CFG->modulesdir . "/" . $module . ".php");
	elseif(file_exists($CFG->modulesdir . "/" . $module . ".phtml"))
		include($CFG->modulesdir . "/" . $module . ".phtml");
	else{
		die("[" . $module . "]:<br>Módulo no implementado.");
	}
	

	switch(nvl($frm["mode"])){

		case "agregar" :
			agregar($_GET);
			break;

		case "insert" :
			insert($_POST);
			break;

		case "editar" :
			editar($_GET);
			break;

		case "actualizar":
			update_list($_GET);
			break;	

		case "consultar" :
			consultar($_GET);
			break;

		case "update" :
			update($_POST);
			break;

		case "eliminar" :
			eliminar($_GET);
			break;

		case "buscar" :
			print_busqueda_form($_GET);
			break;

		case "find":
			find($_GET);
			break;

		default:
			encontrar($_GET);
			break;
	}


/*	********************************************	*/
/*	                 FUNCIONES:                 	*/
/*	********************************************	*/

function encontrar($frm){

	GLOBAL $CFG, $ME, $entidad, $db;

	$entidad->loadValues($frm);
	$entidad->find();
	if(isset($frm['popup']) || isset($frm['iframe'])){
		include($CFG->dirroot."/mtto/templates/listado_modulos_simple.php");
	}
	else{
		$entidad->findPath();
		include($CFG->dirroot."/mtto/templates/listado_modulos.php");
	}
}



function find($frm){
GLOBAL $CFG, $ME, $entidad;

	$schemas = array("bar","esp","gp","llta","mtto","rec");

	$queryArray=array();
	foreach($frm AS $key=>$val){
		if(is_array($val))
			$val=implode(",",$val);
		else
			$val = trim(strip_tags($val));

		foreach($schemas as $sh)
		{
			if(preg_match("/^".$sh."_/",$key,$match))
				$key = str_replace($sh."_",$sh.".",$key);
		}

		if($key!="mode" && $key!="mode_ant" && $val!="" && $val!="%"){
			array_push($queryArray,$key . "=" . $val);
		}
	}
	$queryString=$ME . "?";
	$queryString.=implode("&",$queryArray);


	echo "<script>\n";
	echo "var url='" . $queryString . "';\n";
	echo "var openerUrl=window.opener.location.href;\n";
	echo "if(openerUrl.indexOf('iframe')!=-1) url=url + '&iframe';\n";
	echo "window.opener.location.href=url;\nwindow.close();\n</script>\n";
	echo "</script>\n";

}


function agregar($frm){
	GLOBAL $CFG, $db, $ME, $entidad;
	
	if($frm["module"] == "rec.pesos")
	{
		include($CFG->dirroot."/mtto/templates/pesos_form.php");
	}else
	{
		$entidad->set("mode","$frm[mode]");
		$entidad->set("newMode","insert");
		$entidad->loadValues($frm);
		$string_entidad=$entidad->getForm($frm);
		$javascript_entidad=$entidad->getJavaScript();
		$iframe=$entidad->getLinkIframe();
		include($CFG->dirroot."/mtto/templates/modulos_form.php");
	}
}


function insert($frm){
GLOBAL $CFG, $ME, $entidad,$db;

	$entidad->loadValues($frm);
	$entidad->set("mode","$frm[mode]");
	$frm["id"]=$entidad->insert();

	if($frm["module"] == "rec.pesos")
	{
		insertar_movimientos($frm);
		die;
	}

	$iframe=$entidad->getLinkIframe();
	if($iframe==0){
		$url=parse_url($_SERVER["HTTP_REFERER"]);
		$queryString=$url["query"];
		$queryArray=explode("&",$queryString);
		$frmReferer=array();
		foreach($queryArray AS $val){
			if(preg_match("/^([^=]*)=(.*)$/",$val,$matches)){
				if($matches[1]=="foreignLabelFields"){
					if(base64_decode($matches[2])) $matches[2]=base64_decode($matches[2]);
				}
				$frmReferer[$matches[1]]=$matches[2];
			}
		}
		echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
		if(isset($frmReferer["inputName"]) && $frmReferer["inputName"]!=""){
			$strQuery="
				SELECT " . $frmReferer["foreignLabelFields"] . " AS nombre
				FROM " . $frmReferer["foreignTable"] . "
				WHERE " . $frmReferer["foreignField"] . "='" . $frm["id"] . "'
			";
			$qid=$db->sql_query($strQuery);
			if($result=$db->sql_fetchrow($qid)){
				$comboId=$frm["id"];
				$comboNombre=$result[0];
				echo "
					if(window.opener.document.entryform!=undefined && window.opener.document.entryform." . $frmReferer["inputName"] . "!=undefined && window.opener.document.entryform." . $frmReferer["inputName"] . ".selectedIndex!=undefined){
						elementos=window.opener.document.entryform." . $frmReferer["inputName"] . ".length;
						var nuevaOpcion=new Option('" . $comboNombre . "','" . $comboId . "');
						window.opener.document.entryform." . $frmReferer["inputName"] . "[elementos]=nuevaOpcion;
						window.opener.document.entryform." . $frmReferer["inputName"] . ".selectedIndex=elementos;
						window.close();
					}
					else{
						window.opener.location.reload();
						window.close();
					}
				";
			}
			else{
				echo "window.opener.location.reload();\nwindow.close();\n";
			}
		}
		else{
			echo "window.opener.location.reload();\nwindow.close();\n";
		}
		echo "</script>";
	}
	else{
		$frm['mode']="editar";
		editar($frm);
	}
}

function insertar_movimientos($frm)
{
	GLOBAL $CFG, $ME, $entidad, $db;

	if($frm["reparte"] == "f" && $frm["id_unico_movimiento"] != "%")
	{
		$val = explode("_", $frm["id_unico_movimiento"]);
		$db->sql_query("INSERT INTO rec.movimientos_pesos (id_peso,id_movimiento, porcentaje,viaje) values ('".$frm["id"]."', '".$val[0]."', '100', '".$val[1]."')");
	}elseif($frm["reparte"] == "t")
	{
		if(isset($frm["id_movimientos"]))
		{
			$div = 100/count($frm["id_movimientos"]);
			foreach($frm["id_movimientos"] as $idmov)
			{
				$val = explode("_", $idmov);
				$db->sql_query("INSERT INTO rec.movimientos_pesos (id_peso,id_movimiento, porcentaje,viaje) values ('".$frm["id"]."', '".$val[0]."', '".$div."', '".$val[1]."')");
			}
		}
	}

	$frm['mode']="editar";
	editar($frm);
}


function editar($frm){
GLOBAL $CFG, $ME, $entidad;

	$entidad->load($frm["id"]);
	$entidad->set("newMode","update");
	$entidad->set("mode","$frm[mode]");
	
	$string_entidad=$entidad->getForm($frm);
	$javascript_entidad=$entidad->getJavaScript();
	$iframe=$entidad->getLinkIframe();
	include($CFG->dirroot."/mtto/templates/modulos_form.php");
}

function consultar($frm){
GLOBAL $CFG, $ME, $entidad;

	$entidad->load($frm["id"]);
	$entidad->set("newMode","consultar");
	$entidad->set("mode","$frm[mode]");
	$string_entidad=$entidad->getForm($frm);
	$javascript_entidad=$entidad->getJavaScript();
	include($CFG->dirroot."/mtto/templates/modulos_form.php");
}

function update($frm){
	GLOBAL $CFG, $ME, $entidad;

	$entidad->loadValues($frm);
	$entidad->set("mode","$frm[mode]");
	$entidad->update();
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
	
}

function eliminar($frm){
GLOBAL $CFG, $ME, $entidad;

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

function print_busqueda_form($frm){
GLOBAL $CFG, $ME, $entidad;

	$entidad->set("mode","$frm[mode]");
	$javascript_entidad=$entidad->getJavaScript();
	$string=$entidad->printBusqueda();
	include($CFG->dirroot."/mtto/templates/busqueda_modulo_form.php");
}








?>
