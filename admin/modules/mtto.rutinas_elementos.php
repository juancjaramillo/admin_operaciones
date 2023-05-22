<?
	require_once($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Elementos");
	$entidad->set("table",$entidad->get("name"));

	include("style.php");
	$entidad->set("formColumns",1);
	if(!isset($_SESSION[$CFG->sesion]["user"]) || $_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------

// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_rutina");
	$atributo->set("label","Rutina");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","mtto.rutinas");
	$atributo->set("foreignLabelFields","rutina");
	$atributo->set("foreignTableFilter"," id IN (SELECT id_rutina FROM mtto.rutinas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."'))");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","id_elemento");
	$atributo->set("label","Elemento");
	$atributo->set("ACIdField","e.id");
	$atributo->set("ACLabel","e.codigo || ' - ' || e.elemento||' ('||u.unidad||')'");
	$atributo->set("ACFrom","mtto.elementos e LEFT JOIN mtto.unidades u ON u.id=e.id_unidad");
	$atributo->set("ACFields","e.elemento,e.codigo");
	$atributo->set("ACWhere","e.id IN (SELECT id_elemento FROM mtto.elementos_centros WHERE id_centro IN(SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."'))");
	$atributo->set("inputType","autocomplete");
	$atributo->set("sqlType","int");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);



	/*
	$atributo=new attribute($entidad);
	$atributo->set("field","id_elemento");
	$atributo->set("label","Elemento");
	$atributo->set("sqlType","integer");
	$atributo->set("inputType","querySelect");
	$atributo->set("qsQuery","
			SELECT e.id, e.elemento||'/'||u.unidad||' ('||e.codigo||')' as nombre
			FROM mtto.elementos e
			LEFT JOIN mtto.unidades u ON u.id=e.id_unidad
			ORDER BY e.elemento,u.unidad,e.codigo");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	*/
	

	$atributo=new attribute($entidad);
	$atributo->set("field","cantidad");
	$atributo->set("label","Cantidad");
	$atributo->set("sqlType","real");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	
	$entidad->checkSqlStructure();

?>
