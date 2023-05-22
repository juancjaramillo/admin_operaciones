<form name="entryform" action="<?=$ME?>" method="POST"  class="form" enctype="multipart/form-data" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id_movimiento" value="<?=$frm["id_movimiento"]?>">

<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong>AGREGAR PEAJE</strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td align='right'>Peaje</td>
								<td align='left'> <select name='id_peaje' ><?=$peajes?></select></td>
							</tr>
							<tr>
								<td align='right'>Veces</td>
								<td align='left'> <input type='text' size='5' class="casillatext" name='veces' value=''> </td>
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
	if(document.entryform.id_peaje.options[document.entryform.id_peaje.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Peaje');
		document.entryform.id_peaje.focus();
		return(false);
	}
	if(document.entryform.veces.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Veces');
		document.entryform.veces.focus();
		return(false);
	}
	else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.veces.value)){
			window.alert('[Veces] no contiene un dato válido.');
			document.entryform.veces.focus();
			return(false);
		}
	}

	return(true);
}

</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>

