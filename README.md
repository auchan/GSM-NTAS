默认登陆名：admin 密码：gsm

源文件：Sources/

Sources 目录结构：

* Action/    	-- 表现层
* Bean/
* bootstrap/
* DAO/
* f/
* javascripts/
* jquery/
* JQuery-File-Upload/
* php-excel-reader-2.21/
* res/
* Service/						-- 业务逻辑层
* stylesheets/
* upload/
* conn.php						-- 数据库连接配置文件
* index.php				
* login.php						
* signin.css

conn.php 默认内容：
```php
<?php
	$conn = odbc_connect('GSM2', 'sa', 'password');
	if (!$conn)
	{
		exit("Connection Failed: " . odbc_error());
	}
?>
```

odbc_connect()：

* 第一个参数为 ODBC 数据源名称 （这是您所需要设置的ODBC数据源名称）
* 第二个参数为 数据库用户名
* 第三个参数为 数据库密码

res/CreateDatabase.sql		--数据库建库脚本
res/CSV/                    --导入数据功能要用到的数据集