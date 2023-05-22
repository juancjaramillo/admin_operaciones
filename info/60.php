<?
// echo "<pre>";
// print_r($_POST);
// print_r($_GET);
// echo "</pre>"; 
// error_reporting(E_ALL);
// ini_set("display_errors", 1);

include("../application.php");
$html = true;

$user=$_SESSION[$CFG->sesion]["user"];

if(isset($_POST["id_centro"]) && $_POST["id_centro"] != "")
	$centro = $_POST["id_centro"];
elseif(isset($_GET["id_centro"]) && $_GET["id_centro"] != "")
	$centro = $_GET["id_centro"];
else
{
	$qidCentro = $db->sql_row("SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."' ORDER BY id_centro");
	$centro = $qidCentro["id_centro"];
}

if(isset($_GET["format"])) 
{
	$html=false;
	$inicio = $_GET["inicio"];
	$final = $_GET["final"];
	
}

$titulo1 = $db->sql_row("SELECT upper(nombre||' : '||informe) as inf FROM informes i LEFT JOIN categorias_informes c ON c.id=i.id_categoria_informe WHERE i.id=".str_replace(".php","",simple_me($ME)));

if($html)
{
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/info/templates/fechas_form.php");
	tablita_titulos($titulo1["inf"],$inicio." / ".$final);
	$estilosText = array(1=>"align='left'", 2=>"align='left'");
}else{
	require_once $CFG->common_libdir."/writeexcel/class.writeexcel_workbook.inc.php";
	require_once $CFG->common_libdir."/writeexcel/class.writeexcel_worksheet.inc.php";

	$fname=$CFG->tmpdir."/informe.xls";
	if(file_exists($fname))
		unlink($fname);

	$workbook = new writeexcel_workbook($fname);
	$workbook->set_tempdir($CFG->tmpdir);
	$worksheet = &$workbook->addworksheet("reporte");
	$worksheet->set_zoom(80);
	titulo_grande_xls($workbook, $worksheet, 0, 11, $titulo1["inf"]."\n".$inicio." / ".$final);
	$fila=2; $columna=0;
	$estilosTit = array(1=>"azul_izq");
	$estilosText = array(1=>"txt_izq", 2=>"txt_izq");
}

$lineaPrimera = array("AÑO","P=PERIODOS","MACRO","RUTA","CODIGO RELLENO","FECHA DESDE","FECHA HASTA","NRDls","NRRls","Frec. CCU","ifr_nat", "ihr_nal","CTR_NA");

if($html)
{
	echo '<table width="70%" border=1 bordercolor="#7fa840" class="tabla_sencilla" align="center">
			<tr>';
		foreach($lineaPrimera as $ln)
			echo '<th height="40" valign="top">'.$ln.'</th>';
	echo "</tr>";
}else
{
	titulos_uno_xls($workbook, $worksheet, $fila, $columna, $lineaPrimera);
	$fila++;$columna=0;
}

$cond = " i.fecmov >= '".$inicio."' AND i.fecmov <= '".$final."'";

if($inicio == $final)
	$cond =" i.fecmov = '".$inicio."'";


$consulta = "CREATE OR REPLACE VIEW rec.indica720 AS 
(
        ( SELECT rutas.id_centro,
            date_part('isoyear'::text, rutas.inicio) AS ano,
            date_part('month'::text, rutas.inicio) AS mes,
            date_part('week'::text, rutas.inicio) AS semana,
            rutas.inicio AS fecmov,
            rutas.dia,
            rutas.id_micro,
            rutas.codigo,
            rutas.id_turno,
            rutas.hora_inicio,
            rutas.hora_fin,
            rutas.fecha_desde,
            rutas.fecha_hasta,
            mov.inicio,
            mov.final,
                CASE
                    WHEN mov.inicio IS NULL THEN 1
                    ELSE 0
                END AS incumplefrec,
                CASE
                    WHEN rutas.hora_fin < rutas.hora_inicio THEN rutas.inicio + 1 + rutas.hora_fin + '03:00:00'::interval
                    ELSE rutas.inicio + rutas.hora_fin + '03:00:00'::interval
                END AS maxhorafin,
                CASE
                    WHEN mov.inicio >
                    CASE
                        WHEN rutas.hora_fin < rutas.hora_inicio THEN rutas.inicio + 1 + rutas.hora_fin + '03:00:00'::interval
                        ELSE rutas.inicio + rutas.hora_fin + '03:00:00'::interval
                    END THEN 1
                    ELSE 0
                END AS incumplehor
           FROM ( SELECT rut.id_centro,
                    dias.inicio,
                    rut.dia,
                    rut.id_micro,
                    rut.codigo,
                    rut.id_turno,
                    rut.hora_inicio,
                    rut.hora_fin,
                    rut.fecha_desde,
                    rut.fecha_hasta
                   FROM ( SELECT dias_1.inicio,
                            date_part('isodow'::text, dias_1.inicio) AS dia
                           FROM ( SELECT m.inicio::date AS inicio
                                   FROM rec.movimientos m
                                  WHERE m.inicio::date >= '2018-08-12'::date) dias_1
                          GROUP BY dias_1.inicio
                          ORDER BY dias_1.inicio) dias
                     JOIN ( SELECT a.id_centro,
                            f.id_micro,
                            m.codigo,
                            f.dia,
                            f.id_turno,
                            f.hora_inicio,
                            f.hora_fin,
                            m.fecha_desde,
                            m.fecha_hasta
                           FROM micros_frecuencia f
                             LEFT JOIN micros m ON m.id = f.id_micro
                             LEFT JOIN ases a ON a.id = m.id_ase
                             LEFT JOIN servicios s ON s.id = m.id_servicio
                          WHERE s.esquema::text = 'rec'::text AND s.id in (1,10)
                          ORDER BY m.codigo) rut ON rut.dia::double precision = dias.dia
                  ORDER BY rut.codigo, dias.inicio) rutas
             LEFT JOIN ( SELECT mov1.id_centro,
                    mov1.fecha,
                    mov1.id_micro,
                    min(mov1.inicio) AS inicio,
                    max(mov1.final) AS final
                   FROM ( SELECT a.id_centro,
                            m.id AS id_movimiento,
                            m.inicio::date AS fecha,
                                CASE
                                    WHEN (( SELECT desplazamientos.hora_inicio
                                       FROM rec.desplazamientos
                                      WHERE desplazamientos.id_movimiento = m.id
                                      ORDER BY desplazamientos.hora_inicio
                                     LIMIT 1)) IS NOT NULL AND (( SELECT desplazamientos.hora_inicio
                                       FROM rec.desplazamientos
                                      WHERE desplazamientos.id_movimiento = m.id
                                      ORDER BY desplazamientos.hora_inicio
                                     LIMIT 1)) < apo.inicio THEN ( SELECT desplazamientos.hora_inicio
                                       FROM rec.desplazamientos
                                      WHERE desplazamientos.id_movimiento = m.id
                                      ORDER BY desplazamientos.hora_inicio
                                     LIMIT 1)
                                    WHEN apo.inicio IS NOT NULL AND apo.inicio < (( SELECT desplazamientos.hora_inicio
                                       FROM rec.desplazamientos
                                      WHERE desplazamientos.id_movimiento = m.id
                                      ORDER BY desplazamientos.hora_inicio
                                     LIMIT 1)) THEN apo.inicio
                                    WHEN (( SELECT desplazamientos.hora_inicio
                                       FROM rec.desplazamientos
                                      WHERE desplazamientos.id_movimiento = m.id
                                      ORDER BY desplazamientos.hora_inicio
                                     LIMIT 1)) IS NULL AND apo.inicio IS NOT NULL THEN apo.inicio
                                    ELSE ( SELECT desplazamientos.hora_inicio
                                       FROM rec.desplazamientos
                                      WHERE desplazamientos.id_movimiento = m.id
                                      ORDER BY desplazamientos.hora_inicio
                                     LIMIT 1)
                                END AS inicio,
                            ( SELECT desplazamientos.hora_inicio
                                   FROM rec.desplazamientos
                                  WHERE desplazamientos.id_movimiento = m.id
                                  ORDER BY desplazamientos.hora_inicio
                                 LIMIT 1) AS inicio_real,
                                CASE
                                    WHEN (( SELECT desplazamientos.hora_inicio
                                       FROM rec.desplazamientos
                                      WHERE desplazamientos.id_movimiento = m.id AND desplazamientos.id_tipo_desplazamiento = 4
                                      ORDER BY desplazamientos.hora_inicio DESC
                                     LIMIT 1)) IS NOT NULL AND (( SELECT desplazamientos.hora_inicio
                                       FROM rec.desplazamientos
                                      WHERE desplazamientos.id_movimiento = m.id AND desplazamientos.id_tipo_desplazamiento = 4
                                      ORDER BY desplazamientos.hora_inicio DESC
                                     LIMIT 1)) < apo.final THEN apo.final
                                    WHEN apo.final IS NOT NULL AND apo.final < (( SELECT desplazamientos.hora_inicio
                                       FROM rec.desplazamientos
                                      WHERE desplazamientos.id_movimiento = m.id AND desplazamientos.id_tipo_desplazamiento = 4
                                      ORDER BY desplazamientos.hora_inicio DESC
                                     LIMIT 1)) THEN ( SELECT desplazamientos.hora_inicio
                                       FROM rec.desplazamientos
                                      WHERE desplazamientos.id_movimiento = m.id AND desplazamientos.id_tipo_desplazamiento = 4
                                      ORDER BY desplazamientos.hora_inicio DESC
                                     LIMIT 1)
                                    WHEN (( SELECT desplazamientos.hora_inicio
                                       FROM rec.desplazamientos
                                      WHERE desplazamientos.id_movimiento = m.id AND desplazamientos.id_tipo_desplazamiento = 4
                                      ORDER BY desplazamientos.hora_inicio DESC
                                     LIMIT 1)) IS NULL AND apo.inicio IS NOT NULL THEN apo.final
                                    ELSE ( SELECT desplazamientos.hora_inicio
                                       FROM rec.desplazamientos
                                      WHERE desplazamientos.id_movimiento = m.id AND desplazamientos.id_tipo_desplazamiento = 4
                                      ORDER BY desplazamientos.hora_inicio DESC
                                     LIMIT 1)
                                END AS final,
                            ( SELECT desplazamientos.hora_inicio
                                   FROM rec.desplazamientos
                                  WHERE desplazamientos.id_movimiento = m.id AND desplazamientos.id_tipo_desplazamiento = 4
                                  ORDER BY desplazamientos.hora_inicio DESC
                                 LIMIT 1) AS final_real,
                            m.id_micro,
                            'rec' AS esquma,
                            ap.id_apoyo,
                            ap.id_movimiento,
                            apo.inicio AS inicioap,
                            apo.final AS finalap,
                            apo.peso
                           FROM rec.movimientos m
                             LEFT JOIN micros r ON r.id = m.id_micro
                             LEFT JOIN ases a ON a.id = r.id_ase
                             LEFT JOIN servicios s ON s.id = r.id_servicio
                             LEFT JOIN rec.apoyos_movimientos ap ON m.id = ap.id_movimiento
                             LEFT JOIN rec.apoyos apo ON apo.id = ap.id_apoyo
                          WHERE m.final IS NOT NULL AND m.inicio::date >= '2018-08-12'::date AND s.id in (1,10)) mov1(id_centro, id_movimiento, fecha, inicio, inicio_real, final, final_real, id_micro, esquma, id_apoyo, id_movimiento_1, inicioap, finalap, peso)
                  GROUP BY mov1.id_centro, mov1.fecha, mov1.id_micro
                  ORDER BY mov1.fecha) mov ON rutas.inicio = mov.fecha AND rutas.id_micro = mov.id_micro
          WHERE rutas.fecha_desde <= mov.inicio::date AND (rutas.fecha_hasta >= mov.inicio::date OR rutas.fecha_hasta IS NULL)
          ORDER BY rutas.id_micro, rutas.inicio)
        UNION
        ( SELECT rutas.id_centro,
            date_part('isoyear'::text, rutas.inicio) AS ano,
            date_part('month'::text, rutas.inicio) AS mes,
            date_part('week'::text, rutas.inicio) AS semana,
            rutas.inicio AS fecmov,
            rutas.dia,
            rutas.id_micro,
            rutas.codigo,
            rutas.id_turno,
            rutas.hora_inicio,
            rutas.hora_fin,
            rutas.fecha_desde,
            rutas.fecha_hasta,
            NULL::timestamp without time zone AS inicio,
            NULL::timestamp without time zone AS final,
            1 AS incumplefrec,
                CASE
                    WHEN rutas.hora_fin < rutas.hora_inicio THEN rutas.inicio + 1 + rutas.hora_fin + '03:00:00'::interval
                    ELSE rutas.inicio + rutas.hora_fin + '03:00:00'::interval
                END AS maxhorafin,
            1 AS incumplehor
           FROM ( SELECT rut.id_centro,
                    dias.inicio,
                    rut.dia,
                    rut.id_micro,
                    rut.codigo,
                    rut.id_turno,
                    rut.hora_inicio,
                    rut.hora_fin,
                    rut.fecha_desde,
                    rut.fecha_hasta
                   FROM ( SELECT dias_1.inicio,
                            date_part('isodow'::text, dias_1.inicio) AS dia
                           FROM ( SELECT m.inicio::date AS inicio
                                   FROM rec.movimientos m
                                  WHERE m.inicio::date >= '2018-08-12'::date) dias_1
                          GROUP BY dias_1.inicio
                          ORDER BY dias_1.inicio) dias
                     JOIN ( SELECT a.id_centro,
                            f.id_micro,
                            m.codigo,
                            f.dia,
                            f.id_turno,
                            f.hora_inicio,
                            f.hora_fin,
                            m.fecha_desde,
                            m.fecha_hasta
                           FROM micros_frecuencia f
                             LEFT JOIN micros m ON m.id = f.id_micro
                             LEFT JOIN ases a ON a.id = m.id_ase
                             LEFT JOIN servicios s ON s.id = m.id_servicio
                          WHERE s.esquema::text = 'rec'::text AND s.id in (1,10)
                          ORDER BY m.codigo) rut ON rut.dia::double precision = dias.dia
                  WHERE dias.inicio >= rut.fecha_desde AND rut.fecha_hasta IS NULL OR dias.inicio >= rut.fecha_desde AND dias.inicio <= rut.fecha_hasta
                  ORDER BY rut.codigo) rutas
          ORDER BY rutas.id_micro)
) EXCEPT
( SELECT rutas.id_centro,
    date_part('isoyear'::text, rutas.inicio) AS ano,
    date_part('month'::text, rutas.inicio) AS mes,
    date_part('week'::text, rutas.inicio) AS semana,
    rutas.inicio AS fecmov,
    rutas.dia,
    rutas.id_micro,
    rutas.codigo,
    rutas.id_turno,
    rutas.hora_inicio,
    rutas.hora_fin,
    rutas.fecha_desde,
    rutas.fecha_hasta,
    NULL::timestamp without time zone AS inicio,
    NULL::timestamp without time zone AS final,
    1 AS incumplefrec,
        CASE
            WHEN rutas.hora_fin < rutas.hora_inicio THEN rutas.inicio + 1 + rutas.hora_fin + '03:00:00'::interval
            ELSE rutas.inicio + rutas.hora_fin + '03:00:00'::interval
        END AS maxhorafin,
    1 AS incumplehor
   FROM ( SELECT rut.id_centro,
            dias.inicio,
            rut.dia,
            rut.id_micro,
            rut.codigo,
            rut.id_turno,
            rut.hora_inicio,
            rut.hora_fin,
            rut.fecha_desde,
            rut.fecha_hasta
           FROM ( SELECT dias_1.inicio,
                    date_part('isodow'::text, dias_1.inicio) AS dia
                   FROM ( SELECT m.inicio::date AS inicio
                           FROM rec.movimientos m
                          WHERE m.inicio::date >= '2018-08-12'::date) dias_1
                  GROUP BY dias_1.inicio
                  ORDER BY dias_1.inicio) dias
             JOIN ( SELECT a.id_centro,
                    f.id_micro,
                    m.codigo,
                    f.dia,
                    f.id_turno,
                    f.hora_inicio,
                    f.hora_fin,
                    m.fecha_desde,
                    m.fecha_hasta
                   FROM micros_frecuencia f
                     LEFT JOIN micros m ON m.id = f.id_micro
                     LEFT JOIN ases a ON a.id = m.id_ase
                     LEFT JOIN servicios s ON s.id = m.id_servicio
                  WHERE s.esquema::text = 'rec'::text AND s.id in (1,10)
                  ORDER BY m.codigo) rut ON rut.dia::double precision = dias.dia
          ORDER BY rut.codigo, dias.inicio) rutas
     LEFT JOIN ( SELECT mov1.id_centro,
            mov1.fecha,
            mov1.id_micro,
            min(mov1.inicio) AS inicio,
            max(mov1.final) AS final
           FROM ( SELECT a.id_centro,
                    m.id AS id_movimiento,
                    m.inicio::date AS fecha,
                        CASE
                            WHEN (( SELECT desplazamientos.hora_inicio
                               FROM rec.desplazamientos
                              WHERE desplazamientos.id_movimiento = m.id
                              ORDER BY desplazamientos.hora_inicio
                             LIMIT 1)) IS NOT NULL AND (( SELECT desplazamientos.hora_inicio
                               FROM rec.desplazamientos
                              WHERE desplazamientos.id_movimiento = m.id
                              ORDER BY desplazamientos.hora_inicio
                             LIMIT 1)) < apo.inicio THEN ( SELECT desplazamientos.hora_inicio
                               FROM rec.desplazamientos
                              WHERE desplazamientos.id_movimiento = m.id
                              ORDER BY desplazamientos.hora_inicio
                             LIMIT 1)
                            WHEN apo.inicio IS NOT NULL AND apo.inicio < (( SELECT desplazamientos.hora_inicio
                               FROM rec.desplazamientos
                              WHERE desplazamientos.id_movimiento = m.id
                              ORDER BY desplazamientos.hora_inicio
                             LIMIT 1)) THEN apo.inicio
                            WHEN (( SELECT desplazamientos.hora_inicio
                               FROM rec.desplazamientos
                              WHERE desplazamientos.id_movimiento = m.id
                              ORDER BY desplazamientos.hora_inicio
                             LIMIT 1)) IS NULL AND apo.inicio IS NOT NULL THEN apo.inicio
                            ELSE ( SELECT desplazamientos.hora_inicio
                               FROM rec.desplazamientos
                              WHERE desplazamientos.id_movimiento = m.id
                              ORDER BY desplazamientos.hora_inicio
                             LIMIT 1)
                        END AS inicio,
                    ( SELECT desplazamientos.hora_inicio
                           FROM rec.desplazamientos
                          WHERE desplazamientos.id_movimiento = m.id
                          ORDER BY desplazamientos.hora_inicio
                         LIMIT 1) AS inicio_real,
                        CASE
                            WHEN (( SELECT desplazamientos.hora_inicio
                               FROM rec.desplazamientos
                              WHERE desplazamientos.id_movimiento = m.id AND desplazamientos.id_tipo_desplazamiento = 4
                              ORDER BY desplazamientos.hora_inicio DESC
                             LIMIT 1)) IS NOT NULL AND (( SELECT desplazamientos.hora_inicio
                               FROM rec.desplazamientos
                              WHERE desplazamientos.id_movimiento = m.id AND desplazamientos.id_tipo_desplazamiento = 4
                              ORDER BY desplazamientos.hora_inicio DESC
                             LIMIT 1)) < apo.final THEN apo.final
                            WHEN apo.final IS NOT NULL AND apo.final < (( SELECT desplazamientos.hora_inicio
                               FROM rec.desplazamientos
                              WHERE desplazamientos.id_movimiento = m.id AND desplazamientos.id_tipo_desplazamiento = 4
                              ORDER BY desplazamientos.hora_inicio DESC
                             LIMIT 1)) THEN ( SELECT desplazamientos.hora_inicio
                               FROM rec.desplazamientos
                              WHERE desplazamientos.id_movimiento = m.id AND desplazamientos.id_tipo_desplazamiento = 4
                              ORDER BY desplazamientos.hora_inicio DESC
                             LIMIT 1)
                            WHEN (( SELECT desplazamientos.hora_inicio
                               FROM rec.desplazamientos
                              WHERE desplazamientos.id_movimiento = m.id AND desplazamientos.id_tipo_desplazamiento = 4
                              ORDER BY desplazamientos.hora_inicio DESC
                             LIMIT 1)) IS NULL AND apo.inicio IS NOT NULL THEN apo.final
                            ELSE ( SELECT desplazamientos.hora_inicio
                               FROM rec.desplazamientos
                              WHERE desplazamientos.id_movimiento = m.id AND desplazamientos.id_tipo_desplazamiento = 4
                              ORDER BY desplazamientos.hora_inicio DESC
                             LIMIT 1)
                        END AS final,
                    ( SELECT desplazamientos.hora_inicio
                           FROM rec.desplazamientos
                          WHERE desplazamientos.id_movimiento = m.id AND desplazamientos.id_tipo_desplazamiento = 4
                          ORDER BY desplazamientos.hora_inicio DESC
                         LIMIT 1) AS final_real,
                    m.id_micro,
                    'rec' AS esquma,
                    ap.id_apoyo,
                    ap.id_movimiento,
                    apo.inicio AS inicioap,
                    apo.final AS finalap,
                    apo.peso
                   FROM rec.movimientos m
                     LEFT JOIN micros r ON r.id = m.id_micro
                     LEFT JOIN ases a ON a.id = r.id_ase
                     LEFT JOIN servicios s ON s.id = r.id_servicio
                     LEFT JOIN rec.apoyos_movimientos ap ON m.id = ap.id_movimiento
                     LEFT JOIN rec.apoyos apo ON apo.id = ap.id_apoyo
                  WHERE m.final IS NOT NULL AND m.inicio::date >= '2018-08-12'::date AND s.id in (1,10)) mov1(id_centro, id_movimiento, fecha, inicio, inicio_real, final, final_real, id_micro, esquma, id_apoyo, id_movimiento_1, inicioap, finalap, peso)
          GROUP BY mov1.id_centro, mov1.fecha, mov1.id_micro
          ORDER BY mov1.fecha) mov ON rutas.inicio = mov.fecha AND rutas.id_micro = mov.id_micro
  WHERE rutas.fecha_desde <= mov.inicio::date AND (rutas.fecha_hasta >= mov.inicio::date OR rutas.fecha_hasta IS NULL)
  ORDER BY rutas.id_micro, rutas.inicio);";
#echo $consulta;
$qid = $db->sql_query($consulta);

$days_of_month = date('t',strtotime($inicio,$final));
$days = (strtotime($final)-strtotime($inicio))/(24*60*60);
$days = intval($days/31)+1; 

$consulta = "SELECT id_centro,ano, A.macro,id_micro,A.cod_relleno,A.codigo,mic.fecha_desde, mic.fecha_hasta,fccu,
	(((CASE WHEN mic.fecha_hasta<'2019-06-30' THEN fecha_hasta  ELSE '$final' END)-(CASE WHEN mic.fecha_desde<'$inicio' THEN '$inicio' ELSE mic.fecha_desde END))/31+1) as p,
	sum(ifr_nat) as ifr_nat, sum(ihr_nal) as ihr_nal,
	(0.5 *(sum(ifr_nat)+sum(ihr_nal)))/(((CASE WHEN mic.fecha_hasta<'$final' THEN fecha_hasta  ELSE '$final' END)-(CASE WHEN mic.fecha_desde<'2019-01-01' THEN '2019-01-01' ELSE mic.fecha_desde END))/31+1) as CTR_NA,
	sum(nrrls) as nrrls, sum(nrlits) as nrlits
	FROM  
	(SELECT i.id_centro,i.ano,i.mes,i.semana,i.id_micro,frec.cod_relleno,mac.macro,i.codigo, sum(incumplefrec) as nrlits, 
		(date_part('days', (date(i.ano||'-'||i.mes||'-01')+ interval '1 month') - date(i.ano||'-'||i.mes||'-01'))) as ndm, 
		frec.fccu, sum(mac.incumplehor) as nrrls,
		(CAST (sum(i.incumplefrec) AS numeric)) / (CAST((date_part('days', (date(i.ano||'-'||i.mes||'-01')+ interval '1 month') - date(i.ano||'-'||i.mes||'-01')))/7.00*frec.fccu AS numeric))*100.00 as ifr_nat, 
		(CAST (sum(mac.incumplehor) AS numeric)) / (CAST((date_part('days', (date(i.ano||'-'||i.mes||'-01')+ interval '1 month') - date(i.ano||'-'||i.mes||'-01')))/7.00*frec.fccu AS numeric))*100.00 as ihr_nal 
	FROM rec.indica720 i 
	LEFT JOIN centros c ON c.id = i.id_centro 
	LEFT JOIN (SELECT a.id_centro,f.id_micro,m.cod_relleno,m.macro,count(*) as fccu 
				FROM micros_frecuencia f 
				LEFT JOIN micros m ON m.id = f.id_micro 
				LEFT JOIN ases a ON a.id = m.id_ase 
				GROUP BY a.id_centro,f.id_micro,m.cod_relleno,m.macro) as frec on i.id_micro=frec.id_micro 
	LEFT JOIN (SELECT ind.id_centro,fecmov,mic.macro, max(incumplehor) as incumplehor 
				FROM rec.indica720 ind
				LEFT JOIN micros mic ON mic.id = ind.id_micro 
				GROUP BY ind.id_centro,fecmov,mic.macro) as mac 
				ON mac.id_centro=i.id_centro AND mac.fecmov=i.fecmov AND frec.macro=mac.macro
	WHERE i.id_centro = ".$centro ." AND ".$cond."
	GROUP BY i.id_centro,ano,mes,4,i.id_micro,frec.cod_relleno,mac.macro,i.codigo,fccu 
	ORDER BY i.id_centro,i.codigo,ano,mes,semana)A
	LEFT JOIN micros mic ON a.id_micro=mic.id 	
GROUP BY id_centro,ano, A.macro,A.id_micro,A.cod_relleno,A.codigo,mic.fecha_desde,mic.fecha_hasta,fccu
ORDER BY id_centro,ano,codigo";

#echo $consulta;

$qid = $db->sql_query($consulta);
while($query = $db->sql_fetchrow($qid))
{
	$linea = array($query["ano"],$query["p"],$query["macro"],$query["codigo"],$query["cod_relleno"],$query["fecha_desde"],$query["fecha_hasta"],$query["nrlits"],$query["nrrls"],$query["fccu"],number_format($query["ifr_nat"],2).'%',number_format($query["ihr_nal"],2).'%',number_format($query["ctr_na"],2).'%');
	if($html)
		imprimirLinea($linea,"", array(1=>"align='center'", 2=>"align='center'", 3=>"align='center'", 4=>"align='center'", 5=>"align='center'",6=>"align='center'",7=>"align='center'",7=>"align='center'",8=>"align='center'",9=>"align='center'",10=>"align='center'"));
	else
		imprimirLinea_xls($workbook, $worksheet, $fila, $columna, $linea, array(1=>"txt_center",  2=>"txt_center", 3=>"txt_center", 4=>"txt_center", 5=>"txt_center", 6=>"txt_center", 7=>"txt_center", 8=>"txt_center",9=>"txt_center",10=>"txt_center"));
		
}

//final
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final."&id_centro=".$centro;
	echo "</table><br /><br />";
	#graficaIndicadores($dxGraf, "CUMPLIMIENTO HORARIOS RECOLECCIÓN: ".$inicio."/".$final);
	echo "
	<table width='98%' align='center'>
		<tr>
			<td height='50' valign='bottom' align='right'><input type='button' class='boton_verde' value='Bajar en xls' onclick=\"window.location.href='".$ME.$link."'\"/></td>
		</tr>
	</table>
	";
}
else
{
	$workbook->close();
	$nombreArchivo=preg_replace("/[^0-9a-z_.]/i","_",$titulo1["inf"])."_".$inicio."_".$final.".xls";
	header("Content-Type: application/x-msexcel; name=\"".$nombreArchivo."\"");
	header("Content-Disposition: inline; filename=\"".$nombreArchivo."\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
}

?>
