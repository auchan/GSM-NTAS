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
        <li class="active"><a href="f/traffic-analysis/hours-traffic.php">小时级话务查询</a></li>
        <li><a href="f/traffic-analysis/minutes-traffic.php">分钟级话务量查询</a></li>
		<li><a href="f/traffic-analysis/congestion-traffic.php">拥塞小区查询</a></li>
    </ul>
	<br>
	<div class="panel panel-success">
		<div class="panel-heading">
			<button class="btn btn-primary" type="button" id="search">开始查询</button>
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
		<div class="panel-heading">小时级话务量</div>
		<div class="panel-body">
			<canvas id="canvas1" height="450" width="1000"></canvas>
		</div>
	</div>
	<div class="panel panel-success">
		<div class="panel-heading">小时级拥塞率(*1000)</div>
		<div class="panel-body">
			<canvas id="canvas2" height="450" width="1000"></canvas>
		</div>
	</div>
	<div class="panel panel-success">
		<div class="panel-heading">小时级每线话务量</div>
		<div class="panel-body">
			<canvas id="canvas3" height="450" width="1000"></canvas>
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
<script>
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
		var dest = "Action/14QueryHoursTraffic.php"
		$.getJSON(
		dest,
		{CellID:CellID, startDateTime:startDateTime, endDateTime:endDateTime}, 
		function (data){
			if (data.success) {
				var label_array = new Array();
				var hoursTraffic = new Array();
				var hoursCallcongs = new Array();
				var hoursTrafficPerTCH = new Array();
				for (var i=0; i< data.rownum; i++) {
					label_array[i] = data.rows[i]["datetime"];
					hoursTraffic[i] = parseInt(data.rows[i]["hoursTraffic"]);
					hoursCallcongs[i] = parseInt(data.rows[i]["hoursCallcongs"]*1000);
					hoursTrafficPerTCH[i] = parseInt(data.rows[i]["hoursTrafficPerTCH"]);
				}

				var lineChartData1 = {
					labels : label_array,
					datasets : [
						{
							fillColor : "rgba(220,220,220,0.5)",
							strokeColor : "rgba(220,220,220,1)",
							pointColor : "rgba(220,220,220,1)",
							pointStrokeColor : "#fff",
							data : hoursTraffic
						}
					]
					
				}
				var lineChartData2 = {
					labels : label_array,
					datasets : [
						{
							fillColor : "rgba(151,187,205,0.5)",
							strokeColor : "rgba(151,187,205,1)",
							pointColor : "rgba(151,187,205,1)",
							pointStrokeColor : "#fff",
							data : hoursCallcongs
						}
					]
					
				}
				var lineChartData3 = {
					labels : label_array,
					datasets : [
						{
							fillColor : "rgba(66,220,255,0.5)",
							strokeColor : "rgba(66,220,255,1)",
							pointColor : "rgba(66,220,255,1)",
							pointStrokeColor : "#fff",
							data : hoursTrafficPerTCH
						},
					]
					
				}

				var myLine1 = new Chart(document.getElementById("canvas1").getContext("2d")).Line(lineChartData1);
				var myLine2 = new Chart(document.getElementById("canvas2").getContext("2d")).Line(lineChartData2);
				var myLine3 = new Chart(document.getElementById("canvas3").getContext("2d")).Line(lineChartData3);
			}
		});
	});
	$('#clear').click(function(){
		$("#inputval").val("");
	});
	
</script>
</body> 
</html>
