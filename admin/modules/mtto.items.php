<?
	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Ítems");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","mtto.grupos.nombre,orden");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}


// ---------- Vinculos a muchos  ----------------

$link=new link($entidad);
$link->set("name","mtto.inspecciones_items");
$link->set("url",$ME . "?module=mtto.inspecciones_items");
$link->set("icon","icon-settings.gif");
$link->set("description","Inspecciones");
$link->set("field","id_item");
$link->set("type","iframe");
$link->set("relatedTable","mtto.inspecciones_items");
$link->set("popup",true);
$entidad->addLink($link);


// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_grupo");
	$atributo->set("label","Grupo");
	$atributo->set("inputType","recursiveSelect");
	$atributo->set("parentIdLabel","mtto.grupos.nombre");
	$atributo->set("parentTable","mtto.grupos");
	$atributo->set("useGetPath","TRUE");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","orden");
	$atributo->set("label","Orden");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","texto");
	$atributo->set("label","Texto");
	$atributo->set("sqlType","text");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	
	$entidad->checkSqlStructure();
?>
