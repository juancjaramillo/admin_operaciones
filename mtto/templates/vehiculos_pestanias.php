<form class="form">
	<table width="100%">
		<tr>
			<td height="40" colspan=3 align="center"><span class="azul_16"><strong><?=$titulo?></strong></span></td>
		</tr>
		<tr>
			<td valign="top">
				<table width="100%" cellpadding="5" cellspacing="3" border =0>
					<tr>
						<td>
							<table width="100%">
								<tr>
									<td align="left">
										<a class="<?if($botonActive=="generales") echo "boton_verde_active"; else echo "boton_verde"?>" href="<?=$CFG->wwwroot?>/mtto/listado_hoja_vida_vehiculo.php?mode=detalles&id=<?=$idVehiculo?>" title="Datos Generales">Datos Generales</a>
										<?
										if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["equiposdesdeHV"])){
											$qidEq = $db->sql_query("SELECT id, nombre FROM mtto.equipos WHERE id_vehiculo=".$idVehiculo." ORDER BY nombre");
											while($eq = $db->sql_fetchrow($qidEq))
											{
												$class = "boton_verde";
												if($botonActive=="eq_".$eq["id"]) $class = "boton_verde_active"; 
												echo '&nbsp;&nbsp;&nbsp;<a class="'.$class.'" href="'.$CFG->wwwroot.'/mtto/listado_hoja_vida_vehiculo.php?mode=detalles_equipo&idEquipo='.$eq["id"].'" title="'.$eq["nombre"].'">Equipo '.$eq["nombre"].'</a>';
											}
										}
										if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["novedades_opera"])){?>
										&nbsp;&nbsp;&nbsp;<a class="<?if($botonActive=="novOperacion") echo "boton_verde_active"; else echo "boton_verde"?>" href="<?=$CFG->wwwroot?>/mtto/listado_hoja_vida_vehiculo.php?mode=novedadesOperacion&id=<?=$idVehiculo?>" title="Novedades Operación">Novedades Operación</a>
                    &nbsp;&nbsp;&nbsp;<a class="<?if($botonActive=="hisOperacion") echo "boton_verde_active"; else echo "boton_verde"?>" href="<?=$CFG->wwwroot?>/mtto/listado_hoja_vida_vehiculo.php?mode=hisOperacion&id=<?=$idVehiculo?>" title="Historico Operación">Historico Operación</a>				
            				<?}?>
									</td>
								</tr>
							</table>
						</td>
					</tr>
						<td>
							<table width="100%" border=1 bordercolor="#7fa840" align="center" id="tabla_mov">
								<tr>
									<td><?=$datos?></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</form>
