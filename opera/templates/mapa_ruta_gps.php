<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
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


var epsg_4326 = new OpenLayers.Projection("EPSG:4326"),
	epsg_900913 = new OpenLayers.Projection("EPSG:900913");

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

function complementarRuta(store,storeAux){
	Ext.MessageBox.show({
		msg: 'Calculando arcos de la ruta ...',
		title: 'Por favor espere...',
		progress: true,
		closable: false,
		width:300,
		wait:true,
		waitConfig: {interval:200}
	});

	storeAux.removeAll();
	storeAux.load({
		params: {
			id_vehiculo: Ext.getCmp('id_vehiculo').value,
			desde: Ext.getCmp('fecha_desde').value + ' ' + Ext.getCmp('hora_desde').value,
			hasta: Ext.getCmp('fecha_hasta').value + ' ' + Ext.getCmp('hora_hasta').value
		},
		callback: function(){
			Ext.MessageBox.hide();
		}
	});
}

function pgrouting(store) {
	Ext.MessageBox.show({
		msg: 'Calculando arcos de la ruta ...',
		title: 'Por favor espere...',
		progress: true,
		closable: false,
		width:300,
		wait:true,
		waitConfig: {interval:200}
	});

	store.removeAll();
	store.load({
		params: {
			id_vehiculo: Ext.getCmp('id_vehiculo').value,
			desde: Ext.getCmp('fecha_desde').value + ' ' + Ext.getCmp('hora_desde').value,
			hasta: Ext.getCmp('fecha_hasta').value + ' ' + Ext.getCmp('hora_hasta').value
		},
		callback: function(){
			Ext.MessageBox.hide();
		}
	});
}



var mapPanel, store, gridPanel, mainPanel, storePC;

Ext.onReady(function() {

	//vars para los puntos de control
	var agregarParadero=0;
	var nomParadero="";
	var horaParadero="";


	function fn_saveRoute(button,event){
		var strPoints="";
		var geom;
		var arrayFeatures=new Array();

		Ext.MessageBox.show({
			msg: 'Guardando ruta ...',
			title: 'Por favor espere...',
			progress: true,
			closable: false,
			width:300,
			wait:true,
			waitConfig: {interval:200}
		});

		for(i=0;i<route_layer.features.length;i++){
			geom=route_layer.features[i].geometry.components[0].transform(epsg_900913, epsg_4326);
			feature=new Object();
			feature.geometry=geom.toString();
			feature.id=route_layer.features[i].data.id;
			feature.nombre=route_layer.features[i].data.name;
			arrayFeatures[i]=feature;
			route_layer.features[i].geometry.transform(epsg_4326, epsg_900913);
		}
	 	Ext.Ajax.request({
			url : './pgrouting.php',
			method: 'POST',
			params :{mode: 'save',id_micro: '<?=$frm["id_micro"]?>',features: JSON.stringify(arrayFeatures)},
			success: function ( result, request ) {
				Ext.MessageBox.hide();
//				window.location.href='mapa_ruta.php?id_micro=<?=$frm["id_micro"]?>';
				/*
				var jsonData = Ext.util.JSON.decode(result.responseText);
				var resultMessage = jsonData.data.result;
				console.log(resultMessage);
				*/
			},
			failure: function ( result, request ) {
				var jsonData = Ext.util.JSON.decode(result.responseText);
				var resultMessage = jsonData.data.result;
				Ext.MessageBox.hide();
				window.alert(result);
			}
		});
	}



	var toolbarItems = [], action, actions = {};





	//var nuevoArco=function(){Ext.Msg.alert('Nuevo', 'Nuevo.');};
	//var subirArco=function(){Ext.Msg.alert('Subir', 'Subir.');};
	//var bajarArco=function(){Ext.Msg.alert('Bajar', 'Bajar.');};

	var actNav = new GeoExt.Action({
		text: "nav",
		control: new OpenLayers.Control.Navigation(),
		map: map,
		// button options
		toggleGroup: "editArc",
		allowDepress: false,
		pressed: true,
		tooltip: "navigate",
		// check item options
		group: "editArc",
		checked: true
	});
	actions["nav"] = actNav;
	toolbarItems.push(actNav);


	var borrarArco = new GeoExt.Action({
		text: "Borrar Arco",
		map: map,
		// button options
		handler: function(){
			if (gridPanel.getSelectionModel().getSelected() == undefined)
				Ext.Msg.alert('Alerta','¡Escoja un arco primero!'); 
			else
			{
				Ext.Msg.confirm('Confirmación','¿Esta seguro de querer borrar el arco?',function(btn){  
					if(btn === 'yes'){ 
						var orden =parseInt(gridPanel.getSelectionModel().getSelected().get('num'));
						store.remove(gridPanel.getSelectionModel().getSelected());
						gridPanel.store.each(function(record){
							numtemp =parseInt(record.get('num'));
							if(numtemp >= orden)
								record.set('num',parseInt(numtemp)-1);
						});
					} 
				});
			}
		},
		disabled: true,
		tooltip: "borrar",
	});
	actions["borrar"] = borrarArco;
	toolbarItems.push(borrarArco);

	var subirArco = new GeoExt.Action({
		text: "Subir Arco",
		map: map,
		// button options
		handler: function(){
			if (gridPanel.getSelectionModel().getSelected() == undefined)
				Ext.Msg.alert('Alerta','¡Escoja un arco primero!'); 
			else
			{
				var orden =parseInt(gridPanel.getSelectionModel().getSelected().get('num'));
				if(orden == 1)
				{
					Ext.Msg.alert('Alerta','Es el primero, no se puede subir'); 
				}else{
					gridPanel.store.each(function(record){
						numtemp =parseInt(record.get('num'));
						if(numtemp == orden)
							record.set('num',parseInt(numtemp)-1);
						if(numtemp == orden-1)
							record.set('num',parseInt(numtemp)+1);
					});
					store.sort("num","ASC");
				}
			}
		},
		disabled: true,
		tooltip: "subir arco",
	});
	actions["subir"] = subirArco;
	toolbarItems.push(subirArco);

	var bajarArco = new GeoExt.Action({
		text: "Bajar Arco",
		map: map,
		// button options
		handler: function(){
			if (gridPanel.getSelectionModel().getSelected() == undefined)
				Ext.Msg.alert('Alerta','¡Escoja un arco primero!'); 
			else
			{
				numElementos = store.data.length;
				var orden =parseInt(gridPanel.getSelectionModel().getSelected().get('num'));
				if(orden == numElementos)
				{
					Ext.Msg.alert('Alerta','Es el último, no se puede bajar'); 
				}else{
					gridPanel.store.each(function(record){
						numtemp =parseInt(record.get('num'));
						if(numtemp == orden)
							record.set('num',parseInt(numtemp)+1);
						if(numtemp == orden+1)
							record.set('num',parseInt(numtemp)-1);
					});
					store.sort("num","ASC");
				}
			}
		},
		disabled: true,
		tooltip: "bajar arco",
	});
	actions["bajar"] = bajarArco;
	toolbarItems.push(bajarArco);



	var borrarPuntoControl = new GeoExt.Action({
		text: "Borrar Punto Control",
		map: map,
		// button options
		handler: function(){
			if (puntosCPanel.getSelectionModel().getSelected() == undefined)
				Ext.Msg.alert('Alerta','¡Escoja el punto de control a borrar!'); 
			else
			{
				Ext.Msg.confirm('Confirmación','¿Esta seguro de querer borrar el punto de control?',function(btn){  
					if(btn === 'yes'){ 
						var puntoABorrar = puntosCPanel.getSelectionModel().getSelected().get('id_punto');
						Ext.Ajax.request({
							url : './pgrouting.php',
							method: 'POST',
							params :{mode: 'borrar_punto_control', id: puntoABorrar},
							success: function ( result, request ) {
								storePC.remove(puntosCPanel.getSelectionModel().getSelected());
								storePC.sort("hora","ASC");
							},
							failure: function ( result, request ) {
								var jsonData = Ext.util.JSON.decode(result.responseText);
								var resultMessage = jsonData.data.result;
							}
						});
					
/*



			




					
						var orden =parseInt(gridPanel.getSelectionModel().getSelected().get('num'));
						store.remove(gridPanel.getSelectionModel().getSelected());
						gridPanel.store.each(function(record){
							numtemp =parseInt(record.get('num'));
							if(numtemp >= orden)
								record.set('num',parseInt(numtemp)-1);
						});
*/					

					} 
				});
			}
		},
		
		tooltip: "borrar punto de control",
	});
	actions["borrarPuntoControl"] = borrarPuntoControl;
	toolbarItems.push(borrarPuntoControl);





	toolbarItems.push("-");
	toolbarItems.push("->");
	toolbarItems.push({
		text: "menu",
		menu: new Ext.menu.Menu({
			items: [
				new Ext.menu.CheckItem(actions["nav"]),
				//new Ext.menu.CheckItem(actions["nuevo"]),
				new Ext.menu.CheckItem(actions["borrar"]),
				new Ext.menu.CheckItem(actions["subir"]),
				new Ext.menu.CheckItem(actions["bajar"]),
				new Ext.menu.CheckItem(actions["borrarPuntoControl"])		
			]
		})
	});










	var map = new OpenLayers.Map();

	// create the layer where the route will be drawn
	var defaultStyle = new OpenLayers.Style({strokeColor: "#00ff00",strokeWidth: 2});
	var selectStyle = new OpenLayers.Style({strokeColor: "#ff0000",strokeWidth: 4});
/*
	var route_layer = new OpenLayers.Layer.Vector("route", {
		styleMap: new OpenLayers.StyleMap({'default': defaultStyle,'select': selectStyle})
	});
*/
//	var route_layer = new OpenLayers.Layer.GML("Ruta guardada", "../geometrias.php?table=micros&field=geometry&id=<?=$frm["id_micro"]?>" + "&rand=" + Math.random() ,{styleMap: new OpenLayers.StyleMap({'default': defaultStyle,'select': selectStyle})});
	var route_layer = new OpenLayers.Layer.GML("Ruta guardada", "rutas.php?id=<?=$frm["id_micro"]?>" + "&rand=" + Math.random() ,{styleMap: new OpenLayers.StyleMap({'default': defaultStyle,'select': selectStyle})});
	route_layer.events.register("loadend",route_layer, function (e) {
		var routeExtents=route_layer.getDataExtent();
		if(routeExtents!=null){
			map.zoomToExtent(route_layer.getDataExtent());
		}
	});

	var wmsLayer = new OpenLayers.Layer.OSM("OSM");

//var LayerParaderos=new OpenLayers.Layer.GML("Puntos Control", "../puntos_control.php?id_micro=<?=$frm["id_micro"]?>" + "&rand=" + Math.random() ,{styleMap: gpsStyle});
	var LayerParaderos = new OpenLayers.Layer.Vector("Puntos Control");
	var LayerGps=new OpenLayers.Layer.GML("Rastro de GPS", "../gps.php?id_vehi=<?=$vehiculo["idgps"]?>&desde=<?=$frm["fecha_desde"]?>&hasta=<?=$frm["fecha_hasta"]?>" + "&rand=" + Math.random() ,{styleMap: gpsStyle});
	var LayerGpsLoadEnd=0;
//	var LayerGps=new OpenLayers.Layer.GML("Rastro de GPS", "",{styleMap: gpsStyle});
//	LayerGps.events.register("loadend",LayerGps, function (e) {
//		map.zoomToExtent(LayerGps.getDataExtent());
//	});

	// add the layers to the map
	map.addLayers([LayerParaderos,  LayerGps, route_layer, wmsLayer])



OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control, {
	defaultHandlerOptions: {
		single: true,
		double: false,
		pixelTolerance: 0,
		stopSingle: true
	},
	initialize: function(options) {
		this.handlerOptions = OpenLayers.Util.extend(
			options && options.handlerOptions || {}, 
			this.defaultHandlerOptions
		);
		OpenLayers.Control.prototype.initialize.apply(
			this, arguments
		); 
		this.handler = new OpenLayers.Handler.Click(
			this, 
			{
				click: this.trigger
			}, 
			this.handlerOptions
		);
	},
	trigger: function(e) {
		var lonlatWGS84 = mapPanel.map.getLonLatFromViewPortPx(e.xy).transform(
			mapPanel.map.getProjectionObject(), // from Spherical Mercator Projection
			new OpenLayers.Projection("EPSG:4326") // to WGS 1984
		);
		x=lonlatWGS84.lon;
		y=lonlatWGS84.lat;
		if(agregarParadero==1){
			agregarParadero=0;
			nomParadero="";
			horaParadero="";
			while(nomParadero==""){
				nomParadero=window.prompt("Nombre del Punto");
				if(nomParadero==null) return;
			}
			while(horaParadero==""){
				horaParadero=window.prompt("Hora (HH:mm)");
				if(horaParadero==null) return;
			}
			Ext.Ajax.request({
				url : './pgrouting.php',
				method: 'GET',
				params :{mode: 'saveParadero',id_micro: '<?=$frm["id_micro"]?>',nombre: nomParadero, hora:horaParadero, x: x, y: y},
				success: function ( result, request ) {
					storePC.removeAll();
					var geojson_format = new OpenLayers.Format.GeoJSON();
					LayerParaderos.addFeatures(geojson_format.read(result.responseText));
				},
				failure: function ( result, request ) {
					var jsonData = Ext.util.JSON.decode(result.responseText);
					var resultMessage = jsonData.data.result;
				}
			});
		}
	},
	CLASS_NAME: "OpenLayers.Control.Click"
});





	

    // create map panel
    mapPanel = new GeoExt.MapPanel({
		region: "center",
		map: map,
		center: [-8250124,517550],
		zoom: 5,
		height: 400,
		width: 600,
		tbar: toolbarItems
    });
	//var map = mapPanel.map;
	//map.addControl(new OpenLayers.Control.LayerSwitcher());
	mapPanel.map.addControl(new OpenLayers.Control.LayerSwitcher());
	var click = new OpenLayers.Control.Click();
	mapPanel.map.addControl(click);
	click.activate();



	//
	var rightClick = new OpenLayers.Control.Navigation ({handleRightClicks: true});
	mapPanel.map.addControl(rightClick);
	rightClick.handlers.click.callbacks.rightclick = function(evt) { 
		var menu = new Ext.menu.Menu({
			items: [{
				text : 'Agregar arco despues del seleccionado',
				handler : function(){
					if (gridPanel.getSelectionModel().getSelected() == undefined)
						Ext.Msg.alert('Alerta','Escoja primero el arco que va antes del que se quiere agregar.'); 
					else
					{	
						var orden =parseInt(gridPanel.getSelectionModel().getSelected().get('num')) + 1;
						var lonLat = mapPanel.map.getLonLatFromViewPortPx(evt.xy);

						//convertir
						var projWGS84 = new OpenLayers.Projection("EPSG:4326");
						var proj900913 = new OpenLayers.Projection("EPSG:900913");
						var puntoTrans = lonLat.transform(proj900913, projWGS84);

						Ext.Ajax.request({
							url : './pgrouting.php',
							method: 'POST',
							params :{mode: 'agregar_arco',id_micro: '<?=$frm["id_micro"]?>',x: puntoTrans.lon, y:puntoTrans.lat, orden: orden},
							success: function ( result, request ) {

								gridPanel.store.each(function(record){
									numtemp =parseInt(record.get('num'));
									if(numtemp >= orden)
										record.set('num',parseInt(numtemp)+1);
								});

								var geojson_format = new OpenLayers.Format.GeoJSON();
								route_layer.addFeatures(geojson_format.read(result.responseText));
								store.sort("num","ASC");
							},
							failure: function ( result, request ) {
								var jsonData = Ext.util.JSON.decode(result.responseText);
								var resultMessage = jsonData.data.result;
							}
						});
						alert("Se ha agregado el arco con orden "+ orden);
					}
				}
			},
			{
				text : 'Agregar arco antes del seleccionado',
				handler : function(){
					if (gridPanel.getSelectionModel().getSelected() == undefined)
						Ext.Msg.alert('Alerta','Escoja primero el arco que va despues del que se quiere agregar.'); 
					else
					{	
						var orden =parseInt(gridPanel.getSelectionModel().getSelected().get('num')) ;
						var lonLat = mapPanel.map.getLonLatFromViewPortPx(evt.xy);

						//convertir
						var projWGS84 = new OpenLayers.Projection("EPSG:4326");
						var proj900913 = new OpenLayers.Projection("EPSG:900913");
						var puntoTrans = lonLat.transform(proj900913, projWGS84);

						Ext.Ajax.request({
							url : './pgrouting.php',
							method: 'POST',
							params :{mode: 'agregar_arco',id_micro: '<?=$frm["id_micro"]?>',x: puntoTrans.lon, y:puntoTrans.lat, orden: orden},
							success: function ( result, request ) {

								gridPanel.store.each(function(record){
									numtemp =parseInt(record.get('num'));
									if(numtemp >= orden)
										record.set('num',parseInt(numtemp)+1);
								});

								var geojson_format = new OpenLayers.Format.GeoJSON();
								route_layer.addFeatures(geojson_format.read(result.responseText));
								store.sort("num","ASC");
							},
							failure: function ( result, request ) {
								var jsonData = Ext.util.JSON.decode(result.responseText);
								var resultMessage = jsonData.data.result;
							}
						});
						alert("Se ha agregado el arco con orden "+ orden);
					}
				}
			},
			{
				text : 'Agregar un arco para iniciar en 0',
				handler : function(){
						var orden =0;
						var lonLat = mapPanel.map.getLonLatFromViewPortPx(evt.xy);

						//convertir
						var projWGS84 = new OpenLayers.Projection("EPSG:4326");
						var proj900913 = new OpenLayers.Projection("EPSG:900913");
						var puntoTrans = lonLat.transform(proj900913, projWGS84);

						Ext.Ajax.request({
							url : './pgrouting.php',
							method: 'POST',
							params :{mode: 'agregar_arco',id_micro: '<?=$frm["id_micro"]?>',x: puntoTrans.lon, y:puntoTrans.lat, orden: 0},
							success: function ( result, request ) {

								gridPanel.store.each(function(record){
									numtemp =parseInt(record.get('num'));
									if(numtemp >= orden)
										record.set('num',parseInt(numtemp)+1);
								});

								var geojson_format = new OpenLayers.Format.GeoJSON();
								route_layer.addFeatures(geojson_format.read(result.responseText));
								store.sort("num","ASC");
							},
							failure: function ( result, request ) {
								var jsonData = Ext.util.JSON.decode(result.responseText);
								var resultMessage = jsonData.data.result;
							}
						});
						alert("Se ha agregado el arco con orden "+ orden);
					
				}
			}


			]
		});

		e = evt.xy;		
		X = e.x+15,
		Y = e.y,		
		menu.showAt([X,Y]);		
	}




	var form = new Ext.form.FormPanel({ //(1)
		renderTo: "divform", //(2)
		url: '<?=$ME?>',
		standardSubmit : true,
		frame: true,
		method: 'GET',
		width: 700,
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
				id: 'id_vehiculo',
				hiddenName: 'id_vehiculo',
				triggerAction: "all",
				store: [<?while($veh=$db->sql_fetchrow($qVehiculos)) echo "[\"$veh[idgps]\", \"$veh[label]\"],";?>],
				value: '<?if(isset($vehiculo["idgps"]) && $vehiculo["idgps"]!="") echo $vehiculo["idgps"];?>'
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
						id: 'fecha_desde',
						name: 'fecha_desde',
						value: '<?=date("Y-m-d",strtotime($frm["fecha_desde"]))?>',
						width:100
					}),
					new Ext.form.TimeField({
						name: 'hora_desde',
						id: 'hora_desde',
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
						id: 'fecha_hasta',
						name: 'fecha_hasta',
						value: '<?=date("Y-m-d",strtotime($frm["fecha_hasta"]))?>',
						width:100
					}),
					new Ext.form.TimeField({
						id: 'hora_hasta',
						name: 'hora_hasta',
						format:"H:i:s",
						increment: 30,
						value: '<?=date("H:i:s",strtotime($frm["fecha_hasta"]))?>',
						width:80
					})
				]
			}

		],
		buttons: [
			{
				text: 'Guardar ruta',
				handler: fn_saveRoute
			},{
				text: 'Hacer ruta',
				handler: function() {
					pgrouting(store);
				}
			},{
				text: 'Complementar ruta',
				handler: function() {
					complementarRuta(store,storeAux);
				}
			},{
				text: 'Cargar GPS',
					handler: function() {
						if(Ext.getCmp('id_vehiculo').value==""){
							window.alert('Por favor seleccione el vehículo.');
							return;
						}
//						form.getForm().submit();
						Ext.MessageBox.show({
							msg: 'Trayendo registros de GPS ...',
							title: 'Por favor espere...',
							progress: true,
							closable: false,
							width:300,
							wait:true,
							waitConfig: {interval:200}
						});
						idVehiculo=form.findById('id_vehiculo').getValue();
						desde=form.form.findField('fecha_desde').value + ' ' + form.form.findField('hora_desde').value;
						hasta=form.form.findField('fecha_hasta').value + ' ' + form.form.findField('hora_hasta').value;
						url="../gps.php?id_vehi=" + idVehiculo + "&desde=" + desde + "&hasta=" + hasta + "&rand=" + Math.random();
						if(LayerGpsLoadEnd==0){
							LayerGpsLoadEnd=1;
							LayerGps.events.register("loadend",LayerGps, function (e) {
								mapPanel.map.zoomToExtent(LayerGps.getDataExtent());
								Ext.MessageBox.hide();
							});
						}
						LayerGps.setUrl(url);
					}
/*
				handler: function() {
					idVehiculo=form.findById('id_vehiculo').getValue();
					desde=form.form.findField('fecha_desde').value + ' ' + form.form.findField('hora_desde').value;
					hasta=form.form.findField('fecha_hasta').value + ' ' + form.form.findField('hora_hasta').value;
					url="../gps.php?id_vehi=" + idVehiculo + "&desde=" + desde + "&hasta=" + hasta + "&rand=" + Math.random();
					LayerGps.setUrl(url);
//					form.getForm().submit();
				}
*/
			}, {
				id: 'btnAgregarParadero',
				text: 'Agregar Punto Control',
				handler: function() {
					agregarParadero=1;
				}
			}, {
				text: 'Cerrar',
				handler: function() {
					if(window.opener && window.opener.focus) window.opener.focus();
					window.close();
				}
			}
		]
	});



	// create the store to query the web service
	var store = new GeoExt.data.FeatureStore({
		layer: route_layer,
		fields: [
			{name: "name"},{name:"id"},{name:"num", type:"int"}
		],
		proxy: new GeoExt.data.ProtocolProxy({
			protocol: new OpenLayers.Protocol.HTTP({
				url: "./pgrouting.php",
				format: new OpenLayers.Format.GeoJSON({
					internalProjection: epsg_900913,
					externalProjection: epsg_4326
				})
			})
		}),
	});

	store.on('load', function() {
		borrarArco.setDisabled(false);
		subirArco.setDisabled(false);
		bajarArco.setDisabled(false);
	});

	var storeAux = new GeoExt.data.FeatureStore({
		fields: [
			{name: "name"},{name:"id"},{name:"num", type:"int"}
		],
		proxy: new GeoExt.data.ProtocolProxy({
			protocol: new OpenLayers.Protocol.HTTP({
				url: "./pgrouting.php",
				format: new OpenLayers.Format.GeoJSON({
					internalProjection: epsg_900913,
					externalProjection: epsg_4326
				})
			})
		}),
	});
	storeAux.on('load', function() {
		this.each(function(record){
			route_layer.addFeatures(record.data.feature);
		});
	});





gridPanel = new Ext.grid.GridPanel({
		title: "Arcos de la ruta",
		//region: "east",
		renderTo:"arcosRuta",
		store: store,
		width: 300,
		height:500,
		autoScroll:true,
		columns: [{
			header: "#",
			width: 40,
			dataIndex: "num",
			sortable: true,
			type: 'int',
			
		}, {
			header: "ID",
			width: 50,
			dataIndex: "id"
		}, {
			header: "Nombre",
			width: 208,
			dataIndex: "name"
		}],
		sm: new GeoExt.grid.FeatureSelectionModel() 
	});


    // create feature store, binding it to the vector layer
    storePC = new GeoExt.data.FeatureStore({
        layer: LayerParaderos,
        fields: [
				{name: 'id_punto'},
            {name: 'direccion', type: 'string'},
            {name: 'hora'}
        ],
			proxy: new GeoExt.data.ProtocolProxy({
			protocol: new OpenLayers.Protocol.HTTP({
				url: "./pgrouting.php",
				params :{mode: 'cargar_puntos_control',id_micro: '<?=$frm["id_micro"]?>'},
				format: new OpenLayers.Format.GeoJSON({
					//internalProjection: epsg_900913,
//					externalProjection: epsg_4326
					//externalProjection: epsg_900913
				})
			})
		}),
        autoLoad: true
    });
	

    // create grid panel configured with feature store
    puntosCPanel = new Ext.grid.GridPanel({
        title: "Puntos de Control",
        renderTo: "puntosControl",
        store: storePC,
        width: 300,
			autoHeight: true,
        columns: [
			{
            header: "ID",
            width: 30,
            dataIndex: "id_punto"
        },
			{
            header: "Dirección",
            width: 170,
            dataIndex: "direccion"
        }, {
            header: "Hora",
            width: 100,
            dataIndex: "hora"
        }],
        sm: new GeoExt.grid.FeatureSelectionModel() 
    });

    // create a panel and add the map panel and grid panel
    // inside it
    mainPanel = new Ext.Panel({
        renderTo: "mainpanel",
        layout: "border",
        height: 400,
        width: 700,
        items: [mapPanel]
    });
});


</script>
</head>

<body>
	<table><tbody>
		<tr>
			<td valign="top">
				<table>
					<tr>
						<td><div id="mainpanel"></div></td>
					</tr>
					<tr>
						<td><div id="divform"></div></td>
					</tr>
				</table>
			</td>
			<td valign="top">
				<table>
					<tr>
						<td valign="top"><div id="puntosControl"></div>	</td>
					</tr>
					<tr>
						<td><div id="arcosRuta"></div></td>
					</tr>
				</table>
			</td>
		</tr>
	</tbody></table>
     
    </body>
</html>

