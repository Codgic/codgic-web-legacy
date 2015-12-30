<?php
header("Content-type:text/html;charset=utf-8");  
if(!isset($_POST['type'],$_POST['nick'],$_POST['email'],$_POST['school']))
	die('Invalid argument.');
if(strlen($_POST['nick'])>190)
	die('昵称太长');
if(strlen($_POST['school'])>60)
	die('学校名太长');
if(strlen($_POST['email'])>60)
	die('邮箱太长');
if($_POST['type']=='profile'){
	if(!isset($_POST['oldpwd']))
		die('Invalid argument.');
	session_start();
	if(!isset($_SESSION['user']))
		die('Not logged in.');
	$user=$_SESSION['user'];
	require('inc/database.php');
    require_once('inc/checkpwd.php');
    if(!password_right($user, $_POST['oldpwd']))
		die('旧密码不正确');
	
	$query='update users set email=\''.mysql_real_escape_string($_POST['email']).'\',school=\''.mysql_real_escape_string($_POST['school']).'\',nick=\''.mysql_real_escape_string($_POST['nick']).'\'';
	if(isset($_POST['newpwd']) && $_POST['newpwd']!=''){
		$len=strlen($_POST['newpwd']);
		if($len<6||$len>50)
			die('密码不符合要求(至少6位)');
		$query.=',password=\''.mysql_real_escape_string(my_rsa($_POST['newpwd'])).'\'';
	}
	$query.=" where user_id='$user'";
	mysql_query($query);
	echo "用户信息更新成功";
}else if($_POST['type']=='reg'){
	if(!isset($_POST['userid'],$_POST['newpwd']))
		die('Invalid argument.');
	require('inc/database.php');
	$user=mysql_real_escape_string(trim($_POST['userid']));
	$len=strlen($user);
	if($len==0)
		die('用户名不可为空');
	if($len>20)
		die('用户名太长了');
	if(preg_match('/\W/',$user))
		die('用户名只能由字母，数字或是下划线构成');

	$len=strlen($_POST['newpwd']);
	if($len<6||$len>50)
		die('密码不符合要求(至少6位)');
	$pwd=mysql_real_escape_string($_POST['newpwd']);

	mysql_query("insert into users (user_id,email,password,reg_time,nick,school,defunct) values ('$user','".mysql_real_escape_string($_POST['email'])."','$pwd',NOW(),'".mysql_real_escape_string($_POST['nick'])."','".mysql_real_escape_string($_POST['school'])."','Y')");
	$code=mysql_errno();
	if($code==0)
		echo '成功创建用户';
	else if($code==1062)
		echo "用户'$user' 已经存在";
	else 
		echo "未知错误 =.=";
}
?>