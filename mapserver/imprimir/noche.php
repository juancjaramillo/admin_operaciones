<?php
require('microsnoche.php');

$dbconn = pg_connect("host=localhost dbname=trabajogis user=promoambiental password=kiR41178")
    or die('No se ha podido conectar: ' . pg_last_error());

// Realizando una consulta SQL
$query = "SELECT codigo,localidad,frecuencia,macro,micro,jornada,horario,dias,sup_cod,sup_id,ROUND(km_bordillo,3) AS km_bordillo,version, ST_XMin(the_geom) AS xmin, ST_XMax(the_geom) AS xmax, ST_YMin(the_geom) AS ymin, ST_YMax(the_geom) AS ymax FROM ";
	$query .= "barrido_poligono_noche ";
$query .= " WHERE TRUE AND ST_xmin(the_geom) IS NOT NULL";
//echo $query;
if($_GET['localidad']) $query .= " AND localidad ILIKE '%".$_GET['localidad']."%'";
if($_GET['codigo']) $query .= " AND codigo=".$_GET['codigo'];
if($_GET['macro']) $query .= " AND macro=".$_GET['macro'];
if($_GET['sup_cod']) $query .= " AND sup_cod ILIKE '".$_GET['sup_cod']."'";
$query .= " ORDER BY codigo ASC";
//echo $query;
$result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

// Imprimiendo los resultados en HTML
$i=1;
$pdf = new PDF('L','mm','Letter');
$title = 'Microrrutas';
$pdf->SetTitle($title);
$pdf->SetAuthor('Promoambiental Distrito S.A ESP');
while ($micro = pg_fetch_array($result, null, PGSQL_ASSOC)) {
$pdf->PrintChapter($i,'','PROMOAMBIENTAL DISTRITO S.A. ESP',$micro);
	$i++;
}
$pdf->Output();

// Liberando el conjunto de resultados
pg_free_result($result);

// Cerrando la conexión
pg_close($dbconn);
?>
