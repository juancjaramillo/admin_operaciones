<?
	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Actividades");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","orden");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------

	$link=new link($entidad);
	$link->set("name","Cargos");
	$link->set("url",$ME . "?module=mtto.ordenes_trabajo_actividades_cargos");
	$link->set("icon","boy.gif");
	$link->set("description","Cargos");
	$link->set("field","id_orden_trabajo_actividad");
	$link->set("relatedTable","mtto.ordenes_trabajo_actividades_cargos");
	$link->set("type","iframe");
	$link->set("popup",true);
	$entidad->addLink($link);

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
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","orden");
	$atributo->set("label","Orden");
	$atributo->set("sqlType","smallint");
	$atributo->set("defaultValue",1);
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","descripcion");
	$atributo->set("label","Descripción");
	$atributo->set("sqlType","text");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	
	$atributo=new attribute($entidad);
	$atributo->set("field","tiempo");
	$atributo->set("label","Tiempo (minutos)");
	$atributo->set("sqlType","real");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);




	$entidad->checkSqlStructure();

?>
 
