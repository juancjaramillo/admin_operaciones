<?
/*	----------------------------------------------	*/
/*						MAIN						*/
/*	----------------------------------------------	*/


/************************************************************/
/***********Cargas las secciones de la pagina inicial********/
/***********************************************************/



include("application.php");
$frm=$_GET;
if(!isset($frm["page"])) $frm["page"]="avl";

$page=$frm["page"] . ".php";
$url1="encabezado.php?page=" . $frm["page"];
$url2=$page;
unset($frm["page"]);
if(sizeof($frm)>0){
	$url1=$url1 . "&";
	$url2=$url2 . "?";
	$arrayQS=array();
	foreach($frm AS $key=> $val){
		array_push($arrayQS,$key . "=" . $val);
	}
	$url1=$url1 . implode("&amp;",$arrayQS);
	$url2=$url2 . implode("&amp;",$arrayQS);
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/XHTML1/DTD/XHTML1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>AIDA</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<frameset rows="69px,*" frameborder="0" />
	<frame src="<?=$url1?>" scrolling="no" />
	<frame src="<?=$url2?>" />
</frameset> 
</html>
