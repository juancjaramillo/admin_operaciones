<?
	require($CFG->objectPath . "/object.php");

	class excepciones extends entity
	{
		function find()
		{
			$condicionAnterior = " id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".$_SESSION[$this->CFG->sesion]["user"]["id"]."')";
			parent::find($condicionAnterior);
		}
	}

	$entidad =& new excepciones();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Excepciones Diarias");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","dia");
	$entidad->set("HLRows",false);

	include("style.php");
	$entidad->set("formColumns",1);
	/*
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}
	*/

// ---------- Vinculos a muchos  ----------------



// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_centro");
	$atributo->set("label","Centro");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","centros");
	$atributo->set("foreignLabelFields","centro");
	$atributo->set("sqlType","smallint");
	$atributo->set("foreignTableFilter"," id IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","dia");
	$atributo->set("label","Día");
	$atributo->set("sqlType","smallint");
	$atributo->set("inputType","arraySelect");
	$atributo->set("arrayValues",array("1"=>"Lunes", "2"=>"Martes","3"=>"Miercóles","4"=>"Jueves","5"=>"Viernes","6"=>"Sábado","7"=>"Domingo"));
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$entidad->checkSqlStructure();

?>
