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

if(in_array($nivel_acceso,$CFG->permisos["actualizacioneskmyhr"]))
	$arrPaginas[] = array("paged"=>"actualizacioneskmyhr","titulo"=>"Actualizaciones de Kms");

if(in_array($nivel_acceso,$CFG->permisos["movimientos_rec"]))
	$arrPaginas[] = array("paged"=>"movimientos_rec","titulo"=>"Mov. Recolección");

if(in_array($nivel_acceso,$CFG->permisos["movimientos_bar"]))
	$arrPaginas[] = array("paged"=>"movimientos_bar","titulo"=>"Mov. Barrido");

if(in_array($nivel_acceso,$CFG->permisos["novedades_opera"]))
	$arrPaginas[] = array("paged"=>"novedades","titulo"=>"Novedades");

if(in_array($nivel_acceso,$CFG->permisos["listado_hoja_vida_vehiculo"]))
	$arrPaginas[] = array("paged"=>"listado_hoja_vida_vehiculo","titulo"=>"HV Vehículos");

if(in_array($nivel_acceso,$CFG->permisos["micros"]))
	$arrPaginas[] = array("paged"=>"micros","titulo"=>"Rutas");

switch(nvl($paged)){
	
	case "actualizacioneskmyhr":
		$link = "/opera/actualizacioneskmyhr.php";
	break;
	
	case "micros":
		$link = "/opera/micros.php";
	break;

	case "movimientos_rec":
		$link = "/opera/movimientos_rec.php?esquema=rec";
	break;

	case "movimientos_bar":
		$link = "/opera/movimientos_bar.php?esquema=bar";
	break;

	case "novedades":
		$link = "/novedades/novedades.php";
	break;

	case "listado_hoja_vida_vehiculo":
		$link = "/mtto/listado_hoja_vida_vehiculo.php";
	break;

	default:
		$link = "/opera/movimientos_rec.php?esquema=rec";
#$link = "/opera/actualizacioneskmyhr.php";
	break;
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
				<a target="_parent" class="<?=$class?>" href="<?=$CFG->wwwroot."/frames.php?page=opera/index&paged=".$pag["paged"]?>" ><?=$pag["titulo"]?></a>&nbsp;
			<?}?>
		</td>
	</tr>
	<tr>
		<td valign="top"><iframe src="<?=$CFG->wwwroot.$link?>" style="border-style: none; width: 100%; height: 100%; scrolling:auto; "></td>
	</tr>
</table>
</body>
</html>
