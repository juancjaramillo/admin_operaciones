<table width="100%">
	<tr>
		<td height="30">&nbsp;</td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
				<tr>
					<td>
						<table width="100%">
							<?if(simple_me($ME)=="calendario.php"){
							if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["agregarOT"])){?>
							<tr>
								<td align="center"><img alt='Agregar Orden Trabajo' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/herramienta_fondo.gif" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('ordenes','900','500','<?=$CFG->wwwroot?>/mtto/ordenes.php?mode=agregar_facil')" style="color:#506f77;text-decoration: none">Agregar Orden de Trabajo</a></td>
							</tr>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>
							<tr>
								<td align="center"><img alt='Imprimir OT´s Agrupadas' src="<?=$CFG->wwwroot?>/files/mtto.prioridades/imagen/5" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('ordenes','900','500','<?=$CFG->wwwroot?>/mtto/templates/busqueda_ordenes_imprimir.php')" style="color:#506f77;text-decoration: none">Imprimir OT's Agrupadas</a></td>
							</tr>
							<tr>
								<td align="center"><img alt='Consultar Ultimos Preventivos' src="<?=$CFG->wwwroot?>/files/mtto.prioridades/imagen/ultimo" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('ordenes','900','500','<?=$CFG->wwwroot?>/mtto/templates/busqueda_ultimo_preventivo.php')" style="color:#506f77;text-decoration: none">Consultar Ultimos Preventivos</a></td>
							</tr>
							<tr>
								<td align="center"><img alt='Programación Personal Fin de Semana' src="<?=$CFG->wwwroot?>/files/mtto.prioridades/imagen/meca" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('ordenes','900','500','<?=$CFG->wwwroot?>/mtto/templates/prog_tecnicos.php')" style="color:#506f77;text-decoration: none">Prog.								Técnicos Fin de Semana</a></td>
							</tr>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["busquedaOT"])){?>
							<tr>
								<td align="center"><img alt='Buscar' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/lupa.jpeg" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('busq_ordenes','600','500','<?=$CFG->wwwroot?>/mtto/templates/busqueda_ordenes_form.php')" style="color:#506f77;text-decoration: none">Buscar Ordenes Trabajo</a></td>
							</tr>
							<?}?>
							<tr>
								<td align="center"><img alt='Cerradas' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/check_green.png" border="0"></td>
								<td><a href="<?=$CFG->wwwroot?>/mtto/calendario.php?mode=listado_mensual&abiertas=0" style="color:#506f77;text-decoration: none">Listar Ordenes Cerradas</a></td>
							</tr>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>
							<?if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_mtto.excepciones_diarias"])){?>
							<tr>
								<td align="center"><img alt='Excepciones Diarias' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/calendar_16.gif" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('modulos','600','500','<?=$CFG->wwwroot?>/mtto/modules.php?module=mtto.excepciones_diarias')" style="color:#506f77;text-decoration: none">Excepciones Diarias</a></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_mtto.excepciones_periodos"])){?>								
							<tr>
								<td align="center"><img alt='Excepciones Periodos' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/calendar_16.gif" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('modulos','600','500','<?=$CFG->wwwroot?>/mtto/modules.php?module=mtto.excepciones_periodos')" style="color:#506f77;text-decoration: none">Excepciones Periodos</a></td>
							</tr>
							<?}?>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>
							<?if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_mtto.motivos"])){?>
							<tr>
								<td align="center"><img alt='Motivos' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/llaveydestonillador.jpg" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('modulos','600','500','<?=$CFG->wwwroot?>/mtto/modules.php?module=mtto.motivos')" style="color:#506f77;text-decoration: none">Motivos de la Orden</a></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_mtto.variables_mtto"])){?>
							<tr>
								<td align="center"><img alt='Variables' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/variable.png" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('modulos','600','500','<?=$CFG->wwwroot?>/mtto/modules.php?module=mtto.variables_mtto')" style="color:#506f77;text-decoration: none">Variables del Mantenimiento</a></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_mtto.unidades"])){?>
							<tr>
								<td align="center"><img alt='Unidades' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/balance.gif" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('modulos','600','500','<?=$CFG->wwwroot?>/mtto/modules.php?module=mtto.unidades')" style="color:#506f77;text-decoration: none">Unidades</a></td>
							</tr>
							<?}?>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>
							<tr>
								<td colspan=2>
									<table width='100%'>
										<tr>
											<th colspan=2 height=30 valign="center">CONVENCIONES</th>
										</tr>
										<tr>
											<td width="10%" bgcolor='#d24747'></td><td height=30 valign="center">Órdenes atrasadas</td>
										</tr>
										<tr>
											<td width="10%" bgcolor='#f48e2b'></td><td  height=30 valign="center">Órdenes reprogramadas</td>
										</tr>
										<tr>
											<td width="10%" bgcolor='#9d3577'></td><td height=30 valign="center">Órdenes atrasadas y reprogramadas</td>
										</tr>
										<tr>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<td width="10%"><img alt='Prioridad' src="<?=$CFG->wwwroot?>/files/mtto.prioridades/imagen/1"  border='0'></td><td>Prioridad Alta</td>
										</tr>
										<tr>
											<td width="10%"><img alt='Prioridad' src="<?=$CFG->wwwroot?>/files/mtto.prioridades/imagen/2"  border='0'></td><td>Prioridad Media</td>
										</tr>
										<tr>
											<td width="10%"><img alt='Prioridad' src="<?=$CFG->wwwroot?>/files/mtto.prioridades/imagen/3"  border='0'></td><td>Prioridad Baja</td>
										</tr>
										<tr>
											<td width="10%" style='font:bold 10px Verdana, Arial, Helvetica, sans-serif; font-style: italic;'>NA</td><td>Novedades Abiertas</td>
										</tr>
									</table>
								</td>
							</tr>
							<?}elseif(simple_me($ME)=="llantas.php"){
							if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["agregarMovimientoLlanta"])){?>
							<tr>
								<td align="center"><img alt='Agregar Movimiento' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-add.gif" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('listar_movimientos','800','500','<?=$CFG->wwwroot?>/mtto/templates/ingreso_movimiento_facil_form.php')" style="color:#506f77;text-decoration: none">Agregar Movimiento</a></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["llantas"])){?>
							<tr>
								<td align="center"><img alt='Listar Movimientos' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-overview.gif" border="0"></td>
								<td><a href="<?=$CFG->wwwroot?>/mtto/llantas.php?mode=listar_movimientos" style="color:#506f77;text-decoration: none">Listar Movimientos</a></td>
							</tr>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["agregarLlanta"])){?>
							<tr>
								<td align="center"><img alt='Agregar Llanta' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-add.gif" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('llanta_form','800','500','<?=$CFG->wwwroot?>/mtto/llantas.php?mode=agregar')" style="color:#506f77;text-decoration: none">Agregar Llanta</a></td>
							</tr>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["llantas"])){?>
							<tr>
								<td align="center"><img alt='Buscar' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/lupa.jpeg" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('busq_llantas','600','500','<?=$CFG->wwwroot?>/mtto/templates/busqueda_llantas_form.php')" style="color:#506f77;text-decoration: none">Buscar Llantas</a></td>
							</tr>
							<tr>
								<td align="center"><img alt='LLantas Montadas' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/lupa.jpeg" border="0"></td>
								<td><a href="<?=$CFG->wwwroot?>/mtto/llantas.php?mode=listado_llantas&id_estado=1" style="color:#506f77;text-decoration: none">Listar Llantas Montadas</a></td>
							</tr>
							<tr>
								<td align="center"><img alt='LLantas Desmontadas' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/lupa.jpeg" border="0"></td>
								<td><a href="<?=$CFG->wwwroot?>/mtto/llantas.php?mode=listado_llantas&id_estado=2" style="color:#506f77;text-decoration: none">Listar Llantas Desmontadas</a></td>
							</tr>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_llta.marcas"])){?>
							<tr>
								<td align="center"><img alt='Marcas' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/bookmarks.png" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('modulos','600','500','<?=$CFG->wwwroot?>/mtto/modules.php?module=llta.marcas')" style="color:#506f77;text-decoration: none">Marcas de Llantas</a></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_llta.dimensiones"])){?>
							<tr>
								<td align="center"><img alt='Referencias' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/llanta.jpeg" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('modulos','600','500','<?=$CFG->wwwroot?>/mtto/modules.php?module=llta.dimensiones')" style="color:#506f77;text-decoration: none">Dimensiones de Llantas</a></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_llta.tipos_llantas"])){?>
							<tr>
								<td align="center"><img alt='Tipos' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/columnas.png" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('modulos','600','500','<?=$CFG->wwwroot?>/mtto/modules.php?module=llta.tipos_llantas')" style="color:#506f77;text-decoration: none">Tipos de Llantas</a></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_llta.proveedores"])){?>
							<tr>
								<td align="center"><img alt='Proveedores' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/grupo.jpeg" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('modulos','600','500','<?=$CFG->wwwroot?>/mtto/modules.php?module=llta.proveedores')" style="color:#506f77;text-decoration: none">Proveedores</a></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_llta.tipos_movimientos"])){?>
							<tr>
								<td align="center"><img alt='Tipos Movimientos' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/state2.gif" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('modulos','600','500','<?=$CFG->wwwroot?>/mtto/modules.php?module=llta.tipos_movimientos')" style="color:#506f77;text-decoration: none">Tipos Movimientos</a></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_llta.subtipos_movimientos"])){?>
							<tr>
								<td align="center"><img alt='SubTipos Movimientos' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/state3.jpeg" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('modulos','600','500','<?=$CFG->wwwroot?>/mtto/modules.php?module=llta.subtipos_movimientos')" style="color:#506f77;text-decoration: none">Subtipos Movimientos</a></td>
							</tr>
							<?}?>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>
							<?if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["matrizRendimiento"])){?>
							<tr>
								<td align="center"><img alt='Marcas' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/bookmarks.png" border="0"></td>
								<td><a href="consultas.php?id=1&format=xls" style="color:#506f77;text-decoration: none">Matriz de rendimiento</a></td>
							</tr>
							<tr>
								<td align="center"><img alt='Marcas' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/bookmarks.png" border="0"></td>
								<td><a href="consultas.php?id=2&format=xls" style="color:#506f77;text-decoration: none">Por Vehículo</a></td>
							</tr>
								<?if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"] == 1){?>
							<tr>
								<td align="center"><img alt='Marcas' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/bookmarks.png" border="0"></td>
								<td><a href="consultas.php?id=3&format=xls" style="color:#506f77;text-decoration: none">Por Llanta</a></td>
							</tr>

								<?}?>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["cargarArchivoInspeccionesLlantas"])){?>
							<tr>
								<td align="center"><img alt='Marcas' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/bookmarks.png" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('subir_inspecciones','600','500','<?=$CFG->wwwroot?>/mtto/subir_inspecciones.php')" style="color:#506f77;text-decoration: none">Cargar archivo de inspecciones</a></td>
							</tr>
							<?}
							}elseif(simple_me($ME)=="novedades.php"){
							if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["opciones_novedades"])){?>
							<tr>
								<td align="center"><img alt='Novedades' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/estrella.gif" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('novedades','800','500','<?=$CFG->wwwroot?>/novedades/novedades.php?mode=agregar&clase=<?=$clase?>')" style="color:#506f77;text-decoration: none">Agregar Novedad</a></td>
							</tr>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["buscarylistar_novedades"])){?>
							<tr>
								<td align="center"><img alt='Buscar Novedades' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/lupa.jpeg" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('novedades','600','500','<?=$CFG->wwwroot?>/novedades/novedades.php?mode=buscar&clase=<?=$clase?>')" style="color:#506f77;text-decoration: none">Buscar Novedades</a></td>
							</tr>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>
							<tr>
								<td align="center"><img alt='Listar Novedades Abiertas' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/gear.png" border="0"></td>
								<td><a href="<?=$CFG->wwwroot?>/novedades/novedades.php?clase=<?=$clase?>" style="color:#506f77;text-decoration: none">Listar Novedades Abiertas</a></td>
							</tr>
							<tr>
								<td align="center"><img alt='Listar Novedades Cerradas' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/check_green.png" border="0"></td>
								<td><a href="<?=$CFG->wwwroot?>/novedades/novedades.php?mode=listado&abierta=f&clase=<?=$clase?>" style="color:#506f77;text-decoration: none">Listar Novedades Cerradas</a></td>
							</tr>
							<?}
							}elseif(simple_me($ME)=="movimientos_rec.php"){
							if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["cerrar_movimiento"])){?>
							<tr>
								<td align="center"><img alt='Cerrar Movimiento' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/check_green.png" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('movimientos',400,400,'<?=$CFG->wwwroot?>/opera/templates/cerrar_movimiento_rec_form.php?fecha=<?=nvl($fecha)?>')" style="color:#506f77;text-decoration: none">Cerrar Movimiento</a></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["agregar_desplazamiento"])){?>
							<tr>
								<td align="center"><img alt='Agregar Desplazamiento' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-add.gif" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('movimientos',500,500,'<?=$CFG->wwwroot?>/opera/templates/agregar_desplazamiento_busq_form.php?fecha=<?=nvl($fecha)?>')" style="color:#506f77;text-decoration: none">Agregar Desplazamiento</a></td>
							</tr>
							<tr>
								<td align="center"><img alt='Agregar Desplazamiento 2' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-add.gif" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('movimientos',500,500,'<?=$CFG->wwwroot?>/opera/templates/agregar_desplazamiento2.php?fecha=<?=nvl($fecha)?>')" style="color:#506f77;text-decoration: none">Agregar Desplazamiento Modo 2</a></td>
							</tr>
							<?}?>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>
							<?if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["agregar_movimiento_descuadrado"])){?>
							<tr>
								<td align="center"><img alt='Agregar Movimiento' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/camino.jpeg" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('movimientos',500,300,'<?=$CFG->wwwroot?>/opera/templates/movimiento_descuadrado_form.php?esquema=rec&fecha=<?=nvl($fecha)?>')" style="color:#506f77;text-decoration: none">Agregar Movimiento (sin frecuencia de hoy)</a></td>
							</tr>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["pantallaCapturaDiario"])){?>
							<tr>
								<td align="center"><img alt='captura diario' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/bookmarks.png" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('movimientos',900,700,'<?=$CFG->wwwroot?>/opera/captura_diario.php?fecha=<?=nvl($fecha)?>')" style="color:#506f77;text-decoration: none">Captura Diario</a></td>
							</tr>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>

							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_rec.pesos"])){?>
							<tr>
								<td align="center"><img alt='Pesos' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/balance.gif" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('modulos','800','500','<?=$CFG->wwwroot?>/mtto/modules.php?module=rec.pesos')" style="color:#506f77;text-decoration: none">Pesos (todos)</a></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_rec.pesos_sin_mov"])){?>
							<tr>
								<td align="center"><img alt='Pesos' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/balance.gif" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('modulos','800','500','<?=$CFG->wwwroot?>/mtto/modules.php?module=rec.pesos_sin_mov')" style="color:#506f77;text-decoration: none">Pesos (sin movimientos)</a></td>
							</tr>
							<?}?>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>
							<?if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["listar_rutas_recoleccion_dia"])){?>
							<tr>
								<td align="center"><img alt='Listar rutas de Recolección del día' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/gear.png" border="0"></td>
								<td><a href="<?=$CFG->wwwroot?>/opera/movimientos_rec.php?mode=listado_micros&esquema=rec" style="color:#506f77;text-decoration: none">Listar Rutas de Recolección del Día</a></td>
							</tr>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["listar_movimientos"])){?>
							<tr>
								<td align="center"><img alt='Listar Movimientos Abiertos' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/gear.png" border="0"></td>
								<td><a href="<?=$CFG->wwwroot?>/opera/movimientos_rec.php?esquema=rec&fecha=<?=$fecha?>&estado=abierta" style="color:#506f77;text-decoration: none">Listar Mov. Abiertos</a></td>
							</tr>
							<tr>
								<td align="center"><img alt='Listar Movimientos Cerrados' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/check_green.png" border="0"></td>
								<td><a href="<?=$CFG->wwwroot?>/opera/movimientos_rec.php?esquema=rec&fecha=<?=$fecha?>&estado=cerrada" style="color:#506f77;text-decoration: none">Listar Mov. Cerrados</a></td>
							</tr>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["reporte_dia_opera"])){?>
							<tr>
								<td align="center"><img alt='Reporte del Día' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/ico_articulos.gif" border="0"></td>
								<td><a href="<?=$CFG->wwwroot?>/opera/movimientos_rec.php?esquema=rec&fecha=<?=$fecha?>&mode=reporte_dia" style="color:#506f77;text-decoration: none">Reporte del Día</a></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_vehiculos_horarios"])){?>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>
							<tr>
								<td align="center"><img alt='Vehículos Horarios Laborables' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/cal_dos.jpg" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('modulos','600','500','<?=$CFG->wwwroot?>/mtto/modules.php?module=vehiculos_horarios')" style="color:#506f77;text-decoration: none">Horarios Laborables - Vehículos</a></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_rec.desplazamientos_trailer"])){?>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>
							<tr>
								<td align="center"><img alt='Desplazamientos del Trailer' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/v_campero.png" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('modulos','800','400','<?=$CFG->wwwroot?>/mtto/modules.php?module=rec.desplazamientos_trailer&fecha=<?=$fecha?>')" style="color:#506f77;text-decoration: none">Desplazamientos Trailer</a></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["horarios_operarios"])){?>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>
							<tr>
								<td align="center"><img alt='horarios' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/grupo.jpeg" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('movimientos',500,500,'<?=$CFG->wwwroot?>/opera/informe_horarios_operarios.php?esquema=rec&fecha=<?=$fecha?>')" style="color:#506f77;text-decoration: none">Horarios Operarios</a></td>
							</tr>
							<?}}elseif(simple_me($ME)=="movimientos_bar.php"){
							if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["listar_rutas_barrido_dia"])){?>
							<tr>
								<td align="center"><img alt='Listar Rutas de Barrido del día' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/gear.png" border="0"></td>
								<td><a href="<?=$CFG->wwwroot?>/opera/movimientos_bar.php?mode=listado_micros&esquema=bar" style="color:#506f77;text-decoration: none">Listar Rutas de Barrido del Día</a></td>
							</tr>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["listar_movimientos_barrido"])){?>
							<tr>
								<td align="center"><img alt='Recursos Abiertos' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/gear.png" border="0"></td>
								<td><a href="<?=$CFG->wwwroot?>/opera/movimientos_bar.php?esquema=bar&fecha=<?=$fecha?>&estado=abierta" style="color:#506f77;text-decoration: none">Listar Recursos Abiertos</a></td>
							</tr>
							<tr>
								<td align="center"><img alt='Recursos Cerrados' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/check_green.png" border="0"></td>
								<td><a href="<?=$CFG->wwwroot?>/opera/movimientos_bar.php?esquema=bar&fecha=<?=$fecha?>&estado=cerrada" style="color:#506f77;text-decoration: none">Listar Recursos Cerrados</a></td>
							</tr>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["agregar_movimiento_barrido_descuadrado"])){?>
							<tr>
								<td align="center"><img alt='Agregar Movimiento' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/camino.jpeg" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('movimientos',500,300,'<?=$CFG->wwwroot?>/opera/templates/movimiento_descuadrado_form.php?esquema=bar&fecha=<?=nvl($fecha)?>')" style="color:#506f77;text-decoration: none">Agregar Movimiento (sin frecuencia de hoy)</a></td>
							</tr>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_bar.tipos_bolsas"])){?>
							<tr>
								<td align="center"><img alt='Tipos Bolsas' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/bolsa.jpeg" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('modulos','600','500','<?=$CFG->wwwroot?>/mtto/modules.php?module=bar.tipos_bolsas')" style="color:#506f77;text-decoration: none">Tipos Bolsas</a></td>
							</tr>
							<?}?>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>
							<?if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_elementos_dotaciones"])){?>
							<tr>
								<td align="center"><img alt='Elementos Dotación' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/herramienta_fondo.gif" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('modulos','600','500','<?=$CFG->wwwroot?>/mtto/modules.php?module=elementos_dotaciones')" style="color:#506f77;text-decoration: none">Elementos Dotación</a></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_dotaciones"])){?>
							<tr>
								<td align="center"><img alt='Dotación' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/kgpg_identity.png" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('modulos','600','500','<?=$CFG->wwwroot?>/mtto/modules.php?module=dotaciones')" style="color:#506f77;text-decoration: none">Dotaciones</a></td>
							</tr>
							<?}
							}elseif(simple_me($ME)=="micros.php"){
							if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["agregar_micro"])){?>
							<tr>
								<td align="center" height=25><img alt='Rutas' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/ruta.jpeg" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('micros','500','500','<?=$CFG->wwwroot?>/opera/micros.php?mode=agregar')" style="color:#506f77;text-decoration: none">Agregar Ruta</a></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["buscar_micro"])){?>
							<tr>
								<td align="center" height=25><img alt='Buscar' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/lupa.jpeg" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('busq_ordenes','600','500','<?=$CFG->wwwroot?>/opera/templates/busqueda_micros_form.php')" style="color:#506f77;text-decoration: none">Buscar Rutas</a></td>
							</tr>
							<?}?>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>
							<?if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_tipos_residuos"])){?>
							<tr>
								<td align="center" height=25><img alt='Tipos Bolsas' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/tipo_residuo.jpeg" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('modulos','600','500','<?=$CFG->wwwroot?>/mtto/modules.php?module=tipos_residuos')" style="color:#506f77;text-decoration: none">Tipos Residuos</a></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_cuartelillos"])){?>
							<tr>
								<td align="center" height=25><img alt='Cuartelillos' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-move-zone.gif" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('modulos','600','500','<?=$CFG->wwwroot?>/mtto/modules.php?module=cuartelillos')" style="color:#506f77;text-decoration: none">Cuartelillos</a></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_ases"])){?>
							<tr>
								<td align="center" height=25><img alt='Cuartelillos' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/kwikdisk.gif" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('modulos','600','500','<?=$CFG->wwwroot?>/mtto/modules.php?module=ases')" style="color:#506f77;text-decoration: none">Areas de Prestación Servicios</a></td>
							</tr>
							<?}?>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>
							<?if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_peajes"])){?>
							<tr>
								<td align="center" height=25><img alt='Peajes' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/ico-peaje.jpeg" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('modulos','600','500','<?=$CFG->wwwroot?>/mtto/modules.php?module=peajes')" style="color:#506f77;text-decoration: none">Peajes</a></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_peajes_vigencias"])){?>
							<tr>
								<td align="center" height=25><img alt='Vigencia Peajes' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/calendar_16.gif" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('modulos','600','500','<?=$CFG->wwwroot?>/mtto/modules.php?module=peajes_vigencias')" style="color:#506f77;text-decoration: none">Vigencia de Peajes</a></td>
							</tr>
							<?}?>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>
							<?if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_rec.tipos_desplazamientos"])){?>
							<tr>
								<td align="center" height=25><img alt='SubTipos Movimientos' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/state3.jpeg" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('modulos','600','500','<?=$CFG->wwwroot?>/mtto/modules.php?module=rec.tipos_desplazamientos')" style="color:#506f77;text-decoration: none">Tipos Desplazamientos</a></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_lugares_descargue"])){?>
							<tr>
								<td align="center" height=25><img alt='Lugares Descargue' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/download.gif" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('modulos','600','500','<?=$CFG->wwwroot?>/mtto/modules.php?module=lugares_descargue')" style="color:#506f77;text-decoration: none">Lugares de Descargue</a></td>
							</tr>
							<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["modulo_puntos_control"])){?>
							<tr>
								<td colspan=2><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td>
							</tr>
							<tr>
								<td align="center" height=25><img alt='Puntos de Interés' src="<?=$CFG->wwwroot?>/admin/iconos/transparente/bookmarks.png" border="0"></td>
								<td><a href="javascript:abrirVentanaJavaScript('modulos','600','500','<?=$CFG->wwwroot?>/mtto/modules.php?module=puntos_control')" style="color:#506f77;text-decoration: none">Puntos de Interés</a></td>
							</tr>
							<?}


							}?>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
