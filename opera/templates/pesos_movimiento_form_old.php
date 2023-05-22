<form name="entryform" action="<?=$ME?>" method="POST"  class="form" enctype="multipart/form-data" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id" value="<?=nvl($mov["id"])?>">

<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong><?=$titulo?> PESOS</strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td align='right'>Peso Inicial</td>
								<td align='left'> <input type='text' size='20' class="casillatext" name='peso_inicial' value='<?=$mov["peso_inicial"]?>'> </td>
							</tr>
							<tr>
								<td align='right'>Peso Final</td>
								<td align='left'> <input type='text' size='20' class="casillatext" name='peso_final' value='<?=$mov["peso_final"]?>'> </td>
							</tr>
							<tr>
								<td align='right'>Lugar Descargue</td>
								<td align='left'><select  name="id_lugar_descargue"><?=$descargue?></select></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=3 align="center">
			<?if($mov["final"] == ""){?>
			<input type="submit" class="boton_verde" value="Aceptar" />
			<?}?>
			<input type="button" class="boton_verde" value="Cancelar" onclick="window.close()"/>
		</td>
	</tr>
	</form>
</table>
<script type="text/javascript">

function revisar()
{
	if(document.entryform.peso_inicial.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.peso_inicial.value)){
			window.alert('[Peso Inicial] no contiene un dato válido.');
			document.entryform.peso_inicial.focus();
			return(false);
		}
	}
	if(document.entryform.peso_final.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.peso_final.value)){
			window.alert('[Peso Final] no contiene un dato válido.');
			document.entryform.peso_final.focus();
			return(false);
		}
	}

	return(true);
}

</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>

