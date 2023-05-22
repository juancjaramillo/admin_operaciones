<form name="entryform" action="<?=$ME?>" method="POST"  class="form" enctype="multipart/form-data" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id" value="<?=nvl($apoyo["id"])?>">
<input type="hidden" name="accion" value="">

<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong><?=$titulo?> APOYO</strong></span></td>
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
									<input size="20" id="f_inicio" class="casillatext_fecha" name='inicio' value='<?if($newMode == "actualizar_apoyo") echo $apoyo["inicio"]; else echo $apoyo["inicio"]." ".date("H:i:s")?>' onChange='updateRecursive_id_MovimientoxDia()' /><button id="b_inicio" onclick="javascript:showCalendarHoraConCambio('f_inicio','b_inicio')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>Vehículo</td>
								<td align='left'><select  name="id_vehiculo" onChange='updateRecursiveKmXVehiculo()'><?=$vehiculos?></select></td>
							</tr>
							<tr>
								<td align='right'>Ruta / Fecha Inicio</td>
								<td align='left'><div id="movimiento">
									<select multiple name="id_movimiento[]" id="movimientoin" style="width:150px" SIZE=5 >
										<?
										while($rutas = $db->sql_fetchrow($qidMov))
										{
											$selected = "";
											if(in_array($rutas["id"], $movimientos)) $selected = " selected";
											echo "<option value='".$rutas["id"]."' $selected>".$rutas["codigo"]."</option>";
										}
										?>
									</select></div>
								</td>
							</tr>
							<tr>
								<td align='right'>Peso Total</td>
								<td align='left'>
									<input type='text' size='20' class="casillatext" name='peso' value='<?=nvl($apoyo["peso"])?>'>
								</td>
							</tr>
							<tr>
								<td align='right'>Km Inicial</td>
								<td align='left'>
									<input type='text' size='20' class="casillatext" name='km_inicial' value='<?=nvl($apoyo["km_inicial"])?>'><div id="valxvehiculo"></div>
								</td>
							</tr>
							<tr>
								<td align='right'>Km Final</td>
								<td align='left'>
									<input type='text' size='20' class="casillatext" name='km_final' value='<?=nvl($apoyo["km_final"])?>'>
								</td>
							</tr>
							<tr>
								<td align='right'>Fecha Final</td>
								<td align='left'>
									<input size="20" id="f_final" class="casillatext_fecha" name='final' value='<?=nvl($apoyo["final"])?>' /><button id="b_final" onclick="javascript:showCalendarHora('f_final','b_final')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
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
			<input type="button" class="boton_verde" value="Guardar y cerrar" onclick="aceptarCerrarOr('cerrar')" />
			<input type="button" class="boton_verde" value="Guardar sin cerrar" onclick="aceptarCerrarOr('sincerrar')"/>
			<input type="button" class="boton_verde" value="Guardar e insertar otro apoyo" onclick="aceptarCerrarOr('otro')" />
			<input type="button" class="boton_verde" value="Cancelar" onclick="window.close()"/>
		</td>
	</tr>
	</form>
</table>
<script type="text/javascript">
function aceptarCerrarOr(accion)
{
	if(revisar())
	{
		document.entryform.accion.value = accion;
		document.entryform.submit();
	}
}

function revisar()
{
	if(document.entryform.inicio.value.replace(/ /g, '')  == '')
	{
		window.alert('Por favor escriba : Fecha inicio');
		document.entryform.inicio.focus();
		return false;
	}else{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.inicio.value)){
			window.alert('[Fecha inicio] no contiene un dato válido.');
			document.entryform.inicio.focus();
			return(false);
		}
	}

	if(document.entryform.id_vehiculo.options[document.entryform.id_vehiculo.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Vehículo');
		document.entryform.id_vehiculo.focus();
		return(false);
	}


	if(!multiselect_validate(document.getElementById('movimientoin'), document.getElementById('movimientoin').options.length  )){
		window.alert('Por favor seleccione: Ruta');
		return(false);
	}

	if(document.entryform.peso.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Peso');
		document.entryform.peso.focus();
		return(false);		
	}else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.peso.value)){
			window.alert('[Peso] no contiene un dato válido.');
			document.entryform.peso.focus();
			return(false);
		}
	}

	if(document.entryform.km_inicial.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.km_inicial.value)){
			window.alert('[Km Inicio] no contiene un dato válido.');
			document.entryform.km_inicial.focus();
			return(false);
		}
	}
	if(document.entryform.km_final.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.km_final.value)){
			window.alert('[Km Final] no contiene un dato válido.');
			document.entryform.km_final.focus();
			return(false);
		}
	}
	if(document.entryform.final.value !=''){
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.final.value)){
			window.alert('[Fecha Fin] no contiene un dato válido.');
			document.entryform.final.focus();
			return(false);
		}
	}

	return(true);
}


function multiselect_validate(select, num) {  

	if(!num) {  
		num = 1;  
	}  

	var found = 0;  
	for(var i = 0; i < select.options.length; i++) {  
		if(select.options[i].selected) {  
			found++;
		}  
	}  

	if(found != 0)
		return true;

	return false;  
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

var oXmlHttp_movimiento;
function updateRecursive_id_MovimientoxDia(){
	namediv='movimiento';
	nameId='movimiento';
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateOpera.php?tipo=id_MovimientoxDia&fecha=" + document.entryform.inicio.value + "&divid=" + namediv;
	oXmlHttp_movimiento=GetHttpObject(cambiarRecursive_movimiento);
	oXmlHttp_movimiento.open("GET", url , true);
	oXmlHttp_movimiento.send(null);
}
function cambiarRecursive_movimiento(){
	if (oXmlHttp_movimiento.readyState==4 || oXmlHttp_movimiento.readyState=="complete"){
		document.getElementById('movimiento').innerHTML=oXmlHttp_movimiento.responseText
	}
}

var oXmlHttp_valxvehiculo;
function updateRecursiveKmXVehiculo(){
	namediv='valxvehiculo';
	nameId='valxvehiculo';
	id_vehiculo = document.entryform.id_vehiculo.options[document.entryform.id_vehiculo.selectedIndex].value;
	document.getElementById(namediv).innerHTML='';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateOpera.php?tipo=KmXVehiculoNota&id_vehiculo=" + id_vehiculo + "&divid=" + namediv;
	oXmlHttp_valxvehiculo=GetHttpObject(cambiarRecursive_valxvehiculo);
	oXmlHttp_valxvehiculo.open("GET", url , true);
	oXmlHttp_valxvehiculo.send(null);
}
function cambiarRecursive_valxvehiculo(){
	if (oXmlHttp_valxvehiculo.readyState==4 || oXmlHttp_valxvehiculo.readyState=="complete"){
		document.getElementById('valxvehiculo').innerHTML=oXmlHttp_valxvehiculo.responseText
	}
}



function showCalendarHoraConCambio(casilla, boton)
{
	var cal = Calendar.setup({
		onSelect: function(cal) { cal.hide() },
		showTime: true,
		minuteStep:1,
		weekNumbers:true,
		inputField: casilla,
		trigger: boton,
		dateFormat:  "%Y-%m-%d %H:%M:%S",
		opacity:0,
		onChange : function() { updateRecursive_id_MovimientoxDia() }
	});
}



</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>

