<?

	require($CFG->objectPath . "/object.php");

class proveedores extends entity
{
	function find()
	{
		$condicionAnterior = "";
		$user=$_SESSION[$this->CFG->sesion]["user"];
		$condicionAnterior = " llta.proveedores.id IN (SELECT distinct(id_proveedor) FROM llta.proveedores_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]'))";
		parent::find($condicionAnterior);
	}
}



	$entidad =& new proveedores();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Proveedores");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","razon");
	$entidad->set("HLRows",false);

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


	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
//		$entidad->set("btnAdd",FALSE);
//		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------

	$user=$_SESSION[$CFG->sesion]["user"];

	$link=new link($entidad);
	$link->set("name","Centros");
	$link->set("description","Centros");
	$link->set("field","id_proveedor");
	$link->set("type","checkbox");
	$link->set("visible",FALSE);
	$link->set("relatedTable","llta.proveedores_centros");
	$link->set("relatedICTable","centros");
	$link->set("relatedICField","centros.centro");
	$link->set("relatedICIdFieldUno","id_proveedor");
	$link->set("relatedICIdFieldDos","id_centro");
	if($user["nivel_acceso"]!=1)
		$link->set("relatedICTableFilter","centros.id IN (" . implode(",",$user["id_centro"]) . ")");
	$entidad->addLink($link);


	if(preg_match("/admin/",$ME,$match)){
	$link=new link($entidad);
	$link->set("name","llantas");
	$link->set("url",$ME . "?module=llta.llantas");
	$link->set("icon","llanta.jpeg");
	$link->set("description","Llantas");
	$link->set("field","id_proveedor");
	$link->set("relatedTable","llta.llantas");
	$entidad->addLink($link);
	}


// ---------- ATRIBUTOS          ----------------


	$atributo=new attribute($entidad);
	$atributo->set("field","razon");
	$atributo->set("label","Razón Social");
	$atributo->set("sqlType","character varying(255)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	
	$atributo=new attribute($entidad);
	$atributo->set("field","nit");
	$atributo->set("label","NIT");
	$atributo->set("sqlType","character varying(20)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","contacto");
	$atributo->set("label","Contacto");
	$atributo->set("sqlType","character varying(255)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","direccion");
	$atributo->set("label","Dirección");
	$atributo->set("sqlType","character varying(125)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","telefono");
	$atributo->set("label","Teléfono");
	$atributo->set("sqlType","character varying(125)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	
	$atributo=new attribute($entidad);
	$atributo->set("field","celular");
	$atributo->set("label","Celular");
	$atributo->set("sqlType","character varying(125)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$entidad->checkSqlStructure();

?>
