<?
	require($CFG->objectPath . "/object.php");

	class movimientos_pesos extends entity
	{
		function update()
		{
			parent::update();
			actualizarPesoAsignado($this->id);
		}

		function insert()
		{
			$this->id = parent::insert();
			actualizarPesoAsignado($this->id);
			return $this->id;
		}

		function delete()
		{
			$peso = $this->db->sql_row("SELECT id_peso FROM rec.movimientos_pesos WHERE id=".$this->id);
			parent::delete();
			actualizarPesoAsignado(0,true,$peso["id_peso"]);
		}
	}

	$entidad =& new movimientos_pesos();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$entidad->set("labelModule","Movimientos / Pesos");
	$entidad->set("table",$entidad->get("name"));

	include("style.php");
	$entidad->set("formColumns",1);

// ---------- Vinculos a muchos  ----------------

// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_peso");
	$atributo->set("label","Peso");
	$atributo->set("inputType","querySelect");
	$qsQuery = "SELECT rec.pesos.id, rec.pesos.fecha_entrada ||' / '||lugares_descargue.nombre||' / '||centros.centro as nombre  
			FROM rec.pesos
			LEFT JOIN lugares_descargue ON lugares_descargue.id = rec.pesos.id_lugar_descargue
			LEFT JOIN centros ON centros.id=lugares_descargue.id_centro
			WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')
			ORDER BY rec.pesos.fecha_entrada desc, lugares_descargue.nombre, centros.centro";
	if(isset($_GET["mode"]) && $_GET["mode"] == "agregar")
		$qsQuery = "SELECT rec.pesos.id, rec.pesos.fecha_entrada ||' / '||lugares_descargue.nombre||' / '||centros.centro as nombre  
				FROM rec.pesos
				LEFT JOIN lugares_descargue ON lugares_descargue.id = rec.pesos.id_lugar_descargue
				LEFT JOIN centros ON centros.id=lugares_descargue.id_centro
				WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."') AND fecha_entrada::date BETWEEN (now()::date - integer '120') AND now()
				ORDER BY rec.pesos.fecha_entrada desc, lugares_descargue.nombre, centros.centro";
	$atributo->set("qsQuery",$qsQuery);
	$atributo->set("foreignModule","rec.pesos");

	/*
	$atributo->set("ACIdField","rec.pesos.id");
	$atributo->set("ACLabel","(rec.pesos.fecha_entrada ||' / '||lugares_descargue.nombre||' / '||centros.centro)");
	$atributo->set("ACFrom","rec.pesos LEFT JOIN lugares_descargue ON lugares_descargue.id = rec.pesos.id_lugar_descargue LEFT JOIN centros ON centros.id=lugares_descargue.id_centro");
	$atributo->set("ACFields","to_char(fecha_entrada,'YYYY-MM-DD HH24:MI:SS'), lugares_descargue.nombre, centros.centro");
	$atributo->set("ACWhere","id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')");
	$atributo->set("inputType","autocomplete");
	*/
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","id_movimiento");
	$atributo->set("label","Mov: fecha/ruta/placa/codigo");
	$atributo->set("inputType","querySelect");
	$atributo->set("qsQuery","
			SELECT mov.id, mov.inicio||' / '||m.codigo||' / '||v.placa||' / '||v.codigo as nombre
			FROM rec.movimientos mov
			LEFT JOIN vehiculos v ON v.id=mov.id_vehiculo
			LEFT JOIN micros m ON m.id=mov.id_micro
			WHERE m.id_ase IN (SELECT id FROM ases WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."'))
			ORDER BY mov.inicio desc, m.codigo, v.codigo, v.placa");
	/*
	if(isset($_GET["mode"]) && $_GET["mode"] == "agregar")
		$atributo->set("qsQuery","
				SELECT mov.id, mov.inicio||' / '||m.codigo||' / '||v.placa||' / '||v.codigo as nombre
				FROM rec.movimientos mov
				LEFT JOIN vehiculos v ON v.id=mov.id_vehiculo
				LEFT JOIN micros m ON m.id=mov.id_micro
				WHERE m.id_ase IN (SELECT id FROM ases WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')) AND mov.inicio::date BETWEEN (now()::date - integer '120') AND now()
				ORDER BY mov.inicio desc, m.codigo, v.codigo, v.placa");
	*/
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","viaje");
	$atributo->set("label","Viaje");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","porcentaje");
	$atributo->set("label","Porcentaje");
	$atributo->set("sqlType","double precision");
	$atributo->set("mandatory",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$atributo->set("defaultValue",100);
	$entidad->addAttribute($atributo);

	
	$entidad->checkSqlStructure();

?>