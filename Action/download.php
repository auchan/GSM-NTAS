<?php
include_once dirname(__FILE__) . '/../conn.php';
extract($_POST);

$table_name = "GSM2.dbo.$fileclass";
//$output_file = dirname(__FILE__) . '/tmp/' . $reqtime . '.tmp';
$output_file = 'D:\\' . $reqtime . '.tmp';
// ?建??文件
$file = fopen($output_file, 'w'); 
fclose($file);
// 把?据?出到??文件
$sql = 'exec export_data \''.$table_name.'\', \'"'.$output_file.'"\'';
odbc_exec($conn, $sql);

$filename = $output_file;
// output file name
$out_filename = "$fileclass.csv";
if(!file_exists($filename)) {
echo 'Not Found ' . $filename;
exit;
} else {
// We'll be outputting a file
@header('Accept-Ranges: bytes');
@header('Accept-Length: ' . filesize($filename));
// It will be called
@header('Content-Transfer-Encoding: binary');
@header('Content-type: application/octet-stream');
@header('Content-Disposition: attachment; filename=' . $out_filename);
@header('Content-Type: application/octet-stream; name=' . $out_filename);
// The source is in filename
$file = @fopen($filename, "r");
echo @fread($file, @filesize($filename));
@fclose($file);
if (file_exists($output_file)) {
    $result=unlink ($output_file);
    //echo $result;
}
exit;
}
?>
