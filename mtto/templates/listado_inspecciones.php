<?
include($CFG->dirroot."/templates/header_2panel.php");
?>
<div id="right1">
<?/*include($CFG->dirroot."/mtto/templates/busqueda_ordenes_form.php");*/?>
</div>
<div id="center1">

<div height="100%">
	<table class="tabla_grande"> 
		<tr>
			<td colspan=3 align="right" height="50" valign="center">
				<a class="<?if($tipoListado=="diario") echo "boton_verde_active"; else echo "boton_verde";?>" href="<?=$CFG->wwwroot?>/mtto/inspecciones.php?mode=listar_inspecciones_diario" title="Diario">Inspecciones Diarias</a> &middot; 
				<a class="<?if($tipoListado=="semanal") echo "boton_verde_active"; else echo "boton_verde";?>" href="<?=$CFG->wwwroot?>/mtto/inspecciones.php?mode=listar_inspecciones_semanales" title="Semanal">Inspecciones Semanales</a> &middot;
				<a class="<?if($tipoListado=="mensual") echo "boton_verde_active"; else echo "boton_verde";?>" href="<?=$CFG->wwwroot?>/mtto/inspecciones.php?mode=listar_inspecciones_mensuales" title="Mensual">Inspecciones Mensuales</a>&nbsp;&nbsp; 
			</td>
		</tr>
		<tr>
			<td align="right"><?if($ant != ""){?><a href="<?=$CFG->wwwroot?>/mtto/inspecciones.php?fecha=<?=$ant?>"><img src="<?=$CFG->wwwroot?>/admin/iconos/transparente/flechaizquierda.gif" border=0></a><?}else echo "&nbsp;"?></td>
			<td height="50" width="80%" valign="middle" class="azul_16" align="center"><?=$titulo?></td>
			<td align="left"><?if($sig != ""){?><a href="<?=$CFG->wwwroot?>/mtto/inspecciones.php?fecha=<?=$sig?>"><img src="<?=$CFG->wwwroot?>/admin/iconos/transparente/flechaderecha.gif" border=0></a><?}else echo "&nbsp;"?></td>
		</tr>
		<?if($tipoListado=="mensual"){?>
		<tr>
			<td colspan=3 align="center" width="100%">
				<table class="tabla_calendario">
					<tr>
						<td class="calendario_titulos" width='14%'>Lunes</td>
						<td class="calendario_titulos" width='14%'>Martes</td>
						<td class="calendario_titulos" width='14%'>Miercóles</td>
						<td class="calendario_titulos" width='14%'>Jueves</td>
						<td class="calendario_titulos" width='14%'>Viernes</td>
						<td class="calendario_titulos" width='14%'>Sábado</td>
						<td class="calendario_titulos" width='14%'>Domingo</td>
					</tr>
					<?  
					echo "<tr>\n";
					for ($i=0;$i<7;$i++)
					{ 
						$class = "calendario_blanco";
						if(fmod($i,2)!=0)
							$class = "calendario_azul";

						$ot = "";
						if(isset($datos[$dia_actual]))
						{
							$ot = "<table width='100%'>";
							foreach($datos[$dia_actual] as $dx)
							{
								$classTxt = "calendario_txt";
								if(!in_array($dx["id_estado"],$estCerrados))
									if($dx["date"]<date("Y-m-d H:i:s"))
										$classTxt = "calendario_txt_rojo";

								$ot.= "<tr><td class='".$classTxt."'>".$dx["line"]."</td></tr>";
							}
							$ot.="</table>";
						}

						if ($i < $numero_dia)
							echo "<td class=\"".$class."\" valign='top' width='14%'>&nbsp;</td>\n";
						else{
							echo "<td class=\"".$class."\" valign='top' width='14%'><table width='100%'><tr><td class='calendario_td'><a href='".$CFG->wwwroot."/mtto/calendario.php?fecha=".$anio."-".$mes."-".$dia_actual."' class='calendario_numeros'>".$dia_actual."</a></td></tr></table>".$ot."</td>\n";
							$dia_actual++;
						}
					}
					echo "</tr>\n";
					//recorro todos los demás días hasta el final del mes
					$numero_dia = 0;
					while ($dia_actual <= ultimoDia($mes,$anio))
					{
						$class = "calendario_blanco";
						if(fmod($i,2)!=0)
							$class = "calendario_azul";

						$ot = "";
						if(isset($datos[$dia_actual]))
						{
							$ot = "<table width='100%'>";
							foreach($datos[$dia_actual] as $dx)
							{
								$classTxt = "calendario_txt";
								$ot.= "<tr><td class='".$classTxt."'>".$dx["line"]."</td></tr>";
							}
							$ot.="</table>";
						}

						//si estamos a principio de la semana escribo el <TR>
						if ($numero_dia == 0)
							echo "<tr>\n";
						echo "<td class=\"".$class."\" valign='top' width='14%'><table width='100%'><tr><td class='calendario_td'><a href='".$CFG->wwwroot."/mtto/calendario.php?fecha=".$anio."-".$mes."-".$dia_actual."' class='calendario_numeros'>".$dia_actual."</a></td></tr></table>".$ot."</td>\n";

						$dia_actual++;
						$numero_dia++;
						if ($numero_dia == 7){
							$numero_dia = 0;
							echo "</tr>\n";
						}
						$i++;
					}
					//compruebo que celdas me faltan por escribir vacías de la última semana del mes (si no quedó en domingo)
					if($numero_dia != 0)
					{
						for ($j=$numero_dia;$j<7;$j++)
						{
							$class = "calendario_blanco";
							if(fmod($i,2)!=0)
								$class = "calendario_azul";

							echo "<td class=\"".$class."\" width='14%'>&nbsp;</td>\n";
							$i++;
						}
						echo "</tr>\n";
					}
					?>
				</table>
			</td>
		</tr>
		<?}elseif($tipoListado=="semanal"){?>
		<tr>
			<td colspan=3 align="center" width="100%">
				<table class="tabla_calendario_dos">
					<tr>
						<td class="calendario_titulos" width='9px'  >Hora</td>
						<?
						list($anio,$mes,$dia)=split("-",$semana["Monday"]);
						for($i=0;$i<7;$i++){
							$diaTexto = ucfirst(strftime("%A %e",strtotime(date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + $i * 24 * 60 * 60))));
							echo "<td class=\"calendario_titulos\" width='13%'>".$diaTexto."</td>\n";
						}?>
					</tr>
					<?
					if(count($horas)==0)
					{
						$prim = "08";	
						$ult = "17";
					}
					else
					{
						$prim = array_shift($horas);
						if(count($datos)>1)
							$ult = array_pop($horas);
						else
							$ult = $prim;

						if($prim>8)
							$prim = "08";
						if($ult<17)
							$ult = "17";
					}
				
					$j=1;
					for($i=$prim;$i<=$ult;$i++)
					{
						$class = "calendario_blanco";
						if(fmod($j,2)!=0)
							$class = "calendario_azul";

						$hi = strftime("%H",strtotime(date("Y-m-d ".$i.":00:s")));
						$hf = $i+1;
						$hf = strftime("%H:%M",strtotime(date("Y-m-d ".$hf.":00:s")));
						echo "<tr>
								<td class=\"".$class."\"><table width='100%'><tr><td class='calendario_txt' align='center'>".$hi.":00 - ".$hf."</td></tr></table></td>";

						if(isset($datos[$hi]))
						{
							for($d=0;$d<7;$d++){
								echo "<td class=\"".$class."\" valign='top'>";
								$diaTexto = ucfirst(strftime("%d",strtotime(date("Y-m-d",mktime(0,0,0, $mes,$dia,$anio) + $d * 24 * 60 * 60))));
								if(isset($datos[$hi][$diaTexto]))
								{
									$ot = "<table width='100%'>";
									foreach($datos[$hi][$diaTexto] as $dx)
									{
										$classTxt = "calendario_txt";
										$ot.= "<tr><td class='".$classTxt."'>".$dx["line"]."</td></tr>";
									}
									$ot.="</table>";
									echo $ot;
								}
								else
									echo "&nbsp;";
								echo "</td>";
							}
						}else
						{
							for($d=0;$d<7;$d++){
								echo "<td class=\"".$class."\">&nbsp;</td>";
							}
						}
						echo "</tr>";	
						$j++;
					}
					?>
				</table>	
			</td>
		</tr>
		<?}else{?>
		<tr>
			<td colspan=3 align="center" width="100%" valign="top"> <div id="cellediting"> </div></td>
		</tr>
		<?}?>
		<tr><td height="15">&nbsp;</td></tr>
		<tr>
			<td colspan=3 align="right" height="50" valign="center">
				<a class="boton_verde" href="javascript:abrirVentanaJavaScript('insp_form','800','500','<?=$CFG->wwwroot?>/mtto/inspecciones.php?mode=agregar')" title="Agregar Inspección">Agregar Inspección</a>&nbsp;&nbsp;
			</td>
		</tr>
	</table>
</div>
</div>
<?include($CFG->dirroot."/templates/footer_2panel.php");?>

<script type="text/javascript">
<?if($tipoListado=="diario" || $tipoListado=="diarioResultados"){?>
YAHOO.example.Data = { addresses: [<?=implode(",",$datos)?>]}
YAHOO.util.Event.addListener(window, "load", function() {
		YAHOO.example.InlineCellEditing = function() {
		// Custom formatter for "address" column to preserve line breaks
		var formatAddress = function(elCell, oRecord, oColumn, oData) {
		elCell.innerHTML = "<pre class=\"address\">" + oData + "</pre>";
		};

		var myColumnDefs = [
			{key:"id"},
			{key:"fecha", label:"Fecha", sortable: true},
			{key:"hora", label:"Hora", sortable: true},
			{key:"vehiculo", label:"Vehiculo", sortable: true},
			{key:"reporto", label:"Reportó", sortable: true},
			{key:"editar", label:"Opciones"}
			];

		var myDataSource = new YAHOO.util.DataSource(YAHOO.example.Data.addresses);
		myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
		myDataSource.responseSchema = {
			fields: ["fecha","hora","vehiculo","reporto", {key:"id"},  "editar"]
		}

		var myDataTable = new YAHOO.widget.DataTable("cellediting", myColumnDefs, myDataSource, {
				 dateOptions:{format:'%Y/%m/%d'}
				});

		// Set up editing flow
		var highlightEditableCell = function(oArgs) {
			var elCell = oArgs.target;
			if(YAHOO.util.Dom.hasClass(elCell, "yui-dt-editable")) {
				this.highlightCell(elCell);
			}
		};
		myDataTable.subscribe("cellMouseoverEvent", highlightEditableCell);
		myDataTable.subscribe("cellMouseoutEvent", myDataTable.onEventUnhighlightCell);
		myDataTable.subscribe("cellClickEvent", myDataTable.onEventShowCellEditor);
		var oColumn = myDataTable.getColumn(0);
		myDataTable.hideColumn(oColumn);

		return {
			oDS: myDataSource,
			oDT: myDataTable
		};
		}();
});

<?}?>

function edicion(id)
{
	url = '<?=$CFG->wwwroot."/mtto/inspecciones.php?mode=editar&id="?>'+id;
	abrirVentanaJavaScript('insp_form','800','500',url);
}

</script>
