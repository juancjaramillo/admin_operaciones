<?
include(dirname(__FILE__) . "/../application.php");

$days_of_month = date('t',strtotime(date("Y-m-01", strtotime("-2 months"))));
$mesini = date("m", strtotime("-2 months"));
$anoini = date("Y", strtotime("-2 months"));
$inicio=date("Y-m-01", mktime(0, 0, 0, $mesini , '01', $anoini));
$final=date("Y-m-".$days_of_month,strtotime("-2 months"));
echo "Guardando Backup desde ".$inicio." hasta el ".$final;
$var = "historico.gps".$anoini.$mesini;
$qid = $db->sql_query("CREATE TABLE ".$var." as
				select * from gps_vehi where tiempo::date between '".$inicio."' and '".$final."';");
$qid = $db->sql_query("delete from gps_vehi where tiempo::date between '".$inicio."' and '".$final."';");

$db->sql_query("vacuum ANALYZE GPS_VEHI");
?>
