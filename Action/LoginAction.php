<?php
	include('../Service/LoginService.php');
	
	$ls = new LoginService();
	
	//ע����¼
	$lout_rs = $ls->logout();
	if ($lout_rs) {
		if ($lout_rs == 1) {
			echo "<script>location.href='../';</script>";
		}
		else {
			echo '<script>history.go(-1);</script>';
		}
		exit;
	}
	
	//���ʺϷ��Լ��
	if(!($ls->valid_check())) {
		exit('�Ƿ�����!');
	}
	extract($_POST);
	// ��½
	$user = new User($username, $password);
	$login_rs = $ls->login($user);
	if ($login_rs == 1)
	{
		echo "<script>location.href='../index.php';</script>";
		exit;
	}
	exit('�û�����������󣡵���˴� <a href="javascript:history.back(-1);">����</a> ����');
?>