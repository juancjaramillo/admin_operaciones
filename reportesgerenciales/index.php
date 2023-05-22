<?
include("../application.php");

if(!isset($_SESSION[$CFG->sesion]["user"])){
	$errorMsg="No existe la sesión.";
	error_log($errorMsg);
	die($errorMsg);
}
$user=$_SESSION[$CFG->sesion]["user"];

$paged=nvl($_GET["paged"],nvl($_POST["paged"],""));
$link = "/reportesgerenciales/blanco.php";
if($paged != "")
	$link = "/reportesgerenciales/".$paged;

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Informes</title>
<style type="text/css">
body {
	margin:0;
	padding:0;
	font-family: Verdana, Arial, Helvetica, sans-serif;
}

* { margin: 0px;
padding: 0px; outline: 0;
}
html, body { width: 100%;}

#menu {  
	text-align: center;
	font-size: 0.7em;
	margin: 20px auto;
	width: 1000px;
}
#menu ul { list-style-type: none;}
#menu ul li.nivel1 { 
	float: left;
	width: 162px;
	margin-right: 10px;
}
#menu ul li a {
	display: block;
	text-decoration: none;
	color: #fff;
	background-color: #7c9cab;
	border: solid 1px #fff;
	padding: 8px;
	position: relative;
	font:bold 11px Verdana, Arial, Helvetica, sans-serif;
}
#menu ul li:hover {position: relative;
}
#menu ul li a:hover, #menu ul li:hover a.nivel1 {
	background-color: #496e77;
	color: #fff;
	position: relative;
}
#menu ul li a.nivel1 {
	display: block!important;display: none;
	position: relative;
}
#menu ul li ul {display: none;
}
#menu ul li a:hover ul, #menu ul li:hover ul {
	display: block;
	position: absolute;left: 0px;
}
#menu ul li ul li a {
	width: 160px;
	padding: 6px 0px 8px 0px;
	border: solid 1px;
	border-top-color: #fff;
}
#menu ul li ul li a:hover {
	border-top-color: #fff;
	position: relative;
}
table.falsa {
	border-collapse:collapse;
	border:0px;
	float: left;
	position: relative;
}

</style>

</head>

<body>
<table width="100%" height="100%" style="border-spacing:1px 1px; border:solid #fff; ">
	<tr height="35px" bgcolor="#b2d2e1">
		<td valign="center">
			<div id="menu">
				<ul>
					<?
					$user=$_SESSION[$CFG->sesion]["user"];
					$cat = $inf = $mis = array();
					$qidCI = $db->sql_query("SELECT c.id as id_cat, c.nombre, i.informe, i.id as id_informe
							FROM personas_informes pi
							LEFT JOIN informes i ON i.id=pi.id_informe
							LEFT JOIN categorias_informes c ON c.id=i.id_categoria_informe
							WHERE pi.id_persona='".$user["id"]."' AND c.id != 4
							ORDER BY c.nombre, i.informe");
					while($in = $db->sql_fetchrow($qidCI))
					{
						$cat[$in["id_cat"]] = $in["nombre"];
						$mis[$in["id_cat"]][$in["id_informe"]] = array("id"=>$in["id_informe"], "nombre"=>$in["informe"]);	
					}

					foreach($cat as $idCat => $nmCat)
					{?>
						<li class="nivel1"><a href="#" class="nivel1"><?=$nmCat?></a>
							<!--[if lte IE 6]><a href="#" class="nivel1ie">Opción 1<table class="falsa"><tr><td><![endif]-->
							<ul>
								<?foreach($mis[$idCat] as $dx){									
									
									?>
									<li><a target="_parent" href="<?=$CFG->wwwroot."/frames.php?page=reportesgerenciales/index&paged=".$dx["id"].".php"?>"><?=$dx["nombre"]?></a></li>
								<?}?>
							</ul>
							<!--[if lte IE 6]></td></tr></table></a><![endif]-->
						</li>
					<?}?>
				</ul>
			</div>
		</td>
	</tr>
	<tr>
		<td valign="top"><iframe src="<?=$CFG->wwwroot.$link?>" style="border-style: none; width: 100%; height: 100%; scrolling:auto; "></td>
	</tr>
</table>
</body>
</html>
