<?php
if(!defined("PUBLIC_KEY"))
    require __DIR__.'/../conf/encsettings.php';
    
function my_rsa($value){
    $len=strlen($value);
    if($len>64)
        $value=substr($value,0,64);
    else
        $value=str_pad($value,64,"\x00");

    $crypted="";
    openssl_public_encrypt($value,$crypted,PUBLIC_KEY,OPENSSL_NO_PADDING);
    return "\x00".base64_encode($crypted);
}

function password_right($usr, $pwd_in){
    require __DIR__.'/../conf/database.php';
    $result=mysqli_query($con,"select password,user_id from users where user_id='$usr' or email='$usr' limit 1");
    if(!($row=mysqli_fetch_row($result)) || !$row[0])
        return false;
    $usr=$row[1];
    $pwd_enc=my_rsa($pwd_in);
    $pwd_real=$row[0];
    if(ord($pwd_real)!=0){ //password in database is not encrypted password
        $pwd_real=my_rsa($pwd_real);
        $pwd_escaped=mysqli_escape_string($con,$pwd_real);
        mysqli_query($con,"update users set password='$pwd_escaped' where user_id='$usr'");
    }
    if(strcmp($pwd_enc, $pwd_real)!=0)
        return false;
    else
        return true;
}