<?

/*	----------------------------------------------	*/
/*						MAIN						*/
/*	----------------------------------------------	*/


/************************************************************/
/***Cargas el mapa indicando la ubicaci�n de cada cliente***/
/******** Se inicializa en el archivo application************/
/***********************************************************/




include("application.php");
if(!isset($_SESSION[$CFG->sesion]["user"])){
	$errorMsg="No existe la sesi�n.";
	error_log($errorMsg);
	die($errorMsg);
}
$user=$_SESSION[$CFG->sesion]["user"];
if($user["nivel_acceso"]!=1){//No es admin
	$condicion="cl.id_centro IN (" . implode(",",$user["id_centro"]) . ")";
}
else $condicion="true";

#$extents=$db->sql_row("SELECT min(ST_XMin(cl.the_geom)) as minx, max(ST_XMax(cl.the_geom)) as maxx,
#		min(ST_YMin(cl.the_geom)) as miny, max(ST_YMax(cl.the_geom)) as maxy FROM catastro cl ");

$extents=$db->sql_row("SELECT -74.173976 as minx, -74.024752 as maxx,
		4.495615 as miny,4.758483 as maxy FROM catastro cl");
?>
<html xmlns="https://www.w3.org/1999/xhtml">
  <head>
		<title><?=$CFG->siteTitle?> / AVL</title>
		<style type="text/css">
			#map {
				width: 99%;
				height: 99%;
				border: 1px solid black;
			}
			body {
				padding:0px; 
				margin:0px
			}
			#labs {
				position:absolute;
				bottom:15px;
				left:7px;
				font-size:smaller;
				z-index: 5000;
			}	 
			#info {
				position:absolute;
				bottom:15px;
				left:7px;
				right:7px;
				font-size:smaller;
				text-align:center;
				z-index: 6000;
				visibility:hidden;
			}	 
			#pa {
				position:absolute;
				top:15px;
				right:15px;
				font-size:smaller;
				z-index: 5000;
			}
    </style>
		<link rel="stylesheet" type="text/css" href="<?=$CFG->wwwroot?>/lib/yui2-master/build/reset-fonts-grids/reset-fonts-grids.css" />
		<link rel="stylesheet" type="text/css" href="<?=$CFG->wwwroot?>/lib/yui2-master/build/resize/assets/skins/sam/resize.css" />
		<link rel="stylesheet" type="text/css" href="<?=$CFG->wwwroot?>/lib/yui2-master/build/layout/assets/skins/sam/layout.css" />
		<link rel="stylesheet" type="text/css" href="<?=$CFG->wwwroot?>/lib/yui2-master/build/button/assets/skins/sam/button.css" />
		<link rel="stylesheet" type="text/css" href="<?=$CFG->wwwroot?>/lib/yui2-master/build/tabview/assets/skins/sam/tabview.css" />
		<link rel="stylesheet" type="text/css" href="<?=$CFG->wwwroot?>/lib/yui2-master/build/datatable/assets/skins/sam/datatable.css" />
		<link rel="stylesheet" type="text/css" href="<?=$CFG->wwwroot?>/lib/yui2-master/build/menu/assets/skins/sam/menu.css" />

		<script type="text/javascript" src="<?=$CFG->wwwroot?>/lib/yui2-master/build/yahoo/yahoo-min.js"></script>
		<script type="text/javascript" src="<?=$CFG->wwwroot?>/lib/yui2-master/build/event/event-min.js"></script>
		<script type="text/javascript" src="<?=$CFG->wwwroot?>/lib/yui2-master/build/dom/dom-min.js"></script>
		<script type="text/javascript" src="<?=$CFG->wwwroot?>/lib/yui2-master/build/element/element-min.js"></script>
		<script type="text/javascript" src="<?=$CFG->wwwroot?>/lib/yui2-master/build/dragdrop/dragdrop-min.js"></script>
		<script type="text/javascript" src="<?=$CFG->wwwroot?>/lib/yui2-master/build/resize/resize-min.js"></script>
		<script type="text/javascript" src="<?=$CFG->wwwroot?>/lib/yui2-master/build/animation/animation-min.js"></script>
		<script type="text/javascript" src="<?=$CFG->wwwroot?>/lib/yui2-master/build/layout/layout-min.js"></script>
		<script type="text/javascript" src="<?=$CFG->wwwroot?>/lib/yui2-master/build/tabview/tabview-min.js"></script>
		<script src="<?=$CFG->wwwroot?>/lib/yui2-master/build/yahoo-dom-event/yahoo-dom-event.js"></script>
		<script src="<?=$CFG->wwwroot?>/lib/yui2-master/build/container/container_core-min.js"></script>
		<script src="<?=$CFG->wwwroot?>/lib/yui2-master/build/menu/menu-min.js"></script>
		<script src="<?=$CFG->wwwroot?>/lib/yui2-master/build/event-delegate/event-delegate-min.js"></script>
		<script src="<?=$CFG->wwwroot?>/lib/yui2-master/build/datasource/datasource-min.js"></script>
		<script src="<?=$CFG->wwwroot?>/lib/yui2-master/build/datatable/datatable-min.js"></script>

		<script src="js/OpenLayers-2.12-rc7/lib/OpenLayers.js"></script>

		<link rel="stylesheet" type="text/css" href="https://extjs.cachefly.net/ext-3.4.0/resources/css/ext-all.css">
		<script type="text/javascript" src="https://extjs.cachefly.net/ext-3.4.0/adapter/ext/ext-base.js"></script>
		<script type="text/javascript" src="https://extjs.cachefly.net/ext-3.4.0/ext-all.js"></script>

    <script type="text/javascript">
        <!--
        var map, layer =null;
		var popup=null;
		var contador=0;
		var dataSource;
		var myColumnDefs;
		var myDataTable;
		var selectControl;
		var testControl;
		var lastFeature = null;
		var tooltipPopup = null;
		var detailPopup=null;
		var replayCont;
		var replayTimer;
		var replayFeatures;
		var replayPlaying=false;
		var replayStarted=false;
		var resumenOperacion=0;

		var myStyles = new OpenLayers.StyleMap({
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

				label : "${name}",
				fontSize: "12px",
				fontFamily: "Courier New, monospace",
				fontWeight: "bold",
				labelYOffset: 12
		});

		function onPopupClose(evt) {
			selectControl.unselect(selectedFeature);
			if(popup!=undefined && popup!=null) popup = null;
		}

		function onFeatureUnselect(feat) {
			if(popup!=undefined && popup!=null){
				map.removePopup(popup);
				popup.destroy();
				popup = null;
			}
		}
		
        function init(){	
			var maxBounds=new OpenLayers.Bounds(-79.10, -4.30, -66.90, 12.50);
			var bounds=new OpenLayers.Bounds(<?=$extents["minx"]?>, <?=$extents["miny"]?>, <?=$extents["maxx"]?>, <?=$extents["maxy"]?>);
			var osm = new OpenLayers.Layer.OSM("Cartografia OSM",null,{ "tileOptions": {"crossOriginKeyword": null }});
			//var ghyb = new OpenLayers.Layer.Google("Cartograf�a Google",{type: google.maps.MapTypeId.HYBRID, numZoomLevels: 20});
			var maxBounds900913=maxBounds.transform(new OpenLayers.Projection("EPSG:4326"), new OpenLayers.Projection("EPSG:900913"));
			var options = {displayProjection: new OpenLayers.Projection("EPSG:4326"),restrictedExtent: maxBounds}

			map = new OpenLayers.Map( $('map'), options);

			map.addLayer(osm);
			//map.addLayer(ghyb);
			map.projection='EPSG:4326';
			map.addControl(new OpenLayers.Control.MousePosition());
			map.addControl(new OpenLayers.Control.LayerSwitcher());

			var puntoStyle = new OpenLayers.Style({
				strokeColor: '${strokecolor}',
				strokeWidth: 1,
				pointRadius: 3,
				fillColor: "#ffcc66" 
			});
			var catastroStyle = new OpenLayers.Style({
				strokeColor: '${strokecolor}',
				strokeWidth: 1,
				pointRadius: 3,
				fillColor: "#cc66ff" 
			});
			var poligonoStyle = new OpenLayers.Style({
				strokeColor: '${strokecolor}',
				strokeWidth: 1,
				fillColor: '${strokecolor}' 
			});
					
			recoleccionMacrosLayer = new OpenLayers.Layer.Vector("Recoleccion Macros", {
				protocol: new OpenLayers.Protocol.HTTP({
						url: "layer_rec_macros_pol.php?rand=" + Math.random(),
						format: new OpenLayers.Format.GML()
				}),
				strategies: [new OpenLayers.Strategy.Fixed()],
				styleMap: poligonoStyle,
				visibility:false
			});
			
			recoleccionLayer = new OpenLayers.Layer.Vector("Recoleccion Micros", {
				protocol: new OpenLayers.Protocol.HTTP({
						url: "layer_rec_micros_pol.php?rand=" + Math.random(),
						format: new OpenLayers.Format.GML()
				}),
				strategies: [new OpenLayers.Strategy.Fixed()],
				styleMap: poligonoStyle,
				visibility:false
			});
		  
      nopapLayer = new OpenLayers.Layer.Vector("No Puerta a Puerta", {
        protocol: new OpenLayers.Protocol.HTTP({
            url: "layer_catastro_no_pap.php?rand=" + Math.random(),
            format: new OpenLayers.Format.GML()
        }),
        strategies: [new OpenLayers.Strategy.Fixed()],
        styleMap: catastroStyle,
        visibility:false
      });

	
			map.addLayer(recoleccionLayer);
			map.addLayer(recoleccionMacrosLayer);
      map.addLayer(nopapLayer);

			var delayInMilliseconds = 30000; //1 second
			<?
			$layersAd = array();
			$qidPC = $db->sql_query("SELECT DISTINCT (alcaldia) as alcaldia FROM catastro order by 1");
			while($pint = $db->sql_fetchrow($qidPC))
			{
				
				$layersAd[] = "Layer_int_".$pint["alcaldia"];
			?>
				Layer_int_<?=$pint["alcaldia"]?>=new OpenLayers.Layer.Vector("<?="Localidad ".$pint["alcaldia"]?>", {
					protocol: new OpenLayers.Protocol.HTTP({
							url: "layer_catastro.php?alcaldia=<?=$pint["alcaldia"]?>&rand=" + Math.random(),
							format: new OpenLayers.Format.GML()
					}),
					strategies: [new OpenLayers.Strategy.Fixed()],
					styleMap: catastroStyle,
					<?if($i==0){?>
						visibility:true
					<?}else {?>
						visibility:false
					<?}$i++;?>
				});
				
			<?}
			$i=0;
			foreach($layersAd as $ad)
			{?>
				map.addLayer(<?=$ad?>);
			<?}?>
			
			//map.addLayer(catastroLayer);
						
			selectControl = new OpenLayers.Control.SelectFeature(<?=$ad?>,{clickout: true, toggle: false,multiple: false, hover: false});
			map.addControl(selectControl);
			selectControl.activate();
			<?=$ad?>.events.on({
				"featureselected": function(e) {onFeatureSelect(e.feature);},
				"featureunselected": function(e) {onFeatureUnselect(e.feature);}
			});

			map.zoomToExtent(bounds.transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject()),false);

        }
				

		function switchLayer(msLayer){
			if(LayerClientes.params.LAYERS==msLayer){
				if(!LayerClientes.drawn){
					map.addLayer(LayerClientes);
					LayerClientes.drawn=true;
				}
				return;
			}
			LayerClientes.params.LAYERS=msLayer;
			if(LayerClientes.drawn) LayerClientes.redraw();
			else{
				LayerClientes.drawn=true;
			}
		}
        // -->
    </script>
  </head>
  <body class="yui-skin-sam" onload="init()">
	<div id="right1">
	<form>
		<input onClick="switchLayer(this.value)" type="radio" name="class" value="clientesnopap" id="class_1"> <label for="class_1">No Puerta a Puerta</label><br>
	</form>
	</div>
	<div id="center1" style="height:100%">
    <div id="map">
	    <div id="labs"><a target="_NEW" href="https://apli-k.com"><img src="images/apli-k.png" border="0"></a></div>
	    <div id="info">
				<table border="0" cellspacing="0" cellpadding="0" align="center">
					<tr>
						<td width="10"><img src="images/borderUL.gif"></td>
						<td background="images/borderT.gif"></td>
						<td width="10"><img src="images/borderUR.gif"></td>
					</tr>
					<tr>
						<td background="images/borderL.gif"></td>
						<td id="info_cell" align="center" bgcolor="#004457" style="color:#FFFFFF"></td>
						<td background="images/borderR.gif"></td>
					</tr>
					<tr>
						<td width="10"><img src="images/borderBL.gif"></td>
						<td background="images/borderB.gif"></td>
						<td width="10"><img src="images/borderBR.gif"></td>
					</tr>
				</table>
			</div>
    </div>
	</div>

	<script>
	(function() {
		var Dom = YAHOO.util.Dom,
		Event = YAHOO.util.Event;

		Event.onDOMReady(function() {
			var layout = new YAHOO.widget.Layout({
				units: [
					{ position: 'center', body: 'center1' }
				]
			});
			layout.on('render', function() {
				layout.getUnitByPosition('left').on('close', function() {
					closeLeft();
				});
			});
			layout.render();
		});
	})();
	</script>
  </body>
</html>
