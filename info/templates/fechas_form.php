<?
$days_of_month = date('t',strtotime(date("Y-m-01", strtotime("last month"))));
$inicio=date("Y-m-01", strtotime("last month"));
if (simple_me($ME)=="46.php") $inicio=date("Y-m-01", strtotime("-2 month"));
$final=date("Y-m-".$days_of_month,strtotime("last month"));

if(isset($_POST["inicio"])) $inicio=$_POST["inicio"];
if(isset($_POST["final"])) $final=$_POST["final"];

/*
41 :	centro,  ase (con opcion de todos) y turno
95: 	centro,  ase (con opcion de todos)
135:	centro y turno
173: centro,  ase (con opción todos) y vistas (Por Fecha Vehículo, Por Sitio Disposición)
217: centro
242: coordinadores
273: centro y turno y vistas (Por Tipo Vehículo, Por Vehículo)
319: centro y ase (sin opción de todos)
*/

?>
<table width="100%">
  <tr>
    <td valign="top">
			<form name="entryform" action="<?=$ME?>" method="POST" onSubmit="return revisar()" class="form">
      <table width="60%" cellpadding="5" cellspacing="3" align="center">
        <tr>
          <td>
						<?if(simple_me($ME)!="20.php"){?>
						<table width="100%" border=1 bordercolor="#7fa840" align="right">
							<tr>
								<td align="center" valign="center" width='50%'>Inicio 
									<input type='text' size="10" id="f_inicio" class="casillatext_fecha" name='inicio' value='<?=$inicio?>'  readonly /><button id="b_inicio" onclick="javascript:showCalendarSencillo('f_inicio','b_inicio')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
								<td align="center" valign="center">Fin 
									<input type='text' size="10" id="f_final" class="casillatext_fecha" name='final' value='<?=$final?>'  readonly /><button id="b_final" onclick="javascript:showCalendarSencillo('f_final','b_final')"><img alt="Fecha" src='<?=$CFG->wwwroot?>/admin/iconos/transparente/icon-date.gif' border='0'></button>
								</td>
							</tr>
						</table>
						<?}

						//centro,  ase (con opcion de todos) y turno
						if(simple_me($ME)=="1.php" || simple_me($ME)=="2.php" || simple_me($ME)=="5.php" || simple_me($ME)=="6.php" || simple_me($ME)=="8.php" || simple_me($ME)=="14.php" || simple_me($ME)=="19.php" || simple_me($ME)=="21.php" || simple_me($ME)=="22.php" || simple_me($ME)=="23.php"  || simple_me($ME)=="24.php"  || simple_me($ME)=="25.php"  || simple_me($ME)=="41.php" || simple_me($ME)=="43.php" || simple_me($ME)=="50.php" || simple_me($ME)=="56.php"){?>
						<table width="100%" border=1 bordercolor="#7fa840" align="right">
							<tr>
								<td align='center' width='33%'>Centro &nbsp;&nbsp;
								<select  name='id_centro'  onChange="updateRecursive_id_turno(this), updateRecursive_id_ase_dos(this)">
									<?
										$qidCn = $db->sql_query("SELECT id, centro 
											FROM centros WHERE id IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') 
											ORDER BY centro");
										while($cn = $db->sql_fetchrow($qidCn)){
											$selected = "";
											if($centro == $cn["id"]) $selected = " selected";
											echo '<option value="'.$cn["id"].'" '.$selected.'>'.$cn["centro"].'</option>';
									}
									?>
									</select>
								</td>
								<td align='center' width='33%'>
									<div id="id_ase_dos">Ase &nbsp;&nbsp;
									<select  name='id_ase' id='id_ase_dos'  style="width:150px">
										<option value=''>Todas</option>
											<?
											$qidAs = $db->sql_query("SELECT a.id, a.ase FROM ases a WHERE a.id_centro=".$centro);
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
								<td align='center' width='33%'>
									<div id="id_turno">Turno &nbsp;&nbsp;
									<select  name='id_turno' id='id_turno'  style="width:150px">
										<option value="">Todos</option>
										<?
											$qidTur = $db->sql_query("SELECT t.id, t.turno FROM turnos t LEFT JOIN centros c ON c.id_empresa = t.id_empresa WHERE c.id=".$centro);
											while($tur = $db->sql_fetchrow($qidTur))
											{
												$selected = "";
												if(isset($id_turno) && $id_turno == $tur["id"]) $selected = " selected";
													echo '<option value="'.$tur["id"].'" '.$selected.'>'.$tur["turno"].'</option>';
											}
										?>
									</select>
									</div>
								</td>
								<?if(simple_me($ME)=="23.php" || simple_me($ME)=="1.php"){?>
								<td align='center' width='33%'>
									Por días &nbsp;&nbsp;
									<select  name='dia'>
										<option value=''>Todos</option>
										<option value='2' <?if(isset($dia) && $dia==2) echo "selected";?>>Lunes</option>
										<option value='3' <?if(isset($dia) && $dia==3) echo "selected";?>>Martes</option>
										<option value='4' <?if(isset($dia) && $dia==4) echo "selected";?>>Miercóles</option>
										<option value='5' <?if(isset($dia) && $dia==5) echo "selected";?>>Jueves</option>
										<option value='6' <?if(isset($dia) && $dia==6) echo "selected";?>>Viernes</option>
										<option value='7' <?if(isset($dia) && $dia==7) echo "selected";?>>Sábado</option>
										<option value='1' <?if(isset($dia) && $dia==1) echo "selected";?>>Domingo</option>
									</select>
								</td>
								<?}
								if(simple_me($ME) == "8.php"){?>
								<td align='center' valign="center" width='33%'>Vista&nbsp;&nbsp;
									<select  name='vista'>
									<option value="detalle" <?if(isset($vista) && $vista=="detalle") echo "selected"?>>Detallada</option>
									<option value="mensual" <?if(isset($vista) && $vista=="mensual") echo "selected"?>>Mensual</option>
									</selected>
								</td>
							<?}?>
							</tr>
						</table>
						<?}

						//centro,  ase (con opcion de todos)
						
						if(simple_me($ME)=="11.php" || simple_me($ME)=="12.php" ){?>
						<table width="100%" border=1 bordercolor="#7fa840" align="right">
							<tr>
								<td align='center' width='33%'>Centro &nbsp;&nbsp;
								<select  name='id_centro'  onChange="updateRecursive_id_ase_dos(this)">
									<?
										$qidCn = $db->sql_query("SELECT id, centro 
											FROM centros WHERE id IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') 
											ORDER BY centro");
										while($cn = $db->sql_fetchrow($qidCn)){
											$selected = "";
											if($centro == $cn["id"]) $selected = " selected";
											echo '<option value="'.$cn["id"].'" '.$selected.'>'.$cn["centro"].'</option>';
									}
									?>
									</select>
								</td>
								<td align='center' width='33%'>
									<div id="id_ase_dos">Ase &nbsp;&nbsp;
									<select  name='id_ase' id='id_ase_dos'  style="width:150px">
										<option value=''>Todas</option>
											<?
											$qidAs = $db->sql_query("SELECT a.id, a.ase FROM ases a WHERE a.id_centro=".$centro);
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
							</tr>
						</table>
						<?}
						


						//centro y turno
						if(simple_me($ME)=="3.php"   || simple_me($ME)=="18.php" || simple_me($ME)=="26.php" || simple_me($ME)=="27.php"){?>
						<table width="100%" border=1 bordercolor="#7fa840" align="right">
							<tr>
								<td align='center' width='50%'>Centro &nbsp;&nbsp;
								<select  name='id_centro'  onChange="updateRecursive_id_turno(this)">
									<?
										$qidCn = $db->sql_query("SELECT id, centro 
											FROM centros WHERE id IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') 
											ORDER BY centro");
										while($cn = $db->sql_fetchrow($qidCn)){
											$selected = "";
											if($centro == $cn["id"]) $selected = " selected";
											echo '<option value="'.$cn["id"].'" '.$selected.'>'.$cn["centro"].'</option>';
									}
									?>
									</select>
								</td>
								<td align='center' width='50%'>
									<div id="id_turno">Turno &nbsp;&nbsp;
									<select  name='id_turno' id='id_turno'  style="width:150px">
										<option value="">Todos</option>
										<?
											$qidTur = $db->sql_query("SELECT t.id, t.turno FROM turnos t LEFT JOIN centros c ON c.id_empresa = t.id_empresa WHERE c.id=".$centro);
											while($tur = $db->sql_fetchrow($qidTur))
											{
												$selected = "";
												if(isset($id_turno) && $id_turno == $tur["id"]) $selected = " selected";
													echo '<option value="'.$tur["id"].'" '.$selected.'>'.$tur["turno"].'</option>';
											}
										?>
									</select>
									</div>
								</td>
							</tr>
						</table>
						<?}

						//centro,  ase (con opción todos) y vistas (Por Fecha Vehículo, Por Sitio Disposición)
						if(simple_me($ME)=="4.php"){?>
						<table width="100%" border=1 bordercolor="#7fa840" align="right">
							<tr>
								<td align='center' width='33%'>Centro &nbsp;&nbsp;
									<select  name='id_centro' onChange="updateRecursive_id_ase_dos(this)">
									<?
									$qidCn = $db->sql_query("SELECT id, centro 
										FROM centros WHERE id IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') 
										ORDER BY centro");
									while($cn = $db->sql_fetchrow($qidCn)){
										$selected = "";
										if($centro == $cn["id"]) $selected = " selected";
										echo '<option value="'.$cn["id"].'" '.$selected.'>'.$cn["centro"].'</option>';
									}
									?>
									</select>
								</td>
								<td align='center' width='33%'>
									<div id="id_ase_dos">Ase &nbsp;&nbsp;
									<select  name='id_ase' id='id_ase_dos'  style="width:150px">
										<option value=''>Todas</option>
											<?
											$qidAs = $db->sql_query("SELECT a.id, a.ase FROM ases a WHERE a.id_centro=".$centro);
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
								<td align='center' valign="center" width='33%'>Vista&nbsp;&nbsp;
									<select  name='vista'>
									<option value="fecha" <?if(isset($vista) && $vista=="fecha") echo "selected"?>>Por Fecha Vehículo</option>
									<option value="sitio" <?if(isset($vista) && $vista=="sitio") echo "selected"?>>Por Sitio Disposición</option>
									</selected>
								</td>
							</tr>
						</table>
						<?}

						//solo centro
						if(simple_me($ME)=="13.php" || simple_me($ME)=="15.php" || simple_me($ME)=="20.php" || simple_me($ME)=="29.php" || simple_me($ME)=="31.php" || simple_me($ME)=="32.php" || simple_me($ME)=="34.php" || simple_me($ME)=="35.php" || simple_me($ME)=="36.php" || simple_me($ME)=="38.php" || simple_me($ME)=="42.php" || simple_me($ME)=="51.php" || simple_me($ME)=="52.php" || simple_me($ME)=="58.php" || simple_me($ME)=="59.php" || simple_me($ME)=="60.php"){?>
						<table width="100%" border=1 bordercolor="#7fa840" align="right">
							<tr>
								<td align='center' width='50%'>Centro &nbsp;&nbsp;
								<select  name='id_centro'>
									<?
										$qidCn = $db->sql_query("SELECT id, centro 
											FROM centros WHERE id IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') 
											ORDER BY centro");
										while($cn = $db->sql_fetchrow($qidCn)){
											$selected = "";
											if($centro == $cn["id"]) $selected = " selected";
											echo '<option value="'.$cn["id"].'" '.$selected.'>'.$cn["centro"].'</option>';
									}
									?>
									</select>
								</td>
							</tr>
						</table>
					<?}

					if(simple_me($ME)=="45.php"){?>
						<table width="100%" border=1 bordercolor="#7fa840" align="right">
							<tr>
								<td align='center' width='33%'>Centro &nbsp;&nbsp;
								<select  name='id_centro' onChange="updateRecursive_id_vehiculo(this)">
									<?
										$qidCn = $db->sql_query("SELECT id, centro 
											FROM centros WHERE id IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') 
											ORDER BY centro");
										while($cn = $db->sql_fetchrow($qidCn)){
											$selected = "";
											if($centro == $cn["id"]) $selected = " selected";
											echo '<option value="'.$cn["id"].'" '.$selected.'>'.$cn["centro"].'</option>';
									}
									?>
									</select>
								</td>
								<td align='center' width='33%'>
								<div id="id_ase_dos">Vehículo &nbsp;&nbsp;
								<select  name='id_vehiculo' id='id_vehiculo'  style="width:150px">
									<option value=''>Todos</option>
									<?
										$qidVehi = $db->sql_query("SELECT id,codigo||'/'||placa as codigo 
											FROM vehiculos 
											WHERE id_centro = '".$centro."'
											ORDER BY codigo, placa");
										while($vehi = $db->sql_fetchrow($qidVehi)){
											$selected = "";
											if(nvl($id_vehiculo) == $vehi["id"]) $selected = " selected";
											echo '<option value="'.$vehi["id"].'" '.$selected.'>'.$vehi["codigo"].'</option>';
										}
									?>
									</select>
									</div>
								</td>
								<td align='center' valign="center" width='33%'>Vista
									<select  name='vista'>
										<option value="general" <?if(isset($vista) && $vista=="general") echo "selected"?>>General</option>
										<option value="dias" <?if(isset($vista) && $vista=="dias") echo "selected"?>>Días</option>
									</selected>
								</td>
							</tr>
						</table>
					<?}


					if(simple_me($ME)=="7.php" || simple_me($ME)=="40.php" || simple_me($ME)=="54.php"){?>
						<table width="100%" border=1 bordercolor="#7fa840" align="right">
							<tr>
								<td align='center' width='33%'>Centro &nbsp;&nbsp;
								<select  name='id_centro' onChange="updateRecursive_id_vehiculoDos(this), updateRutasXCentro(this)">
									<?
										$qidCn = $db->sql_query("SELECT id, centro 
											FROM centros WHERE id IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') 
											ORDER BY centro");
										while($cn = $db->sql_fetchrow($qidCn)){
											$selected = "";
											if($centro == $cn["id"]) $selected = " selected";
											echo '<option value="'.$cn["id"].'" '.$selected.'>'.$cn["centro"].'</option>';
									}
									?>
									</select>
								</td>
								<td align='center' width='33%'>
								<div id="id_ase_dos">Vehículo &nbsp;&nbsp;
								<select  name='id_vehiculo' id='id_vehiculo'  style="width:150px" onChange="updateRutasXVehiculo(this)">
									<option value=''>Todos</option>
									<?
										$qidVehi = $db->sql_query("SELECT distinct(v.id), v.codigo, v.placa
											FROM rec.pesos p
											LEFT JOIN vehiculos v ON v.id = p.id_vehiculo
											WHERE v.id_centro =".$centro." AND  fecha_entrada::date>='".$inicio."' AND fecha_entrada::date<= '".$final."'
											ORDER BY codigo,placa, id");
										while($vehi = $db->sql_fetchrow($qidVehi)){
											$selected = "";
											if(nvl($id_vehiculo) == $vehi["id"]) $selected = " selected";
											echo '<option value="'.$vehi["id"].'" '.$selected.'>'.$vehi["codigo"]." / ".$vehi["placa"].'</option>';
										}
									?>
									</select>
									</div>
								</td>
								<td align='center' width='33%'>
								<div id="id_micro">Ruta &nbsp;&nbsp;
								<select  name='id_micro' id='id_micro'  style="width:150px">
									<option value=''>Todas</option>
									<?
										$cond = "";
										if(nvl($id_vehiculo) != "")
											$cond =  " AND p.id_vehiculo = '".$id_vehiculo."'";
										$consulta =  "SELECT distinct(i.id),i.codigo 
											FROM rec.movimientos_pesos mp 
											LEFT JOIN rec.pesos p ON p.id = mp.id_peso
											LEFT JOIN rec.movimientos m ON m.id = mp.id_movimiento
											LEFT JOIN micros i ON i.id = m.id_micro	
											LEFT JOIN ases a ON a.id=i.id_ase
											WHERE a.id_centro = '".$centro."' AND p.fecha_entrada::date>='".$inicio."' AND p.fecha_entrada::date<= '".$final."' ".$cond."
											ORDER BY codigo";
										//echo $consulta;
										$qidRutas = $db->sql_query($consulta);
										while($queryRuta = $db->sql_fetchrow($qidRutas)){
											$selected = "";
											if(nvl($id_micro) == $queryRuta["id"]) $selected = " selected";
											echo '<option value="'.$queryRuta["id"].'" '.$selected.'>'.$queryRuta["codigo"].'</option>';
										}
									?>
									</select>
									</div>
								</td>

								
							</tr>
						</table>
					<?}



				


						//coordinadores
						if(simple_me($ME)=="9.php"){?>
						<div id="fillformgestra" style="<?if(simple_me($ME)=="9.php") echo ""; else echo "display:none;visibility:hidden"?>">
							<table width="100%" border=1 bordercolor="#7fa840" align="right">
								<tr>
									<td align='right' valign="center">Coordinadores</td>
									<td align='left'> 
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
										<input type="checkbox" name="id_coordinador[]" value="<?=$coo["id"]?>" <?=$checked?>>&nbsp;&nbsp;<?=$coo["nombre"]?><br>
									<?}?>
									</td>
								</tr>
							</table>
						</div>
						<?}



						//centro y turno y vistas (Por Tipo Vehículo, Por Vehículo)
						if(simple_me($ME)=="37.php"){?>
						<div id="vehiculos_21" style="<?if(simple_me($ME)=="21.php" || simple_me($ME)=="37.php") echo ""; else echo "display:none;visibility:hidden"?>">
							<table width="100%" border=1 bordercolor="#7fa840" align="right">
								<tr>
									<td align='center' width='20%'>Centro &nbsp;&nbsp;
										<select  name='id_centro' onChange="updateRecursive_id_turno(this), updateRecursive_id_ase_dos(this)">
										<?
										$qidCn = $db->sql_query("SELECT id, centro 
										FROM centros WHERE id IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') 
										ORDER BY centro");
										while($cn = $db->sql_fetchrow($qidCn)){
										$selected = "";
										if($centro == $cn["id"]) $selected = " selected";
										echo '<option value="'.$cn["id"].'" '.$selected.'>'.$cn["centro"].'</option>';
										}
										?>
										</select>
									</td>
									<td align='center' width='25%'>
										<div id="id_ase_dos">Ase &nbsp;&nbsp;
										<select  name='id_ase' id='id_ase_dos'  style="width:150px">
											<option value=''>Todas</option>
												<?
												$qidAs = $db->sql_query("SELECT a.id, a.ase FROM ases a WHERE a.id_centro=".$centro);
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
									<td align='center' width='25%'>
									<div id="id_turno">Turno &nbsp;&nbsp;
									<select  name='id_turno' id='id_turno'  style="width:100px">
										<option value="">Todos</option>
										<?
											$qidTur = $db->sql_query("SELECT t.id, t.turno FROM turnos t LEFT JOIN centros c ON c.id_empresa = t.id_empresa WHERE c.id=".$centro);
											while($tur = $db->sql_fetchrow($qidTur))
											{
												$selected = "";
												if(isset($id_turno) && $id_turno == $tur["id"]) $selected = " selected";
													echo '<option value="'.$tur["id"].'" '.$selected.'>'.$tur["turno"].'</option>';
											}
										?>
									</select>
									</div>
								</td>
									<td align='center' valign="center" width='25%'>Vista
										<select  name='vista'>
												<option value="tipo" <?if(isset($vista) && $vista=="tipo") echo "selected"?>>Por Tipo Vehículo</option>
												<option value="vehiculo" <?if(isset($vista) && $vista=="vehiculo") echo "selected"?>>Por Vehículo</option>
										</selected>
									</td>
								</tr>
							</table>
						</div>	
						<?}

						//centro y ase (sin opción de todos)
						if(simple_me($ME)=="39.php"){?>
						<table width="100%" border=1 bordercolor="#7fa840" align="right">
							<tr>
								<td align='center' width='50%'>Centro &nbsp;&nbsp;
									<select  name='id_centro' onChange="updateRecursive_id_ase(this)">
									<?
									$qidCn = $db->sql_query("SELECT id, centro 
									FROM centros WHERE id IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') 
									ORDER BY centro");
									while($cn = $db->sql_fetchrow($qidCn)){
									$selected = "";
									if($centro == $cn["id"]) $selected = " selected";
									echo '<option value="'.$cn["id"].'" '.$selected.'>'.$cn["centro"].'</option>';
									}
									?>
									</select>
								</td>
								<td align='center' width='50%'>
								<div id="id_ase">Ase &nbsp;&nbsp;
								<select  name='id_ase' id='id_ase'  style="width:150px">
										<?
										$qidAs = $db->sql_query("SELECT a.id, a.ase FROM ases a WHERE a.id_centro=".$centro);
										while($as = $db->sql_fetchrow($qidAs))
										{
											echo '<option value="'.$as["id"].'">'.$as["ase"].'</option>';
										}
									?>
								</select>
								</div>
								</td>
							</tr>
						</table>
						<?}

						//centro y ase (sin opción de todos)
						if(simple_me($ME)=="49.php"){?>
						<table width="100%" border=1 bordercolor="#7fa840" align="right">
							<tr>
								<td align='center' width='50%'>Centro &nbsp;&nbsp;
									<select  name='id_centro' onChange="updateRecursive_id_personal(this)">
									<?
									$qidCn = $db->sql_query("SELECT id, centro 
									FROM centros WHERE id IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') 
									ORDER BY centro");
									while($cn = $db->sql_fetchrow($qidCn)){
									$selected = "";
									if($centro == $cn["id"]) $selected = " selected";
									echo '<option value="'.$cn["id"].'" '.$selected.'>'.$cn["centro"].'</option>';
									}
									?>
									</select>
								</td>
								<td align='center' width='50%'>
								<div id="id_ase">Operario &nbsp;&nbsp;
								<select  name='id_persona' id='id_persona'  style="width:150px">
									<option value="">Todos</option>
										<?
										$qidPs = $db->sql_query("
											SELECT distinct(id), nombre
											FROM(
												SELECT p.id, p.nombre||' '||p.apellido||' ('||p.cedula||')' as nombre 
												FROM rec.movimientos_personas mp 
												LEFT JOIN personas p ON p.id=mp.id_persona
												WHERE mp.hora_inicio::date >= '".$inicio."' AND mp.hora_inicio<='".$final."' AND mp.id_persona IN (SELECT id_persona FROM personas_centros WHERE id_centro = '".$centro."')
												UNION
												SELECT p.id, p.nombre||' '||p.apellido||' ('||p.cedula||')' as nombre 
												FROM bar.movimientos_personas mp 
												LEFT JOIN personas p ON p.id=mp.id_persona
												WHERE mp.hora_inicio::date >= '".$inicio."' AND mp.hora_inicio<='".$final."' AND mp.id_persona IN (SELECT id_persona FROM personas_centros WHERE id_centro = '".$centro."')
											) AS foo
											ORDER BY nombre");
										while($as = $db->sql_fetchrow($qidPs))
										{
											echo '<option value="'.$as["id"].'">'.$as["nombre"].'</option>';
										}
									?>
								</select>
								</div>
								</td>
							</tr>
						</table>
						<?}?>




						
						
					</td>
					<td align="center" valign="center" > <input type="submit" class="boton_verde" value="Aceptar"/> </td>

				</tr>
				<tr>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>

<script type="text/javascript">

function revisar()
{
	<?if(simple_me($ME)!="20.php"){?>
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

	if(document.entryform.final.value.replace(/ /g, '') ==''){
		window.alert('Por favor seleccione: Fecha Final');
		document.entryform.final.focus();
		return(false);
	}
	else{
		var regexpression=/^[0-9]{4}-[0-9]{2}-[0-9]{2}/;
		if(!regexpression.test(document.entryform.final.value)){
			window.alert('[Fecha Final] no contiene un dato válido.');
			document.entryform.final.focus();
			return(false);
		}
	}

	if(document.entryform.inicio.value > document.entryform.final.value){
		window.alert('La fecha de inicio no puede ser mayor que la fecha final');
		document.entryform.inicio.focus();
		return(false);
	}
	
	<?}?>
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

var oXmlHttp_turno;
function updateRecursive_id_turno(select){
	namediv='id_turno';
	nameId='id_turno';
	id=select.options[select.selectedIndex].value;
	document.getElementById(namediv).innerHTML='Turno <select id="' + nameId + '" style="width:100px"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateInformes.php?tipo=turnoxcentro&id_centro=" + id + "&divid=" + namediv;
	oXmlHttp_id_turno=GetHttpObject(cambiarRecursive_id_turno);
	oXmlHttp_id_turno.open("GET", url , true);
	oXmlHttp_id_turno.send(null);
}
function cambiarRecursive_id_turno(){
	if (oXmlHttp_id_turno.readyState==4 || oXmlHttp_id_turno.readyState=="complete"){
		document.getElementById('id_turno').innerHTML=oXmlHttp_id_turno.responseText
	}
}


var oXmlHttp_id_ase;
function updateRecursive_id_ase(select){
	namediv='id_ase';
	nameId='id_ase';
	id=select.options[select.selectedIndex].value;
	document.getElementById(namediv).innerHTML='Ase <select id="' + nameId + '" style="width:150px"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateInformes.php?tipo=asexcentro&id_centro=" + id + "&divid=" + namediv;
	oXmlHttp_id_ase=GetHttpObject(cambiarRecursive_id_ase);
	oXmlHttp_id_ase.open("GET", url , true);
	oXmlHttp_id_ase.send(null);
}
function cambiarRecursive_id_ase(){
	if (oXmlHttp_id_ase.readyState==4 || oXmlHttp_id_ase.readyState=="complete"){
		document.getElementById('id_ase').innerHTML=oXmlHttp_id_ase.responseText
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


var oXmlHttp_id_vehiculo;
function updateRecursive_id_vehiculo(select){
	namediv='id_vehiculo';
	nameId='id_vehiculo';
	id=select.options[select.selectedIndex].value;
	document.getElementById(namediv).innerHTML='Vehículo <select id="' + nameId + '" style="width:150px"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateInformes.php?tipo=vehiculoxcentro&id_centro=" + id + "&divid=" + namediv;
	oXmlHttp_id_vehiculo=GetHttpObject(cambiarRecursive_id_vehiculo);
	oXmlHttp_id_vehiculo.open("GET", url , true);
	oXmlHttp_id_vehiculo.send(null);
}

function updateRecursive_id_vehiculoDos(select){
	namediv='id_vehiculo';
	nameId='id_vehiculo';
	id=select.options[select.selectedIndex].value;
	document.getElementById(namediv).innerHTML='Vehículo <select id="' + nameId + '" style="width:150px" onChange="updateRutasXVehiculo()"><option>Actualizando...<\/select>';
	f_inicio = document.entryform.inicio.value;
	f_final = document.entryform.final.value;
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateInformes.php?tipo=vehiculoxcentro_pesos&id_centro=" + id + "&divid=" + namediv + "&f_inicio=" + f_inicio + "&f_final=" + f_final;
	oXmlHttp_id_vehiculo=GetHttpObject(cambiarRecursive_id_vehiculo);
	oXmlHttp_id_vehiculo.open("GET", url , true);
	oXmlHttp_id_vehiculo.send(null);
}

function cambiarRecursive_id_vehiculo(){
	if (oXmlHttp_id_vehiculo.readyState==4 || oXmlHttp_id_vehiculo.readyState=="complete"){
		document.getElementById('id_vehiculo').innerHTML=oXmlHttp_id_vehiculo.responseText
	}
}

var oXmlHttp_id_micro;
function updateRutasXCentro(select){
	namediv='id_micro';
	nameId='id_micro';
	id=select.options[select.selectedIndex].value;
	f_inicio = document.entryform.inicio.value;
	f_final = document.entryform.final.value;
	document.getElementById(namediv).innerHTML='Ruta <select id="' + nameId + '" style="width:150px"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateInformes.php?tipo=rutaxcentro_pesos&id_centro=" + id + "&divid=" + namediv + "&f_inicio=" + f_inicio + "&f_final=" + f_final;
	oXmlHttp_id_micro=GetHttpObject(cambiarRecursive_id_micro);
	oXmlHttp_id_micro.open("GET", url , true);
	oXmlHttp_id_micro.send(null);
}
function updateRutasXVehiculo(select){
	namediv='id_micro';
	nameId='id_micro';
	id=select.options[select.selectedIndex].value;
	f_inicio = document.entryform.inicio.value;
	f_final = document.entryform.final.value;
	document.getElementById(namediv).innerHTML='Ruta <select id="' + nameId + '" style="width:150px"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateInformes.php?tipo=rutaxvehiculo_pesos&id_vehiculo=" + id + "&divid=" + namediv + "&f_inicio=" + f_inicio + "&f_final=" + f_final;
	oXmlHttp_id_micro=GetHttpObject(cambiarRecursive_id_micro);
	oXmlHttp_id_micro.open("GET", url , true);
	oXmlHttp_id_micro.send(null);
}
function cambiarRecursive_id_micro(){
	if (oXmlHttp_id_micro.readyState==4 || oXmlHttp_id_micro.readyState=="complete"){
		document.getElementById('id_micro').innerHTML=oXmlHttp_id_micro.responseText
	}
}


var oXmlHttp_id_persona;
function updateRecursive_id_personal(select){
	namediv='id_persona';
	nameId='id_persona';
	id=select.options[select.selectedIndex].value;
	f_inicio = document.entryform.inicio.value;
	f_final = document.entryform.final.value;
	document.getElementById(namediv).innerHTML='Operario <select id="' + nameId + '" style="width:150px"><option>Actualizando...<\/select>';
	var url="<?=$CFG->wwwroot?>/lib/ajaxUpdateInformes.php?tipo=personasxmovimiento&id_centro=" + id + "&divid=" + namediv + "&f_inicio=" + f_inicio + "&f_final=" + f_final;
	oXmlHttp_id_persona=GetHttpObject(cambiarRecursive_id_persona);
	oXmlHttp_id_persona.open("GET", url , true);
	oXmlHttp_id_persona.send(null);
}
function cambiarRecursive_id_persona(){
	if (oXmlHttp_id_persona.readyState==4 || oXmlHttp_id_persona.readyState=="complete"){
		document.getElementById('id_persona').innerHTML=oXmlHttp_id_persona.responseText
	}
}


</script>
