<html xmlns="https://www.w3.org/1999/xhtml" lang="en" xml:lang="en" style="height: 100%">
<head>
<title><?=$CFG->siteTitle?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body bgcolor="#FFFFFF" style="margin: 0; height: 100%">
<table align="center" border="0" cellspacing="1" cellpadding="5" width="100%" style="height: 100%">
	<tr>
		<td height="90%" align="center" valign="middle">
			<form name="entryform" method="post" action="login.php">
			<input type="hidden" name="goto" value="<?=nvl($frm["goto"])?>" />
			<table align="center" border="0" cellspacing="1" cellpadding="5" width="100%" style="height: 100%">
				<tr>
					<td height="30" width="100%" align=center valign=middle>
						<table align="center" border="0" class="tabla_externa">
							<tr>
								<td height="104" align="center">
									<IMG BORDER="0" ALT="<?=$CFG->siteTitle?>" src="<?=$CFG->siteLogo?>"/>
								</td>
								<td width="300" align="center" valign="middle" bgcolor="#ffffff">
								</td>
							</tr>
							<tr>
								<td align="center" valign="middle" colspan="2">
									<p>&nbsp;</p>
									<? if (! empty($errormsg)) { ?>
										<div align=center><b><?=nvl($errormsg) ?></b></div>
									<?}?>

									<table border="0" cellspacing="1" cellpadding="0" style="height: 50" align="center">
										<tr>
											<th align="right" >Usuario :&nbsp;</th>
											<td align="center" valign="middle"><input value="<?=nvl($frm["username"]) ?>" type="text" name="username" size="16" /></td>
										</tr>
										<tr>
											<th align="right">Clave :&nbsp;</th>
											<td align="center" valign="middle"><input type=password name="password" size="16" /></td>
										</tr>
										<tr> 
											<td align="center" valign="middle" colspan="2"><br/><input type="submit" name="Submit" value="entrar" /></td>
										</tr>
									</table>

								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			</form>
		</td>
	</tr>	
	<tr>
		<td align="center" valign="middle">			
			<div class="titulo_boton">
			  ¿Ha olvidado su contraseña?
			  <a style='cursor: pointer;' onClick="muestra_oculta('contenido')" title="" class="boton_mostrar">Mostrar / Ocultar</a>
			</div>            
		</td>
	</tr>	
	<tr>
		<td height="90%" align="center" valign="middle">
		  <div id="contenido">	
<form name="entryform" method="post" action="send_password.php">		  
			<!--form method="post" action="send_password.php"-->
			  <p>Ingrse su  Email Para enviar su password</p>
			  <!--input value="<?/*=nvl($frm["username"])*/ ?>" type="text" name="username" size="16" />
			  <input type="text" name="email">
			  <input type="submit" name="submit_email"-->			  
			  
			  <table border="0" cellspacing="1" cellpadding="0" style="height: 50" align="center">
					<tr>
						<th align="right" >Usuario :&nbsp;</th>
						<td align="center" valign="middle"><input value="<?=nvl($frm["username"]) ?>" type="text" name="username" size="16" /></td>
					</tr>
					<tr>
						<th align="right">Email :&nbsp;</th>
						<td align="center" valign="middle"><input type="text" name="email" size="16" /></td>
					</tr>
					<tr> 
						<td align="center" valign="middle" colspan="2"><br/><input type="submit" name="Submit" value="entrar" /></td>
					</tr>
				</table>			  
			</form>		
	    </div>
		</td>
	</tr>
	<tr>
		<td width="100%" height="60" bgcolor="#cbe1f8">
			<table align="center" border="0" cellspacing="1" cellpadding="1" width="100%">
				<tr>
					<td align="right" valign="middle" width="50"></td>
					<td align="left" valign="middle" width="30"></td>
					<td align="right"></td>
					<td align="left" valign="middle" width="250">Versión: 1.1.3 &nbsp; Fecha: 2019-09-10&nbsp;</td>
				</tr>
				<tr>
					<td align="right" valign="middle" width="50"></td>
					<td align="left" valign="middle" width="30"></td>
					<td align="center">
						<a href="https://www.spreadfirefox.com/?q=affiliates&amp;id=200139&amp;t=210">
						<img border="0" alt="Firefox 2" title="Firefox 2" src="https://sfx-images.mozilla.org/affiliates/Buttons/firefox2/firefox-spread-btn-1b.png"/></a>
					</td>
					<td align="right" valign="top" width="100">
						<A HREF="https://www.postgresql.net/"><IMG BORDER="0" ALT="PostgreSQL Powered" SRC="<?=$CFG->imagedir?>/postgresql_powered.gif"/></A>
						<A HREF="https://php.net/"><IMG BORDER="0" ALT="PHP Powered" SRC="<?=$CFG->imagedir?>/php-power-micro2.png"/></A>
						<A HREF="https://www.apache.org/"><IMG BORDER="0" ALT="Apache Powered" SRC="<?=$CFG->imagedir?>/apache_powered.gif"/></A>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<!--ACA VA EL PIE DEL DISEÑO-->
<script type="text/javascript">
document.entryform.username.focus();

function muestra_oculta(id){
if (document.getElementById){ //se obtiene el id
var el = document.getElementById(id); //se define la variable "el" igual a nuestro div
el.style.display = (el.style.display == 'none') ? 'block' : 'none'; //damos un atributo display:none que oculta el div
}
}
window.onload = function(){/*hace que se cargue la función lo que predetermina que div estará oculto hasta llamar a la función nuevamente*/
muestra_oculta('contenido');/* "contenido_a_mostrar" es el nombre que le dimos al DIV */
}
	
</script>
</body>
</html>

