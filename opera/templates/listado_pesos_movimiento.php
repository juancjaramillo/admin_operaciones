<?
include("../../application.php");
include($CFG->dirroot."/templates/header_popup.php");

if(!isset($_SESSION[$CFG->sesion]["user"])){
	$errorMsg="No existe la sesión.";
	error_log($errorMsg);
	die($errorMsg);
}

$condicion = "true";
$link = "";
$linkCerrado = "";
$opcionesEdicion = true;

if(isset($_GET["id_peso"]))
{
	$condicion .= " AND mp.id_peso=".$_GET["id_peso"];
	$link .= "&id_peso=".$_GET["id_peso"];
	$agregar = $db->sql_row("SELECT sum(porcentaje) as suma FROM rec.movimientos_pesos WHERE id_peso=".$_GET["id_peso"]);
	if($agregar["suma"] >= 100)
		$link="";

	$cerrado = $db->sql_row("SELECT cerrado FROM rec.pesos WHERE id=".$_GET["id_peso"]);
	if($cerrado["cerrado"] == "t")
		$link="";
}elseif(isset($_GET["id_movimiento"]))
{
	$condicion .= " AND mp.id_movimiento=".$_GET["id_movimiento"];
	$link .= "&id_movimiento=".$_GET["id_movimiento"];

	$totalPesoMov = $db->sql_row("SELECT sum(porcentaje) as total FROM rec.movimientos_pesos WHERE id_movimiento=".$_GET["id_movimiento"]);
	if($totalPesoMov["total"]>=100)
		$link="";

	$cerrado = $db->sql_row("SELECT peso_cerrado FROM rec.movimientos WHERE id=".$_GET["id_movimiento"]);
	if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["cerrar_pesos"]))
	{
		if($cerrado["peso_cerrado"]=="f")
			$linkCerrado = '<a href="'.$CFG->wwwroot.'/opera/movimientos_rec.php?mode=cerrar_peso_movimiento&id_movimiento='.$_GET["id_movimiento"].'" class="link_verde" title="cerrarr peso">Cerrar Peso del Movimiento</a>&nbsp;&nbsp;&nbsp;';
	}
	if($cerrado["peso_cerrado"]=="t")
		$link = "";
}

$qid = $db->sql_query("SELECT mp.*,  v2.codigo||' ('|| v2.placa||') / '|| p.fecha_entrada ||' / '||l.nombre||' / '||c.centro as peso, mov.inicio||' / '||i.codigo||' / '|| v.codigo||' ('|| v.placa||')' as movimiento, mov.peso_cerrado
		FROM rec.movimientos_pesos mp
		LEFT JOIN rec.pesos p ON p.id=mp.id_peso
		LEFT JOIN vehiculos v2 ON v2.id = p.id_vehiculo
		LEFT JOIN lugares_descargue l ON l.id=p.id_lugar_descargue
		LEFT JOIN centros c ON c.id=l.id_centro
		LEFT JOIN rec.movimientos mov ON mov.id=mp.id_movimiento
		LEFT JOIN vehiculos v ON v.id = mov.id_vehiculo
		LEFT JOIN micros i ON i.id=mov.id_micro
		WHERE ".$condicion);


?>
<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong>MOVIMIENTOS / PESOS</strong></span></td>
	</tr>
	<tr>
		<td align="right"><?echo $linkCerrado; if($link != ""){?><a href="javascript:abrirVentanaJavaScript('pesos','1000','300','<?=$CFG->wwwroot?>/opera/movimientos_rec.php?mode=agregar_peso_movimiento<?=$link?>')" class="link_verde" title="Agregar">Agregar</a><?}?></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840">
							<tr>
								<td width="80%" align="center" class="casillatext">PESO: VEHÍCULO/FECHA/LUGAR/CENTRO</td>
								<td width="10%" align="center" class="casillatext">MOV: FECHA/RUTA/VEHÍCULO</td>
								<td width="10%" align="center" class="casillatext">PORCENTAJE</td>
								<td width="80%" align="center" class="casillatext">VIAJE</td>
								<td width="80%" align="center" class="casillatext">OPCIONES</td>
							</tr>
							<?
							while($act = $db->sql_fetchrow($qid)){?>
							<tr>
								<td><?=$act["peso"]?></td>
								<td><?=$act["movimiento"]?></td>
								<td><?=$act["porcentaje"]?></td>
								<td><?=$act["viaje"]?></td>
								<td align="center">
									<?if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["opciones_movimientos_pesos"])){
										if($act["peso_cerrado"] == "f"){?>
									<a href="javascript:abrirVentanaJavaScript('pesos','500','500','<?=$CFG->wwwroot?>/opera/movimientos_rec.php?mode=editar_peso_movimiento&id=<?=$act["id"]?>')" class="link_verde" title="Editar">E</a>&nbsp;
									<a href="javascript:eliminar_peso_mov('<?=$act["id"]?>')" class="link_verde" title="Borrar">B</a>&nbsp;
									<?}}?>
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

function eliminar_peso_mov(id)
{
	url = '<?=$CFG->wwwroot?>/opera/movimientos_rec.php?mode=eliminar_peso_movimiento&id='+id;
	abrirVentanaJavaScript('pesos','1','1',url);
}

</script>
<?include($CFG->templatedir . "/resize_window.php");?>
