<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
	<tr>
		<td height="40" colspan=3 align="center"><span class="azul_16"><strong>EQUIPO: <?=$equipo["nombre"]?></strong></span></td>
	</tr>
	<?if(!$vistaDesdeCalendario){?>
	<tr>
		<td align="center">
			<table width="98%" border=1 bordercolor="#7fa840" id="tabla_actividades">
				<?if($equipo["imagen"] != ""){?>
				<tr>
					<td colspan=2 align="center"><img border="0" src="<?=srcImagen($equipo["id"],"mtto.equipos",$equipo["imagen"],"imagen",250)?>"/></td>
				</tr>
				<?}?>
				<tr>
					<td width="20%">Grupo</td>
					<td><?=$equipo["grupo"]?></td>
				</tr>
				<tr>
					<td>Código</td>
					<td><?=$equipo["codigo"]?></td>
				</tr>
				<tr>
					<td>Serial</td>
					<td><?=$equipo["serial"]?></td>
				</tr>
				<tr>
					<td>Centro</td>
					<td><?=$equipo["centro"]?></td>
				</tr>
				<tr>
					<td>Kilometraje</td>
					<td><?=$equipo["kilometraje"]?></td>
				</tr>
				<tr>
					<td>Horómetro</td>
					<td><?=$equipo["horometro"]?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<?if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["agregarEquipo"])){?>
		<td align="right"><a href="javascript:abrirVentanaJavaScript('equiposform','900','500','<?=$CFG->wwwroot?>/mtto/equipos.php?mode=editar&id=<?=$equipo["id"]?>')" class='link_verde'>+ Editar Equipo +</a>&nbsp;&nbsp;&nbsp;</td>
		<?}?>
	</tr>
	
	<tr>
		<td align="center"><span class="azul_12">ORDENES DE TRABAJO</span></td>
	</tr>
	<tr>
		<td align="center">
			<form name="entryformOT" action="<?=$ME?>" method="GET">
			<input type="hidden" name="id_equipo" value="<?=$idEquipo?>">
			<input type="hidden" name="botonCerrar" value="<?=$botonCerrar?>">
			<input type="hidden" name="mode" value="hoja_vida">
			<table width="98%" height="40">
				<tr>
					<td>Rutina: <?=$selectRutinas?></td>
					<td>Tipo: <?=$selectTipos?></td>
					<td>Estado: <?=$selectEstado?></td>
					<td>Sistema: <?=$selectSistema?></td>
					<td><input type="Submit" style="font-size:8pt" value="Filtrar">&nbsp;&nbsp;<input type="button" onclick="javascript:bajarOTXLS()"  style="font-size:8pt" value="Bajar .xls"></td>
				</tr>
			</table>
			</form>
			<table width="98%" border=1 bordercolor="#7fa840" id="tabla_actividades">
				<tr>
					<td align="center">N. OT</td>
					<td align="center">¿A TIEMPO?</td>
					<td align="center">HORA PLANEADA</td>
					<td align="center">FECHA PLANEADA</td>
					<td align="center">RUTINA</td>
					<td align="center">TIPO</td>
					<td align="center">EJECUCIÓN<br />INICIO</td>
					<td align="center">EJECUCIÓN<br /> FIN</td>
					<td align="center">ESTADO</td>
					<td align="center">KM</td>
					<td align="center">HORO</td>
					<td align="center">VER</td>
				</tr>
			<?
			if($db->sql_numrows($qid) == 0)
				echo "</tr><td colspan=11>No hay resultados</td></tr>";
			while($query = $db->sql_fetchrow($qid))
			{
				echo "<tr>
					<td align=\"center\">".$query["id"]."</td>
					<td align=\"center\">".$query["atiempo"]."</td>
					<td align=\"center\">".$query["hora_planeada"]."</td>
					<td align=\"center\">".$query["fecha_planeada"]."</td>
					<td>".$query["rutina"]."</td>
					<td align=\"center\">".$query["tipo"]."</td>
					<td align=\"center\">".$query["fecha_ejecucion_inicio"]."</td>
					<td align=\"center\">".$query["fecha_ejecucion_fin"]."</td>
					<td align=\"center\">".$query["estado"]."</td>
					<td align=\"center\">".$query["km"]."</td>
					<td align=\"center\">".$query["horometro"]."</td>
					<td align=\"center\">";
					if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["ordenes_opciones"]))
						echo "<a href='javascript:abrirVentanaJavaScript(\"ordeneseditar\",\"900\",\"500\",\"".$CFG->wwwroot."/mtto/ordenes.php?mode=editar&id=".$query["id"]."\")'>".$query["editar"]."</a></td>";
				echo "
				</tr>
					";
			}
			?>
			</table>
		</td>
	</tr>
	<?}?>
	<script type="text/javascript">
		function bajarOTXLS()
		{
			document.entryformOT.mode.value='bajarOTXLS';
			document.entryformOT.submit();
		}

		function bajarNAXLS()
		{
			document.entryformNA.mode.value='bajarNAXLS';
			document.entryformNA.submit();
		}

		function bajarNCXLS()
		{
			document.entryformNC.mode.value='bajarNCXLS';
			document.entryformNC.submit();
		}


	</script>
	<?
	
	if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["mttoNovedades"])){
	if(!$vistaDesdeCalendario){?>
	<tr><td height=40>&nbsp;</td></tr>
	<?}?>
	<tr>
		<td align="center"><span class="azul_12">NOVEDADES MTTO ABIERTAS</span></td>
	</tr>
	<tr>
		<td align="center">
			<form name="entryformNA" action="<?=$ME?>" method="GET">
			<input type="hidden" name="id_equipo" value="<?=$idEquipo?>">
			<input type="hidden" name="botonCerrar" value="<?=$botonCerrar?>">
			<input type="hidden" name="mode" value="hoja_vida">
			<?if($vistaDesdeCalendario){?>
			<input type="hidden" name="calendario" value="true">
			<?}?>
			<table width="40%" height="40">
				<tr>
					<td align="right">Observaciones: <input type='text' size='20'  name='nov_ab' value='<?=$nov_ab?>'  ></td>
					<td><input type="Submit" style="font-size:8pt" value="Buscar">&nbsp;&nbsp;<input type="button" onclick="javascript:bajarNAXLS()"  style="font-size:8pt" value="Bajar .xls"></td>
				</tr>
			</table>
			</form>

			<table width="98%" border=1 bordercolor="#7fa840" id="tabla_actividades">
				<tr>
					<td align="center">FECHA</td>
					<td align="center">OBSERVACIONES</td>
					<td align="center">ÓRDENES</td>
					<td align="center">OPCIONES</td>
				</tr>
				<?if($db->sql_numrows($qidNovAbiertas) == 0)
						echo "</tr><td colspan=4>No hay resultados</td></tr>";
				
				while($nov = $db->sql_fetchrow($qidNovAbiertas))
				{
					echo "<tr>
						<td>".$nov["hora_inicio"]."</td>
						<td>".$nov["observaciones"]."</td>
						<td>".$nov["ots"]."</td>
						<td align ='center'>";
						if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["opciones_novedades"]))
							echo "<a href='javascript:abrirVentanaJavaScript(\"novedades\",\"800\",\"500\",\"".$CFG->wwwroot."/novedades/novedades.php?mode=editar&id=".$nov["id"]."\")'><img alt='Editar' src='".$CFG->wwwroot."/admin/iconos/transparente/iconoeditar.gif' border='0'></a>&nbsp;&nbsp;&nbsp;<a href='javascript:abrirVentanaJavaScript(\"novedades\",\"1\",\"1\",\"".$CFG->wwwroot."/novedades/novedades.php?mode=cerrar&id=".$nov["id"]."\")'><img alt='Cerrar' src='".$CFG->wwwroot."/admin/iconos/transparente/check_green.png' border='0'></a>";
					echo "</td>
					</tr>";
				}
				?>
				</table>
		</td>
	</tr>
	
	<?if(!$vistaDesdeCalendario){?>
	<tr><td height=40>&nbsp;</td></tr>
	<tr>
		<td align="center"><span class="azul_12">NOVEDADES MTTO CERRADAS</span></td>
	</tr>
	<tr>
		<td align="center">
			<form name="entryformNC" action="<?=$ME?>" method="GET">
			<input type="hidden" name="id_equipo" value="<?=$idEquipo?>">
			<input type="hidden" name="botonCerrar" value="<?=$botonCerrar?>">
			<input type="hidden" name="mode" value="hoja_vida">
			<table width="40%" height="40">
				<tr>
					<td align="right">Observaciones: <input type='text' size='20'  name='nov_cer' value='<?=$nov_cer?>'  ></td>
					<td><input type="Submit" style="font-size:8pt" value="Buscar">&nbsp;&nbsp;<input type="button" onclick="javascript:bajarNCXLS()"  style="font-size:8pt" value="Bajar .xls"></td>
				</tr>
			</table>
			</form>
			<table width="98%" border=1 bordercolor="#7fa840" id="tabla_actividades">
				<tr>
					<td align="center">FECHA INICIO</td>
					<td align="center">FECHA FIN</td>
					<td align="center">OBSERVACIONES</td>
					<td align="center">ÓRDENES</td>
					<td align="center">OPCIONES</td>
				</tr>
				<?if($db->sql_numrows($qidNovCerradas) == 0)
					echo "</tr><td colspan=4>No hay resultados</td></tr>";
				
				while($nov = $db->sql_fetchrow($qidNovCerradas))
				{
					echo "<tr>
						<td>".$nov["hora_inicio"]."</td>
						<td>".$nov["hora_fin"]."</td>
						<td>".$nov["observaciones"]."</td>
						<td>".$nov["ots"]."</td>
						<td align ='center'>";
						if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["opciones_novedades"]))
							echo "<a href='javascript:abrirVentanaJavaScript(\"novedades\",\"800\",\"500\",\"".$CFG->wwwroot."/novedades/novedades.php?mode=editar&id=".$nov["id"]."\")'><img alt='Editar' src='".$CFG->wwwroot."/admin/iconos/transparente/iconoeditar.gif' border='0'></a>";
					echo "</td>
					</tr>";
				}
				?>
				</table>
		</td>
	</tr>
	<?
	

	if($db->sql_numrows($qidAA) > 0){?>
	<tr><td height=40>&nbsp;</td></tr>
	<tr>
		<td align="center"><span class="azul_12">ARCHIVOS ADJUNTOS</span></td>
	</tr>
	<tr>
		<td align="center">
			<table width="98%" border=1 bordercolor="#7fa840" id="tabla_actividades">
				<tr>
					<td align="center">FECHA</td>
					<td align="center">NOMBRE</td>
					<td align="center">ARCHIVO</td>
					<td align="center">OPCIONES</td>
				</tr>
				<?
				while($query = $db->sql_fetchrow($qidAA))
				{
					echo "<tr>
						<td align=\"center\">".$query["fecha"]."</td>
						<td align=\"center\">".$query["nombre"]."</td>
						<td align=\"center\">".$query["link"]."</td>
						<td align=\"center\"><a href='javascript:abrirVentanaJavaScript(\"adjuntos\",\"500\",\"400\",\"".$CFG->wwwroot."/mtto/equipos.php?mode=editar_archivo&id=".$query["id"]."\")'><img alt='Editar' src='".$CFG->wwwroot."/admin/iconos/transparente/iconoeditar.gif' border='0' title='Editar'></a>&nbsp;&nbsp;<a href='".$CFG->wwwroot."/mtto/equipos.php?mode=borrar_archivo&id=".$query["id"]."&id_equipo=".$query["id_equipo"]."'><img alt='Borrar' src='".$CFG->wwwroot."/admin/iconos/transparente/icon-erase.gif' border='0' title='Borrar'></a></td>
					</tr>";
				}
				?>
			</table>
		</td>
	</tr>
	<?}?>
	<tr><td height=40>&nbsp;</td></tr>
	<tr>
		<?if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["agregarArchivoEquipo"])){?>
		<td align="right"><a href="javascript:abrirVentanaJavaScript('adjuntos','500','400','<?=$CFG->wwwroot?>/mtto/equipos.php?mode=agregar_archivo&id_equipo=<?=$equipo["id"]?>')" class='link_verde'>+ Agregar Archivo al Equipo +</a>&nbsp;&nbsp;&nbsp;</td>
		<?}?>
	</tr>
	<?}}?>
</table>
<?if($botonCerrar){?>
<table width="100%" cellpadding="5" cellspacing="3">
	<tr>
		<td align="center" height="40" valign="bottom">
			<input type="button" class="boton_verde" value="Cerrar" onclick="window.close()"/>
		</td>
	</tr>
</table>
<?}?>
