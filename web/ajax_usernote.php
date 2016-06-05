<?php
session_start();
if(!isset($_SESSION['user']))
    die('Not Logged in');
if(!isset($_POST['content'],$_POST['tags'],$_POST['problem_id']))
	die('Wrong argument.');

require('inc/database.php');
$content = mysqli_real_escape_string($con,$_POST['content']);
$tags = mysqli_real_escape_string($con,$_POST['tags']);
$problem_id = intval($_POST['problem_id']);
$user=$_SESSION['user'];

$res=mysqli_query($con,"INSERT INTO user_notes(problem_id,user_id,tags,content,edit_time) VALUES ($problem_id,'$user','$tags','$content',NOW())");
if(!$res){
    if(mysqli_errno($con) == 1062){ //dup
        $res=mysqli_query($con,"UPDATE user_notes set tags='$tags',content='$content',edit_time=NOW() where problem_id=$problem_id and user_id='$user'");
    }
    if(!$res)
        die('error');
}
echo "__ok__";
?>