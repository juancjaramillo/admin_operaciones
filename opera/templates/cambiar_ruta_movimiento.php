<?
include("../../application.php");
include($CFG->dirroot."/templates/header_popup.php");

if(!in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["cambiar_ruta_movimiento"]))
	die("no tiene permisos");

$user=$_SESSION[$CFG->sesion]["user"];

$rutaAct = $db->sql_row("SELECT r.codigo, to_char(m.inicio,'YYYY-MM-DD') as fecha, id_ase,a.id_centro
	FROM ".$_GET["esquema"].".movimientos m
	LEFT JOIN micros r ON r.id = m.id_micro
	LEFT JOIN ases a ON r.id_ase = a.id
	WHERE m.id=".$_GET["id_movimiento"]);

#$qidRutas = $db->sql_query("SELECT r.id, r.codigo
#	FROM micros r
#	LEFT JOIN ases a ON a.id = r.id_ase
#	WHERE a.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')  AND r.id NOT IN (SELECT id_micro FROM  ".$_GET["esquema"].".movimientos WHERE inicio::date = '".$rutaAct["fecha"]."'  ) AND id_servicio IN (SELECT id FROM servicios WHERE esquema='".$_GET["esquema"]."' )
#	ORDER BY r.codigo");
$qidRutas = $db->sql_query("SELECT r.id, r.codigo
	FROM micros r
	LEFT JOIN ases a ON a.id = r.id_ase
	WHERE a.id_centro = '".$rutaAct["id_centro"]."' AND id_servicio IN (SELECT id FROM servicios WHERE esquema='".$_GET["esquema"]."' ) AND (r.fecha_hasta IS NULL OR r.fecha_hasta>'" . date("Y-m-d") . "')
	ORDER BY r.codigo");

?>
<form name="entryform" action="<?=$CFG->wwwroot?>/opera/movimientos_<?=$_GET["esquema"]?>.php" method="POST"  class="form" onSubmit="return revisar()">
<input type="hidden" name="mode" value="cambiar_ruta_movimiento">
<input type="hidden" name="id_movimiento" value="<?=$_GET["id_movimiento"]?>">
<input type="hidden" name="esquema" value="<?=$_GET["esquema"]?>">

<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong>CAMBIAR LA RUTA <?=$rutaAct["codigo"]?> AL MOVIMIENTO</strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td align='right'>Nueva Ruta</td>
								<td align='left'><select  name="id_nueva_ruta" ><option value='%'>Seleccione ...</option>
									<?
									while($rt = $db->sql_fetchrow($qidRutas))
									{
										echo "<option value='".$rt["id"]."'>".$rt["codigo"]."</option>";
									}
									?></select></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
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
	if(document.entryform.id_nueva_ruta.options[document.entryform.id_nueva_ruta.selectedIndex].value=='%'){
		window.alert('Por favor seleccione la nueva ruta');
		document.entryform.id_nueva_ruta.focus();
		return(false);
	}

	return(true);
}

</script>

<?
include($CFG->templatedir . "/resize_window.php");
	include($CFG->dirroot."/templates/footer_popup.php");
?>

