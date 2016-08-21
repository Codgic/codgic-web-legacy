<?php
require 'inc/global.php';
require 'inc/privilege.php';
function CMP_TYPE($way, $precision)
{
	if($way=='tra')
		return 0;
	else if($way=='float')
		return (1 << 16)+ ($precision & 65535);
	else if($way=='int')
		return 2 << 16;
	else if($way=='spj')
		return 3 << 16;
	return 0;
}
if(!check_priv(PRIV_PROBLEM)){
	echo _('Permission Denied...');
    exit();
}
else if(!isset($_POST['op'])){
	echo _('Invalid Argument...');
    exit();
}
require 'inc/database.php';

if($_POST['op']=='del'){
    if(!isset($_POST['problem_id'])){
        echo _('No such problem...');
        exit();
    }
    $id=intval($_POST['problem_id']);
    $result=mysqli_query($con,"select defunct from problem where problem_id=$id");
    if($row=mysqli_fetch_row($result)){
	if($row[0]=='N') $opr='Y';
    else $opr='N';
    if(mysqli_query($con,"update problem set defunct='$opr' where problem_id=$id"))
        echo 'success';
    else
        echo _('Something went wrong...');
    }
}else{
    if(isset($_POST['time'])&&!empty($_POST['time'])){
        $time=intval($_POST['time']);
    }else{
        die('Please enter Time Limit...');
        exit();
    }
    if($time<0){
        echo _('Invalid Time Limit...');
        exit();
    }
    if(isset($_POST['memory'])&&!empty($_POST['memory'])){ 
        $memory=intval($_POST['memory']);
    }else{
        echo _('Please enter Memory Limit...');
        exit();
    }
    if($memory<0){
        echo _('Invalid Memory Limit...');
        exit();
    }
    if(isset($_POST['score'])&&!empty($_POST['score'])){ 
        $score=intval($_POST['score']);
    }else{
        echo _('Please enter Case Score...');
        exit();
    }
    if($score<0){
        echo _('Invalid Case Score...');
        exit();
    }
    $compare_way=isset($_POST['compare']) ? CMP_TYPE($_POST['compare'], intval($_POST['precision'])) : 0;
    if(isset($_POST['title'])&&!empty($_POST['title'])){
        $title=mysqli_real_escape_string($con,$_POST['title']);
    }else{
        echo _('Please enter Title...');
        exit();
    }
    $des=isset($_POST['description']) ? mysqli_real_escape_string($con,$_POST['description']) : '';
    $input=isset($_POST['input']) ? mysqli_real_escape_string($con,$_POST['input']) : '';
    $output=isset($_POST['output']) ? mysqli_real_escape_string($con,$_POST['output']) : '';
    $samp_in=isset($_POST['sample_input']) ? mysqli_real_escape_string($con,$_POST['sample_input']) : '';
    $samp_out=isset($_POST['sample_output']) ? mysqli_real_escape_string($con,$_POST['sample_output']) : '';
    $hint=isset($_POST['hint']) ? mysqli_real_escape_string($con,$_POST['hint']) : '';
    $source=isset($_POST['source']) ? mysqli_real_escape_string($con,$_POST['source']) : '';

    require 'inc/problem_flags.php';
    $has_tex=0;
    if(isset($_POST['option_open_source'])){
        switch(intval($_POST['option_open_source'])){
            case 0:
                break;
            case 1:
                $has_tex|=PROB_SOLVED_OPENSOURCE;
                break;
            case 2:
                $has_tex|=PROB_DISABLE_OPENSOURCE;
                break;
        }
    }
    if(isset($_POST['option_level'])){
        $l=intval($_POST['option_level']);
        $level_max=(PROB_LEVEL_MASK>>PROB_LEVEL_SHIFT);
        if($l>=0 && $l<=$level_max){
            $has_tex|=($l<<PROB_LEVEL_SHIFT);
        }
    }
    if(isset($_POST['hide_prob'])){
        $has_tex|=PROB_IS_HIDE;
    }
    foreach ($_POST as $value) {
        if(strstr($value,'[tex]') || strstr($value,'[inline]')) {
            $has_tex|=PROB_HAS_TEX;
            //echo $value;
            break;
        }
    }

    if($_POST['op']=='edit'){
        if(!isset($_POST['problem_id'])){
            echo _('Invalid Argument...');
            exit();
        }
        $id=intval($_POST['problem_id']);
        $result=mysqli_query($con,"update problem set title='$title',case_time_limit=$time,memory_limit=$memory,case_score=$score,description='$des',input='$input',output='$output',sample_input='$samp_in',sample_output='$samp_out',hint='$hint',source='$source',has_tex=$has_tex,compare_way=$compare_way where problem_id=$id");
    }else if($_POST['op']=='add'){
        $id=1000;
        $result=mysqli_query($con,'select max(problem_id) from problem');
        if(($row=mysqli_fetch_row($result)) && intval($row[0]))
            $id=intval($row[0])+1;
        $result=mysqli_query($con,"insert into problem (problem_id,title,description,input,output,sample_input,sample_output,hint,source,in_date,memory_limit,case_time_limit,case_score,has_tex,compare_way) values ($id,'$title','$des','$input','$output','$samp_in','$samp_out','$hint','$source',NOW(),$memory,$time,$score,$has_tex,$compare_way)");

    }else{
        echo _('Invalid Argument...');
        exit();
    }

    if(!$result)
        echo _('Something went wrong...');
    else
        echo 'success';
}