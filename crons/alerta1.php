<?
echo "<pre>";
print_r($_POST);
print_r($_GET);
echo "</pre>"; 
error_reporting(E_ALL);
ini_set("display_errors", 1);

include(dirname(__FILE__) . "/../application.php");
ini_set("error_log",dirname(__FILE__) . "/log_alertas.log");
$ME = qualified_me();
echo $ME;
session_regenerate_id();

$qCorreos=$db->sql_query("SELECT DISTINCT trim(per.email) AS email, pc.id_centro, centros.centro
	FROM personas_centros pc 
	LEFT JOIN personas per ON pc.id_persona=per.id
	LEFT JOIN centros  on pc.id_centro=centros.id
	WHERE per.id_cargo IN(91) AND per.id_estado<>3
	group by  trim(per.email), pc.id_centro,centros.centro
	order by pc.id_centro");

while($correos=$db->sql_fetchrow($qCorreos)){

	$strMail1= " 
	El �o periodo en que se cargo la informaci�orresponde al mes
	De acuerdo a las pol�cas de la compa�el plazo m�mo para la entrega de dicha informaci�s el 16 de cada mes para la informaci�el mes anterior.
			";
	Print_r($correos);
	echo $strMail;
	$cabeceras = 'From: AIDA <aida@promoambientaldistrito.com>' . '\r\n';
	error_log("Enviando correo a " . $correos["email"]);
	mail($correos["email"],"Alerta Autom�ca de AIDA",$strMail,$cabeceras);
}

?>
