<?
include_once("../../application.php");
include($CFG->dirroot."/templates/header_popup.php");

$user=$_SESSION[$CFG->sesion]["user"];

$db->crear_select("SELECT id, ase FROM ases WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') ORDER BY ase",$ases);
$db->crear_select("SELECT id, nombre FROM tipos_residuos ORDER BY nombre",$tipos_residuos);
$db->crear_select("SELECT id, servicio FROM servicios ORDER BY servicio",$servicios);
$db->crear_select("SELECT id, nombre FROM lugares_descargue WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') ORDER BY nombre",$descargue);

$db->crear_select("SELECT id, nombre FROM cuartelillos WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') ORDER BY nombre",$cuartelillos);
$db->crear_select("SELECT id, codigo||'/'||placa as codigo FROM vehiculos WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') ORDER BY codigo, placa",$vehiculos);

$cargos = array(8);
obtenerIdCargos(8,$cargos);
$db->crear_select("SELECT id, nombre||' '||apellido as nombre FROM personas WHERE id IN (SELECT id_persona FROM personas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."')) AND id_cargo IN (".implode(",",$cargos).") ORDER BY nombre,apellido",$coordinador);


?>

<form name="entryform" action="<?=$CFG->wwwroot?>/opera/micros.php" method="POST"  class="form" enctype="multipart/form-data" onSubmit="return revisar()">
<input type="hidden" name="mode" value="resultados">

<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong>BUSCAR RUTA</strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td align='right'>Código</td>
								<td align='left'>
									<input type='text' size='12' class="casillatext" name='codigo' value='<?=nvl($micro["codigo"])?>'>
								</td>
							</tr>
							<tr>
								<td align='right'>Ase</td>
								<td align='left'><select  name="id_ase" onChange="updateRecursive_id_vehiculo(), updateRecursive_id_cuartelillo(), updateRecursive_id_coordinador()"><?=$ases?></select></td>
							</tr>
							<tr>
								<td align='right'>Tipo Residuo</td>
								<td align='left'><select  name="id_tipo_residuo" ><?=$tipos_residuos?></select></td>
							</tr>
							<tr>
								<td align='right'>Servicio</td>
								<td align='left'><select  name="id_servicio" onChange="updateRecursive_id_vehiculo()" ><?=$servicios?></select></td>
							</tr>
							<tr>
								<td align='right'>Km</td>
								<td align='left'>
									<input type='text' size='12' class="casillatext" name='km_inicial' value=''> a 
									<input type='text' size='12' class="casillatext" name='km_final' value=''> 
								</td>
							</tr>
							<tr>
								<td align='right'>Cuartelillo</td>
								<td align='left'><div id="id_cuartelillo"><select  name="id_cuartelillo" id="id_cuartelillo" style="width:250px" ><?=nvl($cuartelillos)?></select></div></td>
							</tr>
							<tr>
								<td align='right'>Vehículo</td>
								<td align='left'><div id="id_vehiculo"><select  name="id_vehiculo" id="id_vehiculo" style="width:250px"><?=nvl($vehiculos)?></select></div></td>
							</tr>
							<tr>
								<td align='right'>Coordinador</td>
								<td align='left'><div id="id_coordinador"><select  name="id_coordinador" id="id_coordinador" style="width:250px" ><?=nvl($coordinador)?></select></div></td>
							</tr>
							<tr>
								<td align='right'>Vigencia desde</td>
								<td align='left'>
									<input type='text' size="10" id="f_fecha_desde" class="casillatext_fecha" name='fecha_desde' value='<?=nvl($micro["fecha_desde"])?>'  readonly /><button id="b_fecha_desde" onclick="javascript:showCalendarSencillo('f_fecha_desde','b_fecha_desde')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>Vigencia Hasta</td>
								<td align='left'>
									<input type='text' size="10" id="f_fecha_hasta" class="casillatext_fecha" name='fecha_hasta' value='<?=nvl($micro["fecha_hasta"])?>'  readonly /><button id="b_fecha_hasta" onclick="javascript:showCalendarSencillo('f_fecha_hasta','b_fecha_hasta')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>Lugar Descargue</td>
								<td align='left'><select  name="id_lugar_descargue" ><?=$descargue?></select></td>
							</tr>
							<tr>
								<td align='right'>Compactadas</td>
								<td align='left'>
									<input type='text' size='12' class="casillatext" name='compactadas_inicial' value=''> a
									<input type='text' size='12' class="casillatext" name='compactadas_final' value=''> 
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
	if(document.entryform.codigo.value.replace(/ /g, '') =='' && document.entryform.id_ase.options[document.entryform.id_ase.selectedIndex].value=='%' && document.entryform.id_tipo_residuo.options[document.entryform.id_tipo_residuo.selectedIndex].value=='%' && document.entryform.id_servicio.options[document.entryform.id_servicio.selectedIndex].value=='%' && document.entryform.km_inicial.value.replace(/ /g, '') == '' && document.entryform.km_final.value.replace(/ /g, '') == '' && document.entryform.id_cuartelillo.options[document.entryform.id_cuartelillo.selectedIndex].value=='%' && document.entryform.id_vehiculo.options[document.entryform.id_vehiculo.selectedIndex].value=='%' && document.entryform.id_coordinador.options[document.entryform.id_coordinador.selectedIndex].value=='%' && document.entryform.fecha_desde.value.replace(/ /g, '') =='' && document.entryform.fecha_hasta.value.replace(/ /g, '') == '' && document.entryform.id_lugar_descargue.options[document.entryform.id_lugar_descargue.selectedIndex].value=='%' && document.entryform.compactadas_inicial.value.replace(/ /g, '') == '' && document.entryform.compactadas_final.value.replace(/ /g, '') == '')
	{
		window.alert("Escriba o seleccione algún criterio de búsqueda");
		return false;	
	}


	if(document.entryform.codigo.value.replace(/ /g, '') !=''){
		var regexpression=/^.{1,1055}$/m;
		if(!regexpression.test(document.entryform.codigo.value)){
			window.alert('[Código] no contiene un dato válido.');
			document.entryform.codigo.focus();
			return(false);
		}
	}
	if(document.entryform.km_inicial.value !=''){
		var regexpression=/^.{1,1055}$/m;
		if(!regexpression.test(document.entryform.km_inicial.value)){
			window.alert('[Km Inicial] no contiene un dato válido.');
			document.entryform.km_inicial.focus();
			return(false);
		}
	}
	if(document.entryform.km_final.value !=''){
		var regexpression=/^.{1,1055}$/m;
		if(!regexpression.test(document.entryform.km_final.value)){
			window.alert('[Km Final] no contiene un dato válido.');
			document.entryform.km_final.focus();
			return(false);
		}
	}

	if((document.entryform.km_inicial.value !='' && document.entryform.km_final.value =='') || (document.entryform.km_inicial.value =='' && document.entryform.km_final.value !='') ){
		if(document.entryform.km_inicial.value =='')
			window.alert('[Km Inicial] no contiene un dato válido.');
		if(document.entryform.km_final.value =='')
			window.alert('[Km Final] no contiene un dato válido.');
		return(false);
	}else
	{
		if(document.entryform.km_final.value < document.entryform.km_inicial.value)
		{
			window.alert('[Km Final] no puede ser menor que el inicial');
			document.entryform.km_final.focus();
			return(false);
		}
	}

	if(document.entryform.fecha_desde.value.replace(/ /g, '') !=''){
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/m;
		if(!regexpression.test(document.entryform.fecha_desde.value)){
			window.alert('[Vigencia desde] no contiene un dato válido.');
			document.entryform.fecha_desde.focus();
			return(false);
		}
	}
	if(document.entryform.fecha_hasta.value !=''){
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/m;
		if(!regexpression.test(document.entryform.fecha_hasta.value)){
			window.alert('[Vigencia Hasta] no contiene un dato válido.');
			document.entryform.fecha_hasta.focus();
			return(false);
		}
	}
	if(document.entryform.compactadas_inicial.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.compactadas_inicial.value)){
			window.alert('[Compactadas Iniciales] no contiene un dato válido.');
			document.entryform.compactadas_inicial.focus();
			return(false);
		}
	}
	if(document.entryform.compactadas_final.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.compactadas_final.value)){
			window.alert('[Compactadas Finales] no contiene un dato válido.');
			document.entryform.compactadas_final.focus();
			return(false);
		}
	}

	if((document.entryform.compactadas_inicial.value !='' && document.entryform.compactadas_final.value =='') || (document.entryform.compactadas_inicial.value =='' && document.entryform.compactadas_final.value !='') ){
		if(document.entryform.compactadas_inicial.value =='')
			window.alert('[Compactadas Inicial] no contiene un dato válido.');
		if(document.entryform.compactadas_final.value =='')
			window.alert('[Compactadas Final] no contiene un dato válido.');
		return(false);
	}else
	{
		if(document.entryform.compactadas_final.value < document.entryform.compactadas_inicial.value)
		{
			window.alert('[Compactadas Final] no puede ser menor que las iniciales.');
			document.entryform.compactadas_final.focus();
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

var oXmlHttp_id_equipo;
function updateRecursive_id_vehiculo(){
	namediv='id_vehiculo';
	nameId='id_vehiculo';
	id_ase = document.entryform.id_ase.options[document.entryform.id_ase.selectedIndex].value;
	id_servicio = document.entryform.id_servicio.options[document.entryform.id_servicio.selectedIndex].value;
	if(id_ase != '%' && id_servicio != '%')
	{
		document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
		var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateOpera.php?tipo=listadoVehiculosXAseyServicio&id_ase=" + id_ase + "&id_servicio="+ id_servicio + "&divid=" + namediv;
		oXmlHttp_id_vehiculo=GetHttpObject(cambiarRecursive_id_vehiculo);
		oXmlHttp_id_vehiculo.open("GET", url , true);
		oXmlHttp_id_vehiculo.send(null);
	}
}
function cambiarRecursive_id_vehiculo(){
	if (oXmlHttp_id_vehiculo.readyState==4 || oXmlHttp_id_vehiculo.readyState=="complete"){
		document.getElementById('id_vehiculo').innerHTML=oXmlHttp_id_vehiculo.responseText
	}
}

var oXmlHttp_id_cuartelillo;
function updateRecursive_id_cuartelillo(){
	namediv='id_cuartelillo';
	nameId='id_cuartelillo';
	id_ase = document.entryform.id_ase.options[document.entryform.id_ase.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateOpera.php?tipo=listadoCuartelilloXAse&id_ase=" + id_ase + "&divid=" + namediv;
	oXmlHttp_id_cuartelillo=GetHttpObject(cambiarRecursive_id_cuartelillo);
	oXmlHttp_id_cuartelillo.open("GET", url , true);
	oXmlHttp_id_cuartelillo.send(null);
}
function cambiarRecursive_id_cuartelillo(){
	if (oXmlHttp_id_cuartelillo.readyState==4 || oXmlHttp_id_cuartelillo.readyState=="complete"){
		document.getElementById('id_cuartelillo').innerHTML=oXmlHttp_id_cuartelillo.responseText
	}
}

var oXmlHttp_id_coordinador;
function updateRecursive_id_coordinador(){
	namediv='id_coordinador';
	nameId='id_coordinador';
	id_ase = document.entryform.id_ase.options[document.entryform.id_ase.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateOpera.php?tipo=listadoCoordinadorXAse&id_ase=" + id_ase + "&divid=" + namediv;
	oXmlHttp_id_coordinador=GetHttpObject(cambiarRecursive_id_coordinador);
	oXmlHttp_id_coordinador.open("GET", url , true);
	oXmlHttp_id_coordinador.send(null);
}
function cambiarRecursive_id_coordinador(){
	if (oXmlHttp_id_coordinador.readyState==4 || oXmlHttp_id_coordinador.readyState=="complete"){
		document.getElementById('id_coordinador').innerHTML=oXmlHttp_id_coordinador.responseText
	}
}

</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>

