<?php
static $check_login=1;
if(!isset($_SESSION))
    session_start();
if(!isset($_SESSION['user'])){
    if(!function_exists('check_cookie')) 
        require __DIR__.'/cookie.php';
    if(!check_cookie()){
        if($require_auth){
            header("location: /login.php");
             exit();
        }
    }else{
        if(!function_exists('login')) 
            require __DIR__.'/userlogin.php';
        if(TRUE===login($_SESSION['user'], TRUE))
            write_cookie(1);
    }
}