<?

	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Informes");
	$entidad->set("table",$entidad->get("name"));

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------

		$link=new link($entidad);
		$link->set("name","personas_informes");
		$link->set("url",$ME . "?module=personas_informes");
		$link->set("iconoLetra","I");
		$link->set("description","Informes");
		$link->set("field","id_informe");
		$link->set("type","iframe");
		$link->set("relatedTable","personas_informes");
		$link->set("popup",true);
		$entidad->addLink($link);


// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_categoria_informe");
	$atributo->set("label","Categoria");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","categorias_informes");
	$atributo->set("foreignLabelFields","nombre");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","informe");
	$atributo->set("label","Informe");
	$atributo->set("sqlType","character varying(255)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	
	
	$entidad->checkSqlStructure();

?>
 
