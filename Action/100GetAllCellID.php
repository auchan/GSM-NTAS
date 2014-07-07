<?php
	include_once dirname(__FILE__) . '/../conn.php';
	$sql = "select CellID from CELL";
	$rs = @odbc_exec($conn, $sql);
while (odbc_fetch_row($rs)) {
	$CellID = odbc_result($rs, 1);
	$CellIDs[] = $CellID;
}
	echo json_encode($CellIDs);
?>