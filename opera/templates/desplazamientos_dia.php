<?
	$db->crear_select("SELECT t.id, centro||' / '||t.turno 
		FROM turnos t 
		LEFT JOIN centros c ON c.id_empresa = t.id_empresa
		WHERE c.id in (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')
		ORDER BY centro,t.id",$turnos,$id_turno, "Seleccione...");
?>

<table width="100%">
  <tr>
    <td height="40" colspan=3 align="center"><span class="azul_16"><strong>DESPLAZAMIENTOS DEL DÍA <?=$fecha?></strong></span></td>
  </tr>
	<form name="entryform_filtro" action="<?=$ME?>" method="GET"  class="form">
	<input type="hidden" name="fecha" value="<?=$fecha?>">
	<input type="hidden" name="mode" value="desplazamientos_<?=$squema?>">
	<tr>
		<td align="left" height="30">
			&nbsp;&nbsp;<span class="azul_11">Filtrar por Vehículo: </span><input type='text' size='8' class="casillatext" name='codigo_vehiculo' value='<?=$codigo_vehiculo?>'> 
			&nbsp;&nbsp;<span class="azul_11">Filtrar por Turno: </span><select  name="id_turno" id="id_turno" class="select_solo" onchange="this.form.submit();"><?=$turnos?></select>
			&nbsp;&nbsp;<input type="submit" class="boton_verde_peq" value="Filtrar" value="Filtrar">
		</td>
	</tr>
	</form>

	<?
		if($db->sql_numrows($qid) == 0) echo '<tr><td height="30" align="center">No existen resultados. <br /><a href="'.$ME.'?fecha='.$fecha.'&mode=desplazamientos_'.$squema.'" class="link_verde" title="Volver">Volver</a></td></tr>';
		while($micros = $db->sql_fetchrow($qid)){
		if($movxdia)
			$mov = $db->sql_row("SELECT m.*, v.codigo FROM rec.movimientos m LEFT JOIN vehiculos v ON v.id=m.id_vehiculo WHERE m.inicio::date='".$fecha."' AND m.id_micro='".$micros["id_micro"]."'");
		elseif($idMovimiento != "")
			$mov = $db->sql_row("SELECT m.*, v.codigo FROM rec.movimientos m LEFT JOIN vehiculos v ON v.id=m.id_vehiculo WHERE m.id='".$idMovimiento."'");

		$alto = 80;
		$numDes = $db->sql_row("SELECT count(id) AS num FROM rec.desplazamientos WHERE id_movimiento=".$mov["id"]);
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
											<td align='left'><span class="azul_12">RUTA <?=$micros["codigo"]?></span> / Vehículo: <?=$mov["codigo"]?> / Fecha Inicial: <?=$mov["inicio"]?> / Fecha Final: <?=$mov["final"]?></td>
											<td align="right"><?if($mov["final"] == ""){?><a href="javascript:abrirVentanaJavaScript('movcerrar','500','300','<?=$CFG->wwwroot?>/opera/movimientos_rec.php?mode=cerrarMovimientoConFecha_form&esquema=rec&id_movimiento=<?=$mov["id"]?>')" class="link_verde" title="Cerrar Movimiento con Otra Fecha">Cerrar Movimiento Con Otra Fecha</a><br /><a href="javascript:abrirVentanaJavaScript('movimientos','400','300','<?=$CFG->wwwroot?>/opera/templates/cerrar_movimientoFechaActual.php?esquema=rec&id_movimiento=<?=$mov["id"]?>')" class="link_verde" title="Cerrar Movimiento con Fecha Actual">Cerrar Movimiento con Fecha Actual</a><br /><?}?><a href="javascript:abrirVentanaJavaScript('novedades','800','500','<?=$CFG->wwwroot?>/novedades/novedades.php?mode=agregar&id_movimiento=<?=$mov["id"]?>&id_centro=<?=$micros["id_centro"]?>&clase=')" class="link_verde" title="Agregar Novedad">Agregar Novedad</a></td>
										</tr>
										<tr>
											<td colspan=2>
												<table width='100%' border=1 bordercolor="#7fa840" cellpadding=0 cellspacing=0>
													<tr><td align="center"><iframe name='listtrip' width='100%' height='<?=$alto?>' frameborder='0' src="<?=$CFG->wwwroot?>/opera/movimientos_rec.php?mode=listado_operarios&esquema=rec&id_movimiento=<?=$mov["id"]?>"></iframe></td></tr>
												</table>
											</td>
										</tr>
										<tr>
											<td colspan=2>
												<table width='100%' border=1 bordercolor="#7fa840" cellpadding=0 cellspacing=0>
													<tr><td align="center"><iframe name='listdesp' width='100%' height='<?=$alto?>' frameborder='0' src="<?=$CFG->wwwroot?>/opera/movimientos_rec.php?mode=listado_desplazamientos&id_movimiento=<?=$mov["id"]?>"></iframe></td></tr>
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
