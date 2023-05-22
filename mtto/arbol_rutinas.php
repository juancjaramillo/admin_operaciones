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

arbol_recursive_grupos($nodos);

function arbol_recursive_grupos(&$nodos,$id=-1 )
{
	global $db,$CFG;

	if($id==-1) $condicion="IS NULL";
	else $condicion="='$id'";
	
	$user=$_SESSION[$CFG->sesion]["user"];
	$condicion.=" AND (id_centro IS NULL OR id_centro IN (SELECT id_centro FROM personas_centros WHERE id_persona='$user[id]'))"; 

	$strQuery="SELECT id, nombre FROM mtto.grupos WHERE id_superior $condicion ORDER BY nombre";
	$qid = $db->sql_query($strQuery);
	while ($result =  $db->sql_fetchrow($qid)) {
		$nombre = str_replace("'","\'",$result["nombre"]);
		$nodos.= "{type:'Text', title:'".$result["id"]."', label:'".$nombre."', href:'javascript:submitURL(\'".$result["id"]."\')' ";
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

<table width="100%">
	<tr>
		 <td height="50" width="80%" valign="middle" class="azul_16" align="center">RUTINAS DE MANTENIMIENTO</td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="5" cellspacing="3" class="tabla_form">
				<tr>
					<td>
						<table width="100%" border=1 bordercolor="#7fa840">
							<tr>
								<td align='left' rowspan=4 width="20%">
									<span class="azul_12">GRUPOS</span><br /><br />
										<div id="treeView" style="background-color:white"></div>
								</td>
								<?if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["arbol_equipos"])){?>
							  <td>
									<span class="azul_12">EQUIPOS <div id="msg">&nbsp;</div> </span> <br /><br />
									<iframe name='equipos' width='100%' height='300' frameborder='0' src=""></iframe>
								</td>
								<?}?>
							</tr>
							<?if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["listado_rutinas"])){?>
							<tr>
								<td>
									<span class="azul_12">RUTINAS PREVENTIVAS<div id="msgdos">&nbsp;</div></span><br /><br />
									<iframe name='rutinas' width='100%' height='300' frameborder='0' src=""></iframe>	
								</td>
							</tr>
							<tr>
								<td>
									<span class="azul_12">RUTINAS CORRECTIVAS Y PREDICTIVAS<div id="msgdos">&nbsp;</div></span><br /><br />
									<iframe name='rutinasdos' width='100%' height='300' frameborder='0' src=""></iframe>	
								</td>
							</tr>
							<tr>
								<td>
									<span class="azul_12">RUTINAS INACTIVAS<div id="msgdos">&nbsp;</div></span><br /><br />
									<iframe name='rutinastres' width='100%' height='300' frameborder='0' src=""></iframe>	
								</td>
							</tr>
							<?}?>
							<?/*
							<tr>
								<td>
									<span class="azul_12">ÍTEMS INSPECCIONES <div id="msgtres">&nbsp;</div></span><br /><br />
									<iframe name='inspecciones' width='100%' height='300' frameborder='0' src=""></iframe>	
								</td>
							</tr>
							*/?>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="60" valign="bottom" align="right">
			<?if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["agregarGrupo"])){?>
			<a class="boton_verde" href="javascript:abrirVentanaJavaScript('gruposform','900','500','<?=$CFG->wwwroot?>/mtto/grupos.php?mode=agregar')" title="Agregar Grupo">Agregar Grupo</a>&nbsp;&nbsp;&nbsp;&nbsp;
			<?}if(in_array($_SESSION[$CFG->sesion]["user"]["nivel_acceso"],$CFG->permisos["agregarRutina"])){?>
			<a class="boton_verde" href="javascript:abrirVentanaJavaScript('rutinafo','900','500','<?=$CFG->wwwroot?>/mtto/rutinas.php?mode=agregar')" title="Agregar Rutina">Agregar Rutina</a>&nbsp;&nbsp;&nbsp;&nbsp;
			<?}?>
		</td>
	</tr>
</table>


<script type="text/javascript">

var tree;

YAHOO.widget.TextNode.prototype.displayEditedValue = function (value,editorData) {
    var node = editorData.node;
    node.label = value;
    node.getLabelEl().innerHTML = YAHOO.lang.escapeHTML(value);
};

(function() {
	var treeInit = function() {
		tree = new YAHOO.widget.TreeView("treeView", [
		<?=$nodos?>
		]);
		tree.render();

//		tree.subscribe('dblClickEvent',tree.onEventEditNode);
		
		tree.root.children[1].focus();
		
		tree.subscribe('enterKeyPressed',function(node) {
			YAHOO.util.Dom.get('msg').innerHTML = 'Enter key pressed on node: ' + node.label;
		});
		tree.subscribe('clickEvent',function(oArgs) {
			YAHOO.util.Dom.get('msg').innerHTML = oArgs.node.label;
			YAHOO.util.Dom.get('msgdos').innerHTML = oArgs.node.label;
		});
		tree.subscribe('dblClickEvent',function(oArgs) {
			url = '<?=$CFG->wwwroot?>/mtto/grupos.php?mode=editar&id='+oArgs.node.title;
			abrirVentanaJavaScript('gruposform','900','500',url);
		});
		
			
	};

  YAHOO.util.Event.onDOMReady(treeInit);

})();

function submitURL(id_grupo) {

	url1 = '<?=$CFG->wwwroot?>/mtto/arbol_equipos.php?id_grupo='+id_grupo;
	equipos.document.location.href = url1;
	url2 = '<?=$CFG->wwwroot?>/mtto/templates/listado_rutinas.php?id_grupo='+id_grupo+'&tipo=1';
	rutinas.document.location.href = url2;
	url3 = '<?=$CFG->wwwroot?>/mtto/templates/listado_rutinas.php?id_grupo='+id_grupo;
	rutinasdos.document.location.href = url3;
	url4 = '<?=$CFG->wwwroot?>/mtto/templates/listado_rutinas.php?id_grupo='+id_grupo+'&inactiva';
	rutinastres.document.location.href = url4;
}

</script>

<?include($CFG->dirroot."/templates/footer_2panel.php");?>
