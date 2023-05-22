<?

	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Clientes");
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
			FROM gp.movimientos mov
			LEFT JOIN vehiculos v ON v.id=mov.id_vehiculo
			LEFT JOIN gp.rutas m ON m.id=mov.id_ruta
			ORDER BY nombre");
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
	$atributo->set("ACIdField","clientes.id");
	$atributo->set("ACLabel","(clientes.codigo||'/'||clientes.nombre||'/'||clientes.direccion)");
	$atributo->set("ACFrom","clientes");
	$atributo->set("ACFields","clientes.codigo||'/'||clientes.nombre||'/'||clientes.direccion");
	$atributo->set("inputType","autocomplete");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","hora");
	$atributo->set("label","Hora");
	$atributo->set("sqlType","time");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","produccion");
	$atributo->set("label","Producción");
	$atributo->set("sqlType","real");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$entidad->checkSqlStructure();

?>
