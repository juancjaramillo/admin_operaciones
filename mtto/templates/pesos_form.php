<?


$cons = "SELECT vehiculos.id, vehiculos.codigo||' / '||vehiculos.placa as nombre
		FROM vehiculos
		WHERE id_estado<>4 and  id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$_SESSION[$CFG->sesion]["user"]["id"]."')
		ORDER BY codigo,placa";
$db->crear_select($cons,$vehiculos);


?>
<script type="text/javascript">
	
	

</script>

<form name="entryform" action="<?=$ME?>" method="POST" enctype="multipart/form-data" onSubmit="return revisar()">
<input type="hidden" name="module" value="rec.pesos">
<input type="hidden" name="mode" value="insert">

<table width="100%">
	<tr>
		<td height="40" class="azul_16"><strong><?=strtoupper($entidad->labelModule)?></strong>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840">
							<tr bgcolor='#ffffff'>
								<td align='right'  width="30%">(*) Vehículo : </td>
								<td bgcolor="#ffffff" >
									<select  name='id_vehiculo'   onChange="updateRecursive_id_lugar_descargue(this), updateRecursiveVariosMovimientosSinPeso(this), updateRecursiveUltimoMovimientoSinPeso(this)"><?=$vehiculos?></select>
								</td>
							</tr>
							<tr bgcolor='#ffffff'>
								<td align='right'  >Peso Inicial : </td>
								<td bgcolor="#ffffff"><input type='text' size='20'  name='peso_inicial' value=''  ></td>
							</tr>
							<tr bgcolor='#ffffff'>
								<td align='right'  >Peso Final : </td>
								<td bgcolor="#ffffff"><input type='text' size='20'  name='peso_final' value=''  ></td>
							</tr>
							<tr bgcolor='#ffffff'>
								<td align='right'  >Peso Total : </td>
								<td bgcolor="#ffffff"><input type='text' size='20'  name='peso_total' value=''  ></td>
							</tr>
							<tr bgcolor='#ffffff'>
								<td align='right'  >(*) Lugar Descargue : </td>
								<td bgcolor="#ffffff">
									<div id="id_lugar_descargue"><select  name="id_lugar_descargue" id="id_lugar_descargue" style="width:250px" ><option value="%">Seleccione...</option></select></div> 
								</td>
							</tr>
							<tr bgcolor='#ffffff'>
								<td align='right'  >Tiquete Entrada : </td>
								<td bgcolor="#ffffff"><input type='text' size='20'  name='tiquete_entrada' value=''  ></td>
							</tr>
							<tr bgcolor='#ffffff'>
								<td align='right'  >Tiquete Salida : </td>
								<td bgcolor="#ffffff"><input type='text' size='20'  name='tiquete_salida' value=''  ></td>
							</tr>
							<tr bgcolor='#ffffff'>
								<td align='right'  >(*) Fecha Entrada : </td>
								<td bgcolor="#ffffff"><input type='text' size='20' style='text-align:center;'  name='fecha_entrada' value='<?=date("Y-m-d H:i:s")?>'  id='f_fecha_entrada'>&nbsp;<button id="b_fecha_entrada" onclick="javascript:showCalendarHora('f_fecha_entrada','b_fecha_entrada')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button></td>
							</tr>
							<tr bgcolor='#ffffff'>
								<td align='right'  >(*) Fecha Salida : </td>
								<td bgcolor="#ffffff"><input type='text' size='20' style='text-align:center;'  name='fecha_salida' value='<?=date("Y-m-d H:i:s")?>'  id='f_fecha_salida'>&nbsp;<button id="b_fecha_salida" onclick="javascript:showCalendarHora('f_fecha_salida','b_fecha_salida')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button></td>
							</tr>
							<tr bgcolor='#ffffff'>
								<td align='right'  >(*) Reparte : </td>
								<td bgcolor="#ffffff"><label for='reparte_t'>Sí</label>&nbsp;<input type='radio' id='reparte_t' name='reparte' value='t'  onclick="verMovimientos('t')" >&nbsp;<label for='reparte_f'>No</label>&nbsp;<input type='radio' id='reparte_f' name='reparte' value='f'  CHECKED onclick="verMovimientos('f')">&nbsp;</td>
							</tr>
						</table>
						<div id="fillVariosMovimientos" style="display:none;visibility:hidden">
							<table  width="100%" border=1 bordercolor="#7fa840">
								<tr bgcolor='#ffffff'>
									<td align='right' width="30%">Movimientos / Inicio:<br />(movimientos sin peso)</td>
									<td bgcolor="#ffffff">
										<div id="id_movimientos"><select  name="id_movimientos" id="id_movimientos" style="width:250px" ><option value="%">Seleccione...</option></select></div> </td>
								</tr>
							</table>
						</div>
						<div id="fillUnicoMovimientos" style="">
							<table  width="100%" border=1 bordercolor="#7fa840">
								<tr bgcolor='#ffffff'>
									<td align='right' width="30%">Último Movimiento / Inicio:<br />(movimiento sin peso)</td>
									<td bgcolor="#ffffff">
										<div id="id_unico_movimiento"><select  name="id_unico_movimiento" id="id_unico_movimiento" style="width:250px" ><option value="%">Seleccione...</option></select></div> </td>
								</tr>
							</table>
						</div>

					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="3">
				<tr>
					<td height=50>
						<input type="Submit" class="boton_verde_peq" value="Aceptar">
						<input type="button" class="boton_verde_peq" value="Cancelar" onClick="window.opener.focus();window.close();">
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>

<?include($CFG->templatedir . "/resize_window.php");?>

<script type="text/javascript">

function verMovimientos(valor)
{
	estilo = document.getElementById("fillVariosMovimientos");
	estiloDos = document.getElementById("fillUnicoMovimientos");
	if(valor=="t"){
		estilo.style.display=''
		estilo.style.visibility='';
		estiloDos.style.display='none'
		estiloDos.style.visibility='hidden';
	}
	else{
		estilo.style.display='none'
		estilo.style.visibility='hidden';
		estiloDos.style.display=''
		estiloDos.style.visibility='';
	}
}

function revisar(){
	if(document.entryform.id_vehiculo.options[document.entryform.id_vehiculo.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Vehículo');
		document.entryform.id_vehiculo.focus();
		return(false);
	}
	if(document.entryform.peso_inicial.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.peso_inicial.value)){
			window.alert('[Peso Inicial] no contiene un dato válido.');
			document.entryform.peso_inicial.focus();
			return(false);
		}
	}
	if(document.entryform.peso_final.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.peso_final.value)){
			window.alert('[Peso Final] no contiene un dato válido.');
			document.entryform.peso_final.focus();
			return(false);
		}
	}
	if(document.entryform.peso_total.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.peso_total.value)){
			window.alert('[Peso Total] no contiene un dato válido.');
			document.entryform.peso_total.focus();
			return(false);
		}
	}
	if(document.entryform.id_lugar_descargue.options[document.entryform.id_lugar_descargue.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Lugar Descargue');
		document.entryform.id_lugar_descargue.focus();
		return(false);
	}
	if(document.entryform.tiquete_entrada.value !=''){
		var regexpression=/^.{1,16}$/m;
		if(!regexpression.test(document.entryform.tiquete_entrada.value)){
			window.alert('[Tiquete Entrada] no contiene un dato válido.');
			document.entryform.tiquete_entrada.focus();
			return(false);
		}
	}
	if(document.entryform.tiquete_salida.value !=''){
		var regexpression=/^.{1,16}$/m;
		if(!regexpression.test(document.entryform.tiquete_salida.value)){
			window.alert('[Tiquete Salida] no contiene un dato válido.');
			document.entryform.tiquete_salida.focus();
			return(false);
		}
	}
	if(document.entryform.fecha_entrada.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Fecha Entrada');
		document.entryform.fecha_entrada.focus();
		return(false);
	}
	else{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2}/;
		if(!regexpression.test(document.entryform.fecha_entrada.value)){
			window.alert('[Fecha Entrada] no contiene un dato válido.');
			document.entryform.fecha_entrada.focus();
			return(false);
		}
	}
	if(document.entryform.fecha_salida.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Fecha Salida');
		document.entryform.fecha_salida.focus();
		return(false);
	}
	else{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2}/;
		if(!regexpression.test(document.entryform.fecha_salida.value)){
			window.alert('[Fecha Salida] no contiene un dato válido.');
			document.entryform.fecha_salida.focus();
			return(false);
		}
	}

	var seleccionadas=0;
	for(i=0;document.entryform.reparte[i]!=undefined;i++){
		if(document.entryform.reparte[i].checked) seleccionadas++;
	}
	if(seleccionadas==0){
		window.alert('Por favor seleccione una opción para: Reparte');
		return(false);
	}
						
	if(document.entryform.peso_inicial.value.replace(/ /g, '') =='' && document.entryform.peso_final.value.replace(/ /g, '') =='' && document.entryform.peso_total.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba el peso inicial y final o el peso total');
		document.entryform.peso_inicial.focus();
		return(false);
	}

	if(document.entryform.peso_inicial.value.replace(/ /g, '') !='' && document.entryform.peso_final.value.replace(/ /g, '') == '' ){
		window.alert('Por favor escriba el peso inicial y final');
		document.entryform.peso_final.focus();
		return(false);
	}

	if(document.entryform.peso_inicial.value.replace(/ /g, '') =='' && document.entryform.peso_final.value.replace(/ /g, '') != '' ){
		window.alert('Por favor escriba el peso inicial y final');
		document.entryform.peso_inicial.focus();
		return(false);
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

var oXmlHttp_id_lugar_descargue;
function updateRecursive_id_lugar_descargue(select){
	namediv='id_lugar_descargue';
	nameId='id_lugar_descargue';
	id=select.options[select.selectedIndex].value;
	width=document.getElementById(nameId).style.width;
	consulta='SELECT id, nombre FROM lugares_descargue WHERE id_vehiculo=\'' + id + '\'';
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '" style="width:' + document.getElementById(nameId).style.width + '"><option>Actualizando...<\/select>';
	var consulta;
	query=escape(consulta);
	var url="/lib/ajaxUpdateRecursive.php?module=rec.pesos&field=id_lugar_descargue&id=" + id + "&divid=" + namediv + "&width=" + width;
	oXmlHttp_id_lugar_descargue=GetHttpObject(cambiarRecursive_id_lugar_descargue);
	oXmlHttp_id_lugar_descargue.open("GET", url , true);
	oXmlHttp_id_lugar_descargue.send(null);
}
function cambiarRecursive_id_lugar_descargue(){
	if (oXmlHttp_id_lugar_descargue.readyState==4 || oXmlHttp_id_lugar_descargue.readyState=="complete"){
		document.getElementById('id_lugar_descargue').innerHTML=oXmlHttp_id_lugar_descargue.responseText
	}
}


var oXmlHttp_id_movimientos;
function updateRecursiveVariosMovimientosSinPeso(select){
	namediv='id_movimientos';
	nameId='id_movimientos';
	id=select.options[select.selectedIndex].value;
	fecha = document.entryform.fecha_entrada.value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateOpera.php?tipo=listadoVariosMovimientosSinPesoXVehiculo&id_vehiculo=" + id + "&fecha_entrada="+fecha+"&divid=" + namediv;
	oXmlHttp_id_movimientos=GetHttpObject(cambiarRecursive_id_movimientos);
	oXmlHttp_id_movimientos.open("GET", url , true);
	oXmlHttp_id_movimientos.send(null);
}
function cambiarRecursive_id_movimientos(){
	if (oXmlHttp_id_movimientos.readyState==4 || oXmlHttp_id_movimientos.readyState=="complete"){
		document.getElementById('id_movimientos').innerHTML=oXmlHttp_id_movimientos.responseText
	}
}

var oXmlHttp_id_unico_movimiento;
function updateRecursiveUltimoMovimientoSinPeso(select){
	namediv='id_unico_movimiento';
	nameId='id_unico_movimiento';
	id=select.options[select.selectedIndex].value;
	fecha = document.entryform.fecha_entrada.value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateOpera.php?tipo=listadoUltimoMovimientoSinPesoXVehiculo&id_vehiculo=" + id + "&fecha_entrada="+fecha+"&divid=" + namediv;
	oXmlHttp_id_unico_movimiento=GetHttpObject(cambiarRecursive_id_unico_movimiento);
	oXmlHttp_id_unico_movimiento.open("GET", url , true);
	oXmlHttp_id_unico_movimiento.send(null);
}
function cambiarRecursive_id_unico_movimiento(){
	if (oXmlHttp_id_unico_movimiento.readyState==4 || oXmlHttp_id_unico_movimiento.readyState=="complete"){
		document.getElementById('id_unico_movimiento').innerHTML=oXmlHttp_id_unico_movimiento.responseText
	}
}



</script>

</body>
</html>
