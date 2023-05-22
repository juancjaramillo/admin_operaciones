<?
include($CFG->dirroot."/templates/header_popup_tabview.php");
?>
<div id="center1">
	<div height="100%">
		<table class="tabla_grande"> 
			<tr>
				<td height="50" width="80%" valign="middle" class="azul_16" align="center"><?=$titulo?></td>
			</tr>
			<tr>
				<td align="center" width="100%"><div id="cellediting"> </div>	</td>
			</tr>
		</table>
	</div>
</div>
<?include($CFG->dirroot."/templates/footer.php");?>

<script type="text/javascript">

YAHOO.example.Data = { addresses: [<?=implode(",",$datos)?>]}
YAHOO.util.Event.addListener(window, "load", function() {
		YAHOO.example.InlineCellEditing = function() {
		// Custom formatter for "address" column to preserve line breaks
		var formatAddress = function(elCell, oRecord, oColumn, oData) {
		elCell.innerHTML = "<pre class=\"address\">" + oData + "</pre>";
		};

		var myColumnDefs = [
			{key:"centro", label:"Centro", sortable: true},
				{key:"codigo", label:"Código", sortable: true},
				{key:"placa", label:"Placa", sortable: true},
				{key:"kilometraje", label:"Km", sortable: true},
				{key:"horometro", label:"Horo", sortable: true},
				{key:"tipo", label:"Tipo", sortable: true},
				{key:"estado", label:"Estado", sortable: true},
				{key:"opciones", label:"Opciones"},
			];

		var myDataSource = new YAHOO.util.DataSource(YAHOO.example.Data.addresses);
		myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
		myDataSource.responseSchema = {
			fields: ["centro", "codigo", "placa", "kilometraje", "horometro", "tipo", "estado", "opciones"]
		}

		var myConfigs = {
			paginator : new YAHOO.widget.Paginator({
				rowsPerPage: 30,
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
		

		return {
			oDS: myDataSource,
			oDT: myDataTable
		};
		}();
});


function detalles(id)
{
	url = '<?=$CFG->wwwroot."/mtto/listado_hoja_vida_vehiculo.php?mode=detalles&id="?>'+id;
	abrirVentanaJavaScript('vehiculo','800','500',url);
}

</script>