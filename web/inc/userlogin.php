<?php
if(!function_exists('my_rsa'))
	require 'inc/checkpwd.php';
function login($user, $is_cookie, $pwd='')
{
	require 'inc/functions.php';
	require 'inc/database.php';
	$user=mysqli_real_escape_string($con,$user);
	$res=mysqli_query($con,"select password,user_id,language,defunct,email,privilege from users where user_id='$user'");
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
    $_SESSION['email']=$r[4];
	$_SESSION['priv']=$r[5];

	if(!class_exists('preferences')) 
		require 'inc/preferences.php';
	$pref=new preferences();
	$res=mysqli_query($con,"select property,value from preferences where user_id='$user'");
	while($r=mysqli_fetch_row($res)){
		$property=$r[0];
		$pref->$property=$r[1];
	}
	$_SESSION['pref']=serialize($pref);

   $ip=mysqli_escape_string($con,get_ip());
	mysqli_query($con,"update users set accesstime=NOW(),ip='$ip' where user_id='$user'");

	return TRUE;
}
