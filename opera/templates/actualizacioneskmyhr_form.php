<div height="100%">
	<table class="tabla_grande">
		<tr>
			<td height="50" width="100%" valign="middle" class="azul_16" align="center">ACTUALIZACIONES DE KILOMETRAJE Y HORÓMETRO</td>
		</tr>
		<tr>
		  <td align="center" width="100%"> <div id="cellediting"> </div></td>
		</tr>
	</table>
</div>

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
		{key:"equipo", label:"EQUIPO"},
		{key:"grupo", label:"GRUPO"},
		{key:"kilometraje",label: "KILOMETRAJE", sortable: true, editor: new YAHOO.widget.TextboxCellEditor({
			LABEL_CANCEL:"Cerrar", 
			LABEL_SAVE:"Guardar",
			asyncSubmitter: function (callback, newValue) {
				var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
				if(!regexpression.test(newValue)){
					window.alert('Kilometraje no contiene un dato válido.');
					callback();
				}else{
					var record = this.getRecord();
					var primaryKey ="";
					var datatable = this.getDataTable();
					var cols = datatable.getColumnSet().keys;
					for (var i = 0; i < cols.length; i++) {
						if (cols[i].key=="id") {
							primaryKey = '&' + cols[i].key + '=' + escape(record.getData(cols[i].key));
						}
					}
					record,
					column = this.getColumn(),
					oldValue = this.value,
					datatable;
					YAHOO.util.Connect.asyncRequest(
						'POST',	'actualizacioneskmyhr.php', 
						{
							success:function(o) {
								callback(true,newValue);
							} ,
							failure:function(o) {
								alert(o.statusText);
								callback();
							},
							scope:this
						},
						'action=cellEdit&column=' + column.key + '&newValue=' + escape(newValue) + '&oldValue=' + escape(oldValue)+'&mode=kilometraje' + primaryKey 
					); 
				} 
			}
		})
	},
	{key:"horometro",label: "HORÓMETRO", sortable: true, editor: new YAHOO.widget.TextboxCellEditor({
			LABEL_CANCEL:"Cerrar", 
			LABEL_SAVE:"Guardar",
			asyncSubmitter: function (callback, newValue) {
				var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
				if(!regexpression.test(newValue)){
					window.alert('Horómetro no contiene un dato válido.');
					callback();
				}else{
					var record = this.getRecord();
					var primaryKey ="";
					var datatable = this.getDataTable();
					var cols = datatable.getColumnSet().keys;
					for (var i = 0; i < cols.length; i++) {
						if (cols[i].key=="id") {
							primaryKey = '&' + cols[i].key + '=' + escape(record.getData(cols[i].key));
						}
					}
					record,
					column = this.getColumn(),
					oldValue = this.value,
					datatable;
					YAHOO.util.Connect.asyncRequest(
						'POST',	'actualizacioneskmyhr.php', 
						{
							success:function(o) {
								callback(true,newValue);
							} ,
							failure:function(o) {
								alert(o.statusText);
								callback();
							},
							scope:this
						},
						'action=cellEdit&column=' + column.key + '&newValue=' + escape(newValue) + '&oldValue=' + escape(oldValue)+'&mode=horometro' + primaryKey 
					); 
				} 
			}
		})
	}
	];

	var myDataSource = new YAHOO.util.DataSource(YAHOO.example.Data.addresses);
	myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
	myDataSource.responseSchema = {
		fields: ["equipo","grupo","kilometraje","horometro", {key:"id"}]
	}	

	var myDataTable = new YAHOO.widget.DataTable("cellediting", myColumnDefs, myDataSource, {
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
</script>
