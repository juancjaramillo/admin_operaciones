<?  
//phpinfo();

$image_url = "http://barrido.promoambientaldistrito.com:8080/geoserver/promo/wms?service=WMS&version=1.1.0&request=GetMap&layers=promo:mvi,promo:manz,promo:barrido_poligono,promo:cestas,promo:inifin,promo:barrido_flecheo&bbox=-74.06676,4.62558,-74.06015,4.63430&width=800&height=768&srs=EPSG:4686&format=image/png&cql_Filter=mostrar=1;mostrar=1;codigo=610523;mostrar=1;codmicro=610523;codigo=610523";
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $image_url);
// Getting binary data
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
$image = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);


if($httpCode == 200)//si esta la imagen
{
	header("Content-type: image/jpeg");
	echo $image;
}
else //no esta mostramos una imagen de default del domnio local
{
	
	$nombreImagen = 'thum.jpg';  

       //Cargamos la imagen en formato JPEG  

	$imagen = imagecreatefromjpeg($nombreImagen);  

	//Enviamos la cabecera Content-Type  

	header('Content-Type: image/jpeg');  

	//Enviamos la imagen al navegador  

	imagejpeg($imagen);  

	//Destruimos la imagen  

	 imagedestroy($imagen);  
}

?>
