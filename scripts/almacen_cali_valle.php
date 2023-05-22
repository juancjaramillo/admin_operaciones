<?
include("../application.php");
include("funciones_comunes.php");
$CFG->db_oracle_dbhost="190.145.2.51";
$CFG->db_oracle_dbname="XE";
$CFG->db_oracle_dbuser="biable01";
$CFG->db_oracle_dbpass="biable01";
$CFG->array_centros=array(1,2);//Cali - Valle

dl("oci8.so");
include("../lib/db/oci.php");
$dbOracle=new sql_db_oci($CFG->db_oracle_dbhost,$CFG->db_oracle_dbuser,$CFG->db_oracle_dbpass,$CFG->db_oracle_dbname);
$result=$dbOracle->sql_row("SELECT MAX(FECHA_GEN) as FECHA FROM CMRESUMEN_INVENTARIO");
$fecha=$result["FECHA"];
print_r($result);
//print_r($total);
$strSQL="
	SELECT CMRESUMEN_INVENTARIO.ID_CO, CMRESUMEN_INVENTARIO.ID_LOCAL, BODEGAS.CMLOCAL_DESCRIPCION, CMRESUMEN_INVENTARIO.ID_ITEM, SUM(CMRESUMEN_INVENTARIO.CAN_EXIS_FIN) AS UNIDADES, MAX(CMRESUMEN_INVENTARIO.COSTO_UNI) AS COSTO
	FROM CMRESUMEN_INVENTARIO JOIN BODEGAS ON CMRESUMEN_INVENTARIO.ID_LOCAL=BODEGAS.ID_LOCAL
	WHERE (CMRESUMEN_INVENTARIO.ID_CO='001' OR CMRESUMEN_INVENTARIO.ID_CO='002') AND CMRESUMEN_INVENTARIO.FECHA_GEN='$fecha'
	GROUP BY CMRESUMEN_INVENTARIO.ID_CO, CMRESUMEN_INVENTARIO.ID_LOCAL, BODEGAS.CMLOCAL_DESCRIPCION, CMRESUMEN_INVENTARIO.ID_ITEM
";
print_r($strSQL);
$result=$dbOracle->sql_row("SELECT COUNT(*) AS TOTAL FROM ($strSQL)");
$total=$result["TOTAL"];
print_r($total);
echo "\n";
$qid=$dbOracle->sql_query($strSQL);
$i=0;
while($result=$dbOracle->sql_fetchrow($qid)){
	$item=$dbOracle->sql_row("SELECT ITEMS.*, GRUPO_CONTABLE.DESCRIPCION AS GRUCON FROM ITEMS JOIN GRUPO_CONTABLE ON ITEMS.ID_GRUCON=GRUPO_CONTABLE.ID_GRUCON WHERE ITEMS.ID_ITEM='$result[ID_ITEM]'");
//	print_r($item);
	$elemento=array();
	$elemento["codigo"]=trim($result["ID_ITEM"]);
	$elemento["elemento"]=trim($item["DESCRIPCION"]);
	$elemento["id_unidad"]=unidades(trim($item["UNIMED_INV_1"]));
	$elemento["tipoe"]=tipo(trim($item["GRUCON"]));
	$strSQL="
		SELECT id FROM mtto.elementos
		WHERE codigo='$elemento[codigo]' AND elemento='$elemento[elemento]' AND id_unidad='$elemento[id_unidad]'
			AND id IN (SELECT id_elemento FROM mtto.elementos_centros WHERE id_centro IN(" . implode($CFG->array_centros,",") . "))
	";
	if(!$elemento["id"]=$db->sql_field($strSQL)){
		$elemento["id"]=$db->sql_insert("mtto.elementos",$elemento);
		foreach($CFG->array_centros AS $j => $id_centro){
			$qInsert=$db->sql_query("INSERT INTO mtto.elementos_centros (id_elemento,id_centro) VALUES ('$elemento[id]','$id_centro')");
		}
	}
	else{//Verificar vínculo con mtto.elementos_centros
		$qUpdate=$db->sql_query("UPDATE mtto.elementos SET tipoe='$elemento[tipoe]' WHERE id='$elemento[id]'");
		foreach($CFG->array_centros AS $j => $id_centro){
			if(!$resultado=$db->sql_row("SELECT * FROM mtto.elementos_centros WHERE id_elemento='$elemento[id]' AND id_centro='$id_centro'")){
				$qInsert=$db->sql_query("INSERT INTO mtto.elementos_centros (id_elemento,id_centro) VALUES ('$elemento[id]','$id_centro')");
			}
		}
	}
	verificar_existencias($elemento["id"],trim($result["ID_CO"]),trim($result["ID_LOCAL"]),trim($result["CMLOCAL_DESCRIPCION"]),trim($result["UNIDADES"]),trim($result["COSTO"]));
	echo "\r" . $i;

	$i++;
//	if($i>50) break;
}
echo "\n";

?>
