<form name="entryform" action="<?=$ME?>" method="POST"  class="form" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id_frecuencia" value="<?=$id_frecuencia?>">
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
								<td align='right' width="30%">(*) Cargo</td>
								<td align='left'> <select  name='id_cargo' onChange="updateRecursive_id_persona(this)" ><?=$cargos?> </select>
								</td>
							</tr>
							<tr>
								<td align='right'>(*) Persona</td>
								<td align='left'><div id="id_persona"><select name='id_persona' id="id_persona" style="width:250px"><option value='%'>Seleccione...</option></select></div></td>
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
	if(document.entryform.id_cargo.options[document.entryform.id_cargo.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Cargo');
		document.entryform.id_cargo.focus();
		return(false);
	}
	if(document.entryform.id_persona.options[document.entryform.id_persona.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Persona');
		document.entryform.id_persona.focus();
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

var oXmlHttp_id_persona;
function updateRecursive_id_persona(select){
	namediv='id_persona';
	nameId='id_persona';
	id=select.options[select.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="/lib/ajaxUpdateOpera.php?tipo=listadoPersonaXCargoXFrecuencia&id_centro=<?=$esquema["id_centro"]?>&dia=<?=$esquema["dia"]?>&id_cargo=" + id;
	oXmlHttp_id_persona=GetHttpObject(cambiarRecursive_id_persona);
	oXmlHttp_id_persona.open("GET", url , true);
	oXmlHttp_id_persona.send(null);
}
function cambiarRecursive_id_persona(){
	if (oXmlHttp_id_persona.readyState==4 || oXmlHttp_id_persona.readyState=="complete"){
		document.getElementById('id_persona').innerHTML=oXmlHttp_id_persona.responseText
	}
}

</script>
<?
include($CFG->templatedir . "/resize_window.php");
?>
