<?php
session_start();
if(!isset($_SESSION['user'])||!isset($_SESSION['administrator']))
	die('You are not administrator');
if(!isset($_GET['problem_id']))
	die('No such problem');

require('inc/database.php');
$id=intval($_GET['problem_id']);
$result=mysqli_query($con,"select defunct from problem where problem_id=$id");
if($row=mysqli_fetch_row($result)){
	if($row[0]=='N')
		mysqli_query($con,"update problem set defunct='Y' where problem_id=$id");
	else
		mysqli_query($con,"update problem set defunct='N' where problem_id=$id");
	echo('OK');
}
?>