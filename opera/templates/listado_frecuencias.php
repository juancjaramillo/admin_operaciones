<?
include_once("../../application.php");
include($CFG->dirroot."/templates/header_popup.php");

$micro = $db->sql_row("SELECT codigo FROM micros WHERE id=".$_GET["id_micro"]);
$qid = $db->sql_query("SELECT f.id,dia as odia, case when dia=1 then 'Lunes' when dia=2 then 'Martes' when dia=3 then 'Miércoles' when dia=4 then 'Jueves' when dia=5 then 'Viernes' when dia=6 then 'Sábado' else 'Domingo' end as dia, turno, produccion, viajes
		FROM micros_frecuencia f
		LEFT JOIN turnos t ON t.id=f.id_turno
		WHERE f.id_micro=".$_GET["id_micro"]."
		ORDER BY odia");
?>
<table width="100%">
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
				<tr>
					<td height="40" class="azul_16" valign="center"><strong>FRECUENCIAS DE LA RUTA <?=$micro["codigo"]?></strong></td>
				</tr>
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840">
							<tr>
								<td width="80%" align="center" class="casillatext">DÍA</td>
								<td width="10%" align="center" class="casillatext">TURNO</td>
								<td width="80%" align="center" class="casillatext">PRODUCCIÓN</td>
								<td width="10%" align="center" class="casillatext">VIAJES</td>
								<td width="10%" align="center" class="casillatext">OPCIONES</td>
							</tr>
							<?
							while($frec = $db->sql_fetchrow($qid)){?>
							<tr>
								<td><?=$frec["dia"]?></td>
								<td><?=$frec["turno"]?></td>
								<td><?=$frec["produccion"]?></td>
								<td><?=$frec["viajes"]?></td>
								<td align="center">
									<a href="javascript:abrirVentanaJavaScript('frecedi','500','500','<?=$CFG->wwwroot?>/opera/micros.php?mode=editar_frecuencia&id=<?=$frec["id"]?>')" class="link_verde" title="Actualizar">A</a>&nbsp;
									<a href="javascript:eliminar_frecuencia('<?=$frec["id"]?>')" class="link_verde" title="Borrar">B</a>&nbsp;
									<a href="<?=$CFG->wwwroot?>/opera/micros.php?mode=duplicar_frecuencia&id_frecuencia=<?=$frec["id"]?>&id_micro=<?=$_GET["id_micro"]?>" class="link_verde" title="Duplicar">DP</a>&nbsp;
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
		<td align="right" height=50>
			<table width="100%" border="0" cellspacing="0" cellpadding="3" >
				<tr>
					<td align="right">
						<input type="button" class="boton_verde_peq" value="Agregar" onClick="agregar()">&nbsp;
						<input type="button" class="boton_verde_peq" value="Cerrar" onClick="window.close()">&nbsp;
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<script>
function eliminar_frecuencia(idFrecuencia) {
	if (!confirm('¿Está seguro de querer borrar la frecuencia?')) 
		return;
	else
	{
		url = '<?=$CFG->wwwroot?>/opera/micros.php?mode=eliminar_frecuencia&id='+idFrecuencia;
		abrirVentanaJavaScript('borrarrutina','100','100',url);	
	}
}

function agregar()
{
	url = '<?=$CFG->wwwroot?>/opera/micros.php?mode=agregar_frecuencia&id_micro=<?=$_GET["id_micro"]?>';
	abrirVentanaJavaScript('frecedi','500','500',url);	
}


</script>
