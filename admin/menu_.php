<?
if(!isset($_SESSION[$CFG->sesion]))
{
	echo "<script> window.location.href='".$CFG->wwwroot."/admin/login.php'; </script> ";
	die;
}
$nivel=$_SESSION[$CFG->sesion]["user"]["nivel"];
?>
<table width="100%" border="0" cellspacing="0" cellpadding="1">

<tr><td colspan=2><hr></td></tr>
	<?if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"] == 1 || $_SESSION[$CFG->sesion]["user"]["nivel_acceso"]==13 || $_SESSION[$CFG->sesion]["user"]["nivel_acceso"]==6){?>
	<tr><td colspan=2><b><span onClick="switchDisplay('div_configuracion')" style="cursor:pointer">CONFIGURACIÓN</span></b></td></tr>
	<tr>
		<td colspan="2">
			<div id="div_configuracion"<?if(!in_array(nvl($_GET["module"],$CFG->defaultModule),array("elementos_dotaciones","errores","estados_vehiculos","eventos","categorias", "categorias_puntos_interes", "centros", "clientes", "empresas", "departamentos", "dotaciones", "marcas_vehiculos", "servicios", "tipos_vehiculos", "tipos_vehiculos_sui", "unidades", "vehiculos"))) echo " style=\"display:none\""?>>
			<table width="100%" border="0" cellspacing="0" cellpadding="1">
				<?if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"] == 1){?>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="departamentos") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=departamentos" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="departamentos") echo "active"; ?>">Departamentos</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="empresas") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=empresas" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="empresas") echo "active"; ?>">Empresas</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="centros") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=centros" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="centros") echo "active"; ?>">Centros</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="servicios") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=servicios" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="servicios") echo "active"; ?>">Servicios</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="marcas_vehiculos") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=marcas_vehiculos" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="marcas_vehiculos") echo "active"; ?>">Marcas Vehículos</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="tipos_vehiculos") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=tipos_vehiculos" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="tipos_vehiculos") echo "active"; ?>">Tipos Vehículos</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="tipos_vehiculos_sui") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=tipos_vehiculos_sui" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="tipos_vehiculos_sui") echo "active"; ?>">Tipos Vehículos SUI</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="estados_vehiculos") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=estados_vehiculos" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="estados_vehiculos") echo "active"; ?>">Estados Vehículos</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="actividad_vehiculo_sui") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=actividad_vehiculo_sui" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="actividad_vehiculo_sui") echo "active"; ?>">Actividad Vehículos SUI</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="categorias_puntos_interes") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=categorias_puntos_interes" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="categorias_puntos_interes") echo "active"; ?>">Puntos de interés</a>
					</td>
				</tr>


				<?}
				if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"] == 1 || $_SESSION[$CFG->sesion]["user"]["nivel_acceso"]==13 || $_SESSION[$CFG->sesion]["user"]["nivel_acceso"]==6){?>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="vehiculos") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=vehiculos" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="vehiculos") echo "active"; ?>">Vehículos</a>
					</td>
				</tr>
				<?}?>
				<?if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"] == 1){?>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="clientes") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=clientes" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="clientes") echo "active"; ?>">Clientes</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="unidades") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=unidades" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="unidades") echo "active"; ?>">Unidades</a>
					</td>
				</tr>
				
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="errores") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=errores" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="errores") echo "active"; ?>">Errores</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="eventos") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=eventos" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="eventos") echo "active"; ?>">Eventos</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="elementos_dotaciones") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=elementos_dotaciones" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="elementos_dotaciones") echo "active"; ?>">Elementos Dotaciones</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="dotaciones") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=dotaciones" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="dotaciones") echo "active"; ?>">Dotaciones</a>
					</td>
				</tr>
				<?}?>
			</table>
			</div>
		</td>
	</tr>
	<?}
		if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"] == 1 || $_SESSION[$CFG->sesion]["user"]["nivel_acceso"]==13 || $_SESSION[$CFG->sesion]["user"]["nivel_acceso"]= 6){

		if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"] == 1 || $_SESSION[$CFG->sesion]["user"]["nivel_acceso"]==13){
		?>
	<tr><td colspan=2><hr></td></tr>
	<tr><td colspan=2><b><span onClick="switchDisplay('div_operaciones')" style="cursor:pointer">OPERACIÓN</span></b></td></tr>
	<?}?>
	<tr>
		<td colspan="2">
			<div id="div_operaciones"<?if(!in_array(nvl($_GET["module"],$CFG->defaultModule),array("ases","bar.tipos_bolsas","bar.movimientos","clasificacion_peajes", "comunas","cuartelillos","gps_vehi","lugares_descargue","micros","novedades","peajes",  "rec.apoyos", "desplazamientos_trailer", "rec.movimientos","rec.pesos", "costos", "rec.tipos_desplazamientos", "tipos_barridos_sui", "tipos_novedades", "tipo_micros_sui", "tipos_residuos", "tipos_residuos_sui", "tipos_segmentos","turnos","alertas","tipos_alertas"))) echo " style=\"display:none\""?>>
			<table width="100%" border="0" cellspacing="0" cellpadding="1">
				<?if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"] == 1 || $_SESSION[$CFG->sesion]["user"]["nivel_acceso"]==13){?>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="gps_vehi") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=gps_vehi" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="gps_vehi") echo "active"; ?>">Detalle GPS</a>
					</td>
				</tr>
				<?}?>
				<?if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"] == 1){?>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="comunas") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=comunas" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="comunas") echo "active"; ?>">Comunas</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="peajes") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=peajes" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="peajes") echo "active"; ?>">Peajes</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="clasificacion_peajes") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=clasificacion_peajes" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="clasificacion_peajes") echo "active"; ?>">Clasificación Peajes</a>
					</td>
				</tr>
				<?} 
				if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"] == 1 || $_SESSION[$CFG->sesion]["user"]["nivel_acceso"] == 13){?>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="ases") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=ases" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="ases") echo "active"; ?>">Ases</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="turnos") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=turnos" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="turnos") echo "active"; ?>">Turnos</a>
					</td>
				</tr>
				<?}
				if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"] == 1){?>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="tipos_segmentos") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=tipos_segmentos" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="tipos_segmentos") echo "active"; ?>">Tipos Segmentos</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="cuartelillos") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=cuartelillos" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="cuartelillos") echo "active"; ?>">Cuartelillos</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="lugares_descargue") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=lugares_descargue" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="lugares_descargue") echo "active"; ?>">Lugares Descargue</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="bar.tipos_bolsas") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=bar.tipos_bolsas" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="bar.tipos_bolsas") echo "active"; ?>">Tipos Bolsas</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="rec.tipos_desplazamientos") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=rec.tipos_desplazamientos" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="rec.tipos_desplazamientos") echo "active"; ?>">Tipos Desplazamientos (Recolección)</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="tipos_novedades") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=tipos_novedades" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="tipos_novedades") echo "active"; ?>">Tipos Novedades</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="tipos_residuos") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=tipos_residuos" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="tipos_residuos") echo "active"; ?>">Tipos Residuos</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="tipos_residuos_sui") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=tipos_residuos_sui" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="tipos_residuos_sui") echo "active"; ?>">Tipos Residuos (SUI)</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="micros") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=micros" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="micros") echo "active"; ?>">Micros</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="tipo_micros_sui") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=tipo_micros_sui" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="tipo_micros_sui") echo "active"; ?>">Tipos Micros SUI</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="tipos_barridos_sui") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=tipos_barridos_sui" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="tipos_barridos_sui") echo "active"; ?>">Tipos Barrido SUI</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="bar.movimientos") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=bar.movimientos" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="bar.movimientos") echo "active"; ?>">Movimientos Barrido</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="rec.desplazamientos_trailer") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=rec.desplazamientos_trailer" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="rec.desplazamientos_trailer") echo "active"; ?>">Desplazamientos Trailer</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="rec.movimientos") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=rec.movimientos" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="rec.movimientos") echo "active"; ?>">Movimientos Recolección</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="novedades") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=novedades" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="novedades") echo "active"; ?>">Novedades</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="rec.apoyos") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=rec.apoyos" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="rec.apoyos") echo "active"; ?>">Apoyos</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="rec.pesos") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=rec.pesos" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="rec.pesos") echo "active"; ?>">Pesos</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="costos") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=costos" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="costos") echo "active"; ?>">Costos</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="tipos_alertas") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=tipos_alertas" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="tipos_alertas") echo "active"; ?>">Tipos de alerta</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="alertas") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=alertas" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="alertas") echo "active"; ?>">Alertas</a>
					</td>
				</tr>
				<?}?>
			
	



		
			</table>
			</div>
		</td>
	</tr>
	
	<tr><td colspan=2><hr></td></tr>
	<?if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"] == 1){?>
	<tr><td colspan=2><b><span onClick="switchDisplay('div_informes')" style="cursor:pointer">INFORMES</span></b></td></tr>
	<tr>
		<td colspan="2">
			<div id="div_informes"<?if(!in_array(nvl($_GET["module"],$CFG->defaultModule),array("informes","categorias_informes"))) echo " style=\"display:none\""?>>
			<table width="100%" border="0" cellspacing="0" cellpadding="1">
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="categorias_informes") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=categorias_informes" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="categorias_informes") echo "active"; ?>">Categorías</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="informes") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=informes" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="informes") echo "active"; ?>">Informes</a>
					</td>
				</tr>

			</table>
			</div>
		</td>
	</tr>
	


	<tr><td colspan=2><hr></td></tr>
	<?}?>
	<tr><td colspan=2><b><span onClick="switchDisplay('div_admin')" style="cursor:pointer">PERSONAS</span></b></td></tr>
	<tr>
		<td colspan="2">
			<div id="div_admin"<?if(!in_array(nvl($_GET["module"],$CFG->defaultModule),array("personas","cargos","estados_personas","tareas"))) echo " style=\"display:none\""?>>
			<table width="100%" border="0" cellspacing="0" cellpadding="1">
				<?if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"] == 1 || $_SESSION[$CFG->sesion]["user"]["nivel_acceso"] == 13){?>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="cargos") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=cargos" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="cargos") echo "active"; ?>">Cargos</a>
					</td>
				</tr>
				<?}
				if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"] == 1){?>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="estados_personas") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=estados_personas" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="estados_personas") echo "active"; ?>">Estados Personas</a>
					</td>
				</tr>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="personas") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=personas" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="personas") echo "active"; ?>">Personas</a>
					</td>
				</tr>
				<?}?>
				<?if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"] == 1){?>
				<tr>
					<td><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="tareas") echo "selected"; ?>">
						<a href="<?=$CFG->admin_dir?>/index.php?module=tareas" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="tareas") echo "active"; ?>">Tareas</a>
					</td>
				</tr>
				<?}?>

			</table>
			</div>
		</td>
	</tr>
	<tr><td colspan=2><hr></td></tr>
	<?if($_SESSION[$CFG->sesion]["user"]["nivel_acceso"] == 1){?>
	<tr><td colspan=2><b><span onClick="switchDisplay('div_mtto')" style="cursor:pointer">MANTENIMIENTO</span></b></td></tr>
	<tr>
		<td colspan="2">
			<div id="div_mtto"<?if(!in_array(nvl($_GET["module"],$CFG->defaultModule),array("mtto.grupos","mtto.elementos","mtto.equipos","mtto.estados_ordenes_trabajo","mtto.excepciones_diarias","mtto.excepciones_periodos","mtto.frecuencias","mtto.inspecciones","mtto.items","mtto.motivos","mtto.ordenes_trabajo","mtto.prioridades","mtto.rutinas","mtto.sistemas","mtto.tipos","mtto.unidades","mtto.variables_mtto","mtto.bodegas"))) echo " style=\"display:none\""?>>
			<table width="100%" border="0" cellspacing="0" cellpadding="1">
				<tr>
					<td width="17"><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.frecuencias") echo "selected"; ?>">
					<a href="<?=$CFG->admin_dir?>/index.php?module=mtto.frecuencias" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.frecuencias") echo "active"; ?>">Frecuencias</a>
					</td>
				</tr>
				<tr>
					<td width="17"><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.tipos") echo "selected"; ?>">
					<a href="<?=$CFG->admin_dir?>/index.php?module=mtto.tipos" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.tipos") echo "active"; ?>">Tipos</a>
					</td>
				</tr>
				<tr>
					<td width="17"><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.motivos") echo "selected"; ?>">
					<a href="<?=$CFG->admin_dir?>/index.php?module=mtto.motivos" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.motivos") echo "active"; ?>">Motivos (OT)</a>
					</td>
				</tr>
				<tr>
					<td width="17"><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.prioridades") echo "selected"; ?>">
					<a href="<?=$CFG->admin_dir?>/index.php?module=mtto.prioridades" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.prioridades") echo "active"; ?>">Prioridades</a>
					</td>
				</tr>
				<tr>
					<td width="17"><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.sistemas") echo "selected"; ?>">
					<a href="<?=$CFG->admin_dir?>/index.php?module=mtto.sistemas" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.sistemas") echo "active"; ?>">Sistemas</a>
					</td>
				</tr>
				<tr>
					<td width="17"><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.excepciones_diarias") echo "selected"; ?>">
					<a href="<?=$CFG->admin_dir?>/index.php?module=mtto.excepciones_diarias" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.excepciones_diarias") echo "active"; ?>">Excepciones Diarias</a>
					</td>
				</tr>
				<tr>
					<td width="17"><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.excepciones_periodos") echo "selected"; ?>">
					<a href="<?=$CFG->admin_dir?>/index.php?module=mtto.excepciones_periodos" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.excepciones_periodos") echo "active"; ?>">Excepciones Periodos</a>
					</td>
				</tr>
				<tr>
					<td width="17"><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.items") echo "selected"; ?>">
					<a href="<?=$CFG->admin_dir?>/index.php?module=mtto.items" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.items") echo "active"; ?>">Ítems (Inspecciones)</a>
					</td>
				</tr>
				<tr>
					<td width="17"><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.inspecciones") echo "selected"; ?>">
					<a href="<?=$CFG->admin_dir?>/index.php?module=mtto.inspecciones" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.inspecciones") echo "active"; ?>">Inspecciones</a>
					</td>
				</tr>
				<tr>
					<td width="17"><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.grupos") echo "selected"; ?>">
					<a href="<?=$CFG->admin_dir?>/index.php?module=mtto.grupos" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.grupos") echo "active"; ?>">Grupos</a>
					</td>
				</tr>
				<tr>
					<td width="17"><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.equipos") echo "selected"; ?>">
					<a href="<?=$CFG->admin_dir?>/index.php?module=mtto.equipos" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.equipos") echo "active"; ?>">Equipos</a>
					</td>
				</tr>
				<tr>
					<td width="17"><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.rutinas") echo "selected"; ?>">
					<a href="<?=$CFG->admin_dir?>/index.php?module=mtto.rutinas" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.rutinas") echo "active"; ?>">Rutinas</a>
					</td>
				</tr>
				<tr>
					<td width="17"><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.estados_ordenes_trabajo") echo "selected"; ?>">
					<a href="<?=$CFG->admin_dir?>/index.php?module=mtto.estados_ordenes_trabajo" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.estados_ordenes_trabajo") echo "active"; ?>">Estados</a>
					</td>
				</tr>
				<tr>
					<td width="17"><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.ordenes_trabajo") echo "selected"; ?>">
					<a href="<?=$CFG->admin_dir?>/index.php?module=mtto.ordenes_trabajo" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.ordenes_trabajo") echo "active"; ?>">Ordenes de Trabajo</a>
					</td>
				</tr>
				<tr>
					<td width="17"><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.variables_mtto") echo "selected"; ?>">
					<a href="<?=$CFG->admin_dir?>/index.php?module=mtto.variables_mtto" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.variables_mtto") echo "active"; ?>">Variables Programación Mtto</a>
					</td>
				</tr>
				<tr>
					<td width="17"><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.unidades") echo "selected"; ?>">
					<a href="<?=$CFG->admin_dir?>/index.php?module=mtto.unidades" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.unidades") echo "active"; ?>">Unidades</a>
					</td>
				</tr>
				<tr>
					<td width="17"><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.bodegas") echo "selected"; ?>">
					<a href="<?=$CFG->admin_dir?>/index.php?module=mtto.bodegas" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.bodegas") echo "active"; ?>">Bodegas</a>
					</td>
				</tr>
				<tr>
					<td width="17"><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.elementos") echo "selected"; ?>">
					<a href="<?=$CFG->admin_dir?>/index.php?module=mtto.elementos" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="mtto.elementos") echo "active"; ?>">Elementos</a>
					</td>
				</tr>
			</table>
			</div>
		</td>
	</tr>
	<tr><td colspan=2><hr></td></tr>
	<tr><td colspan=2><b><span onClick="switchDisplay('div_llantas')" style="cursor:pointer">LLANTAS</span></b></td></tr>
	<tr>
		<td colspan="2">
			<div id="div_llantas"<?if(!in_array(nvl($_GET["module"],$CFG->defaultModule),array("llta.estados_reencauches","llta.llantas","llta.proveedores","llta.marcas","llta.dimensiones","llta.tipos_llantas","llta.tipos_movimientos","llta.estados"))) echo " style=\"display:none\""?>>
			<table width="100%" border="0" cellspacing="0" cellpadding="1">
				<tr>
					<td width="17"><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="llta.marcas") echo "selected"; ?>">
					<a href="<?=$CFG->admin_dir?>/index.php?module=llta.marcas" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="llta.marcas") echo "active"; ?>">Marcas</a>
					</td>
				</tr>
				<tr>
					<td width="17"><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="llta.estados") echo "selected"; ?>">
					<a href="<?=$CFG->admin_dir?>/index.php?module=llta.estados" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="llta.estados") echo "active"; ?>">Estados Llantas</a>
					</td>
				</tr>
				<tr>
					<td width="17"><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="llta.tipos_llantas") echo "selected"; ?>">
					<a href="<?=$CFG->admin_dir?>/index.php?module=llta.tipos_llantas" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="llta.tipos_llantas") echo "active"; ?>">Tipos Llantas</a>
					</td>
				</tr>
				<tr>
					<td width="17"><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="llta.dimensiones") echo "selected"; ?>">
					<a href="<?=$CFG->admin_dir?>/index.php?module=llta.dimensiones" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="llta.dimensiones") echo "active"; ?>">Dimensiones</a>
					</td>
				</tr>
				<tr>
					<td width="17"><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="llta.proveedores") echo "selected"; ?>">
					<a href="<?=$CFG->admin_dir?>/index.php?module=llta.proveedores" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="llta.proveedores") echo "active"; ?>">Proveedores</a>
					</td>
				</tr>
				<tr>
					<td width="17"><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="llta.tipos_movimientos") echo "selected"; ?>">
					<a href="<?=$CFG->admin_dir?>/index.php?module=llta.tipos_movimientos" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="llta.tipos_movimientos") echo "active"; ?>">Tipos Movimientos</a>
					</td>
				</tr>
				<tr>
					<td width="17"><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="llta.estados_reencauches") echo "selected"; ?>">
					<a href="<?=$CFG->admin_dir?>/index.php?module=llta.estados_reencauches" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="llta.estados_reencauches") echo "active"; ?>">Estados Reencauche</a>
					</td>
				</tr>
				<tr>
					<td width="17"><img alt="" src="<?=$CFG->imagedir?>/vineta-cg.gif" height="7" width="11">&nbsp;</td>
					<td class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="llta.llantas") echo "selected"; ?>">
					<a href="<?=$CFG->admin_dir?>/index.php?module=llta.llantas" class="<?if (nvl($_GET["module"],$CFG->defaultModule)=="llta.llantas") echo "active"; ?>">Llantas</a>
					</td>
				</tr>
			</table>
			</div>
		</td>
	</tr>
	
	<tr><td colspan=2><hr></td></tr>
	<?}?>
	<tr>
		<td><a target="_parent" href="<?=$CFG->admin_dir?>/login.php"><b>Salir</b></a></td>
	</tr>
	<?}?>
</table>
