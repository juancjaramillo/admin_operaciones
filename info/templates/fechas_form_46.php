<?
$days_of_month = date('t',strtotime(date("Y-m-01", strtotime("last month"))));

$mesini = date("m", strtotime("-1 months"));
$anoini = date("Y", strtotime("-1 months"));

$inicio=date("Y-m-01", mktime(0, 0, 0, '01' , '01' , $anoini));

if (simple_me($ME)=="46.php") $inicio=date("Y-m-01", mktime(0, 0, 0, $mesini , '01', $anoini));
if (simple_me($ME)=="67.php") $inicio=date("Y-m-01", mktime(0, 0, 0, $mesini , '01', $anoini));

$final=date("Y-m-".$days_of_month,strtotime("last month"));

if(isset($_POST["inicio"])) $inicio=$_POST["inicio"];
if(isset($_POST["final"])) $final=$_POST["final"];

?>
<table width="100%">
  <tr>
    <td valign="top">
		<form name="entryform" action="<?=$ME?>" method="POST" onSubmit="return revisar()" class="form">
		<input type="hidden" name="modo" value="resultados">
      <table width="50%" cellpadding="5" cellspacing="3" align="center">
			<tr>
				<td>
					<table width="100%" border=1 bordercolor="#7fa840" align="right">
						<tr>
							<td align="center" valign="center" width='35%'>Inicio 
								<input type='text' size="10" id="f_inicio" class="casillatext_fecha" name='inicio' value='<?=$inicio?>'  readonly /><button id="b_inicio" onclick="javascript:showCalendarSencillo('f_inicio','b_inicio')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
							</td>
							<td align="center" valign="center" width='35%'>Fin 
								<input type='text' size="10" id="f_final" class="casillatext_fecha" name='final' value='<?=$final?>'  readonly /><button id="b_final" onclick="javascript:showCalendarSencillo('f_final','b_final')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
							</td>
							<td align='center' width='30%'>Mostrar&nbsp;&nbsp;
								<select  name='order'>
									<option value='1' <?if(isset($order) && $order =="1") echo "selected"?>>Detallado</option>
									<option value='2' <?if(isset($order) && $order =="2") echo "selected"?>>Consolidado</option>
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
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2}/;
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
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2}/;
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
