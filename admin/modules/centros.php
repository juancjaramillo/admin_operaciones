<?

	require($CFG->objectPath . "/object.php");

class centros extends entity
{
	function find()
	{
		$condicionAnterior="";
		if($_SESSION[$this->CFG->sesion]["user"]["nivel_acceso"]!=1)
			$condicionAnterior=" centros.id IN (SELECT id_centro FROM personas_centros WHERE id_persona = '".$_SESSION[$this->CFG->sesion]["user"]["id"]."')";

		parent::find($condicionAnterior);
	}
}

	$entidad =& new centros();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Centros");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","centro");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}


// ---------- Vinculos a muchos  ----------------

	$link=new link($entidad);
	$link->set("name","clientes");
	$link->set("url",$ME . "?module=clientes");
	$link->set("icon","grupo.jpeg");
	$link->set("description","Clientes");
	$link->set("field","id_centro");
	$link->set("relatedTable","clientes");
	$entidad->addLink($link);

	$link=new link($entidad);
	$link->set("name","vehiculos");
	$link->set("url",$ME . "?module=vehiculos");
	$link->set("icon","truck.gif");
	$link->set("description","Vehiculos");
	$link->set("field","id_centro");
	$link->set("relatedTable","vehiculos");
	$entidad->addLink($link);

	$link=new link($entidad);
	$link->set("name","personas");
	$link->set("url",$ME . "?module=personas");
	$link->set("icon","boy.gif");
	$link->set("description","Personas");
	$link->set("field","id_centro");
	$link->set("relatedTable","personas");
	$entidad->addLink($link);

// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_empresa");
	$atributo->set("label","Empresa");
	$atributo->set("inputType","querySelect");
	$atributo->set("qsQuery","
		SELECT e.id, e.empresa as nombre
		FROM empresas e
		WHERE e.id IN (SELECT id_empresa FROM centros WHERE id IN (SELECT id_centro FROM personas_centros WHERE id_persona = '".$_SESSION[$CFG->sesion]["user"]["id"]."'))");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","centro");
	$atributo->set("label","Centro");
	$atributo->set("sqlType","character varying(50)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","codigo");
	$atributo->set("label","Código (SW Almacén)");
	$atributo->set("sqlType","character varying(50)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_municipio");
	$atributo->set("label","Municipio");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","municipios");
	$atributo->set("foreignLabelFields","municipio");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","facturacion");
	$atributo->set("label","Facturación");
	$atributo->set("sqlType","boolean");
	$atributo->set("inputType","option");
	$atributo->set("defaultValue","t");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	
	$atributo=new attribute($entidad);
	$atributo->set("field","logo");
	$atributo->set("label","Logo");
	$atributo->set("sqlType","text");
	$atributo->set("inputType","fileFS");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",FALSE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);



	$entidad->checkSqlStructure();

?>
 
