<form name="entryform_filtro" action="<?=$ME?>" method="GET"  class="form" enctype="multipart/form-data">
<input type="hidden" name="mode" value="agregar_movimientos">
<input type="hidden" name="esquema" value="<?=$esquema?>">
<input type="hidden" name="fecha" value="<?=$fecha?>">
<table width="100%">
	<tr>
		<td height="40" colspan=2 align="center"><span class="azul_16"><strong><?=$titulo?> MOVIMIENTO</strong></span></td>
	</tr>
	<tr>
		<td>
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td width="140">Filtrar por turno : </td>
					<td><select  name="id_turno" id='id_turno' onchange="this.form.submit();"><?=$turnos?></select></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>

<form name="entryform" action="<?=$ME?>" method="POST"  class="form" enctype="multipart/form-data" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="esquema" value="<?=$esquema?>">

<table width="100%">
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center" id="tabla_mov">
							<tr>
								<td align="center">RUTA</td>
								<td align="center">TIPO RESIDUO</td>
								<td align="center">SERVICIO</td>
								<td align="center">FECHA INICIO</td>
								<td align="center">HORA INICIO</td>
								<td align="center">No. ORDEN</td>
								<td align="center">VEHÍCULO</td>
								<td align="center">NO INCLUIR</td>
							</tr>
							<?
							$i=0;
							while($micro = $db->sql_fetchrow($qid))
							{
								$frec = $db->sql_row("SELECT * FROM micros_frecuencia WHERE id_micro='".$micro["id"]."' AND dia='".strftime("%u",strtotime($fecha))."'");
								echo "<tr id=\"celda_".$i."\">\n";
								echo '<input type="hidden" name="micro_'.$i.'" value="'.$micro["id"].'">';
								echo '<input type="hidden" name="turno_'.$i.'" value="'.$frec["id_turno"].'">';
								echo '<input type="hidden" name="centro_'.$i.'" id ="centro_'.$i.'" value="'.$micro["id_centro"].'">';
								echo "<td align='center'>".$micro["codigo"]."</td>\n";
								echo "<td align='center'>".$micro["tipo_residuo"]."</td>\n";
								echo "<td align='center'>".$micro["servicio"]."</td>\n";
								echo "<td align='center'><input type='text' size='12' class='casillatext_fecha' name='inicio_".$i."' value='".$fecha."' readonly ></td>";
								echo "<td align='center'><input type='text' size='12' class='casillatext_fecha' name='hora_".$i."' id='hora_".$i."' value='".$frec["hora_inicio"]."' readonly >&nbsp;<a title=\"Calendario\" href=\"javascript:abrirSoloHora('hora_".$i."','entryform');\"><img alt=\"Hora\" src='".$CFG->wwwroot."/admin/iconos/transparente/icon-clock.png' border='0'></a><a href=\"javascript:copiar('hora','".$i."')\"><img border=0 alt=\"Copiar Valor Hacia Abajo\" title=\"Copiar Valor Hacia Abajo\" src=\"".$CFG->wwwroot."/admin/iconos/transparente/flechaabajo.gif\"></a></td>\n";
								echo "<td align='center'><input type='text' size='6' class='casillatext' name='numero_orden_".$i."' id='numero_orden_".$i."' value=''></td>";
								$db->crear_select("SELECT v.id, v.codigo || '/' || v.placa || CASE WHEN (select count(o.id) FROM mtto.ordenes_trabajo o WHERE o.id_equipo=e.id AND o.fecha_planeada::date = '".$fecha."') != 0 then '(Mantenimiento Programado)' else '' end as nombre
										FROM vehiculos v
										LEFT JOIN mtto.equipos e ON v.id=e.id_vehiculo
										LEFT JOIN tipos_vehiculos_servicios tp ON tp.id_tipo_vehiculo=v.id_tipo_vehiculo
										WHERE v.id_estado<>4 and v.id_centro = '".$micro["id_centro"]."' AND tp.id_servicio='".$micro["id_servicio"]."'
										ORDER BY v.codigo,v.placa",$vehiculos,$micro["id_vehiculo"]);
								echo "<td align='left'><select  name=\"vehiculo_".$i."\" id='vehiculo_".$i."' >".$vehiculos."</select></td>";
								echo "<td align='center'><a href=\"javascript:delete_celda('celda_".$i."')\" class=\"link_verde\" title=\"No Incluir\">N</a> </td>\n";
								echo "</tr>\n";
								$i++;
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
var rowNum = <?=$i?>;

function revisar()
{
	<?if($esquema == "rec"){?>
	for(j=0; j<rowNum; j++){
		celda = 'celda_'+ j;
		if (document.getElementById(celda))
		{
			//si es cali o valle, se valida el número de orden
			if(document.getElementById('centro_'+j).value =='1' ||  document.getElementById('centro_'+j).value =='2')
			{
				if(document.getElementById('numero_orden_'+j).value.replace(/ /g, '') ==''){
					window.alert('Por favor escriba el Número de Orden');
					document.getElementById('numero_orden_'+j).focus();
					return(false);
				}
				else{
					var regexpression=/(^-?\d+$)|(^-?\d+\.\d+$)/;
					if(!regexpression.test(document.getElementById('numero_orden_'+j).value)){
						window.alert('[Número de Orden] no contiene un dato válido.');
						document.getElementById('numero_orden_'+j).focus();
						return(false);
					}
				}
			}

			//que haya escogido vehiculo
			if(document.getElementById('vehiculo_'+j).value =='%'){
				window.alert('Por favor seleccione: Vehículo');
				document.getElementById('vehiculo_'+j).focus();
				return(false);
			}


		}



		<??>


	}
	<?//echo $valAdicional;
	}
	?>

	return(true);
}

function delete_celda(id_row){
	var tbl = document.getElementById('tabla_mov');
	var row = document.getElementById(id_row);
	tbl.getElementsByTagName("tbody")[0].removeChild(row);
}

function copiar(indice,sub)
{
	casilla = indice+'_'+sub;
	valor = document.getElementById(casilla).value;
	for(j=sub;j<rowNum;j++)
	{
		celda  = indice+'_'+j;
		if (document.getElementById(celda))
			document.getElementById(celda).value=valor;
	}
}



</script>

<?
include($CFG->templatedir . "/resize_window.php");
?>

