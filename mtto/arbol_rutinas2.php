<?
include_once("../application.php");
arbol_recursive_grupos($nodos);

function arbol_recursive_grupos(&$nodos,$id=-1 )
{
	global $db,$CFG;

	if($id==-1) $condicion="IS NULL";
	else $condicion="='$id'";
	
	$user=$_SESSION[$CFG->sesion]["user"];
	if($user["nivel_acceso"]!=1)
		$condicion.=" AND (id_centro IS NULL OR id_centro IN (".implode(",",$user["id_centro"])."))";

	$strQuery="SELECT id, nombre FROM mtto.grupos WHERE id_superior $condicion ORDER BY nombre";
	$qid = $db->sql_query($strQuery);
	while ($result =  $db->sql_fetchrow($qid)) {
		$nombre = str_replace("'","\'",$result["nombre"]);
		$nodos.= "{type:'Text', title:'".$result["id"]."', label:'".$nombre."' ";
		if ($result[0] != $id)
		{
			$nodos.= " ,children: [";
			arbol_recursive_grupos($nodos, $result[0]);
			$nodos.= "]";
		}
		$nodos.= "},";
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title><?=$CFG->siteTitle?> :: <?=nvl($CFG->pageTitle)?></title>
<style type="text/css">
/*margin and padding on body element
  can introduce errors in determining
  element position and are not recommended;
  we turn them off as a foundation for YUI
  CSS treatments. */
body {
	margin:0;
	padding:0;
}
</style>

<link rel="stylesheet" type="text/css" href="../css/cfc.css" />

<link rel="stylesheet" type="text/css" href="http://developer.yahoo.com/yui/build/reset-fonts-grids/reset-fonts-grids.css" />
<link rel="stylesheet" type="text/css" href="http://developer.yahoo.com/yui/build/layout/assets/layout-core.css" />
<script type="text/javascript" src="http://developer.yahoo.com/yui/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="http://developer.yahoo.com/yui/build/element/element-min.js"></script>
<script type="text/javascript" src="http://developer.yahoo.com/yui/build/selector/selector-min.js"></script>
<script type="text/javascript" src="http://developer.yahoo.com/yui/build/layout/layout-min.js"></script>


<link rel="stylesheet" type="text/css" href="http://developer.yahoo.com/yui/build/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="http://developer.yahoo.com/yui/build/treeview/assets/skins/sam/treeview.css" />

<script type="text/javascript" src="http://developer.yahoo.com/yui/build/treeview/treeview-min.js"></script>
<link rel="stylesheet" type="text/css" href="../css/folders/tree.css">

<script type="text/javascript" src="http://developer.yahoo.com/yui/build/yahoo/yahoo-min.js"></script>
<script type="text/javascript" src="http://developer.yahoo.com/yui/build/event/event-min.js"></script>
<script type="text/javascript" src="http://developer.yahoo.com/yui/build/connection/connection-min.js"></script>

</head>
<body class="yui-skin-sam">
   <div id="hd" style="height:100%;">
		<table width="100%" height="100%"><tr><td class="azul_16" style="border-bottom:thin solid #7fa840;">RUTINAS DE MANTENIMIENTO</td></tr></table>
	</div>
  <div id="nav">
		<span class="azul_12">GRUPOS</span><br />
		<div id="treeView" style="background-color:white"></div>
	</div>
  <div id="bd" style="border-left:thin solid #7fa840;height:100%;">
		<div id="tools" style="text-align:left;padding:5px">Tools</div>
		<div id="cent" style="border-top:thin solid #7fa840;text-align:left;padding:5px">Center</div>
		<div id="stat" style="border-top:thin solid #7fa840;">Status</div>
	</div>

<script>
var currentGroup=0;
(function() {
    var Dom = YAHOO.util.Dom,
        Event = YAHOO.util.Event;

		var treeInit = function() {
			tree = new YAHOO.widget.TreeView("treeView", [
			<?=$nodos?>
			]);
			tree.render();

	//		tree.subscribe('dblClickEvent',tree.onEventEditNode);
			
			tree.root.children[0].focus();
			submitURL(tree.root.children[0]);
			
			tree.subscribe('collapse',function(oArgs) {
				submitURL(oArgs);
			});
			tree.subscribe('expand',function(oArgs) {
				submitURL(oArgs);
			});
			tree.subscribe('clickEvent',function(oArgs) {
				submitURL(oArgs.node);
			});
		};

    Event.onDOMReady(function() {
        var layout = new YAHOO.widget.Layout({
            units: [
                { position: 'top', height: 35, body: 'hd' },
                { position: 'left', width: 250, body: 'nav', grids: true , scroll: true },
                { position: 'center', body: 'bd', grids: true , scroll: true }
            ]
        });

        //Handle the resizing of the window
        Event.on(window, 'resize', layout.resize, layout, true);
				layout.on('render', function() {
					var el = layout.getUnitByPosition('center').get('wrap');
	        var layout2 = new YAHOO.widget.Layout(el,{
						parent: layout,
						minWidth: 400,
            minHeight: 300, //So it doesn't get too small
            units: [
                { position: 'top', height: 35, body: 'tools' },
                { position: 'bottom', height: 35, body: 'stat', grids: true },
                { position: 'center', body: 'cent', grids: true }
            ]
  	      });
        	layout2.render();
				});

        layout.render();

        //Handle the resizing of the window
//        Event.on(window, 'resize', layout2.resize, layout, true);

				treeInit();
    });
})();
function submitURL(node) {
//	console.dir(node);
	id_grupo=node.title;
	if(id_grupo!=currentGroup){
		YAHOO.util.Dom.get('cent').innerHTML = 'Actualizando grupo ' + id_grupo + '...';
		makeRequest(id_grupo);
		var path="";
		while(node._type!="RootNode"){
			path=node.label + "/" + path;
			node=node.parent;
		}
		path="/" + path;
		YAHOO.util.Dom.get('tools').innerHTML = path;
	}
	currentGroup=id_grupo;
}

var div = document.getElementById('cent');

var handleSuccess = function(o){

//	YAHOO.log("The success handler was called.  tId: " + o.tId + ".", "info", "example");
	
	if(o.responseText !== undefined){
//		div.innerHTML = "<li>Transaction id: " + o.tId + "</li>";
//		div.innerHTML += "<li>HTTP status: " + o.status + "</li>";
//		div.innerHTML += "<li>Status code message: " + o.statusText + "</li>";
//		div.innerHTML += "<li>HTTP headers: <ul>" + o.getAllResponseHeaders + "</ul></li>";
//		div.innerHTML += "<li>Server response: " + o.responseText + "</li>";
		div.innerHTML = o.responseText;
	}
}

var handleFailure = function(o){
	window.alert('Ocurrió un error comunicándose al servidor');
/*
		YAHOO.log("The failure handler was called.  tId: " + o.tId + ".", "info", "example");

	if(o.responseText !== undefined){
		div.innerHTML = "<ul><li>Transaction id: " + o.tId + "</li>";
		div.innerHTML += "<li>HTTP status: " + o.status + "</li>";
		div.innerHTML += "<li>Status code message: " + o.statusText + "</li></ul>";
	}
*/
}

var callback =
{
	success:handleSuccess,
	failure:handleFailure
};

function makeRequest(id_grupo){
	var request = YAHOO.util.Connect.asyncRequest('GET', 'nodo.php?id_grupo=' + id_grupo, callback);
	YAHOO.log("Initiating request; tId: " + request.tId + ".", "info", "example");
}

</script>
</body>
</html>

