<?

	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Estados");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","estado");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------

	$link=new link($entidad);
	$link->set("name","ordenes_trabajo");
	$link->set("url",$ME . "?module=mtto.ordenes_trabajo");
	$link->set("iconoLetra","O");
	$link->set("description","Órdenes");
	$link->set("field","id_estado_orden_trabajo");
	$link->set("relatedTable","mtto.ordenes_trabajo");
	$entidad->addLink($link);


// ---------- ATRIBUTOS          ----------------

	
	$atributo=new attribute($entidad);
	$atributo->set("field","estado");
	$atributo->set("label","Estado");
	$atributo->set("sqlType","character varying(255)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","cerrado");
	$atributo->set("label","¿Cerrado?");
	$atributo->set("sqlType","boolean");
	$atributo->set("defaultValue",false);
	$atributo->set("inputType","option");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","indicadores");
	$atributo->set("label","¿Afecta Indicadores?");
	$atributo->set("sqlType","boolean");
	$atributo->set("defaultValue",true);
	$atributo->set("inputType","option");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
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
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);




	$entidad->checkSqlStructure();

?>
