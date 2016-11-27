<?php
require __DIR__.'/../inc/init.php';
require __DIR__.'/../func/contest.php';
//header('Content-Type: application/json');

if(!isset($_POST['op'])){
    echo _('Invalid Argument...');
    exit();
}
$op=$_POST['op'];

if(!isset($_POST['contest_id'])||empty($_POST['contest_id'])){
    echo _('Invalid Argument...');
    exit();
}
$cont_id=intval($_POST['contest_id']);

require __DIR__.'/../conf/database.php';

if($op=='get_rank_table'){
    $cont_id=intval($_POST['contest_id']);
    //Obtain basic info.
    $row=mysqli_fetch_row(mysqli_query($con, "select start_time,end_time,last_upd_time,need_update from contest where contest_id=$cont_id"));
    if(!$row){
        echo _('No such contest...');
        exit();
    }
    $cont_starttime=strtotime($row[0]);
    $cont_endtime=strtotime($row[1]);
    $cont_lastrank=strtotime($row[2]);
    $cont_needupdate=$row[3];

    $enrolled=false;
    //Determine if an update is needed.
    if(time()>=$cont_starttime){
        if(is_null($cont_lastrank) || $row[3] || ($cont_endtime>time() && time()-$cont_lastrank>20))
            update_cont_scr($cont_id);
        if(time()<=$cont_endtime){
            //Determine whether show the problem list or not.
            if(isset($_SESSION['user'])){
                $uid=$_SESSION['user'];
                //Check if user has joined contest.
                if(mysqli_num_rows(mysqli_query($con,"select 1 from contest_status where user_id='$uid' and contest_id=$cont_id limit 1")))
                    $enrolled=true;
                //Check if user is contest owner.
                if(!$enrolled)
                    if(mysqli_num_rows(mysqli_query($con, "select 1 from contest_owner where user_id='$uid' and contest_id=$cont_id"))>0)
                        $enrolled=true;
            }
        }
    }
    $q=mysqli_query($con, "select user_id,tot_score,tot_time,rank from contest_status where contest_id=$cont_id order by rank,user_id");
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
                <?php
                    if(time()>=$cont_endtime||(time()>=$cont_starttime&&$enrolled)){  
                        $r=mysqli_query($con, "select problem_id from contest_problem where contest_id=$cont_id order by place");
                        while($row=mysqli_fetch_row($r))
                            echo "<th>$row[0]</th>";
                    }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
                while($row=mysqli_fetch_row($q)){
                    echo '<tr>';
                    //Rank
                    if(time()>=$cont_starttime)
                        echo '<td>',$row[3],'</td>';
                    else
                        echo '<td>-</td>';
                    //User
                    echo "<td>$row[0]</td>";
                    //Total Score
                    echo "<td>$row[1]</td>";
                    //Total Time
                    echo "<td>$row[2]</td>";
                    //Problems
                    if(time()>=$cont_endtime||(time()>=$cont_starttime&&$enrolled)){
                        $r=mysqli_query($con, "SELECT contest_detail.problem_id,score,result,contest_problem.place from contest_detail
                                               LEFT JOIN (select problem_id,place from contest_problem where contest_id=$cont_id) as contest_problem on (contest_problem.problem_id=contest_detail.problem_id)
                                               where contest_id=$cont_id and user_id='$row[0]'");
                        while($t_row=mysqli_fetch_row($r)){
                            echo '<td><i class=', is_null($t_row[2]) ? '"fa fa-fw fa-question" style="color:grey"' : ($t_row[2] ? '"fa fa-fw fa-remove" style="color:red"' : '"fa fa-fw fa-check" style="color:green"'), '></i> ';
                            echo $t_row[1],'</td>';
                        }
                    }
                    echo "<tr>\n";
                }
            ?>
        </tbody>
    </table>
<?php 
}else{
    header('Content-Type: application/json');
    if(!isset($_SESSION['user'])){
        echo json_encode(array('success' => false, 'message' => _('Please login first...')));
        exit();
    }
    $uid=$_SESSION['user'];
    
    if($op=='enroll'){
        $row=mysqli_fetch_row(mysqli_query($con,"select start_time,end_time,num,enroll_user from contest where contest_id=$cont_id"));
        if(!$row){
            echo json_encode(array('success' => false, 'message' => _('No such contest...')));
            exit();
        }
        if(strtotime($row[1])<=time()){
            echo json_encode(array('success' => false, 'message' => _('Contest has ended...')));
            exit();
        }
        //Check if current user is contest owner.
        if(mysqli_num_rows(mysqli_query($con, "select 1 from contest_owner where contest_id=$cont_id and user_id='$uid' limit 1"))>0){
            echo json_encode(array('success' => false, 'message' => _('Contest owner can\'t enroll in his contest...')));
            exit();
        }
        //Check if already enrolled.
        if(mysqli_num_rows(mysqli_query($con,"select 1 from contest_status where user_id='$uid' and contest_id=$cont_id limit 1"))){
            echo json_encode(array('success' => true));
            exit();
        }
        //Insert contest_status.
        if(!mysqli_query($con, "insert into contest_status (contest_id,user_id,tot_score,tot_time,rank,enroll_time,leave_time) VALUES ($cont_id,'$uid',0,0,0,NOW(),NULL)")){
            //ERROR 01
            die('01');
        }
        //Obtain problems
        $prob_arr=get_cont_probs($cont_id);
        //Insert contest_detail
        for($i=0;$i<sizeof($prob_arr);$i++){
            if(!mysqli_query($con, "insert into contest_detail (user_id,contest_id,problem_id,result,score,time) VALUES ('$uid',$cont_id,$prob_arr[$i],NULL,0,0)")){
                //ERROR 02
                die('02');
            }     
        }
        //Update contest rank.
        update_cont_rank($cont_id);
        echo 'success';
    }else if($op=='leave'){
        $row=mysqli_fetch_row(mysqli_query($con,"select end_time from contest where contest_id=$cont_id"));
        if(!$row){
            echo json_encode(array('success' => false, 'message' => _('No such contest...')));
            exit();
        }
        if(strtotime($row[0])<=time()){
            echo json_encode(array('success' => false, 'message' => _('Contest has ended...')));
            exit();
        }
        if(mysqli_num_rows(mysqli_query($con, "select 1 from contest_status where contest_id=$cont_id and leave_time is not null and user_id='$uid'"))>0){
            echo json_encode(array('success' => false, 'message' => _('You have left the contest...')));
            exit();
        }
        if(!mysqli_query($con, "update contest_status set leave_time=NOW() where contest_id=$cont_id and user_id='$uid'")){
            //ERROR 03
            die("update contest_status set leave_time=NOW() where contest_id=$cont_id and user_id='$uid'");
        }
        echo json_encode(array('success' => true));
    }else
        echo json_encode(array('success' => false, 'message' => _('Invalid Argument...')));
}