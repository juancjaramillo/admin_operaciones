<form name="entryform" action="<?=$ME?>" method="POST"  class="form" enctype="multipart/form-data" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id" value="<?=nvl($mov["id"])?>">
<input type="hidden" name="esquema" value="<?=$squema?>">

<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong>CERRAR MOVIMIENTO</strong></span></td>
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
									<input type='text' size='20' class="casillatext_fecha" name='inicio' value='<?=nvl($mov["inicio"])?>' readonly >
								</td>
							</tr>
							<tr>
								<td align='right'>Fecha Fin</td>
								<td align='left'>
									<input size="20" id="f_final" class="casillatext_fecha" name='final' value='<?=date("Y-m-d H:i:s")?>' /><button id="b_final" onclick="javascript:showCalendarHora('f_final','b_final')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>Km</td>
								<td align='left'>
									<input type='text' size='20' class="casillatext" name='kilometraje' value='<?=nvl($kmHoro["kilometraje"])?>'>
								</td>
							</tr>
							<tr>
								<td align='right'>Horómetro</td>
								<td align='left'>
									<input type='text' size='20' class="casillatext" name='horometro' value='<?=nvl($kmHoro["horometro"])?>'>
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
	if(document.entryform.final.value.replace(/ /g, '')  == '')
	{
		window.alert('Por favor escriba : Fecha Fin');
		document.entryform.final.focus();
		return false;
	}else{
		inicio = document.entryform.inicio.value;

		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.final.value)){
			window.alert('[Fecha Fin] no contiene un dato válido.');
			document.entryform.final.focus();
			return(false);
		}

		if(document.entryform.final.value < inicio)
		{
			window.alert('La fecha final no puede ser menor que la fecha de inicio');
			return(false);
		}
	}

	<?if($squema == "rec"){?>
	if(document.entryform.kilometraje.value.replace(/ /g, '')  == ''){
		window.alert('Por favor escriba : Km');
		document.entryform.kilometraje.focus();
		return false;
	}else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.kilometraje.value)){
			window.alert('[Km] no contiene un dato válido.');
			document.entryform.kilometraje.focus();
			return(false);
		}
	}

	if(document.entryform.horometro.value.replace(/ /g, '')  == ''){
		window.alert('Por favor escriba : Horómetro');
		document.entryform.horometro.focus();
		return false;
	}else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.horometro.value)){
			window.alert('[Horómetro] no contiene un dato válido.');
			document.entryform.horometro.focus();
			return(false);
		}
	}
	<?}?>

	return(true);
}

</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>

