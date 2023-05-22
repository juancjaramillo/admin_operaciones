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
<?
/*
echo "<pre>";
print_r($datos);
echo "</pre>";
*/
?>
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
	url = '<?=$CFG->wwwroot?>/opera/movimientos_<?=$schema?>.php?mode=agregar_movimiento_unico&id_micro='+id_micro+'&fecha='+fecha;
	abrirVentanaJavaScript('movimientos','900','500',url);
}

function editar_vehiculo(id_movimiento)
{
	esquema = '<?=nvl($schema)?>';
	url = '<?=$CFG->wwwroot?>/opera/movimientos_<?=$schema?>.php?mode=editar_vehiculo&id_movimiento='+id_movimiento+'&esquema='+esquema;
	abrirVentanaJavaScript('movimientos','500','300',url);
}

function cerrar_movimiento(id_movimiento)
{
	esquema = '<?=nvl($schema)?>';
	url = '<?=$CFG->wwwroot?>/opera/movimientos_<?=$schema?>.php?mode=cerrarMovimiento&id_movimiento='+id_movimiento+'&esquema='+esquema;
	abrirVentanaJavaScript('movimientos','900','500',url);
}

function cerrar_movimientoFechaActual(id_movimiento)
{
	esquema = '<?=nvl($schema)?>';
	url = '<?=$CFG->wwwroot?>/opera/templates/cerrar_movimientoFechaActual.php?id_movimiento='+id_movimiento+'&esquema='+esquema;
	abrirVentanaJavaScript('movimientos','400','300',url);
}



function cerrar_movimiento_con_fecha(id_movimiento)
{
	esquema = '<?=nvl($schema)?>';
	url = '<?=$CFG->wwwroot?>/opera/movimientos_<?=$schema?>.php?mode=cerrarMovimientoConFecha_form&id_movimiento='+id_movimiento+'&esquema='+esquema;
	abrirVentanaJavaScript('movimientos','500','300',url);
}

function desplazamiento(id_movimiento)
{
	esquema = '<?=nvl($schema)?>';
	url = '<?=$CFG->wwwroot?>/opera/movimientos_<?=$schema?>.php?mode=desplazamientos_'+ esquema + '&id_movimiento='+id_movimiento;
	abrirVentanaJavaScript('movimientos','900','500',url);
}

function pesos(id_movimiento)
{
	url = '<?=$CFG->wwwroot?>/opera/templates/listado_pesos_movimiento.php?id_movimiento='+id_movimiento;
	abrirVentanaJavaScript('movimientos','800','600',url);
}

function peajes(id_movimiento)
{
	url = '<?=$CFG->wwwroot?>/opera/templates/listado_peajes_movimiento.php?id_movimiento='+id_movimiento;
	abrirVentanaJavaScript('movimientos','500','300',url);
}

function combustible(id_movimiento)
{
	url = '<?=$CFG->wwwroot?>/opera/movimientos_<?=$schema?>.php?mode=editar_combustible&id_movimiento='+id_movimiento;
	abrirVentanaJavaScript('movimientos','400','300',url);
}

function eliminar_movimiento(id_movimiento)
{
	if (confirm('¿Está seguro de querer borrar el movimiento?'))
	{
		esquema = '<?=nvl($schema)?>';
		url = '<?=$CFG->wwwroot?>/opera/movimientos_<?=$schema?>.php?mode=eliminar_movimiento&id='+id_movimiento+'&esquema='+esquema;
		abrirVentanaJavaScript('movimientos','500','300',url);
	}
}

function editar_movimiento_cerrado(id_movimiento)
{
	esquema = '<?=nvl($schema)?>';
	url = '<?=$CFG->wwwroot?>/opera/movimientos_<?=$schema?>.php?mode=editar_movimiento_cerrado&id_movimiento='+id_movimiento+'&esquema='+esquema;
	abrirVentanaJavaScript('movimientos','500','400',url);
}

function clientes(id_movimiento)
{
	url = '<?=$CFG->wwwroot?>/opera/templates/listado_clientes_movimiento.php?id_movimiento='+id_movimiento;
	abrirVentanaJavaScript('movimientos','500','400',url);
}

function cambiarRuta(id_movimiento)
{
	esquema = '<?=nvl($schema)?>';
	url = '<?=$CFG->wwwroot?>/opera/templates/cambiar_ruta_movimiento.php?id_movimiento='+id_movimiento+'&esquema='+esquema;
	abrirVentanaJavaScript('movimientos','500','300',url);
}

function log_movimientos(id_movimiento)
{
	esquema = '<?=nvl($schema)?>';
	url = '<?=$CFG->wwwroot?>/opera/movimientos_<?=$schema?>.php?mode=log_movimientos&id_movimiento='+id_movimiento+'&esquema='+esquema;
	abrirVentanaJavaScript('movimientos','400','700',url);
}


</script>
