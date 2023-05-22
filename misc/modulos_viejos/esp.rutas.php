<?

	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Rutas Especiales");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","fecha desc");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------

	$link=new link($entidad);
	$link->set("name","esp.rutas_clientes");
	$link->set("url",$ME . "?module=esp.rutas_clientes");
	$link->set("icon","icon-route.png");
	$link->set("description","Rutas clientes");
	$link->set("field","id_ruta");
	$link->set("relatedTable","esp.rutas_clientes");
	$link->set("type","iframe");
	$link->set("popup",true);
	$entidad->addLink($link);


// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","fecha");
	$atributo->set("label","Fecha");
	$atributo->set("sqlType","date");
	$atributo->set("defaultValue",date("Y-m-d"));
	$atributo->set("inputType","date");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_tipo_especial");
	$atributo->set("label","Tipo");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","esp.tipos_especiales");
	$atributo->set("foreignLabelFields","esp.tipos_especiales.tipo");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","produccion");
	$atributo->set("label","Producción");
	$atributo->set("sqlType","real");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	

	$entidad->checkSqlStructure();

?>
 
