<?
include("../../application.php");
include($CFG->dirroot."/templates/header_popup.php");

$mov = $db->sql_row("SELECT m.id , a.id_centro, m.inicio FROM rec.movimientos m LEFT JOIN micros i ON i.id=m.id_micro LEFT JOIN ases a ON a.id=i.id_ase WHERE m.id=".$_GET["id_movimiento"]);


$db->crear_select("SELECT p.id, p.nombre||' '||p.apellido || ' (' || COALESCE(p.cedula,'') || ')'
		FROM personas p 
		LEFT JOIN personas_cargos pc ON pc.id_persona=p.id
		LEFT JOIN estados_personas ep ON ep.id = p.id_estado
		WHERE ep.activo AND pc.id_cargo = 21 AND p.id IN (SELECT id_persona FROM personas_centros WHERE id_centro='$mov[id_centro]') AND p.id NOT IN (SELECT id_persona FROM rec.movimientos_personas LEFT JOIN rec.movimientos ON rec.movimientos.id=rec.movimientos_personas.id_movimiento WHERE rec.movimientos.inicio='".$mov["inicio"]."')
		ORDER BY p.nombre",$conductor);
$db->crear_select("SELECT p.id, p.nombre||' '||p.apellido || ' (' || COALESCE(p.cedula,'') || ')'
		FROM personas p 
		LEFT JOIN personas_cargos pc ON pc.id_persona=p.id
		LEFT JOIN estados_personas ep ON ep.id = p.id_estado
		WHERE ep.activo AND pc.id_cargo = 22 AND p.id IN (SELECT id_persona FROM personas_centros WHERE id_centro='$mov[id_centro]') AND p.id NOT IN (SELECT id_persona FROM rec.movimientos_personas LEFT JOIN rec.movimientos ON rec.movimientos.id=rec.movimientos_personas.id_movimiento WHERE rec.movimientos.inicio='".$mov["inicio"]."')
		ORDER BY p.nombre",$ayudantes);




?>

<form name="entryform" action="<?=$CFG->wwwroot?>/opera/movimientos_rec.php" method="POST"  class="form" onSubmit="return revisar()">
<input type="hidden" name="mode" value="insertarOperariosVarios">
<input type="hidden" name="esquema" value="rec">
<input type="hidden" name="id_movimiento" value="<?=$_GET["id_movimiento"]?>">

<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong>TRIPULACIÓN</strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr><td rowspan=4>Conductor</td></tr>
							<tr>
								<td align='right'>Persona</td>
								<td align='left'><select  name="conductor_id_persona"><?=$conductor?></select></td>
							</tr>
							<tr>
								<td align='right'>Hora Inicio</td>
								<td align='left'>
									<input size="20" id="f_conductor_hora_inicio" class="casillatext_fecha" name='conductor_hora_inicio' value='<?=date("Y-m-d H:i:s")?>' /><button id="b_conductor_hora_inicio" onclick="javascript:showCalendarHora('f_conductor_hora_inicio','b_conductor_hora_inicio')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>Hora Fin</td>
								<td align='left'>
									<input size="20" id="f_conductor_hora_fin" class="casillatext_fecha" name='conductor_hora_fin' value='' /><button id="b_conductor_hora_fin" onclick="javascript:showCalendarHora('f_conductor_hora_fin','b_conductor_hora_fin')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr><td rowspan=4>Ayudante 1</td></tr>
							<tr>
								<td align='right'>Persona</td>
								<td align='left'><select  name="ayudante1_id_persona"><?=$ayudantes?></select></td>
							</tr>
							<tr>
								<td align='right'>Hora Inicio</td>
								<td align='left'>
									<input size="20" id="f_ayudante1_hora_inicio" class="casillatext_fecha" name='ayudante1_hora_inicio' value='<?=date("Y-m-d H:i:s")?>' /><button id="b_ayudante1_hora_inicio" onclick="javascript:showCalendarHora('f_ayudante1_hora_inicio','b_ayudante1_hora_inicio')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>Hora Fin</td>
								<td align='left'>
									<input size="20" id="f_ayudante1_hora_fin" class="casillatext_fecha" name='ayudante1_hora_fin' value='' /><button id="b_ayudante1_hora_fin" 	onclick="javascript:showCalendarHora('f_ayudante1_hora_fin','b_ayudante1_hora_fin')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr><td rowspan=4>Ayudante 2</td></tr>
							<tr>
								<td align='right'>Persona</td>
								<td align='left'><select  name="ayudante2_id_persona"><?=$ayudantes?></select></td>
							</tr>
							<tr>
								<td align='right'>Hora Inicio</td>
								<td align='left'>
									<input size="20" id="f_ayudante2_hora_inicio" class="casillatext_fecha" name='ayudante2_hora_inicio' value='<?=date("Y-m-d H:i:s")?>' /><button id="b_ayudante2_hora_inicio" 		onclick="javascript:showCalendarHora('f_ayudante2_hora_inicio','b_ayudante2_hora_inicio')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>Hora Fin</td>
								<td align='left'>
									<input size="20" id="f_ayudante2_hora_fin" class="casillatext_fecha" name='ayudante2_hora_fin' value='' /><button id="b_ayudante2_hora_fin" 		onclick="javascript:showCalendarHora('f_ayudante2_hora_fin','b_ayudante2_hora_fin')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr><td rowspan=4>Ayudante 3</td></tr>
							<tr>
								<td align='right'>Persona</td>
								<td align='left'><select  name="ayudante3_id_persona"><?=$ayudantes?></select></td>
							</tr>
							<tr>
								<td align='right'>Hora Inicio</td>
								<td align='left'>
									<input size="20" id="f_ayudante3_hora_inicio" class="casillatext_fecha" name='ayudante3_hora_inicio' value='<?=date("Y-m-d H:i:s")?>' /><button id="b_ayudante3_hora_inicio" 		onclick="javascript:showCalendarHora('f_ayudante3_hora_inicio','b_ayudante3_hora_inicio')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>Hora Fin</td>
								<td align='left'>
									<input size="20" id="f_ayudante3_hora_fin" class="casillatext_fecha" name='ayudante3_hora_fin' value='' /><button id="b_ayudante3_hora_fin" 		onclick="javascript:showCalendarHora('f_ayudante3_hora_fin','b_ayudante3_hora_fin')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
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
	if(document.entryform.conductor_id_persona.options[document.entryform.conductor_id_persona.selectedIndex].value!='%'){
		if(document.entryform.conductor_hora_inicio.value.replace(/ /g, '') ==''){
			window.alert('Por favor escriba: Hora Inicio Conductor');
			document.entryform.conductor_hora_inicio.focus();
			return(false);
		}
	}

	if(document.entryform.ayudante1_id_persona.options[document.entryform.ayudante1_id_persona.selectedIndex].value!='%'){
		if(document.entryform.ayudante1_hora_inicio.value.replace(/ /g, '') ==''){
			window.alert('Por favor escriba: Hora Inicio Ayudante 1');
			document.entryform.ayudante1_hora_inicio.focus();
			return(false);
		}
	}

	if(document.entryform.ayudante2_id_persona.options[document.entryform.ayudante2_id_persona.selectedIndex].value!='%'){
		if(document.entryform.ayudante2_hora_inicio.value.replace(/ /g, '') ==''){
			window.alert('Por favor escriba: Hora Inicio Ayudante 2');
			document.entryform.ayudante2_hora_inicio.focus();
			return(false);
		}
	}

	if(document.entryform.ayudante3_id_persona.options[document.entryform.ayudante3_id_persona.selectedIndex].value!='%'){
		if(document.entryform.ayudante3_hora_inicio.value.replace(/ /g, '') ==''){
			window.alert('Por favor escriba: Hora Inicio Ayudante 3');
			document.entryform.ayudante3_hora_inicio.focus();
			return(false);
		}
	}

	if(document.entryform.conductor_hora_inicio.value.replace(/ /g, '') !=''){
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.conductor_hora_inicio.value)){
			window.alert('[Hora Inicio Conductor] no contiene un dato válido.');
			document.entryform.conductor_hora_inicio.focus();
			return(false);
		}
	}

	if(document.entryform.conductor_hora_fin.value.replace(/ /g, '') !=''){
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.conductor_hora_fin.value)){
			window.alert('[Hora Fin Conductor] no contiene un dato válido.');
			document.entryform.conductor_hora_fin.focus();
			return(false);
		}
	}

	if(document.entryform.ayudante1_hora_inicio.value.replace(/ /g, '') !=''){
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.ayudante1_hora_inicio.value)){
			window.alert('[Hora Inicio Ayudante 1] no contiene un dato válido.');
			document.entryform.ayudante1_hora_inicio.focus();
			return(false);
		}
	}

	if(document.entryform.ayudante1_hora_fin.value.replace(/ /g, '') !=''){
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.ayudante1_hora_fin.value)){
			window.alert('[Hora Fin Ayudante 1] no contiene un dato válido.');
			document.entryform.ayudante1_hora_fin.focus();
			return(false);
		}
	}

	if(document.entryform.ayudante2_hora_inicio.value.replace(/ /g, '') !=''){
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.ayudante2_hora_inicio.value)){
			window.alert('[Hora Inicio Ayudante 2] no contiene un dato válido.');
			document.entryform.ayudante2_hora_inicio.focus();
			return(false);
		}
	}

	if(document.entryform.ayudante2_hora_fin.value.replace(/ /g, '') !=''){
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.ayudante2_hora_fin.value)){
			window.alert('[Hora Fin Ayudante 2] no contiene un dato válido.');
			document.entryform.ayudante2_hora_fin.focus();
			return(false);
		}
	}

	if(document.entryform.ayudante3_hora_inicio.value.replace(/ /g, '') !=''){
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.ayudante3_hora_inicio.value)){
			window.alert('[Hora Inicio Ayudante 3] no contiene un dato válido.');
			document.entryform.ayudante3_hora_inicio.focus();
			return(false);
		}
	}

	if(document.entryform.ayudante3_hora_fin.value.replace(/ /g, '') !=''){
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.ayudante3_hora_fin.value)){
			window.alert('[Hora Fin Ayudante 3] no contiene un dato válido.');
			document.entryform.ayudante3_hora_fin.focus();
			return(false);
		}
	}


	return(true);
}

</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>

