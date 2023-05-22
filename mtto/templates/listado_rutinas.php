<?
include_once("../../application.php");
include($CFG->dirroot."/templates/header_popup.php");

if(!isset($_SESSION[$CFG->sesion]["user"])){
	$errorMsg="No existe la sesión.";
	error_log($errorMsg);
	die($errorMsg);
}

verificarPagina(simple_me($ME));

$user=$_SESSION[$CFG->sesion]["user"];
$condicion=" activa AND mtto.rutinas.id IN (SELECT id_rutina FROM mtto.rutinas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."'))";

if(isset($_GET["tipo"]))
	$condicion.=" AND mtto.tipos.id=1";
else
	$condicion.=" AND mtto.tipos.id!=1";

if(isset($_GET["inactiva"]))
	$condicion=" NOT activa AND mtto.rutinas.id IN (SELECT id_rutina FROM mtto.rutinas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."'))";

$idsGrupos[] = $_GET["id_grupo"];
obtenerIdsGrupos($_GET["id_grupo"],$idsGrupos);

$qid = "SELECT mtto.rutinas.id, mtto.rutinas.rutina, mtto.sistemas.sistema AS sistema, CASE WHEN mtto.rutinas.lugar='Interno' THEN 'Interno' WHEN mtto.rutinas.lugar='Externo' THEN 'Externo' END AS lugar, tipo AS tipo_mantenimiento, prioridad AS prioridad, f.frecuencia, frec_horas, frec_km
	FROM mtto.rutinas
	LEFT JOIN mtto.sistemas ON mtto.rutinas.id_sistema=mtto.sistemas.id
	LEFT JOIN mtto.tipos ON mtto.rutinas.id_tipo_mantenimiento=mtto.tipos.id
	LEFT JOIN mtto.prioridades ON mtto.rutinas.id_prioridad=mtto.prioridades.id
	LEFT JOIN mtto.frecuencias f ON f.id=mtto.rutinas.id_frecuencia
	WHERE ".$condicion." AND id_grupo IN (".implode(",",$idsGrupos).")
	ORDER BY mtto.rutinas.rutina";
$qid = $db->sql_query($qid);


?>

<table width="100%">
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840">
							<tr>
								<td width="80%" align="center" class="casillatext">NOMBRE</td>
								<td width="10%" align="center" class="casillatext">SISTEMA</td>
								<td width="10%" align="center" class="casillatext">FRECUENCIAS</td>
								<td width="80%" align="center" class="casillatext">LUGAR</td>
								<td width="10%" align="center" class="casillatext">TIPO</td>
								<td width="10%" align="center" class="casillatext">PRIORIDAD</td>
								<td width="10%" align="center" class="casillatext">OPCIONES</td>
							</tr>
							<?
							while($act = $db->sql_fetchrow($qid)){?>
							<tr>
								<td><?=$act["rutina"]?></td>
								<td><?=$act["sistema"]?></td>
								<td>
								<?
								if($act["frecuencia"] != "") echo $act["frecuencia"]."/";
								if($act["frec_horas"] != "") echo "Horas: ".$act["frec_horas"]."/";
								if($act["frec_km"] != "") echo "Kms: ".$act["frec_km"];
								?>
								</td>
								<td><?=$act["lugar"]?></td>
								<td><?=$act["tipo_mantenimiento"]?></td>
								<td><?=$act["prioridad"]?></td>
								<td align="center">
									<?if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["rutinas_opciones"])){?>
									<a href="javascript:abrirVentanaJavaScript('rutinafo','1100','500','<?=$CFG->wwwroot?>/mtto/rutinas.php?mode=editar&id=<?=$act["id"]?>')" class="link_verde" title="Actualizar">A</a>&nbsp;
									<a href="<?=$CFG->wwwroot?>/mtto/rutinas.php?mode=doblar_rutina&id_rutina=<?=$act["id"]?>&id_grupo=<?=$_GET["id_grupo"]?>" class="link_verde" title="Duplicar">DP</a>&nbsp;
									<a href="javascript:eliminar_rutina('<?=$act["id"]?>')" class="link_verde" title="Borrar">B</a>&nbsp;
									<?}?>
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
function eliminar_rutina(idRutina) {
	if (!confirm('¿Está seguro de querer borrar la rutina?')) 
		return;
	else
	{
		url = '<?=$CFG->wwwroot?>/mtto/rutinas.php?mode=eliminar&id='+idRutina;
		abrirVentanaJavaScript('borrarrutina','200','200',url);	
	}
}

</script>
