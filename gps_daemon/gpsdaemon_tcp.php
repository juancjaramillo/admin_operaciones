#!/usr/bin/php -q
<?

//error_reporting(E_ALL);
// ini_set("display_errors", 1);
// echo "<pre>";
// print_r($_POST);
// print_r($_GET);
// echo "</pre>"; 
// Create a TCP Stream socket
/*	------------------------	*/
/*	MAIN						*/
/*	------------------------	*/
// Set time limit to indefinite execution
include(dirname(__FILE__) . "/../application.php");
require($CFG->common_libdir . "/funciones_gps.php");
//include("funciones.php");
$argumentos=$_SERVER["argv"];
$CFG->verbose=0;
for($i=1;$i<sizeof($argumentos);$i++){
  if($argumentos[$i]=="-p" && isset($argumentos[$i+1]) && is_numeric($argumentos[$i+1])){
    $port=$argumentos[$i+1];
    $i++;
  }
  if($argumentos[$i]=="-v") $CFG->verbose=1;
}

$msg="[" . date("Y-m-d H:i:s") . "] Starting...\n";
if($CFG->verbose) echo $msg;

set_time_limit (0);

// Set the ip and port we will listen on
//$address = '69.60.111.94';
$address = '192.168.100.213';

if(!isset($port)) $port = 7776;
$msg="[" . date("Y-m-d H:i:s") . "] ....Demonio 2 PUERTO : ".$port."\n\n";
if($CFG->verbose) echo $msg;

$max_clients = 300;

// Array that will hold client information
$client = Array();

// Create a TCP Stream socket
$sock = socket_create(AF_INET, SOCK_STREAM, 0);
if (!socket_setopt($sock,SOL_SOCKET,SO_REUSEADDR,1)) {
  $msg=date("[Y-m-d H:i:s]") . "socket_setopt() failed: reason: ".socket_strerror(socket_last_error($sock))."\n";
  if($CFG->verbose) echo $msg;
  exit;
}
// Bind the socket to an address/port
socket_bind($sock, $address, $port) or die('Could not bind to address');
// Start listening for connections
socket_listen($sock);

$activo=FALSE;
$inicio=time();
$arreglo_pendientes=array();
// Loop continuously
while (true) {
	// Setup clients listen socket for reading
	$read[0] = $sock;
	for ($i = 0; $i < $max_clients; $i++)
	{
		if (isset($client[$i]['sock']) && $client[$i]['sock']!= null)
		$read[$i + 1] = $client[$i]['sock'] ;
	}
	// Set up a blocking call to socket_select()
	$ready = socket_select($read,$write = null,$except = null,null);
	/* if a new connection is being made add it to the client array */
	if (in_array($sock, $read)) {
		for ($i = 0; $i < $max_clients; $i++)
		{
			if (!isset($client[$i]['sock']) || $client[$i]['sock'] == null) {
				$client[$i]['sock'] = socket_accept($sock);
				break;
			}
			elseif ($i == $max_clients - 1){
				print ("too many clients");
				die();
			}
		}
		if (--$ready <= 0)
			continue;
	} // end if in_array

	// If a client is trying to write - handle it now
	for ($i = 0; $i < $max_clients; $i++) // for each client
	{
		if (isset($client[$i]['sock']) && in_array($client[$i]['sock'] , $read))
		{			
		//	Print_r($client[$i])."\n";
			
			if(!$input = socket_read($client[$i]['sock'] , 4096)){
        		echo "[" . date("Y-m-d H:i:s") . "] Socket $i (" . nvl($client[$i]['id_gps']). ") DESCONECTADO\n";
        		echo "Error en la conexión.  Reseteando...\n";
        		socket_close($client[$i]['sock']);
        		unset($client[$i]);
      		}
			
			if ($input)
			{
				$DBConnected=false;
				$msg=date("[Y-m-d H:i:s]") . "\n\n";
				if($CFG->verbose) echo $msg;
				
				//if (empty($client[$i]['sock']['id_gps'])) 
				if ((empty($client[$i]['id_gps']))  || (is_null($client[$i]['id_gps'])))
					$client[$i]['id_gps'] = substr($input,40,6);
				
				Print_R($client[$i])."\n";
				
				echo "Imprimiento Input bytes --->>>>".$input."\n\n";				
				$array[0] = $client[$i]['id_gps'];
				$array[1] = substr($input,0,(strpos($input, '>')-1));
				$array[2] = substr($input,(strpos($input, '>')),109);
				//print_r($array)."\n\n";	
			
			
		 	echo "ENTERO:: ".(hexdec($array[1]))."\n";	
			echo "CONVERTIDO A BASE 16 A BINARIO 2 :: ".base_convert(($array[1]), 16, 2)."\n"; 
			//	printf("%u\n", hexdec($array[1]))."\n"; 
			//printf("%05.10f\n", $array[1])."\n";			
			//	$f = bchexdec($array[1]);
			//	var_dump($f)."\n"; 
			
			
echo "VALIDAR SI ES STRING:: ".gettype(($array[1]))."\n"; // before datatype conversion: $count is a string
settype(hexdec($array[1]),'int')."\n";
echo "CONVERTIR A ENTERO:: ".gettype(($array[1]))."\n"; 
			echo "HEXDEC:: ".hexdec($array[1])."\n"; 
				
				
				if(substr($array[2],1,4)=='GS06' && strlen($array[2]) > 22)
				{ 
					$imei = substr($array[2],6,15);
					#echo "\n IMEI " . $imei;
					#echo "\n";
					$megatrama = preg_replace('/^\*GS06,[0-9]{15},/', '', $array[2]);
					$tramas = array();
					if(strstr($array[2],'$') === FALSE){
						$tramas[0] = $megatrama;
					}
					else{ 
						$tramas = explode('$',$megatrama);
					}
					for($j=0;$j<sizeof($tramas);$j++){
						if(!isset($vehiculos)) $vehiculos=array();
							$arreglo = interpretar_trama_sp4600($tramas[$j],$imei);
							insertar_registro_gps($arreglo);
							socket_write($client[$i]['sock'], chr(1));
					}
				}
								
				if(substr($array[2],0,5)=='>STXT' && strlen($array[2]) > 22)
				{ 
					//$imei = substr($array[2],6,15);
					$imei = 0;
					 $megatrama = preg_replace('/^\*F8,[0-9]{15},/', '', $array[2]);
					$tramas = array();
					if(strstr($array[2],'$') == FALSE) $tramas[0] = $megatrama;
					else $tramas = explode('F8',$megatrama); 			
					
					$qidvehi=$db->sql_query("SELECT id as id_vehi FROM public.vehiculos WHERE placa = '" . $array[0] . "'");	
				
					if(!$equipo=$db->sql_fetchrow($qidvehi)){
						$id_vehi='???';
						error_log("No tiene vehiculo asociado :: " . $array[0]);
					}else{ 
					$id_vehi = $equipo["id_vehi"];
					}
					
					echo "IDVEHI::>".$id_vehi."\n";
					for($j=0;$j<sizeof($tramas);$j++){
						if(!isset($vehiculos)) $vehiculos=array();
						$arreglo = interpretar_trama_spF8($tramas[$j],$imei,$id_vehi);
						insertar_registro_gps_F8($arreglo);
						socket_write($client[$i]['sock'], chr(1));
					}
				}
			}
		}
	}
} // end while
// Close the master sockets
socket_close($sock);
?>
