<form name="entryform" action="<?=$ME?>" method="POST"  class="form" enctype="multipart/form-data" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id" value="<?=nvl($archivo["id"])?>">
<input type="hidden" name="id_equipo" value="<?=nvl($archivo["id_equipo"])?>">
<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong><?=strtoupper($newMode)?> ARCHIVO</strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td align='right'>(*) Fecha</td>
								<td align='left'>
									<input type='text' size="10" id="f_fecha" class="casillatext_fecha" name='fecha' value='<?=nvl($archivo["fecha"])?>' /><button id="b_fecha" onclick="javascript:showCalendarSencillo('f_fecha','b_fecha')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>(*) Nombre Archivo</td>
								<td align='left'><input type='text' size='30' class='casillatext' name='nombre' value='<?=nvl($archivo["nombre"])?>'></td>
							</tr>

							<tr>
								<td align='right'>Observaciones</td>
								<td align='left'><textarea   name='observaciones'><?=nvl($archivo["observaciones"])?></textarea></td>
							</tr>
							<tr>
								<td align='right'>(*) Documento</td>
								<td align='left'>
									<?
									if($newMode != "insertar"){
										if(nvl($archivo["archivo"]) != ''){
											echo "Archivo actual:<br>
											<a href='".$CFG->wwwroot."/admin/file.php?table=mtto.equipos_archivos&field=archivo&id=".$archivo["id"]."' class='link_verde'>".$archivo["mmdd_archivo_filename"]."</a>";
										}?>
										<input type='hidden' name='mmdd_archivo_filename' value='<?=nvl($archivo["mmdd_archivo_filename"])?>'>
										<input type='hidden' name='mmdd_archivo_filetype' value='<?=nvl($archivo["mmdd_archivo_filetype"])?>'>
										<input type='hidden' name='mmdd_archivo_filesize' value='<?=nvl($archivo["mmdd_archivo_filesize"])?>'>
									<?}?>
										<input type='file' name='archivo' size='20' >
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
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
	if(document.entryform.fecha.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Fecha');
		document.entryform.fecha.focus();
		return(false);
	}
	else{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/m;
		if(!regexpression.test(document.entryform.fecha.value)){
			window.alert('[Fecha] no contiene un dato válido.');
			document.entryform.fecha.focus();
			return(false);
		}
	}
	if(document.entryform.nombre.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Nombre');
		document.entryform.nombre.focus();
		return(false);
	}
	else{
		var regexpression=/^.{1,255}$/m;
		if(!regexpression.test(document.entryform.nombre.value)){
			window.alert('[Nombre] no contiene un dato válido.');
			document.entryform.nombre.focus();
			return(false);
		}
	}
	if(document.entryform.observaciones.value !=''){
		var regexpression=/./;
		if(!regexpression.test(document.entryform.observaciones.value)){
			window.alert('[Observaciones] no contiene un dato válido.');
			document.entryform.observaciones.focus();
			return(false);
		}
	}
	<?if($newMode == "insertar_archivo"){?>
	if(document.entryform.archivo.value ==''){
		window.alert('Por favor seleccione un documento a subir');
		document.entryform.archivo.focus();
		return(false);
	}
	<?}?>

	return(true);
}


</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>

