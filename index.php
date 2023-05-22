<?
/*	----------------------------------------------	*/
/*						MAIN						*/
/*	----------------------------------------------	*/


/************************************************************/
/***Cargas las capas layers (en mapas)y el menu de ingreso***/
/******** Se inicializa en el archivo application************/
/***********************************************************/



include("application.php");
$user_extents = (isset($_SESSION[$CFG->sesion]["user"]["id"])) ? $_SESSION[$CFG->sesion]["user"]["id"] : 0;
$extents=$db->sql_row("
	SELECT min(ST_XMin(the_geom)) as minx, max(ST_XMax(the_geom)) as maxx,
		min(ST_YMin(the_geom)) as miny, max(ST_YMax(the_geom)) as maxy
	FROM vehiculos WHERE vehiculos.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user_extents."'
)");
?>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <style type="text/css">
        #map {
            width: 100%;
            height: 99%;
            border: 1px solid black;
        }
	body {
	  padding:0px;
	  margin:2px
	}
	#labs {
	 position:absolute;
	 bottom:15px;
	 left:7px;
	 font-size:smaller;
	 z-index: 5000;
        }
	#pa {
	 position:absolute;
	 top:15px;
	 right:15px;
	 font-size:smaller;
	 z-index: 5000;
        }
    </style>
    <script src="lib/OpenLayers/OpenLayers.js"></script>
    <script type="text/javascript">
        <!--
        var map, layer, LayerVehiculos;

				var myStyles = new OpenLayers.StyleMap({
								// Set the external graphic and background graphic images.
//								externalGraphic: "./images/taxi.png",
								externalGraphic: "${graphic}",
								backgroundGraphic: "./images/marker_shadow.png",

								// Makes sure the background graphic is placed correctly relative
								// to the external graphic.
								backgroundXOffset: 0,
								backgroundYOffset: -7,

								// Set the z-indexes of both graphics to make sure the background
								// graphics stay in the background
								graphicZIndex: 11,
								backgroundGraphicZIndex: 10,

								pointRadius: 10,

								label : "${placa}",
								fontSize: "12px",
								fontFamily: "Courier New, monospace",
								fontWeight: "bold",
								labelYOffset: 12
				});

				function recargar(){
/
					LayerVehiculos.setUrl("point.php?rand=" + Math.random());
					timerRecargar=setTimeout ( "recargar()", 10000 );
				}

        function init(){
					var maxBounds=new OpenLayers.Bounds(-79.10, -4.30, -66.90, 12.50);
					var bounds=new OpenLayers.Bounds(<?=$extents["minx"]?>, <?=$extents["miny"]?>, <?=$extents["maxx"]?>, <?=$extents["maxy"]?>);
					var osm = new OpenLayers.Layer.OSM();

					var maxBounds900913=maxBounds.transform(new OpenLayers.Projection("EPSG:4326"), new OpenLayers.Projection("EPSG:900913"));

					map = new OpenLayers.Map( $('map'), {restrictedExtent: maxBounds});
					layer = new OpenLayers.Layer.WMS( "osm",
						"http://<?=$_SERVER["HTTP_HOST"]?>/cgi-bin/tilecache/tilecache.cgi?", {layers: 'osm', format: 'image/png' }, {transitionEffect: 'resize'}
					);

//					map.addLayer(layer);
					map.addLayer(osm);
					map.projection='EPSG:4326';

					LayerVehiculos=new OpenLayers.Layer.GML("GML", "point.php?rand=" + Math.random() ,{styleMap: myStyles});
					map.addLayer(LayerVehiculos);
					map.zoomToExtent(bounds.transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject()),false);

        }
				timerRecargar=setTimeout ( "recargar()", 10000 );
        // -->
    </script>
  </head>
  <body onload="init()">
    <div id="map">
    <div id="labs"><a target="_NEW" href="http://apli-k.com"><img src="images/apli-k.png" border="0"></a></div>
    <div id="pa"><img src="images/logo1.png"></div>
    </div>
  </body>
</html>
