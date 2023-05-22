<div height="100%">
	<table class="tabla_grande">
		<tr>
			<td align="left" class="link_verde"><?if($esquema=="rec") echo "TRIPULACIÓN"; else echo "OPERARIOS";?></td>
			<td align="right"><?if(!$movimientoCerrado){ if($esquema=="rec"){?><a href="javascript:abrirVentanaJavaScript('desplazamientos','500','500','<?=$CFG->wwwroot?>/opera/templates/tripulacion_form.php?id_movimiento=<?=$idMovimiento?>')" class="link_verde" title="Agregar Tripulación">Agregar Tripulación</a><?} else {?><a href="javascript:abrirVentanaJavaScript('desplazamientos','500','500','<?=$CFG->wwwroot?>/opera/templates/operario_barrido_form.php?id_movimiento=<?=$idMovimiento?>')" class="link_verde" title="Agregar Operario">Agregar Operario</a><?}}?></td>
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
			<?if($esquema=="rec"){?>
			{key:"cargo", label:"Cargo", sortable: true},
			<?}?>
			{key:"persona", label:"Persona", sortable: true},
			{key:"hora_inicio",label: "Inicio", sortable: true, editor: new YAHOO.widget.TextboxCellEditor({
				LABEL_CANCEL:"Cerrar", 
				LABEL_SAVE:"Guardar",
				asyncSubmitter: function (callback, newValue) {
					var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/;
					if(!regexpression.test(newValue)){
						window.alert('Hora Inicio no contiene un dato válido.');
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
							'<?=$CFG->wwwroot?>/opera/movimientos_<?=$esquema?>.php', 
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
							'action=cellEdit&column=' + column.key + '&newValue=' + escape(newValue) + '&oldValue=' + escape(oldValue)+'&mode=actualizar_datos_operarios&esquema=<?=$esquema?>&campo=hora_inicio' + primaryKey 
						); 
					} 
				}
			})},
			{key:"hora_fin", label:"Fin", sortable: true},
			{key:"opciones", label:"Finalizar"}
		];

		var myDataSource = new YAHOO.util.DataSource(YAHOO.example.Data.addresses);
		myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
		myDataSource.responseSchema = {
			<?if($esquema=="rec"){?>
			fields: [{key:"id"}, "cargo", "persona", "hora_inicio","hora_fin", "opciones"]
			<?}else{?>
			fields: [{key:"id"}, "persona", "hora_inicio","hora_fin", "opciones"]
			<?}?>
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
		myDataTable.subscribe('cellClickEvent',function(ev) {
			var seguir = false;
			var target = YAHOO.util.Event.getTarget(ev);
			var record = this.getRecord(target);
			var cols = this.getColumnSet().keys;
			for (var i = 0; i < cols.length; i++) {
				if(cols[i].key=="hora_fin" && record.getData(cols[i].key) == "")
					seguir = true;
			}
			if(seguir)
				myDataTable.onEventShowCellEditor(ev);
		});




		var oColumn = myDataTable.getColumn(0);
		myDataTable.hideColumn(oColumn);

		return {
			oDS: myDataSource, oDT: myDataTable
		};
	}();
});


function cerrar(id)
{
	url = '<?=$CFG->wwwroot?>/opera/movimientos_<?=$esquema?>.php?newValue=<?=date("Y-m-d H:i:s")?>&mode=actualizar_datos_operarios_get&esquema=<?=$esquema?>&campo=hora_fin&reload=true&id='+id;
	abrirVentanaJavaScript('actdes','1','1',url);
}

function eliminar(id)
{
	url = '<?=$CFG->wwwroot?>/opera/movimientos_<?=$esquema?>.php?mode=eliminar_operario&esquema=<?=$esquema?>&id='+id;
	abrirVentanaJavaScript('actdes','1','1',url);
}

</script>
<?include($CFG->templatedir . "/resize_window.php");?>
