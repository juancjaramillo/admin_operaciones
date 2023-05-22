<?
require($CFG->objectPath . "/object.php");

class per_cargos extends entity
{
	function insert()
	{
		$this->id = parent::insert();
		$this->actualizarPersonas();
		return($this->id);
	}

	function update()
	{
		parent::update();
		$this->actualizarPersonas();
	}

	function delete()
	{
		parent::delete();
		$this->actualizarPersonas();
	}

	function actualizarPersonas()
	{
		$cons="SELECT * FROM ".$this->table." WHERE id_persona=".$this->getAttributeByName("id_persona")->get("value")." AND fecha_fin IS NULL ORDER BY fecha_inicio DESC LIMIT 1";
		$qid = $this->db->sql_query($cons);
		if($this->db->sql_numrows($qid)==0)
			$this->db->sql_query("UPDATE personas SET id_cargo = null WHERE id='".$this->getAttributeByName("id_persona")->get("value")."'");
		else
		{
			$query = $this->db->sql_fetchrow($qid);
			$this->db->sql_query("UPDATE personas SET id_cargo = '".$query["id_cargo"]."' WHERE id='".$this->getAttributeByName("id_persona")->get("value")."'");
		}
	}


	function find()
	{
		global $ME;
		$condicionAnterior = "true";
		
		$condicionAnterior = "id_persona IN (SELECT id_persona FROM personas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona = '".$_SESSION[$this->CFG->sesion]["user"]["id"]."'))";
				
		parent::find($condicionAnterior);
	}


}


	$entidad =& new per_cargos();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Cargos");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","fecha_inicio desc, fecha_fin desc");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1 && $_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=13 && $_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=6){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------


// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_persona");
	$atributo->set("label","Persona");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","personas");
	$atributo->set("foreignLabelFields","personas.nombre||' '||personas.apellido");
	$atributo->set("foreignTableFilter","id IN (SELECT id_persona FROM personas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona = '".$_SESSION[$CFG->sesion]["user"]["id"]."'))");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","id_cargo");
	$atributo->set("label","Cargo");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","cargos");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","fecha_inicio");
	$atributo->set("label","Inicio");
	$atributo->set("sqlType","date");
	$atributo->set("defaultValue",date("Y-m-d"));
	$atributo->set("inputType","date");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	

	$atributo=new attribute($entidad);
	$atributo->set("field","fecha_fin");
	$atributo->set("label","Fin");
	$atributo->set("sqlType","date");
	$atributo->set("inputType","date");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$entidad->checkSqlStructure();

?>
