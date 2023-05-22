<?

	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Prioridades");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","porcentaje");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------

	$link=new link($entidad);
	$link->set("name","rutinas");
	$link->set("url",$ME . "?module=rutinas");
	$link->set("iconoLetra","R");
	$link->set("description","Rutinas");
	$link->set("field","id_prioridad");
	$link->set("relatedTable","rutinas");
	$entidad->addLink($link);


// ---------- ATRIBUTOS          ----------------

	
	$atributo=new attribute($entidad);
	$atributo->set("field","prioridad");
	$atributo->set("label","Prioridad");
	$atributo->set("sqlType","character varying(255)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	
	$atributo=new attribute($entidad);
	$atributo->set("field","porcentaje");
	$atributo->set("label","Porcentaje");
	$atributo->set("sqlType","real");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","imagen");
	$atributo->set("label","Imagen");
	$atributo->set("sqlType","text");
	$atributo->set("inputType","fileFS");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",FALSE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	
	$entidad->checkSqlStructure();

?>
