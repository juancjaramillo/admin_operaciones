<html>
<head>
	<script>
		function revisar(frm){
			if(frm.id_motivo.selectedIndex==0){
				window.alert('Por favor indique el motivo.');
				return(false);
			}
			return(true);
		}
	</script>
</head>
<body>
<form name="entryform" action="<?=$ME?>" onSubmit="return revisar(this);">
<input type="hidden" name="mode" value="give_ack">
<input type="hidden" name="id_alerta" value="<?=$alerta["id"]?>">
<table border="1" align="center">
<tr><td align="right">Centro:</td><td><?=$alerta["centro"]?></td></tr>
<tr><td align="right">Tipo:</td><td><?=$alerta["tipo"]?></td></tr>
<?if($alerta["ruta"]!=""){?>
<tr><td align="right">Ruta:</td><td><?=$alerta["ruta"]?></td></tr>
<?}?>
<?if($alerta["vehiculo"]!=""){?>
<tr><td align="right">Vehículo:</td><td><?=$alerta["vehiculo"]?></td></tr>
<?}?>
<tr><td align="right">Fecha:</td><td><?=date("Y-m-d",strtotime($alerta["hora"]))?></td></tr>
<tr><td align="right">Hora:</td><td><?=date("H:i:s",strtotime($alerta["hora"]))?></td></tr>
<tr><td align="right">Generar VoBo:</td><td><select name="id_motivo"><?=$optionsMotivos?></select></td></tr>
<tr>
	<td colspan="2" align="center">
		<input type="submit" value="Dar VoBo">
		<input type="button" value="Cerrar" onClick="if(window.opener && window.opener.focus) window.opener.focus(); window.close();">
	</td>
</tr>
</table>
</form>
</body>
</html>


