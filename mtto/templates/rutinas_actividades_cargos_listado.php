<table width="100%">
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" id="tabla_actividades">
							<tr>
								<td align='left' colspan=3><span class="azul_12">CARGOS</span></td>
							</tr>
							<tr>
								<td width="90%" align="center" class="casillatext">CARGO</td>
								<td width="10%" align="center" class="casillatext">TIEMPO</td>
								<td width="10%" align="center" class="casillatext">OPCIONES</td>
							</tr>
							<?
							while($car = $db->sql_fetchrow($qidR)){?>
							<tr>
								<td><?=$car["cargo"]?></td>
								<td align="center"><?=$car["tiempo"]?></td>
								<td align="center"><a href="javascript:abrirVentanaJavaScript('cargos','600','250','<?=$CFG->wwwroot?>/mtto/rutinas.php?mode=editar_cargo&id=<?=$car["id"]?>')" class="link_verde" title="Actualizar">A</a>&nbsp;&nbsp;&nbsp;
								<a href="javascript:eliminar_cargo('<?=$car["id"]?>')" class="link_verde" title="Borrar">B</a></td>
							</tr>
							<?}?>
						</table>
					</td>
				</tr>
				<tr>
					<td align="center"><a href="javascript:abrirVentanaJavaScript('cargos','600','250','<?=$CFG->wwwroot?>/mtto/rutinas.php?mode=agregar_cargo&id_actividad=<?=$idActividad?>')" class="link_verde">+ Agregar Cargo +</a> </td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<script  type="text/javascript">
function eliminar_cargo(id)
{
	if(confirm("¿Está seguro de borrar el cargo?"))
	{
		url = "<?=$CFG->wwwroot?>/mtto/rutinas.php?mode=eliminar_cargo&id="+id;
		abrirVentanaJavaScript('cargos','500','150',url);
	}
}

function cargarValorTiempoActividadUno(valor)
{
	window.parent.cargarValorTiempoActividadDos(valor);
}


</script>

