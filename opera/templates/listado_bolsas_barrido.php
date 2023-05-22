<div height="100%">
	<table class="tabla_grande">
		<tr>
			<td align="left" class="link_verde">BOLSAS</td>
			<td align="right"><?if(!$movimientoCerrado){?> <a href="javascript:abrirVentanaJavaScript('desplazamientos','500','500','<?=$CFG->wwwroot?>/opera/templates/bolsas_form.php?id_movimiento=<?=$idMovimiento?>')" class="link_verde" title="Agregar Bolsas">Agregar Bolsas</a><?}?></td>
		</tr>
		<tr>
			<td colspan=2 align="center" width="100%"> <div id="cellediting"> </div></td>
		</tr>
	</table>
</div>

<script type="text/javascript">
YAHOO.example.Data = { addresses: [<?=implode(",",$data)?>]}

YAHOO.util.Event.addListener(window, "load", function() {
	YAHOO.example.InlineCellEditing = function() {

		var myColumnDefs = [
			{key:"id"},
			{key:"tipo", label:"Tipo Bolsa", sortable: true},
			<?if(!$movimientoCerrado){?>
			{key:"numero_inicio",label: "Iniciales", sortable: true, editor: new YAHOO.widget.TextboxCellEditor({
				LABEL_CANCEL:"Cerrar", 
				LABEL_SAVE:"Guardar",
				asyncSubmitter: function (callback, newValue) {
					var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
					if(!regexpression.test(newValue)){
						window.alert('Iniciales no contiene un dato válido.');
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
							'POST',
							'<?=$CFG->wwwroot?>/opera/movimientos_bar.php', 
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
							'action=cellEdit&column=' + column.key + '&newValue=' + escape(newValue) + '&oldValue=' + escape(oldValue)+'&mode=actualizar_datos_bolsas&campo=numero_inicio' + primaryKey 
						); 
					} 
				}
			})},
			{key:"numero_fin",label: "Finales", sortable: true, editor: new YAHOO.widget.TextboxCellEditor({
				LABEL_CANCEL:"Cerrar", 
				LABEL_SAVE:"Guardar",
				asyncSubmitter: function (callback, newValue) {
					var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
					if(!regexpression.test(newValue)){
						window.alert('Finales no contiene un dato válido.');
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
							'POST',
							'<?=$CFG->wwwroot?>/opera/movimientos_bar.php', 
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
							'action=cellEdit&column=' + column.key + '&newValue=' + escape(newValue) + '&oldValue=' + escape(oldValue)+'&mode=actualizar_datos_bolsas&campo=numero_fin' + primaryKey 
						); 
					} 
				}
			})}
			<?}else{?>
			{key:"numero_inicio",label: "Iniciales", sortable: true},
			{key:"numero_fin",label: "Finales", sortable: true}
			<?}?>
		];

		var myDataSource = new YAHOO.util.DataSource(YAHOO.example.Data.addresses);
		myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
		myDataSource.responseSchema = {
			fields: [{key:"id"}, "tipo", "numero_inicio","numero_fin"]
		}

		
		var myDataTable = new YAHOO.widget.DataTable("cellediting", myColumnDefs, myDataSource, {
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
			oDS: myDataSource, oDT: myDataTable
		};
	}();
});

</script>
<?include($CFG->templatedir . "/resize_window.php");?>
