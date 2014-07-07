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
	<link rel="stylesheet" href="stylesheets/bootstrap-select.min.css">
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
                <li><a href="f/traffic-analysis/">话务分析</a></li>
                <li class="active"><a href="f/query-adjacent-areas/">邻区查询</a></li>
            </ul>
        </div>
    </div>
</div>
<div class="container">
	<br>
	<div class="panel panel-success">
		<div class="panel-heading">
			<button class="btn btn-primary" type="button" id="search">开始查询</button>
		</div>
		<div class="panel-body">
				<div class="input-group">
					<div class="input-group">
						<span class="input-group-btn">
							<button class="btn btn-default" type="button">小区ID</button>
						</span>
						<!--input type="text" class="form-control" placeholder="" id="inputval"-->
						  <select id="select-cellid" class="selectpicker" data-width="100px">
						  <option>请选择</option>
						  </select>
						<!--span class="input-group-btn">
							<button class="btn btn-default" type="button" id="clear">清空</button>
						</span-->
					</div>
				</div>
		</div>
	</div>
	<div class="panel panel-success">
		<div class="panel-heading">
			<div class="row">
				<div class="col-md-2" style="height:200%;line-height:200%;">
					邻区信息
				</div>
				<div class="col-md-offset-11">
					<button class="btn btn-default right" type="button" id="clear-table">清空表格</button>
				</div>
			</div>
		<!--button class="btn btn-default" type="button" id="clear-table">清空表格</button-->
		</div>
		<div class="panel-body">
			<table class="table table-striped table-bordered" id="cong-traffic-info">
				<thead>
				<tr>
				  <th>本小区ID（CellID）</th>
				  <th>邻区ID（Adj-CellID）</th>
				  <th>距离（Distance）</th>
				</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">说明</h3>
        </div>
        <div class="panel-body">
            <ul>
				<li>部分查询条件还未进行过滤</li>
				<li></li>
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
<script src="javascripts/bootstrap-select.min.js"></script>
<!--script src="javascripts/locales/bootstrap-datetimepicker.fr.js" charset="UTF-8"></script-->
<script>

$(document).ready(function (){
	$.getJSON(
	"Action/100GetAllCellID.php",
	function (data) {	
		for (var i in data) {
			var option = $("<option></option>").html(data[i]).appendTo('#select-cellid');
		}
		$('.selectpicker').selectpicker('refresh');
	});
});

  $('.selectpicker').selectpicker({
	size: 6
  });

    $('.form_datetime').datetimepicker({
		format: 'yyyy-mm-dd hh:00',
		autoclose:true,
		minView:1,
		startDate: "2007-10-01",
	});
	$('#search').click(function(){
		//var CellID = $("#inputval").val();
		var CellID = $('.selectpicker').selectpicker('val');
		if (CellID == "请选择") {
			return;
		}
		var dest = "Action/18QueryAdjcellInfo.php"
		$.getJSON(
		dest,
		{CellID:CellID},
		function (data) {
			if (data.success) {
				for (i=0; i < data.rownum; i++) {
					tr = $("<tr></tr>").appendTo("#cong-traffic-info tbody");
					// 添加本小区ID
					td = $("<td></td>").appendTo(tr);
					$(td).html(CellID);
					for(var each in data.rows[i]){
						td = $("<td></td>").appendTo(tr);
						$(td).html(data.rows[i][each]);
					}
				}
				if (!data.rownum) {
					$('#inputval').popover('destroy');
					 $('#inputval').popover({content: "小区ID: "+CellID+" 在数据库中不存在", 
					 placement:'bottom',
					 trigger:'manual'
					 });	
					 $('#inputval').popover('show')
				}
				else{
					$('#inputval').popover('destroy');
				}
			}
			else {
				//alert(data.error_info);
			}
		});
	});
	$('#clear').click(function(){
		$("#inputval").val("");
		$('#inputval').popover('destroy');
	});
	$("#clear-table").click(function () {
		$("#cong-traffic-info tbody").empty();
	});	
</script>
</body> 
</html>
