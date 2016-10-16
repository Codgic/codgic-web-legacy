<?php
function update_cont_rank($cont_id){
    require __DIR__.'/../conf/database.php';
    $row=mysqli_fetch_row(mysqli_query($con,"select problems,num,start_time,end_time,judge_way from contest where contest_id=$cont_id"));
    $prob_arr=unserialize($row[0]);
    $cont_num=$row[1];
    $cont_start=$row[2];
    $cont_end=$row[3];
    $cont_judgeway=$row[4];
    $q=mysqli_query($con,"select user_id from contest_status where contest_id=$cont_id");
    while($row=mysqli_fetch_row($q)){
        $user_id=$row[0];
		$tot_scores=0;
		$tot_times=0;
        for($i=0;$i<$cont_num;$i++){
          if($cont_judgeway==3){
            //OI-like: Only recognize the first submit
            $s_row=mysqli_fetch_row(mysqli_query($con, "select score,result,in_date from solution where user_id='$user_id' and in_date>'".$cont_start."' and in_date<'".$cont_end."' and problem_id=".$prob_arr[$i].' order by in_date limit 1'));
            //Process score
            if(isset($s_row[0]))
                $score_arr["$prob_arr[$i]"]=$s_row[0];
            else
                $score_arr["$prob_arr[$i]"]=0;
            $tot_scores+=$score_arr["$prob_arr[$i]"];
            //Process result
            if(isset($s_row[1]))
                $res_arr["$prob_arr[$i]"]=$s_row[1];
            else
                $res_arr["$prob_arr[$i]"]=NULL;
            //Process time
            if(isset($s_row[2]))
                $time_arr["$prob_arr[$i]"]=$s_row[2];
            else
                $s_row[2]=0;
            $tot_times+=$time_arr["$prob_arr[$i]"];
          }else{
            $s_row=mysqli_fetch_row(mysqli_query($con,"select max(score),count(score),min(result),max(in_date) from solution where user_id='$user_id' and in_date>'".$cont_start."' and in_date<'".$cont_end."' and problem_id=".$prob_arr[$i]));
            //Process scores
            if(isset($s_row[0])){
                if($s_row[0]!=100&&$cont_judgeway==2) //ACM
                    $s_row[0]=0;
                $score_arr["$prob_arr[$i]"]=$s_row[0];
                if($cont_judgeway==1&&$s_row[1]!=0){ //CWOJ
                    $score_arr["$prob_arr[$i]"]-=5*($s_row[1]-1);
                    if($score_arr["$prob_arr[$i]"]<0)
                        $score_arr["$prob_arr[$i]"]=0;
                }
            }else
                $score_arr["$prob_arr[$i]"]=0;
            $tot_scores+=$score_arr["$prob_arr[$i]"];
            //Process results
            if(isset($s_row[2]))
                $res_arr["$prob_arr[$i]"]=$s_row[2];
            else
                $res_arr["$prob_arr[$i]"]=NULL;
            //Process times
            if(isset($s_row[3])){
                if($s_row[0]==100)
                    $time_arr["$prob_arr[$i]"]=strtotime($s_row[3])-strtotime($cont_start)+1200*($s_row[1]-1);
                else 
                    $time_arr["$prob_arr[$i]"]=1200*$s_row[1];
            }else
                $time_arr["$prob_arr[$i]"]=0;
            $tot_times+=$time_arr["$prob_arr[$i]"];
          }
        }
        $scores=serialize($score_arr);
        $results=serialize($res_arr);
        $times=serialize($time_arr);
        unset($score_arr);
        unset($res_arr);
        mysqli_query($con,"update contest_status set scores='$scores', results='$results', times='$times', tot_scores=$tot_scores, tot_times=$tot_times where contest_id=$cont_id and user_id='$user_id'");
    }
    $q=mysqli_query($con,"select user_id,tot_scores,tot_times from contest_status where contest_id=$cont_id order by tot_scores desc,tot_times");
    $pre_score=-1;
    $pre_time=-1;
    $pre_rank=-1;
    $cnt=0;
    while($row=mysqli_fetch_row($q)){
        $user_id=$row[0];
        $cnt++;
        if($pre_score==$row[1] && $pre_time==$row[2]) 
            $tmp=$pre_rank;
        else
            $tmp=$cnt;
        $pre_rank=$tmp;
        $pre_score=$row[1];
        $pre_time=$row[2];
        mysqli_query($con, "update contest_status set rank=$tmp where user_id='$user_id' and contest_id=$cont_id");
    }
    for($i=0;$i<$cont_num;$i++)
      mysqli_query($con, "update problem set rejudged='N' where problem_id=".$prob_arr[$i]);
    mysqli_query($con, "update contest set last_rank_time=NOW() where contest_id=$cont_id");
}

function get_judgeway_destext($judge_way){
    if($judge_way==0) return _('Final score is the sum of the highest score of each problem. Time Penalty records the latest submit time for solved problems and 1200s for unsolved problems.').'<br><code>final_score = max_score;</code><br><code>final_time = AC ? (last_submit_time + 1200s * (submit_times - 1)) : 1200s * submit_times; </code>';
    else if($judge_way==1) return _('Based on the highest score, each non-first submit will let you lose 5 points. Time Penalty records the latest submit time for solved problems and 1200s for unsolved problems.').'<br><code>final_score = max_score - 5 * (0.9, submit_times - 1);</code><br><code>final_time = AC ? (last_submit_time + 1200s * (submit_times - 1)) : 1200s * submit_times; </code>';
    else if($judge_way==2) return _('Final score is the sum of the scores of SOLVED problems. Time Penalty records the latest submit time for SOLVED problems and 1200s for unsolved problems.').'<br><code>final_score = (max_score == full_score) ? full_score : 0;</code><br><code>final_time = AC ? last_submit_time + 1200s * (submit_times - 1) : 1200s * submit_times; </code>';
    else if($judge_way==3) return _('Final score is the sum of the score of the FIRST submit of each problem. Time Penalty is the sum of the FIRST submit time of each problem.').'<br><code>final_score = first_submit_score;</code><br><code>final_time = first_submit_time; </code>';
}

function get_time_text($time){
    $hour=intval($time/3600);
    if($hour<10) $hour='0'.$hour;
    $min=intval(($time-3600*$hour)/60);
    if($min<10) $min='0'.$min;
    $sec=$time-3600*$hour-60*$min;
    if($sec<10) $sec='0'.$sec;
    $ret="$hour:$min:$sec";
    return $ret;
}