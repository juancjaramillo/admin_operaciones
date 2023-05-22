<?
 header("Content-type: application/vnd.ms-excel");
 header('Content-Disposition: attachment; filename="costos_servicio.csv"');
 readfile("http://64.131.77.126/pa/opera/costos_servicio.csv");
?>
