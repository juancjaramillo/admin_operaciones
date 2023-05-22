<?

	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Servicios de los Vehículos");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","servicio");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------


// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_tipo_vehiculo");
	$atributo->set("label","Tipo Vehículo");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","tipos_vehiculos");
	$atributo->set("foreignLabelFields","tipo");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_servicio");
	$atributo->set("label","Servicio");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","servicios");
	$atributo->set("foreignLabelFields","servicio");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	
	$entidad->checkSqlStructure();

?>
 
