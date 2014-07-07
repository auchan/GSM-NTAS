<?php
include('../DAO/UserDAO.php');

class LoginService
{
	public function __construct() {
       session_start();
    }
	public function __destruct() {
   }
	public function logout() {
		if(isset($_GET['action'])){
			if ($_GET['action'] == "logout") {
				unset($_SESSION['username']);
				return 1;
			}
		}
		return 0;
	}
	public function login($user) {
		$password = md5($user->get_password());
		$username = $user->get_username();
		//检测用户名及密码是否正确
		$userdao = new UserDAO();
		$pwd = $userdao->get_password($username);
		if ($pwd == -1)
		{
			return -1;
		}
		if (!is_numeric($pwd) and $pwd == $password)
		{
			$_SESSION['username'] = $username;
			return 1;
		}
		return 0;
	}
	public function valid_check() {
		//return isset($_POST['submit']);
		return true;
	}
}
?>