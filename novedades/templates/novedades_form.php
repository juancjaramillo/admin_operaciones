<form name="entryform" action="<?=$ME?>" method="POST"  class="form" enctype="multipart/form-data" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="mode2" value="">
<input type="hidden" name="id" value="<?=nvl($nov["id"])?>">
<input type="hidden" name="clase" value="<?=$nov["clase"]?>">
<input type="hidden" name="id_movimiento" value="<?=nvl($nov["id_movimiento"])?>">
<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong><?=strtoupper($newMode)?> NOVEDADES (Novedad No. <?echo $nov["id"];?>)</strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<?if($nov["clase"] == "mtto"){?>
							<tr>
								<td align='right'>(*) Equipo</td>
								<td align='left'> <select  name='id_equipo' onChange="updateRecursive_id_reporta_dequipo()"><?=$equipos?></select> </td>
							</tr>
							<?}else{?>
							<tr>
								<td align='right'>(*) Centro</td>
								<td align='left'> <select  name='id_centro' onChange="updateRecursive_id_reporta_dcentro(), updateRecursive_id_vehiculo_apoyo()"><?=$centros?></select> </td>
							</tr>
							<?}?>
							<tr>
								<td align='right'>(*) Clase</td>
								<td align='left'> 
									<select  name='esquema' onChange="updateRecursive_id_tipo_novedad()">
										<option value="%">Seleccione...
										<?if($nov["clase"] == "mtto"){?>
										<option value="mtto" <?if(nvl($nov["esquema"]) == "mtto") echo "SELECTED"?>>Mantenimiento</option>
										<?}else{?>
										<option value="bar" <?if(nvl($nov["esquema"]) == "bar") echo "SELECTED"?>>Barrido</option>
										<option value="esp" <?if(nvl($nov["esquema"]) == "esp") echo "SELECTED"?>>Especiales</option>
										<option value="gp" <?if(nvl($nov["esquema"]) == "gp") echo "SELECTED"?>>Grandes Productores</option>
										<option value="rec" <?if(nvl($nov["esquema"]) == "rec") echo "SELECTED"?>>Recolección</option>
										<?}?>
									</select> 
								</td>
							</tr>
							<tr>
								<td align='right'>Tipo</td>
								<td align='left'> 
									<div id="id_tipo_novedad"><select  name='id_tipo_novedad' id="id_tipo_novedad" style="width:250px"><option value="%">Seleccione...</option><?=nvl($tipos)?></select></div>
								</td>
							</tr>
							<tr>
								<td align='right'>(*) Fecha Inicio</td>
								<td align='left'>
									<input size="20" id="f_hora_inicio" class="casillatext_fecha" name='hora_inicio' value='<?=nvl($nov["hora_inicio"],date("Y-m-d H:i:s"))?>' /><button id="b_hora_inicio" onclick="javascript:showCalendarHora('f_hora_inicio','b_hora_inicio')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>Fecha Fin</td>
								<td align='left'>
									<input size="20" id="f_hora_fin" class="casillatext_fecha" name='hora_fin' value='<?=nvl($nov["hora_fin"])?>' /><button id="b_hora_fin" onclick="javascript:showCalendarHora('f_hora_fin','b_hora_fin')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>(*) Persona que ingresa al sistema</td>
								<td align='left'> <select  name='id_ingresa'><?=$personas_i?></select> </td>
							</tr>
							<tr>
								<td align='right'>Persona que reporta la novedad</td>
								<td align='left'><div id="id_reporta"> <select  name='id_reporta' id="id_reporta" style="width:250px"><?=$personas_r?></select></div> </td>
							</tr>
							<tr>
								<td align='right'>(*) Observaciones</td>
								<td align='left'><textarea  cols="50" name='observaciones'><?=nvl($nov["observaciones"])?></textarea></td>
							</tr>
							<?if($nov["clase"] == ""){?>
							<tr>
								<td align='right'>Vehículo Apoyo</td>
								<td align='left'><div id="id_vehiculo_apoyo"> <select name='id_vehiculo_apoyo' id="id_vehiculo_apoyo" style="width:250px"><?=$vehiculos?></select> </div></td>
							</tr>
							<?}?>
						</table>
					</td>
				</tr>
			</table>
		</td>
		<?
		if($newMode != "insertar"){
			$ot = $db->sql_row("SELECT count(*) as num FROM mtto.ordenes_trabajo WHERE id_novedad=".$nov["id"]);
			if($ot["num"] != 0)
			{?>
				<td width="20px">
					  &nbsp;
				</td>

				<td valign="top">
					<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
						<tr>
							<td>
								<table width="100%" border=1 bordercolor="#7fa840" id="tabla_actividad">
									<tr>
										<td align='left' colspan=3><span class="azul_12">ORDENES DE TRABAJO RELACIONADAS</span></td>
									</tr>
									<tr>
										<td align='center'>FECHA PLANEADA</td>
										<td align='center' width="10%">RUTINA</td>
										<td align='center' width="10%">OPCIONES</td>
									</tr>
									<?
									$qidR = $db->sql_query("SELECT ot.id, fecha_planeada, rutina 
											FROM mtto.ordenes_trabajo ot 
											LEFT JOIN mtto.rutinas r ON r.id=ot.id_rutina 
											WHERE ot.id_novedad=".$nov["id"]);
									while($ot = $db->sql_fetchrow($qidR))				
									{?>
										<tr>
											<td><?=$ot["fecha_planeada"]?></td>
											<td><?=$ot["rutina"]?></td>
											<td align='center'><a href="javascript:abrirVentanaJavaScript('ordenes','900','500','<?=$CFG->wwwroot?>/mtto/ordenes.php?mode=editar&id=<?=$ot["id"]?>')"><img alt='Editar\' src='<?=$CFG->wwwroot?>/admin/iconos/transparente/iconoeditar.gif' border='0'></a></td>
										</tr>
									<?}?>
								</table>
							</td>
						</tr>
					</table>
				</td>
		<?} 
		}
		?>

	</tr>
	<tr>
		<td colspan=3 align="center">
			<?if($nov["hora_fin"] == "" || in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["novedades_guardar_cerrada"]) ){?>
			<input type="submit" class="boton_verde" value="Aceptar" />
			<?}?>
			<input type="button" class="boton_verde" value="Cancelar" onclick="window.close()"/>
			<?if($newMode != "insertar"){?>
			<input type="button" class="boton_rojo" value="Eliminar" onclick="eliminar()"/>
			<?if($nov["clase"] == "mtto" && in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["novedades_agregar_OT"])){?>
			<input type="button" class="boton_verde" value="Generar OT" onclick="generarOT()"/>
			<?}}?>
		</td>
	</tr>
	</form>
</table>
<script type="text/javascript">

function revisar()
{
	<?if($nov["clase"] == "mtto"){?>
	if(document.entryform.id_equipo.options[document.entryform.id_equipo.selectedIndex].value=='%'){
		window.alert('Por favor seleccione Equipo');
		return(false);
	}
	<?}else{?>
	if(document.entryform.id_centro.options[document.entryform.id_centro.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Centro');
		document.entryform.id_centro.focus();
		return(false);
	}
	<?}?>


	if(document.entryform.esquema.options[document.entryform.esquema.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Clase');
		document.entryform.esquema.focus();
		return(false);
	}

	if(document.entryform.hora_inicio.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Fecha Inicio');
		document.entryform.hora_inicio.focus();
		return(false);
	}
	else{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.hora_inicio.value)){
			window.alert('[Fecha Inicio] no contiene un dato válido.');
			document.entryform.hora_inicio.focus();
			return(false);
		}
	}

	if(document.entryform.hora_fin.value.replace(/ /g, '') !=''){
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.hora_fin.value)){
			window.alert('[Fecha Fin] no contiene un dato válido.');
			document.entryform.hora_fin.focus();
			return(false);
		}
	}

	if(document.entryform.observaciones.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Observaciones');
		document.entryform.observaciones.focus();
		return(false);
	}

	return(true);
}

function generarOT(){
	id_equipo=document.entryform.id_equipo.options[document.entryform.id_equipo.selectedIndex].value;
<?if($newMode == "insertar"){?>
	if(revisar()){
		document.entryform.mode2.value='generarOT';
		document.entryform.submit();
	}
<?}else{?>
	abrirVentanaJavaScript('ordenes','600','500','<?=$CFG->wwwroot?>/mtto/ordenes.php?mode=agregar_facil&id_equipo=' + id_equipo + '&id_novedad=<?=$id?>');
<?}?>
}

function eliminar()
{
	texto='¿Está seguro de querer borrar la novedad?';
	if(!confirm(texto)) return;

	document.entryform.mode.value='eliminar';
	document.entryform.submit();
	return;
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

var oXmlHttp_id_tipo_novedad;
function updateRecursive_id_tipo_novedad(){
	namediv='id_tipo_novedad';
	nameId='id_tipo_novedad';
	clase=document.entryform.esquema.options[document.entryform.esquema.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateNovedades.php?tipo=listar_tipo_novedad&clase=" + clase + "&divid=" + namediv;
	oXmlHttp_id_tipo_novedad=GetHttpObject(cambiarRecursive_id_tipo_novedad);
	oXmlHttp_id_tipo_novedad.open("GET", url , true);
	oXmlHttp_id_tipo_novedad.send(null);
}
function cambiarRecursive_id_tipo_novedad(){
	if (oXmlHttp_id_tipo_novedad.readyState==4 || oXmlHttp_id_tipo_novedad.readyState=="complete"){
		document.getElementById('id_tipo_novedad').innerHTML=oXmlHttp_id_tipo_novedad.responseText
	}
}


var oXmlHttp_id_reporta;
function updateRecursive_id_reporta_dcentro(){
	namediv='id_reporta';
	nameId='id_reporta';
	id_centro=document.entryform.id_centro.options[document.entryform.id_centro.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateNovedades.php?tipo=listar_reportadcentro&id_centro=" + id_centro + "&divid=" + namediv;
	oXmlHttp_id_reporta=GetHttpObject(cambiarRecursive_id_reporta);
	oXmlHttp_id_reporta.open("GET", url , true);
	oXmlHttp_id_reporta.send(null);
}

function updateRecursive_id_reporta_dequipo(){
	namediv='id_reporta';
	nameId='id_reporta';
	id_equipo=document.entryform.id_equipo.options[document.entryform.id_equipo.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateNovedades.php?tipo=listar_reportadequipo&id_equipo=" + id_equipo + "&divid=" + namediv;
	oXmlHttp_id_reporta=GetHttpObject(cambiarRecursive_id_reporta);
	oXmlHttp_id_reporta.open("GET", url , true);
	oXmlHttp_id_reporta.send(null);
}

function cambiarRecursive_id_reporta(){
	if (oXmlHttp_id_reporta.readyState==4 || oXmlHttp_id_reporta.readyState=="complete"){
		document.getElementById('id_reporta').innerHTML=oXmlHttp_id_reporta.responseText
	}
}

function updateRecursive_id_vehiculo_apoyo(){
	namediv='id_vehiculo_apoyo';
	nameId='id_vehiculo_apoyo';
	id_centro=document.entryform.id_centro.options[document.entryform.id_centro.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateNovedades.php?tipo=listar_id_vehiculo_apoyo&id_centro=" + id_centro + "&divid=" + namediv;
	oXmlHttp_id_vehiculo_apoyo=GetHttpObject(cambiarRecursive_id_vehiculo_apoyo);
	oXmlHttp_id_vehiculo_apoyo.open("GET", url , true);
	oXmlHttp_id_vehiculo_apoyo.send(null);
}
function cambiarRecursive_id_vehiculo_apoyo(){
	if (oXmlHttp_id_vehiculo_apoyo.readyState==4 || oXmlHttp_id_vehiculo_apoyo.readyState=="complete"){
		document.getElementById('id_vehiculo_apoyo').innerHTML=oXmlHttp_id_vehiculo_apoyo.responseText
	}
}




<?
if(isset($_GET["mode2"]) && $_GET["mode2"]=="generarOT") echo "generarOT();\n";
?>
</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>
