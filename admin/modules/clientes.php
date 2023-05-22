<?

	require($CFG->objectPath . "/object.php");

	class cliente extends entity{
		function insert($dummy=FALSE){
			$atributo=$this->getAttributeByName("fecha");
			$atributo->set("value",date("Y-m-d H:i:s"));
			$this->updateAttribute($atributo);
			return(parent::insert($dummy));
		}
		function update($dummy=FALSE){
			$atributo=$this->getAttributeByName("fecha");
			$atributo->set("value",date("Y-m-d H:i:s"));
			$this->updateAttribute($atributo);
			
			if($_SESSION[$this->CFG->sesion]["user"]["nivel_acceso"] == 6)
				$this->db->sql_query("UPDATE clientes SET gp='".$this->getAttributeByName("gp")->get("value")."', fecha = '".date("Y-m-d H:i:s")."' WHERE id =".$this->id);
			else
				return(parent::update($dummy));
		}

		function find()
		{
			$condicionAnterior=" id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona = '".$_SESSION[$this->CFG->sesion]["user"]["id"]."')";
			parent::find($condicionAnterior);
		}
	}

	$entidad =& new cliente();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Clientes");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","telefono");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnDelete",FALSE);
	}


// ---------- Vinculos a muchos  ----------------
	$link=new link($entidad);
	$link->set("name","mapa");
	$link->set("url","modules/map.phtml?module=vehiculo");
	$link->set("letra","M");
	$link->set("icon","truck.gif");
	$link->set("description","Ver en el mapa");
	$link->set("field","idvehi");
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
	$atributo->set("field","codigo");
	$atributo->set("label","Código");
	$atributo->set("sqlType","bigint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","producto");
	$atributo->set("label","Producto");
	$atributo->set("sqlType","bigint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","plan_facturacion");
	$atributo->set("label","Plan de facturación");
	$atributo->set("sqlType","bigint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","nombre");
	$atributo->set("label","Nombre");
	$atributo->set("sqlType","character varying(128)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","direccion");
	$atributo->set("label","Dirección");
	$atributo->set("sqlType","character varying(128)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_categoria");
	$atributo->set("label","Categoría");
	$atributo->set("inputType","arraySelect");
	$atributo->set("arrayValues",array("1"=>"Residencial","2"=>"Comercial","3"=>"Oficial"));
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_subcategoria");
	$atributo->set("label","Subcategoría");
//	$atributo->set("inputType","arraySelect");
//	$atributo->set("arrayValues",array("1"=>"Residencial","2"=>"Comercial","3"=>"Oficial"));
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","ciclo");
	$atributo->set("label","Ciclo");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","operador");
	$atributo->set("label","Operador");
//	$atributo->set("inputType","arraySelect");
//	$atributo->set("arrayValues",array("1"=>"Residencial","2"=>"Comercial","3"=>"Oficial"));
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","estado");
	$atributo->set("label","Estado");
//	$atributo->set("inputType","arraySelect");
//	$atributo->set("arrayValues",array("1"=>"Residencial","2"=>"Comercial","3"=>"Oficial"));
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","telefono");
	$atributo->set("label","Teléfono");
	$atributo->set("sqlType","varchar(32)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","unidadeshab");
	$atributo->set("label","Unidades habitacionales");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","produccion");
	$atributo->set("label","Producción");
	$atributo->set("sqlType","float");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","gp");
	$atributo->set("label","¿Gran Productor?");
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
	$atributo->set("field","the_geom");
	$atributo->set("label","Posición");
	$atributo->set("sqlType","geometry");
	$atributo->set("inputType","golLocation");
	$atributo->set("geometryType","POINT");
	$atributo->set("geometrySRID",4326);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$atributo->set("visible",FALSE);
	$atributo->set("editable",'READONLY');
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","fecha");
	$atributo->set("label","Última modificación");
	$atributo->set("sqlType","timestamp");
	$atributo->set("inputType","timestamp");
	$atributo->set("readonly",TRUE);
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$atributo->set("editable",'READONLY');
	$entidad->addAttribute($atributo);

	


/*	********	*/

	$entidad->checkSqlStructure(TRUE);

?>
 
