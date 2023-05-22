<form name="entryform" action="<?=$ME?>" method="POST" onSubmit="return revisar()" class="form">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id_novedad" value="<?=nvl($orden["id_novedad"])?>">

<table width="100%">
	<tr>
		<td height="40" class="azul_16"><strong>ORDEN DE TRABAJO</strong></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840">
							<tr>
								<td align='right' width="30%">(*) Equipo</td>
								<td align='left'> 
									<div id="id_equipo"><select  name="id_equipo" id="id_equipo" style="width:250px" onChange="updateRecursive_id_rutina()"><?=nvl($equipos)?></select></div>
								</td>
							</tr>
							<tr>
								<td align='right'>Sistema</td>
								<td align='left'>
									<select  name='id_sistema' onChange="updateRecursive_id_rutina()"><?=$sistemas?></select> 
								</td>
							</tr>
							<tr>
								<td align='right'>Tipo</td>
								<td align='left'>
									<select  name='id_tipo_mantenimiento' onChange="updateRecursive_id_rutina()"><?=$tipos?></select> 
								</td>
							</tr>
							<tr>
								<td align='right'>(*) Rutina</td>
								<td align='left'>
									<?if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["agregarOT"])){?>
									<div id="id_rutina"><select  name='id_rutina' id="id_rutina" style="width:250px"><?=$rutinas?></select>&nbsp;&nbsp;<a href="javascript:abrirVentanaJavaScript('rutinafo','1100','500','<?=$CFG->wwwroot?>/mtto/rutinas.php?mode=agregar&devolver=1')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-add.gif' border='0'></a>
									</div>
									<?}?>
								</td>
							</tr>
							<tr>
								<td align='right'>(*) Fecha Planeada</td>
								<td align='left'>
									<input size="20" id="f_fecha_planeada" class="casillatext_fecha" name='fecha_planeada' value='<?=nvl($orden["fecha_planeada"])?>' /><button id="b_fecha_planeada" onclick="javascript:showCalendarHora('f_fecha_planeada','b_fecha_planeada')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center">
			<input type="submit" class="boton_verde" value="Aceptar"/>
			<input type="button" class="boton_verde" value="Cancelar" onclick="window.close()"/>
		</td>
	</tr>
</table>
</form>
<script type="text/javascript">


function revisar()
{
	if(document.entryform.id_rutina.options[document.entryform.id_rutina.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Rutina');
		document.entryform.id_rutina.focus();
		return(false);
	}
	if(document.entryform.id_equipo.options[document.entryform.id_equipo.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Equipo');
		document.entryform.id_equipo.focus();
		return(false);
	}

	if(document.entryform.fecha_planeada.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Fecha Planeada');
		document.entryform.fecha_planeada.focus();
		return(false);
	}
	else{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.fecha_planeada.value)){
			window.alert('[Fecha Planeada] no contiene un dato válido.');
			document.entryform.fecha_planeada.focus();
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

var oXmlHttp_id_rutina;
function updateRecursive_id_rutina(){
	namediv='id_rutina';
	nameId='id_rutina';
	id_equipo=document.entryform.id_equipo.options[document.entryform.id_equipo.selectedIndex].value;
	id_sistema=document.entryform.id_sistema.options[document.entryform.id_sistema.selectedIndex].value;
	id_tipo_mantenimiento=document.entryform.id_tipo_mantenimiento.options[document.entryform.id_tipo_mantenimiento.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateMtto.php?tipo=rutinasxequipoysistemaytipo&id_equipo=" + id_equipo + "&id_sistema=" + id_sistema + "&id_tipo_mantenimiento=" + id_tipo_mantenimiento + "&divid=" + namediv;
	oXmlHttp_id_rutina=GetHttpObject(cambiarRecursive_id_rutina);
	oXmlHttp_id_rutina.open("GET", url , true);
	oXmlHttp_id_rutina.send(null);
}
function cambiarRecursive_id_rutina(){
	if (oXmlHttp_id_rutina.readyState==4 || oXmlHttp_id_rutina.readyState=="complete"){
		document.getElementById('id_rutina').innerHTML=oXmlHttp_id_rutina.responseText
	}
}


function cargarValoresRutina()
{
	updateRecursive_id_rutina();
}



</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>

