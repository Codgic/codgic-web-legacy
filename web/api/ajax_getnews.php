<?php
require __DIR__.'/../conf/ojsettings.php';
require __DIR__.'/../inc/init.php';
require __DIR__.'/../func/privilege.php';
require __DIR__.'/../conf/database.php';
header('Content-Type: application/json');

$arr=array('success'=>false,'title'=>'','content'=>'','time'=>'','priv'=>'');

if(!isset($_POST['newsid'])){
    $arr['content']=_('Invalid Argument...');
	die(json_encode($arr));
}

$newsid = intval($_POST['newsid']);

$res=mysqli_query($con,"select title,content,time,privilege from news where news_id=$newsid");
$row=mysqli_fetch_row($res);

if(($require_auth==1 || $row[3]!=0) && !isset($_SESSION['user'])){
    $arr['content']=_('Permission Denied...');
    die(json_encode($arr));
}

if($row[3]!=0){
    if(!($_SESSION['priv'] & $row[3])){
        $arr['content']=_('Permission Denied...');
        die(json_encode($arr));
    }
}

if(empty($row[1])) 
    $row[1]=_('This piece of news is empty...');
$arr['success']=true;
$arr['title']=$row[0];
$arr['content']=$row[1];
$arr['time']=$row[2];
$arr['priv']=list_priv($row[3]);

echo json_encode($arr);