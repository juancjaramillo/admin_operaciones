<?
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
?>
<html>
<head>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>

<link rel="stylesheet" type="text/css" href="sagscroller.css" />

<script src="sagscroller.js">

/***********************************************
* SAG Content Scroller- © Dynamic Drive DHTML code library (www.dynamicdrive.com)
* Visit http://www.dynamicDrive.com for hundreds of DHTML scripts
* This notice must stay intact for legal use
***********************************************/

</script>
<script>
function abrirVentanaNueva(url,width,height){
	izq=(screen.width-width)/2;
	arriba=(screen.height-height)/2;
	vent=window.open(url,'ack','scrollbars=yes,width=' + width +',height=' + height +',resizable=yes,left='+izq+',top='+arriba);
	if(vent.focus) vent.focus();
}
function acknowledge(id_alerta){
	abrirVentanaNueva('alertas.php?mode=ack&id_alerta=' + id_alerta, 500, 500);
}

</script>

<style type="text/css">

/* #SAG scroller demo #1 */

div#mysagscroller{
width: 97%;  /*width of scroller*/
height:145px;
}

div#mysagscroller ul li{
background:white;
color:navy;
padding:2px;
margin-bottom:2px; /*bottom spacing between each LI*/
font-family:Fixed, monospace;
}

div#mysagscroller ul li:first-letter{

}

</style>

<script>

//SAG scroller demo #1:

var sagscroller1=new sagscroller({
	id:'mysagscroller',
	ajaxsource: 'alertas.php',
	refreshsecs: 300,
	mode: 'auto' //<--no comma following last option
})

//SAG scroller demo #2:

var sagscroller2=new sagscroller({
	id:'mysagscroller2',
	mode: 'auto',
	pause: 1500,
	animatespeed: 400 //<--no comma following last option
})

</script>
</head>
<body style="margin: 0px"><div id="mysagscroller" class="sagscroller"></div></body>
</html>
