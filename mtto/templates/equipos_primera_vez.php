<form name="entryform" action="<?=$ME?>" method="POST"  class="form" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id" value="<?=$equipo["id"]?>">
<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong>EQUIPO: <?=$equipo["nombre"]?></strong></span><br />Horómetro : <?=number_format($equipo["horo"],2,".","")?><br />Km : <?=number_format($equipo["km"],2,".","")?></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td align="center"><b>RUTINAS</b></td>
								<?
									echo "<td align=\"center\" width=\"20%\"><b>REALIZARLA EL DÍA</b></td>";
									echo "<td align=\"center\" width=\"18%\"><b>CUANDO EL HOROMETRO<br />LLEGUE A</b></td>";
									echo "<td align=\"center\" width=\"18%\"><b>CUANDO EL KM<br /> LLEGUE A</b></td>";
								?>
							</tr>
							<?
							$script = "";
							$nf=$nk=$nh=1;
							foreach($rutinas as $idRutina => $dx)
							{
								echo "<tr>
									<td>".$dx["nombre"]."</td>
									<td align='center'>";
								if($dx["dias"] != "")
								{
									list($anio,$mes,$dia)=split("-",date("Y-m-d"));
									$diaRut = date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + $dx["dias"] * 24 * 60 * 60);
									?>
									<a href="javascript:copiar('fechacopy','<?=$nf?>')"><img border="0" alt="Copiar Valor Hacia Abajo" title="Copiar Valor Hacia Abajo" src="<?=$CFG->wwwroot?>/admin/iconos/transparente/flechaabajo.gif"></a>	<input type='text' size="10" id="fechacopy_<?=$nf?>" class="casillatext_fecha" name='fecha_<?=$idRutina?>' value='<?=$diaRut?>' /><button id="b_fechacopy_<?=$nf?>" onclick="javascript:showCalendarSencillo('fechacopy_<?=$nf?>','b_fechacopy_<?=$nf?>')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								<?
								$nf++;
								$script.= "
									if(document.entryform.fecha_".$idRutina.".value.replace(/ /g, '') !=''){
										var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2}/;
										var regexpressionDos=/^[+][0-9]+$/;
										if(!regexpression.test(document.entryform.fecha_".$idRutina.".value) && !regexpressionDos.test(document.entryform.fecha_".$idRutina.".value)){
											window.alert('La fecha de la rutina ".$dx["nombre"]." no contiene un dato válido.');
											document.entryform.fecha_".$idRutina.".focus();
											return(false);
										}
										if(regexpression.test(document.entryform.fecha_".$idRutina.".value)){
											if(document.entryform.fecha_".$idRutina.".value <= '".date("Y-m-d")."'){
												window.alert('El día de la rutina ".$dx["nombre"]." no puede ser hoy o menor que hoy');
												document.entryform.fecha_".$idRutina.".focus();
												return(false);
											}
										}
									}\n";
								}

								echo "</td><td align='center'>";

								if($dx["horo"] != "")
								{?>
								<a href="javascript:copiar('horocopy','<?=$nh?>')"><img border="0" alt="Copiar Valor Hacia Abajo" title="Copiar Valor Hacia Abajo" src="<?=$CFG->wwwroot?>/admin/iconos/transparente/flechaabajo.gif"></a><input type='text' size='10' name='horo_<?=$idRutina?>' class='casillatext' value='' id='horocopy_<?=$nh?>'>
								<?
								$nh++;
								$script.= "
									if(document.entryform.horo_".$idRutina.".value.replace(/ /g, '') !=''){
										var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
										var regexpressionDos=/^[+][0-9]+$/;
										if(!regexpression.test(document.entryform.horo_".$idRutina.".value) && !regexpressionDos.test(document.entryform.horo_".$idRutina.".value)){
											window.alert('El horometro de la rutina ".$dx["nombre"]." no contiene un dato válido.');
											document.entryform.horo_".$idRutina.".focus();
											return(false);
										}

										if(regexpression.test(document.entryform.horo_".$idRutina.".value)){
											if(document.entryform.horo_".$idRutina.".value < ".$equipo["horo"]."){
												window.alert('Las horas para la rutina ".$dx["nombre"]." no pueden ser menores que las actuales.');
												document.entryform.horo_".$idRutina.".focus();
												return(false);
											}
										}
									}\n";
								}
								echo "</td><td align='center'>";

								if($dx["km"] != "")
								{?>
								<a href="javascript:copiar('kmcopy','<?=$nk?>')"><img border="0" alt="Copiar Valor Hacia Abajo" title="Copiar Valor Hacia Abajo" src="<?=$CFG->wwwroot?>/admin/iconos/transparente/flechaabajo.gif"></a><input type='text' size='10' name='km_<?=$idRutina?>' class='casillatext' value='' id='kmcopy_<?=$nk?>'>
								<?
								$nk++;
								$script.= "
									if(document.entryform.km_".$idRutina.".value.replace(/ /g, '') !=''){
										var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
										var regexpressionDos=/^[+][0-9]+$/;
										if(!regexpression.test(document.entryform.km_".$idRutina.".value) && !regexpressionDos.test(document.entryform.km_".$idRutina.".value)){
											window.alert('El kilometraje de la rutina ".$dx["nombre"]." no contiene un dato válido.');
											document.entryform.km_".$idRutina.".focus();
											return(false);
										}

										if(regexpression.test(document.entryform.km_".$idRutina.".value)){
											if(document.entryform.km_".$idRutina.".value < ".$equipo["km"]."){
												window.alert('Los km para la rutina ".$dx["nombre"]." no pueden ser menores que los actuales.');
												document.entryform.km_".$idRutina.".focus();
												return(false);
											}
										}
									}\n";
								}
								echo "</td>";
								echo "</tr>";
							}

							?>
						</table>
					</td>
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
	</form>
</table>
<script type="text/javascript">
function revisar()
{
	<?=$script?>

	return(true);
}

function copiar(indice,sub)
{
	casilla = indice+'_'+sub;
	valor = document.getElementById(casilla).value;

	if(indice=='fechacopy')
	{
		for(j=sub;j<<?=$nf?>;j++)
		{
			casilla  = indice+'_'+j;
			document.getElementById(casilla).value=valor;
		}
	}

	if(indice=='horocopy')
	{
		for(j=sub;j<<?=$nh?>;j++)
		{
			casilla  = indice+'_'+j;
			document.getElementById(casilla).value=valor;
		}
	}

	if(indice=='kmcopy')
	{
		for(j=sub;j<<?=$nk?>;j++)
		{
			casilla  = indice+'_'+j;
			document.getElementById(casilla).value=valor;
		}
	}

}



</script>
