<?
// echo "<pre>";
// print_r($_POST);
// print_r($_GET);
// echo "</pre>"; 
// error_reporting(E_ALL);
// ini_set("display_errors", 1);

require_once("../application.php");
require_once('../lib/tcpdf/config/lang/eng.php');
require_once('../lib/tcpdf/tcpdf.php');

$user=$_SESSION[$CFG->sesion]["user"];
$fecini=$_POST["inicio_fecha_planeada"];
$fecactual = date ( "Y-m-d H:m:s" , time());

$condicion = "veh.id_centro IN (".implode(",",$user["id_centro"]).")";

class MYPDF extends TCPDF {
	// Page footer
	public function Footer() {
		// Position at 1.5 cm from bottom
		$this->SetY(-15);
		// Set font
		$this->SetFont('helvetica', 'I', 8);
		// Page number
		$this->Cell(0, 10, 'Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 0, 'C');
	}
}

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

$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 1.5);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 

//set some language-dependent strings
$pdf->setLanguageArray($l); 

// set font
$pdf->SetFont('helvetica', 'B', 12);
// add a page
$pdf->AddPage('L');

$Query= "select * from centros where  id IN (".implode(",",$user["id_centro"]).")";
$qid1 = $db->sql_query($Query);
while($query1rd = $db->sql_fetchrow($qid1))
{
	$id_centro = $query1rd[id];
}

// INICIO ENCABEZADO
$pdf->Image('../images/logos/'.$id_centro.'.jpg', 8, 7, 47, 17,  '','');
$pdf->Ln(11.6);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(37, 3, '', '0', 2, 'R', 0); // 
$pdf->RoundedRect(5, 5, 265, 25, 1.50, '1111', 'D',$style5, array(0, 0, 0)); // Rectangulo general
$pdf->Line(75, 5, 75, 30, array(230, 230, 230));  // linea Vertical logo
$pdf->Line(195, 5, 195, 30, array(230, 230, 230));
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetXY(55,15.5);
$pdf->Cell(146, 5, 'PROGRAMACIÓN TRABAJO TÉCNICOS', '0', 2, 'C', 0);  
$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(155,5);

//FIN ENCABEZADO
$pdf->Ln(25);
$pdf->SetFont('helvetica', 'B', 8);

//INFORMACIÓN DE LA PROGRAMACION
$cons = "select veh.id_centro,ot.id as orden, min(ot.fecha_planeada::time) as fecha_planeada, veh.nombre as equipo, emp.cedula,
	trim(emp.nombre)||' '||trim(emp.apellido) as empleado, car.nombre as cargo, rut.rutina
	from mtto.ordenes_trabajo as ot
	left join mtto.equipos veh on ot.id_equipo=veh.id
	left join mtto.ordenes_trabajo_actividades as act on ot.id=act.id_orden_trabajo
	inner join mtto.ordenes_trabajo_actividades_cargos as per on act.id=per.id_orden_trabajo_actividad
	left join personas as emp on per.id_persona=emp.id
	left join cargos as car on emp.id_cargo=car.id
	left join mtto.rutinas as rut on ot.id_rutina=rut.id 
	where (ot.fecha_ejecucion_inicio::date>='".$fecini."' and ot.fecha_ejecucion_inicio::date<='".$fecini."')
	and ".$condicion." and emp.cedula is not null
	group by veh.id_centro,ot.id,veh.nombre,cedula,empleado,cargo,rut.rutina 
	order by 5,3";
$qidO = $db->sql_query($cons);
$cedula1="";
$orden1="";
$actividad1 ="";

$pdf->Ln(1);
// create some HTML content
$tbl = '<table cellspacing="1" cellpadding="1" border="1"><tr align="center" bgcolor="#cccccc"><td width="8%"><FONT SIZE=10><b>Cédula</b></FONT></td><td height="10" width="20%"><FONT SIZE=10><b>Nombre</b></FONT></td><td height="10" width="7%"><FONT SIZE=10><b>Hora Entrada</b></FONT></td><td height="10" width="10%"><FONT SIZE=10><b>Firma</b></FONT></td><td height="10" width="7%"><FONT SIZE=10><b>Hora Salida</b></FONT></td><td height="10" width="10%"><FONT SIZE=10><b>Firma</b></FONT></td><td height="10" width="35%"><FONT SIZE=10><b>Observación</b></FONT></td></tr>';
$fila=0;
while($queryOrd = $db->sql_fetchrow($qidO))
{	
	$cedula=$queryOrd[cedula];
	$empleado= $queryOrd[empleado];
	$cargo= $queryOrd[cargo];
	$orden =$queryOrd[orden];
	$actividad =$queryOrd[rutina];
  $equipo =$queryOrd[equipo];
	$fechaini =$queryOrd[fecha_planeada];
	
	if ($cedula1<>$cedula) {
		if ($desactiv<>''){
			$tbl.= $tbl1.'<td width="35%"><FONT SIZE=7></FONT>'.$desactiv.'</td></tr>';
			$tbl1='';
		}
		$desactiv = 'OT N.'.$orden.': '.$actividad.'<br/>';
		$tbl1= '<tr><td height="10" width="8%" align="right"><FONT SIZE=8>'.$cedula.'</FONT></td><td height="10" width="20%"><FONT SIZE=8>'.$empleado.'</FONT></td><td height="10" width="7%" align="center"><FONT SIZE=8>'.$fechaini.'</FONT></td><td height="10" width="10%"><FONT SIZE=8><b></b></FONT></td><td height="10" width="7%"><FONT SIZE=8><b></b></FONT></td><td height="10" width="10%"><FONT SIZE=8><b></b></FONT></td>';
	}
	else {
			$desactiv .= 'OT No.'.$orden.' Veh�culo: '.$equipo." ".$actividad.'<br/>';
	}

		
	$cedula1=$cedula;
	
	if ($fila==20) {
		$tbl.='</tab	le>';
		$pdf->writeHTML($tbl, true, 0, true, 0);
		//Creamos las casillas para las firmas de autorización
		$pdf->SetFont('helvetica', 'B', 10);
		$pdf->Cell(45, 8, 'Los funcionarios del área de mantenimiento  relacionados, tienen autorizado el ingreso a las instalaciones de la empresa el día '.$fecini, '', 1, 'L', 0);
		$pdf->SetFont('helvetica', 'B', 8);
		$pdf->RoundedRect(10, 185, 110, 15, 1.50, '0110', 'D',$style5, array(200, 200, 200));
		$pdf->SetXY(10,185);
		$pdf->Cell(45, 8, 'Gerente Administrativo y Financiero:', '', 1, 'L', 0);
		$pdf->RoundedRect(130, 185, 110, 15, 1.50, '0110', 'D',$style5, array(200, 200, 200));
		$pdf->SetXY(135,185);
		$pdf->Cell(45, 8, 'Gerente de Mantenimiento :', '', 1, 'L', 0);
		$pdf->AddPage();
		$tbl = '<table cellspacing="1" cellpadding="1" border="1"><tr align="center" bgcolor="#cccccc"><td width="8%"><FONT SIZE=10><b>Cédula</b></FONT></td><td height="10" width="20%"><FONT SIZE=10><b>Nombre</b></FONT></td><td height="10" width="7%"><FONT SIZE=10><b>Hora Entrada</b></FONT></td><td height="10" width="10%"><FONT SIZE=10><b>Firma</b></FONT></td><td height="10" width="7%"><FONT SIZE=10><b>Hora Salida</b></FONT></td><td height="10" width="10%"><FONT SIZE=10><b>Firma</b></FONT></td><td height="10" width="35%"><FONT SIZE=10><b>Observación</b></FONT></td></tr>';
		$tbl1= '<tr><td height="10" width="8%" align="right"><FONT SIZE=8>'.$cedula.'</FONT></td><td height="10" width="20%"><FONT SIZE=8>'.$empleado.'</FONT></td><td height="10" width="7%" align="center"><FONT SIZE=8>'.$fechaini.'</FONT></td><td height="10" width="10%"><FONT SIZE=8><b></b></FONT></td><td height="10" width="7%"><FONT SIZE=8><b></b></FONT></td><td height="10" width="10%"><FONT SIZE=8><b></b></FONT></td>';
		$fila=0;
		$continua=1;
	}
	$fila++;
}

$tbl.= $tbl1.'<td width="35%"><FONT SIZE=7></FONT>'.$desactiv.'</td></tr>';
$tbl .='</table>';
$pdf->writeHTML($tbl, true, 0, true, 0);

//Creamos las casillas para las firmas de autorización
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(45, 8, 'Los funcionarios del área de mantenimiento  relacionados, tienen autorizado el ingreso a las instalaciones de la empresa el día '.$fecini, '', 1, 'L', 0);
$pdf->SetFont('helvetica', 'B', 8);
$pdf->RoundedRect(10, 185, 110, 15, 1.50, '0110', 'D',$style5, array(200, 200, 200));
$pdf->SetXY(10,185);
$pdf->Cell(45, 8, 'Gerente Administrativo y Financiero:', '', 1, 'L', 0);
$pdf->RoundedRect(130, 185, 110, 15, 1.50, '0110', 'D',$style5, array(200, 200, 200));
$pdf->SetXY(135,185);
$pdf->Cell(45, 8, 'Gerente de Mantenimiento :', '', 1, 'L', 0);

//Close and output PDF document
$pdf->Output('PROGOT.pdf', 'I');
?>
