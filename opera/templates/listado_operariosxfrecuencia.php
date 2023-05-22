<?
include_once("../../application.php");
include($CFG->dirroot."/templates/header_popup.php");

$qid = $db->sql_query("SELECT fo.id,p.cedula, c.nombre as cargo, p.nombre||' '||p.apellido as operario
		FROM frecuencias_operarios fo
		LEFT JOIN cargos c ON c.id=fo.id_cargo
		LEFT JOIN personas p ON p.id=fo.id_persona
		WHERE fo.id_frecuencia=".$_GET["id_frecuencia"]);

$esquema = $db->sql_row("SELECT s.esquema
		FROM servicios s
		LEFT JOIN micros m ON s.id=m.id_servicio
		LEFT JOIN micros_frecuencia mf ON m.id=mf.id_micro
		WHERE mf.id=".$_GET["id_frecuencia"]);

?>
<table width="100%">
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" id="tabla_actividades">
							<tr>
								<td align='left' colspan=3><span class="azul_12">OPERARIOS PREDETERMINADOS</span></td>
							</tr>
							<tr>
								<td width="20%" align="center" class="casillatext">CEDULA</td>
								<td width="20%" align="center" class="casillatext">CARGO</td>
								<td width="50%" align="center" class="casillatext">OPERARIO</td>
								<td width="10%" align="center" class="casillatext">OPCIONES</td>
							</tr>
							<?
							while($car = $db->sql_fetchrow($qid)){?>
							<tr>
							  <td><?=$car["cedula"]?></td>
								<td><?=$car["cargo"]?></td>
								<td align="left"><?=$car["operario"]?></td>
								<td align="left"><a href="javascript:eliminar_cargo('<?=$car["id"]?>')" class="link_verde" title="Borrar">B</a></td>
							</tr>
							<?}?>
						</table>
					</td>
				</tr>
				<?
				$agregar = false;
				if($esquema["esquema"] == "bar" && $db->sql_numrows($qid) < 1)
					$agregar=true;
				elseif($esquema["esquema"] == "rec" && $db->sql_numrows($qid) < 4)
					$agregar=true;
				
				if($agregar){?>
				<tr>
					<td align="center"><a href="javascript:abrirVentanaJavaScript('cargos','600','250','<?=$CFG->wwwroot?>/opera/micros.php?mode=agregar_operario&id_frecuencia=<?=$_GET["id_frecuencia"]?>')" class="link_verde">+ Agregar Operario +</a> </td>
				</tr>
				<?}?>
			</table>
		</td>
	</tr>
</table>
<script  type="text/javascript">
function eliminar_cargo(id)
{
	if(confirm("¿Está seguro de borrar el operario?"))
	{
		url = "<?=$CFG->wwwroot?>/opera/micros.php?mode=eliminar_operario&id="+id;
		abrirVentanaJavaScript('cargos','100','100',url);
	}
}

</script>

