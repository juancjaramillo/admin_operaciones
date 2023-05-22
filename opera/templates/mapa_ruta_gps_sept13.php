<html>
<head>

<title><?=$CFG->siteTitle?></title>
<script src="http://extjs.cachefly.net/ext-3.4.0/adapter/ext/ext-base.js" type="text/javascript"></script>
<script src="http://extjs.cachefly.net/ext-3.4.0/ext-all.js"  type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="http://extjs.cachefly.net/ext-3.4.0/resources/css/ext-all.css" />
<script src="http://www.openlayers.org/api/2.11/OpenLayers.js" type="text/javascript"></script>
<script src="http://api.geoext.org/1.1/script/GeoExt.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css"
      href="http://api.geoext.org/1.1/resources/css/geoext-all.css" />

<script src="<?=$CFG->wwwroot?>/lib/proj4js/lib/proj4js.js" type="text/javascript"></script>

<script type="text/javascript">

		var gpsStyle = new OpenLayers.StyleMap({
				// Set the external graphic and background graphic images.
				externalGraphic: "${graphic}",

				// Makes sure the background graphic is placed correctly relative
				// to the external graphic.
				backgroundXOffset: 0,
				backgroundYOffset: -5,

				// Set the z-indexes of both graphics to make sure the background
				// graphics stay in the background 
				graphicZIndex: 11,
				backgroundGraphicZIndex: 10,

				pointRadius: 7.5,
				rotation: "${rumbo}"
		});

     // global projection objects (uses the proj4js lib)
     var epsg_4326 = new OpenLayers.Projection("EPSG:4326"),
         epsg_900913 = new OpenLayers.Projection("EPSG:900913");

		function pgrouting(store) {
			store.removeAll();
			store.load({
				params: {
					id_vehiculo: '<?=$frm["id_vehiculo"]?>',
					desde: '<?=$fecha_desde?>',
					hasta: '<?=$fecha_hasta?>'
				}
			});
		}

    Ext.onReady(function() {
//	==========================================
 function fn_saveRoute(button,event){
// 	button.desactivar...
// 	console.log(route_layer);
	var strPoints="";
	var geom;
	for(i=0;i<route_layer.features.length;i++){
		geom=route_layer.features[i].geometry.transform(epsg_900913, epsg_4326);
		arrVertices=geom.getVertices();
		for(j=0;j<arrVertices.length;j++){
			if(strPoints!="") strPoints+=",";
			strPoints+=arrVertices[j].x.toString() + " " + arrVertices[j].y.toString();
		}
	}
	 Ext.Ajax.request({
			url : './pgrouting.php',
			method: 'POST',
//			params :{id_vehiculo:'<?=$frm["id_vehiculo"]?>', desde: '<?=$fecha_desde?>', hasta: '<?=$fecha_hasta?>'},
			params :{mode: 'save',id_micro: '<?=$frm["id_micro"]?>',points: strPoints},
			success: function ( result, request ) {
				window.location.href='<?=$ME?>?id_micro=<?=$frm["id_micro"]?>';
				/*
				var jsonData = Ext.util.JSON.decode(result.responseText);
				var resultMessage = jsonData.data.result;
				console.log(resultMessage);
				*/
			},
			failure: function ( result, request ) {
				var jsonData = Ext.util.JSON.decode(result.responseText);
				var resultMessage = jsonData.data.result;
			}
	 });
	
 }
//	==========================================



        // create the map panel
        var panel = new GeoExt.MapPanel({
            renderTo: "gxmap",
            map: {
                layers: [new OpenLayers.Layer.OSM("OSM")]
            },
//            center: [-11685000, 4827000],
            center: [-8250124,517550],
            zoom: 5,
            height: 400,
            width: 600,
            title: "Grabación de ruta desde GPS"
        });
        var map = panel.map;
				map.addControl(new OpenLayers.Control.LayerSwitcher());

        // create the layer where the route will be drawn
        var route_layer = new OpenLayers.Layer.Vector("route", {
            styleMap: new OpenLayers.StyleMap(new OpenLayers.Style({
                strokeColor: "#ff0000",
                strokeWidth: 3
            }))
        });

				var LayerGps=new OpenLayers.Layer.GML("Rastro de GPS", "../gps.php?id_vehi=<?=$vehiculo["idgps"]?>&desde=<?=$frm["fecha_desde"]?>&hasta=<?=$frm["fecha_hasta"]?>" + "&rand=" + Math.random() ,{styleMap: gpsStyle});
				LayerGps.events.register("loadend",LayerGps, function (e) {
					map.zoomToExtent(LayerGps.getDataExtent());
				});

        // add the layers to the map
        map.addLayers([LayerGps, route_layer]);

        // create the store to query the web service
        var store = new GeoExt.data.FeatureStore({
            layer: route_layer,
            fields: [
                {name: "length"}
            ],
            proxy: new GeoExt.data.ProtocolProxy({
                protocol: new OpenLayers.Protocol.HTTP({
                    url: "./pgrouting.php",
                    format: new OpenLayers.Format.GeoJSON({
                        internalProjection: epsg_900913,
                        externalProjection: epsg_4326
                    })
                })
            })
        });

				var form = new Ext.form.FormPanel({ //(1)

					renderTo: "divform", //(2)
					url: '<?=$ME?>',
					standardSubmit : true,
					frame: true,
					method: 'GET',
					width: 400,
					items: [
						{xtype:'hidden', name:'mode', value:'<?=$frm["newMode"]?>'}, 
						new Ext.form.ComboBox({
							fieldLabel: 'Ruta',
							hiddenName: 'id_micro',
							triggerAction: "all",
							width: 180,
							store: [["<?=$micro["id"]?>","<?echo $micro["centro"] . "/" . $micro["codigo"]?>"]],
							value: '<?=$micro["id"]?>'
						}),
						new Ext.form.ComboBox({
							fieldLabel: 'Vehículo',
							width: 180,
							hiddenName: 'id_vehiculo',
							triggerAction: "all",
							store: [<?while($veh=$db->sql_fetchrow($qVehiculos)) echo "[\"$veh[id]\", \"$veh[label]\"],";?>],
							value: '<?=nvl($frm["id_vehiculo"])?>'
						}),{
						xtype       : 'container',
						border      : false,
						layout      : 'column',
						anchor      : '100%',
						defaultType : 'field',
						fieldLabel: 'Desde',
						items       : [																														
							new Ext.form.DateField({
								format: 'Y-m-d',
								name: 'fecha_desde',
								value: '<?=date("Y-m-d",strtotime($frm["fecha_desde"]))?>',
								width:100
							}),
							new Ext.form.TimeField({
								name: 'hora_desde',
								format:"H:i:s",
								increment: 30,
								value: '<?=date("H:i:s",strtotime($frm["fecha_desde"]))?>',
								width:80
							})
						]
					},{
						xtype       : 'container',
						border      : false,
						layout      : 'column',
						anchor      : '100%',
						defaultType : 'field',
						fieldLabel: 'Hasta',
						items       : [																														
							new Ext.form.DateField({
								format: 'Y-m-d',
								name: 'fecha_hasta',
								value: '<?=date("Y-m-d",strtotime($frm["fecha_hasta"]))?>',
								width:100
							}),
							new Ext.form.TimeField({
								name: 'hora_hasta',
								format:"H:i:s",
								increment: 30,
								value: '<?=date("H:i:s",strtotime($frm["fecha_hasta"]))?>',
								width:80
							})
						]
					}

				],
				buttons: [{
					text: 'Guardar ruta',
					handler: fn_saveRoute
				},{
					text: 'Hacer ruta',
					handler: function() {
						pgrouting(store);
					}
				},{
					text: 'Recargar',
					handler: function() {
						form.getForm().submit();
					}
				},{
					text: 'Cancelar',
					handler: function() {
						if(window.opener && window.opener.focus) window.opener.focus();
						window.close();
					}
				}]
			 });
    });



</script>
</head>
<body>
<div id="gxmap"></div>
<div id="divform"></div>
</body>
</html>
