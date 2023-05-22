<?

	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Movimientos / Clientes");
	$entidad->set("table",$entidad->get("name"));

	include("style.php");
	$entidad->set("formColumns",1);

// ---------- Vinculos a muchos  ----------------

// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_movimiento");
	$atributo->set("label","Mov: fecha/ruta/placa/codigo");
	$atributo->set("inputType","querySelect");
	$atributo->set("qsQuery","
			SELECT mov.id, mov.inicio||' / '||m.codigo||' / '||v.placa||' / '||v.codigo as nombre
			FROM rec.movimientos mov
			LEFT JOIN vehiculos v ON v.id=mov.id_vehiculo
			LEFT JOIN micros m ON m.id=mov.id_micro
			WHERE m.id_ase IN (SELECT id FROM ases WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."'))
			ORDER BY mov.inicio desc, m.codigo, v.codigo, v.placa");
	/*
	if(isset($_GET["mode"]) && $_GET["mode"] == "agregar")
		$atributo->set("qsQuery","
				SELECT mov.id, mov.inicio||' / '||m.codigo||' / '||v.placa||' / '||v.codigo as nombre
				FROM rec.movimientos mov
				LEFT JOIN vehiculos v ON v.id=mov.id_vehiculo
				LEFT JOIN micros m ON m.id=mov.id_micro
				WHERE m.id_ase IN (SELECT id FROM ases WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')) AND mov.inicio::date BETWEEN (now()::date - integer '120') AND now()
				ORDER BY mov.inicio desc, m.codigo, v.codigo, v.placa");
	*/
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_cliente");
	$atributo->set("label","Cliente");
	$atributo->set("sqlType","integer");
	$atributo->set("ACIdField","c.id");
	$atributo->set("ACLabel","(c.nombre||' / '||c.direccion)");
	$atributo->set("ACFrom","clientes c");
	$atributo->set("ACFields","c.nombre, c.direccion, c.telefono");
	$atributo->set("ACWhere"," c.gp='t' AND id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')");
	$atributo->set("inputType","autocomplete");
	$atributo->set("inputSizeAutocomplete","70");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$entidad->checkSqlStructure();

?>
