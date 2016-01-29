<?php
header('Cache-Control: max-age=155520000');
if(!isset($_GET['message_id']))
	die('Wrong argument.');
$msg=intval($_GET['message_id']);
if(!$msg)
	die('Wrong argument.');

require('inc/database.php');

$res=mysqli_query($con,'select content from message where message_id='.$msg);
if($res){
	$row=mysqli_fetch_row($res);
	if($row)
		echo htmlspecialchars($row[0]);
}
?>
