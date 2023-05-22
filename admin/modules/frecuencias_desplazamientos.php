<?
	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","DESPLAZAMIENTOS PREDETERMINADOS");
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
	$atributo->set("field","id_frecuencia");
	$atributo->set("label","Micro/Frecuencia");
	$atributo->set("inputType","querySelect");
	$atributo->set("qsQuery","
			SELECT micros_frecuencia.id, micros.codigo||'/'||case when dia=1 then 'Lunes' when dia=2 then 'Martes' when dia=3 then 'Mi�rcoles' when dia=4 then 'Jueves' when dia=5 then 'Viernes' when dia=6 then 'S�bado' else 'Domingo' end as nombre
			FROM micros_frecuencia
			LEFT JOIN micros ON micros_frecuencia.id_micro=micros.id
			WHERE micros.id IN (SELECT id FROM micros WHERE id_ase IN (SELECT id FROM ases WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona = '".$_SESSION[$CFG->sesion]["user"]["id"]."')))
			ORDER BY micros.codigo, dia");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","id_tipo_desplazamiento");
	$atributo->set("label","Tipo");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","rec.tipos_desplazamientos");
	$atributo->set("foreignLabelFields","rec.tipos_desplazamientos.tipo");
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
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$entidad->checkSqlStructure();

?>
