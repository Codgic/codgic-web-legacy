<?php
require 'inc/global.php';
if(!isset($_SESSION['user'])){
    echo _('Please login first...');
    exit();
}
if(!isset($_POST['content'],$_POST['tags'],$_POST['problem_id'])){
	echo _('Invalid Argument...');
    exit();
}

require 'inc/database.php';
$content = mysqli_real_escape_string($con,$_POST['content']);
$tags = mysqli_real_escape_string($con,$_POST['tags']);
$problem_id = intval($_POST['problem_id']);
$user=$_SESSION['user'];

if(empty($content)==1){
    $res=mysqli_query($con,"DELETE FROM user_notes where problem_id=$problem_id and user_id='$user'");
    if($res)
        echo 'success';
    else
        echo _('Something went wrong...');
}else{
    $res=mysqli_query($con,"INSERT INTO user_notes(problem_id,user_id,tags,content,edit_time) VALUES ($problem_id,'$user','$tags','$content',NOW())");
    if(!$res){
        if(mysqli_errno($con) == 1062){ 
            //If already exist
            if($res=mysqli_query($con,"UPDATE user_notes set tags='$tags',content='$content',edit_time=NOW() where problem_id=$problem_id and user_id='$user'"))
                echo 'success';
            else 
                echo _('Something went wrong...');
        }
    }else 
        echo 'success';
}
