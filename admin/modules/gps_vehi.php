<?

require($CFG->objectPath . "/object.php");

class m_gps extends entity
{
	function find()
	{
		global $ME;

		$condicionAnterior = "";
		{
			$user=$_SESSION[$this->CFG->sesion]["user"];
			$condicionAnterior = " id_vehi in (select idgps::bigint from vehiculos where id_centro in (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$this->CFG->sesion]["user"]["id"]."'))";
		}
		parent::find($condicionAnterior);
	}
}

	$entidad =& new m_gps();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Vehículos");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","id");
	$entidad->set("orderByMode","DESC");

	include("style.php");
	$entidad->set("formColumns",1);
	$entidad->set("btnAdd",FALSE);
	$entidad->set("btnEdit",FALSE);
	$entidad->set("btnDelete",FALSE);

// ---------- Vinculos a muchos  ----------------
// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_vehi");
	$atributo->set("label","Móvil");
	$atributo->set("sqlType","bigint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","tiempo");
	$atributo->set("label","Hora");
	$atributo->set("sqlType","timestamp");
	$atributo->set("visible",TRUE);
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("searchableRange",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","rumbo");
	$atributo->set("label","rumbo");
	$atributo->set("sqlType","smallint");
	$atributo->set("visible",TRUE);
	$atributo->set("editable",FALSE);
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","velocidad");
	$atributo->set("label","Velocidad");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("browseable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","gps_geom");
	$atributo->set("label","Posición");
	$atributo->set("sqlType","geometry");
	$atributo->set("geometryType","POINT");
	$atributo->set("geometrySRID",4326);
	$atributo->set("searchable",FALSE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$atributo->set("visible",TRUE);
	$atributo->set("editable",'READONLY');
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","satelites");
	$atributo->set("label","Satélites");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("browseable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","evento");
	$atributo->set("label","Evento");
	$atributo->set("sqlType","smallint");

	$atributo->set("inputType","select");
	$atributo->set("foreignTable","eventos");
	$atributo->set("foreignField","codigo");

	$atributo->set("mandatory",FALSE);
	$atributo->set("browseable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","hrposition");//Human-Readable Position
	$atributo->set("label","Posición");
	$atributo->set("sqlType","character varying(128)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",FALSE);
	$atributo->set("browseable",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

/*	********	*/

	$entidad->checkSqlStructure(FALSE);

	
?>
