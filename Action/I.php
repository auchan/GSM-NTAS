<?php
/** Include PHPExcel_IOFactory */
require_once dirname(__FILE__) . '/../Classes/PHPExcel/IOFactory.php';
require_once dirname(__FILE__) . '/../Classes/PHPExcel/Writer/CSV.php';
include_once(dirname(__FILE__) . '/../conn.php');

date_default_timezone_set('PRC') ;

$filePath = dirname(__FILE__) . "/../upload/files/01MS.xls";
if (!file_exists($filePath)) {
	exit("File do not exist.");
}
$objReader = new PHPExcel_Reader_Excel2007(); 
if(!$objReader->canRead($filePath)){ 
	$objReader = new PHPExcel_Reader_Excel5(); 
}
$sheetname = 'Data Sheet #3'; 


class chunkReadFilter implements PHPExcel_Reader_IReadFilter 
{ 
    private $_startRow = 0; 
    private $_endRow   = 0; 

    /**  Set the list of rows that we want to read  */ 
    public function setRows($startRow, $chunkSize) { 
        $this->_startRow = $startRow; 
        $this->_endRow   = $startRow + $chunkSize; 
    } 

    public function readCell($column, $row, $worksheetName = '') { 
        //  Only read the heading row, and the configured rows 
        if (($row == 1) ||
            ($row >= $this->_startRow && $row < $this->_endRow)) { 
            return true; 
        } 
        return false; 
    } 
} 
// Ã¿´Î¶Á50ÐÐ
$chunkSize = 50;
$chunkFilter = new chunkReadFilter(); 

/**  Tell the Reader that we want to use the Read Filter  **/ 
$objReader->setReadFilter($chunkFilter);  
/**  Load only the rows and columns that match our filter to PHPExcel  **/ 
//$objReader->setReadDataOnly(true);

$highestRow = 0;
/**  Loop to read our worksheet in "chunk size" blocks  **/ 
for ($startRow = 2; $highestRow != 1; $startRow += $chunkSize) { 
    /**  Tell the Read Filter which rows we want this iteration  **/ 
    $chunkFilter->setRows($startRow,$chunkSize); 
    /**  Load only the rows that match our filter  **/ 
    $objPHPExcel = $objReader->load($filePath); 
    //    Do some processing here 
	$sheet = $objPHPExcel->getSheet(0); 
    $highestRow = $sheet->getHighestRow(); 
	
	if ($highestRow == 1)
		break;
	$objWriter = new PHPExcel_Writer_CSV($objPHPExcel);
	$objWriter->setPreCalculateFormulas(false);
	$objWriter->setEnclosure('');
	$objWriter->setLineEnding("\r\n");
	$objWriter->save("tmp/01MS.csv");
	odbc_exec($conn, "exec Bulk_in_MS");
} 
	$rs = array("imported"=>2, "total"=>5);
	echo json_encode($rs);
?>