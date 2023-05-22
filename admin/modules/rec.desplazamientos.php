<?
	require($CFG->objectPath . "/object.php");

class desplazamientos extends entity
{
	function insert()
	{
		$this->db->sql_query("UPDATE rec.desplazamientos SET hora_fin='".$this->getAttributeByName("hora_inicio")->get("value")."' WHERE id_movimiento='".$this->getAttributeByName("id_movimiento")->get("value")."' AND hora_fin IS NULL AND hora_inicio <='".$this->getAttributeByName("hora_inicio")->get("value")."'");
		$this->id = parent::insert();
		return($this->id);
	}
}

	$entidad =& new desplazamientos();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Desplazamientos");
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
			FROM rec.movimientos mov
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
	$atributo->set("field","hora_inicio");
	$atributo->set("label","Hora Inicio");
	$atributo->set("sqlType","timestamp");
	$atributo->set("inputType","timestamp");
	$atributo->set("mandatory",FALSE);
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
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","numero_viaje");
	$atributo->set("label","Número viaje");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","km");
	$atributo->set("label","Km");
	$atributo->set("sqlType","float");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	
	$atributo=new attribute($entidad);
	$atributo->set("field","horometro");
	$atributo->set("label","Horómetro");
	$atributo->set("sqlType","float");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","orden_micro");
	$atributo->set("label","Orden_micro");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",FALSE);
	$atributo->set("searchable",FALSE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);


	$entidad->checkSqlStructure();

?>
