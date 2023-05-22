<?
	require($CFG->objectPath . "/object.php");

	class pesos extends entity
	{
		function find()
		{
			$user=$_SESSION[$this->CFG->sesion]["user"];
			$condicionAnterior = " NOT asignado AND id_vehiculo IN (SELECT id FROM vehiculos WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')) ";
			parent::find($condicionAnterior);
		}

		function delete()
		{
			$cerrado = $this->db->sql_row("SELECT cerrado FROM rec.pesos WHERE id=".$this->id);
			if($cerrado["cerrado"] == "t")
			{
				avisoError("<br /><strong>¡ERROR!</strong><br />El peso no se puede eliminar.  Es un peso que ya fue cerrado. ");
				die;
			}
			
			parent::delete();
		}

	}

	$entidad =& new pesos();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Pesos");
	$entidad->set("table","rec.pesos");
	$entidad->set("orderBy","fecha_entrada DESC");

	$entidad->set("JSComplementaryRevision","
			if(document.entryform.peso_inicial.value.replace(/ /g, '') =='' && document.entryform.peso_final.value.replace(/ /g, '') =='' && document.entryform.peso_total.value.replace(/ /g, '') ==''){
				window.alert('Por favor escriba el peso inicial y final o el peso total');
				document.entryform.peso_inicial.focus();
				return(false);
			}

			if(document.entryform.peso_inicial.value.replace(/ /g, '') !='' && document.entryform.peso_final.value.replace(/ /g, '') == '' ){
				window.alert('Por favor escriba el peso inicial y final');
				document.entryform.peso_final.focus();
				return(false);
			}

			if(document.entryform.peso_inicial.value.replace(/ /g, '') =='' && document.entryform.peso_final.value.replace(/ /g, '') != '' ){
				window.alert('Por favor escriba el peso inicial y final');
				document.entryform.peso_inicial.focus();
				return(false);
			}
	");

	if(!in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_rec.pesos"])){
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

	include("style.php");
	$entidad->set("formColumns",1);

// ---------- Vinculos a muchos  ----------------

	if(preg_match("/admin/",$ME,$match)){
		$link=new link($entidad);
		$link->set("name","movimientos");
		$link->set("url",$ME . "?module=rec.movimientos_pesos");
		$link->set("icon","gear.png");
		$link->set("description","Movimientos");
		$link->set("field","id_peso");
		$link->set("type","iframe");
		$link->set("relatedTable","rec.movimientos_pesos");
		$link->set("popup",true);
		$entidad->addLink($link);
	}


// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_vehiculo");
	$atributo->set("label","Vehículo");
	$atributo->set("inputType","select");
	$atributo->set("inputType","querySelect");
	$atributo->set("qsQuery","SELECT vehiculos.id, vehiculos.codigo||' / '||vehiculos.placa as nombre
		FROM vehiculos
		WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')
		ORDER BY codigo,placa");
	$atributo->set("sqlType","integer");
	if(!isset($_GET["mode"])  || (isset($_GET["mode"])   && $_GET["mode"] != "buscar"))
		$atributo->set("onChange","updateRecursive_id_lugar_descargue(this)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","peso_inicial");
	$atributo->set("label","Peso Inicial");
	$atributo->set("sqlType","double precision");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","peso_final");
	$atributo->set("label","Peso Final");
	$atributo->set("sqlType","double precision");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","peso_total");
	$atributo->set("label","Peso Total");
	$atributo->set("sqlType","double precision");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_lugar_descargue");
	$atributo->set("label","Lugar Descargue");
	$atributo->set("sqlType","smallint");
	if(isset($_GET["mode"]) && $_GET["mode"] == "buscar")
	{
		$atributo->set("inputType","querySelect");
		$atributo->set("qsQuery","SELECT l.id, l.nombre||' / '||c.centro as nombre
			FROM lugares_descargue l
			LEFT JOIN centros c ON c.id=l.id_centro
			WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')
			ORDER BY l.nombre, c.centro");
	}
	else
	{
		$atributo->set("inputType","select_dependiente");
		$atributo->set("fieldIdParent","id_vehiculo");
		$atributo->set("namediv","id_lugar_descargue");
		$atributo->set("foreignTable","lugares_descargue");
		$atributo->set("parentJoin", " LEFT JOIN centros ON centros.id = foo.id_centro " );
		$atributo->set("sd_condition", " id_centro IN (SELECT id_centro FROM vehiculos WHERE id='__%idARemp%__')");
		$atributo->set("foreignLabelFields","nombre");
		$queryACargar = "SELECT l.id, l.nombre||' / '||c.centro as nombre
			FROM lugares_descargue l
			LEFT JOIN centros c ON c.id=l.id_centro
			WHERE id_centro IN (SELECT id_centro FROM vehiculos WHERE id='__%idARemp%__')
			ORDER BY l.nombre, c.centro";
		$atributo->set("qsQuery",$queryACargar);
	}

	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","tiquete_entrada");
	$atributo->set("label","Tiquete Entrada");
	$atributo->set("sqlType","varchar(16)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","tiquete_salida");
	$atributo->set("label","Tiquete Salida");
	$atributo->set("sqlType","varchar(16)");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","fecha_entrada");
	$atributo->set("label","Fecha Entrada");
	$atributo->set("sqlType","timestamp");
	$atributo->set("searchableRange",TRUE);
	$atributo->set("inputType","timestamp");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","fecha_salida");
	$atributo->set("label","Fecha Salida");
	$atributo->set("sqlType","timestamp");
	$atributo->set("searchableRange",TRUE);
	$atributo->set("inputType","timestamp");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","reparte");
	$atributo->set("label","Reparte");
	$atributo->set("sqlType","boolean");
	$atributo->set("inputType","option");
	$atributo->set("defaultValue","f");
	$atributo->set("mandatory",FALSE);
	$atributo->set("searchable",FALSE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$atributo->set("editable",FALSE);
	$atributo->set("visible",FALSE);
	if(isset($_GET["mode"]) && $_GET["mode"] == "agregar")
	{
		$atributo->set("editable",TRUE);
		$atributo->set("visible",TRUE);
		$atributo->set("mandatory",TRUE);
	}
	$entidad->addAttribute($atributo);



	$atributo=new attribute($entidad);
	$atributo->set("field","suma");
	$atributo->set("label","Total Porcentajes");
	$atributo->set("sqlType","subquery");
	$atributo->set("inputType","subQuery");
	$atributo->set("subQuery","
			SELECT sum(porcentaje) as total
			FROM rec.movimientos_pesos
			WHERE rec.movimientos_pesos.id_peso=pesos.id");
	$atributo->set("browseable",TRUE);
	$atributo->set("searchable",FALSE);
	$atributo->set("editable",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","asignado");
	$atributo->set("label","¿Asignado?");
	$atributo->set("sqlType","boolean");
	$atributo->set("defaultValue","false");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",FALSE);
	$atributo->set("searchable",FALSE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$atributo->set("visible",FALSE);
	$entidad->addAttribute($atributo);

$atributo=new attribute($entidad);
	$atributo->set("field","cerrado");
	$atributo->set("label","¿Cerrado?");
	$atributo->set("inputType","option");
	$atributo->set("sqlType","boolean");
	$atributo->set("defaultValue","0");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",FALSE);
	$atributo->set("searchable",FALSE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$atributo->set("visible",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_persona_cerro");
	$atributo->set("label","Persona Cerró");
	$atributo->set("sqlType","integer");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","personas");
	$atributo->set("foreignLabelFields","personas.nombre||' '||personas.apellido");
	$atributo->set("foreignTableFilter"," id IN (SELECT id_persona FROM personas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE  id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')) ");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",FALSE);
	$atributo->set("searchable",FALSE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$atributo->set("visible",FALSE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","fecha_cerro");
	$atributo->set("label","Fecha Cerro");
	$atributo->set("sqlType","timestamp");
	$atributo->set("searchableRange",TRUE);
	$atributo->set("inputType","timestamp");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",FALSE);
	$atributo->set("searchable",FALSE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$atributo->set("visible",FALSE);
	$entidad->addAttribute($atributo);









	$entidad->checkSqlStructure();

?>
