<?

	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Micros");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","codigo");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------

	$link=new link($entidad);
	$link->set("name","bar.movimientos");
	$link->set("url",$ME . "?module=bar.movimientos");
	$link->set("icon","icon-activate.gif");
	$link->set("description","Movimientos");
	$link->set("field","id_micro_barrido");
	$link->set("relatedTable","bar.movimientos");
	$entidad->addLink($link);

// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_micro");
	$atributo->set("label","Micro");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","micros");
	$atributo->set("foreignLabelFields","micros.codigo");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","codigo");
	$atributo->set("label","Código");
	$atributo->set("sqlType","character varying(1055)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	
	$atributo=new attribute($entidad);
	$atributo->set("field","km");
	$atributo->set("label","Km");
	$atributo->set("sqlType","character varying(1055)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	

	$atributo=new attribute($entidad);
	$atributo->set("field","id_cuartelillo");
	$atributo->set("label","Cuartelillo");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","cuartelillos");
	$atributo->set("foreignLabelFields","cuartelillos.nombre");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","geometry");
	$atributo->set("label","Geometry");
	$atributo->set("sqlType","geometry");
	$atributo->set("geometryType","POLYGON");
	$atributo->set("geometrySRID",4326);
	$atributo->set("searchable",FALSE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$atributo->set("visible",FALSE);
	$atributo->set("editable",'READONLY');
	$entidad->addAttribute($atributo);


	$entidad->checkSqlStructure();

?>
 
