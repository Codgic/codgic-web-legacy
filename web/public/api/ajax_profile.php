<?php
require __DIR__.'/../inc/init.php';

if(!isset($_POST['type'],$_POST['nick'],$_POST['email'],$_POST['school'])){
    echo _('Invalid Argument...');
    exit();
}
if(strlen($_POST['nick'])>40){
    echo _('Nickname too long...');
    exit();
}
if(strlen($_POST['school'])>60){
    echo _('School name too long...');
    exit();
}
if(strlen($_POST['email'])>60){
    echo _('Email too long...');
    exit();
}
if(!preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i",$_POST['email'])){
    echo _('Invalid Email...');
    exit();
}

if($_POST['type']=='profile'){
    if(!isset($_SESSION['user'])){
        echo _('Please login first...');
    }else
        $user=$_SESSION['user'];
    if(!isset($_POST['oldpwd'])){
        echo _('Invalid Argument...');
        exit();
    }
    
    require __DIR__.'/../conf/database.php';

    if(!function_exists('my_rsa'))
        require __DIR__.'/../conf/ojsettings.php';
    require __DIR__.'/../func/checkpwd.php';
    
    if(!password_right($user, $_POST['oldpwd'])){
        echo _('Wrong Password...');
        exit();
    }
    if(strlen($_POST['motto'])>200){
        echo _('Motto too long...');
        exit();
    }
    $query='update users set email=\''.mysqli_real_escape_string($con,$_POST['email']).'\',school=\''.mysqli_real_escape_string($con,$_POST['school']).'\',nick=\''.mysqli_real_escape_string($con,$_POST['nick']).'\',motto=\''.mysqli_real_escape_string($con,$_POST['motto']).'\'';
    if(isset($_POST['newpwd']) && $_POST['newpwd']!=''){
        $len=strlen($_POST['newpwd']);
        if($len<6||$len>50){
            echo _('Password too long or too short (6~50)...');
            exit();
        }
        $query.=',password=\''.mysqli_real_escape_string($con,my_rsa($_POST['newpwd'])).'\'';
    }
    $query.=" where user_id='$user'";
    mysqli_query($con,$query);
    $_SESSION['email']=mysqli_real_escape_string($con,$_POST['email']);
    echo 'success';
}

else if($_POST['type']=='reg'){
    if(!isset($_POST['userid'],$_POST['newpwd'])){
        echo _('Invalid Argument...');
        exit();
    }
    if(!isset($_POST['lic'])){
        echo _('Please agree the license agreement first...');
        exit();
    }
    
    require __DIR__.'/../conf/database.php';

    $user=mysqli_real_escape_string($con,trim($_POST['userid']));
    $len=strlen($user);
    if($len==0){
        echo _('Username can\'t be empty...');
        exit();
    }
    if($len>20){
        echo _('Username too long...');
        exit();
    }
    if(preg_match('/\W/',$user)){
        echo _('Username can only contain alphabets, digits or "_"');
        exit();
    }
    $len=strlen($_POST['newpwd']);
    if($len<6||$len>50){
        echo _('Password too long or too short (6~50)...');
        exit();
    }
    $pwd=mysqli_real_escape_string($con,$_POST['newpwd']);
    
    //If new regs need to be reviewed by administrators.
    if($require_confirm) 
        $priv=0;
    else
        $priv=1;
        
    mysqli_query($con,"insert into users (user_id,email,password,reg_time,nick,school,motto,privilege) values ('$user','".mysqli_real_escape_string($con,$_POST['email'])."','$pwd',NOW(),'".mysqli_real_escape_string($con,$_POST['nick'])."','".mysqli_real_escape_string($con,$_POST['school'])."','',$priv)");
    $code=mysqli_errno($con);
    if($code==0)
        echo 'success';
    else if($code==1062)
        echo _('Username/Email already exists...');
    else 
        echo _('Something went wrong...');
}else
    echo _('Invalid Argument...');