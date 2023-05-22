<table width="100%">
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" id="tabla_actividades">
							<tr>
								<td align='left' colspan=4><span class="azul_12">ACTIVIDADES</span></td>
							</tr>
							<tr>
								<td width="10%" align="center" class="casillatext">ORDEN</td>
								<td width="80%" align="center" class="casillatext">DESCRIPCIÓN</td>
								<td width="10%" align="center" class="casillatext">TIEMPO</td>
								<td width="10%" align="center" class="casillatext">OPCIONES</td>
							</tr>
							<?
							while($act = $db->sql_fetchrow($qidR)){?>
							<tr>
								<td><?=$act["orden"]?></td>
								<td><?=$act["descripcion"]?></td>
								<td><?=$act["tiempo"]?></td>
								<td align="center"><a href="javascript:abrirVentanaJavaScript('actividad','500','500','<?=$CFG->wwwroot?>/mtto/rutinas.php?mode=editar_actividad&id=<?=$act["id"]?>')" class="link_verde" title="Actualizar">A</a>&nbsp;&nbsp;&nbsp;<a href="javascript:eliminar_actividad('<?=$act["id"]?>')" class="link_verde" title="Borrar">B</a></td>
							</tr>
							<?}?>
						</table>
					</td>
				</tr>
				<tr>
					<td align="center"><a href="javascript:abrirVentanaJavaScript('actividad','500','500','<?=$CFG->wwwroot?>/mtto/rutinas.php?mode=agregar_actividad&id_rutina=<?=$idRutina?>')" class="link_verde">+ Agregar Actividad +</a> </td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<script  type="text/javascript">
function eliminar_actividad(id)
{
	if(confirm("¿Está seguro de borrar la actividad?"))
	{
		url = "<?=$CFG->wwwroot?>/mtto/rutinas.php?mode=eliminar_actividad&id="+id;
		abrirVentanaJavaScript('actividad','500','150',url);
	}
}

function cargarValorTiempoRutinaUno(valor)
{
	window.parent.cargarValorTiempoRutinaDos(valor);
}



</script>
