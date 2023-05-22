<?
	require($CFG->objectPath . "/object.php");

	class vehiculos extends entity
	{
		function insert()
		{
			$this->id=parent::insert();
			$this->crearEquipo();
			return($this->id);	
		}

		function update()
		{
			parent::update();
			$this->crearEquipo();
			actualizarKmyHoro("kilometraje", $this->getAttributeByName("kilometraje")->get("value"), "", $this->id);
			actualizarKmyHoro("horometro", $this->getAttributeByName("horometro")->get("value"), "", $this->id);
			actualizarIdGrupo("mtto.equipos",$this->getAttributeByName("id_grupo")->get("value"),"id_vehiculo",$this->id);
		}

		function delete()
		{
			die("no es permitido borrar vehiculos");
		}

		function crearEquipo()
		{
//	Lo comenté porque al editar un vehículo, cambiándole de centro, se duplica.
//			$qid = $this->db->sql_query("SELECT id,id_grupo FROM mtto.equipos WHERE id_vehiculo=".$this->id." AND id_centro=".$this->getAttributeByName("id_centro")->get("value"));
			$qid = $this->db->sql_query("SELECT id,id_grupo,id_centro,kilometraje,horometro FROM mtto.equipos WHERE id_vehiculo='".$this->id."'");
			if($this->db->sql_numrows($qid) == 0){
				$cons = "INSERT INTO mtto.equipos (id_grupo, nombre, id_vehiculo, id_centro) VALUES ('".$this->getAttributeByName("id_grupo")->get("value")."','".$this->getAttributeByName("codigo")->get("value")."/".$this->getAttributeByName("placa")->get("value")."','".$this->id."','".$this->getAttributeByName("id_centro")->get("value")."')";
				$this->db->sql_query($cons);	
			}
			elseif($this->db->sql_numrows($qid) == 1){//Porque si hay más de uno, no se sabe cuál es el maestro del equipo...
				$gr = $this->db->sql_fetchrow($qid);
				if(
					$gr["id_grupo"] != $this->getAttributeByName("id_grupo")->get("value") ||
					$gr["id_centro"] != $this->getAttributeByName("id_centro")->get("value") ||
					$gr["kilometraje"] != $this->getAttributeByName("kilometraje")->get("value") ||
					$gr["horometro"] != $this->getAttributeByName("horometro")->get("value") ||
					$gr["nombre"] != $this->getAttributeByName("codigo")->get("value")."/".$this->getAttributeByName("placa")->get("value")
				)
				{
					$this->db->sql_query("
						UPDATE mtto.equipos SET 
							id_grupo=(SELECT id_grupo FROM vehiculos WHERE id='" . $this->id . "'),
							id_centro=(SELECT id_centro FROM vehiculos WHERE id='" . $this->id . "'),
							kilometraje=(SELECT kilometraje FROM vehiculos WHERE id='" . $this->id . "'),
							horometro=(SELECT horometro FROM vehiculos WHERE id='" . $this->id . "'),
							nombre=(SELECT codigo || '/' || placa FROM vehiculos WHERE id='" . $this->id . "')
						WHERE id='".$gr["id"]."'
					");
				}
			}
			else{//Hay más de un equipo asociado al mismo vehículo
				$gr = $this->db->sql_fetchrow($qid);
				if(
					$gr["id_grupo"] != $this->getAttributeByName("id_grupo")->get("value") ||
					$gr["id_centro"] != $this->getAttributeByName("id_centro")->get("value")
				)
				{
					$this->db->sql_query("
						UPDATE mtto.equipos SET 
							id_grupo=(SELECT id_grupo FROM vehiculos WHERE id='" . $this->id . "'),
							id_centro=(SELECT id_centro FROM vehiculos WHERE id='" . $this->id . "')
						WHERE id='".$gr["id"]."'
					");
				}

			}
    ##Actualizamos id_GPS con placa por si lo cambian
    $this->db->sql_query("UPDATE vehiculos SET idgps=placa WHERE id='". $this->id . "'");
		}

		function find()
		{
			$condicionAnterior=" vehiculos.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona = '".$_SESSION[$this->CFG->sesion]["user"]["id"]."')";
			parent::find($condicionAnterior);
		}
	}

	$entidad =& new vehiculos();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Vehículos");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","codigo");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1 && $_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=13 && $_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=6){//No es administrador
		$entidad->set("btnAdd",FALSE);
	//	$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}


// ---------- Vinculos a muchos  ----------------

	$link=new link($entidad);
	$link->set("name","Registros_GPS");
	$link->set("url",$ME . "?module=gps_vehi");
	$link->set("icon","taxi.gif");
	$link->set("description","Registros GPS");
	$link->set("field","id_vehi");
	$link->set("type","iframe");
	$link->set("relatedTable","gps_vehi");
	$link->set("selfField","idgps");
	$link->set("popup",true);
	$link->set("jsWindowWidth",650);
	$link->set("jsWindowHeight",500);
	$link->set("showInEdit",false);
//	$link->set("visible",false);
	$entidad->addLink($link);

	
	$link=new link($entidad);
	$link->set("name","vehiculos_horarios");
	$link->set("url",$ME . "?module=vehiculos_horarios");
	$link->set("icon","icon-settings.gif");
	$link->set("description","Horarios Laborables");
	$link->set("field","id_vehiculo");
	$link->set("type","iframe");
	$link->set("relatedTable","vehiculos_horarios");
	$link->set("popup",true);
	$entidad->addLink($link);


// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_centro");
	$atributo->set("label","Centro");
	$atributo->set("sqlType","smallint");
	$atributo->set("inputType","querySelect");
	$atributo->set("qsQuery","
		SELECT c.id, c.centro as nombre
		FROM centros c
		WHERE c.id IN (SELECT id_centro FROM personas_centros WHERE id_persona = '".$_SESSION[$CFG->sesion]["user"]["id"]."')");

	$atributo->set("mandatory",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("inputType","select");
	$atributo->set("field","id_tipo_vehiculo");
	$atributo->set("label","Tipo Vehículo");
	$atributo->set("sqlType","smallint");
	$atributo->set("foreignTable","tipos_vehiculos");
	$atributo->set("foreignLabelFields","tipo");
	$atributo->set("mandatory",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("inputType","select");
	$atributo->set("field","id_tipo_vehiculo_sui");
	$atributo->set("label","Tipo Vehículo SUI");
	$atributo->set("sqlType","smallint");
	$atributo->set("foreignTable","tipos_vehiculos_sui");
	$atributo->set("foreignLabelFields","tipo_sui");
	$atributo->set("mandatory",FALSE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","id_referencia");
	$atributo->set("label","Marca/Referencia");
	$atributo->set("sqlType","smallint");
	$atributo->set("inputType","querySelect");
	$atributo->set("qsQuery","
			SELECT r.id, m.marca||'/'||r.referencia as nombre
			FROM referencias r 
			LEFT JOIN marcas_vehiculos m ON m.id=r.id_marca_vehiculo
			ORDER BY m.marca,r.referencia");
	$atributo->set("mandatory",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","codigo");
	$atributo->set("label","Código");
	$atributo->set("sqlType","character varying(16)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("visible",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_grupo");
	$atributo->set("label","Grupo");
	$atributo->set("parentIdLabel","mtto.grupos.nombre");
	$atributo->set("parentTable","mtto.grupos");
	$atributo->set("parentCondicion"," id IN (SELECT id FROM mtto.grupos WHERE id_centro IS NULL OR id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."'))");
	$atributo->set("useGetPath","TRUE");
	$atributo->set("inputType","recursiveSelect");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);
	

	$atributo=new attribute($entidad);
	$atributo->set("field","idgps");
	$atributo->set("label","ID GPS");
	$atributo->set("sqlType","character varying(16)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("visible",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","serie_simcard");
	$atributo->set("label","Serie SIM CARD");
	$atributo->set("sqlType","character varying(32)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("visible",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","serie_unidad");
	$atributo->set("label","Serie unidad");
	$atributo->set("sqlType","character varying(32)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("visible",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","kilometraje");
	$atributo->set("label","Kilometraje");
	$atributo->set("sqlType","float");
//	$atributo->set("visible",FALSE);
//	$atributo->set("editable",FALSE);
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","horometro");
	$atributo->set("label","Horómetro");
	$atributo->set("sqlType","double precision");
//	$atributo->set("visible",FALSE);
//	$atributo->set("editable",FALSE);
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","modelo");
	$atributo->set("label","Modelo");
	$atributo->set("sqlType","character varying(50)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);
  
  $atributo=new attribute($entidad);
  $atributo->set("field","cilindraje");
  $atributo->set("label","Cilindraje");
  $atributo->set("sqlType","character varying(30)");
  $atributo->set("mandatory",FALSE);
  $atributo->set("searchable",TRUE);
  $atributo->set("browseable",FALSE);
  $atributo->set("shortList",FALSE);
  $entidad->addAttribute($atributo);
  
  $atributo=new attribute($entidad);
  $atributo->set("field","nunmotor");
  $atributo->set("label","Numero Motor");
  $atributo->set("sqlType","character varying(30)");
  $atributo->set("mandatory",FALSE);
  $atributo->set("searchable",TRUE);
  $atributo->set("browseable",FALSE);
  $atributo->set("shortList",FALSE);
  $entidad->addAttribute($atributo);
  
  $atributo=new attribute($entidad);
  $atributo->set("field","nunchasis");
  $atributo->set("label","Numero Chasis");
  $atributo->set("sqlType","character varying(30)");
  $atributo->set("mandatory",FALSE);
  $atributo->set("searchable",TRUE);
  $atributo->set("browseable",FALSE);
  $atributo->set("shortList",FALSE);
  $entidad->addAttribute($atributo);
  
	$atributo=new attribute($entidad);
	$atributo->set("field","ano");
	$atributo->set("sqlType","integer");
	$atributo->set("sqlType","character varying(50)");
	$atributo->set("label","Año");
	$atributo->set("mandatory",FALSE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","placa");
	$atributo->set("label","Placa");
	$atributo->set("sqlType","varchar(51)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","id_estado");
	$atributo->set("label","Estado");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","estados_vehiculos");
	$atributo->set("foreignLabelFields","estados_vehiculos.estado");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_estado_motor");
	$atributo->set("label","Estado motor");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","eventos");
	$atributo->set("foreignField","codigo");
//	$atributo->set("foreignLabelFields","estados_vehiculos.estado");
	$atributo->set("sqlType","smallint");
	$atributo->set("readonly",TRUE);
	$atributo->set("mandatory",TRUE);
	$atributo->set("defaultValueSQL","15");
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","hrposition");//Human-Readable Position
	$atributo->set("label","Última posición");
	$atributo->set("sqlType","character varying(128)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",FALSE);
	$atributo->set("browseable",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);



	$atributo=new attribute($entidad);
	$atributo->set("field","alarm");
	$atributo->set("label","¿Notificar cuando no reciba GPS?");
	$atributo->set("inputType","option");
	$atributo->set("defaultValue",1);
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);


/*	********	*/
	
	$atributo=new attribute($entidad);
	$atributo->set("field","ingreso");
	$atributo->set("label","Ingreso al sistema");
	$atributo->set("sqlType","timestamp");
	$atributo->set("visible",FALSE);
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",FALSE);
	$atributo->set("browseable",FALSE);
	$atributo->set("editable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","tiempo");
	$atributo->set("label","Hora de recepción<br> último evento");
	$atributo->set("sqlType","timestamp");
	$atributo->set("visible",FALSE);
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",FALSE);
	$atributo->set("browseable",TRUE);
	$atributo->set("editable",FALSE);
	$atributo->set("shortList",FALSE);
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
	$atributo->set("inputType","select");
	$atributo->set("field","id_unidad");
	$atributo->set("label","Tipo de unidad");
	$atributo->set("sqlType","smallint");
	$atributo->set("foreignTable","unidades");
	$atributo->set("foreignModule","unidades");
	$atributo->set("mandatory",FALSE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","the_geom");
	$atributo->set("label","Geometría");
	$atributo->set("sqlType","geometry");
	$atributo->set("geometryType","POINT");
	$atributo->set("geometrySRID",4326);
	$atributo->set("searchable",FALSE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$atributo->set("visible",FALSE);
	$atributo->set("editable",'READONLY');
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","km_virtual");
	$atributo->set("label","Km Virtual");
	$atributo->set("sqlType","real");
	$atributo->set("defaultValueSQL","0");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("searchable",FALSE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","horometro_virtual");
	$atributo->set("label","Horómetro Virtual");
	$atributo->set("sqlType","real");
	$atributo->set("defaultValueSQL","0");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("searchable",FALSE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","publico");
	$atributo->set("label","¿Público?");
	$atributo->set("inputType","option");
	$atributo->set("sqlType","boolean");
	$atributo->set("defaultValue","t");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","tiene_gps");
	$atributo->set("label","¿Tiene GPS?");
	$atributo->set("inputType","option");
	$atributo->set("sqlType","boolean");
	$atributo->set("defaultValue","t");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","alquilado");
	$atributo->set("label","¿Alquilado?");
	$atributo->set("inputType","option");
	$atributo->set("sqlType","boolean");
	$atributo->set("defaultValue","f");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","fecha_entrada_operacion");
	$atributo->set("label","Fecha de entrada en Operación");
	$atributo->set("sqlType","date");
	$atributo->set("defaultValue",date("Y-m-d"));
	$atributo->set("searchableRange",TRUE);
	$atributo->set("inputType","date");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","fecha_salida_operacion");
	$atributo->set("label","Fecha de salida en Operación");
	$atributo->set("sqlType","date");
	$atributo->set("searchableRange",TRUE);
	$atributo->set("inputType","date");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);
	

	$atributo=new attribute($entidad);
	$atributo->set("field","id_actividad_vehiculo_sui");
	$atributo->set("label","Actividad SUI");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","actividad_vehiculo_sui");
	$atributo->set("foreignLabelFields","actividad");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);


/*	********	*/

//	$entidad->checkSqlStructure(FALSE);

	
?>
 
