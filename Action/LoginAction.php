<?php
	include('../Service/LoginService.php');
	
	$ls = new LoginService();
	
	//注销登录
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
	
	//访问合法性检测
	if(!($ls->valid_check())) {
		exit('非法访问!');
	}
	extract($_POST);
	// 登陆
	$user = new User($username, $password);
	$login_rs = $ls->login($user);
	if ($login_rs == 1)
	{
		echo "<script>location.href='../index.php';</script>";
		exit;
	}
	exit('用户名或密码错误！点击此处 <a href="javascript:history.back(-1);">返回</a> 重试');
?>