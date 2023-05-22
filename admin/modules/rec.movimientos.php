<?
	require($CFG->objectPath . "/object.php");
	
class movimientos extends entity
{
	function insert()
	{
		$this->id = parent::insert();

		//sacar el día
		$dia = strftime("%u",strtotime($this->getAttributeByName("inicio")->get("value")));
		//seleccrionar la frecuencia que se está insertando
		$qid = $this->db->sql_query("SELECT * FROM micros_frecuencia WHERE id_micro='".$this->getAttributeByName("id_micro")->get("value")."' AND dia='".$dia."'");
		if($this->db->sql_numrows($qid) != 0)
		{
			$frec = $this->db->sql_fetchrow($qid);

			//operarios
			$this->db->sql_query("INSERT INTO rec.movimientos_personas (id_movimiento, cargo, id_persona, hora_inicio) SELECT '".$this->id."', id_cargo, id_persona, '".$this->getAttributeByName("inicio")->get("value")."' FROM frecuencias_operarios WHERE id_frecuencia=".$frec["id"]);

			//desplazamientos
		$this->db->sql_query("INSERT INTO rec.desplazamientos (id_movimiento, id_tipo_desplazamiento, numero_viaje, orden_micro) SELECT '".$this->id."', id_tipo_desplazamiento, 1, orden FROM frecuencias_desplazamientos WHERE id_frecuencia=".$frec["id"]." ORDER BY orden");
		}

		$this->db->sql_query("INSERT INTO rec.movimientos_peajes (id_movimiento, id_peaje, veces) SELECT '".$this->id."', id_peaje, 1 FROM peajes_micros WHERE id_micro = '".$this->getAttributeByName("id_micro")->get("value")."'");

		ingresarLogMovimiento("rec",$this->id,"Insertó el movimiento");

		return $this->id;
	}

	function find()
	{
		$condicionAnterior = " id_micro IN (SELECT m.id FROM micros m LEFT JOIN ases a ON a.id=m.id_ase WHERE a.id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".$_SESSION[$this->CFG->sesion]["user"]["id"]."')) ";
		parent::find($condicionAnterior);
	}
}




	$entidad =& new movimientos();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Movimientos Recolección");
	$entidad->set("table",$entidad->get("name"));

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------

	$link=new link($entidad);
	$link->set("name","rec.movimientos_personas");
	$link->set("url",$ME . "?module=rec.movimientos_personas");
	$link->set("icon","grupo.jpeg");
	$link->set("description","Personas");
	$link->set("field","id_movimiento");
	$link->set("type","iframe");
	$link->set("relatedTable","rec.movimientos_personas");
	$link->set("popup",true);
	$entidad->addLink($link);

	$link=new link($entidad);
	$link->set("name","rec.movimientos_pesos");
	$link->set("url",$ME . "?module=rec.movimientos_pesos");
	$link->set("icon","balance.gif");
	$link->set("description","Pesos");
	$link->set("field","id_movimiento");
	$link->set("type","iframe");
	$link->set("relatedTable","rec.movimientos_pesos");
	$link->set("popup",true);
	$entidad->addLink($link);


	$link=new link($entidad);
	$link->set("name","rec.desplazamientos");
	$link->set("url",$ME . "?module=rec.desplazamientos");
	$link->set("icon","icon-route.png");
	$link->set("description","Desplazamientos");
	$link->set("field","id_movimiento");
	$link->set("type","iframe");
	$link->set("relatedTable","rec.desplazamientos");
	$link->set("popup",true);
	$entidad->addLink($link);


	
	$link=new link($entidad);
	$link->set("name","rec.movimientos_clientes");
	$link->set("url",$ME . "?module=rec.movimientos_clientes");
	$link->set("icon","grupo.jpeg");
	$link->set("description","Clientes");
	$link->set("field","id_movimiento");
	$link->set("type","iframe");
	$link->set("relatedTable","rec.movimientos_clientes");
	$link->set("popup",true);
	$entidad->addLink($link);


// ---------- ATRIBUTOS          ----------------


	$atributo=new attribute($entidad);
	$atributo->set("field","id_micro");
	$atributo->set("label","Micro");
	$atributo->set("sqlType","integer");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","micros");
	$atributo->set("foreignLabelFields","micros.codigo");
	$atributo->set("foreignTableFilter"," id IN (SELECT m.id FROM micros m LEFT JOIN ases a ON a.id=m.id_ase WHERE a.id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')) ");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


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
	$atributo->set("field","inicio");
	$atributo->set("label","Fecha Inicio");
	$atributo->set("sqlType","timestamp");
	$atributo->set("defaultValue",date("Y-m-d H:i:s"));
	$atributo->set("searchableRange",TRUE);
	$atributo->set("inputType","timestamp");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","final");
	$atributo->set("label","Fecha Final");
	$atributo->set("sqlType","timestamp");
	$atributo->set("searchableRange",TRUE);
	$atributo->set("inputType","timestamp");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","km_final");
	$atributo->set("label","Km Final");
	$atributo->set("sqlType","float");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","horometro_final");
	$atributo->set("label","Horómetro Final");
	$atributo->set("sqlType","float");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","combustible");
	$atributo->set("label","Combustible");
	$atributo->set("sqlType","float");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","km_tanqueo");
	$atributo->set("label","Km tanqueo");
	$atributo->set("sqlType","float");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","numero_orden");
	$atributo->set("label","Orden No.");
	$atributo->set("sqlType","varchar(16)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_turno");
	$atributo->set("label","Turno");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","id_persona_cerro");
	$atributo->set("label","Persona Cerró");
	$atributo->set("sqlType","integer");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","personas");
	$atributo->set("foreignLabelFields","personas.nombre||' '||personas.apellido");
	$atributo->set("foreignTableFilter"," id IN (SELECT id_persona FROM personas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')) ");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","log");
	$atributo->set("label","Log");
	$atributo->set("sqlType","text");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);


	/*   para que no se pueda incluir o editar más peso al movimiento */

	$atributo=new attribute($entidad);
	$atributo->set("field","peso_cerrado");
	$atributo->set("label","¿Cerrado?");
	$atributo->set("inputType","option");
	$atributo->set("sqlType","boolean");
	$atributo->set("defaultValue","0");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",FALSE);
	$atributo->set("searchable",FALSE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$atributo->set("visible",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_persona_cerro_peso");
	$atributo->set("label","Persona Cerró");
	$atributo->set("sqlType","integer");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","personas");
	$atributo->set("foreignLabelFields","personas.nombre||' '||personas.apellido");
	$atributo->set("foreignTableFilter"," id IN (SELECT id_persona FROM personas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')) ");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",FALSE);
	$atributo->set("searchable",FALSE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$atributo->set("visible",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","fecha_cerro_peso");
	$atributo->set("label","Fecha Cerro");
	$atributo->set("sqlType","timestamp");
	$atributo->set("searchableRange",TRUE);
	$atributo->set("inputType","timestamp");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",FALSE);
	$atributo->set("searchable",FALSE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$atributo->set("visible",FALSE);
	$entidad->addAttribute($atributo);



	$entidad->checkSqlStructure();

?>