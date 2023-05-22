<?
require($CFG->objectPath . "/object.php");

class movimientos extends entity
{
	function insert()
	{
		$this->id = parent::insert();

		/*
		//sacar el día
		$dia = strftime("%u",strtotime($this->getAttributeByName("inicio")->get("value")));
		//seleccrionar la frecuencia que se está insertando
		$qid = $this->db->sql_query("SELECT * FROM micros_frecuencia WHERE id_micro='".$this->getAttributeByName("id_micro")->get("value")."' AND dia='".$dia."'");
		if($this->db->sql_numrows($qid) != 0)
		{
			$frec = $this->db->sql_fetchrow($qid);

			//operarios
			$this->db->sql_query("INSERT INTO bar.movimientos_personas (id_movimiento, id_persona, hora_inicio) SELECT '".$this->id."', id_persona, '".$this->getAttributeByName("inicio")->get("value")."' FROM frecuencias_operarios WHERE id_frecuencia=".$frec["id"]);

			//bolsas
			$this->db->sql_query("INSERT INTO bar.movimientos_bolsas (id_movimiento, id_tipo_bolsa, numero_inicio) SELECT '".$this->id."', id_tipo_bolsa, numero_inicio FROM frecuencias_bolsas WHERE id_frecuencia=".$frec["id"]);
		}
		*/

		return $this->id;
	}

	function find()
	{
		$condicionAnterior = " id_micro IN (SELECT m.id FROM micros m LEFT JOIN ases a ON a.id=m.id_ase WHERE a.id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".$_SESSION[$this->CFG->sesion]["user"]["id"]."')) ";
		parent::find($condicionAnterior);
	}
}

	$entidad =& new movimientos();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Movimientos Barrido");
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
	$link->set("name","bar.movimientos_bolsas");
	$link->set("url",$ME . "?module=bar.movimientos_bolsas");
	$link->set("icon","bolsa.jpeg");
	$link->set("description","Tipo Proyecto");
	$link->set("field","id_movimiento");
	$link->set("type","iframe");
	$link->set("relatedTable","bar.movimientos_bolsas");
	$link->set("popup",true);
	$entidad->addLink($link);

	$link=new link($entidad);
	$link->set("name","bar.movimientos_personas");
	$link->set("url",$ME . "?module=bar.movimientos_personas");
	$link->set("icon","grupo.jpeg");
	$link->set("description","Personas");
	$link->set("field","id_movimiento");
	$link->set("type","iframe");
	$link->set("relatedTable","bar.movimientos_bolsas");
	$link->set("popup",true);
	$entidad->addLink($link);


// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_micro");
	$atributo->set("label","Micro");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","micros");
	$atributo->set("foreignLabelFields","micros.codigo");
	$atributo->set("foreignTableFilter"," id IN (SELECT m.id FROM micros m LEFT JOIN ases a ON a.id=m.id_ase WHERE a.id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')) ");
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
	$atributo->set("foreignTableFilter"," id IN (SELECT id FROM vehiculos WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')) ");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","inicio");
	$atributo->set("label","Fecha Inicio");
	$atributo->set("sqlType","timestamp");
	$atributo->set("defaultValue",date("Y-m-d H:i:s"));
	$atributo->set("searchableRange",TRUE);
	$atributo->set("inputType","timestamp");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","final");
	$atributo->set("label","Fecha Final");
	$atributo->set("sqlType","timestamp");
	$atributo->set("searchableRange",TRUE);
	$atributo->set("inputType","timestamp");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","km_final");
	$atributo->set("label","Km Final");
	$atributo->set("sqlType","float");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","horometro_final");
	$atributo->set("label","Horómetro Final");
	$atributo->set("sqlType","float");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","numero_orden");
	$atributo->set("label","Orden No.");
	$atributo->set("sqlType","varchar(16)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_turno");
	$atributo->set("label","Turno");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_persona_cerro");
	$atributo->set("label","Persona Cerró");
	$atributo->set("sqlType","integer");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","personas");
	$atributo->set("foreignLabelFields","personas.nombre||' '||personas.apellido");
	$atributo->set("foreignTableFilter"," id IN (SELECT id_persona FROM personas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')) ");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);	

	$atributo=new attribute($entidad);
	$atributo->set("field","log");
	$atributo->set("label","Log");
	$atributo->set("sqlType","text");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);


	$entidad->checkSqlStructure();

?>
