<?
include_once("../../application.php");
include($CFG->dirroot."/templates/header_popup.php");

$user=$_SESSION[$CFG->sesion]["user"];

?>
<table width="60%" align="center">
	<tr>
		<td height="40" class="azul_16"><strong>Programación Laboral de Técnicos  </strong></td>
	</tr>
	<tr>
		<td>
			<form name="busq_form" action="<?=$CFG->wwwroot?>/mtto/imp_prog_tec.php" method="POST" onSubmit="return revisar()" class="form">
			<input type="hidden" name="mode" value="resultados">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840">
							<tr>
								<td>Fechas de  Programación</td>
								<td>
									<input type='text' size="10" id="f_inicio_fecha_planeada" class="casillatext_fecha" name='inicio_fecha_planeada' value='<?=nvl($busq["inicio_fecha_planeada"])?>' readonly /><button id="b_inicio_fecha_planeada" onclick="javascript:showCalendarSencillo('f_inicio_fecha_planeada','b_inicio_fecha_planeada')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
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
	if(	document.busq_form.inicio_fecha_planeada.value.replace(/ /g, '') =='' )
	{
		window.alert('Seleccione los criterio de búsqueda');
		return(false);
	}

	return true;
}


</script>
