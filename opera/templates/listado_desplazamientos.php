<div height="100%">
	<table class="tabla_grande">
		<tr>
			<td align="left" class="link_verde">DESPLAZAMIENTOS</td>
			<td align="right" rowspan=2 valign="top">
				<?if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["opciones_desplazamientos"])){?>
				<?if(!$movimientoCerrado){?><a href="javascript:abrirVentanaJavaScript('desplazamientos','500','500','<?=$CFG->wwwroot?>/opera/templates/desplazamientos_form.php?id_movimiento=<?=$idMovimento?>')" class="link_verde" title="Agregar Deslazamiento">Agregar Desplazamiento</a><br /><?}?>
				<a href="javascript:abrirVentanaJavaScript('apoyos','700','400','<?=$CFG->wwwroot?>/opera/movimientos_rec.php?mode=agregar_apoyo&fecha=<?=strftime("%Y-%m-%d",strtotime($mov["inicio"]))?>&id_vehiculo=<?=$mov["id_vehiculo"]?>&id_movimiento=<?=$idMovimento?>')" class="link_verde" title="Agregar Apoyo">Agregar Apoyo</a>
				<?}?>
			</td>
		</tr>
		<tr>
			<td>
				<span class="azul_12">Ruta: <?=$mov["micro"]?></span><span class="azul_11"> / Vehículo: <?=$mov["vehiculo"]?> / Fecha Inicial: <?=$mov["inicio"]?> / Fecha Final: <?=$mov["final"]?> </span>
			</td>
		</tr>
		<tr>
			<td colspan=2 align="center" width="100%"> <div id="cellediting"> </div></td>
		</tr>
	</table>
</div>

<script type="text/javascript">
YAHOO.example.Data = { addresses: [<?=implode(",",$data)?>], stateAbbrs : [<?=implode(",",$tipos)?>]}

YAHOO.util.Event.addListener(window, "load", function() {
	YAHOO.example.InlineCellEditing = function() {

		var myColumnDefs = [
			{key:"id"},
			{key:"tipo", label:"Tipo", sortable: true, editor: new YAHOO.widget.DropdownCellEditor({
				dropdownOptions:YAHOO.example.Data.stateAbbrs,
				LABEL_CANCEL:"Cerrar", 
				LABEL_SAVE:"Guardar", 
				asyncSubmitter: function (callback, newValue) {
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
						'<?=$CFG->wwwroot?>/opera/movimientos_rec.php', 
						{
							success:function(o) {
								callback(true,newValue);
								window.location.reload();
							} ,
							failure:function(o) {
								alert(o.statusText);
								callback();
							},
							scope:this
						},
						'action=cellEdit&column=' + column.key + '&newValue=' + escape(newValue) + '&oldValue=' + escape(oldValue)+'&mode=actualizar_datos_desplazamientos_rec&campo=id_tipo_desplazamiento' + primaryKey 
					); 
				}	
				})
			},
			{key:"fecha_inicio",label: "Fecha Inicio", sortable: true, editor: new YAHOO.widget.TextboxCellEditor({
				LABEL_CANCEL:"Cerrar", 
				LABEL_SAVE:"Guardar",
				asyncSubmitter: function (callback, newValue) {
					var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2}/;
					if(!regexpression.test(newValue)){
						window.alert('Fecha Inicio no contiene un dato válido.');
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
								'<?=$CFG->wwwroot?>/opera/movimientos_rec.php', 
								{
									success:function(o) {
										callback(true,newValue);
										window.location.reload();
									} ,
									failure:function(o) {
										alert(o.statusText);
										callback();
									},
									scope:this
								},
						'action=cellEdit&column=' + column.key + '&newValue=' + escape(newValue) + '&oldValue=' + escape(oldValue)+'&mode=actualizar_datos_desplazamientos_rec&campo=fecha_inicio' + primaryKey 
					); 
					} 
				}
			})},
			{key:"hora_inicio",label: "Hora Inicio", sortable: true, editor: new YAHOO.widget.TextboxCellEditor({
				LABEL_CANCEL:"Cerrar", 
				LABEL_SAVE:"Guardar",
				asyncSubmitter: function (callback, newValue) {
					var regexpression=/^[0-9]{2}:[0-9]{2}:[0-9]{2}/;
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
								'<?=$CFG->wwwroot?>/opera/movimientos_rec.php', 
								{
									success:function(o) {
										callback(true,newValue);
										window.location.reload();
									} ,
									failure:function(o) {
										alert(o.statusText);
										callback();
									},
									scope:this
								},
						'action=cellEdit&column=' + column.key + '&newValue=' + escape(newValue) + '&oldValue=' + escape(oldValue)+'&mode=actualizar_datos_desplazamientos_rec&campo=hora_inicio' + primaryKey 
					); 
					} 
				}
			})},
			{key:"hora_fin", label:"Fin", sortable: true},
			{key:"numero_viaje", label:"Num Viaje", sortable: true, editor: new YAHOO.widget.TextboxCellEditor({
				validator:YAHOO.widget.DataTable.validateNumber,
				LABEL_CANCEL:"Cerrar", 
				LABEL_SAVE:"Guardar", 
				asyncSubmitter: function (callback, newValue) {
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
						'<?=$CFG->wwwroot?>/opera/movimientos_rec.php', 
						{
							success:function(o) {
								callback(true,newValue);
								window.location.reload();
							} ,
							failure:function(o) {
								alert(o.statusText);
								callback();
							},
							scope:this
						},
						'action=cellEdit&column=' + column.key + '&newValue=' + escape(newValue) + '&oldValue=' + escape(oldValue)+'&mode=actualizar_datos_desplazamientos_rec&campo=numero_viaje' + primaryKey 
					); 
				}}) 
			},
			{key:"km", label:"Km", sortable: true,  editor: new YAHOO.widget.TextboxCellEditor({
				validator:YAHOO.widget.DataTable.validateNumber,
				LABEL_CANCEL:"Cerrar", 
				LABEL_SAVE:"Guardar", 
				asyncSubmitter: function (callback, newValue) {
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
						'<?=$CFG->wwwroot?>/opera/movimientos_rec.php', 
						{
							success:function(o) {
									callback(true,newValue);
									window.location.reload();
								//callback(true,newValue);
							} ,
							failure:function(o) {
								alert(o.statusText);
								callback();
							},
							scope:this
						},
						'action=cellEdit&column=' + column.key + '&newValue=' + escape(newValue) + '&oldValue=' + escape(oldValue)+'&mode=actualizar_datos_desplazamientos_rec&campo=km' + primaryKey 
					); 
				}}) 
			},
			{key:"horometro", label:"Horómetro", sortable: true,  editor: new YAHOO.widget.TextboxCellEditor({
				validator:YAHOO.widget.DataTable.validateNumber,
					LABEL_CANCEL:"Cerrar", 
					LABEL_SAVE:"Guardar", 
				asyncSubmitter: function (callback, newValue) {
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
						'<?=$CFG->wwwroot?>/opera/movimientos_rec.php', 
						{
							success:function(o) {
								callback(true,newValue);
								window.location.reload();
							} ,
							failure:function(o) {
								alert(o.statusText);
								callback();
							},
							scope:this
						},
						'action=cellEdit&column=' + column.key + '&newValue=' + escape(newValue) + '&oldValue=' + escape(oldValue)+'&mode=actualizar_datos_desplazamientos_rec&campo=horometro' + primaryKey 
					); 
				}}) 
			},
			{key:"orden_micro", label:"Orden Pred."},
			{key:"opciones", label:"Cerrar Mov"}
		];

		var myDataSource = new YAHOO.util.DataSource(YAHOO.example.Data.addresses);
		myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
		myDataSource.responseSchema = {
			fields: [{key:"id"}, "tipo", "fecha_inicio", "hora_inicio","hora_fin", "numero_viaje", "km", "horometro", "orden_micro", "opciones"]
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
			var seguir = true;
			var target = YAHOO.util.Event.getTarget(ev);
			var record = this.getRecord(target);
			var cols = this.getColumnSet().keys;

			parada =  this.getColumn(target);
			parada = parada.key;
			
			if(parada != 'km' && parada != 'horometro' )
			{
				for (var i = 0; i < cols.length; i++) {
					if(cols[i].key=="hora_fin" && record.getData(cols[i].key) != "")
					{
						seguir = false;
					}
				}
			}

			for (var i = 0; i < cols.length; i++) {
				if(cols[i].key=="hora_inicio" && record.getData(cols[i].key) == "")
					seguir = false;
			}

			<?if($movimientoCerrado){?>
				seguir = false;
			<?}?>

			<?if(!in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["opciones_desplazamientos"])){?>
				seguir = false;
			<?}?>

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


function cerrar(idMov)
{
	url = '<?=$CFG->wwwroot?>/opera/movimientos_rec.php?mode=actualizar_datos_desplazamientos_rec_get&campo=hora_fin&reload=true&id='+idMov;
	abrirVentanaJavaScript('actdes','1','1',url);
}

function cerrarOtraHora(idDes)
{
	url = '<?=$CFG->wwwroot?>/opera/templates/cerrarConOtraHora_form.php?id_desplazamiento='+idDes;
	abrirVentanaJavaScript('actdesotra','400','300',url);
}

function eliminar(idMov)
{
	url = '<?=$CFG->wwwroot?>/opera/movimientos_rec.php?mode=eliminar_desplazamiento_rec&id='+idMov;
	abrirVentanaJavaScript('actdes','1','1',url);
}

function activar(idDes)
{
	window.location.href='<?=$CFG->wwwroot?>/opera/movimientos_rec.php?mode=activar_desplazamiento&id_desplazamiento='+idDes;
}

function editar_desplazamiento_cerrado(idDes)
{
	esquema = 'rec';
	url = '/opera/movimientos_rec.php?mode=editar_desplazamiento_cerrado&id_desplazamiento='+idDes;
	abrirVentanaJavaScript('movimientosotro','500','400',url);
}

</script>
<?include($CFG->templatedir . "/resize_window.php");?>
