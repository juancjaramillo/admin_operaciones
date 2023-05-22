<?

require($CFG->objectPath . "/object.php");

class horarios extends entity
{
	function find()
	{
		global $ME;

		$condicionAnterior = "";
		{
			$user=$_SESSION[$this->CFG->sesion]["user"];
			$condicionAnterior = " id_vehiculo in (select id from vehiculos where id_centro in (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$this->CFG->sesion]["user"]["id"]."'))";
		}
		parent::find($condicionAnterior);
	}
}

	$entidad =& new horarios();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Vehículos / Horarios Laborables");
	$entidad->set("table",$entidad->get("name"));
	//$entidad->set("orderBy","id");
	//$entidad->set("orderByMode","DESC");

	include("style.php");
	$entidad->set("formColumns",1);
	//$entidad->set("btnAdd",FALSE);
	//$entidad->set("btnEdit",FALSE);
	//$entidad->set("btnDelete",FALSE);

// ---------- Vinculos a muchos  ----------------
// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_vehiculo");
	$atributo->set("label","Vehículo");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","vehiculos");
	$atributo->set("foreignLabelFields","vehiculos.codigo||'/'||vehiculos.placa");
	$atributo->set("foreignTableFilter"," id IN (SELECT id FROM vehiculos WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')) ");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","dia");
	$atributo->set("label","Día");
	$atributo->set("sqlType","smallint");
	$atributo->set("inputType","arraySelect");
	$atributo->set("arrayValues",array("1"=>"Lunes", "2"=>"Martes","3"=>"Miércoles","4"=>"Jueves","5"=>"Viernes","6"=>"Sábado","7"=>"Domingo"));
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	
	$atributo=new attribute($entidad);
	$atributo->set("field","hora_inicio");
	$atributo->set("label","Hora Inicio");
	$atributo->set("sqlType","time");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","hora_final");
	$atributo->set("label","Hora Final");
	$atributo->set("sqlType","time");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$entidad->checkSqlStructure(FALSE);

	
?>