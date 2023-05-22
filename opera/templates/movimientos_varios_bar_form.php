<?

$dx = $bolsas = array();
while($micro = $db->sql_fetchrow($qid))
{
	$dx[$micro["id"]] = $micro;

	//operarios
	$qidO = $db->sql_row("SELECT id_persona FROM frecuencias_operarios o LEFT JOIN micros_frecuencia f ON f.id=o.id_frecuencia WHERE f.id_micro=".$micro["id"]." AND dia='".strftime("%u",strtotime($fecha))."'");
	$dx[$micro["id"]]["id_persona"] = nvl($qidO["id_persona"]);

	$frec = $db->sql_row("SELECT hora_inicio, id_turno FROM micros_frecuencia WHERE id_micro='".$micro["id"]."' AND dia='".strftime("%u",strtotime($fecha))."'");
	$dx[$micro["id"]]["hora_inicio"] = $frec["hora_inicio"]	;
	$dx[$micro["id"]]["turno"] = $frec["id_turno"]	;

	//bolsas
	$qidB = $db->sql_query("SELECT tipo, b.numero_inicio, b.id_tipo_bolsa FROM frecuencias_bolsas b LEFT JOIN micros_frecuencia f ON f.id=b.id_frecuencia LEFT JOIN bar.tipos_bolsas t ON t.id=b.id_tipo_bolsa WHERE f.id_micro=".$micro["id"]." AND dia='".strftime("%u",strtotime($fecha))."' ORDER BY tipo");
	while($queryBo = $db->sql_fetchrow($qidB))
	{
		$bolsas[$queryBo["id_tipo_bolsa"]] = $queryBo["tipo"];
		$dx[$micro["id"]]["bolsas"][$queryBo["id_tipo_bolsa"]] = $queryBo["numero_inicio"];
	}
}

?>


<form name="entryform" action="<?=$ME?>" method="POST"  class="form" enctype="multipart/form-data" onSubmit="return revisar()">
<input type="hidden" name="mode" value="<?=$newMode?>">
<input type="hidden" name="esquema" value="<?=$esquema?>">

<table width="100%">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong><?=$titulo?> MOVIMIENTO</strong></span></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840" align="center" id="tabla_mov">
							<tr>
								<td align="center">RUTA</td>
								<td align="center">SERVICIO</td>
								<td align="center">OPERARIO</td>
								<td align="center">FECHA INICIO</td>
								<td align="center">HORA INICIO</td>
								<td align="center">VEHÍCULO</td>
								<?foreach($bolsas as $tipoB){?>
								<td align="center"><?=$tipoB?></td>
								<?}?>
								<td align="center">NO INCLUIR</td>
							</tr>
							<?
							$i=0;
							foreach($dx as $micro)
							{
								echo "<tr id=\"celda_".$i."\">\n";
								echo '<input type="hidden" name="micro_'.$i.'" value="'.$micro["id"].'">';
								echo '<input type="hidden" name="turno_'.$i.'" value="'.$micro["turno"].'">';
								echo "<td align='center'>".$micro["codigo"]."</td>\n";
								echo "<td align='center'>".$micro["servicio"]."</td>\n";
								$db->crear_select("SELECT p.id, p.nombre||' '||p.apellido 
										FROM personas p 
										LEFT JOIN personas_cargos pc ON pc.id_persona=p.id
										WHERE pc.id_cargo = 23 AND p.id IN (SELECT id_persona FROM personas_centros WHERE id_centro='$micro[id_centro]') AND p.id NOT IN (SELECT id_persona FROM bar.movimientos_personas LEFT JOIN bar.movimientos ON bar.movimientos.id=bar.movimientos_personas.id_movimiento WHERE bar.movimientos.inicio::date='".$fecha."')
										ORDER BY p.nombre",$operario,$micro["id_persona"]);
								echo "<td><select name=\"persona_".$i."\" id=\"persona_".$i."\">".$operario."</select></td>";
								echo "<td align='center'><input type='text' size='12' class='casillatext_fecha' name='inicio_".$i."' value='".$fecha."' readonly ></td>";
								echo "<td align='center'><input type='text' size='12' class='casillatext_fecha' name='hora_".$i."' id='hora_".$i."' value='".$micro["hora_inicio"]."' readonly >&nbsp;<a title=\"Calendario\" href=\"javascript:abrirSoloHora('hora_".$i."','entryform');\"><img alt=\"Hora\" src='".$CFG->wwwroot."/admin/iconos/transparente/icon-clock.png' border='0'></a><a href=\"javascript:copiar('hora','".$i."')\"><img border=0 alt=\"Copiar Valor Hacia Abajo\" title=\"Copiar Valor Hacia Abajo\" src=\"".$CFG->wwwroot."/admin/iconos/transparente/flechaabajo.gif\"></a></td>\n";
								$db->crear_select("SELECT v.id, v.codigo || '/' || v.placa || CASE WHEN (select count(o.id) FROM mtto.ordenes_trabajo o WHERE o.id_equipo=e.id AND o.fecha_planeada::date = '".$fecha."') != 0 then '(Mantenimiento Programado)' else '' end as nombre
										FROM vehiculos v
										LEFT JOIN mtto.equipos e ON v.id=e.id_vehiculo
										LEFT JOIN tipos_vehiculos_servicios tp ON tp.id_tipo_vehiculo=v.id_tipo_vehiculo
										WHERE v.id_centro = '".$micro["id_centro"]."' AND tp.id_servicio='".$micro["id_servicio"]."'
										ORDER BY v.codigo,v.placa",$vehiculos,$micro["id_vehiculo"]);
								echo "<td align='left'><select  name=\"vehiculo_".$i."\" id='vehiculo_".$i."' >".$vehiculos."</select></td>";
								foreach($bolsas as $idTipo =>$tipoB){
									echo "<td>";
									if(isset($micro["bolsas"][$idTipo]))
										echo "<input type='text' size='4' class='casillatext' name='bolsa_".$idTipo."_".$i."' value='".$micro["bolsas"][$idTipo]."'>";
									else
										echo "&nbsp;";
									echo "</td>";
								}	
			
			
			
			
			
			
			
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
	for(j=0; j<rowNum; j++){
		celda = 'celda_'+ j;
		if (document.getElementById(celda))
		{
			if(document.getElementById('persona_'+j).value =='%'){
				window.alert('Por favor seleccione: Operario');
				document.getElementById('persona_'+j).focus();
				return(false);
			}
		}
	}

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

