<?
$frm=$_GET;
if(!isset($frm["dirroot"])) die("Error: " . __FILE__ . ":" . __LINE__);
if(!isset($frm["module"])) die("Error: " . __FILE__ . ":" . __LINE__);
if(!isset($frm["field"])) die("Error: " . __FILE__ . ":" . __LINE__);
if(!file_exists($frm["dirroot"] . "/application.php")) die("Error: " . __FILE__ . ":" . __LINE__);
include($frm["dirroot"] . "/application.php");
$fileModule=$CFG->modulesdir . "/" . $frm["module"] . ".php";
if(!file_exists($fileModule)) die("Error: " . __FILE__ . ":" . __LINE__);
header('Content-Type: text/html;charset=iso-8859-1');
include($fileModule);
$att=$entidad->getAttributeByName($frm["field"]);
unset($att->parent);
$ACWhere=$att->ACWhere;
$ACFields=$att->ACFields;
$ACLabel=$att->ACLabel;
$ACIdField=$att->ACIdField;
$ACFrom=$att->ACFrom;
$ACLimit=$att->ACLimit;
if(trim($ACWhere)!="") $where=$ACWhere;
else $where="true";

$centro = $db->sql_row("SELECT a.id_centro FROM rec.movimientos m LEFT JOIN micros r ON r.id=m.id_micro LEFT JOIN ases a ON a.id=r.id_ase WHERE m.id=".$frm["id_movimiento"]);
$where.= " AND id_centro=".$centro["id_centro"];


if(trim($frm["s"])!=""){
	if(get_magic_quotes_gpc()) $ACLabel=stripslashes($ACLabel);
	$arrayValues=explode(" ",$frm["s"]);
	$arregloValores=array();
	$arrayFields=explode(",",$ACFields);
	for($j=0;$j<sizeof($arrayValues);$j++){
		$arrayValues[$j]=iconv("UTF-8", "ISO-8859-1//IGNORE", $arrayValues[$j]);

		$arregloCampos=array();
		for($i=0;$i<sizeof($arrayFields);$i++){
			if(trim($arrayFields[$i])!=""){
				if(SQL_LAYER == "postgresql") $arregloCampos[]=trim($arrayFields[$i]) . " ~* '\\\\m" . $arrayValues[$j] . "'";
				else $arregloCampos[]=trim($arrayFields[$i]) . " REGEXP '[[:<:]]" . $arrayValues[$j] . "'";
			}
		}
		$arregloValores[]="(" . implode(" OR ",$arregloCampos) .  ")";

	}
	$where.=" AND (" . implode(" AND ", $arregloValores) . ")";
}

$strQuery="
SELECT DISTINCT " . $ACIdField . ", " . $ACLabel . "
FROM " . $ACFrom . "
WHERE " . $where . "
ORDER BY 2
LIMIT $ACLimit
";
error_log($strQuery);
//file_put_contents('/tmp/autocomplete.log',$strQuery . "=====",FILE_APPEND);
/*
if($_SERVER["REMOTE_ADDR"]=="192.168.0.7"){
	preguntar($strQuery);
}
*/
if($att->ACbd != "")
	$qid=$att->ACbd->sql_query($strQuery);
else
	$qid=$db->sql_query($strQuery);


while($result=$db->sql_fetchrow($qid)){
	echo $result[1] . "\t";
	$label=htmlnumericentities($result[0]);
	if(trim($label)=="") $label=" ";
	echo $label . "\n";
}

function htmlnumericentities($str){
	  return preg_replace('/[^!-%\x27-;=?-~ ]/e', '"&#".ord("$0").chr(59)', $str);
};//EoFn htmlnumericentities

?>
