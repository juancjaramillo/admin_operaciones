<?

	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Referencias");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","marca,referencia");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------

	$link=new link($entidad);
	$link->set("name","vehiculos");
	$link->set("url",$ME . "?module=vehiculos");
	$link->set("icon","truck.gif");
	$link->set("description","Vehiculos");
	$link->set("field","id_referencia");
	$link->set("relatedTable","vehiculos");
	$entidad->addLink($link);


// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_marca_vehiculo");
	$atributo->set("label","Marca");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","marcas_vehiculos");
	$atributo->set("foreignLabelFields","marca");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);



	$atributo=new attribute($entidad);
	$atributo->set("field","referencia");
	$atributo->set("label","Referencia");
	$atributo->set("sqlType","character varying(155)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	
	$entidad->checkSqlStructure();

?>
 
