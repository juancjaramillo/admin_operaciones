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
			{key:"marca", label:"Marca", sortable: true},
			{key:"disenio", label:"Diseño", sortable: true},
			{key:"total", label:"$/Km", sortable: true}
			];

		var myDataSource = new YAHOO.util.DataSource(YAHOO.example.Data.addresses);
		myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
		myDataSource.responseSchema = {
			fields: ["marca", "disenio", "total"]
		}

		var myConfigs = {};

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

</script>