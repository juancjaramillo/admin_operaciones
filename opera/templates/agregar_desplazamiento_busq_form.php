<?
include("../../application.php");
$user=$_SESSION[$CFG->sesion]["user"];

include($CFG->dirroot."/templates/header_popup.php");
?>
<form name="entryform" action="<?=$CFG->wwwroot?>/opera/movimientos_rec.php" method="POST"  class="form" enctype="multipart/form-data" onSubmit="return revisar()">
<input type="hidden" name="mode" value="agregar_desplazamiento_desde_busq">
<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong>AGREGAR DESPLAZAMIENTO</strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<?if(isset($_GET["error"])){?>
							<tr>
								<td align='center' colspan=2><i>No existe un movimiento con los datos dados</i></td>
							</tr>
							<?}?>
							<tr>
								<td align='right'>Fecha</td>
								<td align='left'>
									<input type='text' size="10" id="f_fecha" class="casillatext_fecha" name='fecha' value='<?=nvl($_GET["fecha"],date("Y-m-d"))?>' /><button id="b_fecha" onclick="javascript:showCalendarSencillo('f_fecha','b_fecha')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>Vehículo</td>
								<td align='left'> <input type='text' size='8' class="casillatext" name='codigo' value='<?=nvl($_GET["codigo"])?>'> </td>
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
	if(document.entryform.fecha.value.replace(/ /g, '') =='' && document.entryform.codigo.value.replace(/ /g, '') ==''){
		window.alert('Por favor seleccione algún criterio de búsqueda');
		return(false);
	}


	if(document.entryform.fecha.value.replace(/ /g, '') !=''){
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2}/;
		if(!regexpression.test(document.entryform.fecha.value)){
			window.alert('[Fecha] no contiene un dato válido.');
			document.entryform.fecha.focus();
			return(false);
		}
	}



	return(true);
}

</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>
