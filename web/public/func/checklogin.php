<?php
static $check_login=1;
if(!isset($_SESSION))
    session_start();
if(!isset($_SESSION['user'])){
	require_once __DIR__.'/cookie.php';
    if(!check_cookie()){
        if($require_auth){
            header("location: /login.php");
             exit();
        }
    }else{
		require_once __DIR__.'/userlogin.php';
        if(TRUE===login($_SESSION['user'], TRUE))
            write_cookie(1);
    }
}
