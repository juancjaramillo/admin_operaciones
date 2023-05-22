<?
include_once("../application.php");

if(!isset($_SESSION[$CFG->sesion]["user"])){
  $errorMsg="No existe la sesión.";
  error_log($errorMsg);
  die($errorMsg);
}

$mode=nvl($_GET["mode"],nvl($_POST["mode"],""));

switch(nvl($mode)){

	case "detalles":
		detalles($_GET["id"]);
	break;

	case "detalles_equipo":
		detalles_equipo($_GET["idEquipo"]);
	break;
  
  case "hisOperacion":
    hisOperacion($_GET["id"]);
  break;

	case "novedadesOperacion":
		novedadesOperacion($_GET["id"]);
	break;

	case "bajar_novedadesOperacion":
		bajar_novedadesOperacion($_GET);
	break;

	default:
		listado(nvl($_GET));
	break;

}


function listado($frm)
{
	global $CFG, $db,$ME;

	$user=$_SESSION[$CFG->sesion]["user"];
	$titulo = "VEHÍCULOS";

	$consulta = "SELECT v.id, v.codigo, v.placa, v.kilometraje, v.horometro,  c.centro, tp.tipo, e.estado, 
		'&nbsp;<a href='|| chr(39)||'javascript:detalles('||v.id||')'||chr(39)||')><img alt='|| chr(39)||'Ver Detalles'|| chr(39)||'src=".$CFG->wwwroot."/admin/iconos/transparente/icon-activate.gif border=0></a>' as opciones
    FROM vehiculos v
		LEFT JOIN centros c ON c.id=v.id_centro
		LEFT JOIN tipos_vehiculos tp ON tp.id = v.id_tipo_vehiculo
		LEFT JOIN estados_vehiculos e ON e.id = v.id_estado
		WHERE v.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]')
		ORDER BY centro, codigo";

	$datos = array();
	$qid = $db->sql_query($consulta);
	while($query = $db->sql_fetchrow($qid))
	{
		$datos[] = '{centro:"'.$query["centro"].'", codigo:"'.$query["codigo"].'", placa:"'.$query["placa"].'", kilometraje:"'.number_format($query["kilometraje"], 2, ",", ".").'",  horometro:"'. number_format($query["horometro"], 2, ",", ".").'", tipo:"'.$query["tipo"].'", estado:"'.$query["estado"].'", opciones:"'.$query["opciones"].'"}';
	}

	include($CFG->dirroot."/mtto/templates/listado_vehiculos.php");
}

function detalles($idVehiculo)
{
	global $CFG, $db,$ME;

	$botonActive="generales";
	$veh = $db->sql_row("SELECT v.*,  c.centro, tp.tipo,tp.ejes, e.estado, ts.tipo_sui, m.marca||'/'||r.referencia as marca, eve.nombre as estado_motor, case when v.tiene_gps='t' then 'Sí' else 'No' end as gps, case when v.alquilado ='t' then 'Sí' else 'No' end as alquiler
		FROM vehiculos v
		LEFT JOIN centros c ON c.id=v.id_centro
		LEFT JOIN tipos_vehiculos tp ON tp.id = v.id_tipo_vehiculo
		LEFT JOIN estados_vehiculos e ON e.id = v.id_estado
		LEFT JOIN tipos_vehiculos_sui ts ON ts.id=v.id_tipo_vehiculo_sui
		LEFT JOIN referencias r ON r.id=v.id_referencia
		LEFT JOIN marcas_vehiculos m ON m.id = r.id_marca_vehiculo
		LEFT JOIN eventos eve ON eve.codigo = cast(v.id_estado_motor  as char) 
		WHERE v.id=".$idVehiculo);
		
	$titulo = "VEHÍCULO ".$veh["codigo"]." / ".$veh["placa"];
	$datos = '
		<table width="60%" align = "center" border=1 bordercolor="#7fa840">
			<tr>
				<td align="right" width="30%">Centro :</td>
				<td align="left">'.$veh["centro"].'	</td>
			</tr>
			<tr>
				<td align="right" width="30%">Tipo Vehículo :</td>
				<td align="left">'.$veh["tipo"].'	</td>
			</tr>
			<tr>
				<td align="right" width="30%">Tipo Vehículo SUI :</td>
				<td align="left">'.$veh["tipo_sui"].'	</td>
			</tr>
			<tr>
				<td align="right" width="30%">Marca/Referencia :</td>
				<td align="left">'.$veh["marca"].'	</td>
			</tr>
			<tr>
				<td align="right" width="30%">Código :</td>
				<td align="left">'.$veh["codigo"].'	</td>
			</tr>
			<tr>
				<td align="right" width="30%">Serie SIM CARD :</td>
				<td align="left">'.$veh["serie_simcard"].'	</td>
			</tr>
			<tr>
				<td align="right" width="30%">Serie unidad :</td>
				<td align="left">'.$veh["serie_unidad"].'	</td>
			</tr>
			<tr>
				<td align="right" width="30%">Kilometraje :</td>
				<td align="left">'.$veh["kilometraje"].'	</td>
			</tr>
			<tr>
				<td align="right" width="30%">Horómetro :</td>
				<td align="left">'.$veh["horometro"].'	</td>
			</tr>
			<tr>
				<td align="right" width="30%">Modelo :</td>
				<td align="left">'.$veh["modelo"].'	</td>
			</tr>
			<tr>
				<td align="right" width="30%">Año :</td>
				<td align="left">'.$veh["ano"].'	</td>
			</tr>
		  <tr>
        <td align="right" width="30%">Cilindraje:</td>
        <td align="left">'.$veh["cilindraje"].' </td>
      </tr>
      <tr>
        <td align="right" width="30%">No. Motor :</td>
        <td align="left">'.$veh["nunmotor"].' </td>
      </tr>
      <tr>
        <td align="right" width="30%">No. Chasis :</td>
        <td align="left">'.$veh["nunchasis"].'  </td>
      </tr>
      <tr>
        <td align="right" width="30%">No. Ejes :</td>
        <td align="left">'.$veh["ejes"].' </td>
      </tr>
    	<tr>
				<td align="right" width="30%">Placa :</td>
				<td align="left">'.$veh["placa"].'	</td>
			</tr>
			<tr>
				<td align="right" width="30%">Estado :</td>
				<td align="left">'.$veh["estado"].'	</td>
			</tr>
			<tr>
				<td align="right" width="30%">Estado motor :</td>
				<td align="left">'.$veh["estado_motor"].'	</td>
			</tr>
			<tr>
				<td align="right" width="30%">Última posición :</td>
				<td align="left">'.$veh["hrposition"].'	</td>
			</tr>
			<tr>
				<td align="right" width="30%">Velocidad :</td>
				<td align="left">'.$veh["velocidad"].'	</td>
			</tr>
			<tr>
				<td align="right" width="30%">¿Tiene GPS? :</td>
				<td align="left">'.$veh["gps"].'	</td>
			</tr>
			<tr>
				<td align="right" width="30%">¿Alquilado? :</td>
				<td align="left">'.$veh["alquiler"].'	</td>
			</tr>
		</table>
	';
	
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/vehiculos_pestanias.php");
}

function detalles_equipo($idEquipo)
{
	global $CFG, $db,$ME;

	$botonActive="eq_".$idEquipo;
	$veh = $db->sql_row("SELECT v.id, v.codigo, v.placa
		FROM mtto.equipos e
		LEFT JOIN vehiculos v ON v.id=e.id_vehiculo
		WHERE e.id=".$idEquipo);
	$idVehiculo = $veh["id"];
		
	$titulo = "VEHÍCULO ".$veh["codigo"]." / ".$veh["placa"];
	$datos = '<iframe src="'.$CFG->wwwroot.'/mtto/equipos.php?mode=hoja_vida&id_equipo='.$idEquipo.'&botonCerrar=0" style="border-style: none; width: 100%; height: 900px; scrolling:auto; ">';

	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/vehiculos_pestanias.php");
}

function novedadesOperacion($idVehiculo)
{
	global $CFG, $db,$ME;

	$botonActive="novOperacion";
	$veh = $db->sql_row("SELECT v.id, v.codigo, v.placa
		FROM vehiculos v 
		WHERE v.id=".$idVehiculo);
	$idVehiculo = $veh["id"];
		
	$titulo = "VEHÍCULO ".$veh["codigo"]." / ".$veh["placa"];
	$datos = "";

	//novedades abiertas
	$datos = '
		<table width="100%" align = "center">
			<tr>
				<td height="50" align="right">
					<a class="boton_verde" href="'.$CFG->wwwroot.'/mtto/listado_hoja_vida_vehiculo.php?mode=bajar_novedadesOperacion&id_vehiculo='.$idVehiculo.'" title="Exportar a excel">Exportar a Excel</a>&nbsp;&nbsp;
				</td>
			</tr>
			<tr>
				<td align="center" height="40" valign="bottom"><span class="azul_12">NOVEDADES OPERACIÓN ABIERTAS</span></td>
			</tr>
		</table>';
	$qidNovAbiertas = $db->sql_query("
		SELECT * FROM (
			SELECT n.*, case when n.esquema='rec' then r.codigo||'/'||m.inicio else rb.codigo||'/'||b.inicio end as mov, 'No' as apoyo
			FROM novedades n
			LEFT JOIN rec.movimientos m ON m.id=n.id_movimiento
			LEFT JOIN bar.movimientos b ON b.id=n.id_movimiento
			LEFT JOIN micros r ON r.id=m.id_micro
			LEFT JOIN micros rb ON rb.id=b.id_micro
			WHERE m.id_vehiculo =". $idVehiculo." AND hora_fin IS NULL AND n.esquema != 'mtto' AND n.id_vehiculo_apoyo !=". $idVehiculo."
			UNION
			SELECT n.*, case when n.esquema='rec' then r.codigo||'/'||m.inicio else rb.codigo||'/'||b.inicio end as mov, 'Sí' as apoyo
			FROM novedades n
			LEFT JOIN rec.movimientos m ON m.id=n.id_movimiento
			LEFT JOIN bar.movimientos b ON b.id=n.id_movimiento
			LEFT JOIN micros r ON r.id=m.id_micro
			LEFT JOIN micros rb ON rb.id=b.id_micro
			WHERE n.id_vehiculo_apoyo =". $idVehiculo." AND hora_fin IS NULL AND n.esquema != 'mtto'
		) as foo
		ORDER BY foo.hora_inicio");
	if($db->sql_numrows($qidNovAbiertas)){
		$datos .='
		<table width="100%" border=1 bordercolor="#7fa840" id="tabla_actividades">
			<tr>
				<td align="center">FECHA</td>
				<td align="center">OBSERVACIONES</td>
				<td align="center">MOVIMIENTO<br>(RUTA/FECHA)</td>
				<td align="center">¿APOYÓ?</td>
				<td align="center">OPCIONES</td>
			</tr>';
		while($nov = $db->sql_fetchrow($qidNovAbiertas))
		{
			$datos.= "<tr>
				<td>".$nov["hora_inicio"]."</td>
				<td>".$nov["observaciones"]."</td>
				<td>".$nov["mov"]."</td>
				<td>".$nov["apoyo"]."</td>
				<td align ='center'>";
				if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["opciones_novedades"]))
					$datos.= "<a href='javascript:abrirVentanaJavaScript(\"novedades\",\"800\",\"500\",\"".$CFG->wwwroot."/novedades/novedades.php?mode=editar&id=".$nov["id"]."\")'><img alt='Editar' src='".$CFG->wwwroot."/admin/iconos/transparente/iconoeditar.gif' border='0'></a>&nbsp;&nbsp;&nbsp;<a href='javascript:abrirVentanaJavaScript(\"novedades\",\"1\",\"1\",\"".$CFG->wwwroot."/novedades/novedades.php?mode=cerrar&id=".$nov["id"]."\")'><img alt='Cerrar' src='".$CFG->wwwroot."/admin/iconos/transparente/check_green.png' border='0'></a>";
			$datos.= "</td>
			</tr>";
		}
		$datos.="</table>";
	}else
		$datos .='
		<table width="100%" border=1 bordercolor="#7fa840" id="tabla_actividades">
			<tr>
				<td align="center">NO HAY DATOS</td>
		</table>';

	//cerradas
	$datos .= '
		<table width="100%" align = "center">
			<tr>
				<td align="center" height="40" valign="bottom"><span class="azul_12">NOVEDADES OPERACIÓN CERRADAS</span></td>
			</tr>
		</table>';
	$qidNovCerradas = $db->sql_query("
		SELECT * FROM (
			SELECT n.*, case when n.esquema='rec' then r.codigo||'/'||m.inicio else rb.codigo||'/'||b.inicio end as mov, 'No' as apoyo
			FROM novedades n
			LEFT JOIN rec.movimientos m ON m.id=n.id_movimiento
			LEFT JOIN bar.movimientos b ON b.id=n.id_movimiento
			LEFT JOIN micros r ON r.id=m.id_micro
			LEFT JOIN micros rb ON rb.id=b.id_micro
			WHERE m.id_vehiculo =". $idVehiculo." AND hora_fin IS NOT NULL AND n.esquema != 'mtto' AND n.id_vehiculo_apoyo !=". $idVehiculo."
			UNION
			SELECT n.*, case when n.esquema='rec' then r.codigo||'/'||m.inicio else rb.codigo||'/'||b.inicio end as mov, 'Sí' as apoyo
			FROM novedades n
			LEFT JOIN rec.movimientos m ON m.id=n.id_movimiento
			LEFT JOIN bar.movimientos b ON b.id=n.id_movimiento
			LEFT JOIN micros r ON r.id=m.id_micro
			LEFT JOIN micros rb ON rb.id=b.id_micro
			WHERE n.id_vehiculo_apoyo =". $idVehiculo." AND hora_fin IS NOT NULL AND n.esquema != 'mtto'
		) as foo
		ORDER BY foo.hora_inicio");
	if($db->sql_numrows($qidNovCerradas)){
		$datos .= '
		<table width="100%" border=1 bordercolor="#7fa840" id="tabla_actividades">
			<tr>
				<td align="center">FECHA</td>
				<td align="center">OBSERVACIONES</td>
				<td align="center">MOVIMIENTO<br>(RUTA/FECHA)</td>
				<td align="center">¿APOYÓ?</td>
				<td align="center">OPCIONES</td>
			</tr>';
		while($nov = $db->sql_fetchrow($qidNovCerradas))
		{
			$datos.= "<tr>
				<td>".$nov["hora_inicio"]."</td>
				<td>".$nov["observaciones"]."</td>
				<td>".$nov["mov"]."</td>
				<td>".$nov["apoyo"]."</td>
				<td align ='center'>";
				if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["opciones_novedades"]))
					$datos.= "<a href='javascript:abrirVentanaJavaScript(\"novedades\",\"800\",\"500\",\"".$CFG->wwwroot."/novedades/novedades.php?mode=editar&id=".$nov["id"]."\")'><img alt='Editar' src='".$CFG->wwwroot."/admin/iconos/transparente/iconoeditar.gif' border='0'></a>";
			$datos.= "</td>
			</tr>";
		}
		$datos.="</table>";
	}else
		$datos .='
		<table width="100%" border=1 bordercolor="#7fa840" id="tabla_actividades">
			<tr>
				<td align="center">NO HAY DATOS</td>
		</table>';

	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/mtto/templates/vehiculos_pestanias.php");
}


function bajar_novedadesOperacion($frm)
{
	global $db, $CFG;

	$veh = $db->sql_row("SELECT codigo||'/'||placa as codigo FROM vehiculos WHERE id = ".$frm["id_vehiculo"]);

	$qid = $db->sql_query("
		SELECT * FROM (
			SELECT n.*, case when n.esquema='rec' then r.codigo||'/'||m.inicio else rb.codigo||'/'||b.inicio end as mov, 'No' as apoyo
			FROM novedades n
			LEFT JOIN rec.movimientos m ON m.id=n.id_movimiento
			LEFT JOIN bar.movimientos b ON b.id=n.id_movimiento
			LEFT JOIN micros r ON r.id=m.id_micro
			LEFT JOIN micros rb ON rb.id=b.id_micro
			WHERE m.id_vehiculo =". $frm["id_vehiculo"]." AND n.esquema != 'mtto' AND n.id_vehiculo_apoyo !=". $frm["id_vehiculo"]."
			UNION
			SELECT n.*, case when n.esquema='rec' then r.codigo||'/'||m.inicio else rb.codigo||'/'||b.inicio end as mov, 'Sí' as apoyo
			FROM novedades n
			LEFT JOIN rec.movimientos m ON m.id=n.id_movimiento
			LEFT JOIN bar.movimientos b ON b.id=n.id_movimiento
			LEFT JOIN micros r ON r.id=m.id_micro
			LEFT JOIN micros rb ON rb.id=b.id_micro
			WHERE n.id_vehiculo_apoyo =". $frm["id_vehiculo"]." AND n.esquema != 'mtto'
		) as foo
		ORDER BY foo.hora_inicio DESC");

	$titulos = array("FECHA" , "OBSERVACIONES", "MOVIMIENTO (RUTA/FECHA)", "¿APOYÓ?");
	$dx = array();
	while($nov = $db->sql_fetchrow($qid))
	{
		$dx[] = array($nov["hora_inicio"] , $nov["observaciones"] , $nov["mov"] , $nov["apoyo"]);
	}

	$stylos = array(1=>"txt_center", 2=>"txt_izq", 3=>"txt_center", 4=>"txt_center");
	imprimirXLS($titulos, $dx, "novedades_".$veh["codigo"], $stylos);
}

function hisOperacion($idVehiculo)
{
  global $CFG, $db,$ME;

  $botonActive="hisOperacion";
  $veh = $db->sql_row("SELECT v.id, v.codigo, v.placa
    FROM vehiculos v 
    WHERE v.id=".$idVehiculo);
  $idVehiculo = $veh["id"];
    
  $titulo = "VEHÍULO ".$veh["codigo"]." / ".$veh["placa"];
  $datos = "";

  $datos = '
    <table width="100%" align = "center">
      <tr>
        <td align="center" height="40" valign="bottom"><span
class="azul_12">OPERACIÓ DEL VEHICULO</span></td>
      </tr>
    </table>';

  $qidhisOperacion = $db->sql_query("
    SELECT m.*, i.codigo, v.codigo||' / '||v.placa as vehi, v.id as id_vehiculo
    FROM rec.movimientos m 
    LEFT JOIN micros i ON i.id=m.id_micro 
    LEFT JOIN vehiculos v ON v.id=m.id_vehiculo
    LEFT JOIN ases a ON a.id = i.id_ase
    WHERE v.id =". $idVehiculo." 
    ORDER BY v.codigo, v.placa, m.inicio");
    
  if($db->sql_numrows($qidhisOperacion)){
    $datos .='
    <table width="100%" border=1 bordercolor="#7fa840" id="tabla_actividades">
      <tr>
        <td align="center">VEHICULO</td>
        <td align="center">HORA INICIO</td>
        <td align="center">HORA FINAL</td>
        <td align="center">RUTA</td>
        <td align="center">VIAJES</td>
        <td align="center">KM SALIDA</td>
        <td align="center">KM REGRESO</td>
        <td align="center">KM RECORRIDO</td>
        <td align="center">PESO NETO</td>
        <td align="center">COMBUSTIBLE</td>
        <td align="center">HOROMETRO FINAL</td>
      </tr>';
    while($hismov = $db->sql_fetchrow($qidhisOperacion))
    {
      $viajes = averiguarViajeXMov($hismov["id"]);
      $recorrido = kmsRecorridoPorMov($hismov["id"]);
      $kmini = $db->sql_row("SELECT km FROM rec.desplazamientos WHERE
id_movimiento=".$hismov["id"]." ORDER BY hora_inicio LIMIT 1");
      if(nvl($kmini["km"],0)==0){
        $kmfin="0";
        $recorrido="0";
      }
      else $kmfin=$hismov["km_final"];
      $peso = averiguarPesoXMov($hismov["id"]);
      if(nvl($peso,0)==0){
          $peso="0";
      }
      $tiquetes = array();
      $qidPT = $db->sql_query("SELECT tiquete_entrada FROM
rec.movimientos_pesos mp LEFT JOIN rec.pesos p ON p.id=mp.id_peso WHERE
id_movimiento=".$hismov["id"]);
      while($tq = $db->sql_fetchrow($qidPT))
      {
        if($tq["tiquete_entrada"] != "")
          $tiquetes[] = $tq["tiquete_entrada"];
      }

      $datos.= "<tr>
        <td>".$hismov["vehi"]."</td>
        <td>".$hismov["inicio"]."</td>
        <td>".$hismov["final"]."</td>
        <td>".$hismov["codigo"]."</td>
        <td>".$viajes."</td>
        <td>".nvl($kmini["km"],0)."</td>
        <td>".$kmfin."</td>
        <td>".$recorrido."</td>
        <td>".number_format($peso,3)."</td>
        <td>".$mov["combustible"]."</td>
        <td>".$mov["horometro_final"]."</td>
      </tr>";
    }
    $datos.="</table>";
  }else
    $datos .='
    <table width="100%" border=1 bordercolor="#7fa840" id="hisOperacion">
      <tr>
        <td align="center">NO HAY DATOS</td>
    </table>';

  include($CFG->dirroot."/templates/header_popup.php");
  include($CFG->dirroot."/mtto/templates/vehiculos_pestanias.php");
}

?>
