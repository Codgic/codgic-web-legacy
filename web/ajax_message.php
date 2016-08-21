<?php
require 'inc/global.php';
require 'inc/mutex.php';
require 'inc/ojsettings.php';
require 'inc/database.php';
function getNextMsgID(){
	require 'inc/database.php';
	$ID=1000;
	$res=mysqli_query($con,"select max(message_id) from message");
	if($res && ($r=mysqli_fetch_row($res))){
		if($r[0])
			$ID=$r[0]+1;
	}
	return $ID;
}
if($require_auth&&!isset($_SESSION['user'])){
    echo _('Please login first...');
    exit();
}
if(!isset($_POST['op'])){
    echo _('Invalid Argument...');
    exit();
}else
    $op=$_POST['op'];
    
if($op=='get_message'){
    if(!isset($_POST['message_id'])){
        echo _('Invalid Argument...');
        exit();
    }
    $msg=intval($_POST['message_id']);
    if(!$msg){
        echo _('Invalid Argument...');
        exit();
    }
    
    $res=mysqli_query($con,'select content from message where message_id='.$msg);
    if($res){
        $row=mysqli_fetch_row($res);
        if($row)
            echo htmlspecialchars($row[0]);
        else 
            echo _('Something went wrong...');
    }
}

else if($op=='msg_create'){
    if(!isset($_POST['message']) || !isset($_POST['detail'])){
        echo _('Invalid Argument...');
        exit();
    }
    $user_id=$_SESSION['user'];
    $prob_id=0;
    $msg_id=0;
    $order_num=0;
    $depth=0;
    $title=mysqli_real_escape_string($con,trim($_POST['message']));
    $title_len=strlen($title);
    if($title_len==0){
        echo _('Post can\'t be empty...');
        exit();
    }
    if($title_len>500){
        echo _('Post too long...');
        exit();
    }
    $content=mysqli_real_escape_string($con,$_POST['detail']);
    $mutex=new php_mutex("$temp_dir");
    $new_msg_id=getNextMsgID();
    if(isset($_POST['message_id']) 
    && ($tmp=intval($_POST['message_id']))
	&& ($row=mysqli_fetch_row(mysqli_query($con,'select orderNum,depth,thread_id,problem_id from message where message_id='.$tmp)))){ //Reply message
        $msg_id=$tmp;
        $order_num=$row[0];
        $depth=$row[1];
        $thread_id=$row[2];
        $prob_id=$row[3];
        $res=mysqli_query($con,"select depth,orderNum from message where thread_id=$thread_id and orderNum>$order_num order by orderNum");
        while($row=mysqli_fetch_row($res)){
            if($row[0]<=$depth)
                break;
            $order_num=$row[1];
        }
        mysqli_query($con,"update message set orderNum=orderNum+1 where thread_id=$thread_id and orderNum>$order_num");
        mysqli_query($con,"update message set thread_id=$new_msg_id where thread_id=$thread_id");
        $depth++;
        $order_num++;
    }else{  //New message, check problem_id
        if(isset($_POST['problem_id'])){
            $tmp=intval($_POST['problem_id']);
            if(mysqli_num_rows(mysqli_query($con,'select problem_id from problem where problem_id='.$tmp)))
                $prob_id=$tmp;
        }
    }
    if(mysqli_query($con,"insert into message (thread_id,message_id,parent_id,orderNum,problem_id,depth,user_id,title,content,in_date) values($new_msg_id,$new_msg_id,$msg_id,$order_num,$prob_id,$depth,'$user_id','$title','$content',NOW())"))
        echo 'success';
    else 
        echo _('Something went wrong...');
    $mutex->release_mutex();
}

else if($op=='msg_edit'){
    $prob_id=0;
    if(!isset($_POST['message']) || !isset($_POST['detail'])||!isset($_POST['message_id'])){
        echo _('Invalid Argument...');
        exit();
    }
    $title=mysqli_real_escape_string($con,trim($_POST['message']));
    $title_len=strlen($title);
    $message_id=intval($_POST['message_id']);
    $row=mysqli_fetch_row(mysqli_query($con, "select user_id from message where message_id=$message_id"));
    if($title_len==0){
        echo _('Post can\'t be empty...');
        exit();
    }
    if($title_len>500){
        echo _('Post too long...');
        exit();
    }
    $content=mysqli_real_escape_string($con,$_POST['detail']);
    if($row[0]==$_SESSION['user']) 
        if(mysqli_query($con, "update message set title='$title',content='$content' where message_id=$message_id"))
            echo 'success';
        else 
            echo _('Something went wrong...');
}

else
    echo _('Invalid Argument...');