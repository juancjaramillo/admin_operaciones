<?
include("../application.php");
require_once($CFG->libdir . "/nusoap/nusoap.php");
$CFG->soap_url="http://www.centurycontrol.com/webservices/CenturyServices.php?wsdl";
$client= new nusoap_client($CFG->soap_url,"wsdl");
//$client = $service->getProxy();
$params = array('user'=>"CCERON",'password'=>"170781",'placas'=>"VCM-251");
//$result = $client->call('GPSxPLACA',$params);
$result = $client->call('GPSxPLACA',$params);
echo "<pre>\n";
print_r($result);
echo "ASD\n";
error_log("asdasd");
echo $client->debug_str;
?>
