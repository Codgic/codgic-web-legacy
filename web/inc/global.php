<?php
session_start();
if(!function_exists('check_i18n_cookie')) 
    require 'inc/cookie.php';
//Preference is just a preference.
//Actual display language is determined by cookie.
if(!class_exists('preferences')) 
    require 'inc/preferences.php';
if(isset($_SESSION['pref'])){
    //If preferences initialized already.
    $pref=unserialize($_SESSION['pref']);
    //If no session, create it.
    if(!isset($_SESSION['i18n']))
        $_SESSION['i18n']=$pref->i18n;
}else{
    //Initialize preferences.
    $pref=new preferences();
    //Override default i18n with cookie.
    if($r=check_i18n_cookie())
        $pref->i18n=$r;
    if(!isset($_SESSION['i18n']))
        $_SESSION['i18n']=$pref->i18n;
    $_SESSION['pref']=serialize($pref);
}

if($_SESSION['i18n']!='auto'){
    $language = $_SESSION['i18n'].'.UTF-8';
    //If no cookie, create it.
    if(!check_i18n_cookie()){
        write_i18n_cookie($pref->i18n);
    }
}else{
    //Auto determine language.
    preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
    $lang = strtolower($matches[1]);
    switch($lang){
        case 'zh-hans-cn':
            $language = 'zh_CN';
            break;
        case 'zh-cn':
            $language = 'zh_CN';
            break;
        default:
            $language = 'en_US';
            break;
    }
    $_SESSION['i18n']=$language;
    setcookie('i18n','',time()-3600);
    write_i18n_cookie($language);
    $language.='.UTF-8';
}

putenv("LANGUAGE=" . $language); 
setlocale(LC_ALL, $language);
bindtextdomain("main", "locale"); 
bind_textdomain_codeset("main", 'UTF-8');
textdomain("main");