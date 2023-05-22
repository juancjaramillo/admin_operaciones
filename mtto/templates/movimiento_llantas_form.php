<form name="entryform" action="<?=$ME?>" method="POST"  class="form" enctype="multipart/form-data" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id" value="<?=nvl($mov["id"])?>">
<input type="hidden" name="id_llanta" value="<?=nvl($mov["id_llanta"])?>">
<input type="hidden" name="facil" value="<?=nvl($facil,false)?>">
<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong><?=strtoupper(str_replace("_"," ",$newMode))?> DE LA LLANTA <?=$mov["numero"]?></strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td align='right'>(*) Fecha</td>
								<td align='left'>
									<input type='text' size="10" id="f_fecha" class="casillatext_fecha" name='fecha' value='<?=nvl($mov["fecha"],date("Y-m-d"))?>' /><button id="b_fecha" onclick="javascript:showCalendarSencillo('f_fecha','b_fecha')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>(*) Tipo Movimiento</td>
								<td align='left'> <select  name='id_tipo_movimiento' onChange="updateRecursive_id_subtipo_movimiento(this)"><?=$tipos?></select> </td>
							</tr>
							<tr>
								<td align='right'>Subtipo Movimiento</td>
								<td align='left'> <div id="id_subtipo_movimiento"><select  name="id_subtipo_movimiento" id="id_subtipo_movimiento" style="width:250px" ><option value="%">Seleccione...</option><?=nvl($subtipos)?></select></div></td>
							</tr>
							<tr>
								<td align='right'>Km</td>
								<td align='left'><input type='text' size='10'  name='km' class='casillatext' value='<?=nvl($mov["km"])?>'></td>
							</tr>
							<tr>
								<td align='right'>Horas</td>
								<td align='left'><input type='text' size='10'  name='horas' class='casillatext' value='<?=nvl($mov["horas"])?>'></td>
							</tr>
							<tr>
								<td align='right'>Profundidad Uno</td>
								<td align='left'><input type='text' size='10'  name='prof_uno' class='casillatext' value='<?=nvl($mov["prof_uno"])?>'></td>
							</tr>
							<tr>
								<td align='right'>Profundidad Dos</td>
								<td align='left'><input type='text' size='10'  name='prof_dos' class='casillatext' value='<?=nvl($mov["prof_dos"])?>'></td>
							</tr>
							<tr>
								<td align='right'>Profundidad Tres</td>
								<td align='left'><input type='text' size='10'  name='prof_tres' class='casillatext' value='<?=nvl($mov["prof_tres"])?>'></td>
							</tr>
							<tr>
								<td align='right'>Vehículo</td>
								<td align='left'><select  name="id_vehiculo" id="id_vehiculo" onChange="updateRecursive_posicion(this)"><option value="%">Seleccione...</option><?=nvl($vehiculos)?></select></td>
							</tr>
							<tr>
								<td align='right'>Posición</td>
								<td align='left'> <div id="posicion"><select  name="posicion" id="posicion" style="width:250px"><option value="%">Seleccione...</option><?=nvl($posiciones)?></select></div></td>
							</tr>
							<tr>
								<td align='right'>Costo</td>
								<td align='left'><input type='text' size='10'  name='costo' class='casillatext' value='<?=nvl($mov["costo"])?>'></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=3 align="center">
			<?if($newMode != "detalles_movimiento"){?>
			<input type="submit" class="boton_verde" value="Aceptar" />
			<input type="button" class="boton_verde" value="Cancelar" onclick="window.close()"/>
			<?}else{
				if($popup!=""){?>
					<input type="button" class="boton_verde" value="Cerrar" onclick="window.close()"/>
				<?}else{?>
					<input type="button" class="boton_verde" value="Cancelar" onclick="javascript:window.history.back();"/>
			<?}}?>
		</td>
	</tr>
	</form>
</table>
<script type="text/javascript">

function revisar()
{
	if(document.entryform.fecha.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Fecha');
		document.entryform.fecha.focus();
		return(false);
	}
	else{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/m;
		if(!regexpression.test(document.entryform.fecha.value)){
			window.alert('[Fecha] no contiene un dato válido.');
			document.entryform.fecha.focus();
			return(false);
		}
	}
	if(document.entryform.id_tipo_movimiento.options[document.entryform.id_tipo_movimiento.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Tipo');
		document.entryform.id_tipo_movimiento.focus();
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
	if(document.entryform.horas.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.horas.value)){
			window.alert('[Horas] no contiene un dato válido.');
			document.entryform.horas.focus();
			return(false);
		}
	}
	if(document.entryform.prof_uno.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.prof_uno.value)){
			window.alert('[Profundidad Uno] no contiene un dato válido.');
			document.entryform.prof_uno.focus();
			return(false);
		}
	}
	if(document.entryform.prof_dos.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.prof_dos.value)){
			window.alert('[Profundidad Dos] no contiene un dato válido.');
			document.entryform.prof_dos.focus();
			return(false);
		}
	}
	if(document.entryform.prof_tres.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.prof_tres.value)){
			window.alert('[Profundidad Tres] no contiene un dato válido.');
			document.entryform.prof_tres.focus();
			return(false);
		}
	}
	if(document.entryform.costo.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.costo.value)){
			window.alert('[Costo] no contiene un dato válido.');
			document.entryform.costo.focus();
			return(false);
		}
	}

	if(document.entryform.id_tipo_movimiento.options[document.entryform.id_tipo_movimiento.selectedIndex].value=='1')
	{
		/*No, si es reencauche no debe estar montada.  Antes lo contrario...
		if(document.entryform.id_vehiculo.options[document.entryform.id_vehiculo.selectedIndex].value=='%' || document.entryform.posicion.options[document.entryform.posicion.selectedIndex].value=='%' || document.entryform.costo.value.replace(/ /g, '') =='' || document.entryform.id_subtipo_movimiento.options[document.entryform.id_subtipo_movimiento.selectedIndex].value=='%')
		{
			window.alert('Si el estado es reencauche los campos de Vehículo, posición, costo, y subtipo deben diligenciarse.');
			return(false);
		}
		*/
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

var oXmlHttp_id_subtipo_movimiento;
function updateRecursive_id_subtipo_movimiento(select){
	namediv='id_subtipo_movimiento';
	nameId='id_subtipo_movimiento';
	id=select.options[select.selectedIndex].value;
	width=document.getElementById(nameId).style.width;
	consulta='SELECT id, p.subtipo FROM llta.subtipos_movimientos WHERE id_tipo_movimiento=\'' + id + '\'';
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '" style="width:' + document.getElementById(nameId).style.width + '"><option>Actualizando...<\/select>';
	var consulta;
	query=escape(consulta);
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateRecursive.php?module=llta.movimientos&field=id_subtipo_movimiento&id=" + id + "&divid=" + namediv + "&width=" + width;
	oXmlHttp_id_subtipo_movimiento=GetHttpObject(cambiarRecursive_id_subtipo_movimiento);
	oXmlHttp_id_subtipo_movimiento.open("GET", url , true);
	oXmlHttp_id_subtipo_movimiento.send(null);
}
function cambiarRecursive_id_subtipo_movimiento(){
	if (oXmlHttp_id_subtipo_movimiento.readyState==4 || oXmlHttp_id_subtipo_movimiento.readyState=="complete"){
		document.getElementById('id_subtipo_movimiento').innerHTML=oXmlHttp_id_subtipo_movimiento.responseText
	}
}

var oXmlHttp_posicion;
function updateRecursive_posicion(select){
	namediv='posicion';
	nameId='posicion';
	id=select.options[select.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateMtto.php?tipo=actualizarEjes&id_vehiculo=" + id + "&divid=" + namediv;
	oXmlHttp_posicion=GetHttpObject(cambiarRecursive_posicion);
	oXmlHttp_posicion.open("GET", url , true);
	oXmlHttp_posicion.send(null);
}
function cambiarRecursive_posicion(){
	if (oXmlHttp_posicion.readyState==4 || oXmlHttp_posicion.readyState=="complete"){
		document.getElementById('posicion').innerHTML=oXmlHttp_posicion.responseText
	}
}













</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>

