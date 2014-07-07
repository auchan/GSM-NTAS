<?php
include_once dirname(__FILE__) . '/../conn.php';

$CellID = @$_GET['CellID'];
if ($CellID == "*") {
	$sql = "select * from CELL";
}
else {
	$sql = "exec Cell_info '" . $CellID . "'";
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
	$CellID = odbc_result($rs, 1);
	$BtsName = odbc_result($rs, 2);
	$AreaName = odbc_result($rs, 3);
	$LAC = odbc_result($rs, 4);
	$Bcch = odbc_result($rs, 6);
	$tup = array("CellID"=>$CellID, "BtsName"=>$BtsName, "AreaName"=>$AreaName, 
					"LAC"=>$LAC, "Bcch"=>$Bcch);
	$json["rows"][] = $tup;
	$rownum++;
}
$json["rownum"] = $rownum;
echo json_encode($json);
exit;
?>