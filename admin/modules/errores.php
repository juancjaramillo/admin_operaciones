<?

	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Errores");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","id");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}


// ---------- Vinculos a muchos  ----------------


// ---------- ATRIBUTOS          ----------------


if((!isset($_GET["mode"]) && !isset($_POST["mode"])) ||  (isset($_GET["mode"]) && ($_GET["mode"]=="" || $_GET["mode"]=="buscar" || $_GET["mode"]=="consultar"))){
	$atributo=$entidad->getAttributeByName("id");
	$atributo->set("label","id");
	$atributo->set("editable",TRUE);
	$atributo->set("visible",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
}

	$atributo=new attribute($entidad);
	$atributo->set("field","nombre");
	$atributo->set("label","Nombre");
	$atributo->set("sqlType","character varying(128)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	
	$entidad->checkSqlStructure();

?>
 
