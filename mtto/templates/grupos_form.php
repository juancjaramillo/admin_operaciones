<form name="entryform" action="<?=$ME?>" method="POST"  class="form" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id" value="<?=nvl($grupo["id"])?>">
<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong><?=strtoupper($newMode)?> GRUPO</strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td align='right'>Grupo Superior</td>
								<td align='left'> <select  name='id_superior'><option value="">Raíz</option><?=$select_grupos?></select> </td>
							</tr>
							<tr>
								<td align='right'>Nombre</td>
								<td align='left'><input type='text' size='40' class='casillatext' name='nombre' value='<?=nvl($grupo["nombre"])?>'></td>
							</tr>
							<tr>
								<td align='right'>Centro</td>
								<td align='left'> <select  name='id_centro'><?=$centros?></select> </td>
							</tr>
							<tr>
								<td align='right'>Descripción</td>
								<td align='left'><textarea  rows='2' cols='40' name='descripcion'><?=nvl($grupo["descripcion"])?></textarea></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=3 align="center">
			<?if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["editarEliminarGrupo"])){?>
			<input type="submit" class="boton_verde" value="Aceptar" />
			<input type="button" class="boton_verde" value="Cancelar" onclick="window.close()"/>
			<?if($newMode != "insertar"){?>
				<input type="button" class="boton_rojo" value="Eliminar" onclick="eliminar()"/>
			<?}}else{?>
			<input type="button" class="boton_verde" value="Cancelar" onclick="window.close()"/>
			<?}?>
		</td>
	</tr>
	</form>
</table>
<script type="text/javascript">

function revisar()
{
	if(document.entryform.id_superior.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.id_superior.value)){
			window.alert('[Grupo Superior] no contiene un dato válido.');
			document.entryform.id_superior.focus();
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
	if(document.entryform.descripcion.value !=''){
		var regexpression=/./;
		if(!regexpression.test(document.entryform.descripcion.value)){
			window.alert('[Descripción] no contiene un dato válido.');
			document.entryform.descripcion.focus();
			return(false);
		}
	}

	return(true);
}

function eliminar()
{
	texto='¿Está seguro de querer borrar el grupo?';
	if(!confirm(texto)) return;

	document.entryform.mode.value='eliminar';
	document.entryform.submit();
	return;
}




</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>

