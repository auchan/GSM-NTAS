<?php
include_once dirname(__FILE__) . '/../conn.php';

$CellID = @$_GET['CellID'];

//9011, 71014, 0, 71019, 21
//$CellID = 9011;

$sql = "exec adj_info $CellID";
$rs = @odbc_exec($conn, $sql);

if (!$rs) {
	$json = array("success"=>false, "error_info"=>iconv("gbk", "utf-8",odbc_errormsg()));
	echo json_encode($json);
	exit;
}
$json = array("success"=>true);
$rownum = 0;
while (odbc_fetch_row($rs)) {
	$adjcellID = odbc_result($rs, 1);
	$distance = odbc_result($rs, 2);

	$tup = array("adjcellID"=>$adjcellID, "distance"=>$distance);
	$json["rows"][] = $tup;
	$rownum++;
}
$json["rownum"] = $rownum;
echo json_encode($json);
exit;
?>