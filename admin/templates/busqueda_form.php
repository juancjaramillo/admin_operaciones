<?echo $javascript_entidad?>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="4" bgcolor="#FFFFFF" class="textobco10">
  <tr>
    <td bgcolor="<?=$entidad->get("lightBgColor");?>"><table width="100%"  border="0" cellpadding="0" cellspacing="5" class="textobco10">
      <tr>
        <td>
					<table width="100%"  border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="textobco10">
          	<tr bgcolor="<?=$entidad->get("darkBgColor");?>">
            	<td align="right">A trav&eacute;s de este formulario usted puede buscar <span class="style2"><?=strtoupper($entidad->get("labelModule"))?></span> </td>
          	</tr>
	        </table>
					<br>
					<form name="entryform" action="<?=$ME?>" method="GET"  onSubmit="window.opener.focus()">
						<?
						if(isset($frm['iframe']) || isset($frm['popup'])){
						?>
							<input type="hidden" name="popup" value="">
						<?
						}
						?>
						<input type="hidden" name="module" value="<?=$entidad->get("name");?>">
						<input type="hidden" name="mode" value="find">
          	<table width="100%"  border="0" cellpadding="4" cellspacing="1" bgcolor="#999999" class="textobco10">
							<?=$string?>
              <tr bgcolor="<?=$entidad->get("darkBgColor");?>"> 
                <td width="40%" align="right">&nbsp;</td>
                <td>
									<input type="submit" value="Buscar">
									<input type="button" value="Cancelar" onClick="window.opener.focus();window.close()">
								</td>
              </tr>
          	</table>
					</form>
        <br>
				</td>
      </tr>
    </table></td>
  </tr>
</table>
</body>
</html>
