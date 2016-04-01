<?php
function UserExist($uid){
	if(preg_match('/\W/',$uid))
		return false;
	$res=mysqli_query($con,"select user_id from users where user_id='$uid'");
	if($res && mysqli_num_rows($res))
		return true;
	return false;
}
session_start();
header('Content-Type: text/html; charset=utf-8');
if(!isset($_SESSION['user']))
	die('Not Logged in');
if(!isset($_GET['op']))
	die('Not supported');
$op = $_GET['op'];

require('inc/database.php');
require('inc/mail_flags.php');

if($op=='check'){
	$uid=$_SESSION['user'];
	$res=mysqli_query($con,"select sum(new_mail) from mail where UPPER(defunct)='N' and to_user='$uid'");
	if($res && ($row=mysqli_fetch_row($res)) && $row[0])
		echo $row[0];
	else
		echo '0';
}else if($op=='send'){
	$from=$_SESSION['user'];

	if(!isset($_POST['touser']) || strlen($touser=mysqli_real_escape_string($con,trim($_POST['touser'])))==0)
		die('收信人不能为空');
	if(!UserExist($touser))
		die('用户不存在');
	if(!isset($_POST['title']) || strlen($title=mysqli_real_escape_string($con,trim($_POST['title'])))==0)
		die('标题不能为空');
	if(isset($_POST['detail']))
		$detail=mysqli_real_escape_string($con,$_POST['detail']);
	else
		$detail='';
	mysqli_query($con,"insert into mail (from_user,to_user,title,content,in_date) VALUES ('$from','$touser','$title','$detail',NOW())");

	echo '__OK__';
}else{
	if(!isset($_GET['mail_id']))
		die('Wrong argument.');
	$mail=intval($_GET['mail_id']);

	if($op=='show'){
		$res=mysqli_query($con,"select content,new_mail,to_user from mail where UPPER(defunct)='N' and mail_id=$mail");
		if($res && ($row=mysqli_fetch_row($res))){
			if(strcasecmp($_SESSION['user'], $row[2]))
				exit(0);
			echo htmlspecialchars($row[0]);
			if($row[1])
				mysqli_query($con,"update mail set new_mail=0 where mail_id=$mail");
		}
	}else if($op=='delete'){
		$res=mysqli_query($con,"select to_user from mail where UPPER(defunct)='N' and mail_id=$mail");
		if($res && ($row=mysqli_fetch_row($res))){
			if(strcasecmp($row[0],$_SESSION['user'])==0)
				mysqli_query($con,"update mail set defunct='Y' where mail_id=$mail");
		}
	}else if($op=='star'){
		$uid=$_SESSION['user'];
		$mask=MAIL_FLAG_STAR;
		mysqli_query($con,"update mail set flags=(flags ^ $mask) where to_user='$uid' and mail_id=$mail");
		if(mysqli_affected_rows($con)==1)
			echo  '__OK__';
	}
}
