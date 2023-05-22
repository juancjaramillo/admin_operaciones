<form name="entryform" action="<?=$ME?>" method="POST"  class="form" enctype="multipart/form-data" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id" value="<?=nvl($insp["id"])?>">
<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong><?=strtoupper($newMode)?> INSPECCIONES</strong></span></td>
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
									<input size="20" id="f_fecha" class="casillatext_fecha" name='fecha' value='<?=nvl($insp["fecha"],date("Y-m-d H:i:s"))?>' /><button id="b_fecha" onclick="javascript:showCalendarHora('f_fecha','b_fecha')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>(*) Vehículo</td>
								<td align='left'> <select  name='id_vehiculo' onChange="updateRecursive_id_reporto(this)"><option value="%">Seleccione...</option><?=$vehiculos?></select> </td>
							</tr>
							<tr>
								<td align='right'>(*) Reportó</td>
								<td align='left'>
									<div id="id_reporto"><select  name="id_reporto" id="id_reporto" style="width:250px"><option value="%">Seleccione...</option><?=nvl($reporto)?></select></div>
								</td>
							</tr>
							<tr>
								<td align='right'>Observaciones</td>
								<td align='left'><textarea  cols="50" name='observaciones'><?=nvl($insp["observaciones"])?></textarea></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<?if($newMode != "insertar"){?>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
				  <td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td width="10%" align="center">ORDEN</td>
								<td width="60%" align="center">ÍTEM</td>
								<td width="10%" align="center">HECHO</td>
								<td align="center">OBSERVACIONES</td>
							</tr>
							<?while($item = $db->sql_fetchrow($qid)){?>
							<tr>
								<td align="center"><?=$item["orden"]?></td>
								<td><?=$item["texto"]?></td>
								<td align="center"><input type="checkbox" name="insp_check_<?=$item["id"]?>" value="true" <?if($item["hecha"]=="t") echo "checked"?>></td>
								<td><textarea rows='2' name='insp_observa_<?=$item["id"]?>'><?=$item["observaciones"]?></textarea></td>
							</tr>
							<?}?>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<?}?>
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
	if(document.entryform.fecha.value.replace(/ /g, '') =='%'){
		window.alert('Por favor escriba: Fecha');
		document.entryform.fecha.focus();
		return(false);
	}
	else{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.fecha.value)){
			window.alert('[Fecha] no contiene un dato válido.');
			document.entryform.fecha.focus();
			return(false);
		}
	}
	if(document.entryform.id_vehiculo.options[document.entryform.id_vehiculo.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Vehiculo');
		document.entryform.id_vehiculo.focus();
		return(false);
	}
	if(document.entryform.id_reporto.options[document.entryform.id_reporto.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Reportó');
		document.entryform.id_reporto.focus();
		return(false);
	}
	if(document.entryform.observaciones.value !=''){
		var regexpression=/./;
		if(!regexpression.test(document.entryform.observaciones.value)){
			window.alert('[Observaciones] no contiene un dato válido.');
			document.entryform.observaciones.focus();
			return(false);
		}
	}
	return(true);
}

function eliminar()
{
	texto='¿Está seguro de querer borrar la inspección?';
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

var oXmlHttp_id_reporto;
function updateRecursive_id_reporto(select){
	namediv='id_reporto';
	nameId='id_reporto';
	id=select.options[select.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateMtto.php?tipo=reporto_inspecciones&id_vehiculo=" + id + "&divid=" + namediv;
	oXmlHttp_id_reporto=GetHttpObject(cambiarRecursive_id_reporto);
	oXmlHttp_id_reporto.open("GET", url , true);
	oXmlHttp_id_reporto.send(null);
}
function cambiarRecursive_id_reporto(){
	if (oXmlHttp_id_reporto.readyState==4 || oXmlHttp_id_reporto.readyState=="complete"){
		document.getElementById('id_reporto').innerHTML=oXmlHttp_id_reporto.responseText
	}
}




</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>

