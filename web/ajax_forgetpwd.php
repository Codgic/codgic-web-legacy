<?php
//UNFINISHED!
header("Content-type:text/html;charset=utf-8");  
$to='';
$subject='CWOJ';
$content='Hello World.';
require('inc/database.php');
function postmail($to,$subject = '',$body = ''){
    error_reporting(E_STRICT);
    date_default_timezone_set('Asia/Shanghai');
    require_once('inc/class.phpmailer.php');
    include('inc/class.smtp.php');
    $mail             = new PHPMailer(); //new一个PHPMailer对象出来
    $body            = eregi_replace("[\]",'',$body); //对邮件内容进行必要的过滤
    $mail->CharSet ="GBK";
    $mail->IsSMTP();
    $mail->SMTPDebug  = 0;                     // 2 - DISABLE
    $mail->SMTPAuth   = true;               
    $mail->SMTPSecure = "ssl";                
    $mail->Host       = 'smtp.126.com';      
    $mail->Port       = 465;                   
    $mail->Username   = 'YOUREMAIL@CWOJ.TK';  
    $mail->Password   = 'EMAILPASSWORD';            
    $mail->SetFrom('YOUREMAIL@CWOJ.TK', 'CWOJ');
    //$mail->AddReplyTo('xxx@xxx.xxx','who');
    $mail->Subject    = $subject;
    $mail->AltBody    = 'To view the message, please use an HTML compatible email viewer!'; // optional, comment out and test
    $mail->MsgHTML($body);
    $address = $to;
    $mail->AddAddress($address, '');

    if(!$mail->Send()) {
        echo '发送邮件失败: ' . $mail->ErrorInfo;
    } else {
        echo "请查收您邮箱里的验证码以继续...";
    }
}
postmail($to,$subject,$content);
//session_start();
//require('inc/database.php');
//echo $emailpwd;
//$uid= $_POST['userid']; //Import user name
//$pwd=$_POST['pwd'];
//echo "fuck";
//mysql_query("update users set password='$pwd' where user_id='$uid'");
//if(1==mysql_affected_rows())
//	echo "成功";
//else
//	echo "失败";
?>