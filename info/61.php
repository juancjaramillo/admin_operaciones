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
.container {
    display: grid;
    grid-template-columns: 100% ;
    grid-template-rows: 100%;
	width: 100%;
   height: 100%;
	
	html, body {
    height: 100%;
}

div {
    height: 100%;
	width:  100%;
}

object {
    width: 100%;
    min-height: 100%;
	 height: 100%;
}     
}
</style>
<script type='text/javascript'>
function redimensionariframe(obj){
	obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
	obj.style.width  = obj.contentWindow.document.body.scrollWidth  + 'px';
}
</script>


</head>
<body>
<?
include("../applicationreports.php");
$html = true;
$user=$_SESSION[$CFG->sesion]["user"];

$nivel=$_SESSION[$CFG->sesion]["user"]["nivel_acceso"];
$tipo_info = $_POST["order"];
if($tipo_info=="") {
	$tipo_info=2;
}

if(isset($_GET["format"])) 
{
	$html=false;
	$inicio = $_GET["inicio"];
	$final = $_GET["final"];
}
$titulo1 = $db->sql_row("SELECT upper(nombre||' : '||informe) as inf FROM informes i LEFT JOIN categorias_informes c ON c.id=i.id_categoria_informe WHERE i.id=".str_replace(".php","",simple_me($ME)));
if($html)
{
	include($CFG->dirroot."/templates/header_popup.php");
	include($CFG->dirroot."/info/templates/fechas_form_46.php");
	tablita_titulos($titulo1["inf"],$inicio." / ".$final);	
	$post = "?inicio=".$inicio."&final=".$final."&tipo_info=".$tipo_info;	
}else{	
	require_once $CFG->common_libdir."/writeexcel/class.writeexcel_workbook.inc.php";
	require_once $CFG->common_libdir."/writeexcel/class.writeexcel_worksheet.inc.php";
	$fname=$CFG->tmpdir."/informe.xls";
	if(file_exists($fname))
	unlink($fname);
	$workbook = new writeexcel_workbook($fname);
	$workbook->set_tempdir($CFG->tmpdir);
	$worksheet = &$workbook->addworksheet("reporte");
	$worksheet->set_zoom(80);
	titulo_grande_xls($workbook, $worksheet, 0, 11, $titulo1["inf"]."\n".$inicio." / ".$final);
	$fila=2; $columna=0;
}
$fechas = sacarMeses($inicio, $final);
$titulos = array();
$estilostitulosFinanciera = array(1=>" height='30'  class='azul_osc_14'");
$estilos = array(1=>"align='left'  class='azul_osc_12'");
$i=2;
	
$qidCentro = $db->sql_query("SELECT id_centro, centro FROM personas_centros LEFT JOIN centros ON centros.id=personas_centros.id_centro WHERE id_persona='".$user["id"]."' ORDER BY id_centro");

while($queryCen = $db->sql_fetchrow($qidCentro)){
	$centros[$queryCen["id_centro"]] = $queryCen["centro"];
	$titulos[] = $queryCen["centro"];

	if ($tipo_info==1){
		foreach($fechas as $mes) 
		{
			$titulosFinanciera[] = $titulosCliente[] = $titulosProceso[] = $titulosInnovacion[] = ucfirst(strftime("%b.%Y",strtotime($mes."-01")));
			$estilostitulosFinanciera[$i] = " class='azul_osc_14'";
			$estilos[$i] = "class='azul_osc_12'";
			$i++;
		}
	}
	else {
		$titulosFinanciera[]=$titulosCliente[]=$titulosProceso[]=$titulosInnovacion[] = $titulosIndFinan[] = $queryCen["centro"];
		$estilostitulosFinanciera[] = " class='azul_osc_14'";
		$estilos[] = "class='azul_osc_12'";
	}
	
}

?>
<table width="100%" height="100%" style="border-spacing:1px 1px; border:solid #fff; ">
	<tr height="35px" bgcolor="#b2d2e1">
		<td valign="center">
			<div id="menu">
				<ul>
					<?				
					$user=$_SESSION[$CFG->sesion]["user"];
					$cat = $inf = $mis = array();
					$qidCI = $db->sql_query("SELECT r.reporte, r.id as id_reporte
							FROM personas_reportes pr
							LEFT JOIN reportes r ON r.id=pr.id_reportes							
							WHERE pr.id_persona='".$user["id"]."' AND pr.id_informe=".str_replace(".php","",simple_me($ME))."
							ORDER BY r.reporte ASC") or die (mysql_error());
					
					if ($db->sql_numrows($qidCI)>0){									
									while($in = $db->sql_fetchrow($qidCI)){
									?>
									<tr>
									<td align='left'>
										<iframe height="100%" width="100%"  name="iframe" id="iframe" src="<?=$CFG->wwwroot."/reportesgerenciales/".$in[0].".php".$post?>" class="iframe" scrolling="no"  frameborder="0" onload="redimensionariframe(this)"></iframe>
									</td>
									</tr>
									<?}									
							}else {
								echo "No tiene permisos de acceso, consulte con el administrador del sistema";
							}
					?>
</table>
<?
if($html)
{
	$link = "?format=xls&inicio=".$inicio."&final=".$final;
	echo "</table><br /><br />";
	echo "
	<table width='98%' align='center'>
		<tr>
			<td height='50' valign='bottom' align='right'><input type='button' class='boton_verde' value='Bajar en xls' onclick=\"window.location.href='".$ME.$link."'\"/></td>
		</tr>
	</table>
	";
}
else
{
	$workbook->close();
	$nombreArchivo=preg_replace("/[^0-9a-z_.]/i","_",$titulo1["inf"])."_".$inicio."_".$final.".xls";
	header("Content-Type: application/x-msexcel; name=\"".$nombreArchivo."\"");
	header("Content-Disposition: inline; filename=\"".$nombreArchivo."\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
}
?>