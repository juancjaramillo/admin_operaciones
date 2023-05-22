<?
include("../application.php");

require_once(dirname(__FILE__) . "/buscar_direccion.php");
$qid=$db->sql_query("SELECT cl.*,mun.codigo FROM clientes cl LEFT JOIN centros c ON cl.id_centro=c.id LEFT JOIN municipios mun ON c.id_municipio=mun.id WHERE cl.the_geom IS NULL ORDER BY cl.id");
$i=0;
while($cliente=$db->sql_fetchrow($qid)){
	echo $i . "\t";
	$coord=array();
//	$ciudad="11001";
	$ciudad=$cliente["codigo"];
	$dir=$cliente["direccion"];
	if(!$results=calcular_direccion($dir, $base="", $error, $ciudad, $barrio="", $descrError)){
		$error="No identificado";
	}
	elseif(is_numeric($results)){
		//			echo "<br>ERRORCODE : " . $results . "<br>";
		$error=$descrError;
		echo "Error:\t" . $dir . "\t" . $results . "\t" . $error . "\n";
/*
		if($results==1) $error="Dirección vacía";
		elseif($results==2) $error="No se puede traducir la dirección";
		elseif($results==3) $error="La entidad no existe en los arcos";
		elseif($results==4) $error="Es una avenida, pero no está en la base de datos";
		elseif($results==5) $error="Dirección extrapolada";
		elseif($results==6) $error="Fuera de cobertura";
		elseif($results==7) $error="Hay más de un arco con esa dirección, y no se proporcionó un barrio.";
		elseif($results==8) $error="Hay más de un arco con esa dirección, y el barrio no se encontró.";
		elseif($results==9) $error="Hay más de un arco con esa dirección, y el barrio existe, pero ninguno de los arcos se cruza con el barrio.";
*/
	}
	else{
		echo "OK:\t" . $dir . "\t" . $results->x . "\t" . $results->y . "\n";
		$qUpdate=$db->sql_query("UPDATE clientes SET the_geom=GeometryFromText('POINT(" . $results->x . " " . $results->y . ")',4326) WHERE id='$cliente[id]'");
	}
	$i++;
}
?>
