<?
include("../application.php");

$user=$_SESSION[$CFG->sesion]["user"];

include($CFG->dirroot."/templates/header_popup.php");
?>
<form name="entryform" action="" class="form">
<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong>HORARIOS OPERARIOS</strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td align='right' height=30>Fecha</td>
								<td align='left'>
									<input type='text' size="10" id="f_fecha" class="casillatext_fecha" name='fecha' value='<?=nvl($_GET["fecha"],date("Y-m-d"))?>'  readonly/><button id="b_fecha" onclick="javascript:showCalendarSencilloFuncion('f_fecha','b_fecha')" ><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right' height=30>Vehículo</td>
								<td align='left'>
									<div id="id_vehiculo">
									<select  name='id_vehiculo' id='id_vehiculo'  style="width:150px" onchange="updateRecursive_id_movimiento(this)">
										<option value="0">Seleccione</option>
										<?
											$qidVeh = $db->sql_query("
												SELECT distinct(v.id), v.codigo || '/'||v.placa as vehiculo, codigo, placa
												FROM ".$_GET["esquema"].".movimientos m
												LEFT JOIN vehiculos v ON v.id = m.id_vehiculo
												WHERE m.inicio::date = '".$_GET["fecha"]."' AND id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') 
												ORDER BY codigo, placa");
											while($veh = $db->sql_fetchrow($qidVeh))
											{
												echo '<option value="'.$veh["id"].'">'.$veh["vehiculo"].'</option>';
											}
										?>
									</select>
									</div>
								</td>
							</tr>
							<tr>
								<td align='right' height=30>Ruta</td>
								<td align='left'>
									<div id="id_movimiento">
									<select  name='id_movimiento' id='id_movimiento'  style="width:150px" onchange="updateRecursive_personas(this)">
										<option value="0">Seleccione</option>
									</select>
									</div>
								</td>
							</tr>
							<tr>
								<td align='right' height=30>Operarios</td>
								<td align='left'>
									<div id="personas">
									
									</select>
									</div>
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
			<input type="button" class="boton_verde" value="Cerrar" onclick="window.close()"/>
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

var oXmlHttp_id_vehiculo;
function updateRecursive_id_vehiculo(){
	namediv='id_vehiculo';
	nameId='id_vehiculo';
	fecha= document.entryform.fecha.value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '" style="width:150px"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateInformes.php?tipo=vehiculosxdiaMov&fecha=" + fecha + "&esquema=<?=$_GET["esquema"]?>&divid=" + namediv;
	oXmlHttp_id_vehiculo=GetHttpObject(cambiarRecursive_id_vehiculo);
	oXmlHttp_id_vehiculo.open("GET", url , true);
	oXmlHttp_id_vehiculo.send(null);
}
function cambiarRecursive_id_vehiculo(){
	if (oXmlHttp_id_vehiculo.readyState==4 || oXmlHttp_id_vehiculo.readyState=="complete"){
		document.getElementById('id_vehiculo').innerHTML=oXmlHttp_id_vehiculo.responseText
	}
}

var oXmlHttp_id_movimiento;
function updateRecursive_id_movimiento(select){
	namediv='id_movimiento';
	nameId='id_movimiento';
	fecha= document.entryform.fecha.value;
	id_vehiculo=select.options[select.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '" style="width:150px"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateInformes.php?tipo=movimientosxdiaxvehiculo&fecha=" + fecha + "&id_vehiculo=" + id_vehiculo + "&esquema=<?=$_GET["esquema"]?>&divid=" + namediv;
	oXmlHttp_id_movimiento=GetHttpObject(cambiarRecursive_id_movimiento);
	oXmlHttp_id_movimiento.open("GET", url , true);
	oXmlHttp_id_movimiento.send(null);
}
function cambiarRecursive_id_movimiento(){
	if (oXmlHttp_id_movimiento.readyState==4 || oXmlHttp_id_movimiento.readyState=="complete"){
		document.getElementById('id_movimiento').innerHTML=oXmlHttp_id_movimiento.responseText
	}
}

var oXmlHttp_personas;
function updateRecursive_personas(select){
	namediv='personas';
	id=select.options[select.selectedIndex].value;
	document.getElementById(namediv).innerHTML='Actualizando...';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateInformes.php?tipo=personasxmovimiento_tabla&id_movimiento=" + id + "&esquema=<?=$_GET["esquema"]?>&divid=" + namediv;
	oXmlHttp_personas=GetHttpObject(cambiarRecursive_personas);
	oXmlHttp_personas.open("GET", url , true);
	oXmlHttp_personas.send(null);
}
function cambiarRecursive_personas(){
	if (oXmlHttp_personas.readyState==4 || oXmlHttp_personas.readyState=="complete"){
		document.getElementById('personas').innerHTML=oXmlHttp_personas.responseText
	}
}







function showCalendarSencilloFuncion(casilla, boton)
{
	var cal = Calendar.setup({
		onSelect: function(cal) { cal.hide() },
		weekNumbers:true,
		inputField: casilla,
		trigger: boton,
		dateFormat:  "%Y-%m-%d",
		opacity:0,
		onChange : function() { updateRecursive_id_vehiculo() }
	});
}

</script>

<?include($CFG->templatedir . "/resize_window.php");?>