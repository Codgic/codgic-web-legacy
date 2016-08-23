<?php
session_start();
if(!function_exists('check_i18n_cookie')) 
    require 'inc/cookie.php';
//Check & Initialize preference if uninitialized.
if(!class_exists('preferences')) 
    require 'inc/preferences.php';
if(isset($_SESSION['pref']))
    $pref=unserialize($_SESSION['pref']);
else
    $pref=new preferences();

//Check & initialize session if uninitialized.
if(!isset($_SESSION['i18n'])){
    if(!$r=check_i18n_cookie()){
        //If no cookie.
        if($pref->i18n=='auto'){
            preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
            $lang = strtolower($matches[1]);
            switch($lang){
                case 'zh-hans-cn':
                    $_SESSION['i18n'] = 'zh_CN';
                    break;
                case 'zh-cn':
                    $_SESSION['i18n'] = 'zh_CN';
                    break;
                default:
                    $_SESSION['i18n'] = 'en_US';
                    break;
            }
            write_i18n_cookie($_SESSION['i18n']);
        }else{
            $_SESSION['i18n']=$pref->i18n;
            write_i18n_cookie($_SESSION['i18n']);
        }
    }else{
        $_SESSION['i18n']=$r;
        //Override with user settings.
        if($pref->i18n!='auto' && $_SESSION['i18n']!=$pref->i18n){
            $_SESSION['i18n']=$pref->i18n;
            write_i18n_cookie($_SESSION['i18n']);
        }
    }
}

putenv("LANGUAGE=" . $_SESSION['i18n'].'.UTF-8'); 
setlocale(LC_ALL, $_SESSION['i18n'].'.UTF-8');
bindtextdomain("main", "locale"); 
bind_textdomain_codeset("main", 'UTF-8');
textdomain("main");