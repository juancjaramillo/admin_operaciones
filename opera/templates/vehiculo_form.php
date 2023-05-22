<form name="entryform" action="<?=$ME?>" method="POST"  class="form" enctype="multipart/form-data" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id" value="<?=nvl($mov["id"])?>">
<input type="hidden" name="esquema" value="<?=nvl($mov["esquema"])?>">

<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong>EDITAR VEHÍCULO Y No. ORDEN</strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td align='right'>Vehículo</td>
								<td align='left'><select  name="id_vehiculo" id="id_vehiculo" ><?=$vehiculos?></select></td>
							</tr>
							<tr>
								<td align='right'>No. Orden</td>
								<td align='left'>
									<input type='text' size='12' class="casillatext" name='numero_orden' value='<?=nvl($mov["numero_orden"])?>'>
								</td>
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
	if(document.entryform.id_vehiculo.options[document.entryform.id_vehiculo.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Vehículo');
		document.entryform.id_vehiculo.focus();
		return(false);
	}

	return(true);
}

</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>

