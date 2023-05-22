<form name="entryform" action="<?=$ME?>" method="POST"  class="form" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id_rutina" value="<?=$medicion["id_rutina"]?>">
<input type="hidden" name="id" value="<?=nvl($medicion["id"])?>">
<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong><?=strtoupper(str_replace("_"," ",$newMode))?></strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td align='right'>(*) Orden</td>
								<td align='left'><input type='text' size='5'  name='orden' class='casillatext' value='<?=nvl($medicion["orden"])?>'></td>
							</tr>
							<tr>
								<td align='right'>(*) Nombre</td>
								<td align='left'><input type='text' size='30'  name='nombre' class='casillatext' value='<?=nvl($medicion["nombre"])?>'></td>
							</tr>
							<tr>
								<td align='right'>(*) Unidad</td>
								<td align='left'><div id="id_unidad"><select  name='id_unidad' id="id_unidad"><?=$unidades?></select>&nbsp;&nbsp;<a href="javascript:abrirVentanaJavaScript('unidades','500','200','<?=$CFG->wwwroot?>/mtto/rutinas.php?mode=agregar_unidad')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-add.gif' border='0'></a></div></td>
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
		</td>
	</tr>
</table>
</form>
<script type="text/javascript">

function revisar()
{
	if(document.entryform.orden.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Orden');
		document.entryform.orden.focus();
		return(false);
	}
	else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.orden.value)){
			window.alert('[Orden] no contiene un dato válido.');
			document.entryform.orden.focus();
			return(false);
		}
	}

	if(document.entryform.nombre.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Nombre');
		document.entryform.nombre.focus();
		return(false);
	}
	else{
		var regexpression=/^.{1,255}$/m;
		if(!regexpression.test(document.entryform.nombre.value)){
			window.alert('[Nombre] no contiene un dato válido.');
			document.entryform.nombre.focus();
			return(false);
		}
	}
	if(document.entryform.id_unidad.options[document.entryform.id_unidad.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Unidad');
		document.entryform.id_unidad.focus();
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

var oXmlHttp_id_unidad;
function updateRecursive_id_unidad(){
	namediv='id_unidad';
	nameId='id_unidad';
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateMtto.php?tipo=listar_unidades&divid=" + namediv;
	oXmlHttp_id_unidad=GetHttpObject(cambiarRecursive_id_unidad);
	oXmlHttp_id_unidad.open("GET", url , true);
	oXmlHttp_id_unidad.send(null);
}
function cambiarRecursive_id_unidad(){
	if (oXmlHttp_id_unidad.readyState==4 || oXmlHttp_id_unidad.readyState=="complete"){
		document.getElementById('id_unidad').innerHTML=oXmlHttp_id_unidad.responseText
	}
}

function cargarValoresUnidades()
{
	updateRecursive_id_unidad();	
}

</script>
<?
include($CFG->templatedir . "/resize_window.php");
?>
