<?php
require('inc/database.php');
require('inc/mailsettings.php');
require('inc/ojsettings.php');
require ('inc/ojencrypt.php');
session_start(); 

if(!isset($_POST['type']))
	die('Invalid argument.');
$nowtime = date("Y/m/d H:i:s");
if($_POST['type'] == 'encode'){
	if(!isset($_POST['usercode']))
	    die('Invalid argument.');
	$usercode = $rsa -> encrypt($_POST['usercode']);
	die ($usercode);
}

else if($_POST['type'] == 'verify'){
	if(!isset($_POST['user'],$_POST['email'],$_POST['code']))
		die('Invalid argument.');
	$user = $_POST['user'];
	if(preg_match('/\W/',$user))
		die('无效的用户名');
	$email = $_POST['email'];
	$code = $_POST['code'];
	$code = $rsa -> decrypt($code);
	$result=mysqli_query($con,"select email from users where user_id='$user'");
	if(!($row=mysqli_fetch_row($result)) || !$row[0])
		die ('用户不存在!');
	if($row[0] != $email)
		die('邮箱错误!');
	$subject='CWOJ 密码重置验证';
    $content="<div>亲爱的 {$user} ,<br><p>我们收到了您在CWOJ (https://www.cwoj.tk) 重置密码的请求并发送了验证码来确认您的身份。</p><p>请求时间: <b>{$nowtime} (UTC+08:00)</b></p><p>验证码: <b>{$code}</b></p><p>如果您没有在CWOJ有过重置密码的请求，您只需忽略这封邮件并不要把验证码告诉任何人。<br>如有任何问题，请回复该邮件来与管理员取得联系。</p><br>谢谢！<p>{$oj_copy}</p></div>";
    postmail($email,$subject,$content);
}

else if($_POST['type'] == 'resend'){
	if(!isset($_POST['user'],$_POST['email'],$_POST['code']))
	  die('Invalid argument.');
    $user = $_POST['user'];
	if(preg_match('/\W/',$user))
		die('无效的用户名');
	$email = $_POST['email'];
	$code = $_POST['code'];
	$code = $rsa -> decrypt($code);
	$subject='CWOJ 密码重置验证';
    $content="<div>亲爱的 {$user} ,<br><p>我们收到了您在CWOJ (https://www.cwoj.tk) 重置密码的请求并发送了验证码来确认您的身份。</p><p>请求时间: <b>{$nowtime} (UTC+08:00)</b></p><p>验证码: <b>{$code}</b></p><p>如果您没有在CWOJ有过重置密码的请求，您只需忽略这封邮件并不要把验证码告诉任何人。<br>如有任何问题，请回复该邮件来与管理员取得联系。</p><br>谢谢！<p>{$oj_copy}</p></div>";
    postmail($email,$subject,$content);
}

else if($_POST['type'] == 'setflag'){
	$_SESSION['resetpwd_flag']=1;
	echo 'flagged';
}

else if($_POST['type'] == 'update'){
	if(!isset($_POST['user'],$_POST['newpwd']))
	    die('Invalid argument.');
	if(!isset($_SESSION['resetpwd_flag']) || $_SESSION['resetpwd_flag']==0)
		die('身份验证失败，请刷新页面重新开始...');
	require_once('inc/checkpwd.php');
	$user = $_POST['user'];
	if(preg_match('/\W/',$user))
		die('无效的用户名');
	$len=strlen($_POST['newpwd']);
	if($len<6||$len>50)
		die('密码不符合要求(至少6位)');
	$query='update users set password=\''.mysqli_real_escape_string($con,my_rsa($_POST['newpwd'])).'\'';
	$query.=" where user_id='$user'";
	$_SESSION['resetpwd_flag']=0;
	if(mysqli_query($con,$query))
		echo 'success';
	else
		echo '未知错误 =.=';
}

else die('Invalid argument.');
?>