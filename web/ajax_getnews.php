<?php
require('inc/database.php');
require('inc/ojsettings.php');
if(!isset($_POST['newsid']))
	die('Invalid argument.');
$newsid = intval($_POST['newsid']);

$res=mysqli_query($con,"select title,content,time,importance from news where news_id=$newsid");
$row=mysqli_fetch_row($res);
$arr=array('title'=>$row[0],'content'=>$row[1],'time'=>$row[2],'importance'=>$row[3]);

echo json_encode($arr);
?>