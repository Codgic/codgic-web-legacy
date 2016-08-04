<?php
function update_cont_rank($cont_id){
    require 'inc/database.php';
    $row=mysqli_fetch_row(mysqli_query($con,"select problems,num,start_time,end_time,judge_way from contest where contest_id=$cont_id"));
    $prob_arr=unserialize($row[0]);
    $cont_num=$row[1];
    $cont_start=$row[2];
    $cont_end=$row[3];
    $cont_judgeway=$row[4];
    $q=mysqli_query($con,"select user_id from contest_status where contest_id=$cont_id");
    while($row=mysqli_fetch_row($q)){
        $user_id=$row[0];
        for($i=0;$i<$cont_num;$i++){
            $s_row=mysqli_fetch_row(mysqli_query($con,"select max(score),count(score),min(result),max(in_date) from solution where user_id='$user_id' and in_date>'$cont_start' and in_date<'$cont_end' and problem_id=".$prob_arr[$i]));
            if(!isset($s_row[0])) $s_row[0]=0;
            if(!isset($s_row[2])) $s_row[2]=NULL;
            if(!isset($s_row[3])) $s_row[3]=$cont_end;
            $score_arr["$prob_arr[$i]"]=$s_row[0];
            $res_arr["$prob_arr[$i]"]=$s_row[2];
            $time_arr["$prob_arr[$i]"]=strtotime($s_row[3])-strtotime($cont_start);
            if($cont_judgeway==1) $time_arr["$prob_arr[$i]"]+=1200*($s_row[1]-1);
        }
        $tot_scores=array_sum($score_arr);
        $tot_times=array_sum($time_arr);
        $scores=serialize($score_arr);
        $results=serialize($res_arr);
        $times=serialize($time_arr);
        unset($score_arr);
        unset($res_arr);
        mysqli_query($con,"update contest_status set scores='$scores', results='$results', times='$times', tot_scores=$tot_scores, tot_times=$tot_times where contest_id=$cont_id and user_id='$user_id'");
    }
    $q=mysqli_query($con,"select user_id,tot_scores,tot_times from contest_status order by tot_scores desc,tot_times");
    $pre_score=-1;
    $pre_time=-1;
    $cnt=0;
    while($row=mysqli_fetch_row($q)){
        $user_id=$row[0];
        if($pre_score!=$row[1] || $pre_time!=$row[2]) $cnt++;
        $pre_score=$row[1];
        $pre_time=$row[2];
        mysqli_query($con, "update contest_status set rank=$cnt where user_id='$user_id'");
    }
    for($i=0;$i<$cont_num;$i++)
      mysqli_query($con, "update problem set rejudged='N' where problem_id=".$prob_arr[$i]);
    mysqli_query($con, "update contest set ranked='Y' where contest_id=$cont_id");
}

function get_judgeway_destext($judge_way){
    if($judge_way==0) return '总分即时间内每题最高分之和，总时即时间内每题最后提交距比赛开始时间之和。<br><code>final_score = max_score;</code><br><code>final_time = end_time - last_submit_time; </code><br><code>(last_submit_time >= start_time)</code>';
    else if($judge_way==1) return '总分即时间内每题最高分之和，总时即时间内每题最后提交距比赛开始时间之和加非第一次AC提交次数*1200s。<br><code>final_score = max_score;</code><br><code>final_time = end_time - last_submit_time + 1200 * ( submit_times - 1 ); </code><br><code>(last_submit_time >= start_time)</code>';
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

function get_ip(){
   if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      return $_SERVER['HTTP_CLIENT_IP'];
    }
	else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
  else {
      return $_SERVER['REMOTE_ADDR'];
	}
}

function get_gravatar($email, $s=80, $d='mm'){
    $email = md5($email); 
    //$avatar = "https://secure.gravatar.com/avatar/$email?s=$s&d=$d&r=g"; 
    $avatar = "//sdn.geekzu.org/avatar/$email?s=$s&d=$d&r=g";
    return $avatar; 
}

function get_ipgeo($ip = ''){
	if(preg_match("/^((192\.168|172\.([1][6-9]|[2]\d|3[01]))(\.([2][0-4]\d|[2][5][0-5]|[01]?\d?\d)){2}|10(\.([2][0-4]\d|[2][5][0-5]|[01]?\d?\d)){3})$/",$ip)) return '局域网';
    $res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);
    if(empty($res)){ return '未知'; }
    $jsonMatches = array();
    preg_match('#\{.+?\}#', $res, $jsonMatches);
    if(!isset($jsonMatches[0])){ return false; }
    $json = json_decode($jsonMatches[0], true);
    if(isset($json['ret']) && $json['ret'] == 1){
        $json['ip'] = $ip;
        unset($json['ret']);
    }else{
        return '未知';
    }
	return $json["city"];
}

function resetpwd_mail(){
    require 'inc/ojsettings.php';
    if(!isset($_SESSION['resetpwd_user']) || !isset($_SESSION['resetpwd_email']) || !isset($_SESSION['resetpwd_code'])) return 'timeout';
    $user = $_SESSION['resetpwd_user'];
    $email = $_SESSION['resetpwd_email'];
    $code = $_SESSION['resetpwd_code'];
    $subject = "$oj_name 密码重置验证";
    $ip = get_ip();
    $nowtime = date("Y/m/d H:i:s");
    $content = "<div>亲爱的 $user ,<br><p>我们收到了您在{$oj_name}重置密码的请求并发送了验证码来确认您的身份。</p><b><p>请求时间: $nowtime (UTC+08:00)</p><p>IP地址: $ip</p><p>验证码: $code</p></b><p>如果您没有在{$oj_name}有过重置密码的请求，您只需忽略这封邮件并不要把验证码告诉任何人。<br>如有任何问题，请回复该邮件来与管理员取得联系。</p><br>谢谢！<p>$oj_copy</p></div>";
    return postmail($email,$subject,$content);
}

function postmail($to,$subject = '',$body = ''){
    //error_reporting(E_STRICT);
    date_default_timezone_set('Asia/Shanghai');
	if(!class_exists("phpmailer")) require 'inc/class.phpmailer.php';
	if(!class_exists("SMTP")) include 'inc/class.smtp.php';
    $mail = new PHPMailer(); 
    $mail->CharSet ="UTF-8";
    $mail->Encoding ="base64";
    $mail->IsSMTP();
    //$mail->SMTPDebug  = 0;                     // 2 - DISABLE
    $mail->SMTPAuth   = true;               
    $mail->SMTPSecure = "ssl";         
    $mail->Host       = 'YOURSMTPSERVER';      //SMTP Server Address
    $mail->Port       = 999;     //SMTP Service Port             
    $mail->Username   = 'YOUREMAIL';  //Input your admin email
    $mail->Password   = 'YOURPASSWORD';  //Input your email password.     
    $mail->SetFrom('YOUREMAIL', 'CWOJ');
    $mail->AddReplyTo('YOUREMAIL','CWOJ');
    $mail->Subject    = $subject;
	$mail->WordWrap = 60;
    //$mail->AltBody    = 'To view the message, please use an HTML compatible email viewer!'; // optional, comment out and test
    $mail->MsgHTML($body);http://www.jb51.net/article/37929.htm
    $address = $to;
    $mail->AddAddress($address, '');
    $mail->IsHTML(true); 
    if(!$mail->Send()) {
        return '发送邮件失败: ' . $mail->ErrorInfo;
    } else {
        return 'success';
    }
}
