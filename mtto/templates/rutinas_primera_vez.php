<form name="entryform" action="<?=$ME?>" method="POST"  class="form" onSubmit="return revisar()">

<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="id" value="<?=$rutina["id"]?>">
<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center">
			<span class="azul_16"><strong>RUTINA: <?=strtoupper($rutina["rutina"])?></strong></span>
			<?if($rutina["activa"]=="f") echo "<br><b>Nota:</b> La rutina est� inactiva, no se puede programar.";?>
		</td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center">
							<tr>
								<td align="center" width="50%"><b>EQUIPO</b></td>
								<?
								if($rutina["id_frecuencia"] != "")
									echo "<td align=\"center\" width=\"20%\"><b>REALIZARLA EL D�A</b></td>";
								if($rutina["frec_horas"] != "")
									echo "<td align=\"center\" width=\"15%\"><b>CUANDO EL HOROMETRO<br />LLEGUE A</b></td>";
								if($rutina["frec_km"] != "")
									echo "<td align=\"center\" width=\"15%\"><b>CUANDO EL KM<br /> LLEGUE A</b></td>";
								?>
							</tr>
							<?
							$script = "";
							$nf=$nk=$nh=1;
							foreach($equipos as $idEquipo => $eq){
								$nameEquipo = $eq["nombre"];
								?>
							<tr>
								<td><?=$nameEquipo." (horo: ".$eq["horo"]."/km: ".$eq["km"].")"?></td>
								<?
								if($rutina["id_frecuencia"] != ""){
									$script.= "if(document.entryform.fecha_".$idEquipo.".value.replace(/ /g, '') !=''){
										var regexpressionUno=/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/;
										var regexpressionDos=/^[+][0-9]+$/;
										if(!regexpressionUno.test(document.entryform.fecha_".$idEquipo.".value) && !regexpressionDos.test(document.entryform.fecha_".$idEquipo.".value) ){
											window.alert('[El d�a del equipo ".$nameEquipo."] no contienen un dato v�lido.');
											document.entryform.fecha_".$idEquipo.".focus();
											return(false);
										}

										if(regexpressionUno.test(document.entryform.fecha_".$idEquipo.".value)){
											if(document.entryform.fecha_".$idEquipo.".value <= '".date("Y-m-d")."'){
												window.alert('El d�a del equipo ".$nameEquipo." no puede ser hoy o menor que hoy');
												document.entryform.fecha_".$idEquipo.".focus();
												return(false);
											}
										}
									}\n";
								?>
								<td align='center'><a href="javascript:copiar('fecha','<?=$nf?>')"><img border="0" alt="Copiar Valor Hacia Abajo" title="Copiar Valor Hacia Abajo" src="<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-clock.png"></a>
									<input type='text' size="10" id="fecha_<?=$nf?>" class="casillatext_fecha" name='fecha_<?=$idEquipo?>' value='' /><button id="b_fecha_<?=$idEquipo?>" onclick="javascript:showCalendarSencillo('fecha_<?=$nf?>','b_fecha_<?=$idEquipo?>')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
								<?
								$nf++;
								}
								if($rutina["frec_horas"] != ""){?>
									<td align='center'><a href="javascript:copiar('horometro','<?=$nh?>')"><img border="0" alt="Copiar Valor Hacia Abajo" title="Copiar Valor Hacia Abajo" src="<?=$CFG->wwwroot?>/admin/iconos/transparente/flechaabajo.gif"></a><input type='text' size='10' name='horometro_<?=$idEquipo?>' id='horometro_<?=$nh?>' class='casillatext' value=''></td>
								<?
									$nh++;
									$script.= "
									if(document.entryform.horometro_".$idEquipo.".value.replace(/ /g, '') !=''){
										var regexpressionUno=/(^-?\d+$)|(^-?\d+\.\d+$)/;
										var regexpressionDos=/^[+][0-9]+$/;
										if(!regexpressionUno.test(document.entryform.horometro_".$idEquipo.".value) && !regexpressionDos.test(document.entryform.horometro_".$idEquipo.".value) ){
											window.alert('[Las horas del equipo ".$nameEquipo."] no contienen un dato v�lido.');
											document.entryform.horometro_".$idEquipo.".focus();
											return(false);
										}
									}\n";
									if($eq["horo"] != ""){
										$script.= "
											var regexpressionUno=/(^-?\d+$)|(^-?\d+\.\d+$)/;
											if(regexpressionUno.test(document.entryform.horometro_".$idEquipo.".value)){
												if(document.entryform.horometro_".$idEquipo.".value.replace(/ /g, '') !=''){
													if(document.entryform.horometro_".$idEquipo.".value < ".$eq["horo"]."){
														window.alert('[Las horas del equipo ".$nameEquipo."] no pueden ser menores que las actuales.');
														document.entryform.horometro_".$idEquipo.".focus();
														return(false);
													}
												}
											}\n";
									}
								}
								if($rutina["frec_km"] != ""){?>
									<td align='center'><a href="javascript:copiar('km','<?=$nk?>')"><img border="0" alt="Copiar Valor Hacia Abajo" title="Copiar Valor Hacia Abajo" src="<?=$CFG->wwwroot?>/admin/iconos/transparente/flechaabajo.gif"></a><input type='text' size='10' name='km_<?=$idEquipo?>' id='km_<?=$nk?>' class='casillatext' value=''></td>
								<?
									$nk++;
									$script.= "
									if(document.entryform.km_".$idEquipo.".value.replace(/ /g, '') !=''){
										var regexpressionDos=/^[+][0-9]+$/;
										var regexpressionUno=/(^-?\d+$)|(^-?\d+\.\d+$)/;
										if(!regexpressionUno.test(document.entryform.km_".$idEquipo.".value) && !regexpressionDos.test(document.entryform.km_".$idEquipo.".value) ){
											window.alert('[El Km del equipo ".$nameEquipo."] no contiene un dato v�lido.');
											document.entryform.km_".$idEquipo.".focus();
											return(false);
										}
									}\n";

									if($eq["km"] != "")
									{
										$script.= "
											var regexpressionUno=/(^-?\d+$)|(^-?\d+\.\d+$)/;
											if(regexpressionUno.test(document.entryform.km_".$idEquipo.".value)){
												if(document.entryform.km_".$idEquipo.".value.replace(/ /g, '') !=''){
													if(document.entryform.km_".$idEquipo.".value < ".$eq["km"]."){
														window.alert('[El Km del equipo ".$nameEquipo."] no puede ser menor que el actual.');
														document.entryform.km_".$idEquipo.".focus();
														return(false);
													}
												}
											}\n";
									}
								}
								?>
							</tr>
							<?}?>
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

	if(indice=='fecha')
	{
		for(j=sub;j<<?=$nf?>;j++)
		{
			casilla  = indice+'_'+j;
			document.getElementById(casilla).value=valor;
		}
	}

	if(indice=='horometro')
	{
		for(j=sub;j<<?=$nh?>;j++)
		{
			casilla  = indice+'_'+j;
			document.getElementById(casilla).value=valor;
		}
	}

	if(indice=='km')
	{
		for(j=sub;j<<?=$nk?>;j++)
		{
			casilla  = indice+'_'+j;
			document.getElementById(casilla).value=valor;
		}
	}

}



</script>
