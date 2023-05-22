<form name="entryform" action="<?=$ME?>" method="POST"  class="form" enctype="multipart/form-data" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type='hidden' name='id_movimiento' value='<?=$frm["id_movimiento"]?>'>
<input type='hidden' id='id_cliente' name='id_cliente' value=''>

<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong>MOVIMIENTO <?=$mov["codigo"]." / ".$mov["inicio"]?></strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td align='right'>Cliente (código/dirección):</td>
								<td align='left'><select name = "id_cliente"><?=$clientes?></select></td>

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
	if(document.entryform.id_cliente.options[document.entryform.id_cliente.selectedIndex].value=='%'){
		window.alert('Por favor escoja: Cliente');
		document.entryform.id_cliente.focus();
		return(false);
	}

	return(true);
}

</script>

<?include($CFG->templatedir . "/resize_window.php");?>