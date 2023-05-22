<?
	require($CFG->objectPath . "/object.php");

class ases extends entity
{
	function find()
	{
		global $ME;

		$condicionAnterior = "";
		$user=$_SESSION[$this->CFG->sesion]["user"];
		$condicionAnterior = "ases.id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='$user[id]')";
		parent::find($condicionAnterior);
	}
}


	$entidad =& new ases();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Ases");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","ase");

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
	$atributo->set("field","ase");
	$atributo->set("label","Ase");
	$atributo->set("sqlType","character varying(32)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	
	$atributo=new attribute($entidad);
	$atributo->set("field","nuap");
	$atributo->set("label","NUAP");
	$atributo->set("sqlType","int");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","fecha_entrada");
	$atributo->set("label","Fecha Entrada Operación");
	$atributo->set("sqlType","date");
	$atributo->set("defaultValue",date("Y-m-d"));
	$atributo->set("inputType","date");
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
 
