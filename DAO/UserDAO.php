<?php
//包含数据库连接文件
include_once(dirname(__FILE__) . '/../conn.php');
require_once(dirname(__FILE__) . '/../Bean/User.php');

class UserDAO
{
	public function __construct() {
    }
	public function __destruct() {
	   //global $conn;
       //odbc_close($conn);
    }
	public function get_password($username) {
		global $conn;
		$pwd_rs = odbc_exec($conn, "select top 1 password from [user] 
					where username='$username' ");
		if (!$pwd_rs)
		{
			return -1;
		}
		odbc_fetch_row($pwd_rs);
		$pwd = odbc_result($pwd_rs, "password");
		if ($pwd)
		{
			return $pwd;
		}
		return 0;
	}
	public function get_username($username) {
		global $conn;
		//检测用户名及密码是否正确
		$uname_rs = odbc_exec($conn, "select top 1 username from [user] 
					where username='$username' ");
		if (!$uname_rs)
		{
			return -1;
		}
		odbc_fetch_row($uname_rs);
		$uname = odbc_result($uname_rs, "username");
		if ($uname)
		{
			return $uname;
		}
		return 0;
	}
	public function get_uid($username) {
		global $conn;
		$uid_rs = odbc_exec($conn, "select top 1 uid from [user] where username='$username' ");
		if (!$uid_rs) {
			return -1;
		}
		odbc_fetch_row($uid_rs);
		$uid = odbc_result($uid_rs, 'uid');
		if ($uid) {
			return $uid;
		}
		return 0;
	}
	public function add_user($user) {
		global $conn;
		$username = $user->get_username();
		$password = $user->get_password();
		$type = $user->get_type();
		$sql = "insert into [user] (username, password, type)
				values('$username', '$password', $type)
		";
		$rs = odbc_exec($conn, $sql);
		if (!$rs)
		{
			return 0;
		}
		return 1;
	}
	public function delete_user($username) {
		global $conn;
		$sql = "delete from [user] where username='$username' ";
		$rs = odbc_exec($conn, $sql);
		if (!$rs)
		{
			return 0;
		}
		return 1;
	}
	public function delete_via_uid($uid) {
		global $conn;
		$sql = "delete from [user] where uid='$uid' ";
		$rs = odbc_exec($conn, $sql);
		if (!$rs)
		{
			return 0;
		}
		return 1;
	}
	public function get_all_users() {
		global $conn;
		$sql = "select * from [user] ";
		$rs = odbc_exec($conn, $sql);
		if (!$rs)
		{
			return 0;
		}
		$users = array();
		$num = 0;
		while (odbc_fetch_row($rs)) {
			$user = array();
			for ($i = 1; $i < 5; $i++){
				$elem = odbc_result($rs, $i);
				$user[$i-1] = $elem;
			}
			$users[$num] = $user;
			$num += 1;
		}
		return $users;
	}
	public function get_user($uid) {
		global $conn;
		$sql = "select * from [user] where uid = $uid ";
		$rs = odbc_exec($conn, $sql);
		if (!$rs)
		{
			return 0;
		}
		
		$num = 0;
		odbc_fetch_row($rs);
		$user = array();
		for ($i = 1; $i < 5; $i++){
			$elem = odbc_result($rs, $i);
			$user[$i-1] = $elem;
		}
		return $user;
	}
	public function update_user($user) {
		global $conn;
		$uid = $user->get_uid();
		$username = $user->get_username();
		$password = $user->get_password();
		$usertype = $user->get_type();
	
		$sql = "update [user] set username = '$username', password = '$password', type = $usertype 
				where uid = $uid";
		$rs = odbc_exec($conn, $sql);
		if (!$rs)
		{
			return 0;
		}
		return 1;
	}
}
?>