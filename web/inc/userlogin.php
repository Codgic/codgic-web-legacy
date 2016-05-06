<?php

require_once 'inc/checkpwd.php';
header("Content-type:text/html;charset=utf-8"); 
function login($user, $is_cookie, $pwd='')
{
	require 'inc/database.php';
	$user=mysqli_real_escape_string($con,$user);
	$res=mysqli_query($con,"select password,user_id,language,defunct from users where user_id='$user'");
	$r=mysqli_fetch_row($res);
	if(!$r)
		return ("用户不存在!");
	if($r[3]!='N')
		return ("您的帐户仍在被管理员审核，请耐心等待~");

	if(!$is_cookie && !password_right($user, $pwd))
		return ("用户名/密码错误!");

	session_unset();
	setcookie('SID', '', 31415926);
	$_SESSION['user']=$r[1];
	$_SESSION['lang']=$r[2];
	
	$res=mysqli_query($con,"select rightstr from privilege where user_id='$user'");
	while($r=mysqli_fetch_row($res)){
		if($r[0]=='administrator' || $r[0]=='source_browser' || $r[0]=='insider')
			$_SESSION[$r[0]]=true;
	}
	
	require_once 'inc/preferences.php';
	$pref=new preferences();
	$res=mysqli_query($con,"select property,value from preferences where user_id='$user'");
	while($r=mysqli_fetch_row($res)){
		$property=$r[0];
		$pref->$property=$r[1];
	}
	$_SESSION['pref']=serialize($pref);

	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip=mysqli_escape_string($con,$_SERVER['HTTP_CLIENT_IP']);
    }
	else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip=mysqli_escape_string($con,$_SERVER['HTTP_X_FORWARDED_FOR']);
    }
    else {
		$ip=mysqli_escape_string($con,$_SERVER['REMOTE_ADDR']);
	}
	mysqli_query($con,"update users set accesstime=NOW(),ip='$ip' where user_id='$user'");

	return TRUE;
}