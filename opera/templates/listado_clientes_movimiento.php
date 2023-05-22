<?
include("../../application.php");
include($CFG->dirroot."/templates/header_popup.php");

if(!isset($_SESSION[$CFG->sesion]["user"])){
	$errorMsg="No existe la sesión.";
	error_log($errorMsg);
	die($errorMsg);
}


$qid = $db->sql_query("SELECT m.id, c.nombre, c.direccion, c.codigo
		FROM rec.movimientos_clientes m
		LEFT JOIN clientes c ON c.id = m.id_cliente
		WHERE m.id_movimiento=".$_GET["id_movimiento"]);


?>
<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong>MOVIMIENTOS / CLIENTES</strong></span></td>
	</tr>
	<tr>
		<td align="right"><a href="javascript:abrirVentanaJavaScript('pesos','800','200','<?=$CFG->wwwroot?>/opera/movimientos_rec.php?mode=agregar_cliente_movimiento&id_movimiento=<?=$_GET["id_movimiento"]?>')" class="link_verde" title="Agregar">Agregar</a></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840">
							<tr>
								<td width="50%" align="center" class="casillatext">CLIENTE</td>
								<td width="40%" align="center" class="casillatext">CÓDIGO</td>
								<td width="40%" align="center" class="casillatext">DIRECCIÓN</td>
								<td width="10%" align="center" class="casillatext">OPCIONES</td>
							</tr>
							<?
							while($act = $db->sql_fetchrow($qid)){?>
							<tr>
								<td><?=$act["nombre"]?></td>
								<td><?=$act["codigo"]?></td>
								<td><?=$act["direccion"]?></td>
								<td align="center">
									<a href="javascript:eliminar_cliente_mov('<?=$act["id"]?>')" class="link_verde" title="Borrar">B</a>&nbsp;
								</td>
							</tr>
							<?}?>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<script>

function eliminar_cliente_mov(id)
{
	url = '<?=$CFG->wwwroot?>/opera/movimientos_rec.php?mode=eliminar_cliente_movimiento&id_movimiento=<?=$_GET["id_movimiento"]?>&id='+id;
	window.location.href=url;
}

</script>
<?include($CFG->templatedir . "/resize_window.php");?>