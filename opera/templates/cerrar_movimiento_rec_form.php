<?
include("../../application.php");
$user=$_SESSION[$CFG->sesion]["user"];

$db->crear_select("SELECT id, codigo||'/'||placa FROM vehiculos WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]') AND id IN (SELECT id_vehiculo FROM rec.movimientos WHERE final IS NULL) ORDER BY codigo,placa",$vehiculos, nvl($_GET["id_vehiculo"]));

include($CFG->dirroot."/templates/header_popup.php");

?>
<form name="entryform" action="<?=$CFG->wwwroot?>/opera/movimientos_rec.php" method="POST"  class="form" enctype="multipart/form-data" onSubmit="return revisar()">
<input type="hidden" name="mode" value="cerrar_movimiento_rec_desde_busq">
<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong>CERRAR MOVIMIENTO</strong></span></td>
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
								<td align='right'>Fecha Inicio</td>
								<td align='left'>
									<input type='text' size="20" id="f_inicio" class="casillatext_fecha" name='inicio' value='<?=nvl($_GET["fecha"],date("Y-m-d"))?>' /><button id="b_inicio" onclick="javascript:showCalendarSencillo('f_inicio','b_inicio')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>Fecha Fin</td>
								<td align='left'>
									<input type='text' size="20" id="f_final" class="casillatext_fecha" name='final' value='<?=nvl($_GET["fecha"],date("Y-m-d"))." ".date("H:i:s")?>' /><button id="b_final" onclick="javascript:showCalendarHora('f_final','b_final')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>Vehículo</td>
								<td align='left'> <select  name='id_vehiculo' onChange="updateRecursive_kilometraje(), updateRecursive_horometro()"><?=$vehiculos?></select> </td>
							</tr>
							<tr>
								<td align='right'>Km</td>
								<td align='left'>
									<div id="kilometraje"><input type='text' size='20' class="casillatext" name='kilometraje' id="kilometraje" value='<?=nvl($kmHoro["kilometraje"])?>'></div>
								</td>
							</tr>
							<tr>
								<td align='right'>Horómetro</td>
								<td align='left'>
									<div id="horometro"><input type='text' size='20' class="casillatext" name='horometro' id="horometro" value='<?=nvl($kmHoro["horometro"])?>'></div>
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
	if(document.entryform.inicio.value.replace(/ /g, '')  == '')
	{
		window.alert('Por favor escriba : Fecha Inicio');
		document.entryform.inicio.focus();
		return false;
	}else{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2}/;
		if(!regexpression.test(document.entryform.inicio.value)){
			window.alert('[Fecha Inicio] no contiene un dato válido.');
			document.entryform.inicio.focus();
			return(false);
		}
	}

	if(document.entryform.final.value.replace(/ /g, '')  == '')
	{
		window.alert('Por favor escriba : Fecha Fin');
		document.entryform.final.focus();
		return false;
	}else{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.final.value)){
			window.alert('[Fecha Fin] no contiene un dato válido.');
			document.entryform.final.focus();
			return(false);
		}
	}

	if(document.entryform.id_vehiculo.options[document.entryform.id_vehiculo.selectedIndex].value=='%'){
		window.alert('Por favor escoja el vehículo');
		document.entryform.id_vehiculo.focus();
		return(false);
	}

	if(document.entryform.kilometraje.value.replace(/ /g, '')  == ''){
		window.alert('Por favor escriba : Km');
		document.entryform.kilometraje.focus();
		return false;
	}else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.kilometraje.value)){
			window.alert('[Km] no contiene un dato válido.');
			document.entryform.kilometraje.focus();
			return(false);
		}
	}

	if(document.entryform.horometro.value.replace(/ /g, '')  == ''){
		window.alert('Por favor escriba : Horómetro');
		document.entryform.horometro.focus();
		return false;
	}else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.horometro.value)){
			window.alert('[Horómetro] no contiene un dato válido.');
			document.entryform.horometro.focus();
			return(false);
		}
	}



	return(true);
}

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

var oXmlHttp_kilometraje;
function updateRecursive_kilometraje(){
	namediv='kilometraje';
	nameId='kilometraje';
	id_vehiculo = document.entryform.id_vehiculo.options[document.entryform.id_vehiculo.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<input type="text" size="20" class="casillatext" name="' + nameId + '" id="' + nameId + '" value="Actualizando...">';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateOpera.php?tipo=kmXVehiculo&id_vehiculo=" + id_vehiculo ;
	oXmlHttp_kilometraje=GetHttpObject(cambiarRecursive_kilometraje);
	oXmlHttp_kilometraje.open("GET", url , true);
	oXmlHttp_kilometraje.send(null);
}
function cambiarRecursive_kilometraje(){
	if (oXmlHttp_kilometraje.readyState==4 || oXmlHttp_kilometraje.readyState=="complete"){
		document.getElementById('kilometraje').innerHTML=oXmlHttp_kilometraje.responseText
	}
}

var oXmlHttp_horometro;
function updateRecursive_horometro(){
	namediv='horometro';
	nameId='horometro';
	id_vehiculo = document.entryform.id_vehiculo.options[document.entryform.id_vehiculo.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<input type="text" size="20" class="casillatext" name="' + nameId + '" id="' + nameId + '" value="Actualizando...">';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateOpera.php?tipo=horoXVehiculo&id_vehiculo=" + id_vehiculo ;
	oXmlHttp_horometro=GetHttpObject(cambiarRecursive_horometro);
	oXmlHttp_horometro.open("GET", url , true);
	oXmlHttp_horometro.send(null);
}
function cambiarRecursive_horometro(){
	if (oXmlHttp_horometro.readyState==4 || oXmlHttp_horometro.readyState=="complete"){
		document.getElementById('horometro').innerHTML=oXmlHttp_horometro.responseText
	}
}

</script>
<?
include($CFG->templatedir . "/resize_window.php");
?>
