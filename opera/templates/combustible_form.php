<form name="entryform" action="<?=$ME?>" method="POST"  class="form"  onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id" value="<?=nvl($mov["id"])?>">

<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong><?=$titulo?> COMBUSTIBLE</strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td align='right'>Combustible (gal)</td>
								<td align='left'> <input type='text' size='20' class="casillatext" name='combustible' value='<?=$mov["combustible"]?>'> </td>
							</tr>
							<tr>
								<td align='right'>Km tanqueo</td>
								<td align='left'> <input type='text' size='20' class="casillatext" name='km_tanqueo' value='<?=$mov["km_tanqueo"]?>'> </td>
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
	if(document.entryform.combustible.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.combustible.value)){
			window.alert('[Combustible] no contiene un dato v�lido.');
			document.entryform.combustible.focus();
			return(false);
		}

		if(document.entryform.combustible.value > <?=$mov["mx_comb"]?>){
			window.alert('[Combustible] no puede ser mayor de <?=$mov["mx_comb"]?> gal.');
			document.entryform.combustible.focus();
			return(false);
		}

	}

	if(document.entryform.km_tanqueo.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.km_tanqueo.value)){
			window.alert('[Km tanqueo] no contiene un dato v�lido.');
			document.entryform.km_tanqueo.focus();
			return(false);
		}
	}
	
	return(true);
}

</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>

