<?
	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Puntos de interés");
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
	$link->set("name","puntos_interes");
	$link->set("url",$ME . "?module=puntos_interes");
	$link->set("icon","icon-overview.gif");
	$link->set("description","Puntos de interés");
	$link->set("field","id_categoria");
	$link->set("type","iframe");
	$link->set("relatedTable","puntos_interes");
	$link->set("popup",true);
	$entidad->addLink($link);

// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","nombre");
	$atributo->set("label","Categoría");
	$atributo->set("sqlType","character varying(50)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","icono");
	$atributo->set("label","Icono");
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
