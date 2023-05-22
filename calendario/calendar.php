<?
//require_once("../application.php");
//$_GET = $HTTP_GET_VARS;
//$_POST = $HTTP_POST_VARS;
///////////////////////////////////////////////////////////////////////////////////////////////
//Libreria para mostrar un calendario y obtener una fecha
//
//La página que llame a esta libreria debe contener un formulario con tres campos donde se introducirá el día el mes y el año que se desee
//Para que este calendario pueda actualizar los campos de formulario correctos debe recibir varios datos (por GET)
//formulario (con el nombre del formulario donde estan los campos
//dia (con el nombre del campo donde se colocará el día)
//mes (con el nombre del campo donde se colocará el mes)
//ano (con el nombre del campo donde se colocará el año)
///////////////////////////////////////////////////////////////////////////////////////////////
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>Calendario</title>
	<link rel="STYLESHEET" type="text/css" href="estilo.css">
	<script>
		function devuelveFecha(dia,mes,ano){
      //Se encarga de escribir en el formulario adecuado los valores seleccionados
      //también debe cerrar la ventana del calendario
      var formulario_destino = '<?echo $_GET['formulario']?>'

      var campo_destino = '<?echo $_GET['nomcampo']?>'

      //meto el dia^M
      if(mes < 10)
        mes = "0"+mes;
      if(dia < 10)
        dia = "0"+dia;
      if(dia == 0 && mes == 0 && ano == 0)
        eval ("opener.document." + formulario_destino + "." + campo_destino + ".value=''")
      else
        eval ("opener.document." + formulario_destino + "." + campo_destino + ".value='" + ano + "-" + mes + "-" + dia + "'")
      window.close()
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
$parametros_formulario = "formulario=" . $_GET["formulario"] . "&nomcampo=" . $_GET["nomcampo"];
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
	
formularioCalendario($mes,$ano);
mostrar_calendario($mes,$ano);
?>
</div>
</body>
</html>
