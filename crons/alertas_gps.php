<?
error_reporting(E_ALL);
ini_set("display_errors", 1);

include(dirname(__FILE__) . "/../application.php");
ini_set("error_log",dirname(__FILE__) . "/log_alertas.log");
$idcentro =15;
$strMail.= "Cordial Saludo.\n

Esta es una Alerta Automatica del Estado del reporte de los Dispositivos GPS instalados en los Vehiculos de Promoambiental Distrito S.A.S ESP.\n

";

$qVehi=$db->sql_query("SELECT codigo,idgps,placa,id_estado,estado,fecha_entrada_operacion,tiempo
	FROM vehiculos v
	LEFT JOIN estados_vehiculos ev on ev.id=v.id_estado
	WHERE tiene_gps IS TRUE AND TIEMPO<= now()-interval '2 hour' AND v.id_estado<>4 
	order by tiempo desc");
while($vehi=$db->sql_fetchrow($qVehi)){
	$strMail.= "El Vehículo con No. Interno $vehi[codigo] y placa $vehi[placa] con estado $vehi[estado] NO tiene Registros de GPS en las 2 últimas horas, El ultimo Registro fue a las  $vehi[tiempo].\n";
//	
}

Print_r($strMail);
if (isset($strMail)){
	$qCorreos=$db->sql_query(" SELECT DISTINCT trim(per.email) AS email
	FROM personas_centros pc LEFT JOIN personas per ON pc.id_persona=per.id
	WHERE per.id_cargo IN(8,91,23,54,69,89,109,108,112) AND per.id_estado<>3 AND pc.id_centro='$idcentro'");

	if($db->sql_numrows($qCorreos)>0){
		$strMail.="\t


Att.:
Sistema de Informacion AIDA \n";
		#Print_r($strMail["$idcentro"]);
		$cabeceras = 'From: AIDA <aida@promoambientaldistrito.com>' . "\r\n";
		while($correo=$db->sql_fetchrow($qCorreos)){
			error_log("Enviando correo a " . $correo["email"]);
			mail($correo["email"],"Alerta Automática de AIDA",$strMail,$cabeceras);
		}		
	}
}
?>
