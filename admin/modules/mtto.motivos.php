<?

	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Motivos");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","nombre");
	$entidad->set("HLRows",false);

	include("style.php");
	$entidad->set("formColumns",1);

	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
	//	$entidad->set("btnAdd",FALSE);
	//	$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------



// ---------- ATRIBUTOS          ----------------

	
	$atributo=new attribute($entidad);
	$atributo->set("field","id_superior");
	$atributo->set("label","Superior");
	$atributo->set("inputType","recursiveSelect");
	$atributo->set("useGetPath",TRUE);
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","nombre");
	$atributo->set("label","Motivo");
	$atributo->set("sqlType","character varying(255)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	
	$entidad->checkSqlStructure();

?>
