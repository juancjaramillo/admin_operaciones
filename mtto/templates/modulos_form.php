<?echo $javascript_entidad?>
<script type="text/javascript">
	
	function agregar(module) {
		document.entryform.mode.value='agregar';
		nombre_module = module
		nombre_module = nombre_module.replace('.','_');
		url = '<?=$ME?>?module=' + module + '&mode=agregar';
		abrirVentanaJavaScript(nombre_module,'700','500', url)
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

</script>

<form name="entryform" action="<?=$ME?>" method="POST" enctype="multipart/form-data" onSubmit="return revisar()">
<?
$pk=$entidad->getAttributeByName($entidad->get("primaryKey"));
?>
<input type="hidden" name="module" value="<?=$entidad->get("name");?>">
<input type="hidden" name="mode" value="<?=$entidad->get("newMode");?>">
<input type="hidden" name="<?=$entidad->get("primaryKey")?>" value="<?=$pk->get("value");?>">
<table width="100%">
	<tr>
		<td height="40" class="azul_16"><strong><?=strtoupper($entidad->labelModule)?></strong>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840">
							<?echo str_replace("nowrap","",$string_entidad);?>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="3">
				<tr>
					<td height=50>
						<?
						$aceptar = true;
						$botonAdicional = "";
						if($entidad->get("newMode")!="insert" && ($entidad->get("name") == "rec.pesos" || $entidad->get("name") == "rec.pesos_sin_mov"))
						{
							$cerrado = $entidad->db->sql_row("SELECT cerrado FROM rec.pesos WHERE id=".$pk->get("value"));
							if($cerrado["cerrado"] == "f" && in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["cerrar_pesos"]))
								$botonAdicional = '<input type="button" class="boton_verde_peq" value="Cerrar Peso" onclick="window.location.href=\''.$CFG->wwwroot.'/opera/movimientos_rec.php?mode=cerrar_peso_movimiento&id_peso='.$pk->get("value").'\'">';
							if($cerrado["cerrado"] == "t")
								$aceptar = false;
						}

						if($entidad->get("newMode")!="consultar"){
						echo $botonAdicional;
						if($iframe==0){
							if($aceptar){?>
							<input type="Submit" class="boton_verde_peq" value="Aceptar">
						<?}}else{
							if($entidad->get("newMode")=="insert"){?>
								<input type="hidden" name="iframes" value="yes">
								<input type="Submit" class="boton_verde_peq" value="Siguiente">
							<?}else{
								if($aceptar){?>
								<input type="Submit" class="boton_verde_peq" value="Aceptar">
							<?}}
							}
						}?>	
						<input type="button" class="boton_verde_peq" value="Cancelar" onClick="window.opener.focus();window.close();">
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<?
	if($entidad->get("newMode")!="insert" && ($entidad->get("name") == "rec.pesos" || $entidad->get("name") == "rec.pesos_sin_mov")){?>
	<tr><td><hr style="color:#506f77; background-color:#506f77; height:1px;border:none;"></td></td>
	<tr>
		<td><iframe src="<?=$CFG->wwwroot?>/opera/templates/listado_pesos_movimiento.php?id_peso=<?=$pk->get("value")?>" frameborder="0" width="100%" height="200" scrolling="auto" name="postit_iframe"></iframe>
		</td>
	</tr>

	<?}?>
</table>
</form>

<?include($CFG->templatedir . "/resize_window.php");?>
</body>
</html>
