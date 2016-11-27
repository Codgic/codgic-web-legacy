<?php
//Fetch contest problems.
function get_cont_probs($cont_id){
    require __DIR__.'/../conf/database.php';

    $res=mysqli_query($con, "select problem_id from contest_problem where contest_id=$cont_id order by place");
    $i=0;
    while($row=mysqli_fetch_row($res)){
        $prob_arr[$i]=$row[0];
        $i++;
    }
    return $prob_arr;
}

//Update contest rank.
function update_cont_rank($cont_id){
    require __DIR__.'/../conf/database.php';

    $res=mysqli_query($con,"select user_id,tot_score,tot_time from contest_status where contest_id=$cont_id order by tot_score desc,tot_time");
    $pre_score=-1;
    $pre_time=-1;
    $pre_rank=-1;
    $cnt=0;
    while($row=mysqli_fetch_row($res)){
        $user_id=$row[0];
        $cnt++;
        if($pre_score==$row[1] && $pre_time==$row[2]) 
            $tmp=$pre_rank;
        else
            $tmp=$cnt;
        $pre_rank=$tmp;
        $pre_score=$row[1];
        $pre_time=$row[2];
        if(!mysqli_query($con, "update contest_status set rank=$tmp where user_id='$user_id' and contest_id=$cont_id")){
            //Log error_log.
        }
    }
}

//Update contest score.
function update_cont_scr($cont_id){
    require __DIR__.'/../conf/database.php';

    $row=mysqli_fetch_row(mysqli_query($con, "select start_time,end_time,judge_way from contest where contest_id=$cont_id limit 1"));
    $cont_start=$row[0];
    $cont_end=$row[1];
    $cont_judgeway=$row[2];

    //Obtain problems.
    $prob_arr=get_cont_probs($cont_id);
    
    //Update scores for each user.
    $res=mysqli_query($con,"select user_id,enroll_time,leave_time from contest_status where contest_id=$cont_id");
    while($row=mysqli_fetch_row($res)){
        $user_id=$row[0];
        $tot_score=0;
        $tot_time=0;
        //Obtain time range.
        $range_start=$cont_start;
        $range_end=$cont_end;
        if($cont_start<strtotime($row[1]))
            $range_start=$row[1];
        if($row[2]&&$cont_end>strtotime($row[2]))
            $range_end=$row[2];

        //As for every problem.
        for($i=0;$i<sizeof($prob_arr);$i++){
            $score=0;
            $time=0;
            $result=NULL;
            if($cont_judgeway==3){ //OI-Like: Only recognize the first submit.
                $s_row=mysqli_fetch_row(mysqli_query($con, "select score,result,in_date from solution where user_id='$user_id' and in_date>'".$range_start."' and in_date<'".$range_end."' and problem_id=".$prob_arr[$i].' order by in_date limit 1'));
                //Process score.
                if($s_row[0])
                    $score=$s_row[0];
                $tot_score+=$score;
                //Process result.
                if($s_row[1])
                    $result=$s_row[1];
                //Process time.
                if(isset($s_row[2]))
                    $time=strtotime($s_row[2])-strtotime($range_start);
                $tot_time+=$time;
            }else{  //Others: Select the highest score among eligible submits.
                $s_row=mysqli_fetch_row(mysqli_query($con,"select MAX(score),COUNT(score),MIN(result),MAX(in_date) from solution where user_id='$user_id' and in_date>'".$range_start."' and in_date<'".$range_end."' and problem_id=".$prob_arr[$i]));
                //Process score.
                if($s_row[0]){
                    if($cont_judgeway==2 && $s_row[0]==100) //ACM: if score != 100 then score = 0.
                        $score=100;
                    else if($cont_judgeway==1 && $s_row[1]!=0){ //CWOJ: Minus 5 points per non-AC submit.
                        $score=$s_row[0]-5*($s_row[1]-1);
                        if($score<0)
                            $score=0;
                    }else if($cont_judgeway==0) //Training: MAX(score).
                        $score=$s_row[0];
                }
                $tot_score+=$score;
                //Process result.
                if($s_row[2])
                    $result=$s_row[2];
                //Process time.
                if($s_row[3]){
                    if($s_row[0]==100)
                        $time=strtotime($s_row[3])-strtotime($range_start)+1200*($s_row[1]-1);
                    else 
                        $time=1200*$s_row[1];
                }
                $tot_time+=$time;
            }
            //Write into database.
            mysqli_query($con, "INSERT into contest_detail (user_id,contest_id,problem_id,result,score,time) VALUES ('$user_id',$cont_id,".$prob_arr[$i].",$result,$score,$time) ON DUPLICATE KEY UPDATE result=$result,score=$score,time=$time");
        }
    }
    //Update user rank.
    update_cont_rank($cont_id);
    //Update last_upd_time.
    mysqli_query($con, "update contest set last_upd_time=NOW() where contest_id=$cont_id");
}

//Return description text of each type of contest.
function get_judgeway_destext($judge_way){
    if($judge_way==0)
        return _('Final score is the sum of the highest score of each problem. Time Penalty records the latest submit time for solved problems and 1200s for unsolved problems.').'<br><code>final_score = max_score;</code><br><code>final_time = AC ? (last_submit_time + 1200s * (submit_times - 1)) : 1200s * submit_times; </code>';
    else if($judge_way==1)
        return _('Based on the highest score, each non-first submit will let you lose 5 points. Time Penalty records the latest submit time for solved problems and 1200s for unsolved problems.').'<br><code>final_score = max_score - 5 * (0.9, submit_times - 1);</code><br><code>final_time = AC ? (last_submit_time + 1200s * (submit_times - 1)) : 1200s * submit_times; </code>';
    else if($judge_way==2)
        return _('Final score is the sum of the scores of SOLVED problems. Time Penalty records the latest submit time for SOLVED problems and 1200s for unsolved problems.').'<br><code>final_score = (max_score == full_score) ? full_score : 0;</code><br><code>final_time = AC ? last_submit_time + 1200s * (submit_times - 1) : 1200s * submit_times; </code>';
    else if($judge_way==3)
        return _('Final score is the sum of the score of the FIRST submit of each problem. Time Penalty is the sum of the FIRST submit time of each problem.').'<br><code>final_score = first_submit_score;</code><br><code>final_time = first_submit_time; </code>';
}

//Generate time countdown text.
function get_time_text($time){
    $hour=intval($time/3600);
    if($hour<10)
        $hour='0'.$hour;
    $min=intval(($time-3600*$hour)/60);
    if($min<10)
        $min='0'.$min;
    $sec=$time-3600*$hour-60*$min;
    if($sec<10)
        $sec='0'.$sec;
    $ret="$hour:$min:$sec";
    return $ret;
}