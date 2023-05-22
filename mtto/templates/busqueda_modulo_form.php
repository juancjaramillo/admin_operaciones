<?echo $javascript_entidad?>
<form name="entryform" action="<?=$ME?>" method="GET"  onSubmit="window.opener.focus()">
<?if(isset($frm['iframe']) || isset($frm['popup'])){?>
		<input type="hidden" name="popup" value="">
<?}?>
<input type="hidden" name="module" value="<?=$entidad->get("name");?>">
<input type="hidden" name="mode" value="find">

<table width="100%">
	<tr>
		<td height="40" class="azul_16"><strong><?=strtoupper($entidad->labelModule)?></strong>
	</tr>
	<tr>
		<td>
			<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
				<tr>
					<td width="40%" height=20 align="left">
						A trav&eacute;s de este formulario usted puede buscar <?=strtoupper($entidad->get("labelModule"))?>
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
							<?=$string?>
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
					<td align="center">
						<input type="submit" value="Buscar" class="boton_verde_peq">
						<input type="button" value="Cancelar" class="boton_verde_peq" onClick="window.opener.focus();window.close()">
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
</body>
</html>
