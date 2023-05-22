<?

	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Tipos Vehiculos");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","tipo");

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
	$link->set("field","id_tipo_vehiculo");
	$link->set("relatedTable","vehiculos");
	$entidad->addLink($link);


	$link=new link($entidad);
	$link->set("name","tipos_vehiculos_servicios");
	$link->set("url",$ME . "?module=tipos_vehiculos_servicios");
	$link->set("iconoLetra","S");
	$link->set("description","Servicios");
	$link->set("field","id_tipo_vehiculo");
	$link->set("type","iframe");
	$link->set("relatedTable","tipos_vehiculos_servicios");
	$link->set("popup",true);
	$entidad->addLink($link);

// ---------- ATRIBUTOS          ----------------


	$atributo=new attribute($entidad);
	$atributo->set("field","tipo");
	$atributo->set("label","Tipo");
	$atributo->set("sqlType","character varying(155)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","capacidad");
	$atributo->set("label","Capacidad (ton)");
	$atributo->set("sqlType","real");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	//para el informe del sui
	$atributo=new attribute($entidad);
	$atributo->set("field","capacidad_yardas");
	$atributo->set("label","Capacidad (yd<sup>3</sup>)");
	$atributo->set("sqlType","real");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","peso");
	$atributo->set("label","Peso");
	$atributo->set("sqlType","real");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","ejes");
	$atributo->set("label","Ejes");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","num_llantas");
	$atributo->set("label","Número LLantas");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","icono");
	$atributo->set("label","Icono");
	$atributo->set("sqlType","character varying(50)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	

	$atributo=new attribute($entidad);
	$atributo->set("field","combustible");
	$atributo->set("label","Combustible max (gal)");
	$atributo->set("sqlType","float");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","orden_info");
	$atributo->set("label","Orden)");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$entidad->checkSqlStructure();

?>
 
