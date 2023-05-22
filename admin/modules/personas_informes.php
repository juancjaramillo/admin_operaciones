<?

	require($CFG->objectPath . "/object.php");

class per_informes extends entity
{
	function find()
	{
		global $ME;
		$condicionAnterior = "true";
		
		$condicionAnterior = "id_persona IN (SELECT id_persona FROM personas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona = '".$_SESSION[$this->CFG->sesion]["user"]["id"]."'))";
				
		parent::find($condicionAnterior);
	}
}

	$entidad =& new per_informes();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Personas / Informes");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","id_informe");

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
	$atributo->set("foreignLabelFields","nombre||' '||apellido");
	$atributo->set("foreignTableFilter","id IN (SELECT id_persona FROM personas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona = '".$_SESSION[$CFG->sesion]["user"]["id"]."'))");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_informe");
	$atributo->set("label","Informe");
	$atributo->set("inputType","querySelect");
	$atributo->set("qsQuery","
			SELECT informes.id, categorias_informes.nombre||'/'||informes.informe as nombre
			FROM informes
			LEFT JOIN categorias_informes ON categorias_informes.id=informes.id_categoria_informe
			ORDER BY categorias_informes.nombre, informes.informe");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","primera");
	$atributo->set("label","¿Primera Página?");
	$atributo->set("inputType","option");
	$atributo->set("sqlType","boolean");
	$atributo->set("defaultValue","f");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	
	$entidad->checkSqlStructure();

?>
 
