<?

	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Bolsas");
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
	$atributo->set("field","id_movimiento");
	$atributo->set("label","Movimiento");
	
	$atributo->set("inputType","querySelect");
	$atributo->set("qsQuery","
			SELECT mov.id, m.codigo||'/'||v.placa||'/'||v.codigo as nombre
			FROM bar.movimientos mov
			LEFT JOIN vehiculos v ON v.id=mov.id_vehiculo
			LEFT JOIN micros m ON m.id=mov.id_micro
			WHERE mov.id_micro IN (SELECT mi.id FROM micros mi LEFT JOIN ases a ON a.id=mi.id_ase WHERE a.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona ='".$_SESSION[$CFG->sesion]["user"]["id"]."')) 
			ORDER BY nombre");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","id_tipo_bolsa");
	$atributo->set("label","Bolsa");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","bar.tipos_bolsas");
	$atributo->set("foreignLabelFields","bar.tipos_bolsas.tipo");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","numero_inicio");
	$atributo->set("label","Bolsas Inicio");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","numero_fin");
	$atributo->set("label","Bolsas Fin");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$entidad->checkSqlStructure();

?>
