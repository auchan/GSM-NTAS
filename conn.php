<?php
	$conn = odbc_connect('GSM2', 'sa', 'password');
	if (!$conn)
	{
		exit("Connection Failed: " . odbc_error());
	}
?>