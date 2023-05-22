<?
$estCerrados=array();
$qidEstCerr = $db->sql_query("SELECT id FROM mtto.estados_ordenes_trabajo WHERE cerrado");
while($queryEC = $db->sql_fetchrow($qidEstCerr))
{
	$estCerrados[] = $queryEC["id"];
}
?>
<div height="100%">
	<table class="tabla_grande"> 
		<tr>
			<td colspan=3 align="right" height="50" valign="center">
				<?if(!isset($sinBotones)){?>
				<a class="<?if($tipoListado=="diario") echo "boton_verde_active"; else echo "boton_verde";?>" href="<?=$CFG->wwwroot?>/mtto/calendario.php?mode=listado_diario" title="Diario">Programación Diaria</a> &middot; 
				<a class="<?if($tipoListado=="semanal") echo "boton_verde_active"; else echo "boton_verde";?>" href="<?=$CFG->wwwroot?>/mtto/calendario.php?mode=listado_semanal" title="Semanal">Programación Semanal</a> &middot;
				<a class="<?if($tipoListado=="mensual") echo "boton_verde_active"; else echo "boton_verde";?>" href="<?=$CFG->wwwroot?>/mtto/calendario.php?mode=listado_mensual" title="Mensual">Programación Mensual</a>&nbsp;&nbsp; 
				<?}?>
			</td>
		</tr>
		<tr>
			<td align="right"><?if($ant != ""){?><a href="<?=$CFG->wwwroot?>/mtto/calendario.php?fecha=<?=$ant?>"><img src="<?=$CFG->wwwroot?>/admin/iconos/transparente/flechaizquierda.gif" border=0></a><?}else echo "&nbsp;"?></td>
			<td height="50" width="80%" valign="middle" class="azul_16" align="center"><?=$titulo?></td>
			<td align="left"><?if($sig != ""){?><a href="<?=$CFG->wwwroot?>/mtto/calendario.php?fecha=<?=$sig?>"><img src="<?=$CFG->wwwroot?>/admin/iconos/transparente/flechaderecha.gif" border=0></a><?}else echo "&nbsp;"?></td>
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
								$qidRep = $db->sql_row("SELECT count(id) as num FROM mtto.ordenes_trabajo_fechas_programadas WHERE id_orden_trabajo='".$dx["idOT"]."'");
								if($qidRep["num"] != 1)
									$classTxt = "calendario_txt_reprogramadas";
								
								if(!in_array($dx["id_estado"],$estCerrados))
								{
									if($dx["date"]<date("Y-m-d H:i:s"))
										$classTxt = "calendario_txt_rojo";
									if($dx["date"]<date("Y-m-d H:i:s") && $qidRep["num"] != 1)
										$classTxt = "calendario_txt_atrasadasyreprogramadas";
								}

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

								$qidRep = $db->sql_row("SELECT count(id) as num FROM mtto.ordenes_trabajo_fechas_programadas WHERE id_orden_trabajo='".$dx["idOT"]."'");
								if($qidRep["num"] != 1)
									$classTxt = "calendario_txt_reprogramadas";

								if(!in_array($dx["id_estado"],$estCerrados))
								{
									if($dx["date"]<date("Y-m-d H:i:s"))
										$classTxt = "calendario_txt_rojo";

									if($dx["date"]<date("Y-m-d H:i:s") && $qidRep["num"] != 1)
										$classTxt = "calendario_txt_atrasadasyreprogramadas";
								}

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

										$qidRep = $db->sql_row("SELECT count(id) as num FROM mtto.ordenes_trabajo_fechas_programadas WHERE id_orden_trabajo='".$dx["idOT"]."'");
										if($qidRep["num"] != 1)
											$classTxt = "calendario_txt_reprogramadas";

										if(!in_array($dx["id_estado"],$estCerrados))
										{
											if($dx["date"]<date("Y-m-d H:i:s"))
												$classTxt = "calendario_txt_rojo";

											if($dx["date"]<date("Y-m-d H:i:s") && $qidRep["num"] != 1)
												$classTxt = "calendario_txt_atrasadasyreprogramadas";
										}

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
			<td colspan=3 align="center" width="100%"> <div id="cellediting"> </div></td>
		</tr>
		<?}?>
		<tr><td height="15">&nbsp;</td></tr>
		<tr>
			<td colspan=3 align="right" height="50" valign="center">
				<?
					if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["ordenes_opciones"])){
					if($tipoListado=="diario"){?>
					<a class="boton_verde" href="javascript:imprimir()" title="Imprimir Orden Trabajo">Imprimir Ordenes de Trabajo</a>&nbsp;&nbsp;
				<?}}
				if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["agregarOT"])){	?>
				<a class="boton_verde" href="javascript:abrirVentanaJavaScript('ordenes','900','500','<?=$CFG->wwwroot?>/mtto/ordenes.php?mode=agregar_facil')" title="Agregar Orden Trabajo">Agregar Orden Trabajo</a>&nbsp;&nbsp;
				<?}
				$linkEAE = $CFG->wwwroot."/mtto/bajar_excel_OT.php?mode=exportar_a_excel";
				if($tipoListado == "mensual")
					$linkEAE.="&inicio=".$primerDia."&final=".$ultimoDia;
				elseif($tipoListado == "semanal")
					$linkEAE.= "&inicio=".$semana["Monday"]."&final=".$semana["Sunday"];
				elseif($tipoListado=="diario")
					$linkEAE.="&inicio=".$fecha."&final=".$fecha;
				
				if(isset($link_bajar_OT_resultados))
					$linkEAE = $link_bajar_OT_resultados;

				?>
				<a class="boton_verde" href="<?=$linkEAE?>" title="Exportar a excel">Exportar a Excel</a>&nbsp;&nbsp;
			</td>
		</tr>
	</table>
</div>
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
			{key:"id", label:"No. OT"},	
			{key:"atiempo", label:"¿A tiempo?"},
			{key:"hora_planeada",label: "Hora Planeada", sortable: true, editor: new YAHOO.widget.TextboxCellEditor({
				LABEL_CANCEL:"Cerrar", 
				LABEL_SAVE:"Guardar",
				asyncSubmitter: function (callback, newValue) {
				var regexpression=/^[0-9]{2}:[0-9]{2}:[0-9]{2}/;
				if(!regexpression.test(newValue)){
					window.alert('Hora no contiene un dato válido.');
					callback();
				}else{
					var record = this.getRecord();
					var primaryKey ="";
					var datatable = this.getDataTable();
					var cols = datatable.getColumnSet().keys;
					for (var i = 0; i < cols.length; i++) {
							if (cols[i].key=="id") {
							primaryKey = '&' + cols[i].key + '=' + escape(record.getData(cols[i].key));
						}
					}
					record,
					column = this.getColumn(),
					oldValue = this.value,
					datatable;
					YAHOO.util.Connect.asyncRequest(
						'POST',
						'calendario.php', 
						{
								success:function(o) {
								callback(true,newValue);
							}	,
							failure:function(o) {
								alert(o.statusText);
								callback();
							},
							scope:this
						},
						'action=cellEdit&column=' + column.key + '&newValue=' + escape(newValue) + '&oldValue=' + escape(oldValue)+'&mode=hora_planeada' + primaryKey 
					); 
					}	
				}
				})
			},
			{key:"fecha_planeada", label:"Fecha Planeada", sortable: true, formatter:YAHOO.widget.DataTable.formatDate, editor: new YAHOO.widget.DateCellEditor({
				LABEL_CANCEL:"Cerrar", 
				LABEL_SAVE:"Guardar",	
				asyncSubmitter: function (callback, newValue) {
				var record = this.getRecord();
				var primaryKey ="";
				var datatable = this.getDataTable();
				var cols = datatable.getColumnSet().keys;
				for (var i = 0; i < cols.length; i++) {
						if (cols[i].key=="id") {
						primaryKey = '&' + cols[i].key + '=' + escape(record.getData(cols[i].key));
					}
				}
				record,
				column = this.getColumn(),
				oldValue = this.value,
				datatable;
				YAHOO.util.Connect.asyncRequest(
					'POST',
					'calendario.php', 
					{
							success:function(o) {
							callback(true,newValue);
							window.location.reload();
						}	,
						failure:function(o) {
							alert(o.statusText);
							callback();
						},
						scope:this
					},
					'action=cellEdit&column=' + column.key + '&newValue=' + escape(newValue) + '&oldValue=' + escape(oldValue)+'&mode=fecha_planeada' + primaryKey 
				); 
				}	
			})},
			{key:"prioridad", label:"Prioridad"},
			{key:"rutina", label:"Rutina", sortable: true},
			{key:"equipo", label:"Equipo", sortable: true},
			{key:"estado", label:"Estado", sortable: true},
			{key:"observaciones", label:"Observaciones", editor: new YAHOO.widget.TextboxCellEditor({
				LABEL_CANCEL:"Cerrar", 
				LABEL_SAVE:"Guardar",	
				asyncSubmitter: function (callback, newValue) {
				var record = this.getRecord();
				var primaryKey ="";
				var datatable = this.getDataTable();
				var cols = datatable.getColumnSet().keys;
				for (var i = 0; i < cols.length; i++) {
						if (cols[i].key=="id") {
						primaryKey = '&' + cols[i].key + '=' + escape(record.getData(cols[i].key));
					}
				}
				record,
				column = this.getColumn(),
				oldValue = this.value,
				datatable;
				YAHOO.util.Connect.asyncRequest(
					'POST',
					'calendario.php', 
					{
							success:function(o) {
							callback(true,newValue);
						}	,
						failure:function(o) {
							alert(o.statusText);
							callback();
						},
						scope:this
					},
					'action=cellEdit&column=' + column.key + '&newValue=' + escape(newValue) + '&oldValue=' + escape(oldValue)+'&mode=observaciones' + primaryKey 
				); 
				}}) 
			},
			{key:"editar", label:"Opciones"}

			];

		var myDataSource = new YAHOO.util.DataSource(YAHOO.example.Data.addresses);
		myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
		myDataSource.responseSchema = {
			fields: ["rutina","equipo","hora_planeada",{key:"fecha_planeada",parser:"date"},"estado","observaciones", {key:"id"}, "prioridad", "editar","atiempo"]
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
	url = '<?=$CFG->wwwroot."/mtto/ordenes.php?mode=editar&id="?>'+id;
	abrirVentanaJavaScript('ordenes','900','500',url);
}

function verNovedadesAbiertas(idEquipo)
{
	url = '<?=$CFG->wwwroot."/mtto/equipos.php?mode=hoja_vida&id_equipo="?>'+idEquipo+'&calendario=true';
	abrirVentanaJavaScript('ordenes','900','500',url);
}


<?if($tipoListado=="diario"){?>
function imprimir()
{<?
	$go = true;
	$condicion = "r.activa";
	$user=$_SESSION[$CFG->sesion]["user"];
	if($user["nivel_acceso"]!=1)
		$condicion="r.id IN (SELECT id_rutina FROM mtto.rutinas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."'))";

	$cons = "SELECT o.id, o.id_responsable, o.id_planeador, r.id as id_rutina
		FROM mtto.ordenes_trabajo o
		LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina
		LEFT JOIN mtto.estados_ordenes_trabajo est ON est.id=o.id_estado_orden_trabajo
		WHERE ".$condicion." AND o.fecha_planeada::date = '".$fecha."' AND NOT est.cerrado";
	$qid = $db->sql_query($cons);
	while($query = $db->sql_fetchrow($qid))
	{
		if($query["id_responsable"] == "")
			$go=false;		
		if($query["id_planeador"] == "")
			$go=false;

		$qidCargos = $db->sql_row("SELECT count(c.*) as num
			FROM mtto.ordenes_trabajo_actividades_cargos c 
			LEFT JOIN mtto.ordenes_trabajo_actividades a ON a.id=c.id_orden_trabajo_actividad
			WHERE a.id_orden_trabajo=".$query["id"]." AND id_persona IS NULL");
			if($qidCargos["num"] != 0)
				$go=false;
	}

	if($go){?>
		window.location.href='<?=$CFG->wwwroot?>/mtto/ordenes.php?mode=imprimirDiario&fecha=<?=$fecha?>';
	<?}else{?>
		url = '<?=$CFG->wwwroot."/mtto/ordenes.php?mode=imprimirFaltantes&fecha=".$fecha?>';
		abrirVentanaJavaScript('imprimirfallos','900','500',url);
	<?}
?>
}
<?}?>

</script>
