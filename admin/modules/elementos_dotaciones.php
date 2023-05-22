<?

	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Elementos Dotación");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","elemento");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------


// ---------- ATRIBUTOS          ----------------

	
	$atributo=new attribute($entidad);
	$atributo->set("field","elemento");
	$atributo->set("label","Elemento");
	$atributo->set("sqlType","character varying(255)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

		
	$entidad->checkSqlStructure();
?>
