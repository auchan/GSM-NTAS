<?php
include_once dirname(__FILE__) . '/../conn.php';

$CellID = @$_GET['CellID'];
$startDateTime = @$_GET['startDateTime'];
$endDateTime = @$_GET['endDateTime'];

//9011, 71014, 0, 71019, 21
//$CellID = 9011;
//$startDateTime = "2007-10-14 00:04:23";
//$endDateTime = "2007-10-19 21:04:23";
function datetime_trans($datetime) {
	$temp1 = explode("-", $datetime);
	$temp2 = explode(" ", $temp1[2]);
	$date = substr($temp1[0], 2, 2) . $temp1[1] . $temp2[0];
	$time = substr($temp2[1], 0, 2);
	return array($date, $time);
}
$startT = @datetime_trans($startDateTime);
$endT = @datetime_trans($endDateTime);
$date_s = $startT[0]; $time_s = $startT[1];
$date_e = $endT[0]; $time_e = $endT[1];
$sql = "exec traffic_min_15 $CellID, $date_s, $time_s, $date_e, $time_e";
$rs = @odbc_exec($conn, $sql);

if (!$rs) {
	$json = array("success"=>false, "error_info"=>iconv("gbk", "utf-8",odbc_errormsg()));
	echo json_encode($json);
	exit;
}
$json = array("success"=>true);
$rownum = 0;
while (odbc_fetch_row($rs)) {
	$data = odbc_result($rs, 1);
	$hour = odbc_result($rs, 2);
	$quarter = odbc_result($rs, 3);
	$quartersTraffic = odbc_result($rs, 4);
	if (strlen($data) == 5) {
		$datetime = "0" . substr($data, 0, 1) . "-" . 
					substr($data, 1, 2) . "-" .
					substr($data, 3, 2) . "-";
		
	}else if (strlen($data) == 6) {
		$datetime = substr($data, 0, 2) . "-" . 
					substr($data, 2, 2) . "-" .
					substr($data, 4, 2) . "-";		
	}
	else {
		$json = array("success"=>false, "error_info"=>"出现了不可预料的错误, ErrorCode:0001");
		odbc_close($conn);
		echo json_encode($json);
		exit;		
	}
	if (strlen($hour) == 1) {
		$datetime .= "0" . $hour;
	}
	else if (strlen($hour) == 2) {
		$datetime .= $hour;
	}
	else {
		$json = array("success"=>false, "error_info"=>"出现了不可预料的错误, ErrorCode:0001");
		odbc_close($conn);
		echo json_encode($json);
		exit;		
	}

	$quarter = intval($quarter, 10);
	$quarter *= 15;
	if ($quarter == 0)
		$quarter = "00";
	$datetime .= ":".$quarter;
	$tup = array("datetime"=>$datetime, "quartersTraffic"=>$quartersTraffic);
	$json["rows"][] = $tup;
	$rownum++;
}
$json["rownum"] = $rownum;
echo json_encode($json);
exit;
?>