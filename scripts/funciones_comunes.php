<?
function tipo($tipo){
GLOBAL $db;
	if($id_tipo=$db->sql_field("SELECT id FROM mtto.ele_tipos WHERE nombre='$tipo'")){
		return($id_tipo);
	}
	else{
		return($db->sql_insert("mtto.ele_tipos",array("nombre"=>$tipo)));
	}
}

function verificar_bodega($id_centro,$bodega_cod,$bodega_nombre){
GLOBAL $db;
	if($id_bodega=$db->sql_field("SELECT id FROM mtto.bodegas WHERE id_centro='$id_centro' AND codigo='$bodega_cod'")){
		return($id_bodega);
	}
	else{
		return($db->sql_insert("mtto.bodegas",array("id_centro"=>$id_centro,"codigo"=>$bodega_cod,"nombre"=>$bodega_nombre)));
	}
}

function verificar_existencias($id_elemento,$centro,$bodega_cod,$bodega_nombre,$unidades,$costo){
GLOBAL $db,$CFG;

	if($centro=="" && sizeof($CFG->array_centros)==1) $id_centro=$CFG->array_centros[0];
	else $id_centro=$db->sql_field("SELECT id FROM centros WHERE codigo='$centro' AND id IN (" . implode($CFG->array_centros,",") . ")");

	$id_bodega=verificar_bodega($id_centro,$bodega_cod,$bodega_nombre);
	
	if($id_existencia=$db->sql_field("
			SELECT id FROM mtto.elementos_existencias
			WHERE id_elemento='$id_elemento' AND id_bodega='$id_bodega'
	")){
		$qUpdate=$db->sql_query("UPDATE mtto.elementos_existencias SET existencia='$unidades', precio='$costo' WHERE id='$id_existencia'");
	}
	else{
		$existencia["id_bodega"]=$id_bodega;
		$existencia["id_elemento"]=$id_elemento;
		$existencia["existencia"]=$unidades;
		$existencia["precio"]=$costo;

		$qInsert=$db->sql_insert("mtto.elementos_existencias",$existencia);
	}

}

function unidades($unidad){
GLOBAL $db;
	if($id_unidad=$db->sql_field("SELECT id FROM mtto.unidades WHERE unidad='$unidad'")){
		return($id_unidad);
	}
	else{
		return($db->sql_insert("mtto.unidades",array("unidad"=>$unidad)));
	}
}

?>
