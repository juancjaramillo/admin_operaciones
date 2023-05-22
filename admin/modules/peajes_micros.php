<?
require($CFG->objectPath . "/object.php");

class peajes_micros extends entity
{
	function find()
	{
		global $ME;

		$condicionAnterior = "";
		$user=$_SESSION[$this->CFG->sesion]["user"];
		$condicionAnterior = "peajes_micros.id_peaje IN (SELECT peajes.id FROM peajes WHERE peajes.id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='$user[id]'))";
		parent::find($condicionAnterior);
	}
}

	$entidad =& new peajes_micros();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Peajes / Rutas");
	$entidad->set("table",$entidad->get("name"));

	include("style.php");
	$entidad->set("formColumns",1);
	if(!in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_peajes_micros_opcion_eliminar"])){
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------

// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_peaje");
	$atributo->set("label","Peaje");
	$atributo->set("inputType","querySelect");
	$atributo->set("qsQuery","SELECT p.id, p.nombre||'/'||c.centro as nombre
			FROM peajes p
			LEFT JOIN centros c ON c.id=p.id_centro
			WHERE p.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')
			ORDER BY p.nombre, c,centro");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","id_micro");
	$atributo->set("label","Ruta");
	$atributo->set("inputType","querySelect");
	$atributo->set("qsQuery","SELECT m.id, m.codigo ||'/'||s.servicio as nombre
			FROM micros m
			LEFT JOIN servicios s ON s.id=m.id_servicio
			LEFT JOIN ases a ON a.id=m.id_ase
			WHERE a.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

		
	$entidad->checkSqlStructure();

?>
