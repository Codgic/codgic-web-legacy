<?php
require_once __DIR__.'/../../config/config.php';
if(!defined('BIND_DOMAIN'))
    define('BIND_DOMAIN', $_SERVER['HTTP_HOST']);

//Check whether user's login cookie is valid.
function check_cookie(){
    if(!isset($_COOKIE['SID']))
        return false;
    $cookie = decrypt(COOKIE_KEY, $_COOKIE['SID']);
    if($cookie === false)
        return false;
    $arr = unserialize($cookie);

    if(false===$arr || !isset($arr['magic']) || $arr['magic']!="codgic")
        return false;
    $user = $arr['user'];
    if(preg_match('/\W/',$user) || strlen($user)==0)
        return false;

    $_SESSION['user'] = $user;
    return true;
}

//Write login cookie.
function write_cookie($remember){
    $arr = array('magic'=>'codgic');
    $arr['user']=$_SESSION['user'];

    $data = encrypt(COOKIE_KEY, serialize($arr));
    if($remember==1)
        setcookie('SID', $data, time()+COOKIE_EXPIRE, '/', BIND_DOMAIN);
    else 
        setcookie('SID', $data, 0, '/', BIND_DOMAIN);
}

//Clear certain cookie.
function clear_cookie($name){
    setcookie("$name",'',time()-3600, '/', BIND_DOMAIN);
}

//Encrypt cookie.
function encrypt($key, $plain_text){
    $iv='7284565820000000';
    $key=hash('sha256',$key,true);
    return openssl_encrypt($plain_text,'aes-256-cbc',$key,false,$iv);
}

//Decrypt cookie.
function decrypt($key, $c_t){ 
    $iv='7284565820000000';
    $key=hash('sha256',$key,true);
    return openssl_decrypt($c_t,'aes-256-cbc',$key,false,$iv);
}
