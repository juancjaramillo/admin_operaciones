<script type="text/javascript">
function revisar(frm){
	if(frm.id_centro.selectedIndex==0){
		window.alert("Por favor seleccione un centro");
		return(false);
	}
	if(frm.archivo.value==""){
		window.alert("No seleccionó ningún archivo.");
		return(false);
	}
	if(frm.archivo.value.substring(frm.archivo.value.length-4)!=".xls"){
		window.alert('Por favor seleccione un archivo con extensión xls');
		return(false);
	}
	return(true);
}

</script>
<form name="entryform" action="<?=$ME?>" method="POST"  class="form" enctype="multipart/form-data" onSubmit="return revisar(this)">
<input type="hidden" name="mode" value="upload">
<table width="100%">
	<tr>
		<td height="40" align="center"><span class="azul_16"><strong>CARGAR ARCHIVO INSPECCIONES</strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" border=1 bordercolor="#7fa840" align="center">
				<tr>
					<td align='right'>Centro</td>
					<td align='left'> <select name='id_centro'><?=$centrosOptions?></select> </td>
				</tr>
				<tr>
					<td align='right'>Archivo</td>
					<td><input type="file" name="archivo">
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=3 align="center">
			<input type="submit" class="boton_verde" value="Aceptar" />
			<input type="button" class="boton_verde" value="Cancelar" onclick="window.close()"/>
		</td>
	</tr>
</table>
</form>

