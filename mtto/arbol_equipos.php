<?
include_once("../application.php");
include($CFG->dirroot."/templates/header_arbol.php");

if(!isset($_SESSION[$CFG->sesion]["user"])){
  $errorMsg="No existe la sesión.";
  error_log($errorMsg);
  die($errorMsg);
}
$user=$_SESSION[$CFG->sesion]["user"];
verificarPagina(simple_me($ME));
$id_grupo = $_GET["id_grupo"];
arbol_recursive_equipos($nodos,"-1",$id_grupo);

function arbol_recursive_equipos(&$nodos,$id=-1,$id_grupo)
{
	global $db,$CFG;

	if($id==-1) $condicion="IS NULL";
	else $condicion="='$id'";

	$user=$_SESSION[$CFG->sesion]["user"];
	$condicion.=" AND (mtto.equipos.id_centro IS NULL OR mtto.equipos.id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]'))";

	$strQuery="SELECT mtto.equipos.id, mtto.equipos.nombre FROM mtto.equipos LEFT JOIN vehiculos on mtto.equipos.id_vehiculo = vehiculos.id
		WHERE id_superior $condicion AND (vehiculos.id_estado!=4  or vehiculos.id_estado is null) AND mtto.equipos.id_grupo=$id_grupo ORDER BY nombre;";
	$qid = $db->sql_query($strQuery);
	while ($result =  $db->sql_fetchrow($qid)) {
		$nombre = str_replace("'","\'",$result["nombre"]);
		$url = $CFG->wwwroot."/mtto/equipos.php?mode=hoja_vida&id_equipo=".$result["id"];
		$nodos.= "{type:'Text', label:'".$nombre."', href:'javascript:abrirVentanaJavaScript(\'hojavidaequipos\',\'900\',\'600\',\'".$url."\')' ";
		if ($result[0] != $id)
		{
			$nodos.= " ,children: [";
			arbol_recursive_equipos($nodos, $result[0], $id_grupo);
			$nodos.= "]";
		}
		$nodos.= "},";
	}
}

?>

<table width="100%">
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#fff">
							<tr>
								<td align='left' rowspan=2 width="20%">
									<div id="treeView" style="background-color:white"></div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<?if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["agregarEquipo"])){?>
		<td align="right" valign="bottom" height="50"><a class="boton_verde" href="javascript:abrirVentanaJavaScript('equiposform','900','500','<?=$CFG->wwwroot?>/mtto/equipos.php?mode=agregar&id_grupo=<?=$id_grupo?>')" title="Agregar Equipo">Agregar Equipo</a>&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<?}?>
	</tr>
</table>

<div id="msg">&nbsp;</div>
<script type="text/javascript">

//global variable to allow console inspection of tree:
var tree;

YAHOO.widget.TextNode.prototype.displayEditedValue = function (value,editorData) {
    var node = editorData.node;
    node.label = value;
    node.getLabelEl().innerHTML = YAHOO.lang.escapeHTML(value);
};

//anonymous function wraps the remainder of the logic:
(function() {
	var treeInit = function() {
		tree = new YAHOO.widget.TreeView("treeView", [
		<?=$nodos?>
		]);
		tree.render();

		tree.subscribe('dblClickEvent',tree.onEventEditNode);
		
		tree.root.children[1].focus();
		
		tree.subscribe('enterKeyPressed',function(node) {
			YAHOO.util.Dom.get('msg').innerHTML = 'Enter key pressed on node: ' + node.label;
		});
		tree.subscribe('clickEvent',function(oArgs) {
//			YAHOO.util.Dom.get('msg').innerHTML = 'Click on node: ' + oArgs.node.label;
		});
		tree.subscribe('dblClickEvent',function(oArgs) {
			YAHOO.util.Dom.get('msg').innerHTML = 'Double click on node: ' + oArgs.node.label;
		});
	};

	//Add an onDOMReady handler to build the tree when the document is ready
    YAHOO.util.Event.onDOMReady(treeInit);
})();

</script>

<?include($CFG->dirroot."/templates/footer_2panel.php");?>
