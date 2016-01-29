<?php
$contact_email = 'info@cwoj.tk';
function postmail($to,$subject = '',$body = ''){
    //error_reporting(E_STRICT);
    date_default_timezone_set('Asia/Shanghai');
    require('inc/class.phpmailer.php');
    include('inc/class.smtp.php');
    $mail = new PHPMailer(); 
    $mail->CharSet ="UTF-8";
    $mail->Encoding ="base64";
    $mail->IsSMTP();
    //$mail->SMTPDebug  = 0;                     // 2 - DISABLE
    $mail->SMTPAuth   = true;               
    $mail->SMTPSecure = "ssl";         
    $mail->Host       = 'smpt.xx.xx';      //SMTP Server Address
    $mail->Port       = 000;     //SMTP Service Port             
    $mail->Username   = 'info@cwoj.tk';  //Input your admin email
    $mail->Password   = 'yourpassword';  //Input your email password.     
    $mail->SetFrom('info@cwoj.tk', 'CWOJ');
    $mail->AddReplyTo('info@cwoj.tk','CWOJ');
    $mail->Subject    = $subject;
	$mail->WordWrap = 60;
    //$mail->AltBody    = 'To view the message, please use an HTML compatible email viewer!'; // optional, comment out and test
    $mail->MsgHTML($body);
    $address = $to;
    $mail->AddAddress($address, '');
    $mail->IsHTML(true); 
    if(!$mail->Send()) {
        echo '发送邮件失败: ' . $mail->ErrorInfo;
    } else {
        echo "success";
    }
}
