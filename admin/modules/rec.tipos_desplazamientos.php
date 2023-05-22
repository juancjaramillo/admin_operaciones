<?
	require($CFG->objectPath . "/object.php");

class tipos extends entity
{
	function insert()
	{
		if($this->getAttributeByName("recoge_peso")->get("value") == "t")
			$this->db->sql_query("UPDATE rec.tipos_desplazamientos SET recoge_peso=false");

		if($this->getAttributeByName("descarga_peso")->get("value") == "t")
			$this->db->sql_query("UPDATE rec.tipos_desplazamientos SET descarga_peso=false");

		$this->id = parent::insert();
		return($this->id);
	}

	function update()
	{
		if($this->getAttributeByName("recoge_peso")->get("value") == "t")
			$this->db->sql_query("UPDATE rec.tipos_desplazamientos SET recoge_peso=false");

		if($this->getAttributeByName("descarga_peso")->get("value") == "t")
			$this->db->sql_query("UPDATE rec.tipos_desplazamientos SET descarga_peso=false");

		parent::update();
	}


	function delete()
	{
		$num = $this->db->sql_row("SELECT count(*) as num FROM rec.desplazamientos WHERE id_tipo_desplazamiento=".$this->id);
		if($num["num"] != 0)
		{
			echo "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.outerHeight=200;\nwindow.outerWidth=300;\n</script>\n";
			echo "No se puede borrar, porque tiene elementos relacionados.<br><br>\n";
			echo "<input type=\"button\" onClick=\"window.close();\" value=\"Cerrar\">";
			die();
		}

		parent::delete();
	}

}


	$entidad =& new tipos();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Tipos Desplazamientos");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","orden");

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
	$atributo->set("field","tipo");
	$atributo->set("label","Tipo");
	$atributo->set("sqlType","varchar(255)");
	$atributo->set("mandatory",TRUE);
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
	$atributo->set("field","recoge_peso");
	$atributo->set("label","¿Recoge peso?");
	$atributo->set("inputType","option");
	$atributo->set("sqlType","boolean");
	$atributo->set("defaultValue","false");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","descarga_peso");
	$atributo->set("label","¿Descarga peso?");
	$atributo->set("inputType","option");
	$atributo->set("sqlType","boolean");
	$atributo->set("defaultValue","false");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","informe_tiempos_operacion_tiempos");
	$atributo->set("label","Informe Tiempos de Operacion (tiempos)");
	$atributo->set("inputType","option");
	$atributo->set("sqlType","boolean");
	$atributo->set("defaultValue","false");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","informe_tiempos_operacion_horas_inicio");
	$atributo->set("label","Informe Tiempos de Operacion (horas inicio)");
	$atributo->set("inputType","option");
	$atributo->set("sqlType","boolean");
	$atributo->set("defaultValue","false");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","informe_tiempos_operacion_horas_final");
	$atributo->set("label","Informe Tiempos de Operacion (horas final)");
	$atributo->set("inputType","option");
	$atributo->set("sqlType","boolean");
	$atributo->set("defaultValue","false");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$entidad->checkSqlStructure();

?>
 
