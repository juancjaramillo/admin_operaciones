<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <title>Seleccionar Color</title>

<script type="text/javascript">
	function View(color) {                  // preview color
  	document.getElementById("ColorPreview").style.backgroundColor = '#' + color;
  	document.getElementById("ColorHex").value = '#' + color;
	}
	function Update(color,color_hex) {
		window.opener.document.getElementById('<?=$frm["input"]?>').value=color;
<?
if(nvl($_GET["submit"],0)){	
?>
		window.opener.document.entryform.submit();
<?
}
else{
?>
		window.opener.document.getElementById("cell_<?=$frm["input"]?>").style.backgroundColor = '#' + color_hex;
<?
}
?>
		window.opener.focus();
		window.close();
	}
</script>

</head>
<body style="margin:0px; padding:0px">

<table border="0" cellspacing="1px" cellpadding="0px" bgcolor="#000000">
	<tr><td colspan="18">
		<table border="0px" cellspacing="0px" cellpadding="4" width="100%">
		 <tr>
  		<td style="background:buttonface" valign=center>
				<div style="background-color: #000000; padding: 1; height: 21px; width: 50px">
					<div id="ColorPreview" style="background-color: #<?=$defaultColor?>; height: 100%; width: 100%"></div>
				</div>
			</td>
  		<td style="background:buttonface" valign=center>
				<input type="text" name="ColorHex" id="ColorHex" value="#<?=$defaultColor?>" size=15 style="font-size: 12px">
			</td>
  		<td style="background:buttonface" width=100%></td>
		 </tr>
		</table>
	</td></tr>
<?
echo $string_colors;
?> 
</table>
</body>
</html>
