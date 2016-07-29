<?php
require 'inc/database.php';
require 'inc/ojsettings.php';
require 'inc/privilege.php';
session_start();
$arr=array('type'=>'fail','title'=>'','content'=>'','time'=>'','priv'=>'');

if(!isset($_POST['newsid'])){
    $arr['content']='参数无效...';
	die(json_encode($arr));
}

$newsid = intval($_POST['newsid']);

$res=mysqli_query($con,"select title,content,time,privilege from news where news_id=$newsid");
$row=mysqli_fetch_row($res);

if(($require_auth==1 || $row[3]!=0) && !isset($_SESSION['user'])){
    $arr['content']='你没有权限...';
    die(json_encode($arr));
}

if($row[3]!=0){
    if(!($_SESSION['priv'] & $row[3])){
        $arr['content']='你没有权限...';
        die(json_encode($arr));
    }
}

if(empty($row[1])) $row[1]='本条新闻内容为空...';
$arr['type']='success';
$arr['title']=$row[0];
$arr['content']=$row[1];
$arr['time']=$row[2];
$arr['priv']=list_priv($row[3]);

echo json_encode($arr);
?>