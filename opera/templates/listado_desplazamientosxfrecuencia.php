<?
include_once("../../application.php");
include($CFG->dirroot."/templates/header_popup.php");

$qid = $db->sql_query("SELECT f.*, t.tipo
		FROM frecuencias_desplazamientos f
		LEFT JOIN rec.tipos_desplazamientos t ON t.id=f.id_tipo_desplazamiento
		WHERE f.id_frecuencia=".$_GET["id_frecuencia"]."
		ORDER BY orden");
?>

<table width="100%">
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" id="tabla_actividades">
							<tr>
								<td align='left' colspan=4><span class="azul_12">DESPLAZAMIENTOS PREDETERMINADOS</span></td>
							</tr>
							<tr>
								<td width="50%" align="center" class="casillatext">TIPO</td>
								<td width="10%" align="center" class="casillatext">ORDEN</td>
								<td width="10%" align="center" class="casillatext">OPCIONES</td>
							</tr>
							<?
							while($car = $db->sql_fetchrow($qid)){?>
							<tr>
								<td><?=$car["tipo"]?></td>
								<td align="center"><?=$car["orden"]?></td>
								<td align="center"><a href="javascript:eliminar('<?=$car["id"]?>')" class="link_verde" title="Borrar">B</a></td>
							</tr>
							<?}?>
						</table>
					</td>
				</tr>
				<tr>
					<td align="center"><a href="javascript:abrirVentanaJavaScript('cargos','600','250','<?=$CFG->wwwroot?>/opera/micros.php?mode=agregar_desplazamiento&id_frecuencia=<?=$_GET["id_frecuencia"]?>')" class="link_verde">+ Agregar Desplazamiento +</a> </td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<script  type="text/javascript">
function eliminar(id)
{
	if(confirm("¿Está seguro de borrar el desplazamiento?"))
	{
		url = "<?=$CFG->wwwroot?>/opera/micros.php?mode=eliminar_desplazamiento&id="+id;
		abrirVentanaJavaScript('cargos','100','100',url);
	}
}
</script>
