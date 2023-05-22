<form name="entryform" action="<?=$ME?>" method="POST"  class="form" enctype="multipart/form-data" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id" value="<?=nvl($equipo["id"])?>">
<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong><?=strtoupper($newMode)?> EQUIPO</strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td align='right'>(*) Grupo</td>
								<td align='left'> <select  name='id_grupo'><option value="">Raíz...</option><?=$grupos?></select> </td>
							</tr>
							<tr>
								<td align='right'>(*) Nombre</td>
								<td align='left'><input type='text' size='30' class='casillatext' name='nombre' value='<?=nvl($equipo["nombre"])?>'></td>
							</tr>
							<tr>
								<td align='right'>(*) Centro</td>
								<td align='left'> <select  name='id_centro' onChange="updateRecursive_id_equipo(this)"><?=$centros?></select> </td>
							</tr>
							<tr>
								<td align='right'>Vehículo</td>
								<td align='left'> <select  name='id_vehiculo'><option value="%">Seleccione...</option><?=$vehiculos?></select> </td>
							</tr>
							<tr>
								<td align='right'>Equipo Superior</td>
								<td align='left'><select  name="id_superior"><option value="%">Seleccione...</option><?=$select_equipos?></select></td>
							</tr>
							<tr>
								<td align='right'>Código</td>
								<td align='left'><input type='text' size='30' class='casillatext' name='codigo' value='<?=nvl($equipo["codigo"])?>'></td>
							</tr>
							<tr>
								<td align='right'>Serial</td>
								<td align='left'><input type='text' size='30' class='casillatext' name='serial' value='<?=nvl($equipo["serial"])?>'></td>
							</tr>
							<tr>
								<td align='right'>(*) Kilometraje</td>
								<td align='left'><input type='text' size='10' class='casillatext' name='kilometraje' value='<?=nvl($equipo["kilometraje"])?>'></td>
							</tr>
							<tr>
								<td align='right'>(*) Horómetro</td>
								<td align='left'><input type='text' size='10' class='casillatext' name='horometro' value='<?=nvl($equipo["horometro"])?>'></td>
							</tr>
							<tr>
								<td align='right'>Imagen</td>
								<td align='left'>
									<?
									if($newMode != "insertar"){
										if(nvl($equipo["imagen"]) != ''){
											echo "Imagen actual:<br>
											<img src='".srcImagen($equipo["id"],"mtto.equipos",$equipo["mmdd_imagen_filename"],"imagen",250)."' border=0>&nbsp;
											<a href='".$ME."?mode=eliminarImagen&id=".$equipo["id"]."' class='link_verde'>(Borrar Imagen)</a><br>";
										}?>
										<input type='hidden' name='mmdd_imagen_filename' value='<?=$equipo["mmdd_imagen_filename"]?>'>
										<input type='hidden' name='mmdd_imagen_filetype' value='<?=$equipo["mmdd_imagen_filetype"]?>'>
										<input type='hidden' name='mmdd_imagen_filesize' value='<?=$equipo["mmdd_imagen_filesize"]?>'>
									<?}?>
										<input type='file' name='imagen' size='20' >
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<?if($newMode != "insertar"){?>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
			<?
				$entrar = $entrarEditar = false;
				$datos = array($equipo["id_grupo"]);
				obtenerIdsGrupos($equipo["id_grupo"],$datos);
				$rutinas = array();
				$qid = $db->sql_query("SELECT id
						FROM mtto.rutinas 
						WHERE mtto.rutinas.activa AND id_grupo IN (".implode(",",$datos).")");
				while($queryRut = $db->sql_fetchrow($qid))
				{
					$qidOT = $db->sql_row("SELECT count(*) as num FROM mtto.ordenes_trabajo WHERE id_equipo='".$equipo["id"]."' AND id_rutina='".$queryRut["id"]."'");
					if($qidOT["num"] == 0)
						$entrar = true;
				}
				$qid = $db->sql_query("SELECT id FROM mtto.rutinas WHERE mtto.rutinas.activa AND id_equipo='".$equipo["id"]."'");
				if($db->sql_numrows($qid)>0)
				{
					while($queryRut = $db->sql_fetchrow($qid))
					{
						$qidOT = $db->sql_row("SELECT count(*) as num FROM mtto.ordenes_trabajo WHERE id_equipo='".$equipo["id"]."' AND id_rutina='".$queryRut["id"]."'");
						if($qidOT["num"] == 0)
							$entrar = true;
					}
				}

				//editarlas de primera vez
				$qidOTPV = $db->sql_row("SELECT count(pv.*) as num
						FROM mtto.rutinas_primera_vez pv
						LEFT JOIN mtto.ordenes_trabajo o ON o.id = pv.id_orden_trabajo
						LEFT JOIN mtto.rutinas r ON r.id=pv.id_rutina
						WHERE pv.id_equipo='".$equipo["id"]."' AND r.activa AND o.id_estado_orden_trabajo=10 AND o.fecha_ejecucion_inicio IS NULL");
				if($qidOTPV["num"] > 0)
					$entrarEditar = true;
				
				if($entrar){?>
				<tr>
					<td><a href="javascript:abrirVentanaJavaScript('rut_privez','800','500','<?=$CFG->wwwroot?>/mtto/equipos.php?mode=primera_vez&id_equipo=<?=$equipo["id"]?>')" class="link_verde">+ Programar Primera Vez +</a></td>
				</tr>
				<?}
				if($entrarEditar){?>
				<tr>
					<td><a href="javascript:abrirVentanaJavaScript('rut_privez','800','500','<?=$CFG->wwwroot?>/mtto/equipos.php?mode=editar_primera_vez&id_equipo=<?=$equipo["id"]?>')" class="link_verde">+ Editar Programación Primera Vez +</a></td>
				</tr>
			<?}?>
			</table>
		</td>
		<?}?>
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
	if(document.entryform.id_grupo.value.replace(/ /g, '') ==''){
		window.alert('Por favor seleccione: Grupo');
		document.entryform.id_grupo.focus();
		return(false);
	}
	else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.id_grupo.value)){
			window.alert('[Grupo] no contiene un dato válido.');
			document.entryform.id_grupo.focus();
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
	if(document.entryform.id_centro.options[document.entryform.id_centro.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Centro');
		document.entryform.id_centro.focus();
		return(false);
	}

	if(document.entryform.id_superior.value !='%'){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.id_superior.value)){
			window.alert('[Equipo Superior] no contiene un dato válido.');
			document.entryform.id_superior.focus();
			return(false);
		}
	}
	if(document.entryform.codigo.value !=''){
		var regexpression=/^.{1,125}$/m;
		if(!regexpression.test(document.entryform.codigo.value)){
			window.alert('[Código] no contiene un dato válido.');
			document.entryform.codigo.focus();
			return(false);
		}
	}
	if(document.entryform.serial.value !=''){
		var regexpression=/^.{1,125}$/m;
		if(!regexpression.test(document.entryform.serial.value)){
			window.alert('[Serial] no contiene un dato válido.');
			document.entryform.serial.focus();
			return(false);
		}
	}
	if(document.entryform.kilometraje.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Kilometraje');
		document.entryform.kilometraje.focus();
		return(false);
	}
	else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.kilometraje.value)){
			window.alert('[Kilometraje] no contiene un dato válido.');
			document.entryform.kilometraje.focus();
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


	return(true);
}


</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>

