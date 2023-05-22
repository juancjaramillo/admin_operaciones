<?
include(dirname(__FILE__) . "/../application.php");
if(!isset($_SERVER["argv"][1]) || !file_exists($_SERVER["argv"][1])) die("No se encuentra el archivo.\n");
$archivo=$_SERVER["argv"][1];
if(
	!isset($_SERVER["argv"][2]) ||
	!($centro=$db->sql_row("SELECT * FROM centros WHERE centro ILIKE '" . $_SERVER["argv"][2] . "'"))
){
	die("No se encuentra el centro.\n");
}
if(
	!isset($_SERVER["argv"][3]) ||
	!($proveedor=$db->sql_row("SELECT * FROM llta.proveedores WHERE id='" . $_SERVER["argv"][3] . "'"))
){
	echo "No se encuentra el proveedor.\nDebe escoger uno de los siguientes:\n";
	$qid=$db->sql_query("SELECT * FROM llta.proveedores WHERE id IN (SELECT id_proveedor FROM llta.proveedores_centros WHERE id_centro='$centro[id]') ORDER BY id");
	while($proveedor=$db->sql_fetchrow($qid)){
		echo $proveedor["id"] . ") " . $proveedor["razon"] . "\n";
	}
	die("\n");
}
$handle = fopen($archivo, "r");
$placa="";
$codigo="";
$i=0;
$j=0;
while(($data = fgetcsv($handle, 10000, "\t")) !== FALSE) {
	foreach($data AS $key => $val) $data[$key]=trim($val);
	if((preg_match("/[a-z]{3}-?[0-9]{3}/i",$data[0]) && !vacio($data,2,10)) || ($placa!="" && !vacio($data,2,20))){
		if(preg_match("/([a-z]{3})-?([0-9]{3})/i",$data[0],$matches)){
			$placa=trim(strtoupper($matches[1])) . "-" . $matches[2];
			$codigo=trim($data[1]);
		}
		$data[0]=$placa;
		$data[1]=$codigo;
		$numero=$data[2];
		if(!$llanta=$db->sql_row("SELECT * FROM llta.llantas WHERE numero='$numero'")){
			agregar_llanta($data);
		}
		$i++;
	}
	else{
//		print_r(implode("|",$data) . "\n");
	}
	$j++;
	echo "\r" . $j;
}
echo "\n" . $i . "\n";

function vacio($arreglo,$desde,$hasta){
	for($i=$desde;$i<=$hasta;$i++){
		if(trim($arreglo[$i])==""){
//			echo $i . "=> vacío\n";
			return(true);
		}
	}
	return(false);
}

function agregar_llanta($arreglo){
	GLOBAL $db, $centro, $proveedor;
	if(!$vehiculo=$db->sql_row("SELECT * FROM vehiculos WHERE placa='$arreglo[0]' AND codigo='$arreglo[1]'")){
		echo $arreglo[0] . " => " . $arreglo[1] . " :: No existe.\n";
//		print_r($arreglo);
		return(false);
	}
	$llanta["numero"]=$arreglo[2];
	$llanta["posicion"]=$arreglo[3];
	$llanta["id_proveedor"]=$proveedor["id"];
	$llanta["fecha_compra"]=preg_replace('/^([0-9]*)[\/ ]([0-9]*)[\/ ]([0-9]*)$/',"$3-$2-$1",$arreglo[8]);
//	$llanta["dot"]=$arreglo[];
//	$llanta["matricula"]=$arreglo[];
	$llanta["id_vehiculo"]=$vehiculo["id"];
	$llanta["km"]=preg_replace("/[.,]/","",$arreglo[11]);
	$llanta["id_centro"]=$centro["id"];
	$llanta["vida"]=$arreglo[6];
	$llanta["disenio"]=$arreglo[7];
	$llanta["id_estado"]="1";//Montada
	if($arreglo[3]==1 || $arreglo[3]==2) $llanta["id_tipo_llanta"]=1;//Direccional
	else $llanta["id_tipo_llanta"]=2;//De tracción
	if(!$marca=$db->sql_row("SELECT * FROM llta.marcas WHERE marca='$arreglo[4]'")){
		$marca["marca"]=$arreglo[4];
		$marca["id"]=$db->sql_insert("llta.marcas",$marca);
	}
	if(!$dimension=$db->sql_row("SELECT * FROM llta.dimensiones WHERE id_marca='$marca[id]' AND dimension='$arreglo[5]'")){
		$dimension["id_marca"]=$marca["id"];
		$dimension["dimension"]=$arreglo[5];
		$dimension["id"]=$db->sql_insert("llta.dimensiones",$dimension);
	}
	$llanta["id_dimension"]=$dimension["id"];
	$llanta["id"]=$db->sql_insert("llta.llantas",$llanta);
	revisar_movimientos($llanta["id"],$arreglo,$vehiculo["id"]);
}

function revisar_movimientos($id_llanta,$arreglo,$id_vehiculo){
	GLOBAL $db, $centro, $proveedor;

	$mov["id_llanta"]=$id_llanta;
	$mov["fecha"]=preg_replace('/^([0-9]*)[\/ ]([0-9]*)[\/ ]([0-9]*)$/',"$3-$2-$1",$arreglo[8]);
	$mov["id_tipo_movimiento"]=4;//Ingreso
	$mov["id_vehiculo"]="";
	$mov["posicion"]="";
	$mov["km"]="0";
//	$mov["horas"]=$arreglo[];
	$mov["prof_uno"]=$arreglo[13];
	$mov["prof_dos"]=$arreglo[13];
	$mov["prof_tres"]=$arreglo[13];
	$mov["costo"]=str_replace(".","",$arreglo[19]);
	$db->sql_insert("llta.movimientos",$mov);
	$mov["id_tipo_movimiento"]=5;//Montaje
	$mov["id_vehiculo"]=$id_vehiculo;
	$mov["posicion"]=$arreglo[3];
	$mov["km"]=preg_replace("/[,.]/","",$arreglo[10]);
	$db->sql_insert("llta.movimientos",$mov);
	$mov["id_tipo_movimiento"]=2;//Inspección
	$mov["fecha"]=preg_replace('/^([0-9]*)[\/ ]([0-9]*)[\/ ]([0-9]*)$/',"$3-$2-$1",$arreglo[9]);
	$mov["prof_uno"]=$arreglo[14];
	$mov["prof_dos"]=$arreglo[14];
	$mov["prof_tres"]=$arreglo[14];
	$mov["km"]=preg_replace("/[,.]/","",$arreglo[11]);
	$db->sql_insert("llta.movimientos",$mov);

}
?>

