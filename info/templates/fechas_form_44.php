<?
$inicio=date("Y-m-d")." 00:00:01";
$final=date("Y-m-d H:i:s");

if(isset($_POST["inicio"])) $inicio=$_POST["inicio"];
if(isset($_POST["final"])) $final=$_POST["final"];



?>
<table width="100%">
  <tr>
    <td valign="top">
		<form name="entryform" action="<?=$ME?>" method="POST" onSubmit="return revisar()" class="form">
		<input type="hidden" name="modo" value="resultados">
      <table width="80%" cellpadding="5" cellspacing="3" align="center">
			<tr>
				<td>
					<table width="100%" border=1 bordercolor="#7fa840" align="right">
						<tr>
							<td align="center" valign="center" width='50%'>Inicio 
								<input type='text' size="20" id="f_inicio" class="casillatext_fecha" name='inicio' value='<?=$inicio?>'  readonly /><button id="b_inicio" onclick="javascript:showCalendarHora('f_inicio','b_inicio')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
							</td>
							<td align="center" valign="center">Fin 
								<input type='text' size="20" id="f_final" class="casillatext_fecha" name='final' value='<?=$final?>'  readonly /><button id="b_final" onclick="javascript:showCalendarHora('f_final','b_final')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
							</td>
						</tr>
					</table>
					<table width="100%" border=1 bordercolor="#7fa840" align="right">
						<tr>
							<td align='center'>Centro &nbsp;&nbsp;
							<select  name='id_centro'  onChange="updateRecursive_id_punto_control(this), updateRecursive_id_vehiculo(this)">
								<?
									$qidCn = $db->sql_query("SELECT id, centro 
										FROM centros WHERE id IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') 
										ORDER BY centro");
									while($cn = $db->sql_fetchrow($qidCn)){
										$selected = "";
										if($centro == $cn["id"]) $selected = " selected";
										echo '<option value="'.$cn["id"].'" '.$selected.'>'.$cn["centro"].'</option>';
								}
								?>
								</select>
							</td>
							<td align='center'>
								<div id="id_punto_control">Punto Control &nbsp;&nbsp;
								<select  name='id_punto_control' id='id_punto_control'  style="width:150px">
									<option value='%'>Seleccione</option>
										<?
										$qidAs = $db->sql_query("SELECT p.id, p.punto||' ('||nombre||')' as control
										FROM puntos_interes p 
										LEFT JOIN categorias_puntos_interes c ON c.id = p.id_categoria
										WHERE id_centro=".$centro."
										ORDER BY p.punto");
										while($as = $db->sql_fetchrow($qidAs))
										{
											$selected = "";
											if(isset($id_punto_control) && $id_punto_control == $as["id"]) $selected = " selected";
											echo '<option value="'.$as["id"].'" '.$selected.'>'.$as["control"].'</option>';
										}
									?>
								</select>
								</div>
							</td>
							<td align='center'>
								<div id="id_vehiculo">Vehículo &nbsp;&nbsp;
								<select  name='id_vehiculo' id='id_vehiculo'  style="width:150px">
									<option value=''>Todos</option>
										<?
										$qidAs = $db->sql_query("SELECT id, codigo||'/'||placa as vehiculo
										FROM vehiculos
										WHERE id_centro=".$centro." AND idgps is not null
										ORDER BY codigo,placa");
										while($as = $db->sql_fetchrow($qidAs))
										{
											$selected = "";
											if(isset($id_vehiculo) && $id_vehiculo == $as["id"]) $selected = " selected";
											echo '<option value="'.$as["id"].'" '.$selected.'>'.$as["vehiculo"].'</option>';
										}
									?>
								</select>
								</div>
							</td>
							<td align='center'>Radio (m)&nbsp;&nbsp;<input type='text' size="3" class="casillatext" name='radio' value='<?=$radio?>'/></td>
							<td align='center'>Ordenar por&nbsp;&nbsp;
								<select  name='order'>
									<option value='g.tiempo' <?if(isset($order) && $order =="g.tiempo") echo "selected"?>>Fecha</option>
									<option value='distancia' <?if(isset($order) && $order =="distancia") echo "selected"?>>Distancia</option>
								</select>
							</td>
						</tr>
					</table>
				</td>
				<td align="center" valign="center" > <input type="submit" class="boton_verde" value="Aceptar"/> </td>
			</form>
			</tr>
		</table>
	</td>
  </tr>
</table>

<script type="text/javascript">

function revisar()
{
	if(document.entryform.inicio.value.replace(/ /g, '') ==''){
		window.alert('Por favor seleccione: Fecha Inicio');
		document.entryform.inicio.focus();
		return(false);
	}
	else{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.inicio.value)){
			window.alert('[Fecha Inicio] no contiene un dato válido.');
			document.entryform.inicio.focus();
			return(false);
		}
	}

	if(document.entryform.final.value.replace(/ /g, '') ==''){
		window.alert('Por favor seleccione: Fecha Final');
		document.entryform.final.focus();
		return(false);
	}
	else{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
		if(!regexpression.test(document.entryform.final.value)){
			window.alert('[Fecha Final] no contiene un dato válido.');
			document.entryform.final.focus();
			return(false);
		}
	}

	if(document.entryform.inicio.value > document.entryform.final.value){
		window.alert('La fecha de inicio no puede ser mayor que la fecha final');
		document.entryform.inicio.focus();
		return(false);
	}

	if(document.entryform.id_punto_control.options[document.entryform.id_punto_control.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Punto de Control');
		document.entryform.id_punto_control.focus();
		return(false);
	}
	
	if(document.entryform.radio.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Radio');
		document.entryform.radio.focus();
		return(false);
	}
	else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.radio.value)){
			window.alert('[Radio] no contiene un dato válido.');
			document.entryform.radio.focus();
			return(false);
		}
	}




	return true;
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

var oXmlHttp_id_punto_control;
function updateRecursive_id_punto_control(select){
	namediv='id_punto_control';
	nameId='id_punto_control';
	id=select.options[select.selectedIndex].value;
	document.getElementById(namediv).innerHTML='Punto Control <select id="' + nameId + '" style="width:100px"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateInformes.php?tipo=puntocontrolxcentro&id_centro=" + id + "&divid=" + namediv;
	oXmlHttp_id_punto_control=GetHttpObject(cambiarRecursive_id_punto_control);
	oXmlHttp_id_punto_control.open("GET", url , true);
	oXmlHttp_id_punto_control.send(null);
}
function cambiarRecursive_id_punto_control(){
	if (oXmlHttp_id_punto_control.readyState==4 || oXmlHttp_id_punto_control.readyState=="complete"){
		document.getElementById('id_punto_control').innerHTML=oXmlHttp_id_punto_control.responseText
	}
}


var oXmlHttp_id_vehiculo;
function updateRecursive_id_vehiculo(select){
	namediv='id_vehiculo';
	nameId='id_vehiculo';
	id=select.options[select.selectedIndex].value;
	document.getElementById(namediv).innerHTML='Vehículo <select id="' + nameId + '" style="width:150px"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateInformes.php?tipo=vehiculoxcentro&id_centro=" + id + "&divid=" + namediv;
	oXmlHttp_id_vehiculo=GetHttpObject(cambiarRecursive_id_vehiculo);
	oXmlHttp_id_vehiculo.open("GET", url , true);
	oXmlHttp_id_vehiculo.send(null);
}
function cambiarRecursive_id_vehiculo(){
	if (oXmlHttp_id_vehiculo.readyState==4 || oXmlHttp_id_vehiculo.readyState=="complete"){
		document.getElementById('id_vehiculo').innerHTML=oXmlHttp_id_vehiculo.responseText
	}
}



</script>
