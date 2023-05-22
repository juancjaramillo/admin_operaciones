<?
include("../application.php");
set_time_limit(0);

$ids = "15,16,17,18";
$ids = "14,13,12,11";
$ids = "19,20,21,22";
$ids = "23,24,25,26";
$ids = "27,28,29,30";
$datos="CÓDIGO, TIEMPO, VELOCIDAD, POSICIÓN, LOGITUD, LATITUD\n";
$cons = "select v.codigo, g.tiempo, g.velocidad, g.hrposition, x(gps_geom) as longitud, y(gps_geom) as latitud, v.idgps as vidgps, g.id_vehi
		from gps_vehi g 
		LEFT JOIN prueba v ON v.idgps=g.id_vehi
		where v.id in ($ids) AND gps_geom is not null and g.tiempo <= now() 
		order by tiempo desc" ;
$qid = $db->sql_query($cons);

while($cli = $db->sql_fetchrow($qid))
{
  $datos.=$cli["codigo"].",".$cli["tiempo"].",".$cli["velocidad"].",".$cli["hrposition"].",".$cli["longitud"].",".$cli["latitud"]."\n";
}

$csv_file = "valle_seis.csv";

if(!$handle = fopen($csv_file, "w")) {
     echo "Cannot open file";
     exit;
 }
 if (fwrite($handle, $datos) === FALSE) {
     echo "Cannot write to file";
     exit;
 }
 fclose($handle);

echo "fin";
?>
