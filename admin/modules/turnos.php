<?
	require($CFG->objectPath . "/object.php");

class turnos extends entity
{
	function find()
	{
		$condicionAnterior=" id_empresa IN (SELECT id_empresa FROM centros WHERE id IN (SELECT id_centro FROM personas_centros WHERE id_persona = '".$_SESSION[$this->CFG->sesion]["user"]["id"]."'))";
		parent::find($condicionAnterior);
	}
}


	$entidad =& new turnos();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Turnos");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","id_empresa,turno");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1 && $_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=13){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------


// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_empresa");
	$atributo->set("label","Empresa");
	$atributo->set("inputType","querySelect");
	$atributo->set("qsQuery","
		SELECT e.id, e.empresa as nombre
		FROM empresas e
		WHERE e.id IN (SELECT id_empresa FROM centros WHERE id IN (SELECT id_centro FROM personas_centros WHERE id_persona = '".$_SESSION[$CFG->sesion]["user"]["id"]."'))");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","turno");
	$atributo->set("label","Turno");
	$atributo->set("sqlType","character varying(255)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	

	$atributo=new attribute($entidad);
	$atributo->set("field","hora_inicio");
	$atributo->set("label","Hora Inicio");
	$atributo->set("sqlType","time");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	
	$entidad->checkSqlStructure();

?>
 
