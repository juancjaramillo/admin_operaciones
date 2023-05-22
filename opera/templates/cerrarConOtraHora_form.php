<?
include("../../application.php");
include($CFG->dirroot."/templates/header_popup.php");

$des = $db->sql_row("SELECT hora_inicio FROM rec.desplazamientos WHERE id=".$_GET["id_desplazamiento"]);

?>

<form name="entryform" action="<?=$CFG->wwwroot?>/opera/movimientos_rec.php" method="POST"  class="form" onSubmit="return revisar()">
<input type="hidden" name="mode" value="cerrarDesplazamientoOtraHora">
<input type="hidden" name="id_desplazamiento" value="<?=$_GET["id_desplazamiento"]?>">

<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong>CERRAR DESPLAZAMIENTO</strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td align='right'>Hora Fin</td>
								<td align='left'>
									<input size="20" id="f_hora_fin" class="casillatext_fecha" name='hora_fin' value='<?=date("Y-m-d H:i:s")?>' /><button id="b_hora_fin" onclick="javascript:showCalendarHora('f_hora_fin','b_hora_fin')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
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
<?include($CFG->dirroot."/templates/footer_popup.php");?>
<script type="text/javascript">

function revisar()
{
	if(document.entryform.hora_fin.value ==''){
		window.alert('Por favor seleccione la hora final.');
		document.entryform.hora_fin.focus();
		return(false);
	}else{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.hora_fin.value)){
			window.alert('[Hora Fin] no contiene un dato válido.');
			document.entryform.hora_fin.focus();
			return(false);
		}
	}

	if('<?=$des["hora_inicio"]?>' > document.entryform.hora_fin.value)
	{
		window.alert('[Hora Fin] no debe ser menor de la Hora Inicio.');
		document.entryform.hora_fin.focus();
		return(false);
	}


	return(true);
}

</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>

