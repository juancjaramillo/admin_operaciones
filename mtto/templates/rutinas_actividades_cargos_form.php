<form name="entryform" action="<?=$ME?>" method="POST"  class="form" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id_actividad" value="<?=$cargo["id_actividad"]?>">
<input type="hidden" name="id" value="<?=nvl($cargo["id"])?>">
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
								<td align='right'>(*) Cargo</td>
								<td align='left'><select  name='id_cargo'><?=$selectCargos?></select></td>
							</tr>
							<tr>
								<td align='right'>(*) Tiempo (minutos)</td>
								<td align='left'><input type='text' size='5'  name='tiempo' class='casillatext' value='<?=nvl($cargo["tiempo"])?>'></td>
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

	if(document.entryform.tiempo.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Tiempo (minutos)');
		document.entryform.tiempo.focus();
		return(false);
	}
	else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.tiempo.value)){
			window.alert('[Tiempo (minutos)] no contiene un dato válido.');
			document.entryform.tiempo.focus();
			return(false);
		}
	}

	return(true);
}

</script>
<?
?>
