<?

/*	----------------------------------------------	*/
/*						MAIN						*/
/*	----------------------------------------------	*/


/************************************************************/
/***************Cargas lo smodulos de ingreso****************/
/******** Se inicializa en el archivo application************/
/***********************************************************/


	include("../application.php");
	if(sizeof($_POST)>sizeof($_GET)) $frm=$_POST;
	else $frm=$_GET; 
	$module=nvl($frm["module"],$CFG->defaultModule);
	$CFG->module=$module;
	$nivel_accesoAd=$_SESSION[$CFG->sesion]["user"]["nivel_acceso"];


	if(nvl($_GET["module"], nvl($_POST["module"])) == "")
		echo "<script>window.location.href='".$CFG->wwwroot."/admin/index.php?module=".$module."';</script>";

  $permModulos = array("vehiculos");
  if(in_array($nivel_accesoAd,array(9)))
    if(!in_array($module, $permModulos))
      die("No tiene permisos.");


	$permModulos = array("vehiculos","personas","personas_cargos","personas_servicios", "personas_informes","gps_vehi","ases","turnos", "cargos", "clientes");
	if(in_array($nivel_accesoAd,array(13)))
		if(!in_array($module, $permModulos))
			die("No tiene permisos.");
  $permModulos = array("vehiculos");
  if(in_array($nivel_accesoAd,array(9)))
    if(!in_array($module, $permModulos))
      die("No tiene permisos.");
  	
	if(file_exists($CFG->modulesdir . "/" . $module . ".php"))
		include($CFG->modulesdir . "/" . $module . ".php");
	elseif(file_exists($CFG->modulesdir . "/" . $module . ".phtml"))
		include($CFG->modulesdir . "/" . $module . ".phtml");
	else{
		include($CFG->templatedir . "/header.php");
		die("[" . $module . "]:<br>Módulo no implementado.");
	}
	if(file_exists($CFG->adminroot . "/permisos.php")) include($CFG->adminroot . "/permisos.php");


	switch(nvl($frm["mode"])){
		case "download":
			download($_GET);
		  break;

		case "agregar" :
			agregar($_GET);
			break;

		case "insert" :
			insert($_POST);
			break;

		case "editar" :
			editar($_GET);
			break;

		case "consultar" :
			consultar($_GET);
			break;

		case "update" :
			update($_POST);
			break;

		case "update_event_input" :
			update_event_input($_POST);
			break;

		case "eliminar" :
			eliminar($_GET);
			break;

		case "buscar" :
			print_busqueda_form($_GET);
			break;

		case "aprobar":
			aprobar($_POST);
			break;

		case "actualizar":
			update_list($_GET);
			break;	
		
		case "find":
			find($_GET);
			break;

		case "eliminarImagen":
			eliminarImagen($_GET);
		break;

		case "eliminarFS":
			eliminarFS($_GET);
		break;

		case "duplicar_rutina_en_admin":
			duplicar_rutina_en_admin($_GET);
		break;

		default:
			encontrar($_GET);
			break;
	}


/*	********************************************	*/
/*	                 FUNCIONES:                 	*/
/*	********************************************	*/

function download($frm){
	GLOBAL $CFG, $ME, $entidad;
	$entidad->loadValues($frm);
	$entidad->maxRows=NULL;//Para que no les ponga límite a los listados.
	$entidad->find();
	$filename=$entidad->name;
	$ext="csv";
	include("./templates/csv.php");
}

function eliminarFS($frm){
  global $db,$entidad,$ME,$CFG;

  $dir=preg_replace("/\/+/","/",$CFG->dirroot . "/" . $CFG->filesdir);
  $archivo=$dir . "/" . $entidad->table . "/" . $frm["field"] . "/" . $frm["id"];
  if(file_exists($archivo)){
    if(!unlink($archivo)) die("Error:<br>\nNo se pudo eliminar el archivo <b>$archivo</b>.<br>\n<a href=\"" . $_SERVER["HTTP_REFERER"] . "\">Volver</a>");
  }

  $entidad->db->sql_query("UPDATE ".$entidad->table."
      SET
      mmdd_".$frm["field"]."_filename = NULL,
      mmdd_".$frm["field"]."_filetype = NULL,
      mmdd_".$frm["field"]."_filesize = NULL,
      ".$frm["field"]."= NULL
      WHERE id = ".$frm["id"]);

  $frm['mode']="editar";
  editar($frm);
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

function aprobar($frm){
}

function agregar($frm){
GLOBAL $CFG, $ME, $entidad;

	$entidad->set("mode","$frm[mode]");
	$entidad->set("newMode","insert");
	$entidad->loadValues($frm);
	include($CFG->templatedir . "/headerpopup.php");
	$string_entidad=$entidad->getForm($frm);
	$javascript_entidad=$entidad->getJavaScript();
	$iframe=$entidad->getLinkIframe();
	include("templates/entidad_form.php");

}

function insert($frm){
GLOBAL $CFG, $ME, $entidad,$db;

	$entidad->loadValues($frm);
	$entidad->set("mode","$frm[mode]");
	$frm["id"]=$entidad->insert();
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
//						window.opener.focus();
						window.close();
					}
					else{
						window.opener.location.reload();
//						window.opener.focus();
						window.close();
					}
				";
			}
			else{
//				echo "window.opener.location.reload();\nwindow.opener.focus();\nwindow.close();\n";
				echo "window.opener.location.reload();\nwindow.close();\n";
			}
		}
		else{
//			echo "window.opener.location.reload();\nwindow.opener.focus();\nwindow.close();\n";
			echo "window.opener.location.reload();\nwindow.close();\n";
		}
		echo "</script>";
	}
	else{
		$frm['mode']="editar";
		editar($frm);
	}
		
}

function editar($frm){
GLOBAL $CFG, $ME, $entidad;

	$entidad->load($frm["id"]);
	$entidad->set("newMode","update");
	if(isset($frm["update_event_input"]))
		$entidad->set("newMode","update_event_input");
	$entidad->set("mode","$frm[mode]");
	include($CFG->templatedir . "/headerpopup.php");
	
	if($entidad->name == "cuentas_correo")
		echo "<center><font color='red'>NOTA: El único campo que se puede modificar es:  Password.</font></center><br>";
	if($entidad->name == "sitios")
		echo "<center><font color='red'>NOTA: Los únicos campos que <b>NO</b> puede editar son: Dominio, Tamaño del disco, Manejo DNS.</font></center><br>";
	
	$string_entidad=$entidad->getForm($frm);
	$javascript_entidad=$entidad->getJavaScript();
	$iframe=$entidad->getLinkIframe();
	include("templates/entidad_form.php");

}

function consultar($frm){
GLOBAL $CFG, $ME, $entidad;

	$entidad->load($frm["id"]);
	$entidad->set("newMode","consultar");
	$entidad->set("mode","$frm[mode]");
	include($CFG->templatedir . "/headerpopup.php");
	$string_entidad=$entidad->getForm($frm);
	$javascript_entidad=$entidad->getJavaScript();
	include("templates/entidad_form.php");

}

function update($frm){
	GLOBAL $CFG, $ME, $entidad;

	$entidad->loadValues($frm);
//	$entidad->load($frm["id"]);
	$entidad->set("mode","$frm[mode]");
	$entidad->update();
//	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.opener.focus();\nwindow.close();\n</script>";
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
	
}

function update_event_input($frm){
	GLOBAL $CFG, $ME, $entidad,$db;

	if($frm["mode"]=="update_event_input"){
		$fecha=explode(" ",$frm["fecha"]);
		$id_gps_vehi=$frm["id_gps_vehi"];
		
		$strq="SELECT x(the_geom) AS longitude, y(the_geom) AS latitude FROM gps_vehi WHERE id=$id_gps_vehi";
		$qid=$db->sql_query($strq);
		if($gps_vehi=$db->sql_fetchrow($qid)){
			$arreglo["longitude"]=$gps_vehi["longitude"];
			$arreglo["latitude"]=$gps_vehi["latitude"];
		}
			
		$arreglo["date"]=$fecha[0];
		$arreglo["time"]=$fecha[1];
		$arreglo["phone"]=$frm["phone"];
		$arreglo["event"]=$frm["event"];
		$arreglo["eventInput"]=$frm["event_input"];

		include($CFG->libdir . "/funciones_gps_solo_hll.php");
		
		$array_err=alimentar_tablas($arreglo,$id_gps_vehi);
		if(is_array($array_err)){
			$array_err["descripcion"]="CORREGIDO : " . $frm["descripcion"];
		}
		else{
			$array_err=array();
			$array_err["id_tipo"]=11;
		}
			$db->sql_update("errores",$array_err,$frm["id"]);
	}
//	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.opener.focus();\nwindow.close();\n</script>";
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
	
}

function update_list($frm){
GLOBAL $CFG, $ME, $entidad;
	
	$entidad->set("mode","$frm[mode]");
	$entidad->update_list($frm);
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.location.href='$ME?module=$frm[module]'</script>";

}

function eliminar($frm){
GLOBAL $CFG, $ME, $entidad;

//	for($i=0;$i<sizeof($frm["id"]);$i++){
//		$entidad->load($frm["id"][$i]);
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
//	}
//	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.opener.focus();\nwindow.close();\n</script>";
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.opener.location.reload();\nwindow.close();\n</script>";
	
}

function print_busqueda_form($frm){
GLOBAL $CFG, $ME, $entidad;

	include($CFG->templatedir . "/headerpopup.php");

	$entidad->set("mode","$frm[mode]");
	$javascript_entidad=$entidad->getJavaScript();
	$string=$entidad->printBusqueda();
	include("templates/busqueda_form.php");

}

function encontrar($frm){
GLOBAL $CFG, $ME, $entidad, $db;

	$entidad->loadValues($frm);
	$entidad->find();
	if(isset($frm['popup']) || isset($frm['iframe'])){
		include($CFG->templatedir . "/headerpopup.php");	
		include("templates/listado_simple.php");
	}
	else{
		include($CFG->templatedir . "/header.php");
		$entidad->findPath();
		include("templates/listado.php");
		include($CFG->templatedir . "/footer.php");
	}

}


function eliminarImagen($frm)
{
	global $db,$entidad,$ME,$CFG;

	$entidad->db->sql_query("UPDATE ".$entidad->table."
			SET
			mmdd_".$frm["field"]."_filename = NULL,
			mmdd_".$frm["field"]."_filetype = NULL,
			mmdd_".$frm["field"]."_filesize = 0,
			".$frm["field"]."= NULL
			WHERE id = ".$frm["id"]);
	$frm['mode']="editar";
	editar($frm);
}

function duplicar_rutina_en_admin($frm)
{
	global $db,$entidad,$ME,$CFG;

	duplicar_rutina($frm);

	$url = $CFG->wwwroot."/admin/index.php?module=mtto.rutinas";
	echo "<script>
		    window.location.href='".$url."';
	</script>";
}









?>
