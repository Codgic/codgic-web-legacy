<?php
require 'inc/global.php';
require 'inc/functions.php';
if(!isset($_POST['op'])){
    echo _('Invalid Argument...');
    exit();
}
$op=$_POST['op'];
require 'inc/database.php';

if($op=='get_rank_table'){
    if(!isset($_POST['contest_id'])||empty($_POST['contest_id'])){
        echo _('Invalid Argument...');
        exit();
    }
    $cont_id=intval($_POST['contest_id']);
    $row=mysqli_fetch_row(mysqli_query($con, "select start_time,end_time,num,problems,last_rank_time from contest where contest_id=$cont_id"));
    if(!$row){
        echo _('No such contest...');
        exit();
    }
    $prob_arr=unserialize($row[3]);
    $cont_num=$row[2];
    $cont_starttime=strtotime($row[0]);
    if(time()>=$cont_starttime){
        if($row[4]==NULL) 
            //If last ranked time is undefined
            update_cont_rank($cont_id);
        else if(strtotime($row[1])>time()&&time()-strtotime($row[4])>20)
            //If contest hasn't ended
            //Won't rank if ranked less than 20 seconds ago.
            update_cont_rank($cont_id);
        else{
            //If contest has ended
            for($i=0;$i<$row[2];$i++){
                $s_row=mysqli_fetch_row(mysqli_query($con,'select rejudged from problem where problem_id='.$prob_arr[$i].' limit 1'));
                if(isset($s_row[1])&&$s_row[1]=='Y'){
                    update_cont_rank($cont_id);
                    break;
                }
            }
        }
    }
    $q=mysqli_query($con,"select user_id,scores,results,tot_scores,tot_times,rank from contest_status where contest_id=$cont_id order by rank,user_id");
    if(mysqli_num_rows($q)==0){
        echo _('Looks like nobody enrolled in this contest...');
        exit();
    }
?>
    <table class="table table-condensed">
      <thead>
        <tr>
          <th>No.</th>
          <th><?php echo _('User')?></th>
          <th><?php echo _('Score')?></th>
          <th><?php echo _('Time Penalty')?></th>
          <?php if(time()>=$cont_starttime)
          for($i=0;$i<$cont_num;$i++)
            echo "<th>$prob_arr[$i]</th>";?>
        </tr>
      </thead>
      <tbody>
        <?php
          while($row=mysqli_fetch_row($q)){
              $scr_arr=unserialize($row[1]);
              $res_arr=unserialize($row[2]);
              echo '<tr><td>',$row[5],'</td>';
              echo '<td>',$row[0],'</td>';
              echo '<td>',$row[3],'</td>';
              echo '<td>',get_time_text($row[4]),'</td>';
              if(time()>=$cont_starttime){
                for($i=0;$i<$cont_num;$i++){
                  echo '<td><i class=', is_null($res_arr["$prob_arr[$i]"]) ? '"fa fa-fw fa-question" style="color:grey"' : ($res_arr["$prob_arr[$i]"] ? '"fa fa-fw fa-remove" style="color:red"' : '"fa fa-fw fa-check" style="color:green"'), '></i> ';
                  echo $scr_arr[$prob_arr[$i]],'</td>';
                }
              }
              echo "</tr>\n";
          }
        ?>
      </tbody>
    </table>
<?php 
}else{
    if(!isset($_SESSION['user'])){
        echo _('Please login first...');
        exit();
    }
    $uid=$_SESSION['user'];
    if(!isset($_POST['contest_id'])){
        echo _('Invalid Argument...');
        exit();
    }
    $cont_id=intval($_POST['contest_id']);
    
    if($op=='enroll'){
        $row=mysqli_fetch_row(mysqli_query($con,"select end_time,problems,num,enroll_user from contest where contest_id=$cont_id"));
        if(!$row){
            echo _('No such contest...');
            exit();
        }
        if(strtotime($row[0])<=time()){
            echo _('Contest has ended...');
            exit();
        }
        //Check if already enrolled.
        if(mysqli_num_rows(mysqli_query($con,"select 1 from contest_status where user_id='$uid' and contest_id=$cont_id limit 1"))){
            echo 'success';
            exit();
        }

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
                echo _('Something went wrong...');
        }else
            echo _('Something went wrong...');
    }else if($op=='leave'){
        $row=mysqli_fetch_row(mysqli_query($con,"select end_time,enroll_user from contest where contest_id=$cont_id"));
        if(!$row){
            echo _('No such contest...');
            exit();
        }
        if(strtotime($row[0])<=time()){
            echo _('Contest has ended...');
            exit();
        }
        if(mysqli_query($con,"DELETE from contest_status where user_id='$uid' and contest_id=$cont_id"))
            echo 'success';
        else
            echo _('Something went wrong...');
    }else
        echo _('Invalid Argument...');
}