<?
include("../application.php");

if(!isset($_SESSION[$CFG->sesion]["user"])){
	$errorMsg="No existe la sesión.";
	error_log($errorMsg);
	die($errorMsg);
}


$paged=nvl($_GET["paged"],nvl($_POST["paged"],""));

$nivel_acceso=$_SESSION[$CFG->sesion]["user"]["nivel_acceso"];
$arrPaginas = array();

if(in_array($nivel_acceso,$CFG->permisos["calendario"]))
	$arrPaginas["calendario"] = array("paged"=>"calendario","titulo"=>"Calendario");

if(in_array($nivel_acceso,$CFG->permisos["arbol_rutinas"]))
	$arrPaginas["arbol_rutinas"] = array("paged"=>"arbol_rutinas","titulo"=>"Rutinas");

if(in_array($nivel_acceso,$CFG->permisos["llantas"]))
	$arrPaginas["llantas"] = array("paged"=>"llantas","titulo"=>"Llantas");

if(in_array($nivel_acceso,$CFG->permisos["llantas_desmontadas_informe"]))
	$arrPaginas["llantas_desmontadas_informe"] = array("paged"=>"llantas_desmontadas_informe","titulo"=>"Llantas Desmontadas");

if(in_array($nivel_acceso,$CFG->permisos["listado_hoja_vida_vehiculo"]))
		$arrPaginas["listado_hoja_vida_vehiculo"] = array("paged"=>"listado_hoja_vida_vehiculo","titulo"=>"HV Vehículos");

if(in_array($nivel_acceso,$CFG->permisos["mttoNovedades"]))
		$arrPaginas["novedades"] = array("paged"=>"novedades","titulo"=>"Novedades");

if(in_array($nivel_acceso,$CFG->permisos["Tablero_Control"]))
	$arrPaginas["Tablero_Control"] = array("paged"=>"Tablero_Control","titulo"=>"Tablero Control Preventivos");

switch(nvl($paged)){
	case "arbol_rutinas":
		$link = "/mtto/arbol_rutinas.php";
		break;

	case "inspecciones":
		$link = "/mtto/inspecciones.php";
		break;

	case "llantas":
		$link = "/mtto/llantas.php";
		break;

	case "llantas_desmontadas_informe":
		$link = "/mtto/llantas_desmontadas_informe.php?mode=llantas_desmontadas_informe";
		break;

	case "novedades":
		$link = "/novedades/novedades.php?mode=listado&clase=mtto";
		break;

	case "listado_hoja_vida_vehiculo":
		$link = "/mtto/listado_hoja_vida_vehiculo.php";
		break;

	case "Tablero_Control":
		$link = "/mtto/Tablero_Control.php";
		break;

	default:
		$link = "/mtto/calendario.php";
		break;
}

if($link == "/mtto/calendario.php" && !array_key_exists("calendario", $arrPaginas))
{
	$copia = $arrPaginas;
	$ultimo = array_pop($copia);
	if(count($ultimo) > 0)
	{
		if($ultimo["paged"] == "novedades")
			$link = "/novedades/novedades.php?mode=listado&clase=mtto";
		else
			$link = "/mtto/".$ultimo["paged"].".php";
	}
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Mantenimiento</title>
<style type="text/css">
body {
	margin:0;
	padding:0;
}

.boton{
	background-color:#cae2e8;
	border:solid 1px #fff;
	color:#fff;
	padding: 4px 10px 4px 10px;
	text-decoration: none;
	font:bold 11px Verdana, Arial, Helvetica, sans-serif;
	text-align:center;
} 
.boton:hover {
	background-color:#496e77;
	border:solid 1px #cae2e8;
	color:#fff;
	padding: 4px 10px 4px 10px;
	text-decoration: none;
	font:bold 12px Verdana, Arial, Helvetica, sans-serif;
} 
.boton_active {
	background-color:#496e77;
	border:solid 1px #cae2e8;
	color:#fff;
	padding: 4px 10px 4px 10px;
	text-decoration: none;
	font:bold 12px Verdana, Arial, Helvetica, sans-serif;
} 


</style>

<body>
<table width="100%" height="100%" style="border-spacing:1px 1px; border:solid #fff; ">
	<tr height="35px" bgcolor="#b2d2e1">
		<td valign="center">
			&nbsp;&nbsp;&nbsp;
			<?foreach($arrPaginas AS $pag){
					$class = "boton";
					if(simple_me(strip_querystring($link)) == $pag["paged"].".php")
						$class = "boton_active";
				?>	
				<a target="_parent" class="<?=$class?>" href="<?=$CFG->wwwroot."/frames.php?page=mtto/index&paged=".$pag["paged"]?>" ><?=$pag["titulo"]?></a>&nbsp;
			<?}?>
		</td>
	</tr>
	<tr>
		<td valign="top"><iframe src="<?=$CFG->wwwroot.$link?>" style="border-style: none; width: 100%; height: 100%; scrolling:auto; "></td>
	</tr>
</table>
</body>
</html>
