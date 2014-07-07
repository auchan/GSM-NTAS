<?php
error_reporting(E_ALL ^ E_NOTICE);
require_once 'excel_reader2.php';
require_once dirname(__FILE__) . '/../Service/ImportService.php';

$fname = "06FREQ.xls";
$fname_array = explode(".", $fname);
$data = new Spreadsheet_Excel_Reader("../upload/files/" . $fname);
$highestRow = $data->rowcount($sheet_index=0);

$chunkSize = 50;
$paths = array("filepath"=>"/sourcefile/".$fname_array[0]. ".csv",
				"sfile"=>"\\sourcefile\\".$fname_array[0]. ".csv",
				"mfile"=>"\\metafile\\".$fname_array[0]. ".xml");
for ($startRow = 1; $startRow <= $highestRow; $startRow += $chunkSize) {
	import($data, $startRow, $chunkSize, $paths, "FREQ");
}
?>
