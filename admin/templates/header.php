<html>
<head>
<title><?=$CFG->siteTitle?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?=$CFG->admin_dir?>/style.css" rel="stylesheet" type="text/css">
<script>
function switchDisplay(div){
	if(document.getElementById(div).style.display=='none') document.getElementById(div).style.display='inline';
	else if(document.getElementById(div).style.display=='inline' || document.getElementById(div).style.display=='') document.getElementById(div).style.display='none';
}
</script>
<?if(isset($requireAC) && $requireAC==1){?>
	<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.6.0/build/autocomplete/assets/skins/sam/autocomplete.css" />

	<script type="text/javascript" src="http://yui.yahooapis.com/2.6.0/build/yahoo-dom-event/yahoo-dom-event.js"></script>
	<script type="text/javascript" src="http://yui.yahooapis.com/2.6.0/build/connection/connection-min.js"></script>

	<script type="text/javascript" src="http://yui.yahooapis.com/2.6.0/build/datasource/datasource-min.js"></script>
	<script type="text/javascript" src="http://yui.yahooapis.com/2.6.0/build/autocomplete/autocomplete-min.js"></script>

	<style type="text/css">
		.AutoComplete {
			width:25em; /* set width here or else widget will expand to fit its container */
			padding-bottom:2em;
		}
	</style>
<?}?>
</head>

<body bgcolor="#ffffff" text="#000000">

<table border="0" cellpadding="0" cellspacing="0" width="100%" style="height:100%">
	<tr>
		<td width="180" valign="top" height="100%">
			<table border="0"  cellpadding="0" cellspacing="0" width="100%" style="height:100%">
				<tr valign="top" bgcolor="#d9d9c3">
					<td width="20" height="24"></td>
					<td></td>
				</tr>
				<tr valign="top" bgcolor="#d9d9c3">
					<td width="20">&nbsp;</td>
					<td height="100%">
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td class="nav">
								</td>
								<td>
									<?
										include("menu_.php");
									?>
									<hr>
									<div id="div_status"></div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td width="20">&nbsp;</td>
					<td><img alt="" src="<?=$CFG->imagedir?>/break.gif" height="1" width="160" vspace="4"></td>
				</tr>
				<tr valign="top">
					<td width="20">&nbsp;</td>
					<td align="left" class="pipe">Usuario: <?echo $_SESSION[$CFG->sesion]["user"]["nombre"] . " " . $_SESSION[$CFG->sesion]["user"]["apellido"];?></td>
				</tr>
				<tr valign="bottom">
					<td align="right" colspan="2">
					</td>
				</tr>
			</table>
		</td>
		<td valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="titulo" valign="bottom" colspan=2 height=53 >
					</td>
				</tr>
				<tr>
					<td class="titulo" valign="bottom">&nbsp;&nbsp;<?=$CFG->siteTitle?> [<?if(isset($entidad)) echo $entidad->labelModule?>]</td>
				<td align="right">
						<table border="0" cellpadding="2" cellspacing="0">
								<tr>
									<td width="10" align="center">&nbsp;
									</td>
									<td valign="bottom"> <a title="Home" HREF="<?=$CFG->wwwroot?>" target="_blank">
										<IMG BORDER="0" ALT="Home" SRC="<?=$CFG->imagedir?>/gohome.png"></a>
									</td>
									<td width="10" align="center">&nbsp;
									</td>
									<td valign="bottom"> <a target="_parent" title="Salir" HREF="<?=$CFG->admin_dir?>/login.php">
										<IMG BORDER="0" ALT="Home" SRC="<?=$CFG->imagedir?>/exit.png"></a>
									</td>
									<td width="10" align="center">&nbsp;&nbsp;&nbsp;&nbsp;
									</td>
								</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td valign="top" colspan=2 bgcolor="#ffffff">
						<table width="100%" cellpadding="1" cellspacing="0" border="0">
							<tr>
								<td>
									<!-- empiezan las páginas -->














<!--

								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

-->			

