<?php
error_reporting(15);
//print_r($_SERVER["argv"]);
if(!isset($_SERVER["argv"][1])) die("No escribió el host.\n");
$host=$_SERVER["argv"][1];
if(!isset($_SERVER["argv"][2])) die("No escribió el mensaje.\n");
$comando=$_SERVER["argv"][2];
$da = stream_socket_client("udp://" . $host, $errno, $errstr);
if (!$da) {
    echo "ERROR: $errno - $errstr\n";
} else {
/*
  $comando="AT\$WAKEUP?";
  $comando="AT\$AREG?";
  $comando="AT\$IOGP1=0";
  $comando="AT\$IOGP1?";
  $comando="AT\$MDMID?";
  $comando="AT&F";
  $comando="AT\$EVTIM1?";
  $comando="AT\$MDMID?";
  $comando="AT\$MDMID=\"AD2002\"";
  $comando="AT\$EVTIM1=60";
  $comando="AT&W";
  $comando="AT\$EVENT?";
  $comando="AT\$IOCFG?";
  $comando="AT\$IOGPA?";
  $comando="AT+CGQREG=1,2,0,3,0,0";
  $comando="AT&V";
  $comando="AT\$PKG?";
  $comando="AT\$IOPULUP?";
*/
echo dechex(ord("T")) . "\n";
	$srcIP="69.60.111.94";
	preg_match("/^([^:]*):([^:]*)$/",$host,$matches);
	$dstIP=$matches[1];
  $udp_msg="45";//Version Length
	$udp_msg.="00";//Type of Service
	$udp_msg.="0023";//Length of Packet
	$udp_msg.="0000";//Identification
	$udp_msg.="0000";//Fragmentation Offset
	$udp_msg.="00";//Time to live
	$udp_msg.="11";//Protocol (UDP)
	$udp_msg.="0000";//IP Header Checksum
	$udp_msg.=ip2hex($srcIP);//Source IP
 	$udp_msg.=ip2hex($dstIP);//Destination IP
	$udp_msg.=strtoupper(dechex(7777));//src port
	$udp_msg.=strtoupper(dechex(7777));//dst port
	$udp_msg.="0004";//Length of Packet:
	$udp_msg.="0000";//UDP Checksum:
	$udp_msg.="0001";//UPD API Command
	$udp_msg.="04";//UDP API Read
	$udp_msg.="00";//UDP API Reserved
	$udp_msg.="415449";//AT Command = ATI

  $bin_msg="";
	echo "\$udp_msg=" . $udp_msg . "\n";
  for($contador=0;$contador<strlen($udp_msg);$contador=$contador+2){
    $byte=substr($udp_msg,$contador,2);
//    echo "BYTE=" . $byte . "\n";
    $byte_bin = pack('H*', $byte);
//    echo "BYTE_BIN=" . $byte_bin . "\n";
    $bin_msg.=$byte_bin;
 	}
	echo "bin_msg=" . $bin_msg . "\n";
  $stringTotal="";
  if(fwrite($da, $comando)){
//  if(fwrite($da, $bin_msg)){
    stream_set_timeout($da, 5);
    while($string=fread($da, 2048)){
			echo "String:" . $string . "\n";
      $stringTotal.=$string;
      echo $string;
      if(preg_match("/^(OK)|(ERROR)\$/m",trim($string))){
        fclose($da);
        die();
      }
    }
  }
  else echo "ERROR";
  fclose($da);
//  if(trim($stringTotal)=="") echo $errno;
}
/*
   20081003
   PUEDE SER EL FIREWALL.  REVISAR.
*/
function ip2hex($src){
	$arrSrc=explode(".",$src);
	$hexSrc="";
	foreach($arrSrc as $dec){
		$hexSrc.=dechex($dec);
	}
	$hexSrc=strtoupper($hexSrc);
	echo $src . ": " . $hexSrc . "\n";
	return($hexSrc);
}
?>

