<table width="100%">
  <tr>
    <td height="40" colspan=3 align="center"><span class="azul_16"><strong>BARRIDO DEL DÍA <?=$fecha?></strong></span></td>
  </tr>
	<?while($micros = $db->sql_fetchrow($qid)){
		if($fecha != "")
			$mov = $db->sql_row("SELECT m.*, v.codigo FROM bar.movimientos m LEFT JOIN vehiculos v ON v.id=m.id_vehiculo WHERE m.inicio::date='".$fecha."' AND m.id_micro='".$micros["id_micro"]."'");
		elseif($idMovimiento != "")
			$mov = $db->sql_row("SELECT m.*, v.codigo FROM bar.movimientos m LEFT JOIN vehiculos v ON v.id=m.id_vehiculo WHERE m.id='".$idMovimiento."'");

		$alto = 80;
		$numDes = $db->sql_row("SELECT count(id) AS num FROM bar.movimientos_bolsas WHERE id_movimiento=".$mov["id"]);
		$alto = $alto + (25 * $numDes["num"]);
	?>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" >
							<tr>
								<td>
									<table width="100%">
										<tr>
											<td align='left'><span class="azul_12">RUTA <?=$micros["codigo"]?></span> / Vehículo: <?=$mov["codigo"]?> / Fecha Final: <?=$mov["final"]?></td>
											<td align="right"><?if($mov["final"] == ""){?><a href="javascript:abrirVentanaJavaScript('movcerrar','500','300','<?=$CFG->wwwroot?>/opera/movimientos_bar.php?mode=cerrarMovimientoConFecha_form&esquema=bar&id_movimiento=<?=$mov["id"]?>')" class="link_verde" title="Cerrar Movimiento con Otra Fecha">Cerrar Movimiento Con Otra Fecha</a><br /><a href="javascript:abrirVentanaJavaScript('movimientos','10','10','<?=$CFG->wwwroot?>/opera/movimientos_bar.php?mode=cerrarMovimiento&esquema=bar&id_movimiento=<?=$mov["id"]?>')" class="link_verde" title="Cerrar Movimiento">Cerrar Movimiento</a><br /><?}?><a href="javascript:abrirVentanaJavaScript('novedades','800','500','<?=$CFG->wwwroot?>/novedades/novedades.php?mode=agregar&id_movimiento=<?=$mov["id"]?>&id_centro=<?=$micros["id_centro"]?>&clase=')" class="link_verde" title="Agregar Novedad">Agregar Novedad</a></td>
										</tr>
										<tr>
											<td width='65%'>
												<table width='100%' border=1 bordercolor="#7fa840" cellpadding=0 cellspacing=0>
													<tr><td align="center"><iframe name='listtrip' width='100%' height='<?=$alto?>' frameborder='0' src="<?=$CFG->wwwroot?>/opera/movimientos_bar.php?mode=listado_operarios&esquema=bar&id_movimiento=<?=$mov["id"]?>"></iframe></td></tr>
												</table>
											</td>
											<td >
												<table width='100%' border=1 bordercolor="#7fa840" cellpadding=0 cellspacing=0>
													<tr><td align="center"><iframe name='listdesp' width='100%' height='<?=$alto?>' frameborder='0' src="<?=$CFG->wwwroot?>/opera/movimientos_bar.php?mode=listado_bolsas_barrido&id_movimiento=<?=$mov["id"]?>"></iframe></td></tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td height="20">&nbsp;</td></tr>
<?}?>
</table>
