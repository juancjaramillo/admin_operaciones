<div height="100%">
	<table class="tabla_grande">
		<tr>
			<td height="80" width="100%" valign="middle" class="azul_16" align="center">ACTUALIZAR HORA FIN Y BOLSAS FINALES DE LOS MOVIMIENTOS DEL DÍA <?=strtoupper(strftime("%A %d de %B de %Y",strtotime($fecha)))?></td>
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

		var myColumnDefs = [
			{key:"id"},
			<?foreach($tp as $key => $dx){ $name = preg_replace("/[^0-9a-z_.]/i","_",$dx);?>
			{key:"id_movimiento_bolsa_<?=$name?>"},
			<?}?>
			{key:"codigo", label:"Código", sortable: true},
			{key:"inicio", label:"Hora Inicio", sortable: true},
			{key:"final",label: "Hora Fin", sortable: true, editor: new YAHOO.widget.TextboxCellEditor({
				LABEL_CANCEL:"Cerrar", 
				LABEL_SAVE:"Guardar",
				asyncSubmitter: function (callback, newValue) {
					var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
					if(!regexpression.test(newValue)){
						window.alert('Hora Fin no contiene un dato válido.');
						callback();
					}else{
						var record = this.getRecord();
						var primaryKey ="";
						var datatable = this.getDataTable();
						var cols = datatable.getColumnSet().keys;
						for (var i = 0; i < cols.length; i++) {
							if (cols[i].key=="id") {
								primaryKey = '&id_movimiento=' + escape(record.getData(cols[i].key));
							}
						}
						record,
						column = this.getColumn(),
						oldValue = this.value, datatable;
						YAHOO.util.Connect.asyncRequest(
							'POST', '<?=$CFG->wwwroot?>/opera/movimientos_bar.php', {
								success:function(o) {
								callback(true,newValue);
								} ,
								failure:function(o) {
									alert(o.statusText);
									callback();
								},
							scope:this
						},
						'action=cellEdit&column=' + column.key + '&fecha=' + escape(newValue) + '&oldValue=' + escape(oldValue)+'&mode=cerrarMovimientoBarDesdeListadoFinal'+ primaryKey 
						); 
					} 
				}
				})},
			<?foreach($tp as $key => $dx){
				$name = preg_replace("/[^0-9a-z_.]/i","_",$dx);?>
			{key:"inicio_<?=$name?>", label:"<?=$dx?> Iniciales", sortable: true, editor: new YAHOO.widget.TextboxCellEditor({
				LABEL_CANCEL:"Cerrar", 
				LABEL_SAVE:"Guardar",
				asyncSubmitter: function (callback, newValue) {
					var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
					if(!regexpression.test(newValue)){
						window.alert('<?=$dx?> Iniciales no contiene un dato válido.');
						callback();
					}else{
						var record = this.getRecord();
						var primaryKey ="";
						var datatable = this.getDataTable();
						var cols = datatable.getColumnSet().keys;
						for (var i = 0; i < cols.length; i++) {
							if (cols[i].key=="id_movimiento_bolsa_<?=$name?>") {
								primaryKey = '&id_movimiento_bolsa=' + escape(record.getData(cols[i].key));
							}
						}
						record,
						column = this.getColumn(),
						oldValue = this.value, datatable;
						if(oldValue != "NA")
						{
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
								'action=cellEdit&column=' + column.key + '&newValue=' + escape(newValue) + '&oldValue=' + escape(oldValue)+'&mode=actualizar_datos_bolsasDesdeListadoFinal&campo=numero_inicio' + primaryKey 
							); 
						}else
						{
							window.alert("No existe la bolsa para éste movimiento");
							window.location.reload();
						}
					} 
				}
			})},
			{key:"final_<?=$name?>",label: "<?=$dx?> Finales", sortable: true, editor: new YAHOO.widget.TextboxCellEditor({
				LABEL_CANCEL:"Cerrar", 
				LABEL_SAVE:"Guardar",
				asyncSubmitter: function (callback, newValue) {
					var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
					if(!regexpression.test(newValue)){
						window.alert('<?=$dx?> Finales no contiene un dato válido.');
						callback();
					}else{
						var record = this.getRecord();
						var primaryKey ="";
						var datatable = this.getDataTable();
						var cols = datatable.getColumnSet().keys;
						for (var i = 0; i < cols.length; i++) {
							if (cols[i].key=="id_movimiento_bolsa_<?=$name?>") {
								primaryKey = '&id_movimiento_bolsa=' + escape(record.getData(cols[i].key));
							}
						}
						record,
						column = this.getColumn(),
						oldValue = this.value, datatable;
						if(oldValue != "NA")
						{
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
								'action=cellEdit&column=' + column.key + '&newValue=' + escape(newValue) + '&oldValue=' + escape(oldValue)+'&mode=actualizar_datos_bolsasDesdeListadoFinal&campo=numero_fin' + primaryKey 
							); 
						}else
						{
							window.alert("No existe la bolsa para éste movimiento");
							window.location.reload();
						}
					} 
				}
			})},
			<?}?>
			{key:"opciones", label:"Opciones"}
		];

		var myDataSource = new YAHOO.util.DataSource(YAHOO.example.Data.addresses);
		myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
		myDataSource.responseSchema = {
			fields: [<?=$fields?>]
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
		<?
		$i=1;
		foreach($tp as $key => $dx){?>
		var oColumn_<?=$i?> = myDataTable.getColumn(<?=$i?>);
		myDataTable.hideColumn(oColumn_<?=$i?>);
		<?$i++;}?>

		return {
			oDS: myDataSource, oDT: myDataTable
		};
	}();
});

function cerrar_movimiento(id_movimiento)
{
	url = '<?=$CFG->wwwroot?>/opera/movimientos_bar.php?mode=cerrarMovimiento&id_movimiento='+id_movimiento+'&esquema=bar';
	abrirVentanaJavaScript('movimientos','900','500',url);
}



</script>
<?include($CFG->templatedir . "/resize_window.php");?>
