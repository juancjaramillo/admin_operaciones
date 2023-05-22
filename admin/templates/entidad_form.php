<?echo $javascript_entidad?>
<script type="text/javascript">
	function abrirVentanaNueva(url,name,width,height){
		izq=(screen.width-width)/2;
		arriba=(screen.height-height)/2;
		return window.open(url,name,'scrollbars=yes,width=' + width +',height=' + height +',resizable=yes,left='+izq+',top='+arriba);
	}
	
	function agregar(module) {
		document.entryform.mode.value='agregar';
		string='ventana_' + module;
		eval(string + "=abrirVentanaNueva('<?=$ME?>?module=" + module + "&mode=agregar','ventana_" + module + "',700,500)");
		eval(string + ".focus()");
		return;
	}

	function newWindow(url,name,width,height) {
		string='ventana_' + name;
		eval(string + "=abrirVentanaNueva(url,'ventana_" + name + "'," + width + "," + height + ")");
		eval(string + ".focus()");
		return;
	}

	function evaluar_accion(mode,id,field){
		verify=window.confirm("Para Georreferenciar la dirección haga click en Aceptar.\n Si desea mantener los datos actuales haga click en Cancelar.");
		eval("var valor=document.entryform."+field+".value");
		if(verify){
			newWindow('modules/map.phtml?type='+mode+'&map_type=georref&id='+id+'&again=&direccion=' + escape(valor),'georref','600','400');	
		}
		else{
			newWindow('modules/map.phtml?type='+mode+'&map_type=georref&id='+id+'&direccion=' + escape(valor),'georref','600','400');
		}
	}

	function abrirVentanaJavaScript(name,width,height,url,scrollbars){
		vent=abrirVentanaNueva('',name,width,height,scrollbars);
		vent.location.href=url;
		vent.focus();
	}

	function popup_color(color,input){
			abrirVentanaJavaScript('colors',200,170,'<?=$CFG->admin_dir?>/colors.php?color=' + color + '&submit=0&input=' + input,'no');
	}


</script>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="textobco10">
  <tr>
    <td bgcolor="<?=$entidad->get("lightBgColor")?>"><table width="100%"  border="0" cellpadding="0" cellspacing="5" class="textobco10">
      <tr>
        <td>
					<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="textobco10">
          <tr>
            <td>&nbsp;</td>
          </tr>
        </table>
          <table width="100%"  border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="textobco10">
          <tr bgcolor="#bfbfac">
                <td align="left">
									<span class="style2">DATOS <?=strtoupper($entidad->get("labelModule"))?> : </span>
								</td>
            </tr>
<?if(isset($frm["mensajeDeError"]) && $frm["mensajeDeError"]!=""){?>
          <tr bgcolor="#bfbfac">
	          <td align="left">
							<span class="style2"><?=$frm["mensajeDeError"]?></span>
						</td>
          </tr>
<?}?>
        </table>
					<form name="entryform" action="<?=$ME?>" method="POST" enctype="multipart/form-data" onSubmit="return revisar()">
					<?
						$pk=$entidad->getAttributeByName($entidad->get("primaryKey"));
					?>
						<input type="hidden" name="module" value="<?=$entidad->get("name");?>">
						<input type="hidden" name="mode" value="<?=$entidad->get("newMode");?>">
						<input type="hidden" name="<?=$entidad->get("primaryKey")?>" value="<?=$pk->get("value");?>">
						<br>        
            <table width="100%"  border="0" cellpadding="2" cellspacing="1" bgcolor="#999999" class="textobco10">
<!--	********************************************	-->
<? echo $string_entidad;?>
<!--	********************************************	-->
						</table>
<!--	********************************************	-->
<?
if($entidad->get("newMode")!="insert"){
for($i=0;$i<sizeof($entidad->relationships);$i++){
	$relation=$entidad->getRelationshipByIndex($i);
?>
<!--	********************************************	-->
        <br>
        <table width="100%"  border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="textobco10">
          <tr bgcolor="#728A8C">
                <td align="left" bgcolor="#728A8C"><span class="style2"><?=$relation->get("label");?>:</span></td>
          </tr>
        </table>
        <br>
        <table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#999999" class="textobco10">
              <tr bgcolor="#728A8C"> 
                <td bgcolor="#728A8C">
									<iframe src="relation.php?name=<?=$relation->get("name")?>&masterTable=<?=$entidad->get("name")?>&masterFieldValue=<?=$relation->get("masterFieldValue")?>" frameborder="1" width="100%" height="200" scrolling="auto" name="postit_iframe"></iframe>
								</td>
              </tr>
				</table>
<!--	********************************************	-->
<?
}
}
?>
<!--	********************************************	-->

            <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="textobco10">
              <tr> 
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr> 
                <td colspan="2">
									<table width="100%" border="0" cellspacing="0" cellpadding="3">
                    <tr>
                      <td>
											<?if($entidad->get("newMode")!="consultar"){
													if($iframe==0){?>
														<input type="Submit" style="font-size:8pt" value="Aceptar">
											 <?}else{
												 		if($entidad->get("newMode")=="insert"){?>
													 		<input type="hidden" name="iframes" value="yes">
															<input type="Submit" style="font-size:8pt" value="Siguiente">
													 <?}else{?>
														 <input type="Submit" style="font-size:8pt" value="Aceptar">
													 <?}?>
											 <?}?>
											<?}?>	
												<input type="button" style="font-size:8pt" value="Cancelar" onClick="window.opener.focus();window.close();">
											</td>
                    </tr>
                  </table>
								</td>
              </tr>
            </table>
					</form>
				</td>
      </tr>
    </table></td>
  </tr>
</table>
<?
include($CFG->templatedir . "/resize_window.php");
?>
</body>
</html>

