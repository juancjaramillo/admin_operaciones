<form name="entryform" action="<?=$ME?>" method="POST" onSubmit="return revisar()" class="form">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id" value="<?=nvl($orden["id"])?>">
<input type="hidden" name="id_orden_trabajo_origen" value="<?=nvl($frm["id_orden_trabajo_origen"])?>">
<input type="hidden" name="id_novedad" value="<?=nvl($orden["id_novedad"])?>">
<input type="hidden" name="accion" value="">
<?php

$kmAc = $db->sql_row("SELECT kilometraje as km FROM mtto.equipos WHERE id=".nvl($orden["id_equipo"],0));
$horoAc = $db->sql_row("SELECT horometro as horo FROM mtto.equipos WHERE id=".nvl($orden["id_equipo"],0));	

$idCentro = $db->sql_row("SELECT e.id_centro 
						FROM mtto.equipos e
										WHERE e.id=".nvl($orden["id_equipo"],0));

if (($idCentro["id_centro"]==1 || $idCentro["id_centro"]==2) and ($orden[km]=='' and $orden[fecha_ejecucion_fin]==''))
{
	$orden[km]=((int) ($kmAc[km]));
	$orden[horometro]=((int) ($horoAc[horo]));
	$orden["fecha_ejecucion_inicio"]=$orden["fecha_planeada"];
}



?>

<table width="100%">
	<tr>
		<td height="40" colspan=3 class="azul_16"><strong>ORDEN DE TRABAJO No. <?echo "  ".$orden["id"]?></strong></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840">
							<?if(isset($sistemas)){?>
							<tr>
								<td align='right'>(*) Sistema</td>
								<td align='left'>
									<select  name='id_sistema' onChange="updateRecursive_id_rutina()"><option value="%">Seleccione...</option><?=$sistemas?></select> 
								</td>
							</tr>
							<?}?>			
							<tr>
								<td align='right'>(*) Rutina</td>
								<td align='left'>
								<?if($newMode == "actualizar"){
									echo "<input type=\"hidden\" name=\"id_creador\" value=\"".$orden["id_creador"]."\">\n";
									echo "<input type=\"hidden\" name=\"id_rutina\" value=\"".$orden["id_rutina"]."\">\n";
									echo "<input type=\"hidden\" name=\"id_equipo\" value=\"".$orden["id_equipo"]."\">\n";
									echo $orden["rutina"];
								}else{?>
									<div id="id_rutina"><select  name='id_rutina' id="id_rutina" style="width:250px" onChange="updateRecursive_id_equipo(this), updateRecursive_id_responsable(this), updateRecursive_id_planeador(this), updateRecursive_id_ingreso_ejecutada(this)"><?=$rutinas?></select>&nbsp;&nbsp;<a href="javascript:abrirVentanaJavaScript('rutinafo','1100','500','<?=$CFG->wwwroot?>/mtto/rutinas.php?mode=agregar')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-add.gif' border='0'></a></div> 
								<?}?>			
								</td>
							</tr>
							<tr>
								<td align='right'>(*) Equipo</td>
								<td align='left'> 
								<?if($newMode == "actualizar"){
									echo $orden["equipo"];
								}else{?>
									<div id="id_equipo"><select  name="id_equipo" id="id_equipo" style="width:250px"><option value="%">Seleccione...</option><?=nvl($equipos)?></select></div>
								<?}?>
								</td>
							</tr>
							<tr>
								<td align='right'>Motivo</td>
								<td align='left'><select  name='id_motivo'><option value='%'>Seleccione...</option><?=$options?></select></td>
							</tr>
							<tr>
								<td align='right'>(*) Fecha Planeada</td>
								<td align='left'>
									<input size="20" id="f_fecha_planeada" class="casillatext_fecha" name='fecha_planeada' value='<?=nvl($orden["fecha_planeada"])?>' /><button id="b_fecha_planeada" onclick="javascript:showCalendarHora('f_fecha_planeada','b_fecha_planeada')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
									&nbsp;&nbsp;<a href="javascript:abrirVentanaJavaScript('historicootinfo','500','200','<?=$CFG->wwwroot?>/mtto/ordenes.php?mode=historicoFechasProgramacion&id_orden_trabajo=<?=$orden["id"]?>')"><img alt="Histórico" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/camino.jpeg' border='0'></a>
								</td>
							</tr>
							<tr>
								<td align='right'>Fecha Ejecución Inicio</td>
								<td align='left'>
									<input size="20" id="f_fecha_ejecucion_inicio" class="casillatext_fecha" name='fecha_ejecucion_inicio' value='<?=nvl($orden["fecha_ejecucion_inicio"])?>' /><button id="b_fecha_ejecucion_inicio" onclick="javascript:showCalendarHora('f_fecha_ejecucion_inicio','b_fecha_ejecucion_inicio')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>Fecha Ejecución Fin</td>
								<td align='left'>
									<input size="20" id="f_fecha_ejecucion_fin" class="casillatext_fecha" name='fecha_ejecucion_fin' value='<?=nvl($orden["fecha_ejecucion_fin"])?>' /><button id="b_fecha_ejecucion_fin" onclick="javascript:showCalendarHora('f_fecha_ejecucion_fin','b_fecha_ejecucion_fin')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>Responsable</td>
								<td align='left'><div id="id_responsable"><select  name="id_responsable" id="id_responsable" style="width:250px"><?=nvl($responsable)?></select></div> </td>
							</tr>
							<tr>
								<td align='right'>Creador</td>
								<td align='left'>
								<?if($newMode == "actualizar"){
									echo $creador["nombre"];
								}else{
									echo "<input type=\"hidden\" name=\"id_creador\" value=\"".$creador["id"]."\">\n";
									echo $creador["nombre"];
								}?>
								</td>
							</tr>
							<tr>
								<td align='right'>Planeador</td>
								<td align='left'><div id="id_planeador"><select  name="id_planeador" id="id_planeador" style="width:250px"><?=nvl($planeador)?></select></div> </td>
							</tr>
							<tr>
								<td align='right'>Ingresó Ejecutada</td>
								<td align='left'>
									<?if(nvl($orden["id_ingreso_ejecutada"],"") == ""){?>
										<div id="id_ingreso_ejecutada"><select  name="id_ingreso_ejecutada" id="id_ingreso_ejecutada" style="width:250px"><?=nvl($ingreso_ejecutada)?></select></div> 
									<?}else{
										echo "<input type=\"hidden\" name=\"id_ingreso_ejecutada\" value=\"".$ingreso_ejecutada["id"]."\">\n";
										echo $ingreso_ejecutada["nombre"];
									}?>
								</td>
							</tr>
							<tr>
								<td align='right'>(*) Estado</td>
								<td align='left'><select  name='id_estado_orden_trabajo'><?=$estado?></select></td>
							</tr>
							<tr>
								<td align='right'>Km</td>
								<td align='left'><input type='text' size='20' class="casillatext"  name='km' value='<?=nvl($orden["km"])?>'>
								<?if($newMode != "insertar" && $newMode != "insertar_relacionada_correctiva") {
										$kmAc = $db->sql_row("SELECT kilometraje as km FROM mtto.equipos WHERE id=".$orden["id_equipo"]);									
										echo "<br />Valor Actual : ".number_format($kmAc["km"],0);
									}?>
								</td>
							</tr>
							<tr>
								<td align='right'>Horómetro</td>
								<td align='left'><input type='text' size='20' class="casillatext" name='horometro' value='<?=nvl($orden["horometro"])?>'>
								<?if($newMode != "insertar"  && $newMode != "insertar_relacionada_correctiva") {
										$horoAc = $db->sql_row("SELECT horometro as horo FROM mtto.equipos WHERE id=".$orden["id_equipo"]);									
										echo "<br />Valor Actual : ".number_format($horoAc["horo"],0);
									}?>
								</td>
							</tr>
							<tr>
								<td align='right'>IEV</td>
								<td align='left'><input type='text' size='20' class="casillatext"  name='iev' value='<?=nvl($orden["iev"])?>'>
							</tr>
							<tr>
								<td align='right'>Herramientas</td>
								<td align='left'><textarea   name='herramientas'><?=nvl($orden["herramientas"])?></textarea></td>
							</tr>
							<tr>
								<td align='right'>Observaciones</td>
								<td align='left'><textarea   name='observaciones'><?=nvl($orden["observaciones"])?></textarea></td>
							</tr>
							<?if($newMode != "insertar" && $newMode != "insertar_relacionada_correctiva"){
									while($campo=$db->sql_fetchrow($qCampos)){
										$qResultado=$db->sql_query("SELECT * FROM mtto.ordenes_rutinas_mediciones WHERE id_orden_trabajo=".$orden["id"]." AND id_medicion='".$campo["id"]."'");
										if($resultado=$db->sql_fetchrow($qResultado)) $VALUE=$resultado["resultado"];
										else{
											$qInsert=$db->sql_query("INSERT INTO mtto.ordenes_rutinas_mediciones (id_orden_trabajo,id_medicion) VALUES('".$orden["id"]."','".$campo["id"]."')");
											$VALUE='';
										}
										echo "<tr><td align='right'>" . $campo["nombre"] . "</td>";
										echo "<td align='left'><input type='text' class=\"casillatext\" size='20' name='CA_".$campo["id"]."' value='".$VALUE."'></td>";
										echo "</tr>\n";
									}
								}?>
						</table>
					</td>
				</tr>
			</table>
		</td>
		<td width="20px">
			&nbsp;
		</td>
		<?
		$valScript=$valScriptPersonas="";
		$rowNum = $numCellCar = $idActividad = $rowTaller = 1;
		if($newMode != "insertar" && $newMode != "insertar_relacionada_correctiva"){?>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" id="tabla_actividad">
							<tr>
								<td align='left' colspan=2><span class="azul_12">RESPONSABLES</span></td>
							</tr>
							<?
							foreach($actividades as $idAc => $act){?>
							<tr id='act_<?=$idActividad?>'>
								<td>
									<table width="100%" border=0 cellpadding=0 cellspacing=0>
									<?
									echo "<tr>
										<td>
											<table width='100%' cellpadding=0 cellspacing=0>
												<tr>
													<td>
														<table width='100%' cellpadding=0 cellspacing=0><tr>
															<td><b>ACTIVIDAD</b></td>
															<td width='50%' align=\"right\"><a href=\"javascript:delete_celda_actividad('act_".$idActividad."')\" class=\"link_verde\" title=\"Borrar Actividad\">BORRAR</a></td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td>
														<table width='100%' border=1 bordercolor=\"#7fa840\" cellpadding=0 cellspacing=0>
															<tr><td align=\"center\">ORDEN</td><td align=\"center\">TAREA</td><td align=\"center\">TIEMPO (mn)</td></tr>
															<tr>
																<td width='10%'><input type='text' size='2' class=\"casillatext\" name='ordencargo_".$idActividad."' value='".$act["prin"]["orden"]."'></td>
																<td align=\"center\"><textarea name='descripcioncargo_".$idActividad."'>".$act["prin"]["descripcion"]."</textarea></td>
																<td align=\"center\" width='30%'><input type='text' size='4' class=\"casillatext\" name='tiempocargo_".$idActividad."' value='".$act["prin"]["tiempo"]."'></td>
															</tr>
														</table>
													</td>
												</tr>
												<tr><td><b>CARGOS</b></td></tr>
												<tr>
													<td>
														<table width='100%' border=1 bordercolor=\"#7fa840\" cellpadding=0 cellspacing=0 id=\"tabla_cargos_".$idActividad."\">
															<tr><td align=\"center\">CARGO</td><td align=\"center\">PERSONA</td><td align=\"center\">TIEMPO (mn)</td><td>OPC</td></tr>";
			
									foreach($act["car"] as $key => $cargo)
									{
										$idCentro = $db->sql_row("SELECT e.id_centro 
												FROM mtto.equipos e
												WHERE e.id=".$orden["id_equipo"]);
										$consulta = "SELECT distinct(p.id), p.nombre||' '||p.apellido as nombre 
												FROM personas_cargos pc
												LEFT JOIN personas p ON p.id=pc.id_persona
												LEFT JOIN estados_personas e ON e.id=p.id_estado
												WHERE e.activo AND pc.id_cargo='".$cargo["id_cargo"]."' AND p.id IN (SELECT id_persona FROM personas_centros WHERE id_centro='".$idCentro["id_centro"]."') AND fecha_fin IS NULL" ;
										$db->crear_select($consulta,$opcPersCar,$cargo["id_persona"]);
										echo "<tr id='cargos_".$numCellCar."'>
											<input type=\"hidden\" name=\"cargos_".$idActividad."_idcargo_".$numCellCar."\" value=\"".$cargo["id_cargo"]."\">
											<td>".$cargo["cargo"]."</td>\n<td><select name='cargos_".$idActividad."_idpersona_".$numCellCar."' id='cargos_".$idActividad."_idpersona_".$numCellCar."'>".$opcPersCar."</select></td>
											<td><input type='text' size='4' class=\"casillatext_fecha\" name='cargos_".$idActividad."_tiempo_".$numCellCar."' value='".$cargo["tiempo"]."'></td>
											<td align=\"center\"><a href=\"javascript:delete_celda_cargos('".$idActividad."','".$numCellCar."')\" class=\"link_verde\" title=\"Borrar Cargo\">B</a></td>
										</tr>";
										$numCellCar+=1;
									}
													
									echo  	"</table>
													</td>
												</tr>
												<tr><td align=\"center\" colspan=4><a href=\"javascript:agrega_celda_cargos('".$idActividad."')\" class=\"link_verde\">+ Agregar Cargo +</a> </td></tr>
											</table>
										</td>
									</tr>
										
										";
									/*
									foreach($act["cargos"] as $cargos){
										$nc = $db->sql_row("SELECT nombre FROM cargos WHERE id=".$cargos["id_cargo"]);
										echo "<tr><td>".$nc["nombre"]."</td>";
										$idCentro = $db->sql_row("SELECT case when e.id_centro IS NOT NULL then e.id_centro else v.id_centro end
												FROM mtto.equipos e
												LEFT JOIN vehiculos v ON v.id=e.id_vehiculo
												WHERE e.id=".$orden["id_equipo"]);
										$esta=$db->sql_row("SELECT * FROM mtto.ordenes_trabajo_personas WHERE id_orden_trabajo='".$orden["id"]."' AND id_rutina_actividad_cargo='".$cargos["id_rac"]."'");
										$db->crear_select("SELECT distinct(p.id), p.nombre||' '||p.apellido as nombre 
												FROM personas_cargos pc
												LEFT JOIN personas p ON p.id=pc.id_persona
												LEFT JOIN estados_personas e ON e.id=p.id_estado
												WHERE e.activo AND pc.id_cargo='".$cargos["id_cargo"]."' AND p.id IN (SELECT id_persona FROM personas_centros WHERE id_centro='".$idCentro["id_centro"]."') AND fecha_fin IS NULL",$personas,nvl($esta["id_persona"]));
										echo "<td><select name='rac_".$cargos["id_rac"]."'>".$personas."</select></td>
											<td align='center'><input type='text' size='3' name='rac_tiempo_".$cargos["id_rac"]."' value='".nvl($esta["tiempo_ejecucion"])."'></td>
										</tr>";
									$valScript.="
										if(document.entryform.rac_".$cargos["id_rac"].".options[document.entryform.rac_".$cargos["id_rac"].".selectedIndex].value=='%'){
											window.alert('Por favor seleccione la persona del cargo ".$nc["nombre"]."');
											document.entryform.rac_".$cargos["id_rac"].".focus();
											return(false);
										}
										if(document.entryform.rac_tiempo_".$cargos["id_rac"].".value.replace(/ /g, '') ==''){
											window.alert('Por favor escriba: Tiempo Ejecución (minutos) del cargo ".$nc["nombre"]."');
											document.entryform.rac_tiempo_".$cargos["id_rac"].".focus();
											return(false);
										}
										else{
											var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
											if(!regexpression.test(document.entryform.rac_tiempo_".$cargos["id_rac"].".value)){
												window.alert('[Tiempo Ejecución (minutos) del cargo ".$nc["nombre"]."] no contiene un dato válido.');
												document.entryform.rac_tiempo_".$cargos["id_rac"].".focus();
												return(false);
											}
										}";

										$valScriptPersonas.="
											if(document.entryform.rac_".$cargos["id_rac"].".options[document.entryform.rac_".$cargos["id_rac"].".selectedIndex].value=='%'){
												window.alert('Por favor seleccione la persona del cargo ".$nc["nombre"]."');
												document.entryform.rac_".$cargos["id_rac"].".focus();
												return(false);
											}
										";
									}

									*/			
									?>
									</table>	
								</td>
							</tr>
							<?
							$idActividad++;
							}?>
						</table>
					</td>
				</tr>
				<tr>
					<?if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["ordenes_opciones"])){?>
						<td align="center"><a href="javascript:agrega_celda_actividad()" class="link_verde">+ Agregar Actividad +</a> </td>
					<?}?>
				</tr>
				<tr>
					<td height="20">&nbsp;</td>
				</tr>
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" id="tabla_elementos">
							<tr>
								<td align='left' colspan=3><span class="azul_12">ELEMENTOS</span></td>
							</tr>
							<tr>
								<td align='center'>ELEMENTO</td>
								<td align='center' width="10%">CANTIDAD</td>
								<td align='center' width="10%">OPCIONES</td>
							</tr>
							<?
							while($queryEle = $db->sql_fetchrow($qidEleExis)){?>
							<tr <?if($queryEle["opciones"] != "") echo "id=\"existe_".$queryEle["id"]."\"";?>>
								<input type="hidden" name="id_elemento_<?=$rowNum?>" value="<?=$queryEle["id_elemento"]?>">
								<td><?=$queryEle["cod"]?>'</td>
								<td align='center'><input type='text' size='4' class="casillatext_fecha" name='cantidad_<?=$rowNum?>' value='<?=$queryEle["cantidad"]?>'></td>
								<td align='center'><?=$queryEle["opciones"]?></td>
							</tr>
							<?$rowNum++;
							}?>
						</table>
					</td>
				</tr>
				<?if($db->sql_numrows($qidOEl)>0){?>
				<tr>
					<?if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["ordenes_opciones"])){?>
					<td align="center" height="30"><a href="javascript:agrega_celda()" class="link_verde">+ Agregar Elemento +</a> </td>
					<?}?>
				</tr>
				<?}?>
				<tr>
					<td height="20">&nbsp;</td>
				</tr>
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" id="tabla_talleres">
							<tr>
								<td align='left' colspan=3><span class="azul_12">TRABAJO EXTERNO</span></td>
							</tr>
							<tr>
								<td align='center'>TALLER</td>
								<td align='center' width="10%">COSTO</td>
								<td align='center' width="10%">TIEMPO (hr)</td>
								<td align='center' width="10%">OPCIONES</td>
							</tr>
							<?
							while($qta = $db->sql_fetchrow($qidTE)){?>
							<tr id="tallext_<?=$qta["id"]?>">
								<input type="hidden" name="talleridproveedor_<?=$rowTaller?>" value="<?=$qta["id_proveedor"]?>">
								<td><?=$qta["razon"]?></td>
								<td align='center'><input type='text' size='4' class="casillatext_fecha" name='tallercosto_<?=$rowTaller?>' value='<?=$qta["costo"]?>'></td>
								<td align='center'><input type='text' size='4' class="casillatext_fecha" name='tallertiempo_<?=$rowTaller?>' value='<?=$qta["tiempo"]?>'></td>
								<td align='center'><a href="javascript:delete_celda_taller('tallext_<?=$qta["id"]?>')" class="link_verde" title="Borrar">B</a></td>
							</tr>
							<?$rowTaller++;
							}?>
						</table>
					</td>
				</tr>
				<tr>
					<?if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["ordenes_opciones"])){?>
					<td align="center" height="30"><a href="javascript:agrega_celda_taller()" class="link_verde">+ Agregar Taller +</a> </td>
					<?}?>
				</tr>


				<?if($db->sql_numrows($qidOtR)>0){?>
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" id="tabla_elementos">
							<tr>
								<td align='left' colspan=3><span class="azul_12">ORDENES DE TRABAJO DERIVADAS</span></td>
							</tr>
							<tr>
								<td align='center'>RUTINA</td>
								<td align='center'>EQUIPO</td>
								<td align='center'>FECHA PLANEADA</td>
							</tr>
							<?
							while($otr = $db->sql_fetchrow($qidOtR)){?>
							<tr>
								<td><?=$otr["rutina"]?></td>
								<td><?=$otr["equipo"]?></td>
								<td><?=$otr["fecha"]?></td>
							</tr>
							<?}?>
						</table>
					</td>
				</tr>
				<?}?>
				<tr>
					<?if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["agregarOT"])){?>
					<td height="40" valign="bottom" align="center"><a href="javascript:abrirVentanaJavaScript('ordenes_corr','600','500','<?=$CFG->wwwroot?>/mtto/ordenes.php?mode=agregar_relacionada_correctiva&id_orden_trabajo_origen=<?=$orden["id"]?>&id_equipo=<?=$orden["id_equipo"]?>')" class="link_verde">+ Agregar Orden Trabajo Correctiva +</a> </td>
					<?}?>
				</tr>
				<tr>
					<td height="20">&nbsp;</td>
				</tr>
			</table>
		</td>
		<?}?>
	</tr>
	<tr>
		<td colspan=3 align="center">
			<?
			if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["ordenes_opciones"])){
			if(!estaCerradaOT(nvl($orden["id"])))
			{
			if($newMode == "insertar"){?>
				<input type="submit" class="boton_verde" value="Aceptar"/>
			<?}else{?>
				<input type="button" class="boton_verde" value="Guardar y cerrar" onclick="aceptarCerrarOr('cerrar')" />
				<input type="button" class="boton_verde" value="Guardar sin cerrar" onclick="aceptarCerrarOr('sincerrar')"/>
			<?}
			if($newMode != "insertar" && $newMode != "insertar_relacionada_correctiva"){?>
			<input type="button" class="boton_verde" value="Imprimir" onclick="javascript:imprimir()"/>
			<input type="button" class="boton_verde" value="Cerrar definitivo" onclick="cerrarDefinitivo()"/>
			<input type="button" class="boton_verde" value="Eliminar Orden" onclick="eliminar()"/>
			<?}}else{?>
			<input type="button" class="boton_verde" value="Reimprimir" onclick="javascript:reimprimir()"/>
			<?}}?>
			<input type="button" class="boton_verde" value="Cancelar" onclick="window.close()"/>
			<?
			if($_SESSION[$CFG->sesion]["user"]["login"] == "luisa"){?>
						<input type="button" class="boton_verde" value="Imprimir Aplik" onclick="javascript:reimprimir()"/>
			<?}?>


		</td>
	</tr>
</table>
</form>
<script type="text/javascript">

function aceptarCerrarOr(accion)
{
	if(revisar())
	{
		document.entryform.accion.value = accion;
		document.entryform.submit();
	}
}

function eliminar(){
	var result=window.confirm("¿Está seguro de eliminar esta orden de trabajo?");
	if(result) window.location.href='<?=$ME?>?mode=eliminar&id=<?=nvl($frm["id"],0)?>&id_user=<?=$_SESSION[$CFG->sesion]["user"]["id"]?>';
}

var rowNum=<?=$rowNum?>;
var rowCargosNum=<?=$numCellCar?>;
var rowActividadNum=<?=$idActividad?>;
var rowTaller=<?=$rowTaller?>;

function agrega_celda(){
	var tbl = document.getElementById('tabla_elementos');
	var lastRow = tbl.rows.length;
	var row = tbl.insertRow(lastRow);
	var idRow="row"+rowNum;
	row.setAttribute("id", idRow);

	var cell1 = document.createElement("td");
	var inputElem = document.createElement('input');
	inputElem.id="id_elemento_"+rowNum;
	inputElem.name="id_elemento_"+rowNum;
	inputElem.type = 'hidden';
	inputElem.value = '';
	cell1.appendChild(inputElem);
	
	var div1 = document.createElement("div");
	div1.setAttribute("class", "yui-skin-sam");
	div1.setAttribute("style","width:25em;padding-bottom:2em;position:relative;");
	var inputElem2 = document.createElement('input');
	inputElem2.id="AC_id_elemento_"+rowNum;
	inputElem2.type = 'text';
	inputElem2.value = '';
	div1.appendChild(inputElem2);
	var div2 = document.createElement("div");
	div2.setAttribute("id", "popup_id_elemento_"+rowNum);
	div2.setAttribute("style","width:25em;");

	var sc = document.createElement('script');
	var codigo = " YAHOO.example.BasicRemote = function() { var oDS = new YAHOO.util.XHRDataSource('<?=$CFG->wwwroot?>/autocomplete2/autocomplete.php'); oDS.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT; oDS.responseSchema = {recordDelim: '\\n', fieldDelim: '\\t' }; oDS.maxCacheEntries = 5; var oAC = new YAHOO.widget.AutoComplete(\"AC_id_elemento_"+rowNum+"\", \"popup_id_elemento_"+rowNum+"\", oDS); oAC.maxResultsDisplayed = 30; oAC.generateRequest = function(sQuery) { return \"?dirroot=%2Fvar%2Fwww%2Fhtml%2Fpa&module=mtto.rutinas_elementos&field=id_elemento&strCentros=<?=nvl($orden["id_centro"],0)?>&s=\" + sQuery ; }; var myHiddenField = YAHOO.util.Dom.get(\"id_elemento_"+rowNum+"\"); var myHandler = function(sType, aArgs) { var myAC = aArgs[0]; var elLI = aArgs[1]; myHiddenField.value = aArgs[2][1]; }; oAC.itemSelectEvent.subscribe(myHandler); return { oDS: oDS, oAC: oAC }; }();";
	
	var tt = document.createTextNode(codigo);
	sc.appendChild(tt);
	cell1.appendChild(sc);
	
	div1.appendChild(div2);
	cell1.appendChild(div1);


	var cell2 = document.createElement("td");
	cell2.style.textAlign="center";
	var inputCant = document.createElement('input');
	inputCant.id="cantidad_"+rowNum;
	inputCant.type = 'text';
	inputCant.value = '0';
	inputCant.size = 4;
	inputCant.name = 'cantidad_'+rowNum;
	inputCant.style.textAlign="center";
	cell2.appendChild(inputCant);


	var cell3 = document.createElement("td");
	cell3.style.textAlign="center";
	var a = document.createElement("a");
	var txt = document.createTextNode("B");
	a.appendChild(txt);
	a.href = "javascript:delete_celda('"+idRow+"')";
	a.title = "Borrar";
	a.className = "link_verde";
	cell3.appendChild(a);

	row.appendChild(cell1);
	row.appendChild(cell2);
	row.appendChild(cell3);

	rowNum+=1;
}

function delete_celda(id_row){
	if(confirm("¿Esta seguro de borrar el elemento?")){
		var tbl = document.getElementById('tabla_elementos');
		var row = document.getElementById(id_row);
		tbl.getElementsByTagName("tbody")[0].removeChild(row);
		rowNum-=1;
	}
}

function agrega_celda_cargos(idActividad){
	var key = rowCargosNum;
	var tabla = 'tabla_cargos_' + idActividad;
	var tbl = document.getElementById(tabla);
	var lastRow = tbl.rows.length;
	var row = tbl.insertRow(lastRow);
	var idRow="cargos_"+key;
	row.setAttribute("id", idRow);

	var cargo = row.insertCell(0);
	var sel = document.createElement('select');
	sel.name = 'cargos_' + idActividad + '_idcargo_'+key;
	sel.id = 'cargos_' + idActividad + '_idcargo_'+key;
	sel.onchange= function(){updateRecursive_id_personas_cargos(this,idActividad,key);};
	sel.options[0] = new Option('Seleccione...', '%');
	var c=1;
	<?if($newMode != "insertar" && $newMode != "insertar_relacionada_correctiva"){
		$qidCar = $db->sql_query("SELECT id, nombre FROM cargos where id_superior=32 ORDER BY nombre");
		while($car = $db->sql_fetchrow($qidCar)){?>
			sel.options[c] = new Option('<?=$car["nombre"]?>', '<?=$car["id"]?>');
			c+=1;
		<?}}?>
	cargo.appendChild(sel);

	var persona = row.insertCell(1);
	var sel2 = document.createElement('select');
	sel2.name = 'cargos_' + idActividad + '_idpersona_'+key;
	sel2.id = 'cargos_' + idActividad + '_idpersona_'+key;
	sel2.options[0] = new Option('Seleccione...', '%');
	persona.appendChild(sel2);
	
	var cellCantidad = row.insertCell(2);
	var inputCant = document.createElement('input');
	inputCant.id = 'cargos_' + idActividad + '_tiempo_'+key;
	inputCant.name = 'cargos_' + idActividad + '_tiempo_'+key;
	inputCant.type = 'text';
	inputCant.value = '0';
	inputCant.size = 4;
	inputCant.style.textAlign="center";
	cellCantidad.appendChild(inputCant);


	var cellOpciones = row.insertCell(3);
	cellOpciones.style.textAlign="center";
	var a = document.createElement("a");
	var txt = document.createTextNode("B");
	a.appendChild(txt);
	a.href = "javascript:delete_celda_cargos('"+idActividad+"','"+key+"')";
	a.title = "Borrar";
	a.className = "link_verde";
	cellOpciones.appendChild(a);


	rowCargosNum+=1;
}

function delete_celda_cargos(idActividad,id_row){
	if(confirm("¿Esta seguro de borrar el cargo?")){
		var tabla = 'tabla_cargos_' + idActividad;
		var tbl = document.getElementById(tabla);
		id_row = 'cargos_'+id_row;
		var row = document.getElementById(id_row);
		tbl.getElementsByTagName("tbody")[0].removeChild(row);
		rowCargosNum-=1;
	}
}

function agrega_celda_actividad(){
	var tbl = document.getElementById('tabla_actividad');
	var lastRow = tbl.rows.length;
	var row = tbl.insertRow(lastRow);
	var idRow=rowActividadNum;
	row.setAttribute("id", idRow);

	var cell1 = row.insertCell(0);

	var tbl1 = document.createElement("table");
	var tblBody1 = document.createElement("tbody");
	var row1 = document.createElement("tr");
	var cell2 = document.createElement("td");

	var tbl2 = document.createElement("table");
	var tblBody2 = document.createElement("tbody");
	var row2 = document.createElement("tr");
	var cell3 = document.createElement("td");
	
	var tbl3 = document.createElement("table");
	var tblBody3 = document.createElement("tbody");
	var row3 = document.createElement("tr");
	var cell4 = document.createElement("td");
	var b = document.createElement("b");
	var txt = document.createTextNode("ACTIVIDAD");
	b.appendChild(txt);
	cell4.appendChild(b);

	var cell5 = document.createElement("td");
	var a = document.createElement("a");
	var txt1 = document.createTextNode("BORRAR");
	a.appendChild(txt1);
	a.href = "javascript:delete_celda_actividad('act_"+idRow+"')";
	a.title = "Borrar Actividad";
	a.className = "link_verde";
	cell5.appendChild(a);

	var row4 = document.createElement("tr");
	var cell6 = document.createElement("td");
	
	var tbl4 = document.createElement("table");
	tbl4.setAttribute("border", "1");
	tbl4.setAttribute("width","100%");
	tbl4.setAttribute("bordercolor","#7fa840");

	var tblBody4 = document.createElement("tbody");
	var row5 = document.createElement("tr");
	var cell7 = document.createElement("td");
	var cellText = document.createTextNode("ORDEN");
	cell7.appendChild(cellText);
	var cell8 = document.createElement("td");
	var cellText1 = document.createTextNode("TAREA");
	cell8.appendChild(cellText1);
	var cell9 = document.createElement("td");
	var cellText2 = document.createTextNode("TIEMPO (mn)");
	cell9.appendChild(cellText2);

	var row6 = document.createElement("tr");
	var cell10 = document.createElement("td");
	cell10.style.textAlign="center";
	var inputOrd = document.createElement('input');
	inputOrd.id="ordencargo_"+rowActividadNum;
	inputOrd.name = 'ordencargo_'+rowActividadNum;
	inputOrd.type = 'text';
	inputOrd.value = '0';
	inputOrd.size = 2;
	inputOrd.style.textAlign="center";
	cell10.appendChild(inputOrd);

	var cell11 = document.createElement("td");
	cell11.style.textAlign="center";
	var inputDe = document.createElement('input');
	inputDe.id="descripcioncargo_"+rowActividadNum;
	inputDe.name = 'descripcioncargo_'+rowActividadNum;
	inputDe.type = 'textarea';
	cell11.appendChild(inputDe);

	var cell12 = document.createElement("td");
	cell12.style.textAlign="center";
	var inputTime = document.createElement('input');
	inputTime.id="ordencargo_"+rowActividadNum;
	inputTime.name = 'tiempocargo_'+rowActividadNum;
	inputTime.type = 'text';
	inputTime.value = '0';
	inputTime.size = 2;
	inputTime.style.textAlign="center";
	cell12.appendChild(inputTime);

	var row7 = document.createElement("tr");
	var cell13 = document.createElement("td");
	var cellText3 = document.createTextNode("CARGOS");
	cell13.appendChild(cellText3);

	var row8 = document.createElement("tr");
	var cell14 = document.createElement("td");
	var tbl5 = document.createElement("table");
	var tblBody5 = document.createElement("tbody");
	tbl5.setAttribute("border", "1");
	tbl5.setAttribute("width","100%");
	tbl5.setAttribute("bordercolor","#7fa840");
	tbl5.setAttribute("id","tabla_cargos_"+idRow);
	var row9 = document.createElement("tr");
	var cell15 = document.createElement("td");
	var cellText4 = document.createTextNode("CARGO");
	cell15.appendChild(cellText4);
	var cell16 = document.createElement("td");
	var cellText5 = document.createTextNode("PERSONA");
	cell16.appendChild(cellText5);
	var cell17 = document.createElement("td");
	var cellText6 = document.createTextNode("TIEMPO (mn)");
	cell17.appendChild(cellText6);
	var cell18 = document.createElement("td");
	var cellText7 = document.createTextNode("OPC");
	cell18.appendChild(cellText7);

	var row10 = document.createElement("tr");
	var cell20 = document.createElement("td");
	var a2 = document.createElement("a");
	var txt2 = document.createTextNode("+ Agregar Cargo +");
	a2.appendChild(txt2);
	a2.href = "javascript:agrega_celda_cargos('"+idRow+"')";
	a2.className = "link_verde";
	cell20.appendChild(a2);

	row1.appendChild(cell2);
	tblBody1.appendChild(row1);
	tbl1.appendChild(tblBody1);
	cell1.appendChild(tbl1);

	row2.appendChild(cell3);
	tblBody2.appendChild(row2);
	tbl2.appendChild(tblBody2);
	cell2.appendChild(tbl2);

	row3.appendChild(cell4);
	row3.appendChild(cell5);
	tblBody3.appendChild(row3);
	tbl3.appendChild(tblBody3);
	cell3.appendChild(tbl3);

	row5.appendChild(cell7);
	row5.appendChild(cell8);
	row5.appendChild(cell9);
	tblBody4.appendChild(row5);
	row6.appendChild(cell10);
	row6.appendChild(cell11);
	row6.appendChild(cell12);

	tblBody4.appendChild(row6);
	tbl4.appendChild(tblBody4);
	cell6.appendChild(tbl4);

	row4.appendChild(cell6);
	tblBody3.appendChild(row4);

	row7.appendChild(cell13);
	tblBody3.appendChild(row7);

	row8.appendChild(cell14);
	tblBody3.appendChild(row8);
	
	tbl5.appendChild(tblBody5);
	cell14.appendChild(tbl5);
	tblBody5.appendChild(row9);
	row9.appendChild(cell15);
	row9.appendChild(cell16);
	row9.appendChild(cell17);
	row9.appendChild(cell18);

	row10.appendChild(cell20);
	tblBody3.appendChild(row10);

	rowActividadNum+=1;
}

function delete_celda_actividad(idActividad){
	if(confirm("¿Esta seguro de borrar la actividad?")){
		var tbl = document.getElementById('tabla_actividad');
		var row = document.getElementById(idActividad);
		tbl.getElementsByTagName("tbody")[0].removeChild(row);
		rowActividadNum-=1;
	}
}

function agrega_celda_taller(){
	var key = rowTaller;
	var tabla = 'tabla_talleres';
	var tbl = document.getElementById(tabla);
	var lastRow = tbl.rows.length;
	var row = tbl.insertRow(lastRow);
	var idRow="tallext_"+key;
	row.setAttribute("id", idRow);

	var prov = row.insertCell(0);
	var sel = document.createElement('select');
	sel.name = 'talleridproveedor_'+key;
	sel.id = 'tallerid_proveedor_'+key;
	sel.options[0] = new Option('Seleccione...', '%');
	var c=1;
	<?if($newMode != "insertar" && $newMode != "insertar_relacionada_correctiva"){
		while($qprov = $db->sql_fetchrow($qidProveedores)){?>
			sel.options[c] = new Option('<?=$qprov["razon"]?>', '<?=$qprov["id"]?>');
			c+=1;
		<?}}?>
	prov.appendChild(sel);

	var cellCosto = row.insertCell(1);
	var inputCosto = document.createElement('input');
	inputCosto.id = 'tallercosto_'+key;
	inputCosto.name = 'tallercosto_'+key;
	inputCosto.type = 'text';
	inputCosto.value = '0';
	inputCosto.size = 4;
	inputCosto.style.textAlign="center";
	cellCosto.appendChild(inputCosto);

	var cellTiempo = row.insertCell(2);
	var inputTiempo = document.createElement('input');
	inputTiempo.id = 'tallertiempo_'+key;
	inputTiempo.name = 'tallertiempo_'+key;
	inputTiempo.type = 'text';
	inputTiempo.value = '0';
	inputTiempo.size = 4;
	inputTiempo.style.textAlign="center";
	cellTiempo.appendChild(inputTiempo);

	var cellOpcionesT = row.insertCell(3);
	cellOpcionesT.style.textAlign="center";
	var a = document.createElement("a");
	var txt = document.createTextNode("B");
	a.appendChild(txt);
	a.href = "javascript:delete_celda_taller('"+key+"')";
	a.title = "Borrar";
	a.className = "link_verde";
	cellOpcionesT.appendChild(a);

	rowTaller+=1;
}

function delete_celda_taller(id_row){
	if(confirm("¿Esta seguro de borrar el taller?")){
		var tbl = document.getElementById('tabla_talleres');
		var row = document.getElementById(id_row);
		tbl.getElementsByTagName("tbody")[0].removeChild(row);
		rowTaller-=1;
	}
}

function revisar()
{
	<?if($newMode != "actualizar"){?>
	if(document.entryform.id_rutina.options[document.entryform.id_rutina.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Rutina');
		document.entryform.id_rutina.focus();
		return(false);
	}
	if(document.entryform.id_equipo.options[document.entryform.id_equipo.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Equipo');
		document.entryform.id_equipo.focus();
		return(false);
	}
	<?}
	if($newMode == "insertar_relacionada_correctiva"){?>
	if(document.entryform.id_motivo.options[document.entryform.id_motivo.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Motivo');
		document.entryform.id_motivo.focus();
		return(false);
	}
	<?}?>

	if(document.entryform.fecha_planeada.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Fecha Planeada');
		document.entryform.fecha_planeada.focus();
		return(false);
	}
	else{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.fecha_planeada.value)){
			window.alert('[Fecha Planeada] no contiene un dato válido.');
			document.entryform.fecha_planeada.focus();
			return(false);
		}
	}
	if(document.entryform.fecha_ejecucion_inicio.value !=''){
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.fecha_ejecucion_inicio.value)){
			window.alert('[Fecha Ejecución Inicio] no contiene un dato válido.');
			document.entryform.fecha_ejecucion_inicio.focus();
			return(false);
		}
	}
	if(document.entryform.fecha_ejecucion_fin.value !=''){
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.fecha_ejecucion_fin.value)){
			window.alert('[Fecha Ejecución Fin] no contiene un dato válido.');
			document.entryform.fecha_ejecucion_fin.focus();
			return(false);
		}
	}

	if(document.entryform.fecha_ejecucion_inicio.value !='' && document.entryform.fecha_ejecucion_fin.value !=''){
		if(document.entryform.fecha_ejecucion_inicio.value > document.entryform.fecha_ejecucion_fin.value)
		{
			window.alert('La Fecha Ejecución Inicio no puede ser mayor que la Fecha Ejecución Fin');
			return(false);
		}		
		
		<?
		$condEstadosAbiertos = array();
		while($estAbiertos = $db->sql_fetchrow($qidEstadosAbiertos)){
			$condEstadosAbiertos[]="document.entryform.id_estado_orden_trabajo.options[document.entryform.id_estado_orden_trabajo.selectedIndex].value=='".$estAbiertos["id"]."'";
		}
		?>
		if(<?=implode(" || ",$condEstadosAbiertos)?>)
		{
			window.alert('Por favor cambie el estado de la orden a uno cerrado');
			document.entryform.id_estado_orden_trabajo.focus();
			return(false);
		}
	}

	if(document.entryform.id_estado_orden_trabajo.options[document.entryform.id_estado_orden_trabajo.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Estado');
		document.entryform.id_estado_orden_trabajo.focus();
		return(false);
	}

	if(document.entryform.km.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.km.value)){
			window.alert('[Km] no contiene un dato válido.');
			document.entryform.km.focus();
			return(false);
		}
	}
	if(document.entryform.horometro.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.horometro.value)){
			window.alert('[Horómetro] no contiene un dato válido.');
			document.entryform.horometro.focus();
			return(false);
		}
	}
	if(document.entryform.observaciones.value !=''){
		var regexpression=/./;
		if(!regexpression.test(document.entryform.observaciones.value)){
			window.alert('[Observaciones] no contiene un dato válido.');
			document.entryform.observaciones.focus();
			return(false);
		}
	}


	if(document.entryform.id_estado_orden_trabajo.options[document.entryform.id_estado_orden_trabajo.selectedIndex].value!='%')
	{
		<?
		$condEstados = array();
		while($est = $db->sql_fetchrow($qidEstados)){
			$condEstados[]="document.entryform.id_estado_orden_trabajo.options[document.entryform.id_estado_orden_trabajo.selectedIndex].value=='".$est["id"]."'";
		}
		?>
		if(<?=implode(" || ",$condEstados)?>)
		{
			if(document.entryform.fecha_ejecucion_inicio.value=='')
			{
				window.alert('Por favor seleccione Fecha Ejecución Inicio');
				return(false);
			}
			if(document.entryform.fecha_ejecucion_fin.value=='')
			{
				window.alert('Por favor seleccione Fecha Ejecución Fin');
				return(false);
			}
			if(document.entryform.fecha_ejecucion_inicio.value > document.entryform.fecha_ejecucion_fin.value)
			{
				window.alert('La Fecha Ejecución Inicio no puede ser mayor que la Fecha Ejecución Fin');
				return(false);
			}

			if(document.entryform.id_ingreso_ejecutada.value=='%' || document.entryform.id_ingreso_ejecutada.value==''){
				window.alert('Por favor seleccione: Ingresó Ejecutada');
				document.entryform.id_ingreso_ejecutada.focus();
				return(false);
			}
			if(document.entryform.km.value == '' && document.entryform.horometro.value ==''){
				window.alert('Por favor escriba Km ó Horómetro.');
				document.entryform.km.focus();
				return(false);
			}

			<?=$valScript;?>
		}
	}

	for(i=<?=$rowNum?>;i<rowNum;i++)
	{
		if(document.getElementById('id_elemento_'+i).value =='%'){
			window.alert('Por favor seleccione: Elemento');
			return(false);
		}

		if(document.getElementById('cantidad_'+i).value =='0' || document.getElementById('cantidad_'+i).value ==''){
			window.alert('La cantidad no puede ser 0 o vacía');
			return(false);
		}
	}

	document.entryform.mode.value='<?=$newMode?>';
	return(true);
}


function cerrarDefinitivo()
{
	document.entryform.id_estado_orden_trabajo.options[document.entryform.id_estado_orden_trabajo.selectedIndex].value=2;
	document.entryform.accion.value = 'cerrar';
	if(revisar())
	{
		document.entryform.submit();
	}

}

function imprimir()
{
	if(revisar())
		if(revisarPersonas())
		{
			document.entryform.mode.value='imprimir_unica';
			document.entryform.submit();
		}
}

function reimprimir()
{
	document.entryform.mode.value='reimprimir';
	document.entryform.submit();
}

function revisarPersonas()
{
	if(document.entryform.id_responsable.options[document.entryform.id_responsable.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Responsable');
		document.entryform.id_responsable.focus();
		return(false);
	}

	if(document.entryform.id_planeador.options[document.entryform.id_planeador.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Planeador');
		document.entryform.id_planeador.focus();
		return(false);
	}

	<?=$valScriptPersonas;?>

		/*
for(j=1;j<rowActividadNum;j++)
{
	for(i=1;i<rowCargosNum;i++)
	{
		window.alert(j)
		window.alert(j);
		var casilla = "cargos_"+j+"_idcargo_"+i;
		if(document.getElementById(eval(casilla)).value =='%'){
			window.alert('Por favor seleccione: Persona');
			return(false);
		}

	}
}
*/

	return true;
}


function GetHttpObject(handler){
	try
	{
		var oRequester = new ActiveXObject("Microsoft.XMLHTTP");
		oRequester.onreadystatechange=handler;
		return oRequester;
	}
	catch (error){
		try{
			var oRequester = new XMLHttpRequest();
			oRequester.onload=handler;
			oRequester.onerror=handler;
			return oRequester;
		} 
		catch (error){
			return false;
		}
	}
} 

var oXmlHttp_id_equipo;
function updateRecursive_id_equipo(select){
	namediv='id_equipo';
	nameId='id_equipo';
	id=select.options[select.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateMtto.php?tipo=equipos_ordenes&id_rutina=" + id + "&divid=" + namediv;
	oXmlHttp_id_equipo=GetHttpObject(cambiarRecursive_id_equipo);
	oXmlHttp_id_equipo.open("GET", url , true);
	oXmlHttp_id_equipo.send(null);
}
function cambiarRecursive_id_equipo(){
	if (oXmlHttp_id_equipo.readyState==4 || oXmlHttp_id_equipo.readyState=="complete"){
		document.getElementById('id_equipo').innerHTML=oXmlHttp_id_equipo.responseText
	}
}

var oXmlHttp_id_responsable;
function updateRecursive_id_responsable(select){
	namediv='id_responsable';
	nameId='id_responsable';
	id=select.options[select.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateMtto.php?tipo=responsable_ordenes&id_rutina=" + id + "&divid=" + namediv;
	oXmlHttp_id_responsable=GetHttpObject(cambiarRecursive_id_responsable);
	oXmlHttp_id_responsable.open("GET", url , true);
	oXmlHttp_id_responsable.send(null);
}
function cambiarRecursive_id_responsable(){
	if (oXmlHttp_id_responsable.readyState==4 || oXmlHttp_id_responsable.readyState=="complete"){
		document.getElementById('id_responsable').innerHTML=oXmlHttp_id_responsable.responseText
	}
}

var oXmlHttp_id_planeador;
function updateRecursive_id_planeador(select){
	namediv='id_planeador';
	nameId='id_planeador';
	id=select.options[select.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateMtto.php?tipo=planeador_ordenes&id_rutina=" + id + "&divid=" + namediv;
	oXmlHttp_id_planeador=GetHttpObject(cambiarRecursive_id_planeador);
	oXmlHttp_id_planeador.open("GET", url , true);
	oXmlHttp_id_planeador.send(null);
}
function cambiarRecursive_id_planeador(){
	if (oXmlHttp_id_planeador.readyState==4 || oXmlHttp_id_planeador.readyState=="complete"){
		document.getElementById('id_planeador').innerHTML=oXmlHttp_id_planeador.responseText
	}
}

var oXmlHttp_id_ingreso_ejecutada;
function updateRecursive_id_ingreso_ejecutada(select){
	namediv='id_ingreso_ejecutada';
	nameId='id_ingreso_ejecutada';
	id=select.options[select.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateMtto.php?tipo=ingreso_ejecutada_ordenes&id_rutina=" + id + "&divid=" + namediv;
	oXmlHttp_id_ingreso_ejecutada=GetHttpObject(cambiarRecursive_id_ingreso_ejecutada);
	oXmlHttp_id_ingreso_ejecutada.open("GET", url , true);
	oXmlHttp_id_ingreso_ejecutada.send(null);
}
function cambiarRecursive_id_ingreso_ejecutada(){
	if (oXmlHttp_id_ingreso_ejecutada.readyState==4 || oXmlHttp_id_ingreso_ejecutada.readyState=="complete"){
		document.getElementById('id_ingreso_ejecutada').innerHTML=oXmlHttp_id_ingreso_ejecutada.responseText
	}
}


var oXmlHttp_id_rutina;
function updateRecursive_id_rutina(){
	namediv='id_rutina';
	nameId='id_rutina';
	id=document.entryform.id_sistema.options[document.entryform.id_sistema.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateMtto.php?tipo=rutinasxsistema&id_sistema=" + id + "&divid=" + namediv;
	oXmlHttp_id_rutina=GetHttpObject(cambiarRecursive_id_rutina);
	oXmlHttp_id_rutina.open("GET", url , true);
	oXmlHttp_id_rutina.send(null);
}
function cambiarRecursive_id_rutina(){
	if (oXmlHttp_id_rutina.readyState==4 || oXmlHttp_id_rutina.readyState=="complete"){
		document.getElementById('id_rutina').innerHTML=oXmlHttp_id_rutina.responseText
	}
}

function cargarValoresRutina()
{
	updateRecursive_id_rutina();
}


<?if($newMode != "insertar" && $newMode != "insertar_relacionada_correctiva"){?>
origen = new Array();

<?
$i=0;
$idCentro = $db->sql_row("SELECT e.id_centro FROM mtto.equipos e WHERE e.id=".$orden["id_equipo"]);
$personasCargos=$db->sql_query("SELECT distinct(p.id), p.nombre||' '||p.apellido as nombre, p.id_cargo
		FROM personas_cargos pc
		LEFT JOIN personas p ON p.id=pc.id_persona
		LEFT JOIN estados_personas e ON e.id=p.id_estado
		WHERE e.activo AND p.id IN (SELECT id_persona FROM personas_centros WHERE id_centro='".$idCentro["id_centro"]."') AND fecha_fin IS NULL
		ORDER BY nombre");
while($panIng = $db->sql_fetchrow($personasCargos))
{?>
	origen[<?=$i?>]="<?=$panIng["id"]."::".$panIng["nombre"]."::".$panIng["id_cargo"]?>"
  <?$i++;
}
?>


function updateRecursive_id_personas_cargos(select, idActividad, key)
{
	id=select.options[select.selectedIndex].value;
	recarga = 'document.entryform.cargos_' + idActividad + '_idpersona_'+ key;
	actualizarOnChange(eval(recarga),origen,id);
}

function eliminarOnChange(lista)
{
	for (i=lista.length-1;i>=0;i--)
	{
		lista.options[i]=null
	}
}

function crearOnChange(lista,arreglo,id)
{

	lista.options[0] = new Option('Seleccione...', '%');
	j=1;

	for (i=0; i<arreglo.length; i++)
	{
		if (parseInt(sacarOnChange(arreglo[i],2))==parseInt(id))
		{
			cadena = "var option" + j + "= new Option(\"" + sacarOnChange(arreglo[i],1) + "\",\"" + sacarOnChange(arreglo[i],0) + "\")"
			eval(cadena)
			cadena="lista.options[j] = option" + j
			eval(cadena)
			if (j==0) lista.selectedIndex=j
			j++
		}
	}
}

function sacarOnChange(cadena,posicion)
{
	var separador="::"
	arregloDeCadenas = cadena.split(separador)
	return arregloDeCadenas[posicion]
}

function actualizarOnChange(lista,arreglo,id)
{
	eliminarOnChange(lista)
	crearOnChange(lista,arreglo,id)
}




<?}?>


function fecha( cadena ) {  
  
	var separador = " "  
	var separadorPuno = "-";
	var separadorPdos = ":";

	if ( cadena.indexOf( separador ) != -1 ) {  
		var posFecha = 0;
		var posHora = cadena.indexOf( separador, posFecha + 1 );
		puno = cadena.substring(posFecha, posHora);
		pdos = cadena.substring(posHora, cadena.length );
		
		if ( puno.indexOf( separadorPuno) != -1 ) {  
			var posi1 = 0  
			var posi2 = puno.indexOf( separadorPuno, posi1 + 1 )  
			var posi3 = puno.indexOf( separadorPuno, posi2 + 1 )

			this.anio = puno.substring( posi1, posi2 )  
			this.mes = puno.substring( posi2 + 1, posi3 )  
			this.dia = puno.substring( posi3 + 1, puno.length ) 
		}else {  
			this.dia = 0  
			this.mes = 0  
			this.anio = 0     
		} 

		if ( pdos.indexOf( separadorPdos) != -1 ) {  
			var posi1 = 0  
			var posi2 = pdos.indexOf( separadorPdos, posi1 + 1 )  
			var posi3 = pdos.indexOf( separadorPdos, posi2 + 1 )

			this.hora = pdos.substring( posi1, posi2 )  
			this.minuto = pdos.substring( posi2 + 1, posi3 )  
			this.segundo = pdos.substring( posi3 + 1, pdos.length ) 
		}else {  
			this.hora = 0  
			this.minuto = 0  
			this.segundo = 0     
		} 
	}	 
}  
  
</script>

















<?
include($CFG->templatedir . "/resize_window.php");
?>

