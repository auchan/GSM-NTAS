<?php
include_once dirname(__FILE__) . '/../conn.php';

$partdata;
function import($data, $startRow, $chunkSize, $paths, $tablename) {
	$filepath = $paths['filepath'];

	$fp = fopen(dirname(__FILE__) . "$filepath", "w"); 
	if ($data->rowcount($sheet_index=0) >= $startRow + $chunkSize - 1) {
		$rownum = $startRow + $chunkSize - 1;
	}
	else {
		$rownum = $data->rowcount($sheet_index=0);
	}
	global $partdata;
	$partdata = array();
	for ($i = $startRow; $i <= $rownum; $i++) {
	  
	  $tup = filter($tablename, $data, $i);

	  if ($tup === 0) {
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
	global $partdata;
	$tup = "";
	if ($tablename == "MS") {
		$col1 = $data->sheets[0]['cells'][$i][1];
		if (!is_numeric($col1))
			return 0;
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
			return 0;
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
			return 0;
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
			return 0;
		
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
			return 0;
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
			return 0;
		$col2 = $data->sheets[0]['cells'][$i][2];
		$col3 = $data->sheets[0]['cells'][$i][3];
		$primary_key = "$col1" . "$col2" . "$col3";
		if (in_array($primary_key, $partdata)) {
			return 0;
		}
		$partdata[] = $primary_key;
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
			return 0;
		$colnum = $data->colcount($sheet_index=0);
		for ($j = 1; $j <= $colnum; $j++) {
			$tup .= $data->sheets[0]['cells'][$i][$j];
			if ($j < $colnum)
				$tup .= ",";
		}
		return $tup;
	}
}
function import2($fname, $chunkSize, $paths, $tablename) {

	$filepath = dirname(__FILE__) . $paths['filepath'];
	global $partdata;
	$partdata = array();

	$sourcefile = dirname(__FILE__) . $paths['sfile'];
	$metafile = dirname(__FILE__) . $paths['mfile'];	
	$file = fopen("../upload/files/" . $fname,'r'); 
	$linenum = 0;
	$rows = array();
	while ($line = fgetcsv($file)) { //每次读取CSV里面的一行内容
		$tup = filter2($tablename, $line);
		if ($tup == 0) {
			// echo '不符合要求的数据'
			continue;
		}
		
		$linenum++;
		$tup .= "\n";
		//print_r($tup);
		//$tup = iconv("utf-8", "gbk", $tup);
		$rows[] = $tup;
		//print_r($tup);
		if ($linenum == 200) { 
			file_put_contents($filepath, $rows);
			$insert_rs = bulk_insert($tablename, $sourcefile, $metafile);
			unlink($filepath);
			if(!$insert_rs) {
				return false;
			}
			$partdata = array();
			$rows = array();
			$linenum = 0;
		}
	}
	//fclose($fp);
	if ($linenum != 0) {
		file_put_contents($filepath, $rows);
		$insert_rs = bulk_insert($tablename, $sourcefile, $metafile);
		if(!$insert_rs) {
			return false;
		}	
	}
	return true;
}
function filter2($tablename, $line) {
	$colnum = count($line);
	global $partdata;
	$tup = "";
	if ($tablename == "TRAFFIC") {
		$col1 = $line[0];
		if (!is_numeric($col1))
			return 0;
		$col2 = $line[1];
		$col3 = $line[2];
		$primary_key = "$col1" . "$col2" . "$col3";
		if (in_array($primary_key, $partdata)) {
			return 0;
		}
		$partdata[] = $primary_key;
		$colnum = 10;
		// col6 = rate 半速率话务量比
		$col6 = $line[5];
		if (!is_numeric($col6)) {
			$line[5]=0;
		}
		// col10 = callcongs 拥塞率
		$col10 = $line[9];
		if (!is_numeric($col10)) {
			$line[9]=0;
		}
		for ($j = 0; $j < $colnum - 1; $j++) {
			$tup .= $line[$j] . ",";
		}
		$tup .= $line[$colnum - 1];
		return $tup;
	}
	elseif ($tablename == "MS") {
		$col1 = trim($line[0]);
		if (!is_numeric($col1))
			return 0;
		$colnum = 8;
		for ($j = 0; $j < $colnum - 1; $j++) {
			$tup .= $line[$j] . ",";
		}
		$tup .= $line[$colnum - 1];
		return $tup;		
	}
	elseif ($tablename == "MSC" || $tablename == "MSCT") {
		$col1 = $line[0];
		if (!is_numeric($col1))
			return 0;
		$colnum = 6;
		for ($j = 0; $j < $colnum - 1; $j++) {
			$tup .= $line[$j] . ",";
		}
		$tup .= $line[$colnum - 1];
		return $tup;
	}
	elseif ($tablename == "BSC") {
		$col1 = $line[0];
		if (!is_numeric($col1))
			return 0;
		$colnum = 6;
		for ($j = 0; $j < $colnum - 1; $j++) {
			$tup .= $line[$j] . ",";
		}
		$tup .= $line[$colnum - 1];
		return $tup;
	}
	elseif ($tablename == "BTS") {
		$col2 = $line[1];
		if (!is_numeric($col2))
			return 0;
		$colnum = 7;
		for ($j = 0; $j < $colnum - 1; $j++) {
			$tup .= $line[$j] . ",";
		}
		$tup .= $line[$colnum - 1];
		return $tup;
	}
	elseif ($tablename == "CELL") {
		$col1 = $line[0];
		if (!is_numeric($col1))
			return 0;
		$colnum = 9;
		for ($j = 0; $j < $colnum - 1; $j++) {
			$tup .= $line[$j] . ",";
		}
		$tup .= $line[$colnum - 1];
		return $tup;
	}
	else {
		$col1 = $line[0];
		if (!is_numeric($col1))
			return 0;
		for ($j = 0; $j < $colnum - 1; $j++) {
			$tup .= $line[$j] . ",";
		}
		$tup .= $line[$colnum - 1];
		return $tup;
	}
}
?>