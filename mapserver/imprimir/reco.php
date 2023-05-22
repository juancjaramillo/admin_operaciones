<?php
require('microsreco.php');

$dbconn = pg_connect("host=localhost dbname=trabajogis user=promoambiental password=kiR41178")
    or die('No se ha podido conectar: ' . pg_last_error());

// Realizando una consulta SQL
$version = (!isset($_GET['V'])) ? "" : "_".$_GET['V'];
$query = "SELECT 
        idruta AS codigo,localidad,idmacrut AS macro, horario,dias,supervisor,version,tipo,micro, 
        ST_XMin(the_geom) AS xmin, ST_XMax(the_geom) AS xmax, ST_YMin(the_geom) AS ymin, ST_YMax(the_geom) AS ymax 
    FROM ";
	$query .= "recoleccion_poligono_t1_0600_1400".$version;
$query .= " WHERE TRUE AND length(idruta) = 7 AND ST_xmin(the_geom) IS NOT NULL";
//echo $query;
if($_GET['localidad']) $query .= " AND localidad ILIKE '%".$_GET['localidad']."%'";
if($_GET['codigo']) $query .= " AND idruta IN ('".$_GET['codigo']."')";
if($_GET['macro']) $query .= " AND macro=".$_GET['macro'];
if($_GET['sup_cod']) $query .= " AND supervisor ILIKE '".$_GET['sup_cod']."'";
$query .= " ORDER BY idruta ASC";
if($_GET['limit']) $query .= " LIMIT ".$_GET['limit'];
//echo $query;
//die();
$result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

// Imprimiendo los resultados en HTML
$i=1;
$pdf = new PDF('L','mm','Letter');
$title = 'Microrrutas';
$pdf->SetTitle($title);
$pdf->SetAuthor('Promoambiental Distrito S.A ESP');
while ($micro = pg_fetch_array($result, null, PGSQL_ASSOC)) {
$pdf->PrintChapter($i,'','PROMOAMBIENTAL DISTRITO S.A. ESP',$micro,$version);
	$i++;
}
$pdf->Output();

// Liberando el conjunto de resultados
pg_free_result($result);

// Cerrando la conexión
pg_close($dbconn);
?>
