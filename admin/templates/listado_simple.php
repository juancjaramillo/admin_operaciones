<?
$agregar = true;

?>


<?//listado de los iframes?>
<script language="JavaScript" type="text/javascript">
<!--
/**
 * This array is used to remember mark status of rows in browse mode
 */
var marked_row = new Array;


/**
 * Sets/unsets the pointer and marker in browse mode
 *
 * @param   object    the table row
 * @param   interger  the row number
 * @param   string    the action calling this script (over, out or click)
 * @param   string    the default background color
 * @param   string    the color to use for mouseover
 * @param   string    the color to use for marking a row
 *
 * @return  boolean  whether pointer is set or not
 */
function setPointer(theRow, theRowNum, theAction, theDefaultColor, thePointerColor, theMarkColor)
{
	var theCells = null;

	// 1. Pointer and mark feature are disabled or the browser can't get the
	//    row -> exits
	if ((thePointerColor == '' && theMarkColor == '')
			|| typeof(theRow.style) == 'undefined') {
		return false;
	}

	// 2. Gets the current row and exits if the browser can't get it
	if (typeof(document.getElementsByTagName) != 'undefined') {
		theCells = theRow.getElementsByTagName('td');
	}
	else if (typeof(theRow.cells) != 'undefined') {
		theCells = theRow.cells;
	}
	else {
		return false;
	}

	// 3. Gets the current color...
	var rowCellsCnt  = theCells.length;
	var domDetect    = null;
	var currentColor = null;
	var newColor     = null;
	// 3.1 ... with DOM compatible browsers except Opera that does not return
	//         valid values with "getAttribute"
	if (typeof(window.opera) == 'undefined'
			&& typeof(theCells[0].getAttribute) != 'undefined') {
		currentColor = theCells[0].getAttribute('bgcolor');
		domDetect    = true;
	}
	// 3.2 ... with other browsers
	else {
		currentColor = theCells[0].style.backgroundColor;
		domDetect    = false;
	} // end 3

	// 4. Defines the new color
	// 4.1 Current color is the default one
	if(currentColor == null) currentColor='#ffffff';
	if (currentColor == ''
			|| currentColor.toLowerCase() == theDefaultColor.toLowerCase()) {
		if (theAction == 'over' && thePointerColor != '') {
			newColor              = thePointerColor;
		}
		else if (theAction == 'click' && theMarkColor != '') {
			newColor              = theMarkColor;
			marked_row[theRowNum] = true;
		}
	}
	// 4.1.2 Current color is the pointer one
	else if (currentColor.toLowerCase() == thePointerColor.toLowerCase()
			&& (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])) {
		if (theAction == 'out') {
			newColor              = theDefaultColor;
		}
		else if (theAction == 'click' && theMarkColor != '') {
			newColor              = theMarkColor;
			marked_row[theRowNum] = true;
		}
	}
	// 4.1.3 Current color is the marker one
	else if (currentColor.toLowerCase() == theMarkColor.toLowerCase()) {
		if (theAction == 'click') {
			newColor              = (thePointerColor != '')
				? thePointerColor
				: theDefaultColor;
			marked_row[theRowNum] = (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])
				? true
				: null;
		}
	} // end 4

	// 5. Sets the new color...
	if (newColor) {
		var c = null;
		// 5.1 ... with DOM compatible browsers except Opera
		if (domDetect) {
			for (c = 0; c < rowCellsCnt; c++) {
				theCells[c].setAttribute('bgcolor', newColor, 0);
			} // end for
		}
		// 5.2 ... with other browsers
		else {
			for (c = 0; c < rowCellsCnt; c++) {
				theCells[c].style.backgroundColor = newColor;
			}
		}
	} // end 5

		return true;
	} // end of the 'setPointer()' function

	function abrirVentanaNueva(name,width,height){
		izq=(screen.width-width)/2;
		arriba=(screen.height-height)/2;
		return window.open('',name,'scrollbars=yes,width=' + width +',height=' + height +',resizable=yes,left='+izq+',top='+arriba);
	}

function abrirVentanaJavaScript(name,width,height,url,scrollbars){
	vent=abrirVentanaNueva(name,width,height,scrollbars);
	vent.location.href=url;
	vent.focus();
}


	function buscar() {
		document.entryform.mode.value='buscar';
		ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>=abrirVentanaNueva('ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>',700,500);
		ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>.focus();
		ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>.opener.name='opener';
		document.entryform.target='ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>';
		document.entryform.submit();
		return;
	}

	function agregar() {
		document.entryform.mode.value='agregar';
		ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>=abrirVentanaNueva('ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>',700,500);
		ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>.focus();
		document.entryform.target='ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>';
		document.entryform.submit();
		return;
	}

	function editar(modo) {
		var seleccionadas = "0";
		var seleccionada=0;
		for (var i = 0; i < document.entryform.elements.length; i++) {
			if (document.entryform.elements[i].name=='id' && document.entryform.elements[i].checked==1){
				seleccionadas++;
				document.entryform.id.value=document.entryform.elements[i].value;
				seleccionada=i;
			}
		}
		if (seleccionadas==0){
			window.alert('No seleccionó ningún elemento');
			return;
		}
		if (seleccionadas>1){
			window.alert('Debe escoger sólo un elemento para editar');
			return;
		}
		document.entryform.mode.value=modo;
		document.entryform.elements[seleccionada].name='id';
		ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>=abrirVentanaNueva('ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>',700,500);
		ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>.focus();
		document.entryform.target='ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>';
		document.entryform.submit();
		return;
	}

	function aprobar(){
		document.entryform.mode.value='actualizar';
		//document.entryform.action='modules/aprobar.php';
    document.entryform.submit();
	}

	function addRelationship() {
		var seleccionadas = 0;
		var texto_ids="";
		for (var i = 0; i < document.entryform.elements.length; i++) {
			if (document.entryform.elements[i].type=='checkbox' && document.entryform.elements[i].checked==1){
				seleccionadas++;
				if(texto_ids!="") texto_ids=texto_ids + ",";
				texto_ids=texto_ids + document.entryform.elements[i].value;
			}
		}
		if (seleccionadas==0){
			window.alert('No seleccionó ningún elemento');
			return;
		}
		parent.list.document.entryform.mode.value="addRelationship";
		parent.list.document.entryform.ids.value=texto_ids;
		parent.list.document.entryform.submit();
	}


	function eliminar() {
		var seleccionadas = 0;
		var texto="";
		for (var i = 0; i < document.entryform.elements.length; i++) {
			if (document.entryform.elements[i].name=='id' && document.entryform.elements[i].checked==1) seleccionadas++;
		}
		if (seleccionadas==0){
			window.alert('No seleccionó ningún elemento');
			return;
		}
		if (seleccionadas==1) texto='¿Está seguro de querer borrar el elemento seleccionado?';
		else texto='¿Está seguro de querer borrar los elementos seleccionados?';
		if (!confirm(texto)) return;

		document.entryform.mode.value='eliminar';
		ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>=abrirVentanaNueva('ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>',1,1);
		ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>.focus();
		document.entryform.target='ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>';
		document.entryform.submit();
		return;
	}

/*****************************************************************************************************
		FUNCION DATE
*********************************************************************************************************/
	function abrir(campo){
	  ruta='<?=$CFG->common_libdir?>/calendar.php?formulario=entryform&nomcampo=' + campo;
		ventana = 'v_calendar';
		window.open(ruta,ventana,'scrollbars=yes,width=250,height=350,screenX=100,screenY=100');
  }

/****************************************************************************************************************
		FUNCIONES DEL INPUT BUSCAR
***************************************************************************************************************/

function psBuscar(select,from,where,order,field,input,edit){
	//select:Los campos (con sus respectivos alias) que se van a mostrar
  //field:El campo asociado al input
  string='texto=document.entryform.' + input + '__1' + edit +'.value';
  select = select + ', ' + 'id';
  eval(string);
  url='busqueda_edit.php?select=' + select + '&from=' + from + '&where=' + where + '&order=' + order + '&field=' + field + '&input=' + input + '&texto=' + texto + '&edit=' + edit;  psPopup(500,400,url);
}
function psPopup(width, height, url){
	izq=(screen.width-width)/2;
	arriba=(screen.height-height)/2;
	ventana='popup';
	vent=window.open(url,ventana,'scrollbars=yes,width=' + width +',height=' + height +',resizable=yes,left='+izq+',top='+arriba);
	vent.focus();
}
function psEnviar(){
	document.entryform.method='GET';
	document.entryform.mode.value='" . $_GET["mode"] . "';
	document.entryform.submit();
}

//***************************************************************************************************************

//-->
</script>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#ffffff" class="">
	<?if($entidad->get("fieldTitleTable")!=""){
			
	?>
			<tr><td><??></td></tr>
	<?}
	?>
  <tr>
    <td bgcolor="<?=$entidad->get("lightBgColor")?>"><table width="100%"  border="0" cellpadding="0" cellspacing="<? echo nvl($CFG->con_encabezado) ? "35" : "1";?>" class="textobco10">
      <tr>
        <td>
<form name="entryform" action="<?=$ME?>" method="get">
	<input type="hidden" name="module" value="<?=$entidad->name?>">
	<input type="hidden" name="mode">
	<input type="hidden" name="table_parent" value="<?=$entidad->tableParent?>">
	<input type="hidden" name="id_parent" value="<?=$entidad->idParent?>">
	<?
		$_GET=array_reverse($_GET);
		  $i=0;
		foreach($_GET AS $label => $value){
		  if($label != "module" && $label != "mode" && $label != "table_parent" && $label != "id_parent"){
		  $arreglo = explode("_",$label);
		  if(count($arreglo)>=2 AND $arreglo[0] == "id" && $i==0){
			  echo "<input type='hidden' name='campo' value='$label'>\n";
				$i=1;
			}
			echo "<input type='hidden' name='$label' value='$value'>\n";
			}
		}		
	?>
          	<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="textobco10">
              <tr> 
                <td colspan="2">
									<table width="100%" border="0" cellspacing="0" cellpadding="3">
                    <tr>
								<td>
											<?if($agregar){?>
												<?if($entidad->btnAdd){?><a title="Agregar" href="javascript:agregar()"><img alt="Agregar" src="<?=$CFG->admin_dir . "/iconos/transparente/icon-add.gif"?>" border="0"></a><?}?>
											<?}?>
											<?if($entidad->btnEdit){?><a title="Editar" href="javascript:editar('editar')"><img alt="Editar" src="<?=$CFG->admin_dir . "/iconos/transparente/iconoeditar.gif"?>" border="0"></a><?}?>
											<?if($entidad->btnDelete){?><a title="Eliminar" href="javascript:eliminar()"><img alt="Eliminar" src="<?=$CFG->admin_dir . "/iconos/transparente/icon-erase.gif"?>" border="0"></a><?}?>
											<?if($entidad->btnDetails){?><a title="Detalles" href="javascript:editar('consultar')"><img alt="Detalles" src="<?=$CFG->admin_dir . "/iconos/transparente/file2.gif"?>" border="0"></a><?}?>
											<?if($entidad->btnDownload){?><a title="Buscar" href="javascript:buscar()"><img alt="Buscar" src="<?=$CFG->admin_dir . "/iconos/transparente/search1.gif"?>" border="0"></a><?}?>
								</td>
											<td width="85%">
												<table width="100%"  border="0" cellpadding="1" cellspacing="0" bgcolor="#ffffff" class="textobco10">
							          	<tr bgcolor="#bfbfac">
														<td align="right" width="35%"><?=$entidad->get("previousPage")?>
														</td>
														<td align="right" nowrap class="style2">
															<?//preguntar($entidad);
															if($entidad->labelModule!="") echo strtoupper($entidad->labelModule); else echo strtoupper($entidad->get("name"))?> - 
														</td>
							            	<td width="180" align="center" nowrap><span class="style2">
															<?=$entidad->numRows?> resultados</span> (Página <?=$entidad->currentPage?> / <?=$entidad->totalPages?>)
														</td>
														<td align="left" width="35%"><?=$entidad->get("nextPage")?>
														</td>
							          	</tr>
							        	</table>          
											</td>
                    </tr>
                  </table>
								</td>
              </tr>
            </table>
            <table width="100%"  border="0" cellpadding="1" cellspacing="1" bgcolor="#999999" class="textobco10">
							<?
							if(!isset($_GET["iframe"])) $marco=0;
							$entidad->rowColor="#cfcfcf";
							echo $entidad->getTitleRow($marco=1);
							$entidad->set("rowColor","#ffffff");
							while($row=$entidad->getRow($marco=1)) {
								echo $row;
							}
							?>
            </table>
</form>

				</td>
      </tr>
    </table>
		</td>
  </tr>
</table>
</body>
</html>
