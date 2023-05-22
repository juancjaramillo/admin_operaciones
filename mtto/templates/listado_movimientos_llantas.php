<?
if($popup)
	include($CFG->dirroot."/templates/header_popup_tabview.php");
else{
	include($CFG->dirroot."/templates/header_2panel.php");
	?>
	<div id="right1">
		<?include($CFG->dirroot."/mtto/templates/opciones.php");?>
	</div>
	<div id="center1">
<?}?>

<div height="100%">
	<table class="tabla_grande" width="100%"> 
		<tr>
			<td height="50" width="80%" valign="middle" class="azul_16" align="center"><?=$titulo?></td>
		</tr>
		<tr>
			<td align="center" width="100%" valign="top"> <div id="cellediting"> </div></td>
		</tr>
		<?if($popup){?>
		<tr><td height="15">&nbsp;</td></tr>
		<tr>
			<td align="right" height="50" valign="center">
				<?if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["eliminarAgregarMovimientoLlanta"])){?>
				<a class="boton_verde" href="javascript:agregar_movimiento('<?=$idLLanta?>')" title="Agregar Movimiento">Agregar Movimiento</a>&nbsp;&nbsp;
				<?}?>
				<a class="boton_verde" href="javascript:window.close()" title="Cerrar">Cerrar</a>&nbsp;&nbsp;
			</td>
		</tr>
		<?}?>
	</table>
</div>
<?
if($popup)
	include($CFG->dirroot."/templates/footer_popup.php");
else
	include($CFG->dirroot."/templates/footer_2panel.php");
?>

<script type="text/javascript">
YAHOO.example.Data = { addresses: [<?=implode(",",$datos)?>]}
YAHOO.util.Event.addListener(window, "load", function() {
		YAHOO.example.InlineCellEditing = function() {
		// Custom formatter for "address" column to preserve line breaks
		var formatAddress = function(elCell, oRecord, oColumn, oData) {
		elCell.innerHTML = "<pre class=\"address\">" + oData + "</pre>";
		};

		var myColumnDefs = [
			{key:"id"},
			{key:"fecha", label:"Fecha", sortable: true, width:100, formatter:YAHOO.widget.DataTable.formatDate},
			{key:"tipo", label:"Tipo Movimiento", sortable: true},
			{key:"km", label:"Km", sortable: true},
			{key:"horas", label:"Horas", sortable: true},
			{key:"vehiculo", label:"Vehículo", sortable: true},
			{key:"posicion", label:"Posición", sortable: true},
			{key:"opciones", label:"Opciones"}
			];

		var myDataSource = new YAHOO.util.DataSource(YAHOO.example.Data.addresses);
		myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
		myDataSource.responseSchema = {
			fields: ["fecha", "tipo", "km", "horas", "vehiculo", "posicion",{key:"id"},  "opciones"]
		}

		var myConfigs = {
			paginator : new YAHOO.widget.Paginator({
				rowsPerPage: <?if($popup) echo "10"; else echo "20";?>,
				firstPageLinkLabel:'<< primera',
				lastPageLinkLabel:'última >>',
				nextPageLinkLabel: 'siguiente >',
				previousPageLinkLabel: '< anterior'
			})
		};

		var myDataTable = new YAHOO.widget.DataTable("cellediting", myColumnDefs, myDataSource, myConfigs,  {
				 dateOptions:{format:'%Y-%m-%d'}
				});

		// Set up editing flow
		var highlightEditableCell = function(oArgs) {
			var elCell = oArgs.target;
			if(YAHOO.util.Dom.hasClass(elCell, "yui-dt-editable")) {
				this.highlightCell(elCell);
			}
		};
		myDataTable.subscribe("cellMouseoverEvent", highlightEditableCell);
		myDataTable.subscribe("cellMouseoutEvent", myDataTable.onEventUnhighlightCell);
		myDataTable.subscribe("cellClickEvent", myDataTable.onEventShowCellEditor);
		var oColumn = myDataTable.getColumn(0);
		myDataTable.hideColumn(oColumn);

		return {
			oDS: myDataSource,
			oDT: myDataTable
		};
		}();
});

function pop_detalles_movimiento(id)
{
	url = '<?=$CFG->wwwroot."/mtto/llantas.php?mode=detalles_movimiento&popup=true&id="?>'+id;
	abrirVentanaJavaScript('listar_movimientos','800','500',url);
}

function editar_movimiento(id)
{
	url = '<?=$CFG->wwwroot."/mtto/llantas.php?mode=editar_movimiento&id="?>'+id;
	window.location.href=url;
}

function detalles_movimiento(id)
{
	url = '<?=$CFG->wwwroot."/mtto/llantas.php?mode=detalles_movimiento&id="?>'+id;
	window.location.href=url;
}

function pop_eliminar_movimiento(id)
{
	if(confirm('¿Desea borrar el movimiento?')){
		url = '<?=$CFG->wwwroot."/mtto/llantas.php?mode=eliminar_movimiento&popup=true&id="?>'+id;
		abrirVentanaJavaScript('eliminar_movimientos','1','1',url);
	}
}

function eliminar_movimiento(id)
{
	if(confirm('¿Desea borrar el movimiento?')){
		url = '<?=$CFG->wwwroot."/mtto/llantas.php?mode=eliminar_movimiento&id="?>'+id;
		window.location.href=url;
	}
}


function agregar_movimiento(id_llanta)
{
	url = '<?=$CFG->wwwroot."/mtto/llantas.php?mode=agregar_movimiento&id_llanta="?>'+id_llanta;
	window.location.href=url;
}

</script>
