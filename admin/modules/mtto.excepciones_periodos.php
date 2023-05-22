<?
require($CFG->objectPath . "/object.php");

class excepciones extends entity
{
	function find()
	{
		$condicionAnterior = " id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".$_SESSION[$this->CFG->sesion]["user"]["id"]."')";
		parent::find($condicionAnterior);
	} 
} 

$entidad =& new excepciones();
$entidad->set("db",$db);

$entidad->set("name",basename(__FILE__, ".php"));
$entidad->set("labelModule","Excepciones Periodos");
$entidad->set("table",$entidad->get("name"));
$entidad->set("orderBy","fecha_inicio");
$entidad->set("HLRows",false);


if(nvl($_GET["mode"]) != "buscar")
{
	$entidad->set("JSComplementaryRevision","
		if(document.entryform.fecha_inicio.value > document.entryform.fecha_final.value){
		window.alert('La fecha inicial no puede ser mayor que la final');
		document.entryform.fecha_inicio.focus();
		return(false);
		}");
}

include("style.php");
$entidad->set("formColumns",1);
/*
if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
	$entidad->set("btnAdd",FALSE);
	$entidad->set("btnEdit",FALSE);
	$entidad->set("btnDelete",FALSE);
}
*/

// ---------- Vinculos a muchos  ----------------


// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_centro");
	$atributo->set("label","Centro");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","centros");
	$atributo->set("foreignLabelFields","centro");
	$atributo->set("foreignTableFilter"," id IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","fecha_inicio");
	$atributo->set("label","Fecha Inicial");
	$atributo->set("sqlType","date");
	$atributo->set("defaultValue",date("Y-m-d"));
	$atributo->set("searchableRange",TRUE);
	$atributo->set("inputType","date");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","fecha_final");
	$atributo->set("label","Fecha Final");
	$atributo->set("sqlType","date");
	$atributo->set("defaultValue",date("Y-m-d"));
	$atributo->set("searchableRange",TRUE);
	$atributo->set("inputType","date");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$entidad->checkSqlStructure();

?>
