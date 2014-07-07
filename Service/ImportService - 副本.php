<?php
include_once dirname(__FILE__) . '/../conn.php';

$primarykey_set;
function import($data, $startRow, $chunkSize, $paths, $tablename) {
	$filepath = $paths['filepath'];
	$fp = fopen(dirname(__FILE__) . "$filepath", "w"); 
	if ($data->rowcount($sheet_index=0) >= $startRow + $chunkSize - 1) {
		$rownum = $startRow + $chunkSize - 1;
	}
	else {
		$rownum = $data->rowcount($sheet_index=0);
	}
	global $primarykey_set;
	$primarykey_set = array();
	for ($i = $startRow; $i <= $rownum; $i++) {
	  
	  $tup = filter($tablename, $data, $i);
	  if ($tup == -1) {
		// echo '不符合要求的数据'
		continue;
	  }
	  $tup .= "\n";
	  $tup = iconv("utf-8", "gbk", $tup);
	  fwrite($fp, $tup);
	}
	
	fclose($fp);
	$sourcefile = dirname(__FILE__) . $paths['sfile'];
	$metafile = dirname(__FILE__) . $paths['mfile'];
	
	$insert_rs = bulk_insert($tablename, $sourcefile, $metafile);
	if(!$insert_rs) {
		return false;
	}
	return $i - 1;
}
function bulk_insert($tablename, $sourcefile, $metafile) {
	global $conn;
	$sourcefile = addslashes($sourcefile);
	$metafile = addslashes($metafile);
	$sql = "bulk insert $tablename from '$sourcefile'
	with(   
	FIRSTROW=1,
    FIRE_TRIGGERS,
	FIELDTERMINATOR=',',   
	ROWTERMINATOR='\n',
	FORMATFILE = '$metafile'
	)";
	$i = odbc_exec($conn, $sql);	
	echo odbc_errormsg();
	return $i;
}
function filter($tablename, $data, $i) {
	$tup = "";
	if ($tablename == "MS") {
		$col1 = $data->sheets[0]['cells'][$i][1];
		if (!is_numeric($col1))
			return -1;
		$colnum = 8;
		for ($j = 1; $j <= $colnum; $j++) {
			$tup .= $data->sheets[0]['cells'][$i][$j];
			if ($j < $colnum)
				$tup .= ",";
		}
		return $tup;		
	}
	elseif ($tablename == "MSC" || $tablename == "MSCT") {
		$col1 = $data->sheets[0]['cells'][$i][1];
		if (!is_numeric($col1))
			return -1;
		$colnum = 6;
		for ($j = 1; $j <= $colnum; $j++) {
			$tup .= $data->sheets[0]['cells'][$i][$j];
			if ($j < $colnum)
				$tup .= ",";
		}
		return $tup;
	}
	elseif ($tablename == "BSC") {
		$col1 = $data->sheets[0]['cells'][$i][1];
		if (!is_numeric($col1))
			return -1;
		$colnum = 6;
		for ($j = 1; $j <= $colnum; $j++) {
			$tup .= $data->sheets[0]['cells'][$i][$j];
			if ($j < $colnum)
				$tup .= ",";
		}
		return $tup;
	}
	elseif ($tablename == "BTS") {
		$col2 = $data->sheets[0]['cells'][$i][2];
		if (!is_numeric($col2))
			return -1;
		$colnum = 7;
		for ($j = 1; $j <= $colnum; $j++) {
			$tup .= $data->sheets[0]['cells'][$i][$j];
			if ($j < $colnum)
				$tup .= ",";
		}
		return $tup;
	}
	elseif ($tablename == "CELL") {
		$col1 = $data->sheets[0]['cells'][$i][1];
		if (!is_numeric($col1))
			return -1;
		$colnum = 9;
		for ($j = 1; $j <= $colnum; $j++) {
			$tup .= $data->sheets[0]['cells'][$i][$j];
			if ($j < $colnum)
				$tup .= ",";
		}
		return $tup;
	}
	elseif ($tablename == "TRAFFIC") {
		$col1 = $data->sheets[0]['cells'][$i][1];
		if (!is_numeric($col1))
			return -1;
		$colnum = 10;
		// col6 = rate 半速率话务量比
		$col6 = $data->sheets[0]['cells'][$i][6];
		if (!is_numeric($col6)) {
			$data->sheets[0]['cells'][$i][6]=0;
		}
		// col10 = callcongs 拥塞率
		$col10 = $data->sheets[0]['cells'][$i][10];
		if (!is_numeric($col10)) {
			$data->sheets[0]['cells'][$i][10]=0;
		}
		for ($j = 1; $j <= $colnum; $j++) {
			$tup .= $data->sheets[0]['cells'][$i][$j];
			if ($j < $colnum)
				$tup .= ",";
		}
		return $tup;
	}
	else {
		$col1 = $data->sheets[0]['cells'][$i][1];
		if (!is_numeric($col1))
			return -1;
		$colnum = $data->colcount($sheet_index=0);
		for ($j = 1; $j <= $colnum; $j++) {
			$tup .= $data->sheets[0]['cells'][$i][$j];
			if ($j < $colnum)
				$tup .= ",";
		}
		return $tup;
	}
}
?>