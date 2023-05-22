<?
	require($CFG->objectPath . "/object.php");

class inspecciones extends entity
{
	function insert()
	{
		$this->id = parent::insert();
		
		$grupo = $this->db->sql_row("SELECT id_grupo FROM vehiculos WHERE id=".$this->getAttributeByName("id_vehiculo")->get("value"));
		$idsGrupos[] = $grupo["id_grupo"];
		obtenerIdsGrupos($grupo["id_grupo"],$idsGrupos);

		$qid = $this->db->sql_query("SELECT id FROM mtto.items WHERE id_grupo IN (".implode(",",$idsGrupos).") ORDER BY id_grupo, orden");	
		while($query = $this->db->sql_fetchrow($qid))
		{
			$this->db->sql_query("INSERT INTO mtto.inspecciones_items (id_inspeccion, id_item, hecha) VALUES ('".$this->id."', '".$query["id"]."',false)");
		}
	
		return ($this->id);
	}
}


	$entidad =& new inspecciones();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Inspecciones");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","fecha DESC");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}


// ---------- Vinculos a muchos  ----------------

	$link=new link($entidad);
	$link->set("name","Ítems");
	$link->set("url",$ME . "?module=mtto.inspecciones_items");
	$link->set("icon","bug2.gif");
	$link->set("description","Ítems");
	$link->set("field","id_inspeccion");
	$link->set("relatedTable","mtto.inspecciones_items");
	$link->set("type","iframe");
	$link->set("popup",true);
	$entidad->addLink($link);
	
// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","fecha");
	$atributo->set("label","Fecha");
	$atributo->set("sqlType","timestamp");
	$atributo->set("defaultValue",date("Y-m-d H:i:s"));
	$atributo->set("inputType","timestamp");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_vehiculo");
	$atributo->set("label","Vehiculo");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","vehiculos");
	$atributo->set("foreignLabelFields","vehiculos.codigo");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_reporto");
	$atributo->set("label","Reportó");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","personas");
	$atributo->set("foreignLabelFields","personas.nombre||' '||personas.apellido");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","observaciones");
	$atributo->set("label","Observaciones");
	$atributo->set("sqlType","text");
	$atributo->set("inputType","textarea");
	$atributo->set("inputSize",40);
	$atributo->set("inputRows",2);
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);


	$entidad->checkSqlStructure();

?>
