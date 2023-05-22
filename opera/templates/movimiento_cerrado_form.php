<form name="entryform" action="<?=$ME?>" method="POST"  class="form" enctype="multipart/form-data" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id" value="<?=$mov["id"]?>">

<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong><?=$titulo?></strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">

							<?if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["cambiar_ruta_movimiento"])){?>
							<tr>
								<td align='right'>Ruta</td>
								<td align='left'>
									<select name="id_micro"><?=$rutas?></select>
								</td>
							</tr>
							<?}?>
							<tr>
								<td align='right'>Vehículo</td>
								<td align='left'>
									<select name="id_vehiculo"><?=$vehiculos?></select>
								</td>
							</tr>
							<tr>
								<td align='right'>Inicio</td>
								<td align='left'>
									<input size="20" id="f_inicio" class="casillatext_fecha" name='inicio' value='<?=$mov["inicio"]?>' /><button id="b_inicio" onclick="javascript:showCalendarHora('f_inicio','b_inicio')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>Final</td>
								<td align='left'>
										<input size="20" id="f_final" class="casillatext_fecha" name='final' value='<?=$mov["final"]?>' /><button id="b_final" onclick="javascript:showCalendarHora('f_final','b_final')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>Km Final</td>
								<td align='left'>
									<input type='text' size='20' class="casillatext" name='km_final' value='<?=$mov["km_final"]?>'>
								</td>
							</tr>
							<tr>
								<td align='right'>Horómetro Final</td>
								<td align='left'>
									<input type='text' size='20' class="casillatext" name='horometro_final' value='<?=$mov["horometro_final"]?>'>
								</td>
							</tr>
							<tr>
								<td align='right'>No. Orden</td>
								<td align='left'>
									<input type='text' size='12' class="casillatext" name='numero_orden' value='<?=$mov["numero_orden"]?>'>
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
			<input type="submit" class="boton_verde" value="Guardar" />
			<input type="button" class="boton_verde" value="Cancelar" onclick="window.close()"/>
		</td>
	</tr>
	<tr>
		<td>Nota:  no se actualizará el km ni el horómetro del vehículo.</td>
	</tr>
	</form>
</table>
<script type="text/javascript">

function revisar()
{
	<?if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["cambiar_ruta_movimiento"])){?>
	if(document.entryform.id_micro.options[document.entryform.id_micro.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Ruta');
		document.entryform.id_micro.focus();
		return(false);
	}
	<?}?>

	if(document.entryform.id_vehiculo.options[document.entryform.id_vehiculo.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Vehículo');
		document.entryform.id_vehiculo.focus();
		return(false);
	}

	if(document.entryform.inicio.value.replace(/ /g, '') ==''){
	window.alert('Por favor escriba: Fecha Inicio');
	document.entryform.inicio.focus();
	return(false);
	}
	else{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.inicio.value)){
			window.alert('[Fecha Inicio] no contiene un dato válido.');
			document.entryform.inicio.focus();
			return(false);
		}
	}

	if(document.entryform.final.value.replace(/ /g, '') ==''){
	window.alert('Por favor escriba: Fecha Final');
	document.entryform.final.focus();
	return(false);
	}
	else{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.final.value)){
			window.alert('[Fecha Final] no contiene un dato válido.');
			document.entryform.final.focus();
			return(false);
		}
	}

	if(document.entryform.inicio.value > document.entryform.final.value)
	{
		window.alert('[Fecha Final] no puede ser menor que la [Fecha Inicio] .');
		document.entryform.final.focus();
		return(false);
	}

	if(document.entryform.km_final.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Km Final');
		document.entryform.km_final.focus();
		return(false);
	}
	else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.km_final.value)){
			window.alert('[Km Final] no contiene un dato válido.');
			document.entryform.km_final.focus();
			return(false);
		}
	}

	if(document.entryform.horometro_final.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Horómetro Final');
		document.entryform.horometro_final.focus();
		return(false);
	}
	else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.horometro_final.value)){
			window.alert('[Horómetro Final] no contiene un dato válido.');
			document.entryform.horometro_final.focus();
			return(false);
		}
	}

	if(document.entryform.numero_orden.value !=''){
		var regexpression=/^.{1,16}$/m;
		if(!regexpression.test(document.entryform.numero_orden.value)){
			window.alert('[Orden No.] no contiene un dato válido.');
			document.entryform.numero_orden.focus();
			return(false);
		}
	}

	return(true);
}

</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>