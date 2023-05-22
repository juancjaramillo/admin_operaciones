<form name="entryform" action="<?=$ME?>" method="POST"  class="form" enctype="multipart/form-data" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id" value="<?=nvl($pm["id"])?>">
<?
$opciones = "";
$qidPeso = $db->sql_query($consultaPeso);
while($ps = $db->sql_fetchrow($qidPeso))
{
	$selected = "";
	if(isset($frm["id_peso"]) && $frm["id_peso"] == $ps["id"]) $selected = " selected";
	if(isset($pm["id_peso"]) && $pm["id_peso"] == $ps["id"]) $selected = " selected";

	$entra = true;
	if($ps["porc"] >= 100)
		$entra = false;
	if($newMode == "actualizar_peso_movimiento")
		if($ps["id"] == nvl($pm["id_peso"]))
			$entra = true;

	if($entra)
			$opciones.='<option value="'.$ps["id"].'" '.$selected.'>'.$ps["vehiculo"]." / ". $ps["peso"]. " (".$ps["porc"]."%) / ". $ps["fecha_entrada"]." / ". $ps["nombre"]." / ". $ps["centro"]." / ". $ps["tiquete"]."</option>\n";
}
?>
<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong><?=$titulo?> PESO MOVIMIENTO</strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td align='right'>Peso:<br/>Vehículo/Peso (suma %)/Fecha/Descargue/Centro/Tiquete.Entrada</td>
								<td align='left'>
									<div id='id_peso'>
										<select id='id_peso' name='id_peso' onChange="updateRecursive_id_movimiento(this)">
											<?=$opciones;?>
										</select>
									</div>
								</td>
							</tr>
							<tr>
								<td align='right'>Movimiento:<br />Inicio/Ruta/Vehículo</td>
								<td align='left'>
									<div id='id_movimiento'><select id='id_movimiento' name='id_movimiento' onChange="updateRecursive_id_peso(this), updateRecursive_viaje(this)"><?=$movimientos?></select></div>
								</td>
							</tr>
							<tr>
								<td align='right'>Porcentaje</td>
								<td align='left'><input type='text' size='10' class="casillatext" name='porcentaje' value='<?=nvl($pm["porcentaje"])?>'></td>
							</tr>
							<tr>
								<td align='right'>Viaje</td>
								<td align='left'>
									<div id='viaje'><select  name='viaje' id='viaje'><?=nvl($viajes)?></select></div>
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

var oXmlHttp_id_movimiento;
function updateRecursive_id_movimiento(select){
	namediv='id_movimiento';
	nameId='id_movimiento';
	id=select.options[select.selectedIndex].value;
	id_movimiento = document.entryform.id_movimiento.options[document.entryform.id_movimiento.selectedIndex].value;	
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateOpera.php?tipo=movimientosxpeso&id_peso=" + id + "&id_movimiento=" + id_movimiento  + "&divid=" + namediv;
	oXmlHttp_id_movimiento=GetHttpObject(cambiarRecursive_id_movimiento);
	oXmlHttp_id_movimiento.open("GET", url , true);
	oXmlHttp_id_movimiento.send(null);
}
function cambiarRecursive_id_movimiento(){
	if (oXmlHttp_id_movimiento.readyState==4 || oXmlHttp_id_movimiento.readyState=="complete"){
		document.getElementById('id_movimiento').innerHTML=oXmlHttp_id_movimiento.responseText
	}
}

var oXmlHttp_id_peso;
function updateRecursive_id_peso(select){
	namediv='id_peso';
	nameId='id_peso';
	id=select.options[select.selectedIndex].value;
	id_peso = document.entryform.id_peso.options[document.entryform.id_peso.selectedIndex].value;	
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateOpera.php?tipo=pesoxmovimiento&id_movimiento=" + id + "&id_peso=" + id_peso +"&divid=" + namediv;
	oXmlHttp_id_peso=GetHttpObject(cambiarRecursive_id_peso);
	oXmlHttp_id_peso.open("GET", url , true);
	oXmlHttp_id_peso.send(null);
}
function cambiarRecursive_id_peso(){
	if (oXmlHttp_id_peso.readyState==4 || oXmlHttp_id_peso.readyState=="complete"){
		document.getElementById('id_peso').innerHTML=oXmlHttp_id_peso.responseText
	}
}

var oXmlHttp_viaje;
function updateRecursive_viaje(select){
	namediv='viaje';
	nameId='viaje';
	id=select.options[select.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateOpera.php?tipo=viajexmovimiento&id_movimiento=" + id + "&divid=" + namediv;
	oXmlHttp_viaje=GetHttpObject(cambiarRecursive_viaje);
	oXmlHttp_viaje.open("GET", url , true);
	oXmlHttp_viaje.send(null);
}
function cambiarRecursive_viaje(){
	if (oXmlHttp_viaje.readyState==4 || oXmlHttp_viaje.readyState=="complete"){
		document.getElementById('viaje').innerHTML=oXmlHttp_viaje.responseText
	}
}




function revisar()
{
	if(document.entryform.id_peso.options[document.entryform.id_peso.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Peso');
		document.entryform.id_peso.focus();
		return(false);
	}
	if(document.entryform.id_movimiento.options[document.entryform.id_movimiento.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Mov: fecha/ruta/placa/codigo');
		document.entryform.id_movimiento.focus();
		return(false);
	}
	if(document.entryform.porcentaje.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Porcentaje');
		document.entryform.porcentaje.focus();
		return(false);
	}
	else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.porcentaje.value)){
			window.alert('[Porcentaje] no contiene un dato válido.');
			document.entryform.porcentaje.focus();
			return(false);
		}
		if(document.entryform.porcentaje.value > 100)
		{
		 	window.alert('[Porcentaje] no puede ser mayor a 100.');
			document.entryform.porcentaje.focus();
			return(false); 
	
		}
	}

	if(document.entryform.viaje.value.replace(/ /g, '') ==''){
		window.alert('Por favor seleccione: Viaje');
		document.entryform.viaje.focus();
		return(false);
	}
	else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.viaje.value)){
			window.alert('[Viaje] no contiene un dato válido.');
			document.entryform.viaje.focus();
			return(false);
		}
	}

	return(true);
}

</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>

