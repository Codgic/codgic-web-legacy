<?php
require('inc/lang_conf.php');
require('inc/functions.php');
session_start();
header('Content-Type: text/plain; charset=utf-8');

if(!isset($_SESSION['user']) || !isset($_SESSION['administrator']))
	die('您尚未登录...');

if(!isset($_GET['problem_id']))
	die('No argument.');

$prob=intval($_GET['problem_id']);

require('inc/database.php');

$res=mysqli_query($con,"select case_time_limit,memory_limit,case_score,compare_way from problem where problem_id=$prob");
if(!($row=mysqli_fetch_row($res)))
	die('问题不存在...');

$data=array(
	'a'=>$prob,
	'c'=>$row[0],
	'd'=>$row[1],
	'e'=>$row[2],
	'h'=>"rejudge".$prob,
	'j'=>$row[3],
	'k'=>1 //TYPE_rejudge
);
ignore_user_abort(TRUE);
$result = posttodaemon($data);

if(strstr($result,"OK"))
	echo "success";
else if(strstr($result,"another"))
	echo "目前正在执行另一评测任务，请稍后再试。";
else
	echo $result;
?>