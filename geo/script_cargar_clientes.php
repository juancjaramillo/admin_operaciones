<?
include("../application.php");
include($CFG->modulesdir . "/clientes.php");
print_r($_SERVER["argv"]);
if(!isset($_SERVER["argv"][1])){
	echo "El primer parámetro debe ser un archivo.\n";
	die();
}
if(!file_exists($_SERVER["argv"][1])){
	echo "El archivo " . $_SERVER["argv"][1] . " no existe.\n";
	die();
}
$csvFile=$_SERVER["argv"][1];
$id_centro=$_SERVER["argv"][2];
$arrayCentros=array();
$qid=$db->sql_query("SELECT * FROM centros ORDER BY id");
while($centro=$db->sql_fetchrow($qid)){
	$arrayCentros[]=$centro["id"];
}
if(!isset($_SERVER["argv"][2]) || !in_array($id_centro,$arrayCentros)){
	$db->sql_rowseek(0,$qid);
	echo "El segundo parámetro debe ser el id del centro:\n";
	while($centro=$db->sql_fetchrow($qid)){
		echo $centro["id"] . "=>" . $centro["centro"] . "\n";
		$arrayCentros[]=$centro["id"];
	}
	die();
}
$municipio=$db->sql_field("SELECT mun.codigo FROM centros c LEFT JOIN municipios mun ON c.id_municipio=mun.id WHERE c.id='$id_centro'");
require_once(dirname(__FILE__) . "/buscar_direccion.php");
$handle = fopen($csvFile, "r");
$cont=0;
$data = fgetcsv($handle, 1000, "|");//Primera línea de encabezado
while (($data = fgetcsv($handle, 1000, "|")) !== FALSE) {
	if($cont>=0){
		echo $cont . "\t";
		foreach($data as $i=>$val) $data[$i]=trim($val);
		$cliente=array();
		$cliente["id_centro"]=$id_centro;
		$cliente["codigo"]=$data[0];
		$cliente["producto"]=$data[1];
		$cliente["plan_facturacion"]=$data[2];
		$cliente["nombre"]=$data[4];
		$cliente["direccion"]=$data[3];
		$cliente["id_categoria"]=$data[8];
		$cliente["id_subcategoria"]=$data[10];
		$cliente["ciclo"]=$data[11];
		$cliente["operador"]=$data[14];
		$cliente["estado"]=$data[15];
		$cliente["telefono"]=$data[16];
		$cliente["unidadeshab"]=$data[17];
		$cliente["produccion"]=str_replace(",",".",$data[18]);
		$cliente["fecha"]=date("Y-m-d H:i:s");
		if($cliente["nombre"]=="") $cliente["nombre"]="-";

		$dir=$cliente["direccion"];

		if(preg_match("/^AV? /",$dir)) $dir=preg_replace("/^AV? /","AK ",$dir);
		if(preg_match("/^C .* NA .*/",$dir)){
			$dir=preg_replace("/^(C .*) NA (.*)$/","\$1 \$2 NORTE",$dir);
			echo "**\t";
		}
		elseif(preg_match("/^AK .* \*?N .*/",$dir)){
			$dir=preg_replace("/^(AK .*) \*N (.*)$/","\$1 NORTE \$2",$dir);
			echo "**\t";
		}

		if(!$results=calcular_direccion($dir, $base="", $error, $municipio, $barrio="", $descrError)){
			$error="No identificado";
		}
		elseif(is_numeric($results)){
			$error=$descrError;
			echo "Error:\t" . $dir . "\t" . $results . "\t" . $error . "\n";
			$cliente["the_geom"]="";
		}
		else{
			echo "OK:\t" . $cliente["direccion"] . "\t" . $results->x . "\t" . $results->y . "\n";
			$cliente["the_geom"]="GeomFromEWKT('SRID=4326;POINT(" . $results->x . " " . $results->y . ")')";
		}
		$entidad->loadValues($cliente);
		$cliente["id"]=$entidad->insert();
	}
	$cont++;
}

?>
