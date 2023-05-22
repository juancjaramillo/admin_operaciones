<?
$days_of_month = date('t',strtotime(date("Y-m-01", strtotime("last month"))));
$inicio=date("Y-m-01", strtotime("last month"));
$final=date("Y-m-".$days_of_month,strtotime("last month"));

$user=$_SESSION[$CFG->sesion]["user"];

?>
<table width="100%">
  <tr>
    <td height="30">&nbsp;</td>
  </tr>
  <tr>
    <td valign="top">
			<form name="entryform" action="<?=$CFG->wwwroot?>/opera/informes.php" method="POST" onSubmit="return revisar()" class="form">
      <table width="100%" cellpadding="1" cellspacing="1">
        <tr>
          <td>
            <table width="100%">
              <tr>
								<td class="azul_16" align="center" colspan=2>CONSULTAR</td>
							</tr>
							<tr>
                <td align="right" valign="bottom" height="30">Inicio</td>
                <td valign="bottom" height="30" align='left'><input type='text' size='10' class="casillatext_fecha" name='inicio' value='<?=nvl($_POST["inicio"],$inicio)?>' readonly>&nbsp;<a title="Calendario" href="javascript:abrir('inicio','entryform');"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></a></td>
              </tr>
							<tr>
                <td align="right" valign="bottom" height="30">Fin</td>
                <td valign="bottom" height="30" align='left'><input type='text' size='10' class="casillatext_fecha" name='fin' value='<?=nvl($_POST["fin"],$final)?>' readonly>&nbsp;<a title="Calendario" href="javascript:abrir('fin','entryform');"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></a></td>
              </tr>
							<tr>
								<td align='right' valign="bottom" height="30">Informe</td>
								<td align='left' valign="bottom">
									<select  name='tipo' onChange="camposVisibles(this)">
										<option value='%'>Seleccione...</option>
										<option value='produccion_diaria_servicio' <?if(nvl($_POST["tipo"])=="produccion_diaria_servicio") echo "selected"?>>Producción diaria X servicio</option>
										<option value='produccionxmicro' <?if(nvl($_POST["tipo"])=="produccionxmicro") echo "selected"?>>Producción X Ruta</option>
										<option value='produccionxase' <?if(nvl($_POST["tipo"])=="produccionxase") echo "selected"?>>Producción X Ase</option>
										<option value='descarguediario' <?if(nvl($_POST["tipo"])=="descarguediario") echo "selected"?>>Descargue Diario</option>
										<option value='tiemposxruta' <?if(nvl($_POST["tipo"])=="tiemposxruta") echo "selected"?>>Promedio Tiempos X Ruta</option>
										<option value='tiemposxmovimiento' <?if(nvl($_POST["tipo"])=="tiemposxmovimiento") echo "selected"?>>Promedio Tiempos X Movimiento</option>
										<option value='sobrepesos' <?if(nvl($_POST["tipo"])=="sobrepesos") echo "selected"?>>Sobrepesos</option>
										<option value='consumocombustible' <?if(nvl($_POST["tipo"])=="consumocombustible") echo "selected"?>>Consumo Combustible</option>
										<option value='barridoxcoordinador' <?if(nvl($_POST["tipo"])=="barridoxcoordinador") echo "selected"?>>Longitudes Coordinador</option>
										<option value='consumosdiarios' <?if(nvl($_POST["tipo"])=="consumosdiarios") echo "selected"?>>Consumos Diarios</option>
										<option value='indicador_factorcarga' <?if(nvl($_POST["tipo"])=="indicador_factorcarga") echo "selected"?>>Indicador Factor Carga</option>
										<option value='indicador_factorutilizacion' <?if(nvl($_POST["tipo"])=="indicador_factorutilizacion") echo "selected"?>>Indicador Factor Utlización</option>

						
						
									</select>
								</td>
							</tr>
						</table>
						<div id="fillformgestra" style="<?if(nvl($_POST["tipo"])=="barridoxcoordinador") echo ""; else echo "display:none;visibility:hidden"?>">
							<table width="100%">
								<tr>
									<td align='left' valign="bottom" height="30">Coordinadores</td>
								</tr>
								<tr>
									<td align='left' valign="bottom"> 
									<?
									$cargos = array(8);
									obtenerIdCargos(8,$cargos);
									$qidCoord = $db->sql_query("SELECT id, nombre||' '||apellido as nombre 
											FROM personas WHERE id IN (SELECT id_persona FROM personas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."')) AND id_cargo IN (".implode(",",$cargos).") 
											ORDER BY nombre,apellido");
									while($coo = $db->sql_fetchrow($qidCoord)){
										$checked = "checked";
										if(isset($_POST["id_coordinador"]))
										{
											$checked = "";
											if(in_array($coo["id"], $_POST["id_coordinador"]))
												$checked = "checked";
										}?>
										<input type="checkbox" name="id_coordinador[]" value="<?=$coo["id"]?>" <?=$checked?>><?=$coo["nombre"]?><br>
									<?}?>
									</td>
								</tr>
							</table>
						</div>	

					</td>
				</tr>
				<tr>
					<td align="center" valign="bottom" height="50">
						<input type="submit" class="boton_verde" value="Aceptar"/>
					</td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>

<script type="text/javascript">

function revisar()
{
	if(document.entryform.inicio.value.replace(/ /g, '') ==''){
		window.alert('Por favor seleccione: Fecha Inicio');
		document.entryform.inicio.focus();
		return(false);
	}
	else{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2}/;
		if(!regexpression.test(document.entryform.inicio.value)){
			window.alert('[Fecha Inicio] no contiene un dato válido.');
			document.entryform.inicio.focus();
			return(false);
		}
	}

	if(document.entryform.fin.value.replace(/ /g, '') ==''){
		window.alert('Por favor seleccione: Fecha Final');
		document.entryform.fin.focus();
		return(false);
	}
	else{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2}/;
		if(!regexpression.test(document.entryform.fin.value)){
			window.alert('[Fecha Final] no contiene un dato válido.');
			document.entryform.fin.focus();
			return(false);
		}
	}

	if(document.entryform.inicio.value > document.entryform.fin.value){
		window.alert('La fecha de inicio no puede ser mayor que la fecha final');
		document.entryform.inicio.focus();
		return(false);
	}

	if(document.entryform.tipo.options[document.entryform.tipo.selectedIndex].value=='%'){
		window.alert('Por favor seleccione el tipo de informe');
		document.entryform.tipo.focus();
		return(false);
	}

	return true;
}

function camposVisibles(select)
{
	campo=select.options[select.selectedIndex].value;
	estilo = document.getElementById("fillformgestra");
	if(campo=="barridoxcoordinador"){
		estilo.style.display=''
		estilo.style.visibility='';
	}
	else{
		estilo.style.display='none'
		estilo.style.visibility='hidden';
	}
}



</script>
