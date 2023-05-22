<?
	require($CFG->objectPath . "/object.php");

class empresas extends entity
{
	function find()
	{
		$condicionAnterior=" empresas.id IN (SELECT id_empresa FROM centros WHERE id IN (SELECT id_centro FROM personas_centros WHERE id_persona = '".$_SESSION[$this->CFG->sesion]["user"]["id"]."'))";
		parent::find($condicionAnterior);
	}
}

	$entidad =& new empresas();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Empresas");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","empresa");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------

	$link=new link($entidad);
	$link->set("name","centros");
	$link->set("url",$ME . "?module=centros");
	$link->set("iconoLetra","C");
	$link->set("description","Centros");
	$link->set("field","id_empresa");
	$link->set("relatedTable","centros");
	$entidad->addLink($link);




// ---------- ATRIBUTOS          ----------------


	$atributo=new attribute($entidad);
	$atributo->set("field","empresa");
	$atributo->set("label","Empresa");
	$atributo->set("sqlType","character varying(255)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	
	$atributo=new attribute($entidad);
	$atributo->set("field","nit");
	$atributo->set("label","NIT");
	$atributo->set("sqlType","character varying(20)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","direccion");
	$atributo->set("label","Dirección");
	$atributo->set("sqlType","character varying(125)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","telefono");
	$atributo->set("label","Teléfono");
	$atributo->set("sqlType","character varying(125)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","codigo_sui");
	$atributo->set("label","Código SUI");
	$atributo->set("sqlType","character varying(125)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	
	$entidad->checkSqlStructure();

?>
