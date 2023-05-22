<?

	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Marcas");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","marca");
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
	$link->set("name","referencias");
	$link->set("url",$ME . "?module=llta.referencias");
	$link->set("iconoLetra","R");
	$link->set("description","Referencias");
	$link->set("field","id_marca");
	$link->set("popup",true);

	$entidad->addLink($link);
	}

// ---------- ATRIBUTOS          ----------------


	$atributo=new attribute($entidad);
	$atributo->set("field","marca");
	$atributo->set("label","Marca");
	$atributo->set("sqlType","character varying(255)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	
	
	$entidad->checkSqlStructure();

?>
 
