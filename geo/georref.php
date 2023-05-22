<?
include("../application.php");
header("Content-type: text/xml");
echo "<?xml version=\"1.0\"  encoding=\"ISO-8859-1\" ?>\n";
if(isset($_GET["direccion"])){

	require_once(dirname(__FILE__) . "/buscar_direccion.php");

		$coord=array();
		$string=$_GET["direccion"];
		if(isset($_GET["ciudad"])) $ciudad=$_GET["ciudad"];
		else $ciudad="11001";
		if(!$results=calcular_direccion($string, $base="", $error, $ciudad, $barrio="", $descrError)){
			$error="No identificado";
		}
		elseif(is_numeric($results)){
			//			echo "<br>ERRORCODE : " . $results . "<br>";
			$error=$descrError;
/*
			if($results==1) $error="Direcci�n vac�a";
			elseif($results==2) $error="No se puede traducir la direcci�n";
			elseif($results==3) $error="La entidad no existe en los arcos";
			elseif($results==4) $error="Es una avenida, pero no est� en la base de datos";
			elseif($results==5) $error="Direcci�n extrapolada";
			elseif($results==6) $error="Fuera de cobertura";
			elseif($results==7) $error="Hay m�s de un arco con esa direcci�n, y no se proporcion� un barrio.";
			elseif($results==8) $error="Hay m�s de un arco con esa direcci�n, y el barrio no se encontr�.";
			elseif($results==9) $error="Hay m�s de un arco con esa direcci�n, y el barrio existe, pero ninguno de los arcos se cruza con el barrio.";
*/
			echo "<errores>\n";
			echo "\t<error>\n";
			echo "\t\t<codigo>$results</codigo>\n";
			echo "\t\t<descripcion>$error</descripcion>\n";
			echo "\t</error>\n";
			echo "</errores>\n";
		}
		else{
			if($results->errorCode=="") $error="Ubicado con precisi�n";
			else $error=$results->errors[$results->errorCode];
			$x=$results->x;
			$y=$results->y;
			echo "<coordenadas>\n";
			echo "\t<coordenada>\n";
			echo "\t\t<x>$x</x>\n";
			echo "\t\t<y>$y</y>\n";
			echo "\t</coordenada>\n";
			echo "</coordenadas>\n";
		}
}
else{
	echo "<errores>\n";
	echo "\t<error>\n";
	echo "\t\t<codigo>1</codigo>\n";
	echo "\t\t<descripcion>No viene la variable con la direcci�n</descripcion>\n";
	echo "\t</error>\n";
	echo "</errores>\n";
}
?>
