<?php
	session_start();
	if (!isset($_SESSION['username'])) {
		header('Location: ../../login.php');
		exit;
	}
	header('Location: import-network-config.php');
?>

