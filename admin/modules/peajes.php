<?
require($CFG->objectPath . "/object.php");

class personas extends entity
{
	function find()
	{
		global $ME;

		$condicionAnterior = "";
		$user=$_SESSION[$this->CFG->sesion]["user"];
		$condicionAnterior = " peajes.id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='$user[id]')";
		parent::find($condicionAnterior);
	}
}

	$entidad =& new personas();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Peajes");
	$entidad->set("table",$entidad->get("name"));

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------

	if(preg_match("/admin/",$ME,$match)){

		$link=new link($entidad);
		$link->set("name","peajes_vigencias");
		$link->set("url",$ME . "?module=peajes_vigencias");
		$link->set("icon","calendar_16.gif");
		$link->set("description","Vigencias");
		$link->set("field","id_peaje");
		$link->set("type","iframe");
		$link->set("relatedTable","peajes_vigencias");
		$link->set("popup",true);
		$entidad->addLink($link);

		$link=new link($entidad);
		$link->set("name","peajes_micros");
		$link->set("url",$ME . "?module=peajes_micros");
		$link->set("icon","bug2.gif");
		$link->set("description","Micros");
		$link->set("field","id_peaje");
		$link->set("type","iframe");
		$link->set("relatedTable","peajes_micros");
		$link->set("popup",true);
		$entidad->addLink($link);

	}


// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_centro");
	$atributo->set("label","Centro");
	$atributo->set("inputType","querySelect");
	$atributo->set("qsQuery","SELECT id, centro as nombre
			FROM centros 
			WHERE id IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')");
	$atributo->set("onChange","updateRecursive_id_lugar_descargue(this)");
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
	$atributo->set("sqlType","character varying(125)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	

	$atributo=new attribute($entidad);
	$atributo->set("field","veces");
	$atributo->set("label","Num Veces");
	$atributo->set("sqlType","int");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_lugar_descargue");
	$atributo->set("label","Sitio que recibe");
	$atributo->set("foreignTable","lugares_descargue");
	$atributo->set("foreignTableAlias","p");
	$atributo->set("foreignLabelFields","p.nombre");
	$atributo->set("sqlType","smallint");
	$atributo->set("inputType","select_dependiente");
	$atributo->set("fieldIdParent","id_centro");
	$atributo->set("namediv","id_lugar_descargue");
	$queryACargar = "SELECT p.id, p.nombre
	FROM lugares_descargue p
	WHERE p.id_centro='__%idARemp%__'
	ORDER BY p.nombre";
	$atributo->set("qsQuery",$queryACargar);
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","sentido");
	$atributo->set("label","Sentido");
	$atributo->set("sqlType","varchar(16)");
	$atributo->set("inputType","arraySelect");
	$atributo->set("arrayValues",array("1"=>"Del área de prestación del servicio al sitio de disposición final", "2"=>"Del sitio de disposición final al área de prestación del servicio", "1-2"=>"Los dos anteriores"));
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_clasificacion_peaje");
	$atributo->set("label","Clasificación");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","clasificacion_peajes");
	$atributo->set("foreignLabelFields","clasificacion");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);
	
	
	$atributo=new attribute($entidad);
	$atributo->set("field","the_geom");
	$atributo->set("label","Centroide");
	$atributo->set("sqlType","geometry");
	$atributo->set("inputType","golLocation");
	$atributo->set("geometryType","POINT");
	$atributo->set("geometrySRID",4326);
	$atributo->set("searchable",FALSE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$atributo->set("visible",FALSE);
	$atributo->set("editable",'READONLY');
	$entidad->addAttribute($atributo);
	
	$entidad->checkSqlStructure();

?>
 
