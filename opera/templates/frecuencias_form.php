<form name="entryform" action="<?=$ME?>" method="POST"  class="form" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id_micro" value="<?=$frecuencia["id_micro"]?>">
<input type="hidden" name="id" value="<?=nvl($frecuencia["id"])?>">
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
								<td align='right'>(*) Día</td>
								<td align='left'>
									<select  name='dia'  >
										<option value="%">Seleccione...
										<option value="1" <?if(nvl($frecuencia["dia"])==1) echo "selected"?>>Lunes
										<option value="2" <?if(nvl($frecuencia["dia"])==2) echo "selected"?>>Martes
										<option value="3" <?if(nvl($frecuencia["dia"])==3) echo "selected"?>>Miércoles
										<option value="4" <?if(nvl($frecuencia["dia"])==4) echo "selected"?>>Jueves
										<option value="5" <?if(nvl($frecuencia["dia"])==5) echo "selected"?>>Viernes
										<option value="6" <?if(nvl($frecuencia["dia"])==6) echo "selected"?>>Sábado
										<option value="7" <?if(nvl($frecuencia["dia"])==7) echo "selected"?>>Domingo
									</select>
								</td>
							</tr>
							<tr>
								<td align='right'>(*) Turno</td>
								<td align='left'><select  name='id_turno'><?=$turnos?></select></td>
							</tr>
							<tr>
								<td align='right'>Producción</td>
								<td align='left'><input type='text' size='5'  name='produccion' class='casillatext' value='<?=nvl($frecuencia["produccion"])?>'></td>
							</tr>
							<tr>
								<td align='right'>(*) Viajes</td>
								<td align='left'><input type='text' size='5'  name='viajes' class='casillatext' value='<?=nvl($frecuencia["viajes"])?>'></td>
							</tr>
							<tr>
								<td align='right'>(*) Hora Inicio</td>
								<td align='left'><input type='text' size='12' class='casillatext_fecha' name='hora_inicio' value='<?=nvl($frecuencia["hora_inicio"],date("H:i:s"))?>' readonly >&nbsp;<a title="Calendario" href="javascript:abrirSoloHora('hora_inicio','entryform')"><img alt="Hora" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-clock.png' border='0'></a> </td>
							</tr>
							<tr>
								<td align='right'>(*) Hora Fin</td>
								<td align='left'><input type='text' size='12' class='casillatext_fecha' name='hora_fin' value='<?=nvl($frecuencia["hora_fin"])?>' readonly >&nbsp;<a title="Calendario" href="javascript:abrirSoloHora('hora_fin','entryform')"><img alt="Hora" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-clock.png' border='0'></a> </td>
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
<?if($newMode != "insertar_frecuencia"){?>
<table width="98%" align="center">
	<tr>
		<td>
			<iframe id='operarios' src='<?=$CFG->wwwroot?>/opera/templates/listado_operariosxfrecuencia.php?id_frecuencia=<?=$frecuencia["id"]?>' width='100%' height='150' frameborder='0'></iframe>
		</td>
	</tr>
	<?if($frecuencia["esquema"] == "bar"){?>
	<tr>
		<td>
			<iframe id='bolsas' src='<?=$CFG->wwwroot?>/opera/templates/listado_bolsasxfrecuencia.php?id_frecuencia=<?=$frecuencia["id"]?>' width='100%' height='150' frameborder='0'></iframe>
		</td>
	</tr>
	<?}elseif($frecuencia["esquema"] == "rec"){?>
	<tr>
		<td>
			<iframe id='desplazamientos' src='<?=$CFG->wwwroot?>/opera/templates/listado_desplazamientosxfrecuencia.php?id_frecuencia=<?=$frecuencia["id"]?>' width='100%' height='200' frameborder='0'></iframe>
		</td>
	</tr>
	<?}?>
</table>
<?}?>
<script type="text/javascript">

function revisar()
{
	if(document.entryform.dia.options[document.entryform.dia.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Día');
		document.entryform.dia.focus();
		return(false);
	}
	if(document.entryform.id_turno.options[document.entryform.id_turno.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Turno');
		document.entryform.id_turno.focus();
		return(false);
	}
	if(document.entryform.produccion.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.produccion.value)){
			window.alert('[Producción] no contiene un dato válido.');
			document.entryform.produccion.focus();
			return(false);
		}
	}
	if(document.entryform.viajes.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Viajes');
		document.entryform.viajes.focus();
		return(false);
	}
	else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.viajes.value)){
			window.alert('[Viajes] no contiene un dato válido.');
			document.entryform.viajes.focus();
			return(false);
		}
	}
	if(document.entryform.hora_inicio.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Hora Inicio');
		document.entryform.hora_inicio.focus();
		return(false);
	}
	else{
		var regexpression=/^[0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.hora_inicio.value)){
			window.alert('[Hora Inicio] no contiene un dato válido.');
			document.entryform.hora_inicio.focus();
			return(false);
		}
	}
	if(document.entryform.hora_fin.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Hora Fin');
		document.entryform.hora_fin.focus();
		return(false);
	}
	else{
		var regexpression=/^[0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.hora_fin.value)){
			window.alert('[Hora Fin] no contiene un dato válido.');
			document.entryform.hora_fin.focus();
			return(false);
		}
	}

	return(true);
}

</script>
<?
include($CFG->templatedir . "/resize_window.php");
?>
