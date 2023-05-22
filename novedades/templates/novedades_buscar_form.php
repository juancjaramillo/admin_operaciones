<form name="entryform" action="<?=$ME?>" method="POST"  class="form" enctype="multipart/form-data" onSubmit="return revisar()">
<input type="hidden" name="mode" value="resultados">
<input type="hidden" name="clase" value="<?=$clase?>">

<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong>BÚSQUEDA NOVEDADES</strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td align='right'>Centro</td>
								<td align='left'> <select  name='id_centro' onChange="updateRecursive_id_reporta_dcentro(), updateRecursive_id_ingresa_dcentro(), updateRecursive_id_vehiculo_apoyo(), updateRecursive_id_equipo()"><?=$centros?></select> </td>
							</tr>
							<?if($clase == "mtto"){?>
							<tr>
								<td align='right'>Equipo</td>
								<td align='left'><div id="id_equipo"> <select name='id_equipo' id="id_equipo" style="width:250px"><?=$equipos?></select> </div></td>
							</tr>
							<?}?>
							<tr>
								<td align='right'>Clase</td>
								<td align='left'>
									<select  name='esquema' onChange="updateRecursive_id_tipo_novedad()">
									<option value="%">Seleccione...
									<?if($clase == "mtto"){?>
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
								<td align='right'>Fecha Inicio</td>
								<td align='left'>
									<input size="20" id="f_hora_inicio_inicio" class="casillatext_fecha" name='hora_inicio_inicio' value='' /><button id="b_hora_inicio_inicio" onclick="javascript:showCalendarHora('f_hora_inicio_inicio','b_hora_inicio_inicio')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button> - 
									<input size="20" id="f_hora_inicio_fin" class="casillatext_fecha" name='hora_inicio_fin' value='' /><button id="b_hora_inicio_fin" onclick="javascript:showCalendarHora('f_hora_inicio_fin','b_hora_inicio_fin')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>Fecha Fin</td>
								<td align='left'>
									<input type='text' size='20' class="casillatext_fecha" name='hora_fin_inicio' value='' id="f_hora_fin_inicio"><button id="b_hora_fin_inicio" onclick="javascript:showCalendarHora('f_hora_fin_inicio','b_hora_fin_inicio')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button> - 
									<input type='text' size='20' class="casillatext_fecha" name='hora_fin_fin' value='' id='f_hora_fin_fin'><button id="b_hora_fin_fin" onclick="javascript:showCalendarHora('f_hora_fin_fin','b_hora_fin_fin')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>Persona que ingresa al sistema:</td>
								<td align='left'> <div id="id_ingresa"> <select  name='id_ingresa' id="id_ingresa"><?=$personas_i?></select> </div></td>
							</tr>
							<tr>
								<td align='right'>Persona que reporta la novedad</td>
								<td align='left'><div id="id_reporta"> <select  name='id_reporta' id="id_reporta" style="width:250px"><?=$personas_r?></select></div> </td>
							</tr>
							<tr>
								<td align='right'>Observaciones</td>
								<td align='left'><textarea  name='observaciones'><?=nvl($nov["observaciones"])?></textarea></td>
							</tr>
							<tr>
								<td align='right'>Abierta/Cerrada</td>
								<td align='left'>
									<select  name='estado'>
										<option value="%">Seleccione...</option>
										<option value="abierta">Abierta</option>
										<option value="cerrada">Cerrada</option>
									</select>
								</td>
							</td>
							<?if($clase == ""){?>
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
	</tr>
	<tr>
		<td colspan=3 align="center">
			<input type="submit" class="boton_verde" value="Buscar" />
			<input type="button" class="boton_verde" value="Cancelar" onclick="window.close()"/>
		</td>
	</tr>
	</form>
</table>
<script type="text/javascript">

function revisar()
{
	<?if($clase == ""){?>
	if(document.entryform.id_centro.options[document.entryform.id_centro.selectedIndex].value=='%' && document.entryform.esquema.options[document.entryform.esquema.selectedIndex].value=='%' && document.entryform.id_tipo_novedad.options[document.entryform.id_tipo_novedad.selectedIndex].value=='%' && document.entryform.hora_inicio_inicio.value.replace(/ /g, '') =='' && document.entryform.hora_inicio_fin.value.replace(/ /g, '') =='' && document.entryform.hora_fin_inicio.value.replace(/ /g, '') =='' && document.entryform.hora_fin_fin.value.replace(/ /g, '') =='' && document.entryform.id_ingresa.options[document.entryform.id_ingresa.selectedIndex].value=='%' && document.entryform.id_reporta.options[document.entryform.id_reporta.selectedIndex].value=='%' && document.entryform.observaciones.value.replace(/ /g, '') =='' && document.entryform.id_vehiculo_apoyo.options[document.entryform.id_vehiculo_apoyo.selectedIndex].value=='%'){
		window.alert('Por favor seleccione algun criterio para buscar');
		return(false);
	}
	<?}else{?>
	if(document.entryform.id_centro.options[document.entryform.id_centro.selectedIndex].value=='%' && document.entryform.esquema.options[document.entryform.esquema.selectedIndex].value=='%' && document.entryform.id_tipo_novedad.options[document.entryform.id_tipo_novedad.selectedIndex].value=='%' && document.entryform.hora_inicio_inicio.value.replace(/ /g, '') =='' && document.entryform.hora_inicio_fin.value.replace(/ /g, '') =='' && document.entryform.hora_fin_inicio.value.replace(/ /g, '') =='' && document.entryform.hora_fin_fin.value.replace(/ /g, '') =='' && document.entryform.id_ingresa.options[document.entryform.id_ingresa.selectedIndex].value=='%' && document.entryform.id_reporta.options[document.entryform.id_reporta.selectedIndex].value=='%' && document.entryform.observaciones.value.replace(/ /g, '') =='' && document.entryform.id_equipo.options[document.entryform.id_equipo.selectedIndex].value=='%'){
		window.alert('Por favor seleccione algun criterio para buscar');
		return(false);
	}
	<?}?>

	if(document.entryform.hora_inicio_inicio.value.replace(/ /g, '') !=''){
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.hora_inicio_inicio.value)){
			window.alert('[Fecha Inicio] no contiene un dato válido.');
			document.entryform.hora_inicio_inicio.focus();
			return(false);
		}
	}
	if(document.entryform.hora_inicio_fin.value.replace(/ /g, '') !=''){
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.hora_inicio_fin.value)){
			window.alert('[Fecha Inicio] no contiene un dato válido.');
			document.entryform.hora_inicio_fin.focus();
			return(false);
		}
	}

	if((document.entryform.hora_inicio_inicio.value !='' && document.entryform.hora_inicio_fin.value.replace(/ /g, '') =='') || (document.entryform.hora_inicio_fin.value !='' && document.entryform.hora_inicio_inicio.value.replace(/ /g, '') =='')){
	 window.alert('[Fecha Inicio] no contiene un rango válido.');
	 if(document.entryform.hora_inicio_inicio.value !='')
		 document.entryform.hora_inicio_fin.focus();
	 else
		 document.entryform.hora_inicio_inicio.focus();
	 return(false);
	}

	if(document.entryform.hora_inicio_inicio.value > document.entryform.hora_inicio_fin.value){
	 window.alert('[Fecha Inicio] no contiene un rango válido.');
	 return(false);
	}

	if(document.entryform.hora_fin_inicio.value.replace(/ /g, '') !=''){
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.hora_fin_inicio.value)){
			window.alert('[Fecha Fin] no contiene un dato válido.');
			document.entryform.hora_fin_inicio.focus();
			return(false);
		}
	}

	if(document.entryform.hora_fin_fin.value.replace(/ /g, '') !=''){
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.hora_fin_fin.value)){
			window.alert('[Fecha Fin] no contiene un dato válido.');
			document.entryform.hora_fin_fin.focus();
			return(false);
		}
	}

	if((document.entryform.hora_fin_inicio.value !='' && document.entryform.hora_fin_fin.value.replace(/ /g, '') =='') || (document.entryform.hora_fin_fin.value !='' && document.entryform.hora_fin_inicio.value.replace(/ /g, '') =='')){
	 window.alert('[Fecha Fin] no contiene un rango válido.');
	 if(document.entryform.hora_fin_fin.value !='')
		 document.entryform.hora_fin_inicio.focus();
	 else
		 document.entryform.hora_fin_fin.focus();
	 return(false);
	}

	if(document.entryform.hora_fin_inicio.value > document.entryform.hora_fin_fin.value){
	 window.alert('[Fecha Fin] no contiene un rango válido.');
	 return(false);
	}

	return(true);
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

function cambiarRecursive_id_reporta(){
	if (oXmlHttp_id_reporta.readyState==4 || oXmlHttp_id_reporta.readyState=="complete"){
		document.getElementById('id_reporta').innerHTML=oXmlHttp_id_reporta.responseText
	}
}

var oXmlHttp_id_ingresa;
function updateRecursive_id_ingresa_dcentro(){
	namediv='id_ingresa';
	nameId='id_ingresa';
	id_centro=document.entryform.id_centro.options[document.entryform.id_centro.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateNovedades.php?tipo=listar_ingresadcentro&id_centro=" + id_centro + "&divid=" + namediv;
	oXmlHttp_id_ingresa=GetHttpObject(cambiarRecursive_id_ingresa);
	oXmlHttp_id_ingresa.open("GET", url , true);
	oXmlHttp_id_ingresa.send(null);
}

function cambiarRecursive_id_ingresa(){
	if (oXmlHttp_id_ingresa.readyState==4 || oXmlHttp_id_ingresa.readyState=="complete"){
		document.getElementById('id_ingresa').innerHTML=oXmlHttp_id_ingresa.responseText
	}
}

function updateRecursive_id_vehiculo_apoyo(){
	<?if($clase==""){?>
	namediv='id_vehiculo_apoyo';
	nameId='id_vehiculo_apoyo';
	id_centro=document.entryform.id_centro.options[document.entryform.id_centro.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateNovedades.php?tipo=listar_id_vehiculo_apoyo&id_centro=" + id_centro + "&divid=" + namediv;
	oXmlHttp_id_vehiculo_apoyo=GetHttpObject(cambiarRecursive_id_vehiculo_apoyo);
	oXmlHttp_id_vehiculo_apoyo.open("GET", url , true);
	oXmlHttp_id_vehiculo_apoyo.send(null);
	<?}?>
}
function cambiarRecursive_id_vehiculo_apoyo(){
	if (oXmlHttp_id_vehiculo_apoyo.readyState==4 || oXmlHttp_id_vehiculo_apoyo.readyState=="complete"){
		document.getElementById('id_vehiculo_apoyo').innerHTML=oXmlHttp_id_vehiculo_apoyo.responseText
	}
}

function updateRecursive_id_equipo(){
	<?if($clase=="mtto"){?>
	namediv='id_equipo';
	nameId='id_equipo';
	id_centro=document.entryform.id_centro.options[document.entryform.id_centro.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateNovedades.php?tipo=listar_id_equipoxcentro&id_centro=" + id_centro + "&divid=" + namediv;
	oXmlHttp_id_equipo=GetHttpObject(cambiarRecursive_id_equipo);
	oXmlHttp_id_equipo.open("GET", url , true);
	oXmlHttp_id_equipo.send(null);
	<?}?>
}
function cambiarRecursive_id_equipo(){
	if (oXmlHttp_id_equipo.readyState==4 || oXmlHttp_id_equipo.readyState=="complete"){
		document.getElementById('id_equipo').innerHTML=oXmlHttp_id_equipo.responseText
	}
}




</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>
