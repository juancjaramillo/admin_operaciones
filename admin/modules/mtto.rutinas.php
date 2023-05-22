<?
	require($CFG->objectPath . "/object.php");
	
class rutinas extends entity
{
	function delete()
	{
		$qid = $this->db->sql_row("SELECT count(id) as num FROM mtto.ordenes_trabajo WHERE id_rutina=".$this->id);
		if($qid["num"] != 0)
		{
			include($this->CFG->dirroot."/templates/header_popup.php");
			echo '<br><br>La rutina no se puede borrar porque está relacionada con alguna orden de trabajo.<br><br>La rutina se desactivará.<br><br><input type="button" class="boton_verde" value="Cerrar" onclick="window.close()"/>';
			$this->db->sql_query("UPDATE mtto.rutinas SET activa=false WHERE id=".$this->id);
			die;
		}
		parent::delete();	
	}

	function find()
	{
		$condicionAnterior = " mtto.rutinas.id IN (SELECT id_rutina FROM mtto.rutinas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".$_SESSION[$this->CFG->sesion]["user"]["id"]."'))";
		parent::find($condicionAnterior);
	}

}

	$entidad =& new rutinas();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Rutinas");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","rutina");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

	$entidad->set("JSComplementaryRevision","
		if(document.entryform.id_grupo.options[document.entryform.id_grupo.selectedIndex].value=='' && document.entryform.id_equipo.options[document.entryform.id_equipo.selectedIndex].value=='')
		{
			window.alert('Por favor seleccione Grupo o Equipo uno');
			return(false);
		}

		if(document.entryform.id_grupo.options[document.entryform.id_grupo.selectedIndex].value!='' && document.entryform.id_equipo.options[document.entryform.id_equipo.selectedIndex].value!='')
		{
			window.alert('Por favor seleccione Grupo o Equipo dos');
			return(false);
		}


		if(document.entryform.id_frecuencia.options[document.entryform.id_frecuencia.selectedIndex].value=='%' && document.entryform.frec_horas.value.replace(/ /g, '') =='' && document.entryform.frec_km.value.replace(/ /g, '') =='')
		{
			window.alert('Por favor seleccione alguna frecuencia');
			return(false);
		}");

// ---------- Vinculos a muchos  ----------------

	$link=new link($entidad);
	$link->set("name","Centros");
	$link->set("description","Centros");
	$link->set("field","id_rutina");
	$link->set("type","checkbox");
	$link->set("visible",FALSE);
	$link->set("relatedTable","mtto.rutinas_centros");
	$link->set("relatedICTable","centros");
	$link->set("relatedICField","centros.centro");
	$link->set("relatedICIdFieldUno","id_rutina");
	$link->set("relatedICIdFieldDos","id_centro");
	$link->set("relatedICTableFilter","id IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')");
	$entidad->addLink($link);



	$link=new link($entidad);
	$link->set("name","Actividades");
	$link->set("url",$ME . "?module=mtto.rutinas_actividades");
	$link->set("icon","icon-overview.gif");
	$link->set("description","Actividades");
	$link->set("field","id_rutina");
	$link->set("relatedTable","mtto.rutinas_actividades");
	$link->set("type","iframe");
	$link->set("popup",true);
	$entidad->addLink($link);
	
	
	$link=new link($entidad);
	$link->set("name","Elementos");
	$link->set("url",$ME . "?module=mtto.rutinas_elementos");
	$link->set("icon","herramienta_fondo.gif");
	$link->set("description","Elementos");
	$link->set("field","id_rutina");
	$link->set("relatedTable","mtto.rutinas_elementos");
	$link->set("type","iframe");
	$link->set("popup",true);
	$entidad->addLink($link);

	$link=new link($entidad);
	$link->set("name","primera_vez");
	$link->set("url",$ME . "?module=mtto.rutinas_primera_vez");
	$link->set("iconoLetra","PV");
	$link->set("description","Primera Vez");
	$link->set("field","id_rutina");
	$link->set("relatedTable","mtto.rutinas_primera_vez");
	$link->set("type","iframe");
	$link->set("popup",true);
	$entidad->addLink($link);

	$link=new link($entidad);
	$link->set("name","duplicar");
	$link->set("url",$ME . "?module=mtto.rutinas&mode=duplicar_rutina_en_admin");
	$link->set("iconoLetra","DP");
	$link->set("description","Duplicar Rutina");
	$link->set("field","id_rutina");
	$entidad->addLink($link);

	$link=new link($entidad);
	$link->set("name","rutinas_mediciones");
	$link->set("url",$ME . "?module=mtto.rutinas_mediciones");
	$link->set("iconoLetra","M");
	$link->set("description","Mediciones");
	$link->set("field","id_rutina");
	$link->set("relatedTable","mtto.rutinas_mediciones");
	$link->set("type","iframe");
	$link->set("popup",true);
	$entidad->addLink($link);

	$link=new link($entidad);
	$link->set("name","rutinas_talleres");
	$link->set("url",$ME . "?module=mtto.rutinas_talleres");
	$link->set("iconoLetra","TE");
	$link->set("description","Talleres Externos");
	$link->set("field","id_rutina");
	$link->set("relatedTable","mtto.rutinas_talleres");
	$link->set("type","iframe");
	$link->set("popup",true);
	$entidad->addLink($link);

// ---------- ATRIBUTOS          ----------------


	if(nvl($_GET["mode"]) != "agregar")
	{	
		$atributo=new attribute($entidad);
		$atributo->set("field","centro");
		$atributo->set("label","Centro");
		$atributo->set("sqlType","subquery");
		$atributo->set("inputType","subQuery");
		$atributo->set("subQuery","
				(SELECT 
				 array_to_string(array(
					 SELECT centros.centro
					 FROM mtto.rutinas_centros
					 LEFT JOIN centros ON centros.id=mtto.rutinas_centros.id_centro
					 WHERE mtto.rutinas_centros.id_rutina=rutinas.id
				 ),', ') as cen
				)
				");
		$atributo->set("browseable",TRUE);
		$atributo->set("searchable",FALSE);
		$entidad->addAttribute($atributo);
	}


	$atributo=new attribute($entidad);
	$atributo->set("field","rutina");
	$atributo->set("label","Rutina");
	$atributo->set("sqlType","varchar(1055)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_sistema");
	$atributo->set("label","Sistema");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","mtto.sistemas");
	$atributo->set("foreignLabelFields","mtto.sistemas.sistema");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_grupo");
	$atributo->set("label","Grupo");
	$atributo->set("parentIdLabel","mtto.grupos.nombre");
	$atributo->set("parentTable","mtto.grupos");
	$atributo->set("useGetPath","TRUE");
	$atributo->set("parentCondicion"," id IN (SELECT id FROM mtto.grupos WHERE id_centro IS NULL OR id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."'))");
	$atributo->set("inputType","recursiveSelect");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_equipo");
	$atributo->set("label","Equipo");
	/*
	$atributo->set("foreignLabelFields","mtto.equipos.nombre");
	$atributo->set("foreignTable","mtto.equipos");
	$atributo->set("fieldIdParent","id_centro");
	$atributo->set("inputType","select_dependiente");
	$atributo->set("namediv","id_equipo");
	$queryACargar = "SELECT mtto.equipos.id, mtto.equipos.nombre
		FROM mtto.equipos
		WHERE mtto.equipos.id_centro='__%idARemp%__'
		ORDER BY mtto.equipos.nombre";
	$atributo->set("qsQuery",$queryACargar);
	*/
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","mtto.equipos");
	$atributo->set("foreignLabelFields","mtto.equipos.nombre");
	$atributo->set("foreignTableFilter"," id IN (SELECT id FROM mtto.equipos WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."'))");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","lugar");
	$atributo->set("label","Lugar");
	$atributo->set("sqlType","varchar(12)");
	$atributo->set("inputType","arraySelect");
	$atributo->set("arrayValues",array("Interno"=>"Interno", "Externo"=>"Externo"));
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);




	$atributo=new attribute($entidad);
	$atributo->set("field","id_frecuencia");
	$atributo->set("label","Frecuencia");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","mtto.frecuencias");
	$atributo->set("foreignLabelFields","mtto.frecuencias.frecuencia");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","frec_horas");
	$atributo->set("label","Frecuencia Horas");
	$atributo->set("sqlType","real");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","frec_km");
	$atributo->set("label","Frecuencia Km");
	$atributo->set("sqlType","real");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","fec_cumplir");
	$atributo->set("label","No. de frecuencias a cumplir");
	$atributo->set("sqlType","integer");
	$atributo->set("defaultValue","1");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);



	$atributo=new attribute($entidad);
	$atributo->set("field","tiempo_ejecucion");
	$atributo->set("label","Tiempo Ejecución (minutos)");
	$atributo->set("sqlType","real");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","comentarios");
	$atributo->set("label","Comentarios");
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
	$atributo->set("field","id_tipo_mantenimiento");
	$atributo->set("label","Tipo");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","mtto.tipos");
	$atributo->set("foreignLabelFields","tipo");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_prioridad");
	$atributo->set("label","Prioridad");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","mtto.prioridades");
	$atributo->set("foreignLabelFields","prioridad");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","activa");
	$atributo->set("label","¿Activa?");
	$atributo->set("inputType","option");
	$atributo->set("sqlType","boolean");
	$atributo->set("defaultValue","t");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);




	$entidad->checkSqlStructure();

?>
