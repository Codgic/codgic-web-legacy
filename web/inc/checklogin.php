<?php
if(!isset($_SESSION)) session_start();
if(!isset($_SESSION['user'])){
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
