<?
include("../../application.php");
include($CFG->dirroot."/templates/header_popup.php");

$mov = $db->sql_row("SELECT m.inicio, m.final, m.id_vehiculo, i.codigo, kilometraje as km, horometro as horo
	FROM ".$_GET["esquema"].".movimientos m 
	LEFT JOIN micros i ON i.id=m.id_micro 
	LEFT JOIN vehiculos v ON v.id=m.id_vehiculo
	WHERE m.id=".$_GET["id_movimiento"]);

?>
<form name="entryform" action="<?=$CFG->wwwroot?>/opera/movimientos_<?=$_GET["esquema"]?>.php" method="get"  class="form" enctype="multipart/form-data" onSubmit="return revisar()">
<input type="hidden" name="mode" value="cerrarMovimiento">
<input type="hidden" name="id_movimiento" value="<?=nvl($mov["id"])?>">
<input type="hidden" name="esquema" value="<?=$_GET["esquema"]?>">
<input type="hidden" name="fecha" value="<?=date("Y-m-d H:i:s")?>">

<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong>CERRAR MOVIMIENTO RUTA <?=$mov["codigo"]?></strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td align='right'>Fecha Inicio</td>
								<td align='left'>
									<input type='text' size='20' class="casillatext_fecha" name='inicio' value='<?=nvl($mov["inicio"])?>' readonly >
								</td>
							</tr>
							<tr>
								<td align='right'>Km</td>
								<td align='left'>
									<input type='text' size='20' class="casillatext" name='km' value='<?=nvl(str_replace(",","",number_format($mov["km"])))?>'>
								</td>
							</tr>
							<tr>
								<td align='right'>Horómetro</td>
								<td align='left'>
									<input type='text' size='20' class="casillatext" name='horo' value='<?=nvl(str_replace(",","",number_format($mov["horo"])))?>'>
								</td>
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
<script type="text/javascript">

function revisar()
{
	<?if($_GET["esquema"] == "rec"){?>
	if(document.entryform.km.value.replace(/ /g, '')  == ''){
		window.alert('Por favor escriba : Km');
		document.entryform.km.focus();
		return false;
	}else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.km.value)){
			window.alert('[Km] no contiene un dato válido.');
			document.entryform.km.focus();
			return(false);
		}
	}

	if(document.entryform.horo.value.replace(/ /g, '')  == ''){
		window.alert('Por favor escriba : Horómetro');
		document.entryform.horo.focus();
		return false;
	}else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.horo.value)){
			window.alert('[Horómetro] no contiene un dato válido.');
			document.entryform.horo.focus();
			return(false);
		}
	}
	<?}?>

	return(true);
}

</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>

