<?
//require_once("../application.php");
//$_GET = $HTTP_GET_VARS;
//$_POST = $HTTP_POST_VARS;

/****************************************************
	
Libreria para mostrar un calendario y obtener una fecha y una hora
Primero se escoge la fecha normalmente, y luego pasa a un formulario donde se escoge la hora, para luego
mandarla al campo del formulario escogido.

******************************************************/

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Calendario</title>
	<link rel="STYLESHEET" type="text/css" href="estilo.css">
	<script>
		function escogerHora(dia,mes,ano)
		{
			var formulario_destino = '<?echo $_GET['formulario']?>'
			var campo_destino = '<?echo $_GET['nomcampo']?>'
			var mode = '<?echo $_GET["mode"]?>'
			if(mes < 10)
				mes = "0"+mes;
			if(dia < 10)
				dia = "0"+dia;

			ruta= 'hora.php?formulario='+formulario_destino+'&nomcampo='+campo_destino+'&mode='+mode+'&dia='+dia+'&mes='+mes+'&ano='+ano;
			window.location=ruta;
		}

		function resetFecha()
		{
			var formulario_destino = '<?echo $_GET['formulario']?>'
			var campo_destino = '<?echo $_GET['nomcampo']?>'
			eval ("opener.document." + formulario_destino + "." + campo_destino + ".value=''");
			window.close();
		}
		
	</script>
</head>

<body>

<?
//TOMO LOS DATOS QUE RECIBO POR LA url Y LOS COMPONGO PARA PASARLOS EN SUCESIVAS EJECUCIONES DEL CALENDARIO
$parametros_formulario = "formulario=" . $_GET["formulario"] . "&nomcampo=" . $_GET["nomcampo"] ."&mode=".$_GET["mode"];

?>

<div align="center">
<?
require ("calendario2.php");
$tiempo_actual = time();
$dia_solo_hoy = date("d",$tiempo_actual);
if (!$_POST && !isset($_GET["nuevo_mes"]) && !isset($_GET["nuevo_ano"])){
	$mes = date("n", $tiempo_actual);
	$ano = date("Y", $tiempo_actual);
}elseif ($_POST) {
	$mes = $_POST["nuevo_mes"];
	$ano = $_POST["nuevo_ano"];
}else{
	$mes = $_GET["nuevo_mes"];
	$ano = $_GET["nuevo_ano"];
}

mostrar_calendario_hora($mes,$ano);
?>
</div>
</body>
</html>
