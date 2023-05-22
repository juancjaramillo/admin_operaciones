<?
include_once("../../application.php");
include($CFG->dirroot."/templates/header_popup.php");

$user=$_SESSION[$CFG->sesion]["user"];

$db->crear_select("SELECT id, tipo FROM mtto.tipos ORDER BY tipo",$tipos);
$db->crear_select("SELECT id, sistema FROM mtto.sistemas ORDER BY sistema",$sistemas);

$condicion = $condicionRut= $condicionPers = "true";
if($user["nivel_acceso"]!=1)
{
	$condicion = "id_centro IS NULL OR id_centro IN (".implode(",",$user["id_centro"]).")";
	$condicionRut=" id IN (SELECT id_rutina FROM mtto.rutinas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."'))";
	$condicionPers = "id IN (SELECT id_persona FROM personas_centros WHERE id_centro IN (".implode(",",$user["id_centro"])."))";
}

$condicionRut=" id IN (SELECT id_rutina FROM mtto.rutinas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."'))";

$db->crear_select("SELECT id, rutina FROM mtto.rutinas WHERE activa AND ".$condicionRut." ORDER BY rutina",$rutinasBusq,nvl($busq["id_rutina"]),"Cualquiera...");
$db->crear_select("SELECT id, nombre FROM mtto.equipos WHERE ".$condicion." ORDER BY  nombre",$equiposBusq,nvl($busq["id_equipo"]),"Cualquiera...");
$db->build_recursive_tree_path("mtto.motivos",$motivosBusq,nvl($busq["id_motivo"]),"id","id_superior","mtto.motivos.nombre");
$db->crear_select("SELECT id, nombre||' '||apellido as nombre FROM personas WHERE ".$condicionPers." ORDER BY nombre,apellido",$responsableBusq,nvl($busq["id_responsable"]),"Cualquiera...");
$db->crear_select("SELECT id, nombre||' '||apellido as nombre FROM personas WHERE ".$condicionPers." ORDER BY nombre,apellido",$creadorBusq,nvl($busq["id_creador"]),"Cualquiera...");
$db->crear_select("SELECT id, nombre||' '||apellido as nombre FROM personas WHERE ".$condicionPers." ORDER BY nombre,apellido",$planeadorBusq,nvl($busq["id_planeador"]),"Cualquiera...");
$db->crear_select("SELECT id, estado FROM mtto.estados_ordenes_trabajo ORDER BY estado",$estadoBusq,nvl($busq["id_estado_orden_trabajo"]),"Cualquiera...");



?>
<table width="100%">
	<tr>
		<td height="40" class="azul_16"><strong>BUSCAR ÓRDENES</strong></td>
	</tr>
	<tr>
		<td>
			<form name="busq_form" action="<?=$CFG->wwwroot?>/mtto/calendario.php" method="POST" onSubmit="return revisarBusq()" class="form">
			<input type="hidden" name="mode" value="resultados">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840">
							<tr>
								<td>Número Orden</td>
								<td><input type='text' size='10'  name='id' value='<?=nvl($busq["id"])?>'></td>
							</tr>
							<tr>
								<td>Tipo</td>
								<td align='left'> <select  name='id_tipo_mantenimiento' onChange="updateRecursive_id_rutina()"><?=$tipos?></select> </td>
							</tr>
							<tr>
								<td>Sistema</td>
								<td align='left'> <select  name='id_sistema' onChange="updateRecursive_id_rutina()"><?=$sistemas?></select> </td>
							</tr>

							<tr>
								<td>Rutina</td>
								<td><div id="id_rutina"><select  name='id_rutina' id="id_rutina" style="width:250px"><?=$rutinasBusq?></select></div></td>
							</tr>
							<tr>
								<td>Equipo</td>
								<td><select  name='id_equipo'><?=$equiposBusq?></select></td>
							</tr>
							<tr>
								<td>Motivo</td>
								<td><select  name='id_motivo'><option value='%'>Cualquiera...</option><?=$motivosBusq?></select></td>
							</tr>
							<tr>
								<td>Fecha Planeada</td>
								<td>
									<input type='text' size="10" id="f_inicio_fecha_planeada" class="casillatext_fecha" name='inicio_fecha_planeada' value='<?=nvl($busq["inicio_fecha_planeada"])?>' readonly /><button id="b_inicio_fecha_planeada" onclick="javascript:showCalendarSencillo('f_inicio_fecha_planeada','b_inicio_fecha_planeada')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
									 a <input type='text' size="10" id="f_fin_fecha_planeada" class="casillatext_fecha" name='fin_fecha_planeada' value='<?=nvl($busq["fin_fecha_planeada"])?>' readonly /><button id="b_fin_fecha_planeada" onclick="javascript:showCalendarSencillo('f_fin_fecha_planeada','b_fin_fecha_planeada')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td>Hora Planeada</td>
								<td>
									Hora Inicio:<select name='inicio_hora_planeada'><option value="%">...</option>
									<?for($i=0;$i<=23;$i++)
										{
											$selected="";
											if(strlen($i)==1) $hora="0".$i; else $hora=$i;
											if(nvl($busq["inicio_hora_planeada"])==$hora) $selected=" selected";
											echo '<option value="'.$hora.'" '.$selected.'>'.$hora.'</option>';
										}
									?></select>
									Minuto Inicio:<select name='inicio_minuto_planeada'><option value="%">...</option>
									<?for($i=0;$i<=59;$i++)
										{
											$selected="";
											if(strlen($i)==1) $minuto="0".$i; else $minuto=$i;
											if(nvl($busq["inicio_minuto_planeada"])==$minuto) $selected=" selected";
											echo '<option value="'.$minuto.'" '.$selected.'>'.$minuto.'</option>';
										}
									?></select>
									a Hora Fin:<select name='fin_hora_planeada'><option value="%">...</option>
									<?for($i=0;$i<=23;$i++)
										{
											$selected="";
											if(strlen($i)==1) $hora="0".$i; else $hora=$i;
											if(nvl($busq["fin_hora_planeada"])==$hora) $selected=" selected";
											echo '<option value="'.$hora.'" '.$selected.'>'.$hora.'</option>';
										}
									?></select>
									Minuto Fin:<select name='fin_minuto_planeada'><option value="%">...</option>
									<?for($i=0;$i<=59;$i++)
										{
											$selected="";
											if(strlen($i)==1) $minuto="0".$i; else $minuto=$i;
											if(nvl($busq["fin_minuto_planeada"])==$minuto) $selected=" selected";
											echo '<option value="'.$minuto.'" '.$selected.'>'.$minuto.'</option>';
										}
									?></select>
								</td>
							</tr>
							<tr>
								<td>Fecha Ejecución</td>
								<td>
									<input type='text' size="10" id="f_inicio_fecha_ejecucion" class="casillatext_fecha" name='inicio_fecha_ejecucion' value='<?=nvl($busq["inicio_fecha_ejecucion"])?>' readonly /><button id="b_inicio_fecha_ejecucion" onclick="javascript:showCalendarSencillo('f_inicio_fecha_ejecucion','b_inicio_fecha_ejecucion')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
									a <input type='text' size="10" id="f_fin_fecha_ejecucion" class="casillatext_fecha" name='fin_fecha_ejecucion' value='<?=nvl($busq["fin_fecha_ejecucion"])?>' readonly /><button id="b_fin_fecha_ejecucion" onclick="javascript:showCalendarSencillo('f_fin_fecha_ejecucion','b_fin_fecha_ejecucion')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td>Hora Ejecución</td>
								<td>
									Hora Inicio:<select name='inicio_hora_ejecucion'><option value="%">...</option>
									<?for($i=0;$i<=23;$i++)
										{
											$selected="";
											if(strlen($i)==1) $hora="0".$i; else $hora=$i;
											if(nvl($busq["inicio_hora_ejecucion"])==$hora) $selected=" selected";
											echo '<option value="'.$hora.'" '.$selected.'>'.$hora.'</option>';
										}	
									?></select>
									Minuto Inicio:<select name='inicio_minuto_ejecucion'><option value="%">...</option>
									<?for($i=0;$i<=59;$i++)
										{
											$selected="";
											if(strlen($i)==1) $minuto="0".$i; else $minuto=$i;
											if(nvl($busq["inicio_minuto_ejecucion"])==$minuto) $selected=" selected";
											echo '<option value="'.$minuto.'" '.$selected.'>'.$minuto.'</option>';
										}
									?></select>
									a Hora Fin:<select name='fin_hora_ejecucion'><option value="%">...</option>
									<?for($i=0;$i<=23;$i++)
										{
											$selected="";
											if(strlen($i)==1) $hora="0".$i; else $hora=$i;
											if(nvl($busq["fin_hora_ejecucion"])==$hora) $selected=" selected";
											echo '<option value="'.$hora.'" '.$selected.'>'.$hora.'</option>';
										}	
									?></select>
									Minuto Fin:<select name='fin_minuto_ejecucion'><option value="%">...</option>
									<?for($i=0;$i<=59;$i++)
										{
											$selected="";
											if(strlen($i)==1) $minuto="0".$i; else $minuto=$i;
											if(nvl($busq["fin_minuto_ejecucion"])==$minuto) $selected=" selected";
											echo '<option value="'.$minuto.'" '.$selected.'>'.$minuto.'</option>';
										}
									?></select>
								</td>
							</tr>
							<tr>
								<td>Responsable</td>
								<td> <select name='id_responsable'><?=$responsableBusq?></select> </td>
							</tr>
							<tr>
								<td>Creador</td>
								<td><select name='id_creador'><?=$creadorBusq?></select> </td>
							</tr>
							<tr>
								<td>Planeador</td>
								<td><select name='id_planeador'><?=$planeadorBusq?></select> </td>
							</tr>
							<tr>
								<td>Estado</td>
								<td><select  name='id_estado_orden_trabajo'><?=$estadoBusq?></select></td>
							</tr>
							<tr>
								<td>Tiempo Ejecución</td>
								<td><input type='text' size='5'  name='inicio_tiempo_ejecucion' value='<?=nvl($busq["inicio_tiempo_ejecucion"])?>'> a <input type='text' size='5'  name='fin_tiempo_ejecucion' value='<?=nvl($busq["fin_tiempo_ejecucion"])?>'></td>
							</tr>
							<tr>
								<td>Km</td>
								<td><input type='text' size='5'  name='inicio_km' value='<?=nvl($busq["inicio_km"])?>'> a <input type='text' size='5'  name='fin_km' value='<?=nvl($busq["fin_km"])?>'></td>
							</tr>
							<tr>
								<td>Horómetro</td>
								<td><input type='text' size='5'  name='inicio_horometro' value='<?=nvl($busq["inicio_horometro"])?>'> a <input type='text' size='5'  name='fin_horometro' value='<?=nvl($busq["fin_horometro"])?>'></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center" height="35" valign="bottom"><input type="submit" class="boton_verde" value="Buscar"/>&nbsp;&nbsp;<input type="button" class="boton_verde" value="Limpiar" onclick="limpiarFormBusq()"/>&nbsp;&nbsp;<input type="button" class="boton_verde" value="Cerrar" onclick="window.close()"/></td>
	</tr>
</table>
</form>



<script type="text/javascript">
function revisarBusq()
{ 
	if(document.busq_form.id.value.replace(/ /g, '') =='' &&
			document.busq_form.id_rutina.options[document.busq_form.id_rutina.selectedIndex].value=='%' &&
			document.busq_form.id_sistema.options[document.busq_form.id_sistema.selectedIndex].value=='%' &&
			document.busq_form.id_tipo_mantenimiento.options[document.busq_form.id_tipo_mantenimiento.selectedIndex].value=='%' &&
			document.busq_form.id_equipo.options[document.busq_form.id_equipo.selectedIndex].value=='%' &&
			document.busq_form.id_motivo.options[document.busq_form.id_motivo.selectedIndex].value=='%' &&
			document.busq_form.inicio_fecha_planeada.value.replace(/ /g, '') =='' &&
			document.busq_form.fin_fecha_planeada.value.replace(/ /g, '') =='' &&
			document.busq_form.inicio_hora_planeada.options[document.busq_form.inicio_hora_planeada.selectedIndex].value=='%' &&
			document.busq_form.fin_hora_planeada.options[document.busq_form.fin_hora_planeada.selectedIndex].value=='%' &&
			document.busq_form.inicio_minuto_planeada.options[document.busq_form.inicio_minuto_planeada.selectedIndex].value=='%' &&
			document.busq_form.fin_minuto_planeada.options[document.busq_form.fin_minuto_planeada.selectedIndex].value=='%' &&
			document.busq_form.inicio_fecha_ejecucion.value.replace(/ /g, '') =='' &&
			document.busq_form.fin_fecha_ejecucion.value.replace(/ /g, '') =='' &&
			document.busq_form.inicio_hora_ejecucion.options[document.busq_form.inicio_hora_ejecucion.selectedIndex].value=='%' &&
			document.busq_form.fin_hora_ejecucion.options[document.busq_form.fin_hora_ejecucion.selectedIndex].value=='%' &&
			document.busq_form.inicio_minuto_ejecucion.options[document.busq_form.inicio_minuto_ejecucion.selectedIndex].value=='%' &&
			document.busq_form.fin_minuto_ejecucion.options[document.busq_form.fin_minuto_ejecucion.selectedIndex].value=='%' &&
			document.busq_form.id_responsable.options[document.busq_form.id_responsable.selectedIndex].value=='%' &&
			document.busq_form.id_creador.options[document.busq_form.id_creador.selectedIndex].value=='%' &&
			document.busq_form.id_planeador.options[document.busq_form.id_planeador.selectedIndex].value=='%' &&
			document.busq_form.id_estado_orden_trabajo.options[document.busq_form.id_estado_orden_trabajo.selectedIndex].value=='%' &&
			document.busq_form.inicio_tiempo_ejecucion.value.replace(/ /g, '') =='' &&
			document.busq_form.fin_tiempo_ejecucion.value.replace(/ /g, '') =='' &&
			document.busq_form.inicio_km.value.replace(/ /g, '') =='' &&
			document.busq_form.fin_km.value.replace(/ /g, '') =='' &&
			document.busq_form.inicio_horometro.value.replace(/ /g, '') =='' &&
			document.busq_form.fin_horometro.value.replace(/ /g, '') ==''){
				window.alert('Seleccione algún criterio de búsqueda');
				return(false);
			}

	if((document.busq_form.inicio_fecha_planeada.value !='' && document.busq_form.fin_fecha_planeada.value.replace(/ /g, '') =='') || (document.busq_form.fin_fecha_planeada.value !='' && document.busq_form.inicio_fecha_planeada.value.replace(/ /g, '') =='')){
		window.alert('[Fecha Planeada] no contiene un rango válido.');
		if(document.busq_form.inicio_fecha_planeada.value !='')
			document.busq_form.fin_fecha_planeada.focus();
		else
			document.busq_form.inicio_fecha_planeada.focus();
		return(false);
	}

	if(document.busq_form.inicio_fecha_planeada.value !='' && document.busq_form.fin_fecha_planeada.value !=''){
		if(document.busq_form.inicio_fecha_planeada.value>document.busq_form.fin_fecha_planeada.value)
		{
			window.alert('[Fecha Planeada] no contiene un rango válido.');
			document.busq_form.fin_fecha_planeada.focus();
			return(false);
		}
	}

	if(document.busq_form.inicio_hora_planeada.options[document.busq_form.inicio_hora_planeada.selectedIndex].value!='%' || document.busq_form.fin_hora_planeada.options[document.busq_form.fin_hora_planeada.selectedIndex].value!='%' || document.busq_form.inicio_minuto_planeada.options[document.busq_form.inicio_minuto_planeada.selectedIndex].value!='%' || document.busq_form.fin_minuto_planeada.options[document.busq_form.fin_minuto_planeada.selectedIndex].value!='%'){

		if((document.busq_form.inicio_hora_planeada.options[document.busq_form.inicio_hora_planeada.selectedIndex].value=='%' && document.busq_form.inicio_minuto_planeada.options[document.busq_form.inicio_minuto_planeada.selectedIndex].value!='%') || (document.busq_form.inicio_hora_planeada.options[document.busq_form.inicio_hora_planeada.selectedIndex].value!='%' && document.busq_form.inicio_minuto_planeada.options[document.busq_form.inicio_minuto_planeada.selectedIndex].value=='%')){
			window.alert('[Hora Planeada (inicio)] no contiene un rango válido.');
			return(false);
		}
		pi = document.busq_form.inicio_hora_planeada.options[document.busq_form.inicio_hora_planeada.selectedIndex].value+':'+document.busq_form.inicio_minuto_planeada.options[document.busq_form.inicio_minuto_planeada.selectedIndex].value;

		if((document.busq_form.fin_hora_planeada.options[document.busq_form.fin_hora_planeada.selectedIndex].value=='%' && document.busq_form.fin_minuto_planeada.options[document.busq_form.fin_minuto_planeada.selectedIndex].value!='%') || (document.busq_form.fin_hora_planeada.options[document.busq_form.fin_hora_planeada.selectedIndex].value!='%' && document.busq_form.fin_minuto_planeada.options[document.busq_form.fin_minuto_planeada.selectedIndex].value=='%')){
			window.alert('[Hora Planeada (fin)] no contiene un rango válido.');
			return(false);
		}
		pf = document.busq_form.fin_hora_planeada.options[document.busq_form.fin_hora_planeada.selectedIndex].value+':'+document.busq_form.fin_minuto_planeada.options[document.busq_form.fin_minuto_planeada.selectedIndex].value;
		
		if(pi=='%:%' || pf=='%:%')
		{
			window.alert('[Hora Planeada] no contiene un rango válido.');
			return(false);
		}
		if(pi>pf)
		{
			window.alert('[Hora Planeada] no contiene un rango válido.');
			return(false);
		}
	}

	if(document.busq_form.inicio_hora_ejecucion.options[document.busq_form.inicio_hora_ejecucion.selectedIndex].value!='%' || document.busq_form.fin_hora_ejecucion.options[document.busq_form.fin_hora_ejecucion.selectedIndex].value!='%' || document.busq_form.inicio_minuto_ejecucion.options[document.busq_form.inicio_minuto_ejecucion.selectedIndex].value!='%' || document.busq_form.fin_minuto_ejecucion.options[document.busq_form.fin_minuto_ejecucion.selectedIndex].value!='%'){

		if((document.busq_form.inicio_hora_ejecucion.options[document.busq_form.inicio_hora_ejecucion.selectedIndex].value=='%' && document.busq_form.inicio_minuto_ejecucion.options[document.busq_form.inicio_minuto_ejecucion.selectedIndex].value!='%') || (document.busq_form.inicio_hora_ejecucion.options[document.busq_form.inicio_hora_ejecucion.selectedIndex].value!='%' && document.busq_form.inicio_minuto_ejecucion.options[document.busq_form.inicio_minuto_ejecucion.selectedIndex].value=='%')){
			window.alert('[Hora Ejecución (inicio)] no contiene un rango válido.');
			return(false);
		}
		pi = document.busq_form.inicio_hora_ejecucion.options[document.busq_form.inicio_hora_ejecucion.selectedIndex].value+':'+document.busq_form.inicio_minuto_ejecucion.options[document.busq_form.inicio_minuto_ejecucion.selectedIndex].value;

		if((document.busq_form.fin_hora_ejecucion.options[document.busq_form.fin_hora_ejecucion.selectedIndex].value=='%' && document.busq_form.fin_minuto_ejecucion.options[document.busq_form.fin_minuto_ejecucion.selectedIndex].value!='%') || (document.busq_form.fin_hora_ejecucion.options[document.busq_form.fin_hora_ejecucion.selectedIndex].value!='%' && document.busq_form.fin_minuto_ejecucion.options[document.busq_form.fin_minuto_ejecucion.selectedIndex].value=='%')){
			window.alert('[Hora Ejecución (fin)] no contiene un rango válido.');
			return(false);
		}
		pf = document.busq_form.fin_hora_ejecucion.options[document.busq_form.fin_hora_ejecucion.selectedIndex].value+':'+document.busq_form.fin_minuto_ejecucion.options[document.busq_form.fin_minuto_ejecucion.selectedIndex].value;
		
		if(pi=='%:%' || pf=='%:%')
		{
			window.alert('[Hora Ejecución] no contiene un rango válido.');
			return(false);
		}
		if(pi>pf)
		{
			window.alert('[Hora Ejecución] no contiene un rango válido.');
			return(false);
		}
	}
			
	if(document.busq_form.inicio_tiempo_ejecucion.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.busq_form.inicio_tiempo_ejecucion.value)){
			window.alert('[Tiempo Ejecución (inicio)] no contiene un dato válido.');
			document.busq_form.inicio_tiempo_ejecucion.focus();
			return(false);
		}
	}
	if(document.busq_form.fin_tiempo_ejecucion.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.busq_form.fin_tiempo_ejecucion.value)){
			window.alert('[Tiempo Ejecución (fin)] no contiene un dato válido.');
			document.busq_form.fin_tiempo_ejecucion.focus();
			return(false);
		}
	}
	if((document.busq_form.inicio_tiempo_ejecucion.value !='' && document.busq_form.fin_tiempo_ejecucion.value.replace(/ /g, '') =='') || (document.busq_form.fin_tiempo_ejecucion.value !='' && document.busq_form.inicio_tiempo_ejecucion.value.replace(/ /g, '') =='')){
		window.alert('[Tiempo Ejecución] no contiene un rango válido.');
		if(document.busq_form.inicio_tiempo_ejecucion.value !='')
			document.busq_form.fin_tiempo_ejecucion.focus();
		else
			document.busq_form.inicio_tiempo_ejecucion.focus();
		return(false);
	}
	if(document.busq_form.inicio_tiempo_ejecucion.value > document.busq_form.fin_tiempo_ejecucion.value){
		window.alert('[Tiempo Ejecución] no contiene un rango válido.');
		document.busq_form.fin_tiempo_ejecucion.focus();
		return(false);
	}
	if(document.busq_form.inicio_km.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.busq_form.inicio_km.value)){
			window.alert('[Km (inicio)] no contiene un dato válido.');
			document.busq_form.inicio_km.focus();
			return(false);
		}
	}
	if(document.busq_form.fin_km.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.busq_form.fin_km.value)){
			window.alert('[Km (fin)] no contiene un dato válido.');
			document.busq_form.fin_km.focus();
			return(false);
		}
	}
	if((document.busq_form.inicio_km.value !='' && document.busq_form.fin_km.value.replace(/ /g, '') =='') || (document.busq_form.fin_km.value !='' && document.busq_form.inicio_km.value.replace(/ /g, '') =='')){
		window.alert('[Km] no contiene un rango válido.');
		if(document.busq_form.inicio_km.value !='')
			document.busq_form.fin_km.focus();
		else
			document.busq_form.inicio_km.focus();
		return(false);
	}
	if(document.busq_form.inicio_km.value > document.busq_form.fin_km.value){
		window.alert('[Km] no contiene un rango válido.');
		document.busq_form.fin_km.focus();
		return(false);
	}
	
	if(document.busq_form.inicio_horometro.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.busq_form.inicio_horometro.value)){
			window.alert('[Horómetro (inicio)] no contiene un dato válido.');
			document.busq_form.inicio_horometro.focus();
			return(false);
		}
	}
	if(document.busq_form.fin_horometro.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.busq_form.fin_horometro.value)){
			window.alert('[Horómetro (fin)] no contiene un dato válido.');
			document.busq_form.fin_horometro.focus();
			return(false);
		}
	}
	if((document.busq_form.inicio_horometro.value !='' && document.busq_form.fin_horometro.value.replace(/ /g, '') =='') || (document.busq_form.fin_horometro.value !='' && document.busq_form.inicio_horometro.value.replace(/ /g, '') =='')){
		window.alert('[Horómetro] no contiene un rango válido.uno');
		if(document.busq_form.inicio_horometro.value !='')
			document.busq_form.fin_horometro.focus();
		else
			document.busq_form.inicio_horometro.focus();
		return(false);
	}
	if(document.busq_form.inicio_horometro.value>document.busq_form.fin_horometro.value){
		window.alert('[Horómetro] no contiene un rango válido.dos');
		document.busq_form.fin_horometro.focus();
		return(false);
	}

	return true;
}

function limpiarFormBusq()
{
	document.busq_form.id.value='';
	document.busq_form.id_tipo_mantenimiento.value='%';
	document.busq_form.id_sistema.value='%';
	document.busq_form.id_rutina.value='%';
	document.busq_form.id_equipo.value='%';
	document.busq_form.id_motivo.value='%';
	document.busq_form.inicio_fecha_planeada.value ='';
	document.busq_form.fin_fecha_planeada.value='';
	document.busq_form.inicio_hora_planeada.value='%';
	document.busq_form.fin_hora_planeada.value='%';
	document.busq_form.inicio_minuto_planeada.value='%';
	document.busq_form.fin_minuto_planeada.value='%';
	document.busq_form.inicio_fecha_ejecucion.value ='';
	document.busq_form.fin_fecha_ejecucion.value='';
	document.busq_form.inicio_hora_ejecucion.value='%';
	document.busq_form.fin_hora_ejecucion.value='%';
	document.busq_form.inicio_minuto_ejecucion.value='%';
	document.busq_form.fin_minuto_ejecucion.value='%';
	document.busq_form.id_responsable.value='%';
	document.busq_form.id_creador.value='%';
	document.busq_form.id_planeador.value='%';
	document.busq_form.id_estado_orden_trabajo.value='%';
	document.busq_form.inicio_tiempo_ejecucion.value ='';
	document.busq_form.fin_tiempo_ejecucion.value ='';
	document.busq_form.inicio_km.value ='';
	document.busq_form.fin_km.value ='';
	document.busq_form.inicio_horometro.value ='';
	document.busq_form.fin_horometro.value ='';
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

var oXmlHttp_id_rutina;
function updateRecursive_id_rutina(){
	namediv='id_rutina';
	nameId='id_rutina';
	id_tipo_mantenimiento=document.busq_form.id_tipo_mantenimiento.options[document.busq_form.id_tipo_mantenimiento.selectedIndex].value;
	id_sistema=document.busq_form.id_sistema.options[document.busq_form.id_sistema.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateMtto.php?tipo=rutinasxtipoysistema&id_tipo_mantenimiento=" + id_tipo_mantenimiento + "&id_sistema=" + id_sistema + "&divid=" + namediv;
	oXmlHttp_id_rutina=GetHttpObject(cambiarRecursive_id_rutina);
	oXmlHttp_id_rutina.open("GET", url , true);
	oXmlHttp_id_rutina.send(null);
}
function cambiarRecursive_id_rutina(){
	if (oXmlHttp_id_rutina.readyState==4 || oXmlHttp_id_rutina.readyState=="complete"){
		document.getElementById('id_rutina').innerHTML=oXmlHttp_id_rutina.responseText
	}
}


</script>
