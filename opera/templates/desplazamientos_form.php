<?
include("../../application.php");
include($CFG->dirroot."/templates/header_popup.php");

$qidPrDes = $db->sql_row("SELECT * FROM rec.desplazamientos WHERE id_movimiento='".$_GET["id_movimiento"]."' AND hora_inicio IS NULL AND hora_fin IS NULL ORDER BY id LIMIT 1");

$db->crear_select("SELECT id, tipo FROM rec.tipos_desplazamientos ORDER BY tipo",$tipos,nvl($qidPrDes["id_tipo_desplazamiento"]));
$vehiculo = $db->sql_row("SELECT id, kilometraje as km, horometro as horo FROM vehiculos WHERE id=(SELECT id_vehiculo FROM rec.movimientos WHERE id='".$_GET["id_movimiento"]."')");

$desNumAnt = $db->sql_row("SELECT max(numero_viaje) as viajes FROM rec.desplazamientos WHERE id_movimiento=".$_GET["id_movimiento"]);
$numMov = $desNumAnt["viajes"];

$mov = $db->sql_row("SELECT i.codigo, v.codigo||'/'||v.placa AS vehiculo, inicio
		FROM rec.movimientos m 
		LEFT JOIN micros i ON i.id=m.id_micro 
		LEFT JOIN vehiculos v ON v.id=m.id_vehiculo 
		WHERE m.id='".$_GET["id_movimiento"]."'");

?>

<form name="entryform" action="<?=$CFG->wwwroot?>/opera/movimientos_rec.php" method="POST"  class="form" onSubmit="return revisar()">
<input type="hidden" name="mode" value="insertarDesplazamientoRec">
<input type="hidden" name="id_movimiento" value="<?=$_GET["id_movimiento"]?>">
<input type="hidden" name="id" value="<?=nvl($qidPrDes["id"])?>">

<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong>DESPLAZAMIENTO<br />(Micro : <?=$mov["codigo"]?> - Vehículo: <?=$mov["vehiculo"]?>)</strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td align='right' width="150">(*) Tipo</td>
								<td align='left'><select  name="id_tipo_desplazamiento"><?=$tipos?></select></td>
							</tr>
							<tr>
								<td align='right'>(*) Hora Inicio</td>
								<td align='left'>
									<input size="20" id="f_hora_inicio" class="casillatext_fecha" name='hora_inicio' value='<?=nvl($_GET["fecha"], date("Y-m-d"))." ".date("H:i:s")?>' /><button id="b_hora_inicio" onclick="javascript:showCalendarHora('f_hora_inicio','b_hora_inicio')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>Hora Fin</td>
								<td align='left'>
									<input size="20" id="f_hora_fin" class="casillatext_fecha" name='hora_fin' value='' /><button id="b_hora_fin" onclick="javascript:showCalendarHora('f_hora_fin','b_hora_fin')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>Número Viaje</td>
								<td align='left'><input type='text' size='4' class="casillatext" name='numero_viaje' value='<?=$numMov?>'></td>
							</tr>
							<tr>
								<td align='right'>Km</td>
								<td align='left'><input type='text' size='20' class="casillatext" name='km' value='<?=nvl(number_format($vehiculo["km"],0,"",""),0)?>'></td>
							</tr>
							<tr>
								<td align='right'>Horómetro</td>
								<td align='left'><input type='text' size='20' class="casillatext" name='horometro' value='<?=nvl(number_format($vehiculo["horo"],0,"",""),0)?>'></td>
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
<?include($CFG->dirroot."/templates/footer_popup.php");?>
<script type="text/javascript">
window.onload = document.entryform.km.focus();

function revisar()
{
	if(document.entryform.id_tipo_desplazamiento.options[document.entryform.id_tipo_desplazamiento.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Tipo');
		document.entryform.id_tipo_desplazamiento.focus();
		return(false);
	}
	if(document.entryform.hora_inicio.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Hora Inicio');
		document.entryform.hora_inicio.focus();
		return(false);
	}
	else{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.hora_inicio.value)){
			window.alert('[Hora Inicio] no contiene un dato válido.');
			document.entryform.hora_inicio.focus();
			return(false);
		}
		if(document.entryform.hora_inicio.value < '<?=$mov["inicio"]?>')
		{
			window.alert('[Hora Inicio] no puede ser menor que la de inicio del movimiento');
			document.entryform.hora_inicio.focus();
			return(false);
		}
	}
	if(document.entryform.hora_fin.value !=''){
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.hora_fin.value)){
			window.alert('[Hora Fin] no contiene un dato válido.');
			document.entryform.hora_fin.focus();
			return(false);
		}
		if(document.entryform.hora_inicio.value > document.entryform.hora_fin.value)
		{
			window.alert('[Hora Fin] no debe ser menor de la Hora Inicio.');
			document.entryform.hora_fin.focus();
			return(false);
		}
	}

	if(document.entryform.numero_viaje.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Número viaje');
		document.entryform.numero_viaje.focus();
		return(false);
	}
	else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.numero_viaje.value)){
			window.alert('[Número viaje] no contiene un dato válido.');
			document.entryform.numero_viaje.focus();
			return(false);
		}
	}
	if(document.entryform.km.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Km');
		document.entryform.km.focus();
		return(false);
	}
	else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.km.value)){
			window.alert('[Km] no contiene un dato válido.');
			document.entryform.km.focus();
			return(false);
		}
	}
	if(document.entryform.horometro.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Horómetro');
		document.entryform.horometro.focus();
		return(false);
	}
	else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.horometro.value)){
			window.alert('[Horómetro] no contiene un dato válido.');
			document.entryform.horometro.focus();
			return(false);
		}
	}

	return(true);
}

</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>

