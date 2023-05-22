<?
include("../application.php");
include("funciones_comunes.php");
$CFG->array_centros=array(4);//Cartagena
require($CFG->common_libdir . "/php-excel-reader/excel_reader2.php");
print_r($_SERVER["argv"]);
if(!isset($_SERVER["argv"][1])) die("No viene el archivo de excel.\n");
$file=$_SERVER["argv"][1];
if(!file_exists($file)) die("No existe el archivo [$file].\n");

$data = new Spreadsheet_Excel_Reader($file,false);
$filas=$data->rowcount();
$columnas=$data->colcount();
echo "Filas=$filas\n";
echo "Columnas=$columnas\n";
for($col=1;$col<=$columnas;$col++){
	echo $col . "\t";
}
$started=false;
//$filas=50;
for($row=0;$row<$filas;$row++){
	$vacio=true;
	$arrayFila=array();
	for($col=1;$col<=$columnas;$col++){
		if($data->val($row,$col)!="") $vacio=false;
		array_push($arrayFila,$data->val($row,$col));
	}
	if(!$vacio && $started) evaluar($arrayFila);
	if(preg_match("/^[ií]tem$/i",$arrayFila[0])) $started=true;
	if(!$vacio) echo implode($arrayFila,"\t") . "\n";
}

echo "\n";

function evaluar($item){
	GLOBAL $CFG,$db;

	$elemento["codigo"]=trim($item[0]);
	$elemento["elemento"]=trim($item[1]);
	$elemento["id_unidad"]=13;//Unidades, porque por ahora no viuenen las unidades en el archivo.
	$elemento["tipoe"]=tipo(trim($item[2]));
	$strSQL="
		SELECT id FROM mtto.elementos
		WHERE codigo='$elemento[codigo]' AND elemento='$elemento[elemento]' AND id_unidad='$elemento[id_unidad]'
			AND id IN (SELECT id_elemento FROM mtto.elementos_centros WHERE id_centro IN(" . implode($CFG->array_centros,",") . "))
	";
	if(!$elemento["id"]=$db->sql_field($strSQL)){
		$elemento["id"]=$db->sql_insert("mtto.elementos",$elemento);
		foreach($CFG->array_centros AS $j => $id_centro){
			$qInsert=$db->sql_query("INSERT INTO mtto.elementos_centros (id_elemento,id_centro) VALUES ('$elemento[id]','$id_centro')");
		}
	}
	else{//Verificar vínculo con mtto.elementos_centros
		$qUpdate=$db->sql_query("UPDATE mtto.elementos SET tipoe='$elemento[tipoe]' WHERE id='$elemento[id]'");
		foreach($CFG->array_centros AS $j => $id_centro){
			if(!$resultado=$db->sql_row("SELECT * FROM mtto.elementos_centros WHERE id_elemento='$elemento[id]' AND id_centro='$id_centro'")){
				$qInsert=$db->sql_query("INSERT INTO mtto.elementos_centros (id_elemento,id_centro) VALUES ('$elemento[id]','$id_centro')");
			}
		}
	}
	$valor=trim($item[6]);
	if(preg_match("/\\..*,/",$valor)){
		$valor=str_replace(".","",$valor);
		$valor=str_replace(",",".",$valor);
	}
	if(preg_match("/,.*\\./",$valor)){
		$valor=str_replace(",","",$valor);
	}
	$cantidad=trim($item[5]);
	$valor=$valor/$cantidad;
	verificar_existencias($elemento["id"],"",trim($item[4]),trim($item[3]),$cantidad,$valor);
//	print_r($elemento);
}
?>
