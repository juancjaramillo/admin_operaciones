<?
	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Alertas");
	$entidad->set("table",$entidad->get("name"));

	include("style.php");
	$user=$_SESSION[$CFG->sesion]["user"];

	$entidad->set("formColumns",1);
	if($user["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------

// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","hora");
	$atributo->set("label","Hora");
	$atributo->set("sqlType","timestamp");
	$atributo->set("defaultValue",date("Y-m-d H:i:s"));
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_centro");
	$atributo->set("label","Centro");
	$atributo->set("sqlType","smallint");
	$atributo->set("inputType","querySelect");
	$atributo->set("qsQuery","
		SELECT c.id, c.centro as nombre
		FROM centros c
		WHERE c.id IN (SELECT id_centro FROM personas_centros WHERE id_persona = '".$user["id"]."')");
	$atributo->set("mandatory",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_tipo");
	$atributo->set("label","Tipo");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","tipos_alertas");
	$atributo->set("sqlType","integer");
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
	$atributo->set("foreignLabelFields","vehiculos.codigo||'/'||vehiculos.placa");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_micro");
	$atributo->set("label","Ruta");
	$atributo->set("inputType","querySelect");
	$atributo->set("qsQuery","
		SELECT m.id, m.codigo ||'/'||s.servicio as nombre
		FROM micros m
			LEFT JOIN servicios s ON s.id=m.id_servicio
			LEFT JOIN ases a ON a.id=m.id_ase
		WHERE a.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."')
	");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","ack_id_motivo");
	$atributo->set("label","Motivo de VoBo");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","motivos_ack");
//	$atributo->set("foreignLabelFields","personas.nombre||' '||personas.apellido");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","ack_id_persona");
	$atributo->set("label","Persona que da VoBo");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","personas");
	$atributo->set("foreignLabelFields","personas.nombre||' '||personas.apellido");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","ack_hora");
	$atributo->set("label","Hora de VoBo");
	$atributo->set("sqlType","timestamp");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","observaciones");
	$atributo->set("label","Observaciones");
	$atributo->set("sqlType","text");
	$atributo->set("inputType","textarea");
	$atributo->set("inputSize",40);
	$atributo->set("inputRows",2);
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$entidad->checkSqlStructure();

?>
