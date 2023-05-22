<form name="entryform" action="<?=$ME?>" method="POST"  class="form" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
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
								<td align='right'>(*) Unidad</td>
								<td align='left'><input type='text' size='30'  name='unidad' class='casillatext' value=''></td>
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
	if(document.entryform.unidad.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Unidad');
		document.entryform.unidad.focus();
		return(false);
	}
	else{
		var regexpression=/^.{1,255}$/m;
		if(!regexpression.test(document.entryform.unidad.value)){
			window.alert('[Unidad] no contiene un dato válido.');
			document.entryform.unidad.focus();
			return(false);
		}
	}

	return(true);
}

</script>
<?
include($CFG->templatedir . "/resize_window.php");
?>
