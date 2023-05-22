<?
ob_start();
include("../application.php");
include("funciones_comunes.php");
$CFG->db_oracle_dbhost="190.145.2.51";
$CFG->db_oracle_dbname="XE";
$CFG->db_oracle_dbuser="biable01";
$CFG->db_oracle_dbpass="biable01";
$CFG->array_centros=array(1,2);//Cali - Valle

dl("oci8.so");
include("../lib/db/oci.php");
include("snoopy/snoopy.inc");
$snoopy = new Snoopy;
$url="http://69.60.111.94/pa/scripts/post.php";

$dbOracle=new sql_db_oci($CFG->db_oracle_dbhost,$CFG->db_oracle_dbuser,$CFG->db_oracle_dbpass,$CFG->db_oracle_dbname);
$strSQL="
SELECT CODIGO,ID_TERC, ID_SUC, ID_NDC, ESTADO, ID_EMP, ID_CO,
 ID_CCOSTO, FECHA_INGRESO, FECHA_RETIRO, ID_CARGO, SALARIO, CANTIDAD,
 APELLIDO1, APELLIDO2, NOMBRES
FROM CONTRATOS JOIN NMEMPLEADOS ON ID_TERC = EMPLEADO
";
print_r($strSQL);
$result=$dbOracle->sql_row("SELECT COUNT(*) AS TOTAL FROM ($strSQL)");
$total=$result["TOTAL"];
print_r($total);
echo "\n";
$qid=$dbOracle->sql_query($strSQL);
$i=0;
while($result=$dbOracle->sql_fetchrow($qid)){
	echo "$i\n";
	print_r($result);
	$i++;
	if($i%5==0){
		$out=ob_get_contents();
		$submit_vars["output"]=$out;
		$snoopy->submit($url,$submit_vars);
		ob_clean();
	}
//	if($i>50) break;
}
echo "\n";


$strSQL="SELECT * FROM CARGOS";
print_r($strSQL);
echo "\n";
$qid=$dbOracle->sql_query($strSQL);
$i=0;
while($result=$dbOracle->sql_fetchrow($qid)){
	echo "$i\n";
	print_r($result);
	$i++;
	if($i%5==0){
		$out=ob_get_contents();
		$submit_vars["output"]=$out;
		$snoopy->submit($url,$submit_vars);
		ob_clean();
	}
}
echo "\n";



ob_end_clean();
?>
