<?
include("../../application.php");
$user=$_SESSION[$CFG->sesion]["user"];

$db->crear_select("SELECT id, centro, id as id_centro FROM centros WHERE id IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]') ORDER BY centro",$centros);
$db->crear_select("SELECT id, codigo||'/'||placa FROM vehiculos WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]') ORDER BY codigo,placa",$vehiculos);
$db->crear_select("SELECT id, tipo FROM llta.tipos_llantas ORDER BY tipo",$tipos,"","");
$db->crear_select("SELECT d.id, d.dimension ||' ('||m.marca||')' FROM llta.dimensiones d LEFT JOIN llta.marcas m ON m.id=d.id_marca ORDER BY d.dimension,m.marca",$dimensiones,"","");
$db->crear_select("SELECT id, marca FROM llta.marcas ORDER BY marca",$marcas,"","");
$db->crear_select("SELECT id, nombre FROM llta.estados ORDER BY nombre",$estados);

include($CFG->dirroot."/templates/header_popup.php");

?>


<form name="entryform" action="<?=$CFG->wwwroot?>/mtto/llantas.php" method="POST"  class="form" enctype="multipart/form-data" onSubmit="return revisar()">
<input type="hidden" name="mode" value="resultados">
<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong>BUSCAR LLANTAS</strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td align='right'>Centro</td>
								<td align='left'> <select  name='id_centro' onChange="updateRecursive_id_proveedor(this)"><?=$centros?></select> </td>
							</tr>
							<tr>
								<td align='right'>Número</td>
								<td align='left'><input type='text' size='40'  name='numero' class='casillatext' value='<?=nvl($llanta["numero"])?>'></td>
							</tr>
							<tr>
								<td align='right'>Estado</td>
								<td align='left'> <select  name="id_estado" style="width:250px" ><?=nvl($estados)?></select></td>
							</tr>
							<tr>
								<td align='right'>Marca</td>
								<td align='left'> <select  name='id_marca' onChange="updateRecursive_id_dimension(this)"><option value='%'>Seleccione...</option><?=$marcas?></select> </td>
							</tr>
							<tr>
								<td align='right'>Dimensión</td>
								<td align='left'> <div id="id_dimension"><select  name="id_dimension" id="id_dimension" style="width:250px" ><option value='%'>Seleccione...</option><?=nvl($dimensiones)?></select></div></td>
							</tr>
							<tr>
								<td align='right'>Diseño</td>
								<td align='left'><input type='text' size='40'  name='disenio' class='casillatext' value='<?=nvl($llanta["disenio"])?>'></td>
							</tr>
							<tr>
								<td align='right'>Tipo</td>
								<td align='left'> <select  name="id_tipo_llanta" id="id_tipo_llanta" ><option value='%'>Seleccione...</option><?=nvl($tipos)?></select></td>
							</tr>


							<tr>
								<td align='right'>Proveedor</td>
								<td align='left'> <div id="id_proveedor"><select  name="id_proveedor" id="id_proveedor" style="width:250px" ><option value="%">Seleccione...</option><?=nvl($proveedores)?></select></div></td>
							</tr>
							<tr>
								<td align='right'>Fecha Compra</td>
								<td align='left'>
									Inicial 	<input type='text' size="10" id="f_fecha_compra_inicial" class="casillatext_fecha" name='fecha_compra_inicial' value='' /><button id="b_fecha_compra_inicial" onclick="javascript:showCalendarSencillo('f_fecha_compra_inicial','b_fecha_compra_inicial')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button> a Final <input type='text' size="10" id="f_fecha_compra_final" class="casillatext_fecha" name='fecha_compra_final' value='' /><button id="b_fecha_compra_final" onclick="javascript:showCalendarSencillo('f_fecha_compra_final','b_fecha_compra_final')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
							<tr>
								<td align='right'>Dot</td>
								<td align='left'><input type='text' size='40'  name='dot' class='casillatext' value='<?=nvl($llanta["dot"])?>'></td>
							</tr>
							<tr>
								<td align='right'>Matricula No.</td>
								<td align='left'><input type='text' size='40'  name='matricula' class='casillatext' value='<?=nvl($llanta["matricula"])?>'></td>
							</tr>
							<tr>
								<td align='right'>Vehículo</td>
								<td align='left'> <select  name='id_vehiculo'><?=$vehiculos?></select> </td>
							</tr>
							<tr>
								<td align='right'>Km</td>
								<td align='left'> <input type='text' size='10'  name='km_inicial' class='casillatext' value=''> a <input type='text' size='10'  name='km_final' class='casillatext' value=''></td>
							</tr>
							<tr>
								<td align='right'>Vida</td>
								<td align='left'>
									<select  name="vida">
										<option value="%">Seleccione...</option>
										<option value="N">Nueva (N)</option>
										<option value="1">Primer reencauche (1)</option>
										<option value="2">Segundo reencauche (2)</option>
										<option value="3">Tercer reencauche (3)</option>
									</select>
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
	if(
			document.entryform.id_centro.options[document.entryform.id_centro.selectedIndex].value=='%' && 
			document.entryform.numero.value.replace(/ /g, '') =='' && 
			document.entryform.id_marca.options[document.entryform.id_marca.selectedIndex].value=='%' && 
			document.entryform.id_dimension.options[document.entryform.id_dimension.selectedIndex].value=='%' && 
			document.entryform.disenio.value.replace(/ /g, '') =='' &&
			document.entryform.id_tipo_llanta.options[document.entryform.id_tipo_llanta.selectedIndex].value=='%' && 
			document.entryform.id_proveedor.options[document.entryform.id_proveedor.selectedIndex].value=='%' && 
			document.entryform.fecha_compra_inicial.value.replace(/ /g, '') =='' && document.entryform.fecha_compra_final.value.replace(/ /g, '') =='' && 
			document.entryform.dot.value.replace(/ /g, '') =='' && 
			document.entryform.matricula.value.replace(/ /g, '') =='' && 
			document.entryform.id_vehiculo.options[document.entryform.id_vehiculo.selectedIndex].value=='%' && 
			document.entryform.km_inicial.value.replace(/ /g, '') =='' && document.entryform.km_final.value.replace(/ /g, '') =='' &&
			document.entryform.vida.options[document.entryform.vida.selectedIndex].value=='%' && 
			document.entryform.id_estado.options[document.entryform.id_estado.selectedIndex].value=='%' 
		){
		window.alert('Por favor seleccione algún criterio de búsqueda');
		return(false);
	}

	if(document.entryform.numero.value.replace(/ /g, '') !=''){
		var regexpression=/^.{1,1055}$/m;
		if(!regexpression.test(document.entryform.numero.value)){
			window.alert('[Número] no contiene un dato válido.');
			document.entryform.numero.focus();
			return(false);
		}
	}

	if(document.entryform.disenio.value.replace(/ /g, '') !=''){
		var regexpression=/^.{1,1055}$/m;
		if(!regexpression.test(document.entryform.disenio.value)){
			window.alert('[Diseño] no contiene un dato válido.');
			document.entryform.disenio.focus();
			return(false);
		}
	}

	if((document.entryform.fecha_compra_inicial.value.replace(/ /g, '') !='' && document.entryform.fecha_compra_final.value.replace(/ /g, '') =='') || (document.entryform.fecha_compra_final.value.replace(/ /g, '') !='' && document.entryform.fecha_compra_inicial.value.replace(/ /g, '') ==''))
	{
		window.alert('Por favor seleccione la fecha inicial y final');
		return(false);
	}

	if(document.entryform.fecha_compra_inicial.value.replace(/ /g, '') !=''){
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2}/;
		if(!regexpression.test(document.entryform.fecha_compra_inicial.value)){
			window.alert('[Fecha Inicial] no contiene un dato válido.');
			document.entryform.fecha_compra_inicial.focus();
			return(false);
		}
	}

	if(document.entryform.fecha_compra_final.value.replace(/ /g, '') !=''){
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2}/;
		if(!regexpression.test(document.entryform.fecha_compra_final.value)){
			window.alert('[Fecha final] no contiene un dato válido.');
			document.entryform.fecha_compra_final.focus();
			return(false);
		}
	}


	if(document.entryform.fecha_compra_inicial.value > document.entryform.fecha_compra_final.value)
	{
		window.alert('La fecha inicial no puede ser mayor que la final');
		return(false);
	}

	if(document.entryform.dot.value !=''){
		var regexpression=/^.{1,255}$/m;
		if(!regexpression.test(document.entryform.dot.value)){
			window.alert('[Dot] no contiene un dato válido.');
			document.entryform.dot.focus();
			return(false);
		}
	}
	
	if(document.entryform.matricula.value !=''){
		var regexpression=/^.{1,255}$/m;
		if(!regexpression.test(document.entryform.matricula.value)){
			window.alert('[Matricula No.] no contiene un dato válido.');
			document.entryform.matricula.focus();
			return(false);
		}
	}

	if((document.entryform.km_inicial.value.replace(/ /g, '') !='' && document.entryform.km_final.value.replace(/ /g, '') =='') || (document.entryform.km_final.value.replace(/ /g, '') !='' && document.entryform.km_inicial.value.replace(/ /g, '') ==''))
	{
		window.alert('Por favor escriba el km inicial y final');
		return(false);
	}

	if(document.entryform.km_inicial.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.km_inicial.value)){
			window.alert('[Km inicial] no contiene un dato válido.');
			document.entryform.km_inicial.focus();
			return(false);
		}
	}

	if(document.entryform.km_final.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.km_final.value)){
			window.alert('[Km final] no contiene un dato válido.');
			document.entryform.km_final.focus();
			return(false);
		}
	}

	if(document.entryform.km_inicial.value > document.entryform.km_final.value)
	{
		window.alert('El km inicial no puede ser mayor que el final');
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

var oXmlHttp_id_dimension;
function updateRecursive_id_dimension(select){
	namediv='id_dimension';
	nameId='id_dimension';
	id=select.options[select.selectedIndex].value;
	width=document.getElementById(nameId).style.width;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '" style="width:' + document.getElementById(nameId).style.width + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateMtto.php?tipo=actualizarDimensiones&id_marca=" + id + "&divid=" + namediv;
	oXmlHttp_id_dimension=GetHttpObject(cambiarRecursive_id_dimension);
	oXmlHttp_id_dimension.open("GET", url , true);
	oXmlHttp_id_dimension.send(null);
}
function cambiarRecursive_id_dimension(){
	if (oXmlHttp_id_dimension.readyState==4 || oXmlHttp_id_dimension.readyState=="complete"){
		document.getElementById('id_dimension').innerHTML=oXmlHttp_id_dimension.responseText
	}
}


var oXmlHttp_id_proveedor;
function updateRecursive_id_proveedor(select){
	namediv='id_proveedor';
	nameId='id_proveedor';
	id=select.options[select.selectedIndex].value;
	width=document.getElementById(nameId).style.width;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '" style="width:' + document.getElementById(nameId).style.width + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateMtto.php?tipo=proveedoresxcentro&id_centro=" + id + "&divid=" + namediv;
	oXmlHttp_id_proveedor=GetHttpObject(cambiarRecursive_id_proveedor);
	oXmlHttp_id_proveedor.open("GET", url , true);
	oXmlHttp_id_proveedor.send(null);
}
function cambiarRecursive_id_proveedor(){
	if (oXmlHttp_id_proveedor.readyState==4 || oXmlHttp_id_proveedor.readyState=="complete"){
		document.getElementById('id_proveedor').innerHTML=oXmlHttp_id_proveedor.responseText
	}
}

</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>

