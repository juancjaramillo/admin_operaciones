<?
	require($CFG->objectPath . "/object.php");

	class grupos extends entity
	{
		function find()
		{
			$condicionAnterior = " (id_centro IS NULL OR id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".$_SESSION[$this->CFG->sesion]["user"]["id"]."'))";
			parent::find($condicionAnterior);
		}
	}

	$entidad =& new grupos();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Grupos");
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
	$link->set("name","equipos");
	$link->set("url",$ME . "?module=mtto.equipos");
	$link->set("iconoLetra","E");
	$link->set("description","Equipos");
	$link->set("field","id_grupo");
	$link->set("relatedTable","mtto.equipos");
	$entidad->addLink($link);

	$link=new link($entidad);
	$link->set("name","rutinas");
	$link->set("url",$ME . "?module=mtto.rutinas");
	$link->set("iconoLetra","R");
	$link->set("description","Rutinas");
	$link->set("field","id_grupo");
	$link->set("relatedTable","mtto.rutinas");
	$entidad->addLink($link);

	$link=new link($entidad);
	$link->set("name","vehiculos");
	$link->set("url",$ME . "?module=vehiculos");
	$link->set("iconoLetra","V");
	$link->set("description","Vehiculos");
	$link->set("field","id_grupo");
	$link->set("relatedTable","vehiculos");
	$entidad->addLink($link);

// ---------- ATRIBUTOS          ----------------

	
	$atributo=new attribute($entidad);
	$atributo->set("field","id_superior");
	$atributo->set("label","Superior");
	$atributo->set("inputType","recursiveSelect");
	$atributo->set("useGetPath",TRUE);
	$atributo->set("parentCondicion"," id IN (SELECT id FROM mtto.grupos WHERE id_centro IS NULL OR id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."'))");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","nombre");
	$atributo->set("label","Nombre");
	$atributo->set("sqlType","character varying(255)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_centro");
	$atributo->set("label","Centro");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","centros");
	$atributo->set("foreignLabelFields","centro");
	$atributo->set("foreignTableFilter"," id IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	
	$atributo=new attribute($entidad);
	$atributo->set("field","descripcion");
	$atributo->set("label","Descripción");
	$atributo->set("sqlType","text");
	$atributo->set("inputType","textarea");
	$atributo->set("inputSize",40);
	$atributo->set("inputRows",2);
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	
	$entidad->checkSqlStructure();

?>
