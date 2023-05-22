<?
	require($CFG->objectPath . "/object.php");

class costos extends entity
{
	function find()
	{
		global $ME;

		$condicionAnterior = "";
		$user=$_SESSION[$this->CFG->sesion]["user"];
		$condicionAnterior = "costos.id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='$user[id]')";
		parent::find($condicionAnterior);
	}
}


	$entidad =& new costos();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","costos");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","fecha DESC");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
//		$entidad->set("btnAdd",FALSE);
//		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------


// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_centro");
	$atributo->set("label","Centro");
	$atributo->set("inputType","querySelect");
	$atributo->set("qsQuery","
			SELECT centros.id, centros.centro as nombre
			FROM centros
			WHERE centros.id IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')
			ORDER BY centros.centro");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_servicio");
	$atributo->set("label","Servicio");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","servicios");
	$atributo->set("foreignLabelFields","servicio");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$atributo->set("sortableYahoo",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_variable_informe");
	$atributo->set("label","Variable");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","variables_informes");
	$atributo->set("foreignLabelFields","variable");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$atributo->set("sortableYahoo",TRUE);
	$entidad->addAttribute($atributo);




	$atributo=new attribute($entidad);
	$atributo->set("field","fecha");
	$atributo->set("label","Fecha (YYYY-MM)");
	$atributo->set("sqlType","character varying(7)");
	$atributo->set("defaultValue",date("Y-m"));
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	
	$atributo=new attribute($entidad);
	$atributo->set("field","valor");
	$atributo->set("label","Valor");
	$atributo->set("sqlType","real");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	
	$entidad->checkSqlStructure();
?>
