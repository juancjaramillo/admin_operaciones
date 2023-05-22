<table width="100%">
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" id="tabla_actividades">
							<tr>
								<td align='left' colspan=4><span class="azul_12">MEDICIONES</span></td>
							</tr>
							<tr>
								<td width="10%" align="center" class="casillatext">ORDEN</td>
								<td width="80%" align="center" class="casillatext">NOMBRE</td>
								<td width="10%" align="center" class="casillatext">UNIDAD</td>
								<td width="10%" align="center" class="casillatext">OPCIONES</td>
							</tr>
							<?
							while($act = $db->sql_fetchrow($qidR)){?>
							<tr>
								<td><?=$act["orden"]?></td>
								<td><?=$act["nombre"]?></td>
								<td><?=$act["unidad"]?></td>
								<td align="center"><a href="javascript:abrirVentanaJavaScript('mediciones','500','200','<?=$CFG->wwwroot?>/mtto/rutinas.php?mode=editar_medicion&id=<?=$act["id"]?>')" class="link_verde" title="Actualizar">A</a>&nbsp;&nbsp;&nbsp;<a href="javascript:eliminar_medicion('<?=$act["id"]?>')" class="link_verde" title="Borrar">B</a></td>
							</tr>
							<?}?>
						</table>
					</td>
				</tr>
				<tr>
					<td align="center"><a href="javascript:abrirVentanaJavaScript('mediciones','500','200','<?=$CFG->wwwroot?>/mtto/rutinas.php?mode=agregar_medicion&id_rutina=<?=$idRutina?>')" class="link_verde">+ Agregar Medición +</a> </td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<script  type="text/javascript">
function eliminar_medicion(id)
{
	if(confirm("¿Está seguro de borrar la medición?"))
	{
		url = "<?=$CFG->wwwroot?>/mtto/rutinas.php?mode=eliminar_medicion&id="+id;
		abrirVentanaJavaScript('mediciones','500','150',url);
	}
}
</script>
