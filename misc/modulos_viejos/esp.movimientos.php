<?

	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Movimientos Especiales");
	$entidad->set("table",$entidad->get("name"));

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------

	$link=new link($entidad);
	$link->set("name","esp.movimientos_personas");
	$link->set("url",$ME . "?module=esp.movimientos_personas");
	$link->set("icon","grupo.jpeg");
	$link->set("description","Personas");
	$link->set("field","id_movimiento");
	$link->set("type","iframe");
	$link->set("relatedTable","esp.movimientos_personas");
	$link->set("popup",true);
	$entidad->addLink($link);


// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_ruta");
	$atributo->set("label","Ruta");
	$atributo->set("sqlType","integer");
	$atributo->set("inputType","querySelect");
	$atributo->set("qsQuery","
			SELECT r.id,  r.fecha||'/'||t.tipo as nombre
			FROM esp.rutas r
			LEFT JOIN esp.tipos_especiales t ON t.id=r.id_tipo_especial
			ORDER BY r.fecha DESC");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","id_vehiculo");
	$atributo->set("label","Vehículo");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","vehiculos");
	$atributo->set("foreignLabelFields","vehiculos.placa||'/'||vehiculos.codigo");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","hora_inicio");
	$atributo->set("label","Hora Inicio");
	$atributo->set("sqlType","date");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","hora_fin");
	$atributo->set("label","Hora Fin");
	$atributo->set("sqlType","date");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$entidad->checkSqlStructure();

?>
