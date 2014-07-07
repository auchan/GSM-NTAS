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
				<li class="active"><a href="f/data-management/import-network-config.php">����������Ϣ����</a></li>
				<li><a href="f/data-management/export-network-config.php">����������Ϣ����</a></li>
				<li><a href="f/data-management/import-traffic-data.php">�������ݵ���</a></li>
				<li><a href="f/data-management/export-traffic-data.php">�������ݵ���</a></li>
			</ul>
			<br>
			<div class="panel panel-default">
				<div class="panel-body">
					<label style="margin-right:10px;"><input type="radio" name="iCheck" value="MS" checked>MS</label>
					<label style="margin-right:10px;"><input type="radio" name="iCheck" value="BTS">BTS</label>
					<label style="margin-right:10px;"><input type="radio" name="iCheck" value="CELL">С��</label>
					<label style="margin-right:10px;"><input type="radio" name="iCheck" value="BSC">BSC</label>
					<label style="margin-right:10px;"><input type="radio" name="iCheck" value="MSC">MSC</label>
				</div>
			</div>
			<br>
			<!-- The fileinput-button span is used to style the file input field as button -->
			<span class="btn btn-success fileinput-button">
				<i class="glyphicon glyphicon-plus"></i>
				<span>ѡ���ļ�</span>
				<!-- The file input field used as target for the file upload widget -->
				<input id="fileupload" type="file" name="files[]" multiple>
			</span>
			<br>
			<br>
			<!-- The global progress bar -->
			<div id="progress" class="progress">
				<div class="progress-bar progress-bar-success"></div>
			</div>
			<!-- The container for the uploaded files -->
			<div id="files" class="files"></div>
			<br>
			<span id="import" class="btn btn-success">
				<i class="glyphicon glyphicon-import"></i>
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
						<li>���"ѡ���ļ�",���ļ��ϴ���������</li>
						<li>���"��ʼ����",���ļ����뵽���ݿ�</li>
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
		var fname = $('#files').children().html();
		if (typeof fname === "undefined") {
			alert("�����ϴ��ļ���");
			return;
		}

		var choice = $('input:radio:checked').val();
		var dest;
		switch (choice) {
		case "MS":
			dest = "Action/01ImportMS.php";
			break;
		case "MSC":
			dest = "Action/02ImportMSC.php";
			break;
		case "BSC":
			dest = "Action/03ImportBSC.php";
			break;
		case "BTS":
			dest = "Action/04ImportBTS.php";
			break;
		case "CELL":
			dest = "Action/05ImportCELL.php";
			break;
		default:
			break;
		}
		$('#import span').html("���ڵ���...");
		$.getJSON(
		dest,
		{fname:fname}, 
		function (data){
			$('#import span').html("��ʼ����");
			if (data.success) {
				$('<p/>').text("����ɹ���").appendTo('#import-state');
				var progress = parseInt(1 / 1 * 100, 10);
				$('#import-progress .progress-bar').css(
					'width',
					progress + '%'
				);
				resetProgressbar();
			}else {
				$('<p/>').text("����ʧ�ܣ�(������ͻ)").appendTo('#import-state');
				var progress = parseInt(0 / 1 * 100, 10);
				$('#import-progress .progress-bar').css(
					'width',
					progress + '%'
				);
				resetProgressbar();
			}
		});
	});
});
		/*jslint unparam: true */
		/*global window, $ */
		$(function () {
			'use strict';
			// Change this to the location of your server-side upload handler:
			//var url = 'jQuery-File-Upload/9.5.7/server/php/';
			var url = 'upload/';
			$('#fileupload').fileupload({
				url: url,
				dataType: 'json',
				done: function (e, data) {
					$.each(data.result.files, function (index, file) {
						$('<p/>').text(file.name).appendTo('#files');
					});
				},
				progressall: function (e, data) {
					var progress = parseInt(data.loaded / data.total * 100, 10);
					$('#progress .progress-bar').css(
						'width',
						progress + '%'
					);
				}
			}).prop('disabled', !$.support.fileInput)
				.parent().addClass($.support.fileInput ? undefined : 'disabled');
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
