<?php
require_once 'inc/ojsettings.php';
if(!defined('DISALLOW_GOOGLEBOT'))
define('DISALLOW_GOOGLEBOT',0);

if(!isset($_SESSION)) 
{ 
session_start(); 
}  

if(!isset($_SESSION['user']) && (DISALLOW_GOOGLEBOT || !isset($_SERVER['HTTP_USER_AGENT']) || FALSE===strstr($_SERVER['HTTP_USER_AGENT'],'Googlebot'))){
	require 'inc/cookie.php';
	if(!check_cookie()) {
		if($require_auth) {
		  header("location: /auth.php");
		  exit;
		}
	}else{
		require_once 'inc/database.php';
		require_once 'inc/userlogin.php';
		if(TRUE===login($_SESSION['user'], TRUE))
			write_cookie();
		mysqli_close($con);
	}
}
