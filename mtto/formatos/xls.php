<?
require_once($CFG->libdir . "/writeexcel/class.writeexcel_workbook.inc.php");
require_once($CFG->libdir . "/writeexcel/class.writeexcel_worksheet.inc.php");

$fname = tempnam("/tmp", "matriz.xls");
$workbook =& new writeexcel_workbook($fname);
$worksheet =& $workbook->addworksheet('Libro1');

$heading  =& $workbook->addformat(array(
                                        'bold'    => 1,
                                        'size'    => 12,
                                        'align'    => 'center',
                                        ));

$headings = array();
for ($i=0; $i<$db->sql_numfields($qid); $i++){
	array_push($headings,$db->sql_fieldname($i,$qid));
}
$row=1;
while ($result=$db->sql_fetchrow($qid)) {
	for ($col=0; $col<$db->sql_numfields($qid); $col++){
		$worksheet->write($row,$col,$result[$col]);
	}
	$row++;
}

$worksheet->write_row('A1', $headings, $heading);

$workbook->close();

header("Content-Type: application/x-msexcel; name=\"matriz_llantas.xls\"");
header("Content-Disposition: inline; filename=\"matriz_llantas.xls\"");
$fh=fopen($fname, "rb");
fpassthru($fh);
unlink($fname);

?>
