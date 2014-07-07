<?php
	include_once dirname(__FILE__) . '/../conn.php';
	$sql = "select Btsname from BTS";
	$rs = @odbc_exec($conn, $sql);
while (odbc_fetch_row($rs)) {
	$Btsname = odbc_result($rs, 1);
	$Btsnames[] = $Btsname;
}
	echo json_encode($Btsnames);
?>