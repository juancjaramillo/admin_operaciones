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
								<td align='right' width="30%">(*) Tipo de Bolsa</td>
								<td align='left'> <select  name='id_tipo_bolsa'><?=$tipos?> </select>
								</td>
							</tr>
							<tr>
								<td align='right' width="30%">(*) N�mero Inicio</td>
								<td align='left'><input type='text' size='12' class='casillatext' name='numero_inicio' value=''></td>
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
	if(document.entryform.id_tipo_bolsa.options[document.entryform.id_tipo_bolsa.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Bolsa');
		document.entryform.id_tipo_bolsa.focus();
		return(false);
	}
	if(document.entryform.numero_inicio.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Bolsas Inicio');
		document.entryform.numero_inicio.focus();
		return(false);
	}
	else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.numero_inicio.value)){
			window.alert('[Bolsas Inicio] no contiene un dato v�lido.');
			document.entryform.numero_inicio.focus();
			return(false);
		}
	}

	return(true);
}

</script>
<?
include($CFG->templatedir . "/resize_window.php");
?>
