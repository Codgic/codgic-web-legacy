<?php
require __DIR__.'/../conf/ojsettings.php';
require __DIR__.'/../inc/init.php';

if(!isset($_POST['op'])||!isset($_POST['id'])){
	echo _('Invalid Argument...');
    exit();
}else
    $op=$_POST['op'];

if(!isset($_SESSION['user'])){
	echo _('Please login first...');
    exit();
}

require __DIR__.'/../conf/database.php';
$uid=($_SESSION['user']);

if($op=='osc'){
    if($_POST['id']=='all'){
        if(mysqli_query($con,"update solution set public_code=1 where user_id='$uid'"))
            echo 'success';
        else
            echo _('Something went wrong...');
    }else{
        $id=intval($_POST['id']);
        mysqli_query($con,"update solution set public_code=(!public_code) where solution_id=$id and user_id='$uid'");
        if(mysqli_affected_rows($con)==1)
            echo 'success';
        else
            echo _('Something went wrong...');
    }
}else if($op=='mark_mal'){
    require __DIR__.'/../func/privilege.php';
    if(!check_priv(PRIV_PROBLEM)&&!check_priv(PRIV_SYSTEM)){
        echo _('Permission Denied...');
        exit();
    }
    $id=intval($_POST['id']);
    mysqli_query($con,"update solution set malicious=(!malicious) where solution_id=$id");
    if(mysqli_affected_rows($con)==1)
        echo 'success';
    else
        echo _('Something went wrong...');
}