
<?
function calcula_numero_dia_semana($dia,$mes,$ano){
	$numerodiasemana = date('w', mktime(0,0,0,$mes,$dia,$ano));
	if ($numerodiasemana == 0) 
		$numerodiasemana = 6;
	else
		$numerodiasemana--;
	return $numerodiasemana;
}

//funcion que devuelve el �ltimo d�a de un mes y a�o dados
function ultimoDia($mes,$ano){ 
    $ultimo_dia=28; 
    while (checkdate($mes,$ultimo_dia + 1,$ano)){ 
       $ultimo_dia++; 
    } 
    return $ultimo_dia; 
} 

function dame_nombre_mes($mes){
	 switch ($mes){
	 	case 1:
			$nombre_mes="Enero";
			break;
	 	case 2:
			$nombre_mes="Febrero";
			break;
	 	case 3:
			$nombre_mes="Marzo";
			break;
	 	case 4:
			$nombre_mes="Abril";
			break;
	 	case 5:
			$nombre_mes="Mayo";
			break;
	 	case 6:
			$nombre_mes="Junio";
			break;
	 	case 7:
			$nombre_mes="Julio";
			break;
	 	case 8:
			$nombre_mes="Agosto";
			break;
	 	case 9:
			$nombre_mes="Septiembre";
			break;
	 	case 10:
			$nombre_mes="Octubre";
			break;
	 	case 11:
			$nombre_mes="Noviembre";
			break;
	 	case 12:
			$nombre_mes="Diciembre";
			break;
	}
	return $nombre_mes;
}

function dame_estilo($dia_imprimir){
	global $mes,$ano,$dia_solo_hoy,$tiempo_actual;
	//dependiendo si el d�a es Hoy, Domigo o Cualquier otro, devuelvo un estilo
	if ($dia_solo_hoy == $dia_imprimir && $mes==date("n", $tiempo_actual) && $ano==date("Y", $tiempo_actual)){
		//si es hoy
		$estilo = " class='hoy'";
	}else{
		$fecha=mktime(12,0,0,$mes,$dia_imprimir,$ano);
		if (date("w",$fecha)==0){
			//si es domingo 
			$estilo = " class='domingo'";
		}else{
			//si es cualquier dia
			$estilo = " class='diario'";
		}
	}
	return $estilo;
}

function mostrar_calendario($mes,$ano,$hora=false,$modo=""){
	global $parametros_formulario;

	if(!$hora)
	{
		$href = "calendar";
		$script = "devuelveFecha";
	}
	else
	{
		$href = "calendarAndHora";
		$script = "escogerHora";
		echo "<font size='3pt' color='red'>1. Escoja la fecha</font><br><br>";
	}

	if($modo != "")
	{
		$href = "calendarWithMode";
		$script = "devuelveFecha";
	}
	
	//tomo el nombre del mes que hay que imprimir
	$nombre_mes = dame_nombre_mes($mes);
	
	//construyo la cabecera de la tabla
	echo "<table width=200 cellspacing=3 cellpadding=2 border=0><tr><td colspan=7 align=center class=tit>";
	echo "<table width=100% cellspacing=2 cellpadding=2 border=0><tr><td style=font-size:10pt;font-weight:bold;color:white>";
	//calculo el mes y ano del mes anterior
	$mes_anterior = $mes - 1;
	$ano_anterior = $ano;
	if ($mes_anterior==0){
		$ano_anterior--;
		$mes_anterior=12;
	}
	echo "<a style=color:white;text-decoration:none href=".$href.".php?$parametros_formulario&nuevo_mes=$mes_anterior&nuevo_ano=$ano_anterior>&lt;&lt;</a></td>";
	   echo "<td align=center class=tit>$nombre_mes $ano</td>";
	   echo "<td align=right style=font-size:10pt;font-weight:bold;color:white>";
	//calculo el mes y ano del mes siguiente
	$mes_siguiente = $mes + 1;
	$ano_siguiente = $ano;
	if ($mes_siguiente==13){
		$ano_siguiente++;
		$mes_siguiente=1;
	}
	echo "<a style=color:white;text-decoration:none href=".$href.".php?$parametros_formulario&nuevo_mes=$mes_siguiente&nuevo_ano=$ano_siguiente>&gt;&gt;</a></td></tr></table></td></tr>";
	echo '	<tr>
			    <td width=14% align=center class=altn>L</td>
			    <td width=14% align=center class=altn>M</td>
			    <td width=14% align=center class=altn>X</td>
			    <td width=14% align=center class=altn>J</td>
			    <td width=14% align=center class=altn>V</td>
			    <td width=14% align=center class=altn>S</td>
			    <td width=14% align=center class=altn>D</td>
			</tr>';
	
	//Variable para llevar la cuenta del dia actual
	$dia_actual = 1;
	
	//calculo el numero del dia de la semana del primer dia
	$numero_dia = calcula_numero_dia_semana(1,$mes,$ano);
	//echo "Numero del dia de demana del primer: $numero_dia <br>";
	
	//calculo el �ltimo dia del mes
	$ultimo_dia = ultimoDia($mes,$ano);
	
	//escribo la primera fila de la semana
	echo "<tr>";
	for ($i=0;$i<7;$i++){
		if ($i < $numero_dia){
			//si el dia de la semana i es menor que el numero del primer dia de la semana no pongo nada en la celda
			echo "<td></td>";
		} else {
			echo "<td align=center><a href='javascript:".$script."($dia_actual,$mes,$ano)'". dame_estilo($dia_actual) .">$dia_actual</a></td>";
			$dia_actual++;
		}
	}
	echo "</tr>";
	
	//recorro todos los dem�s d�as hasta el final del mes
	$numero_dia = 0;
	while ($dia_actual <= $ultimo_dia){
		//si estamos a principio de la semana escribo el <TR>
		if ($numero_dia == 0)
			echo "<tr>";
		echo "<td align=center><a href='javascript:".$script."($dia_actual,$mes,$ano)'". dame_estilo($dia_actual) .">$dia_actual</a></td>";
		$dia_actual++;
		$numero_dia++;
		//si es el u�timo de la semana, me pongo al principio de la semana y escribo el </tr>
		if ($numero_dia == 7){
			$numero_dia = 0;
			echo "</tr>";
		}
	}
	
	//compruebo que celdas me faltan por escribir vacias de la �ltima semana del mes
	for ($i=$numero_dia;$i<7;$i++){
		echo "<td></td>";
	}
	
	echo "</tr>";
	echo "<tr><td colspan='7' align='center'><a href='javascript:resetFecha()' style='color:black;font-size:8pt;text-decoration:none;'>Reset</a></td>
		</tr>";
	echo "</table>";
}	

function mostrar_calendario_hora($mes,$ano)
{
	formularioCalendario($mes,$ano,true);
	mostrar_calendario($mes,$ano,true);
}


function formularioCalendario($mes,$ano,$hora=false,$mode=""){
	global $parametros_formulario;

	if(!$hora)
	{
		$action = "calendar";
	}
	else
	{
		$action = "calendarAndHora";
	}

	if($mode!="")
		$action = "calendarWithMode";
	
echo '
	<br>
	<table align="center" cellspacing="2" cellpadding="2" border="0" class=tform>
	<tr><form action="'.$action.'.php?' . $parametros_formulario . '" method="POST">';
echo '
    <td align="center" valign="top">
		Mes: <br>
		<select name=nuevo_mes onChange="this.form.submit()">
		<option value="1"';
if ($mes==1)
 echo "selected";
echo'>Enero
		<option value="2" ';
if ($mes==2) 
	echo "selected";
echo'>Febrero
		<option value="3" ';
if ($mes==3) 
	echo "selected";
echo'>Marzo
		<option value="4" ';
if ($mes==4) 
	echo "selected";
echo '>Abril
		<option value="5" ';
if ($mes==5) 
		echo "selected";
echo '>Mayo
		<option value="6" ';
if ($mes==6) 
	echo "selected";
echo '>Junio
		<option value="7" ';
if ($mes==7) 
	echo "selected";
echo '>Julio
		<option value="8" ';
if ($mes==8) 
	echo "selected";
echo '>Agosto
		<option value="9" ';
if ($mes==9) 
	echo "selected";
echo '>Septiembre
		<option value="10" ';
if ($mes==10) 
	echo "selected";
echo '>Octubre
		<option value="11" ';
if ($mes==11) 
	echo "selected";
echo '>Noviembre
		<option value="12" ';
if ($mes==12) 
    echo "selected";
echo '>Diciembre
		</select>
		</td>';
echo '		
	    <td align="center" valign="top">
		A&ntilde;o: <br>
		<select name=nuevo_ano onChange="this.form.submit()">';

for ($cont=1900;$cont<$ano+6;$cont++){
	echo "<option value='$cont'";
	if ($ano==$cont) 
   		echo " selected";
   	echo ">$cont";
}
echo '
	</select>
		</td>';
echo '
	</tr>
	<tr>
	    <td colspan="2" align="center"><input type="Submit" class="buscador3" value="[ IR A ESE MES ]"></td>
	</tr>
	</table><br>
	
	<br>
	
	</form>';
}
	
////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funci�n que escribe en la p�gina un fomrulario preparado para introducir una fecha y enlazado con el calendario para seleccionarla comodamente
////////////////////////////////////////////////////////////////////////////////////////////////////////////
function escribe_formulario_fecha_vacio($nombrecampo,$nombreformulario){
	global $raiz;
	echo '
	<INPUT name="'.$nombrecampo.'" value="" size="10">
	<input type=button value=">>" valign="middle" class="buscador3" onclick="muestraCalendario(\''. $raiz.'\',\''. $nombreformulario .'\',\''.$nombrecampo.'\')">
	';	
}
function escribe_formulario($nombrecampo,$nombreformulario){
	global $raiz;
	echo '

	<input type=button value=">>" valign="middle" class="buscador3" onclick="muestraContacto(\''. $raiz.'\',\''. $nombreformulario .'\',\''.$nombrecampo.'\')">
	';	
}
/*function escribe_comentario($nombrecampo,$nombreformulario)
{
global $raiz;
	$link = mysql_connect("localhost","icondb","&c0ndba") or die ("no se ha podido conectar"); 
#Seleccion de la base de datos a utilizar 
mysql_select_db("base_datos") or die("Error al tratar de selecccionar esta base"); 
	$result=mysql_query("SELECT
    fecha_contactado.descripcion,
	fecha_contactado.fecha_contactado
	
  FROM
   empresa empresa,
   contacto contacto,
   fecha_contactado fecha_contactado,
   estado estado,
   empresa_contactado empresa_contactado
   
   

  WHERE
	
	
	empresa.id_empresa like '$nombrecampo' and
	empresa.id_empresa=contacto.id_empresa_fk and
	contacto.id_contacto=empresa_contactado.id_empresa_contactado_fk and
	fecha_contactado.id_descripcion=empresa_contactado.id_contactado_empresa_fk
	
	
	
    	
GROUP BY
fecha_contactado.fecha_contactado

order by
	fecha_contactado.fecha_contactado DESC
	 ",$link);
	 
	while($row = mysql_fetch_array($result) ) { 
	
	
	//echo  '<INPUT name="nombre_contacto" value="'.$row['nombre_contacto'].'"size="25">'; 
   // echo $row['nombre_contacto'];
   $cadena = substr($row['descripcion'],0, 90);
	echo '<td width="501" height="5" colspan="-6" >'.$cadena.'&nbsp;</td>
	<td width="80">'.$row['fecha_contactado'].'</td>';
	
}
}

function escribe_contacto($nombrecampo,$nombreformulario){
	global $raiz;
	$link = mysql_connect("localhost","icondb","&c0ndba") or die ("no se ha podido conectar"); 
#Seleccion de la base de datos a utilizar 
mysql_select_db("base_datos") or die("Error al tratar de selecccionar esta base"); 
	$result=mysql_query("SELECT
   	contacto.nombre_contacto,
	contacto.id_contacto
	FROM
    contacto contacto
    WHERE
	contacto.id_empresa_fk like '$nombrecampo'
	 ",$link);
	 
	while($row = mysql_fetch_array($result) ) { 
	
	
	//echo  '<INPUT name="nombre_contacto" value="'.$row['nombre_contacto'].'"size="25">'; 
   // echo $row['nombre_contacto'];
	echo '<table><td width="160" class="layers2" ><a href="javascript:muestraContacto(\''. $raiz.'\',\''. $nombreformulario .'\',\''.$row['id_contacto'].'\')" class="layers2">'.$row['nombre_contacto'].'</a></td>
	<td>
	
	</td></table>';	
		}
		
		
}

function nuevo_contacto($nombrecampo,$nombreformulario){
	global $raiz;
	
	echo '
	<input type=button value="Nuevo Contacto" valign="middle" class="buscador3" onclick="muestraEditarContacto(\''. $raiz.'\',\''. $nombreformulario .'\',\''. $nombrecampo .'\')">
	';	
}
function licencia($nombrecampo){
	global $raiz;
	
	echo '
	<input type=button value="Ver informaci�n de licencia" valign="middle" class="buscador3" onclick="muestraLicencia(\''. $raiz.'\',\''. $nombrecampo .'\')">
	';	
}

function Descargar($ElFichero){ 
    
    $TheFile = basename($ElFichero); 
         
    header( "Content-Type: application/octet-stream");  
    header( "Content-Length: ".filesize($ElFichero));  
    header( "Content-Disposition: attachment; filename=".$TheFile."");  
    readfile($ElFichero);  
} 

function guardar_archivo($empresa,$contacto,$estado,$ciudad,$fecha_inicio,$fecha_fin,$creacion_inicio,$creacion_fin)
{
$link = mysql_connect("localhost","icondb","&c0ndba") or die ("no se ha podido conectar"); 
#Seleccion de la base de datos a utilizar 
mysql_select_db("base_datos") or die("Error al tratar de selecccionar esta base"); 
 $result=mysql_query("SELECT 
   empresa.id_empresa,
    empresa.nombre_empresa,
	empresa.direccion,
	empresa.telefono,
	empresa.fax, 
	empresa.id_encargado_fk,
	empresa.id_estado_fk,
	empresa.id_sector_fk,
	empresa.fecha_creacion,
	empresa.numero_pc,
	fecha_contactado.descripcion, 
	fecha_contactado.id_descripcion, 
	fecha_contactado.fecha_contactado, 
	contacto.id_contacto,
	contacto.nombre_contacto,
	contacto.correo,
    contacto.cargo_contacto,
	ciudad.nombre_ciudad, 
	estado.nombre_estado, 
	administrador.nombre_administrador 
	
  FROM 
   empresa empresa,
   contacto contacto,
   fecha_contactado fecha_contactado,
   estado estado,
   empresa_contactado empresa_contactado,
   empresa_ciudad empresa_ciudad,
   ciudad ciudad,
   administrador administrador 
   
  WHERE 
	empresa.nombre_empresa like '%$empresa%' and
	contacto.nombre_contacto like '%$contacto%' and
	estado.id_estado like '$estado' and
	ciudad.id_ciudad like '$ciudad' and
	fecha_contactado.fecha_contactado between '$fecha_inicio' and '$fecha_fin' and
	empresa.fecha_creacion between '$creacion_inicio' and '$creacion_fin' and
	empresa.id_empresa=contacto.id_empresa_fk and 
	empresa.id_estado_fk=estado.id_estado and
	contacto.id_contacto=empresa_contactado.id_empresa_contactado_fk and
	empresa.id_empresa=empresa_ciudad.id_empresa_ciudad_fk and
	empresa.id_encargado_fk=administrador.id_administrador and
	fecha_contactado.id_descripcion=empresa_contactado.id_contactado_empresa_fk and
	ciudad.id_ciudad=empresa_ciudad.id_ciudad_empresa_fk  
	
	GROUP BY
     empresa.id_empresa
	 	  
  ORDER BY
	empresa.nombre_empresa 
	
	
	 ",$link); 
	 $shtml="<table border=1>";
$shtml=$shtml."<td>EMPRESA</td><td>CONTACTO</td><td>CARGO</td><td>CORREO</td><td>DESCRIPCION</td><td>FECHA CONTACTADO</td><td>NUMERO PC</td><td>ESTADO</td><td>CIUDAD</td><br></tr>";
	
	 while($row = mysql_fetch_array($result))
	 {
	 
	 $nombre_empresa=$row['nombre_empresa'];
	 $nombre_contacto=$row['nombre_contacto'];
	 $cargo=$row['cargo_contacto'];
	 $direccion=$row['direccion'];
	 $nombre_ciudad=$row['nombre_ciudad'];
	 $nombre_estado=$row['nombre_estado'];
	 $descripcion=$row['descripcion'];
	 $fecha_contactado=$row['fecha_contactado'];
	 $nombre_estado=$row['nombre_estado'];
	 $numero_pc=$row['numero_pc'];
	 $correo=$row['correo'];
	 $shtml=$shtml."<tr>";
	 $shtml=$shtml."<td>$nombre_empresa</td><td>$nombre_contacto</td><td>$cargo</td><td>$correo</td><td>$descripcion</td><td>$fecha_contactado</td><td>$numero_pc</td><td>$nombre_estado</td><td>$nombre_ciudad</td><td><p align=\"right\"></td><br></tr>";
$scarpeta="\\www\www\\"; //carpeta donde guardar el archivo.
//debe tener permisos 775 por lo menos
$nombre_archivo="consulta_BD.xls";
$sfile=$scarpeta.$nombre_archivo; //ruta del archivo a generar
$fp=fopen($sfile,"w");
fwrite($fp,$shtml);
fclose($fp);

	 }
	 echo "<a href='contactos_BD.xls'>a</a>";
//Descargar("\\www\www\\consulta_BD.xls"); 
}


//Descargar("consulta_BD.xls"); 

*/
?>
