<?php
require __DIR__.'/../conf/ojsettings.php';
require __DIR__.'/../inc/init.php';
require __DIR__.'/../conf/mailsettings.php';
require __DIR__.'/../conf/database.php';

if(!isset($_POST['type'])){
    echo _('Invalid Argument...');
    exit();
}

if($_POST['type'] == 'verify'){
	if(!isset($_POST['email'])){
		echo _('Invalid Argument...');
        exit();
    }
	$email = $_POST['email'];
    if(!preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i",$_POST['email'])){
        echo _('Invalid Email...');
        exit();
    }
	$result=mysqli_query($con,'select user_id from users where email="'.mysqli_real_escape_string($con,$_POST['email']).'" limit 1');
	if(!($row=mysqli_fetch_row($result)) || !$row[0]){
        echo _('Invalid Email...');
        exit();
    }
	if(!isset($_SESSION['resetpwd_code'])||empty($_SESSION['resetpwd_code']))
		die('timeout');
    $code = $_SESSION['resetpwd_code'];
    $_SESSION['resetpwd_user'] = $row[0];
    $_SESSION['resetpwd_email'] = $email;
    echo resetpwd_mail();
}

else if($_POST['type'] == 'resend'){
	echo resetpwd_mail();
}

else if($_POST['type'] == 'match'){
	if(!isset($_POST['usercode'])){
	    echo _('Invalid Argument...');
        exit();
    }
	if(!isset($_SESSION['resetpwd_code'])||empty($_SESSION['resetpwd_code']))
        die('timeout');
	if($_POST['usercode']==$_SESSION['resetpwd_code']){
        $_SESSION['resetpwd_flag']=1;
        echo 'success';
    }else{
        $_SESSION['resetpwd_wrongnum']++;
        if($_SESSION['resetpwd_wrongnum'] >= 3) 
            echo 'fuckyou';
		else
            echo 'fail';
    }
}

else if($_POST['type'] == 'update'){
	if(!isset($_POST['newpwd'])){
	    echo _('Invalid Argument...');
        exit();
    }
	if(!isset($_SESSION['resetpwd_user']) || empty($_SESSION['resetpwd_user']) || !isset($_SESSION['resetpwd_flag']) || $_SESSION['resetpwd_flag']!=1)
		die('timeout');
	if(!function_exists('my_rsa'))
		require __DIR__.'/../func/checkpwd.php';

	$user = $_SESSION['resetpwd_user'];
	$len=strlen($_POST['newpwd']);
	if($len<6||$len>50){
		echo _('Password too long or too short (6~50)...');
        exit();
    }
	$query='update users set password=\''.mysqli_real_escape_string($con,my_rsa($_POST['newpwd'])).'\'';
	$query.=" where user_id='$user'";
    //Cleaning up
	unset($_SESSION['resetpwd_code']);
	unset($_SESSION['resetpwd_user']);
	unset($_SESSION['resetpwd_email']);
	unset($_SESSION['resetpwd_wrongnum']);
	unset($_SESSION['resetpwd_flag']);
	session_destroy();
    
	if(mysqli_query($con,$query))
		echo 'success';
	else
		echo _('Something went wrong...');
}

else
    echo _('Invalid Argument...');