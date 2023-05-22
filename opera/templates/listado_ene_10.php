<?
include($CFG->dirroot."/templates/header_2panel.php");
?>
<div id="right1">
	<?include($CFG->dirroot."/mtto/templates/opciones.php");?>
</div>
<div id="center1">
	<div height="100%">
		<table class="tabla_grande" border=0>
			<?
			$altura = 80;
			if(isset($botones)){ $altura = 30;?>
			<tr>
				<td width="70%" height="50" valign="middle" align="right" colspan=3><?=nvl($botones)?></td>
			</tr>
			<?}?>
			<tr>
				<td align="right" valign="middle" width="10%"><?=nvl($paginacionAnt)?></td>
				<td height="<?=$altura?>" width="80%" valign="middle" class="azul_16" align="center"><?=$titulo?></td>
				<td align="left" valign="middle" width="10%"><?=nvl($paginacionSig)?></td>
			</tr>
			<tr>
				<td align="center" width="100%" valign="top" colspan=3> <div id="cellediting"> </div></td>
			</tr>
		</table>
	</div>
</div>
<?include($CFG->dirroot."/templates/footer_2panel.php");?>


<script type="text/javascript">
YAHOO.example.Data = { addresses: [<?=$datos["data"]?>]}
YAHOO.util.Event.addListener(window, "load", function() {
		YAHOO.example.InlineCellEditing = function() {
		// Custom formatter for "address" column to preserve line breaks
		var formatAddress = function(elCell, oRecord, oColumn, oData) {
		elCell.innerHTML = "<pre class=\"address\">" + oData + "</pre>";
		};

		var myColumnDefs = [<?=$datos["myColumnDefs"]?>];

		var myDataSource = new YAHOO.util.DataSource(YAHOO.example.Data.addresses);
		myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
		myDataSource.responseSchema = {
			fields: [<?=$datos["fields"]?>]
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
		<?if(isset($cellClickEvent)) echo $cellClickEvent; 
		else{?>
		myDataTable.subscribe("cellClickEvent", myDataTable.onEventShowCellEditor);
		<?}?>

		var oColumn = myDataTable.getColumn(0);
		myDataTable.hideColumn(oColumn);

		return {
			oDS: myDataSource,
			oDT: myDataTable
		};
		}();
});

<?=nvl($scriptC)?>

function agregar_movimiento_unico(id_micro)
{
	fecha = '<?=nvl($fecha)?>';
	url = '<?=$CFG->wwwroot?>/opera/movimientos.php?mode=agregar_movimiento_unico&id_micro='+id_micro+'&fecha='+fecha;
	abrirVentanaJavaScript('movimientos','900','500',url);
}

function editar_movimiento_unico(id_movimiento)
{
	esquema = '<?=nvl($schema)?>';
	url = '<?=$CFG->wwwroot?>/opera/movimientos.php?mode=editar_movimiento_unico&id_movimiento='+id_movimiento+'&esquema='+esquema;
	abrirVentanaJavaScript('movimientos','900','500',url);
}

function cerrar_movimiento(id_movimiento)
{
	esquema = '<?=nvl($schema)?>';
	url = '<?=$CFG->wwwroot?>/opera/movimientos.php?mode=cerrarMovimiento&id_movimiento='+id_movimiento+'&esquema='+esquema;
	abrirVentanaJavaScript('movimientos','900','500',url);
}

function cerrar_movimiento_con_fecha(id_movimiento)
{
	esquema = '<?=nvl($schema)?>';
	url = '<?=$CFG->wwwroot?>/opera/movimientos.php?mode=cerrarMovimientoConFecha_form&id_movimiento='+id_movimiento+'&esquema='+esquema;
	abrirVentanaJavaScript('movimientos','500','300',url);
}

function desplazamiento(id_movimiento)
{
	esquema = '<?=nvl($schema)?>';
	url = '<?=$CFG->wwwroot?>/opera/movimientos.php?mode=desplazamientos_'+ esquema + '&id_movimiento='+id_movimiento;
	abrirVentanaJavaScript('movimientos','900','500',url);
}

function pesos(id_movimiento)
{
	url = '<?=$CFG->wwwroot?>/opera/movimientos.php?mode=editar_pesos&id_movimiento='+id_movimiento;
	abrirVentanaJavaScript('movimientos','800','300',url);
}

function combustible(id_movimiento)
{
	url = '<?=$CFG->wwwroot?>/opera/movimientos.php?mode=editar_combustible&id_movimiento='+id_movimiento;
	abrirVentanaJavaScript('movimientos','400','300',url);
}

</script>
