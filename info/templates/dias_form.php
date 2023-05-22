<table width="100%">
  <tr>
    <td valign="top">
			<form name="entryform" action="<?=$ME?>" method="POST" onSubmit="return revisar()" class="form">
      <table width="60%" cellpadding="5" cellspacing="3" align="center">
        <tr>
          <td>
						<table width="100%" border=1 bordercolor="#7fa840" align="right">
							<tr>
								<td align="center" valign="center" width='33%'>Día : 
									<select  name='dia'>
									<?
									foreach($semana as $numDia => $nombreDia)
									{
											$selected = "";
											if($dia == $numDia) $selected = "selected";
											echo "<option value='".$numDia."' ".$selected.">".$nombreDia."</option>";
									}
									?>
									</select>
								</td>
								<td align='center'  width='33%'>Centro :
									<select  name='vista' onChange="updateRecursive_id_ase_dos(this)">
									<?
									$qidCn = $db->sql_query("SELECT id, centro 
											FROM centros WHERE id IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') 
											ORDER BY centro");
									while($cn = $db->sql_fetchrow($qidCn)){?>
												<option value="<?=$cn["id"]?>" <?if(isset($vista) && $vista==$cn["id"]) echo "selected"?>><?=$cn["centro"]?></option>
										<?}?>
									</selected>
								</td>
								<td align='center' width='33%'>
									<div id="id_ase_dos">Ase &nbsp;&nbsp;
									<select  name='id_ase' id='id_ase_dos'  style="width:150px">
										<option value=''>Todas</option>
											<?
											$qidAs = $db->sql_query("SELECT a.id, a.ase FROM ases a WHERE a.id_centro=".$vista);
											while($as = $db->sql_fetchrow($qidAs))
											{
												$selected = "";
												if(isset($id_ase) && $id_ase == $as["id"]) $selected = " selected";
												echo '<option value="'.$as["id"].'" '.$selected.'>'.$as["ase"].'</option>';
											}
										?>
									</select>
									</div>
								</td>
								<td align="center" valign="center" > <input type="submit" class="boton_verde" value="Aceptar"/> </td>
							</tr>		
						</table>
					</td>
				</tr>
			</table>
			</form>
		</td>
  </tr>
</table>
<script>

function GetHttpObject(handler){
	try
	{
		var oRequester = new ActiveXObject("Microsoft.XMLHTTP");
		oRequester.onreadystatechange=handler;
		return oRequester;
	}
	catch (error){
		try{
			var oRequester = new XMLHttpRequest();
			oRequester.onload=handler;
			oRequester.onerror=handler;
			return oRequester;
		} 
		catch (error){
			return false;
		}
	}
} 

var oXmlHttp_id_ase_dos;
function updateRecursive_id_ase_dos(select){
	namediv='id_ase_dos';
	nameId='id_ase_dos';
	id=select.options[select.selectedIndex].value;
	document.getElementById(namediv).innerHTML='Ase <select id="' + nameId + '" style="width:150px"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateInformes.php?tipo=asexcentro_dos&id_centro=" + id + "&divid=" + namediv;
	oXmlHttp_id_ase_dos=GetHttpObject(cambiarRecursive_id_ase_dos);
	oXmlHttp_id_ase_dos.open("GET", url , true);
	oXmlHttp_id_ase_dos.send(null);
}
function cambiarRecursive_id_ase_dos(){
	if (oXmlHttp_id_ase_dos.readyState==4 || oXmlHttp_id_ase_dos.readyState=="complete"){
		document.getElementById('id_ase_dos').innerHTML=oXmlHttp_id_ase_dos.responseText
	}
}

</script>
