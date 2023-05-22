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

function ChapterBody($file, $micro)
{
	$codigo = $micro['codigo'];
	$mapa = 'tmp/'.$codigo.'.jpg';
	$bbox = $this->GetBbox($micro['xmin'],$micro['xmax'],$micro['ymin'],$micro['ymax']);
	$image_url = "http://barrido.promoambientaldistrito.com:8080/geoserver/promo/wms?service=WMS&version=1.1.0&request=GetMap&layers=promo:mvi,promo:manz,promo:barrido_poligono_noche,promo:cestas,promo:inifin,promo:barrido_flecheo_noche&bbox=".$bbox."&width=900&height=695&srs=EPSG:4686&format=image/jpeg&cql_Filter=mostrar=1;mostrar=1;codigo=".$codigo.";mostrar=1;codmicro=".$codigo.";codigo=".$codigo;
	$this->download_image1($image_url, $mapa);
//	$this->Image('test1.jpg',10,8,33)
	$this->Cell(259.5,155, $this->Image($mapa, $this->GetX(), $this->GetY(),259.5,155),1);
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
    $this->Cell(129.5,8,'Microrrutas de BARRIDO MANUAL',1,0,'C');
$this->SetY($this->GetY()+8);
$this->SetX($this->GetX()+130);
    $this->SetFont('','I');
    $this->Cell(129.5,6,'Localidad : ' . $micro['localidad'] . ' | Cuartelillo ' . $micro['sup_cod'],1);
$this->SetY($this->GetY()+6);
$this->SetX($this->GetX()+130);
    $this->Cell(60,6,'Dias : ' . $micro['dias'],1);
$this->GetX();
    $this->Cell(69.5,6,'Horario : ' . $micro['horario'],1);
$this->SetY($this->GetY()+6);
$this->SetX($this->GetX()+130);
    $this->Cell(25,10,'Macro : ' . $micro['macro'],1);
$this->GetX();
    $this->Cell(55,10,'Micro : ' . $micro['micro'].'  [ '. $micro['km_bordillo'].' ]',1);
$this->SetFont('Arial','B',26);
    $this->Cell(49.5,10,$micro['codigo'],1,1,'C');
}

function PrintChapter($num, $title, $file, $micro)
{
    $this->AddPage();
    //$this->ChapterTitle($num,$title);
    $this->ChapterBody($file, $micro);
}

function GetBbox($xmin, $xmax, $ymin, $ymax)
{
	if(($ymax - $ymin)>($xmax - $xmin))
	{		

		$dy = ($ymax - $ymin)*0.05;
		$y = ($ymax - $ymin)*1.1; //holgura del 10%
		$ymax = $ymax + $dy;  
		$ymin = $ymin - $dy;  
		$x = $y*1.294; // relacion de aspecto tamaño carta
		$dx = ($x - ($xmax - $xmin))/2;
		$xmax = $xmax + $dx;  
		$xmin = $xmin - $dx;  
	}	
	else
	{
		$dx = ($xmax - $xmin)*0.025;
		$x = ($xmax - $xmin)*1.05;
		$xmax = $xmax + $dx;  
		$xmin = $xmin - $dx;  
		$y = $x*0.7727;
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
