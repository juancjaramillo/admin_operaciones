<?
include("../../application.php");
include($CFG->dirroot."/templates/header_popup.php");

$db->crear_select("SELECT id, tipo FROM bar.tipos_bolsas ORDER BY tipo",$tipo);

?>

<form name="entryform" action="<?=$CFG->wwwroot?>/opera/movimientos_bar.php" method="POST"  class="form" onSubmit="return revisar()">
<input type="hidden" name="mode" value="insertarBolsas">
<input type="hidden" name="id_movimiento" value="<?=$_GET["id_movimiento"]?>">

<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong>BOLSAS BARRIDO</strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td align='right'>(*) Tipo</td>
								<td align='left'><select  name="id_tipo_bolsa"><?=$tipo?></select></td>
							</tr>
							<tr>
								<td align='right'>(*) Iniciales</td>
								<td align='left'><input type='text' size='10' name='numero_inicio' class="casillatext" value=''></td>
							</tr>
							<tr>
								<td align='right'>Finales</td>
								<td align='left'><input type='text' size='10' name='numero_fin' class="casillatext" value=''></td>
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

function revisar()
{
	if(document.entryform.id_tipo_bolsa.options[document.entryform.id_tipo_bolsa.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Tipo');
		document.entryform.id_tipo_bolsa.focus();
		return(false);
	}
	if(document.entryform.numero_inicio.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Bolsas Iniciales');
		document.entryform.numero_inicio.focus();
		return(false);
	}
	else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.numero_inicio.value)){
			window.alert('[Bolsas Iniciales] no contiene un dato válido.');
			document.entryform.numero_inicio.focus();
			return(false);
		}
	}
	if(document.entryform.numero_fin.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.numero_fin.value)){
			window.alert('[Bolsas Finales] no contiene un dato válido.');
			document.entryform.numero_fin.focus();
			return(false);
		}
	}




	return(true);
}

</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>

