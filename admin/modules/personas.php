<?
	require($CFG->objectPath . "/object.php");

class personas extends entity
{
	function insert()
	{
		if($this->getAttributeByName("login")->get("value") != "")
		{
			$qid = $this->db->sql_query("SELECT count(*) as numero FROM personas WHERE upper(login)='".strtoupper($this->getAttributeByName("login")->get("value"))."'");
			$query = $this->db->sql_fetchrow($qid);
			if($query["numero"]==0)
			{
				$this->id = parent::insert();
				return($this->id);
			}
			else
				avisoError("Ya existe un registro con el mismo login.<br>NO se puede insertar.");
		}
		else
		{
			$this->id = parent::insert();
			return($this->id);
		}
	}

	function update()
	{
		$idsCentros = $idsTareas = array();
		//centros
		$qid = $this->db->sql_query("SELECT * FROM personas_centros WHERE id_persona=".$this->id);
		while($query = $this->db->sql_fetchrow($qid))
		{
			$idsCentros[] = $query["id_centro"];
		}
		
		//tareas
		$qid = $this->db->sql_query("SELECT * FROM personas_tareas WHERE id_persona=".$this->id);
		while($query = $this->db->sql_fetchrow($qid))
		{
			$idsTareas[] = $query["id_tarea"];
		}

		if($this->getAttributeByName("login")->get("value") != "")
		{
			$qid = $this->db->sql_query("SELECT count(*) as numero FROM personas WHERE upper(login)='".strtoupper($this->getAttributeByName("login")->get("value"))."' AND id != ".$this->id);
			$query = $this->db->sql_fetchrow($qid);
			if($query["numero"]==0)
				parent::update();
			else
				avisoError("Ya existe un registro con el mismo login.<br>NO se puede actualizar el registro.");
		}else
			parent::update();

		if($_SESSION[$this->CFG->sesion]["user"]["nivel_acceso"]!=1 && $_SESSION[$this->CFG->sesion]["user"]["nivel_acceso"]!=13)
		{
			$this->db->sql_query("DELETE FROM personas_centros WHERE id_persona=".$this->id);
			foreach($idsCentros as $idCentro)
				$this->db->sql_query("INSERT INTO personas_centros (id_persona, id_centro) VALUES ('".$this->id."', '".$idCentro."')");
				
			$this->db->sql_query("DELETE FROM personas_tareas WHERE id_persona=".$this->id);
			foreach($idsTareas as $idTarea)
				$this->db->sql_query("INSERT INTO personas_tareas (id_persona, id_tarea) VALUES ('".$this->id."', '".$idTarea."')");
		}
	}


	function find()
	{
		global $ME;
		$condicionAnterior = "true";

		if(!preg_match("/admin/",$ME,$match)){
			$condicionAnterior = " personas.id='".$_SESSION[$this->CFG->sesion]["user"]["id"]."'";
		}else{
			$condicionAnterior = "personas.id IN (SELECT id_persona FROM personas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona = '".$_SESSION[$this->CFG->sesion]["user"]["id"]."'))";
		}
		
		if($_SESSION[$this->CFG->sesion]["user"]["nivel_acceso"]==13)
			$condicionAnterior .= " AND nivel_acceso != 1";
			


		parent::find($condicionAnterior);
	}
}

	$entidad =& new personas();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Personas");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","apellido,nombre");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1 && $_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=13 && $_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=9){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnDelete",FALSE);
	}
/*
  if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]=6){//Es Jefe de Control

		$entidad->set("btnAdd",TRUE);
	}
*/

	if(!preg_match("/admin/",$ME,$match)){
		$entidad->set("btnAdd",FALSE);
	}

	
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]==1  || $_SESSION[$CFG->sesion]["user"]["nivel_acceso"]==13)
	{
	$entidad->set("JSComplementaryRevision","
		var sel_centros = 0;
		for (var i = 0; i < document.entryform.elements.length; i++) {
			if (document.entryform.elements[i].type=='checkbox' && document.entryform.elements[i].checked==1){
				sel_centros++;
			}         
		}           
		if (sel_centros==0){
			window.alert('Por favor seleccione: Centro');
			return(false);
		}     
		");
	}



// ---------- Vinculos a muchos  ----------------

	$link=new link($entidad);
	$link->set("name","Centros");
	$link->set("description","Centros");
	$link->set("field","id_persona");
	$link->set("type","checkbox");
	$link->set("visible",FALSE);
	$link->set("relatedTable","personas_centros");
	$link->set("relatedICTable","centros");
	$link->set("relatedICField","centros.centro");
	$link->set("relatedICIdFieldUno","id_persona");
	$link->set("relatedICIdFieldDos","id_centro");
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1  && $_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=13)
	{
		$ctos = array();
		$actual = $db->sql_query("SELECT id_centro FROM personas_centros WHERE id_persona=".$_SESSION[$CFG->sesion]["user"]["id"]);
		while($qa = $db->sql_fetchrow($actual))
		{
			$ctos[] = $qa["id_centro"];
		}
		$link->set("relatedICTableFilter","centros.id IN (".implode(",",$ctos).")");
	}else
		$entidad->addLink($link);


	if(preg_match("/admin/",$ME,$match)){
		
		
	$link=new link($entidad);
		$link->set("name","Reportes");
		$link->set("description","Reportes");
		$link->set("field","id_persona");
		$link->set("type","checkbox");
		$link->set("visible",FALSE);
		$link->set("relatedTable","personas_reportes");
		$link->set("relatedICTable","reportes");
		$link->set("relatedICField","reportes.nombre_reporte");
		$link->set("relatedICIdFieldUno","id_persona");
		$link->set("relatedICIdFieldDos","id_reportes");
	//	$link->set("relatedICIdFieldTres","id_informe");		
		$entidad->addLink($link);
		

		$link=new link($entidad);
		$link->set("name","Tareas");
		$link->set("description","Tareas");
		$link->set("field","id_persona");
		$link->set("type","checkbox");
		$link->set("visible",FALSE);
		$link->set("relatedTable","personas_tareas");
		$link->set("relatedICTable","tareas");
		$link->set("relatedICField","tareas.tarea");
		$link->set("relatedICIdFieldUno","id_persona");
		$link->set("relatedICIdFieldDos","id_tarea");
		$entidad->addLink($link);



		$link=new link($entidad);
		$link->set("name","personas_cargos");
		$link->set("url",$ME . "?module=personas_cargos");
		$link->set("icon","icon-overview.gif");
		$link->set("description","Cargos");
		$link->set("field","id_persona");
		$link->set("type","iframe");
		$link->set("relatedTable","personas_cargos");
		$link->set("popup",true);
		$entidad->addLink($link);

		$link=new link($entidad);
		$link->set("name","personas_servicios");
		$link->set("url",$ME . "?module=personas_servicios");
		$link->set("iconoLetra","S");
		$link->set("description","Servicios");
		$link->set("field","id_persona");
		$link->set("type","iframe");
		$link->set("relatedTable","personas_servicios");
		$link->set("popup",true);
		$entidad->addLink($link);


		$link=new link($entidad);
		$link->set("name","personas_informes");
		$link->set("url",$ME . "?module=personas_informes");
		$link->set("iconoLetra","I");
		$link->set("description","Informes");
		$link->set("field","id_persona");
		$link->set("type","iframe");
		$link->set("relatedTable","personas_informes");
		$link->set("popup",true);
		$entidad->addLink($link);
	}

// ---------- ATRIBUTOS          ----------------


	$atributo=new attribute($entidad);
	$atributo->set("field","nivel_acceso");
	$atributo->set("label","Nivel de acceso");
	$atributo->set("inputType","arraySelect");
	$array_accesos=array(
										"0"=>"Empleado Sin Acceso",
										"1"=>"Administrador",
										"2"=>"Radio Operador",
										"3"=>"Visor AVL x ciudad",
										"4"=>"Visor AVL global",
										"5"=>"Interventoría AVL", 
										"6"=>"Jefe de Control",
										"7"=>"Gerente Operaciones (observador)",
										"8"=>"Gerente Operaciones (supervisor)",
										"9"=>"Planeador Mantenimiento",
										"10"=>"Supervisor de Mantenimiento",
										"11"=>"Gerente Mantenimiento",
										"12"=>"Gerente General",
										"13"=>"Administrador Regional",
										"14"=>"Radio Operador Avanzado",
										"15"=>"Gerente General Avanzado"
					);
	
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]==13)
		unset($array_accesos[1]);

	if(!preg_match("/admin/",$ME,$match)){
		$actual = $db->sql_row("SELECT nivel_acceso FROM personas WHERE id=".$_SESSION[$CFG->sesion]["user"]["id"]);
		foreach($array_accesos as $idAc => $nv)
		{
			if($idAc == $actual["nivel_acceso"])
				$array_accesos=array($idAc=> $nv);
		}
	}
	$atributo->set("arrayValues",$array_accesos);
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","centro");
	$atributo->set("label","Centro");
	$atributo->set("sqlType","subquery");
	$atributo->set("inputType","subQuery");
	$atributo->set("subQuery","
		(SELECT 
			array_to_string(array(
				SELECT centros.centro
				FROM personas_centros
				LEFT JOIN centros ON centros.id=personas_centros.id_centro
				WHERE personas_centros.id_persona=personas.id
				),', ') as cen
		)
			");
	$atributo->set("browseable",TRUE);
	$atributo->set("searchable",FALSE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","id_cargo");
	$atributo->set("label","Cargo");
	$atributo->set("sqlType","smallint");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","cargos");
	$atributo->set("foreignLabelFields","cargos.nombre");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("visible",FALSE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","nombre");
	$atributo->set("label","Nombre");
	$atributo->set("sqlType","character varying(60)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","apellido");
	$atributo->set("label","Apellido");
	$atributo->set("sqlType","character varying(60)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","cedula");
	$atributo->set("label","Cédula");
	$atributo->set("sqlType","character varying(128)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","id_estado");
	$atributo->set("label","Estado");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","estados_personas");
	$atributo->set("foreignLabelFields","estados_personas.estado");

	if(!preg_match("/admin/",$ME,$match)){
		$array_accesos=array();
		$actual = $db->sql_row("SELECT id_estado, estado FROM personas LEFT JOIN estados_personas ON estados_personas.id=personas.id_estado WHERE personas.id=".$_SESSION[$CFG->sesion]["user"]["id"]);
		$array_accesos=array($actual["id_estado"]=> $actual["estado"]);

		$atributo->set("inputType","arraySelect");
		$atributo->set("arrayValues",$array_accesos);
	}
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);



	$atributo=new attribute($entidad);
	$atributo->set("field","login");
	$atributo->set("label","Login");
	$atributo->set("sqlType","character varying(16)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","password");
	$atributo->set("label","Password");
	$atributo->set("inputType","password");
	$atributo->set("encrypted",TRUE);
	$atributo->set("sqlType","character varying(32)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",FALSE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","email");
	$atributo->set("label","E-mail");
	$atributo->set("sqlType","character varying(1055)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","direccion");
	$atributo->set("label","Dirección");
	$atributo->set("sqlType","character varying(128)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","telefono");
	$atributo->set("label","Teléfono");
	$atributo->set("sqlType","character varying(128)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","celular");
	$atributo->set("label","Celular");
	$atributo->set("sqlType","character varying(128)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","fecha");
	$atributo->set("label","Último acceso");
	$atributo->set("sqlType","timestamp");
	$atributo->set("inputType","timestamp");
	$atributo->set("readonly",TRUE);
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$entidad->checkSqlStructure();

//	$entidad->db->sql_query("VACUUM ANALYZE " . $entidad->table);

?>
