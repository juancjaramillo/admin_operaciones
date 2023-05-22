<form name="entryform" action="<?=$ME?>" method="POST"  class="form" enctype="multipart/form-data" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id" value="<?=nvl($micro["id"])?>">

<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong><?=strtoupper($newMode)?> RUTA</strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td align='right'>(*) Código</td>
								<td align='left'>
									<input type='text' size='12' class="casillatext" name='codigo' value='<?=nvl($micro["codigo"])?>'>
								</td>
							</tr>
							<tr>
								<td align='right'>(*) Macro</td>
								<td align='left'>
									<input type='text' size='12' class="casillatext" name='macro' value='<?=nvl($micro["macro"])?>'>
								</td>
							</tr>
							<tr>
								<td align='right'>(*) Ase</td>
								<td align='left'><select  name="id_ase" onChange="updateRecursive_id_vehiculo(), updateRecursive_id_cuartelillo(), updateRecursive_id_coordinador(), updateRecursive_id_lugar_descargue()"><?=$ases?></select></td>
							</tr>
							<tr>
								<td align='right'>(*) Tipo Residuo</td>
								<td align='left'><select  name="id_tipo_residuo" ><?=$tipos_residuos?></select></td>
							</tr>
							<tr>
								<td align='right'>(*) Servicio</td>
								<td align='left'><select  name="id_servicio" onChange="updateRecursive_id_vehiculo()" ><?=$servicios?></select></td>
							</tr>
							<tr>
								<td align='right'>Km</td>
								<td align='left'>
									<input type='text' size='12' class="casillatext" name='km' value='<?=nvl($micro["km"])?>'>
								</td>
							</tr>
							<tr>
								<td align='right'>(*)  % suelo rural</td>
								<td align='left'>
									<input type='text' size='12' class="casillatext" name='porc_rural' value='<?=nvl($micro["porc_rural"],0)?>'>
								</td>
							</tr>
							<tr>
								<td align='right'>(*)  % suelo urbano</td>
								<td align='left'>
									<input type='text' size='12' class="casillatext" name='porc_urbano' value='<?=nvl($micro["porc_urbano"],100)?>'>
								</td>
							</tr>
<!--							<tr>
								<td align='right'>Cuartelillo</td>
								<td align='left'><div id="id_cuartelillo"><select  name="id_cuartelillo" id="id_cuartelillo" style="width:250px" ><?=nvl($cuartelillos)?></select></div></td>
							</tr>
-->							<tr>
								<td align='right'>Vehículo</td>
								<td align='left'><div id="id_vehiculo"><select  name="id_vehiculo" id="id_vehiculo" style="width:250px"><?=nvl($vehiculos)?></select></div></td>
							</tr>
<!--							<tr>
								<td align='right'>Supervisor</td>
								<td align='left'><div id="id_coordinador"><select  name="id_coordinador" id="id_coordinador" style="width:250px" ><?=nvl($coordinador)?></select></div></td>
							</tr>
-->							<tr>
								<td align='right'>(*) Vigencia desde</td>
								<td align='left'>
									<input type='text' size="10" id="f_fecha_desde" class="casillatext_fecha" name='fecha_desde' value='<?=nvl($micro["fecha_desde"])?>'  readonly /><button id="b_fecha_desde" onclick="javascript:showCalendarSencillo('f_fecha_desde','b_fecha_desde')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>Vigencia Hasta</td>
								<td align='left'>
									<input type='text' size="10" id="f_fecha_hasta" class="casillatext_fecha" name='fecha_hasta' value='<?=nvl($micro["fecha_hasta"])?>'  readonly /><button id="b_fecha_hasta" onclick="javascript:showCalendarSencillo('f_fecha_hasta','b_fecha_hasta')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>Lugar Descargue</td>
								<td align='left'><div id="id_lugar_descargue"><select  name="id_lugar_descargue" id="id_lugar_descargue" style="width:250px" ><?=$descargue?></select></div></td>
							</tr>
							<tr>
								<td align='right'>Compactadas</td>
								<td align='left'>
									<input type='text' size='12' class="casillatext" name='compactadas' value='<?=nvl($micro["compactadas"])?>'>
								</td>
							</tr>
							<tr>
								<td align='right'>Geometry</td>
								<td align='left'> <input type='text' size='20' READONLY name='geometry' value=''  ></td>	
							</tr>
							<tr>
								<td align='right'>Recolección Selectiva (SUI) </td>
								<td bgcolor="#ffffff">
									<select  name='selectiva'  >
										<option value="%">Seleccione...
										<option value="SÍ" <?if($micro["selectiva"] == "SÍ") echo "selected";?>>SÍ
										<option value="NO" <?if($micro["selectiva"] == "NO") echo "selected";?>>NO
									</select>
								</td>
							</tr>
							<tr>
								<td align='right'>Código SUI</td>
								<td align='left'>
									<input type='text' size='12' class="casillatext" name='codigo_sui' value='<?=nvl($micro["codigo_sui"])?>'>
								</td>
							</tr>
							<tr>
								<td align='right'>Comuna/Localidad</td>
								<td align='left'>
									<input type='text' size='12' class="casillatext" name='comuna' value='<?=nvl($micro["comuna"])?>'>
								</td>
							</tr>
							<tr>
								<td align='right'>Codigo Relleno</td>
								<td align='left'>
									<input type='text' size='12' class="casillatext" name='cod_relleno' value='<?=nvl($micro["cod_relleno"])?>'>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=3 align="center">
			<input type="submit" class="boton_verde" value="Aceptar" />
			<input type="button" class="boton_verde" value="Cancelar" onclick="window.close()"/>
			<?if($newMode != "insertar"){?>
			<input type="button" class="boton_rojo" value="Eliminar" onclick="eliminar()"/>
			<?}?>
		</td>
	</tr>
	</form>
</table>
<script type="text/javascript">

function revisar()
{
	if(document.entryform.codigo.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Código');
		document.entryform.codigo.focus();
		return(false);
	}
	else{
		var regexpression=/^.{1,1055}$/m;
		if(!regexpression.test(document.entryform.codigo.value)){
			window.alert('[Código] no contiene un dato válido.');
			document.entryform.codigo.focus();
			return(false);
		}
	}
	if(document.entryform.macro.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Macro');
		document.entryform.macro.focus();
		return(false);
	}
	else{
		var regexpression=/^.{1,1055}$/m;
		if(!regexpression.test(document.entryform.macro.value)){
			window.alert('[Macro] no contiene un dato válido.');
			document.entryform.macro.focus();
			return(false);
		}
	}
	if(document.entryform.id_ase.options[document.entryform.id_ase.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Ase');
		document.entryform.id_ase.focus();
		return(false);
	}
	if(document.entryform.id_tipo_residuo.options[document.entryform.id_tipo_residuo.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Tipo Residuo');
		document.entryform.id_tipo_residuo.focus();
		return(false);
	}
	if(document.entryform.id_servicio.options[document.entryform.id_servicio.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Servicio');
		document.entryform.id_servicio.focus();
		return(false);
	}
	if(document.entryform.km.value !=''){
		var regexpression=/^.{1,1055}$/m;
		if(!regexpression.test(document.entryform.km.value)){
			window.alert('[Km] no contiene un dato válido.');
			document.entryform.km.focus();
			return(false);
		}
	}
	if(document.entryform.porc_rural.value.replace(/ /g, '') ==''){
	window.alert('Por favor escriba: % suelo rural');
	document.entryform.porc_rural.focus();
	return(false);
	}
	else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.porc_rural.value)){
			window.alert('[% suelo rural] no contiene un dato válido.');
			document.entryform.porc_rural.focus();
			return(false);
		}
	}
	if(document.entryform.porc_urbano.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: % suelo urbano');
		document.entryform.porc_urbano.focus();
		return(false);
	}
	else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.porc_urbano.value)){
			window.alert('[% suelo urbano] no contiene un dato válido.');
			document.entryform.porc_urbano.focus();
			return(false);
		}
	}

	suma = parseFloat(document.entryform.porc_rural.value) + parseFloat(document.entryform.porc_urbano.value);
	if(suma != 100){
		window.alert('La suma de los suelos no da el 100%');
		document.entryform.porc_urbano.focus();
		return(false);
	}

	if(document.entryform.fecha_desde.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Vigencia desde');
		document.entryform.fecha_desde.focus();
		return(false);
	}
	else{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/m;
		if(!regexpression.test(document.entryform.fecha_desde.value)){
			window.alert('[Vigencia desde] no contiene un dato válido.');
			document.entryform.fecha_desde.focus();
			return(false);
		}
	}
	if(document.entryform.fecha_hasta.value !=''){
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/m;
		if(!regexpression.test(document.entryform.fecha_hasta.value)){
			window.alert('[Vigencia Hasta] no contiene un dato válido.');
			document.entryform.fecha_hasta.focus();
			return(false);
		}
	}

	if(document.entryform.selectiva.options[document.entryform.selectiva.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Recolección Selectiva (SUI)');
		document.entryform.selectiva.focus();
		return(false);
	}

	if(document.entryform.codigo_sui.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.codigo_sui.value)){
			window.alert('[Código SUI] no contiene un dato válido.');
			document.entryform.codigo_sui.focus();
			return(false);
		}
	}

	if(document.entryform.comuna.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.comuna.value)){
			window.alert('[Comuna] no contiene un dato válido.');
			document.entryform.comuna.focus();
			return(false);
		}
	}

	return(true);
}

function eliminar()
{
	texto='¿Está seguro de querer borrar la ruta?';
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

var oXmlHttp_id_equipo;
function updateRecursive_id_vehiculo(){
	namediv='id_vehiculo';
	nameId='id_vehiculo';
	id_ase = document.entryform.id_ase.options[document.entryform.id_ase.selectedIndex].value;
	id_servicio = document.entryform.id_servicio.options[document.entryform.id_servicio.selectedIndex].value;
	if(id_ase != '%' && id_servicio != '%')
	{
		document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
		var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateOpera.php?tipo=listadoVehiculosXAseyServicio&id_ase=" + id_ase + "&id_servicio="+ id_servicio + "&divid=" + namediv;
		oXmlHttp_id_vehiculo=GetHttpObject(cambiarRecursive_id_vehiculo);
		oXmlHttp_id_vehiculo.open("GET", url , true);
		oXmlHttp_id_vehiculo.send(null);
	}
}
function cambiarRecursive_id_vehiculo(){
	if (oXmlHttp_id_vehiculo.readyState==4 || oXmlHttp_id_vehiculo.readyState=="complete"){
		document.getElementById('id_vehiculo').innerHTML=oXmlHttp_id_vehiculo.responseText
	}
}

var oXmlHttp_id_cuartelillo;
function updateRecursive_id_cuartelillo(){
	namediv='id_cuartelillo';
	nameId='id_cuartelillo';
	id_ase = document.entryform.id_ase.options[document.entryform.id_ase.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateOpera.php?tipo=listadoCuartelilloXAse&id_ase=" + id_ase + "&divid=" + namediv;
	oXmlHttp_id_cuartelillo=GetHttpObject(cambiarRecursive_id_cuartelillo);
	oXmlHttp_id_cuartelillo.open("GET", url , true);
	oXmlHttp_id_cuartelillo.send(null);
}
function cambiarRecursive_id_cuartelillo(){
	if (oXmlHttp_id_cuartelillo.readyState==4 || oXmlHttp_id_cuartelillo.readyState=="complete"){
		document.getElementById('id_cuartelillo').innerHTML=oXmlHttp_id_cuartelillo.responseText
	}
}

var oXmlHttp_id_coordinador;
function updateRecursive_id_coordinador(){
	namediv='id_coordinador';
	nameId='id_coordinador';
	id_ase = document.entryform.id_ase.options[document.entryform.id_ase.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateOpera.php?tipo=listadoCoordinadorXAse&id_ase=" + id_ase + "&divid=" + namediv;
	oXmlHttp_id_coordinador=GetHttpObject(cambiarRecursive_id_coordinador);
	oXmlHttp_id_coordinador.open("GET", url , true);
	oXmlHttp_id_coordinador.send(null);
}
function cambiarRecursive_id_coordinador(){
	if (oXmlHttp_id_coordinador.readyState==4 || oXmlHttp_id_coordinador.readyState=="complete"){
		document.getElementById('id_coordinador').innerHTML=oXmlHttp_id_coordinador.responseText
	}
}

var oXmlHttp_id_lugar_descargue;
function updateRecursive_id_lugar_descargue(){
	namediv='id_lugar_descargue';
	nameId='id_lugar_descargue';
	id_ase = document.entryform.id_ase.options[document.entryform.id_ase.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateOpera.php?tipo=listadoLugaresDescargueXAse&id_ase=" + id_ase + "&divid=" + namediv;
	oXmlHttp_id_lugar_descargue=GetHttpObject(cambiarRecursive_id_lugar_descargue);
	oXmlHttp_id_lugar_descargue.open("GET", url , true);
	oXmlHttp_id_lugar_descargue.send(null);
}
function cambiarRecursive_id_lugar_descargue(){
	if (oXmlHttp_id_lugar_descargue.readyState==4 || oXmlHttp_id_lugar_descargue.readyState=="complete"){
		document.getElementById('id_lugar_descargue').innerHTML=oXmlHttp_id_lugar_descargue.responseText
	}
}







</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>

