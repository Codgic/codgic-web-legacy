<?php
require 'inc/database.php';
require 'inc/ojsettings.php';
session_start();
if($require_auth==1 && !isset($_SESSION['user'])) 
    die('你没有权限...');
if(!isset($_POST['newsid']))
	die('Invalid argument.');
$newsid = intval($_POST['newsid']);

$res=mysqli_query($con,"select title,content,time,importance from news where news_id=$newsid");
$row=mysqli_fetch_row($res);
if(empty($row[1])) $row[1]='本条新闻内容为空...';
$arr=array('title'=>$row[0],'content'=>$row[1],'time'=>$row[2],'importance'=>$row[3]);

echo json_encode($arr);
?>