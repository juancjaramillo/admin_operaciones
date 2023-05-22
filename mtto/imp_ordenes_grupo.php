<?
// echo "<pre>";
// print_r($_POST);
// print_r($_GET);
// echo "</pre>"; 

require_once("../application.php");
require_once('../lib/tcpdf/config/lang/eng.php');
require_once('../lib/tcpdf/tcpdf.php');

$fecini=$_POST["inicio_fecha_planeada"];
$fecfin=$_POST["fin_fecha_planeada"];
$fecactual = date ( "Y-m-d H:m:s" , time());

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); 
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('AIDA');
// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// disable header and footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(true);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 1.5);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 

//set some language-dependent strings
$pdf->setLanguageArray($l); 

// set font
$pdf->SetFont('helvetica', 'B', 12);
// add a page
$pdf->AddPage();

//INFORMACIÓN GENERAL DEL VEHÍCULO
$vehiculo = "select v.id,v.kilometraje,v.horometro,c.id as id_centro,c.centro,e.nombre as equipo
	from vehiculos  v
	left join centros c on v.id_centro=c.id
	LEFT JOIN mtto.equipos e ON e.id_vehiculo=v.id
	where e.id=$_POST[id_equipo]";
$qid1 = $db->sql_query($vehiculo);
while($query1rd = $db->sql_fetchrow($qid1))
{
	$id_centro = $query1rd[id_centro];
	$centro = $query1rd[centro];
	$equipo = $query1rd[equipo];
	$kms_vehi = number_format($query1rd[kilometraje],0);
	$hs_vehi = number_format($query1rd[horometro],0);
}

// INICIO ENCABEZADO
$pdf->Image('../images/logos/'.$id_centro.'.png', 21, 5.2, 17, 17,  '','');
$pdf->Ln(11.6);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(37, 3, '', '0', 2, 'R', 0); // 
$pdf->RoundedRect(5, 5, 205, 25, 1.50, '1111', 'D',$style5, array(0, 0, 0)); // Rectangulo general
$pdf->Line(55, 5, 55, 30, array(230, 230, 230));  // linea Vertical logo
$pdf->Line(155, 5, 155, 30, array(230, 230, 230));
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetXY(55,15.5);
$pdf->Cell(106, 5, 'ORDENES DE TRABAJO POR VEHICULO', '0', 2, 'C', 0);  
$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(155,5);
$pdf->Cell(55, 5, '', '', 2, 'L', 0);
$pdf->Cell(55, 5, '', '1', 2, 'L', 0);
$pdf->Cell(55, 5, '', '1', 2, 'L', 0);
$pdf->Cell(55, 5, '', '1', 2, 'L', 0);
$pdf->Cell(55, 5, '', '', 2, 'L', 0);
//FIN ENCABEZADO

$tbl = <<<EOD
<table cellspacing="0" cellpadding="1" border="1">
<tr>
	<td align="center" height="20" colspan=4><b><FONT SIZE=12>INFORMACIÓN GENERAL DEL EQUIPO</FONT></b></td>
</tr>
<tr>
	<td height="20" width="25%"><FONT SIZE=10><b>Centro:</b> $centro</FONT></td>
	<td height="20" width="25%"><FONT SIZE=10><b>Vehículo: </b>$equipo</FONT></td>
	<td height="20" width="25%"><FONT SIZE=10><b>Kms Vehículo:</b> $kms_vehi</FONT></td>
	<td height="20" width="25%"><FONT SIZE=10><b>Horometro Vehículo:</b> $hs_vehi</FONT></td>
</tr>
<tr>
	<td height="20" width="33%" ><FONT SIZE=10><b>Fecha Inicial Programación:</b> $fecini</FONT></td>
	<td height="20" width="33%"><FONT SIZE=10><b>Fecha Fin Programación: </b>$fecfin</FONT></td>
	<td height="20" width="34%"><FONT SIZE=10><b>Fecha Generación:</b> $fecactual</FONT></td>
</tr>
<tr>
	<td height="20" width="50%"><FONT SIZE=10><b>Kms Actual:</b> </FONT></td>
	<td height="20" width="50%"><FONT SIZE=10><b>Horometro Actual:</b> </FONT></td>
</tr>
</table>
EOD;

$pdf->Ln(1);
$pdf->writeHTML($tbl, true, false, false, false, '');
//FIN INFORMACIÓN GENERAL DEL VEHÍCULO

//INFORMACIÓN DE LAS RUTINAS
$condicion=" r.id IN (SELECT id_rutina FROM mtto.rutinas_centros WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."'))";
$cons = "SELECT o.id as ot,e.id_vehiculo, e.nombre as equipo, r.rutina, o.id_responsable, o.id_planeador, r.id as id_rutina,o.observaciones,
	act.*,cargos.nombre as cargo,car.tiempo,per.nombre||' '||per.apellido as persona
	FROM mtto.ordenes_trabajo o LEFT JOIN mtto.rutinas r ON r.id=o.id_rutina 
	LEFT JOIN mtto.equipos e ON e.id=o.id_equipo 
	LEFT JOIN mtto.estados_ordenes_trabajo est ON est.id=o.id_estado_orden_trabajo 
	LEFT JOIN mtto.ordenes_trabajo_actividades act on o.id= act.id_orden_trabajo
	LEFT JOIN mtto.ordenes_trabajo_actividades_cargos car ON act.id_orden_trabajo=car.id_orden_trabajo_actividad
	LEFT JOIN cargos ON car.id_cargo=cargos.id
	LEFT JOIN personas per ON car.id_persona=per.id
	WHERE ".$condicion." AND o.fecha_planeada::date >= '".$_POST["inicio_fecha_planeada"]."' 
	AND o.fecha_planeada::date <= '".$_POST["fin_fecha_planeada"]."' AND e.id=".$_POST["id_equipo"]." AND NOT est.cerrado 
	order by 1,10";

$qidO = $db->sql_query($cons);

$ot1="";
$orden1="";
$actividad1 ="";

while($queryOrd = $db->sql_fetchrow($qidO))
{	
	$ot=$queryOrd[ot];
	$rutina=$queryOrd[rutina];
	$observa=$queryOrd[observaciones];

	$orden =$queryOrd[orden];
	$actividad =$queryOrd[descripcion];
	$acttiempo =$queryOrd[tiempo];
	$tecnico =$queryOrd[persona];
	$cargo =$queryOrd[cargo];
	
	if ($ot1<>$ot) {
	$tbl = <<<EOD
		<table cellspacing="0" cellpadding="0" border="1">
		<tr>
			<td height="20" width="10%"><FONT SIZE=8><b>OT N.: $ot </b></FONT></td>
			<td height="20" width="45%"><FONT SIZE=8><b>Rutina:</b> $rutina</FONT></td>
			<td height="20" width="45%"><FONT SIZE=8><b>Observación:</b> $observa</FONT></td>
		</tr>
		<tr>
			<td height="20" width="40%"><FONT SIZE=8><b>Actividad $orden:</b> $actividad</FONT></td>
			<td height="20" width="10%"><FONT SIZE=8><b>Tiempo:</b> $acttiempo</FONT></td>
			<td height="20" width="30%"><FONT SIZE=8><br><b>Técnico: $tecnico </b></br><br>$cargo</br></FONT></td>
			<td height="20" width="25%"><FONT SIZE=8><b>Firma:</b>____________________</FONT></td>
		</tr>

EOD;
	}
	else {
	$tbl = <<<EOD
	<table cellspacing="0" cellpadding="0" border="1">
		<tr>
			<td height="20" width="40%"><FONT SIZE=8><b>Actividad $orden:</b> $actividad</FONT></td>
			<td height="20" width="10%"><FONT SIZE=8><b>Tiempo:</b> $acttiempo</FONT></td>
			<td height="20" width="30%"><FONT SIZE=8><br><b>Técnico: $tecnico </b></br><br>$cargo</br></FONT></td>
			<td height="20" width="25%"><FONT SIZE=8><b>Firma:</b>____________________</FONT></td>
		</tr>

EOD;
	}
	$pdf->writeHTML($tbl, true, false, false, false, '');
	$db->sql_query("UPDATE mtto.ordenes_trabajo SET id_estado_orden_trabajo=5 WHERE id=".$queryOrd[ot]);
	$ot1=$ot;
}
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 8);
$pdf->RoundedRect(10, 250, 90, 15, 1.50, '0110', 'D',$style5, array(200, 200, 200));
$pdf->SetXY(10,253);
$pdf->Cell(45, 8, 'Supervisor de Mantenimiento:', '', 1, 'L', 0);
$pdf->RoundedRect(105, 250, 90, 15, 1.50, '0110', 'D',$style5, array(200, 200, 200));
$pdf->SetXY(108,253);
$pdf->Cell(45, 8, 'Planeador y/o Programador :', '', 1, 'L', 0);




//Close and output PDF document
$pdf->Output('OT.pdf', 'I');
?>
