<?
include_once("../../application.php");
include($CFG->dirroot."/templates/header_popup.php");

$qid = $db->sql_query("SELECT f.*, t.tipo
		FROM frecuencias_bolsas f
		LEFT JOIN bar.tipos_bolsas t ON t.id=f.id_tipo_bolsa
		WHERE f.id_frecuencia=".$_GET["id_frecuencia"]);
?>

<table width="100%">
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" id="tabla_actividades">
							<tr>
								<td align='left' colspan=4><span class="azul_12">BOLSAS PREDETERMINADAS</span></td>
							</tr>
							<tr>
								<td width="50%" align="center" class="casillatext">TIPO</td>
								<td width="30%" align="center" class="casillatext">NÚMERO INICIO</td>
								<td width="10%" align="center" class="casillatext">OPCIONES</td>
							</tr>
							<?
							while($car = $db->sql_fetchrow($qid)){?>
							<tr>
								<td><?=$car["tipo"]?></td>
								<td align="center"><?=$car["numero_inicio"]?></td>
								<td align="center"><a href="javascript:eliminar('<?=$car["id"]?>')" class="link_verde" title="Borrar">B</a></td>
							</tr>
							<?}?>
						</table>
					</td>
				</tr>
				<tr>
					<td align="center"><a href="javascript:abrirVentanaJavaScript('cargos','600','250','<?=$CFG->wwwroot?>/opera/micros.php?mode=agregar_bolsa&id_frecuencia=<?=$_GET["id_frecuencia"]?>')" class="link_verde">+ Agregar Bolsa +</a> </td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<script  type="text/javascript">
function eliminar(id)
{
	if(confirm("¿Está seguro de borrar la bolsa?"))
	{
		url = "<?=$CFG->wwwroot?>/opera/micros.php?mode=eliminar_bolsa&id="+id;
		abrirVentanaJavaScript('cargos','100','100',url);
	}
}
</script>
