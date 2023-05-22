<?php
require('fpdf.php');

class PDF extends FPDF
{
function Header()
{
    global $title;

    // Arial bold 15
    $this->SetFont('Arial','B',15);
    // Calculamos ancho y posición del título.
    $w = $this->GetStringWidth($title)+6;
    //$this->SetX((210-$w)/2);
    // Colores de los bordes, fondo y texto
    $this->SetDrawColor(0,80,180);
    $this->SetFillColor(230,230,0);
    $this->SetTextColor(220,50,50);
    // Ancho del borde (1 mm)
    $this->SetLineWidth(1);
    // Título
    //$this->Cell($w,9,$title,1,1,'C',true);
    // Salto de línea
    //$this->Ln(10);
}

function Footer()
{
    // Posición a 1,5 cm del final
    $this->SetY(-15);
    // Arial itálica 8
    $this->SetFont('Arial','I',8);
    // Color del texto en gris
    $this->SetTextColor(128);
    // Número de página
    $this->Cell(0,10,'Hoja '.$this->PageNo(),0,0,'C');
}

function ChapterTitle($num, $label)
{
    // Arial 12
    $this->SetFont('Arial','',12);
    // Color de fondo
    $this->SetFillColor(200,220,255);
    // Título
    $this->Cell(0,6,"Capítulo $num : $label",0,1,'L',true);
    // Salto de línea
    $this->Ln(14);
}

function ChapterBody($file, $micro, $version)
{
	$capa = 'recoleccion_poligono_t1_0600_1400'.$version;
	$flecheo = 'recoleccion_flecheo_t1_0600_1400'.$version;
	$inifin = 'recoleccion_inifin_t1_0600_1400'.$version;
	$codigo = $micro['codigo'];
	$mapa = 'tmp/'.$codigo.'.jpg';
	$pxAncho = 259.5;
	$pxAlto = 155;
	$rarea = $pxAncho/$pxAlto;
	$bbox = $this->GetBbox($micro['xmin'],$micro['xmax'],$micro['ymin'],$micro['ymax'],$rarea);
    $image_url = "http://barrido.promoambientaldistrito.com:8080/geoserver/promo/wms?service=WMS&version=1.1.0&request=GetMap"
        . "&bbox=".$bbox."&width=900&height=695&srs=EPSG:4686&format=image/jpeg"
        . "&layers="
        . "promo:mvi,promo:".$capa.",promo:manz,promo:".$inifin.",promo:".$flecheo
        . "&cql_Filter="
        . "mostrar=1;idruta=".$codigo.";mostrar=1;codigo=".$codigo.";codigo=".$codigo
        . "";
die($image_url);
	$this->download_image1($image_url, $mapa);
//	$this->Image('test1.jpg',10,8,33)
	$this->Cell($pxAncho,$pxAlto, $this->Image($mapa, $this->GetX(), $this->GetY(),$pxAncho,$pxAlto),1);
    $this->Ln();
    // Times 12
    $this->SetFont('Arial','B',12);
    // Imprimimos el texto justificado
    $this->Cell(130,30,$this->Image('logo_150.png', $this->GetX()+5, $this->GetY()+2,33).$file.'   ',1,1,'R');
$this->SetY($this->GetY()-30);
$this->SetX($this->GetX()+130);
    // Salto de línea
    // Cita en itálica
    $this->SetFont('Times','B',12);
    $this->Cell(129.5,8,'Microrrutas de RECOLECCIÓN DE '.mb_convert_encoding($micro['tipo'],'UTF-8','auto'),1,0,'C');
$this->SetY($this->GetY()+8);
$this->SetX($this->GetX()+130);
    $this->SetFont('','I');
    $this->Cell(129.5,6,'Localidad : ' . $micro['localidad'] . ' | Supervisor ' . $micro['supervisor'],1);
$this->SetY($this->GetY()+6);
$this->SetX($this->GetX()+130);
    $this->Cell(60,6,'Dias : ' . $micro['dias'],1);
$this->GetX();
    $this->Cell(69.5,6,'Horario : ' . $micro['horario'],1);
$this->SetY($this->GetY()+6);
$this->SetX($this->GetX()+130);
    $this->Cell(25,10,'Macro : ' . $micro['macro'],1);
$this->GetX();
    $this->Cell(65,10,'Micro : ' . $micro['micro'].'  [ '. $micro['version'].' ]',1);
$this->SetFont('Arial','B',26);
    $this->Cell(39.5,10,$micro['codigo'],1,1,'C');
}

function PrintChapter($num, $title, $file, $micro, $version)
{
    $this->AddPage();
    //$this->ChapterTitle($num,$title);
    $this->ChapterBody($file, $micro, $version);
}

function GetBbox($xmin, $xmax, $ymin, $ymax, $rarea)
{
	$relacion = (abs($xmax - $xmin)/abs($ymax - $ymin));
	if($relacion <= $rarea)
	{		
		$dy = ($ymax - $ymin)*0.05;
		$y = ($ymax - $ymin)*1.1; //holgura del 10%
		$ymax = $ymax + $dy;  
		$ymin = $ymin - $dy;  
		$x = $y*1.674; // relacion de aspecto tamaño carta
		$dx = ($x - ($xmax - $xmin))/2;
		$xmax = $xmax + $dx;  
		$xmin = $xmin - $dx;  
	}	
	else
	{
		$dx = ($xmax - $xmin)*0.05;
		$x = ($xmax - $xmin)*1.1;
		$xmax = $xmax + $dx;  
		$xmin = $xmin - $dx;  
		$y = $x/$rarea;
		$dy = ($y - ($ymax - $ymin))/2;	
		$ymax = $ymax + $dy;  
		$ymin = $ymin - $dy;  
	}
	$bbox = $xmin.','.$ymin.','.$xmax.','.$ymax;

	return $bbox;
	
}
// takes URL of image and Path for the image as parameter
function download_image1($image_url, $image_file){
    $fp = fopen ($image_file, 'w+');              // open file handle

    $ch = curl_init($image_url);
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // enable if you want
    curl_setopt($ch, CURLOPT_FILE, $fp);          // output to file
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1000);      // some large value to allow curl to run for a long time
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    // curl_setopt($ch, CURLOPT_VERBOSE, true);   // Enable this line to see debug prints
    curl_exec($ch);

    curl_close($ch);                              // closing curl handle
    fclose($fp);                                  // closing file handle
}
}

?>
