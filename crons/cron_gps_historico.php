<?
include(dirname(__FILE__) . "/../application.php");
echo date("Y-m-d H:i:s") . "\n";
$fecha = date("Y-m-d");
$fecha = date("Y-m-d 23:59:59", strtotime($fecha . " - 3 months"));
echo $fecha . "\n";
$strSQL="INSERT INTO gps_historico select * from gps_vehi where tiempo < '".$fecha."'";
echo $strSQL . "\n";
$qid=$db->sql_query($strSQL);
//$qid=db_query("DELETE FROM gps_historico WHERE tiempo < '" . $fecha . "'");
//$qid=db_query("VACUUM ANALYZE gps_vehi");
//$total=db_affected_rows($qid);
//echo $total . " registros eliminados.\n";
echo date("Y-m-d H:i:s") . "\n";
?>

