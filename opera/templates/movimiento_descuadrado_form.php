<?
include("../../application.php");
include($CFG->dirroot."/templates/header_popup.php");

$user=$_SESSION[$CFG->sesion]["user"];

$consulta = "SELECT f.id, m.codigo||' / '||case when f.dia=1 then 'Lunes' when f.dia=2 then 'Martes' when f.dia=3 then 'Miércoles' when f.dia=4 then 'Jueves' when f.dia=5 then 'Viernes' when f.dia=6 then 'Sábado' else 'Domingo' end as dia
	FROM micros_frecuencia f
	LEFT JOIN micros m ON m.id=f.id_micro
	LEFT JOIN servicios s ON s.id = m.id_servicio
	LEFT JOIN ases a ON a.id=m.id_ase
	WHERE s.esquema='".$_GET["esquema"]."' AND m.fecha_hasta IS NULL AND m.id_ase IN (SELECT id FROM ases WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')) 
	ORDER BY m.codigo, f.dia";
$db->crear_select($consulta,$micros);

$db->crear_select("SELECT t.id, centro||' / '||t.turno 
		FROM turnos t 
		LEFT JOIN centros c ON c.id_empresa = t.id_empresa
		WHERE c.id in (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')
		ORDER BY centro,t.turno",$turnos);

?>



<form name="entryform" action="<?=$CFG->wwwroot?>/opera/movimientos_<?=$_GET["esquema"]?>.php" method="POST"  class="form" enctype="multipart/form-data" onSubmit="return revisar()">
<input type="hidden" name="mode" value="agregar_movimiento_descuadrado">
<input type="hidden" name="esquema" value="<?=$_GET["esquema"]?>">

<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong>AGREGAR MOVIMIENTO</strong></span></td>
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
									<input type='text' size='10' class="casillatext_fecha" name='inicio' value='<?=$_GET["fecha"]?>' readonly>
								</td>
							</tr>
							<tr>
								<td align='right'>Turno</td>
								<td align='left'><select  name="id_turno" id="id_turno" onChange="updateRecursive_id_micro_frecuencia(this)"><?=$turnos?></select></td>
							</tr>
							<tr>
								<td align='right'>Ruta</td>
								<?if($_GET["esquema"] == "rec"){?>
								<td align='left'><div id="id_micro_frecuencia"><select  name="id_micro_frecuencia" id="id_micro_frecuencia" style="width:150px" onChange="updateRecursive_id_vehiculo(this), updateRecursive_id_lugar_descargue(this), updateRecursive_hora(this)"><?=$micros?></select></div></td>
								<?}else{?>
								<td align='left'><div id="id_micro_frecuencia"><select  name="id_micro_frecuencia" id="id_micro_frecuencia" style="width:150px" onChange="updateRecursive_id_vehiculo(this), updateRecursive_id_persona(this), updateRecursive_hora(this), updateRecursive_bolsas(this)"><?=$micros?></select></div></td>
								<?}?>
							</tr>
							<tr>
								<td align='right'>Hora Inicio</td>
								<td align='left'>
									<div id="hora"><input type='text' size='10' class="casillatext_fecha" name='hora' id="hora" value=''>&nbsp;<a title="Calendario" href="javascript:abrirSoloHora('hora','entryform');"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-clock.png' border='0'></a></div>
								</td>
							</tr>
							<?if($_GET["esquema"] == "bar"){?>
							<tr>
								<td align='right'>Operario</td>
								<td align='left'><div id="id_persona"><select  name="id_persona" id="id_persona" style="width:250px"><option value='%'>Seleccione...</option></select></div></td>
							</tr>
							<?}?>
							<tr>
								<td align='right'>Vehículo</td>
								<td align='left'><div id="id_vehiculo"><select  name="id_vehiculo" id="id_vehiculo" style="width:150px"><option value='%'>Seleccione...</option></select></div></td>
							</tr>
							<?if($_GET["esquema"] == "bar"){?>
							<tr>
								<td align='right'>Bolsas</td><td align='left'><div id="bolsas"></div></td>
							</tr>
							<?}?>
							<?if($_GET["esquema"] == "rec"){?>
							<tr>
								<td align='right'>Lugar Descargue</td>
								<td align='left'><div id="id_lugar_descargue"><select  name="id_lugar_descargue" id="id_lugar_descargue"><option value='%'>Seleccione...</option></select></div></td>
							</tr>
							<tr>
								<td align='right'>Peso Inicio</td>
								<td align='left'>
									<input type='text' size='20' class="casillatext" name='peso_inicial' id='peso_inicial' value=''>
								</td>
							</tr>
							<?}?>
							<tr>
								<td align='right'>No. Orden</td>
								<td align='left'>
									<input type='text' size='20' class="casillatext" name='numero_orden' id='numero_orden' value=''>
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

function GetHttpObject(handler){
	try
	{
		var oRequester = new ActiveXObject("Microsoft.XMLHTTP");
		oRequester.onreadystatechange=handler;
		return oRequester;
	}
	catch (error){
		try{
			var oRequester = new XMLHttpRequest();
			oRequester.onload=handler;
			oRequester.onerror=handler;
			return oRequester;
		} 
		catch (error){
			return false;
		}
	}
} 

var oXmlHttp_id_micro_frecuencia;
function updateRecursive_id_micro_frecuencia(select){
	namediv='id_micro_frecuencia';
	nameId='id_micro_frecuencia';
	id=select.options[select.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateOpera.php?tipo=listadoMicrosXTurno&id_turno=" + id + "&esquema=<?=$_GET["esquema"]?>&user=<?=$user["id"]?>&divid=" + namediv;
	oXmlHttp_id_micro_frecuencia=GetHttpObject(cambiarRecursive_id_micro_frecuencia);
	oXmlHttp_id_micro_frecuencia.open("GET", url , true);
	oXmlHttp_id_micro_frecuencia.send(null);
}
function cambiarRecursive_id_micro_frecuencia(){
	if (oXmlHttp_id_micro_frecuencia.readyState==4 || oXmlHttp_id_micro_frecuencia.readyState=="complete"){
		document.getElementById('id_micro_frecuencia').innerHTML=oXmlHttp_id_micro_frecuencia.responseText
	}
}

var oXmlHttp_id_vehiculo;
function updateRecursive_id_vehiculo(select){
	namediv='id_vehiculo';
	nameId='id_vehiculo';
	id=select.options[select.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateOpera.php?tipo=listadoVehiculosXFrecuencia&id_frecuencia=" + id + "&fecha=<?=$_GET["fecha"]?>&divid=" + namediv;
	oXmlHttp_id_vehiculo=GetHttpObject(cambiarRecursive_id_vehiculo);
	oXmlHttp_id_vehiculo.open("GET", url , true);
	oXmlHttp_id_vehiculo.send(null);
}
function cambiarRecursive_id_vehiculo(){
	if (oXmlHttp_id_vehiculo.readyState==4 || oXmlHttp_id_vehiculo.readyState=="complete"){
		document.getElementById('id_vehiculo').innerHTML=oXmlHttp_id_vehiculo.responseText
	}
}

var oXmlHttp_id_lugar_descargue;
function updateRecursive_id_lugar_descargue(select){
	namediv='id_lugar_descargue';
	nameId='id_lugar_descargue';
	id=select.options[select.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateOpera.php?tipo=listadoDescarguesXFrecuencia&id_frecuencia=" + id + "&divid=" + namediv;
	oXmlHttp_id_lugar_descargue=GetHttpObject(cambiarRecursive_id_lugar_descargue);
	oXmlHttp_id_lugar_descargue.open("GET", url , true);
	oXmlHttp_id_lugar_descargue.send(null);
}
function cambiarRecursive_id_lugar_descargue(){
	if (oXmlHttp_id_lugar_descargue.readyState==4 || oXmlHttp_id_lugar_descargue.readyState=="complete"){
		document.getElementById('id_lugar_descargue').innerHTML=oXmlHttp_id_lugar_descargue.responseText
	}
}

var oXmlHttp_hora;
function updateRecursive_hora(select){
	namediv='hora';
	nameId='hora';
	id=select.options[select.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<input type="text" size="20" class="casillatext_fecha" name="hora" id="hora" value="Actualizando">';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateOpera.php?tipo=HoraXFrecuencia&id_frecuencia=" + id + "&divid=" + namediv;
	oXmlHttp_hora=GetHttpObject(cambiarRecursive_hora);
	oXmlHttp_hora.open("GET", url , true);
	oXmlHttp_hora.send(null);
}
function cambiarRecursive_hora(){
	if (oXmlHttp_hora.readyState==4 || oXmlHttp_hora.readyState=="complete"){
		document.getElementById('hora').innerHTML=oXmlHttp_hora.responseText
	}
}


var oXmlHttp_id_persona;
function updateRecursive_id_persona(select){
	namediv='id_persona';
	nameId='id_persona';
	id=select.options[select.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateOpera.php?tipo=listadoPersonasXFrecuencia&id_frecuencia=" + id + "&fecha=<?=$_GET["fecha"]?>&divid=" + namediv;
	oXmlHttp_id_persona=GetHttpObject(cambiarRecursive_id_persona);
	oXmlHttp_id_persona.open("GET", url , true);
	oXmlHttp_id_persona.send(null);
}
function cambiarRecursive_id_persona(){
	if (oXmlHttp_id_persona.readyState==4 || oXmlHttp_id_persona.readyState=="complete"){
		document.getElementById('id_persona').innerHTML=oXmlHttp_id_persona.responseText
	}
}

var oXmlHttp_bolsas;
function updateRecursive_bolsas(select){
	namediv='bolsas';
	nameId='bolsas';
	id=select.options[select.selectedIndex].value;
	document.getElementById(namediv).innerHTML='Actualizando bolsas...';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateOpera.php?tipo=listadoBolsasXFrecuencia&id_frecuencia=" + id + "&divid=" + namediv;
	oXmlHttp_bolsas=GetHttpObject(cambiarRecursive_bolsas);
	oXmlHttp_bolsas.open("GET", url , true);
	oXmlHttp_bolsas.send(null);
}
function cambiarRecursive_bolsas(){
	if (oXmlHttp_bolsas.readyState==4 || oXmlHttp_bolsas.readyState=="complete"){
		document.getElementById('bolsas').innerHTML=oXmlHttp_bolsas.responseText
	}
}

function revisar()
{
	if(document.entryform.id_micro_frecuencia.options[document.entryform.id_micro_frecuencia.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Ruta');
		document.entryform.id_micro_frecuencia.focus();
		return(false);
	}

	if(document.entryform.hora.value.replace(/ /g, '')  == '')
	{
		window.alert('Por favor escriba : Hora Inicio');
		document.entryform.hora.focus();
		return false;
	}else{
		var regexpression=/^[0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.hora.value)){
			window.alert('[Hora Inicio] no contiene un dato válido.');
			document.entryform.hora.focus();
			return(false);
		}
	}

	<?if($_GET["esquema"] == "rec"){?>
	if(document.entryform.id_vehiculo.options[document.entryform.id_vehiculo.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Vehículo');
		document.entryform.id_vehiculo.focus();
		return(false);
	}
	
	if(document.entryform.id_lugar_descargue.options[document.entryform.id_lugar_descargue.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Lugar Descargue');
		document.entryform.id_lugar_descargue.focus();
		return(false);
	}

	if(document.entryform.peso_inicial.value.replace(/ /g, '') != ''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.peso_inicial.value)){
			window.alert('[Peso Inicial] no contiene un dato válido.');
			document.entryform.peso_inicial.focus();
			return(false);
		}
	}

	<?}?>

	return(true);
}

</script>

<?
include($CFG->templatedir . "/resize_window.php");
include($CFG->dirroot."/templates/footer_popup.php");

?>

