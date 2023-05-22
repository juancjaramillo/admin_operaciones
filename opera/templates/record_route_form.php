<br />
<br />
<form name="entryform" method="GET" action="<?=$ME?>" onSubmit="return revisar();">
	<input type="hidden" name="mode" value="<?=$frm["newMode"]?>">
	<input type="hidden" name="id_micro" value="<?=$frm["id_micro"]?>">
	<table align="center">
		<tr>
			<td>Seleccione el vehículo:</td>
			<td><select name="id_vehiculo"><?=$vehiculosOptions?></select></td>
		</tr>
		<tr>
			<td>Fecha / hora inicial:</td><td><input size="15" name="fecha_desde" value="<?=nvl($frm["fecha_desde"])?>"></td>
		</tr>
		<tr>
			<td>Fecha / hora final:</td><td><input size="15" name="fecha_hasta" value="<?=nvl($frm["fecha_hasta"])?>"></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Aceptar"></td>
		</tr>
	</table>
</form>
