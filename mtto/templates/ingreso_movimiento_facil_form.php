<?
include("../../application.php");
$condicion="";

$user=$_SESSION[$CFG->sesion]["user"];
$condicion=" AND id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')";

$db->crear_select("SELECT id, codigo||'/'||placa FROM vehiculos WHERE true ".$condicion." ORDER BY codigo,placa",$vehiculos,nvl($llanta["id_vehiculo"]));

include($CFG->dirroot."/templates/header_popup.php");

?>


<form name="entryform" action="<?=$CFG->wwwroot?>/mtto/llantas.php" method="POST"  class="form" onSubmit="return revisar()">
<input type="hidden" name="mode" value="ingreso_movimiento_facil">
<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong>BUSCAR LLANTA</strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<?if(isset($error)){?>
							<tr>
								<td align='center' colspan=2><i>No se encontró ninguna llanta con los datos ingresados.</i></td>
							</tr>
							<?}?>
							<tr>
								<td align='right' width="30%">Número</td>
								<td align='left' width="70%"><input type='text' size='40'  name='numero' class='casillatext' value='<?=nvl($llanta["numero"])?>'></td>
							</tr>
							<tr>
								<td align='right'>Vehículo</td>
								<td align='left'> <select  name='id_vehiculo'><?=$vehiculos?></select> </td>
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
	if(document.entryform.numero.value.replace(/ /g, '') =='' && document.entryform.id_vehiculo.options[document.entryform.id_vehiculo.selectedIndex].value=='%' && ){
		window.alert('Por favor seleccione algún criterio de búsqueda');
		return(false);
	}

	if(document.entryform.numero.value.replace(/ /g, '') !=''){
		var regexpression=/^.{1,1055}$/m;
		if(!regexpression.test(document.entryform.numero.value)){
			window.alert('[Número] no contiene un dato válido.');
			document.entryform.numero.focus();
			return(false);
		}
	}


	return(true);
}


</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>

