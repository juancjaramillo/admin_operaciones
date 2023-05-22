<?
require_once($CFG->objectPath . "/object.php");

	class orden_trabajo extends entity
	{
		function insert()
		{
			$herramientas = $this->db->sql_row("SELECT herramientas FROM mtto.rutinas WHERE id=".$this->getAttributeByName("id_rutina")->get("value"));
			$this->getAttributeByName("herramientas")->set("value",$herramientas["herramientas"]);
			$this->id = parent::insert();

			//elementos
			$this->db->sql_query("INSERT INTO mtto.ordenes_trabajo_elementos (id_orden_trabajo, id_elemento, cantidad) SELECT '".$this->id."', id_elemento, cantidad FROM mtto.rutinas_elementos WHERE id_rutina='".$this->getAttributeByName("id_rutina")->get("value")."'  ");

			//actividades
			$qid = $this->db->sql_query("SELECT * FROM mtto.rutinas_actividades WHERE id_rutina=".$this->getAttributeByName("id_rutina")->get("value"));
			while($query = $this->db->sql_fetchrow($qid)){
				$this->db->sql_query("INSERT INTO mtto.ordenes_trabajo_actividades (id_orden_trabajo,orden, descripcion, tiempo) VALUES ('".$this->id."', '".$query["orden"]."', '".$query["descripcion"]."', '".$query["tiempo"]."')");
				$idOTA = $this->db->sql_nextid();

				//cargos
				$qidCargos = $this->db->sql_query("SELECT * FROM mtto.rutinas_actividades_cargos WHERE id_actividad=".$query["id"]);
				while($cargos = $this->db->sql_fetchrow(""))
				{
					$this->db->sql_query("INSERT INTO mtto.ordenes_trabajo_actividades_cargos (id_orden_trabajo_actividad, id_cargo, tiempo) VALUES	('".$idOTA."', '".$cargos["id_cargo"]."', '".$cargos["tiempo"]."')");
				}
			}
			
			//talleres
			$this->db->sql_query("INSERT INTO mtto.ordenes_trabajo_talleres (id_orden_trabajo, id_proveedor, costo, tiempo) SELECT '".$this->id."', id_proveedor, costo, tiempo FROM mtto.rutinas_talleres WHERE id_rutina='".$this->getAttributeByName("id_rutina")->get("value")."'  ");

			
			actualizarTiempoEjecucion($this->id);
			return $this->id;	
		}

		function update()
		{
			parent::update();
			actualizarTiempoEjecucion($this->id);
		}

		function find()
		{
			$condicionAnterior = " mtto.ordenes_trabajo.id IN (
				SELECT o.id FROM mtto.ordenes_trabajo o
				LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
				WHERE r.id IN (SELECT id_rutina FROM mtto.rutinas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".$_SESSION[$this->CFG->sesion]["user"]["id"]."')))";
			parent::find($condicionAnterior);
		}

	}

	$entidad =& new orden_trabajo();
//	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Ordenes de Trabajo");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","fecha_planeada DESC");

	if(preg_match("/admin/",$ME,$match))
		include_once("style.php");

	$entidad->set("formColumns",1);
	if(nvl($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],0)!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

/*
	$entidad->set("JSComplementaryRevision","
		if(document.entryform.id_estado_orden_trabajo.options[document.entryform.id_estado_orden_trabajo.selectedIndex].value!='%')
	 		if(document.entryform.id_estado_orden_trabajo.options[document.entryform.id_estado_orden_trabajo.selectedIndex].value!='1')
			{
		 		if(document.entryform.fecha_ejecucion_inicio.value=='')
				{
					window.alert('Por favor seleccione fecha ejecución inicial');
					return(false);
				}
				if(document.entryform.fecha_ejecucion_fin.value=='')
				{
					window.alert('Por favor seleccione fecha ejecución final');
					return(false);
				}
			}
			");
*/
// ---------- Vinculos a muchos  ----------------

	$link=new link($entidad);
	$link->set("name","mtto.ordenes_trabajo_fechas_programadas");
	$link->set("url",$ME . "?module=mtto.ordenes_trabajo_fechas_programadas");
	$link->set("icon","calendar_16.gif");
	$link->set("description","Fechas Programadas");
	$link->set("field","id_orden_trabajo");
	$link->set("relatedTable","mtto.ordenes_trabajo_fechas_programadas");
	$link->set("type","iframe");
	$link->set("popup",true);
	$entidad->addLink($link);
	

	$link=new link($entidad);
	$link->set("name","Actividades");
	$link->set("url",$ME . "?module=mtto.ordenes_trabajo_actividades");
	$link->set("icon","herramienta_fondo.gif");
	$link->set("description","Actividades");
	$link->set("field","id_orden_trabajo");
	$link->set("relatedTable","mtto.ordenes_trabajo_actividades");
	$link->set("type","iframe");
	$link->set("popup",true);
	$entidad->addLink($link);

	$link=new link($entidad);
	$link->set("name","Elementos");
	$link->set("url",$ME . "?module=mtto.ordenes_trabajo_elementos");
	$link->set("icon","herramienta_fondo.gif");
	$link->set("description","Elementos");
	$link->set("field","id_orden_trabajo");
	$link->set("relatedTable","mtto.ordenes_trabajo_elementos");
	$link->set("type","iframe");
	$link->set("popup",true);
	$entidad->addLink($link);

	$link=new link($entidad);
	$link->set("name","Talleres Externos");
	$link->set("url",$ME . "?module=mtto.ordenes_trabajo_talleres");
	$link->set("iconoLetra","TE");
	$link->set("description","Talleres Externos");
	$link->set("field","id_orden_trabajo");
	$link->set("relatedTable","mtto.ordenes_trabajo_talleres");
	$link->set("type","iframe");
	$link->set("popup",true);
	$entidad->addLink($link);
	
	$link=new link($entidad);
	$link->set("name","Otra Orden Trabajo");
	$link->set("url",$ME . "?module=mtto.ordenes_trabajo_origen");
	$link->set("icon","bug2.gif");
	$link->set("description","Personas");
	$link->set("field","id_orden_trabajo");
	$link->set("relatedTable","mtto.ordenes_trabajo_origen");
	$link->set("type","iframe");
	$link->set("popup",true);
	$entidad->addLink($link);
	
// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_rutina");
	$atributo->set("label","Rutina");
	$atributo->set("sqlType","integer");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","mtto.rutinas");
	$atributo->set("foreignLabelFields","mtto.rutinas.rutina");
	$atributo->set("foreignModule","mtto.rutinas");
	$atributo->set("foreignTableFilter"," id IN (SELECT id_rutina FROM mtto.rutinas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".nvl($_SESSION[$CFG->sesion]["user"]["id"],0)."'))");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_equipo");
	$atributo->set("label","Equipo");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","mtto.equipos");
	$atributo->set("foreignLabelFields","mtto.equipos.nombre");
	$atributo->set("sqlType","smallint");
	//$atributo->set("onChange","updateRecursive_id_responsable(this), updateRecursive_id_creador(this)");
	$atributo->set("foreignTableFilter"," id IN (SELECT id FROM mtto.equipos WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".nvl($_SESSION[$CFG->sesion]["user"]["id"],0)."'))");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_motivo");
	$atributo->set("label","Motivo");
	$atributo->set("inputType","recursiveSelect");
	$atributo->set("parentIdLabel","mtto.motivos.nombre");
	$atributo->set("parentTable","mtto.motivos");
	$atributo->set("useGetPath","TRUE");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","fecha_planeada");
	$atributo->set("label","Fecha Planeada");
	$atributo->set("sqlType","timestamp");
	$atributo->set("defaultValue",date("Y-m-d H:i:s"));
	$atributo->set("inputType","timestamp");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","fecha_ejecucion_inicio");
	$atributo->set("label","Fecha Ejecución Inicio");
	$atributo->set("sqlType","timestamp");
	$atributo->set("inputType","timestamp");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","fecha_ejecucion_fin");
	$atributo->set("label","Fecha Ejecución Fin");
	$atributo->set("sqlType","timestamp");
	$atributo->set("inputType","timestamp");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","id_responsable");
	$atributo->set("label","Responsable");
	$atributo->set("sqlType","smallint");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","personas");
	$atributo->set("foreignLabelFields","nombre");
	$atributo->set("foreignLabelFields","p.nombre||' '||p.apellido");
	$atributo->set("foreignTableAlias","p");
	$atributo->set("foreignTableFilter"," p.id IN (SELECT id_persona FROM personas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".nvl($_SESSION[$CFG->sesion]["user"]["id"],0)."')) ");
	
//	$atributo->set("inputType","select_dependiente");
//	$atributo->set("namediv","id_responsable");
//	$queryACargar = "SELECT p.id, p.nombre||' '||p.apellido as nombre
//		FROM personas p
//		LEFT JOIN mtto.equipos e ON e.id_centro=p.id_centro
//		WHERE e.id='__%idARemp%__'
//		ORDER BY p.nombre,p.apellido";
//	$atributo->set("qsQuery",$queryACargar);
	
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_creador");
	$atributo->set("label","Creador");
	$atributo->set("sqlType","smallint");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","personas");
	$atributo->set("foreignTableAlias","p2");
	$atributo->set("foreignLabelFields","p2.nombre||' '||p2.apellido");
	$atributo->set("foreignTableFilter"," p2.id IN (SELECT id_persona FROM personas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".nvl($_SESSION[$CFG->sesion]["user"]["id"],0)."')) ");
	
//	$atributo->set("inputType","select_dependiente");
//	$atributo->set("namediv","id_creador");
//	$queryACargar = "SELECT p2.id, p2.nombre||' '||p2.apellido as nombre
//		FROM personas p2
//		LEFT JOIN mtto.equipos e2 ON e2.id_centro=p2.id_centro
//		WHERE e2.id='__%idARemp%__'
//		ORDER BY p2.nombre,p2.apellido";
//	$atributo->set("qsQuery",$queryACargar);
	
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_planeador");
	$atributo->set("label","Planeador");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","personas");
	$atributo->set("foreignTableAlias","p3");
	$atributo->set("foreignLabelFields","p3.nombre||' '||p3.apellido");
	$atributo->set("foreignTableFilter"," p3.id IN (SELECT id_persona FROM personas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".nvl($_SESSION[$CFG->sesion]["user"]["id"],0)."')) ");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_ingreso_ejecutada");
	$atributo->set("label","Ingreso Ejecutada");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","personas");
	$atributo->set("foreignTableAlias","p4");
	$atributo->set("foreignLabelFields","p4.nombre||' '||p4.apellido");
	$atributo->set("foreignTableFilter"," p4.id IN (SELECT id_persona FROM personas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".	nvl($_SESSION[$CFG->sesion]["user"]["id"],0)."')) ");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","id_estado_orden_trabajo");
	$atributo->set("label","Estado");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","mtto.estados_ordenes_trabajo");
	$atributo->set("foreignLabelFields","mtto.estados_ordenes_trabajo.estado");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","tiempo_ejecucion");
	$atributo->set("label","Tiempo Ejecución (minutos)");
	$atributo->set("sqlType","real");
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
	$atributo->set("field","horometro");
	$atributo->set("label","Horómetro");
	$atributo->set("sqlType","real");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","herramientas");
	$atributo->set("label","Herramientas");
	$atributo->set("sqlType","text");
	$atributo->set("inputType","textarea");
	$atributo->set("inputSize",40);
	$atributo->set("inputRows",2);
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
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

	$atributo=new attribute($entidad);
	$atributo->set("field","id_novedad");
	$atributo->set("label","Novedad");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","novedades");
	$atributo->set("foreignLabelFields","novedades.observaciones");
	$atributo->set("foreignTableFilter"," id IN (SELECT id FROM novedades WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".nvl($_SESSION[$CFG->sesion]["user"]["id"],0)."')) ");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","iev");
	$atributo->set("label","IEV");
	$atributo->set("sqlType","varchar(255)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);


	$entidad->checkSqlStructure();

?>
