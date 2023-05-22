<?
	require_once($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Orden Trabajo Origen");
	$entidad->set("table",$entidad->get("name"));

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}


// ---------- Vinculos a muchos  ----------------


// ---------- ATRIBUTOS          ----------------


	$atributo=new attribute($entidad);
	$atributo->set("field","id_orden_trabajo");
	$atributo->set("label","Orden Trabajo");
	$atributo->set("inputType","querySelect");
	$atributo->set("qsQuery","
			SELECT o.id, r.rutina||'/'||o.fecha_planeada as nombre
			FROM mtto.ordenes_trabajo o
			LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
			WHERE r.id IN (SELECT id_rutina FROM mtto.rutinas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."'))
			");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_orden_trabajo_origen");
	$atributo->set("label","Orden Trabajo Origen");
	$atributo->set("inputType","querySelect");
	$atributo->set("qsQuery","
			SELECT o.id, r.rutina||'/'||o.fecha_planeada as nombre
			FROM mtto.ordenes_trabajo o
			LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
			WHERE r.id IN (SELECT id_rutina FROM mtto.rutinas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."'))
			");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_persona_reporto");
	$atributo->set("label","Persona");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","personas");
	$atributo->set("foreignLabelFields","personas.nombre||' '||personas.apellido");
	$atributo->set("foreignTableFilter"," personas.id IN (SELECT id_persona FROM personas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')) ");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","id_motivo");
	$atributo->set("label","Motivo");
	$atributo->set("inputType","recursiveSelect");
	$atributo->set("parentIdLabel","mtto.motivos.nombre");
	$atributo->set("parentTable","mtto.motivos");
	$atributo->set("useGetPath","TRUE");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	
	$entidad->checkSqlStructure();

?>
