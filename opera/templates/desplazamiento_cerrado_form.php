<form name="entryform" action="<?=$ME?>" method="POST"  class="form" enctype="multipart/form-data" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id" value="<?=$des["id"]?>">

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
							<tr>
								<td align='right'>Tipo</td>
								<td align='left'>
									<select name="id_tipo_desplazamiento"><?=$tipos?></select>
								</td>
							</tr>
							<tr>
								<td align='right'>Inicio</td>
								<td align='left'>
									<input size="20" id="f_hora_inicio" class="casillatext_fecha" name='hora_inicio' value='<?=$des["hora_inicio"]?>' /><button id="b_hora_inicio" onclick="javascript:showCalendarHora('f_hora_inicio','b_hora_inicio')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>Final</td>
								<td align='left'>
									<input size="20" id="f_hora_fin" class="casillatext_fecha" name='hora_fin' value='<?=$des["hora_fin"]?>' /><button id="b_hora_fin" onclick="javascript:showCalendarHora('f_hora_fin','b_hora_fin')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>Num. Viaje</td>
								<td align='left'>
									<input type='text' size='5' class="casillatext" name='numero_viaje' value='<?=$des["numero_viaje"]?>'>
								</td>
							</tr>	
							<tr>
								<td align='right'>Km</td>
								<td align='left'>
									<input type='text' size='20' class="casillatext" name='km' value='<?=$des["km"]?>'>
								</td>
							</tr>
							<tr>
								<td align='right'>Horómetro</td>
								<td align='left'>
									<input type='text' size='20' class="casillatext" name='horometro' value='<?=$des["horometro"]?>'>
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
	if(document.entryform.id_tipo_desplazamiento.options[document.entryform.id_tipo_desplazamiento.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Tipo');
		document.entryform.id_tipo_desplazamiento.focus();
		return(false);
	}

	if(document.entryform.hora_inicio.value.replace(/ /g, '') ==''){
	window.alert('Por favor escriba: Inicio');
	document.entryform.hora_inicio.focus();
	return(false);
	}
	else{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.hora_inicio.value)){
			window.alert('[Inicio] no contiene un dato válido.');
			document.entryform.hora_inicio.focus();
			return(false);
		}
	}

	if(document.entryform.hora_fin.value.replace(/ /g, '') ==''){
	window.alert('Por favor escriba: Fin');
	document.entryform.hora_fin.focus();
	return(false);
	}
	else{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.hora_fin.value)){
			window.alert('[Fin] no contiene un dato válido.');
			document.entryform.hora_fin.focus();
			return(false);
		}
	}

	if(document.entryform.hora_inicio.value > document.entryform.hora_fin.value)
	{
		window.alert('[Fin] no puede ser menor que la [Inicio] .');
		document.entryform.hora_fin.focus();
		return(false);
	}

	if(document.entryform.km.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Km');
		document.entryform.km.focus();
		return(false);
	}
	else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.km.value)){
			window.alert('[Km] no contiene un dato válido.');
			document.entryform.km.focus();
			return(false);
		}
	}

	if(document.entryform.horometro.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Horómetro');
		document.entryform.horometro.focus();
		return(false);
	}
	else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.horometro.value)){
			window.alert('[Horómetro] no contiene un dato válido.');
			document.entryform.horometro.focus();
			return(false);
		}
	}


	if(document.entryform.numero_viaje.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Número viaje');
		document.entryform.numero_viaje.focus();
		return(false);
	}
	else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.numero_viaje.value)){
			window.alert('[Número viaje] no contiene un dato válido.');
			document.entryform.numero_viaje.focus();
			return(false);
		}
	}

	return(true);
}

</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>