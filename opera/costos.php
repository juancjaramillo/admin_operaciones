<?
include("../application.php");
include($CFG->dirroot."/templates/header_popup.php");


$mode=nvl($_POST["mode"],"");

switch(nvl($mode)){

	case "subir_datos":
		subir_datos($_POST);
	break;

	default:
		form();
	break;



}

function subir_datos($frm)
{
	global $CFG, $db, $ME;

	$servicios = array(1=>1, 2=>2, 3=>2, 4=>2, 5=>2, 6=>2);
	$file = $_FILES["archivo"]["tmp_name"];
	$num = 0;

	$fp=fopen($file,"r");
	while(($data=fgetcsv($fp,1000,";"))!=FALSE)
	{
		$dx = explode(",",$data[0]);
		if(is_numeric($dx[0]))
		{
			$meses = array_slice($dx, 2); 		
			$idVar = $dx[0];
			for($i=0 ; $i<12; $i++)
			{
				if(trim($meses[$i]) != "")
				{
					$j = $i+1;
					$fecha = strftime("%Y-%m",strtotime($frm["anio"]."-".$j."-01")) ;
					$db->sql_query("INSERT INTO costos (id_centro, id_servicio, id_variable_informe, fecha, valor ) VALUES ('".$frm["id_centro"]."', ".nvl($servicios[$idVar], 'null').", '".$idVar."', '".$fecha."', '".$meses[$i]."')");
					$num++;
				}
			}
		}
	}
?>
<table width="100%">
	<tr>
		<td valign="top">
			<form  class="form">
			<table width="50%" cellpadding="5" cellspacing="3" align="center">
				<tr>
					<td align = "center">
						<table width="100%" >
							<tr>
								<td align="center" height=100 style="font:bold 14px Verdana, Arial, Helvetica, sans-serif;">COSTOS DEL SERVICIO</td>
							</tr>
							<tr>
								<td align="center" >
									Se ha subido exitosamente el archivo "<?=$_FILES["archivo"]["name"]?>".<br />
									Se han insertado <?=$num?> registros.
								</td>
							</tr>
							<tr>
								<td align="center" valign="bottom" height=80 > <input type="button" class="boton_verde" value="Volver" onclick="window.location.href='<?=$CFG->wwwroot?>/opera/costos.php'"/> </td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>	
<?
}



function form()
{
	global $CFG, $db, $ME;

	$user=$_SESSION[$CFG->sesion]["user"];
?>
<table width="100%">
	<tr>
		<td valign="top">
			<form name="entryform" action="<?=$ME?>" method="POST" enctype="multipart/form-data" onSubmit="return revisar()" class="form">
			<input type="hidden" name="mode" value="subir_datos">
			<table width="50%" cellpadding="5" cellspacing="3" align="center">
				<tr>
					<td align = "center">
						<table width="100%" >
							<tr>
								<td align="center" height=100 style="font:bold 14px Verdana, Arial, Helvetica, sans-serif;">COSTOS DEL SERVICIO</td>
							</tr>
						</table>

						<table width="100%" border=1 bordercolor="#7fa840" align="right">
							<tr>
								<td align='right' width='30%'>Centro : </td>
								<td>
									<select  name='id_centro'  >
										<option value='%'>Seleccione...</option>
									<?
										$qidCn = $db->sql_query("SELECT id, centro 
											FROM centros WHERE id IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') 
											ORDER BY centro");
										while($cn = $db->sql_fetchrow($qidCn)){
											echo '<option value="'.$cn["id"].'">'.$cn["centro"].'</option>';
										}
									?>
									</select>
								</td>
							</tr>
							<tr>
								<td align='right' width='30%'>Año : </td>
								<td>
									<select  name='anio'  >
										<option value='%'>Seleccione...</option>
										<option value='2012'>2012</option>
										<option value='2013'>2013</option>										
										<option value='2014'>2014</option>
										<option value='2015'>2015</option>
										<option value='2016'>2016</option>
										<option value='2017'>2017</option>
										<option value='2018'>2018</option>
										<option value='2019'>2019</option>
									</select>
								</td>
							</tr>
							<tr>
								<td align='right' width='30%'>Archivo : </td>
								<td><input type='file' name='archivo' size='20' ></td>
							</tr>
						</table>
						<table width="100%" >
							<tr>
								<td align="center" valign="center" height=50 > <input type="submit" class="boton_verde" value="Aceptar"/> </td>
							</tr>
						</table>
						<table width="100%" >
							<tr>
								<td align="left" valign="bottom" height=50 >NOTAS:  
									<ul>
										<li>&middot;  Recuerde que es un archivo .csv &nbsp;&nbsp;<a href="<?=$CFG->wwwroot?>/opera/bajar_costos_servicio.php">Bajar formato</a> </li>
										<li>&middot;  No se debe borrar ninguna columna o fila.  Si la casilla está vacía, el sistema no actualizará el dato.</li>
									</ul>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>	

<script type="text/javascript">

function revisar(){

	if(document.entryform.id_centro.value =='%'){
		window.alert('Por favor seleccione: Centro');
		document.entryform.id_centro.focus();
		return(false);
	}

	if(document.entryform.anio.value =='%'){
		window.alert('Por favor seleccione: Año');
		document.entryform.anio.focus();
		return(false);
	}

	if(document.entryform.archivo.value.replace(/ /g, '') ==''){
		window.alert('Por favor seleccione: Archivo');
		document.entryform.archivo.focus();
		return(false);
	}

	return true;
}

</script>
<?}

?>
