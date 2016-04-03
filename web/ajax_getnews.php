<?php
require('inc/database.php');
require('inc/ojsettings.php');
if(!isset($_POST['newsid']))
	die('Invalid argument.');
$newsid = $_POST['newsid'];
$res=mysqli_query($con,"select title, content,time from news where news_id=$newsid");
$row=mysqli_fetch_row($res);
$title = $row[0];
$content=$row[1];
$time = $row[2];
$result = $title.'^1a@#FuckZK1#@^a1'.$content.'2b@#^FuckZK2^#@b2'.$time;
echo $result;
?>