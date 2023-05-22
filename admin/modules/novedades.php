<?
	require_once($CFG->objectPath . "/object.php");

	class novedades extends entity
	{
		function find()
		{
			$condicionAnterior = " novedades.id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".$_SESSION[$this->CFG->sesion]["user"]["id"]."')";
			parent::find($condicionAnterior);
		}
	}

	$entidad =& new novedades();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Novedades");
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
	$atributo->set("inputType","select");
	$atributo->set("field","id_centro");
	$atributo->set("label","Centro");
	$atributo->set("sqlType","smallint");
	$atributo->set("foreignTable","centros");
	$atributo->set("foreignLabelFields","centro");
	$atributo->set("foreignTableFilter"," id IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')");
	$atributo->set("mandatory",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","id_tipo_novedad");
	$atributo->set("label","Tipo");
	$atributo->set("parentIdLabel","tipos_novedades.nombre");
	$atributo->set("parentTable","tipos_novedades");
	$atributo->set("useGetPath","TRUE");
	$atributo->set("inputType","recursiveSelect");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","esquema");
	$atributo->set("label","Esquema");
	$atributo->set("sqlType","varchar(16)");
	$atributo->set("inputType","arraySelect");
	$atributo->set("arrayValues",array("bar"=>"Barrido", "esp"=>"Especiales", "gp"=>"Grandes Productores", "rec"=>"Recolección","mtto"=>"Mantenimiento"));
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","id_movimiento");
	$atributo->set("label","Movimiento");
	$atributo->set("inputType","querySelect");
	$atributo->set("qsQuery","
		SELECT mov.id, m.codigo||'/'||v.codigo||'/'||v.placa as nombre
		FROM rec.movimientos mov
		LEFT JOIN vehiculos v ON v.id=mov.id_vehiculo
		LEFT JOIN micros m ON m.id=mov.id_micro
		ORDER BY nombre");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","hora_inicio");
	$atributo->set("label","Hora Inicio");
	$atributo->set("sqlType","timestamp");
	$atributo->set("inputType","timestamp");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","hora_fin");
	$atributo->set("label","Hora Fin");
	$atributo->set("sqlType","timestamp");
	$atributo->set("inputType","timestamp");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
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


	$atributo=new attribute($entidad);
	$atributo->set("field","id_vehiculo_apoyo");
	$atributo->set("label","Vehículo Apoyo");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","vehiculos");
	$atributo->set("foreignLabelFields","vehiculos.codigo||'/'||vehiculos.placa");
	$atributo->set("foreignTableFilter"," id IN (SELECT id FROM vehiculos WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')) ");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","id_equipo");
	$atributo->set("label","Equipo");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","mtto.equipos");
	$atributo->set("foreignLabelFields","mtto.equipos.nombre");
	$atributo->set("foreignTableFilter"," id IN (SELECT id FROM mtto.equipos WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')) ");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_reporta");
	$atributo->set("label","Persona que reporta");
	$atributo->set("sqlType","smallint");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","personas");
	$atributo->set("foreignTableAlias","p1");
	$atributo->set("foreignLabelFields","p1.nombre||' '||p1.apellido");
	$atributo->set("foreignTableFilter"," p1.id IN (SELECT id_persona FROM personas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')) ");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_ingresa");
	$atributo->set("label","Persona que ingresa al sist.");
	$atributo->set("sqlType","smallint");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","personas");
	$atributo->set("foreignTableAlias","p2");
	$atributo->set("foreignLabelFields","p2.nombre||' '||p2.apellido");
	$atributo->set("foreignTableFilter"," p2.id IN (SELECT id_persona FROM personas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')) ");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$entidad->checkSqlStructure();

?>
