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

require __DIR__.'/../../src/database.php';
$success=false;
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
        echo json_encode(array('success' => false, 'message' => _('Please enter start time...')));
        exit();
    }
    if($start_time<0){
        echo json_encode(array('success' => false, 'message' => _('Invalid start time...')));
        exit();
    }
    if(isset($_POST['end_time'])&&!empty($_POST['end_time'])){
        $end_time=mysqli_real_escape_string($con,$_POST['end_time']);
    }else{
        echo json_encode(array('success' => false, 'message' => _('Please enter end time...')));
        exit();
    }
    if($end_time<0){
        echo json_encode(array('success' => false, 'message' => _('Invalid end time...')));
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
        echo json_encode(array('success' => false, 'message' => _('Please enter title...')));
        exit();
    }
    if(isset($_POST['problems'])&&!empty($_POST['problems'])){
        $problems=mysqli_real_escape_string($con,$_POST['problems']);
    }else{
        echo json_encode(array('success' => false, 'message' => _('Please specify problems...')));
        exit();
    }
    
    $prob_arr=array_unique(explode(',',$problems));
    $des=isset($_POST['description']) ? mysqli_real_escape_string($con,$_POST['description']) : '';
    $source=isset($_POST['source']) ? mysqli_real_escape_string($con,$_POST['source']) : '';
    if(isset($_POST['owners'])&&!empty($_POST['owners'])){
        $owners=mysqli_real_escape_string($con,$_POST['owners']);
        $owners_arr=explode(',',$owners);
        $owners="'".implode("','",$owners_arr)."'";
    }else
        $owners="''";
    $has_tex=0;
    if(isset($_POST['option_level'])){
        $l=intval($_POST['option_level']);
        $level_max=(PROB_LEVEL_MASK>>PROB_LEVEL_SHIFT);
        if($l>=0 && $l<=$level_max){
            $has_tex|=($l<<PROB_LEVEL_SHIFT);
        }
    }

    //Verify problems availability.
    for($i=0;$i<sizeof($prob_arr);$i++){
        $r=mysqli_fetch_row(mysqli_query($con,'select has_tex from problem where problem_id='.$prob_arr[$i].' limit 1'));
        if(!$r){
            echo json_encode(array('success' => false, 'message' => _('Problem ').'#'.$prob_arr[$i]._(' does not exist...')));
            exit();
        }
        if($r[0] & PROB_IS_HIDE && !isset($_POST['hide_cont'])){
            echo json_encode(array('success' => false, 'message' => _('Problem ').'#'.$prob_arr[$i]._('is hidden. Please hide the contest to add it...')));
            exit();
        }
    }

    //Verify owners availability.
    if(isset($owners_arr)){
        for($i=0;$i<sizeof($owners_arr);$i++){
            $r=mysqli_num_rows(mysqli_query($con, 'select 1 from users where user_id=\''.$owners_arr[$i].'\' limit 1'));
            if($r<=0){
                echo json_encode(array('success' => false, 'message' => _('User ').'"'.$owners_arr[$i].'"'.' does not exist...'));
                exit();
            }
        }
    }

    if(isset($_POST['hide_cont'])){
        $has_tex|=PROB_IS_HIDE;
    }

    $hide_source = isset($_POST['hide_source']) ? 1 : 0;

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
        //LEGACY CODE STILL NOT DELETED!
        if(!mysqli_query($con,"update contest set title='$title',start_time='$start_time',end_time='$end_time',description='$des',source='$source',has_tex=$has_tex,judge_way=$judge_way,hide_source_code=$hide_source where contest_id=$id")){
            echo json_encode(array('success' => false, 'message' => _('Database operation failed...')));
            exit();
        }else{
            //Clean up previous problems.
            if(!mysqli_query($con, "delete from contest_problem where contest_id=$id and problem_id not in($problems)")){
                echo json_encode(array('success' => false, 'message' => _('Database operation failed...')));
                exit();
            }
            if(!mysqli_query($con, "delete from contest_detail where contest_id=$id and problem_id not in($problems)")){
                echo json_encode(array('success' => false, 'message' => _('Database operation failed...')));
                exit();
            }            
            //Clean up previous owners.
            if(!mysqli_query($con, "delete from contest_owner where contest_id=$id and user_id not in($owners)")){
                echo json_encode(array('success' => false, 'message' => _('Database operation failed...')));
                exit();
            }
            //Insert problems.
            for($i=0;$i<sizeof($prob_arr);$i++){
                if(!mysqli_query($con, "INSERT into contest_problem (contest_id,problem_id,place) VALUES ($id,".$prob_arr[$i].",$i) ON DUPLICATE KEY update place=$i")){
                    echo json_encode(array('success' => false, 'message' => _('Database operation failed...')));
                    exit();
                }
            }
            //Insert owners.
            if(isset($owners_arr)){
                for($i=0;$i<sizeof($owners_arr);$i++){
                    if(!mysqli_query($con, "INSERT IGNORE into contest_owner (contest_id,user_id) VALUES ($id,'".$owners_arr[$i]."')")){
                        echo json_encode(array('success' => false, 'message' => _('Database operation failed...')));
                        exit();
                    }
                }
            }
            require __DIR__.'/../func/contest.php';
            update_cont_scr($id);
            $success=true;
        }
    }else if($_POST['op']=='add'){
        $id=1000;
        $result=mysqli_query($con,'select max(contest_id) from contest');
        if(($row=mysqli_fetch_row($result)) && intval($row[0]))
            $id=intval($row[0])+1;
        if(!mysqli_query($con,"insert into contest (contest_id,title,start_time,end_time,description,source,in_date,has_tex,judge_way,hide_source_code,enroll_user) values ($id,'$title','$start_time','$end_time','$des','$source',NOW(),$has_tex,$judge_way,$hide_source,0)")){
            echo json_encode(array('success' => false, 'message' => _('Database operation failed...')));
            exit();
        }else{
            //Insert problems.
            for($i=0;$i<sizeof($prob_arr);$i++){
                if(!mysqli_query($con, "insert into contest_problem (contest_id,problem_id,place) VALUES ($id,".$prob_arr[$i].",$i) ON DUPLICATE KEY update contest_id=$id,problem_id=".$prob_arr[$i].",place=$i")){
                    echo json_encode(array('success' => false, 'message' => _('Database operation failed...')));
                    exit();
                }
            }
            //Insert owners.
            if(isset($owners_arr)){
                for($i=0;$i<sizeof($owners_arr);$i++){
                    if(!mysqli_query($con, "INSERT IGNORE into contest_owner (contest_id,user_id) VALUES ($id,'".$owners_arr[$i]."')")){
                        echo json_encode(array('success' => false, 'message' => _('Database operation failed...')));
                        exit();
                    }
                }
            }
            $success=true;
        }
    }else{
        echo json_encode(array('success' => false, 'message' => _('Invalid Argument...')));
        exit();
    }

    if($success)
        echo json_encode(array('success' => true, 'contestID' => $id));
    else
        echo json_encode(array('success' => false, 'message' => _('Unknown Error...')));
}
