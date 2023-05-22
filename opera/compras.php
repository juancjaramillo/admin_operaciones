<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

<?

// error_reporting(E_ALL);
// ini_set("display_errors", 1);
// echo "<pre>";
// print_r($_POST);
// print_r($_GET);
// echo "</pre>"; 
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Informes</title>
<style type="text/css">



</style>
<script type='text/javascript'>


openwindow();
function openwindow()
{
	//window.open("https://192.168.100.21/Account/Login?ReturnUrl=%2f","mywindow","menubar=1,resizable=1,width=350,height=250");
	window.open("http://192.168.100.21/Serenity","mywindow","menubar=1,resizable=1,width=900,height=800");
}


function redimensionariframe($url){
	
	obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';	
	obj.style.width  = obj.contentWindow.document.body.scrollWidth  + 'px';

}
</script>


</head>
<body>
<?
header('Content-type: text/html');
/* include("../application.php");
$html = true;
$user=$_SESSION[$CFG->sesion]["user"];
$nivel=$_SESSION[$CFG->sesion]["user"]["nivel_acceso"];
$tipo_info = $_POST["order"];
if($tipo_info=="") {
	$tipo_info=1;
}

$id=$_SESSION[$CFG->sesion]["user"]["id"];
$token=$user["token"];
$key=$user["key"];
$union=$token.$key;
//$post = "?token='".$union."'&id='".$id."'";
$post = "?id='".$id."'&token='".$union."'"; */
?>
<table width="100%" height="100%" style="border-spacing:1px 1px; border:solid #fff; ">
	<tr height="35px" bgcolor="#b2d2e1">
		<td valign="center">
			<div id="menu">
				<ul>
				
		<tr>
		<!--td align='left'>
		<iframe height="100%" width="100%"  name="iframe" id="iframe" src="https://192.168.100.21/Account/Login?ReturnUrl=%2f" class="iframe"   frameborder="0" ></iframe>
		</td-->
		
	<td align='left'>
	<iframe height="100%" width="100%"  name="iframe" id="iframe" src="https://192.168.100.203/pa/redirect.php?url=http://192.168.100.21/Serenity" class="iframe" scrolling="no"  frameborder="0" onload="redimensionariframe(this)"></iframe>
	<iframe height="100%" width="100%"  name="iframe" id="iframe" src="<?php print ("https://google.com"); ?>" onload="redimensionariframe(this)></iframe>
	
	</td>
		
	<!--td align='left'>
	<iframe height="100%" width="100%"  name="iframe" id="iframe" src="https://192.168.100.203/pa/redirect.php?url=https://192.168.100.21/Serenity" class="iframe" scrolling="no"  frameborder="0"></iframe>
	
	
	</td>	
	
	<td align='left'>
	<iframe height="100%" width="100%"  name="iframe" id="iframe" src="http://192.168.100.21/Serenity" class="iframe" scrolling="no"  frameborder="0" onload="redimensionariframe(this)"></iframe>
	
	
	</td-->
	
		
		<!--https://192.168.100.21/Account/Login?ReturnUrl=2fSerenity
		
		http://www.brainnova.co/
		-->
		
		
		</tr>
									
									</ul>
									</div>
									</td>
									</tr>
					
</table>
<?

?>