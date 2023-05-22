<?
include("../application.php");
$frm=$_GET;
if(!isset($frm["id_micro"])) die("No viene el id de la ruta");
if(!$micro=$db->sql_row("SELECT id,astext(ST_TRANSFORM(geometry,900913)) as geometry FROM micros WHERE id='$frm[id_micro]'")){
	echo "No existe la ruta<br>\n";
	echo "<input type=\"button\" value=\"Cerrar\" onClick=\"if(window.focus) window.opener.focus();window.close();\">";
	die();
}
if($micro["geometry"]==""){
	echo "La ruta no tiene geometría cargada.<br>\n";
	echo "<input type=\"button\" value=\"Cerrar\" onClick=\"if(window.focus) window.opener.focus();window.close();\">";
	echo "<input type=\"button\" value=\"Cargar a partir de GPS\" onClick=\"window.location.href='record_route.php?mode=traer_gps&id_micro=$frm[id_micro]';\">";
	die();
}
//preguntar($micro);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<title>Mapa Ruta</title>

		<script src="http://openlayers.org/api/OpenLayers.js"></script>

		<script type="text/javascript">
		//<![CDATA[
		var pos;
		var map;
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
				}
		});

		function load() {
			map = new OpenLayers.Map("mapdiv");
			map.addLayer(new OpenLayers.Layer.OSM());

			// style the vectorlayer
			var styleMap = new OpenLayers.StyleMap({
					'default': new OpenLayers.Style({
							strokeColor: "#ff0000",
							strokeWidth: 3,
							strokeOpacity: 1
					})
			});
			var vectorlayer = new OpenLayers.Layer.Vector('Vectorlayer', {
				styleMap: styleMap
			});
			var original = OpenLayers.Geometry.fromWKT("<?=$micro["geometry"]?>");
			vectorlayer.addFeatures([new OpenLayers.Feature.Vector(original)]);
			var maxExtent = vectorlayer.getDataExtent();
			map.addLayer(vectorlayer);
			map.zoomToExtent(maxExtent);
			map.addControl(new OpenLayers.Control.MousePosition());

<?
	echo "\t\t\t\tcount=0;\n";
?>
		}
		function aceptar(){
		}
		//]]>
		</script>
	</head>
	<body onload="load()">
		<div align="center">
			<div id="mapdiv" style="width:450px;height:400px"></div>
			<form name="entryform">
				<br />
				<input type="button" value="Cerrar" onClick="if(window.focus) window.opener.focus();window.close();">
				<input type="button" value="Cargar a partir de GPS" onClick="window.location.href='record_route.php?mode=traer_gps&id_micro=<?=$frm["id_micro"]?>';">
			</form>
		</div>
	</body>
</html>

