<?
	require($CFG->objectPath . "/object.php");

	class equipos extends entity
	{
		function insert()
		{
			$this->id=parent::insert();
			return($this->id);
		}

		function update()
		{
			parent::update();
			actualizarKmyHoro("kilometraje", $this->getAttributeByName("kilometraje")->get("value"), $this->id);
			actualizarKmyHoro("horometro", $this->getAttributeByName("horometro")->get("value"), $this->id);
		}

		function find()
		{
			$condicionAnterior = " mtto.equipos.id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".$_SESSION[$this->CFG->sesion]["user"]["id"]."')";
			parent::find($condicionAnterior);
		}
	}

	$entidad =& new equipos();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Equipos");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","nombre");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

	$entidad->set("JSComplementaryRevision","
		if(document.entryform.id_superior.options[document.entryform.id_superior.selectedIndex].value=='')
		{
			if(document.entryform.id_vehiculo.options[document.entryform.id_vehiculo.selectedIndex].value=='%' && document.entryform.id_centro.options[document.entryform.id_centro.selectedIndex].value=='%'){
				window.alert('Por favor seleccione Centro ó Vehiculo');
				document.entryform.id_centro.focus();
				return(false);
			}
		}");

// ---------- Vinculos a muchos  ----------------

	$link=new link($entidad);
	$link->set("name","primera_vez");
	$link->set("url",$ME . "?module=mtto.rutinas_primera_vez");
	$link->set("iconoLetra","PV");
	$link->set("description","Primera Vez");
	$link->set("field","id_equipo");
	$link->set("relatedTable","mtto.rutinas_primera_vez");
	$link->set("type","iframe");
	$link->set("popup",true);
	$entidad->addLink($link);

	$link=new link($entidad);
	$link->set("name","mtto.equipos_archivos");
	$link->set("url",$ME . "?module=mtto.equipos_archivos");
	$link->set("iconoLetra","AA");
	$link->set("description","Archivos Adjuntos");
	$link->set("field","id_equipo");
	$link->set("relatedTable","mtto.equipos_archivos");
	$link->set("type","iframe");
	$link->set("popup",true);
	$entidad->addLink($link);

// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_grupo");
	$atributo->set("label","Grupo");
	$atributo->set("inputType","recursiveSelect");
	$atributo->set("parentIdLabel","mtto.grupos.nombre");
	$atributo->set("parentTable","mtto.grupos");
	$atributo->set("parentCondicion"," id IN (SELECT id FROM mtto.grupos WHERE id_centro IS NULL OR id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."'))");
	$atributo->set("useGetPath","TRUE");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","nombre");
	$atributo->set("label","Nombre");
	$atributo->set("sqlType","character varying(255)");
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
	$atributo->set("foreignLabelFields","COALESCE(vehiculos.codigo,'')||'/'||COALESCE(vehiculos.placa,'')");
	$atributo->set("foreignTableFilter"," id IN (SELECT id FROM vehiculos WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')) ");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	
	$atributo=new attribute($entidad);
	$atributo->set("field","id_superior");
	$atributo->set("label","Superior");
	$atributo->set("inputType","recursiveSelect");
	$atributo->set("useGetPath",TRUE);
	$atributo->set("parentCondicion"," id IN (SELECT id FROM mtto.equipos WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."'))");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	
	$atributo=new attribute($entidad);
	$atributo->set("field","codigo");
	$atributo->set("label","Código");
	$atributo->set("sqlType","varchar(125)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","serial");
	$atributo->set("label","Serial");
	$atributo->set("sqlType","varchar(125)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","kilometraje");
	$atributo->set("label","Kilometraje");
	$atributo->set("sqlType","float");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","horometro");
	$atributo->set("label","Horómetro");
	$atributo->set("sqlType","double precision");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","id_centro");
	$atributo->set("label","Centro");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","centros");
	$atributo->set("foreignLabelFields","centro");
	$atributo->set("foreignTableFilter"," id IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","imagen");
	$atributo->set("label","Imagen");
	$atributo->set("sqlType","text");
	$atributo->set("inputType","image");
	$atributo->set("tamanioImagen","200");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",FALSE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);


	
	$entidad->checkSqlStructure();
?>
