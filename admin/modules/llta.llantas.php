<?
	require($CFG->objectPath . "/object.php");

class llantas extends entity
{
	function insert()
	{
		$this->id = parent::insert();
		$this->insertarHistoricoDisenio();
		return($this->id);
	}

	function update()
	{
		$ant = $this->db->sql_row("SELECT disenio, id_tipo_llanta FROM llta.llantas WHERE id=".$this->id);
		if(($ant["disenio"] != $this->getAttributeByName("disenio")->get("value")) || ($ant["id_tipo_llanta"] != $this->getAttributeByName("id_tipo_llanta")->get("value")))
			$this->insertarHistoricoDisenio();

		parent::update();	
	}

	function insertarHistoricoDisenio()
	{
		$consulta = "INSERT INTO llta.llantas_historico_disenio (id_llanta, fecha, disenio, id_tipo_llanta) VALUES ('".$this->id."', now(), '".$this->getAttributeByName("disenio")->get("value")."', '".$this->getAttributeByName("id_tipo_llanta")->get("value")."')";
		$this->db->sql_query($consulta);
	}

}



	$entidad =& new llantas();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Llantas");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","numero");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}


// ---------- Vinculos a muchos  ----------------

	$link=new link($entidad);
	$link->set("name","Movimientos");
	$link->set("url",$ME . "?module=llta.movimientos");
	$link->set("icon","icon-overview.gif");
	$link->set("description","Movimientos");
	$link->set("field","id_llanta");
	$link->set("relatedTable","llta.movimientos");
	$link->set("type","iframe");
	$link->set("popup",true);
	$entidad->addLink($link);
	
	$link=new link($entidad);
	$link->set("name","Histórico Diseños");
	$link->set("url",$ME . "?module=llta.llantas_historico_disenio");
	$link->set("icon","bug2.gif");
	$link->set("description","Histórico Diseños");
	$link->set("field","id_llanta");
//	$link->set("relatedTable","llta.llantas_historico_disenio");
	$link->set("type","iframe");
	$link->set("popup",true);
	$entidad->addLink($link);

// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_centro");
	$atributo->set("label","Centro");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","centros");
	$atributo->set("foreignLabelFields","centro");
	$atributo->set("sqlType","smallint");
	$atributo->set("onChange","updateRecursive_id_proveedor(this)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","numero");
	$atributo->set("label","Número");
	$atributo->set("sqlType","varchar(1055)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_estado");
	$atributo->set("label","Estado");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","llta.estados");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_dimension");
	$atributo->set("label","Marca / Dimensión");
	$atributo->set("sqlType","smallint");
	$atributo->set("inputType","querySelect");
	$atributo->set("qsQuery","
			SELECT ref.id, m.marca||' / '||ref.dimension as nombre
			FROM llta.dimensiones ref
			LEFT JOIN llta.marcas m ON m.id=ref.id_marca
			ORDER BY m.marca,ref.dimension");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","disenio");
	$atributo->set("label","Diseño");
	$atributo->set("sqlType","varchar(1055)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","id_tipo_llanta");
	$atributo->set("label","Tipo");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","llta.tipos_llantas");
	$atributo->set("foreignLabelFields","llta.tipos_llantas.tipo");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","id_proveedor");
	$atributo->set("label","Proveedor");
	$atributo->set("inputType","select_dependiente");
	$atributo->set("foreignTable","(SELECT llta.proveedores.id, llta.proveedores.razon, llta.proveedores_centros.id_centro FROM llta.proveedores_centros LEFT JOIN llta.proveedores ON llta.proveedores_centros.id_proveedor=llta.proveedores.id)");
	$atributo->set("foreignLabelFields","p.razon");
	$atributo->set("foreignTableAlias","p");
	$atributo->set("fieldIdParent","id_centro");
	$atributo->set("sqlType","smallint");
	$atributo->set("namediv","id_proveedor");
	$queryACargar = "SELECT p.id, p.razon as nombre
		FROM llta.proveedores p
		WHERE p.id IN(SELECT id_proveedor FROM llta.proveedores_centros WHERE id_centro='__%idARemp%__')
		ORDER BY p.razon";
	$atributo->set("qsQuery",$queryACargar);
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","fecha_compra");
	$atributo->set("label","Fecha compra");
	$atributo->set("sqlType","date");
	$atributo->set("defaultValue",date("Y-m-d"));
	$atributo->set("inputType","date");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","costo");
	$atributo->set("label","Valor de compra");
	$atributo->set("sqlType","real");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","dot");
	$atributo->set("label","Dot");
	$atributo->set("sqlType","varchar(255)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","matricula");
	$atributo->set("label","Matricula No.");
	$atributo->set("sqlType","varchar(255)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_vehiculo");
	$atributo->set("label","Vehiculo");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","vehiculos");
	$atributo->set("foreignLabelFields","COALESCE(vehiculos.codigo,'')||'/'||COALESCE(vehiculos.placa,'')");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",FALSE);
	$atributo->set("visible",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","posicion");
	$atributo->set("label","Posición");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","km");
	$atributo->set("label","Km");
	$atributo->set("sqlType","real");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","vida");
	$atributo->set("label","Vida");
	$atributo->set("sqlType","varchar(1)");
	$atributo->set("inputType","arraySelect");
	$atributo->set("arrayValues",array("N"=>"Nueva (N)","1"=>"Primer reencauche (1)","2"=>"Segundo reencauche (2)","3"=>"Tercer reencauche (3)"));
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);



	$entidad->checkSqlStructure(FALSE);

?>
