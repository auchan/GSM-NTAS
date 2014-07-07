<?php
/** Include PHPExcel_IOFactory */
require_once dirname(__FILE__) . '/../Classes/PHPExcel/IOFactory.php';
require_once dirname(__FILE__) . '/../Classes/PHPExcel/Writer/CSV.php';

date_default_timezone_set('PRC') ;

$filePath = dirname(__FILE__) . "/../upload/files/附录2-最小数据支撑集-08-v1.xls";
//$filePath = dirname(__FILE__) . "/../upload/files/example1.xls";
if (!file_exists($filePath)) {
	exit("File do not exist.");
}
$objReader = new PHPExcel_Reader_Excel2007(); 
if(!$objReader->canRead($filePath)){ 
	$objReader = new PHPExcel_Reader_Excel5(); 
}


$objReader->setReadDataOnly(true);
set_time_limit(300); 
ini_set("memory_limit", "2048M"); 

$objPHPExcel = $objReader->load($filePath); 
echo $objPHPExcel->getSheetCount();
$objWriter = new PHPExcel_Writer_CSV($objPHPExcel);
$objWriter->setPreCalculateFormulas(false);
$objWriter->setEnclosure('');
$objWriter->setLineEnding("\r\n");
$objWriter->setSheetIndex(0);
$objWriter->save("tmp/01MS.csv");

$objWriter->setSheetIndex(1);
$objWriter->save("tmp/02MSC.csv");

$objWriter->setSheetIndex(2);
$objWriter->save("tmp/03BSC.csv");

$objWriter->setSheetIndex(3);
$objWriter->save("tmp/04BTS.csv");

$objWriter->setSheetIndex(4);
$objWriter->save("tmp/05CELL.csv");

$objWriter->setSheetIndex(5);
$objWriter->save("tmp/06FREQ.csv");

$objWriter->setSheetIndex(6);
$objWriter->save("tmp/07ATENNA.csv");

$objWriter->setSheetIndex(7);
$objWriter->save("tmp/08ADJCELL.csv");

$objWriter->setSheetIndex(8);
$objWriter->save("tmp/09TRAFFIC.csv");

$objWriter->setSheetIndex(9);
$objWriter->save("tmp/10MONITORING.csv");

	$rs = array("imported"=>2, "total"=>5);
	echo json_encode($rs);
?>