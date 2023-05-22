<form name="entryform" action="<?=$ME?>" method="POST"  class="form" enctype="multipart/form-data" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id" value="<?=nvl($llanta["id"])?>">
<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong><?=strtoupper($newMode)?> LLANTAS</strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td align='right'>(*) Centro</td>
								<td align='left'> <select  name='id_centro' onChange="updateRecursive_id_proveedor(this), updateRecursive_id_vehiculo(this)"><?=$centros?></select> </td>
							</tr>
							<tr>
								<td align='right'>(*) Número</td>
								<td align='left'><input type='text' size='40'  name='numero' class='casillatext' value='<?=nvl($llanta["numero"])?>'></td>
							</tr>
							<tr>
								<td align='right'>(*) Estado</td>
								<td align='left'> <select  name="id_estado" style="width:250px" ><option value='%'>Seleccione...</option><?=nvl($estados)?></select></td>
							</tr>
							<tr>
								<td align='right'>(*) Marca</td>
								<td align='left'> <select  name='id_marca' onChange="updateRecursive_id_dimension(this)"><option value='%'>Seleccione...</option><?=$marcas?></select> </td>
							</tr>
							<tr>
								<td align='right'>(*) Dimensión</td>
								<td align='left'> <div id="id_dimension"><select  name="id_dimension" id="id_dimension" style="width:250px" ><option value='%'>Seleccione...</option><?=nvl($dimensiones)?></select></div></td>
							</tr>
							<tr>
								<td align='right'>(*) Diseño</td>
								<td align='left'><input type='text' size='40'  name='disenio' class='casillatext' value='<?=nvl($llanta["disenio"])?>'></td>
							</tr>
							<tr>
								<td align='right'>(*) Tipo</td>
								<td align='left'> <select  name="id_tipo_llanta" id="id_tipo_llanta" ><option value='%'>Seleccione...</option><?=nvl($tipos)?></select></td>
							</tr>
							<tr>
								<td align='right'>(*) Proveedor</td>
								<td align='left'> <div id="id_proveedor"><select  name="id_proveedor" id="id_proveedor" style="width:250px" ><option value="%">Seleccione...</option><?=nvl($proveedores)?></select></div></td>
							</tr>
							<tr>
								<td align='right'>(*) Fecha Compra</td>
								<td align='left'>
									<input type='text' size="10" id="f_fecha_compra" class="casillatext_fecha" name='fecha_compra' value='<?=nvl($llanta["fecha_compra"],date("Y-m-d"))?>' /><button id="b_fecha_compra" onclick="javascript:showCalendarSencillo('f_fecha_compra','b_fecha_compra')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align="right" nowrap="nowrap">(*) Valor de compra : </td>
								<td><input size="20" name="costo" type="text" class='casillatext' value='<? if(isset($llanta["costo"])) echo number_format($llanta["costo"],0,',','')?>'></td>
							</tr>
							<tr>
								<td align='right'>Dot</td>
								<td align='left'><input type='text' size='40'  name='dot' class='casillatext' value='<?=nvl($llanta["dot"])?>'></td>
							</tr>
							<tr>
								<td align='right'>Matricula No.</td>
								<td align='left'><input type='text' size='40'  name='matricula' class='casillatext' value='<?=nvl($llanta["matricula"])?>'></td>
							</tr>
							<?if($newMode == "insertar"){?>
							<tr>
								<td align='right'>Vehículo</td>
								<td align='left'><div id="id_vehiculo"><select  name="id_vehiculo" id="id_vehiculo" style="width:250px" onChange="updateRecursive_posicion(this);updateEstado();"><option value="%">Seleccione...</option></select></div></td>
							</tr>
							<tr>
								<td align='right'>Posición</td>
								<td align='left'> <div id="posicion"><select  name="posicion" id="posicion" style="width:250px"><option value="%">Seleccione...</option><?=nvl($posiciones)?></select></div></td>
							</tr>
							<?}else{?>
							<tr>
								<td align='right'>Vehículo</td>
								<td align='left'> <?=nvl($llanta["vehiculo"])?></td>
							</tr>
							<?}?>
							<tr>
								<td align='right'>Km</td>
								<td align='left'><input type='text' size='40'  name='km' class='casillatext' value='<?=nvl($llanta["km"])?>'> </td>
							</tr>
							<tr>
								<td align='right'>(*) Vida</td>
								<td align='left'> 
									<select  name="vida">
										<option value="%">Seleccione...</option>
										<option value="N" <?if(nvl($llanta["vida"])=="N") echo "selected";?>>Nueva (N)</option>
										<option value="1" <?if(nvl($llanta["vida"])=="1") echo "selected";?>>Primer reencauche (1)</option>
										<option value="2" <?if(nvl($llanta["vida"])=="2") echo "selected";?>>Segundo reencauche (2)</option>
										<option value="3" <?if(nvl($llanta["vida"])=="3") echo "selected";?>>Tercer reencauche (3)</option>
									</select>
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
			<?if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["agregarLlanta"])){?>
			<input type="submit" class="boton_verde" value="Aceptar" />
			<input type="button" class="boton_verde" value="Cancelar" onclick="window.close()"/>
			<?if($newMode != "insertar"){?>
			<input type="button" class="boton_rojo" value="Eliminar" onclick="eliminar()"/>
			<?}}?>
		</td>
	</tr>
	</form>
</table>
<script type="text/javascript">

function revisar()
{
	if(document.entryform.id_centro.options[document.entryform.id_centro.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Centro');
		document.entryform.id_centro.focus();
		return(false);
	}
	if(document.entryform.numero.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Número');
		document.entryform.numero.focus();
		return(false);
	}
	else{
		var regexpression=/^.{1,1055}$/m;
		if(!regexpression.test(document.entryform.numero.value)){
			window.alert('[Número] no contiene un dato válido.');
			document.entryform.numero.focus();
			return(false);
		}
	}
	if(document.entryform.id_marca.options[document.entryform.id_marca.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Marca');
		document.entryform.id_marca.focus();
		return(false);
	}
	if(document.entryform.id_dimension.options[document.entryform.id_dimension.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Dimensión');
		document.entryform.id_dimension.focus();
		return(false);
	}
	if(document.entryform.disenio.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Diseño');
		document.entryform.disenio.focus();
		return(false);
	}
	else{
		var regexpression=/^.{1,1055}$/m;
		if(!regexpression.test(document.entryform.disenio.value)){
			window.alert('[Diseño] no contiene un dato válido.');
			document.entryform.disenio.focus();
			return(false);
		}
	}
	if(document.entryform.id_tipo_llanta.options[document.entryform.id_tipo_llanta.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Tipo');
		document.entryform.id_tipo_llanta.focus();
		return(false);
	}
	if(document.entryform.id_proveedor.options[document.entryform.id_proveedor.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Proveedor');
		document.entryform.id_proveedor.focus();
		return(false);
	}
	if(document.entryform.fecha_compra.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Fecha compra');
		document.entryform.fecha_compra.focus();
		return(false);
	}
	else{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/m;
		if(!regexpression.test(document.entryform.fecha_compra.value)){
			window.alert('[Fecha compra] no contiene un dato válido.');
			document.entryform.fecha_compra.focus();
			return(false);
		}
	}
	if(document.entryform.costo.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Valor de compra');
		document.entryform.costo.focus();
		return(false);
	}

	if(document.entryform.dot.value !=''){
		var regexpression=/^.{1,255}$/m;
		if(!regexpression.test(document.entryform.dot.value)){
			window.alert('[Dot] no contiene un dato válido.');
			document.entryform.dot.focus();
			return(false);
		}
	}
	if(document.entryform.matricula.value !=''){
		var regexpression=/^.{1,255}$/m;
		if(!regexpression.test(document.entryform.matricula.value)){
			window.alert('[Matricula No.] no contiene un dato válido.');
			document.entryform.matricula.focus();
			return(false);
		}
	}

	if(document.entryform.km.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.km.value)){
			window.alert('[Km] no contiene un dato válido.');
			document.entryform.km.focus();
			return(false);
		}
	}

	if(document.entryform.vida.options[document.entryform.vida.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Vida');
		document.entryform.vida.focus();
		return(false);
	}
	if(document.entryform.id_estado.options[document.entryform.id_estado.selectedIndex].value=='1'){//Montada
		if(document.entryform.posicion.selectedIndex==0){
			window.alert('La llanta está en estado Montada.  Por favor indique la posición.');
			document.entryform.posicion.focus();
			return(false);
		}
	}
	return(true);

}

function updateEstado(){
	if(document.entryform.id_vehiculo.selectedIndex==0) document.entryform.id_estado.value=2;
	else document.entryform.id_estado.value=1;
}

function eliminar()
{
	texto='¿Está seguro de querer borrar la llanta?';
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

var oXmlHttp_id_proveedor;
function updateRecursive_id_proveedor(select){
	namediv='id_proveedor';
	nameId='id_proveedor';
	id=select.options[select.selectedIndex].value;
	width=document.getElementById(nameId).style.width;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '" style="width:' + document.getElementById(nameId).style.width + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateMtto.php?tipo=proveedoresxcentro&id_centro=" + id + "&divid=" + namediv;
	oXmlHttp_id_proveedor=GetHttpObject(cambiarRecursive_id_proveedor);
	oXmlHttp_id_proveedor.open("GET", url , true);
	oXmlHttp_id_proveedor.send(null);
}
function cambiarRecursive_id_proveedor(){
	if (oXmlHttp_id_proveedor.readyState==4 || oXmlHttp_id_proveedor.readyState=="complete"){
		document.getElementById('id_proveedor').innerHTML=oXmlHttp_id_proveedor.responseText
	}
}


var oXmlHttp_id_dimension;
function updateRecursive_id_dimension(select){
	namediv='id_dimension';
	nameId='id_dimension';
	id=select.options[select.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateMtto.php?tipo=actualizarDimensiones&id_marca=" + id + "&divid=" + namediv;
	oXmlHttp_id_dimension=GetHttpObject(cambiarRecursive_id_dimension);
	oXmlHttp_id_dimension.open("GET", url , true);
	oXmlHttp_id_dimension.send(null);
}
function cambiarRecursive_id_dimension(){
	if (oXmlHttp_id_dimension.readyState==4 || oXmlHttp_id_dimension.readyState=="complete"){
		document.getElementById('id_dimension').innerHTML=oXmlHttp_id_dimension.responseText
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


var oXmlHttp_id_vehiculo;
function updateRecursive_id_vehiculo(select){
	namediv='id_vehiculo';
	nameId='id_vehiculo';
	id=select.options[select.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateMtto.php?tipo=vehiculosxcentro&id_centro=" + id + "&divid=" + namediv;
	oXmlHttp_id_vehiculo=GetHttpObject(cambiarRecursive_id_vehiculo);
	oXmlHttp_id_vehiculo.open("GET", url , true);
	oXmlHttp_id_vehiculo.send(null);
}
function cambiarRecursive_id_vehiculo(){
	if (oXmlHttp_id_vehiculo.readyState==4 || oXmlHttp_id_vehiculo.readyState=="complete"){
		document.getElementById('id_vehiculo').innerHTML=oXmlHttp_id_vehiculo.responseText
	}
}










</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>

