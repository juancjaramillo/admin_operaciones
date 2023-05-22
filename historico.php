<?
include("application.php");
$CFG->pageTitle="Histórico";
include("templates/header_popup.php");
$frm=$_GET;
echo "XXX".$frm["fecha_desde"];
if(!isset($frm["detalles"])) $frm["detalles"]=0;
if(!isset($frm["fecha_desde"]) || $frm["fecha_desde"]=="") $frm["fecha_desde"]=date("Y-m-d H:i:s",strtotime("-1 hours"));
if(!isset($frm["fecha_hasta"]) || $frm["fecha_hasta"]=="") $frm["fecha_hasta"]=date("Y-m-d H:i:s");
?>
<form name="entryform" method="GET" action="<?=$ME?>">
<input type="hidden" name="id_vehi" value="<?=$frm["id_vehi"]?>">
<input type="hidden" name="detalles" value="<?=$frm["detalles"]?>">
<table width="100%" border="0">
<tr>
<td align="right">Desde:</td><td><input size="20" type="text" name="fecha_desde" value="<?=$frm["fecha_desde"]?>"></td>
<td align="right">Hasta:</td><td><input type="text" name="fecha_hasta" value="<?=$frm["fecha_hasta"]?>"></td>
<?if($frm["detalles"]==1){?>
	<td align="right">Velocidad:</td>
	<td>
		<select name="operador">
			<option value=">">&gt;</option>
			<option value="<">&lt;</option>
			<option value="=">=</option>
		</select>
		<input type="text" name="velocidad" size="3" value="<?=nvl($frm["velocidad"])?>">
	</td>
<?}?>
<td align="right">
	<input type="Submit" value="Aceptar">
	<input type="button" value="Ver en el mapa" onClick="ver_en_mapa();">
	<input type="button" value="Cerrar" onClick="if(window.opener && window.opener.focus) window.opener.focus();window.close();">
</td>
</tr>
</table>
<table><tr><td>
<?
if($frm["detalles"]==0){
	$href=hallar_querystring("detalles",1,$_GET);
	$title="Detalles";
}
else{
	$href=hallar_querystring("detalles",0,$_GET);
	$title="Resumen";
}
?>
<a href="<?=$href?>"><?=$title?></a>
</td></tr></table>
</form>
<?
//$strQuery="SELECT * FROM vehiculos WHERE idgps='$frm[id_vehi]'";
$strQuery="SELECT * FROM vehiculos WHERE placa='$frm[id_vehi]'";
$qid=$db->sql_query($strQuery);
$inicio=microtime();
$vehiculo=$db->sql_fetchrow($qid);
echo "<table width=\"100%\" border=\"1\"><tr>";
echo "<th>Vehículo: $vehiculo[codigo] / $vehiculo[placa]</th>";
echo "</tr></table>";
//preguntar($vehiculo);
$fecha_desde=date("Y-m-d H:i:s",strtotime("+ 0 hours",strtotime($frm["fecha_desde"])));
$fecha_hasta=date("Y-m-d H:i:s",strtotime("+ 0 hours",strtotime($frm["fecha_hasta"])));
if($frm["detalles"]==1 && nvl($frm["velocidad"])!="") $condicion_velocidad="gps.velocidad" . $frm["operador"] . "'" . $frm["velocidad"] . "'";
else $condicion_velocidad="true";
$strQuery="
	SELECT (gps.tiempo) as tiempo, gps.rumbo, gps.velocidad, ev.nombre as evento, gps.hrposition
	FROM gps_vehi gps LEFT JOIN eventos ev ON gps.evento=ev.codigo
	WHERE gps.tiempo>='$fecha_desde' AND gps.tiempo<='$fecha_hasta' AND gps.id_vehi='$vehiculo[id]' AND $condicion_velocidad
	ORDER BY gps.id
";

echo $strQuery;
$qid=$db->sql_query($strQuery);
$arreglo=array();
$i=0;
$iAnterior=0;
$offset=0.0025;
$ciudad_anterior="";
$pos_anterior="";
while($registro=$db->sql_fetchrow($qid)){
	if($registro["hrposition"]!="") $posicion=$registro["hrposition"];
	else $posicion="SIN GPS";

	if($frm["detalles"]==1 || $i==0 || $posicion!=$pos_anterior){
		if($frm["detalles"]==0) $arregloReg["Desde"]=$registro["tiempo"];
		else{
			$arregloReg["Tiempo"]=$registro["tiempo"];
			$arregloReg["Evento"]=$registro["evento"];
			$arregloReg["Velocidad"]=$registro["velocidad"];
		}
		if($frm["detalles"]==0) $arregloReg["Hasta"]="";
//		$arregloReg["Ciudad"]=$ciudad;
		$arregloReg["Posición"]=$posicion;
		$arreglo[$i]=$arregloReg;
		$i++;
//		$ciudad_anterior=$ciudad;
		$pos_anterior=$posicion;
	}
	if($frm["detalles"]==0) $arreglo[$i-1]["Hasta"]=$registro["tiempo"];
	$tiempo=$registro["tiempo"];
}
echo "\n";
echo "<table width=\"100%\" border=\"1\">\n";
if(sizeof($arreglo)!=0){
	$i=0;
	$arr=$arreglo[$i];
	echo "<tr>";
	foreach($arr AS $key=>$val){
		echo "<th>$key</th>";
	}
	echo "</tr>\n";
	for($i=0;$i<sizeof($arreglo);$i++){
		$arr=$arreglo[$i];
		echo "<tr>";
		foreach($arr AS $key=>$val){
			echo "<td>$val</td>";
		}
		echo "</tr>\n";
	}
}
echo "</table>\n";
echo calcular_tiempo_ejecucion($inicio);



function calcular_tiempo_ejecucion($inicio){
  $pos=strpos($inicio," ");
  $microsegundos=substr($inicio,0,$pos);
  $segundos=substr($inicio,$pos);
  $inicio=$segundos+$microsegundos;


  $final=microtime();
  $pos=strpos($final," ");
  $microsegundos=substr($final,0,$pos);
  $segundos=substr($final,$pos);
  $final=$segundos+$microsegundos;
  $diferencia=$final-$inicio;
  $minutos=floor($diferencia/60);

  $segundos=$diferencia%60;

  $microsegundos=$diferencia-floor($diferencia);
  $segundos=$segundos+$microsegundos;
  return("$minutos:$segundos");

}


?>
<script>
	function ver_en_mapa(){
		if(!window.opener || !window.opener.showGPS){
			window.alert("Error:No se pudo conectar con la ventana principal.");
			return;
		}
		window.opener.showGPS(<?=$vehiculo["id"]?>,'<?=$vehiculo["codigo"]?>',document.entryform.fecha_desde.value,document.entryform.fecha_hasta.value);
		if(window.opener.focus) window.opener.focus();
		window.close();
	}
</script>

