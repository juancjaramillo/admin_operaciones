<?

	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Comunas");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","comuna");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------

	$link=new link($entidad);
	$link->set("name","barrios");
	$link->set("url",$ME . "?module=barrios");
	$link->set("iconoLetra","B");
	$link->set("description","Barrios");
	$link->set("field","id_comuna");
	$link->set("type","iframe");
	$link->set("relatedTable","barrios");
	$link->set("popup",true);
	$entidad->addLink($link);

// ---------- ATRIBUTOS          ----------------


	$atributo=new attribute($entidad);
	$atributo->set("field","comuna");
	$atributo->set("label","Comuna");
	$atributo->set("sqlType","character varying(1055)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","geometry");
	$atributo->set("label","Geometry");
	$atributo->set("sqlType","geometry");
	$atributo->set("geometryType","POLYGON");
	$atributo->set("geometrySRID",4326);
	$atributo->set("searchable",FALSE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$atributo->set("visible",FALSE);
	$atributo->set("editable",'READONLY');
	$entidad->addAttribute($atributo);

	
	$entidad->checkSqlStructure();

?>
 
