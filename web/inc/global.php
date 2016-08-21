<?php
session_start();
//Initialize Prefernces.
if(!class_exists('preferences')) 
    require 'inc/preferences.php';
if(isset($_SESSION['pref'])){
    //If preferences initialized already.
    $pref=unserialize($_SESSION['pref']);
    //If no cookie, create it.
    if(!isset($_COOKIE['i18n']))
        setcookie('i18n',$pref->i18n,time()+31536000);
}else{
    //Initialize preferences.
    $pref=new preferences();
    //Override default i18n with cookie.
    if(isset($_COOKIE['i18n']))
        $pref->i18n=$_COOKIE['i18n'];
    $_SESSION['pref']=serialize($pref);
}
if(isset($pref->i18n)&&$pref->i18n!='auto'){
    $language = $pref->i18n.'.UTF-8';
}else{
    //Auto determine language.
    preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
    $lang = strtolower($matches[1]);
    switch($lang){
        case 'zh-hans-cn':
            $language = 'zh_CN.UTF-8';
            break;
        case 'zh-cn':
            $language = 'zh_CN.UTF-8';
            break;
        default:
            $language = 'en_US.UTF-8';
            break;
    }
}
putenv("LANGUAGE=" . $language); 
setlocale(LC_ALL, $language);
bindtextdomain("main", "locale"); 
bind_textdomain_codeset("main", 'UTF-8');
textdomain("main");