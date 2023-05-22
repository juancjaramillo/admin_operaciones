<?

require($CFG->objectPath . "/object.php");
	
class apoyos extends entity
	{
		function find()
		{
			$condicionAnterior = " id_vehiculo IN (SELECT id FROM vehiculos WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".$_SESSION[$this->CFG->sesion]["user"]["id"]."'))";
			parent::find($condicionAnterior);
		}
	}

	$entidad =& new apoyos();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Apoyos");
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
	$link->set("name","rec.apoyos_movimientos");
	$link->set("url",$ME . "?module=rec.apoyos_movimientos");
	$link->set("icon","icon-settings.gif");
	$link->set("description","Movimientos");
	$link->set("field","id_apoyo");
	$link->set("type","iframe");
	$link->set("relatedTable","apoyos_movimientos");
	$link->set("popup",true);
	$entidad->addLink($link);



// ---------- ATRIBUTOS          ----------------


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
	$atributo->set("field","peso");
	$atributo->set("label","Peso");
	$atributo->set("sqlType","float");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","km_inicial");
	$atributo->set("label","Km Inicio");
	$atributo->set("sqlType","float");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
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
	$atributo->set("field","final");
	$atributo->set("label","Fecha Fin");
	$atributo->set("sqlType","timestamp");
	$atributo->set("searchableRange",TRUE);
	$atributo->set("inputType","timestamp");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);



	$entidad->checkSqlStructure();

?>
