<?
include($CFG->dirroot."/templates/header_2panel.php");
?>
<div id="right1">
<?include($CFG->dirroot."/mtto/templates/opciones.php");?>
</div>
<div id="center1">

<div height="100%">
	<table class="tabla_grande"> 
		<tr>
			<td height="50" width="80%" valign="middle" class="azul_16" align="center"><?=$titulo?></td>
		</tr>
		<tr>
			<td align="center" width="100%" valign="top"> <div id="cellediting"> </div></td>
		</tr>
		<tr><td height="15">&nbsp;</td></tr>
	</table>
</div>
</div>
<?include($CFG->dirroot."/templates/footer_2panel.php");?>

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
			{key:"centro", label:"Centro", sortable: true},
			{key:"numero", label:"Número", sortable: true},
			{key:"disenio", label:"Diseño", sortable: true},
			{key:"tipo", label:"Tipo", sortable: true},
			{key:"dimension", label:"Marca / Dimensión", sortable: true},
			{key:"proveedor", label:"Proveedor", sortable: true},
			{key:"vehiculo", label:"Vehículo", sortable: true},
			{key:"posicion", label:"Posición", sortable: true},
			{key:"opciones", label:"Opciones"}
			];

		var myDataSource = new YAHOO.util.DataSource(YAHOO.example.Data.addresses);
		myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
		myDataSource.responseSchema = {
			fields: ["centro", "numero", "disenio", "tipo", "dimension", "proveedor", "vehiculo", "posicion", {key:"id"},  "opciones"]
		}

		var myConfigs = {
			paginator : new YAHOO.widget.Paginator({
				rowsPerPage: 20,
				firstPageLinkLabel:'<< primera',
				lastPageLinkLabel:'última >>',
				nextPageLinkLabel: 'siguiente >',
				previousPageLinkLabel: '< anterior'
			})
		};

		var myDataTable = new YAHOO.widget.DataTable("cellediting", myColumnDefs, myDataSource, myConfigs, {
				 dateOptions:{format:'%Y/%m/%d'}
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


function edicion(id)
{
	url = '<?=$CFG->wwwroot."/mtto/llantas.php?mode=editar&id="?>'+id;
	abrirVentanaJavaScript('llanta_form','800','500',url);
}


function movimientos(id)
{
	url = '<?=$CFG->wwwroot."/mtto/llantas.php?mode=listar_movimientos&id="?>'+id;
	abrirVentanaJavaScript('listar_movimientos','800','500',url);
}




</script>
