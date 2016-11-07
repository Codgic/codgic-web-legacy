<?php
require __DIR__.'/../inc/init.php';
require __DIR__.'/../func/privilege.php';
require __DIR__.'/../lib/problem_flags.php';
header('Content-Type: application/json');

function JUDGE_TYPE($way){
    if($way=='train')
      return 0;
    else if($way=='cwoj')
      return 1;
    else if($way=='acm-like')
      return 2;
    else if($way=='oi-like')
      return 3;
}
if(!check_priv(PRIV_PROBLEM)){
    echo json_encode(array('success' => false, 'message' => _('Permission Denied...')));
    exit();
}else if(!isset($_POST['op'])){
    echo json_encode(array('success' => false, 'message' => _('Invalid Argument...')));
    exit();
}

require __DIR__.'/../conf/database.php';

if($_POST['op']=='del'){
    if(!isset($_POST['contest_id'])){
        echo json_encode(array('success' => false, 'message' => _('No such contest...')));
        exit();
    }
    $id=intval($_POST['contest_id']);
    if(mysqli_query($con,"update contest set defunct=(!defunct) where contest_id=$id"))
        echo json_encode(array('success' => true));
    else
        echo json_encode(array('success' => false, 'message' => _('Database operation failed...')));
}else{
    if(isset($_POST['start_time'])&&!empty($_POST['start_time'])){
         $start_time = mysqli_real_escape_string($con,$_POST['start_time']);
    }else{
        echo json_encode(array('success' => false, 'message' => _('Please enter Start Time...')));
        exit();
    }
    if($start_time<0){
        echo json_encode(array('success' => false, 'message' => _('Invalid Start Time...')));
        exit();
    }
    if(isset($_POST['end_time'])&&!empty($_POST['end_time'])){
        $end_time=mysqli_real_escape_string($con,$_POST['end_time']);
    }else{
        echo json_encode(array('success' => false, 'message' => _('Please enter End Time...')));
        exit();
    }
    if($end_time<0){
        echo json_encode(array('success' => false, 'message' => _('Invalid End Time...')));
        exit();
    }
    if(strtotime($start_time)>strtotime($end_time)){
        echo json_encode(array('success' => false, 'message' => _('Start time can\'t be greater than end time...')));
        exit();
    }
    if(isset($_POST['judge'])){
        $judge_way=JUDGE_TYPE($_POST['judge']);
    }else{
        $judge_way=0;
    }
    if(isset($_POST['title'])&&!empty($_POST['title'])){
        $title=mysqli_real_escape_string($con,$_POST['title']);
    }else{
        echo json_encode(array('success' => false, 'message' => _('Please enter Title...')));
        exit();
    }
    if(isset($_POST['problems'])&&!empty($_POST['problems'])){
        $problems=mysqli_real_escape_string($con,$_POST['problems']);
    }else{
        echo json_encode(array('success' => false, 'message' => _('Please specify problems...')));
        exit();
    }
    $num=substr_count($problems,',')+1;
    $prob_arr=explode(',',$problems);
    $problems=serialize($prob_arr);
    $des=isset($_POST['description']) ? mysqli_real_escape_string($con,$_POST['description']) : '';
    $source=isset($_POST['source']) ? mysqli_real_escape_string($con,$_POST['source']) : '';
    
    $has_tex=0;
    if(isset($_POST['option_level'])){
        $l=intval($_POST['option_level']);
        $level_max=(PROB_LEVEL_MASK>>PROB_LEVEL_SHIFT);
        if($l>=0 && $l<=$level_max){
            $has_tex|=($l<<PROB_LEVEL_SHIFT);
        }
    }

    for($i=0;$i<$num;$i++){
        $r=mysqli_fetch_row(mysqli_query($con,'select has_tex from problem where problem_id='.$prob_arr[$i].' limit 1'));
        if(!$r){
            echo json_encode(array('success' => false, 'message' => _('Problem #').$prob_arr[$i]._('does not exist...')));
            exit();
        }
        if($r[0] & PROB_IS_HIDE && !isset($_POST['hide_cont'])){
            echo json_encode(array('success' => false, 'message' => _('Problem #').$prob_arr[$i]._('is hidden. Please hide the contest to add it...')));
            exit();
        }
    } 

    if(isset($_POST['hide_cont'])){
        $has_tex|=PROB_IS_HIDE;
    }
    foreach ($_POST as $value) {
        if(strstr($value,'[tex]') || strstr($value,'[inline]')) {
            $has_tex|=PROB_HAS_TEX;
            break;
        }
    }

    if($_POST['op']=='edit'){
        if(!isset($_POST['contest_id'])){
            echo json_encode(array('success' => false, 'message' => _('Invalid Argument...')));
            exit();
        }
        $id=intval($_POST['contest_id']);
        $result=mysqli_query($con,"update contest set title='$title',start_time='$start_time',end_time='$end_time',problems='$problems',num='$num',description='$des',source='$source',has_tex=$has_tex,judge_way=$judge_way where contest_id=$id");
        if($result){
            require __DIR__.'/../func/contest.php';
            update_cont_rank($id);
        }
    }else if($_POST['op']=='add'){
        $id=1000;
        $result=mysqli_query($con,'select max(contest_id) from contest');
        if(($row=mysqli_fetch_row($result)) && intval($row[0]))
            $id=intval($row[0])+1;
        $result=mysqli_query($con,"insert into contest (contest_id,title,start_time,end_time,description,problems,num,source,in_date,has_tex,judge_way,enroll_user) values ($id,'$title','$start_time','$end_time','$des','$problems','$num','$source',NOW(),$has_tex,$judge_way,0)");
    }else{
        echo json_encode(array('success' => false, 'message' => _('Invalid Argument...')));
        exit();
    }

    if($result)
        echo json_encode(array('success' => true, 'contestID' => $id));
    else
        echo json_encode(array('success' => false, 'message' => _('Unknown Error...')));
}
