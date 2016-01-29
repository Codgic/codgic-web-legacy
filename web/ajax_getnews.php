<?php
require('inc/database.php');
require('inc/ojsettings.php');
if(!isset($_POST['newsid']))
	die('Invalid argument.');
$newsid = $_POST['newsid'];
$row=mysqli_fetch_row(mysqli_query($con,"select title, content from news where news_id=$newsid"));
$title = $row[0];
$content = $row[1];
$result = $title.'Z9EWKWRFE324@EWRFTFFWE443R854QSFDSUERWE4EFRDN'.$content;
echo $result;
?>