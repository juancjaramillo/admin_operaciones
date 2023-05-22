<?
require($CFG->objectPath . "/object.php");

class peajes_vigencias extends entity
{
	function find()
	{
		global $ME;

		$condicionAnterior = "";
		$user=$_SESSION[$this->CFG->sesion]["user"];
		$condicionAnterior = "peajes_vigencias.id_peaje IN (SELECT peajes.id FROM peajes WHERE peajes.id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='$user[id]'))";
		parent::find($condicionAnterior);
	}
}




	$entidad =& new peajes_vigencias();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Vigencias");
	$entidad->set("table",$entidad->get("name"));

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
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
			WHERE p.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_tipo_vehiculo");
	$atributo->set("label","Tipo Vehículo");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","tipos_vehiculos");
	$atributo->set("foreignLabelFields","tipo");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","precio");
	$atributo->set("label","Precio");
	$atributo->set("sqlType","int");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","inicio_vigencia");
	$atributo->set("label","Vigencia Desde");
	$atributo->set("sqlType","date");
	$atributo->set("defaultValue",date("Y-m-d"));
	$atributo->set("inputType","date");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","fin_vigencia");
	$atributo->set("label","Vigencia Fin");
	$atributo->set("sqlType","date");
	$atributo->set("defaultValue",date("Y-m-d"));
	$atributo->set("inputType","date");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

		
	$entidad->checkSqlStructure();

?>
