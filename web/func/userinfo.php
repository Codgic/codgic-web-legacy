<?php
require_once __DIR__.'/../conf/ojsettings.php';

//Obtain user's real IP Address.
function get_ip(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        return $_SERVER['HTTP_CLIENT_IP'];
    }else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        return $_SERVER['REMOTE_ADDR'];
    }
}

//Obtain user's Gravatar.
function get_gravatar($email, $s=80, $d='mm'){
    $email = md5($email); 
    $avatar = constant('GRAVATAR_CDN')."/$email?s=$s&d=$d&r=g"; 
    return $avatar; 
}

//Obtain the geo location of user's IP Address.
function get_ipgeo($ip = ''){
    if(preg_match("/^((192\.168|172\.([1][6-9]|[2]\d|3[01]))(\.([2][0-4]\d|[2][5][0-5]|[01]?\d?\d)){2}|10(\.([2][0-4]\d|[2][5][0-5]|[01]?\d?\d)){3})$/",$ip))
        return _('LAN');
    $res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);
    if(empty($res)){ return _('Unknown'); }
        $jsonMatches = array();
    preg_match('#\{.+?\}#', $res, $jsonMatches);
    if(!isset($jsonMatches[0]))
        return false;
        $json = json_decode($jsonMatches[0], true);
    if(isset($json['ret']) && $json['ret'] == 1){
        $json['ip'] = $ip;
        unset($json['ret']);
    }else
        return _('Unknown');
    return $json["city"];
}
