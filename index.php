<?php
	session_start();
	if (!isset($_SESSION['username'])) {
	echo '<script>location.href="login.php"</script>';
	exit;
}	
?>
<!DOCTYPE HTML>
<html lang="zh-cn">
	<head>
	<!-- Force latest IE rendering engine or ChromeFrame if installed -->
	<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
	<meta charset="gb2312">
	<title>GSM 网络话务分析系统</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Bootstrap styles -->
	<link rel="stylesheet" href="bootstrap/3.1.1/css/bootstrap.min.css">
	<!-- Generic page styles -->	
	<link rel="stylesheet" href="jQuery-File-Upload/9.5.7/css/style.css">
	<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
	<link rel="stylesheet" href="jQuery-File-Upload/9.5.7/css/jquery.fileupload.css">
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
				<a class="navbar-brand" href=".">GSM 网络话务分析系统</a>
			</div>
			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav">
					<li><a href="f/data-management/">数据管理</a></li>
					<li><a href="f/information-services/">信息查询</a></li>
					<li><a href="f/traffic-analysis/">话务分析</a></li>
					<li><a href="f/query-adjacent-areas/">邻区查询</a></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="container">
		<h4>图片展示</h4>
		<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
		  <!-- Indicators -->
		  <ol class="carousel-indicators">
			<li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
			<li data-target="#carousel-example-generic" data-slide-to="1"></li>
			<li data-target="#carousel-example-generic" data-slide-to="2"></li>
		  </ol>

		  <!-- Wrapper for slides -->
		  <div class="carousel-inner">
			<div class="item active">
			  <img src="res/gsm1.png" alt="gsm1.png">
			  <div class="carousel-caption">
				话务分析展示
			  </div>
			</div>
			<div class="item">
			  <img src="res/gsm2.png" alt="gsm1.png">
			  <div class="carousel-caption">
				话务分析展示
			  </div>
			</div>
			<div class="item">
			  <img src="res/gsm3.png" alt="gsm1.png">
			  <div class="carousel-caption">
				话务分析展示
			  </div>
			</div>
		  </div>

		  <!-- Controls -->
		  <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
			<span class="glyphicon glyphicon-chevron-left"></span>
		  </a>
		  <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
			<span class="glyphicon glyphicon-chevron-right"></span>
		  </a>
		</div>
		<br>
		<br>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">使用说明</h3>
			</div>
			<div class="panel-body">
				<ul>
					<li>点击最上面的<strong>导航栏</strong>选择功能.</li>
					<li>本页面为首页，用于展示图片</li>
					<li>by auchan （ auchan129@foxmail.com ）</li>
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
	<script>
	/*jslint unparam: true */
	/*global window, $ */
	$(function () {
		'use strict';
		// Change this to the location of your server-side upload handler:
		var url = window.location.hostname === 'blueimp.github.io' ?
					'//jquery-file-upload.appspot.com/' : 'server/php/';
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
	</script>
	</body> 
</html>
