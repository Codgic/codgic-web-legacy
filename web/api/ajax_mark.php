<?php
require __DIR__.'/../inc/init.php';
require __DIR__.'/../conf/database.php';


if(!isset($_SESSION['user'],$_GET['prob'],$_GET['op'],$_GET['type']))
    exit();
    
$user=$_SESSION['user'];
$problem_id=intval($_GET['prob']);

if($_GET['type']==1) 
    $type='problem';
else if($_GET['type']==2)
    $type='contest';
else if($_GET['type']==3)
    $type='wiki';
else{
    echo _('Invalid Argument...');
    exit();
}

if($_GET['op']=='rm_saved'){
	mysqli_query($con,"DELETE from saved_$type where user_id='$user' and {$type}_id=$problem_id");
}else if($_GET['op']=='add_saved'){
	mysqli_query($con,"INSERT into saved_$type set {$type}_id=$problem_id,user_id='$user',savetime=NOW()");
}

if(mysqli_affected_rows($con)===1)
	echo 'success';
else
    echo _('Something went wrong...');