<?php
require 'inc/privilege.php';
session_start();
if(!check_priv(PRIV_PROBLEM))
	die('你没有权限...');
if(!isset($_GET['problem_id']))
	die('问题不存在...');

require 'inc/database.php';
$id=intval($_GET['problem_id']);
$result=mysqli_query($con,"select defunct from problem where problem_id=$id");
if($row=mysqli_fetch_row($result)){
	if($row[0]=='N') $opr='Y';
    else $opr='N';
    if(mysqli_query($con,"update problem set defunct='$opr' where problem_id=$id"))
        echo 'success';
    else
        echo '系统错误...';
}
?>