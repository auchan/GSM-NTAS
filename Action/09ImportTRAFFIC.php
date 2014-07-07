<?php
error_reporting(E_ALL ^ E_NOTICE);
require_once 'excel_reader2.php';
require_once dirname(__FILE__) . '/../Service/ImportService.php';

//$fname = "09TRAFFIC.xls";
$fname = $_GET['fname'];
$fname_array = explode(".", $fname);
$chunkSize = 50;
$tmpfile = md5($fname) . time();
$paths = array("filepath"=>"/sourcefile/".$tmpfile. ".csv",
				"sfile"=>"\\sourcefile\\".$tmpfile. ".csv",
				"mfile"=>"\\metafile\\09TRAFFIC.xml");
if ($fname_array[1] == "xls") {
	$data = new Spreadsheet_Excel_Reader("../upload/files/" . $fname);
	$highestRow = $data->rowcount($sheet_index=0);
	for ($startRow = 1; $startRow <= $highestRow; $startRow += $chunkSize) {
		$irs = import($data, $startRow, $chunkSize, $paths, "TRAFFIC");
		if (!irs)
			break;
	}
}
else if ($fname_array[1] == "txt" || $fname_array[1] == "csv") {
	$irs = import2($fname, $chunkSize, $paths, "TRAFFIC");
}
else {
// 删除 上传的文件 和 csv 文件
if (file_exists("../upload/files/" . $fname)) {
	unlink ("../Service/sourcefile/" . $tmpfile . ".csv");
    $result=unlink ("../upload/files/" . $fname);
    //echo $result;
}
	$rs = array("success"=>false, "error_info"=>"文件类型错误");
	echo json_encode($rs);
	exit;
}
// 删除 上传的文件 和 csv 文件
if (file_exists("../upload/files/" . $fname)) {
	unlink ("../Service/sourcefile/" . $tmpfile . ".csv");
    $result=unlink ("../upload/files/" . $fname);
    //echo $result;
}
$rs = array("success"=>true);
if (!$irs) {
	$rs['success'] = false;
	$rs['error_info'] = "数据库访问出错";
}
echo json_encode($rs);
?>
