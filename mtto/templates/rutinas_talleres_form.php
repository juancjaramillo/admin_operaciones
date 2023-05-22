<form name="entryform" action="<?=$ME?>" method="POST"  class="form" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id_rutina" value="<?=$taller["id_rutina"]?>">
<input type="hidden" name="id" value="<?=nvl($taller["id"])?>">
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
								<td align='right'>(*) Proveedor</td>
								<td align='left'><select  name='id_proveedor'><?=$proveedores?></select></td>
							</tr>
							<tr>
								<td align='right'>Costo</td>
								<td align='left'><input type='text' size='5'  name='costo' class='casillatext' value='<?=nvl($taller["costo"])?>'></td>
							</tr>
							<tr>
								<td align='right'>Tiempo (horas)</td>
								<td align='left'><input type='text' size='30'  name='tiempo' class='casillatext' value='<?=nvl($medicion["tiempo"])?>'></td>
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
	if(document.entryform.id_proveedor.options[document.entryform.id_proveedor.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Provedor');
		document.entryform.id_proveedor.focus();
		return(false);
	}

	if(document.entryform.costo.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.costo.value)){
			window.alert('[Costo] no contiene un dato válido.');
			document.entryform.costo.focus();
			return(false);
		}
	}

	if(document.entryform.tiempo.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.tiempo.value)){
			window.alert('[Tiempo (Horas)] no contiene un dato válido.');
			document.entryform.tiempo.focus();
			return(false);
		}
	}

	return(true);
}

</script>
<?
include($CFG->templatedir . "/resize_window.php");
?>
