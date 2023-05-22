<?

	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Dimensiones");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","marca,dimension");
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
	$link->set("name","LLantas");
	$link->set("url",$ME . "?module=llta.llantas");
	$link->set("icon","llanta.jpeg");
	$link->set("description","Llantas");
	$link->set("field","id_dimension");
	$link->set("relatedTable","llta.llantas");
	$entidad->addLink($link);
	}
	

// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_marca");
	$atributo->set("label","Marca");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","llta.marcas");
	$atributo->set("foreignLabelFields","llta.marcas.marca");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	
	$atributo=new attribute($entidad);
	$atributo->set("field","dimension");
	$atributo->set("label","Dimensión");
	$atributo->set("sqlType","varchar(255)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	

	$entidad->checkSqlStructure();

?>
