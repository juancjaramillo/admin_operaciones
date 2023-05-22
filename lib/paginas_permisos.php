<?
/*
"1"=>"Administrador",
"13"=>"Administrador Regional",

"3"=>"Visor AVL x ciudad",
"4"=>"Visor AVL global",
"5"=>"Interventoría AVL", 

"2"=>"Radio Operador",
"6"=>"Jefe de Control",
"7"=>"Gerente Operaciones (observador)",
"8"=>"Gerente Operaciones (supervisor)",
"14"=>"Radio Operador Avanzado"

"9"=>"Planeador Mantenimiento",
"10"=>"Supervidor de Mantenimiento", (crear OTs, novedades, crear rutinas correctivas)
"11"=>"Gerente Mantenimiento",

"12"=>"Gerente General"
"15"=>"Gerente General Avanzado "
*/

$CFG->permisos = array(
	"perAdmin" => array(1,6,9,13),
	"perMtto" => array(1,2,6,9,10,11,12,13,14,15),
	"perOpera" => array(1,2,6,7,8,9,10,11,12,13,14,15),
	"perClientes" => array(1,3,12,13,15),
	"perCostos"=> array(1,12),
	"perCompras"=> array(1,12,13), 


	"calendario" => array(1,9,10,11,12,13,15),
	"bajar_excel_OT" => array(1,9,10,11,12,13,15),
	"arbol_rutinas"=>array(1,9,10,11,12,13,15),
	"rutinas"=>array(1,9,10,11,12,13,15),
	"llantas"=>array(1,9,10,11,13),
    "mttoNovedades"=>array(1,2,6,9,10,11,12,13,14,15),
	"agregarOT" => array(1,9,10,13),
	"busquedaOT"=> array(1,9,10,11,12,13,15),
	"ordenes_opciones" => array(1,9,10,11,13),
	"agregarGrupo"=> array(1,10,11,13),
	"Tablero_Control" => array(1,9,10,11,12,13,15),

	"agregarRutina"=> array(1,9,10,11,13),
	"arbol_equipos"=> array(1,9,10,11,13),
	"agregarEquipo"=> array(1,10,11,13),
	"agregarArchivoEquipo"=> array(1,10,11,13),
	"listado_rutinas"=> array(1,9,10,11,12,13,15),
	"rutinas_opciones"=> array(1,9,10,11,13),
	"listado_hoja_vida_vehiculo"=> array(1,2,6,7,8,9,10,11,12,13,14,15),
	"equiposdesdeHV"=> array(1,9,10,11,12,13,15),
	"editarEliminarGrupo"=>array(1,10,11,13),
	"agregarLlanta"=> array(1,9,10,11,13),
	"agregarMovimientoLlanta"=> array(1,9,10,11,13),
	"matrizRendimiento"=> array(1,9,10,11,12,13,15),
	"cargarArchivoInspeccionesLlantas"=> array(1,9,10,11,13),
	"eliminarAgregarMovimientoLlanta"=> array(1,9,10,11,13),
	"llantas_desmontadas_informe"=> array(),

	"actualizacioneskmyhr"=>array(1,2,6,8,9,10,11,12,13,14,15),
	"movimientos_rec"=>array(1,2,6,7,8,9,9,9,9,9,9,9,9,9,12,13,14,15),
	"movimientos_bar"=>array(1,2,7,8,12,13,14,15),
	"novedades_opera"=>array(1,2,6,7,8,12,13,14,15),
	"micros"=>array(1,6,7,8,12,13,14,15),
	"verOperacionDesdeAvl"=>array(1,2,7,8,9,10,11,12,13,14,15),
	"cerrar_movimiento"=>array(1,2,6,8,13,14),
	"agregar_desplazamiento"=>array(1,2,6,8,13,14),
	"agregar_movimiento_descuadrado"=>array(1,2,6,8,13,14),
	"listar_rutas_recoleccion_dia"=>array(1,2,6,8,13,14),
	"listar_movimientos"=>array(1,2,6,7,8,9,12,13,14,15),
	"reporte_dia_opera"=>array(1,2,6,7,8,9,12,13,14,15),
	"opciones_movimientos"=>array(1,2,6,8,13,14),
	"opciones_movimientos_pesos"=>array(1,2,6,8,13,14),
	"opciones_desplazamientos"=>array(1,2,6,8,13,14),
	"listar_rutas_barrido_dia"=>array(1,2,6,8,13,14),
	"listar_movimientos_barrido"=>array(1,2,6,7,8,9,12,13,14,15),
	"agregar_movimiento_barrido_descuadrado"=>array(1,2,6,8,13,14),
	"abrirMovimientoCerrado"=>array(1,6,13,14),
	"pantallaCapturaDiario"=>array(1,2,6,13,14),
	"cambiar_ruta_movimiento" =>array(1,2,6,14),
	"horarios_operarios" =>array(1,6,12,13,15),
	"cerrar_pesos" =>array(1,6),

	"opciones_novedades"=>array(1,2,6,8,9,10,11,12,13,14,15),
	"novedades_guardar_cerrada" => array(1,6,10,11,13),
	"buscarylistar_novedades"=>array(1,2,6,7,8,9,10,11,12,13,14,15),
	"novedades_agregar_OT" => array(1,9,10,13),

	"modulo_personas" => array(1,2,7,8,10,11,12,13,14,15),
	#"modulo_rec.pesos" => array(1,2,6,7,8,12,13,14,15),
	#"modulo_rec.pesos_sin_mov" => array(1,2,6,7,8,12,13,14,15),
	"modulo_rec.pesos" => array(1,13),
	"modulo_rec.pesos_sin_mov" => array(1,13),
	"modulo_mtto.excepciones_diarias" => array(1,9,10,11,13),
	"modulo_mtto.excepciones_periodos" => array(1,9,10,11,13),
	"modulo_mtto.motivos"=>array(1,9,10,11,13),
	"modulo_mtto.variables_mtto"=>array(1,10,11,13),
	"modulo_mtto.unidades"=>array(1,10,11,13),
	"modulo_llta.marcas"=>array(1,10,11,13),
	"modulo_llta.dimensiones"=>array(1,10,11,13),
	"modulo_llta.tipos_llantas"=>array(1,10,11,13),
	"modulo_llta.proveedores"=>array(1,10,11,12,13,15),
	"modulo_llta.tipos_movimientos"=>array(1,10,11,13),
	"modulo_llta.subtipos_movimientos"=>array(1,10,11,13),
	"modulo_bar.tipos_bolsas"=>array(1,8,10,11,13),
	"modulo_elementos_dotaciones"=>array(1,8,10,11,13),
	"modulo_dotaciones"=>array(1,8,10,11,13),
	"modulo_tipos_residuos"=>array(1,6,8,13),
	"modulo_cuartelillos"=>array(1,6,8,13),
	"modulo_ases"=>array(1,6,8,12,13,15),
	"modulo_peajes"=>array(1,6,8,13),
	"modulo_peajes_vigencias"=>array(1,6,8,13),
	"modulo_rec.tipos_desplazamientos"=>array(1),
	"modulo_lugares_descargue"=>array(1,6,8,13),
	"modulo_micros_segmentos"=>array(1,6,8,13),
	"modulo_micros_tipos_vehiculos"=>array(1,6,8,13),
	"modulo_peajes_micros"=>array(1,6,8,13),
	"modulo_peajes_micros_opcion_eliminar"=>array(1,6,13),
	"modulo_micros_puntos_control"=>array(1,6,13),
	"modulo_vehiculos_horarios"=>array(1),
	"modulo_rec.desplazamientos_trailer" => array(1,2,6,13,14),

	"agregar_micro"=>array(1,6,8,13),
	"buscar_micro"=>array(1,6,7,8,12,13,15),
	"opciones_micros"=>array(1,6,8,13)



);

//"modulo_rec.tipos_desplazamientos"=>array(1,6,8,13),

function verificarPagina($pag)
{
	global $CFG, $ME;
	
	$pag = str_replace(".php","",$pag);
	if(!in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos[$pag]))
	{
		$goto = $CFG->wwwroot."/avl"; 
		header("Location: $goto");
	}
}
?>
