Ĭ�ϵ�½����admin ���룺gsm

Դ�ļ���Sources/

Sources Ŀ¼�ṹ��

* Action/    	-- ���ֲ�
* Bean/
* bootstrap/
* DAO/
* f/
* javascripts/
* jquery/
* JQuery-File-Upload/
* php-excel-reader-2.21/
* res/
* Service/						-- ҵ���߼���
* stylesheets/
* upload/
* conn.php						-- ���ݿ����������ļ�
* index.php				
* login.php						
* signin.css

conn.php Ĭ�����ݣ�
```php
<?php
	$conn = odbc_connect('GSM2', 'sa', 'password');
	if (!$conn)
	{
		exit("Connection Failed: " . odbc_error());
	}
?>
```

odbc_connect()��

* ��һ������Ϊ ODBC ����Դ���� ������������Ҫ���õ�ODBC����Դ���ƣ�
* �ڶ�������Ϊ ���ݿ��û���
* ����������Ϊ ���ݿ�����

res/CreateDatabase.sql		--���ݿ⽨��ű�
res/CSV/                    --�������ݹ���Ҫ�õ������ݼ�