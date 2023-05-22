<?
	require($CFG->objectPath . "/object.php");

class personas extends entity
{
	function find()
	{
		global $ME;

		$condicionAnterior = "";
		//if(!preg_match("/admin/",$ME,$match))
		{
			$user=$_SESSION[$this->CFG->sesion]["user"];
			$condicionAnterior = " cuartelillos_personas.id_cuartelillo IN (SELECT id FROM cuartelillos WHERE cuartelillos.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]'))";
		}
		parent::find($condicionAnterior);
	}
}


	$entidad =& new personas();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Personas");
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
	$atributo->set("field","id_cuartelillo");
	$atributo->set("label","Cuartelillo");
	/*
	if(preg_match("/admin/",$ME,$match)){
		$atributo->set("inputType","select");
		$atributo->set("foreignTable","cuartelillos");
		$atributo->set("foreignLabelFields","cuartelillos.nombre");
	}else
	*/
	{
		$atributo->set("inputType","querySelect");
		$atributo->set("qsQuery","
				SELECT cuartelillos.id, cuartelillos.nombre
				FROM cuartelillos
				WHERE cuartelillos.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')
				ORDER BY cuartelillos.nombre");
	}
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","id_persona");
	$atributo->set("label","Personas");
	/*
	if(preg_match("/admin/",$ME,$match)){
		$atributo->set("inputType","select");
		$atributo->set("foreignTable","personas");
		$atributo->set("foreignLabelFields","personas.nombre||' '||personas.apellido");
	}else
	*/
	{
		$atributo->set("inputType","querySelect");
		$atributo->set("qsQuery","
				SELECT personas.id, personas.nombre||' '||personas.apellido as nombre
				FROM personas
				LEFT JOIN personas_centros ON personas_centros.id_persona=personas.id
				WHERE personas_centros.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')");
	}
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","id_turno");
	$atributo->set("label","Turno");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","turnos");
	$atributo->set("foreignLabelFields","turnos.turno");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$entidad->checkSqlStructure();

?>
 
