<?
	require($CFG->objectPath . "/object.php");

class cuartelillos extends entity
{
	function find()
	{
		global $ME;

		$condicionAnterior = "";
		//if(!preg_match("/admin/",$ME,$match))
		{
			$user=$_SESSION[$this->CFG->sesion]["user"];
			$condicionAnterior = " cuartelillos.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')";
		}
		parent::find($condicionAnterior);
	}
}


	$entidad =& new cuartelillos();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Cuartelillos");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","nombre");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------


	if(preg_match("/admin/",$ME,$match)){
		$link=new link($entidad);
		$link->set("name","cuartelillos_personas");
		$link->set("url",$ME . "?module=cuartelillos_personas");
		$link->set("icon","grupos.jpeg");
		$link->set("description","Personas");
		$link->set("field","id_cuartelillo");
		$link->set("type","iframe");
		$link->set("relatedTable","cuartelillos_personas");
		$link->set("popup",true);
		$entidad->addLink($link);
	}

// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_centro");
	$atributo->set("label","Centro");
	if(preg_match("/admin/",$ME,$match)){
		$atributo->set("inputType","select");
		$atributo->set("foreignTable","centros");
		$atributo->set("foreignLabelFields","centro");
	}else{
		$atributo->set("inputType","querySelect");
		$atributo->set("qsQuery","
				SELECT centros.id, centros.centro as nombre
				FROM centros
				WHERE centros.id IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')
				ORDER BY centros.centro");
	}
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","nombre");
	$atributo->set("label","Nombre");
	$atributo->set("sqlType","character varying(1055)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","direccion");
	$atributo->set("label","Dirección");
	$atributo->set("sqlType","character varying(1055)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","telefono");
	$atributo->set("label","Teléfono");
	$atributo->set("sqlType","character varying(1055)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);
		
	$atributo=new attribute($entidad);
	$atributo->set("field","geometry");
	$atributo->set("label","Geometria");
	$atributo->set("inputType","geometry");
	$atributo->set("sqlType","point");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);
	
	$entidad->checkSqlStructure();

?>
 
