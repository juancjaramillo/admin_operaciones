<?
	require($CFG->objectPath . "/object.php");

class micros extends entity
{
	function find($condiciones="")
	{
		global $ME;

		$condicionAnterior = "";
		$user=$_SESSION[$this->CFG->sesion]["user"];
		$condicionAnterior = " micros.id IN (SELECT m.id FROM micros m LEFT JOIN ases a ON a.id=m.id_ase WHERE a.id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='$user[id]')) ".$condiciones;

		parent::find($condicionAnterior);
	}

	function delete()
	{
		$qid = $this->db->sql_row("SELECT sum(num) as total FROM (
				SELECT count(*) as num FROM rec.movimientos WHERE id_micro='".$this->id."'
				UNION
				SELECT count(*) as num FROM bar.movimientos WHERE id_micro='".$this->id."')
				as foo");
		if($qid["total"] != 0)
			die("NO SE PUEDE BORRAR PORQUE TIENE MOVIMIENTOS RELACIONADOS");

		parent::delete();
	}
}

	$entidad =& new micros();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Micros");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","codigo");

	$entidad->set("opcionesYahoo" , "<a href=\"javascript:abrirVentanaJavaScript(\'micros\',\'500\',\'500\',\'".$CFG->wwwroot."/opera/micros.php?mode=editar&id=__id__\',\'\')\"><img alt=\"Editar\" src=\"".$CFG->wwwroot."/admin/iconos/transparente/iconoeditar.gif\" title=\"Editar\" border=0></a>&nbsp;&nbsp;	<a href=\"javascript:abrirVentanaJavaScript(\'micros\',\'1024\',\'581\',\'".$CFG->wwwroot."/opera/record_route.php?mode=traer_gps&id_micro=__id__\',\'\')\"><img alt=\"Ver Mapa\" src=\"".$CFG->wwwroot."/admin/iconos/transparente/camino.jpeg\" title=\"Ver Mapa\" border=0></a>&nbsp;&nbsp;<a href=\"javascript:abrirVentanaJavaScript(\'frecuencias\',\'600\',\'300\',\'".$CFG->wwwroot."/opera/templates/listado_frecuencias.php?id_micro=__id__\',\'\')\"><img alt=\"Frecuencia\" src=\"".$CFG->wwwroot."/admin/iconos/transparente/icon-date.gif\" title=\"Frecuencia\" border=0></a>&nbsp;&nbsp;<a href=\"javascript:accion_modulo(\'micros_tipos_vehiculos\',\'\',\'\',\'&id_micro=__id__\')\"><img alt=\"Vehículos\" src=\"".$CFG->wwwroot."/admin/iconos/transparente/v_campero.png\" title=\"Vehículos\" border=0></a>&nbsp;<a href=\"javascript:accion_modulo(\'peajes_micros\',\'\',\'\',\'&id_micro=__id__\')\"><img alt=\"Peajes de la micro\" src=\"".$CFG->wwwroot."/admin/iconos/transparente/ico-peaje.jpeg\" title=\"Peajes de la micro\" border=0></a>&nbsp;&nbsp; <a href=\"".$CFG->wwwroot."/opera/micros.php?mode=duplicar&id=__id__\"><img alt=\"Duplicar\" src=\"".$CFG->wwwroot."/admin/iconos/transparente/icon-duplicate-zone.gif\" title=\"Duplicar\" border=0></a>");

	if(!in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["opciones_micros"]))
		$entidad->set("opcionesYahoo" , "");

	$entidad->JSComplementary="
		function accion_modulo(modulo, modo, id, complemento)
		{
			url = '".$CFG->wwwroot."/mtto/modules.php?module='+modulo;
			if(modo != '')
				url = url + '&mode='+modo;
			if(id != '')
				url = url + '&id='+id;
			if(complemento  != '')
				url = url + complemento;

			abrirVentanaJavaScript(modulo,'800','500',url);
		}
	";

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------

		$link=new link($entidad);
		$link->set("name","micros_frecuencia");
		$link->set("url",$ME . "?module=micros_frecuencia");
		$link->set("icon","icon-date.gif");
		$link->set("description","Frecuencia");
		$link->set("field","id_micro");
		$link->set("relatedTable","micros_frecuencia");
		$link->set("type","iframe");
		$link->set("popup",true);
		$entidad->addLink($link);
/*
		$link=new link($entidad);
		$link->set("name","micros_segmentos");
		$link->set("url",$ME . "?module=micros_segmentos");
		$link->set("icon","icon-route.png");
		$link->set("description","Segmentos");
		$link->set("field","id_micro");
		$link->set("relatedTable","micros_segmentos");
		$link->set("type","iframe");
		$link->set("popup",true);
		$entidad->addLink($link);
*/
		$link=new link($entidad);
		$link->set("name","micros_arcos");
		$link->set("url",$ME . "?module=micros_arcos");
		$link->set("icon","icon-route.png");
		$link->set("description","Arcos");
		$link->set("field","id_micro");
		$link->set("relatedTable","micros_arcos");
		$link->set("type","iframe");
		$link->set("popup",true);
		$entidad->addLink($link);

		$link=new link($entidad);
		$link->set("name","micros_tipos_vehiculos");
		$link->set("url",$ME . "?module=micros_tipos_vehiculos");
		$link->set("icon","v_campero.png");
		$link->set("description","Vehículos");
		$link->set("field","id_micro");
		$link->set("relatedTable","micros_tipos_vehiculos");
		$link->set("type","iframe");
		$link->set("popup",true);
		$entidad->addLink($link);

		$link=new link($entidad);
		$link->set("name","peajes_micros");
		$link->set("url",$ME . "?module=peajes_micros");
		$link->set("icon","bug2.gif");
		$link->set("description","Peajes");
		$link->set("field","id_micro");
		$link->set("type","iframe");
		$link->set("relatedTable","peajes_micros");
		$link->set("popup",true);
		$entidad->addLink($link);

		$link=new link($entidad);
		$link->set("name","micros_puntos_control");
		$link->set("url",$ME . "?module=micros_puntos_control");
		$link->set("icon","alert.png");
		$link->set("description","Puntos Control");
		$link->set("field","id_micro");
		$link->set("type","iframe");
		$link->set("relatedTable","micros_puntos_control");
		$link->set("popup",true);
		$entidad->addLink($link);

		$link=new link($entidad);
		$link->set("name","mapa");
		$link->set("url",$CFG->wwwroot . "/opera/record_route.php?mode=traer_gps&");
		$link->set("icon","gnome-globe.png");
		$link->set("description","Mapa");
		$link->set("field","id_micro");
		$link->set("type","iframe");
		$link->set("popup",true);
		$link->set("jsWindowWidth",1050);
		$link->set("jsWindowHeight",650);
		$entidad->addLink($link);

	
// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","codigo");
	$atributo->set("label","Código");
	$atributo->set("sqlType","character varying(1055)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$atributo->set("sortableYahoo",TRUE);
	if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["opciones_micros"]))
		$atributo->set("typeEditorYahoo","TextboxCellEditor");
	$entidad->addAttribute($atributo);
	$atributo=new attribute($entidad);
	

	$atributo=new attribute($entidad);
	$atributo->set("field","id_ase");
	$atributo->set("label","Ase");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","ases");
	$atributo->set("foreignLabelFields","ase");
	$atributo->set("foreignTableFilter"," id IN (SELECT id FROM ases WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona = '".$_SESSION[$CFG->sesion]["user"]["id"]."'))");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$atributo->set("sortableYahoo",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_tipo_residuo");
	$atributo->set("label","Tipo Residuo");
	$atributo->set("sqlType","smallint");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","tipos_residuos");
	$atributo->set("foreignLabelFields","tipos_residuos.nombre");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","id_servicio");
	$atributo->set("label","Servicio");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","servicios");
	$atributo->set("foreignLabelFields","servicio");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$atributo->set("sortableYahoo",TRUE);
	$entidad->addAttribute($atributo);

	
	$atributo=new attribute($entidad);
	$atributo->set("field","km");
	$atributo->set("label","Km");
	$atributo->set("sqlType","character varying(1055)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	

	$atributo=new attribute($entidad);
	$atributo->set("field","id_cuartelillo");
	$atributo->set("label","Cuartelillo");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","cuartelillos");
	$atributo->set("foreignLabelFields","cuartelillos.nombre");
	$atributo->set("foreignTableFilter"," id IN (SELECT id FROM cuartelillos WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona = '".$_SESSION[$CFG->sesion]["user"]["id"]."'))");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$atributo->set("visible",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_vehiculo");
	$atributo->set("label","Vehiculo");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","vehiculos");
	$atributo->set("foreignLabelFields","vehiculos.placa||'/'||vehiculos.codigo");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_coordinador");
	$atributo->set("label","Coordinador");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","personas");
	$atributo->set("foreignLabelFields","personas.nombre||' '||personas.apellido");
	$atributo->set("foreignTableFilter","id IN (SELECT id_persona FROM personas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona = '".$_SESSION[$CFG->sesion]["user"]["id"]."'))");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$atributo->set("visible",FALSE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","fecha_desde");
	$atributo->set("label","Vigencia desde");
	$atributo->set("sqlType","date");
	$atributo->set("defaultValue",date("Y-m-d"));
	$atributo->set("searchableRange",TRUE);
	$atributo->set("inputType","date");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","fecha_hasta");
	$atributo->set("label","Vigencia Hasta");
	$atributo->set("sqlType","date");
	$atributo->set("searchableRange",TRUE);
	$atributo->set("inputType","date");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_lugar_descargue");
	$atributo->set("label","Lugar Descargue");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","lugares_descargue");
	$atributo->set("foreignLabelFields","nombre");
	$atributo->set("foreignTableFilter"," id IN (SELECT id FROM lugares_descargue WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona = '".$_SESSION[$CFG->sesion]["user"]["id"]."'))");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","compactadas");
	$atributo->set("label","Compactadas");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","geometry");
	$atributo->set("label","Geometry");
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
	$atributo->set("field","selectiva");
	$atributo->set("label","Recoleccion Selectiva (SUI)");
	$atributo->set("sqlType","varchar(16)");
	$atributo->set("inputType","arraySelect");
	$atributo->set("arrayValues",array("SÍ"=>"SÍ", "NO"=>"NO"));
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","codigo_sui");
	$atributo->set("label","Codigo SUI");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);
	
	$atributo=new attribute($entidad);
	$atributo->set("field","comuna");
	$atributo->set("label","Comuna/Localidad");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","porc_rural");
	$atributo->set("label","% Rural");
	$atributo->set("sqlType","real");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);
	
	$atributo->set("field","macro");
	$atributo->set("label","Macro");
	$atributo->set("sqlType","character varying(3)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","porc_urbano");
	$atributo->set("label","% Urbano");
	$atributo->set("sqlType","real");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","cod_relleno");
	$atributo->set("label","Codigo Relleno");
	$atributo->set("sqlType","character varying(20)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);

	#$entidad->checkSqlStructure();
?>
 
