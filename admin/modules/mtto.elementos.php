<?

	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Elementos");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","elemento");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------

	$link=new link($entidad);
	$link->set("name","Centros");
	$link->set("description","Centros");
	$link->set("field","id_elemento");
	$link->set("type","checkbox");
	$link->set("visible",FALSE);
	$link->set("relatedTable","mtto.elementos_centros");
	$link->set("relatedICTable","centros");
	$link->set("relatedICField","centros.centro");
	$link->set("relatedICIdFieldUno","id_elemento");
	$link->set("relatedICIdFieldDos","id_centro");
	$entidad->addLink($link);

	$link=new link($entidad);
	$link->set("name","mtto.elementos_existencias");
	$link->set("url",$ME . "?module=mtto.elementos_existencias");
	$link->set("icon","icon-settings.gif");
	$link->set("description","Existencias");
	$link->set("field","id_elemento");
	$link->set("type","iframe");
	$link->set("relatedTable","mtto.elementos_existencias");
	$link->set("popup",true);
	$entidad->addLink($link);

// ---------- ATRIBUTOS          ----------------

	
	$atributo=new attribute($entidad);
	$atributo->set("field","codigo");
	$atributo->set("label","Código");
	$atributo->set("sqlType","character varying(255)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	
	$atributo=new attribute($entidad);
	$atributo->set("field","elemento");
	$atributo->set("label","Elemento");
	$atributo->set("sqlType","character varying(255)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","tipoe");
	$atributo->set("label","Tipo");
	$atributo->set("sqlType","integer");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","mtto.ele_tipos");
//	$atributo->set("arrayValues",array("1"=>"Repuesto", "2"=>"Consumible"));
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_unidad");
	$atributo->set("label","Unidad");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","mtto.unidades");
	$atributo->set("foreignLabelFields","unidad");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	
	$entidad->checkSqlStructure();

?>
