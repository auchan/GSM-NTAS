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
<title>GSM ���绰�����ϵͳ</title>
<base href="../../" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap styles -->
<!-- Generic page styles -->
<link rel="stylesheet" href="jQuery-File-Upload/9.5.7/css/style.css">
<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<link rel="stylesheet" href="jQuery-File-Upload/9.5.7/css/jquery.fileupload.css">
<link rel="stylesheet" href="bootstrap/3.1.1/css/bootstrap.min.css">
<link rel="stylesheet" href="stylesheets/bootstrap-select.min.css">
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
                <li><a href="f/data-management/">���ݹ���</a></li>
                <li class="active"><a href="f/information-services/">��Ϣ��ѯ</a></li>
                <li><a href="f/traffic-analysis/">�������</a></li>
                <li><a href="f/query-adjacent-areas/">������ѯ</a></li>
            </ul>
        </div>
    </div>
</div>
<div class="container">
	<br>
    <ul class="nav nav-tabs">
        <li><a href="f/information-services/BTS-info.php">��վ��Ϣ��ѯ</a></li>
        <li class="active"><a href="f/information-services/CELL-info.php">С����Ϣ��ѯ</a></li>
    </ul>
	<br>
	<div class="panel panel-success">
		<div class="panel-heading">
			<div class="row">
				<div class="col-md-4">
					<div class="input-group" >
						<!--input type="text" class="form-control" placeholder="������С��ID" id="inputval"-->
						<select id="select-cellid" class="selectpicker" data-width="100px">
						<option>��ѡ��</option>
						</select>
						<span class="input-group-btn">
							<button class="btn btn-default" type="button" id="search">��ѯ</button>
						</span>
					</div>
				</div>
				<div class="col-md-offset-11">
					<button class="btn btn-default right" type="button" id="clear-table">��ձ��</button>
				</div>
			</div>
		</div>
		<div class="panel-body">
			<table class="table table-striped table-bordered" id="bts-info">
				<thead>
				<tr>
				  <th>С����ID��</th>
				  <th>��վ��BTS��</th>
				  <th>��������AreaName��</th>
				  <th>λ�������루LAC��</th>
				  <th>�㲥�����ŵ���BCCH��</th>
				</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>

    <div class="panel panel-success">
        <div class="panel-heading">
            <h3 class="panel-title">˵��</h3>
        </div>
        <div class="panel-body">
            <ul>
				<li>���ֲ�ѯ������δ���й���</li>
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
<script src="javascripts/bootstrap-select.min.js"></script>
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
$(function () {
	$("#search").click(function () {
		//var CellID = $("#inputval").val();	
		var CellID = $('.selectpicker').selectpicker('val');
		if (CellID === "��ѡ��") {
			 return 0;
		}
		$.getJSON(
		"Action/12QueryCELL.php",
		{CellID:CellID},
		function (data) {
			if (data.success) {
				for (i=0; i < data.rownum; i++) {
					tr = $("<tr></tr>").appendTo("#bts-info tbody");
					for(var each in data.rows[i]){
						td = $("<td></td>").appendTo(tr);
						$(td).html(data.rows[i][each]);
					}
				}
				if (!data.rownum) {
					$('#inputval').popover('destroy');
					 $('#inputval').popover({content: "С��ID: "+CellID+" �����ݿ��в�����", 
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
	$("#clear").click(function () {
		//$("#bts-info tbody").empty();
		$('#inputval').val("");
		$('#inputval').popover('destroy');
	});
	$("#clear-table").click(function () {
		$("#bts-info tbody").empty();
	});
});
</script>
</body> 
</html>
