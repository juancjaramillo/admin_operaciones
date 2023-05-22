<?

	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Estados llantas");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","nombre");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------
	$link=new link($entidad);
	$link->set("name","Posibles movimientos");
	$link->set("description","Posibles movimientos");
	$link->set("field","id_estado");
	$link->set("type","checkbox");
	$link->set("visible",FALSE);
	$link->set("relatedTable","llta.estados_tiposmovimiento");
	$link->set("relatedICTable","llta.tipos_movimientos");
	$link->set("relatedICField","llta.tipos_movimientos.tipo");
	$link->set("relatedICIdFieldUno","id_estado");
	$link->set("relatedICIdFieldDos","id_tipo_movimiento");
	$entidad->addLink($link);


// ---------- ATRIBUTOS          ----------------


	$atributo=new attribute($entidad);
	$atributo->set("field","nombre");
	$atributo->set("label","Estado");
	$atributo->set("sqlType","character varying(155)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$entidad->checkSqlStructure();

?>
 
