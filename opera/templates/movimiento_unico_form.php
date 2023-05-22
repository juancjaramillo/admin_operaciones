<form name="entryform" action="<?=$ME?>" method="POST"  class="form" enctype="multipart/form-data" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id" value="<?=nvl($mov["id"])?>">
<input type="hidden" name="esquema" value="<?=nvl($mov["esquema"])?>">
<input type="hidden" name="id_micro" value="<?=nvl($mov["id_micro"])?>">

<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong>AGREGAR MOVIMIENTO</strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td align='right'>Fecha Inicio</td>
								<td align='left'>
									<input size="20" id="f_inicio" class="casillatext_fecha" name='inicio' value='<?=$mov["inicio"]." ".date("H:i:s")?>' /><button id="b_inicio" onclick="javascript:showCalendarHora('f_inicio','b_inicio')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
							</td>
							</tr>
							<tr>
								<td align='right'>Vehículo</td>
								<td align='left'><select  name="id_vehiculo" id="id_vehiculo" ><?=$vehiculos?></select></td>
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
	if(document.entryform.inicio.value.replace(/ /g, '')  == '')
	{
		window.alert('Por favor escriba : Fecha Inicio');
		document.entryform.inicio.focus();
		return false;
	}else{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.inicio.value)){
			window.alert('[Fecha Inicio] no contiene un dato válido.');
			document.entryform.inicio.focus();
			return(false);
		}
	}

	<?if($mov["esquema"] == "rec"){?>
	if(document.entryform.id_vehiculo.options[document.entryform.id_vehiculo.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Vehículo');
		document.entryform.id_vehiculo.focus();
		return(false);
	}
	<?}?>

	return(true);
}

</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>

