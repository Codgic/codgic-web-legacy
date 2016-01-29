<?php
if(!isset($_POST['id']))
	die('Invaild argument.');
session_start();
if(!isset($_SESSION['user']))
	die('然而你并没有登录。');

require('inc/database.php');

$uid=($_SESSION['user']);
if('all'==$_POST['id']){
	mysqli_query($con,"update solution set public_code=1 where user_id='$uid'");
}else{
	$id=intval($_POST['id']);
	mysqli_query($con,"update solution set public_code=(!public_code) where solution_id=$id and user_id='$uid'");
	if(1==mysqli_affected_rows($con))
		echo "success";
	else
		echo "failed";
}
?>
