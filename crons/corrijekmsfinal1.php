<?
error_reporting(E_ALL);
ini_set("display_errors", 1);
$finicio = '2013-01-01';
$ffinal = date ( "Y-m-d" , time()-86400);	 
#$finicio = '2013-01-01';
#$ffinal =  '2013-06-30';	 
// operaciones : báscula
 include(dirname(__FILE__) . "/../application.php");
///Consulto todos los movimientos
#$consulta = "update rec.movimientos set km_final=rec.john.km_final from rec.john
#where rec.movimientos.id=rec.john.id ";
#		echo $consulta;
#		$qid = $db->sql_query($consulta);

?>
