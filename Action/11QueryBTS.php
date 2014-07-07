<?php
include_once dirname(__FILE__) . '/../conn.php';

$btsname = @$_GET['btsname'];
//$btsname = "BINHELU1";
if ($btsname == "*") {
	$sql = "select * from BTS";
}
else {
	$sql = "exec BTS_info '" . $btsname . "'";
}
$rs = @odbc_exec($conn, $sql);

if (!$rs) {
	$json = array("success"=>false, "error_info"=>iconv("gbk", "utf-8",odbc_errormsg()));
	echo json_encode($json);
	exit;
}
$json = array("success"=>true);
$rownum = 0;
while (odbc_fetch_row($rs)) {
	$btsname = odbc_result($rs, 1);
	$latitude = odbc_result($rs, 2);
	$longitude = odbc_result($rs, 3);
	$altitude = odbc_result($rs, 4);
	$btspower = odbc_result($rs, 6);
	$tup = array("btsname"=>$btsname, "longitude"=>$longitude, "latitude"=>$latitude, 
					"altitude"=>$altitude, "btspower"=>$btspower);
	$json["rows"][] = $tup;
	$rownum++;
}
$json["rownum"] = $rownum;
echo json_encode($json);
exit;
?>