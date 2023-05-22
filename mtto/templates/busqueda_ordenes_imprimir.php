<?
include_once("../../application.php");
include($CFG->dirroot."/templates/header_popup.php");

$user=$_SESSION[$CFG->sesion]["user"];

$db->crear_select("SELECT id, tipo FROM mtto.tipos ORDER BY tipo",$tipos);
$db->crear_select("SELECT id, sistema FROM mtto.sistemas ORDER BY sistema",$sistemas);

$condicion = $condicionRut= $condicionPers = "true";
if($user["nivel_acceso"]!=1)
{
	$condicion = "id_centro IS NULL OR id_centro IN (".implode(",",$user["id_centro"]).")";
	$condicionRut=" id IN (SELECT id_rutina FROM mtto.rutinas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."'))";
	$condicionPers = "id IN (SELECT id_persona FROM personas_centros WHERE id_centro IN (".implode(",",$user["id_centro"])."))";
}
$condicion = "id_centro IS NULL OR id_centro IN (".implode(",",$user["id_centro"]).")";
#	$condicionRut=" id IN (SELECT id_rutina FROM mtto.rutinas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."'))";

$db->crear_select("SELECT id, rutina FROM mtto.rutinas WHERE activa AND ".$condicionRut." ORDER BY rutina",$rutinasBusq,nvl($busq["id_rutina"]),"Cualquiera...");
$db->crear_select("SELECT id, nombre FROM mtto.equipos WHERE ".$condicion." ORDER BY  nombre",$equiposBusq,nvl($busq["id_equipo"]),"Cualquiera...");
$db->build_recursive_tree_path("mtto.motivos",$motivosBusq,nvl($busq["id_motivo"]),"id","id_superior","mtto.motivos.nombre");
$db->crear_select("SELECT id, nombre||' '||apellido as nombre FROM personas WHERE ".$condicionPers." ORDER BY nombre,apellido",$responsableBusq,nvl($busq["id_responsable"]),"Cualquiera...");
$db->crear_select("SELECT id, nombre||' '||apellido as nombre FROM personas WHERE ".$condicionPers." ORDER BY nombre,apellido",$creadorBusq,nvl($busq["id_creador"]),"Cualquiera...");
$db->crear_select("SELECT id, nombre||' '||apellido as nombre FROM personas WHERE ".$condicionPers." ORDER BY nombre,apellido",$planeadorBusq,nvl($busq["id_planeador"]),"Cualquiera...");
$db->crear_select("SELECT id, estado FROM mtto.estados_ordenes_trabajo ORDER BY estado",$estadoBusq,nvl($busq["id_estado_orden_trabajo"]),"Cualquiera...");



?>
<table width="70%" align="center">
	<tr>
		<td height="40" class="azul_16"><strong>BUSCAR ÓRDENES A IMPRIMIR AGRUPADAS</strong></td>
	</tr>
	<tr>
		<td>
			<form name="busq_form" action="<?=$CFG->wwwroot?>/mtto/imp_ordenes_grupo.php" method="POST" onSubmit="return revisar()" class="form">
			<input type="hidden" name="mode" value="resultados">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840">
							<tr>
								<td>Equipo</td>
								<td><select  name='id_equipo'><?=$equiposBusq?></select></td>
							</tr>
							<tr>
								<td>Fecha Planeada</td>
								<td>
									<input type='text' size="10" id="f_inicio_fecha_planeada" class="casillatext_fecha" name='inicio_fecha_planeada' value='<?=nvl($busq["inicio_fecha_planeada"])?>' readonly /><button id="b_inicio_fecha_planeada" onclick="javascript:showCalendarSencillo('f_inicio_fecha_planeada','b_inicio_fecha_planeada')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
									 a <input type='text' size="10" id="f_fin_fecha_planeada" class="casillatext_fecha" name='fin_fecha_planeada' value='<?=nvl($busq["fin_fecha_planeada"])?>' readonly /><button id="b_fin_fecha_planeada" onclick="javascript:showCalendarSencillo('f_fin_fecha_planeada','b_fin_fecha_planeada')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center" height="35" valign="bottom"><input type="button" class="boton_verde" value="Imprimir" onclick="javascript:imprimir()"/></td>
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
	if(document.busq_form.id_equipo.options[document.busq_form.id_equipo.selectedIndex].value=='%' ||
		document.busq_form.inicio_fecha_planeada.value.replace(/ /g, '') =='' ||
		document.busq_form.fin_fecha_planeada.value.replace(/ /g, '') =='')
	{
		window.alert('Seleccione los criterio de búsqueda');
		return(false);
	}

	if(document.busq_form.inicio_fecha_planeada.value > document.busq_form.fin_fecha_planeada.value){
		window.alert('[Fecha Fin Planeada] Debe ser Mayor que Fecha Inicio Planeadas');
		document.busq_form.fin_fecha_planeada.focus();
		return(false);
	}

	return true;
}


</script>
