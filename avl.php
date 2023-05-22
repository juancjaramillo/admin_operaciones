<?
include("application.php");
if(!isset($_SESSION[$CFG->sesion]["user"])){
	$errorMsg="No existe la sesión.";
	error_log($errorMsg);
	die($errorMsg);
}
$user=$_SESSION[$CFG->sesion]["user"];

$condicion = "v.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."') AND v.publico='t' AND v.id_estado<>4";
$qCentros=$db->sql_query("
	SELECT *
	FROM centros
	WHERE id IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."')
");

if($user["nivel_acceso"]==5) $condicion.=" AND v.id_tipo_vehiculo NOT IN (3,13) AND v.id_estado<>4";

/**/
if(!is_array($user["id_centro"]))
{ 
	echo "<script>
		window.location.href='".$CFG->wwwroot."/admin/login.php';
	</script>";
	die;
}
/**/

$extents=$db->sql_row("
	SELECT min(ST_XMin(v.the_geom)) as minx, max(ST_XMax(v.the_geom)) as maxx,
		min(ST_YMin(v.the_geom)) as miny, max(ST_YMax(v.the_geom)) as maxy
	FROM vehiculos v
	WHERE $condicion
");

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

		<script src="https://maps.google.com/maps/api/js?key=AIzaSyD1dxPkFjL50IsGTB7kNFBnZ8w9IVtT_fA&v=3.6&sensor=false"></script>
    <script src="js/OpenLayers-2.12-rc7/lib/OpenLayers.js"></script>

		<link rel="stylesheet" type="text/css" href="https://extjs.cachefly.net/ext-3.4.0/resources/css/ext-all.css">
		<script type="text/javascript" src="https://extjs.cachefly.net/ext-3.4.0/adapter/ext/ext-base.js"></script>
		<script type="text/javascript" src="https://extjs.cachefly.net/ext-3.4.0/ext-all.js"></script>

		<?/*
		<link rel="stylesheet" type="text/css" href="http://dev.sencha.com/deploy/ext-4.0.7-gpl/resources/css/ext-all.css">
		<script type="text/javascript" src="http://dev.sencha.com/deploy/ext-4.0.7-gpl/bootstrap.js"></script>
		*/?>

    <script type="text/javascript">
        <!--
        var map, layer, LayerVehiculos, LayerGps, routeLayer, routeLayerLabels =null;
				var gpsDesde,gpsHasta=null;
				var popup=null;
				var GPSActual='0';
				var contador=0;
				var dataSource;
				var myColumnDefs;
				var myDataTable;
				var selectControl;
				var testControl;

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

				var gpsStyle = new OpenLayers.StyleMap({
						// Set the external graphic and background graphic images.
//								externalGraphic: "./images/taxi.png",
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

				function recargar(){
					if(popup!=undefined && popup!=null){
						map.removePopup(popup);
						popup.destroy();
						popup = null;
					}
//					LayerVehiculos.setUrl("point.php?rand=" + Math.random());
					LayerVehiculos.refresh({url: 'point.php?rand=' + Math.random()});
/*
					Ext.Ajax.request({
						url : "alertas.php",
						method: "GET",
						success: function ( result, request ) {
							document.getElementById("bottom1").innerHTML=result.responseText;
						},
						failure: function ( result, request ) {
						}
					});
*/
					timerRecargar=setTimeout ( "recargar()", 5000);
        }

        function onFeatureSelect(feat){
          selectedFeature = feat;
					popup = new OpenLayers.Popup.FramedCloud("chicken",
							feat.geometry.getBounds().getCenterLonLat(),
							null,
					"<div style='font-size:.8em'>Vehículo: <b>" + feat.attributes.name + "</b><br/>Tipo: <b>" + feat.attributes.tipo + "</b><br/>Placa: <b>" + feat.attributes.placa + "</b><br/>Posición: <b>" + feat.attributes.hrposition + "</b><br/>Coordenadas: <b>" + feat.attributes.y +" " + feat.attributes.x + "</b><br/><?if($user["nivel_acceso"]!=5){?>Velocidad: <b>" + feat.attributes.velocidad + "</b><br/>Estado motor: <b>" + feat.attributes.estado_motor + "</b><br/>Odóro: <b>" + feat.attributes.k_v + "</b><br/>Horóro: <b>" + feat.attributes.h_v + "</b><br/>Hora: <b>" + feat.attributes.tiempo + "</b><br/><span onClick='showGPS(\"" + feat.attributes.ogc_fid +"\",\"" + feat.attributes.name +"\")' style='cursor:pointer'><u>" + ((GPSActual==feat.attributes.ogc_fid) ? "Ocultar" : "Mostrar") + " GPS</u></span><br/><span onClick='showGPS(\"" + feat.attributes.ogc_fid +"\",\"" + feat.attributes.name +"\",\"turno\")' style='cursor:pointer'><u>" + ((GPSActual==feat.attributes.ogc_fid) ? "Ocultar" : "Mostrar") + " GPS del turno</u></span><br/><span onClick='verHistorial(\"" + feat.attributes.ogc_fid +"\")' style='cursor:pointer'><u>Ver Historial</u></span><?if(in_array($user["nivel_acceso"],$CFG->permisos["verOperacionDesdeAvl"])){?><br/><span onClick='verDesplazamientosOperacion(\"" + feat.attributes.id_vehiculo +"\")' style='cursor:pointer'><u>Ver Operación </u></span><?}}?></div>",		
							null, true, onPopupClose);
					feat.popup = popup;
					map.addPopup(popup);
				}	

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

				function showGPS(id_vehi,vehi_name,desde,hasta){
					strTiempo="";
	
					if(typeof(desde) != 'undefined') strTiempo+="&desde=" + desde;
					else desde="";
					if(typeof(hasta) != 'undefined') strTiempo+="&hasta=" + hasta;
					else hasta="";

					if(popup!=undefined && popup!=null){
						if(id_vehi==GPSActual) testControl.unselect(selectedFeature);
						else selectControl.unselect(selectedFeature);
					}
					if(id_vehi==GPSActual){
						map.removeControl(testControl);//No estoy seguro de que se necesite esta línea
						testControl.destroy();
						LayerGps.destroy();
//						LayerGps = null;
						GPSActual='0';
						document.getElementById('info_cell').innerHTML="Layer info oculto";
						document.getElementById('info').style.visibility="hidden";
					}
					else{
						if(GPSActual!='0'){//Hay otro activo
							testControl.destroy();
							LayerGps.destroy();
//							LayerGps = null;
							document.getElementById('replayButton').value='Reproducir';
						}
						replayCont=0;
						clearTimeout(replayTimer);
						replayPlaying=false;
						replayStarted=false;
						if(detailPopup != null){
							map.removePopup(detailPopup);
							detailPopup.destroy();
							detailPopup=null;
						}

						map.removeControl(selectControl);//No estoy seguro de que se necesite esta línea
//						LayerGps=new OpenLayers.Layer.GML("Rastro de GPS", "gps.php?id_vehi=" + id_vehi + strTiempo + "&rand=" + Math.random() ,{styleMap: gpsStyle});
						LayerGps=new OpenLayers.Layer.Vector("Rastro de GPS", {
							protocol: new OpenLayers.Protocol.HTTP({
								url: "gps.php?id_vehi=" + id_vehi + strTiempo + "&rand=" + Math.random(),
								format: new OpenLayers.Format.GML()
							}),
							strategies: [new OpenLayers.Strategy.Fixed()],
							styleMap: gpsStyle
						});
console.log("gps.php?id_vehi=" + id_vehi + strTiempo + "&rand=" + Math.random());

						LayerGps.events.register("loadend",LayerGps, function (e) {
							map.zoomToExtent(LayerGps.getDataExtent());
						});
						map.addLayer(LayerGps);

						testControl = new OpenLayers.Control.SelectFeature(
							[LayerVehiculos,LayerGps],
							{
								clickout: true, toggle: false,
								multiple: false, hover: false
							}
						);

						map.addControl(testControl);
						testControl.activate();
						
            LayerGps.events.on({
                "featureselected": function(e) {
                  tooltipSelect(e);
                },
                "featureunselected": function(e) {
                	tooltipUnselect(e);
                }
            });

						GPSActual=id_vehi;
						document.getElementById('info_cell').innerHTML="Visualizando GPS del vehículo <b>" + vehi_name + "</b><br />\n";
						if(desde=="") document.getElementById('info_cell').innerHTML+="Registros de la última hora";
						else{
							if(desde=='turno') document.getElementById('info_cell').innerHTML+="Registros del turno";
							else document.getElementById('info_cell').innerHTML+="Desde: " + desde + " || Hasta: " + hasta;
						}
						document.getElementById('info_cell').innerHTML+="<br />\n<form>";
						document.getElementById('info_cell').innerHTML+="<input type=\"button\" value=\"Ocultar\" onClick=\"showGPS('" + id_vehi + "','" + vehi_name + "');\">";
						if(desde=="")
							document.getElementById('info_cell').innerHTML+="<input type=\"button\" value=\"Refrescar\" onClick=\"showGPS('" + id_vehi + "','" + vehi_name + "');showGPS('" + id_vehi + "','" + vehi_name + "');\">";
						else{
							if(desde=="turno")
								document.getElementById('info_cell').innerHTML+="<input type=\"button\" value=\"Refrescar\" onClick=\"showGPS('" + id_vehi + "','" + vehi_name + "');showGPS('" + id_vehi + "','" + vehi_name + "','" + desde + "');\">";
							else
								document.getElementById('info_cell').innerHTML+="<input type=\"button\" value=\"Refrescar\" onClick=\"showGPS('" + id_vehi + "','" + vehi_name + "');showGPS('" + id_vehi + "','" + vehi_name + "','" + desde + "','" + hasta + "');\">";
						}
						document.getElementById('info_cell').innerHTML+="<input id=\"replayButton\" type=\"button\" value=\"Reproducir\" onClick=\"replay()\">";
						document.getElementById('info_cell').innerHTML+="</form>";
						document.getElementById('info').style.visibility="visible";
//						map.zoomToExtent(LayerGps.getDataExtent());
					}
				}

				var lastFeature = null;
				var tooltipPopup = null;


				var detailPopup=null;
				var replayCont;
				var replayTimer;
				var replayFeatures;
				var replayPlaying=false;
				var replayStarted=false;
				var resumenOperacion=0;

				function replay(){
					if(!replayStarted){
						replayFeatures=new Array();
						replayCont=0;
						for(var j=0;LayerGps.features.length>0;j++){
							replayFeatures[j]=LayerGps.features[0];
							LayerGps.removeFeatures(LayerGps.features[0],true);
						}
						replayStarted=true;
					}

					if(replayPlaying){
						document.getElementById('replayButton').value='Reproducción en pausa';
						clearTimeout(replayTimer);
						replayPlaying=false;
					}
					else{
						document.getElementById('replayButton').value='Reproduciendo...';
						replayPlaying=true;
						replayForward();
					}
				}
	
				function replayForward(){
					if(replayCont<replayFeatures.length){
						LayerGps.addFeatures(replayFeatures[replayCont]);
						showTooltip(replayFeatures[replayCont]);
						replayCont++;
					}
					if(replayCont<replayFeatures.length){
						replayTimer=setTimeout("replayForward()",1000);
					}
					else{
						if(detailPopup != null){
							map.removePopup(detailPopup);
							detailPopup.destroy();
							detailPopup=null;
						}
						replayFeatures=[];
						clearTimeout(replayTimer);
						replayPlaying=false;
						replayStarted=false;
						document.getElementById('replayButton').value='Reproducir';
					}
				}

				function showTooltip(feature){
					if(detailPopup != null){
						map.removePopup(detailPopup);
						detailPopup.destroy();
						detailPopup=null;
					}
//					selectedFeature = feature;
					detailPopup = new OpenLayers.Popup.FramedCloud("chicken",
							feature.geometry.getBounds().getCenterLonLat(),
							null,
							"<div style='font-size:.8em'>Hora: <b>" + feature.attributes.hora + "</b><br />Velocidad: <b>" + feature.attributes.velocidad + "km/h</b></div>",
							null, true);
					feature.popup = detailPopup;
					map.addPopup(detailPopup);
				}

				function tooltipSelect(event){
					var feature = event.feature;
					var selectedFeature = feature;
					//if there is already an opened details window, don\'t draw the tooltip
						if(feature.popup != null){
							return;
						}
					//if there are other tooltips active, destroy them
					if(tooltipPopup != null){
						map.removePopup(tooltipPopup);
						tooltipPopup.destroy();
						if(lastFeature != null){
							delete lastFeature.popup;
							tooltipPopup = null;
						}
					}
					lastFeature = feature;
					var tooltipPopup = new OpenLayers.Popup("activetooltip",
							feature.geometry.getBounds().getCenterLonLat(),
							new OpenLayers.Size(80,12),
							"&nbsp;&nbsp;&nbsp;"+feature.attributes.hora + " / " + feature.attributes.velocidad+"kp/h",
							true );
					//this is messy, but I'm not a CSS guru
					tooltipPopup.contentDiv.style.backgroundColor='ffffcb';
					tooltipPopup.closeDiv.style.backgroundColor='ffffcb';
					tooltipPopup.contentDiv.style.overflow='hidden';
					tooltipPopup.contentDiv.style.padding='3px';
					tooltipPopup.contentDiv.style.margin='0';
					tooltipPopup.closeOnMove = true;
					tooltipPopup.autoSize = true;
					feature.popup = tooltipPopup;
					map.addPopup(tooltipPopup);
				}

				function tooltipUnselect(event){
					var feature = event.feature;
					if(feature != null && feature.popup != null){
						map.removePopup(feature.popup);
						if(feature.popup!= null){
							feature.popup.destroy();
							delete feature.popup;
						}
						tooltipPopup = null;
						lastFeature = null;
					}
				}

				function verHistorial(id_vehi){
					if(popup!=undefined && popup!=null){
						selectControl.unselect(selectedFeature);
//						map.removePopup(popup);
//						popup = null;
					}
					ancho=screen.width*0.8;
					alto=screen.height*0.7;
					izq=(screen.width-ancho)/2;
					arriba=(screen.height-alto)/2;
					vent_historico=window.open('historico.php?id_vehi=' + id_vehi,'historico','scrollbars=yes,width=' + ancho +',height=' + alto +',resizable=yes,left='+izq+',top='+arriba);
					if(vent_historico.focus) vent_historico.focus();
				}

				function verDesplazamientosOperacion(id_vehiculo){
					if(popup!=undefined && popup!=null){
						selectControl.unselect(selectedFeature);
					}
					ancho=800;
					alto=100;
					izq=(screen.width-ancho)/2;
					arriba=(screen.height-alto)/2;
					vent_desplazamientos=window.open('opera/movimientos_rec.php?mode=listadoDesplazamientosDesdeAVL&id_vehiculo=' + id_vehiculo,'listunicadesp','scrollbars=yes,width=' + ancho +',height=' + alto +',resizable=yes,left='+izq+',top='+arriba);
					if(vent_desplazamientos.focus) vent_desplazamientos.focus();
				}
				
        function init(){
					<?if($user["nivel_acceso"]!=5){?>
					document.getElementById('iframeAlertas').style.width=document.body.offsetWidth + 'px';
					<?}?>
					var maxBounds=new OpenLayers.Bounds(-79.10, -4.30, -66.90, 12.50);
					var bounds=new OpenLayers.Bounds(<?=$extents["minx"]?>, <?=$extents["miny"]?>, <?=$extents["maxx"]?>, <?=$extents["maxy"]?>);
					var osm = new OpenLayers.Layer.OSM("Cartografia OSM",null,{ "tileOptions": {"crossOriginKeyword": null }});
					var ghyb = new OpenLayers.Layer.Google(
						"Cartografía Google",
						{type: google.maps.MapTypeId.HYBRID, numZoomLevels: 20}
					);

					var maxBounds900913=maxBounds.transform(new OpenLayers.Projection("EPSG:4326"), new OpenLayers.Projection("EPSG:900913"));

					var options = {
						displayProjection: new OpenLayers.Projection("EPSG:4326"),
						restrictedExtent: maxBounds
					}

					map = new OpenLayers.Map( $('map'), options);

					map.addLayer(osm);
					map.addLayer(ghyb);
					map.projection='EPSG:4326';


					//LayerGps=new OpenLayers.Layer.GML("GML", "gps.php?rand=" + Math.random() ,{styleMap: gpsStyle});
					//map.addLayer(LayerGps);
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
					var routeStyle = new OpenLayers.Style({
						strokeColor: '${strokecolor}',
						strokeWidth: 2
					});
					var routeLabelStyle = new OpenLayers.Style({
						strokeWidth: 2,
						label : "Ruta ${name}",
						fontColor: "${strokeColor}",
						fontSize: "12px",
						fontFamily: "Courier New, monospace",
						fontWeight: "bold",
						labelAlign: "cm",
						labelOutlineColor: "white",
						labelOutlineWidth: 3
					});
//					routeLayer = new OpenLayers.Layer.GML("Rutas", "layer_rutas.php?" + "rand=" + Math.random() ,{styleMap: new OpenLayers.StyleMap({'default': routeStyle}),visibility:false});
					routeLayer = new OpenLayers.Layer.Vector("Rutas", {
						protocol: new OpenLayers.Protocol.HTTP({
								url: "layer_rutas.php?rand=" + Math.random(),
								format: new OpenLayers.Format.GML()
						}),
						strategies: [new OpenLayers.Strategy.Fixed()],
						styleMap: routeStyle,
						visibility:false
					});

//					routeLayerLabels = new OpenLayers.Layer.GML("RutasLabels", "layer_rutas_labels.php?" + "rand=" + Math.random() ,{styleMap: new OpenLayers.StyleMap({'default': routeLabelStyle}),visibility:false});
					routeLayerLabels = new OpenLayers.Layer.Vector("RutasLabels", {
						protocol: new OpenLayers.Protocol.HTTP({
								url: "layer_rutas_labels.php?rand=" + Math.random(),
								format: new OpenLayers.Format.GML()
						}),
						strategies: [new OpenLayers.Strategy.Fixed()],
						styleMap: routeLabelStyle,
						visibility:false
					});
					cestasLayer = new OpenLayers.Layer.Vector("Cestas publicas", {
						protocol: new OpenLayers.Protocol.HTTP({
								url: "layer_cestas.php?rand=" + Math.random(),
								format: new OpenLayers.Format.GML()
						}),
						strategies: [new OpenLayers.Strategy.Fixed()],
						styleMap: puntoStyle,
						visibility:false
					});
					  pcriticosLayer = new OpenLayers.Layer.Vector("Puntos Crícos", {
						protocol: new OpenLayers.Protocol.HTTP({
							url: "layer_pcriticos.php?rand=" + Math.random(),
							format: new OpenLayers.Format.GML()
						}),
						strategies: [new OpenLayers.Strategy.Fixed()],
						styleMap: puntoStyle,
						visibility:false
					  });
					catastroLayer = new OpenLayers.Layer.Vector("Catastro de usuarios", {
						protocol: new OpenLayers.Protocol.HTTP({
								url: "layer_catastro.php?rand=" + Math.random(),
								format: new OpenLayers.Format.GML()
						}),
						strategies: [new OpenLayers.Strategy.Fixed()],
						styleMap: catastroStyle,
						visibility:false
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
					
					barridoLayer = new OpenLayers.Layer.Vector("Barrido Micros", {
						protocol: new OpenLayers.Protocol.HTTP({
								url: "layer_barrido_micros.php?rand=" + Math.random(),
								format: new OpenLayers.Format.GML()
						}),
						strategies: [new OpenLayers.Strategy.Fixed()],
						styleMap: routeStyle,
						visibility:false
					});
					barridoMacrosLayer = new OpenLayers.Layer.Vector("Barrido Macros", {
						protocol: new OpenLayers.Protocol.HTTP({
								url: "layer_barrido_macros.php?rand=" + Math.random(),
								format: new OpenLayers.Format.GML()
						}),
						strategies: [new OpenLayers.Strategy.Fixed()],
						styleMap: routeStyle,
						visibility:false
					});
					
					lavadoLayer = new OpenLayers.Layer.Vector("Lavado", {
						protocol: new OpenLayers.Protocol.HTTP({
								url: "layer_lavado_macros_pol.php?rand=" + Math.random(),
								format: new OpenLayers.Format.GML()
						}),
						strategies: [new OpenLayers.Strategy.Fixed()],
						styleMap: poligonoStyle,
						visibility:false
					})
					
					arbolesLayer = new OpenLayers.Layer.Vector("Poda Arboles", {
						protocol: new OpenLayers.Protocol.HTTP({
								url: "layer_poda_macros_pol.php?rand=" + Math.random(),
								format: new OpenLayers.Format.GML()
						}),
						strategies: [new OpenLayers.Strategy.Fixed()],
						styleMap: poligonoStyle,
						visibility:false
					})
					
					cespedLayer = new OpenLayers.Layer.Vector("Corte de Cesped", {
						protocol: new OpenLayers.Protocol.HTTP({
								url: "layer_cesped_macros_pol.php?rand=" + Math.random(),
								format: new OpenLayers.Format.GML()
						}),
						strategies: [new OpenLayers.Strategy.Fixed()],
						styleMap: poligonoStyle,
						visibility:false
					})
					
					puentesLayer = new OpenLayers.Layer.Vector("Puentes IDECA", {
						protocol: new OpenLayers.Protocol.HTTP({
								url: "layer_puentes.php?rand=" + Math.random(),
								format: new OpenLayers.Format.GML()
						}),
						strategies: [new OpenLayers.Strategy.Fixed()],
						styleMap: poligonoStyle,
						visibility:false
					});
					
					puentesLayer1 = new OpenLayers.Layer.Vector("Puentes Transmilenio", {
						protocol: new OpenLayers.Protocol.HTTP({
								url: "layer_puentes1.php?rand=" + Math.random(),
								format: new OpenLayers.Format.GML()
						}),
						strategies: [new OpenLayers.Strategy.Fixed()],
						styleMap: poligonoStyle,
						visibility:false
					});
					
					contenedorLayer = new OpenLayers.Layer.Vector("Contenedores", {
						protocol: new OpenLayers.Protocol.HTTP({
							url: "layer_contenedores.php?rand=" + Math.random(),
							format: new OpenLayers.Format.GML()
						}),
						strategies: [new OpenLayers.Strategy.Fixed()],
						styleMap: puntoStyle,
						visibility:false
					  });
					
					
					routeLayer.events.register("loadend",routeLayer, function (e) {
						if(resumenOperacion==0){
							document.getElementById('info_cell').innerHTML="Listo.";
							document.getElementById('info').style.visibility="hidden";
							if(routeLayer.features.length>0){
								map.zoomToExtent(routeLayer.getDataExtent());
							}
						}
						else resumenOperacion=0;
					});

					map.addLayer(routeLayer);
					map.addLayer(routeLayerLabels);
					map.addLayer(recoleccionLayer);
					map.addLayer(recoleccionMacrosLayer);
					map.addLayer(barridoLayer);
					map.addLayer(barridoMacrosLayer);
					map.addLayer(contenedorLayer);
					map.addLayer(lavadoLayer);
					map.addLayer(arbolesLayer);
					map.addLayer(cespedLayer);
					map.addLayer(cestasLayer);
					map.addLayer(pcriticosLayer);
					map.addLayer(puentesLayer);
					map.addLayer(puentesLayer1);
					

//					LayerVehiculos=new OpenLayers.Layer.GML("Vehiculos", "point.php?rand=" + Math.random() ,{styleMap: myStyles});
					LayerVehiculos=new OpenLayers.Layer.Vector("Vehículos", {
						protocol: new OpenLayers.Protocol.HTTP({
								url: "point.php?rand=" + Math.random(),
								format: new OpenLayers.Format.GML()
						}),
						strategies: [new OpenLayers.Strategy.Fixed()],
						styleMap: myStyles
					});

					<?
					$layersAd = array();
					$qidPC = $db->sql_query("SELECT cat.id, cat.nombre as categoria 
						FROM puntos_interes pi 
						LEFT JOIN categorias_puntos_interes cat ON cat.id = pi.id_categoria
						WHERE id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='".$user["id"]."')");
					while($pint = $db->sql_fetchrow($qidPC))
					{
						$layersAd[] = "Layer_int_".$pint["id"];
					?>
//						Layer_int_<?=$pint["id"]?>=new OpenLayers.Layer.GML("<?=$pint["categoria"]?>", "puntos_interes.php?id_categoria=<?=$pint["id"]?>" ,{styleMap: myStyles});
						Layer_int_<?=$pint["id"]?>=new OpenLayers.Layer.Vector("<?=$pint["categoria"]?>", {
							protocol: new OpenLayers.Protocol.HTTP({
									url: "puntos_interes.php?id_categoria=<?=$pint["id"]?>&rand=" + Math.random(),
									format: new OpenLayers.Format.GML()
							}),
							strategies: [new OpenLayers.Strategy.Fixed()],
							styleMap: myStyles
						});

					<?}?>


					LayerVehiculos.events.register("loadend",LayerVehiculos, function (e) {
						loadTable();
/*
						Ext.Ajax.request({
							url : "alertas.php",
							method: "GET",
							success: function ( result, request ) {
								document.getElementById("bottom1").innerHTML=result.responseText;
							},
							failure: function ( result, request ) {
							}
						});
*/
					});
					map.addLayer(LayerVehiculos);
					<?
					foreach($layersAd as $ad)
					{?>
						map.addLayer(<?=$ad?>);
					<?}?>

					LayerClientes = new OpenLayers.Layer.WMS( "Clientes",
						"https://<?=$_SERVER["SERVER_NAME"]?>/cgi-bin/mapserv?",
						{
							restrictedExtent: bounds,
							map: '<?=dirname(__FILE__)?>/pa.map',
							format: 'aggpng24',
							transparent: 'true',
							layers: 'clientes',
							units: 'm',
							transitionEffect: 'resize',
							gutter: 15
						}
					);
//					map.addLayer(LayerClientes);


//					selectControl = new OpenLayers.Control.SelectFeature(LayerVehiculos,{onSelect: onFeatureSelect, onUnselect: onFeatureUnselect});
					selectControl = new OpenLayers.Control.SelectFeature(LayerVehiculos,{clickout: true, toggle: false,multiple: false, hover: false});
					map.addControl(selectControl);
					selectControl.activate();
					LayerVehiculos.events.on({
						"featureselected": function(e) {onFeatureSelect(e.feature);},
						"featureunselected": function(e) {onFeatureUnselect(e.feature);}
					});

					map.zoomToExtent(bounds.transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject()),false);

        }
				timerRecargar=setTimeout ( "recargar()", 30000 );

				function loadTable(){
					dataSource=getDataSourceFromGML();
					if(contador==0){
						myColumnDefs = [
								{key:"name", resizeable:true, label:"id"},
								{key:"placa", resizeable:true, label:"placa"},
                {key:"hrposition", resizeable:true, label:"Posición"}<?if($user["nivel_acceso"]!=5){?>,
								{key:"velocidad", resizeable:true, label:"Vel."},
								{key:"ultimodesplazamiento", resizeable:true, label:"Operación"},
								{key:"ruta", resizeable:true, label:"Ruta"},
                {key:"turno", resizeable:true, label:"Turno"},
                {key:"hsbas", resizeable:true, label:"HSBAS"},
                {key:"himic", resizeable:true, label:"HIMIC"},
                {key:"hfmic", resizeable:true, label:"HFMIC"},
								{key:"distancia", resizeable:true, label:"Dist."}<?}?>
						];

						myDataSource = new YAHOO.util.DataSource(dataSource);
						myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
						myDataSource.responseSchema = {
								fields:["name","placa","hrposition","velocidad","ultimodesplazamiento","ruta","turno","hsbas","himic","hfmic","distancia"]
						};

						var onContextMenuClick = function(p_sType, p_aArgs, p_myDataTable) {
							var task = p_aArgs[1];
							if(task) {
								// Extract which TR element triggered the context menu
								var elRow = this.contextEventTarget;
								elRow = p_myDataTable.getTrEl(elRow);

								if(elRow) {
									rowIndex=elRow.rowIndex-2;
									switch(task.index) {
										case 0:
											var oRecord = p_myDataTable.getRecord(elRow);
											var ruta=LayerVehiculos.features[rowIndex].data.ruta;
											if(ruta==null || ruta==''){
												window.alert('Error: El vehículo ' + LayerVehiculos.features[rowIndex].data.name + ' no tiene asociada ninguna ruta.');
												return(false);
											}
											resumenOperacion=1;
//											routeLayer.setUrl("layer_rutas.php?rand=" + Math.random() + "&id_micro=" + LayerVehiculos.features[rowIndex].data.id_ruta);
											routeLayer.refresh({url: 'layer_rutas.php?rand=' + Math.random() + '&id_micro=' + LayerVehiculos.features[rowIndex].data.id_ruta});
											routeLayer.setVisibility(true);
//											routeLayerLabels.setUrl("layer_rutas_labels.php?rand=" + Math.random() + "&id_micro=" + LayerVehiculos.features[rowIndex].data.id_ruta);
											routeLayerLabels.refresh({url: 'layer_rutas_labels.php?rand=' + Math.random() + '&id_micro=' + LayerVehiculos.features[rowIndex].data.id_ruta});
											routeLayerLabels.setVisibility(true);
											showGPS(LayerVehiculos.features[rowIndex].data.ogc_fid,LayerVehiculos.features[rowIndex].data.name,"turno");
									}
								}
							}
						};

						myDataTable = new YAHOO.widget.DataTable("right1", myColumnDefs, myDataSource, {caption:"Vehículos"});
						myDataTable.set("selectionMode", "single");
						myDataTable.subscribe("rowClickEvent", myDataTable.onEventSelectRow); 
						myDataTable.subscribe("rowClickEvent",function(oArgs) {
								var rowIndex=oArgs.target.rowIndex-2;
								var myX=LayerVehiculos.features[rowIndex].geometry.x;
								var myY=LayerVehiculos.features[rowIndex].geometry.y;
								map.setCenter(new OpenLayers.LonLat(myX, myY), 15);
						});

						var myContextMenu = new YAHOO.widget.ContextMenu("mycontextmenu",
								{trigger:myDataTable.getTbodyEl()});
						myContextMenu.addItem("Resumen operación");
						myContextMenu.render("right1");
						myContextMenu.clickEvent.subscribe(onContextMenuClick, myDataTable);
					}
					else{
						myDataTable.load({datasource: new YAHOO.util.DataSource(dataSource)});
	//					window.alert('No es primera vez');
					}
				contador++;
/*
				if(GPSActual!=0){
					myDataTable.selectRow(myDataTable.getTrEl(3));
				}
*/
				}

				function getDataSourceFromGML(){
					var ds=new Array();
					var strAlertas="";
					for(var j=0;j<LayerVehiculos.features.length;j++){
//						if(LayerVehiculos.features[j].attributes.alerta=="1") console.log(LayerVehiculos.features[j].attributes.name);
						ds[j]={"name": LayerVehiculos.features[j].attributes.name, "placa":LayerVehiculos.features[j].attributes.placa, "hrposition":LayerVehiculos.features[j].attributes.hrposition, "velocidad":LayerVehiculos.features[j].attributes.velocidad, "ultimodesplazamiento":LayerVehiculos.features[j].attributes.ultimodesplazamiento, "ruta":LayerVehiculos.features[j].attributes.ruta,"turno":LayerVehiculos.features[j].attributes.turno,"hsbas":LayerVehiculos.features[j].attributes.hsbas,"himic":LayerVehiculos.features[j].attributes.himic,"hfmic":LayerVehiculos.features[j].attributes.hfmic, "distancia": LayerVehiculos.features[j].attributes.distancia};
/*
						if(LayerVehiculos.features[j].attributes.distancia != '-' && LayerVehiculos.features[j].attributes.distancia>1000){
							strAlertas+="Móvil " + LayerVehiculos.features[j].attributes.name + " fuera de ruta.<br>\n";
						}
						document.getElementById("bottom1").innerHTML="Alertas:<br>\n" + strAlertas;
*/
					}
					return(ds);
				}

				var filterWindow=null;
				var id_empresa=0;
				var dias=new Array();
				var id_turno=0;
				var id_micro="";
				var arrTurnos=new Array();
<?
$qTurnos=$db->sql_query("SELECT * FROM turnos ORDER BY id_empresa,hora_inicio");
$i=0;
while($turno=$db->sql_fetchrow($qTurnos)){
	echo "\tturno=new Object();\n";
	echo "\tturno.id=" . $turno["id"] . ";\n";
	echo "\tturno.id_empresa=" . $turno["id_empresa"] . ";\n";
	echo "\tturno.turno='" . $turno["turno"] . "';\n";
	echo "\tarrTurnos[" . $i . "]=turno;\n";
	$i++;
}
?>
				function filtrarDatos(){
					var primeraVez=true;
					if(filterWindow){
						filterWindow.destroy();
						filterWindow=null;
					}
					var formPanel = new Ext.form.FormPanel({
						region: 'center',
						width: 400,
						items: [
							new Ext.form.ComboBox({
								fieldLabel: 'Centro',
								editable: false,
								listeners:{
									'select':function() {
										var elm = Ext.getCmp('cmbTurno');
										elm.store.removeAll();
										var recs=[] , recType = elm.store.recordType;
										recs.push(new recType({id:0,name:'===='}));
										for(i=0;i<arrTurnos.length;i++){
											if(arrTurnos[i].id_empresa==this.value){
												recs.push(new recType({id:arrTurnos[i].id,name:arrTurnos[i].turno}));
											}
										}
										elm.store.add(recs);
										elm.setValue(0);
										updateCmbRoutes();
									}
								},
								width: 180,
								id: 'cmbEmpresa',
								hiddenName: 'id_empresa',
								triggerAction: "all",
								store: [["0","Ninguno"]<?while($centro=$db->sql_fetchrow($qCentros)) echo ",[\"$centro[id_empresa]\", \"$centro[centro]\"]";?>],
								value: "0"
							}),{
		            xtype: 'checkboxgroup',
        		    fieldLabel: 'Día',
								id: 'cbgDia',
    		        cls: 'x-check-group-alt',
								listeners : {
									'change' : function() {
										updateCmbRoutes();
									}
								},
		            items: [
									{boxLabel: 'Lu', inputValue: '1'},
									{boxLabel: 'Ma', inputValue: '2'},
									{boxLabel: 'Mi', inputValue: '3'},
									{boxLabel: 'Ju', inputValue: '4'},
									{boxLabel: 'Vi', inputValue: '5'},
									{boxLabel: 'Sá', inputValue: '6'},
									{boxLabel: 'Do', inputValue: '7'}
      		      ]
			        },new Ext.form.ComboBox({
								queryMode: 'local',
								fieldLabel: 'Turno',
								editable: false,
								width: 120,
								id: 'cmbTurno',
								multiSelect: true,
								hiddenName: 'id_turno',
								value: "0",
								mode:'local',
								triggerAction: "all",
								displayField: 'name',
								valueField: 'id',
//								store: [["0","===="]]
								store: new Ext.data.ArrayStore({
									fields: ['id','name'],
									data: [[0,'====']]
								}),
								listeners : {
									'select' : function() {
										updateCmbRoutes();
									}
								},
								valueField: 'id',
								displayField: 'name'
							}),new Ext.form.ComboBox({
								queryMode: 'local',
								fieldLabel: 'Ruta',
								editable: false,
								width: 110,
								id: 'cmbRoutes',
								multiSelect: true,
								hiddenName: 'id_micro',
								mode:'local',
								triggerAction: "all",
								displayField: 'codigo',
								valueField: 'id',
								store: new Ext.data.JsonStore({
									autoLoad: true,
									fields: ['id','codigo'],
									url:'ajax/getRoutes.php',
									baseParams:{
										id_empresa: id_empresa,
										id_turno: id_turno,
										dias: dias.join()
									},
									root: 'rows',
									listeners: {
										load: function () {
											if(primeraVez){
												Ext.getCmp('cmbRoutes').setValue(id_micro);
												primeraVez=false;
											}
										}
									}
								}),
								valueField: 'id',
								displayField: 'codigo'
							})
						],
						buttons: [
							{
								text: 'Aceptar',
								handler: function() {
									id_empresa=Ext.getCmp('cmbEmpresa').value;
									dias = [];
									cbgDia=Ext.getCmp('cbgDia');
									for(i=0;i<cbgDia.items.length;i++){
										if(cbgDia.items.items[i].checked) dias.push(cbgDia.items.items[i].inputValue);
									}
									id_turno=Ext.getCmp('cmbTurno').value;
									id_micro=Ext.getCmp('cmbRoutes').value;
//									routeLayer.setUrl("layer_rutas.php?rand=" + Math.random() + "&id_empresa=" + id_empresa + "&dias=" + dias.join() + "&id_turno=" + id_turno + "&id_micro=" + id_micro);
									routeLayer.refresh({url: 'layer_rutas.php?rand=' + Math.random() + '&id_empresa=' + id_empresa + '&dias=' + dias.join() + '&id_turno=' + id_turno + '&id_micro=' + id_micro});
									routeLayer.setVisibility(true);
//									routeLayerLabels.setUrl("layer_rutas_labels.php?rand=" + Math.random() + "&id_empresa=" + id_empresa + "&dias=" + dias.join() + "&id_turno=" + id_turno + "&id_micro=" + id_micro);
									routeLayerLabels.refresh({url: 'layer_rutas_labels.php?rand=' + Math.random() + '&id_empresa=' + id_empresa + '&dias=' + dias.join() + '&id_turno=' + id_turno + '&id_micro=' + id_micro});
									routeLayerLabels.setVisibility(true);

									document.getElementById('info_cell').innerHTML="<img src='images/wait.gif'> Trayendo layer de rutas...";
									document.getElementById('info').style.visibility="visible";
									filterWindow.close();
								}
							},{
								text: 'Cerrar',
								handler: function() {
									filterWindow.close();
								}
							}
						]
					});
					filterWindow=new Ext.Window({
						title: "Mostrar rutas",
						height: 200,
						width: 400,
						layout: "fit",
						items: formPanel
					});
					Ext.getCmp('cmbEmpresa').setValue(id_empresa);
					cbgDia=Ext.getCmp('cbgDia');
					for(i=0;i<cbgDia.items.length;i++){
						if(dias.indexOf(cbgDia.items[i].inputValue)!=-1) cbgDia.items[i].checked=true;
					}

					var elm = Ext.getCmp('cmbTurno');
					var recs=[] , recType = elm.store.recordType;
					for(i=0;i<arrTurnos.length;i++){
						if(arrTurnos[i].id_empresa==id_empresa){
							recs.push(new recType({id:arrTurnos[i].id,name:arrTurnos[i].turno}));
						}
					}
					elm.store.add(recs);
					elm.setValue(id_turno);

					filterWindow.show();
				}

				function updateCmbRoutes(){

					Ext.getCmp('cmbRoutes').setValue('');
					var arrSelectedDays=[];
					cbgDia=Ext.getCmp('cbgDia');
					for(i=0;i<cbgDia.items.length;i++){
						if(cbgDia.items.items[i].checked) arrSelectedDays.push(cbgDia.items.items[i].inputValue);
					}
					Ext.getCmp('cmbRoutes').store.reload({params: {
						id_empresa: Ext.getCmp('cmbEmpresa').value,
						id_turno: Ext.getCmp('cmbTurno').value,
						dias: arrSelectedDays.join()
					}});
				}

				Ext.onReady(function() {
				});

        // -->
    </script>
  </head>
  <body class="yui-skin-sam" onload="init()">
	<div id="right1">Cargando datos...</div>
	<div id="center1" style="height:100%">
    <div id="map">
			<div style="position:absolute; top:80px; right:5px; display:block; width:  48px;height: 48px; z-index:5000">
				<img style="cursor:pointer" src="images/view-filter.png" title="Filtrar datos" onClick="filtrarDatos()"><br>
			</div>

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
<?if($user["nivel_acceso"]!=5){?>
	<div id="bottom1" style="height:150px; overflow:-moz-scrollbars-vertical;"><iframe scrolling="no" height="145px" id="iframeAlertas" src="alertas/index2.php"></iframe></div>
<?}?>
	<script>

	(function() {
		var Dom = YAHOO.util.Dom,
		Event = YAHOO.util.Event;

		Event.onDOMReady(function() {
			var layout = new YAHOO.widget.Layout({
				units: [
					{ position: 'right', width: 450, resize: true, collapse: true, scroll: true, body: 'right1' },
					{ position: 'center', body: 'center1' }<?if($user["nivel_acceso"]!=5){?>,
					{ position: 'bottom', height:150, body: 'bottom1' , scroll: false}<?}?>
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
