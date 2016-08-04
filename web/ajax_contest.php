<?php
require 'inc/functions.php';
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
  if(!$row)
    die('比赛不存在...');
  if(strtotime($row[0])<=time())
    die('比赛已经结束...');
  if(mysqli_num_rows(mysqli_query($con,"select 1 from contest_status where user_id='$uid' and contest_id=$cont_id limit 1")))
    die('success');
  $prob_arr=unserialize($row[1]);
  $newp_arr=array();
  for($i=0;$i<$row[2];$i++){
    $newp_arr["$prob_arr[$i]"]=0;
	$newr_arr["$prob_arr[$i]"]=NULL;
  }
  $problems=serialize($newp_arr);
  $results=serialize($newr_arr);
  if(mysqli_query($con,"insert into contest_status (user_id,contest_id,scores,results,times) VALUES ('$uid',$cont_id,'$problems','$results','$problems')")){
    if(mysqli_query($con,'update contest set enroll_user='.($row[3]+1)." where contest_id=$cont_id"))
      echo 'success';
    else
      echo '系统错误...';
  }else
    echo '系统错误...';
}else if($op=='get_rank_table'){
    if(!isset($_POST['contest_id'])||empty($_POST['contest_id']))
      die('参数无效...');
    $cont_id=intval($_POST['contest_id']);
    $row=mysqli_fetch_row(mysqli_query($con, "select end_time,num,problems,ranked from contest where contest_id=$cont_id"));
    if(!$row)
      die('比赛不存在...');
    if(strtotime($row[0]>time()))
      die('请在比赛结束后查看排名...');
    $prob_arr=unserialize($row[2]);
    $cont_num=$row[1];
    if($row[3]=='N') update_cont_rank($cont_id);
    else{
      for($i=0;$i<$row[1];$i++){
        $s_row=mysqli_fetch_row(mysqli_query($con,'select rejudged from problem where problem_id='.$prob_arr[$i].' limit 1'));
        if($s_row[0]=='Y'){
            update_cont_rank($cont_id);
            break;
        }
      }
    }
    $q=mysqli_query($con,"select user_id,scores,results,tot_scores,tot_times,rank from contest_status where contest_id=$cont_id order by rank");
    if(mysqli_num_rows($q)==0) die('看起来没有人参加过这场比赛...');
?>
    <table class="table table-condensed">
      <thead>
        <tr>
          <th>No.</th>
          <th>用户</th>
          <th>总分</th>
          <th>总时</th>
          <?php for($i=0;$i<$cont_num;$i++)
            echo "<th>$prob_arr[$i]</th>";?>
        </tr>
      </thead>
      <tbody>
        <?php
          while($row=mysqli_fetch_row($q)){
              $scr_arr=unserialize($row[1]);
              $res_arr=unserialize($row[2]);
              echo '<tr><td>',$row[5],'</td>';
              $pre_scores=$row[3];
              $pre_times=$row[4];
              echo '<td>',$row[0],'</td>';
              echo '<td>',$row[3],'</td>';
              echo '<td>',$row[4],'</td>';
              for($i=0;$i<$cont_num;$i++){
                  echo '<td><i class=', is_null($res_arr["$prob_arr[$i]"]) ? '"fa fa-fw fa-question" style="color:grey"' : ($res_arr["$prob_arr[$i]"] ? '"fa fa-fw fa-remove" style="color:red"' : '"fa fa-fw fa-check" style="color:green"'), '></i> ';
                  echo $scr_arr[$prob_arr[$i]],'</td>';
              }
              echo '</tr>';
          }
        ?>
      </tbody>
    </table>
<?php }?>