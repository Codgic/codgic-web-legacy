<?php
session_start();
if(!isset($_SESSION['user']))
  die('你还没有登录...');
$uid=$_SESSION['user'];
if(!isset($_POST['op']))
  die('参数无效...');
$op=$_POST['op'];
require 'inc/database.php';
if($op=='enroll'){
  if(!isset($_POST['contest_id']))
    die('参数无效...');
  $cont_id=intval($_POST['contest_id']);
  $row=mysqli_fetch_row(mysqli_query($con,"select end_time,problems,num,enroll_user from contest where contest_id=$cont_id"));
  if(strtotime($row[0])<=time())
    die('比赛已经结束...');
  if(mysqli_num_rows(mysqli_query($con,"select 1 from contest_status where user_id='$uid' and contest_id=$cont_id limit 1")))
    die('success');
  $prob_arr=unserialize($row[1]);
  $new_arr=array();
  for($i=0;$i<$row[2];$i++)
    $new_arr["$prob_arr[$i]"]=0;
  $problems=serialize($new_arr);
  if(mysqli_query($con,"insert into contest_status (user_id,contest_id,scores) VALUES ('$uid',$cont_id,'$problems')")){
    if(mysqli_query($con,'update contest set enroll_user='.($row[3]+1)." where contest_id=$cont_id"))
      echo 'success';
    else
      echo '系统错误...';
  }else
    echo '系统错误...';
}else if($op=='get_rank'){
 die ('Coming Not Soon...');
}
?>