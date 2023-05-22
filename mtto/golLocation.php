<?
	include_once("../application.php");
	$frm=$_GET;
	if(!isset($frm["module"])) die("No viene el módulo");
	include($CFG->modulesdir . "/" . $frm["module"] . ".php");
	if(!isset($frm["inputName"])) die("No viene el atributo");
	$att=$entidad->getAttributeByName($frm["inputName"]);
	$srid=$att->get("geometrySRID");

	$key=$att->get("gmapsApiKey");
	$drawPoint=FALSE;

	if(isset($frm["zoom_level"]) && $frm["zoom_level"]!="") $zoom=$frm["zoom_level"];
	else $zoom=$att->get("gLocZoom");

	$lat=$att->get("gLocLat");
	$lng=$att->get("gLocLng");

	if(preg_match("/POINT\(([0-9.\-]*) ([0-9.\-]*)\)/",$frm["value"],$matches)){
		$drawPoint=TRUE;
		$lat=$matches[2];
		$lng=$matches[1];
	}
	elseif(preg_match("/^([0-9.\-]*) ([0-9.\-]*)$/",$frm["value"],$matches)){
		$drawPoint=TRUE;
		$lat=$matches[2];
		$lng=$matches[1];
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<title>golLocation</title>

		<script src="http://openlayers.org/api/OpenLayers.js"></script>

		<script type="text/javascript">
		//<![CDATA[
		var pos;
		var map;
		var markers;
		var x;
		var y;

		OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control, {                
				defaultHandlerOptions: {
						'single': true,
						'double': false,
						'pixelTolerance': 0,
						'stopSingle': false,
						'stopDouble': false
				},

				initialize: function(options) {
						this.handlerOptions = OpenLayers.Util.extend(
								{}, this.defaultHandlerOptions
						);
						OpenLayers.Control.prototype.initialize.apply(
								this, arguments
						); 
						this.handler = new OpenLayers.Handler.Click(
								this, {
										'click': this.trigger
								}, this.handlerOptions
						);
				}, 

				trigger: function(e) {
					var lonlatWGS84 = map.getLonLatFromViewPortPx(e.xy).transform(
						map.getProjectionObject(), // from Spherical Mercator Projection
						new OpenLayers.Projection("EPSG:4326") // to WGS 1984
					);
					if(count==0){
						count=1;
					}
					else{
						markers.removeMarker(pos);
					}
					x=lonlatWGS84.lon;
					y=lonlatWGS84.lat;

					pos=new OpenLayers.Marker(lonlatWGS84.transform(new OpenLayers.Projection("EPSG:4326"),map.getProjectionObject()));
					markers.addMarker(pos);
				}
		});

		function load() {
			map = new OpenLayers.Map("mapdiv");
			map.addLayer(new OpenLayers.Layer.OSM());

			var lonLat = new OpenLayers.LonLat( <?=$lng?>,<?=$lat?> )
				.transform(
						new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
						map.getProjectionObject() // to Spherical Mercator Projection
			);

			var zoom=<?=$zoom?>;

			markers = new OpenLayers.Layer.Markers( "Markers" );
			map.addLayer(markers);

			map.setCenter (lonLat, zoom);

<?
	if($drawPoint){
		echo "pos=new OpenLayers.Marker(lonLat);\n";
		echo "markers.addMarker(pos);\n";
		echo "\t\t\t\tcount=1;\n";
		echo "\t\t\t\tx=$lng;\n";
		echo "\t\t\t\ty=$lat;\n";
	}
	else echo "\t\t\t\tcount=0;\n";
?>
			var click = new OpenLayers.Control.Click();
			map.addControl(click);
			click.activate();
		}
		function aceptar(){
			var punto;
			if(pos==undefined){
				window.alert('No se ha definido el sitio.');
				return(false);
			}
			lat=y;
			lng=x;
			zoom=map.getZoom();
			document.entryform.point.value="GeomFromEWKT('SRID=<?=$srid?>;POINT(" + lng + " " + lat + ")')";
			if(window.opener!=undefined && window.opener.document.<?=$frm["formName"]?>!=undefined &&	window.opener.document.<?=$frm["formName"]?>.<?=$frm["inputName"]?>!=undefined){
				window.opener.document.<?=$frm["formName"]?>.<?=$frm["inputName"]?>.value=document.entryform.point.value;
				if(window.opener.document.<?=$frm["formName"]?>.<?=$frm["inputName"]?>_zl!=undefined) window.opener.document.<?=$frm["formName"]?>.<?=$frm["inputName"]?>_zl.value=zoom;
				if(window.focus) window.opener.focus();
				window.close();
			}
			else{
				window.alert('No se pudo actualizar la ubicación.');
			}
		}
		//]]>
		</script>
	</head>
	<body onload="load()">
		<div align="center">
			<div id="mapdiv" style="width:450px;height:400px"></div>
			<form name="entryform">
				<input type="hidden" name="zoom_level" value="<?=nvl($frm["zoom_level"])?>">
				<input type="hidden" name="point" value="<?=nvl($frm["value"])?>">
				<input type="button" value="Aceptar" onClick="aceptar()">
				<input type="button" value="Cancelar" onClick="if(window.focus) window.opener.focus();window.close();">
			</form>
		</div>
	</body>
</html>

