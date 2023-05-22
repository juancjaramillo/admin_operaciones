<?
include($CFG->dirroot."/templates/header_2panel.php");
?>
<div id="right1">
	<?include($CFG->dirroot."/mtto/templates/opciones.php");?>
</div>
<div id="center1">
	<div height="100%">
		<table class="tabla_grande"> 
			<?if(isset($excel)){?>
			<tr>
				<td height="40" align="right"><a class="boton_verde" href="<?=$excel?>" title="Bajar">Bajar a Excel</a>&nbsp;&nbsp;&nbsp;</td>
			</tr>
			<?}?>
			<tr>
				<td height="50" width="80%" valign="middle" class="azul_16" align="center"><?=$titulo?></td>
			</tr>
			<tr>
				<td align="center" width="100%"> <div id="cellediting"> </div></td>
			</tr>
		</table>
	</div>
</div>
<?include($CFG->dirroot."/templates/footer_2panel.php");
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
			<?if($clase == "mtto"){?>
			{key:"equipo", label:"Equipo", sortable: true},
			<?}?>
			{key:"fini", label:"Fecha Inicio", sortable: true},
			{key:"hini", label:"Hora Inicio", sortable: true},
			<?if($editarObser && in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["opciones_novedades"])){?>
			{key:"observaciones", label:"Observaciones", editor: new YAHOO.widget.TextboxCellEditor({
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
					'novedades.php', 
					{
							success:function(o) {
							callback(true,newValue);
						}	,
						failure:function(o) {
							alert(o.statusText);
							callback();
						},
						scope:this
					},
					'action=cellEdit&column=' + column.key + '&newValue=' + escape(newValue) + '&oldValue=' + escape(oldValue)+'&mode=observaciones' + primaryKey 
				); 
				}}) 
			},
			<?}else{?>
			{key:"observaciones", label:"Observaciones"},
			<?}if($clase == "mtto"){?>
				{key:"ordenes", label:"Órdenes (Rutina/Fecha Progr.)", sortable: true},
			<?}?>

			{key:"editar", label:"Opciones"}
			];

		var myDataSource = new YAHOO.util.DataSource(YAHOO.example.Data.addresses);
		myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
		myDataSource.responseSchema = {
			fields: ["equipo","fini","hini","observaciones", "ordenes",{key:"id"}, "editar"]
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
	url = '<?=$CFG->wwwroot."/novedades/novedades.php?mode=editar&id="?>'+id;
	abrirVentanaJavaScript('novedades','800','500',url);
}

function cerrar(id)
{
	url = '<?=$CFG->wwwroot."/novedades/novedades.php?mode=cerrar&id="?>'+id;
	abrirVentanaJavaScript('novedades','1','1',url);
}


</script>
