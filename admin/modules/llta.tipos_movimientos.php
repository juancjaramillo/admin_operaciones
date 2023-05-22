<?

	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Tipos Movimientos");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","tipo");
	$entidad->set("HLRows",false);


	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
//		$entidad->set("btnAdd",FALSE);
//		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------

	if(preg_match("/admin/",$ME,$match)){
	$link=new link($entidad);
	$link->set("name","llta.subtipos_movimientos");
	$link->set("url",$ME . "?module=llta.subtipos_movimientos");
	$link->set("icon","icon-settings.gif");
	$link->set("description","Subtipos");
	$link->set("field","id_tipo_movimiento");
	$link->set("type","iframe");
	$link->set("relatedTable","llta.subtipos_movimientos");
	$link->set("popup",true);
	$entidad->addLink($link);
	}



// ---------- ATRIBUTOS          ----------------


	$atributo=new attribute($entidad);
	$atributo->set("field","tipo");
	$atributo->set("label","Tipo");
	$atributo->set("sqlType","character varying(255)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	
	$atributo=new attribute($entidad);
	$atributo->set("field","id_estado");
	$atributo->set("label","Cambia automáticamente a");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","llta.estados");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$entidad->checkSqlStructure();

?>
 
