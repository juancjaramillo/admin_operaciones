<form name="entryform" action="<?=$ME?>" method="POST"  class="form" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id_rutina" value="<?=$actividad["id_rutina"]?>">
<input type="hidden" name="id" value="<?=nvl($actividad["id"])?>">
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
								<td align='right'>(*) Orden</td>
								<td align='left'><input type='text' size='5'  name='orden' class='casillatext' value='<?=nvl($actividad["orden"])?>'></td>
							</tr>
							<tr>
								<td align='right'>(*) Descripción</td>
								<td align='left'><textarea  rows='2' cols='40' name='descripcion'><?=nvl($actividad["descripcion"])?></textarea></td>
							</tr>
							<tr>
								<td align='right'>(*) Tiempo (minutos)</td>
								<td align='left'><input type='text' size='5'  name='tiempo' class='casillatext' value='<?=nvl($actividad["tiempo"])?>'></td>
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
<?if($newMode != "insertar_actividad"){?>
<table width="98%" align="center">
	<tr>
		<td>
			<iframe id='actividades' src='<?=$CFG->wwwroot?>/mtto/rutinas.php?mode=listar_cargos&id_actividad=<?=$actividad["id"]?>' width='100%' height='200' frameborder='0'></iframe>
		</td>
	</tr>
</table>
<?}?>
<script type="text/javascript">

function revisar()
{
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
	if(document.entryform.descripcion.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Descripción');
		document.entryform.descripcion.focus();
		return(false);
	}
	else{
		var regexpression=/./;
		if(!regexpression.test(document.entryform.descripcion.value)){
			window.alert('[Descripción] no contiene un dato válido.');
			document.entryform.descripcion.focus();
			return(false);
		}
	}
	if(document.entryform.tiempo.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Tiempo (minutos)');
		document.entryform.tiempo.focus();
		return(false);
	}
	else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.tiempo.value)){
			window.alert('[Tiempo (minutos)] no contiene un dato válido.');
			document.entryform.tiempo.focus();
			return(false);
		}
	}

	return(true);
}

function cargarValorTiempoActividadDos(valor)
{
	document.entryform.tiempo.value=valor;

}


</script>
<?
include($CFG->templatedir . "/resize_window.php");
?>
