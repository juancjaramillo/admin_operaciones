<?
include("../application.php");


$total = $peso = 0;
$qid = $db->sql_query("SELECT id,  peso_total, fecha_entrada, fecha_salida,id_vehiculo 
	FROM rec.pesos 
	WHERE id_vehiculo = 87 AND fecha_entrada::date >= '2012-06-01' AND fecha_entrada::date <= '2012-06-30' ORDER BY fecha_entrada");
while($query = $db->sql_fetchrow($qid))
{
	$total+= $query["peso_total"];
	preguntar($query);
	//$peso = 0;
	$qidMP = $db->sql_query("SELECT mp.*, m.inicio, m.final, id_vehiculo 
		FROM rec.movimientos_pesos mp 
		LEFT JOIN rec.movimientos m ON m.id=mp.id_movimiento
		WHERE mp.id_peso=".$query["id"]);
	while($ps = $db->sql_fetchrow($qidMP))
	{
		$asumar =($query["peso_total"]*$ps["porcentaje"])/100; 
		$peso += $asumar;

		preguntar($ps);
		if(!preg_match("/2012-06/",$ps["inicio"],$match))
		{
			echo "NO ES JUNIO<BR>";
		}
		if($ps["id_vehiculo"] != 87)
			echo "NO ES VEHICULO87<BR>";

		preguntar("peso a sumar ". $asumar);
	}

	echo "VAN GRAN TOTAL :".$total."  VS peso movimientos: ".$peso;
	echo "<BR>********************<BR>";
}
echo "PESO SUMA TOTAL ".$total;


/*
$peso = 0;
$con = "SELECT m.id, m.combustible, v.codigo||'/'||v.placa||' / '||centro as vehiculo, m.id_vehiculo, to_char(inicio, 'D') as dia, extract(dow from inicio) as dowinicio, inicio, final 
	FROM rec.movimientos m 
	LEFT JOIN micros i ON i.id=m.id_micro 
	LEFT JOIN ases a ON a.id=i.id_ase 
	LEFT JOIN centros c ON c.id=a.id_centro 
	LEFT JOIN vehiculos v ON v.id=m.id_vehiculo 
	WHERE inicio::date >= '2012-06-01' AND inicio::date<='2012-06-30' and m.id_vehiculo = 87 
	ORDER BY inicio";
$qid = $db->sql_query($con);
while($query = $db->sql_fetchrow($qid))
{
	preguntar($query);
	preguntar("PESOS : ");
	$consulta =  "SELECT mp.*, p.peso_inicial, p.peso_final, p.peso_total, p.id_vehiculo
			FROM rec.movimientos_pesos mp
			LEFT JOIN rec.movimientos m ON m.id=mp.id_movimiento
			LEFT JOIN rec.pesos p ON p.id=mp.id_peso
			WHERE mp.id_movimiento=".$query["id"]." AND p.id_vehiculo = 87";
	$qidPr = $db->sql_query($consulta);
	while($queryPeso = $db->sql_fetchrow($qidPr))
	{ 
		$pesoNeto = 0;
//		if($queryPeso["peso_inicial"] != "" && $queryPeso["peso_final"] != "") $pesoNeto = abs($queryPeso["peso_inicial"]-$queryPeso["peso_final"]);
	//	elseif($queryPeso["peso_total"] != "") $pesoNeto = $queryPeso["peso_total"];
		$pesoNeto = $queryPeso["peso_total"];
		$peso += ($pesoNeto*$queryPeso["porcentaje"])/100;
		preguntar($queryPeso);

	}
	echo "/********************";
}

echo "PESO SUMA TOTAL ".$peso;
*/


?>







