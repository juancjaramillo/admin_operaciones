<html>
    <head>
        <title>GeoExt FeatureStore in an Ext Grid</title>

        <script type="text/javascript" src="http://extjs.cachefly.net/ext-3.2.1/adapter/ext/ext-base.js"></script>
        <script type="text/javascript" src="http://extjs.cachefly.net/ext-3.2.1/ext-all.js"></script>
        <link rel="stylesheet" type="text/css" href="http://extjs.cachefly.net/ext-3.2.1/resources/css/ext-all.css" />
        <link rel="stylesheet" type="text/css" href="http://extjs.cachefly.net/ext-3.2.1/examples/shared/examples.css" />

        <script src="http://www.openlayers.org/api/2.10/OpenLayers.js"></script>
        <script type="text/javascript" src="script/GeoExt.js"></script>
        
        <script type="text/javascript">
					/**
					 * Copyright (c) 2008-2010 The Open Source Geospatial Foundation
					 * 
					 * Published under the BSD license.
					 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
					 * of the license.
					 */

					/** api: example[feature-grid]
					 *  Grid with Features
					 *  ------------------
					 *  Synchronize selection of features between a grid and a layer.
					 */

					var mapPanel, store, gridPanel, mainPanel;

					Ext.onReady(function() {
							// create map instance
							var map = new OpenLayers.Map();
							var wmsLayer = new OpenLayers.Layer.WMS(
									"vmap0",
									"http://vmap0.tiles.osgeo.org/wms/vmap0",
									{layers: 'basic'}
							);

							// create vector layer
							var vecLayer = new OpenLayers.Layer.Vector("vector");
							map.addLayers([wmsLayer, vecLayer]);

							// create map panel
							mapPanel = new GeoExt.MapPanel({
									title: "Map",
									region: "center",
									height: 400,
									width: 600,
									map: map,
									center: new OpenLayers.LonLat(5, 45),
									zoom: 6
							});
					 
							// create feature store, binding it to the vector layer
							store = new GeoExt.data.FeatureStore({
									layer: vecLayer,
									fields: [
											{name: 'name', type: 'string'},
											{name: 'elevation', type: 'float'}
									],
									proxy: new GeoExt.data.ProtocolProxy({
											protocol: new OpenLayers.Protocol.HTTP({
													url: "http://api.geoext.org/1.0/examples/data/summits.json",
													format: new OpenLayers.Format.GeoJSON()
											})
									}),
									autoLoad: true
							});

							// create grid panel configured with feature store
							gridPanel = new Ext.grid.GridPanel({
									title: "Feature Grid",
									region: "east",
									store: store,
									width: 320,
									columns: [{
											header: "Name",
											width: 200,
											dataIndex: "name"
									}, {
											header: "Elevation",
											width: 100,
											dataIndex: "elevation"
									}],
									sm: new GeoExt.grid.FeatureSelectionModel() 
							});

							// create a panel and add the map panel and grid panel
							// inside it
							mainPanel = new Ext.Panel({
									renderTo: "mainpanel",
									layout: "border",
									height: 500,
									width: "100%",
									items: [mapPanel, gridPanel]
							});
					});

				</script>

    </head>
    <body style="padding:0px;margin:0px;height:100%;">
			<div style="height:100%;background-color:#ff0000">
        <div id="mainpanel" style="height:100%;"></div>
			</div>
    </body>
</html>

