<form name="entryform" action="<?=$ME?>" method="POST"  class="form" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id" value="<?=$item["id"]?>">
<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong><?=strtoupper($newMode)?> ÍTEM</strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td align='right'>Grupo</td>
								<td align='left'> <select  name='id_grupo'><option value="">Grupo</option><?=$grupos?></select> </td>
							</tr>
							<tr>
								<td align='right'>Orden</td>
								<td align='left'><input type='text' size='10' class='casillatext' name='orden' value='<?=nvl($item["orden"])?>'></td>
							</tr>
							<tr>
								<td align='right'>Texto</td>
								<td align='left'><textarea  rows='3' cols='40' name='texto'><?=nvl($item["texto"])?></textarea></td>
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
	</form>
</table>
<script type="text/javascript">

function revisar()
{
	if(document.entryform.id_grupo.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Grupo');
		document.entryform.id_grupo.focus();
		return(false);
	}
	else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.id_grupo.value)){
			window.alert('[Grupo] no contiene un dato válido.');
			document.entryform.id_grupo.focus();
			return(false);
		}
	}
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
	if(document.entryform.texto.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Texto');
		document.entryform.texto.focus();
		return(false);
	}
	else{
		var regexpression=/./;
		if(!regexpression.test(document.entryform.texto.value)){
			window.alert('[Texto] no contiene un dato válido.');
			document.entryform.texto.focus();
			return(false);
		}
	}
	return(true);
}
</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>

