<?
include("../../application.php");
$user=$_SESSION[$CFG->sesion]["user"];
$qTipos=$db->sql_query("SELECT id, tipo as nombre FROM rec.tipos_desplazamientos ORDER BY orden, tipo");
$tiposOptions="<option value='%'>Seleccione...";
$i=0;
while($tipo=$db->sql_fetchrow($qTipos)){
	$i++;
	$tiposOptions.="<option value=\"" . $tipo["id"] . "\">" . $i . " - " . $tipo["nombre"];
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title><?=$CFG->siteTitle?> :: <?=nvl($CFG->pageTitle)?></title>
<style type="text/css">
body {
	margin:0;
	padding:0;
}

</style>
<link rel="stylesheet" type="text/css" href="http://64.131.77.126/pa/css/cfc.css" />
<script type="text/javascript">
var focused, inputs=document.getElementsByTagName('input');
var tabbableElements=0;
var revisando=false;
document.onkeydown = function(event) {
	var key_code = event.keyCode;
	if(key_code==13){//Enter
		return false;
	}
}

document.onkeyup = function(event) {
	var key_press = String.fromCharCode(event.keyCode);
	var key_code = event.keyCode;
	if(key_code==13 && revisando){//Enter
		revisando=!revisando;
		return false;
	}
	if(key_code==13 && !revisando){//Enter		
		currentTabIndex=document.activeElement.tabIndex;
		if(currentTabIndex==tabbableElements){
			if(revisar()) document.entryform.submit();
			return false;
		}
		else nextTab=currentTabIndex+1;
		for (var i=0, input; i<document.entryform.elements.length; i++) {
			input = document.entryform.elements[i];
			if(input.tabIndex==nextTab) input.focus();
		}
		return false;
	}
	else if(key_code==27){//ESC
		if(window.opener && window.opener.focus) window.opener.focus();
		window.close();
	}
}
function initialize(){
	document.entryform.codigo.focus();
	for (var i=0, input; i<document.entryform.elements.length; i++) {
		input = document.entryform.elements[i];
		if(input.type==='text' || input.type==='select-one'){
			tabbableElements++;
			input.tabIndex=tabbableElements;
		}
	}
}
function refreshMov(){
	var mygetrequest=new XMLHttpRequest();

	mygetrequest.onreadystatechange=function(){
		if (mygetrequest.readyState==4){
			if (mygetrequest.status==200 || window.location.href.indexOf("http")==-1){
				var jsondata=eval("("+mygetrequest.responseText+")"); //retrieve result as an JavaScript object
				if(jsondata.movements.length==0){
					window.alert('El móvil no tiene movimientos, por favor escriba otro.');
					revisando=true;
					document.entryform.codigo.value="";
					if(document.entryform.codigo.focus) document.entryform.codigo.focus();
					document.getElementById('ruta').innerHTML="";
				}
				else if(jsondata.movements.length==1){
					document.entryform.id_movimiento.value=jsondata.movements[0].id;
					document.getElementById('ruta').innerHTML=" (Micro " + jsondata.movements[0].code + ")";
					document.entryform.km.value=jsondata.movements[0].kilometraje;
					document.entryform.horometro.value=jsondata.movements[0].horometro;
				}
				else{
					var rutas="";
					for(i=0;i<jsondata.movements.length;i++){
						if(i==0) var defaultRoute=jsondata.movements[i].code;
						if(i!="0") rutas+=",";
						rutas+=jsondata.movements[i].code;
					}
					var respuesta=0;
					var strMsg="Indique la ruta";
					while(true){
						revisando=true;
						respuesta=window.prompt(strMsg + " (" + rutas + ")",defaultRoute);
						if(respuesta!=null){
							found=false;
							for(i=0;i<jsondata.movements.length;i++){
								if(jsondata.movements[i].code==respuesta){
									document.entryform.id_movimiento.value=jsondata.movements[i].id;
									document.getElementById('ruta').innerHTML=" (Micro " + jsondata.movements[i].code + ")";
									document.entryform.km.value=jsondata.movements[i].kilometraje;
									document.entryform.horometro.value=jsondata.movements[i].horometro;
									found=true;
								}
							}
							if(found)	break;
							strMsg="La ruta " + respuesta + " no existe.  Por favor escriba la ruta";
						}
					}
				}

//				output+='</ul>';
//				document.getElementById("movements").innerHTML=output;
			}
			else{
				alert("An error has occured making the request");
			}
		}
	}

	mygetrequest.open("GET", "<?=$CFG->wwwroot?>/ajax/getMovement.php?fecha=" + document.entryform.fecha.value + "&codigo=" + document.entryform.codigo.value, true);
	mygetrequest.send(null)
}
</script>
</head>
<body onload="initialize();">
<form name="entryform" action="<?=$CFG->wwwroot?>/opera/movimientos_rec.php" method="POST"  class="form" enctype="multipart/form-data" onSubmit="return revisar()">
<input type="hidden" name="mode" value="insertarDesplazamientoRec2">
<input type="hidden" name="id_movimiento" value="">
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
							<tr>
								<td align='right' width="30%">Fecha:</td>
								<td align='left'>
									<input type='text' size="10" id="f_fecha" class="casillatext_fecha" name='fecha' value='<?=nvl($_GET["fecha"],date("Y-m-d"))?>' onfocus="this.select()" onMouseUp="return false" /><button id="b_fecha" onclick="javascript:showCalendarSencillo('f_fecha','b_fecha')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>Vehículo:</td>
								<td align='left'>
									<input autocomplete="off" type='text' style="font-family: Arial; font-size: 20pt; background-color: #BDBDBD" size='6' name='codigo' value='<?=nvl($_GET["codigo"])?>'	onChange="refreshMov()" onfocus="this.select()" onMouseUp="return false">
									<span id="ruta"></span>
								</td>
							</tr>
							<tr>
								<td align='right'>Tipo:</td>
								<td align='left'>
									<select  name="id_tipo_desplazamiento"><?=$tiposOptions?></select>
								</td>
							</tr>
							<tr>
								<td align='right'>Hora inicio:</td>
								<td align='left'>
									<input autocomplete="off" type='text' style="font-family: Arial; font-size: 20pt; background-color: #BDBDBD; text-align: center" size='2' name='horas_inicio' value='<?=date("H")?>' onfocus="this.select()" onMouseUp="return false"> :
									<input autocomplete="off" type='text' style="font-family: Arial; font-size: 20pt; background-color: #BDBDBD; text-align: center" size='2' name='minutos_inicio' value='<?=date("i")?>' onfocus="this.select()" onMouseUp="return false">
								</td>
							</tr>
							<tr>
								<td align='right'>Hora fin:</td>
								<td align='left'>
									<input autocomplete="off" type='text' style="font-family: Arial; font-size: 20pt; background-color: #BDBDBD; text-align: center" size='2' name='horas_fin' onfocus="this.select()" onMouseUp="return false"> :
									<input autocomplete="off" type='text' style="font-family: Arial; font-size: 20pt; background-color: #BDBDBD; text-align: center" size='2' name='minutos_fin' onfocus="this.select()" onMouseUp="return false">
								</td>
							</tr>
							<tr>
								<td align='right'>Número de viaje:</td>
								<td align='left'>
									<input autocomplete="off" type='text' style="font-family: Arial; font-size: 20pt; background-color: #BDBDBD; text-align: center" size='2' name='numero_viaje' value='1' onfocus="this.select()" onMouseUp="return false">
								</td>
							</tr>
							<tr>
								<td align='right'>Km:</td>
								<td align='left'>
									<input autocomplete="off" type='text' style="font-family: Arial; font-size: 20pt; background-color: #BDBDBD; text-align: center" size='6' name='km' onfocus="this.select()" onMouseUp="return false">
								</td>
							</tr>
							<tr>
								<td align='right'>Horómetro:</td>
								<td align='left'>
									<input autocomplete="off" type='text' style="font-family: Arial; font-size: 20pt; background-color: #BDBDBD; text-align: center" size='4' name='horometro' onfocus="this.select()" onMouseUp="return false">
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
	if(document.entryform.fecha.value.replace(/ /g, '') !=''){
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2}/;
		if(!regexpression.test(document.entryform.fecha.value)){
			window.alert('[Fecha] no contiene un dato válido.');
			document.entryform.fecha.focus();
			revisando=true;
			return(false);
		}
	}
	if(document.entryform.id_movimiento.value==""){
		window.alert('No se especificó ningún movimiento.');
		document.entryform.codigo.focus();
		revisando=true;
		return(false);
	}
	if(document.entryform.id_tipo_desplazamiento.options[document.entryform.id_tipo_desplazamiento.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Tipo');
		document.entryform.id_tipo_desplazamiento.focus();
		revisando=true;
		return(false);
	}
	regexpression=/^([01][0-9])|(2[03])$/;
	if(!regexpression.test(document.entryform.horas_inicio.value)){
		window.alert('[Hora inicio] no contiene un dato válido.');
		document.entryform.horas_inicio.focus();
		revisando=true;
		return(false);
	}
	regexpression=/^[0-5][0-9]$/;
	if(!regexpression.test(document.entryform.minutos_inicio.value)){
		window.alert('[Minuto inicio] no contiene un dato válido.');
		document.entryform.minutos_inicio.focus();
		revisando=true;
		return(false);
	}
	if(document.entryform.horas_fin.value!="" || document.entryform.minutos_fin.value!=""){
		regexpression=/^([01][0-9])|(2[03])$/;
		if(!regexpression.test(document.entryform.horas_fin.value)){
			window.alert('[Hora fin] no contiene un dato válido.');
			document.entryform.horas_fin.focus();
			revisando=true;
			return(false);
		}
		regexpression=/^[0-5][0-9]$/;
		if(!regexpression.test(document.entryform.minutos_fin.value)){
			window.alert('[Minuto fin] no contiene un dato válido.');
			document.entryform.minutos_fin.focus();
			revisando=true;
			return(false);
		}
	}
	regexpression=/^[0-9]+$/;
	if(!regexpression.test(document.entryform.numero_viaje.value)){
		window.alert('[Número de viaje] no contiene un dato válido.');
		document.entryform.numero_viaje.focus();
		revisando=true;
		return(false);
	}
	if(!regexpression.test(document.entryform.km.value)){
		window.alert('[Kilometraje] no contiene un dato válido.');
		document.entryform.km.focus();
		revisando=true;
		return(false);
	}
	if(document.entryform.horometro.value!=""){
		if(!regexpression.test(document.entryform.horometro.value)){
			window.alert('[Horómetro] no contiene un dato válido.');
			document.entryform.horometro.focus();
			revisando=true;
			return(false);
		}
	}
	revisando=false;
	return(true);
}

</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>
