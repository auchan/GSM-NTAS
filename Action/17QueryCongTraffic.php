<?php
include_once dirname(__FILE__) . '/../conn.php';

$congthreshold = @$_GET['congthreshold'];
$startDateTime = @$_GET['startDateTime'];
$endDateTime = @$_GET['endDateTime'];

//9011, 71014, 0, 71019, 21
//$congthreshold = 0.2;
//$startDateTime = "2007-10-01 00:04:23";
//$endDateTime = "2007-10-31 21:04:23";
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
$sql = "exec cong $congthreshold, $date_s, $time_s, $date_e, $time_e";
$rs = @odbc_exec($conn, $sql);

if (!$rs) {
	$json = array("success"=>false, "error_info"=>iconv("gbk", "utf-8",odbc_errormsg()));
	echo json_encode($json);
	exit;
}
$json = array("success"=>true);
$rownum = 0;
while (odbc_fetch_row($rs)) {
	$CellID = odbc_result($rs, 1); 
	$date = odbc_result($rs, 2);
	$hour = odbc_result($rs, 3);
	$hoursTraffic = odbc_result($rs, 4);
	$hourscongsnum = odbc_result($rs, 5);
	$hoursrate = odbc_result($rs, 6);
	if (strlen($date) == 5) {
		$date = "0" . substr($date, 0, 1) . "-" . 
					substr($date, 1, 2) . "-" .
					substr($date, 3, 2);
		
	}else if (strlen($date) == 6) {
		$date = substr($date, 0, 2) . "-" . 
					substr($date, 2, 2) . "-" .
					substr($date, 4, 2);		
	}
	else {
		$json = array("success"=>false, "error_info"=>"出现了不可预料的错误, ErrorCode:0001");
		odbc_close($conn);
		echo json_encode($json);
		exit;		
	}

	if (strlen($hour) == 1) {
		$hour_s = "0" . $hour;
	}
	else {
		$hour_s = $hour;
	}
	
	if (strlen(strval(intval($hour)+1)) == 1) {
		$hour_e = "0" . strval(intval($hour)+1);
	}
	else {
		$hour_e = strval(intval($hour)+1);
	}
	
	$period = $hour_s . " ~ " . $hour_e;
	$tup = array("CellID"=>$CellID, "date"=>$date, "period"=>$period, "hoursTraffic"=>$hoursTraffic, 
				"hourscongsnum"=>$hourscongsnum, "hoursrate"=>$hoursrate);
	$json["rows"][] = $tup;
	$rownum++;
}
$json["rownum"] = $rownum;
echo json_encode($json);
exit;
?>