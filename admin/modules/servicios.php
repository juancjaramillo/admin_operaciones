<?

	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Servicios");
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

	$link=new link($entidad);
	$link->set("name","tipos_vehiculos_servicios");
	$link->set("url",$ME . "?module=tipos_vehiculos_servicios");
	$link->set("iconoLetra","S");
	$link->set("description","Servicios");
	$link->set("field","id_servicio");
	$link->set("type","iframe");
	$link->set("relatedTable","tipos_vehiculos_servicios");
	$link->set("popup",true);
	$entidad->addLink($link);


// ---------- ATRIBUTOS          ----------------


	$atributo=new attribute($entidad);
	$atributo->set("field","servicio");
	$atributo->set("label","Servicio");
	$atributo->set("sqlType","character varying(155)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","esquema");
	$atributo->set("label","Esquema");
	$atributo->set("sqlType","varchar(16)");
	$atributo->set("inputType","arraySelect");
	$atributo->set("arrayValues",array("bar"=>"Barrido", "rec"=>"Recolección"));
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_tipo_micro_sui");
	$atributo->set("label","Tipo Micro SUI");
	$atributo->set("sqlType","smallint");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","tipo_micros_sui");
	$atributo->set("foreignTableAlias","ui2");
	$atributo->set("foreignLabelFields", $atributo->get("foreignTableAlias") .".tipo_sui");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_tipo_barrido_sui");
	$atributo->set("label","Tipo Barrido SUI");
	$atributo->set("sqlType","smallint");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","tipos_barridos_sui");
	$atributo->set("foreignTableAlias","ui3");
	$atributo->set("foreignLabelFields", $atributo->get("foreignTableAlias") .".tipo_sui");
		$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","inf_indope");
	$atributo->set("label","Informe Ind Operacionales");
	$atributo->set("sqlType","varchar(16)");
	$atributo->set("inputType","arraySelect");
	//$atributo->set("arrayValues",array("bar"=>"Barrido", "esp"=>"Especiales", "gp"=>"Grandes Productores", "rec"=>"Recolección"));
	$atributo->set("arrayValues",array("bar"=>"Barrido", "rec"=>"Recolección"));
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$entidad->checkSqlStructure();

?>
