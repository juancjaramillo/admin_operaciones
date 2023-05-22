<table width="100%">
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" id="tabla_actividades">
							<tr>
								<td align='left' colspan=4><span class="azul_12">TRABAJO EXTERNO</span></td>
							</tr>
							<tr>
								<td width="60%" align="center" class="casillatext">RAZÓN SOCIAL</td>
								<td width="15%" align="center" class="casillatext">COSTO</td>
								<td width="25%" align="center" class="casillatext">TIEMPO (hr)</td>
								<td width="25%" align="center" class="casillatext">OPCIÓN</td>
							</tr>
							<?
							while($act = $db->sql_fetchrow($qidR)){?>
							<tr>
								<td><?=$act["razon"]?></td>
								<td><?=$act["costo"]?></td>
								<td><?=$act["tiempo"]?></td>
								<td align="center"><a href="javascript:eliminar_taller('<?=$act["id"]?>')" class="link_verde" title="Borrar">B</a></td>
							</tr>
							<?}?>
						</table>
					</td>
				</tr>
				<tr>
					<td align="center"><a href="javascript:abrirVentanaJavaScript('talleres','500','200','<?=$CFG->wwwroot?>/mtto/rutinas.php?mode=agregar_taller&id_rutina=<?=$idRutina?>')" class="link_verde">+ Agregar Taller +</a> </td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<script  type="text/javascript">
function eliminar_taller(id)
{
	if(confirm("¿Está seguro de borrar el taller?"))
	{
		url = "<?=$CFG->wwwroot?>/mtto/rutinas.php?mode=eliminar_taller&id="+id;
		abrirVentanaJavaScript('talleres','1','1',url);
	}
}

function cargarValorTiempoRutinaUno(valor)
{
	window.parent.cargarValorTiempoRutinaDos(valor);
}


</script>
