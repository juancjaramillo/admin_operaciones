<?

	require($CFG->objectPath . "/object.php");

	$entidad =& new entity();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Rutas GP");
	$entidad->set("table",$entidad->get("name"));
	$entidad->set("orderBy","dia");

	include("style.php");
	$entidad->set("formColumns",1);
	if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"]!=1){//No es administrador
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------

	$link=new link($entidad);
	$link->set("name","gp.rutas_clientes");
	$link->set("url",$ME . "?module=gp.rutas_clientes");
	$link->set("icon","icon-route.png");
	$link->set("description","Rutas clientes");
	$link->set("field","id_ruta");
	$link->set("relatedTable","gp.rutas_clientes");
	$link->set("type","iframe");
	$link->set("popup",true);
	$entidad->addLink($link);

	$link=new link($entidad);
	$link->set("name","gp.movimientos");
	$link->set("url",$ME . "?module=gp.movimientos");
	$link->set("icon","icon-activate.gif");
	$link->set("description","Movimientos");
	$link->set("field","id_ruta");
	$link->set("relatedTable","gp.movimientos");
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
	$entidad->addAttribute($atributo);
	
	$atributo=new attribute($entidad);
	$atributo->set("field","dia");
	$atributo->set("label","Día");
	$atributo->set("sqlType","smallint");
	$atributo->set("inputType","arraySelect");
	$atributo->set("arrayValues",array("1"=>"Lunes", "2"=>"Martes", "3"=>"Miércoles", "4"=>"Jueves","5"=>"Viernes","6"=>"Sábado","7"=>"Domingo"));
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);



	$entidad->checkSqlStructure();

?>
 
