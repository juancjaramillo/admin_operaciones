<?
include("../../application.php");
include($CFG->dirroot."/templates/header_popup.php");

$mov = $db->sql_row("SELECT m.id , a.id_centro, m.inicio FROM bar.movimientos m LEFT JOIN micros i ON i.id=m.id_micro LEFT JOIN ases a ON a.id=i.id_ase WHERE m.id=".$_GET["id_movimiento"]);

$db->crear_select("SELECT p.id, p.nombre||' '||p.apellido 
		FROM personas p 
		LEFT JOIN personas_cargos pc ON pc.id_persona=p.id
		WHERE pc.id_cargo = 23 AND p.id IN (SELECT id_persona FROM personas_centros WHERE id_centro='$mov[id_centro]') AND p.id NOT IN (SELECT id_persona FROM bar.movimientos_personas LEFT JOIN bar.movimientos ON bar.movimientos.id=bar.movimientos_personas.id_movimiento WHERE bar.movimientos.inicio='".$mov["inicio"]."')
		ORDER BY p.nombre",$operario);

?>

<form name="entryform" action="<?=$CFG->wwwroot?>/opera/movimientos_bar.php" method="POST"  class="form" onSubmit="return revisar()">
<input type="hidden" name="mode" value="insertarOperariosVarios">
<input type="hidden" name="esquema" value="bar">
<input type="hidden" name="id_movimiento" value="<?=$_GET["id_movimiento"]?>">

<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong>OPERARIO BARRIDO</strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td align='right'>Persona</td>
								<td align='left'><select  name="id_persona"><?=$operario?></select></td>
							</tr>
							<tr>
								<td align='right'>Hora Inicio</td>
								<td align='left'>
									<input size="20" id="f_hora_inicio" class="casillatext_fecha" name='hora_inicio' value='<?=date("Y-m-d H:i:s")?>' /><button id="b_hora_inicio" onclick="javascript:showCalendarHora('f_hora_inicio','b_hora_inicio')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>Hora Fin</td>
								<td align='left'>
									<input size="20" id="f_hora_fin" class="casillatext_fecha" name='hora_fin' value='' /><button id="b_hora_fin" onclick="javascript:showCalendarHora('f_hora_fin','b_hora_fin')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
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
	if(document.entryform.id_persona.options[document.entryform.id_persona.selectedIndex].value=='%'){
		window.alert('Por favor escoja la persona');
		document.entryform.id_persona.focus();
		return(false);
	}
	
	if(document.entryform.hora_inicio.value.replace(/ /g, '')  == '')
	{
		window.alert('Por favor escriba : Fecha Inicio');
		document.entryform.hora_inicio.focus();
		return false;
	}else{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.hora_inicio.value)){
			window.alert('[Fecha Inicio] no contiene un dato válido.');
			document.entryform.hora_inicio.focus();
			return(false);
		}
	}

	if(document.entryform.hora_fin.value.replace(/ /g, '')  != '')
	{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.hora_fin.value)){
			window.alert('[Fecha fin] no contiene un dato válido.');
			document.entryform.hora_fin.focus();
			return(false);
		}

		if(document.entryform.hora_fin.value < document.entryform.hora_inicio.value)
		{
			window.alert('La fecha final no puede ser menor que la fecha de inicio');
			return(false);
		}
	}

	return(true);
}

</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>

