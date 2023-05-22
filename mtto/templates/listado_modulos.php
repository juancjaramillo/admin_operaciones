<script language="JavaScript" type="text/javascript">

	function buscar() {
		document.entryform.mode.value='buscar';
		ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>=abrirVentanaNueva('ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>',700,500);
		ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>.focus();
		ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>.opener.name='opener';
		document.entryform.target='ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>';
		document.entryform.submit();
		return;
	}

	function agregar() {
		document.entryform.mode.value='agregar';
		ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>=abrirVentanaNueva('ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>',700,500);
		ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>.focus();
		document.entryform.target='ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>';
		document.entryform.submit();
		return;
	}

	function editar(modo) {
		var seleccionadas = "0";
		var seleccionada=0;
		for (var i = 0; i < document.entryform.elements.length; i++) {
			if (document.entryform.elements[i].name=='id' && document.entryform.elements[i].checked==1){
				seleccionadas++;
				document.entryform.id.value=document.entryform.elements[i].value;
				seleccionada=i;
			}
		}
		if (seleccionadas==0){
			window.alert('No seleccionó ningún elemento');
			return;
		}
		if (seleccionadas>1){
			window.alert('Debe escoger sólo un elemento para editar');
			return;
		}
		document.entryform.mode.value=modo;
		document.entryform.elements[seleccionada].name='id';
		ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>=abrirVentanaNueva('ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>',700,500);
		ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>.focus();
		document.entryform.target='ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>';
		document.entryform.submit();
		return;
	}


	function eliminar() {
		var seleccionadas = 0;
		var texto="";
		for (var i = 0; i < document.entryform.elements.length; i++) {
			if (document.entryform.elements[i].name=='id' && document.entryform.elements[i].checked==1) seleccionadas++;
		}
		if (seleccionadas==0){
			window.alert('No seleccionó ningún elemento');
			return;
		}
		if (seleccionadas==1) texto='¿Está seguro de querer borrar el elemento seleccionado?';
		else texto='¿Está seguro de querer borrar los elementos seleccionados?';
		if (!confirm(texto)) return;

		document.entryform.mode.value='eliminar';
		ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>=abrirVentanaNueva('ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>',1,1);
		ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>.focus();
		document.entryform.target='ventana_<?=preg_replace("/[^a-z0-9]/i","_",$entidad->name)?>';
		document.entryform.submit();
		return;
	}


</script>

<form name="entryform" action="<?=$ME?>" method="get">
<input type="hidden" name="module" value="<?=$entidad->name?>">
<input type="hidden" name="mode">
<input type="hidden" name="table_parent" value="<?=$entidad->tableParent?>">
<input type="hidden" name="id_parent" value="<?=$entidad->idParent?>">
<?
	foreach($_GET AS $label => $value){
		if($label != "module" && $label != "mode" && $label != "table_parent" && $label != "id_parent"){
		  $arreglo = explode("_",$label);
		  if(count($arreglo)>=2 AND $arreglo[0] == "id"){
			  echo "<input type='hidden' name='campo' value='$label'>\n";
			}
			echo "<input type='hidden' name='$label' value='$value'>\n";
		}
	}		
	?>

<table width="100%">
	<tr>
		<td height="40" class="azul_16"><strong><?=strtoupper($entidad->labelModule)?></strong><?=$entidad->labelModuleAdicional?>
	</tr>
	<tr>
		<td>
			<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
				<tr>
					<td width="40%" height=40 align="left">
						<?=$entidad->numRows?> resultados<br />(Página <?=$entidad->currentPage?> / <?=$entidad->totalPages?>)
					</td>
					<td align="right" width="55%">
						<?=$entidad->get("previousPage") ." / ".$entidad->get("nextPage")?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840">
							<?
							echo $entidad->getTitleRow();
							while($row=$entidad->getRow()) {
								echo $row;
							}
							?>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="right" height=50>
			<table width="100%" border="0" cellspacing="0" cellpadding="3" >
				<tr>
					<td align="right">
						<?$module=nvl($_GET['module'],$CFG->defaultModule);?>
						<?if(nvl($entidad->btnAdd,TRUE)){?>	<input type="button" class="boton_verde_peq" value="Agregar" onClick="agregar()">&nbsp;<?}?>
						<?if(nvl($entidad->btnEdit,TRUE)){?><input type="button" class="boton_verde_peq" value="Editar" onClick="editar('editar')">&nbsp;<?}?>
						<?if(nvl($entidad->btnDelete,TRUE)){?><input type="button" class="boton_verde_peq" value="Eliminar" onClick="eliminar()">&nbsp;<?}?>
						<?if(nvl($entidad->btnDetails,TRUE)){?><input type="button" class="boton_verde_peq" value="Detalles" onClick="editar('consultar')">&nbsp;<?}?>
						<?if(nvl($entidad->btnSearch,TRUE)){?><input type="button" class="boton_verde_peq" value="Buscar" onClick="buscar()">&nbsp;<?}?>
						<?if($entidad->name != "rec.movimientos_pesos"){?><input type="button" class="boton_verde_peq" value="Cerrar" onClick="window.close()">&nbsp;<?}?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
