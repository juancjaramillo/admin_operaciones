<form name="entryform" action="<?=$ME?>" method="POST"  class="form" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id" value="<?=nvl($rutina["id"])?>">
<input type="hidden" name="devolver" value="<?=nvl($rutina["devolver"],0)?>">
<input type="hidden" name="accion" value="">
<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong><?=strtoupper($newMode)?> RUTINA</strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
							  <td align='left' colspan=2><span class="azul_12">DATOS BÁSICOS</span></td>
							</tr>
							<tr>
								<td align='right'>(*) Centro</td>
								<td align='left'>
								<?
								$qidCen = $db->sql_query("SELECT id, centro FROM centros WHERE id IN (" . implode(",",$user["id_centro"]).")");
								while($cen =$db->sql_fetchrow($qidCen))
								{
									$checked = "";
									if(in_array($cen["id"],nvl($rutinasCentros,array())))
										$checked = "checked";
									echo '<input type="checkbox" name="mtto.rutinas_centros[]" value="'.$cen["id"].'" '.$checked.'>&nbsp;'.$cen["centro"].'<br>';
								}
								?>
								</td>
							</tr>
							<tr>
								<td align='right'>(*) Nombre de la rutina</td>
								<td align='left'><input type='text' size='40'  name='rutina' class='casillatext' value='<?=nvl($rutina["rutina"])?>'></td>
							</tr>
							<tr>
								<td align='right'>(*) Tipo</td>
								<td align='left'> <select  name='id_tipo_mantenimiento'><?=$tipos?></select> </td>
							</tr>
							<tr>
								<td align='right'>(*) Prioridad</td>
								<td align='left'> <select  name='id_prioridad'><?=$prioridades?></select> </td>
							</tr>
							<tr>
								<td align='right'>(*) Sistema</td>
								<td align='left'> <select  name='id_sistema'><?=$sistemas?></select> </td>
							</tr>
							<tr>
								<td align='right'>Grupo</td>
								<td align='left'> <select  name='id_grupo'><?=$grupos?></select> </td>
							</tr>
							<?if(!isset($sinEquipo)){?>
							<tr>
								<td align='right'>Equipo</td>
								<td align='left'> <div id="id_equipo"><select  name="id_equipo" id="id_equipo"><option value="%">Seleccione...</option><?=$equipos?></select></div> </td>
							</tr>
							<?}else{?>
								<input type="hidden" name="id_equipo" value="%">
							<?}?>
							<tr>
								<td align='right'>(*) Lugar</td>
								<td align='left'>
									<select  name='lugar'>
									<option value="%">Seleccione...</option>
									<option value="Interno" <?if(nvl($rutina["lugar"])=="Interno") echo "selected";?>>Interno</option>
									<option value="Externo" <?if(nvl($rutina["lugar"])=="Externo") echo "selected";?>>Externo</option>
									</select> 
								</td>
							</tr>
							<?if(isset($sinFrecuencias)){?>
								<input type="hidden" name="id_frecuencia" value="%">
								<input type="hidden" name="frec_horas" value="">
								<input type="hidden" name="frec_km" value="">
								<input type="hidden" name="fec_cumplir" value="0">
							<?}else{?>
							<tr>
								<td align='right'>Frecuencia</td>
								<td align='left'> <select  name='id_frecuencia'><?=$frecuencias?></select> </td>
							</tr>
							<tr>
								<td align='right'>Frecuencia Horas</td>
								<td align='left'><input type='text' size='7' class='casillatext' name='frec_horas' value='<?=nvl($rutina["frec_horas"])?>'></td>
							</tr>
							<tr>
								<td align='right'>Frecuencia Km</td>
								<td align='left'><input type='text' size='7' class='casillatext' name='frec_km' value='<?=nvl($rutina["frec_km"])?>'></td>
							</tr>
							<tr>
								<td align='right'>(*) No. de frecuencias a cumplir</td>
								<td align='left'><input type='text' size='7' class='casillatext' name='fec_cumplir' value='<?=nvl($rutina["fec_cumplir"])?>'></td>
							</tr>
							<?}?>
							<tr>
								<td align='right'>(*) Tiempo Ejecución (minutos)</td>
								<td align='left'><input type='text' size='7' class='casillatext' name='tiempo_ejecucion' value='<?=nvl($rutina["tiempo_ejecucion"],0)?>'></td>
							</tr>
							<tr>
								<td align='right'>(*) ¿Activa?</td>
								<td align='left'><label for='activa_t'>Sí</label>&nbsp;<input type='radio' id='activa_t' name='activa' value='t' <?if(nvl($rutina["activa"],"t")=="t") echo "CHECKED"?>  >&nbsp;<label for='activa_f'>No</label>&nbsp;<input type='radio' id='activa_f' name='activa' value='f' <?if(nvl($rutina["activa"])=="f") echo "CHECKED"?> >&nbsp;</td>
							<tr>
								<td align='right'>Comentarios</td>
								<td align='left'><textarea  rows='2' cols='40' name='comentarios'><?=nvl($rutina["comentarios"])?></textarea></td>
							</tr>
							<tr>
								<td align='right'>Herramientas</td>
								<td align='left'><textarea  rows='2' cols='40' name='herramientas'><?=nvl($rutina["herramientas"])?></textarea></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
		<?if($newMode != "insertar"){?>
		<td width="20px"> &nbsp; </td>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
				<tr>
					<td align="center">
						<table width="98%" border=1 bordercolor="#7fa840" id="tabla_actividades">
							<tr>
								<td align='left' colspan=3><span class="azul_12">ELEMENTOS</span></td>
							</tr>
							<tr>
								<td width="90%" align="center">ELEMENTO</td>
								<td width="10%" align="center">CANTIDAD</td>
								<td width="10%" align="center">OPCIONES</td>
							</tr>
							<?
							while($elEx = $db->sql_fetchrow($qidEleExist)){
								$nele = $db->sql_row("SELECT e.id, e.codigo||' ('||e.elemento||'/'||u.unidad||')' as cod FROM mtto.elementos e LEFT JOIN mtto.unidades u ON u.id=e.id_unidad WHERE e.id=".$elEx["id_elemento"]);
								?>
							<tr id="existe_<?=$elEx["id"]?>">
								<input type="hidden" name="existe_id_ele_<?=$elEx["id"]?>" value="<?=$nele["id"]?>">
								<td><?=$nele["cod"]?></td>
								<td align="center"><input type='text' size='4' class='casillatext_fecha' name='ex_cant_<?=$elEx["id"]?>' value='<?=$elEx["cantidad"]?>'></td>
								<td align="center"><a href="javascript:delete_celda('existe_<?=$elEx["id"]?>')" class="link_verde" title="Borrar">B</a> </td>
							</tr>
							<?}?>
						</table>
					</td>
				</tr>
				<tr>
					<td align="center" height="50"><a href="javascript:agrega_celda()" class="link_verde">+ Agregar Elemento +</a> </td>
				</tr>
				<tr>
					<td>
						<iframe id='actividades' src='<?=$CFG->wwwroot?>/mtto/rutinas.php?mode=listar_actividades&id_rutina=<?=$rutina["id"]?>' width='100%' height='200' frameborder='0'></iframe>
					</td>
				</tr>
				<tr>
					<td>
						<iframe id='mediciones' src='<?=$CFG->wwwroot?>/mtto/rutinas.php?mode=listar_mediciones&id_rutina=<?=$rutina["id"]?>' width='100%' height='150' frameborder='0'></iframe>
					</td>
				</tr>
				<tr>
					<td>
						<iframe id='talleres' src='<?=$CFG->wwwroot?>/mtto/rutinas.php?mode=listar_talleres&id_rutina=<?=$rutina["id"]?>' width='100%' height='150' frameborder='0'></iframe>
					</td>
				</tr>
				<?
				$entra=$entraEditar=false;
				//los equipo 
				if($rutina["activa"]){
					if($rutina["id_grupo"] != "")
					{
						$idsGrupos=array($rutina["id_grupo"]);
						obtenerIdsGruposAbajo($rutina["id_grupo"],$idsGrupos);
						$qidEq = $db->sql_query("SELECT e.id 
								FROM mtto.equipos e 
								WHERE (e.id_centro IS NULL OR e.id_centro IN (".implode(",",$rutinasCentros).")) AND e.id_grupo IN (".implode(",",$idsGrupos).")");
						while($query = $db->sql_fetchrow($qidEq))
						{
							$qidOT = $db->sql_row("SELECT count(*) as num FROM mtto.ordenes_trabajo WHERE id_equipo='".$query["id"]."' AND id_rutina='".$rutina["id"]."'");
							if($qidOT["num"]==0)
							{
								$entra = true;
								break;	
							}
						}

						//si es para editarla
						//Para ver qué pasa:  se le dice que las muestre siempre, no únicamente las que están preprogramadas.
//WHERE e.id_grupo IN (".implode(",",$idsGrupos).") AND pv.id_rutina='".$rutina["id"]."' AND o.id_estado_orden_trabajo=10 AND o.fecha_ejecucion_inicio IS NULL";
						//John: Deberian ser las que esten en mtto.rutinas_primera_vez y no con ot en fecha_ejecucion_inicio = null
						$strSQL="SELECT pv.*
								FROM mtto.rutinas_primera_vez pv
								LEFT JOIN mtto.ordenes_trabajo o ON o.id=pv.id_orden_trabajo
								LEFT JOIN mtto.equipos e ON e.id=pv.id_equipo
								WHERE e.id_grupo IN (".implode(",",$idsGrupos).") AND pv.id_rutina='".$rutina["id"]."'";
						$qidNPV = $db->sql_query($strSQL);
						if($db->sql_numrows($qidNPV) > 0)
							$entraEditar=true;	
					}elseif($rutina["id_equipo"])
					{
						$qidOT = $db->sql_row("SELECT count(*) as num FROM mtto.ordenes_trabajo WHERE id_equipo='".$rutina["id_equipo"]."' AND id_rutina='".$rutina["id"]."'");
						if($qidOT["num"]==0)
							$entra = true;

						//si es para editarla
						$qidNPV = $db->sql_query("SELECT pv.*
								FROM mtto.rutinas_primera_vez pv
								LEFT JOIN mtto.ordenes_trabajo o ON o.id=pv.id_orden_trabajo
								WHERE pv.id_equipo='".$rutina["id_equipo"]."' AND pv.id_rutina='".$rutina["id"]."' AND o.id_estado_orden_trabajo=10");
						if($db->sql_numrows($qidNPV) > 0)
							$entraEditar=true;	
					}
				}

				if($entra){?>
				<tr>
					<td><a href="javascript:abrirVentanaJavaScript('rut_privez','800','500','/mtto/rutinas.php?mode=primera_vez&id_rutina=<?=$rutina["id"]?>')" class="link_verde">+ Programar Primera Vez +</a></td>
				</tr>
				<?}
				if($entraEditar){?>
				<tr>
					<td><a href="javascript:abrirVentanaJavaScript('rut_privez','800','500','/mtto/rutinas.php?mode=editar_primera_vez&id_rutina=<?=$rutina["id"]?>')" class="link_verde">+ Editar Programar Primera Vez +</a></td>
				</tr>


				<?}?>
			</table>
		</td>
		<?}?>
	</tr>
	<tr>
		<td colspan=3 align="center">
			<?if($newMode == "insertar"){?>
			<input type="submit" class="boton_verde" value="Aceptar"/>
			<?}else{?>
			<input type="button" class="boton_verde" value="Aceptar y cerrar" onclick="aceptarCerrar('cerrar')" />
			<input type="button" class="boton_verde" value="Aceptar sin cerrar" onclick="aceptarCerrar('sincerrar')"/>
			<?}?>
			<input type="button" class="boton_verde" value="Cancelar" onclick="window.close()"/>
		</td>
	</tr>
	</form>
</table>
<script type="text/javascript">

function aceptarCerrar(accion)
{
	if(revisar())
	{
		document.entryform.accion.value = accion;
		document.entryform.submit();
	}
}

function centros_seleccionados(){
	var arrayCentros=new Array();
	for (var i = 0; i < document.entryform.elements.length; i++) {
		if (document.entryform.elements[i].type=='checkbox' && document.entryform.elements[i].name=='mtto.rutinas_centros[]' && document.entryform.elements[i].checked==1){
			arrayCentros[arrayCentros.length]=document.entryform.elements[i].value;
		}
	}
	var strCentros=arrayCentros.join(',');
	return(strCentros);
}

function revisar()
{
	var seleccionadas = 0;
	for (var i = 0; i < document.entryform.elements.length; i++) {
		if (document.entryform.elements[i].type=='checkbox' && document.entryform.elements[i].checked==1){
			seleccionadas++;
		}
	}
	if (seleccionadas==0){
		window.alert('Por favor seleccione: Centro');
		return(false);
	}

	if(document.entryform.rutina.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Rutina');
		document.entryform.rutina.focus();
		return(false);
	}
	else{
		var regexpression=/^.{1,1055}$/m;
		if(!regexpression.test(document.entryform.rutina.value)){
			window.alert('[Rutina] no contiene un dato válido.');
			document.entryform.rutina.focus();
			return(false);
		}
	}
	if(document.entryform.id_tipo_mantenimiento.options[document.entryform.id_tipo_mantenimiento.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Tipo');
		document.entryform.id_tipo_mantenimiento.focus();
		return(false);
	}
	if(document.entryform.id_prioridad.options[document.entryform.id_prioridad.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Prioridad');
		document.entryform.id_prioridad.focus();
		return(false);
	}
	if(document.entryform.id_sistema.options[document.entryform.id_sistema.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Sistema');
		document.entryform.id_sistema.focus();
		return(false);
	}

	<?if(isset($sinEquipo)){?>
	if(document.entryform.id_grupo.options[document.entryform.id_grupo.selectedIndex].value=='%')
	{
		window.alert('Por favor seleccione Grupo');
		return(false);
	}
	<?}else{?>
	if(document.entryform.id_grupo.options[document.entryform.id_grupo.selectedIndex].value=='%' && document.entryform.id_equipo.options[document.entryform.id_equipo.selectedIndex].value=='%')
	{
		window.alert('Por favor seleccione Grupo o Equipo');
		return(false);
	}
	if(document.entryform.id_grupo.options[document.entryform.id_grupo.selectedIndex].value!='%' && document.entryform.id_equipo.options[document.entryform.id_equipo.selectedIndex].value!='%')
	{
		window.alert('Por favor seleccione Grupo o Equipo');
		return(false);
	}
	<?}?>

	if(document.entryform.lugar.options[document.entryform.lugar.selectedIndex].value=='%'){
		window.alert('Por favor seleccione: Lugar');
		document.entryform.lugar.focus();
		return(false);
	}
	if(document.entryform.frec_horas.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.frec_horas.value)){
			window.alert('[Frecuencia Horas] no contiene un dato válido.');
			document.entryform.frec_horas.focus();
			return(false);
		}
	}
	if(document.entryform.frec_km.value !=''){
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.frec_km.value)){
			window.alert('[Frecuencia Km] no contiene un dato válido.');
			document.entryform.frec_km.focus();
			return(false);
		}
	}

	if(document.entryform.id_tipo_mantenimiento.options[document.entryform.id_tipo_mantenimiento.selectedIndex].value!='2'){
		if(document.entryform.id_frecuencia.options[document.entryform.id_frecuencia.selectedIndex].value=='%' && document.entryform.frec_horas.value.replace(/ /g, '') =='' && document.entryform.frec_km.value.replace(/ /g, '') =='')
		{
			window.alert('Por favor seleccione alguna frecuencia');
			return(false);
		}
	}
	if(document.entryform.fec_cumplir.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: No. de frecuencias a cumplir');
		document.entryform.fec_cumplir.focus();
		return(false);
	}
	else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.fec_cumplir.value)){
			window.alert('[No. de frecuencias a cumplir] no contiene un dato válido.');
			document.entryform.fec_cumplir.focus();
			return(false);
		}
	}
	if(document.entryform.tiempo_ejecucion.value.replace(/ /g, '') ==''){
		window.alert('Por favor escriba: Tiempo Ejecución (minutos)');
		document.entryform.tiempo_ejecucion.focus();
		return(false);
	}
	else{
		var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
		if(!regexpression.test(document.entryform.tiempo_ejecucion.value)){
			window.alert('[Tiempo Ejecución (minutos)] no contiene un dato válido.');
			document.entryform.tiempo_ejecucion.focus();
			return(false);
		}
	}
	if(document.entryform.comentarios.value !=''){
		var regexpression=/./;
		if(!regexpression.test(document.entryform.comentarios.value)){
			window.alert('[Comentarios] no contiene un dato válido.');
			document.entryform.comentarios.focus();
			return(false);
		}
	}

	for(i=1;i<rowNum;i++)
	{
		if(document.getElementById('id_elemento_'+i).value =='%'){
		  window.alert('Por favor seleccione: Elemento');
		  return(false);
		}

		if(document.getElementById('cantidad_'+i).value =='0' || document.getElementById('cantidad_'+i).value ==''){
		  window.alert('La cantidad no puede ser 0 o vacía');
		  return(false);
		}
	
	}

	<?if($newMode != "insertar"){
		if($rutina["id_frecuencia"]=="") $rutina["id_frecuencia"]="%";
		?>
	if(document.entryform.id_frecuencia.options[document.entryform.id_frecuencia.selectedIndex].value != '<?=$rutina["id_frecuencia"]?>' || document.entryform.frec_horas.value.replace(/ /g, '') !='<?=$rutina["frec_horas"]?>' || document.entryform.frec_km.value.replace(/ /g, '') !='<?=$rutina["frec_km"]?>' )	
	{
		if(!confirm('Si cambia las frecuencias, la rutina se debe reprogramar.  Las ordenes de trabajo que no hayan sido cerradas se borrarán.'))
		{
			valor = '%';
			if('<?=$rutina["id_frecuencia"]?>' != '') valor = '<?=$rutina["id_frecuencia"]?>';
			document.entryform.id_frecuencia.value = valor;
			document.entryform.frec_horas.value='<?=$rutina["frec_horas"]?>';
			document.entryform.frec_km.value ='<?=$rutina["frec_km"]?>';

			return(false);
		}
	}

	<?}?>

	return(true);
}

var rowNum=1;
function agrega_celda(){
	var tbl = document.getElementById('tabla_actividades');
	var lastRow = tbl.rows.length;
	var row = tbl.insertRow(lastRow);
	var idRow="row"+rowNum;
	row.setAttribute("id", idRow);

	var cell1 = document.createElement("td");
	var inputElem = document.createElement('input');
	inputElem.id="id_elemento_"+rowNum;
	inputElem.name="id_elemento_"+rowNum;
	inputElem.type = 'hidden';
	inputElem.value = '';
	cell1.appendChild(inputElem);

	var div1 = document.createElement("div");
	div1.setAttribute("class", "yui-skin-sam");
	div1.setAttribute("style","width:25em;padding-bottom:2em;position:relative;");
	var inputElem2 = document.createElement('input');
	inputElem2.id="AC_id_elemento_"+rowNum;
	inputElem2.type = 'text';
	inputElem2.value = '';
	div1.appendChild(inputElem2);
	var div2 = document.createElement("div");
	div2.setAttribute("id", "popup_id_elemento_"+rowNum);
	div2.setAttribute("style","width:25em;");

	var sc = document.createElement('script');
	var codigo = " YAHOO.example.BasicRemote = function() { var oDS = new YAHOO.util.XHRDataSource('<?=$CFG->wwwroot?>/autocomplete2/autocomplete.php'); oDS.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT; oDS.responseSchema = {recordDelim: '\\n', fieldDelim: '\\t' }; oDS.maxCacheEntries = 5; var oAC = new YAHOO.widget.AutoComplete(\"AC_id_elemento_"+rowNum+"\", \"popup_id_elemento_"+rowNum+"\", oDS); oAC.maxResultsDisplayed = 30; oAC.generateRequest = function(sQuery) { return \"?dirroot=%2Fvar%2Fwww%2Fhtml%2Fpa&module=mtto.rutinas_elementos&field=id_elemento&strCentros=\" + centros_seleccionados()  + \"&s=\" + sQuery ; }; var myHiddenField = YAHOO.util.Dom.get(\"id_elemento_"+rowNum+"\"); var myHandler = function(sType, aArgs) { var myAC = aArgs[0]; var elLI = aArgs[1]; myHiddenField.value = aArgs[2][1]; }; oAC.itemSelectEvent.subscribe(myHandler); return { oDS: oDS, oAC: oAC }; }();";

	var tt = document.createTextNode(codigo);
	sc.appendChild(tt);
	cell1.appendChild(sc);

	div1.appendChild(div2);
	cell1.appendChild(div1);


	var cell2 = document.createElement("td");
	cell2.style.textAlign="center";
	var inputCant = document.createElement('input');
	inputCant.id="cantidad_"+rowNum;
	inputCant.type = 'text';
	inputCant.value = '0';
	inputCant.size = 4;
	inputCant.name = 'cantidad_'+rowNum;
	inputCant.style.textAlign="center";
	cell2.appendChild(inputCant);


	var cell3 = document.createElement("td");
	cell3.style.textAlign="center";
	var a = document.createElement("a");
	var txt = document.createTextNode("B");
	a.appendChild(txt);
	a.href = "javascript:delete_celda('"+idRow+"')";
	a.title = "Borrar";
	a.className = "link_verde";
	cell3.appendChild(a);


	row.appendChild(cell1);
	row.appendChild(cell2);
	row.appendChild(cell3);

	rowNum+=1;
}

function delete_celda(id_row){
	if(confirm("¿Esta seguro de borrar el elemento?")){
		var tbl = document.getElementById('tabla_actividades');
		var row = document.getElementById(id_row);
		tbl.getElementsByTagName("tbody")[0].removeChild(row);
	}
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
function updateRecursive_id_equipo(select){
	namediv='id_equipo';
	nameId='id_equipo';
	id=select.options[select.selectedIndex].value;
	document.getElementById(namediv).innerHTML='<select id="' + nameId + '"><option>Actualizando...<\/select>';
	var url="/lib/ajaxUpdateEquiposRutinas.php?id=" + id;
	oXmlHttp_id_equipo=GetHttpObject(cambiarRecursive_id_equipo);
	oXmlHttp_id_equipo.open("GET", url , true);
	oXmlHttp_id_equipo.send(null);
}
function cambiarRecursive_id_equipo(){
	if (oXmlHttp_id_equipo.readyState==4 || oXmlHttp_id_equipo.readyState=="complete"){
		document.getElementById('id_equipo').innerHTML=oXmlHttp_id_equipo.responseText
	}
}


function cargarValorTiempoRutinaDos(valor)
{
	document.entryform.tiempo_ejecucion.value=valor;	
}


</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>

