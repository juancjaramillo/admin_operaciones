<?
require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Puntos de control / Rutas");
	$entidad->set("table",$entidad->get("name"));

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------

// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_micro");
	$atributo->set("label","Ruta");
	$atributo->set("inputType","querySelect");
	$atributo->set("qsQuery","SELECT m.id, m.codigo ||'/'||s.servicio as nombre
			FROM micros m
			LEFT JOIN servicios s ON s.id=m.id_servicio
			LEFT JOIN ases a ON a.id=m.id_ase
			WHERE a.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')");
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
	$atributo->set("defaultValueSQL","1");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","hora");
	$atributo->set("label","Hora");
	$atributo->set("sqlType","varchar(5)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","direccion");
	$atributo->set("label","Dirección");
	$atributo->set("sqlType","varchar(256)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","the_geom");
	$atributo->set("label","Ubicación");
	$atributo->set("sqlType","geometry");
	$atributo->set("mandatory",TRUE);
	$atributo->set("inputType","golLocation");
	$atributo->set("geometryType","POINT");
	$atributo->set("geometrySRID",4326);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$atributo->set("visible",FALSE);
	$atributo->set("editable",'READONLY');
	$entidad->addAttribute($atributo);

		
	$entidad->checkSqlStructure();

?>
