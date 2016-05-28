<?php
require('inc/database.php');
require('inc/ojsettings.php');
require('inc/functions.php');
session_start(); 

if(!isset($_POST['type']))
	die('Invalid argument.');

if($_POST['type'] == 'match'){
	if(!isset($_POST['usercode']))
	    die('Invalid argument.');
	if(!isset($_SESSION['resetpwd_code'])||empty($_SESSION['resetpwd_code']))
		die('未知错误 =.=');
	if($_POST['usercode']==$_SESSION['resetpwd_code']) {
     $_SESSION['resetpwd_flag']=1;
		echo 'success';
    }
	else {
     $_SESSION['resetpwd_wrongnum']++;
     if($_SESSION['resetpwd_wrongnum'] >= 3) echo 'fuckyou';
		else echo 'fail';
    }
}

else if($_POST['type'] == 'verify'){
	if(!isset($_POST['user'],$_POST['email']))
		die('Invalid argument.');
	$user = mysqli_real_escape_string($con,$_POST['user']);
	if(preg_match('/\W/',$user))
		die('无效的用户名');
	$email = $_POST['email'];
	$result=mysqli_query($con,"select email from users where user_id='$user'");
	if(!($row=mysqli_fetch_row($result)) || !$row[0])
		die('用户不存在!');
	if($row[0] != $email)
		die('邮箱错误!');
	if(!isset($_SESSION['resetpwd_code'])||empty($_SESSION['resetpwd_code']))
		die('timeout');
	$code = $_SESSION['resetpwd_code'];
   $_SESSION['resetpwd_user'] = $user;
   $_SESSION['resetpwd_email'] = $email;
	 echo resetpwd_mail();
}

else if($_POST['type'] == 'resend'){
	echo resetpwd_mail();
}

else if($_POST['type'] == 'update'){
	if(!isset($_POST['newpwd']))
	    die('Invalid argument.');
	if(!isset($_SESSION['resetpwd_user']) || empty($_SESSION['resetpwd_user']) || !isset($_SESSION['resetpwd_flag']) || $_SESSION['resetpwd_flag']!=1)
		die('身份验证超时，请刷新页面重新开始...');
	require_once('inc/checkpwd.php');
	$user = $_SESSION['resetpwd_user'];
	$len=strlen($_POST['newpwd']);
	if($len<6||$len>50)
		die('密码不符合要求(至少6位)');
	$query='update users set password=\''.mysqli_real_escape_string($con,my_rsa($_POST['newpwd'])).'\'';
	$query.=" where user_id='$user'";
	$_SESSION['resetpwd_flag']=0;
	if(mysqli_query($con,$query)){
		echo 'success';
     session_destroy();
  }
	else
		echo '未知错误 =.=';
}

else die('Invalid argument.');
?>