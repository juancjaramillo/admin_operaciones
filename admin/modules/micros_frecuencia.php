<?

	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Frecuencias");
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
	$link->set("name","frecuencias_operarios");
	$link->set("url",$ME . "?module=frecuencias_operarios");
	$link->set("icon","grupo.jpeg");
	$link->set("description","Operarios");
	$link->set("field","id_frecuencia");
	$link->set("relatedTable","frecuencias_operarios");
	$link->set("type","iframe");
	$link->set("popup",true);
	$entidad->addLink($link);

	$link=new link($entidad);
	$link->set("name","frecuencias_desplazamientos");
	$link->set("url",$ME . "?module=frecuencias_desplazamientos");
	$link->set("icon","icon-overview.gif");
	$link->set("description","Desplazamientos");
	$link->set("field","id_frecuencia");
	$link->set("relatedTable","frecuencias_desplazamientos");
	$link->set("type","iframe");
	$link->set("popup",true);
	$entidad->addLink($link);

	$link=new link($entidad);
	$link->set("name","frecuencias_bolsas");
	$link->set("url",$ME . "?module=frecuencias_bolsas");
	$link->set("icon","bolsa.jpeg");
	$link->set("description","Bolsas");
	$link->set("field","id_frecuencia");
	$link->set("relatedTable","frecuencias_bolsas");
	$link->set("type","iframe");
	$link->set("popup",true);
	$entidad->addLink($link);


// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_micro");
	$atributo->set("label","Micro");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","micros");
	$atributo->set("foreignLabelFields","micros.codigo");
	$atributo->set("foreignTableFilter","id IN (SELECT id FROM micros WHERE id_ase IN (SELECT id FROM ases WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona = '".$_SESSION[$CFG->sesion]["user"]["id"]."')))");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","dia");
	$atributo->set("label","Día");
	$atributo->set("sqlType","smallint");
	$atributo->set("inputType","arraySelect");
	$atributo->set("arrayValues",array("1"=>"Lunes", "2"=>"Martes","3"=>"Miércoles","4"=>"Jueves","5"=>"Viernes","6"=>"Sábado","7"=>"Domingo"));
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","id_turno");
	$atributo->set("label","Turno");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","turnos");
	$atributo->set("foreignLabelFields","turnos.turno");
	$atributo->set("foreignTableFilter","id IN (SELECT t.id FROM turnos t LEFT JOIN centros c ON c.id_empresa=t.id_empresa WHERE c.id IN (SELECT id_centro FROM personas_centros WHERE id_persona = '".$_SESSION[$CFG->sesion]["user"]["id"]."'))");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","produccion");
	$atributo->set("label","Producción");
	$atributo->set("sqlType","real");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);
	
	$atributo=new attribute($entidad);
	$atributo->set("field","viajes");
	$atributo->set("label","Viajes");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);
	
	$atributo=new attribute($entidad);
	$atributo->set("field","hora_inicio");
	$atributo->set("label","Hora Inicio");
	$atributo->set("sqlType","time");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);
	
	$atributo=new attribute($entidad);
	$atributo->set("field","hora_fin");
	$atributo->set("label","Hora Fin");
	$atributo->set("sqlType","time");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);
	

	$entidad->checkSqlStructure();

?>
