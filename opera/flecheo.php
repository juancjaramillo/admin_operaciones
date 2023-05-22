<?
include("../application.php");

//****** Obtiene el "codigo de relleno" desde la tabla micros *****//
$micro = $db->sql_row("SELECT cod_relleno FROM micros WHERE id=".$_GET["id_micro"]);

//****** Obtiene el punto máximo en X y Y para centrar el mapa *****//
$extents=$dbtrabajogis->sql_row("
	SELECT min(ST_XMin(geom)) as minx, max(ST_XMax(geom)) as maxx,
		min(ST_YMin(geom)) as miny, max(ST_YMax(geom)) as maxy
	FROM flecheo_detalle where codmicro='".$micro["cod_relleno"]."'");

?>
<html>
  <head>
    <title>Flecheo MAP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.16.0/ol.css" type="text/css">
 <script src="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.16.0/ol.js"></script>
  </head>
  <body>
     <div id="map" class="map"></div>
    <script>
var vectorSource = new ol.source.Vector({});
var vectorSourcePoint = new ol.source.Vector({});
        var style = new ol.style.Style({
            image: new ol.style.Circle({
                radius: 4,
                fill: new ol.style.Fill({
                    color: '#ffa500'
                }),
                stroke: new ol.style.Stroke({
                    color: 'red',
                    width: 0.5
                })
            })
        });
        
  var styleFunction = function(feature) {
  var geometry = feature.getGeometry();
  
    var styles = [
    // linestring
    new ol.style.Style({
      stroke: new ol.style.Stroke({
        color: '#ffcc33',
        width: 2
      })
    })
  ];
  
var lineStringsArray = geometry.getLineStrings();
for(var i=0;i<lineStringsArray.length;i++){
	
  //****** Dibuja la flecha al final de cada segmento *****//
  
  lineStringsArray[i].forEachSegment(function(start, end) {
    var dx = end[0] - start[0];
    var dy = end[1] - start[1];
    var rotation = Math.atan2(dy, dx);
    styles.push(new ol.style.Style({
      geometry: new ol.geom.Point(end),
      image: new ol.style.Icon({
		   
        src: '<?echo $CFG->wwwroot."/images/arrow.png";?>',
		 anchor: [0.75, 0.5],
        rotateWithView: false,
        rotation: -rotation
      })
    }));
  });
}


  return styles;
};

var map = new ol.Map({
  layers: [
      new ol.layer.Tile({
      source: new ol.source.OSM()
      }),
      new ol.layer.Vector({
          source: vectorSource,
          style:styleFunction
      }),
	  new ol.layer.Vector({
          source: vectorSourcePoint,
		  style: style
      })
  ],
  target: 'map',
  view: new ol.View({  
  
  <? if (!empty($extents["minx"])){?>
	 center: ol.proj.transform([<?=$extents["minx"]?>, <?=$extents["miny"]?>], 'EPSG:4326', 'EPSG:3857'),
	 zoom: 17
    <?}else{?>
	center: ol.proj.transform([-74.03509643054903, 4.729596127229558], 'EPSG:4326', 'EPSG:3857'),	
	zoom: 13
<?}?> 
  })
  
});
<?
 //****** BUSCA CADA POLILINEA POR CADA CODIGO DE RELLENO *****//
	 $qid = $dbtrabajogis->sql_query("SELECT ST_X(ST_StartPoint(geom)),ST_y(ST_StartPoint(geom)), ST_AsText(geom),geom
		FROM public.flecheo_detalle where codmicro='".$micro["cod_relleno"]."' ");
	echo $dbtrabajogis->sql_fetchrow($qid);
	$rows = pg_num_rows($qid);
	//echo "XXX".$rows;
	/* if ($dbtrabajogis->sql_fetchrow($qid)>0)
{
print("Exite al menos un registro");
} else {
print("No Existen registros");
}
 */
	
	while($frec = $dbtrabajogis->sql_fetchrow($qid)){ ?>	

<?
//****** LIMPIA EL CAMPO st_astext ELIMINANDO LOS PARENTESIS () Y LA PALABRA "LINESTRING"  *****//
	  $limpiarcampo=$frec["st_astext"];  
      $limpiarcampo = str_replace(
        array("(", ")","LINESTRING"),
        '',
        $limpiarcampo
    );  
	$nodos = explode(",", $limpiarcampo);
	$contador=count($nodos)-1;
	$nodos2 = explode(" ", $nodos[0]);
?>
	var points1=[];
	var points2=[];	
	<?
		for ($i = 0; $i < $contador; $i++) { 	
		$nodoa = explode(" ", $nodos[$i]);	
		$nodob = explode(" ", $nodos[$i+1]);
	?>	
		points1.push(ol.proj.transform([<?=$nodoa[0]?>,<?=$nodoa[1]?>],'EPSG:4326', 'EPSG:3857'));	
		points2.push(ol.proj.transform([<?=$nodob[0]?>,<?=$nodob[1]?>],'EPSG:4326', 'EPSG:3857'));
	<?}?>
	
		var thing = new ol.geom.MultiLineString([points1,points2]);
		var featurething = new ol.Feature({
		name: "Thing",
		geometry: thing,
		style : new ol.style.Style({
		stroke : new ol.style.Stroke({
        color : 'red'
      })
    })
});
vectorSource.addFeature( featurething);

	<?}?>	
    </script>
  </body>
</html>
