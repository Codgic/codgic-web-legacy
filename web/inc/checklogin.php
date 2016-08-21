<?php
$check_login=1;
if(!isset($_SESSION)) 
    session_start();
if(!isset($_SESSION['user'])){
	if(!function_exists('check_cookie')) 
		require 'inc/cookie.php';
	if(!check_cookie()) {
		if($require_auth) {
		  header("location: /login.php");
		  exit;
		}
	}else{
		if(!isset($con)) 
			require 'inc/database.php';
		if(!function_exists('login')) 
			require 'inc/userlogin.php';
		if(TRUE===login($_SESSION['user'], TRUE))
			write_cookie(1);
		mysqli_close($con);
	}
}
