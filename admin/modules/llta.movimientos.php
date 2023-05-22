<?
	require_once($CFG->objectPath . "/object.php");

	class movimientos extends entity
	{
		function insert()
		{
			$this->id = parent::insert();
			$this->actualizarKmyIdVehiculoLLanta();	
		}

		function update()
		{
			parent::update();
			$this->actualizarKmyIdVehiculoLLanta();	
		}

		function actualizarKmyIdVehiculoLLanta()
		{
			$idLLanta = $this->getAttributeByName("id_llanta")->get("value");
			if($this->getAttributeByName("id_vehiculo")->get("value") != "%" && $this->getAttributeByName("id_vehiculo")->get("value") != "")
				$this->db->sql_query("UPDATE llta.llantas SET id_vehiculo='".$this->getAttributeByName("id_vehiculo")->get("value")."' WHERE id=".$idLLanta);
			if($this->getAttributeByName("km")->get("value") != "")
				$this->db->sql_query("UPDATE llta.llantas SET km='".$this->getAttributeByName("km")->get("value")."' WHERE id=".$idLLanta);
		}
	}

	$entidad =& new movimientos();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Movimientos");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","fecha");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

	$entidad->set("JSComplementaryRevision","
		if(document.entryform.id_tipo_movimiento.options[document.entryform.id_tipo_movimiento.selectedIndex].value=='1')
		{
			if(document.entryform.id_vehiculo.options[document.entryform.id_vehiculo.selectedIndex].value=='%' || document.entryform.posicion.options[document.entryform.posicion.selectedIndex].value=='%' || document.entryform.costo.value.replace(/ /g, '') =='' || document.entryform.id_subtipo_movimiento.options[document.entryform.id_subtipo_movimiento.selectedIndex].value=='%')
			{
				window.alert('Si el estado es reencauche los campos de Vehículo, posición, costo, y subtipo deben diligenciarse.');
				return(false);
			}
		}
	");


// ---------- Vinculos a muchos  ----------------


// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_llanta");
	$atributo->set("label","Llanta");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","llta.llantas");
	$atributo->set("foreignLabelFields","llta.llantas.numero");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","fecha");
	$atributo->set("label","Fecha");
	$atributo->set("sqlType","date");
	$atributo->set("defaultValue",date("Y-m-d"));
	$atributo->set("inputType","date");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_tipo_movimiento");
	$atributo->set("label","Tipo");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","llta.tipos_movimientos");
	$atributo->set("foreignLabelFields","llta.tipos_movimientos.tipo");
	$atributo->set("onChange","updateRecursive_id_subtipo_movimiento(this)");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","id_subtipo_movimiento");
	$atributo->set("label","SubTipo");
	$atributo->set("inputType","select_dependiente");
	$atributo->set("foreignTable","llta.subtipos_movimientos");
	$atributo->set("foreignTableAlias","p");
	$atributo->set("foreignLabelFields","p.subtipo");
	$atributo->set("fieldIdParent","id_tipo_movimiento");
	$atributo->set("sqlType","smallint");
	$atributo->set("namediv","id_subtipo_movimiento");
	$queryACargar = "SELECT p.id, p.subtipo as nombre
		FROM llta.subtipos_movimientos p
		LEFT JOIN llta.tipos_movimientos m ON m.id=p.id_tipo_movimiento
		WHERE m.id='__%idARemp%__'
		ORDER BY p.subtipo";
	$atributo->set("qsQuery",$queryACargar);
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
	$atributo->set("field","horas");
	$atributo->set("label","Horas");
	$atributo->set("sqlType","real");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","prof_uno");
	$atributo->set("label","Profundidad Uno");
	$atributo->set("sqlType","real");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","prof_dos");
	$atributo->set("label","Profundidad Dos");
	$atributo->set("sqlType","real");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);
	
	$atributo=new attribute($entidad);
	$atributo->set("field","prof_tres");
	$atributo->set("label","Profundidad Tres");
	$atributo->set("sqlType","real");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_vehiculo");
	$atributo->set("label","Vehiculo");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","vehiculos");
//	$atributo->set("foreignLabelFields","vehiculos.placa");
	$atributo->set("foreignLabelFields","COALESCE(vehiculos.codigo,'')||'/'||COALESCE(vehiculos.placa,'')");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","posicion");
	$atributo->set("label","Posición Llanta");
	$atributo->set("sqlType","integer");
	$atributo->set("inputType","arraySelect");
	$pos = array();
	for($i=1;$i<=12;$i++)
	{
		$pos[$i]=$i;
	}
	$atributo->set("arrayValues",$pos);
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","costo");
	$atributo->set("label","Costo");
	$atributo->set("sqlType","real");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);


	$entidad->checkSqlStructure();

?>
