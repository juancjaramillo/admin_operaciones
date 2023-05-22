<?
include_once("../../application.php");
include($CFG->dirroot."/templates/header_popup.php");

$user=$_SESSION[$CFG->sesion]["user"];

#$condicion= "  AND id in (select id_rutina from mtto.rutinas_primera_vez 
#where id_equipo in (select id_equipo from mtto.equipos where id_centro in 
#						(SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."')))";

$condicion= "  AND mtto.rutinas.id in (select id_rutina from mtto.rutinas_centros where id_centro in  
						(SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."'))";

$consulta = "SELECT mtto.rutinas.id, rutina||' Frec. '|| CASE WHEN frec_km<>0 or frec_km=0 THEN cast(frec_km as char(6))||' kms' WHEN frec_horas<>0 THEN cast(frec_horas as char(6))||' hrs' 
     ELSE (select frecuencia from mtto.frecuencias where id in(id_frecuencia)) END as rutina 
FROM mtto.rutinas LEFT JOIN mtto.grupos ON mtto.rutinas.id_grupo=mtto.grupos.id 
WHERE activa AND id_tipo_mantenimiento=1 AND id_centro is not null AND   mtto.grupos.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."')
".$condicion." ORDER BY rutina";

$db->crear_select($consulta,$rutinasBusq,nvl($busq["id_rutina"]),"Cualquiera...");

?>
<table width="70%" align="Center">
	<tr>
		<td height="40" class="azul_16"><strong>BUSCAR ÓRDENES A IMPRIMIR AGRUPADAS</strong></td>
	</tr>
	<tr>
		<td>
			<form name="busq_form" action="<?=$CFG->wwwroot?>/mtto/listado_ultimo_prev.php" method="POST" onSubmit="return revisar()" class="form">
			<input type="hidden" name="mode" value="resultados">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840">
							<tr>
								<td>Rutina Preventiva:</td>
								<td><select  name='id_rutina'><?=$rutinasBusq?></select></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center" height="35" valign="bottom"><input type="button" class="boton_verde" value="Buscar" onclick="javascript:imprimir()"/></td>
	</tr>
</table>
</form>



<script type="text/javascript">
function imprimir()
{
	if(revisar())
		{
			document.busq_form.submit();
		}
}

function revisar()
{ 
	if(document.busq_form.id_rutina.options[document.busq_form.id_rutina.selectedIndex].value=='%')
	{
		window.alert('Seleccione los criterio de búsqueda');
		return(false);
	}

	return true;
}


</script>
