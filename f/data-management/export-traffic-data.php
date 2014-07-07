<?php
	session_start();
	if (!isset($_SESSION['username'])) {
		header('Location: ../../login.php');
		exit;
	}	
?>
<!DOCTYPE HTML>
<!--
/*
 * jQuery File Upload Plugin Basic Demo 1.2.4
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2013, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
-->
<html lang="zh-cn">
	<head>
		<!-- Force latest IE rendering engine or ChromeFrame if installed -->
		<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
		<meta charset="gb2312">
		<title>���ݹ��� - GSM ���绰�����ϵͳ</title>
		<base href="../../" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<!-- Bootstrap styles -->
		<link rel="stylesheet" href="bootstrap/3.1.1/css/bootstrap.min.css">
		<!-- Generic page styles -->
		<link rel="stylesheet" href="jQuery-File-Upload/9.5.7/css/style.css">
		<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
		<link rel="stylesheet" href="jQuery-File-Upload/9.5.7/css/jquery.fileupload.css">
		<link href="/res/skins/flat/blue.css" rel="stylesheet">
	</head>
	<body>
		<div class="navbar navbar-default navbar-fixed-top">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-fixed-top .navbar-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href=".">GSM ���绰�����ϵͳ</a>
				</div>
				<div class="navbar-collapse collapse">
					<ul class="nav navbar-nav">
						<li class="active"><a href="f/data-management/">���ݹ���</a></li>
						<li><a href="f/information-services/">��Ϣ��ѯ</a></li>
						<li><a href="f/traffic-analysis/">�������</a></li>
						<li><a href="f/query-adjacent-areas/">������ѯ</a></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="container">
			<br>
			<ul class="nav nav-tabs">
				<li><a href="f/data-management/import-network-config.php">����������Ϣ����</a></li>
				<li><a href="f/data-management/export-network-config.php">����������Ϣ����</a></li>
				<li><a href="f/data-management/import-traffic-data.php">�������ݵ���</a></li>
				<li class="active"><a href="f/data-management/export-traffic-data.php">�������ݵ���</a></li>
			</ul>
			<br>
			<div class="panel panel-default">
				<div class="panel-body">
					<label style="margin-right:10px;"><input type="radio" name="iCheck" value="TRAFFIC" checked>���Ӽ���������</label>
				</div>
			</div>
			<br>
			<span id="import" class="btn btn-success">
				<i class="glyphicon glyphicon-export"></i>
				<span>��ʼ����</span>
			</span>	
			<br>
			<br>
			<div id="import-progress" class="progress">
				<div class="progress-bar progress-bar-success"></div>
			</div>
			<div id="import-state"></div>
			<br>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">ʹ��˵��</h3>
				</div>
				<div class="panel-body">
					<ul>
						<li>ѡ����������ѡ��</li>
						<li>���"��ʼ����",�����ݵ��뵽�ļ�</li>
						<li>������棬���շ��������͵��ļ�</li>
						<li>���</li>
					</ul>
				</div>
			</div>
		</div>
		<script src="jquery/1.11.1/jquery.min.js"></script>
		<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
		<script src="jQuery-File-Upload/9.5.7/js/vendor/jquery.ui.widget.js"></script>
		<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
		<script src="jQuery-File-Upload/9.5.7/js/jquery.iframe-transport.js"></script>
		<!-- The basic File Upload plugin -->
		<script src="jQuery-File-Upload/9.5.7/js/jquery.fileupload.js"></script>
		<!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
		<script src="bootstrap/3.1.1/js/bootstrap.min.js"></script>
		<script src="javascripts/icheck.js"></script>
		<script>
$(document).ready(function(){
  $('input').iCheck({
    checkboxClass: 'icheckbox_flat-blue',
    radioClass: 'iradio_flat-blue'
  });
  $('input').on('ifChecked', function(event){
 // alert(event.type + ' callback');
 // ����Ѿ��ϴ��õ��ļ�
	$('#files').empty();
	// ��̨ҲҪ���С�����
});
	$('#import').click(function() {
		var choice = $('input:radio:checked').val();
		var fileclass = choice;
		$('#import span').html("���ڵ���...");
		var form=$("<form>");//����һ��form��
		form.attr("style","display:none");
		form.attr("target","");
		form.attr("method","post");
		form.attr("action","Action/download.php");
		var input1=$("<input>");
		input1.attr("type","hidden");
		input1.attr("name","reqtime");
		input1.attr("value",(new Date()).getMilliseconds());

		var input2=$("<input>");
		input2.attr("type","hidden");
		input2.attr("name","fileclass");
		input2.attr("value",fileclass);
		$("body").append(form);//����������web��
		form.append(input1);
		form.append(input2);
		form.submit();
		$('#import span').html("��ʼ����");
	});
});
		
function resetProgressbar() {
	$('#files').empty();
	$('#progress .progress-bar').css(
		'width',
		0 + '%'
	);
};
		</script>
	</body> 
</html>
