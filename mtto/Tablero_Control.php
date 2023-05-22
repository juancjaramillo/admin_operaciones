<?
include_once("../application.php");
include($CFG->dirroot."/templates/header_popup.php");

$user=$_SESSION[$CFG->sesion]["user"];
$hoy= date("Y-m-d", strtotime("-1 day"));
$periodos = $db->sql_query("select  distinct(periodo) as periodo from  mtto.ordenes_trabajo_proyecta order by 1 limit 12");	

?>
      
<table width="1520" border=1 bordercolor="#7fa840" align="center">
	<tr>
    	<td colspan="19" class="azul_16">
			TABLERO DE CONTROL
        </td>
    </tr>
	<tr>
		<th height='25' width=200" bgcolor='#b2d2e1' class='azul_osc_14'> EQUIPO/MES </th>
<?PHP
	$i=0;
	while($per = $db->sql_fetchrow($periodos))
	{
		echo "<th height='25' width='150' bgcolor='#b2d2e1' class='azul_osc_14'><strong>  ".$per["periodo"]."  </strong></th>";
		$ArPer[$i]=$per["periodo"];
		$i++;
	}
	
?>
	<tr>
<?
$qid = $db->sql_query("select id as id_vehiculo, codigo ||'/'|| placa as codigo from vehiculos v 
	where (v.id_estado!=4  or v.id_estado is null) and id_centro in 
	(SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') order by codigo");	
	while($vehi = $db->sql_fetchrow($qid))
	{
		echo "<tr>";
		echo "<th height='30' class='azul_osc_12'><strong>  ".$vehi["codigo"]."  </strong></th>";
		$count = count($ArPer);
		for ($i = 0; $i < $count; $i++) {
			// $cons = "SELECT ot.id_rutina,(cast(date_part('year',fecha_planeada) as text)||'-'||
					// CASE WHEN date_part('month',fecha_planeada)<10 	THEN  '0'||cast(date_part('month',fecha_planeada) as text)
						// ELSE cast(date_part('month',fecha_planeada) as text) END) AS PERIODO, 
					// REPLACE(REPLACE(REPLACE(REPLACE(rutina, 'PREVENTIVO ', ''),' HORAS','h'),'MTTO ',''),' ','-') AS rutina,
					// MAX(fecha_ejecucion_inicio) AS fecha_ejecucion_inicio
					// FROM  mtto.ordenes_trabajo ot
					// LEFT JOIN mtto.equipos e ON ot.id_equipo=e.id
					// LEFT JOIN mtto.rutinas r ON ot.id_rutina=r.id
					// WHERE id_rutina in (select id from mtto.rutinas where id_tipo_mantenimiento=1 and id>=4475 order by 1) 
					// AND e.id_vehiculo=".$vehi["id_vehiculo"]." AND (cast(date_part('year',fecha_planeada) as text)||'-'||
					// CASE WHEN date_part('month',fecha_planeada)<10 	THEN  '0'||cast(date_part('month',fecha_planeada) as text)
						// ELSE cast(date_part('month',fecha_planeada) as text) END)='".$ArPer[$i]."'
					// GROUP BY 1,2,3
					// UNION
					// SELECT ot.id_rutina,ot.periodo,
					 // REPLACE(REPLACE(REPLACE(REPLACE(rutina, 'PREVENTIVO ', ''),' HORAS','h'),'MTTO ',''),' ','-') AS rutina,
					 // fecha_ejecucion_inicio
					 // FROM  mtto.ordenes_trabajo_proyecta ot
					 // LEFT JOIN mtto.equipos e ON ot.id_equipo=e.id
					 // LEFT JOIN mtto.rutinas r ON ot.id_rutina=r.id	
					 // WHERE e.id_vehiculo=".$vehi["id_vehiculo"]." AND ot.periodo='".$ArPer[$i]."'
					 // GROUP BY 1,2,3,4 ORDER BY 3";
			
			$cons = "SELECT ot.id_rutina,ot.periodo,
					 REPLACE(REPLACE(REPLACE(REPLACE(rutina, 'PREVENTIVO ', ''),' HORAS','h'),'MTTO ',''),' ','-') AS rutina,
				   min(fecha_planeada) as fecha_planeada	
					 FROM  mtto.ordenes_trabajo_proyecta ot
					 LEFT JOIN mtto.equipos e ON ot.id_equipo=e.id
					 LEFT JOIN mtto.rutinas r ON ot.id_rutina=r.id	
					 WHERE e.id_vehiculo=".$vehi["id_vehiculo"]." AND ot.periodo='".$ArPer[$i]."'
					 GROUP BY 1,2,3 ORDER BY 3";

			 $qidReg = $db->sql_query($cons);	
			
			if($db->sql_numrows($qidReg)>0)
			{		
				echo "<td align='left' height='25' width='80' class='azul_osc_10'>";
				while($qidImp = $db->sql_fetchrow($qidReg))
				{
					if ($qidImp["fecha_planeada"]<$hoy) 
						echo "<P style='COLOR: #000000; BACKGROUND-COLOR: #ee152b'>".$qidImp["rutina"]."</p>";
					else
						echo "<P>".$qidImp["rutina"]."</p>";
				}
				echo "</td>";
			}
			else{
				echo "<td height='25' width='80' class='azul_osc_10'></td>";
			}
		}
		echo "</tr>";
	}

	
?>	
</table>	
