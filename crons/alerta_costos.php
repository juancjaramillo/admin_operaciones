<?
include(dirname(__FILE__) . "/../application.php");
ini_set("error_log",dirname(__FILE__) . "/log_alertas.log");


$periodo= date("Y-m", strtotime("-1 month"));

$strMail= "\t 
Cordial Saludo,
		
No se ha cargado la informaci�n de Costos para el periodo '$periodo', Sin esta informaci�n los indicadores Gerenciales no se pueden calcular.
 
De acuerdo a las pol�ticas de la compa�ia el plazo m�ximo para la entrega de dicha informaci�n es el 16 de cada mes para la informaci�n del mes anterior.

";


$qCorreos=$db->sql_query("SELECT DISTINCT trim(per.email) AS email, pc.id_centro, centros.centro
	FROM personas_centros pc 
	LEFT JOIN personas per ON pc.id_persona=per.id
	LEFT JOIN centros  on pc.id_centro=centros.id
	WHERE per.id_cargo IN(91) AND per.id_estado<>3
	group by  trim(per.email), pc.id_centro,centros.centro
	order by pc.id_centro");

while($correos=$db->sql_fetchrow($qCorreos)){
	$qid = $db->sql_query("select distinct(c.id_centro) as id_centro, max(fecha) as ultimocargue
		FROM costos c 
		LEFT JOIN servicios s ON s.id=c.id_servicio
		where c.id_centro = $correos[id_centro] and id_variable_informe=27 and fecha<='$periodo'
		group by c.id_centro");
	while($query = $db->sql_fetchrow($qid))
	{
		$ultimo = $query["ultimocargue"];
		if ($periodo==$ultimo)
		{
			echo("\tSi hay Registros de Costos..\n");
		}
		else{
			echo("\tNo hay Registros de Costos.\n");
			$strMail1= " 
			El �ltimo periodo en que se cargo la informaci�n corresponde al mes '$ultimo'.
			 
			De acuerdo a las pol�ticas de la compa�ia el plazo m�ximo para la entrega de dicha informaci�n es el 16 de cada mes para la informaci�n del mes anterior.

			";
			$strMail2 = $strMail.$strMail1;
			echo $strMail2;
			$cabeceras = 'From: AIDA <aida@promoambientaldistrito.com>' . '\r\n';
			error_log("Enviando correo a " . $correos["email"]);
			mail($correos["email"],"Alerta Autom�tica de AIDA",$strMail2,$cabeceras);
		}
	}	
}
?>
