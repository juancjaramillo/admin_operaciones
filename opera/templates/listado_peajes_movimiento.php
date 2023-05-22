<?
include("../../application.php");
include($CFG->dirroot."/templates/header_popup_tabview.php");

$mov = $db->sql_row("SELECT c.codigo as ruta, v.codigo, mov.inicio, mov.final , a.id_centro
		FROM rec.movimientos mov
		LEFT JOIN vehiculos v ON v.id=mov.id_vehiculo
		LEFT JOIN micros c ON c.id=mov.id_micro
		LEFT JOIN ases a ON a.id=c.id_ase
		WHERE mov.id=".$_GET["id_movimiento"]);

$titulo = '<span class="azul_12">RUTA '.$mov["ruta"].'</span> / Vehículo: '.$mov["codigo"].' / Fecha Inicial: '.$mov["inicio"].' / Fecha Final: '.$mov["final"];

$data = array();
$qid = $db->sql_query("SELECT p.nombre, mp.id, mp.veces, '<a href='|| chr(39)||'javascript:eliminar('||mp.id||')'|| chr(39)||'><img alt='|| chr(39)||'Eliminar'|| chr(39)||' title='|| chr(39)||'Eliminar'|| chr(39)||' src='|| chr(39)||'".$CFG->wwwroot."/admin/iconos/transparente/trash-x.png'|| chr(39)||' border='|| chr(39)||'0'|| chr(39)||'></a>' as opciones
		FROM rec.movimientos_peajes mp
		LEFT JOIN peajes p ON p.id=mp.id_peaje
		WHERE mp.id_movimiento=".$_GET["id_movimiento"]);
while($query = $db->sql_fetchrow($qid))
{
	$data[] = '{id: "'.$query["id"].'", peaje: "'.$query["nombre"].'", veces: "'.$query["veces"].'", opciones : "'.$query["opciones"].'"}';
}

?>

<div>
<table width="100%">
  <tr>
    <td height="40" colspan=3 align="center"><span class="azul_16"><strong>PEAJES DEL MOVIMIENTO</strong></span></td>
  </tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
				<tr>
					<td>
						<table width="100%" >
							<tr>
								<td>
									<table width="100%">
										<tr>
											<td align='left'><?=$titulo?></td>
											<td align="right"><a href="<?=$CFG->wwwroot?>/opera/movimientos_rec.php?mode=agregar_peaje&id_movimiento=<?=$_GET["id_movimiento"]?>" class="link_verde" title="Agregar Registro">Agregar Registro</a></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center"><div id="cellediting"> </div></td>
	</tr>

</table>
</div>

<script type="text/javascript">
YAHOO.example.Data = { addresses: [<?=implode(",",$data)?>]}

YAHOO.util.Event.addListener(window, "load", function() {
		YAHOO.example.InlineCellEditing = function() {
		var myColumnDefs = [
		{key:"id"},
		{key:"peaje", label:"Peaje", sortable: true},
		{key:"veces", label:"Veces", sortable: true, editor: new YAHOO.widget.TextboxCellEditor({
			LABEL_CANCEL:"Cerrar", 
			LABEL_SAVE:"Guardar",
			asyncSubmitter: function (callback, newValue) {
			var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
			if(!regexpression.test(newValue)){
			window.alert('Veces no contiene un dato válido.');
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
			} ,
			failure:function(o) {
			alert(o.statusText);
			callback();
			},
			scope:this
			},
			'action=cellEdit&column=' + column.key + '&newValue=' + escape(newValue) + '&oldValue=' + escape(oldValue)+'&mode=actualizar_peaje&campo=veces' + primaryKey 
			); 
			} 
			}
		})},
		{key:"opciones", label:"Eliminar"}
];

var myDataSource = new YAHOO.util.DataSource(YAHOO.example.Data.addresses);
myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
myDataSource.responseSchema = {
fields: [{key:"id"}, "peaje", "veces","opciones"]
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


function eliminar(id)
{
	url = '<?=$CFG->wwwroot?>/opera/movimientos_rec.php?mode=eliminar_peaje&id_movimiento=<?=$_GET["id_movimiento"]?>&id='+id;
	window.location.href= url;
}

</script>
<?
include($CFG->templatedir . "/resize_window.php");
include($CFG->dirroot."/templates/footer_popup.php");
?>
