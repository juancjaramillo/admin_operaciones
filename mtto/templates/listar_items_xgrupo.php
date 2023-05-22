<table width="100%">
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840">
							<tr>
								<td width="5%" align="center" class="casillatext">ORDEN</td>
								<td width="20%" align="center" class="casillatext">GRUPO</td>
								<td width="75%" align="center" class="casillatext">TEXTO</td>
								<td width="75%" align="center" class="casillatext">OPCIONES</td>
							</tr>
							<?
							while($act = $db->sql_fetchrow($qid)){?>
							<tr>
								<td><?=$act["orden"]?></td>
								<td><?=$act["grupo"]?></td>
								<td><?=$act["texto"]?></td>
								<td align="center">
									<a href="javascript:abrirVentanaJavaScript('itemae','500','230','<?=$CFG->wwwroot?>/mtto/inspecciones.php?mode=editar_item&id=<?=$act["id"]?>')" class="link_verde" title="Actualizar">A</a>&nbsp;&nbsp;
									<a href="javascript:abrirVentanaJavaScript('itemae','500','230','<?=$CFG->wwwroot?>/mtto/inspecciones.php?mode=eliminar_item&id=<?=$act["id"]?>')" class="link_verde" title="Borrar">B</a>								
								</td>
							</tr>
							<?}?>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="right" height="50" valign="center">
			<a class="boton_verde" href="javascript:abrirVentanaJavaScript('itemae','500','230','<?=$CFG->wwwroot?>/mtto/inspecciones.php?mode=agregar_item&id_grupo=<?=$idGrupo?>')" title="Agregar Ítem">Agregar Ítem</a>&nbsp;
		</td>
	</tr>
</table>
