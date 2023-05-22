<?
	require($CFG->objectPath . "/object.php");

	class trailer extends entity
	{

		function find()
		{
			$condicionAnterior=" rec.desplazamientos_trailer.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona = '".$_SESSION[$this->CFG->sesion]["user"]["id"]."')";
			if(nvl($_GET["fecha"], date("Y-m-d")))
				$condicionAnterior.=" AND hora_inicio::date = '".nvl($_GET["fecha"], date("Y-m-d"))."'";

			parent::find($condicionAnterior);
		}
	}

	$entidad =& new trailer();
	$entidad->set("db",$db);

	$entidad->set("name",basename(__FILE__, ".php"));
	$titulo = "Desplazamientos Trailer, ".strftime("%B %d de %Y",strtotime(nvl($_GET["fecha"], date("Y-m-d"))));
	$entidad->set("labelModule", $titulo);
	$titulo = "<br /><br /><input type='button' class='boton_verde' value='Ir a Fecha' onClick=\"abrirCalendarioConModoModule('/mtto/modules','rec.desplazamientos_trailer')\"><br />";
	$entidad->set("labelModuleAdicional", $titulo);

	$entidad->set("table",$entidad->get("name"));

	include("style.php");
	$entidad->set("formColumns",1);

	if(!in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_rec.desplazamientos_trailer"]))
	{
		$entidad->set("btnAdd",FALSE);
		$entidad->set("btnEdit",FALSE);
		$entidad->set("btnDelete",FALSE);
	}

// ---------- Vinculos a muchos  ----------------

// ---------- ATRIBUTOS          ----------------

	$atributo=new attribute($entidad);
	$atributo->set("field","id_centro");
	$atributo->set("label","Centro");
	$atributo->set("sqlType","integer");
	$atributo->set("inputType","querySelect");
	$atributo->set("qsQuery","
		SELECT c.id, c.centro as nombre
		FROM centros c
		WHERE c.id IN (SELECT id_centro FROM personas_centros WHERE id_persona = '".$_SESSION[$CFG->sesion]["user"]["id"]."')");
	$atributo->set("onChange","updateRecursive_id_vehiculo(this), updateRecursive_id_trailer(this)");
	$atributo->set("mandatory",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","id_vehiculo");
	$atributo->set("label","Vehículo");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","vehiculos");
	$atributo->set("foreignTableAlias","v");
	$atributo->set("foreignLabelFields","v.codigo||'/'||v.placa");
	$atributo->set("sqlType","smallint");
	$atributo->set("inputType","select_dependiente");
	$atributo->set("fieldIdParent","id_centro");
	$atributo->set("namediv","id_vehiculo");
	$queryACargar = "SELECT v.id, v.codigo||'/'||v.placa as nombre
		FROM vehiculos v
		WHERE v.id_tipo_vehiculo = 6 AND v.id_centro='__%idARemp%__'
		ORDER BY v.codigo, v.placa";
	$atributo->set("qsQuery",$queryACargar);
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","id_trailer");
	$atributo->set("label","Trailer");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","vehiculos");
	$atributo->set("foreignTableAlias","v2");
	$atributo->set("foreignLabelFields","v2.codigo||'/'||v2.placa");
	$atributo->set("sqlType","smallint");
	$atributo->set("inputType","select_dependiente");
	$atributo->set("fieldIdParent","id_centro");
	$atributo->set("namediv","id_trailer");
	$queryACargar2 = "SELECT v2.id, v2.codigo||'/'||v2.placa as nombre
		FROM vehiculos v2
		WHERE v2.id_tipo_vehiculo = 25 AND v2.id_centro='__%idARemp%__'
		ORDER BY v2.codigo, v2.placa";
	$atributo->set("qsQuery",$queryACargar2);
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","id_tipo_desplazamiento");
	$atributo->set("label","Tipo");
	$atributo->set("inputType","select");
	$atributo->set("foreignTable","rec.tipos_desplazamientos");
	$atributo->set("foreignLabelFields","rec.tipos_desplazamientos.tipo");
	$atributo->set("sqlType","integer");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);


	$atributo=new attribute($entidad);
	$atributo->set("field","hora_inicio");
	$atributo->set("label","Hora Inicio");
	$atributo->set("sqlType","timestamp");
	$atributo->set("inputType","timestamp");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","hora_fin");
	$atributo->set("label","Hora Fin");
	$atributo->set("sqlType","timestamp");
	$atributo->set("inputType","timestamp");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","numero_viaje");
	$atributo->set("label","Número viaje");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	$atributo=new attribute($entidad);
	$atributo->set("field","km_inicial");
	$atributo->set("label","Km Inicial");
	$atributo->set("sqlType","float");
	$atributo->set("mandatory",TRUE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);
	
	$atributo=new attribute($entidad);
	$atributo->set("field","km_final");
	$atributo->set("label","Km Final");
	$atributo->set("sqlType","float");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",TRUE);
	$atributo->set("searchable",TRUE);
	$atributo->set("browseable",TRUE);
	$atributo->set("shortList",TRUE);
	$entidad->addAttribute($atributo);

	
	$atributo=new attribute($entidad);
	$atributo->set("field","orden_micro");
	$atributo->set("label","Orden_micro");
	$atributo->set("sqlType","smallint");
	$atributo->set("mandatory",FALSE);
	$atributo->set("editable",FALSE);
	$atributo->set("searchable",FALSE);
	$atributo->set("browseable",FALSE);
	$atributo->set("shortList",FALSE);
	$entidad->addAttribute($atributo);


	$entidad->checkSqlStructure();

?>