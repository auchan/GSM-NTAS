<?php
	session_start();
	if (!isset($_SESSION['username'])) {
	echo '<script>location.href="../../login.php"</script>';
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
	<title>GSM 网络话务分析系统</title>
	<base href="../../" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Bootstrap styles -->
	<!-- Generic page styles -->
	<link rel="stylesheet" href="jQuery-File-Upload/9.5.7/css/style.css">
	<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
	<link rel="stylesheet" href="jQuery-File-Upload/9.5.7/css/jquery.fileupload.css">
	<link rel="stylesheet" href="bootstrap/3.1.1/css/bootstrap.min.css">
	<link href="stylesheets/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
	<link href="/res/skins/flat/blue.css" rel="stylesheet">
	<style>
.form-control {
	background-color:#fff;
}
	</style>
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
                <li class="active"><a href="f/traffic-analysis/">话务分析</a></li>
                <li><a href="f/query-adjacent-areas/">邻区查询</a></li>
            </ul>
        </div>
    </div>
</div>
<div class="container">
	<br>
    <ul class="nav nav-tabs">
        <li><a href="f/traffic-analysis/hours-traffic.php">小时级话务查询</a></li>
        <li class="active"><a href="f/traffic-analysis/minutes-traffic.php">分钟级话务量查询</a></li>
		<li><a href="f/traffic-analysis/congestion-traffic.php">拥塞小区查询</a></li>
    </ul>
	<br>
	<div class="panel panel-success">
		<div class="panel-heading">
			<div class="row">
			<div class="col-md-4">
				<div class="input-group">
				  <span class="input-group-addon" style="background-color:#DFF0D8;">
						<label style="margin-right:10px;"><input type="radio" name="iCheck" value="15" checked>15分钟级</label>
						<label style="margin-right:10px;"><input type="radio" name="iCheck" value="1">1分钟级</label>
				  </span>
					<div class="input-group-btn">
						<button class="btn btn-primary" type="button" id="search">开始查询</button>
					</div>
			</div><!-- /input-group -->
			</div>
			</div>
		</div>
		<div class="panel-body">
				<div class="input-group">
					<div class="input-group date form_datetime" data-link-field="dtp_input1" data-date="1979-09-16 08:00">
						<span class="input-group-btn">
							<button class="btn btn-default" type="button">开始日期</button>
						</span>
						<input style="background-color:#fff;" class="form-control" size="16" type="text" value="" readonly>
						<span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
						<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
					</div>
					<div class="input-group date form_datetime" data-link-field="dtp_input2" data-date="1979-09-16 08:00">
						<span class="input-group-btn">
							<button class="btn btn-default" type="button">结束日期</button>
						</span>
						<input style="background-color:#fff;" class="form-control" size="16" type="text" value="" readonly>
						<span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
						<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
					</div>
					<div class="input-group">
						<span class="input-group-btn">
							<button class="btn btn-default" type="button">@小区ID</button>
						</span>
						<input type="text" class="form-control" placeholder="" id="inputval">
						<span class="input-group-btn">
							<button class="btn btn-default" type="button" id="clear">清空</button>
						</span>
					</div>
					<input type="hidden" id="dtp_input1" value="" />
					<input type="hidden" id="dtp_input2" value="" />
				</div>
		</div>
	</div>
	<div class="panel panel-success">
		<div class="panel-heading">分钟级话务量</div>
		<div class="panel-body">
			<canvas id="canvas1" height="450" width="1000"></canvas>
		</div>
	</div>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">说明</h3>
        </div>
        <div class="panel-body">
            <ul>
				<li>部分查询条件还未进行过滤</li>
				<li>小区ID目前只支持手动输入，如：9023</li>
				<li>10-19-08:00 ~ 09:00 9011 小区</li>
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
<script src="javascripts/Chart.min.js"></script>
<script src="javascripts/bootstrap-datetimepicker.min.js" charset="UTF-8"></script>
<!--script src="javascripts/locales/bootstrap-datetimepicker.fr.js" charset="UTF-8"></script-->
<script src="javascripts/icheck.js"></script>
<script>
	$('input').iCheck({
	checkboxClass: 'icheckbox_flat-blue',
	radioClass: 'iradio_flat-blue'
	});
	
    $('.form_datetime').datetimepicker({
		format: 'yyyy-mm-dd hh:00',
		autoclose:true,
		minView:1,
		startDate: "2007-10-01",
	});
	$('#search').click(function(){
		var startDateTime = $("#dtp_input1").val();
		var endDateTime = $("#dtp_input2").val();
		var CellID = $("#inputval").val();
		var dest = "Action/16QueryMinutesTraffic1.php"
		var type = "minutesTraffic";
		var choice = $('input:radio:checked').val();
		if (choice == 15) {
			dest = "Action/15QueryMinutesTraffic15.php";
			type = "quartersTraffic";
		}
		else if (choice == 1) {
			dest = "Action/16QueryMinutesTraffic1.php";
			type = "minutesTraffic";
		}
		//var type = "quartersTraffic";
		$.getJSON(
		dest,
		{CellID:CellID, startDateTime:startDateTime, endDateTime:endDateTime}, 
		function (data){
			if (data.success) {
				var label_array = new Array();
				var Traffic = new Array();
				//var hoursCallcongs = new Array();
				//var hoursTrafficPerTCH = new Array();
				for (var i=0; i< data.rownum; i++) {
					label_array[i] = data.rows[i]["datetime"];
					Traffic[i] = parseInt(data.rows[i][type]);
				}

				var lineChartData1 = {
					labels : label_array,
					datasets : [
						{
							fillColor : "rgba(220,220,220,0.5)",
							strokeColor : "rgba(220,220,220,1)",
							pointColor : "rgba(220,220,220,1)",
							pointStrokeColor : "#fff",
							data : Traffic
						}
					]
					
				}
				var myLine1 = new Chart(document.getElementById("canvas1").getContext("2d")).Line(lineChartData1);
			}
		});
	});
	$('#clear').click(function(){
		$("#inputval").val("");
	});
	
</script>
</body> 
</html>
