<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
  <title>Calendario</title>
  <link rel="STYLESHEET" type="text/css" href="estilo.css">
  <script>

    function devuelveFecha(){
      //Se encarga de escribir en el formulario adecuado los valores seleccionados
      //tambi� debe cerrar la ventana del calendariou

			var formulario_destino = '<?echo $_GET['formulario']?>'
      var campo_destino = '<?echo $_GET['nomcampo']?>'

			if(document.form_hora.hora.value == "" || document.form_hora.minuto.value == "")
				window.close();
			else{
	      eval ("opener.document." + formulario_destino + "." + campo_destino + ".value='" + document.form_hora.hora.value + ":" + document.form_hora.minuto.value + ":00'")
	      window.close()
			}
    }
  </script>
</head>


<form name="form_hora">
<?

$horaActual = date('H');
$minutoActual = date('i');
echo "<center><font size='3pt' color='red'>Escoja la Hora</font></center><br>";
echo '<table align="center" cellspacing="2" cellpadding="2" border="0" class=tform>
				<tr>
					<td align="center" valign="top">
						Hora : <select name=hora>';
							for($i=0;$i<=23;$i++)
							{
								$selected ="";
								if(strlen($i)==1) $hora="0".$i; else $hora=$i;
								if($horaActual==$hora) $selected = " selected ";
								echo '<option value="'.$hora.'"'.$selected.'>'.$hora.'</option>';
							}
echo				'</select>
					</td>
					<td align="center" valign="top">
						Minuto : <select name=minuto>';
						for($i=0;$i<=59;$i++)
							{
								$selected ="";
								if(strlen($i)==1) $minuto="0".$i; else $minuto=$i;
								if($minutoActual==$minuto) $selected = " selected ";
								echo '<option value="'.$minuto.'"'.$selected.'>'.$minuto.'</option>';
							}
 echo        '</select>
					</td>
				</tr>
  			</tr>
				<tr>
					<td colspan="2" align="center">
						<input type="button" class="buscador3" value="Aceptar" onClick=devuelveFecha()>
					</td>
				</tr>
			</table> ';
?>

</form>
<body>
