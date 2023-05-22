<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/XHTML1/DTD/XHTML1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>AIDA</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script src="<?=$CFG->wwwroot?>/lib/scripts.js" type="text/javascript"></script>
<style type="text/css" media="all">
#globalnav {
	position:relative;
	float:left;
	width:100%;
	padding:0 0 0 1em;
	margin:0;
	list-style:none;
	line-height:1em;
	font:normal 13px verdana,helvetica,arial;
}

#globalnav LI {
	float:left;
	margin:0;
	padding:0;
}

#globalnav A {
	display:block;
	color:#ffffff;
	text-decoration:none;
	font-weight:bold;
	background:#cae2e8;
	margin:0;
	padding:0.25em 1em;
	border-left:1px solid #fff;
	border-top:1px solid #fff;
	border-right:1px solid #aaa;
}

#globalnav A:hover,
#globalnav A:active,
#globalnav A.here:link,
#globalnav A.here:visited {
	background:#496e77;
}

#globalnav A.here:link,
#globalnav A.here:visited {
	position:relative;
	z-index:102;
}
</style>
<?

$nivel_acceso=$_SESSION[$CFG->sesion]["user"]["nivel_acceso"];

//preguntar($_SESSION);
//preguntar("nivel acceso ".$nivel_acceso);


$currPage=nvl($_GET["page"]);
$arrPaginas=array();
$arrPaginas[]=array("link"=>"avl","titulo"=>"AVL");

if(in_array($nivel_acceso,$CFG->permisos["perAdmin"]))
	$arrPaginas[]=array("link"=>"admin/index","titulo"=>"Administraci�n");

if(in_array($nivel_acceso, $CFG->permisos["perMtto"]))
	$arrPaginas[]=array("link"=>"mtto/index","titulo"=>"Mantenimiento");

if(in_array($nivel_acceso, $CFG->permisos["perOpera"]))
	$arrPaginas[]=array("link"=>"opera/index","titulo"=>"Operaciones");

if(in_array($nivel_acceso,$CFG->permisos["perCompras"]))
$arrPaginas[]=array("link"=>"opera/compras","titulo"=>"Compras");

if(in_array($nivel_acceso,$CFG->permisos["perClientes"]))
	$arrPaginas[]=array("link"=>"clientes","titulo"=>"Clientes");

$qidNI = $db->sql_row("SELECT count(id_informe) as num FROM personas_informes WHERE id_persona=".nvl($_SESSION[$CFG->sesion]["user"]["id"],0));

if($qidNI["num"] > 0)
	$arrPaginas[]=array("link"=>"info/index","titulo"=>"Control");

if(in_array($nivel_acceso,$CFG->permisos["perCostos"]))
	$arrPaginas[]=array("link"=>"opera/costos","titulo"=>"Costos");

?>
</head>

<body style="margin:0;padding:0; background:url(images/fondo.gif); background-repeat:repeat-x; background-color:#517179; font-family: helvetica, arial;">
<table border="0" width="100%" height="62" cellpadding="0" cellspacing="0" style="border-bottom:thick solid #ffffff;" >
	<tr>
		<td height="100%" valign="bottom">
			<ul id="globalnav">
			<?foreach($arrPaginas AS $pag){?>
				<li><a target="_parent" href="<?=$CFG->mainPage?>?page=<?=$pag["link"]?>" <?if($pag["link"]==$currPage) echo " class=\"here\"";?>><?=$pag["titulo"]?></a></li>
			<?}?>
				<li><a target="_blank" href="Manual-AIDA.pdf">Manual</a></li>
				<li><a target="_parent" href="admin/login.php">Salir</a></li>
				<li><a href="javascript:abrirVentanaJavaScript('personas','600','500','<?=$CFG->wwwroot?>/mtto/modules.php?module=personas')"><img src="<?=$CFG->wwwroot?>/admin/iconos/transparente/stock_person.png" border=0 height="14" alt="Mi Usuario" title="Mi Usuario"></a></li>
				<td width="10%">&nbsp;</td>
				<td width="10%">Versi�n 1.1.3</td>
			</ul>
		</td>
		<td width="100"><img src="images/aida.jpg"></td>
	</tr>
</table>
</body>
</html>
